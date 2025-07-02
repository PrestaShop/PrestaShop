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
import Router from '@components/router';
import OrderViewPageMap from './OrderViewPageMap';

export default class SplitShipmentManager {
  private refreshCarriersRoute = 'admin_orders_shipment_refresh_carriers';

  private router = new Router();

  constructor() {
    this.initSplitShipmentEventHandler();
    this.initCarrierRefreshOnProductSelection();
  }

  initSplitShipmentEventHandler(): void {
    const mainDiv = document.querySelector(OrderViewPageMap.mainDiv);

    if (!mainDiv) {
      return;
    }

    mainDiv.addEventListener('click', (event) => {
      const target = event.target as HTMLElement;

      if (target && target.matches(OrderViewPageMap.showSplitShipmentModalBtn)) {
        const shipmentId = target.getAttribute('data-shipment-id');
        const input = document.querySelector<HTMLInputElement>(OrderViewPageMap.splitShipmentTrackingShipmentId);

        if (input && shipmentId) {
          input.value = shipmentId;
        }
      }
    });
  }

  initCarrierRefreshOnProductSelection(): void {
    const modal = document.getElementById('splitShipmentModal');

    if (!modal) {
      return;
    }

    const carrierSelect = modal.querySelector<HTMLSelectElement>('select[name$="[carrier]"]');

    if (!carrierSelect) {
      return;
    }

    modal.addEventListener('change', async (event) => {
      const target = event.target as HTMLInputElement;

      if (target && target.matches('input[type="checkbox"][name$="[selected]"]')) {
        const checkboxes = modal.querySelectorAll<HTMLInputElement>('input[type="checkbox"][name$="[selected]"]:checked');
        const selectedProductIds: number[] = [];

        checkboxes.forEach((checkbox) => {
          const id = checkbox.dataset.productId;

          if (id) {
            selectedProductIds.push(Number(id));
          }
        });

        try {
          const response = await fetch(this.router.generate(this.refreshCarriersRoute), {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({selectedProducts: selectedProductIds}),
          });

          if (!response.ok) {
            throw new Error(await response.text());
          }

          const data = await response.json();
          const carriersMap = data.carriers as Record<string, string>;

          carrierSelect.innerHTML = ''; // Clear options

          for (const [label, value] of Object.entries(carriersMap)) {
            const option = new Option(label, value);
            carrierSelect.appendChild(option);
          }
        } catch (error) {
          console.error('Error while loading carriers:', error);
        }
      }
    });
  }
}
