# Per-combination virtual flag & downloadable files — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers-extended-cc:subagent-driven-development (recommended) or superpowers-extended-cc:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the `virtual_combinations` product type with a per-combination `is_virtual` flag and downloadable files decoupled from that flag, so each combination of a `combinations` product can be physical or virtual and optionally carry its own file.

**Architecture:** Reuse the combination-scoped `product_download` work already on this branch (column, legacy/ORM model, CQRS, order/FO/email resolution). Remove the new product type. Add `is_virtual` to `ps_product_attribute` and the Combination domain. Make cart/order virtuality per-line (a line is virtual when its combination is virtual, else the product's flag), generalising today's product-level mixed-cart behaviour — no order/shipment splitting.

**Tech Stack:** PHP 8, Symfony forms, CQRS (PrestaShop command bus), Doctrine ORM + legacy ObjectModel, MySQL, Playwright/Mocha UI tests.

**Spec:** `docs/superpowers/specs/2026-06-29-per-combination-virtual-products-design.md`

**Conventions:**
- Worktree: `/Users/jonathan/Documents/GitHub/PrestaShop/core-virtcombi-wt`, branch `feat-virtual-product-combinations-files` (now a draft PR #41825, being reworked in place).
- Unit tests: `php -d memory_limit=-1 ./vendor/bin/phpunit -c tests/Unit/phpunit.xml <path>`.
- One commit per task, conventional-commit messages, trailer `Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>`.
- Legacy `ProductDownload` is aliased `VirtualProductFile` in Core.

---

## Task 0: Remove the `virtual_combinations` product type

**Goal:** Drop the new product type and all its references; keep the combination-scoped `product_download` infrastructure intact.

**Files:**
- Modify: `src/Core/Domain/Product/ValueObject/ProductType.php` (remove `TYPE_VIRTUAL_COMBINATIONS`; drop it from `AVAILABLE_TYPES` and from the `isVirtualType()`/`hasCombinations()` arrays — the helpers stay, now single-member)
- Modify: `install-dev/data/db_structure.sql` (revert `product.product_type` enum to `'standard','pack','virtual','combinations',''`)
- Modify: `src/Core/Form/ChoiceProvider/ProductTypeChoiceProvider.php` (remove the vc choice + its `getChoicesAttributes` entry)
- Modify: `src/PrestaShopBundle/Form/Admin/Sell/Product/EventListener/ProductTypeListener.php` (remove the product-level-virtual-file removal that was gated on "not vc"; restore original virtual/combinations handling via the now-single-member helpers)
- Modify: `classes/Product.php` (`getDynamicProductType()` — remove the combined `is_virtual && hasCombinations` branch; revert the `add()/update()` guard to the original `if ($this->is_virtual)`)
- Modify: `src/Adapter/Product/Update/ProductTypeUpdater.php` (remove vc-specific branches; KEEP `deleteAllFilesForProduct` on leaving combinations — still needed)
- Modify: `src/PrestaShopBundle/Form/Admin/Sell/Product/Stock/StockType.php` (remove the vc label case)
- Modify/Delete tests: `tests/Unit/Core/Domain/Product/ValueObject/ProductTypeTest.php` (drop vc assertions), `tests/Unit/Classes/ProductDynamicTypeTest.php` (remove the vc case)

**Acceptance Criteria:**
- [ ] `grep -rn "TYPE_VIRTUAL_COMBINATIONS\|virtual_combinations" src/ classes/ install-dev/ tests/Unit/` returns nothing.
- [ ] `ProductType::AVAILABLE_TYPES` no longer contains the value; `isVirtualType()` = {virtual}, `hasCombinations()` = {combinations}.
- [ ] Existing product-type unit tests pass.

**Verify:** `php -d memory_limit=-1 ./vendor/bin/phpunit -c tests/Unit/phpunit.xml tests/Unit/Core/Domain/Product/ValueObject tests/Unit/Adapter/Product/Update tests/Unit/Classes` → green; `grep -rn virtual_combinations src/ classes/ install-dev/` → empty.

**Steps:**
- [ ] **Step 1:** In `ProductType.php`, delete the `TYPE_VIRTUAL_COMBINATIONS` const, remove it from `AVAILABLE_TYPES`, and remove the `self::TYPE_VIRTUAL_COMBINATIONS` element from both `isVirtualType()` and `hasCombinations()` arrays (leaving `[self::TYPE_VIRTUAL]` and `[self::TYPE_COMBINATIONS]`).
- [ ] **Step 2:** Update `ProductTypeTest.php` — remove the data-provider rows asserting vc; keep the rest. Run → green.
- [ ] **Step 3:** Revert the enum in `db_structure.sql` (drop `'virtual_combinations'`).
- [ ] **Step 4:** Remove the vc choice from `ProductTypeChoiceProvider.php` (the `getChoices` map entry + the `getChoicesAttributes` block).
- [ ] **Step 5:** In `ProductTypeListener.php`, restore the original section handling. The combination/virtual branches already call `ProductType::hasCombinations()/isVirtualType()` (now single-member, so equivalent to the original `=== TYPE_COMBINATIONS` / `=== TYPE_VIRTUAL`). Remove any vc-specific product-level-file logic; the product-level virtual-file section is removed for everything except `TYPE_VIRTUAL` (original behaviour).
- [ ] **Step 6:** In `Product.php`, remove the combined branch in `getDynamicProductType()`; revert `add()`/`update()` to `if ($this->is_virtual) { $this->product_type = ProductType::TYPE_VIRTUAL; }`. Update/remove `ProductDynamicTypeTest.php` accordingly. Run → green.
- [ ] **Step 7:** In `ProductTypeUpdater.php`, simplify: `$leavingCombinations = ProductType::hasCombinations($product->product_type) && !ProductType::hasCombinations($productType->getValue());` then delete combinations + `deleteAllFilesForProduct` when leaving combinations; `$leavingVirtual` triggers `deleteAllFilesForProduct` when leaving virtual. (Keeps the file-cleanup; drops vc specifics.)
- [ ] **Step 8:** Remove the vc case in `StockType.php` label switch.
- [ ] **Step 9:** Run the verify suite + grep. Commit: `refactor(product): drop virtual_combinations type in favor of per-combination flag`.

---

## Task 1: DB — `ps_product_attribute.is_virtual`

**Goal:** Add the combination-level virtual flag to the schema (fresh installs).

**Files:**
- Modify: `install-dev/data/db_structure.sql` (table `product_attribute`)

**Acceptance Criteria:**
- [ ] `product_attribute` has `is_virtual tinyint(1) unsigned NOT NULL DEFAULT '0'`.

**Verify:** `grep -n "is_virtual" install-dev/data/db_structure.sql` shows it under `product_attribute`.

**Steps:**
- [ ] **Step 1:** In the `CREATE TABLE \`PREFIX_product_attribute\`` block, add `\`is_virtual\` tinyint(1) unsigned NOT NULL DEFAULT '0',` after a suitable column (e.g. after `default_on`). Match the file's formatting.
- [ ] **Step 2:** Commit: `feat(db): add is_virtual to product_attribute`.

---

## Task 2: Legacy `Combination` ObjectModel + Doctrine entity — `is_virtual`

**Goal:** Expose `is_virtual` on the combination models.

**Files:**
- Modify: `classes/Combination.php` (property `$is_virtual = false;` + `$definition['fields']['is_virtual'] => ['type' => self::TYPE_BOOL, 'validate' => 'isBool']`)
- Modify: the Doctrine entity for product attribute/combination if one maps these columns (`grep -rln "id_product_attribute" src/PrestaShopBundle/Entity` → find the combination/attribute entity; add `idProductAttribute`-style mapping for `is_virtual` if the entity exists; if no entity maps product_attribute, note it and skip)
- Test: `tests/Unit/Classes/CombinationDefinitionTest.php` (definition + property)

**Acceptance Criteria:**
- [ ] `Combination::$definition['fields']` contains `is_virtual`; `(new Combination())->is_virtual === false`.

**Verify:** `php -d memory_limit=-1 ./vendor/bin/phpunit -c tests/Unit/phpunit.xml tests/Unit/Classes/CombinationDefinitionTest.php`

**Steps:**
- [ ] **Step 1:** Write the failing test (mirror `tests/Unit/Classes/ProductDownloadDefinitionTest.php`): assert `array_key_exists('is_virtual', Combination::$definition['fields'])` and the default. Run → FAIL.
- [ ] **Step 2:** Add the property + definition field in `classes/Combination.php`. Run → PASS.
- [ ] **Step 3:** If a Doctrine entity maps `product_attribute`, add the `is_virtual` field mapping (boolean, default false) with getter/setter, matching sibling style; validate with `php ./bin/console doctrine:schema:validate --skip-sync`.
- [ ] **Step 4:** Commit: `feat(combination): add is_virtual field to legacy model and entity`.

---

## Task 3: Combination domain/CQRS — carry `is_virtual`

**Goal:** Persist and read `is_virtual` through the combination commands and query.

**Files:**
- Modify: `src/Core/Domain/Product/Combination/Command/UpdateCombinationCommand.php` (add nullable `isVirtual` setter/getter)
- Modify: the combination create command if combinations are created with attributes (`grep -rln "class.*CombinationCommand" src/Core/Domain/Product/Combination/Command`)
- Modify: `src/Core/Domain/Product/Combination/QueryResult/CombinationForEditing.php` (add `bool $isVirtual` — append last, default false; getter)
- Modify: the command handler/updater that writes combinations (`grep -rln "UpdateCombinationHandler\|CombinationUpdater" src/Adapter/Product/Combination`) to persist `is_virtual`
- Modify: `src/Adapter/Product/Combination/QueryHandler/GetCombinationForEditingHandler.php` (populate `isVirtual` from the combination)
- Test: `tests/Unit/.../Combination/UpdateCombinationCommandTest.php` (or extend existing)

**Acceptance Criteria:**
- [ ] `UpdateCombinationCommand` exposes `setIsVirtual(bool)/getIsVirtual(): ?bool`.
- [ ] `CombinationForEditing::isVirtual(): bool`.
- [ ] The updater writes `$combination->is_virtual`.

**Verify:** `php -d memory_limit=-1 ./vendor/bin/phpunit -c tests/Unit/phpunit.xml tests/Unit/Core/Domain/Product/Combination` (+ the new test).

**Steps:**
- [ ] **Step 1:** Read the existing combination command/updater/query handler to learn the property-update pattern (how `reference`, `eanCode` etc. flow). Mirror it for `is_virtual`.
- [ ] **Step 2:** Add `isVirtual` to `UpdateCombinationCommand` (setter/getter, nullable so partial updates skip it). Write/extend a unit test asserting the getter; red→green.
- [ ] **Step 3:** Add `is_virtual` to `CombinationForEditing` (last constructor arg default false + getter) and populate it in `GetCombinationForEditingHandler`.
- [ ] **Step 4:** In the combination updater, when `getIsVirtual()` is not null, set `$combination->is_virtual` and include it in the updated fields.
- [ ] **Step 5:** Commit: `feat(combination): carry is_virtual through CQRS`.

---

## Task 4: Effective per-line virtuality in Cart & Order

**Goal:** A cart/order line is virtual when its combination is virtual (else the product flag); `isVirtualCart()`/`Order::isVirtual()` and shipping use the effective flag.

**Files:**
- Modify: `classes/Cart.php` — `getProducts()` raw SQL (~line 720; join `product_attribute pa ON pa.id_product_attribute = cp.id_product_attribute` and select effective `IF(cp.id_product_attribute > 0, pa.is_virtual, p.is_virtual) AS is_virtual`); `hasRealProducts()` (the gate behind `isVirtualCart()` — ensure it counts a line as "real" only when the effective flag is 0); weight/carrier computations that sum line weight must treat an effective-virtual line as non-shippable.
- Modify: `classes/order/Order.php` — `getProducts()` SQL so `is_virtual` per line is effective (join `product_attribute` on `od.product_attribute_id`); `isVirtual()` then works unchanged.
- Test: `tests/Integration/.../CartVirtualLineTest.php` (mixed vs all-virtual cart)

**Acceptance Criteria:**
- [ ] A line with a virtual combination reports `is_virtual = 1` from `Cart::getProducts()`/`Order::getProducts()`; a physical combination reports the combination's flag, not the product's.
- [ ] `Cart::isVirtualCart()` is true only when every line is effectively virtual; a mixed cart is not virtual and keeps shipping.
- [ ] Weight/carrier eligibility excludes effective-virtual lines.

**Verify:** `php -d memory_limit=-1 ./vendor/bin/phpunit -c tests/Integration/phpunit.xml tests/Integration/.../CartVirtualLineTest.php` (requires test DB; otherwise rely on CI and the unit-level guards).

**Steps:**
- [ ] **Step 1:** Read `Cart::getProducts()`, `hasRealProducts()`, and the weight/carrier code paths that read `is_virtual` (grep `is_virtual` in `classes/Cart.php`). Identify each site that must use the effective per-line value.
- [ ] **Step 2:** Update `getProducts()` SQL: add `LEFT JOIN \`PREFIX_product_attribute\` pa ON pa.\`id_product_attribute\` = cp.\`id_product_attribute\`` and replace the `p.is_virtual` projection with `IF(cp.\`id_product_attribute\` > 0, pa.\`is_virtual\`, p.\`is_virtual\`) AS \`is_virtual\``.
- [ ] **Step 3:** Update `hasRealProducts()` similarly (it has its own query/logic) so a line counts as real only when the effective flag is 0.
- [ ] **Step 4:** Audit weight/carrier: ensure summed shipping weight and carrier restrictions skip lines whose effective `is_virtual` is 1 (mirror how product-level virtual products are already excluded).
- [ ] **Step 5:** Apply the same effective-`is_virtual` projection to `Order::getProducts()`.
- [ ] **Step 6:** Write the integration test (create a product with 2 combinations, one `is_virtual=1` with a file and one physical; build a cart with both → `isVirtualCart()` false, shipping applies; a cart with only the virtual one → `isVirtualCart()` true). Run red→green (CI if no local DB).
- [ ] **Step 7:** Commit: `feat(cart): resolve virtuality per line from the combination`.

---

## Task 5: BO form — `is_virtual` switch + decoupled file section

**Goal:** Combination modal offers an `is_virtual` switch and a downloadable-file section for any combination of a `combinations` product.

**Files:**
- Modify: `src/PrestaShopBundle/Form/Admin/Sell/Product/Combination/CombinationFormType.php` (add an `is_virtual` `SwitchType`; the `virtual_product_file` sub-form is no longer gated on the product type — show it for every combination)
- Modify: `src/Core/Form/IdentifiableObject/DataProvider/CombinationFormDataProvider.php` (map `is_virtual`; keep `virtual_product_file` mapping)
- Modify: `src/Core/Form/IdentifiableObject/CommandBuilder/Product/Combination/CombinationVirtualProductFileCommandsBuilder.php` (already builds the file command — no type gate; unchanged or simplified) and the combination command builder that maps scalar fields, to emit `is_virtual` into `UpdateCombinationCommand`
- Modify: `src/Core/Form/IdentifiableObject/OptionProvider/CombinationFormOptionsProvider.php` (drop the `product_type` gating option added for vc; keep `virtual_product_file_id`)
- Modify: `src/Adapter/Product/VirtualProduct/Update/VirtualProductUpdater.php` (`addFile()` — relax the guard that required product type `virtual_combinations` for `combinationId > 0`; now accept any combination of a `combinations`-type product regardless of `is_virtual`)
- Test: command-builder unit test asserting `is_virtual` reaches the command, and the file command builds regardless of `is_virtual`

**Acceptance Criteria:**
- [ ] The combination form has an `is_virtual` switch and a `virtual_product_file` section, both present for any combination of a `combinations` product.
- [ ] Saving sets the combination's `is_virtual` and the file (independently).

**Verify:** command-builder unit test green; manual + E2E (Task 8).

**Steps:**
- [ ] **Step 1:** Add the `is_virtual` `SwitchType` to `CombinationFormType`; remove the `product_type === virtual_combinations` gate around `virtual_product_file` so it renders for all combinations.
- [ ] **Step 2:** Map `is_virtual` in the data provider; drop the vc-driven `product_type` option in the options provider.
- [ ] **Step 3:** Extend the combination command builder to set `is_virtual` on `UpdateCombinationCommand`; confirm the file command builder no longer depends on the product type. Red→green unit test.
- [ ] **Step 4:** Commit: `feat(product-ui): per-combination is_virtual switch and decoupled file section`.

---

## Task 6: Combination deletion — remove its downloadable file

**Goal:** Deleting a combination removes its `product_download` row and the file on disk.

**Files:**
- Modify: `src/Adapter/Product/Combination/Update/CombinationDeleter.php` (before/after deleting a combination, delete its virtual file via `VirtualProductUpdater`/repository by `(productId, combinationId)`) — or the legacy `Combination::delete()` if that is the single choke point (`grep -rn "deleteAllProductCombinations\|function delete" src/Adapter/Product/Combination classes/Combination.php`)
- Test: `tests/Integration/.../CombinationDeleteRemovesFileTest.php`

**Acceptance Criteria:**
- [ ] After deleting a combination that had a file, no `product_download` row remains for `(id_product, id_product_attribute)` and the file is removed from disk.
- [ ] Deleting a combination without a file is a no-op (no error).

**Verify:** integration test (CI if no local DB).

**Steps:**
- [ ] **Step 1:** Identify the single deletion choke point used by both single and bulk/all deletion. Add file cleanup there using `findByCombinationId` (catch not-found → skip) + uploader remove + repository delete.
- [ ] **Step 2:** Integration test red→green.
- [ ] **Step 3:** Commit: `feat(combination): delete per-combination downloadable file on combination removal`.

---

## Task 7: autoupgrade companion — swap SQL

**Goal:** Update the autoupgrade companion to add `product_attribute.is_virtual` and keep the `product_download` changes; drop the enum change.

**Files (in the `autoupgrade` clone, branch `feat-virtual-combinations-9.2.0` off `7.6.x`):**
- Modify: `upgrade/sql/9.2.0.sql`

**Acceptance Criteria:**
- [ ] The appended block adds `product_attribute.is_virtual`, keeps the `product_download` column + unique-key change, and no longer modifies the `product_type` enum.

**Verify:** `git diff` shows only the corrected block; PR #1855 updated.

**Steps:**
- [ ] **Step 1:** Replace the `ALTER ... product_type ... MODIFY ENUM(...)` line with `ALTER TABLE \`PREFIX_product_attribute\` ADD COLUMN \`is_virtual\` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;`. Keep the two `product_download` ALTERs.
- [ ] **Step 2:** Commit + force-push the branch; PR #1855 (base `7.6.x`) updates. Comment the change.

---

## Task 8: ui-testing-library companion + E2E rework

**Goal:** Update page objects and the E2E scenario for the per-combination flag.

**Files (ui-testing-library worktree `uitl-wt-virtcombi`, branch off `main`):**
- Modify: combination tab page object — replace any `virtual_combinations` type selection with: create a `combinations` product, then in a combination set `is_virtual` and upload a file (new method `setCombinationIsVirtual` + reuse `setCombinationVirtualProductFile`)
- Modify: remove `virtualCombinationsProductDescription` (type gone)
- Modify (core worktree): `tests/UI/campaigns/functional/BO/03_catalog/01_products/<NN>_*/...` E2E scenario → create combinations product, mark one combination virtual+file and one physical, order each, assert shipping + per-combination download

**Acceptance Criteria:**
- [ ] E2E scenario exercises a mixed virtual/physical combinations product end-to-end.
- [ ] Page objects type-check + lint clean (`tsc --noEmit`, eslint).

**Verify:** `tsc --noEmit` + eslint in the uitl worktree; E2E runs in CI.

**Steps:**
- [ ] **Step 1:** Update the uitl page objects; build/lint (borrow node_modules from the main clone). Push to `prestaedit`; PR #1047 updates.
- [ ] **Step 2:** Rewrite the core E2E scenario; lint per tests/UI. Commit on the core branch.

---

## Task 9: Rework PR #41825 + reply to Hlavtox

**Goal:** Present the reworked branch as the per-combination design and close the loop upstream.

**Steps:**
- [ ] **Step 1:** Verify the branch contains the kept + new work and none of the reverted type code (`grep -rn virtual_combinations` over `src/ classes/ install-dev/` empty).
- [ ] **Step 2:** Update PR #41825 title/description to the per-combination design (REST PATCH per the gh-pr-edit bug note); keep it draft until CI is green, then mark ready.
- [ ] **Step 3:** Post the approved acknowledgement reply to Hlavtox on discussion #41826.
- [ ] **Step 4:** Final full-suite verification + holistic review before marking ready.

---

## Final: verification before marking PR ready

- [ ] `php -d memory_limit=-1 ./vendor/bin/phpunit -c tests/Unit/phpunit.xml tests/Unit/Core/Domain/Product tests/Unit/Adapter/Product tests/Unit/Classes tests/Unit/Core/Form/IdentifiableObject` → green.
- [ ] `grep -rn "virtual_combinations" src/ classes/ install-dev/ admin-dev/ tests/` → empty.
- [ ] ESLint (new-theme) + PHP CS Fixer clean.
- [ ] CI green on #41825; companions (#1855, #1047) updated and linked.
