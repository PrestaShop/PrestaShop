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

const {$} = window;

/**
 * Related html template src/PrestaShopBundle/Resources/views/Admin/Common/javascript_pagination.html.twig
 *
 * Usage
 *```
 *  $paginator new DynamicPaginator(
 *    '#foo-container',
 *    FooDataService,
 *    FooRenderer
 *  );
 *  this.eventEmitter.on('fooEventThatShouldTriggerPagination', () => $paginator.paginate(1));
 *```
 * You can also provide the starting page to initiate it automatically on page load:
 *```
 *  $paginator new DynamicPaginator(
 *    '#foo-container',
 *    FooDataService,
 *    FooRenderer,
 *    1
 *  );
 *```
 *  There is also a possibility to provide custom selectorsMap as 5th argument. See this.setSelectorsMap().
 *
 * Pagination service must have a method fetch(offset, limit) which returns data.{any resources name} & data.total
 * e.g.
 * ```
 * class FooDataService {
 *  fetch(offset, limit) {
 *    return $.get(this.router.generate('admin_products_combinations', {
 *      productId: this.productId,
 *      page,
 *      limit,
 *    }));
 *  }
 * }
 *```
 *  * In this case the action of route `admin_products_combinations` returns following json:
 * ```
 * {
 *   total: 100,
 *   combinations: [{combinationId: 1, name: foo...}, {combinationId: 2, name: bar...}]
 * }
 *```
 *
 * The renderer must have a method render(data) which accepts the data from PaginationService
 * and renders it depending on needs
 */
export default class DynamicPaginator {
  /**
   * @param {String} containerSelector
   * @param {Object} paginationService
   * @param {Object} renderer
   * @param {Number|null} startingPage If provided it will load the provided page data on page load
   * @param {Object|null} selectorsMap If provided it will override css selectors used for all the actions.
   */
  constructor(containerSelector, paginationService, renderer, startingPage = null, selectorsMap = null) {
    this.$paginationContainer = $(containerSelector);
    this.paginationService = paginationService;
    this.renderer = renderer;
    this.setSelectorsMap(selectorsMap);
    this.init();
    this.currentPage = 1;
    if (startingPage !== null) {
      this.paginate(startingPage);
    }

    return {
      paginate: (page) => this.paginate(page),
      getCurrentPage: () => this.currentPage,
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
        const page = this.getValidPageNumber(Number(e.currentTarget.value));
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
    this.currentPage = page;
    this.renderer.toggleLoading(true);
    const limit = this.getLimit();
    const data = await this.paginationService.fetch(this.calculateOffset(page, limit), limit);
    $(this.selectorsMap.jumpToPageInput).val(page);
    this.countPages(data.total);
    this.refreshButtonsData(page);
    this.refreshInfoLabel(page, data.total);

    this.toggleTargetAvailability(this.selectorsMap.firstPageItem, page > 1);
    this.toggleTargetAvailability(this.selectorsMap.previousPageItem, page > 1);
    this.toggleTargetAvailability(this.selectorsMap.nextPageItem, page < this.pagesCount);
    this.toggleTargetAvailability(this.selectorsMap.lastPageItem, page < this.pagesCount);

    this.renderer.render(data);
    this.renderer.toggleLoading(false);

    window.prestaShopUiKit.initToolTips();
  }

  /**
   * @param page
   * @param limit
   *
   * @returns {Number}
   */
  calculateOffset(page, limit) {
    return (page - 1) * limit;
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
   * @param {Number} page
   * @param {Number} total
   */
  refreshInfoLabel(page, total) {
    const infoLabel = this.$paginationContainer.find(this.selectorsMap.paginationInfoLabel);
    const limit = this.getLimit();
    const from = page === 1 ? 1 : Math.round((page - 1) * limit);
    const to = page === this.pagesCount ? total : Math.round(page * limit);
    const modifiedInfoText = infoLabel
      .data('pagination-info')
      .replace(/%from%/g, from)
      .replace(/%to%/g, to)
      .replace(/%total%/g, total)
      .replace(/%current_page%/g, page)
      .replace(/%page_count%/g, this.pagesCount);

    infoLabel.text(modifiedInfoText);
  }

  /**
   * @param {String} targetSelector
   * @param {Boolean} enable
   *
   * @private
   */
  toggleTargetAvailability(targetSelector, enable) {
    const target = this.$paginationContainer.find(targetSelector);

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
  getValidPageNumber(page) {
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
      paginationInfoLabel: '#pagination-info',
    };
  }
}
