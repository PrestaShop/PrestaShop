/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ProductMap from '@pages/product/product-map';

import ClickEvent = JQuery.ClickEvent;

const {$} = window;
const CategoryFilterMap = ProductMap.categories.categoryFilter;

export default class CategoryTreeFilter {
  private $categoryTree: JQuery;

  private $filterForm: JQuery;

  constructor() {
    this.$categoryTree = $(CategoryFilterMap.container);
    this.$filterForm = this.$categoryTree.parent('form');

    this.init();
  }

  private init(): void {
    this.$categoryTree.on('click', CategoryFilterMap.categoryLabel, (event: ClickEvent) => {
      // We need to be careful here because the radio button is inside the label but we want to trigger only one
      // of the two actions, either expand/collapse or filter the selected category So We check which target has been
      // clicked exactly
      if (event.target instanceof HTMLInputElement) {
        this.$filterForm.submit();
      } else if (event.target.classList.contains(CategoryFilterMap.categoryLabelClass)) {
        this.toggleCategory($(event.currentTarget).parent(CategoryFilterMap.categoryNode));
      }
    });

    this.$categoryTree.on('click', CategoryFilterMap.expandAll, () => {
      this.expandAll();
    });
    this.$categoryTree.on('click', CategoryFilterMap.collapseAll, () => {
      this.collapseAll();
    });
    $(CategoryFilterMap.resetFilter).on('click', () => {
      this.resetFilter();
    });

    this.collapseAll();
  }

  private toggleCategory($categoryNode: JQuery): void {
    const $children = $categoryNode.find(CategoryFilterMap.categoryChildren).first();

    if (!$children.length) {
      return;
    }

    const isExpanded = $categoryNode.hasClass(CategoryFilterMap.expandedClass);
    $children.toggleClass('d-none', isExpanded);
    $categoryNode.toggleClass(CategoryFilterMap.expandedClass, !isExpanded);
    $categoryNode.toggleClass(CategoryFilterMap.collapsedClass, isExpanded);
  }

  private resetFilter(): void {
    // Reset selected category
    this.$categoryTree
      .find(CategoryFilterMap.categoryRadio)
      .prop('checked', false);

    this.$filterForm.submit();
  }

  private expandAll(): void {
    this.$categoryTree.find(CategoryFilterMap.categoryChildren).removeClass('d-none');
    this.$categoryTree
      .find(CategoryFilterMap.categoryChildren)
      .parent(CategoryFilterMap.categoryNode)
      .removeClass(CategoryFilterMap.collapsedClass)
      .addClass(CategoryFilterMap.expandedClass);
  }

  private collapseAll(): void {
    this.$categoryTree.find(CategoryFilterMap.categoryChildren).addClass('d-none');
    this.$categoryTree
      .find(CategoryFilterMap.categoryChildren)
      .parent(CategoryFilterMap.categoryNode)
      .removeClass(CategoryFilterMap.expandedClass)
      .addClass(CategoryFilterMap.collapsedClass);
  }
}
