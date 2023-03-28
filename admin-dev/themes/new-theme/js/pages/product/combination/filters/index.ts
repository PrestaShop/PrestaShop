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
import EventEmitter from '@components/event-emitter';
import Filters from '@pages/product/combination/filters/Filters.vue';
import {createI18n} from 'vue-i18n';
import ReplaceFormatter from '@PSVue/plugins/vue-i18n/replace-formatter';
import {AttributeGroup} from '@pages/product/combination/types';

/**
 * @param {string} combinationsFiltersSelector
 * @param {EventEmitter} eventEmitter
 * @param {array} attributeGroups
 * @returns {Vue | CombinedVueInstance<Vue, {eventEmitter, filters}, object, object, Record<never, any>>}
 */
export default function initCombinationsFilters(
  combinationsFiltersSelector: string,
  eventEmitter: typeof EventEmitter,
  attributeGroups: Array<AttributeGroup>,
): App {
  const container = <HTMLElement> document.querySelector(combinationsFiltersSelector);

  const translations = JSON.parse(<string>container.dataset.translations);
  const i18n = createI18n({
    locale: 'en',
    formatter: new ReplaceFormatter(),
    messages: {en: translations},
  });

  const vueApp = createApp(Filters, {
    i18n,
    attributeGroups,
    eventEmitter,
  }).use(i18n);

  vueApp.mount(combinationsFiltersSelector);

  return vueApp;
}
