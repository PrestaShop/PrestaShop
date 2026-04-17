/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {createApp, App} from 'vue';
import CarrierSelector from '@pages/product/carrier/CarrierSelector.vue';
import {createI18n} from 'vue-i18n';
import ReplaceFormatter from '@PSVue/plugins/vue-i18n/replace-formatter';
import ProductMap from '@pages/product/product-map';
import {Choice} from '@app/components/checkboxes-dropdown/types';
import EventEmitter from '@components/event-emitter';

/**
 * @returns {Vue | CombinedVueInstance<Vue, {eventEmitter, carriers}, object, object, Record<never, any>>}
 */
export default function initCarrierSelector(
  carrierChoicesSelector: string,
  eventEmitter: typeof EventEmitter,
): App {
  const container = <HTMLElement> document.querySelector(carrierChoicesSelector);
  const translations = JSON.parse(<string>container.dataset.translations);
  const i18n = createI18n({
    locale: 'en',
    formatter: new ReplaceFormatter(),
    messages: {en: translations},
  });

  const carrierChoiceLabelElements = <NodeListOf<HTMLLabelElement>> container.querySelectorAll(
    ProductMap.shipping.carrierChoiceLabel,
  );

  const carrierChoices: Choice[] = [];
  const initialCarrierIds = <number[]> [];
  let choiceInputName = '';
  // get and format carrier choices to fit for checkbox dropdown component type requirement
  carrierChoiceLabelElements.forEach((label: HTMLLabelElement) => {
    const input = <HTMLInputElement> label.querySelector('input');

    if (input.checked) {
      initialCarrierIds.push(Number(input.value));
    }

    carrierChoices.push({
      id: Number(input.value),
      name: <string> input.name,
      label: <string> label.textContent,
    });

    if (choiceInputName === '') {
      // get the name of choice input which is important so that in backend side it is correctly filled in form when handling request
      choiceInputName = input.name;
    }
  });

  const vueApp: App<Element> = createApp(CarrierSelector, {
    i18n,
    carrierChoices,
    initialCarrierIds,
    choiceInputName,
    eventEmitter,
  }).use(i18n);

  vueApp.mount(carrierChoicesSelector);

  return vueApp;
}
