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

import ClickEvent = JQuery.ClickEvent;

const {$} = window;
const CategoryFilterMap = ProductMap.categories.categoryFilter;

export default class CategoryTreeFilter {
  private $categoryTree: JQuery;

  private $filterForm: JQuery;

  private $categoryInput: JQuery;

  constructor() {
    this.$categoryTree = $(CategoryFilterMap.container);
    this.$filterForm = $(CategoryFilterMap.filterForm);
    this.$categoryInput = this.$filterForm.find(CategoryFilterMap.categoryInput);

    this.init();
  }

  private init(): void {
    this.$categoryTree.on('click', CategoryFilterMap.categoryLabel, (event: ClickEvent) => {
      // We need to be careful here because the radio button is inside the label but we want to trigger only one
      // of the two actions, either expand/collapse or filter the selected category So We check which target has been
      // clicked exactly
      if (event.target instanceof HTMLInputElement) {
        this.filterCategory(event.target.value);
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
    this.$categoryTree.on('click', CategoryFilterMap.resetFilter, () => {
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

  private filterCategory(categoryId: string): void {
    this.$categoryInput.val(categoryId);
    this.$filterForm.submit();
  }

  private resetFilter(): void {
    this.$categoryTree
      .find(CategoryFilterMap.categoryRadio)
      .prop('checked', false);
    this.$categoryInput.val('');
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
