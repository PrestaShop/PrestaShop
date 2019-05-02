/**
 * 2007-2019 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import LocalizationException from './exception/localization';
import NumberSymbol from './number-symbol';

/**
 * Currency display option: symbol notation.
 */
const CURRENCY_DISPLAY_SYMBOL = 'symbol';


class PriceSpecification {
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
    positivePattern,
    negativePattern,
    symbol,
    maxFractionDigits,
    minFractionDigits,
    groupingUsed,
    primaryGroupSize,
    secondaryGroupSize,
    currencySymbol,
    currencyCode,
  ) {
    this.currencySymbol = currencySymbol;
    this.currencyCode = currencyCode;

    this.positivePattern = positivePattern;
    this.negativePattern = negativePattern;
    this.symbol = symbol;

    this.maxFractionDigits = maxFractionDigits;
    // eslint-disable-next-line
    this.minFractionDigits = maxFractionDigits < minFractionDigits ? maxFractionDigits : minFractionDigits;

    this.groupingUsed = groupingUsed;
    this.primaryGroupSize = primaryGroupSize;
    this.secondaryGroupSize = secondaryGroupSize;

    this.validateData();
  }

  /**
   * Get type of display for currency symbol.
   *
   * @return string
   */
  static getCurrencyDisplay() {
    return CURRENCY_DISPLAY_SYMBOL;
  }

  /**
   * Get the currency symbol
   * e.g.: €.
   *
   * @return string
   */
  getCurrencySymbol() {
    return this.currencySymbol;
  }

  /**
   * Get the currency ISO code
   * e.g.: EUR.
   *
   * @return string
   */
  getCurrencyCode() {
    return this.currencyCode;
  }

  /**
   * Data (attributes) validation.
   *
   * @throws LocalizationException
   */
  validateData() {
    if (!this.positivePattern || typeof this.positivePattern !== 'string') {
      throw new LocalizationException('Invalid positivePattern');
    }

    if (!this.negativePattern || typeof this.negativePattern !== 'string') {
      throw new LocalizationException('Invalid negativePattern');
    }

    if (!this.symbol || !(this.symbol instanceof NumberSymbol)) {
      throw new LocalizationException('Invalid symbol');
    }

    if (typeof this.maxFractionDigits !== 'number') {
      throw new LocalizationException('Invalid maxFractionDigits');
    }

    if (typeof this.minFractionDigits !== 'number') {
      throw new LocalizationException('Invalid minFractionDigits');
    }

    if (typeof this.groupingUsed !== 'boolean') {
      throw new LocalizationException('Invalid groupingUsed');
    }

    if (typeof this.primaryGroupSize !== 'number') {
      throw new LocalizationException('Invalid primaryGroupSize');
    }

    if (typeof this.secondaryGroupSize !== 'number') {
      throw new LocalizationException('Invalid secondaryGroupSize');
    }

    if (!this.currencySymbol || typeof this.currencySymbol !== 'string') {
      throw new LocalizationException('Invalid currencySymbol');
    }

    if (!this.currencyCode || typeof this.currencyCode !== 'string') {
      throw new LocalizationException('Invalid currencyCode');
    }
  }

  /**
   * Get symbol.
   *
   * @return NumberSymbol
   */
  getSymbol() {
    return this.symbol;
  }

  /**
   * Get the formatting rules for this number (when positive).
   *
   * This pattern uses the Unicode CLDR number pattern syntax
   *
   * @return string
   */
  getPositivePattern() {
    return this.positivePattern;
  }

  /**
   * Get the formatting rules for this number (when negative).
   *
   * This pattern uses the Unicode CLDR number pattern syntax
   *
   * @return string
   */
  getNegativePattern() {
    return this.negativePattern;
  }

  /**
   * Get the maximum number of digits after decimal separator (rounding if needed).
   *
   * @return int
   */
  getMaxFractionDigits() {
    return this.maxFractionDigits;
  }

  /**
   * Get the minimum number of digits after decimal separator (fill with "0" if needed).
   *
   * @return int
   */
  getMinFractionDigits() {
    return this.minFractionDigits;
  }

  /**
   * Get the "grouping" flag. This flag defines if digits
   * grouping should be used when formatting this number.
   *
   * @return bool
   */
  isGroupingUsed() {
    return this.groupingUsed;
  }

  /**
   * Get the size of primary digits group in the number.
   *
   * @return int
   */
  getPrimaryGroupSize() {
    return this.primaryGroupSize;
  }

  /**
   * Get the size of secondary digits groups in the number.
   *
   * @return int
   */
  getSecondaryGroupSize() {
    return this.secondaryGroupSize;
  }
}

export default PriceSpecification;
