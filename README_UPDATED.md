# Smart Cafeteria – Updated Minor Project

## New changes

- Login and registration now have browser-side validation plus server-side validation.
- Password, username, email and mobile rules are checked before registration/login processing.
- Logout clears the PHP session and session cookie, then redirects to the login page.
- Protected pages send no-cache headers, so browser Back will not reopen the dashboard after logout.
- Login regenerates the session ID and dashboard/admin access remains session protected.
- Admin order management is available inside `admin_dashboard.php`.
- Admin can update an order as Pending, Confirmed, Preparing, Ready for Pickup, Completed / Done, Cancelled, Refund Requested or Refunded.
- Admin can store an optional note for every order.
- Daily collection excludes cancelled and refunded orders.
- The existing 5-minute Active Now user status is preserved, including after logout.

## Database update (required once)

1. Open phpMyAdmin.
2. Select the `smart_cafeteria` database.
3. Open `database_update.sql` and execute it once.
4. Then copy this project folder into the XAMPP `htdocs` directory.
5. Check the database credentials in `config.php` and open `login_page.php`.

The migration adds `order_status`, `admin_note` and `status_updated_at` to the `orders` table. Existing orders are set to `pending`; new orders are inserted as `pending` automatically.

## Admin workflow

Login with an admin user, open the Control Center, scroll to **Order Status Management**, select a new status, optionally write a note, and click the green check button. The customer-facing ordering logic is preserved.

## Important note

Run the SQL migration before opening the new admin order table. The migration is intended to be run once on the existing database.
