# Invoice Changelog System - Implementation Proposal

## Overview
Track all changes made to invoices including who made the change, when, what field changed, and the old/new values.

## Database Design

### Table: `invoice_changelogs`
```sql
CREATE TABLE `invoice_changelogs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `action` VARCHAR(50) NOT NULL, -- 'created', 'updated', 'deleted', 'status_changed', etc.
  `field_name` VARCHAR(255) NULL, -- Field that changed (NULL for create/delete)
  `old_value` TEXT NULL, -- Previous value
  `new_value` TEXT NULL, -- New value
  `field_label` VARCHAR(255) NULL, -- Human-readable field name
  `description` TEXT NULL, -- Custom description of the change
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_invoice_id` (`invoice_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Implementation Approach

### Option 1: Model Observer (Recommended)
- Use Laravel Model Observers to automatically track changes
- Clean separation of concerns
- Easy to maintain

### Option 2: Event Listeners
- Use Laravel Events (creating, created, updating, updated)
- More granular control

### Option 3: Package (Spatie Activity Log)
- Use `spatie/laravel-activitylog` package
- Feature-rich but adds dependency

## Features to Track

### Fields to Monitor:
1. **Basic Info**: customer_name, location, assignee_name
2. **Status**: status, delivered_date
3. **Counts & Weights**: total_count, actual_silver_weight
4. **Costs**: stone_cost, wastage_making_certification_cost
5. **Product**: product_type_id
6. **Toggle**: toggle_silver_cost
7. **Remarks**: remarks
8. **Invoice Details**: Product additions/removals/updates

### Actions to Track:
- **Created**: When invoice is first created
- **Updated**: When any field is modified
- **Deleted**: When invoice is deleted
- **Status Changed**: Special tracking for status changes
- **Product Added**: When product is added to invoice
- **Product Removed**: When product is removed
- **Product Updated**: When product details change

## UI Design Ideas

### 1. Changelog Tab in Invoice View/Edit Page
- Add a "Changelog" or "History" tab
- Timeline view showing all changes
- Group by date
- Color-coded by action type

### 2. Changelog Modal
- Click "View History" button
- Modal with scrollable changelog
- Filter options (date range, user, field)

### 3. Inline Changelog Section
- Expandable section at bottom of invoice view
- Shows last 5 changes by default
- "Show All" button to expand

### 4. Changelog Page
- Dedicated page: `/gem-admin/invoices/{id}/changelog`
- Full history with filters
- Export option

## Display Format Examples

### Timeline View:
```
📅 02-02-2026 14:30:25
👤 John Doe
✅ Invoice Created
   - Customer: New Customer
   - Status: NEW

📅 02-02-2026 15:45:10
👤 Jane Smith
🔄 Status Changed
   - From: NEW → To: ASSIGNED

📅 02-02-2026 16:20:15
👤 Jane Smith
✏️ Field Updated
   - Total Count: 5 → 10
   - Assignee: John → Jane
```

### Table View:
| Date/Time | User | Action | Field | Old Value | New Value |
|-----------|------|--------|-------|-----------|-----------|
| 02-02-2026 14:30 | John Doe | Created | - | - | - |
| 02-02-2026 15:45 | Jane Smith | Updated | Status | NEW | ASSIGNED |
| 02-02-2026 16:20 | Jane Smith | Updated | Total Count | 5 | 10 |

## Implementation Steps

1. **Create Migration** for `invoice_changelogs` table
2. **Create Model** `InvoiceChangelog` with relationships
3. **Create Observer** `InvoiceObserver` to track changes
4. **Update InvoiceController** to log manual actions
5. **Create Changelog View** component/page
6. **Add Route** for changelog access
7. **Style the UI** with Tailwind CSS

## Additional Features (Optional)

1. **Filtering**: By user, date range, field, action type
2. **Search**: Search within changelog descriptions
3. **Export**: Export changelog as PDF/CSV
4. **Notifications**: Email on critical changes
5. **Revert**: Ability to revert to previous values
6. **Compare**: Side-by-side comparison of two versions
7. **Activity Summary**: Dashboard showing recent invoice activities

## Field Label Mapping

For better readability, map field names to labels:
```php
[
    'customer_name' => 'Customer Name',
    'location' => 'Location',
    'status' => 'Status',
    'total_count' => 'Total Count',
    'actual_silver_weight' => 'Actual Silver Weight',
    'assignee_name' => 'Assignee',
    'product_type_id' => 'Product Type',
    // ... etc
]
```

## Value Formatting

Format values for better display:
- **Status**: Show status name instead of code
- **Dates**: Format as dd-mm-YYYY
- **Numbers**: Format with decimals
- **Booleans**: Show "Yes/No" or "Enabled/Disabled"
- **Relationships**: Show related model name (e.g., Product Type name)
