/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import LocalizationException from '@app/cldr/exception/localization';

class NumberSymbol {
  decimal: string;

  group: string;

  list: string;

  percentSign: string;

  minusSign: string;

  plusSign: string;

  exponential: string;

  superscriptingExponent: string;

  perMille: string;

  infinity: string;

  nan: string;

  /**
   * NumberSymbolList constructor.
   *
   * @param string decimal Decimal separator character
   * @param string group Digits group separator character
   * @param string list List elements separator character
   * @param string percentSign Percent sign character
   * @param string minusSign Minus sign character
   * @param string plusSign Plus sign character
   * @param string exponential Exponential character
   * @param string superscriptingExponent Superscripting exponent character
   * @param string perMille Permille sign character
   * @param string infinity The infinity sign. Corresponds to the IEEE infinity bit pattern.
   * @param string nan The NaN (Not A Number) sign. Corresponds to the IEEE NaN bit pattern.
   *
   * @throws LocalizationException
   */
  constructor(
    decimal: string,
    group: string,
    list: string,
    percentSign: string,
    minusSign: string,
    plusSign: string,
    exponential: string,
    superscriptingExponent: string,
    perMille: string,
    infinity: string,
    nan: string,
  ) {
    this.decimal = decimal;
    this.group = group;
    this.list = list;
    this.percentSign = percentSign;
    this.minusSign = minusSign;
    this.plusSign = plusSign;
    this.exponential = exponential;
    this.superscriptingExponent = superscriptingExponent;
    this.perMille = perMille;
    this.infinity = infinity;
    this.nan = nan;

    this.validateData();
  }

  /**
   * Get the decimal separator.
   *
   * @return string
   */
  getDecimal(): string {
    return this.decimal;
  }

  /**
   * Get the digit groups separator.
   *
   * @return string
   */
  getGroup(): string {
    return this.group;
  }

  /**
   * Get the list elements separator.
   *
   * @return string
   */
  getList(): string {
    return this.list;
  }

  /**
   * Get the percent sign.
   *
   * @return string
   */
  getPercentSign(): string {
    return this.percentSign;
  }

  /**
   * Get the minus sign.
   *
   * @return string
   */
  getMinusSign(): string {
    return this.minusSign;
  }

  /**
   * Get the plus sign.
   *
   * @return string
   */
  getPlusSign(): string {
    return this.plusSign;
  }

  /**
   * Get the exponential character.
   *
   * @return string
   */
  getExponential(): string {
    return this.exponential;
  }

  /**
   * Get the exponent character.
   *
   * @return string
   */
  getSuperscriptingExponent(): string {
    return this.superscriptingExponent;
  }

  /**
   * Gert the per mille symbol (often "‰").
   *
   * @see https://en.wikipedia.org/wiki/Per_mille
   *
   * @return string
   */
  getPerMille(): string {
    return this.perMille;
  }

  /**
   * Get the infinity symbol (often "∞").
   *
   * @see https://en.wikipedia.org/wiki/Infinity_symbol
   *
   * @return string
   */
  getInfinity(): string {
    return this.infinity;
  }

  /**
   * Get the NaN (not a number) sign.
   *
   * @return string
   */
  getNan(): string {
    return this.nan;
  }

  /**
   * Symbols list validation.
   *
   * @throws LocalizationException
   */
  validateData(): void {
    if (!this.decimal || typeof this.decimal !== 'string') {
      throw new LocalizationException('Invalid decimal');
    }

    if (!this.group || typeof this.group !== 'string') {
      throw new LocalizationException('Invalid group');
    }

    if (!this.list || typeof this.list !== 'string') {
      throw new LocalizationException('Invalid symbol list');
    }

    if (!this.percentSign || typeof this.percentSign !== 'string') {
      throw new LocalizationException('Invalid percentSign');
    }

    if (!this.minusSign || typeof this.minusSign !== 'string') {
      throw new LocalizationException('Invalid minusSign');
    }

    if (!this.plusSign || typeof this.plusSign !== 'string') {
      throw new LocalizationException('Invalid plusSign');
    }

    if (!this.exponential || typeof this.exponential !== 'string') {
      throw new LocalizationException('Invalid exponential');
    }

    if (!this.superscriptingExponent || typeof this.superscriptingExponent !== 'string') {
      throw new LocalizationException('Invalid superscriptingExponent');
    }

    if (!this.perMille || typeof this.perMille !== 'string') {
      throw new LocalizationException('Invalid perMille');
    }

    if (!this.infinity || typeof this.infinity !== 'string') {
      throw new LocalizationException('Invalid infinity');
    }

    if (!this.nan || typeof this.nan !== 'string') {
      throw new LocalizationException('Invalid nan');
    }
  }
}

export default NumberSymbol;
