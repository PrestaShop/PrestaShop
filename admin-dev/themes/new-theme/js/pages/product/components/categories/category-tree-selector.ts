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

// @ts-ignore-next-line
import Bloodhound from 'typeahead.js';
import AutoCompleteSearch, {InputAutoCompleteSearchConfig} from '@components/auto-complete-search';
import Tokenizers from '@components/bloodhound/tokenizers';
import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import {getCategories} from '@pages/product/services/categories';
import TagsRenderer from '@pages/product/components/categories/tags-renderer';
import {EventEmitter} from 'events';
import Modal, {ModalType} from '@components/modal/modal';

const {$} = window;

const ProductCategoryMap = ProductMap.categories;

export default class CategoryTreeSelector {
  eventEmitter: EventEmitter;

  selectedCategories: Array<Category>

  treeCategories: Array<TreeCategory>

  typeaheadCategories: Array<TypeaheadCategory>;

  defaultCategoryId: number;

  modalContentContainer: HTMLElement|null

  modal: ModalType|null

  categoryTree: HTMLElement|null;

  tagsRenderer: TagsRenderer|null;

  constructor(eventEmitter: EventEmitter) {
    this.eventEmitter = eventEmitter;
    this.treeCategories = [];
    this.typeaheadCategories = [];
    this.selectedCategories = [];
    this.defaultCategoryId = 0;
    this.modalContentContainer = null;
    this.modal = null;
    this.categoryTree = null;
    this.tagsRenderer = null;
  }

