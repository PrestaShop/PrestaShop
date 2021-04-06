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
  /**
   * @param eventEmitter {EventEmitter}
   */
  constructor() {
    this.initCategories();
    this.categoriesElement = document.querySelector(
      ProductCategoryMap.categoryTree,
    );
    this.treeContainer = document.querySelector(ProductCategoryMap.overflow);
    this.datas = [];
    this.typeaheadDatas = [];
    this.categoryTree = null;
    this.actionButton = document.querySelector(ProductCategoryMap.actions);
    this.searchInput = $(ProductCategoryMap.searchInput);
    this.expanded = false;

    return {};
  }

  async initCategories() {
    this.datas = await getCategories(1);

    this.initTypeaheadDatas(this.datas);
    this.initTypeahead();
    this.initTree();
  }

  /**
   * Init the category tree element
   */
  initTree() {
    this.categoryTree = document.createElement('ul');
    this.categoryTree.classList.add('category-tree');

    this.datas.forEach((category) => {
      const item = this.createItem(category);
      this.categoryTree.append(item);
    });

    this.treeContainer.append(this.categoryTree);

    this.actionButton.addEventListener('click', () => {
      this.toggleExpand();
    });

    this.categoriesElement
      .querySelector(ProductCategoryMap.fieldset)
      .classList.remove('hide');
    this.categoriesElement
      .querySelector(ProductCategoryMap.loader)
      .classList.add('hide');
  }

  /**
   * @param category {object}
   *
   * Used to recursively create items of the category tree
   */
  createItem(category) {
    const listItem = document.createElement('li');
    const hasChilds = category.childs && category.childs.length > 0;

    const checkboxContainer = document.createElement('div');
    checkboxContainer.classList.add('checkbox');

    if (hasChilds) {
      listItem.classList.add('more');
    }

    const labelElement = document.createElement('label');

    const inputElement = document.createElement('input');
    inputElement.setAttribute('type', 'checkbox');
    inputElement.setAttribute('name', 'form[step1][categories][tree][]');
    inputElement.setAttribute('value', category.id);
    inputElement.classList.add('category');

    const radioElement = document.createElement('input');
    radioElement.setAttribute('type', 'radio');
    radioElement.setAttribute('name', 'ignore');
    radioElement.setAttribute('value', category.id);
    radioElement.classList.add('default-category');

    labelElement.append(inputElement, ` ${category.name}`, radioElement);
    checkboxContainer.append(labelElement);
    listItem.append(checkboxContainer);

    if (hasChilds) {
      const childList = document.createElement('ul');
      childList.classList.add('hide', 'child-list');

      checkboxContainer.addEventListener('click', (event) => {
        if (
          !event.target.classList.contains('default-category')
          && !event.target.classList.contains('category')
        ) {
          childList.classList.toggle('hide');

          if (childList.classList.contains('hide')) {
            checkboxContainer.parentElement.classList.remove('less');
            checkboxContainer.parentElement.classList.add('more');
          } else {
            checkboxContainer.parentElement.classList.remove('more');
            checkboxContainer.parentElement.classList.add('less');
          }
        }
      });

      category.childs.forEach((element) => {
        const child = this.createItem(element);

        childList.append(child);
      });

      listItem.append(childList);
    }

    return listItem;
  }

  /**
   * @param force {boolean} Force expanding instead of toggle
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
    nextAction.classList.remove('hide');
    nextAction.style.display = 'block';
    currentAction.classList.add('hide');
    currentAction.style.display = 'none';

    this.categoriesElement
      .querySelectorAll(ProductCategoryMap.childList)
      .forEach((e) => {
        if (this.expanded && !force) {
          e.classList.add('hide');
        } else {
          e.classList.remove('hide');
        }
      });

    this.categoriesElement
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
  initTypeaheadDatas(datas) {
    datas.forEach((item) => {
      this.typeaheadDatas.push(item);

      if (item.childs) {
        this.initTypeaheadDatas(item.childs);
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
