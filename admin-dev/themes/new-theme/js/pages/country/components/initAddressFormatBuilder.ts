/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {App, createApp} from 'vue';
import {createI18n} from 'vue-i18n';
import ReplaceFormatter from '@PSVue/plugins/vue-i18n/replace-formatter';
import AddressFormatBuilder from '@pages/country/components/AddressFormatBuilder.vue';

/**
 * Mounts the address-format Vue 3 builder onto every container matching
 * `.js-address-format-builder` rendered by the AddressFormatType form theme.
 *
 * The component owns a hidden `<input>` (rendered by the Twig theme) so the
 * Symfony form processor keeps its existing contract — Vue just keeps the
 * input's `value` in sync with the visual state on every mutation.
 */
export default function initAddressFormatBuilder(wrapperSelector: string): App | null {
  const wrapper = document.querySelector<HTMLElement>(wrapperSelector);

  if (!wrapper) {
    return null;
  }

  const root = wrapper.querySelector<HTMLElement>('.js-address-format-builder');
  const hiddenInput = wrapper.querySelector<HTMLInputElement>('.js-address-format-input');

  if (!root || !hiddenInput) {
    return null;
  }

  const initialValue = root.dataset.value ?? '';
  const objects = JSON.parse(root.dataset.objects ?? '{}');
  const requiredFields = JSON.parse(root.dataset.requiredFields ?? '[]');
  const sampleData = JSON.parse(root.dataset.sampleData ?? '{}');
  const translations = JSON.parse(root.dataset.translations ?? '{}');
  const defaultFormat = root.dataset.defaultFormat ?? '';
  const requiredFieldsUrl = root.dataset.requiredFieldsUrl ?? '';

  const i18n = createI18n({
    locale: 'en',
    formatter: new ReplaceFormatter(),
    messages: {en: translations},
  });

  const app = createApp(AddressFormatBuilder, {
    hiddenInput,
    initialValue,
    objects,
    requiredFields,
    defaultFormat,
    sampleData,
    requiredFieldsUrl,
  });
  app.use(i18n);
  app.mount(root);

  return app;
}

export function initAllAddressFormatBuilders(): App[] {
  const wrappers = Array.from(document.querySelectorAll<HTMLElement>('.js-address-format-wrapper'));

  return wrappers
    .map((wrapper) => {
      const id = wrapper.id || `address-format-wrapper-${Math.random().toString(36).slice(2, 9)}`;
      wrapper.setAttribute('id', id);
      return initAddressFormatBuilder(`#${id}`);
    })
    .filter((app): app is App => app !== null);
}
