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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

// @ts-ignore-next-line
import Bloodhound from 'typeahead.js';
import AutoCompleteSearch, {InputAutoCompleteSearchConfig} from '@components/auto-complete-search';
import Tokenizers from '@components/bloodhound/tokenizers';
import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import {getCategories} from '@pages/product/service/category';
import TagsRenderer from '@pages/product/category/tags-renderer';
import {EventEmitter} from 'events';
import Modal, {ModalType} from '@components/modal/modal';
import {Category, TreeCategory} from '@pages/product/category/types';

const {$} = window;

const ProductCategoryMap = ProductMap.categories;

export default class CategoryTreeSelector {
  eventEmitter: EventEmitter;

  selectedCategories: Array<Category>;

  treeCategories: Array<TreeCategory>;

  typeaheadCategories: Array<Category>;

  defaultCategoryId: number;

  modalContentContainer: HTMLElement|null;

  modal: ModalType|null;

  categoryTree: HTMLElement|null;

  tagsRenderer: TagsRenderer|null;

  constructor(eventEmitter: EventEmitter) {
    this.eventEmitter = eventEmitter;
    this.selectedCategories = [];
    this.typeaheadCategories = [];
    this.treeCategories = [];
    this.defaultCategoryId = 0;
    this.modalContentContainer = null;
    this.modal = null;
    this.categoryTree = null;
    this.tagsRenderer = null;
  }

  public showModal(selectedCategories: Array<Category>, defaultCategoryId: number): void {
    if (!defaultCategoryId) {
      console.error('Default category id is invalid.');

      return;
    }

    this.selectedCategories = selectedCategories;
    this.defaultCategoryId = defaultCategoryId;

    const modalContent = $(ProductCategoryMap.categoriesModalTemplate);
    const modal = new Modal({
      id: ProductCategoryMap.categoriesModalId,
      dialogStyle: {
        maxWidth: '90%',
      },
    });

    modal.render(modalContent.html());

    try {
      this.initModal();
    } catch (e: any) {
      // catch and log fatal errors to avoid breaking other components
      console.error('Category tree selector component stopped working due to fatal error.');
      return;
    }

    modal.show();
    this.modal = modal;
  }

  private async initModal(): Promise<void> {
    const modalContentContainer = document.querySelector<HTMLElement>(ProductCategoryMap.modalContentContainer);

    if (!modalContentContainer) {
      throw new Error(`Essential element ${ProductCategoryMap.modalContentContainer} was not found.`);
    }

    this.modalContentContainer = modalContentContainer;
    this.categoryTree = this.modalContentContainer.querySelector<HTMLElement>(ProductCategoryMap.categoryTree);
    this.tagsRenderer = new TagsRenderer(
      this.eventEmitter,
      `${ProductCategoryMap.modalContentContainer} ${ProductCategoryMap.tagsContainer}`,
      ProductEventMap.categories.tagRemoved,
    );
    this.tagsRenderer.render(this.selectedCategories);
    this.treeCategories = await getCategories();

    this.initTypeaheadData(this.treeCategories);
    this.initTypeahead();
    this.initTree();
    this.listenCancelChanges();
    this.listenApplyChanges();
    this.eventEmitter.on(ProductEventMap.categories.tagRemoved, (categoryId) => this.updateCategory(categoryId, false));
  }

  private listenApplyChanges(): void {
    if (!this.modalContentContainer) {
      return;
    }

    const applyBtn = this.modalContentContainer.querySelector<HTMLElement>(ProductCategoryMap.applyCategoriesBtn);

    applyBtn?.addEventListener('click', () => {
      this.eventEmitter.emit(ProductEventMap.categories.applyCategoryTreeChanges, {
        categories: this.selectedCategories,
      });
      this.closeModal();
    });
  }

  private listenCancelChanges(): void {
    if (!this.modalContentContainer) {
      return;
    }

    const cancelBtn = this.modalContentContainer.querySelector<HTMLElement>(ProductCategoryMap.cancelCategoriesBtn);
    cancelBtn?.addEventListener('click', () => this.closeModal());
  }

