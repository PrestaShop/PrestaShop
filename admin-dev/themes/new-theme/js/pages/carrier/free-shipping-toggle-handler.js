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
 * Responsible for toggling/disabling form fields that are dependant from free-shipping field value
 */
export default class FreeShippingToggleHandler {
  constructor(
    freeShippingSelector,
    handlingCostSelector,
    rangeTableRowsSelector,
    addRangeBtnSelector
  ) {
    this.freeShippingSelector = freeShippingSelector;
    this.handlingCostSelector = handlingCostSelector;
    this.rangesTableRowsSelector = rangeTableRowsSelector;

    this.$addRangeBtnSelector = $(addRangeBtnSelector);
    this.$freeShippingSelector = $(freeShippingSelector);
    this.$handlingCostSelector = $(handlingCostSelector);

    this._handle();
    this.$freeShippingSelector.change(event => this._handle(event));
  }

  _handle() {
    const isFreeShipping = $(`${this.freeShippingSelector}:checked`).val() === '1';
    this._toggleHandlingCost(isFreeShipping);
    this._toggleRangesAvailability(isFreeShipping);
    this._disableAddingRange(isFreeShipping);
  }

  _toggleHandlingCost(isFreeShipping) {
    this.$handlingCostSelector.prop('disabled', isFreeShipping);
    $(`${this.handlingCostSelector}:not(:checked)`).prop('checked', !isFreeShipping);

    if (isFreeShipping) {
      $(`${this.handlingCostSelector}:checked`).prop('checked', false);
    }
  }

  _toggleRangesAvailability(isFreeShipping) {
    $(`${this.rangesTableRowsSelector}`).find('input:not([type="checkbox"])').prop('readonly', isFreeShipping);
  }

  _disableAddingRange(isFreeShipping) {
    this.$addRangeBtnSelector.show();
    if (isFreeShipping) {
      this.$addRangeBtnSelector.hide();
    }
  }
}
