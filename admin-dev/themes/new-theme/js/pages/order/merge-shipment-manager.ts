/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import Router from '@js/components/router';
import OrderViewPageMap from './OrderViewPageMap';

export default class MergeShipmentManager {
  private formRoute = 'admin_orders_shipment_get_merge_form';

  private shipmentId: number|null = null;

  private orderId: number|null = null;

  private router = new Router();

  constructor() {
    this.initMergeShipmentEventHandler();
  }

  initMergeShipmentEventHandler(): void {
    const mainDiv = document.querySelector(OrderViewPageMap.mainDiv);

    if (!mainDiv) {
      throw new Error(
        `Initialization failed: main container not found for selector "${
          OrderViewPageMap.mainDiv
        }". The shipment merge feature cannot be initialized.`,
      );
    }
    mainDiv.addEventListener('click', this.onMergeShipmentClick);
  }

  initSubmitMergeShipmentStateHandler(): void {
    const submitBtnEl = document.querySelector(OrderViewPageMap.submitMergeShipment);
    const shipmentSelectEl = document.querySelector(OrderViewPageMap.selectMergeShipment);
    const checkboxes = document.querySelectorAll('.form-check-input');

    if (!(submitBtnEl instanceof HTMLButtonElement)
      || !(shipmentSelectEl instanceof HTMLSelectElement)
      || checkboxes.length === 0) {
      return;
    }

    const submitBtn = submitBtnEl;
    const shipmentSelect = shipmentSelectEl;
    const {isValid} = this.form.dataset;

    function toggleSubmit() {
      const atLeastOneChecked = Array.from(checkboxes).some((cb) => cb instanceof HTMLInputElement && cb.checked);

      const shipmentSelected = shipmentSelect.value !== '';
      submitBtn.disabled = !(atLeastOneChecked && shipmentSelected && !!isValid);
    }

    checkboxes.forEach((cb) => cb.addEventListener('change', toggleSubmit));
    shipmentSelect.addEventListener('change', toggleSubmit);
    toggleSubmit();
  }

  onMergeShipmentClick = (event: Event): void => {
    const target = event.target as HTMLElement;

    if (target && target.matches(OrderViewPageMap.showMergeShipmentModalBtn)) {
      if (!target.dataset.orderId) {
        throw new Error('impossible to retrieve order id');
      }
      this.orderId = Number(target.dataset.orderId);

      if (!target.dataset.shipmentId) {
        throw new Error('impossible to retrieve shipment id');
      }
      this.shipmentId = Number(target.dataset.shipmentId);

      this.refreshMergeShipmentForm();
    }
  };

  async refreshMergeShipmentForm(): Promise<void> {
    const modal = document.querySelector(OrderViewPageMap.mergeShipmentModal) as HTMLElement;

    if (!modal) {
      throw new Error('Merge shipment modal not found.');
    }

    modal.dataset.state = 'loading';

    try {
      const response = await fetch(this.router.generate(this.formRoute, {
        orderId: this.orderId,
        shipmentId: this.shipmentId,
      }), {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      });

      if (!response.ok) {
        throw new Error(await response.text());
      }
      const formContainer = document.querySelector(OrderViewPageMap.mergeShipmentModalContainer) as HTMLElement;
      formContainer!.innerHTML = await response.text();

      modal.dataset.state = 'loaded';

      window.prestaShopUiKit.init();
      this.initSubmitMergeShipmentStateHandler();
    } catch (error) {
      console.error('Error while loading merge shipment form:', error);
    }
  }

  private get form(): HTMLFormElement {
    const form = document.forms.namedItem(OrderViewPageMap.mergeShipmentFormName) as HTMLFormElement;

    if (!form) {
      throw new Error('Merge shipment form not found');
    }
    return form;
  }
}
