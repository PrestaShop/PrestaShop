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

import {FormIframeModal} from '@components/modal';
import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import {EventEmitter} from 'events';
import SpecificPriceList from '@pages/product/components/specific-price/specific-price-list';
import Vue from 'vue';
import VueI18n from 'vue-i18n';
import Router from '@components/router';
import FormFieldDisabler from '@components/form/form-field-disabler';

Vue.use(VueI18n);
const SpecificPriceMap = ProductMap.specificPrice;
const PriorityMap = SpecificPriceMap.priority;

export default class SpecificPricesManager {
  eventEmitter: EventEmitter;

  productId: number;

  specificPriceList: SpecificPriceList;

  router: Router;

  constructor(
    productId: number,
  ) {
    this.router = new Router();
    this.productId = productId;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.specificPriceList = new SpecificPriceList(productId);
    this.initComponents();
    this.specificPriceList.renderList();
    this.initListeners();
  }

  private initListeners(): void {
    this.eventEmitter.on(ProductEventMap.specificPrice.specificPriceUpdated, () => this.specificPriceList.renderList());
  }

  private initComponents() {
    this.initSpecificPriceModal();
    new FormFieldDisabler({
      disablingInputSelector: PriorityMap.priorityTypeCheckboxesSelector,
      matchingValue: '0',
      targetSelector: PriorityMap.priorityListWrapper,
    });
  }

  private initSpecificPriceModal() {
    const addButton = document.querySelector(SpecificPriceMap.addSpecificPriceBtn);

    if (addButton === null) {
      return;
    }

    addButton.addEventListener('click', (e) => {
      e.stopImmediatePropagation();
      const url = this.router.generate(
        'admin_products_specific_prices_create',
        {
          productId: this.productId,
          liteDisplaying: 1,
        },
      );
      this.renderSpecificPriceModal(url);
    });
  }

  private renderSpecificPriceModal(formUrl: string) {
    const iframeModal = new FormIframeModal({
      id: 'modal-create-specific-price',
      formSelector: 'form[name="specific_price"]',
      formUrl,
      closable: true,
      closeButtonLabel: 'close',
      confirmButtonLabel: 'save',
      closeOnConfirm: false,
      confirmCallback: (iframe: HTMLIFrameElement): void => {
        if (!iframe.contentWindow) {
          return;
        }
        const form = iframe.contentWindow.document.querySelector<HTMLFormElement>('form[name="specific_price"]');

        if (!form) {
          return;
        }

        form.submit();
      },
    });
    iframeModal.show();
  }

  private async deleteSpecificPrice() {
    alert('toto');
  }
}
