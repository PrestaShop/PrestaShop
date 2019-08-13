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
/**
 * These placeholders are used in CLDR number formatting templates.
 * They are meant to be replaced by the correct localized symbols in the number formatting process.
 */
import NumberSymbol from './number-symbol';
import PriceSpecification from './specifications/price';
import NumberSpecification from './specifications/number';

const escapeRE = require('lodash.escaperegexp');

const CURRENCY_SYMBOL_PLACEHOLDER = '¤';
const DECIMAL_SEPARATOR_PLACEHOLDER = '.';
const GROUP_SEPARATOR_PLACEHOLDER = ',';
const MINUS_SIGN_PLACEHOLDER = '-';
const PERCENT_SYMBOL_PLACEHOLDER = '%';
const PLUS_SIGN_PLACEHOLDER = '+';

class NumberFormatter {
  /**
   * @param NumberSpecification specification Number specification to be used
   *   (can be a number spec, a price spec, a percentage spec)
   */
  constructor(specification) {
    this.numberSpecification = specification;
  }

  /**
   * Formats the passed number according to specifications.
   *
   * @param int|float|string number The number to format
   * @param NumberSpecification specification Number specification to be used
   *   (can be a number spec, a price spec, a percentage spec)
   *
   * @return string The formatted number
   *                You should use this this value for display, without modifying it
   */
  format(number, specification) {
    if (specification !== undefined) {
      this.numberSpecification = specification;
    }

    /*
     * We need to work on the absolute value first.
     * Then the CLDR pattern will add the sign if relevant (at the end).
     */
    const num = Math.abs(number).toFixed(this.numberSpecification.getMaxFractionDigits());

    let [majorDigits, minorDigits] = this.extractMajorMinorDigits(num);
    majorDigits = this.splitMajorGroups(majorDigits);
    minorDigits = this.adjustMinorDigitsZeroes(minorDigits);

    // Assemble the final number
    let formattedNumber = majorDigits;
    if (minorDigits) {
      formattedNumber += DECIMAL_SEPARATOR_PLACEHOLDER + minorDigits;
    }

    // Get the good CLDR formatting pattern. Sign is important here !
    const pattern = this.getCldrPattern(majorDigits < 0);
    formattedNumber = this.addPlaceholders(formattedNumber, pattern);
    formattedNumber = this.replaceSymbols(formattedNumber);

    formattedNumber = this.performSpecificReplacements(formattedNumber);

    return formattedNumber;
  }

  /**
   * Get number's major and minor digits.
   *
   * Major digits are the "integer" part (before decimal separator),
   * minor digits are the fractional part
   * Result will be an array of exactly 2 items: [majorDigits, minorDigits]
   *
   * Usage example:
   *  list(majorDigits, minorDigits) = this.getMajorMinorDigits(decimalNumber);
   *
   * @param DecimalNumber number
   *
   * @return string[]
   */
  extractMajorMinorDigits(number) {
    // Get the number's major and minor digits.
    const result = number.toString().split('.');
    const majorDigits = result[0];
    const minorDigits = (result[1] === undefined) ? '' : result[1];
    return [majorDigits, minorDigits];
  }

  /**
   * Splits major digits into groups.
   *
   * e.g.: Given the major digits "1234567", and major group size
   *  configured to 3 digits, the result would be "1 234 567"
   *
   * @param string majorDigits The major digits to be grouped
   *
   * @return string The grouped major digits
   */
  splitMajorGroups(digit) {
    if (!this.numberSpecification.isGroupingUsed()) {
      return digit;
    }

    // Reverse the major digits, since they are grouped from the right.
    const majorDigits = digit.split('').reverse();

    // Group the major digits.
    let groups = [];
    groups.push(majorDigits.splice(0, this.numberSpecification.getPrimaryGroupSize()));
    while (majorDigits.length) {
      groups.push(majorDigits.splice(0, this.numberSpecification.getSecondaryGroupSize()));
    }

    // Reverse back the digits and the groups
    groups = groups.reverse();
    const newGroups = [];
    groups.forEach((group) => {
      newGroups.push(group.reverse().join(''));
    });

    // Reconstruct the major digits.
    return newGroups.join(GROUP_SEPARATOR_PLACEHOLDER);
  }

  /**
   * Adds or remove trailing zeroes, depending on specified min and max fraction digits numbers.
   *
   * @param string minorDigits Digits to be adjusted with (trimmed or padded) zeroes
   *
   * @return string The adjusted minor digits
   */
  adjustMinorDigitsZeroes(minorDigits) {
    let digit = minorDigits;
    if (digit.length > this.numberSpecification.getMaxFractionDigits()) {
      // Strip any trailing zeroes.
      digit = digit.replace(/0+$/, '');
    }

    if (digit.length < this.numberSpecification.getMinFractionDigits()) {
      // Re-add needed zeroes
      digit = digit.padEnd(
        this.numberSpecification.getMinFractionDigits(),
        '0',
      );
    }

    return digit;
  }

