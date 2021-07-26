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

import AutoCompleteSearch from '@components/auto-complete-search';
import Tokenizers from '@components/bloodhound/tokenizers';
import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import {getCategories} from '@pages/product/services/categories';
import Tags from '@pages/product/components/categories/tags';

const {$} = window;

const ProductCategoryMap = ProductMap.categories;

export default class CategoryTreeSelector {
  constructor(eventEmitter) {
    this.eventEmitter = eventEmitter;
    this.categories = [];
    this.typeaheadDatas = [];

    return {
      showModal: (selectedCategoryIds, defaultCategoryId) => this.showModal(selectedCategoryIds, defaultCategoryId),
    };
  }

  showModal(selectedCategoryIds, defaultCategoryId) {
    this.selectedCategoryIds = selectedCategoryIds;
    this.defaultCategoryId = defaultCategoryId;
    const modalContent = $(ProductCategoryMap.categoriesModalTemplate);
    // @todo: replace fancybox with Modal after following PR is merged - https://github.com/PrestaShop/PrestaShop/pull/25184
    $.fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%',
      fitToView: false,
      autoSize: false,
      content: modalContent.html(),
      afterShow: () => {
        this.initCategories();
      },
    });
  }

  onApplyCategoryChanges() {
    this.modalContainer.querySelector(ProductCategoryMap.applyCategoriesBtn).addEventListener('click', () => {
      this.eventEmitter.emit(ProductEventMap.categories.applyCategoryTreeChanges, {
        categories: this.collectSelectedCategories(),
      });
      // @todo: close modal. ($.fancybox.close() not working)
    });
  }

  async initCategories() {
    this.modalContainer = document.querySelector(ProductCategoryMap.categoriesModalContainer);
    this.categoryTree = this.modalContainer.querySelector(ProductCategoryMap.categoryTree);
    this.prototypeTemplate = this.categoryTree.dataset.prototype;
    this.prototypeName = this.categoryTree.dataset.prototypeName;
    this.expandAllButton = this.modalContainer.querySelector(ProductCategoryMap.expandAllButton);
    this.reduceAllButton = this.modalContainer.querySelector(ProductCategoryMap.reduceAllButton);
    this.tags = new Tags(
      `${ProductCategoryMap.categoriesModalContainer} ${ProductCategoryMap.tagsContainer}`,
      this.selectedCategoryIds,
      true,
    );
    this.categories = await getCategories();

    this.initTypeaheadData(this.categories, '');
    this.initTypeahead();
    this.initTree();
    this.updateCategoriesTags();
  }

  initTree() {
    this.categories.forEach((category) => {
      const item = this.generateCategoryTree(category);
      this.categoryTree.append(item);
    });

    this.expandAllButton.addEventListener('click', () => {
      this.toggleAll(true);
    });
    this.reduceAllButton.addEventListener('click', () => {
      this.toggleAll(false);
    });

    this.categoryTree.querySelectorAll(ProductCategoryMap.checkboxInput).forEach((checkbox) => {
      const categoryId = Number(checkbox.dataset.id);

      if (this.selectedCategoryIds.some((category) => category.id === categoryId)) {
        checkbox.checked = true;
      }

      checkbox.addEventListener('change', () => this.updateCategoriesTags());
    }, this);
    // Tree is initialized we can show it and hide loader
    this.modalContainer
      .querySelector(ProductCategoryMap.fieldset)
      .classList.remove('d-none');
    this.modalContainer
      .querySelector(ProductCategoryMap.loader)
      .classList.add('d-none');
  }

  /**
   * Used to recursively create items of the category tree
   *
   * @param {Object} category
   */
  generateCategoryTree(category) {
    const categoryNode = this.generateTreeElement(category);
    const childrenList = categoryNode.querySelector(ProductCategoryMap.childrenList);
    childrenList.classList.add('d-none');

    const hasChildren = category.children && category.children.length > 0;
    categoryNode.classList.toggle('more', hasChildren);
    if (hasChildren) {
      const inputsContainer = categoryNode.querySelector(ProductCategoryMap.treeElementInputs);
      inputsContainer.addEventListener('click', (event) => {
        // We don't want to mess with the inputs behaviour (no toggle when checkbox or radio is clicked)
        // So we only toggle when the div itself is clicked.
        if (event.target !== event.currentTarget) {
          return;
        }

        const isExpanded = !childrenList.classList.contains('d-none');
        categoryNode.classList.toggle('less', !isExpanded);
        categoryNode.classList.toggle('more', isExpanded);
        childrenList.classList.toggle('d-none', isExpanded);
      });

      // Recursively build the children trees
      category.children.forEach((childCategory) => {
        const childTree = this.generateCategoryTree(childCategory);

        childrenList.append(childTree);
      });
    }

    return categoryNode;
  }

  /**
   * If the category is among the initial ones (inserted by the form on load) the existing element is used,
   * if not then it is generated based on the prototype template. In both case the element is injected with the
   * category name and click on radio is handled.
   *
   * @param {Object} category
   *
   * @returns {HTMLElement}
   */
  generateTreeElement(category) {
    const template = this.prototypeTemplate.replace(new RegExp(this.prototypeName, 'g'), category.id);
    // Trim is important here or the first child could be some text (whitespace, or \n)
    const frag = document.createRange().createContextualFragment(template.trim());
    const categoryNode = frag.firstChild;

    // Add category name as a text between the checkbox and the radio
    const checkboxInput = categoryNode.querySelector(ProductCategoryMap.checkboxInput);
    const nameelem = document.createTextNode(category.name);
    const elem = category.active ? nameelem : document.createElement('i').appendChild(nameelem).parentNode;
    checkboxInput.parentNode.insertBefore(
      elem,
      checkboxInput,
    );

    return categoryNode;
  }

  /**
   * Expand/reduce the category tree
   *
   * @param {boolean} expanded Force expanding instead of toggle
   */
  toggleAll(expanded) {
    this.expandAllButton.style.display = expanded ? 'none' : 'block';
    this.reduceAllButton.style.display = !expanded ? 'none' : 'block';

    this.modalContainer
      .querySelectorAll(ProductCategoryMap.childrenList)
      .forEach((e) => {
        e.classList.toggle('d-none', !expanded);
      });

    this.modalContainer
      .querySelectorAll(ProductCategoryMap.everyItems)
      .forEach((e) => {
        e.classList.toggle('more', !expanded);
        e.classList.toggle('less', expanded);
      });
  }

  /**
   * Check the selected category (matched by its ID) and toggle the tree by going up through the category's ancestors.
   *
   * @param {int} categoryId
   */
  selectCategory(categoryId) {
    const checkbox = this.modalContainer.querySelector(
      `[name="${ProductCategoryMap.checkboxName(categoryId)}"]`,
    );

    if (!checkbox) {
      return;
    }
    this.updateCheckbox(checkbox, true);
    this.openCategoryParents(checkbox);
    this.updateCategoriesTags();
  }

  /**
   * @param {HTMLElement} checkbox
   */
  openCategoryParents(checkbox) {
    // This is the element containing the checkbox
    let parentItem = checkbox.closest(ProductCategoryMap.treeElement);

    if (parentItem !== null) {
      // This is the first (potential) parent element
      parentItem = parentItem.parentNode.closest(ProductCategoryMap.treeElement);
    }

    while (parentItem !== null && this.categoryTree.contains(parentItem)) {
      const childrenList = parentItem.querySelector(ProductCategoryMap.childrenList);

      if (childrenList.childNodes.length) {
        parentItem.classList.add('less');
        parentItem.classList.remove('more');
        parentItem.querySelector(ProductCategoryMap.childrenList).classList.remove('d-none');
      }

      parentItem = parentItem.parentNode.closest(ProductCategoryMap.treeElement);
    }
  }

  /**
   * @param {int} categoryId
   */
  unselectCategory(categoryId) {
    const checkbox = this.modalContainer.querySelector(
      `[name="${ProductCategoryMap.treeCheckboxName(categoryId)}"]`,
    );

    if (!checkbox) {
      return;
    }
    this.updateCheckbox(checkbox, false);
    this.openCategoryParents(checkbox);
    this.updateCategoriesTags();
  }

  /**
   * Typeahead data require to have only one array level, we also build the breadcrumb as we go through the
   * categories.
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
      datumTokenizer: Tokenizers.obj.letters(
        'breadcrumb',
      ),
      queryTokenizer: Bloodhound.tokenizers.nonword,
      local: this.typeaheadDatas,
    });

    const dataSetConfig = {
      source,
      display: 'breadcrumb',
      value: 'id',
      onSelect: (selectedItem, e, $searchInput) => {
        this.selectCategory(selectedItem.id);

        // This resets the search input or else previous search is cached and can be added again
        $searchInput.typeahead('val', '');
      },
    };

    new AutoCompleteSearch($(ProductCategoryMap.searchInput), dataSetConfig);
  }

  updateCategoriesTags() {
    const checkedCheckboxes = this.categoryTree.querySelectorAll(ProductCategoryMap.checkedCheckboxInputs);

    const categories = [];
    checkedCheckboxes.forEach((checkboxInput) => {
      const categoryId = Number(checkboxInput.dataset.id);
      const category = this.getCategoryById(categoryId);

      if (!category) {
        return;
      }

      categories.push(category);
    });
    this.tags.refresh(categories);
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
   * @param {boolean} checked
   */
  updateCheckbox(checkboxInput, checked) {
    if (checkboxInput.checked !== checked) {
      checkboxInput.checked = checked;
      this.eventEmitter.emit(ProductEventMap.updateSubmitButtonState);
    }
  }

  collectSelectedCategories() {
    const tags = this.modalContainer
      .querySelector(ProductCategoryMap.tagsContainer)
      .querySelectorAll(ProductCategoryMap.tagItem);

    const categories = [];
    tags.forEach((tagItem) => {
      categories.push({
        id: tagItem.dataset.id,
        name: tagItem.innerHTML,
      });
    });

    return categories;
  }
}
