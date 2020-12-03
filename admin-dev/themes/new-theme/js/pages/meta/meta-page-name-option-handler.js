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

const $ = window.$;

/**
 * Class MetaPageNameOptionHandler is responsible for checking the index page condition - if index page is selected it
 * does not allow to enter url rewrite field by disabling that input. In another cases url rewrite field is mandatory to
 * enter.
 */
export default class MetaPageNameOptionHandler {
  constructor() {
    const pageNameSelector = '.js-meta-page-name';
    const currentPage = $(pageNameSelector).val();
    this.setUrlRewriteDisabledStatusByCurrentPage(currentPage);

    $(document).on('change', pageNameSelector, event => this.changePageNameEvent(event));
  }

  /**
   * An event which is being called after the selector is being updated.
   * @param {object} event
   * @private
   */
  changePageNameEvent(event) {
    const $this = $(event.currentTarget);
    const currentPage = $this.val();

    this.setUrlRewriteDisabledStatusByCurrentPage(currentPage);
  }

  /**
   * Sets url rewrite form field to disabled or enabled according to current page value.
   * @param {string} currentPage
   * @private
   */
  setUrlRewriteDisabledStatusByCurrentPage(currentPage) {
    $('.js-url-rewrite input').prop('disabled', currentPage === 'index');
  }
}