  private initTree(): void {
    const {categoryTree} = this;

    if (!(categoryTree instanceof HTMLElement)) {
      console.error('Category tree is not valid HTMLElement.');

      return;
    }

    this.treeCategories.forEach((treeCategory) => {
      const treeCategoryElement = this.generateCategoryTree(treeCategory);
      categoryTree.append(treeCategoryElement);
    });

    categoryTree.querySelectorAll(ProductCategoryMap.checkboxInput).forEach((checkbox) => {
      if (checkbox instanceof HTMLInputElement) {
        const categoryId = Number(checkbox.value);

        if (this.selectedCategories.some((category) => category.id === categoryId)) {
          // eslint-disable-next-line no-param-reassign
          checkbox.checked = true;
        }

        checkbox.addEventListener('change', (e) => {
          const {currentTarget} = e;

          if (!(currentTarget instanceof HTMLInputElement)) {
            console.error('currentTarget expected to be HTMLInputElement.');

            return;
          }

          // do not allow unchecking last remaining category
          if (this.selectedCategories.length === 1) {
            currentTarget.checked = true;
          }

          this.updateSelectedCategories();
        });
      }
    }, this);
    // Tree is initialized we can show it and hide loader
    if (this.modalContentContainer) {
      const fieldset = this.modalContentContainer.querySelector<HTMLElement>(ProductCategoryMap.fieldset);
      const loader = this.modalContentContainer.querySelector<HTMLElement>(ProductCategoryMap.loader);

      fieldset?.classList.remove('d-none');
      loader?.classList.add('d-none');
    }
  }

  /**
   * Used to recursively create items of the category tree
   */
  private generateCategoryTree(treeCategory: TreeCategory): HTMLElement {
    const categoryNode = this.generateTreeElement(treeCategory);
    const childrenList = categoryNode.querySelector<HTMLElement>(ProductCategoryMap.childrenList);
    const hasChildren = treeCategory.children && treeCategory.children.length > 0;

    const checkboxInput = categoryNode.querySelector<HTMLInputElement>(ProductCategoryMap.treeCheckboxInput);

    if (!checkboxInput) {
      // checkbox input is mandatory. If there is none, it's something fatally wrong already.
      throw new Error('Checkbox input not found in category tree. It is mandatory element for category tree to work.');
    }

    // check if this category is selected
    const isSelected = this.selectedCategories.some((selectedCategory: Category) => selectedCategory.id === treeCategory.id);

    categoryNode.classList.toggle('more', hasChildren);

    if (hasChildren) {
      const inputsContainer = categoryNode.querySelector<HTMLElement>(ProductCategoryMap.treeElementInputs);
      checkboxInput.value = String(treeCategory.id);

      inputsContainer?.addEventListener('click', (event) => {
        // We don't want to mess with the inputs behaviour (no toggle when checkbox or radio is clicked)
        // So we only toggle when the div itself is clicked.
        if (event.target !== event.currentTarget) {
          return;
        }

        const isExpanded = !childrenList?.classList.contains('d-none');
        categoryNode.classList.toggle('less', !isExpanded);
        categoryNode.classList.toggle('more', isExpanded);
        childrenList?.classList.toggle('d-none', isExpanded);
      });

      // Recursively build the children trees
      let containsSelectedChild = false;
      treeCategory.children.forEach((childCategory) => {
        const childTree = this.generateCategoryTree(childCategory);

        childrenList?.append(childTree);

        // check if at least one child is selected in child categories tree. Do not perform the check if at least one is found already
        if (!containsSelectedChild) {
          containsSelectedChild = this.selectedCategories
            .some((selectedCategory: Category) => selectedCategory.id === childCategory.id);
        }
      });

      // Expanding trees which contains at least one selected category or its parent category is selected
      childrenList?.classList.toggle('d-none', !(isSelected || containsSelectedChild));
    }

    return categoryNode;
  }

  private generateTreeElement(category: TreeCategory): HTMLElement {
    const categoryTree = this.categoryTree as HTMLElement;
    const prototypeTemplate = categoryTree.dataset.prototype as string;
    const prototypeName = categoryTree.dataset.prototypeName as string;

    // It is convenient to have categoryId as prototype name index because it is unique, but nothing depends on it
    const template = prototypeTemplate.replace(new RegExp(prototypeName, 'g'), String(category.id));
    // Trim is important here or the first child could be some text (whitespace, or \n)
    const frag = document.createRange().createContextualFragment(template.trim());
    const categoryNode = frag.firstChild as HTMLElement;

    // Add category name text
    const checkboxInput = categoryNode.querySelector<HTMLInputElement>(ProductCategoryMap.checkboxInput);

    if (!checkboxInput) {
      console.error(`Element ${ProductCategoryMap.checkboxInput} was not found.`);

      return categoryNode;
    }

    checkboxInput.value = String(category.id);

    const nameElement = document.createTextNode(category.name);
    const element = category.active
      ? nameElement
      : document.createElement('i').appendChild(nameElement).parentNode;

    if (!(element instanceof HTMLElement || element instanceof Text)) {
      console.error('Unexpected element type. Expected HTMLElement or Text.');

      return categoryNode;
    }

    if (!(checkboxInput.parentNode instanceof HTMLElement)) {
      console.error('Unexpected element type. Expected HTMLElement.');

      return categoryNode;
    }

    (checkboxInput.parentNode).insertBefore(element, checkboxInput);

    return categoryNode;
  }

