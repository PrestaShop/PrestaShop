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

class StockManagementOptionHandler {
  constructor() {
    this.handle();

    $('#form_stock_stock_management').on('change', this.handle.bind(this));
  }

  handle() {
    const stockManagementVal = $('#form_stock_stock_management').val();
    const isStockManagementEnabled = parseInt(stockManagementVal);

    this.handleAllowOrderingOutOfStockOption(isStockManagementEnabled);
    this.handleDisplayAvailableQuantitiesOption(isStockManagementEnabled);
  }

  /**
   * If stock managament is disabled
   * then 'Allow ordering of out-of-stock products' option must be Yes and disabled
   * otherwise it should be enabled
   *
   * @param {int} isStockManagementEnabled
   */
  handleAllowOrderingOutOfStockOption(isStockManagementEnabled) {
    const allowOrderingOosSelect = $('#form_stock_allow_ordering_oos');

    if (isStockManagementEnabled) {
        allowOrderingOosSelect.removeAttr('disabled');
    } else {
        allowOrderingOosSelect.val(1);
        allowOrderingOosSelect.attr('disabled', 'disabled');
    }
  }

  /**
   * If stock managament is disabled
   * then 'Display available quantities on the product page' option must be No and disabled
   * otherwise it should be enabled
   *
   * @param {int} isStockManagementEnabled
   */
  handleDisplayAvailableQuantitiesOption(isStockManagementEnabled) {
    const displayQuantitiesSelect = $('#form_page_display_quantities');

    if (isStockManagementEnabled) {
        displayQuantitiesSelect.removeAttr('disabled');
    } else {
        displayQuantitiesSelect.val(0);
        displayQuantitiesSelect.attr('disabled', 'disabled');
    }
  }
}

export default StockManagementOptionHandler;
