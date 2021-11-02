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
import TagsRenderer from '@pages/product/components/categories/tags-renderer';
import {EventEmitter} from 'events';

const ProductCategoryMap = ProductMap.categories;

export default class CategoriesManager {
  eventEmitter: EventEmitter;

  categoryTreeSelector: CategoryTreeSelector;

  categoriesContainer: HTMLElement;

  defaultCategoryInput: HTMLInputElement;

  addCategoriesBtn: HTMLElement;

  tagsRenderer: TagsRenderer;

  constructor(eventEmitter: EventEmitter) {
    this.eventEmitter = eventEmitter;
    this.categoryTreeSelector = new CategoryTreeSelector(eventEmitter);
    this.categoriesContainer = document.querySelector(ProductCategoryMap.categoriesContainer) as HTMLElement;
    this.addCategoriesBtn = this.categoriesContainer.querySelector(ProductCategoryMap.addCategoriesBtn) as HTMLElement;
    this.defaultCategoryInput = this.categoriesContainer
      .querySelector(ProductCategoryMap.defaultCategorySelectInput) as HTMLInputElement;
    this.tagsRenderer = new TagsRenderer(
      eventEmitter,
      `${ProductCategoryMap.categoriesContainer} ${ProductCategoryMap.tagsContainer}`,
      ProductEventMap.categories.categoriesUpdated,
    );
    this.listenCategoryChanges();
    this.listenDefaultCategorySelect();
    this.initCategoryTreeModal();
  }

  private initCategoryTreeModal(): void {
    this.addCategoriesBtn.addEventListener('click', () => this.categoryTreeSelector.showModal(
      this.collectCategories(),
      this.getDefaultCategoryId(),
    ));
    this.eventEmitter.on(ProductEventMap.categories.applyCategoryTreeChanges, (eventData) => {
      this.tagsRenderer.render(eventData.categories, this.getDefaultCategoryId());
      this.eventEmitter.emit(ProductEventMap.categories.categoriesUpdated);
    });
  }

  private collectCategories(): Array<Category> {
    // these are at first rendered on page load and later updated dynamically
    const tagsContainer = this.categoriesContainer.querySelector(ProductCategoryMap.tagsContainer) as HTMLElement;
    const tags = tagsContainer.querySelectorAll(ProductCategoryMap.tagItem);
    const categories: Array<Category> = [];

    tags.forEach((tag: Element) => {
      if (tag instanceof HTMLElement) {
        const idInput = tag.querySelector(ProductCategoryMap.tagCategoryIdInput) as HTMLInputElement;

        categories.push({
          id: Number(idInput.value),
          name: this.extractCategoryName(tag as HTMLElement),
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

    const selectElement = this.categoriesContainer.querySelector(ProductCategoryMap.defaultCategorySelectInput) as HTMLElement;
    const defaultCategoryId = this.getDefaultCategoryId();
    selectElement.innerHTML = '';

    categories.forEach((category) => {
      const optionElement = document.createElement('option');
      optionElement.value = String(category.id);
      optionElement.innerHTML = category.name;
      optionElement.selected = category.id === defaultCategoryId;

      selectElement.append(optionElement);
    });
  }

  private listenDefaultCategorySelect(): void {
    this.defaultCategoryInput.addEventListener('change', (e) => {
      const currentTarget = e.currentTarget as HTMLInputElement;
      const newDefaultCategoryId = Number(currentTarget.value);
      const categories = this.collectCategories()
        .map((category) => ({...category, isDefault: category.id === newDefaultCategoryId}));

      this.tagsRenderer.render(categories, this.getDefaultCategoryId());
    });
  }

  private listenCategoryChanges(): void {
    this.eventEmitter.on(ProductEventMap.categories.categoriesUpdated, () => this.renderDefaultCategorySelection());
  }

  private getDefaultCategoryId(): number {
    return Number(this.defaultCategoryInput.value);
  }
}
