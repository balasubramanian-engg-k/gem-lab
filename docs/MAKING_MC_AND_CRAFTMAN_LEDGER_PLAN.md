# Making MC Section & Craftman Ledger Report – Plan

## 1. Overview

Add a new section **Making MC** (similar to Invoices) that:

- Shows a **list of Labour Receipts** (like the invoice list).
- Provides **Create Labour receipt** to create a new **MC Id** (e.g. `ADCR001`) and enter receipt details.
- Allows a **Craftman Ledger** report (under Report section) filtered by craftman, viewable as **PDF or mobile-friendly** with the specified columns and totals.

No code implementation in this doc—only the plan.

---

## 2. Prerequisites / Master Data

### 2.1 Craftman master

- **Need:** A list of **registered craftmen** so the Labour Receipt form can show “Craftman Name” as a **dropdown** (only registered names).
- **Option A:** New table `craftmen` (e.g. `id`, `name`, optional `phone`, `created_at`, `updated_at`) with a `Craftman` model.
- **Option B:** If craftmen are already stored elsewhere (e.g. users or another table), reuse that and expose a list for the dropdown.
- **Deliverable:** CRUD or at least a way to maintain craftman names; Labour Receipt form uses this list for “Craftman Name”.

### 2.2 Product list for “Product name”

- **Need:** “Product name” on the Labour Receipt should be selected from a list (as per “fetch from product list”).
- **Option A:** Reuse existing **Product Type** (`product_types` table) as the product list for MC.
- **Option B:** Introduce a separate **Product** master (e.g. `products`: `id`, `name`, …) if product and product-type are different in your business.
- **Deliverable:** A single source of product names for the Labour Receipt dropdown (ProductType or Product model).

---

## 3. Data Model – Labour Receipt (MC)

### 3.1 Main table (e.g. `labour_receipts` or `making_mcs`)

| Column               | Type / Notes                                                                 |
|----------------------|-------------------------------------------------------------------------------|
| `id`                 | Primary key (auto-increment).                                                |
| `receipt_number`     | Unique display id for “MC Id” (e.g. `ADCR001`). Can be generated from `id`.   |
| `craftman_id`        | FK to craftman (or craftman name if no master table).                        |
| `silver_gross_weight`| Decimal (e.g. grams).                                                        |
| `product_id`         | FK to product type or product table (for “Product name”).                    |
| `payment`            | Decimal (or integer) – payment amount.                                       |
| `total_count`        | Integer – total count of pieces.                                             |
| `status`             | Enum or string: **Issued** / **Received** (as in the form radio).            |
| `count_issued`       | Optional: store issued count if different from total when status = Issued.   |
| `count_received`     | Optional: store received count when status = Received.                        |
| `remarks`            | Optional text.                                                               |
| `created_at`, `updated_at` | Timestamps.                                                            |

- **MC Id format:** e.g. prefix `ADCR` + zero-padded `id` → `ADCR001`, `ADCR002`, … (same idea as invoice `AD` + id).
- **Receipt number:** Either stored in `receipt_number` or computed in app (e.g. `'ADCR' . str_pad($id, 6, '0')`).

### 3.2 Relationships

- Labour Receipt **belongsTo** Craftman (or store `craftman_name` if no master).
- Labour Receipt **belongsTo** ProductType (or Product) for product name.

### 3.3 Counts and weights for report

- For **Craftman Ledger** you need “Count Issued”, “Count Received”, “Gross weight – Issued”, “Gross weight – Received” per row.
- **Option A:** One row per receipt; store both issued and received counts/weights on the same record (e.g. when status = Issued, fill `count_issued` and `gross_weight_issued`; when Received, fill `count_received` and `gross_weight_received`). Some receipts might be “Issued” only (received = 0) or “Received” only.
- **Option B:** Two record types or two tables (issued vs received); then aggregate by receipt/craftman for the report. More complex.
- **Recommendation:** Start with one table and two sets of fields: issued count/weight and received count/weight; form fills them depending on status (e.g. “Issued” sets issued fields, “Received” sets received fields), so the ledger can sum them per craftman.

---

## 3A. Workflow: Order to Craftsman with Partial Returns (recommended)

This section describes a **workflow** where each **order** (items given to the craftsman) is one record, and **count_received** is tracked against that order—either in one shot or in **multiple partial returns** until the order is fully received.

### 3A.1 Business flow

1. **Issue (order to craftsman)**  
   You create one **order**: e.g. 30 rings, X grams silver, amount, product, craftman. This is a single record (e.g. MC Id `ADCR001`).

