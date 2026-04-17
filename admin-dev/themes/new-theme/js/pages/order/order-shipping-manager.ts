/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
      $(OrderViewPageMap.updateOrderShippingNewCarrierIdSelect)
        .val($btn.data('carrier-id'))
        .trigger('change');
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
