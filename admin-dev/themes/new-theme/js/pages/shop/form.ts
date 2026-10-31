/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import initColorPickers from '@app/utils/colorpicker';
import initShopTree from '@components/shop-tree';
import ShopMap from '@pages/shop/shop-map';

// Product related data only makes sense in a shop sharing the root category of the source shop
const ROOT_CATEGORY_DEPENDENT_DATA = ['product', 'product_attribute', 'image', 'stock_available'];

const selectRootCategoryTree = (): void => {
  const rootCategoryCheckbox = $(ShopMap.categoryCheckbox(String($(ShopMap.rootCategorySelect).val())));

  $(`${ShopMap.categoryTree} input[type="checkbox"]`).prop('checked', false);
  rootCategoryCheckbox.closest('li').find('input[type="checkbox"]').prop('checked', true);
};

const toggleImportedData = (): void => {
  const useImportData = $(`${ShopMap.useImportDataInput}:checked`).val() === '1';

  $(`${ShopMap.sourceShopSelect}, ${ShopMap.importedDataInput}`).closest(ShopMap.formGroup).toggleClass('d-none', !useImportData);
};

const toggleRootCategoryDependentData = (): void => {
  const sourceShopSelect = $(ShopMap.sourceShopSelect);
  const sourceRootCategoryId = sourceShopSelect.data('root-category-ids')[String(sourceShopSelect.val())];
  const sameRootCategory = Number(sourceRootCategoryId) === Number($(ShopMap.rootCategorySelect).val());

  ROOT_CATEGORY_DEPENDENT_DATA.forEach((data: string) => {
    $(`${ShopMap.importedDataInput}[value="${data}"]`).prop('disabled', !sameRootCategory).prop('checked', sameRootCategory);
  });
};

$(() => {
  initColorPickers();
  initShopTree();
  new window.prestashop.component.ChoiceTree(ShopMap.categoryTree).enableAutoCheckChildren();

  $(ShopMap.rootCategorySelect).on('change', selectRootCategoryTree);

  if ($(ShopMap.sourceShopSelect).length) {
    toggleImportedData();
    toggleRootCategoryDependentData();
    $(ShopMap.useImportDataInput).on('change', toggleImportedData);
    $(`${ShopMap.rootCategorySelect}, ${ShopMap.sourceShopSelect}`).on('change', toggleRootCategoryDependentData);
  }
});
