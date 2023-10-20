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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import LocalizationException from '@app/cldr/exception/localization';
import NumberSymbol from '@app/cldr/number-symbol';

class NumberSpecification {
  positivePattern: string;

  negativePattern: string;

  symbol: NumberSymbol;

  maxFractionDigits: number;

  minFractionDigits: number;

  groupingUsed: boolean;

  primaryGroupSize: number;

  secondaryGroupSize: number;

  /**
   * Number specification constructor.
   *
   * @param string positivePattern CLDR formatting pattern for positive amounts
   * @param string negativePattern CLDR formatting pattern for negative amounts
   * @param NumberSymbol symbol Number symbol
   * @param int maxFractionDigits Maximum number of digits after decimal separator
   * @param int minFractionDigits Minimum number of digits after decimal separator
   * @param bool groupingUsed Is digits grouping used ?
   * @param int primaryGroupSize Size of primary digits group in the number
   * @param int secondaryGroupSize Size of secondary digits group in the number
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
  ) {
    this.positivePattern = positivePattern;
    this.negativePattern = negativePattern;
    this.symbol = symbol;

    this.maxFractionDigits = maxFractionDigits;
    // eslint-disable-next-line
    this.minFractionDigits =
      maxFractionDigits < minFractionDigits
        ? maxFractionDigits
        : minFractionDigits;

    this.groupingUsed = groupingUsed;
    this.primaryGroupSize = primaryGroupSize;
    this.secondaryGroupSize = secondaryGroupSize;

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
  }

  /**
   * Get symbol.
   *
   * @return NumberSymbol
   */
  getSymbol(): NumberSymbol {
    return this.symbol;
  }

  /**
   * Get the formatting rules for this number (when positive).
   *
   * This pattern uses the Unicode CLDR number pattern syntax
   *
   * @return string
   */
  getPositivePattern(): string {
    return this.positivePattern;
  }

  /**
   * Get the formatting rules for this number (when negative).
   *
   * This pattern uses the Unicode CLDR number pattern syntax
   *
   * @return string
   */
  getNegativePattern(): string {
    return this.negativePattern;
  }

  /**
   * Get the maximum number of digits after decimal separator (rounding if needed).
   *
   * @return int
   */
  getMaxFractionDigits(): number {
    return this.maxFractionDigits;
  }

  /**
   * Get the minimum number of digits after decimal separator (fill with "0" if needed).
   *
   * @return int
   */
  getMinFractionDigits(): number {
    return this.minFractionDigits;
  }

  /**
   * Get the "grouping" flag. This flag defines if digits
   * grouping should be used when formatting this number.
   *
   * @return bool
   */
  isGroupingUsed(): boolean {
    return this.groupingUsed;
  }

  /**
   * Get the size of primary digits group in the number.
   *
   * @return int
   */
  getPrimaryGroupSize(): number {
    return this.primaryGroupSize;
  }

  /**
   * Get the size of secondary digits groups in the number.
   *
   * @return int
   */
  getSecondaryGroupSize(): number {
    return this.secondaryGroupSize;
  }
}

export default NumberSpecification;
