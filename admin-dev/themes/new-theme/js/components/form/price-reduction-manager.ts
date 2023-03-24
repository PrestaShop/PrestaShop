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
import CurrencySymbolUpdater from '@components/form/currency-symbol-updater';

const {$} = window;

/**
 * Handles dynamics (shows/hides fields, changes currency symbols) of price reduction form fields
 */
export default class PriceReductionManager {
  reductionTypeSelector: string;

  $reductionTypeSelect: JQuery;

  $taxInclusionInputs: JQuery;

  currencySelect: string;

  reductionValueSymbolSelector: string;

  hideCurrencyOnPercentageType: boolean;

  updateCurrencySymbol: boolean;

  constructor(
    reductionTypeSelector: string,
    taxInclusionInputs: string,
    currencySelect: string,
    reductionValueSymbolSelector: string,
    hideCurrencyOnPercentageType: boolean = false,
    updateCurrencySymbol = true,
  ) {
    this.reductionTypeSelector = reductionTypeSelector;
    this.$reductionTypeSelect = $(reductionTypeSelector);
    this.$taxInclusionInputs = $(taxInclusionInputs);
    this.currencySelect = currencySelect;
    this.reductionValueSymbolSelector = reductionValueSymbolSelector;
    this.hideCurrencyOnPercentageType = hideCurrencyOnPercentageType;
    this.updateCurrencySymbol = updateCurrencySymbol;
    this.handle();
    this.$reductionTypeSelect.on('change', () => this.handle());
  }

  /**
   * When source value is 'percentage', target field is shown, else hidden
   */
  private handle(): void {
    if (this.updateCurrencySymbol) {
      this.initCurrencySymbolUpdater();
    }

    const isPercentage = this.$reductionTypeSelect.val() === 'percentage';

    if (isPercentage) {
      this.$taxInclusionInputs.fadeOut();
      if (this.hideCurrencyOnPercentageType) {
        $(this.currencySelect).fadeOut();
      }
    } else {
      this.$taxInclusionInputs.fadeIn();
      if (this.hideCurrencyOnPercentageType) {
        $(this.currencySelect).fadeIn();
      }
    }

    if (this.reductionValueSymbolSelector !== '') {
      const reductionTypeAmountSymbols = document.querySelectorAll(this.reductionValueSymbolSelector);

      if (reductionTypeAmountSymbols.length) {
        reductionTypeAmountSymbols.forEach((value: Element) => {
          // eslint-disable-next-line no-param-reassign
          value.innerHTML = isPercentage ? '%' : this.getSymbol(value.innerHTML);
        });
      }
    }
  }

  private getSymbol(defaultValue: string): string {
    const select = document.querySelector<HTMLSelectElement>(this.currencySelect);

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

  private initCurrencySymbolUpdater(): void {
    new CurrencySymbolUpdater(
      this.currencySelect,
      ((symbol: string): void => {
        if (symbol === '') {
          return;
        }

        const reductionTypeSelect = <HTMLSelectElement> document.querySelector(this.reductionTypeSelector);

        if (reductionTypeSelect) {
          for (let i = 0; i < reductionTypeSelect.options.length; i += 1) {
            const reductionOption = reductionTypeSelect.options[i];

            if (reductionOption.value === 'amount') {
              // Update reduction amount choice symbol
              reductionOption.innerHTML = symbol;
            }
          }

          const selectedReduction = reductionTypeSelect.options[reductionTypeSelect.selectedIndex].value;

          if (selectedReduction === 'amount') {
            const reductionTypeAmountSymbols = document.querySelectorAll(this.reductionValueSymbolSelector);

            if (reductionTypeAmountSymbols.length) {
              // Update reduction value field symbol when "amount" type is selected
              reductionTypeAmountSymbols.forEach((value: Element) => {
                // eslint-disable-next-line no-param-reassign
                value.innerHTML = symbol;
              });
            }
          }
        }
      }),
    );
  }
}
