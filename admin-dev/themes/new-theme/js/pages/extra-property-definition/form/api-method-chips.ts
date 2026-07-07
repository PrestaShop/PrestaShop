/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ExtraPropertyFormMap from '@pages/extra-property-definition/form/extra-property-form-map';
import cloneTemplate from '@pages/extra-property-definition/form/templates';
import {ExtraPropertyCatalogs} from '@pages/extra-property-definition/form/types';

// Whitelist mirror of AssociationEntryParser::ALLOWED_HTTP_METHODS (display fallback for
// endpoints outside the catalog — the server stays the validation authority).
const ALLOWED_HTTP_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

/**
 * Method chips of the Admin API placement rows: the raw CSV input ("GET,PATCH") stays the row's
 * value carrier but is hidden behind toggle chips — one per method the catalog exposes for the
 * row's endpoint (the full whitelist for endpoints outside the catalog, and any extra method
 * already present in the raw value so nothing is ever lost). No selection = the "All methods"
 * pill = a bare entry with no ":METHODS" suffix.
 */
export default class ApiMethodChips {
  private methodsByUri: Map<string, string[]>;

  constructor(catalogs: ExtraPropertyCatalogs) {
    this.methodsByUri = new Map(catalogs.apis.map((entry) => [entry.uriTemplate, entry.methods]));
  }

  enhance(row: HTMLElement): void {
    const methodsInput = row.querySelector<HTMLInputElement>(ExtraPropertyFormMap.rowField('methods'));
    const uriInput = row.querySelector<HTMLInputElement>(ExtraPropertyFormMap.rowField('uri'));

    if (!methodsInput || !uriInput) {
      return;
    }

    methodsInput.classList.add('d-none');
    this.render(row, uriInput, methodsInput);

    // A different endpoint may expose different methods.
    uriInput.addEventListener('input', () => this.render(row, uriInput, methodsInput));
  }

  private render(row: HTMLElement, uriInput: HTMLInputElement, methodsInput: HTMLInputElement): void {
    row.querySelector(ExtraPropertyFormMap.api.methodChips)?.remove();

    const selected = methodsInput.value.split(',').map((method) => method.trim()).filter((method) => method !== '');
    const catalogMethods = this.methodsByUri.get(uriInput.value.trim()) ?? ALLOWED_HTTP_METHODS;
    // Union keeps manually-typed methods visible even when the catalog does not list them.
    const available = catalogMethods.concat(selected.filter((method) => !catalogMethods.includes(method)));

    const chips = cloneTemplate('method-chips');
    const allPill = <HTMLButtonElement>chips.querySelector(ExtraPropertyFormMap.api.allMethodsChip);
    allPill.classList.toggle('active', selected.length === 0);
    allPill.setAttribute('aria-pressed', selected.length === 0 ? 'true' : 'false');
    allPill.addEventListener('click', () => ApiMethodChips.write(row, uriInput, methodsInput, [], this));

    available.forEach((method) => {
      const chip = cloneTemplate('method-chip');
      chip.classList.toggle('active', selected.includes(method));
      chip.setAttribute('aria-pressed', selected.includes(method) ? 'true' : 'false');
      chip.textContent = method;
      chip.addEventListener('click', () => {
        const next = selected.includes(method)
          ? selected.filter((candidate) => candidate !== method)
          : [...selected, method];
        ApiMethodChips.write(row, uriInput, methodsInput, next, this);
      });
      chips.appendChild(chip);
    });

    methodsInput.insertAdjacentElement('afterend', chips);
  }

  private static write(
    row: HTMLElement,
    uriInput: HTMLInputElement,
    methodsInput: HTMLInputElement,
    methods: string[],
    chips: ApiMethodChips,
  ): void {
    /* eslint-disable no-param-reassign */
    methodsInput.value = methods.join(',');
    /* eslint-enable no-param-reassign */
    methodsInput.dispatchEvent(new Event('input', {bubbles: true}));
    chips.render(row, uriInput, methodsInput);
  }
}
