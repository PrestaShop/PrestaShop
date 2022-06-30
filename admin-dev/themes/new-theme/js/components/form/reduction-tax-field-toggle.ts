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

const {$} = window;

/**
 * Shows/hides 'include_tax' field depending from 'reduction_type' field value
 */
export default class ReductionTaxFieldToggle {
  $reductionTypeSelector: JQuery;

  $taxInclusionInputs: JQuery;

  currencySymbolSelect: string;

  reductionAmountSymbolSelector: string;

  constructor(
    reductionTypeSelector: string,
    taxInclusionInputs: string,
    currencySymbolSelect: string,
    reductionAmountSymbolSelector:string,
  ) {
    this.$reductionTypeSelector = $(reductionTypeSelector);
    this.$taxInclusionInputs = $(taxInclusionInputs);
    this.currencySymbolSelect = currencySymbolSelect;
    this.reductionAmountSymbolSelector = reductionAmountSymbolSelector;
    this.handle();
    this.$reductionTypeSelector.on('change', () => this.handle());
  }

  /**
   * When source value is 'percentage', target field is shown, else hidden
   */
  private handle(): void {
    const isPercentage = this.$reductionTypeSelector.val() === 'percentage';

    if (isPercentage) {
      this.$taxInclusionInputs.fadeOut();
    } else {
      this.$taxInclusionInputs.fadeIn();
    }

    if (this.reductionAmountSymbolSelector !== '') {
      const reductionTypeAmountSymbols = document.querySelectorAll(this.reductionAmountSymbolSelector);

      if (reductionTypeAmountSymbols.length) {
        reductionTypeAmountSymbols.forEach((value: Element) => {
          const elt = value;
          elt.innerHTML = isPercentage ? '%' : this.getSymbol(value.innerHTML);
        });
      }
    }
  }

  private getSymbol(defaultValue: string): string {
    const select = document.querySelector<HTMLSelectElement>(this.currencySymbolSelect);

    if (!select) {
      return defaultValue;
    }

    const defaultCurrencySymbol: string = select.dataset.defaultCurrencySymbol ?? '';
    const selectItem = select.item(select.selectedIndex);

    if (!selectItem) {
      return defaultCurrencySymbol;
    }
    return selectItem.getAttribute('symbol') ?? defaultCurrencySymbol;
  }
}
