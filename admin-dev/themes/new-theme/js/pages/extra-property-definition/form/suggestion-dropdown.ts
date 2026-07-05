/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export interface SuggestionItem {
  /** Written into the input on selection. */
  value: string;
  /** Main display text (human label). */
  label: string;
  /** Gray secondary text (technical id / FQCN). */
  detail: string;
}

interface SuggestionDropdownOptions {
  /** Receives the focused input so items can depend on the row context (e.g. its grid id). */
  items: (input: HTMLInputElement) => SuggestionItem[];
  /** Called after a suggestion was written into the input (side effects like defaulting siblings). */
  onSelect?: (value: string, input: HTMLInputElement) => void;
}

/**
 * Generic "free text + suggestions" enhancer shared by the catalog-backed inputs (form/grid id
 * pickers, form type FQCN): the input always accepts free text — pointing outside the catalog
 * is supported — and focusing/typing shows a filtered dropdown of catalog entries. Delegated on
 * a container so it survives dynamically added rows. Keyboard operable (arrows/Enter/Escape).
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
      if (event.target === this.activeInput) {
        this.filter(this.activeInput?.value ?? '');
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

    const dropdown = document.createElement('div');
    dropdown.className = 'extra-property-anchor-dropdown';
    dropdown.setAttribute('role', 'listbox');

    items.forEach((item) => {
      const option = document.createElement('button');
      option.type = 'button';
      option.className = 'extra-property-anchor-option';
      option.setAttribute('role', 'option');
      option.dataset.value = item.value;
      option.innerHTML = '<span class="extra-property-anchor-option__label"></span>'
        + '<span class="extra-property-anchor-option__path"></span>';
      (<HTMLElement>option.querySelector('.extra-property-anchor-option__label')).textContent = item.label;
      (<HTMLElement>option.querySelector('.extra-property-anchor-option__path')).textContent = item.detail;
      option.addEventListener('mousedown', (event) => {
        // mousedown (not click) so the selection beats the input's focusout.
        event.preventDefault();
        this.select(option);
      });
      dropdown.appendChild(option);
    });

    let wrap = input.closest<HTMLElement>('.extra-property-anchor-wrap');

    if (!wrap) {
      wrap = document.createElement('span');
      wrap.className = 'extra-property-anchor-wrap';
      input.insertAdjacentElement('beforebegin', wrap);
      wrap.appendChild(input);
    }

    wrap.appendChild(dropdown);
    this.dropdown = dropdown;
    this.filter(input.value);
  }

  private select(option: HTMLElement): void {
    const input = this.activeInput;

    if (!input) {
      return;
    }

    input.value = option.dataset.value ?? '';
    input.dispatchEvent(new Event('input', {bubbles: true}));
    this.options.onSelect?.(input.value, input);
    this.close();
    input.focus();
  }

  private visibleOptions(): HTMLElement[] {
    return Array.from(this.dropdown?.querySelectorAll<HTMLElement>('.extra-property-anchor-option:not(.d-none)') ?? []);
  }

  private filter(query: string): void {
    if (!this.dropdown) {
      return;
    }

    const needle = query.trim().toLowerCase();

    this.dropdown.querySelectorAll<HTMLElement>('.extra-property-anchor-option').forEach((option) => {
      const haystack = `${option.dataset.value} ${option.textContent}`.toLowerCase();
      option.classList.toggle('d-none', needle !== '' && !haystack.includes(needle));
    });

    this.highlight(-1);
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
