window["order_view"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 520);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

exports.default = function (instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
};

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _defineProperty = __webpack_require__(19);

var _defineProperty2 = _interopRequireDefault(_defineProperty);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      (0, _defineProperty2.default)(target, descriptor.key, descriptor);
    }
  }

  return function (Constructor, protoProps, staticProps) {
    if (protoProps) defineProperties(Constructor.prototype, protoProps);
    if (staticProps) defineProperties(Constructor, staticProps);
    return Constructor;
  };
}();

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__(7)(function(){
  return Object.defineProperty({}, 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),
/* 3 */
/***/ (function(module, exports) {

var core = module.exports = {version: '2.4.0'};
if(typeof __e == 'number')__e = core; // eslint-disable-line no-undef

/***/ }),
/* 4 */
/***/ (function(module, exports) {

module.exports = function(it){
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};

/***/ }),
/* 5 */
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self : Function('return this')();
if(typeof __g == 'number')__g = global; // eslint-disable-line no-undef

/***/ }),
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

var anObject       = __webpack_require__(11)
  , IE8_DOM_DEFINE = __webpack_require__(17)
  , toPrimitive    = __webpack_require__(13)
  , dP             = Object.defineProperty;

exports.f = __webpack_require__(2) ? Object.defineProperty : function defineProperty(O, P, Attributes){
  anObject(O);
  P = toPrimitive(P, true);
  anObject(Attributes);
  if(IE8_DOM_DEFINE)try {
    return dP(O, P, Attributes);
  } catch(e){ /* empty */ }
  if('get' in Attributes || 'set' in Attributes)throw TypeError('Accessors not supported!');
  if('value' in Attributes)O[P] = Attributes.value;
  return O;
};

/***/ }),
/* 7 */
/***/ (function(module, exports) {

module.exports = function(exec){
  try {
    return !!exec();
  } catch(e){
    return true;
  }
};

/***/ }),
/* 8 */
/***/ (function(module, exports, __webpack_require__) {

var global    = __webpack_require__(5)
  , core      = __webpack_require__(3)
  , ctx       = __webpack_require__(15)
  , hide      = __webpack_require__(10)
  , PROTOTYPE = 'prototype';

var $export = function(type, name, source){
  var IS_FORCED = type & $export.F
    , IS_GLOBAL = type & $export.G
    , IS_STATIC = type & $export.S
    , IS_PROTO  = type & $export.P
    , IS_BIND   = type & $export.B
    , IS_WRAP   = type & $export.W
    , exports   = IS_GLOBAL ? core : core[name] || (core[name] = {})
    , expProto  = exports[PROTOTYPE]
    , target    = IS_GLOBAL ? global : IS_STATIC ? global[name] : (global[name] || {})[PROTOTYPE]
    , key, own, out;
  if(IS_GLOBAL)source = name;
  for(key in source){
    // contains in native
    own = !IS_FORCED && target && target[key] !== undefined;
    if(own && key in exports)continue;
    // export native or passed
    out = own ? target[key] : source[key];
    // prevent global pollution for namespaces
    exports[key] = IS_GLOBAL && typeof target[key] != 'function' ? source[key]
    // bind timers to global for call from export context
    : IS_BIND && own ? ctx(out, global)
    // wrap global constructors for prevent change them in library
    : IS_WRAP && target[key] == out ? (function(C){
      var F = function(a, b, c){
        if(this instanceof C){
          switch(arguments.length){
            case 0: return new C;
            case 1: return new C(a);
            case 2: return new C(a, b);
          } return new C(a, b, c);
        } return C.apply(this, arguments);
      };
      F[PROTOTYPE] = C[PROTOTYPE];
      return F;
    // make static versions for prototype methods
    })(out) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
    // export proto methods to core.%CONSTRUCTOR%.methods.%NAME%
    if(IS_PROTO){
      (exports.virtual || (exports.virtual = {}))[key] = out;
      // export proto methods to core.%CONSTRUCTOR%.prototype.%NAME%
      if(type & $export.R && expProto && !expProto[key])hide(expProto, key, out);
    }
  }
};
// type bitmap
$export.F = 1;   // forced
$export.G = 2;   // global
$export.S = 4;   // static
$export.P = 8;   // proto
$export.B = 16;  // bind
$export.W = 32;  // wrap
$export.U = 64;  // safe
$export.R = 128; // real proto method for `library` 
module.exports = $export;

/***/ }),
/* 9 */
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
/* 10 */
/***/ (function(module, exports, __webpack_require__) {

var dP         = __webpack_require__(6)
  , createDesc = __webpack_require__(12);
module.exports = __webpack_require__(2) ? function(object, key, value){
  return dP.f(object, key, createDesc(1, value));
} : function(object, key, value){
  object[key] = value;
  return object;
};

/***/ }),
/* 11 */
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4);
module.exports = function(it){
  if(!isObject(it))throw TypeError(it + ' is not an object!');
  return it;
};

/***/ }),
/* 12 */
/***/ (function(module, exports) {

module.exports = function(bitmap, value){
  return {
    enumerable  : !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable    : !(bitmap & 4),
    value       : value
  };
};

/***/ }),
/* 13 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__(4);
// instead of the ES6 spec version, we didn't implement @@toPrimitive case
// and the second argument - flag - preferred type is a string
module.exports = function(it, S){
  if(!isObject(it))return it;
  var fn, val;
  if(S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
  if(typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it)))return val;
  if(!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
  throw TypeError("Can't convert object to primitive value");
};

/***/ }),
/* 14 */,
/* 15 */
/***/ (function(module, exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__(18);
module.exports = function(fn, that, length){
  aFunction(fn);
  if(that === undefined)return fn;
  switch(length){
    case 1: return function(a){
      return fn.call(that, a);
    };
    case 2: return function(a, b){
      return fn.call(that, a, b);
    };
    case 3: return function(a, b, c){
      return fn.call(that, a, b, c);
    };
  }
  return function(/* ...args */){
    return fn.apply(that, arguments);
  };
};

/***/ }),
/* 16 */
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4)
  , document = __webpack_require__(5).document
  // in old IE typeof document.createElement is 'object'
  , is = isObject(document) && isObject(document.createElement);
module.exports = function(it){
  return is ? document.createElement(it) : {};
};

/***/ }),
/* 17 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__(2) && !__webpack_require__(7)(function(){
  return Object.defineProperty(__webpack_require__(16)('div'), 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),
/* 18 */
/***/ (function(module, exports) {

module.exports = function(it){
  if(typeof it != 'function')throw TypeError(it + ' is not a function!');
  return it;
};

/***/ }),
/* 19 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(20), __esModule: true };

/***/ }),
/* 20 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(21);
var $Object = __webpack_require__(3).Object;
module.exports = function defineProperty(it, key, desc){
  return $Object.defineProperty(it, key, desc);
};

/***/ }),
/* 21 */
/***/ (function(module, exports, __webpack_require__) {

var $export = __webpack_require__(8);
// 19.1.2.4 / 15.2.3.6 Object.defineProperty(O, P, Attributes)
$export($export.S + $export.F * !__webpack_require__(2), 'Object', {defineProperty: __webpack_require__(6).f});

/***/ }),
/* 22 */
/***/ (function(module, exports, __webpack_require__) {

// to indexed object, toObject with fallback for non-array-like ES3 strings
var IObject = __webpack_require__(51)
  , defined = __webpack_require__(38);
module.exports = function(it){
  return IObject(defined(it));
};

/***/ }),
/* 23 */,
/* 24 */,
/* 25 */,
/* 26 */,
/* 27 */
/***/ (function(module, exports) {

var hasOwnProperty = {}.hasOwnProperty;
module.exports = function(it, key){
  return hasOwnProperty.call(it, key);
};

/***/ }),
/* 28 */,
/* 29 */
/***/ (function(module, exports, __webpack_require__) {

var store      = __webpack_require__(50)('wks')
  , uid        = __webpack_require__(43)
  , Symbol     = __webpack_require__(5).Symbol
  , USE_SYMBOL = typeof Symbol == 'function';

var $exports = module.exports = function(name){
  return store[name] || (store[name] =
    USE_SYMBOL && Symbol[name] || (USE_SYMBOL ? Symbol : uid)('Symbol.' + name));
};

$exports.store = store;

/***/ }),
/* 30 */,
/* 31 */,
/* 32 */,
/* 33 */,
/* 34 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 / 15.2.3.14 Object.keys(O)
var $keys       = __webpack_require__(55)
  , enumBugKeys = __webpack_require__(49);

module.exports = Object.keys || function keys(O){
  return $keys(O, enumBugKeys);
};

/***/ }),
/* 35 */,
/* 36 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.EventEmitter = undefined;

var _events = __webpack_require__(53);

var _events2 = _interopRequireDefault(_events);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * We instanciate one EventEmitter (restricted via a const) so that every components
 * register/dispatch on the same one and can communicate with each other.
 */
var EventEmitter = exports.EventEmitter = new _events2.default(); /**
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

/***/ }),
/* 37 */,
/* 38 */
/***/ (function(module, exports) {

// 7.2.1 RequireObjectCoercible(argument)
module.exports = function(it){
  if(it == undefined)throw TypeError("Can't call method on  " + it);
  return it;
};

/***/ }),
/* 39 */
/***/ (function(module, exports) {

// 7.1.4 ToInteger
var ceil  = Math.ceil
  , floor = Math.floor;
module.exports = function(it){
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};

/***/ }),
/* 40 */,
/* 41 */,
/* 42 */,
/* 43 */
/***/ (function(module, exports) {

var id = 0
  , px = Math.random();
module.exports = function(key){
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};

/***/ }),
/* 44 */,
/* 45 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = ConfirmModal;
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

var $ = window.$;

/**
 * ConfirmModal component
 *
 * @param {String} id
 * @param {String} confirmTitle
 * @param {String} confirmMessage
 * @param {String} closeButtonLabel
 * @param {String} confirmButtonLabel
 * @param {String} confirmButtonClass
 * @param {Boolean} closable
 * @param {Function} confirmCallback
 *
 */
function ConfirmModal(params, confirmCallback) {
  var _this = this;

  // Construct the modal
  var id = params.id,
      closable = params.closable;

  this.modal = Modal(params);

  // jQuery modal object
  this.$modal = $(this.modal.container);

  this.show = function () {
    _this.$modal.modal();
  };

  this.modal.confirmButton.addEventListener('click', confirmCallback);

  this.$modal.modal({
    backdrop: closable ? true : 'static',
    keyboard: closable !== undefined ? closable : true,
    closable: closable !== undefined ? closable : true,
    show: false
  });

  this.$modal.on('hidden.bs.modal', function () {
    document.querySelector('#' + id).remove();
  });

  document.body.appendChild(this.modal.container);
}

/**
 * Modal component to improve lisibility by constructing the modal outside the main function
 *
 * @param {Object} params
 *
 */
function Modal(_ref) {
  var _ref$id = _ref.id,
      id = _ref$id === undefined ? 'confirm_modal' : _ref$id,
      confirmTitle = _ref.confirmTitle,
      _ref$confirmMessage = _ref.confirmMessage,
      confirmMessage = _ref$confirmMessage === undefined ? '' : _ref$confirmMessage,
      _ref$closeButtonLabel = _ref.closeButtonLabel,
      closeButtonLabel = _ref$closeButtonLabel === undefined ? 'Close' : _ref$closeButtonLabel,
      _ref$confirmButtonLab = _ref.confirmButtonLabel,
      confirmButtonLabel = _ref$confirmButtonLab === undefined ? 'Accept' : _ref$confirmButtonLab,
      _ref$confirmButtonCla = _ref.confirmButtonClass,
      confirmButtonClass = _ref$confirmButtonCla === undefined ? 'btn-primary' : _ref$confirmButtonCla;

  var modal = {};

  // Main modal element
  modal.container = document.createElement('div');
  modal.container.classList.add('modal', 'fade');
  modal.container.id = id;

  // Modal dialog element
  modal.dialog = document.createElement('div');
  modal.dialog.classList.add('modal-dialog');

  // Modal content element
  modal.content = document.createElement('div');
  modal.content.classList.add('modal-content');

  // Modal header element
  modal.header = document.createElement('div');
  modal.header.classList.add('modal-header');

  // Modal title element
  if (confirmTitle) {
    modal.title = document.createElement('h4');
    modal.title.classList.add('modal-title');
    modal.title.innerHTML = confirmTitle;
  }

  // Modal close button icon
  modal.closeIcon = document.createElement('button');
  modal.closeIcon.classList.add('close');
  modal.closeIcon.setAttribute('type', 'button');
  modal.closeIcon.dataset.dismiss = 'modal';
  modal.closeIcon.innerHTML = '×';

  // Modal body element
  modal.body = document.createElement('div');
  modal.body.classList.add('modal-body', 'text-left', 'font-weight-normal');

  // Modal message element
  modal.message = document.createElement('p');
  modal.message.classList.add('confirm-message');
  modal.message.innerHTML = confirmMessage;

  // Modal footer element
  modal.footer = document.createElement('div');
  modal.footer.classList.add('modal-footer');

  // Modal close button element
  modal.closeButton = document.createElement('button');
  modal.closeButton.setAttribute('type', 'button');
  modal.closeButton.classList.add('btn', 'btn-outline-secondary', 'btn-lg');
  modal.closeButton.dataset.dismiss = 'modal';
  modal.closeButton.innerHTML = closeButtonLabel;

  // Modal close button element
  modal.confirmButton = document.createElement('button');
  modal.confirmButton.setAttribute('type', 'button');
  modal.confirmButton.classList.add('btn', confirmButtonClass, 'btn-lg', 'btn-confirm-submit');
  modal.confirmButton.dataset.dismiss = 'modal';
  modal.confirmButton.innerHTML = confirmButtonLabel;

  // Constructing the modal
  if (confirmTitle) {
    modal.header.append(modal.title, modal.closeIcon);
  } else {
    modal.header.appendChild(modal.closeIcon);
  }

  modal.body.appendChild(modal.message);
  modal.footer.append(modal.closeButton, modal.confirmButton);
  modal.content.append(modal.header, modal.body, modal.footer);
  modal.dialog.appendChild(modal.content);
  modal.container.appendChild(modal.dialog);

  return modal;
}

/***/ }),
/* 46 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.13 ToObject(argument)
var defined = __webpack_require__(38);
module.exports = function(it){
  return Object(defined(it));
};

/***/ }),
/* 47 */
/***/ (function(module, exports, __webpack_require__) {

var shared = __webpack_require__(50)('keys')
  , uid    = __webpack_require__(43);
module.exports = function(key){
  return shared[key] || (shared[key] = uid(key));
};

/***/ }),
/* 48 */
/***/ (function(module, exports) {

var toString = {}.toString;

module.exports = function(it){
  return toString.call(it).slice(8, -1);
};

/***/ }),
/* 49 */
/***/ (function(module, exports) {

// IE 8- don't enum bug keys
module.exports = (
  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
).split(',');

/***/ }),
/* 50 */
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__(5)
  , SHARED = '__core-js_shared__'
  , store  = global[SHARED] || (global[SHARED] = {});
module.exports = function(key){
  return store[key] || (store[key] = {});
};

/***/ }),
/* 51 */
/***/ (function(module, exports, __webpack_require__) {

// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = __webpack_require__(48);
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function(it){
  return cof(it) == 'String' ? it.split('') : Object(it);
};

/***/ }),
/* 52 */
/***/ (function(module, exports) {

exports.f = {}.propertyIsEnumerable;

/***/ }),
/* 53 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.



var R = typeof Reflect === 'object' ? Reflect : null
var ReflectApply = R && typeof R.apply === 'function'
  ? R.apply
  : function ReflectApply(target, receiver, args) {
    return Function.prototype.apply.call(target, receiver, args);
  }

var ReflectOwnKeys
if (R && typeof R.ownKeys === 'function') {
  ReflectOwnKeys = R.ownKeys
} else if (Object.getOwnPropertySymbols) {
  ReflectOwnKeys = function ReflectOwnKeys(target) {
    return Object.getOwnPropertyNames(target)
      .concat(Object.getOwnPropertySymbols(target));
  };
} else {
  ReflectOwnKeys = function ReflectOwnKeys(target) {
    return Object.getOwnPropertyNames(target);
  };
}

function ProcessEmitWarning(warning) {
  if (console && console.warn) console.warn(warning);
}

var NumberIsNaN = Number.isNaN || function NumberIsNaN(value) {
  return value !== value;
}

function EventEmitter() {
  EventEmitter.init.call(this);
}
module.exports = EventEmitter;

// Backwards-compat with node 0.10.x
EventEmitter.EventEmitter = EventEmitter;

EventEmitter.prototype._events = undefined;
EventEmitter.prototype._eventsCount = 0;
EventEmitter.prototype._maxListeners = undefined;

// By default EventEmitters will print a warning if more than 10 listeners are
// added to it. This is a useful default which helps finding memory leaks.
var defaultMaxListeners = 10;

Object.defineProperty(EventEmitter, 'defaultMaxListeners', {
  enumerable: true,
  get: function() {
    return defaultMaxListeners;
  },
  set: function(arg) {
    if (typeof arg !== 'number' || arg < 0 || NumberIsNaN(arg)) {
      throw new RangeError('The value of "defaultMaxListeners" is out of range. It must be a non-negative number. Received ' + arg + '.');
    }
    defaultMaxListeners = arg;
  }
});

EventEmitter.init = function() {

  if (this._events === undefined ||
      this._events === Object.getPrototypeOf(this)._events) {
    this._events = Object.create(null);
    this._eventsCount = 0;
  }

  this._maxListeners = this._maxListeners || undefined;
};

// Obviously not all Emitters should be limited to 10. This function allows
// that to be increased. Set to zero for unlimited.
EventEmitter.prototype.setMaxListeners = function setMaxListeners(n) {
  if (typeof n !== 'number' || n < 0 || NumberIsNaN(n)) {
    throw new RangeError('The value of "n" is out of range. It must be a non-negative number. Received ' + n + '.');
  }
  this._maxListeners = n;
  return this;
};

function $getMaxListeners(that) {
  if (that._maxListeners === undefined)
    return EventEmitter.defaultMaxListeners;
  return that._maxListeners;
}

EventEmitter.prototype.getMaxListeners = function getMaxListeners() {
  return $getMaxListeners(this);
};

EventEmitter.prototype.emit = function emit(type) {
  var args = [];
  for (var i = 1; i < arguments.length; i++) args.push(arguments[i]);
  var doError = (type === 'error');

  var events = this._events;
  if (events !== undefined)
    doError = (doError && events.error === undefined);
  else if (!doError)
    return false;

  // If there is no 'error' event listener then throw.
  if (doError) {
    var er;
    if (args.length > 0)
      er = args[0];
    if (er instanceof Error) {
      // Note: The comments on the `throw` lines are intentional, they show
      // up in Node's output if this results in an unhandled exception.
      throw er; // Unhandled 'error' event
    }
    // At least give some kind of context to the user
    var err = new Error('Unhandled error.' + (er ? ' (' + er.message + ')' : ''));
    err.context = er;
    throw err; // Unhandled 'error' event
  }

  var handler = events[type];

  if (handler === undefined)
    return false;

  if (typeof handler === 'function') {
    ReflectApply(handler, this, args);
  } else {
    var len = handler.length;
    var listeners = arrayClone(handler, len);
    for (var i = 0; i < len; ++i)
      ReflectApply(listeners[i], this, args);
  }

  return true;
};

function _addListener(target, type, listener, prepend) {
  var m;
  var events;
  var existing;

  if (typeof listener !== 'function') {
    throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
  }

  events = target._events;
  if (events === undefined) {
    events = target._events = Object.create(null);
    target._eventsCount = 0;
  } else {
    // To avoid recursion in the case that type === "newListener"! Before
    // adding it to the listeners, first emit "newListener".
    if (events.newListener !== undefined) {
      target.emit('newListener', type,
                  listener.listener ? listener.listener : listener);

      // Re-assign `events` because a newListener handler could have caused the
      // this._events to be assigned to a new object
      events = target._events;
    }
    existing = events[type];
  }

  if (existing === undefined) {
    // Optimize the case of one listener. Don't need the extra array object.
    existing = events[type] = listener;
    ++target._eventsCount;
  } else {
    if (typeof existing === 'function') {
      // Adding the second element, need to change to array.
      existing = events[type] =
        prepend ? [listener, existing] : [existing, listener];
      // If we've already got an array, just append.
    } else if (prepend) {
      existing.unshift(listener);
    } else {
      existing.push(listener);
    }

    // Check for listener leak
    m = $getMaxListeners(target);
    if (m > 0 && existing.length > m && !existing.warned) {
      existing.warned = true;
      // No error code for this since it is a Warning
      // eslint-disable-next-line no-restricted-syntax
      var w = new Error('Possible EventEmitter memory leak detected. ' +
                          existing.length + ' ' + String(type) + ' listeners ' +
                          'added. Use emitter.setMaxListeners() to ' +
                          'increase limit');
      w.name = 'MaxListenersExceededWarning';
      w.emitter = target;
      w.type = type;
      w.count = existing.length;
      ProcessEmitWarning(w);
    }
  }

  return target;
}

EventEmitter.prototype.addListener = function addListener(type, listener) {
  return _addListener(this, type, listener, false);
};

EventEmitter.prototype.on = EventEmitter.prototype.addListener;

EventEmitter.prototype.prependListener =
    function prependListener(type, listener) {
      return _addListener(this, type, listener, true);
    };

function onceWrapper() {
  var args = [];
  for (var i = 0; i < arguments.length; i++) args.push(arguments[i]);
  if (!this.fired) {
    this.target.removeListener(this.type, this.wrapFn);
    this.fired = true;
    ReflectApply(this.listener, this.target, args);
  }
}

function _onceWrap(target, type, listener) {
  var state = { fired: false, wrapFn: undefined, target: target, type: type, listener: listener };
  var wrapped = onceWrapper.bind(state);
  wrapped.listener = listener;
  state.wrapFn = wrapped;
  return wrapped;
}

EventEmitter.prototype.once = function once(type, listener) {
  if (typeof listener !== 'function') {
    throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
  }
  this.on(type, _onceWrap(this, type, listener));
  return this;
};

EventEmitter.prototype.prependOnceListener =
    function prependOnceListener(type, listener) {
      if (typeof listener !== 'function') {
        throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
      }
      this.prependListener(type, _onceWrap(this, type, listener));
      return this;
    };

// Emits a 'removeListener' event if and only if the listener was removed.
EventEmitter.prototype.removeListener =
    function removeListener(type, listener) {
      var list, events, position, i, originalListener;

      if (typeof listener !== 'function') {
        throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
      }

      events = this._events;
      if (events === undefined)
        return this;

      list = events[type];
      if (list === undefined)
        return this;

      if (list === listener || list.listener === listener) {
        if (--this._eventsCount === 0)
          this._events = Object.create(null);
        else {
          delete events[type];
          if (events.removeListener)
            this.emit('removeListener', type, list.listener || listener);
        }
      } else if (typeof list !== 'function') {
        position = -1;

        for (i = list.length - 1; i >= 0; i--) {
          if (list[i] === listener || list[i].listener === listener) {
            originalListener = list[i].listener;
            position = i;
            break;
          }
        }

        if (position < 0)
          return this;

        if (position === 0)
          list.shift();
        else {
          spliceOne(list, position);
        }

        if (list.length === 1)
          events[type] = list[0];

        if (events.removeListener !== undefined)
          this.emit('removeListener', type, originalListener || listener);
      }

      return this;
    };

EventEmitter.prototype.off = EventEmitter.prototype.removeListener;

EventEmitter.prototype.removeAllListeners =
    function removeAllListeners(type) {
      var listeners, events, i;

      events = this._events;
      if (events === undefined)
        return this;

      // not listening for removeListener, no need to emit
      if (events.removeListener === undefined) {
        if (arguments.length === 0) {
          this._events = Object.create(null);
          this._eventsCount = 0;
        } else if (events[type] !== undefined) {
          if (--this._eventsCount === 0)
            this._events = Object.create(null);
          else
            delete events[type];
        }
        return this;
      }

      // emit removeListener for all listeners on all events
      if (arguments.length === 0) {
        var keys = Object.keys(events);
        var key;
        for (i = 0; i < keys.length; ++i) {
          key = keys[i];
          if (key === 'removeListener') continue;
          this.removeAllListeners(key);
        }
        this.removeAllListeners('removeListener');
        this._events = Object.create(null);
        this._eventsCount = 0;
        return this;
      }

      listeners = events[type];

      if (typeof listeners === 'function') {
        this.removeListener(type, listeners);
      } else if (listeners !== undefined) {
        // LIFO order
        for (i = listeners.length - 1; i >= 0; i--) {
          this.removeListener(type, listeners[i]);
        }
      }

      return this;
    };

function _listeners(target, type, unwrap) {
  var events = target._events;

  if (events === undefined)
    return [];

  var evlistener = events[type];
  if (evlistener === undefined)
    return [];

  if (typeof evlistener === 'function')
    return unwrap ? [evlistener.listener || evlistener] : [evlistener];

  return unwrap ?
    unwrapListeners(evlistener) : arrayClone(evlistener, evlistener.length);
}

EventEmitter.prototype.listeners = function listeners(type) {
  return _listeners(this, type, true);
};

EventEmitter.prototype.rawListeners = function rawListeners(type) {
  return _listeners(this, type, false);
};

EventEmitter.listenerCount = function(emitter, type) {
  if (typeof emitter.listenerCount === 'function') {
    return emitter.listenerCount(type);
  } else {
    return listenerCount.call(emitter, type);
  }
};

EventEmitter.prototype.listenerCount = listenerCount;
function listenerCount(type) {
  var events = this._events;

  if (events !== undefined) {
    var evlistener = events[type];

    if (typeof evlistener === 'function') {
      return 1;
    } else if (evlistener !== undefined) {
      return evlistener.length;
    }
  }

  return 0;
}

EventEmitter.prototype.eventNames = function eventNames() {
  return this._eventsCount > 0 ? ReflectOwnKeys(this._events) : [];
};

function arrayClone(arr, n) {
  var copy = new Array(n);
  for (var i = 0; i < n; ++i)
    copy[i] = arr[i];
  return copy;
}

function spliceOne(list, index) {
  for (; index + 1 < list.length; index++)
    list[index] = list[index + 1];
  list.pop();
}

function unwrapListeners(arr) {
  var ret = new Array(arr.length);
  for (var i = 0; i < ret.length; ++i) {
    ret[i] = arr[i].listener || arr[i];
  }
  return ret;
}


/***/ }),
/* 54 */
/***/ (function(module, exports) {

module.exports = {};

/***/ }),
/* 55 */
/***/ (function(module, exports, __webpack_require__) {

var has          = __webpack_require__(27)
  , toIObject    = __webpack_require__(22)
  , arrayIndexOf = __webpack_require__(58)(false)
  , IE_PROTO     = __webpack_require__(47)('IE_PROTO');

module.exports = function(object, names){
  var O      = toIObject(object)
    , i      = 0
    , result = []
    , key;
  for(key in O)if(key != IE_PROTO)has(O, key) && result.push(key);
  // Don't enum bug & hidden keys
  while(names.length > i)if(has(O, key = names[i++])){
    ~arrayIndexOf(result, key) || result.push(key);
  }
  return result;
};

/***/ }),
/* 56 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.15 ToLength
var toInteger = __webpack_require__(39)
  , min       = Math.min;
module.exports = function(it){
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};

/***/ }),
/* 57 */
/***/ (function(module, exports) {

exports.f = Object.getOwnPropertySymbols;

/***/ }),
/* 58 */
/***/ (function(module, exports, __webpack_require__) {

// false -> Array#indexOf
// true  -> Array#includes
var toIObject = __webpack_require__(22)
  , toLength  = __webpack_require__(56)
  , toIndex   = __webpack_require__(59);
module.exports = function(IS_INCLUDES){
  return function($this, el, fromIndex){
    var O      = toIObject($this)
      , length = toLength(O.length)
      , index  = toIndex(fromIndex, length)
      , value;
    // Array#includes uses SameValueZero equality algorithm
    if(IS_INCLUDES && el != el)while(length > index){
      value = O[index++];
      if(value != value)return true;
    // Array#toIndex ignores holes, Array#includes - not
    } else for(;length > index; index++)if(IS_INCLUDES || index in O){
      if(O[index] === el)return IS_INCLUDES || index || 0;
    } return !IS_INCLUDES && -1;
  };
};

/***/ }),
/* 59 */
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__(39)
  , max       = Math.max
  , min       = Math.min;
module.exports = function(index, length){
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};

/***/ }),
/* 60 */,
/* 61 */,
/* 62 */
/***/ (function(module, exports, __webpack_require__) {

var def = __webpack_require__(6).f
  , has = __webpack_require__(27)
  , TAG = __webpack_require__(29)('toStringTag');

module.exports = function(it, tag, stat){
  if(it && !has(it = stat ? it : it.prototype, TAG))def(it, TAG, {configurable: true, value: tag});
};

/***/ }),
/* 63 */
/***/ (function(module, exports) {

module.exports = true;

/***/ }),
/* 64 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $at  = __webpack_require__(102)(true);

// 21.1.3.27 String.prototype[@@iterator]()
__webpack_require__(76)(String, 'String', function(iterated){
  this._t = String(iterated); // target
  this._i = 0;                // next index
// 21.1.5.2.1 %StringIteratorPrototype%.next()
}, function(){
  var O     = this._t
    , index = this._i
    , point;
  if(index >= O.length)return {value: undefined, done: true};
  point = $at(O, index);
  this._i += point.length;
  return {value: point, done: false};
});

/***/ }),
/* 65 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _assign = __webpack_require__(82);

var _assign2 = _interopRequireDefault(_assign);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _fosRouting = __webpack_require__(179);

var _fosRouting2 = _interopRequireDefault(_fosRouting);

var _fos_js_routes = __webpack_require__(162);

var _fos_js_routes2 = _interopRequireDefault(_fos_js_routes);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

var $ = window.$;

/**
 * Wraps FOSJsRoutingbundle with exposed routes.
 * To expose route add option `expose: true` in .yml routing config
 *
 * e.g.
 *
 * `my_route
 *    path: /my-path
 *    options:
 *      expose: true
 * `
 * And run `bin/console fos:js-routing:dump --format=json --target=admin-dev/themes/new-theme/js/fos_js_routes.json`
 */

var Router = function () {
  function Router() {
    (0, _classCallCheck3.default)(this, Router);

    _fosRouting2.default.setData(_fos_js_routes2.default);
    _fosRouting2.default.setBaseUrl($(document).find('body').data('base-url'));

    return this;
  }

  /**
   * Decorated "generate" method, with predefined security token in params
   *
   * @param route
   * @param params
   *
   * @returns {String}
   */


  (0, _createClass3.default)(Router, [{
    key: 'generate',
    value: function generate(route) {
      var params = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

      var tokenizedParams = (0, _assign2.default)(params, { _token: $(document).find('body').data('token') });

      return _fosRouting2.default.generate(route, tokenizedParams);
    }
  }]);
  return Router;
}();

exports.default = Router;

/***/ }),
/* 66 */,
/* 67 */,
/* 68 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(84), __esModule: true };

/***/ }),
/* 69 */
/***/ (function(module, exports, __webpack_require__) {

var global         = __webpack_require__(5)
  , core           = __webpack_require__(3)
  , LIBRARY        = __webpack_require__(63)
  , wksExt         = __webpack_require__(70)
  , defineProperty = __webpack_require__(6).f;
module.exports = function(name){
  var $Symbol = core.Symbol || (core.Symbol = LIBRARY ? {} : global.Symbol || {});
  if(name.charAt(0) != '_' && !(name in $Symbol))defineProperty($Symbol, name, {value: wksExt.f(name)});
};

/***/ }),
/* 70 */
/***/ (function(module, exports, __webpack_require__) {

exports.f = __webpack_require__(29);

/***/ }),
/* 71 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
var anObject    = __webpack_require__(11)
  , dPs         = __webpack_require__(101)
  , enumBugKeys = __webpack_require__(49)
  , IE_PROTO    = __webpack_require__(47)('IE_PROTO')
  , Empty       = function(){ /* empty */ }
  , PROTOTYPE   = 'prototype';

// Create object with fake `null` prototype: use iframe Object with cleared prototype
var createDict = function(){
  // Thrash, waste and sodomy: IE GC bug
  var iframe = __webpack_require__(16)('iframe')
    , i      = enumBugKeys.length
    , lt     = '<'
    , gt     = '>'
    , iframeDocument;
  iframe.style.display = 'none';
  __webpack_require__(95).appendChild(iframe);
  iframe.src = 'javascript:'; // eslint-disable-line no-script-url
  // createDict = iframe.contentWindow.Object;
  // html.removeChild(iframe);
  iframeDocument = iframe.contentWindow.document;
  iframeDocument.open();
  iframeDocument.write(lt + 'script' + gt + 'document.F=Object' + lt + '/script' + gt);
  iframeDocument.close();
  createDict = iframeDocument.F;
  while(i--)delete createDict[PROTOTYPE][enumBugKeys[i]];
  return createDict();
};

module.exports = Object.create || function create(O, Properties){
  var result;
  if(O !== null){
    Empty[PROTOTYPE] = anObject(O);
    result = new Empty;
    Empty[PROTOTYPE] = null;
    // add "__proto__" for Object.getPrototypeOf polyfill
    result[IE_PROTO] = O;
  } else result = createDict();
  return Properties === undefined ? result : dPs(result, Properties);
};


/***/ }),
/* 72 */,
/* 73 */,
/* 74 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(104);
var global        = __webpack_require__(5)
  , hide          = __webpack_require__(10)
  , Iterators     = __webpack_require__(54)
  , TO_STRING_TAG = __webpack_require__(29)('toStringTag');

for(var collections = ['NodeList', 'DOMTokenList', 'MediaList', 'StyleSheetList', 'CSSRuleList'], i = 0; i < 5; i++){
  var NAME       = collections[i]
    , Collection = global[NAME]
    , proto      = Collection && Collection.prototype;
  if(proto && !proto[TO_STRING_TAG])hide(proto, TO_STRING_TAG, NAME);
  Iterators[NAME] = Iterators.Array;
}

/***/ }),
/* 75 */,
/* 76 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var LIBRARY        = __webpack_require__(63)
  , $export        = __webpack_require__(8)
  , redefine       = __webpack_require__(81)
  , hide           = __webpack_require__(10)
  , has            = __webpack_require__(27)
  , Iterators      = __webpack_require__(54)
  , $iterCreate    = __webpack_require__(99)
  , setToStringTag = __webpack_require__(62)
  , getPrototypeOf = __webpack_require__(90)
  , ITERATOR       = __webpack_require__(29)('iterator')
  , BUGGY          = !([].keys && 'next' in [].keys()) // Safari has buggy iterators w/o `next`
  , FF_ITERATOR    = '@@iterator'
  , KEYS           = 'keys'
  , VALUES         = 'values';

var returnThis = function(){ return this; };

module.exports = function(Base, NAME, Constructor, next, DEFAULT, IS_SET, FORCED){
  $iterCreate(Constructor, NAME, next);
  var getMethod = function(kind){
    if(!BUGGY && kind in proto)return proto[kind];
    switch(kind){
      case KEYS: return function keys(){ return new Constructor(this, kind); };
      case VALUES: return function values(){ return new Constructor(this, kind); };
    } return function entries(){ return new Constructor(this, kind); };
  };
  var TAG        = NAME + ' Iterator'
    , DEF_VALUES = DEFAULT == VALUES
    , VALUES_BUG = false
    , proto      = Base.prototype
    , $native    = proto[ITERATOR] || proto[FF_ITERATOR] || DEFAULT && proto[DEFAULT]
    , $default   = $native || getMethod(DEFAULT)
    , $entries   = DEFAULT ? !DEF_VALUES ? $default : getMethod('entries') : undefined
    , $anyNative = NAME == 'Array' ? proto.entries || $native : $native
    , methods, key, IteratorPrototype;
  // Fix native
  if($anyNative){
    IteratorPrototype = getPrototypeOf($anyNative.call(new Base));
    if(IteratorPrototype !== Object.prototype){
      // Set @@toStringTag to native iterators
      setToStringTag(IteratorPrototype, TAG, true);
      // fix for some old engines
      if(!LIBRARY && !has(IteratorPrototype, ITERATOR))hide(IteratorPrototype, ITERATOR, returnThis);
    }
  }
  // fix Array#{values, @@iterator}.name in V8 / FF
  if(DEF_VALUES && $native && $native.name !== VALUES){
    VALUES_BUG = true;
    $default = function values(){ return $native.call(this); };
  }
  // Define iterator
  if((!LIBRARY || FORCED) && (BUGGY || VALUES_BUG || !proto[ITERATOR])){
    hide(proto, ITERATOR, $default);
  }
  // Plug for library
  Iterators[NAME] = $default;
  Iterators[TAG]  = returnThis;
  if(DEFAULT){
    methods = {
      values:  DEF_VALUES ? $default : getMethod(VALUES),
      keys:    IS_SET     ? $default : getMethod(KEYS),
      entries: $entries
    };
    if(FORCED)for(key in methods){
      if(!(key in proto))redefine(proto, key, methods[key]);
    } else $export($export.P + $export.F * (BUGGY || VALUES_BUG), NAME, methods);
  }
  return methods;
};

/***/ }),
/* 77 */
/***/ (function(module, exports, __webpack_require__) {

// most Object methods by ES6 should accept primitives
var $export = __webpack_require__(8)
  , core    = __webpack_require__(3)
  , fails   = __webpack_require__(7);
module.exports = function(KEY, exec){
  var fn  = (core.Object || {})[KEY] || Object[KEY]
    , exp = {};
  exp[KEY] = exec(fn);
  $export($export.S + $export.F * fails(function(){ fn(1); }), 'Object', exp);
};

/***/ }),
/* 78 */,
/* 79 */,
/* 80 */,
/* 81 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(10);

/***/ }),
/* 82 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(83), __esModule: true };

/***/ }),
/* 83 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(87);
module.exports = __webpack_require__(3).Object.assign;

/***/ }),
/* 84 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(88);
module.exports = __webpack_require__(3).Object.keys;

/***/ }),
/* 85 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 19.1.2.1 Object.assign(target, source, ...)
var getKeys  = __webpack_require__(34)
  , gOPS     = __webpack_require__(57)
  , pIE      = __webpack_require__(52)
  , toObject = __webpack_require__(46)
  , IObject  = __webpack_require__(51)
  , $assign  = Object.assign;

// should work with symbols and should have deterministic property order (V8 bug)
module.exports = !$assign || __webpack_require__(7)(function(){
  var A = {}
    , B = {}
    , S = Symbol()
    , K = 'abcdefghijklmnopqrst';
  A[S] = 7;
  K.split('').forEach(function(k){ B[k] = k; });
  return $assign({}, A)[S] != 7 || Object.keys($assign({}, B)).join('') != K;
}) ? function assign(target, source){ // eslint-disable-line no-unused-vars
  var T     = toObject(target)
    , aLen  = arguments.length
    , index = 1
    , getSymbols = gOPS.f
    , isEnum     = pIE.f;
  while(aLen > index){
    var S      = IObject(arguments[index++])
      , keys   = getSymbols ? getKeys(S).concat(getSymbols(S)) : getKeys(S)
      , length = keys.length
      , j      = 0
      , key;
    while(length > j)if(isEnum.call(S, key = keys[j++]))T[key] = S[key];
  } return T;
} : $assign;

/***/ }),
/* 86 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.7 / 15.2.3.4 Object.getOwnPropertyNames(O)
var $keys      = __webpack_require__(55)
  , hiddenKeys = __webpack_require__(49).concat('length', 'prototype');

exports.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O){
  return $keys(O, hiddenKeys);
};

/***/ }),
/* 87 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.3.1 Object.assign(target, source)
var $export = __webpack_require__(8);

$export($export.S + $export.F, 'Object', {assign: __webpack_require__(85)});

/***/ }),
/* 88 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 Object.keys(O)
var toObject = __webpack_require__(46)
  , $keys    = __webpack_require__(34);

__webpack_require__(77)('keys', function(){
  return function keys(it){
    return $keys(toObject(it));
  };
});

/***/ }),
/* 89 */,
/* 90 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.9 / 15.2.3.2 Object.getPrototypeOf(O)
var has         = __webpack_require__(27)
  , toObject    = __webpack_require__(46)
  , IE_PROTO    = __webpack_require__(47)('IE_PROTO')
  , ObjectProto = Object.prototype;

module.exports = Object.getPrototypeOf || function(O){
  O = toObject(O);
  if(has(O, IE_PROTO))return O[IE_PROTO];
  if(typeof O.constructor == 'function' && O instanceof O.constructor){
    return O.constructor.prototype;
  } return O instanceof Object ? ObjectProto : null;
};

/***/ }),
/* 91 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
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

exports.default = {
  mainDiv: '#order-view-page',
  orderPaymentDetailsBtn: '.js-payment-details-btn',
  orderPaymentFormAmountInput: '#order_payment_amount',
  orderPaymentInvoiceSelect: '#order_payment_id_invoice',
  viewOrderPaymentsBlock: '#view_order_payments_block',
  privateNoteToggleBtn: '.js-private-note-toggle-btn',
  privateNoteBlock: '.js-private-note-block',
  privateNoteInput: '#private_note_note',
  privateNoteSubmitBtn: '.js-private-note-btn',
  addCartRuleModal: '#addOrderDiscountModal',
  addCartRuleInvoiceIdSelect: '#add_order_cart_rule_invoice_id',
  addCartRuleTypeSelect: '#add_order_cart_rule_type',
  addCartRuleValueInput: '#add_order_cart_rule_value',
  addCartRuleValueUnit: '#add_order_cart_rule_value_unit',
  cartRuleHelpText: '.js-cart-rule-value-help',
  updateOrderStatusActionBtn: '#update_order_status_action_btn',
  updateOrderStatusActionInput: '#update_order_status_action_input',
  updateOrderStatusActionInputWrapper: '#update_order_status_action_input_wrapper',
  updateOrderStatusActionForm: '#update_order_status_action_form',
  showOrderShippingUpdateModalBtn: '.js-update-shipping-btn',
  updateOrderShippingTrackingNumberInput: '#update_order_shipping_tracking_number',
  updateOrderShippingCurrentOrderCarrierIdInput: '#update_order_shipping_current_order_carrier_id',
  updateCustomerAddressModal: '#updateCustomerAddressModal',
  openOrderAddressUpdateModalBtn: '.js-update-customer-address-modal-btn',
  updateOrderAddressTypeInput: '#change_order_address_address_type',
  deliveryAddressEditBtn: '#js-delivery-address-edit-btn',
  invoiceAddressEditBtn: '#js-invoice-address-edit-btn',
  orderMessageNameSelect: '#order_message_order_message',
  orderMessagesContainer: '.js-order-messages-container',
  orderMessage: '#order_message_message',
  orderMessageChangeWarning: '.js-message-change-warning',
  orderDocumentsTabCount: '#orderDocumentsTab .count',
  orderDocumentsTabBody: '#orderDocumentsTabContent .card-body',
  allMessagesModal: '#view_all_messages_modal',
  allMessagesList: '#all-messages-list',
  openAllMessagesBtn: '.js-open-all-messages-btn',
  // Products table elements
  productOriginalPosition: '#orderProductsOriginalPosition',
  productModificationPosition: '#orderProductsModificationPosition',
  productsPanel: '#orderProductsPanel',
  productsCount: '#orderProductsPanelCount',
  productDeleteBtn: '.js-order-product-delete-btn',
  productsTable: '#orderProductsTable',
  productsPagination: '.order-product-pagination',
  productsNavPagination: '#orderProductsNavPagination',
  productsTablePagination: '#orderProductsTablePagination',
  productsTablePaginationNext: '#orderProductsTablePaginationNext',
  productsTablePaginationPrev: '#orderProductsTablePaginationPrev',
  productsTablePaginationLink: '.page-item:not(.d-none):not(#orderProductsTablePaginationNext):not(#orderProductsTablePaginationPrev) .page-link',
  productsTablePaginationActive: '#orderProductsTablePagination .page-item.active span',
  productsTablePaginationTemplate: '#orderProductsTablePagination .page-item.d-none',
  productsTablePaginationNumberSelector: '#orderProductsTablePaginationNumberSelector',
  productsTableRow: function productsTableRow(productId) {
    return '#orderProduct_' + productId;
  },
  productsTableRowEdited: function productsTableRowEdited(productId) {
    return '#editOrderProduct_' + productId;
  },
  productsTableRows: 'tr.cellProduct',
  productsCellLocation: 'tr .cellProductLocation',
  productsCellRefunded: 'tr .cellProductRefunded',
  productsCellLocationDisplayed: 'tr:not(.d-none) .cellProductLocation',
  productsCellRefundedDisplayed: 'tr:not(.d-none) .cellProductRefunded',
  productsTableCustomizationRows: '#orderProductsTable .order-product-customization',
  productEditButtons: '.js-order-product-edit-btn',
  productEditBtn: function productEditBtn(productId) {
    return '#orderProduct_' + productId + ' .js-order-product-edit-btn';
  },
  productAddBtn: '#addProductBtn',
  productActionBtn: '.js-product-action-btn',
  productAddActionBtn: '#add_product_row_add',
  productCancelAddBtn: '#add_product_row_cancel',
  productAddRow: '#addProductTableRow',
  productSearchInput: '#add_product_row_search',
  productSearchInputAutocomplete: '#addProductTableRow .dropdown',
  productSearchInputAutocompleteMenu: '#addProductTableRow .dropdown .dropdown-menu',
  productAddIdInput: '#add_product_row_product_id',
  productAddTaxRateInput: '#add_product_row_tax_rate',
  productAddCombinationsBlock: '#addProductCombinations',
  productAddCombinationsSelect: '#addProductCombinationId',
  productAddPriceTaxExclInput: '#add_product_row_price_tax_excluded',
  productAddPriceTaxInclInput: '#add_product_row_price_tax_included',
  productAddQuantityInput: '#add_product_row_quantity',
  productAddAvailableText: '#addProductAvailable',
  productAddLocationText: '#addProductLocation',
  productAddTotalPriceText: '#addProductTotalPrice',
  productAddInvoiceSelect: '#add_product_row_invoice',
  productAddFreeShippingSelect: '#add_product_row_free_shipping',
  productAddNewInvoiceInfo: '#addProductNewInvoiceInfo',
  productEditSaveBtn: '.productEditSaveBtn',
  productEditCancelBtn: '.productEditCancelBtn',
  productEditRowTemplate: '#editProductTableRowTemplate',
  productEditRow: '.editProductRow',
  productEditImage: '.cellProductImg',
  productEditName: '.cellProductName',
  productEditUnitPrice: '.cellProductUnitPrice',
  productEditQuantity: '.cellProductQuantity',
  productEditAvailableQuantity: '.cellProductAvailableQuantity',
  productEditTotalPrice: '.cellProductTotalPrice',
  productEditPriceTaxExclInput: '.editProductPriceTaxExcl',
  productEditPriceTaxInclInput: '.editProductPriceTaxIncl',
  productEditInvoiceSelect: '.editProductInvoice',
  productEditQuantityInput: '.editProductQuantity',
  productEditLocationText: '.editProductLocation',
  productEditAvailableText: '.editProductAvailable',
  productEditTotalPriceText: '.editProductTotalPrice',
  // Product Discount List
  productDiscountList: {
    list: '.table.discountList'
  },
  // Product Pack Modal
  productPackModal: {
    modal: '#product-pack-modal',
    table: '#product-pack-modal-table tbody',
    rows: '#product-pack-modal-table tbody tr:not(#template-pack-table-row)',
    template: '#template-pack-table-row',
    product: {
      img: '.cell-product-img img',
      link: '.cell-product-name a',
      name: '.cell-product-name .product-name',
      ref: '.cell-product-name .product-reference',
      supplierRef: '.cell-product-name .product-supplier-reference',
      quantity: '.cell-product-quantity',
      availableQuantity: '.cell-product-available-quantity'
    }
  },
  // Order price elements
  orderProductsTotal: '#orderProductsTotal',
  orderDiscountsTotalContainer: '#order-discounts-total-container',
  orderDiscountsTotal: '#orderDiscountsTotal',
  orderWrappingTotal: '#orderWrappingTotal',
  orderShippingTotal: '#orderShippingTotal',
  orderTaxesTotal: '#orderTaxesTotal',
  orderTotal: '#orderTotal',
  orderHookTabsContainer: '#order_hook_tabs',
  // Product cancel/refund elements
  cancelProduct: {
    form: 'form[name="cancel_product"]',
    buttons: {
      abort: 'button.cancel-product-element-abort',
      save: '#cancel_product_save',
      partialRefund: 'button.partial-refund-display',
      standardRefund: 'button.standard-refund-display',
      returnProduct: 'button.return-product-display',
      cancelProducts: 'button.cancel-product-display'
    },
    inputs: {
      quantity: '.cancel-product-quantity input',
      amount: '.cancel-product-amount input',
      selector: '.cancel-product-selector input'
    },
    table: {
      cell: '.cancel-product-cell',
      header: 'th.cancel-product-element p',
      actions: 'td.cellProductActions, th.product_actions'
    },
    checkboxes: {
      restock: '#cancel_product_restock',
      creditSlip: '#cancel_product_credit_slip',
      voucher: '#cancel_product_voucher'
    },
    radios: {
      voucherRefundType: {
        productPrices: 'input[voucher-refund-type="0"]',
        productPricesVoucherExcluded: 'input[voucher-refund-type="1"]',
        negativeErrorMessage: '.voucher-refund-type-negative-error'
      }
    },
    toggle: {
      partialRefund: '.cancel-product-element:not(.hidden):not(.shipping-refund), .cancel-product-amount',
      standardRefund: '.cancel-product-element:not(.hidden):not(.shipping-refund-amount):not(.restock-products), .cancel-product-selector',
      returnProduct: '.cancel-product-element:not(.hidden):not(.shipping-refund-amount), .cancel-product-selector',
      cancelProducts: '.cancel-product-element:not(.hidden):not(.shipping-refund-amount):not(.shipping-refund):not(.restock-products):not(.refund-credit-slip):not(.refund-voucher):not(.voucher-refund-type), .cancel-product-selector'
    }
  },
  printOrderViewPageButton: '.js-print-order-view-page'
};

/***/ }),
/* 92 */,
/* 93 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _iterator = __webpack_require__(112);

var _iterator2 = _interopRequireDefault(_iterator);

var _symbol = __webpack_require__(111);

var _symbol2 = _interopRequireDefault(_symbol);

var _typeof = typeof _symbol2.default === "function" && typeof _iterator2.default === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof _symbol2.default === "function" && obj.constructor === _symbol2.default && obj !== _symbol2.default.prototype ? "symbol" : typeof obj; };

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = typeof _symbol2.default === "function" && _typeof(_iterator2.default) === "symbol" ? function (obj) {
  return typeof obj === "undefined" ? "undefined" : _typeof(obj);
} : function (obj) {
  return obj && typeof _symbol2.default === "function" && obj.constructor === _symbol2.default && obj !== _symbol2.default.prototype ? "symbol" : typeof obj === "undefined" ? "undefined" : _typeof(obj);
};

/***/ }),
/* 94 */
/***/ (function(module, exports, __webpack_require__) {

// getting tag from 19.1.3.6 Object.prototype.toString()
var cof = __webpack_require__(48)
  , TAG = __webpack_require__(29)('toStringTag')
  // ES3 wrong here
  , ARG = cof(function(){ return arguments; }()) == 'Arguments';

// fallback for IE11 Script Access Denied error
var tryGet = function(it, key){
  try {
    return it[key];
  } catch(e){ /* empty */ }
};

module.exports = function(it){
  var O, T, B;
  return it === undefined ? 'Undefined' : it === null ? 'Null'
    // @@toStringTag case
    : typeof (T = tryGet(O = Object(it), TAG)) == 'string' ? T
    // builtinTag case
    : ARG ? cof(O)
    // ES3 arguments fallback
    : (B = cof(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : B;
};

/***/ }),
/* 95 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(5).document && document.documentElement;

/***/ }),
/* 96 */
/***/ (function(module, exports, __webpack_require__) {

var pIE            = __webpack_require__(52)
  , createDesc     = __webpack_require__(12)
  , toIObject      = __webpack_require__(22)
  , toPrimitive    = __webpack_require__(13)
  , has            = __webpack_require__(27)
  , IE8_DOM_DEFINE = __webpack_require__(17)
  , gOPD           = Object.getOwnPropertyDescriptor;

exports.f = __webpack_require__(2) ? gOPD : function getOwnPropertyDescriptor(O, P){
  O = toIObject(O);
  P = toPrimitive(P, true);
  if(IE8_DOM_DEFINE)try {
    return gOPD(O, P);
  } catch(e){ /* empty */ }
  if(has(O, P))return createDesc(!pIE.f.call(O, P), O[P]);
};

/***/ }),
/* 97 */,
/* 98 */
/***/ (function(module, exports) {

module.exports = function(){ /* empty */ };

/***/ }),
/* 99 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var create         = __webpack_require__(71)
  , descriptor     = __webpack_require__(12)
  , setToStringTag = __webpack_require__(62)
  , IteratorPrototype = {};

// 25.1.2.1.1 %IteratorPrototype%[@@iterator]()
__webpack_require__(10)(IteratorPrototype, __webpack_require__(29)('iterator'), function(){ return this; });

module.exports = function(Constructor, NAME, next){
  Constructor.prototype = create(IteratorPrototype, {next: descriptor(1, next)});
  setToStringTag(Constructor, NAME + ' Iterator');
};

/***/ }),
/* 100 */
/***/ (function(module, exports) {

module.exports = function(done, value){
  return {value: value, done: !!done};
};

/***/ }),
/* 101 */
/***/ (function(module, exports, __webpack_require__) {

var dP       = __webpack_require__(6)
  , anObject = __webpack_require__(11)
  , getKeys  = __webpack_require__(34);

module.exports = __webpack_require__(2) ? Object.defineProperties : function defineProperties(O, Properties){
  anObject(O);
  var keys   = getKeys(Properties)
    , length = keys.length
    , i = 0
    , P;
  while(length > i)dP.f(O, P = keys[i++], Properties[P]);
  return O;
};

/***/ }),
/* 102 */
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__(39)
  , defined   = __webpack_require__(38);
// true  -> String#at
// false -> String#codePointAt
module.exports = function(TO_STRING){
  return function(that, pos){
    var s = String(defined(that))
      , i = toInteger(pos)
      , l = s.length
      , a, b;
    if(i < 0 || i >= l)return TO_STRING ? '' : undefined;
    a = s.charCodeAt(i);
    return a < 0xd800 || a > 0xdbff || i + 1 === l || (b = s.charCodeAt(i + 1)) < 0xdc00 || b > 0xdfff
      ? TO_STRING ? s.charAt(i) : a
      : TO_STRING ? s.slice(i, i + 2) : (a - 0xd800 << 10) + (b - 0xdc00) + 0x10000;
  };
};

/***/ }),
/* 103 */
/***/ (function(module, exports, __webpack_require__) {

var classof   = __webpack_require__(94)
  , ITERATOR  = __webpack_require__(29)('iterator')
  , Iterators = __webpack_require__(54);
module.exports = __webpack_require__(3).getIteratorMethod = function(it){
  if(it != undefined)return it[ITERATOR]
    || it['@@iterator']
    || Iterators[classof(it)];
};

/***/ }),
/* 104 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var addToUnscopables = __webpack_require__(98)
  , step             = __webpack_require__(100)
  , Iterators        = __webpack_require__(54)
  , toIObject        = __webpack_require__(22);

// 22.1.3.4 Array.prototype.entries()
// 22.1.3.13 Array.prototype.keys()
// 22.1.3.29 Array.prototype.values()
// 22.1.3.30 Array.prototype[@@iterator]()
module.exports = __webpack_require__(76)(Array, 'Array', function(iterated, kind){
  this._t = toIObject(iterated); // target
  this._i = 0;                   // next index
  this._k = kind;                // kind
// 22.1.5.2.1 %ArrayIteratorPrototype%.next()
}, function(){
  var O     = this._t
    , kind  = this._k
    , index = this._i++;
  if(!O || index >= O.length){
    this._t = undefined;
    return step(1);
  }
  if(kind == 'keys'  )return step(0, index);
  if(kind == 'values')return step(0, O[index]);
  return step(0, [index, O[index]]);
}, 'values');

// argumentsList[@@iterator] is %ArrayProto_values% (9.4.4.6, 9.4.4.7)
Iterators.Arguments = Iterators.Array;

addToUnscopables('keys');
addToUnscopables('values');
addToUnscopables('entries');

/***/ }),
/* 105 */,
/* 106 */
/***/ (function(module, exports) {



/***/ }),
/* 107 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _localization = __webpack_require__(109);

var _localization2 = _interopRequireDefault(_localization);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
    (0, _classCallCheck3.default)(this, NumberSymbol);

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


  (0, _createClass3.default)(NumberSymbol, [{
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
}(); /**
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


exports.default = NumberSymbol;

/***/ }),
/* 108 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _localization = __webpack_require__(109);

var _localization2 = _interopRequireDefault(_localization);

var _numberSymbol = __webpack_require__(107);

var _numberSymbol2 = _interopRequireDefault(_numberSymbol);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
    (0, _classCallCheck3.default)(this, NumberSpecification);

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


  (0, _createClass3.default)(NumberSpecification, [{
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
/* 109 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
var LocalizationException = function LocalizationException(message) {
  (0, _classCallCheck3.default)(this, LocalizationException);

  this.message = message;
  this.name = 'LocalizationException';
};

exports.default = LocalizationException;

/***/ }),
/* 110 */,
/* 111 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(115), __esModule: true };

/***/ }),
/* 112 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(116), __esModule: true };

/***/ }),
/* 113 */,
/* 114 */,
/* 115 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(122);
__webpack_require__(106);
__webpack_require__(123);
__webpack_require__(124);
module.exports = __webpack_require__(3).Symbol;

/***/ }),
/* 116 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(64);
__webpack_require__(74);
module.exports = __webpack_require__(70).f('iterator');

/***/ }),
/* 117 */
/***/ (function(module, exports, __webpack_require__) {

// all enumerable object keys, includes symbols
var getKeys = __webpack_require__(34)
  , gOPS    = __webpack_require__(57)
  , pIE     = __webpack_require__(52);
module.exports = function(it){
  var result     = getKeys(it)
    , getSymbols = gOPS.f;
  if(getSymbols){
    var symbols = getSymbols(it)
      , isEnum  = pIE.f
      , i       = 0
      , key;
    while(symbols.length > i)if(isEnum.call(it, key = symbols[i++]))result.push(key);
  } return result;
};

/***/ }),
/* 118 */
/***/ (function(module, exports, __webpack_require__) {

// 7.2.2 IsArray(argument)
var cof = __webpack_require__(48);
module.exports = Array.isArray || function isArray(arg){
  return cof(arg) == 'Array';
};

/***/ }),
/* 119 */
/***/ (function(module, exports, __webpack_require__) {

var getKeys   = __webpack_require__(34)
  , toIObject = __webpack_require__(22);
module.exports = function(object, el){
  var O      = toIObject(object)
    , keys   = getKeys(O)
    , length = keys.length
    , index  = 0
    , key;
  while(length > index)if(O[key = keys[index++]] === el)return key;
};

/***/ }),
/* 120 */
/***/ (function(module, exports, __webpack_require__) {

var META     = __webpack_require__(43)('meta')
  , isObject = __webpack_require__(4)
  , has      = __webpack_require__(27)
  , setDesc  = __webpack_require__(6).f
  , id       = 0;
var isExtensible = Object.isExtensible || function(){
  return true;
};
var FREEZE = !__webpack_require__(7)(function(){
  return isExtensible(Object.preventExtensions({}));
});
var setMeta = function(it){
  setDesc(it, META, {value: {
    i: 'O' + ++id, // object ID
    w: {}          // weak collections IDs
  }});
};
var fastKey = function(it, create){
  // return primitive with prefix
  if(!isObject(it))return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
  if(!has(it, META)){
    // can't set metadata to uncaught frozen object
    if(!isExtensible(it))return 'F';
    // not necessary to add metadata
    if(!create)return 'E';
    // add missing metadata
    setMeta(it);
  // return object ID
  } return it[META].i;
};
var getWeak = function(it, create){
  if(!has(it, META)){
    // can't set metadata to uncaught frozen object
    if(!isExtensible(it))return true;
    // not necessary to add metadata
    if(!create)return false;
    // add missing metadata
    setMeta(it);
  // return hash weak collections IDs
  } return it[META].w;
};
// add metadata on freeze-family methods calling
var onFreeze = function(it){
  if(FREEZE && meta.NEED && isExtensible(it) && !has(it, META))setMeta(it);
  return it;
};
var meta = module.exports = {
  KEY:      META,
  NEED:     false,
  fastKey:  fastKey,
  getWeak:  getWeak,
  onFreeze: onFreeze
};

/***/ }),
/* 121 */
/***/ (function(module, exports, __webpack_require__) {

// fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
var toIObject = __webpack_require__(22)
  , gOPN      = __webpack_require__(86).f
  , toString  = {}.toString;

var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
  ? Object.getOwnPropertyNames(window) : [];

var getWindowNames = function(it){
  try {
    return gOPN(it);
  } catch(e){
    return windowNames.slice();
  }
};

module.exports.f = function getOwnPropertyNames(it){
  return windowNames && toString.call(it) == '[object Window]' ? getWindowNames(it) : gOPN(toIObject(it));
};


/***/ }),
/* 122 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// ECMAScript 6 symbols shim
var global         = __webpack_require__(5)
  , has            = __webpack_require__(27)
  , DESCRIPTORS    = __webpack_require__(2)
  , $export        = __webpack_require__(8)
  , redefine       = __webpack_require__(81)
  , META           = __webpack_require__(120).KEY
  , $fails         = __webpack_require__(7)
  , shared         = __webpack_require__(50)
  , setToStringTag = __webpack_require__(62)
  , uid            = __webpack_require__(43)
  , wks            = __webpack_require__(29)
  , wksExt         = __webpack_require__(70)
  , wksDefine      = __webpack_require__(69)
  , keyOf          = __webpack_require__(119)
  , enumKeys       = __webpack_require__(117)
  , isArray        = __webpack_require__(118)
  , anObject       = __webpack_require__(11)
  , toIObject      = __webpack_require__(22)
  , toPrimitive    = __webpack_require__(13)
  , createDesc     = __webpack_require__(12)
  , _create        = __webpack_require__(71)
  , gOPNExt        = __webpack_require__(121)
  , $GOPD          = __webpack_require__(96)
  , $DP            = __webpack_require__(6)
  , $keys          = __webpack_require__(34)
  , gOPD           = $GOPD.f
  , dP             = $DP.f
  , gOPN           = gOPNExt.f
  , $Symbol        = global.Symbol
  , $JSON          = global.JSON
  , _stringify     = $JSON && $JSON.stringify
  , PROTOTYPE      = 'prototype'
  , HIDDEN         = wks('_hidden')
  , TO_PRIMITIVE   = wks('toPrimitive')
  , isEnum         = {}.propertyIsEnumerable
  , SymbolRegistry = shared('symbol-registry')
  , AllSymbols     = shared('symbols')
  , OPSymbols      = shared('op-symbols')
  , ObjectProto    = Object[PROTOTYPE]
  , USE_NATIVE     = typeof $Symbol == 'function'
  , QObject        = global.QObject;
// Don't use setters in Qt Script, https://github.com/zloirock/core-js/issues/173
var setter = !QObject || !QObject[PROTOTYPE] || !QObject[PROTOTYPE].findChild;

// fallback for old Android, https://code.google.com/p/v8/issues/detail?id=687
var setSymbolDesc = DESCRIPTORS && $fails(function(){
  return _create(dP({}, 'a', {
    get: function(){ return dP(this, 'a', {value: 7}).a; }
  })).a != 7;
}) ? function(it, key, D){
  var protoDesc = gOPD(ObjectProto, key);
  if(protoDesc)delete ObjectProto[key];
  dP(it, key, D);
  if(protoDesc && it !== ObjectProto)dP(ObjectProto, key, protoDesc);
} : dP;

var wrap = function(tag){
  var sym = AllSymbols[tag] = _create($Symbol[PROTOTYPE]);
  sym._k = tag;
  return sym;
};

var isSymbol = USE_NATIVE && typeof $Symbol.iterator == 'symbol' ? function(it){
  return typeof it == 'symbol';
} : function(it){
  return it instanceof $Symbol;
};

var $defineProperty = function defineProperty(it, key, D){
  if(it === ObjectProto)$defineProperty(OPSymbols, key, D);
  anObject(it);
  key = toPrimitive(key, true);
  anObject(D);
  if(has(AllSymbols, key)){
    if(!D.enumerable){
      if(!has(it, HIDDEN))dP(it, HIDDEN, createDesc(1, {}));
      it[HIDDEN][key] = true;
    } else {
      if(has(it, HIDDEN) && it[HIDDEN][key])it[HIDDEN][key] = false;
      D = _create(D, {enumerable: createDesc(0, false)});
    } return setSymbolDesc(it, key, D);
  } return dP(it, key, D);
};
var $defineProperties = function defineProperties(it, P){
  anObject(it);
  var keys = enumKeys(P = toIObject(P))
    , i    = 0
    , l = keys.length
    , key;
  while(l > i)$defineProperty(it, key = keys[i++], P[key]);
  return it;
};
var $create = function create(it, P){
  return P === undefined ? _create(it) : $defineProperties(_create(it), P);
};
var $propertyIsEnumerable = function propertyIsEnumerable(key){
  var E = isEnum.call(this, key = toPrimitive(key, true));
  if(this === ObjectProto && has(AllSymbols, key) && !has(OPSymbols, key))return false;
  return E || !has(this, key) || !has(AllSymbols, key) || has(this, HIDDEN) && this[HIDDEN][key] ? E : true;
};
var $getOwnPropertyDescriptor = function getOwnPropertyDescriptor(it, key){
  it  = toIObject(it);
  key = toPrimitive(key, true);
  if(it === ObjectProto && has(AllSymbols, key) && !has(OPSymbols, key))return;
  var D = gOPD(it, key);
  if(D && has(AllSymbols, key) && !(has(it, HIDDEN) && it[HIDDEN][key]))D.enumerable = true;
  return D;
};
var $getOwnPropertyNames = function getOwnPropertyNames(it){
  var names  = gOPN(toIObject(it))
    , result = []
    , i      = 0
    , key;
  while(names.length > i){
    if(!has(AllSymbols, key = names[i++]) && key != HIDDEN && key != META)result.push(key);
  } return result;
};
var $getOwnPropertySymbols = function getOwnPropertySymbols(it){
  var IS_OP  = it === ObjectProto
    , names  = gOPN(IS_OP ? OPSymbols : toIObject(it))
    , result = []
    , i      = 0
    , key;
  while(names.length > i){
    if(has(AllSymbols, key = names[i++]) && (IS_OP ? has(ObjectProto, key) : true))result.push(AllSymbols[key]);
  } return result;
};

// 19.4.1.1 Symbol([description])
if(!USE_NATIVE){
  $Symbol = function Symbol(){
    if(this instanceof $Symbol)throw TypeError('Symbol is not a constructor!');
    var tag = uid(arguments.length > 0 ? arguments[0] : undefined);
    var $set = function(value){
      if(this === ObjectProto)$set.call(OPSymbols, value);
      if(has(this, HIDDEN) && has(this[HIDDEN], tag))this[HIDDEN][tag] = false;
      setSymbolDesc(this, tag, createDesc(1, value));
    };
    if(DESCRIPTORS && setter)setSymbolDesc(ObjectProto, tag, {configurable: true, set: $set});
    return wrap(tag);
  };
  redefine($Symbol[PROTOTYPE], 'toString', function toString(){
    return this._k;
  });

  $GOPD.f = $getOwnPropertyDescriptor;
  $DP.f   = $defineProperty;
  __webpack_require__(86).f = gOPNExt.f = $getOwnPropertyNames;
  __webpack_require__(52).f  = $propertyIsEnumerable;
  __webpack_require__(57).f = $getOwnPropertySymbols;

  if(DESCRIPTORS && !__webpack_require__(63)){
    redefine(ObjectProto, 'propertyIsEnumerable', $propertyIsEnumerable, true);
  }

  wksExt.f = function(name){
    return wrap(wks(name));
  }
}

$export($export.G + $export.W + $export.F * !USE_NATIVE, {Symbol: $Symbol});

for(var symbols = (
  // 19.4.2.2, 19.4.2.3, 19.4.2.4, 19.4.2.6, 19.4.2.8, 19.4.2.9, 19.4.2.10, 19.4.2.11, 19.4.2.12, 19.4.2.13, 19.4.2.14
  'hasInstance,isConcatSpreadable,iterator,match,replace,search,species,split,toPrimitive,toStringTag,unscopables'
).split(','), i = 0; symbols.length > i; )wks(symbols[i++]);

for(var symbols = $keys(wks.store), i = 0; symbols.length > i; )wksDefine(symbols[i++]);

$export($export.S + $export.F * !USE_NATIVE, 'Symbol', {
  // 19.4.2.1 Symbol.for(key)
  'for': function(key){
    return has(SymbolRegistry, key += '')
      ? SymbolRegistry[key]
      : SymbolRegistry[key] = $Symbol(key);
  },
  // 19.4.2.5 Symbol.keyFor(sym)
  keyFor: function keyFor(key){
    if(isSymbol(key))return keyOf(SymbolRegistry, key);
    throw TypeError(key + ' is not a symbol!');
  },
  useSetter: function(){ setter = true; },
  useSimple: function(){ setter = false; }
});

$export($export.S + $export.F * !USE_NATIVE, 'Object', {
  // 19.1.2.2 Object.create(O [, Properties])
  create: $create,
  // 19.1.2.4 Object.defineProperty(O, P, Attributes)
  defineProperty: $defineProperty,
  // 19.1.2.3 Object.defineProperties(O, Properties)
  defineProperties: $defineProperties,
  // 19.1.2.6 Object.getOwnPropertyDescriptor(O, P)
  getOwnPropertyDescriptor: $getOwnPropertyDescriptor,
  // 19.1.2.7 Object.getOwnPropertyNames(O)
  getOwnPropertyNames: $getOwnPropertyNames,
  // 19.1.2.8 Object.getOwnPropertySymbols(O)
  getOwnPropertySymbols: $getOwnPropertySymbols
});

// 24.3.2 JSON.stringify(value [, replacer [, space]])
$JSON && $export($export.S + $export.F * (!USE_NATIVE || $fails(function(){
  var S = $Symbol();
  // MS Edge converts symbol values to JSON as {}
  // WebKit converts symbol values to JSON as null
  // V8 throws on boxed symbols
  return _stringify([S]) != '[null]' || _stringify({a: S}) != '{}' || _stringify(Object(S)) != '{}';
})), 'JSON', {
  stringify: function stringify(it){
    if(it === undefined || isSymbol(it))return; // IE8 returns string on undefined
    var args = [it]
      , i    = 1
      , replacer, $replacer;
    while(arguments.length > i)args.push(arguments[i++]);
    replacer = args[1];
    if(typeof replacer == 'function')$replacer = replacer;
    if($replacer || !isArray(replacer))replacer = function(key, value){
      if($replacer)value = $replacer.call(this, key, value);
      if(!isSymbol(value))return value;
    };
    args[1] = replacer;
    return _stringify.apply($JSON, args);
  }
});

// 19.4.3.4 Symbol.prototype[@@toPrimitive](hint)
$Symbol[PROTOTYPE][TO_PRIMITIVE] || __webpack_require__(10)($Symbol[PROTOTYPE], TO_PRIMITIVE, $Symbol[PROTOTYPE].valueOf);
// 19.4.3.5 Symbol.prototype[@@toStringTag]
setToStringTag($Symbol, 'Symbol');
// 20.2.1.9 Math[@@toStringTag]
setToStringTag(Math, 'Math', true);
// 24.3.3 JSON[@@toStringTag]
setToStringTag(global.JSON, 'JSON', true);

/***/ }),
/* 123 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(69)('asyncIterator');

/***/ }),
/* 124 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(69)('observable');

/***/ }),
/* 125 */,
/* 126 */,
/* 127 */,
/* 128 */,
/* 129 */,
/* 130 */,
/* 131 */,
/* 132 */,
/* 133 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _getPrototypeOf = __webpack_require__(166);

var _getPrototypeOf2 = _interopRequireDefault(_getPrototypeOf);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _possibleConstructorReturn2 = __webpack_require__(170);

var _possibleConstructorReturn3 = _interopRequireDefault(_possibleConstructorReturn2);

var _inherits2 = __webpack_require__(169);

var _inherits3 = _interopRequireDefault(_inherits2);

var _localization = __webpack_require__(109);

var _localization2 = _interopRequireDefault(_localization);

var _number = __webpack_require__(108);

var _number2 = _interopRequireDefault(_number);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Currency display option: symbol notation.
 */
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
var CURRENCY_DISPLAY_SYMBOL = 'symbol';

var PriceSpecification = function (_NumberSpecification) {
  (0, _inherits3.default)(PriceSpecification, _NumberSpecification);

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
    (0, _classCallCheck3.default)(this, PriceSpecification);

    var _this = (0, _possibleConstructorReturn3.default)(this, (PriceSpecification.__proto__ || (0, _getPrototypeOf2.default)(PriceSpecification)).call(this, positivePattern, negativePattern, symbol, maxFractionDigits, minFractionDigits, groupingUsed, primaryGroupSize, secondaryGroupSize));

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


  (0, _createClass3.default)(PriceSpecification, [{
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

/***/ }),
/* 134 */,
/* 135 */
/***/ (function(module, exports, __webpack_require__) {

// check on default Array iterator
var Iterators  = __webpack_require__(54)
  , ITERATOR   = __webpack_require__(29)('iterator')
  , ArrayProto = Array.prototype;

module.exports = function(it){
  return it !== undefined && (Iterators.Array === it || ArrayProto[ITERATOR] === it);
};

/***/ }),
/* 136 */
/***/ (function(module, exports, __webpack_require__) {

// call something on iterator step with safe closing on error
var anObject = __webpack_require__(11);
module.exports = function(iterator, fn, value, entries){
  try {
    return entries ? fn(anObject(value)[0], value[1]) : fn(value);
  // 7.4.6 IteratorClose(iterator, completion)
  } catch(e){
    var ret = iterator['return'];
    if(ret !== undefined)anObject(ret.call(iterator));
    throw e;
  }
};

/***/ }),
/* 137 */
/***/ (function(module, exports, __webpack_require__) {

var ITERATOR     = __webpack_require__(29)('iterator')
  , SAFE_CLOSING = false;

try {
  var riter = [7][ITERATOR]();
  riter['return'] = function(){ SAFE_CLOSING = true; };
  Array.from(riter, function(){ throw 2; });
} catch(e){ /* empty */ }

module.exports = function(exec, skipClosing){
  if(!skipClosing && !SAFE_CLOSING)return false;
  var safe = false;
  try {
    var arr  = [7]
      , iter = arr[ITERATOR]();
    iter.next = function(){ return {done: safe = true}; };
    arr[ITERATOR] = function(){ return iter; };
    exec(arr);
  } catch(e){ /* empty */ }
  return safe;
};

/***/ }),
/* 138 */,
/* 139 */,
/* 140 */,
/* 141 */,
/* 142 */,
/* 143 */,
/* 144 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(148), __esModule: true };

/***/ }),
/* 145 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(149), __esModule: true };

/***/ }),
/* 146 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(150), __esModule: true };

/***/ }),
/* 147 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _isIterable2 = __webpack_require__(146);

var _isIterable3 = _interopRequireDefault(_isIterable2);

var _getIterator2 = __webpack_require__(145);

var _getIterator3 = _interopRequireDefault(_getIterator2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function () {
  function sliceIterator(arr, i) {
    var _arr = [];
    var _n = true;
    var _d = false;
    var _e = undefined;

    try {
      for (var _i = (0, _getIterator3.default)(arr), _s; !(_n = (_s = _i.next()).done); _n = true) {
        _arr.push(_s.value);

        if (i && _arr.length === i) break;
      }
    } catch (err) {
      _d = true;
      _e = err;
    } finally {
      try {
        if (!_n && _i["return"]) _i["return"]();
      } finally {
        if (_d) throw _e;
      }
    }

    return _arr;
  }

  return function (arr, i) {
    if (Array.isArray(arr)) {
      return arr;
    } else if ((0, _isIterable3.default)(Object(arr))) {
      return sliceIterator(arr, i);
    } else {
      throw new TypeError("Invalid attempt to destructure non-iterable instance");
    }
  };
}();

/***/ }),
/* 148 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(64);
__webpack_require__(154);
module.exports = __webpack_require__(3).Array.from;

/***/ }),
/* 149 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(74);
__webpack_require__(64);
module.exports = __webpack_require__(152);

/***/ }),
/* 150 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(74);
__webpack_require__(64);
module.exports = __webpack_require__(153);

/***/ }),
/* 151 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $defineProperty = __webpack_require__(6)
  , createDesc      = __webpack_require__(12);

module.exports = function(object, index, value){
  if(index in object)$defineProperty.f(object, index, createDesc(0, value));
  else object[index] = value;
};

/***/ }),
/* 152 */
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__(11)
  , get      = __webpack_require__(103);
module.exports = __webpack_require__(3).getIterator = function(it){
  var iterFn = get(it);
  if(typeof iterFn != 'function')throw TypeError(it + ' is not iterable!');
  return anObject(iterFn.call(it));
};

/***/ }),
/* 153 */
/***/ (function(module, exports, __webpack_require__) {

var classof   = __webpack_require__(94)
  , ITERATOR  = __webpack_require__(29)('iterator')
  , Iterators = __webpack_require__(54);
module.exports = __webpack_require__(3).isIterable = function(it){
  var O = Object(it);
  return O[ITERATOR] !== undefined
    || '@@iterator' in O
    || Iterators.hasOwnProperty(classof(O));
};

/***/ }),
/* 154 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var ctx            = __webpack_require__(15)
  , $export        = __webpack_require__(8)
  , toObject       = __webpack_require__(46)
  , call           = __webpack_require__(136)
  , isArrayIter    = __webpack_require__(135)
  , toLength       = __webpack_require__(56)
  , createProperty = __webpack_require__(151)
  , getIterFn      = __webpack_require__(103);

$export($export.S + $export.F * !__webpack_require__(137)(function(iter){ Array.from(iter); }), 'Array', {
  // 22.1.2.1 Array.from(arrayLike, mapfn = undefined, thisArg = undefined)
  from: function from(arrayLike/*, mapfn = undefined, thisArg = undefined*/){
    var O       = toObject(arrayLike)
      , C       = typeof this == 'function' ? this : Array
      , aLen    = arguments.length
      , mapfn   = aLen > 1 ? arguments[1] : undefined
      , mapping = mapfn !== undefined
      , index   = 0
      , iterFn  = getIterFn(O)
      , length, result, step, iterator;
    if(mapping)mapfn = ctx(mapfn, aLen > 2 ? arguments[2] : undefined, 2);
    // if object isn't iterable or it's array with default iterator - use simple case
    if(iterFn != undefined && !(C == Array && isArrayIter(iterFn))){
      for(iterator = iterFn.call(O), result = new C; !(step = iterator.next()).done; index++){
        createProperty(result, index, mapping ? call(iterator, mapfn, [step.value, index], true) : step.value);
      }
    } else {
      length = toLength(O.length);
      for(result = new C(length); length > index; index++){
        createProperty(result, index, mapping ? mapfn(O[index], index) : O[index]);
      }
    }
    result.length = index;
    return result;
  }
});


/***/ }),
/* 155 */,
/* 156 */,
/* 157 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.NumberSymbol = exports.NumberFormatter = exports.NumberSpecification = exports.PriceSpecification = undefined;

var _numberFormatter = __webpack_require__(161);

var _numberFormatter2 = _interopRequireDefault(_numberFormatter);

var _numberSymbol = __webpack_require__(107);

var _numberSymbol2 = _interopRequireDefault(_numberSymbol);

var _price = __webpack_require__(133);

var _price2 = _interopRequireDefault(_price);

var _number = __webpack_require__(108);

var _number2 = _interopRequireDefault(_number);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
exports.PriceSpecification = _price2.default;
exports.NumberSpecification = _number2.default;
exports.NumberFormatter = _numberFormatter2.default;
exports.NumberSymbol = _numberSymbol2.default;

/***/ }),
/* 158 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _from = __webpack_require__(144);

var _from2 = _interopRequireDefault(_from);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function (arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  } else {
    return (0, _from2.default)(arr);
  }
};

/***/ }),
/* 159 */,
/* 160 */,
/* 161 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _toConsumableArray2 = __webpack_require__(158);

var _toConsumableArray3 = _interopRequireDefault(_toConsumableArray2);

var _keys = __webpack_require__(68);

var _keys2 = _interopRequireDefault(_keys);

var _slicedToArray2 = __webpack_require__(147);

var _slicedToArray3 = _interopRequireDefault(_slicedToArray2);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _numberSymbol = __webpack_require__(107);

var _numberSymbol2 = _interopRequireDefault(_numberSymbol);

var _price = __webpack_require__(133);

var _price2 = _interopRequireDefault(_price);

var _number = __webpack_require__(108);

var _number2 = _interopRequireDefault(_number);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var escapeRE = __webpack_require__(180); /**
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
/**
 * These placeholders are used in CLDR number formatting templates.
 * They are meant to be replaced by the correct localized symbols in the number formatting process.
 */


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
    (0, _classCallCheck3.default)(this, NumberFormatter);

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


  (0, _createClass3.default)(NumberFormatter, [{
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
          _extractMajorMinorDig2 = (0, _slicedToArray3.default)(_extractMajorMinorDig, 2),
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
      var pattern = this.getCldrPattern(number < 0);
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
      var substrs = (0, _keys2.default)(pairs).map(escapeRE);
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
      var symbol = void 0;
      if (undefined !== specifications.numberSymbols) {
        symbol = new (Function.prototype.bind.apply(_numberSymbol2.default, [null].concat((0, _toConsumableArray3.default)(specifications.numberSymbols))))();
      } else {
        symbol = new (Function.prototype.bind.apply(_numberSymbol2.default, [null].concat((0, _toConsumableArray3.default)(specifications.symbol))))();
      }

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
/* 162 */
/***/ (function(module, exports) {

module.exports = {"base_url":"","routes":{"admin_product_form":{"tokens":[["variable","/","\\d+","id"],["text","/sell/catalog/products"]],"defaults":[],"requirements":{"id":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_cart_rules_search":{"tokens":[["text","/sell/catalog/cart-rules/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_view":{"tokens":[["text","/view"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_customers_search":{"tokens":[["text","/sell/customers/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_carts":{"tokens":[["text","/carts"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_orders":{"tokens":[["text","/orders"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_addresses_create":{"tokens":[["text","/sell/addresses/new"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_addresses_edit":{"tokens":[["text","/edit"],["variable","/","\\d+","addressId"],["text","/sell/addresses"]],"defaults":[],"requirements":{"addressId":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_order_addresses_edit":{"tokens":[["text","/edit"],["variable","/","delivery|invoice","addressType"],["variable","/","\\d+","orderId"],["text","/sell/addresses/order"]],"defaults":[],"requirements":{"orderId":"\\d+","addressType":"delivery|invoice"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_cart_addresses_edit":{"tokens":[["text","/edit"],["variable","/","delivery|invoice","addressType"],["variable","/","\\d+","cartId"],["text","/sell/addresses/cart"]],"defaults":[],"requirements":{"cartId":"\\d+","addressType":"delivery|invoice"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_carts_view":{"tokens":[["text","/view"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_carts_info":{"tokens":[["text","/info"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_carts_create":{"tokens":[["text","/sell/orders/carts/new"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_addresses":{"tokens":[["text","/addresses"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_carrier":{"tokens":[["text","/carrier"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_currency":{"tokens":[["text","/currency"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_language":{"tokens":[["text","/language"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_set_delivery_settings":{"tokens":[["text","/rules/delivery-settings"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_add_cart_rule":{"tokens":[["text","/cart-rules"],["variable","/","[^/]++","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_delete_cart_rule":{"tokens":[["text","/delete"],["variable","/","[^/]++","cartRuleId"],["text","/cart-rules"],["variable","/","[^/]++","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_add_product":{"tokens":[["text","/products"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_product_price":{"tokens":[["text","/price"],["variable","/","\\d+","productId"],["text","/products"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+","productId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_product_quantity":{"tokens":[["text","/quantity"],["variable","/","\\d+","productId"],["text","/products"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+","productId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_delete_product":{"tokens":[["text","/delete-product"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_place":{"tokens":[["text","/sell/orders/place"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_view":{"tokens":[["text","/view"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_orders_duplicate_cart":{"tokens":[["text","/duplicate-cart"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_update_product":{"tokens":[["variable","/","\\d+","orderDetailId"],["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+","orderDetailId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_partial_refund":{"tokens":[["text","/partial-refund"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_standard_refund":{"tokens":[["text","/standard-refund"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_return_product":{"tokens":[["text","/return-product"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_send_process_order_email":{"tokens":[["text","/sell/orders/process-order-email"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_add_product":{"tokens":[["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_delete_product":{"tokens":[["text","/delete"],["variable","/","\\d+","orderDetailId"],["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+","orderDetailId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_get_discounts":{"tokens":[["text","/discounts"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_prices":{"tokens":[["text","/prices"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_products":{"tokens":[["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_invoices":{"tokens":[["text","/invoices"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_documents":{"tokens":[["text","/documents"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_cancellation":{"tokens":[["text","/cancellation"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_configure_product_pagination":{"tokens":[["text","/sell/orders/configure-product-pagination"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_product_prices":{"tokens":[["text","/products/prices"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_products_search":{"tokens":[["text","/sell/orders/products/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]}},"prefix":"","host":"localhost","port":"","scheme":"http","locale":[]}

/***/ }),
/* 163 */,
/* 164 */,
/* 165 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(171), __esModule: true };

/***/ }),
/* 166 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(172), __esModule: true };

/***/ }),
/* 167 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(173), __esModule: true };

/***/ }),
/* 168 */,
/* 169 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _setPrototypeOf = __webpack_require__(167);

var _setPrototypeOf2 = _interopRequireDefault(_setPrototypeOf);

var _create = __webpack_require__(165);

var _create2 = _interopRequireDefault(_create);

var _typeof2 = __webpack_require__(93);

var _typeof3 = _interopRequireDefault(_typeof2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function (subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function, not " + (typeof superClass === "undefined" ? "undefined" : (0, _typeof3.default)(superClass)));
  }

  subClass.prototype = (0, _create2.default)(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      enumerable: false,
      writable: true,
      configurable: true
    }
  });
  if (superClass) _setPrototypeOf2.default ? (0, _setPrototypeOf2.default)(subClass, superClass) : subClass.__proto__ = superClass;
};

/***/ }),
/* 170 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _typeof2 = __webpack_require__(93);

var _typeof3 = _interopRequireDefault(_typeof2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function (self, call) {
  if (!self) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return call && ((typeof call === "undefined" ? "undefined" : (0, _typeof3.default)(call)) === "object" || typeof call === "function") ? call : self;
};

/***/ }),
/* 171 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(175);
var $Object = __webpack_require__(3).Object;
module.exports = function create(P, D){
  return $Object.create(P, D);
};

/***/ }),
/* 172 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(176);
module.exports = __webpack_require__(3).Object.getPrototypeOf;

/***/ }),
/* 173 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(177);
module.exports = __webpack_require__(3).Object.setPrototypeOf;

/***/ }),
/* 174 */
/***/ (function(module, exports, __webpack_require__) {

// Works with __proto__ only. Old v8 can't work with null proto objects.
/* eslint-disable no-proto */
var isObject = __webpack_require__(4)
  , anObject = __webpack_require__(11);
var check = function(O, proto){
  anObject(O);
  if(!isObject(proto) && proto !== null)throw TypeError(proto + ": can't set as prototype!");
};
module.exports = {
  set: Object.setPrototypeOf || ('__proto__' in {} ? // eslint-disable-line
    function(test, buggy, set){
      try {
        set = __webpack_require__(15)(Function.call, __webpack_require__(96).f(Object.prototype, '__proto__').set, 2);
        set(test, []);
        buggy = !(test instanceof Array);
      } catch(e){ buggy = true; }
      return function setPrototypeOf(O, proto){
        check(O, proto);
        if(buggy)O.__proto__ = proto;
        else set(O, proto);
        return O;
      };
    }({}, false) : undefined),
  check: check
};

/***/ }),
/* 175 */
/***/ (function(module, exports, __webpack_require__) {

var $export = __webpack_require__(8)
// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
$export($export.S, 'Object', {create: __webpack_require__(71)});

/***/ }),
/* 176 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.9 Object.getPrototypeOf(O)
var toObject        = __webpack_require__(46)
  , $getPrototypeOf = __webpack_require__(90);

__webpack_require__(77)('getPrototypeOf', function(){
  return function getPrototypeOf(it){
    return $getPrototypeOf(toObject(it));
  };
});

/***/ }),
/* 177 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.3.19 Object.setPrototypeOf(O, proto)
var $export = __webpack_require__(8);
$export($export.S, 'Object', {setPrototypeOf: __webpack_require__(174).set});

/***/ }),
/* 178 */,
/* 179 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
var _extends=Object.assign||function(a){for(var b,c=1;c<arguments.length;c++)for(var d in b=arguments[c],b)Object.prototype.hasOwnProperty.call(b,d)&&(a[d]=b[d]);return a},_typeof='function'==typeof Symbol&&'symbol'==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&'function'==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?'symbol':typeof a};function _classCallCheck(a,b){if(!(a instanceof b))throw new TypeError('Cannot call a class as a function')}var Routing=function a(){var b=this;_classCallCheck(this,a),this.setRoutes=function(a){b.routesRouting=a||[]},this.getRoutes=function(){return b.routesRouting},this.setBaseUrl=function(a){b.contextRouting.base_url=a},this.getBaseUrl=function(){return b.contextRouting.base_url},this.setPrefix=function(a){b.contextRouting.prefix=a},this.setScheme=function(a){b.contextRouting.scheme=a},this.getScheme=function(){return b.contextRouting.scheme},this.setHost=function(a){b.contextRouting.host=a},this.getHost=function(){return b.contextRouting.host},this.buildQueryParams=function(a,c,d){var e=new RegExp(/\[]$/);c instanceof Array?c.forEach(function(c,f){e.test(a)?d(a,c):b.buildQueryParams(a+'['+('object'===('undefined'==typeof c?'undefined':_typeof(c))?f:'')+']',c,d)}):'object'===('undefined'==typeof c?'undefined':_typeof(c))?Object.keys(c).forEach(function(e){return b.buildQueryParams(a+'['+e+']',c[e],d)}):d(a,c)},this.getRoute=function(a){var c=b.contextRouting.prefix+a;if(!!b.routesRouting[c])return b.routesRouting[c];else if(!b.routesRouting[a])throw new Error('The route "'+a+'" does not exist.');return b.routesRouting[a]},this.generate=function(a,c,d){var e=b.getRoute(a),f=c||{},g=_extends({},f),h='_scheme',i='',j=!0,k='';if((e.tokens||[]).forEach(function(b){if('text'===b[0])return i=b[1]+i,void(j=!1);if('variable'===b[0]){var c=(e.defaults||{})[b[3]];if(!1==j||!c||(f||{})[b[3]]&&f[b[3]]!==e.defaults[b[3]]){var d;if((f||{})[b[3]])d=f[b[3]],delete g[b[3]];else if(c)d=e.defaults[b[3]];else{if(j)return;throw new Error('The route "'+a+'" requires the parameter "'+b[3]+'".')}var h=!0===d||!1===d||''===d;if(!h||!j){var k=encodeURIComponent(d).replace(/%2F/g,'/');'null'===k&&null===d&&(k=''),i=b[1]+k+i}j=!1}else c&&delete g[b[3]];return}throw new Error('The token type "'+b[0]+'" is not supported.')}),''==i&&(i='/'),(e.hosttokens||[]).forEach(function(a){var b;return'text'===a[0]?void(k=a[1]+k):void('variable'===a[0]&&((f||{})[a[3]]?(b=f[a[3]],delete g[a[3]]):e.defaults[a[3]]&&(b=e.defaults[a[3]]),k=a[1]+b+k))}),i=b.contextRouting.base_url+i,e.requirements[h]&&b.getScheme()!==e.requirements[h]?i=e.requirements[h]+'://'+(k||b.getHost())+i:k&&b.getHost()!==k?i=b.getScheme()+'://'+k+i:!0===d&&(i=b.getScheme()+'://'+b.getHost()+i),0<Object.keys(g).length){var l=[],m=function(a,b){var c=b;c='function'==typeof c?c():c,c=null===c?'':c,l.push(encodeURIComponent(a)+'='+encodeURIComponent(c))};Object.keys(g).forEach(function(a){return b.buildQueryParams(a,g[a],m)}),i=i+'?'+l.join('&').replace(/%20/g,'+')}return i},this.setData=function(a){b.setBaseUrl(a.base_url),b.setRoutes(a.routes),'prefix'in a&&b.setPrefix(a.prefix),b.setHost(a.host),b.setScheme(a.scheme)},this.contextRouting={base_url:'',prefix:'',host:'',scheme:''}};module.exports=new Routing;

/***/ }),
/* 180 */
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

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(9)))

/***/ }),
/* 181 */,
/* 182 */,
/* 183 */,
/* 184 */,
/* 185 */,
/* 186 */,
/* 187 */,
/* 188 */,
/* 189 */,
/* 190 */,
/* 191 */,
/* 192 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
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

exports.default = {
  productDeletedFromOrder: 'productDeletedFromOrder',
  productAddedToOrder: 'productAddedToOrder',
  productUpdated: 'productUpdated',
  productEditionCanceled: 'productEditionCanceled',
  productListPaginated: 'productListPaginated',
  productListNumberPerPage: 'productListNumberPerPage'
};

/***/ }),
/* 193 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(207), __esModule: true };

/***/ }),
/* 194 */,
/* 195 */,
/* 196 */,
/* 197 */,
/* 198 */,
/* 199 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

var _window = window,
    $ = _window.$;

var OrderPricesRefresher = function () {
  function OrderPricesRefresher() {
    (0, _classCallCheck3.default)(this, OrderPricesRefresher);

    this.router = new _router2.default();
  }

  (0, _createClass3.default)(OrderPricesRefresher, [{
    key: 'refresh',
    value: function refresh(orderId) {
      $.ajax(this.router.generate('admin_orders_get_prices', { orderId: orderId })).then(function (response) {
        $(_OrderViewPageMap2.default.orderTotal).text(response.orderTotalFormatted);
        $(_OrderViewPageMap2.default.orderDiscountsTotal).text('-' + response.discountsAmountFormatted);
        $(_OrderViewPageMap2.default.orderDiscountsTotalContainer).toggleClass('d-none', !response.discountsAmountDisplayed);
        $(_OrderViewPageMap2.default.orderProductsTotal).text(response.productsTotalFormatted);
        $(_OrderViewPageMap2.default.orderShippingTotal).text(response.shippingTotalFormatted);
        $(_OrderViewPageMap2.default.orderTaxesTotal).text(response.taxesTotalFormatted);
      });
    }
  }, {
    key: 'refreshProductPrices',
    value: function refreshProductPrices(orderId) {
      $.ajax(this.router.generate('admin_orders_product_prices', { orderId: orderId })).then(function (productPricesList) {
        productPricesList.forEach(function (productPrices) {
          var orderProductTrId = _OrderViewPageMap2.default.productsTableRow(productPrices.orderDetailId);

          $(orderProductTrId + ' ' + _OrderViewPageMap2.default.productEditUnitPrice).text(productPrices.unitPrice);
          $(orderProductTrId + ' ' + _OrderViewPageMap2.default.productEditQuantity).text(productPrices.quantity);
          $(orderProductTrId + ' ' + _OrderViewPageMap2.default.productEditAvailableQuantity).text(productPrices.availableQuantity);
          $(orderProductTrId + ' ' + _OrderViewPageMap2.default.productEditTotalPrice).text(productPrices.totalPrice);

          // update order row price values
          var productEditButton = $(_OrderViewPageMap2.default.productEditBtn(productPrices.orderDetailId));

          productEditButton.data('product-price-tax-incl', productPrices.unitPriceTaxInclRaw);
          productEditButton.data('product-price-tax-excl', productPrices.unitPriceTaxExclRaw);
          productEditButton.data('product-quantity', productPrices.quantity);
        });
      });
    }
  }, {
    key: 'checkOtherProductPricesMatch',
    value: function checkOtherProductPricesMatch(givenPrice, productId, combinationId, invoiceId, orderDetailId) {
      var productRows = document.querySelectorAll('tr.cellProduct');
      // We convert the expected values into int/float to avoid a type mismatch that would be wrongly interpreted
      var expectedProductId = Number(productId);
      var expectedCombinationId = Number(combinationId);
      var expectedGivenPrice = Number(givenPrice);
      var unmatchingPriceExists = false;

      productRows.forEach(function (productRow) {
        var productRowId = $(productRow).attr('id');

        // No need to check edited row (especially if it's the only one for this product)
        if (orderDetailId && productRowId === 'orderProduct_' + orderDetailId) {
          return;
        }

        var productEditBtn = $('#' + productRowId + ' ' + _OrderViewPageMap2.default.productEditButtons);
        var currentOrderInvoiceId = Number(productEditBtn.data('order-invoice-id'));

        // No need to check target invoice, only if others have matching products
        if (invoiceId && currentOrderInvoiceId && invoiceId === currentOrderInvoiceId) {
          return;
        }

        var currentProductId = Number(productEditBtn.data('product-id'));
        var currentCombinationId = Number(productEditBtn.data('combination-id'));

        if (currentProductId !== expectedProductId || currentCombinationId !== expectedCombinationId) {
          return;
        }

        if (expectedGivenPrice !== Number(productEditBtn.data('product-price-tax-incl'))) {
          unmatchingPriceExists = true;
        }
      });

      return !unmatchingPriceExists;
    }
  }]);
  return OrderPricesRefresher;
}();

exports.default = OrderPricesRefresher;

/***/ }),
/* 200 */,
/* 201 */,
/* 202 */,
/* 203 */,
/* 204 */,
/* 205 */,
/* 206 */,
/* 207 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(209);
module.exports = __webpack_require__(3).Object.values;

/***/ }),
/* 208 */
/***/ (function(module, exports, __webpack_require__) {

var getKeys   = __webpack_require__(34)
  , toIObject = __webpack_require__(22)
  , isEnum    = __webpack_require__(52).f;
module.exports = function(isEntries){
  return function(it){
    var O      = toIObject(it)
      , keys   = getKeys(O)
      , length = keys.length
      , i      = 0
      , result = []
      , key;
    while(length > i)if(isEnum.call(O, key = keys[i++])){
      result.push(isEntries ? [key, O[key]] : O[key]);
    } return result;
  };
};

/***/ }),
/* 209 */
/***/ (function(module, exports, __webpack_require__) {

// https://github.com/tc39/proposal-object-values-entries
var $export = __webpack_require__(8)
  , $values = __webpack_require__(208)(false);

$export($export.S, 'Object', {
  values: function values(it){
    return $values(it);
  }
});

/***/ }),
/* 210 */,
/* 211 */,
/* 212 */,
/* 213 */,
/* 214 */,
/* 215 */,
/* 216 */,
/* 217 */,
/* 218 */,
/* 219 */,
/* 220 */,
/* 221 */,
/* 222 */,
/* 223 */,
/* 224 */,
/* 225 */,
/* 226 */,
/* 227 */,
/* 228 */,
/* 229 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _isNan = __webpack_require__(231);

var _isNan2 = _interopRequireDefault(_isNan);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

var OrderPrices = function () {
  function OrderPrices() {
    (0, _classCallCheck3.default)(this, OrderPrices);
  }

  (0, _createClass3.default)(OrderPrices, [{
    key: "calculateTaxExcluded",
    value: function calculateTaxExcluded(taxIncluded, taxRatePerCent, currencyPrecision) {
      var priceTaxIncl = parseFloat(taxIncluded);
      if (priceTaxIncl < 0 || (0, _isNan2.default)(priceTaxIncl)) {
        priceTaxIncl = 0;
      }
      var taxRate = taxRatePerCent / 100 + 1;
      return window.ps_round(priceTaxIncl / taxRate, currencyPrecision);
    }
  }, {
    key: "calculateTaxIncluded",
    value: function calculateTaxIncluded(taxExcluded, taxRatePerCent, currencyPrecision) {
      var priceTaxExcl = parseFloat(taxExcluded);
      if (priceTaxExcl < 0 || (0, _isNan2.default)(priceTaxExcl)) {
        priceTaxExcl = 0;
      }
      var taxRate = taxRatePerCent / 100 + 1;
      return window.ps_round(priceTaxExcl * taxRate, currencyPrecision);
    }
  }, {
    key: "calculateTotalPrice",
    value: function calculateTotalPrice(quantity, unitPrice, currencyPrecision) {
      return window.ps_round(unitPrice * quantity, currencyPrecision);
    }
  }]);
  return OrderPrices;
}();

exports.default = OrderPrices;

/***/ }),
/* 230 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

var _orderProductEdit = __webpack_require__(525);

var _orderProductEdit2 = _interopRequireDefault(_orderProductEdit);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
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

var OrderProductRenderer = function () {
  function OrderProductRenderer() {
    (0, _classCallCheck3.default)(this, OrderProductRenderer);

    this.router = new _router2.default();
  }

  (0, _createClass3.default)(OrderProductRenderer, [{
    key: 'addOrUpdateProductToList',
    value: function addOrUpdateProductToList($productRow, newRow) {
      if ($productRow.length > 0) {
        $productRow.html($(newRow).html());
      } else {
        $(_OrderViewPageMap2.default.productAddRow).before($(newRow).hide().fadeIn());
      }
    }
  }, {
    key: 'updateNumProducts',
    value: function updateNumProducts(numProducts) {
      $(_OrderViewPageMap2.default.productsCount).html(numProducts);
    }
  }, {
    key: 'editProductFromList',
    value: function editProductFromList(orderDetailId, quantity, priceTaxIncl, priceTaxExcl, taxRate, location, availableQuantity, availableOutOfStock, orderInvoiceId) {
      var $orderEdit = new _orderProductEdit2.default(orderDetailId);
      $orderEdit.displayProduct({
        price_tax_excl: priceTaxExcl,
        price_tax_incl: priceTaxIncl,
        tax_rate: taxRate,
        quantity: quantity,
        location: location,
        availableQuantity: availableQuantity,
        availableOutOfStock: availableOutOfStock,
        orderInvoiceId: orderInvoiceId
      });
      $(_OrderViewPageMap2.default.productAddActionBtn).addClass('d-none');
      $(_OrderViewPageMap2.default.productAddRow).addClass('d-none');
    }
  }, {
    key: 'moveProductsPanelToModificationPosition',
    value: function moveProductsPanelToModificationPosition() {
      var scrollTarget = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'body';

      $(_OrderViewPageMap2.default.productActionBtn).addClass('d-none');
      $(_OrderViewPageMap2.default.productAddActionBtn + ', ' + _OrderViewPageMap2.default.productAddRow).removeClass('d-none');
      this.moveProductPanelToTop(scrollTarget);
    }
  }, {
    key: 'moveProductsPanelToRefundPosition',
    value: function moveProductsPanelToRefundPosition() {
      this.resetAllEditRows();
      $(_OrderViewPageMap2.default.productAddActionBtn + ', ' + _OrderViewPageMap2.default.productAddRow + ', ' + _OrderViewPageMap2.default.productActionBtn).addClass('d-none');
      this.moveProductPanelToTop();
    }
  }, {
    key: 'moveProductPanelToTop',
    value: function moveProductPanelToTop() {
      var scrollTarget = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'body';

      var $modificationPosition = $(_OrderViewPageMap2.default.productModificationPosition);
      if ($modificationPosition.find(_OrderViewPageMap2.default.productsPanel).length > 0) {
        return;
      }
      $(_OrderViewPageMap2.default.productsPanel).detach().appendTo($modificationPosition);
      $modificationPosition.closest('.row').removeClass('d-none');

      // Show column location & refunded
      this.toggleColumn(_OrderViewPageMap2.default.productsCellLocation);
      this.toggleColumn(_OrderViewPageMap2.default.productsCellRefunded);

      // Show all rows, hide pagination controls
      var $rows = $(_OrderViewPageMap2.default.productsTable).find('tr[id^="orderProduct_"]');
      $rows.removeClass('d-none');
      $(_OrderViewPageMap2.default.productsPagination).addClass('d-none');

      var scrollValue = $(scrollTarget).offset().top - $('.header-toolbar').height() - 100;
      $('html,body').animate({ scrollTop: scrollValue }, 'slow');
    }
  }, {
    key: 'moveProductPanelToOriginalPosition',
    value: function moveProductPanelToOriginalPosition() {
      $(_OrderViewPageMap2.default.productAddNewInvoiceInfo).addClass('d-none');
      $(_OrderViewPageMap2.default.productModificationPosition).closest('.row').addClass('d-none');

      $(_OrderViewPageMap2.default.productsPanel).detach().appendTo(_OrderViewPageMap2.default.productOriginalPosition);

      $(_OrderViewPageMap2.default.productsPagination).removeClass('d-none');
      $(_OrderViewPageMap2.default.productActionBtn).removeClass('d-none');
      $(_OrderViewPageMap2.default.productAddActionBtn + ', ' + _OrderViewPageMap2.default.productAddRow).addClass('d-none');

      // Restore pagination
      this.paginate(1);
    }
  }, {
    key: 'resetAddRow',
    value: function resetAddRow() {
      $(_OrderViewPageMap2.default.productAddIdInput).val('');
      $(_OrderViewPageMap2.default.productSearchInput).val('');
      $(_OrderViewPageMap2.default.productAddCombinationsBlock).addClass('d-none');
      $(_OrderViewPageMap2.default.productAddCombinationsSelect).val('');
      $(_OrderViewPageMap2.default.productAddCombinationsSelect).prop('disabled', false);
      $(_OrderViewPageMap2.default.productAddPriceTaxExclInput).val('');
      $(_OrderViewPageMap2.default.productAddPriceTaxInclInput).val('');
      $(_OrderViewPageMap2.default.productAddQuantityInput).val('');
      $(_OrderViewPageMap2.default.productAddAvailableText).html('');
      $(_OrderViewPageMap2.default.productAddLocationText).html('');
      $(_OrderViewPageMap2.default.productAddNewInvoiceInfo).addClass('d-none');
      $(_OrderViewPageMap2.default.productAddActionBtn).prop('disabled', true);
    }
  }, {
    key: 'resetAllEditRows',
    value: function resetAllEditRows() {
      var _this = this;

      $(_OrderViewPageMap2.default.productEditButtons).each(function (key, editButton) {
        _this.resetEditRow($(editButton).data('orderDetailId'));
      });
    }
  }, {
    key: 'resetEditRow',
    value: function resetEditRow(orderProductId) {
      var $productRow = $(_OrderViewPageMap2.default.productsTableRow(orderProductId));
      var $productEditRow = $(_OrderViewPageMap2.default.productsTableRowEdited(orderProductId));
      $productEditRow.remove();
      $productRow.removeClass('d-none');
    }
  }, {
    key: 'paginate',
    value: function paginate(numPage) {
      var $rows = $(_OrderViewPageMap2.default.productsTable).find('tr[id^="orderProduct_"]');
      var $customizationRows = $(_OrderViewPageMap2.default.productsTableCustomizationRows);
      var $tablePagination = $(_OrderViewPageMap2.default.productsTablePagination);
      var numRowsPerPage = parseInt($tablePagination.data('numPerPage'), 10);
      var maxPage = Math.ceil($rows.length / numRowsPerPage);
      numPage = Math.max(1, Math.min(numPage, maxPage));
      this.paginateUpdateControls(numPage);

      // Hide all rows...
      $rows.addClass('d-none');
      $customizationRows.addClass('d-none');
      // ... and display good ones

      var startRow = (numPage - 1) * numRowsPerPage + 1;
      var endRow = numPage * numRowsPerPage;
      for (var i = startRow - 1; i < Math.min(endRow, $rows.length); i++) {
        $($rows[i]).removeClass('d-none');
      }
      $customizationRows.each(function () {
        if (!$(this).prev().hasClass('d-none')) {
          $(this).removeClass('d-none');
        }
      });

      // Remove all edition rows (careful not to remove the template)
      $(_OrderViewPageMap2.default.productEditRow).not(_OrderViewPageMap2.default.productEditRowTemplate).remove();

      // Toggle Column Location & Refunded
      this.toggleColumn(_OrderViewPageMap2.default.productsCellLocationDisplayed);
      this.toggleColumn(_OrderViewPageMap2.default.productsCellRefundedDisplayed);
    }
  }, {
    key: 'paginateUpdateControls',
    value: function paginateUpdateControls(numPage) {
      // Why 3 ? Next & Prev & Template
      var totalPage = $(_OrderViewPageMap2.default.productsTablePagination).find('li.page-item').length - 3;
      $(_OrderViewPageMap2.default.productsTablePagination).find('.active').removeClass('active');
      $(_OrderViewPageMap2.default.productsTablePagination).find('li:has(> [data-page="' + numPage + '"])').addClass('active');
      $(_OrderViewPageMap2.default.productsTablePaginationPrev).removeClass('disabled');
      if (numPage === 1) {
        $(_OrderViewPageMap2.default.productsTablePaginationPrev).addClass('disabled');
      }
      $(_OrderViewPageMap2.default.productsTablePaginationNext).removeClass('disabled');
      if (numPage === totalPage) {
        $(_OrderViewPageMap2.default.productsTablePaginationNext).addClass('disabled');
      }
      this.togglePaginationControls();
    }
  }, {
    key: 'updateNumPerPage',
    value: function updateNumPerPage(numPerPage) {
      if (numPerPage < 1) {
        numPerPage = 1;
      }
      var $rows = $(_OrderViewPageMap2.default.productsTable).find('tr[id^="orderProduct_"]');
      var $tablePagination = $(_OrderViewPageMap2.default.productsTablePagination);
      var numPages = Math.ceil($rows.length / numPerPage);

      // Update table data fields
      $tablePagination.data('numPages', numPages);
      $tablePagination.data('numPerPage', numPerPage);

      // Clean all page links, reinsert the removed template
      var $linkPaginationTemplate = $(_OrderViewPageMap2.default.productsTablePaginationTemplate);
      $(_OrderViewPageMap2.default.productsTablePagination).find('li:has(> [data-page])').remove();
      $(_OrderViewPageMap2.default.productsTablePaginationNext).before($linkPaginationTemplate);

      // Add appropriate pages
      for (var i = 1; i <= numPages; ++i) {
        var $linkPagination = $linkPaginationTemplate.clone();
        $linkPagination.find('span').attr('data-page', i);
        $linkPagination.find('span').html(i);
        $linkPaginationTemplate.before($linkPagination.removeClass('d-none'));
      }
    }
  }, {
    key: 'paginationAddPage',
    value: function paginationAddPage(numPage) {
      var $tablePagination = $(_OrderViewPageMap2.default.productsTablePagination);
      $tablePagination.data('numPages', numPage);
      var $linkPagination = $(_OrderViewPageMap2.default.productsTablePaginationTemplate).clone();
      $linkPagination.find('span').attr('data-page', numPage);
      $linkPagination.find('span').html(numPage);
      $(_OrderViewPageMap2.default.productsTablePaginationTemplate).before($linkPagination.removeClass('d-none'));
      this.togglePaginationControls();
    }
  }, {
    key: 'paginationRemovePage',
    value: function paginationRemovePage(numPage) {
      var $tablePagination = $(_OrderViewPageMap2.default.productsTablePagination);
      var numPages = $tablePagination.data('numPages');
      $tablePagination.data('numPages', numPages - 1);
      $(_OrderViewPageMap2.default.productsTablePagination).find('li:has(> [data-page="' + numPage + '"])').remove();
      this.togglePaginationControls();
    }
  }, {
    key: 'togglePaginationControls',
    value: function togglePaginationControls() {
      // Why 3 ? Next & Prev & Template
      var totalPage = $(_OrderViewPageMap2.default.productsTablePagination).find('li.page-item').length - 3;
      $(_OrderViewPageMap2.default.productsNavPagination).toggleClass('d-none', totalPage <= 1);
    }
  }, {
    key: 'toggleProductAddNewInvoiceInfo',
    value: function toggleProductAddNewInvoiceInfo() {
      if (parseInt($(_OrderViewPageMap2.default.productAddInvoiceSelect).val(), 10) === 0) {
        $(_OrderViewPageMap2.default.productAddNewInvoiceInfo).removeClass('d-none');
      } else {
        $(_OrderViewPageMap2.default.productAddNewInvoiceInfo).addClass('d-none');
      }
    }
  }, {
    key: 'toggleColumn',
    value: function toggleColumn(target) {
      var forceDisplay = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      var isColumnDisplayed = false;
      if (forceDisplay === null) {
        $(target).filter('td').each(function () {
          if ($(this).html().trim() !== '') {
            isColumnDisplayed = true;
            return false;
          }
        });
      } else {
        isColumnDisplayed = forceDisplay;
      }
      $(target).toggleClass('d-none', !isColumnDisplayed);
    }
  }]);
  return OrderProductRenderer;
}();

exports.default = OrderProductRenderer;

/***/ }),
/* 231 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(592), __esModule: true };

/***/ }),
/* 232 */,
/* 233 */,
/* 234 */,
/* 235 */,
/* 236 */,
/* 237 */,
/* 238 */,
/* 239 */,
/* 240 */,
/* 241 */,
/* 242 */,
/* 243 */,
/* 244 */,
/* 245 */,
/* 246 */,
/* 247 */,
/* 248 */,
/* 249 */,
/* 250 */,
/* 251 */,
/* 252 */,
/* 253 */,
/* 254 */,
/* 255 */,
/* 256 */,
/* 257 */,
/* 258 */,
/* 259 */,
/* 260 */,
/* 261 */,
/* 262 */,
/* 263 */,
/* 264 */,
/* 265 */,
/* 266 */,
/* 267 */,
/* 268 */,
/* 269 */,
/* 270 */,
/* 271 */,
/* 272 */,
/* 273 */,
/* 274 */,
/* 275 */,
/* 276 */,
/* 277 */,
/* 278 */,
/* 279 */,
/* 280 */,
/* 281 */,
/* 282 */,
/* 283 */,
/* 284 */,
/* 285 */,
/* 286 */,
/* 287 */,
/* 288 */,
/* 289 */,
/* 290 */,
/* 291 */,
/* 292 */,
/* 293 */,
/* 294 */,
/* 295 */,
/* 296 */,
/* 297 */,
/* 298 */,
/* 299 */,
/* 300 */,
/* 301 */,
/* 302 */,
/* 303 */,
/* 304 */,
/* 305 */,
/* 306 */,
/* 307 */,
/* 308 */,
/* 309 */,
/* 310 */,
/* 311 */,
/* 312 */,
/* 313 */,
/* 314 */,
/* 315 */,
/* 316 */,
/* 317 */,
/* 318 */,
/* 319 */,
/* 320 */,
/* 321 */,
/* 322 */,
/* 323 */,
/* 324 */,
/* 325 */,
/* 326 */,
/* 327 */,
/* 328 */,
/* 329 */,
/* 330 */,
/* 331 */,
/* 332 */,
/* 333 */,
/* 334 */,
/* 335 */,
/* 336 */,
/* 337 */,
/* 338 */,
/* 339 */,
/* 340 */,
/* 341 */,
/* 342 */,
/* 343 */,
/* 344 */,
/* 345 */,
/* 346 */,
/* 347 */,
/* 348 */,
/* 349 */,
/* 350 */,
/* 351 */,
/* 352 */,
/* 353 */,
/* 354 */,
/* 355 */,
/* 356 */,
/* 357 */,
/* 358 */,
/* 359 */,
/* 360 */,
/* 361 */,
/* 362 */,
/* 363 */,
/* 364 */,
/* 365 */,
/* 366 */,
/* 367 */,
/* 368 */,
/* 369 */,
/* 370 */,
/* 371 */,
/* 372 */,
/* 373 */,
/* 374 */,
/* 375 */,
/* 376 */,
/* 377 */,
/* 378 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

var $ = window.$;

/**
 * TextWithLengthCounter handles input with length counter UI.
 *
 * Usage:
 *
 * There must be an element that wraps both input & counter display with ".js-text-with-length-counter" class.
 * Counter display must have ".js-countable-text-display" class and input must have ".js-countable-text-input" class.
 * Text input must have "data-max-length" attribute.
 *
 * <div class="js-text-with-length-counter">
 *  <span class="js-countable-text"></span>
 *  <input class="js-countable-input" data-max-length="255">
 * </div>
 *
 * In Javascript you must enable this component:
 *
 * new TextWithLengthCounter();
 */

var TextWithLengthCounter = function TextWithLengthCounter() {
  var _this = this;

  (0, _classCallCheck3.default)(this, TextWithLengthCounter);

  this.wrapperSelector = '.js-text-with-length-counter';
  this.textSelector = '.js-countable-text';
  this.inputSelector = '.js-countable-input';

  $(document).on('input', this.wrapperSelector + ' ' + this.inputSelector, function (e) {
    var $input = $(e.currentTarget);
    var remainingLength = $input.data('max-length') - $input.val().length;

    $input.closest(_this.wrapperSelector).find(_this.textSelector).text(remainingLength);
  });
};

exports.default = TextWithLengthCounter;

/***/ }),
/* 379 */,
/* 380 */,
/* 381 */,
/* 382 */,
/* 383 */,
/* 384 */,
/* 385 */,
/* 386 */,
/* 387 */,
/* 388 */,
/* 389 */,
/* 390 */,
/* 391 */,
/* 392 */,
/* 393 */,
/* 394 */,
/* 395 */,
/* 396 */,
/* 397 */,
/* 398 */,
/* 399 */,
/* 400 */,
/* 401 */,
/* 402 */,
/* 403 */,
/* 404 */,
/* 405 */,
/* 406 */,
/* 407 */,
/* 408 */,
/* 409 */,
/* 410 */,
/* 411 */,
/* 412 */,
/* 413 */,
/* 414 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * All actions for order view page messages are registered in this class.
 */
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

var OrderViewPageMessagesHandler = function () {
  function OrderViewPageMessagesHandler() {
    var _this = this;

    (0, _classCallCheck3.default)(this, OrderViewPageMessagesHandler);

    this.$orderMessageChangeWarning = $(_OrderViewPageMap2.default.orderMessageChangeWarning);
    this.$messagesContainer = $(_OrderViewPageMap2.default.orderMessagesContainer);

    return {
      listenForPredefinedMessageSelection: function listenForPredefinedMessageSelection() {
        return _this._handlePredefinedMessageSelection();
      },
      listenForFullMessagesOpen: function listenForFullMessagesOpen() {
        return _this._onFullMessagesOpen();
      }
    };
  }

  /**
   * Handles predefined order message selection.
   *
   * @private
   */


  (0, _createClass3.default)(OrderViewPageMessagesHandler, [{
    key: '_handlePredefinedMessageSelection',
    value: function _handlePredefinedMessageSelection() {
      var _this2 = this;

      $(document).on('change', _OrderViewPageMap2.default.orderMessageNameSelect, function (e) {
        var $currentItem = $(e.currentTarget);
        var valueId = $currentItem.val();

        if (!valueId) {
          return;
        }

        var message = _this2.$messagesContainer.find('div[data-id=' + valueId + ']').text().trim();
        var $orderMessage = $(_OrderViewPageMap2.default.orderMessage);
        var isSameMessage = $orderMessage.val().trim() === message;

        if (isSameMessage) {
          return;
        }

        if ($orderMessage.val() && !confirm(_this2.$orderMessageChangeWarning.text())) {
          return;
        }

        $orderMessage.val(message);
      });
    }

    /**
     * Listens for event when all messages modal is being opened
     *
     * @private
     */

  }, {
    key: '_onFullMessagesOpen',
    value: function _onFullMessagesOpen() {
      var _this3 = this;

      $(document).on('click', _OrderViewPageMap2.default.openAllMessagesBtn, function () {
        return _this3._scrollToMsgListBottom();
      });
    }

    /**
     * Scrolls down to the bottom of all messages list
     *
     * @private
     */

  }, {
    key: '_scrollToMsgListBottom',
    value: function _scrollToMsgListBottom() {
      var $msgModal = $(_OrderViewPageMap2.default.allMessagesModal);
      var msgList = document.querySelector(_OrderViewPageMap2.default.allMessagesList);

      var classCheckInterval = window.setInterval(function () {
        if ($msgModal.hasClass('show')) {
          msgList.scrollTop = msgList.scrollHeight;
          clearInterval(classCheckInterval);
        }
      }, 10);
    }
  }]);
  return OrderViewPageMessagesHandler;
}();

exports.default = OrderViewPageMessagesHandler;

/***/ }),
/* 415 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
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

var OrderShippingManager = function () {
  function OrderShippingManager() {
    (0, _classCallCheck3.default)(this, OrderShippingManager);

    this._initOrderShippingUpdateEventHandler();
  }

  (0, _createClass3.default)(OrderShippingManager, [{
    key: '_initOrderShippingUpdateEventHandler',
    value: function _initOrderShippingUpdateEventHandler() {
      $(_OrderViewPageMap2.default.showOrderShippingUpdateModalBtn).on('click', function (event) {
        var $btn = $(event.currentTarget);

        $(_OrderViewPageMap2.default.updateOrderShippingTrackingNumberInput).val($btn.data('order-tracking-number'));
        $(_OrderViewPageMap2.default.updateOrderShippingCurrentOrderCarrierIdInput).val($btn.data('order-carrier-id'));
      });
    }
  }]);
  return OrderShippingManager;
}();

exports.default = OrderShippingManager;

/***/ }),
/* 416 */,
/* 417 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _values = __webpack_require__(193);

var _values2 = _interopRequireDefault(_values);

var _keys = __webpack_require__(68);

var _keys2 = _interopRequireDefault(_keys);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
var _window = window,
    $ = _window.$;

var OrderProductAutocomplete = function () {
  function OrderProductAutocomplete(input) {
    (0, _classCallCheck3.default)(this, OrderProductAutocomplete);

    this.activeSearchRequest = null;
    this.router = new _router2.default();
    this.input = input;
    this.results = [];
    this.dropdownMenu = $(_OrderViewPageMap2.default.productSearchInputAutocompleteMenu);
    /**
     * Permit to link to each value of dropdown a callback after item is clicked
     */
    this.onItemClickedCallback = function () {};
  }

  (0, _createClass3.default)(OrderProductAutocomplete, [{
    key: 'listenForSearch',
    value: function listenForSearch() {
      var _this = this;

      this.input.on('click', function (event) {
        event.stopImmediatePropagation();
        _this.updateResults(_this.results);
      });

      this.input.on('keyup', function (event) {
        return _this.delaySearch(event.currentTarget);
      });

      $(document).on('click', function () {
        return _this.dropdownMenu.hide();
      });
    }
  }, {
    key: 'delaySearch',
    value: function delaySearch(input) {
      var _this2 = this;

      clearTimeout(this.searchTimeoutId);

      this.searchTimeoutId = setTimeout(function () {
        _this2.search(input.value, $(input).data('currency'), $(input).data('order'));
      }, 300);
    }
  }, {
    key: 'search',
    value: function search(_search, currency, orderId) {
      var _this3 = this;

      var params = { search_phrase: _search };

      if (currency) {
        params.currency_id = currency;
      }

      if (orderId) {
        params.order_id = orderId;
      }

      if (this.activeSearchRequest !== null) {
        this.activeSearchRequest.abort();
      }

      this.activeSearchRequest = $.get(this.router.generate('admin_orders_products_search', params));
      this.activeSearchRequest.then(function (response) {
        return _this3.updateResults(response);
      }).always(function () {
        _this3.activeSearchRequest = null;
      });
    }
  }, {
    key: 'updateResults',
    value: function updateResults(results) {
      var _this4 = this;

      this.dropdownMenu.empty();

      if (!results || !results.products || (0, _keys2.default)(results.products).length <= 0) {
        this.dropdownMenu.hide();
        return;
      }

      this.results = results.products;

      (0, _values2.default)(this.results).forEach(function (val) {
        var link = $('<a class="dropdown-item" data-id="' + val.productId + '" href="#">' + val.name + '</a>');

        link.on('click', function (event) {
          event.preventDefault();
          _this4.onItemClicked($(event.target).data('id'));
        });

        _this4.dropdownMenu.append(link);
      });

      this.dropdownMenu.show();
    }
  }, {
    key: 'onItemClicked',
    value: function onItemClicked(id) {
      var selectedProduct = this.results.filter(function (product) {
        return product.productId === id;
      });

      if (selectedProduct.length !== 0) {
        this.input.val(selectedProduct[0].name);
        this.onItemClickedCallback(selectedProduct[0]);
      }
    }
  }]);
  return OrderProductAutocomplete;
}();

exports.default = OrderProductAutocomplete;

/***/ }),
/* 418 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _keys = __webpack_require__(68);

var _keys2 = _interopRequireDefault(_keys);

var _values = __webpack_require__(193);

var _values2 = _interopRequireDefault(_values);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

var _eventEmitter = __webpack_require__(36);

var _orderViewEventMap = __webpack_require__(192);

var _orderViewEventMap2 = _interopRequireDefault(_orderViewEventMap);

var _orderPrices = __webpack_require__(229);

var _orderPrices2 = _interopRequireDefault(_orderPrices);

var _orderProductRenderer = __webpack_require__(230);

var _orderProductRenderer2 = _interopRequireDefault(_orderProductRenderer);

var _modal = __webpack_require__(45);

var _modal2 = _interopRequireDefault(_modal);

var _orderPricesRefresher = __webpack_require__(199);

var _orderPricesRefresher2 = _interopRequireDefault(_orderPricesRefresher);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

var _window = window,
    $ = _window.$;

var OrderProductAdd = function () {
  function OrderProductAdd() {
    (0, _classCallCheck3.default)(this, OrderProductAdd);

    this.router = new _router2.default();
    this.productAddActionBtn = $(_OrderViewPageMap2.default.productAddActionBtn);
    this.productIdInput = $(_OrderViewPageMap2.default.productAddIdInput);
    this.combinationsBlock = $(_OrderViewPageMap2.default.productAddCombinationsBlock);
    this.combinationsSelect = $(_OrderViewPageMap2.default.productAddCombinationsSelect);
    this.priceTaxIncludedInput = $(_OrderViewPageMap2.default.productAddPriceTaxInclInput);
    this.priceTaxExcludedInput = $(_OrderViewPageMap2.default.productAddPriceTaxExclInput);
    this.taxRateInput = $(_OrderViewPageMap2.default.productAddTaxRateInput);
    this.quantityInput = $(_OrderViewPageMap2.default.productAddQuantityInput);
    this.availableText = $(_OrderViewPageMap2.default.productAddAvailableText);
    this.locationText = $(_OrderViewPageMap2.default.productAddLocationText);
    this.totalPriceText = $(_OrderViewPageMap2.default.productAddTotalPriceText);
    this.invoiceSelect = $(_OrderViewPageMap2.default.productAddInvoiceSelect);
    this.freeShippingSelect = $(_OrderViewPageMap2.default.productAddFreeShippingSelect);
    this.productAddMenuBtn = $(_OrderViewPageMap2.default.productAddBtn);
    this.available = null;
    this.setupListener();
    this.product = {};
    this.currencyPrecision = $(_OrderViewPageMap2.default.productsTable).data('currencyPrecision');
    this.priceTaxCalculator = new _orderPrices2.default();
    this.orderProductRenderer = new _orderProductRenderer2.default();
    this.orderPricesRefresher = new _orderPricesRefresher2.default();
  }

  (0, _createClass3.default)(OrderProductAdd, [{
    key: 'setupListener',
    value: function setupListener() {
      var _this = this;

      this.combinationsSelect.on('change', function (event) {
        _this.priceTaxExcludedInput.val(window.ps_round($(event.currentTarget).find(':selected').data('priceTaxExcluded'), _this.currencyPrecision));

        _this.priceTaxIncludedInput.val(window.ps_round($(event.currentTarget).find(':selected').data('priceTaxIncluded'), _this.currencyPrecision));

        _this.locationText.html($(event.currentTarget).find(':selected').data('location'));

        _this.available = $(event.currentTarget).find(':selected').data('stock');

        _this.quantityInput.trigger('change');
        _this.orderProductRenderer.toggleColumn(_OrderViewPageMap2.default.productsCellLocation);
      });

      this.quantityInput.on('change keyup', function (event) {
        if (_this.available !== null) {
          var newQuantity = Number(event.target.value);
          var remainingAvailable = _this.available - newQuantity;
          var availableOutOfStock = _this.availableText.data('availableOutOfStock');
          _this.availableText.text(remainingAvailable);
          _this.availableText.toggleClass('text-danger font-weight-bold', remainingAvailable < 0);
          var disableAddActionBtn = newQuantity <= 0 || remainingAvailable < 0 && !availableOutOfStock;
          _this.productAddActionBtn.prop('disabled', disableAddActionBtn);
          _this.invoiceSelect.prop('disabled', !availableOutOfStock && remainingAvailable < 0);

          var taxIncluded = parseFloat(_this.priceTaxIncludedInput.val());
          _this.totalPriceText.html(_this.priceTaxCalculator.calculateTotalPrice(newQuantity, taxIncluded, _this.currencyPrecision));
        }
      });

      this.productIdInput.on('change', function () {
        _this.productAddActionBtn.removeAttr('disabled');
        _this.invoiceSelect.removeAttr('disabled');
      });

      this.priceTaxIncludedInput.on('change keyup', function (event) {
        var taxIncluded = parseFloat(event.target.value);
        var taxExcluded = _this.priceTaxCalculator.calculateTaxExcluded(taxIncluded, _this.taxRateInput.val(), _this.currencyPrecision);
        var quantity = parseInt(_this.quantityInput.val(), 10);

        _this.priceTaxExcludedInput.val(taxExcluded);
        _this.totalPriceText.html(_this.priceTaxCalculator.calculateTotalPrice(quantity, taxIncluded, _this.currencyPrecision));
      });

      this.priceTaxExcludedInput.on('change keyup', function (event) {
        var taxExcluded = parseFloat(event.target.value);
        var taxIncluded = _this.priceTaxCalculator.calculateTaxIncluded(taxExcluded, _this.taxRateInput.val(), _this.currencyPrecision);
        var quantity = parseInt(_this.quantityInput.val(), 10);

        _this.priceTaxIncludedInput.val(taxIncluded);
        _this.totalPriceText.html(_this.priceTaxCalculator.calculateTotalPrice(quantity, taxIncluded, _this.currencyPrecision));
      });

      this.productAddActionBtn.on('click', function (event) {
        return _this.confirmNewInvoice(event);
      });
      this.invoiceSelect.on('change', function () {
        return _this.orderProductRenderer.toggleProductAddNewInvoiceInfo();
      });
    }
  }, {
    key: 'setProduct',
    value: function setProduct(product) {
      this.productIdInput.val(product.productId).trigger('change');
      this.priceTaxExcludedInput.val(window.ps_round(product.priceTaxExcl, this.currencyPrecision));
      this.priceTaxIncludedInput.val(window.ps_round(product.priceTaxIncl, this.currencyPrecision));
      this.taxRateInput.val(product.taxRate);
      this.locationText.html(product.location);
      this.available = product.stock;
      this.availableText.data('availableOutOfStock', product.availableOutOfStock);
      this.quantityInput.val(1);
      this.quantityInput.trigger('change');
      this.setCombinations(product.combinations);
      this.orderProductRenderer.toggleColumn(_OrderViewPageMap2.default.productsCellLocation);
    }
  }, {
    key: 'setCombinations',
    value: function setCombinations(combinations) {
      var _this2 = this;

      this.combinationsSelect.empty();

      (0, _values2.default)(combinations).forEach(function (val) {
        _this2.combinationsSelect.append('<option value="' + val.attributeCombinationId + '" data-price-tax-excluded="' + val.priceTaxExcluded + '" data-price-tax-included="' + val.priceTaxIncluded + '" data-stock="' + val.stock + '" data-location="' + val.location + '">' + val.attribute + '</option>');
      });

      this.combinationsBlock.toggleClass('d-none', (0, _keys2.default)(combinations).length === 0);

      if ((0, _keys2.default)(combinations).length > 0) {
        this.combinationsSelect.trigger('change');
      }
    }
  }, {
    key: 'addProduct',
    value: function addProduct(orderId) {
      var _this3 = this;

      this.productAddActionBtn.prop('disabled', true);
      this.invoiceSelect.prop('disabled', true);
      this.combinationsSelect.prop('disabled', true);

      var params = {
        product_id: this.productIdInput.val(),
        combination_id: $(':selected', this.combinationsSelect).val(),
        price_tax_incl: this.priceTaxIncludedInput.val(),
        price_tax_excl: this.priceTaxExcludedInput.val(),
        quantity: this.quantityInput.val(),
        invoice_id: this.invoiceSelect.val(),
        free_shipping: this.freeShippingSelect.prop('checked')
      };

      $.ajax({
        url: this.router.generate('admin_orders_add_product', { orderId: orderId }),
        method: 'POST',
        data: params
      }).then(function (response) {
        _eventEmitter.EventEmitter.emit(_orderViewEventMap2.default.productAddedToOrder, {
          orderId: orderId,
          orderProductId: params.product_id,
          newRow: response
        });
      }, function (response) {
        _this3.productAddActionBtn.prop('disabled', false);
        _this3.invoiceSelect.prop('disabled', false);
        _this3.combinationsSelect.prop('disabled', false);

        if (response.responseJSON && response.responseJSON.message) {
          $.growl.error({ message: response.responseJSON.message });
        }
      });
    }
  }, {
    key: 'confirmNewInvoice',
    value: function confirmNewInvoice(event) {
      var _this4 = this;

      var invoiceId = parseInt(this.invoiceSelect.val(), 10);
      var orderId = $(event.currentTarget).data('orderId');

      // Explicit 0 value is used when we the user selected New Invoice
      if (invoiceId === 0) {
        var modal = new _modal2.default({
          id: 'modal-confirm-new-invoice',
          confirmTitle: this.invoiceSelect.data('modal-title'),
          confirmMessage: this.invoiceSelect.data('modal-body'),
          confirmButtonLabel: this.invoiceSelect.data('modal-apply'),
          closeButtonLabel: this.invoiceSelect.data('modal-cancel')
        }, function () {
          _this4.confirmNewPrice(orderId, invoiceId);
        });
        modal.show();
      } else if (!isNaN(invoiceId)) {
        // If id is not 0 nor NaN a specific invoice was selected
        this.confirmNewPrice(orderId, invoiceId);
      } else {
        // Last case is Nan, the selector is not even present, we simply add product and let the BO handle it
        this.addProduct(orderId);
      }
    }
  }, {
    key: 'confirmNewPrice',
    value: function confirmNewPrice(orderId, invoiceId) {
      var _this5 = this;

      var combinationId = typeof $(':selected', this.combinationsSelect).val() === 'undefined' ? 0 : $(':selected', this.combinationsSelect).val();
      var productPriceMatch = this.orderPricesRefresher.checkOtherProductPricesMatch(this.priceTaxIncludedInput.val(), this.productIdInput.val(), combinationId, invoiceId);

      if (!productPriceMatch) {
        var modalEditPrice = new _modal2.default({
          id: 'modal-confirm-new-price',
          confirmTitle: this.invoiceSelect.data('modal-edit-price-title'),
          confirmMessage: this.invoiceSelect.data('modal-edit-price-body'),
          confirmButtonLabel: this.invoiceSelect.data('modal-edit-price-apply'),
          closeButtonLabel: this.invoiceSelect.data('modal-edit-price-cancel')
        }, function () {
          _this5.addProduct(orderId);
        });
        modalEditPrice.show();
      } else {
        this.addProduct(orderId);
      }
    }
  }]);
  return OrderProductAdd;
}();

exports.default = OrderProductAdd;

/***/ }),
/* 419 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _orderProductManager = __webpack_require__(526);

var _orderProductManager2 = _interopRequireDefault(_orderProductManager);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

var _orderViewEventMap = __webpack_require__(192);

var _orderViewEventMap2 = _interopRequireDefault(_orderViewEventMap);

var _eventEmitter = __webpack_require__(36);

var _orderDiscountsRefresher = __webpack_require__(521);

var _orderDiscountsRefresher2 = _interopRequireDefault(_orderDiscountsRefresher);

var _orderProductRenderer = __webpack_require__(230);

var _orderProductRenderer2 = _interopRequireDefault(_orderProductRenderer);

var _orderPricesRefresher = __webpack_require__(199);

var _orderPricesRefresher2 = _interopRequireDefault(_orderPricesRefresher);

var _orderProductsRefresher = __webpack_require__(527);

var _orderProductsRefresher2 = _interopRequireDefault(_orderProductsRefresher);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _orderInvoicesRefresher = __webpack_require__(523);

var _orderInvoicesRefresher2 = _interopRequireDefault(_orderInvoicesRefresher);

var _orderProductCancel = __webpack_require__(524);

var _orderProductCancel2 = _interopRequireDefault(_orderProductCancel);

var _orderDocumentsRefresher = __webpack_require__(522);

var _orderDocumentsRefresher2 = _interopRequireDefault(_orderDocumentsRefresher);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

var $ = window.$;

var OrderViewPage = function () {
  function OrderViewPage() {
    (0, _classCallCheck3.default)(this, OrderViewPage);

    this.orderDiscountsRefresher = new _orderDiscountsRefresher2.default();
    this.orderProductManager = new _orderProductManager2.default();
    this.orderProductRenderer = new _orderProductRenderer2.default();
    this.orderPricesRefresher = new _orderPricesRefresher2.default();
    this.orderProductsRefresher = new _orderProductsRefresher2.default();
    this.orderDocumentsRefresher = new _orderDocumentsRefresher2.default();
    this.orderInvoicesRefresher = new _orderInvoicesRefresher2.default();
    this.orderProductCancel = new _orderProductCancel2.default();
    this.router = new _router2.default();
    this.listenToEvents();
  }

  (0, _createClass3.default)(OrderViewPage, [{
    key: 'listenToEvents',
    value: function listenToEvents() {
      var _this = this;

      $(_OrderViewPageMap2.default.invoiceAddressEditBtn).fancybox({
        'type': 'iframe',
        'width': '90%',
        'height': '90%'
      });
      $(_OrderViewPageMap2.default.deliveryAddressEditBtn).fancybox({
        'type': 'iframe',
        'width': '90%',
        'height': '90%'
      });

      _eventEmitter.EventEmitter.on(_orderViewEventMap2.default.productDeletedFromOrder, function (event) {
        // Remove the row
        var $row = $(_OrderViewPageMap2.default.productsTableRow(event.oldOrderDetailId));
        var $next = $row.next();
        $row.remove();
        if ($next.hasClass('order-product-customization')) {
          $next.remove();
        }

        var $tablePagination = $(_OrderViewPageMap2.default.productsTablePagination);
        var numPages = $tablePagination.data('numPages');
        var numRowsPerPage = $tablePagination.data('numPerPage');
        var numRows = $(_OrderViewPageMap2.default.productsTable).find('tr[id^="orderProduct_"]:not(.d-none)').length;
        var currentPage = parseInt($(_OrderViewPageMap2.default.productsTablePaginationActive).html(), 10);
        var numProducts = parseInt($(_OrderViewPageMap2.default.productsCount).html(), 10);
        if ((numProducts - 1) % numRowsPerPage === 0) {
          _this.orderProductRenderer.paginationRemovePage(numPages);
        }
        if (numRows === 1 && currentPage === numPages) {
          currentPage -= 1;
        }
        _eventEmitter.EventEmitter.emit(_orderViewEventMap2.default.productListPaginated, {
          numPage: currentPage
        });

        _this.orderProductRenderer.updateNumProducts(numProducts - 1);
        _this.orderPricesRefresher.refresh(event.orderId);
        _this.orderProductsRefresher.refresh(event.orderId);
        _this.orderDiscountsRefresher.refresh(event.orderId);
        _this.orderDocumentsRefresher.refresh(event.orderId);
      });

      _eventEmitter.EventEmitter.on(_orderViewEventMap2.default.productEditionCanceled, function (event) {
        _this.orderProductRenderer.resetEditRow(event.orderDetailId);
        var editRowsLeft = $(_OrderViewPageMap2.default.productEditRow).not(_OrderViewPageMap2.default.productEditRowTemplate).length;
        if (editRowsLeft > 0) {
          return;
        }
        _this.orderProductRenderer.moveProductPanelToOriginalPosition();
      });

      _eventEmitter.EventEmitter.on(_orderViewEventMap2.default.productUpdated, function (event) {
        _this.orderProductRenderer.addOrUpdateProductToList($(_OrderViewPageMap2.default.productsTableRow(event.orderDetailId)), event.newRow);
        _this.orderProductRenderer.resetEditRow(event.orderDetailId);
        _this.orderPricesRefresher.refresh(event.orderId);
        _this.orderPricesRefresher.refreshProductPrices(event.orderId);
        _this.orderDiscountsRefresher.refresh(event.orderId);
        _this.orderInvoicesRefresher.refresh(event.orderId);
        _this.orderDocumentsRefresher.refresh(event.orderId);
        _this.listenForProductDelete();
        _this.listenForProductEdit();
        _this.resetToolTips();

        var editRowsLeft = $(_OrderViewPageMap2.default.productEditRow).not(_OrderViewPageMap2.default.productEditRowTemplate).length;
        if (editRowsLeft > 0) {
          return;
        }
        _this.orderProductRenderer.moveProductPanelToOriginalPosition();
      });

      _eventEmitter.EventEmitter.on(_orderViewEventMap2.default.productAddedToOrder, function (event) {
        var $tablePagination = $(_OrderViewPageMap2.default.productsTablePagination);
        var numRowsPerPage = $tablePagination.data('numPerPage');
        var initialNumProducts = parseInt($(_OrderViewPageMap2.default.productsCount).html(), 10);

        _this.orderProductRenderer.addOrUpdateProductToList($('#' + $(event.newRow).find('tr').attr('id')), event.newRow);
        _this.listenForProductDelete();
        _this.listenForProductEdit();
        _this.resetToolTips();

        var newNumProducts = $(_OrderViewPageMap2.default.productsTableRows).length;
        var initialPagesNum = Math.ceil(initialNumProducts / numRowsPerPage);
        var newPagesNum = Math.ceil(newNumProducts / numRowsPerPage);

        // Update pagination
        if (newPagesNum > initialPagesNum) {
          _this.orderProductRenderer.paginationAddPage(newPagesNum);
        }

        _this.orderProductRenderer.updateNumProducts(newNumProducts);
        _this.orderProductRenderer.resetAddRow();
        _this.orderPricesRefresher.refreshProductPrices(event.orderId);
        _this.orderPricesRefresher.refresh(event.orderId);
        _this.orderDiscountsRefresher.refresh(event.orderId);
        _this.orderInvoicesRefresher.refresh(event.orderId);
        _this.orderDocumentsRefresher.refresh(event.orderId);
        _this.orderProductRenderer.moveProductPanelToOriginalPosition();

        // Move to last page to see the added product
        _eventEmitter.EventEmitter.emit(_orderViewEventMap2.default.productListPaginated, {
          numPage: newPagesNum
        });
      });
    }
  }, {
    key: 'listenForProductDelete',
    value: function listenForProductDelete() {
      var _this2 = this;

      $(_OrderViewPageMap2.default.productDeleteBtn).off('click').on('click', function (event) {
        _this2.orderProductManager.handleDeleteProductEvent(event);
      });
    }
  }, {
    key: 'resetToolTips',
    value: function resetToolTips() {
      $(_OrderViewPageMap2.default.productEditButtons).pstooltip();
      $(_OrderViewPageMap2.default.productDeleteBtn).pstooltip();
    }
  }, {
    key: 'listenForProductEdit',
    value: function listenForProductEdit() {
      var _this3 = this;

      $(_OrderViewPageMap2.default.productEditButtons).off('click').on('click', function (event) {
        var $btn = $(event.currentTarget);
        _this3.orderProductRenderer.moveProductsPanelToModificationPosition();
        _this3.orderProductRenderer.editProductFromList($btn.data('orderDetailId'), $btn.data('productQuantity'), $btn.data('productPriceTaxIncl'), $btn.data('productPriceTaxExcl'), $btn.data('taxRate'), $btn.data('location'), $btn.data('availableQuantity'), $btn.data('availableOutOfStock'), $btn.data('orderInvoiceId'));
      });
    }
  }, {
    key: 'listenForProductPack',
    value: function listenForProductPack() {
      var _this4 = this;

      $(_OrderViewPageMap2.default.productPackModal.modal).on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var packItems = button.data('packItems');
        var modal = $(_OrderViewPageMap2.default.productPackModal.modal);
        $(_OrderViewPageMap2.default.productPackModal.rows).remove();
        packItems.forEach(function (item) {
          var $item = $(_OrderViewPageMap2.default.productPackModal.template).clone();
          $item.attr('id', 'productpack_' + item.id).removeClass('d-none');
          $item.find(_OrderViewPageMap2.default.productPackModal.product.img).attr('src', item.imagePath);
          $item.find(_OrderViewPageMap2.default.productPackModal.product.name).html(item.name);
          $item.find(_OrderViewPageMap2.default.productPackModal.product.link).attr('href', _this4.router.generate('admin_product_form', { 'id': item.id }));
          if (item.reference !== '') {
            $item.find(_OrderViewPageMap2.default.productPackModal.product.ref).append(item.reference);
          } else {
            $item.find(_OrderViewPageMap2.default.productPackModal.product.ref).remove();
          }
          if (item.supplierReference !== '') {
            $item.find(_OrderViewPageMap2.default.productPackModal.product.supplierRef).append(item.supplierReference);
          } else {
            $item.find(_OrderViewPageMap2.default.productPackModal.product.supplierRef).remove();
          }
          if (item.quantity > 1) {
            $item.find(_OrderViewPageMap2.default.productPackModal.product.quantity + ' span').html(item.quantity);
          } else {
            $item.find(_OrderViewPageMap2.default.productPackModal.product.quantity).html(item.quantity);
          }
          $item.find(_OrderViewPageMap2.default.productPackModal.product.availableQuantity).html(item.availableQuantity);
          $(_OrderViewPageMap2.default.productPackModal.template).before($item);
        });
      });
    }
  }, {
    key: 'listenForProductAdd',
    value: function listenForProductAdd() {
      var _this5 = this;

      $(_OrderViewPageMap2.default.productAddBtn).on('click', function (event) {
        _this5.orderProductRenderer.toggleProductAddNewInvoiceInfo();
        _this5.orderProductRenderer.moveProductsPanelToModificationPosition(_OrderViewPageMap2.default.productSearchInput);
      });
      $(_OrderViewPageMap2.default.productCancelAddBtn).on('click', function (event) {
        return _this5.orderProductRenderer.moveProductPanelToOriginalPosition();
      });
    }
  }, {
    key: 'listenForProductPagination',
    value: function listenForProductPagination() {
      var _this6 = this;

      $(_OrderViewPageMap2.default.productsTablePagination).on('click', _OrderViewPageMap2.default.productsTablePaginationLink, function (event) {
        event.preventDefault();
        var $btn = $(event.currentTarget);
        _eventEmitter.EventEmitter.emit(_orderViewEventMap2.default.productListPaginated, {
          numPage: $btn.data('page')
        });
      });
      $(_OrderViewPageMap2.default.productsTablePaginationNext).on('click', function (event) {
        event.preventDefault();
        var $btn = $(event.currentTarget);
        if ($btn.hasClass('disabled')) {
          return;
        }
        var activePage = _this6.getActivePage();
        _eventEmitter.EventEmitter.emit(_orderViewEventMap2.default.productListPaginated, {
          numPage: parseInt($(activePage).html(), 10) + 1
        });
      });
      $(_OrderViewPageMap2.default.productsTablePaginationPrev).on('click', function (event) {
        event.preventDefault();
        var $btn = $(event.currentTarget);
        if ($btn.hasClass('disabled')) {
          return;
        }
        var activePage = _this6.getActivePage();
        _eventEmitter.EventEmitter.emit(_orderViewEventMap2.default.productListPaginated, {
          numPage: parseInt($(activePage).html(), 10) - 1
        });
      });
      $(_OrderViewPageMap2.default.productsTablePaginationNumberSelector).on('change', function (event) {
        event.preventDefault();
        var $select = $(event.currentTarget);
        var numPerPage = parseInt($select.val(), 10);
        _eventEmitter.EventEmitter.emit(_orderViewEventMap2.default.productListNumberPerPage, {
          numPerPage: numPerPage
        });
      });

      _eventEmitter.EventEmitter.on(_orderViewEventMap2.default.productListPaginated, function (event) {
        _this6.orderProductRenderer.paginate(event.numPage);
        _this6.listenForProductDelete();
        _this6.listenForProductEdit();
        _this6.resetToolTips();
      });

      _eventEmitter.EventEmitter.on(_orderViewEventMap2.default.productListNumberPerPage, function (event) {
        // Update pagination num per page (page links are regenerated)
        _this6.orderProductRenderer.updateNumPerPage(event.numPerPage);

        // Paginate to page 1
        _eventEmitter.EventEmitter.emit(_orderViewEventMap2.default.productListPaginated, {
          numPage: 1
        });

        // Save new config
        $.ajax({
          url: _this6.router.generate('admin_orders_configure_product_pagination'),
          method: 'POST',
          data: { numPerPage: event.numPerPage }
        });
      });
    }
  }, {
    key: 'listenForRefund',
    value: function listenForRefund() {
      var _this7 = this;

      $(_OrderViewPageMap2.default.cancelProduct.buttons.partialRefund).on('click', function () {
        _this7.orderProductRenderer.moveProductsPanelToRefundPosition();
        _this7.orderProductCancel.showPartialRefund();
      });

      $(_OrderViewPageMap2.default.cancelProduct.buttons.standardRefund).on('click', function () {
        _this7.orderProductRenderer.moveProductsPanelToRefundPosition();
        _this7.orderProductCancel.showStandardRefund();
      });

      $(_OrderViewPageMap2.default.cancelProduct.buttons.returnProduct).on('click', function () {
        _this7.orderProductRenderer.moveProductsPanelToRefundPosition();
        _this7.orderProductCancel.showReturnProduct();
      });

      $(_OrderViewPageMap2.default.cancelProduct.buttons.abort).on('click', function () {
        _this7.orderProductRenderer.moveProductPanelToOriginalPosition();
        _this7.orderProductCancel.hideRefund();
      });
    }
  }, {
    key: 'listenForCancelProduct',
    value: function listenForCancelProduct() {
      var _this8 = this;

      $(_OrderViewPageMap2.default.cancelProduct.buttons.cancelProducts).on('click', function (event) {
        _this8.orderProductRenderer.moveProductsPanelToRefundPosition();
        _this8.orderProductCancel.showCancelProductForm();
      });
    }
  }, {
    key: 'getActivePage',
    value: function getActivePage() {
      return $(_OrderViewPageMap2.default.productsTablePagination).find('.active span').get(0);
    }
  }]);
  return OrderViewPage;
}();

exports.default = OrderViewPage;

/***/ }),
/* 420 */,
/* 421 */,
/* 422 */,
/* 423 */,
/* 424 */,
/* 425 */,
/* 426 */,
/* 427 */,
/* 428 */,
/* 429 */,
/* 430 */,
/* 431 */,
/* 432 */,
/* 433 */,
/* 434 */,
/* 435 */,
/* 436 */,
/* 437 */,
/* 438 */,
/* 439 */,
/* 440 */,
/* 441 */,
/* 442 */,
/* 443 */,
/* 444 */,
/* 445 */,
/* 446 */,
/* 447 */,
/* 448 */,
/* 449 */,
/* 450 */,
/* 451 */,
/* 452 */,
/* 453 */,
/* 454 */,
/* 455 */,
/* 456 */,
/* 457 */,
/* 458 */,
/* 459 */,
/* 460 */,
/* 461 */,
/* 462 */,
/* 463 */,
/* 464 */,
/* 465 */,
/* 466 */,
/* 467 */,
/* 468 */,
/* 469 */,
/* 470 */,
/* 471 */,
/* 472 */,
/* 473 */,
/* 474 */,
/* 475 */,
/* 476 */,
/* 477 */,
/* 478 */,
/* 479 */,
/* 480 */,
/* 481 */,
/* 482 */,
/* 483 */,
/* 484 */,
/* 485 */,
/* 486 */,
/* 487 */,
/* 488 */,
/* 489 */,
/* 490 */,
/* 491 */,
/* 492 */,
/* 493 */,
/* 494 */,
/* 495 */,
/* 496 */,
/* 497 */,
/* 498 */,
/* 499 */,
/* 500 */,
/* 501 */,
/* 502 */,
/* 503 */,
/* 504 */,
/* 505 */,
/* 506 */,
/* 507 */,
/* 508 */,
/* 509 */,
/* 510 */,
/* 511 */,
/* 512 */,
/* 513 */,
/* 514 */,
/* 515 */,
/* 516 */,
/* 517 */,
/* 518 */,
/* 519 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * Manages adding/editing note for invoice documents.
 */
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

var InvoiceNoteManager = function () {
  function InvoiceNoteManager() {
    (0, _classCallCheck3.default)(this, InvoiceNoteManager);

    this.setupListeners();
  }

  (0, _createClass3.default)(InvoiceNoteManager, [{
    key: 'setupListeners',
    value: function setupListeners() {
      this._initShowNoteFormEventHandler();
      this._initCloseNoteFormEventHandler();
      this._initEnterPaymentEventHandler();
    }
  }, {
    key: '_initShowNoteFormEventHandler',
    value: function _initShowNoteFormEventHandler() {
      $('.js-open-invoice-note-btn').on('click', function (event) {
        event.preventDefault();
        var $btn = $(event.currentTarget);
        var $noteRow = $btn.closest('tr').next();

        $noteRow.removeClass('d-none');
      });
    }
  }, {
    key: '_initCloseNoteFormEventHandler',
    value: function _initCloseNoteFormEventHandler() {
      $('.js-cancel-invoice-note-btn').on('click', function (event) {
        $(event.currentTarget).closest('tr').addClass('d-none');
      });
    }
  }, {
    key: '_initEnterPaymentEventHandler',
    value: function _initEnterPaymentEventHandler() {
      $('.js-enter-payment-btn').on('click', function (event) {

        var $btn = $(event.currentTarget);
        var paymentAmount = $btn.data('payment-amount');

        $(_OrderViewPageMap2.default.viewOrderPaymentsBlock).get(0).scrollIntoView({ behavior: "smooth" });
        $(_OrderViewPageMap2.default.orderPaymentFormAmountInput).val(paymentAmount);
      });
    }
  }]);
  return InvoiceNoteManager;
}();

exports.default = InvoiceNoteManager;

/***/ }),
/* 520 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

var _orderShippingManager = __webpack_require__(415);

var _orderShippingManager2 = _interopRequireDefault(_orderShippingManager);

var _orderViewPage = __webpack_require__(419);

var _orderViewPage2 = _interopRequireDefault(_orderViewPage);

var _orderProductAddAutocomplete = __webpack_require__(417);

var _orderProductAddAutocomplete2 = _interopRequireDefault(_orderProductAddAutocomplete);

var _orderProductAdd = __webpack_require__(418);

var _orderProductAdd2 = _interopRequireDefault(_orderProductAdd);

var _orderViewPageMessagesHandler = __webpack_require__(414);

var _orderViewPageMessagesHandler2 = _interopRequireDefault(_orderViewPageMessagesHandler);

var _textWithLengthCounter = __webpack_require__(378);

var _textWithLengthCounter2 = _interopRequireDefault(_textWithLengthCounter);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
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

$(function () {
  var DISCOUNT_TYPE_AMOUNT = 'amount';
  var DISCOUNT_TYPE_PERCENT = 'percent';
  var DISCOUNT_TYPE_FREE_SHIPPING = 'free_shipping';

  new _orderShippingManager2.default();
  new _textWithLengthCounter2.default();
  var orderViewPage = new _orderViewPage2.default();
  var orderAddAutocomplete = new _orderProductAddAutocomplete2.default($(_OrderViewPageMap2.default.productSearchInput));
  var orderAdd = new _orderProductAdd2.default();

  orderViewPage.listenForProductPack();
  orderViewPage.listenForProductDelete();
  orderViewPage.listenForProductEdit();
  orderViewPage.listenForProductAdd();
  orderViewPage.listenForProductPagination();
  orderViewPage.listenForRefund();
  orderViewPage.listenForCancelProduct();

  orderAddAutocomplete.listenForSearch();
  orderAddAutocomplete.onItemClickedCallback = function (product) {
    return orderAdd.setProduct(product);
  };

  handlePaymentDetailsToggle();
  handlePrivateNoteChange();
  handleUpdateOrderStatusButton();

  var orderViewPageMessageHandler = new _orderViewPageMessagesHandler2.default();
  orderViewPageMessageHandler.listenForPredefinedMessageSelection();
  orderViewPageMessageHandler.listenForFullMessagesOpen();
  $(_OrderViewPageMap2.default.privateNoteToggleBtn).on('click', function (event) {
    event.preventDefault();
    togglePrivateNoteBlock();
  });

  $(_OrderViewPageMap2.default.printOrderViewPageButton).on('click', function () {
    var tempTitle = document.title;
    document.title = $(_OrderViewPageMap2.default.mainDiv).data('orderTitle');
    window.print();
    document.title = tempTitle;
  });

  initAddCartRuleFormHandler();
  initChangeAddressFormHandler();
  initHookTabs();

  function initHookTabs() {
    $(_OrderViewPageMap2.default.orderHookTabsContainer).find('.nav-tabs li:first-child a').tab('show');
  }

  function handlePaymentDetailsToggle() {
    $(_OrderViewPageMap2.default.orderPaymentDetailsBtn).on('click', function (event) {
      var $paymentDetailRow = $(event.currentTarget).closest('tr').next(':first');

      $paymentDetailRow.toggleClass('d-none');
    });
  }

  function togglePrivateNoteBlock() {
    var $block = $(_OrderViewPageMap2.default.privateNoteBlock);
    var $btn = $(_OrderViewPageMap2.default.privateNoteToggleBtn);
    var isPrivateNoteOpened = $btn.hasClass('is-opened');

    if (isPrivateNoteOpened) {
      $btn.removeClass('is-opened');
      $block.addClass('d-none');
    } else {
      $btn.addClass('is-opened');
      $block.removeClass('d-none');
    }

    var $icon = $btn.find('.material-icons');
    $icon.text(isPrivateNoteOpened ? 'add' : 'remove');
  }

  function handlePrivateNoteChange() {
    var $submitBtn = $(_OrderViewPageMap2.default.privateNoteSubmitBtn);

    $(_OrderViewPageMap2.default.privateNoteInput).on('input', function () {
      $submitBtn.prop('disabled', false);
    });
  }

  function initAddCartRuleFormHandler() {
    var $modal = $(_OrderViewPageMap2.default.addCartRuleModal);
    var $form = $modal.find('form');
    var $valueHelp = $modal.find(_OrderViewPageMap2.default.cartRuleHelpText);
    var $valueInput = $form.find(_OrderViewPageMap2.default.addCartRuleValueInput);
    var $valueFormGroup = $valueInput.closest('.form-group');

    $form.find(_OrderViewPageMap2.default.addCartRuleTypeSelect).on('change', function (event) {
      var selectedCartRuleType = $(event.currentTarget).val();
      var $valueUnit = $form.find(_OrderViewPageMap2.default.addCartRuleValueUnit);

      if (selectedCartRuleType === DISCOUNT_TYPE_AMOUNT) {
        $valueHelp.removeClass('d-none');
        $valueUnit.html($valueUnit.data('currencySymbol'));
      } else {
        $valueHelp.addClass('d-none');
      }

      if (selectedCartRuleType === DISCOUNT_TYPE_PERCENT) {
        $valueUnit.html('%');
      }

      if (selectedCartRuleType === DISCOUNT_TYPE_FREE_SHIPPING) {
        $valueFormGroup.addClass('d-none');
        $valueInput.attr('disabled', true);
      } else {
        $valueFormGroup.removeClass('d-none');
        $valueInput.attr('disabled', false);
      }
    });
  }

  function handleUpdateOrderStatusButton() {
    var $btn = $(_OrderViewPageMap2.default.updateOrderStatusActionBtn);
    var $wrapper = $(_OrderViewPageMap2.default.updateOrderStatusActionInputWrapper);

    $(_OrderViewPageMap2.default.updateOrderStatusActionInput).on('change', function (event) {
      var $element = $(event.currentTarget);
      var $option = $('option:selected', $element);
      var selectedOrderStatusId = $element.val();

      $wrapper.css('background-color', $option.data('background-color'));
      $wrapper.toggleClass('is-bright', $option.data('is-bright') !== undefined);

      $btn.prop('disabled', parseInt(selectedOrderStatusId, 10) === $btn.data('orderStatusId'));
    });
  }

  function initChangeAddressFormHandler() {
    var $modal = $(_OrderViewPageMap2.default.updateCustomerAddressModal);

    $(_OrderViewPageMap2.default.openOrderAddressUpdateModalBtn).on('click', function (event) {
      $modal.find(_OrderViewPageMap2.default.updateOrderAddressTypeInput).val($(event.currentTarget).data('addressType'));
    });
  }
});

/***/ }),
/* 521 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

var $ = window.$;

var OrderDiscountsRefresher = function () {
  function OrderDiscountsRefresher() {
    (0, _classCallCheck3.default)(this, OrderDiscountsRefresher);

    this.router = new _router2.default();
  }

  (0, _createClass3.default)(OrderDiscountsRefresher, [{
    key: 'refresh',
    value: function refresh(orderId) {
      $.ajax(this.router.generate('admin_orders_get_discounts', { orderId: orderId })).then(function (response) {
        $(_OrderViewPageMap2.default.productDiscountList.list).replaceWith(response);
      });
    }
  }]);
  return OrderDiscountsRefresher;
}();

exports.default = OrderDiscountsRefresher;

/***/ }),
/* 522 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

var _invoiceNoteManager = __webpack_require__(519);

var _invoiceNoteManager2 = _interopRequireDefault(_invoiceNoteManager);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
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

var OrderDocumentsRefresher = function () {
  function OrderDocumentsRefresher() {
    (0, _classCallCheck3.default)(this, OrderDocumentsRefresher);

    this.router = new _router2.default();
    this.invoiceNoteManager = new _invoiceNoteManager2.default();
  }

  (0, _createClass3.default)(OrderDocumentsRefresher, [{
    key: 'refresh',
    value: function refresh(orderId) {
      var _this = this;

      $.ajax(this.router.generate('admin_orders_get_documents', { orderId: orderId })).then(function (response) {
        $(_OrderViewPageMap2.default.orderDocumentsTabCount).text(response.total);
        $(_OrderViewPageMap2.default.orderDocumentsTabBody).html(response.html);
        _this.invoiceNoteManager.setupListeners();
      });
    }
  }]);
  return OrderDocumentsRefresher;
}();

exports.default = OrderDocumentsRefresher;

/***/ }),
/* 523 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _keys = __webpack_require__(68);

var _keys2 = _interopRequireDefault(_keys);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

var $ = window.$;

var OrderInvoicesRefresher = function () {
  function OrderInvoicesRefresher() {
    (0, _classCallCheck3.default)(this, OrderInvoicesRefresher);

    this.router = new _router2.default();
  }

  (0, _createClass3.default)(OrderInvoicesRefresher, [{
    key: 'refresh',
    value: function refresh(orderId) {
      $.ajax(this.router.generate('admin_orders_get_invoices', { orderId: orderId })).then(function (response) {
        if (!response || !response.invoices || (0, _keys2.default)(response.invoices).length <= 0) {
          return;
        }

        var $paymentInvoiceSelect = $(_OrderViewPageMap2.default.orderPaymentInvoiceSelect);
        var $addProductInvoiceSelect = $(_OrderViewPageMap2.default.productAddInvoiceSelect);
        var $existingInvoicesGroup = $addProductInvoiceSelect.find('optgroup:first');
        var $productEditInvoiceSelect = $(_OrderViewPageMap2.default.productEditInvoiceSelect);
        var $addDiscountInvoiceSelect = $(_OrderViewPageMap2.default.addCartRuleInvoiceIdSelect);
        $existingInvoicesGroup.empty();
        $paymentInvoiceSelect.empty();
        $productEditInvoiceSelect.empty();
        $addDiscountInvoiceSelect.empty();

        (0, _keys2.default)(response.invoices).forEach(function (invoiceName) {
          var invoiceId = response.invoices[invoiceName];
          var invoiceNameWithoutPrice = invoiceName.split(' - ')[0];

          $existingInvoicesGroup.append('<option value="' + invoiceId + '">' + invoiceNameWithoutPrice + '</option>');
          $paymentInvoiceSelect.append('<option value="' + invoiceId + '">' + invoiceNameWithoutPrice + '</option>');
          $productEditInvoiceSelect.append('<option value="' + invoiceId + '">' + invoiceNameWithoutPrice + '</option>');
          $addDiscountInvoiceSelect.append('<option value="' + invoiceId + '">' + invoiceName + '</option>');
        });

        document.querySelector(_OrderViewPageMap2.default.productAddInvoiceSelect).selectedIndex = 0;
      });
    }
  }]);
  return OrderInvoicesRefresher;
}();

exports.default = OrderInvoicesRefresher;

/***/ }),
/* 524 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _isNan = __webpack_require__(231);

var _isNan2 = _interopRequireDefault(_isNan);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

var _cldr = __webpack_require__(157);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var _window = window,
    $ = _window.$;

/**
 * manages all product cancel actions, that includes all refund operations
 */
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

var OrderProductCancel = function () {
  function OrderProductCancel() {
    (0, _classCallCheck3.default)(this, OrderProductCancel);

    this.router = new _router2.default();
    this.cancelProductForm = $(_OrderViewPageMap2.default.cancelProduct.form);
    this.orderId = this.cancelProductForm.data('orderId');
    this.orderDelivered = parseInt(this.cancelProductForm.data('isDelivered'), 10) === 1;
    this.isTaxIncluded = parseInt(this.cancelProductForm.data('isTaxIncluded'), 10) === 1;
    this.discountsAmount = parseFloat(this.cancelProductForm.data('discountsAmount'));
    this.currencyFormatter = _cldr.NumberFormatter.build(this.cancelProductForm.data('priceSpecification'));
    this.useAmountInputs = true;
    this.listenForInputs();
  }

  (0, _createClass3.default)(OrderProductCancel, [{
    key: 'showPartialRefund',
    value: function showPartialRefund() {
      // Always start by hiding elements then show the others, since some elements are common
      this.hideCancelElements();
      $(_OrderViewPageMap2.default.cancelProduct.toggle.partialRefund).show();
      this.useAmountInputs = true;
      this.initForm($(_OrderViewPageMap2.default.cancelProduct.buttons.save).data('partialRefundLabel'), this.router.generate('admin_orders_partial_refund', { orderId: this.orderId }), 'partial-refund');
    }
  }, {
    key: 'showStandardRefund',
    value: function showStandardRefund() {
      // Always start by hiding elements then show the others, since some elements are common
      this.hideCancelElements();
      $(_OrderViewPageMap2.default.cancelProduct.toggle.standardRefund).show();
      this.useAmountInputs = false;
      this.initForm($(_OrderViewPageMap2.default.cancelProduct.buttons.save).data('standardRefundLabel'), this.router.generate('admin_orders_standard_refund', { orderId: this.orderId }), 'standard-refund');
    }
  }, {
    key: 'showReturnProduct',
    value: function showReturnProduct() {
      // Always start by hiding elements then show the others, since some elements are common
      this.hideCancelElements();
      $(_OrderViewPageMap2.default.cancelProduct.toggle.returnProduct).show();
      this.useAmountInputs = false;
      this.initForm($(_OrderViewPageMap2.default.cancelProduct.buttons.save).data('returnProductLabel'), this.router.generate('admin_orders_return_product', { orderId: this.orderId }), 'return-product');
    }
  }, {
    key: 'hideRefund',
    value: function hideRefund() {
      this.hideCancelElements();
      $(_OrderViewPageMap2.default.cancelProduct.table.actions).show();
    }
  }, {
    key: 'hideCancelElements',
    value: function hideCancelElements() {
      $(_OrderViewPageMap2.default.cancelProduct.toggle.standardRefund).hide();
      $(_OrderViewPageMap2.default.cancelProduct.toggle.partialRefund).hide();
      $(_OrderViewPageMap2.default.cancelProduct.toggle.returnProduct).hide();
      $(_OrderViewPageMap2.default.cancelProduct.table.actions).hide();
    }
  }, {
    key: 'initForm',
    value: function initForm(actionName, formAction, formClass) {
      this.updateVoucherRefund();

      this.cancelProductForm.prop('action', formAction);
      this.cancelProductForm.removeClass('standard-refund partial-refund return-product cancel-product').addClass(formClass);
      $(_OrderViewPageMap2.default.cancelProduct.buttons.save).html(actionName);
      $(_OrderViewPageMap2.default.cancelProduct.table.header).html(actionName);
      $(_OrderViewPageMap2.default.cancelProduct.checkboxes.restock).prop('checked', this.orderDelivered);
      $(_OrderViewPageMap2.default.cancelProduct.checkboxes.creditSlip).prop('checked', true);
      $(_OrderViewPageMap2.default.cancelProduct.checkboxes.voucher).prop('checked', false);
    }
  }, {
    key: 'listenForInputs',
    value: function listenForInputs() {
      var _this = this;

      $(document).on('change', _OrderViewPageMap2.default.cancelProduct.inputs.quantity, function (event) {
        var $productQuantityInput = $(event.target);
        if (_this.useAmountInputs) {
          _this.updateAmountInput($productQuantityInput);
        }
        _this.updateVoucherRefund();
      });

      $(document).on('change', _OrderViewPageMap2.default.cancelProduct.inputs.amount, function () {
        _this.updateVoucherRefund();
      });

      $(document).on('change', _OrderViewPageMap2.default.cancelProduct.inputs.selector, function (event) {
        var $productCheckbox = $(event.target);
        var $parentCell = $productCheckbox.parents(_OrderViewPageMap2.default.cancelProduct.table.cell);
        var productQuantityInput = $parentCell.find(_OrderViewPageMap2.default.cancelProduct.inputs.quantity);
        var refundableQuantity = parseInt(productQuantityInput.data('quantityRefundable'), 10);
        var productQuantity = parseInt(productQuantityInput.val(), 10);
        if (!$productCheckbox.is(':checked')) {
          productQuantityInput.val(0);
        } else if ((0, _isNan2.default)(productQuantity) || productQuantity === 0) {
          productQuantityInput.val(refundableQuantity);
        }
        _this.updateVoucherRefund();
      });
    }
  }, {
    key: 'updateAmountInput',
    value: function updateAmountInput($productQuantityInput) {
      var $parentCell = $productQuantityInput.parents(_OrderViewPageMap2.default.cancelProduct.table.cell);
      var $productAmount = $parentCell.find(_OrderViewPageMap2.default.cancelProduct.inputs.amount);
      var productQuantity = parseInt($productQuantityInput.val(), 10);
      if (productQuantity <= 0) {
        $productAmount.val(0);

        return;
      }

      var priceFieldName = this.isTaxIncluded ? 'productPriceTaxIncl' : 'productPriceTaxExcl';
      var productUnitPrice = parseFloat($productQuantityInput.data(priceFieldName));
      var amountRefundable = parseFloat($productQuantityInput.data('amountRefundable'));
      var guessedAmount = productUnitPrice * productQuantity < amountRefundable ? productUnitPrice * productQuantity : amountRefundable;
      var amountValue = parseFloat($productAmount.val());
      if ($productAmount.val() === '' || amountValue === 0 || amountValue > guessedAmount) {
        $productAmount.val(guessedAmount);
      }
    }
  }, {
    key: 'getRefundAmount',
    value: function getRefundAmount() {
      var _this2 = this;

      var totalAmount = 0;

      if (this.useAmountInputs) {
        $(_OrderViewPageMap2.default.cancelProduct.inputs.amount).each(function (index, amount) {
          var floatValue = parseFloat(amount.value);
          totalAmount += !(0, _isNan2.default)(floatValue) ? floatValue : 0;
        });
      } else {
        $(_OrderViewPageMap2.default.cancelProduct.inputs.quantity).each(function (index, quantity) {
          var $quantityInput = $(quantity);
          var priceFieldName = _this2.isTaxIncluded ? 'productPriceTaxIncl' : 'productPriceTaxExcl';
          var productUnitPrice = parseFloat($quantityInput.data(priceFieldName));
          var productQuantity = parseInt($quantityInput.val(), 10);
          totalAmount += productQuantity * productUnitPrice;
        });
      }

      return totalAmount;
    }
  }, {
    key: 'updateVoucherRefund',
    value: function updateVoucherRefund() {
      var refundAmount = this.getRefundAmount();

      this.updateVoucherRefundTypeLabel($(_OrderViewPageMap2.default.cancelProduct.radios.voucherRefundType.productPrices), refundAmount);
      var refundVoucherExcluded = refundAmount - this.discountsAmount;
      this.updateVoucherRefundTypeLabel($(_OrderViewPageMap2.default.cancelProduct.radios.voucherRefundType.productPricesVoucherExcluded), refundVoucherExcluded);

      // Disable voucher excluded option when the voucher amount is too high
      if (refundVoucherExcluded < 0) {
        $(_OrderViewPageMap2.default.cancelProduct.radios.voucherRefundType.productPricesVoucherExcluded).prop('checked', false).prop('disabled', true);
        $(_OrderViewPageMap2.default.cancelProduct.radios.voucherRefundType.productPrices).prop('checked', true);
        $(_OrderViewPageMap2.default.cancelProduct.radios.voucherRefundType.negativeErrorMessage).show();
      } else {
        $(_OrderViewPageMap2.default.cancelProduct.radios.voucherRefundType.productPricesVoucherExcluded).prop('disabled', false);
        $(_OrderViewPageMap2.default.cancelProduct.radios.voucherRefundType.negativeErrorMessage).hide();
      }
    }
  }, {
    key: 'updateVoucherRefundTypeLabel',
    value: function updateVoucherRefundTypeLabel($input, refundAmount) {
      var defaultLabel = $input.data('defaultLabel');
      var $label = $input.parents('label');
      var formattedAmount = this.currencyFormatter.format(refundAmount);

      // Change the ending text part only to avoid removing the input (the EOL is on purpose for better display)
      $label.get(0).lastChild.nodeValue = '\n    ' + defaultLabel + ' ' + formattedAmount;
    }
  }, {
    key: 'showCancelProductForm',
    value: function showCancelProductForm() {
      var cancelProductRoute = this.router.generate('admin_orders_cancellation', { orderId: this.orderId });
      this.initForm($(_OrderViewPageMap2.default.cancelProduct.buttons.save).data('cancelLabel'), cancelProductRoute, 'cancel-product');
      this.hideCancelElements();
      $(_OrderViewPageMap2.default.cancelProduct.toggle.cancelProducts).show();
    }
  }]);
  return OrderProductCancel;
}();

exports.default = OrderProductCancel;

/***/ }),
/* 525 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

var _eventEmitter = __webpack_require__(36);

var _orderViewEventMap = __webpack_require__(192);

var _orderViewEventMap2 = _interopRequireDefault(_orderViewEventMap);

var _orderPrices = __webpack_require__(229);

var _orderPrices2 = _interopRequireDefault(_orderPrices);

var _modal = __webpack_require__(45);

var _modal2 = _interopRequireDefault(_modal);

var _orderPricesRefresher = __webpack_require__(199);

var _orderPricesRefresher2 = _interopRequireDefault(_orderPricesRefresher);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var _window = window,
    $ = _window.$; /**
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

var OrderProductEdit = function () {
  function OrderProductEdit(orderDetailId) {
    (0, _classCallCheck3.default)(this, OrderProductEdit);

    this.router = new _router2.default();
    this.orderDetailId = orderDetailId;
    this.productRow = $('#orderProduct_' + this.orderDetailId);
    this.product = {};
    this.currencyPrecision = $(_OrderViewPageMap2.default.productsTable).data('currencyPrecision');
    this.priceTaxCalculator = new _orderPrices2.default();
    this.productEditSaveBtn = $(_OrderViewPageMap2.default.productEditSaveBtn);
    this.quantityInput = $(_OrderViewPageMap2.default.productEditQuantityInput);
    this.orderPricesRefresher = new _orderPricesRefresher2.default();
  }

  (0, _createClass3.default)(OrderProductEdit, [{
    key: 'setupListener',
    value: function setupListener() {
      var _this = this;

      this.quantityInput.on('change keyup', function (event) {
        var newQuantity = Number(event.target.value);
        var availableQuantity = parseInt($(event.currentTarget).data('availableQuantity'), 10);
        var previousQuantity = parseInt(_this.quantityInput.data('previousQuantity'), 10);
        var remainingAvailable = availableQuantity - (newQuantity - previousQuantity);
        var availableOutOfStock = _this.availableText.data('availableOutOfStock');
        _this.availableText.text(remainingAvailable);
        _this.availableText.toggleClass('text-danger font-weight-bold', remainingAvailable < 0);
        _this.updateTotal();
        var disableEditActionBtn = newQuantity <= 0 || remainingAvailable < 0 && !availableOutOfStock;
        _this.productEditSaveBtn.prop('disabled', disableEditActionBtn);
      });

      this.productEditInvoiceSelect.on('change', function () {
        _this.productEditSaveBtn.prop('disabled', false);
      });

      this.priceTaxIncludedInput.on('change keyup', function (event) {
        _this.taxIncluded = parseFloat(event.target.value);
        var taxExcluded = _this.priceTaxCalculator.calculateTaxExcluded(_this.taxIncluded, _this.taxRate, _this.currencyPrecision);
        _this.priceTaxExcludedInput.val(taxExcluded);
        _this.updateTotal();
      });

      this.priceTaxExcludedInput.on('change keyup', function (event) {
        var taxExcluded = parseFloat(event.target.value);
        _this.taxIncluded = _this.priceTaxCalculator.calculateTaxIncluded(taxExcluded, _this.taxRate, _this.currencyPrecision);
        _this.priceTaxIncludedInput.val(_this.taxIncluded);
        _this.updateTotal();
      });

      this.productEditSaveBtn.on('click', function (event) {
        var $btn = $(event.currentTarget);
        var confirmed = window.confirm($btn.data('updateMessage'));

        if (!confirmed) {
          return;
        }

        $btn.prop('disabled', true);
        _this.handleEditProductWithConfirmationModal(event);
      });

      this.productEditCancelBtn.on('click', function () {
        _eventEmitter.EventEmitter.emit(_orderViewEventMap2.default.productEditionCanceled, {
          orderDetailId: _this.orderDetailId
        });
      });
    }
  }, {
    key: 'updateTotal',
    value: function updateTotal() {
      var updatedTotal = this.priceTaxCalculator.calculateTotalPrice(this.quantity, this.taxIncluded, this.currencyPrecision);
      this.priceTotalText.html(updatedTotal);
      this.productEditSaveBtn.prop('disabled', updatedTotal === this.initialTotal);
    }
  }, {
    key: 'displayProduct',
    value: function displayProduct(product) {
      this.productRowEdit = $(_OrderViewPageMap2.default.productEditRowTemplate).clone(true);
      this.productRowEdit.attr('id', 'editOrderProduct_' + this.orderDetailId);
      this.productRowEdit.find('*[id]').each(function removeAllIds() {
        $(this).removeAttr('id');
      });

      // Find controls
      this.productEditSaveBtn = this.productRowEdit.find(_OrderViewPageMap2.default.productEditSaveBtn);
      this.productEditCancelBtn = this.productRowEdit.find(_OrderViewPageMap2.default.productEditCancelBtn);
      this.productEditInvoiceSelect = this.productRowEdit.find(_OrderViewPageMap2.default.productEditInvoiceSelect);
      this.productEditImage = this.productRowEdit.find(_OrderViewPageMap2.default.productEditImage);
      this.productEditName = this.productRowEdit.find(_OrderViewPageMap2.default.productEditName);
      this.priceTaxIncludedInput = this.productRowEdit.find(_OrderViewPageMap2.default.productEditPriceTaxInclInput);
      this.priceTaxExcludedInput = this.productRowEdit.find(_OrderViewPageMap2.default.productEditPriceTaxExclInput);
      this.quantityInput = this.productRowEdit.find(_OrderViewPageMap2.default.productEditQuantityInput);
      this.locationText = this.productRowEdit.find(_OrderViewPageMap2.default.productEditLocationText);
      this.availableText = this.productRowEdit.find(_OrderViewPageMap2.default.productEditAvailableText);
      this.priceTotalText = this.productRowEdit.find(_OrderViewPageMap2.default.productEditTotalPriceText);

      // Init input values
      this.priceTaxExcludedInput.val(window.ps_round(product.price_tax_excl, this.currencyPrecision));

      this.priceTaxIncludedInput.val(window.ps_round(product.price_tax_incl, this.currencyPrecision));

      this.quantityInput.val(product.quantity).data('availableQuantity', product.availableQuantity).data('previousQuantity', product.quantity);
      this.availableText.data('availableOutOfStock', product.availableOutOfStock);

      // set this product's orderInvoiceId as selected
      if (product.orderInvoiceId) {
        this.productEditInvoiceSelect.val(product.orderInvoiceId);
      }

      // Init editor data
      this.taxRate = product.tax_rate;
      this.initialTotal = this.priceTaxCalculator.calculateTotalPrice(product.quantity, product.price_tax_incl, this.currencyPrecision);
      this.quantity = product.quantity;
      this.taxIncluded = product.price_tax_incl;

      // Copy product content in cells
      this.productEditImage.html(this.productRow.find(_OrderViewPageMap2.default.productEditImage).html());
      this.productEditName.html(this.productRow.find(_OrderViewPageMap2.default.productEditName).html());
      this.locationText.html(product.location);
      this.availableText.html(product.availableQuantity);
      this.priceTotalText.html(this.initialTotal);
      this.productRow.addClass('d-none').after(this.productRowEdit.removeClass('d-none'));

      this.setupListener();
    }
  }, {
    key: 'handleEditProductWithConfirmationModal',
    value: function handleEditProductWithConfirmationModal(event) {
      var _this2 = this;

      var productEditBtn = $('#orderProduct_' + this.orderDetailId + ' ' + _OrderViewPageMap2.default.productEditButtons);
      var productId = productEditBtn.data('product-id');
      var combinationId = productEditBtn.data('combination-id');
      var orderInvoiceId = productEditBtn.data('order-invoice-id');
      var productPriceMatch = this.orderPricesRefresher.checkOtherProductPricesMatch(this.priceTaxIncludedInput.val(), productId, combinationId, orderInvoiceId, this.orderDetailId);

      if (productPriceMatch) {
        this.editProduct($(event.currentTarget).data('orderId'), this.orderDetailId);

        return;
      }

      var modalEditPrice = new _modal2.default({
        id: 'modal-confirm-new-price',
        confirmTitle: this.productEditInvoiceSelect.data('modal-edit-price-title'),
        confirmMessage: this.productEditInvoiceSelect.data('modal-edit-price-body'),
        confirmButtonLabel: this.productEditInvoiceSelect.data(' '),
        closeButtonLabel: this.productEditInvoiceSelect.data('modal-edit-price-cancel')
      }, function () {
        _this2.editProduct($(event.currentTarget).data('orderId'), _this2.orderDetailId);
      });

      modalEditPrice.show();
    }
  }, {
    key: 'editProduct',
    value: function editProduct(orderId, orderDetailId) {
      var params = {
        price_tax_incl: this.priceTaxIncludedInput.val(),
        price_tax_excl: this.priceTaxExcludedInput.val(),
        quantity: this.quantityInput.val(),
        invoice: this.productEditInvoiceSelect.val()
      };

      $.ajax({
        url: this.router.generate('admin_orders_update_product', {
          orderId: orderId,
          orderDetailId: orderDetailId
        }),
        method: 'POST',
        data: params
      }).then(function (response) {
        _eventEmitter.EventEmitter.emit(_orderViewEventMap2.default.productUpdated, {
          orderId: orderId,
          orderDetailId: orderDetailId,
          newRow: response
        });
      }, function (response) {
        if (response.responseJSON && response.responseJSON.message) {
          $.growl.error({ message: response.responseJSON.message });
        }
      });
    }
  }]);
  return OrderProductEdit;
}();

exports.default = OrderProductEdit;

/***/ }),
/* 526 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _eventEmitter = __webpack_require__(36);

var _orderViewEventMap = __webpack_require__(192);

var _orderViewEventMap2 = _interopRequireDefault(_orderViewEventMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
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

var OrderProductManager = function () {
  function OrderProductManager() {
    (0, _classCallCheck3.default)(this, OrderProductManager);

    this.router = new _router2.default();
  }

  (0, _createClass3.default)(OrderProductManager, [{
    key: 'handleDeleteProductEvent',
    value: function handleDeleteProductEvent(event) {
      event.preventDefault();

      var $btn = $(event.currentTarget);
      var confirmed = window.confirm($btn.data('deleteMessage'));
      if (!confirmed) {
        return;
      }

      $btn.pstooltip('dispose');
      $btn.prop('disabled', true);
      this.deleteProduct($btn.data('orderId'), $btn.data('orderDetailId'));
    }
  }, {
    key: 'deleteProduct',
    value: function deleteProduct(orderId, orderDetailId) {
      $.ajax(this.router.generate('admin_orders_delete_product', { orderId: orderId, orderDetailId: orderDetailId }), {
        method: 'POST'
      }).then(function () {
        _eventEmitter.EventEmitter.emit(_orderViewEventMap2.default.productDeletedFromOrder, {
          oldOrderDetailId: orderDetailId,
          orderId: orderId
        });
      }, function (response) {
        if (response.message) {
          $.growl.error({ message: response.message });
        }
      });
    }
  }]);
  return OrderProductManager;
}();

exports.default = OrderProductManager;

/***/ }),
/* 527 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

var _window = window,
    $ = _window.$;

var OrderProductsRefresher = function () {
  function OrderProductsRefresher() {
    (0, _classCallCheck3.default)(this, OrderProductsRefresher);

    this.router = new _router2.default();
  }

  (0, _createClass3.default)(OrderProductsRefresher, [{
    key: 'refresh',
    value: function refresh(orderId) {
      var _this = this;

      $.ajax(this.router.generate('admin_orders_get_products', { orderId: orderId })).then(function (response) {
        var $orderProducts = response.products;
        var $orderDetailIds = $orderProducts.map(function (product) {
          return product.orderDetailId;
        });

        // Remove products that don't belong to the order anymore
        var orderDetailRows = document.querySelectorAll('tr.cellProduct');
        orderDetailRows.forEach(function (orderDetailRow) {
          var $orderDetailRowId = parseInt($(orderDetailRow).attr('id').match(/\d+/g)[0], 10);

          if (!$orderDetailIds.includes($orderDetailRowId)) {
            _this.removeProductRow($orderDetailRowId);
          }
        });

        // Add products that are not displayed
        // Page needs to be refreshed ?
      });
    }
  }, {
    key: 'removeProductRow',
    value: function removeProductRow(orderDetailRowId) {
      // Remove the row
      var $row = $(_OrderViewPageMap2.default.productsTableRow(orderDetailRowId));
      var $next = $row.next();
      $row.remove();
      if ($next.hasClass('order-product-customization')) {
        $next.remove();
      }
    }
  }]);
  return OrderProductsRefresher;
}();

exports.default = OrderProductsRefresher;

/***/ }),
/* 528 */,
/* 529 */,
/* 530 */,
/* 531 */,
/* 532 */,
/* 533 */,
/* 534 */,
/* 535 */,
/* 536 */,
/* 537 */,
/* 538 */,
/* 539 */,
/* 540 */,
/* 541 */,
/* 542 */,
/* 543 */,
/* 544 */,
/* 545 */,
/* 546 */,
/* 547 */,
/* 548 */,
/* 549 */,
/* 550 */,
/* 551 */,
/* 552 */,
/* 553 */,
/* 554 */,
/* 555 */,
/* 556 */,
/* 557 */,
/* 558 */,
/* 559 */,
/* 560 */,
/* 561 */,
/* 562 */,
/* 563 */,
/* 564 */,
/* 565 */,
/* 566 */,
/* 567 */,
/* 568 */,
/* 569 */,
/* 570 */,
/* 571 */,
/* 572 */,
/* 573 */,
/* 574 */,
/* 575 */,
/* 576 */,
/* 577 */,
/* 578 */,
/* 579 */,
/* 580 */,
/* 581 */,
/* 582 */,
/* 583 */,
/* 584 */,
/* 585 */,
/* 586 */,
/* 587 */,
/* 588 */,
/* 589 */,
/* 590 */,
/* 591 */,
/* 592 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(601);
module.exports = __webpack_require__(3).Number.isNaN;

/***/ }),
/* 593 */,
/* 594 */,
/* 595 */,
/* 596 */,
/* 597 */,
/* 598 */,
/* 599 */,
/* 600 */,
/* 601 */
/***/ (function(module, exports, __webpack_require__) {

// 20.1.2.4 Number.isNaN(number)
var $export = __webpack_require__(8);

$export($export.S, 'Number', {
  isNaN: function isNaN(number){
    return number != number;
  }
});

/***/ })
/******/ ]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgYWUzMjBmMTEyMTliN2E2YjRmMzQiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY2xhc3NDYWxsQ2hlY2suanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY3JlYXRlQ2xhc3MuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZGVzY3JpcHRvcnMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY29yZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pcy1vYmplY3QuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZ2xvYmFsLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1kcC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19mYWlscy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19leHBvcnQuanMiLCJ3ZWJwYWNrOi8vLyh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oaWRlLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2FuLW9iamVjdC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19wcm9wZXJ0eS1kZXNjLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLXByaW1pdGl2ZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jdHguanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZG9tLWNyZWF0ZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pZTgtZG9tLWRlZmluZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hLWZ1bmN0aW9uLmpzIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5LmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLWlvYmplY3QuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faGFzLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3drcy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3Qta2V5cy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2V2ZW50LWVtaXR0ZXIuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZGVmaW5lZC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1pbnRlZ2VyLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3VpZC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL21vZGFsLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLW9iamVjdC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19zaGFyZWQta2V5LmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvZi5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19lbnVtLWJ1Zy1rZXlzLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NoYXJlZC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pb2JqZWN0LmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1waWUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9ldmVudHMvZXZlbnRzLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXJhdG9ycy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3Qta2V5cy1pbnRlcm5hbC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1sZW5ndGguanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdvcHMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYXJyYXktaW5jbHVkZXMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8taW5kZXguanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fc2V0LXRvLXN0cmluZy10YWcuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fbGlicmFyeS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5zdHJpbmcuaXRlcmF0b3IuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9yb3V0ZXIuanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2tleXMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fd2tzLWRlZmluZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL193a3MtZXh0LmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1jcmVhdGUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy93ZWIuZG9tLml0ZXJhYmxlLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXItZGVmaW5lLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1zYXAuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fcmVkZWZpbmUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2Fzc2lnbi5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvYXNzaWduLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9rZXlzLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1hc3NpZ24uanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdvcG4uanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LmFzc2lnbi5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3Qua2V5cy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZ3BvLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL09yZGVyVmlld1BhZ2VNYXAuanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvdHlwZW9mLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NsYXNzb2YuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faHRtbC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZ29wZC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hZGQtdG8tdW5zY29wYWJsZXMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXRlci1jcmVhdGUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXRlci1zdGVwLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1kcHMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fc3RyaW5nLWF0LmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvY29yZS5nZXQtaXRlcmF0b3ItbWV0aG9kLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2LmFycmF5Lml0ZXJhdG9yLmpzIiwid2VicGFjazovLy8uL2pzL2FwcC9jbGRyL251bWJlci1zeW1ib2wuanMiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL2NsZHIvc3BlY2lmaWNhdGlvbnMvbnVtYmVyLmpzIiwid2VicGFjazovLy8uL2pzL2FwcC9jbGRyL2V4Y2VwdGlvbi9sb2NhbGl6YXRpb24uanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvc3ltYm9sLmpzIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL3N5bWJvbC9pdGVyYXRvci5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9mbi9zeW1ib2wvaW5kZXguanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vc3ltYm9sL2l0ZXJhdG9yLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2VudW0ta2V5cy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pcy1hcnJheS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19rZXlvZi5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19tZXRhLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1nb3BuLWV4dC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5zeW1ib2wuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczcuc3ltYm9sLmFzeW5jLWl0ZXJhdG9yLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM3LnN5bWJvbC5vYnNlcnZhYmxlLmpzIiwid2VicGFjazovLy8uL2pzL2FwcC9jbGRyL3NwZWNpZmljYXRpb25zL3ByaWNlLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2lzLWFycmF5LWl0ZXIuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXRlci1jYWxsLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXItZGV0ZWN0LmpzIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL2FycmF5L2Zyb20uanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvZ2V0LWl0ZXJhdG9yLmpzIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL2lzLWl0ZXJhYmxlLmpzIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL3NsaWNlZFRvQXJyYXkuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vYXJyYXkvZnJvbS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9mbi9nZXQtaXRlcmF0b3IuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vaXMtaXRlcmFibGUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY3JlYXRlLXByb3BlcnR5LmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvY29yZS5nZXQtaXRlcmF0b3IuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9jb3JlLmlzLWl0ZXJhYmxlLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2LmFycmF5LmZyb20uanMiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL2NsZHIvaW5kZXguanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvdG9Db25zdW1hYmxlQXJyYXkuanMiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL2NsZHIvbnVtYmVyLWZvcm1hdHRlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9mb3NfanNfcm91dGVzLmpzb24iLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2NyZWF0ZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3QvZ2V0LXByb3RvdHlwZS1vZi5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3Qvc2V0LXByb3RvdHlwZS1vZi5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9pbmhlcml0cy5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9wb3NzaWJsZUNvbnN0cnVjdG9yUmV0dXJuLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9jcmVhdGUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2dldC1wcm90b3R5cGUtb2YuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L3NldC1wcm90b3R5cGUtb2YuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fc2V0LXByb3RvLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5jcmVhdGUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LmdldC1wcm90b3R5cGUtb2YuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LnNldC1wcm90b3R5cGUtb2YuanMiLCJ3ZWJwYWNrOi8vLy4vfi9mb3Mtcm91dGluZy9kaXN0L3JvdXRpbmcuanMiLCJ3ZWJwYWNrOi8vLy4vfi9sb2Rhc2guZXNjYXBlcmVnZXhwL2luZGV4LmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItdmlldy1ldmVudC1tYXAuanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L3ZhbHVlcy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByaWNlcy1yZWZyZXNoZXIuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L3ZhbHVlcy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtdG8tYXJyYXkuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczcub2JqZWN0LnZhbHVlcy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByaWNlcy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtcmVuZGVyZXIuanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvbnVtYmVyL2lzLW5hbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2Zvcm0vdGV4dC13aXRoLWxlbmd0aC1jb3VudGVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL21lc3NhZ2Uvb3JkZXItdmlldy1wYWdlLW1lc3NhZ2VzLWhhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvb3JkZXItc2hpcHBpbmctbWFuYWdlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtYWRkLWF1dG9jb21wbGV0ZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtYWRkLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItdmlldy1wYWdlLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2ludm9pY2Utbm90ZS1tYW5hZ2VyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci1kaXNjb3VudHMtcmVmcmVzaGVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItZG9jdW1lbnRzLXJlZnJlc2hlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLWludm9pY2VzLXJlZnJlc2hlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtY2FuY2VsLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcHJvZHVjdC1lZGl0LmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcHJvZHVjdC1tYW5hZ2VyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcHJvZHVjdHMtcmVmcmVzaGVyLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L2ZuL251bWJlci9pcy1uYW4uanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYubnVtYmVyLmlzLW5hbi5qcyJdLCJuYW1lcyI6WyJFdmVudEVtaXR0ZXIiLCJFdmVudEVtaXR0ZXJDbGFzcyIsIkNvbmZpcm1Nb2RhbCIsIiQiLCJ3aW5kb3ciLCJwYXJhbXMiLCJjb25maXJtQ2FsbGJhY2siLCJpZCIsImNsb3NhYmxlIiwibW9kYWwiLCJNb2RhbCIsIiRtb2RhbCIsImNvbnRhaW5lciIsInNob3ciLCJjb25maXJtQnV0dG9uIiwiYWRkRXZlbnRMaXN0ZW5lciIsImJhY2tkcm9wIiwia2V5Ym9hcmQiLCJ1bmRlZmluZWQiLCJvbiIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvciIsInJlbW92ZSIsImJvZHkiLCJhcHBlbmRDaGlsZCIsImNvbmZpcm1UaXRsZSIsImNvbmZpcm1NZXNzYWdlIiwiY2xvc2VCdXR0b25MYWJlbCIsImNvbmZpcm1CdXR0b25MYWJlbCIsImNvbmZpcm1CdXR0b25DbGFzcyIsImNyZWF0ZUVsZW1lbnQiLCJjbGFzc0xpc3QiLCJhZGQiLCJkaWFsb2ciLCJjb250ZW50IiwiaGVhZGVyIiwidGl0bGUiLCJpbm5lckhUTUwiLCJjbG9zZUljb24iLCJzZXRBdHRyaWJ1dGUiLCJkYXRhc2V0IiwiZGlzbWlzcyIsIm1lc3NhZ2UiLCJmb290ZXIiLCJjbG9zZUJ1dHRvbiIsImFwcGVuZCIsIlJvdXRlciIsIlJvdXRpbmciLCJzZXREYXRhIiwicm91dGVzIiwic2V0QmFzZVVybCIsImZpbmQiLCJkYXRhIiwicm91dGUiLCJ0b2tlbml6ZWRQYXJhbXMiLCJfdG9rZW4iLCJnZW5lcmF0ZSIsIm1haW5EaXYiLCJvcmRlclBheW1lbnREZXRhaWxzQnRuIiwib3JkZXJQYXltZW50Rm9ybUFtb3VudElucHV0Iiwib3JkZXJQYXltZW50SW52b2ljZVNlbGVjdCIsInZpZXdPcmRlclBheW1lbnRzQmxvY2siLCJwcml2YXRlTm90ZVRvZ2dsZUJ0biIsInByaXZhdGVOb3RlQmxvY2siLCJwcml2YXRlTm90ZUlucHV0IiwicHJpdmF0ZU5vdGVTdWJtaXRCdG4iLCJhZGRDYXJ0UnVsZU1vZGFsIiwiYWRkQ2FydFJ1bGVJbnZvaWNlSWRTZWxlY3QiLCJhZGRDYXJ0UnVsZVR5cGVTZWxlY3QiLCJhZGRDYXJ0UnVsZVZhbHVlSW5wdXQiLCJhZGRDYXJ0UnVsZVZhbHVlVW5pdCIsImNhcnRSdWxlSGVscFRleHQiLCJ1cGRhdGVPcmRlclN0YXR1c0FjdGlvbkJ0biIsInVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uSW5wdXQiLCJ1cGRhdGVPcmRlclN0YXR1c0FjdGlvbklucHV0V3JhcHBlciIsInVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uRm9ybSIsInNob3dPcmRlclNoaXBwaW5nVXBkYXRlTW9kYWxCdG4iLCJ1cGRhdGVPcmRlclNoaXBwaW5nVHJhY2tpbmdOdW1iZXJJbnB1dCIsInVwZGF0ZU9yZGVyU2hpcHBpbmdDdXJyZW50T3JkZXJDYXJyaWVySWRJbnB1dCIsInVwZGF0ZUN1c3RvbWVyQWRkcmVzc01vZGFsIiwib3Blbk9yZGVyQWRkcmVzc1VwZGF0ZU1vZGFsQnRuIiwidXBkYXRlT3JkZXJBZGRyZXNzVHlwZUlucHV0IiwiZGVsaXZlcnlBZGRyZXNzRWRpdEJ0biIsImludm9pY2VBZGRyZXNzRWRpdEJ0biIsIm9yZGVyTWVzc2FnZU5hbWVTZWxlY3QiLCJvcmRlck1lc3NhZ2VzQ29udGFpbmVyIiwib3JkZXJNZXNzYWdlIiwib3JkZXJNZXNzYWdlQ2hhbmdlV2FybmluZyIsIm9yZGVyRG9jdW1lbnRzVGFiQ291bnQiLCJvcmRlckRvY3VtZW50c1RhYkJvZHkiLCJhbGxNZXNzYWdlc01vZGFsIiwiYWxsTWVzc2FnZXNMaXN0Iiwib3BlbkFsbE1lc3NhZ2VzQnRuIiwicHJvZHVjdE9yaWdpbmFsUG9zaXRpb24iLCJwcm9kdWN0TW9kaWZpY2F0aW9uUG9zaXRpb24iLCJwcm9kdWN0c1BhbmVsIiwicHJvZHVjdHNDb3VudCIsInByb2R1Y3REZWxldGVCdG4iLCJwcm9kdWN0c1RhYmxlIiwicHJvZHVjdHNQYWdpbmF0aW9uIiwicHJvZHVjdHNOYXZQYWdpbmF0aW9uIiwicHJvZHVjdHNUYWJsZVBhZ2luYXRpb24iLCJwcm9kdWN0c1RhYmxlUGFnaW5hdGlvbk5leHQiLCJwcm9kdWN0c1RhYmxlUGFnaW5hdGlvblByZXYiLCJwcm9kdWN0c1RhYmxlUGFnaW5hdGlvbkxpbmsiLCJwcm9kdWN0c1RhYmxlUGFnaW5hdGlvbkFjdGl2ZSIsInByb2R1Y3RzVGFibGVQYWdpbmF0aW9uVGVtcGxhdGUiLCJwcm9kdWN0c1RhYmxlUGFnaW5hdGlvbk51bWJlclNlbGVjdG9yIiwicHJvZHVjdHNUYWJsZVJvdyIsInByb2R1Y3RJZCIsInByb2R1Y3RzVGFibGVSb3dFZGl0ZWQiLCJwcm9kdWN0c1RhYmxlUm93cyIsInByb2R1Y3RzQ2VsbExvY2F0aW9uIiwicHJvZHVjdHNDZWxsUmVmdW5kZWQiLCJwcm9kdWN0c0NlbGxMb2NhdGlvbkRpc3BsYXllZCIsInByb2R1Y3RzQ2VsbFJlZnVuZGVkRGlzcGxheWVkIiwicHJvZHVjdHNUYWJsZUN1c3RvbWl6YXRpb25Sb3dzIiwicHJvZHVjdEVkaXRCdXR0b25zIiwicHJvZHVjdEVkaXRCdG4iLCJwcm9kdWN0QWRkQnRuIiwicHJvZHVjdEFjdGlvbkJ0biIsInByb2R1Y3RBZGRBY3Rpb25CdG4iLCJwcm9kdWN0Q2FuY2VsQWRkQnRuIiwicHJvZHVjdEFkZFJvdyIsInByb2R1Y3RTZWFyY2hJbnB1dCIsInByb2R1Y3RTZWFyY2hJbnB1dEF1dG9jb21wbGV0ZSIsInByb2R1Y3RTZWFyY2hJbnB1dEF1dG9jb21wbGV0ZU1lbnUiLCJwcm9kdWN0QWRkSWRJbnB1dCIsInByb2R1Y3RBZGRUYXhSYXRlSW5wdXQiLCJwcm9kdWN0QWRkQ29tYmluYXRpb25zQmxvY2siLCJwcm9kdWN0QWRkQ29tYmluYXRpb25zU2VsZWN0IiwicHJvZHVjdEFkZFByaWNlVGF4RXhjbElucHV0IiwicHJvZHVjdEFkZFByaWNlVGF4SW5jbElucHV0IiwicHJvZHVjdEFkZFF1YW50aXR5SW5wdXQiLCJwcm9kdWN0QWRkQXZhaWxhYmxlVGV4dCIsInByb2R1Y3RBZGRMb2NhdGlvblRleHQiLCJwcm9kdWN0QWRkVG90YWxQcmljZVRleHQiLCJwcm9kdWN0QWRkSW52b2ljZVNlbGVjdCIsInByb2R1Y3RBZGRGcmVlU2hpcHBpbmdTZWxlY3QiLCJwcm9kdWN0QWRkTmV3SW52b2ljZUluZm8iLCJwcm9kdWN0RWRpdFNhdmVCdG4iLCJwcm9kdWN0RWRpdENhbmNlbEJ0biIsInByb2R1Y3RFZGl0Um93VGVtcGxhdGUiLCJwcm9kdWN0RWRpdFJvdyIsInByb2R1Y3RFZGl0SW1hZ2UiLCJwcm9kdWN0RWRpdE5hbWUiLCJwcm9kdWN0RWRpdFVuaXRQcmljZSIsInByb2R1Y3RFZGl0UXVhbnRpdHkiLCJwcm9kdWN0RWRpdEF2YWlsYWJsZVF1YW50aXR5IiwicHJvZHVjdEVkaXRUb3RhbFByaWNlIiwicHJvZHVjdEVkaXRQcmljZVRheEV4Y2xJbnB1dCIsInByb2R1Y3RFZGl0UHJpY2VUYXhJbmNsSW5wdXQiLCJwcm9kdWN0RWRpdEludm9pY2VTZWxlY3QiLCJwcm9kdWN0RWRpdFF1YW50aXR5SW5wdXQiLCJwcm9kdWN0RWRpdExvY2F0aW9uVGV4dCIsInByb2R1Y3RFZGl0QXZhaWxhYmxlVGV4dCIsInByb2R1Y3RFZGl0VG90YWxQcmljZVRleHQiLCJwcm9kdWN0RGlzY291bnRMaXN0IiwibGlzdCIsInByb2R1Y3RQYWNrTW9kYWwiLCJ0YWJsZSIsInJvd3MiLCJ0ZW1wbGF0ZSIsInByb2R1Y3QiLCJpbWciLCJsaW5rIiwibmFtZSIsInJlZiIsInN1cHBsaWVyUmVmIiwicXVhbnRpdHkiLCJhdmFpbGFibGVRdWFudGl0eSIsIm9yZGVyUHJvZHVjdHNUb3RhbCIsIm9yZGVyRGlzY291bnRzVG90YWxDb250YWluZXIiLCJvcmRlckRpc2NvdW50c1RvdGFsIiwib3JkZXJXcmFwcGluZ1RvdGFsIiwib3JkZXJTaGlwcGluZ1RvdGFsIiwib3JkZXJUYXhlc1RvdGFsIiwib3JkZXJUb3RhbCIsIm9yZGVySG9va1RhYnNDb250YWluZXIiLCJjYW5jZWxQcm9kdWN0IiwiZm9ybSIsImJ1dHRvbnMiLCJhYm9ydCIsInNhdmUiLCJwYXJ0aWFsUmVmdW5kIiwic3RhbmRhcmRSZWZ1bmQiLCJyZXR1cm5Qcm9kdWN0IiwiY2FuY2VsUHJvZHVjdHMiLCJpbnB1dHMiLCJhbW91bnQiLCJzZWxlY3RvciIsImNlbGwiLCJhY3Rpb25zIiwiY2hlY2tib3hlcyIsInJlc3RvY2siLCJjcmVkaXRTbGlwIiwidm91Y2hlciIsInJhZGlvcyIsInZvdWNoZXJSZWZ1bmRUeXBlIiwicHJvZHVjdFByaWNlcyIsInByb2R1Y3RQcmljZXNWb3VjaGVyRXhjbHVkZWQiLCJuZWdhdGl2ZUVycm9yTWVzc2FnZSIsInRvZ2dsZSIsInByaW50T3JkZXJWaWV3UGFnZUJ1dHRvbiIsIk51bWJlclN5bWJvbCIsImRlY2ltYWwiLCJncm91cCIsInBlcmNlbnRTaWduIiwibWludXNTaWduIiwicGx1c1NpZ24iLCJleHBvbmVudGlhbCIsInN1cGVyc2NyaXB0aW5nRXhwb25lbnQiLCJwZXJNaWxsZSIsImluZmluaXR5IiwibmFuIiwidmFsaWRhdGVEYXRhIiwiTG9jYWxpemF0aW9uRXhjZXB0aW9uIiwiTnVtYmVyU3BlY2lmaWNhdGlvbiIsInBvc2l0aXZlUGF0dGVybiIsIm5lZ2F0aXZlUGF0dGVybiIsInN5bWJvbCIsIm1heEZyYWN0aW9uRGlnaXRzIiwibWluRnJhY3Rpb25EaWdpdHMiLCJncm91cGluZ1VzZWQiLCJwcmltYXJ5R3JvdXBTaXplIiwic2Vjb25kYXJ5R3JvdXBTaXplIiwiQ1VSUkVOQ1lfRElTUExBWV9TWU1CT0wiLCJQcmljZVNwZWNpZmljYXRpb24iLCJjdXJyZW5jeVN5bWJvbCIsImN1cnJlbmN5Q29kZSIsIk51bWJlckZvcm1hdHRlciIsImVzY2FwZVJFIiwicmVxdWlyZSIsIkNVUlJFTkNZX1NZTUJPTF9QTEFDRUhPTERFUiIsIkRFQ0lNQUxfU0VQQVJBVE9SX1BMQUNFSE9MREVSIiwiR1JPVVBfU0VQQVJBVE9SX1BMQUNFSE9MREVSIiwiTUlOVVNfU0lHTl9QTEFDRUhPTERFUiIsIlBFUkNFTlRfU1lNQk9MX1BMQUNFSE9MREVSIiwiUExVU19TSUdOX1BMQUNFSE9MREVSIiwic3BlY2lmaWNhdGlvbiIsIm51bWJlclNwZWNpZmljYXRpb24iLCJudW1iZXIiLCJudW0iLCJNYXRoIiwiYWJzIiwidG9GaXhlZCIsImdldE1heEZyYWN0aW9uRGlnaXRzIiwiZXh0cmFjdE1ham9yTWlub3JEaWdpdHMiLCJtYWpvckRpZ2l0cyIsIm1pbm9yRGlnaXRzIiwic3BsaXRNYWpvckdyb3VwcyIsImFkanVzdE1pbm9yRGlnaXRzWmVyb2VzIiwiZm9ybWF0dGVkTnVtYmVyIiwicGF0dGVybiIsImdldENsZHJQYXR0ZXJuIiwiYWRkUGxhY2Vob2xkZXJzIiwicmVwbGFjZVN5bWJvbHMiLCJwZXJmb3JtU3BlY2lmaWNSZXBsYWNlbWVudHMiLCJyZXN1bHQiLCJ0b1N0cmluZyIsInNwbGl0IiwiZGlnaXQiLCJpc0dyb3VwaW5nVXNlZCIsInJldmVyc2UiLCJncm91cHMiLCJwdXNoIiwic3BsaWNlIiwiZ2V0UHJpbWFyeUdyb3VwU2l6ZSIsImxlbmd0aCIsImdldFNlY29uZGFyeUdyb3VwU2l6ZSIsIm5ld0dyb3VwcyIsImZvckVhY2giLCJqb2luIiwicmVwbGFjZSIsImdldE1pbkZyYWN0aW9uRGlnaXRzIiwicGFkRW5kIiwiaXNOZWdhdGl2ZSIsImdldE5lZ2F0aXZlUGF0dGVybiIsImdldFBvc2l0aXZlUGF0dGVybiIsInN5bWJvbHMiLCJnZXRTeW1ib2wiLCJtYXAiLCJnZXREZWNpbWFsIiwiZ2V0R3JvdXAiLCJnZXRNaW51c1NpZ24iLCJnZXRQZXJjZW50U2lnbiIsImdldFBsdXNTaWduIiwic3RydHIiLCJzdHIiLCJwYWlycyIsInN1YnN0cnMiLCJSZWdFeHAiLCJwYXJ0IiwiZ2V0Q3VycmVuY3lTeW1ib2wiLCJzcGVjaWZpY2F0aW9ucyIsIm51bWJlclN5bWJvbHMiLCJwYXJzZUludCIsInByb2R1Y3REZWxldGVkRnJvbU9yZGVyIiwicHJvZHVjdEFkZGVkVG9PcmRlciIsInByb2R1Y3RVcGRhdGVkIiwicHJvZHVjdEVkaXRpb25DYW5jZWxlZCIsInByb2R1Y3RMaXN0UGFnaW5hdGVkIiwicHJvZHVjdExpc3ROdW1iZXJQZXJQYWdlIiwiT3JkZXJQcmljZXNSZWZyZXNoZXIiLCJyb3V0ZXIiLCJvcmRlcklkIiwiYWpheCIsInRoZW4iLCJPcmRlclZpZXdQYWdlTWFwIiwidGV4dCIsInJlc3BvbnNlIiwib3JkZXJUb3RhbEZvcm1hdHRlZCIsImRpc2NvdW50c0Ftb3VudEZvcm1hdHRlZCIsInRvZ2dsZUNsYXNzIiwiZGlzY291bnRzQW1vdW50RGlzcGxheWVkIiwicHJvZHVjdHNUb3RhbEZvcm1hdHRlZCIsInNoaXBwaW5nVG90YWxGb3JtYXR0ZWQiLCJ0YXhlc1RvdGFsRm9ybWF0dGVkIiwicHJvZHVjdFByaWNlc0xpc3QiLCJvcmRlclByb2R1Y3RUcklkIiwib3JkZXJEZXRhaWxJZCIsInVuaXRQcmljZSIsInRvdGFsUHJpY2UiLCJwcm9kdWN0RWRpdEJ1dHRvbiIsInVuaXRQcmljZVRheEluY2xSYXciLCJ1bml0UHJpY2VUYXhFeGNsUmF3IiwiZ2l2ZW5QcmljZSIsImNvbWJpbmF0aW9uSWQiLCJpbnZvaWNlSWQiLCJwcm9kdWN0Um93cyIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJleHBlY3RlZFByb2R1Y3RJZCIsIk51bWJlciIsImV4cGVjdGVkQ29tYmluYXRpb25JZCIsImV4cGVjdGVkR2l2ZW5QcmljZSIsInVubWF0Y2hpbmdQcmljZUV4aXN0cyIsInByb2R1Y3RSb3ciLCJwcm9kdWN0Um93SWQiLCJhdHRyIiwiY3VycmVudE9yZGVySW52b2ljZUlkIiwiY3VycmVudFByb2R1Y3RJZCIsImN1cnJlbnRDb21iaW5hdGlvbklkIiwiT3JkZXJQcmljZXMiLCJ0YXhJbmNsdWRlZCIsInRheFJhdGVQZXJDZW50IiwiY3VycmVuY3lQcmVjaXNpb24iLCJwcmljZVRheEluY2wiLCJwYXJzZUZsb2F0IiwidGF4UmF0ZSIsInBzX3JvdW5kIiwidGF4RXhjbHVkZWQiLCJwcmljZVRheEV4Y2wiLCJPcmRlclByb2R1Y3RSZW5kZXJlciIsIiRwcm9kdWN0Um93IiwibmV3Um93IiwiaHRtbCIsImJlZm9yZSIsImhpZGUiLCJmYWRlSW4iLCJudW1Qcm9kdWN0cyIsImxvY2F0aW9uIiwiYXZhaWxhYmxlT3V0T2ZTdG9jayIsIm9yZGVySW52b2ljZUlkIiwiJG9yZGVyRWRpdCIsIk9yZGVyUHJvZHVjdEVkaXQiLCJkaXNwbGF5UHJvZHVjdCIsInByaWNlX3RheF9leGNsIiwicHJpY2VfdGF4X2luY2wiLCJ0YXhfcmF0ZSIsImFkZENsYXNzIiwic2Nyb2xsVGFyZ2V0IiwicmVtb3ZlQ2xhc3MiLCJtb3ZlUHJvZHVjdFBhbmVsVG9Ub3AiLCJyZXNldEFsbEVkaXRSb3dzIiwiJG1vZGlmaWNhdGlvblBvc2l0aW9uIiwiZGV0YWNoIiwiYXBwZW5kVG8iLCJjbG9zZXN0IiwidG9nZ2xlQ29sdW1uIiwiJHJvd3MiLCJzY3JvbGxWYWx1ZSIsIm9mZnNldCIsInRvcCIsImhlaWdodCIsImFuaW1hdGUiLCJzY3JvbGxUb3AiLCJwYWdpbmF0ZSIsInZhbCIsInByb3AiLCJlYWNoIiwia2V5IiwiZWRpdEJ1dHRvbiIsInJlc2V0RWRpdFJvdyIsIm9yZGVyUHJvZHVjdElkIiwiJHByb2R1Y3RFZGl0Um93IiwibnVtUGFnZSIsIiRjdXN0b21pemF0aW9uUm93cyIsIiR0YWJsZVBhZ2luYXRpb24iLCJudW1Sb3dzUGVyUGFnZSIsIm1heFBhZ2UiLCJjZWlsIiwibWF4IiwibWluIiwicGFnaW5hdGVVcGRhdGVDb250cm9scyIsInN0YXJ0Um93IiwiZW5kUm93IiwiaSIsInByZXYiLCJoYXNDbGFzcyIsIm5vdCIsInRvdGFsUGFnZSIsInRvZ2dsZVBhZ2luYXRpb25Db250cm9scyIsIm51bVBlclBhZ2UiLCJudW1QYWdlcyIsIiRsaW5rUGFnaW5hdGlvblRlbXBsYXRlIiwiJGxpbmtQYWdpbmF0aW9uIiwiY2xvbmUiLCJ0YXJnZXQiLCJmb3JjZURpc3BsYXkiLCJpc0NvbHVtbkRpc3BsYXllZCIsImZpbHRlciIsInRyaW0iLCJUZXh0V2l0aExlbmd0aENvdW50ZXIiLCJ3cmFwcGVyU2VsZWN0b3IiLCJ0ZXh0U2VsZWN0b3IiLCJpbnB1dFNlbGVjdG9yIiwiZSIsIiRpbnB1dCIsImN1cnJlbnRUYXJnZXQiLCJyZW1haW5pbmdMZW5ndGgiLCJPcmRlclZpZXdQYWdlTWVzc2FnZXNIYW5kbGVyIiwiJG9yZGVyTWVzc2FnZUNoYW5nZVdhcm5pbmciLCIkbWVzc2FnZXNDb250YWluZXIiLCJsaXN0ZW5Gb3JQcmVkZWZpbmVkTWVzc2FnZVNlbGVjdGlvbiIsIl9oYW5kbGVQcmVkZWZpbmVkTWVzc2FnZVNlbGVjdGlvbiIsImxpc3RlbkZvckZ1bGxNZXNzYWdlc09wZW4iLCJfb25GdWxsTWVzc2FnZXNPcGVuIiwiJGN1cnJlbnRJdGVtIiwidmFsdWVJZCIsIiRvcmRlck1lc3NhZ2UiLCJpc1NhbWVNZXNzYWdlIiwiY29uZmlybSIsIl9zY3JvbGxUb01zZ0xpc3RCb3R0b20iLCIkbXNnTW9kYWwiLCJtc2dMaXN0IiwiY2xhc3NDaGVja0ludGVydmFsIiwic2V0SW50ZXJ2YWwiLCJzY3JvbGxIZWlnaHQiLCJjbGVhckludGVydmFsIiwiT3JkZXJTaGlwcGluZ01hbmFnZXIiLCJfaW5pdE9yZGVyU2hpcHBpbmdVcGRhdGVFdmVudEhhbmRsZXIiLCJldmVudCIsIiRidG4iLCJPcmRlclByb2R1Y3RBdXRvY29tcGxldGUiLCJpbnB1dCIsImFjdGl2ZVNlYXJjaFJlcXVlc3QiLCJyZXN1bHRzIiwiZHJvcGRvd25NZW51Iiwib25JdGVtQ2xpY2tlZENhbGxiYWNrIiwic3RvcEltbWVkaWF0ZVByb3BhZ2F0aW9uIiwidXBkYXRlUmVzdWx0cyIsImRlbGF5U2VhcmNoIiwiY2xlYXJUaW1lb3V0Iiwic2VhcmNoVGltZW91dElkIiwic2V0VGltZW91dCIsInNlYXJjaCIsInZhbHVlIiwiY3VycmVuY3kiLCJzZWFyY2hfcGhyYXNlIiwiY3VycmVuY3lfaWQiLCJvcmRlcl9pZCIsImdldCIsImFsd2F5cyIsImVtcHR5IiwicHJvZHVjdHMiLCJwcmV2ZW50RGVmYXVsdCIsIm9uSXRlbUNsaWNrZWQiLCJzZWxlY3RlZFByb2R1Y3QiLCJPcmRlclByb2R1Y3RBZGQiLCJwcm9kdWN0SWRJbnB1dCIsImNvbWJpbmF0aW9uc0Jsb2NrIiwiY29tYmluYXRpb25zU2VsZWN0IiwicHJpY2VUYXhJbmNsdWRlZElucHV0IiwicHJpY2VUYXhFeGNsdWRlZElucHV0IiwidGF4UmF0ZUlucHV0IiwicXVhbnRpdHlJbnB1dCIsImF2YWlsYWJsZVRleHQiLCJsb2NhdGlvblRleHQiLCJ0b3RhbFByaWNlVGV4dCIsImludm9pY2VTZWxlY3QiLCJmcmVlU2hpcHBpbmdTZWxlY3QiLCJwcm9kdWN0QWRkTWVudUJ0biIsImF2YWlsYWJsZSIsInNldHVwTGlzdGVuZXIiLCJwcmljZVRheENhbGN1bGF0b3IiLCJvcmRlclByb2R1Y3RSZW5kZXJlciIsIm9yZGVyUHJpY2VzUmVmcmVzaGVyIiwidHJpZ2dlciIsIm5ld1F1YW50aXR5IiwicmVtYWluaW5nQXZhaWxhYmxlIiwiZGlzYWJsZUFkZEFjdGlvbkJ0biIsImNhbGN1bGF0ZVRvdGFsUHJpY2UiLCJyZW1vdmVBdHRyIiwiY2FsY3VsYXRlVGF4RXhjbHVkZWQiLCJjYWxjdWxhdGVUYXhJbmNsdWRlZCIsImNvbmZpcm1OZXdJbnZvaWNlIiwidG9nZ2xlUHJvZHVjdEFkZE5ld0ludm9pY2VJbmZvIiwic3RvY2siLCJzZXRDb21iaW5hdGlvbnMiLCJjb21iaW5hdGlvbnMiLCJhdHRyaWJ1dGVDb21iaW5hdGlvbklkIiwicHJpY2VUYXhFeGNsdWRlZCIsInByaWNlVGF4SW5jbHVkZWQiLCJhdHRyaWJ1dGUiLCJwcm9kdWN0X2lkIiwiY29tYmluYXRpb25faWQiLCJpbnZvaWNlX2lkIiwiZnJlZV9zaGlwcGluZyIsInVybCIsIm1ldGhvZCIsImVtaXQiLCJPcmRlclZpZXdFdmVudE1hcCIsInJlc3BvbnNlSlNPTiIsImdyb3dsIiwiZXJyb3IiLCJjb25maXJtTmV3UHJpY2UiLCJpc05hTiIsImFkZFByb2R1Y3QiLCJwcm9kdWN0UHJpY2VNYXRjaCIsImNoZWNrT3RoZXJQcm9kdWN0UHJpY2VzTWF0Y2giLCJtb2RhbEVkaXRQcmljZSIsIk9yZGVyVmlld1BhZ2UiLCJvcmRlckRpc2NvdW50c1JlZnJlc2hlciIsIk9yZGVyRGlzY291bnRzUmVmcmVzaGVyIiwib3JkZXJQcm9kdWN0TWFuYWdlciIsIk9yZGVyUHJvZHVjdE1hbmFnZXIiLCJvcmRlclByb2R1Y3RzUmVmcmVzaGVyIiwiT3JkZXJQcm9kdWN0c1JlZnJlc2hlciIsIm9yZGVyRG9jdW1lbnRzUmVmcmVzaGVyIiwiT3JkZXJEb2N1bWVudHNSZWZyZXNoZXIiLCJvcmRlckludm9pY2VzUmVmcmVzaGVyIiwiT3JkZXJJbnZvaWNlc1JlZnJlc2hlciIsIm9yZGVyUHJvZHVjdENhbmNlbCIsIk9yZGVyUHJvZHVjdENhbmNlbCIsImxpc3RlblRvRXZlbnRzIiwiZmFuY3lib3giLCIkcm93Iiwib2xkT3JkZXJEZXRhaWxJZCIsIiRuZXh0IiwibmV4dCIsIm51bVJvd3MiLCJjdXJyZW50UGFnZSIsInBhZ2luYXRpb25SZW1vdmVQYWdlIiwidXBkYXRlTnVtUHJvZHVjdHMiLCJyZWZyZXNoIiwiZWRpdFJvd3NMZWZ0IiwibW92ZVByb2R1Y3RQYW5lbFRvT3JpZ2luYWxQb3NpdGlvbiIsImFkZE9yVXBkYXRlUHJvZHVjdFRvTGlzdCIsInJlZnJlc2hQcm9kdWN0UHJpY2VzIiwibGlzdGVuRm9yUHJvZHVjdERlbGV0ZSIsImxpc3RlbkZvclByb2R1Y3RFZGl0IiwicmVzZXRUb29sVGlwcyIsImluaXRpYWxOdW1Qcm9kdWN0cyIsIm5ld051bVByb2R1Y3RzIiwiaW5pdGlhbFBhZ2VzTnVtIiwibmV3UGFnZXNOdW0iLCJwYWdpbmF0aW9uQWRkUGFnZSIsInJlc2V0QWRkUm93Iiwib2ZmIiwiaGFuZGxlRGVsZXRlUHJvZHVjdEV2ZW50IiwicHN0b29sdGlwIiwibW92ZVByb2R1Y3RzUGFuZWxUb01vZGlmaWNhdGlvblBvc2l0aW9uIiwiZWRpdFByb2R1Y3RGcm9tTGlzdCIsImJ1dHRvbiIsInJlbGF0ZWRUYXJnZXQiLCJwYWNrSXRlbXMiLCIkaXRlbSIsIml0ZW0iLCJpbWFnZVBhdGgiLCJyZWZlcmVuY2UiLCJzdXBwbGllclJlZmVyZW5jZSIsImFjdGl2ZVBhZ2UiLCJnZXRBY3RpdmVQYWdlIiwiJHNlbGVjdCIsInVwZGF0ZU51bVBlclBhZ2UiLCJtb3ZlUHJvZHVjdHNQYW5lbFRvUmVmdW5kUG9zaXRpb24iLCJzaG93UGFydGlhbFJlZnVuZCIsInNob3dTdGFuZGFyZFJlZnVuZCIsInNob3dSZXR1cm5Qcm9kdWN0IiwiaGlkZVJlZnVuZCIsInNob3dDYW5jZWxQcm9kdWN0Rm9ybSIsIkludm9pY2VOb3RlTWFuYWdlciIsInNldHVwTGlzdGVuZXJzIiwiX2luaXRTaG93Tm90ZUZvcm1FdmVudEhhbmRsZXIiLCJfaW5pdENsb3NlTm90ZUZvcm1FdmVudEhhbmRsZXIiLCJfaW5pdEVudGVyUGF5bWVudEV2ZW50SGFuZGxlciIsIiRub3RlUm93IiwicGF5bWVudEFtb3VudCIsInNjcm9sbEludG9WaWV3IiwiYmVoYXZpb3IiLCJESVNDT1VOVF9UWVBFX0FNT1VOVCIsIkRJU0NPVU5UX1RZUEVfUEVSQ0VOVCIsIkRJU0NPVU5UX1RZUEVfRlJFRV9TSElQUElORyIsIm9yZGVyVmlld1BhZ2UiLCJvcmRlckFkZEF1dG9jb21wbGV0ZSIsIm9yZGVyQWRkIiwibGlzdGVuRm9yUHJvZHVjdFBhY2siLCJsaXN0ZW5Gb3JQcm9kdWN0QWRkIiwibGlzdGVuRm9yUHJvZHVjdFBhZ2luYXRpb24iLCJsaXN0ZW5Gb3JSZWZ1bmQiLCJsaXN0ZW5Gb3JDYW5jZWxQcm9kdWN0IiwibGlzdGVuRm9yU2VhcmNoIiwic2V0UHJvZHVjdCIsImhhbmRsZVBheW1lbnREZXRhaWxzVG9nZ2xlIiwiaGFuZGxlUHJpdmF0ZU5vdGVDaGFuZ2UiLCJoYW5kbGVVcGRhdGVPcmRlclN0YXR1c0J1dHRvbiIsIm9yZGVyVmlld1BhZ2VNZXNzYWdlSGFuZGxlciIsInRvZ2dsZVByaXZhdGVOb3RlQmxvY2siLCJ0ZW1wVGl0bGUiLCJwcmludCIsImluaXRBZGRDYXJ0UnVsZUZvcm1IYW5kbGVyIiwiaW5pdENoYW5nZUFkZHJlc3NGb3JtSGFuZGxlciIsImluaXRIb29rVGFicyIsInRhYiIsIiRwYXltZW50RGV0YWlsUm93IiwiJGJsb2NrIiwiaXNQcml2YXRlTm90ZU9wZW5lZCIsIiRpY29uIiwiJHN1Ym1pdEJ0biIsIiRmb3JtIiwiJHZhbHVlSGVscCIsIiR2YWx1ZUlucHV0IiwiJHZhbHVlRm9ybUdyb3VwIiwic2VsZWN0ZWRDYXJ0UnVsZVR5cGUiLCIkdmFsdWVVbml0IiwiJHdyYXBwZXIiLCIkZWxlbWVudCIsIiRvcHRpb24iLCJzZWxlY3RlZE9yZGVyU3RhdHVzSWQiLCJjc3MiLCJyZXBsYWNlV2l0aCIsImludm9pY2VOb3RlTWFuYWdlciIsInRvdGFsIiwiaW52b2ljZXMiLCIkcGF5bWVudEludm9pY2VTZWxlY3QiLCIkYWRkUHJvZHVjdEludm9pY2VTZWxlY3QiLCIkZXhpc3RpbmdJbnZvaWNlc0dyb3VwIiwiJHByb2R1Y3RFZGl0SW52b2ljZVNlbGVjdCIsIiRhZGREaXNjb3VudEludm9pY2VTZWxlY3QiLCJpbnZvaWNlTmFtZSIsImludm9pY2VOYW1lV2l0aG91dFByaWNlIiwic2VsZWN0ZWRJbmRleCIsImNhbmNlbFByb2R1Y3RGb3JtIiwib3JkZXJEZWxpdmVyZWQiLCJpc1RheEluY2x1ZGVkIiwiZGlzY291bnRzQW1vdW50IiwiY3VycmVuY3lGb3JtYXR0ZXIiLCJidWlsZCIsInVzZUFtb3VudElucHV0cyIsImxpc3RlbkZvcklucHV0cyIsImhpZGVDYW5jZWxFbGVtZW50cyIsImluaXRGb3JtIiwiYWN0aW9uTmFtZSIsImZvcm1BY3Rpb24iLCJmb3JtQ2xhc3MiLCJ1cGRhdGVWb3VjaGVyUmVmdW5kIiwiJHByb2R1Y3RRdWFudGl0eUlucHV0IiwidXBkYXRlQW1vdW50SW5wdXQiLCIkcHJvZHVjdENoZWNrYm94IiwiJHBhcmVudENlbGwiLCJwYXJlbnRzIiwicHJvZHVjdFF1YW50aXR5SW5wdXQiLCJyZWZ1bmRhYmxlUXVhbnRpdHkiLCJwcm9kdWN0UXVhbnRpdHkiLCJpcyIsIiRwcm9kdWN0QW1vdW50IiwicHJpY2VGaWVsZE5hbWUiLCJwcm9kdWN0VW5pdFByaWNlIiwiYW1vdW50UmVmdW5kYWJsZSIsImd1ZXNzZWRBbW91bnQiLCJhbW91bnRWYWx1ZSIsInRvdGFsQW1vdW50IiwiaW5kZXgiLCJmbG9hdFZhbHVlIiwiJHF1YW50aXR5SW5wdXQiLCJyZWZ1bmRBbW91bnQiLCJnZXRSZWZ1bmRBbW91bnQiLCJ1cGRhdGVWb3VjaGVyUmVmdW5kVHlwZUxhYmVsIiwicmVmdW5kVm91Y2hlckV4Y2x1ZGVkIiwiZGVmYXVsdExhYmVsIiwiJGxhYmVsIiwiZm9ybWF0dGVkQW1vdW50IiwiZm9ybWF0IiwibGFzdENoaWxkIiwibm9kZVZhbHVlIiwiY2FuY2VsUHJvZHVjdFJvdXRlIiwicHJldmlvdXNRdWFudGl0eSIsInVwZGF0ZVRvdGFsIiwiZGlzYWJsZUVkaXRBY3Rpb25CdG4iLCJjb25maXJtZWQiLCJoYW5kbGVFZGl0UHJvZHVjdFdpdGhDb25maXJtYXRpb25Nb2RhbCIsInVwZGF0ZWRUb3RhbCIsInByaWNlVG90YWxUZXh0IiwiaW5pdGlhbFRvdGFsIiwicHJvZHVjdFJvd0VkaXQiLCJyZW1vdmVBbGxJZHMiLCJhZnRlciIsImVkaXRQcm9kdWN0IiwiaW52b2ljZSIsImRlbGV0ZVByb2R1Y3QiLCIkb3JkZXJQcm9kdWN0cyIsIiRvcmRlckRldGFpbElkcyIsIm9yZGVyRGV0YWlsUm93cyIsIiRvcmRlckRldGFpbFJvd0lkIiwib3JkZXJEZXRhaWxSb3ciLCJtYXRjaCIsImluY2x1ZGVzIiwicmVtb3ZlUHJvZHVjdFJvdyIsIm9yZGVyRGV0YWlsUm93SWQiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7O0FDaEVBOztBQUVBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsRTs7Ozs7OztBQ1JBOztBQUVBOztBQUVBOztBQUVBOztBQUVBLHNDQUFzQyx1Q0FBdUMsZ0JBQWdCOztBQUU3RjtBQUNBO0FBQ0EsbUJBQW1CLGtCQUFrQjtBQUNyQztBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsQ0FBQyxHOzs7Ozs7QUMxQkQ7QUFDQTtBQUNBLGlDQUFpQyxRQUFRLGdCQUFnQixVQUFVLEdBQUc7QUFDdEUsQ0FBQyxFOzs7Ozs7QUNIRCw2QkFBNkI7QUFDN0IscUNBQXFDLGdDOzs7Ozs7QUNEckM7QUFDQTtBQUNBLEU7Ozs7OztBQ0ZBO0FBQ0E7QUFDQTtBQUNBLHVDQUF1QyxnQzs7Ozs7O0FDSHZDO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FB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Q0pBLGNBQWMsc0I7Ozs7Ozs7QUNBZDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsQ0FBQzs7QUFFRDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSxpQkFBaUIsc0JBQXNCO0FBQ3ZDOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGVBQWU7QUFDZjtBQUNBO0FBQ0E7QUFDQTtBQUNBLGNBQWM7QUFDZDs7QUFFQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBLG1CQUFtQixTQUFTO0FBQzVCO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtB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Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUM5QnJCO0FBQ0EscUQ7Ozs7Ozs7Ozs7Ozs7O0FDREE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLENBQUMsRSIsImZpbGUiOiJvcmRlcl92aWV3LmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gNTIwKTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCBhZTMyMGYxMTIxOWI3YTZiNGYzNCIsIlwidXNlIHN0cmljdFwiO1xuXG5leHBvcnRzLl9fZXNNb2R1bGUgPSB0cnVlO1xuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoaW5zdGFuY2UsIENvbnN0cnVjdG9yKSB7XG4gIGlmICghKGluc3RhbmNlIGluc3RhbmNlb2YgQ29uc3RydWN0b3IpKSB7XG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcihcIkNhbm5vdCBjYWxsIGEgY2xhc3MgYXMgYSBmdW5jdGlvblwiKTtcbiAgfVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NsYXNzQ2FsbENoZWNrLmpzXG4vLyBtb2R1bGUgaWQgPSAwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiXCJ1c2Ugc3RyaWN0XCI7XG5cbmV4cG9ydHMuX19lc01vZHVsZSA9IHRydWU7XG5cbnZhciBfZGVmaW5lUHJvcGVydHkgPSByZXF1aXJlKFwiLi4vY29yZS1qcy9vYmplY3QvZGVmaW5lLXByb3BlcnR5XCIpO1xuXG52YXIgX2RlZmluZVByb3BlcnR5MiA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX2RlZmluZVByb3BlcnR5KTtcblxuZnVuY3Rpb24gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChvYmopIHsgcmV0dXJuIG9iaiAmJiBvYmouX19lc01vZHVsZSA/IG9iaiA6IHsgZGVmYXVsdDogb2JqIH07IH1cblxuZXhwb3J0cy5kZWZhdWx0ID0gZnVuY3Rpb24gKCkge1xuICBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0aWVzKHRhcmdldCwgcHJvcHMpIHtcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IHByb3BzLmxlbmd0aDsgaSsrKSB7XG4gICAgICB2YXIgZGVzY3JpcHRvciA9IHByb3BzW2ldO1xuICAgICAgZGVzY3JpcHRvci5lbnVtZXJhYmxlID0gZGVzY3JpcHRvci5lbnVtZXJhYmxlIHx8IGZhbHNlO1xuICAgICAgZGVzY3JpcHRvci5jb25maWd1cmFibGUgPSB0cnVlO1xuICAgICAgaWYgKFwidmFsdWVcIiBpbiBkZXNjcmlwdG9yKSBkZXNjcmlwdG9yLndyaXRhYmxlID0gdHJ1ZTtcbiAgICAgICgwLCBfZGVmaW5lUHJvcGVydHkyLmRlZmF1bHQpKHRhcmdldCwgZGVzY3JpcHRvci5rZXksIGRlc2NyaXB0b3IpO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiBmdW5jdGlvbiAoQ29uc3RydWN0b3IsIHByb3RvUHJvcHMsIHN0YXRpY1Byb3BzKSB7XG4gICAgaWYgKHByb3RvUHJvcHMpIGRlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IucHJvdG90eXBlLCBwcm90b1Byb3BzKTtcbiAgICBpZiAoc3RhdGljUHJvcHMpIGRlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IsIHN0YXRpY1Byb3BzKTtcbiAgICByZXR1cm4gQ29uc3RydWN0b3I7XG4gIH07XG59KCk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9jcmVhdGVDbGFzcy5qc1xuLy8gbW9kdWxlIGlkID0gMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8vIFRoYW5rJ3MgSUU4IGZvciBoaXMgZnVubnkgZGVmaW5lUHJvcGVydHlcbm1vZHVsZS5leHBvcnRzID0gIXJlcXVpcmUoJy4vX2ZhaWxzJykoZnVuY3Rpb24oKXtcbiAgcmV0dXJuIE9iamVjdC5kZWZpbmVQcm9wZXJ0eSh7fSwgJ2EnLCB7Z2V0OiBmdW5jdGlvbigpeyByZXR1cm4gNzsgfX0pLmEgIT0gNztcbn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZGVzY3JpcHRvcnMuanNcbi8vIG1vZHVsZSBpZCA9IDJcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGNvcmUgPSBtb2R1bGUuZXhwb3J0cyA9IHt2ZXJzaW9uOiAnMi40LjAnfTtcbmlmKHR5cGVvZiBfX2UgPT0gJ251bWJlcicpX19lID0gY29yZTsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBuby11bmRlZlxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY29yZS5qc1xuLy8gbW9kdWxlIGlkID0gM1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIHR5cGVvZiBpdCA9PT0gJ29iamVjdCcgPyBpdCAhPT0gbnVsbCA6IHR5cGVvZiBpdCA9PT0gJ2Z1bmN0aW9uJztcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pcy1vYmplY3QuanNcbi8vIG1vZHVsZSBpZCA9IDRcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLy8gaHR0cHM6Ly9naXRodWIuY29tL3psb2lyb2NrL2NvcmUtanMvaXNzdWVzLzg2I2lzc3VlY29tbWVudC0xMTU3NTkwMjhcbnZhciBnbG9iYWwgPSBtb2R1bGUuZXhwb3J0cyA9IHR5cGVvZiB3aW5kb3cgIT0gJ3VuZGVmaW5lZCcgJiYgd2luZG93Lk1hdGggPT0gTWF0aFxuICA/IHdpbmRvdyA6IHR5cGVvZiBzZWxmICE9ICd1bmRlZmluZWQnICYmIHNlbGYuTWF0aCA9PSBNYXRoID8gc2VsZiA6IEZ1bmN0aW9uKCdyZXR1cm4gdGhpcycpKCk7XG5pZih0eXBlb2YgX19nID09ICdudW1iZXInKV9fZyA9IGdsb2JhbDsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBuby11bmRlZlxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSA1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBhbk9iamVjdCAgICAgICA9IHJlcXVpcmUoJy4vX2FuLW9iamVjdCcpXG4gICwgSUU4X0RPTV9ERUZJTkUgPSByZXF1aXJlKCcuL19pZTgtZG9tLWRlZmluZScpXG4gICwgdG9QcmltaXRpdmUgICAgPSByZXF1aXJlKCcuL190by1wcmltaXRpdmUnKVxuICAsIGRQICAgICAgICAgICAgID0gT2JqZWN0LmRlZmluZVByb3BlcnR5O1xuXG5leHBvcnRzLmYgPSByZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpID8gT2JqZWN0LmRlZmluZVByb3BlcnR5IDogZnVuY3Rpb24gZGVmaW5lUHJvcGVydHkoTywgUCwgQXR0cmlidXRlcyl7XG4gIGFuT2JqZWN0KE8pO1xuICBQID0gdG9QcmltaXRpdmUoUCwgdHJ1ZSk7XG4gIGFuT2JqZWN0KEF0dHJpYnV0ZXMpO1xuICBpZihJRThfRE9NX0RFRklORSl0cnkge1xuICAgIHJldHVybiBkUChPLCBQLCBBdHRyaWJ1dGVzKTtcbiAgfSBjYXRjaChlKXsgLyogZW1wdHkgKi8gfVxuICBpZignZ2V0JyBpbiBBdHRyaWJ1dGVzIHx8ICdzZXQnIGluIEF0dHJpYnV0ZXMpdGhyb3cgVHlwZUVycm9yKCdBY2Nlc3NvcnMgbm90IHN1cHBvcnRlZCEnKTtcbiAgaWYoJ3ZhbHVlJyBpbiBBdHRyaWJ1dGVzKU9bUF0gPSBBdHRyaWJ1dGVzLnZhbHVlO1xuICByZXR1cm4gTztcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZHAuanNcbi8vIG1vZHVsZSBpZCA9IDZcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihleGVjKXtcbiAgdHJ5IHtcbiAgICByZXR1cm4gISFleGVjKCk7XG4gIH0gY2F0Y2goZSl7XG4gICAgcmV0dXJuIHRydWU7XG4gIH1cbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19mYWlscy5qc1xuLy8gbW9kdWxlIGlkID0gN1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgZ2xvYmFsICAgID0gcmVxdWlyZSgnLi9fZ2xvYmFsJylcbiAgLCBjb3JlICAgICAgPSByZXF1aXJlKCcuL19jb3JlJylcbiAgLCBjdHggICAgICAgPSByZXF1aXJlKCcuL19jdHgnKVxuICAsIGhpZGUgICAgICA9IHJlcXVpcmUoJy4vX2hpZGUnKVxuICAsIFBST1RPVFlQRSA9ICdwcm90b3R5cGUnO1xuXG52YXIgJGV4cG9ydCA9IGZ1bmN0aW9uKHR5cGUsIG5hbWUsIHNvdXJjZSl7XG4gIHZhciBJU19GT1JDRUQgPSB0eXBlICYgJGV4cG9ydC5GXG4gICAgLCBJU19HTE9CQUwgPSB0eXBlICYgJGV4cG9ydC5HXG4gICAgLCBJU19TVEFUSUMgPSB0eXBlICYgJGV4cG9ydC5TXG4gICAgLCBJU19QUk9UTyAgPSB0eXBlICYgJGV4cG9ydC5QXG4gICAgLCBJU19CSU5EICAgPSB0eXBlICYgJGV4cG9ydC5CXG4gICAgLCBJU19XUkFQICAgPSB0eXBlICYgJGV4cG9ydC5XXG4gICAgLCBleHBvcnRzICAgPSBJU19HTE9CQUwgPyBjb3JlIDogY29yZVtuYW1lXSB8fCAoY29yZVtuYW1lXSA9IHt9KVxuICAgICwgZXhwUHJvdG8gID0gZXhwb3J0c1tQUk9UT1RZUEVdXG4gICAgLCB0YXJnZXQgICAgPSBJU19HTE9CQUwgPyBnbG9iYWwgOiBJU19TVEFUSUMgPyBnbG9iYWxbbmFtZV0gOiAoZ2xvYmFsW25hbWVdIHx8IHt9KVtQUk9UT1RZUEVdXG4gICAgLCBrZXksIG93biwgb3V0O1xuICBpZihJU19HTE9CQUwpc291cmNlID0gbmFtZTtcbiAgZm9yKGtleSBpbiBzb3VyY2Upe1xuICAgIC8vIGNvbnRhaW5zIGluIG5hdGl2ZVxuICAgIG93biA9ICFJU19GT1JDRUQgJiYgdGFyZ2V0ICYmIHRhcmdldFtrZXldICE9PSB1bmRlZmluZWQ7XG4gICAgaWYob3duICYmIGtleSBpbiBleHBvcnRzKWNvbnRpbnVlO1xuICAgIC8vIGV4cG9ydCBuYXRpdmUgb3IgcGFzc2VkXG4gICAgb3V0ID0gb3duID8gdGFyZ2V0W2tleV0gOiBzb3VyY2Vba2V5XTtcbiAgICAvLyBwcmV2ZW50IGdsb2JhbCBwb2xsdXRpb24gZm9yIG5hbWVzcGFjZXNcbiAgICBleHBvcnRzW2tleV0gPSBJU19HTE9CQUwgJiYgdHlwZW9mIHRhcmdldFtrZXldICE9ICdmdW5jdGlvbicgPyBzb3VyY2Vba2V5XVxuICAgIC8vIGJpbmQgdGltZXJzIHRvIGdsb2JhbCBmb3IgY2FsbCBmcm9tIGV4cG9ydCBjb250ZXh0XG4gICAgOiBJU19CSU5EICYmIG93biA/IGN0eChvdXQsIGdsb2JhbClcbiAgICAvLyB3cmFwIGdsb2JhbCBjb25zdHJ1Y3RvcnMgZm9yIHByZXZlbnQgY2hhbmdlIHRoZW0gaW4gbGlicmFyeVxuICAgIDogSVNfV1JBUCAmJiB0YXJnZXRba2V5XSA9PSBvdXQgPyAoZnVuY3Rpb24oQyl7XG4gICAgICB2YXIgRiA9IGZ1bmN0aW9uKGEsIGIsIGMpe1xuICAgICAgICBpZih0aGlzIGluc3RhbmNlb2YgQyl7XG4gICAgICAgICAgc3dpdGNoKGFyZ3VtZW50cy5sZW5ndGgpe1xuICAgICAgICAgICAgY2FzZSAwOiByZXR1cm4gbmV3IEM7XG4gICAgICAgICAgICBjYXNlIDE6IHJldHVybiBuZXcgQyhhKTtcbiAgICAgICAgICAgIGNhc2UgMjogcmV0dXJuIG5ldyBDKGEsIGIpO1xuICAgICAgICAgIH0gcmV0dXJuIG5ldyBDKGEsIGIsIGMpO1xuICAgICAgICB9IHJldHVybiBDLmFwcGx5KHRoaXMsIGFyZ3VtZW50cyk7XG4gICAgICB9O1xuICAgICAgRltQUk9UT1RZUEVdID0gQ1tQUk9UT1RZUEVdO1xuICAgICAgcmV0dXJuIEY7XG4gICAgLy8gbWFrZSBzdGF0aWMgdmVyc2lvbnMgZm9yIHByb3RvdHlwZSBtZXRob2RzXG4gICAgfSkob3V0KSA6IElTX1BST1RPICYmIHR5cGVvZiBvdXQgPT0gJ2Z1bmN0aW9uJyA/IGN0eChGdW5jdGlvbi5jYWxsLCBvdXQpIDogb3V0O1xuICAgIC8vIGV4cG9ydCBwcm90byBtZXRob2RzIHRvIGNvcmUuJUNPTlNUUlVDVE9SJS5tZXRob2RzLiVOQU1FJVxuICAgIGlmKElTX1BST1RPKXtcbiAgICAgIChleHBvcnRzLnZpcnR1YWwgfHwgKGV4cG9ydHMudmlydHVhbCA9IHt9KSlba2V5XSA9IG91dDtcbiAgICAgIC8vIGV4cG9ydCBwcm90byBtZXRob2RzIHRvIGNvcmUuJUNPTlNUUlVDVE9SJS5wcm90b3R5cGUuJU5BTUUlXG4gICAgICBpZih0eXBlICYgJGV4cG9ydC5SICYmIGV4cFByb3RvICYmICFleHBQcm90b1trZXldKWhpZGUoZXhwUHJvdG8sIGtleSwgb3V0KTtcbiAgICB9XG4gIH1cbn07XG4vLyB0eXBlIGJpdG1hcFxuJGV4cG9ydC5GID0gMTsgICAvLyBmb3JjZWRcbiRleHBvcnQuRyA9IDI7ICAgLy8gZ2xvYmFsXG4kZXhwb3J0LlMgPSA0OyAgIC8vIHN0YXRpY1xuJGV4cG9ydC5QID0gODsgICAvLyBwcm90b1xuJGV4cG9ydC5CID0gMTY7ICAvLyBiaW5kXG4kZXhwb3J0LlcgPSAzMjsgIC8vIHdyYXBcbiRleHBvcnQuVSA9IDY0OyAgLy8gc2FmZVxuJGV4cG9ydC5SID0gMTI4OyAvLyByZWFsIHByb3RvIG1ldGhvZCBmb3IgYGxpYnJhcnlgIFxubW9kdWxlLmV4cG9ydHMgPSAkZXhwb3J0O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZXhwb3J0LmpzXG4vLyBtb2R1bGUgaWQgPSA4XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBnO1xyXG5cclxuLy8gVGhpcyB3b3JrcyBpbiBub24tc3RyaWN0IG1vZGVcclxuZyA9IChmdW5jdGlvbigpIHtcclxuXHRyZXR1cm4gdGhpcztcclxufSkoKTtcclxuXHJcbnRyeSB7XHJcblx0Ly8gVGhpcyB3b3JrcyBpZiBldmFsIGlzIGFsbG93ZWQgKHNlZSBDU1ApXHJcblx0ZyA9IGcgfHwgRnVuY3Rpb24oXCJyZXR1cm4gdGhpc1wiKSgpIHx8ICgxLGV2YWwpKFwidGhpc1wiKTtcclxufSBjYXRjaChlKSB7XHJcblx0Ly8gVGhpcyB3b3JrcyBpZiB0aGUgd2luZG93IHJlZmVyZW5jZSBpcyBhdmFpbGFibGVcclxuXHRpZih0eXBlb2Ygd2luZG93ID09PSBcIm9iamVjdFwiKVxyXG5cdFx0ZyA9IHdpbmRvdztcclxufVxyXG5cclxuLy8gZyBjYW4gc3RpbGwgYmUgdW5kZWZpbmVkLCBidXQgbm90aGluZyB0byBkbyBhYm91dCBpdC4uLlxyXG4vLyBXZSByZXR1cm4gdW5kZWZpbmVkLCBpbnN0ZWFkIG9mIG5vdGhpbmcgaGVyZSwgc28gaXQnc1xyXG4vLyBlYXNpZXIgdG8gaGFuZGxlIHRoaXMgY2FzZS4gaWYoIWdsb2JhbCkgeyAuLi59XHJcblxyXG5tb2R1bGUuZXhwb3J0cyA9IGc7XHJcblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vICh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qc1xuLy8gbW9kdWxlIGlkID0gOVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA5IDExIDEyIDEzIDE3IDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ2IDU0IiwidmFyIGRQICAgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZHAnKVxuICAsIGNyZWF0ZURlc2MgPSByZXF1aXJlKCcuL19wcm9wZXJ0eS1kZXNjJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJykgPyBmdW5jdGlvbihvYmplY3QsIGtleSwgdmFsdWUpe1xuICByZXR1cm4gZFAuZihvYmplY3QsIGtleSwgY3JlYXRlRGVzYygxLCB2YWx1ZSkpO1xufSA6IGZ1bmN0aW9uKG9iamVjdCwga2V5LCB2YWx1ZSl7XG4gIG9iamVjdFtrZXldID0gdmFsdWU7XG4gIHJldHVybiBvYmplY3Q7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faGlkZS5qc1xuLy8gbW9kdWxlIGlkID0gMTBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi9faXMtb2JqZWN0Jyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgaWYoIWlzT2JqZWN0KGl0KSl0aHJvdyBUeXBlRXJyb3IoaXQgKyAnIGlzIG5vdCBhbiBvYmplY3QhJyk7XG4gIHJldHVybiBpdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hbi1vYmplY3QuanNcbi8vIG1vZHVsZSBpZCA9IDExXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oYml0bWFwLCB2YWx1ZSl7XG4gIHJldHVybiB7XG4gICAgZW51bWVyYWJsZSAgOiAhKGJpdG1hcCAmIDEpLFxuICAgIGNvbmZpZ3VyYWJsZTogIShiaXRtYXAgJiAyKSxcbiAgICB3cml0YWJsZSAgICA6ICEoYml0bWFwICYgNCksXG4gICAgdmFsdWUgICAgICAgOiB2YWx1ZVxuICB9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3Byb3BlcnR5LWRlc2MuanNcbi8vIG1vZHVsZSBpZCA9IDEyXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8vIDcuMS4xIFRvUHJpbWl0aXZlKGlucHV0IFssIFByZWZlcnJlZFR5cGVdKVxudmFyIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi9faXMtb2JqZWN0Jyk7XG4vLyBpbnN0ZWFkIG9mIHRoZSBFUzYgc3BlYyB2ZXJzaW9uLCB3ZSBkaWRuJ3QgaW1wbGVtZW50IEBAdG9QcmltaXRpdmUgY2FzZVxuLy8gYW5kIHRoZSBzZWNvbmQgYXJndW1lbnQgLSBmbGFnIC0gcHJlZmVycmVkIHR5cGUgaXMgYSBzdHJpbmdcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQsIFMpe1xuICBpZighaXNPYmplY3QoaXQpKXJldHVybiBpdDtcbiAgdmFyIGZuLCB2YWw7XG4gIGlmKFMgJiYgdHlwZW9mIChmbiA9IGl0LnRvU3RyaW5nKSA9PSAnZnVuY3Rpb24nICYmICFpc09iamVjdCh2YWwgPSBmbi5jYWxsKGl0KSkpcmV0dXJuIHZhbDtcbiAgaWYodHlwZW9mIChmbiA9IGl0LnZhbHVlT2YpID09ICdmdW5jdGlvbicgJiYgIWlzT2JqZWN0KHZhbCA9IGZuLmNhbGwoaXQpKSlyZXR1cm4gdmFsO1xuICBpZighUyAmJiB0eXBlb2YgKGZuID0gaXQudG9TdHJpbmcpID09ICdmdW5jdGlvbicgJiYgIWlzT2JqZWN0KHZhbCA9IGZuLmNhbGwoaXQpKSlyZXR1cm4gdmFsO1xuICB0aHJvdyBUeXBlRXJyb3IoXCJDYW4ndCBjb252ZXJ0IG9iamVjdCB0byBwcmltaXRpdmUgdmFsdWVcIik7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8tcHJpbWl0aXZlLmpzXG4vLyBtb2R1bGUgaWQgPSAxM1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyBvcHRpb25hbCAvIHNpbXBsZSBjb250ZXh0IGJpbmRpbmdcbnZhciBhRnVuY3Rpb24gPSByZXF1aXJlKCcuL19hLWZ1bmN0aW9uJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGZuLCB0aGF0LCBsZW5ndGgpe1xuICBhRnVuY3Rpb24oZm4pO1xuICBpZih0aGF0ID09PSB1bmRlZmluZWQpcmV0dXJuIGZuO1xuICBzd2l0Y2gobGVuZ3RoKXtcbiAgICBjYXNlIDE6IHJldHVybiBmdW5jdGlvbihhKXtcbiAgICAgIHJldHVybiBmbi5jYWxsKHRoYXQsIGEpO1xuICAgIH07XG4gICAgY2FzZSAyOiByZXR1cm4gZnVuY3Rpb24oYSwgYil7XG4gICAgICByZXR1cm4gZm4uY2FsbCh0aGF0LCBhLCBiKTtcbiAgICB9O1xuICAgIGNhc2UgMzogcmV0dXJuIGZ1bmN0aW9uKGEsIGIsIGMpe1xuICAgICAgcmV0dXJuIGZuLmNhbGwodGhhdCwgYSwgYiwgYyk7XG4gICAgfTtcbiAgfVxuICByZXR1cm4gZnVuY3Rpb24oLyogLi4uYXJncyAqLyl7XG4gICAgcmV0dXJuIGZuLmFwcGx5KHRoYXQsIGFyZ3VtZW50cyk7XG4gIH07XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY3R4LmpzXG4vLyBtb2R1bGUgaWQgPSAxNVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgaXNPYmplY3QgPSByZXF1aXJlKCcuL19pcy1vYmplY3QnKVxuICAsIGRvY3VtZW50ID0gcmVxdWlyZSgnLi9fZ2xvYmFsJykuZG9jdW1lbnRcbiAgLy8gaW4gb2xkIElFIHR5cGVvZiBkb2N1bWVudC5jcmVhdGVFbGVtZW50IGlzICdvYmplY3QnXG4gICwgaXMgPSBpc09iamVjdChkb2N1bWVudCkgJiYgaXNPYmplY3QoZG9jdW1lbnQuY3JlYXRlRWxlbWVudCk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGlzID8gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChpdCkgOiB7fTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kb20tY3JlYXRlLmpzXG4vLyBtb2R1bGUgaWQgPSAxNlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9ICFyZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpICYmICFyZXF1aXJlKCcuL19mYWlscycpKGZ1bmN0aW9uKCl7XG4gIHJldHVybiBPYmplY3QuZGVmaW5lUHJvcGVydHkocmVxdWlyZSgnLi9fZG9tLWNyZWF0ZScpKCdkaXYnKSwgJ2EnLCB7Z2V0OiBmdW5jdGlvbigpeyByZXR1cm4gNzsgfX0pLmEgIT0gNztcbn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faWU4LWRvbS1kZWZpbmUuanNcbi8vIG1vZHVsZSBpZCA9IDE3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICBpZih0eXBlb2YgaXQgIT0gJ2Z1bmN0aW9uJyl0aHJvdyBUeXBlRXJyb3IoaXQgKyAnIGlzIG5vdCBhIGZ1bmN0aW9uIScpO1xuICByZXR1cm4gaXQ7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYS1mdW5jdGlvbi5qc1xuLy8gbW9kdWxlIGlkID0gMThcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSB7IFwiZGVmYXVsdFwiOiByZXF1aXJlKFwiY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9kZWZpbmUtcHJvcGVydHlcIiksIF9fZXNNb2R1bGU6IHRydWUgfTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanNcbi8vIG1vZHVsZSBpZCA9IDE5XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM2Lm9iamVjdC5kZWZpbmUtcHJvcGVydHknKTtcbnZhciAkT2JqZWN0ID0gcmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9fY29yZScpLk9iamVjdDtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24gZGVmaW5lUHJvcGVydHkoaXQsIGtleSwgZGVzYyl7XG4gIHJldHVybiAkT2JqZWN0LmRlZmluZVByb3BlcnR5KGl0LCBrZXksIGRlc2MpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanNcbi8vIG1vZHVsZSBpZCA9IDIwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciAkZXhwb3J0ID0gcmVxdWlyZSgnLi9fZXhwb3J0Jyk7XG4vLyAxOS4xLjIuNCAvIDE1LjIuMy42IE9iamVjdC5kZWZpbmVQcm9wZXJ0eShPLCBQLCBBdHRyaWJ1dGVzKVxuJGV4cG9ydCgkZXhwb3J0LlMgKyAkZXhwb3J0LkYgKiAhcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSwgJ09iamVjdCcsIHtkZWZpbmVQcm9wZXJ0eTogcmVxdWlyZSgnLi9fb2JqZWN0LWRwJykuZn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LmRlZmluZS1wcm9wZXJ0eS5qc1xuLy8gbW9kdWxlIGlkID0gMjFcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLy8gdG8gaW5kZXhlZCBvYmplY3QsIHRvT2JqZWN0IHdpdGggZmFsbGJhY2sgZm9yIG5vbi1hcnJheS1saWtlIEVTMyBzdHJpbmdzXG52YXIgSU9iamVjdCA9IHJlcXVpcmUoJy4vX2lvYmplY3QnKVxuICAsIGRlZmluZWQgPSByZXF1aXJlKCcuL19kZWZpbmVkJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIElPYmplY3QoZGVmaW5lZChpdCkpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLWlvYmplY3QuanNcbi8vIG1vZHVsZSBpZCA9IDIyXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsInZhciBoYXNPd25Qcm9wZXJ0eSA9IHt9Lmhhc093blByb3BlcnR5O1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCwga2V5KXtcbiAgcmV0dXJuIGhhc093blByb3BlcnR5LmNhbGwoaXQsIGtleSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faGFzLmpzXG4vLyBtb2R1bGUgaWQgPSAyN1xuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJ2YXIgc3RvcmUgICAgICA9IHJlcXVpcmUoJy4vX3NoYXJlZCcpKCd3a3MnKVxuICAsIHVpZCAgICAgICAgPSByZXF1aXJlKCcuL191aWQnKVxuICAsIFN5bWJvbCAgICAgPSByZXF1aXJlKCcuL19nbG9iYWwnKS5TeW1ib2xcbiAgLCBVU0VfU1lNQk9MID0gdHlwZW9mIFN5bWJvbCA9PSAnZnVuY3Rpb24nO1xuXG52YXIgJGV4cG9ydHMgPSBtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKG5hbWUpe1xuICByZXR1cm4gc3RvcmVbbmFtZV0gfHwgKHN0b3JlW25hbWVdID1cbiAgICBVU0VfU1lNQk9MICYmIFN5bWJvbFtuYW1lXSB8fCAoVVNFX1NZTUJPTCA/IFN5bWJvbCA6IHVpZCkoJ1N5bWJvbC4nICsgbmFtZSkpO1xufTtcblxuJGV4cG9ydHMuc3RvcmUgPSBzdG9yZTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3drcy5qc1xuLy8gbW9kdWxlIGlkID0gMjlcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSAxNCIsIi8vIDE5LjEuMi4xNCAvIDE1LjIuMy4xNCBPYmplY3Qua2V5cyhPKVxudmFyICRrZXlzICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWtleXMtaW50ZXJuYWwnKVxuICAsIGVudW1CdWdLZXlzID0gcmVxdWlyZSgnLi9fZW51bS1idWcta2V5cycpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IE9iamVjdC5rZXlzIHx8IGZ1bmN0aW9uIGtleXMoTyl7XG4gIHJldHVybiAka2V5cyhPLCBlbnVtQnVnS2V5cyk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWtleXMuanNcbi8vIG1vZHVsZSBpZCA9IDM0XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IEV2ZW50RW1pdHRlckNsYXNzIGZyb20gJ2V2ZW50cyc7XG5cbi8qKlxuICogV2UgaW5zdGFuY2lhdGUgb25lIEV2ZW50RW1pdHRlciAocmVzdHJpY3RlZCB2aWEgYSBjb25zdCkgc28gdGhhdCBldmVyeSBjb21wb25lbnRzXG4gKiByZWdpc3Rlci9kaXNwYXRjaCBvbiB0aGUgc2FtZSBvbmUgYW5kIGNhbiBjb21tdW5pY2F0ZSB3aXRoIGVhY2ggb3RoZXIuXG4gKi9cbmV4cG9ydCBjb25zdCBFdmVudEVtaXR0ZXIgPSBuZXcgRXZlbnRFbWl0dGVyQ2xhc3MoKTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZXZlbnQtZW1pdHRlci5qcyIsIi8vIDcuMi4xIFJlcXVpcmVPYmplY3RDb2VyY2libGUoYXJndW1lbnQpXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgaWYoaXQgPT0gdW5kZWZpbmVkKXRocm93IFR5cGVFcnJvcihcIkNhbid0IGNhbGwgbWV0aG9kIG9uICBcIiArIGl0KTtcbiAgcmV0dXJuIGl0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2RlZmluZWQuanNcbi8vIG1vZHVsZSBpZCA9IDM4XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIi8vIDcuMS40IFRvSW50ZWdlclxudmFyIGNlaWwgID0gTWF0aC5jZWlsXG4gICwgZmxvb3IgPSBNYXRoLmZsb29yO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIHJldHVybiBpc05hTihpdCA9ICtpdCkgPyAwIDogKGl0ID4gMCA/IGZsb29yIDogY2VpbCkoaXQpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLWludGVnZXIuanNcbi8vIG1vZHVsZSBpZCA9IDM5XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsInZhciBpZCA9IDBcbiAgLCBweCA9IE1hdGgucmFuZG9tKCk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGtleSl7XG4gIHJldHVybiAnU3ltYm9sKCcuY29uY2F0KGtleSA9PT0gdW5kZWZpbmVkID8gJycgOiBrZXksICcpXycsICgrK2lkICsgcHgpLnRvU3RyaW5nKDM2KSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdWlkLmpzXG4vLyBtb2R1bGUgaWQgPSA0M1xuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDb25maXJtTW9kYWwgY29tcG9uZW50XG4gKlxuICogQHBhcmFtIHtTdHJpbmd9IGlkXG4gKiBAcGFyYW0ge1N0cmluZ30gY29uZmlybVRpdGxlXG4gKiBAcGFyYW0ge1N0cmluZ30gY29uZmlybU1lc3NhZ2VcbiAqIEBwYXJhbSB7U3RyaW5nfSBjbG9zZUJ1dHRvbkxhYmVsXG4gKiBAcGFyYW0ge1N0cmluZ30gY29uZmlybUJ1dHRvbkxhYmVsXG4gKiBAcGFyYW0ge1N0cmluZ30gY29uZmlybUJ1dHRvbkNsYXNzXG4gKiBAcGFyYW0ge0Jvb2xlYW59IGNsb3NhYmxlXG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSBjb25maXJtQ2FsbGJhY2tcbiAqXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIENvbmZpcm1Nb2RhbChwYXJhbXMsIGNvbmZpcm1DYWxsYmFjaykge1xuICAvLyBDb25zdHJ1Y3QgdGhlIG1vZGFsXG4gIGNvbnN0IHtpZCwgY2xvc2FibGV9ID0gcGFyYW1zO1xuICB0aGlzLm1vZGFsID0gTW9kYWwocGFyYW1zKTtcblxuICAvLyBqUXVlcnkgbW9kYWwgb2JqZWN0XG4gIHRoaXMuJG1vZGFsID0gJCh0aGlzLm1vZGFsLmNvbnRhaW5lcik7XG5cbiAgdGhpcy5zaG93ID0gKCkgPT4ge1xuICAgIHRoaXMuJG1vZGFsLm1vZGFsKCk7XG4gIH07XG5cbiAgdGhpcy5tb2RhbC5jb25maXJtQnV0dG9uLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgY29uZmlybUNhbGxiYWNrKTtcblxuICB0aGlzLiRtb2RhbC5tb2RhbCh7XG4gICAgYmFja2Ryb3A6IChjbG9zYWJsZSA/IHRydWUgOiAnc3RhdGljJyksXG4gICAga2V5Ym9hcmQ6IGNsb3NhYmxlICE9PSB1bmRlZmluZWQgPyBjbG9zYWJsZSA6IHRydWUsXG4gICAgY2xvc2FibGU6IGNsb3NhYmxlICE9PSB1bmRlZmluZWQgPyBjbG9zYWJsZSA6IHRydWUsXG4gICAgc2hvdzogZmFsc2UsXG4gIH0pO1xuXG4gIHRoaXMuJG1vZGFsLm9uKCdoaWRkZW4uYnMubW9kYWwnLCAoKSA9PiB7XG4gICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvcihgIyR7aWR9YCkucmVtb3ZlKCk7XG4gIH0pO1xuXG4gIGRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQodGhpcy5tb2RhbC5jb250YWluZXIpO1xufVxuXG4vKipcbiAqIE1vZGFsIGNvbXBvbmVudCB0byBpbXByb3ZlIGxpc2liaWxpdHkgYnkgY29uc3RydWN0aW5nIHRoZSBtb2RhbCBvdXRzaWRlIHRoZSBtYWluIGZ1bmN0aW9uXG4gKlxuICogQHBhcmFtIHtPYmplY3R9IHBhcmFtc1xuICpcbiAqL1xuZnVuY3Rpb24gTW9kYWwoXG4gIHtcbiAgICBpZCA9ICdjb25maXJtX21vZGFsJyxcbiAgICBjb25maXJtVGl0bGUsXG4gICAgY29uZmlybU1lc3NhZ2UgPSAnJyxcbiAgICBjbG9zZUJ1dHRvbkxhYmVsID0gJ0Nsb3NlJyxcbiAgICBjb25maXJtQnV0dG9uTGFiZWwgPSAnQWNjZXB0JyxcbiAgICBjb25maXJtQnV0dG9uQ2xhc3MgPSAnYnRuLXByaW1hcnknLFxuICB9KSB7XG4gIGNvbnN0IG1vZGFsID0ge307XG5cbiAgLy8gTWFpbiBtb2RhbCBlbGVtZW50XG4gIG1vZGFsLmNvbnRhaW5lciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICBtb2RhbC5jb250YWluZXIuY2xhc3NMaXN0LmFkZCgnbW9kYWwnLCAnZmFkZScpO1xuICBtb2RhbC5jb250YWluZXIuaWQgPSBpZDtcblxuICAvLyBNb2RhbCBkaWFsb2cgZWxlbWVudFxuICBtb2RhbC5kaWFsb2cgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuZGlhbG9nLmNsYXNzTGlzdC5hZGQoJ21vZGFsLWRpYWxvZycpO1xuXG4gIC8vIE1vZGFsIGNvbnRlbnQgZWxlbWVudFxuICBtb2RhbC5jb250ZW50ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gIG1vZGFsLmNvbnRlbnQuY2xhc3NMaXN0LmFkZCgnbW9kYWwtY29udGVudCcpO1xuXG4gIC8vIE1vZGFsIGhlYWRlciBlbGVtZW50XG4gIG1vZGFsLmhlYWRlciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICBtb2RhbC5oZWFkZXIuY2xhc3NMaXN0LmFkZCgnbW9kYWwtaGVhZGVyJyk7XG5cbiAgLy8gTW9kYWwgdGl0bGUgZWxlbWVudFxuICBpZiAoY29uZmlybVRpdGxlKSB7XG4gICAgbW9kYWwudGl0bGUgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdoNCcpO1xuICAgIG1vZGFsLnRpdGxlLmNsYXNzTGlzdC5hZGQoJ21vZGFsLXRpdGxlJyk7XG4gICAgbW9kYWwudGl0bGUuaW5uZXJIVE1MID0gY29uZmlybVRpdGxlO1xuICB9XG5cbiAgLy8gTW9kYWwgY2xvc2UgYnV0dG9uIGljb25cbiAgbW9kYWwuY2xvc2VJY29uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYnV0dG9uJyk7XG4gIG1vZGFsLmNsb3NlSWNvbi5jbGFzc0xpc3QuYWRkKCdjbG9zZScpO1xuICBtb2RhbC5jbG9zZUljb24uc2V0QXR0cmlidXRlKCd0eXBlJywgJ2J1dHRvbicpO1xuICBtb2RhbC5jbG9zZUljb24uZGF0YXNldC5kaXNtaXNzID0gJ21vZGFsJztcbiAgbW9kYWwuY2xvc2VJY29uLmlubmVySFRNTCA9ICfDlyc7XG5cbiAgLy8gTW9kYWwgYm9keSBlbGVtZW50XG4gIG1vZGFsLmJvZHkgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuYm9keS5jbGFzc0xpc3QuYWRkKCdtb2RhbC1ib2R5JywgJ3RleHQtbGVmdCcsICdmb250LXdlaWdodC1ub3JtYWwnKTtcblxuICAvLyBNb2RhbCBtZXNzYWdlIGVsZW1lbnRcbiAgbW9kYWwubWVzc2FnZSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ3AnKTtcbiAgbW9kYWwubWVzc2FnZS5jbGFzc0xpc3QuYWRkKCdjb25maXJtLW1lc3NhZ2UnKTtcbiAgbW9kYWwubWVzc2FnZS5pbm5lckhUTUwgPSBjb25maXJtTWVzc2FnZTtcblxuICAvLyBNb2RhbCBmb290ZXIgZWxlbWVudFxuICBtb2RhbC5mb290ZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuZm9vdGVyLmNsYXNzTGlzdC5hZGQoJ21vZGFsLWZvb3RlcicpO1xuXG4gIC8vIE1vZGFsIGNsb3NlIGJ1dHRvbiBlbGVtZW50XG4gIG1vZGFsLmNsb3NlQnV0dG9uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYnV0dG9uJyk7XG4gIG1vZGFsLmNsb3NlQnV0dG9uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgbW9kYWwuY2xvc2VCdXR0b24uY2xhc3NMaXN0LmFkZCgnYnRuJywgJ2J0bi1vdXRsaW5lLXNlY29uZGFyeScsICdidG4tbGcnKTtcbiAgbW9kYWwuY2xvc2VCdXR0b24uZGF0YXNldC5kaXNtaXNzID0gJ21vZGFsJztcbiAgbW9kYWwuY2xvc2VCdXR0b24uaW5uZXJIVE1MID0gY2xvc2VCdXR0b25MYWJlbDtcblxuICAvLyBNb2RhbCBjbG9zZSBidXR0b24gZWxlbWVudFxuICBtb2RhbC5jb25maXJtQnV0dG9uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYnV0dG9uJyk7XG4gIG1vZGFsLmNvbmZpcm1CdXR0b24uc2V0QXR0cmlidXRlKCd0eXBlJywgJ2J1dHRvbicpO1xuICBtb2RhbC5jb25maXJtQnV0dG9uLmNsYXNzTGlzdC5hZGQoJ2J0bicsIGNvbmZpcm1CdXR0b25DbGFzcywgJ2J0bi1sZycsICdidG4tY29uZmlybS1zdWJtaXQnKTtcbiAgbW9kYWwuY29uZmlybUJ1dHRvbi5kYXRhc2V0LmRpc21pc3MgPSAnbW9kYWwnO1xuICBtb2RhbC5jb25maXJtQnV0dG9uLmlubmVySFRNTCA9IGNvbmZpcm1CdXR0b25MYWJlbDtcblxuICAvLyBDb25zdHJ1Y3RpbmcgdGhlIG1vZGFsXG4gIGlmIChjb25maXJtVGl0bGUpIHtcbiAgICBtb2RhbC5oZWFkZXIuYXBwZW5kKG1vZGFsLnRpdGxlLCBtb2RhbC5jbG9zZUljb24pO1xuICB9IGVsc2Uge1xuICAgIG1vZGFsLmhlYWRlci5hcHBlbmRDaGlsZChtb2RhbC5jbG9zZUljb24pO1xuICB9XG5cbiAgbW9kYWwuYm9keS5hcHBlbmRDaGlsZChtb2RhbC5tZXNzYWdlKTtcbiAgbW9kYWwuZm9vdGVyLmFwcGVuZChtb2RhbC5jbG9zZUJ1dHRvbiwgbW9kYWwuY29uZmlybUJ1dHRvbik7XG4gIG1vZGFsLmNvbnRlbnQuYXBwZW5kKG1vZGFsLmhlYWRlciwgbW9kYWwuYm9keSwgbW9kYWwuZm9vdGVyKTtcbiAgbW9kYWwuZGlhbG9nLmFwcGVuZENoaWxkKG1vZGFsLmNvbnRlbnQpO1xuICBtb2RhbC5jb250YWluZXIuYXBwZW5kQ2hpbGQobW9kYWwuZGlhbG9nKTtcblxuICByZXR1cm4gbW9kYWw7XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL21vZGFsLmpzIiwiLy8gNy4xLjEzIFRvT2JqZWN0KGFyZ3VtZW50KVxudmFyIGRlZmluZWQgPSByZXF1aXJlKCcuL19kZWZpbmVkJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIE9iamVjdChkZWZpbmVkKGl0KSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8tb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSA0NlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJ2YXIgc2hhcmVkID0gcmVxdWlyZSgnLi9fc2hhcmVkJykoJ2tleXMnKVxuICAsIHVpZCAgICA9IHJlcXVpcmUoJy4vX3VpZCcpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihrZXkpe1xuICByZXR1cm4gc2hhcmVkW2tleV0gfHwgKHNoYXJlZFtrZXldID0gdWlkKGtleSkpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NoYXJlZC1rZXkuanNcbi8vIG1vZHVsZSBpZCA9IDQ3XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsInZhciB0b1N0cmluZyA9IHt9LnRvU3RyaW5nO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIHRvU3RyaW5nLmNhbGwoaXQpLnNsaWNlKDgsIC0xKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jb2YuanNcbi8vIG1vZHVsZSBpZCA9IDQ4XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIi8vIElFIDgtIGRvbid0IGVudW0gYnVnIGtleXNcbm1vZHVsZS5leHBvcnRzID0gKFxuICAnY29uc3RydWN0b3IsaGFzT3duUHJvcGVydHksaXNQcm90b3R5cGVPZixwcm9wZXJ0eUlzRW51bWVyYWJsZSx0b0xvY2FsZVN0cmluZyx0b1N0cmluZyx2YWx1ZU9mJ1xuKS5zcGxpdCgnLCcpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZW51bS1idWcta2V5cy5qc1xuLy8gbW9kdWxlIGlkID0gNDlcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwidmFyIGdsb2JhbCA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpXG4gICwgU0hBUkVEID0gJ19fY29yZS1qc19zaGFyZWRfXydcbiAgLCBzdG9yZSAgPSBnbG9iYWxbU0hBUkVEXSB8fCAoZ2xvYmFsW1NIQVJFRF0gPSB7fSk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGtleSl7XG4gIHJldHVybiBzdG9yZVtrZXldIHx8IChzdG9yZVtrZXldID0ge30pO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NoYXJlZC5qc1xuLy8gbW9kdWxlIGlkID0gNTBcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLy8gZmFsbGJhY2sgZm9yIG5vbi1hcnJheS1saWtlIEVTMyBhbmQgbm9uLWVudW1lcmFibGUgb2xkIFY4IHN0cmluZ3NcbnZhciBjb2YgPSByZXF1aXJlKCcuL19jb2YnKTtcbm1vZHVsZS5leHBvcnRzID0gT2JqZWN0KCd6JykucHJvcGVydHlJc0VudW1lcmFibGUoMCkgPyBPYmplY3QgOiBmdW5jdGlvbihpdCl7XG4gIHJldHVybiBjb2YoaXQpID09ICdTdHJpbmcnID8gaXQuc3BsaXQoJycpIDogT2JqZWN0KGl0KTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSA1MVxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJleHBvcnRzLmYgPSB7fS5wcm9wZXJ0eUlzRW51bWVyYWJsZTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1waWUuanNcbi8vIG1vZHVsZSBpZCA9IDUyXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNSAxNiAxOCIsIi8vIENvcHlyaWdodCBKb3llbnQsIEluYy4gYW5kIG90aGVyIE5vZGUgY29udHJpYnV0b3JzLlxuLy9cbi8vIFBlcm1pc3Npb24gaXMgaGVyZWJ5IGdyYW50ZWQsIGZyZWUgb2YgY2hhcmdlLCB0byBhbnkgcGVyc29uIG9idGFpbmluZyBhXG4vLyBjb3B5IG9mIHRoaXMgc29mdHdhcmUgYW5kIGFzc29jaWF0ZWQgZG9jdW1lbnRhdGlvbiBmaWxlcyAodGhlXG4vLyBcIlNvZnR3YXJlXCIpLCB0byBkZWFsIGluIHRoZSBTb2Z0d2FyZSB3aXRob3V0IHJlc3RyaWN0aW9uLCBpbmNsdWRpbmdcbi8vIHdpdGhvdXQgbGltaXRhdGlvbiB0aGUgcmlnaHRzIHRvIHVzZSwgY29weSwgbW9kaWZ5LCBtZXJnZSwgcHVibGlzaCxcbi8vIGRpc3RyaWJ1dGUsIHN1YmxpY2Vuc2UsIGFuZC9vciBzZWxsIGNvcGllcyBvZiB0aGUgU29mdHdhcmUsIGFuZCB0byBwZXJtaXRcbi8vIHBlcnNvbnMgdG8gd2hvbSB0aGUgU29mdHdhcmUgaXMgZnVybmlzaGVkIHRvIGRvIHNvLCBzdWJqZWN0IHRvIHRoZVxuLy8gZm9sbG93aW5nIGNvbmRpdGlvbnM6XG4vL1xuLy8gVGhlIGFib3ZlIGNvcHlyaWdodCBub3RpY2UgYW5kIHRoaXMgcGVybWlzc2lvbiBub3RpY2Ugc2hhbGwgYmUgaW5jbHVkZWRcbi8vIGluIGFsbCBjb3BpZXMgb3Igc3Vic3RhbnRpYWwgcG9ydGlvbnMgb2YgdGhlIFNvZnR3YXJlLlxuLy9cbi8vIFRIRSBTT0ZUV0FSRSBJUyBQUk9WSURFRCBcIkFTIElTXCIsIFdJVEhPVVQgV0FSUkFOVFkgT0YgQU5ZIEtJTkQsIEVYUFJFU1Ncbi8vIE9SIElNUExJRUQsIElOQ0xVRElORyBCVVQgTk9UIExJTUlURUQgVE8gVEhFIFdBUlJBTlRJRVMgT0Zcbi8vIE1FUkNIQU5UQUJJTElUWSwgRklUTkVTUyBGT1IgQSBQQVJUSUNVTEFSIFBVUlBPU0UgQU5EIE5PTklORlJJTkdFTUVOVC4gSU5cbi8vIE5PIEVWRU5UIFNIQUxMIFRIRSBBVVRIT1JTIE9SIENPUFlSSUdIVCBIT0xERVJTIEJFIExJQUJMRSBGT1IgQU5ZIENMQUlNLFxuLy8gREFNQUdFUyBPUiBPVEhFUiBMSUFCSUxJVFksIFdIRVRIRVIgSU4gQU4gQUNUSU9OIE9GIENPTlRSQUNULCBUT1JUIE9SXG4vLyBPVEhFUldJU0UsIEFSSVNJTkcgRlJPTSwgT1VUIE9GIE9SIElOIENPTk5FQ1RJT04gV0lUSCBUSEUgU09GVFdBUkUgT1IgVEhFXG4vLyBVU0UgT1IgT1RIRVIgREVBTElOR1MgSU4gVEhFIFNPRlRXQVJFLlxuXG4ndXNlIHN0cmljdCc7XG5cbnZhciBSID0gdHlwZW9mIFJlZmxlY3QgPT09ICdvYmplY3QnID8gUmVmbGVjdCA6IG51bGxcbnZhciBSZWZsZWN0QXBwbHkgPSBSICYmIHR5cGVvZiBSLmFwcGx5ID09PSAnZnVuY3Rpb24nXG4gID8gUi5hcHBseVxuICA6IGZ1bmN0aW9uIFJlZmxlY3RBcHBseSh0YXJnZXQsIHJlY2VpdmVyLCBhcmdzKSB7XG4gICAgcmV0dXJuIEZ1bmN0aW9uLnByb3RvdHlwZS5hcHBseS5jYWxsKHRhcmdldCwgcmVjZWl2ZXIsIGFyZ3MpO1xuICB9XG5cbnZhciBSZWZsZWN0T3duS2V5c1xuaWYgKFIgJiYgdHlwZW9mIFIub3duS2V5cyA9PT0gJ2Z1bmN0aW9uJykge1xuICBSZWZsZWN0T3duS2V5cyA9IFIub3duS2V5c1xufSBlbHNlIGlmIChPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzKSB7XG4gIFJlZmxlY3RPd25LZXlzID0gZnVuY3Rpb24gUmVmbGVjdE93bktleXModGFyZ2V0KSB7XG4gICAgcmV0dXJuIE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKHRhcmdldClcbiAgICAgIC5jb25jYXQoT2JqZWN0LmdldE93blByb3BlcnR5U3ltYm9scyh0YXJnZXQpKTtcbiAgfTtcbn0gZWxzZSB7XG4gIFJlZmxlY3RPd25LZXlzID0gZnVuY3Rpb24gUmVmbGVjdE93bktleXModGFyZ2V0KSB7XG4gICAgcmV0dXJuIE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKHRhcmdldCk7XG4gIH07XG59XG5cbmZ1bmN0aW9uIFByb2Nlc3NFbWl0V2FybmluZyh3YXJuaW5nKSB7XG4gIGlmIChjb25zb2xlICYmIGNvbnNvbGUud2FybikgY29uc29sZS53YXJuKHdhcm5pbmcpO1xufVxuXG52YXIgTnVtYmVySXNOYU4gPSBOdW1iZXIuaXNOYU4gfHwgZnVuY3Rpb24gTnVtYmVySXNOYU4odmFsdWUpIHtcbiAgcmV0dXJuIHZhbHVlICE9PSB2YWx1ZTtcbn1cblxuZnVuY3Rpb24gRXZlbnRFbWl0dGVyKCkge1xuICBFdmVudEVtaXR0ZXIuaW5pdC5jYWxsKHRoaXMpO1xufVxubW9kdWxlLmV4cG9ydHMgPSBFdmVudEVtaXR0ZXI7XG5cbi8vIEJhY2t3YXJkcy1jb21wYXQgd2l0aCBub2RlIDAuMTAueFxuRXZlbnRFbWl0dGVyLkV2ZW50RW1pdHRlciA9IEV2ZW50RW1pdHRlcjtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5fZXZlbnRzID0gdW5kZWZpbmVkO1xuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5fZXZlbnRzQ291bnQgPSAwO1xuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5fbWF4TGlzdGVuZXJzID0gdW5kZWZpbmVkO1xuXG4vLyBCeSBkZWZhdWx0IEV2ZW50RW1pdHRlcnMgd2lsbCBwcmludCBhIHdhcm5pbmcgaWYgbW9yZSB0aGFuIDEwIGxpc3RlbmVycyBhcmVcbi8vIGFkZGVkIHRvIGl0LiBUaGlzIGlzIGEgdXNlZnVsIGRlZmF1bHQgd2hpY2ggaGVscHMgZmluZGluZyBtZW1vcnkgbGVha3MuXG52YXIgZGVmYXVsdE1heExpc3RlbmVycyA9IDEwO1xuXG5PYmplY3QuZGVmaW5lUHJvcGVydHkoRXZlbnRFbWl0dGVyLCAnZGVmYXVsdE1heExpc3RlbmVycycsIHtcbiAgZW51bWVyYWJsZTogdHJ1ZSxcbiAgZ2V0OiBmdW5jdGlvbigpIHtcbiAgICByZXR1cm4gZGVmYXVsdE1heExpc3RlbmVycztcbiAgfSxcbiAgc2V0OiBmdW5jdGlvbihhcmcpIHtcbiAgICBpZiAodHlwZW9mIGFyZyAhPT0gJ251bWJlcicgfHwgYXJnIDwgMCB8fCBOdW1iZXJJc05hTihhcmcpKSB7XG4gICAgICB0aHJvdyBuZXcgUmFuZ2VFcnJvcignVGhlIHZhbHVlIG9mIFwiZGVmYXVsdE1heExpc3RlbmVyc1wiIGlzIG91dCBvZiByYW5nZS4gSXQgbXVzdCBiZSBhIG5vbi1uZWdhdGl2ZSBudW1iZXIuIFJlY2VpdmVkICcgKyBhcmcgKyAnLicpO1xuICAgIH1cbiAgICBkZWZhdWx0TWF4TGlzdGVuZXJzID0gYXJnO1xuICB9XG59KTtcblxuRXZlbnRFbWl0dGVyLmluaXQgPSBmdW5jdGlvbigpIHtcblxuICBpZiAodGhpcy5fZXZlbnRzID09PSB1bmRlZmluZWQgfHxcbiAgICAgIHRoaXMuX2V2ZW50cyA9PT0gT2JqZWN0LmdldFByb3RvdHlwZU9mKHRoaXMpLl9ldmVudHMpIHtcbiAgICB0aGlzLl9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgIHRoaXMuX2V2ZW50c0NvdW50ID0gMDtcbiAgfVxuXG4gIHRoaXMuX21heExpc3RlbmVycyA9IHRoaXMuX21heExpc3RlbmVycyB8fCB1bmRlZmluZWQ7XG59O1xuXG4vLyBPYnZpb3VzbHkgbm90IGFsbCBFbWl0dGVycyBzaG91bGQgYmUgbGltaXRlZCB0byAxMC4gVGhpcyBmdW5jdGlvbiBhbGxvd3Ncbi8vIHRoYXQgdG8gYmUgaW5jcmVhc2VkLiBTZXQgdG8gemVybyBmb3IgdW5saW1pdGVkLlxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5zZXRNYXhMaXN0ZW5lcnMgPSBmdW5jdGlvbiBzZXRNYXhMaXN0ZW5lcnMobikge1xuICBpZiAodHlwZW9mIG4gIT09ICdudW1iZXInIHx8IG4gPCAwIHx8IE51bWJlcklzTmFOKG4pKSB7XG4gICAgdGhyb3cgbmV3IFJhbmdlRXJyb3IoJ1RoZSB2YWx1ZSBvZiBcIm5cIiBpcyBvdXQgb2YgcmFuZ2UuIEl0IG11c3QgYmUgYSBub24tbmVnYXRpdmUgbnVtYmVyLiBSZWNlaXZlZCAnICsgbiArICcuJyk7XG4gIH1cbiAgdGhpcy5fbWF4TGlzdGVuZXJzID0gbjtcbiAgcmV0dXJuIHRoaXM7XG59O1xuXG5mdW5jdGlvbiAkZ2V0TWF4TGlzdGVuZXJzKHRoYXQpIHtcbiAgaWYgKHRoYXQuX21heExpc3RlbmVycyA9PT0gdW5kZWZpbmVkKVxuICAgIHJldHVybiBFdmVudEVtaXR0ZXIuZGVmYXVsdE1heExpc3RlbmVycztcbiAgcmV0dXJuIHRoYXQuX21heExpc3RlbmVycztcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5nZXRNYXhMaXN0ZW5lcnMgPSBmdW5jdGlvbiBnZXRNYXhMaXN0ZW5lcnMoKSB7XG4gIHJldHVybiAkZ2V0TWF4TGlzdGVuZXJzKHRoaXMpO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5lbWl0ID0gZnVuY3Rpb24gZW1pdCh0eXBlKSB7XG4gIHZhciBhcmdzID0gW107XG4gIGZvciAodmFyIGkgPSAxOyBpIDwgYXJndW1lbnRzLmxlbmd0aDsgaSsrKSBhcmdzLnB1c2goYXJndW1lbnRzW2ldKTtcbiAgdmFyIGRvRXJyb3IgPSAodHlwZSA9PT0gJ2Vycm9yJyk7XG5cbiAgdmFyIGV2ZW50cyA9IHRoaXMuX2V2ZW50cztcbiAgaWYgKGV2ZW50cyAhPT0gdW5kZWZpbmVkKVxuICAgIGRvRXJyb3IgPSAoZG9FcnJvciAmJiBldmVudHMuZXJyb3IgPT09IHVuZGVmaW5lZCk7XG4gIGVsc2UgaWYgKCFkb0Vycm9yKVxuICAgIHJldHVybiBmYWxzZTtcblxuICAvLyBJZiB0aGVyZSBpcyBubyAnZXJyb3InIGV2ZW50IGxpc3RlbmVyIHRoZW4gdGhyb3cuXG4gIGlmIChkb0Vycm9yKSB7XG4gICAgdmFyIGVyO1xuICAgIGlmIChhcmdzLmxlbmd0aCA+IDApXG4gICAgICBlciA9IGFyZ3NbMF07XG4gICAgaWYgKGVyIGluc3RhbmNlb2YgRXJyb3IpIHtcbiAgICAgIC8vIE5vdGU6IFRoZSBjb21tZW50cyBvbiB0aGUgYHRocm93YCBsaW5lcyBhcmUgaW50ZW50aW9uYWwsIHRoZXkgc2hvd1xuICAgICAgLy8gdXAgaW4gTm9kZSdzIG91dHB1dCBpZiB0aGlzIHJlc3VsdHMgaW4gYW4gdW5oYW5kbGVkIGV4Y2VwdGlvbi5cbiAgICAgIHRocm93IGVyOyAvLyBVbmhhbmRsZWQgJ2Vycm9yJyBldmVudFxuICAgIH1cbiAgICAvLyBBdCBsZWFzdCBnaXZlIHNvbWUga2luZCBvZiBjb250ZXh0IHRvIHRoZSB1c2VyXG4gICAgdmFyIGVyciA9IG5ldyBFcnJvcignVW5oYW5kbGVkIGVycm9yLicgKyAoZXIgPyAnICgnICsgZXIubWVzc2FnZSArICcpJyA6ICcnKSk7XG4gICAgZXJyLmNvbnRleHQgPSBlcjtcbiAgICB0aHJvdyBlcnI7IC8vIFVuaGFuZGxlZCAnZXJyb3InIGV2ZW50XG4gIH1cblxuICB2YXIgaGFuZGxlciA9IGV2ZW50c1t0eXBlXTtcblxuICBpZiAoaGFuZGxlciA9PT0gdW5kZWZpbmVkKVxuICAgIHJldHVybiBmYWxzZTtcblxuICBpZiAodHlwZW9mIGhhbmRsZXIgPT09ICdmdW5jdGlvbicpIHtcbiAgICBSZWZsZWN0QXBwbHkoaGFuZGxlciwgdGhpcywgYXJncyk7XG4gIH0gZWxzZSB7XG4gICAgdmFyIGxlbiA9IGhhbmRsZXIubGVuZ3RoO1xuICAgIHZhciBsaXN0ZW5lcnMgPSBhcnJheUNsb25lKGhhbmRsZXIsIGxlbik7XG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBsZW47ICsraSlcbiAgICAgIFJlZmxlY3RBcHBseShsaXN0ZW5lcnNbaV0sIHRoaXMsIGFyZ3MpO1xuICB9XG5cbiAgcmV0dXJuIHRydWU7XG59O1xuXG5mdW5jdGlvbiBfYWRkTGlzdGVuZXIodGFyZ2V0LCB0eXBlLCBsaXN0ZW5lciwgcHJlcGVuZCkge1xuICB2YXIgbTtcbiAgdmFyIGV2ZW50cztcbiAgdmFyIGV4aXN0aW5nO1xuXG4gIGlmICh0eXBlb2YgbGlzdGVuZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgfVxuXG4gIGV2ZW50cyA9IHRhcmdldC5fZXZlbnRzO1xuICBpZiAoZXZlbnRzID09PSB1bmRlZmluZWQpIHtcbiAgICBldmVudHMgPSB0YXJnZXQuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgdGFyZ2V0Ll9ldmVudHNDb3VudCA9IDA7XG4gIH0gZWxzZSB7XG4gICAgLy8gVG8gYXZvaWQgcmVjdXJzaW9uIGluIHRoZSBjYXNlIHRoYXQgdHlwZSA9PT0gXCJuZXdMaXN0ZW5lclwiISBCZWZvcmVcbiAgICAvLyBhZGRpbmcgaXQgdG8gdGhlIGxpc3RlbmVycywgZmlyc3QgZW1pdCBcIm5ld0xpc3RlbmVyXCIuXG4gICAgaWYgKGV2ZW50cy5uZXdMaXN0ZW5lciAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICB0YXJnZXQuZW1pdCgnbmV3TGlzdGVuZXInLCB0eXBlLFxuICAgICAgICAgICAgICAgICAgbGlzdGVuZXIubGlzdGVuZXIgPyBsaXN0ZW5lci5saXN0ZW5lciA6IGxpc3RlbmVyKTtcblxuICAgICAgLy8gUmUtYXNzaWduIGBldmVudHNgIGJlY2F1c2UgYSBuZXdMaXN0ZW5lciBoYW5kbGVyIGNvdWxkIGhhdmUgY2F1c2VkIHRoZVxuICAgICAgLy8gdGhpcy5fZXZlbnRzIHRvIGJlIGFzc2lnbmVkIHRvIGEgbmV3IG9iamVjdFxuICAgICAgZXZlbnRzID0gdGFyZ2V0Ll9ldmVudHM7XG4gICAgfVxuICAgIGV4aXN0aW5nID0gZXZlbnRzW3R5cGVdO1xuICB9XG5cbiAgaWYgKGV4aXN0aW5nID09PSB1bmRlZmluZWQpIHtcbiAgICAvLyBPcHRpbWl6ZSB0aGUgY2FzZSBvZiBvbmUgbGlzdGVuZXIuIERvbid0IG5lZWQgdGhlIGV4dHJhIGFycmF5IG9iamVjdC5cbiAgICBleGlzdGluZyA9IGV2ZW50c1t0eXBlXSA9IGxpc3RlbmVyO1xuICAgICsrdGFyZ2V0Ll9ldmVudHNDb3VudDtcbiAgfSBlbHNlIHtcbiAgICBpZiAodHlwZW9mIGV4aXN0aW5nID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAvLyBBZGRpbmcgdGhlIHNlY29uZCBlbGVtZW50LCBuZWVkIHRvIGNoYW5nZSB0byBhcnJheS5cbiAgICAgIGV4aXN0aW5nID0gZXZlbnRzW3R5cGVdID1cbiAgICAgICAgcHJlcGVuZCA/IFtsaXN0ZW5lciwgZXhpc3RpbmddIDogW2V4aXN0aW5nLCBsaXN0ZW5lcl07XG4gICAgICAvLyBJZiB3ZSd2ZSBhbHJlYWR5IGdvdCBhbiBhcnJheSwganVzdCBhcHBlbmQuXG4gICAgfSBlbHNlIGlmIChwcmVwZW5kKSB7XG4gICAgICBleGlzdGluZy51bnNoaWZ0KGxpc3RlbmVyKTtcbiAgICB9IGVsc2Uge1xuICAgICAgZXhpc3RpbmcucHVzaChsaXN0ZW5lcik7XG4gICAgfVxuXG4gICAgLy8gQ2hlY2sgZm9yIGxpc3RlbmVyIGxlYWtcbiAgICBtID0gJGdldE1heExpc3RlbmVycyh0YXJnZXQpO1xuICAgIGlmIChtID4gMCAmJiBleGlzdGluZy5sZW5ndGggPiBtICYmICFleGlzdGluZy53YXJuZWQpIHtcbiAgICAgIGV4aXN0aW5nLndhcm5lZCA9IHRydWU7XG4gICAgICAvLyBObyBlcnJvciBjb2RlIGZvciB0aGlzIHNpbmNlIGl0IGlzIGEgV2FybmluZ1xuICAgICAgLy8gZXNsaW50LWRpc2FibGUtbmV4dC1saW5lIG5vLXJlc3RyaWN0ZWQtc3ludGF4XG4gICAgICB2YXIgdyA9IG5ldyBFcnJvcignUG9zc2libGUgRXZlbnRFbWl0dGVyIG1lbW9yeSBsZWFrIGRldGVjdGVkLiAnICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgZXhpc3RpbmcubGVuZ3RoICsgJyAnICsgU3RyaW5nKHR5cGUpICsgJyBsaXN0ZW5lcnMgJyArXG4gICAgICAgICAgICAgICAgICAgICAgICAgICdhZGRlZC4gVXNlIGVtaXR0ZXIuc2V0TWF4TGlzdGVuZXJzKCkgdG8gJyArXG4gICAgICAgICAgICAgICAgICAgICAgICAgICdpbmNyZWFzZSBsaW1pdCcpO1xuICAgICAgdy5uYW1lID0gJ01heExpc3RlbmVyc0V4Y2VlZGVkV2FybmluZyc7XG4gICAgICB3LmVtaXR0ZXIgPSB0YXJnZXQ7XG4gICAgICB3LnR5cGUgPSB0eXBlO1xuICAgICAgdy5jb3VudCA9IGV4aXN0aW5nLmxlbmd0aDtcbiAgICAgIFByb2Nlc3NFbWl0V2FybmluZyh3KTtcbiAgICB9XG4gIH1cblxuICByZXR1cm4gdGFyZ2V0O1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmFkZExpc3RlbmVyID0gZnVuY3Rpb24gYWRkTGlzdGVuZXIodHlwZSwgbGlzdGVuZXIpIHtcbiAgcmV0dXJuIF9hZGRMaXN0ZW5lcih0aGlzLCB0eXBlLCBsaXN0ZW5lciwgZmFsc2UpO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5vbiA9IEV2ZW50RW1pdHRlci5wcm90b3R5cGUuYWRkTGlzdGVuZXI7XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucHJlcGVuZExpc3RlbmVyID1cbiAgICBmdW5jdGlvbiBwcmVwZW5kTGlzdGVuZXIodHlwZSwgbGlzdGVuZXIpIHtcbiAgICAgIHJldHVybiBfYWRkTGlzdGVuZXIodGhpcywgdHlwZSwgbGlzdGVuZXIsIHRydWUpO1xuICAgIH07XG5cbmZ1bmN0aW9uIG9uY2VXcmFwcGVyKCkge1xuICB2YXIgYXJncyA9IFtdO1xuICBmb3IgKHZhciBpID0gMDsgaSA8IGFyZ3VtZW50cy5sZW5ndGg7IGkrKykgYXJncy5wdXNoKGFyZ3VtZW50c1tpXSk7XG4gIGlmICghdGhpcy5maXJlZCkge1xuICAgIHRoaXMudGFyZ2V0LnJlbW92ZUxpc3RlbmVyKHRoaXMudHlwZSwgdGhpcy53cmFwRm4pO1xuICAgIHRoaXMuZmlyZWQgPSB0cnVlO1xuICAgIFJlZmxlY3RBcHBseSh0aGlzLmxpc3RlbmVyLCB0aGlzLnRhcmdldCwgYXJncyk7XG4gIH1cbn1cblxuZnVuY3Rpb24gX29uY2VXcmFwKHRhcmdldCwgdHlwZSwgbGlzdGVuZXIpIHtcbiAgdmFyIHN0YXRlID0geyBmaXJlZDogZmFsc2UsIHdyYXBGbjogdW5kZWZpbmVkLCB0YXJnZXQ6IHRhcmdldCwgdHlwZTogdHlwZSwgbGlzdGVuZXI6IGxpc3RlbmVyIH07XG4gIHZhciB3cmFwcGVkID0gb25jZVdyYXBwZXIuYmluZChzdGF0ZSk7XG4gIHdyYXBwZWQubGlzdGVuZXIgPSBsaXN0ZW5lcjtcbiAgc3RhdGUud3JhcEZuID0gd3JhcHBlZDtcbiAgcmV0dXJuIHdyYXBwZWQ7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUub25jZSA9IGZ1bmN0aW9uIG9uY2UodHlwZSwgbGlzdGVuZXIpIHtcbiAgaWYgKHR5cGVvZiBsaXN0ZW5lciAhPT0gJ2Z1bmN0aW9uJykge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ1RoZSBcImxpc3RlbmVyXCIgYXJndW1lbnQgbXVzdCBiZSBvZiB0eXBlIEZ1bmN0aW9uLiBSZWNlaXZlZCB0eXBlICcgKyB0eXBlb2YgbGlzdGVuZXIpO1xuICB9XG4gIHRoaXMub24odHlwZSwgX29uY2VXcmFwKHRoaXMsIHR5cGUsIGxpc3RlbmVyKSk7XG4gIHJldHVybiB0aGlzO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5wcmVwZW5kT25jZUxpc3RlbmVyID1cbiAgICBmdW5jdGlvbiBwcmVwZW5kT25jZUxpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gICAgICBpZiAodHlwZW9mIGxpc3RlbmVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ1RoZSBcImxpc3RlbmVyXCIgYXJndW1lbnQgbXVzdCBiZSBvZiB0eXBlIEZ1bmN0aW9uLiBSZWNlaXZlZCB0eXBlICcgKyB0eXBlb2YgbGlzdGVuZXIpO1xuICAgICAgfVxuICAgICAgdGhpcy5wcmVwZW5kTGlzdGVuZXIodHlwZSwgX29uY2VXcmFwKHRoaXMsIHR5cGUsIGxpc3RlbmVyKSk7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9O1xuXG4vLyBFbWl0cyBhICdyZW1vdmVMaXN0ZW5lcicgZXZlbnQgaWYgYW5kIG9ubHkgaWYgdGhlIGxpc3RlbmVyIHdhcyByZW1vdmVkLlxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yZW1vdmVMaXN0ZW5lciA9XG4gICAgZnVuY3Rpb24gcmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXIpIHtcbiAgICAgIHZhciBsaXN0LCBldmVudHMsIHBvc2l0aW9uLCBpLCBvcmlnaW5hbExpc3RlbmVyO1xuXG4gICAgICBpZiAodHlwZW9mIGxpc3RlbmVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ1RoZSBcImxpc3RlbmVyXCIgYXJndW1lbnQgbXVzdCBiZSBvZiB0eXBlIEZ1bmN0aW9uLiBSZWNlaXZlZCB0eXBlICcgKyB0eXBlb2YgbGlzdGVuZXIpO1xuICAgICAgfVxuXG4gICAgICBldmVudHMgPSB0aGlzLl9ldmVudHM7XG4gICAgICBpZiAoZXZlbnRzID09PSB1bmRlZmluZWQpXG4gICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICBsaXN0ID0gZXZlbnRzW3R5cGVdO1xuICAgICAgaWYgKGxpc3QgPT09IHVuZGVmaW5lZClcbiAgICAgICAgcmV0dXJuIHRoaXM7XG5cbiAgICAgIGlmIChsaXN0ID09PSBsaXN0ZW5lciB8fCBsaXN0Lmxpc3RlbmVyID09PSBsaXN0ZW5lcikge1xuICAgICAgICBpZiAoLS10aGlzLl9ldmVudHNDb3VudCA9PT0gMClcbiAgICAgICAgICB0aGlzLl9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgICAgICBlbHNlIHtcbiAgICAgICAgICBkZWxldGUgZXZlbnRzW3R5cGVdO1xuICAgICAgICAgIGlmIChldmVudHMucmVtb3ZlTGlzdGVuZXIpXG4gICAgICAgICAgICB0aGlzLmVtaXQoJ3JlbW92ZUxpc3RlbmVyJywgdHlwZSwgbGlzdC5saXN0ZW5lciB8fCBsaXN0ZW5lcik7XG4gICAgICAgIH1cbiAgICAgIH0gZWxzZSBpZiAodHlwZW9mIGxpc3QgIT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgcG9zaXRpb24gPSAtMTtcblxuICAgICAgICBmb3IgKGkgPSBsaXN0Lmxlbmd0aCAtIDE7IGkgPj0gMDsgaS0tKSB7XG4gICAgICAgICAgaWYgKGxpc3RbaV0gPT09IGxpc3RlbmVyIHx8IGxpc3RbaV0ubGlzdGVuZXIgPT09IGxpc3RlbmVyKSB7XG4gICAgICAgICAgICBvcmlnaW5hbExpc3RlbmVyID0gbGlzdFtpXS5saXN0ZW5lcjtcbiAgICAgICAgICAgIHBvc2l0aW9uID0gaTtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChwb3NpdGlvbiA8IDApXG4gICAgICAgICAgcmV0dXJuIHRoaXM7XG5cbiAgICAgICAgaWYgKHBvc2l0aW9uID09PSAwKVxuICAgICAgICAgIGxpc3Quc2hpZnQoKTtcbiAgICAgICAgZWxzZSB7XG4gICAgICAgICAgc3BsaWNlT25lKGxpc3QsIHBvc2l0aW9uKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChsaXN0Lmxlbmd0aCA9PT0gMSlcbiAgICAgICAgICBldmVudHNbdHlwZV0gPSBsaXN0WzBdO1xuXG4gICAgICAgIGlmIChldmVudHMucmVtb3ZlTGlzdGVuZXIgIT09IHVuZGVmaW5lZClcbiAgICAgICAgICB0aGlzLmVtaXQoJ3JlbW92ZUxpc3RlbmVyJywgdHlwZSwgb3JpZ2luYWxMaXN0ZW5lciB8fCBsaXN0ZW5lcik7XG4gICAgICB9XG5cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUub2ZmID0gRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yZW1vdmVMaXN0ZW5lcjtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yZW1vdmVBbGxMaXN0ZW5lcnMgPVxuICAgIGZ1bmN0aW9uIHJlbW92ZUFsbExpc3RlbmVycyh0eXBlKSB7XG4gICAgICB2YXIgbGlzdGVuZXJzLCBldmVudHMsIGk7XG5cbiAgICAgIGV2ZW50cyA9IHRoaXMuX2V2ZW50cztcbiAgICAgIGlmIChldmVudHMgPT09IHVuZGVmaW5lZClcbiAgICAgICAgcmV0dXJuIHRoaXM7XG5cbiAgICAgIC8vIG5vdCBsaXN0ZW5pbmcgZm9yIHJlbW92ZUxpc3RlbmVyLCBubyBuZWVkIHRvIGVtaXRcbiAgICAgIGlmIChldmVudHMucmVtb3ZlTGlzdGVuZXIgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICBpZiAoYXJndW1lbnRzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgICAgICAgdGhpcy5fZXZlbnRzQ291bnQgPSAwO1xuICAgICAgICB9IGVsc2UgaWYgKGV2ZW50c1t0eXBlXSAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgaWYgKC0tdGhpcy5fZXZlbnRzQ291bnQgPT09IDApXG4gICAgICAgICAgICB0aGlzLl9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgIGRlbGV0ZSBldmVudHNbdHlwZV07XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICB9XG5cbiAgICAgIC8vIGVtaXQgcmVtb3ZlTGlzdGVuZXIgZm9yIGFsbCBsaXN0ZW5lcnMgb24gYWxsIGV2ZW50c1xuICAgICAgaWYgKGFyZ3VtZW50cy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgdmFyIGtleXMgPSBPYmplY3Qua2V5cyhldmVudHMpO1xuICAgICAgICB2YXIga2V5O1xuICAgICAgICBmb3IgKGkgPSAwOyBpIDwga2V5cy5sZW5ndGg7ICsraSkge1xuICAgICAgICAgIGtleSA9IGtleXNbaV07XG4gICAgICAgICAgaWYgKGtleSA9PT0gJ3JlbW92ZUxpc3RlbmVyJykgY29udGludWU7XG4gICAgICAgICAgdGhpcy5yZW1vdmVBbGxMaXN0ZW5lcnMoa2V5KTtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLnJlbW92ZUFsbExpc3RlbmVycygncmVtb3ZlTGlzdGVuZXInKTtcbiAgICAgICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICAgICAgdGhpcy5fZXZlbnRzQ291bnQgPSAwO1xuICAgICAgICByZXR1cm4gdGhpcztcbiAgICAgIH1cblxuICAgICAgbGlzdGVuZXJzID0gZXZlbnRzW3R5cGVdO1xuXG4gICAgICBpZiAodHlwZW9mIGxpc3RlbmVycyA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICB0aGlzLnJlbW92ZUxpc3RlbmVyKHR5cGUsIGxpc3RlbmVycyk7XG4gICAgICB9IGVsc2UgaWYgKGxpc3RlbmVycyAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgIC8vIExJRk8gb3JkZXJcbiAgICAgICAgZm9yIChpID0gbGlzdGVuZXJzLmxlbmd0aCAtIDE7IGkgPj0gMDsgaS0tKSB7XG4gICAgICAgICAgdGhpcy5yZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcnNbaV0pO1xuICAgICAgICB9XG4gICAgICB9XG5cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH07XG5cbmZ1bmN0aW9uIF9saXN0ZW5lcnModGFyZ2V0LCB0eXBlLCB1bndyYXApIHtcbiAgdmFyIGV2ZW50cyA9IHRhcmdldC5fZXZlbnRzO1xuXG4gIGlmIChldmVudHMgPT09IHVuZGVmaW5lZClcbiAgICByZXR1cm4gW107XG5cbiAgdmFyIGV2bGlzdGVuZXIgPSBldmVudHNbdHlwZV07XG4gIGlmIChldmxpc3RlbmVyID09PSB1bmRlZmluZWQpXG4gICAgcmV0dXJuIFtdO1xuXG4gIGlmICh0eXBlb2YgZXZsaXN0ZW5lciA9PT0gJ2Z1bmN0aW9uJylcbiAgICByZXR1cm4gdW53cmFwID8gW2V2bGlzdGVuZXIubGlzdGVuZXIgfHwgZXZsaXN0ZW5lcl0gOiBbZXZsaXN0ZW5lcl07XG5cbiAgcmV0dXJuIHVud3JhcCA/XG4gICAgdW53cmFwTGlzdGVuZXJzKGV2bGlzdGVuZXIpIDogYXJyYXlDbG9uZShldmxpc3RlbmVyLCBldmxpc3RlbmVyLmxlbmd0aCk7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJzID0gZnVuY3Rpb24gbGlzdGVuZXJzKHR5cGUpIHtcbiAgcmV0dXJuIF9saXN0ZW5lcnModGhpcywgdHlwZSwgdHJ1ZSk7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnJhd0xpc3RlbmVycyA9IGZ1bmN0aW9uIHJhd0xpc3RlbmVycyh0eXBlKSB7XG4gIHJldHVybiBfbGlzdGVuZXJzKHRoaXMsIHR5cGUsIGZhbHNlKTtcbn07XG5cbkV2ZW50RW1pdHRlci5saXN0ZW5lckNvdW50ID0gZnVuY3Rpb24oZW1pdHRlciwgdHlwZSkge1xuICBpZiAodHlwZW9mIGVtaXR0ZXIubGlzdGVuZXJDb3VudCA9PT0gJ2Z1bmN0aW9uJykge1xuICAgIHJldHVybiBlbWl0dGVyLmxpc3RlbmVyQ291bnQodHlwZSk7XG4gIH0gZWxzZSB7XG4gICAgcmV0dXJuIGxpc3RlbmVyQ291bnQuY2FsbChlbWl0dGVyLCB0eXBlKTtcbiAgfVxufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5saXN0ZW5lckNvdW50ID0gbGlzdGVuZXJDb3VudDtcbmZ1bmN0aW9uIGxpc3RlbmVyQ291bnQodHlwZSkge1xuICB2YXIgZXZlbnRzID0gdGhpcy5fZXZlbnRzO1xuXG4gIGlmIChldmVudHMgIT09IHVuZGVmaW5lZCkge1xuICAgIHZhciBldmxpc3RlbmVyID0gZXZlbnRzW3R5cGVdO1xuXG4gICAgaWYgKHR5cGVvZiBldmxpc3RlbmVyID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICByZXR1cm4gMTtcbiAgICB9IGVsc2UgaWYgKGV2bGlzdGVuZXIgIT09IHVuZGVmaW5lZCkge1xuICAgICAgcmV0dXJuIGV2bGlzdGVuZXIubGVuZ3RoO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiAwO1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmV2ZW50TmFtZXMgPSBmdW5jdGlvbiBldmVudE5hbWVzKCkge1xuICByZXR1cm4gdGhpcy5fZXZlbnRzQ291bnQgPiAwID8gUmVmbGVjdE93bktleXModGhpcy5fZXZlbnRzKSA6IFtdO1xufTtcblxuZnVuY3Rpb24gYXJyYXlDbG9uZShhcnIsIG4pIHtcbiAgdmFyIGNvcHkgPSBuZXcgQXJyYXkobik7XG4gIGZvciAodmFyIGkgPSAwOyBpIDwgbjsgKytpKVxuICAgIGNvcHlbaV0gPSBhcnJbaV07XG4gIHJldHVybiBjb3B5O1xufVxuXG5mdW5jdGlvbiBzcGxpY2VPbmUobGlzdCwgaW5kZXgpIHtcbiAgZm9yICg7IGluZGV4ICsgMSA8IGxpc3QubGVuZ3RoOyBpbmRleCsrKVxuICAgIGxpc3RbaW5kZXhdID0gbGlzdFtpbmRleCArIDFdO1xuICBsaXN0LnBvcCgpO1xufVxuXG5mdW5jdGlvbiB1bndyYXBMaXN0ZW5lcnMoYXJyKSB7XG4gIHZhciByZXQgPSBuZXcgQXJyYXkoYXJyLmxlbmd0aCk7XG4gIGZvciAodmFyIGkgPSAwOyBpIDwgcmV0Lmxlbmd0aDsgKytpKSB7XG4gICAgcmV0W2ldID0gYXJyW2ldLmxpc3RlbmVyIHx8IGFycltpXTtcbiAgfVxuICByZXR1cm4gcmV0O1xufVxuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2V2ZW50cy9ldmVudHMuanNcbi8vIG1vZHVsZSBpZCA9IDUzXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDcgMTAgMTEgMTIgMTUgMTYgMTcgMTggMjEgMjMgMjQgMjUgMzQgNDEgNDMgNDYgNDggNTAgNTEiLCJtb2R1bGUuZXhwb3J0cyA9IHt9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXRlcmF0b3JzLmpzXG4vLyBtb2R1bGUgaWQgPSA1NFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IDE0IiwidmFyIGhhcyAgICAgICAgICA9IHJlcXVpcmUoJy4vX2hhcycpXG4gICwgdG9JT2JqZWN0ICAgID0gcmVxdWlyZSgnLi9fdG8taW9iamVjdCcpXG4gICwgYXJyYXlJbmRleE9mID0gcmVxdWlyZSgnLi9fYXJyYXktaW5jbHVkZXMnKShmYWxzZSlcbiAgLCBJRV9QUk9UTyAgICAgPSByZXF1aXJlKCcuL19zaGFyZWQta2V5JykoJ0lFX1BST1RPJyk7XG5cbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24ob2JqZWN0LCBuYW1lcyl7XG4gIHZhciBPICAgICAgPSB0b0lPYmplY3Qob2JqZWN0KVxuICAgICwgaSAgICAgID0gMFxuICAgICwgcmVzdWx0ID0gW11cbiAgICAsIGtleTtcbiAgZm9yKGtleSBpbiBPKWlmKGtleSAhPSBJRV9QUk9UTyloYXMoTywga2V5KSAmJiByZXN1bHQucHVzaChrZXkpO1xuICAvLyBEb24ndCBlbnVtIGJ1ZyAmIGhpZGRlbiBrZXlzXG4gIHdoaWxlKG5hbWVzLmxlbmd0aCA+IGkpaWYoaGFzKE8sIGtleSA9IG5hbWVzW2krK10pKXtcbiAgICB+YXJyYXlJbmRleE9mKHJlc3VsdCwga2V5KSB8fCByZXN1bHQucHVzaChrZXkpO1xuICB9XG4gIHJldHVybiByZXN1bHQ7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWtleXMtaW50ZXJuYWwuanNcbi8vIG1vZHVsZSBpZCA9IDU1XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIi8vIDcuMS4xNSBUb0xlbmd0aFxudmFyIHRvSW50ZWdlciA9IHJlcXVpcmUoJy4vX3RvLWludGVnZXInKVxuICAsIG1pbiAgICAgICA9IE1hdGgubWluO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIHJldHVybiBpdCA+IDAgPyBtaW4odG9JbnRlZ2VyKGl0KSwgMHgxZmZmZmZmZmZmZmZmZikgOiAwOyAvLyBwb3coMiwgNTMpIC0gMSA9PSA5MDA3MTk5MjU0NzQwOTkxXG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8tbGVuZ3RoLmpzXG4vLyBtb2R1bGUgaWQgPSA1NlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJleHBvcnRzLmYgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdvcHMuanNcbi8vIG1vZHVsZSBpZCA9IDU3XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNSAxNiAxOCIsIi8vIGZhbHNlIC0+IEFycmF5I2luZGV4T2Zcbi8vIHRydWUgIC0+IEFycmF5I2luY2x1ZGVzXG52YXIgdG9JT2JqZWN0ID0gcmVxdWlyZSgnLi9fdG8taW9iamVjdCcpXG4gICwgdG9MZW5ndGggID0gcmVxdWlyZSgnLi9fdG8tbGVuZ3RoJylcbiAgLCB0b0luZGV4ICAgPSByZXF1aXJlKCcuL190by1pbmRleCcpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihJU19JTkNMVURFUyl7XG4gIHJldHVybiBmdW5jdGlvbigkdGhpcywgZWwsIGZyb21JbmRleCl7XG4gICAgdmFyIE8gICAgICA9IHRvSU9iamVjdCgkdGhpcylcbiAgICAgICwgbGVuZ3RoID0gdG9MZW5ndGgoTy5sZW5ndGgpXG4gICAgICAsIGluZGV4ICA9IHRvSW5kZXgoZnJvbUluZGV4LCBsZW5ndGgpXG4gICAgICAsIHZhbHVlO1xuICAgIC8vIEFycmF5I2luY2x1ZGVzIHVzZXMgU2FtZVZhbHVlWmVybyBlcXVhbGl0eSBhbGdvcml0aG1cbiAgICBpZihJU19JTkNMVURFUyAmJiBlbCAhPSBlbCl3aGlsZShsZW5ndGggPiBpbmRleCl7XG4gICAgICB2YWx1ZSA9IE9baW5kZXgrK107XG4gICAgICBpZih2YWx1ZSAhPSB2YWx1ZSlyZXR1cm4gdHJ1ZTtcbiAgICAvLyBBcnJheSN0b0luZGV4IGlnbm9yZXMgaG9sZXMsIEFycmF5I2luY2x1ZGVzIC0gbm90XG4gICAgfSBlbHNlIGZvcig7bGVuZ3RoID4gaW5kZXg7IGluZGV4KyspaWYoSVNfSU5DTFVERVMgfHwgaW5kZXggaW4gTyl7XG4gICAgICBpZihPW2luZGV4XSA9PT0gZWwpcmV0dXJuIElTX0lOQ0xVREVTIHx8IGluZGV4IHx8IDA7XG4gICAgfSByZXR1cm4gIUlTX0lOQ0xVREVTICYmIC0xO1xuICB9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2FycmF5LWluY2x1ZGVzLmpzXG4vLyBtb2R1bGUgaWQgPSA1OFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJ2YXIgdG9JbnRlZ2VyID0gcmVxdWlyZSgnLi9fdG8taW50ZWdlcicpXG4gICwgbWF4ICAgICAgID0gTWF0aC5tYXhcbiAgLCBtaW4gICAgICAgPSBNYXRoLm1pbjtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaW5kZXgsIGxlbmd0aCl7XG4gIGluZGV4ID0gdG9JbnRlZ2VyKGluZGV4KTtcbiAgcmV0dXJuIGluZGV4IDwgMCA/IG1heChpbmRleCArIGxlbmd0aCwgMCkgOiBtaW4oaW5kZXgsIGxlbmd0aCk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8taW5kZXguanNcbi8vIG1vZHVsZSBpZCA9IDU5XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsInZhciBkZWYgPSByZXF1aXJlKCcuL19vYmplY3QtZHAnKS5mXG4gICwgaGFzID0gcmVxdWlyZSgnLi9faGFzJylcbiAgLCBUQUcgPSByZXF1aXJlKCcuL193a3MnKSgndG9TdHJpbmdUYWcnKTtcblxubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCwgdGFnLCBzdGF0KXtcbiAgaWYoaXQgJiYgIWhhcyhpdCA9IHN0YXQgPyBpdCA6IGl0LnByb3RvdHlwZSwgVEFHKSlkZWYoaXQsIFRBRywge2NvbmZpZ3VyYWJsZTogdHJ1ZSwgdmFsdWU6IHRhZ30pO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NldC10by1zdHJpbmctdGFnLmpzXG4vLyBtb2R1bGUgaWQgPSA2MlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IDE0IiwibW9kdWxlLmV4cG9ydHMgPSB0cnVlO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fbGlicmFyeS5qc1xuLy8gbW9kdWxlIGlkID0gNjNcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSAxNCIsIid1c2Ugc3RyaWN0JztcbnZhciAkYXQgID0gcmVxdWlyZSgnLi9fc3RyaW5nLWF0JykodHJ1ZSk7XG5cbi8vIDIxLjEuMy4yNyBTdHJpbmcucHJvdG90eXBlW0BAaXRlcmF0b3JdKClcbnJlcXVpcmUoJy4vX2l0ZXItZGVmaW5lJykoU3RyaW5nLCAnU3RyaW5nJywgZnVuY3Rpb24oaXRlcmF0ZWQpe1xuICB0aGlzLl90ID0gU3RyaW5nKGl0ZXJhdGVkKTsgLy8gdGFyZ2V0XG4gIHRoaXMuX2kgPSAwOyAgICAgICAgICAgICAgICAvLyBuZXh0IGluZGV4XG4vLyAyMS4xLjUuMi4xICVTdHJpbmdJdGVyYXRvclByb3RvdHlwZSUubmV4dCgpXG59LCBmdW5jdGlvbigpe1xuICB2YXIgTyAgICAgPSB0aGlzLl90XG4gICAgLCBpbmRleCA9IHRoaXMuX2lcbiAgICAsIHBvaW50O1xuICBpZihpbmRleCA+PSBPLmxlbmd0aClyZXR1cm4ge3ZhbHVlOiB1bmRlZmluZWQsIGRvbmU6IHRydWV9O1xuICBwb2ludCA9ICRhdChPLCBpbmRleCk7XG4gIHRoaXMuX2kgKz0gcG9pbnQubGVuZ3RoO1xuICByZXR1cm4ge3ZhbHVlOiBwb2ludCwgZG9uZTogZmFsc2V9O1xufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5zdHJpbmcuaXRlcmF0b3IuanNcbi8vIG1vZHVsZSBpZCA9IDY0XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkgMTQiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCBSb3V0aW5nIGZyb20gJ2Zvcy1yb3V0aW5nJztcbmltcG9ydCByb3V0ZXMgZnJvbSAnQGpzL2Zvc19qc19yb3V0ZXMuanNvbic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBXcmFwcyBGT1NKc1JvdXRpbmdidW5kbGUgd2l0aCBleHBvc2VkIHJvdXRlcy5cbiAqIFRvIGV4cG9zZSByb3V0ZSBhZGQgb3B0aW9uIGBleHBvc2U6IHRydWVgIGluIC55bWwgcm91dGluZyBjb25maWdcbiAqXG4gKiBlLmcuXG4gKlxuICogYG15X3JvdXRlXG4gKiAgICBwYXRoOiAvbXktcGF0aFxuICogICAgb3B0aW9uczpcbiAqICAgICAgZXhwb3NlOiB0cnVlXG4gKiBgXG4gKiBBbmQgcnVuIGBiaW4vY29uc29sZSBmb3M6anMtcm91dGluZzpkdW1wIC0tZm9ybWF0PWpzb24gLS10YXJnZXQ9YWRtaW4tZGV2L3RoZW1lcy9uZXctdGhlbWUvanMvZm9zX2pzX3JvdXRlcy5qc29uYFxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBSb3V0ZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICBSb3V0aW5nLnNldERhdGEocm91dGVzKTtcbiAgICBSb3V0aW5nLnNldEJhc2VVcmwoJChkb2N1bWVudCkuZmluZCgnYm9keScpLmRhdGEoJ2Jhc2UtdXJsJykpO1xuXG4gICAgcmV0dXJuIHRoaXM7XG4gIH1cblxuICAvKipcbiAgICogRGVjb3JhdGVkIFwiZ2VuZXJhdGVcIiBtZXRob2QsIHdpdGggcHJlZGVmaW5lZCBzZWN1cml0eSB0b2tlbiBpbiBwYXJhbXNcbiAgICpcbiAgICogQHBhcmFtIHJvdXRlXG4gICAqIEBwYXJhbSBwYXJhbXNcbiAgICpcbiAgICogQHJldHVybnMge1N0cmluZ31cbiAgICovXG4gIGdlbmVyYXRlKHJvdXRlLCBwYXJhbXMgPSB7fSkge1xuICAgIGNvbnN0IHRva2VuaXplZFBhcmFtcyA9IE9iamVjdC5hc3NpZ24ocGFyYW1zLCB7X3Rva2VuOiAkKGRvY3VtZW50KS5maW5kKCdib2R5JykuZGF0YSgndG9rZW4nKX0pO1xuXG4gICAgcmV0dXJuIFJvdXRpbmcuZ2VuZXJhdGUocm91dGUsIHRva2VuaXplZFBhcmFtcyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvcm91dGVyLmpzIiwibW9kdWxlLmV4cG9ydHMgPSB7IFwiZGVmYXVsdFwiOiByZXF1aXJlKFwiY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9rZXlzXCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3Qva2V5cy5qc1xuLy8gbW9kdWxlIGlkID0gNjhcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA4IDkgMTAgMTUgMTkgMjAiLCJ2YXIgZ2xvYmFsICAgICAgICAgPSByZXF1aXJlKCcuL19nbG9iYWwnKVxuICAsIGNvcmUgICAgICAgICAgID0gcmVxdWlyZSgnLi9fY29yZScpXG4gICwgTElCUkFSWSAgICAgICAgPSByZXF1aXJlKCcuL19saWJyYXJ5JylcbiAgLCB3a3NFeHQgICAgICAgICA9IHJlcXVpcmUoJy4vX3drcy1leHQnKVxuICAsIGRlZmluZVByb3BlcnR5ID0gcmVxdWlyZSgnLi9fb2JqZWN0LWRwJykuZjtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24obmFtZSl7XG4gIHZhciAkU3ltYm9sID0gY29yZS5TeW1ib2wgfHwgKGNvcmUuU3ltYm9sID0gTElCUkFSWSA/IHt9IDogZ2xvYmFsLlN5bWJvbCB8fCB7fSk7XG4gIGlmKG5hbWUuY2hhckF0KDApICE9ICdfJyAmJiAhKG5hbWUgaW4gJFN5bWJvbCkpZGVmaW5lUHJvcGVydHkoJFN5bWJvbCwgbmFtZSwge3ZhbHVlOiB3a3NFeHQuZihuYW1lKX0pO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3drcy1kZWZpbmUuanNcbi8vIG1vZHVsZSBpZCA9IDY5XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkiLCJleHBvcnRzLmYgPSByZXF1aXJlKCcuL193a3MnKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3drcy1leHQuanNcbi8vIG1vZHVsZSBpZCA9IDcwXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkiLCIvLyAxOS4xLjIuMiAvIDE1LjIuMy41IE9iamVjdC5jcmVhdGUoTyBbLCBQcm9wZXJ0aWVzXSlcbnZhciBhbk9iamVjdCAgICA9IHJlcXVpcmUoJy4vX2FuLW9iamVjdCcpXG4gICwgZFBzICAgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZHBzJylcbiAgLCBlbnVtQnVnS2V5cyA9IHJlcXVpcmUoJy4vX2VudW0tYnVnLWtleXMnKVxuICAsIElFX1BST1RPICAgID0gcmVxdWlyZSgnLi9fc2hhcmVkLWtleScpKCdJRV9QUk9UTycpXG4gICwgRW1wdHkgICAgICAgPSBmdW5jdGlvbigpeyAvKiBlbXB0eSAqLyB9XG4gICwgUFJPVE9UWVBFICAgPSAncHJvdG90eXBlJztcblxuLy8gQ3JlYXRlIG9iamVjdCB3aXRoIGZha2UgYG51bGxgIHByb3RvdHlwZTogdXNlIGlmcmFtZSBPYmplY3Qgd2l0aCBjbGVhcmVkIHByb3RvdHlwZVxudmFyIGNyZWF0ZURpY3QgPSBmdW5jdGlvbigpe1xuICAvLyBUaHJhc2gsIHdhc3RlIGFuZCBzb2RvbXk6IElFIEdDIGJ1Z1xuICB2YXIgaWZyYW1lID0gcmVxdWlyZSgnLi9fZG9tLWNyZWF0ZScpKCdpZnJhbWUnKVxuICAgICwgaSAgICAgID0gZW51bUJ1Z0tleXMubGVuZ3RoXG4gICAgLCBsdCAgICAgPSAnPCdcbiAgICAsIGd0ICAgICA9ICc+J1xuICAgICwgaWZyYW1lRG9jdW1lbnQ7XG4gIGlmcmFtZS5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICByZXF1aXJlKCcuL19odG1sJykuYXBwZW5kQ2hpbGQoaWZyYW1lKTtcbiAgaWZyYW1lLnNyYyA9ICdqYXZhc2NyaXB0Oic7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tc2NyaXB0LXVybFxuICAvLyBjcmVhdGVEaWN0ID0gaWZyYW1lLmNvbnRlbnRXaW5kb3cuT2JqZWN0O1xuICAvLyBodG1sLnJlbW92ZUNoaWxkKGlmcmFtZSk7XG4gIGlmcmFtZURvY3VtZW50ID0gaWZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQ7XG4gIGlmcmFtZURvY3VtZW50Lm9wZW4oKTtcbiAgaWZyYW1lRG9jdW1lbnQud3JpdGUobHQgKyAnc2NyaXB0JyArIGd0ICsgJ2RvY3VtZW50LkY9T2JqZWN0JyArIGx0ICsgJy9zY3JpcHQnICsgZ3QpO1xuICBpZnJhbWVEb2N1bWVudC5jbG9zZSgpO1xuICBjcmVhdGVEaWN0ID0gaWZyYW1lRG9jdW1lbnQuRjtcbiAgd2hpbGUoaS0tKWRlbGV0ZSBjcmVhdGVEaWN0W1BST1RPVFlQRV1bZW51bUJ1Z0tleXNbaV1dO1xuICByZXR1cm4gY3JlYXRlRGljdCgpO1xufTtcblxubW9kdWxlLmV4cG9ydHMgPSBPYmplY3QuY3JlYXRlIHx8IGZ1bmN0aW9uIGNyZWF0ZShPLCBQcm9wZXJ0aWVzKXtcbiAgdmFyIHJlc3VsdDtcbiAgaWYoTyAhPT0gbnVsbCl7XG4gICAgRW1wdHlbUFJPVE9UWVBFXSA9IGFuT2JqZWN0KE8pO1xuICAgIHJlc3VsdCA9IG5ldyBFbXB0eTtcbiAgICBFbXB0eVtQUk9UT1RZUEVdID0gbnVsbDtcbiAgICAvLyBhZGQgXCJfX3Byb3RvX19cIiBmb3IgT2JqZWN0LmdldFByb3RvdHlwZU9mIHBvbHlmaWxsXG4gICAgcmVzdWx0W0lFX1BST1RPXSA9IE87XG4gIH0gZWxzZSByZXN1bHQgPSBjcmVhdGVEaWN0KCk7XG4gIHJldHVybiBQcm9wZXJ0aWVzID09PSB1bmRlZmluZWQgPyByZXN1bHQgOiBkUHMocmVzdWx0LCBQcm9wZXJ0aWVzKTtcbn07XG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1jcmVhdGUuanNcbi8vIG1vZHVsZSBpZCA9IDcxXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkgMTQiLCJyZXF1aXJlKCcuL2VzNi5hcnJheS5pdGVyYXRvcicpO1xudmFyIGdsb2JhbCAgICAgICAgPSByZXF1aXJlKCcuL19nbG9iYWwnKVxuICAsIGhpZGUgICAgICAgICAgPSByZXF1aXJlKCcuL19oaWRlJylcbiAgLCBJdGVyYXRvcnMgICAgID0gcmVxdWlyZSgnLi9faXRlcmF0b3JzJylcbiAgLCBUT19TVFJJTkdfVEFHID0gcmVxdWlyZSgnLi9fd2tzJykoJ3RvU3RyaW5nVGFnJyk7XG5cbmZvcih2YXIgY29sbGVjdGlvbnMgPSBbJ05vZGVMaXN0JywgJ0RPTVRva2VuTGlzdCcsICdNZWRpYUxpc3QnLCAnU3R5bGVTaGVldExpc3QnLCAnQ1NTUnVsZUxpc3QnXSwgaSA9IDA7IGkgPCA1OyBpKyspe1xuICB2YXIgTkFNRSAgICAgICA9IGNvbGxlY3Rpb25zW2ldXG4gICAgLCBDb2xsZWN0aW9uID0gZ2xvYmFsW05BTUVdXG4gICAgLCBwcm90byAgICAgID0gQ29sbGVjdGlvbiAmJiBDb2xsZWN0aW9uLnByb3RvdHlwZTtcbiAgaWYocHJvdG8gJiYgIXByb3RvW1RPX1NUUklOR19UQUddKWhpZGUocHJvdG8sIFRPX1NUUklOR19UQUcsIE5BTUUpO1xuICBJdGVyYXRvcnNbTkFNRV0gPSBJdGVyYXRvcnMuQXJyYXk7XG59XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL3dlYi5kb20uaXRlcmFibGUuanNcbi8vIG1vZHVsZSBpZCA9IDc0XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkgMTQiLCIndXNlIHN0cmljdCc7XG52YXIgTElCUkFSWSAgICAgICAgPSByZXF1aXJlKCcuL19saWJyYXJ5JylcbiAgLCAkZXhwb3J0ICAgICAgICA9IHJlcXVpcmUoJy4vX2V4cG9ydCcpXG4gICwgcmVkZWZpbmUgICAgICAgPSByZXF1aXJlKCcuL19yZWRlZmluZScpXG4gICwgaGlkZSAgICAgICAgICAgPSByZXF1aXJlKCcuL19oaWRlJylcbiAgLCBoYXMgICAgICAgICAgICA9IHJlcXVpcmUoJy4vX2hhcycpXG4gICwgSXRlcmF0b3JzICAgICAgPSByZXF1aXJlKCcuL19pdGVyYXRvcnMnKVxuICAsICRpdGVyQ3JlYXRlICAgID0gcmVxdWlyZSgnLi9faXRlci1jcmVhdGUnKVxuICAsIHNldFRvU3RyaW5nVGFnID0gcmVxdWlyZSgnLi9fc2V0LXRvLXN0cmluZy10YWcnKVxuICAsIGdldFByb3RvdHlwZU9mID0gcmVxdWlyZSgnLi9fb2JqZWN0LWdwbycpXG4gICwgSVRFUkFUT1IgICAgICAgPSByZXF1aXJlKCcuL193a3MnKSgnaXRlcmF0b3InKVxuICAsIEJVR0dZICAgICAgICAgID0gIShbXS5rZXlzICYmICduZXh0JyBpbiBbXS5rZXlzKCkpIC8vIFNhZmFyaSBoYXMgYnVnZ3kgaXRlcmF0b3JzIHcvbyBgbmV4dGBcbiAgLCBGRl9JVEVSQVRPUiAgICA9ICdAQGl0ZXJhdG9yJ1xuICAsIEtFWVMgICAgICAgICAgID0gJ2tleXMnXG4gICwgVkFMVUVTICAgICAgICAgPSAndmFsdWVzJztcblxudmFyIHJldHVyblRoaXMgPSBmdW5jdGlvbigpeyByZXR1cm4gdGhpczsgfTtcblxubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihCYXNlLCBOQU1FLCBDb25zdHJ1Y3RvciwgbmV4dCwgREVGQVVMVCwgSVNfU0VULCBGT1JDRUQpe1xuICAkaXRlckNyZWF0ZShDb25zdHJ1Y3RvciwgTkFNRSwgbmV4dCk7XG4gIHZhciBnZXRNZXRob2QgPSBmdW5jdGlvbihraW5kKXtcbiAgICBpZighQlVHR1kgJiYga2luZCBpbiBwcm90bylyZXR1cm4gcHJvdG9ba2luZF07XG4gICAgc3dpdGNoKGtpbmQpe1xuICAgICAgY2FzZSBLRVlTOiByZXR1cm4gZnVuY3Rpb24ga2V5cygpeyByZXR1cm4gbmV3IENvbnN0cnVjdG9yKHRoaXMsIGtpbmQpOyB9O1xuICAgICAgY2FzZSBWQUxVRVM6IHJldHVybiBmdW5jdGlvbiB2YWx1ZXMoKXsgcmV0dXJuIG5ldyBDb25zdHJ1Y3Rvcih0aGlzLCBraW5kKTsgfTtcbiAgICB9IHJldHVybiBmdW5jdGlvbiBlbnRyaWVzKCl7IHJldHVybiBuZXcgQ29uc3RydWN0b3IodGhpcywga2luZCk7IH07XG4gIH07XG4gIHZhciBUQUcgICAgICAgID0gTkFNRSArICcgSXRlcmF0b3InXG4gICAgLCBERUZfVkFMVUVTID0gREVGQVVMVCA9PSBWQUxVRVNcbiAgICAsIFZBTFVFU19CVUcgPSBmYWxzZVxuICAgICwgcHJvdG8gICAgICA9IEJhc2UucHJvdG90eXBlXG4gICAgLCAkbmF0aXZlICAgID0gcHJvdG9bSVRFUkFUT1JdIHx8IHByb3RvW0ZGX0lURVJBVE9SXSB8fCBERUZBVUxUICYmIHByb3RvW0RFRkFVTFRdXG4gICAgLCAkZGVmYXVsdCAgID0gJG5hdGl2ZSB8fCBnZXRNZXRob2QoREVGQVVMVClcbiAgICAsICRlbnRyaWVzICAgPSBERUZBVUxUID8gIURFRl9WQUxVRVMgPyAkZGVmYXVsdCA6IGdldE1ldGhvZCgnZW50cmllcycpIDogdW5kZWZpbmVkXG4gICAgLCAkYW55TmF0aXZlID0gTkFNRSA9PSAnQXJyYXknID8gcHJvdG8uZW50cmllcyB8fCAkbmF0aXZlIDogJG5hdGl2ZVxuICAgICwgbWV0aG9kcywga2V5LCBJdGVyYXRvclByb3RvdHlwZTtcbiAgLy8gRml4IG5hdGl2ZVxuICBpZigkYW55TmF0aXZlKXtcbiAgICBJdGVyYXRvclByb3RvdHlwZSA9IGdldFByb3RvdHlwZU9mKCRhbnlOYXRpdmUuY2FsbChuZXcgQmFzZSkpO1xuICAgIGlmKEl0ZXJhdG9yUHJvdG90eXBlICE9PSBPYmplY3QucHJvdG90eXBlKXtcbiAgICAgIC8vIFNldCBAQHRvU3RyaW5nVGFnIHRvIG5hdGl2ZSBpdGVyYXRvcnNcbiAgICAgIHNldFRvU3RyaW5nVGFnKEl0ZXJhdG9yUHJvdG90eXBlLCBUQUcsIHRydWUpO1xuICAgICAgLy8gZml4IGZvciBzb21lIG9sZCBlbmdpbmVzXG4gICAgICBpZighTElCUkFSWSAmJiAhaGFzKEl0ZXJhdG9yUHJvdG90eXBlLCBJVEVSQVRPUikpaGlkZShJdGVyYXRvclByb3RvdHlwZSwgSVRFUkFUT1IsIHJldHVyblRoaXMpO1xuICAgIH1cbiAgfVxuICAvLyBmaXggQXJyYXkje3ZhbHVlcywgQEBpdGVyYXRvcn0ubmFtZSBpbiBWOCAvIEZGXG4gIGlmKERFRl9WQUxVRVMgJiYgJG5hdGl2ZSAmJiAkbmF0aXZlLm5hbWUgIT09IFZBTFVFUyl7XG4gICAgVkFMVUVTX0JVRyA9IHRydWU7XG4gICAgJGRlZmF1bHQgPSBmdW5jdGlvbiB2YWx1ZXMoKXsgcmV0dXJuICRuYXRpdmUuY2FsbCh0aGlzKTsgfTtcbiAgfVxuICAvLyBEZWZpbmUgaXRlcmF0b3JcbiAgaWYoKCFMSUJSQVJZIHx8IEZPUkNFRCkgJiYgKEJVR0dZIHx8IFZBTFVFU19CVUcgfHwgIXByb3RvW0lURVJBVE9SXSkpe1xuICAgIGhpZGUocHJvdG8sIElURVJBVE9SLCAkZGVmYXVsdCk7XG4gIH1cbiAgLy8gUGx1ZyBmb3IgbGlicmFyeVxuICBJdGVyYXRvcnNbTkFNRV0gPSAkZGVmYXVsdDtcbiAgSXRlcmF0b3JzW1RBR10gID0gcmV0dXJuVGhpcztcbiAgaWYoREVGQVVMVCl7XG4gICAgbWV0aG9kcyA9IHtcbiAgICAgIHZhbHVlczogIERFRl9WQUxVRVMgPyAkZGVmYXVsdCA6IGdldE1ldGhvZChWQUxVRVMpLFxuICAgICAga2V5czogICAgSVNfU0VUICAgICA/ICRkZWZhdWx0IDogZ2V0TWV0aG9kKEtFWVMpLFxuICAgICAgZW50cmllczogJGVudHJpZXNcbiAgICB9O1xuICAgIGlmKEZPUkNFRClmb3Ioa2V5IGluIG1ldGhvZHMpe1xuICAgICAgaWYoIShrZXkgaW4gcHJvdG8pKXJlZGVmaW5lKHByb3RvLCBrZXksIG1ldGhvZHNba2V5XSk7XG4gICAgfSBlbHNlICRleHBvcnQoJGV4cG9ydC5QICsgJGV4cG9ydC5GICogKEJVR0dZIHx8IFZBTFVFU19CVUcpLCBOQU1FLCBtZXRob2RzKTtcbiAgfVxuICByZXR1cm4gbWV0aG9kcztcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pdGVyLWRlZmluZS5qc1xuLy8gbW9kdWxlIGlkID0gNzZcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSAxNCIsIi8vIG1vc3QgT2JqZWN0IG1ldGhvZHMgYnkgRVM2IHNob3VsZCBhY2NlcHQgcHJpbWl0aXZlc1xudmFyICRleHBvcnQgPSByZXF1aXJlKCcuL19leHBvcnQnKVxuICAsIGNvcmUgICAgPSByZXF1aXJlKCcuL19jb3JlJylcbiAgLCBmYWlscyAgID0gcmVxdWlyZSgnLi9fZmFpbHMnKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oS0VZLCBleGVjKXtcbiAgdmFyIGZuICA9IChjb3JlLk9iamVjdCB8fCB7fSlbS0VZXSB8fCBPYmplY3RbS0VZXVxuICAgICwgZXhwID0ge307XG4gIGV4cFtLRVldID0gZXhlYyhmbik7XG4gICRleHBvcnQoJGV4cG9ydC5TICsgJGV4cG9ydC5GICogZmFpbHMoZnVuY3Rpb24oKXsgZm4oMSk7IH0pLCAnT2JqZWN0JywgZXhwKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3Qtc2FwLmpzXG4vLyBtb2R1bGUgaWQgPSA3N1xuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDggOSAxMCAxNSAxOSAyMCIsIm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi9faGlkZScpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fcmVkZWZpbmUuanNcbi8vIG1vZHVsZSBpZCA9IDgxXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkgMTQiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2Fzc2lnblwiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2Fzc2lnbi5qc1xuLy8gbW9kdWxlIGlkID0gODJcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDcgMTAgMTEgMTIgMTMgMTUgMTYgMTgiLCJyZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5vYmplY3QuYXNzaWduJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4uLy4uL21vZHVsZXMvX2NvcmUnKS5PYmplY3QuYXNzaWduO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2Fzc2lnbi5qc1xuLy8gbW9kdWxlIGlkID0gODNcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDcgMTAgMTEgMTIgMTMgMTUgMTYgMTgiLCJyZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5vYmplY3Qua2V5cycpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuT2JqZWN0LmtleXM7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9mbi9vYmplY3Qva2V5cy5qc1xuLy8gbW9kdWxlIGlkID0gODRcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA4IDkgMTAgMTUgMTkgMjAiLCIndXNlIHN0cmljdCc7XG4vLyAxOS4xLjIuMSBPYmplY3QuYXNzaWduKHRhcmdldCwgc291cmNlLCAuLi4pXG52YXIgZ2V0S2V5cyAgPSByZXF1aXJlKCcuL19vYmplY3Qta2V5cycpXG4gICwgZ09QUyAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZ29wcycpXG4gICwgcElFICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtcGllJylcbiAgLCB0b09iamVjdCA9IHJlcXVpcmUoJy4vX3RvLW9iamVjdCcpXG4gICwgSU9iamVjdCAgPSByZXF1aXJlKCcuL19pb2JqZWN0JylcbiAgLCAkYXNzaWduICA9IE9iamVjdC5hc3NpZ247XG5cbi8vIHNob3VsZCB3b3JrIHdpdGggc3ltYm9scyBhbmQgc2hvdWxkIGhhdmUgZGV0ZXJtaW5pc3RpYyBwcm9wZXJ0eSBvcmRlciAoVjggYnVnKVxubW9kdWxlLmV4cG9ydHMgPSAhJGFzc2lnbiB8fCByZXF1aXJlKCcuL19mYWlscycpKGZ1bmN0aW9uKCl7XG4gIHZhciBBID0ge31cbiAgICAsIEIgPSB7fVxuICAgICwgUyA9IFN5bWJvbCgpXG4gICAgLCBLID0gJ2FiY2RlZmdoaWprbG1ub3BxcnN0JztcbiAgQVtTXSA9IDc7XG4gIEsuc3BsaXQoJycpLmZvckVhY2goZnVuY3Rpb24oayl7IEJba10gPSBrOyB9KTtcbiAgcmV0dXJuICRhc3NpZ24oe30sIEEpW1NdICE9IDcgfHwgT2JqZWN0LmtleXMoJGFzc2lnbih7fSwgQikpLmpvaW4oJycpICE9IEs7XG59KSA/IGZ1bmN0aW9uIGFzc2lnbih0YXJnZXQsIHNvdXJjZSl7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tdW51c2VkLXZhcnNcbiAgdmFyIFQgICAgID0gdG9PYmplY3QodGFyZ2V0KVxuICAgICwgYUxlbiAgPSBhcmd1bWVudHMubGVuZ3RoXG4gICAgLCBpbmRleCA9IDFcbiAgICAsIGdldFN5bWJvbHMgPSBnT1BTLmZcbiAgICAsIGlzRW51bSAgICAgPSBwSUUuZjtcbiAgd2hpbGUoYUxlbiA+IGluZGV4KXtcbiAgICB2YXIgUyAgICAgID0gSU9iamVjdChhcmd1bWVudHNbaW5kZXgrK10pXG4gICAgICAsIGtleXMgICA9IGdldFN5bWJvbHMgPyBnZXRLZXlzKFMpLmNvbmNhdChnZXRTeW1ib2xzKFMpKSA6IGdldEtleXMoUylcbiAgICAgICwgbGVuZ3RoID0ga2V5cy5sZW5ndGhcbiAgICAgICwgaiAgICAgID0gMFxuICAgICAgLCBrZXk7XG4gICAgd2hpbGUobGVuZ3RoID4gailpZihpc0VudW0uY2FsbChTLCBrZXkgPSBrZXlzW2orK10pKVRba2V5XSA9IFNba2V5XTtcbiAgfSByZXR1cm4gVDtcbn0gOiAkYXNzaWduO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWFzc2lnbi5qc1xuLy8gbW9kdWxlIGlkID0gODVcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDcgMTAgMTEgMTIgMTMgMTUgMTYgMTgiLCIvLyAxOS4xLjIuNyAvIDE1LjIuMy40IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKE8pXG52YXIgJGtleXMgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1rZXlzLWludGVybmFsJylcbiAgLCBoaWRkZW5LZXlzID0gcmVxdWlyZSgnLi9fZW51bS1idWcta2V5cycpLmNvbmNhdCgnbGVuZ3RoJywgJ3Byb3RvdHlwZScpO1xuXG5leHBvcnRzLmYgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyB8fCBmdW5jdGlvbiBnZXRPd25Qcm9wZXJ0eU5hbWVzKE8pe1xuICByZXR1cm4gJGtleXMoTywgaGlkZGVuS2V5cyk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdvcG4uanNcbi8vIG1vZHVsZSBpZCA9IDg2XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkiLCIvLyAxOS4xLjMuMSBPYmplY3QuYXNzaWduKHRhcmdldCwgc291cmNlKVxudmFyICRleHBvcnQgPSByZXF1aXJlKCcuL19leHBvcnQnKTtcblxuJGV4cG9ydCgkZXhwb3J0LlMgKyAkZXhwb3J0LkYsICdPYmplY3QnLCB7YXNzaWduOiByZXF1aXJlKCcuL19vYmplY3QtYXNzaWduJyl9KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5hc3NpZ24uanNcbi8vIG1vZHVsZSBpZCA9IDg3XG4vLyBtb2R1bGUgY2h1bmtzID0gMyA3IDEwIDExIDEyIDEzIDE1IDE2IDE4IiwiLy8gMTkuMS4yLjE0IE9iamVjdC5rZXlzKE8pXG52YXIgdG9PYmplY3QgPSByZXF1aXJlKCcuL190by1vYmplY3QnKVxuICAsICRrZXlzICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWtleXMnKTtcblxucmVxdWlyZSgnLi9fb2JqZWN0LXNhcCcpKCdrZXlzJywgZnVuY3Rpb24oKXtcbiAgcmV0dXJuIGZ1bmN0aW9uIGtleXMoaXQpe1xuICAgIHJldHVybiAka2V5cyh0b09iamVjdChpdCkpO1xuICB9O1xufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3Qua2V5cy5qc1xuLy8gbW9kdWxlIGlkID0gODhcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA4IDkgMTAgMTUgMTkgMjAiLCIvLyAxOS4xLjIuOSAvIDE1LjIuMy4yIE9iamVjdC5nZXRQcm90b3R5cGVPZihPKVxudmFyIGhhcyAgICAgICAgID0gcmVxdWlyZSgnLi9faGFzJylcbiAgLCB0b09iamVjdCAgICA9IHJlcXVpcmUoJy4vX3RvLW9iamVjdCcpXG4gICwgSUVfUFJPVE8gICAgPSByZXF1aXJlKCcuL19zaGFyZWQta2V5JykoJ0lFX1BST1RPJylcbiAgLCBPYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbm1vZHVsZS5leHBvcnRzID0gT2JqZWN0LmdldFByb3RvdHlwZU9mIHx8IGZ1bmN0aW9uKE8pe1xuICBPID0gdG9PYmplY3QoTyk7XG4gIGlmKGhhcyhPLCBJRV9QUk9UTykpcmV0dXJuIE9bSUVfUFJPVE9dO1xuICBpZih0eXBlb2YgTy5jb25zdHJ1Y3RvciA9PSAnZnVuY3Rpb24nICYmIE8gaW5zdGFuY2VvZiBPLmNvbnN0cnVjdG9yKXtcbiAgICByZXR1cm4gTy5jb25zdHJ1Y3Rvci5wcm90b3R5cGU7XG4gIH0gcmV0dXJuIE8gaW5zdGFuY2VvZiBPYmplY3QgPyBPYmplY3RQcm90byA6IG51bGw7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdwby5qc1xuLy8gbW9kdWxlIGlkID0gOTBcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSAxNCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuZXhwb3J0IGRlZmF1bHQge1xuICBtYWluRGl2OiAnI29yZGVyLXZpZXctcGFnZScsXG4gIG9yZGVyUGF5bWVudERldGFpbHNCdG46ICcuanMtcGF5bWVudC1kZXRhaWxzLWJ0bicsXG4gIG9yZGVyUGF5bWVudEZvcm1BbW91bnRJbnB1dDogJyNvcmRlcl9wYXltZW50X2Ftb3VudCcsXG4gIG9yZGVyUGF5bWVudEludm9pY2VTZWxlY3Q6ICcjb3JkZXJfcGF5bWVudF9pZF9pbnZvaWNlJyxcbiAgdmlld09yZGVyUGF5bWVudHNCbG9jazogJyN2aWV3X29yZGVyX3BheW1lbnRzX2Jsb2NrJyxcbiAgcHJpdmF0ZU5vdGVUb2dnbGVCdG46ICcuanMtcHJpdmF0ZS1ub3RlLXRvZ2dsZS1idG4nLFxuICBwcml2YXRlTm90ZUJsb2NrOiAnLmpzLXByaXZhdGUtbm90ZS1ibG9jaycsXG4gIHByaXZhdGVOb3RlSW5wdXQ6ICcjcHJpdmF0ZV9ub3RlX25vdGUnLFxuICBwcml2YXRlTm90ZVN1Ym1pdEJ0bjogJy5qcy1wcml2YXRlLW5vdGUtYnRuJyxcbiAgYWRkQ2FydFJ1bGVNb2RhbDogJyNhZGRPcmRlckRpc2NvdW50TW9kYWwnLFxuICBhZGRDYXJ0UnVsZUludm9pY2VJZFNlbGVjdDogJyNhZGRfb3JkZXJfY2FydF9ydWxlX2ludm9pY2VfaWQnLFxuICBhZGRDYXJ0UnVsZVR5cGVTZWxlY3Q6ICcjYWRkX29yZGVyX2NhcnRfcnVsZV90eXBlJyxcbiAgYWRkQ2FydFJ1bGVWYWx1ZUlucHV0OiAnI2FkZF9vcmRlcl9jYXJ0X3J1bGVfdmFsdWUnLFxuICBhZGRDYXJ0UnVsZVZhbHVlVW5pdDogJyNhZGRfb3JkZXJfY2FydF9ydWxlX3ZhbHVlX3VuaXQnLFxuICBjYXJ0UnVsZUhlbHBUZXh0OiAnLmpzLWNhcnQtcnVsZS12YWx1ZS1oZWxwJyxcbiAgdXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25CdG46ICcjdXBkYXRlX29yZGVyX3N0YXR1c19hY3Rpb25fYnRuJyxcbiAgdXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25JbnB1dDogJyN1cGRhdGVfb3JkZXJfc3RhdHVzX2FjdGlvbl9pbnB1dCcsXG4gIHVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uSW5wdXRXcmFwcGVyOiAnI3VwZGF0ZV9vcmRlcl9zdGF0dXNfYWN0aW9uX2lucHV0X3dyYXBwZXInLFxuICB1cGRhdGVPcmRlclN0YXR1c0FjdGlvbkZvcm06ICcjdXBkYXRlX29yZGVyX3N0YXR1c19hY3Rpb25fZm9ybScsXG4gIHNob3dPcmRlclNoaXBwaW5nVXBkYXRlTW9kYWxCdG46ICcuanMtdXBkYXRlLXNoaXBwaW5nLWJ0bicsXG4gIHVwZGF0ZU9yZGVyU2hpcHBpbmdUcmFja2luZ051bWJlcklucHV0OiAnI3VwZGF0ZV9vcmRlcl9zaGlwcGluZ190cmFja2luZ19udW1iZXInLFxuICB1cGRhdGVPcmRlclNoaXBwaW5nQ3VycmVudE9yZGVyQ2FycmllcklkSW5wdXQ6ICcjdXBkYXRlX29yZGVyX3NoaXBwaW5nX2N1cnJlbnRfb3JkZXJfY2Fycmllcl9pZCcsXG4gIHVwZGF0ZUN1c3RvbWVyQWRkcmVzc01vZGFsOiAnI3VwZGF0ZUN1c3RvbWVyQWRkcmVzc01vZGFsJyxcbiAgb3Blbk9yZGVyQWRkcmVzc1VwZGF0ZU1vZGFsQnRuOiAnLmpzLXVwZGF0ZS1jdXN0b21lci1hZGRyZXNzLW1vZGFsLWJ0bicsXG4gIHVwZGF0ZU9yZGVyQWRkcmVzc1R5cGVJbnB1dDogJyNjaGFuZ2Vfb3JkZXJfYWRkcmVzc19hZGRyZXNzX3R5cGUnLFxuICBkZWxpdmVyeUFkZHJlc3NFZGl0QnRuOiAnI2pzLWRlbGl2ZXJ5LWFkZHJlc3MtZWRpdC1idG4nLFxuICBpbnZvaWNlQWRkcmVzc0VkaXRCdG46ICcjanMtaW52b2ljZS1hZGRyZXNzLWVkaXQtYnRuJyxcbiAgb3JkZXJNZXNzYWdlTmFtZVNlbGVjdDogJyNvcmRlcl9tZXNzYWdlX29yZGVyX21lc3NhZ2UnLFxuICBvcmRlck1lc3NhZ2VzQ29udGFpbmVyOiAnLmpzLW9yZGVyLW1lc3NhZ2VzLWNvbnRhaW5lcicsXG4gIG9yZGVyTWVzc2FnZTogJyNvcmRlcl9tZXNzYWdlX21lc3NhZ2UnLFxuICBvcmRlck1lc3NhZ2VDaGFuZ2VXYXJuaW5nOiAnLmpzLW1lc3NhZ2UtY2hhbmdlLXdhcm5pbmcnLFxuICBvcmRlckRvY3VtZW50c1RhYkNvdW50OiAnI29yZGVyRG9jdW1lbnRzVGFiIC5jb3VudCcsXG4gIG9yZGVyRG9jdW1lbnRzVGFiQm9keTogJyNvcmRlckRvY3VtZW50c1RhYkNvbnRlbnQgLmNhcmQtYm9keScsXG4gIGFsbE1lc3NhZ2VzTW9kYWw6ICcjdmlld19hbGxfbWVzc2FnZXNfbW9kYWwnLFxuICBhbGxNZXNzYWdlc0xpc3Q6ICcjYWxsLW1lc3NhZ2VzLWxpc3QnLFxuICBvcGVuQWxsTWVzc2FnZXNCdG46ICcuanMtb3Blbi1hbGwtbWVzc2FnZXMtYnRuJyxcbiAgLy8gUHJvZHVjdHMgdGFibGUgZWxlbWVudHNcbiAgcHJvZHVjdE9yaWdpbmFsUG9zaXRpb246ICcjb3JkZXJQcm9kdWN0c09yaWdpbmFsUG9zaXRpb24nLFxuICBwcm9kdWN0TW9kaWZpY2F0aW9uUG9zaXRpb246ICcjb3JkZXJQcm9kdWN0c01vZGlmaWNhdGlvblBvc2l0aW9uJyxcbiAgcHJvZHVjdHNQYW5lbDogJyNvcmRlclByb2R1Y3RzUGFuZWwnLFxuICBwcm9kdWN0c0NvdW50OiAnI29yZGVyUHJvZHVjdHNQYW5lbENvdW50JyxcbiAgcHJvZHVjdERlbGV0ZUJ0bjogJy5qcy1vcmRlci1wcm9kdWN0LWRlbGV0ZS1idG4nLFxuICBwcm9kdWN0c1RhYmxlOiAnI29yZGVyUHJvZHVjdHNUYWJsZScsXG4gIHByb2R1Y3RzUGFnaW5hdGlvbjogJy5vcmRlci1wcm9kdWN0LXBhZ2luYXRpb24nLFxuICBwcm9kdWN0c05hdlBhZ2luYXRpb246ICcjb3JkZXJQcm9kdWN0c05hdlBhZ2luYXRpb24nLFxuICBwcm9kdWN0c1RhYmxlUGFnaW5hdGlvbjogJyNvcmRlclByb2R1Y3RzVGFibGVQYWdpbmF0aW9uJyxcbiAgcHJvZHVjdHNUYWJsZVBhZ2luYXRpb25OZXh0OiAnI29yZGVyUHJvZHVjdHNUYWJsZVBhZ2luYXRpb25OZXh0JyxcbiAgcHJvZHVjdHNUYWJsZVBhZ2luYXRpb25QcmV2OiAnI29yZGVyUHJvZHVjdHNUYWJsZVBhZ2luYXRpb25QcmV2JyxcbiAgcHJvZHVjdHNUYWJsZVBhZ2luYXRpb25MaW5rOiAnLnBhZ2UtaXRlbTpub3QoLmQtbm9uZSk6bm90KCNvcmRlclByb2R1Y3RzVGFibGVQYWdpbmF0aW9uTmV4dCk6bm90KCNvcmRlclByb2R1Y3RzVGFibGVQYWdpbmF0aW9uUHJldikgLnBhZ2UtbGluaycsXG4gIHByb2R1Y3RzVGFibGVQYWdpbmF0aW9uQWN0aXZlOiAnI29yZGVyUHJvZHVjdHNUYWJsZVBhZ2luYXRpb24gLnBhZ2UtaXRlbS5hY3RpdmUgc3BhbicsXG4gIHByb2R1Y3RzVGFibGVQYWdpbmF0aW9uVGVtcGxhdGU6ICcjb3JkZXJQcm9kdWN0c1RhYmxlUGFnaW5hdGlvbiAucGFnZS1pdGVtLmQtbm9uZScsXG4gIHByb2R1Y3RzVGFibGVQYWdpbmF0aW9uTnVtYmVyU2VsZWN0b3I6ICcjb3JkZXJQcm9kdWN0c1RhYmxlUGFnaW5hdGlvbk51bWJlclNlbGVjdG9yJyxcbiAgcHJvZHVjdHNUYWJsZVJvdzogKHByb2R1Y3RJZCkgPT4gYCNvcmRlclByb2R1Y3RfJHtwcm9kdWN0SWR9YCxcbiAgcHJvZHVjdHNUYWJsZVJvd0VkaXRlZDogKHByb2R1Y3RJZCkgPT4gYCNlZGl0T3JkZXJQcm9kdWN0XyR7cHJvZHVjdElkfWAsXG4gIHByb2R1Y3RzVGFibGVSb3dzOiAndHIuY2VsbFByb2R1Y3QnLFxuICBwcm9kdWN0c0NlbGxMb2NhdGlvbjogJ3RyIC5jZWxsUHJvZHVjdExvY2F0aW9uJyxcbiAgcHJvZHVjdHNDZWxsUmVmdW5kZWQ6ICd0ciAuY2VsbFByb2R1Y3RSZWZ1bmRlZCcsXG4gIHByb2R1Y3RzQ2VsbExvY2F0aW9uRGlzcGxheWVkOiAndHI6bm90KC5kLW5vbmUpIC5jZWxsUHJvZHVjdExvY2F0aW9uJyxcbiAgcHJvZHVjdHNDZWxsUmVmdW5kZWREaXNwbGF5ZWQ6ICd0cjpub3QoLmQtbm9uZSkgLmNlbGxQcm9kdWN0UmVmdW5kZWQnLFxuICBwcm9kdWN0c1RhYmxlQ3VzdG9taXphdGlvblJvd3M6ICcjb3JkZXJQcm9kdWN0c1RhYmxlIC5vcmRlci1wcm9kdWN0LWN1c3RvbWl6YXRpb24nLFxuICBwcm9kdWN0RWRpdEJ1dHRvbnM6ICcuanMtb3JkZXItcHJvZHVjdC1lZGl0LWJ0bicsXG4gIHByb2R1Y3RFZGl0QnRuOiAocHJvZHVjdElkKSA9PiBgI29yZGVyUHJvZHVjdF8ke3Byb2R1Y3RJZH0gLmpzLW9yZGVyLXByb2R1Y3QtZWRpdC1idG5gLFxuICBwcm9kdWN0QWRkQnRuOiAnI2FkZFByb2R1Y3RCdG4nLFxuICBwcm9kdWN0QWN0aW9uQnRuOiAnLmpzLXByb2R1Y3QtYWN0aW9uLWJ0bicsXG4gIHByb2R1Y3RBZGRBY3Rpb25CdG46ICcjYWRkX3Byb2R1Y3Rfcm93X2FkZCcsXG4gIHByb2R1Y3RDYW5jZWxBZGRCdG46ICcjYWRkX3Byb2R1Y3Rfcm93X2NhbmNlbCcsXG4gIHByb2R1Y3RBZGRSb3c6ICcjYWRkUHJvZHVjdFRhYmxlUm93JyxcbiAgcHJvZHVjdFNlYXJjaElucHV0OiAnI2FkZF9wcm9kdWN0X3Jvd19zZWFyY2gnLFxuICBwcm9kdWN0U2VhcmNoSW5wdXRBdXRvY29tcGxldGU6ICcjYWRkUHJvZHVjdFRhYmxlUm93IC5kcm9wZG93bicsXG4gIHByb2R1Y3RTZWFyY2hJbnB1dEF1dG9jb21wbGV0ZU1lbnU6ICcjYWRkUHJvZHVjdFRhYmxlUm93IC5kcm9wZG93biAuZHJvcGRvd24tbWVudScsXG4gIHByb2R1Y3RBZGRJZElucHV0OiAnI2FkZF9wcm9kdWN0X3Jvd19wcm9kdWN0X2lkJyxcbiAgcHJvZHVjdEFkZFRheFJhdGVJbnB1dDogJyNhZGRfcHJvZHVjdF9yb3dfdGF4X3JhdGUnLFxuICBwcm9kdWN0QWRkQ29tYmluYXRpb25zQmxvY2s6ICcjYWRkUHJvZHVjdENvbWJpbmF0aW9ucycsXG4gIHByb2R1Y3RBZGRDb21iaW5hdGlvbnNTZWxlY3Q6ICcjYWRkUHJvZHVjdENvbWJpbmF0aW9uSWQnLFxuICBwcm9kdWN0QWRkUHJpY2VUYXhFeGNsSW5wdXQ6ICcjYWRkX3Byb2R1Y3Rfcm93X3ByaWNlX3RheF9leGNsdWRlZCcsXG4gIHByb2R1Y3RBZGRQcmljZVRheEluY2xJbnB1dDogJyNhZGRfcHJvZHVjdF9yb3dfcHJpY2VfdGF4X2luY2x1ZGVkJyxcbiAgcHJvZHVjdEFkZFF1YW50aXR5SW5wdXQ6ICcjYWRkX3Byb2R1Y3Rfcm93X3F1YW50aXR5JyxcbiAgcHJvZHVjdEFkZEF2YWlsYWJsZVRleHQ6ICcjYWRkUHJvZHVjdEF2YWlsYWJsZScsXG4gIHByb2R1Y3RBZGRMb2NhdGlvblRleHQ6ICcjYWRkUHJvZHVjdExvY2F0aW9uJyxcbiAgcHJvZHVjdEFkZFRvdGFsUHJpY2VUZXh0OiAnI2FkZFByb2R1Y3RUb3RhbFByaWNlJyxcbiAgcHJvZHVjdEFkZEludm9pY2VTZWxlY3Q6ICcjYWRkX3Byb2R1Y3Rfcm93X2ludm9pY2UnLFxuICBwcm9kdWN0QWRkRnJlZVNoaXBwaW5nU2VsZWN0OiAnI2FkZF9wcm9kdWN0X3Jvd19mcmVlX3NoaXBwaW5nJyxcbiAgcHJvZHVjdEFkZE5ld0ludm9pY2VJbmZvOiAnI2FkZFByb2R1Y3ROZXdJbnZvaWNlSW5mbycsXG4gIHByb2R1Y3RFZGl0U2F2ZUJ0bjogJy5wcm9kdWN0RWRpdFNhdmVCdG4nLFxuICBwcm9kdWN0RWRpdENhbmNlbEJ0bjogJy5wcm9kdWN0RWRpdENhbmNlbEJ0bicsXG4gIHByb2R1Y3RFZGl0Um93VGVtcGxhdGU6ICcjZWRpdFByb2R1Y3RUYWJsZVJvd1RlbXBsYXRlJyxcbiAgcHJvZHVjdEVkaXRSb3c6ICcuZWRpdFByb2R1Y3RSb3cnLFxuICBwcm9kdWN0RWRpdEltYWdlOiAnLmNlbGxQcm9kdWN0SW1nJyxcbiAgcHJvZHVjdEVkaXROYW1lOiAnLmNlbGxQcm9kdWN0TmFtZScsXG4gIHByb2R1Y3RFZGl0VW5pdFByaWNlOiAnLmNlbGxQcm9kdWN0VW5pdFByaWNlJyxcbiAgcHJvZHVjdEVkaXRRdWFudGl0eTogJy5jZWxsUHJvZHVjdFF1YW50aXR5JyxcbiAgcHJvZHVjdEVkaXRBdmFpbGFibGVRdWFudGl0eTogJy5jZWxsUHJvZHVjdEF2YWlsYWJsZVF1YW50aXR5JyxcbiAgcHJvZHVjdEVkaXRUb3RhbFByaWNlOiAnLmNlbGxQcm9kdWN0VG90YWxQcmljZScsXG4gIHByb2R1Y3RFZGl0UHJpY2VUYXhFeGNsSW5wdXQ6ICcuZWRpdFByb2R1Y3RQcmljZVRheEV4Y2wnLFxuICBwcm9kdWN0RWRpdFByaWNlVGF4SW5jbElucHV0OiAnLmVkaXRQcm9kdWN0UHJpY2VUYXhJbmNsJyxcbiAgcHJvZHVjdEVkaXRJbnZvaWNlU2VsZWN0OiAnLmVkaXRQcm9kdWN0SW52b2ljZScsXG4gIHByb2R1Y3RFZGl0UXVhbnRpdHlJbnB1dDogJy5lZGl0UHJvZHVjdFF1YW50aXR5JyxcbiAgcHJvZHVjdEVkaXRMb2NhdGlvblRleHQ6ICcuZWRpdFByb2R1Y3RMb2NhdGlvbicsXG4gIHByb2R1Y3RFZGl0QXZhaWxhYmxlVGV4dDogJy5lZGl0UHJvZHVjdEF2YWlsYWJsZScsXG4gIHByb2R1Y3RFZGl0VG90YWxQcmljZVRleHQ6ICcuZWRpdFByb2R1Y3RUb3RhbFByaWNlJyxcbiAgLy8gUHJvZHVjdCBEaXNjb3VudCBMaXN0XG4gIHByb2R1Y3REaXNjb3VudExpc3Q6IHtcbiAgICBsaXN0OiAnLnRhYmxlLmRpc2NvdW50TGlzdCcsXG4gIH0sXG4gIC8vIFByb2R1Y3QgUGFjayBNb2RhbFxuICBwcm9kdWN0UGFja01vZGFsOiB7XG4gICAgbW9kYWw6ICcjcHJvZHVjdC1wYWNrLW1vZGFsJyxcbiAgICB0YWJsZTogJyNwcm9kdWN0LXBhY2stbW9kYWwtdGFibGUgdGJvZHknLFxuICAgIHJvd3M6ICcjcHJvZHVjdC1wYWNrLW1vZGFsLXRhYmxlIHRib2R5IHRyOm5vdCgjdGVtcGxhdGUtcGFjay10YWJsZS1yb3cpJyxcbiAgICB0ZW1wbGF0ZTogJyN0ZW1wbGF0ZS1wYWNrLXRhYmxlLXJvdycsXG4gICAgcHJvZHVjdDoge1xuICAgICAgaW1nOiAnLmNlbGwtcHJvZHVjdC1pbWcgaW1nJyxcbiAgICAgIGxpbms6ICcuY2VsbC1wcm9kdWN0LW5hbWUgYScsXG4gICAgICBuYW1lOiAnLmNlbGwtcHJvZHVjdC1uYW1lIC5wcm9kdWN0LW5hbWUnLFxuICAgICAgcmVmOiAnLmNlbGwtcHJvZHVjdC1uYW1lIC5wcm9kdWN0LXJlZmVyZW5jZScsXG4gICAgICBzdXBwbGllclJlZjogJy5jZWxsLXByb2R1Y3QtbmFtZSAucHJvZHVjdC1zdXBwbGllci1yZWZlcmVuY2UnLFxuICAgICAgcXVhbnRpdHk6ICcuY2VsbC1wcm9kdWN0LXF1YW50aXR5JyxcbiAgICAgIGF2YWlsYWJsZVF1YW50aXR5OiAnLmNlbGwtcHJvZHVjdC1hdmFpbGFibGUtcXVhbnRpdHknLFxuICAgIH0sXG4gIH0sXG4gIC8vIE9yZGVyIHByaWNlIGVsZW1lbnRzXG4gIG9yZGVyUHJvZHVjdHNUb3RhbDogJyNvcmRlclByb2R1Y3RzVG90YWwnLFxuICBvcmRlckRpc2NvdW50c1RvdGFsQ29udGFpbmVyOiAnI29yZGVyLWRpc2NvdW50cy10b3RhbC1jb250YWluZXInLFxuICBvcmRlckRpc2NvdW50c1RvdGFsOiAnI29yZGVyRGlzY291bnRzVG90YWwnLFxuICBvcmRlcldyYXBwaW5nVG90YWw6ICcjb3JkZXJXcmFwcGluZ1RvdGFsJyxcbiAgb3JkZXJTaGlwcGluZ1RvdGFsOiAnI29yZGVyU2hpcHBpbmdUb3RhbCcsXG4gIG9yZGVyVGF4ZXNUb3RhbDogJyNvcmRlclRheGVzVG90YWwnLFxuICBvcmRlclRvdGFsOiAnI29yZGVyVG90YWwnLFxuICBvcmRlckhvb2tUYWJzQ29udGFpbmVyOiAnI29yZGVyX2hvb2tfdGFicycsXG4gIC8vIFByb2R1Y3QgY2FuY2VsL3JlZnVuZCBlbGVtZW50c1xuICBjYW5jZWxQcm9kdWN0OiB7XG4gICAgZm9ybTogJ2Zvcm1bbmFtZT1cImNhbmNlbF9wcm9kdWN0XCJdJyxcbiAgICBidXR0b25zOiB7XG4gICAgICBhYm9ydDogJ2J1dHRvbi5jYW5jZWwtcHJvZHVjdC1lbGVtZW50LWFib3J0JyxcbiAgICAgIHNhdmU6ICcjY2FuY2VsX3Byb2R1Y3Rfc2F2ZScsXG4gICAgICBwYXJ0aWFsUmVmdW5kOiAnYnV0dG9uLnBhcnRpYWwtcmVmdW5kLWRpc3BsYXknLFxuICAgICAgc3RhbmRhcmRSZWZ1bmQ6ICdidXR0b24uc3RhbmRhcmQtcmVmdW5kLWRpc3BsYXknLFxuICAgICAgcmV0dXJuUHJvZHVjdDogJ2J1dHRvbi5yZXR1cm4tcHJvZHVjdC1kaXNwbGF5JyxcbiAgICAgIGNhbmNlbFByb2R1Y3RzOiAnYnV0dG9uLmNhbmNlbC1wcm9kdWN0LWRpc3BsYXknLFxuICAgIH0sXG4gICAgaW5wdXRzOiB7XG4gICAgICBxdWFudGl0eTogJy5jYW5jZWwtcHJvZHVjdC1xdWFudGl0eSBpbnB1dCcsXG4gICAgICBhbW91bnQ6ICcuY2FuY2VsLXByb2R1Y3QtYW1vdW50IGlucHV0JyxcbiAgICAgIHNlbGVjdG9yOiAnLmNhbmNlbC1wcm9kdWN0LXNlbGVjdG9yIGlucHV0JyxcbiAgICB9LFxuICAgIHRhYmxlOiB7XG4gICAgICBjZWxsOiAnLmNhbmNlbC1wcm9kdWN0LWNlbGwnLFxuICAgICAgaGVhZGVyOiAndGguY2FuY2VsLXByb2R1Y3QtZWxlbWVudCBwJyxcbiAgICAgIGFjdGlvbnM6ICd0ZC5jZWxsUHJvZHVjdEFjdGlvbnMsIHRoLnByb2R1Y3RfYWN0aW9ucycsXG4gICAgfSxcbiAgICBjaGVja2JveGVzOiB7XG4gICAgICByZXN0b2NrOiAnI2NhbmNlbF9wcm9kdWN0X3Jlc3RvY2snLFxuICAgICAgY3JlZGl0U2xpcDogJyNjYW5jZWxfcHJvZHVjdF9jcmVkaXRfc2xpcCcsXG4gICAgICB2b3VjaGVyOiAnI2NhbmNlbF9wcm9kdWN0X3ZvdWNoZXInLFxuICAgIH0sXG4gICAgcmFkaW9zOiB7XG4gICAgICB2b3VjaGVyUmVmdW5kVHlwZToge1xuICAgICAgICBwcm9kdWN0UHJpY2VzOiAnaW5wdXRbdm91Y2hlci1yZWZ1bmQtdHlwZT1cIjBcIl0nLFxuICAgICAgICBwcm9kdWN0UHJpY2VzVm91Y2hlckV4Y2x1ZGVkOiAnaW5wdXRbdm91Y2hlci1yZWZ1bmQtdHlwZT1cIjFcIl0nLFxuICAgICAgICBuZWdhdGl2ZUVycm9yTWVzc2FnZTogJy52b3VjaGVyLXJlZnVuZC10eXBlLW5lZ2F0aXZlLWVycm9yJyxcbiAgICAgIH0sXG4gICAgfSxcbiAgICB0b2dnbGU6IHtcbiAgICAgIHBhcnRpYWxSZWZ1bmQ6ICcuY2FuY2VsLXByb2R1Y3QtZWxlbWVudDpub3QoLmhpZGRlbik6bm90KC5zaGlwcGluZy1yZWZ1bmQpLCAuY2FuY2VsLXByb2R1Y3QtYW1vdW50JyxcbiAgICAgIHN0YW5kYXJkUmVmdW5kOiAnLmNhbmNlbC1wcm9kdWN0LWVsZW1lbnQ6bm90KC5oaWRkZW4pOm5vdCguc2hpcHBpbmctcmVmdW5kLWFtb3VudCk6bm90KC5yZXN0b2NrLXByb2R1Y3RzKSwgLmNhbmNlbC1wcm9kdWN0LXNlbGVjdG9yJyxcbiAgICAgIHJldHVyblByb2R1Y3Q6ICcuY2FuY2VsLXByb2R1Y3QtZWxlbWVudDpub3QoLmhpZGRlbik6bm90KC5zaGlwcGluZy1yZWZ1bmQtYW1vdW50KSwgLmNhbmNlbC1wcm9kdWN0LXNlbGVjdG9yJyxcbiAgICAgIGNhbmNlbFByb2R1Y3RzOiAnLmNhbmNlbC1wcm9kdWN0LWVsZW1lbnQ6bm90KC5oaWRkZW4pOm5vdCguc2hpcHBpbmctcmVmdW5kLWFtb3VudCk6bm90KC5zaGlwcGluZy1yZWZ1bmQpOm5vdCgucmVzdG9jay1wcm9kdWN0cyk6bm90KC5yZWZ1bmQtY3JlZGl0LXNsaXApOm5vdCgucmVmdW5kLXZvdWNoZXIpOm5vdCgudm91Y2hlci1yZWZ1bmQtdHlwZSksIC5jYW5jZWwtcHJvZHVjdC1zZWxlY3RvcicsXG4gICAgfSxcbiAgfSxcbiAgcHJpbnRPcmRlclZpZXdQYWdlQnV0dG9uOiAnLmpzLXByaW50LW9yZGVyLXZpZXctcGFnZScsXG59O1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvT3JkZXJWaWV3UGFnZU1hcC5qcyIsIlwidXNlIHN0cmljdFwiO1xuXG5leHBvcnRzLl9fZXNNb2R1bGUgPSB0cnVlO1xuXG52YXIgX2l0ZXJhdG9yID0gcmVxdWlyZShcIi4uL2NvcmUtanMvc3ltYm9sL2l0ZXJhdG9yXCIpO1xuXG52YXIgX2l0ZXJhdG9yMiA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX2l0ZXJhdG9yKTtcblxudmFyIF9zeW1ib2wgPSByZXF1aXJlKFwiLi4vY29yZS1qcy9zeW1ib2xcIik7XG5cbnZhciBfc3ltYm9sMiA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX3N5bWJvbCk7XG5cbnZhciBfdHlwZW9mID0gdHlwZW9mIF9zeW1ib2wyLmRlZmF1bHQgPT09IFwiZnVuY3Rpb25cIiAmJiB0eXBlb2YgX2l0ZXJhdG9yMi5kZWZhdWx0ID09PSBcInN5bWJvbFwiID8gZnVuY3Rpb24gKG9iaikgeyByZXR1cm4gdHlwZW9mIG9iajsgfSA6IGZ1bmN0aW9uIChvYmopIHsgcmV0dXJuIG9iaiAmJiB0eXBlb2YgX3N5bWJvbDIuZGVmYXVsdCA9PT0gXCJmdW5jdGlvblwiICYmIG9iai5jb25zdHJ1Y3RvciA9PT0gX3N5bWJvbDIuZGVmYXVsdCAmJiBvYmogIT09IF9zeW1ib2wyLmRlZmF1bHQucHJvdG90eXBlID8gXCJzeW1ib2xcIiA6IHR5cGVvZiBvYmo7IH07XG5cbmZ1bmN0aW9uIF9pbnRlcm9wUmVxdWlyZURlZmF1bHQob2JqKSB7IHJldHVybiBvYmogJiYgb2JqLl9fZXNNb2R1bGUgPyBvYmogOiB7IGRlZmF1bHQ6IG9iaiB9OyB9XG5cbmV4cG9ydHMuZGVmYXVsdCA9IHR5cGVvZiBfc3ltYm9sMi5kZWZhdWx0ID09PSBcImZ1bmN0aW9uXCIgJiYgX3R5cGVvZihfaXRlcmF0b3IyLmRlZmF1bHQpID09PSBcInN5bWJvbFwiID8gZnVuY3Rpb24gKG9iaikge1xuICByZXR1cm4gdHlwZW9mIG9iaiA9PT0gXCJ1bmRlZmluZWRcIiA/IFwidW5kZWZpbmVkXCIgOiBfdHlwZW9mKG9iaik7XG59IDogZnVuY3Rpb24gKG9iaikge1xuICByZXR1cm4gb2JqICYmIHR5cGVvZiBfc3ltYm9sMi5kZWZhdWx0ID09PSBcImZ1bmN0aW9uXCIgJiYgb2JqLmNvbnN0cnVjdG9yID09PSBfc3ltYm9sMi5kZWZhdWx0ICYmIG9iaiAhPT0gX3N5bWJvbDIuZGVmYXVsdC5wcm90b3R5cGUgPyBcInN5bWJvbFwiIDogdHlwZW9mIG9iaiA9PT0gXCJ1bmRlZmluZWRcIiA/IFwidW5kZWZpbmVkXCIgOiBfdHlwZW9mKG9iaik7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvdHlwZW9mLmpzXG4vLyBtb2R1bGUgaWQgPSA5M1xuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwiLy8gZ2V0dGluZyB0YWcgZnJvbSAxOS4xLjMuNiBPYmplY3QucHJvdG90eXBlLnRvU3RyaW5nKClcbnZhciBjb2YgPSByZXF1aXJlKCcuL19jb2YnKVxuICAsIFRBRyA9IHJlcXVpcmUoJy4vX3drcycpKCd0b1N0cmluZ1RhZycpXG4gIC8vIEVTMyB3cm9uZyBoZXJlXG4gICwgQVJHID0gY29mKGZ1bmN0aW9uKCl7IHJldHVybiBhcmd1bWVudHM7IH0oKSkgPT0gJ0FyZ3VtZW50cyc7XG5cbi8vIGZhbGxiYWNrIGZvciBJRTExIFNjcmlwdCBBY2Nlc3MgRGVuaWVkIGVycm9yXG52YXIgdHJ5R2V0ID0gZnVuY3Rpb24oaXQsIGtleSl7XG4gIHRyeSB7XG4gICAgcmV0dXJuIGl0W2tleV07XG4gIH0gY2F0Y2goZSl7IC8qIGVtcHR5ICovIH1cbn07XG5cbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICB2YXIgTywgVCwgQjtcbiAgcmV0dXJuIGl0ID09PSB1bmRlZmluZWQgPyAnVW5kZWZpbmVkJyA6IGl0ID09PSBudWxsID8gJ051bGwnXG4gICAgLy8gQEB0b1N0cmluZ1RhZyBjYXNlXG4gICAgOiB0eXBlb2YgKFQgPSB0cnlHZXQoTyA9IE9iamVjdChpdCksIFRBRykpID09ICdzdHJpbmcnID8gVFxuICAgIC8vIGJ1aWx0aW5UYWcgY2FzZVxuICAgIDogQVJHID8gY29mKE8pXG4gICAgLy8gRVMzIGFyZ3VtZW50cyBmYWxsYmFja1xuICAgIDogKEIgPSBjb2YoTykpID09ICdPYmplY3QnICYmIHR5cGVvZiBPLmNhbGxlZSA9PSAnZnVuY3Rpb24nID8gJ0FyZ3VtZW50cycgOiBCO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NsYXNzb2YuanNcbi8vIG1vZHVsZSBpZCA9IDk0XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSAxNCIsIm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi9fZ2xvYmFsJykuZG9jdW1lbnQgJiYgZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faHRtbC5qc1xuLy8gbW9kdWxlIGlkID0gOTVcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSAxNCIsInZhciBwSUUgICAgICAgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1waWUnKVxuICAsIGNyZWF0ZURlc2MgICAgID0gcmVxdWlyZSgnLi9fcHJvcGVydHktZGVzYycpXG4gICwgdG9JT2JqZWN0ICAgICAgPSByZXF1aXJlKCcuL190by1pb2JqZWN0JylcbiAgLCB0b1ByaW1pdGl2ZSAgICA9IHJlcXVpcmUoJy4vX3RvLXByaW1pdGl2ZScpXG4gICwgaGFzICAgICAgICAgICAgPSByZXF1aXJlKCcuL19oYXMnKVxuICAsIElFOF9ET01fREVGSU5FID0gcmVxdWlyZSgnLi9faWU4LWRvbS1kZWZpbmUnKVxuICAsIGdPUEQgICAgICAgICAgID0gT2JqZWN0LmdldE93blByb3BlcnR5RGVzY3JpcHRvcjtcblxuZXhwb3J0cy5mID0gcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSA/IGdPUEQgOiBmdW5jdGlvbiBnZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3IoTywgUCl7XG4gIE8gPSB0b0lPYmplY3QoTyk7XG4gIFAgPSB0b1ByaW1pdGl2ZShQLCB0cnVlKTtcbiAgaWYoSUU4X0RPTV9ERUZJTkUpdHJ5IHtcbiAgICByZXR1cm4gZ09QRChPLCBQKTtcbiAgfSBjYXRjaChlKXsgLyogZW1wdHkgKi8gfVxuICBpZihoYXMoTywgUCkpcmV0dXJuIGNyZWF0ZURlc2MoIXBJRS5mLmNhbGwoTywgUCksIE9bUF0pO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1nb3BkLmpzXG4vLyBtb2R1bGUgaWQgPSA5NlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbigpeyAvKiBlbXB0eSAqLyB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYWRkLXRvLXVuc2NvcGFibGVzLmpzXG4vLyBtb2R1bGUgaWQgPSA5OFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IDE0IiwiJ3VzZSBzdHJpY3QnO1xudmFyIGNyZWF0ZSAgICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWNyZWF0ZScpXG4gICwgZGVzY3JpcHRvciAgICAgPSByZXF1aXJlKCcuL19wcm9wZXJ0eS1kZXNjJylcbiAgLCBzZXRUb1N0cmluZ1RhZyA9IHJlcXVpcmUoJy4vX3NldC10by1zdHJpbmctdGFnJylcbiAgLCBJdGVyYXRvclByb3RvdHlwZSA9IHt9O1xuXG4vLyAyNS4xLjIuMS4xICVJdGVyYXRvclByb3RvdHlwZSVbQEBpdGVyYXRvcl0oKVxucmVxdWlyZSgnLi9faGlkZScpKEl0ZXJhdG9yUHJvdG90eXBlLCByZXF1aXJlKCcuL193a3MnKSgnaXRlcmF0b3InKSwgZnVuY3Rpb24oKXsgcmV0dXJuIHRoaXM7IH0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKENvbnN0cnVjdG9yLCBOQU1FLCBuZXh0KXtcbiAgQ29uc3RydWN0b3IucHJvdG90eXBlID0gY3JlYXRlKEl0ZXJhdG9yUHJvdG90eXBlLCB7bmV4dDogZGVzY3JpcHRvcigxLCBuZXh0KX0pO1xuICBzZXRUb1N0cmluZ1RhZyhDb25zdHJ1Y3RvciwgTkFNRSArICcgSXRlcmF0b3InKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pdGVyLWNyZWF0ZS5qc1xuLy8gbW9kdWxlIGlkID0gOTlcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSAxNCIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oZG9uZSwgdmFsdWUpe1xuICByZXR1cm4ge3ZhbHVlOiB2YWx1ZSwgZG9uZTogISFkb25lfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pdGVyLXN0ZXAuanNcbi8vIG1vZHVsZSBpZCA9IDEwMFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IDE0IiwidmFyIGRQICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWRwJylcbiAgLCBhbk9iamVjdCA9IHJlcXVpcmUoJy4vX2FuLW9iamVjdCcpXG4gICwgZ2V0S2V5cyAgPSByZXF1aXJlKCcuL19vYmplY3Qta2V5cycpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJykgPyBPYmplY3QuZGVmaW5lUHJvcGVydGllcyA6IGZ1bmN0aW9uIGRlZmluZVByb3BlcnRpZXMoTywgUHJvcGVydGllcyl7XG4gIGFuT2JqZWN0KE8pO1xuICB2YXIga2V5cyAgID0gZ2V0S2V5cyhQcm9wZXJ0aWVzKVxuICAgICwgbGVuZ3RoID0ga2V5cy5sZW5ndGhcbiAgICAsIGkgPSAwXG4gICAgLCBQO1xuICB3aGlsZShsZW5ndGggPiBpKWRQLmYoTywgUCA9IGtleXNbaSsrXSwgUHJvcGVydGllc1tQXSk7XG4gIHJldHVybiBPO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1kcHMuanNcbi8vIG1vZHVsZSBpZCA9IDEwMVxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IDE0IiwidmFyIHRvSW50ZWdlciA9IHJlcXVpcmUoJy4vX3RvLWludGVnZXInKVxuICAsIGRlZmluZWQgICA9IHJlcXVpcmUoJy4vX2RlZmluZWQnKTtcbi8vIHRydWUgIC0+IFN0cmluZyNhdFxuLy8gZmFsc2UgLT4gU3RyaW5nI2NvZGVQb2ludEF0XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKFRPX1NUUklORyl7XG4gIHJldHVybiBmdW5jdGlvbih0aGF0LCBwb3Mpe1xuICAgIHZhciBzID0gU3RyaW5nKGRlZmluZWQodGhhdCkpXG4gICAgICAsIGkgPSB0b0ludGVnZXIocG9zKVxuICAgICAgLCBsID0gcy5sZW5ndGhcbiAgICAgICwgYSwgYjtcbiAgICBpZihpIDwgMCB8fCBpID49IGwpcmV0dXJuIFRPX1NUUklORyA/ICcnIDogdW5kZWZpbmVkO1xuICAgIGEgPSBzLmNoYXJDb2RlQXQoaSk7XG4gICAgcmV0dXJuIGEgPCAweGQ4MDAgfHwgYSA+IDB4ZGJmZiB8fCBpICsgMSA9PT0gbCB8fCAoYiA9IHMuY2hhckNvZGVBdChpICsgMSkpIDwgMHhkYzAwIHx8IGIgPiAweGRmZmZcbiAgICAgID8gVE9fU1RSSU5HID8gcy5jaGFyQXQoaSkgOiBhXG4gICAgICA6IFRPX1NUUklORyA/IHMuc2xpY2UoaSwgaSArIDIpIDogKGEgLSAweGQ4MDAgPDwgMTApICsgKGIgLSAweGRjMDApICsgMHgxMDAwMDtcbiAgfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19zdHJpbmctYXQuanNcbi8vIG1vZHVsZSBpZCA9IDEwMlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IDE0IiwidmFyIGNsYXNzb2YgICA9IHJlcXVpcmUoJy4vX2NsYXNzb2YnKVxuICAsIElURVJBVE9SICA9IHJlcXVpcmUoJy4vX3drcycpKCdpdGVyYXRvcicpXG4gICwgSXRlcmF0b3JzID0gcmVxdWlyZSgnLi9faXRlcmF0b3JzJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4vX2NvcmUnKS5nZXRJdGVyYXRvck1ldGhvZCA9IGZ1bmN0aW9uKGl0KXtcbiAgaWYoaXQgIT0gdW5kZWZpbmVkKXJldHVybiBpdFtJVEVSQVRPUl1cbiAgICB8fCBpdFsnQEBpdGVyYXRvciddXG4gICAgfHwgSXRlcmF0b3JzW2NsYXNzb2YoaXQpXTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2NvcmUuZ2V0LWl0ZXJhdG9yLW1ldGhvZC5qc1xuLy8gbW9kdWxlIGlkID0gMTAzXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSAxNCIsIid1c2Ugc3RyaWN0JztcbnZhciBhZGRUb1Vuc2NvcGFibGVzID0gcmVxdWlyZSgnLi9fYWRkLXRvLXVuc2NvcGFibGVzJylcbiAgLCBzdGVwICAgICAgICAgICAgID0gcmVxdWlyZSgnLi9faXRlci1zdGVwJylcbiAgLCBJdGVyYXRvcnMgICAgICAgID0gcmVxdWlyZSgnLi9faXRlcmF0b3JzJylcbiAgLCB0b0lPYmplY3QgICAgICAgID0gcmVxdWlyZSgnLi9fdG8taW9iamVjdCcpO1xuXG4vLyAyMi4xLjMuNCBBcnJheS5wcm90b3R5cGUuZW50cmllcygpXG4vLyAyMi4xLjMuMTMgQXJyYXkucHJvdG90eXBlLmtleXMoKVxuLy8gMjIuMS4zLjI5IEFycmF5LnByb3RvdHlwZS52YWx1ZXMoKVxuLy8gMjIuMS4zLjMwIEFycmF5LnByb3RvdHlwZVtAQGl0ZXJhdG9yXSgpXG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4vX2l0ZXItZGVmaW5lJykoQXJyYXksICdBcnJheScsIGZ1bmN0aW9uKGl0ZXJhdGVkLCBraW5kKXtcbiAgdGhpcy5fdCA9IHRvSU9iamVjdChpdGVyYXRlZCk7IC8vIHRhcmdldFxuICB0aGlzLl9pID0gMDsgICAgICAgICAgICAgICAgICAgLy8gbmV4dCBpbmRleFxuICB0aGlzLl9rID0ga2luZDsgICAgICAgICAgICAgICAgLy8ga2luZFxuLy8gMjIuMS41LjIuMSAlQXJyYXlJdGVyYXRvclByb3RvdHlwZSUubmV4dCgpXG59LCBmdW5jdGlvbigpe1xuICB2YXIgTyAgICAgPSB0aGlzLl90XG4gICAgLCBraW5kICA9IHRoaXMuX2tcbiAgICAsIGluZGV4ID0gdGhpcy5faSsrO1xuICBpZighTyB8fCBpbmRleCA+PSBPLmxlbmd0aCl7XG4gICAgdGhpcy5fdCA9IHVuZGVmaW5lZDtcbiAgICByZXR1cm4gc3RlcCgxKTtcbiAgfVxuICBpZihraW5kID09ICdrZXlzJyAgKXJldHVybiBzdGVwKDAsIGluZGV4KTtcbiAgaWYoa2luZCA9PSAndmFsdWVzJylyZXR1cm4gc3RlcCgwLCBPW2luZGV4XSk7XG4gIHJldHVybiBzdGVwKDAsIFtpbmRleCwgT1tpbmRleF1dKTtcbn0sICd2YWx1ZXMnKTtcblxuLy8gYXJndW1lbnRzTGlzdFtAQGl0ZXJhdG9yXSBpcyAlQXJyYXlQcm90b192YWx1ZXMlICg5LjQuNC42LCA5LjQuNC43KVxuSXRlcmF0b3JzLkFyZ3VtZW50cyA9IEl0ZXJhdG9ycy5BcnJheTtcblxuYWRkVG9VbnNjb3BhYmxlcygna2V5cycpO1xuYWRkVG9VbnNjb3BhYmxlcygndmFsdWVzJyk7XG5hZGRUb1Vuc2NvcGFibGVzKCdlbnRyaWVzJyk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5hcnJheS5pdGVyYXRvci5qc1xuLy8gbW9kdWxlIGlkID0gMTA0XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkgMTQiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5pbXBvcnQgTG9jYWxpemF0aW9uRXhjZXB0aW9uIGZyb20gJ0BhcHAvY2xkci9leGNlcHRpb24vbG9jYWxpemF0aW9uJztcblxuY2xhc3MgTnVtYmVyU3ltYm9sIHtcbiAgLyoqXG4gICAqIE51bWJlclN5bWJvbExpc3QgY29uc3RydWN0b3IuXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgZGVjaW1hbCBEZWNpbWFsIHNlcGFyYXRvciBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBncm91cCBEaWdpdHMgZ3JvdXAgc2VwYXJhdG9yIGNoYXJhY3RlclxuICAgKiBAcGFyYW0gc3RyaW5nIGxpc3QgTGlzdCBlbGVtZW50cyBzZXBhcmF0b3IgY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgcGVyY2VudFNpZ24gUGVyY2VudCBzaWduIGNoYXJhY3RlclxuICAgKiBAcGFyYW0gc3RyaW5nIG1pbnVzU2lnbiBNaW51cyBzaWduIGNoYXJhY3RlclxuICAgKiBAcGFyYW0gc3RyaW5nIHBsdXNTaWduIFBsdXMgc2lnbiBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBleHBvbmVudGlhbCBFeHBvbmVudGlhbCBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBzdXBlcnNjcmlwdGluZ0V4cG9uZW50IFN1cGVyc2NyaXB0aW5nIGV4cG9uZW50IGNoYXJhY3RlclxuICAgKiBAcGFyYW0gc3RyaW5nIHBlck1pbGxlIFBlcm1pbGxlIHNpZ24gY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgaW5maW5pdHkgVGhlIGluZmluaXR5IHNpZ24uIENvcnJlc3BvbmRzIHRvIHRoZSBJRUVFIGluZmluaXR5IGJpdCBwYXR0ZXJuLlxuICAgKiBAcGFyYW0gc3RyaW5nIG5hbiBUaGUgTmFOIChOb3QgQSBOdW1iZXIpIHNpZ24uIENvcnJlc3BvbmRzIHRvIHRoZSBJRUVFIE5hTiBiaXQgcGF0dGVybi5cbiAgICpcbiAgICogQHRocm93cyBMb2NhbGl6YXRpb25FeGNlcHRpb25cbiAgICovXG4gIGNvbnN0cnVjdG9yKFxuICAgIGRlY2ltYWwsXG4gICAgZ3JvdXAsXG4gICAgbGlzdCxcbiAgICBwZXJjZW50U2lnbixcbiAgICBtaW51c1NpZ24sXG4gICAgcGx1c1NpZ24sXG4gICAgZXhwb25lbnRpYWwsXG4gICAgc3VwZXJzY3JpcHRpbmdFeHBvbmVudCxcbiAgICBwZXJNaWxsZSxcbiAgICBpbmZpbml0eSxcbiAgICBuYW4sXG4gICkge1xuICAgIHRoaXMuZGVjaW1hbCA9IGRlY2ltYWw7XG4gICAgdGhpcy5ncm91cCA9IGdyb3VwO1xuICAgIHRoaXMubGlzdCA9IGxpc3Q7XG4gICAgdGhpcy5wZXJjZW50U2lnbiA9IHBlcmNlbnRTaWduO1xuICAgIHRoaXMubWludXNTaWduID0gbWludXNTaWduO1xuICAgIHRoaXMucGx1c1NpZ24gPSBwbHVzU2lnbjtcbiAgICB0aGlzLmV4cG9uZW50aWFsID0gZXhwb25lbnRpYWw7XG4gICAgdGhpcy5zdXBlcnNjcmlwdGluZ0V4cG9uZW50ID0gc3VwZXJzY3JpcHRpbmdFeHBvbmVudDtcbiAgICB0aGlzLnBlck1pbGxlID0gcGVyTWlsbGU7XG4gICAgdGhpcy5pbmZpbml0eSA9IGluZmluaXR5O1xuICAgIHRoaXMubmFuID0gbmFuO1xuXG4gICAgdGhpcy52YWxpZGF0ZURhdGEoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGRlY2ltYWwgc2VwYXJhdG9yLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0RGVjaW1hbCgpIHtcbiAgICByZXR1cm4gdGhpcy5kZWNpbWFsO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgZGlnaXQgZ3JvdXBzIHNlcGFyYXRvci5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldEdyb3VwKCkge1xuICAgIHJldHVybiB0aGlzLmdyb3VwO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgbGlzdCBlbGVtZW50cyBzZXBhcmF0b3IuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRMaXN0KCkge1xuICAgIHJldHVybiB0aGlzLmxpc3Q7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBwZXJjZW50IHNpZ24uXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRQZXJjZW50U2lnbigpIHtcbiAgICByZXR1cm4gdGhpcy5wZXJjZW50U2lnbjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIG1pbnVzIHNpZ24uXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRNaW51c1NpZ24oKSB7XG4gICAgcmV0dXJuIHRoaXMubWludXNTaWduO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgcGx1cyBzaWduLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0UGx1c1NpZ24oKSB7XG4gICAgcmV0dXJuIHRoaXMucGx1c1NpZ247XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBleHBvbmVudGlhbCBjaGFyYWN0ZXIuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRFeHBvbmVudGlhbCgpIHtcbiAgICByZXR1cm4gdGhpcy5leHBvbmVudGlhbDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGV4cG9uZW50IGNoYXJhY3Rlci5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldFN1cGVyc2NyaXB0aW5nRXhwb25lbnQoKSB7XG4gICAgcmV0dXJuIHRoaXMuc3VwZXJzY3JpcHRpbmdFeHBvbmVudDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXJ0IHRoZSBwZXIgbWlsbGUgc3ltYm9sIChvZnRlbiBcIuKAsFwiKS5cbiAgICpcbiAgICogQHNlZSBodHRwczovL2VuLndpa2lwZWRpYS5vcmcvd2lraS9QZXJfbWlsbGVcbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldFBlck1pbGxlKCkge1xuICAgIHJldHVybiB0aGlzLnBlck1pbGxlO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgaW5maW5pdHkgc3ltYm9sIChvZnRlbiBcIuKInlwiKS5cbiAgICpcbiAgICogQHNlZSBodHRwczovL2VuLndpa2lwZWRpYS5vcmcvd2lraS9JbmZpbml0eV9zeW1ib2xcbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldEluZmluaXR5KCkge1xuICAgIHJldHVybiB0aGlzLmluZmluaXR5O1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgTmFOIChub3QgYSBudW1iZXIpIHNpZ24uXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXROYW4oKSB7XG4gICAgcmV0dXJuIHRoaXMubmFuO1xuICB9XG5cbiAgLyoqXG4gICAqIFN5bWJvbHMgbGlzdCB2YWxpZGF0aW9uLlxuICAgKlxuICAgKiBAdGhyb3dzIExvY2FsaXphdGlvbkV4Y2VwdGlvblxuICAgKi9cbiAgdmFsaWRhdGVEYXRhKCkge1xuICAgIGlmICghdGhpcy5kZWNpbWFsIHx8IHR5cGVvZiB0aGlzLmRlY2ltYWwgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIGRlY2ltYWwnKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMuZ3JvdXAgfHwgdHlwZW9mIHRoaXMuZ3JvdXAgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIGdyb3VwJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLmxpc3QgfHwgdHlwZW9mIHRoaXMubGlzdCAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgc3ltYm9sIGxpc3QnKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMucGVyY2VudFNpZ24gfHwgdHlwZW9mIHRoaXMucGVyY2VudFNpZ24gIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHBlcmNlbnRTaWduJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLm1pbnVzU2lnbiB8fCB0eXBlb2YgdGhpcy5taW51c1NpZ24gIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIG1pbnVzU2lnbicpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5wbHVzU2lnbiB8fCB0eXBlb2YgdGhpcy5wbHVzU2lnbiAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgcGx1c1NpZ24nKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMuZXhwb25lbnRpYWwgfHwgdHlwZW9mIHRoaXMuZXhwb25lbnRpYWwgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIGV4cG9uZW50aWFsJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLnN1cGVyc2NyaXB0aW5nRXhwb25lbnQgfHwgdHlwZW9mIHRoaXMuc3VwZXJzY3JpcHRpbmdFeHBvbmVudCAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgc3VwZXJzY3JpcHRpbmdFeHBvbmVudCcpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5wZXJNaWxsZSB8fCB0eXBlb2YgdGhpcy5wZXJNaWxsZSAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgcGVyTWlsbGUnKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMuaW5maW5pdHkgfHwgdHlwZW9mIHRoaXMuaW5maW5pdHkgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIGluZmluaXR5Jyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLm5hbiB8fCB0eXBlb2YgdGhpcy5uYW4gIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIG5hbicpO1xuICAgIH1cbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBOdW1iZXJTeW1ib2w7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvY2xkci9udW1iZXItc3ltYm9sLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuaW1wb3J0IExvY2FsaXphdGlvbkV4Y2VwdGlvbiBmcm9tICdAYXBwL2NsZHIvZXhjZXB0aW9uL2xvY2FsaXphdGlvbic7XG5pbXBvcnQgTnVtYmVyU3ltYm9sIGZyb20gJ0BhcHAvY2xkci9udW1iZXItc3ltYm9sJztcblxuY2xhc3MgTnVtYmVyU3BlY2lmaWNhdGlvbiB7XG4gIC8qKlxuICAgKiBOdW1iZXIgc3BlY2lmaWNhdGlvbiBjb25zdHJ1Y3Rvci5cbiAgICpcbiAgICogQHBhcmFtIHN0cmluZyBwb3NpdGl2ZVBhdHRlcm4gQ0xEUiBmb3JtYXR0aW5nIHBhdHRlcm4gZm9yIHBvc2l0aXZlIGFtb3VudHNcbiAgICogQHBhcmFtIHN0cmluZyBuZWdhdGl2ZVBhdHRlcm4gQ0xEUiBmb3JtYXR0aW5nIHBhdHRlcm4gZm9yIG5lZ2F0aXZlIGFtb3VudHNcbiAgICogQHBhcmFtIE51bWJlclN5bWJvbCBzeW1ib2wgTnVtYmVyIHN5bWJvbFxuICAgKiBAcGFyYW0gaW50IG1heEZyYWN0aW9uRGlnaXRzIE1heGltdW0gbnVtYmVyIG9mIGRpZ2l0cyBhZnRlciBkZWNpbWFsIHNlcGFyYXRvclxuICAgKiBAcGFyYW0gaW50IG1pbkZyYWN0aW9uRGlnaXRzIE1pbmltdW0gbnVtYmVyIG9mIGRpZ2l0cyBhZnRlciBkZWNpbWFsIHNlcGFyYXRvclxuICAgKiBAcGFyYW0gYm9vbCBncm91cGluZ1VzZWQgSXMgZGlnaXRzIGdyb3VwaW5nIHVzZWQgP1xuICAgKiBAcGFyYW0gaW50IHByaW1hcnlHcm91cFNpemUgU2l6ZSBvZiBwcmltYXJ5IGRpZ2l0cyBncm91cCBpbiB0aGUgbnVtYmVyXG4gICAqIEBwYXJhbSBpbnQgc2Vjb25kYXJ5R3JvdXBTaXplIFNpemUgb2Ygc2Vjb25kYXJ5IGRpZ2l0cyBncm91cCBpbiB0aGUgbnVtYmVyXG4gICAqXG4gICAqIEB0aHJvd3MgTG9jYWxpemF0aW9uRXhjZXB0aW9uXG4gICAqL1xuICBjb25zdHJ1Y3RvcihcbiAgICBwb3NpdGl2ZVBhdHRlcm4sXG4gICAgbmVnYXRpdmVQYXR0ZXJuLFxuICAgIHN5bWJvbCxcbiAgICBtYXhGcmFjdGlvbkRpZ2l0cyxcbiAgICBtaW5GcmFjdGlvbkRpZ2l0cyxcbiAgICBncm91cGluZ1VzZWQsXG4gICAgcHJpbWFyeUdyb3VwU2l6ZSxcbiAgICBzZWNvbmRhcnlHcm91cFNpemUsXG4gICkge1xuICAgIHRoaXMucG9zaXRpdmVQYXR0ZXJuID0gcG9zaXRpdmVQYXR0ZXJuO1xuICAgIHRoaXMubmVnYXRpdmVQYXR0ZXJuID0gbmVnYXRpdmVQYXR0ZXJuO1xuICAgIHRoaXMuc3ltYm9sID0gc3ltYm9sO1xuXG4gICAgdGhpcy5tYXhGcmFjdGlvbkRpZ2l0cyA9IG1heEZyYWN0aW9uRGlnaXRzO1xuICAgIC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZVxuICAgIHRoaXMubWluRnJhY3Rpb25EaWdpdHMgPSBtYXhGcmFjdGlvbkRpZ2l0cyA8IG1pbkZyYWN0aW9uRGlnaXRzID8gbWF4RnJhY3Rpb25EaWdpdHMgOiBtaW5GcmFjdGlvbkRpZ2l0cztcblxuICAgIHRoaXMuZ3JvdXBpbmdVc2VkID0gZ3JvdXBpbmdVc2VkO1xuICAgIHRoaXMucHJpbWFyeUdyb3VwU2l6ZSA9IHByaW1hcnlHcm91cFNpemU7XG4gICAgdGhpcy5zZWNvbmRhcnlHcm91cFNpemUgPSBzZWNvbmRhcnlHcm91cFNpemU7XG5cbiAgICBpZiAoIXRoaXMucG9zaXRpdmVQYXR0ZXJuIHx8IHR5cGVvZiB0aGlzLnBvc2l0aXZlUGF0dGVybiAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgcG9zaXRpdmVQYXR0ZXJuJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLm5lZ2F0aXZlUGF0dGVybiB8fCB0eXBlb2YgdGhpcy5uZWdhdGl2ZVBhdHRlcm4gIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIG5lZ2F0aXZlUGF0dGVybicpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5zeW1ib2wgfHwgISh0aGlzLnN5bWJvbCBpbnN0YW5jZW9mIE51bWJlclN5bWJvbCkpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgc3ltYm9sJyk7XG4gICAgfVxuXG4gICAgaWYgKHR5cGVvZiB0aGlzLm1heEZyYWN0aW9uRGlnaXRzICE9PSAnbnVtYmVyJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBtYXhGcmFjdGlvbkRpZ2l0cycpO1xuICAgIH1cblxuICAgIGlmICh0eXBlb2YgdGhpcy5taW5GcmFjdGlvbkRpZ2l0cyAhPT0gJ251bWJlcicpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgbWluRnJhY3Rpb25EaWdpdHMnKTtcbiAgICB9XG5cbiAgICBpZiAodHlwZW9mIHRoaXMuZ3JvdXBpbmdVc2VkICE9PSAnYm9vbGVhbicpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgZ3JvdXBpbmdVc2VkJyk7XG4gICAgfVxuXG4gICAgaWYgKHR5cGVvZiB0aGlzLnByaW1hcnlHcm91cFNpemUgIT09ICdudW1iZXInKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHByaW1hcnlHcm91cFNpemUnKTtcbiAgICB9XG5cbiAgICBpZiAodHlwZW9mIHRoaXMuc2Vjb25kYXJ5R3JvdXBTaXplICE9PSAnbnVtYmVyJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBzZWNvbmRhcnlHcm91cFNpemUnKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogR2V0IHN5bWJvbC5cbiAgICpcbiAgICogQHJldHVybiBOdW1iZXJTeW1ib2xcbiAgICovXG4gIGdldFN5bWJvbCgpIHtcbiAgICByZXR1cm4gdGhpcy5zeW1ib2w7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBmb3JtYXR0aW5nIHJ1bGVzIGZvciB0aGlzIG51bWJlciAod2hlbiBwb3NpdGl2ZSkuXG4gICAqXG4gICAqIFRoaXMgcGF0dGVybiB1c2VzIHRoZSBVbmljb2RlIENMRFIgbnVtYmVyIHBhdHRlcm4gc3ludGF4XG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRQb3NpdGl2ZVBhdHRlcm4oKSB7XG4gICAgcmV0dXJuIHRoaXMucG9zaXRpdmVQYXR0ZXJuO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgZm9ybWF0dGluZyBydWxlcyBmb3IgdGhpcyBudW1iZXIgKHdoZW4gbmVnYXRpdmUpLlxuICAgKlxuICAgKiBUaGlzIHBhdHRlcm4gdXNlcyB0aGUgVW5pY29kZSBDTERSIG51bWJlciBwYXR0ZXJuIHN5bnRheFxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0TmVnYXRpdmVQYXR0ZXJuKCkge1xuICAgIHJldHVybiB0aGlzLm5lZ2F0aXZlUGF0dGVybjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIG1heGltdW0gbnVtYmVyIG9mIGRpZ2l0cyBhZnRlciBkZWNpbWFsIHNlcGFyYXRvciAocm91bmRpbmcgaWYgbmVlZGVkKS5cbiAgICpcbiAgICogQHJldHVybiBpbnRcbiAgICovXG4gIGdldE1heEZyYWN0aW9uRGlnaXRzKCkge1xuICAgIHJldHVybiB0aGlzLm1heEZyYWN0aW9uRGlnaXRzO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgbWluaW11bSBudW1iZXIgb2YgZGlnaXRzIGFmdGVyIGRlY2ltYWwgc2VwYXJhdG9yIChmaWxsIHdpdGggXCIwXCIgaWYgbmVlZGVkKS5cbiAgICpcbiAgICogQHJldHVybiBpbnRcbiAgICovXG4gIGdldE1pbkZyYWN0aW9uRGlnaXRzKCkge1xuICAgIHJldHVybiB0aGlzLm1pbkZyYWN0aW9uRGlnaXRzO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgXCJncm91cGluZ1wiIGZsYWcuIFRoaXMgZmxhZyBkZWZpbmVzIGlmIGRpZ2l0c1xuICAgKiBncm91cGluZyBzaG91bGQgYmUgdXNlZCB3aGVuIGZvcm1hdHRpbmcgdGhpcyBudW1iZXIuXG4gICAqXG4gICAqIEByZXR1cm4gYm9vbFxuICAgKi9cbiAgaXNHcm91cGluZ1VzZWQoKSB7XG4gICAgcmV0dXJuIHRoaXMuZ3JvdXBpbmdVc2VkO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgc2l6ZSBvZiBwcmltYXJ5IGRpZ2l0cyBncm91cCBpbiB0aGUgbnVtYmVyLlxuICAgKlxuICAgKiBAcmV0dXJuIGludFxuICAgKi9cbiAgZ2V0UHJpbWFyeUdyb3VwU2l6ZSgpIHtcbiAgICByZXR1cm4gdGhpcy5wcmltYXJ5R3JvdXBTaXplO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgc2l6ZSBvZiBzZWNvbmRhcnkgZGlnaXRzIGdyb3VwcyBpbiB0aGUgbnVtYmVyLlxuICAgKlxuICAgKiBAcmV0dXJuIGludFxuICAgKi9cbiAgZ2V0U2Vjb25kYXJ5R3JvdXBTaXplKCkge1xuICAgIHJldHVybiB0aGlzLnNlY29uZGFyeUdyb3VwU2l6ZTtcbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBOdW1iZXJTcGVjaWZpY2F0aW9uO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL2NsZHIvc3BlY2lmaWNhdGlvbnMvbnVtYmVyLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuY2xhc3MgTG9jYWxpemF0aW9uRXhjZXB0aW9uIHtcbiAgY29uc3RydWN0b3IobWVzc2FnZSkge1xuICAgIHRoaXMubWVzc2FnZSA9IG1lc3NhZ2U7XG4gICAgdGhpcy5uYW1lID0gJ0xvY2FsaXphdGlvbkV4Y2VwdGlvbic7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTG9jYWxpemF0aW9uRXhjZXB0aW9uO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL2NsZHIvZXhjZXB0aW9uL2xvY2FsaXphdGlvbi5qcyIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9zeW1ib2xcIiksIF9fZXNNb2R1bGU6IHRydWUgfTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL3N5bWJvbC5qc1xuLy8gbW9kdWxlIGlkID0gMTExXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vc3ltYm9sL2l0ZXJhdG9yXCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9zeW1ib2wvaXRlcmF0b3IuanNcbi8vIG1vZHVsZSBpZCA9IDExMlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczYuc3ltYm9sJyk7XG5yZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5vYmplY3QudG8tc3RyaW5nJyk7XG5yZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNy5zeW1ib2wuYXN5bmMtaXRlcmF0b3InKTtcbnJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM3LnN5bWJvbC5vYnNlcnZhYmxlJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4uLy4uL21vZHVsZXMvX2NvcmUnKS5TeW1ib2w7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9mbi9zeW1ib2wvaW5kZXguanNcbi8vIG1vZHVsZSBpZCA9IDExNVxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczYuc3RyaW5nLml0ZXJhdG9yJyk7XG5yZXF1aXJlKCcuLi8uLi9tb2R1bGVzL3dlYi5kb20uaXRlcmFibGUnKTtcbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9fd2tzLWV4dCcpLmYoJ2l0ZXJhdG9yJyk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9mbi9zeW1ib2wvaXRlcmF0b3IuanNcbi8vIG1vZHVsZSBpZCA9IDExNlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwiLy8gYWxsIGVudW1lcmFibGUgb2JqZWN0IGtleXMsIGluY2x1ZGVzIHN5bWJvbHNcbnZhciBnZXRLZXlzID0gcmVxdWlyZSgnLi9fb2JqZWN0LWtleXMnKVxuICAsIGdPUFMgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZ29wcycpXG4gICwgcElFICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1waWUnKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICB2YXIgcmVzdWx0ICAgICA9IGdldEtleXMoaXQpXG4gICAgLCBnZXRTeW1ib2xzID0gZ09QUy5mO1xuICBpZihnZXRTeW1ib2xzKXtcbiAgICB2YXIgc3ltYm9scyA9IGdldFN5bWJvbHMoaXQpXG4gICAgICAsIGlzRW51bSAgPSBwSUUuZlxuICAgICAgLCBpICAgICAgID0gMFxuICAgICAgLCBrZXk7XG4gICAgd2hpbGUoc3ltYm9scy5sZW5ndGggPiBpKWlmKGlzRW51bS5jYWxsKGl0LCBrZXkgPSBzeW1ib2xzW2krK10pKXJlc3VsdC5wdXNoKGtleSk7XG4gIH0gcmV0dXJuIHJlc3VsdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19lbnVtLWtleXMuanNcbi8vIG1vZHVsZSBpZCA9IDExN1xuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwiLy8gNy4yLjIgSXNBcnJheShhcmd1bWVudClcbnZhciBjb2YgPSByZXF1aXJlKCcuL19jb2YnKTtcbm1vZHVsZS5leHBvcnRzID0gQXJyYXkuaXNBcnJheSB8fCBmdW5jdGlvbiBpc0FycmF5KGFyZyl7XG4gIHJldHVybiBjb2YoYXJnKSA9PSAnQXJyYXknO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2lzLWFycmF5LmpzXG4vLyBtb2R1bGUgaWQgPSAxMThcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsInZhciBnZXRLZXlzICAgPSByZXF1aXJlKCcuL19vYmplY3Qta2V5cycpXG4gICwgdG9JT2JqZWN0ID0gcmVxdWlyZSgnLi9fdG8taW9iamVjdCcpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihvYmplY3QsIGVsKXtcbiAgdmFyIE8gICAgICA9IHRvSU9iamVjdChvYmplY3QpXG4gICAgLCBrZXlzICAgPSBnZXRLZXlzKE8pXG4gICAgLCBsZW5ndGggPSBrZXlzLmxlbmd0aFxuICAgICwgaW5kZXggID0gMFxuICAgICwga2V5O1xuICB3aGlsZShsZW5ndGggPiBpbmRleClpZihPW2tleSA9IGtleXNbaW5kZXgrK11dID09PSBlbClyZXR1cm4ga2V5O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2tleW9mLmpzXG4vLyBtb2R1bGUgaWQgPSAxMTlcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsInZhciBNRVRBICAgICA9IHJlcXVpcmUoJy4vX3VpZCcpKCdtZXRhJylcbiAgLCBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpXG4gICwgaGFzICAgICAgPSByZXF1aXJlKCcuL19oYXMnKVxuICAsIHNldERlc2MgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWRwJykuZlxuICAsIGlkICAgICAgID0gMDtcbnZhciBpc0V4dGVuc2libGUgPSBPYmplY3QuaXNFeHRlbnNpYmxlIHx8IGZ1bmN0aW9uKCl7XG4gIHJldHVybiB0cnVlO1xufTtcbnZhciBGUkVFWkUgPSAhcmVxdWlyZSgnLi9fZmFpbHMnKShmdW5jdGlvbigpe1xuICByZXR1cm4gaXNFeHRlbnNpYmxlKE9iamVjdC5wcmV2ZW50RXh0ZW5zaW9ucyh7fSkpO1xufSk7XG52YXIgc2V0TWV0YSA9IGZ1bmN0aW9uKGl0KXtcbiAgc2V0RGVzYyhpdCwgTUVUQSwge3ZhbHVlOiB7XG4gICAgaTogJ08nICsgKytpZCwgLy8gb2JqZWN0IElEXG4gICAgdzoge30gICAgICAgICAgLy8gd2VhayBjb2xsZWN0aW9ucyBJRHNcbiAgfX0pO1xufTtcbnZhciBmYXN0S2V5ID0gZnVuY3Rpb24oaXQsIGNyZWF0ZSl7XG4gIC8vIHJldHVybiBwcmltaXRpdmUgd2l0aCBwcmVmaXhcbiAgaWYoIWlzT2JqZWN0KGl0KSlyZXR1cm4gdHlwZW9mIGl0ID09ICdzeW1ib2wnID8gaXQgOiAodHlwZW9mIGl0ID09ICdzdHJpbmcnID8gJ1MnIDogJ1AnKSArIGl0O1xuICBpZighaGFzKGl0LCBNRVRBKSl7XG4gICAgLy8gY2FuJ3Qgc2V0IG1ldGFkYXRhIHRvIHVuY2F1Z2h0IGZyb3plbiBvYmplY3RcbiAgICBpZighaXNFeHRlbnNpYmxlKGl0KSlyZXR1cm4gJ0YnO1xuICAgIC8vIG5vdCBuZWNlc3NhcnkgdG8gYWRkIG1ldGFkYXRhXG4gICAgaWYoIWNyZWF0ZSlyZXR1cm4gJ0UnO1xuICAgIC8vIGFkZCBtaXNzaW5nIG1ldGFkYXRhXG4gICAgc2V0TWV0YShpdCk7XG4gIC8vIHJldHVybiBvYmplY3QgSURcbiAgfSByZXR1cm4gaXRbTUVUQV0uaTtcbn07XG52YXIgZ2V0V2VhayA9IGZ1bmN0aW9uKGl0LCBjcmVhdGUpe1xuICBpZighaGFzKGl0LCBNRVRBKSl7XG4gICAgLy8gY2FuJ3Qgc2V0IG1ldGFkYXRhIHRvIHVuY2F1Z2h0IGZyb3plbiBvYmplY3RcbiAgICBpZighaXNFeHRlbnNpYmxlKGl0KSlyZXR1cm4gdHJ1ZTtcbiAgICAvLyBub3QgbmVjZXNzYXJ5IHRvIGFkZCBtZXRhZGF0YVxuICAgIGlmKCFjcmVhdGUpcmV0dXJuIGZhbHNlO1xuICAgIC8vIGFkZCBtaXNzaW5nIG1ldGFkYXRhXG4gICAgc2V0TWV0YShpdCk7XG4gIC8vIHJldHVybiBoYXNoIHdlYWsgY29sbGVjdGlvbnMgSURzXG4gIH0gcmV0dXJuIGl0W01FVEFdLnc7XG59O1xuLy8gYWRkIG1ldGFkYXRhIG9uIGZyZWV6ZS1mYW1pbHkgbWV0aG9kcyBjYWxsaW5nXG52YXIgb25GcmVlemUgPSBmdW5jdGlvbihpdCl7XG4gIGlmKEZSRUVaRSAmJiBtZXRhLk5FRUQgJiYgaXNFeHRlbnNpYmxlKGl0KSAmJiAhaGFzKGl0LCBNRVRBKSlzZXRNZXRhKGl0KTtcbiAgcmV0dXJuIGl0O1xufTtcbnZhciBtZXRhID0gbW9kdWxlLmV4cG9ydHMgPSB7XG4gIEtFWTogICAgICBNRVRBLFxuICBORUVEOiAgICAgZmFsc2UsXG4gIGZhc3RLZXk6ICBmYXN0S2V5LFxuICBnZXRXZWFrOiAgZ2V0V2VhayxcbiAgb25GcmVlemU6IG9uRnJlZXplXG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fbWV0YS5qc1xuLy8gbW9kdWxlIGlkID0gMTIwXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkiLCIvLyBmYWxsYmFjayBmb3IgSUUxMSBidWdneSBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyB3aXRoIGlmcmFtZSBhbmQgd2luZG93XG52YXIgdG9JT2JqZWN0ID0gcmVxdWlyZSgnLi9fdG8taW9iamVjdCcpXG4gICwgZ09QTiAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWdvcG4nKS5mXG4gICwgdG9TdHJpbmcgID0ge30udG9TdHJpbmc7XG5cbnZhciB3aW5kb3dOYW1lcyA9IHR5cGVvZiB3aW5kb3cgPT0gJ29iamVjdCcgJiYgd2luZG93ICYmIE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzXG4gID8gT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXMod2luZG93KSA6IFtdO1xuXG52YXIgZ2V0V2luZG93TmFtZXMgPSBmdW5jdGlvbihpdCl7XG4gIHRyeSB7XG4gICAgcmV0dXJuIGdPUE4oaXQpO1xuICB9IGNhdGNoKGUpe1xuICAgIHJldHVybiB3aW5kb3dOYW1lcy5zbGljZSgpO1xuICB9XG59O1xuXG5tb2R1bGUuZXhwb3J0cy5mID0gZnVuY3Rpb24gZ2V0T3duUHJvcGVydHlOYW1lcyhpdCl7XG4gIHJldHVybiB3aW5kb3dOYW1lcyAmJiB0b1N0cmluZy5jYWxsKGl0KSA9PSAnW29iamVjdCBXaW5kb3ddJyA/IGdldFdpbmRvd05hbWVzKGl0KSA6IGdPUE4odG9JT2JqZWN0KGl0KSk7XG59O1xuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZ29wbi1leHQuanNcbi8vIG1vZHVsZSBpZCA9IDEyMVxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwiJ3VzZSBzdHJpY3QnO1xuLy8gRUNNQVNjcmlwdCA2IHN5bWJvbHMgc2hpbVxudmFyIGdsb2JhbCAgICAgICAgID0gcmVxdWlyZSgnLi9fZ2xvYmFsJylcbiAgLCBoYXMgICAgICAgICAgICA9IHJlcXVpcmUoJy4vX2hhcycpXG4gICwgREVTQ1JJUFRPUlMgICAgPSByZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpXG4gICwgJGV4cG9ydCAgICAgICAgPSByZXF1aXJlKCcuL19leHBvcnQnKVxuICAsIHJlZGVmaW5lICAgICAgID0gcmVxdWlyZSgnLi9fcmVkZWZpbmUnKVxuICAsIE1FVEEgICAgICAgICAgID0gcmVxdWlyZSgnLi9fbWV0YScpLktFWVxuICAsICRmYWlscyAgICAgICAgID0gcmVxdWlyZSgnLi9fZmFpbHMnKVxuICAsIHNoYXJlZCAgICAgICAgID0gcmVxdWlyZSgnLi9fc2hhcmVkJylcbiAgLCBzZXRUb1N0cmluZ1RhZyA9IHJlcXVpcmUoJy4vX3NldC10by1zdHJpbmctdGFnJylcbiAgLCB1aWQgICAgICAgICAgICA9IHJlcXVpcmUoJy4vX3VpZCcpXG4gICwgd2tzICAgICAgICAgICAgPSByZXF1aXJlKCcuL193a3MnKVxuICAsIHdrc0V4dCAgICAgICAgID0gcmVxdWlyZSgnLi9fd2tzLWV4dCcpXG4gICwgd2tzRGVmaW5lICAgICAgPSByZXF1aXJlKCcuL193a3MtZGVmaW5lJylcbiAgLCBrZXlPZiAgICAgICAgICA9IHJlcXVpcmUoJy4vX2tleW9mJylcbiAgLCBlbnVtS2V5cyAgICAgICA9IHJlcXVpcmUoJy4vX2VudW0ta2V5cycpXG4gICwgaXNBcnJheSAgICAgICAgPSByZXF1aXJlKCcuL19pcy1hcnJheScpXG4gICwgYW5PYmplY3QgICAgICAgPSByZXF1aXJlKCcuL19hbi1vYmplY3QnKVxuICAsIHRvSU9iamVjdCAgICAgID0gcmVxdWlyZSgnLi9fdG8taW9iamVjdCcpXG4gICwgdG9QcmltaXRpdmUgICAgPSByZXF1aXJlKCcuL190by1wcmltaXRpdmUnKVxuICAsIGNyZWF0ZURlc2MgICAgID0gcmVxdWlyZSgnLi9fcHJvcGVydHktZGVzYycpXG4gICwgX2NyZWF0ZSAgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtY3JlYXRlJylcbiAgLCBnT1BORXh0ICAgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1nb3BuLWV4dCcpXG4gICwgJEdPUEQgICAgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZ29wZCcpXG4gICwgJERQICAgICAgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZHAnKVxuICAsICRrZXlzICAgICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWtleXMnKVxuICAsIGdPUEQgICAgICAgICAgID0gJEdPUEQuZlxuICAsIGRQICAgICAgICAgICAgID0gJERQLmZcbiAgLCBnT1BOICAgICAgICAgICA9IGdPUE5FeHQuZlxuICAsICRTeW1ib2wgICAgICAgID0gZ2xvYmFsLlN5bWJvbFxuICAsICRKU09OICAgICAgICAgID0gZ2xvYmFsLkpTT05cbiAgLCBfc3RyaW5naWZ5ICAgICA9ICRKU09OICYmICRKU09OLnN0cmluZ2lmeVxuICAsIFBST1RPVFlQRSAgICAgID0gJ3Byb3RvdHlwZSdcbiAgLCBISURERU4gICAgICAgICA9IHdrcygnX2hpZGRlbicpXG4gICwgVE9fUFJJTUlUSVZFICAgPSB3a3MoJ3RvUHJpbWl0aXZlJylcbiAgLCBpc0VudW0gICAgICAgICA9IHt9LnByb3BlcnR5SXNFbnVtZXJhYmxlXG4gICwgU3ltYm9sUmVnaXN0cnkgPSBzaGFyZWQoJ3N5bWJvbC1yZWdpc3RyeScpXG4gICwgQWxsU3ltYm9scyAgICAgPSBzaGFyZWQoJ3N5bWJvbHMnKVxuICAsIE9QU3ltYm9scyAgICAgID0gc2hhcmVkKCdvcC1zeW1ib2xzJylcbiAgLCBPYmplY3RQcm90byAgICA9IE9iamVjdFtQUk9UT1RZUEVdXG4gICwgVVNFX05BVElWRSAgICAgPSB0eXBlb2YgJFN5bWJvbCA9PSAnZnVuY3Rpb24nXG4gICwgUU9iamVjdCAgICAgICAgPSBnbG9iYWwuUU9iamVjdDtcbi8vIERvbid0IHVzZSBzZXR0ZXJzIGluIFF0IFNjcmlwdCwgaHR0cHM6Ly9naXRodWIuY29tL3psb2lyb2NrL2NvcmUtanMvaXNzdWVzLzE3M1xudmFyIHNldHRlciA9ICFRT2JqZWN0IHx8ICFRT2JqZWN0W1BST1RPVFlQRV0gfHwgIVFPYmplY3RbUFJPVE9UWVBFXS5maW5kQ2hpbGQ7XG5cbi8vIGZhbGxiYWNrIGZvciBvbGQgQW5kcm9pZCwgaHR0cHM6Ly9jb2RlLmdvb2dsZS5jb20vcC92OC9pc3N1ZXMvZGV0YWlsP2lkPTY4N1xudmFyIHNldFN5bWJvbERlc2MgPSBERVNDUklQVE9SUyAmJiAkZmFpbHMoZnVuY3Rpb24oKXtcbiAgcmV0dXJuIF9jcmVhdGUoZFAoe30sICdhJywge1xuICAgIGdldDogZnVuY3Rpb24oKXsgcmV0dXJuIGRQKHRoaXMsICdhJywge3ZhbHVlOiA3fSkuYTsgfVxuICB9KSkuYSAhPSA3O1xufSkgPyBmdW5jdGlvbihpdCwga2V5LCBEKXtcbiAgdmFyIHByb3RvRGVzYyA9IGdPUEQoT2JqZWN0UHJvdG8sIGtleSk7XG4gIGlmKHByb3RvRGVzYylkZWxldGUgT2JqZWN0UHJvdG9ba2V5XTtcbiAgZFAoaXQsIGtleSwgRCk7XG4gIGlmKHByb3RvRGVzYyAmJiBpdCAhPT0gT2JqZWN0UHJvdG8pZFAoT2JqZWN0UHJvdG8sIGtleSwgcHJvdG9EZXNjKTtcbn0gOiBkUDtcblxudmFyIHdyYXAgPSBmdW5jdGlvbih0YWcpe1xuICB2YXIgc3ltID0gQWxsU3ltYm9sc1t0YWddID0gX2NyZWF0ZSgkU3ltYm9sW1BST1RPVFlQRV0pO1xuICBzeW0uX2sgPSB0YWc7XG4gIHJldHVybiBzeW07XG59O1xuXG52YXIgaXNTeW1ib2wgPSBVU0VfTkFUSVZFICYmIHR5cGVvZiAkU3ltYm9sLml0ZXJhdG9yID09ICdzeW1ib2wnID8gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gdHlwZW9mIGl0ID09ICdzeW1ib2wnO1xufSA6IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGl0IGluc3RhbmNlb2YgJFN5bWJvbDtcbn07XG5cbnZhciAkZGVmaW5lUHJvcGVydHkgPSBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0eShpdCwga2V5LCBEKXtcbiAgaWYoaXQgPT09IE9iamVjdFByb3RvKSRkZWZpbmVQcm9wZXJ0eShPUFN5bWJvbHMsIGtleSwgRCk7XG4gIGFuT2JqZWN0KGl0KTtcbiAga2V5ID0gdG9QcmltaXRpdmUoa2V5LCB0cnVlKTtcbiAgYW5PYmplY3QoRCk7XG4gIGlmKGhhcyhBbGxTeW1ib2xzLCBrZXkpKXtcbiAgICBpZighRC5lbnVtZXJhYmxlKXtcbiAgICAgIGlmKCFoYXMoaXQsIEhJRERFTikpZFAoaXQsIEhJRERFTiwgY3JlYXRlRGVzYygxLCB7fSkpO1xuICAgICAgaXRbSElEREVOXVtrZXldID0gdHJ1ZTtcbiAgICB9IGVsc2Uge1xuICAgICAgaWYoaGFzKGl0LCBISURERU4pICYmIGl0W0hJRERFTl1ba2V5XSlpdFtISURERU5dW2tleV0gPSBmYWxzZTtcbiAgICAgIEQgPSBfY3JlYXRlKEQsIHtlbnVtZXJhYmxlOiBjcmVhdGVEZXNjKDAsIGZhbHNlKX0pO1xuICAgIH0gcmV0dXJuIHNldFN5bWJvbERlc2MoaXQsIGtleSwgRCk7XG4gIH0gcmV0dXJuIGRQKGl0LCBrZXksIEQpO1xufTtcbnZhciAkZGVmaW5lUHJvcGVydGllcyA9IGZ1bmN0aW9uIGRlZmluZVByb3BlcnRpZXMoaXQsIFApe1xuICBhbk9iamVjdChpdCk7XG4gIHZhciBrZXlzID0gZW51bUtleXMoUCA9IHRvSU9iamVjdChQKSlcbiAgICAsIGkgICAgPSAwXG4gICAgLCBsID0ga2V5cy5sZW5ndGhcbiAgICAsIGtleTtcbiAgd2hpbGUobCA+IGkpJGRlZmluZVByb3BlcnR5KGl0LCBrZXkgPSBrZXlzW2krK10sIFBba2V5XSk7XG4gIHJldHVybiBpdDtcbn07XG52YXIgJGNyZWF0ZSA9IGZ1bmN0aW9uIGNyZWF0ZShpdCwgUCl7XG4gIHJldHVybiBQID09PSB1bmRlZmluZWQgPyBfY3JlYXRlKGl0KSA6ICRkZWZpbmVQcm9wZXJ0aWVzKF9jcmVhdGUoaXQpLCBQKTtcbn07XG52YXIgJHByb3BlcnR5SXNFbnVtZXJhYmxlID0gZnVuY3Rpb24gcHJvcGVydHlJc0VudW1lcmFibGUoa2V5KXtcbiAgdmFyIEUgPSBpc0VudW0uY2FsbCh0aGlzLCBrZXkgPSB0b1ByaW1pdGl2ZShrZXksIHRydWUpKTtcbiAgaWYodGhpcyA9PT0gT2JqZWN0UHJvdG8gJiYgaGFzKEFsbFN5bWJvbHMsIGtleSkgJiYgIWhhcyhPUFN5bWJvbHMsIGtleSkpcmV0dXJuIGZhbHNlO1xuICByZXR1cm4gRSB8fCAhaGFzKHRoaXMsIGtleSkgfHwgIWhhcyhBbGxTeW1ib2xzLCBrZXkpIHx8IGhhcyh0aGlzLCBISURERU4pICYmIHRoaXNbSElEREVOXVtrZXldID8gRSA6IHRydWU7XG59O1xudmFyICRnZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3IgPSBmdW5jdGlvbiBnZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3IoaXQsIGtleSl7XG4gIGl0ICA9IHRvSU9iamVjdChpdCk7XG4gIGtleSA9IHRvUHJpbWl0aXZlKGtleSwgdHJ1ZSk7XG4gIGlmKGl0ID09PSBPYmplY3RQcm90byAmJiBoYXMoQWxsU3ltYm9scywga2V5KSAmJiAhaGFzKE9QU3ltYm9scywga2V5KSlyZXR1cm47XG4gIHZhciBEID0gZ09QRChpdCwga2V5KTtcbiAgaWYoRCAmJiBoYXMoQWxsU3ltYm9scywga2V5KSAmJiAhKGhhcyhpdCwgSElEREVOKSAmJiBpdFtISURERU5dW2tleV0pKUQuZW51bWVyYWJsZSA9IHRydWU7XG4gIHJldHVybiBEO1xufTtcbnZhciAkZ2V0T3duUHJvcGVydHlOYW1lcyA9IGZ1bmN0aW9uIGdldE93blByb3BlcnR5TmFtZXMoaXQpe1xuICB2YXIgbmFtZXMgID0gZ09QTih0b0lPYmplY3QoaXQpKVxuICAgICwgcmVzdWx0ID0gW11cbiAgICAsIGkgICAgICA9IDBcbiAgICAsIGtleTtcbiAgd2hpbGUobmFtZXMubGVuZ3RoID4gaSl7XG4gICAgaWYoIWhhcyhBbGxTeW1ib2xzLCBrZXkgPSBuYW1lc1tpKytdKSAmJiBrZXkgIT0gSElEREVOICYmIGtleSAhPSBNRVRBKXJlc3VsdC5wdXNoKGtleSk7XG4gIH0gcmV0dXJuIHJlc3VsdDtcbn07XG52YXIgJGdldE93blByb3BlcnR5U3ltYm9scyA9IGZ1bmN0aW9uIGdldE93blByb3BlcnR5U3ltYm9scyhpdCl7XG4gIHZhciBJU19PUCAgPSBpdCA9PT0gT2JqZWN0UHJvdG9cbiAgICAsIG5hbWVzICA9IGdPUE4oSVNfT1AgPyBPUFN5bWJvbHMgOiB0b0lPYmplY3QoaXQpKVxuICAgICwgcmVzdWx0ID0gW11cbiAgICAsIGkgICAgICA9IDBcbiAgICAsIGtleTtcbiAgd2hpbGUobmFtZXMubGVuZ3RoID4gaSl7XG4gICAgaWYoaGFzKEFsbFN5bWJvbHMsIGtleSA9IG5hbWVzW2krK10pICYmIChJU19PUCA/IGhhcyhPYmplY3RQcm90bywga2V5KSA6IHRydWUpKXJlc3VsdC5wdXNoKEFsbFN5bWJvbHNba2V5XSk7XG4gIH0gcmV0dXJuIHJlc3VsdDtcbn07XG5cbi8vIDE5LjQuMS4xIFN5bWJvbChbZGVzY3JpcHRpb25dKVxuaWYoIVVTRV9OQVRJVkUpe1xuICAkU3ltYm9sID0gZnVuY3Rpb24gU3ltYm9sKCl7XG4gICAgaWYodGhpcyBpbnN0YW5jZW9mICRTeW1ib2wpdGhyb3cgVHlwZUVycm9yKCdTeW1ib2wgaXMgbm90IGEgY29uc3RydWN0b3IhJyk7XG4gICAgdmFyIHRhZyA9IHVpZChhcmd1bWVudHMubGVuZ3RoID4gMCA/IGFyZ3VtZW50c1swXSA6IHVuZGVmaW5lZCk7XG4gICAgdmFyICRzZXQgPSBmdW5jdGlvbih2YWx1ZSl7XG4gICAgICBpZih0aGlzID09PSBPYmplY3RQcm90bykkc2V0LmNhbGwoT1BTeW1ib2xzLCB2YWx1ZSk7XG4gICAgICBpZihoYXModGhpcywgSElEREVOKSAmJiBoYXModGhpc1tISURERU5dLCB0YWcpKXRoaXNbSElEREVOXVt0YWddID0gZmFsc2U7XG4gICAgICBzZXRTeW1ib2xEZXNjKHRoaXMsIHRhZywgY3JlYXRlRGVzYygxLCB2YWx1ZSkpO1xuICAgIH07XG4gICAgaWYoREVTQ1JJUFRPUlMgJiYgc2V0dGVyKXNldFN5bWJvbERlc2MoT2JqZWN0UHJvdG8sIHRhZywge2NvbmZpZ3VyYWJsZTogdHJ1ZSwgc2V0OiAkc2V0fSk7XG4gICAgcmV0dXJuIHdyYXAodGFnKTtcbiAgfTtcbiAgcmVkZWZpbmUoJFN5bWJvbFtQUk9UT1RZUEVdLCAndG9TdHJpbmcnLCBmdW5jdGlvbiB0b1N0cmluZygpe1xuICAgIHJldHVybiB0aGlzLl9rO1xuICB9KTtcblxuICAkR09QRC5mID0gJGdldE93blByb3BlcnR5RGVzY3JpcHRvcjtcbiAgJERQLmYgICA9ICRkZWZpbmVQcm9wZXJ0eTtcbiAgcmVxdWlyZSgnLi9fb2JqZWN0LWdvcG4nKS5mID0gZ09QTkV4dC5mID0gJGdldE93blByb3BlcnR5TmFtZXM7XG4gIHJlcXVpcmUoJy4vX29iamVjdC1waWUnKS5mICA9ICRwcm9wZXJ0eUlzRW51bWVyYWJsZTtcbiAgcmVxdWlyZSgnLi9fb2JqZWN0LWdvcHMnKS5mID0gJGdldE93blByb3BlcnR5U3ltYm9scztcblxuICBpZihERVNDUklQVE9SUyAmJiAhcmVxdWlyZSgnLi9fbGlicmFyeScpKXtcbiAgICByZWRlZmluZShPYmplY3RQcm90bywgJ3Byb3BlcnR5SXNFbnVtZXJhYmxlJywgJHByb3BlcnR5SXNFbnVtZXJhYmxlLCB0cnVlKTtcbiAgfVxuXG4gIHdrc0V4dC5mID0gZnVuY3Rpb24obmFtZSl7XG4gICAgcmV0dXJuIHdyYXAod2tzKG5hbWUpKTtcbiAgfVxufVxuXG4kZXhwb3J0KCRleHBvcnQuRyArICRleHBvcnQuVyArICRleHBvcnQuRiAqICFVU0VfTkFUSVZFLCB7U3ltYm9sOiAkU3ltYm9sfSk7XG5cbmZvcih2YXIgc3ltYm9scyA9IChcbiAgLy8gMTkuNC4yLjIsIDE5LjQuMi4zLCAxOS40LjIuNCwgMTkuNC4yLjYsIDE5LjQuMi44LCAxOS40LjIuOSwgMTkuNC4yLjEwLCAxOS40LjIuMTEsIDE5LjQuMi4xMiwgMTkuNC4yLjEzLCAxOS40LjIuMTRcbiAgJ2hhc0luc3RhbmNlLGlzQ29uY2F0U3ByZWFkYWJsZSxpdGVyYXRvcixtYXRjaCxyZXBsYWNlLHNlYXJjaCxzcGVjaWVzLHNwbGl0LHRvUHJpbWl0aXZlLHRvU3RyaW5nVGFnLHVuc2NvcGFibGVzJ1xuKS5zcGxpdCgnLCcpLCBpID0gMDsgc3ltYm9scy5sZW5ndGggPiBpOyApd2tzKHN5bWJvbHNbaSsrXSk7XG5cbmZvcih2YXIgc3ltYm9scyA9ICRrZXlzKHdrcy5zdG9yZSksIGkgPSAwOyBzeW1ib2xzLmxlbmd0aCA+IGk7ICl3a3NEZWZpbmUoc3ltYm9sc1tpKytdKTtcblxuJGV4cG9ydCgkZXhwb3J0LlMgKyAkZXhwb3J0LkYgKiAhVVNFX05BVElWRSwgJ1N5bWJvbCcsIHtcbiAgLy8gMTkuNC4yLjEgU3ltYm9sLmZvcihrZXkpXG4gICdmb3InOiBmdW5jdGlvbihrZXkpe1xuICAgIHJldHVybiBoYXMoU3ltYm9sUmVnaXN0cnksIGtleSArPSAnJylcbiAgICAgID8gU3ltYm9sUmVnaXN0cnlba2V5XVxuICAgICAgOiBTeW1ib2xSZWdpc3RyeVtrZXldID0gJFN5bWJvbChrZXkpO1xuICB9LFxuICAvLyAxOS40LjIuNSBTeW1ib2wua2V5Rm9yKHN5bSlcbiAga2V5Rm9yOiBmdW5jdGlvbiBrZXlGb3Ioa2V5KXtcbiAgICBpZihpc1N5bWJvbChrZXkpKXJldHVybiBrZXlPZihTeW1ib2xSZWdpc3RyeSwga2V5KTtcbiAgICB0aHJvdyBUeXBlRXJyb3Ioa2V5ICsgJyBpcyBub3QgYSBzeW1ib2whJyk7XG4gIH0sXG4gIHVzZVNldHRlcjogZnVuY3Rpb24oKXsgc2V0dGVyID0gdHJ1ZTsgfSxcbiAgdXNlU2ltcGxlOiBmdW5jdGlvbigpeyBzZXR0ZXIgPSBmYWxzZTsgfVxufSk7XG5cbiRleHBvcnQoJGV4cG9ydC5TICsgJGV4cG9ydC5GICogIVVTRV9OQVRJVkUsICdPYmplY3QnLCB7XG4gIC8vIDE5LjEuMi4yIE9iamVjdC5jcmVhdGUoTyBbLCBQcm9wZXJ0aWVzXSlcbiAgY3JlYXRlOiAkY3JlYXRlLFxuICAvLyAxOS4xLjIuNCBPYmplY3QuZGVmaW5lUHJvcGVydHkoTywgUCwgQXR0cmlidXRlcylcbiAgZGVmaW5lUHJvcGVydHk6ICRkZWZpbmVQcm9wZXJ0eSxcbiAgLy8gMTkuMS4yLjMgT2JqZWN0LmRlZmluZVByb3BlcnRpZXMoTywgUHJvcGVydGllcylcbiAgZGVmaW5lUHJvcGVydGllczogJGRlZmluZVByb3BlcnRpZXMsXG4gIC8vIDE5LjEuMi42IE9iamVjdC5nZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3IoTywgUClcbiAgZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yOiAkZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yLFxuICAvLyAxOS4xLjIuNyBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyhPKVxuICBnZXRPd25Qcm9wZXJ0eU5hbWVzOiAkZ2V0T3duUHJvcGVydHlOYW1lcyxcbiAgLy8gMTkuMS4yLjggT2JqZWN0LmdldE93blByb3BlcnR5U3ltYm9scyhPKVxuICBnZXRPd25Qcm9wZXJ0eVN5bWJvbHM6ICRnZXRPd25Qcm9wZXJ0eVN5bWJvbHNcbn0pO1xuXG4vLyAyNC4zLjIgSlNPTi5zdHJpbmdpZnkodmFsdWUgWywgcmVwbGFjZXIgWywgc3BhY2VdXSlcbiRKU09OICYmICRleHBvcnQoJGV4cG9ydC5TICsgJGV4cG9ydC5GICogKCFVU0VfTkFUSVZFIHx8ICRmYWlscyhmdW5jdGlvbigpe1xuICB2YXIgUyA9ICRTeW1ib2woKTtcbiAgLy8gTVMgRWRnZSBjb252ZXJ0cyBzeW1ib2wgdmFsdWVzIHRvIEpTT04gYXMge31cbiAgLy8gV2ViS2l0IGNvbnZlcnRzIHN5bWJvbCB2YWx1ZXMgdG8gSlNPTiBhcyBudWxsXG4gIC8vIFY4IHRocm93cyBvbiBib3hlZCBzeW1ib2xzXG4gIHJldHVybiBfc3RyaW5naWZ5KFtTXSkgIT0gJ1tudWxsXScgfHwgX3N0cmluZ2lmeSh7YTogU30pICE9ICd7fScgfHwgX3N0cmluZ2lmeShPYmplY3QoUykpICE9ICd7fSc7XG59KSksICdKU09OJywge1xuICBzdHJpbmdpZnk6IGZ1bmN0aW9uIHN0cmluZ2lmeShpdCl7XG4gICAgaWYoaXQgPT09IHVuZGVmaW5lZCB8fCBpc1N5bWJvbChpdCkpcmV0dXJuOyAvLyBJRTggcmV0dXJucyBzdHJpbmcgb24gdW5kZWZpbmVkXG4gICAgdmFyIGFyZ3MgPSBbaXRdXG4gICAgICAsIGkgICAgPSAxXG4gICAgICAsIHJlcGxhY2VyLCAkcmVwbGFjZXI7XG4gICAgd2hpbGUoYXJndW1lbnRzLmxlbmd0aCA+IGkpYXJncy5wdXNoKGFyZ3VtZW50c1tpKytdKTtcbiAgICByZXBsYWNlciA9IGFyZ3NbMV07XG4gICAgaWYodHlwZW9mIHJlcGxhY2VyID09ICdmdW5jdGlvbicpJHJlcGxhY2VyID0gcmVwbGFjZXI7XG4gICAgaWYoJHJlcGxhY2VyIHx8ICFpc0FycmF5KHJlcGxhY2VyKSlyZXBsYWNlciA9IGZ1bmN0aW9uKGtleSwgdmFsdWUpe1xuICAgICAgaWYoJHJlcGxhY2VyKXZhbHVlID0gJHJlcGxhY2VyLmNhbGwodGhpcywga2V5LCB2YWx1ZSk7XG4gICAgICBpZighaXNTeW1ib2wodmFsdWUpKXJldHVybiB2YWx1ZTtcbiAgICB9O1xuICAgIGFyZ3NbMV0gPSByZXBsYWNlcjtcbiAgICByZXR1cm4gX3N0cmluZ2lmeS5hcHBseSgkSlNPTiwgYXJncyk7XG4gIH1cbn0pO1xuXG4vLyAxOS40LjMuNCBTeW1ib2wucHJvdG90eXBlW0BAdG9QcmltaXRpdmVdKGhpbnQpXG4kU3ltYm9sW1BST1RPVFlQRV1bVE9fUFJJTUlUSVZFXSB8fCByZXF1aXJlKCcuL19oaWRlJykoJFN5bWJvbFtQUk9UT1RZUEVdLCBUT19QUklNSVRJVkUsICRTeW1ib2xbUFJPVE9UWVBFXS52YWx1ZU9mKTtcbi8vIDE5LjQuMy41IFN5bWJvbC5wcm90b3R5cGVbQEB0b1N0cmluZ1RhZ11cbnNldFRvU3RyaW5nVGFnKCRTeW1ib2wsICdTeW1ib2wnKTtcbi8vIDIwLjIuMS45IE1hdGhbQEB0b1N0cmluZ1RhZ11cbnNldFRvU3RyaW5nVGFnKE1hdGgsICdNYXRoJywgdHJ1ZSk7XG4vLyAyNC4zLjMgSlNPTltAQHRvU3RyaW5nVGFnXVxuc2V0VG9TdHJpbmdUYWcoZ2xvYmFsLkpTT04sICdKU09OJywgdHJ1ZSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5zeW1ib2wuanNcbi8vIG1vZHVsZSBpZCA9IDEyMlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwicmVxdWlyZSgnLi9fd2tzLWRlZmluZScpKCdhc3luY0l0ZXJhdG9yJyk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNy5zeW1ib2wuYXN5bmMtaXRlcmF0b3IuanNcbi8vIG1vZHVsZSBpZCA9IDEyM1xuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwicmVxdWlyZSgnLi9fd2tzLWRlZmluZScpKCdvYnNlcnZhYmxlJyk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNy5zeW1ib2wub2JzZXJ2YWJsZS5qc1xuLy8gbW9kdWxlIGlkID0gMTI0XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5pbXBvcnQgTG9jYWxpemF0aW9uRXhjZXB0aW9uIGZyb20gJ0BhcHAvY2xkci9leGNlcHRpb24vbG9jYWxpemF0aW9uJztcbmltcG9ydCBOdW1iZXJTcGVjaWZpY2F0aW9uIGZyb20gJ0BhcHAvY2xkci9zcGVjaWZpY2F0aW9ucy9udW1iZXInO1xuXG4vKipcbiAqIEN1cnJlbmN5IGRpc3BsYXkgb3B0aW9uOiBzeW1ib2wgbm90YXRpb24uXG4gKi9cbmNvbnN0IENVUlJFTkNZX0RJU1BMQVlfU1lNQk9MID0gJ3N5bWJvbCc7XG5cblxuY2xhc3MgUHJpY2VTcGVjaWZpY2F0aW9uIGV4dGVuZHMgTnVtYmVyU3BlY2lmaWNhdGlvbiB7XG4gIC8qKlxuICAgKiBQcmljZSBzcGVjaWZpY2F0aW9uIGNvbnN0cnVjdG9yLlxuICAgKlxuICAgKiBAcGFyYW0gc3RyaW5nIHBvc2l0aXZlUGF0dGVybiBDTERSIGZvcm1hdHRpbmcgcGF0dGVybiBmb3IgcG9zaXRpdmUgYW1vdW50c1xuICAgKiBAcGFyYW0gc3RyaW5nIG5lZ2F0aXZlUGF0dGVybiBDTERSIGZvcm1hdHRpbmcgcGF0dGVybiBmb3IgbmVnYXRpdmUgYW1vdW50c1xuICAgKiBAcGFyYW0gTnVtYmVyU3ltYm9sIHN5bWJvbCBOdW1iZXIgc3ltYm9sXG4gICAqIEBwYXJhbSBpbnQgbWF4RnJhY3Rpb25EaWdpdHMgTWF4aW11bSBudW1iZXIgb2YgZGlnaXRzIGFmdGVyIGRlY2ltYWwgc2VwYXJhdG9yXG4gICAqIEBwYXJhbSBpbnQgbWluRnJhY3Rpb25EaWdpdHMgTWluaW11bSBudW1iZXIgb2YgZGlnaXRzIGFmdGVyIGRlY2ltYWwgc2VwYXJhdG9yXG4gICAqIEBwYXJhbSBib29sIGdyb3VwaW5nVXNlZCBJcyBkaWdpdHMgZ3JvdXBpbmcgdXNlZCA/XG4gICAqIEBwYXJhbSBpbnQgcHJpbWFyeUdyb3VwU2l6ZSBTaXplIG9mIHByaW1hcnkgZGlnaXRzIGdyb3VwIGluIHRoZSBudW1iZXJcbiAgICogQHBhcmFtIGludCBzZWNvbmRhcnlHcm91cFNpemUgU2l6ZSBvZiBzZWNvbmRhcnkgZGlnaXRzIGdyb3VwIGluIHRoZSBudW1iZXJcbiAgICogQHBhcmFtIHN0cmluZyBjdXJyZW5jeVN5bWJvbCBDdXJyZW5jeSBzeW1ib2wgb2YgdGhpcyBwcmljZSAoZWcuIDog4oKsKVxuICAgKiBAcGFyYW0gY3VycmVuY3lDb2RlIEN1cnJlbmN5IGNvZGUgb2YgdGhpcyBwcmljZSAoZS5nLjogRVVSKVxuICAgKlxuICAgKiBAdGhyb3dzIExvY2FsaXphdGlvbkV4Y2VwdGlvblxuICAgKi9cbiAgY29uc3RydWN0b3IoXG4gICAgcG9zaXRpdmVQYXR0ZXJuLFxuICAgIG5lZ2F0aXZlUGF0dGVybixcbiAgICBzeW1ib2wsXG4gICAgbWF4RnJhY3Rpb25EaWdpdHMsXG4gICAgbWluRnJhY3Rpb25EaWdpdHMsXG4gICAgZ3JvdXBpbmdVc2VkLFxuICAgIHByaW1hcnlHcm91cFNpemUsXG4gICAgc2Vjb25kYXJ5R3JvdXBTaXplLFxuICAgIGN1cnJlbmN5U3ltYm9sLFxuICAgIGN1cnJlbmN5Q29kZSxcbiAgKSB7XG4gICAgc3VwZXIoXG4gICAgICBwb3NpdGl2ZVBhdHRlcm4sXG4gICAgICBuZWdhdGl2ZVBhdHRlcm4sXG4gICAgICBzeW1ib2wsXG4gICAgICBtYXhGcmFjdGlvbkRpZ2l0cyxcbiAgICAgIG1pbkZyYWN0aW9uRGlnaXRzLFxuICAgICAgZ3JvdXBpbmdVc2VkLFxuICAgICAgcHJpbWFyeUdyb3VwU2l6ZSxcbiAgICAgIHNlY29uZGFyeUdyb3VwU2l6ZSxcbiAgICApO1xuICAgIHRoaXMuY3VycmVuY3lTeW1ib2wgPSBjdXJyZW5jeVN5bWJvbDtcbiAgICB0aGlzLmN1cnJlbmN5Q29kZSA9IGN1cnJlbmN5Q29kZTtcblxuICAgIGlmICghdGhpcy5jdXJyZW5jeVN5bWJvbCB8fCB0eXBlb2YgdGhpcy5jdXJyZW5jeVN5bWJvbCAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgY3VycmVuY3lTeW1ib2wnKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMuY3VycmVuY3lDb2RlIHx8IHR5cGVvZiB0aGlzLmN1cnJlbmN5Q29kZSAhPT0gJ3N0cmluZycpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgY3VycmVuY3lDb2RlJyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0eXBlIG9mIGRpc3BsYXkgZm9yIGN1cnJlbmN5IHN5bWJvbC5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIHN0YXRpYyBnZXRDdXJyZW5jeURpc3BsYXkoKSB7XG4gICAgcmV0dXJuIENVUlJFTkNZX0RJU1BMQVlfU1lNQk9MO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgY3VycmVuY3kgc3ltYm9sXG4gICAqIGUuZy46IOKCrC5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldEN1cnJlbmN5U3ltYm9sKCkge1xuICAgIHJldHVybiB0aGlzLmN1cnJlbmN5U3ltYm9sO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgY3VycmVuY3kgSVNPIGNvZGVcbiAgICogZS5nLjogRVVSLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0Q3VycmVuY3lDb2RlKCkge1xuICAgIHJldHVybiB0aGlzLmN1cnJlbmN5Q29kZTtcbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBQcmljZVNwZWNpZmljYXRpb247XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvY2xkci9zcGVjaWZpY2F0aW9ucy9wcmljZS5qcyIsIi8vIGNoZWNrIG9uIGRlZmF1bHQgQXJyYXkgaXRlcmF0b3JcbnZhciBJdGVyYXRvcnMgID0gcmVxdWlyZSgnLi9faXRlcmF0b3JzJylcbiAgLCBJVEVSQVRPUiAgID0gcmVxdWlyZSgnLi9fd2tzJykoJ2l0ZXJhdG9yJylcbiAgLCBBcnJheVByb3RvID0gQXJyYXkucHJvdG90eXBlO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGl0ICE9PSB1bmRlZmluZWQgJiYgKEl0ZXJhdG9ycy5BcnJheSA9PT0gaXQgfHwgQXJyYXlQcm90b1tJVEVSQVRPUl0gPT09IGl0KTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pcy1hcnJheS1pdGVyLmpzXG4vLyBtb2R1bGUgaWQgPSAxMzVcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IiwiLy8gY2FsbCBzb21ldGhpbmcgb24gaXRlcmF0b3Igc3RlcCB3aXRoIHNhZmUgY2xvc2luZyBvbiBlcnJvclxudmFyIGFuT2JqZWN0ID0gcmVxdWlyZSgnLi9fYW4tb2JqZWN0Jyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0ZXJhdG9yLCBmbiwgdmFsdWUsIGVudHJpZXMpe1xuICB0cnkge1xuICAgIHJldHVybiBlbnRyaWVzID8gZm4oYW5PYmplY3QodmFsdWUpWzBdLCB2YWx1ZVsxXSkgOiBmbih2YWx1ZSk7XG4gIC8vIDcuNC42IEl0ZXJhdG9yQ2xvc2UoaXRlcmF0b3IsIGNvbXBsZXRpb24pXG4gIH0gY2F0Y2goZSl7XG4gICAgdmFyIHJldCA9IGl0ZXJhdG9yWydyZXR1cm4nXTtcbiAgICBpZihyZXQgIT09IHVuZGVmaW5lZClhbk9iamVjdChyZXQuY2FsbChpdGVyYXRvcikpO1xuICAgIHRocm93IGU7XG4gIH1cbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pdGVyLWNhbGwuanNcbi8vIG1vZHVsZSBpZCA9IDEzNlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUiLCJ2YXIgSVRFUkFUT1IgICAgID0gcmVxdWlyZSgnLi9fd2tzJykoJ2l0ZXJhdG9yJylcbiAgLCBTQUZFX0NMT1NJTkcgPSBmYWxzZTtcblxudHJ5IHtcbiAgdmFyIHJpdGVyID0gWzddW0lURVJBVE9SXSgpO1xuICByaXRlclsncmV0dXJuJ10gPSBmdW5jdGlvbigpeyBTQUZFX0NMT1NJTkcgPSB0cnVlOyB9O1xuICBBcnJheS5mcm9tKHJpdGVyLCBmdW5jdGlvbigpeyB0aHJvdyAyOyB9KTtcbn0gY2F0Y2goZSl7IC8qIGVtcHR5ICovIH1cblxubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihleGVjLCBza2lwQ2xvc2luZyl7XG4gIGlmKCFza2lwQ2xvc2luZyAmJiAhU0FGRV9DTE9TSU5HKXJldHVybiBmYWxzZTtcbiAgdmFyIHNhZmUgPSBmYWxzZTtcbiAgdHJ5IHtcbiAgICB2YXIgYXJyICA9IFs3XVxuICAgICAgLCBpdGVyID0gYXJyW0lURVJBVE9SXSgpO1xuICAgIGl0ZXIubmV4dCA9IGZ1bmN0aW9uKCl7IHJldHVybiB7ZG9uZTogc2FmZSA9IHRydWV9OyB9O1xuICAgIGFycltJVEVSQVRPUl0gPSBmdW5jdGlvbigpeyByZXR1cm4gaXRlcjsgfTtcbiAgICBleGVjKGFycik7XG4gIH0gY2F0Y2goZSl7IC8qIGVtcHR5ICovIH1cbiAgcmV0dXJuIHNhZmU7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXRlci1kZXRlY3QuanNcbi8vIG1vZHVsZSBpZCA9IDEzN1xuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vYXJyYXkvZnJvbVwiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvYXJyYXkvZnJvbS5qc1xuLy8gbW9kdWxlIGlkID0gMTQ0XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9nZXQtaXRlcmF0b3JcIiksIF9fZXNNb2R1bGU6IHRydWUgfTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL2dldC1pdGVyYXRvci5qc1xuLy8gbW9kdWxlIGlkID0gMTQ1XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgMTQiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vaXMtaXRlcmFibGVcIiksIF9fZXNNb2R1bGU6IHRydWUgfTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL2lzLWl0ZXJhYmxlLmpzXG4vLyBtb2R1bGUgaWQgPSAxNDZcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCAxNCIsIlwidXNlIHN0cmljdFwiO1xuXG5leHBvcnRzLl9fZXNNb2R1bGUgPSB0cnVlO1xuXG52YXIgX2lzSXRlcmFibGUyID0gcmVxdWlyZShcIi4uL2NvcmUtanMvaXMtaXRlcmFibGVcIik7XG5cbnZhciBfaXNJdGVyYWJsZTMgPSBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KF9pc0l0ZXJhYmxlMik7XG5cbnZhciBfZ2V0SXRlcmF0b3IyID0gcmVxdWlyZShcIi4uL2NvcmUtanMvZ2V0LWl0ZXJhdG9yXCIpO1xuXG52YXIgX2dldEl0ZXJhdG9yMyA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX2dldEl0ZXJhdG9yMik7XG5cbmZ1bmN0aW9uIF9pbnRlcm9wUmVxdWlyZURlZmF1bHQob2JqKSB7IHJldHVybiBvYmogJiYgb2JqLl9fZXNNb2R1bGUgPyBvYmogOiB7IGRlZmF1bHQ6IG9iaiB9OyB9XG5cbmV4cG9ydHMuZGVmYXVsdCA9IGZ1bmN0aW9uICgpIHtcbiAgZnVuY3Rpb24gc2xpY2VJdGVyYXRvcihhcnIsIGkpIHtcbiAgICB2YXIgX2FyciA9IFtdO1xuICAgIHZhciBfbiA9IHRydWU7XG4gICAgdmFyIF9kID0gZmFsc2U7XG4gICAgdmFyIF9lID0gdW5kZWZpbmVkO1xuXG4gICAgdHJ5IHtcbiAgICAgIGZvciAodmFyIF9pID0gKDAsIF9nZXRJdGVyYXRvcjMuZGVmYXVsdCkoYXJyKSwgX3M7ICEoX24gPSAoX3MgPSBfaS5uZXh0KCkpLmRvbmUpOyBfbiA9IHRydWUpIHtcbiAgICAgICAgX2Fyci5wdXNoKF9zLnZhbHVlKTtcblxuICAgICAgICBpZiAoaSAmJiBfYXJyLmxlbmd0aCA9PT0gaSkgYnJlYWs7XG4gICAgICB9XG4gICAgfSBjYXRjaCAoZXJyKSB7XG4gICAgICBfZCA9IHRydWU7XG4gICAgICBfZSA9IGVycjtcbiAgICB9IGZpbmFsbHkge1xuICAgICAgdHJ5IHtcbiAgICAgICAgaWYgKCFfbiAmJiBfaVtcInJldHVyblwiXSkgX2lbXCJyZXR1cm5cIl0oKTtcbiAgICAgIH0gZmluYWxseSB7XG4gICAgICAgIGlmIChfZCkgdGhyb3cgX2U7XG4gICAgICB9XG4gICAgfVxuXG4gICAgcmV0dXJuIF9hcnI7XG4gIH1cblxuICByZXR1cm4gZnVuY3Rpb24gKGFyciwgaSkge1xuICAgIGlmIChBcnJheS5pc0FycmF5KGFycikpIHtcbiAgICAgIHJldHVybiBhcnI7XG4gICAgfSBlbHNlIGlmICgoMCwgX2lzSXRlcmFibGUzLmRlZmF1bHQpKE9iamVjdChhcnIpKSkge1xuICAgICAgcmV0dXJuIHNsaWNlSXRlcmF0b3IoYXJyLCBpKTtcbiAgICB9IGVsc2Uge1xuICAgICAgdGhyb3cgbmV3IFR5cGVFcnJvcihcIkludmFsaWQgYXR0ZW1wdCB0byBkZXN0cnVjdHVyZSBub24taXRlcmFibGUgaW5zdGFuY2VcIik7XG4gICAgfVxuICB9O1xufSgpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvc2xpY2VkVG9BcnJheS5qc1xuLy8gbW9kdWxlIGlkID0gMTQ3XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgMTQiLCJyZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5zdHJpbmcuaXRlcmF0b3InKTtcbnJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM2LmFycmF5LmZyb20nKTtcbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9fY29yZScpLkFycmF5LmZyb207XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9mbi9hcnJheS9mcm9tLmpzXG4vLyBtb2R1bGUgaWQgPSAxNDhcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IiwicmVxdWlyZSgnLi4vbW9kdWxlcy93ZWIuZG9tLml0ZXJhYmxlJyk7XG5yZXF1aXJlKCcuLi9tb2R1bGVzL2VzNi5zdHJpbmcuaXRlcmF0b3InKTtcbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi4vbW9kdWxlcy9jb3JlLmdldC1pdGVyYXRvcicpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vZ2V0LWl0ZXJhdG9yLmpzXG4vLyBtb2R1bGUgaWQgPSAxNDlcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCAxNCIsInJlcXVpcmUoJy4uL21vZHVsZXMvd2ViLmRvbS5pdGVyYWJsZScpO1xucmVxdWlyZSgnLi4vbW9kdWxlcy9lczYuc3RyaW5nLml0ZXJhdG9yJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4uL21vZHVsZXMvY29yZS5pcy1pdGVyYWJsZScpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vaXMtaXRlcmFibGUuanNcbi8vIG1vZHVsZSBpZCA9IDE1MFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDE0IiwiJ3VzZSBzdHJpY3QnO1xudmFyICRkZWZpbmVQcm9wZXJ0eSA9IHJlcXVpcmUoJy4vX29iamVjdC1kcCcpXG4gICwgY3JlYXRlRGVzYyAgICAgID0gcmVxdWlyZSgnLi9fcHJvcGVydHktZGVzYycpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKG9iamVjdCwgaW5kZXgsIHZhbHVlKXtcbiAgaWYoaW5kZXggaW4gb2JqZWN0KSRkZWZpbmVQcm9wZXJ0eS5mKG9iamVjdCwgaW5kZXgsIGNyZWF0ZURlc2MoMCwgdmFsdWUpKTtcbiAgZWxzZSBvYmplY3RbaW5kZXhdID0gdmFsdWU7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY3JlYXRlLXByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAxNTFcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IiwidmFyIGFuT2JqZWN0ID0gcmVxdWlyZSgnLi9fYW4tb2JqZWN0JylcbiAgLCBnZXQgICAgICA9IHJlcXVpcmUoJy4vY29yZS5nZXQtaXRlcmF0b3ItbWV0aG9kJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4vX2NvcmUnKS5nZXRJdGVyYXRvciA9IGZ1bmN0aW9uKGl0KXtcbiAgdmFyIGl0ZXJGbiA9IGdldChpdCk7XG4gIGlmKHR5cGVvZiBpdGVyRm4gIT0gJ2Z1bmN0aW9uJyl0aHJvdyBUeXBlRXJyb3IoaXQgKyAnIGlzIG5vdCBpdGVyYWJsZSEnKTtcbiAgcmV0dXJuIGFuT2JqZWN0KGl0ZXJGbi5jYWxsKGl0KSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9jb3JlLmdldC1pdGVyYXRvci5qc1xuLy8gbW9kdWxlIGlkID0gMTUyXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgMTQiLCJ2YXIgY2xhc3NvZiAgID0gcmVxdWlyZSgnLi9fY2xhc3NvZicpXG4gICwgSVRFUkFUT1IgID0gcmVxdWlyZSgnLi9fd2tzJykoJ2l0ZXJhdG9yJylcbiAgLCBJdGVyYXRvcnMgPSByZXF1aXJlKCcuL19pdGVyYXRvcnMnKTtcbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi9fY29yZScpLmlzSXRlcmFibGUgPSBmdW5jdGlvbihpdCl7XG4gIHZhciBPID0gT2JqZWN0KGl0KTtcbiAgcmV0dXJuIE9bSVRFUkFUT1JdICE9PSB1bmRlZmluZWRcbiAgICB8fCAnQEBpdGVyYXRvcicgaW4gT1xuICAgIHx8IEl0ZXJhdG9ycy5oYXNPd25Qcm9wZXJ0eShjbGFzc29mKE8pKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2NvcmUuaXMtaXRlcmFibGUuanNcbi8vIG1vZHVsZSBpZCA9IDE1M1xuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDE0IiwiJ3VzZSBzdHJpY3QnO1xudmFyIGN0eCAgICAgICAgICAgID0gcmVxdWlyZSgnLi9fY3R4JylcbiAgLCAkZXhwb3J0ICAgICAgICA9IHJlcXVpcmUoJy4vX2V4cG9ydCcpXG4gICwgdG9PYmplY3QgICAgICAgPSByZXF1aXJlKCcuL190by1vYmplY3QnKVxuICAsIGNhbGwgICAgICAgICAgID0gcmVxdWlyZSgnLi9faXRlci1jYWxsJylcbiAgLCBpc0FycmF5SXRlciAgICA9IHJlcXVpcmUoJy4vX2lzLWFycmF5LWl0ZXInKVxuICAsIHRvTGVuZ3RoICAgICAgID0gcmVxdWlyZSgnLi9fdG8tbGVuZ3RoJylcbiAgLCBjcmVhdGVQcm9wZXJ0eSA9IHJlcXVpcmUoJy4vX2NyZWF0ZS1wcm9wZXJ0eScpXG4gICwgZ2V0SXRlckZuICAgICAgPSByZXF1aXJlKCcuL2NvcmUuZ2V0LWl0ZXJhdG9yLW1ldGhvZCcpO1xuXG4kZXhwb3J0KCRleHBvcnQuUyArICRleHBvcnQuRiAqICFyZXF1aXJlKCcuL19pdGVyLWRldGVjdCcpKGZ1bmN0aW9uKGl0ZXIpeyBBcnJheS5mcm9tKGl0ZXIpOyB9KSwgJ0FycmF5Jywge1xuICAvLyAyMi4xLjIuMSBBcnJheS5mcm9tKGFycmF5TGlrZSwgbWFwZm4gPSB1bmRlZmluZWQsIHRoaXNBcmcgPSB1bmRlZmluZWQpXG4gIGZyb206IGZ1bmN0aW9uIGZyb20oYXJyYXlMaWtlLyosIG1hcGZuID0gdW5kZWZpbmVkLCB0aGlzQXJnID0gdW5kZWZpbmVkKi8pe1xuICAgIHZhciBPICAgICAgID0gdG9PYmplY3QoYXJyYXlMaWtlKVxuICAgICAgLCBDICAgICAgID0gdHlwZW9mIHRoaXMgPT0gJ2Z1bmN0aW9uJyA/IHRoaXMgOiBBcnJheVxuICAgICAgLCBhTGVuICAgID0gYXJndW1lbnRzLmxlbmd0aFxuICAgICAgLCBtYXBmbiAgID0gYUxlbiA+IDEgPyBhcmd1bWVudHNbMV0gOiB1bmRlZmluZWRcbiAgICAgICwgbWFwcGluZyA9IG1hcGZuICE9PSB1bmRlZmluZWRcbiAgICAgICwgaW5kZXggICA9IDBcbiAgICAgICwgaXRlckZuICA9IGdldEl0ZXJGbihPKVxuICAgICAgLCBsZW5ndGgsIHJlc3VsdCwgc3RlcCwgaXRlcmF0b3I7XG4gICAgaWYobWFwcGluZyltYXBmbiA9IGN0eChtYXBmbiwgYUxlbiA+IDIgPyBhcmd1bWVudHNbMl0gOiB1bmRlZmluZWQsIDIpO1xuICAgIC8vIGlmIG9iamVjdCBpc24ndCBpdGVyYWJsZSBvciBpdCdzIGFycmF5IHdpdGggZGVmYXVsdCBpdGVyYXRvciAtIHVzZSBzaW1wbGUgY2FzZVxuICAgIGlmKGl0ZXJGbiAhPSB1bmRlZmluZWQgJiYgIShDID09IEFycmF5ICYmIGlzQXJyYXlJdGVyKGl0ZXJGbikpKXtcbiAgICAgIGZvcihpdGVyYXRvciA9IGl0ZXJGbi5jYWxsKE8pLCByZXN1bHQgPSBuZXcgQzsgIShzdGVwID0gaXRlcmF0b3IubmV4dCgpKS5kb25lOyBpbmRleCsrKXtcbiAgICAgICAgY3JlYXRlUHJvcGVydHkocmVzdWx0LCBpbmRleCwgbWFwcGluZyA/IGNhbGwoaXRlcmF0b3IsIG1hcGZuLCBbc3RlcC52YWx1ZSwgaW5kZXhdLCB0cnVlKSA6IHN0ZXAudmFsdWUpO1xuICAgICAgfVxuICAgIH0gZWxzZSB7XG4gICAgICBsZW5ndGggPSB0b0xlbmd0aChPLmxlbmd0aCk7XG4gICAgICBmb3IocmVzdWx0ID0gbmV3IEMobGVuZ3RoKTsgbGVuZ3RoID4gaW5kZXg7IGluZGV4Kyspe1xuICAgICAgICBjcmVhdGVQcm9wZXJ0eShyZXN1bHQsIGluZGV4LCBtYXBwaW5nID8gbWFwZm4oT1tpbmRleF0sIGluZGV4KSA6IE9baW5kZXhdKTtcbiAgICAgIH1cbiAgICB9XG4gICAgcmVzdWx0Lmxlbmd0aCA9IGluZGV4O1xuICAgIHJldHVybiByZXN1bHQ7XG4gIH1cbn0pO1xuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5hcnJheS5mcm9tLmpzXG4vLyBtb2R1bGUgaWQgPSAxNTRcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuaW1wb3J0IE51bWJlckZvcm1hdHRlciBmcm9tICdAYXBwL2NsZHIvbnVtYmVyLWZvcm1hdHRlcic7XG5pbXBvcnQgTnVtYmVyU3ltYm9sIGZyb20gJ0BhcHAvY2xkci9udW1iZXItc3ltYm9sJztcbmltcG9ydCBQcmljZVNwZWNpZmljYXRpb24gZnJvbSAnQGFwcC9jbGRyL3NwZWNpZmljYXRpb25zL3ByaWNlJztcbmltcG9ydCBOdW1iZXJTcGVjaWZpY2F0aW9uIGZyb20gJ0BhcHAvY2xkci9zcGVjaWZpY2F0aW9ucy9udW1iZXInO1xuXG5leHBvcnQge1xuICBQcmljZVNwZWNpZmljYXRpb24sXG4gIE51bWJlclNwZWNpZmljYXRpb24sXG4gIE51bWJlckZvcm1hdHRlcixcbiAgTnVtYmVyU3ltYm9sLFxufTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL2luZGV4LmpzIiwiXCJ1c2Ugc3RyaWN0XCI7XG5cbmV4cG9ydHMuX19lc01vZHVsZSA9IHRydWU7XG5cbnZhciBfZnJvbSA9IHJlcXVpcmUoXCIuLi9jb3JlLWpzL2FycmF5L2Zyb21cIik7XG5cbnZhciBfZnJvbTIgPSBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KF9mcm9tKTtcblxuZnVuY3Rpb24gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChvYmopIHsgcmV0dXJuIG9iaiAmJiBvYmouX19lc01vZHVsZSA/IG9iaiA6IHsgZGVmYXVsdDogb2JqIH07IH1cblxuZXhwb3J0cy5kZWZhdWx0ID0gZnVuY3Rpb24gKGFycikge1xuICBpZiAoQXJyYXkuaXNBcnJheShhcnIpKSB7XG4gICAgZm9yICh2YXIgaSA9IDAsIGFycjIgPSBBcnJheShhcnIubGVuZ3RoKTsgaSA8IGFyci5sZW5ndGg7IGkrKykge1xuICAgICAgYXJyMltpXSA9IGFycltpXTtcbiAgICB9XG5cbiAgICByZXR1cm4gYXJyMjtcbiAgfSBlbHNlIHtcbiAgICByZXR1cm4gKDAsIF9mcm9tMi5kZWZhdWx0KShhcnIpO1xuICB9XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvdG9Db25zdW1hYmxlQXJyYXkuanNcbi8vIG1vZHVsZSBpZCA9IDE1OFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuLyoqXG4gKiBUaGVzZSBwbGFjZWhvbGRlcnMgYXJlIHVzZWQgaW4gQ0xEUiBudW1iZXIgZm9ybWF0dGluZyB0ZW1wbGF0ZXMuXG4gKiBUaGV5IGFyZSBtZWFudCB0byBiZSByZXBsYWNlZCBieSB0aGUgY29ycmVjdCBsb2NhbGl6ZWQgc3ltYm9scyBpbiB0aGUgbnVtYmVyIGZvcm1hdHRpbmcgcHJvY2Vzcy5cbiAqL1xuaW1wb3J0IE51bWJlclN5bWJvbCBmcm9tICdAYXBwL2NsZHIvbnVtYmVyLXN5bWJvbCc7XG5pbXBvcnQgUHJpY2VTcGVjaWZpY2F0aW9uIGZyb20gJ0BhcHAvY2xkci9zcGVjaWZpY2F0aW9ucy9wcmljZSc7XG5pbXBvcnQgTnVtYmVyU3BlY2lmaWNhdGlvbiBmcm9tICdAYXBwL2NsZHIvc3BlY2lmaWNhdGlvbnMvbnVtYmVyJztcblxuY29uc3QgZXNjYXBlUkUgPSByZXF1aXJlKCdsb2Rhc2guZXNjYXBlcmVnZXhwJyk7XG5cbmNvbnN0IENVUlJFTkNZX1NZTUJPTF9QTEFDRUhPTERFUiA9ICfCpCc7XG5jb25zdCBERUNJTUFMX1NFUEFSQVRPUl9QTEFDRUhPTERFUiA9ICcuJztcbmNvbnN0IEdST1VQX1NFUEFSQVRPUl9QTEFDRUhPTERFUiA9ICcsJztcbmNvbnN0IE1JTlVTX1NJR05fUExBQ0VIT0xERVIgPSAnLSc7XG5jb25zdCBQRVJDRU5UX1NZTUJPTF9QTEFDRUhPTERFUiA9ICclJztcbmNvbnN0IFBMVVNfU0lHTl9QTEFDRUhPTERFUiA9ICcrJztcblxuY2xhc3MgTnVtYmVyRm9ybWF0dGVyIHtcbiAgLyoqXG4gICAqIEBwYXJhbSBOdW1iZXJTcGVjaWZpY2F0aW9uIHNwZWNpZmljYXRpb24gTnVtYmVyIHNwZWNpZmljYXRpb24gdG8gYmUgdXNlZFxuICAgKiAgIChjYW4gYmUgYSBudW1iZXIgc3BlYywgYSBwcmljZSBzcGVjLCBhIHBlcmNlbnRhZ2Ugc3BlYylcbiAgICovXG4gIGNvbnN0cnVjdG9yKHNwZWNpZmljYXRpb24pIHtcbiAgICB0aGlzLm51bWJlclNwZWNpZmljYXRpb24gPSBzcGVjaWZpY2F0aW9uO1xuICB9XG5cbiAgLyoqXG4gICAqIEZvcm1hdHMgdGhlIHBhc3NlZCBudW1iZXIgYWNjb3JkaW5nIHRvIHNwZWNpZmljYXRpb25zLlxuICAgKlxuICAgKiBAcGFyYW0gaW50fGZsb2F0fHN0cmluZyBudW1iZXIgVGhlIG51bWJlciB0byBmb3JtYXRcbiAgICogQHBhcmFtIE51bWJlclNwZWNpZmljYXRpb24gc3BlY2lmaWNhdGlvbiBOdW1iZXIgc3BlY2lmaWNhdGlvbiB0byBiZSB1c2VkXG4gICAqICAgKGNhbiBiZSBhIG51bWJlciBzcGVjLCBhIHByaWNlIHNwZWMsIGEgcGVyY2VudGFnZSBzcGVjKVxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZyBUaGUgZm9ybWF0dGVkIG51bWJlclxuICAgKiAgICAgICAgICAgICAgICBZb3Ugc2hvdWxkIHVzZSB0aGlzIHRoaXMgdmFsdWUgZm9yIGRpc3BsYXksIHdpdGhvdXQgbW9kaWZ5aW5nIGl0XG4gICAqL1xuICBmb3JtYXQobnVtYmVyLCBzcGVjaWZpY2F0aW9uKSB7XG4gICAgaWYgKHNwZWNpZmljYXRpb24gIT09IHVuZGVmaW5lZCkge1xuICAgICAgdGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uID0gc3BlY2lmaWNhdGlvbjtcbiAgICB9XG5cbiAgICAvKlxuICAgICAqIFdlIG5lZWQgdG8gd29yayBvbiB0aGUgYWJzb2x1dGUgdmFsdWUgZmlyc3QuXG4gICAgICogVGhlbiB0aGUgQ0xEUiBwYXR0ZXJuIHdpbGwgYWRkIHRoZSBzaWduIGlmIHJlbGV2YW50IChhdCB0aGUgZW5kKS5cbiAgICAgKi9cbiAgICBjb25zdCBudW0gPSBNYXRoLmFicyhudW1iZXIpLnRvRml4ZWQodGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmdldE1heEZyYWN0aW9uRGlnaXRzKCkpO1xuXG4gICAgbGV0IFttYWpvckRpZ2l0cywgbWlub3JEaWdpdHNdID0gdGhpcy5leHRyYWN0TWFqb3JNaW5vckRpZ2l0cyhudW0pO1xuICAgIG1ham9yRGlnaXRzID0gdGhpcy5zcGxpdE1ham9yR3JvdXBzKG1ham9yRGlnaXRzKTtcbiAgICBtaW5vckRpZ2l0cyA9IHRoaXMuYWRqdXN0TWlub3JEaWdpdHNaZXJvZXMobWlub3JEaWdpdHMpO1xuXG4gICAgLy8gQXNzZW1ibGUgdGhlIGZpbmFsIG51bWJlclxuICAgIGxldCBmb3JtYXR0ZWROdW1iZXIgPSBtYWpvckRpZ2l0cztcbiAgICBpZiAobWlub3JEaWdpdHMpIHtcbiAgICAgIGZvcm1hdHRlZE51bWJlciArPSBERUNJTUFMX1NFUEFSQVRPUl9QTEFDRUhPTERFUiArIG1pbm9yRGlnaXRzO1xuICAgIH1cblxuICAgIC8vIEdldCB0aGUgZ29vZCBDTERSIGZvcm1hdHRpbmcgcGF0dGVybi4gU2lnbiBpcyBpbXBvcnRhbnQgaGVyZSAhXG4gICAgY29uc3QgcGF0dGVybiA9IHRoaXMuZ2V0Q2xkclBhdHRlcm4obnVtYmVyIDwgMCk7XG4gICAgZm9ybWF0dGVkTnVtYmVyID0gdGhpcy5hZGRQbGFjZWhvbGRlcnMoZm9ybWF0dGVkTnVtYmVyLCBwYXR0ZXJuKTtcbiAgICBmb3JtYXR0ZWROdW1iZXIgPSB0aGlzLnJlcGxhY2VTeW1ib2xzKGZvcm1hdHRlZE51bWJlcik7XG5cbiAgICBmb3JtYXR0ZWROdW1iZXIgPSB0aGlzLnBlcmZvcm1TcGVjaWZpY1JlcGxhY2VtZW50cyhmb3JtYXR0ZWROdW1iZXIpO1xuXG4gICAgcmV0dXJuIGZvcm1hdHRlZE51bWJlcjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgbnVtYmVyJ3MgbWFqb3IgYW5kIG1pbm9yIGRpZ2l0cy5cbiAgICpcbiAgICogTWFqb3IgZGlnaXRzIGFyZSB0aGUgXCJpbnRlZ2VyXCIgcGFydCAoYmVmb3JlIGRlY2ltYWwgc2VwYXJhdG9yKSxcbiAgICogbWlub3IgZGlnaXRzIGFyZSB0aGUgZnJhY3Rpb25hbCBwYXJ0XG4gICAqIFJlc3VsdCB3aWxsIGJlIGFuIGFycmF5IG9mIGV4YWN0bHkgMiBpdGVtczogW21ham9yRGlnaXRzLCBtaW5vckRpZ2l0c11cbiAgICpcbiAgICogVXNhZ2UgZXhhbXBsZTpcbiAgICogIGxpc3QobWFqb3JEaWdpdHMsIG1pbm9yRGlnaXRzKSA9IHRoaXMuZ2V0TWFqb3JNaW5vckRpZ2l0cyhkZWNpbWFsTnVtYmVyKTtcbiAgICpcbiAgICogQHBhcmFtIERlY2ltYWxOdW1iZXIgbnVtYmVyXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nW11cbiAgICovXG4gIGV4dHJhY3RNYWpvck1pbm9yRGlnaXRzKG51bWJlcikge1xuICAgIC8vIEdldCB0aGUgbnVtYmVyJ3MgbWFqb3IgYW5kIG1pbm9yIGRpZ2l0cy5cbiAgICBjb25zdCByZXN1bHQgPSBudW1iZXIudG9TdHJpbmcoKS5zcGxpdCgnLicpO1xuICAgIGNvbnN0IG1ham9yRGlnaXRzID0gcmVzdWx0WzBdO1xuICAgIGNvbnN0IG1pbm9yRGlnaXRzID0gKHJlc3VsdFsxXSA9PT0gdW5kZWZpbmVkKSA/ICcnIDogcmVzdWx0WzFdO1xuICAgIHJldHVybiBbbWFqb3JEaWdpdHMsIG1pbm9yRGlnaXRzXTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTcGxpdHMgbWFqb3IgZGlnaXRzIGludG8gZ3JvdXBzLlxuICAgKlxuICAgKiBlLmcuOiBHaXZlbiB0aGUgbWFqb3IgZGlnaXRzIFwiMTIzNDU2N1wiLCBhbmQgbWFqb3IgZ3JvdXAgc2l6ZVxuICAgKiAgY29uZmlndXJlZCB0byAzIGRpZ2l0cywgdGhlIHJlc3VsdCB3b3VsZCBiZSBcIjEgMjM0IDU2N1wiXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgbWFqb3JEaWdpdHMgVGhlIG1ham9yIGRpZ2l0cyB0byBiZSBncm91cGVkXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nIFRoZSBncm91cGVkIG1ham9yIGRpZ2l0c1xuICAgKi9cbiAgc3BsaXRNYWpvckdyb3VwcyhkaWdpdCkge1xuICAgIGlmICghdGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmlzR3JvdXBpbmdVc2VkKCkpIHtcbiAgICAgIHJldHVybiBkaWdpdDtcbiAgICB9XG5cbiAgICAvLyBSZXZlcnNlIHRoZSBtYWpvciBkaWdpdHMsIHNpbmNlIHRoZXkgYXJlIGdyb3VwZWQgZnJvbSB0aGUgcmlnaHQuXG4gICAgY29uc3QgbWFqb3JEaWdpdHMgPSBkaWdpdC5zcGxpdCgnJykucmV2ZXJzZSgpO1xuXG4gICAgLy8gR3JvdXAgdGhlIG1ham9yIGRpZ2l0cy5cbiAgICBsZXQgZ3JvdXBzID0gW107XG4gICAgZ3JvdXBzLnB1c2gobWFqb3JEaWdpdHMuc3BsaWNlKDAsIHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRQcmltYXJ5R3JvdXBTaXplKCkpKTtcbiAgICB3aGlsZSAobWFqb3JEaWdpdHMubGVuZ3RoKSB7XG4gICAgICBncm91cHMucHVzaChtYWpvckRpZ2l0cy5zcGxpY2UoMCwgdGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmdldFNlY29uZGFyeUdyb3VwU2l6ZSgpKSk7XG4gICAgfVxuXG4gICAgLy8gUmV2ZXJzZSBiYWNrIHRoZSBkaWdpdHMgYW5kIHRoZSBncm91cHNcbiAgICBncm91cHMgPSBncm91cHMucmV2ZXJzZSgpO1xuICAgIGNvbnN0IG5ld0dyb3VwcyA9IFtdO1xuICAgIGdyb3Vwcy5mb3JFYWNoKChncm91cCkgPT4ge1xuICAgICAgbmV3R3JvdXBzLnB1c2goZ3JvdXAucmV2ZXJzZSgpLmpvaW4oJycpKTtcbiAgICB9KTtcblxuICAgIC8vIFJlY29uc3RydWN0IHRoZSBtYWpvciBkaWdpdHMuXG4gICAgcmV0dXJuIG5ld0dyb3Vwcy5qb2luKEdST1VQX1NFUEFSQVRPUl9QTEFDRUhPTERFUik7XG4gIH1cblxuICAvKipcbiAgICogQWRkcyBvciByZW1vdmUgdHJhaWxpbmcgemVyb2VzLCBkZXBlbmRpbmcgb24gc3BlY2lmaWVkIG1pbiBhbmQgbWF4IGZyYWN0aW9uIGRpZ2l0cyBudW1iZXJzLlxuICAgKlxuICAgKiBAcGFyYW0gc3RyaW5nIG1pbm9yRGlnaXRzIERpZ2l0cyB0byBiZSBhZGp1c3RlZCB3aXRoICh0cmltbWVkIG9yIHBhZGRlZCkgemVyb2VzXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nIFRoZSBhZGp1c3RlZCBtaW5vciBkaWdpdHNcbiAgICovXG4gIGFkanVzdE1pbm9yRGlnaXRzWmVyb2VzKG1pbm9yRGlnaXRzKSB7XG4gICAgbGV0IGRpZ2l0ID0gbWlub3JEaWdpdHM7XG4gICAgaWYgKGRpZ2l0Lmxlbmd0aCA+IHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRNYXhGcmFjdGlvbkRpZ2l0cygpKSB7XG4gICAgICAvLyBTdHJpcCBhbnkgdHJhaWxpbmcgemVyb2VzLlxuICAgICAgZGlnaXQgPSBkaWdpdC5yZXBsYWNlKC8wKyQvLCAnJyk7XG4gICAgfVxuXG4gICAgaWYgKGRpZ2l0Lmxlbmd0aCA8IHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRNaW5GcmFjdGlvbkRpZ2l0cygpKSB7XG4gICAgICAvLyBSZS1hZGQgbmVlZGVkIHplcm9lc1xuICAgICAgZGlnaXQgPSBkaWdpdC5wYWRFbmQoXG4gICAgICAgIHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRNaW5GcmFjdGlvbkRpZ2l0cygpLFxuICAgICAgICAnMCcsXG4gICAgICApO1xuICAgIH1cblxuICAgIHJldHVybiBkaWdpdDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuLlxuICAgKlxuICAgKiBAc2VlIGh0dHA6Ly9jbGRyLnVuaWNvZGUub3JnL3RyYW5zbGF0aW9uL251bWJlci1wYXR0ZXJuc1xuICAgKlxuICAgKiBAcGFyYW0gYm9vbCBpc05lZ2F0aXZlIElmIHRydWUsIHRoZSBuZWdhdGl2ZSBwYXR0ZXJuXG4gICAqIHdpbGwgYmUgcmV0dXJuZWQgaW5zdGVhZCBvZiB0aGUgcG9zaXRpdmUgb25lXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nIFRoZSBDTERSIGZvcm1hdHRpbmcgcGF0dGVyblxuICAgKi9cbiAgZ2V0Q2xkclBhdHRlcm4oaXNOZWdhdGl2ZSkge1xuICAgIGlmIChpc05lZ2F0aXZlKSB7XG4gICAgICByZXR1cm4gdGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmdldE5lZ2F0aXZlUGF0dGVybigpO1xuICAgIH1cblxuICAgIHJldHVybiB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0UG9zaXRpdmVQYXR0ZXJuKCk7XG4gIH1cblxuICAvKipcbiAgICogUmVwbGFjZSBwbGFjZWhvbGRlciBudW1iZXIgc3ltYm9scyB3aXRoIHJlbGV2YW50IG51bWJlcmluZyBzeXN0ZW0ncyBzeW1ib2xzLlxuICAgKlxuICAgKiBAcGFyYW0gc3RyaW5nIG51bWJlclxuICAgKiAgICAgICAgICAgICAgICAgICAgICAgVGhlIG51bWJlciB0byBwcm9jZXNzXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqICAgICAgICAgICAgICAgIFRoZSBudW1iZXIgd2l0aCByZXBsYWNlZCBzeW1ib2xzXG4gICAqL1xuICByZXBsYWNlU3ltYm9scyhudW1iZXIpIHtcbiAgICBjb25zdCBzeW1ib2xzID0gdGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmdldFN5bWJvbCgpO1xuXG4gICAgY29uc3QgbWFwID0ge307XG4gICAgbWFwW0RFQ0lNQUxfU0VQQVJBVE9SX1BMQUNFSE9MREVSXSA9IHN5bWJvbHMuZ2V0RGVjaW1hbCgpO1xuICAgIG1hcFtHUk9VUF9TRVBBUkFUT1JfUExBQ0VIT0xERVJdID0gc3ltYm9scy5nZXRHcm91cCgpO1xuICAgIG1hcFtNSU5VU19TSUdOX1BMQUNFSE9MREVSXSA9IHN5bWJvbHMuZ2V0TWludXNTaWduKCk7XG4gICAgbWFwW1BFUkNFTlRfU1lNQk9MX1BMQUNFSE9MREVSXSA9IHN5bWJvbHMuZ2V0UGVyY2VudFNpZ24oKTtcbiAgICBtYXBbUExVU19TSUdOX1BMQUNFSE9MREVSXSA9IHN5bWJvbHMuZ2V0UGx1c1NpZ24oKTtcblxuICAgIHJldHVybiB0aGlzLnN0cnRyKG51bWJlciwgbWFwKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBzdHJ0cigpIGZvciBKYXZhU2NyaXB0XG4gICAqIFRyYW5zbGF0ZSBjaGFyYWN0ZXJzIG9yIHJlcGxhY2Ugc3Vic3RyaW5nc1xuICAgKlxuICAgKiBAcGFyYW0gc3RyXG4gICAqICBTdHJpbmcgdG8gcGFyc2VcbiAgICogQHBhcmFtIHBhaXJzXG4gICAqICBIYXNoIG9mICgnZnJvbScgPT4gJ3RvJywgLi4uKS5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIHN0cnRyKHN0ciwgcGFpcnMpIHtcbiAgICBjb25zdCBzdWJzdHJzID0gT2JqZWN0LmtleXMocGFpcnMpLm1hcChlc2NhcGVSRSk7XG4gICAgcmV0dXJuIHN0ci5zcGxpdChSZWdFeHAoYCgke3N1YnN0cnMuam9pbignfCcpfSlgKSlcbiAgICAgICAgICAgICAgLm1hcChwYXJ0ID0+IHBhaXJzW3BhcnRdIHx8IHBhcnQpXG4gICAgICAgICAgICAgIC5qb2luKCcnKTtcbiAgfVxuXG5cbiAgLyoqXG4gICAqIEFkZCBtaXNzaW5nIHBsYWNlaG9sZGVycyB0byB0aGUgbnVtYmVyIHVzaW5nIHRoZSBwYXNzZWQgQ0xEUiBwYXR0ZXJuLlxuICAgKlxuICAgKiBNaXNzaW5nIHBsYWNlaG9sZGVycyBjYW4gYmUgdGhlIHBlcmNlbnQgc2lnbiwgY3VycmVuY3kgc3ltYm9sLCBldGMuXG4gICAqXG4gICAqIGUuZy4gd2l0aCBhIGN1cnJlbmN5IENMRFIgcGF0dGVybjpcbiAgICogIC0gUGFzc2VkIG51bWJlciAocGFydGlhbGx5IGZvcm1hdHRlZCk6IDEsMjM0LjU2N1xuICAgKiAgLSBSZXR1cm5lZCBudW1iZXI6IDEsMjM0LjU2NyDCpFxuICAgKiAgKFwiwqRcIiBzeW1ib2wgaXMgdGhlIGN1cnJlbmN5IHN5bWJvbCBwbGFjZWhvbGRlcilcbiAgICpcbiAgICogQHNlZSBodHRwOi8vY2xkci51bmljb2RlLm9yZy90cmFuc2xhdGlvbi9udW1iZXItcGF0dGVybnNcbiAgICpcbiAgICogQHBhcmFtIGZvcm1hdHRlZE51bWJlclxuICAgKiAgTnVtYmVyIHRvIHByb2Nlc3NcbiAgICogQHBhcmFtIHBhdHRlcm5cbiAgICogIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIHRvIHVzZVxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgYWRkUGxhY2Vob2xkZXJzKGZvcm1hdHRlZE51bWJlciwgcGF0dGVybikge1xuICAgIC8qXG4gICAgICogUmVnZXggZ3JvdXBzIGV4cGxhbmF0aW9uOlxuICAgICAqICMgICAgICAgICAgOiBsaXRlcmFsIFwiI1wiIGNoYXJhY3Rlci4gT25jZS5cbiAgICAgKiAoLCMrKSogICAgIDogYW55IG90aGVyIFwiI1wiIGNoYXJhY3RlcnMgZ3JvdXAsIHNlcGFyYXRlZCBieSBcIixcIi4gWmVybyB0byBpbmZpbml0eSB0aW1lcy5cbiAgICAgKiAwICAgICAgICAgIDogbGl0ZXJhbCBcIjBcIiBjaGFyYWN0ZXIuIE9uY2UuXG4gICAgICogKFxcLlswI10rKSogOiBhbnkgY29tYmluYXRpb24gb2YgXCIwXCIgYW5kIFwiI1wiIGNoYXJhY3RlcnMgZ3JvdXBzLCBzZXBhcmF0ZWQgYnkgJy4nLlxuICAgICAqICAgICAgICAgICAgICBaZXJvIHRvIGluZmluaXR5IHRpbWVzLlxuICAgICAqL1xuICAgIHJldHVybiBwYXR0ZXJuLnJlcGxhY2UoLyM/KCwjKykqMChcXC5bMCNdKykqLywgZm9ybWF0dGVkTnVtYmVyKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBQZXJmb3JtIHNvbWUgbW9yZSBzcGVjaWZpYyByZXBsYWNlbWVudHMuXG4gICAqXG4gICAqIFNwZWNpZmljIHJlcGxhY2VtZW50cyBhcmUgbmVlZGVkIHdoZW4gbnVtYmVyIHNwZWNpZmljYXRpb24gaXMgZXh0ZW5kZWQuXG4gICAqIEZvciBpbnN0YW5jZSwgcHJpY2VzIGhhdmUgYW4gZXh0ZW5kZWQgbnVtYmVyIHNwZWNpZmljYXRpb24gaW4gb3JkZXIgdG9cbiAgICogYWRkIGN1cnJlbmN5IHN5bWJvbCB0byB0aGUgZm9ybWF0dGVkIG51bWJlci5cbiAgICpcbiAgICogQHBhcmFtIHN0cmluZyBmb3JtYXR0ZWROdW1iZXJcbiAgICpcbiAgICogQHJldHVybiBtaXhlZFxuICAgKi9cbiAgcGVyZm9ybVNwZWNpZmljUmVwbGFjZW1lbnRzKGZvcm1hdHRlZE51bWJlcikge1xuICAgIGlmICh0aGlzLm51bWJlclNwZWNpZmljYXRpb24gaW5zdGFuY2VvZiBQcmljZVNwZWNpZmljYXRpb24pIHtcbiAgICAgIHJldHVybiBmb3JtYXR0ZWROdW1iZXJcbiAgICAgICAgLnNwbGl0KENVUlJFTkNZX1NZTUJPTF9QTEFDRUhPTERFUilcbiAgICAgICAgLmpvaW4odGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmdldEN1cnJlbmN5U3ltYm9sKCkpO1xuICAgIH1cblxuICAgIHJldHVybiBmb3JtYXR0ZWROdW1iZXI7XG4gIH1cblxuICBzdGF0aWMgYnVpbGQoc3BlY2lmaWNhdGlvbnMpIHtcbiAgICBsZXQgc3ltYm9sO1xuICAgIGlmICh1bmRlZmluZWQgIT09IHNwZWNpZmljYXRpb25zLm51bWJlclN5bWJvbHMpIHtcbiAgICAgIHN5bWJvbCA9IG5ldyBOdW1iZXJTeW1ib2woLi4uc3BlY2lmaWNhdGlvbnMubnVtYmVyU3ltYm9scyk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHN5bWJvbCA9IG5ldyBOdW1iZXJTeW1ib2woLi4uc3BlY2lmaWNhdGlvbnMuc3ltYm9sKTtcbiAgICB9XG5cbiAgICBsZXQgc3BlY2lmaWNhdGlvbjtcbiAgICBpZiAoc3BlY2lmaWNhdGlvbnMuY3VycmVuY3lTeW1ib2wpIHtcbiAgICAgIHNwZWNpZmljYXRpb24gPSBuZXcgUHJpY2VTcGVjaWZpY2F0aW9uKFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5wb3NpdGl2ZVBhdHRlcm4sXG4gICAgICAgIHNwZWNpZmljYXRpb25zLm5lZ2F0aXZlUGF0dGVybixcbiAgICAgICAgc3ltYm9sLFxuICAgICAgICBwYXJzZUludChzcGVjaWZpY2F0aW9ucy5tYXhGcmFjdGlvbkRpZ2l0cywgMTApLFxuICAgICAgICBwYXJzZUludChzcGVjaWZpY2F0aW9ucy5taW5GcmFjdGlvbkRpZ2l0cywgMTApLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5ncm91cGluZ1VzZWQsXG4gICAgICAgIHNwZWNpZmljYXRpb25zLnByaW1hcnlHcm91cFNpemUsXG4gICAgICAgIHNwZWNpZmljYXRpb25zLnNlY29uZGFyeUdyb3VwU2l6ZSxcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMuY3VycmVuY3lTeW1ib2wsXG4gICAgICAgIHNwZWNpZmljYXRpb25zLmN1cnJlbmN5Q29kZSxcbiAgICAgICk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHNwZWNpZmljYXRpb24gPSBuZXcgTnVtYmVyU3BlY2lmaWNhdGlvbihcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMucG9zaXRpdmVQYXR0ZXJuLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5uZWdhdGl2ZVBhdHRlcm4sXG4gICAgICAgIHN5bWJvbCxcbiAgICAgICAgcGFyc2VJbnQoc3BlY2lmaWNhdGlvbnMubWF4RnJhY3Rpb25EaWdpdHMsIDEwKSxcbiAgICAgICAgcGFyc2VJbnQoc3BlY2lmaWNhdGlvbnMubWluRnJhY3Rpb25EaWdpdHMsIDEwKSxcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMuZ3JvdXBpbmdVc2VkLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5wcmltYXJ5R3JvdXBTaXplLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5zZWNvbmRhcnlHcm91cFNpemUsXG4gICAgICApO1xuICAgIH1cblxuICAgIHJldHVybiBuZXcgTnVtYmVyRm9ybWF0dGVyKHNwZWNpZmljYXRpb24pO1xuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IE51bWJlckZvcm1hdHRlcjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL251bWJlci1mb3JtYXR0ZXIuanMiLCJtb2R1bGUuZXhwb3J0cyA9IHtcImJhc2VfdXJsXCI6XCJcIixcInJvdXRlc1wiOntcImFkbWluX3Byb2R1Y3RfZm9ybVwiOntcInRva2Vuc1wiOltbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJpZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9jYXRhbG9nL3Byb2R1Y3RzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJpZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiLFwiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0X3J1bGVzX3NlYXJjaFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9jYXRhbG9nL2NhcnQtcnVsZXMvc2VhcmNoXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY3VzdG9tZXJzX3ZpZXdcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3ZpZXdcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY3VzdG9tZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9jdXN0b21lcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImN1c3RvbWVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIixcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY3VzdG9tZXJzX3NlYXJjaFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9jdXN0b21lcnMvc2VhcmNoXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY3VzdG9tZXJzX2NhcnRzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9jYXJ0c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjdXN0b21lcklkXCJdLFtcInRleHRcIixcIi9zZWxsL2N1c3RvbWVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY3VzdG9tZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jdXN0b21lcnNfb3JkZXJzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9vcmRlcnNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY3VzdG9tZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9jdXN0b21lcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImN1c3RvbWVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fYWRkcmVzc2VzX2NyZWF0ZVwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9hZGRyZXNzZXMvbmV3XCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIixcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fYWRkcmVzc2VzX2VkaXRcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2VkaXRcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiYWRkcmVzc0lkXCJdLFtcInRleHRcIixcIi9zZWxsL2FkZHJlc3Nlc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiYWRkcmVzc0lkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCIsXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyX2FkZHJlc3Nlc19lZGl0XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9lZGl0XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJkZWxpdmVyeXxpbnZvaWNlXCIsXCJhZGRyZXNzVHlwZVwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL2FkZHJlc3Nlcy9vcmRlclwiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCIsXCJhZGRyZXNzVHlwZVwiOlwiZGVsaXZlcnl8aW52b2ljZVwifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIixcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydF9hZGRyZXNzZXNfZWRpdFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvZWRpdFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiZGVsaXZlcnl8aW52b2ljZVwiLFwiYWRkcmVzc1R5cGVcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL2FkZHJlc3Nlcy9jYXJ0XCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wiLFwiYWRkcmVzc1R5cGVcIjpcImRlbGl2ZXJ5fGludm9pY2VcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCIsXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX3ZpZXdcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3ZpZXdcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2luZm9cIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2luZm9cIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2NyZWF0ZVwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHMvbmV3XCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2VkaXRfYWRkcmVzc2VzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9hZGRyZXNzZXNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19lZGl0X2NhcnJpZXJcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2NhcnJpZXJcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19lZGl0X2N1cnJlbmN5XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9jdXJyZW5jeVwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2VkaXRfbGFuZ3VhZ2VcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2xhbmd1YWdlXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfc2V0X2RlbGl2ZXJ5X3NldHRpbmdzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9ydWxlcy9kZWxpdmVyeS1zZXR0aW5nc1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2FkZF9jYXJ0X3J1bGVcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2NhcnQtcnVsZXNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlteL10rK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19kZWxldGVfY2FydF9ydWxlXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9kZWxldGVcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlteL10rK1wiLFwiY2FydFJ1bGVJZFwiXSxbXCJ0ZXh0XCIsXCIvY2FydC1ydWxlc1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiW14vXSsrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2FkZF9wcm9kdWN0XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wcm9kdWN0c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2VkaXRfcHJvZHVjdF9wcmljZVwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvcHJpY2VcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwicHJvZHVjdElkXCJdLFtcInRleHRcIixcIi9wcm9kdWN0c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wiLFwicHJvZHVjdElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19lZGl0X3Byb2R1Y3RfcXVhbnRpdHlcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3F1YW50aXR5XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcInByb2R1Y3RJZFwiXSxbXCJ0ZXh0XCIsXCIvcHJvZHVjdHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIixcInByb2R1Y3RJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfZGVsZXRlX3Byb2R1Y3RcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2RlbGV0ZS1wcm9kdWN0XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3BsYWNlXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9zZWxsL29yZGVycy9wbGFjZVwiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfdmlld1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvdmlld1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiLFwiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfZHVwbGljYXRlX2NhcnRcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2R1cGxpY2F0ZS1jYXJ0XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfdXBkYXRlX3Byb2R1Y3RcIjp7XCJ0b2tlbnNcIjpbW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJEZXRhaWxJZFwiXSxbXCJ0ZXh0XCIsXCIvcHJvZHVjdHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wiLFwib3JkZXJEZXRhaWxJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3BhcnRpYWxfcmVmdW5kXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wYXJ0aWFsLXJlZnVuZFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3N0YW5kYXJkX3JlZnVuZFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc3RhbmRhcmQtcmVmdW5kXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfcmV0dXJuX3Byb2R1Y3RcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3JldHVybi1wcm9kdWN0XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfc2VuZF9wcm9jZXNzX29yZGVyX2VtYWlsXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9zZWxsL29yZGVycy9wcm9jZXNzLW9yZGVyLWVtYWlsXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19hZGRfcHJvZHVjdFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvcHJvZHVjdHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19kZWxldGVfcHJvZHVjdFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvZGVsZXRlXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVyRGV0YWlsSWRcIl0sW1widGV4dFwiLFwiL3Byb2R1Y3RzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIixcIm9yZGVyRGV0YWlsSWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19nZXRfZGlzY291bnRzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9kaXNjb3VudHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX2dldF9wcmljZXNcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3ByaWNlc1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfZ2V0X3Byb2R1Y3RzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wcm9kdWN0c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfZ2V0X2ludm9pY2VzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9pbnZvaWNlc1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfZ2V0X2RvY3VtZW50c1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvZG9jdW1lbnRzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19jYW5jZWxsYXRpb25cIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2NhbmNlbGxhdGlvblwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX2NvbmZpZ3VyZV9wcm9kdWN0X3BhZ2luYXRpb25cIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NvbmZpZ3VyZS1wcm9kdWN0LXBhZ2luYXRpb25cIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOltdLFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3Byb2R1Y3RfcHJpY2VzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wcm9kdWN0cy9wcmljZXNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3Byb2R1Y3RzX3NlYXJjaFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvcHJvZHVjdHMvc2VhcmNoXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119fSxcInByZWZpeFwiOlwiXCIsXCJob3N0XCI6XCJsb2NhbGhvc3RcIixcInBvcnRcIjpcIlwiLFwic2NoZW1lXCI6XCJodHRwXCIsXCJsb2NhbGVcIjpbXX1cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL2pzL2Zvc19qc19yb3V0ZXMuanNvblxuLy8gbW9kdWxlIGlkID0gMTYyXG4vLyBtb2R1bGUgY2h1bmtzID0gMyAxMCAxMyIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvY3JlYXRlXCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3QvY3JlYXRlLmpzXG4vLyBtb2R1bGUgaWQgPSAxNjVcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvZ2V0LXByb3RvdHlwZS1vZlwiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2dldC1wcm90b3R5cGUtb2YuanNcbi8vIG1vZHVsZSBpZCA9IDE2NlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IiwibW9kdWxlLmV4cG9ydHMgPSB7IFwiZGVmYXVsdFwiOiByZXF1aXJlKFwiY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9zZXQtcHJvdG90eXBlLW9mXCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3Qvc2V0LXByb3RvdHlwZS1vZi5qc1xuLy8gbW9kdWxlIGlkID0gMTY3XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQiLCJcInVzZSBzdHJpY3RcIjtcblxuZXhwb3J0cy5fX2VzTW9kdWxlID0gdHJ1ZTtcblxudmFyIF9zZXRQcm90b3R5cGVPZiA9IHJlcXVpcmUoXCIuLi9jb3JlLWpzL29iamVjdC9zZXQtcHJvdG90eXBlLW9mXCIpO1xuXG52YXIgX3NldFByb3RvdHlwZU9mMiA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX3NldFByb3RvdHlwZU9mKTtcblxudmFyIF9jcmVhdGUgPSByZXF1aXJlKFwiLi4vY29yZS1qcy9vYmplY3QvY3JlYXRlXCIpO1xuXG52YXIgX2NyZWF0ZTIgPSBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KF9jcmVhdGUpO1xuXG52YXIgX3R5cGVvZjIgPSByZXF1aXJlKFwiLi4vaGVscGVycy90eXBlb2ZcIik7XG5cbnZhciBfdHlwZW9mMyA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX3R5cGVvZjIpO1xuXG5mdW5jdGlvbiBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KG9iaikgeyByZXR1cm4gb2JqICYmIG9iai5fX2VzTW9kdWxlID8gb2JqIDogeyBkZWZhdWx0OiBvYmogfTsgfVxuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoc3ViQ2xhc3MsIHN1cGVyQ2xhc3MpIHtcbiAgaWYgKHR5cGVvZiBzdXBlckNsYXNzICE9PSBcImZ1bmN0aW9uXCIgJiYgc3VwZXJDbGFzcyAhPT0gbnVsbCkge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoXCJTdXBlciBleHByZXNzaW9uIG11c3QgZWl0aGVyIGJlIG51bGwgb3IgYSBmdW5jdGlvbiwgbm90IFwiICsgKHR5cGVvZiBzdXBlckNsYXNzID09PSBcInVuZGVmaW5lZFwiID8gXCJ1bmRlZmluZWRcIiA6ICgwLCBfdHlwZW9mMy5kZWZhdWx0KShzdXBlckNsYXNzKSkpO1xuICB9XG5cbiAgc3ViQ2xhc3MucHJvdG90eXBlID0gKDAsIF9jcmVhdGUyLmRlZmF1bHQpKHN1cGVyQ2xhc3MgJiYgc3VwZXJDbGFzcy5wcm90b3R5cGUsIHtcbiAgICBjb25zdHJ1Y3Rvcjoge1xuICAgICAgdmFsdWU6IHN1YkNsYXNzLFxuICAgICAgZW51bWVyYWJsZTogZmFsc2UsXG4gICAgICB3cml0YWJsZTogdHJ1ZSxcbiAgICAgIGNvbmZpZ3VyYWJsZTogdHJ1ZVxuICAgIH1cbiAgfSk7XG4gIGlmIChzdXBlckNsYXNzKSBfc2V0UHJvdG90eXBlT2YyLmRlZmF1bHQgPyAoMCwgX3NldFByb3RvdHlwZU9mMi5kZWZhdWx0KShzdWJDbGFzcywgc3VwZXJDbGFzcykgOiBzdWJDbGFzcy5fX3Byb3RvX18gPSBzdXBlckNsYXNzO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2luaGVyaXRzLmpzXG4vLyBtb2R1bGUgaWQgPSAxNjlcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCIsIlwidXNlIHN0cmljdFwiO1xuXG5leHBvcnRzLl9fZXNNb2R1bGUgPSB0cnVlO1xuXG52YXIgX3R5cGVvZjIgPSByZXF1aXJlKFwiLi4vaGVscGVycy90eXBlb2ZcIik7XG5cbnZhciBfdHlwZW9mMyA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX3R5cGVvZjIpO1xuXG5mdW5jdGlvbiBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KG9iaikgeyByZXR1cm4gb2JqICYmIG9iai5fX2VzTW9kdWxlID8gb2JqIDogeyBkZWZhdWx0OiBvYmogfTsgfVxuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoc2VsZiwgY2FsbCkge1xuICBpZiAoIXNlbGYpIHtcbiAgICB0aHJvdyBuZXcgUmVmZXJlbmNlRXJyb3IoXCJ0aGlzIGhhc24ndCBiZWVuIGluaXRpYWxpc2VkIC0gc3VwZXIoKSBoYXNuJ3QgYmVlbiBjYWxsZWRcIik7XG4gIH1cblxuICByZXR1cm4gY2FsbCAmJiAoKHR5cGVvZiBjYWxsID09PSBcInVuZGVmaW5lZFwiID8gXCJ1bmRlZmluZWRcIiA6ICgwLCBfdHlwZW9mMy5kZWZhdWx0KShjYWxsKSkgPT09IFwib2JqZWN0XCIgfHwgdHlwZW9mIGNhbGwgPT09IFwiZnVuY3Rpb25cIikgPyBjYWxsIDogc2VsZjtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9wb3NzaWJsZUNvbnN0cnVjdG9yUmV0dXJuLmpzXG4vLyBtb2R1bGUgaWQgPSAxNzBcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCIsInJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM2Lm9iamVjdC5jcmVhdGUnKTtcbnZhciAkT2JqZWN0ID0gcmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9fY29yZScpLk9iamVjdDtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24gY3JlYXRlKFAsIEQpe1xuICByZXR1cm4gJE9iamVjdC5jcmVhdGUoUCwgRCk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2NyZWF0ZS5qc1xuLy8gbW9kdWxlIGlkID0gMTcxXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQiLCJyZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5vYmplY3QuZ2V0LXByb3RvdHlwZS1vZicpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuT2JqZWN0LmdldFByb3RvdHlwZU9mO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2dldC1wcm90b3R5cGUtb2YuanNcbi8vIG1vZHVsZSBpZCA9IDE3MlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczYub2JqZWN0LnNldC1wcm90b3R5cGUtb2YnKTtcbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9fY29yZScpLk9iamVjdC5zZXRQcm90b3R5cGVPZjtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9zZXQtcHJvdG90eXBlLW9mLmpzXG4vLyBtb2R1bGUgaWQgPSAxNzNcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCIsIi8vIFdvcmtzIHdpdGggX19wcm90b19fIG9ubHkuIE9sZCB2OCBjYW4ndCB3b3JrIHdpdGggbnVsbCBwcm90byBvYmplY3RzLlxuLyogZXNsaW50LWRpc2FibGUgbm8tcHJvdG8gKi9cbnZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpXG4gICwgYW5PYmplY3QgPSByZXF1aXJlKCcuL19hbi1vYmplY3QnKTtcbnZhciBjaGVjayA9IGZ1bmN0aW9uKE8sIHByb3RvKXtcbiAgYW5PYmplY3QoTyk7XG4gIGlmKCFpc09iamVjdChwcm90bykgJiYgcHJvdG8gIT09IG51bGwpdGhyb3cgVHlwZUVycm9yKHByb3RvICsgXCI6IGNhbid0IHNldCBhcyBwcm90b3R5cGUhXCIpO1xufTtcbm1vZHVsZS5leHBvcnRzID0ge1xuICBzZXQ6IE9iamVjdC5zZXRQcm90b3R5cGVPZiB8fCAoJ19fcHJvdG9fXycgaW4ge30gPyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lXG4gICAgZnVuY3Rpb24odGVzdCwgYnVnZ3ksIHNldCl7XG4gICAgICB0cnkge1xuICAgICAgICBzZXQgPSByZXF1aXJlKCcuL19jdHgnKShGdW5jdGlvbi5jYWxsLCByZXF1aXJlKCcuL19vYmplY3QtZ29wZCcpLmYoT2JqZWN0LnByb3RvdHlwZSwgJ19fcHJvdG9fXycpLnNldCwgMik7XG4gICAgICAgIHNldCh0ZXN0LCBbXSk7XG4gICAgICAgIGJ1Z2d5ID0gISh0ZXN0IGluc3RhbmNlb2YgQXJyYXkpO1xuICAgICAgfSBjYXRjaChlKXsgYnVnZ3kgPSB0cnVlOyB9XG4gICAgICByZXR1cm4gZnVuY3Rpb24gc2V0UHJvdG90eXBlT2YoTywgcHJvdG8pe1xuICAgICAgICBjaGVjayhPLCBwcm90byk7XG4gICAgICAgIGlmKGJ1Z2d5KU8uX19wcm90b19fID0gcHJvdG87XG4gICAgICAgIGVsc2Ugc2V0KE8sIHByb3RvKTtcbiAgICAgICAgcmV0dXJuIE87XG4gICAgICB9O1xuICAgIH0oe30sIGZhbHNlKSA6IHVuZGVmaW5lZCksXG4gIGNoZWNrOiBjaGVja1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NldC1wcm90by5qc1xuLy8gbW9kdWxlIGlkID0gMTc0XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQiLCJ2YXIgJGV4cG9ydCA9IHJlcXVpcmUoJy4vX2V4cG9ydCcpXG4vLyAxOS4xLjIuMiAvIDE1LjIuMy41IE9iamVjdC5jcmVhdGUoTyBbLCBQcm9wZXJ0aWVzXSlcbiRleHBvcnQoJGV4cG9ydC5TLCAnT2JqZWN0Jywge2NyZWF0ZTogcmVxdWlyZSgnLi9fb2JqZWN0LWNyZWF0ZScpfSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuY3JlYXRlLmpzXG4vLyBtb2R1bGUgaWQgPSAxNzVcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCIsIi8vIDE5LjEuMi45IE9iamVjdC5nZXRQcm90b3R5cGVPZihPKVxudmFyIHRvT2JqZWN0ICAgICAgICA9IHJlcXVpcmUoJy4vX3RvLW9iamVjdCcpXG4gICwgJGdldFByb3RvdHlwZU9mID0gcmVxdWlyZSgnLi9fb2JqZWN0LWdwbycpO1xuXG5yZXF1aXJlKCcuL19vYmplY3Qtc2FwJykoJ2dldFByb3RvdHlwZU9mJywgZnVuY3Rpb24oKXtcbiAgcmV0dXJuIGZ1bmN0aW9uIGdldFByb3RvdHlwZU9mKGl0KXtcbiAgICByZXR1cm4gJGdldFByb3RvdHlwZU9mKHRvT2JqZWN0KGl0KSk7XG4gIH07XG59KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5nZXQtcHJvdG90eXBlLW9mLmpzXG4vLyBtb2R1bGUgaWQgPSAxNzZcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCIsIi8vIDE5LjEuMy4xOSBPYmplY3Quc2V0UHJvdG90eXBlT2YoTywgcHJvdG8pXG52YXIgJGV4cG9ydCA9IHJlcXVpcmUoJy4vX2V4cG9ydCcpO1xuJGV4cG9ydCgkZXhwb3J0LlMsICdPYmplY3QnLCB7c2V0UHJvdG90eXBlT2Y6IHJlcXVpcmUoJy4vX3NldC1wcm90bycpLnNldH0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LnNldC1wcm90b3R5cGUtb2YuanNcbi8vIG1vZHVsZSBpZCA9IDE3N1xuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IiwiJ3VzZSBzdHJpY3QnO3ZhciBfZXh0ZW5kcz1PYmplY3QuYXNzaWdufHxmdW5jdGlvbihhKXtmb3IodmFyIGIsYz0xO2M8YXJndW1lbnRzLmxlbmd0aDtjKyspZm9yKHZhciBkIGluIGI9YXJndW1lbnRzW2NdLGIpT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKGIsZCkmJihhW2RdPWJbZF0pO3JldHVybiBhfSxfdHlwZW9mPSdmdW5jdGlvbic9PXR5cGVvZiBTeW1ib2wmJidzeW1ib2wnPT10eXBlb2YgU3ltYm9sLml0ZXJhdG9yP2Z1bmN0aW9uKGEpe3JldHVybiB0eXBlb2YgYX06ZnVuY3Rpb24oYSl7cmV0dXJuIGEmJidmdW5jdGlvbic9PXR5cGVvZiBTeW1ib2wmJmEuY29uc3RydWN0b3I9PT1TeW1ib2wmJmEhPT1TeW1ib2wucHJvdG90eXBlPydzeW1ib2wnOnR5cGVvZiBhfTtmdW5jdGlvbiBfY2xhc3NDYWxsQ2hlY2soYSxiKXtpZighKGEgaW5zdGFuY2VvZiBiKSl0aHJvdyBuZXcgVHlwZUVycm9yKCdDYW5ub3QgY2FsbCBhIGNsYXNzIGFzIGEgZnVuY3Rpb24nKX12YXIgUm91dGluZz1mdW5jdGlvbiBhKCl7dmFyIGI9dGhpcztfY2xhc3NDYWxsQ2hlY2sodGhpcyxhKSx0aGlzLnNldFJvdXRlcz1mdW5jdGlvbihhKXtiLnJvdXRlc1JvdXRpbmc9YXx8W119LHRoaXMuZ2V0Um91dGVzPWZ1bmN0aW9uKCl7cmV0dXJuIGIucm91dGVzUm91dGluZ30sdGhpcy5zZXRCYXNlVXJsPWZ1bmN0aW9uKGEpe2IuY29udGV4dFJvdXRpbmcuYmFzZV91cmw9YX0sdGhpcy5nZXRCYXNlVXJsPWZ1bmN0aW9uKCl7cmV0dXJuIGIuY29udGV4dFJvdXRpbmcuYmFzZV91cmx9LHRoaXMuc2V0UHJlZml4PWZ1bmN0aW9uKGEpe2IuY29udGV4dFJvdXRpbmcucHJlZml4PWF9LHRoaXMuc2V0U2NoZW1lPWZ1bmN0aW9uKGEpe2IuY29udGV4dFJvdXRpbmcuc2NoZW1lPWF9LHRoaXMuZ2V0U2NoZW1lPWZ1bmN0aW9uKCl7cmV0dXJuIGIuY29udGV4dFJvdXRpbmcuc2NoZW1lfSx0aGlzLnNldEhvc3Q9ZnVuY3Rpb24oYSl7Yi5jb250ZXh0Um91dGluZy5ob3N0PWF9LHRoaXMuZ2V0SG9zdD1mdW5jdGlvbigpe3JldHVybiBiLmNvbnRleHRSb3V0aW5nLmhvc3R9LHRoaXMuYnVpbGRRdWVyeVBhcmFtcz1mdW5jdGlvbihhLGMsZCl7dmFyIGU9bmV3IFJlZ0V4cCgvXFxbXSQvKTtjIGluc3RhbmNlb2YgQXJyYXk/Yy5mb3JFYWNoKGZ1bmN0aW9uKGMsZil7ZS50ZXN0KGEpP2QoYSxjKTpiLmJ1aWxkUXVlcnlQYXJhbXMoYSsnWycrKCdvYmplY3QnPT09KCd1bmRlZmluZWQnPT10eXBlb2YgYz8ndW5kZWZpbmVkJzpfdHlwZW9mKGMpKT9mOicnKSsnXScsYyxkKX0pOidvYmplY3QnPT09KCd1bmRlZmluZWQnPT10eXBlb2YgYz8ndW5kZWZpbmVkJzpfdHlwZW9mKGMpKT9PYmplY3Qua2V5cyhjKS5mb3JFYWNoKGZ1bmN0aW9uKGUpe3JldHVybiBiLmJ1aWxkUXVlcnlQYXJhbXMoYSsnWycrZSsnXScsY1tlXSxkKX0pOmQoYSxjKX0sdGhpcy5nZXRSb3V0ZT1mdW5jdGlvbihhKXt2YXIgYz1iLmNvbnRleHRSb3V0aW5nLnByZWZpeCthO2lmKCEhYi5yb3V0ZXNSb3V0aW5nW2NdKXJldHVybiBiLnJvdXRlc1JvdXRpbmdbY107ZWxzZSBpZighYi5yb3V0ZXNSb3V0aW5nW2FdKXRocm93IG5ldyBFcnJvcignVGhlIHJvdXRlIFwiJythKydcIiBkb2VzIG5vdCBleGlzdC4nKTtyZXR1cm4gYi5yb3V0ZXNSb3V0aW5nW2FdfSx0aGlzLmdlbmVyYXRlPWZ1bmN0aW9uKGEsYyxkKXt2YXIgZT1iLmdldFJvdXRlKGEpLGY9Y3x8e30sZz1fZXh0ZW5kcyh7fSxmKSxoPSdfc2NoZW1lJyxpPScnLGo9ITAsaz0nJztpZigoZS50b2tlbnN8fFtdKS5mb3JFYWNoKGZ1bmN0aW9uKGIpe2lmKCd0ZXh0Jz09PWJbMF0pcmV0dXJuIGk9YlsxXStpLHZvaWQoaj0hMSk7aWYoJ3ZhcmlhYmxlJz09PWJbMF0pe3ZhciBjPShlLmRlZmF1bHRzfHx7fSlbYlszXV07aWYoITE9PWp8fCFjfHwoZnx8e30pW2JbM11dJiZmW2JbM11dIT09ZS5kZWZhdWx0c1tiWzNdXSl7dmFyIGQ7aWYoKGZ8fHt9KVtiWzNdXSlkPWZbYlszXV0sZGVsZXRlIGdbYlszXV07ZWxzZSBpZihjKWQ9ZS5kZWZhdWx0c1tiWzNdXTtlbHNle2lmKGopcmV0dXJuO3Rocm93IG5ldyBFcnJvcignVGhlIHJvdXRlIFwiJythKydcIiByZXF1aXJlcyB0aGUgcGFyYW1ldGVyIFwiJytiWzNdKydcIi4nKX12YXIgaD0hMD09PWR8fCExPT09ZHx8Jyc9PT1kO2lmKCFofHwhail7dmFyIGs9ZW5jb2RlVVJJQ29tcG9uZW50KGQpLnJlcGxhY2UoLyUyRi9nLCcvJyk7J251bGwnPT09ayYmbnVsbD09PWQmJihrPScnKSxpPWJbMV0raytpfWo9ITF9ZWxzZSBjJiZkZWxldGUgZ1tiWzNdXTtyZXR1cm59dGhyb3cgbmV3IEVycm9yKCdUaGUgdG9rZW4gdHlwZSBcIicrYlswXSsnXCIgaXMgbm90IHN1cHBvcnRlZC4nKX0pLCcnPT1pJiYoaT0nLycpLChlLmhvc3R0b2tlbnN8fFtdKS5mb3JFYWNoKGZ1bmN0aW9uKGEpe3ZhciBiO3JldHVybid0ZXh0Jz09PWFbMF0/dm9pZChrPWFbMV0rayk6dm9pZCgndmFyaWFibGUnPT09YVswXSYmKChmfHx7fSlbYVszXV0/KGI9ZlthWzNdXSxkZWxldGUgZ1thWzNdXSk6ZS5kZWZhdWx0c1thWzNdXSYmKGI9ZS5kZWZhdWx0c1thWzNdXSksaz1hWzFdK2IraykpfSksaT1iLmNvbnRleHRSb3V0aW5nLmJhc2VfdXJsK2ksZS5yZXF1aXJlbWVudHNbaF0mJmIuZ2V0U2NoZW1lKCkhPT1lLnJlcXVpcmVtZW50c1toXT9pPWUucmVxdWlyZW1lbnRzW2hdKyc6Ly8nKyhrfHxiLmdldEhvc3QoKSkraTprJiZiLmdldEhvc3QoKSE9PWs/aT1iLmdldFNjaGVtZSgpKyc6Ly8nK2sraTohMD09PWQmJihpPWIuZ2V0U2NoZW1lKCkrJzovLycrYi5nZXRIb3N0KCkraSksMDxPYmplY3Qua2V5cyhnKS5sZW5ndGgpe3ZhciBsPVtdLG09ZnVuY3Rpb24oYSxiKXt2YXIgYz1iO2M9J2Z1bmN0aW9uJz09dHlwZW9mIGM/YygpOmMsYz1udWxsPT09Yz8nJzpjLGwucHVzaChlbmNvZGVVUklDb21wb25lbnQoYSkrJz0nK2VuY29kZVVSSUNvbXBvbmVudChjKSl9O09iamVjdC5rZXlzKGcpLmZvckVhY2goZnVuY3Rpb24oYSl7cmV0dXJuIGIuYnVpbGRRdWVyeVBhcmFtcyhhLGdbYV0sbSl9KSxpPWkrJz8nK2wuam9pbignJicpLnJlcGxhY2UoLyUyMC9nLCcrJyl9cmV0dXJuIGl9LHRoaXMuc2V0RGF0YT1mdW5jdGlvbihhKXtiLnNldEJhc2VVcmwoYS5iYXNlX3VybCksYi5zZXRSb3V0ZXMoYS5yb3V0ZXMpLCdwcmVmaXgnaW4gYSYmYi5zZXRQcmVmaXgoYS5wcmVmaXgpLGIuc2V0SG9zdChhLmhvc3QpLGIuc2V0U2NoZW1lKGEuc2NoZW1lKX0sdGhpcy5jb250ZXh0Um91dGluZz17YmFzZV91cmw6JycscHJlZml4OicnLGhvc3Q6Jycsc2NoZW1lOicnfX07bW9kdWxlLmV4cG9ydHM9bmV3IFJvdXRpbmc7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2Zvcy1yb3V0aW5nL2Rpc3Qvcm91dGluZy5qc1xuLy8gbW9kdWxlIGlkID0gMTc5XG4vLyBtb2R1bGUgY2h1bmtzID0gMyAxMCAxMyIsIi8qKlxuICogbG9kYXNoIChDdXN0b20gQnVpbGQpIDxodHRwczovL2xvZGFzaC5jb20vPlxuICogQnVpbGQ6IGBsb2Rhc2ggbW9kdWxhcml6ZSBleHBvcnRzPVwibnBtXCIgLW8gLi9gXG4gKiBDb3B5cmlnaHQgalF1ZXJ5IEZvdW5kYXRpb24gYW5kIG90aGVyIGNvbnRyaWJ1dG9ycyA8aHR0cHM6Ly9qcXVlcnkub3JnLz5cbiAqIFJlbGVhc2VkIHVuZGVyIE1JVCBsaWNlbnNlIDxodHRwczovL2xvZGFzaC5jb20vbGljZW5zZT5cbiAqIEJhc2VkIG9uIFVuZGVyc2NvcmUuanMgMS44LjMgPGh0dHA6Ly91bmRlcnNjb3JlanMub3JnL0xJQ0VOU0U+XG4gKiBDb3B5cmlnaHQgSmVyZW15IEFzaGtlbmFzLCBEb2N1bWVudENsb3VkIGFuZCBJbnZlc3RpZ2F0aXZlIFJlcG9ydGVycyAmIEVkaXRvcnNcbiAqL1xuXG4vKiogVXNlZCBhcyByZWZlcmVuY2VzIGZvciB2YXJpb3VzIGBOdW1iZXJgIGNvbnN0YW50cy4gKi9cbnZhciBJTkZJTklUWSA9IDEgLyAwO1xuXG4vKiogYE9iamVjdCN0b1N0cmluZ2AgcmVzdWx0IHJlZmVyZW5jZXMuICovXG52YXIgc3ltYm9sVGFnID0gJ1tvYmplY3QgU3ltYm9sXSc7XG5cbi8qKlxuICogVXNlZCB0byBtYXRjaCBgUmVnRXhwYFxuICogW3N5bnRheCBjaGFyYWN0ZXJzXShodHRwOi8vZWNtYS1pbnRlcm5hdGlvbmFsLm9yZy9lY21hLTI2Mi82LjAvI3NlYy1wYXR0ZXJucykuXG4gKi9cbnZhciByZVJlZ0V4cENoYXIgPSAvW1xcXFxeJC4qKz8oKVtcXF17fXxdL2csXG4gICAgcmVIYXNSZWdFeHBDaGFyID0gUmVnRXhwKHJlUmVnRXhwQ2hhci5zb3VyY2UpO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYGdsb2JhbGAgZnJvbSBOb2RlLmpzLiAqL1xudmFyIGZyZWVHbG9iYWwgPSB0eXBlb2YgZ2xvYmFsID09ICdvYmplY3QnICYmIGdsb2JhbCAmJiBnbG9iYWwuT2JqZWN0ID09PSBPYmplY3QgJiYgZ2xvYmFsO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYHNlbGZgLiAqL1xudmFyIGZyZWVTZWxmID0gdHlwZW9mIHNlbGYgPT0gJ29iamVjdCcgJiYgc2VsZiAmJiBzZWxmLk9iamVjdCA9PT0gT2JqZWN0ICYmIHNlbGY7XG5cbi8qKiBVc2VkIGFzIGEgcmVmZXJlbmNlIHRvIHRoZSBnbG9iYWwgb2JqZWN0LiAqL1xudmFyIHJvb3QgPSBmcmVlR2xvYmFsIHx8IGZyZWVTZWxmIHx8IEZ1bmN0aW9uKCdyZXR1cm4gdGhpcycpKCk7XG5cbi8qKiBVc2VkIGZvciBidWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcy4gKi9cbnZhciBvYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbi8qKlxuICogVXNlZCB0byByZXNvbHZlIHRoZVxuICogW2B0b1N0cmluZ1RhZ2BdKGh0dHA6Ly9lY21hLWludGVybmF0aW9uYWwub3JnL2VjbWEtMjYyLzYuMC8jc2VjLW9iamVjdC5wcm90b3R5cGUudG9zdHJpbmcpXG4gKiBvZiB2YWx1ZXMuXG4gKi9cbnZhciBvYmplY3RUb1N0cmluZyA9IG9iamVjdFByb3RvLnRvU3RyaW5nO1xuXG4vKiogQnVpbHQtaW4gdmFsdWUgcmVmZXJlbmNlcy4gKi9cbnZhciBTeW1ib2wgPSByb290LlN5bWJvbDtcblxuLyoqIFVzZWQgdG8gY29udmVydCBzeW1ib2xzIHRvIHByaW1pdGl2ZXMgYW5kIHN0cmluZ3MuICovXG52YXIgc3ltYm9sUHJvdG8gPSBTeW1ib2wgPyBTeW1ib2wucHJvdG90eXBlIDogdW5kZWZpbmVkLFxuICAgIHN5bWJvbFRvU3RyaW5nID0gc3ltYm9sUHJvdG8gPyBzeW1ib2xQcm90by50b1N0cmluZyA6IHVuZGVmaW5lZDtcblxuLyoqXG4gKiBUaGUgYmFzZSBpbXBsZW1lbnRhdGlvbiBvZiBgXy50b1N0cmluZ2Agd2hpY2ggZG9lc24ndCBjb252ZXJ0IG51bGxpc2hcbiAqIHZhbHVlcyB0byBlbXB0eSBzdHJpbmdzLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBwcm9jZXNzLlxuICogQHJldHVybnMge3N0cmluZ30gUmV0dXJucyB0aGUgc3RyaW5nLlxuICovXG5mdW5jdGlvbiBiYXNlVG9TdHJpbmcodmFsdWUpIHtcbiAgLy8gRXhpdCBlYXJseSBmb3Igc3RyaW5ncyB0byBhdm9pZCBhIHBlcmZvcm1hbmNlIGhpdCBpbiBzb21lIGVudmlyb25tZW50cy5cbiAgaWYgKHR5cGVvZiB2YWx1ZSA9PSAnc3RyaW5nJykge1xuICAgIHJldHVybiB2YWx1ZTtcbiAgfVxuICBpZiAoaXNTeW1ib2wodmFsdWUpKSB7XG4gICAgcmV0dXJuIHN5bWJvbFRvU3RyaW5nID8gc3ltYm9sVG9TdHJpbmcuY2FsbCh2YWx1ZSkgOiAnJztcbiAgfVxuICB2YXIgcmVzdWx0ID0gKHZhbHVlICsgJycpO1xuICByZXR1cm4gKHJlc3VsdCA9PSAnMCcgJiYgKDEgLyB2YWx1ZSkgPT0gLUlORklOSVRZKSA/ICctMCcgOiByZXN1bHQ7XG59XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgb2JqZWN0LWxpa2UuIEEgdmFsdWUgaXMgb2JqZWN0LWxpa2UgaWYgaXQncyBub3QgYG51bGxgXG4gKiBhbmQgaGFzIGEgYHR5cGVvZmAgcmVzdWx0IG9mIFwib2JqZWN0XCIuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSA0LjAuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgb2JqZWN0LWxpa2UsIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc09iamVjdExpa2Uoe30pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNPYmplY3RMaWtlKFsxLCAyLCAzXSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc09iamVjdExpa2UoXy5ub29wKTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc09iamVjdExpa2UobnVsbCk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG5mdW5jdGlvbiBpc09iamVjdExpa2UodmFsdWUpIHtcbiAgcmV0dXJuICEhdmFsdWUgJiYgdHlwZW9mIHZhbHVlID09ICdvYmplY3QnO1xufVxuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGNsYXNzaWZpZWQgYXMgYSBgU3ltYm9sYCBwcmltaXRpdmUgb3Igb2JqZWN0LlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgNC4wLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGEgc3ltYm9sLCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNTeW1ib2woU3ltYm9sLml0ZXJhdG9yKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzU3ltYm9sKCdhYmMnKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzU3ltYm9sKHZhbHVlKSB7XG4gIHJldHVybiB0eXBlb2YgdmFsdWUgPT0gJ3N5bWJvbCcgfHxcbiAgICAoaXNPYmplY3RMaWtlKHZhbHVlKSAmJiBvYmplY3RUb1N0cmluZy5jYWxsKHZhbHVlKSA9PSBzeW1ib2xUYWcpO1xufVxuXG4vKipcbiAqIENvbnZlcnRzIGB2YWx1ZWAgdG8gYSBzdHJpbmcuIEFuIGVtcHR5IHN0cmluZyBpcyByZXR1cm5lZCBmb3IgYG51bGxgXG4gKiBhbmQgYHVuZGVmaW5lZGAgdmFsdWVzLiBUaGUgc2lnbiBvZiBgLTBgIGlzIHByZXNlcnZlZC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMC4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gcHJvY2Vzcy5cbiAqIEByZXR1cm5zIHtzdHJpbmd9IFJldHVybnMgdGhlIHN0cmluZy5cbiAqIEBleGFtcGxlXG4gKlxuICogXy50b1N0cmluZyhudWxsKTtcbiAqIC8vID0+ICcnXG4gKlxuICogXy50b1N0cmluZygtMCk7XG4gKiAvLyA9PiAnLTAnXG4gKlxuICogXy50b1N0cmluZyhbMSwgMiwgM10pO1xuICogLy8gPT4gJzEsMiwzJ1xuICovXG5mdW5jdGlvbiB0b1N0cmluZyh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgPT0gbnVsbCA/ICcnIDogYmFzZVRvU3RyaW5nKHZhbHVlKTtcbn1cblxuLyoqXG4gKiBFc2NhcGVzIHRoZSBgUmVnRXhwYCBzcGVjaWFsIGNoYXJhY3RlcnMgXCJeXCIsIFwiJFwiLCBcIlxcXCIsIFwiLlwiLCBcIipcIiwgXCIrXCIsXG4gKiBcIj9cIiwgXCIoXCIsIFwiKVwiLCBcIltcIiwgXCJdXCIsIFwie1wiLCBcIn1cIiwgYW5kIFwifFwiIGluIGBzdHJpbmdgLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMy4wLjBcbiAqIEBjYXRlZ29yeSBTdHJpbmdcbiAqIEBwYXJhbSB7c3RyaW5nfSBbc3RyaW5nPScnXSBUaGUgc3RyaW5nIHRvIGVzY2FwZS5cbiAqIEByZXR1cm5zIHtzdHJpbmd9IFJldHVybnMgdGhlIGVzY2FwZWQgc3RyaW5nLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmVzY2FwZVJlZ0V4cCgnW2xvZGFzaF0oaHR0cHM6Ly9sb2Rhc2guY29tLyknKTtcbiAqIC8vID0+ICdcXFtsb2Rhc2hcXF1cXChodHRwczovL2xvZGFzaFxcLmNvbS9cXCknXG4gKi9cbmZ1bmN0aW9uIGVzY2FwZVJlZ0V4cChzdHJpbmcpIHtcbiAgc3RyaW5nID0gdG9TdHJpbmcoc3RyaW5nKTtcbiAgcmV0dXJuIChzdHJpbmcgJiYgcmVIYXNSZWdFeHBDaGFyLnRlc3Qoc3RyaW5nKSlcbiAgICA/IHN0cmluZy5yZXBsYWNlKHJlUmVnRXhwQ2hhciwgJ1xcXFwkJicpXG4gICAgOiBzdHJpbmc7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gZXNjYXBlUmVnRXhwO1xuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2xvZGFzaC5lc2NhcGVyZWdleHAvaW5kZXguanNcbi8vIG1vZHVsZSBpZCA9IDE4MFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5leHBvcnQgZGVmYXVsdCB7XG4gIHByb2R1Y3REZWxldGVkRnJvbU9yZGVyOiAncHJvZHVjdERlbGV0ZWRGcm9tT3JkZXInLFxuICBwcm9kdWN0QWRkZWRUb09yZGVyOiAncHJvZHVjdEFkZGVkVG9PcmRlcicsXG4gIHByb2R1Y3RVcGRhdGVkOiAncHJvZHVjdFVwZGF0ZWQnLFxuICBwcm9kdWN0RWRpdGlvbkNhbmNlbGVkOiAncHJvZHVjdEVkaXRpb25DYW5jZWxlZCcsXG4gIHByb2R1Y3RMaXN0UGFnaW5hdGVkOiAncHJvZHVjdExpc3RQYWdpbmF0ZWQnLFxuICBwcm9kdWN0TGlzdE51bWJlclBlclBhZ2U6ICdwcm9kdWN0TGlzdE51bWJlclBlclBhZ2UnLFxufTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItdmlldy1ldmVudC1tYXAuanMiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L3ZhbHVlc1wiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L3ZhbHVlcy5qc1xuLy8gbW9kdWxlIGlkID0gMTkzXG4vLyBtb2R1bGUgY2h1bmtzID0gMyAxMCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IFJvdXRlciBmcm9tICdAY29tcG9uZW50cy9yb3V0ZXInO1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL09yZGVyVmlld1BhZ2VNYXAnO1xuXG5jb25zdCB7JH0gPSB3aW5kb3c7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIE9yZGVyUHJpY2VzUmVmcmVzaGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gIH1cblxuICByZWZyZXNoKG9yZGVySWQpIHtcbiAgICAkLmFqYXgodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19nZXRfcHJpY2VzJywge29yZGVySWR9KSkudGhlbihyZXNwb25zZSA9PiB7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJUb3RhbCkudGV4dChyZXNwb25zZS5vcmRlclRvdGFsRm9ybWF0dGVkKTtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlckRpc2NvdW50c1RvdGFsKS50ZXh0KGAtJHtyZXNwb25zZS5kaXNjb3VudHNBbW91bnRGb3JtYXR0ZWR9YCk7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJEaXNjb3VudHNUb3RhbENvbnRhaW5lcikudG9nZ2xlQ2xhc3MoJ2Qtbm9uZScsICFyZXNwb25zZS5kaXNjb3VudHNBbW91bnREaXNwbGF5ZWQpO1xuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLm9yZGVyUHJvZHVjdHNUb3RhbCkudGV4dChyZXNwb25zZS5wcm9kdWN0c1RvdGFsRm9ybWF0dGVkKTtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlclNoaXBwaW5nVG90YWwpLnRleHQocmVzcG9uc2Uuc2hpcHBpbmdUb3RhbEZvcm1hdHRlZCk7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJUYXhlc1RvdGFsKS50ZXh0KHJlc3BvbnNlLnRheGVzVG90YWxGb3JtYXR0ZWQpO1xuICAgIH0pO1xuICB9XG5cbiAgcmVmcmVzaFByb2R1Y3RQcmljZXMob3JkZXJJZCkge1xuICAgICQuYWpheCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fb3JkZXJzX3Byb2R1Y3RfcHJpY2VzJywge29yZGVySWR9KSkudGhlbihwcm9kdWN0UHJpY2VzTGlzdCA9PiB7XG4gICAgICBwcm9kdWN0UHJpY2VzTGlzdC5mb3JFYWNoKHByb2R1Y3RQcmljZXMgPT4ge1xuICAgICAgICBjb25zdCBvcmRlclByb2R1Y3RUcklkID0gT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUm93KHByb2R1Y3RQcmljZXMub3JkZXJEZXRhaWxJZCk7XG5cbiAgICAgICAgJChgJHtvcmRlclByb2R1Y3RUcklkfSAke09yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRVbml0UHJpY2V9YCkudGV4dChwcm9kdWN0UHJpY2VzLnVuaXRQcmljZSk7XG4gICAgICAgICQoYCR7b3JkZXJQcm9kdWN0VHJJZH0gJHtPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0UXVhbnRpdHl9YCkudGV4dChwcm9kdWN0UHJpY2VzLnF1YW50aXR5KTtcbiAgICAgICAgJChgJHtvcmRlclByb2R1Y3RUcklkfSAke09yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRBdmFpbGFibGVRdWFudGl0eX1gKS50ZXh0KHByb2R1Y3RQcmljZXMuYXZhaWxhYmxlUXVhbnRpdHkpO1xuICAgICAgICAkKGAke29yZGVyUHJvZHVjdFRySWR9ICR7T3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFRvdGFsUHJpY2V9YCkudGV4dChwcm9kdWN0UHJpY2VzLnRvdGFsUHJpY2UpO1xuXG4gICAgICAgIC8vIHVwZGF0ZSBvcmRlciByb3cgcHJpY2UgdmFsdWVzXG4gICAgICAgIGNvbnN0IHByb2R1Y3RFZGl0QnV0dG9uID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0QnRuKHByb2R1Y3RQcmljZXMub3JkZXJEZXRhaWxJZCkpO1xuXG4gICAgICAgIHByb2R1Y3RFZGl0QnV0dG9uLmRhdGEoJ3Byb2R1Y3QtcHJpY2UtdGF4LWluY2wnLCBwcm9kdWN0UHJpY2VzLnVuaXRQcmljZVRheEluY2xSYXcpO1xuICAgICAgICBwcm9kdWN0RWRpdEJ1dHRvbi5kYXRhKCdwcm9kdWN0LXByaWNlLXRheC1leGNsJywgcHJvZHVjdFByaWNlcy51bml0UHJpY2VUYXhFeGNsUmF3KTtcbiAgICAgICAgcHJvZHVjdEVkaXRCdXR0b24uZGF0YSgncHJvZHVjdC1xdWFudGl0eScsIHByb2R1Y3RQcmljZXMucXVhbnRpdHkpO1xuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cblxuICBjaGVja090aGVyUHJvZHVjdFByaWNlc01hdGNoKGdpdmVuUHJpY2UsIHByb2R1Y3RJZCwgY29tYmluYXRpb25JZCwgaW52b2ljZUlkLCBvcmRlckRldGFpbElkKSB7XG4gICAgY29uc3QgcHJvZHVjdFJvd3MgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCd0ci5jZWxsUHJvZHVjdCcpO1xuICAgIC8vIFdlIGNvbnZlcnQgdGhlIGV4cGVjdGVkIHZhbHVlcyBpbnRvIGludC9mbG9hdCB0byBhdm9pZCBhIHR5cGUgbWlzbWF0Y2ggdGhhdCB3b3VsZCBiZSB3cm9uZ2x5IGludGVycHJldGVkXG4gICAgY29uc3QgZXhwZWN0ZWRQcm9kdWN0SWQgPSBOdW1iZXIocHJvZHVjdElkKTtcbiAgICBjb25zdCBleHBlY3RlZENvbWJpbmF0aW9uSWQgPSBOdW1iZXIoY29tYmluYXRpb25JZCk7XG4gICAgY29uc3QgZXhwZWN0ZWRHaXZlblByaWNlID0gTnVtYmVyKGdpdmVuUHJpY2UpO1xuICAgIGxldCB1bm1hdGNoaW5nUHJpY2VFeGlzdHMgPSBmYWxzZTtcblxuICAgIHByb2R1Y3RSb3dzLmZvckVhY2goKHByb2R1Y3RSb3cpID0+IHtcbiAgICAgIGNvbnN0IHByb2R1Y3RSb3dJZCA9ICQocHJvZHVjdFJvdykuYXR0cignaWQnKTtcblxuICAgICAgLy8gTm8gbmVlZCB0byBjaGVjayBlZGl0ZWQgcm93IChlc3BlY2lhbGx5IGlmIGl0J3MgdGhlIG9ubHkgb25lIGZvciB0aGlzIHByb2R1Y3QpXG4gICAgICBpZiAob3JkZXJEZXRhaWxJZCAmJiBwcm9kdWN0Um93SWQgPT09IGBvcmRlclByb2R1Y3RfJHtvcmRlckRldGFpbElkfWApIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBjb25zdCBwcm9kdWN0RWRpdEJ0biA9ICQoYCMke3Byb2R1Y3RSb3dJZH0gJHtPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0QnV0dG9uc31gKTtcbiAgICAgIGNvbnN0IGN1cnJlbnRPcmRlckludm9pY2VJZCA9IE51bWJlcihwcm9kdWN0RWRpdEJ0bi5kYXRhKCdvcmRlci1pbnZvaWNlLWlkJykpO1xuXG4gICAgICAvLyBObyBuZWVkIHRvIGNoZWNrIHRhcmdldCBpbnZvaWNlLCBvbmx5IGlmIG90aGVycyBoYXZlIG1hdGNoaW5nIHByb2R1Y3RzXG4gICAgICBpZiAoaW52b2ljZUlkICYmIGN1cnJlbnRPcmRlckludm9pY2VJZCAmJiBpbnZvaWNlSWQgPT09IGN1cnJlbnRPcmRlckludm9pY2VJZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIGNvbnN0IGN1cnJlbnRQcm9kdWN0SWQgPSBOdW1iZXIocHJvZHVjdEVkaXRCdG4uZGF0YSgncHJvZHVjdC1pZCcpKTtcbiAgICAgIGNvbnN0IGN1cnJlbnRDb21iaW5hdGlvbklkID0gTnVtYmVyKHByb2R1Y3RFZGl0QnRuLmRhdGEoJ2NvbWJpbmF0aW9uLWlkJykpO1xuXG4gICAgICBpZiAoY3VycmVudFByb2R1Y3RJZCAhPT0gZXhwZWN0ZWRQcm9kdWN0SWQgfHwgY3VycmVudENvbWJpbmF0aW9uSWQgIT09IGV4cGVjdGVkQ29tYmluYXRpb25JZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIGlmIChleHBlY3RlZEdpdmVuUHJpY2UgIT09IE51bWJlcihwcm9kdWN0RWRpdEJ0bi5kYXRhKCdwcm9kdWN0LXByaWNlLXRheC1pbmNsJykpKSB7XG4gICAgICAgIHVubWF0Y2hpbmdQcmljZUV4aXN0cyA9IHRydWU7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICByZXR1cm4gIXVubWF0Y2hpbmdQcmljZUV4aXN0cztcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcmljZXMtcmVmcmVzaGVyLmpzIiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczcub2JqZWN0LnZhbHVlcycpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuT2JqZWN0LnZhbHVlcztcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC92YWx1ZXMuanNcbi8vIG1vZHVsZSBpZCA9IDIwN1xuLy8gbW9kdWxlIGNodW5rcyA9IDMgMTAiLCJ2YXIgZ2V0S2V5cyAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWtleXMnKVxuICAsIHRvSU9iamVjdCA9IHJlcXVpcmUoJy4vX3RvLWlvYmplY3QnKVxuICAsIGlzRW51bSAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1waWUnKS5mO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpc0VudHJpZXMpe1xuICByZXR1cm4gZnVuY3Rpb24oaXQpe1xuICAgIHZhciBPICAgICAgPSB0b0lPYmplY3QoaXQpXG4gICAgICAsIGtleXMgICA9IGdldEtleXMoTylcbiAgICAgICwgbGVuZ3RoID0ga2V5cy5sZW5ndGhcbiAgICAgICwgaSAgICAgID0gMFxuICAgICAgLCByZXN1bHQgPSBbXVxuICAgICAgLCBrZXk7XG4gICAgd2hpbGUobGVuZ3RoID4gaSlpZihpc0VudW0uY2FsbChPLCBrZXkgPSBrZXlzW2krK10pKXtcbiAgICAgIHJlc3VsdC5wdXNoKGlzRW50cmllcyA/IFtrZXksIE9ba2V5XV0gOiBPW2tleV0pO1xuICAgIH0gcmV0dXJuIHJlc3VsdDtcbiAgfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtdG8tYXJyYXkuanNcbi8vIG1vZHVsZSBpZCA9IDIwOFxuLy8gbW9kdWxlIGNodW5rcyA9IDMgMTAiLCIvLyBodHRwczovL2dpdGh1Yi5jb20vdGMzOS9wcm9wb3NhbC1vYmplY3QtdmFsdWVzLWVudHJpZXNcbnZhciAkZXhwb3J0ID0gcmVxdWlyZSgnLi9fZXhwb3J0JylcbiAgLCAkdmFsdWVzID0gcmVxdWlyZSgnLi9fb2JqZWN0LXRvLWFycmF5JykoZmFsc2UpO1xuXG4kZXhwb3J0KCRleHBvcnQuUywgJ09iamVjdCcsIHtcbiAgdmFsdWVzOiBmdW5jdGlvbiB2YWx1ZXMoaXQpe1xuICAgIHJldHVybiAkdmFsdWVzKGl0KTtcbiAgfVxufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNy5vYmplY3QudmFsdWVzLmpzXG4vLyBtb2R1bGUgaWQgPSAyMDlcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDEwIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPcmRlclByaWNlcyB7XG4gIGNhbGN1bGF0ZVRheEV4Y2x1ZGVkKHRheEluY2x1ZGVkLCB0YXhSYXRlUGVyQ2VudCwgY3VycmVuY3lQcmVjaXNpb24pIHtcbiAgICBsZXQgcHJpY2VUYXhJbmNsID0gcGFyc2VGbG9hdCh0YXhJbmNsdWRlZCk7XG4gICAgaWYgKHByaWNlVGF4SW5jbCA8IDAgfHwgTnVtYmVyLmlzTmFOKHByaWNlVGF4SW5jbCkpIHtcbiAgICAgIHByaWNlVGF4SW5jbCA9IDA7XG4gICAgfVxuICAgIGNvbnN0IHRheFJhdGUgPSB0YXhSYXRlUGVyQ2VudCAvIDEwMCArIDE7XG4gICAgcmV0dXJuIHdpbmRvdy5wc19yb3VuZChwcmljZVRheEluY2wgLyB0YXhSYXRlLCBjdXJyZW5jeVByZWNpc2lvbik7XG4gIH1cblxuICBjYWxjdWxhdGVUYXhJbmNsdWRlZCh0YXhFeGNsdWRlZCwgdGF4UmF0ZVBlckNlbnQsIGN1cnJlbmN5UHJlY2lzaW9uKSB7XG4gICAgbGV0IHByaWNlVGF4RXhjbCA9IHBhcnNlRmxvYXQodGF4RXhjbHVkZWQpO1xuICAgIGlmIChwcmljZVRheEV4Y2wgPCAwIHx8IE51bWJlci5pc05hTihwcmljZVRheEV4Y2wpKSB7XG4gICAgICBwcmljZVRheEV4Y2wgPSAwO1xuICAgIH1cbiAgICBjb25zdCB0YXhSYXRlID0gdGF4UmF0ZVBlckNlbnQgLyAxMDAgKyAxO1xuICAgIHJldHVybiB3aW5kb3cucHNfcm91bmQocHJpY2VUYXhFeGNsICogdGF4UmF0ZSwgY3VycmVuY3lQcmVjaXNpb24pO1xuICB9XG5cbiAgY2FsY3VsYXRlVG90YWxQcmljZShxdWFudGl0eSwgdW5pdFByaWNlLCBjdXJyZW5jeVByZWNpc2lvbikge1xuICAgIHJldHVybiB3aW5kb3cucHNfcm91bmQodW5pdFByaWNlICogcXVhbnRpdHksIGN1cnJlbmN5UHJlY2lzaW9uKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcmljZXMuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gJ0BwYWdlcy9vcmRlci9PcmRlclZpZXdQYWdlTWFwJztcbmltcG9ydCBPcmRlclByb2R1Y3RFZGl0IGZyb20gJ0BwYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtZWRpdCc7XG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3JkZXJQcm9kdWN0UmVuZGVyZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgfVxuXG4gIGFkZE9yVXBkYXRlUHJvZHVjdFRvTGlzdCgkcHJvZHVjdFJvdywgbmV3Um93KSB7XG4gICAgaWYgKCRwcm9kdWN0Um93Lmxlbmd0aCA+IDApIHtcbiAgICAgICRwcm9kdWN0Um93Lmh0bWwoJChuZXdSb3cpLmh0bWwoKSk7XG4gICAgfSBlbHNlIHtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkUm93KS5iZWZvcmUoJChuZXdSb3cpLmhpZGUoKS5mYWRlSW4oKSk7XG4gICAgfVxuICB9XG5cbiAgdXBkYXRlTnVtUHJvZHVjdHMobnVtUHJvZHVjdHMpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNDb3VudCkuaHRtbChudW1Qcm9kdWN0cyk7XG4gIH1cblxuICBlZGl0UHJvZHVjdEZyb21MaXN0KFxuICAgIG9yZGVyRGV0YWlsSWQsXG4gICAgcXVhbnRpdHksXG4gICAgcHJpY2VUYXhJbmNsLFxuICAgIHByaWNlVGF4RXhjbCxcbiAgICB0YXhSYXRlLFxuICAgIGxvY2F0aW9uLFxuICAgIGF2YWlsYWJsZVF1YW50aXR5LFxuICAgIGF2YWlsYWJsZU91dE9mU3RvY2ssXG4gICAgb3JkZXJJbnZvaWNlSWQsXG4gICkge1xuICAgIGNvbnN0ICRvcmRlckVkaXQgPSBuZXcgT3JkZXJQcm9kdWN0RWRpdChvcmRlckRldGFpbElkKTtcbiAgICAkb3JkZXJFZGl0LmRpc3BsYXlQcm9kdWN0KHtcbiAgICAgIHByaWNlX3RheF9leGNsOiBwcmljZVRheEV4Y2wsXG4gICAgICBwcmljZV90YXhfaW5jbDogcHJpY2VUYXhJbmNsLFxuICAgICAgdGF4X3JhdGU6IHRheFJhdGUsXG4gICAgICBxdWFudGl0eSxcbiAgICAgIGxvY2F0aW9uLFxuICAgICAgYXZhaWxhYmxlUXVhbnRpdHksXG4gICAgICBhdmFpbGFibGVPdXRPZlN0b2NrLFxuICAgICAgb3JkZXJJbnZvaWNlSWQsXG4gICAgfSk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRBY3Rpb25CdG4pLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZFJvdykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgbW92ZVByb2R1Y3RzUGFuZWxUb01vZGlmaWNhdGlvblBvc2l0aW9uKHNjcm9sbFRhcmdldCA9ICdib2R5Jykge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWN0aW9uQnRuKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgJChgJHtPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRBY3Rpb25CdG59LCAke09yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZFJvd31gKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgdGhpcy5tb3ZlUHJvZHVjdFBhbmVsVG9Ub3Aoc2Nyb2xsVGFyZ2V0KTtcbiAgfVxuXG4gIG1vdmVQcm9kdWN0c1BhbmVsVG9SZWZ1bmRQb3NpdGlvbigpIHtcbiAgICB0aGlzLnJlc2V0QWxsRWRpdFJvd3MoKTtcbiAgICAkKGAke09yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZEFjdGlvbkJ0bn0sICR7T3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkUm93fSwgJHtPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBY3Rpb25CdG59YCkuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIHRoaXMubW92ZVByb2R1Y3RQYW5lbFRvVG9wKCk7XG4gIH1cblxuICBtb3ZlUHJvZHVjdFBhbmVsVG9Ub3Aoc2Nyb2xsVGFyZ2V0ID0gJ2JvZHknKSB7XG4gICAgY29uc3QgJG1vZGlmaWNhdGlvblBvc2l0aW9uID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RNb2RpZmljYXRpb25Qb3NpdGlvbik7XG4gICAgaWYgKCRtb2RpZmljYXRpb25Qb3NpdGlvbi5maW5kKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNQYW5lbCkubGVuZ3RoID4gMCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNQYW5lbCkuZGV0YWNoKCkuYXBwZW5kVG8oJG1vZGlmaWNhdGlvblBvc2l0aW9uKTtcbiAgICAkbW9kaWZpY2F0aW9uUG9zaXRpb24uY2xvc2VzdCgnLnJvdycpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcblxuICAgIC8vIFNob3cgY29sdW1uIGxvY2F0aW9uICYgcmVmdW5kZWRcbiAgICB0aGlzLnRvZ2dsZUNvbHVtbihPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzQ2VsbExvY2F0aW9uKTtcbiAgICB0aGlzLnRvZ2dsZUNvbHVtbihPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzQ2VsbFJlZnVuZGVkKTtcblxuICAgIC8vIFNob3cgYWxsIHJvd3MsIGhpZGUgcGFnaW5hdGlvbiBjb250cm9sc1xuICAgIGNvbnN0ICRyb3dzID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGUpLmZpbmQoJ3RyW2lkXj1cIm9yZGVyUHJvZHVjdF9cIl0nKTtcbiAgICAkcm93cy5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzUGFnaW5hdGlvbikuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuXG4gICAgY29uc3Qgc2Nyb2xsVmFsdWUgPSAkKHNjcm9sbFRhcmdldCkub2Zmc2V0KCkudG9wIC0gJCgnLmhlYWRlci10b29sYmFyJykuaGVpZ2h0KCkgLSAxMDA7XG4gICAgJCgnaHRtbCxib2R5JykuYW5pbWF0ZSh7c2Nyb2xsVG9wOiBzY3JvbGxWYWx1ZX0sICdzbG93Jyk7XG4gIH1cblxuICBtb3ZlUHJvZHVjdFBhbmVsVG9PcmlnaW5hbFBvc2l0aW9uKCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkTmV3SW52b2ljZUluZm8pLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdE1vZGlmaWNhdGlvblBvc2l0aW9uKS5jbG9zZXN0KCcucm93JykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuXG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzUGFuZWwpLmRldGFjaCgpLmFwcGVuZFRvKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdE9yaWdpbmFsUG9zaXRpb24pO1xuXG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzUGFnaW5hdGlvbikucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWN0aW9uQnRuKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgJChgJHtPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRBY3Rpb25CdG59LCAke09yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZFJvd31gKS5hZGRDbGFzcygnZC1ub25lJyk7XG5cbiAgICAvLyBSZXN0b3JlIHBhZ2luYXRpb25cbiAgICB0aGlzLnBhZ2luYXRlKDEpO1xuICB9XG5cbiAgcmVzZXRBZGRSb3coKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRJZElucHV0KS52YWwoJycpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0U2VhcmNoSW5wdXQpLnZhbCgnJyk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRDb21iaW5hdGlvbnNCbG9jaykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkQ29tYmluYXRpb25zU2VsZWN0KS52YWwoJycpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkQ29tYmluYXRpb25zU2VsZWN0KS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZFByaWNlVGF4RXhjbElucHV0KS52YWwoJycpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkUHJpY2VUYXhJbmNsSW5wdXQpLnZhbCgnJyk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRRdWFudGl0eUlucHV0KS52YWwoJycpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkQXZhaWxhYmxlVGV4dCkuaHRtbCgnJyk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRMb2NhdGlvblRleHQpLmh0bWwoJycpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkTmV3SW52b2ljZUluZm8pLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZEFjdGlvbkJ0bikucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcbiAgfVxuXG4gIHJlc2V0QWxsRWRpdFJvd3MoKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0QnV0dG9ucykuZWFjaCgoa2V5LCBlZGl0QnV0dG9uKSA9PiB7XG4gICAgICB0aGlzLnJlc2V0RWRpdFJvdygkKGVkaXRCdXR0b24pLmRhdGEoJ29yZGVyRGV0YWlsSWQnKSk7XG4gICAgfSk7XG4gIH1cblxuICByZXNldEVkaXRSb3cob3JkZXJQcm9kdWN0SWQpIHtcbiAgICBjb25zdCAkcHJvZHVjdFJvdyA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUm93KG9yZGVyUHJvZHVjdElkKSk7XG4gICAgY29uc3QgJHByb2R1Y3RFZGl0Um93ID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVSb3dFZGl0ZWQob3JkZXJQcm9kdWN0SWQpKTtcbiAgICAkcHJvZHVjdEVkaXRSb3cucmVtb3ZlKCk7XG4gICAgJHByb2R1Y3RSb3cucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgcGFnaW5hdGUobnVtUGFnZSkge1xuICAgIGNvbnN0ICRyb3dzID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGUpLmZpbmQoJ3RyW2lkXj1cIm9yZGVyUHJvZHVjdF9cIl0nKTtcbiAgICBjb25zdCAkY3VzdG9taXphdGlvblJvd3MgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZUN1c3RvbWl6YXRpb25Sb3dzKTtcbiAgICBjb25zdCAkdGFibGVQYWdpbmF0aW9uID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uKTtcbiAgICBjb25zdCBudW1Sb3dzUGVyUGFnZSA9IHBhcnNlSW50KCR0YWJsZVBhZ2luYXRpb24uZGF0YSgnbnVtUGVyUGFnZScpLCAxMCk7XG4gICAgY29uc3QgbWF4UGFnZSA9IE1hdGguY2VpbCgkcm93cy5sZW5ndGggLyBudW1Sb3dzUGVyUGFnZSk7XG4gICAgbnVtUGFnZSA9IE1hdGgubWF4KDEsIE1hdGgubWluKG51bVBhZ2UsIG1heFBhZ2UpKTtcbiAgICB0aGlzLnBhZ2luYXRlVXBkYXRlQ29udHJvbHMobnVtUGFnZSk7XG5cbiAgICAvLyBIaWRlIGFsbCByb3dzLi4uXG4gICAgJHJvd3MuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICRjdXN0b21pemF0aW9uUm93cy5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgLy8gLi4uIGFuZCBkaXNwbGF5IGdvb2Qgb25lc1xuXG4gICAgY29uc3Qgc3RhcnRSb3cgPSAoKG51bVBhZ2UgLSAxKSAqIG51bVJvd3NQZXJQYWdlKSArIDE7XG4gICAgY29uc3QgZW5kUm93ID0gbnVtUGFnZSAqIG51bVJvd3NQZXJQYWdlO1xuICAgIGZvciAobGV0IGkgPSBzdGFydFJvdy0xOyBpIDwgTWF0aC5taW4oZW5kUm93LCAkcm93cy5sZW5ndGgpOyBpKyspIHtcbiAgICAgICQoJHJvd3NbaV0pLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICB9XG4gICAgJGN1c3RvbWl6YXRpb25Sb3dzLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgaWYgKCEkKHRoaXMpLnByZXYoKS5oYXNDbGFzcygnZC1ub25lJykpIHtcbiAgICAgICAgJCh0aGlzKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICAvLyBSZW1vdmUgYWxsIGVkaXRpb24gcm93cyAoY2FyZWZ1bCBub3QgdG8gcmVtb3ZlIHRoZSB0ZW1wbGF0ZSlcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRSb3cpLm5vdChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0Um93VGVtcGxhdGUpLnJlbW92ZSgpO1xuXG4gICAgLy8gVG9nZ2xlIENvbHVtbiBMb2NhdGlvbiAmIFJlZnVuZGVkXG4gICAgdGhpcy50b2dnbGVDb2x1bW4oT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c0NlbGxMb2NhdGlvbkRpc3BsYXllZCk7XG4gICAgdGhpcy50b2dnbGVDb2x1bW4oT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c0NlbGxSZWZ1bmRlZERpc3BsYXllZCk7XG4gIH1cblxuICBwYWdpbmF0ZVVwZGF0ZUNvbnRyb2xzKG51bVBhZ2UpIHtcbiAgICAvLyBXaHkgMyA/IE5leHQgJiBQcmV2ICYgVGVtcGxhdGVcbiAgICBjb25zdCB0b3RhbFBhZ2UgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb24pLmZpbmQoJ2xpLnBhZ2UtaXRlbScpLmxlbmd0aCAtIDM7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uKS5maW5kKCcuYWN0aXZlJykucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUGFnaW5hdGlvbikuZmluZChgbGk6aGFzKD4gW2RhdGEtcGFnZT1cIiR7bnVtUGFnZX1cIl0pYCkuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUGFnaW5hdGlvblByZXYpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuICAgIGlmIChudW1QYWdlID09PSAxKSB7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb25QcmV2KS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcbiAgICB9XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uTmV4dCkucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgaWYgKG51bVBhZ2UgPT09IHRvdGFsUGFnZSkge1xuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uTmV4dCkuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgfVxuICAgIHRoaXMudG9nZ2xlUGFnaW5hdGlvbkNvbnRyb2xzKCk7XG4gIH1cblxuICB1cGRhdGVOdW1QZXJQYWdlKG51bVBlclBhZ2UpIHtcbiAgICBpZiAobnVtUGVyUGFnZSA8IDEpIHtcbiAgICAgIG51bVBlclBhZ2UgPSAxO1xuICAgIH1cbiAgICBjb25zdCAkcm93cyA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlKS5maW5kKCd0cltpZF49XCJvcmRlclByb2R1Y3RfXCJdJyk7XG4gICAgY29uc3QgJHRhYmxlUGFnaW5hdGlvbiA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUGFnaW5hdGlvbik7XG4gICAgY29uc3QgbnVtUGFnZXMgPSBNYXRoLmNlaWwoJHJvd3MubGVuZ3RoIC8gbnVtUGVyUGFnZSk7XG5cbiAgICAvLyBVcGRhdGUgdGFibGUgZGF0YSBmaWVsZHNcbiAgICAkdGFibGVQYWdpbmF0aW9uLmRhdGEoJ251bVBhZ2VzJywgbnVtUGFnZXMpO1xuICAgICR0YWJsZVBhZ2luYXRpb24uZGF0YSgnbnVtUGVyUGFnZScsIG51bVBlclBhZ2UpO1xuXG4gICAgLy8gQ2xlYW4gYWxsIHBhZ2UgbGlua3MsIHJlaW5zZXJ0IHRoZSByZW1vdmVkIHRlbXBsYXRlXG4gICAgY29uc3QgJGxpbmtQYWdpbmF0aW9uVGVtcGxhdGUgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb25UZW1wbGF0ZSk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uKS5maW5kKGBsaTpoYXMoPiBbZGF0YS1wYWdlXSlgKS5yZW1vdmUoKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb25OZXh0KS5iZWZvcmUoJGxpbmtQYWdpbmF0aW9uVGVtcGxhdGUpO1xuXG4gICAgLy8gQWRkIGFwcHJvcHJpYXRlIHBhZ2VzXG4gICAgZm9yIChsZXQgaSA9IDE7IGkgPD0gbnVtUGFnZXM7ICsraSkge1xuICAgICAgY29uc3QgJGxpbmtQYWdpbmF0aW9uID0gJGxpbmtQYWdpbmF0aW9uVGVtcGxhdGUuY2xvbmUoKTtcbiAgICAgICRsaW5rUGFnaW5hdGlvbi5maW5kKCdzcGFuJykuYXR0cignZGF0YS1wYWdlJywgaSk7XG4gICAgICAkbGlua1BhZ2luYXRpb24uZmluZCgnc3BhbicpLmh0bWwoaSk7XG4gICAgICAkbGlua1BhZ2luYXRpb25UZW1wbGF0ZS5iZWZvcmUoJGxpbmtQYWdpbmF0aW9uLnJlbW92ZUNsYXNzKCdkLW5vbmUnKSk7XG4gICAgfVxuICB9XG5cbiAgcGFnaW5hdGlvbkFkZFBhZ2UobnVtUGFnZSkge1xuICAgIGNvbnN0ICR0YWJsZVBhZ2luYXRpb24gPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb24pO1xuICAgICR0YWJsZVBhZ2luYXRpb24uZGF0YSgnbnVtUGFnZXMnLCBudW1QYWdlKTtcbiAgICBjb25zdCAkbGlua1BhZ2luYXRpb24gPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb25UZW1wbGF0ZSkuY2xvbmUoKTtcbiAgICAkbGlua1BhZ2luYXRpb24uZmluZCgnc3BhbicpLmF0dHIoJ2RhdGEtcGFnZScsIG51bVBhZ2UpO1xuICAgICRsaW5rUGFnaW5hdGlvbi5maW5kKCdzcGFuJykuaHRtbChudW1QYWdlKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb25UZW1wbGF0ZSkuYmVmb3JlKCRsaW5rUGFnaW5hdGlvbi5yZW1vdmVDbGFzcygnZC1ub25lJykpO1xuICAgIHRoaXMudG9nZ2xlUGFnaW5hdGlvbkNvbnRyb2xzKCk7XG4gIH1cblxuICBwYWdpbmF0aW9uUmVtb3ZlUGFnZShudW1QYWdlKSB7XG4gICAgY29uc3QgJHRhYmxlUGFnaW5hdGlvbiA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUGFnaW5hdGlvbik7XG4gICAgY29uc3QgbnVtUGFnZXMgPSAkdGFibGVQYWdpbmF0aW9uLmRhdGEoJ251bVBhZ2VzJyk7XG4gICAgJHRhYmxlUGFnaW5hdGlvbi5kYXRhKCdudW1QYWdlcycsIG51bVBhZ2VzIC0gMSk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uKS5maW5kKGBsaTpoYXMoPiBbZGF0YS1wYWdlPVwiJHtudW1QYWdlfVwiXSlgKS5yZW1vdmUoKTtcbiAgICB0aGlzLnRvZ2dsZVBhZ2luYXRpb25Db250cm9scygpO1xuICB9XG5cbiAgdG9nZ2xlUGFnaW5hdGlvbkNvbnRyb2xzKCkge1xuICAgIC8vIFdoeSAzID8gTmV4dCAmIFByZXYgJiBUZW1wbGF0ZVxuICAgIGNvbnN0IHRvdGFsUGFnZSA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUGFnaW5hdGlvbikuZmluZCgnbGkucGFnZS1pdGVtJykubGVuZ3RoIC0gMztcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNOYXZQYWdpbmF0aW9uKS50b2dnbGVDbGFzcygnZC1ub25lJywgdG90YWxQYWdlIDw9IDEpO1xuICB9XG5cbiAgdG9nZ2xlUHJvZHVjdEFkZE5ld0ludm9pY2VJbmZvKCkge1xuICAgIGlmIChwYXJzZUludCgkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZEludm9pY2VTZWxlY3QpLnZhbCgpLCAxMCkgPT09IDApIHtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkTmV3SW52b2ljZUluZm8pLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICB9IGVsc2Uge1xuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGROZXdJbnZvaWNlSW5mbykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIH1cbiAgfVxuXG4gIHRvZ2dsZUNvbHVtbih0YXJnZXQsIGZvcmNlRGlzcGxheSA9IG51bGwpIHtcbiAgICBsZXQgaXNDb2x1bW5EaXNwbGF5ZWQgPSBmYWxzZTtcbiAgICBpZiAoZm9yY2VEaXNwbGF5ID09PSBudWxsKSB7XG4gICAgICAkKHRhcmdldCkuZmlsdGVyKCd0ZCcpLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgIGlmICgkKHRoaXMpLmh0bWwoKS50cmltKCkgIT09ICcnKSB7XG4gICAgICAgICAgaXNDb2x1bW5EaXNwbGF5ZWQgPSB0cnVlO1xuICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIGlzQ29sdW1uRGlzcGxheWVkID0gZm9yY2VEaXNwbGF5O1xuICAgIH1cbiAgICAkKHRhcmdldCkudG9nZ2xlQ2xhc3MoJ2Qtbm9uZScsICFpc0NvbHVtbkRpc3BsYXllZCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcHJvZHVjdC1yZW5kZXJlci5qcyIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9udW1iZXIvaXMtbmFuXCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9udW1iZXIvaXMtbmFuLmpzXG4vLyBtb2R1bGUgaWQgPSAyMzFcbi8vIG1vZHVsZSBjaHVua3MgPSAzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogVGV4dFdpdGhMZW5ndGhDb3VudGVyIGhhbmRsZXMgaW5wdXQgd2l0aCBsZW5ndGggY291bnRlciBVSS5cbiAqXG4gKiBVc2FnZTpcbiAqXG4gKiBUaGVyZSBtdXN0IGJlIGFuIGVsZW1lbnQgdGhhdCB3cmFwcyBib3RoIGlucHV0ICYgY291bnRlciBkaXNwbGF5IHdpdGggXCIuanMtdGV4dC13aXRoLWxlbmd0aC1jb3VudGVyXCIgY2xhc3MuXG4gKiBDb3VudGVyIGRpc3BsYXkgbXVzdCBoYXZlIFwiLmpzLWNvdW50YWJsZS10ZXh0LWRpc3BsYXlcIiBjbGFzcyBhbmQgaW5wdXQgbXVzdCBoYXZlIFwiLmpzLWNvdW50YWJsZS10ZXh0LWlucHV0XCIgY2xhc3MuXG4gKiBUZXh0IGlucHV0IG11c3QgaGF2ZSBcImRhdGEtbWF4LWxlbmd0aFwiIGF0dHJpYnV0ZS5cbiAqXG4gKiA8ZGl2IGNsYXNzPVwianMtdGV4dC13aXRoLWxlbmd0aC1jb3VudGVyXCI+XG4gKiAgPHNwYW4gY2xhc3M9XCJqcy1jb3VudGFibGUtdGV4dFwiPjwvc3Bhbj5cbiAqICA8aW5wdXQgY2xhc3M9XCJqcy1jb3VudGFibGUtaW5wdXRcIiBkYXRhLW1heC1sZW5ndGg9XCIyNTVcIj5cbiAqIDwvZGl2PlxuICpcbiAqIEluIEphdmFzY3JpcHQgeW91IG11c3QgZW5hYmxlIHRoaXMgY29tcG9uZW50OlxuICpcbiAqIG5ldyBUZXh0V2l0aExlbmd0aENvdW50ZXIoKTtcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgVGV4dFdpdGhMZW5ndGhDb3VudGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy53cmFwcGVyU2VsZWN0b3IgPSAnLmpzLXRleHQtd2l0aC1sZW5ndGgtY291bnRlcic7XG4gICAgdGhpcy50ZXh0U2VsZWN0b3IgPSAnLmpzLWNvdW50YWJsZS10ZXh0JztcbiAgICB0aGlzLmlucHV0U2VsZWN0b3IgPSAnLmpzLWNvdW50YWJsZS1pbnB1dCc7XG5cbiAgICAkKGRvY3VtZW50KS5vbignaW5wdXQnLCBgJHt0aGlzLndyYXBwZXJTZWxlY3Rvcn0gJHt0aGlzLmlucHV0U2VsZWN0b3J9YCwgKGUpID0+IHtcbiAgICAgIGNvbnN0ICRpbnB1dCA9ICQoZS5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0IHJlbWFpbmluZ0xlbmd0aCA9ICRpbnB1dC5kYXRhKCdtYXgtbGVuZ3RoJykgLSAkaW5wdXQudmFsKCkubGVuZ3RoO1xuXG4gICAgICAkaW5wdXQuY2xvc2VzdCh0aGlzLndyYXBwZXJTZWxlY3RvcikuZmluZCh0aGlzLnRleHRTZWxlY3RvcikudGV4dChyZW1haW5pbmdMZW5ndGgpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2Zvcm0vdGV4dC13aXRoLWxlbmd0aC1jb3VudGVyLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICcuLi9PcmRlclZpZXdQYWdlTWFwJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIEFsbCBhY3Rpb25zIGZvciBvcmRlciB2aWV3IHBhZ2UgbWVzc2FnZXMgYXJlIHJlZ2lzdGVyZWQgaW4gdGhpcyBjbGFzcy5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3JkZXJWaWV3UGFnZU1lc3NhZ2VzSGFuZGxlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuJG9yZGVyTWVzc2FnZUNoYW5nZVdhcm5pbmcgPSAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJNZXNzYWdlQ2hhbmdlV2FybmluZyk7XG4gICAgdGhpcy4kbWVzc2FnZXNDb250YWluZXIgPSAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJNZXNzYWdlc0NvbnRhaW5lcik7XG5cbiAgICByZXR1cm4ge1xuICAgICAgbGlzdGVuRm9yUHJlZGVmaW5lZE1lc3NhZ2VTZWxlY3Rpb246ICgpID0+IHRoaXMuX2hhbmRsZVByZWRlZmluZWRNZXNzYWdlU2VsZWN0aW9uKCksXG4gICAgICBsaXN0ZW5Gb3JGdWxsTWVzc2FnZXNPcGVuOiAoKSA9PiB0aGlzLl9vbkZ1bGxNZXNzYWdlc09wZW4oKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZXMgcHJlZGVmaW5lZCBvcmRlciBtZXNzYWdlIHNlbGVjdGlvbi5cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVQcmVkZWZpbmVkTWVzc2FnZVNlbGVjdGlvbigpIHtcbiAgICAkKGRvY3VtZW50KS5vbignY2hhbmdlJywgT3JkZXJWaWV3UGFnZU1hcC5vcmRlck1lc3NhZ2VOYW1lU2VsZWN0LCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGN1cnJlbnRJdGVtID0gJChlLmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgdmFsdWVJZCA9ICRjdXJyZW50SXRlbS52YWwoKTtcblxuICAgICAgaWYgKCF2YWx1ZUlkKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgY29uc3QgbWVzc2FnZSA9IHRoaXMuJG1lc3NhZ2VzQ29udGFpbmVyLmZpbmQoYGRpdltkYXRhLWlkPSR7dmFsdWVJZH1dYCkudGV4dCgpLnRyaW0oKTtcbiAgICAgIGNvbnN0ICRvcmRlck1lc3NhZ2UgPSAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJNZXNzYWdlKTtcbiAgICAgIGNvbnN0IGlzU2FtZU1lc3NhZ2UgPSAkb3JkZXJNZXNzYWdlLnZhbCgpLnRyaW0oKSA9PT0gbWVzc2FnZTtcblxuICAgICAgaWYgKGlzU2FtZU1lc3NhZ2UpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBpZiAoJG9yZGVyTWVzc2FnZS52YWwoKSAmJiAhY29uZmlybSh0aGlzLiRvcmRlck1lc3NhZ2VDaGFuZ2VXYXJuaW5nLnRleHQoKSkpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICAkb3JkZXJNZXNzYWdlLnZhbChtZXNzYWdlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBldmVudCB3aGVuIGFsbCBtZXNzYWdlcyBtb2RhbCBpcyBiZWluZyBvcGVuZWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkZ1bGxNZXNzYWdlc09wZW4oKSB7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgT3JkZXJWaWV3UGFnZU1hcC5vcGVuQWxsTWVzc2FnZXNCdG4sICgpID0+IHRoaXMuX3Njcm9sbFRvTXNnTGlzdEJvdHRvbSgpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTY3JvbGxzIGRvd24gdG8gdGhlIGJvdHRvbSBvZiBhbGwgbWVzc2FnZXMgbGlzdFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Njcm9sbFRvTXNnTGlzdEJvdHRvbSgpIHtcbiAgICBjb25zdCAkbXNnTW9kYWwgPSAkKE9yZGVyVmlld1BhZ2VNYXAuYWxsTWVzc2FnZXNNb2RhbCk7XG4gICAgY29uc3QgbXNnTGlzdCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoT3JkZXJWaWV3UGFnZU1hcC5hbGxNZXNzYWdlc0xpc3QpO1xuXG4gICAgY29uc3QgY2xhc3NDaGVja0ludGVydmFsID0gd2luZG93LnNldEludGVydmFsKCgpID0+IHtcbiAgICAgIGlmICgkbXNnTW9kYWwuaGFzQ2xhc3MoJ3Nob3cnKSkge1xuICAgICAgICBtc2dMaXN0LnNjcm9sbFRvcCA9IG1zZ0xpc3Quc2Nyb2xsSGVpZ2h0O1xuICAgICAgICBjbGVhckludGVydmFsKGNsYXNzQ2hlY2tJbnRlcnZhbCk7XG4gICAgICB9XG4gICAgfSwgMTApO1xuXG5cbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvbWVzc2FnZS9vcmRlci12aWV3LXBhZ2UtbWVzc2FnZXMtaGFuZGxlci5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gJy4vT3JkZXJWaWV3UGFnZU1hcCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3JkZXJTaGlwcGluZ01hbmFnZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLl9pbml0T3JkZXJTaGlwcGluZ1VwZGF0ZUV2ZW50SGFuZGxlcigpO1xuICB9XG5cbiAgX2luaXRPcmRlclNoaXBwaW5nVXBkYXRlRXZlbnRIYW5kbGVyKCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5zaG93T3JkZXJTaGlwcGluZ1VwZGF0ZU1vZGFsQnRuKS5vbignY2xpY2snLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRidG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAudXBkYXRlT3JkZXJTaGlwcGluZ1RyYWNraW5nTnVtYmVySW5wdXQpLnZhbCgkYnRuLmRhdGEoJ29yZGVyLXRyYWNraW5nLW51bWJlcicpKTtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVPcmRlclNoaXBwaW5nQ3VycmVudE9yZGVyQ2FycmllcklkSW5wdXQpLnZhbCgkYnRuLmRhdGEoJ29yZGVyLWNhcnJpZXItaWQnKSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL29yZGVyLXNoaXBwaW5nLW1hbmFnZXIuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICdAcGFnZXMvb3JkZXIvT3JkZXJWaWV3UGFnZU1hcCc7XG5cbmNvbnN0IHskfSA9IHdpbmRvdztcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3JkZXJQcm9kdWN0QXV0b2NvbXBsZXRlIHtcbiAgY29uc3RydWN0b3IoaW5wdXQpIHtcbiAgICB0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3QgPSBudWxsO1xuICAgIHRoaXMucm91dGVyID0gbmV3IFJvdXRlcigpO1xuICAgIHRoaXMuaW5wdXQgPSBpbnB1dDtcbiAgICB0aGlzLnJlc3VsdHMgPSBbXTtcbiAgICB0aGlzLmRyb3Bkb3duTWVudSA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0U2VhcmNoSW5wdXRBdXRvY29tcGxldGVNZW51KTtcbiAgICAvKipcbiAgICAgKiBQZXJtaXQgdG8gbGluayB0byBlYWNoIHZhbHVlIG9mIGRyb3Bkb3duIGEgY2FsbGJhY2sgYWZ0ZXIgaXRlbSBpcyBjbGlja2VkXG4gICAgICovXG4gICAgdGhpcy5vbkl0ZW1DbGlja2VkQ2FsbGJhY2sgPSAoKSA9PiB7fTtcbiAgfVxuXG4gIGxpc3RlbkZvclNlYXJjaCgpIHtcbiAgICB0aGlzLmlucHV0Lm9uKCdjbGljaycsIGV2ZW50ID0+IHtcbiAgICAgIGV2ZW50LnN0b3BJbW1lZGlhdGVQcm9wYWdhdGlvbigpO1xuICAgICAgdGhpcy51cGRhdGVSZXN1bHRzKHRoaXMucmVzdWx0cyk7XG4gICAgfSk7XG5cbiAgICB0aGlzLmlucHV0Lm9uKCdrZXl1cCcsIGV2ZW50ID0+IHRoaXMuZGVsYXlTZWFyY2goZXZlbnQuY3VycmVudFRhcmdldCkpO1xuXG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgKCkgPT4gdGhpcy5kcm9wZG93bk1lbnUuaGlkZSgpKTtcbiAgfVxuXG4gIGRlbGF5U2VhcmNoKGlucHV0KSB7XG4gICAgY2xlYXJUaW1lb3V0KHRoaXMuc2VhcmNoVGltZW91dElkKTtcblxuICAgIHRoaXMuc2VhcmNoVGltZW91dElkID0gc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICB0aGlzLnNlYXJjaChpbnB1dC52YWx1ZSwgJChpbnB1dCkuZGF0YSgnY3VycmVuY3knKSwgJChpbnB1dCkuZGF0YSgnb3JkZXInKSk7XG4gICAgfSwgMzAwKTtcbiAgfVxuXG4gIHNlYXJjaChzZWFyY2gsIGN1cnJlbmN5LCBvcmRlcklkKSB7XG4gICAgY29uc3QgcGFyYW1zID0ge3NlYXJjaF9waHJhc2U6IHNlYXJjaH07XG5cbiAgICBpZiAoY3VycmVuY3kpIHtcbiAgICAgIHBhcmFtcy5jdXJyZW5jeV9pZCA9IGN1cnJlbmN5O1xuICAgIH1cblxuICAgIGlmIChvcmRlcklkKSB7XG4gICAgICBwYXJhbXMub3JkZXJfaWQgPSBvcmRlcklkO1xuICAgIH1cblxuICAgIGlmICh0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3QgIT09IG51bGwpIHtcbiAgICAgIHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdC5hYm9ydCgpO1xuICAgIH1cblxuICAgIHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdCA9ICQuZ2V0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9vcmRlcnNfcHJvZHVjdHNfc2VhcmNoJywgcGFyYW1zKSk7XG4gICAgdGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0XG4gICAgICAudGhlbihyZXNwb25zZSA9PiB0aGlzLnVwZGF0ZVJlc3VsdHMocmVzcG9uc2UpKVxuICAgICAgLmFsd2F5cygoKSA9PiB7XG4gICAgICAgIHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdCA9IG51bGw7XG4gICAgICB9KTtcbiAgfVxuXG4gIHVwZGF0ZVJlc3VsdHMocmVzdWx0cykge1xuICAgIHRoaXMuZHJvcGRvd25NZW51LmVtcHR5KCk7XG5cbiAgICBpZiAoIXJlc3VsdHMgfHwgIXJlc3VsdHMucHJvZHVjdHMgfHwgT2JqZWN0LmtleXMocmVzdWx0cy5wcm9kdWN0cykubGVuZ3RoIDw9IDApIHtcbiAgICAgIHRoaXMuZHJvcGRvd25NZW51LmhpZGUoKTtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB0aGlzLnJlc3VsdHMgPSByZXN1bHRzLnByb2R1Y3RzO1xuXG4gICAgT2JqZWN0LnZhbHVlcyh0aGlzLnJlc3VsdHMpLmZvckVhY2godmFsID0+IHtcbiAgICAgIGNvbnN0IGxpbmsgPSAkKGA8YSBjbGFzcz1cImRyb3Bkb3duLWl0ZW1cIiBkYXRhLWlkPVwiJHt2YWwucHJvZHVjdElkfVwiIGhyZWY9XCIjXCI+JHt2YWwubmFtZX08L2E+YCk7XG5cbiAgICAgIGxpbmsub24oJ2NsaWNrJywgZXZlbnQgPT4ge1xuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICB0aGlzLm9uSXRlbUNsaWNrZWQoJChldmVudC50YXJnZXQpLmRhdGEoJ2lkJykpO1xuICAgICAgfSk7XG5cbiAgICAgIHRoaXMuZHJvcGRvd25NZW51LmFwcGVuZChsaW5rKTtcbiAgICB9KTtcblxuICAgIHRoaXMuZHJvcGRvd25NZW51LnNob3coKTtcbiAgfVxuXG4gIG9uSXRlbUNsaWNrZWQoaWQpIHtcbiAgICBjb25zdCBzZWxlY3RlZFByb2R1Y3QgPSB0aGlzLnJlc3VsdHMuZmlsdGVyKHByb2R1Y3QgPT4gcHJvZHVjdC5wcm9kdWN0SWQgPT09IGlkKTtcblxuICAgIGlmIChzZWxlY3RlZFByb2R1Y3QubGVuZ3RoICE9PSAwKSB7XG4gICAgICB0aGlzLmlucHV0LnZhbChzZWxlY3RlZFByb2R1Y3RbMF0ubmFtZSk7XG4gICAgICB0aGlzLm9uSXRlbUNsaWNrZWRDYWxsYmFjayhzZWxlY3RlZFByb2R1Y3RbMF0pO1xuICAgIH1cbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcm9kdWN0LWFkZC1hdXRvY29tcGxldGUuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCBSb3V0ZXIgZnJvbSAnQGNvbXBvbmVudHMvcm91dGVyJztcbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gJ0BwYWdlcy9vcmRlci9PcmRlclZpZXdQYWdlTWFwJztcbmltcG9ydCB7RXZlbnRFbWl0dGVyfSBmcm9tICdAY29tcG9uZW50cy9ldmVudC1lbWl0dGVyJztcbmltcG9ydCBPcmRlclZpZXdFdmVudE1hcCBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci12aWV3LWV2ZW50LW1hcCc7XG5pbXBvcnQgT3JkZXJQcmljZXMgZnJvbSAnQHBhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcHJpY2VzJztcbmltcG9ydCBPcmRlclByb2R1Y3RSZW5kZXJlciBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcm9kdWN0LXJlbmRlcmVyJztcbmltcG9ydCBDb25maXJtTW9kYWwgZnJvbSAnQGNvbXBvbmVudHMvbW9kYWwnO1xuaW1wb3J0IE9yZGVyUHJpY2VzUmVmcmVzaGVyIGZyb20gJ0BwYWdlcy9vcmRlci92aWV3L29yZGVyLXByaWNlcy1yZWZyZXNoZXInO1xuXG5jb25zdCB7JH0gPSB3aW5kb3c7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIE9yZGVyUHJvZHVjdEFkZCB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMucm91dGVyID0gbmV3IFJvdXRlcigpO1xuICAgIHRoaXMucHJvZHVjdEFkZEFjdGlvbkJ0biA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkQWN0aW9uQnRuKTtcbiAgICB0aGlzLnByb2R1Y3RJZElucHV0ID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRJZElucHV0KTtcbiAgICB0aGlzLmNvbWJpbmF0aW9uc0Jsb2NrID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRDb21iaW5hdGlvbnNCbG9jayk7XG4gICAgdGhpcy5jb21iaW5hdGlvbnNTZWxlY3QgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZENvbWJpbmF0aW9uc1NlbGVjdCk7XG4gICAgdGhpcy5wcmljZVRheEluY2x1ZGVkSW5wdXQgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZFByaWNlVGF4SW5jbElucHV0KTtcbiAgICB0aGlzLnByaWNlVGF4RXhjbHVkZWRJbnB1dCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkUHJpY2VUYXhFeGNsSW5wdXQpO1xuICAgIHRoaXMudGF4UmF0ZUlucHV0ID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRUYXhSYXRlSW5wdXQpO1xuICAgIHRoaXMucXVhbnRpdHlJbnB1dCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkUXVhbnRpdHlJbnB1dCk7XG4gICAgdGhpcy5hdmFpbGFibGVUZXh0ID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRBdmFpbGFibGVUZXh0KTtcbiAgICB0aGlzLmxvY2F0aW9uVGV4dCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkTG9jYXRpb25UZXh0KTtcbiAgICB0aGlzLnRvdGFsUHJpY2VUZXh0ID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRUb3RhbFByaWNlVGV4dCk7XG4gICAgdGhpcy5pbnZvaWNlU2VsZWN0ID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRJbnZvaWNlU2VsZWN0KTtcbiAgICB0aGlzLmZyZWVTaGlwcGluZ1NlbGVjdCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkRnJlZVNoaXBwaW5nU2VsZWN0KTtcbiAgICB0aGlzLnByb2R1Y3RBZGRNZW51QnRuID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRCdG4pO1xuICAgIHRoaXMuYXZhaWxhYmxlID0gbnVsbDtcbiAgICB0aGlzLnNldHVwTGlzdGVuZXIoKTtcbiAgICB0aGlzLnByb2R1Y3QgPSB7fTtcbiAgICB0aGlzLmN1cnJlbmN5UHJlY2lzaW9uID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGUpLmRhdGEoJ2N1cnJlbmN5UHJlY2lzaW9uJyk7XG4gICAgdGhpcy5wcmljZVRheENhbGN1bGF0b3IgPSBuZXcgT3JkZXJQcmljZXMoKTtcbiAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyID0gbmV3IE9yZGVyUHJvZHVjdFJlbmRlcmVyKCk7XG4gICAgdGhpcy5vcmRlclByaWNlc1JlZnJlc2hlciA9IG5ldyBPcmRlclByaWNlc1JlZnJlc2hlcigpO1xuICB9XG5cbiAgc2V0dXBMaXN0ZW5lcigpIHtcbiAgICB0aGlzLmNvbWJpbmF0aW9uc1NlbGVjdC5vbignY2hhbmdlJywgZXZlbnQgPT4ge1xuICAgICAgdGhpcy5wcmljZVRheEV4Y2x1ZGVkSW5wdXQudmFsKFxuICAgICAgICB3aW5kb3cucHNfcm91bmQoXG4gICAgICAgICAgJChldmVudC5jdXJyZW50VGFyZ2V0KVxuICAgICAgICAgICAgLmZpbmQoJzpzZWxlY3RlZCcpXG4gICAgICAgICAgICAuZGF0YSgncHJpY2VUYXhFeGNsdWRlZCcpLFxuICAgICAgICAgIHRoaXMuY3VycmVuY3lQcmVjaXNpb25cbiAgICAgICAgKVxuICAgICAgKTtcblxuICAgICAgdGhpcy5wcmljZVRheEluY2x1ZGVkSW5wdXQudmFsKFxuICAgICAgICB3aW5kb3cucHNfcm91bmQoXG4gICAgICAgICAgJChldmVudC5jdXJyZW50VGFyZ2V0KVxuICAgICAgICAgICAgLmZpbmQoJzpzZWxlY3RlZCcpXG4gICAgICAgICAgICAuZGF0YSgncHJpY2VUYXhJbmNsdWRlZCcpLFxuICAgICAgICAgIHRoaXMuY3VycmVuY3lQcmVjaXNpb25cbiAgICAgICAgKVxuICAgICAgKTtcblxuICAgICAgdGhpcy5sb2NhdGlvblRleHQuaHRtbChcbiAgICAgICAgJChldmVudC5jdXJyZW50VGFyZ2V0KVxuICAgICAgICAgIC5maW5kKCc6c2VsZWN0ZWQnKVxuICAgICAgICAgIC5kYXRhKCdsb2NhdGlvbicpXG4gICAgICApO1xuXG4gICAgICB0aGlzLmF2YWlsYWJsZSA9ICQoZXZlbnQuY3VycmVudFRhcmdldClcbiAgICAgICAgLmZpbmQoJzpzZWxlY3RlZCcpXG4gICAgICAgIC5kYXRhKCdzdG9jaycpO1xuXG4gICAgICB0aGlzLnF1YW50aXR5SW5wdXQudHJpZ2dlcignY2hhbmdlJyk7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLnRvZ2dsZUNvbHVtbihPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzQ2VsbExvY2F0aW9uKTtcbiAgICB9KTtcblxuICAgIHRoaXMucXVhbnRpdHlJbnB1dC5vbignY2hhbmdlIGtleXVwJywgZXZlbnQgPT4ge1xuICAgICAgaWYgKHRoaXMuYXZhaWxhYmxlICE9PSBudWxsKSB7XG4gICAgICAgIGNvbnN0IG5ld1F1YW50aXR5ID0gTnVtYmVyKGV2ZW50LnRhcmdldC52YWx1ZSk7XG4gICAgICAgIGNvbnN0IHJlbWFpbmluZ0F2YWlsYWJsZSA9IHRoaXMuYXZhaWxhYmxlIC0gbmV3UXVhbnRpdHk7XG4gICAgICAgIGNvbnN0IGF2YWlsYWJsZU91dE9mU3RvY2sgPSB0aGlzLmF2YWlsYWJsZVRleHQuZGF0YSgnYXZhaWxhYmxlT3V0T2ZTdG9jaycpO1xuICAgICAgICB0aGlzLmF2YWlsYWJsZVRleHQudGV4dChyZW1haW5pbmdBdmFpbGFibGUpO1xuICAgICAgICB0aGlzLmF2YWlsYWJsZVRleHQudG9nZ2xlQ2xhc3MoJ3RleHQtZGFuZ2VyIGZvbnQtd2VpZ2h0LWJvbGQnLCByZW1haW5pbmdBdmFpbGFibGUgPCAwKTtcbiAgICAgICAgY29uc3QgZGlzYWJsZUFkZEFjdGlvbkJ0biA9IG5ld1F1YW50aXR5IDw9IDAgfHwgKHJlbWFpbmluZ0F2YWlsYWJsZSA8IDAgJiYgIWF2YWlsYWJsZU91dE9mU3RvY2spO1xuICAgICAgICB0aGlzLnByb2R1Y3RBZGRBY3Rpb25CdG4ucHJvcCgnZGlzYWJsZWQnLCBkaXNhYmxlQWRkQWN0aW9uQnRuKTtcbiAgICAgICAgdGhpcy5pbnZvaWNlU2VsZWN0LnByb3AoJ2Rpc2FibGVkJywgIWF2YWlsYWJsZU91dE9mU3RvY2sgJiYgcmVtYWluaW5nQXZhaWxhYmxlIDwgMCk7XG5cbiAgICAgICAgY29uc3QgdGF4SW5jbHVkZWQgPSBwYXJzZUZsb2F0KHRoaXMucHJpY2VUYXhJbmNsdWRlZElucHV0LnZhbCgpKTtcbiAgICAgICAgdGhpcy50b3RhbFByaWNlVGV4dC5odG1sKFxuICAgICAgICAgIHRoaXMucHJpY2VUYXhDYWxjdWxhdG9yLmNhbGN1bGF0ZVRvdGFsUHJpY2UobmV3UXVhbnRpdHksIHRheEluY2x1ZGVkLCB0aGlzLmN1cnJlbmN5UHJlY2lzaW9uKVxuICAgICAgICApO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgdGhpcy5wcm9kdWN0SWRJbnB1dC5vbignY2hhbmdlJywgKCkgPT4ge1xuICAgICAgdGhpcy5wcm9kdWN0QWRkQWN0aW9uQnRuLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgICB0aGlzLmludm9pY2VTZWxlY3QucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcbiAgICB9KTtcblxuICAgIHRoaXMucHJpY2VUYXhJbmNsdWRlZElucHV0Lm9uKCdjaGFuZ2Uga2V5dXAnLCBldmVudCA9PiB7XG4gICAgICBjb25zdCB0YXhJbmNsdWRlZCA9IHBhcnNlRmxvYXQoZXZlbnQudGFyZ2V0LnZhbHVlKTtcbiAgICAgIGNvbnN0IHRheEV4Y2x1ZGVkID0gdGhpcy5wcmljZVRheENhbGN1bGF0b3IuY2FsY3VsYXRlVGF4RXhjbHVkZWQoXG4gICAgICAgIHRheEluY2x1ZGVkLFxuICAgICAgICB0aGlzLnRheFJhdGVJbnB1dC52YWwoKSxcbiAgICAgICAgdGhpcy5jdXJyZW5jeVByZWNpc2lvblxuICAgICAgKTtcbiAgICAgIGNvbnN0IHF1YW50aXR5ID0gcGFyc2VJbnQodGhpcy5xdWFudGl0eUlucHV0LnZhbCgpLCAxMCk7XG5cbiAgICAgIHRoaXMucHJpY2VUYXhFeGNsdWRlZElucHV0LnZhbCh0YXhFeGNsdWRlZCk7XG4gICAgICB0aGlzLnRvdGFsUHJpY2VUZXh0Lmh0bWwoXG4gICAgICAgIHRoaXMucHJpY2VUYXhDYWxjdWxhdG9yLmNhbGN1bGF0ZVRvdGFsUHJpY2UocXVhbnRpdHksIHRheEluY2x1ZGVkLCB0aGlzLmN1cnJlbmN5UHJlY2lzaW9uKVxuICAgICAgKTtcbiAgICB9KTtcblxuICAgIHRoaXMucHJpY2VUYXhFeGNsdWRlZElucHV0Lm9uKCdjaGFuZ2Uga2V5dXAnLCBldmVudCA9PiB7XG4gICAgICBjb25zdCB0YXhFeGNsdWRlZCA9IHBhcnNlRmxvYXQoZXZlbnQudGFyZ2V0LnZhbHVlKTtcbiAgICAgIGNvbnN0IHRheEluY2x1ZGVkID0gdGhpcy5wcmljZVRheENhbGN1bGF0b3IuY2FsY3VsYXRlVGF4SW5jbHVkZWQoXG4gICAgICAgIHRheEV4Y2x1ZGVkLFxuICAgICAgICB0aGlzLnRheFJhdGVJbnB1dC52YWwoKSxcbiAgICAgICAgdGhpcy5jdXJyZW5jeVByZWNpc2lvblxuICAgICAgKTtcbiAgICAgIGNvbnN0IHF1YW50aXR5ID0gcGFyc2VJbnQodGhpcy5xdWFudGl0eUlucHV0LnZhbCgpLCAxMCk7XG5cbiAgICAgIHRoaXMucHJpY2VUYXhJbmNsdWRlZElucHV0LnZhbCh0YXhJbmNsdWRlZCk7XG4gICAgICB0aGlzLnRvdGFsUHJpY2VUZXh0Lmh0bWwoXG4gICAgICAgIHRoaXMucHJpY2VUYXhDYWxjdWxhdG9yLmNhbGN1bGF0ZVRvdGFsUHJpY2UocXVhbnRpdHksIHRheEluY2x1ZGVkLCB0aGlzLmN1cnJlbmN5UHJlY2lzaW9uKVxuICAgICAgKTtcbiAgICB9KTtcblxuICAgIHRoaXMucHJvZHVjdEFkZEFjdGlvbkJ0bi5vbignY2xpY2snLCBldmVudCA9PiB0aGlzLmNvbmZpcm1OZXdJbnZvaWNlKGV2ZW50KSk7XG4gICAgdGhpcy5pbnZvaWNlU2VsZWN0Lm9uKCdjaGFuZ2UnLCAoKSA9PiB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLnRvZ2dsZVByb2R1Y3RBZGROZXdJbnZvaWNlSW5mbygpKTtcbiAgfVxuXG4gIHNldFByb2R1Y3QocHJvZHVjdCkge1xuICAgIHRoaXMucHJvZHVjdElkSW5wdXQudmFsKHByb2R1Y3QucHJvZHVjdElkKS50cmlnZ2VyKCdjaGFuZ2UnKTtcbiAgICB0aGlzLnByaWNlVGF4RXhjbHVkZWRJbnB1dC52YWwod2luZG93LnBzX3JvdW5kKHByb2R1Y3QucHJpY2VUYXhFeGNsLCB0aGlzLmN1cnJlbmN5UHJlY2lzaW9uKSk7XG4gICAgdGhpcy5wcmljZVRheEluY2x1ZGVkSW5wdXQudmFsKHdpbmRvdy5wc19yb3VuZChwcm9kdWN0LnByaWNlVGF4SW5jbCwgdGhpcy5jdXJyZW5jeVByZWNpc2lvbikpO1xuICAgIHRoaXMudGF4UmF0ZUlucHV0LnZhbChwcm9kdWN0LnRheFJhdGUpO1xuICAgIHRoaXMubG9jYXRpb25UZXh0Lmh0bWwocHJvZHVjdC5sb2NhdGlvbik7XG4gICAgdGhpcy5hdmFpbGFibGUgPSBwcm9kdWN0LnN0b2NrO1xuICAgIHRoaXMuYXZhaWxhYmxlVGV4dC5kYXRhKCdhdmFpbGFibGVPdXRPZlN0b2NrJywgcHJvZHVjdC5hdmFpbGFibGVPdXRPZlN0b2NrKTtcbiAgICB0aGlzLnF1YW50aXR5SW5wdXQudmFsKDEpO1xuICAgIHRoaXMucXVhbnRpdHlJbnB1dC50cmlnZ2VyKCdjaGFuZ2UnKTtcbiAgICB0aGlzLnNldENvbWJpbmF0aW9ucyhwcm9kdWN0LmNvbWJpbmF0aW9ucyk7XG4gICAgdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlci50b2dnbGVDb2x1bW4oT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c0NlbGxMb2NhdGlvbik7XG4gIH1cblxuICBzZXRDb21iaW5hdGlvbnMoY29tYmluYXRpb25zKSB7XG4gICAgdGhpcy5jb21iaW5hdGlvbnNTZWxlY3QuZW1wdHkoKTtcblxuICAgIE9iamVjdC52YWx1ZXMoY29tYmluYXRpb25zKS5mb3JFYWNoKHZhbCA9PiB7XG4gICAgICB0aGlzLmNvbWJpbmF0aW9uc1NlbGVjdC5hcHBlbmQoXG4gICAgICAgIGA8b3B0aW9uIHZhbHVlPVwiJHt2YWwuYXR0cmlidXRlQ29tYmluYXRpb25JZH1cIiBkYXRhLXByaWNlLXRheC1leGNsdWRlZD1cIiR7dmFsLnByaWNlVGF4RXhjbHVkZWR9XCIgZGF0YS1wcmljZS10YXgtaW5jbHVkZWQ9XCIke3ZhbC5wcmljZVRheEluY2x1ZGVkfVwiIGRhdGEtc3RvY2s9XCIke3ZhbC5zdG9ja31cIiBkYXRhLWxvY2F0aW9uPVwiJHt2YWwubG9jYXRpb259XCI+JHt2YWwuYXR0cmlidXRlfTwvb3B0aW9uPmBcbiAgICAgICk7XG4gICAgfSk7XG5cbiAgICB0aGlzLmNvbWJpbmF0aW9uc0Jsb2NrLnRvZ2dsZUNsYXNzKCdkLW5vbmUnLCBPYmplY3Qua2V5cyhjb21iaW5hdGlvbnMpLmxlbmd0aCA9PT0gMCk7XG5cbiAgICBpZiAoT2JqZWN0LmtleXMoY29tYmluYXRpb25zKS5sZW5ndGggPiAwKSB7XG4gICAgICB0aGlzLmNvbWJpbmF0aW9uc1NlbGVjdC50cmlnZ2VyKCdjaGFuZ2UnKTtcbiAgICB9XG4gIH1cblxuICBhZGRQcm9kdWN0KG9yZGVySWQpIHtcbiAgICB0aGlzLnByb2R1Y3RBZGRBY3Rpb25CdG4ucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcbiAgICB0aGlzLmludm9pY2VTZWxlY3QucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcbiAgICB0aGlzLmNvbWJpbmF0aW9uc1NlbGVjdC5wcm9wKCdkaXNhYmxlZCcsIHRydWUpO1xuXG4gICAgY29uc3QgcGFyYW1zID0ge1xuICAgICAgcHJvZHVjdF9pZDogdGhpcy5wcm9kdWN0SWRJbnB1dC52YWwoKSxcbiAgICAgIGNvbWJpbmF0aW9uX2lkOiAkKCc6c2VsZWN0ZWQnLCB0aGlzLmNvbWJpbmF0aW9uc1NlbGVjdCkudmFsKCksXG4gICAgICBwcmljZV90YXhfaW5jbDogdGhpcy5wcmljZVRheEluY2x1ZGVkSW5wdXQudmFsKCksXG4gICAgICBwcmljZV90YXhfZXhjbDogdGhpcy5wcmljZVRheEV4Y2x1ZGVkSW5wdXQudmFsKCksXG4gICAgICBxdWFudGl0eTogdGhpcy5xdWFudGl0eUlucHV0LnZhbCgpLFxuICAgICAgaW52b2ljZV9pZDogdGhpcy5pbnZvaWNlU2VsZWN0LnZhbCgpLFxuICAgICAgZnJlZV9zaGlwcGluZzogdGhpcy5mcmVlU2hpcHBpbmdTZWxlY3QucHJvcCgnY2hlY2tlZCcpXG4gICAgfTtcblxuICAgICQuYWpheCh7XG4gICAgICB1cmw6IHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9vcmRlcnNfYWRkX3Byb2R1Y3QnLCB7b3JkZXJJZH0pLFxuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICBkYXRhOiBwYXJhbXNcbiAgICB9KS50aGVuKFxuICAgICAgcmVzcG9uc2UgPT4ge1xuICAgICAgICBFdmVudEVtaXR0ZXIuZW1pdChPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0QWRkZWRUb09yZGVyLCB7XG4gICAgICAgICAgb3JkZXJJZCxcbiAgICAgICAgICBvcmRlclByb2R1Y3RJZDogcGFyYW1zLnByb2R1Y3RfaWQsXG4gICAgICAgICAgbmV3Um93OiByZXNwb25zZVxuICAgICAgICB9KTtcbiAgICAgIH0sXG4gICAgICByZXNwb25zZSA9PiB7XG4gICAgICAgIHRoaXMucHJvZHVjdEFkZEFjdGlvbkJ0bi5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAgICAgdGhpcy5pbnZvaWNlU2VsZWN0LnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICAgICAgICB0aGlzLmNvbWJpbmF0aW9uc1NlbGVjdC5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcblxuICAgICAgICBpZiAocmVzcG9uc2UucmVzcG9uc2VKU09OICYmIHJlc3BvbnNlLnJlc3BvbnNlSlNPTi5tZXNzYWdlKSB7XG4gICAgICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogcmVzcG9uc2UucmVzcG9uc2VKU09OLm1lc3NhZ2V9KTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICk7XG4gIH1cblxuICBjb25maXJtTmV3SW52b2ljZShldmVudCkge1xuICAgIGNvbnN0IGludm9pY2VJZCA9IHBhcnNlSW50KHRoaXMuaW52b2ljZVNlbGVjdC52YWwoKSwgMTApO1xuICAgIGNvbnN0IG9yZGVySWQgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ29yZGVySWQnKTtcblxuICAgIC8vIEV4cGxpY2l0IDAgdmFsdWUgaXMgdXNlZCB3aGVuIHdlIHRoZSB1c2VyIHNlbGVjdGVkIE5ldyBJbnZvaWNlXG4gICAgaWYgKGludm9pY2VJZCA9PT0gMCkge1xuICAgICAgY29uc3QgbW9kYWwgPSBuZXcgQ29uZmlybU1vZGFsKFxuICAgICAgICB7XG4gICAgICAgICAgaWQ6ICdtb2RhbC1jb25maXJtLW5ldy1pbnZvaWNlJyxcbiAgICAgICAgICBjb25maXJtVGl0bGU6IHRoaXMuaW52b2ljZVNlbGVjdC5kYXRhKCdtb2RhbC10aXRsZScpLFxuICAgICAgICAgIGNvbmZpcm1NZXNzYWdlOiB0aGlzLmludm9pY2VTZWxlY3QuZGF0YSgnbW9kYWwtYm9keScpLFxuICAgICAgICAgIGNvbmZpcm1CdXR0b25MYWJlbDogdGhpcy5pbnZvaWNlU2VsZWN0LmRhdGEoJ21vZGFsLWFwcGx5JyksXG4gICAgICAgICAgY2xvc2VCdXR0b25MYWJlbDogdGhpcy5pbnZvaWNlU2VsZWN0LmRhdGEoJ21vZGFsLWNhbmNlbCcpXG4gICAgICAgIH0sXG4gICAgICAgICgpID0+IHtcbiAgICAgICAgICB0aGlzLmNvbmZpcm1OZXdQcmljZShvcmRlcklkLCBpbnZvaWNlSWQpO1xuICAgICAgICB9XG4gICAgICApO1xuICAgICAgbW9kYWwuc2hvdygpO1xuICAgIH0gZWxzZSBpZiAoIWlzTmFOKGludm9pY2VJZCkpIHtcbiAgICAgIC8vIElmIGlkIGlzIG5vdCAwIG5vciBOYU4gYSBzcGVjaWZpYyBpbnZvaWNlIHdhcyBzZWxlY3RlZFxuICAgICAgdGhpcy5jb25maXJtTmV3UHJpY2Uob3JkZXJJZCwgaW52b2ljZUlkKTtcbiAgICB9IGVsc2Uge1xuICAgICAgLy8gTGFzdCBjYXNlIGlzIE5hbiwgdGhlIHNlbGVjdG9yIGlzIG5vdCBldmVuIHByZXNlbnQsIHdlIHNpbXBseSBhZGQgcHJvZHVjdCBhbmQgbGV0IHRoZSBCTyBoYW5kbGUgaXRcbiAgICAgIHRoaXMuYWRkUHJvZHVjdChvcmRlcklkKTtcbiAgICB9XG4gIH1cblxuICBjb25maXJtTmV3UHJpY2Uob3JkZXJJZCwgaW52b2ljZUlkKSB7XG4gICAgY29uc3QgY29tYmluYXRpb25JZCA9XG4gICAgICB0eXBlb2YgJCgnOnNlbGVjdGVkJywgdGhpcy5jb21iaW5hdGlvbnNTZWxlY3QpLnZhbCgpID09PSAndW5kZWZpbmVkJ1xuICAgICAgICA/IDBcbiAgICAgICAgOiAkKCc6c2VsZWN0ZWQnLCB0aGlzLmNvbWJpbmF0aW9uc1NlbGVjdCkudmFsKCk7XG4gICAgY29uc3QgcHJvZHVjdFByaWNlTWF0Y2ggPSB0aGlzLm9yZGVyUHJpY2VzUmVmcmVzaGVyLmNoZWNrT3RoZXJQcm9kdWN0UHJpY2VzTWF0Y2goXG4gICAgICB0aGlzLnByaWNlVGF4SW5jbHVkZWRJbnB1dC52YWwoKSxcbiAgICAgIHRoaXMucHJvZHVjdElkSW5wdXQudmFsKCksXG4gICAgICBjb21iaW5hdGlvbklkLFxuICAgICAgaW52b2ljZUlkXG4gICAgKTtcblxuICAgIGlmICghcHJvZHVjdFByaWNlTWF0Y2gpIHtcbiAgICAgIGNvbnN0IG1vZGFsRWRpdFByaWNlID0gbmV3IENvbmZpcm1Nb2RhbChcbiAgICAgICAge1xuICAgICAgICAgIGlkOiAnbW9kYWwtY29uZmlybS1uZXctcHJpY2UnLFxuICAgICAgICAgIGNvbmZpcm1UaXRsZTogdGhpcy5pbnZvaWNlU2VsZWN0LmRhdGEoJ21vZGFsLWVkaXQtcHJpY2UtdGl0bGUnKSxcbiAgICAgICAgICBjb25maXJtTWVzc2FnZTogdGhpcy5pbnZvaWNlU2VsZWN0LmRhdGEoJ21vZGFsLWVkaXQtcHJpY2UtYm9keScpLFxuICAgICAgICAgIGNvbmZpcm1CdXR0b25MYWJlbDogdGhpcy5pbnZvaWNlU2VsZWN0LmRhdGEoJ21vZGFsLWVkaXQtcHJpY2UtYXBwbHknKSxcbiAgICAgICAgICBjbG9zZUJ1dHRvbkxhYmVsOiB0aGlzLmludm9pY2VTZWxlY3QuZGF0YSgnbW9kYWwtZWRpdC1wcmljZS1jYW5jZWwnKVxuICAgICAgICB9LFxuICAgICAgICAoKSA9PiB7XG4gICAgICAgICAgdGhpcy5hZGRQcm9kdWN0KG9yZGVySWQpO1xuICAgICAgICB9XG4gICAgICApO1xuICAgICAgbW9kYWxFZGl0UHJpY2Uuc2hvdygpO1xuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLmFkZFByb2R1Y3Qob3JkZXJJZCk7XG4gICAgfVxuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtYWRkLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgT3JkZXJQcm9kdWN0TWFuYWdlciBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcm9kdWN0LW1hbmFnZXInO1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL09yZGVyVmlld1BhZ2VNYXAnO1xuaW1wb3J0IE9yZGVyVmlld0V2ZW50TWFwIGZyb20gJ0BwYWdlcy9vcmRlci92aWV3L29yZGVyLXZpZXctZXZlbnQtbWFwJztcbmltcG9ydCB7RXZlbnRFbWl0dGVyfSBmcm9tICdAY29tcG9uZW50cy9ldmVudC1lbWl0dGVyJztcbmltcG9ydCBPcmRlckRpc2NvdW50c1JlZnJlc2hlciBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci1kaXNjb3VudHMtcmVmcmVzaGVyJztcbmltcG9ydCBPcmRlclByb2R1Y3RSZW5kZXJlciBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcm9kdWN0LXJlbmRlcmVyJztcbmltcG9ydCBPcmRlclByaWNlc1JlZnJlc2hlciBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcmljZXMtcmVmcmVzaGVyJztcbmltcG9ydCBPcmRlclByb2R1Y3RzUmVmcmVzaGVyIGZyb20gJ0BwYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3RzLXJlZnJlc2hlcic7XG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQgT3JkZXJJbnZvaWNlc1JlZnJlc2hlciBmcm9tICcuL29yZGVyLWludm9pY2VzLXJlZnJlc2hlcic7XG5pbXBvcnQgT3JkZXJQcm9kdWN0Q2FuY2VsIGZyb20gJy4vb3JkZXItcHJvZHVjdC1jYW5jZWwnO1xuaW1wb3J0IE9yZGVyRG9jdW1lbnRzUmVmcmVzaGVyIGZyb20gXCIuL29yZGVyLWRvY3VtZW50cy1yZWZyZXNoZXJcIjtcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPcmRlclZpZXdQYWdlIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5vcmRlckRpc2NvdW50c1JlZnJlc2hlciA9IG5ldyBPcmRlckRpc2NvdW50c1JlZnJlc2hlcigpO1xuICAgIHRoaXMub3JkZXJQcm9kdWN0TWFuYWdlciA9IG5ldyBPcmRlclByb2R1Y3RNYW5hZ2VyKCk7XG4gICAgdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlciA9IG5ldyBPcmRlclByb2R1Y3RSZW5kZXJlcigpO1xuICAgIHRoaXMub3JkZXJQcmljZXNSZWZyZXNoZXIgPSBuZXcgT3JkZXJQcmljZXNSZWZyZXNoZXIoKTtcbiAgICB0aGlzLm9yZGVyUHJvZHVjdHNSZWZyZXNoZXIgPSBuZXcgT3JkZXJQcm9kdWN0c1JlZnJlc2hlcigpO1xuICAgIHRoaXMub3JkZXJEb2N1bWVudHNSZWZyZXNoZXIgPSBuZXcgT3JkZXJEb2N1bWVudHNSZWZyZXNoZXIoKTtcbiAgICB0aGlzLm9yZGVySW52b2ljZXNSZWZyZXNoZXIgPSBuZXcgT3JkZXJJbnZvaWNlc1JlZnJlc2hlcigpO1xuICAgIHRoaXMub3JkZXJQcm9kdWN0Q2FuY2VsID0gbmV3IE9yZGVyUHJvZHVjdENhbmNlbCgpO1xuICAgIHRoaXMucm91dGVyID0gbmV3IFJvdXRlcigpO1xuICAgIHRoaXMubGlzdGVuVG9FdmVudHMoKTtcbiAgfVxuXG4gIGxpc3RlblRvRXZlbnRzKCkge1xuXG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmludm9pY2VBZGRyZXNzRWRpdEJ0bikuZmFuY3lib3goe1xuICAgICAgJ3R5cGUnOiAnaWZyYW1lJyxcbiAgICAgICd3aWR0aCc6ICc5MCUnLFxuICAgICAgJ2hlaWdodCc6ICc5MCUnLFxuICAgIH0pO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5kZWxpdmVyeUFkZHJlc3NFZGl0QnRuKS5mYW5jeWJveCh7XG4gICAgICAndHlwZSc6ICdpZnJhbWUnLFxuICAgICAgJ3dpZHRoJzogJzkwJScsXG4gICAgICAnaGVpZ2h0JzogJzkwJScsXG4gICAgfSk7XG5cbiAgICBFdmVudEVtaXR0ZXIub24oT3JkZXJWaWV3RXZlbnRNYXAucHJvZHVjdERlbGV0ZWRGcm9tT3JkZXIsIChldmVudCkgPT4ge1xuICAgICAgLy8gUmVtb3ZlIHRoZSByb3dcbiAgICAgIGNvbnN0ICRyb3cgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVJvdyhldmVudC5vbGRPcmRlckRldGFpbElkKSk7XG4gICAgICBjb25zdCAkbmV4dCA9ICRyb3cubmV4dCgpO1xuICAgICAgJHJvdy5yZW1vdmUoKTtcbiAgICAgIGlmICgkbmV4dC5oYXNDbGFzcygnb3JkZXItcHJvZHVjdC1jdXN0b21pemF0aW9uJykpIHtcbiAgICAgICAgJG5leHQucmVtb3ZlKCk7XG4gICAgICB9XG5cbiAgICAgIGNvbnN0ICR0YWJsZVBhZ2luYXRpb24gPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb24pO1xuICAgICAgbGV0IG51bVBhZ2VzID0gJHRhYmxlUGFnaW5hdGlvbi5kYXRhKCdudW1QYWdlcycpO1xuICAgICAgY29uc3QgbnVtUm93c1BlclBhZ2UgPSAkdGFibGVQYWdpbmF0aW9uLmRhdGEoJ251bVBlclBhZ2UnKTtcbiAgICAgIGNvbnN0IG51bVJvd3MgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZSkuZmluZCgndHJbaWRePVwib3JkZXJQcm9kdWN0X1wiXTpub3QoLmQtbm9uZSknKS5sZW5ndGg7XG4gICAgICBsZXQgY3VycmVudFBhZ2UgPSBwYXJzZUludCgkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb25BY3RpdmUpLmh0bWwoKSwgMTApO1xuICAgICAgY29uc3QgbnVtUHJvZHVjdHMgPSBwYXJzZUludCgkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNDb3VudCkuaHRtbCgpLCAxMCk7XG4gICAgICBpZiAoKG51bVByb2R1Y3RzIC0gMSkgJSBudW1Sb3dzUGVyUGFnZSA9PT0gMCkge1xuICAgICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLnBhZ2luYXRpb25SZW1vdmVQYWdlKG51bVBhZ2VzKTtcbiAgICAgIH1cbiAgICAgIGlmIChudW1Sb3dzID09PSAxICYmIGN1cnJlbnRQYWdlID09PSBudW1QYWdlcykge1xuICAgICAgICBjdXJyZW50UGFnZSAtPSAxO1xuICAgICAgfVxuICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoT3JkZXJWaWV3RXZlbnRNYXAucHJvZHVjdExpc3RQYWdpbmF0ZWQsIHtcbiAgICAgICAgbnVtUGFnZTogY3VycmVudFBhZ2VcbiAgICAgIH0pO1xuXG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLnVwZGF0ZU51bVByb2R1Y3RzKG51bVByb2R1Y3RzIC0gMSk7XG4gICAgICB0aGlzLm9yZGVyUHJpY2VzUmVmcmVzaGVyLnJlZnJlc2goZXZlbnQub3JkZXJJZCk7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdHNSZWZyZXNoZXIucmVmcmVzaChldmVudC5vcmRlcklkKTtcbiAgICAgIHRoaXMub3JkZXJEaXNjb3VudHNSZWZyZXNoZXIucmVmcmVzaChldmVudC5vcmRlcklkKTtcbiAgICAgIHRoaXMub3JkZXJEb2N1bWVudHNSZWZyZXNoZXIucmVmcmVzaChldmVudC5vcmRlcklkKTtcbiAgICB9KTtcblxuICAgIEV2ZW50RW1pdHRlci5vbihPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0RWRpdGlvbkNhbmNlbGVkLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIucmVzZXRFZGl0Um93KGV2ZW50Lm9yZGVyRGV0YWlsSWQpO1xuICAgICAgY29uc3QgZWRpdFJvd3NMZWZ0ID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0Um93KS5ub3QoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFJvd1RlbXBsYXRlKS5sZW5ndGg7XG4gICAgICBpZiAoZWRpdFJvd3NMZWZ0ID4gMCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLm1vdmVQcm9kdWN0UGFuZWxUb09yaWdpbmFsUG9zaXRpb24oKTtcbiAgICB9KTtcblxuICAgIEV2ZW50RW1pdHRlci5vbihPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0VXBkYXRlZCwgKGV2ZW50KSA9PiB7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLmFkZE9yVXBkYXRlUHJvZHVjdFRvTGlzdChcbiAgICAgICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVSb3coZXZlbnQub3JkZXJEZXRhaWxJZCkpLFxuICAgICAgICBldmVudC5uZXdSb3dcbiAgICAgICk7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLnJlc2V0RWRpdFJvdyhldmVudC5vcmRlckRldGFpbElkKTtcbiAgICAgIHRoaXMub3JkZXJQcmljZXNSZWZyZXNoZXIucmVmcmVzaChldmVudC5vcmRlcklkKTtcbiAgICAgIHRoaXMub3JkZXJQcmljZXNSZWZyZXNoZXIucmVmcmVzaFByb2R1Y3RQcmljZXMoZXZlbnQub3JkZXJJZCk7XG4gICAgICB0aGlzLm9yZGVyRGlzY291bnRzUmVmcmVzaGVyLnJlZnJlc2goZXZlbnQub3JkZXJJZCk7XG4gICAgICB0aGlzLm9yZGVySW52b2ljZXNSZWZyZXNoZXIucmVmcmVzaChldmVudC5vcmRlcklkKTtcbiAgICAgIHRoaXMub3JkZXJEb2N1bWVudHNSZWZyZXNoZXIucmVmcmVzaChldmVudC5vcmRlcklkKTtcbiAgICAgIHRoaXMubGlzdGVuRm9yUHJvZHVjdERlbGV0ZSgpO1xuICAgICAgdGhpcy5saXN0ZW5Gb3JQcm9kdWN0RWRpdCgpO1xuICAgICAgdGhpcy5yZXNldFRvb2xUaXBzKCk7XG5cbiAgICAgIGNvbnN0IGVkaXRSb3dzTGVmdCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFJvdykubm90KE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRSb3dUZW1wbGF0ZSkubGVuZ3RoO1xuICAgICAgaWYgKGVkaXRSb3dzTGVmdCA+IDApIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlci5tb3ZlUHJvZHVjdFBhbmVsVG9PcmlnaW5hbFBvc2l0aW9uKCk7XG4gICAgfSk7XG5cbiAgICBFdmVudEVtaXR0ZXIub24oT3JkZXJWaWV3RXZlbnRNYXAucHJvZHVjdEFkZGVkVG9PcmRlciwgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkdGFibGVQYWdpbmF0aW9uID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uKTtcbiAgICAgIGNvbnN0IG51bVJvd3NQZXJQYWdlID0gJHRhYmxlUGFnaW5hdGlvbi5kYXRhKCdudW1QZXJQYWdlJyk7XG4gICAgICBjb25zdCBpbml0aWFsTnVtUHJvZHVjdHMgPSBwYXJzZUludCgkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNDb3VudCkuaHRtbCgpLCAxMCk7XG5cbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIuYWRkT3JVcGRhdGVQcm9kdWN0VG9MaXN0KFxuICAgICAgICAkKGAjJHskKGV2ZW50Lm5ld1JvdykuZmluZCgndHInKS5hdHRyKCdpZCcpfWApLFxuICAgICAgICBldmVudC5uZXdSb3dcbiAgICAgICk7XG4gICAgICB0aGlzLmxpc3RlbkZvclByb2R1Y3REZWxldGUoKTtcbiAgICAgIHRoaXMubGlzdGVuRm9yUHJvZHVjdEVkaXQoKTtcbiAgICAgIHRoaXMucmVzZXRUb29sVGlwcygpO1xuXG4gICAgICBjb25zdCBuZXdOdW1Qcm9kdWN0cyA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUm93cykubGVuZ3RoO1xuICAgICAgY29uc3QgaW5pdGlhbFBhZ2VzTnVtID0gTWF0aC5jZWlsKGluaXRpYWxOdW1Qcm9kdWN0cyAvIG51bVJvd3NQZXJQYWdlKTtcbiAgICAgIGNvbnN0IG5ld1BhZ2VzTnVtID0gTWF0aC5jZWlsKG5ld051bVByb2R1Y3RzIC8gbnVtUm93c1BlclBhZ2UpO1xuXG4gICAgICAvLyBVcGRhdGUgcGFnaW5hdGlvblxuICAgICAgaWYgKG5ld1BhZ2VzTnVtID4gaW5pdGlhbFBhZ2VzTnVtKSB7XG4gICAgICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIucGFnaW5hdGlvbkFkZFBhZ2UobmV3UGFnZXNOdW0pO1xuICAgICAgfVxuXG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLnVwZGF0ZU51bVByb2R1Y3RzKG5ld051bVByb2R1Y3RzKTtcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIucmVzZXRBZGRSb3coKTtcbiAgICAgIHRoaXMub3JkZXJQcmljZXNSZWZyZXNoZXIucmVmcmVzaFByb2R1Y3RQcmljZXMoZXZlbnQub3JkZXJJZCk7XG4gICAgICB0aGlzLm9yZGVyUHJpY2VzUmVmcmVzaGVyLnJlZnJlc2goZXZlbnQub3JkZXJJZCk7XG4gICAgICB0aGlzLm9yZGVyRGlzY291bnRzUmVmcmVzaGVyLnJlZnJlc2goZXZlbnQub3JkZXJJZCk7XG4gICAgICB0aGlzLm9yZGVySW52b2ljZXNSZWZyZXNoZXIucmVmcmVzaChldmVudC5vcmRlcklkKTtcbiAgICAgIHRoaXMub3JkZXJEb2N1bWVudHNSZWZyZXNoZXIucmVmcmVzaChldmVudC5vcmRlcklkKTtcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIubW92ZVByb2R1Y3RQYW5lbFRvT3JpZ2luYWxQb3NpdGlvbigpO1xuXG4gICAgICAvLyBNb3ZlIHRvIGxhc3QgcGFnZSB0byBzZWUgdGhlIGFkZGVkIHByb2R1Y3RcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KE9yZGVyVmlld0V2ZW50TWFwLnByb2R1Y3RMaXN0UGFnaW5hdGVkLCB7XG4gICAgICAgIG51bVBhZ2U6IG5ld1BhZ2VzTnVtXG4gICAgICB9KTtcbiAgICB9KTtcbiAgfVxuXG4gIGxpc3RlbkZvclByb2R1Y3REZWxldGUoKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3REZWxldGVCdG4pXG4gICAgICAub2ZmKCdjbGljaycpXG4gICAgICAub24oJ2NsaWNrJywgZXZlbnQgPT4ge1xuICAgICAgICB0aGlzLm9yZGVyUHJvZHVjdE1hbmFnZXIuaGFuZGxlRGVsZXRlUHJvZHVjdEV2ZW50KGV2ZW50KTtcbiAgICAgIH1cbiAgICApO1xuICB9XG5cbiAgcmVzZXRUb29sVGlwcygpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRCdXR0b25zKS5wc3Rvb2x0aXAoKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdERlbGV0ZUJ0bikucHN0b29sdGlwKCk7XG4gIH1cblxuICBsaXN0ZW5Gb3JQcm9kdWN0RWRpdCgpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRCdXR0b25zKS5vZmYoJ2NsaWNrJykub24oJ2NsaWNrJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkYnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIubW92ZVByb2R1Y3RzUGFuZWxUb01vZGlmaWNhdGlvblBvc2l0aW9uKCk7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLmVkaXRQcm9kdWN0RnJvbUxpc3QoXG4gICAgICAgICRidG4uZGF0YSgnb3JkZXJEZXRhaWxJZCcpLFxuICAgICAgICAkYnRuLmRhdGEoJ3Byb2R1Y3RRdWFudGl0eScpLFxuICAgICAgICAkYnRuLmRhdGEoJ3Byb2R1Y3RQcmljZVRheEluY2wnKSxcbiAgICAgICAgJGJ0bi5kYXRhKCdwcm9kdWN0UHJpY2VUYXhFeGNsJyksXG4gICAgICAgICRidG4uZGF0YSgndGF4UmF0ZScpLFxuICAgICAgICAkYnRuLmRhdGEoJ2xvY2F0aW9uJyksXG4gICAgICAgICRidG4uZGF0YSgnYXZhaWxhYmxlUXVhbnRpdHknKSxcbiAgICAgICAgJGJ0bi5kYXRhKCdhdmFpbGFibGVPdXRPZlN0b2NrJyksXG4gICAgICAgICRidG4uZGF0YSgnb3JkZXJJbnZvaWNlSWQnKSxcbiAgICAgICk7XG4gICAgfSk7XG4gIH1cblxuICBsaXN0ZW5Gb3JQcm9kdWN0UGFjaygpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdFBhY2tNb2RhbC5tb2RhbCkub24oJ3Nob3cuYnMubW9kYWwnLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0IGJ1dHRvbiA9ICQoZXZlbnQucmVsYXRlZFRhcmdldCk7XG4gICAgICBjb25zdCBwYWNrSXRlbXMgPSBidXR0b24uZGF0YSgncGFja0l0ZW1zJyk7XG4gICAgICBjb25zdCBtb2RhbCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0UGFja01vZGFsLm1vZGFsKTtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0UGFja01vZGFsLnJvd3MpLnJlbW92ZSgpO1xuICAgICAgcGFja0l0ZW1zLmZvckVhY2goaXRlbSA9PiB7XG4gICAgICAgIGNvbnN0ICRpdGVtID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RQYWNrTW9kYWwudGVtcGxhdGUpLmNsb25lKCk7XG4gICAgICAgICRpdGVtLmF0dHIoJ2lkJywgYHByb2R1Y3RwYWNrXyR7aXRlbS5pZH1gKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICAgICRpdGVtLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0UGFja01vZGFsLnByb2R1Y3QuaW1nKS5hdHRyKCdzcmMnLCBpdGVtLmltYWdlUGF0aCk7XG4gICAgICAgICRpdGVtLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0UGFja01vZGFsLnByb2R1Y3QubmFtZSkuaHRtbChpdGVtLm5hbWUpO1xuICAgICAgICAkaXRlbS5maW5kKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdFBhY2tNb2RhbC5wcm9kdWN0LmxpbmspLmF0dHIoJ2hyZWYnLCB0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fcHJvZHVjdF9mb3JtJywgeydpZCc6IGl0ZW0uaWR9KSk7XG4gICAgICAgIGlmIChpdGVtLnJlZmVyZW5jZSAhPT0gJycpIHtcbiAgICAgICAgICAkaXRlbS5maW5kKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdFBhY2tNb2RhbC5wcm9kdWN0LnJlZikuYXBwZW5kKGl0ZW0ucmVmZXJlbmNlKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAkaXRlbS5maW5kKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdFBhY2tNb2RhbC5wcm9kdWN0LnJlZikucmVtb3ZlKCk7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGl0ZW0uc3VwcGxpZXJSZWZlcmVuY2UgIT09ICcnKSB7XG4gICAgICAgICAgJGl0ZW0uZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RQYWNrTW9kYWwucHJvZHVjdC5zdXBwbGllclJlZikuYXBwZW5kKGl0ZW0uc3VwcGxpZXJSZWZlcmVuY2UpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICRpdGVtLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0UGFja01vZGFsLnByb2R1Y3Quc3VwcGxpZXJSZWYpLnJlbW92ZSgpO1xuICAgICAgICB9XG4gICAgICAgIGlmIChpdGVtLnF1YW50aXR5ID4gMSkge1xuICAgICAgICAgICRpdGVtLmZpbmQoYCR7T3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0UGFja01vZGFsLnByb2R1Y3QucXVhbnRpdHl9IHNwYW5gKS5odG1sKGl0ZW0ucXVhbnRpdHkpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICRpdGVtLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0UGFja01vZGFsLnByb2R1Y3QucXVhbnRpdHkpLmh0bWwoaXRlbS5xdWFudGl0eSk7XG4gICAgICAgIH1cbiAgICAgICAgJGl0ZW0uZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RQYWNrTW9kYWwucHJvZHVjdC5hdmFpbGFibGVRdWFudGl0eSkuaHRtbChpdGVtLmF2YWlsYWJsZVF1YW50aXR5KTtcbiAgICAgICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RQYWNrTW9kYWwudGVtcGxhdGUpLmJlZm9yZSgkaXRlbSk7XG4gICAgICB9KTtcbiAgICB9KTtcbiAgfVxuXG4gIGxpc3RlbkZvclByb2R1Y3RBZGQoKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRCdG4pLm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgIGV2ZW50ID0+IHtcbiAgICAgICAgdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlci50b2dnbGVQcm9kdWN0QWRkTmV3SW52b2ljZUluZm8oKTtcbiAgICAgICAgdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlci5tb3ZlUHJvZHVjdHNQYW5lbFRvTW9kaWZpY2F0aW9uUG9zaXRpb24oT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0U2VhcmNoSW5wdXQpO1xuICAgICAgfVxuICAgICk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RDYW5jZWxBZGRCdG4pLm9uKFxuICAgICAgJ2NsaWNrJywgZXZlbnQgPT4gdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlci5tb3ZlUHJvZHVjdFBhbmVsVG9PcmlnaW5hbFBvc2l0aW9uKClcbiAgICApO1xuICB9XG5cbiAgbGlzdGVuRm9yUHJvZHVjdFBhZ2luYXRpb24oKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uKS5vbignY2xpY2snLCBPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uTGluaywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0TGlzdFBhZ2luYXRlZCwge1xuICAgICAgICBudW1QYWdlOiAkYnRuLmRhdGEoJ3BhZ2UnKVxuICAgICAgfSk7XG4gICAgfSk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uTmV4dCkub24oJ2NsaWNrJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBpZiAoJGJ0bi5oYXNDbGFzcygnZGlzYWJsZWQnKSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBjb25zdCBhY3RpdmVQYWdlID0gdGhpcy5nZXRBY3RpdmVQYWdlKCk7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0TGlzdFBhZ2luYXRlZCwge1xuICAgICAgICBudW1QYWdlOiBwYXJzZUludCgkKGFjdGl2ZVBhZ2UpLmh0bWwoKSwgMTApICsgMVxuICAgICAgfSk7XG4gICAgfSk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uUHJldikub24oJ2NsaWNrJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBpZiAoJGJ0bi5oYXNDbGFzcygnZGlzYWJsZWQnKSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICBjb25zdCBhY3RpdmVQYWdlID0gdGhpcy5nZXRBY3RpdmVQYWdlKCk7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0TGlzdFBhZ2luYXRlZCwge1xuICAgICAgICBudW1QYWdlOiBwYXJzZUludCgkKGFjdGl2ZVBhZ2UpLmh0bWwoKSwgMTApIC0gMVxuICAgICAgfSk7XG4gICAgfSk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uTnVtYmVyU2VsZWN0b3IpLm9uKCdjaGFuZ2UnLCAoZXZlbnQpID0+IHtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBjb25zdCAkc2VsZWN0ID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0IG51bVBlclBhZ2UgPSBwYXJzZUludCgkc2VsZWN0LnZhbCgpLCAxMCk7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0TGlzdE51bWJlclBlclBhZ2UsIHtcbiAgICAgICAgbnVtUGVyUGFnZTogbnVtUGVyUGFnZVxuICAgICAgfSk7XG4gICAgfSk7XG5cbiAgICBFdmVudEVtaXR0ZXIub24oT3JkZXJWaWV3RXZlbnRNYXAucHJvZHVjdExpc3RQYWdpbmF0ZWQsIChldmVudCkgPT4ge1xuICAgICAgdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlci5wYWdpbmF0ZShldmVudC5udW1QYWdlKTtcbiAgICAgIHRoaXMubGlzdGVuRm9yUHJvZHVjdERlbGV0ZSgpO1xuICAgICAgdGhpcy5saXN0ZW5Gb3JQcm9kdWN0RWRpdCgpO1xuICAgICAgdGhpcy5yZXNldFRvb2xUaXBzKCk7XG4gICAgfSk7XG5cbiAgICBFdmVudEVtaXR0ZXIub24oT3JkZXJWaWV3RXZlbnRNYXAucHJvZHVjdExpc3ROdW1iZXJQZXJQYWdlLCAoZXZlbnQpID0+IHtcbiAgICAgIC8vIFVwZGF0ZSBwYWdpbmF0aW9uIG51bSBwZXIgcGFnZSAocGFnZSBsaW5rcyBhcmUgcmVnZW5lcmF0ZWQpXG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLnVwZGF0ZU51bVBlclBhZ2UoZXZlbnQubnVtUGVyUGFnZSk7XG5cbiAgICAgIC8vIFBhZ2luYXRlIHRvIHBhZ2UgMVxuICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoT3JkZXJWaWV3RXZlbnRNYXAucHJvZHVjdExpc3RQYWdpbmF0ZWQsIHtcbiAgICAgICAgbnVtUGFnZTogMVxuICAgICAgfSk7XG5cbiAgICAgIC8vIFNhdmUgbmV3IGNvbmZpZ1xuICAgICAgJC5hamF4KHtcbiAgICAgICAgdXJsOiB0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fb3JkZXJzX2NvbmZpZ3VyZV9wcm9kdWN0X3BhZ2luYXRpb24nKSxcbiAgICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICAgIGRhdGE6IHtudW1QZXJQYWdlOiBldmVudC5udW1QZXJQYWdlfSxcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9XG5cbiAgbGlzdGVuRm9yUmVmdW5kKCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LmJ1dHRvbnMucGFydGlhbFJlZnVuZCkub24oJ2NsaWNrJywgKCkgPT4ge1xuICAgICAgdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlci5tb3ZlUHJvZHVjdHNQYW5lbFRvUmVmdW5kUG9zaXRpb24oKTtcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0Q2FuY2VsLnNob3dQYXJ0aWFsUmVmdW5kKCk7XG4gICAgfSk7XG5cbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5idXR0b25zLnN0YW5kYXJkUmVmdW5kKS5vbignY2xpY2snLCAoKSA9PiB7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLm1vdmVQcm9kdWN0c1BhbmVsVG9SZWZ1bmRQb3NpdGlvbigpO1xuICAgICAgdGhpcy5vcmRlclByb2R1Y3RDYW5jZWwuc2hvd1N0YW5kYXJkUmVmdW5kKCk7XG4gICAgfSk7XG5cbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5idXR0b25zLnJldHVyblByb2R1Y3QpLm9uKCdjbGljaycsICgpID0+IHtcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIubW92ZVByb2R1Y3RzUGFuZWxUb1JlZnVuZFBvc2l0aW9uKCk7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdENhbmNlbC5zaG93UmV0dXJuUHJvZHVjdCgpO1xuICAgIH0pO1xuXG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuYnV0dG9ucy5hYm9ydCkub24oJ2NsaWNrJywgKCkgPT4ge1xuICAgICAgdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlci5tb3ZlUHJvZHVjdFBhbmVsVG9PcmlnaW5hbFBvc2l0aW9uKCk7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdENhbmNlbC5oaWRlUmVmdW5kKCk7XG4gICAgfSk7XG4gIH1cblxuICBsaXN0ZW5Gb3JDYW5jZWxQcm9kdWN0KCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LmJ1dHRvbnMuY2FuY2VsUHJvZHVjdHMpLm9uKCdjbGljaycsIChldmVudCkgPT4ge1xuICAgICAgdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlci5tb3ZlUHJvZHVjdHNQYW5lbFRvUmVmdW5kUG9zaXRpb24oKTtcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0Q2FuY2VsLnNob3dDYW5jZWxQcm9kdWN0Rm9ybSgpO1xuICAgIH0pO1xuICB9XG5cbiAgZ2V0QWN0aXZlUGFnZSgpIHtcbiAgICByZXR1cm4gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uKS5maW5kKCcuYWN0aXZlIHNwYW4nKS5nZXQoMCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItdmlldy1wYWdlLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNYXAgZnJvbSAnLi9PcmRlclZpZXdQYWdlTWFwJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIE1hbmFnZXMgYWRkaW5nL2VkaXRpbmcgbm90ZSBmb3IgaW52b2ljZSBkb2N1bWVudHMuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEludm9pY2VOb3RlTWFuYWdlciB7XG5cbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5zZXR1cExpc3RlbmVycygpO1xuICB9XG5cbiAgc2V0dXBMaXN0ZW5lcnMoKSB7XG4gICAgdGhpcy5faW5pdFNob3dOb3RlRm9ybUV2ZW50SGFuZGxlcigpO1xuICAgIHRoaXMuX2luaXRDbG9zZU5vdGVGb3JtRXZlbnRIYW5kbGVyKCk7XG4gICAgdGhpcy5faW5pdEVudGVyUGF5bWVudEV2ZW50SGFuZGxlcigpO1xuICB9XG5cbiAgX2luaXRTaG93Tm90ZUZvcm1FdmVudEhhbmRsZXIoKSB7XG4gICAgJCgnLmpzLW9wZW4taW52b2ljZS1ub3RlLWJ0bicpLm9uKCdjbGljaycsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIGNvbnN0ICRidG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgJG5vdGVSb3cgPSAkYnRuLmNsb3Nlc3QoJ3RyJykubmV4dCgpO1xuXG4gICAgICAkbm90ZVJvdy5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgfSk7XG4gIH1cblxuICBfaW5pdENsb3NlTm90ZUZvcm1FdmVudEhhbmRsZXIoKSB7XG4gICAgJCgnLmpzLWNhbmNlbC1pbnZvaWNlLW5vdGUtYnRuJykub24oJ2NsaWNrJywgKGV2ZW50KSA9PiB7XG4gICAgICAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmNsb3Nlc3QoJ3RyJykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIH0pO1xuICB9XG5cbiAgX2luaXRFbnRlclBheW1lbnRFdmVudEhhbmRsZXIoKSB7XG4gICAgJCgnLmpzLWVudGVyLXBheW1lbnQtYnRuJykub24oJ2NsaWNrJywgKGV2ZW50KSA9PiB7XG5cbiAgICAgIGNvbnN0ICRidG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgbGV0IHBheW1lbnRBbW91bnQgPSAkYnRuLmRhdGEoJ3BheW1lbnQtYW1vdW50Jyk7XG5cbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC52aWV3T3JkZXJQYXltZW50c0Jsb2NrKS5nZXQoMCkuc2Nyb2xsSW50b1ZpZXcoe2JlaGF2aW9yOiBcInNtb290aFwifSk7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJQYXltZW50Rm9ybUFtb3VudElucHV0KS52YWwocGF5bWVudEFtb3VudCk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2ludm9pY2Utbm90ZS1tYW5hZ2VyLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICdAcGFnZXMvb3JkZXIvT3JkZXJWaWV3UGFnZU1hcCc7XG5pbXBvcnQgT3JkZXJTaGlwcGluZ01hbmFnZXIgZnJvbSAnQHBhZ2VzL29yZGVyL29yZGVyLXNoaXBwaW5nLW1hbmFnZXInO1xuaW1wb3J0IE9yZGVyVmlld1BhZ2UgZnJvbSAnQHBhZ2VzL29yZGVyL3ZpZXcvb3JkZXItdmlldy1wYWdlJztcbmltcG9ydCBPcmRlclByb2R1Y3RBdXRvY29tcGxldGUgZnJvbSAnQHBhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcHJvZHVjdC1hZGQtYXV0b2NvbXBsZXRlJztcbmltcG9ydCBPcmRlclByb2R1Y3RBZGQgZnJvbSAnQHBhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcHJvZHVjdC1hZGQnO1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNZXNzYWdlc0hhbmRsZXIgZnJvbSAnLi9tZXNzYWdlL29yZGVyLXZpZXctcGFnZS1tZXNzYWdlcy1oYW5kbGVyJztcbmltcG9ydCBUZXh0V2l0aExlbmd0aENvdW50ZXIgZnJvbSAnQGNvbXBvbmVudHMvZm9ybS90ZXh0LXdpdGgtbGVuZ3RoLWNvdW50ZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBjb25zdCBESVNDT1VOVF9UWVBFX0FNT1VOVCA9ICdhbW91bnQnO1xuICBjb25zdCBESVNDT1VOVF9UWVBFX1BFUkNFTlQgPSAncGVyY2VudCc7XG4gIGNvbnN0IERJU0NPVU5UX1RZUEVfRlJFRV9TSElQUElORyA9ICdmcmVlX3NoaXBwaW5nJztcblxuICBuZXcgT3JkZXJTaGlwcGluZ01hbmFnZXIoKTtcbiAgbmV3IFRleHRXaXRoTGVuZ3RoQ291bnRlcigpO1xuICBjb25zdCBvcmRlclZpZXdQYWdlID0gbmV3IE9yZGVyVmlld1BhZ2UoKTtcbiAgY29uc3Qgb3JkZXJBZGRBdXRvY29tcGxldGUgPSBuZXcgT3JkZXJQcm9kdWN0QXV0b2NvbXBsZXRlKCQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0U2VhcmNoSW5wdXQpKTtcbiAgY29uc3Qgb3JkZXJBZGQgPSBuZXcgT3JkZXJQcm9kdWN0QWRkKCk7XG5cbiAgb3JkZXJWaWV3UGFnZS5saXN0ZW5Gb3JQcm9kdWN0UGFjaygpO1xuICBvcmRlclZpZXdQYWdlLmxpc3RlbkZvclByb2R1Y3REZWxldGUoKTtcbiAgb3JkZXJWaWV3UGFnZS5saXN0ZW5Gb3JQcm9kdWN0RWRpdCgpO1xuICBvcmRlclZpZXdQYWdlLmxpc3RlbkZvclByb2R1Y3RBZGQoKTtcbiAgb3JkZXJWaWV3UGFnZS5saXN0ZW5Gb3JQcm9kdWN0UGFnaW5hdGlvbigpO1xuICBvcmRlclZpZXdQYWdlLmxpc3RlbkZvclJlZnVuZCgpO1xuICBvcmRlclZpZXdQYWdlLmxpc3RlbkZvckNhbmNlbFByb2R1Y3QoKTtcblxuICBvcmRlckFkZEF1dG9jb21wbGV0ZS5saXN0ZW5Gb3JTZWFyY2goKTtcbiAgb3JkZXJBZGRBdXRvY29tcGxldGUub25JdGVtQ2xpY2tlZENhbGxiYWNrID0gcHJvZHVjdCA9PiBvcmRlckFkZC5zZXRQcm9kdWN0KHByb2R1Y3QpO1xuXG4gIGhhbmRsZVBheW1lbnREZXRhaWxzVG9nZ2xlKCk7XG4gIGhhbmRsZVByaXZhdGVOb3RlQ2hhbmdlKCk7XG4gIGhhbmRsZVVwZGF0ZU9yZGVyU3RhdHVzQnV0dG9uKCk7XG5cbiAgY29uc3Qgb3JkZXJWaWV3UGFnZU1lc3NhZ2VIYW5kbGVyID0gbmV3IE9yZGVyVmlld1BhZ2VNZXNzYWdlc0hhbmRsZXIoKTtcbiAgb3JkZXJWaWV3UGFnZU1lc3NhZ2VIYW5kbGVyLmxpc3RlbkZvclByZWRlZmluZWRNZXNzYWdlU2VsZWN0aW9uKCk7XG4gIG9yZGVyVmlld1BhZ2VNZXNzYWdlSGFuZGxlci5saXN0ZW5Gb3JGdWxsTWVzc2FnZXNPcGVuKCk7XG4gICQoT3JkZXJWaWV3UGFnZU1hcC5wcml2YXRlTm90ZVRvZ2dsZUJ0bikub24oJ2NsaWNrJywgZXZlbnQgPT4ge1xuICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgdG9nZ2xlUHJpdmF0ZU5vdGVCbG9jaygpO1xuICB9KTtcblxuICAkKE9yZGVyVmlld1BhZ2VNYXAucHJpbnRPcmRlclZpZXdQYWdlQnV0dG9uKS5vbignY2xpY2snLCAoKSA9PiB7XG4gICAgY29uc3QgdGVtcFRpdGxlID0gZG9jdW1lbnQudGl0bGU7XG4gICAgZG9jdW1lbnQudGl0bGUgPSAkKE9yZGVyVmlld1BhZ2VNYXAubWFpbkRpdikuZGF0YSgnb3JkZXJUaXRsZScpO1xuICAgIHdpbmRvdy5wcmludCgpO1xuICAgIGRvY3VtZW50LnRpdGxlID0gdGVtcFRpdGxlO1xuICB9KTtcblxuICBpbml0QWRkQ2FydFJ1bGVGb3JtSGFuZGxlcigpO1xuICBpbml0Q2hhbmdlQWRkcmVzc0Zvcm1IYW5kbGVyKCk7XG4gIGluaXRIb29rVGFicygpO1xuXG4gIGZ1bmN0aW9uIGluaXRIb29rVGFicygpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJIb29rVGFic0NvbnRhaW5lcilcbiAgICAgIC5maW5kKCcubmF2LXRhYnMgbGk6Zmlyc3QtY2hpbGQgYScpXG4gICAgICAudGFiKCdzaG93Jyk7XG4gIH1cblxuICBmdW5jdGlvbiBoYW5kbGVQYXltZW50RGV0YWlsc1RvZ2dsZSgpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJQYXltZW50RGV0YWlsc0J0bikub24oJ2NsaWNrJywgZXZlbnQgPT4ge1xuICAgICAgY29uc3QgJHBheW1lbnREZXRhaWxSb3cgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpXG4gICAgICAgIC5jbG9zZXN0KCd0cicpXG4gICAgICAgIC5uZXh0KCc6Zmlyc3QnKTtcblxuICAgICAgJHBheW1lbnREZXRhaWxSb3cudG9nZ2xlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIH0pO1xuICB9XG5cbiAgZnVuY3Rpb24gdG9nZ2xlUHJpdmF0ZU5vdGVCbG9jaygpIHtcbiAgICBjb25zdCAkYmxvY2sgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJpdmF0ZU5vdGVCbG9jayk7XG4gICAgY29uc3QgJGJ0biA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcml2YXRlTm90ZVRvZ2dsZUJ0bik7XG4gICAgY29uc3QgaXNQcml2YXRlTm90ZU9wZW5lZCA9ICRidG4uaGFzQ2xhc3MoJ2lzLW9wZW5lZCcpO1xuXG4gICAgaWYgKGlzUHJpdmF0ZU5vdGVPcGVuZWQpIHtcbiAgICAgICRidG4ucmVtb3ZlQ2xhc3MoJ2lzLW9wZW5lZCcpO1xuICAgICAgJGJsb2NrLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICB9IGVsc2Uge1xuICAgICAgJGJ0bi5hZGRDbGFzcygnaXMtb3BlbmVkJyk7XG4gICAgICAkYmxvY2sucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIH1cblxuICAgIGNvbnN0ICRpY29uID0gJGJ0bi5maW5kKCcubWF0ZXJpYWwtaWNvbnMnKTtcbiAgICAkaWNvbi50ZXh0KGlzUHJpdmF0ZU5vdGVPcGVuZWQgPyAnYWRkJyA6ICdyZW1vdmUnKTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGhhbmRsZVByaXZhdGVOb3RlQ2hhbmdlKCkge1xuICAgIGNvbnN0ICRzdWJtaXRCdG4gPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJpdmF0ZU5vdGVTdWJtaXRCdG4pO1xuXG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByaXZhdGVOb3RlSW5wdXQpLm9uKCdpbnB1dCcsICgpID0+IHtcbiAgICAgICRzdWJtaXRCdG4ucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gICAgfSk7XG4gIH1cblxuICBmdW5jdGlvbiBpbml0QWRkQ2FydFJ1bGVGb3JtSGFuZGxlcigpIHtcbiAgICBjb25zdCAkbW9kYWwgPSAkKE9yZGVyVmlld1BhZ2VNYXAuYWRkQ2FydFJ1bGVNb2RhbCk7XG4gICAgY29uc3QgJGZvcm0gPSAkbW9kYWwuZmluZCgnZm9ybScpO1xuICAgIGNvbnN0ICR2YWx1ZUhlbHAgPSAkbW9kYWwuZmluZChPcmRlclZpZXdQYWdlTWFwLmNhcnRSdWxlSGVscFRleHQpO1xuICAgIGNvbnN0ICR2YWx1ZUlucHV0ID0gJGZvcm0uZmluZChPcmRlclZpZXdQYWdlTWFwLmFkZENhcnRSdWxlVmFsdWVJbnB1dCk7XG4gICAgY29uc3QgJHZhbHVlRm9ybUdyb3VwID0gJHZhbHVlSW5wdXQuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKTtcblxuICAgICRmb3JtLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5hZGRDYXJ0UnVsZVR5cGVTZWxlY3QpLm9uKCdjaGFuZ2UnLCBldmVudCA9PiB7XG4gICAgICBjb25zdCBzZWxlY3RlZENhcnRSdWxlVHlwZSA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkudmFsKCk7XG4gICAgICBjb25zdCAkdmFsdWVVbml0ID0gJGZvcm0uZmluZChPcmRlclZpZXdQYWdlTWFwLmFkZENhcnRSdWxlVmFsdWVVbml0KTtcblxuICAgICAgaWYgKHNlbGVjdGVkQ2FydFJ1bGVUeXBlID09PSBESVNDT1VOVF9UWVBFX0FNT1VOVCkge1xuICAgICAgICAkdmFsdWVIZWxwLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAgICAgJHZhbHVlVW5pdC5odG1sKCR2YWx1ZVVuaXQuZGF0YSgnY3VycmVuY3lTeW1ib2wnKSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkdmFsdWVIZWxwLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgIH1cblxuICAgICAgaWYgKHNlbGVjdGVkQ2FydFJ1bGVUeXBlID09PSBESVNDT1VOVF9UWVBFX1BFUkNFTlQpIHtcbiAgICAgICAgJHZhbHVlVW5pdC5odG1sKCclJyk7XG4gICAgICB9XG5cbiAgICAgIGlmIChzZWxlY3RlZENhcnRSdWxlVHlwZSA9PT0gRElTQ09VTlRfVFlQRV9GUkVFX1NISVBQSU5HKSB7XG4gICAgICAgICR2YWx1ZUZvcm1Hcm91cC5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgICAgICR2YWx1ZUlucHV0LmF0dHIoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkdmFsdWVGb3JtR3JvdXAucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgICAkdmFsdWVJbnB1dC5hdHRyKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGhhbmRsZVVwZGF0ZU9yZGVyU3RhdHVzQnV0dG9uKCkge1xuICAgIGNvbnN0ICRidG4gPSAkKE9yZGVyVmlld1BhZ2VNYXAudXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25CdG4pO1xuICAgIGNvbnN0ICR3cmFwcGVyID0gJChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uSW5wdXRXcmFwcGVyKTtcblxuICAgICQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVPcmRlclN0YXR1c0FjdGlvbklucHV0KS5vbignY2hhbmdlJywgZXZlbnQgPT4ge1xuICAgICAgY29uc3QgJGVsZW1lbnQgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgJG9wdGlvbiA9ICQoJ29wdGlvbjpzZWxlY3RlZCcsICRlbGVtZW50KTtcbiAgICAgIGNvbnN0IHNlbGVjdGVkT3JkZXJTdGF0dXNJZCA9ICRlbGVtZW50LnZhbCgpO1xuXG4gICAgICAkd3JhcHBlci5jc3MoJ2JhY2tncm91bmQtY29sb3InLCAkb3B0aW9uLmRhdGEoJ2JhY2tncm91bmQtY29sb3InKSk7XG4gICAgICAkd3JhcHBlci50b2dnbGVDbGFzcygnaXMtYnJpZ2h0JywgJG9wdGlvbi5kYXRhKCdpcy1icmlnaHQnKSAhPT0gdW5kZWZpbmVkKTtcblxuICAgICAgJGJ0bi5wcm9wKCdkaXNhYmxlZCcsIHBhcnNlSW50KHNlbGVjdGVkT3JkZXJTdGF0dXNJZCwgMTApID09PSAkYnRuLmRhdGEoJ29yZGVyU3RhdHVzSWQnKSk7XG4gICAgfSk7XG4gIH1cblxuICBmdW5jdGlvbiBpbml0Q2hhbmdlQWRkcmVzc0Zvcm1IYW5kbGVyKCkge1xuICAgIGNvbnN0ICRtb2RhbCA9ICQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVDdXN0b21lckFkZHJlc3NNb2RhbCk7XG5cbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3Blbk9yZGVyQWRkcmVzc1VwZGF0ZU1vZGFsQnRuKS5vbignY2xpY2snLCBldmVudCA9PiB7XG4gICAgICAkbW9kYWwuZmluZChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZU9yZGVyQWRkcmVzc1R5cGVJbnB1dCkudmFsKCQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnYWRkcmVzc1R5cGUnKSk7XG4gICAgfSk7XG4gIH1cbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvdmlldy5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IFJvdXRlciBmcm9tICdAY29tcG9uZW50cy9yb3V0ZXInO1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL09yZGVyVmlld1BhZ2VNYXAnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIE9yZGVyRGlzY291bnRzUmVmcmVzaGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gIH1cblxuICByZWZyZXNoKG9yZGVySWQpIHtcbiAgICAkLmFqYXgodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19nZXRfZGlzY291bnRzJywge29yZGVySWR9KSlcbiAgICAgIC50aGVuKChyZXNwb25zZSkgPT4ge1xuICAgICAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdERpc2NvdW50TGlzdC5saXN0KS5yZXBsYWNlV2l0aChyZXNwb25zZSk7XG4gICAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci1kaXNjb3VudHMtcmVmcmVzaGVyLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICdAcGFnZXMvb3JkZXIvT3JkZXJWaWV3UGFnZU1hcCc7XG5pbXBvcnQgSW52b2ljZU5vdGVNYW5hZ2VyIGZyb20gJy4uL2ludm9pY2Utbm90ZS1tYW5hZ2VyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPcmRlckRvY3VtZW50c1JlZnJlc2hlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMucm91dGVyID0gbmV3IFJvdXRlcigpO1xuICAgIHRoaXMuaW52b2ljZU5vdGVNYW5hZ2VyID0gbmV3IEludm9pY2VOb3RlTWFuYWdlcigpO1xuICB9XG5cbiAgcmVmcmVzaChvcmRlcklkKSB7XG4gICAgJC5hamF4KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9vcmRlcnNfZ2V0X2RvY3VtZW50cycsIHtvcmRlcklkfSkpXG4gICAgICAudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgICAgJChPcmRlclZpZXdQYWdlTWFwLm9yZGVyRG9jdW1lbnRzVGFiQ291bnQpLnRleHQocmVzcG9uc2UudG90YWwpO1xuICAgICAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJEb2N1bWVudHNUYWJCb2R5KS5odG1sKHJlc3BvbnNlLmh0bWwpO1xuICAgICAgICB0aGlzLmludm9pY2VOb3RlTWFuYWdlci5zZXR1cExpc3RlbmVycygpO1xuICAgICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItZG9jdW1lbnRzLXJlZnJlc2hlci5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IFJvdXRlciBmcm9tICdAY29tcG9uZW50cy9yb3V0ZXInO1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL09yZGVyVmlld1BhZ2VNYXAnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIE9yZGVySW52b2ljZXNSZWZyZXNoZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgfVxuXG4gIHJlZnJlc2gob3JkZXJJZCkge1xuICAgICQuYWpheCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fb3JkZXJzX2dldF9pbnZvaWNlcycsIHtvcmRlcklkfSkpXG4gICAgICAudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgICAgaWYgKCFyZXNwb25zZSB8fCAhcmVzcG9uc2UuaW52b2ljZXMgfHwgT2JqZWN0LmtleXMocmVzcG9uc2UuaW52b2ljZXMpLmxlbmd0aCA8PSAwKSB7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgY29uc3QgJHBheW1lbnRJbnZvaWNlU2VsZWN0ID0gJChPcmRlclZpZXdQYWdlTWFwLm9yZGVyUGF5bWVudEludm9pY2VTZWxlY3QpO1xuICAgICAgICBjb25zdCAkYWRkUHJvZHVjdEludm9pY2VTZWxlY3QgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZEludm9pY2VTZWxlY3QpO1xuICAgICAgICBjb25zdCAkZXhpc3RpbmdJbnZvaWNlc0dyb3VwID0gJGFkZFByb2R1Y3RJbnZvaWNlU2VsZWN0LmZpbmQoJ29wdGdyb3VwOmZpcnN0Jyk7XG4gICAgICAgIGNvbnN0ICRwcm9kdWN0RWRpdEludm9pY2VTZWxlY3QgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRJbnZvaWNlU2VsZWN0KTtcbiAgICAgICAgY29uc3QgJGFkZERpc2NvdW50SW52b2ljZVNlbGVjdCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5hZGRDYXJ0UnVsZUludm9pY2VJZFNlbGVjdCk7XG4gICAgICAgICRleGlzdGluZ0ludm9pY2VzR3JvdXAuZW1wdHkoKTtcbiAgICAgICAgJHBheW1lbnRJbnZvaWNlU2VsZWN0LmVtcHR5KCk7XG4gICAgICAgICRwcm9kdWN0RWRpdEludm9pY2VTZWxlY3QuZW1wdHkoKTtcbiAgICAgICAgJGFkZERpc2NvdW50SW52b2ljZVNlbGVjdC5lbXB0eSgpO1xuXG4gICAgICAgIE9iamVjdC5rZXlzKHJlc3BvbnNlLmludm9pY2VzKS5mb3JFYWNoKChpbnZvaWNlTmFtZSkgPT4ge1xuICAgICAgICAgIGNvbnN0IGludm9pY2VJZCA9IHJlc3BvbnNlLmludm9pY2VzW2ludm9pY2VOYW1lXTtcbiAgICAgICAgICBjb25zdCBpbnZvaWNlTmFtZVdpdGhvdXRQcmljZSA9IGludm9pY2VOYW1lLnNwbGl0KCcgLSAnKVswXTtcblxuICAgICAgICAgICRleGlzdGluZ0ludm9pY2VzR3JvdXAuYXBwZW5kKGA8b3B0aW9uIHZhbHVlPVwiJHtpbnZvaWNlSWR9XCI+JHtpbnZvaWNlTmFtZVdpdGhvdXRQcmljZX08L29wdGlvbj5gKTtcbiAgICAgICAgICAkcGF5bWVudEludm9pY2VTZWxlY3QuYXBwZW5kKGA8b3B0aW9uIHZhbHVlPVwiJHtpbnZvaWNlSWR9XCI+JHtpbnZvaWNlTmFtZVdpdGhvdXRQcmljZX08L29wdGlvbj5gKTtcbiAgICAgICAgICAkcHJvZHVjdEVkaXRJbnZvaWNlU2VsZWN0LmFwcGVuZChgPG9wdGlvbiB2YWx1ZT1cIiR7aW52b2ljZUlkfVwiPiR7aW52b2ljZU5hbWVXaXRob3V0UHJpY2V9PC9vcHRpb24+YCk7XG4gICAgICAgICAgJGFkZERpc2NvdW50SW52b2ljZVNlbGVjdC5hcHBlbmQoYDxvcHRpb24gdmFsdWU9XCIke2ludm9pY2VJZH1cIj4ke2ludm9pY2VOYW1lfTwvb3B0aW9uPmApO1xuICAgICAgICB9KTtcblxuICAgICAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZEludm9pY2VTZWxlY3QpLnNlbGVjdGVkSW5kZXggPSAwO1xuICAgICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItaW52b2ljZXMtcmVmcmVzaGVyLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICdAcGFnZXMvb3JkZXIvT3JkZXJWaWV3UGFnZU1hcCc7XG5pbXBvcnQgeyBOdW1iZXJGb3JtYXR0ZXIgfSBmcm9tICdAYXBwL2NsZHInO1xuXG5jb25zdCB7JH0gPSB3aW5kb3c7XG5cbi8qKlxuICogbWFuYWdlcyBhbGwgcHJvZHVjdCBjYW5jZWwgYWN0aW9ucywgdGhhdCBpbmNsdWRlcyBhbGwgcmVmdW5kIG9wZXJhdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3JkZXJQcm9kdWN0Q2FuY2VsIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gICAgdGhpcy5jYW5jZWxQcm9kdWN0Rm9ybSA9ICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LmZvcm0pO1xuICAgIHRoaXMub3JkZXJJZCA9IHRoaXMuY2FuY2VsUHJvZHVjdEZvcm0uZGF0YSgnb3JkZXJJZCcpO1xuICAgIHRoaXMub3JkZXJEZWxpdmVyZWQgPSBwYXJzZUludCh0aGlzLmNhbmNlbFByb2R1Y3RGb3JtLmRhdGEoJ2lzRGVsaXZlcmVkJyksIDEwKSA9PT0gMTtcbiAgICB0aGlzLmlzVGF4SW5jbHVkZWQgPSBwYXJzZUludCh0aGlzLmNhbmNlbFByb2R1Y3RGb3JtLmRhdGEoJ2lzVGF4SW5jbHVkZWQnKSwgMTApID09PSAxO1xuICAgIHRoaXMuZGlzY291bnRzQW1vdW50ID0gcGFyc2VGbG9hdCh0aGlzLmNhbmNlbFByb2R1Y3RGb3JtLmRhdGEoJ2Rpc2NvdW50c0Ftb3VudCcpKTtcbiAgICB0aGlzLmN1cnJlbmN5Rm9ybWF0dGVyID0gTnVtYmVyRm9ybWF0dGVyLmJ1aWxkKHRoaXMuY2FuY2VsUHJvZHVjdEZvcm0uZGF0YSgncHJpY2VTcGVjaWZpY2F0aW9uJykpO1xuICAgIHRoaXMudXNlQW1vdW50SW5wdXRzID0gdHJ1ZTtcbiAgICB0aGlzLmxpc3RlbkZvcklucHV0cygpO1xuICB9XG5cbiAgc2hvd1BhcnRpYWxSZWZ1bmQoKSB7XG4gICAgLy8gQWx3YXlzIHN0YXJ0IGJ5IGhpZGluZyBlbGVtZW50cyB0aGVuIHNob3cgdGhlIG90aGVycywgc2luY2Ugc29tZSBlbGVtZW50cyBhcmUgY29tbW9uXG4gICAgdGhpcy5oaWRlQ2FuY2VsRWxlbWVudHMoKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC50b2dnbGUucGFydGlhbFJlZnVuZCkuc2hvdygpO1xuICAgIHRoaXMudXNlQW1vdW50SW5wdXRzID0gdHJ1ZTtcbiAgICB0aGlzLmluaXRGb3JtKFxuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuYnV0dG9ucy5zYXZlKS5kYXRhKCdwYXJ0aWFsUmVmdW5kTGFiZWwnKSxcbiAgICAgIHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9vcmRlcnNfcGFydGlhbF9yZWZ1bmQnLCB7b3JkZXJJZDogdGhpcy5vcmRlcklkfSksXG4gICAgICAncGFydGlhbC1yZWZ1bmQnXG4gICAgKTtcbiAgfVxuXG4gIHNob3dTdGFuZGFyZFJlZnVuZCgpIHtcbiAgICAvLyBBbHdheXMgc3RhcnQgYnkgaGlkaW5nIGVsZW1lbnRzIHRoZW4gc2hvdyB0aGUgb3RoZXJzLCBzaW5jZSBzb21lIGVsZW1lbnRzIGFyZSBjb21tb25cbiAgICB0aGlzLmhpZGVDYW5jZWxFbGVtZW50cygpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnRvZ2dsZS5zdGFuZGFyZFJlZnVuZCkuc2hvdygpO1xuICAgIHRoaXMudXNlQW1vdW50SW5wdXRzID0gZmFsc2U7XG4gICAgdGhpcy5pbml0Rm9ybShcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LmJ1dHRvbnMuc2F2ZSkuZGF0YSgnc3RhbmRhcmRSZWZ1bmRMYWJlbCcpLFxuICAgICAgdGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19zdGFuZGFyZF9yZWZ1bmQnLCB7b3JkZXJJZDogdGhpcy5vcmRlcklkfSksXG4gICAgICAnc3RhbmRhcmQtcmVmdW5kJ1xuICAgICk7XG4gIH1cblxuICBzaG93UmV0dXJuUHJvZHVjdCgpIHtcbiAgICAvLyBBbHdheXMgc3RhcnQgYnkgaGlkaW5nIGVsZW1lbnRzIHRoZW4gc2hvdyB0aGUgb3RoZXJzLCBzaW5jZSBzb21lIGVsZW1lbnRzIGFyZSBjb21tb25cbiAgICB0aGlzLmhpZGVDYW5jZWxFbGVtZW50cygpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnRvZ2dsZS5yZXR1cm5Qcm9kdWN0KS5zaG93KCk7XG4gICAgdGhpcy51c2VBbW91bnRJbnB1dHMgPSBmYWxzZTtcbiAgICB0aGlzLmluaXRGb3JtKFxuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuYnV0dG9ucy5zYXZlKS5kYXRhKCdyZXR1cm5Qcm9kdWN0TGFiZWwnKSxcbiAgICAgIHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9vcmRlcnNfcmV0dXJuX3Byb2R1Y3QnLCB7b3JkZXJJZDogdGhpcy5vcmRlcklkfSksXG4gICAgICAncmV0dXJuLXByb2R1Y3QnXG4gICAgKTtcbiAgfVxuXG4gIGhpZGVSZWZ1bmQoKSB7XG4gICAgdGhpcy5oaWRlQ2FuY2VsRWxlbWVudHMoKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC50YWJsZS5hY3Rpb25zKS5zaG93KCk7XG4gIH1cblxuICBoaWRlQ2FuY2VsRWxlbWVudHMoKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QudG9nZ2xlLnN0YW5kYXJkUmVmdW5kKS5oaWRlKCk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QudG9nZ2xlLnBhcnRpYWxSZWZ1bmQpLmhpZGUoKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC50b2dnbGUucmV0dXJuUHJvZHVjdCkuaGlkZSgpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnRhYmxlLmFjdGlvbnMpLmhpZGUoKTtcbiAgfVxuXG4gIGluaXRGb3JtKGFjdGlvbk5hbWUsIGZvcm1BY3Rpb24sIGZvcm1DbGFzcykge1xuICAgIHRoaXMudXBkYXRlVm91Y2hlclJlZnVuZCgpO1xuXG4gICAgdGhpcy5jYW5jZWxQcm9kdWN0Rm9ybS5wcm9wKCdhY3Rpb24nLCBmb3JtQWN0aW9uKTtcbiAgICB0aGlzLmNhbmNlbFByb2R1Y3RGb3JtLnJlbW92ZUNsYXNzKCdzdGFuZGFyZC1yZWZ1bmQgcGFydGlhbC1yZWZ1bmQgcmV0dXJuLXByb2R1Y3QgY2FuY2VsLXByb2R1Y3QnKS5hZGRDbGFzcyhmb3JtQ2xhc3MpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LmJ1dHRvbnMuc2F2ZSkuaHRtbChhY3Rpb25OYW1lKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC50YWJsZS5oZWFkZXIpLmh0bWwoYWN0aW9uTmFtZSk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuY2hlY2tib3hlcy5yZXN0b2NrKS5wcm9wKCdjaGVja2VkJywgdGhpcy5vcmRlckRlbGl2ZXJlZCk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuY2hlY2tib3hlcy5jcmVkaXRTbGlwKS5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuY2hlY2tib3hlcy52b3VjaGVyKS5wcm9wKCdjaGVja2VkJywgZmFsc2UpO1xuICB9XG5cbiAgbGlzdGVuRm9ySW5wdXRzKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjaGFuZ2UnLCBPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuaW5wdXRzLnF1YW50aXR5LCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRwcm9kdWN0UXVhbnRpdHlJbnB1dCA9ICQoZXZlbnQudGFyZ2V0KTtcbiAgICAgIGlmICh0aGlzLnVzZUFtb3VudElucHV0cykge1xuICAgICAgICB0aGlzLnVwZGF0ZUFtb3VudElucHV0KCRwcm9kdWN0UXVhbnRpdHlJbnB1dCk7XG4gICAgICB9XG4gICAgICB0aGlzLnVwZGF0ZVZvdWNoZXJSZWZ1bmQoKTtcbiAgICB9KTtcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjaGFuZ2UnLCBPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuaW5wdXRzLmFtb3VudCwgKCkgPT4ge1xuICAgICAgdGhpcy51cGRhdGVWb3VjaGVyUmVmdW5kKCk7XG4gICAgfSk7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2hhbmdlJywgT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LmlucHV0cy5zZWxlY3RvciwgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkcHJvZHVjdENoZWNrYm94ID0gJChldmVudC50YXJnZXQpO1xuICAgICAgY29uc3QgJHBhcmVudENlbGwgPSAkcHJvZHVjdENoZWNrYm94LnBhcmVudHMoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnRhYmxlLmNlbGwpO1xuICAgICAgY29uc3QgcHJvZHVjdFF1YW50aXR5SW5wdXQgPSAkcGFyZW50Q2VsbC5maW5kKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5pbnB1dHMucXVhbnRpdHkpO1xuICAgICAgY29uc3QgcmVmdW5kYWJsZVF1YW50aXR5ID0gcGFyc2VJbnQocHJvZHVjdFF1YW50aXR5SW5wdXQuZGF0YSgncXVhbnRpdHlSZWZ1bmRhYmxlJyksIDEwKTtcbiAgICAgIGNvbnN0IHByb2R1Y3RRdWFudGl0eSA9IHBhcnNlSW50KHByb2R1Y3RRdWFudGl0eUlucHV0LnZhbCgpLCAxMCk7XG4gICAgICBpZiAoISRwcm9kdWN0Q2hlY2tib3guaXMoJzpjaGVja2VkJykpIHtcbiAgICAgICAgcHJvZHVjdFF1YW50aXR5SW5wdXQudmFsKDApO1xuICAgICAgfSBlbHNlIGlmIChOdW1iZXIuaXNOYU4ocHJvZHVjdFF1YW50aXR5KSB8fCBwcm9kdWN0UXVhbnRpdHkgPT09IDApIHtcbiAgICAgICAgcHJvZHVjdFF1YW50aXR5SW5wdXQudmFsKHJlZnVuZGFibGVRdWFudGl0eSk7XG4gICAgICB9XG4gICAgICB0aGlzLnVwZGF0ZVZvdWNoZXJSZWZ1bmQoKTtcbiAgICB9KTtcbiAgfVxuXG4gIHVwZGF0ZUFtb3VudElucHV0KCRwcm9kdWN0UXVhbnRpdHlJbnB1dCkge1xuICAgIGNvbnN0ICRwYXJlbnRDZWxsID0gJHByb2R1Y3RRdWFudGl0eUlucHV0LnBhcmVudHMoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnRhYmxlLmNlbGwpO1xuICAgIGNvbnN0ICRwcm9kdWN0QW1vdW50ID0gJHBhcmVudENlbGwuZmluZChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuaW5wdXRzLmFtb3VudCk7XG4gICAgY29uc3QgcHJvZHVjdFF1YW50aXR5ID0gcGFyc2VJbnQoJHByb2R1Y3RRdWFudGl0eUlucHV0LnZhbCgpLCAxMCk7XG4gICAgaWYgKHByb2R1Y3RRdWFudGl0eSA8PSAwKSB7XG4gICAgICAkcHJvZHVjdEFtb3VudC52YWwoMCk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCBwcmljZUZpZWxkTmFtZSA9IHRoaXMuaXNUYXhJbmNsdWRlZCA/ICdwcm9kdWN0UHJpY2VUYXhJbmNsJyA6ICdwcm9kdWN0UHJpY2VUYXhFeGNsJztcbiAgICBjb25zdCBwcm9kdWN0VW5pdFByaWNlID0gcGFyc2VGbG9hdCgkcHJvZHVjdFF1YW50aXR5SW5wdXQuZGF0YShwcmljZUZpZWxkTmFtZSkpO1xuICAgIGNvbnN0IGFtb3VudFJlZnVuZGFibGUgPSBwYXJzZUZsb2F0KCRwcm9kdWN0UXVhbnRpdHlJbnB1dC5kYXRhKCdhbW91bnRSZWZ1bmRhYmxlJykpO1xuICAgIGNvbnN0IGd1ZXNzZWRBbW91bnQgPSAocHJvZHVjdFVuaXRQcmljZSAqIHByb2R1Y3RRdWFudGl0eSkgPCBhbW91bnRSZWZ1bmRhYmxlID9cbiAgICAgIChwcm9kdWN0VW5pdFByaWNlICogcHJvZHVjdFF1YW50aXR5KSA6IGFtb3VudFJlZnVuZGFibGU7XG4gICAgY29uc3QgYW1vdW50VmFsdWUgPSBwYXJzZUZsb2F0KCRwcm9kdWN0QW1vdW50LnZhbCgpKTtcbiAgICBpZiAoJHByb2R1Y3RBbW91bnQudmFsKCkgPT09ICcnIHx8IGFtb3VudFZhbHVlID09PSAwIHx8IGFtb3VudFZhbHVlID4gZ3Vlc3NlZEFtb3VudCkge1xuICAgICAgJHByb2R1Y3RBbW91bnQudmFsKGd1ZXNzZWRBbW91bnQpO1xuICAgIH1cbiAgfVxuXG4gIGdldFJlZnVuZEFtb3VudCgpIHtcbiAgICBsZXQgdG90YWxBbW91bnQgPSAwO1xuXG4gICAgaWYgKHRoaXMudXNlQW1vdW50SW5wdXRzKSB7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5pbnB1dHMuYW1vdW50KS5lYWNoKChpbmRleCwgYW1vdW50KSA9PiB7XG4gICAgICAgIGNvbnN0IGZsb2F0VmFsdWUgPSBwYXJzZUZsb2F0KGFtb3VudC52YWx1ZSk7XG4gICAgICAgIHRvdGFsQW1vdW50ICs9ICFOdW1iZXIuaXNOYU4oZmxvYXRWYWx1ZSkgPyBmbG9hdFZhbHVlIDogMDtcbiAgICAgIH0pO1xuICAgIH0gZWxzZSB7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5pbnB1dHMucXVhbnRpdHkpLmVhY2goKGluZGV4LCBxdWFudGl0eSkgPT4ge1xuICAgICAgICBjb25zdCAkcXVhbnRpdHlJbnB1dCA9ICQocXVhbnRpdHkpO1xuICAgICAgICBjb25zdCBwcmljZUZpZWxkTmFtZSA9IHRoaXMuaXNUYXhJbmNsdWRlZCA/ICdwcm9kdWN0UHJpY2VUYXhJbmNsJyA6ICdwcm9kdWN0UHJpY2VUYXhFeGNsJztcbiAgICAgICAgY29uc3QgcHJvZHVjdFVuaXRQcmljZSA9IHBhcnNlRmxvYXQoJHF1YW50aXR5SW5wdXQuZGF0YShwcmljZUZpZWxkTmFtZSkpO1xuICAgICAgICBjb25zdCBwcm9kdWN0UXVhbnRpdHkgPSBwYXJzZUludCgkcXVhbnRpdHlJbnB1dC52YWwoKSwgMTApO1xuICAgICAgICB0b3RhbEFtb3VudCArPSBwcm9kdWN0UXVhbnRpdHkgKiBwcm9kdWN0VW5pdFByaWNlO1xuICAgICAgfSk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHRvdGFsQW1vdW50O1xuICB9XG5cbiAgdXBkYXRlVm91Y2hlclJlZnVuZCgpIHtcbiAgICBjb25zdCByZWZ1bmRBbW91bnQgPSB0aGlzLmdldFJlZnVuZEFtb3VudCgpO1xuXG4gICAgdGhpcy51cGRhdGVWb3VjaGVyUmVmdW5kVHlwZUxhYmVsKFxuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QucmFkaW9zLnZvdWNoZXJSZWZ1bmRUeXBlLnByb2R1Y3RQcmljZXMpLFxuICAgICAgcmVmdW5kQW1vdW50XG4gICAgKTtcbiAgICBjb25zdCByZWZ1bmRWb3VjaGVyRXhjbHVkZWQgPSByZWZ1bmRBbW91bnQgLSB0aGlzLmRpc2NvdW50c0Ftb3VudDtcbiAgICB0aGlzLnVwZGF0ZVZvdWNoZXJSZWZ1bmRUeXBlTGFiZWwoXG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5yYWRpb3Mudm91Y2hlclJlZnVuZFR5cGUucHJvZHVjdFByaWNlc1ZvdWNoZXJFeGNsdWRlZCksXG4gICAgICByZWZ1bmRWb3VjaGVyRXhjbHVkZWRcbiAgICApO1xuXG4gICAgLy8gRGlzYWJsZSB2b3VjaGVyIGV4Y2x1ZGVkIG9wdGlvbiB3aGVuIHRoZSB2b3VjaGVyIGFtb3VudCBpcyB0b28gaGlnaFxuICAgIGlmIChyZWZ1bmRWb3VjaGVyRXhjbHVkZWQgPCAwKSB7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5yYWRpb3Mudm91Y2hlclJlZnVuZFR5cGUucHJvZHVjdFByaWNlc1ZvdWNoZXJFeGNsdWRlZClcbiAgICAgICAgLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSlcbiAgICAgICAgLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5yYWRpb3Mudm91Y2hlclJlZnVuZFR5cGUucHJvZHVjdFByaWNlcykucHJvcCgnY2hlY2tlZCcsIHRydWUpO1xuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QucmFkaW9zLnZvdWNoZXJSZWZ1bmRUeXBlLm5lZ2F0aXZlRXJyb3JNZXNzYWdlKS5zaG93KCk7XG4gICAgfSBlbHNlIHtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnJhZGlvcy52b3VjaGVyUmVmdW5kVHlwZS5wcm9kdWN0UHJpY2VzVm91Y2hlckV4Y2x1ZGVkKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnJhZGlvcy52b3VjaGVyUmVmdW5kVHlwZS5uZWdhdGl2ZUVycm9yTWVzc2FnZSkuaGlkZSgpO1xuICAgIH1cbiAgfVxuXG4gIHVwZGF0ZVZvdWNoZXJSZWZ1bmRUeXBlTGFiZWwoJGlucHV0LCByZWZ1bmRBbW91bnQpIHtcbiAgICBjb25zdCBkZWZhdWx0TGFiZWwgPSAkaW5wdXQuZGF0YSgnZGVmYXVsdExhYmVsJyk7XG4gICAgY29uc3QgJGxhYmVsID0gJGlucHV0LnBhcmVudHMoJ2xhYmVsJyk7XG4gICAgY29uc3QgZm9ybWF0dGVkQW1vdW50ID0gdGhpcy5jdXJyZW5jeUZvcm1hdHRlci5mb3JtYXQocmVmdW5kQW1vdW50KTtcblxuICAgIC8vIENoYW5nZSB0aGUgZW5kaW5nIHRleHQgcGFydCBvbmx5IHRvIGF2b2lkIHJlbW92aW5nIHRoZSBpbnB1dCAodGhlIEVPTCBpcyBvbiBwdXJwb3NlIGZvciBiZXR0ZXIgZGlzcGxheSlcbiAgICAkbGFiZWwuZ2V0KDApLmxhc3RDaGlsZC5ub2RlVmFsdWUgPSBgXG4gICAgJHtkZWZhdWx0TGFiZWx9ICR7Zm9ybWF0dGVkQW1vdW50fWA7XG4gIH1cblxuICBzaG93Q2FuY2VsUHJvZHVjdEZvcm0oKSB7XG4gICAgY29uc3QgY2FuY2VsUHJvZHVjdFJvdXRlID0gdGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19jYW5jZWxsYXRpb24nLCB7b3JkZXJJZDogdGhpcy5vcmRlcklkfSk7XG4gICAgdGhpcy5pbml0Rm9ybShcbiAgICAgICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuYnV0dG9ucy5zYXZlKS5kYXRhKCdjYW5jZWxMYWJlbCcpLFxuICAgICAgICBjYW5jZWxQcm9kdWN0Um91dGUsXG4gICAgICAgICdjYW5jZWwtcHJvZHVjdCcsXG4gICAgKTtcbiAgICB0aGlzLmhpZGVDYW5jZWxFbGVtZW50cygpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnRvZ2dsZS5jYW5jZWxQcm9kdWN0cykuc2hvdygpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtY2FuY2VsLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICdAcGFnZXMvb3JkZXIvT3JkZXJWaWV3UGFnZU1hcCc7XG5pbXBvcnQge0V2ZW50RW1pdHRlcn0gZnJvbSAnQGNvbXBvbmVudHMvZXZlbnQtZW1pdHRlcic7XG5pbXBvcnQgT3JkZXJWaWV3RXZlbnRNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL3ZpZXcvb3JkZXItdmlldy1ldmVudC1tYXAnO1xuaW1wb3J0IE9yZGVyUHJpY2VzIGZyb20gJ0BwYWdlcy9vcmRlci92aWV3L29yZGVyLXByaWNlcyc7XG5pbXBvcnQgQ29uZmlybU1vZGFsIGZyb20gJ0Bjb21wb25lbnRzL21vZGFsJztcbmltcG9ydCBPcmRlclByaWNlc1JlZnJlc2hlciBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcmljZXMtcmVmcmVzaGVyJztcblxuY29uc3QgeyR9ID0gd2luZG93O1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPcmRlclByb2R1Y3RFZGl0IHtcbiAgY29uc3RydWN0b3Iob3JkZXJEZXRhaWxJZCkge1xuICAgIHRoaXMucm91dGVyID0gbmV3IFJvdXRlcigpO1xuICAgIHRoaXMub3JkZXJEZXRhaWxJZCA9IG9yZGVyRGV0YWlsSWQ7XG4gICAgdGhpcy5wcm9kdWN0Um93ID0gJChgI29yZGVyUHJvZHVjdF8ke3RoaXMub3JkZXJEZXRhaWxJZH1gKTtcbiAgICB0aGlzLnByb2R1Y3QgPSB7fTtcbiAgICB0aGlzLmN1cnJlbmN5UHJlY2lzaW9uID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGUpLmRhdGEoJ2N1cnJlbmN5UHJlY2lzaW9uJyk7XG4gICAgdGhpcy5wcmljZVRheENhbGN1bGF0b3IgPSBuZXcgT3JkZXJQcmljZXMoKTtcbiAgICB0aGlzLnByb2R1Y3RFZGl0U2F2ZUJ0biA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFNhdmVCdG4pO1xuICAgIHRoaXMucXVhbnRpdHlJbnB1dCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFF1YW50aXR5SW5wdXQpO1xuICAgIHRoaXMub3JkZXJQcmljZXNSZWZyZXNoZXIgPSBuZXcgT3JkZXJQcmljZXNSZWZyZXNoZXIoKTtcbiAgfVxuXG4gIHNldHVwTGlzdGVuZXIoKSB7XG4gICAgdGhpcy5xdWFudGl0eUlucHV0Lm9uKCdjaGFuZ2Uga2V5dXAnLCBldmVudCA9PiB7XG4gICAgICBjb25zdCBuZXdRdWFudGl0eSA9IE51bWJlcihldmVudC50YXJnZXQudmFsdWUpO1xuICAgICAgY29uc3QgYXZhaWxhYmxlUXVhbnRpdHkgPSBwYXJzZUludCgkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ2F2YWlsYWJsZVF1YW50aXR5JyksIDEwKTtcbiAgICAgIGNvbnN0IHByZXZpb3VzUXVhbnRpdHkgPSBwYXJzZUludCh0aGlzLnF1YW50aXR5SW5wdXQuZGF0YSgncHJldmlvdXNRdWFudGl0eScpLCAxMCk7XG4gICAgICBjb25zdCByZW1haW5pbmdBdmFpbGFibGUgPSBhdmFpbGFibGVRdWFudGl0eSAtIChuZXdRdWFudGl0eSAtIHByZXZpb3VzUXVhbnRpdHkpO1xuICAgICAgY29uc3QgYXZhaWxhYmxlT3V0T2ZTdG9jayA9IHRoaXMuYXZhaWxhYmxlVGV4dC5kYXRhKCdhdmFpbGFibGVPdXRPZlN0b2NrJyk7XG4gICAgICB0aGlzLmF2YWlsYWJsZVRleHQudGV4dChyZW1haW5pbmdBdmFpbGFibGUpO1xuICAgICAgdGhpcy5hdmFpbGFibGVUZXh0LnRvZ2dsZUNsYXNzKCd0ZXh0LWRhbmdlciBmb250LXdlaWdodC1ib2xkJywgcmVtYWluaW5nQXZhaWxhYmxlIDwgMCk7XG4gICAgICB0aGlzLnVwZGF0ZVRvdGFsKCk7XG4gICAgICBjb25zdCBkaXNhYmxlRWRpdEFjdGlvbkJ0biA9IG5ld1F1YW50aXR5IDw9IDAgfHwgKHJlbWFpbmluZ0F2YWlsYWJsZSA8IDAgJiYgIWF2YWlsYWJsZU91dE9mU3RvY2spO1xuICAgICAgdGhpcy5wcm9kdWN0RWRpdFNhdmVCdG4ucHJvcCgnZGlzYWJsZWQnLCBkaXNhYmxlRWRpdEFjdGlvbkJ0bik7XG4gICAgfSk7XG5cbiAgICB0aGlzLnByb2R1Y3RFZGl0SW52b2ljZVNlbGVjdC5vbignY2hhbmdlJywgKCkgPT4ge1xuICAgICAgdGhpcy5wcm9kdWN0RWRpdFNhdmVCdG4ucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gICAgfSk7XG5cbiAgICB0aGlzLnByaWNlVGF4SW5jbHVkZWRJbnB1dC5vbignY2hhbmdlIGtleXVwJywgZXZlbnQgPT4ge1xuICAgICAgdGhpcy50YXhJbmNsdWRlZCA9IHBhcnNlRmxvYXQoZXZlbnQudGFyZ2V0LnZhbHVlKTtcbiAgICAgIGNvbnN0IHRheEV4Y2x1ZGVkID0gdGhpcy5wcmljZVRheENhbGN1bGF0b3IuY2FsY3VsYXRlVGF4RXhjbHVkZWQoXG4gICAgICAgIHRoaXMudGF4SW5jbHVkZWQsXG4gICAgICAgIHRoaXMudGF4UmF0ZSxcbiAgICAgICAgdGhpcy5jdXJyZW5jeVByZWNpc2lvblxuICAgICAgKTtcbiAgICAgIHRoaXMucHJpY2VUYXhFeGNsdWRlZElucHV0LnZhbCh0YXhFeGNsdWRlZCk7XG4gICAgICB0aGlzLnVwZGF0ZVRvdGFsKCk7XG4gICAgfSk7XG5cbiAgICB0aGlzLnByaWNlVGF4RXhjbHVkZWRJbnB1dC5vbignY2hhbmdlIGtleXVwJywgZXZlbnQgPT4ge1xuICAgICAgY29uc3QgdGF4RXhjbHVkZWQgPSBwYXJzZUZsb2F0KGV2ZW50LnRhcmdldC52YWx1ZSk7XG4gICAgICB0aGlzLnRheEluY2x1ZGVkID0gdGhpcy5wcmljZVRheENhbGN1bGF0b3IuY2FsY3VsYXRlVGF4SW5jbHVkZWQoXG4gICAgICAgIHRheEV4Y2x1ZGVkLFxuICAgICAgICB0aGlzLnRheFJhdGUsXG4gICAgICAgIHRoaXMuY3VycmVuY3lQcmVjaXNpb25cbiAgICAgICk7XG4gICAgICB0aGlzLnByaWNlVGF4SW5jbHVkZWRJbnB1dC52YWwodGhpcy50YXhJbmNsdWRlZCk7XG4gICAgICB0aGlzLnVwZGF0ZVRvdGFsKCk7XG4gICAgfSk7XG5cbiAgICB0aGlzLnByb2R1Y3RFZGl0U2F2ZUJ0bi5vbignY2xpY2snLCBldmVudCA9PiB7XG4gICAgICBjb25zdCAkYnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0IGNvbmZpcm1lZCA9IHdpbmRvdy5jb25maXJtKCRidG4uZGF0YSgndXBkYXRlTWVzc2FnZScpKTtcblxuICAgICAgaWYgKCFjb25maXJtZWQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICAkYnRuLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gICAgICB0aGlzLmhhbmRsZUVkaXRQcm9kdWN0V2l0aENvbmZpcm1hdGlvbk1vZGFsKGV2ZW50KTtcbiAgICB9KTtcblxuICAgIHRoaXMucHJvZHVjdEVkaXRDYW5jZWxCdG4ub24oJ2NsaWNrJywgKCkgPT4ge1xuICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoT3JkZXJWaWV3RXZlbnRNYXAucHJvZHVjdEVkaXRpb25DYW5jZWxlZCwge1xuICAgICAgICBvcmRlckRldGFpbElkOiB0aGlzLm9yZGVyRGV0YWlsSWRcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9XG5cbiAgdXBkYXRlVG90YWwoKSB7XG4gICAgY29uc3QgdXBkYXRlZFRvdGFsID0gdGhpcy5wcmljZVRheENhbGN1bGF0b3IuY2FsY3VsYXRlVG90YWxQcmljZShcbiAgICAgIHRoaXMucXVhbnRpdHksXG4gICAgICB0aGlzLnRheEluY2x1ZGVkLFxuICAgICAgdGhpcy5jdXJyZW5jeVByZWNpc2lvblxuICAgICk7XG4gICAgdGhpcy5wcmljZVRvdGFsVGV4dC5odG1sKHVwZGF0ZWRUb3RhbCk7XG4gICAgdGhpcy5wcm9kdWN0RWRpdFNhdmVCdG4ucHJvcCgnZGlzYWJsZWQnLCB1cGRhdGVkVG90YWwgPT09IHRoaXMuaW5pdGlhbFRvdGFsKTtcbiAgfVxuXG4gIGRpc3BsYXlQcm9kdWN0KHByb2R1Y3QpIHtcbiAgICB0aGlzLnByb2R1Y3RSb3dFZGl0ID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0Um93VGVtcGxhdGUpLmNsb25lKHRydWUpO1xuICAgIHRoaXMucHJvZHVjdFJvd0VkaXQuYXR0cignaWQnLCBgZWRpdE9yZGVyUHJvZHVjdF8ke3RoaXMub3JkZXJEZXRhaWxJZH1gKTtcbiAgICB0aGlzLnByb2R1Y3RSb3dFZGl0LmZpbmQoJypbaWRdJykuZWFjaChmdW5jdGlvbiByZW1vdmVBbGxJZHMoKSB7XG4gICAgICAkKHRoaXMpLnJlbW92ZUF0dHIoJ2lkJyk7XG4gICAgfSk7XG5cbiAgICAvLyBGaW5kIGNvbnRyb2xzXG4gICAgdGhpcy5wcm9kdWN0RWRpdFNhdmVCdG4gPSB0aGlzLnByb2R1Y3RSb3dFZGl0LmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFNhdmVCdG4pO1xuICAgIHRoaXMucHJvZHVjdEVkaXRDYW5jZWxCdG4gPSB0aGlzLnByb2R1Y3RSb3dFZGl0LmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdENhbmNlbEJ0bik7XG4gICAgdGhpcy5wcm9kdWN0RWRpdEludm9pY2VTZWxlY3QgPSB0aGlzLnByb2R1Y3RSb3dFZGl0LmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdEludm9pY2VTZWxlY3QpO1xuICAgIHRoaXMucHJvZHVjdEVkaXRJbWFnZSA9IHRoaXMucHJvZHVjdFJvd0VkaXQuZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0SW1hZ2UpO1xuICAgIHRoaXMucHJvZHVjdEVkaXROYW1lID0gdGhpcy5wcm9kdWN0Um93RWRpdC5maW5kKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXROYW1lKTtcbiAgICB0aGlzLnByaWNlVGF4SW5jbHVkZWRJbnB1dCA9IHRoaXMucHJvZHVjdFJvd0VkaXQuZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0UHJpY2VUYXhJbmNsSW5wdXQpO1xuICAgIHRoaXMucHJpY2VUYXhFeGNsdWRlZElucHV0ID0gdGhpcy5wcm9kdWN0Um93RWRpdC5maW5kKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRQcmljZVRheEV4Y2xJbnB1dCk7XG4gICAgdGhpcy5xdWFudGl0eUlucHV0ID0gdGhpcy5wcm9kdWN0Um93RWRpdC5maW5kKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRRdWFudGl0eUlucHV0KTtcbiAgICB0aGlzLmxvY2F0aW9uVGV4dCA9IHRoaXMucHJvZHVjdFJvd0VkaXQuZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0TG9jYXRpb25UZXh0KTtcbiAgICB0aGlzLmF2YWlsYWJsZVRleHQgPSB0aGlzLnByb2R1Y3RSb3dFZGl0LmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdEF2YWlsYWJsZVRleHQpO1xuICAgIHRoaXMucHJpY2VUb3RhbFRleHQgPSB0aGlzLnByb2R1Y3RSb3dFZGl0LmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFRvdGFsUHJpY2VUZXh0KTtcblxuICAgIC8vIEluaXQgaW5wdXQgdmFsdWVzXG4gICAgdGhpcy5wcmljZVRheEV4Y2x1ZGVkSW5wdXQudmFsKHdpbmRvdy5wc19yb3VuZChwcm9kdWN0LnByaWNlX3RheF9leGNsLCB0aGlzLmN1cnJlbmN5UHJlY2lzaW9uKSk7XG5cbiAgICB0aGlzLnByaWNlVGF4SW5jbHVkZWRJbnB1dC52YWwod2luZG93LnBzX3JvdW5kKHByb2R1Y3QucHJpY2VfdGF4X2luY2wsIHRoaXMuY3VycmVuY3lQcmVjaXNpb24pKTtcblxuICAgIHRoaXMucXVhbnRpdHlJbnB1dFxuICAgICAgLnZhbChwcm9kdWN0LnF1YW50aXR5KVxuICAgICAgLmRhdGEoJ2F2YWlsYWJsZVF1YW50aXR5JywgcHJvZHVjdC5hdmFpbGFibGVRdWFudGl0eSlcbiAgICAgIC5kYXRhKCdwcmV2aW91c1F1YW50aXR5JywgcHJvZHVjdC5xdWFudGl0eSk7XG4gICAgdGhpcy5hdmFpbGFibGVUZXh0LmRhdGEoJ2F2YWlsYWJsZU91dE9mU3RvY2snLCBwcm9kdWN0LmF2YWlsYWJsZU91dE9mU3RvY2spO1xuXG4gICAgLy8gc2V0IHRoaXMgcHJvZHVjdCdzIG9yZGVySW52b2ljZUlkIGFzIHNlbGVjdGVkXG4gICAgaWYgKHByb2R1Y3Qub3JkZXJJbnZvaWNlSWQpIHtcbiAgICAgIHRoaXMucHJvZHVjdEVkaXRJbnZvaWNlU2VsZWN0LnZhbChwcm9kdWN0Lm9yZGVySW52b2ljZUlkKTtcbiAgICB9XG5cbiAgICAvLyBJbml0IGVkaXRvciBkYXRhXG4gICAgdGhpcy50YXhSYXRlID0gcHJvZHVjdC50YXhfcmF0ZTtcbiAgICB0aGlzLmluaXRpYWxUb3RhbCA9IHRoaXMucHJpY2VUYXhDYWxjdWxhdG9yLmNhbGN1bGF0ZVRvdGFsUHJpY2UoXG4gICAgICBwcm9kdWN0LnF1YW50aXR5LFxuICAgICAgcHJvZHVjdC5wcmljZV90YXhfaW5jbCxcbiAgICAgIHRoaXMuY3VycmVuY3lQcmVjaXNpb25cbiAgICApO1xuICAgIHRoaXMucXVhbnRpdHkgPSBwcm9kdWN0LnF1YW50aXR5O1xuICAgIHRoaXMudGF4SW5jbHVkZWQgPSBwcm9kdWN0LnByaWNlX3RheF9pbmNsO1xuXG4gICAgLy8gQ29weSBwcm9kdWN0IGNvbnRlbnQgaW4gY2VsbHNcbiAgICB0aGlzLnByb2R1Y3RFZGl0SW1hZ2UuaHRtbCh0aGlzLnByb2R1Y3RSb3cuZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0SW1hZ2UpLmh0bWwoKSk7XG4gICAgdGhpcy5wcm9kdWN0RWRpdE5hbWUuaHRtbCh0aGlzLnByb2R1Y3RSb3cuZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0TmFtZSkuaHRtbCgpKTtcbiAgICB0aGlzLmxvY2F0aW9uVGV4dC5odG1sKHByb2R1Y3QubG9jYXRpb24pO1xuICAgIHRoaXMuYXZhaWxhYmxlVGV4dC5odG1sKHByb2R1Y3QuYXZhaWxhYmxlUXVhbnRpdHkpO1xuICAgIHRoaXMucHJpY2VUb3RhbFRleHQuaHRtbCh0aGlzLmluaXRpYWxUb3RhbCk7XG4gICAgdGhpcy5wcm9kdWN0Um93LmFkZENsYXNzKCdkLW5vbmUnKS5hZnRlcih0aGlzLnByb2R1Y3RSb3dFZGl0LnJlbW92ZUNsYXNzKCdkLW5vbmUnKSk7XG5cbiAgICB0aGlzLnNldHVwTGlzdGVuZXIoKTtcbiAgfVxuXG4gIGhhbmRsZUVkaXRQcm9kdWN0V2l0aENvbmZpcm1hdGlvbk1vZGFsKGV2ZW50KSB7XG4gICAgY29uc3QgcHJvZHVjdEVkaXRCdG4gPSAkKGAjb3JkZXJQcm9kdWN0XyR7dGhpcy5vcmRlckRldGFpbElkfSAke09yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRCdXR0b25zfWApO1xuICAgIGNvbnN0IHByb2R1Y3RJZCA9IHByb2R1Y3RFZGl0QnRuLmRhdGEoJ3Byb2R1Y3QtaWQnKTtcbiAgICBjb25zdCBjb21iaW5hdGlvbklkID0gcHJvZHVjdEVkaXRCdG4uZGF0YSgnY29tYmluYXRpb24taWQnKTtcbiAgICBjb25zdCBvcmRlckludm9pY2VJZCA9IHByb2R1Y3RFZGl0QnRuLmRhdGEoJ29yZGVyLWludm9pY2UtaWQnKTtcbiAgICBjb25zdCBwcm9kdWN0UHJpY2VNYXRjaCA9IHRoaXMub3JkZXJQcmljZXNSZWZyZXNoZXIuY2hlY2tPdGhlclByb2R1Y3RQcmljZXNNYXRjaChcbiAgICAgIHRoaXMucHJpY2VUYXhJbmNsdWRlZElucHV0LnZhbCgpLFxuICAgICAgcHJvZHVjdElkLFxuICAgICAgY29tYmluYXRpb25JZCxcbiAgICAgIG9yZGVySW52b2ljZUlkLFxuICAgICAgdGhpcy5vcmRlckRldGFpbElkXG4gICAgKTtcblxuICAgIGlmIChwcm9kdWN0UHJpY2VNYXRjaCkge1xuICAgICAgdGhpcy5lZGl0UHJvZHVjdCgkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ29yZGVySWQnKSwgdGhpcy5vcmRlckRldGFpbElkKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGNvbnN0IG1vZGFsRWRpdFByaWNlID0gbmV3IENvbmZpcm1Nb2RhbChcbiAgICAgIHtcbiAgICAgICAgaWQ6ICdtb2RhbC1jb25maXJtLW5ldy1wcmljZScsXG4gICAgICAgIGNvbmZpcm1UaXRsZTogdGhpcy5wcm9kdWN0RWRpdEludm9pY2VTZWxlY3QuZGF0YSgnbW9kYWwtZWRpdC1wcmljZS10aXRsZScpLFxuICAgICAgICBjb25maXJtTWVzc2FnZTogdGhpcy5wcm9kdWN0RWRpdEludm9pY2VTZWxlY3QuZGF0YSgnbW9kYWwtZWRpdC1wcmljZS1ib2R5JyksXG4gICAgICAgIGNvbmZpcm1CdXR0b25MYWJlbDogdGhpcy5wcm9kdWN0RWRpdEludm9pY2VTZWxlY3QuZGF0YSgnICcpLFxuICAgICAgICBjbG9zZUJ1dHRvbkxhYmVsOiB0aGlzLnByb2R1Y3RFZGl0SW52b2ljZVNlbGVjdC5kYXRhKCdtb2RhbC1lZGl0LXByaWNlLWNhbmNlbCcpXG4gICAgICB9LFxuICAgICAgKCkgPT4ge1xuICAgICAgICB0aGlzLmVkaXRQcm9kdWN0KCQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnb3JkZXJJZCcpLCB0aGlzLm9yZGVyRGV0YWlsSWQpO1xuICAgICAgfVxuICAgICk7XG5cbiAgICBtb2RhbEVkaXRQcmljZS5zaG93KCk7XG4gIH1cblxuICBlZGl0UHJvZHVjdChvcmRlcklkLCBvcmRlckRldGFpbElkKSB7XG4gICAgY29uc3QgcGFyYW1zID0ge1xuICAgICAgcHJpY2VfdGF4X2luY2w6IHRoaXMucHJpY2VUYXhJbmNsdWRlZElucHV0LnZhbCgpLFxuICAgICAgcHJpY2VfdGF4X2V4Y2w6IHRoaXMucHJpY2VUYXhFeGNsdWRlZElucHV0LnZhbCgpLFxuICAgICAgcXVhbnRpdHk6IHRoaXMucXVhbnRpdHlJbnB1dC52YWwoKSxcbiAgICAgIGludm9pY2U6IHRoaXMucHJvZHVjdEVkaXRJbnZvaWNlU2VsZWN0LnZhbCgpXG4gICAgfTtcblxuICAgICQuYWpheCh7XG4gICAgICB1cmw6IHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9vcmRlcnNfdXBkYXRlX3Byb2R1Y3QnLCB7XG4gICAgICAgIG9yZGVySWQsXG4gICAgICAgIG9yZGVyRGV0YWlsSWRcbiAgICAgIH0pLFxuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICBkYXRhOiBwYXJhbXNcbiAgICB9KS50aGVuKFxuICAgICAgcmVzcG9uc2UgPT4ge1xuICAgICAgICBFdmVudEVtaXR0ZXIuZW1pdChPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0VXBkYXRlZCwge1xuICAgICAgICAgIG9yZGVySWQsXG4gICAgICAgICAgb3JkZXJEZXRhaWxJZCxcbiAgICAgICAgICBuZXdSb3c6IHJlc3BvbnNlXG4gICAgICAgIH0pO1xuICAgICAgfSxcbiAgICAgIHJlc3BvbnNlID0+IHtcbiAgICAgICAgaWYgKHJlc3BvbnNlLnJlc3BvbnNlSlNPTiAmJiByZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSkge1xuICAgICAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IHJlc3BvbnNlLnJlc3BvbnNlSlNPTi5tZXNzYWdlfSk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICApO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtZWRpdC5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IFJvdXRlciBmcm9tICdAY29tcG9uZW50cy9yb3V0ZXInO1xuaW1wb3J0IHtFdmVudEVtaXR0ZXJ9IGZyb20gJ0Bjb21wb25lbnRzL2V2ZW50LWVtaXR0ZXInO1xuaW1wb3J0IE9yZGVyVmlld0V2ZW50TWFwIGZyb20gJ0BwYWdlcy9vcmRlci92aWV3L29yZGVyLXZpZXctZXZlbnQtbWFwJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPcmRlclByb2R1Y3RNYW5hZ2VyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gIH1cblxuICBoYW5kbGVEZWxldGVQcm9kdWN0RXZlbnQoZXZlbnQpIHtcbiAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgY29uc3QgY29uZmlybWVkID0gd2luZG93LmNvbmZpcm0oJGJ0bi5kYXRhKCdkZWxldGVNZXNzYWdlJykpO1xuICAgIGlmICghY29uZmlybWVkKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgJGJ0bi5wc3Rvb2x0aXAoJ2Rpc3Bvc2UnKTtcbiAgICAkYnRuLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gICAgdGhpcy5kZWxldGVQcm9kdWN0KCRidG4uZGF0YSgnb3JkZXJJZCcpLCAkYnRuLmRhdGEoJ29yZGVyRGV0YWlsSWQnKSk7XG4gIH1cblxuICBkZWxldGVQcm9kdWN0KG9yZGVySWQsIG9yZGVyRGV0YWlsSWQpIHtcbiAgICAkLmFqYXgodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19kZWxldGVfcHJvZHVjdCcsIHtvcmRlcklkLCBvcmRlckRldGFpbElkfSksIHtcbiAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgIH0pLnRoZW4oKCkgPT4ge1xuICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoT3JkZXJWaWV3RXZlbnRNYXAucHJvZHVjdERlbGV0ZWRGcm9tT3JkZXIsIHtcbiAgICAgICAgb2xkT3JkZXJEZXRhaWxJZDogb3JkZXJEZXRhaWxJZCxcbiAgICAgICAgb3JkZXJJZCxcbiAgICAgIH0pO1xuICAgIH0sIChyZXNwb25zZSkgPT4ge1xuICAgICAgaWYgKHJlc3BvbnNlLm1lc3NhZ2UpIHtcbiAgICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogcmVzcG9uc2UubWVzc2FnZX0pO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtbWFuYWdlci5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IFJvdXRlciBmcm9tICdAY29tcG9uZW50cy9yb3V0ZXInO1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL09yZGVyVmlld1BhZ2VNYXAnO1xuXG5jb25zdCB7JH0gPSB3aW5kb3c7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIE9yZGVyUHJvZHVjdHNSZWZyZXNoZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgfVxuXG4gIHJlZnJlc2gob3JkZXJJZCkge1xuICAgICQuYWpheCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fb3JkZXJzX2dldF9wcm9kdWN0cycsIHtvcmRlcklkfSkpLnRoZW4oKHJlc3BvbnNlKSA9PiB7XG4gICAgICBjb25zdCAkb3JkZXJQcm9kdWN0cyA9IHJlc3BvbnNlLnByb2R1Y3RzO1xuICAgICAgY29uc3QgJG9yZGVyRGV0YWlsSWRzID0gJG9yZGVyUHJvZHVjdHMubWFwKHByb2R1Y3QgPT4gcHJvZHVjdC5vcmRlckRldGFpbElkKTtcblxuICAgICAgLy8gUmVtb3ZlIHByb2R1Y3RzIHRoYXQgZG9uJ3QgYmVsb25nIHRvIHRoZSBvcmRlciBhbnltb3JlXG4gICAgICBsZXQgb3JkZXJEZXRhaWxSb3dzID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgndHIuY2VsbFByb2R1Y3QnKTtcbiAgICAgIG9yZGVyRGV0YWlsUm93cy5mb3JFYWNoKG9yZGVyRGV0YWlsUm93ID0+IHtcbiAgICAgICAgY29uc3QgJG9yZGVyRGV0YWlsUm93SWQgPSBwYXJzZUludCgoJChvcmRlckRldGFpbFJvdykuYXR0cignaWQnKS5tYXRjaCgvXFxkKy9nKSlbMF0sIDEwKTtcblxuICAgICAgICBpZiAoISAkb3JkZXJEZXRhaWxJZHMuaW5jbHVkZXMoJG9yZGVyRGV0YWlsUm93SWQpKSB7XG4gICAgICAgICAgdGhpcy5yZW1vdmVQcm9kdWN0Um93KCRvcmRlckRldGFpbFJvd0lkKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG5cbiAgICAgIC8vIEFkZCBwcm9kdWN0cyB0aGF0IGFyZSBub3QgZGlzcGxheWVkXG4gICAgICAvLyBQYWdlIG5lZWRzIHRvIGJlIHJlZnJlc2hlZCA/XG4gICAgfSk7XG4gIH1cblxuICByZW1vdmVQcm9kdWN0Um93KG9yZGVyRGV0YWlsUm93SWQpIHtcbiAgICAvLyBSZW1vdmUgdGhlIHJvd1xuICAgIGNvbnN0ICRyb3cgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVJvdyhvcmRlckRldGFpbFJvd0lkKSk7XG4gICAgY29uc3QgJG5leHQgPSAkcm93Lm5leHQoKTtcbiAgICAkcm93LnJlbW92ZSgpO1xuICAgIGlmICgkbmV4dC5oYXNDbGFzcygnb3JkZXItcHJvZHVjdC1jdXN0b21pemF0aW9uJykpIHtcbiAgICAgICRuZXh0LnJlbW92ZSgpO1xuICAgIH1cbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcm9kdWN0cy1yZWZyZXNoZXIuanMiLCJyZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5udW1iZXIuaXMtbmFuJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4uLy4uL21vZHVsZXMvX2NvcmUnKS5OdW1iZXIuaXNOYU47XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9mbi9udW1iZXIvaXMtbmFuLmpzXG4vLyBtb2R1bGUgaWQgPSA1OTJcbi8vIG1vZHVsZSBjaHVua3MgPSAzIiwiLy8gMjAuMS4yLjQgTnVtYmVyLmlzTmFOKG51bWJlcilcbnZhciAkZXhwb3J0ID0gcmVxdWlyZSgnLi9fZXhwb3J0Jyk7XG5cbiRleHBvcnQoJGV4cG9ydC5TLCAnTnVtYmVyJywge1xuICBpc05hTjogZnVuY3Rpb24gaXNOYU4obnVtYmVyKXtcbiAgICByZXR1cm4gbnVtYmVyICE9IG51bWJlcjtcbiAgfVxufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5udW1iZXIuaXMtbmFuLmpzXG4vLyBtb2R1bGUgaWQgPSA2MDFcbi8vIG1vZHVsZSBjaHVua3MgPSAzIl0sInNvdXJjZVJvb3QiOiIifQ==