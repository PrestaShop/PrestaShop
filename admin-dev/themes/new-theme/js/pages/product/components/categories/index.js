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

import Bloodhound from 'typeahead.js';
import _ from 'lodash';

import AutoCompleteSearch from '@components/auto-complete-search';
import Tokenizers from '@components/bloodhound/tokenizers';
import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import {getCategories} from '@pages/product/services/categories';
import CategoryTreeSelector from '@pages/product/components/categories/category-tree-selector';

const {$} = window;

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
    this.categories = [];
    this.typeaheadDatas = [];
    this.categoryTreeSelector = new CategoryTreeSelector(eventEmitter);

    this.addCategoriesBtn.addEventListener('click', () => this.categoryTreeSelector.showModal(
      this.collectCategoryIdsFromTags(),
    ));
    this.listenCategoryTreeChanges();

    return {};
  }

  listenCategoryTreeChanges() {
    this.eventEmitter.on(ProductEventMap.categories.applyCategoryTreeChanges, (eventData) => {
      this.updateCategories(eventData.categories);
    });
  }

  collectCategoryIdsFromTags() {
    const tags = this.categoriesContainer.querySelector(ProductCategoryMap.tagsContainer)
      .querySelectorAll(ProductCategoryMap.tagItem);

    const categoryIds = [];
    tags.forEach((tag) => {
      categoryIds.push(tag.dataset.id);
    });

    return categoryIds;
  }

  getDefaultCategoryId() {
    //@todo: default category will have to be retrieved from dedicated input when its implemented
    return this.collectCategoryIdsFromTags()[0];
  }

  updateCategories(categories) {
    const tagsContainer = this.categoriesContainer.querySelector(ProductCategoryMap.tagsContainer);
    tagsContainer.innerHTML = '';

    const tagTemplate = tagsContainer.dataset.prototype;

    categories.forEach((category) => {
      const template = tagTemplate.replace(RegExp(tagsContainer.dataset.prototypeName, 'g'), category.id);
      const frag = document.createRange().createContextualFragment(template.trim());
      frag.firstChild.querySelector(ProductCategoryMap.tagItem).innerHTML = category.name;
      tagsContainer.append(frag);
    });
  }
}
