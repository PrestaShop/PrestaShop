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

import Router from '@components/router';

const {$} = window;

export default class DynamicGridPaginator {
  /**
   * @param {String} containerSelector
   * @param {Object} dataProvider
   * @param {Object} gridRenderer
   * @param {Number|null} startingPage If provided it will load the provided page data on page load
   * @param {Object|null} selectorsMap If provided it will override css selectors used for all the actions.
   */
  constructor(
    containerSelector,
    dataProvider,
    gridRenderer,
    startingPage = null,
    selectorsMap = null,
  ) {
    this.$paginationContainer = $(containerSelector);
    this.router = new Router();
    this.dataProvider = dataProvider;
    this.gridRenderer = gridRenderer;
    this.setSelectorsMap(selectorsMap);
    this.init();
    if (startingPage !== null) {
      this.paginate(startingPage);
    }

    return {
      paginate: (page) => this.paginate(page),
    };
  }

  /**
   * Initiates the pagination component
   *
   * @private
   */
  init() {
    this.$paginationContainer.on('click', this.selectorsMap.pageLink, (e) => {
      this.paginate(Number($(e.currentTarget).data('page')));
    });
    this.$paginationContainer.find(this.selectorsMap.jumpToPageInput).keypress((e) => {
      if (e.which === 13) {
        e.preventDefault();
        const page = this.validatePageNumber(Number(e.currentTarget.value));
        this.paginate(page);
      }
    });
    this.$paginationContainer.on('change', this.selectorsMap.limitSelect, () => {
      this.paginate(1);
    });
  }

  /**
   * @param {Number} page
   */
  async paginate(page) {
    const data = await this.dataProvider.get(page, this.getLimit());
    $(this.selectorsMap.jumpToPageInput).val(page);
    this.countPages(data.total);
    this.refreshButtonsData(page);

    if (page === this.pagesCount) {
      this.updatePaginatorForLastPage();
    } else if (page === 1) {
      this.updatePaginatorForFirstPage();
    } else {
      this.updatePaginatorForMiddlePage();
    }
    this.gridRenderer.render(data);
  }

  /**
   * @private
   */
  updatePaginatorForFirstPage() {
    this.toggleFirstPageAvailability(false);
    this.togglePreviousPageAvailability(false);
    this.toggleNextPageAvailability(true);
    this.toggleLastPageAvailability(true);
  }

  /**
   * @private
   */
  updatePaginatorForLastPage() {
    this.toggleNextPageAvailability(false);
    this.toggleLastPageAvailability(false);
    this.toggleFirstPageAvailability(true);
    this.togglePreviousPageAvailability(true);
  }

  /**
   * @private
   */
  updatePaginatorForMiddlePage() {
    this.toggleFirstPageAvailability(true);
    this.togglePreviousPageAvailability(true);
    this.toggleNextPageAvailability(true);
    this.toggleLastPageAvailability(true);
  }

  /**
   * @param {Number} page
   *
   * @private
   */
  refreshButtonsData(page) {
    this.$paginationContainer.find(this.selectorsMap.nextPageBtn).data('page', page + 1);
    this.$paginationContainer.find(this.selectorsMap.previousPageBtn).data('page', page - 1);
    this.$paginationContainer.find(this.selectorsMap.lastPageBtn).data('page', this.pagesCount);
  }

  /**
   * @param {Boolean} enable
   *
   * @private
   */
  toggleFirstPageAvailability(enable) {
    this.toggleTargetAvailability(this.$paginationContainer.find(this.selectorsMap.firstPageItem), enable);
  }

  /**
   * @param {Boolean} enable
   *
   * @private
   */
  toggleLastPageAvailability(enable) {
    this.toggleTargetAvailability(this.$paginationContainer.find(this.selectorsMap.lastPageItem), enable);
  }

  /**
   * @param {Boolean} enable
   *
   * @private
   */
  togglePreviousPageAvailability(enable) {
    this.toggleTargetAvailability(this.$paginationContainer.find(this.selectorsMap.previousPageItem), enable);
  }

  /**
   * @param {boolean} enable
   *
   * @private
   */
  toggleNextPageAvailability(enable) {
    this.toggleTargetAvailability(this.$paginationContainer.find(this.selectorsMap.nextPageItem), enable);
  }

  /**
   * @param {Object} target
   * @param {Boolean} enable
   *
   * @private
   */
  toggleTargetAvailability(target, enable) {
    if (enable) {
      target.removeClass('disabled');
    } else {
      target.addClass('disabled');
    }
  }

  /**
   * @param {Number} total
   *
   * @private
   */
  countPages(total) {
    this.pagesCount = Math.ceil(total / this.getLimit());
    const lastPageItem = this.$paginationContainer.find(this.selectorsMap.lastPageBtn);
    lastPageItem.data('page', this.pagesCount);
    lastPageItem.text(this.pagesCount);
  }

  /**
   * @returns {Number}
   *
   * @private
   */
  getLimit() {
    return this.$paginationContainer.find(this.selectorsMap.limitSelect).val();
  }

  /**
   *
   * @param page
   *
   * @returns {Number}
   */
  validatePageNumber(page) {
    if (page > this.pagesCount) {
      return this.pagesCount;
    }

    if (page < 1) {
      return 1;
    }

    return page;
  }

  /**
   * @param {Object} selectorsMap
   */
  setSelectorsMap(selectorsMap) {
    if (selectorsMap) {
      this.selectorsMap = selectorsMap;

      return;
    }

    this.selectorsMap = {
      jumpToPageInput: 'input[name="paginator-jump-page"]',
      firstPageBtn: 'button.page-link.first',
      firstPageItem: 'li.page-item.first',
      nextPageBtn: 'button.page-link.next',
      nextPageItem: 'li.page-item.next',
      previousPageBtn: 'button.page-link.previous',
      previousPageItem: 'li.page-item.previous',
      lastPageItem: 'li.page-item.last',
      lastPageBtn: 'button.page-link.last',
      pageLink: 'button.page-link',
      limitSelect: '#paginator-limit',
    };
  }
}
