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
/******/ 	return __webpack_require__(__webpack_require__.s = 315);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || Function("return this")() || (1,eval)("this");
} catch(e) {
	// This works if the window reference is available
	if(typeof window === "object")
		g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ 235:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * 2007-2019 PrestaShop SA and Contributors
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * NOTICE OF LICENSE
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * This source file is subject to the Open Software License (OSL 3.0)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * that is bundled with this package in the file LICENSE.txt.
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
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * needs please refer to https://www.prestashop.com for more information.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @author    PrestaShop SA <contact@prestashop.com>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @copyright 2007-2019 PrestaShop SA and Contributors
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * International Registered Trademark & Property of PrestaShop SA
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      */
/**
 * These placeholders are used in CLDR number formatting templates.
 * They are meant to be replaced by the correct localized symbols in the number formatting process.
 */


var _numberSymbol = __webpack_require__(69);

var _numberSymbol2 = _interopRequireDefault(_numberSymbol);

var _price = __webpack_require__(95);

var _price2 = _interopRequireDefault(_price);

var _number = __webpack_require__(70);

var _number2 = _interopRequireDefault(_number);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var escapeRE = __webpack_require__(451);

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

      var map = {};
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

  }, {
    key: 'strtr',
    value: function strtr(str, pairs) {
      var substrs = Object.keys(pairs).map(escapeRE);
      return str.split(RegExp('(' + substrs.join('|') + ')')).map(function (part) {
        return pairs[part] || part;
      }).join('');
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

      return new NumberFormatter(specification);
    }
  }]);

  return NumberFormatter;
}();

exports.default = NumberFormatter;

/***/ }),

/***/ 315:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.NumberSymbol = exports.NumberFormatter = exports.NumberSpecification = exports.PriceSpecification = undefined;

var _numberFormatter = __webpack_require__(235);

var _numberFormatter2 = _interopRequireDefault(_numberFormatter);

var _numberSymbol = __webpack_require__(69);

var _numberSymbol2 = _interopRequireDefault(_numberSymbol);

var _price = __webpack_require__(95);

var _price2 = _interopRequireDefault(_price);

var _number = __webpack_require__(70);

var _number2 = _interopRequireDefault(_number);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
exports.PriceSpecification = _price2.default;
exports.NumberSpecification = _number2.default;
exports.NumberFormatter = _numberFormatter2.default;
exports.NumberSymbol = _numberSymbol2.default;

/***/ }),

/***/ 451:
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {/**
 * lodash (Custom Build) <https://lodash.com/>
 * Build: `lodash modularize exports="npm" -o ./`
 * Copyright jQuery Foundation and other contributors <https://jquery.org/>
 * Released under MIT license <https://lodash.com/license>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 */

/** Used as references for various `Number` constants. */
var INFINITY = 1 / 0;

/** `Object#toString` result references. */
var symbolTag = '[object Symbol]';

/**
 * Used to match `RegExp`
 * [syntax characters](http://ecma-international.org/ecma-262/6.0/#sec-patterns).
 */
var reRegExpChar = /[\\^$.*+?()[\]{}|]/g,
    reHasRegExpChar = RegExp(reRegExpChar.source);

/** Detect free variable `global` from Node.js. */
var freeGlobal = typeof global == 'object' && global && global.Object === Object && global;

/** Detect free variable `self`. */
var freeSelf = typeof self == 'object' && self && self.Object === Object && self;

/** Used as a reference to the global object. */
var root = freeGlobal || freeSelf || Function('return this')();

/** Used for built-in method references. */
var objectProto = Object.prototype;

/**
 * Used to resolve the
 * [`toStringTag`](http://ecma-international.org/ecma-262/6.0/#sec-object.prototype.tostring)
 * of values.
 */
var objectToString = objectProto.toString;

/** Built-in value references. */
var Symbol = root.Symbol;

/** Used to convert symbols to primitives and strings. */
var symbolProto = Symbol ? Symbol.prototype : undefined,
    symbolToString = symbolProto ? symbolProto.toString : undefined;

/**
 * The base implementation of `_.toString` which doesn't convert nullish
 * values to empty strings.
 *
 * @private
 * @param {*} value The value to process.
 * @returns {string} Returns the string.
 */
function baseToString(value) {
  // Exit early for strings to avoid a performance hit in some environments.
  if (typeof value == 'string') {
    return value;
  }
  if (isSymbol(value)) {
    return symbolToString ? symbolToString.call(value) : '';
  }
  var result = (value + '');
  return (result == '0' && (1 / value) == -INFINITY) ? '-0' : result;
}

/**
 * Checks if `value` is object-like. A value is object-like if it's not `null`
 * and has a `typeof` result of "object".
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 * @example
 *
 * _.isObjectLike({});
 * // => true
 *
 * _.isObjectLike([1, 2, 3]);
 * // => true
 *
 * _.isObjectLike(_.noop);
 * // => false
 *
 * _.isObjectLike(null);
 * // => false
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/**
 * Checks if `value` is classified as a `Symbol` primitive or object.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a symbol, else `false`.
 * @example
 *
 * _.isSymbol(Symbol.iterator);
 * // => true
 *
 * _.isSymbol('abc');
 * // => false
 */
function isSymbol(value) {
  return typeof value == 'symbol' ||
    (isObjectLike(value) && objectToString.call(value) == symbolTag);
}

/**
 * Converts `value` to a string. An empty string is returned for `null`
 * and `undefined` values. The sign of `-0` is preserved.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to process.
 * @returns {string} Returns the string.
 * @example
 *
 * _.toString(null);
 * // => ''
 *
 * _.toString(-0);
 * // => '-0'
 *
 * _.toString([1, 2, 3]);
 * // => '1,2,3'
 */
function toString(value) {
  return value == null ? '' : baseToString(value);
}

/**
 * Escapes the `RegExp` special characters "^", "$", "\", ".", "*", "+",
 * "?", "(", ")", "[", "]", "{", "}", and "|" in `string`.
 *
 * @static
 * @memberOf _
 * @since 3.0.0
 * @category String
 * @param {string} [string=''] The string to escape.
 * @returns {string} Returns the escaped string.
 * @example
 *
 * _.escapeRegExp('[lodash](https://lodash.com/)');
 * // => '\[lodash\]\(https://lodash\.com/\)'
 */
function escapeRegExp(string) {
  string = toString(string);
  return (string && reHasRegExpChar.test(string))
    ? string.replace(reRegExpChar, '\\$&')
    : string;
}

module.exports = escapeRegExp;

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(0)))

/***/ }),

/***/ 69:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * 2007-2019 PrestaShop SA and Contributors
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * NOTICE OF LICENSE
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * This source file is subject to the Open Software License (OSL 3.0)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * that is bundled with this package in the file LICENSE.txt.
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
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * needs please refer to https://www.prestashop.com for more information.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @author    PrestaShop SA <contact@prestashop.com>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @copyright 2007-2019 PrestaShop SA and Contributors
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * International Registered Trademark & Property of PrestaShop SA
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      */


var _localization = __webpack_require__(72);

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

/***/ 70:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * 2007-2019 PrestaShop SA and Contributors
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * NOTICE OF LICENSE
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * This source file is subject to the Open Software License (OSL 3.0)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * that is bundled with this package in the file LICENSE.txt.
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
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * needs please refer to https://www.prestashop.com for more information.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @author    PrestaShop SA <contact@prestashop.com>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @copyright 2007-2019 PrestaShop SA and Contributors
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * International Registered Trademark & Property of PrestaShop SA
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      */


var _localization = __webpack_require__(72);

var _localization2 = _interopRequireDefault(_localization);

var _numberSymbol = __webpack_require__(69);

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

/***/ 72:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
var LocalizationException = function LocalizationException(message) {
  _classCallCheck(this, LocalizationException);

  this.message = message;
  this.name = 'LocalizationException';
};

exports.default = LocalizationException;

/***/ }),

/***/ 95:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _localization = __webpack_require__(72);

var _localization2 = _interopRequireDefault(_localization);

var _number = __webpack_require__(70);

