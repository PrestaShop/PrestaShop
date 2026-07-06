/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import cloneTemplate from '@pages/extra-property-definition/form/templates';
import ExtraPropertyFormMap from '@pages/extra-property-definition/form/extra-property-form-map';

export interface SuggestionItem {
  /** Written into the input on selection (unless writeValue is false). */
  value: string;
  /** Main display text (human label). */
  label: string;
  /** Gray secondary text (technical id / FQCN / methods). */
  detail: string;
  /** Optional heading: consecutive items sharing a group render under one heading row. */
  group?: string;
  /** Escape-hatch styling (e.g. "use as a custom path"). */
  custom?: boolean;
}

interface SuggestionDropdownOptions {
  /** Receives the focused input so items can depend on the row context (e.g. its grid id). */
  items: (input: HTMLInputElement) => SuggestionItem[];
  /** Called after a suggestion was picked (side effects like defaulting siblings, adding rows). */
  onSelect?: (value: string, input: HTMLInputElement) => void;
  /** Write the picked value into the input and fire "input" (default true) — search-to-add pickers turn it off. */
  writeValue?: boolean;
  /** Rebuild the items on every keystroke instead of filtering the opened list — for item sets that depend on the query itself. */
  dynamicItems?: boolean;
  /** Enter with nothing highlighted picks the first visible option (default false). */
  enterSelectsFirst?: boolean;
  /** Extra class on option labels (e.g. text-monospace for URI templates). */
  labelClass?: string;
}

/**
 * Generic "free text + suggestions" enhancer shared by every picker of the page (form/grid id,
 * grid column, constraint name, form type FQCN, endpoint search): the input always accepts free
 * text — pointing outside the catalog is supported — and focusing/typing shows a filtered
 * dropdown of catalog entries, optionally grouped under headings. Delegated on a container so it
 * survives dynamically added rows. Keyboard operable (arrows/Enter/Escape).
 */
export default class SuggestionDropdown {
  private options: SuggestionDropdownOptions;

  private dropdown: HTMLElement | null = null;

  private activeInput: HTMLInputElement | null = null;

  private activeIndex = -1;

  constructor(container: HTMLElement, inputSelector: string, options: SuggestionDropdownOptions) {
    this.options = options;

    container.addEventListener('focusin', (event) => {
      const input = <HTMLElement>event.target;

      if (input.matches(inputSelector) && !(<HTMLInputElement>input).disabled) {
        this.open(<HTMLInputElement>input);
      }
    });

    container.addEventListener('input', (event) => {
      if (event.target !== this.activeInput || this.activeInput === null) {
        return;
      }

      if (this.options.dynamicItems) {
        this.open(this.activeInput);
      } else {
        this.filter(this.activeInput.value);
      }
    });

    container.addEventListener('keydown', (event) => this.onKeydown(event));

    container.addEventListener('focusout', () => {
      window.setTimeout(() => {
        if (document.activeElement !== this.activeInput && !this.dropdown?.contains(document.activeElement)) {
          this.close();
        }
      }, 150);
    });
  }

  private open(input: HTMLInputElement): void {
    this.close();

    const items = this.options.items(input);

    if (items.length === 0) {
      return;
    }

    this.activeInput = input;

    const dropdown = cloneTemplate('suggestion-dropdown');

    let lastGroup: string | null = null;
    items.forEach((item) => {
      if (item.group !== undefined && item.group !== lastGroup) {
        lastGroup = item.group;
        const heading = cloneTemplate('suggestion-heading');
        heading.textContent = item.group;
        dropdown.appendChild(heading);
      }

      const option = cloneTemplate('suggestion-option');
      option.classList.toggle(ExtraPropertyFormMap.dropdown.customOptionClass, item.custom === true);
      option.dataset.value = item.value;
      const label = <HTMLElement>option.querySelector(ExtraPropertyFormMap.dropdown.optionLabel);
      label.textContent = item.label;

      if (this.options.labelClass) {
        label.classList.add(this.options.labelClass);
      }
      (<HTMLElement>option.querySelector(ExtraPropertyFormMap.dropdown.optionDetail)).textContent = item.detail;
      option.addEventListener('mousedown', (event) => {
        // mousedown (not click) so the selection beats the input's focusout.
        event.preventDefault();
        this.select(option);
      });
      dropdown.appendChild(option);
    });

    let wrap = input.closest<HTMLElement>(ExtraPropertyFormMap.dropdown.wrap);

    if (!wrap) {
      wrap = document.createElement('span');
      wrap.className = ExtraPropertyFormMap.dropdown.wrapClass;
      input.insertAdjacentElement('beforebegin', wrap);
      wrap.appendChild(input);
    }

    wrap.appendChild(dropdown);
    this.dropdown = dropdown;
    // Query-dependent items are already narrowed by items() itself.
    this.filter(this.options.dynamicItems ? '' : input.value);
  }

