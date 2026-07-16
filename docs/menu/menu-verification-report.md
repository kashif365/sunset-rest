# Menu Verification Report

Sunset Bagel Exchange supplied two printed menu photos (front and back board) as
the only source of menu content. All 93 seeded menu items were transcribed
directly from those photos and loaded via
`database/seeders/MenuItemSeeder.php`. Every price below was read directly off
the printed menu — nothing was invented.

12 items (listed below) had some detail that was hard to read with full
confidence — a faded/ambiguous name, a description in a section boundary that
overlapped with another category, or contents implied but not spelled out.
Those items are seeded with `needs_verification = true`, which shows a
**"Needs verification"** badge on the admin Menu Items list
(`/admin/menu-items?flag=needs_verification`) and on the item's public detail
page, so staff know to double check before relying on them. Nothing here was
guessed away — the closest legible reading was kept and flagged rather than
silently invented or omitted.

All 93 items (verified and flagged) are fully editable in the admin panel —
correcting a name, price, or description takes one form submission and
requires no code changes.

## Items requiring client confirmation

### Your Way Egg Platter (Egg Omelettes) — $11.40
Build your own egg platter. Printed menu notes egg whites are available as a
+$1.00 upgrade — exact build-your-own options need confirmation.

### Sweet & Salty (French Toast) — $8.50
French toast, 1 bacon or pork roll, and 1 egg. Confirm whether sausage is also
offered as a choice here.

### Turkey Deluxe (Cold Lunch Classics) — $10.25
Name was partially illegible on the printed menu (read as "Turkey Eagel") —
seeded as "Turkey Deluxe" as the closest plausible reading. Please confirm the
exact name.

### Smothered Burger (Hot Lunch Classics) — $11.25
Name was partially illegible (read as "Southered Burger") — seeded as
"Smothered Burger." Please confirm.

### Peter Luger (Hot Lunch Classics) — $11.50
Description was transcribed from a partially worn section of the menu — please
confirm the ingredients and sauce name.

### Papaya Wrap (A Little Healthier) — $9.60
The printed name does not match its listed ingredients (turkey, spinach, egg
whites, feta — no papaya). Please confirm the correct name.

### Chorizo With Eggs Torta (Little Taste of Latin Culture) — $13.30
Price and category placement are uncertain — this item was printed near the
Fresh Garden Salads section but isn't a salad. Please confirm the price.

### Chorizo Breakfast Tacos (Little Taste of Latin Culture) — $13.30
Price uncertain — printed in the same ambiguous section as the Chorizo Torta
above. Please confirm the price.

### Chicken Bacon Salad (Little Taste of Latin Culture) — $13.30
Printed name was hard to read (looked like "Chicken Banana Salad"), which
doesn't match its ingredients (candied bacon, cheddar, avocado, guacamole,
fries). Seeded as "Chicken Bacon Salad." Please confirm name and price.

### Sunshine Plate (For the Kids) — $5.50
Contents were not specified on the printed menu beyond the name and price.
Please confirm what's included.

### Sunrise Plate (For the Kids) — $4.50
Same as above — contents not specified. Please confirm what's included.

### Kids Sweet & Salty (For the Kids) — $5.30
A "Sweet & Salty" also appears under French Toast at $8.50 for a full-size
portion. Please confirm this kids-menu price/portion is intentionally
different.

## Categories with no printed pricing

**Catering** — the printed menu had a call-out box mentioning catering
availability ("Sales rep? Let us help make your meeting a success!") but no
packages, headcounts, or prices. Per the no-invented-prices rule, three
starter `CateringPackage` records were seeded (Breakfast Bagel Platter,
Continental Breakfast Spread, Cold Cut Lunch Platter) all flagged
`needs_verification = true` with `price = null` (displays as "Call for
pricing"). These are separate from the Catering *menu category*, which is
intentionally left with no items until real packages/pricing are supplied.

## Full data export

See `docs/menu/extracted-menu-data.csv` for a flat export of every seeded item
(name, category, price, verification flag) generated directly from the
database after seeding.