  /**
   * Check/uncheck the selected category (matched by its ID) and toggle the tree by going up through the category's ancestors.
   */
  private updateCategory(categoryId: number, check: boolean): void {
    const treeElement = this.categoryTree as HTMLElement;
    const checkbox = treeElement.querySelector<HTMLInputElement>(ProductCategoryMap.inputByValue(categoryId));

    if (checkbox) {
      checkbox.checked = check;
      this.openCategoryParents(checkbox);
      this.updateSelectedCategories();
    } else {
      console.error(`Checkbox ${ProductCategoryMap.inputByValue(categoryId)} was not found`);
    }
  }

  private openCategoryParents(checkbox: HTMLInputElement): void {
    let parentItem = this.findParentTreeElement(checkbox);

    while (this.categoryTree && parentItem !== null && this.categoryTree.contains(parentItem)) {
      const childrenList = parentItem.querySelector(ProductCategoryMap.childrenList);

      if (childrenList && childrenList.childNodes.length) {
        parentItem.classList.add('less');
        parentItem.classList.remove('more');
        childrenList.classList.remove('d-none');
      }

      parentItem = this.findParentTreeElement(parentItem);
    }
  }

  private findParentTreeElement(element: HTMLElement): HTMLElement|null {
    // This is the element containing the checkbox
    let parentItem = element.closest(ProductCategoryMap.treeElement);

    if (parentItem && parentItem.parentNode instanceof HTMLElement) {
      // This is the first (potential) parent element
      parentItem = parentItem.parentNode.closest(ProductCategoryMap.treeElement);
    }

    if (!(parentItem instanceof HTMLElement)) {
      return null;
    }

    return parentItem;
  }

  private initTypeaheadData(treeCategories: Array<TreeCategory>) {
    treeCategories.forEach((treeCategory) => {
      this.typeaheadCategories.push({
        id: treeCategory.id,
        name: treeCategory.name,
        displayName: treeCategory.displayName,
      });

      if (treeCategory.children) {
        // Unfold the category tree, so that every child and parent categories stays on same level
        this.initTypeaheadData(treeCategory.children);
      }
    });
  }

  private initTypeahead(): void {
    const source: Bloodhound = new Bloodhound({
      // @ts-ignore
      datumTokenizer: Tokenizers.obj.letters('displayName'),
      queryTokenizer: Bloodhound.tokenizers.nonword,
      local: this.typeaheadCategories,
    });

    const searchConfig: InputAutoCompleteSearchConfig = {
      source,
      display: 'displayName',
      value: 'id',
      onSelect: (selectedItem: any, e: JQueryEventObject, searchInput: JQuery): boolean => {
        this.updateCategory(Number(selectedItem.id), true);

        // This resets the search input or else previous search is cached and can be added again
        searchInput.typeahead('val', '');

        return true;
      },
    };

    new AutoCompleteSearch($(ProductCategoryMap.searchInput), searchConfig);
  }

  private updateSelectedCategories(): void {
    if (!this.categoryTree || !this.tagsRenderer) {
      return;
    }

    const checkedCheckboxes = <NodeListOf<HTMLInputElement>> this.categoryTree
      .querySelectorAll(ProductCategoryMap.checkedCheckboxInputs);

    const categories: Array<Category> = [];
    checkedCheckboxes.forEach((checkbox) => {
      const categoryId = Number((checkbox as HTMLInputElement).value);
      const searchedCategory = this.searchCategoryInTree(categoryId, this.treeCategories);

      if (searchedCategory) {
        categories.push({
          id: searchedCategory.id,
          name: searchedCategory.name,
          displayName: searchedCategory.displayName,
        });
      }

      // do not allow to uncheck the checkbox if it is the last one selected category
      // eslint-disable-next-line no-param-reassign
      checkbox.disabled = checkedCheckboxes.length === 1;
    });

    this.tagsRenderer.render(categories);
    this.selectedCategories = categories;
  }

  private searchCategoryInTree(
    categoryId: number,
    treeCategories: Array<TreeCategory>): TreeCategory|null {
    let searchedCategory: any = null;

    treeCategories.forEach((category) => {
      if (categoryId === category.id) {
        searchedCategory = category;
      }

      if (searchedCategory === null && category.children && category.children.length > 0) {
        searchedCategory = this.searchCategoryInTree(categoryId, category.children);
      }
    });

    return searchedCategory;
  }

  private closeModal(): void {
    if (!(this.modal)) {
      return;
    }

    this.modal.hide();
  }
}
