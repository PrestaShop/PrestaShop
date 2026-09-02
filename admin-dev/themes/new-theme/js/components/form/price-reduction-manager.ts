/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import CurrencySymbolUpdater from '@components/form/currency-symbol-updater';

const {$} = window;

/**
 * Handles dynamics (shows/hides fields, changes currency symbols) of price reduction form fields
 */
export default class PriceReductionManager {
  private readonly reductionTypeSelector: string;

  private readonly $reductionTypeSelect: JQuery;

  private readonly $taxInclusionInputs: JQuery;

  private readonly $reductionValueInputs: JQuery;

  private readonly currencySelect: string;

  private readonly reductionValueSymbolSelector: string;

  private readonly currencySymbolUpdater: CurrencySymbolUpdater;

  private readonly toggleCurrencySelector: string | null;

  constructor(
    reductionTypeSelector: string,
    taxInclusionInputs: string,
    currencySelect: string,
    reductionValueSymbolSelector: string,
    reductionValueSelector: string,
    toggleCurrencySelector: string | null = null,
  ) {
    this.reductionTypeSelector = reductionTypeSelector;
    this.$reductionTypeSelect = $(reductionTypeSelector);
    this.$taxInclusionInputs = $(taxInclusionInputs);
    this.$reductionValueInputs = $(reductionValueSelector);
    this.currencySelect = currencySelect;
    this.reductionValueSymbolSelector = reductionValueSymbolSelector;
    this.toggleCurrencySelector = toggleCurrencySelector;

    this.currencySymbolUpdater = new CurrencySymbolUpdater(
      this.currencySelect,
      ((symbol: string): void => {
        if (symbol === '') {
          return;
        }

        this.updateSymbol(symbol);
      }),
    );

    this.handle();
    this.$reductionTypeSelect.on('change', () => this.handle());
  }

  /**
   * Update reduction fields according to the selected reduction type.
   */
  private handle(): void {
    const isPercentage = this.$reductionTypeSelect.val() === 'percentage';

    this.$reductionValueInputs.toggleClass(
      'js-comma-transformer',
      !isPercentage,
    );

    if (isPercentage) {
      this.$taxInclusionInputs.fadeOut();

      if (this.toggleCurrencySelector) {
        $(this.toggleCurrencySelector).fadeOut();
      }
    } else {
      this.$taxInclusionInputs.fadeIn();

      if (this.toggleCurrencySelector) {
        $(this.toggleCurrencySelector).fadeIn();
      }
    }

    this.updateSymbol(this.currencySymbolUpdater.getSymbol());
  }

  private updateSymbol(symbol: string): void {
    const reductionTypeSelect = <HTMLSelectElement> document.querySelector(
      this.reductionTypeSelector,
    );

    if (reductionTypeSelect) {
      for (let i = 0; i < reductionTypeSelect.options.length; i += 1) {
        const reductionOption = reductionTypeSelect.options[i];

        if (reductionOption.value === 'amount') {
          // Update reduction type choice "amount" symbol
          reductionOption.innerHTML = symbol;
        }
      }

      const selectedReduction = <string> reductionTypeSelect.options[
        reductionTypeSelect.selectedIndex
      ].value;

      const reductionValueSymbols = <NodeListOf<HTMLSelectElement>>
        document.querySelectorAll(this.reductionValueSymbolSelector);

      if (reductionValueSymbols.length === 0) {
        return;
      }

      // Update reduction value field symbol when "amount" type is selected
      reductionValueSymbols.forEach((value: Element) => {
        // eslint-disable-next-line no-param-reassign
        value.innerHTML = selectedReduction === 'amount' ? symbol : '%';
      });
    }
  }
}
