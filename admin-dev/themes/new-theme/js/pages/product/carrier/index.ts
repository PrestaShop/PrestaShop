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
