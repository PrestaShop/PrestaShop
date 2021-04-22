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
    this.expandAllButton = this.categoriesContainer.querySelector(ProductCategoryMap.expandAllButton);
    this.reduceAllButton = this.categoriesContainer.querySelector(ProductCategoryMap.reduceAllButton);
    this.searchInput = $(ProductCategoryMap.searchInput);

    return {};
  }

  async initCategories() {
    this.categories = await getCategories();

    // This regexp is gonna be used to get id from checkbox name
    let regexpString = ProductCategoryMap.checkboxName('__REGEXP__');
    regexpString = regexpString.replaceAll('[', '\\[');
    regexpString = regexpString.replaceAll(']', '\\]');
    regexpString = regexpString.replace('__REGEXP__', '([0-9]+)');
    this.categoryIdRegexp = new RegExp(regexpString);

    this.initTypeaheadData(this.categories, '');
    this.initTypeahead();
    this.initTree();
    this.updateCategoriesTags();
  }

  /**
   * Init the category tree element
   */
  initTree() {
    const initialElements = {};

    this.categoryTree.querySelectorAll(ProductCategoryMap.categoryTreeElement).forEach((treeElement) => {
      const checkboxInput = treeElement.querySelector(ProductCategoryMap.checkboxInput);
      const categoryId = this.getIdFromCheckbox(checkboxInput);
      initialElements[categoryId] = treeElement;
    });

    this.categories.forEach((category) => {
      const item = this.generateCategoryTree(category, initialElements);
      this.categoryTree.append(item);
    });

    this.expandAllButton.addEventListener('click', () => {
      this.toggleAll(true);
    });
    this.reduceAllButton.addEventListener('click', () => {
      this.toggleAll(false);
    });

    this.categoryTree.querySelectorAll(ProductCategoryMap.checkboxInput).forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        this.updateCategoriesTags();
      });
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
        // We don't want to mess with the inputs behaviour
        if (event.target !== event.currentTarget) {
          return;
        }
        const treeElement = event.currentTarget;
        const isExpanded = !childrenList.classList.contains('d-none');
        treeElement.classList.toggle('less', !isExpanded);
        treeElement.classList.toggle('more', isExpanded);
        childrenList.classList.toggle('d-none', isExpanded);
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
      // Trim is important here or the first child could be some text (whitespace, or \n)
      const frag = document.createRange().createContextualFragment(template.trim());
      categoryNode = frag.firstChild;
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
   * @param {boolean} expanded Force expanding instead of toggle
   *
   * Expand the category tree
   */
  toggleAll(expanded) {
    this.expandAllButton.style.display = expanded ? 'none' : 'block';
    this.reduceAllButton.style.display = !expanded ? 'none' : 'block';

    this.categoriesContainer
      .querySelectorAll(ProductCategoryMap.childrenList)
      .forEach((e) => {
        e.classList.toggle('d-none', !expanded);
      });

    this.categoriesContainer
      .querySelectorAll(ProductCategoryMap.everyItems)
      .forEach((e) => {
        e.classList.toggle('more', expanded);
        e.classList.toggle('less', !expanded);
      });
  }

  /**
   * @param {int} categoryId
   */
  toggleCategory(categoryId) {
    const checkbox = this.categoriesContainer.querySelector(
      `[name="${ProductCategoryMap.checkboxName(categoryId)}"]`,
    );

    // This is the element containing the checkbox
    let parentItem = checkbox.closest(ProductCategoryMap.categoryTreeElement);

    if (parentItem !== null) {
      // This is the first (potential) parent element
      parentItem = parentItem.parentNode.closest(ProductCategoryMap.categoryTreeElement);
    }

    while (parentItem !== null && this.categoryTree.contains(parentItem)) {
      const childrenList = parentItem.querySelector(ProductCategoryMap.childrenList);

      if (childrenList.childNodes.length) {
        parentItem.classList.add('less');
        parentItem.classList.remove('more');
        parentItem.querySelector(ProductCategoryMap.childrenList).classList.remove('d-none');
      }

      parentItem = parentItem.parentNode.closest(ProductCategoryMap.categoryTreeElement);
    }
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
      onSelect: (selectedItem, e, $searchInput) => {
        const checkbox = this.categoriesContainer.querySelector(
          `[name="${ProductCategoryMap.checkboxName(selectedItem.id)}"]`,
        );
        checkbox.checked = true;
        this.toggleCategory(selectedItem.id);

        // This resets the search input or else previous search is cached and can be added again
        $searchInput.typeahead('val', '');
      },
      onClose: (event, $searchInput) => {
        $searchInput.typeahead('val', '');
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

  updateCategoriesTags() {
    const checkedCheckboxes = this.categoryTree.querySelectorAll(ProductCategoryMap.checkedCheckboxInputs);
    const tagsContainer = this.categoriesContainer.querySelector(ProductCategoryMap.tagsContainer);
    tagsContainer.innerHTML = '';

    checkedCheckboxes.forEach((checkboxInput) => {
      const categoryId = this.getIdFromCheckbox(checkboxInput);
      const category = this.getCategoryById(categoryId);
      const template = `
        <span class="pstaggerTag">
            <span data-id="${category.id}" title="${category.breadcrumb}">${category.name}</span>
            <a class="pstaggerClosingCross" href="#" data-id="${category.id}">x</a>
        </span>
      `;

      const frag = document.createRange().createContextualFragment(template.trim());
      tagsContainer.append(frag.firstChild);
    });

    tagsContainer.querySelectorAll('.pstaggerClosingCross').forEach((closeLink) => {
      closeLink.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopImmediatePropagation();
        const categoryId = event.currentTarget.dataset.id;
        const checkbox = this.categoriesContainer.querySelector(
          `[name="${ProductCategoryMap.checkboxName(categoryId)}"]`,
        );
        checkbox.checked = false;
        this.updateCategoriesTags();
      });
    });

    tagsContainer.classList.toggle('d-block', checkedCheckboxes.length > 0);
  }

  /**
   * @param {int} categoryId
   *
   * @returns {Object|null}
   */
  getCategoryById(categoryId) {
    return this.searchCategory(categoryId, this.categories);
  }

  /**
   * @param {int} categoryId
   * @param {array} categories
   * @returns {Object|null}
   */
  searchCategory(categoryId, categories) {
    let searchedCategory = null;
    categories.forEach((category) => {
      if (categoryId === category.id) {
        searchedCategory = category;
      }

      if (searchedCategory === null && category.children && category.children.length > 0) {
        searchedCategory = this.searchCategory(categoryId, category.children);
      }
    });

    return searchedCategory;
  }

  /**
   * @param {HTMLElement} checkboxInput
   *
   * @returns {number}
   */
  getIdFromCheckbox(checkboxInput) {
    const matches = checkboxInput.name.match(this.categoryIdRegexp);

    return Number(matches[1]);
  }
}
