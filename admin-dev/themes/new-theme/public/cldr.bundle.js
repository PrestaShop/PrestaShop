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

/***/ 1:
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

/***/ 227:
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


var _numberSymbol = __webpack_require__(63);

var _numberSymbol2 = _interopRequireDefault(_numberSymbol);

var _price = __webpack_require__(89);

var _price2 = _interopRequireDefault(_price);

var _number = __webpack_require__(64);

var _number2 = _interopRequireDefault(_number);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var escapeRE = __webpack_require__(411);

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

/***/ 296:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.NumberSymbol = exports.NumberFormatter = exports.NumberSpecification = exports.PriceSpecification = undefined;

var _numberFormatter = __webpack_require__(227);

var _numberFormatter2 = _interopRequireDefault(_numberFormatter);

var _numberSymbol = __webpack_require__(63);

var _numberSymbol2 = _interopRequireDefault(_numberSymbol);

var _price = __webpack_require__(89);

var _price2 = _interopRequireDefault(_price);

var _number = __webpack_require__(64);

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

/***/ 411:
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

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

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


var _localization = __webpack_require__(65);

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

/***/ 64:
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


var _localization = __webpack_require__(65);

var _localization2 = _interopRequireDefault(_localization);

var _numberSymbol = __webpack_require__(63);

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

/***/ 65:
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

/***/ 89:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _localization = __webpack_require__(65);

var _localization2 = _interopRequireDefault(_localization);

