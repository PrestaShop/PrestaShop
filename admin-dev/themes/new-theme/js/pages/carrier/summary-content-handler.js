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
 * Responsible for carrier summary content which depends from previous steps inputs
 */
export default class SummaryContentHandler {
  constructor(
    formWrapper,
    freeShippingInput,
    transitTimeInput,
    billingChoice,
    taxRuleSelect
  ) {
    this.$formWrapper = $(formWrapper);
    this.freeShippingInput = freeShippingInput;
    this.transitTimeInput = transitTimeInput;
    this.$billing = $(billingChoice);
    this.$taxRuleSelect = $(taxRuleSelect);
    this._handle();
  }

  _handle() {
    this.$formWrapper.bind('step-switched', () => {
      const isFreeShipping = $(`${this.freeShippingInput}:checked`).val() === '1';
      this.summarizeTransitTime(isFreeShipping);
      this.summarizeShippingCost(isFreeShipping);
    });
  }

  summarizeTransitTime(isFreeShipping) {
    const contextLangId = this.$formWrapper.data('context-lang-id');
    const defaultLangId = this.$formWrapper.data('default-lang-id');

    let transitValue = this.$formWrapper
      .find(`${this.transitTimeInput}[${contextLangId}]"]`)
      .val();

    if (transitValue === '') {
      transitValue = this.$formWrapper
        .find(`${this.transitTimeInput}[${defaultLangId}]"]`)
        .val();
    }

    const transitCasePaid = this.$formWrapper.find('#js-paid-carrier-transit');
    const transitCaseFree = this.$formWrapper.find('#js-free-carrier-transit');

    if (isFreeShipping) {
      const transitContent = transitCaseFree.data('carrier-transit').replace(
        '__TRANSIT_TIME__', `<b>${transitValue}</b>`,
      );
      transitCaseFree.html(transitContent);
      transitCaseFree.show();
      transitCasePaid.hide();
    } else {
      const transitContent = transitCaseFree.data('carrier-transit').replace(
        '__TRANSIT_TIME__', `<b>${transitValue}</b>`,
      );
      transitCasePaid.html(transitContent);
      transitCasePaid.show();
      transitCaseFree.hide();
    }
  }

  summarizeShippingCost(isFreeShipping) {
    const billingCasePrice = this.$formWrapper.find('#js-carrier-shipping-cost-price');
    const billingCaseWeight = this.$formWrapper.find('#js-carrier-shipping-cost-weight');

    if (isFreeShipping) {
      billingCasePrice.hide();
      billingCaseWeight.hide();

      return;
    }

    const selectedTaxRule = this.$taxRuleSelect.find(`option[value="${this.$taxRuleSelect.val()}"]`).text();

    // when billing by price is selected
    if (this.$billing.find('input:checked').val() === '2') {
      const billingContent = billingCasePrice.data('carrier-shipping-cost')
        .replace('__TAX_RULE__', `<b>${selectedTaxRule}</b>`);
      billingCasePrice.html(billingContent);
      billingCasePrice.show();
      billingCaseWeight.hide();

      // when nothing or billing by weight is selected
    } else {
      const billingContent = billingCaseWeight.data('carrier-shipping-cost')
        .replace('__TAX_RULE__', `<b>${selectedTaxRule}</b>`);
      billingCaseWeight.html(billingContent);
      billingCaseWeight.show();
      billingCasePrice.hide();
    }
  }
}
