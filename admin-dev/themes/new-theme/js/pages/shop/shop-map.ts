/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default {
  rootCategorySelect: 'select[name="shop[store][root_category_id]"]',
  categoryTree: '#shop_store_category_ids',
  categoryCheckbox: (categoryId: string): string => `#shop_store_category_ids input[value="${categoryId}"]`,
  useImportDataInput: 'input[name="shop[import][use_import_data]"]',
  sourceShopSelect: 'select[name="shop[import][source_shop_id]"]',
  importedDataInput: 'input[name="shop[import][imported_data][]"]',
  formGroup: '.form-group',
};
