/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
