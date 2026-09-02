/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
