/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {getProductQuantity, QuantityResult} from '@pages/product/service/product';
import ProductMap from '@pages/product/product-map';
import {EventEmitter} from '@components/event-emitter';
import ProductEventMap from '@pages/product/product-event-map';

export default class SummaryQuantityUpdater {
  eventEmitter: typeof EventEmitter;

  productId: number;

  shopId: number;

  containerSelector: string;

  constructor(eventEmitter: typeof EventEmitter, productId: number, shopId: number) {
    this.eventEmitter = eventEmitter;
    this.productId = productId;
    this.shopId = shopId;
    this.containerSelector = ProductMap.summaryTotalQuantityContainer;

    this.eventEmitter.on(ProductEventMap.combinations.refreshPage, () => this.refreshQuantity());
  }

  async refreshQuantity(): Promise<void> {
    const response = await getProductQuantity(this.productId, this.shopId);
    const quantityResult = <QuantityResult> await response.json();
    const totalQuantityElement = this.getTotalQuantityElement();
    const quantity = Number(quantityResult.quantity);

    totalQuantityElement.textContent = String(quantity);

    this.refreshAppearance(quantity);
  }

  /**
   * Refreshes the wording in related label and the color of the quantity wrapper.
   *
   * @param {number} quantity
   *
   * @private
   */
  private refreshAppearance(quantity: number) {
    const container = <HTMLElement> document.querySelector(this.containerSelector);
    const lowStockThreshold = Number(container.dataset.lowStockThreshold);

    // one of these classes will be used to show correct color depending on stock level
    const alertClassNames: string[] = ['success', 'warning', 'danger'];
    let alertClass: string = 'success';
    let label: string = <string> container.dataset.inStockLabel;

    // determine class and label depending on quantity
    if (quantity <= 0) {
      alertClass = 'danger';
      label = <string> container.dataset.outOfStockLabel;
    } else if (lowStockThreshold > 0 && quantity <= lowStockThreshold) {
      alertClass = 'warning';
      label = <string> container.dataset.lowStockStockLabel;
    }

    const totalQuantityLabel = <HTMLElement> container.querySelector(ProductMap.summaryTotalQuantityLabel);
    const totalQuantityElement = this.getTotalQuantityElement();
    totalQuantityLabel.textContent = label;

    // replace alert class if needed
    alertClassNames.forEach((className: string) => {
      if (className === alertClass) {
        totalQuantityElement.classList.add(className);
      } else {
        totalQuantityElement.classList.remove(className);
      }
    });
  }

  private getTotalQuantityElement(): HTMLElement {
    return <HTMLElement> document.querySelector(ProductMap.summaryTotalQuantity);
  }
}