  public showModal(selectedCategories: Array<Category>, defaultCategoryId: number): void {
    if (!defaultCategoryId) {
      console.error('Default category id is invalid');

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
    this.initModal();
    modal.show();
    this.modal = modal;
  }

  private async initModal(): Promise<void> {
    this.modalContentContainer = document.querySelector(ProductCategoryMap.modalContentContainer) as HTMLElement;
    this.categoryTree = this.modalContentContainer.querySelector(ProductCategoryMap.categoryTree) as HTMLElement;
    this.tagsRenderer = new TagsRenderer(
      this.eventEmitter,
      `${ProductCategoryMap.modalContentContainer} ${ProductCategoryMap.tagsContainer}`,
      ProductEventMap.categories.tagRemoved,
    );
    this.tagsRenderer.render(this.selectedCategories, this.defaultCategoryId);
    this.treeCategories = await getCategories();

    this.initTypeaheadData(this.treeCategories, '');
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

    const applyBtn = this.modalContentContainer.querySelector(ProductCategoryMap.applyCategoriesBtn) as HTMLElement;

    applyBtn.addEventListener('click', () => {
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

    const cancelBtn = this.modalContentContainer.querySelector(ProductCategoryMap.cancelCategoriesBtn) as HTMLElement;
    cancelBtn.addEventListener('click', () => this.closeModal());
  }

  private initTree(): void {
    const {categoryTree} = this;

    if (!(categoryTree instanceof HTMLElement)) {
      console.error('Category tree is not valid HTMLElement');

      return;
    }

    this.treeCategories.forEach((treeCategory) => {
      const treeCategoryElement = this.generateCategoryTree(treeCategory);
      categoryTree.append(treeCategoryElement);
    });

    categoryTree.querySelectorAll(ProductCategoryMap.checkboxInput).forEach((checkbox) => {
      if (checkbox instanceof HTMLInputElement) {
        const categoryId = Number(checkbox.value);

        // disable main category checkbox
        if (categoryId === this.defaultCategoryId) {
          // eslint-disable-next-line no-param-reassign
          checkbox.disabled = true;
        }

        if (this.selectedCategories.some((category) => category.id === categoryId)) {
          // eslint-disable-next-line no-param-reassign
          checkbox.checked = true;
        }

        checkbox.addEventListener('change', (e) => {
          const currentTarget = e.currentTarget as HTMLInputElement;

          // do not allow unchecking main category id
          if (Number(currentTarget.value) === this.defaultCategoryId && !currentTarget.checked) {
            currentTarget.checked = true;

            return;
          }

          this.updateSelectedCategories();
        });
      }
    }, this);
    // Tree is initialized we can show it and hide loader
    if (this.modalContentContainer) {
      const fieldset = this.modalContentContainer.querySelector(ProductCategoryMap.fieldset) as HTMLElement;
      const loader = this.modalContentContainer.querySelector(ProductCategoryMap.loader) as HTMLElement;

      fieldset.classList.remove('d-none');
      loader.classList.add('d-none');
    }
  }

  /**
   * Used to recursively create items of the category tree
   */
  private generateCategoryTree(treeCategory: TreeCategory): HTMLElement {
    const categoryNode = this.generateTreeElement(treeCategory) as HTMLElement;
    const childrenList = categoryNode.querySelector(ProductCategoryMap.childrenList) as HTMLElement;

    const hasChildren = treeCategory.children && treeCategory.children.length > 0;
    categoryNode.classList.toggle('more', hasChildren);
    if (hasChildren) {
      const inputsContainer = categoryNode.querySelector(ProductCategoryMap.treeElementInputs) as HTMLElement;
      const checkboxInput = inputsContainer.querySelector(ProductCategoryMap.treeCheckboxInput) as HTMLInputElement;
      checkboxInput.value = String(treeCategory.id);

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
      treeCategory.children.forEach((childCategory) => {
        const childTree = this.generateCategoryTree(childCategory);

        childrenList.append(childTree);
      });
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
    const checkboxInput = categoryNode.querySelector(ProductCategoryMap.checkboxInput) as HTMLInputElement;
    checkboxInput.value = String(category.id);

    const nameElement = document.createTextNode(category.name);
    const element = category.active
      ? nameElement
      : document.createElement('i').appendChild(nameElement).parentNode as HTMLElement;

    (checkboxInput.parentNode as HTMLElement).insertBefore(element, checkboxInput);

    return categoryNode;
  }

  /**
   * Check/uncheck the selected category (matched by its ID) and toggle the tree by going up through the category's ancestors.
   */
  private updateCategory(categoryId: number, check: boolean): void {
    const treeElement = this.categoryTree as HTMLElement;
    const checkbox = treeElement.querySelector(ProductCategoryMap.inputByValue(categoryId)) as HTMLInputElement;
    checkbox.checked = check;
    this.openCategoryParents(checkbox);
    this.updateSelectedCategories();
  }

  private openCategoryParents(checkbox: HTMLInputElement): void {
    // This is the element containing the checkbox
    let parentItem = checkbox.closest(ProductCategoryMap.treeElement);

    if (parentItem) {
      // This is the first (potential) parent element
      parentItem = (parentItem.parentNode as HTMLElement).closest(ProductCategoryMap.treeElement);
    }

    while (this.categoryTree && parentItem !== null && this.categoryTree.contains(parentItem)) {
      const childrenList = parentItem.querySelector(ProductCategoryMap.childrenList);

      if (childrenList && childrenList.childNodes.length) {
        parentItem.classList.add('less');
        parentItem.classList.remove('more');
        childrenList.classList.remove('d-none');
      }

      parentItem = (parentItem.parentNode as HTMLElement).closest(ProductCategoryMap.treeElement);
    }
  }

  private initTypeaheadData(
    treeCategories: Array<TreeCategory>,
    parentBreadcrumb: string,
  ) {
    treeCategories.forEach((treeCategory) => {
      const typeaheadCategory: TypeaheadCategory = {
        id: treeCategory.id,
        name: treeCategory.name,
        breadcrumb: parentBreadcrumb ? `${parentBreadcrumb} > ${treeCategory.name}` : treeCategory.name,
      };
      this.typeaheadCategories.push(typeaheadCategory);

      if (treeCategory.children) {
        this.initTypeaheadData(treeCategory.children, typeaheadCategory.breadcrumb);
      }
    });
  }

  private initTypeahead(): void {
    const source: Bloodhound = new Bloodhound({
      // @ts-ignore
      datumTokenizer: Tokenizers.obj.letters('breadcrumb'),
      queryTokenizer: Bloodhound.tokenizers.nonword,
      local: this.typeaheadCategories,
    });

    const searchConfig: InputAutoCompleteSearchConfig = {
      source,
      display: 'breadcrumb',
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

    const checkedCheckboxes = this.categoryTree.querySelectorAll(ProductCategoryMap.checkedCheckboxInputs);

    const categories: Array<Category> = [];
    checkedCheckboxes.forEach((checkbox) => {
      const categoryId = Number((checkbox as HTMLInputElement).value);
      const searchedCategory = this.searchCategoryInTree(categoryId, this.treeCategories);

      if (searchedCategory) {
        categories.push({
          id: searchedCategory.id,
          name: searchedCategory.name,
        });
      }
    });

    this.tagsRenderer.render(categories, this.defaultCategoryId);
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
