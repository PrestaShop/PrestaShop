/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

export default class Grid {
  constructor() {
    this.handleBulkActionSelectAllCheckbox();
    this.handleBulkActionCheckboxSelect();
  }

  /**
   * Handles "Select all" button in the grid
   */
  handleBulkActionSelectAllCheckbox() {
    $(document).on('change', '.js-select-all-btn', (e) => {
      const $checkbox = $(e.target);
      const $grid = $checkbox.closest('.js-grid');
      const $items = $grid.find('.js-bulk-action-checkbox');
      const $bulkActionsBtn = $grid.find('.js-bulk-actions-btn');

      const isChecked = $checkbox.is(':checked');

      $items.prop('checked', isChecked);
      $bulkActionsBtn.prop('disabled', !isChecked);
    });
  }

  /**
   * Handles each bulk action checkbox select in the grid
   */
  handleBulkActionCheckboxSelect() {
    //@todo
  }
}