  private select(option: HTMLElement): void {
    const input = this.activeInput;

    if (!input) {
      return;
    }

    const value = option.dataset.value ?? '';

    if (this.options.writeValue !== false) {
      input.value = value;
      input.dispatchEvent(new Event('input', {bubbles: true}));
    }
    this.options.onSelect?.(value, input);
    this.close();
    input.focus();
  }

  private visibleOptions(): HTMLElement[] {
    return Array.from(this.dropdown?.querySelectorAll<HTMLElement>(ExtraPropertyFormMap.dropdown.visibleOption) ?? []);
  }

  private filter(query: string): void {
    if (!this.dropdown) {
      return;
    }

    const needle = query.trim().toLowerCase();

    this.dropdown.querySelectorAll<HTMLElement>(ExtraPropertyFormMap.dropdown.option).forEach((option) => {
      const haystack = `${option.dataset.value} ${option.textContent}`.toLowerCase();
      option.classList.toggle('d-none', needle !== '' && !haystack.includes(needle));
    });

    this.refreshHeadings();
    this.highlight(-1);
  }

  /**
   * A heading hides when the filter left it no visible option before the next heading.
   */
  private refreshHeadings(): void {
    if (!this.dropdown) {
      return;
    }

    let heading: HTMLElement | null = null;
    let hasVisibleOption = false;
    const flush = (): void => {
      heading?.classList.toggle('d-none', !hasVisibleOption);
    };

    Array.from(this.dropdown.children).forEach((child) => {
      if (child.classList.contains(ExtraPropertyFormMap.dropdown.headingClass)) {
        flush();
        heading = <HTMLElement>child;
        hasVisibleOption = false;
      } else if (!child.classList.contains('d-none')) {
        hasVisibleOption = true;
      }
    });
    flush();
  }

  private highlight(index: number): void {
    const options = this.visibleOptions();
    this.activeIndex = index;
    options.forEach((option, i) => option.classList.toggle('active', i === index));

    if (index >= 0 && options[index]) {
      options[index].scrollIntoView({block: 'nearest'});
    }
  }

  private onKeydown(event: KeyboardEvent): void {
    if (!this.dropdown || event.target !== this.activeInput) {
      return;
    }

    const options = this.visibleOptions();

    switch (event.key) {
      case 'ArrowDown':
        event.preventDefault();
        this.highlight(Math.min(this.activeIndex + 1, options.length - 1));
        break;
      case 'ArrowUp':
        event.preventDefault();
        this.highlight(Math.max(this.activeIndex - 1, 0));
        break;
      case 'Enter':
        if (this.activeIndex >= 0 && options[this.activeIndex]) {
          event.preventDefault();
          this.select(options[this.activeIndex]);
        } else if (this.options.enterSelectsFirst && options.length > 0) {
          event.preventDefault();
          this.select(options[0]);
        }
        break;
      case 'Escape':
        this.close();
        break;
      default:
    }
  }

  private close(): void {
    this.dropdown?.remove();
    this.dropdown = null;
    this.activeIndex = -1;
    this.activeInput = null;
  }
}
