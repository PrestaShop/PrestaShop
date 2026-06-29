# Companion library notes — `23_virtualCombinations`

This campaign proves the per-combination `is_virtual` flag and its decoupled
downloadable file. It depends on page-object methods that live in the external
`@prestashop-core/ui-testing` library, added in the companion PR:

- **ui-testing-library companion PR: PrestaShop/ui-testing-library#1047**

The test is therefore **CI-gated**: it can only run once the companion lib PR is
merged (or pinned) and the lib is installed in `tests/UI/node_modules`. It cannot
be run from this core worktree alone.

## Library methods used

Back office (`boProductsCreateTabCombinationsPage`):

- `setProductAttributes(page, attributes)` — declare attributes, returns the
  generate-combinations button label.
- `generateCombinations(page)` — generate the combinations.
- `generateCombinationModalIsClosed(page)` — assert the generation modal closed.
- `clickOnEditIcon(page, row)` — open a combination's edit modal.
- `setCombinationIsVirtual(page, isVirtual: boolean): Promise<string>` — toggle
  the per-combination "is virtual" switch and save; returns the success message.
- `setCombinationVirtualProductFile(page, filePath: string): Promise<string>` —
  upload a downloadable file to the open combination and save; returns the
  success message.
- `getCombinationVirtualProductFileName(page): Promise<string>` — read the
  attached file name back from the open modal.
- `closeEditCombinationModal(page): Promise<boolean>` — close the modal.

Generic / shared:

- `boProductsPage.selectProductType(page, 'combinations')`.
- `foHummingbirdProductPage.selectDefaultAttributes(page, ProductAttribute[])` —
  select a combination in FO.
- `foHummingbirdProductPage.getProductDownloadFileName(page): Promise<string>` —
  read the per-combination download file name in FO.

## Known TODO / risk

- **FO download selector (`getProductDownloadFileName`)** — the selector for the
  per-combination download link in the rendered Hummingbird theme is still a
  **TODO** in the companion lib. The assertion in
  `01_perCombinationVirtual.ts` (virtual combination returns the file name, the
  physical one returns an empty string) must be **confirmed against the rendered
  theme** once the lib method's selector is finalized. If the lib returns the
  empty string differently (e.g. throws or returns `null`) for a combination
  with no download, the physical-combination assertion may need adjusting.

## Honesty note

This test has **not** been executed. `tests/UI/node_modules` is absent in this
worktree, so neither the lint nor the runtime path could be exercised here; the
TypeScript validity was checked by inspection against the sibling campaign
`03_CRUDProductWithCombinations.ts`. Do not treat it as passing until it runs
green in CI with the companion lib PR in place.
