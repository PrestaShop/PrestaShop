/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import PlacementList from '@pages/extra-property-definition/form/placement-list';
import {ExtraPropertyCatalogs} from '@pages/extra-property-definition/form/types';

/**
 * Search-to-add affordance of the Admin API subsection: typing filters the endpoint catalog,
 * grouped by resource (first URI segment); selecting a suggestion adds a row matching all
 * methods. The LAST item is always the escape hatch — "Use "<query>" as a custom path" — so a
 * third-party endpoint outside the catalog can be added without opening the code view (it
 * simply gets the amber not-in-catalog pill).
 */
export default class EndpointSearch {
  private input: HTMLInputElement;

  private list: PlacementList;

  private catalogs: ExtraPropertyCatalogs;

  private dropdown: HTMLElement | null = null;

  private activeIndex = -1;

  constructor(container: HTMLElement, catalogs: ExtraPropertyCatalogs, list: PlacementList) {
    const input = container.querySelector<HTMLInputElement>('[data-role="endpoint-search"]');

    if (!input) {
      throw new Error('Endpoint search input not found');
    }

    this.input = input;
    this.list = list;
    this.catalogs = catalogs;

    input.closest('[data-role="endpoint-search-wrapper"]')?.classList.add('extra-property-anchor-wrap');
    input.addEventListener('focus', () => this.open());
    input.addEventListener('input', () => this.open());
    input.addEventListener('keydown', (event) => this.onKeydown(event));
    input.addEventListener('blur', () => {
      window.setTimeout(() => {
        if (document.activeElement !== this.input) {
          this.close();
        }
      }, 150);
    });
  }

  private open(): void {
    this.close();

    const query = this.input.value.trim().toLowerCase();
    const dropdown = document.createElement('div');
    dropdown.className = 'extra-property-anchor-dropdown';
    dropdown.setAttribute('role', 'listbox');

    const matches = this.catalogs.apis.filter(
      (entry) => query === '' || entry.uriTemplate.toLowerCase().includes(query),
    );

    let lastGroup = '';
    matches.forEach((entry) => {
      const group = entry.uriTemplate.split('/').filter((segment) => segment !== '')[0] ?? '';

      if (group !== lastGroup) {
        lastGroup = group;
        const heading = document.createElement('div');
        heading.className = 'extra-property-anchor-heading';
        heading.textContent = `/${group}`;
        dropdown.appendChild(heading);
      }

      dropdown.appendChild(this.option(
        entry.uriTemplate,
        entry.uriTemplate,
        entry.methods.join(' · '),
        false,
      ));
    });

    if (this.input.value.trim() !== '') {
      dropdown.appendChild(this.option(
        this.input.value.trim(),
        `+ ${this.input.dataset.customLabel ?? 'Use as a custom path'}: ${this.input.value.trim()}`,
        '',
        true,
      ));
    }

    if (!dropdown.hasChildNodes()) {
      return;
    }

    this.input.closest('[data-role="endpoint-search-wrapper"]')?.appendChild(dropdown);
    this.dropdown = dropdown;
    this.activeIndex = -1;
  }

  private option(uri: string, label: string, detail: string, custom: boolean): HTMLElement {
    const option = document.createElement('button');
    option.type = 'button';
    option.className = `extra-property-anchor-option${custom ? ' extra-property-anchor-option--custom' : ''}`;
    option.setAttribute('role', 'option');
    option.dataset.uri = uri;
    option.innerHTML = '<span class="extra-property-anchor-option__label text-monospace"></span>'
      + '<span class="extra-property-anchor-option__path"></span>';
    (<HTMLElement>option.querySelector('.extra-property-anchor-option__label')).textContent = label;
    (<HTMLElement>option.querySelector('.extra-property-anchor-option__path')).textContent = detail;
    option.addEventListener('mousedown', (event) => {
      event.preventDefault();
      this.select(option);
    });

    return option;
  }

  private select(option: HTMLElement): void {
    this.list.addRowWithValues({uri: option.dataset.uri ?? '', methods: ''});
    this.input.value = '';
    this.close();
    this.input.focus();
  }

  private visibleOptions(): HTMLElement[] {
    return Array.from(this.dropdown?.querySelectorAll<HTMLElement>('.extra-property-anchor-option') ?? []);
  }

  private highlight(index: number): void {
    const options = this.visibleOptions();
    this.activeIndex = index;
    options.forEach((candidate, i) => candidate.classList.toggle('active', i === index));

    if (index >= 0 && options[index]) {
      options[index].scrollIntoView({block: 'nearest'});
    }
  }

  private onKeydown(event: KeyboardEvent): void {
    if (!this.dropdown) {
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
        event.preventDefault();

        if (this.activeIndex >= 0 && options[this.activeIndex]) {
          this.select(options[this.activeIndex]);
        } else if (options.length > 0) {
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
  }
}
