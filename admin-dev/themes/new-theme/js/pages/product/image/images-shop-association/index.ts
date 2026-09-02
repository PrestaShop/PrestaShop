/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {createApp, App} from 'vue';
import {createI18n} from 'vue-i18n';
import ReplaceFormatter from '@PSVue/plugins/vue-i18n/replace-formatter';
import ImageShopAssociationModal from './ImageShopAssociationModal.vue';

export default function initImagesShopAssociation(buttonContainerSelector: string, shopId: number): App | null {
  const container = document.querySelector<HTMLElement>(buttonContainerSelector);

  if (!container) {
    return null;
  }

  const translations = JSON.parse(<string>container.dataset.translations);
  const i18n = createI18n({
    locale: 'en',
    formatter: new ReplaceFormatter(),
    messages: {en: translations},
  });

  const productId = Number(container.dataset.productId);

  const vueApp = createApp(ImageShopAssociationModal, {
    el: buttonContainerSelector,
    template: '<image-shop-association-modal :productId=productId />',
    i18n,
    productId,
    shopId,
  }).use(i18n);

  vueApp.mount(buttonContainerSelector);

  return vueApp;
}
