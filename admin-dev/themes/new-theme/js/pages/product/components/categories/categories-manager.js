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

const ProductCategoryMap = ProductMap.categories;

export default class CategoriesManager {
  /**
   * @param {EventEmitter} eventEmitter
   * @returns {{}}
   */
  constructor(eventEmitter) {
    this.eventEmitter = eventEmitter;
    this.categoryTreeSelector = new CategoryTreeSelector(eventEmitter);
    this.categoriesContainer = document.querySelector(ProductCategoryMap.categoriesContainer);
    this.addCategoriesBtn = this.categoriesContainer.querySelector(ProductCategoryMap.addCategoriesBtn);
    this.typeaheadDatas = [];
    this.tags = new Tags(
      eventEmitter,
      `${ProductCategoryMap.categoriesContainer} ${ProductCategoryMap.tagsContainer}`,
    );
    this.tags.update(this.collectCategories());
    this.renderDefaultCategorySelection();
    this.listenCategoryChanges();
    this.listenDefaultCategorySelect();
    this.initCategoryTreeModal();

    return {};
  }

  initCategoryTreeModal() {
    this.addCategoriesBtn.addEventListener('click', () => this.categoryTreeSelector.showModal(
      this.collectCategories(),
    ));
    this.eventEmitter.on(ProductEventMap.categories.applyCategoryTreeChanges, (eventData) => {
      this.tags.update(eventData.categories);
    });
  }

  /**
   * Collects categories from tags
   *
   * @returns {[]}
   */
  collectCategories() {
    // these are at first rendered on page load and later updated dynamically
    const tags = this.categoriesContainer.querySelector(ProductCategoryMap.tagsContainer)
      .querySelectorAll(ProductCategoryMap.tagItem);
    const categories = [];

    tags.forEach((tag) => {
      categories.push({
        id: Number(tag.dataset.id),
        name: tag.querySelector(ProductCategoryMap.categoryNamePreview).firstChild.data,
        //@todo: move to map
        isDefault: tag.querySelector('.is_default_category_checkbox').checked,
      });
    });

    return categories;
  }

  getDefaultCategoryId() {
    const checkedDefaultCategory = this.categoriesContainer
      .querySelector(ProductCategoryMap.tagsContainer)
      .querySelector('.is_default_category_checkbox:checked');

    return Number(checkedDefaultCategory.dataset.id);
  }

  renderDefaultCategorySelection() {
    const categories = this.collectCategories();
    //@todo: move selectors to map
    const selectContainer = this.categoriesContainer.querySelector('#default-category-selector-widget');
    const selectElement = this.categoriesContainer.querySelector('#default-category-id');
    selectElement.innerHTML = '';

    categories.forEach((category) => {
      const optionElement = document.createElement('option');
      optionElement.value = category.id;
      optionElement.innerHTML = category.name;
      optionElement.selected = category.isDefault;

      selectElement.append(optionElement);
    });

    selectContainer.classList.remove('d-none');
  }

  listenDefaultCategorySelect() {
    this.categoriesContainer.querySelector('#default-category-id')
      .addEventListener('change', (e) => {
        const newDefaultCategoryId = Number(e.currentTarget.value);
        const categories = this.collectCategories();
        categories.forEach((category) => {
          category.isDefault = category.id === newDefaultCategoryId;
        });
        this.tags.update(categories);
      });
  }

  listenCategoryChanges() {
    this.eventEmitter.on(ProductEventMap.categories.categoryRemoved, () => this.renderDefaultCategorySelection());
    this.eventEmitter.on(ProductEventMap.categories.categoriesUpdated, () => this.renderDefaultCategorySelection());
  }
}
