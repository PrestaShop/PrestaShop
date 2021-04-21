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
import {getCategories} from '@pages/product/services/categories';
import Bloodhound from 'typeahead.js';
import AutoCompleteSearch from '@components/auto-complete-search';

const {$} = window;

const ProductCategoryMap = ProductMap.categories;

export default class CategoriesManager {
  constructor() {
    this.initCategories();
    this.categoriesContainer = document.querySelector(
      ProductCategoryMap.categoriesContainer,
    );
    this.categories = [];
    this.typeaheadDatas = [];
    this.categoryTree = this.categoriesContainer.querySelector(ProductCategoryMap.categoryTree);
    this.prototypeTemplate = this.categoryTree.dataset.prototype;
    this.prototypeName = this.categoryTree.dataset.prototypeName;
    this.actionButton = document.querySelector(ProductCategoryMap.actions);
    this.searchInput = $(ProductCategoryMap.searchInput);
    this.expanded = false;

    return {};
  }

  async initCategories() {
    this.categories = await getCategories();

    this.initTypeaheadData(this.categories, '');
    this.initTypeahead();
    this.initTree();
  }

  /**
   * Init the category tree element
   */
  initTree() {
    const initialElements = {};

    let regexpString = ProductCategoryMap.checkboxName('__REGEXP__');
    regexpString = regexpString.replaceAll('[', '\\[');
    regexpString = regexpString.replaceAll(']', '\\]');
    regexpString = regexpString.replace('__REGEXP__', '([0-9]+)');
    const categoryIdRegexp = new RegExp(regexpString);

    this.categoryTree.querySelectorAll(ProductCategoryMap.categoryTreeElement).forEach((treeElement) => {
      const checkboxInput = treeElement.querySelector(ProductCategoryMap.checkboxInput);
      const matches = checkboxInput.name.match(categoryIdRegexp);
      const categoryId = Number(matches[1]);
      initialElements[categoryId] = treeElement;
    });
    console.log('initialElements', initialElements);

    this.categories.forEach((category) => {
      const item = this.generateCategoryTree(category, initialElements);
      this.categoryTree.append(item);
    });

    this.actionButton.addEventListener('click', () => {
      this.toggleExpand();
    });

    // Tree is initialized we can show it and hide loader
    this.categoriesContainer
      .querySelector(ProductCategoryMap.fieldset)
      .classList.remove('d-none');
    this.categoriesContainer
      .querySelector(ProductCategoryMap.loader)
      .classList.add('d-none');
  }

  /**
   * @param {Object} category
   * @param {Object} initialElements
   *
   * Used to recursively create items of the category tree
   */
  generateCategoryTree(category, initialElements) {
    const hasChildren = category.children && category.children.length > 0;
    const categoryNode = this.generateCategoryNode(category, initialElements);
    categoryNode.classList.toggle('more', hasChildren);

    const inputsContainer = categoryNode.querySelector(ProductCategoryMap.categoryTreeInputs);
    const childrenList = categoryNode.querySelector(ProductCategoryMap.childrenList);
    childrenList.classList.add('d-none', 'childrenList');
    if (hasChildren) {
      inputsContainer.addEventListener('click', (event) => {
        if (
          !event.target.classList.contains('default-category')
          && !event.target.classList.contains('category')
        ) {
          childrenList.classList.toggle('d-none');

          if (childrenList.classList.contains('d-none')) {
            inputsContainer.parentElement.classList.remove('less');
            inputsContainer.parentElement.classList.add('more');
          } else {
            inputsContainer.parentElement.classList.remove('more');
            inputsContainer.parentElement.classList.add('less');
          }
        }
      });

      category.children.forEach((childCategory) => {
        const childTree = this.generateCategoryTree(childCategory, initialElements);

        childrenList.append(childTree);
      });
    }

    return categoryNode;
  }

  /**
   * @param {Object} category
   * @param {Object} initialElements
   *
   * @returns {ChildNode}
   */
  generateCategoryNode(category, initialElements) {
    let categoryNode;

    if (!Object.prototype.hasOwnProperty.call(initialElements, category.id)) {
      const template = this.prototypeTemplate.replace(new RegExp(this.prototypeName, 'g'), category.id);
      const frag = document.createRange().createContextualFragment(template);
      categoryNode = frag.querySelector('li');
    } else {
      categoryNode = initialElements[category.id];
    }
    const radioInput = categoryNode.querySelector(ProductCategoryMap.radioInput);
    radioInput.parentNode.insertBefore(
      document.createTextNode(category.name),
      radioInput,
    );

    return categoryNode;
  }

  /**
   * @param {boolean} force Force expanding instead of toggle
   *
   * Expand the category tree
   */
  toggleExpand(force) {
    const currentAction = this.actionButton.querySelector(
      ProductCategoryMap.currentAction,
    );
    const nextAction = this.actionButton.querySelector(
      ProductCategoryMap.nextAction,
    );
    nextAction.classList.remove('d-none');
    nextAction.style.display = 'block';
    currentAction.classList.add('d-none');
    currentAction.style.display = 'none';

    this.categoriesContainer
      .querySelectorAll(ProductCategoryMap.childrenList)
      .forEach((e) => {
        if (this.expanded && !force) {
          e.classList.add('d-none');
        } else {
          e.classList.remove('d-none');
        }
      });

    this.categoriesContainer
      .querySelectorAll(ProductCategoryMap.everyItems)
      .forEach((e) => {
        if (this.expanded && !force) {
          e.classList.add('more');
          e.classList.remove('less');
        } else {
          e.classList.add('less');
          e.classList.remove('more');
        }
      });

    this.expanded = !this.expanded;
  }

  /**
   * Typeahead datas require to have only one array level
   */
  initTypeaheadData(data, parentBreadcrumb) {
    data.forEach((category) => {
      category.breadcrumb = parentBreadcrumb ? `${parentBreadcrumb} > ${category.name}` : category.name;
      this.typeaheadDatas.push(category);

      if (category.children) {
        this.initTypeaheadData(category.children, category.breadcrumb);
      }
    });
  }

  initTypeahead() {
    const that = this;

    const source = new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.obj.whitespace(
        'name',
        'value',
        'color',
        'group_name',
      ),
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      local: this.typeaheadDatas,
    });

    const dataSetConfig = {
      source,
      display: 'breadcrumb',
      value: 'id',
      onSelect(selectedItem) {
        const checkbox = document.querySelector(
          ProductCategoryMap.itemCheckbox(selectedItem.id),
        );
        checkbox.checked = true;
        that.toggleExpand(true);
      },
      onClose() {
        that.searchInput.val('');
        return true;
      },
    };

    dataSetConfig.templates = {
      suggestion: (item) => {
        let displaySuggestion = item;

        if (typeof dataSetConfig.display === 'function') {
          dataSetConfig.display(item);
        } else if (
          Object.prototype.hasOwnProperty.call(item, dataSetConfig.display)
        ) {
          displaySuggestion = item[dataSetConfig.display];
        }

        return `<div class="px-2">${displaySuggestion}</div>`;
      },
    };

    new AutoCompleteSearch(this.searchInput, dataSetConfig);
  }
}
