# Silver Stock Management – Plan for Client

## What is this feature?

Today, when you prepare invoices (jewellery orders), the app does not track how much **silver** you have in stock. This new feature will:

1. **Let you enter** how much silver stock you have.
2. **Automatically reduce** that stock whenever an invoice is prepared (based on silver used in that invoice).
3. **Show you** how much silver is **used** and how much is **remaining** at any time.

So you always know: *“How much silver do I have left?”* and *“How much did I use for orders?”*.

---

## How it will work (simple flow)

```
You add silver stock (e.g. 1000 grams)
        ↓
You create/prepare an invoice (e.g. uses 50 grams of silver)
        ↓
System reduces stock: 1000 - 50 = 950 grams remaining
        ↓
You can see: Used = 50 g, Remaining = 950 g
```

- **Stock** = silver you have available (from “Add stock” minus “Sold”).
- **Used** = silver consumed by invoices (preparations).
- **Remaining** = stock minus used = what is left for future orders.
- **Negative stock:** If you keep adding invoices when there is zero (or low) stock, the system will still allow it. **Remaining stock can go negative** (e.g. -50 g). This shows you that you have used more silver than you had in stock (useful until you add or buy more silver).

---

## Screens you will get

### Screen 1: Stock / Dashboard (main view)

**Where:** A new menu item, e.g. **“Stock”** or **“Silver Stock”**, in the top navigation (next to Invoice, Report, etc.).

**What you see:**

| Label           | Meaning                          |
|-----------------|----------------------------------|
| **Current stock** | Total silver (in grams) you have added so far. |
| **Used stock**    | Total silver (in grams) used in invoice preparations. |
| **Remaining stock** | Current stock minus used = silver left for new orders. |

- **“Add stock”** button – to add new silver (e.g. when you buy or receive silver).
- **“Sell silver”** button – to record silver sold (not used in invoices). This deducts from your total stock so remaining goes down.
- Optional: a small **history** or **list** of Add stock, Sell, and invoice usage entries.

**In short:** One screen that answers: *How much silver do I have? How much is used? How much is left?* and lets you Add stock or Sell silver.

---

### Screen 2: Add stock

**When:** You click **“Add stock”** from the main Stock screen.

**What you see:**

- A form with:
  - **Amount** (e.g. grams of silver).
  - **Date** (optional, defaults to today).
  - **Remarks / notes** (optional, e.g. “Purchase from supplier X”).
- Buttons: **Save** and **Cancel**.

**After Save:** You go back to the Stock screen and “Current stock” (and “Remaining stock”) increase by the amount you added.

---

### Screen 2b: Sell silver

**When:** You click **“Sell silver”** from the main Stock screen.

**What you see:**

- A form with:
  - **Amount** (grams of silver sold).
  - **Date** (optional, defaults to today).
  - **Remarks / notes** (optional, e.g. “Sold to customer X”, “Cash sale”).
- Buttons: **Save** and **Cancel**.

**After Save:** The **total stock** is reduced by the sold amount. So “Remaining stock” also goes down by that amount. This is separate from invoice usage – it is for when you sell raw silver (not through an invoice). Selling is allowed even if remaining stock is low; **stock can go negative** after a sale (e.g. 10 g left, you sell 20 g → remaining = -10 g).

---

### Screen 3: Invoice (existing screen – small change)

**What changes:**

- When you **create** or **update** an invoice, the app will use the **silver weight** (or similar) from that invoice to:
  - **Increase “Used stock”** by that amount.
  - **Reduce “Remaining stock”** by that amount.
- No extra button for you; it happens automatically when you save the invoice.

**You still work on invoices as today;** the system will just “consume” silver from stock in the background. **Even if remaining stock is zero or negative,** you can still create or edit invoices; the system will keep deducting and remaining stock can become more negative (e.g. -100 g). This way you never get blocked, and you can see how much you are “short” until you add or buy more silver.

---

### Screen 4: Stock history (optional)

**Where:** A link or tab from the main Stock screen, e.g. **“History”**.

**What you see:**

- A list of:
  - **Add stock** entries: date, amount added, remarks.
  - **Sell silver** entries: date, amount sold, remarks.
  - **Usage** entries: date, invoice number, amount used (silver consumed by invoices).
- This helps you audit: *“When did I add stock?”*, *“When did I sell silver?”*, and *“Which invoice used how much?”*.

---

## Summary for client (layman level)

| What | Explanation |
|------|-------------|
| **Silver stock** | You enter how much silver you have (e.g. in grams) via “Add stock”. |
| **Sell silver** | You can record silver sold (raw sale, not invoice). This deducts from total stock; remaining stock goes down. Stock can go negative if you sell more than you have. |
| **After each invoice** | The app reduces your stock by the silver used in that invoice. |
| **Remaining stock** | Shown on the Stock screen = what you have left. Can be **negative** if you add invoices or sell when stock is zero (shows how much you are short). |
| **Used stock** | Shown on the Stock screen = total silver consumed by invoices. |
| **Negative stock** | Allowed. If you keep adding invoices (or sell) with zero stock, remaining can become -ve (e.g. -50 g). You are never blocked; you can always add invoices and see how much you need to add later. |
| **Screens** | (1) Stock summary + Add stock + Sell silver, (2) Add stock form, (2b) Sell silver form, (3) Invoice (unchanged for you, auto-deduction), (4) Optional history (add / sell / usage). |

---

## Technical outline (for development)

- **New table(s):** e.g. `silver_stock` (running total or opening balance) and `stock_transactions` to store:
  - **Add stock:** type = add, amount, date, remarks.
  - **Sell silver:** type = sell, amount, date, remarks (deducts from total stock).
  - **Usage:** type = invoice_usage, invoice_id, amount, date (deducts from remaining when invoice is saved).
- **Remaining stock:** Computed as (total added − total sold − total invoice usage). **Allow negative** (no constraint or check that blocks when stock is below zero).
- **Link to invoice:** Use existing invoice field(s) for silver weight to deduct on create/update; deduction happens even when current remaining is 0 or negative.
- **Screens:** One main Stock page (summary + Add stock + Sell silver), Add stock form, Sell silver form, optional History list (add/sell/usage); Invoice screen unchanged except backend deduction.
- **Access:** Same login as rest of app; restrict add/sell to relevant users if required.

---

## Next steps

1. Confirm with client: *“Do you want exactly these screens and this flow?”* (Stock summary, Add stock, **Sell silver**, Invoice auto-deduction, optional History.)
2. Confirm that **negative stock** is acceptable (invoices and sell allowed when stock is zero; remaining can go negative).
3. Confirm unit: grams (or other) for silver.
4. Confirm which invoice field(s) represent “silver used” (e.g. one per invoice or sum of items).
5. After approval, implement: database changes → Stock screens (including Sell silver) → link invoice save to stock deduction → allow negative remaining stock.