var _number = __webpack_require__(64);

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQ/MjBkNCoqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzPzM2OTgqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2FwcC9jbGRyL251bWJlci1mb3JtYXR0ZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL2NsZHIvaW5kZXguanMiLCJ3ZWJwYWNrOi8vLy4vfi9sb2Rhc2guZXNjYXBlcmVnZXhwL2luZGV4LmpzIiwid2VicGFjazovLy8uL2pzL2FwcC9jbGRyL251bWJlci1zeW1ib2wuanMiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL2NsZHIvc3BlY2lmaWNhdGlvbnMvbnVtYmVyLmpzIiwid2VicGFjazovLy8uL2pzL2FwcC9jbGRyL2V4Y2VwdGlvbi9sb2NhbGl6YXRpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL2NsZHIvc3BlY2lmaWNhdGlvbnMvcHJpY2UuanMiXSwibmFtZXMiOlsiZXNjYXBlUkUiLCJyZXF1aXJlIiwiQ1VSUkVOQ1lfU1lNQk9MX1BMQUNFSE9MREVSIiwiREVDSU1BTF9TRVBBUkFUT1JfUExBQ0VIT0xERVIiLCJHUk9VUF9TRVBBUkFUT1JfUExBQ0VIT0xERVIiLCJNSU5VU19TSUdOX1BMQUNFSE9MREVSIiwiUEVSQ0VOVF9TWU1CT0xfUExBQ0VIT0xERVIiLCJQTFVTX1NJR05fUExBQ0VIT0xERVIiLCJOdW1iZXJGb3JtYXR0ZXIiLCJzcGVjaWZpY2F0aW9uIiwibnVtYmVyU3BlY2lmaWNhdGlvbiIsIm51bWJlciIsInVuZGVmaW5lZCIsIm51bSIsIk1hdGgiLCJhYnMiLCJ0b0ZpeGVkIiwiZ2V0TWF4RnJhY3Rpb25EaWdpdHMiLCJleHRyYWN0TWFqb3JNaW5vckRpZ2l0cyIsIm1ham9yRGlnaXRzIiwibWlub3JEaWdpdHMiLCJzcGxpdE1ham9yR3JvdXBzIiwiYWRqdXN0TWlub3JEaWdpdHNaZXJvZXMiLCJmb3JtYXR0ZWROdW1iZXIiLCJwYXR0ZXJuIiwiZ2V0Q2xkclBhdHRlcm4iLCJhZGRQbGFjZWhvbGRlcnMiLCJyZXBsYWNlU3ltYm9scyIsInBlcmZvcm1TcGVjaWZpY1JlcGxhY2VtZW50cyIsInJlc3VsdCIsInRvU3RyaW5nIiwic3BsaXQiLCJkaWdpdCIsImlzR3JvdXBpbmdVc2VkIiwicmV2ZXJzZSIsImdyb3VwcyIsInB1c2giLCJzcGxpY2UiLCJnZXRQcmltYXJ5R3JvdXBTaXplIiwibGVuZ3RoIiwiZ2V0U2Vjb25kYXJ5R3JvdXBTaXplIiwibmV3R3JvdXBzIiwiZm9yRWFjaCIsImdyb3VwIiwiam9pbiIsInJlcGxhY2UiLCJnZXRNaW5GcmFjdGlvbkRpZ2l0cyIsInBhZEVuZCIsImlzTmVnYXRpdmUiLCJnZXROZWdhdGl2ZVBhdHRlcm4iLCJnZXRQb3NpdGl2ZVBhdHRlcm4iLCJzeW1ib2xzIiwiZ2V0U3ltYm9sIiwibWFwIiwiZ2V0RGVjaW1hbCIsImdldEdyb3VwIiwiZ2V0TWludXNTaWduIiwiZ2V0UGVyY2VudFNpZ24iLCJnZXRQbHVzU2lnbiIsInN0cnRyIiwic3RyIiwicGFpcnMiLCJzdWJzdHJzIiwiT2JqZWN0Iiwia2V5cyIsIlJlZ0V4cCIsInBhcnQiLCJQcmljZVNwZWNpZmljYXRpb24iLCJnZXRDdXJyZW5jeVN5bWJvbCIsInNwZWNpZmljYXRpb25zIiwic3ltYm9sIiwiTnVtYmVyU3ltYm9sIiwiY3VycmVuY3lTeW1ib2wiLCJwb3NpdGl2ZVBhdHRlcm4iLCJuZWdhdGl2ZVBhdHRlcm4iLCJwYXJzZUludCIsIm1heEZyYWN0aW9uRGlnaXRzIiwibWluRnJhY3Rpb25EaWdpdHMiLCJncm91cGluZ1VzZWQiLCJwcmltYXJ5R3JvdXBTaXplIiwic2Vjb25kYXJ5R3JvdXBTaXplIiwiY3VycmVuY3lDb2RlIiwiTnVtYmVyU3BlY2lmaWNhdGlvbiIsImRlY2ltYWwiLCJsaXN0IiwicGVyY2VudFNpZ24iLCJtaW51c1NpZ24iLCJwbHVzU2lnbiIsImV4cG9uZW50aWFsIiwic3VwZXJzY3JpcHRpbmdFeHBvbmVudCIsInBlck1pbGxlIiwiaW5maW5pdHkiLCJuYW4iLCJ2YWxpZGF0ZURhdGEiLCJMb2NhbGl6YXRpb25FeGNlcHRpb24iLCJtZXNzYWdlIiwibmFtZSIsIkNVUlJFTkNZX0RJU1BMQVlfU1lNQk9MIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7OztBQ2hFQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxDQUFDOztBQUVEO0FBQ0E7QUFDQTtBQUNBLENBQUM7QUFDRDtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0EsNENBQTRDOztBQUU1Qzs7Ozs7Ozs7Ozs7Ozs7Ozs7cWpCQ3BCQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBd0JBOzs7Ozs7QUFJQTs7OztBQUNBOzs7O0FBQ0E7Ozs7Ozs7Ozs7QUFFQSxJQUFNQSxXQUFXLG1CQUFBQyxDQUFRLEdBQVIsQ0FBakI7O0FBRUEsSUFBTUMsOEJBQThCLEdBQXBDO0FBQ0EsSUFBTUMsZ0NBQWdDLEdBQXRDO0FBQ0EsSUFBTUMsOEJBQThCLEdBQXBDO0FBQ0EsSUFBTUMseUJBQXlCLEdBQS9CO0FBQ0EsSUFBTUMsNkJBQTZCLEdBQW5DO0FBQ0EsSUFBTUMsd0JBQXdCLEdBQTlCOztJ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O0FBQ0Q7Ozs7RUExRDhCZixnQjs7a0JBaUZsQmYsa0IiLCJmaWxlIjoiY2xkci5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDI5Nik7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQiLCJ2YXIgZztcclxuXHJcbi8vIFRoaXMgd29ya3MgaW4gbm9uLXN0cmljdCBtb2RlXHJcbmcgPSAoZnVuY3Rpb24oKSB7XHJcblx0cmV0dXJuIHRoaXM7XHJcbn0pKCk7XHJcblxyXG50cnkge1xyXG5cdC8vIFRoaXMgd29ya3MgaWYgZXZhbCBpcyBhbGxvd2VkIChzZWUgQ1NQKVxyXG5cdGcgPSBnIHx8IEZ1bmN0aW9uKFwicmV0dXJuIHRoaXNcIikoKSB8fCAoMSxldmFsKShcInRoaXNcIik7XHJcbn0gY2F0Y2goZSkge1xyXG5cdC8vIFRoaXMgd29ya3MgaWYgdGhlIHdpbmRvdyByZWZlcmVuY2UgaXMgYXZhaWxhYmxlXHJcblx0aWYodHlwZW9mIHdpbmRvdyA9PT0gXCJvYmplY3RcIilcclxuXHRcdGcgPSB3aW5kb3c7XHJcbn1cclxuXHJcbi8vIGcgY2FuIHN0aWxsIGJlIHVuZGVmaW5lZCwgYnV0IG5vdGhpbmcgdG8gZG8gYWJvdXQgaXQuLi5cclxuLy8gV2UgcmV0dXJuIHVuZGVmaW5lZCwgaW5zdGVhZCBvZiBub3RoaW5nIGhlcmUsIHNvIGl0J3NcclxuLy8gZWFzaWVyIHRvIGhhbmRsZSB0aGlzIGNhc2UuIGlmKCFnbG9iYWwpIHsgLi4ufVxyXG5cclxubW9kdWxlLmV4cG9ydHMgPSBnO1xyXG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAod2VicGFjaykvYnVpbGRpbi9nbG9iYWwuanNcbi8vIG1vZHVsZSBpZCA9IDFcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDI1IDI4IDMyIDM2IiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcC5cbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgQWNhZGVtaWMgRnJlZSBMaWNlbnNlIDMuMCAoQUZMLTMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9BRkwtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0FcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9BRkwtMy4wIEFjYWRlbWljIEZyZWUgTGljZW5zZSAzLjAgKEFGTC0zLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG4vKipcbiAqIFRoZXNlIHBsYWNlaG9sZGVycyBhcmUgdXNlZCBpbiBDTERSIG51bWJlciBmb3JtYXR0aW5nIHRlbXBsYXRlcy5cbiAqIFRoZXkgYXJlIG1lYW50IHRvIGJlIHJlcGxhY2VkIGJ5IHRoZSBjb3JyZWN0IGxvY2FsaXplZCBzeW1ib2xzIGluIHRoZSBudW1iZXIgZm9ybWF0dGluZyBwcm9jZXNzLlxuICovXG5pbXBvcnQgTnVtYmVyU3ltYm9sIGZyb20gJy4vbnVtYmVyLXN5bWJvbCc7XG5pbXBvcnQgUHJpY2VTcGVjaWZpY2F0aW9uIGZyb20gJy4vc3BlY2lmaWNhdGlvbnMvcHJpY2UnO1xuaW1wb3J0IE51bWJlclNwZWNpZmljYXRpb24gZnJvbSAnLi9zcGVjaWZpY2F0aW9ucy9udW1iZXInO1xuXG5jb25zdCBlc2NhcGVSRSA9IHJlcXVpcmUoJ2xvZGFzaC5lc2NhcGVyZWdleHAnKTtcblxuY29uc3QgQ1VSUkVOQ1lfU1lNQk9MX1BMQUNFSE9MREVSID0gJ8KkJztcbmNvbnN0IERFQ0lNQUxfU0VQQVJBVE9SX1BMQUNFSE9MREVSID0gJy4nO1xuY29uc3QgR1JPVVBfU0VQQVJBVE9SX1BMQUNFSE9MREVSID0gJywnO1xuY29uc3QgTUlOVVNfU0lHTl9QTEFDRUhPTERFUiA9ICctJztcbmNvbnN0IFBFUkNFTlRfU1lNQk9MX1BMQUNFSE9MREVSID0gJyUnO1xuY29uc3QgUExVU19TSUdOX1BMQUNFSE9MREVSID0gJysnO1xuXG5jbGFzcyBOdW1iZXJGb3JtYXR0ZXIge1xuICAvKipcbiAgICogQHBhcmFtIE51bWJlclNwZWNpZmljYXRpb24gc3BlY2lmaWNhdGlvbiBOdW1iZXIgc3BlY2lmaWNhdGlvbiB0byBiZSB1c2VkXG4gICAqICAgKGNhbiBiZSBhIG51bWJlciBzcGVjLCBhIHByaWNlIHNwZWMsIGEgcGVyY2VudGFnZSBzcGVjKVxuICAgKi9cbiAgY29uc3RydWN0b3Ioc3BlY2lmaWNhdGlvbikge1xuICAgIHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbiA9IHNwZWNpZmljYXRpb247XG4gIH1cblxuICAvKipcbiAgICogRm9ybWF0cyB0aGUgcGFzc2VkIG51bWJlciBhY2NvcmRpbmcgdG8gc3BlY2lmaWNhdGlvbnMuXG4gICAqXG4gICAqIEBwYXJhbSBpbnR8ZmxvYXR8c3RyaW5nIG51bWJlciBUaGUgbnVtYmVyIHRvIGZvcm1hdFxuICAgKiBAcGFyYW0gTnVtYmVyU3BlY2lmaWNhdGlvbiBzcGVjaWZpY2F0aW9uIE51bWJlciBzcGVjaWZpY2F0aW9uIHRvIGJlIHVzZWRcbiAgICogICAoY2FuIGJlIGEgbnVtYmVyIHNwZWMsIGEgcHJpY2Ugc3BlYywgYSBwZXJjZW50YWdlIHNwZWMpXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nIFRoZSBmb3JtYXR0ZWQgbnVtYmVyXG4gICAqICAgICAgICAgICAgICAgIFlvdSBzaG91bGQgdXNlIHRoaXMgdGhpcyB2YWx1ZSBmb3IgZGlzcGxheSwgd2l0aG91dCBtb2RpZnlpbmcgaXRcbiAgICovXG4gIGZvcm1hdChudW1iZXIsIHNwZWNpZmljYXRpb24pIHtcbiAgICBpZiAoc3BlY2lmaWNhdGlvbiAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICB0aGlzLm51bWJlclNwZWNpZmljYXRpb24gPSBzcGVjaWZpY2F0aW9uO1xuICAgIH1cblxuICAgIC8qXG4gICAgICogV2UgbmVlZCB0byB3b3JrIG9uIHRoZSBhYnNvbHV0ZSB2YWx1ZSBmaXJzdC5cbiAgICAgKiBUaGVuIHRoZSBDTERSIHBhdHRlcm4gd2lsbCBhZGQgdGhlIHNpZ24gaWYgcmVsZXZhbnQgKGF0IHRoZSBlbmQpLlxuICAgICAqL1xuICAgIGNvbnN0IG51bSA9IE1hdGguYWJzKG51bWJlcikudG9GaXhlZCh0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0TWF4RnJhY3Rpb25EaWdpdHMoKSk7XG5cbiAgICBsZXQgW21ham9yRGlnaXRzLCBtaW5vckRpZ2l0c10gPSB0aGlzLmV4dHJhY3RNYWpvck1pbm9yRGlnaXRzKG51bSk7XG4gICAgbWFqb3JEaWdpdHMgPSB0aGlzLnNwbGl0TWFqb3JHcm91cHMobWFqb3JEaWdpdHMpO1xuICAgIG1pbm9yRGlnaXRzID0gdGhpcy5hZGp1c3RNaW5vckRpZ2l0c1plcm9lcyhtaW5vckRpZ2l0cyk7XG5cbiAgICAvLyBBc3NlbWJsZSB0aGUgZmluYWwgbnVtYmVyXG4gICAgbGV0IGZvcm1hdHRlZE51bWJlciA9IG1ham9yRGlnaXRzO1xuICAgIGlmIChtaW5vckRpZ2l0cykge1xuICAgICAgZm9ybWF0dGVkTnVtYmVyICs9IERFQ0lNQUxfU0VQQVJBVE9SX1BMQUNFSE9MREVSICsgbWlub3JEaWdpdHM7XG4gICAgfVxuXG4gICAgLy8gR2V0IHRoZSBnb29kIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuLiBTaWduIGlzIGltcG9ydGFudCBoZXJlICFcbiAgICBjb25zdCBwYXR0ZXJuID0gdGhpcy5nZXRDbGRyUGF0dGVybihtYWpvckRpZ2l0cyA8IDApO1xuICAgIGZvcm1hdHRlZE51bWJlciA9IHRoaXMuYWRkUGxhY2Vob2xkZXJzKGZvcm1hdHRlZE51bWJlciwgcGF0dGVybik7XG4gICAgZm9ybWF0dGVkTnVtYmVyID0gdGhpcy5yZXBsYWNlU3ltYm9scyhmb3JtYXR0ZWROdW1iZXIpO1xuXG4gICAgZm9ybWF0dGVkTnVtYmVyID0gdGhpcy5wZXJmb3JtU3BlY2lmaWNSZXBsYWNlbWVudHMoZm9ybWF0dGVkTnVtYmVyKTtcblxuICAgIHJldHVybiBmb3JtYXR0ZWROdW1iZXI7XG4gIH1cblxuICAvKipcbiAgICogR2V0IG51bWJlcidzIG1ham9yIGFuZCBtaW5vciBkaWdpdHMuXG4gICAqXG4gICAqIE1ham9yIGRpZ2l0cyBhcmUgdGhlIFwiaW50ZWdlclwiIHBhcnQgKGJlZm9yZSBkZWNpbWFsIHNlcGFyYXRvciksXG4gICAqIG1pbm9yIGRpZ2l0cyBhcmUgdGhlIGZyYWN0aW9uYWwgcGFydFxuICAgKiBSZXN1bHQgd2lsbCBiZSBhbiBhcnJheSBvZiBleGFjdGx5IDIgaXRlbXM6IFttYWpvckRpZ2l0cywgbWlub3JEaWdpdHNdXG4gICAqXG4gICAqIFVzYWdlIGV4YW1wbGU6XG4gICAqICBsaXN0KG1ham9yRGlnaXRzLCBtaW5vckRpZ2l0cykgPSB0aGlzLmdldE1ham9yTWlub3JEaWdpdHMoZGVjaW1hbE51bWJlcik7XG4gICAqXG4gICAqIEBwYXJhbSBEZWNpbWFsTnVtYmVyIG51bWJlclxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1tdXG4gICAqL1xuICBleHRyYWN0TWFqb3JNaW5vckRpZ2l0cyhudW1iZXIpIHtcbiAgICAvLyBHZXQgdGhlIG51bWJlcidzIG1ham9yIGFuZCBtaW5vciBkaWdpdHMuXG4gICAgY29uc3QgcmVzdWx0ID0gbnVtYmVyLnRvU3RyaW5nKCkuc3BsaXQoJy4nKTtcbiAgICBjb25zdCBtYWpvckRpZ2l0cyA9IHJlc3VsdFswXTtcbiAgICBjb25zdCBtaW5vckRpZ2l0cyA9IChyZXN1bHRbMV0gPT09IHVuZGVmaW5lZCkgPyAnJyA6IHJlc3VsdFsxXTtcbiAgICByZXR1cm4gW21ham9yRGlnaXRzLCBtaW5vckRpZ2l0c107XG4gIH1cblxuICAvKipcbiAgICogU3BsaXRzIG1ham9yIGRpZ2l0cyBpbnRvIGdyb3Vwcy5cbiAgICpcbiAgICogZS5nLjogR2l2ZW4gdGhlIG1ham9yIGRpZ2l0cyBcIjEyMzQ1NjdcIiwgYW5kIG1ham9yIGdyb3VwIHNpemVcbiAgICogIGNvbmZpZ3VyZWQgdG8gMyBkaWdpdHMsIHRoZSByZXN1bHQgd291bGQgYmUgXCIxIDIzNCA1NjdcIlxuICAgKlxuICAgKiBAcGFyYW0gc3RyaW5nIG1ham9yRGlnaXRzIFRoZSBtYWpvciBkaWdpdHMgdG8gYmUgZ3JvdXBlZFxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZyBUaGUgZ3JvdXBlZCBtYWpvciBkaWdpdHNcbiAgICovXG4gIHNwbGl0TWFqb3JHcm91cHMoZGlnaXQpIHtcbiAgICBpZiAoIXRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5pc0dyb3VwaW5nVXNlZCgpKSB7XG4gICAgICByZXR1cm4gZGlnaXQ7XG4gICAgfVxuXG4gICAgLy8gUmV2ZXJzZSB0aGUgbWFqb3IgZGlnaXRzLCBzaW5jZSB0aGV5IGFyZSBncm91cGVkIGZyb20gdGhlIHJpZ2h0LlxuICAgIGNvbnN0IG1ham9yRGlnaXRzID0gZGlnaXQuc3BsaXQoJycpLnJldmVyc2UoKTtcblxuICAgIC8vIEdyb3VwIHRoZSBtYWpvciBkaWdpdHMuXG4gICAgbGV0IGdyb3VwcyA9IFtdO1xuICAgIGdyb3Vwcy5wdXNoKG1ham9yRGlnaXRzLnNwbGljZSgwLCB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0UHJpbWFyeUdyb3VwU2l6ZSgpKSk7XG4gICAgd2hpbGUgKG1ham9yRGlnaXRzLmxlbmd0aCkge1xuICAgICAgZ3JvdXBzLnB1c2gobWFqb3JEaWdpdHMuc3BsaWNlKDAsIHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRTZWNvbmRhcnlHcm91cFNpemUoKSkpO1xuICAgIH1cblxuICAgIC8vIFJldmVyc2UgYmFjayB0aGUgZGlnaXRzIGFuZCB0aGUgZ3JvdXBzXG4gICAgZ3JvdXBzID0gZ3JvdXBzLnJldmVyc2UoKTtcbiAgICBjb25zdCBuZXdHcm91cHMgPSBbXTtcbiAgICBncm91cHMuZm9yRWFjaCgoZ3JvdXApID0+IHtcbiAgICAgIG5ld0dyb3Vwcy5wdXNoKGdyb3VwLnJldmVyc2UoKS5qb2luKCcnKSk7XG4gICAgfSk7XG5cbiAgICAvLyBSZWNvbnN0cnVjdCB0aGUgbWFqb3IgZGlnaXRzLlxuICAgIHJldHVybiBuZXdHcm91cHMuam9pbihHUk9VUF9TRVBBUkFUT1JfUExBQ0VIT0xERVIpO1xuICB9XG5cbiAgLyoqXG4gICAqIEFkZHMgb3IgcmVtb3ZlIHRyYWlsaW5nIHplcm9lcywgZGVwZW5kaW5nIG9uIHNwZWNpZmllZCBtaW4gYW5kIG1heCBmcmFjdGlvbiBkaWdpdHMgbnVtYmVycy5cbiAgICpcbiAgICogQHBhcmFtIHN0cmluZyBtaW5vckRpZ2l0cyBEaWdpdHMgdG8gYmUgYWRqdXN0ZWQgd2l0aCAodHJpbW1lZCBvciBwYWRkZWQpIHplcm9lc1xuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZyBUaGUgYWRqdXN0ZWQgbWlub3IgZGlnaXRzXG4gICAqL1xuICBhZGp1c3RNaW5vckRpZ2l0c1plcm9lcyhtaW5vckRpZ2l0cykge1xuICAgIGxldCBkaWdpdCA9IG1pbm9yRGlnaXRzO1xuICAgIGlmIChkaWdpdC5sZW5ndGggPiB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0TWF4RnJhY3Rpb25EaWdpdHMoKSkge1xuICAgICAgLy8gU3RyaXAgYW55IHRyYWlsaW5nIHplcm9lcy5cbiAgICAgIGRpZ2l0ID0gZGlnaXQucmVwbGFjZSgvMCskLywgJycpO1xuICAgIH1cblxuICAgIGlmIChkaWdpdC5sZW5ndGggPCB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0TWluRnJhY3Rpb25EaWdpdHMoKSkge1xuICAgICAgLy8gUmUtYWRkIG5lZWRlZCB6ZXJvZXNcbiAgICAgIGRpZ2l0ID0gZGlnaXQucGFkRW5kKFxuICAgICAgICB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0TWluRnJhY3Rpb25EaWdpdHMoKSxcbiAgICAgICAgJzAnLFxuICAgICAgKTtcbiAgICB9XG5cbiAgICByZXR1cm4gZGlnaXQ7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBDTERSIGZvcm1hdHRpbmcgcGF0dGVybi5cbiAgICpcbiAgICogQHNlZSBodHRwOi8vY2xkci51bmljb2RlLm9yZy90cmFuc2xhdGlvbi9udW1iZXItcGF0dGVybnNcbiAgICpcbiAgICogQHBhcmFtIGJvb2wgaXNOZWdhdGl2ZSBJZiB0cnVlLCB0aGUgbmVnYXRpdmUgcGF0dGVyblxuICAgKiB3aWxsIGJlIHJldHVybmVkIGluc3RlYWQgb2YgdGhlIHBvc2l0aXZlIG9uZVxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZyBUaGUgQ0xEUiBmb3JtYXR0aW5nIHBhdHRlcm5cbiAgICovXG4gIGdldENsZHJQYXR0ZXJuKGlzTmVnYXRpdmUpIHtcbiAgICBpZiAoaXNOZWdhdGl2ZSkge1xuICAgICAgcmV0dXJuIHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXROZWdhdGl2ZVBhdHRlcm4oKTtcbiAgICB9XG5cbiAgICByZXR1cm4gdGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmdldFBvc2l0aXZlUGF0dGVybigpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlcGxhY2UgcGxhY2Vob2xkZXIgbnVtYmVyIHN5bWJvbHMgd2l0aCByZWxldmFudCBudW1iZXJpbmcgc3lzdGVtJ3Mgc3ltYm9scy5cbiAgICpcbiAgICogQHBhcmFtIHN0cmluZyBudW1iZXJcbiAgICogICAgICAgICAgICAgICAgICAgICAgIFRoZSBudW1iZXIgdG8gcHJvY2Vzc1xuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKiAgICAgICAgICAgICAgICBUaGUgbnVtYmVyIHdpdGggcmVwbGFjZWQgc3ltYm9sc1xuICAgKi9cbiAgcmVwbGFjZVN5bWJvbHMobnVtYmVyKSB7XG4gICAgY29uc3Qgc3ltYm9scyA9IHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRTeW1ib2woKTtcblxuICAgIGNvbnN0IG1hcCA9IHt9O1xuICAgIG1hcFtERUNJTUFMX1NFUEFSQVRPUl9QTEFDRUhPTERFUl0gPSBzeW1ib2xzLmdldERlY2ltYWwoKTtcbiAgICBtYXBbR1JPVVBfU0VQQVJBVE9SX1BMQUNFSE9MREVSXSA9IHN5bWJvbHMuZ2V0R3JvdXAoKTtcbiAgICBtYXBbTUlOVVNfU0lHTl9QTEFDRUhPTERFUl0gPSBzeW1ib2xzLmdldE1pbnVzU2lnbigpO1xuICAgIG1hcFtQRVJDRU5UX1NZTUJPTF9QTEFDRUhPTERFUl0gPSBzeW1ib2xzLmdldFBlcmNlbnRTaWduKCk7XG4gICAgbWFwW1BMVVNfU0lHTl9QTEFDRUhPTERFUl0gPSBzeW1ib2xzLmdldFBsdXNTaWduKCk7XG5cbiAgICByZXR1cm4gdGhpcy5zdHJ0cihudW1iZXIsIG1hcCk7XG4gIH1cblxuICAvKipcbiAgICogc3RydHIoKSBmb3IgSmF2YVNjcmlwdFxuICAgKiBUcmFuc2xhdGUgY2hhcmFjdGVycyBvciByZXBsYWNlIHN1YnN0cmluZ3NcbiAgICpcbiAgICogQHBhcmFtIHN0clxuICAgKiAgU3RyaW5nIHRvIHBhcnNlXG4gICAqIEBwYXJhbSBwYWlyc1xuICAgKiAgSGFzaCBvZiAoJ2Zyb20nID0+ICd0bycsIC4uLikuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBzdHJ0cihzdHIsIHBhaXJzKSB7XG4gICAgY29uc3Qgc3Vic3RycyA9IE9iamVjdC5rZXlzKHBhaXJzKS5tYXAoZXNjYXBlUkUpO1xuICAgIHJldHVybiBzdHIuc3BsaXQoUmVnRXhwKGAoJHtzdWJzdHJzLmpvaW4oJ3wnKX0pYCkpXG4gICAgICAgICAgICAgIC5tYXAocGFydCA9PiBwYWlyc1twYXJ0XSB8fCBwYXJ0KVxuICAgICAgICAgICAgICAuam9pbignJyk7XG4gIH1cblxuXG4gIC8qKlxuICAgKiBBZGQgbWlzc2luZyBwbGFjZWhvbGRlcnMgdG8gdGhlIG51bWJlciB1c2luZyB0aGUgcGFzc2VkIENMRFIgcGF0dGVybi5cbiAgICpcbiAgICogTWlzc2luZyBwbGFjZWhvbGRlcnMgY2FuIGJlIHRoZSBwZXJjZW50IHNpZ24sIGN1cnJlbmN5IHN5bWJvbCwgZXRjLlxuICAgKlxuICAgKiBlLmcuIHdpdGggYSBjdXJyZW5jeSBDTERSIHBhdHRlcm46XG4gICAqICAtIFBhc3NlZCBudW1iZXIgKHBhcnRpYWxseSBmb3JtYXR0ZWQpOiAxLDIzNC41NjdcbiAgICogIC0gUmV0dXJuZWQgbnVtYmVyOiAxLDIzNC41NjcgwqRcbiAgICogIChcIsKkXCIgc3ltYm9sIGlzIHRoZSBjdXJyZW5jeSBzeW1ib2wgcGxhY2Vob2xkZXIpXG4gICAqXG4gICAqIEBzZWUgaHR0cDovL2NsZHIudW5pY29kZS5vcmcvdHJhbnNsYXRpb24vbnVtYmVyLXBhdHRlcm5zXG4gICAqXG4gICAqIEBwYXJhbSBmb3JtYXR0ZWROdW1iZXJcbiAgICogIE51bWJlciB0byBwcm9jZXNzXG4gICAqIEBwYXJhbSBwYXR0ZXJuXG4gICAqICBDTERSIGZvcm1hdHRpbmcgcGF0dGVybiB0byB1c2VcbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGFkZFBsYWNlaG9sZGVycyhmb3JtYXR0ZWROdW1iZXIsIHBhdHRlcm4pIHtcbiAgICAvKlxuICAgICAqIFJlZ2V4IGdyb3VwcyBleHBsYW5hdGlvbjpcbiAgICAgKiAjICAgICAgICAgIDogbGl0ZXJhbCBcIiNcIiBjaGFyYWN0ZXIuIE9uY2UuXG4gICAgICogKCwjKykqICAgICA6IGFueSBvdGhlciBcIiNcIiBjaGFyYWN0ZXJzIGdyb3VwLCBzZXBhcmF0ZWQgYnkgXCIsXCIuIFplcm8gdG8gaW5maW5pdHkgdGltZXMuXG4gICAgICogMCAgICAgICAgICA6IGxpdGVyYWwgXCIwXCIgY2hhcmFjdGVyLiBPbmNlLlxuICAgICAqIChcXC5bMCNdKykqIDogYW55IGNvbWJpbmF0aW9uIG9mIFwiMFwiIGFuZCBcIiNcIiBjaGFyYWN0ZXJzIGdyb3Vwcywgc2VwYXJhdGVkIGJ5ICcuJy5cbiAgICAgKiAgICAgICAgICAgICAgWmVybyB0byBpbmZpbml0eSB0aW1lcy5cbiAgICAgKi9cbiAgICByZXR1cm4gcGF0dGVybi5yZXBsYWNlKC8jPygsIyspKjAoXFwuWzAjXSspKi8sIGZvcm1hdHRlZE51bWJlcik7XG4gIH1cblxuICAvKipcbiAgICogUGVyZm9ybSBzb21lIG1vcmUgc3BlY2lmaWMgcmVwbGFjZW1lbnRzLlxuICAgKlxuICAgKiBTcGVjaWZpYyByZXBsYWNlbWVudHMgYXJlIG5lZWRlZCB3aGVuIG51bWJlciBzcGVjaWZpY2F0aW9uIGlzIGV4dGVuZGVkLlxuICAgKiBGb3IgaW5zdGFuY2UsIHByaWNlcyBoYXZlIGFuIGV4dGVuZGVkIG51bWJlciBzcGVjaWZpY2F0aW9uIGluIG9yZGVyIHRvXG4gICAqIGFkZCBjdXJyZW5jeSBzeW1ib2wgdG8gdGhlIGZvcm1hdHRlZCBudW1iZXIuXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgZm9ybWF0dGVkTnVtYmVyXG4gICAqXG4gICAqIEByZXR1cm4gbWl4ZWRcbiAgICovXG4gIHBlcmZvcm1TcGVjaWZpY1JlcGxhY2VtZW50cyhmb3JtYXR0ZWROdW1iZXIpIHtcbiAgICBpZiAodGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uIGluc3RhbmNlb2YgUHJpY2VTcGVjaWZpY2F0aW9uKSB7XG4gICAgICByZXR1cm4gZm9ybWF0dGVkTnVtYmVyXG4gICAgICAgIC5zcGxpdChDVVJSRU5DWV9TWU1CT0xfUExBQ0VIT0xERVIpXG4gICAgICAgIC5qb2luKHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRDdXJyZW5jeVN5bWJvbCgpKTtcbiAgICB9XG5cbiAgICByZXR1cm4gZm9ybWF0dGVkTnVtYmVyO1xuICB9XG5cbiAgc3RhdGljIGJ1aWxkKHNwZWNpZmljYXRpb25zKSB7XG4gICAgY29uc3Qgc3ltYm9sID0gbmV3IE51bWJlclN5bWJvbCguLi5zcGVjaWZpY2F0aW9ucy5zeW1ib2wpO1xuICAgIGxldCBzcGVjaWZpY2F0aW9uO1xuICAgIGlmIChzcGVjaWZpY2F0aW9ucy5jdXJyZW5jeVN5bWJvbCkge1xuICAgICAgc3BlY2lmaWNhdGlvbiA9IG5ldyBQcmljZVNwZWNpZmljYXRpb24oXG4gICAgICAgIHNwZWNpZmljYXRpb25zLnBvc2l0aXZlUGF0dGVybixcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMubmVnYXRpdmVQYXR0ZXJuLFxuICAgICAgICBzeW1ib2wsXG4gICAgICAgIHBhcnNlSW50KHNwZWNpZmljYXRpb25zLm1heEZyYWN0aW9uRGlnaXRzLCAxMCksXG4gICAgICAgIHBhcnNlSW50KHNwZWNpZmljYXRpb25zLm1pbkZyYWN0aW9uRGlnaXRzLCAxMCksXG4gICAgICAgIHNwZWNpZmljYXRpb25zLmdyb3VwaW5nVXNlZCxcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMucHJpbWFyeUdyb3VwU2l6ZSxcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMuc2Vjb25kYXJ5R3JvdXBTaXplLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5jdXJyZW5jeVN5bWJvbCxcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMuY3VycmVuY3lDb2RlLFxuICAgICAgKTtcbiAgICB9IGVsc2Uge1xuICAgICAgc3BlY2lmaWNhdGlvbiA9IG5ldyBOdW1iZXJTcGVjaWZpY2F0aW9uKFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5wb3NpdGl2ZVBhdHRlcm4sXG4gICAgICAgIHNwZWNpZmljYXRpb25zLm5lZ2F0aXZlUGF0dGVybixcbiAgICAgICAgc3ltYm9sLFxuICAgICAgICBwYXJzZUludChzcGVjaWZpY2F0aW9ucy5tYXhGcmFjdGlvbkRpZ2l0cywgMTApLFxuICAgICAgICBwYXJzZUludChzcGVjaWZpY2F0aW9ucy5taW5GcmFjdGlvbkRpZ2l0cywgMTApLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5ncm91cGluZ1VzZWQsXG4gICAgICAgIHNwZWNpZmljYXRpb25zLnByaW1hcnlHcm91cFNpemUsXG4gICAgICAgIHNwZWNpZmljYXRpb25zLnNlY29uZGFyeUdyb3VwU2l6ZSxcbiAgICAgICk7XG4gICAgfVxuXG4gICAgcmV0dXJuIG5ldyBOdW1iZXJGb3JtYXR0ZXIoc3BlY2lmaWNhdGlvbik7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTnVtYmVyRm9ybWF0dGVyO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL2NsZHIvbnVtYmVyLWZvcm1hdHRlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AuXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIEFjYWRlbWljIEZyZWUgTGljZW5zZSAzLjAgKEFGTC0zLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cDovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMCBBY2FkZW1pYyBGcmVlIExpY2Vuc2UgMy4wIChBRkwtMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuaW1wb3J0IE51bWJlckZvcm1hdHRlciBmcm9tICcuL251bWJlci1mb3JtYXR0ZXInO1xuaW1wb3J0IE51bWJlclN5bWJvbCBmcm9tICcuL251bWJlci1zeW1ib2wnO1xuaW1wb3J0IFByaWNlU3BlY2lmaWNhdGlvbiBmcm9tICcuL3NwZWNpZmljYXRpb25zL3ByaWNlJztcbmltcG9ydCBOdW1iZXJTcGVjaWZpY2F0aW9uIGZyb20gJy4vc3BlY2lmaWNhdGlvbnMvbnVtYmVyJztcblxuZXhwb3J0IHtcbiAgUHJpY2VTcGVjaWZpY2F0aW9uLFxuICBOdW1iZXJTcGVjaWZpY2F0aW9uLFxuICBOdW1iZXJGb3JtYXR0ZXIsXG4gIE51bWJlclN5bWJvbCxcbn07XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvY2xkci9pbmRleC5qcyIsIi8qKlxuICogbG9kYXNoIChDdXN0b20gQnVpbGQpIDxodHRwczovL2xvZGFzaC5jb20vPlxuICogQnVpbGQ6IGBsb2Rhc2ggbW9kdWxhcml6ZSBleHBvcnRzPVwibnBtXCIgLW8gLi9gXG4gKiBDb3B5cmlnaHQgalF1ZXJ5IEZvdW5kYXRpb24gYW5kIG90aGVyIGNvbnRyaWJ1dG9ycyA8aHR0cHM6Ly9qcXVlcnkub3JnLz5cbiAqIFJlbGVhc2VkIHVuZGVyIE1JVCBsaWNlbnNlIDxodHRwczovL2xvZGFzaC5jb20vbGljZW5zZT5cbiAqIEJhc2VkIG9uIFVuZGVyc2NvcmUuanMgMS44LjMgPGh0dHA6Ly91bmRlcnNjb3JlanMub3JnL0xJQ0VOU0U+XG4gKiBDb3B5cmlnaHQgSmVyZW15IEFzaGtlbmFzLCBEb2N1bWVudENsb3VkIGFuZCBJbnZlc3RpZ2F0aXZlIFJlcG9ydGVycyAmIEVkaXRvcnNcbiAqL1xuXG4vKiogVXNlZCBhcyByZWZlcmVuY2VzIGZvciB2YXJpb3VzIGBOdW1iZXJgIGNvbnN0YW50cy4gKi9cbnZhciBJTkZJTklUWSA9IDEgLyAwO1xuXG4vKiogYE9iamVjdCN0b1N0cmluZ2AgcmVzdWx0IHJlZmVyZW5jZXMuICovXG52YXIgc3ltYm9sVGFnID0gJ1tvYmplY3QgU3ltYm9sXSc7XG5cbi8qKlxuICogVXNlZCB0byBtYXRjaCBgUmVnRXhwYFxuICogW3N5bnRheCBjaGFyYWN0ZXJzXShodHRwOi8vZWNtYS1pbnRlcm5hdGlvbmFsLm9yZy9lY21hLTI2Mi82LjAvI3NlYy1wYXR0ZXJucykuXG4gKi9cbnZhciByZVJlZ0V4cENoYXIgPSAvW1xcXFxeJC4qKz8oKVtcXF17fXxdL2csXG4gICAgcmVIYXNSZWdFeHBDaGFyID0gUmVnRXhwKHJlUmVnRXhwQ2hhci5zb3VyY2UpO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYGdsb2JhbGAgZnJvbSBOb2RlLmpzLiAqL1xudmFyIGZyZWVHbG9iYWwgPSB0eXBlb2YgZ2xvYmFsID09ICdvYmplY3QnICYmIGdsb2JhbCAmJiBnbG9iYWwuT2JqZWN0ID09PSBPYmplY3QgJiYgZ2xvYmFsO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYHNlbGZgLiAqL1xudmFyIGZyZWVTZWxmID0gdHlwZW9mIHNlbGYgPT0gJ29iamVjdCcgJiYgc2VsZiAmJiBzZWxmLk9iamVjdCA9PT0gT2JqZWN0ICYmIHNlbGY7XG5cbi8qKiBVc2VkIGFzIGEgcmVmZXJlbmNlIHRvIHRoZSBnbG9iYWwgb2JqZWN0LiAqL1xudmFyIHJvb3QgPSBmcmVlR2xvYmFsIHx8IGZyZWVTZWxmIHx8IEZ1bmN0aW9uKCdyZXR1cm4gdGhpcycpKCk7XG5cbi8qKiBVc2VkIGZvciBidWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcy4gKi9cbnZhciBvYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbi8qKlxuICogVXNlZCB0byByZXNvbHZlIHRoZVxuICogW2B0b1N0cmluZ1RhZ2BdKGh0dHA6Ly9lY21hLWludGVybmF0aW9uYWwub3JnL2VjbWEtMjYyLzYuMC8jc2VjLW9iamVjdC5wcm90b3R5cGUudG9zdHJpbmcpXG4gKiBvZiB2YWx1ZXMuXG4gKi9cbnZhciBvYmplY3RUb1N0cmluZyA9IG9iamVjdFByb3RvLnRvU3RyaW5nO1xuXG4vKiogQnVpbHQtaW4gdmFsdWUgcmVmZXJlbmNlcy4gKi9cbnZhciBTeW1ib2wgPSByb290LlN5bWJvbDtcblxuLyoqIFVzZWQgdG8gY29udmVydCBzeW1ib2xzIHRvIHByaW1pdGl2ZXMgYW5kIHN0cmluZ3MuICovXG52YXIgc3ltYm9sUHJvdG8gPSBTeW1ib2wgPyBTeW1ib2wucHJvdG90eXBlIDogdW5kZWZpbmVkLFxuICAgIHN5bWJvbFRvU3RyaW5nID0gc3ltYm9sUHJvdG8gPyBzeW1ib2xQcm90by50b1N0cmluZyA6IHVuZGVmaW5lZDtcblxuLyoqXG4gKiBUaGUgYmFzZSBpbXBsZW1lbnRhdGlvbiBvZiBgXy50b1N0cmluZ2Agd2hpY2ggZG9lc24ndCBjb252ZXJ0IG51bGxpc2hcbiAqIHZhbHVlcyB0byBlbXB0eSBzdHJpbmdzLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBwcm9jZXNzLlxuICogQHJldHVybnMge3N0cmluZ30gUmV0dXJucyB0aGUgc3RyaW5nLlxuICovXG5mdW5jdGlvbiBiYXNlVG9TdHJpbmcodmFsdWUpIHtcbiAgLy8gRXhpdCBlYXJseSBmb3Igc3RyaW5ncyB0byBhdm9pZCBhIHBlcmZvcm1hbmNlIGhpdCBpbiBzb21lIGVudmlyb25tZW50cy5cbiAgaWYgKHR5cGVvZiB2YWx1ZSA9PSAnc3RyaW5nJykge1xuICAgIHJldHVybiB2YWx1ZTtcbiAgfVxuICBpZiAoaXNTeW1ib2wodmFsdWUpKSB7XG4gICAgcmV0dXJuIHN5bWJvbFRvU3RyaW5nID8gc3ltYm9sVG9TdHJpbmcuY2FsbCh2YWx1ZSkgOiAnJztcbiAgfVxuICB2YXIgcmVzdWx0ID0gKHZhbHVlICsgJycpO1xuICByZXR1cm4gKHJlc3VsdCA9PSAnMCcgJiYgKDEgLyB2YWx1ZSkgPT0gLUlORklOSVRZKSA/ICctMCcgOiByZXN1bHQ7XG59XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgb2JqZWN0LWxpa2UuIEEgdmFsdWUgaXMgb2JqZWN0LWxpa2UgaWYgaXQncyBub3QgYG51bGxgXG4gKiBhbmQgaGFzIGEgYHR5cGVvZmAgcmVzdWx0IG9mIFwib2JqZWN0XCIuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSA0LjAuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgb2JqZWN0LWxpa2UsIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc09iamVjdExpa2Uoe30pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNPYmplY3RMaWtlKFsxLCAyLCAzXSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc09iamVjdExpa2UoXy5ub29wKTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc09iamVjdExpa2UobnVsbCk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG5mdW5jdGlvbiBpc09iamVjdExpa2UodmFsdWUpIHtcbiAgcmV0dXJuICEhdmFsdWUgJiYgdHlwZW9mIHZhbHVlID09ICdvYmplY3QnO1xufVxuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGNsYXNzaWZpZWQgYXMgYSBgU3ltYm9sYCBwcmltaXRpdmUgb3Igb2JqZWN0LlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgNC4wLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGEgc3ltYm9sLCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNTeW1ib2woU3ltYm9sLml0ZXJhdG9yKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzU3ltYm9sKCdhYmMnKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzU3ltYm9sKHZhbHVlKSB7XG4gIHJldHVybiB0eXBlb2YgdmFsdWUgPT0gJ3N5bWJvbCcgfHxcbiAgICAoaXNPYmplY3RMaWtlKHZhbHVlKSAmJiBvYmplY3RUb1N0cmluZy5jYWxsKHZhbHVlKSA9PSBzeW1ib2xUYWcpO1xufVxuXG4vKipcbiAqIENvbnZlcnRzIGB2YWx1ZWAgdG8gYSBzdHJpbmcuIEFuIGVtcHR5IHN0cmluZyBpcyByZXR1cm5lZCBmb3IgYG51bGxgXG4gKiBhbmQgYHVuZGVmaW5lZGAgdmFsdWVzLiBUaGUgc2lnbiBvZiBgLTBgIGlzIHByZXNlcnZlZC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMC4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gcHJvY2Vzcy5cbiAqIEByZXR1cm5zIHtzdHJpbmd9IFJldHVybnMgdGhlIHN0cmluZy5cbiAqIEBleGFtcGxlXG4gKlxuICogXy50b1N0cmluZyhudWxsKTtcbiAqIC8vID0+ICcnXG4gKlxuICogXy50b1N0cmluZygtMCk7XG4gKiAvLyA9PiAnLTAnXG4gKlxuICogXy50b1N0cmluZyhbMSwgMiwgM10pO1xuICogLy8gPT4gJzEsMiwzJ1xuICovXG5mdW5jdGlvbiB0b1N0cmluZyh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgPT0gbnVsbCA/ICcnIDogYmFzZVRvU3RyaW5nKHZhbHVlKTtcbn1cblxuLyoqXG4gKiBFc2NhcGVzIHRoZSBgUmVnRXhwYCBzcGVjaWFsIGNoYXJhY3RlcnMgXCJeXCIsIFwiJFwiLCBcIlxcXCIsIFwiLlwiLCBcIipcIiwgXCIrXCIsXG4gKiBcIj9cIiwgXCIoXCIsIFwiKVwiLCBcIltcIiwgXCJdXCIsIFwie1wiLCBcIn1cIiwgYW5kIFwifFwiIGluIGBzdHJpbmdgLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMy4wLjBcbiAqIEBjYXRlZ29yeSBTdHJpbmdcbiAqIEBwYXJhbSB7c3RyaW5nfSBbc3RyaW5nPScnXSBUaGUgc3RyaW5nIHRvIGVzY2FwZS5cbiAqIEByZXR1cm5zIHtzdHJpbmd9IFJldHVybnMgdGhlIGVzY2FwZWQgc3RyaW5nLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmVzY2FwZVJlZ0V4cCgnW2xvZGFzaF0oaHR0cHM6Ly9sb2Rhc2guY29tLyknKTtcbiAqIC8vID0+ICdcXFtsb2Rhc2hcXF1cXChodHRwczovL2xvZGFzaFxcLmNvbS9cXCknXG4gKi9cbmZ1bmN0aW9uIGVzY2FwZVJlZ0V4cChzdHJpbmcpIHtcbiAgc3RyaW5nID0gdG9TdHJpbmcoc3RyaW5nKTtcbiAgcmV0dXJuIChzdHJpbmcgJiYgcmVIYXNSZWdFeHBDaGFyLnRlc3Qoc3RyaW5nKSlcbiAgICA/IHN0cmluZy5yZXBsYWNlKHJlUmVnRXhwQ2hhciwgJ1xcXFwkJicpXG4gICAgOiBzdHJpbmc7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gZXNjYXBlUmVnRXhwO1xuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2xvZGFzaC5lc2NhcGVyZWdleHAvaW5kZXguanNcbi8vIG1vZHVsZSBpZCA9IDQxMVxuLy8gbW9kdWxlIGNodW5rcyA9IDI1IiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcC5cbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgQWNhZGVtaWMgRnJlZSBMaWNlbnNlIDMuMCAoQUZMLTMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9BRkwtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0FcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9BRkwtMy4wIEFjYWRlbWljIEZyZWUgTGljZW5zZSAzLjAgKEFGTC0zLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5pbXBvcnQgTG9jYWxpemF0aW9uRXhjZXB0aW9uIGZyb20gJy4vZXhjZXB0aW9uL2xvY2FsaXphdGlvbic7XG5cbmNsYXNzIE51bWJlclN5bWJvbCB7XG4gIC8qKlxuICAgKiBOdW1iZXJTeW1ib2xMaXN0IGNvbnN0cnVjdG9yLlxuICAgKlxuICAgKiBAcGFyYW0gc3RyaW5nIGRlY2ltYWwgRGVjaW1hbCBzZXBhcmF0b3IgY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgZ3JvdXAgRGlnaXRzIGdyb3VwIHNlcGFyYXRvciBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBsaXN0IExpc3QgZWxlbWVudHMgc2VwYXJhdG9yIGNoYXJhY3RlclxuICAgKiBAcGFyYW0gc3RyaW5nIHBlcmNlbnRTaWduIFBlcmNlbnQgc2lnbiBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBtaW51c1NpZ24gTWludXMgc2lnbiBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBwbHVzU2lnbiBQbHVzIHNpZ24gY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgZXhwb25lbnRpYWwgRXhwb25lbnRpYWwgY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgc3VwZXJzY3JpcHRpbmdFeHBvbmVudCBTdXBlcnNjcmlwdGluZyBleHBvbmVudCBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBwZXJNaWxsZSBQZXJtaWxsZSBzaWduIGNoYXJhY3RlclxuICAgKiBAcGFyYW0gc3RyaW5nIGluZmluaXR5IFRoZSBpbmZpbml0eSBzaWduLiBDb3JyZXNwb25kcyB0byB0aGUgSUVFRSBpbmZpbml0eSBiaXQgcGF0dGVybi5cbiAgICogQHBhcmFtIHN0cmluZyBuYW4gVGhlIE5hTiAoTm90IEEgTnVtYmVyKSBzaWduLiBDb3JyZXNwb25kcyB0byB0aGUgSUVFRSBOYU4gYml0IHBhdHRlcm4uXG4gICAqXG4gICAqIEB0aHJvd3MgTG9jYWxpemF0aW9uRXhjZXB0aW9uXG4gICAqL1xuICBjb25zdHJ1Y3RvcihcbiAgICBkZWNpbWFsLFxuICAgIGdyb3VwLFxuICAgIGxpc3QsXG4gICAgcGVyY2VudFNpZ24sXG4gICAgbWludXNTaWduLFxuICAgIHBsdXNTaWduLFxuICAgIGV4cG9uZW50aWFsLFxuICAgIHN1cGVyc2NyaXB0aW5nRXhwb25lbnQsXG4gICAgcGVyTWlsbGUsXG4gICAgaW5maW5pdHksXG4gICAgbmFuLFxuICApIHtcbiAgICB0aGlzLmRlY2ltYWwgPSBkZWNpbWFsO1xuICAgIHRoaXMuZ3JvdXAgPSBncm91cDtcbiAgICB0aGlzLmxpc3QgPSBsaXN0O1xuICAgIHRoaXMucGVyY2VudFNpZ24gPSBwZXJjZW50U2lnbjtcbiAgICB0aGlzLm1pbnVzU2lnbiA9IG1pbnVzU2lnbjtcbiAgICB0aGlzLnBsdXNTaWduID0gcGx1c1NpZ247XG4gICAgdGhpcy5leHBvbmVudGlhbCA9IGV4cG9uZW50aWFsO1xuICAgIHRoaXMuc3VwZXJzY3JpcHRpbmdFeHBvbmVudCA9IHN1cGVyc2NyaXB0aW5nRXhwb25lbnQ7XG4gICAgdGhpcy5wZXJNaWxsZSA9IHBlck1pbGxlO1xuICAgIHRoaXMuaW5maW5pdHkgPSBpbmZpbml0eTtcbiAgICB0aGlzLm5hbiA9IG5hbjtcblxuICAgIHRoaXMudmFsaWRhdGVEYXRhKCk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBkZWNpbWFsIHNlcGFyYXRvci5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldERlY2ltYWwoKSB7XG4gICAgcmV0dXJuIHRoaXMuZGVjaW1hbDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGRpZ2l0IGdyb3VwcyBzZXBhcmF0b3IuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRHcm91cCgpIHtcbiAgICByZXR1cm4gdGhpcy5ncm91cDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGxpc3QgZWxlbWVudHMgc2VwYXJhdG9yLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0TGlzdCgpIHtcbiAgICByZXR1cm4gdGhpcy5saXN0O1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgcGVyY2VudCBzaWduLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0UGVyY2VudFNpZ24oKSB7XG4gICAgcmV0dXJuIHRoaXMucGVyY2VudFNpZ247XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBtaW51cyBzaWduLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0TWludXNTaWduKCkge1xuICAgIHJldHVybiB0aGlzLm1pbnVzU2lnbjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIHBsdXMgc2lnbi5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldFBsdXNTaWduKCkge1xuICAgIHJldHVybiB0aGlzLnBsdXNTaWduO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgZXhwb25lbnRpYWwgY2hhcmFjdGVyLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0RXhwb25lbnRpYWwoKSB7XG4gICAgcmV0dXJuIHRoaXMuZXhwb25lbnRpYWw7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBleHBvbmVudCBjaGFyYWN0ZXIuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRTdXBlcnNjcmlwdGluZ0V4cG9uZW50KCkge1xuICAgIHJldHVybiB0aGlzLnN1cGVyc2NyaXB0aW5nRXhwb25lbnQ7XG4gIH1cblxuICAvKipcbiAgICogR2VydCB0aGUgcGVyIG1pbGxlIHN5bWJvbCAob2Z0ZW4gXCLigLBcIikuXG4gICAqXG4gICAqIEBzZWUgaHR0cHM6Ly9lbi53aWtpcGVkaWEub3JnL3dpa2kvUGVyX21pbGxlXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRQZXJNaWxsZSgpIHtcbiAgICByZXR1cm4gdGhpcy5wZXJNaWxsZTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGluZmluaXR5IHN5bWJvbCAob2Z0ZW4gXCLiiJ5cIikuXG4gICAqXG4gICAqIEBzZWUgaHR0cHM6Ly9lbi53aWtpcGVkaWEub3JnL3dpa2kvSW5maW5pdHlfc3ltYm9sXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRJbmZpbml0eSgpIHtcbiAgICByZXR1cm4gdGhpcy5pbmZpbml0eTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIE5hTiAobm90IGEgbnVtYmVyKSBzaWduLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0TmFuKCkge1xuICAgIHJldHVybiB0aGlzLm5hbjtcbiAgfVxuXG4gIC8qKlxuICAgKiBTeW1ib2xzIGxpc3QgdmFsaWRhdGlvbi5cbiAgICpcbiAgICogQHRocm93cyBMb2NhbGl6YXRpb25FeGNlcHRpb25cbiAgICovXG4gIHZhbGlkYXRlRGF0YSgpIHtcbiAgICBpZiAoIXRoaXMuZGVjaW1hbCB8fCB0eXBlb2YgdGhpcy5kZWNpbWFsICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBkZWNpbWFsJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLmdyb3VwIHx8IHR5cGVvZiB0aGlzLmdyb3VwICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBncm91cCcpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5saXN0IHx8IHR5cGVvZiB0aGlzLmxpc3QgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHN5bWJvbCBsaXN0Jyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLnBlcmNlbnRTaWduIHx8IHR5cGVvZiB0aGlzLnBlcmNlbnRTaWduICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBwZXJjZW50U2lnbicpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5taW51c1NpZ24gfHwgdHlwZW9mIHRoaXMubWludXNTaWduICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBtaW51c1NpZ24nKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMucGx1c1NpZ24gfHwgdHlwZW9mIHRoaXMucGx1c1NpZ24gIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHBsdXNTaWduJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLmV4cG9uZW50aWFsIHx8IHR5cGVvZiB0aGlzLmV4cG9uZW50aWFsICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBleHBvbmVudGlhbCcpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5zdXBlcnNjcmlwdGluZ0V4cG9uZW50IHx8IHR5cGVvZiB0aGlzLnN1cGVyc2NyaXB0aW5nRXhwb25lbnQgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHN1cGVyc2NyaXB0aW5nRXhwb25lbnQnKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMucGVyTWlsbGUgfHwgdHlwZW9mIHRoaXMucGVyTWlsbGUgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHBlck1pbGxlJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLmluZmluaXR5IHx8IHR5cGVvZiB0aGlzLmluZmluaXR5ICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBpbmZpbml0eScpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5uYW4gfHwgdHlwZW9mIHRoaXMubmFuICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBuYW4nKTtcbiAgICB9XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTnVtYmVyU3ltYm9sO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL2NsZHIvbnVtYmVyLXN5bWJvbC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AuXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIEFjYWRlbWljIEZyZWUgTGljZW5zZSAzLjAgKEFGTC0zLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cDovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMCBBY2FkZW1pYyBGcmVlIExpY2Vuc2UgMy4wIChBRkwtMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuaW1wb3J0IExvY2FsaXphdGlvbkV4Y2VwdGlvbiBmcm9tICcuLi9leGNlcHRpb24vbG9jYWxpemF0aW9uJztcbmltcG9ydCBOdW1iZXJTeW1ib2wgZnJvbSAnLi4vbnVtYmVyLXN5bWJvbCc7XG5cbmNsYXNzIE51bWJlclNwZWNpZmljYXRpb24ge1xuICAvKipcbiAgICogTnVtYmVyIHNwZWNpZmljYXRpb24gY29uc3RydWN0b3IuXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgcG9zaXRpdmVQYXR0ZXJuIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIGZvciBwb3NpdGl2ZSBhbW91bnRzXG4gICAqIEBwYXJhbSBzdHJpbmcgbmVnYXRpdmVQYXR0ZXJuIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIGZvciBuZWdhdGl2ZSBhbW91bnRzXG4gICAqIEBwYXJhbSBOdW1iZXJTeW1ib2wgc3ltYm9sIE51bWJlciBzeW1ib2xcbiAgICogQHBhcmFtIGludCBtYXhGcmFjdGlvbkRpZ2l0cyBNYXhpbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3JcbiAgICogQHBhcmFtIGludCBtaW5GcmFjdGlvbkRpZ2l0cyBNaW5pbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3JcbiAgICogQHBhcmFtIGJvb2wgZ3JvdXBpbmdVc2VkIElzIGRpZ2l0cyBncm91cGluZyB1c2VkID9cbiAgICogQHBhcmFtIGludCBwcmltYXJ5R3JvdXBTaXplIFNpemUgb2YgcHJpbWFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlclxuICAgKiBAcGFyYW0gaW50IHNlY29uZGFyeUdyb3VwU2l6ZSBTaXplIG9mIHNlY29uZGFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlclxuICAgKlxuICAgKiBAdGhyb3dzIExvY2FsaXphdGlvbkV4Y2VwdGlvblxuICAgKi9cbiAgY29uc3RydWN0b3IoXG4gICAgcG9zaXRpdmVQYXR0ZXJuLFxuICAgIG5lZ2F0aXZlUGF0dGVybixcbiAgICBzeW1ib2wsXG4gICAgbWF4RnJhY3Rpb25EaWdpdHMsXG4gICAgbWluRnJhY3Rpb25EaWdpdHMsXG4gICAgZ3JvdXBpbmdVc2VkLFxuICAgIHByaW1hcnlHcm91cFNpemUsXG4gICAgc2Vjb25kYXJ5R3JvdXBTaXplLFxuICApIHtcbiAgICB0aGlzLnBvc2l0aXZlUGF0dGVybiA9IHBvc2l0aXZlUGF0dGVybjtcbiAgICB0aGlzLm5lZ2F0aXZlUGF0dGVybiA9IG5lZ2F0aXZlUGF0dGVybjtcbiAgICB0aGlzLnN5bWJvbCA9IHN5bWJvbDtcblxuICAgIHRoaXMubWF4RnJhY3Rpb25EaWdpdHMgPSBtYXhGcmFjdGlvbkRpZ2l0cztcbiAgICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmVcbiAgICB0aGlzLm1pbkZyYWN0aW9uRGlnaXRzID0gbWF4RnJhY3Rpb25EaWdpdHMgPCBtaW5GcmFjdGlvbkRpZ2l0cyA/IG1heEZyYWN0aW9uRGlnaXRzIDogbWluRnJhY3Rpb25EaWdpdHM7XG5cbiAgICB0aGlzLmdyb3VwaW5nVXNlZCA9IGdyb3VwaW5nVXNlZDtcbiAgICB0aGlzLnByaW1hcnlHcm91cFNpemUgPSBwcmltYXJ5R3JvdXBTaXplO1xuICAgIHRoaXMuc2Vjb25kYXJ5R3JvdXBTaXplID0gc2Vjb25kYXJ5R3JvdXBTaXplO1xuXG4gICAgaWYgKCF0aGlzLnBvc2l0aXZlUGF0dGVybiB8fCB0eXBlb2YgdGhpcy5wb3NpdGl2ZVBhdHRlcm4gIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHBvc2l0aXZlUGF0dGVybicpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5uZWdhdGl2ZVBhdHRlcm4gfHwgdHlwZW9mIHRoaXMubmVnYXRpdmVQYXR0ZXJuICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBuZWdhdGl2ZVBhdHRlcm4nKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMuc3ltYm9sIHx8ICEodGhpcy5zeW1ib2wgaW5zdGFuY2VvZiBOdW1iZXJTeW1ib2wpKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHN5bWJvbCcpO1xuICAgIH1cblxuICAgIGlmICh0eXBlb2YgdGhpcy5tYXhGcmFjdGlvbkRpZ2l0cyAhPT0gJ251bWJlcicpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgbWF4RnJhY3Rpb25EaWdpdHMnKTtcbiAgICB9XG5cbiAgICBpZiAodHlwZW9mIHRoaXMubWluRnJhY3Rpb25EaWdpdHMgIT09ICdudW1iZXInKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIG1pbkZyYWN0aW9uRGlnaXRzJyk7XG4gICAgfVxuXG4gICAgaWYgKHR5cGVvZiB0aGlzLmdyb3VwaW5nVXNlZCAhPT0gJ2Jvb2xlYW4nKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIGdyb3VwaW5nVXNlZCcpO1xuICAgIH1cblxuICAgIGlmICh0eXBlb2YgdGhpcy5wcmltYXJ5R3JvdXBTaXplICE9PSAnbnVtYmVyJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBwcmltYXJ5R3JvdXBTaXplJyk7XG4gICAgfVxuXG4gICAgaWYgKHR5cGVvZiB0aGlzLnNlY29uZGFyeUdyb3VwU2l6ZSAhPT0gJ251bWJlcicpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgc2Vjb25kYXJ5R3JvdXBTaXplJyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEdldCBzeW1ib2wuXG4gICAqXG4gICAqIEByZXR1cm4gTnVtYmVyU3ltYm9sXG4gICAqL1xuICBnZXRTeW1ib2woKSB7XG4gICAgcmV0dXJuIHRoaXMuc3ltYm9sO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgZm9ybWF0dGluZyBydWxlcyBmb3IgdGhpcyBudW1iZXIgKHdoZW4gcG9zaXRpdmUpLlxuICAgKlxuICAgKiBUaGlzIHBhdHRlcm4gdXNlcyB0aGUgVW5pY29kZSBDTERSIG51bWJlciBwYXR0ZXJuIHN5bnRheFxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0UG9zaXRpdmVQYXR0ZXJuKCkge1xuICAgIHJldHVybiB0aGlzLnBvc2l0aXZlUGF0dGVybjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGZvcm1hdHRpbmcgcnVsZXMgZm9yIHRoaXMgbnVtYmVyICh3aGVuIG5lZ2F0aXZlKS5cbiAgICpcbiAgICogVGhpcyBwYXR0ZXJuIHVzZXMgdGhlIFVuaWNvZGUgQ0xEUiBudW1iZXIgcGF0dGVybiBzeW50YXhcbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldE5lZ2F0aXZlUGF0dGVybigpIHtcbiAgICByZXR1cm4gdGhpcy5uZWdhdGl2ZVBhdHRlcm47XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBtYXhpbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3IgKHJvdW5kaW5nIGlmIG5lZWRlZCkuXG4gICAqXG4gICAqIEByZXR1cm4gaW50XG4gICAqL1xuICBnZXRNYXhGcmFjdGlvbkRpZ2l0cygpIHtcbiAgICByZXR1cm4gdGhpcy5tYXhGcmFjdGlvbkRpZ2l0cztcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIG1pbmltdW0gbnVtYmVyIG9mIGRpZ2l0cyBhZnRlciBkZWNpbWFsIHNlcGFyYXRvciAoZmlsbCB3aXRoIFwiMFwiIGlmIG5lZWRlZCkuXG4gICAqXG4gICAqIEByZXR1cm4gaW50XG4gICAqL1xuICBnZXRNaW5GcmFjdGlvbkRpZ2l0cygpIHtcbiAgICByZXR1cm4gdGhpcy5taW5GcmFjdGlvbkRpZ2l0cztcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIFwiZ3JvdXBpbmdcIiBmbGFnLiBUaGlzIGZsYWcgZGVmaW5lcyBpZiBkaWdpdHNcbiAgICogZ3JvdXBpbmcgc2hvdWxkIGJlIHVzZWQgd2hlbiBmb3JtYXR0aW5nIHRoaXMgbnVtYmVyLlxuICAgKlxuICAgKiBAcmV0dXJuIGJvb2xcbiAgICovXG4gIGlzR3JvdXBpbmdVc2VkKCkge1xuICAgIHJldHVybiB0aGlzLmdyb3VwaW5nVXNlZDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIHNpemUgb2YgcHJpbWFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlci5cbiAgICpcbiAgICogQHJldHVybiBpbnRcbiAgICovXG4gIGdldFByaW1hcnlHcm91cFNpemUoKSB7XG4gICAgcmV0dXJuIHRoaXMucHJpbWFyeUdyb3VwU2l6ZTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIHNpemUgb2Ygc2Vjb25kYXJ5IGRpZ2l0cyBncm91cHMgaW4gdGhlIG51bWJlci5cbiAgICpcbiAgICogQHJldHVybiBpbnRcbiAgICovXG4gIGdldFNlY29uZGFyeUdyb3VwU2l6ZSgpIHtcbiAgICByZXR1cm4gdGhpcy5zZWNvbmRhcnlHcm91cFNpemU7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTnVtYmVyU3BlY2lmaWNhdGlvbjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL3NwZWNpZmljYXRpb25zL251bWJlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AuXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIEFjYWRlbWljIEZyZWUgTGljZW5zZSAzLjAgKEFGTC0zLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cDovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMCBBY2FkZW1pYyBGcmVlIExpY2Vuc2UgMy4wIChBRkwtMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuY2xhc3MgTG9jYWxpemF0aW9uRXhjZXB0aW9uIHtcbiAgY29uc3RydWN0b3IobWVzc2FnZSkge1xuICAgIHRoaXMubWVzc2FnZSA9IG1lc3NhZ2U7XG4gICAgdGhpcy5uYW1lID0gJ0xvY2FsaXphdGlvbkV4Y2VwdGlvbic7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTG9jYWxpemF0aW9uRXhjZXB0aW9uO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL2NsZHIvZXhjZXB0aW9uL2xvY2FsaXphdGlvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AuXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIEFjYWRlbWljIEZyZWUgTGljZW5zZSAzLjAgKEFGTC0zLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cDovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvQUZMLTMuMCBBY2FkZW1pYyBGcmVlIExpY2Vuc2UgMy4wIChBRkwtMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuaW1wb3J0IExvY2FsaXphdGlvbkV4Y2VwdGlvbiBmcm9tICcuLi9leGNlcHRpb24vbG9jYWxpemF0aW9uJztcbmltcG9ydCBOdW1iZXJTcGVjaWZpY2F0aW9uIGZyb20gJy4vbnVtYmVyJztcblxuLyoqXG4gKiBDdXJyZW5jeSBkaXNwbGF5IG9wdGlvbjogc3ltYm9sIG5vdGF0aW9uLlxuICovXG5jb25zdCBDVVJSRU5DWV9ESVNQTEFZX1NZTUJPTCA9ICdzeW1ib2wnO1xuXG5cbmNsYXNzIFByaWNlU3BlY2lmaWNhdGlvbiBleHRlbmRzIE51bWJlclNwZWNpZmljYXRpb24ge1xuICAvKipcbiAgICogUHJpY2Ugc3BlY2lmaWNhdGlvbiBjb25zdHJ1Y3Rvci5cbiAgICpcbiAgICogQHBhcmFtIHN0cmluZyBwb3NpdGl2ZVBhdHRlcm4gQ0xEUiBmb3JtYXR0aW5nIHBhdHRlcm4gZm9yIHBvc2l0aXZlIGFtb3VudHNcbiAgICogQHBhcmFtIHN0cmluZyBuZWdhdGl2ZVBhdHRlcm4gQ0xEUiBmb3JtYXR0aW5nIHBhdHRlcm4gZm9yIG5lZ2F0aXZlIGFtb3VudHNcbiAgICogQHBhcmFtIE51bWJlclN5bWJvbCBzeW1ib2wgTnVtYmVyIHN5bWJvbFxuICAgKiBAcGFyYW0gaW50IG1heEZyYWN0aW9uRGlnaXRzIE1heGltdW0gbnVtYmVyIG9mIGRpZ2l0cyBhZnRlciBkZWNpbWFsIHNlcGFyYXRvclxuICAgKiBAcGFyYW0gaW50IG1pbkZyYWN0aW9uRGlnaXRzIE1pbmltdW0gbnVtYmVyIG9mIGRpZ2l0cyBhZnRlciBkZWNpbWFsIHNlcGFyYXRvclxuICAgKiBAcGFyYW0gYm9vbCBncm91cGluZ1VzZWQgSXMgZGlnaXRzIGdyb3VwaW5nIHVzZWQgP1xuICAgKiBAcGFyYW0gaW50IHByaW1hcnlHcm91cFNpemUgU2l6ZSBvZiBwcmltYXJ5IGRpZ2l0cyBncm91cCBpbiB0aGUgbnVtYmVyXG4gICAqIEBwYXJhbSBpbnQgc2Vjb25kYXJ5R3JvdXBTaXplIFNpemUgb2Ygc2Vjb25kYXJ5IGRpZ2l0cyBncm91cCBpbiB0aGUgbnVtYmVyXG4gICAqIEBwYXJhbSBzdHJpbmcgY3VycmVuY3lTeW1ib2wgQ3VycmVuY3kgc3ltYm9sIG9mIHRoaXMgcHJpY2UgKGVnLiA6IOKCrClcbiAgICogQHBhcmFtIGN1cnJlbmN5Q29kZSBDdXJyZW5jeSBjb2RlIG9mIHRoaXMgcHJpY2UgKGUuZy46IEVVUilcbiAgICpcbiAgICogQHRocm93cyBMb2NhbGl6YXRpb25FeGNlcHRpb25cbiAgICovXG4gIGNvbnN0cnVjdG9yKFxuICAgIHBvc2l0aXZlUGF0dGVybixcbiAgICBuZWdhdGl2ZVBhdHRlcm4sXG4gICAgc3ltYm9sLFxuICAgIG1heEZyYWN0aW9uRGlnaXRzLFxuICAgIG1pbkZyYWN0aW9uRGlnaXRzLFxuICAgIGdyb3VwaW5nVXNlZCxcbiAgICBwcmltYXJ5R3JvdXBTaXplLFxuICAgIHNlY29uZGFyeUdyb3VwU2l6ZSxcbiAgICBjdXJyZW5jeVN5bWJvbCxcbiAgICBjdXJyZW5jeUNvZGUsXG4gICkge1xuICAgIHN1cGVyKFxuICAgICAgcG9zaXRpdmVQYXR0ZXJuLFxuICAgICAgbmVnYXRpdmVQYXR0ZXJuLFxuICAgICAgc3ltYm9sLFxuICAgICAgbWF4RnJhY3Rpb25EaWdpdHMsXG4gICAgICBtaW5GcmFjdGlvbkRpZ2l0cyxcbiAgICAgIGdyb3VwaW5nVXNlZCxcbiAgICAgIHByaW1hcnlHcm91cFNpemUsXG4gICAgICBzZWNvbmRhcnlHcm91cFNpemUsXG4gICAgKTtcbiAgICB0aGlzLmN1cnJlbmN5U3ltYm9sID0gY3VycmVuY3lTeW1ib2w7XG4gICAgdGhpcy5jdXJyZW5jeUNvZGUgPSBjdXJyZW5jeUNvZGU7XG5cbiAgICBpZiAoIXRoaXMuY3VycmVuY3lTeW1ib2wgfHwgdHlwZW9mIHRoaXMuY3VycmVuY3lTeW1ib2wgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIGN1cnJlbmN5U3ltYm9sJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLmN1cnJlbmN5Q29kZSB8fCB0eXBlb2YgdGhpcy5jdXJyZW5jeUNvZGUgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIGN1cnJlbmN5Q29kZScpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdHlwZSBvZiBkaXNwbGF5IGZvciBjdXJyZW5jeSBzeW1ib2wuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBzdGF0aWMgZ2V0Q3VycmVuY3lEaXNwbGF5KCkge1xuICAgIHJldHVybiBDVVJSRU5DWV9ESVNQTEFZX1NZTUJPTDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGN1cnJlbmN5IHN5bWJvbFxuICAgKiBlLmcuOiDigqwuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRDdXJyZW5jeVN5bWJvbCgpIHtcbiAgICByZXR1cm4gdGhpcy5jdXJyZW5jeVN5bWJvbDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGN1cnJlbmN5IElTTyBjb2RlXG4gICAqIGUuZy46IEVVUi5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldEN1cnJlbmN5Q29kZSgpIHtcbiAgICByZXR1cm4gdGhpcy5jdXJyZW5jeUNvZGU7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgUHJpY2VTcGVjaWZpY2F0aW9uO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL2NsZHIvc3BlY2lmaWNhdGlvbnMvcHJpY2UuanMiXSwic291cmNlUm9vdCI6IiJ9