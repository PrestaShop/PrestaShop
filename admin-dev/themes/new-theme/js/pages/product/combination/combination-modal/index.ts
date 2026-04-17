/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {createApp, App} from 'vue';
import {createI18n} from 'vue-i18n';
import EventEmitter from '@components/event-emitter';
import ReplaceFormatter from '@PSVue/plugins/vue-i18n/replace-formatter';
import CombinationModal from '@pages/product/combination/combination-modal/CombinationModal.vue';
import PaginatedCombinationsService from '@pages/product/service/paginated-combinations-service';

/**
 * @param {string} combinationModalSelector
 * @param {PaginatedCombinationsService} paginatedCombinationsService
 * @param {Object} eventEmitter
 *
 * @returns {Vue|CombinedVueInstance<Vue, {eventEmitter, productId}, object, object, Record<never, any>>|null}
 */
export default function initCombinationModal(
  combinationModalSelector: string,
  paginatedCombinationsService: PaginatedCombinationsService,
  eventEmitter: typeof EventEmitter,
): App | null {
  const container = <HTMLElement> document.querySelector(combinationModalSelector);
  const {emptyImage} = container.dataset;

  const translations = JSON.parse(<string>container.dataset.translations);
  const i18n = createI18n({
    locale: 'en',
    formatter: new ReplaceFormatter(),
    messages: {en: translations},
  });

  const vueApp = createApp(CombinationModal, {
    i18n,
    eventEmitter,
    emptyImage,
    paginatedCombinationsService,
  }).use(i18n);

  vueApp.mount(combinationModalSelector);

  return vueApp;
}
