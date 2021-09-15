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
import {getSpecificPrices} from '@pages/product/services/specific-price-service';

const SpecificPriceMap = ProductMap.specificPrice;

export default class SpecificPricesManager {
  eventEmitter: EventEmitter;

  productId: number;

  specificPriceModalApp: null;

  listContainer: HTMLElement

  constructor(
    productId: number,
  ) {
    this.productId = productId;
    this.listContainer = document.querySelector(SpecificPriceMap.listContainer) as HTMLElement;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.specificPriceModalApp = initSpecificPriceModal(
      productId,
      SpecificPriceMap.formModal,
      this.eventEmitter,
    );
    this.renderList();
  }

  renderList(): void {
    const tbody = this.listContainer.querySelector(`${SpecificPriceMap.listContainer} tbody`) as HTMLElement;
    const trTemplate = this.listContainer.querySelector(SpecificPriceMap.listRowTemplate) as HTMLTemplateElement;
    tbody.innerHTML = '';

    getSpecificPrices(this.productId).then((response) => {
      const specificPrices = response.specificPrices as Array<SpecificPriceForListing>;

      specificPrices.forEach((specificPrice: SpecificPriceForListing) => {
        const trClone = trTemplate.content.cloneNode(true) as HTMLElement;
        const idField = trClone.querySelector('.specific-price-id') as HTMLElement;
        const combinationField = trClone.querySelector('.combination') as HTMLElement;
        const currencyField = trClone.querySelector('.currency') as HTMLElement;
        const groupField = trClone.querySelector('.group') as HTMLElement;
        const customerField = trClone.querySelector('.customer') as HTMLElement;
        const priceField = trClone.querySelector('.price') as HTMLElement;
        const impactField = trClone.querySelector('.impact') as HTMLElement;
        const periodField = trClone.querySelector('.period') as HTMLElement;
        const fromQtyField = trClone.querySelector('.from-qty') as HTMLElement;
        idField.textContent = String(specificPrice.id);
        combinationField.textContent = specificPrice.combination;
        currencyField.textContent = specificPrice.currency;
        groupField.textContent = specificPrice.group;
        customerField.textContent = specificPrice.customer;
        priceField.textContent = specificPrice.price;
        impactField.textContent = specificPrice.impact;
        periodField.textContent = specificPrice.period;
        fromQtyField.textContent = specificPrice.fromQuantity;

        tbody.append(trClone);
      });
    });
  }
}
