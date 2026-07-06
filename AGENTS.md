# Pa-Ecommerce — Agent Guide

## Architecture

- **Two auth guards:** `auth('customer')` for frontend (Fortify), `auth('web')` for admin/owner (Filament)
- **Two Filament panels:** `AdminPanelProvider` at `/admin` (super_admin), `OwnerPanelProvider` at `/owner` (owner role)
- **Same guard limitation:** admin + owner cannot be logged in simultaneously in one browser (same session)
- **Frontend:** Livewire v3 components, Tailwind CSS v4, Vite

## Auth Flow

- Customer uses Fortify (`app/Actions/Fortify/CreateNewCustomer.php`, `app/Providers/FortifyServiceProvider.php`)
- B2B customers auto-logout after register with redirect to login
- B2B pending/rejected are blocked at login with custom error (Fortify's `authenticateUsing`)
- Admin/owner use Filament's built-in login (panel-specific)

## Directory Conventions

- Filament Resources: `app/Filament/Resources/{Plural}/` — `{Name}Resource.php`, `Pages/`, `Schemas/{Name}Form.php`, `Tables/{Name}Table.php`
- Livewire components: `app/Livewire/Customer/` for customer-facing, root `app/Livewire/` for public
- Filament Pages share the `app/Filament/Pages/` directory; use `$isDiscovered = false` to prevent cross-panel auto-discovery
- Email Mailables: `app/Mail/` (all implement `ShouldQueue`)

## Key Packages

| Package | Usage |
|---------|-------|
| `filament/filament` v5 | Admin + Owner panels |
| `laravel/fortify` | Customer auth (login, register) |
| `spatie/laravel-permission` via `bezhansalleh/filament-shield` | Roles: `super_admin`, `owner` |
| `midtrans/midtrans-php` | Payment (Corporate VA for B2B) |
| `barryvdh/laravel-dompdf` | Invoice PDF download |
| `flowframe/laravel-trend` | Revenue chart aggregation |

## Commands

```bash
composer run dev      # starts server + queue + logs + vite (concurrently)
composer run test     # config:clear then phpunit
php artisan rfq:expire     # manual: expire quoted RFQs past valid_until
```

## Database Quirks

- `config/database.php` mysql connection has `ONLY_FULL_GROUP_BY` disabled via `modes` array
- `Trend::model()->where()` does NOT exist — use `Trend::query(Order::where(...))->between(...)` instead
- `TableWidget` queries with `GROUP BY` + no `id` in select: override `getTableRecordKey()` to return a unique column

## Filament Conventions

- Page `canAccess()` checked at navigation time AND route access time (mount → 403)
- Widget `canView()` hides the widget from the page
- `EditOrder::afterSave()` must call `updateStatus()` manually when status changes (form save doesn't create history entries)
- `Dashboard` has `$routePath = '/'`; custom dashboards override `getWidgets()`, `canAccess()`, and `$routePath`

## RFQ Status Flow

`draft` → `submitted` → `under_review` → `quoted` → `accepted` / `rejected` / `expired`

- Admin sets `quoted_price` per item in Filament Repeater; auto-calculates totals
- "Kirim Penawaran" action changes status to `quoted` and emails customer
- `acceptQuotation()` creates Order + Midtrans Snap token (VA-only), redirects to payment

## Order Status Flow

`pending` → `processing` → `shipped` → `delivered` (or `cancelled`)

- Admin changes status via EditOrder form → `afterSave()` creates `OrderStatusHistory` entry
- History shown in customer-facing progress tracker (4 steps + optional cancelled)

## Payment (Midtrans)

- B2B orders (RFQ + direct checkout): `enabled_payments` restricted to VA channels only
- `customer_details.first_name` uses `company_name` for B2B
- Webhook route: `POST /webhook/midtrans` (no auth middleware)

## Environment

- DB: MySQL (local), queue: database, cache: database, session: database
- Mail: SMTP Gmail with app password
- Midtrans sandbox (`MIDTRANS_IS_PRODUCTION=false`)
- Pre-existing admin: `admin@gmail.com` / `password` (super_admin)
