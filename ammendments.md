# Amendments Plan — `salesmanage-claude`

Refactor plan for the Sales Manager Portal. Scope of "current system" is defined
by `resources/views/layouts/admin/partials/sidebar.blade.php` — every route
linked there (not commented out) is treated as **live and must keep working
exactly as-is**: same route names, same URLs, same Blade view paths, same
role gating. Everything else (dormant sidebar blocks, `admin_web.php` template
demo routes, insurance/TIRA code) is legacy and is quarantined, not deleted,
unless separately agreed.

No behavior changes in this plan — only internal restructuring, dead-code
removal, and config/secrets hygiene. Each phase is a separate PR so it can be
reviewed and rolled back independently.

## 0. Ground truth: live modules (from the sidebar)

| Sidebar section | Roles allowed | Backend |
|---|---|---|
| Summary (dashboards) | `home`: Mbao/ADMIN · `home-truck`: ADMIN/Driver · `home-hardcore`: Hardware/ADMIN | `PortalUsersController`, static Blade views |
| Inventory (mbao) | Mbao, ADMIN | `Stock\{Stores,Categories,Products,Customers}Controller` |
| Inventory (hardware) | Hardware, ADMIN | `Tuli\{TuliStores,inventoryManagentController}` |
| Reports | everyone except Driver | `Stock\CustomersController`, `Stock\ProductsController` |
| Logistics (Driver view) | Driver | `Stock\LogisticsController` |
| Logistics (Admin view, full) | ADMIN | `Stock\LogisticsController` (+ 2 hardcoded truck routes: `truck-ejy`, `truck-erw`) |
| Security & Settings | everyone (Profile) / ADMIN (System Users) | `PortalUsersController` |

Roles in active use: `ADMIN`, `Mbao`, `Hardware`, `Driver`.

Dormant in the sidebar (HTML-commented, not just role-gated) — **do not touch
their routes/controllers in this plan, just leave them alone**:
- HRMS (`directorates-management`, `employees-management`, etc.)
- Hotel Management (`home-hotel`, `hotel-management`, room/booking routes)
- Vending Machine (never had real routes, UI-only stub)

## 1. Quarantine dead code (no risk — zero references)

Verified unreferenced anywhere in `routes/` or `app/`:

- `app/Http/Controllers/Tuli/StoresController.php` — superseded by
  `Tuli/TuliStoresController.php`, which is the one actually wired to
  `stores-management-tuli` / `add-stores-tuli`.
- `app/Http/Controllers/Tuli/TuliCategoriesController.php` — categories-tuli
  routes actually go through `inventoryManagentController`.
- `app/Models/bkp/*` (11 files) — stale duplicates of the live Tuli models
  one directory up (`app/Models/*Tuli.php`). Sitting inside `app/Models`
  means they're autoloaded/PSR-4 resolvable, a landmine for anyone who
  `use`s the wrong namespace by mistake.

**Action:** move these to `_legacy/` at the repo root (outside `app/`, so
Composer stops autoloading them) instead of deleting outright, in case
there's logic worth diffing later. Confirm with a `composer dump-autoload`
+ full route list smoke test that nothing 404s.

## 2. Config & secrets hygiene

- `config/payments.php` and `config/tira.php` hardcode live-looking
  credentials (TIRA `clientKey`, EVM payment hash seed, callback URLs).
  These belong to the dormant insurance module, but hardcoded secrets in a
  tracked file are a standing risk regardless of whether the module is
  used. Move to `.env` (`TIRA_CLIENT_KEY`, `EVMAK_HASH_SEED`, etc.) and
  reference via `env()`, with `.env.example` updated with blank placeholders.
- `.env` currently has `APP_NAME=Laravel` and `APP_URL=http://localhost`
  while `config/app.php` defaults to `'PolicyPro'` — cosmetic but worth
  fixing to the real product name so PDFs/emails/error pages don't leak the
  old product identity.
- `config/custom/jubilee.php` — same treatment if it contains credentials
  (needs a quick read before the PR).

## 3. De-duplicate Stock ↔ Tuli controllers (the big one)

The two business lines (Mbao/timber vs Hardware/Tuli) run parallel,
near-identical CRUD flows (stores, categories, inventories, suppliers,
expenses, products, customers, invoices, sales report) against parallel
model pairs (`Product`/`ProductsTuli`, `Invoice`/`InvoicesTuli`,
`Customer`/`CustomersTuli`, `Category`/`CategoriesTuli`, `Store`/`StoresTuli`,
`Expense`/`ExpensesTuli`).

