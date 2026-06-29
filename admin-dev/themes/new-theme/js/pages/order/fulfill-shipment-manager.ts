/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import Router from '@js/components/router';
import OrderViewPageMap from './OrderViewPageMap';

export default class FulfillShipmentManager {
  private formRoute = 'admin_orders_shipment_get_fulfill_form';

  private shipmentId: number|null = null;

  private orderId: number|null = null;

  private router = new Router();

  constructor() {
    this.initFulfillShipmentEventHandler();
  }

  initFulfillShipmentEventHandler(): void {
    const mainDiv = document.querySelector(OrderViewPageMap.mainDiv);

    if (!mainDiv) {
      throw new Error(
        `Initialization failed: main container not found for selector "${
          OrderViewPageMap.mainDiv
        }". The shipment fulfill feature cannot be initialized.`,
      );
    }
    mainDiv.addEventListener('click', this.onFulfillShipmentClick);
  }

  onFulfillShipmentClick = (event: Event): void => {
    const link = (event.target as HTMLElement).closest<HTMLAnchorElement>(OrderViewPageMap.showFulfillShipmentModalBtn);

    if (!link) {
      return;
    }

    const {orderId, shipmentId} = link.dataset;

    if (!orderId || !shipmentId) {
      throw new Error('error while getting orderId or shipmentId');
    }

    this.orderId = Number(orderId);
    this.shipmentId = Number(shipmentId);

    this.refreshFulfillShipmentForm();
  };

  async refreshFulfillShipmentForm(): Promise<void> {
    const modal = document.querySelector<HTMLElement>(OrderViewPageMap.fulfillShipmentModal);

    if (!modal) {
      throw new Error('Fulfill shipment modal not found.');
    }

    modal.dataset.state = 'loading';

    try {
      const response = await fetch(this.router.generate(this.formRoute, {
        orderId: this.orderId,
        shipmentId: this.shipmentId,
      }), {
        method: 'GET',
      });

      if (!response.ok) {
        throw new Error(await response.text());
      }
      const formContainer = document.querySelector<HTMLElement>(OrderViewPageMap.fulfillShipmentModalContainer);
      formContainer!.innerHTML = await response.text();

      modal.dataset.state = 'loaded';

      window.prestaShopUiKit.init();
    } catch (error) {
      modal.dataset.state = 'loaded';
      console.error('Error while loading fulfill shipment form:', error);
    }
  }
}
