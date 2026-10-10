# FreshCatch — Online Fish Marketplace

FreshCatch is a full-stack PHP/MySQL marketplace that connects buyers with local fish sellers. It was built to demonstrate a practical Web Programming project using server-rendered PHP, vanilla JavaScript, relational data modelling, security controls, and responsive CSS.

## Stack

- HTML5, CSS3 and Vanilla JavaScript
- PHP 8+ with PDO prepared statements and sessions
- MySQL / MariaDB
- Apache through XAMPP

## Features

- Buyer/seller registration, session login, password hashing, and role-based page protection.
- Seller approval workflow: new shops are pending until an administrator approves them.
- Searchable, filterable fish catalogue with category and price filters.
- Seller product CRUD, regular stock/availability updates, and restricted image uploads.
- Multi-seller cart, transactional checkout, snapshot pricing, stock validation and stock reduction.
- Parent order plus seller orders, so each seller sees and updates only their portion of an order.
- Buyer addresses with add/edit/delete, order history, progress tracking, and eligible pending-order cancellation.
- Checkout choices for Cash on Delivery or simulated UPI: a clearly labelled demo QR screen records a paid demo payment without contacting a bank.
- Admin dashboards for seller approvals, users, categories, listings, orders and reports.
- CSRF tokens on state-changing forms, escaped output, input checks, authorization ownership checks, and upload MIME/size validation.

## XAMPP setup

1. Copy this `freshcatch` folder to `C:\xampp\htdocs\freshcatch` (or keep its folder name and update `BASE_URL` in `includes/config.php`).
2. Start **Apache** and **MySQL** in XAMPP.
3. For a new database, open phpMyAdmin, choose **Import**, and import `database/schema.sql`.
   For an existing database, import `database/migration_payment.sql` once to add UPI/COD payment fields. Do not re-import the full schema into an existing database.
4. Check credentials in `includes/config.php`. XAMPP defaults are `root` with an empty password.
5. Visit `http://localhost/freshcatch/`.

For Linux XAMPP, keep the project at `/opt/lampp/htdocs/freshcatch` and visit `http://localhost/freshcatch/`. Ensure Apache can write to `uploads/products/` before sellers upload photos.

## Demo accounts

All seeded accounts use password: `password`.

| Role | Email |
|---|---|
| Admin | admin@freshcatch.test |
| Buyer | buyer@freshcatch.test |
| Seller | seller1@freshcatch.test |
| Seller | seller2@freshcatch.test |

If an old import rejects the documented password, run `database/repair-demo-accounts.sql` once in phpMyAdmin. It preserves products and orders while repairing only the demo accounts.

## Folder structure

```
freshcatch/
├── admin/              # marketplace administration
├── assets/css, assets/js
├── buyer/              # buyer account screens
├── database/schema.sql # complete schema and seed data
├── includes/           # config, PDO connection, auth, shared layout
├── seller/             # shop, listings, fulfillment and sales
├── uploads/products/   # validated seller image uploads
├── cart.php, checkout.php, orders.php
└── index.php, products.php, product.php
```

## ER diagram description

`users` is the central identity table. A seller user has one `seller_profiles` record, while a buyer user can own many `buyer_addresses`, one `carts` record, many `orders`, `reviews`, and `reports`. A seller profile owns many `products`; every product belongs to one admin-managed `categories` record. A cart owns many `cart_items`, each referencing a product.

At checkout, one buyer `orders` record is created. It owns one or more `seller_orders` records (one per seller in the cart); each seller order owns its `order_items`. Each order item retains `product_name` and `unit_price` from purchase time, preserving history after listings change. `reviews`, `reports`, and `notifications` connect buyer feedback, moderation, and messages to relevant users/products.

## Key flow

1. Buyer registers, signs in, browses verified seller products, adds quantities that do not exceed stock, then selects delivery/pickup and an address.
2. Checkout starts a database transaction, locks product stock, validates it again, creates parent/seller/item order records, records price snapshots, deducts stock, and clears the cart atomically.
3. Seller updates only their own `seller_orders`; admin approves shops and moderates marketplace entities.

## Demo payment flow

At checkout select **UPI Demo** to see the dynamic total and a non-bank QR illustration for `freshcatch@upi`. The **I've Completed Payment** button creates the order with `payment_method = UPI_DEMO` and `payment_status = paid`. Select **Cash on Delivery** to create it with `payment_method = COD` and `payment_status = pending`.

## Future improvements

- Payment gateway integration and payment states.
- Per-seller delivery fee calculation and automatic parent-order status aggregation.
- Product/seller reviews UI and report-submission form (database support is ready).
- Email/SMS notifications and image resizing.
