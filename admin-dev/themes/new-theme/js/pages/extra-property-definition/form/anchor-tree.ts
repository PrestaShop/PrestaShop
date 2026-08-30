/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ExtraPropertyFormMap from '@pages/extra-property-definition/form/extra-property-form-map';
import cloneTemplate from '@pages/extra-property-definition/form/templates';

interface FormFieldNode {
  name: string;
  path: string;
  label: string;
  compound: boolean;
  children: FormFieldNode[];
}

interface FormFieldsResponse {
  formId: string;
  available: boolean;
  fields: FormFieldNode[];
}

interface AnchorItem {
  path: string;
  label: string;
  compound: boolean;
  depth: number;
}

/** Deepest indentation class defined in SCSS — deeper nodes clamp to it and stay readable. */
const MAX_DEPTH_CLASS = 6;

/**
 * Lazy anchor picker of the Forms placement rows: focusing a row's path input opens a dropdown
 * with the target form's recursive field tree, fetched ONCE per formId from the
 * /form-fields/{formId} endpoint and cached. Selecting a leaf preselects "after" (a leaf is only
 * an anchor); selecting a compound node keeps "Automatic" (append inside the container) but the
 * position stays editable — placing before/after a sub-section (description.categories:before)
 * is legitimate. When the endpoint answers available:false the input silently stays free text —
 * pointing inside a form the introspection cannot build is a supported manual override. Typing
 * while the dropdown is open filters it; typing is also always accepted as a free-text path.
 */
export default class AnchorTreeEnhancer {
  private urlTemplate: string;

  private cache = new Map<string, Promise<FormFieldsResponse>>();

  private dropdown: HTMLElement | null = null;

  private activeInput: HTMLInputElement | null = null;

  private activeIndex = -1;

  constructor(container: HTMLElement) {
    this.urlTemplate = container.dataset.formFieldsUrl ?? '';

    container.addEventListener('focusin', (event) => {
      const input = <HTMLElement>event.target;

      if (input.matches(ExtraPropertyFormMap.rowField('path'))) {
        this.open(<HTMLInputElement>input);
      }
    });

    container.addEventListener('focusout', () => {
      // Delayed so a click on a dropdown item wins over the blur.
      window.setTimeout(() => {
        if (!this.dropdown?.contains(document.activeElement) && document.activeElement !== this.activeInput) {
          this.close();
        }
      }, 150);
    });

    container.addEventListener('keydown', (event) => this.onKeydown(event));
    container.addEventListener('input', (event) => {
      if (event.target === this.activeInput) {
        this.filter(this.activeInput?.value ?? '');
      }
    });
  }

  private formIdOf(input: HTMLInputElement): string {
    const row = input.closest<HTMLElement>(ExtraPropertyFormMap.subsection.row);

    return row?.querySelector<HTMLInputElement>(ExtraPropertyFormMap.rowField('form_id'))?.value.trim() ?? '';
  }

  private fetchTree(formId: string): Promise<FormFieldsResponse> {
    if (!this.cache.has(formId)) {
      this.cache.set(
        formId,
        fetch(this.urlTemplate.replace('__FORM_ID__', encodeURIComponent(formId)))
          .then((response) => {
            if (!response.ok) {
              throw new Error(`form-fields endpoint answered ${response.status}`);
            }

            return <Promise<FormFieldsResponse>>response.json();
          })
          .catch(() => ({formId, available: false, fields: []})),
      );
    }

    return <Promise<FormFieldsResponse>> this.cache.get(formId);
  }

  private static flatten(nodes: FormFieldNode[], depth = 0): AnchorItem[] {
    return nodes.flatMap((node) => [
      {
        path: node.path, label: node.label || node.name, compound: node.compound, depth,
      },
      ...AnchorTreeEnhancer.flatten(node.children ?? [], depth + 1),
    ]);
  }

  private async open(input: HTMLInputElement): Promise<void> {
    const formId = this.formIdOf(input);

    if (formId === '' || this.urlTemplate === '') {
      return;
    }

    this.close();
    this.activeInput = input;
    this.renderLoading(input);

    const tree = await this.fetchTree(formId);

    // Drop the loading state; the user may also have moved on while the tree was loading.
    this.close();

    if (this.activeInput !== input || document.activeElement !== input || !tree.available) {
      return;
    }

    const items = AnchorTreeEnhancer.flatten(tree.fields);

    if (items.length === 0) {
      return;
    }

    this.render(input, items);
    this.filter(input.value);
  }

  /**
   * Shown while the field tree is being fetched (first focus per form — later opens hit the
   * cache); replaced by the tree, or silently removed for a non-introspectable form.
   */
  private renderLoading(input: HTMLInputElement): void {
    const dropdown = cloneTemplate('tree-loading');
    input.closest(ExtraPropertyFormMap.dropdown.anchorWrap)?.appendChild(dropdown);
    this.dropdown = dropdown;
  }

  private render(input: HTMLInputElement, items: AnchorItem[]): void {
    const dropdown = cloneTemplate('suggestion-dropdown');

    items.forEach((item) => {
      const option = cloneTemplate('suggestion-option');
      option.classList.toggle(ExtraPropertyFormMap.dropdown.groupOptionClass, item.compound);

      if (item.depth > 0) {
        option.classList.add(ExtraPropertyFormMap.dropdown.depthClass(Math.min(item.depth, MAX_DEPTH_CLASS)));
      }
      option.dataset.path = item.path;
      option.dataset.compound = item.compound ? '1' : '0';
      (<HTMLElement>option.querySelector(ExtraPropertyFormMap.dropdown.optionLabel)).textContent = item.label;
      (<HTMLElement>option.querySelector(ExtraPropertyFormMap.dropdown.optionDetail)).textContent = item.path;
      option.addEventListener('mousedown', (event) => {
        // mousedown (not click) so the selection beats the input's focusout.
        event.preventDefault();
        this.select(option);
      });
      dropdown.appendChild(option);
    });

    input.closest(ExtraPropertyFormMap.dropdown.anchorWrap)?.appendChild(dropdown);
    this.dropdown = dropdown;
    this.activeIndex = -1;
  }

  private select(option: HTMLElement): void {
    const input = this.activeInput;

    if (!input) {
      return;
    }

    const compound = option.dataset.compound === '1';
    const row = input.closest<HTMLElement>(ExtraPropertyFormMap.subsection.row);
    const modeSelect = row?.querySelector<HTMLSelectElement>(ExtraPropertyFormMap.rowField('mode'));

    input.value = option.dataset.path ?? '';

    if (modeSelect) {
      // Recover from the disabled state older sessions could leave behind.
      modeSelect.disabled = false;

      // A leaf can only be an anchor, so "after" beats the meaningless "Automatic"; a compound
      // defaults to "Automatic" (append inside the container) but before/after stays available —
      // placing relative to a sub-section is legitimate.
      if (!compound && modeSelect.value === '') {
        modeSelect.value = 'after';
      }

      modeSelect.dispatchEvent(new Event('change', {bubbles: true}));
    }

    input.dispatchEvent(new Event('input', {bubbles: true}));
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
      const haystack = `${option.dataset.path} ${option.textContent}`.toLowerCase();
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
  }
}
