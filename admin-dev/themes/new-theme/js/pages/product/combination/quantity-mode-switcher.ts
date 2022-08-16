import {SwitchEventData} from '@components/form/form-field-toggler';
import {EventEmitter} from 'events';
import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';

const combinationMap = ProductMap.combinations;
const combinationEvents = ProductEventMap.combinations;

/**
 * Switches between quantity modes (delta or fixed) to make sure that only one of them is switched on at a time.
 *
 * E.g. if user switches on the fixed quantity input, then delta quantity input is switched off.
 */
export default class QuantityModeSwitcher {
  private eventEmitter: EventEmitter;

  constructor() {
    this.eventEmitter = window.prestashop.component.EventEmitter;
    this.init();
  }

  private init(): void {
    this.eventEmitter.on(combinationEvents.combinationSwitchDeltaQuantity, (eventData: SwitchEventData) => {
      // switch OFF fixed quantity only when delta quantity is being switched ON
      if (!eventData.disable) {
        toggleSwitch(combinationMap.bulkFixedQuantitySwitchName, false);
      }
    });
    this.eventEmitter.on(combinationEvents.combinationSwitchFixedQuantity, (eventData: SwitchEventData) => {
      // switch OFF delta quantity only when fixed quantity is being switched ON
      if (!eventData.disable) {
        toggleSwitch(combinationMap.bulkDeltaQuantitySwitchName, false);
      }
    });

    function toggleSwitch(switchName: string, checked: boolean): void {
      const $switchOn = $(`[name="${switchName}"][value="1"]`);
      const $switchOff = $(`[name="${switchName}"][value="0"]`);

      if ($switchOn.is(':checked') !== checked) {
        $switchOn.prop('checked', checked);
      }
      if ($switchOff.is(':checked') === checked) {
        $switchOff.prop('checked', !checked);
      }
    }
  }
}
