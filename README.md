# 🛒 Laravel E-Commerce Platform

A full-stack e-commerce platform built with **Laravel 12** and **Inertia.js (Vue.js)**, featuring a complete shopping experience, admin dashboard, and intelligent business logic.

---

## 📋 Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Core Features](#core-features)
- [Data Flow: From Cart to Order](#data-flow-from-cart-to-order)
- [Business Logic](#business-logic)
- [Admin Dashboard](#admin-dashboard)
- [Authentication & Authorization](#authentication--authorization)
- [Testing](#testing)
- [Installation](#installation)

---

## Overview

This platform provides a complete B2C e-commerce solution with:
- Guest and authenticated shopping cart
- Coupon and discount system
- Order management with inventory tracking
- Admin dashboard with analytics
- VIP customer tier system
- Role-based access control

---

## Tech Stack

| Layer | Technology | Purpose |
|---|---|---|
| Backend | Laravel 12 | Core framework, routing, ORM |
| Frontend | Inertia.js + Vue.js | SPA-like experience without API overhead |
| Routing (Frontend) | Ziggy | Shares Laravel named routes with Vue |
| Auth | Laravel Breeze | Authentication scaffolding |
| Social Login | Laravel Socialite | OAuth login (Google, etc.) |
| Database | PostgreSQL / MySQL | Production database |
| Testing DB | SQLite (in-memory) | Fast, isolated test runs |
| CI/CD | GitHub Actions | Automated testing on push |

---

## Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/              # Admin-only controllers
│   │   │   ├── DashboardController.php
│   │   │   ├── ProductController.php
│   │   │   ├── OrderController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── AnalyticsController.php
│   │   │   └── InventoryController.php
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   ├── ShopController.php
│   │   └── HomeController.php
│   └── Middleware/
│       └── AdminMiddleware.php
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Order.php / OrderItem.php
│   ├── Cart.php / CartItem.php
│   ├── Coupon.php
│   ├── Category.php / Brand.php
│   └── HomeSetting.php
└── Services/
    ├── CartService.php         # All cart calculation logic
    └── CustomerService.php     # VIP tracking & customer metrics
```

**Why this structure?**
Controllers are kept thin — they receive the request, delegate to Services, and return the response. All business logic lives in `Services/`, making it testable in isolation without HTTP overhead.

---

## Core Features

### 🛍️ Shop
- Product listing with category and brand filtering
- Individual product pages with variants (color, size)
- Stock tracking per product

### 🛒 Cart System
- Works for **both guests and logged-in users**
- Guests use a `session_id` to persist the cart
- On login, the guest cart automatically **merges** with the user's saved cart
- Why this design? It prevents lost carts and improves conversion rate

### 🏷️ Coupon System
- Two coupon types: **percentage** (e.g. 20% off) and **fixed value** (e.g. 10€ off)
- Validation includes: expiry date, usage limit, minimum order value
- Applied at cart level, recalculated on every change

### 📦 Orders
- Full order history for customers
- Per-order status tracking (pending, processing, shipped, delivered)
- Stores a snapshot of product data at purchase time (so price changes don't affect old orders)

---

## Data Flow: From Cart to Order

This is the core journey of the application:

### Step 1 — Adding to Cart
```
User clicks "Add to Cart"
    → CartController@store
    → CartService::addItem()
    → Creates/updates CartItem linked to Cart (by user_id or session_id)
```

### Step 2 — Cart Calculation (CartService)
Every time the cart is viewed or modified, `CartService` recalculates:

```
subtotal = sum of (price × quantity) for all items

discount = coupon applied?
    → percentage coupon: subtotal × (discount% / 100)
    → fixed coupon:      flat amount off

shipping = subtotal > 150€ ? FREE : standard rate

total = subtotal - discount + shipping
```

**Why in a Service?** This logic is reused across CartController, CheckoutController, and tests. A single source of truth prevents calculation mismatches.

### Step 3 — Checkout (CheckoutController)
The entire checkout runs inside a **database transaction**:

```php
DB::transaction(function () {
    1. Validate stock availability for all items
    2. Create Order record (with address, totals, payment status)
    3. Create OrderItem for each CartItem (snapshot of product + price)
    4. Decrement stock for each product
    5. Clear the cart
    6. Send notification to admin
});
```

**Why a transaction?** If any step fails (e.g. a product goes out of stock mid-checkout), the entire operation rolls back. No partial orders, no phantom stock deductions.

### Step 4 — Post-Order
```
Order confirmed
    → Admin receives notification
    → CustomerService updates customer metrics (total spent)
    → VIP status recalculated if total spent >= 1000€
```

---

## Business Logic

### VIP Customer System (`CustomerService`)
Customers are automatically upgraded to VIP status when their cumulative spending reaches **1000€**.

```
CustomerService::recalculateVipStatus($user)
    → sum all completed orders for this user
    → if total >= 1000€ → set is_vip = true
```

VIP customers can receive special pricing, early access, or other perks configurable from the admin panel.

### Free Shipping Threshold
Handled in `CartService` — if `subtotal > 150€`, shipping is set to 0. This threshold is evaluated fresh on every cart recalculation, so it updates in real time as the user adds or removes items.

### Admin Notifications
When a new order is placed, `CheckoutController` dispatches a notification to all admin users. This uses Laravel's built-in notification system and is delivered through the database channel, visible in the admin dashboard.

---

## Admin Dashboard

Protected by `['auth', 'verified', 'admin']` middleware stack.

### Dashboard Metrics (`DashboardController`)
- **Sales Growth Rate** — compares current month revenue vs. previous month
- **Returning Customer Rate** — percentage of customers with more than one order
- **Weekly Sales Chart** — revenue grouped by day for the last 7 days

### Admin Panels
| Panel | Functionality |
|---|---|
| Products | Full CRUD, stock management |
| Orders | View all orders, update status |
| Customers | View profiles, toggle active/banned, toggle VIP manually |
| Inventory | Bulk stock updates per product |
| Analytics | Revenue and order trends |
| Settings | Homepage content (HomeSetting model) |

---

## Authentication & Authorization

### Role System
Roles are stored as a column on the `users` table (`role: 'admin' | 'user'`).

**Why not Spatie Roles?** For a two-role system, a simple column is faster and has no extra dependencies. Spatie is worth adding if roles grow in complexity.

### Middleware Stack for Admin Routes
```php
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')
```

Three layers:
1. `auth` — must be logged in
2. `verified` — email must be verified
3. `admin` — `AdminMiddleware` checks `user->role === 'admin'`

If any layer fails, the request is redirected before reaching the controller.

### Guest Access
Routes like `/cart`, `/shop`, and `/checkout` are intentionally **public** — guests can browse and add to cart without registering. Authentication is only required at checkout to attach the order to an account.

---

## Testing

Tests are organized in two layers:

### Unit Tests (`tests/Unit/`)
| Test | What it verifies |
|---|---|
| `CartServiceTest` | Subtotal calculation, coupon application logic |

These test `CartService` directly with no HTTP, no database, and no framework overhead. Fast and deterministic.

### Feature Tests (`tests/Feature/`)
| Test | What it verifies |
|---|---|
| `AdminAccessTest` | Guests and regular users are blocked; admins can access dashboard |
| `CartTest` | Adding and removing products from cart via HTTP |
| `Auth/*` | Login, registration, password reset, email verification flows |
| `ProfileTest` | Profile update and account deletion |

### CI Configuration
GitHub Actions runs the full test suite on every push to `main` and `develop`:

```yaml
- PHP 8.2 with SQLite in-memory database
- Composer dependencies installed
- App key generated from .env.example
- php artisan test
```

**Note:** `withoutVite()` is called in `TestCase::setUp()` to skip Vite manifest resolution during tests. This means the frontend build step is not required in the Laravel test job, keeping CI fast.

---

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/andmurturi0/SaaS.git
cd SaaS

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies and build assets
npm install && npm run build

# 4. Configure environment
cp .env.example .env
php artisan key:generate

# 5. Set up the database
php artisan migrate --seed

# 6. Start the development server
php artisan serve
```

### Environment Variables (`.env`)
```env
DB_CONNECTION=mysql
DB_DATABASE=saas
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
# Required for email verification and password reset
```

---

## Project Decisions Summary

| Decision | Reason |
|---|---|
| Inertia.js over REST API | Keeps routing and auth on the server; no separate API auth needed |
| CartService as a class | Reusable, testable, single source of truth for cart math |
| DB::transaction in checkout | Atomicity — no partial state if something fails mid-order |
| Role column vs. Spatie | Simpler for two roles; easy to migrate later if needed |
| SQLite for tests | No MySQL setup needed in CI; in-memory = fast teardown |
| withoutVite() in tests | Decouples backend tests from frontend build pipeline |