Current state is inconsistent, not just duplicated:
- Mbao side splits responsibility across 4 controllers: `ProductsController`
  (582 lines), `CategoriesController` (136), `CustomersController` (285),
  `InvoiceController` (625).
- Hardware side crams the equivalent of all four into one 693-line
  `inventoryManagentController` (categories, inventories, suppliers,
  expenses, products, customers, invoices, sales report — everything).

**Plan (route-name preserving, so the sidebar/`web.php` don't change):**
1. Extract shared logic (validation rules, pricing/qty calculations, invoice
   PDF/CSV generation, expense recording) into model-agnostic
   `app/Services/Inventory/*` classes parameterized by model class
   (e.g. `InventoryService::for(Product::class)` vs
   `InventoryService::for(ProductsTuli::class)`), following the pattern
   already started in `app/Services/WhatsAppService.php`.
2. Split `Tuli\inventoryManagentController` into
   `Tuli\{Categories,Inventories,Products,Customers,Invoice}Controller` that
   mirror the `Stock\*` naming, each thin — delegating to the new services.
   Route method names stay identical (`get`, `register`,
   `registerInvetories`, etc.) so `routes/web.php` needs zero changes,
   only the `use` import at the top swaps to the new class.
3. Do this one resource at a time (categories first — smallest, 136 vs a
   slice of 693 lines) so each step is independently testable against the
   sidebar's Hardware Inventory menu before moving to the next.
4. Explicitly out of scope for this plan: merging the Tuli/Stock **models**
   or **tables** into one schema. That's a data migration, not a code
   refactor, and deserves its own separate plan/approval given it touches
   the live database (`new_oppah_db`).

## 4. Route file cleanup

- `routes/admin_web.php` (285 lines) is 100% unused template demo pages
  (ui-kits, chart-widget, ecommerce templates, blog, kanban, etc.) from the
  original admin theme — none of it is linked from the sidebar or any
  active view. Move it to `_legacy/routes/admin_web.php` and remove the
  `@include_once('admin_web.php')` from `routes/web.php`. Confirms nothing
  else `route()`-references these names first (quick grep across
  `resources/views/admin` for the demo route names before removing).
- `routes/web.phpbkp` — stray backup file committed to the repo; move to
  `_legacy/` alongside the others rather than leave it loose at the routes
  root.
- Fix the two truck-report routes that are literally per-vehicle
  (`truck-ejy`, `truck-erw` for plates T821EJY/T343ERW) — leave the routes
  as-is (renaming breaks the sidebar links) but note in a code comment that
  adding a third truck currently means adding a third hardcoded route; a
  follow-up (not in this plan) would parameterize
  `truck/reports/{plate}`.

## 5. Naming / typo cleanup (internal only)

These typos exist in **method and route names that the sidebar depends on**
(`Invetories`, `inventoryManagentController`, `adminidtration`,
`sttaus`) — **do not rename these**, since it would break
`route('...')` calls across every Blade view. Confine cleanup to things with
no external surface:
- Internal variable/comment typos.
- The stray duplicate `Route::get('customers/management', ...)` /
  `Route::post('customers/management', ...)` pair in `web.php` sharing one
  name with different HTTP verbs — verify this is intentional
  (GET = list, POST = search) and add a one-line comment so it's not
  "fixed" accidentally later.

## 6. Documentation

- Add a short `MODULES.md` (or expand `README.md`, currently 3 lines)
  documenting the Mbao vs Hardware split, the role list (`ADMIN`, `Mbao`,
  `Hardware`, `Driver`), and which sidebar sections are live vs dormant —
  so this doesn't have to be re-derived from scratch again.
- Note in the same doc that the DB schema is authoritative over migrations
  (`reliese/laravel` generated 62 models against a live DB with only 4
  actual Laravel migrations) — anyone adding a column needs to alter the
  live DB directly and regenerate, not write a migration.

## Suggested order / PR sequence

1. Quarantine dead code (§1) + route file cleanup (§4) — pure deletion risk,
   fastest to review.
2. Config/secrets hygiene (§2).
3. Documentation (§6) — cheap, unblocks nothing else, do anytime.
4. Stock↔Tuli de-duplication (§3) — largest, split into one PR per
   resource (categories → suppliers/expenses → products → customers →
   invoices) rather than one big PR.

## Explicitly not in scope

- No changes to dormant modules (HRMS, Hotel, Vending, TIRA/insurance
  client) beyond leaving them quarantined in `_legacy/` if touched
  incidentally.
- No database schema changes.
- No route URL, route name, or role-gating changes — the sidebar's current
  behavior is the acceptance test for every phase above.
