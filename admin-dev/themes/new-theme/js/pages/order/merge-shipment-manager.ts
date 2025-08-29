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

    function toggleSubmit() {
      const atLeastOneChecked = Array.from(checkboxes).some((cb) => cb instanceof HTMLInputElement && cb.checked);

      const shipmentSelected = shipmentSelect.value !== '';
      submitBtn.disabled = !(atLeastOneChecked && shipmentSelected);
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
}
