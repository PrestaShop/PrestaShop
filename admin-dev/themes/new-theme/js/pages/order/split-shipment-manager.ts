/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import Router from '@components/router';
import OrderViewPageMap from './OrderViewPageMap';

type ProductData = { selected: number; selected_quantity: number; order_detail_id: number };
type ProductsMap = Record<number, ProductData>;

export default class SplitShipmentManager {
  private readonly refreshFormRoute = 'admin_orders_shipment_get_split_form';

  private orderId?: number;

  private shipmentId?: number;

  private router = new Router();

  private abortController?: AbortController;

  private debounceTimer?: number;

  constructor() {
    this.attachEventListeners();
  }

  private attachEventListeners(): void {
    const container = document.querySelector(OrderViewPageMap.mainDiv);

    if (!container) {
      throw new Error('Main container not found, split shipment manager can not be initiated.');
    }
    container.addEventListener('click', this.handleSplitButtonClick);
  }

  private handleSplitButtonClick = async (event: Event): Promise<void> => {
    const target = event.target as HTMLElement;

    if (!target.matches(OrderViewPageMap.showSplitShipmentModalBtn)) {
      return;
    }

    const container = document.querySelector(OrderViewPageMap.splitShipmentFormContainer);

    if (!container) {
      throw new Error('Form container not found');
    }

    container.innerHTML = '';

    const {orderId} = target.dataset;

    if (!orderId) {
      throw new Error('Order ID missing');
    }

    const {shipmentId} = target.dataset;

    if (!shipmentId) {
      throw new Error('Shipment ID missing');
    }

    this.orderId = Number(orderId);
    this.shipmentId = Number(shipmentId);

    await this.refreshSplitShipmentForm();
  };

  private abortOngoingFetch(): void {
    if (this.abortController) {
      this.abortController.abort();
      this.abortController = undefined;
    }
  }

  private async fetchSplitFormHtml(
    products: ProductsMap = {},
    carrier: number = 0,
  ): Promise<string> {
    this.abortOngoingFetch();
    this.abortController = new AbortController();

    const url = this.router.generate(this.refreshFormRoute, {
      orderId: this.orderId,
      shipmentId: this.shipmentId,
      products,
      carrier,
    });

    const response = await fetch(url, {
      signal: this.abortController.signal,
      headers: {'Content-Type': 'application/json'},
    });

    if (!response.ok) {
      const text = await response.text();
      throw new Error(text);
    }

    return response.text();
  }

  private async refreshSplitShipmentForm(
    products: ProductsMap = {},
    carrier: number = 0,
  ): Promise<void> {
    try {
      this.modal.dataset.state = 'loading';
      const html = await this.fetchSplitFormHtml(products, carrier);
      const container = document.querySelector(OrderViewPageMap.splitShipmentFormContainer);

      if (!container) {
        throw new Error('Form container not found');
      }

      container.innerHTML = html;
      this.modal.dataset.state = 'loaded';
      this.initializeFormBehaviour();
    } catch (error: unknown) {
      if (!(error instanceof Error && error.name === 'AbortError')) {
        throw new Error('Failed to refresh split shipment form');
      }
    }
  }

  private get modal(): HTMLDivElement {
    const modal = document.querySelector(OrderViewPageMap.splitShipmentModal) as HTMLDivElement;

    if (!modal) {
      throw new Error('Split shipment modal not found');
    }
    return modal;
  }

  private get form(): HTMLFormElement {
    const form = document.forms.namedItem(OrderViewPageMap.splitShipmentFormName) as HTMLFormElement;

    if (!form) {
      throw new Error('Split shipment form not found');
    }
    return form;
  }

  private get submitButton(): HTMLButtonElement {
    const btn = document.querySelector<HTMLButtonElement>(
      OrderViewPageMap.splitShipmentFormSubmitButton,
    );

    if (!btn) {
      throw new Error('Submit button not found');
    }
    return btn;
  }

  private initializeFormBehaviour(): void {
    window.prestaShopUiKit.init();

    this.form.removeEventListener('change', this.handleFormChange);
    this.form.addEventListener('change', this.handleFormChange);

    const carrierSelect = this.form.querySelector(
      OrderViewPageMap.splitShipmentCarrierSelector,
    ) as HTMLSelectElement;
    const formIsValid = this.form.dataset.isValid;

    this.toggleSubmitButton(!!carrierSelect?.value && !!formIsValid);
  }

  private handleFormChange = (): void => {
    const {products, carrier} = this.extractFormData();

    clearTimeout(this.debounceTimer);
    this.debounceTimer = window.setTimeout(async () => {
      await this.refreshSplitShipmentForm(products, carrier);
      this.debounceTimer = undefined;
    }, 500);
  };

  private extractFormData(): { products: ProductsMap; carrier: number } {
    const formData = new FormData(this.form);
    const products: ProductsMap = {};
    let carrier = 0;

    formData.forEach((value, key) => {
      if (key === 'split_shipment[carrier]') {
        carrier = Number(value);
        return;
      }

      const match = key.match(
        /split_shipment\[products\]\[(\d+)\]\[([^\]]+)\]/,
      );

      if (!match || match[1] === null || match[2] === null) {
        return;
      }

      const id = Number(match[1]);
      const prop = match[2] as 'selected' | 'selected_quantity' | 'order_detail_id';
      const number = Number(value);

      products[id] = {
        selected: products[id]?.selected ?? 0,
        selected_quantity: products[id]?.selected_quantity ?? 0,
        order_detail_id: products[id]?.order_detail_id ?? 0,
        ...{
          [prop]: number,
        },
      };
    });

    return {products, carrier};
  }

  private toggleSubmitButton(isEnabled: boolean): void {
    this.submitButton.disabled = !isEnabled;
  }
}
