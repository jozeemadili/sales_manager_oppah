# Modules

This app runs two parallel business lines — **Mbao** (timber) and **Hardware**
(branded "Tuli" in code) — plus shared Reports, Logistics, and Settings.
Scope of "live" here is exactly what's linked (not commented out) in
`resources/views/layouts/admin/partials/sidebar.blade.php`; see `ammendments.md`
for the refactor plan this doc supports.

## Roles

Four roles are in active use, checked via `Auth::user()->role` throughout the
sidebar and controllers:

| Role | Access |
|---|---|
| `ADMIN` | everything |
| `Mbao` | Summary Mbao, Inventory (mbao), Reports, Settings |
| `Hardware` | Summary Hardware, Inventory (hardware), Reports, Settings |
| `Driver` | Summary Trucks, Logistics (driver view only), Settings |

## Live sidebar sections → backend

| Section | Roles | Controllers |
|---|---|---|
| Summary | Mbao/ADMIN, ADMIN/Driver, Hardware/ADMIN (three separate dashboards) | `PortalUsersController`, static Blade views |
| Inventory (mbao) | Mbao, ADMIN | `Stock\StoresController` (stores), `Stock\CategoriesController` (categories/inventories/expenses/suppliers), `Stock\ProductsController` (stock registration), `Stock\CustomersController` (customers/sales) |
| Inventory (hardware) | Hardware, ADMIN | `Tuli\TuliStoresController` (stores), `Tuli\inventoryManagentController` (categories, inventories, suppliers, expenses, products, customers, invoices, sales — all in one 693-line controller) |
| Reports | everyone except Driver | `Stock\CustomersController` (invoices, sales report), `Stock\ProductsController` (product edited/transfered), `Stock\InvoiceController` |
| Logistics — Driver view | Driver | `Stock\LogisticsController` (trips, truck reports) |
| Logistics — Admin view | ADMIN | `Stock\LogisticsController`, plus two hardcoded per-truck routes `truck-ejy` / `truck-erw` (plates T821EJY / T343ERW) |
| Security & Settings | everyone (profile), ADMIN (system users) | `PortalUsersController` |

Mbao and Hardware run near-duplicate CRUD flows against parallel model pairs
(`Product`/`ProductsTuli`, `Invoice`/`InvoicesTuli`, `Customer`/`CustomersTuli`,
`Category`/`CategoriesTuli`, `Store`/`StoresTuli`, `Expense`/`ExpensesTuli`).
De-duplicating these into a shared, model-agnostic service layer is `ammendments.md`
§3 — the largest and not-yet-started phase of the refactor plan.

## Dormant (do not treat as dead code, but not reachable from the sidebar)

These sections exist in the sidebar markup as HTML comments (`<!-- -->`), not
just role-gated — no live link reaches them:

- **HRMS** (`directorates-management`, `employees-management`, `section-management`, `branches-management`)
- **Hotel Management** (`home-hotel`, `hotel-management`, room/booking routes)
- **Vending Machine** — UI-only stub, never had real routes

The **TIRA/insurance module** (`app/TIRAClient/`, `config/tira.php`,
`config/payments.php`, `config/custom/jubilee.php`, `Policy`/`Quotation` models
and Livewire components) is a separate leftover product (PolicyPro) bundled
into this codebase. It isn't linked from the sidebar at all and isn't part of
the Sales Manager Portal's current product surface.

Confirmed zero-reference code has already been moved to `_legacy/` (not
deleted) rather than left in `app/`/`routes/` — see `ammendments.md` §1/§4 and
commit `c078692`.

## Database is authoritative over migrations

`app/Models/` has 51 Eloquent models, generated from the live database schema
via `reliese/laravel`, but `database/migrations/` only has Laravel's 4
framework-default migrations (users, password resets, failed jobs, personal
access tokens) — there is no migration history for the actual business tables.

**If you need to add or change a column: alter the live database
(`new_oppah_db`) directly, then regenerate models with `reliese/laravel`.**
Writing a new Laravel migration for an existing table will not match reality
and risks drifting from the schema the models already reflect.

## Refactor plan status

See `ammendments.md` for the full phased plan. As of this writing:

- ✅ §1 + §4 — dead code and unused route quarantine (`c078692`)
- ✅ §2 — config/secrets hygiene (`6ecfdf7`)
- ✅ §6 — this document
- ⬜ §3 — Stock↔Tuli controller de-duplication (not started, largest phase)
- ⬜ §5 — naming/typo cleanup is intentionally minimal-scope; see the plan for what's safe to touch
