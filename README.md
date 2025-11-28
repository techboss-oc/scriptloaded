# Scriptloaded

A modern single-seller (admin) marketplace for website scripts, templates, themes, plugins and app source codes.

## Requirements

- PHP 8.1+
- MySQL 8+

## Setup

1. Create a MySQL database and import `sql/schema.sql` followed by `sql/seed.sql`.
2. Copy `.env.example` to `.env` and adjust the values to match your local credentials.
3. Ensure your web root points to the project root (or move the public files as needed).
4. Set writable permissions for `logs/` and any upload directory you add later.

## Configuration & Environment

- `inc/config.php` is the single source of configuration. On every request it loads key/value pairs from `.env` (falling back to `.env.example` when a custom file is missing) into `$_ENV` and exposes them as `$DB_HOST`, `$DB_NAME`, `$DB_USER`, `$DB_PASS`, and `$BASE_URL`.
- `inc/db.php` requires `inc/config.php` and opens the PDO connection with those values, so the effective database credentials always come from your `.env` file.

### Environment variables

| Key                     | Purpose                                      | Default                         |
| ----------------------- | -------------------------------------------- | ------------------------------- |
| `DB_HOST`               | Database host used by PDO                    | `localhost`                     |
| `DB_NAME`               | Database name                                | `scriptloaded`                  |
| `DB_USER`               | Database user                                | `root`                          |
| `DB_PASS`               | Database password                            | `` (blank)                      |
| `BASE_URL`              | Absolute URL used in emails/links            | `http://localhost/scriptloaded` |
| `CURRENCY_RATE_USD_NGN` | Conversion rate fallback for currency helper | `1500`                          |
| `ADMIN_EMAIL`           | Notification sender/support email            | `admin@scriptloaded.test`       |

Update `.env` whenever these need to change—no PHP code edits are required.

## Running (Built-in Server)

```bash
php -S localhost:8000 -t .
```

Open http://localhost:8000/index.php

## Currency

Toggle via `?currency=USD` or `?currency=NGN` on product links (demo).

## Seeded accounts

After importing `sql/seed.sql` you can log in with:

| Role         | Email                       | Password      |
| ------------ | --------------------------- | ------------- |
| Admin        | `admin@scriptloaded.test`   | `Admin@123`   |
| Creator/User | `creator@scriptloaded.test` | `Creator@123` |

Use the admin account for marketplace management and the creator account to explore the user dashboard.

- Admins sign in at `http://localhost/scriptloaded/admin/login.php`.
- Creators/users sign in at `http://localhost/scriptloaded/user/login.php`.

## Security Notes

- Always output user data with `escape_html()`.
- Protect every POST form with the CSRF helpers in `inc/csrf.php`.

## Next Steps

- Wire the add-card and support forms to real payment/help-desk services.
- Build the admin CRUD workflows for products and orders.
- Replace the mocked billing actions (set primary/edit/delete) with live endpoints.

## Tests (Planned)

Outline in JSON spec (`scriptloaded_ai_prompt.json`).
