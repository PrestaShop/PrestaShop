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
  constructor(
    containerSelector,
    gridRenderer,
    routingOptions,
    selectorsMap = null,
  ) {
    this.$paginationContainer = $(containerSelector);
    this.router = new Router();
    this.routingOptions = routingOptions;
    this.gridRenderer = gridRenderer;
    this.setRoutingOptions(routingOptions);
    this.setSelectorsMap(selectorsMap);
    this.init();
  }

  init() {
    this.$paginationContainer.on('click', this.selectorsMap.pageLink, (e) => {
      const page = this.extractPageFromDataset(e.currentTarget);
      const limit = this.getLimit();
      this.updatePaginator(page, limit);
      this.gridRenderer.render(page, limit);
    });
    this.$paginationContainer.find(this.selectorsMap.jumpToPageInput).keypress((e) => {
      if (e.which === 13) {
        e.preventDefault();
        const page = this.validatePageNumber(Number(e.currentTarget.value));
        const limit = this.getLimit();
        this.gridRenderer.render(page, limit);
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

  getLimit() {
    return this.$paginationContainer.data('limit');
  }

  getPagesCount() {
    const limit = this.getLimit();
    const total = this.getTotal();

    return Math.ceil(total / limit);
  }

  getTotal() {
    return this.$paginationContainer.data('total');
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

  setCurrentPageInput(page) {
    $(this.selectorsMap.jumpToPageInput).val(page);
  }

  updatePaginatorForFirstPage() {
    this.toggleFirstPageAvailability(false);
    this.togglePreviousPageAvailability(false);
    this.updateNextPageUrl(2);
    this.toggleNextPageAvailability(true);
    this.toggleLastPageAvailability(true);
  }

  updatePaginatorForLastPage() {
    this.toggleFirstPageAvailability(true);
    this.toggleNextPageAvailability(false);
    this.toggleLastPageAvailability(false);
    this.updatePreviousPageUrl(this.getPagesCount() - 1);
    this.togglePreviousPageAvailability(true);
  }

  updatePaginatorForMiddlePage(page) {
    this.updatePreviousPageUrl(page - 1);
    this.updateNextPageUrl(page + 1);
    this.toggleFirstPageAvailability(true);
    this.togglePreviousPageAvailability(true);
    this.toggleNextPageAvailability(true);
    this.toggleLastPageAvailability(true);
  }

  updatePreviousPageUrl(page) {
    this.updateUrl(
      this.$paginationContainer.find(this.selectorsMap.previousPageBtn),
      page,
    );
  }

  updateNextPageUrl(page) {
    this.updateUrl(
      this.$paginationContainer.find(this.selectorsMap.nextPageBtn),
      page,
    );
  }

  updateUrl(button, page) {
    button.data('url', this.router.generate(this.routing.route, this.mergeParamsToKeep({
      [this.routing.pageKey]: page,
      [this.routing.limitKey]: this.getLimit(),
    })));
    button.data('page', page);
  }

  toggleFirstPageAvailability(enable) {
    this.toggleTargetAvailability(this.$paginationContainer.find(this.selectorsMap.firstPageItem), enable);
  }

  toggleLastPageAvailability(enable) {
    this.toggleTargetAvailability(this.$paginationContainer.find(this.selectorsMap.lastPageItem), enable);
  }

  togglePreviousPageAvailability(enable) {
    this.toggleTargetAvailability(this.$paginationContainer.find(this.selectorsMap.previousPageItem), enable);
  }

  toggleNextPageAvailability(enable) {
    this.toggleTargetAvailability(this.$paginationContainer.find(this.selectorsMap.nextPageItem), enable);
  }

  toggleTargetAvailability(target, enable) {
    if (enable) {
      target.removeClass('disabled');
    } else {
      target.addClass('disabled');
    }
  }

  mergeParamsToKeep(params) {
    const finalParams = {};

    if (!this.routing.paramsToKeep) {
      return params;
    }

    Object.keys(this.routing.paramsToKeep).forEach((key) => {
      finalParams[key] = this.routing.paramsToKeep[key];
    });

    return finalParams;
  }


  setRoutingOptions(options) {
    this.routing = {};
    if (options.route === 'undefined') {
      console.log('route is missing in dynamic paginator routing options');
      return;
    }

    this.routing.route = options.route;

    if (typeof options.paramsToKeep !== 'undefined') {
      this.routing.paramsToKeep = options.paramsToKeep;
    }

    if (typeof options.pageKey !== 'undefined') {
      this.routing.pageKey = options.pageKey;
    } else {
      this.routing.pageKey = 'page';
    }

    if (typeof options.limitKey !== 'undefined') {
      this.routing.limitKey = options.limitKey;
    } else {
      this.routing.limitKey = 'limit';
    }
  }

  setSelectorsMap(selectorsMap) {
    if (selectorsMap) {
      this.selectorsMap = selectorsMap;

      return;
    }

    this.selectorsMap = {
      jumpToPageInput: 'input[name="paginator_jump_page"]',
      firstPageBtn: 'button.page-link.first',
      firstPageItem: 'li.page-item.first',
      nextPageBtn: 'button.page-link.next',
      nextPageItem: 'li.page-item.next',
      previousPageBtn: 'button.page-link.previous',
      previousPageItem: 'li.page-item.previous',
      lastPageItem: 'li.page-item.last',
      lastPageBtn: 'button.page-link.last',
      pageLink: 'button.page-link',
    };
  }
}
