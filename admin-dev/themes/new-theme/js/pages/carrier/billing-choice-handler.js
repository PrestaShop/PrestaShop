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
 * Responsible for manipulating fields that depends on selected billing choice
 */
export default class BillingChoiceHandler {
  constructor(priceLabel, weightLabel, billingChoice) {
    this.priceContent = priceLabel;
    this.weightContent = weightLabel;

    this.$billingSelector = $(billingChoice);

    this.handle();
    this.$billingSelector.change(event => this.handle(event));

    return {};
  }

  /**
   * Initiates the handler
   */
  handle() {
    this.switchRangeLabel();
  }

  /**
   * Switches range label text based on selected billing choice
   */
  switchRangeLabel() {
    // by default show weight text
    $(this.weightContent).show();
    $(this.priceContent).hide();

    // when billing by price is selected show price text
    if (this.$billingSelector.find('input:checked').val() === '2') {
      $(this.priceContent).show();
      $(this.weightContent).hide();
    }
  }
}
