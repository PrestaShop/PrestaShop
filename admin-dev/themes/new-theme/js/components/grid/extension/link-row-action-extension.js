/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

/**
 * Class LinkRowActionExtension handles link row actions
 */
export default class LinkRowActionExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid) {
    this.initRowLinks(grid);
    this.initConfirmableActions(grid);
  }

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  initConfirmableActions(grid) {
    grid.getContainer().on('click', '.js-link-row-action', (event) => {
      const confirmMessage = $(event.currentTarget).data('confirm-message');

      if (confirmMessage.length && !confirm(confirmMessage)) {
        event.preventDefault();
      }
    });
  }

  /**
   * Add a click event on rows that matches the first link action (if present)
   *
   * @param {Grid} grid
   */
  initRowLinks(grid) {
    $('tr', grid.getContainer()).each(function initEachRow() {
      const $parentRow = $(this);

      $('.js-link-row-action[data-clickable-row=1]:first', $parentRow).each(function propagateFirstLinkAction() {
        const $rowAction = $(this);
        const $parentCell = $rowAction.closest('td');

        /*
         * Only search for cells with non clickable contents to avoid conflicts with
         * previous cell behaviour (action, toggle, ...)
         */
        const clickableCells = $('td.data-type, td.identifier-type:not(:has(input)), td.badge-type, td.position-type', $parentRow)
          .not($parentCell)
        ;

        clickableCells.addClass('cursor-pointer').click(() => {
          const confirmMessage = $rowAction.data('confirm-message');

          if (!confirmMessage.length || confirm(confirmMessage)) {
            document.location = $rowAction.attr('href');
          }
        });
      });
    });
  }
}
