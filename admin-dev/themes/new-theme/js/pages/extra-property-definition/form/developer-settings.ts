/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import SuggestionDropdown from '@pages/extra-property-definition/form/suggestion-dropdown';
import {ExtraPropertyCatalogs} from '@pages/extra-property-definition/form/types';
import ExtraPropertyFormMap from '@pages/extra-property-definition/form/extra-property-form-map';

/**
 * Curated shortlist of the form type FQCNs a developer typically overrides with — free text
 * stays allowed, this only feeds the suggestion dropdown.
 */
const COMMON_FORM_TYPES = [
  'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType',
  'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextareaType',
  'Symfony\\Component\\Form\\Extension\\Core\\Type\\IntegerType',
  'Symfony\\Component\\Form\\Extension\\Core\\Type\\NumberType',
  'Symfony\\Component\\Form\\Extension\\Core\\Type\\MoneyType',
  'Symfony\\Component\\Form\\Extension\\Core\\Type\\EmailType',
  'Symfony\\Component\\Form\\Extension\\Core\\Type\\UrlType',
  'Symfony\\Component\\Form\\Extension\\Core\\Type\\CheckboxType',
  'Symfony\\Component\\Form\\Extension\\Core\\Type\\ChoiceType',
  'Symfony\\Component\\Form\\Extension\\Core\\Type\\DateType',
  'PrestaShopBundle\\Form\\Admin\\Type\\SwitchType',
  'PrestaShopBundle\\Form\\Admin\\Type\\DatePickerType',
  'PrestaShopBundle\\Form\\Admin\\Type\\FormattedTextareaType',
  'PrestaShopBundle\\Form\\Admin\\Type\\ColorPickerType',
];

const shortName = (fqcn: string): string => fqcn.split('\\').pop() ?? fqcn;

/**
 * Developer corner of the Placement card (kept as plain rows — no disclosure):
 * - the Symfony form type input shows the EFFECTIVE default as its placeholder, recomputed live
 *   from the field type select and the inline type=>FormType map, plus an FQCN suggestion
 *   dropdown (free text always allowed);
 * - the form options textarea gets blur-time JSON.parse feedback and a "Format JSON"
 *   pretty-print action. Server-side validation stays authoritative.
 */
export default class DeveloperSettings {
  private container: HTMLElement;

  private catalogs: ExtraPropertyCatalogs;

  private typeInput: HTMLInputElement | null;

  private optionsTextarea: HTMLTextAreaElement | null;

  constructor(container: HTMLElement, catalogs: ExtraPropertyCatalogs) {
    this.container = container;
    this.catalogs = catalogs;
    this.typeInput = container.querySelector<HTMLInputElement>(ExtraPropertyFormMap.developer.formTypeInput);
    this.optionsTextarea = container.querySelector<HTMLTextAreaElement>(ExtraPropertyFormMap.developer.formOptionsTextarea);

    this.wireDefaultPlaceholder();
    this.wireTypeSuggestions();
    this.wireJsonFeedback();
  }

  private label(key: string, fallback: string): string {
    return this.container.dataset[key] ?? fallback;
  }

  private wireDefaultPlaceholder(): void {
    const typeSelect = document.querySelector<HTMLSelectElement>(ExtraPropertyFormMap.fieldTypeSelect);

    if (!this.typeInput) {
      return;
    }

    const refresh = (): void => {
      const defaultFqcn = this.catalogs.defaultFormTypes[typeSelect?.value ?? ''];

      if (defaultFqcn) {
        (<HTMLInputElement> this.typeInput).placeholder = this.label('labelDefaultType', 'Default: %type%')
          .replace('%type%', shortName(defaultFqcn));
      }
    };

    typeSelect?.addEventListener('change', refresh);
    refresh();
  }

  private wireTypeSuggestions(): void {
    if (!this.typeInput || this.typeInput.disabled) {
      return;
    }

    new SuggestionDropdown(this.container, ExtraPropertyFormMap.developer.formTypeInput, {
      items: () => COMMON_FORM_TYPES.map((fqcn) => ({value: fqcn, label: shortName(fqcn), detail: fqcn})),
    });
  }

  private wireJsonFeedback(): void {
    const textarea = this.optionsTextarea;
    const status = this.container.querySelector<HTMLElement>(ExtraPropertyFormMap.developer.jsonStatus);
    const formatButton = this.container.querySelector<HTMLButtonElement>(ExtraPropertyFormMap.developer.formatJson);

    if (!textarea || textarea.disabled || !status || !formatButton) {
      return;
    }

    textarea.classList.add('text-monospace');
    // Rendered hidden by the form theme — only useful once this wiring exists.
    formatButton.classList.remove('d-none');

    const refresh = (): void => {
      if (textarea.value.trim() === '') {
        status.textContent = '';
        status.className = 'extra-property-json-status';

        return;
      }

      try {
        JSON.parse(textarea.value);
        status.className = 'extra-property-json-status extra-property-json-status--ok';
        status.textContent = `✓ ${this.label('labelValidJson', 'Valid JSON')}`;
      } catch (error) {
        status.className = 'extra-property-json-status extra-property-json-status--warn';
        status.textContent = this.label('labelInvalidJson', 'Invalid JSON: %error%')
          .replace('%error%', error instanceof Error ? error.message : String(error));
      }
    };

    textarea.addEventListener('blur', refresh);
    formatButton.addEventListener('click', () => {
      try {
        textarea.value = JSON.stringify(JSON.parse(textarea.value), null, 4);
        refresh();
      } catch (error) {
        refresh();
      }
    });
    refresh();
  }
}