2. **Receive (return from craftsman)**  
   The craftsman returns the work in **one or multiple shots**:
   - One shot: e.g. 30 rings returned at once → order complete.
   - Multiple shots: e.g. 10 + 12 + 8 = 30 → each return is recorded separately against the same order; **count_received** (and weight received) is the **sum** of all return entries for that order.

3. **One screen**  
   All "items given to craftman" (orders) are listed on **one screen**. Each row shows:
   - Order (MC Id, craftman, product, count issued, silver, amount, date).
   - **Total count received so far** (and total weight received) against that order.
   - **Workflow status** (see below).

### 3A.2 Workflow status (per order)

| Status                | Meaning                                                                 |
|-----------------------|-------------------------------------------------------------------------|
| **ISSUED**            | Order created; nothing received yet (count_received = 0).               |
| **PARTIALLY_RECEIVED**| Some items returned; total received < issued (e.g. 18/30 received).     |
| **FULLY_RECEIVED**   | All items returned; total received = count issued (e.g. 30/30).         |

Status is **derived** (or updated) from: `total_count_received` vs `count_issued` (and optionally weight received vs weight issued).

### 3A.3 Data model for workflow

**Table 1: Orders (what we give to craftsman)**  
e.g. `labour_receipts` or `craftman_orders`

| Column                  | Type / Notes                                                                 |
|-------------------------|-------------------------------------------------------------------------------|
| `id`                    | Primary key.                                                                 |
| `receipt_number`        | MC Id (e.g. ADCR001).                                                         |
| `craftman_id`           | FK to craftman.                                                               |
| `product_id`            | FK to product type / product.                                                 |
| `count_issued`          | Total pieces given (e.g. 30 rings). **Fixed** at issue.                       |
| `silver_gross_weight`   | Silver (grams) given. **Fixed** at issue.                                     |
| `amount` / `payment`   | Amount for the order.                                                        |
| `workflow_status`       | **ISSUED** \| **PARTIALLY_RECEIVED** \| **FULLY_RECEIVED** (see above).       |
| `total_count_received`  | **Running total** of pieces received (sum of all return entries).            |
| `total_weight_received` | **Running total** of silver/weight received (sum of all return entries).      |
| `issued_at`             | Date/time order was issued (optional; can use `created_at`).                  |
| `remarks`               | Optional.                                                                    |
| `created_at`, `updated_at` | Timestamps.                                                              |

**Table 2: Returns (each receipt from craftsman)**  
e.g. `labour_receipt_returns` or `craftman_order_returns`

| Column                  | Type / Notes                                          |
|-------------------------|--------------------------------------------------------|
| `id`                    | Primary key.                                          |
| `labour_receipt_id` / `order_id` | FK to the order.                             |
| `count_received`        | Pieces received in **this** return (e.g. 10).         |
| `weight_received`       | Silver/weight received in **this** return (optional).|
| `received_at`          | Date (and time if needed) of this return.              |
| `remarks`               | Optional (e.g. "First batch", "Balance").              |
| `created_at`            | When the return was recorded.                         |

- When a new return row is saved: update the parent order's `total_count_received` (+= `count_received`) and `total_weight_received` (+= `weight_received`), then set `workflow_status` to **PARTIALLY_RECEIVED** or **FULLY_RECEIVED** depending on whether `total_count_received` < or = `count_issued`.

### 3A.4 One screen: "Items given to craftman" (order list)

- **Purpose:** Track all orders (items given to craftsman) and see **count_received** (and weight received) against each, plus workflow status.
- **Content:** Table with columns such as:
  - MC Id, Craftman, Product, Count issued, Silver issued, Amount, Issued date.
  - **Count received** (running total for that order).
  - **Weight received** (running total, if used).
  - **Workflow status** (ISSUED / PARTIALLY_RECEIVED / FULLY_RECEIVED).
  - Actions: View, **Add return** (record a partial or full return), Edit (e.g. order details before any return).
- **Filters:** By craftman, date range, workflow status.
- This can be the **Making MC index** page (same list, with "Create order" = create new labour receipt and "Add return" per row).

### 3A.5 Recording returns (one or multiple shots)

- From the order list (or from the order detail screen): **"Add return"** (or "Record return") opens a small form/section:
  - Order (read-only or link).
  - **Count received** (this shot), **Weight received** (this shot), **Date**, Remarks.
- On submit: insert one row into **Returns** table; then update the order's `total_count_received`, `total_weight_received`, and `workflow_status`.
- Repeat "Add return" for each partial return until the order shows FULLY_RECEIVED (or stop at partial if business allows).

### 3A.6 Craftman Ledger report with this model

