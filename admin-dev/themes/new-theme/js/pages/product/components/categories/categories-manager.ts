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
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import CategoryTreeSelector from '@pages/product/components/categories/category-tree-selector';
import Tags from '@pages/product/components/categories/tags';
import {EventEmitter} from 'events';

const ProductCategoryMap = ProductMap.categories;

export default class CategoriesManager {
  eventEmitter: EventEmitter;

  categoryTreeSelector: CategoryTreeSelector;

  categoriesContainer: HTMLElement;

  addCategoriesBtn: HTMLElement;

  typeaheadData: Array<any>;

  tags: Tags;

  constructor(eventEmitter: EventEmitter) {
    this.eventEmitter = eventEmitter;
    this.categoryTreeSelector = new CategoryTreeSelector(eventEmitter);
    this.categoriesContainer = document.querySelector(ProductCategoryMap.categoriesContainer) as HTMLElement;
    this.addCategoriesBtn = this.categoriesContainer.querySelector(ProductCategoryMap.addCategoriesBtn) as HTMLElement;
    this.typeaheadData = [];
    this.tags = new Tags(
      eventEmitter,
      `${ProductCategoryMap.categoriesContainer} ${ProductCategoryMap.tagsContainer}`,
    );
    this.tags.render(this.collectCategories());
    this.renderDefaultCategorySelection();
    this.listenCategoryChanges();
    this.listenDefaultCategorySelect();
    this.initCategoryTreeModal();
  }

  private initCategoryTreeModal(): void {
    this.addCategoriesBtn.addEventListener('click', () => this.categoryTreeSelector.showModal(
      this.collectCategories(),
    ));
    this.eventEmitter.on(ProductEventMap.categories.applyCategoryTreeChanges, (eventData) => {
      this.tags.render(eventData.categories);
    });
  }

  private collectCategories(): Array<Category> {
    // these are at first rendered on page load and later updated dynamically
    const tagsContainer = this.categoriesContainer.querySelector(ProductCategoryMap.tagsContainer) as HTMLElement;
    const tags = tagsContainer.querySelectorAll(ProductCategoryMap.tagItem);
    const categories: Array<{ id: number; name: string; isDefault: boolean; }> = [];

    tags.forEach((tag: Element) => {
      if (tag instanceof HTMLElement) {
        const defaultCategoryCheckbox = tag.querySelector(ProductCategoryMap.defaultCategoryCheckbox) as HTMLInputElement;

        categories.push({
          id: Number(tag.dataset.id),
          name: this.extractCategoryName(tag as HTMLElement),
          isDefault: defaultCategoryCheckbox.checked,
        });
      }
    });

    return categories;
  }

  private extractCategoryName(tag: HTMLElement): string {
    const tagNameElement = tag.querySelector(ProductCategoryMap.categoryNamePreview) as HTMLElement;

    if (tagNameElement) {
      return tagNameElement.innerText;
    }

    return '';
  }

  private renderDefaultCategorySelection(): void {
    const categories = this.collectCategories();
    const selectContainer = this.categoriesContainer.querySelector(ProductCategoryMap.defaultCategorySelectContainer);

    if (!(selectContainer instanceof HTMLElement)) {
      console.error('"defaultCategorySelectContainer is not defined or invalid');

      return;
    }

    const selectElement = this.categoriesContainer.querySelector(ProductCategoryMap.defaultCategorySelectInput) as HTMLElement;
    selectElement.innerHTML = '';

    categories.forEach((category) => {
      const optionElement = document.createElement('option');
      optionElement.value = String(category.id);
      optionElement.innerHTML = category.name;
      optionElement.selected = category.isDefault;

      selectElement.append(optionElement);
    });

    selectContainer.classList.remove('d-none');
  }

  private listenDefaultCategorySelect(): void {
    const defaultCategoryInput = this.categoriesContainer
      .querySelector(ProductCategoryMap.defaultCategorySelectInput) as HTMLInputElement;

    defaultCategoryInput.addEventListener('change', (e) => {
      const currentTarget = e.currentTarget as HTMLInputElement;
      const newDefaultCategoryId = Number(currentTarget.value);
      const categories = this.collectCategories()
        .map((category) => ({...category, isDefault: category.id === newDefaultCategoryId}));

      this.tags.render(categories);
    });
  }

  private listenCategoryChanges(): void {
    this.eventEmitter.on(ProductEventMap.categories.categoryRemoved, () => this.renderDefaultCategorySelection());
    this.eventEmitter.on(ProductEventMap.categories.categoriesUpdated, () => this.renderDefaultCategorySelection());
  }
}
