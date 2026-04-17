/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

class StockManagementOptionHandler {
  constructor() {
    this.handle();

    $('input[name="stock[stock_management]"]').on('change', () => this.handle(),
    );
  }

  handle(): void {
    const stockManagementVal = $(
      'input[name="stock[stock_management]"]:checked',
    ).val();
    const isStockManagementEnabled = parseInt(<string>stockManagementVal, 10);

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
  handleAllowOrderingOutOfStockOption(isStockManagementEnabled: number): void {
    const allowOrderingOosRadios = $('input[name="stock[allow_ordering_oos]"]');

    if (isStockManagementEnabled) {
      allowOrderingOosRadios.removeAttr('disabled');
    } else {
      allowOrderingOosRadios.val(['1']);
      allowOrderingOosRadios.attr('disabled', 'disabled');
    }
  }

  /**
   * If stock managament is disabled
   * then 'Display available quantities on the product page' option must be No and disabled
   * otherwise it should be enabled
   *
   * @param {int} isStockManagementEnabled
   */
  handleDisplayAvailableQuantitiesOption(
    isStockManagementEnabled: number,
  ): void {
    const displayQuantitiesRadio = $('input[name="page[display_quantities]"]');

    if (isStockManagementEnabled) {
      displayQuantitiesRadio.removeAttr('disabled');
    } else {
      displayQuantitiesRadio.val(['0']);
      displayQuantitiesRadio.attr('disabled', 'disabled');
    }
  }
}

export default StockManagementOptionHandler;
