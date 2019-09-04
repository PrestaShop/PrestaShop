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
export default class FreeShippingHandler {
  constructor(
    freeShippingChoice,
    handlingCostChoice,
    rangesTable,
    addRangeBtn,
    rangeRow,
    billingChoice,
    taxRuleSelect,
    outrangedBehaviorSelect,
  ) {
    this.freeShippingChoice = freeShippingChoice;
    this.handlingCostChoice = handlingCostChoice;
    this.rangesTableRows = rangesTable;
    this.rangeRow = rangeRow;

    this.$billingChoice = $(billingChoice);
    this.$taxRuleSelect = $(taxRuleSelect);
    this.$outrangedBehaviorSelect = $(outrangedBehaviorSelect);
    this.$addRangeBtn = $(addRangeBtn);
    this.$freeShippingChoice = $(freeShippingChoice);
    this.$handlingCostChoice = $(handlingCostChoice);

    this.handle();
    this.$freeShippingChoice.change(event => this.handle(event));
  }

  /**
   * Initiate handler
   */
  handle() {
    const isFreeShipping = $(`${this.freeShippingChoice}:checked`).val() === '1';
    this.toggleHandlingCost(isFreeShipping);
    this.toggleDependenciesVisibility(isFreeShipping);
  }

  /**
   * Toggles handling cost based on free shipping choice
   *
   * @param isFreeShipping
   */
  toggleHandlingCost(isFreeShipping) {
    // when free shipping is true, handling cost falsy value is checked and choices are disabled
    this.$handlingCostChoice.find('input[value="0"]').prop('checked', isFreeShipping);
    this.$handlingCostChoice.find('input').prop('disabled', isFreeShipping);
  }

  /**
   * shows/hide items that depends on free shipping choice
   *
   * @param isFreeShipping
   */
  toggleDependenciesVisibility(isFreeShipping) {
    const $tableRows = $(`${this.rangesTableRows}`);

    if (isFreeShipping) {
      // hide ranges and zone prices
      $tableRows.find('td').fadeOut();
      $tableRows.find(this.rangeRow).fadeOut();

      // hide add range button
      this.$addRangeBtn.fadeOut();
      //hide billing choices
      this.$billingChoice.fadeOut();
      // hide tax rule selections
      this.$taxRuleSelect.fadeOut();
      //hide out of range selections
      this.$outrangedBehaviorSelect.fadeOut();
    } else {
      // show ranges and zone prices
      $tableRows.find('td').fadeIn();
      $tableRows.find(this.rangeRow).fadeIn();

      // show add range button
      this.$addRangeBtn.fadeIn();
      //show billing choice list
      this.$billingChoice.fadeIn();
      //show tax rules selections
      this.$taxRuleSelect.fadeIn();
      //show out of range behavior selections
      this.$outrangedBehaviorSelect.fadeIn();
    }
  }
}
