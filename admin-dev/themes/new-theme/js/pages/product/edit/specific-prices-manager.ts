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
import ProductEventMap from '@pages/product/product-event-map';
import {EventEmitter} from 'events';
import SpecificPriceList from '@pages/product/components/specific-price/specific-price-list';
import Vue from 'vue';
import VueI18n from 'vue-i18n';
import ReplaceFormatter from '@vue/plugins/vue-i18n/replace-formatter';
import SpecificPriceModal from '@pages/product/components/specific-price/SpecificPriceModal.vue';

Vue.use(VueI18n);
const SpecificPriceMap = ProductMap.specificPrice;

export default class SpecificPricesManager {
  eventEmitter: EventEmitter;

  productId: number;

  specificPriceList: SpecificPriceList;

  specificPriceModalApp: null;

  constructor(
    productId: number,
  ) {
    this.productId = productId;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.specificPriceList = new SpecificPriceList(productId);
    this.initSpecificPriceModal(
      productId,
      SpecificPriceMap.formModal,
      this.eventEmitter,
    );
    this.specificPriceList.renderList();
    this.initListeners();
  }

  private initListeners(): void {
    this.eventEmitter.on(ProductEventMap.specificPrice.specificPriceCreated, () => this.specificPriceList.renderList());
    this.eventEmitter.on(ProductEventMap.specificPrice.specificPriceUpdated, () => this.specificPriceList.renderList());
  }

  private initSpecificPriceModal(
    productId: number,
    specificPriceModalSelector: string,
    eventEmitter: EventEmitter,
  ): Vue|null {
    const container = document.querySelector(specificPriceModalSelector);

    if (!(container instanceof HTMLElement)) {
      console.error('Invalid container provided for specificPrice modal');

      return null;
    }

    const translations = JSON.parse(<string>container.dataset.translations);
    const i18n = new VueI18n({
      locale: 'en',
      formatter: new ReplaceFormatter(),
      messages: {en: translations},
    });

    return new Vue({
      el: specificPriceModalSelector,
      template:
        '<specific-price-modal :productId=productId :eventEmitter=eventEmitter />',
      components: {SpecificPriceModal},
      i18n,
      data: {
        eventEmitter,
        productId,
      },
    });
  }
}
