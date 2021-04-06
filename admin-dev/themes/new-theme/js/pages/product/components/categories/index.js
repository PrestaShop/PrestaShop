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
import Router from '@components/router';
import ProductEventMap from '@pages/product/product-event-map';
import {getCategories} from '@pages/product/services/categories';

const {$} = window;

export default class CategoriesManager {
  /**
   * @param eventEmitter {EventEmitter}
   */
  constructor() {
    this.router = new Router();
    this.initCategories();
    this.categoriesElement = document.querySelector('.js-categories-tree');
    this.treeContainer = document.querySelector('.js-category-tree-overflow');
    this.datas = [];
    this.categoryTree = null;
    this.actionButton = document.querySelector('.js-categories-tree-actions');
    this.expanded = false;

    return {};
  }

  async initCategories() {
    this.datas = await getCategories(1);

    this.initTree();
  }

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

    this.categoriesElement.querySelector('fieldset').classList.remove('hide');
    this.categoriesElement.querySelector('.categories-tree-loader').classList.add('hide');
  }

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
        if (!event.target.classList.contains('default-category')) {
          childList.classList.toggle('hide');
        }

        if (childList.classList.contains('hide')) {
          checkboxContainer.parentElement.classList.remove('less');
          checkboxContainer.parentElement.classList.add('more');
        } else {
          checkboxContainer.parentElement.classList.remove('more');
          checkboxContainer.parentElement.classList.add('less');
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

  toggleExpand(force) {
    const currentAction = this.actionButton.querySelector('.form-control-label:not(.hide)');
    const nextAction = this.actionButton.querySelector('.hide');
    nextAction.classList.remove('hide');
    nextAction.style.display = 'block';
    currentAction.classList.add('hide');
    currentAction.style.display = 'none';

    this.categoriesElement.querySelectorAll('.child-list').forEach((e) => {
      if (this.expanded && !force) {
        e.classList.add('hide');
      } else {
        e.classList.remove('hide');
      }
    });

    this.categoriesElement.querySelectorAll('.less, .more').forEach((e) => {
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
}
