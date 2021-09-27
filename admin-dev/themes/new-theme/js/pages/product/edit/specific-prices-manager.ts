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
    const {listFields} = SpecificPriceMap;
    const tbody = this.listContainer.querySelector(`${SpecificPriceMap.listContainer} tbody`) as HTMLElement;
    const trTemplate = this.listContainer.querySelector(SpecificPriceMap.listRowTemplate) as HTMLTemplateElement;
    tbody.innerHTML = '';

    getSpecificPrices(this.productId).then((response) => {
      const specificPrices = response.specificPrices as Array<SpecificPriceForListing>;

      specificPrices.forEach((specificPrice: SpecificPriceForListing) => {
        const trClone = trTemplate.content.cloneNode(true) as HTMLElement;
        const idField = this.selectListField(trClone, listFields.specificPriceId);
        const combinationField = this.selectListField(trClone, listFields.combination);
        const currencyField = this.selectListField(trClone, listFields.currency);
        const countryField = this.selectListField(trClone, listFields.country);
        const groupField = this.selectListField(trClone, listFields.group);
        const customerField = this.selectListField(trClone, listFields.customer);
        const priceField = this.selectListField(trClone, listFields.price);
        const impactField = this.selectListField(trClone, listFields.impact);
        const periodField = this.selectListField(trClone, listFields.period);
        const periodFromField = this.selectListField(trClone, listFields.from);
        const periodToField = this.selectListField(trClone, listFields.to);
        const fromQtyField = this.selectListField(trClone, listFields.fromQuantity);
        idField.textContent = String(specificPrice.id);
        combinationField.textContent = specificPrice.combination;
        currencyField.textContent = specificPrice.currency;
        countryField.textContent = specificPrice.country;
        groupField.textContent = specificPrice.group;
        customerField.textContent = specificPrice.customer;
        priceField.textContent = specificPrice.price;
        impactField.textContent = specificPrice.impact;
        fromQtyField.textContent = specificPrice.fromQuantity;

        if (!specificPrice.period) {
          periodField.textContent = String(periodField.dataset.unlimitedText);
        } else {
          periodFromField.textContent = specificPrice.period.from;
          periodToField.textContent = specificPrice.period.to;
        }

        tbody.append(trClone);
      });
    });
  }

  private selectListField(templateTrClone: HTMLElement, selector: string): HTMLElement {
    return templateTrClone.querySelector(selector) as HTMLElement;
  }
}
