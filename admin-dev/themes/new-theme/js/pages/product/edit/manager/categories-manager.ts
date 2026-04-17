/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import CategoryTreeSelector from '@pages/product/category/category-tree-selector';
import TagsRenderer from '@pages/product/category/tags-renderer';
import {EventEmitter} from 'events';
import {Category} from '@pages/product/category/types';

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
    const categoriesContainer = document.querySelector<HTMLElement>(ProductCategoryMap.categoriesContainer);

    if (!categoriesContainer) {
      throw new Error(`Failed to find essential element to run categories manager: ${ProductCategoryMap.categoriesContainer}.`);
    }
    this.categoriesContainer = categoriesContainer;

    const addCategoriesBtn = this.categoriesContainer.querySelector<HTMLElement>(ProductCategoryMap.addCategoriesBtn);
    const defaultCategoryInput = this.categoriesContainer
      .querySelector<HTMLInputElement>(ProductCategoryMap.defaultCategorySelectInput);

    if (!addCategoriesBtn || !defaultCategoryInput) {
      throw new Error('Failed to find some essential elements to run categories manager.');
    }

    this.addCategoriesBtn = addCategoriesBtn;
    this.defaultCategoryInput = defaultCategoryInput;

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
      this.tagsRenderer.render(eventData.categories);
      this.eventEmitter.emit(ProductEventMap.categories.categoriesUpdated);
    });
  }

  private collectCategories(): Array<Category> {
    // these are at first rendered on page load and later updated dynamically
    const tagsContainer = this.categoriesContainer.querySelector<HTMLElement>(ProductCategoryMap.tagsContainer);

    if (!tagsContainer) {
      throw new Error(`Essential element was not found for categories manager: ${ProductCategoryMap.tagsContainer}`);
    }

    const tags = tagsContainer.querySelectorAll(ProductCategoryMap.tagItem);
    const categories: Array<Category> = [];

    tags.forEach((tag: Element) => {
      if (tag instanceof HTMLElement) {
        const idInput = tag.querySelector<HTMLInputElement>(ProductCategoryMap.tagCategoryIdInput);

        if (idInput instanceof HTMLInputElement) {
          categories.push({
            id: Number(idInput.value),
            name: this.extractCategoryName(tag),
            displayName: this.extractCategoryPreview(tag),
          });
        } else {
          console.error(`Element ${ProductCategoryMap.tagCategoryIdInput} expected to be HTMLInputElement`);
        }
      }
    });

    return categories;
  }

  private extractCategoryPreview(tag: HTMLElement): string {
    const tagPreviewElement = tag.querySelector<HTMLElement>(ProductCategoryMap.categoryNamePreview);

    if (tagPreviewElement) {
      return tagPreviewElement.innerText;
    }

    return '';
  }

  private extractCategoryName(tag: HTMLElement): string {
    const tagNameInput = tag.querySelector<HTMLInputElement>(ProductCategoryMap.categoryNameInput);

    if (tagNameInput) {
      return tagNameInput.value;
    }

    return '';
  }

  private renderDefaultCategorySelection(): void {
    const categories = this.collectCategories();

    const selectElement = this.categoriesContainer.querySelector<HTMLElement>(ProductCategoryMap.defaultCategorySelectInput);

    if (!selectElement) {
      console.error(`${ProductCategoryMap.defaultCategorySelectInput} element was not found.`);

      return;
    }

    const defaultCategoryId = this.getDefaultCategoryId();
    selectElement.innerHTML = '';

    categories.forEach((category) => {
      const optionElement = document.createElement('option');
      optionElement.value = String(category.id);
      optionElement.innerHTML = category.displayName;
      optionElement.selected = category.id === defaultCategoryId;

      selectElement.append(optionElement);
    });
  }

  private listenDefaultCategorySelect(): void {
    $(`#${this.defaultCategoryInput.id}`).on('change', (e) => {
      const {currentTarget} = e;

      if (!(currentTarget instanceof HTMLSelectElement)) {
        console.error('currentTarget expected to be HTMLSelectElement');

        return;
      }

      const newDefaultCategoryId = Number(currentTarget.value);
      const categories = this.collectCategories()
        .map((category) => ({...category, isDefault: category.id === newDefaultCategoryId}));
      this.tagsRenderer.render(categories);
    });
  }

  private listenCategoryChanges(): void {
    this.eventEmitter.on(ProductEventMap.categories.categoriesUpdated, () => {
      this.renderDefaultCategorySelection();
      // if there is only one category left selected, it must be re-rendered without the removal element
      this.tagsRenderer.render(this.collectCategories());
    });
  }

  private getDefaultCategoryId(): number {
    return Number(this.defaultCategoryInput.value);
  }
}
