/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 */

type FilterField = HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement;

const DEFAULT_CONTAINER_SELECTOR = '[data-filter-link-group]';
const DEFAULT_ITEM_SELECTOR = '[data-filter-value]';
const DEFAULT_ACTIVE_CLASS = 'is-active';
const DEFAULT_SUBMIT_BUTTON_SELECTOR = '.grid-search-button';

/**
 * FilterLinkGroup turns a list of links into a form filter controller.
 *
 * Expected markup:
 * <div
 *   data-filter-link-group
 *   data-filter-field-selector="#my-hidden-filter"
 *   data-filter-submit-button-selector=".grid-search-button"
 * >
 *   <a data-filter-value="foo">Foo</a>
 * </div>
 */
export default class FilterLinkGroup {
  private readonly containerSelector: string;

  constructor(containerSelector: string = DEFAULT_CONTAINER_SELECTOR) {
    this.containerSelector = containerSelector;
    this.init();
  }

  private init(): void {
    const containers = document.querySelectorAll<HTMLElement>(this.containerSelector);

    containers.forEach((container) => {
      const fieldSelector = container.dataset.filterFieldSelector;

      if (!fieldSelector) {
        console.warn('[FilterLinkGroup] Missing "data-filter-field-selector" attribute on container.', container);

        return;
      }

      const field = document.querySelector<FilterField>(fieldSelector);

      if (!field) {
        console.warn('[FilterLinkGroup] Unable to find filter field for selector:', fieldSelector);

        return;
      }

      this.bindContainer(container, field);
    });
  }

  private bindContainer(container: HTMLElement, field: FilterField): void {
    const filterField = field;
    const itemSelector = container.dataset.filterItemSelector ?? DEFAULT_ITEM_SELECTOR;
    const activeClass = container.dataset.filterActiveClass ?? DEFAULT_ACTIVE_CLASS;
    const submitButtonSelector = container.dataset.filterSubmitButtonSelector ?? DEFAULT_SUBMIT_BUTTON_SELECTOR;

    const updateActiveState = (value: string): void => {
      const items = container.querySelectorAll<HTMLElement>(itemSelector);

      items.forEach((item) => {
        const itemValue = item.dataset.filterValue ?? item.getAttribute('data-filter-value') ?? '';
        const isActive = itemValue === value;

        if (isActive) {
          item.classList.add(activeClass);
          item.setAttribute('aria-pressed', 'true');
        } else {
          item.classList.remove(activeClass);
          item.setAttribute('aria-pressed', 'false');
        }
      });
    };

    const initialValue = container.dataset.filterInitialValue ?? filterField.value;
    filterField.value = initialValue;
    updateActiveState(initialValue);

    container.addEventListener('click', (event: Event) => {
      const target = (event.target as HTMLElement).closest<HTMLElement>(itemSelector);

      if (!target) {
        return;
      }

      const value = target.dataset.filterValue ?? target.getAttribute('data-filter-value');

      if (value === null) {
        return;
      }

      event.preventDefault();

      if (filterField.value !== value) {
        filterField.value = value;
        filterField.dispatchEvent(new Event('change', {bubbles: true}));
      }

      updateActiveState(value);
      this.submitForm(filterField, container, submitButtonSelector);
    });

    filterField.addEventListener('change', () => {
      updateActiveState(filterField.value);
    });
  }

  private submitForm(field: FilterField, container: HTMLElement, submitButtonSelector: string): void {
    const form = field.closest('form');

    if (!form) {
      return;
    }

    if (submitButtonSelector !== 'none') {
      const submitButton = form.querySelector<HTMLButtonElement>(submitButtonSelector)
        ?? container.querySelector<HTMLButtonElement>(submitButtonSelector);

      if (submitButton && !submitButton.disabled) {
        submitButton.click();

        return;
      }
    }

    form.submit();
  }
}
