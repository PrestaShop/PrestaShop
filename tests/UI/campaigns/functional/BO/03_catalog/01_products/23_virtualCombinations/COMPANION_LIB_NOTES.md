# Companion library requirements — `virtual_combinations` E2E test

The E2E test `01_CRUDVirtualCombinations.ts` is authored against the intended
page-object API of the external package `@prestashop-core/ui-testing`
(repo: https://github.com/PrestaShop/ui-testing-library, branch `#main`).

The page objects do **not** live in the PrestaShop core repo, so the methods and
selectors below must be added in a **companion PR** to the ui-testing-library
before this test can run in CI. Until then, the test compiles against the
intended signatures but will fail at runtime (missing methods / selectors).

Each new call site in the test is annotated with a `// COMPANION-LIB:` comment.

---

## 1. Product type selection — NO new method required

`boProductsPage.selectProductType(page, type)` is already a generic
"select by type string" helper. Passing the new string `'virtual_combinations'`
works as long as the option is rendered in the "New product" modal — which the
core selector work (Task 8) already adds.

The `FakerProduct` type union must, however, accept the new value:

- **File:** `src/data/faker/product.ts` (or wherever `ProductType` / the
  `FakerProduct` `type` field union is declared in the library).
- **Change:** add `'virtual_combinations'` to the allowed product-type string
  literal union used by `FakerProduct({ type })`.
- Without this, TypeScript will reject `type: 'virtual_combinations'` in the test
  data object.

## 2. `boProductsPage.virtualCombinationsProductDescription` — new getter (constant)

- **Page object:** `pages/BO/catalog/products/index.ts`
  (class behind `boProductsPage`).
- **Proposed member:**
  ```ts
  public readonly virtualCombinationsProductDescription: string;
  ```
  initialized in the constructor with the description copy shown in the
  "New product" modal for the new type.
- **Why:** to assert the modal description like the existing
  `virtualProductDescription` / `productWithCombinationsDescription`.
- **Test fallback used meanwhile:** the test currently only asserts the
  description is non-empty; tighten to
  `expect(...).to.contains(boProductsPage.virtualCombinationsProductDescription)`
  once the constant exists.

## 3. `boProductsCreateTabCombinationsPage.setCombinationVirtualProductFile` — new method

- **Page object:** `pages/BO/catalog/products/tabCombinations.ts`
  (class behind `boProductsCreateTabCombinationsPage`).
- **Proposed signature:**
  ```ts
  /**
   * Inside the (already open) combination edit modal, upload a virtual product
   * file in the "Virtual product file" section that only shows for the
   * `virtual_combinations` product type, then save the modal.
   * @param page {Page}
   * @param filePath {string} path/name of the file to upload
   * @returns {Promise<string>} the modal save success message
   */
  async setCombinationVirtualProductFile(page: Page, filePath: string): Promise<string>;
  ```
- **Selectors needed (combination edit modal):**
  - file input, e.g. `#combination_edit_virtual_product_file_file` (hidden input
    `type=file`) — match the actual Symfony form field name produced by Task 7.
  - the modal "Save"/submit button (reuse the existing modal-save selector if the
    page object already has one).
- **Why:** the per-combination virtual file upload is the core new interaction;
  no existing method covers it.

## 4. `boProductsCreateTabCombinationsPage.getCombinationVirtualProductFileName` — new getter

- **Page object:** `pages/BO/catalog/products/tabCombinations.ts`.
- **Proposed signature:**
  ```ts
  /**
   * Read the name of the virtual file currently attached to the combination,
   * from the (already open) combination edit modal.
   * @param page {Page}
   * @returns {Promise<string>}
   */
  async getCombinationVirtualProductFileName(page: Page): Promise<string>;
  ```
- **Selector needed:** the element rendering the uploaded file name / display name
  in the modal's "Virtual product file" section.
- **Why:** to assert per-combination distinctness in the BO (combination 1 keeps
  file 1, combination 2 keeps file 2).

## 5. `foHummingbirdProductPage.getProductDownloadFileName` — new getter

- **Page object:** `pages/FO/hummingbird/product/index.ts`
  (class behind `foHummingbirdProductPage`); ideally also mirror on the classic
  theme page object `foClassicProductPage` for parity.
- **Proposed signature:**
  ```ts
  /**
   * Return the file name of the downloadable file currently offered on the FO
   * product page for the selected combination.
   * @param page {Page}
   * @returns {Promise<string>}
   */
  async getProductDownloadFileName(page: Page): Promise<string>;
  ```
- **Selector needed:** the FO download link/label that reflects the selected
  combination's virtual file (Task 9 wires the FO/order/email resolution).
- **Why:** to assert the FO surfaces a combination-specific download and that
  switching the selected combination switches the offered file.

---

## Already-available API reused as-is (no change needed)

- `boLoginPage.goTo` / `successLogin`
- `boDashboardPage.goToSubMenu` / `getPageTitle`
- `boProductsPage.closeSfToolBar` / `clickOnNewProductButton` /
  `selectProductType` / `getProductDescription` / `clickOnAddNewProduct` /
  `successfulDeleteMessage`
- `boProductsCreatePage.setProduct` / `saveProduct` / `previewProduct` /
  `deleteProduct` / `getPageTitle` / `successfulUpdateMessage`
- `boProductsCreateTabCombinationsPage.clickOnAttributesAndFeaturesLink` /
  `setProductAttributes` / `generateCombinations` /
  `generateCombinationsMessage` / `successfulGenerateCombinationsMessage` /
  `generateCombinationModalIsClosed` / `clickOnEditIcon` /
  `closeEditCombinationModal` / `successfulUpdateMessage`
- `boAttributesPage.getPageTitle` / `closePage`
- `foHummingbirdProductPage.changeLanguage` / `getPageTitle` /
  `selectAttributes` / `closePage`
- `utilsFile.generateImage` / `createFile` / `deleteFile`
- `utilsPlaywright.createBrowserContext` / `newTab` / `closeBrowserContext`

## Run status

This test is authored for CI + the companion ui-testing-library PR. It is NOT
claimed to pass locally: it requires (a) the companion library changes above and
(b) a running shop. It will run once both are in place.
