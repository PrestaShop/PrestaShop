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
    this.categoriesContainer = document.querySelector(ProductCategoryMap.categoriesContainer);
    this.addCategoriesBtn = this.categoriesContainer.querySelector(
      ProductCategoryMap.addCategoriesBtn,
    );
    this.categories = this.collectCategoryIdsFromTags();
    this.typeaheadDatas = [];
    this.categoryTreeSelector = new CategoryTreeSelector(eventEmitter);

    this.addCategoriesBtn.addEventListener('click', () => this.categoryTreeSelector.showModal(
      this.collectCategoryIdsFromTags(),
      this.getDefaultCategoryId(),
    ));
    this.tags = new Tags(
      `${ProductCategoryMap.categoriesContainer} ${ProductCategoryMap.tagsContainer}`,
      this.categories,
      this.getDefaultCategoryId(),
      () => {},
    );
    this.listenCategoryTreeChanges();

    return {};
  }

  listenCategoryTreeChanges() {
    this.eventEmitter.on(ProductEventMap.categories.applyCategoryTreeChanges, (eventData) => {
      this.tags.refresh(eventData.categories);
    });
  }

  collectCategoryIdsFromTags() {
    const tags = this.categoriesContainer.querySelector(ProductCategoryMap.tagsContainer)
      .querySelectorAll(ProductCategoryMap.tagItem);
    const categories = [];

    tags.forEach((tag) => {
      categories.push({
        id: Number(tag.dataset.id),
        name: tag.querySelector(ProductCategoryMap.categoryNamePreview).firstChild.data,
      });
    });

    return categories;
  }

  getDefaultCategoryId() {
    // @todo: default category will have to be retrieved from dedicated input when its implemented
    return 2;
  }
}
