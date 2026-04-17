/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {createApp, App} from 'vue';
import {createI18n} from 'vue-i18n';
import ReplaceFormatter from '@PSVue/plugins/vue-i18n/replace-formatter';
import Dropzone from './Dropzone.vue';

export default function initDropzone(imagesContainerSelector: string): App {
  const container = <HTMLElement>document.querySelector(imagesContainerSelector);

  const translations = JSON.parse(<string>container.dataset.translations);
  const i18n = createI18n({
    locale: 'en',
    formatter: new ReplaceFormatter(),
    messages: {en: translations},
  });

  const productId = Number(container.dataset.productId);
  const locales = JSON.parse(<string>container.dataset.locales);

  const vueApp = createApp(Dropzone, {
    el: imagesContainerSelector,
    template: '<dropzone :productId=productId :locales=locales :token=token :formName=formName />',
    i18n,
    locales,
    productId,
    shopId: Number(container.dataset.shopId),
    isMultiStoreActive: !!Number(container.dataset.isMultiStoreActive),
    token: container.dataset.token,
    formName: container.dataset.formName,
  }).use(i18n);

  vueApp.mount(imagesContainerSelector);

  return vueApp;
}
