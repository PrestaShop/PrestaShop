/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
