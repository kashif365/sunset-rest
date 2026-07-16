# Menu Extraction & Seeding

## Source
Two printed menu photos supplied directly by the client in chat (front board:
Cold/Hot Lunch Classics, Chicken Sandwiches, A Little Healthier, Homemade
Salads; back board: Hand Rolled Bagels, Breakfast Sandwiches, Egg Omelettes,
French Toast, Sides & More, Fresh Garden Salads, Little Taste of Latin
Culture, Fresh Bakery, For the Kids, catering call-out).

## Output
- `docs/menu/extracted-menu-data.csv` — flat export of all 93 seeded menu
  items (name, category, price, verification flag), generated from the
  database after running `database/seeders/MenuItemSeeder.php`.
- `docs/menu/menu-verification-report.md` — the 12 items whose name, price, or
  category placement was ambiguous on the printed menu, with the reasoning
  behind each best-effort reading. All are flagged
  `needs_verification = true` in the database and show a badge in the admin
  panel.

## Status
Complete. All menu content lives in the database (`menu_items` table) and is
fully editable from `/admin/menu-items`. These files are a point-in-time audit
export, not the source of truth — re-export by re-running the tinker snippet
in the delivery notes if the menu changes significantly.
