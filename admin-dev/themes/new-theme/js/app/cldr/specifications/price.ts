/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import LocalizationException from '@app/cldr/exception/localization';
import NumberSpecification from '@app/cldr/specifications/number';
import NumberSymbol from '@app/cldr/number-symbol';

/**
 * Currency display option: symbol notation.
 */
const CURRENCY_DISPLAY_SYMBOL = 'symbol';

class PriceSpecification extends NumberSpecification {
  currencySymbol: string;

  currencyCode: string;

  /**
   * Price specification constructor.
   *
   * @param string positivePattern CLDR formatting pattern for positive amounts
   * @param string negativePattern CLDR formatting pattern for negative amounts
   * @param NumberSymbol symbol Number symbol
   * @param int maxFractionDigits Maximum number of digits after decimal separator
   * @param int minFractionDigits Minimum number of digits after decimal separator
   * @param bool groupingUsed Is digits grouping used ?
   * @param int primaryGroupSize Size of primary digits group in the number
   * @param int secondaryGroupSize Size of secondary digits group in the number
   * @param string currencySymbol Currency symbol of this price (eg. : €)
   * @param currencyCode Currency code of this price (e.g.: EUR)
   *
   * @throws LocalizationException
   */
  constructor(
    positivePattern: string,
    negativePattern: string,
    symbol: NumberSymbol,
    maxFractionDigits: number,
    minFractionDigits: number,
    groupingUsed: boolean,
    primaryGroupSize: number,
    secondaryGroupSize: number,
    currencySymbol: string,
    currencyCode: string,
  ) {
    super(
      positivePattern,
      negativePattern,
      symbol,
      maxFractionDigits,
      minFractionDigits,
      groupingUsed,
      primaryGroupSize,
      secondaryGroupSize,
    );
    this.currencySymbol = currencySymbol;
    this.currencyCode = currencyCode;

    if (!this.currencySymbol || typeof this.currencySymbol !== 'string') {
      throw new LocalizationException('Invalid currencySymbol');
    }

    if (!this.currencyCode || typeof this.currencyCode !== 'string') {
      throw new LocalizationException('Invalid currencyCode');
    }
  }

  /**
   * Get type of display for currency symbol.
   *
   * @return string
   */
  static getCurrencyDisplay(): string {
    return CURRENCY_DISPLAY_SYMBOL;
  }

  /**
   * Get the currency symbol
   * e.g.: €.
   *
   * @return string
   */
  getCurrencySymbol(): string {
    return this.currencySymbol;
  }

  /**
   * Get the currency ISO code
   * e.g.: EUR.
   *
   * @return string
   */
  getCurrencyCode(): string {
    return this.currencyCode;
  }
}

export default PriceSpecification;
