# Codebase Flaw Scan

Date scanned: 2026-04-14

Scope: review of first-party SilverStripe/PHP code, templates, and root project files. This report assumes the platform is used only by logged-in company users and that all relevant CMS pages are access-controlled.

This is not a full security audit, but these are the remaining issues that looked worth investigating first.

## High

### 1. Runtime secrets and data archives are present in the project root

The root contains `.env`, `champion-before-dev-tasks.sql`, and `champion.rar`. `.gitignore` says `.env`, `vendor/`, `themes/simple/`, and generated GraphQL files should be ignored, but these files/directories are present in this working tree.

Evidence:

- `.env` exists and contains keys for database credentials, mailer DSN, bulk mail credentials, default admin username/password, and environment type.
- `.gitignore:1-8` includes `/.env`, `/vendor/`, `/themes/simple/`, `/.graphql-generated/`, and `/public/_graphql/`.
- `champion-before-dev-tasks.sql` exists at the root and is about 244 KB.
- `champion.rar` exists at the root and is about 36 MB.

Impact:

- If this directory is copied, deployed, archived, or committed incorrectly, credentials and database contents could be exposed. The archive may also contain duplicate secrets or data not obvious from the file listing.

Suggested fix:

- Treat the `.env` values as sensitive and avoid distributing this working tree as-is.
- Keep only `.env.example` with placeholders in source/distributable copies.
- Inspect `champion.rar` before sharing or deploying, and keep database dumps outside the project root unless they are explicitly needed.

## Medium

### 2. Product link generation appears to use `$this->owner` inside a `DataObject`

`Product::Link()` references `$this->owner->URLSegment`, but `Product` itself is the `DataObject`; `$owner` is normally used inside extensions, not directly inside the owning object.

Evidence:

- `ShopModule/Model/Product/Product.php:203-211` returns `Controller::join_links($page->Link(), $this->owner->URLSegment, $action)`.
- `Product` directly extends `DataObject` at `ShopModule/Model/Product/Product.php:21`; its `URLSegment` field is added by `App\Extension\Sluggable` at `app/Extension/Sluggable.php:8-21`.

Impact:

- Product links may fail with an undefined property/null access or generate incorrect URLs in cart/order templates and product listings.

Suggested fix:

- Use `$this->URLSegment` in `Product::Link()`.

### 3. Spend limit logic blocks checkout instead of routing to approval

The checkout logic rejects orders over a user's spend limit immediately, even though the UI copy suggests orders can be routed for approval based on spend limits.

Evidence:

- `app/src/Pages/Checkout/CheckoutPageController.php:178-191` redirects with "This order exceeds your spend limit..." when `cartTotal > spendLimit`.
- Approval routing is based only on `$member->RequiresApproval` at `app/src/Pages/Checkout/CheckoutPageController.php:194-197`.
- Public copy in `themes/simple/templates/Includes/FooterInfoBox.ss` mentions approval routing based on "role, spend limits or internal company policies."

Impact:

- Users over a spend limit cannot submit an order for approval. If the intended workflow is approval escalation, this is a business-rule bug rather than an access/security issue.

Suggested fix:

- Clarify the desired rule. If overspend should require approval, set `$requiresApproval` when `cartTotal > spendLimit` rather than rejecting.

## Low

### 4. Dev data tasks include a shared hard-coded password

The seed task creates users with a shared password and prints it. This is acceptable for local development, but it should stay clearly isolated from real company accounts/data.

Evidence:

- `ShopModuleTask/Tasks/PopulateShopModuleDevDataTask.php:18` defines `qwerty123`.
- `ShopModuleTask/Tasks/PopulateShopModuleDevDataTask.php:55` prints the password.
- `ShopModuleTask/_config/config.yml` does not show an environment-only guard.

Impact:

- If the task is accidentally run on shared/staging/production data, predictable credentials may be created.

Suggested fix:

- Guard the task to dev/test environments only, or require an explicit CLI-only confirmation.

### 5. First-party tests are absent

I found dependency test configs under `vendor/`, but no first-party test suite in the application/module folders.

Evidence:

- `find` did not locate first-party `tests/`, `test/`, `*Test.php`, or root `phpunit.xml*` files outside `vendor/`.

Impact:

- The order, approval, cart, and user-management flows have non-trivial business rules but no obvious regression coverage.

Suggested fix:

- Add focused controller/model tests for tenant boundaries, checkout approval rules, cart mutation rules, order visibility, and user-management updates.

## Notes

- The previously listed `ManageUsersPageController::updateUser()` name/email issue has been fixed and is no longer included as an open finding.
- The order visibility logic in `app/src/Pages/Dashboard/OrderHistoryPageController.php:57-70` looks intentionally scoped: CMS admins can view any order, regular users can only view their own order.
- Pending-order approval queries in `app/src/Pages/Dashboard/PendingOrdersPage.php:110-134` scope customer account admins to their own `CustomerAccountID`.
- `MemberExtension::validate()` at `ShopModule/Extension/MemberExtension.php:202-207` checks that a selected role belongs to the member's customer account, which helps protect role assignment.