- **Per order row:** Count Issued = `count_issued`, Count Received = `total_count_received`, Gross weight Issued = `silver_gross_weight`, Gross weight Received = `total_weight_received`.
- **Detail (optional):** Report can also list each **return** (each shot) under the order for audit (e.g. in a second table or appendix).

### 3A.7 Summary of workflow

| Step              | Action           | Where it's tracked                                      |
|-------------------|------------------|--------------------------------------------------------|
| 1. Give order     | Create order     | One row in **Orders** (MC Id, craftman, product, count issued, silver, amount); status = ISSUED. |
| 2. Receive (any)  | Add return       | One row per return in **Returns**; order's total received + status updated. |
| 3. Track          | One screen       | List all orders with count_received and workflow status. |
| 4. Report         | Craftman Ledger  | One row per order with issued/received counts and weights; totals row. |

---

## 4. Routes (similar to Invoices)

- `GET  /gem-admin/making-mc`                          → List (index).
- `GET  /gem-admin/making-mc/create`                    → Create form (Create Labour receipt).
- `POST /gem-admin/making-mc`                          → Store new receipt (creates MC Id).
- `GET  /gem-admin/making-mc/{id}`                     → Show single receipt.
- `GET  /gem-admin/making-mc/{id}/edit`                → Edit form.
- `PUT  /gem-admin/making-mc/{id}`                     → Update.
- `DELETE /gem-admin/making-mc/{id}`                   → Delete (if required).
- Optional: `GET /gem-admin/making-mc/export`          → Excel export of list (like invoices).
- Optional: `GET /gem-admin/making-mc/{id}/download-pdf` → PDF for single receipt.

**If using workflow (3A):**

- `GET  /gem-admin/making-mc/{id}/returns`            → List returns for one order (e.g. on show page).
- `GET  /gem-admin/making-mc/{id}/returns/create`     → "Add return" form (or inline modal).
- `POST /gem-admin/making-mc/{id}/returns`            → Store one return; update order totals and workflow_status.

All under `auth` (and `admin` if you keep Making MC admin-only like Report/Stock).

---

## 5. Controllers & Naming

- **Controller:** e.g. `MakingMcController` or `LabourReceiptController`.
- **Index:** Load labour receipts (paginated), optional filters (craftman, date range, status). Pass to list view.
- **Create:** Load craftmen list + product list (ProductType or Product); show form.
- **Store:** Validate Craftman, Silver gross weight, Product, Payment, Total count, Status (Issued/Received). Generate MC Id (e.g. from next id or sequence). Save. Redirect to show or index.
- **Show / Edit / Update / Destroy:** Same pattern as Invoice (show one, edit form, update, delete with confirmation if needed).

---

## 6. Views

### 6.1 List (Making MC index)

- **Layout:** Same idea as invoice list – table or cards with columns such as: MC Id (Receipt ID), Craftman Name, Product Name, Silver Gross weight, Payment, Total count, Status (Issued/Received), Date, Actions (View, Edit, Delete).
- **Actions:** “Create Labour receipt” button (links to `create`).
- Optional: filters (by craftman, date, status) and search.

### 6.2 Create / Edit form (Create Labour receipt)

- **MC Id:** Shown after create (or “New” on create). On store, generate and show e.g. `ADCR001`.
- **Fields:**
  - **Craftman Name:** Dropdown (only registered craftmen). Required.
  - **Silver Gross weight:** Number input. Required.
  - **Product name:** Dropdown from product list (ProductType or Product). Required.
  - **Payment:** Number input.
  - **Total count:** Integer input.
  - **Status:** Radio – **Issued** | **Received** (exactly one).
- Optional: **Remarks**.
- Buttons: Submit (Create / Update), Cancel.

If you store issued/received counts and weights separately, the form can:
- When “Issued” is selected: fill “Count Issued” and “Gross weight – Issued” (and optionally set received to 0 or leave for later).
- When “Received” is selected: fill “Count Received” and “Gross weight – Received”.

So the same form can capture what the ledger report needs.

---

## 7. Report Section – Craftman Ledger

### 7.1 Entry point

- Under existing **Report** section, add an option/link: **Craftman Ledger** (or “Craftman Ledger Report”).
- Page: e.g. `GET /gem-admin/report/craftman-ledger` (or under same `report` resource with a parameter).

### 7.2 Report form

- **Craftman:** Dropdown (all registered craftmen) – required for “particular craftman” report.
- Optional: Date range (from / to).
- **Submit:** “Download PDF” and/or “View” (for mobile-friendly HTML).

