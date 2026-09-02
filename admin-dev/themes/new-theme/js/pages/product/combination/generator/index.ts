/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {createApp, App} from 'vue';
import {createI18n} from 'vue-i18n';
import EventEmitter from '@components/event-emitter';
import ReplaceFormatter from '@PSVue/plugins/vue-i18n/replace-formatter';
import CombinationGenerator from '@pages/product/combination/generator/CombinationGenerator.vue';

export default function initCombinationGenerator(
  combinationGeneratorSelector: string,
  eventEmitter: typeof EventEmitter,
  productId: number,
  shopId: number,
): App {
  const container = <HTMLElement> document.querySelector(combinationGeneratorSelector);

  const translations = JSON.parse(<string>container.dataset.translations);
  const i18n = createI18n({
    locale: 'en',
    formatter: new ReplaceFormatter(),
    messages: {en: translations},
  });

  const vueApp = createApp(CombinationGenerator, {
    i18n,
    productId,
    shopId,
    isMultiStoreActive: Boolean(container.dataset.isMultiStoreActive),
    eventEmitter,
  }).use(i18n);

  vueApp.mount(combinationGeneratorSelector);

  return vueApp;
}
