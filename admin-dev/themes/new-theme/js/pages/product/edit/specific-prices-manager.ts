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

import ProductMap from '@pages/product/product-map';
import initSpecificPriceModal from '@pages/product/components/specific-price';
import {EventEmitter} from 'events';

const SpecificPriceMap = ProductMap.specificPrice;

export default class SpecificPricesManager {
  eventEmitter: EventEmitter;

  specificPriceModalApp: null;

  specificPriceListApp: null;

  listContainer: HTMLElement

  constructor(productId: number) {
    this.listContainer = document.querySelector(SpecificPriceMap.listContainer) as HTMLElement;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.specificPriceModalApp = initSpecificPriceModal(
      productId,
      SpecificPriceMap.formModal,
      this.eventEmitter,
    );
    this.renderList(productId);
  }

  renderList(productId: number): void {
    const tbody = this.listContainer.querySelector(`${SpecificPriceMap.listContainer} tbody`) as HTMLElement;
    const trTemplate = this.listContainer.querySelector(SpecificPriceMap.listRowTemplate) as HTMLTemplateElement;
    tbody.innerHTML = '';

    this.getSpecificPrices().forEach((specificPrice) => {
      const trClone = trTemplate.content.cloneNode(true) as HTMLElement;

      //@todo; could loop through all td and put content based on css class. (class = object key?)
      const idField = trClone.querySelector('.specific-price-id') as HTMLElement;
      const combinationField = trClone.querySelector('.combination') as HTMLElement;
      const currencyField = trClone.querySelector('.currency') as HTMLElement;
      idField.textContent = specificPrice.id;
      combinationField.textContent = specificPrice.combination;
      currencyField.textContent = specificPrice.currency;
      tbody.append(trClone);
    });
  }

  /**
   * @todo: temporary method. Use specificPriceService (to retrieve the data by ajax) instead when ready
   */
  private getSpecificPrices(): Array<any> {
    return [
      {
        id: 1,
        combination: 'All',
        currency: 'All',
      },
      {
        id: 2,
        combination: 'All',
        currency: 'EUR',
      },
      {
        id: 3,
        combination: 'All',
        currency: 'USD',
      },
    ];
  }
}