### 7.3 Report output (PDF and mobile view)

- **Title:** e.g. “Craftman Ledger Report – [Craftman Name]”.
- **Table columns (as in your mock-up):**

  | Sno | Receipt ID | Craftman Name | Product Name | Count Issued | Count Received | Gross weight - Issued | Gross weight - Received |
  |-----|------------|---------------|--------------|---------------|----------------|------------------------|--------------------------|

- **Rows:** One row per labour receipt for the selected craftman (within date range if applied). Sno = serial number (1, 2, 3…).
- **Totals row (last row):**
  - Total **Count Issued**.
  - Total **Gross weight - Issued**.
  - Optionally total Count Received and Gross weight - Received.
  - Other columns (Sno, Receipt ID, Craftman Name, Product Name) can be blank or “Total” in one cell.

### 7.4 PDF vs mobile view

- **PDF:** Same layout as existing report PDFs (e.g. A4 portrait, table with borders). Use same DomPDF (or current) setup.
- **Mobile view:** Same data and table, but in a responsive HTML view (e.g. same Blade view with a “mobile” or “print” class, or a dedicated route that returns HTML for small screens). No new technology required—just responsive table and optional “Download PDF” button on the same page.

### 7.5 Controller / flow

- **Report controller (or dedicated CraftmanLedgerController):**  
  - GET: Show form (craftman dropdown, optional dates).  
  - On submit (GET or POST): Load all labour receipts for selected craftman (and date range). Compute totals. Pass to:
    - PDF view → return PDF download.
    - HTML view → return responsive page (for “mb view”) with same table and optional “Download PDF” link.

---

## 8. Navigation & Access

- Add **Making MC** (or “Labour receipt” / “MC”) in the main nav (desktop and mobile), next to Invoice, Report, Stock.
- If you use `is_admin`: apply same `admin` middleware to Making MC routes and Report Craftman Ledger route so only admins see them; or leave them for all authenticated users—your choice.

---

## 9. Implementation Order (suggested)

1. **Craftman master** – Migration + model + CRUD (or minimal “list” for dropdown). Seed or UI to add craftmen.
2. **Product list** – Confirm source (ProductType vs new Product table); ensure Labour Receipt can use it.
3. **Labour Receipt (Order)** – Migration for **Orders** table (with `workflow_status`, `total_count_received`, `total_weight_received`), model, routes, MakingMcController, index + create + store (with MC Id generation), show, edit, update, delete.
4. **Returns** – Migration for **Returns** table, model (e.g. `LabourReceiptReturn`), relationship (order hasMany returns). On store: update order totals and set workflow_status (PARTIALLY_RECEIVED / FULLY_RECEIVED).
5. **Making MC list view (one screen)** – Table with MC Id, Craftman, Product, Count issued, Silver, Amount, **Count received**, **Weight received**, **Workflow status**, Actions (View, **Add return**, Edit).
7. **Create/Edit order form** – Craftman, Product, Count issued, Silver gross weight, Payment/amount, Remarks (no "Received" at create—receipt is only via "Add return").
8. **Add return** – Form (modal or separate page): order (read-only), count received, weight received, date, remarks. POST to create return and refresh order.
10. **Craftman Ledger report** – New report option, form (craftman select), query orders + totals, PDF view, responsive HTML view, totals row.
11. **Nav** – Add Making MC and Craftman Ledger link under Report.
12. Optional: Excel export for Making MC list; PDF for single receipt; list of returns per order on show page.

---

## 10. Summary

| Item                    | Description                                                                 |
|-------------------------|-----------------------------------------------------------------------------|
| **Making MC section**   | List of Labour Receipts + “Create Labour receipt” that creates MC Id (e.g. ADCR001). |
| **Labour Receipt form** | Craftman (dropdown), Silver gross weight, Product name (dropdown), Payment, Total count, Status (Issued/Received). |
| **Master data**         | Craftman list (new table or existing); Product list (ProductType or Product). |
| **Craftman Ledger**     | Under Report; select craftman → PDF and mobile view with Sno, Receipt ID, Craftman, Product, Count Issued/Received, Gross weight Issued/Received, totals row. |
| **Workflow**            | One order = one record (issued to craftman); **count_received** tracked via **Returns** (one or multiple shots); status ISSUED → PARTIALLY_RECEIVED → FULLY_RECEIVED; one screen lists all orders with received totals and "Add return" per order. |
| **Access**              | Same auth (and optional admin) as rest of app; nav entry for Making MC and report entry for Craftman Ledger. |

This plan is ready to be turned into tasks (migrations, models, controllers, views, report) without implementing code here.
