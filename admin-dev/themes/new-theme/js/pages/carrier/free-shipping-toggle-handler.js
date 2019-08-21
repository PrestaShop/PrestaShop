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
 * Responsible for toggling/disabling form fields that depends on free-shipping choice value
 */
export default class FreeShippingToggleHandler {
  constructor(
    freeShippingChoice,
    handlingCostChoice,
    rangesTable,
    addRangeBtn,
    rangeRow
  ) {
    this.freeShippingChoice = freeShippingChoice;
    this.handlingCostChoice = handlingCostChoice;
    this.rangesTableRows = rangesTable;
    this.rangeRow = rangeRow;

    this.$addRangeBtn = $(addRangeBtn);
    this.$freeShippingChoice = $(freeShippingChoice);
    this.$handlingCostChoice = $(handlingCostChoice);

    this.handle();
    this.$freeShippingChoice.change(event => this.handle(event));
  }

  handle() {
    const isFreeShipping = $(`${this.freeShippingChoice}:checked`).val() === '1';
    this.toggleHandlingCost(isFreeShipping);
    this.toggleDependenciesVisibility(isFreeShipping);
  }

  toggleHandlingCost(isFreeShipping) {
    this.$handlingCostChoice.prop('disabled', isFreeShipping);
    $(`${this.handlingCostChoice}:not(:checked)`).prop('checked', !isFreeShipping);

    if (isFreeShipping) {
      $(`${this.handlingCostChoice}:checked`).prop('checked', false);
    }
  }

  toggleDependenciesVisibility(isFreeShipping) {
    const $tableRows = $(`${this.rangesTableRows}`);

    // show ranges and zone prices
    $tableRows.find('td').show();
    $tableRows.find(this.rangeRow).show();

    // show add range button
    this.$addRangeBtn.show();

    if (isFreeShipping) {
      // hide ranges and zone prices
      $tableRows.find('td').hide();
      $tableRows.find(this.rangeRow).hide();

      // hide add range button
      this.$addRangeBtn.hide();
    }
  }
}
