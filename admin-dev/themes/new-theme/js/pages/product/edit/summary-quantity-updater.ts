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