  /**
   * Get the CLDR formatting pattern.
   *
   * @see http://cldr.unicode.org/translation/number-patterns
   *
   * @param bool isNegative If true, the negative pattern
   * will be returned instead of the positive one
   *
   * @return string The CLDR formatting pattern
   */
  getCldrPattern(isNegative) {
    if (isNegative) {
      return this.numberSpecification.getNegativePattern();
    }

    return this.numberSpecification.getPositivePattern();
  }

  /**
   * Replace placeholder number symbols with relevant numbering system's symbols.
   *
   * @param string number
   *                       The number to process
   *
   * @return string
   *                The number with replaced symbols
   */
  replaceSymbols(number) {
    const symbols = this.numberSpecification.getSymbol();

    const map = {};
    map[DECIMAL_SEPARATOR_PLACEHOLDER] = symbols.getDecimal();
    map[GROUP_SEPARATOR_PLACEHOLDER] = symbols.getGroup();
    map[MINUS_SIGN_PLACEHOLDER] = symbols.getMinusSign();
    map[PERCENT_SYMBOL_PLACEHOLDER] = symbols.getPercentSign();
    map[PLUS_SIGN_PLACEHOLDER] = symbols.getPlusSign();

    return this.strtr(number, map);
  }

  /**
   * strtr() for JavaScript
   * Translate characters or replace substrings
   *
   * @param str
   *  String to parse
   * @param pairs
   *  Hash of ('from' => 'to', ...).
   *
   * @return string
   */
  strtr(str, pairs) {
    const substrs = Object.keys(pairs).map(escapeRE);
    return str.split(RegExp(`(${substrs.join('|')})`))
              .map(part => pairs[part] || part)
              .join('');
  }


  /**
   * Add missing placeholders to the number using the passed CLDR pattern.
   *
   * Missing placeholders can be the percent sign, currency symbol, etc.
   *
   * e.g. with a currency CLDR pattern:
   *  - Passed number (partially formatted): 1,234.567
   *  - Returned number: 1,234.567 ¤
   *  ("¤" symbol is the currency symbol placeholder)
   *
   * @see http://cldr.unicode.org/translation/number-patterns
   *
   * @param formattedNumber
   *  Number to process
   * @param pattern
   *  CLDR formatting pattern to use
   *
   * @return string
   */
  addPlaceholders(formattedNumber, pattern) {
    /*
     * Regex groups explanation:
     * #          : literal "#" character. Once.
     * (,#+)*     : any other "#" characters group, separated by ",". Zero to infinity times.
     * 0          : literal "0" character. Once.
     * (\.[0#]+)* : any combination of "0" and "#" characters groups, separated by '.'.
     *              Zero to infinity times.
     */
    return pattern.replace(/#?(,#+)*0(\.[0#]+)*/, formattedNumber);
  }

  /**
   * Perform some more specific replacements.
   *
   * Specific replacements are needed when number specification is extended.
   * For instance, prices have an extended number specification in order to
   * add currency symbol to the formatted number.
   *
   * @param string formattedNumber
   *
   * @return mixed
   */
  performSpecificReplacements(formattedNumber) {
    if (this.numberSpecification instanceof PriceSpecification) {
      return formattedNumber
        .split(CURRENCY_SYMBOL_PLACEHOLDER)
        .join(this.numberSpecification.getCurrencySymbol());
    }

    return formattedNumber;
  }

  static build(specifications) {
    const symbol = new NumberSymbol(...specifications.symbol);
    let specification;
    if (specifications.currencySymbol) {
      specification = new PriceSpecification(
        specifications.positivePattern,
        specifications.negativePattern,
        symbol,
        parseInt(specifications.maxFractionDigits, 10),
        parseInt(specifications.minFractionDigits, 10),
        specifications.groupingUsed,
        specifications.primaryGroupSize,
        specifications.secondaryGroupSize,
        specifications.currencySymbol,
        specifications.currencyCode,
      );
    } else {
      specification = new NumberSpecification(
        specifications.positivePattern,
        specifications.negativePattern,
        symbol,
        parseInt(specifications.maxFractionDigits, 10),
        parseInt(specifications.minFractionDigits, 10),
        specifications.groupingUsed,
        specifications.primaryGroupSize,
        specifications.secondaryGroupSize,
      );
    }

    return new NumberFormatter(specification);
  }
}

export default NumberFormatter;
