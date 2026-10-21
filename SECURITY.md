# FreshCatch Security Notes

FreshCatch uses prepared PDO statements, `password_hash`, `password_verify`, session authentication, CSRF form tokens, output escaping, role checks, ownership checks, transaction-based checkout, and validated product image uploads.

## XAMPP production-style setup

1. Keep the `database/` folder outside the web root if possible. If it stays in `htdocs`, the included `.htaccess` blocks web access when Apache `AllowOverride` is enabled.
2. Ensure Apache can write only to `uploads/products/`, never to the whole application folder.
3. Set a dedicated MySQL account with access only to the `freshcatch` database. Do not use a root account for a deployed site.
4. Set `FRESHCATCH_DB_USER`, `FRESHCATCH_DB_PASS`, and `FRESHCATCH_ENV=production` as Apache environment variables. Do not commit real credentials.
5. Use HTTPS for any internet-facing deployment. PHP then marks the session cookie as `Secure` automatically.
6. Keep PHP, Apache, MySQL/MariaDB, and XAMPP patched.

## Limits

This is a college project. The login throttle is session-based; a public production system should additionally use shared rate limiting, audit logging, password-reset email verification, MFA for administrators, backups, HTTPS certificates, and ongoing dependency/server patching.
