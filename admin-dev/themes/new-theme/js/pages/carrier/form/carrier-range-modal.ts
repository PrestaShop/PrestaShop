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

import CarrierFormMap from '@pages/carrier/form/carrier-form-map';
import {createApp} from 'vue';
import {createI18n} from 'vue-i18n';
import CarrierRangesModal from '@pages/carrier/form/components/CarrierRangesModal.vue';
import EventEmitter from '@components/event-emitter';
import ReplaceFormatter from '@PSVue/plugins/vue-i18n/replace-formatter';
import CarrierFormEventMap from '@pages/carrier/form/carrier-form-event-map';

export default class CarrierRanges {
  private readonly eventEmitter: typeof EventEmitter;

  constructor(eventEmitter: typeof EventEmitter) {
    this.eventEmitter = eventEmitter;
    this.initRangesSelectionModal();
  }

  initRangesSelectionModal(): void {
    // Create the modal container
    const $showModal = $(CarrierFormMap.addRangeButton);
    const $modalContainer = $(`<div id="${CarrierFormMap.rangesSelectionAppId.slice(1)}"></div>`);
    $showModal.after($modalContainer);

    // Retreive translations from the button
    const i18n = createI18n({
      locale: 'en',
      formatter: new ReplaceFormatter(),
      messages: {en: $showModal.data('translations')},
    });

    // Initialize the Vue app with the CarrierRangesModal component
    const vueApp = createApp(CarrierRangesModal, {
      i18n,
      eventEmitter: this.eventEmitter,
    }).use(i18n);

    // Mount the Vue app to the modal container
    vueApp.mount(CarrierFormMap.rangesSelectionAppId);

    // Open the modal with data when the button "Add range" is clicked
    $showModal.click((e: JQuery.ClickEvent) => {
      e.preventDefault();
      e.stopImmediatePropagation();
      const data = $(CarrierFormMap.rangesInput).val() || '[]';
      this.eventEmitter.emit(CarrierFormEventMap.openRangeSelectionModal, JSON.parse(data.toString()));
    });

    // Listen the modal to apply the ranges selected to the data
    this.eventEmitter.on(CarrierFormEventMap.rangesUpdated, (ranges: Array<object>) => {
      const $data = $(CarrierFormMap.rangesInput);
      $data.val(JSON.stringify(ranges));
    });
  }
}
