# Hypermarket

Project for the DAW (Web Application Development) course — PHP + MySQL.

## About the project

**Theme:** Hypermarket activities. Customers browse the product catalog and leave reviews;
employees manage products and categories; administrators manage employee accounts and view
sales/visit reports.

**Roles:**

- **Customer** — self-registers, browses/searches the catalog, views product details, and
  writes/edits/deletes their own reviews.
- **Employee** — created by an administrator; manages products and categories (create/update/
  delete).
- **Administrator** — created by another administrator (or bootstrapped directly); manages
  employee accounts and views exportable sales/visit reports, in addition to everything an
  employee can do.

**Main components:**

- `/public` — entry pages: catalog, product details, login/registration, contact, and
  per-role panels.
- `/includes` — shared header/footer/nav templates.
- `/lib` — generic functions for the database, auth, CSRF, CAPTCHA, reports, PDF/CSV export,
  and external content fetching.
- `/actions` — form handlers: auth, registration, product/category/review CRUD, employee CRUD,
  contact, and report export.

**Database:** MySQL, accessed via PDO with prepared statements. Main tables: `users`,
`categories`, `products`, `reviews`, `site_visits`.

**Security:** session-based authentication with password hashing, CSRF tokens on every form,
output escaping against XSS, prepared statements against SQL injection, and CAPTCHA on the
public contact and registration forms.

## Running locally

```
make up
make create-db
make seed-db
```

Then open http://localhost:8000.

Other useful commands: `make down` (stop containers), `make restart`, `make logs`.

## Demo accounts

Seeded by `sql/seed.sql`. All accounts share the same password.

| Role     | Email                        | Password    |
|----------|-------------------------------|-------------|
| Admin    | admin@hypermarket.test        | Demo1234!   |
| Employee | employee1@hypermarket.test    | Demo1234!   |
| Employee | employee2@hypermarket.test    | Demo1234!   |
| Customer | customer1@hypermarket.test    | Demo1234!   |
| Customer | customer2@hypermarket.test    | Demo1234!   |
