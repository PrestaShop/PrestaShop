/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import OrderViewPageMap from './OrderViewPageMap';

const {$} = window;

export default class OrderShippingManager {
  constructor() {
    this.initOrderShippingUpdateEventHandler();
    this.overrideNewCarrierSelect2();
  }

  initOrderShippingUpdateEventHandler(): void {
    $(OrderViewPageMap.mainDiv).on('click', OrderViewPageMap.showOrderShippingUpdateModalBtn, (event) => {
      const $btn = $(event.currentTarget);

      $(OrderViewPageMap.updateOrderShippingTrackingNumberInput).val($btn.data('order-tracking-number'));
      $(OrderViewPageMap.updateOrderShippingCurrentOrderCarrierIdInput).val($btn.data('order-carrier-id'));
    });
  }

  overrideNewCarrierSelect2(): void {
    // Reinitialize Select2 to specify the dropdown container.
    // Required to avoid display issues inside the modal.
    const $select = $(OrderViewPageMap.updateOrderShippingNewCarrierIdSelect);
    const $modal = $select.closest('.modal');

    $select.select2('destroy').select2({
      dropdownParent: $modal,
    });
  }
}
