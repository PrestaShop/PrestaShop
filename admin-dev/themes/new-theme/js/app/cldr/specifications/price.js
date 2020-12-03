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
import LocalizationException from '@app/cldr/exception/localization';
import NumberSpecification from '@app/cldr/specifications/number';

/**
 * Currency display option: symbol notation.
 */
const CURRENCY_DISPLAY_SYMBOL = 'symbol';


class PriceSpecification extends NumberSpecification {
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
}

export default PriceSpecification;
