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

/**
 * Change symbol when the currency select is changed
 */
export default class CurrencySymbolUpdater {
  currencySymbolSelect: string;

  selectCurrency: HTMLSelectElement | null;

  callbackChange: (symbol: string) => void;

  constructor(
    currencySymbolSelect: string,
    callbackChange: (symbol: string) => void,
  ) {
    this.currencySymbolSelect = currencySymbolSelect;
    this.selectCurrency = document.querySelector<HTMLSelectElement>(this.currencySymbolSelect);
    this.callbackChange = callbackChange;

    if (!this.selectCurrency) {
      console.error(`Could not find ${this.currencySymbolSelect}`);
    } else {
      this.init();
    }
  }

  private init(): void {
    const selectCurrency = document.querySelector<HTMLSelectElement>(this.currencySymbolSelect);

    if (selectCurrency) {
      this.callbackChange(this.getSymbol());

      selectCurrency.addEventListener('change', () => this.callbackChange(this.getSymbol()));
    }
  }

  public getSymbol(): string {
    if (!this.selectCurrency) {
      return '';
    }

    const defaultCurrencySymbol: string | null = this.selectCurrency.dataset.defaultCurrencySymbol ?? '';
    const selectItem = this.selectCurrency.item(this.selectCurrency.selectedIndex);

    if (!defaultCurrencySymbol && !selectItem) {
      console.error('Could not find appropriate data attributes');
    }

    if (!selectItem) {
      return defaultCurrencySymbol;
    }

    return selectItem.getAttribute('symbol') ?? defaultCurrencySymbol;
  }
}
