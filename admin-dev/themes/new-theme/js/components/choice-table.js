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
 * ChoiceTable is responsible for managing common actions in choice table form type
 */
export default class ChoiceTable {
  /**
   * Init constructor
   */
  constructor() {
    $(document).on('change', '.js-choice-table-select-all', (e) => {
      this.handleSelectAll(e);
    });
  }

  /**
   * Check/uncheck all boxes in table
   *
   * @param {Event} event
   */
  handleSelectAll(event) {
    const $selectAllCheckboxes = $(event.target);
    const isSelectAllChecked = $selectAllCheckboxes.is(':checked');

    $selectAllCheckboxes.closest('table').find('tbody input:checkbox').prop('checked', isSelectAllChecked);
  }
}
