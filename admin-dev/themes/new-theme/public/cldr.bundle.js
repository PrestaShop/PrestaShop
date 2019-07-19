window["cldr"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 296);
/******/ })
/************************************************************************/
/******/ ({

/***/ 226:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
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


var _numberSymbol = __webpack_require__(62);

var _numberSymbol2 = _interopRequireDefault(_numberSymbol);

var _price = __webpack_require__(88);

var _price2 = _interopRequireDefault(_price);

var _number = __webpack_require__(63);

var _number2 = _interopRequireDefault(_number);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var CURRENCY_SYMBOL_PLACEHOLDER = '¤';
var DECIMAL_SEPARATOR_PLACEHOLDER = '.';
var GROUP_SEPARATOR_PLACEHOLDER = ',';
var MINUS_SIGN_PLACEHOLDER = '-';
var PERCENT_SYMBOL_PLACEHOLDER = '%';
var PLUS_SIGN_PLACEHOLDER = '+';

var NumberFormatter = function () {
  /**
   * @param NumberSpecification specification Number specification to be used
   *   (can be a number spec, a price spec, a percentage spec)
   */
  function NumberFormatter(specification) {
    _classCallCheck(this, NumberFormatter);

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


  _createClass(NumberFormatter, [{
    key: 'format',
    value: function format(number, specification) {
      if (specification !== undefined) {
        this.numberSpecification = specification;
      }

      /*
       * We need to work on the absolute value first.
       * Then the CLDR pattern will add the sign if relevant (at the end).
       */
      var num = Math.abs(number).toFixed(this.numberSpecification.getMaxFractionDigits());

      var _extractMajorMinorDig = this.extractMajorMinorDigits(num),
          _extractMajorMinorDig2 = _slicedToArray(_extractMajorMinorDig, 2),
          majorDigits = _extractMajorMinorDig2[0],
          minorDigits = _extractMajorMinorDig2[1];

      majorDigits = this.splitMajorGroups(majorDigits);
      minorDigits = this.adjustMinorDigitsZeroes(minorDigits);

      // Assemble the final number
      var formattedNumber = majorDigits;
      if (minorDigits) {
        formattedNumber += DECIMAL_SEPARATOR_PLACEHOLDER + minorDigits;
      }

      // Get the good CLDR formatting pattern. Sign is important here !
      var pattern = this.getCldrPattern(majorDigits < 0);
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

  }, {
    key: 'extractMajorMinorDigits',
    value: function extractMajorMinorDigits(number) {
      // Get the number's major and minor digits.
      var result = number.toString().split('.');
      var majorDigits = result[0];
      var minorDigits = result[1] === undefined ? '' : result[1];
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

  }, {
    key: 'splitMajorGroups',
    value: function splitMajorGroups(digit) {
      if (!this.numberSpecification.isGroupingUsed()) {
        return digit;
      }

      // Reverse the major digits, since they are grouped from the right.
      var majorDigits = digit.split('').reverse();

      // Group the major digits.
      var groups = [];
      groups.push(majorDigits.splice(0, this.numberSpecification.getPrimaryGroupSize()));
      while (majorDigits.length) {
        groups.push(majorDigits.splice(0, this.numberSpecification.getSecondaryGroupSize()));
      }

      // Reverse back the digits and the groups
      groups = groups.reverse();
      var newGroups = [];
      groups.forEach(function (group) {
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

  }, {
    key: 'adjustMinorDigitsZeroes',
    value: function adjustMinorDigitsZeroes(minorDigits) {
      var digit = minorDigits;
      if (digit.length > this.numberSpecification.getMaxFractionDigits()) {
        // Strip any trailing zeroes.
        digit = digit.replace(/0+$/, '');
      }

      if (digit.length < this.numberSpecification.getMinFractionDigits()) {
        // Re-add needed zeroes
        digit = digit.padEnd(this.numberSpecification.getMinFractionDigits(), '0');
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

  }, {
    key: 'getCldrPattern',
    value: function getCldrPattern(isNegative) {
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

  }, {
    key: 'replaceSymbols',
    value: function replaceSymbols(number) {
      var symbols = this.numberSpecification.getSymbol();
      var num = number;
      num = num.split(DECIMAL_SEPARATOR_PLACEHOLDER).join(symbols.getDecimal());
      num = num.split(GROUP_SEPARATOR_PLACEHOLDER).join(symbols.getGroup());
      num = num.split(MINUS_SIGN_PLACEHOLDER).join(symbols.getMinusSign());
      num = num.split(PERCENT_SYMBOL_PLACEHOLDER).join(symbols.getPercentSign());
      num = num.split(PLUS_SIGN_PLACEHOLDER).join(symbols.getPlusSign());

      return num;
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

  }, {
    key: 'addPlaceholders',
    value: function addPlaceholders(formattedNumber, pattern) {
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

  }, {
    key: 'performSpecificReplacements',
    value: function performSpecificReplacements(formattedNumber) {
      if (this.numberSpecification instanceof _price2.default) {
        return formattedNumber.split(CURRENCY_SYMBOL_PLACEHOLDER).join(this.numberSpecification.getCurrencySymbol());
      }

      return formattedNumber;
    }
  }], [{
    key: 'build',
    value: function build(specifications) {
      var symbol = new (Function.prototype.bind.apply(_numberSymbol2.default, [null].concat(_toConsumableArray(specifications.symbol))))();
      var specification = void 0;
      if (specifications.currencySymbol) {
        specification = new _price2.default(specifications.positivePattern, specifications.negativePattern, symbol, parseInt(specifications.maxFractionDigits, 10), parseInt(specifications.minFractionDigits, 10), specifications.groupingUsed, specifications.primaryGroupSize, specifications.secondaryGroupSize, specifications.currencySymbol, specifications.currencyCode);
      } else {
        specification = new _number2.default(specifications.positivePattern, specifications.negativePattern, symbol, parseInt(specifications.maxFractionDigits, 10), parseInt(specifications.minFractionDigits, 10), specifications.groupingUsed, specifications.primaryGroupSize, specifications.secondaryGroupSize);
      }

      var currency = new NumberFormatter(specification);

      return currency;
    }
  }]);

  return NumberFormatter;
}();

exports.default = NumberFormatter;

/***/ }),

/***/ 296:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.NumberSymbol = exports.NumberFormatter = exports.NumberSpecification = exports.PriceSpecification = undefined;

var _numberFormatter = __webpack_require__(226);

var _numberFormatter2 = _interopRequireDefault(_numberFormatter);

var _numberSymbol = __webpack_require__(62);

var _numberSymbol2 = _interopRequireDefault(_numberSymbol);

var _price = __webpack_require__(88);

var _price2 = _interopRequireDefault(_price);

var _number = __webpack_require__(63);

var _number2 = _interopRequireDefault(_number);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
exports.PriceSpecification = _price2.default;
exports.NumberSpecification = _number2.default;
exports.NumberFormatter = _numberFormatter2.default;
exports.NumberSymbol = _numberSymbol2.default;

/***/ }),

/***/ 62:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
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


var _localization = __webpack_require__(64);

var _localization2 = _interopRequireDefault(_localization);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var NumberSymbol = function () {
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
  function NumberSymbol(decimal, group, list, percentSign, minusSign, plusSign, exponential, superscriptingExponent, perMille, infinity, nan) {
    _classCallCheck(this, NumberSymbol);

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


  _createClass(NumberSymbol, [{
    key: 'getDecimal',
    value: function getDecimal() {
      return this.decimal;
    }

    /**
     * Get the digit groups separator.
     *
     * @return string
     */

  }, {
    key: 'getGroup',
    value: function getGroup() {
      return this.group;
    }

    /**
     * Get the list elements separator.
     *
     * @return string
     */

  }, {
    key: 'getList',
    value: function getList() {
      return this.list;
    }

    /**
     * Get the percent sign.
     *
     * @return string
     */

  }, {
    key: 'getPercentSign',
    value: function getPercentSign() {
      return this.percentSign;
    }

    /**
     * Get the minus sign.
     *
     * @return string
     */

  }, {
    key: 'getMinusSign',
    value: function getMinusSign() {
      return this.minusSign;
    }

    /**
     * Get the plus sign.
     *
     * @return string
     */

  }, {
    key: 'getPlusSign',
    value: function getPlusSign() {
      return this.plusSign;
    }

    /**
     * Get the exponential character.
     *
     * @return string
     */

  }, {
    key: 'getExponential',
    value: function getExponential() {
      return this.exponential;
    }

    /**
     * Get the exponent character.
     *
     * @return string
     */

  }, {
    key: 'getSuperscriptingExponent',
    value: function getSuperscriptingExponent() {
      return this.superscriptingExponent;
    }

    /**
     * Gert the per mille symbol (often "‰").
     *
     * @see https://en.wikipedia.org/wiki/Per_mille
     *
     * @return string
     */

  }, {
    key: 'getPerMille',
    value: function getPerMille() {
      return this.perMille;
    }

    /**
     * Get the infinity symbol (often "∞").
     *
     * @see https://en.wikipedia.org/wiki/Infinity_symbol
     *
     * @return string
     */

  }, {
    key: 'getInfinity',
    value: function getInfinity() {
      return this.infinity;
    }

    /**
     * Get the NaN (not a number) sign.
     *
     * @return string
     */

  }, {
    key: 'getNan',
    value: function getNan() {
      return this.nan;
    }

    /**
     * Symbols list validation.
     *
     * @throws LocalizationException
     */

  }, {
    key: 'validateData',
    value: function validateData() {
      if (!this.decimal || typeof this.decimal !== 'string') {
        throw new _localization2.default('Invalid decimal');
      }

      if (!this.group || typeof this.group !== 'string') {
        throw new _localization2.default('Invalid group');
      }

      if (!this.list || typeof this.list !== 'string') {
        throw new _localization2.default('Invalid symbol list');
      }

      if (!this.percentSign || typeof this.percentSign !== 'string') {
        throw new _localization2.default('Invalid percentSign');
      }

      if (!this.minusSign || typeof this.minusSign !== 'string') {
        throw new _localization2.default('Invalid minusSign');
      }

      if (!this.plusSign || typeof this.plusSign !== 'string') {
        throw new _localization2.default('Invalid plusSign');
      }

      if (!this.exponential || typeof this.exponential !== 'string') {
        throw new _localization2.default('Invalid exponential');
      }

      if (!this.superscriptingExponent || typeof this.superscriptingExponent !== 'string') {
        throw new _localization2.default('Invalid superscriptingExponent');
      }

      if (!this.perMille || typeof this.perMille !== 'string') {
        throw new _localization2.default('Invalid perMille');
      }

      if (!this.infinity || typeof this.infinity !== 'string') {
        throw new _localization2.default('Invalid infinity');
      }

      if (!this.nan || typeof this.nan !== 'string') {
        throw new _localization2.default('Invalid nan');
      }
    }
  }]);

  return NumberSymbol;
}();

exports.default = NumberSymbol;

/***/ }),

/***/ 63:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
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


var _localization = __webpack_require__(64);

var _localization2 = _interopRequireDefault(_localization);

var _numberSymbol = __webpack_require__(62);

var _numberSymbol2 = _interopRequireDefault(_numberSymbol);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var NumberSpecification = function () {
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
  function NumberSpecification(positivePattern, negativePattern, symbol, maxFractionDigits, minFractionDigits, groupingUsed, primaryGroupSize, secondaryGroupSize) {
    _classCallCheck(this, NumberSpecification);

    this.positivePattern = positivePattern;
    this.negativePattern = negativePattern;
    this.symbol = symbol;

    this.maxFractionDigits = maxFractionDigits;
    // eslint-disable-next-line
    this.minFractionDigits = maxFractionDigits < minFractionDigits ? maxFractionDigits : minFractionDigits;

    this.groupingUsed = groupingUsed;
    this.primaryGroupSize = primaryGroupSize;
    this.secondaryGroupSize = secondaryGroupSize;

    if (!this.positivePattern || typeof this.positivePattern !== 'string') {
      throw new _localization2.default('Invalid positivePattern');
    }

    if (!this.negativePattern || typeof this.negativePattern !== 'string') {
      throw new _localization2.default('Invalid negativePattern');
    }

    if (!this.symbol || !(this.symbol instanceof _numberSymbol2.default)) {
      throw new _localization2.default('Invalid symbol');
    }

    if (typeof this.maxFractionDigits !== 'number') {
      throw new _localization2.default('Invalid maxFractionDigits');
    }

    if (typeof this.minFractionDigits !== 'number') {
      throw new _localization2.default('Invalid minFractionDigits');
    }

    if (typeof this.groupingUsed !== 'boolean') {
      throw new _localization2.default('Invalid groupingUsed');
    }

    if (typeof this.primaryGroupSize !== 'number') {
      throw new _localization2.default('Invalid primaryGroupSize');
    }

    if (typeof this.secondaryGroupSize !== 'number') {
      throw new _localization2.default('Invalid secondaryGroupSize');
    }
  }

  /**
   * Get symbol.
   *
   * @return NumberSymbol
   */


  _createClass(NumberSpecification, [{
    key: 'getSymbol',
    value: function getSymbol() {
      return this.symbol;
    }

    /**
     * Get the formatting rules for this number (when positive).
     *
     * This pattern uses the Unicode CLDR number pattern syntax
     *
     * @return string
     */

  }, {
    key: 'getPositivePattern',
    value: function getPositivePattern() {
      return this.positivePattern;
    }

    /**
     * Get the formatting rules for this number (when negative).
     *
     * This pattern uses the Unicode CLDR number pattern syntax
     *
     * @return string
     */

  }, {
    key: 'getNegativePattern',
    value: function getNegativePattern() {
      return this.negativePattern;
    }

    /**
     * Get the maximum number of digits after decimal separator (rounding if needed).
     *
     * @return int
     */

  }, {
    key: 'getMaxFractionDigits',
    value: function getMaxFractionDigits() {
      return this.maxFractionDigits;
    }

    /**
     * Get the minimum number of digits after decimal separator (fill with "0" if needed).
     *
     * @return int
     */

  }, {
    key: 'getMinFractionDigits',
    value: function getMinFractionDigits() {
      return this.minFractionDigits;
    }

    /**
     * Get the "grouping" flag. This flag defines if digits
     * grouping should be used when formatting this number.
     *
     * @return bool
     */

  }, {
    key: 'isGroupingUsed',
    value: function isGroupingUsed() {
      return this.groupingUsed;
    }

    /**
     * Get the size of primary digits group in the number.
     *
     * @return int
     */

  }, {
    key: 'getPrimaryGroupSize',
    value: function getPrimaryGroupSize() {
      return this.primaryGroupSize;
    }

    /**
     * Get the size of secondary digits groups in the number.
     *
     * @return int
     */

  }, {
    key: 'getSecondaryGroupSize',
    value: function getSecondaryGroupSize() {
      return this.secondaryGroupSize;
    }
  }]);

  return NumberSpecification;
}();

exports.default = NumberSpecification;

/***/ }),

/***/ 64:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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
var LocalizationException = function LocalizationException(message) {
  _classCallCheck(this, LocalizationException);

  this.message = message;
  this.name = 'LocalizationException';
};

exports.default = LocalizationException;

/***/ }),

/***/ 88:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _localization = __webpack_require__(64);

var _localization2 = _interopRequireDefault(_localization);

var _number = __webpack_require__(63);

var _number2 = _interopRequireDefault(_number);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; } /**
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
 * Currency display option: symbol notation.
 */
var CURRENCY_DISPLAY_SYMBOL = 'symbol';

var PriceSpecification = function (_NumberSpecification) {
  _inherits(PriceSpecification, _NumberSpecification);

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
  function PriceSpecification(positivePattern, negativePattern, symbol, maxFractionDigits, minFractionDigits, groupingUsed, primaryGroupSize, secondaryGroupSize, currencySymbol, currencyCode) {
    _classCallCheck(this, PriceSpecification);

    var _this = _possibleConstructorReturn(this, (PriceSpecification.__proto__ || Object.getPrototypeOf(PriceSpecification)).call(this, positivePattern, negativePattern, symbol, maxFractionDigits, minFractionDigits, groupingUsed, primaryGroupSize, secondaryGroupSize));

    _this.currencySymbol = currencySymbol;
    _this.currencyCode = currencyCode;

    if (!_this.currencySymbol || typeof _this.currencySymbol !== 'string') {
      throw new _localization2.default('Invalid currencySymbol');
    }

    if (!_this.currencyCode || typeof _this.currencyCode !== 'string') {
      throw new _localization2.default('Invalid currencyCode');
    }
    return _this;
  }

  /**
   * Get type of display for currency symbol.
   *
   * @return string
   */


  _createClass(PriceSpecification, [{
    key: 'getCurrencySymbol',


    /**
     * Get the currency symbol
     * e.g.: €.
     *
     * @return string
     */
    value: function getCurrencySymbol() {
      return this.currencySymbol;
    }

    /**
     * Get the currency ISO code
     * e.g.: EUR.
     *
     * @return string
     */

  }, {
    key: 'getCurrencyCode',
    value: function getCurrencyCode() {
      return this.currencyCode;
    }
  }], [{
    key: 'getCurrencyDisplay',
    value: function getCurrencyDisplay() {
      return CURRENCY_DISPLAY_SYMBOL;
    }
  }]);

  return PriceSpecification;
}(_number2.default);

exports.default = PriceSpecification;

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODI/M2YxNioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2FwcC9jbGRyL251bWJlci1mb3JtYXR0ZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL2NsZHIvaW5kZXguanMiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL2NsZHIvbnVtYmVyLXN5bWJvbC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvY2xkci9zcGVjaWZpY2F0aW9ucy9udW1iZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL2NsZHIvZXhjZXB0aW9uL2xvY2FsaXphdGlvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvY2xkci9zcGVjaWZpY2F0aW9ucy9wcmljZS5qcyJdLCJuYW1lcyI6WyJDVVJSRU5DWV9TWU1CT0xfUExBQ0VIT0xERVIiLCJERUNJTUFMX1NFUEFSQVRPUl9QTEFDRUhPTERFUiIsIkdST1VQX1NFUEFSQVRPUl9QTEFDRUhPTERFUiIsIk1JTlVTX1NJR05fUExBQ0VIT0xERVIiLCJQRVJDRU5UX1NZTUJPTF9QTEFDRUhPTERFUiIsIlBMVVNfU0lHTl9QTEFDRUhPTERFUiIsIk51bWJlckZvcm1hdHRlciIsInNwZWNpZmljYXRpb24iLCJudW1iZXJTcGVjaWZpY2F0aW9uIiwibnVtYmVyIiwidW5kZWZpbmVkIiwibnVtIiwiTWF0aCIsImFicyIsInRvRml4ZWQiLCJnZXRNYXhGcmFjdGlvbkRpZ2l0cyIsImV4dHJhY3RNYWpvck1pbm9yRGlnaXRzIiwibWFqb3JEaWdpdHMiLCJtaW5vckRpZ2l0cyIsInNwbGl0TWFqb3JHcm91cHMiLCJhZGp1c3RNaW5vckRpZ2l0c1plcm9lcyIsImZvcm1hdHRlZE51bWJlciIsInBhdHRlcm4iLCJnZXRDbGRyUGF0dGVybiIsImFkZFBsYWNlaG9sZGVycyIsInJlcGxhY2VTeW1ib2xzIiwicGVyZm9ybVNwZWNpZmljUmVwbGFjZW1lbnRzIiwicmVzdWx0IiwidG9TdHJpbmciLCJzcGxpdCIsImRpZ2l0IiwiaXNHcm91cGluZ1VzZWQiLCJyZXZlcnNlIiwiZ3JvdXBzIiwicHVzaCIsInNwbGljZSIsImdldFByaW1hcnlHcm91cFNpemUiLCJsZW5ndGgiLCJnZXRTZWNvbmRhcnlHcm91cFNpemUiLCJuZXdHcm91cHMiLCJmb3JFYWNoIiwiZ3JvdXAiLCJqb2luIiwicmVwbGFjZSIsImdldE1pbkZyYWN0aW9uRGlnaXRzIiwicGFkRW5kIiwiaXNOZWdhdGl2ZSIsImdldE5lZ2F0aXZlUGF0dGVybiIsImdldFBvc2l0aXZlUGF0dGVybiIsInN5bWJvbHMiLCJnZXRTeW1ib2wiLCJnZXREZWNpbWFsIiwiZ2V0R3JvdXAiLCJnZXRNaW51c1NpZ24iLCJnZXRQZXJjZW50U2lnbiIsImdldFBsdXNTaWduIiwiUHJpY2VTcGVjaWZpY2F0aW9uIiwiZ2V0Q3VycmVuY3lTeW1ib2wiLCJzcGVjaWZpY2F0aW9ucyIsInN5bWJvbCIsIk51bWJlclN5bWJvbCIsImN1cnJlbmN5U3ltYm9sIiwicG9zaXRpdmVQYXR0ZXJuIiwibmVnYXRpdmVQYXR0ZXJuIiwicGFyc2VJbnQiLCJtYXhGcmFjdGlvbkRpZ2l0cyIsIm1pbkZyYWN0aW9uRGlnaXRzIiwiZ3JvdXBpbmdVc2VkIiwicHJpbWFyeUdyb3VwU2l6ZSIsInNlY29uZGFyeUdyb3VwU2l6ZSIsImN1cnJlbmN5Q29kZSIsIk51bWJlclNwZWNpZmljYXRpb24iLCJjdXJyZW5jeSIsImRlY2ltYWwiLCJsaXN0IiwicGVyY2VudFNpZ24iLCJtaW51c1NpZ24iLCJwbHVzU2lnbiIsImV4cG9uZW50aWFsIiwic3VwZXJzY3JpcHRpbmdFeHBvbmVudCIsInBlck1pbGxlIiwiaW5maW5pdHkiLCJuYW4iLCJ2YWxpZGF0ZURhdGEiLCJMb2NhbGl6YXRpb25FeGNlcHRpb24iLCJtZXNzYWdlIiwibmFtZSIsIkNVUlJFTkNZX0RJU1BMQVlfU1lNQk9MIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7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U0VDLGtCQVRGOztBQVdBLFVBQUtSLGNBQUwsR0FBc0JBLGNBQXRCO0FBQ0EsVUFBS1MsWUFBTCxHQUFvQkEsWUFBcEI7O0FBRUEsUUFBSSxDQUFDLE1BQUtULGNBQU4sSUFBd0IsT0FBTyxNQUFLQSxjQUFaLEtBQStCLFFBQTNELEVBQXFFO0FBQ25FLFlBQU0sSUFBSXVCLHNCQUFKLENBQTBCLHdCQUExQixDQUFOO0FBQ0Q7O0FBRUQsUUFBSSxDQUFDLE1BQUtkLFlBQU4sSUFBc0IsT0FBTyxNQUFLQSxZQUFaLEtBQTZCLFFBQXZELEVBQWlFO0FBQy9ELFlBQU0sSUFBSWMsc0JBQUosQ0FBMEIsc0JBQTFCLENBQU47QUFDRDtBQXBCRDtBQXFCRDs7QUFFRDs7Ozs7Ozs7Ozs7QUFTQTs7Ozs7O3dDQU1vQjtBQUNsQixhQUFPLEtBQUt2QixjQUFaO0FBQ0Q7O0FBRUQ7Ozs7Ozs7OztzQ0FNa0I7QUFDaEIsYUFBTyxLQUFLUyxZQUFaO0FBQ0Q7Ozt5Q0F0QjJCO0FBQzFCLGFBQU9pQix1QkFBUDtBQUNEOzs7O0VBMUQ4QmhCLGdCOztrQkFpRmxCZixrQiIsImZpbGUiOiJjbGRyLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMjk2KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCA1ZDk5OTA5NGQxMWFlZmYwYjU4MiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AuXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIEFjYWRlbWljIEZyZWUgTGljZW5zZSAzLjAgKEFGTC0zLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cDovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMCBBY2FkZW1pYyBGcmVlIExpY2Vuc2UgMy4wIChBRkwtMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuLyoqXG4gKiBUaGVzZSBwbGFjZWhvbGRlcnMgYXJlIHVzZWQgaW4gQ0xEUiBudW1iZXIgZm9ybWF0dGluZyB0ZW1wbGF0ZXMuXG4gKiBUaGV5IGFyZSBtZWFudCB0byBiZSByZXBsYWNlZCBieSB0aGUgY29ycmVjdCBsb2NhbGl6ZWQgc3ltYm9scyBpbiB0aGUgbnVtYmVyIGZvcm1hdHRpbmcgcHJvY2Vzcy5cbiAqL1xuaW1wb3J0IE51bWJlclN5bWJvbCBmcm9tICcuL251bWJlci1zeW1ib2wnO1xuaW1wb3J0IFByaWNlU3BlY2lmaWNhdGlvbiBmcm9tICcuL3NwZWNpZmljYXRpb25zL3ByaWNlJztcbmltcG9ydCBOdW1iZXJTcGVjaWZpY2F0aW9uIGZyb20gJy4vc3BlY2lmaWNhdGlvbnMvbnVtYmVyJztcblxuY29uc3QgQ1VSUkVOQ1lfU1lNQk9MX1BMQUNFSE9MREVSID0gJ8KkJztcbmNvbnN0IERFQ0lNQUxfU0VQQVJBVE9SX1BMQUNFSE9MREVSID0gJy4nO1xuY29uc3QgR1JPVVBfU0VQQVJBVE9SX1BMQUNFSE9MREVSID0gJywnO1xuY29uc3QgTUlOVVNfU0lHTl9QTEFDRUhPTERFUiA9ICctJztcbmNvbnN0IFBFUkNFTlRfU1lNQk9MX1BMQUNFSE9MREVSID0gJyUnO1xuY29uc3QgUExVU19TSUdOX1BMQUNFSE9MREVSID0gJysnO1xuXG5jbGFzcyBOdW1iZXJGb3JtYXR0ZXIge1xuICAvKipcbiAgICogQHBhcmFtIE51bWJlclNwZWNpZmljYXRpb24gc3BlY2lmaWNhdGlvbiBOdW1iZXIgc3BlY2lmaWNhdGlvbiB0byBiZSB1c2VkXG4gICAqICAgKGNhbiBiZSBhIG51bWJlciBzcGVjLCBhIHByaWNlIHNwZWMsIGEgcGVyY2VudGFnZSBzcGVjKVxuICAgKi9cbiAgY29uc3RydWN0b3Ioc3BlY2lmaWNhdGlvbikge1xuICAgIHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbiA9IHNwZWNpZmljYXRpb247XG4gIH1cblxuICAvKipcbiAgICogRm9ybWF0cyB0aGUgcGFzc2VkIG51bWJlciBhY2NvcmRpbmcgdG8gc3BlY2lmaWNhdGlvbnMuXG4gICAqXG4gICAqIEBwYXJhbSBpbnR8ZmxvYXR8c3RyaW5nIG51bWJlciBUaGUgbnVtYmVyIHRvIGZvcm1hdFxuICAgKiBAcGFyYW0gTnVtYmVyU3BlY2lmaWNhdGlvbiBzcGVjaWZpY2F0aW9uIE51bWJlciBzcGVjaWZpY2F0aW9uIHRvIGJlIHVzZWRcbiAgICogICAoY2FuIGJlIGEgbnVtYmVyIHNwZWMsIGEgcHJpY2Ugc3BlYywgYSBwZXJjZW50YWdlIHNwZWMpXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nIFRoZSBmb3JtYXR0ZWQgbnVtYmVyXG4gICAqICAgICAgICAgICAgICAgIFlvdSBzaG91bGQgdXNlIHRoaXMgdGhpcyB2YWx1ZSBmb3IgZGlzcGxheSwgd2l0aG91dCBtb2RpZnlpbmcgaXRcbiAgICovXG4gIGZvcm1hdChudW1iZXIsIHNwZWNpZmljYXRpb24pIHtcbiAgICBpZiAoc3BlY2lmaWNhdGlvbiAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICB0aGlzLm51bWJlclNwZWNpZmljYXRpb24gPSBzcGVjaWZpY2F0aW9uO1xuICAgIH1cblxuICAgIC8qXG4gICAgICogV2UgbmVlZCB0byB3b3JrIG9uIHRoZSBhYnNvbHV0ZSB2YWx1ZSBmaXJzdC5cbiAgICAgKiBUaGVuIHRoZSBDTERSIHBhdHRlcm4gd2lsbCBhZGQgdGhlIHNpZ24gaWYgcmVsZXZhbnQgKGF0IHRoZSBlbmQpLlxuICAgICAqL1xuICAgIGNvbnN0IG51bSA9IE1hdGguYWJzKG51bWJlcikudG9GaXhlZCh0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0TWF4RnJhY3Rpb25EaWdpdHMoKSk7XG5cbiAgICBsZXQgW21ham9yRGlnaXRzLCBtaW5vckRpZ2l0c10gPSB0aGlzLmV4dHJhY3RNYWpvck1pbm9yRGlnaXRzKG51bSk7XG4gICAgbWFqb3JEaWdpdHMgPSB0aGlzLnNwbGl0TWFqb3JHcm91cHMobWFqb3JEaWdpdHMpO1xuICAgIG1pbm9yRGlnaXRzID0gdGhpcy5hZGp1c3RNaW5vckRpZ2l0c1plcm9lcyhtaW5vckRpZ2l0cyk7XG5cbiAgICAvLyBBc3NlbWJsZSB0aGUgZmluYWwgbnVtYmVyXG4gICAgbGV0IGZvcm1hdHRlZE51bWJlciA9IG1ham9yRGlnaXRzO1xuICAgIGlmIChtaW5vckRpZ2l0cykge1xuICAgICAgZm9ybWF0dGVkTnVtYmVyICs9IERFQ0lNQUxfU0VQQVJBVE9SX1BMQUNFSE9MREVSICsgbWlub3JEaWdpdHM7XG4gICAgfVxuXG4gICAgLy8gR2V0IHRoZSBnb29kIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuLiBTaWduIGlzIGltcG9ydGFudCBoZXJlICFcbiAgICBjb25zdCBwYXR0ZXJuID0gdGhpcy5nZXRDbGRyUGF0dGVybihtYWpvckRpZ2l0cyA8IDApO1xuICAgIGZvcm1hdHRlZE51bWJlciA9IHRoaXMuYWRkUGxhY2Vob2xkZXJzKGZvcm1hdHRlZE51bWJlciwgcGF0dGVybik7XG4gICAgZm9ybWF0dGVkTnVtYmVyID0gdGhpcy5yZXBsYWNlU3ltYm9scyhmb3JtYXR0ZWROdW1iZXIpO1xuXG4gICAgZm9ybWF0dGVkTnVtYmVyID0gdGhpcy5wZXJmb3JtU3BlY2lmaWNSZXBsYWNlbWVudHMoZm9ybWF0dGVkTnVtYmVyKTtcblxuICAgIHJldHVybiBmb3JtYXR0ZWROdW1iZXI7XG4gIH1cblxuICAvKipcbiAgICogR2V0IG51bWJlcidzIG1ham9yIGFuZCBtaW5vciBkaWdpdHMuXG4gICAqXG4gICAqIE1ham9yIGRpZ2l0cyBhcmUgdGhlIFwiaW50ZWdlclwiIHBhcnQgKGJlZm9yZSBkZWNpbWFsIHNlcGFyYXRvciksXG4gICAqIG1pbm9yIGRpZ2l0cyBhcmUgdGhlIGZyYWN0aW9uYWwgcGFydFxuICAgKiBSZXN1bHQgd2lsbCBiZSBhbiBhcnJheSBvZiBleGFjdGx5IDIgaXRlbXM6IFttYWpvckRpZ2l0cywgbWlub3JEaWdpdHNdXG4gICAqXG4gICAqIFVzYWdlIGV4YW1wbGU6XG4gICAqICBsaXN0KG1ham9yRGlnaXRzLCBtaW5vckRpZ2l0cykgPSB0aGlzLmdldE1ham9yTWlub3JEaWdpdHMoZGVjaW1hbE51bWJlcik7XG4gICAqXG4gICAqIEBwYXJhbSBEZWNpbWFsTnVtYmVyIG51bWJlclxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1tdXG4gICAqL1xuICBleHRyYWN0TWFqb3JNaW5vckRpZ2l0cyhudW1iZXIpIHtcbiAgICAvLyBHZXQgdGhlIG51bWJlcidzIG1ham9yIGFuZCBtaW5vciBkaWdpdHMuXG4gICAgY29uc3QgcmVzdWx0ID0gbnVtYmVyLnRvU3RyaW5nKCkuc3BsaXQoJy4nKTtcbiAgICBjb25zdCBtYWpvckRpZ2l0cyA9IHJlc3VsdFswXTtcbiAgICBjb25zdCBtaW5vckRpZ2l0cyA9IChyZXN1bHRbMV0gPT09IHVuZGVmaW5lZCkgPyAnJyA6IHJlc3VsdFsxXTtcbiAgICByZXR1cm4gW21ham9yRGlnaXRzLCBtaW5vckRpZ2l0c107XG4gIH1cblxuICAvKipcbiAgICogU3BsaXRzIG1ham9yIGRpZ2l0cyBpbnRvIGdyb3Vwcy5cbiAgICpcbiAgICogZS5nLjogR2l2ZW4gdGhlIG1ham9yIGRpZ2l0cyBcIjEyMzQ1NjdcIiwgYW5kIG1ham9yIGdyb3VwIHNpemVcbiAgICogIGNvbmZpZ3VyZWQgdG8gMyBkaWdpdHMsIHRoZSByZXN1bHQgd291bGQgYmUgXCIxIDIzNCA1NjdcIlxuICAgKlxuICAgKiBAcGFyYW0gc3RyaW5nIG1ham9yRGlnaXRzIFRoZSBtYWpvciBkaWdpdHMgdG8gYmUgZ3JvdXBlZFxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZyBUaGUgZ3JvdXBlZCBtYWpvciBkaWdpdHNcbiAgICovXG4gIHNwbGl0TWFqb3JHcm91cHMoZGlnaXQpIHtcbiAgICBpZiAoIXRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5pc0dyb3VwaW5nVXNlZCgpKSB7XG4gICAgICByZXR1cm4gZGlnaXQ7XG4gICAgfVxuXG4gICAgLy8gUmV2ZXJzZSB0aGUgbWFqb3IgZGlnaXRzLCBzaW5jZSB0aGV5IGFyZSBncm91cGVkIGZyb20gdGhlIHJpZ2h0LlxuICAgIGNvbnN0IG1ham9yRGlnaXRzID0gZGlnaXQuc3BsaXQoJycpLnJldmVyc2UoKTtcblxuICAgIC8vIEdyb3VwIHRoZSBtYWpvciBkaWdpdHMuXG4gICAgbGV0IGdyb3VwcyA9IFtdO1xuICAgIGdyb3Vwcy5wdXNoKG1ham9yRGlnaXRzLnNwbGljZSgwLCB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0UHJpbWFyeUdyb3VwU2l6ZSgpKSk7XG4gICAgd2hpbGUgKG1ham9yRGlnaXRzLmxlbmd0aCkge1xuICAgICAgZ3JvdXBzLnB1c2gobWFqb3JEaWdpdHMuc3BsaWNlKDAsIHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRTZWNvbmRhcnlHcm91cFNpemUoKSkpO1xuICAgIH1cblxuICAgIC8vIFJldmVyc2UgYmFjayB0aGUgZGlnaXRzIGFuZCB0aGUgZ3JvdXBzXG4gICAgZ3JvdXBzID0gZ3JvdXBzLnJldmVyc2UoKTtcbiAgICBjb25zdCBuZXdHcm91cHMgPSBbXTtcbiAgICBncm91cHMuZm9yRWFjaCgoZ3JvdXApID0+IHtcbiAgICAgIG5ld0dyb3Vwcy5wdXNoKGdyb3VwLnJldmVyc2UoKS5qb2luKCcnKSk7XG4gICAgfSk7XG5cbiAgICAvLyBSZWNvbnN0cnVjdCB0aGUgbWFqb3IgZGlnaXRzLlxuICAgIHJldHVybiBuZXdHcm91cHMuam9pbihHUk9VUF9TRVBBUkFUT1JfUExBQ0VIT0xERVIpO1xuICB9XG5cbiAgLyoqXG4gICAqIEFkZHMgb3IgcmVtb3ZlIHRyYWlsaW5nIHplcm9lcywgZGVwZW5kaW5nIG9uIHNwZWNpZmllZCBtaW4gYW5kIG1heCBmcmFjdGlvbiBkaWdpdHMgbnVtYmVycy5cbiAgICpcbiAgICogQHBhcmFtIHN0cmluZyBtaW5vckRpZ2l0cyBEaWdpdHMgdG8gYmUgYWRqdXN0ZWQgd2l0aCAodHJpbW1lZCBvciBwYWRkZWQpIHplcm9lc1xuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZyBUaGUgYWRqdXN0ZWQgbWlub3IgZGlnaXRzXG4gICAqL1xuICBhZGp1c3RNaW5vckRpZ2l0c1plcm9lcyhtaW5vckRpZ2l0cykge1xuICAgIGxldCBkaWdpdCA9IG1pbm9yRGlnaXRzO1xuICAgIGlmIChkaWdpdC5sZW5ndGggPiB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0TWF4RnJhY3Rpb25EaWdpdHMoKSkge1xuICAgICAgLy8gU3RyaXAgYW55IHRyYWlsaW5nIHplcm9lcy5cbiAgICAgIGRpZ2l0ID0gZGlnaXQucmVwbGFjZSgvMCskLywgJycpO1xuICAgIH1cblxuICAgIGlmIChkaWdpdC5sZW5ndGggPCB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0TWluRnJhY3Rpb25EaWdpdHMoKSkge1xuICAgICAgLy8gUmUtYWRkIG5lZWRlZCB6ZXJvZXNcbiAgICAgIGRpZ2l0ID0gZGlnaXQucGFkRW5kKFxuICAgICAgICB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0TWluRnJhY3Rpb25EaWdpdHMoKSxcbiAgICAgICAgJzAnLFxuICAgICAgKTtcbiAgICB9XG5cbiAgICByZXR1cm4gZGlnaXQ7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBDTERSIGZvcm1hdHRpbmcgcGF0dGVybi5cbiAgICpcbiAgICogQHNlZSBodHRwOi8vY2xkci51bmljb2RlLm9yZy90cmFuc2xhdGlvbi9udW1iZXItcGF0dGVybnNcbiAgICpcbiAgICogQHBhcmFtIGJvb2wgaXNOZWdhdGl2ZSBJZiB0cnVlLCB0aGUgbmVnYXRpdmUgcGF0dGVyblxuICAgKiB3aWxsIGJlIHJldHVybmVkIGluc3RlYWQgb2YgdGhlIHBvc2l0aXZlIG9uZVxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZyBUaGUgQ0xEUiBmb3JtYXR0aW5nIHBhdHRlcm5cbiAgICovXG4gIGdldENsZHJQYXR0ZXJuKGlzTmVnYXRpdmUpIHtcbiAgICBpZiAoaXNOZWdhdGl2ZSkge1xuICAgICAgcmV0dXJuIHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXROZWdhdGl2ZVBhdHRlcm4oKTtcbiAgICB9XG5cbiAgICByZXR1cm4gdGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmdldFBvc2l0aXZlUGF0dGVybigpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlcGxhY2UgcGxhY2Vob2xkZXIgbnVtYmVyIHN5bWJvbHMgd2l0aCByZWxldmFudCBudW1iZXJpbmcgc3lzdGVtJ3Mgc3ltYm9scy5cbiAgICpcbiAgICogQHBhcmFtIHN0cmluZyBudW1iZXJcbiAgICogICAgICAgICAgICAgICAgICAgICAgIFRoZSBudW1iZXIgdG8gcHJvY2Vzc1xuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKiAgICAgICAgICAgICAgICBUaGUgbnVtYmVyIHdpdGggcmVwbGFjZWQgc3ltYm9sc1xuICAgKi9cbiAgcmVwbGFjZVN5bWJvbHMobnVtYmVyKSB7XG4gICAgY29uc3Qgc3ltYm9scyA9IHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRTeW1ib2woKTtcbiAgICBsZXQgbnVtID0gbnVtYmVyO1xuICAgIG51bSA9IG51bS5zcGxpdChERUNJTUFMX1NFUEFSQVRPUl9QTEFDRUhPTERFUikuam9pbihzeW1ib2xzLmdldERlY2ltYWwoKSk7XG4gICAgbnVtID0gbnVtLnNwbGl0KEdST1VQX1NFUEFSQVRPUl9QTEFDRUhPTERFUikuam9pbihzeW1ib2xzLmdldEdyb3VwKCkpO1xuICAgIG51bSA9IG51bS5zcGxpdChNSU5VU19TSUdOX1BMQUNFSE9MREVSKS5qb2luKHN5bWJvbHMuZ2V0TWludXNTaWduKCkpO1xuICAgIG51bSA9IG51bS5zcGxpdChQRVJDRU5UX1NZTUJPTF9QTEFDRUhPTERFUikuam9pbihzeW1ib2xzLmdldFBlcmNlbnRTaWduKCkpO1xuICAgIG51bSA9IG51bS5zcGxpdChQTFVTX1NJR05fUExBQ0VIT0xERVIpLmpvaW4oc3ltYm9scy5nZXRQbHVzU2lnbigpKTtcblxuICAgIHJldHVybiBudW07XG4gIH1cblxuICAvKipcbiAgICogQWRkIG1pc3NpbmcgcGxhY2Vob2xkZXJzIHRvIHRoZSBudW1iZXIgdXNpbmcgdGhlIHBhc3NlZCBDTERSIHBhdHRlcm4uXG4gICAqXG4gICAqIE1pc3NpbmcgcGxhY2Vob2xkZXJzIGNhbiBiZSB0aGUgcGVyY2VudCBzaWduLCBjdXJyZW5jeSBzeW1ib2wsIGV0Yy5cbiAgICpcbiAgICogZS5nLiB3aXRoIGEgY3VycmVuY3kgQ0xEUiBwYXR0ZXJuOlxuICAgKiAgLSBQYXNzZWQgbnVtYmVyIChwYXJ0aWFsbHkgZm9ybWF0dGVkKTogMSwyMzQuNTY3XG4gICAqICAtIFJldHVybmVkIG51bWJlcjogMSwyMzQuNTY3IMKkXG4gICAqICAoXCLCpFwiIHN5bWJvbCBpcyB0aGUgY3VycmVuY3kgc3ltYm9sIHBsYWNlaG9sZGVyKVxuICAgKlxuICAgKiBAc2VlIGh0dHA6Ly9jbGRyLnVuaWNvZGUub3JnL3RyYW5zbGF0aW9uL251bWJlci1wYXR0ZXJuc1xuICAgKlxuICAgKiBAcGFyYW0gZm9ybWF0dGVkTnVtYmVyXG4gICAqICBOdW1iZXIgdG8gcHJvY2Vzc1xuICAgKiBAcGFyYW0gcGF0dGVyblxuICAgKiAgQ0xEUiBmb3JtYXR0aW5nIHBhdHRlcm4gdG8gdXNlXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBhZGRQbGFjZWhvbGRlcnMoZm9ybWF0dGVkTnVtYmVyLCBwYXR0ZXJuKSB7XG4gICAgLypcbiAgICAgKiBSZWdleCBncm91cHMgZXhwbGFuYXRpb246XG4gICAgICogIyAgICAgICAgICA6IGxpdGVyYWwgXCIjXCIgY2hhcmFjdGVyLiBPbmNlLlxuICAgICAqICgsIyspKiAgICAgOiBhbnkgb3RoZXIgXCIjXCIgY2hhcmFjdGVycyBncm91cCwgc2VwYXJhdGVkIGJ5IFwiLFwiLiBaZXJvIHRvIGluZmluaXR5IHRpbWVzLlxuICAgICAqIDAgICAgICAgICAgOiBsaXRlcmFsIFwiMFwiIGNoYXJhY3Rlci4gT25jZS5cbiAgICAgKiAoXFwuWzAjXSspKiA6IGFueSBjb21iaW5hdGlvbiBvZiBcIjBcIiBhbmQgXCIjXCIgY2hhcmFjdGVycyBncm91cHMsIHNlcGFyYXRlZCBieSAnLicuXG4gICAgICogICAgICAgICAgICAgIFplcm8gdG8gaW5maW5pdHkgdGltZXMuXG4gICAgICovXG4gICAgcmV0dXJuIHBhdHRlcm4ucmVwbGFjZSgvIz8oLCMrKSowKFxcLlswI10rKSovLCBmb3JtYXR0ZWROdW1iZXIpO1xuICB9XG5cbiAgLyoqXG4gICAqIFBlcmZvcm0gc29tZSBtb3JlIHNwZWNpZmljIHJlcGxhY2VtZW50cy5cbiAgICpcbiAgICogU3BlY2lmaWMgcmVwbGFjZW1lbnRzIGFyZSBuZWVkZWQgd2hlbiBudW1iZXIgc3BlY2lmaWNhdGlvbiBpcyBleHRlbmRlZC5cbiAgICogRm9yIGluc3RhbmNlLCBwcmljZXMgaGF2ZSBhbiBleHRlbmRlZCBudW1iZXIgc3BlY2lmaWNhdGlvbiBpbiBvcmRlciB0b1xuICAgKiBhZGQgY3VycmVuY3kgc3ltYm9sIHRvIHRoZSBmb3JtYXR0ZWQgbnVtYmVyLlxuICAgKlxuICAgKiBAcGFyYW0gc3RyaW5nIGZvcm1hdHRlZE51bWJlclxuICAgKlxuICAgKiBAcmV0dXJuIG1peGVkXG4gICAqL1xuICBwZXJmb3JtU3BlY2lmaWNSZXBsYWNlbWVudHMoZm9ybWF0dGVkTnVtYmVyKSB7XG4gICAgaWYgKHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbiBpbnN0YW5jZW9mIFByaWNlU3BlY2lmaWNhdGlvbikge1xuICAgICAgcmV0dXJuIGZvcm1hdHRlZE51bWJlclxuICAgICAgICAuc3BsaXQoQ1VSUkVOQ1lfU1lNQk9MX1BMQUNFSE9MREVSKVxuICAgICAgICAuam9pbih0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0Q3VycmVuY3lTeW1ib2woKSk7XG4gICAgfVxuXG4gICAgcmV0dXJuIGZvcm1hdHRlZE51bWJlcjtcbiAgfVxuXG4gIHN0YXRpYyBidWlsZChzcGVjaWZpY2F0aW9ucykge1xuICAgIGNvbnN0IHN5bWJvbCA9IG5ldyBOdW1iZXJTeW1ib2woLi4uc3BlY2lmaWNhdGlvbnMuc3ltYm9sKTtcbiAgICBsZXQgc3BlY2lmaWNhdGlvbjtcbiAgICBpZiAoc3BlY2lmaWNhdGlvbnMuY3VycmVuY3lTeW1ib2wpIHtcbiAgICAgIHNwZWNpZmljYXRpb24gPSBuZXcgUHJpY2VTcGVjaWZpY2F0aW9uKFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5wb3NpdGl2ZVBhdHRlcm4sXG4gICAgICAgIHNwZWNpZmljYXRpb25zLm5lZ2F0aXZlUGF0dGVybixcbiAgICAgICAgc3ltYm9sLFxuICAgICAgICBwYXJzZUludChzcGVjaWZpY2F0aW9ucy5tYXhGcmFjdGlvbkRpZ2l0cywgMTApLFxuICAgICAgICBwYXJzZUludChzcGVjaWZpY2F0aW9ucy5taW5GcmFjdGlvbkRpZ2l0cywgMTApLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5ncm91cGluZ1VzZWQsXG4gICAgICAgIHNwZWNpZmljYXRpb25zLnByaW1hcnlHcm91cFNpemUsXG4gICAgICAgIHNwZWNpZmljYXRpb25zLnNlY29uZGFyeUdyb3VwU2l6ZSxcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMuY3VycmVuY3lTeW1ib2wsXG4gICAgICAgIHNwZWNpZmljYXRpb25zLmN1cnJlbmN5Q29kZSxcbiAgICAgICk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHNwZWNpZmljYXRpb24gPSBuZXcgTnVtYmVyU3BlY2lmaWNhdGlvbihcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMucG9zaXRpdmVQYXR0ZXJuLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5uZWdhdGl2ZVBhdHRlcm4sXG4gICAgICAgIHN5bWJvbCxcbiAgICAgICAgcGFyc2VJbnQoc3BlY2lmaWNhdGlvbnMubWF4RnJhY3Rpb25EaWdpdHMsIDEwKSxcbiAgICAgICAgcGFyc2VJbnQoc3BlY2lmaWNhdGlvbnMubWluRnJhY3Rpb25EaWdpdHMsIDEwKSxcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMuZ3JvdXBpbmdVc2VkLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5wcmltYXJ5R3JvdXBTaXplLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5zZWNvbmRhcnlHcm91cFNpemUsXG4gICAgICApO1xuICAgIH1cblxuICAgIGNvbnN0IGN1cnJlbmN5ID0gbmV3IE51bWJlckZvcm1hdHRlcihzcGVjaWZpY2F0aW9uKTtcblxuICAgIHJldHVybiBjdXJyZW5jeTtcbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBOdW1iZXJGb3JtYXR0ZXI7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvY2xkci9udW1iZXItZm9ybWF0dGVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcC5cbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgQWNhZGVtaWMgRnJlZSBMaWNlbnNlIDMuMCAoQUZMLTMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9BRkwtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0FcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9BRkwtMy4wIEFjYWRlbWljIEZyZWUgTGljZW5zZSAzLjAgKEFGTC0zLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5pbXBvcnQgTnVtYmVyRm9ybWF0dGVyIGZyb20gJy4vbnVtYmVyLWZvcm1hdHRlcic7XG5pbXBvcnQgTnVtYmVyU3ltYm9sIGZyb20gJy4vbnVtYmVyLXN5bWJvbCc7XG5pbXBvcnQgUHJpY2VTcGVjaWZpY2F0aW9uIGZyb20gJy4vc3BlY2lmaWNhdGlvbnMvcHJpY2UnO1xuaW1wb3J0IE51bWJlclNwZWNpZmljYXRpb24gZnJvbSAnLi9zcGVjaWZpY2F0aW9ucy9udW1iZXInO1xuXG5leHBvcnQge1xuICBQcmljZVNwZWNpZmljYXRpb24sXG4gIE51bWJlclNwZWNpZmljYXRpb24sXG4gIE51bWJlckZvcm1hdHRlcixcbiAgTnVtYmVyU3ltYm9sLFxufTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL2luZGV4LmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcC5cbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgQWNhZGVtaWMgRnJlZSBMaWNlbnNlIDMuMCAoQUZMLTMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9BRkwtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0FcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9BRkwtMy4wIEFjYWRlbWljIEZyZWUgTGljZW5zZSAzLjAgKEFGTC0zLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5pbXBvcnQgTG9jYWxpemF0aW9uRXhjZXB0aW9uIGZyb20gJy4vZXhjZXB0aW9uL2xvY2FsaXphdGlvbic7XG5cbmNsYXNzIE51bWJlclN5bWJvbCB7XG4gIC8qKlxuICAgKiBOdW1iZXJTeW1ib2xMaXN0IGNvbnN0cnVjdG9yLlxuICAgKlxuICAgKiBAcGFyYW0gc3RyaW5nIGRlY2ltYWwgRGVjaW1hbCBzZXBhcmF0b3IgY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgZ3JvdXAgRGlnaXRzIGdyb3VwIHNlcGFyYXRvciBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBsaXN0IExpc3QgZWxlbWVudHMgc2VwYXJhdG9yIGNoYXJhY3RlclxuICAgKiBAcGFyYW0gc3RyaW5nIHBlcmNlbnRTaWduIFBlcmNlbnQgc2lnbiBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBtaW51c1NpZ24gTWludXMgc2lnbiBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBwbHVzU2lnbiBQbHVzIHNpZ24gY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgZXhwb25lbnRpYWwgRXhwb25lbnRpYWwgY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgc3VwZXJzY3JpcHRpbmdFeHBvbmVudCBTdXBlcnNjcmlwdGluZyBleHBvbmVudCBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBwZXJNaWxsZSBQZXJtaWxsZSBzaWduIGNoYXJhY3RlclxuICAgKiBAcGFyYW0gc3RyaW5nIGluZmluaXR5IFRoZSBpbmZpbml0eSBzaWduLiBDb3JyZXNwb25kcyB0byB0aGUgSUVFRSBpbmZpbml0eSBiaXQgcGF0dGVybi5cbiAgICogQHBhcmFtIHN0cmluZyBuYW4gVGhlIE5hTiAoTm90IEEgTnVtYmVyKSBzaWduLiBDb3JyZXNwb25kcyB0byB0aGUgSUVFRSBOYU4gYml0IHBhdHRlcm4uXG4gICAqXG4gICAqIEB0aHJvd3MgTG9jYWxpemF0aW9uRXhjZXB0aW9uXG4gICAqL1xuICBjb25zdHJ1Y3RvcihcbiAgICBkZWNpbWFsLFxuICAgIGdyb3VwLFxuICAgIGxpc3QsXG4gICAgcGVyY2VudFNpZ24sXG4gICAgbWludXNTaWduLFxuICAgIHBsdXNTaWduLFxuICAgIGV4cG9uZW50aWFsLFxuICAgIHN1cGVyc2NyaXB0aW5nRXhwb25lbnQsXG4gICAgcGVyTWlsbGUsXG4gICAgaW5maW5pdHksXG4gICAgbmFuLFxuICApIHtcbiAgICB0aGlzLmRlY2ltYWwgPSBkZWNpbWFsO1xuICAgIHRoaXMuZ3JvdXAgPSBncm91cDtcbiAgICB0aGlzLmxpc3QgPSBsaXN0O1xuICAgIHRoaXMucGVyY2VudFNpZ24gPSBwZXJjZW50U2lnbjtcbiAgICB0aGlzLm1pbnVzU2lnbiA9IG1pbnVzU2lnbjtcbiAgICB0aGlzLnBsdXNTaWduID0gcGx1c1NpZ247XG4gICAgdGhpcy5leHBvbmVudGlhbCA9IGV4cG9uZW50aWFsO1xuICAgIHRoaXMuc3VwZXJzY3JpcHRpbmdFeHBvbmVudCA9IHN1cGVyc2NyaXB0aW5nRXhwb25lbnQ7XG4gICAgdGhpcy5wZXJNaWxsZSA9IHBlck1pbGxlO1xuICAgIHRoaXMuaW5maW5pdHkgPSBpbmZpbml0eTtcbiAgICB0aGlzLm5hbiA9IG5hbjtcblxuICAgIHRoaXMudmFsaWRhdGVEYXRhKCk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBkZWNpbWFsIHNlcGFyYXRvci5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldERlY2ltYWwoKSB7XG4gICAgcmV0dXJuIHRoaXMuZGVjaW1hbDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGRpZ2l0IGdyb3VwcyBzZXBhcmF0b3IuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRHcm91cCgpIHtcbiAgICByZXR1cm4gdGhpcy5ncm91cDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGxpc3QgZWxlbWVudHMgc2VwYXJhdG9yLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0TGlzdCgpIHtcbiAgICByZXR1cm4gdGhpcy5saXN0O1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgcGVyY2VudCBzaWduLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0UGVyY2VudFNpZ24oKSB7XG4gICAgcmV0dXJuIHRoaXMucGVyY2VudFNpZ247XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBtaW51cyBzaWduLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0TWludXNTaWduKCkge1xuICAgIHJldHVybiB0aGlzLm1pbnVzU2lnbjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIHBsdXMgc2lnbi5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldFBsdXNTaWduKCkge1xuICAgIHJldHVybiB0aGlzLnBsdXNTaWduO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgZXhwb25lbnRpYWwgY2hhcmFjdGVyLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0RXhwb25lbnRpYWwoKSB7XG4gICAgcmV0dXJuIHRoaXMuZXhwb25lbnRpYWw7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBleHBvbmVudCBjaGFyYWN0ZXIuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRTdXBlcnNjcmlwdGluZ0V4cG9uZW50KCkge1xuICAgIHJldHVybiB0aGlzLnN1cGVyc2NyaXB0aW5nRXhwb25lbnQ7XG4gIH1cblxuICAvKipcbiAgICogR2VydCB0aGUgcGVyIG1pbGxlIHN5bWJvbCAob2Z0ZW4gXCLigLBcIikuXG4gICAqXG4gICAqIEBzZWUgaHR0cHM6Ly9lbi53aWtpcGVkaWEub3JnL3dpa2kvUGVyX21pbGxlXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRQZXJNaWxsZSgpIHtcbiAgICByZXR1cm4gdGhpcy5wZXJNaWxsZTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGluZmluaXR5IHN5bWJvbCAob2Z0ZW4gXCLiiJ5cIikuXG4gICAqXG4gICAqIEBzZWUgaHR0cHM6Ly9lbi53aWtpcGVkaWEub3JnL3dpa2kvSW5maW5pdHlfc3ltYm9sXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRJbmZpbml0eSgpIHtcbiAgICByZXR1cm4gdGhpcy5pbmZpbml0eTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIE5hTiAobm90IGEgbnVtYmVyKSBzaWduLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0TmFuKCkge1xuICAgIHJldHVybiB0aGlzLm5hbjtcbiAgfVxuXG4gIC8qKlxuICAgKiBTeW1ib2xzIGxpc3QgdmFsaWRhdGlvbi5cbiAgICpcbiAgICogQHRocm93cyBMb2NhbGl6YXRpb25FeGNlcHRpb25cbiAgICovXG4gIHZhbGlkYXRlRGF0YSgpIHtcbiAgICBpZiAoIXRoaXMuZGVjaW1hbCB8fCB0eXBlb2YgdGhpcy5kZWNpbWFsICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBkZWNpbWFsJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLmdyb3VwIHx8IHR5cGVvZiB0aGlzLmdyb3VwICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBncm91cCcpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5saXN0IHx8IHR5cGVvZiB0aGlzLmxpc3QgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHN5bWJvbCBsaXN0Jyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLnBlcmNlbnRTaWduIHx8IHR5cGVvZiB0aGlzLnBlcmNlbnRTaWduICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBwZXJjZW50U2lnbicpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5taW51c1NpZ24gfHwgdHlwZW9mIHRoaXMubWludXNTaWduICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBtaW51c1NpZ24nKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMucGx1c1NpZ24gfHwgdHlwZW9mIHRoaXMucGx1c1NpZ24gIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHBsdXNTaWduJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLmV4cG9uZW50aWFsIHx8IHR5cGVvZiB0aGlzLmV4cG9uZW50aWFsICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBleHBvbmVudGlhbCcpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5zdXBlcnNjcmlwdGluZ0V4cG9uZW50IHx8IHR5cGVvZiB0aGlzLnN1cGVyc2NyaXB0aW5nRXhwb25lbnQgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHN1cGVyc2NyaXB0aW5nRXhwb25lbnQnKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMucGVyTWlsbGUgfHwgdHlwZW9mIHRoaXMucGVyTWlsbGUgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHBlck1pbGxlJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLmluZmluaXR5IHx8IHR5cGVvZiB0aGlzLmluZmluaXR5ICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBpbmZpbml0eScpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5uYW4gfHwgdHlwZW9mIHRoaXMubmFuICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBuYW4nKTtcbiAgICB9XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTnVtYmVyU3ltYm9sO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL2NsZHIvbnVtYmVyLXN5bWJvbC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AuXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIEFjYWRlbWljIEZyZWUgTGljZW5zZSAzLjAgKEFGTC0zLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cDovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMCBBY2FkZW1pYyBGcmVlIExpY2Vuc2UgMy4wIChBRkwtMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuaW1wb3J0IExvY2FsaXphdGlvbkV4Y2VwdGlvbiBmcm9tICcuLi9leGNlcHRpb24vbG9jYWxpemF0aW9uJztcbmltcG9ydCBOdW1iZXJTeW1ib2wgZnJvbSAnLi4vbnVtYmVyLXN5bWJvbCc7XG5cbmNsYXNzIE51bWJlclNwZWNpZmljYXRpb24ge1xuICAvKipcbiAgICogTnVtYmVyIHNwZWNpZmljYXRpb24gY29uc3RydWN0b3IuXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgcG9zaXRpdmVQYXR0ZXJuIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIGZvciBwb3NpdGl2ZSBhbW91bnRzXG4gICAqIEBwYXJhbSBzdHJpbmcgbmVnYXRpdmVQYXR0ZXJuIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIGZvciBuZWdhdGl2ZSBhbW91bnRzXG4gICAqIEBwYXJhbSBOdW1iZXJTeW1ib2wgc3ltYm9sIE51bWJlciBzeW1ib2xcbiAgICogQHBhcmFtIGludCBtYXhGcmFjdGlvbkRpZ2l0cyBNYXhpbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3JcbiAgICogQHBhcmFtIGludCBtaW5GcmFjdGlvbkRpZ2l0cyBNaW5pbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3JcbiAgICogQHBhcmFtIGJvb2wgZ3JvdXBpbmdVc2VkIElzIGRpZ2l0cyBncm91cGluZyB1c2VkID9cbiAgICogQHBhcmFtIGludCBwcmltYXJ5R3JvdXBTaXplIFNpemUgb2YgcHJpbWFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlclxuICAgKiBAcGFyYW0gaW50IHNlY29uZGFyeUdyb3VwU2l6ZSBTaXplIG9mIHNlY29uZGFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlclxuICAgKlxuICAgKiBAdGhyb3dzIExvY2FsaXphdGlvbkV4Y2VwdGlvblxuICAgKi9cbiAgY29uc3RydWN0b3IoXG4gICAgcG9zaXRpdmVQYXR0ZXJuLFxuICAgIG5lZ2F0aXZlUGF0dGVybixcbiAgICBzeW1ib2wsXG4gICAgbWF4RnJhY3Rpb25EaWdpdHMsXG4gICAgbWluRnJhY3Rpb25EaWdpdHMsXG4gICAgZ3JvdXBpbmdVc2VkLFxuICAgIHByaW1hcnlHcm91cFNpemUsXG4gICAgc2Vjb25kYXJ5R3JvdXBTaXplLFxuICApIHtcbiAgICB0aGlzLnBvc2l0aXZlUGF0dGVybiA9IHBvc2l0aXZlUGF0dGVybjtcbiAgICB0aGlzLm5lZ2F0aXZlUGF0dGVybiA9IG5lZ2F0aXZlUGF0dGVybjtcbiAgICB0aGlzLnN5bWJvbCA9IHN5bWJvbDtcblxuICAgIHRoaXMubWF4RnJhY3Rpb25EaWdpdHMgPSBtYXhGcmFjdGlvbkRpZ2l0cztcbiAgICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmVcbiAgICB0aGlzLm1pbkZyYWN0aW9uRGlnaXRzID0gbWF4RnJhY3Rpb25EaWdpdHMgPCBtaW5GcmFjdGlvbkRpZ2l0cyA/IG1heEZyYWN0aW9uRGlnaXRzIDogbWluRnJhY3Rpb25EaWdpdHM7XG5cbiAgICB0aGlzLmdyb3VwaW5nVXNlZCA9IGdyb3VwaW5nVXNlZDtcbiAgICB0aGlzLnByaW1hcnlHcm91cFNpemUgPSBwcmltYXJ5R3JvdXBTaXplO1xuICAgIHRoaXMuc2Vjb25kYXJ5R3JvdXBTaXplID0gc2Vjb25kYXJ5R3JvdXBTaXplO1xuXG4gICAgaWYgKCF0aGlzLnBvc2l0aXZlUGF0dGVybiB8fCB0eXBlb2YgdGhpcy5wb3NpdGl2ZVBhdHRlcm4gIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHBvc2l0aXZlUGF0dGVybicpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5uZWdhdGl2ZVBhdHRlcm4gfHwgdHlwZW9mIHRoaXMubmVnYXRpdmVQYXR0ZXJuICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBuZWdhdGl2ZVBhdHRlcm4nKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMuc3ltYm9sIHx8ICEodGhpcy5zeW1ib2wgaW5zdGFuY2VvZiBOdW1iZXJTeW1ib2wpKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHN5bWJvbCcpO1xuICAgIH1cblxuICAgIGlmICh0eXBlb2YgdGhpcy5tYXhGcmFjdGlvbkRpZ2l0cyAhPT0gJ251bWJlcicpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgbWF4RnJhY3Rpb25EaWdpdHMnKTtcbiAgICB9XG5cbiAgICBpZiAodHlwZW9mIHRoaXMubWluRnJhY3Rpb25EaWdpdHMgIT09ICdudW1iZXInKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIG1pbkZyYWN0aW9uRGlnaXRzJyk7XG4gICAgfVxuXG4gICAgaWYgKHR5cGVvZiB0aGlzLmdyb3VwaW5nVXNlZCAhPT0gJ2Jvb2xlYW4nKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIGdyb3VwaW5nVXNlZCcpO1xuICAgIH1cblxuICAgIGlmICh0eXBlb2YgdGhpcy5wcmltYXJ5R3JvdXBTaXplICE9PSAnbnVtYmVyJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBwcmltYXJ5R3JvdXBTaXplJyk7XG4gICAgfVxuXG4gICAgaWYgKHR5cGVvZiB0aGlzLnNlY29uZGFyeUdyb3VwU2l6ZSAhPT0gJ251bWJlcicpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgc2Vjb25kYXJ5R3JvdXBTaXplJyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEdldCBzeW1ib2wuXG4gICAqXG4gICAqIEByZXR1cm4gTnVtYmVyU3ltYm9sXG4gICAqL1xuICBnZXRTeW1ib2woKSB7XG4gICAgcmV0dXJuIHRoaXMuc3ltYm9sO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgZm9ybWF0dGluZyBydWxlcyBmb3IgdGhpcyBudW1iZXIgKHdoZW4gcG9zaXRpdmUpLlxuICAgKlxuICAgKiBUaGlzIHBhdHRlcm4gdXNlcyB0aGUgVW5pY29kZSBDTERSIG51bWJlciBwYXR0ZXJuIHN5bnRheFxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0UG9zaXRpdmVQYXR0ZXJuKCkge1xuICAgIHJldHVybiB0aGlzLnBvc2l0aXZlUGF0dGVybjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGZvcm1hdHRpbmcgcnVsZXMgZm9yIHRoaXMgbnVtYmVyICh3aGVuIG5lZ2F0aXZlKS5cbiAgICpcbiAgICogVGhpcyBwYXR0ZXJuIHVzZXMgdGhlIFVuaWNvZGUgQ0xEUiBudW1iZXIgcGF0dGVybiBzeW50YXhcbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldE5lZ2F0aXZlUGF0dGVybigpIHtcbiAgICByZXR1cm4gdGhpcy5uZWdhdGl2ZVBhdHRlcm47XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBtYXhpbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3IgKHJvdW5kaW5nIGlmIG5lZWRlZCkuXG4gICAqXG4gICAqIEByZXR1cm4gaW50XG4gICAqL1xuICBnZXRNYXhGcmFjdGlvbkRpZ2l0cygpIHtcbiAgICByZXR1cm4gdGhpcy5tYXhGcmFjdGlvbkRpZ2l0cztcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIG1pbmltdW0gbnVtYmVyIG9mIGRpZ2l0cyBhZnRlciBkZWNpbWFsIHNlcGFyYXRvciAoZmlsbCB3aXRoIFwiMFwiIGlmIG5lZWRlZCkuXG4gICAqXG4gICAqIEByZXR1cm4gaW50XG4gICAqL1xuICBnZXRNaW5GcmFjdGlvbkRpZ2l0cygpIHtcbiAgICByZXR1cm4gdGhpcy5taW5GcmFjdGlvbkRpZ2l0cztcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIFwiZ3JvdXBpbmdcIiBmbGFnLiBUaGlzIGZsYWcgZGVmaW5lcyBpZiBkaWdpdHNcbiAgICogZ3JvdXBpbmcgc2hvdWxkIGJlIHVzZWQgd2hlbiBmb3JtYXR0aW5nIHRoaXMgbnVtYmVyLlxuICAgKlxuICAgKiBAcmV0dXJuIGJvb2xcbiAgICovXG4gIGlzR3JvdXBpbmdVc2VkKCkge1xuICAgIHJldHVybiB0aGlzLmdyb3VwaW5nVXNlZDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIHNpemUgb2YgcHJpbWFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlci5cbiAgICpcbiAgICogQHJldHVybiBpbnRcbiAgICovXG4gIGdldFByaW1hcnlHcm91cFNpemUoKSB7XG4gICAgcmV0dXJuIHRoaXMucHJpbWFyeUdyb3VwU2l6ZTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIHNpemUgb2Ygc2Vjb25kYXJ5IGRpZ2l0cyBncm91cHMgaW4gdGhlIG51bWJlci5cbiAgICpcbiAgICogQHJldHVybiBpbnRcbiAgICovXG4gIGdldFNlY29uZGFyeUdyb3VwU2l6ZSgpIHtcbiAgICByZXR1cm4gdGhpcy5zZWNvbmRhcnlHcm91cFNpemU7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTnVtYmVyU3BlY2lmaWNhdGlvbjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL3NwZWNpZmljYXRpb25zL251bWJlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AuXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIEFjYWRlbWljIEZyZWUgTGljZW5zZSAzLjAgKEFGTC0zLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cDovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMCBBY2FkZW1pYyBGcmVlIExpY2Vuc2UgMy4wIChBRkwtMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuY2xhc3MgTG9jYWxpemF0aW9uRXhjZXB0aW9uIHtcbiAgY29uc3RydWN0b3IobWVzc2FnZSkge1xuICAgIHRoaXMubWVzc2FnZSA9IG1lc3NhZ2U7XG4gICAgdGhpcy5uYW1lID0gJ0xvY2FsaXphdGlvbkV4Y2VwdGlvbic7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTG9jYWxpemF0aW9uRXhjZXB0aW9uO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL2NsZHIvZXhjZXB0aW9uL2xvY2FsaXphdGlvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AuXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIEFjYWRlbWljIEZyZWUgTGljZW5zZSAzLjAgKEFGTC0zLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cDovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMCBBY2FkZW1pYyBGcmVlIExpY2Vuc2UgMy4wIChBRkwtMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuaW1wb3J0IExvY2FsaXphdGlvbkV4Y2VwdGlvbiBmcm9tICcuLi9leGNlcHRpb24vbG9jYWxpemF0aW9uJztcbmltcG9ydCBOdW1iZXJTcGVjaWZpY2F0aW9uIGZyb20gJy4vbnVtYmVyJztcblxuLyoqXG4gKiBDdXJyZW5jeSBkaXNwbGF5IG9wdGlvbjogc3ltYm9sIG5vdGF0aW9uLlxuICovXG5jb25zdCBDVVJSRU5DWV9ESVNQTEFZX1NZTUJPTCA9ICdzeW1ib2wnO1xuXG5cbmNsYXNzIFByaWNlU3BlY2lmaWNhdGlvbiBleHRlbmRzIE51bWJlclNwZWNpZmljYXRpb24ge1xuICAvKipcbiAgICogUHJpY2Ugc3BlY2lmaWNhdGlvbiBjb25zdHJ1Y3Rvci5cbiAgICpcbiAgICogQHBhcmFtIHN0cmluZyBwb3NpdGl2ZVBhdHRlcm4gQ0xEUiBmb3JtYXR0aW5nIHBhdHRlcm4gZm9yIHBvc2l0aXZlIGFtb3VudHNcbiAgICogQHBhcmFtIHN0cmluZyBuZWdhdGl2ZVBhdHRlcm4gQ0xEUiBmb3JtYXR0aW5nIHBhdHRlcm4gZm9yIG5lZ2F0aXZlIGFtb3VudHNcbiAgICogQHBhcmFtIE51bWJlclN5bWJvbCBzeW1ib2wgTnVtYmVyIHN5bWJvbFxuICAgKiBAcGFyYW0gaW50IG1heEZyYWN0aW9uRGlnaXRzIE1heGltdW0gbnVtYmVyIG9mIGRpZ2l0cyBhZnRlciBkZWNpbWFsIHNlcGFyYXRvclxuICAgKiBAcGFyYW0gaW50IG1pbkZyYWN0aW9uRGlnaXRzIE1pbmltdW0gbnVtYmVyIG9mIGRpZ2l0cyBhZnRlciBkZWNpbWFsIHNlcGFyYXRvclxuICAgKiBAcGFyYW0gYm9vbCBncm91cGluZ1VzZWQgSXMgZGlnaXRzIGdyb3VwaW5nIHVzZWQgP1xuICAgKiBAcGFyYW0gaW50IHByaW1hcnlHcm91cFNpemUgU2l6ZSBvZiBwcmltYXJ5IGRpZ2l0cyBncm91cCBpbiB0aGUgbnVtYmVyXG4gICAqIEBwYXJhbSBpbnQgc2Vjb25kYXJ5R3JvdXBTaXplIFNpemUgb2Ygc2Vjb25kYXJ5IGRpZ2l0cyBncm91cCBpbiB0aGUgbnVtYmVyXG4gICAqIEBwYXJhbSBzdHJpbmcgY3VycmVuY3lTeW1ib2wgQ3VycmVuY3kgc3ltYm9sIG9mIHRoaXMgcHJpY2UgKGVnLiA6IOKCrClcbiAgICogQHBhcmFtIGN1cnJlbmN5Q29kZSBDdXJyZW5jeSBjb2RlIG9mIHRoaXMgcHJpY2UgKGUuZy46IEVVUilcbiAgICpcbiAgICogQHRocm93cyBMb2NhbGl6YXRpb25FeGNlcHRpb25cbiAgICovXG4gIGNvbnN0cnVjdG9yKFxuICAgIHBvc2l0aXZlUGF0dGVybixcbiAgICBuZWdhdGl2ZVBhdHRlcm4sXG4gICAgc3ltYm9sLFxuICAgIG1heEZyYWN0aW9uRGlnaXRzLFxuICAgIG1pbkZyYWN0aW9uRGlnaXRzLFxuICAgIGdyb3VwaW5nVXNlZCxcbiAgICBwcmltYXJ5R3JvdXBTaXplLFxuICAgIHNlY29uZGFyeUdyb3VwU2l6ZSxcbiAgICBjdXJyZW5jeVN5bWJvbCxcbiAgICBjdXJyZW5jeUNvZGUsXG4gICkge1xuICAgIHN1cGVyKFxuICAgICAgcG9zaXRpdmVQYXR0ZXJuLFxuICAgICAgbmVnYXRpdmVQYXR0ZXJuLFxuICAgICAgc3ltYm9sLFxuICAgICAgbWF4RnJhY3Rpb25EaWdpdHMsXG4gICAgICBtaW5GcmFjdGlvbkRpZ2l0cyxcbiAgICAgIGdyb3VwaW5nVXNlZCxcbiAgICAgIHByaW1hcnlHcm91cFNpemUsXG4gICAgICBzZWNvbmRhcnlHcm91cFNpemUsXG4gICAgKTtcbiAgICB0aGlzLmN1cnJlbmN5U3ltYm9sID0gY3VycmVuY3lTeW1ib2w7XG4gICAgdGhpcy5jdXJyZW5jeUNvZGUgPSBjdXJyZW5jeUNvZGU7XG5cbiAgICBpZiAoIXRoaXMuY3VycmVuY3lTeW1ib2wgfHwgdHlwZW9mIHRoaXMuY3VycmVuY3lTeW1ib2wgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIGN1cnJlbmN5U3ltYm9sJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLmN1cnJlbmN5Q29kZSB8fCB0eXBlb2YgdGhpcy5jdXJyZW5jeUNvZGUgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIGN1cnJlbmN5Q29kZScpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdHlwZSBvZiBkaXNwbGF5IGZvciBjdXJyZW5jeSBzeW1ib2wuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBzdGF0aWMgZ2V0Q3VycmVuY3lEaXNwbGF5KCkge1xuICAgIHJldHVybiBDVVJSRU5DWV9ESVNQTEFZX1NZTUJPTDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGN1cnJlbmN5IHN5bWJvbFxuICAgKiBlLmcuOiDigqwuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRDdXJyZW5jeVN5bWJvbCgpIHtcbiAgICByZXR1cm4gdGhpcy5jdXJyZW5jeVN5bWJvbDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGN1cnJlbmN5IElTTyBjb2RlXG4gICAqIGUuZy46IEVVUi5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldEN1cnJlbmN5Q29kZSgpIHtcbiAgICByZXR1cm4gdGhpcy5jdXJyZW5jeUNvZGU7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgUHJpY2VTcGVjaWZpY2F0aW9uO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL2NsZHIvc3BlY2lmaWNhdGlvbnMvcHJpY2UuanMiXSwic291cmNlUm9vdCI6IiJ9