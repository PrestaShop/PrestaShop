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

const {$} = window;

export default class CombinationsManager {
  constructor() {
    this.$paginationContainer = $(ProductMap.combinations.paginationContainer);
    this.$combinationsContainer = $(ProductMap.combinations.combinationsContainer);
    this.router = new Router();
    this.init();
  }

  init() {
    this.$paginationContainer.on('click', ProductMap.combinations.pageLink, (e) => {
      this.cleanTable();
      const page = this.extractPageFromDataset(e.currentTarget);
      const limit = this.getLimit();
      this.updatePaginator(page, limit);
      this.list(page, limit);
    });
    this.$paginationContainer.find(ProductMap.combinations.jumpToPageInput).keypress((e) => {
      if (e.which === 13) {
        const page = this.validatePageNumber(Number(e.currentTarget.value));
        const limit = this.getLimit();
        this.list(page, limit);
        this.updatePaginator(page, limit);
      }
    });
  }

  /**
   * @param {Number} currentPage
   * @param {Number} limit
   */
  updatePaginator(currentPage, limit) {
    this.setCurrentPageInput(currentPage);
    if (currentPage === this.getPagesCount()) {
      this.updatePaginatorForLastPage();
    } else if (currentPage === 1) {
      this.updatePaginatorForFirstPage();
    } else {
      this.updatePaginatorForMiddlePage(currentPage);
    }
  }

  list(page, limit) {
    //@todo:
    // fetch api(page, limit)
    // remove old list
    // render new list by inserting api data to prototype
  }

  cleanTable() {
    this.$combinationsContainer.find('table').empty();
  }

  getLimit() {
    return this.$paginationContainer.data('limit');
  }

  getPagesCount() {
    const limit = this.getLimit();
    const total = this.getTotal();

    return Math.ceil(total / limit);
  }

  getTotal() {
    return this.$paginationContainer.data('totalCombinations');
  }

  validatePageNumber(page) {
    if (page > this.getPagesCount()) {
      return this.getPagesCount();
    }

    if (page < 1) {
      return 1;
    }

    return page;
  }

  extractPageFromDataset(target) {
    return Number($(target).data('page'));
  }

  getProductId() {
    return Number(this.$paginationContainer.data('productId'));
  }

  setCurrentPageInput(page) {
    $(ProductMap.combinations.jumpToPageInput).val(page);
  }

  updatePaginatorForFirstPage() {
    this.toggleFirstPageAvailability(false);
    this.togglePreviousPageAvailability(false);
    this.updateNextPageBtnData(2);
    this.toggleNextPageAvailability(true);
    this.toggleLastPageAvailability(true);
  }

  updatePaginatorForLastPage() {
    this.toggleNextPageAvailability(false);
    this.toggleLastPageAvailability(false);
    this.updatePreviousPageBtnData(this.getPagesCount() - 1);
    this.togglePreviousPageAvailability(true);
  }

  updatePaginatorForMiddlePage(page) {
    this.updatePreviousPageBtnData(page - 1);
    this.updateNextPageBtnData(page + 1);
    this.toggleFirstPageAvailability(true);
    this.togglePreviousPageAvailability(true);
    this.toggleNextPageAvailability(true);
    this.toggleLastPageAvailability(true);
  }

  updatePreviousPageBtnData(page) {
    const previousPageBtn = this.$paginationContainer.find(ProductMap.combinations.previousPageBtn);
    previousPageBtn.data('url', this.router.generate('admin_products_combinations', {
      productId: this.getProductId(),
      page,
      limit: this.getLimit(),
    }));
    previousPageBtn.data('page', page);
  }

  updateNextPageBtnData(page) {
    const nextPageBtn = this.$paginationContainer.find(ProductMap.combinations.nextPageBtn);
    nextPageBtn.data('url', this.router.generate('admin_products_combinations', {
      productId: this.getProductId(),
      page,
      limit: this.getLimit(),
    }));
    nextPageBtn.data('page', page);
  }

  toggleFirstPageAvailability(enable) {
    const firstPageItem = this.$paginationContainer.find(ProductMap.combinations.firstPageItem);
    this.toggleTargetAvailability(firstPageItem, enable);
  }

  toggleLastPageAvailability(enable) {
    const lastPageItem = this.$paginationContainer.find(ProductMap.combinations.lastPageItem);
    this.toggleTargetAvailability(lastPageItem, enable);
  }

  togglePreviousPageAvailability(enable) {
    const previousPageItem = this.$paginationContainer.find(ProductMap.combinations.previousPageItem);
    this.toggleTargetAvailability(previousPageItem, enable);
  }

  toggleNextPageAvailability(enable) {
    const nextPageItem = this.$paginationContainer.find(ProductMap.combinations.nextPageItem);
    this.toggleTargetAvailability(nextPageItem, enable);
  }

  toggleTargetAvailability(target, enable) {
    if (enable) {
      target.removeClass('disabled');
    } else {
      target.addClass('disabled');
    }
  }
}
