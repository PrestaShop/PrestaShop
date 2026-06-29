# Per-combination virtual flag & downloadable files — design

> **Supersedes** the `virtual_combinations` product-type design
> (`2026-06-23-virtual-product-combinations-files-design.md`). Pivot decided on
> upstream discussion [#41826](https://github.com/PrestaShop/PrestaShop/discussions/41826)
> following maintainer (Hlavtox) feedback: model virtuality per **combination**
> instead of introducing a new product type.

## Goal

Let an individual **combination** be virtual (non-shippable) or physical, and let a
downloadable file be attached to any combination — independently of the virtual flag.
This covers a physical edition + a digital edition of the same product, e-book formats,
a downloadable activation key shipped with a physical item, etc., without a new product type.

## Decisions (from brainstorming)

1. **No new product type.** Drop `virtual_combinations`. The per-combination flag applies to
   the existing `combinations` product type. Product-level `is_virtual` stays for products
   without combinations (`standard`/`virtual`).
2. **Per-line shipping semantics generalize the existing product-level behaviour.** A cart/order
   line is virtual when its chosen combination is virtual (else the product's flag). The cart is
   virtual only when *all* lines are virtual; shipping/weight is driven by the physical lines, and
   virtual lines are treated as non-shippable — exactly as a product-level virtual product already
   behaves in a mixed cart today. No order/shipment splitting.
3. **File decoupled from the virtual flag.** `is_virtual` controls shipping only. A downloadable
   file may be attached to any combination, physical or virtual. The download link appears at order
   time whenever a `product_download` row exists for the (product, combination).

## Data model

- **New:** `ps_product_attribute.is_virtual TINYINT(1) NOT NULL DEFAULT 0` — combination-level
  virtual flag.
- **Kept (from #41825):** `ps_product_download.id_product_attribute INT UNSIGNED NOT NULL DEFAULT 0`
  (0 = product level) and `UNIQUE KEY (id_product, id_product_attribute)`.
- **Reverted (from #41825):** the `virtual_combinations` value on the `product.product_type` enum.
- Fresh installs: `install-dev/data/db_structure.sql`. Existing shops: companion `autoupgrade`
  PR — `ALTER TABLE PREFIX_product_attribute ADD is_virtual` + the kept `product_download` changes;
  the enum change is dropped.

## Effective line virtuality

A single source of truth: **a line is virtual iff** it has a combination and that combination's
`is_virtual = 1`, otherwise the product's `is_virtual`.

- `Cart::getProducts()` (raw SQL, ~line 720) currently selects `p.is_virtual`. Join
  `product_attribute` on the line's `id_product_attribute` and expose the **effective** flag
  (combination flag when a combination is present, else product flag).
- `Cart::isVirtualCart()` and `Order::isVirtual()` compute "all lines virtual" using the effective
  per-line flag instead of `p.is_virtual`.
- Weight / carrier eligibility / shipping cost: a virtual line contributes nothing to shipping
  (treated like a product-level virtual product). Audit the weight and carrier-restriction
  computations to skip virtual lines even if the combination carries a non-zero weight in data.
- `isVirtualCart()` gates carrier selection, shipping display and order amounts
  (`PaymentModule`, `OrderAmountUpdater`, the cart/order presenters) — these keep working unchanged
  once the underlying flag is per-line.

## Downloadable file (decoupled, per combination)

Reuses the #41825 combination-scoped download work:

- Legacy `ProductDownload::getIdFromCombination()`; product-level lookups scoped to attribute 0.
- Doctrine `ProductDownload` entity field `idProductAttribute`.
- CQRS: `AddVirtualProductFileCommand` carries `combinationId`; repository `findByCombinationId` /
  `findAllByProductId`; updater `addFile($combinationId)` / `deleteAllFilesForProduct`.
- **Guard change:** the Task 6 guard that required product type `virtual_combinations` for
  `combinationId > 0` is relaxed — a combination file is allowed on any `combinations` product
  regardless of `is_virtual`.
- **Combination deletion cleanup (new, central):** `Combination::delete()` does not cascade to
  `product_download`. Add explicit removal of the combination's `product_download` row + the file on
  disk when a combination is deleted, and when a product leaves the `combinations` type
  (generalises the orphan-cleanup already added in #41825's `ProductTypeUpdater`).
- Order / FO / email download resolution per (product, combination) — kept from #41825 (Task 9),
  unchanged; the link shows whenever a download row exists for the line, independent of `is_virtual`.

## Domain / CQRS — combination is_virtual

- Add `isVirtual` to the Combination domain: the editable/query result (`CombinationForEditing`),
  `AddCombinationCommand` / `UpdateCombinationCommand` (+ handlers), and repository persistence to
  `ps_product_attribute.is_virtual`.
- Legacy `Combination` ObjectModel: add the `is_virtual` field to `$definition` + property.

## BO UX

- Combination edit modal: add an **`is_virtual` switch** (shipping control) and keep the
  **virtual-file section** (Task 7), now shown for any combination of a `combinations` product
  (no longer gated to a product type). The combination-modal JS (toggle + filename prefill) added
  in #41825 stays.
- **Revert** the `virtual_combinations` option in the type selector (`ProductTypeChoiceProvider`)
  and the corresponding `ProductTypeListener` branch.

## Migration / reuse of #41825

| Keep | Revert | New |
|---|---|---|
| `product_download.id_product_attribute` + UNIQUE | `ProductType::TYPE_VIRTUAL_COMBINATIONS` + AVAILABLE_TYPES | `ps_product_attribute.is_virtual` (DB + entity + domain + commands + form) |
| `getIdFromCombination` + product-level scoping | product_type enum value | Cart/Order effective per-line virtuality |
| Doctrine entity field | type selector option + ProductTypeListener branch | Combination `is_virtual` switch in modal |
| Combination-scoped virtual-file CQRS | `getDynamicProductType` combined branch | autoupgrade: `ALTER product_attribute ADD is_virtual` (drop enum ALTER) |
| Order/FO/email per-combination download | `ProductTypeUpdater` virtual_combinations handling; `addFile` type guard; VO type-group helpers (reassess) | Combination-deletion download cleanup |

**Open PRs:** rework the existing PR [#41825](https://github.com/PrestaShop/PrestaShop/pull/41825)
in **draft** on the same branch (keep reusable commits, revert the type commits, add the per-combination
`is_virtual` work), update its title/description. Update companions accordingly: autoupgrade
[#1855](https://github.com/PrestaShop/autoupgrade/pull/1855) (swap SQL) and ui-testing-library
[#1047](https://github.com/PrestaShop/ui-testing-library/pull/1047) (type-selection → per-combination
flag + file). Post an acknowledgement to Hlavtox on the discussion.

## Testing

- **Unit:** effective-line-virtuality helper; combination `is_virtual` command/handler; combination
  command builder emits the file command regardless of `is_virtual`.
- **Integration:** mixed cart (one virtual + one physical combination) → `isVirtualCart()` false,
  shipping applies to the physical line only; all-virtual cart → `isVirtualCart()` true; per-combination
  download distinctness; combination deletion removes its download row + file.
- **E2E:** create a `combinations` product, mark one combination virtual with a file and one physical,
  order each, assert shipping behaviour and the per-combination download. (Companion ui-testing-library
  page objects.)

## Out of scope / non-goals

- No order/shipment splitting by virtuality (a mixed cart ships as one order, virtual lines excluded
  from shipping — current behaviour).
- No change to product-level virtual products without combinations.
- BO combination-modal JS polish beyond the existing toggle/prefill.