var _number2 = _interopRequireDefault(_number);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; } /**
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * 2007-2019 PrestaShop SA and Contributors
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * NOTICE OF LICENSE
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * This source file is subject to the Open Software License (OSL 3.0)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * that is bundled with this package in the file LICENSE.txt.
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
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * needs please refer to https://www.prestashop.com for more information.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                *
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @author    PrestaShop SA <contact@prestashop.com>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @copyright 2007-2019 PrestaShop SA and Contributors
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjA/ODU5MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzPzM2OTgqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvY2xkci9udW1iZXItZm9ybWF0dGVyLmpzIiwid2VicGFjazovLy8uL2pzL2FwcC9jbGRyL2luZGV4LmpzIiwid2VicGFjazovLy8uL34vbG9kYXNoLmVzY2FwZXJlZ2V4cC9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvY2xkci9udW1iZXItc3ltYm9sLmpzIiwid2VicGFjazovLy8uL2pzL2FwcC9jbGRyL3NwZWNpZmljYXRpb25zL251bWJlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvY2xkci9leGNlcHRpb24vbG9jYWxpemF0aW9uLmpzIiwid2VicGFjazovLy8uL2pzL2FwcC9jbGRyL3NwZWNpZmljYXRpb25zL3ByaWNlLmpzIl0sIm5hbWVzIjpbImVzY2FwZVJFIiwicmVxdWlyZSIsIkNVUlJFTkNZX1NZTUJPTF9QTEFDRUhPTERFUiIsIkRFQ0lNQUxfU0VQQVJBVE9SX1BMQUNFSE9MREVSIiwiR1JPVVBfU0VQQVJBVE9SX1BMQUNFSE9MREVSIiwiTUlOVVNfU0lHTl9QTEFDRUhPTERFUiIsIlBFUkNFTlRfU1lNQk9MX1BMQUNFSE9MREVSIiwiUExVU19TSUdOX1BMQUNFSE9MREVSIiwiTnVtYmVyRm9ybWF0dGVyIiwic3BlY2lmaWNhdGlvbiIsIm51bWJlclNwZWNpZmljYXRpb24iLCJudW1iZXIiLCJ1bmRlZmluZWQiLCJudW0iLCJNYXRoIiwiYWJzIiwidG9GaXhlZCIsImdldE1heEZyYWN0aW9uRGlnaXRzIiwiZXh0cmFjdE1ham9yTWlub3JEaWdpdHMiLCJtYWpvckRpZ2l0cyIsIm1pbm9yRGlnaXRzIiwic3BsaXRNYWpvckdyb3VwcyIsImFkanVzdE1pbm9yRGlnaXRzWmVyb2VzIiwiZm9ybWF0dGVkTnVtYmVyIiwicGF0dGVybiIsImdldENsZHJQYXR0ZXJuIiwiYWRkUGxhY2Vob2xkZXJzIiwicmVwbGFjZVN5bWJvbHMiLCJwZXJmb3JtU3BlY2lmaWNSZXBsYWNlbWVudHMiLCJyZXN1bHQiLCJ0b1N0cmluZyIsInNwbGl0IiwiZGlnaXQiLCJpc0dyb3VwaW5nVXNlZCIsInJldmVyc2UiLCJncm91cHMiLCJwdXNoIiwic3BsaWNlIiwiZ2V0UHJpbWFyeUdyb3VwU2l6ZSIsImxlbmd0aCIsImdldFNlY29uZGFyeUdyb3VwU2l6ZSIsIm5ld0dyb3VwcyIsImZvckVhY2giLCJncm91cCIsImpvaW4iLCJyZXBsYWNlIiwiZ2V0TWluRnJhY3Rpb25EaWdpdHMiLCJwYWRFbmQiLCJpc05lZ2F0aXZlIiwiZ2V0TmVnYXRpdmVQYXR0ZXJuIiwiZ2V0UG9zaXRpdmVQYXR0ZXJuIiwic3ltYm9scyIsImdldFN5bWJvbCIsIm1hcCIsImdldERlY2ltYWwiLCJnZXRHcm91cCIsImdldE1pbnVzU2lnbiIsImdldFBlcmNlbnRTaWduIiwiZ2V0UGx1c1NpZ24iLCJzdHJ0ciIsInN0ciIsInBhaXJzIiwic3Vic3RycyIsIk9iamVjdCIsImtleXMiLCJSZWdFeHAiLCJwYXJ0IiwiUHJpY2VTcGVjaWZpY2F0aW9uIiwiZ2V0Q3VycmVuY3lTeW1ib2wiLCJzcGVjaWZpY2F0aW9ucyIsInN5bWJvbCIsIk51bWJlclN5bWJvbCIsImN1cnJlbmN5U3ltYm9sIiwicG9zaXRpdmVQYXR0ZXJuIiwibmVnYXRpdmVQYXR0ZXJuIiwicGFyc2VJbnQiLCJtYXhGcmFjdGlvbkRpZ2l0cyIsIm1pbkZyYWN0aW9uRGlnaXRzIiwiZ3JvdXBpbmdVc2VkIiwicHJpbWFyeUdyb3VwU2l6ZSIsInNlY29uZGFyeUdyb3VwU2l6ZSIsImN1cnJlbmN5Q29kZSIsIk51bWJlclNwZWNpZmljYXRpb24iLCJkZWNpbWFsIiwibGlzdCIsInBlcmNlbnRTaWduIiwibWludXNTaWduIiwicGx1c1NpZ24iLCJleHBvbmVudGlhbCIsInN1cGVyc2NyaXB0aW5nRXhwb25lbnQiLCJwZXJNaWxsZSIsImluZmluaXR5IiwibmFuIiwidmFsaWRhdGVEYXRhIiwiTG9jYWxpemF0aW9uRXhjZXB0aW9uIiwibWVzc2FnZSIsIm5hbWUiLCJDVVJSRU5DWV9ESVNQTEFZX1NZTUJPTCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7Ozs7QUNoRUE7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsQ0FBQzs7QUFFRDtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLDRDQUE0Qzs7QUFFNUM7Ozs7Ozs7Ozs7Ozs7Ozs7O3FqQkNwQkE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXdCQTs7Ozs7O0FBSUE7Ozs7QUFDQTs7OztBQUNBOzs7Ozs7Ozs7O0FBRUEsSUFBTUEsV0FBVyxtQkFBQUMsQ0FBUSxHQUFSLENBQWpCOztBQUVBLElBQU1DLDhCQUE4QixHQUFwQztBQUNBLElBQU1DLGdDQUFnQyxHQUF0QztBQUNBLElBQU1DLDhCQUE4QixHQUFwQztBQUNBLElBQU1DLHlCQUF5QixHQUEvQjtBQUNBLElBQU1DLDZCQUE2QixHQUFuQztBQUNBLElBQU1D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QzFCLGFBQU9nQix1QkFBUDtBQUNEOzs7O0VBMUQ4QmYsZ0I7O2tCQWlGbEJmLGtCIiwiZmlsZSI6ImNsZHIuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAzMTUpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDFlNjYyNjM5MDBlOTY2ZGZiYmYwIiwidmFyIGc7XHJcblxyXG4vLyBUaGlzIHdvcmtzIGluIG5vbi1zdHJpY3QgbW9kZVxyXG5nID0gKGZ1bmN0aW9uKCkge1xyXG5cdHJldHVybiB0aGlzO1xyXG59KSgpO1xyXG5cclxudHJ5IHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIGV2YWwgaXMgYWxsb3dlZCAoc2VlIENTUClcclxuXHRnID0gZyB8fCBGdW5jdGlvbihcInJldHVybiB0aGlzXCIpKCkgfHwgKDEsZXZhbCkoXCJ0aGlzXCIpO1xyXG59IGNhdGNoKGUpIHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIHRoZSB3aW5kb3cgcmVmZXJlbmNlIGlzIGF2YWlsYWJsZVxyXG5cdGlmKHR5cGVvZiB3aW5kb3cgPT09IFwib2JqZWN0XCIpXHJcblx0XHRnID0gd2luZG93O1xyXG59XHJcblxyXG4vLyBnIGNhbiBzdGlsbCBiZSB1bmRlZmluZWQsIGJ1dCBub3RoaW5nIHRvIGRvIGFib3V0IGl0Li4uXHJcbi8vIFdlIHJldHVybiB1bmRlZmluZWQsIGluc3RlYWQgb2Ygbm90aGluZyBoZXJlLCBzbyBpdCdzXHJcbi8vIGVhc2llciB0byBoYW5kbGUgdGhpcyBjYXNlLiBpZighZ2xvYmFsKSB7IC4uLn1cclxuXHJcbm1vZHVsZS5leHBvcnRzID0gZztcclxuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSAwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzNCAzNyA0NSA0OSIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG4vKipcbiAqIFRoZXNlIHBsYWNlaG9sZGVycyBhcmUgdXNlZCBpbiBDTERSIG51bWJlciBmb3JtYXR0aW5nIHRlbXBsYXRlcy5cbiAqIFRoZXkgYXJlIG1lYW50IHRvIGJlIHJlcGxhY2VkIGJ5IHRoZSBjb3JyZWN0IGxvY2FsaXplZCBzeW1ib2xzIGluIHRoZSBudW1iZXIgZm9ybWF0dGluZyBwcm9jZXNzLlxuICovXG5pbXBvcnQgTnVtYmVyU3ltYm9sIGZyb20gJy4vbnVtYmVyLXN5bWJvbCc7XG5pbXBvcnQgUHJpY2VTcGVjaWZpY2F0aW9uIGZyb20gJy4vc3BlY2lmaWNhdGlvbnMvcHJpY2UnO1xuaW1wb3J0IE51bWJlclNwZWNpZmljYXRpb24gZnJvbSAnLi9zcGVjaWZpY2F0aW9ucy9udW1iZXInO1xuXG5jb25zdCBlc2NhcGVSRSA9IHJlcXVpcmUoJ2xvZGFzaC5lc2NhcGVyZWdleHAnKTtcblxuY29uc3QgQ1VSUkVOQ1lfU1lNQk9MX1BMQUNFSE9MREVSID0gJ8KkJztcbmNvbnN0IERFQ0lNQUxfU0VQQVJBVE9SX1BMQUNFSE9MREVSID0gJy4nO1xuY29uc3QgR1JPVVBfU0VQQVJBVE9SX1BMQUNFSE9MREVSID0gJywnO1xuY29uc3QgTUlOVVNfU0lHTl9QTEFDRUhPTERFUiA9ICctJztcbmNvbnN0IFBFUkNFTlRfU1lNQk9MX1BMQUNFSE9MREVSID0gJyUnO1xuY29uc3QgUExVU19TSUdOX1BMQUNFSE9MREVSID0gJysnO1xuXG5jbGFzcyBOdW1iZXJGb3JtYXR0ZXIge1xuICAvKipcbiAgICogQHBhcmFtIE51bWJlclNwZWNpZmljYXRpb24gc3BlY2lmaWNhdGlvbiBOdW1iZXIgc3BlY2lmaWNhdGlvbiB0byBiZSB1c2VkXG4gICAqICAgKGNhbiBiZSBhIG51bWJlciBzcGVjLCBhIHByaWNlIHNwZWMsIGEgcGVyY2VudGFnZSBzcGVjKVxuICAgKi9cbiAgY29uc3RydWN0b3Ioc3BlY2lmaWNhdGlvbikge1xuICAgIHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbiA9IHNwZWNpZmljYXRpb247XG4gIH1cblxuICAvKipcbiAgICogRm9ybWF0cyB0aGUgcGFzc2VkIG51bWJlciBhY2NvcmRpbmcgdG8gc3BlY2lmaWNhdGlvbnMuXG4gICAqXG4gICAqIEBwYXJhbSBpbnR8ZmxvYXR8c3RyaW5nIG51bWJlciBUaGUgbnVtYmVyIHRvIGZvcm1hdFxuICAgKiBAcGFyYW0gTnVtYmVyU3BlY2lmaWNhdGlvbiBzcGVjaWZpY2F0aW9uIE51bWJlciBzcGVjaWZpY2F0aW9uIHRvIGJlIHVzZWRcbiAgICogICAoY2FuIGJlIGEgbnVtYmVyIHNwZWMsIGEgcHJpY2Ugc3BlYywgYSBwZXJjZW50YWdlIHNwZWMpXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nIFRoZSBmb3JtYXR0ZWQgbnVtYmVyXG4gICAqICAgICAgICAgICAgICAgIFlvdSBzaG91bGQgdXNlIHRoaXMgdGhpcyB2YWx1ZSBmb3IgZGlzcGxheSwgd2l0aG91dCBtb2RpZnlpbmcgaXRcbiAgICovXG4gIGZvcm1hdChudW1iZXIsIHNwZWNpZmljYXRpb24pIHtcbiAgICBpZiAoc3BlY2lmaWNhdGlvbiAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICB0aGlzLm51bWJlclNwZWNpZmljYXRpb24gPSBzcGVjaWZpY2F0aW9uO1xuICAgIH1cblxuICAgIC8qXG4gICAgICogV2UgbmVlZCB0byB3b3JrIG9uIHRoZSBhYnNvbHV0ZSB2YWx1ZSBmaXJzdC5cbiAgICAgKiBUaGVuIHRoZSBDTERSIHBhdHRlcm4gd2lsbCBhZGQgdGhlIHNpZ24gaWYgcmVsZXZhbnQgKGF0IHRoZSBlbmQpLlxuICAgICAqL1xuICAgIGNvbnN0IG51bSA9IE1hdGguYWJzKG51bWJlcikudG9GaXhlZCh0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0TWF4RnJhY3Rpb25EaWdpdHMoKSk7XG5cbiAgICBsZXQgW21ham9yRGlnaXRzLCBtaW5vckRpZ2l0c10gPSB0aGlzLmV4dHJhY3RNYWpvck1pbm9yRGlnaXRzKG51bSk7XG4gICAgbWFqb3JEaWdpdHMgPSB0aGlzLnNwbGl0TWFqb3JHcm91cHMobWFqb3JEaWdpdHMpO1xuICAgIG1pbm9yRGlnaXRzID0gdGhpcy5hZGp1c3RNaW5vckRpZ2l0c1plcm9lcyhtaW5vckRpZ2l0cyk7XG5cbiAgICAvLyBBc3NlbWJsZSB0aGUgZmluYWwgbnVtYmVyXG4gICAgbGV0IGZvcm1hdHRlZE51bWJlciA9IG1ham9yRGlnaXRzO1xuICAgIGlmIChtaW5vckRpZ2l0cykge1xuICAgICAgZm9ybWF0dGVkTnVtYmVyICs9IERFQ0lNQUxfU0VQQVJBVE9SX1BMQUNFSE9MREVSICsgbWlub3JEaWdpdHM7XG4gICAgfVxuXG4gICAgLy8gR2V0IHRoZSBnb29kIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuLiBTaWduIGlzIGltcG9ydGFudCBoZXJlICFcbiAgICBjb25zdCBwYXR0ZXJuID0gdGhpcy5nZXRDbGRyUGF0dGVybihtYWpvckRpZ2l0cyA8IDApO1xuICAgIGZvcm1hdHRlZE51bWJlciA9IHRoaXMuYWRkUGxhY2Vob2xkZXJzKGZvcm1hdHRlZE51bWJlciwgcGF0dGVybik7XG4gICAgZm9ybWF0dGVkTnVtYmVyID0gdGhpcy5yZXBsYWNlU3ltYm9scyhmb3JtYXR0ZWROdW1iZXIpO1xuXG4gICAgZm9ybWF0dGVkTnVtYmVyID0gdGhpcy5wZXJmb3JtU3BlY2lmaWNSZXBsYWNlbWVudHMoZm9ybWF0dGVkTnVtYmVyKTtcblxuICAgIHJldHVybiBmb3JtYXR0ZWROdW1iZXI7XG4gIH1cblxuICAvKipcbiAgICogR2V0IG51bWJlcidzIG1ham9yIGFuZCBtaW5vciBkaWdpdHMuXG4gICAqXG4gICAqIE1ham9yIGRpZ2l0cyBhcmUgdGhlIFwiaW50ZWdlclwiIHBhcnQgKGJlZm9yZSBkZWNpbWFsIHNlcGFyYXRvciksXG4gICAqIG1pbm9yIGRpZ2l0cyBhcmUgdGhlIGZyYWN0aW9uYWwgcGFydFxuICAgKiBSZXN1bHQgd2lsbCBiZSBhbiBhcnJheSBvZiBleGFjdGx5IDIgaXRlbXM6IFttYWpvckRpZ2l0cywgbWlub3JEaWdpdHNdXG4gICAqXG4gICAqIFVzYWdlIGV4YW1wbGU6XG4gICAqICBsaXN0KG1ham9yRGlnaXRzLCBtaW5vckRpZ2l0cykgPSB0aGlzLmdldE1ham9yTWlub3JEaWdpdHMoZGVjaW1hbE51bWJlcik7XG4gICAqXG4gICAqIEBwYXJhbSBEZWNpbWFsTnVtYmVyIG51bWJlclxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1tdXG4gICAqL1xuICBleHRyYWN0TWFqb3JNaW5vckRpZ2l0cyhudW1iZXIpIHtcbiAgICAvLyBHZXQgdGhlIG51bWJlcidzIG1ham9yIGFuZCBtaW5vciBkaWdpdHMuXG4gICAgY29uc3QgcmVzdWx0ID0gbnVtYmVyLnRvU3RyaW5nKCkuc3BsaXQoJy4nKTtcbiAgICBjb25zdCBtYWpvckRpZ2l0cyA9IHJlc3VsdFswXTtcbiAgICBjb25zdCBtaW5vckRpZ2l0cyA9IChyZXN1bHRbMV0gPT09IHVuZGVmaW5lZCkgPyAnJyA6IHJlc3VsdFsxXTtcbiAgICByZXR1cm4gW21ham9yRGlnaXRzLCBtaW5vckRpZ2l0c107XG4gIH1cblxuICAvKipcbiAgICogU3BsaXRzIG1ham9yIGRpZ2l0cyBpbnRvIGdyb3Vwcy5cbiAgICpcbiAgICogZS5nLjogR2l2ZW4gdGhlIG1ham9yIGRpZ2l0cyBcIjEyMzQ1NjdcIiwgYW5kIG1ham9yIGdyb3VwIHNpemVcbiAgICogIGNvbmZpZ3VyZWQgdG8gMyBkaWdpdHMsIHRoZSByZXN1bHQgd291bGQgYmUgXCIxIDIzNCA1NjdcIlxuICAgKlxuICAgKiBAcGFyYW0gc3RyaW5nIG1ham9yRGlnaXRzIFRoZSBtYWpvciBkaWdpdHMgdG8gYmUgZ3JvdXBlZFxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZyBUaGUgZ3JvdXBlZCBtYWpvciBkaWdpdHNcbiAgICovXG4gIHNwbGl0TWFqb3JHcm91cHMoZGlnaXQpIHtcbiAgICBpZiAoIXRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5pc0dyb3VwaW5nVXNlZCgpKSB7XG4gICAgICByZXR1cm4gZGlnaXQ7XG4gICAgfVxuXG4gICAgLy8gUmV2ZXJzZSB0aGUgbWFqb3IgZGlnaXRzLCBzaW5jZSB0aGV5IGFyZSBncm91cGVkIGZyb20gdGhlIHJpZ2h0LlxuICAgIGNvbnN0IG1ham9yRGlnaXRzID0gZGlnaXQuc3BsaXQoJycpLnJldmVyc2UoKTtcblxuICAgIC8vIEdyb3VwIHRoZSBtYWpvciBkaWdpdHMuXG4gICAgbGV0IGdyb3VwcyA9IFtdO1xuICAgIGdyb3Vwcy5wdXNoKG1ham9yRGlnaXRzLnNwbGljZSgwLCB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0UHJpbWFyeUdyb3VwU2l6ZSgpKSk7XG4gICAgd2hpbGUgKG1ham9yRGlnaXRzLmxlbmd0aCkge1xuICAgICAgZ3JvdXBzLnB1c2gobWFqb3JEaWdpdHMuc3BsaWNlKDAsIHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRTZWNvbmRhcnlHcm91cFNpemUoKSkpO1xuICAgIH1cblxuICAgIC8vIFJldmVyc2UgYmFjayB0aGUgZGlnaXRzIGFuZCB0aGUgZ3JvdXBzXG4gICAgZ3JvdXBzID0gZ3JvdXBzLnJldmVyc2UoKTtcbiAgICBjb25zdCBuZXdHcm91cHMgPSBbXTtcbiAgICBncm91cHMuZm9yRWFjaCgoZ3JvdXApID0+IHtcbiAgICAgIG5ld0dyb3Vwcy5wdXNoKGdyb3VwLnJldmVyc2UoKS5qb2luKCcnKSk7XG4gICAgfSk7XG5cbiAgICAvLyBSZWNvbnN0cnVjdCB0aGUgbWFqb3IgZGlnaXRzLlxuICAgIHJldHVybiBuZXdHcm91cHMuam9pbihHUk9VUF9TRVBBUkFUT1JfUExBQ0VIT0xERVIpO1xuICB9XG5cbiAgLyoqXG4gICAqIEFkZHMgb3IgcmVtb3ZlIHRyYWlsaW5nIHplcm9lcywgZGVwZW5kaW5nIG9uIHNwZWNpZmllZCBtaW4gYW5kIG1heCBmcmFjdGlvbiBkaWdpdHMgbnVtYmVycy5cbiAgICpcbiAgICogQHBhcmFtIHN0cmluZyBtaW5vckRpZ2l0cyBEaWdpdHMgdG8gYmUgYWRqdXN0ZWQgd2l0aCAodHJpbW1lZCBvciBwYWRkZWQpIHplcm9lc1xuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZyBUaGUgYWRqdXN0ZWQgbWlub3IgZGlnaXRzXG4gICAqL1xuICBhZGp1c3RNaW5vckRpZ2l0c1plcm9lcyhtaW5vckRpZ2l0cykge1xuICAgIGxldCBkaWdpdCA9IG1pbm9yRGlnaXRzO1xuICAgIGlmIChkaWdpdC5sZW5ndGggPiB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0TWF4RnJhY3Rpb25EaWdpdHMoKSkge1xuICAgICAgLy8gU3RyaXAgYW55IHRyYWlsaW5nIHplcm9lcy5cbiAgICAgIGRpZ2l0ID0gZGlnaXQucmVwbGFjZSgvMCskLywgJycpO1xuICAgIH1cblxuICAgIGlmIChkaWdpdC5sZW5ndGggPCB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0TWluRnJhY3Rpb25EaWdpdHMoKSkge1xuICAgICAgLy8gUmUtYWRkIG5lZWRlZCB6ZXJvZXNcbiAgICAgIGRpZ2l0ID0gZGlnaXQucGFkRW5kKFxuICAgICAgICB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0TWluRnJhY3Rpb25EaWdpdHMoKSxcbiAgICAgICAgJzAnLFxuICAgICAgKTtcbiAgICB9XG5cbiAgICByZXR1cm4gZGlnaXQ7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBDTERSIGZvcm1hdHRpbmcgcGF0dGVybi5cbiAgICpcbiAgICogQHNlZSBodHRwOi8vY2xkci51bmljb2RlLm9yZy90cmFuc2xhdGlvbi9udW1iZXItcGF0dGVybnNcbiAgICpcbiAgICogQHBhcmFtIGJvb2wgaXNOZWdhdGl2ZSBJZiB0cnVlLCB0aGUgbmVnYXRpdmUgcGF0dGVyblxuICAgKiB3aWxsIGJlIHJldHVybmVkIGluc3RlYWQgb2YgdGhlIHBvc2l0aXZlIG9uZVxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZyBUaGUgQ0xEUiBmb3JtYXR0aW5nIHBhdHRlcm5cbiAgICovXG4gIGdldENsZHJQYXR0ZXJuKGlzTmVnYXRpdmUpIHtcbiAgICBpZiAoaXNOZWdhdGl2ZSkge1xuICAgICAgcmV0dXJuIHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXROZWdhdGl2ZVBhdHRlcm4oKTtcbiAgICB9XG5cbiAgICByZXR1cm4gdGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmdldFBvc2l0aXZlUGF0dGVybigpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlcGxhY2UgcGxhY2Vob2xkZXIgbnVtYmVyIHN5bWJvbHMgd2l0aCByZWxldmFudCBudW1iZXJpbmcgc3lzdGVtJ3Mgc3ltYm9scy5cbiAgICpcbiAgICogQHBhcmFtIHN0cmluZyBudW1iZXJcbiAgICogICAgICAgICAgICAgICAgICAgICAgIFRoZSBudW1iZXIgdG8gcHJvY2Vzc1xuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKiAgICAgICAgICAgICAgICBUaGUgbnVtYmVyIHdpdGggcmVwbGFjZWQgc3ltYm9sc1xuICAgKi9cbiAgcmVwbGFjZVN5bWJvbHMobnVtYmVyKSB7XG4gICAgY29uc3Qgc3ltYm9scyA9IHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRTeW1ib2woKTtcblxuICAgIGNvbnN0IG1hcCA9IHt9O1xuICAgIG1hcFtERUNJTUFMX1NFUEFSQVRPUl9QTEFDRUhPTERFUl0gPSBzeW1ib2xzLmdldERlY2ltYWwoKTtcbiAgICBtYXBbR1JPVVBfU0VQQVJBVE9SX1BMQUNFSE9MREVSXSA9IHN5bWJvbHMuZ2V0R3JvdXAoKTtcbiAgICBtYXBbTUlOVVNfU0lHTl9QTEFDRUhPTERFUl0gPSBzeW1ib2xzLmdldE1pbnVzU2lnbigpO1xuICAgIG1hcFtQRVJDRU5UX1NZTUJPTF9QTEFDRUhPTERFUl0gPSBzeW1ib2xzLmdldFBlcmNlbnRTaWduKCk7XG4gICAgbWFwW1BMVVNfU0lHTl9QTEFDRUhPTERFUl0gPSBzeW1ib2xzLmdldFBsdXNTaWduKCk7XG5cbiAgICByZXR1cm4gdGhpcy5zdHJ0cihudW1iZXIsIG1hcCk7XG4gIH1cblxuICAvKipcbiAgICogc3RydHIoKSBmb3IgSmF2YVNjcmlwdFxuICAgKiBUcmFuc2xhdGUgY2hhcmFjdGVycyBvciByZXBsYWNlIHN1YnN0cmluZ3NcbiAgICpcbiAgICogQHBhcmFtIHN0clxuICAgKiAgU3RyaW5nIHRvIHBhcnNlXG4gICAqIEBwYXJhbSBwYWlyc1xuICAgKiAgSGFzaCBvZiAoJ2Zyb20nID0+ICd0bycsIC4uLikuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBzdHJ0cihzdHIsIHBhaXJzKSB7XG4gICAgY29uc3Qgc3Vic3RycyA9IE9iamVjdC5rZXlzKHBhaXJzKS5tYXAoZXNjYXBlUkUpO1xuICAgIHJldHVybiBzdHIuc3BsaXQoUmVnRXhwKGAoJHtzdWJzdHJzLmpvaW4oJ3wnKX0pYCkpXG4gICAgICAgICAgICAgIC5tYXAocGFydCA9PiBwYWlyc1twYXJ0XSB8fCBwYXJ0KVxuICAgICAgICAgICAgICAuam9pbignJyk7XG4gIH1cblxuXG4gIC8qKlxuICAgKiBBZGQgbWlzc2luZyBwbGFjZWhvbGRlcnMgdG8gdGhlIG51bWJlciB1c2luZyB0aGUgcGFzc2VkIENMRFIgcGF0dGVybi5cbiAgICpcbiAgICogTWlzc2luZyBwbGFjZWhvbGRlcnMgY2FuIGJlIHRoZSBwZXJjZW50IHNpZ24sIGN1cnJlbmN5IHN5bWJvbCwgZXRjLlxuICAgKlxuICAgKiBlLmcuIHdpdGggYSBjdXJyZW5jeSBDTERSIHBhdHRlcm46XG4gICAqICAtIFBhc3NlZCBudW1iZXIgKHBhcnRpYWxseSBmb3JtYXR0ZWQpOiAxLDIzNC41NjdcbiAgICogIC0gUmV0dXJuZWQgbnVtYmVyOiAxLDIzNC41NjcgwqRcbiAgICogIChcIsKkXCIgc3ltYm9sIGlzIHRoZSBjdXJyZW5jeSBzeW1ib2wgcGxhY2Vob2xkZXIpXG4gICAqXG4gICAqIEBzZWUgaHR0cDovL2NsZHIudW5pY29kZS5vcmcvdHJhbnNsYXRpb24vbnVtYmVyLXBhdHRlcm5zXG4gICAqXG4gICAqIEBwYXJhbSBmb3JtYXR0ZWROdW1iZXJcbiAgICogIE51bWJlciB0byBwcm9jZXNzXG4gICAqIEBwYXJhbSBwYXR0ZXJuXG4gICAqICBDTERSIGZvcm1hdHRpbmcgcGF0dGVybiB0byB1c2VcbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGFkZFBsYWNlaG9sZGVycyhmb3JtYXR0ZWROdW1iZXIsIHBhdHRlcm4pIHtcbiAgICAvKlxuICAgICAqIFJlZ2V4IGdyb3VwcyBleHBsYW5hdGlvbjpcbiAgICAgKiAjICAgICAgICAgIDogbGl0ZXJhbCBcIiNcIiBjaGFyYWN0ZXIuIE9uY2UuXG4gICAgICogKCwjKykqICAgICA6IGFueSBvdGhlciBcIiNcIiBjaGFyYWN0ZXJzIGdyb3VwLCBzZXBhcmF0ZWQgYnkgXCIsXCIuIFplcm8gdG8gaW5maW5pdHkgdGltZXMuXG4gICAgICogMCAgICAgICAgICA6IGxpdGVyYWwgXCIwXCIgY2hhcmFjdGVyLiBPbmNlLlxuICAgICAqIChcXC5bMCNdKykqIDogYW55IGNvbWJpbmF0aW9uIG9mIFwiMFwiIGFuZCBcIiNcIiBjaGFyYWN0ZXJzIGdyb3Vwcywgc2VwYXJhdGVkIGJ5ICcuJy5cbiAgICAgKiAgICAgICAgICAgICAgWmVybyB0byBpbmZpbml0eSB0aW1lcy5cbiAgICAgKi9cbiAgICByZXR1cm4gcGF0dGVybi5yZXBsYWNlKC8jPygsIyspKjAoXFwuWzAjXSspKi8sIGZvcm1hdHRlZE51bWJlcik7XG4gIH1cblxuICAvKipcbiAgICogUGVyZm9ybSBzb21lIG1vcmUgc3BlY2lmaWMgcmVwbGFjZW1lbnRzLlxuICAgKlxuICAgKiBTcGVjaWZpYyByZXBsYWNlbWVudHMgYXJlIG5lZWRlZCB3aGVuIG51bWJlciBzcGVjaWZpY2F0aW9uIGlzIGV4dGVuZGVkLlxuICAgKiBGb3IgaW5zdGFuY2UsIHByaWNlcyBoYXZlIGFuIGV4dGVuZGVkIG51bWJlciBzcGVjaWZpY2F0aW9uIGluIG9yZGVyIHRvXG4gICAqIGFkZCBjdXJyZW5jeSBzeW1ib2wgdG8gdGhlIGZvcm1hdHRlZCBudW1iZXIuXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgZm9ybWF0dGVkTnVtYmVyXG4gICAqXG4gICAqIEByZXR1cm4gbWl4ZWRcbiAgICovXG4gIHBlcmZvcm1TcGVjaWZpY1JlcGxhY2VtZW50cyhmb3JtYXR0ZWROdW1iZXIpIHtcbiAgICBpZiAodGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uIGluc3RhbmNlb2YgUHJpY2VTcGVjaWZpY2F0aW9uKSB7XG4gICAgICByZXR1cm4gZm9ybWF0dGVkTnVtYmVyXG4gICAgICAgIC5zcGxpdChDVVJSRU5DWV9TWU1CT0xfUExBQ0VIT0xERVIpXG4gICAgICAgIC5qb2luKHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRDdXJyZW5jeVN5bWJvbCgpKTtcbiAgICB9XG5cbiAgICByZXR1cm4gZm9ybWF0dGVkTnVtYmVyO1xuICB9XG5cbiAgc3RhdGljIGJ1aWxkKHNwZWNpZmljYXRpb25zKSB7XG4gICAgY29uc3Qgc3ltYm9sID0gbmV3IE51bWJlclN5bWJvbCguLi5zcGVjaWZpY2F0aW9ucy5zeW1ib2wpO1xuICAgIGxldCBzcGVjaWZpY2F0aW9uO1xuICAgIGlmIChzcGVjaWZpY2F0aW9ucy5jdXJyZW5jeVN5bWJvbCkge1xuICAgICAgc3BlY2lmaWNhdGlvbiA9IG5ldyBQcmljZVNwZWNpZmljYXRpb24oXG4gICAgICAgIHNwZWNpZmljYXRpb25zLnBvc2l0aXZlUGF0dGVybixcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMubmVnYXRpdmVQYXR0ZXJuLFxuICAgICAgICBzeW1ib2wsXG4gICAgICAgIHBhcnNlSW50KHNwZWNpZmljYXRpb25zLm1heEZyYWN0aW9uRGlnaXRzLCAxMCksXG4gICAgICAgIHBhcnNlSW50KHNwZWNpZmljYXRpb25zLm1pbkZyYWN0aW9uRGlnaXRzLCAxMCksXG4gICAgICAgIHNwZWNpZmljYXRpb25zLmdyb3VwaW5nVXNlZCxcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMucHJpbWFyeUdyb3VwU2l6ZSxcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMuc2Vjb25kYXJ5R3JvdXBTaXplLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5jdXJyZW5jeVN5bWJvbCxcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMuY3VycmVuY3lDb2RlLFxuICAgICAgKTtcbiAgICB9IGVsc2Uge1xuICAgICAgc3BlY2lmaWNhdGlvbiA9IG5ldyBOdW1iZXJTcGVjaWZpY2F0aW9uKFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5wb3NpdGl2ZVBhdHRlcm4sXG4gICAgICAgIHNwZWNpZmljYXRpb25zLm5lZ2F0aXZlUGF0dGVybixcbiAgICAgICAgc3ltYm9sLFxuICAgICAgICBwYXJzZUludChzcGVjaWZpY2F0aW9ucy5tYXhGcmFjdGlvbkRpZ2l0cywgMTApLFxuICAgICAgICBwYXJzZUludChzcGVjaWZpY2F0aW9ucy5taW5GcmFjdGlvbkRpZ2l0cywgMTApLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5ncm91cGluZ1VzZWQsXG4gICAgICAgIHNwZWNpZmljYXRpb25zLnByaW1hcnlHcm91cFNpemUsXG4gICAgICAgIHNwZWNpZmljYXRpb25zLnNlY29uZGFyeUdyb3VwU2l6ZSxcbiAgICAgICk7XG4gICAgfVxuXG4gICAgcmV0dXJuIG5ldyBOdW1iZXJGb3JtYXR0ZXIoc3BlY2lmaWNhdGlvbik7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTnVtYmVyRm9ybWF0dGVyO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL2NsZHIvbnVtYmVyLWZvcm1hdHRlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5pbXBvcnQgTnVtYmVyRm9ybWF0dGVyIGZyb20gJy4vbnVtYmVyLWZvcm1hdHRlcic7XG5pbXBvcnQgTnVtYmVyU3ltYm9sIGZyb20gJy4vbnVtYmVyLXN5bWJvbCc7XG5pbXBvcnQgUHJpY2VTcGVjaWZpY2F0aW9uIGZyb20gJy4vc3BlY2lmaWNhdGlvbnMvcHJpY2UnO1xuaW1wb3J0IE51bWJlclNwZWNpZmljYXRpb24gZnJvbSAnLi9zcGVjaWZpY2F0aW9ucy9udW1iZXInO1xuXG5leHBvcnQge1xuICBQcmljZVNwZWNpZmljYXRpb24sXG4gIE51bWJlclNwZWNpZmljYXRpb24sXG4gIE51bWJlckZvcm1hdHRlcixcbiAgTnVtYmVyU3ltYm9sLFxufTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL2luZGV4LmpzIiwiLyoqXG4gKiBsb2Rhc2ggKEN1c3RvbSBCdWlsZCkgPGh0dHBzOi8vbG9kYXNoLmNvbS8+XG4gKiBCdWlsZDogYGxvZGFzaCBtb2R1bGFyaXplIGV4cG9ydHM9XCJucG1cIiAtbyAuL2BcbiAqIENvcHlyaWdodCBqUXVlcnkgRm91bmRhdGlvbiBhbmQgb3RoZXIgY29udHJpYnV0b3JzIDxodHRwczovL2pxdWVyeS5vcmcvPlxuICogUmVsZWFzZWQgdW5kZXIgTUlUIGxpY2Vuc2UgPGh0dHBzOi8vbG9kYXNoLmNvbS9saWNlbnNlPlxuICogQmFzZWQgb24gVW5kZXJzY29yZS5qcyAxLjguMyA8aHR0cDovL3VuZGVyc2NvcmVqcy5vcmcvTElDRU5TRT5cbiAqIENvcHlyaWdodCBKZXJlbXkgQXNoa2VuYXMsIERvY3VtZW50Q2xvdWQgYW5kIEludmVzdGlnYXRpdmUgUmVwb3J0ZXJzICYgRWRpdG9yc1xuICovXG5cbi8qKiBVc2VkIGFzIHJlZmVyZW5jZXMgZm9yIHZhcmlvdXMgYE51bWJlcmAgY29uc3RhbnRzLiAqL1xudmFyIElORklOSVRZID0gMSAvIDA7XG5cbi8qKiBgT2JqZWN0I3RvU3RyaW5nYCByZXN1bHQgcmVmZXJlbmNlcy4gKi9cbnZhciBzeW1ib2xUYWcgPSAnW29iamVjdCBTeW1ib2xdJztcblxuLyoqXG4gKiBVc2VkIHRvIG1hdGNoIGBSZWdFeHBgXG4gKiBbc3ludGF4IGNoYXJhY3RlcnNdKGh0dHA6Ly9lY21hLWludGVybmF0aW9uYWwub3JnL2VjbWEtMjYyLzYuMC8jc2VjLXBhdHRlcm5zKS5cbiAqL1xudmFyIHJlUmVnRXhwQ2hhciA9IC9bXFxcXF4kLiorPygpW1xcXXt9fF0vZyxcbiAgICByZUhhc1JlZ0V4cENoYXIgPSBSZWdFeHAocmVSZWdFeHBDaGFyLnNvdXJjZSk7XG5cbi8qKiBEZXRlY3QgZnJlZSB2YXJpYWJsZSBgZ2xvYmFsYCBmcm9tIE5vZGUuanMuICovXG52YXIgZnJlZUdsb2JhbCA9IHR5cGVvZiBnbG9iYWwgPT0gJ29iamVjdCcgJiYgZ2xvYmFsICYmIGdsb2JhbC5PYmplY3QgPT09IE9iamVjdCAmJiBnbG9iYWw7XG5cbi8qKiBEZXRlY3QgZnJlZSB2YXJpYWJsZSBgc2VsZmAuICovXG52YXIgZnJlZVNlbGYgPSB0eXBlb2Ygc2VsZiA9PSAnb2JqZWN0JyAmJiBzZWxmICYmIHNlbGYuT2JqZWN0ID09PSBPYmplY3QgJiYgc2VsZjtcblxuLyoqIFVzZWQgYXMgYSByZWZlcmVuY2UgdG8gdGhlIGdsb2JhbCBvYmplY3QuICovXG52YXIgcm9vdCA9IGZyZWVHbG9iYWwgfHwgZnJlZVNlbGYgfHwgRnVuY3Rpb24oJ3JldHVybiB0aGlzJykoKTtcblxuLyoqIFVzZWQgZm9yIGJ1aWx0LWluIG1ldGhvZCByZWZlcmVuY2VzLiAqL1xudmFyIG9iamVjdFByb3RvID0gT2JqZWN0LnByb3RvdHlwZTtcblxuLyoqXG4gKiBVc2VkIHRvIHJlc29sdmUgdGhlXG4gKiBbYHRvU3RyaW5nVGFnYF0oaHR0cDovL2VjbWEtaW50ZXJuYXRpb25hbC5vcmcvZWNtYS0yNjIvNi4wLyNzZWMtb2JqZWN0LnByb3RvdHlwZS50b3N0cmluZylcbiAqIG9mIHZhbHVlcy5cbiAqL1xudmFyIG9iamVjdFRvU3RyaW5nID0gb2JqZWN0UHJvdG8udG9TdHJpbmc7XG5cbi8qKiBCdWlsdC1pbiB2YWx1ZSByZWZlcmVuY2VzLiAqL1xudmFyIFN5bWJvbCA9IHJvb3QuU3ltYm9sO1xuXG4vKiogVXNlZCB0byBjb252ZXJ0IHN5bWJvbHMgdG8gcHJpbWl0aXZlcyBhbmQgc3RyaW5ncy4gKi9cbnZhciBzeW1ib2xQcm90byA9IFN5bWJvbCA/IFN5bWJvbC5wcm90b3R5cGUgOiB1bmRlZmluZWQsXG4gICAgc3ltYm9sVG9TdHJpbmcgPSBzeW1ib2xQcm90byA/IHN5bWJvbFByb3RvLnRvU3RyaW5nIDogdW5kZWZpbmVkO1xuXG4vKipcbiAqIFRoZSBiYXNlIGltcGxlbWVudGF0aW9uIG9mIGBfLnRvU3RyaW5nYCB3aGljaCBkb2Vzbid0IGNvbnZlcnQgbnVsbGlzaFxuICogdmFsdWVzIHRvIGVtcHR5IHN0cmluZ3MuXG4gKlxuICogQHByaXZhdGVcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIHByb2Nlc3MuXG4gKiBAcmV0dXJucyB7c3RyaW5nfSBSZXR1cm5zIHRoZSBzdHJpbmcuXG4gKi9cbmZ1bmN0aW9uIGJhc2VUb1N0cmluZyh2YWx1ZSkge1xuICAvLyBFeGl0IGVhcmx5IGZvciBzdHJpbmdzIHRvIGF2b2lkIGEgcGVyZm9ybWFuY2UgaGl0IGluIHNvbWUgZW52aXJvbm1lbnRzLlxuICBpZiAodHlwZW9mIHZhbHVlID09ICdzdHJpbmcnKSB7XG4gICAgcmV0dXJuIHZhbHVlO1xuICB9XG4gIGlmIChpc1N5bWJvbCh2YWx1ZSkpIHtcbiAgICByZXR1cm4gc3ltYm9sVG9TdHJpbmcgPyBzeW1ib2xUb1N0cmluZy5jYWxsKHZhbHVlKSA6ICcnO1xuICB9XG4gIHZhciByZXN1bHQgPSAodmFsdWUgKyAnJyk7XG4gIHJldHVybiAocmVzdWx0ID09ICcwJyAmJiAoMSAvIHZhbHVlKSA9PSAtSU5GSU5JVFkpID8gJy0wJyA6IHJlc3VsdDtcbn1cblxuLyoqXG4gKiBDaGVja3MgaWYgYHZhbHVlYCBpcyBvYmplY3QtbGlrZS4gQSB2YWx1ZSBpcyBvYmplY3QtbGlrZSBpZiBpdCdzIG5vdCBgbnVsbGBcbiAqIGFuZCBoYXMgYSBgdHlwZW9mYCByZXN1bHQgb2YgXCJvYmplY3RcIi5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMC4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gY2hlY2suXG4gKiBAcmV0dXJucyB7Ym9vbGVhbn0gUmV0dXJucyBgdHJ1ZWAgaWYgYHZhbHVlYCBpcyBvYmplY3QtbGlrZSwgZWxzZSBgZmFsc2VgLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmlzT2JqZWN0TGlrZSh7fSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc09iamVjdExpa2UoWzEsIDIsIDNdKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzT2JqZWN0TGlrZShfLm5vb3ApO1xuICogLy8gPT4gZmFsc2VcbiAqXG4gKiBfLmlzT2JqZWN0TGlrZShudWxsKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzT2JqZWN0TGlrZSh2YWx1ZSkge1xuICByZXR1cm4gISF2YWx1ZSAmJiB0eXBlb2YgdmFsdWUgPT0gJ29iamVjdCc7XG59XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgY2xhc3NpZmllZCBhcyBhIGBTeW1ib2xgIHByaW1pdGl2ZSBvciBvYmplY3QuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSA0LjAuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgYSBzeW1ib2wsIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc1N5bWJvbChTeW1ib2wuaXRlcmF0b3IpO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNTeW1ib2woJ2FiYycpO1xuICogLy8gPT4gZmFsc2VcbiAqL1xuZnVuY3Rpb24gaXNTeW1ib2wodmFsdWUpIHtcbiAgcmV0dXJuIHR5cGVvZiB2YWx1ZSA9PSAnc3ltYm9sJyB8fFxuICAgIChpc09iamVjdExpa2UodmFsdWUpICYmIG9iamVjdFRvU3RyaW5nLmNhbGwodmFsdWUpID09IHN5bWJvbFRhZyk7XG59XG5cbi8qKlxuICogQ29udmVydHMgYHZhbHVlYCB0byBhIHN0cmluZy4gQW4gZW1wdHkgc3RyaW5nIGlzIHJldHVybmVkIGZvciBgbnVsbGBcbiAqIGFuZCBgdW5kZWZpbmVkYCB2YWx1ZXMuIFRoZSBzaWduIG9mIGAtMGAgaXMgcHJlc2VydmVkLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgNC4wLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBwcm9jZXNzLlxuICogQHJldHVybnMge3N0cmluZ30gUmV0dXJucyB0aGUgc3RyaW5nLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLnRvU3RyaW5nKG51bGwpO1xuICogLy8gPT4gJydcbiAqXG4gKiBfLnRvU3RyaW5nKC0wKTtcbiAqIC8vID0+ICctMCdcbiAqXG4gKiBfLnRvU3RyaW5nKFsxLCAyLCAzXSk7XG4gKiAvLyA9PiAnMSwyLDMnXG4gKi9cbmZ1bmN0aW9uIHRvU3RyaW5nKHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSA9PSBudWxsID8gJycgOiBiYXNlVG9TdHJpbmcodmFsdWUpO1xufVxuXG4vKipcbiAqIEVzY2FwZXMgdGhlIGBSZWdFeHBgIHNwZWNpYWwgY2hhcmFjdGVycyBcIl5cIiwgXCIkXCIsIFwiXFxcIiwgXCIuXCIsIFwiKlwiLCBcIitcIixcbiAqIFwiP1wiLCBcIihcIiwgXCIpXCIsIFwiW1wiLCBcIl1cIiwgXCJ7XCIsIFwifVwiLCBhbmQgXCJ8XCIgaW4gYHN0cmluZ2AuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSAzLjAuMFxuICogQGNhdGVnb3J5IFN0cmluZ1xuICogQHBhcmFtIHtzdHJpbmd9IFtzdHJpbmc9JyddIFRoZSBzdHJpbmcgdG8gZXNjYXBlLlxuICogQHJldHVybnMge3N0cmluZ30gUmV0dXJucyB0aGUgZXNjYXBlZCBzdHJpbmcuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uZXNjYXBlUmVnRXhwKCdbbG9kYXNoXShodHRwczovL2xvZGFzaC5jb20vKScpO1xuICogLy8gPT4gJ1xcW2xvZGFzaFxcXVxcKGh0dHBzOi8vbG9kYXNoXFwuY29tL1xcKSdcbiAqL1xuZnVuY3Rpb24gZXNjYXBlUmVnRXhwKHN0cmluZykge1xuICBzdHJpbmcgPSB0b1N0cmluZyhzdHJpbmcpO1xuICByZXR1cm4gKHN0cmluZyAmJiByZUhhc1JlZ0V4cENoYXIudGVzdChzdHJpbmcpKVxuICAgID8gc3RyaW5nLnJlcGxhY2UocmVSZWdFeHBDaGFyLCAnXFxcXCQmJylcbiAgICA6IHN0cmluZztcbn1cblxubW9kdWxlLmV4cG9ydHMgPSBlc2NhcGVSZWdFeHA7XG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vbG9kYXNoLmVzY2FwZXJlZ2V4cC9pbmRleC5qc1xuLy8gbW9kdWxlIGlkID0gNDUxXG4vLyBtb2R1bGUgY2h1bmtzID0gMzQiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuaW1wb3J0IExvY2FsaXphdGlvbkV4Y2VwdGlvbiBmcm9tICcuL2V4Y2VwdGlvbi9sb2NhbGl6YXRpb24nO1xuXG5jbGFzcyBOdW1iZXJTeW1ib2wge1xuICAvKipcbiAgICogTnVtYmVyU3ltYm9sTGlzdCBjb25zdHJ1Y3Rvci5cbiAgICpcbiAgICogQHBhcmFtIHN0cmluZyBkZWNpbWFsIERlY2ltYWwgc2VwYXJhdG9yIGNoYXJhY3RlclxuICAgKiBAcGFyYW0gc3RyaW5nIGdyb3VwIERpZ2l0cyBncm91cCBzZXBhcmF0b3IgY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgbGlzdCBMaXN0IGVsZW1lbnRzIHNlcGFyYXRvciBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBwZXJjZW50U2lnbiBQZXJjZW50IHNpZ24gY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgbWludXNTaWduIE1pbnVzIHNpZ24gY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgcGx1c1NpZ24gUGx1cyBzaWduIGNoYXJhY3RlclxuICAgKiBAcGFyYW0gc3RyaW5nIGV4cG9uZW50aWFsIEV4cG9uZW50aWFsIGNoYXJhY3RlclxuICAgKiBAcGFyYW0gc3RyaW5nIHN1cGVyc2NyaXB0aW5nRXhwb25lbnQgU3VwZXJzY3JpcHRpbmcgZXhwb25lbnQgY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgcGVyTWlsbGUgUGVybWlsbGUgc2lnbiBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBpbmZpbml0eSBUaGUgaW5maW5pdHkgc2lnbi4gQ29ycmVzcG9uZHMgdG8gdGhlIElFRUUgaW5maW5pdHkgYml0IHBhdHRlcm4uXG4gICAqIEBwYXJhbSBzdHJpbmcgbmFuIFRoZSBOYU4gKE5vdCBBIE51bWJlcikgc2lnbi4gQ29ycmVzcG9uZHMgdG8gdGhlIElFRUUgTmFOIGJpdCBwYXR0ZXJuLlxuICAgKlxuICAgKiBAdGhyb3dzIExvY2FsaXphdGlvbkV4Y2VwdGlvblxuICAgKi9cbiAgY29uc3RydWN0b3IoXG4gICAgZGVjaW1hbCxcbiAgICBncm91cCxcbiAgICBsaXN0LFxuICAgIHBlcmNlbnRTaWduLFxuICAgIG1pbnVzU2lnbixcbiAgICBwbHVzU2lnbixcbiAgICBleHBvbmVudGlhbCxcbiAgICBzdXBlcnNjcmlwdGluZ0V4cG9uZW50LFxuICAgIHBlck1pbGxlLFxuICAgIGluZmluaXR5LFxuICAgIG5hbixcbiAgKSB7XG4gICAgdGhpcy5kZWNpbWFsID0gZGVjaW1hbDtcbiAgICB0aGlzLmdyb3VwID0gZ3JvdXA7XG4gICAgdGhpcy5saXN0ID0gbGlzdDtcbiAgICB0aGlzLnBlcmNlbnRTaWduID0gcGVyY2VudFNpZ247XG4gICAgdGhpcy5taW51c1NpZ24gPSBtaW51c1NpZ247XG4gICAgdGhpcy5wbHVzU2lnbiA9IHBsdXNTaWduO1xuICAgIHRoaXMuZXhwb25lbnRpYWwgPSBleHBvbmVudGlhbDtcbiAgICB0aGlzLnN1cGVyc2NyaXB0aW5nRXhwb25lbnQgPSBzdXBlcnNjcmlwdGluZ0V4cG9uZW50O1xuICAgIHRoaXMucGVyTWlsbGUgPSBwZXJNaWxsZTtcbiAgICB0aGlzLmluZmluaXR5ID0gaW5maW5pdHk7XG4gICAgdGhpcy5uYW4gPSBuYW47XG5cbiAgICB0aGlzLnZhbGlkYXRlRGF0YSgpO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgZGVjaW1hbCBzZXBhcmF0b3IuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXREZWNpbWFsKCkge1xuICAgIHJldHVybiB0aGlzLmRlY2ltYWw7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBkaWdpdCBncm91cHMgc2VwYXJhdG9yLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0R3JvdXAoKSB7XG4gICAgcmV0dXJuIHRoaXMuZ3JvdXA7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBsaXN0IGVsZW1lbnRzIHNlcGFyYXRvci5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldExpc3QoKSB7XG4gICAgcmV0dXJuIHRoaXMubGlzdDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIHBlcmNlbnQgc2lnbi5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldFBlcmNlbnRTaWduKCkge1xuICAgIHJldHVybiB0aGlzLnBlcmNlbnRTaWduO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgbWludXMgc2lnbi5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldE1pbnVzU2lnbigpIHtcbiAgICByZXR1cm4gdGhpcy5taW51c1NpZ247XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBwbHVzIHNpZ24uXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRQbHVzU2lnbigpIHtcbiAgICByZXR1cm4gdGhpcy5wbHVzU2lnbjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGV4cG9uZW50aWFsIGNoYXJhY3Rlci5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldEV4cG9uZW50aWFsKCkge1xuICAgIHJldHVybiB0aGlzLmV4cG9uZW50aWFsO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgZXhwb25lbnQgY2hhcmFjdGVyLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0U3VwZXJzY3JpcHRpbmdFeHBvbmVudCgpIHtcbiAgICByZXR1cm4gdGhpcy5zdXBlcnNjcmlwdGluZ0V4cG9uZW50O1xuICB9XG5cbiAgLyoqXG4gICAqIEdlcnQgdGhlIHBlciBtaWxsZSBzeW1ib2wgKG9mdGVuIFwi4oCwXCIpLlxuICAgKlxuICAgKiBAc2VlIGh0dHBzOi8vZW4ud2lraXBlZGlhLm9yZy93aWtpL1Blcl9taWxsZVxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0UGVyTWlsbGUoKSB7XG4gICAgcmV0dXJuIHRoaXMucGVyTWlsbGU7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBpbmZpbml0eSBzeW1ib2wgKG9mdGVuIFwi4oieXCIpLlxuICAgKlxuICAgKiBAc2VlIGh0dHBzOi8vZW4ud2lraXBlZGlhLm9yZy93aWtpL0luZmluaXR5X3N5bWJvbFxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0SW5maW5pdHkoKSB7XG4gICAgcmV0dXJuIHRoaXMuaW5maW5pdHk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBOYU4gKG5vdCBhIG51bWJlcikgc2lnbi5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldE5hbigpIHtcbiAgICByZXR1cm4gdGhpcy5uYW47XG4gIH1cblxuICAvKipcbiAgICogU3ltYm9scyBsaXN0IHZhbGlkYXRpb24uXG4gICAqXG4gICAqIEB0aHJvd3MgTG9jYWxpemF0aW9uRXhjZXB0aW9uXG4gICAqL1xuICB2YWxpZGF0ZURhdGEoKSB7XG4gICAgaWYgKCF0aGlzLmRlY2ltYWwgfHwgdHlwZW9mIHRoaXMuZGVjaW1hbCAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgZGVjaW1hbCcpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5ncm91cCB8fCB0eXBlb2YgdGhpcy5ncm91cCAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgZ3JvdXAnKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMubGlzdCB8fCB0eXBlb2YgdGhpcy5saXN0ICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBzeW1ib2wgbGlzdCcpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5wZXJjZW50U2lnbiB8fCB0eXBlb2YgdGhpcy5wZXJjZW50U2lnbiAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgcGVyY2VudFNpZ24nKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMubWludXNTaWduIHx8IHR5cGVvZiB0aGlzLm1pbnVzU2lnbiAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgbWludXNTaWduJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLnBsdXNTaWduIHx8IHR5cGVvZiB0aGlzLnBsdXNTaWduICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBwbHVzU2lnbicpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5leHBvbmVudGlhbCB8fCB0eXBlb2YgdGhpcy5leHBvbmVudGlhbCAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgZXhwb25lbnRpYWwnKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMuc3VwZXJzY3JpcHRpbmdFeHBvbmVudCB8fCB0eXBlb2YgdGhpcy5zdXBlcnNjcmlwdGluZ0V4cG9uZW50ICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBzdXBlcnNjcmlwdGluZ0V4cG9uZW50Jyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLnBlck1pbGxlIHx8IHR5cGVvZiB0aGlzLnBlck1pbGxlICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBwZXJNaWxsZScpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5pbmZpbml0eSB8fCB0eXBlb2YgdGhpcy5pbmZpbml0eSAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgaW5maW5pdHknKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMubmFuIHx8IHR5cGVvZiB0aGlzLm5hbiAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgbmFuJyk7XG4gICAgfVxuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IE51bWJlclN5bWJvbDtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL251bWJlci1zeW1ib2wuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuaW1wb3J0IExvY2FsaXphdGlvbkV4Y2VwdGlvbiBmcm9tICcuLi9leGNlcHRpb24vbG9jYWxpemF0aW9uJztcbmltcG9ydCBOdW1iZXJTeW1ib2wgZnJvbSAnLi4vbnVtYmVyLXN5bWJvbCc7XG5cbmNsYXNzIE51bWJlclNwZWNpZmljYXRpb24ge1xuICAvKipcbiAgICogTnVtYmVyIHNwZWNpZmljYXRpb24gY29uc3RydWN0b3IuXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgcG9zaXRpdmVQYXR0ZXJuIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIGZvciBwb3NpdGl2ZSBhbW91bnRzXG4gICAqIEBwYXJhbSBzdHJpbmcgbmVnYXRpdmVQYXR0ZXJuIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIGZvciBuZWdhdGl2ZSBhbW91bnRzXG4gICAqIEBwYXJhbSBOdW1iZXJTeW1ib2wgc3ltYm9sIE51bWJlciBzeW1ib2xcbiAgICogQHBhcmFtIGludCBtYXhGcmFjdGlvbkRpZ2l0cyBNYXhpbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3JcbiAgICogQHBhcmFtIGludCBtaW5GcmFjdGlvbkRpZ2l0cyBNaW5pbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3JcbiAgICogQHBhcmFtIGJvb2wgZ3JvdXBpbmdVc2VkIElzIGRpZ2l0cyBncm91cGluZyB1c2VkID9cbiAgICogQHBhcmFtIGludCBwcmltYXJ5R3JvdXBTaXplIFNpemUgb2YgcHJpbWFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlclxuICAgKiBAcGFyYW0gaW50IHNlY29uZGFyeUdyb3VwU2l6ZSBTaXplIG9mIHNlY29uZGFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlclxuICAgKlxuICAgKiBAdGhyb3dzIExvY2FsaXphdGlvbkV4Y2VwdGlvblxuICAgKi9cbiAgY29uc3RydWN0b3IoXG4gICAgcG9zaXRpdmVQYXR0ZXJuLFxuICAgIG5lZ2F0aXZlUGF0dGVybixcbiAgICBzeW1ib2wsXG4gICAgbWF4RnJhY3Rpb25EaWdpdHMsXG4gICAgbWluRnJhY3Rpb25EaWdpdHMsXG4gICAgZ3JvdXBpbmdVc2VkLFxuICAgIHByaW1hcnlHcm91cFNpemUsXG4gICAgc2Vjb25kYXJ5R3JvdXBTaXplLFxuICApIHtcbiAgICB0aGlzLnBvc2l0aXZlUGF0dGVybiA9IHBvc2l0aXZlUGF0dGVybjtcbiAgICB0aGlzLm5lZ2F0aXZlUGF0dGVybiA9IG5lZ2F0aXZlUGF0dGVybjtcbiAgICB0aGlzLnN5bWJvbCA9IHN5bWJvbDtcblxuICAgIHRoaXMubWF4RnJhY3Rpb25EaWdpdHMgPSBtYXhGcmFjdGlvbkRpZ2l0cztcbiAgICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmVcbiAgICB0aGlzLm1pbkZyYWN0aW9uRGlnaXRzID0gbWF4RnJhY3Rpb25EaWdpdHMgPCBtaW5GcmFjdGlvbkRpZ2l0cyA/IG1heEZyYWN0aW9uRGlnaXRzIDogbWluRnJhY3Rpb25EaWdpdHM7XG5cbiAgICB0aGlzLmdyb3VwaW5nVXNlZCA9IGdyb3VwaW5nVXNlZDtcbiAgICB0aGlzLnByaW1hcnlHcm91cFNpemUgPSBwcmltYXJ5R3JvdXBTaXplO1xuICAgIHRoaXMuc2Vjb25kYXJ5R3JvdXBTaXplID0gc2Vjb25kYXJ5R3JvdXBTaXplO1xuXG4gICAgaWYgKCF0aGlzLnBvc2l0aXZlUGF0dGVybiB8fCB0eXBlb2YgdGhpcy5wb3NpdGl2ZVBhdHRlcm4gIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHBvc2l0aXZlUGF0dGVybicpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5uZWdhdGl2ZVBhdHRlcm4gfHwgdHlwZW9mIHRoaXMubmVnYXRpdmVQYXR0ZXJuICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBuZWdhdGl2ZVBhdHRlcm4nKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMuc3ltYm9sIHx8ICEodGhpcy5zeW1ib2wgaW5zdGFuY2VvZiBOdW1iZXJTeW1ib2wpKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHN5bWJvbCcpO1xuICAgIH1cblxuICAgIGlmICh0eXBlb2YgdGhpcy5tYXhGcmFjdGlvbkRpZ2l0cyAhPT0gJ251bWJlcicpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgbWF4RnJhY3Rpb25EaWdpdHMnKTtcbiAgICB9XG5cbiAgICBpZiAodHlwZW9mIHRoaXMubWluRnJhY3Rpb25EaWdpdHMgIT09ICdudW1iZXInKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIG1pbkZyYWN0aW9uRGlnaXRzJyk7XG4gICAgfVxuXG4gICAgaWYgKHR5cGVvZiB0aGlzLmdyb3VwaW5nVXNlZCAhPT0gJ2Jvb2xlYW4nKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIGdyb3VwaW5nVXNlZCcpO1xuICAgIH1cblxuICAgIGlmICh0eXBlb2YgdGhpcy5wcmltYXJ5R3JvdXBTaXplICE9PSAnbnVtYmVyJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBwcmltYXJ5R3JvdXBTaXplJyk7XG4gICAgfVxuXG4gICAgaWYgKHR5cGVvZiB0aGlzLnNlY29uZGFyeUdyb3VwU2l6ZSAhPT0gJ251bWJlcicpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgc2Vjb25kYXJ5R3JvdXBTaXplJyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEdldCBzeW1ib2wuXG4gICAqXG4gICAqIEByZXR1cm4gTnVtYmVyU3ltYm9sXG4gICAqL1xuICBnZXRTeW1ib2woKSB7XG4gICAgcmV0dXJuIHRoaXMuc3ltYm9sO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgZm9ybWF0dGluZyBydWxlcyBmb3IgdGhpcyBudW1iZXIgKHdoZW4gcG9zaXRpdmUpLlxuICAgKlxuICAgKiBUaGlzIHBhdHRlcm4gdXNlcyB0aGUgVW5pY29kZSBDTERSIG51bWJlciBwYXR0ZXJuIHN5bnRheFxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0UG9zaXRpdmVQYXR0ZXJuKCkge1xuICAgIHJldHVybiB0aGlzLnBvc2l0aXZlUGF0dGVybjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGZvcm1hdHRpbmcgcnVsZXMgZm9yIHRoaXMgbnVtYmVyICh3aGVuIG5lZ2F0aXZlKS5cbiAgICpcbiAgICogVGhpcyBwYXR0ZXJuIHVzZXMgdGhlIFVuaWNvZGUgQ0xEUiBudW1iZXIgcGF0dGVybiBzeW50YXhcbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldE5lZ2F0aXZlUGF0dGVybigpIHtcbiAgICByZXR1cm4gdGhpcy5uZWdhdGl2ZVBhdHRlcm47XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBtYXhpbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3IgKHJvdW5kaW5nIGlmIG5lZWRlZCkuXG4gICAqXG4gICAqIEByZXR1cm4gaW50XG4gICAqL1xuICBnZXRNYXhGcmFjdGlvbkRpZ2l0cygpIHtcbiAgICByZXR1cm4gdGhpcy5tYXhGcmFjdGlvbkRpZ2l0cztcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIG1pbmltdW0gbnVtYmVyIG9mIGRpZ2l0cyBhZnRlciBkZWNpbWFsIHNlcGFyYXRvciAoZmlsbCB3aXRoIFwiMFwiIGlmIG5lZWRlZCkuXG4gICAqXG4gICAqIEByZXR1cm4gaW50XG4gICAqL1xuICBnZXRNaW5GcmFjdGlvbkRpZ2l0cygpIHtcbiAgICByZXR1cm4gdGhpcy5taW5GcmFjdGlvbkRpZ2l0cztcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIFwiZ3JvdXBpbmdcIiBmbGFnLiBUaGlzIGZsYWcgZGVmaW5lcyBpZiBkaWdpdHNcbiAgICogZ3JvdXBpbmcgc2hvdWxkIGJlIHVzZWQgd2hlbiBmb3JtYXR0aW5nIHRoaXMgbnVtYmVyLlxuICAgKlxuICAgKiBAcmV0dXJuIGJvb2xcbiAgICovXG4gIGlzR3JvdXBpbmdVc2VkKCkge1xuICAgIHJldHVybiB0aGlzLmdyb3VwaW5nVXNlZDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIHNpemUgb2YgcHJpbWFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlci5cbiAgICpcbiAgICogQHJldHVybiBpbnRcbiAgICovXG4gIGdldFByaW1hcnlHcm91cFNpemUoKSB7XG4gICAgcmV0dXJuIHRoaXMucHJpbWFyeUdyb3VwU2l6ZTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIHNpemUgb2Ygc2Vjb25kYXJ5IGRpZ2l0cyBncm91cHMgaW4gdGhlIG51bWJlci5cbiAgICpcbiAgICogQHJldHVybiBpbnRcbiAgICovXG4gIGdldFNlY29uZGFyeUdyb3VwU2l6ZSgpIHtcbiAgICByZXR1cm4gdGhpcy5zZWNvbmRhcnlHcm91cFNpemU7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTnVtYmVyU3BlY2lmaWNhdGlvbjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL3NwZWNpZmljYXRpb25zL251bWJlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5jbGFzcyBMb2NhbGl6YXRpb25FeGNlcHRpb24ge1xuICBjb25zdHJ1Y3RvcihtZXNzYWdlKSB7XG4gICAgdGhpcy5tZXNzYWdlID0gbWVzc2FnZTtcbiAgICB0aGlzLm5hbWUgPSAnTG9jYWxpemF0aW9uRXhjZXB0aW9uJztcbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBMb2NhbGl6YXRpb25FeGNlcHRpb247XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvY2xkci9leGNlcHRpb24vbG9jYWxpemF0aW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cbmltcG9ydCBMb2NhbGl6YXRpb25FeGNlcHRpb24gZnJvbSAnLi4vZXhjZXB0aW9uL2xvY2FsaXphdGlvbic7XG5pbXBvcnQgTnVtYmVyU3BlY2lmaWNhdGlvbiBmcm9tICcuL251bWJlcic7XG5cbi8qKlxuICogQ3VycmVuY3kgZGlzcGxheSBvcHRpb246IHN5bWJvbCBub3RhdGlvbi5cbiAqL1xuY29uc3QgQ1VSUkVOQ1lfRElTUExBWV9TWU1CT0wgPSAnc3ltYm9sJztcblxuXG5jbGFzcyBQcmljZVNwZWNpZmljYXRpb24gZXh0ZW5kcyBOdW1iZXJTcGVjaWZpY2F0aW9uIHtcbiAgLyoqXG4gICAqIFByaWNlIHNwZWNpZmljYXRpb24gY29uc3RydWN0b3IuXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgcG9zaXRpdmVQYXR0ZXJuIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIGZvciBwb3NpdGl2ZSBhbW91bnRzXG4gICAqIEBwYXJhbSBzdHJpbmcgbmVnYXRpdmVQYXR0ZXJuIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIGZvciBuZWdhdGl2ZSBhbW91bnRzXG4gICAqIEBwYXJhbSBOdW1iZXJTeW1ib2wgc3ltYm9sIE51bWJlciBzeW1ib2xcbiAgICogQHBhcmFtIGludCBtYXhGcmFjdGlvbkRpZ2l0cyBNYXhpbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3JcbiAgICogQHBhcmFtIGludCBtaW5GcmFjdGlvbkRpZ2l0cyBNaW5pbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3JcbiAgICogQHBhcmFtIGJvb2wgZ3JvdXBpbmdVc2VkIElzIGRpZ2l0cyBncm91cGluZyB1c2VkID9cbiAgICogQHBhcmFtIGludCBwcmltYXJ5R3JvdXBTaXplIFNpemUgb2YgcHJpbWFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlclxuICAgKiBAcGFyYW0gaW50IHNlY29uZGFyeUdyb3VwU2l6ZSBTaXplIG9mIHNlY29uZGFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlclxuICAgKiBAcGFyYW0gc3RyaW5nIGN1cnJlbmN5U3ltYm9sIEN1cnJlbmN5IHN5bWJvbCBvZiB0aGlzIHByaWNlIChlZy4gOiDigqwpXG4gICAqIEBwYXJhbSBjdXJyZW5jeUNvZGUgQ3VycmVuY3kgY29kZSBvZiB0aGlzIHByaWNlIChlLmcuOiBFVVIpXG4gICAqXG4gICAqIEB0aHJvd3MgTG9jYWxpemF0aW9uRXhjZXB0aW9uXG4gICAqL1xuICBjb25zdHJ1Y3RvcihcbiAgICBwb3NpdGl2ZVBhdHRlcm4sXG4gICAgbmVnYXRpdmVQYXR0ZXJuLFxuICAgIHN5bWJvbCxcbiAgICBtYXhGcmFjdGlvbkRpZ2l0cyxcbiAgICBtaW5GcmFjdGlvbkRpZ2l0cyxcbiAgICBncm91cGluZ1VzZWQsXG4gICAgcHJpbWFyeUdyb3VwU2l6ZSxcbiAgICBzZWNvbmRhcnlHcm91cFNpemUsXG4gICAgY3VycmVuY3lTeW1ib2wsXG4gICAgY3VycmVuY3lDb2RlLFxuICApIHtcbiAgICBzdXBlcihcbiAgICAgIHBvc2l0aXZlUGF0dGVybixcbiAgICAgIG5lZ2F0aXZlUGF0dGVybixcbiAgICAgIHN5bWJvbCxcbiAgICAgIG1heEZyYWN0aW9uRGlnaXRzLFxuICAgICAgbWluRnJhY3Rpb25EaWdpdHMsXG4gICAgICBncm91cGluZ1VzZWQsXG4gICAgICBwcmltYXJ5R3JvdXBTaXplLFxuICAgICAgc2Vjb25kYXJ5R3JvdXBTaXplLFxuICAgICk7XG4gICAgdGhpcy5jdXJyZW5jeVN5bWJvbCA9IGN1cnJlbmN5U3ltYm9sO1xuICAgIHRoaXMuY3VycmVuY3lDb2RlID0gY3VycmVuY3lDb2RlO1xuXG4gICAgaWYgKCF0aGlzLmN1cnJlbmN5U3ltYm9sIHx8IHR5cGVvZiB0aGlzLmN1cnJlbmN5U3ltYm9sICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBjdXJyZW5jeVN5bWJvbCcpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5jdXJyZW5jeUNvZGUgfHwgdHlwZW9mIHRoaXMuY3VycmVuY3lDb2RlICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBjdXJyZW5jeUNvZGUnKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogR2V0IHR5cGUgb2YgZGlzcGxheSBmb3IgY3VycmVuY3kgc3ltYm9sLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgc3RhdGljIGdldEN1cnJlbmN5RGlzcGxheSgpIHtcbiAgICByZXR1cm4gQ1VSUkVOQ1lfRElTUExBWV9TWU1CT0w7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBjdXJyZW5jeSBzeW1ib2xcbiAgICogZS5nLjog4oKsLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0Q3VycmVuY3lTeW1ib2woKSB7XG4gICAgcmV0dXJuIHRoaXMuY3VycmVuY3lTeW1ib2w7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBjdXJyZW5jeSBJU08gY29kZVxuICAgKiBlLmcuOiBFVVIuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRDdXJyZW5jeUNvZGUoKSB7XG4gICAgcmV0dXJuIHRoaXMuY3VycmVuY3lDb2RlO1xuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IFByaWNlU3BlY2lmaWNhdGlvbjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL3NwZWNpZmljYXRpb25zL3ByaWNlLmpzIl0sInNvdXJjZVJvb3QiOiIifQ==