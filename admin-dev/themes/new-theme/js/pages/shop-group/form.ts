/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import initColorPickers from '@app/utils/colorpicker';
import initShopTree from '@components/shop-tree';
import ShopGroupMap from '@pages/shop-group/shop-group-map';

const isSwitchOn = (inputSelector: string): boolean => $(`${inputSelector}:checked`).val() === '1';

const toggleShareOrder = (): void => {
  const canShareOrders = isSwitchOn(ShopGroupMap.shareCustomerInput) && isSwitchOn(ShopGroupMap.shareStockInput);

  $(ShopGroupMap.shareOrderInput).prop('disabled', !canShareOrders);

  if (!canShareOrders) {
    $(`${ShopGroupMap.shareOrderInput}[value="0"]`).prop('checked', true);
  }
};

$(() => {
  initColorPickers();
  initShopTree();

  const sharingInputs = $(`${ShopGroupMap.shareCustomerInput}, ${ShopGroupMap.shareStockInput}`);

  if (!sharingInputs.prop('disabled')) {
    toggleShareOrder();
    sharingInputs.on('change', toggleShareOrder);
  }
});
