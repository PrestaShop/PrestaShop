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
/******/ 	return __webpack_require__(__webpack_require__.s = 525);
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
  , toPrimitive    = __webpack_require__(14)
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
  , ctx       = __webpack_require__(13)
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
/* 14 */
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
/* 15 */,
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
var IObject = __webpack_require__(52)
  , defined = __webpack_require__(35);
module.exports = function(it){
  return IObject(defined(it));
};

/***/ }),
/* 23 */
/***/ (function(module, exports, __webpack_require__) {

var store      = __webpack_require__(50)('wks')
  , uid        = __webpack_require__(40)
  , Symbol     = __webpack_require__(5).Symbol
  , USE_SYMBOL = typeof Symbol == 'function';

var $exports = module.exports = function(name){
  return store[name] || (store[name] =
    USE_SYMBOL && Symbol[name] || (USE_SYMBOL ? Symbol : uid)('Symbol.' + name));
};

$exports.store = store;

/***/ }),
/* 24 */,
/* 25 */
/***/ (function(module, exports) {

var hasOwnProperty = {}.hasOwnProperty;
module.exports = function(it, key){
  return hasOwnProperty.call(it, key);
};

/***/ }),
/* 26 */,
/* 27 */,
/* 28 */,
/* 29 */,
/* 30 */,
/* 31 */,
/* 32 */,
/* 33 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 / 15.2.3.14 Object.keys(O)
var $keys       = __webpack_require__(55)
  , enumBugKeys = __webpack_require__(49);

module.exports = Object.keys || function keys(O){
  return $keys(O, enumBugKeys);
};

/***/ }),
/* 34 */,
/* 35 */
/***/ (function(module, exports) {

// 7.2.1 RequireObjectCoercible(argument)
module.exports = function(it){
  if(it == undefined)throw TypeError("Can't call method on  " + it);
  return it;
};

/***/ }),
/* 36 */
/***/ (function(module, exports) {

// 7.1.4 ToInteger
var ceil  = Math.ceil
  , floor = Math.floor;
module.exports = function(it){
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};

/***/ }),
/* 37 */,
/* 38 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.EventEmitter = undefined;

var _events = __webpack_require__(56);

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
/* 39 */,
/* 40 */
/***/ (function(module, exports) {

var id = 0
  , px = Math.random();
module.exports = function(key){
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};

/***/ }),
/* 41 */,
/* 42 */,
/* 43 */,
/* 44 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.13 ToObject(argument)
var defined = __webpack_require__(35);
module.exports = function(it){
  return Object(defined(it));
};

/***/ }),
/* 45 */
/***/ (function(module, exports, __webpack_require__) {

var shared = __webpack_require__(50)('keys')
  , uid    = __webpack_require__(40);
module.exports = function(key){
  return shared[key] || (shared[key] = uid(key));
};

/***/ }),
/* 46 */,
/* 47 */
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
/***/ (function(module, exports) {

module.exports = {};

/***/ }),
/* 52 */
/***/ (function(module, exports, __webpack_require__) {

// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = __webpack_require__(48);
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function(it){
  return cof(it) == 'String' ? it.split('') : Object(it);
};

/***/ }),
/* 53 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.15 ToLength
var toInteger = __webpack_require__(36)
  , min       = Math.min;
module.exports = function(it){
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};

/***/ }),
/* 54 */
/***/ (function(module, exports) {

exports.f = {}.propertyIsEnumerable;

/***/ }),
/* 55 */
/***/ (function(module, exports, __webpack_require__) {

var has          = __webpack_require__(25)
  , toIObject    = __webpack_require__(22)
  , arrayIndexOf = __webpack_require__(57)(false)
  , IE_PROTO     = __webpack_require__(45)('IE_PROTO');

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
/* 57 */
/***/ (function(module, exports, __webpack_require__) {

// false -> Array#indexOf
// true  -> Array#includes
var toIObject = __webpack_require__(22)
  , toLength  = __webpack_require__(53)
  , toIndex   = __webpack_require__(58);
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
/* 58 */
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__(36)
  , max       = Math.max
  , min       = Math.min;
module.exports = function(index, length){
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};

/***/ }),
/* 59 */
/***/ (function(module, exports) {

exports.f = Object.getOwnPropertySymbols;

/***/ }),
/* 60 */
/***/ (function(module, exports, __webpack_require__) {

var def = __webpack_require__(6).f
  , has = __webpack_require__(25)
  , TAG = __webpack_require__(23)('toStringTag');

module.exports = function(it, tag, stat){
  if(it && !has(it = stat ? it : it.prototype, TAG))def(it, TAG, {configurable: true, value: tag});
};

/***/ }),
/* 61 */,
/* 62 */,
/* 63 */
/***/ (function(module, exports) {

module.exports = true;

/***/ }),
/* 64 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $at  = __webpack_require__(99)(true);

// 21.1.3.27 String.prototype[@@iterator]()
__webpack_require__(67)(String, 'String', function(iterated){
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

var _assign = __webpack_require__(83);

var _assign2 = _interopRequireDefault(_assign);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _fosRouting = __webpack_require__(180);

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
/* 66 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
var anObject    = __webpack_require__(11)
  , dPs         = __webpack_require__(98)
  , enumBugKeys = __webpack_require__(49)
  , IE_PROTO    = __webpack_require__(45)('IE_PROTO')
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
  __webpack_require__(93).appendChild(iframe);
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
/* 67 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var LIBRARY        = __webpack_require__(63)
  , $export        = __webpack_require__(8)
  , redefine       = __webpack_require__(76)
  , hide           = __webpack_require__(10)
  , has            = __webpack_require__(25)
  , Iterators      = __webpack_require__(51)
  , $iterCreate    = __webpack_require__(97)
  , setToStringTag = __webpack_require__(60)
  , getPrototypeOf = __webpack_require__(82)
  , ITERATOR       = __webpack_require__(23)('iterator')
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
/* 68 */,
/* 69 */,
/* 70 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(85), __esModule: true };

/***/ }),
/* 71 */
/***/ (function(module, exports, __webpack_require__) {

var global         = __webpack_require__(5)
  , core           = __webpack_require__(3)
  , LIBRARY        = __webpack_require__(63)
  , wksExt         = __webpack_require__(72)
  , defineProperty = __webpack_require__(6).f;
module.exports = function(name){
  var $Symbol = core.Symbol || (core.Symbol = LIBRARY ? {} : global.Symbol || {});
  if(name.charAt(0) != '_' && !(name in $Symbol))defineProperty($Symbol, name, {value: wksExt.f(name)});
};

/***/ }),
/* 72 */
/***/ (function(module, exports, __webpack_require__) {

exports.f = __webpack_require__(23);

/***/ }),
/* 73 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(100);
var global        = __webpack_require__(5)
  , hide          = __webpack_require__(10)
  , Iterators     = __webpack_require__(51)
  , TO_STRING_TAG = __webpack_require__(23)('toStringTag');

for(var collections = ['NodeList', 'DOMTokenList', 'MediaList', 'StyleSheetList', 'CSSRuleList'], i = 0; i < 5; i++){
  var NAME       = collections[i]
    , Collection = global[NAME]
    , proto      = Collection && Collection.prototype;
  if(proto && !proto[TO_STRING_TAG])hide(proto, TO_STRING_TAG, NAME);
  Iterators[NAME] = Iterators.Array;
}

/***/ }),
/* 74 */,
/* 75 */,
/* 76 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(10);

/***/ }),
/* 77 */,
/* 78 */
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
/* 79 */,
/* 80 */,
/* 81 */,
/* 82 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.9 / 15.2.3.2 Object.getPrototypeOf(O)
var has         = __webpack_require__(25)
  , toObject    = __webpack_require__(44)
  , IE_PROTO    = __webpack_require__(45)('IE_PROTO')
  , ObjectProto = Object.prototype;

module.exports = Object.getPrototypeOf || function(O){
  O = toObject(O);
  if(has(O, IE_PROTO))return O[IE_PROTO];
  if(typeof O.constructor == 'function' && O instanceof O.constructor){
    return O.constructor.prototype;
  } return O instanceof Object ? ObjectProto : null;
};

/***/ }),
/* 83 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(84), __esModule: true };

/***/ }),
/* 84 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(89);
module.exports = __webpack_require__(3).Object.assign;

/***/ }),
/* 85 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(90);
module.exports = __webpack_require__(3).Object.keys;

/***/ }),
/* 86 */
/***/ (function(module, exports, __webpack_require__) {

// getting tag from 19.1.3.6 Object.prototype.toString()
var cof = __webpack_require__(48)
  , TAG = __webpack_require__(23)('toStringTag')
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
/* 87 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 19.1.2.1 Object.assign(target, source, ...)
var getKeys  = __webpack_require__(33)
  , gOPS     = __webpack_require__(59)
  , pIE      = __webpack_require__(54)
  , toObject = __webpack_require__(44)
  , IObject  = __webpack_require__(52)
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
/* 88 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.7 / 15.2.3.4 Object.getOwnPropertyNames(O)
var $keys      = __webpack_require__(55)
  , hiddenKeys = __webpack_require__(49).concat('length', 'prototype');

exports.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O){
  return $keys(O, hiddenKeys);
};

/***/ }),
/* 89 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.3.1 Object.assign(target, source)
var $export = __webpack_require__(8);

$export($export.S + $export.F, 'Object', {assign: __webpack_require__(87)});

/***/ }),
/* 90 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 Object.keys(O)
var toObject = __webpack_require__(44)
  , $keys    = __webpack_require__(33);

__webpack_require__(78)('keys', function(){
  return function keys(it){
    return $keys(toObject(it));
  };
});

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
  viewOrderPaymentsAlert: '.js-view-order-payments-alert',
  privateNoteToggleBtn: '.js-private-note-toggle-btn',
  privateNoteBlock: '.js-private-note-block',
  privateNoteInput: '#private_note_note',
  privateNoteSubmitBtn: '.js-private-note-btn',
  addCartRuleModal: '#addOrderDiscountModal',
  addCartRuleInvoiceIdSelect: '#add_order_cart_rule_invoice_id',
  addCartRuleNameInput: '#add_order_cart_rule_name',
  addCartRuleTypeSelect: '#add_order_cart_rule_type',
  addCartRuleValueInput: '#add_order_cart_rule_value',
  addCartRuleValueUnit: '#add_order_cart_rule_value_unit',
  addCartRuleSubmit: '#add_order_cart_rule_submit',
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
  orderShippingTabCount: '#orderShippingTab .count',
  orderShippingTabBody: '#orderShippingTabContent .card-body',
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
  orderShippingTotalContainer: '#order-shipping-total-container',
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
  printOrderViewPageButton: '.js-print-order-view-page',
  refreshProductsListLoadingSpinner: '#orderProductsPanel .spinner-order-products-container#orderProductsLoading'
};

/***/ }),
/* 92 */,
/* 93 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(5).document && document.documentElement;

/***/ }),
/* 94 */
/***/ (function(module, exports) {

module.exports = function(done, value){
  return {value: value, done: !!done};
};

/***/ }),
/* 95 */
/***/ (function(module, exports, __webpack_require__) {

var classof   = __webpack_require__(86)
  , ITERATOR  = __webpack_require__(23)('iterator')
  , Iterators = __webpack_require__(51);
module.exports = __webpack_require__(3).getIteratorMethod = function(it){
  if(it != undefined)return it[ITERATOR]
    || it['@@iterator']
    || Iterators[classof(it)];
};

/***/ }),
/* 96 */
/***/ (function(module, exports) {

module.exports = function(){ /* empty */ };

/***/ }),
/* 97 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var create         = __webpack_require__(66)
  , descriptor     = __webpack_require__(12)
  , setToStringTag = __webpack_require__(60)
  , IteratorPrototype = {};

// 25.1.2.1.1 %IteratorPrototype%[@@iterator]()
__webpack_require__(10)(IteratorPrototype, __webpack_require__(23)('iterator'), function(){ return this; });

module.exports = function(Constructor, NAME, next){
  Constructor.prototype = create(IteratorPrototype, {next: descriptor(1, next)});
  setToStringTag(Constructor, NAME + ' Iterator');
};

/***/ }),
/* 98 */
/***/ (function(module, exports, __webpack_require__) {

var dP       = __webpack_require__(6)
  , anObject = __webpack_require__(11)
  , getKeys  = __webpack_require__(33);

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
/* 99 */
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__(36)
  , defined   = __webpack_require__(35);
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
/* 100 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var addToUnscopables = __webpack_require__(96)
  , step             = __webpack_require__(94)
  , Iterators        = __webpack_require__(51)
  , toIObject        = __webpack_require__(22);

// 22.1.3.4 Array.prototype.entries()
// 22.1.3.13 Array.prototype.keys()
// 22.1.3.29 Array.prototype.values()
// 22.1.3.30 Array.prototype[@@iterator]()
module.exports = __webpack_require__(67)(Array, 'Array', function(iterated, kind){
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
/* 101 */,
/* 102 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _iterator = __webpack_require__(114);

var _iterator2 = _interopRequireDefault(_iterator);

var _symbol = __webpack_require__(113);

var _symbol2 = _interopRequireDefault(_symbol);

var _typeof = typeof _symbol2.default === "function" && typeof _iterator2.default === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof _symbol2.default === "function" && obj.constructor === _symbol2.default && obj !== _symbol2.default.prototype ? "symbol" : typeof obj; };

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = typeof _symbol2.default === "function" && _typeof(_iterator2.default) === "symbol" ? function (obj) {
  return typeof obj === "undefined" ? "undefined" : _typeof(obj);
} : function (obj) {
  return obj && typeof _symbol2.default === "function" && obj.constructor === _symbol2.default && obj !== _symbol2.default.prototype ? "symbol" : typeof obj === "undefined" ? "undefined" : _typeof(obj);
};

/***/ }),
/* 103 */
/***/ (function(module, exports, __webpack_require__) {

var META     = __webpack_require__(40)('meta')
  , isObject = __webpack_require__(4)
  , has      = __webpack_require__(25)
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
/* 104 */
/***/ (function(module, exports, __webpack_require__) {

var pIE            = __webpack_require__(54)
  , createDesc     = __webpack_require__(12)
  , toIObject      = __webpack_require__(22)
  , toPrimitive    = __webpack_require__(14)
  , has            = __webpack_require__(25)
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
/* 105 */
/***/ (function(module, exports) {



/***/ }),
/* 106 */,
/* 107 */
/***/ (function(module, exports, __webpack_require__) {

// 7.2.2 IsArray(argument)
var cof = __webpack_require__(48);
module.exports = Array.isArray || function isArray(arg){
  return cof(arg) == 'Array';
};

/***/ }),
/* 108 */,
/* 109 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _localization = __webpack_require__(111);

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
/* 110 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _localization = __webpack_require__(111);

var _localization2 = _interopRequireDefault(_localization);

var _numberSymbol = __webpack_require__(109);

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
/* 111 */
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
/* 112 */,
/* 113 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(117), __esModule: true };

/***/ }),
/* 114 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(118), __esModule: true };

/***/ }),
/* 115 */,
/* 116 */,
/* 117 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(124);
__webpack_require__(105);
__webpack_require__(125);
__webpack_require__(126);
module.exports = __webpack_require__(3).Symbol;

/***/ }),
/* 118 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(64);
__webpack_require__(73);
module.exports = __webpack_require__(72).f('iterator');

/***/ }),
/* 119 */
/***/ (function(module, exports, __webpack_require__) {

// all enumerable object keys, includes symbols
var getKeys = __webpack_require__(33)
  , gOPS    = __webpack_require__(59)
  , pIE     = __webpack_require__(54);
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
/* 120 */
/***/ (function(module, exports, __webpack_require__) {

// check on default Array iterator
var Iterators  = __webpack_require__(51)
  , ITERATOR   = __webpack_require__(23)('iterator')
  , ArrayProto = Array.prototype;

module.exports = function(it){
  return it !== undefined && (Iterators.Array === it || ArrayProto[ITERATOR] === it);
};

/***/ }),
/* 121 */
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
/* 122 */
/***/ (function(module, exports, __webpack_require__) {

var getKeys   = __webpack_require__(33)
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
/* 123 */
/***/ (function(module, exports, __webpack_require__) {

// fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
var toIObject = __webpack_require__(22)
  , gOPN      = __webpack_require__(88).f
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
/* 124 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// ECMAScript 6 symbols shim
var global         = __webpack_require__(5)
  , has            = __webpack_require__(25)
  , DESCRIPTORS    = __webpack_require__(2)
  , $export        = __webpack_require__(8)
  , redefine       = __webpack_require__(76)
  , META           = __webpack_require__(103).KEY
  , $fails         = __webpack_require__(7)
  , shared         = __webpack_require__(50)
  , setToStringTag = __webpack_require__(60)
  , uid            = __webpack_require__(40)
  , wks            = __webpack_require__(23)
  , wksExt         = __webpack_require__(72)
  , wksDefine      = __webpack_require__(71)
  , keyOf          = __webpack_require__(122)
  , enumKeys       = __webpack_require__(119)
  , isArray        = __webpack_require__(107)
  , anObject       = __webpack_require__(11)
  , toIObject      = __webpack_require__(22)
  , toPrimitive    = __webpack_require__(14)
  , createDesc     = __webpack_require__(12)
  , _create        = __webpack_require__(66)
  , gOPNExt        = __webpack_require__(123)
  , $GOPD          = __webpack_require__(104)
  , $DP            = __webpack_require__(6)
  , $keys          = __webpack_require__(33)
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
  __webpack_require__(88).f = gOPNExt.f = $getOwnPropertyNames;
  __webpack_require__(54).f  = $propertyIsEnumerable;
  __webpack_require__(59).f = $getOwnPropertySymbols;

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
/* 125 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(71)('asyncIterator');

/***/ }),
/* 126 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(71)('observable');

/***/ }),
/* 127 */,
/* 128 */,
/* 129 */,
/* 130 */,
/* 131 */,
/* 132 */,
/* 133 */,
/* 134 */,
/* 135 */,
/* 136 */
/***/ (function(module, exports, __webpack_require__) {

var ITERATOR     = __webpack_require__(23)('iterator')
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
/* 137 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(138), __esModule: true };

/***/ }),
/* 138 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(64);
__webpack_require__(140);
module.exports = __webpack_require__(3).Array.from;

/***/ }),
/* 139 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $defineProperty = __webpack_require__(6)
  , createDesc      = __webpack_require__(12);

module.exports = function(object, index, value){
  if(index in object)$defineProperty.f(object, index, createDesc(0, value));
  else object[index] = value;
};

/***/ }),
/* 140 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var ctx            = __webpack_require__(13)
  , $export        = __webpack_require__(8)
  , toObject       = __webpack_require__(44)
  , call           = __webpack_require__(121)
  , isArrayIter    = __webpack_require__(120)
  , toLength       = __webpack_require__(53)
  , createProperty = __webpack_require__(139)
  , getIterFn      = __webpack_require__(95);

$export($export.S + $export.F * !__webpack_require__(136)(function(iter){ Array.from(iter); }), 'Array', {
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
/* 141 */,
/* 142 */
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

var _localization = __webpack_require__(111);

var _localization2 = _interopRequireDefault(_localization);

var _number = __webpack_require__(110);

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
/* 143 */,
/* 144 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _from = __webpack_require__(137);

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
/* 145 */,
/* 146 */,
/* 147 */,
/* 148 */,
/* 149 */,
/* 150 */,
/* 151 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(154), __esModule: true };

/***/ }),
/* 152 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(155), __esModule: true };

/***/ }),
/* 153 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _isIterable2 = __webpack_require__(152);

var _isIterable3 = _interopRequireDefault(_isIterable2);

var _getIterator2 = __webpack_require__(151);

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
/* 154 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(73);
__webpack_require__(64);
module.exports = __webpack_require__(156);

/***/ }),
/* 155 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(73);
__webpack_require__(64);
module.exports = __webpack_require__(157);

/***/ }),
/* 156 */
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__(11)
  , get      = __webpack_require__(95);
module.exports = __webpack_require__(3).getIterator = function(it){
  var iterFn = get(it);
  if(typeof iterFn != 'function')throw TypeError(it + ' is not iterable!');
  return anObject(iterFn.call(it));
};

/***/ }),
/* 157 */
/***/ (function(module, exports, __webpack_require__) {

var classof   = __webpack_require__(86)
  , ITERATOR  = __webpack_require__(23)('iterator')
  , Iterators = __webpack_require__(51);
module.exports = __webpack_require__(3).isIterable = function(it){
  var O = Object(it);
  return O[ITERATOR] !== undefined
    || '@@iterator' in O
    || Iterators.hasOwnProperty(classof(O));
};

/***/ }),
/* 158 */,
/* 159 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.NumberSymbol = exports.NumberFormatter = exports.NumberSpecification = exports.PriceSpecification = undefined;

var _numberFormatter = __webpack_require__(161);

var _numberFormatter2 = _interopRequireDefault(_numberFormatter);

var _numberSymbol = __webpack_require__(109);

var _numberSymbol2 = _interopRequireDefault(_numberSymbol);

var _price = __webpack_require__(142);

var _price2 = _interopRequireDefault(_price);

var _number = __webpack_require__(110);

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
/* 160 */,
/* 161 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _toConsumableArray2 = __webpack_require__(144);

var _toConsumableArray3 = _interopRequireDefault(_toConsumableArray2);

var _keys = __webpack_require__(70);

var _keys2 = _interopRequireDefault(_keys);

var _slicedToArray2 = __webpack_require__(153);

var _slicedToArray3 = _interopRequireDefault(_slicedToArray2);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _numberSymbol = __webpack_require__(109);

var _numberSymbol2 = _interopRequireDefault(_numberSymbol);

var _price = __webpack_require__(142);

var _price2 = _interopRequireDefault(_price);

var _number = __webpack_require__(110);

var _number2 = _interopRequireDefault(_number);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var escapeRE = __webpack_require__(181); /**
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

module.exports = {"base_url":"","routes":{"admin_product_form":{"tokens":[["variable","/","\\d+","id"],["text","/sell/catalog/products"]],"defaults":[],"requirements":{"id":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_cart_rules_search":{"tokens":[["text","/sell/catalog/cart-rules/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_view":{"tokens":[["text","/view"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_customers_search":{"tokens":[["text","/sell/customers/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_carts":{"tokens":[["text","/carts"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_orders":{"tokens":[["text","/orders"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_addresses_create":{"tokens":[["text","/sell/addresses/new"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_addresses_edit":{"tokens":[["text","/edit"],["variable","/","\\d+","addressId"],["text","/sell/addresses"]],"defaults":[],"requirements":{"addressId":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_order_addresses_edit":{"tokens":[["text","/edit"],["variable","/","delivery|invoice","addressType"],["variable","/","\\d+","orderId"],["text","/sell/addresses/order"]],"defaults":[],"requirements":{"orderId":"\\d+","addressType":"delivery|invoice"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_cart_addresses_edit":{"tokens":[["text","/edit"],["variable","/","delivery|invoice","addressType"],["variable","/","\\d+","cartId"],["text","/sell/addresses/cart"]],"defaults":[],"requirements":{"cartId":"\\d+","addressType":"delivery|invoice"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_carts_view":{"tokens":[["text","/view"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_carts_info":{"tokens":[["text","/info"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_carts_create":{"tokens":[["text","/sell/orders/carts/new"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_addresses":{"tokens":[["text","/addresses"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_carrier":{"tokens":[["text","/carrier"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_currency":{"tokens":[["text","/currency"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_language":{"tokens":[["text","/language"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_set_delivery_settings":{"tokens":[["text","/rules/delivery-settings"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_add_cart_rule":{"tokens":[["text","/cart-rules"],["variable","/","[^/]++","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_delete_cart_rule":{"tokens":[["text","/delete"],["variable","/","[^/]++","cartRuleId"],["text","/cart-rules"],["variable","/","[^/]++","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_add_product":{"tokens":[["text","/products"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_product_price":{"tokens":[["text","/price"],["variable","/","\\d+","productId"],["text","/products"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+","productId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_product_quantity":{"tokens":[["text","/quantity"],["variable","/","\\d+","productId"],["text","/products"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+","productId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_delete_product":{"tokens":[["text","/delete-product"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_place":{"tokens":[["text","/sell/orders/place"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_view":{"tokens":[["text","/view"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_orders_duplicate_cart":{"tokens":[["text","/duplicate-cart"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_update_product":{"tokens":[["variable","/","\\d+","orderDetailId"],["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+","orderDetailId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_partial_refund":{"tokens":[["text","/partial-refund"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_standard_refund":{"tokens":[["text","/standard-refund"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_return_product":{"tokens":[["text","/return-product"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_send_process_order_email":{"tokens":[["text","/sell/orders/process-order-email"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_add_product":{"tokens":[["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_delete_product":{"tokens":[["text","/delete"],["variable","/","\\d+","orderDetailId"],["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+","orderDetailId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_get_discounts":{"tokens":[["text","/discounts"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_prices":{"tokens":[["text","/prices"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_payments":{"tokens":[["text","/payments"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_products":{"tokens":[["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_invoices":{"tokens":[["text","/invoices"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_documents":{"tokens":[["text","/documents"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_shipping":{"tokens":[["text","/shipping"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_cancellation":{"tokens":[["text","/cancellation"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_configure_product_pagination":{"tokens":[["text","/sell/orders/configure-product-pagination"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_product_prices":{"tokens":[["text","/products/prices"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_products_search":{"tokens":[["text","/sell/orders/products/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]}},"prefix":"","host":"localhost","port":"","scheme":"http","locale":[]}

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

var _typeof2 = __webpack_require__(102);

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

var _typeof2 = __webpack_require__(102);

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

__webpack_require__(176);
var $Object = __webpack_require__(3).Object;
module.exports = function create(P, D){
  return $Object.create(P, D);
};

/***/ }),
/* 172 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(177);
module.exports = __webpack_require__(3).Object.getPrototypeOf;

/***/ }),
/* 173 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(178);
module.exports = __webpack_require__(3).Object.setPrototypeOf;

/***/ }),
/* 174 */,
/* 175 */
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
        set = __webpack_require__(13)(Function.call, __webpack_require__(104).f(Object.prototype, '__proto__').set, 2);
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
/* 176 */
/***/ (function(module, exports, __webpack_require__) {

var $export = __webpack_require__(8)
// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
$export($export.S, 'Object', {create: __webpack_require__(66)});

/***/ }),
/* 177 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.9 Object.getPrototypeOf(O)
var toObject        = __webpack_require__(44)
  , $getPrototypeOf = __webpack_require__(82);

__webpack_require__(78)('getPrototypeOf', function(){
  return function getPrototypeOf(it){
    return $getPrototypeOf(toObject(it));
  };
});

/***/ }),
/* 178 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.3.19 Object.setPrototypeOf(O, proto)
var $export = __webpack_require__(8);
$export($export.S, 'Object', {setPrototypeOf: __webpack_require__(175).set});

/***/ }),
/* 179 */,
/* 180 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
var _extends=Object.assign||function(a){for(var b,c=1;c<arguments.length;c++)for(var d in b=arguments[c],b)Object.prototype.hasOwnProperty.call(b,d)&&(a[d]=b[d]);return a},_typeof='function'==typeof Symbol&&'symbol'==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&'function'==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?'symbol':typeof a};function _classCallCheck(a,b){if(!(a instanceof b))throw new TypeError('Cannot call a class as a function')}var Routing=function a(){var b=this;_classCallCheck(this,a),this.setRoutes=function(a){b.routesRouting=a||[]},this.getRoutes=function(){return b.routesRouting},this.setBaseUrl=function(a){b.contextRouting.base_url=a},this.getBaseUrl=function(){return b.contextRouting.base_url},this.setPrefix=function(a){b.contextRouting.prefix=a},this.setScheme=function(a){b.contextRouting.scheme=a},this.getScheme=function(){return b.contextRouting.scheme},this.setHost=function(a){b.contextRouting.host=a},this.getHost=function(){return b.contextRouting.host},this.buildQueryParams=function(a,c,d){var e=new RegExp(/\[]$/);c instanceof Array?c.forEach(function(c,f){e.test(a)?d(a,c):b.buildQueryParams(a+'['+('object'===('undefined'==typeof c?'undefined':_typeof(c))?f:'')+']',c,d)}):'object'===('undefined'==typeof c?'undefined':_typeof(c))?Object.keys(c).forEach(function(e){return b.buildQueryParams(a+'['+e+']',c[e],d)}):d(a,c)},this.getRoute=function(a){var c=b.contextRouting.prefix+a;if(!!b.routesRouting[c])return b.routesRouting[c];else if(!b.routesRouting[a])throw new Error('The route "'+a+'" does not exist.');return b.routesRouting[a]},this.generate=function(a,c,d){var e=b.getRoute(a),f=c||{},g=_extends({},f),h='_scheme',i='',j=!0,k='';if((e.tokens||[]).forEach(function(b){if('text'===b[0])return i=b[1]+i,void(j=!1);if('variable'===b[0]){var c=(e.defaults||{})[b[3]];if(!1==j||!c||(f||{})[b[3]]&&f[b[3]]!==e.defaults[b[3]]){var d;if((f||{})[b[3]])d=f[b[3]],delete g[b[3]];else if(c)d=e.defaults[b[3]];else{if(j)return;throw new Error('The route "'+a+'" requires the parameter "'+b[3]+'".')}var h=!0===d||!1===d||''===d;if(!h||!j){var k=encodeURIComponent(d).replace(/%2F/g,'/');'null'===k&&null===d&&(k=''),i=b[1]+k+i}j=!1}else c&&delete g[b[3]];return}throw new Error('The token type "'+b[0]+'" is not supported.')}),''==i&&(i='/'),(e.hosttokens||[]).forEach(function(a){var b;return'text'===a[0]?void(k=a[1]+k):void('variable'===a[0]&&((f||{})[a[3]]?(b=f[a[3]],delete g[a[3]]):e.defaults[a[3]]&&(b=e.defaults[a[3]]),k=a[1]+b+k))}),i=b.contextRouting.base_url+i,e.requirements[h]&&b.getScheme()!==e.requirements[h]?i=e.requirements[h]+'://'+(k||b.getHost())+i:k&&b.getHost()!==k?i=b.getScheme()+'://'+k+i:!0===d&&(i=b.getScheme()+'://'+b.getHost()+i),0<Object.keys(g).length){var l=[],m=function(a,b){var c=b;c='function'==typeof c?c():c,c=null===c?'':c,l.push(encodeURIComponent(a)+'='+encodeURIComponent(c))};Object.keys(g).forEach(function(a){return b.buildQueryParams(a,g[a],m)}),i=i+'?'+l.join('&').replace(/%20/g,'+')}return i},this.setData=function(a){b.setBaseUrl(a.base_url),b.setRoutes(a.routes),'prefix'in a&&b.setPrefix(a.prefix),b.setHost(a.host),b.setScheme(a.scheme)},this.contextRouting={base_url:'',prefix:'',host:'',scheme:''}};module.exports=new Routing;

/***/ }),
/* 181 */
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
/* 192 */,
/* 193 */
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
/* 194 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(210), __esModule: true };

/***/ }),
/* 195 */,
/* 196 */,
/* 197 */,
/* 198 */,
/* 199 */,
/* 200 */,
/* 201 */,
/* 202 */
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
      $.getJSON(this.router.generate('admin_orders_get_prices', { orderId: orderId })).then(function (response) {
        $(_OrderViewPageMap2.default.orderTotal).text(response.orderTotalFormatted);
        $(_OrderViewPageMap2.default.orderDiscountsTotal).text('-' + response.discountsAmountFormatted);
        $(_OrderViewPageMap2.default.orderDiscountsTotalContainer).toggleClass('d-none', !response.discountsAmountDisplayed);
        $(_OrderViewPageMap2.default.orderProductsTotal).text(response.productsTotalFormatted);
        $(_OrderViewPageMap2.default.orderShippingTotal).text(response.shippingTotalFormatted);
        $(_OrderViewPageMap2.default.orderShippingTotalContainer).toggleClass('d-none', !response.shippingTotalDisplayed);
        $(_OrderViewPageMap2.default.orderTaxesTotal).text(response.taxesTotalFormatted);
      });
    }
  }, {
    key: 'refreshProductPrices',
    value: function refreshProductPrices(orderId) {
      $.getJSON(this.router.generate('admin_orders_product_prices', { orderId: orderId })).then(function (productPricesList) {
        productPricesList.forEach(function (productPrices) {
          var orderProductTrId = _OrderViewPageMap2.default.productsTableRow(productPrices.orderDetailId);
          var $quantity = $(productPrices.quantity);
          if (productPrices.quantity > 1) {
            $quantity = $quantity.wrap('<span class="badge badge-secondary rounded-circle"></span>');
          }

          $(orderProductTrId + ' ' + _OrderViewPageMap2.default.productEditUnitPrice).text(productPrices.unitPrice);
          $(orderProductTrId + ' ' + _OrderViewPageMap2.default.productEditQuantity).html($quantity.html());
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
/* 203 */,
/* 204 */,
/* 205 */,
/* 206 */,
/* 207 */,
/* 208 */,
/* 209 */,
/* 210 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(213);
module.exports = __webpack_require__(3).Object.values;

/***/ }),
/* 211 */
/***/ (function(module, exports, __webpack_require__) {

var getKeys   = __webpack_require__(33)
  , toIObject = __webpack_require__(22)
  , isEnum    = __webpack_require__(54).f;
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
/* 212 */,
/* 213 */
/***/ (function(module, exports, __webpack_require__) {

// https://github.com/tc39/proposal-object-values-entries
var $export = __webpack_require__(8)
  , $values = __webpack_require__(211)(false);

$export($export.S, 'Object', {
  values: function values(it){
    return $values(it);
  }
});

/***/ }),
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
/* 229 */,
/* 230 */,
/* 231 */,
/* 232 */,
/* 233 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _isNan = __webpack_require__(235);

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
/* 234 */
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

var _orderProductEdit = __webpack_require__(531);

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
    value: function editProductFromList(orderDetailId, quantity, priceTaxIncl, priceTaxExcl, taxRate, location, availableQuantity, availableOutOfStock, orderInvoiceId, isOrderTaxIncluded) {
      var $orderEdit = new _orderProductEdit2.default(orderDetailId);
      $orderEdit.displayProduct({
        price_tax_excl: priceTaxExcl,
        price_tax_incl: priceTaxIncl,
        tax_rate: taxRate,
        quantity: quantity,
        location: location,
        availableQuantity: availableQuantity,
        availableOutOfStock: availableOutOfStock,
        orderInvoiceId: orderInvoiceId,
        isOrderTaxIncluded: isOrderTaxIncluded
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
      $(_OrderViewPageMap2.default.productsTablePagination).data('numPerPage', numPerPage);
      this.updatePaginationControls();
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
  }, {
    key: 'updatePaginationControls',
    value: function updatePaginationControls() {
      var $tablePagination = $(_OrderViewPageMap2.default.productsTablePagination);
      var numPerPage = $tablePagination.data('numPerPage');
      var $rows = $(_OrderViewPageMap2.default.productsTable).find('tr[id^="orderProduct_"]');
      var numPages = Math.ceil($rows.length / numPerPage);

      // Update table data fields
      $tablePagination.data('numPages', numPages);

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

      this.togglePaginationControls();
    }
  }]);
  return OrderProductRenderer;
}();

exports.default = OrderProductRenderer;

/***/ }),
/* 235 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(599), __esModule: true };

/***/ }),
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
/* 378 */,
/* 379 */,
/* 380 */,
/* 381 */,
/* 382 */,
/* 383 */
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
/* 414 */,
/* 415 */,
/* 416 */,
/* 417 */,
/* 418 */,
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
        $orderMessage.trigger('input');
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
/* 420 */
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
      $(_OrderViewPageMap2.default.mainDiv).on('click', _OrderViewPageMap2.default.showOrderShippingUpdateModalBtn, function (event) {
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
/* 421 */,
/* 422 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _values = __webpack_require__(194);

var _values2 = _interopRequireDefault(_values);

var _keys = __webpack_require__(70);

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

      // Search only if the search phrase length is greater than 2 characters
      if (input.value.length < 2) {
        return;
      }

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
/* 423 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _keys = __webpack_require__(70);

var _keys2 = _interopRequireDefault(_keys);

var _values = __webpack_require__(194);

var _values2 = _interopRequireDefault(_values);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

var _eventEmitter = __webpack_require__(38);

var _orderViewEventMap = __webpack_require__(193);

var _orderViewEventMap2 = _interopRequireDefault(_orderViewEventMap);

var _orderPrices = __webpack_require__(233);

var _orderPrices2 = _interopRequireDefault(_orderPrices);

var _orderProductRenderer = __webpack_require__(234);

var _orderProductRenderer2 = _interopRequireDefault(_orderProductRenderer);

var _modal = __webpack_require__(47);

var _modal2 = _interopRequireDefault(_modal);

var _orderPricesRefresher = __webpack_require__(202);

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
    this.isOrderTaxIncluded = $(_OrderViewPageMap2.default.productAddRow).data('isOrderTaxIncluded');
    this.taxExcluded = null;
    this.taxIncluded = null;
  }

  (0, _createClass3.default)(OrderProductAdd, [{
    key: 'setupListener',
    value: function setupListener() {
      var _this = this;

      this.combinationsSelect.on('change', function (event) {
        var taxExcluded = window.ps_round($(event.currentTarget).find(':selected').data('priceTaxExcluded'), _this.currencyPrecision);
        _this.priceTaxExcludedInput.val(taxExcluded);
        _this.taxExcluded = parseFloat(taxExcluded);

        var taxIncluded = window.ps_round($(event.currentTarget).find(':selected').data('priceTaxIncluded'), _this.currencyPrecision);
        _this.priceTaxIncludedInput.val(taxIncluded);
        _this.taxIncluded = parseFloat(taxIncluded);

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

          _this.taxIncluded = parseFloat(_this.priceTaxIncludedInput.val());
          _this.totalPriceText.html(_this.priceTaxCalculator.calculateTotalPrice(newQuantity, _this.isOrderTaxIncluded ? _this.taxIncluded : _this.taxExcluded, _this.currencyPrecision));
        }
      });

      this.productIdInput.on('change', function () {
        _this.productAddActionBtn.removeAttr('disabled');
        _this.invoiceSelect.removeAttr('disabled');
      });

      this.priceTaxIncludedInput.on('change keyup', function (event) {
        _this.taxIncluded = parseFloat(event.target.value);
        _this.taxExcluded = _this.priceTaxCalculator.calculateTaxExcluded(_this.taxIncluded, _this.taxRateInput.val(), _this.currencyPrecision);
        var quantity = parseInt(_this.quantityInput.val(), 10);

        _this.priceTaxExcludedInput.val(_this.taxExcluded);
        _this.totalPriceText.html(_this.priceTaxCalculator.calculateTotalPrice(quantity, _this.isOrderTaxIncluded ? _this.taxIncluded : _this.taxExcluded, _this.currencyPrecision));
      });

      this.priceTaxExcludedInput.on('change keyup', function (event) {
        _this.taxExcluded = parseFloat(event.target.value);
        _this.taxIncluded = _this.priceTaxCalculator.calculateTaxIncluded(_this.taxExcluded, _this.taxRateInput.val(), _this.currencyPrecision);
        var quantity = parseInt(_this.quantityInput.val(), 10);

        _this.priceTaxIncludedInput.val(_this.taxIncluded);
        _this.totalPriceText.html(_this.priceTaxCalculator.calculateTotalPrice(quantity, _this.isOrderTaxIncluded ? _this.taxIncluded : _this.taxExcluded, _this.currencyPrecision));
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

      var taxExcluded = window.ps_round(product.priceTaxExcl, this.currencyPrecision);
      this.priceTaxExcludedInput.val(taxExcluded);
      this.taxExcluded = parseFloat(taxExcluded);

      var taxIncluded = window.ps_round(product.priceTaxIncl, this.currencyPrecision);
      this.priceTaxIncludedInput.val(taxIncluded);
      this.taxIncluded = parseFloat(taxIncluded);

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
          orderId: orderId
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
/* 424 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _orderProductManager = __webpack_require__(532);

var _orderProductManager2 = _interopRequireDefault(_orderProductManager);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

var _orderViewEventMap = __webpack_require__(193);

var _orderViewEventMap2 = _interopRequireDefault(_orderViewEventMap);

var _eventEmitter = __webpack_require__(38);

var _orderDiscountsRefresher = __webpack_require__(526);

var _orderDiscountsRefresher2 = _interopRequireDefault(_orderDiscountsRefresher);

var _orderProductRenderer = __webpack_require__(234);

var _orderProductRenderer2 = _interopRequireDefault(_orderProductRenderer);

var _orderPricesRefresher = __webpack_require__(202);

var _orderPricesRefresher2 = _interopRequireDefault(_orderPricesRefresher);

var _orderPaymentsRefresher = __webpack_require__(529);

var _orderPaymentsRefresher2 = _interopRequireDefault(_orderPaymentsRefresher);

var _orderShippingRefresher = __webpack_require__(533);

var _orderShippingRefresher2 = _interopRequireDefault(_orderShippingRefresher);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _orderInvoicesRefresher = __webpack_require__(528);

var _orderInvoicesRefresher2 = _interopRequireDefault(_orderInvoicesRefresher);

var _orderProductCancel = __webpack_require__(530);

var _orderProductCancel2 = _interopRequireDefault(_orderProductCancel);

var _orderDocumentsRefresher = __webpack_require__(527);

var _orderDocumentsRefresher2 = _interopRequireDefault(_orderDocumentsRefresher);

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

var OrderViewPage = function () {
  function OrderViewPage() {
    (0, _classCallCheck3.default)(this, OrderViewPage);

    this.orderDiscountsRefresher = new _orderDiscountsRefresher2.default();
    this.orderProductManager = new _orderProductManager2.default();
    this.orderProductRenderer = new _orderProductRenderer2.default();
    this.orderPricesRefresher = new _orderPricesRefresher2.default();
    this.orderPaymentsRefresher = new _orderPaymentsRefresher2.default();
    this.orderShippingRefresher = new _orderShippingRefresher2.default();
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
        _this.orderPricesRefresher.refresh(event.orderId);
        _this.orderPaymentsRefresher.refresh(event.orderId);
        _this.refreshProductsList(event.orderId);
        _this.orderDiscountsRefresher.refresh(event.orderId);
        _this.orderDocumentsRefresher.refresh(event.orderId);
        _this.orderShippingRefresher.refresh(event.orderId);
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
        _this.orderProductRenderer.resetEditRow(event.orderDetailId);
        _this.orderPricesRefresher.refresh(event.orderId);
        _this.orderPricesRefresher.refreshProductPrices(event.orderId);
        _this.refreshProductsList(event.orderId);
        _this.orderPaymentsRefresher.refresh(event.orderId);
        _this.orderDiscountsRefresher.refresh(event.orderId);
        _this.orderInvoicesRefresher.refresh(event.orderId);
        _this.orderDocumentsRefresher.refresh(event.orderId);
        _this.orderShippingRefresher.refresh(event.orderId);
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
        _this.orderProductRenderer.resetAddRow();
        _this.orderPricesRefresher.refreshProductPrices(event.orderId);
        _this.orderPricesRefresher.refresh(event.orderId);
        _this.refreshProductsList(event.orderId);
        _this.orderPaymentsRefresher.refresh(event.orderId);
        _this.orderDiscountsRefresher.refresh(event.orderId);
        _this.orderInvoicesRefresher.refresh(event.orderId);
        _this.orderDocumentsRefresher.refresh(event.orderId);
        _this.orderShippingRefresher.refresh(event.orderId);
        _this.orderProductRenderer.moveProductPanelToOriginalPosition();
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
        _this3.orderProductRenderer.editProductFromList($btn.data('orderDetailId'), $btn.data('productQuantity'), $btn.data('productPriceTaxIncl'), $btn.data('productPriceTaxExcl'), $btn.data('taxRate'), $btn.data('location'), $btn.data('availableQuantity'), $btn.data('availableOutOfStock'), $btn.data('orderInvoiceId'), $btn.data('isOrderTaxIncluded'));
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
  }, {
    key: 'refreshProductsList',
    value: function refreshProductsList(orderId) {
      var _this9 = this;

      $(_OrderViewPageMap2.default.refreshProductsListLoadingSpinner).show();

      var $tablePagination = $(_OrderViewPageMap2.default.productsTablePagination);
      var numRowsPerPage = $tablePagination.data('numPerPage');
      var initialNumProducts = $(_OrderViewPageMap2.default.productsTableRows).length;
      var currentPage = parseInt($(_OrderViewPageMap2.default.productsTablePaginationActive).html(), 10);

      $.ajax(this.router.generate('admin_orders_get_products', { orderId: orderId })).done(function (response) {
        // Delete previous product lines
        $(_OrderViewPageMap2.default.productsTable).find(_OrderViewPageMap2.default.productsTableRows).remove();
        $(_OrderViewPageMap2.default.productsTableCustomizationRows).remove();

        $(_OrderViewPageMap2.default.productsTable + ' tbody').prepend(response);

        $(_OrderViewPageMap2.default.refreshProductsListLoadingSpinner).hide();

        var newNumProducts = $(_OrderViewPageMap2.default.productsTableRows).length;
        var newPagesNum = Math.ceil(newNumProducts / numRowsPerPage);

        _this9.orderProductRenderer.updateNumProducts(newNumProducts);
        _this9.orderProductRenderer.updatePaginationControls();

        var numPage = 1;
        var message = '';
        // Display alert
        if (initialNumProducts > newNumProducts) {
          // product deleted
          message = initialNumProducts - newNumProducts === 1 ? window.translate_javascripts['The product was successfully removed.'] : window.translate_javascripts['[1] products were successfully removed.'].replace('[1]', initialNumProducts - newNumProducts);

          // Set target page to the page of the deleted item
          numPage = newPagesNum === 1 ? 1 : currentPage;
        } else if (initialNumProducts < newNumProducts) {
          // product added
          message = newNumProducts - initialNumProducts === 1 ? window.translate_javascripts['The product was successfully added.'] : window.translate_javascripts['[1] products were successfully added.'].replace('[1]', newNumProducts - initialNumProducts);

          // Move to first page to see the added product
          numPage = 1;
        }

        if ('' !== message) {
          $.growl.notice({
            title: '',
            message: message
          });
        }

        // Move to page of the modified item
        _eventEmitter.EventEmitter.emit(_orderViewEventMap2.default.productListPaginated, {
          numPage: numPage
        });

        // Bind hover on product rows buttons
        _this9.resetToolTips();
      }).fail(function (errors) {
        $.growl.error({
          title: '',
          message: 'Failed to reload the products list. Please reload the page'
        });
      });
    }
  }]);
  return OrderViewPage;
}();

exports.default = OrderViewPage;

/***/ }),
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
/* 519 */,
/* 520 */,
/* 521 */,
/* 522 */,
/* 523 */,
/* 524 */
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
/* 525 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

var _orderShippingManager = __webpack_require__(420);

var _orderShippingManager2 = _interopRequireDefault(_orderShippingManager);

var _orderViewPage = __webpack_require__(424);

var _orderViewPage2 = _interopRequireDefault(_orderViewPage);

var _orderProductAddAutocomplete = __webpack_require__(422);

var _orderProductAddAutocomplete2 = _interopRequireDefault(_orderProductAddAutocomplete);

var _orderProductAdd = __webpack_require__(423);

var _orderProductAdd2 = _interopRequireDefault(_orderProductAdd);

var _orderViewPageMessagesHandler = __webpack_require__(419);

var _orderViewPageMessagesHandler2 = _interopRequireDefault(_orderViewPageMessagesHandler);

var _textWithLengthCounter = __webpack_require__(383);

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

    $modal.on('shown.bs.modal', function () {
      $(_OrderViewPageMap2.default.addCartRuleSubmit).attr('disabled', true);
    });

    $form.find(_OrderViewPageMap2.default.addCartRuleNameInput).on('keyup', function (event) {
      var cartRuleName = $(event.currentTarget).val();
      $(_OrderViewPageMap2.default.addCartRuleSubmit).attr('disabled', cartRuleName.trim().length === 0);
    });

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

var _invoiceNoteManager = __webpack_require__(524);

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

      $.getJSON(this.router.generate('admin_orders_get_documents', { orderId: orderId })).then(function (response) {
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
/* 528 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _keys = __webpack_require__(70);

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
      $.getJSON(this.router.generate('admin_orders_get_invoices', { orderId: orderId })).then(function (response) {
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
/* 529 */
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

var OrderPaymentsRefresher = function () {
  function OrderPaymentsRefresher() {
    (0, _classCallCheck3.default)(this, OrderPaymentsRefresher);

    this.router = new _router2.default();
  }

  (0, _createClass3.default)(OrderPaymentsRefresher, [{
    key: 'refresh',
    value: function refresh(orderId) {
      $.ajax(this.router.generate('admin_orders_get_payments', { orderId: orderId })).then(function (response) {
        $(_OrderViewPageMap2.default.viewOrderPaymentsAlert).remove();
        $(_OrderViewPageMap2.default.viewOrderPaymentsBlock + ' .card-body').prepend(response);
      }, function (response) {
        if (response.responseJSON && response.responseJSON.message) {
          $.growl.error({ message: response.responseJSON.message });
        }
      });
    }
  }]);
  return OrderPaymentsRefresher;
}();

exports.default = OrderPaymentsRefresher;

/***/ }),
/* 530 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _isNan = __webpack_require__(235);

var _isNan2 = _interopRequireDefault(_isNan);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _router = __webpack_require__(65);

var _router2 = _interopRequireDefault(_router);

var _OrderViewPageMap = __webpack_require__(91);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

var _cldr = __webpack_require__(159);

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
/* 531 */
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

var _eventEmitter = __webpack_require__(38);

var _orderViewEventMap = __webpack_require__(193);

var _orderViewEventMap2 = _interopRequireDefault(_orderViewEventMap);

var _orderPrices = __webpack_require__(233);

var _orderPrices2 = _interopRequireDefault(_orderPrices);

var _modal = __webpack_require__(47);

var _modal2 = _interopRequireDefault(_modal);

var _orderPricesRefresher = __webpack_require__(202);

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
        _this.quantity = newQuantity;
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
        _this.taxExcluded = _this.priceTaxCalculator.calculateTaxExcluded(_this.taxIncluded, _this.taxRate, _this.currencyPrecision);
        _this.priceTaxExcludedInput.val(_this.taxExcluded);
        _this.updateTotal();
      });

      this.priceTaxExcludedInput.on('change keyup', function (event) {
        _this.taxExcluded = parseFloat(event.target.value);
        _this.taxIncluded = _this.priceTaxCalculator.calculateTaxIncluded(_this.taxExcluded, _this.taxRate, _this.currencyPrecision);
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
      var updatedTotal = this.priceTaxCalculator.calculateTotalPrice(this.quantity, this.isOrderTaxIncluded ? this.taxIncluded : this.taxExcluded, this.currencyPrecision);
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
      this.initialTotal = this.priceTaxCalculator.calculateTotalPrice(product.quantity, product.isOrderTaxIncluded ? product.price_tax_incl : product.price_tax_excl, this.currencyPrecision);
      this.isOrderTaxIncluded = product.isOrderTaxIncluded;
      this.quantity = product.quantity;
      this.taxIncluded = product.price_tax_incl;
      this.taxExcluded = product.price_tax_excl;

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

      var dataSelector = Number(orderInvoiceId) === 0 ? this.priceTaxExcludedInput : this.productEditInvoiceSelect;

      var modalEditPrice = new _modal2.default({
        id: 'modal-confirm-new-price',
        confirmTitle: dataSelector.data('modal-edit-price-title'),
        confirmMessage: dataSelector.data('modal-edit-price-body'),
        confirmButtonLabel: dataSelector.data('modal-edit-price-apply'),
        closeButtonLabel: dataSelector.data('modal-edit-price-cancel')
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
      }).then(function () {
        _eventEmitter.EventEmitter.emit(_orderViewEventMap2.default.productUpdated, {
          orderId: orderId,
          orderDetailId: orderDetailId
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
/* 532 */
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

var _eventEmitter = __webpack_require__(38);

var _orderViewEventMap = __webpack_require__(193);

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
/* 533 */
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

var OrderShippingRefresher = function () {
    function OrderShippingRefresher() {
        (0, _classCallCheck3.default)(this, OrderShippingRefresher);

        this.router = new _router2.default();
    }

    (0, _createClass3.default)(OrderShippingRefresher, [{
        key: 'refresh',
        value: function refresh(orderId) {
            $.getJSON(this.router.generate('admin_orders_get_shipping', { orderId: orderId })).then(function (response) {
                $(_OrderViewPageMap2.default.orderShippingTabCount).text(response.total);
                $(_OrderViewPageMap2.default.orderShippingTabBody).html(response.html);
            });
        }
    }]);
    return OrderShippingRefresher;
}();

exports.default = OrderShippingRefresher;

/***/ }),
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
/* 592 */,
/* 593 */,
/* 594 */,
/* 595 */,
/* 596 */,
/* 597 */,
/* 598 */,
/* 599 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(612);
module.exports = __webpack_require__(3).Number.isNaN;

/***/ }),
/* 600 */,
/* 601 */,
/* 602 */,
/* 603 */,
/* 604 */,
/* 605 */,
/* 606 */,
/* 607 */,
/* 608 */,
/* 609 */,
/* 610 */,
/* 611 */,
/* 612 */
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgZjdmNDJiNGJmNTQzNDI4MDYxYmEiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY2xhc3NDYWxsQ2hlY2suanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY3JlYXRlQ2xhc3MuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZGVzY3JpcHRvcnMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY29yZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pcy1vYmplY3QuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZ2xvYmFsLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1kcC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19mYWlscy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19leHBvcnQuanMiLCJ3ZWJwYWNrOi8vLyh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oaWRlLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2FuLW9iamVjdC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19wcm9wZXJ0eS1kZXNjLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2N0eC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1wcmltaXRpdmUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZG9tLWNyZWF0ZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pZTgtZG9tLWRlZmluZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hLWZ1bmN0aW9uLmpzIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5LmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLWlvYmplY3QuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fd2tzLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2hhcy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3Qta2V5cy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kZWZpbmVkLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLWludGVnZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ldmVudC1lbWl0dGVyLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3VpZC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1vYmplY3QuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fc2hhcmVkLWtleS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL21vZGFsLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvZi5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19lbnVtLWJ1Zy1rZXlzLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NoYXJlZC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pdGVyYXRvcnMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faW9iamVjdC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1sZW5ndGguanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LXBpZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3Qta2V5cy1pbnRlcm5hbC5qcyIsIndlYnBhY2s6Ly8vLi9+L2V2ZW50cy9ldmVudHMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYXJyYXktaW5jbHVkZXMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8taW5kZXguanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdvcHMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fc2V0LXRvLXN0cmluZy10YWcuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fbGlicmFyeS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5zdHJpbmcuaXRlcmF0b3IuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9yb3V0ZXIuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWNyZWF0ZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pdGVyLWRlZmluZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3Qva2V5cy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL193a3MtZGVmaW5lLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3drcy1leHQuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy93ZWIuZG9tLml0ZXJhYmxlLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3JlZGVmaW5lLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1zYXAuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdwby5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3QvYXNzaWduLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9hc3NpZ24uanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2tleXMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY2xhc3NvZi5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtYXNzaWduLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1nb3BuLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5hc3NpZ24uanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LmtleXMuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvT3JkZXJWaWV3UGFnZU1hcC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19odG1sLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXItc3RlcC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2NvcmUuZ2V0LWl0ZXJhdG9yLW1ldGhvZC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hZGQtdG8tdW5zY29wYWJsZXMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXRlci1jcmVhdGUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWRwcy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19zdHJpbmctYXQuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYuYXJyYXkuaXRlcmF0b3IuanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvdHlwZW9mLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX21ldGEuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdvcGQuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXMtYXJyYXkuanMiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL2NsZHIvbnVtYmVyLXN5bWJvbC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvY2xkci9zcGVjaWZpY2F0aW9ucy9udW1iZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL2NsZHIvZXhjZXB0aW9uL2xvY2FsaXphdGlvbi5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9zeW1ib2wuanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvc3ltYm9sL2l0ZXJhdG9yLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L2ZuL3N5bWJvbC9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9mbi9zeW1ib2wvaXRlcmF0b3IuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZW51bS1rZXlzLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2lzLWFycmF5LWl0ZXIuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXRlci1jYWxsLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2tleW9mLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1nb3BuLWV4dC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5zeW1ib2wuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczcuc3ltYm9sLmFzeW5jLWl0ZXJhdG9yLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM3LnN5bWJvbC5vYnNlcnZhYmxlLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXItZGV0ZWN0LmpzIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL2FycmF5L2Zyb20uanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vYXJyYXkvZnJvbS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jcmVhdGUtcHJvcGVydHkuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYuYXJyYXkuZnJvbS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvY2xkci9zcGVjaWZpY2F0aW9ucy9wcmljZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy90b0NvbnN1bWFibGVBcnJheS5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9nZXQtaXRlcmF0b3IuanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvaXMtaXRlcmFibGUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvc2xpY2VkVG9BcnJheS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9mbi9nZXQtaXRlcmF0b3IuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vaXMtaXRlcmFibGUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9jb3JlLmdldC1pdGVyYXRvci5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2NvcmUuaXMtaXRlcmFibGUuanMiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL2NsZHIvaW5kZXguanMiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL2NsZHIvbnVtYmVyLWZvcm1hdHRlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9mb3NfanNfcm91dGVzLmpzb24iLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2NyZWF0ZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3QvZ2V0LXByb3RvdHlwZS1vZi5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3Qvc2V0LXByb3RvdHlwZS1vZi5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9pbmhlcml0cy5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9wb3NzaWJsZUNvbnN0cnVjdG9yUmV0dXJuLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9jcmVhdGUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2dldC1wcm90b3R5cGUtb2YuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L3NldC1wcm90b3R5cGUtb2YuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fc2V0LXByb3RvLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5jcmVhdGUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LmdldC1wcm90b3R5cGUtb2YuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LnNldC1wcm90b3R5cGUtb2YuanMiLCJ3ZWJwYWNrOi8vLy4vfi9mb3Mtcm91dGluZy9kaXN0L3JvdXRpbmcuanMiLCJ3ZWJwYWNrOi8vLy4vfi9sb2Rhc2guZXNjYXBlcmVnZXhwL2luZGV4LmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItdmlldy1ldmVudC1tYXAuanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L3ZhbHVlcy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByaWNlcy1yZWZyZXNoZXIuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L3ZhbHVlcy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtdG8tYXJyYXkuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczcub2JqZWN0LnZhbHVlcy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByaWNlcy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtcmVuZGVyZXIuanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvbnVtYmVyL2lzLW5hbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2Zvcm0vdGV4dC13aXRoLWxlbmd0aC1jb3VudGVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL21lc3NhZ2Uvb3JkZXItdmlldy1wYWdlLW1lc3NhZ2VzLWhhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvb3JkZXItc2hpcHBpbmctbWFuYWdlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtYWRkLWF1dG9jb21wbGV0ZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtYWRkLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItdmlldy1wYWdlLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2ludm9pY2Utbm90ZS1tYW5hZ2VyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci1kaXNjb3VudHMtcmVmcmVzaGVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItZG9jdW1lbnRzLXJlZnJlc2hlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLWludm9pY2VzLXJlZnJlc2hlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXBheW1lbnRzLXJlZnJlc2hlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtY2FuY2VsLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcHJvZHVjdC1lZGl0LmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcHJvZHVjdC1tYW5hZ2VyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItc2hpcHBpbmctcmVmcmVzaGVyLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L2ZuL251bWJlci9pcy1uYW4uanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYubnVtYmVyLmlzLW5hbi5qcyJdLCJuYW1lcyI6WyJFdmVudEVtaXR0ZXIiLCJFdmVudEVtaXR0ZXJDbGFzcyIsIkNvbmZpcm1Nb2RhbCIsIiQiLCJ3aW5kb3ciLCJwYXJhbXMiLCJjb25maXJtQ2FsbGJhY2siLCJpZCIsImNsb3NhYmxlIiwibW9kYWwiLCJNb2RhbCIsIiRtb2RhbCIsImNvbnRhaW5lciIsInNob3ciLCJjb25maXJtQnV0dG9uIiwiYWRkRXZlbnRMaXN0ZW5lciIsImJhY2tkcm9wIiwia2V5Ym9hcmQiLCJ1bmRlZmluZWQiLCJvbiIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvciIsInJlbW92ZSIsImJvZHkiLCJhcHBlbmRDaGlsZCIsImNvbmZpcm1UaXRsZSIsImNvbmZpcm1NZXNzYWdlIiwiY2xvc2VCdXR0b25MYWJlbCIsImNvbmZpcm1CdXR0b25MYWJlbCIsImNvbmZpcm1CdXR0b25DbGFzcyIsImNyZWF0ZUVsZW1lbnQiLCJjbGFzc0xpc3QiLCJhZGQiLCJkaWFsb2ciLCJjb250ZW50IiwiaGVhZGVyIiwidGl0bGUiLCJpbm5lckhUTUwiLCJjbG9zZUljb24iLCJzZXRBdHRyaWJ1dGUiLCJkYXRhc2V0IiwiZGlzbWlzcyIsIm1lc3NhZ2UiLCJmb290ZXIiLCJjbG9zZUJ1dHRvbiIsImFwcGVuZCIsIlJvdXRlciIsIlJvdXRpbmciLCJzZXREYXRhIiwicm91dGVzIiwic2V0QmFzZVVybCIsImZpbmQiLCJkYXRhIiwicm91dGUiLCJ0b2tlbml6ZWRQYXJhbXMiLCJfdG9rZW4iLCJnZW5lcmF0ZSIsIm1haW5EaXYiLCJvcmRlclBheW1lbnREZXRhaWxzQnRuIiwib3JkZXJQYXltZW50Rm9ybUFtb3VudElucHV0Iiwib3JkZXJQYXltZW50SW52b2ljZVNlbGVjdCIsInZpZXdPcmRlclBheW1lbnRzQmxvY2siLCJ2aWV3T3JkZXJQYXltZW50c0FsZXJ0IiwicHJpdmF0ZU5vdGVUb2dnbGVCdG4iLCJwcml2YXRlTm90ZUJsb2NrIiwicHJpdmF0ZU5vdGVJbnB1dCIsInByaXZhdGVOb3RlU3VibWl0QnRuIiwiYWRkQ2FydFJ1bGVNb2RhbCIsImFkZENhcnRSdWxlSW52b2ljZUlkU2VsZWN0IiwiYWRkQ2FydFJ1bGVOYW1lSW5wdXQiLCJhZGRDYXJ0UnVsZVR5cGVTZWxlY3QiLCJhZGRDYXJ0UnVsZVZhbHVlSW5wdXQiLCJhZGRDYXJ0UnVsZVZhbHVlVW5pdCIsImFkZENhcnRSdWxlU3VibWl0IiwiY2FydFJ1bGVIZWxwVGV4dCIsInVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uQnRuIiwidXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25JbnB1dCIsInVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uSW5wdXRXcmFwcGVyIiwidXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25Gb3JtIiwic2hvd09yZGVyU2hpcHBpbmdVcGRhdGVNb2RhbEJ0biIsInVwZGF0ZU9yZGVyU2hpcHBpbmdUcmFja2luZ051bWJlcklucHV0IiwidXBkYXRlT3JkZXJTaGlwcGluZ0N1cnJlbnRPcmRlckNhcnJpZXJJZElucHV0IiwidXBkYXRlQ3VzdG9tZXJBZGRyZXNzTW9kYWwiLCJvcGVuT3JkZXJBZGRyZXNzVXBkYXRlTW9kYWxCdG4iLCJ1cGRhdGVPcmRlckFkZHJlc3NUeXBlSW5wdXQiLCJkZWxpdmVyeUFkZHJlc3NFZGl0QnRuIiwiaW52b2ljZUFkZHJlc3NFZGl0QnRuIiwib3JkZXJNZXNzYWdlTmFtZVNlbGVjdCIsIm9yZGVyTWVzc2FnZXNDb250YWluZXIiLCJvcmRlck1lc3NhZ2UiLCJvcmRlck1lc3NhZ2VDaGFuZ2VXYXJuaW5nIiwib3JkZXJEb2N1bWVudHNUYWJDb3VudCIsIm9yZGVyRG9jdW1lbnRzVGFiQm9keSIsIm9yZGVyU2hpcHBpbmdUYWJDb3VudCIsIm9yZGVyU2hpcHBpbmdUYWJCb2R5IiwiYWxsTWVzc2FnZXNNb2RhbCIsImFsbE1lc3NhZ2VzTGlzdCIsIm9wZW5BbGxNZXNzYWdlc0J0biIsInByb2R1Y3RPcmlnaW5hbFBvc2l0aW9uIiwicHJvZHVjdE1vZGlmaWNhdGlvblBvc2l0aW9uIiwicHJvZHVjdHNQYW5lbCIsInByb2R1Y3RzQ291bnQiLCJwcm9kdWN0RGVsZXRlQnRuIiwicHJvZHVjdHNUYWJsZSIsInByb2R1Y3RzUGFnaW5hdGlvbiIsInByb2R1Y3RzTmF2UGFnaW5hdGlvbiIsInByb2R1Y3RzVGFibGVQYWdpbmF0aW9uIiwicHJvZHVjdHNUYWJsZVBhZ2luYXRpb25OZXh0IiwicHJvZHVjdHNUYWJsZVBhZ2luYXRpb25QcmV2IiwicHJvZHVjdHNUYWJsZVBhZ2luYXRpb25MaW5rIiwicHJvZHVjdHNUYWJsZVBhZ2luYXRpb25BY3RpdmUiLCJwcm9kdWN0c1RhYmxlUGFnaW5hdGlvblRlbXBsYXRlIiwicHJvZHVjdHNUYWJsZVBhZ2luYXRpb25OdW1iZXJTZWxlY3RvciIsInByb2R1Y3RzVGFibGVSb3ciLCJwcm9kdWN0SWQiLCJwcm9kdWN0c1RhYmxlUm93RWRpdGVkIiwicHJvZHVjdHNUYWJsZVJvd3MiLCJwcm9kdWN0c0NlbGxMb2NhdGlvbiIsInByb2R1Y3RzQ2VsbFJlZnVuZGVkIiwicHJvZHVjdHNDZWxsTG9jYXRpb25EaXNwbGF5ZWQiLCJwcm9kdWN0c0NlbGxSZWZ1bmRlZERpc3BsYXllZCIsInByb2R1Y3RzVGFibGVDdXN0b21pemF0aW9uUm93cyIsInByb2R1Y3RFZGl0QnV0dG9ucyIsInByb2R1Y3RFZGl0QnRuIiwicHJvZHVjdEFkZEJ0biIsInByb2R1Y3RBY3Rpb25CdG4iLCJwcm9kdWN0QWRkQWN0aW9uQnRuIiwicHJvZHVjdENhbmNlbEFkZEJ0biIsInByb2R1Y3RBZGRSb3ciLCJwcm9kdWN0U2VhcmNoSW5wdXQiLCJwcm9kdWN0U2VhcmNoSW5wdXRBdXRvY29tcGxldGUiLCJwcm9kdWN0U2VhcmNoSW5wdXRBdXRvY29tcGxldGVNZW51IiwicHJvZHVjdEFkZElkSW5wdXQiLCJwcm9kdWN0QWRkVGF4UmF0ZUlucHV0IiwicHJvZHVjdEFkZENvbWJpbmF0aW9uc0Jsb2NrIiwicHJvZHVjdEFkZENvbWJpbmF0aW9uc1NlbGVjdCIsInByb2R1Y3RBZGRQcmljZVRheEV4Y2xJbnB1dCIsInByb2R1Y3RBZGRQcmljZVRheEluY2xJbnB1dCIsInByb2R1Y3RBZGRRdWFudGl0eUlucHV0IiwicHJvZHVjdEFkZEF2YWlsYWJsZVRleHQiLCJwcm9kdWN0QWRkTG9jYXRpb25UZXh0IiwicHJvZHVjdEFkZFRvdGFsUHJpY2VUZXh0IiwicHJvZHVjdEFkZEludm9pY2VTZWxlY3QiLCJwcm9kdWN0QWRkRnJlZVNoaXBwaW5nU2VsZWN0IiwicHJvZHVjdEFkZE5ld0ludm9pY2VJbmZvIiwicHJvZHVjdEVkaXRTYXZlQnRuIiwicHJvZHVjdEVkaXRDYW5jZWxCdG4iLCJwcm9kdWN0RWRpdFJvd1RlbXBsYXRlIiwicHJvZHVjdEVkaXRSb3ciLCJwcm9kdWN0RWRpdEltYWdlIiwicHJvZHVjdEVkaXROYW1lIiwicHJvZHVjdEVkaXRVbml0UHJpY2UiLCJwcm9kdWN0RWRpdFF1YW50aXR5IiwicHJvZHVjdEVkaXRBdmFpbGFibGVRdWFudGl0eSIsInByb2R1Y3RFZGl0VG90YWxQcmljZSIsInByb2R1Y3RFZGl0UHJpY2VUYXhFeGNsSW5wdXQiLCJwcm9kdWN0RWRpdFByaWNlVGF4SW5jbElucHV0IiwicHJvZHVjdEVkaXRJbnZvaWNlU2VsZWN0IiwicHJvZHVjdEVkaXRRdWFudGl0eUlucHV0IiwicHJvZHVjdEVkaXRMb2NhdGlvblRleHQiLCJwcm9kdWN0RWRpdEF2YWlsYWJsZVRleHQiLCJwcm9kdWN0RWRpdFRvdGFsUHJpY2VUZXh0IiwicHJvZHVjdERpc2NvdW50TGlzdCIsImxpc3QiLCJwcm9kdWN0UGFja01vZGFsIiwidGFibGUiLCJyb3dzIiwidGVtcGxhdGUiLCJwcm9kdWN0IiwiaW1nIiwibGluayIsIm5hbWUiLCJyZWYiLCJzdXBwbGllclJlZiIsInF1YW50aXR5IiwiYXZhaWxhYmxlUXVhbnRpdHkiLCJvcmRlclByb2R1Y3RzVG90YWwiLCJvcmRlckRpc2NvdW50c1RvdGFsQ29udGFpbmVyIiwib3JkZXJEaXNjb3VudHNUb3RhbCIsIm9yZGVyV3JhcHBpbmdUb3RhbCIsIm9yZGVyU2hpcHBpbmdUb3RhbENvbnRhaW5lciIsIm9yZGVyU2hpcHBpbmdUb3RhbCIsIm9yZGVyVGF4ZXNUb3RhbCIsIm9yZGVyVG90YWwiLCJvcmRlckhvb2tUYWJzQ29udGFpbmVyIiwiY2FuY2VsUHJvZHVjdCIsImZvcm0iLCJidXR0b25zIiwiYWJvcnQiLCJzYXZlIiwicGFydGlhbFJlZnVuZCIsInN0YW5kYXJkUmVmdW5kIiwicmV0dXJuUHJvZHVjdCIsImNhbmNlbFByb2R1Y3RzIiwiaW5wdXRzIiwiYW1vdW50Iiwic2VsZWN0b3IiLCJjZWxsIiwiYWN0aW9ucyIsImNoZWNrYm94ZXMiLCJyZXN0b2NrIiwiY3JlZGl0U2xpcCIsInZvdWNoZXIiLCJyYWRpb3MiLCJ2b3VjaGVyUmVmdW5kVHlwZSIsInByb2R1Y3RQcmljZXMiLCJwcm9kdWN0UHJpY2VzVm91Y2hlckV4Y2x1ZGVkIiwibmVnYXRpdmVFcnJvck1lc3NhZ2UiLCJ0b2dnbGUiLCJwcmludE9yZGVyVmlld1BhZ2VCdXR0b24iLCJyZWZyZXNoUHJvZHVjdHNMaXN0TG9hZGluZ1NwaW5uZXIiLCJOdW1iZXJTeW1ib2wiLCJkZWNpbWFsIiwiZ3JvdXAiLCJwZXJjZW50U2lnbiIsIm1pbnVzU2lnbiIsInBsdXNTaWduIiwiZXhwb25lbnRpYWwiLCJzdXBlcnNjcmlwdGluZ0V4cG9uZW50IiwicGVyTWlsbGUiLCJpbmZpbml0eSIsIm5hbiIsInZhbGlkYXRlRGF0YSIsIkxvY2FsaXphdGlvbkV4Y2VwdGlvbiIsIk51bWJlclNwZWNpZmljYXRpb24iLCJwb3NpdGl2ZVBhdHRlcm4iLCJuZWdhdGl2ZVBhdHRlcm4iLCJzeW1ib2wiLCJtYXhGcmFjdGlvbkRpZ2l0cyIsIm1pbkZyYWN0aW9uRGlnaXRzIiwiZ3JvdXBpbmdVc2VkIiwicHJpbWFyeUdyb3VwU2l6ZSIsInNlY29uZGFyeUdyb3VwU2l6ZSIsIkNVUlJFTkNZX0RJU1BMQVlfU1lNQk9MIiwiUHJpY2VTcGVjaWZpY2F0aW9uIiwiY3VycmVuY3lTeW1ib2wiLCJjdXJyZW5jeUNvZGUiLCJOdW1iZXJGb3JtYXR0ZXIiLCJlc2NhcGVSRSIsInJlcXVpcmUiLCJDVVJSRU5DWV9TWU1CT0xfUExBQ0VIT0xERVIiLCJERUNJTUFMX1NFUEFSQVRPUl9QTEFDRUhPTERFUiIsIkdST1VQX1NFUEFSQVRPUl9QTEFDRUhPTERFUiIsIk1JTlVTX1NJR05fUExBQ0VIT0xERVIiLCJQRVJDRU5UX1NZTUJPTF9QTEFDRUhPTERFUiIsIlBMVVNfU0lHTl9QTEFDRUhPTERFUiIsInNwZWNpZmljYXRpb24iLCJudW1iZXJTcGVjaWZpY2F0aW9uIiwibnVtYmVyIiwibnVtIiwiTWF0aCIsImFicyIsInRvRml4ZWQiLCJnZXRNYXhGcmFjdGlvbkRpZ2l0cyIsImV4dHJhY3RNYWpvck1pbm9yRGlnaXRzIiwibWFqb3JEaWdpdHMiLCJtaW5vckRpZ2l0cyIsInNwbGl0TWFqb3JHcm91cHMiLCJhZGp1c3RNaW5vckRpZ2l0c1plcm9lcyIsImZvcm1hdHRlZE51bWJlciIsInBhdHRlcm4iLCJnZXRDbGRyUGF0dGVybiIsImFkZFBsYWNlaG9sZGVycyIsInJlcGxhY2VTeW1ib2xzIiwicGVyZm9ybVNwZWNpZmljUmVwbGFjZW1lbnRzIiwicmVzdWx0IiwidG9TdHJpbmciLCJzcGxpdCIsImRpZ2l0IiwiaXNHcm91cGluZ1VzZWQiLCJyZXZlcnNlIiwiZ3JvdXBzIiwicHVzaCIsInNwbGljZSIsImdldFByaW1hcnlHcm91cFNpemUiLCJsZW5ndGgiLCJnZXRTZWNvbmRhcnlHcm91cFNpemUiLCJuZXdHcm91cHMiLCJmb3JFYWNoIiwiam9pbiIsInJlcGxhY2UiLCJnZXRNaW5GcmFjdGlvbkRpZ2l0cyIsInBhZEVuZCIsImlzTmVnYXRpdmUiLCJnZXROZWdhdGl2ZVBhdHRlcm4iLCJnZXRQb3NpdGl2ZVBhdHRlcm4iLCJzeW1ib2xzIiwiZ2V0U3ltYm9sIiwibWFwIiwiZ2V0RGVjaW1hbCIsImdldEdyb3VwIiwiZ2V0TWludXNTaWduIiwiZ2V0UGVyY2VudFNpZ24iLCJnZXRQbHVzU2lnbiIsInN0cnRyIiwic3RyIiwicGFpcnMiLCJzdWJzdHJzIiwiUmVnRXhwIiwicGFydCIsImdldEN1cnJlbmN5U3ltYm9sIiwic3BlY2lmaWNhdGlvbnMiLCJudW1iZXJTeW1ib2xzIiwicGFyc2VJbnQiLCJwcm9kdWN0RGVsZXRlZEZyb21PcmRlciIsInByb2R1Y3RBZGRlZFRvT3JkZXIiLCJwcm9kdWN0VXBkYXRlZCIsInByb2R1Y3RFZGl0aW9uQ2FuY2VsZWQiLCJwcm9kdWN0TGlzdFBhZ2luYXRlZCIsInByb2R1Y3RMaXN0TnVtYmVyUGVyUGFnZSIsIk9yZGVyUHJpY2VzUmVmcmVzaGVyIiwicm91dGVyIiwib3JkZXJJZCIsImdldEpTT04iLCJ0aGVuIiwiT3JkZXJWaWV3UGFnZU1hcCIsInRleHQiLCJyZXNwb25zZSIsIm9yZGVyVG90YWxGb3JtYXR0ZWQiLCJkaXNjb3VudHNBbW91bnRGb3JtYXR0ZWQiLCJ0b2dnbGVDbGFzcyIsImRpc2NvdW50c0Ftb3VudERpc3BsYXllZCIsInByb2R1Y3RzVG90YWxGb3JtYXR0ZWQiLCJzaGlwcGluZ1RvdGFsRm9ybWF0dGVkIiwic2hpcHBpbmdUb3RhbERpc3BsYXllZCIsInRheGVzVG90YWxGb3JtYXR0ZWQiLCJwcm9kdWN0UHJpY2VzTGlzdCIsIm9yZGVyUHJvZHVjdFRySWQiLCJvcmRlckRldGFpbElkIiwiJHF1YW50aXR5Iiwid3JhcCIsInVuaXRQcmljZSIsImh0bWwiLCJ0b3RhbFByaWNlIiwicHJvZHVjdEVkaXRCdXR0b24iLCJ1bml0UHJpY2VUYXhJbmNsUmF3IiwidW5pdFByaWNlVGF4RXhjbFJhdyIsImdpdmVuUHJpY2UiLCJjb21iaW5hdGlvbklkIiwiaW52b2ljZUlkIiwicHJvZHVjdFJvd3MiLCJxdWVyeVNlbGVjdG9yQWxsIiwiZXhwZWN0ZWRQcm9kdWN0SWQiLCJOdW1iZXIiLCJleHBlY3RlZENvbWJpbmF0aW9uSWQiLCJleHBlY3RlZEdpdmVuUHJpY2UiLCJ1bm1hdGNoaW5nUHJpY2VFeGlzdHMiLCJwcm9kdWN0Um93IiwicHJvZHVjdFJvd0lkIiwiYXR0ciIsImN1cnJlbnRPcmRlckludm9pY2VJZCIsImN1cnJlbnRQcm9kdWN0SWQiLCJjdXJyZW50Q29tYmluYXRpb25JZCIsIk9yZGVyUHJpY2VzIiwidGF4SW5jbHVkZWQiLCJ0YXhSYXRlUGVyQ2VudCIsImN1cnJlbmN5UHJlY2lzaW9uIiwicHJpY2VUYXhJbmNsIiwicGFyc2VGbG9hdCIsInRheFJhdGUiLCJwc19yb3VuZCIsInRheEV4Y2x1ZGVkIiwicHJpY2VUYXhFeGNsIiwiT3JkZXJQcm9kdWN0UmVuZGVyZXIiLCIkcHJvZHVjdFJvdyIsIm5ld1JvdyIsImJlZm9yZSIsImhpZGUiLCJmYWRlSW4iLCJudW1Qcm9kdWN0cyIsImxvY2F0aW9uIiwiYXZhaWxhYmxlT3V0T2ZTdG9jayIsIm9yZGVySW52b2ljZUlkIiwiaXNPcmRlclRheEluY2x1ZGVkIiwiJG9yZGVyRWRpdCIsIk9yZGVyUHJvZHVjdEVkaXQiLCJkaXNwbGF5UHJvZHVjdCIsInByaWNlX3RheF9leGNsIiwicHJpY2VfdGF4X2luY2wiLCJ0YXhfcmF0ZSIsImFkZENsYXNzIiwic2Nyb2xsVGFyZ2V0IiwicmVtb3ZlQ2xhc3MiLCJtb3ZlUHJvZHVjdFBhbmVsVG9Ub3AiLCJyZXNldEFsbEVkaXRSb3dzIiwiJG1vZGlmaWNhdGlvblBvc2l0aW9uIiwiZGV0YWNoIiwiYXBwZW5kVG8iLCJjbG9zZXN0IiwidG9nZ2xlQ29sdW1uIiwiJHJvd3MiLCJzY3JvbGxWYWx1ZSIsIm9mZnNldCIsInRvcCIsImhlaWdodCIsImFuaW1hdGUiLCJzY3JvbGxUb3AiLCJwYWdpbmF0ZSIsInZhbCIsInByb3AiLCJlYWNoIiwia2V5IiwiZWRpdEJ1dHRvbiIsInJlc2V0RWRpdFJvdyIsIm9yZGVyUHJvZHVjdElkIiwiJHByb2R1Y3RFZGl0Um93IiwibnVtUGFnZSIsIiRjdXN0b21pemF0aW9uUm93cyIsIiR0YWJsZVBhZ2luYXRpb24iLCJudW1Sb3dzUGVyUGFnZSIsIm1heFBhZ2UiLCJjZWlsIiwibWF4IiwibWluIiwicGFnaW5hdGVVcGRhdGVDb250cm9scyIsInN0YXJ0Um93IiwiZW5kUm93IiwiaSIsInByZXYiLCJoYXNDbGFzcyIsIm5vdCIsInRvdGFsUGFnZSIsInRvZ2dsZVBhZ2luYXRpb25Db250cm9scyIsIm51bVBlclBhZ2UiLCJ1cGRhdGVQYWdpbmF0aW9uQ29udHJvbHMiLCJ0YXJnZXQiLCJmb3JjZURpc3BsYXkiLCJpc0NvbHVtbkRpc3BsYXllZCIsImZpbHRlciIsInRyaW0iLCJudW1QYWdlcyIsIiRsaW5rUGFnaW5hdGlvblRlbXBsYXRlIiwiJGxpbmtQYWdpbmF0aW9uIiwiY2xvbmUiLCJUZXh0V2l0aExlbmd0aENvdW50ZXIiLCJ3cmFwcGVyU2VsZWN0b3IiLCJ0ZXh0U2VsZWN0b3IiLCJpbnB1dFNlbGVjdG9yIiwiZSIsIiRpbnB1dCIsImN1cnJlbnRUYXJnZXQiLCJyZW1haW5pbmdMZW5ndGgiLCJPcmRlclZpZXdQYWdlTWVzc2FnZXNIYW5kbGVyIiwiJG9yZGVyTWVzc2FnZUNoYW5nZVdhcm5pbmciLCIkbWVzc2FnZXNDb250YWluZXIiLCJsaXN0ZW5Gb3JQcmVkZWZpbmVkTWVzc2FnZVNlbGVjdGlvbiIsIl9oYW5kbGVQcmVkZWZpbmVkTWVzc2FnZVNlbGVjdGlvbiIsImxpc3RlbkZvckZ1bGxNZXNzYWdlc09wZW4iLCJfb25GdWxsTWVzc2FnZXNPcGVuIiwiJGN1cnJlbnRJdGVtIiwidmFsdWVJZCIsIiRvcmRlck1lc3NhZ2UiLCJpc1NhbWVNZXNzYWdlIiwiY29uZmlybSIsInRyaWdnZXIiLCJfc2Nyb2xsVG9Nc2dMaXN0Qm90dG9tIiwiJG1zZ01vZGFsIiwibXNnTGlzdCIsImNsYXNzQ2hlY2tJbnRlcnZhbCIsInNldEludGVydmFsIiwic2Nyb2xsSGVpZ2h0IiwiY2xlYXJJbnRlcnZhbCIsIk9yZGVyU2hpcHBpbmdNYW5hZ2VyIiwiX2luaXRPcmRlclNoaXBwaW5nVXBkYXRlRXZlbnRIYW5kbGVyIiwiZXZlbnQiLCIkYnRuIiwiT3JkZXJQcm9kdWN0QXV0b2NvbXBsZXRlIiwiaW5wdXQiLCJhY3RpdmVTZWFyY2hSZXF1ZXN0IiwicmVzdWx0cyIsImRyb3Bkb3duTWVudSIsIm9uSXRlbUNsaWNrZWRDYWxsYmFjayIsInN0b3BJbW1lZGlhdGVQcm9wYWdhdGlvbiIsInVwZGF0ZVJlc3VsdHMiLCJkZWxheVNlYXJjaCIsImNsZWFyVGltZW91dCIsInNlYXJjaFRpbWVvdXRJZCIsInZhbHVlIiwic2V0VGltZW91dCIsInNlYXJjaCIsImN1cnJlbmN5Iiwic2VhcmNoX3BocmFzZSIsImN1cnJlbmN5X2lkIiwib3JkZXJfaWQiLCJnZXQiLCJhbHdheXMiLCJlbXB0eSIsInByb2R1Y3RzIiwicHJldmVudERlZmF1bHQiLCJvbkl0ZW1DbGlja2VkIiwic2VsZWN0ZWRQcm9kdWN0IiwiT3JkZXJQcm9kdWN0QWRkIiwicHJvZHVjdElkSW5wdXQiLCJjb21iaW5hdGlvbnNCbG9jayIsImNvbWJpbmF0aW9uc1NlbGVjdCIsInByaWNlVGF4SW5jbHVkZWRJbnB1dCIsInByaWNlVGF4RXhjbHVkZWRJbnB1dCIsInRheFJhdGVJbnB1dCIsInF1YW50aXR5SW5wdXQiLCJhdmFpbGFibGVUZXh0IiwibG9jYXRpb25UZXh0IiwidG90YWxQcmljZVRleHQiLCJpbnZvaWNlU2VsZWN0IiwiZnJlZVNoaXBwaW5nU2VsZWN0IiwicHJvZHVjdEFkZE1lbnVCdG4iLCJhdmFpbGFibGUiLCJzZXR1cExpc3RlbmVyIiwicHJpY2VUYXhDYWxjdWxhdG9yIiwib3JkZXJQcm9kdWN0UmVuZGVyZXIiLCJvcmRlclByaWNlc1JlZnJlc2hlciIsIm5ld1F1YW50aXR5IiwicmVtYWluaW5nQXZhaWxhYmxlIiwiZGlzYWJsZUFkZEFjdGlvbkJ0biIsImNhbGN1bGF0ZVRvdGFsUHJpY2UiLCJyZW1vdmVBdHRyIiwiY2FsY3VsYXRlVGF4RXhjbHVkZWQiLCJjYWxjdWxhdGVUYXhJbmNsdWRlZCIsImNvbmZpcm1OZXdJbnZvaWNlIiwidG9nZ2xlUHJvZHVjdEFkZE5ld0ludm9pY2VJbmZvIiwic3RvY2siLCJzZXRDb21iaW5hdGlvbnMiLCJjb21iaW5hdGlvbnMiLCJhdHRyaWJ1dGVDb21iaW5hdGlvbklkIiwicHJpY2VUYXhFeGNsdWRlZCIsInByaWNlVGF4SW5jbHVkZWQiLCJhdHRyaWJ1dGUiLCJwcm9kdWN0X2lkIiwiY29tYmluYXRpb25faWQiLCJpbnZvaWNlX2lkIiwiZnJlZV9zaGlwcGluZyIsImFqYXgiLCJ1cmwiLCJtZXRob2QiLCJlbWl0IiwiT3JkZXJWaWV3RXZlbnRNYXAiLCJyZXNwb25zZUpTT04iLCJncm93bCIsImVycm9yIiwiY29uZmlybU5ld1ByaWNlIiwiYWRkUHJvZHVjdCIsInByb2R1Y3RQcmljZU1hdGNoIiwiY2hlY2tPdGhlclByb2R1Y3RQcmljZXNNYXRjaCIsIm1vZGFsRWRpdFByaWNlIiwiT3JkZXJWaWV3UGFnZSIsIm9yZGVyRGlzY291bnRzUmVmcmVzaGVyIiwiT3JkZXJEaXNjb3VudHNSZWZyZXNoZXIiLCJvcmRlclByb2R1Y3RNYW5hZ2VyIiwiT3JkZXJQcm9kdWN0TWFuYWdlciIsIm9yZGVyUGF5bWVudHNSZWZyZXNoZXIiLCJPcmRlclBheW1lbnRzUmVmcmVzaGVyIiwib3JkZXJTaGlwcGluZ1JlZnJlc2hlciIsIk9yZGVyU2hpcHBpbmdSZWZyZXNoZXIiLCJvcmRlckRvY3VtZW50c1JlZnJlc2hlciIsIk9yZGVyRG9jdW1lbnRzUmVmcmVzaGVyIiwib3JkZXJJbnZvaWNlc1JlZnJlc2hlciIsIk9yZGVySW52b2ljZXNSZWZyZXNoZXIiLCJvcmRlclByb2R1Y3RDYW5jZWwiLCJPcmRlclByb2R1Y3RDYW5jZWwiLCJsaXN0ZW5Ub0V2ZW50cyIsImZhbmN5Ym94IiwicmVmcmVzaCIsInJlZnJlc2hQcm9kdWN0c0xpc3QiLCJlZGl0Um93c0xlZnQiLCJtb3ZlUHJvZHVjdFBhbmVsVG9PcmlnaW5hbFBvc2l0aW9uIiwicmVmcmVzaFByb2R1Y3RQcmljZXMiLCJsaXN0ZW5Gb3JQcm9kdWN0RGVsZXRlIiwibGlzdGVuRm9yUHJvZHVjdEVkaXQiLCJyZXNldFRvb2xUaXBzIiwicmVzZXRBZGRSb3ciLCJvZmYiLCJoYW5kbGVEZWxldGVQcm9kdWN0RXZlbnQiLCJwc3Rvb2x0aXAiLCJtb3ZlUHJvZHVjdHNQYW5lbFRvTW9kaWZpY2F0aW9uUG9zaXRpb24iLCJlZGl0UHJvZHVjdEZyb21MaXN0IiwiYnV0dG9uIiwicmVsYXRlZFRhcmdldCIsInBhY2tJdGVtcyIsIiRpdGVtIiwiaXRlbSIsImltYWdlUGF0aCIsInJlZmVyZW5jZSIsInN1cHBsaWVyUmVmZXJlbmNlIiwiYWN0aXZlUGFnZSIsImdldEFjdGl2ZVBhZ2UiLCIkc2VsZWN0IiwidXBkYXRlTnVtUGVyUGFnZSIsIm1vdmVQcm9kdWN0c1BhbmVsVG9SZWZ1bmRQb3NpdGlvbiIsInNob3dQYXJ0aWFsUmVmdW5kIiwic2hvd1N0YW5kYXJkUmVmdW5kIiwic2hvd1JldHVyblByb2R1Y3QiLCJoaWRlUmVmdW5kIiwic2hvd0NhbmNlbFByb2R1Y3RGb3JtIiwiaW5pdGlhbE51bVByb2R1Y3RzIiwiY3VycmVudFBhZ2UiLCJkb25lIiwicHJlcGVuZCIsIm5ld051bVByb2R1Y3RzIiwibmV3UGFnZXNOdW0iLCJ1cGRhdGVOdW1Qcm9kdWN0cyIsInRyYW5zbGF0ZV9qYXZhc2NyaXB0cyIsIm5vdGljZSIsImZhaWwiLCJJbnZvaWNlTm90ZU1hbmFnZXIiLCJzZXR1cExpc3RlbmVycyIsIl9pbml0U2hvd05vdGVGb3JtRXZlbnRIYW5kbGVyIiwiX2luaXRDbG9zZU5vdGVGb3JtRXZlbnRIYW5kbGVyIiwiX2luaXRFbnRlclBheW1lbnRFdmVudEhhbmRsZXIiLCIkbm90ZVJvdyIsIm5leHQiLCJwYXltZW50QW1vdW50Iiwic2Nyb2xsSW50b1ZpZXciLCJiZWhhdmlvciIsIkRJU0NPVU5UX1RZUEVfQU1PVU5UIiwiRElTQ09VTlRfVFlQRV9QRVJDRU5UIiwiRElTQ09VTlRfVFlQRV9GUkVFX1NISVBQSU5HIiwib3JkZXJWaWV3UGFnZSIsIm9yZGVyQWRkQXV0b2NvbXBsZXRlIiwib3JkZXJBZGQiLCJsaXN0ZW5Gb3JQcm9kdWN0UGFjayIsImxpc3RlbkZvclByb2R1Y3RBZGQiLCJsaXN0ZW5Gb3JQcm9kdWN0UGFnaW5hdGlvbiIsImxpc3RlbkZvclJlZnVuZCIsImxpc3RlbkZvckNhbmNlbFByb2R1Y3QiLCJsaXN0ZW5Gb3JTZWFyY2giLCJzZXRQcm9kdWN0IiwiaGFuZGxlUGF5bWVudERldGFpbHNUb2dnbGUiLCJoYW5kbGVQcml2YXRlTm90ZUNoYW5nZSIsImhhbmRsZVVwZGF0ZU9yZGVyU3RhdHVzQnV0dG9uIiwib3JkZXJWaWV3UGFnZU1lc3NhZ2VIYW5kbGVyIiwidG9nZ2xlUHJpdmF0ZU5vdGVCbG9jayIsInRlbXBUaXRsZSIsInByaW50IiwiaW5pdEFkZENhcnRSdWxlRm9ybUhhbmRsZXIiLCJpbml0Q2hhbmdlQWRkcmVzc0Zvcm1IYW5kbGVyIiwiaW5pdEhvb2tUYWJzIiwidGFiIiwiJHBheW1lbnREZXRhaWxSb3ciLCIkYmxvY2siLCJpc1ByaXZhdGVOb3RlT3BlbmVkIiwiJGljb24iLCIkc3VibWl0QnRuIiwiJGZvcm0iLCIkdmFsdWVIZWxwIiwiJHZhbHVlSW5wdXQiLCIkdmFsdWVGb3JtR3JvdXAiLCJjYXJ0UnVsZU5hbWUiLCJzZWxlY3RlZENhcnRSdWxlVHlwZSIsIiR2YWx1ZVVuaXQiLCIkd3JhcHBlciIsIiRlbGVtZW50IiwiJG9wdGlvbiIsInNlbGVjdGVkT3JkZXJTdGF0dXNJZCIsImNzcyIsInJlcGxhY2VXaXRoIiwiaW52b2ljZU5vdGVNYW5hZ2VyIiwidG90YWwiLCJpbnZvaWNlcyIsIiRwYXltZW50SW52b2ljZVNlbGVjdCIsIiRhZGRQcm9kdWN0SW52b2ljZVNlbGVjdCIsIiRleGlzdGluZ0ludm9pY2VzR3JvdXAiLCIkcHJvZHVjdEVkaXRJbnZvaWNlU2VsZWN0IiwiJGFkZERpc2NvdW50SW52b2ljZVNlbGVjdCIsImludm9pY2VOYW1lIiwiaW52b2ljZU5hbWVXaXRob3V0UHJpY2UiLCJzZWxlY3RlZEluZGV4IiwiY2FuY2VsUHJvZHVjdEZvcm0iLCJvcmRlckRlbGl2ZXJlZCIsImlzVGF4SW5jbHVkZWQiLCJkaXNjb3VudHNBbW91bnQiLCJjdXJyZW5jeUZvcm1hdHRlciIsImJ1aWxkIiwidXNlQW1vdW50SW5wdXRzIiwibGlzdGVuRm9ySW5wdXRzIiwiaGlkZUNhbmNlbEVsZW1lbnRzIiwiaW5pdEZvcm0iLCJhY3Rpb25OYW1lIiwiZm9ybUFjdGlvbiIsImZvcm1DbGFzcyIsInVwZGF0ZVZvdWNoZXJSZWZ1bmQiLCIkcHJvZHVjdFF1YW50aXR5SW5wdXQiLCJ1cGRhdGVBbW91bnRJbnB1dCIsIiRwcm9kdWN0Q2hlY2tib3giLCIkcGFyZW50Q2VsbCIsInBhcmVudHMiLCJwcm9kdWN0UXVhbnRpdHlJbnB1dCIsInJlZnVuZGFibGVRdWFudGl0eSIsInByb2R1Y3RRdWFudGl0eSIsImlzIiwiJHByb2R1Y3RBbW91bnQiLCJwcmljZUZpZWxkTmFtZSIsInByb2R1Y3RVbml0UHJpY2UiLCJhbW91bnRSZWZ1bmRhYmxlIiwiZ3Vlc3NlZEFtb3VudCIsImFtb3VudFZhbHVlIiwidG90YWxBbW91bnQiLCJpbmRleCIsImZsb2F0VmFsdWUiLCIkcXVhbnRpdHlJbnB1dCIsInJlZnVuZEFtb3VudCIsImdldFJlZnVuZEFtb3VudCIsInVwZGF0ZVZvdWNoZXJSZWZ1bmRUeXBlTGFiZWwiLCJyZWZ1bmRWb3VjaGVyRXhjbHVkZWQiLCJkZWZhdWx0TGFiZWwiLCIkbGFiZWwiLCJmb3JtYXR0ZWRBbW91bnQiLCJmb3JtYXQiLCJsYXN0Q2hpbGQiLCJub2RlVmFsdWUiLCJjYW5jZWxQcm9kdWN0Um91dGUiLCJwcmV2aW91c1F1YW50aXR5IiwidXBkYXRlVG90YWwiLCJkaXNhYmxlRWRpdEFjdGlvbkJ0biIsImNvbmZpcm1lZCIsImhhbmRsZUVkaXRQcm9kdWN0V2l0aENvbmZpcm1hdGlvbk1vZGFsIiwidXBkYXRlZFRvdGFsIiwicHJpY2VUb3RhbFRleHQiLCJpbml0aWFsVG90YWwiLCJwcm9kdWN0Um93RWRpdCIsInJlbW92ZUFsbElkcyIsImFmdGVyIiwiZWRpdFByb2R1Y3QiLCJkYXRhU2VsZWN0b3IiLCJpbnZvaWNlIiwiZGVsZXRlUHJvZHVjdCIsIm9sZE9yZGVyRGV0YWlsSWQiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7O0FDaEVBOztBQUVBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsRTs7Ozs7OztBQ1JBOztBQUVBOztBQUVBOztBQUVBOztBQUVBLHNDQUFzQyx1Q0FBdUMsZ0JBQWdCOztBQUU3RjtBQUNBO0FBQ0EsbUJBQW1CLGtCQUFrQjtBQUNyQztBQUNBO0FBQ0E7QUFDQTtBQUNB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Q0E7QUFDQTtBQUNBO0FBQ0EsQ0FBQyxFIiwiZmlsZSI6Im9yZGVyX3ZpZXcuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSA1MjUpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIGY3ZjQyYjRiZjU0MzQyODA2MWJhIiwiXCJ1c2Ugc3RyaWN0XCI7XG5cbmV4cG9ydHMuX19lc01vZHVsZSA9IHRydWU7XG5cbmV4cG9ydHMuZGVmYXVsdCA9IGZ1bmN0aW9uIChpbnN0YW5jZSwgQ29uc3RydWN0b3IpIHtcbiAgaWYgKCEoaW5zdGFuY2UgaW5zdGFuY2VvZiBDb25zdHJ1Y3RvcikpIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKFwiQ2Fubm90IGNhbGwgYSBjbGFzcyBhcyBhIGZ1bmN0aW9uXCIpO1xuICB9XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY2xhc3NDYWxsQ2hlY2suanNcbi8vIG1vZHVsZSBpZCA9IDBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJcInVzZSBzdHJpY3RcIjtcblxuZXhwb3J0cy5fX2VzTW9kdWxlID0gdHJ1ZTtcblxudmFyIF9kZWZpbmVQcm9wZXJ0eSA9IHJlcXVpcmUoXCIuLi9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHlcIik7XG5cbnZhciBfZGVmaW5lUHJvcGVydHkyID0gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChfZGVmaW5lUHJvcGVydHkpO1xuXG5mdW5jdGlvbiBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KG9iaikgeyByZXR1cm4gb2JqICYmIG9iai5fX2VzTW9kdWxlID8gb2JqIDogeyBkZWZhdWx0OiBvYmogfTsgfVxuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoKSB7XG4gIGZ1bmN0aW9uIGRlZmluZVByb3BlcnRpZXModGFyZ2V0LCBwcm9wcykge1xuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgcHJvcHMubGVuZ3RoOyBpKyspIHtcbiAgICAgIHZhciBkZXNjcmlwdG9yID0gcHJvcHNbaV07XG4gICAgICBkZXNjcmlwdG9yLmVudW1lcmFibGUgPSBkZXNjcmlwdG9yLmVudW1lcmFibGUgfHwgZmFsc2U7XG4gICAgICBkZXNjcmlwdG9yLmNvbmZpZ3VyYWJsZSA9IHRydWU7XG4gICAgICBpZiAoXCJ2YWx1ZVwiIGluIGRlc2NyaXB0b3IpIGRlc2NyaXB0b3Iud3JpdGFibGUgPSB0cnVlO1xuICAgICAgKDAsIF9kZWZpbmVQcm9wZXJ0eTIuZGVmYXVsdCkodGFyZ2V0LCBkZXNjcmlwdG9yLmtleSwgZGVzY3JpcHRvcik7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIGZ1bmN0aW9uIChDb25zdHJ1Y3RvciwgcHJvdG9Qcm9wcywgc3RhdGljUHJvcHMpIHtcbiAgICBpZiAocHJvdG9Qcm9wcykgZGVmaW5lUHJvcGVydGllcyhDb25zdHJ1Y3Rvci5wcm90b3R5cGUsIHByb3RvUHJvcHMpO1xuICAgIGlmIChzdGF0aWNQcm9wcykgZGVmaW5lUHJvcGVydGllcyhDb25zdHJ1Y3Rvciwgc3RhdGljUHJvcHMpO1xuICAgIHJldHVybiBDb25zdHJ1Y3RvcjtcbiAgfTtcbn0oKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NyZWF0ZUNsYXNzLmpzXG4vLyBtb2R1bGUgaWQgPSAxXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLy8gVGhhbmsncyBJRTggZm9yIGhpcyBmdW5ueSBkZWZpbmVQcm9wZXJ0eVxubW9kdWxlLmV4cG9ydHMgPSAhcmVxdWlyZSgnLi9fZmFpbHMnKShmdW5jdGlvbigpe1xuICByZXR1cm4gT2JqZWN0LmRlZmluZVByb3BlcnR5KHt9LCAnYScsIHtnZXQ6IGZ1bmN0aW9uKCl7IHJldHVybiA3OyB9fSkuYSAhPSA3O1xufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kZXNjcmlwdG9ycy5qc1xuLy8gbW9kdWxlIGlkID0gMlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgY29yZSA9IG1vZHVsZS5leHBvcnRzID0ge3ZlcnNpb246ICcyLjQuMCd9O1xuaWYodHlwZW9mIF9fZSA9PSAnbnVtYmVyJylfX2UgPSBjb3JlOyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lIG5vLXVuZGVmXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jb3JlLmpzXG4vLyBtb2R1bGUgaWQgPSAzXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gdHlwZW9mIGl0ID09PSAnb2JqZWN0JyA/IGl0ICE9PSBudWxsIDogdHlwZW9mIGl0ID09PSAnZnVuY3Rpb24nO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2lzLW9iamVjdC5qc1xuLy8gbW9kdWxlIGlkID0gNFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyBodHRwczovL2dpdGh1Yi5jb20vemxvaXJvY2svY29yZS1qcy9pc3N1ZXMvODYjaXNzdWVjb21tZW50LTExNTc1OTAyOFxudmFyIGdsb2JhbCA9IG1vZHVsZS5leHBvcnRzID0gdHlwZW9mIHdpbmRvdyAhPSAndW5kZWZpbmVkJyAmJiB3aW5kb3cuTWF0aCA9PSBNYXRoXG4gID8gd2luZG93IDogdHlwZW9mIHNlbGYgIT0gJ3VuZGVmaW5lZCcgJiYgc2VsZi5NYXRoID09IE1hdGggPyBzZWxmIDogRnVuY3Rpb24oJ3JldHVybiB0aGlzJykoKTtcbmlmKHR5cGVvZiBfX2cgPT0gJ251bWJlcicpX19nID0gZ2xvYmFsOyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lIG5vLXVuZGVmXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19nbG9iYWwuanNcbi8vIG1vZHVsZSBpZCA9IDVcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGFuT2JqZWN0ICAgICAgID0gcmVxdWlyZSgnLi9fYW4tb2JqZWN0JylcbiAgLCBJRThfRE9NX0RFRklORSA9IHJlcXVpcmUoJy4vX2llOC1kb20tZGVmaW5lJylcbiAgLCB0b1ByaW1pdGl2ZSAgICA9IHJlcXVpcmUoJy4vX3RvLXByaW1pdGl2ZScpXG4gICwgZFAgICAgICAgICAgICAgPSBPYmplY3QuZGVmaW5lUHJvcGVydHk7XG5cbmV4cG9ydHMuZiA9IHJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJykgPyBPYmplY3QuZGVmaW5lUHJvcGVydHkgOiBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0eShPLCBQLCBBdHRyaWJ1dGVzKXtcbiAgYW5PYmplY3QoTyk7XG4gIFAgPSB0b1ByaW1pdGl2ZShQLCB0cnVlKTtcbiAgYW5PYmplY3QoQXR0cmlidXRlcyk7XG4gIGlmKElFOF9ET01fREVGSU5FKXRyeSB7XG4gICAgcmV0dXJuIGRQKE8sIFAsIEF0dHJpYnV0ZXMpO1xuICB9IGNhdGNoKGUpeyAvKiBlbXB0eSAqLyB9XG4gIGlmKCdnZXQnIGluIEF0dHJpYnV0ZXMgfHwgJ3NldCcgaW4gQXR0cmlidXRlcyl0aHJvdyBUeXBlRXJyb3IoJ0FjY2Vzc29ycyBub3Qgc3VwcG9ydGVkIScpO1xuICBpZigndmFsdWUnIGluIEF0dHJpYnV0ZXMpT1tQXSA9IEF0dHJpYnV0ZXMudmFsdWU7XG4gIHJldHVybiBPO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1kcC5qc1xuLy8gbW9kdWxlIGlkID0gNlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGV4ZWMpe1xuICB0cnkge1xuICAgIHJldHVybiAhIWV4ZWMoKTtcbiAgfSBjYXRjaChlKXtcbiAgICByZXR1cm4gdHJ1ZTtcbiAgfVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2ZhaWxzLmpzXG4vLyBtb2R1bGUgaWQgPSA3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBnbG9iYWwgICAgPSByZXF1aXJlKCcuL19nbG9iYWwnKVxuICAsIGNvcmUgICAgICA9IHJlcXVpcmUoJy4vX2NvcmUnKVxuICAsIGN0eCAgICAgICA9IHJlcXVpcmUoJy4vX2N0eCcpXG4gICwgaGlkZSAgICAgID0gcmVxdWlyZSgnLi9faGlkZScpXG4gICwgUFJPVE9UWVBFID0gJ3Byb3RvdHlwZSc7XG5cbnZhciAkZXhwb3J0ID0gZnVuY3Rpb24odHlwZSwgbmFtZSwgc291cmNlKXtcbiAgdmFyIElTX0ZPUkNFRCA9IHR5cGUgJiAkZXhwb3J0LkZcbiAgICAsIElTX0dMT0JBTCA9IHR5cGUgJiAkZXhwb3J0LkdcbiAgICAsIElTX1NUQVRJQyA9IHR5cGUgJiAkZXhwb3J0LlNcbiAgICAsIElTX1BST1RPICA9IHR5cGUgJiAkZXhwb3J0LlBcbiAgICAsIElTX0JJTkQgICA9IHR5cGUgJiAkZXhwb3J0LkJcbiAgICAsIElTX1dSQVAgICA9IHR5cGUgJiAkZXhwb3J0LldcbiAgICAsIGV4cG9ydHMgICA9IElTX0dMT0JBTCA/IGNvcmUgOiBjb3JlW25hbWVdIHx8IChjb3JlW25hbWVdID0ge30pXG4gICAgLCBleHBQcm90byAgPSBleHBvcnRzW1BST1RPVFlQRV1cbiAgICAsIHRhcmdldCAgICA9IElTX0dMT0JBTCA/IGdsb2JhbCA6IElTX1NUQVRJQyA/IGdsb2JhbFtuYW1lXSA6IChnbG9iYWxbbmFtZV0gfHwge30pW1BST1RPVFlQRV1cbiAgICAsIGtleSwgb3duLCBvdXQ7XG4gIGlmKElTX0dMT0JBTClzb3VyY2UgPSBuYW1lO1xuICBmb3Ioa2V5IGluIHNvdXJjZSl7XG4gICAgLy8gY29udGFpbnMgaW4gbmF0aXZlXG4gICAgb3duID0gIUlTX0ZPUkNFRCAmJiB0YXJnZXQgJiYgdGFyZ2V0W2tleV0gIT09IHVuZGVmaW5lZDtcbiAgICBpZihvd24gJiYga2V5IGluIGV4cG9ydHMpY29udGludWU7XG4gICAgLy8gZXhwb3J0IG5hdGl2ZSBvciBwYXNzZWRcbiAgICBvdXQgPSBvd24gPyB0YXJnZXRba2V5XSA6IHNvdXJjZVtrZXldO1xuICAgIC8vIHByZXZlbnQgZ2xvYmFsIHBvbGx1dGlvbiBmb3IgbmFtZXNwYWNlc1xuICAgIGV4cG9ydHNba2V5XSA9IElTX0dMT0JBTCAmJiB0eXBlb2YgdGFyZ2V0W2tleV0gIT0gJ2Z1bmN0aW9uJyA/IHNvdXJjZVtrZXldXG4gICAgLy8gYmluZCB0aW1lcnMgdG8gZ2xvYmFsIGZvciBjYWxsIGZyb20gZXhwb3J0IGNvbnRleHRcbiAgICA6IElTX0JJTkQgJiYgb3duID8gY3R4KG91dCwgZ2xvYmFsKVxuICAgIC8vIHdyYXAgZ2xvYmFsIGNvbnN0cnVjdG9ycyBmb3IgcHJldmVudCBjaGFuZ2UgdGhlbSBpbiBsaWJyYXJ5XG4gICAgOiBJU19XUkFQICYmIHRhcmdldFtrZXldID09IG91dCA/IChmdW5jdGlvbihDKXtcbiAgICAgIHZhciBGID0gZnVuY3Rpb24oYSwgYiwgYyl7XG4gICAgICAgIGlmKHRoaXMgaW5zdGFuY2VvZiBDKXtcbiAgICAgICAgICBzd2l0Y2goYXJndW1lbnRzLmxlbmd0aCl7XG4gICAgICAgICAgICBjYXNlIDA6IHJldHVybiBuZXcgQztcbiAgICAgICAgICAgIGNhc2UgMTogcmV0dXJuIG5ldyBDKGEpO1xuICAgICAgICAgICAgY2FzZSAyOiByZXR1cm4gbmV3IEMoYSwgYik7XG4gICAgICAgICAgfSByZXR1cm4gbmV3IEMoYSwgYiwgYyk7XG4gICAgICAgIH0gcmV0dXJuIEMuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICAgIH07XG4gICAgICBGW1BST1RPVFlQRV0gPSBDW1BST1RPVFlQRV07XG4gICAgICByZXR1cm4gRjtcbiAgICAvLyBtYWtlIHN0YXRpYyB2ZXJzaW9ucyBmb3IgcHJvdG90eXBlIG1ldGhvZHNcbiAgICB9KShvdXQpIDogSVNfUFJPVE8gJiYgdHlwZW9mIG91dCA9PSAnZnVuY3Rpb24nID8gY3R4KEZ1bmN0aW9uLmNhbGwsIG91dCkgOiBvdXQ7XG4gICAgLy8gZXhwb3J0IHByb3RvIG1ldGhvZHMgdG8gY29yZS4lQ09OU1RSVUNUT1IlLm1ldGhvZHMuJU5BTUUlXG4gICAgaWYoSVNfUFJPVE8pe1xuICAgICAgKGV4cG9ydHMudmlydHVhbCB8fCAoZXhwb3J0cy52aXJ0dWFsID0ge30pKVtrZXldID0gb3V0O1xuICAgICAgLy8gZXhwb3J0IHByb3RvIG1ldGhvZHMgdG8gY29yZS4lQ09OU1RSVUNUT1IlLnByb3RvdHlwZS4lTkFNRSVcbiAgICAgIGlmKHR5cGUgJiAkZXhwb3J0LlIgJiYgZXhwUHJvdG8gJiYgIWV4cFByb3RvW2tleV0paGlkZShleHBQcm90bywga2V5LCBvdXQpO1xuICAgIH1cbiAgfVxufTtcbi8vIHR5cGUgYml0bWFwXG4kZXhwb3J0LkYgPSAxOyAgIC8vIGZvcmNlZFxuJGV4cG9ydC5HID0gMjsgICAvLyBnbG9iYWxcbiRleHBvcnQuUyA9IDQ7ICAgLy8gc3RhdGljXG4kZXhwb3J0LlAgPSA4OyAgIC8vIHByb3RvXG4kZXhwb3J0LkIgPSAxNjsgIC8vIGJpbmRcbiRleHBvcnQuVyA9IDMyOyAgLy8gd3JhcFxuJGV4cG9ydC5VID0gNjQ7ICAvLyBzYWZlXG4kZXhwb3J0LlIgPSAxMjg7IC8vIHJlYWwgcHJvdG8gbWV0aG9kIGZvciBgbGlicmFyeWAgXG5tb2R1bGUuZXhwb3J0cyA9ICRleHBvcnQ7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19leHBvcnQuanNcbi8vIG1vZHVsZSBpZCA9IDhcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGc7XHJcblxyXG4vLyBUaGlzIHdvcmtzIGluIG5vbi1zdHJpY3QgbW9kZVxyXG5nID0gKGZ1bmN0aW9uKCkge1xyXG5cdHJldHVybiB0aGlzO1xyXG59KSgpO1xyXG5cclxudHJ5IHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIGV2YWwgaXMgYWxsb3dlZCAoc2VlIENTUClcclxuXHRnID0gZyB8fCBGdW5jdGlvbihcInJldHVybiB0aGlzXCIpKCkgfHwgKDEsZXZhbCkoXCJ0aGlzXCIpO1xyXG59IGNhdGNoKGUpIHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIHRoZSB3aW5kb3cgcmVmZXJlbmNlIGlzIGF2YWlsYWJsZVxyXG5cdGlmKHR5cGVvZiB3aW5kb3cgPT09IFwib2JqZWN0XCIpXHJcblx0XHRnID0gd2luZG93O1xyXG59XHJcblxyXG4vLyBnIGNhbiBzdGlsbCBiZSB1bmRlZmluZWQsIGJ1dCBub3RoaW5nIHRvIGRvIGFib3V0IGl0Li4uXHJcbi8vIFdlIHJldHVybiB1bmRlZmluZWQsIGluc3RlYWQgb2Ygbm90aGluZyBoZXJlLCBzbyBpdCdzXHJcbi8vIGVhc2llciB0byBoYW5kbGUgdGhpcyBjYXNlLiBpZighZ2xvYmFsKSB7IC4uLn1cclxuXHJcbm1vZHVsZS5leHBvcnRzID0gZztcclxuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSA5XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDkgMTAgMTEgMTIgMTMgMTcgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDYgNTQiLCJ2YXIgZFAgICAgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1kcCcpXG4gICwgY3JlYXRlRGVzYyA9IHJlcXVpcmUoJy4vX3Byb3BlcnR5LWRlc2MnKTtcbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSA/IGZ1bmN0aW9uKG9iamVjdCwga2V5LCB2YWx1ZSl7XG4gIHJldHVybiBkUC5mKG9iamVjdCwga2V5LCBjcmVhdGVEZXNjKDEsIHZhbHVlKSk7XG59IDogZnVuY3Rpb24ob2JqZWN0LCBrZXksIHZhbHVlKXtcbiAgb2JqZWN0W2tleV0gPSB2YWx1ZTtcbiAgcmV0dXJuIG9iamVjdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oaWRlLmpzXG4vLyBtb2R1bGUgaWQgPSAxMFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgaXNPYmplY3QgPSByZXF1aXJlKCcuL19pcy1vYmplY3QnKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICBpZighaXNPYmplY3QoaXQpKXRocm93IFR5cGVFcnJvcihpdCArICcgaXMgbm90IGFuIG9iamVjdCEnKTtcbiAgcmV0dXJuIGl0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2FuLW9iamVjdC5qc1xuLy8gbW9kdWxlIGlkID0gMTFcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihiaXRtYXAsIHZhbHVlKXtcbiAgcmV0dXJuIHtcbiAgICBlbnVtZXJhYmxlICA6ICEoYml0bWFwICYgMSksXG4gICAgY29uZmlndXJhYmxlOiAhKGJpdG1hcCAmIDIpLFxuICAgIHdyaXRhYmxlICAgIDogIShiaXRtYXAgJiA0KSxcbiAgICB2YWx1ZSAgICAgICA6IHZhbHVlXG4gIH07XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fcHJvcGVydHktZGVzYy5qc1xuLy8gbW9kdWxlIGlkID0gMTJcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLy8gb3B0aW9uYWwgLyBzaW1wbGUgY29udGV4dCBiaW5kaW5nXG52YXIgYUZ1bmN0aW9uID0gcmVxdWlyZSgnLi9fYS1mdW5jdGlvbicpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihmbiwgdGhhdCwgbGVuZ3RoKXtcbiAgYUZ1bmN0aW9uKGZuKTtcbiAgaWYodGhhdCA9PT0gdW5kZWZpbmVkKXJldHVybiBmbjtcbiAgc3dpdGNoKGxlbmd0aCl7XG4gICAgY2FzZSAxOiByZXR1cm4gZnVuY3Rpb24oYSl7XG4gICAgICByZXR1cm4gZm4uY2FsbCh0aGF0LCBhKTtcbiAgICB9O1xuICAgIGNhc2UgMjogcmV0dXJuIGZ1bmN0aW9uKGEsIGIpe1xuICAgICAgcmV0dXJuIGZuLmNhbGwodGhhdCwgYSwgYik7XG4gICAgfTtcbiAgICBjYXNlIDM6IHJldHVybiBmdW5jdGlvbihhLCBiLCBjKXtcbiAgICAgIHJldHVybiBmbi5jYWxsKHRoYXQsIGEsIGIsIGMpO1xuICAgIH07XG4gIH1cbiAgcmV0dXJuIGZ1bmN0aW9uKC8qIC4uLmFyZ3MgKi8pe1xuICAgIHJldHVybiBmbi5hcHBseSh0aGF0LCBhcmd1bWVudHMpO1xuICB9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2N0eC5qc1xuLy8gbW9kdWxlIGlkID0gMTNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLy8gNy4xLjEgVG9QcmltaXRpdmUoaW5wdXQgWywgUHJlZmVycmVkVHlwZV0pXG52YXIgaXNPYmplY3QgPSByZXF1aXJlKCcuL19pcy1vYmplY3QnKTtcbi8vIGluc3RlYWQgb2YgdGhlIEVTNiBzcGVjIHZlcnNpb24sIHdlIGRpZG4ndCBpbXBsZW1lbnQgQEB0b1ByaW1pdGl2ZSBjYXNlXG4vLyBhbmQgdGhlIHNlY29uZCBhcmd1bWVudCAtIGZsYWcgLSBwcmVmZXJyZWQgdHlwZSBpcyBhIHN0cmluZ1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCwgUyl7XG4gIGlmKCFpc09iamVjdChpdCkpcmV0dXJuIGl0O1xuICB2YXIgZm4sIHZhbDtcbiAgaWYoUyAmJiB0eXBlb2YgKGZuID0gaXQudG9TdHJpbmcpID09ICdmdW5jdGlvbicgJiYgIWlzT2JqZWN0KHZhbCA9IGZuLmNhbGwoaXQpKSlyZXR1cm4gdmFsO1xuICBpZih0eXBlb2YgKGZuID0gaXQudmFsdWVPZikgPT0gJ2Z1bmN0aW9uJyAmJiAhaXNPYmplY3QodmFsID0gZm4uY2FsbChpdCkpKXJldHVybiB2YWw7XG4gIGlmKCFTICYmIHR5cGVvZiAoZm4gPSBpdC50b1N0cmluZykgPT0gJ2Z1bmN0aW9uJyAmJiAhaXNPYmplY3QodmFsID0gZm4uY2FsbChpdCkpKXJldHVybiB2YWw7XG4gIHRocm93IFR5cGVFcnJvcihcIkNhbid0IGNvbnZlcnQgb2JqZWN0IHRvIHByaW1pdGl2ZSB2YWx1ZVwiKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1wcmltaXRpdmUuanNcbi8vIG1vZHVsZSBpZCA9IDE0XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpXG4gICwgZG9jdW1lbnQgPSByZXF1aXJlKCcuL19nbG9iYWwnKS5kb2N1bWVudFxuICAvLyBpbiBvbGQgSUUgdHlwZW9mIGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQgaXMgJ29iamVjdCdcbiAgLCBpcyA9IGlzT2JqZWN0KGRvY3VtZW50KSAmJiBpc09iamVjdChkb2N1bWVudC5jcmVhdGVFbGVtZW50KTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gaXMgPyBkb2N1bWVudC5jcmVhdGVFbGVtZW50KGl0KSA6IHt9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2RvbS1jcmVhdGUuanNcbi8vIG1vZHVsZSBpZCA9IDE2XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gIXJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJykgJiYgIXJlcXVpcmUoJy4vX2ZhaWxzJykoZnVuY3Rpb24oKXtcbiAgcmV0dXJuIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShyZXF1aXJlKCcuL19kb20tY3JlYXRlJykoJ2RpdicpLCAnYScsIHtnZXQ6IGZ1bmN0aW9uKCl7IHJldHVybiA3OyB9fSkuYSAhPSA3O1xufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pZTgtZG9tLWRlZmluZS5qc1xuLy8gbW9kdWxlIGlkID0gMTdcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIGlmKHR5cGVvZiBpdCAhPSAnZnVuY3Rpb24nKXRocm93IFR5cGVFcnJvcihpdCArICcgaXMgbm90IGEgZnVuY3Rpb24hJyk7XG4gIHJldHVybiBpdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hLWZ1bmN0aW9uLmpzXG4vLyBtb2R1bGUgaWQgPSAxOFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eVwiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qc1xuLy8gbW9kdWxlIGlkID0gMTlcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczYub2JqZWN0LmRlZmluZS1wcm9wZXJ0eScpO1xudmFyICRPYmplY3QgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuT2JqZWN0O1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0eShpdCwga2V5LCBkZXNjKXtcbiAgcmV0dXJuICRPYmplY3QuZGVmaW5lUHJvcGVydHkoaXQsIGtleSwgZGVzYyk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qc1xuLy8gbW9kdWxlIGlkID0gMjBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyICRleHBvcnQgPSByZXF1aXJlKCcuL19leHBvcnQnKTtcbi8vIDE5LjEuMi40IC8gMTUuMi4zLjYgT2JqZWN0LmRlZmluZVByb3BlcnR5KE8sIFAsIEF0dHJpYnV0ZXMpXG4kZXhwb3J0KCRleHBvcnQuUyArICRleHBvcnQuRiAqICFyZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpLCAnT2JqZWN0Jywge2RlZmluZVByb3BlcnR5OiByZXF1aXJlKCcuL19vYmplY3QtZHAnKS5mfSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAyMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyB0byBpbmRleGVkIG9iamVjdCwgdG9PYmplY3Qgd2l0aCBmYWxsYmFjayBmb3Igbm9uLWFycmF5LWxpa2UgRVMzIHN0cmluZ3NcbnZhciBJT2JqZWN0ID0gcmVxdWlyZSgnLi9faW9iamVjdCcpXG4gICwgZGVmaW5lZCA9IHJlcXVpcmUoJy4vX2RlZmluZWQnKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gSU9iamVjdChkZWZpbmVkKGl0KSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8taW9iamVjdC5qc1xuLy8gbW9kdWxlIGlkID0gMjJcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJ2YXIgc3RvcmUgICAgICA9IHJlcXVpcmUoJy4vX3NoYXJlZCcpKCd3a3MnKVxuICAsIHVpZCAgICAgICAgPSByZXF1aXJlKCcuL191aWQnKVxuICAsIFN5bWJvbCAgICAgPSByZXF1aXJlKCcuL19nbG9iYWwnKS5TeW1ib2xcbiAgLCBVU0VfU1lNQk9MID0gdHlwZW9mIFN5bWJvbCA9PSAnZnVuY3Rpb24nO1xuXG52YXIgJGV4cG9ydHMgPSBtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKG5hbWUpe1xuICByZXR1cm4gc3RvcmVbbmFtZV0gfHwgKHN0b3JlW25hbWVdID1cbiAgICBVU0VfU1lNQk9MICYmIFN5bWJvbFtuYW1lXSB8fCAoVVNFX1NZTUJPTCA/IFN5bWJvbCA6IHVpZCkoJ1N5bWJvbC4nICsgbmFtZSkpO1xufTtcblxuJGV4cG9ydHMuc3RvcmUgPSBzdG9yZTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3drcy5qc1xuLy8gbW9kdWxlIGlkID0gMjNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IDE0IiwidmFyIGhhc093blByb3BlcnR5ID0ge30uaGFzT3duUHJvcGVydHk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0LCBrZXkpe1xuICByZXR1cm4gaGFzT3duUHJvcGVydHkuY2FsbChpdCwga2V5KTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oYXMuanNcbi8vIG1vZHVsZSBpZCA9IDI1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLy8gMTkuMS4yLjE0IC8gMTUuMi4zLjE0IE9iamVjdC5rZXlzKE8pXG52YXIgJGtleXMgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3Qta2V5cy1pbnRlcm5hbCcpXG4gICwgZW51bUJ1Z0tleXMgPSByZXF1aXJlKCcuL19lbnVtLWJ1Zy1rZXlzJyk7XG5cbm1vZHVsZS5leHBvcnRzID0gT2JqZWN0LmtleXMgfHwgZnVuY3Rpb24ga2V5cyhPKXtcbiAgcmV0dXJuICRrZXlzKE8sIGVudW1CdWdLZXlzKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3Qta2V5cy5qc1xuLy8gbW9kdWxlIGlkID0gMzNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCIvLyA3LjIuMSBSZXF1aXJlT2JqZWN0Q29lcmNpYmxlKGFyZ3VtZW50KVxubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIGlmKGl0ID09IHVuZGVmaW5lZCl0aHJvdyBUeXBlRXJyb3IoXCJDYW4ndCBjYWxsIG1ldGhvZCBvbiAgXCIgKyBpdCk7XG4gIHJldHVybiBpdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kZWZpbmVkLmpzXG4vLyBtb2R1bGUgaWQgPSAzNVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIi8vIDcuMS40IFRvSW50ZWdlclxudmFyIGNlaWwgID0gTWF0aC5jZWlsXG4gICwgZmxvb3IgPSBNYXRoLmZsb29yO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIHJldHVybiBpc05hTihpdCA9ICtpdCkgPyAwIDogKGl0ID4gMCA/IGZsb29yIDogY2VpbCkoaXQpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLWludGVnZXIuanNcbi8vIG1vZHVsZSBpZCA9IDM2XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgRXZlbnRFbWl0dGVyQ2xhc3MgZnJvbSAnZXZlbnRzJztcblxuLyoqXG4gKiBXZSBpbnN0YW5jaWF0ZSBvbmUgRXZlbnRFbWl0dGVyIChyZXN0cmljdGVkIHZpYSBhIGNvbnN0KSBzbyB0aGF0IGV2ZXJ5IGNvbXBvbmVudHNcbiAqIHJlZ2lzdGVyL2Rpc3BhdGNoIG9uIHRoZSBzYW1lIG9uZSBhbmQgY2FuIGNvbW11bmljYXRlIHdpdGggZWFjaCBvdGhlci5cbiAqL1xuZXhwb3J0IGNvbnN0IEV2ZW50RW1pdHRlciA9IG5ldyBFdmVudEVtaXR0ZXJDbGFzcygpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ldmVudC1lbWl0dGVyLmpzIiwidmFyIGlkID0gMFxuICAsIHB4ID0gTWF0aC5yYW5kb20oKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oa2V5KXtcbiAgcmV0dXJuICdTeW1ib2woJy5jb25jYXQoa2V5ID09PSB1bmRlZmluZWQgPyAnJyA6IGtleSwgJylfJywgKCsraWQgKyBweCkudG9TdHJpbmcoMzYpKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL191aWQuanNcbi8vIG1vZHVsZSBpZCA9IDQwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLy8gNy4xLjEzIFRvT2JqZWN0KGFyZ3VtZW50KVxudmFyIGRlZmluZWQgPSByZXF1aXJlKCcuL19kZWZpbmVkJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIE9iamVjdChkZWZpbmVkKGl0KSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8tb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSA0NFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsInZhciBzaGFyZWQgPSByZXF1aXJlKCcuL19zaGFyZWQnKSgna2V5cycpXG4gICwgdWlkICAgID0gcmVxdWlyZSgnLi9fdWlkJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGtleSl7XG4gIHJldHVybiBzaGFyZWRba2V5XSB8fCAoc2hhcmVkW2tleV0gPSB1aWQoa2V5KSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fc2hhcmVkLWtleS5qc1xuLy8gbW9kdWxlIGlkID0gNDVcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDb25maXJtTW9kYWwgY29tcG9uZW50XG4gKlxuICogQHBhcmFtIHtTdHJpbmd9IGlkXG4gKiBAcGFyYW0ge1N0cmluZ30gY29uZmlybVRpdGxlXG4gKiBAcGFyYW0ge1N0cmluZ30gY29uZmlybU1lc3NhZ2VcbiAqIEBwYXJhbSB7U3RyaW5nfSBjbG9zZUJ1dHRvbkxhYmVsXG4gKiBAcGFyYW0ge1N0cmluZ30gY29uZmlybUJ1dHRvbkxhYmVsXG4gKiBAcGFyYW0ge1N0cmluZ30gY29uZmlybUJ1dHRvbkNsYXNzXG4gKiBAcGFyYW0ge0Jvb2xlYW59IGNsb3NhYmxlXG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSBjb25maXJtQ2FsbGJhY2tcbiAqXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIENvbmZpcm1Nb2RhbChwYXJhbXMsIGNvbmZpcm1DYWxsYmFjaykge1xuICAvLyBDb25zdHJ1Y3QgdGhlIG1vZGFsXG4gIGNvbnN0IHtpZCwgY2xvc2FibGV9ID0gcGFyYW1zO1xuICB0aGlzLm1vZGFsID0gTW9kYWwocGFyYW1zKTtcblxuICAvLyBqUXVlcnkgbW9kYWwgb2JqZWN0XG4gIHRoaXMuJG1vZGFsID0gJCh0aGlzLm1vZGFsLmNvbnRhaW5lcik7XG5cbiAgdGhpcy5zaG93ID0gKCkgPT4ge1xuICAgIHRoaXMuJG1vZGFsLm1vZGFsKCk7XG4gIH07XG5cbiAgdGhpcy5tb2RhbC5jb25maXJtQnV0dG9uLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgY29uZmlybUNhbGxiYWNrKTtcblxuICB0aGlzLiRtb2RhbC5tb2RhbCh7XG4gICAgYmFja2Ryb3A6IChjbG9zYWJsZSA/IHRydWUgOiAnc3RhdGljJyksXG4gICAga2V5Ym9hcmQ6IGNsb3NhYmxlICE9PSB1bmRlZmluZWQgPyBjbG9zYWJsZSA6IHRydWUsXG4gICAgY2xvc2FibGU6IGNsb3NhYmxlICE9PSB1bmRlZmluZWQgPyBjbG9zYWJsZSA6IHRydWUsXG4gICAgc2hvdzogZmFsc2UsXG4gIH0pO1xuXG4gIHRoaXMuJG1vZGFsLm9uKCdoaWRkZW4uYnMubW9kYWwnLCAoKSA9PiB7XG4gICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvcihgIyR7aWR9YCkucmVtb3ZlKCk7XG4gIH0pO1xuXG4gIGRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQodGhpcy5tb2RhbC5jb250YWluZXIpO1xufVxuXG4vKipcbiAqIE1vZGFsIGNvbXBvbmVudCB0byBpbXByb3ZlIGxpc2liaWxpdHkgYnkgY29uc3RydWN0aW5nIHRoZSBtb2RhbCBvdXRzaWRlIHRoZSBtYWluIGZ1bmN0aW9uXG4gKlxuICogQHBhcmFtIHtPYmplY3R9IHBhcmFtc1xuICpcbiAqL1xuZnVuY3Rpb24gTW9kYWwoXG4gIHtcbiAgICBpZCA9ICdjb25maXJtX21vZGFsJyxcbiAgICBjb25maXJtVGl0bGUsXG4gICAgY29uZmlybU1lc3NhZ2UgPSAnJyxcbiAgICBjbG9zZUJ1dHRvbkxhYmVsID0gJ0Nsb3NlJyxcbiAgICBjb25maXJtQnV0dG9uTGFiZWwgPSAnQWNjZXB0JyxcbiAgICBjb25maXJtQnV0dG9uQ2xhc3MgPSAnYnRuLXByaW1hcnknLFxuICB9KSB7XG4gIGNvbnN0IG1vZGFsID0ge307XG5cbiAgLy8gTWFpbiBtb2RhbCBlbGVtZW50XG4gIG1vZGFsLmNvbnRhaW5lciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICBtb2RhbC5jb250YWluZXIuY2xhc3NMaXN0LmFkZCgnbW9kYWwnLCAnZmFkZScpO1xuICBtb2RhbC5jb250YWluZXIuaWQgPSBpZDtcblxuICAvLyBNb2RhbCBkaWFsb2cgZWxlbWVudFxuICBtb2RhbC5kaWFsb2cgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuZGlhbG9nLmNsYXNzTGlzdC5hZGQoJ21vZGFsLWRpYWxvZycpO1xuXG4gIC8vIE1vZGFsIGNvbnRlbnQgZWxlbWVudFxuICBtb2RhbC5jb250ZW50ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gIG1vZGFsLmNvbnRlbnQuY2xhc3NMaXN0LmFkZCgnbW9kYWwtY29udGVudCcpO1xuXG4gIC8vIE1vZGFsIGhlYWRlciBlbGVtZW50XG4gIG1vZGFsLmhlYWRlciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICBtb2RhbC5oZWFkZXIuY2xhc3NMaXN0LmFkZCgnbW9kYWwtaGVhZGVyJyk7XG5cbiAgLy8gTW9kYWwgdGl0bGUgZWxlbWVudFxuICBpZiAoY29uZmlybVRpdGxlKSB7XG4gICAgbW9kYWwudGl0bGUgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdoNCcpO1xuICAgIG1vZGFsLnRpdGxlLmNsYXNzTGlzdC5hZGQoJ21vZGFsLXRpdGxlJyk7XG4gICAgbW9kYWwudGl0bGUuaW5uZXJIVE1MID0gY29uZmlybVRpdGxlO1xuICB9XG5cbiAgLy8gTW9kYWwgY2xvc2UgYnV0dG9uIGljb25cbiAgbW9kYWwuY2xvc2VJY29uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYnV0dG9uJyk7XG4gIG1vZGFsLmNsb3NlSWNvbi5jbGFzc0xpc3QuYWRkKCdjbG9zZScpO1xuICBtb2RhbC5jbG9zZUljb24uc2V0QXR0cmlidXRlKCd0eXBlJywgJ2J1dHRvbicpO1xuICBtb2RhbC5jbG9zZUljb24uZGF0YXNldC5kaXNtaXNzID0gJ21vZGFsJztcbiAgbW9kYWwuY2xvc2VJY29uLmlubmVySFRNTCA9ICfDlyc7XG5cbiAgLy8gTW9kYWwgYm9keSBlbGVtZW50XG4gIG1vZGFsLmJvZHkgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuYm9keS5jbGFzc0xpc3QuYWRkKCdtb2RhbC1ib2R5JywgJ3RleHQtbGVmdCcsICdmb250LXdlaWdodC1ub3JtYWwnKTtcblxuICAvLyBNb2RhbCBtZXNzYWdlIGVsZW1lbnRcbiAgbW9kYWwubWVzc2FnZSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ3AnKTtcbiAgbW9kYWwubWVzc2FnZS5jbGFzc0xpc3QuYWRkKCdjb25maXJtLW1lc3NhZ2UnKTtcbiAgbW9kYWwubWVzc2FnZS5pbm5lckhUTUwgPSBjb25maXJtTWVzc2FnZTtcblxuICAvLyBNb2RhbCBmb290ZXIgZWxlbWVudFxuICBtb2RhbC5mb290ZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuZm9vdGVyLmNsYXNzTGlzdC5hZGQoJ21vZGFsLWZvb3RlcicpO1xuXG4gIC8vIE1vZGFsIGNsb3NlIGJ1dHRvbiBlbGVtZW50XG4gIG1vZGFsLmNsb3NlQnV0dG9uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYnV0dG9uJyk7XG4gIG1vZGFsLmNsb3NlQnV0dG9uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgbW9kYWwuY2xvc2VCdXR0b24uY2xhc3NMaXN0LmFkZCgnYnRuJywgJ2J0bi1vdXRsaW5lLXNlY29uZGFyeScsICdidG4tbGcnKTtcbiAgbW9kYWwuY2xvc2VCdXR0b24uZGF0YXNldC5kaXNtaXNzID0gJ21vZGFsJztcbiAgbW9kYWwuY2xvc2VCdXR0b24uaW5uZXJIVE1MID0gY2xvc2VCdXR0b25MYWJlbDtcblxuICAvLyBNb2RhbCBjbG9zZSBidXR0b24gZWxlbWVudFxuICBtb2RhbC5jb25maXJtQnV0dG9uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYnV0dG9uJyk7XG4gIG1vZGFsLmNvbmZpcm1CdXR0b24uc2V0QXR0cmlidXRlKCd0eXBlJywgJ2J1dHRvbicpO1xuICBtb2RhbC5jb25maXJtQnV0dG9uLmNsYXNzTGlzdC5hZGQoJ2J0bicsIGNvbmZpcm1CdXR0b25DbGFzcywgJ2J0bi1sZycsICdidG4tY29uZmlybS1zdWJtaXQnKTtcbiAgbW9kYWwuY29uZmlybUJ1dHRvbi5kYXRhc2V0LmRpc21pc3MgPSAnbW9kYWwnO1xuICBtb2RhbC5jb25maXJtQnV0dG9uLmlubmVySFRNTCA9IGNvbmZpcm1CdXR0b25MYWJlbDtcblxuICAvLyBDb25zdHJ1Y3RpbmcgdGhlIG1vZGFsXG4gIGlmIChjb25maXJtVGl0bGUpIHtcbiAgICBtb2RhbC5oZWFkZXIuYXBwZW5kKG1vZGFsLnRpdGxlLCBtb2RhbC5jbG9zZUljb24pO1xuICB9IGVsc2Uge1xuICAgIG1vZGFsLmhlYWRlci5hcHBlbmRDaGlsZChtb2RhbC5jbG9zZUljb24pO1xuICB9XG5cbiAgbW9kYWwuYm9keS5hcHBlbmRDaGlsZChtb2RhbC5tZXNzYWdlKTtcbiAgbW9kYWwuZm9vdGVyLmFwcGVuZChtb2RhbC5jbG9zZUJ1dHRvbiwgbW9kYWwuY29uZmlybUJ1dHRvbik7XG4gIG1vZGFsLmNvbnRlbnQuYXBwZW5kKG1vZGFsLmhlYWRlciwgbW9kYWwuYm9keSwgbW9kYWwuZm9vdGVyKTtcbiAgbW9kYWwuZGlhbG9nLmFwcGVuZENoaWxkKG1vZGFsLmNvbnRlbnQpO1xuICBtb2RhbC5jb250YWluZXIuYXBwZW5kQ2hpbGQobW9kYWwuZGlhbG9nKTtcblxuICByZXR1cm4gbW9kYWw7XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL21vZGFsLmpzIiwidmFyIHRvU3RyaW5nID0ge30udG9TdHJpbmc7XG5cbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gdG9TdHJpbmcuY2FsbChpdCkuc2xpY2UoOCwgLTEpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvZi5qc1xuLy8gbW9kdWxlIGlkID0gNDhcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCIvLyBJRSA4LSBkb24ndCBlbnVtIGJ1ZyBrZXlzXG5tb2R1bGUuZXhwb3J0cyA9IChcbiAgJ2NvbnN0cnVjdG9yLGhhc093blByb3BlcnR5LGlzUHJvdG90eXBlT2YscHJvcGVydHlJc0VudW1lcmFibGUsdG9Mb2NhbGVTdHJpbmcsdG9TdHJpbmcsdmFsdWVPZidcbikuc3BsaXQoJywnKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2VudW0tYnVnLWtleXMuanNcbi8vIG1vZHVsZSBpZCA9IDQ5XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwidmFyIGdsb2JhbCA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpXG4gICwgU0hBUkVEID0gJ19fY29yZS1qc19zaGFyZWRfXydcbiAgLCBzdG9yZSAgPSBnbG9iYWxbU0hBUkVEXSB8fCAoZ2xvYmFsW1NIQVJFRF0gPSB7fSk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGtleSl7XG4gIHJldHVybiBzdG9yZVtrZXldIHx8IChzdG9yZVtrZXldID0ge30pO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NoYXJlZC5qc1xuLy8gbW9kdWxlIGlkID0gNTBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJtb2R1bGUuZXhwb3J0cyA9IHt9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXRlcmF0b3JzLmpzXG4vLyBtb2R1bGUgaWQgPSA1MVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA4IDkgMTQiLCIvLyBmYWxsYmFjayBmb3Igbm9uLWFycmF5LWxpa2UgRVMzIGFuZCBub24tZW51bWVyYWJsZSBvbGQgVjggc3RyaW5nc1xudmFyIGNvZiA9IHJlcXVpcmUoJy4vX2NvZicpO1xubW9kdWxlLmV4cG9ydHMgPSBPYmplY3QoJ3onKS5wcm9wZXJ0eUlzRW51bWVyYWJsZSgwKSA/IE9iamVjdCA6IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGNvZihpdCkgPT0gJ1N0cmluZycgPyBpdC5zcGxpdCgnJykgOiBPYmplY3QoaXQpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2lvYmplY3QuanNcbi8vIG1vZHVsZSBpZCA9IDUyXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLy8gNy4xLjE1IFRvTGVuZ3RoXG52YXIgdG9JbnRlZ2VyID0gcmVxdWlyZSgnLi9fdG8taW50ZWdlcicpXG4gICwgbWluICAgICAgID0gTWF0aC5taW47XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGl0ID4gMCA/IG1pbih0b0ludGVnZXIoaXQpLCAweDFmZmZmZmZmZmZmZmZmKSA6IDA7IC8vIHBvdygyLCA1MykgLSAxID09IDkwMDcxOTkyNTQ3NDA5OTFcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1sZW5ndGguanNcbi8vIG1vZHVsZSBpZCA9IDUzXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiZXhwb3J0cy5mID0ge30ucHJvcGVydHlJc0VudW1lcmFibGU7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtcGllLmpzXG4vLyBtb2R1bGUgaWQgPSA1NFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTUgMTYgMTgiLCJ2YXIgaGFzICAgICAgICAgID0gcmVxdWlyZSgnLi9faGFzJylcbiAgLCB0b0lPYmplY3QgICAgPSByZXF1aXJlKCcuL190by1pb2JqZWN0JylcbiAgLCBhcnJheUluZGV4T2YgPSByZXF1aXJlKCcuL19hcnJheS1pbmNsdWRlcycpKGZhbHNlKVxuICAsIElFX1BST1RPICAgICA9IHJlcXVpcmUoJy4vX3NoYXJlZC1rZXknKSgnSUVfUFJPVE8nKTtcblxubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihvYmplY3QsIG5hbWVzKXtcbiAgdmFyIE8gICAgICA9IHRvSU9iamVjdChvYmplY3QpXG4gICAgLCBpICAgICAgPSAwXG4gICAgLCByZXN1bHQgPSBbXVxuICAgICwga2V5O1xuICBmb3Ioa2V5IGluIE8paWYoa2V5ICE9IElFX1BST1RPKWhhcyhPLCBrZXkpICYmIHJlc3VsdC5wdXNoKGtleSk7XG4gIC8vIERvbid0IGVudW0gYnVnICYgaGlkZGVuIGtleXNcbiAgd2hpbGUobmFtZXMubGVuZ3RoID4gaSlpZihoYXMoTywga2V5ID0gbmFtZXNbaSsrXSkpe1xuICAgIH5hcnJheUluZGV4T2YocmVzdWx0LCBrZXkpIHx8IHJlc3VsdC5wdXNoKGtleSk7XG4gIH1cbiAgcmV0dXJuIHJlc3VsdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3Qta2V5cy1pbnRlcm5hbC5qc1xuLy8gbW9kdWxlIGlkID0gNTVcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCIvLyBDb3B5cmlnaHQgSm95ZW50LCBJbmMuIGFuZCBvdGhlciBOb2RlIGNvbnRyaWJ1dG9ycy5cbi8vXG4vLyBQZXJtaXNzaW9uIGlzIGhlcmVieSBncmFudGVkLCBmcmVlIG9mIGNoYXJnZSwgdG8gYW55IHBlcnNvbiBvYnRhaW5pbmcgYVxuLy8gY29weSBvZiB0aGlzIHNvZnR3YXJlIGFuZCBhc3NvY2lhdGVkIGRvY3VtZW50YXRpb24gZmlsZXMgKHRoZVxuLy8gXCJTb2Z0d2FyZVwiKSwgdG8gZGVhbCBpbiB0aGUgU29mdHdhcmUgd2l0aG91dCByZXN0cmljdGlvbiwgaW5jbHVkaW5nXG4vLyB3aXRob3V0IGxpbWl0YXRpb24gdGhlIHJpZ2h0cyB0byB1c2UsIGNvcHksIG1vZGlmeSwgbWVyZ2UsIHB1Ymxpc2gsXG4vLyBkaXN0cmlidXRlLCBzdWJsaWNlbnNlLCBhbmQvb3Igc2VsbCBjb3BpZXMgb2YgdGhlIFNvZnR3YXJlLCBhbmQgdG8gcGVybWl0XG4vLyBwZXJzb25zIHRvIHdob20gdGhlIFNvZnR3YXJlIGlzIGZ1cm5pc2hlZCB0byBkbyBzbywgc3ViamVjdCB0byB0aGVcbi8vIGZvbGxvd2luZyBjb25kaXRpb25zOlxuLy9cbi8vIFRoZSBhYm92ZSBjb3B5cmlnaHQgbm90aWNlIGFuZCB0aGlzIHBlcm1pc3Npb24gbm90aWNlIHNoYWxsIGJlIGluY2x1ZGVkXG4vLyBpbiBhbGwgY29waWVzIG9yIHN1YnN0YW50aWFsIHBvcnRpb25zIG9mIHRoZSBTb2Z0d2FyZS5cbi8vXG4vLyBUSEUgU09GVFdBUkUgSVMgUFJPVklERUQgXCJBUyBJU1wiLCBXSVRIT1VUIFdBUlJBTlRZIE9GIEFOWSBLSU5ELCBFWFBSRVNTXG4vLyBPUiBJTVBMSUVELCBJTkNMVURJTkcgQlVUIE5PVCBMSU1JVEVEIFRPIFRIRSBXQVJSQU5USUVTIE9GXG4vLyBNRVJDSEFOVEFCSUxJVFksIEZJVE5FU1MgRk9SIEEgUEFSVElDVUxBUiBQVVJQT1NFIEFORCBOT05JTkZSSU5HRU1FTlQuIElOXG4vLyBOTyBFVkVOVCBTSEFMTCBUSEUgQVVUSE9SUyBPUiBDT1BZUklHSFQgSE9MREVSUyBCRSBMSUFCTEUgRk9SIEFOWSBDTEFJTSxcbi8vIERBTUFHRVMgT1IgT1RIRVIgTElBQklMSVRZLCBXSEVUSEVSIElOIEFOIEFDVElPTiBPRiBDT05UUkFDVCwgVE9SVCBPUlxuLy8gT1RIRVJXSVNFLCBBUklTSU5HIEZST00sIE9VVCBPRiBPUiBJTiBDT05ORUNUSU9OIFdJVEggVEhFIFNPRlRXQVJFIE9SIFRIRVxuLy8gVVNFIE9SIE9USEVSIERFQUxJTkdTIElOIFRIRSBTT0ZUV0FSRS5cblxuJ3VzZSBzdHJpY3QnO1xuXG52YXIgUiA9IHR5cGVvZiBSZWZsZWN0ID09PSAnb2JqZWN0JyA/IFJlZmxlY3QgOiBudWxsXG52YXIgUmVmbGVjdEFwcGx5ID0gUiAmJiB0eXBlb2YgUi5hcHBseSA9PT0gJ2Z1bmN0aW9uJ1xuICA/IFIuYXBwbHlcbiAgOiBmdW5jdGlvbiBSZWZsZWN0QXBwbHkodGFyZ2V0LCByZWNlaXZlciwgYXJncykge1xuICAgIHJldHVybiBGdW5jdGlvbi5wcm90b3R5cGUuYXBwbHkuY2FsbCh0YXJnZXQsIHJlY2VpdmVyLCBhcmdzKTtcbiAgfVxuXG52YXIgUmVmbGVjdE93bktleXNcbmlmIChSICYmIHR5cGVvZiBSLm93bktleXMgPT09ICdmdW5jdGlvbicpIHtcbiAgUmVmbGVjdE93bktleXMgPSBSLm93bktleXNcbn0gZWxzZSBpZiAoT2JqZWN0LmdldE93blByb3BlcnR5U3ltYm9scykge1xuICBSZWZsZWN0T3duS2V5cyA9IGZ1bmN0aW9uIFJlZmxlY3RPd25LZXlzKHRhcmdldCkge1xuICAgIHJldHVybiBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyh0YXJnZXQpXG4gICAgICAuY29uY2F0KE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHModGFyZ2V0KSk7XG4gIH07XG59IGVsc2Uge1xuICBSZWZsZWN0T3duS2V5cyA9IGZ1bmN0aW9uIFJlZmxlY3RPd25LZXlzKHRhcmdldCkge1xuICAgIHJldHVybiBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyh0YXJnZXQpO1xuICB9O1xufVxuXG5mdW5jdGlvbiBQcm9jZXNzRW1pdFdhcm5pbmcod2FybmluZykge1xuICBpZiAoY29uc29sZSAmJiBjb25zb2xlLndhcm4pIGNvbnNvbGUud2Fybih3YXJuaW5nKTtcbn1cblxudmFyIE51bWJlcklzTmFOID0gTnVtYmVyLmlzTmFOIHx8IGZ1bmN0aW9uIE51bWJlcklzTmFOKHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSAhPT0gdmFsdWU7XG59XG5cbmZ1bmN0aW9uIEV2ZW50RW1pdHRlcigpIHtcbiAgRXZlbnRFbWl0dGVyLmluaXQuY2FsbCh0aGlzKTtcbn1cbm1vZHVsZS5leHBvcnRzID0gRXZlbnRFbWl0dGVyO1xuXG4vLyBCYWNrd2FyZHMtY29tcGF0IHdpdGggbm9kZSAwLjEwLnhcbkV2ZW50RW1pdHRlci5FdmVudEVtaXR0ZXIgPSBFdmVudEVtaXR0ZXI7XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX2V2ZW50cyA9IHVuZGVmaW5lZDtcbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX2V2ZW50c0NvdW50ID0gMDtcbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX21heExpc3RlbmVycyA9IHVuZGVmaW5lZDtcblxuLy8gQnkgZGVmYXVsdCBFdmVudEVtaXR0ZXJzIHdpbGwgcHJpbnQgYSB3YXJuaW5nIGlmIG1vcmUgdGhhbiAxMCBsaXN0ZW5lcnMgYXJlXG4vLyBhZGRlZCB0byBpdC4gVGhpcyBpcyBhIHVzZWZ1bCBkZWZhdWx0IHdoaWNoIGhlbHBzIGZpbmRpbmcgbWVtb3J5IGxlYWtzLlxudmFyIGRlZmF1bHRNYXhMaXN0ZW5lcnMgPSAxMDtcblxuT2JqZWN0LmRlZmluZVByb3BlcnR5KEV2ZW50RW1pdHRlciwgJ2RlZmF1bHRNYXhMaXN0ZW5lcnMnLCB7XG4gIGVudW1lcmFibGU6IHRydWUsXG4gIGdldDogZnVuY3Rpb24oKSB7XG4gICAgcmV0dXJuIGRlZmF1bHRNYXhMaXN0ZW5lcnM7XG4gIH0sXG4gIHNldDogZnVuY3Rpb24oYXJnKSB7XG4gICAgaWYgKHR5cGVvZiBhcmcgIT09ICdudW1iZXInIHx8IGFyZyA8IDAgfHwgTnVtYmVySXNOYU4oYXJnKSkge1xuICAgICAgdGhyb3cgbmV3IFJhbmdlRXJyb3IoJ1RoZSB2YWx1ZSBvZiBcImRlZmF1bHRNYXhMaXN0ZW5lcnNcIiBpcyBvdXQgb2YgcmFuZ2UuIEl0IG11c3QgYmUgYSBub24tbmVnYXRpdmUgbnVtYmVyLiBSZWNlaXZlZCAnICsgYXJnICsgJy4nKTtcbiAgICB9XG4gICAgZGVmYXVsdE1heExpc3RlbmVycyA9IGFyZztcbiAgfVxufSk7XG5cbkV2ZW50RW1pdHRlci5pbml0ID0gZnVuY3Rpb24oKSB7XG5cbiAgaWYgKHRoaXMuX2V2ZW50cyA9PT0gdW5kZWZpbmVkIHx8XG4gICAgICB0aGlzLl9ldmVudHMgPT09IE9iamVjdC5nZXRQcm90b3R5cGVPZih0aGlzKS5fZXZlbnRzKSB7XG4gICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICB0aGlzLl9ldmVudHNDb3VudCA9IDA7XG4gIH1cblxuICB0aGlzLl9tYXhMaXN0ZW5lcnMgPSB0aGlzLl9tYXhMaXN0ZW5lcnMgfHwgdW5kZWZpbmVkO1xufTtcblxuLy8gT2J2aW91c2x5IG5vdCBhbGwgRW1pdHRlcnMgc2hvdWxkIGJlIGxpbWl0ZWQgdG8gMTAuIFRoaXMgZnVuY3Rpb24gYWxsb3dzXG4vLyB0aGF0IHRvIGJlIGluY3JlYXNlZC4gU2V0IHRvIHplcm8gZm9yIHVubGltaXRlZC5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuc2V0TWF4TGlzdGVuZXJzID0gZnVuY3Rpb24gc2V0TWF4TGlzdGVuZXJzKG4pIHtcbiAgaWYgKHR5cGVvZiBuICE9PSAnbnVtYmVyJyB8fCBuIDwgMCB8fCBOdW1iZXJJc05hTihuKSkge1xuICAgIHRocm93IG5ldyBSYW5nZUVycm9yKCdUaGUgdmFsdWUgb2YgXCJuXCIgaXMgb3V0IG9mIHJhbmdlLiBJdCBtdXN0IGJlIGEgbm9uLW5lZ2F0aXZlIG51bWJlci4gUmVjZWl2ZWQgJyArIG4gKyAnLicpO1xuICB9XG4gIHRoaXMuX21heExpc3RlbmVycyA9IG47XG4gIHJldHVybiB0aGlzO1xufTtcblxuZnVuY3Rpb24gJGdldE1heExpc3RlbmVycyh0aGF0KSB7XG4gIGlmICh0aGF0Ll9tYXhMaXN0ZW5lcnMgPT09IHVuZGVmaW5lZClcbiAgICByZXR1cm4gRXZlbnRFbWl0dGVyLmRlZmF1bHRNYXhMaXN0ZW5lcnM7XG4gIHJldHVybiB0aGF0Ll9tYXhMaXN0ZW5lcnM7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZ2V0TWF4TGlzdGVuZXJzID0gZnVuY3Rpb24gZ2V0TWF4TGlzdGVuZXJzKCkge1xuICByZXR1cm4gJGdldE1heExpc3RlbmVycyh0aGlzKTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZW1pdCA9IGZ1bmN0aW9uIGVtaXQodHlwZSkge1xuICB2YXIgYXJncyA9IFtdO1xuICBmb3IgKHZhciBpID0gMTsgaSA8IGFyZ3VtZW50cy5sZW5ndGg7IGkrKykgYXJncy5wdXNoKGFyZ3VtZW50c1tpXSk7XG4gIHZhciBkb0Vycm9yID0gKHR5cGUgPT09ICdlcnJvcicpO1xuXG4gIHZhciBldmVudHMgPSB0aGlzLl9ldmVudHM7XG4gIGlmIChldmVudHMgIT09IHVuZGVmaW5lZClcbiAgICBkb0Vycm9yID0gKGRvRXJyb3IgJiYgZXZlbnRzLmVycm9yID09PSB1bmRlZmluZWQpO1xuICBlbHNlIGlmICghZG9FcnJvcilcbiAgICByZXR1cm4gZmFsc2U7XG5cbiAgLy8gSWYgdGhlcmUgaXMgbm8gJ2Vycm9yJyBldmVudCBsaXN0ZW5lciB0aGVuIHRocm93LlxuICBpZiAoZG9FcnJvcikge1xuICAgIHZhciBlcjtcbiAgICBpZiAoYXJncy5sZW5ndGggPiAwKVxuICAgICAgZXIgPSBhcmdzWzBdO1xuICAgIGlmIChlciBpbnN0YW5jZW9mIEVycm9yKSB7XG4gICAgICAvLyBOb3RlOiBUaGUgY29tbWVudHMgb24gdGhlIGB0aHJvd2AgbGluZXMgYXJlIGludGVudGlvbmFsLCB0aGV5IHNob3dcbiAgICAgIC8vIHVwIGluIE5vZGUncyBvdXRwdXQgaWYgdGhpcyByZXN1bHRzIGluIGFuIHVuaGFuZGxlZCBleGNlcHRpb24uXG4gICAgICB0aHJvdyBlcjsgLy8gVW5oYW5kbGVkICdlcnJvcicgZXZlbnRcbiAgICB9XG4gICAgLy8gQXQgbGVhc3QgZ2l2ZSBzb21lIGtpbmQgb2YgY29udGV4dCB0byB0aGUgdXNlclxuICAgIHZhciBlcnIgPSBuZXcgRXJyb3IoJ1VuaGFuZGxlZCBlcnJvci4nICsgKGVyID8gJyAoJyArIGVyLm1lc3NhZ2UgKyAnKScgOiAnJykpO1xuICAgIGVyci5jb250ZXh0ID0gZXI7XG4gICAgdGhyb3cgZXJyOyAvLyBVbmhhbmRsZWQgJ2Vycm9yJyBldmVudFxuICB9XG5cbiAgdmFyIGhhbmRsZXIgPSBldmVudHNbdHlwZV07XG5cbiAgaWYgKGhhbmRsZXIgPT09IHVuZGVmaW5lZClcbiAgICByZXR1cm4gZmFsc2U7XG5cbiAgaWYgKHR5cGVvZiBoYW5kbGVyID09PSAnZnVuY3Rpb24nKSB7XG4gICAgUmVmbGVjdEFwcGx5KGhhbmRsZXIsIHRoaXMsIGFyZ3MpO1xuICB9IGVsc2Uge1xuICAgIHZhciBsZW4gPSBoYW5kbGVyLmxlbmd0aDtcbiAgICB2YXIgbGlzdGVuZXJzID0gYXJyYXlDbG9uZShoYW5kbGVyLCBsZW4pO1xuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgbGVuOyArK2kpXG4gICAgICBSZWZsZWN0QXBwbHkobGlzdGVuZXJzW2ldLCB0aGlzLCBhcmdzKTtcbiAgfVxuXG4gIHJldHVybiB0cnVlO1xufTtcblxuZnVuY3Rpb24gX2FkZExpc3RlbmVyKHRhcmdldCwgdHlwZSwgbGlzdGVuZXIsIHByZXBlbmQpIHtcbiAgdmFyIG07XG4gIHZhciBldmVudHM7XG4gIHZhciBleGlzdGluZztcblxuICBpZiAodHlwZW9mIGxpc3RlbmVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVGhlIFwibGlzdGVuZXJcIiBhcmd1bWVudCBtdXN0IGJlIG9mIHR5cGUgRnVuY3Rpb24uIFJlY2VpdmVkIHR5cGUgJyArIHR5cGVvZiBsaXN0ZW5lcik7XG4gIH1cblxuICBldmVudHMgPSB0YXJnZXQuX2V2ZW50cztcbiAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKSB7XG4gICAgZXZlbnRzID0gdGFyZ2V0Ll9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgIHRhcmdldC5fZXZlbnRzQ291bnQgPSAwO1xuICB9IGVsc2Uge1xuICAgIC8vIFRvIGF2b2lkIHJlY3Vyc2lvbiBpbiB0aGUgY2FzZSB0aGF0IHR5cGUgPT09IFwibmV3TGlzdGVuZXJcIiEgQmVmb3JlXG4gICAgLy8gYWRkaW5nIGl0IHRvIHRoZSBsaXN0ZW5lcnMsIGZpcnN0IGVtaXQgXCJuZXdMaXN0ZW5lclwiLlxuICAgIGlmIChldmVudHMubmV3TGlzdGVuZXIgIT09IHVuZGVmaW5lZCkge1xuICAgICAgdGFyZ2V0LmVtaXQoJ25ld0xpc3RlbmVyJywgdHlwZSxcbiAgICAgICAgICAgICAgICAgIGxpc3RlbmVyLmxpc3RlbmVyID8gbGlzdGVuZXIubGlzdGVuZXIgOiBsaXN0ZW5lcik7XG5cbiAgICAgIC8vIFJlLWFzc2lnbiBgZXZlbnRzYCBiZWNhdXNlIGEgbmV3TGlzdGVuZXIgaGFuZGxlciBjb3VsZCBoYXZlIGNhdXNlZCB0aGVcbiAgICAgIC8vIHRoaXMuX2V2ZW50cyB0byBiZSBhc3NpZ25lZCB0byBhIG5ldyBvYmplY3RcbiAgICAgIGV2ZW50cyA9IHRhcmdldC5fZXZlbnRzO1xuICAgIH1cbiAgICBleGlzdGluZyA9IGV2ZW50c1t0eXBlXTtcbiAgfVxuXG4gIGlmIChleGlzdGluZyA9PT0gdW5kZWZpbmVkKSB7XG4gICAgLy8gT3B0aW1pemUgdGhlIGNhc2Ugb2Ygb25lIGxpc3RlbmVyLiBEb24ndCBuZWVkIHRoZSBleHRyYSBhcnJheSBvYmplY3QuXG4gICAgZXhpc3RpbmcgPSBldmVudHNbdHlwZV0gPSBsaXN0ZW5lcjtcbiAgICArK3RhcmdldC5fZXZlbnRzQ291bnQ7XG4gIH0gZWxzZSB7XG4gICAgaWYgKHR5cGVvZiBleGlzdGluZyA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgLy8gQWRkaW5nIHRoZSBzZWNvbmQgZWxlbWVudCwgbmVlZCB0byBjaGFuZ2UgdG8gYXJyYXkuXG4gICAgICBleGlzdGluZyA9IGV2ZW50c1t0eXBlXSA9XG4gICAgICAgIHByZXBlbmQgPyBbbGlzdGVuZXIsIGV4aXN0aW5nXSA6IFtleGlzdGluZywgbGlzdGVuZXJdO1xuICAgICAgLy8gSWYgd2UndmUgYWxyZWFkeSBnb3QgYW4gYXJyYXksIGp1c3QgYXBwZW5kLlxuICAgIH0gZWxzZSBpZiAocHJlcGVuZCkge1xuICAgICAgZXhpc3RpbmcudW5zaGlmdChsaXN0ZW5lcik7XG4gICAgfSBlbHNlIHtcbiAgICAgIGV4aXN0aW5nLnB1c2gobGlzdGVuZXIpO1xuICAgIH1cblxuICAgIC8vIENoZWNrIGZvciBsaXN0ZW5lciBsZWFrXG4gICAgbSA9ICRnZXRNYXhMaXN0ZW5lcnModGFyZ2V0KTtcbiAgICBpZiAobSA+IDAgJiYgZXhpc3RpbmcubGVuZ3RoID4gbSAmJiAhZXhpc3Rpbmcud2FybmVkKSB7XG4gICAgICBleGlzdGluZy53YXJuZWQgPSB0cnVlO1xuICAgICAgLy8gTm8gZXJyb3IgY29kZSBmb3IgdGhpcyBzaW5jZSBpdCBpcyBhIFdhcm5pbmdcbiAgICAgIC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBuby1yZXN0cmljdGVkLXN5bnRheFxuICAgICAgdmFyIHcgPSBuZXcgRXJyb3IoJ1Bvc3NpYmxlIEV2ZW50RW1pdHRlciBtZW1vcnkgbGVhayBkZXRlY3RlZC4gJyArXG4gICAgICAgICAgICAgICAgICAgICAgICAgIGV4aXN0aW5nLmxlbmd0aCArICcgJyArIFN0cmluZyh0eXBlKSArICcgbGlzdGVuZXJzICcgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAnYWRkZWQuIFVzZSBlbWl0dGVyLnNldE1heExpc3RlbmVycygpIHRvICcgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAnaW5jcmVhc2UgbGltaXQnKTtcbiAgICAgIHcubmFtZSA9ICdNYXhMaXN0ZW5lcnNFeGNlZWRlZFdhcm5pbmcnO1xuICAgICAgdy5lbWl0dGVyID0gdGFyZ2V0O1xuICAgICAgdy50eXBlID0gdHlwZTtcbiAgICAgIHcuY291bnQgPSBleGlzdGluZy5sZW5ndGg7XG4gICAgICBQcm9jZXNzRW1pdFdhcm5pbmcodyk7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIHRhcmdldDtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5hZGRMaXN0ZW5lciA9IGZ1bmN0aW9uIGFkZExpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gIHJldHVybiBfYWRkTGlzdGVuZXIodGhpcywgdHlwZSwgbGlzdGVuZXIsIGZhbHNlKTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUub24gPSBFdmVudEVtaXR0ZXIucHJvdG90eXBlLmFkZExpc3RlbmVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnByZXBlbmRMaXN0ZW5lciA9XG4gICAgZnVuY3Rpb24gcHJlcGVuZExpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gICAgICByZXR1cm4gX2FkZExpc3RlbmVyKHRoaXMsIHR5cGUsIGxpc3RlbmVyLCB0cnVlKTtcbiAgICB9O1xuXG5mdW5jdGlvbiBvbmNlV3JhcHBlcigpIHtcbiAgdmFyIGFyZ3MgPSBbXTtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCBhcmd1bWVudHMubGVuZ3RoOyBpKyspIGFyZ3MucHVzaChhcmd1bWVudHNbaV0pO1xuICBpZiAoIXRoaXMuZmlyZWQpIHtcbiAgICB0aGlzLnRhcmdldC5yZW1vdmVMaXN0ZW5lcih0aGlzLnR5cGUsIHRoaXMud3JhcEZuKTtcbiAgICB0aGlzLmZpcmVkID0gdHJ1ZTtcbiAgICBSZWZsZWN0QXBwbHkodGhpcy5saXN0ZW5lciwgdGhpcy50YXJnZXQsIGFyZ3MpO1xuICB9XG59XG5cbmZ1bmN0aW9uIF9vbmNlV3JhcCh0YXJnZXQsIHR5cGUsIGxpc3RlbmVyKSB7XG4gIHZhciBzdGF0ZSA9IHsgZmlyZWQ6IGZhbHNlLCB3cmFwRm46IHVuZGVmaW5lZCwgdGFyZ2V0OiB0YXJnZXQsIHR5cGU6IHR5cGUsIGxpc3RlbmVyOiBsaXN0ZW5lciB9O1xuICB2YXIgd3JhcHBlZCA9IG9uY2VXcmFwcGVyLmJpbmQoc3RhdGUpO1xuICB3cmFwcGVkLmxpc3RlbmVyID0gbGlzdGVuZXI7XG4gIHN0YXRlLndyYXBGbiA9IHdyYXBwZWQ7XG4gIHJldHVybiB3cmFwcGVkO1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9uY2UgPSBmdW5jdGlvbiBvbmNlKHR5cGUsIGxpc3RlbmVyKSB7XG4gIGlmICh0eXBlb2YgbGlzdGVuZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgfVxuICB0aGlzLm9uKHR5cGUsIF9vbmNlV3JhcCh0aGlzLCB0eXBlLCBsaXN0ZW5lcikpO1xuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucHJlcGVuZE9uY2VMaXN0ZW5lciA9XG4gICAgZnVuY3Rpb24gcHJlcGVuZE9uY2VMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcikge1xuICAgICAgaWYgKHR5cGVvZiBsaXN0ZW5lciAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgICAgIH1cbiAgICAgIHRoaXMucHJlcGVuZExpc3RlbmVyKHR5cGUsIF9vbmNlV3JhcCh0aGlzLCB0eXBlLCBsaXN0ZW5lcikpO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfTtcblxuLy8gRW1pdHMgYSAncmVtb3ZlTGlzdGVuZXInIGV2ZW50IGlmIGFuZCBvbmx5IGlmIHRoZSBsaXN0ZW5lciB3YXMgcmVtb3ZlZC5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlTGlzdGVuZXIgPVxuICAgIGZ1bmN0aW9uIHJlbW92ZUxpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gICAgICB2YXIgbGlzdCwgZXZlbnRzLCBwb3NpdGlvbiwgaSwgb3JpZ2luYWxMaXN0ZW5lcjtcblxuICAgICAgaWYgKHR5cGVvZiBsaXN0ZW5lciAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgICAgIH1cblxuICAgICAgZXZlbnRzID0gdGhpcy5fZXZlbnRzO1xuICAgICAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKVxuICAgICAgICByZXR1cm4gdGhpcztcblxuICAgICAgbGlzdCA9IGV2ZW50c1t0eXBlXTtcbiAgICAgIGlmIChsaXN0ID09PSB1bmRlZmluZWQpXG4gICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICBpZiAobGlzdCA9PT0gbGlzdGVuZXIgfHwgbGlzdC5saXN0ZW5lciA9PT0gbGlzdGVuZXIpIHtcbiAgICAgICAgaWYgKC0tdGhpcy5fZXZlbnRzQ291bnQgPT09IDApXG4gICAgICAgICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICAgICAgZWxzZSB7XG4gICAgICAgICAgZGVsZXRlIGV2ZW50c1t0eXBlXTtcbiAgICAgICAgICBpZiAoZXZlbnRzLnJlbW92ZUxpc3RlbmVyKVxuICAgICAgICAgICAgdGhpcy5lbWl0KCdyZW1vdmVMaXN0ZW5lcicsIHR5cGUsIGxpc3QubGlzdGVuZXIgfHwgbGlzdGVuZXIpO1xuICAgICAgICB9XG4gICAgICB9IGVsc2UgaWYgKHR5cGVvZiBsaXN0ICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHBvc2l0aW9uID0gLTE7XG5cbiAgICAgICAgZm9yIChpID0gbGlzdC5sZW5ndGggLSAxOyBpID49IDA7IGktLSkge1xuICAgICAgICAgIGlmIChsaXN0W2ldID09PSBsaXN0ZW5lciB8fCBsaXN0W2ldLmxpc3RlbmVyID09PSBsaXN0ZW5lcikge1xuICAgICAgICAgICAgb3JpZ2luYWxMaXN0ZW5lciA9IGxpc3RbaV0ubGlzdGVuZXI7XG4gICAgICAgICAgICBwb3NpdGlvbiA9IGk7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBpZiAocG9zaXRpb24gPCAwKVxuICAgICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICAgIGlmIChwb3NpdGlvbiA9PT0gMClcbiAgICAgICAgICBsaXN0LnNoaWZ0KCk7XG4gICAgICAgIGVsc2Uge1xuICAgICAgICAgIHNwbGljZU9uZShsaXN0LCBwb3NpdGlvbik7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAobGlzdC5sZW5ndGggPT09IDEpXG4gICAgICAgICAgZXZlbnRzW3R5cGVdID0gbGlzdFswXTtcblxuICAgICAgICBpZiAoZXZlbnRzLnJlbW92ZUxpc3RlbmVyICE9PSB1bmRlZmluZWQpXG4gICAgICAgICAgdGhpcy5lbWl0KCdyZW1vdmVMaXN0ZW5lcicsIHR5cGUsIG9yaWdpbmFsTGlzdGVuZXIgfHwgbGlzdGVuZXIpO1xuICAgICAgfVxuXG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9mZiA9IEV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlTGlzdGVuZXI7XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlQWxsTGlzdGVuZXJzID1cbiAgICBmdW5jdGlvbiByZW1vdmVBbGxMaXN0ZW5lcnModHlwZSkge1xuICAgICAgdmFyIGxpc3RlbmVycywgZXZlbnRzLCBpO1xuXG4gICAgICBldmVudHMgPSB0aGlzLl9ldmVudHM7XG4gICAgICBpZiAoZXZlbnRzID09PSB1bmRlZmluZWQpXG4gICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICAvLyBub3QgbGlzdGVuaW5nIGZvciByZW1vdmVMaXN0ZW5lciwgbm8gbmVlZCB0byBlbWl0XG4gICAgICBpZiAoZXZlbnRzLnJlbW92ZUxpc3RlbmVyID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgaWYgKGFyZ3VtZW50cy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICB0aGlzLl9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgICAgICAgIHRoaXMuX2V2ZW50c0NvdW50ID0gMDtcbiAgICAgICAgfSBlbHNlIGlmIChldmVudHNbdHlwZV0gIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgIGlmICgtLXRoaXMuX2V2ZW50c0NvdW50ID09PSAwKVxuICAgICAgICAgICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICAgICAgICBlbHNlXG4gICAgICAgICAgICBkZWxldGUgZXZlbnRzW3R5cGVdO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgICAgfVxuXG4gICAgICAvLyBlbWl0IHJlbW92ZUxpc3RlbmVyIGZvciBhbGwgbGlzdGVuZXJzIG9uIGFsbCBldmVudHNcbiAgICAgIGlmIChhcmd1bWVudHMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgIHZhciBrZXlzID0gT2JqZWN0LmtleXMoZXZlbnRzKTtcbiAgICAgICAgdmFyIGtleTtcbiAgICAgICAgZm9yIChpID0gMDsgaSA8IGtleXMubGVuZ3RoOyArK2kpIHtcbiAgICAgICAgICBrZXkgPSBrZXlzW2ldO1xuICAgICAgICAgIGlmIChrZXkgPT09ICdyZW1vdmVMaXN0ZW5lcicpIGNvbnRpbnVlO1xuICAgICAgICAgIHRoaXMucmVtb3ZlQWxsTGlzdGVuZXJzKGtleSk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5yZW1vdmVBbGxMaXN0ZW5lcnMoJ3JlbW92ZUxpc3RlbmVyJyk7XG4gICAgICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgICAgIHRoaXMuX2V2ZW50c0NvdW50ID0gMDtcbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICB9XG5cbiAgICAgIGxpc3RlbmVycyA9IGV2ZW50c1t0eXBlXTtcblxuICAgICAgaWYgKHR5cGVvZiBsaXN0ZW5lcnMgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhpcy5yZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcnMpO1xuICAgICAgfSBlbHNlIGlmIChsaXN0ZW5lcnMgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAvLyBMSUZPIG9yZGVyXG4gICAgICAgIGZvciAoaSA9IGxpc3RlbmVycy5sZW5ndGggLSAxOyBpID49IDA7IGktLSkge1xuICAgICAgICAgIHRoaXMucmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXJzW2ldKTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9O1xuXG5mdW5jdGlvbiBfbGlzdGVuZXJzKHRhcmdldCwgdHlwZSwgdW53cmFwKSB7XG4gIHZhciBldmVudHMgPSB0YXJnZXQuX2V2ZW50cztcblxuICBpZiAoZXZlbnRzID09PSB1bmRlZmluZWQpXG4gICAgcmV0dXJuIFtdO1xuXG4gIHZhciBldmxpc3RlbmVyID0gZXZlbnRzW3R5cGVdO1xuICBpZiAoZXZsaXN0ZW5lciA9PT0gdW5kZWZpbmVkKVxuICAgIHJldHVybiBbXTtcblxuICBpZiAodHlwZW9mIGV2bGlzdGVuZXIgPT09ICdmdW5jdGlvbicpXG4gICAgcmV0dXJuIHVud3JhcCA/IFtldmxpc3RlbmVyLmxpc3RlbmVyIHx8IGV2bGlzdGVuZXJdIDogW2V2bGlzdGVuZXJdO1xuXG4gIHJldHVybiB1bndyYXAgP1xuICAgIHVud3JhcExpc3RlbmVycyhldmxpc3RlbmVyKSA6IGFycmF5Q2xvbmUoZXZsaXN0ZW5lciwgZXZsaXN0ZW5lci5sZW5ndGgpO1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmxpc3RlbmVycyA9IGZ1bmN0aW9uIGxpc3RlbmVycyh0eXBlKSB7XG4gIHJldHVybiBfbGlzdGVuZXJzKHRoaXMsIHR5cGUsIHRydWUpO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yYXdMaXN0ZW5lcnMgPSBmdW5jdGlvbiByYXdMaXN0ZW5lcnModHlwZSkge1xuICByZXR1cm4gX2xpc3RlbmVycyh0aGlzLCB0eXBlLCBmYWxzZSk7XG59O1xuXG5FdmVudEVtaXR0ZXIubGlzdGVuZXJDb3VudCA9IGZ1bmN0aW9uKGVtaXR0ZXIsIHR5cGUpIHtcbiAgaWYgKHR5cGVvZiBlbWl0dGVyLmxpc3RlbmVyQ291bnQgPT09ICdmdW5jdGlvbicpIHtcbiAgICByZXR1cm4gZW1pdHRlci5saXN0ZW5lckNvdW50KHR5cGUpO1xuICB9IGVsc2Uge1xuICAgIHJldHVybiBsaXN0ZW5lckNvdW50LmNhbGwoZW1pdHRlciwgdHlwZSk7XG4gIH1cbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJDb3VudCA9IGxpc3RlbmVyQ291bnQ7XG5mdW5jdGlvbiBsaXN0ZW5lckNvdW50KHR5cGUpIHtcbiAgdmFyIGV2ZW50cyA9IHRoaXMuX2V2ZW50cztcblxuICBpZiAoZXZlbnRzICE9PSB1bmRlZmluZWQpIHtcbiAgICB2YXIgZXZsaXN0ZW5lciA9IGV2ZW50c1t0eXBlXTtcblxuICAgIGlmICh0eXBlb2YgZXZsaXN0ZW5lciA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgcmV0dXJuIDE7XG4gICAgfSBlbHNlIGlmIChldmxpc3RlbmVyICE9PSB1bmRlZmluZWQpIHtcbiAgICAgIHJldHVybiBldmxpc3RlbmVyLmxlbmd0aDtcbiAgICB9XG4gIH1cblxuICByZXR1cm4gMDtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5ldmVudE5hbWVzID0gZnVuY3Rpb24gZXZlbnROYW1lcygpIHtcbiAgcmV0dXJuIHRoaXMuX2V2ZW50c0NvdW50ID4gMCA/IFJlZmxlY3RPd25LZXlzKHRoaXMuX2V2ZW50cykgOiBbXTtcbn07XG5cbmZ1bmN0aW9uIGFycmF5Q2xvbmUoYXJyLCBuKSB7XG4gIHZhciBjb3B5ID0gbmV3IEFycmF5KG4pO1xuICBmb3IgKHZhciBpID0gMDsgaSA8IG47ICsraSlcbiAgICBjb3B5W2ldID0gYXJyW2ldO1xuICByZXR1cm4gY29weTtcbn1cblxuZnVuY3Rpb24gc3BsaWNlT25lKGxpc3QsIGluZGV4KSB7XG4gIGZvciAoOyBpbmRleCArIDEgPCBsaXN0Lmxlbmd0aDsgaW5kZXgrKylcbiAgICBsaXN0W2luZGV4XSA9IGxpc3RbaW5kZXggKyAxXTtcbiAgbGlzdC5wb3AoKTtcbn1cblxuZnVuY3Rpb24gdW53cmFwTGlzdGVuZXJzKGFycikge1xuICB2YXIgcmV0ID0gbmV3IEFycmF5KGFyci5sZW5ndGgpO1xuICBmb3IgKHZhciBpID0gMDsgaSA8IHJldC5sZW5ndGg7ICsraSkge1xuICAgIHJldFtpXSA9IGFycltpXS5saXN0ZW5lciB8fCBhcnJbaV07XG4gIH1cbiAgcmV0dXJuIHJldDtcbn1cblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9ldmVudHMvZXZlbnRzLmpzXG4vLyBtb2R1bGUgaWQgPSA1NlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA3IDEwIDExIDEyIDE1IDE2IDE3IDE4IDIxIDIzIDI0IDI1IDM0IDQxIDQzIDQ2IDQ4IDUwIDUxIiwiLy8gZmFsc2UgLT4gQXJyYXkjaW5kZXhPZlxuLy8gdHJ1ZSAgLT4gQXJyYXkjaW5jbHVkZXNcbnZhciB0b0lPYmplY3QgPSByZXF1aXJlKCcuL190by1pb2JqZWN0JylcbiAgLCB0b0xlbmd0aCAgPSByZXF1aXJlKCcuL190by1sZW5ndGgnKVxuICAsIHRvSW5kZXggICA9IHJlcXVpcmUoJy4vX3RvLWluZGV4Jyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKElTX0lOQ0xVREVTKXtcbiAgcmV0dXJuIGZ1bmN0aW9uKCR0aGlzLCBlbCwgZnJvbUluZGV4KXtcbiAgICB2YXIgTyAgICAgID0gdG9JT2JqZWN0KCR0aGlzKVxuICAgICAgLCBsZW5ndGggPSB0b0xlbmd0aChPLmxlbmd0aClcbiAgICAgICwgaW5kZXggID0gdG9JbmRleChmcm9tSW5kZXgsIGxlbmd0aClcbiAgICAgICwgdmFsdWU7XG4gICAgLy8gQXJyYXkjaW5jbHVkZXMgdXNlcyBTYW1lVmFsdWVaZXJvIGVxdWFsaXR5IGFsZ29yaXRobVxuICAgIGlmKElTX0lOQ0xVREVTICYmIGVsICE9IGVsKXdoaWxlKGxlbmd0aCA+IGluZGV4KXtcbiAgICAgIHZhbHVlID0gT1tpbmRleCsrXTtcbiAgICAgIGlmKHZhbHVlICE9IHZhbHVlKXJldHVybiB0cnVlO1xuICAgIC8vIEFycmF5I3RvSW5kZXggaWdub3JlcyBob2xlcywgQXJyYXkjaW5jbHVkZXMgLSBub3RcbiAgICB9IGVsc2UgZm9yKDtsZW5ndGggPiBpbmRleDsgaW5kZXgrKylpZihJU19JTkNMVURFUyB8fCBpbmRleCBpbiBPKXtcbiAgICAgIGlmKE9baW5kZXhdID09PSBlbClyZXR1cm4gSVNfSU5DTFVERVMgfHwgaW5kZXggfHwgMDtcbiAgICB9IHJldHVybiAhSVNfSU5DTFVERVMgJiYgLTE7XG4gIH07XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYXJyYXktaW5jbHVkZXMuanNcbi8vIG1vZHVsZSBpZCA9IDU3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwidmFyIHRvSW50ZWdlciA9IHJlcXVpcmUoJy4vX3RvLWludGVnZXInKVxuICAsIG1heCAgICAgICA9IE1hdGgubWF4XG4gICwgbWluICAgICAgID0gTWF0aC5taW47XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGluZGV4LCBsZW5ndGgpe1xuICBpbmRleCA9IHRvSW50ZWdlcihpbmRleCk7XG4gIHJldHVybiBpbmRleCA8IDAgPyBtYXgoaW5kZXggKyBsZW5ndGgsIDApIDogbWluKGluZGV4LCBsZW5ndGgpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLWluZGV4LmpzXG4vLyBtb2R1bGUgaWQgPSA1OFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsImV4cG9ydHMuZiA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHM7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZ29wcy5qc1xuLy8gbW9kdWxlIGlkID0gNTlcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE1IDE2IDE4IiwidmFyIGRlZiA9IHJlcXVpcmUoJy4vX29iamVjdC1kcCcpLmZcbiAgLCBoYXMgPSByZXF1aXJlKCcuL19oYXMnKVxuICAsIFRBRyA9IHJlcXVpcmUoJy4vX3drcycpKCd0b1N0cmluZ1RhZycpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0LCB0YWcsIHN0YXQpe1xuICBpZihpdCAmJiAhaGFzKGl0ID0gc3RhdCA/IGl0IDogaXQucHJvdG90eXBlLCBUQUcpKWRlZihpdCwgVEFHLCB7Y29uZmlndXJhYmxlOiB0cnVlLCB2YWx1ZTogdGFnfSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fc2V0LXRvLXN0cmluZy10YWcuanNcbi8vIG1vZHVsZSBpZCA9IDYwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDggOSAxNCIsIm1vZHVsZS5leHBvcnRzID0gdHJ1ZTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2xpYnJhcnkuanNcbi8vIG1vZHVsZSBpZCA9IDYzXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDggOSAxNCIsIid1c2Ugc3RyaWN0JztcbnZhciAkYXQgID0gcmVxdWlyZSgnLi9fc3RyaW5nLWF0JykodHJ1ZSk7XG5cbi8vIDIxLjEuMy4yNyBTdHJpbmcucHJvdG90eXBlW0BAaXRlcmF0b3JdKClcbnJlcXVpcmUoJy4vX2l0ZXItZGVmaW5lJykoU3RyaW5nLCAnU3RyaW5nJywgZnVuY3Rpb24oaXRlcmF0ZWQpe1xuICB0aGlzLl90ID0gU3RyaW5nKGl0ZXJhdGVkKTsgLy8gdGFyZ2V0XG4gIHRoaXMuX2kgPSAwOyAgICAgICAgICAgICAgICAvLyBuZXh0IGluZGV4XG4vLyAyMS4xLjUuMi4xICVTdHJpbmdJdGVyYXRvclByb3RvdHlwZSUubmV4dCgpXG59LCBmdW5jdGlvbigpe1xuICB2YXIgTyAgICAgPSB0aGlzLl90XG4gICAgLCBpbmRleCA9IHRoaXMuX2lcbiAgICAsIHBvaW50O1xuICBpZihpbmRleCA+PSBPLmxlbmd0aClyZXR1cm4ge3ZhbHVlOiB1bmRlZmluZWQsIGRvbmU6IHRydWV9O1xuICBwb2ludCA9ICRhdChPLCBpbmRleCk7XG4gIHRoaXMuX2kgKz0gcG9pbnQubGVuZ3RoO1xuICByZXR1cm4ge3ZhbHVlOiBwb2ludCwgZG9uZTogZmFsc2V9O1xufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5zdHJpbmcuaXRlcmF0b3IuanNcbi8vIG1vZHVsZSBpZCA9IDY0XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDggOSAxNCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IFJvdXRpbmcgZnJvbSAnZm9zLXJvdXRpbmcnO1xuaW1wb3J0IHJvdXRlcyBmcm9tICdAanMvZm9zX2pzX3JvdXRlcy5qc29uJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFdyYXBzIEZPU0pzUm91dGluZ2J1bmRsZSB3aXRoIGV4cG9zZWQgcm91dGVzLlxuICogVG8gZXhwb3NlIHJvdXRlIGFkZCBvcHRpb24gYGV4cG9zZTogdHJ1ZWAgaW4gLnltbCByb3V0aW5nIGNvbmZpZ1xuICpcbiAqIGUuZy5cbiAqXG4gKiBgbXlfcm91dGVcbiAqICAgIHBhdGg6IC9teS1wYXRoXG4gKiAgICBvcHRpb25zOlxuICogICAgICBleHBvc2U6IHRydWVcbiAqIGBcbiAqIEFuZCBydW4gYGJpbi9jb25zb2xlIGZvczpqcy1yb3V0aW5nOmR1bXAgLS1mb3JtYXQ9anNvbiAtLXRhcmdldD1hZG1pbi1kZXYvdGhlbWVzL25ldy10aGVtZS9qcy9mb3NfanNfcm91dGVzLmpzb25gXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFJvdXRlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIFJvdXRpbmcuc2V0RGF0YShyb3V0ZXMpO1xuICAgIFJvdXRpbmcuc2V0QmFzZVVybCgkKGRvY3VtZW50KS5maW5kKCdib2R5JykuZGF0YSgnYmFzZS11cmwnKSk7XG5cbiAgICByZXR1cm4gdGhpcztcbiAgfVxuXG4gIC8qKlxuICAgKiBEZWNvcmF0ZWQgXCJnZW5lcmF0ZVwiIG1ldGhvZCwgd2l0aCBwcmVkZWZpbmVkIHNlY3VyaXR5IHRva2VuIGluIHBhcmFtc1xuICAgKlxuICAgKiBAcGFyYW0gcm91dGVcbiAgICogQHBhcmFtIHBhcmFtc1xuICAgKlxuICAgKiBAcmV0dXJucyB7U3RyaW5nfVxuICAgKi9cbiAgZ2VuZXJhdGUocm91dGUsIHBhcmFtcyA9IHt9KSB7XG4gICAgY29uc3QgdG9rZW5pemVkUGFyYW1zID0gT2JqZWN0LmFzc2lnbihwYXJhbXMsIHtfdG9rZW46ICQoZG9jdW1lbnQpLmZpbmQoJ2JvZHknKS5kYXRhKCd0b2tlbicpfSk7XG5cbiAgICByZXR1cm4gUm91dGluZy5nZW5lcmF0ZShyb3V0ZSwgdG9rZW5pemVkUGFyYW1zKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9yb3V0ZXIuanMiLCIvLyAxOS4xLjIuMiAvIDE1LjIuMy41IE9iamVjdC5jcmVhdGUoTyBbLCBQcm9wZXJ0aWVzXSlcbnZhciBhbk9iamVjdCAgICA9IHJlcXVpcmUoJy4vX2FuLW9iamVjdCcpXG4gICwgZFBzICAgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZHBzJylcbiAgLCBlbnVtQnVnS2V5cyA9IHJlcXVpcmUoJy4vX2VudW0tYnVnLWtleXMnKVxuICAsIElFX1BST1RPICAgID0gcmVxdWlyZSgnLi9fc2hhcmVkLWtleScpKCdJRV9QUk9UTycpXG4gICwgRW1wdHkgICAgICAgPSBmdW5jdGlvbigpeyAvKiBlbXB0eSAqLyB9XG4gICwgUFJPVE9UWVBFICAgPSAncHJvdG90eXBlJztcblxuLy8gQ3JlYXRlIG9iamVjdCB3aXRoIGZha2UgYG51bGxgIHByb3RvdHlwZTogdXNlIGlmcmFtZSBPYmplY3Qgd2l0aCBjbGVhcmVkIHByb3RvdHlwZVxudmFyIGNyZWF0ZURpY3QgPSBmdW5jdGlvbigpe1xuICAvLyBUaHJhc2gsIHdhc3RlIGFuZCBzb2RvbXk6IElFIEdDIGJ1Z1xuICB2YXIgaWZyYW1lID0gcmVxdWlyZSgnLi9fZG9tLWNyZWF0ZScpKCdpZnJhbWUnKVxuICAgICwgaSAgICAgID0gZW51bUJ1Z0tleXMubGVuZ3RoXG4gICAgLCBsdCAgICAgPSAnPCdcbiAgICAsIGd0ICAgICA9ICc+J1xuICAgICwgaWZyYW1lRG9jdW1lbnQ7XG4gIGlmcmFtZS5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICByZXF1aXJlKCcuL19odG1sJykuYXBwZW5kQ2hpbGQoaWZyYW1lKTtcbiAgaWZyYW1lLnNyYyA9ICdqYXZhc2NyaXB0Oic7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tc2NyaXB0LXVybFxuICAvLyBjcmVhdGVEaWN0ID0gaWZyYW1lLmNvbnRlbnRXaW5kb3cuT2JqZWN0O1xuICAvLyBodG1sLnJlbW92ZUNoaWxkKGlmcmFtZSk7XG4gIGlmcmFtZURvY3VtZW50ID0gaWZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQ7XG4gIGlmcmFtZURvY3VtZW50Lm9wZW4oKTtcbiAgaWZyYW1lRG9jdW1lbnQud3JpdGUobHQgKyAnc2NyaXB0JyArIGd0ICsgJ2RvY3VtZW50LkY9T2JqZWN0JyArIGx0ICsgJy9zY3JpcHQnICsgZ3QpO1xuICBpZnJhbWVEb2N1bWVudC5jbG9zZSgpO1xuICBjcmVhdGVEaWN0ID0gaWZyYW1lRG9jdW1lbnQuRjtcbiAgd2hpbGUoaS0tKWRlbGV0ZSBjcmVhdGVEaWN0W1BST1RPVFlQRV1bZW51bUJ1Z0tleXNbaV1dO1xuICByZXR1cm4gY3JlYXRlRGljdCgpO1xufTtcblxubW9kdWxlLmV4cG9ydHMgPSBPYmplY3QuY3JlYXRlIHx8IGZ1bmN0aW9uIGNyZWF0ZShPLCBQcm9wZXJ0aWVzKXtcbiAgdmFyIHJlc3VsdDtcbiAgaWYoTyAhPT0gbnVsbCl7XG4gICAgRW1wdHlbUFJPVE9UWVBFXSA9IGFuT2JqZWN0KE8pO1xuICAgIHJlc3VsdCA9IG5ldyBFbXB0eTtcbiAgICBFbXB0eVtQUk9UT1RZUEVdID0gbnVsbDtcbiAgICAvLyBhZGQgXCJfX3Byb3RvX19cIiBmb3IgT2JqZWN0LmdldFByb3RvdHlwZU9mIHBvbHlmaWxsXG4gICAgcmVzdWx0W0lFX1BST1RPXSA9IE87XG4gIH0gZWxzZSByZXN1bHQgPSBjcmVhdGVEaWN0KCk7XG4gIHJldHVybiBQcm9wZXJ0aWVzID09PSB1bmRlZmluZWQgPyByZXN1bHQgOiBkUHMocmVzdWx0LCBQcm9wZXJ0aWVzKTtcbn07XG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1jcmVhdGUuanNcbi8vIG1vZHVsZSBpZCA9IDY2XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDggOSAxNCIsIid1c2Ugc3RyaWN0JztcbnZhciBMSUJSQVJZICAgICAgICA9IHJlcXVpcmUoJy4vX2xpYnJhcnknKVxuICAsICRleHBvcnQgICAgICAgID0gcmVxdWlyZSgnLi9fZXhwb3J0JylcbiAgLCByZWRlZmluZSAgICAgICA9IHJlcXVpcmUoJy4vX3JlZGVmaW5lJylcbiAgLCBoaWRlICAgICAgICAgICA9IHJlcXVpcmUoJy4vX2hpZGUnKVxuICAsIGhhcyAgICAgICAgICAgID0gcmVxdWlyZSgnLi9faGFzJylcbiAgLCBJdGVyYXRvcnMgICAgICA9IHJlcXVpcmUoJy4vX2l0ZXJhdG9ycycpXG4gICwgJGl0ZXJDcmVhdGUgICAgPSByZXF1aXJlKCcuL19pdGVyLWNyZWF0ZScpXG4gICwgc2V0VG9TdHJpbmdUYWcgPSByZXF1aXJlKCcuL19zZXQtdG8tc3RyaW5nLXRhZycpXG4gICwgZ2V0UHJvdG90eXBlT2YgPSByZXF1aXJlKCcuL19vYmplY3QtZ3BvJylcbiAgLCBJVEVSQVRPUiAgICAgICA9IHJlcXVpcmUoJy4vX3drcycpKCdpdGVyYXRvcicpXG4gICwgQlVHR1kgICAgICAgICAgPSAhKFtdLmtleXMgJiYgJ25leHQnIGluIFtdLmtleXMoKSkgLy8gU2FmYXJpIGhhcyBidWdneSBpdGVyYXRvcnMgdy9vIGBuZXh0YFxuICAsIEZGX0lURVJBVE9SICAgID0gJ0BAaXRlcmF0b3InXG4gICwgS0VZUyAgICAgICAgICAgPSAna2V5cydcbiAgLCBWQUxVRVMgICAgICAgICA9ICd2YWx1ZXMnO1xuXG52YXIgcmV0dXJuVGhpcyA9IGZ1bmN0aW9uKCl7IHJldHVybiB0aGlzOyB9O1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKEJhc2UsIE5BTUUsIENvbnN0cnVjdG9yLCBuZXh0LCBERUZBVUxULCBJU19TRVQsIEZPUkNFRCl7XG4gICRpdGVyQ3JlYXRlKENvbnN0cnVjdG9yLCBOQU1FLCBuZXh0KTtcbiAgdmFyIGdldE1ldGhvZCA9IGZ1bmN0aW9uKGtpbmQpe1xuICAgIGlmKCFCVUdHWSAmJiBraW5kIGluIHByb3RvKXJldHVybiBwcm90b1traW5kXTtcbiAgICBzd2l0Y2goa2luZCl7XG4gICAgICBjYXNlIEtFWVM6IHJldHVybiBmdW5jdGlvbiBrZXlzKCl7IHJldHVybiBuZXcgQ29uc3RydWN0b3IodGhpcywga2luZCk7IH07XG4gICAgICBjYXNlIFZBTFVFUzogcmV0dXJuIGZ1bmN0aW9uIHZhbHVlcygpeyByZXR1cm4gbmV3IENvbnN0cnVjdG9yKHRoaXMsIGtpbmQpOyB9O1xuICAgIH0gcmV0dXJuIGZ1bmN0aW9uIGVudHJpZXMoKXsgcmV0dXJuIG5ldyBDb25zdHJ1Y3Rvcih0aGlzLCBraW5kKTsgfTtcbiAgfTtcbiAgdmFyIFRBRyAgICAgICAgPSBOQU1FICsgJyBJdGVyYXRvcidcbiAgICAsIERFRl9WQUxVRVMgPSBERUZBVUxUID09IFZBTFVFU1xuICAgICwgVkFMVUVTX0JVRyA9IGZhbHNlXG4gICAgLCBwcm90byAgICAgID0gQmFzZS5wcm90b3R5cGVcbiAgICAsICRuYXRpdmUgICAgPSBwcm90b1tJVEVSQVRPUl0gfHwgcHJvdG9bRkZfSVRFUkFUT1JdIHx8IERFRkFVTFQgJiYgcHJvdG9bREVGQVVMVF1cbiAgICAsICRkZWZhdWx0ICAgPSAkbmF0aXZlIHx8IGdldE1ldGhvZChERUZBVUxUKVxuICAgICwgJGVudHJpZXMgICA9IERFRkFVTFQgPyAhREVGX1ZBTFVFUyA/ICRkZWZhdWx0IDogZ2V0TWV0aG9kKCdlbnRyaWVzJykgOiB1bmRlZmluZWRcbiAgICAsICRhbnlOYXRpdmUgPSBOQU1FID09ICdBcnJheScgPyBwcm90by5lbnRyaWVzIHx8ICRuYXRpdmUgOiAkbmF0aXZlXG4gICAgLCBtZXRob2RzLCBrZXksIEl0ZXJhdG9yUHJvdG90eXBlO1xuICAvLyBGaXggbmF0aXZlXG4gIGlmKCRhbnlOYXRpdmUpe1xuICAgIEl0ZXJhdG9yUHJvdG90eXBlID0gZ2V0UHJvdG90eXBlT2YoJGFueU5hdGl2ZS5jYWxsKG5ldyBCYXNlKSk7XG4gICAgaWYoSXRlcmF0b3JQcm90b3R5cGUgIT09IE9iamVjdC5wcm90b3R5cGUpe1xuICAgICAgLy8gU2V0IEBAdG9TdHJpbmdUYWcgdG8gbmF0aXZlIGl0ZXJhdG9yc1xuICAgICAgc2V0VG9TdHJpbmdUYWcoSXRlcmF0b3JQcm90b3R5cGUsIFRBRywgdHJ1ZSk7XG4gICAgICAvLyBmaXggZm9yIHNvbWUgb2xkIGVuZ2luZXNcbiAgICAgIGlmKCFMSUJSQVJZICYmICFoYXMoSXRlcmF0b3JQcm90b3R5cGUsIElURVJBVE9SKSloaWRlKEl0ZXJhdG9yUHJvdG90eXBlLCBJVEVSQVRPUiwgcmV0dXJuVGhpcyk7XG4gICAgfVxuICB9XG4gIC8vIGZpeCBBcnJheSN7dmFsdWVzLCBAQGl0ZXJhdG9yfS5uYW1lIGluIFY4IC8gRkZcbiAgaWYoREVGX1ZBTFVFUyAmJiAkbmF0aXZlICYmICRuYXRpdmUubmFtZSAhPT0gVkFMVUVTKXtcbiAgICBWQUxVRVNfQlVHID0gdHJ1ZTtcbiAgICAkZGVmYXVsdCA9IGZ1bmN0aW9uIHZhbHVlcygpeyByZXR1cm4gJG5hdGl2ZS5jYWxsKHRoaXMpOyB9O1xuICB9XG4gIC8vIERlZmluZSBpdGVyYXRvclxuICBpZigoIUxJQlJBUlkgfHwgRk9SQ0VEKSAmJiAoQlVHR1kgfHwgVkFMVUVTX0JVRyB8fCAhcHJvdG9bSVRFUkFUT1JdKSl7XG4gICAgaGlkZShwcm90bywgSVRFUkFUT1IsICRkZWZhdWx0KTtcbiAgfVxuICAvLyBQbHVnIGZvciBsaWJyYXJ5XG4gIEl0ZXJhdG9yc1tOQU1FXSA9ICRkZWZhdWx0O1xuICBJdGVyYXRvcnNbVEFHXSAgPSByZXR1cm5UaGlzO1xuICBpZihERUZBVUxUKXtcbiAgICBtZXRob2RzID0ge1xuICAgICAgdmFsdWVzOiAgREVGX1ZBTFVFUyA/ICRkZWZhdWx0IDogZ2V0TWV0aG9kKFZBTFVFUyksXG4gICAgICBrZXlzOiAgICBJU19TRVQgICAgID8gJGRlZmF1bHQgOiBnZXRNZXRob2QoS0VZUyksXG4gICAgICBlbnRyaWVzOiAkZW50cmllc1xuICAgIH07XG4gICAgaWYoRk9SQ0VEKWZvcihrZXkgaW4gbWV0aG9kcyl7XG4gICAgICBpZighKGtleSBpbiBwcm90bykpcmVkZWZpbmUocHJvdG8sIGtleSwgbWV0aG9kc1trZXldKTtcbiAgICB9IGVsc2UgJGV4cG9ydCgkZXhwb3J0LlAgKyAkZXhwb3J0LkYgKiAoQlVHR1kgfHwgVkFMVUVTX0JVRyksIE5BTUUsIG1ldGhvZHMpO1xuICB9XG4gIHJldHVybiBtZXRob2RzO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXItZGVmaW5lLmpzXG4vLyBtb2R1bGUgaWQgPSA2N1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA4IDkgMTQiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2tleXNcIiksIF9fZXNNb2R1bGU6IHRydWUgfTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL29iamVjdC9rZXlzLmpzXG4vLyBtb2R1bGUgaWQgPSA3MFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDggOSAxMCAxNSAxOSAyMCIsInZhciBnbG9iYWwgICAgICAgICA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpXG4gICwgY29yZSAgICAgICAgICAgPSByZXF1aXJlKCcuL19jb3JlJylcbiAgLCBMSUJSQVJZICAgICAgICA9IHJlcXVpcmUoJy4vX2xpYnJhcnknKVxuICAsIHdrc0V4dCAgICAgICAgID0gcmVxdWlyZSgnLi9fd2tzLWV4dCcpXG4gICwgZGVmaW5lUHJvcGVydHkgPSByZXF1aXJlKCcuL19vYmplY3QtZHAnKS5mO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihuYW1lKXtcbiAgdmFyICRTeW1ib2wgPSBjb3JlLlN5bWJvbCB8fCAoY29yZS5TeW1ib2wgPSBMSUJSQVJZID8ge30gOiBnbG9iYWwuU3ltYm9sIHx8IHt9KTtcbiAgaWYobmFtZS5jaGFyQXQoMCkgIT0gJ18nICYmICEobmFtZSBpbiAkU3ltYm9sKSlkZWZpbmVQcm9wZXJ0eSgkU3ltYm9sLCBuYW1lLCB7dmFsdWU6IHdrc0V4dC5mKG5hbWUpfSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fd2tzLWRlZmluZS5qc1xuLy8gbW9kdWxlIGlkID0gNzFcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsImV4cG9ydHMuZiA9IHJlcXVpcmUoJy4vX3drcycpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fd2tzLWV4dC5qc1xuLy8gbW9kdWxlIGlkID0gNzJcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsInJlcXVpcmUoJy4vZXM2LmFycmF5Lml0ZXJhdG9yJyk7XG52YXIgZ2xvYmFsICAgICAgICA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpXG4gICwgaGlkZSAgICAgICAgICA9IHJlcXVpcmUoJy4vX2hpZGUnKVxuICAsIEl0ZXJhdG9ycyAgICAgPSByZXF1aXJlKCcuL19pdGVyYXRvcnMnKVxuICAsIFRPX1NUUklOR19UQUcgPSByZXF1aXJlKCcuL193a3MnKSgndG9TdHJpbmdUYWcnKTtcblxuZm9yKHZhciBjb2xsZWN0aW9ucyA9IFsnTm9kZUxpc3QnLCAnRE9NVG9rZW5MaXN0JywgJ01lZGlhTGlzdCcsICdTdHlsZVNoZWV0TGlzdCcsICdDU1NSdWxlTGlzdCddLCBpID0gMDsgaSA8IDU7IGkrKyl7XG4gIHZhciBOQU1FICAgICAgID0gY29sbGVjdGlvbnNbaV1cbiAgICAsIENvbGxlY3Rpb24gPSBnbG9iYWxbTkFNRV1cbiAgICAsIHByb3RvICAgICAgPSBDb2xsZWN0aW9uICYmIENvbGxlY3Rpb24ucHJvdG90eXBlO1xuICBpZihwcm90byAmJiAhcHJvdG9bVE9fU1RSSU5HX1RBR10paGlkZShwcm90bywgVE9fU1RSSU5HX1RBRywgTkFNRSk7XG4gIEl0ZXJhdG9yc1tOQU1FXSA9IEl0ZXJhdG9ycy5BcnJheTtcbn1cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvd2ViLmRvbS5pdGVyYWJsZS5qc1xuLy8gbW9kdWxlIGlkID0gNzNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IDE0IiwibW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuL19oaWRlJyk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19yZWRlZmluZS5qc1xuLy8gbW9kdWxlIGlkID0gNzZcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IDE0IiwiLy8gbW9zdCBPYmplY3QgbWV0aG9kcyBieSBFUzYgc2hvdWxkIGFjY2VwdCBwcmltaXRpdmVzXG52YXIgJGV4cG9ydCA9IHJlcXVpcmUoJy4vX2V4cG9ydCcpXG4gICwgY29yZSAgICA9IHJlcXVpcmUoJy4vX2NvcmUnKVxuICAsIGZhaWxzICAgPSByZXF1aXJlKCcuL19mYWlscycpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihLRVksIGV4ZWMpe1xuICB2YXIgZm4gID0gKGNvcmUuT2JqZWN0IHx8IHt9KVtLRVldIHx8IE9iamVjdFtLRVldXG4gICAgLCBleHAgPSB7fTtcbiAgZXhwW0tFWV0gPSBleGVjKGZuKTtcbiAgJGV4cG9ydCgkZXhwb3J0LlMgKyAkZXhwb3J0LkYgKiBmYWlscyhmdW5jdGlvbigpeyBmbigxKTsgfSksICdPYmplY3QnLCBleHApO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1zYXAuanNcbi8vIG1vZHVsZSBpZCA9IDc4XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgOCA5IDEwIDE1IDE5IDIwIiwiLy8gMTkuMS4yLjkgLyAxNS4yLjMuMiBPYmplY3QuZ2V0UHJvdG90eXBlT2YoTylcbnZhciBoYXMgICAgICAgICA9IHJlcXVpcmUoJy4vX2hhcycpXG4gICwgdG9PYmplY3QgICAgPSByZXF1aXJlKCcuL190by1vYmplY3QnKVxuICAsIElFX1BST1RPICAgID0gcmVxdWlyZSgnLi9fc2hhcmVkLWtleScpKCdJRV9QUk9UTycpXG4gICwgT2JqZWN0UHJvdG8gPSBPYmplY3QucHJvdG90eXBlO1xuXG5tb2R1bGUuZXhwb3J0cyA9IE9iamVjdC5nZXRQcm90b3R5cGVPZiB8fCBmdW5jdGlvbihPKXtcbiAgTyA9IHRvT2JqZWN0KE8pO1xuICBpZihoYXMoTywgSUVfUFJPVE8pKXJldHVybiBPW0lFX1BST1RPXTtcbiAgaWYodHlwZW9mIE8uY29uc3RydWN0b3IgPT0gJ2Z1bmN0aW9uJyAmJiBPIGluc3RhbmNlb2YgTy5jb25zdHJ1Y3Rvcil7XG4gICAgcmV0dXJuIE8uY29uc3RydWN0b3IucHJvdG90eXBlO1xuICB9IHJldHVybiBPIGluc3RhbmNlb2YgT2JqZWN0ID8gT2JqZWN0UHJvdG8gOiBudWxsO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1ncG8uanNcbi8vIG1vZHVsZSBpZCA9IDgyXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDggOSAxNCIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvYXNzaWduXCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3QvYXNzaWduLmpzXG4vLyBtb2R1bGUgaWQgPSA4M1xuLy8gbW9kdWxlIGNodW5rcyA9IDMgNyAxMCAxMSAxMiAxMyAxNSAxNiAxOCIsInJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM2Lm9iamVjdC5hc3NpZ24nKTtcbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9fY29yZScpLk9iamVjdC5hc3NpZ247XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvYXNzaWduLmpzXG4vLyBtb2R1bGUgaWQgPSA4NFxuLy8gbW9kdWxlIGNodW5rcyA9IDMgNyAxMCAxMSAxMiAxMyAxNSAxNiAxOCIsInJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM2Lm9iamVjdC5rZXlzJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4uLy4uL21vZHVsZXMvX2NvcmUnKS5PYmplY3Qua2V5cztcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9rZXlzLmpzXG4vLyBtb2R1bGUgaWQgPSA4NVxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDggOSAxMCAxNSAxOSAyMCIsIi8vIGdldHRpbmcgdGFnIGZyb20gMTkuMS4zLjYgT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZygpXG52YXIgY29mID0gcmVxdWlyZSgnLi9fY29mJylcbiAgLCBUQUcgPSByZXF1aXJlKCcuL193a3MnKSgndG9TdHJpbmdUYWcnKVxuICAvLyBFUzMgd3JvbmcgaGVyZVxuICAsIEFSRyA9IGNvZihmdW5jdGlvbigpeyByZXR1cm4gYXJndW1lbnRzOyB9KCkpID09ICdBcmd1bWVudHMnO1xuXG4vLyBmYWxsYmFjayBmb3IgSUUxMSBTY3JpcHQgQWNjZXNzIERlbmllZCBlcnJvclxudmFyIHRyeUdldCA9IGZ1bmN0aW9uKGl0LCBrZXkpe1xuICB0cnkge1xuICAgIHJldHVybiBpdFtrZXldO1xuICB9IGNhdGNoKGUpeyAvKiBlbXB0eSAqLyB9XG59O1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgdmFyIE8sIFQsIEI7XG4gIHJldHVybiBpdCA9PT0gdW5kZWZpbmVkID8gJ1VuZGVmaW5lZCcgOiBpdCA9PT0gbnVsbCA/ICdOdWxsJ1xuICAgIC8vIEBAdG9TdHJpbmdUYWcgY2FzZVxuICAgIDogdHlwZW9mIChUID0gdHJ5R2V0KE8gPSBPYmplY3QoaXQpLCBUQUcpKSA9PSAnc3RyaW5nJyA/IFRcbiAgICAvLyBidWlsdGluVGFnIGNhc2VcbiAgICA6IEFSRyA/IGNvZihPKVxuICAgIC8vIEVTMyBhcmd1bWVudHMgZmFsbGJhY2tcbiAgICA6IChCID0gY29mKE8pKSA9PSAnT2JqZWN0JyAmJiB0eXBlb2YgTy5jYWxsZWUgPT0gJ2Z1bmN0aW9uJyA/ICdBcmd1bWVudHMnIDogQjtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jbGFzc29mLmpzXG4vLyBtb2R1bGUgaWQgPSA4NlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSAxNCIsIid1c2Ugc3RyaWN0Jztcbi8vIDE5LjEuMi4xIE9iamVjdC5hc3NpZ24odGFyZ2V0LCBzb3VyY2UsIC4uLilcbnZhciBnZXRLZXlzICA9IHJlcXVpcmUoJy4vX29iamVjdC1rZXlzJylcbiAgLCBnT1BTICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1nb3BzJylcbiAgLCBwSUUgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1waWUnKVxuICAsIHRvT2JqZWN0ID0gcmVxdWlyZSgnLi9fdG8tb2JqZWN0JylcbiAgLCBJT2JqZWN0ICA9IHJlcXVpcmUoJy4vX2lvYmplY3QnKVxuICAsICRhc3NpZ24gID0gT2JqZWN0LmFzc2lnbjtcblxuLy8gc2hvdWxkIHdvcmsgd2l0aCBzeW1ib2xzIGFuZCBzaG91bGQgaGF2ZSBkZXRlcm1pbmlzdGljIHByb3BlcnR5IG9yZGVyIChWOCBidWcpXG5tb2R1bGUuZXhwb3J0cyA9ICEkYXNzaWduIHx8IHJlcXVpcmUoJy4vX2ZhaWxzJykoZnVuY3Rpb24oKXtcbiAgdmFyIEEgPSB7fVxuICAgICwgQiA9IHt9XG4gICAgLCBTID0gU3ltYm9sKClcbiAgICAsIEsgPSAnYWJjZGVmZ2hpamtsbW5vcHFyc3QnO1xuICBBW1NdID0gNztcbiAgSy5zcGxpdCgnJykuZm9yRWFjaChmdW5jdGlvbihrKXsgQltrXSA9IGs7IH0pO1xuICByZXR1cm4gJGFzc2lnbih7fSwgQSlbU10gIT0gNyB8fCBPYmplY3Qua2V5cygkYXNzaWduKHt9LCBCKSkuam9pbignJykgIT0gSztcbn0pID8gZnVuY3Rpb24gYXNzaWduKHRhcmdldCwgc291cmNlKXsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBuby11bnVzZWQtdmFyc1xuICB2YXIgVCAgICAgPSB0b09iamVjdCh0YXJnZXQpXG4gICAgLCBhTGVuICA9IGFyZ3VtZW50cy5sZW5ndGhcbiAgICAsIGluZGV4ID0gMVxuICAgICwgZ2V0U3ltYm9scyA9IGdPUFMuZlxuICAgICwgaXNFbnVtICAgICA9IHBJRS5mO1xuICB3aGlsZShhTGVuID4gaW5kZXgpe1xuICAgIHZhciBTICAgICAgPSBJT2JqZWN0KGFyZ3VtZW50c1tpbmRleCsrXSlcbiAgICAgICwga2V5cyAgID0gZ2V0U3ltYm9scyA/IGdldEtleXMoUykuY29uY2F0KGdldFN5bWJvbHMoUykpIDogZ2V0S2V5cyhTKVxuICAgICAgLCBsZW5ndGggPSBrZXlzLmxlbmd0aFxuICAgICAgLCBqICAgICAgPSAwXG4gICAgICAsIGtleTtcbiAgICB3aGlsZShsZW5ndGggPiBqKWlmKGlzRW51bS5jYWxsKFMsIGtleSA9IGtleXNbaisrXSkpVFtrZXldID0gU1trZXldO1xuICB9IHJldHVybiBUO1xufSA6ICRhc3NpZ247XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtYXNzaWduLmpzXG4vLyBtb2R1bGUgaWQgPSA4N1xuLy8gbW9kdWxlIGNodW5rcyA9IDMgNyAxMCAxMSAxMiAxMyAxNSAxNiAxOCIsIi8vIDE5LjEuMi43IC8gMTUuMi4zLjQgT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXMoTylcbnZhciAka2V5cyAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWtleXMtaW50ZXJuYWwnKVxuICAsIGhpZGRlbktleXMgPSByZXF1aXJlKCcuL19lbnVtLWJ1Zy1rZXlzJykuY29uY2F0KCdsZW5ndGgnLCAncHJvdG90eXBlJyk7XG5cbmV4cG9ydHMuZiA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzIHx8IGZ1bmN0aW9uIGdldE93blByb3BlcnR5TmFtZXMoTyl7XG4gIHJldHVybiAka2V5cyhPLCBoaWRkZW5LZXlzKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZ29wbi5qc1xuLy8gbW9kdWxlIGlkID0gODhcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsIi8vIDE5LjEuMy4xIE9iamVjdC5hc3NpZ24odGFyZ2V0LCBzb3VyY2UpXG52YXIgJGV4cG9ydCA9IHJlcXVpcmUoJy4vX2V4cG9ydCcpO1xuXG4kZXhwb3J0KCRleHBvcnQuUyArICRleHBvcnQuRiwgJ09iamVjdCcsIHthc3NpZ246IHJlcXVpcmUoJy4vX29iamVjdC1hc3NpZ24nKX0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LmFzc2lnbi5qc1xuLy8gbW9kdWxlIGlkID0gODlcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDcgMTAgMTEgMTIgMTMgMTUgMTYgMTgiLCIvLyAxOS4xLjIuMTQgT2JqZWN0LmtleXMoTylcbnZhciB0b09iamVjdCA9IHJlcXVpcmUoJy4vX3RvLW9iamVjdCcpXG4gICwgJGtleXMgICAgPSByZXF1aXJlKCcuL19vYmplY3Qta2V5cycpO1xuXG5yZXF1aXJlKCcuL19vYmplY3Qtc2FwJykoJ2tleXMnLCBmdW5jdGlvbigpe1xuICByZXR1cm4gZnVuY3Rpb24ga2V5cyhpdCl7XG4gICAgcmV0dXJuICRrZXlzKHRvT2JqZWN0KGl0KSk7XG4gIH07XG59KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5rZXlzLmpzXG4vLyBtb2R1bGUgaWQgPSA5MFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDggOSAxMCAxNSAxOSAyMCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuZXhwb3J0IGRlZmF1bHQge1xuICBtYWluRGl2OiAnI29yZGVyLXZpZXctcGFnZScsXG4gIG9yZGVyUGF5bWVudERldGFpbHNCdG46ICcuanMtcGF5bWVudC1kZXRhaWxzLWJ0bicsXG4gIG9yZGVyUGF5bWVudEZvcm1BbW91bnRJbnB1dDogJyNvcmRlcl9wYXltZW50X2Ftb3VudCcsXG4gIG9yZGVyUGF5bWVudEludm9pY2VTZWxlY3Q6ICcjb3JkZXJfcGF5bWVudF9pZF9pbnZvaWNlJyxcbiAgdmlld09yZGVyUGF5bWVudHNCbG9jazogJyN2aWV3X29yZGVyX3BheW1lbnRzX2Jsb2NrJyxcbiAgdmlld09yZGVyUGF5bWVudHNBbGVydDogJy5qcy12aWV3LW9yZGVyLXBheW1lbnRzLWFsZXJ0JyxcbiAgcHJpdmF0ZU5vdGVUb2dnbGVCdG46ICcuanMtcHJpdmF0ZS1ub3RlLXRvZ2dsZS1idG4nLFxuICBwcml2YXRlTm90ZUJsb2NrOiAnLmpzLXByaXZhdGUtbm90ZS1ibG9jaycsXG4gIHByaXZhdGVOb3RlSW5wdXQ6ICcjcHJpdmF0ZV9ub3RlX25vdGUnLFxuICBwcml2YXRlTm90ZVN1Ym1pdEJ0bjogJy5qcy1wcml2YXRlLW5vdGUtYnRuJyxcbiAgYWRkQ2FydFJ1bGVNb2RhbDogJyNhZGRPcmRlckRpc2NvdW50TW9kYWwnLFxuICBhZGRDYXJ0UnVsZUludm9pY2VJZFNlbGVjdDogJyNhZGRfb3JkZXJfY2FydF9ydWxlX2ludm9pY2VfaWQnLFxuICBhZGRDYXJ0UnVsZU5hbWVJbnB1dDogJyNhZGRfb3JkZXJfY2FydF9ydWxlX25hbWUnLFxuICBhZGRDYXJ0UnVsZVR5cGVTZWxlY3Q6ICcjYWRkX29yZGVyX2NhcnRfcnVsZV90eXBlJyxcbiAgYWRkQ2FydFJ1bGVWYWx1ZUlucHV0OiAnI2FkZF9vcmRlcl9jYXJ0X3J1bGVfdmFsdWUnLFxuICBhZGRDYXJ0UnVsZVZhbHVlVW5pdDogJyNhZGRfb3JkZXJfY2FydF9ydWxlX3ZhbHVlX3VuaXQnLFxuICBhZGRDYXJ0UnVsZVN1Ym1pdDogJyNhZGRfb3JkZXJfY2FydF9ydWxlX3N1Ym1pdCcsXG4gIGNhcnRSdWxlSGVscFRleHQ6ICcuanMtY2FydC1ydWxlLXZhbHVlLWhlbHAnLFxuICB1cGRhdGVPcmRlclN0YXR1c0FjdGlvbkJ0bjogJyN1cGRhdGVfb3JkZXJfc3RhdHVzX2FjdGlvbl9idG4nLFxuICB1cGRhdGVPcmRlclN0YXR1c0FjdGlvbklucHV0OiAnI3VwZGF0ZV9vcmRlcl9zdGF0dXNfYWN0aW9uX2lucHV0JyxcbiAgdXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25JbnB1dFdyYXBwZXI6ICcjdXBkYXRlX29yZGVyX3N0YXR1c19hY3Rpb25faW5wdXRfd3JhcHBlcicsXG4gIHVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uRm9ybTogJyN1cGRhdGVfb3JkZXJfc3RhdHVzX2FjdGlvbl9mb3JtJyxcbiAgc2hvd09yZGVyU2hpcHBpbmdVcGRhdGVNb2RhbEJ0bjogJy5qcy11cGRhdGUtc2hpcHBpbmctYnRuJyxcbiAgdXBkYXRlT3JkZXJTaGlwcGluZ1RyYWNraW5nTnVtYmVySW5wdXQ6ICcjdXBkYXRlX29yZGVyX3NoaXBwaW5nX3RyYWNraW5nX251bWJlcicsXG4gIHVwZGF0ZU9yZGVyU2hpcHBpbmdDdXJyZW50T3JkZXJDYXJyaWVySWRJbnB1dDogJyN1cGRhdGVfb3JkZXJfc2hpcHBpbmdfY3VycmVudF9vcmRlcl9jYXJyaWVyX2lkJyxcbiAgdXBkYXRlQ3VzdG9tZXJBZGRyZXNzTW9kYWw6ICcjdXBkYXRlQ3VzdG9tZXJBZGRyZXNzTW9kYWwnLFxuICBvcGVuT3JkZXJBZGRyZXNzVXBkYXRlTW9kYWxCdG46ICcuanMtdXBkYXRlLWN1c3RvbWVyLWFkZHJlc3MtbW9kYWwtYnRuJyxcbiAgdXBkYXRlT3JkZXJBZGRyZXNzVHlwZUlucHV0OiAnI2NoYW5nZV9vcmRlcl9hZGRyZXNzX2FkZHJlc3NfdHlwZScsXG4gIGRlbGl2ZXJ5QWRkcmVzc0VkaXRCdG46ICcjanMtZGVsaXZlcnktYWRkcmVzcy1lZGl0LWJ0bicsXG4gIGludm9pY2VBZGRyZXNzRWRpdEJ0bjogJyNqcy1pbnZvaWNlLWFkZHJlc3MtZWRpdC1idG4nLFxuICBvcmRlck1lc3NhZ2VOYW1lU2VsZWN0OiAnI29yZGVyX21lc3NhZ2Vfb3JkZXJfbWVzc2FnZScsXG4gIG9yZGVyTWVzc2FnZXNDb250YWluZXI6ICcuanMtb3JkZXItbWVzc2FnZXMtY29udGFpbmVyJyxcbiAgb3JkZXJNZXNzYWdlOiAnI29yZGVyX21lc3NhZ2VfbWVzc2FnZScsXG4gIG9yZGVyTWVzc2FnZUNoYW5nZVdhcm5pbmc6ICcuanMtbWVzc2FnZS1jaGFuZ2Utd2FybmluZycsXG4gIG9yZGVyRG9jdW1lbnRzVGFiQ291bnQ6ICcjb3JkZXJEb2N1bWVudHNUYWIgLmNvdW50JyxcbiAgb3JkZXJEb2N1bWVudHNUYWJCb2R5OiAnI29yZGVyRG9jdW1lbnRzVGFiQ29udGVudCAuY2FyZC1ib2R5JyxcbiAgb3JkZXJTaGlwcGluZ1RhYkNvdW50OiAnI29yZGVyU2hpcHBpbmdUYWIgLmNvdW50JyxcbiAgb3JkZXJTaGlwcGluZ1RhYkJvZHk6ICcjb3JkZXJTaGlwcGluZ1RhYkNvbnRlbnQgLmNhcmQtYm9keScsXG4gIGFsbE1lc3NhZ2VzTW9kYWw6ICcjdmlld19hbGxfbWVzc2FnZXNfbW9kYWwnLFxuICBhbGxNZXNzYWdlc0xpc3Q6ICcjYWxsLW1lc3NhZ2VzLWxpc3QnLFxuICBvcGVuQWxsTWVzc2FnZXNCdG46ICcuanMtb3Blbi1hbGwtbWVzc2FnZXMtYnRuJyxcbiAgLy8gUHJvZHVjdHMgdGFibGUgZWxlbWVudHNcbiAgcHJvZHVjdE9yaWdpbmFsUG9zaXRpb246ICcjb3JkZXJQcm9kdWN0c09yaWdpbmFsUG9zaXRpb24nLFxuICBwcm9kdWN0TW9kaWZpY2F0aW9uUG9zaXRpb246ICcjb3JkZXJQcm9kdWN0c01vZGlmaWNhdGlvblBvc2l0aW9uJyxcbiAgcHJvZHVjdHNQYW5lbDogJyNvcmRlclByb2R1Y3RzUGFuZWwnLFxuICBwcm9kdWN0c0NvdW50OiAnI29yZGVyUHJvZHVjdHNQYW5lbENvdW50JyxcbiAgcHJvZHVjdERlbGV0ZUJ0bjogJy5qcy1vcmRlci1wcm9kdWN0LWRlbGV0ZS1idG4nLFxuICBwcm9kdWN0c1RhYmxlOiAnI29yZGVyUHJvZHVjdHNUYWJsZScsXG4gIHByb2R1Y3RzUGFnaW5hdGlvbjogJy5vcmRlci1wcm9kdWN0LXBhZ2luYXRpb24nLFxuICBwcm9kdWN0c05hdlBhZ2luYXRpb246ICcjb3JkZXJQcm9kdWN0c05hdlBhZ2luYXRpb24nLFxuICBwcm9kdWN0c1RhYmxlUGFnaW5hdGlvbjogJyNvcmRlclByb2R1Y3RzVGFibGVQYWdpbmF0aW9uJyxcbiAgcHJvZHVjdHNUYWJsZVBhZ2luYXRpb25OZXh0OiAnI29yZGVyUHJvZHVjdHNUYWJsZVBhZ2luYXRpb25OZXh0JyxcbiAgcHJvZHVjdHNUYWJsZVBhZ2luYXRpb25QcmV2OiAnI29yZGVyUHJvZHVjdHNUYWJsZVBhZ2luYXRpb25QcmV2JyxcbiAgcHJvZHVjdHNUYWJsZVBhZ2luYXRpb25MaW5rOiAnLnBhZ2UtaXRlbTpub3QoLmQtbm9uZSk6bm90KCNvcmRlclByb2R1Y3RzVGFibGVQYWdpbmF0aW9uTmV4dCk6bm90KCNvcmRlclByb2R1Y3RzVGFibGVQYWdpbmF0aW9uUHJldikgLnBhZ2UtbGluaycsXG4gIHByb2R1Y3RzVGFibGVQYWdpbmF0aW9uQWN0aXZlOiAnI29yZGVyUHJvZHVjdHNUYWJsZVBhZ2luYXRpb24gLnBhZ2UtaXRlbS5hY3RpdmUgc3BhbicsXG4gIHByb2R1Y3RzVGFibGVQYWdpbmF0aW9uVGVtcGxhdGU6ICcjb3JkZXJQcm9kdWN0c1RhYmxlUGFnaW5hdGlvbiAucGFnZS1pdGVtLmQtbm9uZScsXG4gIHByb2R1Y3RzVGFibGVQYWdpbmF0aW9uTnVtYmVyU2VsZWN0b3I6ICcjb3JkZXJQcm9kdWN0c1RhYmxlUGFnaW5hdGlvbk51bWJlclNlbGVjdG9yJyxcbiAgcHJvZHVjdHNUYWJsZVJvdzogKHByb2R1Y3RJZCkgPT4gYCNvcmRlclByb2R1Y3RfJHtwcm9kdWN0SWR9YCxcbiAgcHJvZHVjdHNUYWJsZVJvd0VkaXRlZDogKHByb2R1Y3RJZCkgPT4gYCNlZGl0T3JkZXJQcm9kdWN0XyR7cHJvZHVjdElkfWAsXG4gIHByb2R1Y3RzVGFibGVSb3dzOiAndHIuY2VsbFByb2R1Y3QnLFxuICBwcm9kdWN0c0NlbGxMb2NhdGlvbjogJ3RyIC5jZWxsUHJvZHVjdExvY2F0aW9uJyxcbiAgcHJvZHVjdHNDZWxsUmVmdW5kZWQ6ICd0ciAuY2VsbFByb2R1Y3RSZWZ1bmRlZCcsXG4gIHByb2R1Y3RzQ2VsbExvY2F0aW9uRGlzcGxheWVkOiAndHI6bm90KC5kLW5vbmUpIC5jZWxsUHJvZHVjdExvY2F0aW9uJyxcbiAgcHJvZHVjdHNDZWxsUmVmdW5kZWREaXNwbGF5ZWQ6ICd0cjpub3QoLmQtbm9uZSkgLmNlbGxQcm9kdWN0UmVmdW5kZWQnLFxuICBwcm9kdWN0c1RhYmxlQ3VzdG9taXphdGlvblJvd3M6ICcjb3JkZXJQcm9kdWN0c1RhYmxlIC5vcmRlci1wcm9kdWN0LWN1c3RvbWl6YXRpb24nLFxuICBwcm9kdWN0RWRpdEJ1dHRvbnM6ICcuanMtb3JkZXItcHJvZHVjdC1lZGl0LWJ0bicsXG4gIHByb2R1Y3RFZGl0QnRuOiAocHJvZHVjdElkKSA9PiBgI29yZGVyUHJvZHVjdF8ke3Byb2R1Y3RJZH0gLmpzLW9yZGVyLXByb2R1Y3QtZWRpdC1idG5gLFxuICBwcm9kdWN0QWRkQnRuOiAnI2FkZFByb2R1Y3RCdG4nLFxuICBwcm9kdWN0QWN0aW9uQnRuOiAnLmpzLXByb2R1Y3QtYWN0aW9uLWJ0bicsXG4gIHByb2R1Y3RBZGRBY3Rpb25CdG46ICcjYWRkX3Byb2R1Y3Rfcm93X2FkZCcsXG4gIHByb2R1Y3RDYW5jZWxBZGRCdG46ICcjYWRkX3Byb2R1Y3Rfcm93X2NhbmNlbCcsXG4gIHByb2R1Y3RBZGRSb3c6ICcjYWRkUHJvZHVjdFRhYmxlUm93JyxcbiAgcHJvZHVjdFNlYXJjaElucHV0OiAnI2FkZF9wcm9kdWN0X3Jvd19zZWFyY2gnLFxuICBwcm9kdWN0U2VhcmNoSW5wdXRBdXRvY29tcGxldGU6ICcjYWRkUHJvZHVjdFRhYmxlUm93IC5kcm9wZG93bicsXG4gIHByb2R1Y3RTZWFyY2hJbnB1dEF1dG9jb21wbGV0ZU1lbnU6ICcjYWRkUHJvZHVjdFRhYmxlUm93IC5kcm9wZG93biAuZHJvcGRvd24tbWVudScsXG4gIHByb2R1Y3RBZGRJZElucHV0OiAnI2FkZF9wcm9kdWN0X3Jvd19wcm9kdWN0X2lkJyxcbiAgcHJvZHVjdEFkZFRheFJhdGVJbnB1dDogJyNhZGRfcHJvZHVjdF9yb3dfdGF4X3JhdGUnLFxuICBwcm9kdWN0QWRkQ29tYmluYXRpb25zQmxvY2s6ICcjYWRkUHJvZHVjdENvbWJpbmF0aW9ucycsXG4gIHByb2R1Y3RBZGRDb21iaW5hdGlvbnNTZWxlY3Q6ICcjYWRkUHJvZHVjdENvbWJpbmF0aW9uSWQnLFxuICBwcm9kdWN0QWRkUHJpY2VUYXhFeGNsSW5wdXQ6ICcjYWRkX3Byb2R1Y3Rfcm93X3ByaWNlX3RheF9leGNsdWRlZCcsXG4gIHByb2R1Y3RBZGRQcmljZVRheEluY2xJbnB1dDogJyNhZGRfcHJvZHVjdF9yb3dfcHJpY2VfdGF4X2luY2x1ZGVkJyxcbiAgcHJvZHVjdEFkZFF1YW50aXR5SW5wdXQ6ICcjYWRkX3Byb2R1Y3Rfcm93X3F1YW50aXR5JyxcbiAgcHJvZHVjdEFkZEF2YWlsYWJsZVRleHQ6ICcjYWRkUHJvZHVjdEF2YWlsYWJsZScsXG4gIHByb2R1Y3RBZGRMb2NhdGlvblRleHQ6ICcjYWRkUHJvZHVjdExvY2F0aW9uJyxcbiAgcHJvZHVjdEFkZFRvdGFsUHJpY2VUZXh0OiAnI2FkZFByb2R1Y3RUb3RhbFByaWNlJyxcbiAgcHJvZHVjdEFkZEludm9pY2VTZWxlY3Q6ICcjYWRkX3Byb2R1Y3Rfcm93X2ludm9pY2UnLFxuICBwcm9kdWN0QWRkRnJlZVNoaXBwaW5nU2VsZWN0OiAnI2FkZF9wcm9kdWN0X3Jvd19mcmVlX3NoaXBwaW5nJyxcbiAgcHJvZHVjdEFkZE5ld0ludm9pY2VJbmZvOiAnI2FkZFByb2R1Y3ROZXdJbnZvaWNlSW5mbycsXG4gIHByb2R1Y3RFZGl0U2F2ZUJ0bjogJy5wcm9kdWN0RWRpdFNhdmVCdG4nLFxuICBwcm9kdWN0RWRpdENhbmNlbEJ0bjogJy5wcm9kdWN0RWRpdENhbmNlbEJ0bicsXG4gIHByb2R1Y3RFZGl0Um93VGVtcGxhdGU6ICcjZWRpdFByb2R1Y3RUYWJsZVJvd1RlbXBsYXRlJyxcbiAgcHJvZHVjdEVkaXRSb3c6ICcuZWRpdFByb2R1Y3RSb3cnLFxuICBwcm9kdWN0RWRpdEltYWdlOiAnLmNlbGxQcm9kdWN0SW1nJyxcbiAgcHJvZHVjdEVkaXROYW1lOiAnLmNlbGxQcm9kdWN0TmFtZScsXG4gIHByb2R1Y3RFZGl0VW5pdFByaWNlOiAnLmNlbGxQcm9kdWN0VW5pdFByaWNlJyxcbiAgcHJvZHVjdEVkaXRRdWFudGl0eTogJy5jZWxsUHJvZHVjdFF1YW50aXR5JyxcbiAgcHJvZHVjdEVkaXRBdmFpbGFibGVRdWFudGl0eTogJy5jZWxsUHJvZHVjdEF2YWlsYWJsZVF1YW50aXR5JyxcbiAgcHJvZHVjdEVkaXRUb3RhbFByaWNlOiAnLmNlbGxQcm9kdWN0VG90YWxQcmljZScsXG4gIHByb2R1Y3RFZGl0UHJpY2VUYXhFeGNsSW5wdXQ6ICcuZWRpdFByb2R1Y3RQcmljZVRheEV4Y2wnLFxuICBwcm9kdWN0RWRpdFByaWNlVGF4SW5jbElucHV0OiAnLmVkaXRQcm9kdWN0UHJpY2VUYXhJbmNsJyxcbiAgcHJvZHVjdEVkaXRJbnZvaWNlU2VsZWN0OiAnLmVkaXRQcm9kdWN0SW52b2ljZScsXG4gIHByb2R1Y3RFZGl0UXVhbnRpdHlJbnB1dDogJy5lZGl0UHJvZHVjdFF1YW50aXR5JyxcbiAgcHJvZHVjdEVkaXRMb2NhdGlvblRleHQ6ICcuZWRpdFByb2R1Y3RMb2NhdGlvbicsXG4gIHByb2R1Y3RFZGl0QXZhaWxhYmxlVGV4dDogJy5lZGl0UHJvZHVjdEF2YWlsYWJsZScsXG4gIHByb2R1Y3RFZGl0VG90YWxQcmljZVRleHQ6ICcuZWRpdFByb2R1Y3RUb3RhbFByaWNlJyxcbiAgLy8gUHJvZHVjdCBEaXNjb3VudCBMaXN0XG4gIHByb2R1Y3REaXNjb3VudExpc3Q6IHtcbiAgICBsaXN0OiAnLnRhYmxlLmRpc2NvdW50TGlzdCcsXG4gIH0sXG4gIC8vIFByb2R1Y3QgUGFjayBNb2RhbFxuICBwcm9kdWN0UGFja01vZGFsOiB7XG4gICAgbW9kYWw6ICcjcHJvZHVjdC1wYWNrLW1vZGFsJyxcbiAgICB0YWJsZTogJyNwcm9kdWN0LXBhY2stbW9kYWwtdGFibGUgdGJvZHknLFxuICAgIHJvd3M6ICcjcHJvZHVjdC1wYWNrLW1vZGFsLXRhYmxlIHRib2R5IHRyOm5vdCgjdGVtcGxhdGUtcGFjay10YWJsZS1yb3cpJyxcbiAgICB0ZW1wbGF0ZTogJyN0ZW1wbGF0ZS1wYWNrLXRhYmxlLXJvdycsXG4gICAgcHJvZHVjdDoge1xuICAgICAgaW1nOiAnLmNlbGwtcHJvZHVjdC1pbWcgaW1nJyxcbiAgICAgIGxpbms6ICcuY2VsbC1wcm9kdWN0LW5hbWUgYScsXG4gICAgICBuYW1lOiAnLmNlbGwtcHJvZHVjdC1uYW1lIC5wcm9kdWN0LW5hbWUnLFxuICAgICAgcmVmOiAnLmNlbGwtcHJvZHVjdC1uYW1lIC5wcm9kdWN0LXJlZmVyZW5jZScsXG4gICAgICBzdXBwbGllclJlZjogJy5jZWxsLXByb2R1Y3QtbmFtZSAucHJvZHVjdC1zdXBwbGllci1yZWZlcmVuY2UnLFxuICAgICAgcXVhbnRpdHk6ICcuY2VsbC1wcm9kdWN0LXF1YW50aXR5JyxcbiAgICAgIGF2YWlsYWJsZVF1YW50aXR5OiAnLmNlbGwtcHJvZHVjdC1hdmFpbGFibGUtcXVhbnRpdHknLFxuICAgIH0sXG4gIH0sXG4gIC8vIE9yZGVyIHByaWNlIGVsZW1lbnRzXG4gIG9yZGVyUHJvZHVjdHNUb3RhbDogJyNvcmRlclByb2R1Y3RzVG90YWwnLFxuICBvcmRlckRpc2NvdW50c1RvdGFsQ29udGFpbmVyOiAnI29yZGVyLWRpc2NvdW50cy10b3RhbC1jb250YWluZXInLFxuICBvcmRlckRpc2NvdW50c1RvdGFsOiAnI29yZGVyRGlzY291bnRzVG90YWwnLFxuICBvcmRlcldyYXBwaW5nVG90YWw6ICcjb3JkZXJXcmFwcGluZ1RvdGFsJyxcbiAgb3JkZXJTaGlwcGluZ1RvdGFsQ29udGFpbmVyOiAnI29yZGVyLXNoaXBwaW5nLXRvdGFsLWNvbnRhaW5lcicsXG4gIG9yZGVyU2hpcHBpbmdUb3RhbDogJyNvcmRlclNoaXBwaW5nVG90YWwnLFxuICBvcmRlclRheGVzVG90YWw6ICcjb3JkZXJUYXhlc1RvdGFsJyxcbiAgb3JkZXJUb3RhbDogJyNvcmRlclRvdGFsJyxcbiAgb3JkZXJIb29rVGFic0NvbnRhaW5lcjogJyNvcmRlcl9ob29rX3RhYnMnLFxuICAvLyBQcm9kdWN0IGNhbmNlbC9yZWZ1bmQgZWxlbWVudHNcbiAgY2FuY2VsUHJvZHVjdDoge1xuICAgIGZvcm06ICdmb3JtW25hbWU9XCJjYW5jZWxfcHJvZHVjdFwiXScsXG4gICAgYnV0dG9uczoge1xuICAgICAgYWJvcnQ6ICdidXR0b24uY2FuY2VsLXByb2R1Y3QtZWxlbWVudC1hYm9ydCcsXG4gICAgICBzYXZlOiAnI2NhbmNlbF9wcm9kdWN0X3NhdmUnLFxuICAgICAgcGFydGlhbFJlZnVuZDogJ2J1dHRvbi5wYXJ0aWFsLXJlZnVuZC1kaXNwbGF5JyxcbiAgICAgIHN0YW5kYXJkUmVmdW5kOiAnYnV0dG9uLnN0YW5kYXJkLXJlZnVuZC1kaXNwbGF5JyxcbiAgICAgIHJldHVyblByb2R1Y3Q6ICdidXR0b24ucmV0dXJuLXByb2R1Y3QtZGlzcGxheScsXG4gICAgICBjYW5jZWxQcm9kdWN0czogJ2J1dHRvbi5jYW5jZWwtcHJvZHVjdC1kaXNwbGF5JyxcbiAgICB9LFxuICAgIGlucHV0czoge1xuICAgICAgcXVhbnRpdHk6ICcuY2FuY2VsLXByb2R1Y3QtcXVhbnRpdHkgaW5wdXQnLFxuICAgICAgYW1vdW50OiAnLmNhbmNlbC1wcm9kdWN0LWFtb3VudCBpbnB1dCcsXG4gICAgICBzZWxlY3RvcjogJy5jYW5jZWwtcHJvZHVjdC1zZWxlY3RvciBpbnB1dCcsXG4gICAgfSxcbiAgICB0YWJsZToge1xuICAgICAgY2VsbDogJy5jYW5jZWwtcHJvZHVjdC1jZWxsJyxcbiAgICAgIGhlYWRlcjogJ3RoLmNhbmNlbC1wcm9kdWN0LWVsZW1lbnQgcCcsXG4gICAgICBhY3Rpb25zOiAndGQuY2VsbFByb2R1Y3RBY3Rpb25zLCB0aC5wcm9kdWN0X2FjdGlvbnMnLFxuICAgIH0sXG4gICAgY2hlY2tib3hlczoge1xuICAgICAgcmVzdG9jazogJyNjYW5jZWxfcHJvZHVjdF9yZXN0b2NrJyxcbiAgICAgIGNyZWRpdFNsaXA6ICcjY2FuY2VsX3Byb2R1Y3RfY3JlZGl0X3NsaXAnLFxuICAgICAgdm91Y2hlcjogJyNjYW5jZWxfcHJvZHVjdF92b3VjaGVyJyxcbiAgICB9LFxuICAgIHJhZGlvczoge1xuICAgICAgdm91Y2hlclJlZnVuZFR5cGU6IHtcbiAgICAgICAgcHJvZHVjdFByaWNlczogJ2lucHV0W3ZvdWNoZXItcmVmdW5kLXR5cGU9XCIwXCJdJyxcbiAgICAgICAgcHJvZHVjdFByaWNlc1ZvdWNoZXJFeGNsdWRlZDogJ2lucHV0W3ZvdWNoZXItcmVmdW5kLXR5cGU9XCIxXCJdJyxcbiAgICAgICAgbmVnYXRpdmVFcnJvck1lc3NhZ2U6ICcudm91Y2hlci1yZWZ1bmQtdHlwZS1uZWdhdGl2ZS1lcnJvcicsXG4gICAgICB9LFxuICAgIH0sXG4gICAgdG9nZ2xlOiB7XG4gICAgICBwYXJ0aWFsUmVmdW5kOiAnLmNhbmNlbC1wcm9kdWN0LWVsZW1lbnQ6bm90KC5oaWRkZW4pOm5vdCguc2hpcHBpbmctcmVmdW5kKSwgLmNhbmNlbC1wcm9kdWN0LWFtb3VudCcsXG4gICAgICBzdGFuZGFyZFJlZnVuZDogJy5jYW5jZWwtcHJvZHVjdC1lbGVtZW50Om5vdCguaGlkZGVuKTpub3QoLnNoaXBwaW5nLXJlZnVuZC1hbW91bnQpOm5vdCgucmVzdG9jay1wcm9kdWN0cyksIC5jYW5jZWwtcHJvZHVjdC1zZWxlY3RvcicsXG4gICAgICByZXR1cm5Qcm9kdWN0OiAnLmNhbmNlbC1wcm9kdWN0LWVsZW1lbnQ6bm90KC5oaWRkZW4pOm5vdCguc2hpcHBpbmctcmVmdW5kLWFtb3VudCksIC5jYW5jZWwtcHJvZHVjdC1zZWxlY3RvcicsXG4gICAgICBjYW5jZWxQcm9kdWN0czogJy5jYW5jZWwtcHJvZHVjdC1lbGVtZW50Om5vdCguaGlkZGVuKTpub3QoLnNoaXBwaW5nLXJlZnVuZC1hbW91bnQpOm5vdCguc2hpcHBpbmctcmVmdW5kKTpub3QoLnJlc3RvY2stcHJvZHVjdHMpOm5vdCgucmVmdW5kLWNyZWRpdC1zbGlwKTpub3QoLnJlZnVuZC12b3VjaGVyKTpub3QoLnZvdWNoZXItcmVmdW5kLXR5cGUpLCAuY2FuY2VsLXByb2R1Y3Qtc2VsZWN0b3InLFxuICAgIH0sXG4gIH0sXG4gIHByaW50T3JkZXJWaWV3UGFnZUJ1dHRvbjogJy5qcy1wcmludC1vcmRlci12aWV3LXBhZ2UnLFxuICByZWZyZXNoUHJvZHVjdHNMaXN0TG9hZGluZ1NwaW5uZXI6ICcjb3JkZXJQcm9kdWN0c1BhbmVsIC5zcGlubmVyLW9yZGVyLXByb2R1Y3RzLWNvbnRhaW5lciNvcmRlclByb2R1Y3RzTG9hZGluZycsXG59O1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvT3JkZXJWaWV3UGFnZU1hcC5qcyIsIm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi9fZ2xvYmFsJykuZG9jdW1lbnQgJiYgZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faHRtbC5qc1xuLy8gbW9kdWxlIGlkID0gOTNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IDE0IiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihkb25lLCB2YWx1ZSl7XG4gIHJldHVybiB7dmFsdWU6IHZhbHVlLCBkb25lOiAhIWRvbmV9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXItc3RlcC5qc1xuLy8gbW9kdWxlIGlkID0gOTRcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IDE0IiwidmFyIGNsYXNzb2YgICA9IHJlcXVpcmUoJy4vX2NsYXNzb2YnKVxuICAsIElURVJBVE9SICA9IHJlcXVpcmUoJy4vX3drcycpKCdpdGVyYXRvcicpXG4gICwgSXRlcmF0b3JzID0gcmVxdWlyZSgnLi9faXRlcmF0b3JzJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4vX2NvcmUnKS5nZXRJdGVyYXRvck1ldGhvZCA9IGZ1bmN0aW9uKGl0KXtcbiAgaWYoaXQgIT0gdW5kZWZpbmVkKXJldHVybiBpdFtJVEVSQVRPUl1cbiAgICB8fCBpdFsnQEBpdGVyYXRvciddXG4gICAgfHwgSXRlcmF0b3JzW2NsYXNzb2YoaXQpXTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2NvcmUuZ2V0LWl0ZXJhdG9yLW1ldGhvZC5qc1xuLy8gbW9kdWxlIGlkID0gOTVcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgMTQiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKCl7IC8qIGVtcHR5ICovIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hZGQtdG8tdW5zY29wYWJsZXMuanNcbi8vIG1vZHVsZSBpZCA9IDk2XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDggOSAxNCIsIid1c2Ugc3RyaWN0JztcbnZhciBjcmVhdGUgICAgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1jcmVhdGUnKVxuICAsIGRlc2NyaXB0b3IgICAgID0gcmVxdWlyZSgnLi9fcHJvcGVydHktZGVzYycpXG4gICwgc2V0VG9TdHJpbmdUYWcgPSByZXF1aXJlKCcuL19zZXQtdG8tc3RyaW5nLXRhZycpXG4gICwgSXRlcmF0b3JQcm90b3R5cGUgPSB7fTtcblxuLy8gMjUuMS4yLjEuMSAlSXRlcmF0b3JQcm90b3R5cGUlW0BAaXRlcmF0b3JdKClcbnJlcXVpcmUoJy4vX2hpZGUnKShJdGVyYXRvclByb3RvdHlwZSwgcmVxdWlyZSgnLi9fd2tzJykoJ2l0ZXJhdG9yJyksIGZ1bmN0aW9uKCl7IHJldHVybiB0aGlzOyB9KTtcblxubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihDb25zdHJ1Y3RvciwgTkFNRSwgbmV4dCl7XG4gIENvbnN0cnVjdG9yLnByb3RvdHlwZSA9IGNyZWF0ZShJdGVyYXRvclByb3RvdHlwZSwge25leHQ6IGRlc2NyaXB0b3IoMSwgbmV4dCl9KTtcbiAgc2V0VG9TdHJpbmdUYWcoQ29uc3RydWN0b3IsIE5BTUUgKyAnIEl0ZXJhdG9yJyk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXRlci1jcmVhdGUuanNcbi8vIG1vZHVsZSBpZCA9IDk3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDggOSAxNCIsInZhciBkUCAgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1kcCcpXG4gICwgYW5PYmplY3QgPSByZXF1aXJlKCcuL19hbi1vYmplY3QnKVxuICAsIGdldEtleXMgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWtleXMnKTtcblxubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpID8gT2JqZWN0LmRlZmluZVByb3BlcnRpZXMgOiBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0aWVzKE8sIFByb3BlcnRpZXMpe1xuICBhbk9iamVjdChPKTtcbiAgdmFyIGtleXMgICA9IGdldEtleXMoUHJvcGVydGllcylcbiAgICAsIGxlbmd0aCA9IGtleXMubGVuZ3RoXG4gICAgLCBpID0gMFxuICAgICwgUDtcbiAgd2hpbGUobGVuZ3RoID4gaSlkUC5mKE8sIFAgPSBrZXlzW2krK10sIFByb3BlcnRpZXNbUF0pO1xuICByZXR1cm4gTztcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZHBzLmpzXG4vLyBtb2R1bGUgaWQgPSA5OFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA4IDkgMTQiLCJ2YXIgdG9JbnRlZ2VyID0gcmVxdWlyZSgnLi9fdG8taW50ZWdlcicpXG4gICwgZGVmaW5lZCAgID0gcmVxdWlyZSgnLi9fZGVmaW5lZCcpO1xuLy8gdHJ1ZSAgLT4gU3RyaW5nI2F0XG4vLyBmYWxzZSAtPiBTdHJpbmcjY29kZVBvaW50QXRcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oVE9fU1RSSU5HKXtcbiAgcmV0dXJuIGZ1bmN0aW9uKHRoYXQsIHBvcyl7XG4gICAgdmFyIHMgPSBTdHJpbmcoZGVmaW5lZCh0aGF0KSlcbiAgICAgICwgaSA9IHRvSW50ZWdlcihwb3MpXG4gICAgICAsIGwgPSBzLmxlbmd0aFxuICAgICAgLCBhLCBiO1xuICAgIGlmKGkgPCAwIHx8IGkgPj0gbClyZXR1cm4gVE9fU1RSSU5HID8gJycgOiB1bmRlZmluZWQ7XG4gICAgYSA9IHMuY2hhckNvZGVBdChpKTtcbiAgICByZXR1cm4gYSA8IDB4ZDgwMCB8fCBhID4gMHhkYmZmIHx8IGkgKyAxID09PSBsIHx8IChiID0gcy5jaGFyQ29kZUF0KGkgKyAxKSkgPCAweGRjMDAgfHwgYiA+IDB4ZGZmZlxuICAgICAgPyBUT19TVFJJTkcgPyBzLmNoYXJBdChpKSA6IGFcbiAgICAgIDogVE9fU1RSSU5HID8gcy5zbGljZShpLCBpICsgMikgOiAoYSAtIDB4ZDgwMCA8PCAxMCkgKyAoYiAtIDB4ZGMwMCkgKyAweDEwMDAwO1xuICB9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3N0cmluZy1hdC5qc1xuLy8gbW9kdWxlIGlkID0gOTlcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IDE0IiwiJ3VzZSBzdHJpY3QnO1xudmFyIGFkZFRvVW5zY29wYWJsZXMgPSByZXF1aXJlKCcuL19hZGQtdG8tdW5zY29wYWJsZXMnKVxuICAsIHN0ZXAgICAgICAgICAgICAgPSByZXF1aXJlKCcuL19pdGVyLXN0ZXAnKVxuICAsIEl0ZXJhdG9ycyAgICAgICAgPSByZXF1aXJlKCcuL19pdGVyYXRvcnMnKVxuICAsIHRvSU9iamVjdCAgICAgICAgPSByZXF1aXJlKCcuL190by1pb2JqZWN0Jyk7XG5cbi8vIDIyLjEuMy40IEFycmF5LnByb3RvdHlwZS5lbnRyaWVzKClcbi8vIDIyLjEuMy4xMyBBcnJheS5wcm90b3R5cGUua2V5cygpXG4vLyAyMi4xLjMuMjkgQXJyYXkucHJvdG90eXBlLnZhbHVlcygpXG4vLyAyMi4xLjMuMzAgQXJyYXkucHJvdG90eXBlW0BAaXRlcmF0b3JdKClcbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi9faXRlci1kZWZpbmUnKShBcnJheSwgJ0FycmF5JywgZnVuY3Rpb24oaXRlcmF0ZWQsIGtpbmQpe1xuICB0aGlzLl90ID0gdG9JT2JqZWN0KGl0ZXJhdGVkKTsgLy8gdGFyZ2V0XG4gIHRoaXMuX2kgPSAwOyAgICAgICAgICAgICAgICAgICAvLyBuZXh0IGluZGV4XG4gIHRoaXMuX2sgPSBraW5kOyAgICAgICAgICAgICAgICAvLyBraW5kXG4vLyAyMi4xLjUuMi4xICVBcnJheUl0ZXJhdG9yUHJvdG90eXBlJS5uZXh0KClcbn0sIGZ1bmN0aW9uKCl7XG4gIHZhciBPICAgICA9IHRoaXMuX3RcbiAgICAsIGtpbmQgID0gdGhpcy5fa1xuICAgICwgaW5kZXggPSB0aGlzLl9pKys7XG4gIGlmKCFPIHx8IGluZGV4ID49IE8ubGVuZ3RoKXtcbiAgICB0aGlzLl90ID0gdW5kZWZpbmVkO1xuICAgIHJldHVybiBzdGVwKDEpO1xuICB9XG4gIGlmKGtpbmQgPT0gJ2tleXMnICApcmV0dXJuIHN0ZXAoMCwgaW5kZXgpO1xuICBpZihraW5kID09ICd2YWx1ZXMnKXJldHVybiBzdGVwKDAsIE9baW5kZXhdKTtcbiAgcmV0dXJuIHN0ZXAoMCwgW2luZGV4LCBPW2luZGV4XV0pO1xufSwgJ3ZhbHVlcycpO1xuXG4vLyBhcmd1bWVudHNMaXN0W0BAaXRlcmF0b3JdIGlzICVBcnJheVByb3RvX3ZhbHVlcyUgKDkuNC40LjYsIDkuNC40LjcpXG5JdGVyYXRvcnMuQXJndW1lbnRzID0gSXRlcmF0b3JzLkFycmF5O1xuXG5hZGRUb1Vuc2NvcGFibGVzKCdrZXlzJyk7XG5hZGRUb1Vuc2NvcGFibGVzKCd2YWx1ZXMnKTtcbmFkZFRvVW5zY29wYWJsZXMoJ2VudHJpZXMnKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2LmFycmF5Lml0ZXJhdG9yLmpzXG4vLyBtb2R1bGUgaWQgPSAxMDBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IDE0IiwiXCJ1c2Ugc3RyaWN0XCI7XG5cbmV4cG9ydHMuX19lc01vZHVsZSA9IHRydWU7XG5cbnZhciBfaXRlcmF0b3IgPSByZXF1aXJlKFwiLi4vY29yZS1qcy9zeW1ib2wvaXRlcmF0b3JcIik7XG5cbnZhciBfaXRlcmF0b3IyID0gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChfaXRlcmF0b3IpO1xuXG52YXIgX3N5bWJvbCA9IHJlcXVpcmUoXCIuLi9jb3JlLWpzL3N5bWJvbFwiKTtcblxudmFyIF9zeW1ib2wyID0gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChfc3ltYm9sKTtcblxudmFyIF90eXBlb2YgPSB0eXBlb2YgX3N5bWJvbDIuZGVmYXVsdCA9PT0gXCJmdW5jdGlvblwiICYmIHR5cGVvZiBfaXRlcmF0b3IyLmRlZmF1bHQgPT09IFwic3ltYm9sXCIgPyBmdW5jdGlvbiAob2JqKSB7IHJldHVybiB0eXBlb2Ygb2JqOyB9IDogZnVuY3Rpb24gKG9iaikgeyByZXR1cm4gb2JqICYmIHR5cGVvZiBfc3ltYm9sMi5kZWZhdWx0ID09PSBcImZ1bmN0aW9uXCIgJiYgb2JqLmNvbnN0cnVjdG9yID09PSBfc3ltYm9sMi5kZWZhdWx0ICYmIG9iaiAhPT0gX3N5bWJvbDIuZGVmYXVsdC5wcm90b3R5cGUgPyBcInN5bWJvbFwiIDogdHlwZW9mIG9iajsgfTtcblxuZnVuY3Rpb24gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChvYmopIHsgcmV0dXJuIG9iaiAmJiBvYmouX19lc01vZHVsZSA/IG9iaiA6IHsgZGVmYXVsdDogb2JqIH07IH1cblxuZXhwb3J0cy5kZWZhdWx0ID0gdHlwZW9mIF9zeW1ib2wyLmRlZmF1bHQgPT09IFwiZnVuY3Rpb25cIiAmJiBfdHlwZW9mKF9pdGVyYXRvcjIuZGVmYXVsdCkgPT09IFwic3ltYm9sXCIgPyBmdW5jdGlvbiAob2JqKSB7XG4gIHJldHVybiB0eXBlb2Ygb2JqID09PSBcInVuZGVmaW5lZFwiID8gXCJ1bmRlZmluZWRcIiA6IF90eXBlb2Yob2JqKTtcbn0gOiBmdW5jdGlvbiAob2JqKSB7XG4gIHJldHVybiBvYmogJiYgdHlwZW9mIF9zeW1ib2wyLmRlZmF1bHQgPT09IFwiZnVuY3Rpb25cIiAmJiBvYmouY29uc3RydWN0b3IgPT09IF9zeW1ib2wyLmRlZmF1bHQgJiYgb2JqICE9PSBfc3ltYm9sMi5kZWZhdWx0LnByb3RvdHlwZSA/IFwic3ltYm9sXCIgOiB0eXBlb2Ygb2JqID09PSBcInVuZGVmaW5lZFwiID8gXCJ1bmRlZmluZWRcIiA6IF90eXBlb2Yob2JqKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy90eXBlb2YuanNcbi8vIG1vZHVsZSBpZCA9IDEwMlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwidmFyIE1FVEEgICAgID0gcmVxdWlyZSgnLi9fdWlkJykoJ21ldGEnKVxuICAsIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi9faXMtb2JqZWN0JylcbiAgLCBoYXMgICAgICA9IHJlcXVpcmUoJy4vX2hhcycpXG4gICwgc2V0RGVzYyAgPSByZXF1aXJlKCcuL19vYmplY3QtZHAnKS5mXG4gICwgaWQgICAgICAgPSAwO1xudmFyIGlzRXh0ZW5zaWJsZSA9IE9iamVjdC5pc0V4dGVuc2libGUgfHwgZnVuY3Rpb24oKXtcbiAgcmV0dXJuIHRydWU7XG59O1xudmFyIEZSRUVaRSA9ICFyZXF1aXJlKCcuL19mYWlscycpKGZ1bmN0aW9uKCl7XG4gIHJldHVybiBpc0V4dGVuc2libGUoT2JqZWN0LnByZXZlbnRFeHRlbnNpb25zKHt9KSk7XG59KTtcbnZhciBzZXRNZXRhID0gZnVuY3Rpb24oaXQpe1xuICBzZXREZXNjKGl0LCBNRVRBLCB7dmFsdWU6IHtcbiAgICBpOiAnTycgKyArK2lkLCAvLyBvYmplY3QgSURcbiAgICB3OiB7fSAgICAgICAgICAvLyB3ZWFrIGNvbGxlY3Rpb25zIElEc1xuICB9fSk7XG59O1xudmFyIGZhc3RLZXkgPSBmdW5jdGlvbihpdCwgY3JlYXRlKXtcbiAgLy8gcmV0dXJuIHByaW1pdGl2ZSB3aXRoIHByZWZpeFxuICBpZighaXNPYmplY3QoaXQpKXJldHVybiB0eXBlb2YgaXQgPT0gJ3N5bWJvbCcgPyBpdCA6ICh0eXBlb2YgaXQgPT0gJ3N0cmluZycgPyAnUycgOiAnUCcpICsgaXQ7XG4gIGlmKCFoYXMoaXQsIE1FVEEpKXtcbiAgICAvLyBjYW4ndCBzZXQgbWV0YWRhdGEgdG8gdW5jYXVnaHQgZnJvemVuIG9iamVjdFxuICAgIGlmKCFpc0V4dGVuc2libGUoaXQpKXJldHVybiAnRic7XG4gICAgLy8gbm90IG5lY2Vzc2FyeSB0byBhZGQgbWV0YWRhdGFcbiAgICBpZighY3JlYXRlKXJldHVybiAnRSc7XG4gICAgLy8gYWRkIG1pc3NpbmcgbWV0YWRhdGFcbiAgICBzZXRNZXRhKGl0KTtcbiAgLy8gcmV0dXJuIG9iamVjdCBJRFxuICB9IHJldHVybiBpdFtNRVRBXS5pO1xufTtcbnZhciBnZXRXZWFrID0gZnVuY3Rpb24oaXQsIGNyZWF0ZSl7XG4gIGlmKCFoYXMoaXQsIE1FVEEpKXtcbiAgICAvLyBjYW4ndCBzZXQgbWV0YWRhdGEgdG8gdW5jYXVnaHQgZnJvemVuIG9iamVjdFxuICAgIGlmKCFpc0V4dGVuc2libGUoaXQpKXJldHVybiB0cnVlO1xuICAgIC8vIG5vdCBuZWNlc3NhcnkgdG8gYWRkIG1ldGFkYXRhXG4gICAgaWYoIWNyZWF0ZSlyZXR1cm4gZmFsc2U7XG4gICAgLy8gYWRkIG1pc3NpbmcgbWV0YWRhdGFcbiAgICBzZXRNZXRhKGl0KTtcbiAgLy8gcmV0dXJuIGhhc2ggd2VhayBjb2xsZWN0aW9ucyBJRHNcbiAgfSByZXR1cm4gaXRbTUVUQV0udztcbn07XG4vLyBhZGQgbWV0YWRhdGEgb24gZnJlZXplLWZhbWlseSBtZXRob2RzIGNhbGxpbmdcbnZhciBvbkZyZWV6ZSA9IGZ1bmN0aW9uKGl0KXtcbiAgaWYoRlJFRVpFICYmIG1ldGEuTkVFRCAmJiBpc0V4dGVuc2libGUoaXQpICYmICFoYXMoaXQsIE1FVEEpKXNldE1ldGEoaXQpO1xuICByZXR1cm4gaXQ7XG59O1xudmFyIG1ldGEgPSBtb2R1bGUuZXhwb3J0cyA9IHtcbiAgS0VZOiAgICAgIE1FVEEsXG4gIE5FRUQ6ICAgICBmYWxzZSxcbiAgZmFzdEtleTogIGZhc3RLZXksXG4gIGdldFdlYWs6ICBnZXRXZWFrLFxuICBvbkZyZWV6ZTogb25GcmVlemVcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19tZXRhLmpzXG4vLyBtb2R1bGUgaWQgPSAxMDNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IiwidmFyIHBJRSAgICAgICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LXBpZScpXG4gICwgY3JlYXRlRGVzYyAgICAgPSByZXF1aXJlKCcuL19wcm9wZXJ0eS1kZXNjJylcbiAgLCB0b0lPYmplY3QgICAgICA9IHJlcXVpcmUoJy4vX3RvLWlvYmplY3QnKVxuICAsIHRvUHJpbWl0aXZlICAgID0gcmVxdWlyZSgnLi9fdG8tcHJpbWl0aXZlJylcbiAgLCBoYXMgICAgICAgICAgICA9IHJlcXVpcmUoJy4vX2hhcycpXG4gICwgSUU4X0RPTV9ERUZJTkUgPSByZXF1aXJlKCcuL19pZTgtZG9tLWRlZmluZScpXG4gICwgZ09QRCAgICAgICAgICAgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yO1xuXG5leHBvcnRzLmYgPSByZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpID8gZ09QRCA6IGZ1bmN0aW9uIGdldE93blByb3BlcnR5RGVzY3JpcHRvcihPLCBQKXtcbiAgTyA9IHRvSU9iamVjdChPKTtcbiAgUCA9IHRvUHJpbWl0aXZlKFAsIHRydWUpO1xuICBpZihJRThfRE9NX0RFRklORSl0cnkge1xuICAgIHJldHVybiBnT1BEKE8sIFApO1xuICB9IGNhdGNoKGUpeyAvKiBlbXB0eSAqLyB9XG4gIGlmKGhhcyhPLCBQKSlyZXR1cm4gY3JlYXRlRGVzYyghcElFLmYuY2FsbChPLCBQKSwgT1tQXSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdvcGQuanNcbi8vIG1vZHVsZSBpZCA9IDEwNFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwiLy8gNy4yLjIgSXNBcnJheShhcmd1bWVudClcbnZhciBjb2YgPSByZXF1aXJlKCcuL19jb2YnKTtcbm1vZHVsZS5leHBvcnRzID0gQXJyYXkuaXNBcnJheSB8fCBmdW5jdGlvbiBpc0FycmF5KGFyZyl7XG4gIHJldHVybiBjb2YoYXJnKSA9PSAnQXJyYXknO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2lzLWFycmF5LmpzXG4vLyBtb2R1bGUgaWQgPSAxMDdcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuaW1wb3J0IExvY2FsaXphdGlvbkV4Y2VwdGlvbiBmcm9tICdAYXBwL2NsZHIvZXhjZXB0aW9uL2xvY2FsaXphdGlvbic7XG5cbmNsYXNzIE51bWJlclN5bWJvbCB7XG4gIC8qKlxuICAgKiBOdW1iZXJTeW1ib2xMaXN0IGNvbnN0cnVjdG9yLlxuICAgKlxuICAgKiBAcGFyYW0gc3RyaW5nIGRlY2ltYWwgRGVjaW1hbCBzZXBhcmF0b3IgY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgZ3JvdXAgRGlnaXRzIGdyb3VwIHNlcGFyYXRvciBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBsaXN0IExpc3QgZWxlbWVudHMgc2VwYXJhdG9yIGNoYXJhY3RlclxuICAgKiBAcGFyYW0gc3RyaW5nIHBlcmNlbnRTaWduIFBlcmNlbnQgc2lnbiBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBtaW51c1NpZ24gTWludXMgc2lnbiBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBwbHVzU2lnbiBQbHVzIHNpZ24gY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgZXhwb25lbnRpYWwgRXhwb25lbnRpYWwgY2hhcmFjdGVyXG4gICAqIEBwYXJhbSBzdHJpbmcgc3VwZXJzY3JpcHRpbmdFeHBvbmVudCBTdXBlcnNjcmlwdGluZyBleHBvbmVudCBjaGFyYWN0ZXJcbiAgICogQHBhcmFtIHN0cmluZyBwZXJNaWxsZSBQZXJtaWxsZSBzaWduIGNoYXJhY3RlclxuICAgKiBAcGFyYW0gc3RyaW5nIGluZmluaXR5IFRoZSBpbmZpbml0eSBzaWduLiBDb3JyZXNwb25kcyB0byB0aGUgSUVFRSBpbmZpbml0eSBiaXQgcGF0dGVybi5cbiAgICogQHBhcmFtIHN0cmluZyBuYW4gVGhlIE5hTiAoTm90IEEgTnVtYmVyKSBzaWduLiBDb3JyZXNwb25kcyB0byB0aGUgSUVFRSBOYU4gYml0IHBhdHRlcm4uXG4gICAqXG4gICAqIEB0aHJvd3MgTG9jYWxpemF0aW9uRXhjZXB0aW9uXG4gICAqL1xuICBjb25zdHJ1Y3RvcihcbiAgICBkZWNpbWFsLFxuICAgIGdyb3VwLFxuICAgIGxpc3QsXG4gICAgcGVyY2VudFNpZ24sXG4gICAgbWludXNTaWduLFxuICAgIHBsdXNTaWduLFxuICAgIGV4cG9uZW50aWFsLFxuICAgIHN1cGVyc2NyaXB0aW5nRXhwb25lbnQsXG4gICAgcGVyTWlsbGUsXG4gICAgaW5maW5pdHksXG4gICAgbmFuLFxuICApIHtcbiAgICB0aGlzLmRlY2ltYWwgPSBkZWNpbWFsO1xuICAgIHRoaXMuZ3JvdXAgPSBncm91cDtcbiAgICB0aGlzLmxpc3QgPSBsaXN0O1xuICAgIHRoaXMucGVyY2VudFNpZ24gPSBwZXJjZW50U2lnbjtcbiAgICB0aGlzLm1pbnVzU2lnbiA9IG1pbnVzU2lnbjtcbiAgICB0aGlzLnBsdXNTaWduID0gcGx1c1NpZ247XG4gICAgdGhpcy5leHBvbmVudGlhbCA9IGV4cG9uZW50aWFsO1xuICAgIHRoaXMuc3VwZXJzY3JpcHRpbmdFeHBvbmVudCA9IHN1cGVyc2NyaXB0aW5nRXhwb25lbnQ7XG4gICAgdGhpcy5wZXJNaWxsZSA9IHBlck1pbGxlO1xuICAgIHRoaXMuaW5maW5pdHkgPSBpbmZpbml0eTtcbiAgICB0aGlzLm5hbiA9IG5hbjtcblxuICAgIHRoaXMudmFsaWRhdGVEYXRhKCk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBkZWNpbWFsIHNlcGFyYXRvci5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldERlY2ltYWwoKSB7XG4gICAgcmV0dXJuIHRoaXMuZGVjaW1hbDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGRpZ2l0IGdyb3VwcyBzZXBhcmF0b3IuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRHcm91cCgpIHtcbiAgICByZXR1cm4gdGhpcy5ncm91cDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGxpc3QgZWxlbWVudHMgc2VwYXJhdG9yLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0TGlzdCgpIHtcbiAgICByZXR1cm4gdGhpcy5saXN0O1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgcGVyY2VudCBzaWduLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0UGVyY2VudFNpZ24oKSB7XG4gICAgcmV0dXJuIHRoaXMucGVyY2VudFNpZ247XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBtaW51cyBzaWduLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0TWludXNTaWduKCkge1xuICAgIHJldHVybiB0aGlzLm1pbnVzU2lnbjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIHBsdXMgc2lnbi5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldFBsdXNTaWduKCkge1xuICAgIHJldHVybiB0aGlzLnBsdXNTaWduO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgZXhwb25lbnRpYWwgY2hhcmFjdGVyLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0RXhwb25lbnRpYWwoKSB7XG4gICAgcmV0dXJuIHRoaXMuZXhwb25lbnRpYWw7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBleHBvbmVudCBjaGFyYWN0ZXIuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRTdXBlcnNjcmlwdGluZ0V4cG9uZW50KCkge1xuICAgIHJldHVybiB0aGlzLnN1cGVyc2NyaXB0aW5nRXhwb25lbnQ7XG4gIH1cblxuICAvKipcbiAgICogR2VydCB0aGUgcGVyIG1pbGxlIHN5bWJvbCAob2Z0ZW4gXCLigLBcIikuXG4gICAqXG4gICAqIEBzZWUgaHR0cHM6Ly9lbi53aWtpcGVkaWEub3JnL3dpa2kvUGVyX21pbGxlXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRQZXJNaWxsZSgpIHtcbiAgICByZXR1cm4gdGhpcy5wZXJNaWxsZTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGluZmluaXR5IHN5bWJvbCAob2Z0ZW4gXCLiiJ5cIikuXG4gICAqXG4gICAqIEBzZWUgaHR0cHM6Ly9lbi53aWtpcGVkaWEub3JnL3dpa2kvSW5maW5pdHlfc3ltYm9sXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRJbmZpbml0eSgpIHtcbiAgICByZXR1cm4gdGhpcy5pbmZpbml0eTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIE5hTiAobm90IGEgbnVtYmVyKSBzaWduLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0TmFuKCkge1xuICAgIHJldHVybiB0aGlzLm5hbjtcbiAgfVxuXG4gIC8qKlxuICAgKiBTeW1ib2xzIGxpc3QgdmFsaWRhdGlvbi5cbiAgICpcbiAgICogQHRocm93cyBMb2NhbGl6YXRpb25FeGNlcHRpb25cbiAgICovXG4gIHZhbGlkYXRlRGF0YSgpIHtcbiAgICBpZiAoIXRoaXMuZGVjaW1hbCB8fCB0eXBlb2YgdGhpcy5kZWNpbWFsICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBkZWNpbWFsJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLmdyb3VwIHx8IHR5cGVvZiB0aGlzLmdyb3VwICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBncm91cCcpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5saXN0IHx8IHR5cGVvZiB0aGlzLmxpc3QgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHN5bWJvbCBsaXN0Jyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLnBlcmNlbnRTaWduIHx8IHR5cGVvZiB0aGlzLnBlcmNlbnRTaWduICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBwZXJjZW50U2lnbicpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5taW51c1NpZ24gfHwgdHlwZW9mIHRoaXMubWludXNTaWduICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBtaW51c1NpZ24nKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMucGx1c1NpZ24gfHwgdHlwZW9mIHRoaXMucGx1c1NpZ24gIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHBsdXNTaWduJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLmV4cG9uZW50aWFsIHx8IHR5cGVvZiB0aGlzLmV4cG9uZW50aWFsICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBleHBvbmVudGlhbCcpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5zdXBlcnNjcmlwdGluZ0V4cG9uZW50IHx8IHR5cGVvZiB0aGlzLnN1cGVyc2NyaXB0aW5nRXhwb25lbnQgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHN1cGVyc2NyaXB0aW5nRXhwb25lbnQnKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMucGVyTWlsbGUgfHwgdHlwZW9mIHRoaXMucGVyTWlsbGUgIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHBlck1pbGxlJyk7XG4gICAgfVxuXG4gICAgaWYgKCF0aGlzLmluZmluaXR5IHx8IHR5cGVvZiB0aGlzLmluZmluaXR5ICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBpbmZpbml0eScpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5uYW4gfHwgdHlwZW9mIHRoaXMubmFuICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBuYW4nKTtcbiAgICB9XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTnVtYmVyU3ltYm9sO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL2NsZHIvbnVtYmVyLXN5bWJvbC5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cbmltcG9ydCBMb2NhbGl6YXRpb25FeGNlcHRpb24gZnJvbSAnQGFwcC9jbGRyL2V4Y2VwdGlvbi9sb2NhbGl6YXRpb24nO1xuaW1wb3J0IE51bWJlclN5bWJvbCBmcm9tICdAYXBwL2NsZHIvbnVtYmVyLXN5bWJvbCc7XG5cbmNsYXNzIE51bWJlclNwZWNpZmljYXRpb24ge1xuICAvKipcbiAgICogTnVtYmVyIHNwZWNpZmljYXRpb24gY29uc3RydWN0b3IuXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgcG9zaXRpdmVQYXR0ZXJuIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIGZvciBwb3NpdGl2ZSBhbW91bnRzXG4gICAqIEBwYXJhbSBzdHJpbmcgbmVnYXRpdmVQYXR0ZXJuIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIGZvciBuZWdhdGl2ZSBhbW91bnRzXG4gICAqIEBwYXJhbSBOdW1iZXJTeW1ib2wgc3ltYm9sIE51bWJlciBzeW1ib2xcbiAgICogQHBhcmFtIGludCBtYXhGcmFjdGlvbkRpZ2l0cyBNYXhpbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3JcbiAgICogQHBhcmFtIGludCBtaW5GcmFjdGlvbkRpZ2l0cyBNaW5pbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3JcbiAgICogQHBhcmFtIGJvb2wgZ3JvdXBpbmdVc2VkIElzIGRpZ2l0cyBncm91cGluZyB1c2VkID9cbiAgICogQHBhcmFtIGludCBwcmltYXJ5R3JvdXBTaXplIFNpemUgb2YgcHJpbWFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlclxuICAgKiBAcGFyYW0gaW50IHNlY29uZGFyeUdyb3VwU2l6ZSBTaXplIG9mIHNlY29uZGFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlclxuICAgKlxuICAgKiBAdGhyb3dzIExvY2FsaXphdGlvbkV4Y2VwdGlvblxuICAgKi9cbiAgY29uc3RydWN0b3IoXG4gICAgcG9zaXRpdmVQYXR0ZXJuLFxuICAgIG5lZ2F0aXZlUGF0dGVybixcbiAgICBzeW1ib2wsXG4gICAgbWF4RnJhY3Rpb25EaWdpdHMsXG4gICAgbWluRnJhY3Rpb25EaWdpdHMsXG4gICAgZ3JvdXBpbmdVc2VkLFxuICAgIHByaW1hcnlHcm91cFNpemUsXG4gICAgc2Vjb25kYXJ5R3JvdXBTaXplLFxuICApIHtcbiAgICB0aGlzLnBvc2l0aXZlUGF0dGVybiA9IHBvc2l0aXZlUGF0dGVybjtcbiAgICB0aGlzLm5lZ2F0aXZlUGF0dGVybiA9IG5lZ2F0aXZlUGF0dGVybjtcbiAgICB0aGlzLnN5bWJvbCA9IHN5bWJvbDtcblxuICAgIHRoaXMubWF4RnJhY3Rpb25EaWdpdHMgPSBtYXhGcmFjdGlvbkRpZ2l0cztcbiAgICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmVcbiAgICB0aGlzLm1pbkZyYWN0aW9uRGlnaXRzID0gbWF4RnJhY3Rpb25EaWdpdHMgPCBtaW5GcmFjdGlvbkRpZ2l0cyA/IG1heEZyYWN0aW9uRGlnaXRzIDogbWluRnJhY3Rpb25EaWdpdHM7XG5cbiAgICB0aGlzLmdyb3VwaW5nVXNlZCA9IGdyb3VwaW5nVXNlZDtcbiAgICB0aGlzLnByaW1hcnlHcm91cFNpemUgPSBwcmltYXJ5R3JvdXBTaXplO1xuICAgIHRoaXMuc2Vjb25kYXJ5R3JvdXBTaXplID0gc2Vjb25kYXJ5R3JvdXBTaXplO1xuXG4gICAgaWYgKCF0aGlzLnBvc2l0aXZlUGF0dGVybiB8fCB0eXBlb2YgdGhpcy5wb3NpdGl2ZVBhdHRlcm4gIT09ICdzdHJpbmcnKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHBvc2l0aXZlUGF0dGVybicpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5uZWdhdGl2ZVBhdHRlcm4gfHwgdHlwZW9mIHRoaXMubmVnYXRpdmVQYXR0ZXJuICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBuZWdhdGl2ZVBhdHRlcm4nKTtcbiAgICB9XG5cbiAgICBpZiAoIXRoaXMuc3ltYm9sIHx8ICEodGhpcy5zeW1ib2wgaW5zdGFuY2VvZiBOdW1iZXJTeW1ib2wpKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIHN5bWJvbCcpO1xuICAgIH1cblxuICAgIGlmICh0eXBlb2YgdGhpcy5tYXhGcmFjdGlvbkRpZ2l0cyAhPT0gJ251bWJlcicpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgbWF4RnJhY3Rpb25EaWdpdHMnKTtcbiAgICB9XG5cbiAgICBpZiAodHlwZW9mIHRoaXMubWluRnJhY3Rpb25EaWdpdHMgIT09ICdudW1iZXInKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIG1pbkZyYWN0aW9uRGlnaXRzJyk7XG4gICAgfVxuXG4gICAgaWYgKHR5cGVvZiB0aGlzLmdyb3VwaW5nVXNlZCAhPT0gJ2Jvb2xlYW4nKSB7XG4gICAgICB0aHJvdyBuZXcgTG9jYWxpemF0aW9uRXhjZXB0aW9uKCdJbnZhbGlkIGdyb3VwaW5nVXNlZCcpO1xuICAgIH1cblxuICAgIGlmICh0eXBlb2YgdGhpcy5wcmltYXJ5R3JvdXBTaXplICE9PSAnbnVtYmVyJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBwcmltYXJ5R3JvdXBTaXplJyk7XG4gICAgfVxuXG4gICAgaWYgKHR5cGVvZiB0aGlzLnNlY29uZGFyeUdyb3VwU2l6ZSAhPT0gJ251bWJlcicpIHtcbiAgICAgIHRocm93IG5ldyBMb2NhbGl6YXRpb25FeGNlcHRpb24oJ0ludmFsaWQgc2Vjb25kYXJ5R3JvdXBTaXplJyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEdldCBzeW1ib2wuXG4gICAqXG4gICAqIEByZXR1cm4gTnVtYmVyU3ltYm9sXG4gICAqL1xuICBnZXRTeW1ib2woKSB7XG4gICAgcmV0dXJuIHRoaXMuc3ltYm9sO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgZm9ybWF0dGluZyBydWxlcyBmb3IgdGhpcyBudW1iZXIgKHdoZW4gcG9zaXRpdmUpLlxuICAgKlxuICAgKiBUaGlzIHBhdHRlcm4gdXNlcyB0aGUgVW5pY29kZSBDTERSIG51bWJlciBwYXR0ZXJuIHN5bnRheFxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0UG9zaXRpdmVQYXR0ZXJuKCkge1xuICAgIHJldHVybiB0aGlzLnBvc2l0aXZlUGF0dGVybjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIGZvcm1hdHRpbmcgcnVsZXMgZm9yIHRoaXMgbnVtYmVyICh3aGVuIG5lZ2F0aXZlKS5cbiAgICpcbiAgICogVGhpcyBwYXR0ZXJuIHVzZXMgdGhlIFVuaWNvZGUgQ0xEUiBudW1iZXIgcGF0dGVybiBzeW50YXhcbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIGdldE5lZ2F0aXZlUGF0dGVybigpIHtcbiAgICByZXR1cm4gdGhpcy5uZWdhdGl2ZVBhdHRlcm47XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBtYXhpbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3IgKHJvdW5kaW5nIGlmIG5lZWRlZCkuXG4gICAqXG4gICAqIEByZXR1cm4gaW50XG4gICAqL1xuICBnZXRNYXhGcmFjdGlvbkRpZ2l0cygpIHtcbiAgICByZXR1cm4gdGhpcy5tYXhGcmFjdGlvbkRpZ2l0cztcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIG1pbmltdW0gbnVtYmVyIG9mIGRpZ2l0cyBhZnRlciBkZWNpbWFsIHNlcGFyYXRvciAoZmlsbCB3aXRoIFwiMFwiIGlmIG5lZWRlZCkuXG4gICAqXG4gICAqIEByZXR1cm4gaW50XG4gICAqL1xuICBnZXRNaW5GcmFjdGlvbkRpZ2l0cygpIHtcbiAgICByZXR1cm4gdGhpcy5taW5GcmFjdGlvbkRpZ2l0cztcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIFwiZ3JvdXBpbmdcIiBmbGFnLiBUaGlzIGZsYWcgZGVmaW5lcyBpZiBkaWdpdHNcbiAgICogZ3JvdXBpbmcgc2hvdWxkIGJlIHVzZWQgd2hlbiBmb3JtYXR0aW5nIHRoaXMgbnVtYmVyLlxuICAgKlxuICAgKiBAcmV0dXJuIGJvb2xcbiAgICovXG4gIGlzR3JvdXBpbmdVc2VkKCkge1xuICAgIHJldHVybiB0aGlzLmdyb3VwaW5nVXNlZDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIHNpemUgb2YgcHJpbWFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlci5cbiAgICpcbiAgICogQHJldHVybiBpbnRcbiAgICovXG4gIGdldFByaW1hcnlHcm91cFNpemUoKSB7XG4gICAgcmV0dXJuIHRoaXMucHJpbWFyeUdyb3VwU2l6ZTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIHNpemUgb2Ygc2Vjb25kYXJ5IGRpZ2l0cyBncm91cHMgaW4gdGhlIG51bWJlci5cbiAgICpcbiAgICogQHJldHVybiBpbnRcbiAgICovXG4gIGdldFNlY29uZGFyeUdyb3VwU2l6ZSgpIHtcbiAgICByZXR1cm4gdGhpcy5zZWNvbmRhcnlHcm91cFNpemU7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTnVtYmVyU3BlY2lmaWNhdGlvbjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL3NwZWNpZmljYXRpb25zL251bWJlci5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cbmNsYXNzIExvY2FsaXphdGlvbkV4Y2VwdGlvbiB7XG4gIGNvbnN0cnVjdG9yKG1lc3NhZ2UpIHtcbiAgICB0aGlzLm1lc3NhZ2UgPSBtZXNzYWdlO1xuICAgIHRoaXMubmFtZSA9ICdMb2NhbGl6YXRpb25FeGNlcHRpb24nO1xuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IExvY2FsaXphdGlvbkV4Y2VwdGlvbjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL2V4Y2VwdGlvbi9sb2NhbGl6YXRpb24uanMiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vc3ltYm9sXCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9zeW1ib2wuanNcbi8vIG1vZHVsZSBpZCA9IDExM1xuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwibW9kdWxlLmV4cG9ydHMgPSB7IFwiZGVmYXVsdFwiOiByZXF1aXJlKFwiY29yZS1qcy9saWJyYXJ5L2ZuL3N5bWJvbC9pdGVyYXRvclwiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvc3ltYm9sL2l0ZXJhdG9yLmpzXG4vLyBtb2R1bGUgaWQgPSAxMTRcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsInJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM2LnN5bWJvbCcpO1xucmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczYub2JqZWN0LnRvLXN0cmluZycpO1xucmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczcuc3ltYm9sLmFzeW5jLWl0ZXJhdG9yJyk7XG5yZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNy5zeW1ib2wub2JzZXJ2YWJsZScpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuU3ltYm9sO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vc3ltYm9sL2luZGV4LmpzXG4vLyBtb2R1bGUgaWQgPSAxMTdcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsInJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM2LnN0cmluZy5pdGVyYXRvcicpO1xucmVxdWlyZSgnLi4vLi4vbW9kdWxlcy93ZWIuZG9tLml0ZXJhYmxlJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4uLy4uL21vZHVsZXMvX3drcy1leHQnKS5mKCdpdGVyYXRvcicpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vc3ltYm9sL2l0ZXJhdG9yLmpzXG4vLyBtb2R1bGUgaWQgPSAxMThcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsIi8vIGFsbCBlbnVtZXJhYmxlIG9iamVjdCBrZXlzLCBpbmNsdWRlcyBzeW1ib2xzXG52YXIgZ2V0S2V5cyA9IHJlcXVpcmUoJy4vX29iamVjdC1rZXlzJylcbiAgLCBnT1BTICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWdvcHMnKVxuICAsIHBJRSAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtcGllJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgdmFyIHJlc3VsdCAgICAgPSBnZXRLZXlzKGl0KVxuICAgICwgZ2V0U3ltYm9scyA9IGdPUFMuZjtcbiAgaWYoZ2V0U3ltYm9scyl7XG4gICAgdmFyIHN5bWJvbHMgPSBnZXRTeW1ib2xzKGl0KVxuICAgICAgLCBpc0VudW0gID0gcElFLmZcbiAgICAgICwgaSAgICAgICA9IDBcbiAgICAgICwga2V5O1xuICAgIHdoaWxlKHN5bWJvbHMubGVuZ3RoID4gaSlpZihpc0VudW0uY2FsbChpdCwga2V5ID0gc3ltYm9sc1tpKytdKSlyZXN1bHQucHVzaChrZXkpO1xuICB9IHJldHVybiByZXN1bHQ7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZW51bS1rZXlzLmpzXG4vLyBtb2R1bGUgaWQgPSAxMTlcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsIi8vIGNoZWNrIG9uIGRlZmF1bHQgQXJyYXkgaXRlcmF0b3JcbnZhciBJdGVyYXRvcnMgID0gcmVxdWlyZSgnLi9faXRlcmF0b3JzJylcbiAgLCBJVEVSQVRPUiAgID0gcmVxdWlyZSgnLi9fd2tzJykoJ2l0ZXJhdG9yJylcbiAgLCBBcnJheVByb3RvID0gQXJyYXkucHJvdG90eXBlO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGl0ICE9PSB1bmRlZmluZWQgJiYgKEl0ZXJhdG9ycy5BcnJheSA9PT0gaXQgfHwgQXJyYXlQcm90b1tJVEVSQVRPUl0gPT09IGl0KTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pcy1hcnJheS1pdGVyLmpzXG4vLyBtb2R1bGUgaWQgPSAxMjBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUiLCIvLyBjYWxsIHNvbWV0aGluZyBvbiBpdGVyYXRvciBzdGVwIHdpdGggc2FmZSBjbG9zaW5nIG9uIGVycm9yXG52YXIgYW5PYmplY3QgPSByZXF1aXJlKCcuL19hbi1vYmplY3QnKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXRlcmF0b3IsIGZuLCB2YWx1ZSwgZW50cmllcyl7XG4gIHRyeSB7XG4gICAgcmV0dXJuIGVudHJpZXMgPyBmbihhbk9iamVjdCh2YWx1ZSlbMF0sIHZhbHVlWzFdKSA6IGZuKHZhbHVlKTtcbiAgLy8gNy40LjYgSXRlcmF0b3JDbG9zZShpdGVyYXRvciwgY29tcGxldGlvbilcbiAgfSBjYXRjaChlKXtcbiAgICB2YXIgcmV0ID0gaXRlcmF0b3JbJ3JldHVybiddO1xuICAgIGlmKHJldCAhPT0gdW5kZWZpbmVkKWFuT2JqZWN0KHJldC5jYWxsKGl0ZXJhdG9yKSk7XG4gICAgdGhyb3cgZTtcbiAgfVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXItY2FsbC5qc1xuLy8gbW9kdWxlIGlkID0gMTIxXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IiwidmFyIGdldEtleXMgICA9IHJlcXVpcmUoJy4vX29iamVjdC1rZXlzJylcbiAgLCB0b0lPYmplY3QgPSByZXF1aXJlKCcuL190by1pb2JqZWN0Jyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKG9iamVjdCwgZWwpe1xuICB2YXIgTyAgICAgID0gdG9JT2JqZWN0KG9iamVjdClcbiAgICAsIGtleXMgICA9IGdldEtleXMoTylcbiAgICAsIGxlbmd0aCA9IGtleXMubGVuZ3RoXG4gICAgLCBpbmRleCAgPSAwXG4gICAgLCBrZXk7XG4gIHdoaWxlKGxlbmd0aCA+IGluZGV4KWlmKE9ba2V5ID0ga2V5c1tpbmRleCsrXV0gPT09IGVsKXJldHVybiBrZXk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fa2V5b2YuanNcbi8vIG1vZHVsZSBpZCA9IDEyMlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwiLy8gZmFsbGJhY2sgZm9yIElFMTEgYnVnZ3kgT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXMgd2l0aCBpZnJhbWUgYW5kIHdpbmRvd1xudmFyIHRvSU9iamVjdCA9IHJlcXVpcmUoJy4vX3RvLWlvYmplY3QnKVxuICAsIGdPUE4gICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1nb3BuJykuZlxuICAsIHRvU3RyaW5nICA9IHt9LnRvU3RyaW5nO1xuXG52YXIgd2luZG93TmFtZXMgPSB0eXBlb2Ygd2luZG93ID09ICdvYmplY3QnICYmIHdpbmRvdyAmJiBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lc1xuICA/IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKHdpbmRvdykgOiBbXTtcblxudmFyIGdldFdpbmRvd05hbWVzID0gZnVuY3Rpb24oaXQpe1xuICB0cnkge1xuICAgIHJldHVybiBnT1BOKGl0KTtcbiAgfSBjYXRjaChlKXtcbiAgICByZXR1cm4gd2luZG93TmFtZXMuc2xpY2UoKTtcbiAgfVxufTtcblxubW9kdWxlLmV4cG9ydHMuZiA9IGZ1bmN0aW9uIGdldE93blByb3BlcnR5TmFtZXMoaXQpe1xuICByZXR1cm4gd2luZG93TmFtZXMgJiYgdG9TdHJpbmcuY2FsbChpdCkgPT0gJ1tvYmplY3QgV2luZG93XScgPyBnZXRXaW5kb3dOYW1lcyhpdCkgOiBnT1BOKHRvSU9iamVjdChpdCkpO1xufTtcblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdvcG4tZXh0LmpzXG4vLyBtb2R1bGUgaWQgPSAxMjNcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsIid1c2Ugc3RyaWN0Jztcbi8vIEVDTUFTY3JpcHQgNiBzeW1ib2xzIHNoaW1cbnZhciBnbG9iYWwgICAgICAgICA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpXG4gICwgaGFzICAgICAgICAgICAgPSByZXF1aXJlKCcuL19oYXMnKVxuICAsIERFU0NSSVBUT1JTICAgID0gcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKVxuICAsICRleHBvcnQgICAgICAgID0gcmVxdWlyZSgnLi9fZXhwb3J0JylcbiAgLCByZWRlZmluZSAgICAgICA9IHJlcXVpcmUoJy4vX3JlZGVmaW5lJylcbiAgLCBNRVRBICAgICAgICAgICA9IHJlcXVpcmUoJy4vX21ldGEnKS5LRVlcbiAgLCAkZmFpbHMgICAgICAgICA9IHJlcXVpcmUoJy4vX2ZhaWxzJylcbiAgLCBzaGFyZWQgICAgICAgICA9IHJlcXVpcmUoJy4vX3NoYXJlZCcpXG4gICwgc2V0VG9TdHJpbmdUYWcgPSByZXF1aXJlKCcuL19zZXQtdG8tc3RyaW5nLXRhZycpXG4gICwgdWlkICAgICAgICAgICAgPSByZXF1aXJlKCcuL191aWQnKVxuICAsIHdrcyAgICAgICAgICAgID0gcmVxdWlyZSgnLi9fd2tzJylcbiAgLCB3a3NFeHQgICAgICAgICA9IHJlcXVpcmUoJy4vX3drcy1leHQnKVxuICAsIHdrc0RlZmluZSAgICAgID0gcmVxdWlyZSgnLi9fd2tzLWRlZmluZScpXG4gICwga2V5T2YgICAgICAgICAgPSByZXF1aXJlKCcuL19rZXlvZicpXG4gICwgZW51bUtleXMgICAgICAgPSByZXF1aXJlKCcuL19lbnVtLWtleXMnKVxuICAsIGlzQXJyYXkgICAgICAgID0gcmVxdWlyZSgnLi9faXMtYXJyYXknKVxuICAsIGFuT2JqZWN0ICAgICAgID0gcmVxdWlyZSgnLi9fYW4tb2JqZWN0JylcbiAgLCB0b0lPYmplY3QgICAgICA9IHJlcXVpcmUoJy4vX3RvLWlvYmplY3QnKVxuICAsIHRvUHJpbWl0aXZlICAgID0gcmVxdWlyZSgnLi9fdG8tcHJpbWl0aXZlJylcbiAgLCBjcmVhdGVEZXNjICAgICA9IHJlcXVpcmUoJy4vX3Byb3BlcnR5LWRlc2MnKVxuICAsIF9jcmVhdGUgICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWNyZWF0ZScpXG4gICwgZ09QTkV4dCAgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZ29wbi1leHQnKVxuICAsICRHT1BEICAgICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWdvcGQnKVxuICAsICREUCAgICAgICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWRwJylcbiAgLCAka2V5cyAgICAgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1rZXlzJylcbiAgLCBnT1BEICAgICAgICAgICA9ICRHT1BELmZcbiAgLCBkUCAgICAgICAgICAgICA9ICREUC5mXG4gICwgZ09QTiAgICAgICAgICAgPSBnT1BORXh0LmZcbiAgLCAkU3ltYm9sICAgICAgICA9IGdsb2JhbC5TeW1ib2xcbiAgLCAkSlNPTiAgICAgICAgICA9IGdsb2JhbC5KU09OXG4gICwgX3N0cmluZ2lmeSAgICAgPSAkSlNPTiAmJiAkSlNPTi5zdHJpbmdpZnlcbiAgLCBQUk9UT1RZUEUgICAgICA9ICdwcm90b3R5cGUnXG4gICwgSElEREVOICAgICAgICAgPSB3a3MoJ19oaWRkZW4nKVxuICAsIFRPX1BSSU1JVElWRSAgID0gd2tzKCd0b1ByaW1pdGl2ZScpXG4gICwgaXNFbnVtICAgICAgICAgPSB7fS5wcm9wZXJ0eUlzRW51bWVyYWJsZVxuICAsIFN5bWJvbFJlZ2lzdHJ5ID0gc2hhcmVkKCdzeW1ib2wtcmVnaXN0cnknKVxuICAsIEFsbFN5bWJvbHMgICAgID0gc2hhcmVkKCdzeW1ib2xzJylcbiAgLCBPUFN5bWJvbHMgICAgICA9IHNoYXJlZCgnb3Atc3ltYm9scycpXG4gICwgT2JqZWN0UHJvdG8gICAgPSBPYmplY3RbUFJPVE9UWVBFXVxuICAsIFVTRV9OQVRJVkUgICAgID0gdHlwZW9mICRTeW1ib2wgPT0gJ2Z1bmN0aW9uJ1xuICAsIFFPYmplY3QgICAgICAgID0gZ2xvYmFsLlFPYmplY3Q7XG4vLyBEb24ndCB1c2Ugc2V0dGVycyBpbiBRdCBTY3JpcHQsIGh0dHBzOi8vZ2l0aHViLmNvbS96bG9pcm9jay9jb3JlLWpzL2lzc3Vlcy8xNzNcbnZhciBzZXR0ZXIgPSAhUU9iamVjdCB8fCAhUU9iamVjdFtQUk9UT1RZUEVdIHx8ICFRT2JqZWN0W1BST1RPVFlQRV0uZmluZENoaWxkO1xuXG4vLyBmYWxsYmFjayBmb3Igb2xkIEFuZHJvaWQsIGh0dHBzOi8vY29kZS5nb29nbGUuY29tL3AvdjgvaXNzdWVzL2RldGFpbD9pZD02ODdcbnZhciBzZXRTeW1ib2xEZXNjID0gREVTQ1JJUFRPUlMgJiYgJGZhaWxzKGZ1bmN0aW9uKCl7XG4gIHJldHVybiBfY3JlYXRlKGRQKHt9LCAnYScsIHtcbiAgICBnZXQ6IGZ1bmN0aW9uKCl7IHJldHVybiBkUCh0aGlzLCAnYScsIHt2YWx1ZTogN30pLmE7IH1cbiAgfSkpLmEgIT0gNztcbn0pID8gZnVuY3Rpb24oaXQsIGtleSwgRCl7XG4gIHZhciBwcm90b0Rlc2MgPSBnT1BEKE9iamVjdFByb3RvLCBrZXkpO1xuICBpZihwcm90b0Rlc2MpZGVsZXRlIE9iamVjdFByb3RvW2tleV07XG4gIGRQKGl0LCBrZXksIEQpO1xuICBpZihwcm90b0Rlc2MgJiYgaXQgIT09IE9iamVjdFByb3RvKWRQKE9iamVjdFByb3RvLCBrZXksIHByb3RvRGVzYyk7XG59IDogZFA7XG5cbnZhciB3cmFwID0gZnVuY3Rpb24odGFnKXtcbiAgdmFyIHN5bSA9IEFsbFN5bWJvbHNbdGFnXSA9IF9jcmVhdGUoJFN5bWJvbFtQUk9UT1RZUEVdKTtcbiAgc3ltLl9rID0gdGFnO1xuICByZXR1cm4gc3ltO1xufTtcblxudmFyIGlzU3ltYm9sID0gVVNFX05BVElWRSAmJiB0eXBlb2YgJFN5bWJvbC5pdGVyYXRvciA9PSAnc3ltYm9sJyA/IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIHR5cGVvZiBpdCA9PSAnc3ltYm9sJztcbn0gOiBmdW5jdGlvbihpdCl7XG4gIHJldHVybiBpdCBpbnN0YW5jZW9mICRTeW1ib2w7XG59O1xuXG52YXIgJGRlZmluZVByb3BlcnR5ID0gZnVuY3Rpb24gZGVmaW5lUHJvcGVydHkoaXQsIGtleSwgRCl7XG4gIGlmKGl0ID09PSBPYmplY3RQcm90bykkZGVmaW5lUHJvcGVydHkoT1BTeW1ib2xzLCBrZXksIEQpO1xuICBhbk9iamVjdChpdCk7XG4gIGtleSA9IHRvUHJpbWl0aXZlKGtleSwgdHJ1ZSk7XG4gIGFuT2JqZWN0KEQpO1xuICBpZihoYXMoQWxsU3ltYm9scywga2V5KSl7XG4gICAgaWYoIUQuZW51bWVyYWJsZSl7XG4gICAgICBpZighaGFzKGl0LCBISURERU4pKWRQKGl0LCBISURERU4sIGNyZWF0ZURlc2MoMSwge30pKTtcbiAgICAgIGl0W0hJRERFTl1ba2V5XSA9IHRydWU7XG4gICAgfSBlbHNlIHtcbiAgICAgIGlmKGhhcyhpdCwgSElEREVOKSAmJiBpdFtISURERU5dW2tleV0paXRbSElEREVOXVtrZXldID0gZmFsc2U7XG4gICAgICBEID0gX2NyZWF0ZShELCB7ZW51bWVyYWJsZTogY3JlYXRlRGVzYygwLCBmYWxzZSl9KTtcbiAgICB9IHJldHVybiBzZXRTeW1ib2xEZXNjKGl0LCBrZXksIEQpO1xuICB9IHJldHVybiBkUChpdCwga2V5LCBEKTtcbn07XG52YXIgJGRlZmluZVByb3BlcnRpZXMgPSBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0aWVzKGl0LCBQKXtcbiAgYW5PYmplY3QoaXQpO1xuICB2YXIga2V5cyA9IGVudW1LZXlzKFAgPSB0b0lPYmplY3QoUCkpXG4gICAgLCBpICAgID0gMFxuICAgICwgbCA9IGtleXMubGVuZ3RoXG4gICAgLCBrZXk7XG4gIHdoaWxlKGwgPiBpKSRkZWZpbmVQcm9wZXJ0eShpdCwga2V5ID0ga2V5c1tpKytdLCBQW2tleV0pO1xuICByZXR1cm4gaXQ7XG59O1xudmFyICRjcmVhdGUgPSBmdW5jdGlvbiBjcmVhdGUoaXQsIFApe1xuICByZXR1cm4gUCA9PT0gdW5kZWZpbmVkID8gX2NyZWF0ZShpdCkgOiAkZGVmaW5lUHJvcGVydGllcyhfY3JlYXRlKGl0KSwgUCk7XG59O1xudmFyICRwcm9wZXJ0eUlzRW51bWVyYWJsZSA9IGZ1bmN0aW9uIHByb3BlcnR5SXNFbnVtZXJhYmxlKGtleSl7XG4gIHZhciBFID0gaXNFbnVtLmNhbGwodGhpcywga2V5ID0gdG9QcmltaXRpdmUoa2V5LCB0cnVlKSk7XG4gIGlmKHRoaXMgPT09IE9iamVjdFByb3RvICYmIGhhcyhBbGxTeW1ib2xzLCBrZXkpICYmICFoYXMoT1BTeW1ib2xzLCBrZXkpKXJldHVybiBmYWxzZTtcbiAgcmV0dXJuIEUgfHwgIWhhcyh0aGlzLCBrZXkpIHx8ICFoYXMoQWxsU3ltYm9scywga2V5KSB8fCBoYXModGhpcywgSElEREVOKSAmJiB0aGlzW0hJRERFTl1ba2V5XSA/IEUgOiB0cnVlO1xufTtcbnZhciAkZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yID0gZnVuY3Rpb24gZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yKGl0LCBrZXkpe1xuICBpdCAgPSB0b0lPYmplY3QoaXQpO1xuICBrZXkgPSB0b1ByaW1pdGl2ZShrZXksIHRydWUpO1xuICBpZihpdCA9PT0gT2JqZWN0UHJvdG8gJiYgaGFzKEFsbFN5bWJvbHMsIGtleSkgJiYgIWhhcyhPUFN5bWJvbHMsIGtleSkpcmV0dXJuO1xuICB2YXIgRCA9IGdPUEQoaXQsIGtleSk7XG4gIGlmKEQgJiYgaGFzKEFsbFN5bWJvbHMsIGtleSkgJiYgIShoYXMoaXQsIEhJRERFTikgJiYgaXRbSElEREVOXVtrZXldKSlELmVudW1lcmFibGUgPSB0cnVlO1xuICByZXR1cm4gRDtcbn07XG52YXIgJGdldE93blByb3BlcnR5TmFtZXMgPSBmdW5jdGlvbiBnZXRPd25Qcm9wZXJ0eU5hbWVzKGl0KXtcbiAgdmFyIG5hbWVzICA9IGdPUE4odG9JT2JqZWN0KGl0KSlcbiAgICAsIHJlc3VsdCA9IFtdXG4gICAgLCBpICAgICAgPSAwXG4gICAgLCBrZXk7XG4gIHdoaWxlKG5hbWVzLmxlbmd0aCA+IGkpe1xuICAgIGlmKCFoYXMoQWxsU3ltYm9scywga2V5ID0gbmFtZXNbaSsrXSkgJiYga2V5ICE9IEhJRERFTiAmJiBrZXkgIT0gTUVUQSlyZXN1bHQucHVzaChrZXkpO1xuICB9IHJldHVybiByZXN1bHQ7XG59O1xudmFyICRnZXRPd25Qcm9wZXJ0eVN5bWJvbHMgPSBmdW5jdGlvbiBnZXRPd25Qcm9wZXJ0eVN5bWJvbHMoaXQpe1xuICB2YXIgSVNfT1AgID0gaXQgPT09IE9iamVjdFByb3RvXG4gICAgLCBuYW1lcyAgPSBnT1BOKElTX09QID8gT1BTeW1ib2xzIDogdG9JT2JqZWN0KGl0KSlcbiAgICAsIHJlc3VsdCA9IFtdXG4gICAgLCBpICAgICAgPSAwXG4gICAgLCBrZXk7XG4gIHdoaWxlKG5hbWVzLmxlbmd0aCA+IGkpe1xuICAgIGlmKGhhcyhBbGxTeW1ib2xzLCBrZXkgPSBuYW1lc1tpKytdKSAmJiAoSVNfT1AgPyBoYXMoT2JqZWN0UHJvdG8sIGtleSkgOiB0cnVlKSlyZXN1bHQucHVzaChBbGxTeW1ib2xzW2tleV0pO1xuICB9IHJldHVybiByZXN1bHQ7XG59O1xuXG4vLyAxOS40LjEuMSBTeW1ib2woW2Rlc2NyaXB0aW9uXSlcbmlmKCFVU0VfTkFUSVZFKXtcbiAgJFN5bWJvbCA9IGZ1bmN0aW9uIFN5bWJvbCgpe1xuICAgIGlmKHRoaXMgaW5zdGFuY2VvZiAkU3ltYm9sKXRocm93IFR5cGVFcnJvcignU3ltYm9sIGlzIG5vdCBhIGNvbnN0cnVjdG9yIScpO1xuICAgIHZhciB0YWcgPSB1aWQoYXJndW1lbnRzLmxlbmd0aCA+IDAgPyBhcmd1bWVudHNbMF0gOiB1bmRlZmluZWQpO1xuICAgIHZhciAkc2V0ID0gZnVuY3Rpb24odmFsdWUpe1xuICAgICAgaWYodGhpcyA9PT0gT2JqZWN0UHJvdG8pJHNldC5jYWxsKE9QU3ltYm9scywgdmFsdWUpO1xuICAgICAgaWYoaGFzKHRoaXMsIEhJRERFTikgJiYgaGFzKHRoaXNbSElEREVOXSwgdGFnKSl0aGlzW0hJRERFTl1bdGFnXSA9IGZhbHNlO1xuICAgICAgc2V0U3ltYm9sRGVzYyh0aGlzLCB0YWcsIGNyZWF0ZURlc2MoMSwgdmFsdWUpKTtcbiAgICB9O1xuICAgIGlmKERFU0NSSVBUT1JTICYmIHNldHRlcilzZXRTeW1ib2xEZXNjKE9iamVjdFByb3RvLCB0YWcsIHtjb25maWd1cmFibGU6IHRydWUsIHNldDogJHNldH0pO1xuICAgIHJldHVybiB3cmFwKHRhZyk7XG4gIH07XG4gIHJlZGVmaW5lKCRTeW1ib2xbUFJPVE9UWVBFXSwgJ3RvU3RyaW5nJywgZnVuY3Rpb24gdG9TdHJpbmcoKXtcbiAgICByZXR1cm4gdGhpcy5faztcbiAgfSk7XG5cbiAgJEdPUEQuZiA9ICRnZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3I7XG4gICREUC5mICAgPSAkZGVmaW5lUHJvcGVydHk7XG4gIHJlcXVpcmUoJy4vX29iamVjdC1nb3BuJykuZiA9IGdPUE5FeHQuZiA9ICRnZXRPd25Qcm9wZXJ0eU5hbWVzO1xuICByZXF1aXJlKCcuL19vYmplY3QtcGllJykuZiAgPSAkcHJvcGVydHlJc0VudW1lcmFibGU7XG4gIHJlcXVpcmUoJy4vX29iamVjdC1nb3BzJykuZiA9ICRnZXRPd25Qcm9wZXJ0eVN5bWJvbHM7XG5cbiAgaWYoREVTQ1JJUFRPUlMgJiYgIXJlcXVpcmUoJy4vX2xpYnJhcnknKSl7XG4gICAgcmVkZWZpbmUoT2JqZWN0UHJvdG8sICdwcm9wZXJ0eUlzRW51bWVyYWJsZScsICRwcm9wZXJ0eUlzRW51bWVyYWJsZSwgdHJ1ZSk7XG4gIH1cblxuICB3a3NFeHQuZiA9IGZ1bmN0aW9uKG5hbWUpe1xuICAgIHJldHVybiB3cmFwKHdrcyhuYW1lKSk7XG4gIH1cbn1cblxuJGV4cG9ydCgkZXhwb3J0LkcgKyAkZXhwb3J0LlcgKyAkZXhwb3J0LkYgKiAhVVNFX05BVElWRSwge1N5bWJvbDogJFN5bWJvbH0pO1xuXG5mb3IodmFyIHN5bWJvbHMgPSAoXG4gIC8vIDE5LjQuMi4yLCAxOS40LjIuMywgMTkuNC4yLjQsIDE5LjQuMi42LCAxOS40LjIuOCwgMTkuNC4yLjksIDE5LjQuMi4xMCwgMTkuNC4yLjExLCAxOS40LjIuMTIsIDE5LjQuMi4xMywgMTkuNC4yLjE0XG4gICdoYXNJbnN0YW5jZSxpc0NvbmNhdFNwcmVhZGFibGUsaXRlcmF0b3IsbWF0Y2gscmVwbGFjZSxzZWFyY2gsc3BlY2llcyxzcGxpdCx0b1ByaW1pdGl2ZSx0b1N0cmluZ1RhZyx1bnNjb3BhYmxlcydcbikuc3BsaXQoJywnKSwgaSA9IDA7IHN5bWJvbHMubGVuZ3RoID4gaTsgKXdrcyhzeW1ib2xzW2krK10pO1xuXG5mb3IodmFyIHN5bWJvbHMgPSAka2V5cyh3a3Muc3RvcmUpLCBpID0gMDsgc3ltYm9scy5sZW5ndGggPiBpOyApd2tzRGVmaW5lKHN5bWJvbHNbaSsrXSk7XG5cbiRleHBvcnQoJGV4cG9ydC5TICsgJGV4cG9ydC5GICogIVVTRV9OQVRJVkUsICdTeW1ib2wnLCB7XG4gIC8vIDE5LjQuMi4xIFN5bWJvbC5mb3Ioa2V5KVxuICAnZm9yJzogZnVuY3Rpb24oa2V5KXtcbiAgICByZXR1cm4gaGFzKFN5bWJvbFJlZ2lzdHJ5LCBrZXkgKz0gJycpXG4gICAgICA/IFN5bWJvbFJlZ2lzdHJ5W2tleV1cbiAgICAgIDogU3ltYm9sUmVnaXN0cnlba2V5XSA9ICRTeW1ib2woa2V5KTtcbiAgfSxcbiAgLy8gMTkuNC4yLjUgU3ltYm9sLmtleUZvcihzeW0pXG4gIGtleUZvcjogZnVuY3Rpb24ga2V5Rm9yKGtleSl7XG4gICAgaWYoaXNTeW1ib2woa2V5KSlyZXR1cm4ga2V5T2YoU3ltYm9sUmVnaXN0cnksIGtleSk7XG4gICAgdGhyb3cgVHlwZUVycm9yKGtleSArICcgaXMgbm90IGEgc3ltYm9sIScpO1xuICB9LFxuICB1c2VTZXR0ZXI6IGZ1bmN0aW9uKCl7IHNldHRlciA9IHRydWU7IH0sXG4gIHVzZVNpbXBsZTogZnVuY3Rpb24oKXsgc2V0dGVyID0gZmFsc2U7IH1cbn0pO1xuXG4kZXhwb3J0KCRleHBvcnQuUyArICRleHBvcnQuRiAqICFVU0VfTkFUSVZFLCAnT2JqZWN0Jywge1xuICAvLyAxOS4xLjIuMiBPYmplY3QuY3JlYXRlKE8gWywgUHJvcGVydGllc10pXG4gIGNyZWF0ZTogJGNyZWF0ZSxcbiAgLy8gMTkuMS4yLjQgT2JqZWN0LmRlZmluZVByb3BlcnR5KE8sIFAsIEF0dHJpYnV0ZXMpXG4gIGRlZmluZVByb3BlcnR5OiAkZGVmaW5lUHJvcGVydHksXG4gIC8vIDE5LjEuMi4zIE9iamVjdC5kZWZpbmVQcm9wZXJ0aWVzKE8sIFByb3BlcnRpZXMpXG4gIGRlZmluZVByb3BlcnRpZXM6ICRkZWZpbmVQcm9wZXJ0aWVzLFxuICAvLyAxOS4xLjIuNiBPYmplY3QuZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yKE8sIFApXG4gIGdldE93blByb3BlcnR5RGVzY3JpcHRvcjogJGdldE93blByb3BlcnR5RGVzY3JpcHRvcixcbiAgLy8gMTkuMS4yLjcgT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXMoTylcbiAgZ2V0T3duUHJvcGVydHlOYW1lczogJGdldE93blByb3BlcnR5TmFtZXMsXG4gIC8vIDE5LjEuMi44IE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHMoTylcbiAgZ2V0T3duUHJvcGVydHlTeW1ib2xzOiAkZ2V0T3duUHJvcGVydHlTeW1ib2xzXG59KTtcblxuLy8gMjQuMy4yIEpTT04uc3RyaW5naWZ5KHZhbHVlIFssIHJlcGxhY2VyIFssIHNwYWNlXV0pXG4kSlNPTiAmJiAkZXhwb3J0KCRleHBvcnQuUyArICRleHBvcnQuRiAqICghVVNFX05BVElWRSB8fCAkZmFpbHMoZnVuY3Rpb24oKXtcbiAgdmFyIFMgPSAkU3ltYm9sKCk7XG4gIC8vIE1TIEVkZ2UgY29udmVydHMgc3ltYm9sIHZhbHVlcyB0byBKU09OIGFzIHt9XG4gIC8vIFdlYktpdCBjb252ZXJ0cyBzeW1ib2wgdmFsdWVzIHRvIEpTT04gYXMgbnVsbFxuICAvLyBWOCB0aHJvd3Mgb24gYm94ZWQgc3ltYm9sc1xuICByZXR1cm4gX3N0cmluZ2lmeShbU10pICE9ICdbbnVsbF0nIHx8IF9zdHJpbmdpZnkoe2E6IFN9KSAhPSAne30nIHx8IF9zdHJpbmdpZnkoT2JqZWN0KFMpKSAhPSAne30nO1xufSkpLCAnSlNPTicsIHtcbiAgc3RyaW5naWZ5OiBmdW5jdGlvbiBzdHJpbmdpZnkoaXQpe1xuICAgIGlmKGl0ID09PSB1bmRlZmluZWQgfHwgaXNTeW1ib2woaXQpKXJldHVybjsgLy8gSUU4IHJldHVybnMgc3RyaW5nIG9uIHVuZGVmaW5lZFxuICAgIHZhciBhcmdzID0gW2l0XVxuICAgICAgLCBpICAgID0gMVxuICAgICAgLCByZXBsYWNlciwgJHJlcGxhY2VyO1xuICAgIHdoaWxlKGFyZ3VtZW50cy5sZW5ndGggPiBpKWFyZ3MucHVzaChhcmd1bWVudHNbaSsrXSk7XG4gICAgcmVwbGFjZXIgPSBhcmdzWzFdO1xuICAgIGlmKHR5cGVvZiByZXBsYWNlciA9PSAnZnVuY3Rpb24nKSRyZXBsYWNlciA9IHJlcGxhY2VyO1xuICAgIGlmKCRyZXBsYWNlciB8fCAhaXNBcnJheShyZXBsYWNlcikpcmVwbGFjZXIgPSBmdW5jdGlvbihrZXksIHZhbHVlKXtcbiAgICAgIGlmKCRyZXBsYWNlcil2YWx1ZSA9ICRyZXBsYWNlci5jYWxsKHRoaXMsIGtleSwgdmFsdWUpO1xuICAgICAgaWYoIWlzU3ltYm9sKHZhbHVlKSlyZXR1cm4gdmFsdWU7XG4gICAgfTtcbiAgICBhcmdzWzFdID0gcmVwbGFjZXI7XG4gICAgcmV0dXJuIF9zdHJpbmdpZnkuYXBwbHkoJEpTT04sIGFyZ3MpO1xuICB9XG59KTtcblxuLy8gMTkuNC4zLjQgU3ltYm9sLnByb3RvdHlwZVtAQHRvUHJpbWl0aXZlXShoaW50KVxuJFN5bWJvbFtQUk9UT1RZUEVdW1RPX1BSSU1JVElWRV0gfHwgcmVxdWlyZSgnLi9faGlkZScpKCRTeW1ib2xbUFJPVE9UWVBFXSwgVE9fUFJJTUlUSVZFLCAkU3ltYm9sW1BST1RPVFlQRV0udmFsdWVPZik7XG4vLyAxOS40LjMuNSBTeW1ib2wucHJvdG90eXBlW0BAdG9TdHJpbmdUYWddXG5zZXRUb1N0cmluZ1RhZygkU3ltYm9sLCAnU3ltYm9sJyk7XG4vLyAyMC4yLjEuOSBNYXRoW0BAdG9TdHJpbmdUYWddXG5zZXRUb1N0cmluZ1RhZyhNYXRoLCAnTWF0aCcsIHRydWUpO1xuLy8gMjQuMy4zIEpTT05bQEB0b1N0cmluZ1RhZ11cbnNldFRvU3RyaW5nVGFnKGdsb2JhbC5KU09OLCAnSlNPTicsIHRydWUpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYuc3ltYm9sLmpzXG4vLyBtb2R1bGUgaWQgPSAxMjRcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsInJlcXVpcmUoJy4vX3drcy1kZWZpbmUnKSgnYXN5bmNJdGVyYXRvcicpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczcuc3ltYm9sLmFzeW5jLWl0ZXJhdG9yLmpzXG4vLyBtb2R1bGUgaWQgPSAxMjVcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsInJlcXVpcmUoJy4vX3drcy1kZWZpbmUnKSgnb2JzZXJ2YWJsZScpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczcuc3ltYm9sLm9ic2VydmFibGUuanNcbi8vIG1vZHVsZSBpZCA9IDEyNlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwidmFyIElURVJBVE9SICAgICA9IHJlcXVpcmUoJy4vX3drcycpKCdpdGVyYXRvcicpXG4gICwgU0FGRV9DTE9TSU5HID0gZmFsc2U7XG5cbnRyeSB7XG4gIHZhciByaXRlciA9IFs3XVtJVEVSQVRPUl0oKTtcbiAgcml0ZXJbJ3JldHVybiddID0gZnVuY3Rpb24oKXsgU0FGRV9DTE9TSU5HID0gdHJ1ZTsgfTtcbiAgQXJyYXkuZnJvbShyaXRlciwgZnVuY3Rpb24oKXsgdGhyb3cgMjsgfSk7XG59IGNhdGNoKGUpeyAvKiBlbXB0eSAqLyB9XG5cbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oZXhlYywgc2tpcENsb3Npbmcpe1xuICBpZighc2tpcENsb3NpbmcgJiYgIVNBRkVfQ0xPU0lORylyZXR1cm4gZmFsc2U7XG4gIHZhciBzYWZlID0gZmFsc2U7XG4gIHRyeSB7XG4gICAgdmFyIGFyciAgPSBbN11cbiAgICAgICwgaXRlciA9IGFycltJVEVSQVRPUl0oKTtcbiAgICBpdGVyLm5leHQgPSBmdW5jdGlvbigpeyByZXR1cm4ge2RvbmU6IHNhZmUgPSB0cnVlfTsgfTtcbiAgICBhcnJbSVRFUkFUT1JdID0gZnVuY3Rpb24oKXsgcmV0dXJuIGl0ZXI7IH07XG4gICAgZXhlYyhhcnIpO1xuICB9IGNhdGNoKGUpeyAvKiBlbXB0eSAqLyB9XG4gIHJldHVybiBzYWZlO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXItZGV0ZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSAxMzZcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vYXJyYXkvZnJvbVwiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvYXJyYXkvZnJvbS5qc1xuLy8gbW9kdWxlIGlkID0gMTM3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczYuc3RyaW5nLml0ZXJhdG9yJyk7XG5yZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5hcnJheS5mcm9tJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4uLy4uL21vZHVsZXMvX2NvcmUnKS5BcnJheS5mcm9tO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vYXJyYXkvZnJvbS5qc1xuLy8gbW9kdWxlIGlkID0gMTM4XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IiwiJ3VzZSBzdHJpY3QnO1xudmFyICRkZWZpbmVQcm9wZXJ0eSA9IHJlcXVpcmUoJy4vX29iamVjdC1kcCcpXG4gICwgY3JlYXRlRGVzYyAgICAgID0gcmVxdWlyZSgnLi9fcHJvcGVydHktZGVzYycpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKG9iamVjdCwgaW5kZXgsIHZhbHVlKXtcbiAgaWYoaW5kZXggaW4gb2JqZWN0KSRkZWZpbmVQcm9wZXJ0eS5mKG9iamVjdCwgaW5kZXgsIGNyZWF0ZURlc2MoMCwgdmFsdWUpKTtcbiAgZWxzZSBvYmplY3RbaW5kZXhdID0gdmFsdWU7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY3JlYXRlLXByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAxMzlcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUiLCIndXNlIHN0cmljdCc7XG52YXIgY3R4ICAgICAgICAgICAgPSByZXF1aXJlKCcuL19jdHgnKVxuICAsICRleHBvcnQgICAgICAgID0gcmVxdWlyZSgnLi9fZXhwb3J0JylcbiAgLCB0b09iamVjdCAgICAgICA9IHJlcXVpcmUoJy4vX3RvLW9iamVjdCcpXG4gICwgY2FsbCAgICAgICAgICAgPSByZXF1aXJlKCcuL19pdGVyLWNhbGwnKVxuICAsIGlzQXJyYXlJdGVyICAgID0gcmVxdWlyZSgnLi9faXMtYXJyYXktaXRlcicpXG4gICwgdG9MZW5ndGggICAgICAgPSByZXF1aXJlKCcuL190by1sZW5ndGgnKVxuICAsIGNyZWF0ZVByb3BlcnR5ID0gcmVxdWlyZSgnLi9fY3JlYXRlLXByb3BlcnR5JylcbiAgLCBnZXRJdGVyRm4gICAgICA9IHJlcXVpcmUoJy4vY29yZS5nZXQtaXRlcmF0b3ItbWV0aG9kJyk7XG5cbiRleHBvcnQoJGV4cG9ydC5TICsgJGV4cG9ydC5GICogIXJlcXVpcmUoJy4vX2l0ZXItZGV0ZWN0JykoZnVuY3Rpb24oaXRlcil7IEFycmF5LmZyb20oaXRlcik7IH0pLCAnQXJyYXknLCB7XG4gIC8vIDIyLjEuMi4xIEFycmF5LmZyb20oYXJyYXlMaWtlLCBtYXBmbiA9IHVuZGVmaW5lZCwgdGhpc0FyZyA9IHVuZGVmaW5lZClcbiAgZnJvbTogZnVuY3Rpb24gZnJvbShhcnJheUxpa2UvKiwgbWFwZm4gPSB1bmRlZmluZWQsIHRoaXNBcmcgPSB1bmRlZmluZWQqLyl7XG4gICAgdmFyIE8gICAgICAgPSB0b09iamVjdChhcnJheUxpa2UpXG4gICAgICAsIEMgICAgICAgPSB0eXBlb2YgdGhpcyA9PSAnZnVuY3Rpb24nID8gdGhpcyA6IEFycmF5XG4gICAgICAsIGFMZW4gICAgPSBhcmd1bWVudHMubGVuZ3RoXG4gICAgICAsIG1hcGZuICAgPSBhTGVuID4gMSA/IGFyZ3VtZW50c1sxXSA6IHVuZGVmaW5lZFxuICAgICAgLCBtYXBwaW5nID0gbWFwZm4gIT09IHVuZGVmaW5lZFxuICAgICAgLCBpbmRleCAgID0gMFxuICAgICAgLCBpdGVyRm4gID0gZ2V0SXRlckZuKE8pXG4gICAgICAsIGxlbmd0aCwgcmVzdWx0LCBzdGVwLCBpdGVyYXRvcjtcbiAgICBpZihtYXBwaW5nKW1hcGZuID0gY3R4KG1hcGZuLCBhTGVuID4gMiA/IGFyZ3VtZW50c1syXSA6IHVuZGVmaW5lZCwgMik7XG4gICAgLy8gaWYgb2JqZWN0IGlzbid0IGl0ZXJhYmxlIG9yIGl0J3MgYXJyYXkgd2l0aCBkZWZhdWx0IGl0ZXJhdG9yIC0gdXNlIHNpbXBsZSBjYXNlXG4gICAgaWYoaXRlckZuICE9IHVuZGVmaW5lZCAmJiAhKEMgPT0gQXJyYXkgJiYgaXNBcnJheUl0ZXIoaXRlckZuKSkpe1xuICAgICAgZm9yKGl0ZXJhdG9yID0gaXRlckZuLmNhbGwoTyksIHJlc3VsdCA9IG5ldyBDOyAhKHN0ZXAgPSBpdGVyYXRvci5uZXh0KCkpLmRvbmU7IGluZGV4Kyspe1xuICAgICAgICBjcmVhdGVQcm9wZXJ0eShyZXN1bHQsIGluZGV4LCBtYXBwaW5nID8gY2FsbChpdGVyYXRvciwgbWFwZm4sIFtzdGVwLnZhbHVlLCBpbmRleF0sIHRydWUpIDogc3RlcC52YWx1ZSk7XG4gICAgICB9XG4gICAgfSBlbHNlIHtcbiAgICAgIGxlbmd0aCA9IHRvTGVuZ3RoKE8ubGVuZ3RoKTtcbiAgICAgIGZvcihyZXN1bHQgPSBuZXcgQyhsZW5ndGgpOyBsZW5ndGggPiBpbmRleDsgaW5kZXgrKyl7XG4gICAgICAgIGNyZWF0ZVByb3BlcnR5KHJlc3VsdCwgaW5kZXgsIG1hcHBpbmcgPyBtYXBmbihPW2luZGV4XSwgaW5kZXgpIDogT1tpbmRleF0pO1xuICAgICAgfVxuICAgIH1cbiAgICByZXN1bHQubGVuZ3RoID0gaW5kZXg7XG4gICAgcmV0dXJuIHJlc3VsdDtcbiAgfVxufSk7XG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2LmFycmF5LmZyb20uanNcbi8vIG1vZHVsZSBpZCA9IDE0MFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cbmltcG9ydCBMb2NhbGl6YXRpb25FeGNlcHRpb24gZnJvbSAnQGFwcC9jbGRyL2V4Y2VwdGlvbi9sb2NhbGl6YXRpb24nO1xuaW1wb3J0IE51bWJlclNwZWNpZmljYXRpb24gZnJvbSAnQGFwcC9jbGRyL3NwZWNpZmljYXRpb25zL251bWJlcic7XG5cbi8qKlxuICogQ3VycmVuY3kgZGlzcGxheSBvcHRpb246IHN5bWJvbCBub3RhdGlvbi5cbiAqL1xuY29uc3QgQ1VSUkVOQ1lfRElTUExBWV9TWU1CT0wgPSAnc3ltYm9sJztcblxuXG5jbGFzcyBQcmljZVNwZWNpZmljYXRpb24gZXh0ZW5kcyBOdW1iZXJTcGVjaWZpY2F0aW9uIHtcbiAgLyoqXG4gICAqIFByaWNlIHNwZWNpZmljYXRpb24gY29uc3RydWN0b3IuXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgcG9zaXRpdmVQYXR0ZXJuIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIGZvciBwb3NpdGl2ZSBhbW91bnRzXG4gICAqIEBwYXJhbSBzdHJpbmcgbmVnYXRpdmVQYXR0ZXJuIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIGZvciBuZWdhdGl2ZSBhbW91bnRzXG4gICAqIEBwYXJhbSBOdW1iZXJTeW1ib2wgc3ltYm9sIE51bWJlciBzeW1ib2xcbiAgICogQHBhcmFtIGludCBtYXhGcmFjdGlvbkRpZ2l0cyBNYXhpbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3JcbiAgICogQHBhcmFtIGludCBtaW5GcmFjdGlvbkRpZ2l0cyBNaW5pbXVtIG51bWJlciBvZiBkaWdpdHMgYWZ0ZXIgZGVjaW1hbCBzZXBhcmF0b3JcbiAgICogQHBhcmFtIGJvb2wgZ3JvdXBpbmdVc2VkIElzIGRpZ2l0cyBncm91cGluZyB1c2VkID9cbiAgICogQHBhcmFtIGludCBwcmltYXJ5R3JvdXBTaXplIFNpemUgb2YgcHJpbWFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlclxuICAgKiBAcGFyYW0gaW50IHNlY29uZGFyeUdyb3VwU2l6ZSBTaXplIG9mIHNlY29uZGFyeSBkaWdpdHMgZ3JvdXAgaW4gdGhlIG51bWJlclxuICAgKiBAcGFyYW0gc3RyaW5nIGN1cnJlbmN5U3ltYm9sIEN1cnJlbmN5IHN5bWJvbCBvZiB0aGlzIHByaWNlIChlZy4gOiDigqwpXG4gICAqIEBwYXJhbSBjdXJyZW5jeUNvZGUgQ3VycmVuY3kgY29kZSBvZiB0aGlzIHByaWNlIChlLmcuOiBFVVIpXG4gICAqXG4gICAqIEB0aHJvd3MgTG9jYWxpemF0aW9uRXhjZXB0aW9uXG4gICAqL1xuICBjb25zdHJ1Y3RvcihcbiAgICBwb3NpdGl2ZVBhdHRlcm4sXG4gICAgbmVnYXRpdmVQYXR0ZXJuLFxuICAgIHN5bWJvbCxcbiAgICBtYXhGcmFjdGlvbkRpZ2l0cyxcbiAgICBtaW5GcmFjdGlvbkRpZ2l0cyxcbiAgICBncm91cGluZ1VzZWQsXG4gICAgcHJpbWFyeUdyb3VwU2l6ZSxcbiAgICBzZWNvbmRhcnlHcm91cFNpemUsXG4gICAgY3VycmVuY3lTeW1ib2wsXG4gICAgY3VycmVuY3lDb2RlLFxuICApIHtcbiAgICBzdXBlcihcbiAgICAgIHBvc2l0aXZlUGF0dGVybixcbiAgICAgIG5lZ2F0aXZlUGF0dGVybixcbiAgICAgIHN5bWJvbCxcbiAgICAgIG1heEZyYWN0aW9uRGlnaXRzLFxuICAgICAgbWluRnJhY3Rpb25EaWdpdHMsXG4gICAgICBncm91cGluZ1VzZWQsXG4gICAgICBwcmltYXJ5R3JvdXBTaXplLFxuICAgICAgc2Vjb25kYXJ5R3JvdXBTaXplLFxuICAgICk7XG4gICAgdGhpcy5jdXJyZW5jeVN5bWJvbCA9IGN1cnJlbmN5U3ltYm9sO1xuICAgIHRoaXMuY3VycmVuY3lDb2RlID0gY3VycmVuY3lDb2RlO1xuXG4gICAgaWYgKCF0aGlzLmN1cnJlbmN5U3ltYm9sIHx8IHR5cGVvZiB0aGlzLmN1cnJlbmN5U3ltYm9sICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBjdXJyZW5jeVN5bWJvbCcpO1xuICAgIH1cblxuICAgIGlmICghdGhpcy5jdXJyZW5jeUNvZGUgfHwgdHlwZW9mIHRoaXMuY3VycmVuY3lDb2RlICE9PSAnc3RyaW5nJykge1xuICAgICAgdGhyb3cgbmV3IExvY2FsaXphdGlvbkV4Y2VwdGlvbignSW52YWxpZCBjdXJyZW5jeUNvZGUnKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogR2V0IHR5cGUgb2YgZGlzcGxheSBmb3IgY3VycmVuY3kgc3ltYm9sLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgc3RhdGljIGdldEN1cnJlbmN5RGlzcGxheSgpIHtcbiAgICByZXR1cm4gQ1VSUkVOQ1lfRElTUExBWV9TWU1CT0w7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBjdXJyZW5jeSBzeW1ib2xcbiAgICogZS5nLjog4oKsLlxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgZ2V0Q3VycmVuY3lTeW1ib2woKSB7XG4gICAgcmV0dXJuIHRoaXMuY3VycmVuY3lTeW1ib2w7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHRoZSBjdXJyZW5jeSBJU08gY29kZVxuICAgKiBlLmcuOiBFVVIuXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqL1xuICBnZXRDdXJyZW5jeUNvZGUoKSB7XG4gICAgcmV0dXJuIHRoaXMuY3VycmVuY3lDb2RlO1xuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IFByaWNlU3BlY2lmaWNhdGlvbjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL3NwZWNpZmljYXRpb25zL3ByaWNlLmpzIiwiXCJ1c2Ugc3RyaWN0XCI7XG5cbmV4cG9ydHMuX19lc01vZHVsZSA9IHRydWU7XG5cbnZhciBfZnJvbSA9IHJlcXVpcmUoXCIuLi9jb3JlLWpzL2FycmF5L2Zyb21cIik7XG5cbnZhciBfZnJvbTIgPSBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KF9mcm9tKTtcblxuZnVuY3Rpb24gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChvYmopIHsgcmV0dXJuIG9iaiAmJiBvYmouX19lc01vZHVsZSA/IG9iaiA6IHsgZGVmYXVsdDogb2JqIH07IH1cblxuZXhwb3J0cy5kZWZhdWx0ID0gZnVuY3Rpb24gKGFycikge1xuICBpZiAoQXJyYXkuaXNBcnJheShhcnIpKSB7XG4gICAgZm9yICh2YXIgaSA9IDAsIGFycjIgPSBBcnJheShhcnIubGVuZ3RoKTsgaSA8IGFyci5sZW5ndGg7IGkrKykge1xuICAgICAgYXJyMltpXSA9IGFycltpXTtcbiAgICB9XG5cbiAgICByZXR1cm4gYXJyMjtcbiAgfSBlbHNlIHtcbiAgICByZXR1cm4gKDAsIF9mcm9tMi5kZWZhdWx0KShhcnIpO1xuICB9XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvdG9Db25zdW1hYmxlQXJyYXkuanNcbi8vIG1vZHVsZSBpZCA9IDE0NFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vZ2V0LWl0ZXJhdG9yXCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9nZXQtaXRlcmF0b3IuanNcbi8vIG1vZHVsZSBpZCA9IDE1MVxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDE0IiwibW9kdWxlLmV4cG9ydHMgPSB7IFwiZGVmYXVsdFwiOiByZXF1aXJlKFwiY29yZS1qcy9saWJyYXJ5L2ZuL2lzLWl0ZXJhYmxlXCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9pcy1pdGVyYWJsZS5qc1xuLy8gbW9kdWxlIGlkID0gMTUyXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgMTQiLCJcInVzZSBzdHJpY3RcIjtcblxuZXhwb3J0cy5fX2VzTW9kdWxlID0gdHJ1ZTtcblxudmFyIF9pc0l0ZXJhYmxlMiA9IHJlcXVpcmUoXCIuLi9jb3JlLWpzL2lzLWl0ZXJhYmxlXCIpO1xuXG52YXIgX2lzSXRlcmFibGUzID0gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChfaXNJdGVyYWJsZTIpO1xuXG52YXIgX2dldEl0ZXJhdG9yMiA9IHJlcXVpcmUoXCIuLi9jb3JlLWpzL2dldC1pdGVyYXRvclwiKTtcblxudmFyIF9nZXRJdGVyYXRvcjMgPSBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KF9nZXRJdGVyYXRvcjIpO1xuXG5mdW5jdGlvbiBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KG9iaikgeyByZXR1cm4gb2JqICYmIG9iai5fX2VzTW9kdWxlID8gb2JqIDogeyBkZWZhdWx0OiBvYmogfTsgfVxuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoKSB7XG4gIGZ1bmN0aW9uIHNsaWNlSXRlcmF0b3IoYXJyLCBpKSB7XG4gICAgdmFyIF9hcnIgPSBbXTtcbiAgICB2YXIgX24gPSB0cnVlO1xuICAgIHZhciBfZCA9IGZhbHNlO1xuICAgIHZhciBfZSA9IHVuZGVmaW5lZDtcblxuICAgIHRyeSB7XG4gICAgICBmb3IgKHZhciBfaSA9ICgwLCBfZ2V0SXRlcmF0b3IzLmRlZmF1bHQpKGFyciksIF9zOyAhKF9uID0gKF9zID0gX2kubmV4dCgpKS5kb25lKTsgX24gPSB0cnVlKSB7XG4gICAgICAgIF9hcnIucHVzaChfcy52YWx1ZSk7XG5cbiAgICAgICAgaWYgKGkgJiYgX2Fyci5sZW5ndGggPT09IGkpIGJyZWFrO1xuICAgICAgfVxuICAgIH0gY2F0Y2ggKGVycikge1xuICAgICAgX2QgPSB0cnVlO1xuICAgICAgX2UgPSBlcnI7XG4gICAgfSBmaW5hbGx5IHtcbiAgICAgIHRyeSB7XG4gICAgICAgIGlmICghX24gJiYgX2lbXCJyZXR1cm5cIl0pIF9pW1wicmV0dXJuXCJdKCk7XG4gICAgICB9IGZpbmFsbHkge1xuICAgICAgICBpZiAoX2QpIHRocm93IF9lO1xuICAgICAgfVxuICAgIH1cblxuICAgIHJldHVybiBfYXJyO1xuICB9XG5cbiAgcmV0dXJuIGZ1bmN0aW9uIChhcnIsIGkpIHtcbiAgICBpZiAoQXJyYXkuaXNBcnJheShhcnIpKSB7XG4gICAgICByZXR1cm4gYXJyO1xuICAgIH0gZWxzZSBpZiAoKDAsIF9pc0l0ZXJhYmxlMy5kZWZhdWx0KShPYmplY3QoYXJyKSkpIHtcbiAgICAgIHJldHVybiBzbGljZUl0ZXJhdG9yKGFyciwgaSk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoXCJJbnZhbGlkIGF0dGVtcHQgdG8gZGVzdHJ1Y3R1cmUgbm9uLWl0ZXJhYmxlIGluc3RhbmNlXCIpO1xuICAgIH1cbiAgfTtcbn0oKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL3NsaWNlZFRvQXJyYXkuanNcbi8vIG1vZHVsZSBpZCA9IDE1M1xuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDE0IiwicmVxdWlyZSgnLi4vbW9kdWxlcy93ZWIuZG9tLml0ZXJhYmxlJyk7XG5yZXF1aXJlKCcuLi9tb2R1bGVzL2VzNi5zdHJpbmcuaXRlcmF0b3InKTtcbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi4vbW9kdWxlcy9jb3JlLmdldC1pdGVyYXRvcicpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vZ2V0LWl0ZXJhdG9yLmpzXG4vLyBtb2R1bGUgaWQgPSAxNTRcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCAxNCIsInJlcXVpcmUoJy4uL21vZHVsZXMvd2ViLmRvbS5pdGVyYWJsZScpO1xucmVxdWlyZSgnLi4vbW9kdWxlcy9lczYuc3RyaW5nLml0ZXJhdG9yJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4uL21vZHVsZXMvY29yZS5pcy1pdGVyYWJsZScpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vaXMtaXRlcmFibGUuanNcbi8vIG1vZHVsZSBpZCA9IDE1NVxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDE0IiwidmFyIGFuT2JqZWN0ID0gcmVxdWlyZSgnLi9fYW4tb2JqZWN0JylcbiAgLCBnZXQgICAgICA9IHJlcXVpcmUoJy4vY29yZS5nZXQtaXRlcmF0b3ItbWV0aG9kJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4vX2NvcmUnKS5nZXRJdGVyYXRvciA9IGZ1bmN0aW9uKGl0KXtcbiAgdmFyIGl0ZXJGbiA9IGdldChpdCk7XG4gIGlmKHR5cGVvZiBpdGVyRm4gIT0gJ2Z1bmN0aW9uJyl0aHJvdyBUeXBlRXJyb3IoaXQgKyAnIGlzIG5vdCBpdGVyYWJsZSEnKTtcbiAgcmV0dXJuIGFuT2JqZWN0KGl0ZXJGbi5jYWxsKGl0KSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9jb3JlLmdldC1pdGVyYXRvci5qc1xuLy8gbW9kdWxlIGlkID0gMTU2XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgMTQiLCJ2YXIgY2xhc3NvZiAgID0gcmVxdWlyZSgnLi9fY2xhc3NvZicpXG4gICwgSVRFUkFUT1IgID0gcmVxdWlyZSgnLi9fd2tzJykoJ2l0ZXJhdG9yJylcbiAgLCBJdGVyYXRvcnMgPSByZXF1aXJlKCcuL19pdGVyYXRvcnMnKTtcbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi9fY29yZScpLmlzSXRlcmFibGUgPSBmdW5jdGlvbihpdCl7XG4gIHZhciBPID0gT2JqZWN0KGl0KTtcbiAgcmV0dXJuIE9bSVRFUkFUT1JdICE9PSB1bmRlZmluZWRcbiAgICB8fCAnQEBpdGVyYXRvcicgaW4gT1xuICAgIHx8IEl0ZXJhdG9ycy5oYXNPd25Qcm9wZXJ0eShjbGFzc29mKE8pKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2NvcmUuaXMtaXRlcmFibGUuanNcbi8vIG1vZHVsZSBpZCA9IDE1N1xuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDE0IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuaW1wb3J0IE51bWJlckZvcm1hdHRlciBmcm9tICdAYXBwL2NsZHIvbnVtYmVyLWZvcm1hdHRlcic7XG5pbXBvcnQgTnVtYmVyU3ltYm9sIGZyb20gJ0BhcHAvY2xkci9udW1iZXItc3ltYm9sJztcbmltcG9ydCBQcmljZVNwZWNpZmljYXRpb24gZnJvbSAnQGFwcC9jbGRyL3NwZWNpZmljYXRpb25zL3ByaWNlJztcbmltcG9ydCBOdW1iZXJTcGVjaWZpY2F0aW9uIGZyb20gJ0BhcHAvY2xkci9zcGVjaWZpY2F0aW9ucy9udW1iZXInO1xuXG5leHBvcnQge1xuICBQcmljZVNwZWNpZmljYXRpb24sXG4gIE51bWJlclNwZWNpZmljYXRpb24sXG4gIE51bWJlckZvcm1hdHRlcixcbiAgTnVtYmVyU3ltYm9sLFxufTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL2luZGV4LmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuLyoqXG4gKiBUaGVzZSBwbGFjZWhvbGRlcnMgYXJlIHVzZWQgaW4gQ0xEUiBudW1iZXIgZm9ybWF0dGluZyB0ZW1wbGF0ZXMuXG4gKiBUaGV5IGFyZSBtZWFudCB0byBiZSByZXBsYWNlZCBieSB0aGUgY29ycmVjdCBsb2NhbGl6ZWQgc3ltYm9scyBpbiB0aGUgbnVtYmVyIGZvcm1hdHRpbmcgcHJvY2Vzcy5cbiAqL1xuaW1wb3J0IE51bWJlclN5bWJvbCBmcm9tICdAYXBwL2NsZHIvbnVtYmVyLXN5bWJvbCc7XG5pbXBvcnQgUHJpY2VTcGVjaWZpY2F0aW9uIGZyb20gJ0BhcHAvY2xkci9zcGVjaWZpY2F0aW9ucy9wcmljZSc7XG5pbXBvcnQgTnVtYmVyU3BlY2lmaWNhdGlvbiBmcm9tICdAYXBwL2NsZHIvc3BlY2lmaWNhdGlvbnMvbnVtYmVyJztcblxuY29uc3QgZXNjYXBlUkUgPSByZXF1aXJlKCdsb2Rhc2guZXNjYXBlcmVnZXhwJyk7XG5cbmNvbnN0IENVUlJFTkNZX1NZTUJPTF9QTEFDRUhPTERFUiA9ICfCpCc7XG5jb25zdCBERUNJTUFMX1NFUEFSQVRPUl9QTEFDRUhPTERFUiA9ICcuJztcbmNvbnN0IEdST1VQX1NFUEFSQVRPUl9QTEFDRUhPTERFUiA9ICcsJztcbmNvbnN0IE1JTlVTX1NJR05fUExBQ0VIT0xERVIgPSAnLSc7XG5jb25zdCBQRVJDRU5UX1NZTUJPTF9QTEFDRUhPTERFUiA9ICclJztcbmNvbnN0IFBMVVNfU0lHTl9QTEFDRUhPTERFUiA9ICcrJztcblxuY2xhc3MgTnVtYmVyRm9ybWF0dGVyIHtcbiAgLyoqXG4gICAqIEBwYXJhbSBOdW1iZXJTcGVjaWZpY2F0aW9uIHNwZWNpZmljYXRpb24gTnVtYmVyIHNwZWNpZmljYXRpb24gdG8gYmUgdXNlZFxuICAgKiAgIChjYW4gYmUgYSBudW1iZXIgc3BlYywgYSBwcmljZSBzcGVjLCBhIHBlcmNlbnRhZ2Ugc3BlYylcbiAgICovXG4gIGNvbnN0cnVjdG9yKHNwZWNpZmljYXRpb24pIHtcbiAgICB0aGlzLm51bWJlclNwZWNpZmljYXRpb24gPSBzcGVjaWZpY2F0aW9uO1xuICB9XG5cbiAgLyoqXG4gICAqIEZvcm1hdHMgdGhlIHBhc3NlZCBudW1iZXIgYWNjb3JkaW5nIHRvIHNwZWNpZmljYXRpb25zLlxuICAgKlxuICAgKiBAcGFyYW0gaW50fGZsb2F0fHN0cmluZyBudW1iZXIgVGhlIG51bWJlciB0byBmb3JtYXRcbiAgICogQHBhcmFtIE51bWJlclNwZWNpZmljYXRpb24gc3BlY2lmaWNhdGlvbiBOdW1iZXIgc3BlY2lmaWNhdGlvbiB0byBiZSB1c2VkXG4gICAqICAgKGNhbiBiZSBhIG51bWJlciBzcGVjLCBhIHByaWNlIHNwZWMsIGEgcGVyY2VudGFnZSBzcGVjKVxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZyBUaGUgZm9ybWF0dGVkIG51bWJlclxuICAgKiAgICAgICAgICAgICAgICBZb3Ugc2hvdWxkIHVzZSB0aGlzIHRoaXMgdmFsdWUgZm9yIGRpc3BsYXksIHdpdGhvdXQgbW9kaWZ5aW5nIGl0XG4gICAqL1xuICBmb3JtYXQobnVtYmVyLCBzcGVjaWZpY2F0aW9uKSB7XG4gICAgaWYgKHNwZWNpZmljYXRpb24gIT09IHVuZGVmaW5lZCkge1xuICAgICAgdGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uID0gc3BlY2lmaWNhdGlvbjtcbiAgICB9XG5cbiAgICAvKlxuICAgICAqIFdlIG5lZWQgdG8gd29yayBvbiB0aGUgYWJzb2x1dGUgdmFsdWUgZmlyc3QuXG4gICAgICogVGhlbiB0aGUgQ0xEUiBwYXR0ZXJuIHdpbGwgYWRkIHRoZSBzaWduIGlmIHJlbGV2YW50IChhdCB0aGUgZW5kKS5cbiAgICAgKi9cbiAgICBjb25zdCBudW0gPSBNYXRoLmFicyhudW1iZXIpLnRvRml4ZWQodGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmdldE1heEZyYWN0aW9uRGlnaXRzKCkpO1xuXG4gICAgbGV0IFttYWpvckRpZ2l0cywgbWlub3JEaWdpdHNdID0gdGhpcy5leHRyYWN0TWFqb3JNaW5vckRpZ2l0cyhudW0pO1xuICAgIG1ham9yRGlnaXRzID0gdGhpcy5zcGxpdE1ham9yR3JvdXBzKG1ham9yRGlnaXRzKTtcbiAgICBtaW5vckRpZ2l0cyA9IHRoaXMuYWRqdXN0TWlub3JEaWdpdHNaZXJvZXMobWlub3JEaWdpdHMpO1xuXG4gICAgLy8gQXNzZW1ibGUgdGhlIGZpbmFsIG51bWJlclxuICAgIGxldCBmb3JtYXR0ZWROdW1iZXIgPSBtYWpvckRpZ2l0cztcbiAgICBpZiAobWlub3JEaWdpdHMpIHtcbiAgICAgIGZvcm1hdHRlZE51bWJlciArPSBERUNJTUFMX1NFUEFSQVRPUl9QTEFDRUhPTERFUiArIG1pbm9yRGlnaXRzO1xuICAgIH1cblxuICAgIC8vIEdldCB0aGUgZ29vZCBDTERSIGZvcm1hdHRpbmcgcGF0dGVybi4gU2lnbiBpcyBpbXBvcnRhbnQgaGVyZSAhXG4gICAgY29uc3QgcGF0dGVybiA9IHRoaXMuZ2V0Q2xkclBhdHRlcm4obnVtYmVyIDwgMCk7XG4gICAgZm9ybWF0dGVkTnVtYmVyID0gdGhpcy5hZGRQbGFjZWhvbGRlcnMoZm9ybWF0dGVkTnVtYmVyLCBwYXR0ZXJuKTtcbiAgICBmb3JtYXR0ZWROdW1iZXIgPSB0aGlzLnJlcGxhY2VTeW1ib2xzKGZvcm1hdHRlZE51bWJlcik7XG5cbiAgICBmb3JtYXR0ZWROdW1iZXIgPSB0aGlzLnBlcmZvcm1TcGVjaWZpY1JlcGxhY2VtZW50cyhmb3JtYXR0ZWROdW1iZXIpO1xuXG4gICAgcmV0dXJuIGZvcm1hdHRlZE51bWJlcjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgbnVtYmVyJ3MgbWFqb3IgYW5kIG1pbm9yIGRpZ2l0cy5cbiAgICpcbiAgICogTWFqb3IgZGlnaXRzIGFyZSB0aGUgXCJpbnRlZ2VyXCIgcGFydCAoYmVmb3JlIGRlY2ltYWwgc2VwYXJhdG9yKSxcbiAgICogbWlub3IgZGlnaXRzIGFyZSB0aGUgZnJhY3Rpb25hbCBwYXJ0XG4gICAqIFJlc3VsdCB3aWxsIGJlIGFuIGFycmF5IG9mIGV4YWN0bHkgMiBpdGVtczogW21ham9yRGlnaXRzLCBtaW5vckRpZ2l0c11cbiAgICpcbiAgICogVXNhZ2UgZXhhbXBsZTpcbiAgICogIGxpc3QobWFqb3JEaWdpdHMsIG1pbm9yRGlnaXRzKSA9IHRoaXMuZ2V0TWFqb3JNaW5vckRpZ2l0cyhkZWNpbWFsTnVtYmVyKTtcbiAgICpcbiAgICogQHBhcmFtIERlY2ltYWxOdW1iZXIgbnVtYmVyXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nW11cbiAgICovXG4gIGV4dHJhY3RNYWpvck1pbm9yRGlnaXRzKG51bWJlcikge1xuICAgIC8vIEdldCB0aGUgbnVtYmVyJ3MgbWFqb3IgYW5kIG1pbm9yIGRpZ2l0cy5cbiAgICBjb25zdCByZXN1bHQgPSBudW1iZXIudG9TdHJpbmcoKS5zcGxpdCgnLicpO1xuICAgIGNvbnN0IG1ham9yRGlnaXRzID0gcmVzdWx0WzBdO1xuICAgIGNvbnN0IG1pbm9yRGlnaXRzID0gKHJlc3VsdFsxXSA9PT0gdW5kZWZpbmVkKSA/ICcnIDogcmVzdWx0WzFdO1xuICAgIHJldHVybiBbbWFqb3JEaWdpdHMsIG1pbm9yRGlnaXRzXTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTcGxpdHMgbWFqb3IgZGlnaXRzIGludG8gZ3JvdXBzLlxuICAgKlxuICAgKiBlLmcuOiBHaXZlbiB0aGUgbWFqb3IgZGlnaXRzIFwiMTIzNDU2N1wiLCBhbmQgbWFqb3IgZ3JvdXAgc2l6ZVxuICAgKiAgY29uZmlndXJlZCB0byAzIGRpZ2l0cywgdGhlIHJlc3VsdCB3b3VsZCBiZSBcIjEgMjM0IDU2N1wiXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgbWFqb3JEaWdpdHMgVGhlIG1ham9yIGRpZ2l0cyB0byBiZSBncm91cGVkXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nIFRoZSBncm91cGVkIG1ham9yIGRpZ2l0c1xuICAgKi9cbiAgc3BsaXRNYWpvckdyb3VwcyhkaWdpdCkge1xuICAgIGlmICghdGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmlzR3JvdXBpbmdVc2VkKCkpIHtcbiAgICAgIHJldHVybiBkaWdpdDtcbiAgICB9XG5cbiAgICAvLyBSZXZlcnNlIHRoZSBtYWpvciBkaWdpdHMsIHNpbmNlIHRoZXkgYXJlIGdyb3VwZWQgZnJvbSB0aGUgcmlnaHQuXG4gICAgY29uc3QgbWFqb3JEaWdpdHMgPSBkaWdpdC5zcGxpdCgnJykucmV2ZXJzZSgpO1xuXG4gICAgLy8gR3JvdXAgdGhlIG1ham9yIGRpZ2l0cy5cbiAgICBsZXQgZ3JvdXBzID0gW107XG4gICAgZ3JvdXBzLnB1c2gobWFqb3JEaWdpdHMuc3BsaWNlKDAsIHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRQcmltYXJ5R3JvdXBTaXplKCkpKTtcbiAgICB3aGlsZSAobWFqb3JEaWdpdHMubGVuZ3RoKSB7XG4gICAgICBncm91cHMucHVzaChtYWpvckRpZ2l0cy5zcGxpY2UoMCwgdGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmdldFNlY29uZGFyeUdyb3VwU2l6ZSgpKSk7XG4gICAgfVxuXG4gICAgLy8gUmV2ZXJzZSBiYWNrIHRoZSBkaWdpdHMgYW5kIHRoZSBncm91cHNcbiAgICBncm91cHMgPSBncm91cHMucmV2ZXJzZSgpO1xuICAgIGNvbnN0IG5ld0dyb3VwcyA9IFtdO1xuICAgIGdyb3Vwcy5mb3JFYWNoKChncm91cCkgPT4ge1xuICAgICAgbmV3R3JvdXBzLnB1c2goZ3JvdXAucmV2ZXJzZSgpLmpvaW4oJycpKTtcbiAgICB9KTtcblxuICAgIC8vIFJlY29uc3RydWN0IHRoZSBtYWpvciBkaWdpdHMuXG4gICAgcmV0dXJuIG5ld0dyb3Vwcy5qb2luKEdST1VQX1NFUEFSQVRPUl9QTEFDRUhPTERFUik7XG4gIH1cblxuICAvKipcbiAgICogQWRkcyBvciByZW1vdmUgdHJhaWxpbmcgemVyb2VzLCBkZXBlbmRpbmcgb24gc3BlY2lmaWVkIG1pbiBhbmQgbWF4IGZyYWN0aW9uIGRpZ2l0cyBudW1iZXJzLlxuICAgKlxuICAgKiBAcGFyYW0gc3RyaW5nIG1pbm9yRGlnaXRzIERpZ2l0cyB0byBiZSBhZGp1c3RlZCB3aXRoICh0cmltbWVkIG9yIHBhZGRlZCkgemVyb2VzXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nIFRoZSBhZGp1c3RlZCBtaW5vciBkaWdpdHNcbiAgICovXG4gIGFkanVzdE1pbm9yRGlnaXRzWmVyb2VzKG1pbm9yRGlnaXRzKSB7XG4gICAgbGV0IGRpZ2l0ID0gbWlub3JEaWdpdHM7XG4gICAgaWYgKGRpZ2l0Lmxlbmd0aCA+IHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRNYXhGcmFjdGlvbkRpZ2l0cygpKSB7XG4gICAgICAvLyBTdHJpcCBhbnkgdHJhaWxpbmcgemVyb2VzLlxuICAgICAgZGlnaXQgPSBkaWdpdC5yZXBsYWNlKC8wKyQvLCAnJyk7XG4gICAgfVxuXG4gICAgaWYgKGRpZ2l0Lmxlbmd0aCA8IHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRNaW5GcmFjdGlvbkRpZ2l0cygpKSB7XG4gICAgICAvLyBSZS1hZGQgbmVlZGVkIHplcm9lc1xuICAgICAgZGlnaXQgPSBkaWdpdC5wYWRFbmQoXG4gICAgICAgIHRoaXMubnVtYmVyU3BlY2lmaWNhdGlvbi5nZXRNaW5GcmFjdGlvbkRpZ2l0cygpLFxuICAgICAgICAnMCcsXG4gICAgICApO1xuICAgIH1cblxuICAgIHJldHVybiBkaWdpdDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuLlxuICAgKlxuICAgKiBAc2VlIGh0dHA6Ly9jbGRyLnVuaWNvZGUub3JnL3RyYW5zbGF0aW9uL251bWJlci1wYXR0ZXJuc1xuICAgKlxuICAgKiBAcGFyYW0gYm9vbCBpc05lZ2F0aXZlIElmIHRydWUsIHRoZSBuZWdhdGl2ZSBwYXR0ZXJuXG4gICAqIHdpbGwgYmUgcmV0dXJuZWQgaW5zdGVhZCBvZiB0aGUgcG9zaXRpdmUgb25lXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nIFRoZSBDTERSIGZvcm1hdHRpbmcgcGF0dGVyblxuICAgKi9cbiAgZ2V0Q2xkclBhdHRlcm4oaXNOZWdhdGl2ZSkge1xuICAgIGlmIChpc05lZ2F0aXZlKSB7XG4gICAgICByZXR1cm4gdGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmdldE5lZ2F0aXZlUGF0dGVybigpO1xuICAgIH1cblxuICAgIHJldHVybiB0aGlzLm51bWJlclNwZWNpZmljYXRpb24uZ2V0UG9zaXRpdmVQYXR0ZXJuKCk7XG4gIH1cblxuICAvKipcbiAgICogUmVwbGFjZSBwbGFjZWhvbGRlciBudW1iZXIgc3ltYm9scyB3aXRoIHJlbGV2YW50IG51bWJlcmluZyBzeXN0ZW0ncyBzeW1ib2xzLlxuICAgKlxuICAgKiBAcGFyYW0gc3RyaW5nIG51bWJlclxuICAgKiAgICAgICAgICAgICAgICAgICAgICAgVGhlIG51bWJlciB0byBwcm9jZXNzXG4gICAqXG4gICAqIEByZXR1cm4gc3RyaW5nXG4gICAqICAgICAgICAgICAgICAgIFRoZSBudW1iZXIgd2l0aCByZXBsYWNlZCBzeW1ib2xzXG4gICAqL1xuICByZXBsYWNlU3ltYm9scyhudW1iZXIpIHtcbiAgICBjb25zdCBzeW1ib2xzID0gdGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmdldFN5bWJvbCgpO1xuXG4gICAgY29uc3QgbWFwID0ge307XG4gICAgbWFwW0RFQ0lNQUxfU0VQQVJBVE9SX1BMQUNFSE9MREVSXSA9IHN5bWJvbHMuZ2V0RGVjaW1hbCgpO1xuICAgIG1hcFtHUk9VUF9TRVBBUkFUT1JfUExBQ0VIT0xERVJdID0gc3ltYm9scy5nZXRHcm91cCgpO1xuICAgIG1hcFtNSU5VU19TSUdOX1BMQUNFSE9MREVSXSA9IHN5bWJvbHMuZ2V0TWludXNTaWduKCk7XG4gICAgbWFwW1BFUkNFTlRfU1lNQk9MX1BMQUNFSE9MREVSXSA9IHN5bWJvbHMuZ2V0UGVyY2VudFNpZ24oKTtcbiAgICBtYXBbUExVU19TSUdOX1BMQUNFSE9MREVSXSA9IHN5bWJvbHMuZ2V0UGx1c1NpZ24oKTtcblxuICAgIHJldHVybiB0aGlzLnN0cnRyKG51bWJlciwgbWFwKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBzdHJ0cigpIGZvciBKYXZhU2NyaXB0XG4gICAqIFRyYW5zbGF0ZSBjaGFyYWN0ZXJzIG9yIHJlcGxhY2Ugc3Vic3RyaW5nc1xuICAgKlxuICAgKiBAcGFyYW0gc3RyXG4gICAqICBTdHJpbmcgdG8gcGFyc2VcbiAgICogQHBhcmFtIHBhaXJzXG4gICAqICBIYXNoIG9mICgnZnJvbScgPT4gJ3RvJywgLi4uKS5cbiAgICpcbiAgICogQHJldHVybiBzdHJpbmdcbiAgICovXG4gIHN0cnRyKHN0ciwgcGFpcnMpIHtcbiAgICBjb25zdCBzdWJzdHJzID0gT2JqZWN0LmtleXMocGFpcnMpLm1hcChlc2NhcGVSRSk7XG4gICAgcmV0dXJuIHN0ci5zcGxpdChSZWdFeHAoYCgke3N1YnN0cnMuam9pbignfCcpfSlgKSlcbiAgICAgICAgICAgICAgLm1hcChwYXJ0ID0+IHBhaXJzW3BhcnRdIHx8IHBhcnQpXG4gICAgICAgICAgICAgIC5qb2luKCcnKTtcbiAgfVxuXG5cbiAgLyoqXG4gICAqIEFkZCBtaXNzaW5nIHBsYWNlaG9sZGVycyB0byB0aGUgbnVtYmVyIHVzaW5nIHRoZSBwYXNzZWQgQ0xEUiBwYXR0ZXJuLlxuICAgKlxuICAgKiBNaXNzaW5nIHBsYWNlaG9sZGVycyBjYW4gYmUgdGhlIHBlcmNlbnQgc2lnbiwgY3VycmVuY3kgc3ltYm9sLCBldGMuXG4gICAqXG4gICAqIGUuZy4gd2l0aCBhIGN1cnJlbmN5IENMRFIgcGF0dGVybjpcbiAgICogIC0gUGFzc2VkIG51bWJlciAocGFydGlhbGx5IGZvcm1hdHRlZCk6IDEsMjM0LjU2N1xuICAgKiAgLSBSZXR1cm5lZCBudW1iZXI6IDEsMjM0LjU2NyDCpFxuICAgKiAgKFwiwqRcIiBzeW1ib2wgaXMgdGhlIGN1cnJlbmN5IHN5bWJvbCBwbGFjZWhvbGRlcilcbiAgICpcbiAgICogQHNlZSBodHRwOi8vY2xkci51bmljb2RlLm9yZy90cmFuc2xhdGlvbi9udW1iZXItcGF0dGVybnNcbiAgICpcbiAgICogQHBhcmFtIGZvcm1hdHRlZE51bWJlclxuICAgKiAgTnVtYmVyIHRvIHByb2Nlc3NcbiAgICogQHBhcmFtIHBhdHRlcm5cbiAgICogIENMRFIgZm9ybWF0dGluZyBwYXR0ZXJuIHRvIHVzZVxuICAgKlxuICAgKiBAcmV0dXJuIHN0cmluZ1xuICAgKi9cbiAgYWRkUGxhY2Vob2xkZXJzKGZvcm1hdHRlZE51bWJlciwgcGF0dGVybikge1xuICAgIC8qXG4gICAgICogUmVnZXggZ3JvdXBzIGV4cGxhbmF0aW9uOlxuICAgICAqICMgICAgICAgICAgOiBsaXRlcmFsIFwiI1wiIGNoYXJhY3Rlci4gT25jZS5cbiAgICAgKiAoLCMrKSogICAgIDogYW55IG90aGVyIFwiI1wiIGNoYXJhY3RlcnMgZ3JvdXAsIHNlcGFyYXRlZCBieSBcIixcIi4gWmVybyB0byBpbmZpbml0eSB0aW1lcy5cbiAgICAgKiAwICAgICAgICAgIDogbGl0ZXJhbCBcIjBcIiBjaGFyYWN0ZXIuIE9uY2UuXG4gICAgICogKFxcLlswI10rKSogOiBhbnkgY29tYmluYXRpb24gb2YgXCIwXCIgYW5kIFwiI1wiIGNoYXJhY3RlcnMgZ3JvdXBzLCBzZXBhcmF0ZWQgYnkgJy4nLlxuICAgICAqICAgICAgICAgICAgICBaZXJvIHRvIGluZmluaXR5IHRpbWVzLlxuICAgICAqL1xuICAgIHJldHVybiBwYXR0ZXJuLnJlcGxhY2UoLyM/KCwjKykqMChcXC5bMCNdKykqLywgZm9ybWF0dGVkTnVtYmVyKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBQZXJmb3JtIHNvbWUgbW9yZSBzcGVjaWZpYyByZXBsYWNlbWVudHMuXG4gICAqXG4gICAqIFNwZWNpZmljIHJlcGxhY2VtZW50cyBhcmUgbmVlZGVkIHdoZW4gbnVtYmVyIHNwZWNpZmljYXRpb24gaXMgZXh0ZW5kZWQuXG4gICAqIEZvciBpbnN0YW5jZSwgcHJpY2VzIGhhdmUgYW4gZXh0ZW5kZWQgbnVtYmVyIHNwZWNpZmljYXRpb24gaW4gb3JkZXIgdG9cbiAgICogYWRkIGN1cnJlbmN5IHN5bWJvbCB0byB0aGUgZm9ybWF0dGVkIG51bWJlci5cbiAgICpcbiAgICogQHBhcmFtIHN0cmluZyBmb3JtYXR0ZWROdW1iZXJcbiAgICpcbiAgICogQHJldHVybiBtaXhlZFxuICAgKi9cbiAgcGVyZm9ybVNwZWNpZmljUmVwbGFjZW1lbnRzKGZvcm1hdHRlZE51bWJlcikge1xuICAgIGlmICh0aGlzLm51bWJlclNwZWNpZmljYXRpb24gaW5zdGFuY2VvZiBQcmljZVNwZWNpZmljYXRpb24pIHtcbiAgICAgIHJldHVybiBmb3JtYXR0ZWROdW1iZXJcbiAgICAgICAgLnNwbGl0KENVUlJFTkNZX1NZTUJPTF9QTEFDRUhPTERFUilcbiAgICAgICAgLmpvaW4odGhpcy5udW1iZXJTcGVjaWZpY2F0aW9uLmdldEN1cnJlbmN5U3ltYm9sKCkpO1xuICAgIH1cblxuICAgIHJldHVybiBmb3JtYXR0ZWROdW1iZXI7XG4gIH1cblxuICBzdGF0aWMgYnVpbGQoc3BlY2lmaWNhdGlvbnMpIHtcbiAgICBsZXQgc3ltYm9sO1xuICAgIGlmICh1bmRlZmluZWQgIT09IHNwZWNpZmljYXRpb25zLm51bWJlclN5bWJvbHMpIHtcbiAgICAgIHN5bWJvbCA9IG5ldyBOdW1iZXJTeW1ib2woLi4uc3BlY2lmaWNhdGlvbnMubnVtYmVyU3ltYm9scyk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHN5bWJvbCA9IG5ldyBOdW1iZXJTeW1ib2woLi4uc3BlY2lmaWNhdGlvbnMuc3ltYm9sKTtcbiAgICB9XG5cbiAgICBsZXQgc3BlY2lmaWNhdGlvbjtcbiAgICBpZiAoc3BlY2lmaWNhdGlvbnMuY3VycmVuY3lTeW1ib2wpIHtcbiAgICAgIHNwZWNpZmljYXRpb24gPSBuZXcgUHJpY2VTcGVjaWZpY2F0aW9uKFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5wb3NpdGl2ZVBhdHRlcm4sXG4gICAgICAgIHNwZWNpZmljYXRpb25zLm5lZ2F0aXZlUGF0dGVybixcbiAgICAgICAgc3ltYm9sLFxuICAgICAgICBwYXJzZUludChzcGVjaWZpY2F0aW9ucy5tYXhGcmFjdGlvbkRpZ2l0cywgMTApLFxuICAgICAgICBwYXJzZUludChzcGVjaWZpY2F0aW9ucy5taW5GcmFjdGlvbkRpZ2l0cywgMTApLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5ncm91cGluZ1VzZWQsXG4gICAgICAgIHNwZWNpZmljYXRpb25zLnByaW1hcnlHcm91cFNpemUsXG4gICAgICAgIHNwZWNpZmljYXRpb25zLnNlY29uZGFyeUdyb3VwU2l6ZSxcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMuY3VycmVuY3lTeW1ib2wsXG4gICAgICAgIHNwZWNpZmljYXRpb25zLmN1cnJlbmN5Q29kZSxcbiAgICAgICk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHNwZWNpZmljYXRpb24gPSBuZXcgTnVtYmVyU3BlY2lmaWNhdGlvbihcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMucG9zaXRpdmVQYXR0ZXJuLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5uZWdhdGl2ZVBhdHRlcm4sXG4gICAgICAgIHN5bWJvbCxcbiAgICAgICAgcGFyc2VJbnQoc3BlY2lmaWNhdGlvbnMubWF4RnJhY3Rpb25EaWdpdHMsIDEwKSxcbiAgICAgICAgcGFyc2VJbnQoc3BlY2lmaWNhdGlvbnMubWluRnJhY3Rpb25EaWdpdHMsIDEwKSxcbiAgICAgICAgc3BlY2lmaWNhdGlvbnMuZ3JvdXBpbmdVc2VkLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5wcmltYXJ5R3JvdXBTaXplLFxuICAgICAgICBzcGVjaWZpY2F0aW9ucy5zZWNvbmRhcnlHcm91cFNpemUsXG4gICAgICApO1xuICAgIH1cblxuICAgIHJldHVybiBuZXcgTnVtYmVyRm9ybWF0dGVyKHNwZWNpZmljYXRpb24pO1xuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IE51bWJlckZvcm1hdHRlcjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9jbGRyL251bWJlci1mb3JtYXR0ZXIuanMiLCJtb2R1bGUuZXhwb3J0cyA9IHtcImJhc2VfdXJsXCI6XCJcIixcInJvdXRlc1wiOntcImFkbWluX3Byb2R1Y3RfZm9ybVwiOntcInRva2Vuc1wiOltbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJpZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9jYXRhbG9nL3Byb2R1Y3RzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJpZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiLFwiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0X3J1bGVzX3NlYXJjaFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9jYXRhbG9nL2NhcnQtcnVsZXMvc2VhcmNoXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY3VzdG9tZXJzX3ZpZXdcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3ZpZXdcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY3VzdG9tZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9jdXN0b21lcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImN1c3RvbWVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIixcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY3VzdG9tZXJzX3NlYXJjaFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9jdXN0b21lcnMvc2VhcmNoXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY3VzdG9tZXJzX2NhcnRzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9jYXJ0c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjdXN0b21lcklkXCJdLFtcInRleHRcIixcIi9zZWxsL2N1c3RvbWVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY3VzdG9tZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jdXN0b21lcnNfb3JkZXJzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9vcmRlcnNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY3VzdG9tZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9jdXN0b21lcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImN1c3RvbWVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fYWRkcmVzc2VzX2NyZWF0ZVwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9hZGRyZXNzZXMvbmV3XCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIixcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fYWRkcmVzc2VzX2VkaXRcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2VkaXRcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiYWRkcmVzc0lkXCJdLFtcInRleHRcIixcIi9zZWxsL2FkZHJlc3Nlc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiYWRkcmVzc0lkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCIsXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyX2FkZHJlc3Nlc19lZGl0XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9lZGl0XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJkZWxpdmVyeXxpbnZvaWNlXCIsXCJhZGRyZXNzVHlwZVwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL2FkZHJlc3Nlcy9vcmRlclwiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCIsXCJhZGRyZXNzVHlwZVwiOlwiZGVsaXZlcnl8aW52b2ljZVwifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIixcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydF9hZGRyZXNzZXNfZWRpdFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvZWRpdFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiZGVsaXZlcnl8aW52b2ljZVwiLFwiYWRkcmVzc1R5cGVcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL2FkZHJlc3Nlcy9jYXJ0XCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wiLFwiYWRkcmVzc1R5cGVcIjpcImRlbGl2ZXJ5fGludm9pY2VcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCIsXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX3ZpZXdcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3ZpZXdcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2luZm9cIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2luZm9cIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2NyZWF0ZVwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHMvbmV3XCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2VkaXRfYWRkcmVzc2VzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9hZGRyZXNzZXNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19lZGl0X2NhcnJpZXJcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2NhcnJpZXJcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19lZGl0X2N1cnJlbmN5XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9jdXJyZW5jeVwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2VkaXRfbGFuZ3VhZ2VcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2xhbmd1YWdlXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfc2V0X2RlbGl2ZXJ5X3NldHRpbmdzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9ydWxlcy9kZWxpdmVyeS1zZXR0aW5nc1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2FkZF9jYXJ0X3J1bGVcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2NhcnQtcnVsZXNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlteL10rK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19kZWxldGVfY2FydF9ydWxlXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9kZWxldGVcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlteL10rK1wiLFwiY2FydFJ1bGVJZFwiXSxbXCJ0ZXh0XCIsXCIvY2FydC1ydWxlc1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiW14vXSsrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2FkZF9wcm9kdWN0XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wcm9kdWN0c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2VkaXRfcHJvZHVjdF9wcmljZVwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvcHJpY2VcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwicHJvZHVjdElkXCJdLFtcInRleHRcIixcIi9wcm9kdWN0c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wiLFwicHJvZHVjdElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19lZGl0X3Byb2R1Y3RfcXVhbnRpdHlcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3F1YW50aXR5XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcInByb2R1Y3RJZFwiXSxbXCJ0ZXh0XCIsXCIvcHJvZHVjdHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIixcInByb2R1Y3RJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfZGVsZXRlX3Byb2R1Y3RcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2RlbGV0ZS1wcm9kdWN0XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3BsYWNlXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9zZWxsL29yZGVycy9wbGFjZVwiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfdmlld1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvdmlld1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiLFwiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfZHVwbGljYXRlX2NhcnRcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2R1cGxpY2F0ZS1jYXJ0XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfdXBkYXRlX3Byb2R1Y3RcIjp7XCJ0b2tlbnNcIjpbW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJEZXRhaWxJZFwiXSxbXCJ0ZXh0XCIsXCIvcHJvZHVjdHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wiLFwib3JkZXJEZXRhaWxJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3BhcnRpYWxfcmVmdW5kXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wYXJ0aWFsLXJlZnVuZFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3N0YW5kYXJkX3JlZnVuZFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc3RhbmRhcmQtcmVmdW5kXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfcmV0dXJuX3Byb2R1Y3RcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3JldHVybi1wcm9kdWN0XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfc2VuZF9wcm9jZXNzX29yZGVyX2VtYWlsXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9zZWxsL29yZGVycy9wcm9jZXNzLW9yZGVyLWVtYWlsXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19hZGRfcHJvZHVjdFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvcHJvZHVjdHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19kZWxldGVfcHJvZHVjdFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvZGVsZXRlXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVyRGV0YWlsSWRcIl0sW1widGV4dFwiLFwiL3Byb2R1Y3RzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIixcIm9yZGVyRGV0YWlsSWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19nZXRfZGlzY291bnRzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9kaXNjb3VudHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX2dldF9wcmljZXNcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3ByaWNlc1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfZ2V0X3BheW1lbnRzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wYXltZW50c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfZ2V0X3Byb2R1Y3RzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wcm9kdWN0c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfZ2V0X2ludm9pY2VzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9pbnZvaWNlc1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfZ2V0X2RvY3VtZW50c1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvZG9jdW1lbnRzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19nZXRfc2hpcHBpbmdcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NoaXBwaW5nXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19jYW5jZWxsYXRpb25cIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2NhbmNlbGxhdGlvblwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX2NvbmZpZ3VyZV9wcm9kdWN0X3BhZ2luYXRpb25cIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NvbmZpZ3VyZS1wcm9kdWN0LXBhZ2luYXRpb25cIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOltdLFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3Byb2R1Y3RfcHJpY2VzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wcm9kdWN0cy9wcmljZXNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3Byb2R1Y3RzX3NlYXJjaFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvcHJvZHVjdHMvc2VhcmNoXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119fSxcInByZWZpeFwiOlwiXCIsXCJob3N0XCI6XCJsb2NhbGhvc3RcIixcInBvcnRcIjpcIlwiLFwic2NoZW1lXCI6XCJodHRwXCIsXCJsb2NhbGVcIjpbXX1cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL2pzL2Zvc19qc19yb3V0ZXMuanNvblxuLy8gbW9kdWxlIGlkID0gMTYyXG4vLyBtb2R1bGUgY2h1bmtzID0gMyAxMCAxMyIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvY3JlYXRlXCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3QvY3JlYXRlLmpzXG4vLyBtb2R1bGUgaWQgPSAxNjVcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvZ2V0LXByb3RvdHlwZS1vZlwiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2dldC1wcm90b3R5cGUtb2YuanNcbi8vIG1vZHVsZSBpZCA9IDE2NlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IiwibW9kdWxlLmV4cG9ydHMgPSB7IFwiZGVmYXVsdFwiOiByZXF1aXJlKFwiY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9zZXQtcHJvdG90eXBlLW9mXCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3Qvc2V0LXByb3RvdHlwZS1vZi5qc1xuLy8gbW9kdWxlIGlkID0gMTY3XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQiLCJcInVzZSBzdHJpY3RcIjtcblxuZXhwb3J0cy5fX2VzTW9kdWxlID0gdHJ1ZTtcblxudmFyIF9zZXRQcm90b3R5cGVPZiA9IHJlcXVpcmUoXCIuLi9jb3JlLWpzL29iamVjdC9zZXQtcHJvdG90eXBlLW9mXCIpO1xuXG52YXIgX3NldFByb3RvdHlwZU9mMiA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX3NldFByb3RvdHlwZU9mKTtcblxudmFyIF9jcmVhdGUgPSByZXF1aXJlKFwiLi4vY29yZS1qcy9vYmplY3QvY3JlYXRlXCIpO1xuXG52YXIgX2NyZWF0ZTIgPSBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KF9jcmVhdGUpO1xuXG52YXIgX3R5cGVvZjIgPSByZXF1aXJlKFwiLi4vaGVscGVycy90eXBlb2ZcIik7XG5cbnZhciBfdHlwZW9mMyA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX3R5cGVvZjIpO1xuXG5mdW5jdGlvbiBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KG9iaikgeyByZXR1cm4gb2JqICYmIG9iai5fX2VzTW9kdWxlID8gb2JqIDogeyBkZWZhdWx0OiBvYmogfTsgfVxuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoc3ViQ2xhc3MsIHN1cGVyQ2xhc3MpIHtcbiAgaWYgKHR5cGVvZiBzdXBlckNsYXNzICE9PSBcImZ1bmN0aW9uXCIgJiYgc3VwZXJDbGFzcyAhPT0gbnVsbCkge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoXCJTdXBlciBleHByZXNzaW9uIG11c3QgZWl0aGVyIGJlIG51bGwgb3IgYSBmdW5jdGlvbiwgbm90IFwiICsgKHR5cGVvZiBzdXBlckNsYXNzID09PSBcInVuZGVmaW5lZFwiID8gXCJ1bmRlZmluZWRcIiA6ICgwLCBfdHlwZW9mMy5kZWZhdWx0KShzdXBlckNsYXNzKSkpO1xuICB9XG5cbiAgc3ViQ2xhc3MucHJvdG90eXBlID0gKDAsIF9jcmVhdGUyLmRlZmF1bHQpKHN1cGVyQ2xhc3MgJiYgc3VwZXJDbGFzcy5wcm90b3R5cGUsIHtcbiAgICBjb25zdHJ1Y3Rvcjoge1xuICAgICAgdmFsdWU6IHN1YkNsYXNzLFxuICAgICAgZW51bWVyYWJsZTogZmFsc2UsXG4gICAgICB3cml0YWJsZTogdHJ1ZSxcbiAgICAgIGNvbmZpZ3VyYWJsZTogdHJ1ZVxuICAgIH1cbiAgfSk7XG4gIGlmIChzdXBlckNsYXNzKSBfc2V0UHJvdG90eXBlT2YyLmRlZmF1bHQgPyAoMCwgX3NldFByb3RvdHlwZU9mMi5kZWZhdWx0KShzdWJDbGFzcywgc3VwZXJDbGFzcykgOiBzdWJDbGFzcy5fX3Byb3RvX18gPSBzdXBlckNsYXNzO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2luaGVyaXRzLmpzXG4vLyBtb2R1bGUgaWQgPSAxNjlcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCIsIlwidXNlIHN0cmljdFwiO1xuXG5leHBvcnRzLl9fZXNNb2R1bGUgPSB0cnVlO1xuXG52YXIgX3R5cGVvZjIgPSByZXF1aXJlKFwiLi4vaGVscGVycy90eXBlb2ZcIik7XG5cbnZhciBfdHlwZW9mMyA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX3R5cGVvZjIpO1xuXG5mdW5jdGlvbiBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KG9iaikgeyByZXR1cm4gb2JqICYmIG9iai5fX2VzTW9kdWxlID8gb2JqIDogeyBkZWZhdWx0OiBvYmogfTsgfVxuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoc2VsZiwgY2FsbCkge1xuICBpZiAoIXNlbGYpIHtcbiAgICB0aHJvdyBuZXcgUmVmZXJlbmNlRXJyb3IoXCJ0aGlzIGhhc24ndCBiZWVuIGluaXRpYWxpc2VkIC0gc3VwZXIoKSBoYXNuJ3QgYmVlbiBjYWxsZWRcIik7XG4gIH1cblxuICByZXR1cm4gY2FsbCAmJiAoKHR5cGVvZiBjYWxsID09PSBcInVuZGVmaW5lZFwiID8gXCJ1bmRlZmluZWRcIiA6ICgwLCBfdHlwZW9mMy5kZWZhdWx0KShjYWxsKSkgPT09IFwib2JqZWN0XCIgfHwgdHlwZW9mIGNhbGwgPT09IFwiZnVuY3Rpb25cIikgPyBjYWxsIDogc2VsZjtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9wb3NzaWJsZUNvbnN0cnVjdG9yUmV0dXJuLmpzXG4vLyBtb2R1bGUgaWQgPSAxNzBcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCIsInJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM2Lm9iamVjdC5jcmVhdGUnKTtcbnZhciAkT2JqZWN0ID0gcmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9fY29yZScpLk9iamVjdDtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24gY3JlYXRlKFAsIEQpe1xuICByZXR1cm4gJE9iamVjdC5jcmVhdGUoUCwgRCk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2NyZWF0ZS5qc1xuLy8gbW9kdWxlIGlkID0gMTcxXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQiLCJyZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5vYmplY3QuZ2V0LXByb3RvdHlwZS1vZicpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuT2JqZWN0LmdldFByb3RvdHlwZU9mO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2dldC1wcm90b3R5cGUtb2YuanNcbi8vIG1vZHVsZSBpZCA9IDE3MlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczYub2JqZWN0LnNldC1wcm90b3R5cGUtb2YnKTtcbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9fY29yZScpLk9iamVjdC5zZXRQcm90b3R5cGVPZjtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9zZXQtcHJvdG90eXBlLW9mLmpzXG4vLyBtb2R1bGUgaWQgPSAxNzNcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCIsIi8vIFdvcmtzIHdpdGggX19wcm90b19fIG9ubHkuIE9sZCB2OCBjYW4ndCB3b3JrIHdpdGggbnVsbCBwcm90byBvYmplY3RzLlxuLyogZXNsaW50LWRpc2FibGUgbm8tcHJvdG8gKi9cbnZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpXG4gICwgYW5PYmplY3QgPSByZXF1aXJlKCcuL19hbi1vYmplY3QnKTtcbnZhciBjaGVjayA9IGZ1bmN0aW9uKE8sIHByb3RvKXtcbiAgYW5PYmplY3QoTyk7XG4gIGlmKCFpc09iamVjdChwcm90bykgJiYgcHJvdG8gIT09IG51bGwpdGhyb3cgVHlwZUVycm9yKHByb3RvICsgXCI6IGNhbid0IHNldCBhcyBwcm90b3R5cGUhXCIpO1xufTtcbm1vZHVsZS5leHBvcnRzID0ge1xuICBzZXQ6IE9iamVjdC5zZXRQcm90b3R5cGVPZiB8fCAoJ19fcHJvdG9fXycgaW4ge30gPyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lXG4gICAgZnVuY3Rpb24odGVzdCwgYnVnZ3ksIHNldCl7XG4gICAgICB0cnkge1xuICAgICAgICBzZXQgPSByZXF1aXJlKCcuL19jdHgnKShGdW5jdGlvbi5jYWxsLCByZXF1aXJlKCcuL19vYmplY3QtZ29wZCcpLmYoT2JqZWN0LnByb3RvdHlwZSwgJ19fcHJvdG9fXycpLnNldCwgMik7XG4gICAgICAgIHNldCh0ZXN0LCBbXSk7XG4gICAgICAgIGJ1Z2d5ID0gISh0ZXN0IGluc3RhbmNlb2YgQXJyYXkpO1xuICAgICAgfSBjYXRjaChlKXsgYnVnZ3kgPSB0cnVlOyB9XG4gICAgICByZXR1cm4gZnVuY3Rpb24gc2V0UHJvdG90eXBlT2YoTywgcHJvdG8pe1xuICAgICAgICBjaGVjayhPLCBwcm90byk7XG4gICAgICAgIGlmKGJ1Z2d5KU8uX19wcm90b19fID0gcHJvdG87XG4gICAgICAgIGVsc2Ugc2V0KE8sIHByb3RvKTtcbiAgICAgICAgcmV0dXJuIE87XG4gICAgICB9O1xuICAgIH0oe30sIGZhbHNlKSA6IHVuZGVmaW5lZCksXG4gIGNoZWNrOiBjaGVja1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NldC1wcm90by5qc1xuLy8gbW9kdWxlIGlkID0gMTc1XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQiLCJ2YXIgJGV4cG9ydCA9IHJlcXVpcmUoJy4vX2V4cG9ydCcpXG4vLyAxOS4xLjIuMiAvIDE1LjIuMy41IE9iamVjdC5jcmVhdGUoTyBbLCBQcm9wZXJ0aWVzXSlcbiRleHBvcnQoJGV4cG9ydC5TLCAnT2JqZWN0Jywge2NyZWF0ZTogcmVxdWlyZSgnLi9fb2JqZWN0LWNyZWF0ZScpfSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuY3JlYXRlLmpzXG4vLyBtb2R1bGUgaWQgPSAxNzZcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCIsIi8vIDE5LjEuMi45IE9iamVjdC5nZXRQcm90b3R5cGVPZihPKVxudmFyIHRvT2JqZWN0ICAgICAgICA9IHJlcXVpcmUoJy4vX3RvLW9iamVjdCcpXG4gICwgJGdldFByb3RvdHlwZU9mID0gcmVxdWlyZSgnLi9fb2JqZWN0LWdwbycpO1xuXG5yZXF1aXJlKCcuL19vYmplY3Qtc2FwJykoJ2dldFByb3RvdHlwZU9mJywgZnVuY3Rpb24oKXtcbiAgcmV0dXJuIGZ1bmN0aW9uIGdldFByb3RvdHlwZU9mKGl0KXtcbiAgICByZXR1cm4gJGdldFByb3RvdHlwZU9mKHRvT2JqZWN0KGl0KSk7XG4gIH07XG59KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5nZXQtcHJvdG90eXBlLW9mLmpzXG4vLyBtb2R1bGUgaWQgPSAxNzdcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCIsIi8vIDE5LjEuMy4xOSBPYmplY3Quc2V0UHJvdG90eXBlT2YoTywgcHJvdG8pXG52YXIgJGV4cG9ydCA9IHJlcXVpcmUoJy4vX2V4cG9ydCcpO1xuJGV4cG9ydCgkZXhwb3J0LlMsICdPYmplY3QnLCB7c2V0UHJvdG90eXBlT2Y6IHJlcXVpcmUoJy4vX3NldC1wcm90bycpLnNldH0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LnNldC1wcm90b3R5cGUtb2YuanNcbi8vIG1vZHVsZSBpZCA9IDE3OFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IiwiJ3VzZSBzdHJpY3QnO3ZhciBfZXh0ZW5kcz1PYmplY3QuYXNzaWdufHxmdW5jdGlvbihhKXtmb3IodmFyIGIsYz0xO2M8YXJndW1lbnRzLmxlbmd0aDtjKyspZm9yKHZhciBkIGluIGI9YXJndW1lbnRzW2NdLGIpT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKGIsZCkmJihhW2RdPWJbZF0pO3JldHVybiBhfSxfdHlwZW9mPSdmdW5jdGlvbic9PXR5cGVvZiBTeW1ib2wmJidzeW1ib2wnPT10eXBlb2YgU3ltYm9sLml0ZXJhdG9yP2Z1bmN0aW9uKGEpe3JldHVybiB0eXBlb2YgYX06ZnVuY3Rpb24oYSl7cmV0dXJuIGEmJidmdW5jdGlvbic9PXR5cGVvZiBTeW1ib2wmJmEuY29uc3RydWN0b3I9PT1TeW1ib2wmJmEhPT1TeW1ib2wucHJvdG90eXBlPydzeW1ib2wnOnR5cGVvZiBhfTtmdW5jdGlvbiBfY2xhc3NDYWxsQ2hlY2soYSxiKXtpZighKGEgaW5zdGFuY2VvZiBiKSl0aHJvdyBuZXcgVHlwZUVycm9yKCdDYW5ub3QgY2FsbCBhIGNsYXNzIGFzIGEgZnVuY3Rpb24nKX12YXIgUm91dGluZz1mdW5jdGlvbiBhKCl7dmFyIGI9dGhpcztfY2xhc3NDYWxsQ2hlY2sodGhpcyxhKSx0aGlzLnNldFJvdXRlcz1mdW5jdGlvbihhKXtiLnJvdXRlc1JvdXRpbmc9YXx8W119LHRoaXMuZ2V0Um91dGVzPWZ1bmN0aW9uKCl7cmV0dXJuIGIucm91dGVzUm91dGluZ30sdGhpcy5zZXRCYXNlVXJsPWZ1bmN0aW9uKGEpe2IuY29udGV4dFJvdXRpbmcuYmFzZV91cmw9YX0sdGhpcy5nZXRCYXNlVXJsPWZ1bmN0aW9uKCl7cmV0dXJuIGIuY29udGV4dFJvdXRpbmcuYmFzZV91cmx9LHRoaXMuc2V0UHJlZml4PWZ1bmN0aW9uKGEpe2IuY29udGV4dFJvdXRpbmcucHJlZml4PWF9LHRoaXMuc2V0U2NoZW1lPWZ1bmN0aW9uKGEpe2IuY29udGV4dFJvdXRpbmcuc2NoZW1lPWF9LHRoaXMuZ2V0U2NoZW1lPWZ1bmN0aW9uKCl7cmV0dXJuIGIuY29udGV4dFJvdXRpbmcuc2NoZW1lfSx0aGlzLnNldEhvc3Q9ZnVuY3Rpb24oYSl7Yi5jb250ZXh0Um91dGluZy5ob3N0PWF9LHRoaXMuZ2V0SG9zdD1mdW5jdGlvbigpe3JldHVybiBiLmNvbnRleHRSb3V0aW5nLmhvc3R9LHRoaXMuYnVpbGRRdWVyeVBhcmFtcz1mdW5jdGlvbihhLGMsZCl7dmFyIGU9bmV3IFJlZ0V4cCgvXFxbXSQvKTtjIGluc3RhbmNlb2YgQXJyYXk/Yy5mb3JFYWNoKGZ1bmN0aW9uKGMsZil7ZS50ZXN0KGEpP2QoYSxjKTpiLmJ1aWxkUXVlcnlQYXJhbXMoYSsnWycrKCdvYmplY3QnPT09KCd1bmRlZmluZWQnPT10eXBlb2YgYz8ndW5kZWZpbmVkJzpfdHlwZW9mKGMpKT9mOicnKSsnXScsYyxkKX0pOidvYmplY3QnPT09KCd1bmRlZmluZWQnPT10eXBlb2YgYz8ndW5kZWZpbmVkJzpfdHlwZW9mKGMpKT9PYmplY3Qua2V5cyhjKS5mb3JFYWNoKGZ1bmN0aW9uKGUpe3JldHVybiBiLmJ1aWxkUXVlcnlQYXJhbXMoYSsnWycrZSsnXScsY1tlXSxkKX0pOmQoYSxjKX0sdGhpcy5nZXRSb3V0ZT1mdW5jdGlvbihhKXt2YXIgYz1iLmNvbnRleHRSb3V0aW5nLnByZWZpeCthO2lmKCEhYi5yb3V0ZXNSb3V0aW5nW2NdKXJldHVybiBiLnJvdXRlc1JvdXRpbmdbY107ZWxzZSBpZighYi5yb3V0ZXNSb3V0aW5nW2FdKXRocm93IG5ldyBFcnJvcignVGhlIHJvdXRlIFwiJythKydcIiBkb2VzIG5vdCBleGlzdC4nKTtyZXR1cm4gYi5yb3V0ZXNSb3V0aW5nW2FdfSx0aGlzLmdlbmVyYXRlPWZ1bmN0aW9uKGEsYyxkKXt2YXIgZT1iLmdldFJvdXRlKGEpLGY9Y3x8e30sZz1fZXh0ZW5kcyh7fSxmKSxoPSdfc2NoZW1lJyxpPScnLGo9ITAsaz0nJztpZigoZS50b2tlbnN8fFtdKS5mb3JFYWNoKGZ1bmN0aW9uKGIpe2lmKCd0ZXh0Jz09PWJbMF0pcmV0dXJuIGk9YlsxXStpLHZvaWQoaj0hMSk7aWYoJ3ZhcmlhYmxlJz09PWJbMF0pe3ZhciBjPShlLmRlZmF1bHRzfHx7fSlbYlszXV07aWYoITE9PWp8fCFjfHwoZnx8e30pW2JbM11dJiZmW2JbM11dIT09ZS5kZWZhdWx0c1tiWzNdXSl7dmFyIGQ7aWYoKGZ8fHt9KVtiWzNdXSlkPWZbYlszXV0sZGVsZXRlIGdbYlszXV07ZWxzZSBpZihjKWQ9ZS5kZWZhdWx0c1tiWzNdXTtlbHNle2lmKGopcmV0dXJuO3Rocm93IG5ldyBFcnJvcignVGhlIHJvdXRlIFwiJythKydcIiByZXF1aXJlcyB0aGUgcGFyYW1ldGVyIFwiJytiWzNdKydcIi4nKX12YXIgaD0hMD09PWR8fCExPT09ZHx8Jyc9PT1kO2lmKCFofHwhail7dmFyIGs9ZW5jb2RlVVJJQ29tcG9uZW50KGQpLnJlcGxhY2UoLyUyRi9nLCcvJyk7J251bGwnPT09ayYmbnVsbD09PWQmJihrPScnKSxpPWJbMV0raytpfWo9ITF9ZWxzZSBjJiZkZWxldGUgZ1tiWzNdXTtyZXR1cm59dGhyb3cgbmV3IEVycm9yKCdUaGUgdG9rZW4gdHlwZSBcIicrYlswXSsnXCIgaXMgbm90IHN1cHBvcnRlZC4nKX0pLCcnPT1pJiYoaT0nLycpLChlLmhvc3R0b2tlbnN8fFtdKS5mb3JFYWNoKGZ1bmN0aW9uKGEpe3ZhciBiO3JldHVybid0ZXh0Jz09PWFbMF0/dm9pZChrPWFbMV0rayk6dm9pZCgndmFyaWFibGUnPT09YVswXSYmKChmfHx7fSlbYVszXV0/KGI9ZlthWzNdXSxkZWxldGUgZ1thWzNdXSk6ZS5kZWZhdWx0c1thWzNdXSYmKGI9ZS5kZWZhdWx0c1thWzNdXSksaz1hWzFdK2IraykpfSksaT1iLmNvbnRleHRSb3V0aW5nLmJhc2VfdXJsK2ksZS5yZXF1aXJlbWVudHNbaF0mJmIuZ2V0U2NoZW1lKCkhPT1lLnJlcXVpcmVtZW50c1toXT9pPWUucmVxdWlyZW1lbnRzW2hdKyc6Ly8nKyhrfHxiLmdldEhvc3QoKSkraTprJiZiLmdldEhvc3QoKSE9PWs/aT1iLmdldFNjaGVtZSgpKyc6Ly8nK2sraTohMD09PWQmJihpPWIuZ2V0U2NoZW1lKCkrJzovLycrYi5nZXRIb3N0KCkraSksMDxPYmplY3Qua2V5cyhnKS5sZW5ndGgpe3ZhciBsPVtdLG09ZnVuY3Rpb24oYSxiKXt2YXIgYz1iO2M9J2Z1bmN0aW9uJz09dHlwZW9mIGM/YygpOmMsYz1udWxsPT09Yz8nJzpjLGwucHVzaChlbmNvZGVVUklDb21wb25lbnQoYSkrJz0nK2VuY29kZVVSSUNvbXBvbmVudChjKSl9O09iamVjdC5rZXlzKGcpLmZvckVhY2goZnVuY3Rpb24oYSl7cmV0dXJuIGIuYnVpbGRRdWVyeVBhcmFtcyhhLGdbYV0sbSl9KSxpPWkrJz8nK2wuam9pbignJicpLnJlcGxhY2UoLyUyMC9nLCcrJyl9cmV0dXJuIGl9LHRoaXMuc2V0RGF0YT1mdW5jdGlvbihhKXtiLnNldEJhc2VVcmwoYS5iYXNlX3VybCksYi5zZXRSb3V0ZXMoYS5yb3V0ZXMpLCdwcmVmaXgnaW4gYSYmYi5zZXRQcmVmaXgoYS5wcmVmaXgpLGIuc2V0SG9zdChhLmhvc3QpLGIuc2V0U2NoZW1lKGEuc2NoZW1lKX0sdGhpcy5jb250ZXh0Um91dGluZz17YmFzZV91cmw6JycscHJlZml4OicnLGhvc3Q6Jycsc2NoZW1lOicnfX07bW9kdWxlLmV4cG9ydHM9bmV3IFJvdXRpbmc7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2Zvcy1yb3V0aW5nL2Rpc3Qvcm91dGluZy5qc1xuLy8gbW9kdWxlIGlkID0gMTgwXG4vLyBtb2R1bGUgY2h1bmtzID0gMyAxMCAxMyIsIi8qKlxuICogbG9kYXNoIChDdXN0b20gQnVpbGQpIDxodHRwczovL2xvZGFzaC5jb20vPlxuICogQnVpbGQ6IGBsb2Rhc2ggbW9kdWxhcml6ZSBleHBvcnRzPVwibnBtXCIgLW8gLi9gXG4gKiBDb3B5cmlnaHQgalF1ZXJ5IEZvdW5kYXRpb24gYW5kIG90aGVyIGNvbnRyaWJ1dG9ycyA8aHR0cHM6Ly9qcXVlcnkub3JnLz5cbiAqIFJlbGVhc2VkIHVuZGVyIE1JVCBsaWNlbnNlIDxodHRwczovL2xvZGFzaC5jb20vbGljZW5zZT5cbiAqIEJhc2VkIG9uIFVuZGVyc2NvcmUuanMgMS44LjMgPGh0dHA6Ly91bmRlcnNjb3JlanMub3JnL0xJQ0VOU0U+XG4gKiBDb3B5cmlnaHQgSmVyZW15IEFzaGtlbmFzLCBEb2N1bWVudENsb3VkIGFuZCBJbnZlc3RpZ2F0aXZlIFJlcG9ydGVycyAmIEVkaXRvcnNcbiAqL1xuXG4vKiogVXNlZCBhcyByZWZlcmVuY2VzIGZvciB2YXJpb3VzIGBOdW1iZXJgIGNvbnN0YW50cy4gKi9cbnZhciBJTkZJTklUWSA9IDEgLyAwO1xuXG4vKiogYE9iamVjdCN0b1N0cmluZ2AgcmVzdWx0IHJlZmVyZW5jZXMuICovXG52YXIgc3ltYm9sVGFnID0gJ1tvYmplY3QgU3ltYm9sXSc7XG5cbi8qKlxuICogVXNlZCB0byBtYXRjaCBgUmVnRXhwYFxuICogW3N5bnRheCBjaGFyYWN0ZXJzXShodHRwOi8vZWNtYS1pbnRlcm5hdGlvbmFsLm9yZy9lY21hLTI2Mi82LjAvI3NlYy1wYXR0ZXJucykuXG4gKi9cbnZhciByZVJlZ0V4cENoYXIgPSAvW1xcXFxeJC4qKz8oKVtcXF17fXxdL2csXG4gICAgcmVIYXNSZWdFeHBDaGFyID0gUmVnRXhwKHJlUmVnRXhwQ2hhci5zb3VyY2UpO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYGdsb2JhbGAgZnJvbSBOb2RlLmpzLiAqL1xudmFyIGZyZWVHbG9iYWwgPSB0eXBlb2YgZ2xvYmFsID09ICdvYmplY3QnICYmIGdsb2JhbCAmJiBnbG9iYWwuT2JqZWN0ID09PSBPYmplY3QgJiYgZ2xvYmFsO1xuXG4vKiogRGV0ZWN0IGZyZWUgdmFyaWFibGUgYHNlbGZgLiAqL1xudmFyIGZyZWVTZWxmID0gdHlwZW9mIHNlbGYgPT0gJ29iamVjdCcgJiYgc2VsZiAmJiBzZWxmLk9iamVjdCA9PT0gT2JqZWN0ICYmIHNlbGY7XG5cbi8qKiBVc2VkIGFzIGEgcmVmZXJlbmNlIHRvIHRoZSBnbG9iYWwgb2JqZWN0LiAqL1xudmFyIHJvb3QgPSBmcmVlR2xvYmFsIHx8IGZyZWVTZWxmIHx8IEZ1bmN0aW9uKCdyZXR1cm4gdGhpcycpKCk7XG5cbi8qKiBVc2VkIGZvciBidWlsdC1pbiBtZXRob2QgcmVmZXJlbmNlcy4gKi9cbnZhciBvYmplY3RQcm90byA9IE9iamVjdC5wcm90b3R5cGU7XG5cbi8qKlxuICogVXNlZCB0byByZXNvbHZlIHRoZVxuICogW2B0b1N0cmluZ1RhZ2BdKGh0dHA6Ly9lY21hLWludGVybmF0aW9uYWwub3JnL2VjbWEtMjYyLzYuMC8jc2VjLW9iamVjdC5wcm90b3R5cGUudG9zdHJpbmcpXG4gKiBvZiB2YWx1ZXMuXG4gKi9cbnZhciBvYmplY3RUb1N0cmluZyA9IG9iamVjdFByb3RvLnRvU3RyaW5nO1xuXG4vKiogQnVpbHQtaW4gdmFsdWUgcmVmZXJlbmNlcy4gKi9cbnZhciBTeW1ib2wgPSByb290LlN5bWJvbDtcblxuLyoqIFVzZWQgdG8gY29udmVydCBzeW1ib2xzIHRvIHByaW1pdGl2ZXMgYW5kIHN0cmluZ3MuICovXG52YXIgc3ltYm9sUHJvdG8gPSBTeW1ib2wgPyBTeW1ib2wucHJvdG90eXBlIDogdW5kZWZpbmVkLFxuICAgIHN5bWJvbFRvU3RyaW5nID0gc3ltYm9sUHJvdG8gPyBzeW1ib2xQcm90by50b1N0cmluZyA6IHVuZGVmaW5lZDtcblxuLyoqXG4gKiBUaGUgYmFzZSBpbXBsZW1lbnRhdGlvbiBvZiBgXy50b1N0cmluZ2Agd2hpY2ggZG9lc24ndCBjb252ZXJ0IG51bGxpc2hcbiAqIHZhbHVlcyB0byBlbXB0eSBzdHJpbmdzLlxuICpcbiAqIEBwcml2YXRlXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBwcm9jZXNzLlxuICogQHJldHVybnMge3N0cmluZ30gUmV0dXJucyB0aGUgc3RyaW5nLlxuICovXG5mdW5jdGlvbiBiYXNlVG9TdHJpbmcodmFsdWUpIHtcbiAgLy8gRXhpdCBlYXJseSBmb3Igc3RyaW5ncyB0byBhdm9pZCBhIHBlcmZvcm1hbmNlIGhpdCBpbiBzb21lIGVudmlyb25tZW50cy5cbiAgaWYgKHR5cGVvZiB2YWx1ZSA9PSAnc3RyaW5nJykge1xuICAgIHJldHVybiB2YWx1ZTtcbiAgfVxuICBpZiAoaXNTeW1ib2wodmFsdWUpKSB7XG4gICAgcmV0dXJuIHN5bWJvbFRvU3RyaW5nID8gc3ltYm9sVG9TdHJpbmcuY2FsbCh2YWx1ZSkgOiAnJztcbiAgfVxuICB2YXIgcmVzdWx0ID0gKHZhbHVlICsgJycpO1xuICByZXR1cm4gKHJlc3VsdCA9PSAnMCcgJiYgKDEgLyB2YWx1ZSkgPT0gLUlORklOSVRZKSA/ICctMCcgOiByZXN1bHQ7XG59XG5cbi8qKlxuICogQ2hlY2tzIGlmIGB2YWx1ZWAgaXMgb2JqZWN0LWxpa2UuIEEgdmFsdWUgaXMgb2JqZWN0LWxpa2UgaWYgaXQncyBub3QgYG51bGxgXG4gKiBhbmQgaGFzIGEgYHR5cGVvZmAgcmVzdWx0IG9mIFwib2JqZWN0XCIuXG4gKlxuICogQHN0YXRpY1xuICogQG1lbWJlck9mIF9cbiAqIEBzaW5jZSA0LjAuMFxuICogQGNhdGVnb3J5IExhbmdcbiAqIEBwYXJhbSB7Kn0gdmFsdWUgVGhlIHZhbHVlIHRvIGNoZWNrLlxuICogQHJldHVybnMge2Jvb2xlYW59IFJldHVybnMgYHRydWVgIGlmIGB2YWx1ZWAgaXMgb2JqZWN0LWxpa2UsIGVsc2UgYGZhbHNlYC5cbiAqIEBleGFtcGxlXG4gKlxuICogXy5pc09iamVjdExpa2Uoe30pO1xuICogLy8gPT4gdHJ1ZVxuICpcbiAqIF8uaXNPYmplY3RMaWtlKFsxLCAyLCAzXSk7XG4gKiAvLyA9PiB0cnVlXG4gKlxuICogXy5pc09iamVjdExpa2UoXy5ub29wKTtcbiAqIC8vID0+IGZhbHNlXG4gKlxuICogXy5pc09iamVjdExpa2UobnVsbCk7XG4gKiAvLyA9PiBmYWxzZVxuICovXG5mdW5jdGlvbiBpc09iamVjdExpa2UodmFsdWUpIHtcbiAgcmV0dXJuICEhdmFsdWUgJiYgdHlwZW9mIHZhbHVlID09ICdvYmplY3QnO1xufVxuXG4vKipcbiAqIENoZWNrcyBpZiBgdmFsdWVgIGlzIGNsYXNzaWZpZWQgYXMgYSBgU3ltYm9sYCBwcmltaXRpdmUgb3Igb2JqZWN0LlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgNC4wLjBcbiAqIEBjYXRlZ29yeSBMYW5nXG4gKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBjaGVjay5cbiAqIEByZXR1cm5zIHtib29sZWFufSBSZXR1cm5zIGB0cnVlYCBpZiBgdmFsdWVgIGlzIGEgc3ltYm9sLCBlbHNlIGBmYWxzZWAuXG4gKiBAZXhhbXBsZVxuICpcbiAqIF8uaXNTeW1ib2woU3ltYm9sLml0ZXJhdG9yKTtcbiAqIC8vID0+IHRydWVcbiAqXG4gKiBfLmlzU3ltYm9sKCdhYmMnKTtcbiAqIC8vID0+IGZhbHNlXG4gKi9cbmZ1bmN0aW9uIGlzU3ltYm9sKHZhbHVlKSB7XG4gIHJldHVybiB0eXBlb2YgdmFsdWUgPT0gJ3N5bWJvbCcgfHxcbiAgICAoaXNPYmplY3RMaWtlKHZhbHVlKSAmJiBvYmplY3RUb1N0cmluZy5jYWxsKHZhbHVlKSA9PSBzeW1ib2xUYWcpO1xufVxuXG4vKipcbiAqIENvbnZlcnRzIGB2YWx1ZWAgdG8gYSBzdHJpbmcuIEFuIGVtcHR5IHN0cmluZyBpcyByZXR1cm5lZCBmb3IgYG51bGxgXG4gKiBhbmQgYHVuZGVmaW5lZGAgdmFsdWVzLiBUaGUgc2lnbiBvZiBgLTBgIGlzIHByZXNlcnZlZC5cbiAqXG4gKiBAc3RhdGljXG4gKiBAbWVtYmVyT2YgX1xuICogQHNpbmNlIDQuMC4wXG4gKiBAY2F0ZWdvcnkgTGFuZ1xuICogQHBhcmFtIHsqfSB2YWx1ZSBUaGUgdmFsdWUgdG8gcHJvY2Vzcy5cbiAqIEByZXR1cm5zIHtzdHJpbmd9IFJldHVybnMgdGhlIHN0cmluZy5cbiAqIEBleGFtcGxlXG4gKlxuICogXy50b1N0cmluZyhudWxsKTtcbiAqIC8vID0+ICcnXG4gKlxuICogXy50b1N0cmluZygtMCk7XG4gKiAvLyA9PiAnLTAnXG4gKlxuICogXy50b1N0cmluZyhbMSwgMiwgM10pO1xuICogLy8gPT4gJzEsMiwzJ1xuICovXG5mdW5jdGlvbiB0b1N0cmluZyh2YWx1ZSkge1xuICByZXR1cm4gdmFsdWUgPT0gbnVsbCA/ICcnIDogYmFzZVRvU3RyaW5nKHZhbHVlKTtcbn1cblxuLyoqXG4gKiBFc2NhcGVzIHRoZSBgUmVnRXhwYCBzcGVjaWFsIGNoYXJhY3RlcnMgXCJeXCIsIFwiJFwiLCBcIlxcXCIsIFwiLlwiLCBcIipcIiwgXCIrXCIsXG4gKiBcIj9cIiwgXCIoXCIsIFwiKVwiLCBcIltcIiwgXCJdXCIsIFwie1wiLCBcIn1cIiwgYW5kIFwifFwiIGluIGBzdHJpbmdgLlxuICpcbiAqIEBzdGF0aWNcbiAqIEBtZW1iZXJPZiBfXG4gKiBAc2luY2UgMy4wLjBcbiAqIEBjYXRlZ29yeSBTdHJpbmdcbiAqIEBwYXJhbSB7c3RyaW5nfSBbc3RyaW5nPScnXSBUaGUgc3RyaW5nIHRvIGVzY2FwZS5cbiAqIEByZXR1cm5zIHtzdHJpbmd9IFJldHVybnMgdGhlIGVzY2FwZWQgc3RyaW5nLlxuICogQGV4YW1wbGVcbiAqXG4gKiBfLmVzY2FwZVJlZ0V4cCgnW2xvZGFzaF0oaHR0cHM6Ly9sb2Rhc2guY29tLyknKTtcbiAqIC8vID0+ICdcXFtsb2Rhc2hcXF1cXChodHRwczovL2xvZGFzaFxcLmNvbS9cXCknXG4gKi9cbmZ1bmN0aW9uIGVzY2FwZVJlZ0V4cChzdHJpbmcpIHtcbiAgc3RyaW5nID0gdG9TdHJpbmcoc3RyaW5nKTtcbiAgcmV0dXJuIChzdHJpbmcgJiYgcmVIYXNSZWdFeHBDaGFyLnRlc3Qoc3RyaW5nKSlcbiAgICA/IHN0cmluZy5yZXBsYWNlKHJlUmVnRXhwQ2hhciwgJ1xcXFwkJicpXG4gICAgOiBzdHJpbmc7XG59XG5cbm1vZHVsZS5leHBvcnRzID0gZXNjYXBlUmVnRXhwO1xuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2xvZGFzaC5lc2NhcGVyZWdleHAvaW5kZXguanNcbi8vIG1vZHVsZSBpZCA9IDE4MVxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5leHBvcnQgZGVmYXVsdCB7XG4gIHByb2R1Y3REZWxldGVkRnJvbU9yZGVyOiAncHJvZHVjdERlbGV0ZWRGcm9tT3JkZXInLFxuICBwcm9kdWN0QWRkZWRUb09yZGVyOiAncHJvZHVjdEFkZGVkVG9PcmRlcicsXG4gIHByb2R1Y3RVcGRhdGVkOiAncHJvZHVjdFVwZGF0ZWQnLFxuICBwcm9kdWN0RWRpdGlvbkNhbmNlbGVkOiAncHJvZHVjdEVkaXRpb25DYW5jZWxlZCcsXG4gIHByb2R1Y3RMaXN0UGFnaW5hdGVkOiAncHJvZHVjdExpc3RQYWdpbmF0ZWQnLFxuICBwcm9kdWN0TGlzdE51bWJlclBlclBhZ2U6ICdwcm9kdWN0TGlzdE51bWJlclBlclBhZ2UnLFxufTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItdmlldy1ldmVudC1tYXAuanMiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L3ZhbHVlc1wiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L3ZhbHVlcy5qc1xuLy8gbW9kdWxlIGlkID0gMTk0XG4vLyBtb2R1bGUgY2h1bmtzID0gMyAxMCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IFJvdXRlciBmcm9tICdAY29tcG9uZW50cy9yb3V0ZXInO1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL09yZGVyVmlld1BhZ2VNYXAnO1xuXG5jb25zdCB7JH0gPSB3aW5kb3c7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIE9yZGVyUHJpY2VzUmVmcmVzaGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gIH1cblxuICByZWZyZXNoKG9yZGVySWQpIHtcbiAgICAkLmdldEpTT04odGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19nZXRfcHJpY2VzJywge29yZGVySWR9KSkudGhlbihyZXNwb25zZSA9PiB7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJUb3RhbCkudGV4dChyZXNwb25zZS5vcmRlclRvdGFsRm9ybWF0dGVkKTtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlckRpc2NvdW50c1RvdGFsKS50ZXh0KGAtJHtyZXNwb25zZS5kaXNjb3VudHNBbW91bnRGb3JtYXR0ZWR9YCk7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJEaXNjb3VudHNUb3RhbENvbnRhaW5lcikudG9nZ2xlQ2xhc3MoJ2Qtbm9uZScsICFyZXNwb25zZS5kaXNjb3VudHNBbW91bnREaXNwbGF5ZWQpO1xuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLm9yZGVyUHJvZHVjdHNUb3RhbCkudGV4dChyZXNwb25zZS5wcm9kdWN0c1RvdGFsRm9ybWF0dGVkKTtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlclNoaXBwaW5nVG90YWwpLnRleHQocmVzcG9uc2Uuc2hpcHBpbmdUb3RhbEZvcm1hdHRlZCk7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJTaGlwcGluZ1RvdGFsQ29udGFpbmVyKS50b2dnbGVDbGFzcygnZC1ub25lJywgIXJlc3BvbnNlLnNoaXBwaW5nVG90YWxEaXNwbGF5ZWQpO1xuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLm9yZGVyVGF4ZXNUb3RhbCkudGV4dChyZXNwb25zZS50YXhlc1RvdGFsRm9ybWF0dGVkKTtcbiAgICB9KTtcbiAgfVxuXG4gIHJlZnJlc2hQcm9kdWN0UHJpY2VzKG9yZGVySWQpIHtcbiAgICAkLmdldEpTT04odGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19wcm9kdWN0X3ByaWNlcycsIHtvcmRlcklkfSkpLnRoZW4ocHJvZHVjdFByaWNlc0xpc3QgPT4ge1xuICAgICAgcHJvZHVjdFByaWNlc0xpc3QuZm9yRWFjaChwcm9kdWN0UHJpY2VzID0+IHtcbiAgICAgICAgY29uc3Qgb3JkZXJQcm9kdWN0VHJJZCA9IE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVJvdyhwcm9kdWN0UHJpY2VzLm9yZGVyRGV0YWlsSWQpO1xuICAgICAgICBsZXQgJHF1YW50aXR5ID0gJChwcm9kdWN0UHJpY2VzLnF1YW50aXR5KTtcbiAgICAgICAgaWYgKHByb2R1Y3RQcmljZXMucXVhbnRpdHkgPiAxKSB7XG4gICAgICAgICAgJHF1YW50aXR5ID0gJHF1YW50aXR5LndyYXAoJzxzcGFuIGNsYXNzPVwiYmFkZ2UgYmFkZ2Utc2Vjb25kYXJ5IHJvdW5kZWQtY2lyY2xlXCI+PC9zcGFuPicpO1xuICAgICAgICB9XG5cbiAgICAgICAgJChgJHtvcmRlclByb2R1Y3RUcklkfSAke09yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRVbml0UHJpY2V9YCkudGV4dChwcm9kdWN0UHJpY2VzLnVuaXRQcmljZSk7XG4gICAgICAgICQoYCR7b3JkZXJQcm9kdWN0VHJJZH0gJHtPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0UXVhbnRpdHl9YCkuaHRtbCgkcXVhbnRpdHkuaHRtbCgpKTtcbiAgICAgICAgJChgJHtvcmRlclByb2R1Y3RUcklkfSAke09yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRBdmFpbGFibGVRdWFudGl0eX1gKS50ZXh0KHByb2R1Y3RQcmljZXMuYXZhaWxhYmxlUXVhbnRpdHkpO1xuICAgICAgICAkKGAke29yZGVyUHJvZHVjdFRySWR9ICR7T3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFRvdGFsUHJpY2V9YCkudGV4dChwcm9kdWN0UHJpY2VzLnRvdGFsUHJpY2UpO1xuXG4gICAgICAgIC8vIHVwZGF0ZSBvcmRlciByb3cgcHJpY2UgdmFsdWVzXG4gICAgICAgIGNvbnN0IHByb2R1Y3RFZGl0QnV0dG9uID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0QnRuKHByb2R1Y3RQcmljZXMub3JkZXJEZXRhaWxJZCkpO1xuXG4gICAgICAgIHByb2R1Y3RFZGl0QnV0dG9uLmRhdGEoJ3Byb2R1Y3QtcHJpY2UtdGF4LWluY2wnLCBwcm9kdWN0UHJpY2VzLnVuaXRQcmljZVRheEluY2xSYXcpO1xuICAgICAgICBwcm9kdWN0RWRpdEJ1dHRvbi5kYXRhKCdwcm9kdWN0LXByaWNlLXRheC1leGNsJywgcHJvZHVjdFByaWNlcy51bml0UHJpY2VUYXhFeGNsUmF3KTtcbiAgICAgICAgcHJvZHVjdEVkaXRCdXR0b24uZGF0YSgncHJvZHVjdC1xdWFudGl0eScsIHByb2R1Y3RQcmljZXMucXVhbnRpdHkpO1xuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cblxuICBjaGVja090aGVyUHJvZHVjdFByaWNlc01hdGNoKGdpdmVuUHJpY2UsIHByb2R1Y3RJZCwgY29tYmluYXRpb25JZCwgaW52b2ljZUlkLCBvcmRlckRldGFpbElkKSB7XG4gICAgY29uc3QgcHJvZHVjdFJvd3MgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCd0ci5jZWxsUHJvZHVjdCcpO1xuICAgIC8vIFdlIGNvbnZlcnQgdGhlIGV4cGVjdGVkIHZhbHVlcyBpbnRvIGludC9mbG9hdCB0byBhdm9pZCBhIHR5cGUgbWlzbWF0Y2ggdGhhdCB3b3VsZCBiZSB3cm9uZ2x5IGludGVycHJldGVkXG4gICAgY29uc3QgZXhwZWN0ZWRQcm9kdWN0SWQgPSBOdW1iZXIocHJvZHVjdElkKTtcbiAgICBjb25zdCBleHBlY3RlZENvbWJpbmF0aW9uSWQgPSBOdW1iZXIoY29tYmluYXRpb25JZCk7XG4gICAgY29uc3QgZXhwZWN0ZWRHaXZlblByaWNlID0gTnVtYmVyKGdpdmVuUHJpY2UpO1xuICAgIGxldCB1bm1hdGNoaW5nUHJpY2VFeGlzdHMgPSBmYWxzZTtcblxuICAgIHByb2R1Y3RSb3dzLmZvckVhY2goKHByb2R1Y3RSb3cpID0+IHtcbiAgICAgIGNvbnN0IHByb2R1Y3RSb3dJZCA9ICQocHJvZHVjdFJvdykuYXR0cignaWQnKTtcblxuICAgICAgLy8gTm8gbmVlZCB0byBjaGVjayBlZGl0ZWQgcm93IChlc3BlY2lhbGx5IGlmIGl0J3MgdGhlIG9ubHkgb25lIGZvciB0aGlzIHByb2R1Y3QpXG4gICAgICBpZiAob3JkZXJEZXRhaWxJZCAmJiBwcm9kdWN0Um93SWQgPT09IGBvcmRlclByb2R1Y3RfJHtvcmRlckRldGFpbElkfWApIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBjb25zdCBwcm9kdWN0RWRpdEJ0biA9ICQoYCMke3Byb2R1Y3RSb3dJZH0gJHtPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0QnV0dG9uc31gKTtcbiAgICAgIGNvbnN0IGN1cnJlbnRPcmRlckludm9pY2VJZCA9IE51bWJlcihwcm9kdWN0RWRpdEJ0bi5kYXRhKCdvcmRlci1pbnZvaWNlLWlkJykpO1xuXG4gICAgICAvLyBObyBuZWVkIHRvIGNoZWNrIHRhcmdldCBpbnZvaWNlLCBvbmx5IGlmIG90aGVycyBoYXZlIG1hdGNoaW5nIHByb2R1Y3RzXG4gICAgICBpZiAoaW52b2ljZUlkICYmIGN1cnJlbnRPcmRlckludm9pY2VJZCAmJiBpbnZvaWNlSWQgPT09IGN1cnJlbnRPcmRlckludm9pY2VJZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIGNvbnN0IGN1cnJlbnRQcm9kdWN0SWQgPSBOdW1iZXIocHJvZHVjdEVkaXRCdG4uZGF0YSgncHJvZHVjdC1pZCcpKTtcbiAgICAgIGNvbnN0IGN1cnJlbnRDb21iaW5hdGlvbklkID0gTnVtYmVyKHByb2R1Y3RFZGl0QnRuLmRhdGEoJ2NvbWJpbmF0aW9uLWlkJykpO1xuXG4gICAgICBpZiAoY3VycmVudFByb2R1Y3RJZCAhPT0gZXhwZWN0ZWRQcm9kdWN0SWQgfHwgY3VycmVudENvbWJpbmF0aW9uSWQgIT09IGV4cGVjdGVkQ29tYmluYXRpb25JZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIGlmIChleHBlY3RlZEdpdmVuUHJpY2UgIT09IE51bWJlcihwcm9kdWN0RWRpdEJ0bi5kYXRhKCdwcm9kdWN0LXByaWNlLXRheC1pbmNsJykpKSB7XG4gICAgICAgIHVubWF0Y2hpbmdQcmljZUV4aXN0cyA9IHRydWU7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICByZXR1cm4gIXVubWF0Y2hpbmdQcmljZUV4aXN0cztcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcmljZXMtcmVmcmVzaGVyLmpzIiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczcub2JqZWN0LnZhbHVlcycpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuT2JqZWN0LnZhbHVlcztcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC92YWx1ZXMuanNcbi8vIG1vZHVsZSBpZCA9IDIxMFxuLy8gbW9kdWxlIGNodW5rcyA9IDMgMTAiLCJ2YXIgZ2V0S2V5cyAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWtleXMnKVxuICAsIHRvSU9iamVjdCA9IHJlcXVpcmUoJy4vX3RvLWlvYmplY3QnKVxuICAsIGlzRW51bSAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1waWUnKS5mO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpc0VudHJpZXMpe1xuICByZXR1cm4gZnVuY3Rpb24oaXQpe1xuICAgIHZhciBPICAgICAgPSB0b0lPYmplY3QoaXQpXG4gICAgICAsIGtleXMgICA9IGdldEtleXMoTylcbiAgICAgICwgbGVuZ3RoID0ga2V5cy5sZW5ndGhcbiAgICAgICwgaSAgICAgID0gMFxuICAgICAgLCByZXN1bHQgPSBbXVxuICAgICAgLCBrZXk7XG4gICAgd2hpbGUobGVuZ3RoID4gaSlpZihpc0VudW0uY2FsbChPLCBrZXkgPSBrZXlzW2krK10pKXtcbiAgICAgIHJlc3VsdC5wdXNoKGlzRW50cmllcyA/IFtrZXksIE9ba2V5XV0gOiBPW2tleV0pO1xuICAgIH0gcmV0dXJuIHJlc3VsdDtcbiAgfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtdG8tYXJyYXkuanNcbi8vIG1vZHVsZSBpZCA9IDIxMVxuLy8gbW9kdWxlIGNodW5rcyA9IDMgMTAiLCIvLyBodHRwczovL2dpdGh1Yi5jb20vdGMzOS9wcm9wb3NhbC1vYmplY3QtdmFsdWVzLWVudHJpZXNcbnZhciAkZXhwb3J0ID0gcmVxdWlyZSgnLi9fZXhwb3J0JylcbiAgLCAkdmFsdWVzID0gcmVxdWlyZSgnLi9fb2JqZWN0LXRvLWFycmF5JykoZmFsc2UpO1xuXG4kZXhwb3J0KCRleHBvcnQuUywgJ09iamVjdCcsIHtcbiAgdmFsdWVzOiBmdW5jdGlvbiB2YWx1ZXMoaXQpe1xuICAgIHJldHVybiAkdmFsdWVzKGl0KTtcbiAgfVxufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNy5vYmplY3QudmFsdWVzLmpzXG4vLyBtb2R1bGUgaWQgPSAyMTNcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDEwIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPcmRlclByaWNlcyB7XG4gIGNhbGN1bGF0ZVRheEV4Y2x1ZGVkKHRheEluY2x1ZGVkLCB0YXhSYXRlUGVyQ2VudCwgY3VycmVuY3lQcmVjaXNpb24pIHtcbiAgICBsZXQgcHJpY2VUYXhJbmNsID0gcGFyc2VGbG9hdCh0YXhJbmNsdWRlZCk7XG4gICAgaWYgKHByaWNlVGF4SW5jbCA8IDAgfHwgTnVtYmVyLmlzTmFOKHByaWNlVGF4SW5jbCkpIHtcbiAgICAgIHByaWNlVGF4SW5jbCA9IDA7XG4gICAgfVxuICAgIGNvbnN0IHRheFJhdGUgPSB0YXhSYXRlUGVyQ2VudCAvIDEwMCArIDE7XG4gICAgcmV0dXJuIHdpbmRvdy5wc19yb3VuZChwcmljZVRheEluY2wgLyB0YXhSYXRlLCBjdXJyZW5jeVByZWNpc2lvbik7XG4gIH1cblxuICBjYWxjdWxhdGVUYXhJbmNsdWRlZCh0YXhFeGNsdWRlZCwgdGF4UmF0ZVBlckNlbnQsIGN1cnJlbmN5UHJlY2lzaW9uKSB7XG4gICAgbGV0IHByaWNlVGF4RXhjbCA9IHBhcnNlRmxvYXQodGF4RXhjbHVkZWQpO1xuICAgIGlmIChwcmljZVRheEV4Y2wgPCAwIHx8IE51bWJlci5pc05hTihwcmljZVRheEV4Y2wpKSB7XG4gICAgICBwcmljZVRheEV4Y2wgPSAwO1xuICAgIH1cbiAgICBjb25zdCB0YXhSYXRlID0gdGF4UmF0ZVBlckNlbnQgLyAxMDAgKyAxO1xuICAgIHJldHVybiB3aW5kb3cucHNfcm91bmQocHJpY2VUYXhFeGNsICogdGF4UmF0ZSwgY3VycmVuY3lQcmVjaXNpb24pO1xuICB9XG5cbiAgY2FsY3VsYXRlVG90YWxQcmljZShxdWFudGl0eSwgdW5pdFByaWNlLCBjdXJyZW5jeVByZWNpc2lvbikge1xuICAgIHJldHVybiB3aW5kb3cucHNfcm91bmQodW5pdFByaWNlICogcXVhbnRpdHksIGN1cnJlbmN5UHJlY2lzaW9uKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcmljZXMuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gJ0BwYWdlcy9vcmRlci9PcmRlclZpZXdQYWdlTWFwJztcbmltcG9ydCBPcmRlclByb2R1Y3RFZGl0IGZyb20gJ0BwYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtZWRpdCc7XG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3JkZXJQcm9kdWN0UmVuZGVyZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgfVxuXG4gIGFkZE9yVXBkYXRlUHJvZHVjdFRvTGlzdCgkcHJvZHVjdFJvdywgbmV3Um93KSB7XG4gICAgaWYgKCRwcm9kdWN0Um93Lmxlbmd0aCA+IDApIHtcbiAgICAgICRwcm9kdWN0Um93Lmh0bWwoJChuZXdSb3cpLmh0bWwoKSk7XG4gICAgfSBlbHNlIHtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkUm93KS5iZWZvcmUoJChuZXdSb3cpLmhpZGUoKS5mYWRlSW4oKSk7XG4gICAgfVxuICB9XG5cbiAgdXBkYXRlTnVtUHJvZHVjdHMobnVtUHJvZHVjdHMpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNDb3VudCkuaHRtbChudW1Qcm9kdWN0cyk7XG4gIH1cblxuICBlZGl0UHJvZHVjdEZyb21MaXN0KFxuICAgIG9yZGVyRGV0YWlsSWQsXG4gICAgcXVhbnRpdHksXG4gICAgcHJpY2VUYXhJbmNsLFxuICAgIHByaWNlVGF4RXhjbCxcbiAgICB0YXhSYXRlLFxuICAgIGxvY2F0aW9uLFxuICAgIGF2YWlsYWJsZVF1YW50aXR5LFxuICAgIGF2YWlsYWJsZU91dE9mU3RvY2ssXG4gICAgb3JkZXJJbnZvaWNlSWQsXG4gICAgaXNPcmRlclRheEluY2x1ZGVkLFxuICApIHtcbiAgICBjb25zdCAkb3JkZXJFZGl0ID0gbmV3IE9yZGVyUHJvZHVjdEVkaXQob3JkZXJEZXRhaWxJZCk7XG4gICAgJG9yZGVyRWRpdC5kaXNwbGF5UHJvZHVjdCh7XG4gICAgICBwcmljZV90YXhfZXhjbDogcHJpY2VUYXhFeGNsLFxuICAgICAgcHJpY2VfdGF4X2luY2w6IHByaWNlVGF4SW5jbCxcbiAgICAgIHRheF9yYXRlOiB0YXhSYXRlLFxuICAgICAgcXVhbnRpdHksXG4gICAgICBsb2NhdGlvbixcbiAgICAgIGF2YWlsYWJsZVF1YW50aXR5LFxuICAgICAgYXZhaWxhYmxlT3V0T2ZTdG9jayxcbiAgICAgIG9yZGVySW52b2ljZUlkLFxuICAgICAgaXNPcmRlclRheEluY2x1ZGVkLFxuICAgIH0pO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkQWN0aW9uQnRuKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRSb3cpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIG1vdmVQcm9kdWN0c1BhbmVsVG9Nb2RpZmljYXRpb25Qb3NpdGlvbihzY3JvbGxUYXJnZXQgPSAnYm9keScpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFjdGlvbkJ0bikuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICQoYCR7T3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkQWN0aW9uQnRufSwgJHtPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRSb3d9YCkucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIHRoaXMubW92ZVByb2R1Y3RQYW5lbFRvVG9wKHNjcm9sbFRhcmdldCk7XG4gIH1cblxuICBtb3ZlUHJvZHVjdHNQYW5lbFRvUmVmdW5kUG9zaXRpb24oKSB7XG4gICAgdGhpcy5yZXNldEFsbEVkaXRSb3dzKCk7XG4gICAgJChgJHtPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRBY3Rpb25CdG59LCAke09yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZFJvd30sICR7T3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWN0aW9uQnRufWApLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICB0aGlzLm1vdmVQcm9kdWN0UGFuZWxUb1RvcCgpO1xuICB9XG5cbiAgbW92ZVByb2R1Y3RQYW5lbFRvVG9wKHNjcm9sbFRhcmdldCA9ICdib2R5Jykge1xuICAgIGNvbnN0ICRtb2RpZmljYXRpb25Qb3NpdGlvbiA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0TW9kaWZpY2F0aW9uUG9zaXRpb24pO1xuICAgIGlmICgkbW9kaWZpY2F0aW9uUG9zaXRpb24uZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzUGFuZWwpLmxlbmd0aCA+IDApIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzUGFuZWwpLmRldGFjaCgpLmFwcGVuZFRvKCRtb2RpZmljYXRpb25Qb3NpdGlvbik7XG4gICAgJG1vZGlmaWNhdGlvblBvc2l0aW9uLmNsb3Nlc3QoJy5yb3cnKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG5cbiAgICAvLyBTaG93IGNvbHVtbiBsb2NhdGlvbiAmIHJlZnVuZGVkXG4gICAgdGhpcy50b2dnbGVDb2x1bW4oT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c0NlbGxMb2NhdGlvbik7XG4gICAgdGhpcy50b2dnbGVDb2x1bW4oT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c0NlbGxSZWZ1bmRlZCk7XG5cbiAgICAvLyBTaG93IGFsbCByb3dzLCBoaWRlIHBhZ2luYXRpb24gY29udHJvbHNcbiAgICBjb25zdCAkcm93cyA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlKS5maW5kKCd0cltpZF49XCJvcmRlclByb2R1Y3RfXCJdJyk7XG4gICAgJHJvd3MucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1BhZ2luYXRpb24pLmFkZENsYXNzKCdkLW5vbmUnKTtcblxuICAgIGNvbnN0IHNjcm9sbFZhbHVlID0gJChzY3JvbGxUYXJnZXQpLm9mZnNldCgpLnRvcCAtICQoJy5oZWFkZXItdG9vbGJhcicpLmhlaWdodCgpIC0gMTAwO1xuICAgICQoJ2h0bWwsYm9keScpLmFuaW1hdGUoe3Njcm9sbFRvcDogc2Nyb2xsVmFsdWV9LCAnc2xvdycpO1xuICB9XG5cbiAgbW92ZVByb2R1Y3RQYW5lbFRvT3JpZ2luYWxQb3NpdGlvbigpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZE5ld0ludm9pY2VJbmZvKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RNb2RpZmljYXRpb25Qb3NpdGlvbikuY2xvc2VzdCgnLnJvdycpLmFkZENsYXNzKCdkLW5vbmUnKTtcblxuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1BhbmVsKS5kZXRhY2goKS5hcHBlbmRUbyhPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RPcmlnaW5hbFBvc2l0aW9uKTtcblxuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1BhZ2luYXRpb24pLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFjdGlvbkJ0bikucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICQoYCR7T3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkQWN0aW9uQnRufSwgJHtPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRSb3d9YCkuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuXG4gICAgLy8gUmVzdG9yZSBwYWdpbmF0aW9uXG4gICAgdGhpcy5wYWdpbmF0ZSgxKTtcbiAgfVxuXG4gIHJlc2V0QWRkUm93KCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkSWRJbnB1dCkudmFsKCcnKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdFNlYXJjaElucHV0KS52YWwoJycpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkQ29tYmluYXRpb25zQmxvY2spLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZENvbWJpbmF0aW9uc1NlbGVjdCkudmFsKCcnKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZENvbWJpbmF0aW9uc1NlbGVjdCkucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRQcmljZVRheEV4Y2xJbnB1dCkudmFsKCcnKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZFByaWNlVGF4SW5jbElucHV0KS52YWwoJycpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkUXVhbnRpdHlJbnB1dCkudmFsKCcnKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZEF2YWlsYWJsZVRleHQpLmh0bWwoJycpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkTG9jYXRpb25UZXh0KS5odG1sKCcnKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZE5ld0ludm9pY2VJbmZvKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRBY3Rpb25CdG4pLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gIH1cblxuICByZXNldEFsbEVkaXRSb3dzKCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdEJ1dHRvbnMpLmVhY2goKGtleSwgZWRpdEJ1dHRvbikgPT4ge1xuICAgICAgdGhpcy5yZXNldEVkaXRSb3coJChlZGl0QnV0dG9uKS5kYXRhKCdvcmRlckRldGFpbElkJykpO1xuICAgIH0pO1xuICB9XG5cbiAgcmVzZXRFZGl0Um93KG9yZGVyUHJvZHVjdElkKSB7XG4gICAgY29uc3QgJHByb2R1Y3RSb3cgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVJvdyhvcmRlclByb2R1Y3RJZCkpO1xuICAgIGNvbnN0ICRwcm9kdWN0RWRpdFJvdyA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUm93RWRpdGVkKG9yZGVyUHJvZHVjdElkKSk7XG4gICAgJHByb2R1Y3RFZGl0Um93LnJlbW92ZSgpO1xuICAgICRwcm9kdWN0Um93LnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIHBhZ2luYXRlKG51bVBhZ2UpIHtcbiAgICBjb25zdCAkcm93cyA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlKS5maW5kKCd0cltpZF49XCJvcmRlclByb2R1Y3RfXCJdJyk7XG4gICAgY29uc3QgJGN1c3RvbWl6YXRpb25Sb3dzID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVDdXN0b21pemF0aW9uUm93cyk7XG4gICAgY29uc3QgJHRhYmxlUGFnaW5hdGlvbiA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUGFnaW5hdGlvbik7XG4gICAgY29uc3QgbnVtUm93c1BlclBhZ2UgPSBwYXJzZUludCgkdGFibGVQYWdpbmF0aW9uLmRhdGEoJ251bVBlclBhZ2UnKSwgMTApO1xuICAgIGNvbnN0IG1heFBhZ2UgPSBNYXRoLmNlaWwoJHJvd3MubGVuZ3RoIC8gbnVtUm93c1BlclBhZ2UpO1xuICAgIG51bVBhZ2UgPSBNYXRoLm1heCgxLCBNYXRoLm1pbihudW1QYWdlLCBtYXhQYWdlKSk7XG4gICAgdGhpcy5wYWdpbmF0ZVVwZGF0ZUNvbnRyb2xzKG51bVBhZ2UpO1xuXG4gICAgLy8gSGlkZSBhbGwgcm93cy4uLlxuICAgICRyb3dzLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAkY3VzdG9taXphdGlvblJvd3MuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIC8vIC4uLiBhbmQgZGlzcGxheSBnb29kIG9uZXNcblxuICAgIGNvbnN0IHN0YXJ0Um93ID0gKChudW1QYWdlIC0gMSkgKiBudW1Sb3dzUGVyUGFnZSkgKyAxO1xuICAgIGNvbnN0IGVuZFJvdyA9IG51bVBhZ2UgKiBudW1Sb3dzUGVyUGFnZTtcbiAgICBmb3IgKGxldCBpID0gc3RhcnRSb3ctMTsgaSA8IE1hdGgubWluKGVuZFJvdywgJHJvd3MubGVuZ3RoKTsgaSsrKSB7XG4gICAgICAkKCRyb3dzW2ldKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgfVxuICAgICRjdXN0b21pemF0aW9uUm93cy5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgIGlmICghJCh0aGlzKS5wcmV2KCkuaGFzQ2xhc3MoJ2Qtbm9uZScpKSB7XG4gICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgLy8gUmVtb3ZlIGFsbCBlZGl0aW9uIHJvd3MgKGNhcmVmdWwgbm90IHRvIHJlbW92ZSB0aGUgdGVtcGxhdGUpXG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0Um93KS5ub3QoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFJvd1RlbXBsYXRlKS5yZW1vdmUoKTtcblxuICAgIC8vIFRvZ2dsZSBDb2x1bW4gTG9jYXRpb24gJiBSZWZ1bmRlZFxuICAgIHRoaXMudG9nZ2xlQ29sdW1uKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNDZWxsTG9jYXRpb25EaXNwbGF5ZWQpO1xuICAgIHRoaXMudG9nZ2xlQ29sdW1uKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNDZWxsUmVmdW5kZWREaXNwbGF5ZWQpO1xuICB9XG5cbiAgcGFnaW5hdGVVcGRhdGVDb250cm9scyhudW1QYWdlKSB7XG4gICAgLy8gV2h5IDMgPyBOZXh0ICYgUHJldiAmIFRlbXBsYXRlXG4gICAgY29uc3QgdG90YWxQYWdlID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uKS5maW5kKCdsaS5wYWdlLWl0ZW0nKS5sZW5ndGggLSAzO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUGFnaW5hdGlvbikuZmluZCgnLmFjdGl2ZScpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb24pLmZpbmQoYGxpOmhhcyg+IFtkYXRhLXBhZ2U9XCIke251bVBhZ2V9XCJdKWApLmFkZENsYXNzKCdhY3RpdmUnKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb25QcmV2KS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcbiAgICBpZiAobnVtUGFnZSA9PT0gMSkge1xuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uUHJldikuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgfVxuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUGFnaW5hdGlvbk5leHQpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuICAgIGlmIChudW1QYWdlID09PSB0b3RhbFBhZ2UpIHtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUGFnaW5hdGlvbk5leHQpLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuICAgIH1cbiAgICB0aGlzLnRvZ2dsZVBhZ2luYXRpb25Db250cm9scygpO1xuICB9XG5cbiAgdXBkYXRlTnVtUGVyUGFnZShudW1QZXJQYWdlKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uKS5kYXRhKCdudW1QZXJQYWdlJywgbnVtUGVyUGFnZSk7XG4gICAgdGhpcy51cGRhdGVQYWdpbmF0aW9uQ29udHJvbHMoKTtcbiAgfVxuXG4gIHRvZ2dsZVBhZ2luYXRpb25Db250cm9scygpIHtcbiAgICAvLyBXaHkgMyA/IE5leHQgJiBQcmV2ICYgVGVtcGxhdGVcbiAgICBjb25zdCB0b3RhbFBhZ2UgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb24pLmZpbmQoJ2xpLnBhZ2UtaXRlbScpLmxlbmd0aCAtIDM7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzTmF2UGFnaW5hdGlvbikudG9nZ2xlQ2xhc3MoJ2Qtbm9uZScsIHRvdGFsUGFnZSA8PSAxKTtcbiAgfVxuXG4gIHRvZ2dsZVByb2R1Y3RBZGROZXdJbnZvaWNlSW5mbygpIHtcbiAgICBpZiAocGFyc2VJbnQoJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRJbnZvaWNlU2VsZWN0KS52YWwoKSwgMTApID09PSAwKSB7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZE5ld0ludm9pY2VJbmZvKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgfSBlbHNlIHtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkTmV3SW52b2ljZUluZm8pLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICB9XG4gIH1cblxuICB0b2dnbGVDb2x1bW4odGFyZ2V0LCBmb3JjZURpc3BsYXkgPSBudWxsKSB7XG4gICAgbGV0IGlzQ29sdW1uRGlzcGxheWVkID0gZmFsc2U7XG4gICAgaWYgKGZvcmNlRGlzcGxheSA9PT0gbnVsbCkge1xuICAgICAgJCh0YXJnZXQpLmZpbHRlcigndGQnKS5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICBpZiAoJCh0aGlzKS5odG1sKCkudHJpbSgpICE9PSAnJykge1xuICAgICAgICAgIGlzQ29sdW1uRGlzcGxheWVkID0gdHJ1ZTtcbiAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuICAgIH0gZWxzZSB7XG4gICAgICBpc0NvbHVtbkRpc3BsYXllZCA9IGZvcmNlRGlzcGxheTtcbiAgICB9XG4gICAgJCh0YXJnZXQpLnRvZ2dsZUNsYXNzKCdkLW5vbmUnLCAhaXNDb2x1bW5EaXNwbGF5ZWQpO1xuICB9XG5cbiAgdXBkYXRlUGFnaW5hdGlvbkNvbnRyb2xzKCkge1xuICAgIGNvbnN0ICR0YWJsZVBhZ2luYXRpb24gPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb24pO1xuICAgIGNvbnN0IG51bVBlclBhZ2UgPSAkdGFibGVQYWdpbmF0aW9uLmRhdGEoJ251bVBlclBhZ2UnKTtcbiAgICBjb25zdCAkcm93cyA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlKS5maW5kKCd0cltpZF49XCJvcmRlclByb2R1Y3RfXCJdJyk7XG4gICAgY29uc3QgbnVtUGFnZXMgPSBNYXRoLmNlaWwoJHJvd3MubGVuZ3RoIC8gbnVtUGVyUGFnZSk7XG5cbiAgICAvLyBVcGRhdGUgdGFibGUgZGF0YSBmaWVsZHNcbiAgICAkdGFibGVQYWdpbmF0aW9uLmRhdGEoJ251bVBhZ2VzJywgbnVtUGFnZXMpO1xuXG4gICAgLy8gQ2xlYW4gYWxsIHBhZ2UgbGlua3MsIHJlaW5zZXJ0IHRoZSByZW1vdmVkIHRlbXBsYXRlXG4gICAgY29uc3QgJGxpbmtQYWdpbmF0aW9uVGVtcGxhdGUgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb25UZW1wbGF0ZSk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uKS5maW5kKGBsaTpoYXMoPiBbZGF0YS1wYWdlXSlgKS5yZW1vdmUoKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb25OZXh0KS5iZWZvcmUoJGxpbmtQYWdpbmF0aW9uVGVtcGxhdGUpO1xuXG4gICAgLy8gQWRkIGFwcHJvcHJpYXRlIHBhZ2VzXG4gICAgZm9yIChsZXQgaSA9IDE7IGkgPD0gbnVtUGFnZXM7ICsraSkge1xuICAgICAgY29uc3QgJGxpbmtQYWdpbmF0aW9uID0gJGxpbmtQYWdpbmF0aW9uVGVtcGxhdGUuY2xvbmUoKTtcbiAgICAgICRsaW5rUGFnaW5hdGlvbi5maW5kKCdzcGFuJykuYXR0cignZGF0YS1wYWdlJywgaSk7XG4gICAgICAkbGlua1BhZ2luYXRpb24uZmluZCgnc3BhbicpLmh0bWwoaSk7XG4gICAgICAkbGlua1BhZ2luYXRpb25UZW1wbGF0ZS5iZWZvcmUoJGxpbmtQYWdpbmF0aW9uLnJlbW92ZUNsYXNzKCdkLW5vbmUnKSk7XG4gICAgfVxuXG4gICAgdGhpcy50b2dnbGVQYWdpbmF0aW9uQ29udHJvbHMoKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcm9kdWN0LXJlbmRlcmVyLmpzIiwibW9kdWxlLmV4cG9ydHMgPSB7IFwiZGVmYXVsdFwiOiByZXF1aXJlKFwiY29yZS1qcy9saWJyYXJ5L2ZuL251bWJlci9pcy1uYW5cIiksIF9fZXNNb2R1bGU6IHRydWUgfTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL251bWJlci9pcy1uYW4uanNcbi8vIG1vZHVsZSBpZCA9IDIzNVxuLy8gbW9kdWxlIGNodW5rcyA9IDMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBUZXh0V2l0aExlbmd0aENvdW50ZXIgaGFuZGxlcyBpbnB1dCB3aXRoIGxlbmd0aCBjb3VudGVyIFVJLlxuICpcbiAqIFVzYWdlOlxuICpcbiAqIFRoZXJlIG11c3QgYmUgYW4gZWxlbWVudCB0aGF0IHdyYXBzIGJvdGggaW5wdXQgJiBjb3VudGVyIGRpc3BsYXkgd2l0aCBcIi5qcy10ZXh0LXdpdGgtbGVuZ3RoLWNvdW50ZXJcIiBjbGFzcy5cbiAqIENvdW50ZXIgZGlzcGxheSBtdXN0IGhhdmUgXCIuanMtY291bnRhYmxlLXRleHQtZGlzcGxheVwiIGNsYXNzIGFuZCBpbnB1dCBtdXN0IGhhdmUgXCIuanMtY291bnRhYmxlLXRleHQtaW5wdXRcIiBjbGFzcy5cbiAqIFRleHQgaW5wdXQgbXVzdCBoYXZlIFwiZGF0YS1tYXgtbGVuZ3RoXCIgYXR0cmlidXRlLlxuICpcbiAqIDxkaXYgY2xhc3M9XCJqcy10ZXh0LXdpdGgtbGVuZ3RoLWNvdW50ZXJcIj5cbiAqICA8c3BhbiBjbGFzcz1cImpzLWNvdW50YWJsZS10ZXh0XCI+PC9zcGFuPlxuICogIDxpbnB1dCBjbGFzcz1cImpzLWNvdW50YWJsZS1pbnB1dFwiIGRhdGEtbWF4LWxlbmd0aD1cIjI1NVwiPlxuICogPC9kaXY+XG4gKlxuICogSW4gSmF2YXNjcmlwdCB5b3UgbXVzdCBlbmFibGUgdGhpcyBjb21wb25lbnQ6XG4gKlxuICogbmV3IFRleHRXaXRoTGVuZ3RoQ291bnRlcigpO1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBUZXh0V2l0aExlbmd0aENvdW50ZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLndyYXBwZXJTZWxlY3RvciA9ICcuanMtdGV4dC13aXRoLWxlbmd0aC1jb3VudGVyJztcbiAgICB0aGlzLnRleHRTZWxlY3RvciA9ICcuanMtY291bnRhYmxlLXRleHQnO1xuICAgIHRoaXMuaW5wdXRTZWxlY3RvciA9ICcuanMtY291bnRhYmxlLWlucHV0JztcblxuICAgICQoZG9jdW1lbnQpLm9uKCdpbnB1dCcsIGAke3RoaXMud3JhcHBlclNlbGVjdG9yfSAke3RoaXMuaW5wdXRTZWxlY3Rvcn1gLCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGlucHV0ID0gJChlLmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgcmVtYWluaW5nTGVuZ3RoID0gJGlucHV0LmRhdGEoJ21heC1sZW5ndGgnKSAtICRpbnB1dC52YWwoKS5sZW5ndGg7XG5cbiAgICAgICRpbnB1dC5jbG9zZXN0KHRoaXMud3JhcHBlclNlbGVjdG9yKS5maW5kKHRoaXMudGV4dFNlbGVjdG9yKS50ZXh0KHJlbWFpbmluZ0xlbmd0aCk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZm9ybS90ZXh0LXdpdGgtbGVuZ3RoLWNvdW50ZXIuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gJy4uL09yZGVyVmlld1BhZ2VNYXAnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQWxsIGFjdGlvbnMgZm9yIG9yZGVyIHZpZXcgcGFnZSBtZXNzYWdlcyBhcmUgcmVnaXN0ZXJlZCBpbiB0aGlzIGNsYXNzLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPcmRlclZpZXdQYWdlTWVzc2FnZXNIYW5kbGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy4kb3JkZXJNZXNzYWdlQ2hhbmdlV2FybmluZyA9ICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlck1lc3NhZ2VDaGFuZ2VXYXJuaW5nKTtcbiAgICB0aGlzLiRtZXNzYWdlc0NvbnRhaW5lciA9ICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlck1lc3NhZ2VzQ29udGFpbmVyKTtcblxuICAgIHJldHVybiB7XG4gICAgICBsaXN0ZW5Gb3JQcmVkZWZpbmVkTWVzc2FnZVNlbGVjdGlvbjogKCkgPT4gdGhpcy5faGFuZGxlUHJlZGVmaW5lZE1lc3NhZ2VTZWxlY3Rpb24oKSxcbiAgICAgIGxpc3RlbkZvckZ1bGxNZXNzYWdlc09wZW46ICgpID0+IHRoaXMuX29uRnVsbE1lc3NhZ2VzT3BlbigpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlcyBwcmVkZWZpbmVkIG9yZGVyIG1lc3NhZ2Ugc2VsZWN0aW9uLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZVByZWRlZmluZWRNZXNzYWdlU2VsZWN0aW9uKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjaGFuZ2UnLCBPcmRlclZpZXdQYWdlTWFwLm9yZGVyTWVzc2FnZU5hbWVTZWxlY3QsIChlKSA9PiB7XG4gICAgICBjb25zdCAkY3VycmVudEl0ZW0gPSAkKGUuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCB2YWx1ZUlkID0gJGN1cnJlbnRJdGVtLnZhbCgpO1xuXG4gICAgICBpZiAoIXZhbHVlSWQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBjb25zdCBtZXNzYWdlID0gdGhpcy4kbWVzc2FnZXNDb250YWluZXIuZmluZChgZGl2W2RhdGEtaWQ9JHt2YWx1ZUlkfV1gKS50ZXh0KCkudHJpbSgpO1xuICAgICAgY29uc3QgJG9yZGVyTWVzc2FnZSA9ICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlck1lc3NhZ2UpO1xuICAgICAgY29uc3QgaXNTYW1lTWVzc2FnZSA9ICRvcmRlck1lc3NhZ2UudmFsKCkudHJpbSgpID09PSBtZXNzYWdlO1xuXG4gICAgICBpZiAoaXNTYW1lTWVzc2FnZSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIGlmICgkb3JkZXJNZXNzYWdlLnZhbCgpICYmICFjb25maXJtKHRoaXMuJG9yZGVyTWVzc2FnZUNoYW5nZVdhcm5pbmcudGV4dCgpKSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgICRvcmRlck1lc3NhZ2UudmFsKG1lc3NhZ2UpO1xuICAgICAgJG9yZGVyTWVzc2FnZS50cmlnZ2VyKCdpbnB1dCcpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIGV2ZW50IHdoZW4gYWxsIG1lc3NhZ2VzIG1vZGFsIGlzIGJlaW5nIG9wZW5lZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uRnVsbE1lc3NhZ2VzT3BlbigpIHtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCBPcmRlclZpZXdQYWdlTWFwLm9wZW5BbGxNZXNzYWdlc0J0biwgKCkgPT4gdGhpcy5fc2Nyb2xsVG9Nc2dMaXN0Qm90dG9tKCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNjcm9sbHMgZG93biB0byB0aGUgYm90dG9tIG9mIGFsbCBtZXNzYWdlcyBsaXN0XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2Nyb2xsVG9Nc2dMaXN0Qm90dG9tKCkge1xuICAgIGNvbnN0ICRtc2dNb2RhbCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5hbGxNZXNzYWdlc01vZGFsKTtcbiAgICBjb25zdCBtc2dMaXN0ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcihPcmRlclZpZXdQYWdlTWFwLmFsbE1lc3NhZ2VzTGlzdCk7XG5cbiAgICBjb25zdCBjbGFzc0NoZWNrSW50ZXJ2YWwgPSB3aW5kb3cuc2V0SW50ZXJ2YWwoKCkgPT4ge1xuICAgICAgaWYgKCRtc2dNb2RhbC5oYXNDbGFzcygnc2hvdycpKSB7XG4gICAgICAgIG1zZ0xpc3Quc2Nyb2xsVG9wID0gbXNnTGlzdC5zY3JvbGxIZWlnaHQ7XG4gICAgICAgIGNsZWFySW50ZXJ2YWwoY2xhc3NDaGVja0ludGVydmFsKTtcbiAgICAgIH1cbiAgICB9LCAxMCk7XG5cblxuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9tZXNzYWdlL29yZGVyLXZpZXctcGFnZS1tZXNzYWdlcy1oYW5kbGVyLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNYXAgZnJvbSAnLi9PcmRlclZpZXdQYWdlTWFwJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPcmRlclNoaXBwaW5nTWFuYWdlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuX2luaXRPcmRlclNoaXBwaW5nVXBkYXRlRXZlbnRIYW5kbGVyKCk7XG4gIH1cblxuICBfaW5pdE9yZGVyU2hpcHBpbmdVcGRhdGVFdmVudEhhbmRsZXIoKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLm1haW5EaXYpLm9uKCdjbGljaycsIE9yZGVyVmlld1BhZ2VNYXAuc2hvd09yZGVyU2hpcHBpbmdVcGRhdGVNb2RhbEJ0biwgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkYnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZU9yZGVyU2hpcHBpbmdUcmFja2luZ051bWJlcklucHV0KS52YWwoJGJ0bi5kYXRhKCdvcmRlci10cmFja2luZy1udW1iZXInKSk7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAudXBkYXRlT3JkZXJTaGlwcGluZ0N1cnJlbnRPcmRlckNhcnJpZXJJZElucHV0KS52YWwoJGJ0bi5kYXRhKCdvcmRlci1jYXJyaWVyLWlkJykpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9vcmRlci1zaGlwcGluZy1tYW5hZ2VyLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuaW1wb3J0IFJvdXRlciBmcm9tICdAY29tcG9uZW50cy9yb3V0ZXInO1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL09yZGVyVmlld1BhZ2VNYXAnO1xuXG5jb25zdCB7JH0gPSB3aW5kb3c7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIE9yZGVyUHJvZHVjdEF1dG9jb21wbGV0ZSB7XG4gIGNvbnN0cnVjdG9yKGlucHV0KSB7XG4gICAgdGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0ID0gbnVsbDtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgICB0aGlzLmlucHV0ID0gaW5wdXQ7XG4gICAgdGhpcy5yZXN1bHRzID0gW107XG4gICAgdGhpcy5kcm9wZG93bk1lbnUgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdFNlYXJjaElucHV0QXV0b2NvbXBsZXRlTWVudSk7XG4gICAgLyoqXG4gICAgICogUGVybWl0IHRvIGxpbmsgdG8gZWFjaCB2YWx1ZSBvZiBkcm9wZG93biBhIGNhbGxiYWNrIGFmdGVyIGl0ZW0gaXMgY2xpY2tlZFxuICAgICAqL1xuICAgIHRoaXMub25JdGVtQ2xpY2tlZENhbGxiYWNrID0gKCkgPT4ge307XG4gIH1cblxuICBsaXN0ZW5Gb3JTZWFyY2goKSB7XG4gICAgdGhpcy5pbnB1dC5vbignY2xpY2snLCBldmVudCA9PiB7XG4gICAgICBldmVudC5zdG9wSW1tZWRpYXRlUHJvcGFnYXRpb24oKTtcbiAgICAgIHRoaXMudXBkYXRlUmVzdWx0cyh0aGlzLnJlc3VsdHMpO1xuICAgIH0pO1xuXG4gICAgdGhpcy5pbnB1dC5vbigna2V5dXAnLCBldmVudCA9PiB0aGlzLmRlbGF5U2VhcmNoKGV2ZW50LmN1cnJlbnRUYXJnZXQpKTtcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICgpID0+IHRoaXMuZHJvcGRvd25NZW51LmhpZGUoKSk7XG4gIH1cblxuICBkZWxheVNlYXJjaChpbnB1dCkge1xuICAgIGNsZWFyVGltZW91dCh0aGlzLnNlYXJjaFRpbWVvdXRJZCk7XG5cbiAgICAvLyBTZWFyY2ggb25seSBpZiB0aGUgc2VhcmNoIHBocmFzZSBsZW5ndGggaXMgZ3JlYXRlciB0aGFuIDIgY2hhcmFjdGVyc1xuICAgIGlmIChpbnB1dC52YWx1ZS5sZW5ndGggPCAyKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdGhpcy5zZWFyY2hUaW1lb3V0SWQgPSBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgIHRoaXMuc2VhcmNoKGlucHV0LnZhbHVlLCAkKGlucHV0KS5kYXRhKCdjdXJyZW5jeScpLCAkKGlucHV0KS5kYXRhKCdvcmRlcicpKTtcbiAgICB9LCAzMDApO1xuICB9XG5cbiAgc2VhcmNoKHNlYXJjaCwgY3VycmVuY3ksIG9yZGVySWQpIHtcbiAgICBjb25zdCBwYXJhbXMgPSB7c2VhcmNoX3BocmFzZTogc2VhcmNofTtcblxuICAgIGlmIChjdXJyZW5jeSkge1xuICAgICAgcGFyYW1zLmN1cnJlbmN5X2lkID0gY3VycmVuY3k7XG4gICAgfVxuXG4gICAgaWYgKG9yZGVySWQpIHtcbiAgICAgIHBhcmFtcy5vcmRlcl9pZCA9IG9yZGVySWQ7XG4gICAgfVxuXG4gICAgaWYgKHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdCAhPT0gbnVsbCkge1xuICAgICAgdGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0LmFib3J0KCk7XG4gICAgfVxuXG4gICAgdGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0ID0gJC5nZXQodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19wcm9kdWN0c19zZWFyY2gnLCBwYXJhbXMpKTtcbiAgICB0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3RcbiAgICAgIC50aGVuKHJlc3BvbnNlID0+IHRoaXMudXBkYXRlUmVzdWx0cyhyZXNwb25zZSkpXG4gICAgICAuYWx3YXlzKCgpID0+IHtcbiAgICAgICAgdGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0ID0gbnVsbDtcbiAgICAgIH0pO1xuICB9XG5cbiAgdXBkYXRlUmVzdWx0cyhyZXN1bHRzKSB7XG4gICAgdGhpcy5kcm9wZG93bk1lbnUuZW1wdHkoKTtcblxuICAgIGlmICghcmVzdWx0cyB8fCAhcmVzdWx0cy5wcm9kdWN0cyB8fCBPYmplY3Qua2V5cyhyZXN1bHRzLnByb2R1Y3RzKS5sZW5ndGggPD0gMCkge1xuICAgICAgdGhpcy5kcm9wZG93bk1lbnUuaGlkZSgpO1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHRoaXMucmVzdWx0cyA9IHJlc3VsdHMucHJvZHVjdHM7XG5cbiAgICBPYmplY3QudmFsdWVzKHRoaXMucmVzdWx0cykuZm9yRWFjaCh2YWwgPT4ge1xuICAgICAgY29uc3QgbGluayA9ICQoYDxhIGNsYXNzPVwiZHJvcGRvd24taXRlbVwiIGRhdGEtaWQ9XCIke3ZhbC5wcm9kdWN0SWR9XCIgaHJlZj1cIiNcIj4ke3ZhbC5uYW1lfTwvYT5gKTtcblxuICAgICAgbGluay5vbignY2xpY2snLCBldmVudCA9PiB7XG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIHRoaXMub25JdGVtQ2xpY2tlZCgkKGV2ZW50LnRhcmdldCkuZGF0YSgnaWQnKSk7XG4gICAgICB9KTtcblxuICAgICAgdGhpcy5kcm9wZG93bk1lbnUuYXBwZW5kKGxpbmspO1xuICAgIH0pO1xuXG4gICAgdGhpcy5kcm9wZG93bk1lbnUuc2hvdygpO1xuICB9XG5cbiAgb25JdGVtQ2xpY2tlZChpZCkge1xuICAgIGNvbnN0IHNlbGVjdGVkUHJvZHVjdCA9IHRoaXMucmVzdWx0cy5maWx0ZXIocHJvZHVjdCA9PiBwcm9kdWN0LnByb2R1Y3RJZCA9PT0gaWQpO1xuXG4gICAgaWYgKHNlbGVjdGVkUHJvZHVjdC5sZW5ndGggIT09IDApIHtcbiAgICAgIHRoaXMuaW5wdXQudmFsKHNlbGVjdGVkUHJvZHVjdFswXS5uYW1lKTtcbiAgICAgIHRoaXMub25JdGVtQ2xpY2tlZENhbGxiYWNrKHNlbGVjdGVkUHJvZHVjdFswXSk7XG4gICAgfVxuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtYWRkLWF1dG9jb21wbGV0ZS5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IFJvdXRlciBmcm9tICdAY29tcG9uZW50cy9yb3V0ZXInO1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL09yZGVyVmlld1BhZ2VNYXAnO1xuaW1wb3J0IHtFdmVudEVtaXR0ZXJ9IGZyb20gJ0Bjb21wb25lbnRzL2V2ZW50LWVtaXR0ZXInO1xuaW1wb3J0IE9yZGVyVmlld0V2ZW50TWFwIGZyb20gJ0BwYWdlcy9vcmRlci92aWV3L29yZGVyLXZpZXctZXZlbnQtbWFwJztcbmltcG9ydCBPcmRlclByaWNlcyBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcmljZXMnO1xuaW1wb3J0IE9yZGVyUHJvZHVjdFJlbmRlcmVyIGZyb20gJ0BwYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtcmVuZGVyZXInO1xuaW1wb3J0IENvbmZpcm1Nb2RhbCBmcm9tICdAY29tcG9uZW50cy9tb2RhbCc7XG5pbXBvcnQgT3JkZXJQcmljZXNSZWZyZXNoZXIgZnJvbSAnQHBhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcHJpY2VzLXJlZnJlc2hlcic7XG5cbmNvbnN0IHskfSA9IHdpbmRvdztcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3JkZXJQcm9kdWN0QWRkIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gICAgdGhpcy5wcm9kdWN0QWRkQWN0aW9uQnRuID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRBY3Rpb25CdG4pO1xuICAgIHRoaXMucHJvZHVjdElkSW5wdXQgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZElkSW5wdXQpO1xuICAgIHRoaXMuY29tYmluYXRpb25zQmxvY2sgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZENvbWJpbmF0aW9uc0Jsb2NrKTtcbiAgICB0aGlzLmNvbWJpbmF0aW9uc1NlbGVjdCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkQ29tYmluYXRpb25zU2VsZWN0KTtcbiAgICB0aGlzLnByaWNlVGF4SW5jbHVkZWRJbnB1dCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkUHJpY2VUYXhJbmNsSW5wdXQpO1xuICAgIHRoaXMucHJpY2VUYXhFeGNsdWRlZElucHV0ID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRQcmljZVRheEV4Y2xJbnB1dCk7XG4gICAgdGhpcy50YXhSYXRlSW5wdXQgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZFRheFJhdGVJbnB1dCk7XG4gICAgdGhpcy5xdWFudGl0eUlucHV0ID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRRdWFudGl0eUlucHV0KTtcbiAgICB0aGlzLmF2YWlsYWJsZVRleHQgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZEF2YWlsYWJsZVRleHQpO1xuICAgIHRoaXMubG9jYXRpb25UZXh0ID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRMb2NhdGlvblRleHQpO1xuICAgIHRoaXMudG90YWxQcmljZVRleHQgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZFRvdGFsUHJpY2VUZXh0KTtcbiAgICB0aGlzLmludm9pY2VTZWxlY3QgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZEludm9pY2VTZWxlY3QpO1xuICAgIHRoaXMuZnJlZVNoaXBwaW5nU2VsZWN0ID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RBZGRGcmVlU2hpcHBpbmdTZWxlY3QpO1xuICAgIHRoaXMucHJvZHVjdEFkZE1lbnVCdG4gPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZEJ0bik7XG4gICAgdGhpcy5hdmFpbGFibGUgPSBudWxsO1xuICAgIHRoaXMuc2V0dXBMaXN0ZW5lcigpO1xuICAgIHRoaXMucHJvZHVjdCA9IHt9O1xuICAgIHRoaXMuY3VycmVuY3lQcmVjaXNpb24gPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZSkuZGF0YSgnY3VycmVuY3lQcmVjaXNpb24nKTtcbiAgICB0aGlzLnByaWNlVGF4Q2FsY3VsYXRvciA9IG5ldyBPcmRlclByaWNlcygpO1xuICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIgPSBuZXcgT3JkZXJQcm9kdWN0UmVuZGVyZXIoKTtcbiAgICB0aGlzLm9yZGVyUHJpY2VzUmVmcmVzaGVyID0gbmV3IE9yZGVyUHJpY2VzUmVmcmVzaGVyKCk7XG4gICAgdGhpcy5pc09yZGVyVGF4SW5jbHVkZWQgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZFJvdykuZGF0YSgnaXNPcmRlclRheEluY2x1ZGVkJyk7XG4gICAgdGhpcy50YXhFeGNsdWRlZCA9IG51bGw7XG4gICAgdGhpcy50YXhJbmNsdWRlZCA9IG51bGw7XG4gIH1cblxuICBzZXR1cExpc3RlbmVyKCkge1xuICAgIHRoaXMuY29tYmluYXRpb25zU2VsZWN0Lm9uKCdjaGFuZ2UnLCBldmVudCA9PiB7XG4gICAgICBjb25zdCB0YXhFeGNsdWRlZCA9IHdpbmRvdy5wc19yb3VuZChcbiAgICAgICAgJChldmVudC5jdXJyZW50VGFyZ2V0KVxuICAgICAgICAgIC5maW5kKCc6c2VsZWN0ZWQnKVxuICAgICAgICAgIC5kYXRhKCdwcmljZVRheEV4Y2x1ZGVkJyksXG4gICAgICAgIHRoaXMuY3VycmVuY3lQcmVjaXNpb25cbiAgICAgICk7XG4gICAgICB0aGlzLnByaWNlVGF4RXhjbHVkZWRJbnB1dC52YWwodGF4RXhjbHVkZWQpO1xuICAgICAgdGhpcy50YXhFeGNsdWRlZCA9IHBhcnNlRmxvYXQodGF4RXhjbHVkZWQpO1xuXG4gICAgICBjb25zdCB0YXhJbmNsdWRlZCA9IHdpbmRvdy5wc19yb3VuZChcbiAgICAgICAgJChldmVudC5jdXJyZW50VGFyZ2V0KVxuICAgICAgICAgIC5maW5kKCc6c2VsZWN0ZWQnKVxuICAgICAgICAgIC5kYXRhKCdwcmljZVRheEluY2x1ZGVkJyksXG4gICAgICAgIHRoaXMuY3VycmVuY3lQcmVjaXNpb25cbiAgICAgICk7XG4gICAgICB0aGlzLnByaWNlVGF4SW5jbHVkZWRJbnB1dC52YWwodGF4SW5jbHVkZWQpO1xuICAgICAgdGhpcy50YXhJbmNsdWRlZCA9IHBhcnNlRmxvYXQodGF4SW5jbHVkZWQpO1xuXG4gICAgICB0aGlzLmxvY2F0aW9uVGV4dC5odG1sKFxuICAgICAgICAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpXG4gICAgICAgICAgLmZpbmQoJzpzZWxlY3RlZCcpXG4gICAgICAgICAgLmRhdGEoJ2xvY2F0aW9uJylcbiAgICAgICk7XG5cbiAgICAgIHRoaXMuYXZhaWxhYmxlID0gJChldmVudC5jdXJyZW50VGFyZ2V0KVxuICAgICAgICAuZmluZCgnOnNlbGVjdGVkJylcbiAgICAgICAgLmRhdGEoJ3N0b2NrJyk7XG5cbiAgICAgIHRoaXMucXVhbnRpdHlJbnB1dC50cmlnZ2VyKCdjaGFuZ2UnKTtcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIudG9nZ2xlQ29sdW1uKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNDZWxsTG9jYXRpb24pO1xuICAgIH0pO1xuXG4gICAgdGhpcy5xdWFudGl0eUlucHV0Lm9uKCdjaGFuZ2Uga2V5dXAnLCBldmVudCA9PiB7XG4gICAgICBpZiAodGhpcy5hdmFpbGFibGUgIT09IG51bGwpIHtcbiAgICAgICAgY29uc3QgbmV3UXVhbnRpdHkgPSBOdW1iZXIoZXZlbnQudGFyZ2V0LnZhbHVlKTtcbiAgICAgICAgY29uc3QgcmVtYWluaW5nQXZhaWxhYmxlID0gdGhpcy5hdmFpbGFibGUgLSBuZXdRdWFudGl0eTtcbiAgICAgICAgY29uc3QgYXZhaWxhYmxlT3V0T2ZTdG9jayA9IHRoaXMuYXZhaWxhYmxlVGV4dC5kYXRhKCdhdmFpbGFibGVPdXRPZlN0b2NrJyk7XG4gICAgICAgIHRoaXMuYXZhaWxhYmxlVGV4dC50ZXh0KHJlbWFpbmluZ0F2YWlsYWJsZSk7XG4gICAgICAgIHRoaXMuYXZhaWxhYmxlVGV4dC50b2dnbGVDbGFzcygndGV4dC1kYW5nZXIgZm9udC13ZWlnaHQtYm9sZCcsIHJlbWFpbmluZ0F2YWlsYWJsZSA8IDApO1xuICAgICAgICBjb25zdCBkaXNhYmxlQWRkQWN0aW9uQnRuID0gbmV3UXVhbnRpdHkgPD0gMCB8fCAocmVtYWluaW5nQXZhaWxhYmxlIDwgMCAmJiAhYXZhaWxhYmxlT3V0T2ZTdG9jayk7XG4gICAgICAgIHRoaXMucHJvZHVjdEFkZEFjdGlvbkJ0bi5wcm9wKCdkaXNhYmxlZCcsIGRpc2FibGVBZGRBY3Rpb25CdG4pO1xuICAgICAgICB0aGlzLmludm9pY2VTZWxlY3QucHJvcCgnZGlzYWJsZWQnLCAhYXZhaWxhYmxlT3V0T2ZTdG9jayAmJiByZW1haW5pbmdBdmFpbGFibGUgPCAwKTtcblxuICAgICAgICB0aGlzLnRheEluY2x1ZGVkID0gcGFyc2VGbG9hdCh0aGlzLnByaWNlVGF4SW5jbHVkZWRJbnB1dC52YWwoKSk7XG4gICAgICAgIHRoaXMudG90YWxQcmljZVRleHQuaHRtbChcbiAgICAgICAgICB0aGlzLnByaWNlVGF4Q2FsY3VsYXRvci5jYWxjdWxhdGVUb3RhbFByaWNlKFxuICAgICAgICAgICAgbmV3UXVhbnRpdHksXG4gICAgICAgICAgICB0aGlzLmlzT3JkZXJUYXhJbmNsdWRlZCA/IHRoaXMudGF4SW5jbHVkZWQgOiB0aGlzLnRheEV4Y2x1ZGVkLFxuICAgICAgICAgICAgdGhpcy5jdXJyZW5jeVByZWNpc2lvblxuICAgICAgICAgIClcbiAgICAgICAgKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgIHRoaXMucHJvZHVjdElkSW5wdXQub24oJ2NoYW5nZScsICgpID0+IHtcbiAgICAgIHRoaXMucHJvZHVjdEFkZEFjdGlvbkJ0bi5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICAgICAgdGhpcy5pbnZvaWNlU2VsZWN0LnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgfSk7XG5cbiAgICB0aGlzLnByaWNlVGF4SW5jbHVkZWRJbnB1dC5vbignY2hhbmdlIGtleXVwJywgZXZlbnQgPT4ge1xuICAgICAgdGhpcy50YXhJbmNsdWRlZCA9IHBhcnNlRmxvYXQoZXZlbnQudGFyZ2V0LnZhbHVlKTtcbiAgICAgIHRoaXMudGF4RXhjbHVkZWQgPSB0aGlzLnByaWNlVGF4Q2FsY3VsYXRvci5jYWxjdWxhdGVUYXhFeGNsdWRlZChcbiAgICAgICAgdGhpcy50YXhJbmNsdWRlZCxcbiAgICAgICAgdGhpcy50YXhSYXRlSW5wdXQudmFsKCksXG4gICAgICAgIHRoaXMuY3VycmVuY3lQcmVjaXNpb25cbiAgICAgICk7XG4gICAgICBjb25zdCBxdWFudGl0eSA9IHBhcnNlSW50KHRoaXMucXVhbnRpdHlJbnB1dC52YWwoKSwgMTApO1xuXG4gICAgICB0aGlzLnByaWNlVGF4RXhjbHVkZWRJbnB1dC52YWwodGhpcy50YXhFeGNsdWRlZCk7XG4gICAgICB0aGlzLnRvdGFsUHJpY2VUZXh0Lmh0bWwoXG4gICAgICAgIHRoaXMucHJpY2VUYXhDYWxjdWxhdG9yLmNhbGN1bGF0ZVRvdGFsUHJpY2UoXG4gICAgICAgICAgcXVhbnRpdHksXG4gICAgICAgICAgdGhpcy5pc09yZGVyVGF4SW5jbHVkZWQgPyB0aGlzLnRheEluY2x1ZGVkIDogdGhpcy50YXhFeGNsdWRlZCxcbiAgICAgICAgICB0aGlzLmN1cnJlbmN5UHJlY2lzaW9uXG4gICAgICAgIClcbiAgICAgICk7XG4gICAgfSk7XG5cbiAgICB0aGlzLnByaWNlVGF4RXhjbHVkZWRJbnB1dC5vbignY2hhbmdlIGtleXVwJywgZXZlbnQgPT4ge1xuICAgICAgdGhpcy50YXhFeGNsdWRlZCA9IHBhcnNlRmxvYXQoZXZlbnQudGFyZ2V0LnZhbHVlKTtcbiAgICAgIHRoaXMudGF4SW5jbHVkZWQgPSB0aGlzLnByaWNlVGF4Q2FsY3VsYXRvci5jYWxjdWxhdGVUYXhJbmNsdWRlZChcbiAgICAgICAgdGhpcy50YXhFeGNsdWRlZCxcbiAgICAgICAgdGhpcy50YXhSYXRlSW5wdXQudmFsKCksXG4gICAgICAgIHRoaXMuY3VycmVuY3lQcmVjaXNpb25cbiAgICAgICk7XG4gICAgICBjb25zdCBxdWFudGl0eSA9IHBhcnNlSW50KHRoaXMucXVhbnRpdHlJbnB1dC52YWwoKSwgMTApO1xuXG4gICAgICB0aGlzLnByaWNlVGF4SW5jbHVkZWRJbnB1dC52YWwodGhpcy50YXhJbmNsdWRlZCk7XG4gICAgICB0aGlzLnRvdGFsUHJpY2VUZXh0Lmh0bWwoXG4gICAgICAgIHRoaXMucHJpY2VUYXhDYWxjdWxhdG9yLmNhbGN1bGF0ZVRvdGFsUHJpY2UoXG4gICAgICAgICAgcXVhbnRpdHksXG4gICAgICAgICAgdGhpcy5pc09yZGVyVGF4SW5jbHVkZWQgPyB0aGlzLnRheEluY2x1ZGVkIDogdGhpcy50YXhFeGNsdWRlZCxcbiAgICAgICAgICB0aGlzLmN1cnJlbmN5UHJlY2lzaW9uXG4gICAgICAgIClcbiAgICAgICk7XG4gICAgfSk7XG5cbiAgICB0aGlzLnByb2R1Y3RBZGRBY3Rpb25CdG4ub24oJ2NsaWNrJywgZXZlbnQgPT4gdGhpcy5jb25maXJtTmV3SW52b2ljZShldmVudCkpO1xuICAgIHRoaXMuaW52b2ljZVNlbGVjdC5vbignY2hhbmdlJywgKCkgPT4gdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlci50b2dnbGVQcm9kdWN0QWRkTmV3SW52b2ljZUluZm8oKSk7XG4gIH1cblxuICBzZXRQcm9kdWN0KHByb2R1Y3QpIHtcbiAgICB0aGlzLnByb2R1Y3RJZElucHV0LnZhbChwcm9kdWN0LnByb2R1Y3RJZCkudHJpZ2dlcignY2hhbmdlJyk7XG5cbiAgICBjb25zdCB0YXhFeGNsdWRlZCA9IHdpbmRvdy5wc19yb3VuZChwcm9kdWN0LnByaWNlVGF4RXhjbCwgdGhpcy5jdXJyZW5jeVByZWNpc2lvbik7XG4gICAgdGhpcy5wcmljZVRheEV4Y2x1ZGVkSW5wdXQudmFsKHRheEV4Y2x1ZGVkKTtcbiAgICB0aGlzLnRheEV4Y2x1ZGVkID0gcGFyc2VGbG9hdCh0YXhFeGNsdWRlZCk7XG5cbiAgICBjb25zdCB0YXhJbmNsdWRlZCA9IHdpbmRvdy5wc19yb3VuZChwcm9kdWN0LnByaWNlVGF4SW5jbCwgdGhpcy5jdXJyZW5jeVByZWNpc2lvbik7XG4gICAgdGhpcy5wcmljZVRheEluY2x1ZGVkSW5wdXQudmFsKHRheEluY2x1ZGVkKTtcbiAgICB0aGlzLnRheEluY2x1ZGVkID0gcGFyc2VGbG9hdCh0YXhJbmNsdWRlZCk7XG5cbiAgICB0aGlzLnRheFJhdGVJbnB1dC52YWwocHJvZHVjdC50YXhSYXRlKTtcbiAgICB0aGlzLmxvY2F0aW9uVGV4dC5odG1sKHByb2R1Y3QubG9jYXRpb24pO1xuICAgIHRoaXMuYXZhaWxhYmxlID0gcHJvZHVjdC5zdG9jaztcbiAgICB0aGlzLmF2YWlsYWJsZVRleHQuZGF0YSgnYXZhaWxhYmxlT3V0T2ZTdG9jaycsIHByb2R1Y3QuYXZhaWxhYmxlT3V0T2ZTdG9jayk7XG4gICAgdGhpcy5xdWFudGl0eUlucHV0LnZhbCgxKTtcbiAgICB0aGlzLnF1YW50aXR5SW5wdXQudHJpZ2dlcignY2hhbmdlJyk7XG4gICAgdGhpcy5zZXRDb21iaW5hdGlvbnMocHJvZHVjdC5jb21iaW5hdGlvbnMpO1xuICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIudG9nZ2xlQ29sdW1uKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNDZWxsTG9jYXRpb24pO1xuICB9XG5cbiAgc2V0Q29tYmluYXRpb25zKGNvbWJpbmF0aW9ucykge1xuICAgIHRoaXMuY29tYmluYXRpb25zU2VsZWN0LmVtcHR5KCk7XG5cbiAgICBPYmplY3QudmFsdWVzKGNvbWJpbmF0aW9ucykuZm9yRWFjaCh2YWwgPT4ge1xuICAgICAgdGhpcy5jb21iaW5hdGlvbnNTZWxlY3QuYXBwZW5kKFxuICAgICAgICBgPG9wdGlvbiB2YWx1ZT1cIiR7dmFsLmF0dHJpYnV0ZUNvbWJpbmF0aW9uSWR9XCIgZGF0YS1wcmljZS10YXgtZXhjbHVkZWQ9XCIke3ZhbC5wcmljZVRheEV4Y2x1ZGVkfVwiIGRhdGEtcHJpY2UtdGF4LWluY2x1ZGVkPVwiJHt2YWwucHJpY2VUYXhJbmNsdWRlZH1cIiBkYXRhLXN0b2NrPVwiJHt2YWwuc3RvY2t9XCIgZGF0YS1sb2NhdGlvbj1cIiR7dmFsLmxvY2F0aW9ufVwiPiR7dmFsLmF0dHJpYnV0ZX08L29wdGlvbj5gXG4gICAgICApO1xuICAgIH0pO1xuXG4gICAgdGhpcy5jb21iaW5hdGlvbnNCbG9jay50b2dnbGVDbGFzcygnZC1ub25lJywgT2JqZWN0LmtleXMoY29tYmluYXRpb25zKS5sZW5ndGggPT09IDApO1xuXG4gICAgaWYgKE9iamVjdC5rZXlzKGNvbWJpbmF0aW9ucykubGVuZ3RoID4gMCkge1xuICAgICAgdGhpcy5jb21iaW5hdGlvbnNTZWxlY3QudHJpZ2dlcignY2hhbmdlJyk7XG4gICAgfVxuICB9XG5cbiAgYWRkUHJvZHVjdChvcmRlcklkKSB7XG4gICAgdGhpcy5wcm9kdWN0QWRkQWN0aW9uQnRuLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gICAgdGhpcy5pbnZvaWNlU2VsZWN0LnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gICAgdGhpcy5jb21iaW5hdGlvbnNTZWxlY3QucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcblxuICAgIGNvbnN0IHBhcmFtcyA9IHtcbiAgICAgIHByb2R1Y3RfaWQ6IHRoaXMucHJvZHVjdElkSW5wdXQudmFsKCksXG4gICAgICBjb21iaW5hdGlvbl9pZDogJCgnOnNlbGVjdGVkJywgdGhpcy5jb21iaW5hdGlvbnNTZWxlY3QpLnZhbCgpLFxuICAgICAgcHJpY2VfdGF4X2luY2w6IHRoaXMucHJpY2VUYXhJbmNsdWRlZElucHV0LnZhbCgpLFxuICAgICAgcHJpY2VfdGF4X2V4Y2w6IHRoaXMucHJpY2VUYXhFeGNsdWRlZElucHV0LnZhbCgpLFxuICAgICAgcXVhbnRpdHk6IHRoaXMucXVhbnRpdHlJbnB1dC52YWwoKSxcbiAgICAgIGludm9pY2VfaWQ6IHRoaXMuaW52b2ljZVNlbGVjdC52YWwoKSxcbiAgICAgIGZyZWVfc2hpcHBpbmc6IHRoaXMuZnJlZVNoaXBwaW5nU2VsZWN0LnByb3AoJ2NoZWNrZWQnKVxuICAgIH07XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdXJsOiB0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fb3JkZXJzX2FkZF9wcm9kdWN0Jywge29yZGVySWR9KSxcbiAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgICAgZGF0YTogcGFyYW1zXG4gICAgfSkudGhlbihcbiAgICAgIHJlc3BvbnNlID0+IHtcbiAgICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoT3JkZXJWaWV3RXZlbnRNYXAucHJvZHVjdEFkZGVkVG9PcmRlciwge1xuICAgICAgICAgIG9yZGVySWQsXG4gICAgICAgIH0pO1xuICAgICAgfSxcbiAgICAgIHJlc3BvbnNlID0+IHtcbiAgICAgICAgdGhpcy5wcm9kdWN0QWRkQWN0aW9uQnRuLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICAgICAgICB0aGlzLmludm9pY2VTZWxlY3QucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gICAgICAgIHRoaXMuY29tYmluYXRpb25zU2VsZWN0LnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuXG4gICAgICAgIGlmIChyZXNwb25zZS5yZXNwb25zZUpTT04gJiYgcmVzcG9uc2UucmVzcG9uc2VKU09OLm1lc3NhZ2UpIHtcbiAgICAgICAgICAkLmdyb3dsLmVycm9yKHttZXNzYWdlOiByZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZX0pO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgKTtcbiAgfVxuXG4gIGNvbmZpcm1OZXdJbnZvaWNlKGV2ZW50KSB7XG4gICAgY29uc3QgaW52b2ljZUlkID0gcGFyc2VJbnQodGhpcy5pbnZvaWNlU2VsZWN0LnZhbCgpLCAxMCk7XG4gICAgY29uc3Qgb3JkZXJJZCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnb3JkZXJJZCcpO1xuXG4gICAgLy8gRXhwbGljaXQgMCB2YWx1ZSBpcyB1c2VkIHdoZW4gd2UgdGhlIHVzZXIgc2VsZWN0ZWQgTmV3IEludm9pY2VcbiAgICBpZiAoaW52b2ljZUlkID09PSAwKSB7XG4gICAgICBjb25zdCBtb2RhbCA9IG5ldyBDb25maXJtTW9kYWwoXG4gICAgICAgIHtcbiAgICAgICAgICBpZDogJ21vZGFsLWNvbmZpcm0tbmV3LWludm9pY2UnLFxuICAgICAgICAgIGNvbmZpcm1UaXRsZTogdGhpcy5pbnZvaWNlU2VsZWN0LmRhdGEoJ21vZGFsLXRpdGxlJyksXG4gICAgICAgICAgY29uZmlybU1lc3NhZ2U6IHRoaXMuaW52b2ljZVNlbGVjdC5kYXRhKCdtb2RhbC1ib2R5JyksXG4gICAgICAgICAgY29uZmlybUJ1dHRvbkxhYmVsOiB0aGlzLmludm9pY2VTZWxlY3QuZGF0YSgnbW9kYWwtYXBwbHknKSxcbiAgICAgICAgICBjbG9zZUJ1dHRvbkxhYmVsOiB0aGlzLmludm9pY2VTZWxlY3QuZGF0YSgnbW9kYWwtY2FuY2VsJylcbiAgICAgICAgfSxcbiAgICAgICAgKCkgPT4ge1xuICAgICAgICAgIHRoaXMuY29uZmlybU5ld1ByaWNlKG9yZGVySWQsIGludm9pY2VJZCk7XG4gICAgICAgIH1cbiAgICAgICk7XG4gICAgICBtb2RhbC5zaG93KCk7XG4gICAgfSBlbHNlIHtcbiAgICAgIC8vIExhc3QgY2FzZSBpcyBOYW4sIHRoZSBzZWxlY3RvciBpcyBub3QgZXZlbiBwcmVzZW50LCB3ZSBzaW1wbHkgYWRkIHByb2R1Y3QgYW5kIGxldCB0aGUgQk8gaGFuZGxlIGl0XG4gICAgICB0aGlzLmFkZFByb2R1Y3Qob3JkZXJJZCk7XG4gICAgfVxuICB9XG5cbiAgY29uZmlybU5ld1ByaWNlKG9yZGVySWQsIGludm9pY2VJZCkge1xuICAgIGNvbnN0IGNvbWJpbmF0aW9uSWQgPVxuICAgICAgdHlwZW9mICQoJzpzZWxlY3RlZCcsIHRoaXMuY29tYmluYXRpb25zU2VsZWN0KS52YWwoKSA9PT0gJ3VuZGVmaW5lZCdcbiAgICAgICAgPyAwXG4gICAgICAgIDogJCgnOnNlbGVjdGVkJywgdGhpcy5jb21iaW5hdGlvbnNTZWxlY3QpLnZhbCgpO1xuICAgIGNvbnN0IHByb2R1Y3RQcmljZU1hdGNoID0gdGhpcy5vcmRlclByaWNlc1JlZnJlc2hlci5jaGVja090aGVyUHJvZHVjdFByaWNlc01hdGNoKFxuICAgICAgdGhpcy5wcmljZVRheEluY2x1ZGVkSW5wdXQudmFsKCksXG4gICAgICB0aGlzLnByb2R1Y3RJZElucHV0LnZhbCgpLFxuICAgICAgY29tYmluYXRpb25JZCxcbiAgICAgIGludm9pY2VJZFxuICAgICk7XG5cbiAgICBpZiAoIXByb2R1Y3RQcmljZU1hdGNoKSB7XG4gICAgICBjb25zdCBtb2RhbEVkaXRQcmljZSA9IG5ldyBDb25maXJtTW9kYWwoXG4gICAgICAgIHtcbiAgICAgICAgICBpZDogJ21vZGFsLWNvbmZpcm0tbmV3LXByaWNlJyxcbiAgICAgICAgICBjb25maXJtVGl0bGU6IHRoaXMuaW52b2ljZVNlbGVjdC5kYXRhKCdtb2RhbC1lZGl0LXByaWNlLXRpdGxlJyksXG4gICAgICAgICAgY29uZmlybU1lc3NhZ2U6IHRoaXMuaW52b2ljZVNlbGVjdC5kYXRhKCdtb2RhbC1lZGl0LXByaWNlLWJvZHknKSxcbiAgICAgICAgICBjb25maXJtQnV0dG9uTGFiZWw6IHRoaXMuaW52b2ljZVNlbGVjdC5kYXRhKCdtb2RhbC1lZGl0LXByaWNlLWFwcGx5JyksXG4gICAgICAgICAgY2xvc2VCdXR0b25MYWJlbDogdGhpcy5pbnZvaWNlU2VsZWN0LmRhdGEoJ21vZGFsLWVkaXQtcHJpY2UtY2FuY2VsJylcbiAgICAgICAgfSxcbiAgICAgICAgKCkgPT4ge1xuICAgICAgICAgIHRoaXMuYWRkUHJvZHVjdChvcmRlcklkKTtcbiAgICAgICAgfVxuICAgICAgKTtcbiAgICAgIG1vZGFsRWRpdFByaWNlLnNob3coKTtcbiAgICB9IGVsc2Uge1xuICAgICAgdGhpcy5hZGRQcm9kdWN0KG9yZGVySWQpO1xuICAgIH1cbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcm9kdWN0LWFkZC5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IE9yZGVyUHJvZHVjdE1hbmFnZXIgZnJvbSAnQHBhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcHJvZHVjdC1tYW5hZ2VyJztcbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gJ0BwYWdlcy9vcmRlci9PcmRlclZpZXdQYWdlTWFwJztcbmltcG9ydCBPcmRlclZpZXdFdmVudE1hcCBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci12aWV3LWV2ZW50LW1hcCc7XG5pbXBvcnQge0V2ZW50RW1pdHRlcn0gZnJvbSAnQGNvbXBvbmVudHMvZXZlbnQtZW1pdHRlcic7XG5pbXBvcnQgT3JkZXJEaXNjb3VudHNSZWZyZXNoZXIgZnJvbSAnQHBhZ2VzL29yZGVyL3ZpZXcvb3JkZXItZGlzY291bnRzLXJlZnJlc2hlcic7XG5pbXBvcnQgT3JkZXJQcm9kdWN0UmVuZGVyZXIgZnJvbSAnQHBhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcHJvZHVjdC1yZW5kZXJlcic7XG5pbXBvcnQgT3JkZXJQcmljZXNSZWZyZXNoZXIgZnJvbSAnQHBhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcHJpY2VzLXJlZnJlc2hlcic7XG5pbXBvcnQgT3JkZXJQYXltZW50c1JlZnJlc2hlciBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wYXltZW50cy1yZWZyZXNoZXInO1xuaW1wb3J0IE9yZGVyU2hpcHBpbmdSZWZyZXNoZXIgZnJvbSAnQHBhZ2VzL29yZGVyL3ZpZXcvb3JkZXItc2hpcHBpbmctcmVmcmVzaGVyJztcbmltcG9ydCBSb3V0ZXIgZnJvbSAnQGNvbXBvbmVudHMvcm91dGVyJztcbmltcG9ydCBPcmRlckludm9pY2VzUmVmcmVzaGVyIGZyb20gJy4vb3JkZXItaW52b2ljZXMtcmVmcmVzaGVyJztcbmltcG9ydCBPcmRlclByb2R1Y3RDYW5jZWwgZnJvbSAnLi9vcmRlci1wcm9kdWN0LWNhbmNlbCc7XG5pbXBvcnQgT3JkZXJEb2N1bWVudHNSZWZyZXNoZXIgZnJvbSBcIi4vb3JkZXItZG9jdW1lbnRzLXJlZnJlc2hlclwiO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIE9yZGVyVmlld1BhZ2Uge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLm9yZGVyRGlzY291bnRzUmVmcmVzaGVyID0gbmV3IE9yZGVyRGlzY291bnRzUmVmcmVzaGVyKCk7XG4gICAgdGhpcy5vcmRlclByb2R1Y3RNYW5hZ2VyID0gbmV3IE9yZGVyUHJvZHVjdE1hbmFnZXIoKTtcbiAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyID0gbmV3IE9yZGVyUHJvZHVjdFJlbmRlcmVyKCk7XG4gICAgdGhpcy5vcmRlclByaWNlc1JlZnJlc2hlciA9IG5ldyBPcmRlclByaWNlc1JlZnJlc2hlcigpO1xuICAgIHRoaXMub3JkZXJQYXltZW50c1JlZnJlc2hlciA9IG5ldyBPcmRlclBheW1lbnRzUmVmcmVzaGVyKCk7XG4gICAgdGhpcy5vcmRlclNoaXBwaW5nUmVmcmVzaGVyID0gbmV3IE9yZGVyU2hpcHBpbmdSZWZyZXNoZXIoKTtcbiAgICB0aGlzLm9yZGVyRG9jdW1lbnRzUmVmcmVzaGVyID0gbmV3IE9yZGVyRG9jdW1lbnRzUmVmcmVzaGVyKCk7XG4gICAgdGhpcy5vcmRlckludm9pY2VzUmVmcmVzaGVyID0gbmV3IE9yZGVySW52b2ljZXNSZWZyZXNoZXIoKTtcbiAgICB0aGlzLm9yZGVyUHJvZHVjdENhbmNlbCA9IG5ldyBPcmRlclByb2R1Y3RDYW5jZWwoKTtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgICB0aGlzLmxpc3RlblRvRXZlbnRzKCk7XG4gIH1cblxuICBsaXN0ZW5Ub0V2ZW50cygpIHtcblxuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5pbnZvaWNlQWRkcmVzc0VkaXRCdG4pLmZhbmN5Ym94KHtcbiAgICAgICd0eXBlJzogJ2lmcmFtZScsXG4gICAgICAnd2lkdGgnOiAnOTAlJyxcbiAgICAgICdoZWlnaHQnOiAnOTAlJyxcbiAgICB9KTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAuZGVsaXZlcnlBZGRyZXNzRWRpdEJ0bikuZmFuY3lib3goe1xuICAgICAgJ3R5cGUnOiAnaWZyYW1lJyxcbiAgICAgICd3aWR0aCc6ICc5MCUnLFxuICAgICAgJ2hlaWdodCc6ICc5MCUnLFxuICAgIH0pO1xuXG4gICAgRXZlbnRFbWl0dGVyLm9uKE9yZGVyVmlld0V2ZW50TWFwLnByb2R1Y3REZWxldGVkRnJvbU9yZGVyLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMub3JkZXJQcmljZXNSZWZyZXNoZXIucmVmcmVzaChldmVudC5vcmRlcklkKTtcbiAgICAgIHRoaXMub3JkZXJQYXltZW50c1JlZnJlc2hlci5yZWZyZXNoKGV2ZW50Lm9yZGVySWQpO1xuICAgICAgdGhpcy5yZWZyZXNoUHJvZHVjdHNMaXN0KGV2ZW50Lm9yZGVySWQpO1xuICAgICAgdGhpcy5vcmRlckRpc2NvdW50c1JlZnJlc2hlci5yZWZyZXNoKGV2ZW50Lm9yZGVySWQpO1xuICAgICAgdGhpcy5vcmRlckRvY3VtZW50c1JlZnJlc2hlci5yZWZyZXNoKGV2ZW50Lm9yZGVySWQpO1xuICAgICAgdGhpcy5vcmRlclNoaXBwaW5nUmVmcmVzaGVyLnJlZnJlc2goZXZlbnQub3JkZXJJZCk7XG4gICAgfSk7XG5cbiAgICBFdmVudEVtaXR0ZXIub24oT3JkZXJWaWV3RXZlbnRNYXAucHJvZHVjdEVkaXRpb25DYW5jZWxlZCwgKGV2ZW50KSA9PiB7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLnJlc2V0RWRpdFJvdyhldmVudC5vcmRlckRldGFpbElkKTtcbiAgICAgIGNvbnN0IGVkaXRSb3dzTGVmdCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFJvdykubm90KE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRSb3dUZW1wbGF0ZSkubGVuZ3RoO1xuICAgICAgaWYgKGVkaXRSb3dzTGVmdCA+IDApIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuICAgICAgdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlci5tb3ZlUHJvZHVjdFBhbmVsVG9PcmlnaW5hbFBvc2l0aW9uKCk7XG4gICAgfSk7XG5cbiAgICBFdmVudEVtaXR0ZXIub24oT3JkZXJWaWV3RXZlbnRNYXAucHJvZHVjdFVwZGF0ZWQsIChldmVudCkgPT4ge1xuICAgICAgdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlci5yZXNldEVkaXRSb3coZXZlbnQub3JkZXJEZXRhaWxJZCk7XG4gICAgICB0aGlzLm9yZGVyUHJpY2VzUmVmcmVzaGVyLnJlZnJlc2goZXZlbnQub3JkZXJJZCk7XG4gICAgICB0aGlzLm9yZGVyUHJpY2VzUmVmcmVzaGVyLnJlZnJlc2hQcm9kdWN0UHJpY2VzKGV2ZW50Lm9yZGVySWQpO1xuICAgICAgdGhpcy5yZWZyZXNoUHJvZHVjdHNMaXN0KGV2ZW50Lm9yZGVySWQpO1xuICAgICAgdGhpcy5vcmRlclBheW1lbnRzUmVmcmVzaGVyLnJlZnJlc2goZXZlbnQub3JkZXJJZCk7XG4gICAgICB0aGlzLm9yZGVyRGlzY291bnRzUmVmcmVzaGVyLnJlZnJlc2goZXZlbnQub3JkZXJJZCk7XG4gICAgICB0aGlzLm9yZGVySW52b2ljZXNSZWZyZXNoZXIucmVmcmVzaChldmVudC5vcmRlcklkKTtcbiAgICAgIHRoaXMub3JkZXJEb2N1bWVudHNSZWZyZXNoZXIucmVmcmVzaChldmVudC5vcmRlcklkKTtcbiAgICAgIHRoaXMub3JkZXJTaGlwcGluZ1JlZnJlc2hlci5yZWZyZXNoKGV2ZW50Lm9yZGVySWQpO1xuICAgICAgdGhpcy5saXN0ZW5Gb3JQcm9kdWN0RGVsZXRlKCk7XG4gICAgICB0aGlzLmxpc3RlbkZvclByb2R1Y3RFZGl0KCk7XG4gICAgICB0aGlzLnJlc2V0VG9vbFRpcHMoKTtcblxuICAgICAgY29uc3QgZWRpdFJvd3NMZWZ0ID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0Um93KS5ub3QoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFJvd1RlbXBsYXRlKS5sZW5ndGg7XG4gICAgICBpZiAoZWRpdFJvd3NMZWZ0ID4gMCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLm1vdmVQcm9kdWN0UGFuZWxUb09yaWdpbmFsUG9zaXRpb24oKTtcbiAgICB9KTtcblxuICAgIEV2ZW50RW1pdHRlci5vbihPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0QWRkZWRUb09yZGVyLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIucmVzZXRBZGRSb3coKTtcbiAgICAgIHRoaXMub3JkZXJQcmljZXNSZWZyZXNoZXIucmVmcmVzaFByb2R1Y3RQcmljZXMoZXZlbnQub3JkZXJJZCk7XG4gICAgICB0aGlzLm9yZGVyUHJpY2VzUmVmcmVzaGVyLnJlZnJlc2goZXZlbnQub3JkZXJJZCk7XG4gICAgICB0aGlzLnJlZnJlc2hQcm9kdWN0c0xpc3QoZXZlbnQub3JkZXJJZCk7XG4gICAgICB0aGlzLm9yZGVyUGF5bWVudHNSZWZyZXNoZXIucmVmcmVzaChldmVudC5vcmRlcklkKTtcbiAgICAgIHRoaXMub3JkZXJEaXNjb3VudHNSZWZyZXNoZXIucmVmcmVzaChldmVudC5vcmRlcklkKTtcbiAgICAgIHRoaXMub3JkZXJJbnZvaWNlc1JlZnJlc2hlci5yZWZyZXNoKGV2ZW50Lm9yZGVySWQpO1xuICAgICAgdGhpcy5vcmRlckRvY3VtZW50c1JlZnJlc2hlci5yZWZyZXNoKGV2ZW50Lm9yZGVySWQpO1xuICAgICAgdGhpcy5vcmRlclNoaXBwaW5nUmVmcmVzaGVyLnJlZnJlc2goZXZlbnQub3JkZXJJZCk7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLm1vdmVQcm9kdWN0UGFuZWxUb09yaWdpbmFsUG9zaXRpb24oKTtcbiAgICB9KTtcbiAgfVxuXG4gIGxpc3RlbkZvclByb2R1Y3REZWxldGUoKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3REZWxldGVCdG4pXG4gICAgICAub2ZmKCdjbGljaycpXG4gICAgICAub24oJ2NsaWNrJywgZXZlbnQgPT4ge1xuICAgICAgICB0aGlzLm9yZGVyUHJvZHVjdE1hbmFnZXIuaGFuZGxlRGVsZXRlUHJvZHVjdEV2ZW50KGV2ZW50KTtcbiAgICAgIH1cbiAgICApO1xuICB9XG5cbiAgcmVzZXRUb29sVGlwcygpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRCdXR0b25zKS5wc3Rvb2x0aXAoKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdERlbGV0ZUJ0bikucHN0b29sdGlwKCk7XG4gIH1cblxuICBsaXN0ZW5Gb3JQcm9kdWN0RWRpdCgpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRCdXR0b25zKS5vZmYoJ2NsaWNrJykub24oJ2NsaWNrJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkYnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIubW92ZVByb2R1Y3RzUGFuZWxUb01vZGlmaWNhdGlvblBvc2l0aW9uKCk7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLmVkaXRQcm9kdWN0RnJvbUxpc3QoXG4gICAgICAgICRidG4uZGF0YSgnb3JkZXJEZXRhaWxJZCcpLFxuICAgICAgICAkYnRuLmRhdGEoJ3Byb2R1Y3RRdWFudGl0eScpLFxuICAgICAgICAkYnRuLmRhdGEoJ3Byb2R1Y3RQcmljZVRheEluY2wnKSxcbiAgICAgICAgJGJ0bi5kYXRhKCdwcm9kdWN0UHJpY2VUYXhFeGNsJyksXG4gICAgICAgICRidG4uZGF0YSgndGF4UmF0ZScpLFxuICAgICAgICAkYnRuLmRhdGEoJ2xvY2F0aW9uJyksXG4gICAgICAgICRidG4uZGF0YSgnYXZhaWxhYmxlUXVhbnRpdHknKSxcbiAgICAgICAgJGJ0bi5kYXRhKCdhdmFpbGFibGVPdXRPZlN0b2NrJyksXG4gICAgICAgICRidG4uZGF0YSgnb3JkZXJJbnZvaWNlSWQnKSxcbiAgICAgICAgJGJ0bi5kYXRhKCdpc09yZGVyVGF4SW5jbHVkZWQnKVxuICAgICAgKTtcbiAgICB9KTtcbiAgfVxuXG4gIGxpc3RlbkZvclByb2R1Y3RQYWNrKCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0UGFja01vZGFsLm1vZGFsKS5vbignc2hvdy5icy5tb2RhbCcsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgYnV0dG9uID0gJChldmVudC5yZWxhdGVkVGFyZ2V0KTtcbiAgICAgIGNvbnN0IHBhY2tJdGVtcyA9IGJ1dHRvbi5kYXRhKCdwYWNrSXRlbXMnKTtcbiAgICAgIGNvbnN0IG1vZGFsID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RQYWNrTW9kYWwubW9kYWwpO1xuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RQYWNrTW9kYWwucm93cykucmVtb3ZlKCk7XG4gICAgICBwYWNrSXRlbXMuZm9yRWFjaChpdGVtID0+IHtcbiAgICAgICAgY29uc3QgJGl0ZW0gPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdFBhY2tNb2RhbC50ZW1wbGF0ZSkuY2xvbmUoKTtcbiAgICAgICAgJGl0ZW0uYXR0cignaWQnLCBgcHJvZHVjdHBhY2tfJHtpdGVtLmlkfWApLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAgICAgJGl0ZW0uZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RQYWNrTW9kYWwucHJvZHVjdC5pbWcpLmF0dHIoJ3NyYycsIGl0ZW0uaW1hZ2VQYXRoKTtcbiAgICAgICAgJGl0ZW0uZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RQYWNrTW9kYWwucHJvZHVjdC5uYW1lKS5odG1sKGl0ZW0ubmFtZSk7XG4gICAgICAgICRpdGVtLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0UGFja01vZGFsLnByb2R1Y3QubGluaykuYXR0cignaHJlZicsIHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9wcm9kdWN0X2Zvcm0nLCB7J2lkJzogaXRlbS5pZH0pKTtcbiAgICAgICAgaWYgKGl0ZW0ucmVmZXJlbmNlICE9PSAnJykge1xuICAgICAgICAgICRpdGVtLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0UGFja01vZGFsLnByb2R1Y3QucmVmKS5hcHBlbmQoaXRlbS5yZWZlcmVuY2UpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICRpdGVtLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0UGFja01vZGFsLnByb2R1Y3QucmVmKS5yZW1vdmUoKTtcbiAgICAgICAgfVxuICAgICAgICBpZiAoaXRlbS5zdXBwbGllclJlZmVyZW5jZSAhPT0gJycpIHtcbiAgICAgICAgICAkaXRlbS5maW5kKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdFBhY2tNb2RhbC5wcm9kdWN0LnN1cHBsaWVyUmVmKS5hcHBlbmQoaXRlbS5zdXBwbGllclJlZmVyZW5jZSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgJGl0ZW0uZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RQYWNrTW9kYWwucHJvZHVjdC5zdXBwbGllclJlZikucmVtb3ZlKCk7XG4gICAgICAgIH1cbiAgICAgICAgaWYgKGl0ZW0ucXVhbnRpdHkgPiAxKSB7XG4gICAgICAgICAgJGl0ZW0uZmluZChgJHtPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RQYWNrTW9kYWwucHJvZHVjdC5xdWFudGl0eX0gc3BhbmApLmh0bWwoaXRlbS5xdWFudGl0eSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgJGl0ZW0uZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RQYWNrTW9kYWwucHJvZHVjdC5xdWFudGl0eSkuaHRtbChpdGVtLnF1YW50aXR5KTtcbiAgICAgICAgfVxuICAgICAgICAkaXRlbS5maW5kKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdFBhY2tNb2RhbC5wcm9kdWN0LmF2YWlsYWJsZVF1YW50aXR5KS5odG1sKGl0ZW0uYXZhaWxhYmxlUXVhbnRpdHkpO1xuICAgICAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdFBhY2tNb2RhbC50ZW1wbGF0ZSkuYmVmb3JlKCRpdGVtKTtcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9XG5cbiAgbGlzdGVuRm9yUHJvZHVjdEFkZCgpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEFkZEJ0bikub24oXG4gICAgICAnY2xpY2snLFxuICAgICAgZXZlbnQgPT4ge1xuICAgICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLnRvZ2dsZVByb2R1Y3RBZGROZXdJbnZvaWNlSW5mbygpO1xuICAgICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLm1vdmVQcm9kdWN0c1BhbmVsVG9Nb2RpZmljYXRpb25Qb3NpdGlvbihPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RTZWFyY2hJbnB1dCk7XG4gICAgICB9XG4gICAgKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdENhbmNlbEFkZEJ0bikub24oXG4gICAgICAnY2xpY2snLCBldmVudCA9PiB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLm1vdmVQcm9kdWN0UGFuZWxUb09yaWdpbmFsUG9zaXRpb24oKVxuICAgICk7XG4gIH1cblxuICBsaXN0ZW5Gb3JQcm9kdWN0UGFnaW5hdGlvbigpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb24pLm9uKCdjbGljaycsIE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb25MaW5rLCAoZXZlbnQpID0+IHtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBjb25zdCAkYnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KE9yZGVyVmlld0V2ZW50TWFwLnByb2R1Y3RMaXN0UGFnaW5hdGVkLCB7XG4gICAgICAgIG51bVBhZ2U6ICRidG4uZGF0YSgncGFnZScpXG4gICAgICB9KTtcbiAgICB9KTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb25OZXh0KS5vbignY2xpY2snLCAoZXZlbnQpID0+IHtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBjb25zdCAkYnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGlmICgkYnRuLmhhc0NsYXNzKCdkaXNhYmxlZCcpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGNvbnN0IGFjdGl2ZVBhZ2UgPSB0aGlzLmdldEFjdGl2ZVBhZ2UoKTtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KE9yZGVyVmlld0V2ZW50TWFwLnByb2R1Y3RMaXN0UGFnaW5hdGVkLCB7XG4gICAgICAgIG51bVBhZ2U6IHBhcnNlSW50KCQoYWN0aXZlUGFnZSkuaHRtbCgpLCAxMCkgKyAxXG4gICAgICB9KTtcbiAgICB9KTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb25QcmV2KS5vbignY2xpY2snLCAoZXZlbnQpID0+IHtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBjb25zdCAkYnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGlmICgkYnRuLmhhc0NsYXNzKCdkaXNhYmxlZCcpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cbiAgICAgIGNvbnN0IGFjdGl2ZVBhZ2UgPSB0aGlzLmdldEFjdGl2ZVBhZ2UoKTtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KE9yZGVyVmlld0V2ZW50TWFwLnByb2R1Y3RMaXN0UGFnaW5hdGVkLCB7XG4gICAgICAgIG51bVBhZ2U6IHBhcnNlSW50KCQoYWN0aXZlUGFnZSkuaHRtbCgpLCAxMCkgLSAxXG4gICAgICB9KTtcbiAgICB9KTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb25OdW1iZXJTZWxlY3Rvcikub24oJ2NoYW5nZScsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIGNvbnN0ICRzZWxlY3QgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgbnVtUGVyUGFnZSA9IHBhcnNlSW50KCRzZWxlY3QudmFsKCksIDEwKTtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KE9yZGVyVmlld0V2ZW50TWFwLnByb2R1Y3RMaXN0TnVtYmVyUGVyUGFnZSwge1xuICAgICAgICBudW1QZXJQYWdlOiBudW1QZXJQYWdlXG4gICAgICB9KTtcbiAgICB9KTtcblxuICAgIEV2ZW50RW1pdHRlci5vbihPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0TGlzdFBhZ2luYXRlZCwgKGV2ZW50KSA9PiB7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLnBhZ2luYXRlKGV2ZW50Lm51bVBhZ2UpO1xuICAgICAgdGhpcy5saXN0ZW5Gb3JQcm9kdWN0RGVsZXRlKCk7XG4gICAgICB0aGlzLmxpc3RlbkZvclByb2R1Y3RFZGl0KCk7XG4gICAgICB0aGlzLnJlc2V0VG9vbFRpcHMoKTtcbiAgICB9KTtcblxuICAgIEV2ZW50RW1pdHRlci5vbihPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0TGlzdE51bWJlclBlclBhZ2UsIChldmVudCkgPT4ge1xuICAgICAgLy8gVXBkYXRlIHBhZ2luYXRpb24gbnVtIHBlciBwYWdlIChwYWdlIGxpbmtzIGFyZSByZWdlbmVyYXRlZClcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIudXBkYXRlTnVtUGVyUGFnZShldmVudC5udW1QZXJQYWdlKTtcblxuICAgICAgLy8gUGFnaW5hdGUgdG8gcGFnZSAxXG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0TGlzdFBhZ2luYXRlZCwge1xuICAgICAgICBudW1QYWdlOiAxXG4gICAgICB9KTtcblxuICAgICAgLy8gU2F2ZSBuZXcgY29uZmlnXG4gICAgICAkLmFqYXgoe1xuICAgICAgICB1cmw6IHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9vcmRlcnNfY29uZmlndXJlX3Byb2R1Y3RfcGFnaW5hdGlvbicpLFxuICAgICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICAgICAgZGF0YToge251bVBlclBhZ2U6IGV2ZW50Lm51bVBlclBhZ2V9LFxuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cblxuICBsaXN0ZW5Gb3JSZWZ1bmQoKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuYnV0dG9ucy5wYXJ0aWFsUmVmdW5kKS5vbignY2xpY2snLCAoKSA9PiB7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLm1vdmVQcm9kdWN0c1BhbmVsVG9SZWZ1bmRQb3NpdGlvbigpO1xuICAgICAgdGhpcy5vcmRlclByb2R1Y3RDYW5jZWwuc2hvd1BhcnRpYWxSZWZ1bmQoKTtcbiAgICB9KTtcblxuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LmJ1dHRvbnMuc3RhbmRhcmRSZWZ1bmQpLm9uKCdjbGljaycsICgpID0+IHtcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0UmVuZGVyZXIubW92ZVByb2R1Y3RzUGFuZWxUb1JlZnVuZFBvc2l0aW9uKCk7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdENhbmNlbC5zaG93U3RhbmRhcmRSZWZ1bmQoKTtcbiAgICB9KTtcblxuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LmJ1dHRvbnMucmV0dXJuUHJvZHVjdCkub24oJ2NsaWNrJywgKCkgPT4ge1xuICAgICAgdGhpcy5vcmRlclByb2R1Y3RSZW5kZXJlci5tb3ZlUHJvZHVjdHNQYW5lbFRvUmVmdW5kUG9zaXRpb24oKTtcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0Q2FuY2VsLnNob3dSZXR1cm5Qcm9kdWN0KCk7XG4gICAgfSk7XG5cbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5idXR0b25zLmFib3J0KS5vbignY2xpY2snLCAoKSA9PiB7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLm1vdmVQcm9kdWN0UGFuZWxUb09yaWdpbmFsUG9zaXRpb24oKTtcbiAgICAgIHRoaXMub3JkZXJQcm9kdWN0Q2FuY2VsLmhpZGVSZWZ1bmQoKTtcbiAgICB9KTtcbiAgfVxuXG4gIGxpc3RlbkZvckNhbmNlbFByb2R1Y3QoKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuYnV0dG9ucy5jYW5jZWxQcm9kdWN0cykub24oJ2NsaWNrJywgKGV2ZW50KSA9PiB7XG4gICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLm1vdmVQcm9kdWN0c1BhbmVsVG9SZWZ1bmRQb3NpdGlvbigpO1xuICAgICAgdGhpcy5vcmRlclByb2R1Y3RDYW5jZWwuc2hvd0NhbmNlbFByb2R1Y3RGb3JtKCk7XG4gICAgfSk7XG4gIH1cblxuICBnZXRBY3RpdmVQYWdlKCkge1xuICAgIHJldHVybiAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb24pLmZpbmQoJy5hY3RpdmUgc3BhbicpLmdldCgwKTtcbiAgfVxuXG4gIHJlZnJlc2hQcm9kdWN0c0xpc3Qob3JkZXJJZCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5yZWZyZXNoUHJvZHVjdHNMaXN0TG9hZGluZ1NwaW5uZXIpLnNob3coKTtcblxuICAgIGNvbnN0ICR0YWJsZVBhZ2luYXRpb24gPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZVBhZ2luYXRpb24pO1xuICAgIGNvbnN0IG51bVJvd3NQZXJQYWdlID0gJHRhYmxlUGFnaW5hdGlvbi5kYXRhKCdudW1QZXJQYWdlJyk7XG4gICAgY29uc3QgaW5pdGlhbE51bVByb2R1Y3RzID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVSb3dzKS5sZW5ndGg7XG4gICAgbGV0IGN1cnJlbnRQYWdlID0gcGFyc2VJbnQoJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVQYWdpbmF0aW9uQWN0aXZlKS5odG1sKCksIDEwKTtcblxuICAgICQuYWpheCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fb3JkZXJzX2dldF9wcm9kdWN0cycsIHtvcmRlcklkfSkpXG4gICAgICAgIC5kb25lKChyZXNwb25zZSkgPT4ge1xuICAgICAgICAgIC8vIERlbGV0ZSBwcmV2aW91cyBwcm9kdWN0IGxpbmVzXG4gICAgICAgICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGUpLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0c1RhYmxlUm93cykucmVtb3ZlKCk7XG4gICAgICAgICAgJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVDdXN0b21pemF0aW9uUm93cykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdHNUYWJsZSArICcgdGJvZHknKS5wcmVwZW5kKHJlc3BvbnNlKTtcblxuICAgICAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5yZWZyZXNoUHJvZHVjdHNMaXN0TG9hZGluZ1NwaW5uZXIpLmhpZGUoKTtcblxuICAgICAgICAgIGNvbnN0IG5ld051bVByb2R1Y3RzID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGVSb3dzKS5sZW5ndGg7XG4gICAgICAgICAgY29uc3QgbmV3UGFnZXNOdW0gPSBNYXRoLmNlaWwobmV3TnVtUHJvZHVjdHMgLyBudW1Sb3dzUGVyUGFnZSk7XG5cbiAgICAgICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLnVwZGF0ZU51bVByb2R1Y3RzKG5ld051bVByb2R1Y3RzKTtcbiAgICAgICAgICB0aGlzLm9yZGVyUHJvZHVjdFJlbmRlcmVyLnVwZGF0ZVBhZ2luYXRpb25Db250cm9scygpO1xuXG4gICAgICAgICAgbGV0IG51bVBhZ2UgPSAxO1xuICAgICAgICAgIGxldCBtZXNzYWdlID0gJyc7XG4gICAgICAgICAgLy8gRGlzcGxheSBhbGVydFxuICAgICAgICAgIGlmIChpbml0aWFsTnVtUHJvZHVjdHMgPiBuZXdOdW1Qcm9kdWN0cykgeyAvLyBwcm9kdWN0IGRlbGV0ZWRcbiAgICAgICAgICAgIG1lc3NhZ2UgPSAoaW5pdGlhbE51bVByb2R1Y3RzLW5ld051bVByb2R1Y3RzID09PSAxKSA/XG4gICAgICAgICAgICAgICAgd2luZG93LnRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snVGhlIHByb2R1Y3Qgd2FzIHN1Y2Nlc3NmdWxseSByZW1vdmVkLiddIDpcbiAgICAgICAgICAgICAgICB3aW5kb3cudHJhbnNsYXRlX2phdmFzY3JpcHRzWydbMV0gcHJvZHVjdHMgd2VyZSBzdWNjZXNzZnVsbHkgcmVtb3ZlZC4nXVxuICAgICAgICAgICAgICAgICAgICAucmVwbGFjZSgnWzFdJywgKGluaXRpYWxOdW1Qcm9kdWN0cy1uZXdOdW1Qcm9kdWN0cykpXG4gICAgICAgICAgICA7XG5cbiAgICAgICAgICAgIC8vIFNldCB0YXJnZXQgcGFnZSB0byB0aGUgcGFnZSBvZiB0aGUgZGVsZXRlZCBpdGVtXG4gICAgICAgICAgICBudW1QYWdlID0gKG5ld1BhZ2VzTnVtID09PSAxKSA/IDEgOiBjdXJyZW50UGFnZTtcbiAgICAgICAgICB9XG4gICAgICAgICAgZWxzZSBpZiAoaW5pdGlhbE51bVByb2R1Y3RzIDwgbmV3TnVtUHJvZHVjdHMpIHsgLy8gcHJvZHVjdCBhZGRlZFxuICAgICAgICAgICAgbWVzc2FnZSA9IChuZXdOdW1Qcm9kdWN0cyAtIGluaXRpYWxOdW1Qcm9kdWN0cyA9PT0gMSkgP1xuICAgICAgICAgICAgICAgIHdpbmRvdy50cmFuc2xhdGVfamF2YXNjcmlwdHNbJ1RoZSBwcm9kdWN0IHdhcyBzdWNjZXNzZnVsbHkgYWRkZWQuJ10gOlxuICAgICAgICAgICAgICAgIHdpbmRvdy50cmFuc2xhdGVfamF2YXNjcmlwdHNbJ1sxXSBwcm9kdWN0cyB3ZXJlIHN1Y2Nlc3NmdWxseSBhZGRlZC4nXVxuICAgICAgICAgICAgICAgICAgICAucmVwbGFjZSgnWzFdJywgKG5ld051bVByb2R1Y3RzLWluaXRpYWxOdW1Qcm9kdWN0cykpXG4gICAgICAgICAgICA7XG5cbiAgICAgICAgICAgIC8vIE1vdmUgdG8gZmlyc3QgcGFnZSB0byBzZWUgdGhlIGFkZGVkIHByb2R1Y3RcbiAgICAgICAgICAgIG51bVBhZ2UgPSAxO1xuICAgICAgICAgIH1cblxuICAgICAgICAgIGlmICgnJyAhPT0gbWVzc2FnZSkge1xuICAgICAgICAgICAgJC5ncm93bC5ub3RpY2Uoe1xuICAgICAgICAgICAgICB0aXRsZTogJycsXG4gICAgICAgICAgICAgIG1lc3NhZ2U6IG1lc3NhZ2UsXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICAvLyBNb3ZlIHRvIHBhZ2Ugb2YgdGhlIG1vZGlmaWVkIGl0ZW1cbiAgICAgICAgICBFdmVudEVtaXR0ZXIuZW1pdChPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0TGlzdFBhZ2luYXRlZCwge1xuICAgICAgICAgICAgbnVtUGFnZTogbnVtUGFnZVxuICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgLy8gQmluZCBob3ZlciBvbiBwcm9kdWN0IHJvd3MgYnV0dG9uc1xuICAgICAgICAgIHRoaXMucmVzZXRUb29sVGlwcygpO1xuICAgICAgICB9KVxuICAgICAgICAuZmFpbChlcnJvcnMgPT4ge1xuICAgICAgICAgICQuZ3Jvd2wuZXJyb3Ioe1xuICAgICAgICAgICAgdGl0bGU6ICcnLFxuICAgICAgICAgICAgbWVzc2FnZTogJ0ZhaWxlZCB0byByZWxvYWQgdGhlIHByb2R1Y3RzIGxpc3QuIFBsZWFzZSByZWxvYWQgdGhlIHBhZ2UnLFxuICAgICAgICAgIH0pO1xuICAgICAgICB9KVxuICAgIDtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci12aWV3LXBhZ2UuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICcuL09yZGVyVmlld1BhZ2VNYXAnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogTWFuYWdlcyBhZGRpbmcvZWRpdGluZyBub3RlIGZvciBpbnZvaWNlIGRvY3VtZW50cy5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgSW52b2ljZU5vdGVNYW5hZ2VyIHtcblxuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnNldHVwTGlzdGVuZXJzKCk7XG4gIH1cblxuICBzZXR1cExpc3RlbmVycygpIHtcbiAgICB0aGlzLl9pbml0U2hvd05vdGVGb3JtRXZlbnRIYW5kbGVyKCk7XG4gICAgdGhpcy5faW5pdENsb3NlTm90ZUZvcm1FdmVudEhhbmRsZXIoKTtcbiAgICB0aGlzLl9pbml0RW50ZXJQYXltZW50RXZlbnRIYW5kbGVyKCk7XG4gIH1cblxuICBfaW5pdFNob3dOb3RlRm9ybUV2ZW50SGFuZGxlcigpIHtcbiAgICAkKCcuanMtb3Blbi1pbnZvaWNlLW5vdGUtYnRuJykub24oJ2NsaWNrJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCAkbm90ZVJvdyA9ICRidG4uY2xvc2VzdCgndHInKS5uZXh0KCk7XG5cbiAgICAgICRub3RlUm93LnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICB9KTtcbiAgfVxuXG4gIF9pbml0Q2xvc2VOb3RlRm9ybUV2ZW50SGFuZGxlcigpIHtcbiAgICAkKCcuanMtY2FuY2VsLWludm9pY2Utbm90ZS1idG4nKS5vbignY2xpY2snLCAoZXZlbnQpID0+IHtcbiAgICAgICQoZXZlbnQuY3VycmVudFRhcmdldCkuY2xvc2VzdCgndHInKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgfSk7XG4gIH1cblxuICBfaW5pdEVudGVyUGF5bWVudEV2ZW50SGFuZGxlcigpIHtcbiAgICAkKCcuanMtZW50ZXItcGF5bWVudC1idG4nKS5vbignY2xpY2snLCAoZXZlbnQpID0+IHtcblxuICAgICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBsZXQgcGF5bWVudEFtb3VudCA9ICRidG4uZGF0YSgncGF5bWVudC1hbW91bnQnKTtcblxuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLnZpZXdPcmRlclBheW1lbnRzQmxvY2spLmdldCgwKS5zY3JvbGxJbnRvVmlldyh7YmVoYXZpb3I6IFwic21vb3RoXCJ9KTtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlclBheW1lbnRGb3JtQW1vdW50SW5wdXQpLnZhbChwYXltZW50QW1vdW50KTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvaW52b2ljZS1ub3RlLW1hbmFnZXIuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gJ0BwYWdlcy9vcmRlci9PcmRlclZpZXdQYWdlTWFwJztcbmltcG9ydCBPcmRlclNoaXBwaW5nTWFuYWdlciBmcm9tICdAcGFnZXMvb3JkZXIvb3JkZXItc2hpcHBpbmctbWFuYWdlcic7XG5pbXBvcnQgT3JkZXJWaWV3UGFnZSBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci12aWV3LXBhZ2UnO1xuaW1wb3J0IE9yZGVyUHJvZHVjdEF1dG9jb21wbGV0ZSBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcm9kdWN0LWFkZC1hdXRvY29tcGxldGUnO1xuaW1wb3J0IE9yZGVyUHJvZHVjdEFkZCBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcm9kdWN0LWFkZCc7XG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1lc3NhZ2VzSGFuZGxlciBmcm9tICcuL21lc3NhZ2Uvb3JkZXItdmlldy1wYWdlLW1lc3NhZ2VzLWhhbmRsZXInO1xuaW1wb3J0IFRleHRXaXRoTGVuZ3RoQ291bnRlciBmcm9tICdAY29tcG9uZW50cy9mb3JtL3RleHQtd2l0aC1sZW5ndGgtY291bnRlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gIGNvbnN0IERJU0NPVU5UX1RZUEVfQU1PVU5UID0gJ2Ftb3VudCc7XG4gIGNvbnN0IERJU0NPVU5UX1RZUEVfUEVSQ0VOVCA9ICdwZXJjZW50JztcbiAgY29uc3QgRElTQ09VTlRfVFlQRV9GUkVFX1NISVBQSU5HID0gJ2ZyZWVfc2hpcHBpbmcnO1xuXG4gIG5ldyBPcmRlclNoaXBwaW5nTWFuYWdlcigpO1xuICBuZXcgVGV4dFdpdGhMZW5ndGhDb3VudGVyKCk7XG4gIGNvbnN0IG9yZGVyVmlld1BhZ2UgPSBuZXcgT3JkZXJWaWV3UGFnZSgpO1xuICBjb25zdCBvcmRlckFkZEF1dG9jb21wbGV0ZSA9IG5ldyBPcmRlclByb2R1Y3RBdXRvY29tcGxldGUoJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RTZWFyY2hJbnB1dCkpO1xuICBjb25zdCBvcmRlckFkZCA9IG5ldyBPcmRlclByb2R1Y3RBZGQoKTtcblxuICBvcmRlclZpZXdQYWdlLmxpc3RlbkZvclByb2R1Y3RQYWNrKCk7XG4gIG9yZGVyVmlld1BhZ2UubGlzdGVuRm9yUHJvZHVjdERlbGV0ZSgpO1xuICBvcmRlclZpZXdQYWdlLmxpc3RlbkZvclByb2R1Y3RFZGl0KCk7XG4gIG9yZGVyVmlld1BhZ2UubGlzdGVuRm9yUHJvZHVjdEFkZCgpO1xuICBvcmRlclZpZXdQYWdlLmxpc3RlbkZvclByb2R1Y3RQYWdpbmF0aW9uKCk7XG4gIG9yZGVyVmlld1BhZ2UubGlzdGVuRm9yUmVmdW5kKCk7XG4gIG9yZGVyVmlld1BhZ2UubGlzdGVuRm9yQ2FuY2VsUHJvZHVjdCgpO1xuXG4gIG9yZGVyQWRkQXV0b2NvbXBsZXRlLmxpc3RlbkZvclNlYXJjaCgpO1xuICBvcmRlckFkZEF1dG9jb21wbGV0ZS5vbkl0ZW1DbGlja2VkQ2FsbGJhY2sgPSBwcm9kdWN0ID0+IG9yZGVyQWRkLnNldFByb2R1Y3QocHJvZHVjdCk7XG5cbiAgaGFuZGxlUGF5bWVudERldGFpbHNUb2dnbGUoKTtcbiAgaGFuZGxlUHJpdmF0ZU5vdGVDaGFuZ2UoKTtcbiAgaGFuZGxlVXBkYXRlT3JkZXJTdGF0dXNCdXR0b24oKTtcblxuICBjb25zdCBvcmRlclZpZXdQYWdlTWVzc2FnZUhhbmRsZXIgPSBuZXcgT3JkZXJWaWV3UGFnZU1lc3NhZ2VzSGFuZGxlcigpO1xuICBvcmRlclZpZXdQYWdlTWVzc2FnZUhhbmRsZXIubGlzdGVuRm9yUHJlZGVmaW5lZE1lc3NhZ2VTZWxlY3Rpb24oKTtcbiAgb3JkZXJWaWV3UGFnZU1lc3NhZ2VIYW5kbGVyLmxpc3RlbkZvckZ1bGxNZXNzYWdlc09wZW4oKTtcbiAgJChPcmRlclZpZXdQYWdlTWFwLnByaXZhdGVOb3RlVG9nZ2xlQnRuKS5vbignY2xpY2snLCBldmVudCA9PiB7XG4gICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICB0b2dnbGVQcml2YXRlTm90ZUJsb2NrKCk7XG4gIH0pO1xuXG4gICQoT3JkZXJWaWV3UGFnZU1hcC5wcmludE9yZGVyVmlld1BhZ2VCdXR0b24pLm9uKCdjbGljaycsICgpID0+IHtcbiAgICBjb25zdCB0ZW1wVGl0bGUgPSBkb2N1bWVudC50aXRsZTtcbiAgICBkb2N1bWVudC50aXRsZSA9ICQoT3JkZXJWaWV3UGFnZU1hcC5tYWluRGl2KS5kYXRhKCdvcmRlclRpdGxlJyk7XG4gICAgd2luZG93LnByaW50KCk7XG4gICAgZG9jdW1lbnQudGl0bGUgPSB0ZW1wVGl0bGU7XG4gIH0pO1xuXG4gIGluaXRBZGRDYXJ0UnVsZUZvcm1IYW5kbGVyKCk7XG4gIGluaXRDaGFuZ2VBZGRyZXNzRm9ybUhhbmRsZXIoKTtcbiAgaW5pdEhvb2tUYWJzKCk7XG5cbiAgZnVuY3Rpb24gaW5pdEhvb2tUYWJzKCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlckhvb2tUYWJzQ29udGFpbmVyKVxuICAgICAgLmZpbmQoJy5uYXYtdGFicyBsaTpmaXJzdC1jaGlsZCBhJylcbiAgICAgIC50YWIoJ3Nob3cnKTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGhhbmRsZVBheW1lbnREZXRhaWxzVG9nZ2xlKCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlclBheW1lbnREZXRhaWxzQnRuKS5vbignY2xpY2snLCBldmVudCA9PiB7XG4gICAgICBjb25zdCAkcGF5bWVudERldGFpbFJvdyA9ICQoZXZlbnQuY3VycmVudFRhcmdldClcbiAgICAgICAgLmNsb3Nlc3QoJ3RyJylcbiAgICAgICAgLm5leHQoJzpmaXJzdCcpO1xuXG4gICAgICAkcGF5bWVudERldGFpbFJvdy50b2dnbGVDbGFzcygnZC1ub25lJyk7XG4gICAgfSk7XG4gIH1cblxuICBmdW5jdGlvbiB0b2dnbGVQcml2YXRlTm90ZUJsb2NrKCkge1xuICAgIGNvbnN0ICRibG9jayA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcml2YXRlTm90ZUJsb2NrKTtcbiAgICBjb25zdCAkYnRuID0gJChPcmRlclZpZXdQYWdlTWFwLnByaXZhdGVOb3RlVG9nZ2xlQnRuKTtcbiAgICBjb25zdCBpc1ByaXZhdGVOb3RlT3BlbmVkID0gJGJ0bi5oYXNDbGFzcygnaXMtb3BlbmVkJyk7XG5cbiAgICBpZiAoaXNQcml2YXRlTm90ZU9wZW5lZCkge1xuICAgICAgJGJ0bi5yZW1vdmVDbGFzcygnaXMtb3BlbmVkJyk7XG4gICAgICAkYmxvY2suYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkYnRuLmFkZENsYXNzKCdpcy1vcGVuZWQnKTtcbiAgICAgICRibG9jay5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgfVxuXG4gICAgY29uc3QgJGljb24gPSAkYnRuLmZpbmQoJy5tYXRlcmlhbC1pY29ucycpO1xuICAgICRpY29uLnRleHQoaXNQcml2YXRlTm90ZU9wZW5lZCA/ICdhZGQnIDogJ3JlbW92ZScpO1xuICB9XG5cbiAgZnVuY3Rpb24gaGFuZGxlUHJpdmF0ZU5vdGVDaGFuZ2UoKSB7XG4gICAgY29uc3QgJHN1Ym1pdEJ0biA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcml2YXRlTm90ZVN1Ym1pdEJ0bik7XG5cbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJpdmF0ZU5vdGVJbnB1dCkub24oJ2lucHV0JywgKCkgPT4ge1xuICAgICAgJHN1Ym1pdEJ0bi5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICB9KTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGluaXRBZGRDYXJ0UnVsZUZvcm1IYW5kbGVyKCkge1xuICAgIGNvbnN0ICRtb2RhbCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5hZGRDYXJ0UnVsZU1vZGFsKTtcbiAgICBjb25zdCAkZm9ybSA9ICRtb2RhbC5maW5kKCdmb3JtJyk7XG4gICAgY29uc3QgJHZhbHVlSGVscCA9ICRtb2RhbC5maW5kKE9yZGVyVmlld1BhZ2VNYXAuY2FydFJ1bGVIZWxwVGV4dCk7XG4gICAgY29uc3QgJHZhbHVlSW5wdXQgPSAkZm9ybS5maW5kKE9yZGVyVmlld1BhZ2VNYXAuYWRkQ2FydFJ1bGVWYWx1ZUlucHV0KTtcbiAgICBjb25zdCAkdmFsdWVGb3JtR3JvdXAgPSAkdmFsdWVJbnB1dC5jbG9zZXN0KCcuZm9ybS1ncm91cCcpO1xuXG4gICAgJG1vZGFsLm9uKCdzaG93bi5icy5tb2RhbCcsICgpID0+IHtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5hZGRDYXJ0UnVsZVN1Ym1pdCkuYXR0cignZGlzYWJsZWQnLCB0cnVlKTtcbiAgICB9KTtcblxuICAgICRmb3JtLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5hZGRDYXJ0UnVsZU5hbWVJbnB1dCkub24oJ2tleXVwJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCBjYXJ0UnVsZU5hbWUgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLnZhbCgpO1xuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLmFkZENhcnRSdWxlU3VibWl0KS5hdHRyKCdkaXNhYmxlZCcsIGNhcnRSdWxlTmFtZS50cmltKCkubGVuZ3RoID09PSAwKTtcbiAgICB9KTtcblxuICAgICRmb3JtLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5hZGRDYXJ0UnVsZVR5cGVTZWxlY3QpLm9uKCdjaGFuZ2UnLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0IHNlbGVjdGVkQ2FydFJ1bGVUeXBlID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS52YWwoKTtcbiAgICAgIGNvbnN0ICR2YWx1ZVVuaXQgPSAkZm9ybS5maW5kKE9yZGVyVmlld1BhZ2VNYXAuYWRkQ2FydFJ1bGVWYWx1ZVVuaXQpO1xuXG4gICAgICBpZiAoc2VsZWN0ZWRDYXJ0UnVsZVR5cGUgPT09IERJU0NPVU5UX1RZUEVfQU1PVU5UKSB7XG4gICAgICAgICR2YWx1ZUhlbHAucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgICAkdmFsdWVVbml0Lmh0bWwoJHZhbHVlVW5pdC5kYXRhKCdjdXJyZW5jeVN5bWJvbCcpKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICR2YWx1ZUhlbHAuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgfVxuXG4gICAgICBpZiAoc2VsZWN0ZWRDYXJ0UnVsZVR5cGUgPT09IERJU0NPVU5UX1RZUEVfUEVSQ0VOVCkge1xuICAgICAgICAkdmFsdWVVbml0Lmh0bWwoJyUnKTtcbiAgICAgIH1cblxuICAgICAgaWYgKHNlbGVjdGVkQ2FydFJ1bGVUeXBlID09PSBESVNDT1VOVF9UWVBFX0ZSRUVfU0hJUFBJTkcpIHtcbiAgICAgICAgJHZhbHVlRm9ybUdyb3VwLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgICAgJHZhbHVlSW5wdXQuYXR0cignZGlzYWJsZWQnLCB0cnVlKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICR2YWx1ZUZvcm1Hcm91cC5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICAgICR2YWx1ZUlucHV0LmF0dHIoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgZnVuY3Rpb24gaGFuZGxlVXBkYXRlT3JkZXJTdGF0dXNCdXR0b24oKSB7XG4gICAgY29uc3QgJGJ0biA9ICQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVPcmRlclN0YXR1c0FjdGlvbkJ0bik7XG4gICAgY29uc3QgJHdyYXBwZXIgPSAkKE9yZGVyVmlld1BhZ2VNYXAudXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25JbnB1dFdyYXBwZXIpO1xuXG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uSW5wdXQpLm9uKCdjaGFuZ2UnLCBldmVudCA9PiB7XG4gICAgICBjb25zdCAkZWxlbWVudCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCAkb3B0aW9uID0gJCgnb3B0aW9uOnNlbGVjdGVkJywgJGVsZW1lbnQpO1xuICAgICAgY29uc3Qgc2VsZWN0ZWRPcmRlclN0YXR1c0lkID0gJGVsZW1lbnQudmFsKCk7XG5cbiAgICAgICR3cmFwcGVyLmNzcygnYmFja2dyb3VuZC1jb2xvcicsICRvcHRpb24uZGF0YSgnYmFja2dyb3VuZC1jb2xvcicpKTtcbiAgICAgICR3cmFwcGVyLnRvZ2dsZUNsYXNzKCdpcy1icmlnaHQnLCAkb3B0aW9uLmRhdGEoJ2lzLWJyaWdodCcpICE9PSB1bmRlZmluZWQpO1xuXG4gICAgICAkYnRuLnByb3AoJ2Rpc2FibGVkJywgcGFyc2VJbnQoc2VsZWN0ZWRPcmRlclN0YXR1c0lkLCAxMCkgPT09ICRidG4uZGF0YSgnb3JkZXJTdGF0dXNJZCcpKTtcbiAgICB9KTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGluaXRDaGFuZ2VBZGRyZXNzRm9ybUhhbmRsZXIoKSB7XG4gICAgY29uc3QgJG1vZGFsID0gJChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZUN1c3RvbWVyQWRkcmVzc01vZGFsKTtcblxuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcGVuT3JkZXJBZGRyZXNzVXBkYXRlTW9kYWxCdG4pLm9uKCdjbGljaycsIGV2ZW50ID0+IHtcbiAgICAgICRtb2RhbC5maW5kKE9yZGVyVmlld1BhZ2VNYXAudXBkYXRlT3JkZXJBZGRyZXNzVHlwZUlucHV0KS52YWwoJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdhZGRyZXNzVHlwZScpKTtcbiAgICB9KTtcbiAgfVxufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci92aWV3LmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICdAcGFnZXMvb3JkZXIvT3JkZXJWaWV3UGFnZU1hcCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3JkZXJEaXNjb3VudHNSZWZyZXNoZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgfVxuXG4gIHJlZnJlc2gob3JkZXJJZCkge1xuICAgICQuYWpheCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fb3JkZXJzX2dldF9kaXNjb3VudHMnLCB7b3JkZXJJZH0pKVxuICAgICAgLnRoZW4oKHJlc3BvbnNlKSA9PiB7XG4gICAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RGlzY291bnRMaXN0Lmxpc3QpLnJlcGxhY2VXaXRoKHJlc3BvbnNlKTtcbiAgICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLWRpc2NvdW50cy1yZWZyZXNoZXIuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCBSb3V0ZXIgZnJvbSAnQGNvbXBvbmVudHMvcm91dGVyJztcbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gJ0BwYWdlcy9vcmRlci9PcmRlclZpZXdQYWdlTWFwJztcbmltcG9ydCBJbnZvaWNlTm90ZU1hbmFnZXIgZnJvbSAnLi4vaW52b2ljZS1ub3RlLW1hbmFnZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIE9yZGVyRG9jdW1lbnRzUmVmcmVzaGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gICAgdGhpcy5pbnZvaWNlTm90ZU1hbmFnZXIgPSBuZXcgSW52b2ljZU5vdGVNYW5hZ2VyKCk7XG4gIH1cblxuICByZWZyZXNoKG9yZGVySWQpIHtcbiAgICAkLmdldEpTT04odGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19nZXRfZG9jdW1lbnRzJywge29yZGVySWR9KSlcbiAgICAgIC50aGVuKChyZXNwb25zZSkgPT4ge1xuICAgICAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJEb2N1bWVudHNUYWJDb3VudCkudGV4dChyZXNwb25zZS50b3RhbCk7XG4gICAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlckRvY3VtZW50c1RhYkJvZHkpLmh0bWwocmVzcG9uc2UuaHRtbCk7XG4gICAgICAgIHRoaXMuaW52b2ljZU5vdGVNYW5hZ2VyLnNldHVwTGlzdGVuZXJzKCk7XG4gICAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci1kb2N1bWVudHMtcmVmcmVzaGVyLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICdAcGFnZXMvb3JkZXIvT3JkZXJWaWV3UGFnZU1hcCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3JkZXJJbnZvaWNlc1JlZnJlc2hlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMucm91dGVyID0gbmV3IFJvdXRlcigpO1xuICB9XG5cbiAgcmVmcmVzaChvcmRlcklkKSB7XG4gICAgJC5nZXRKU09OKHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9vcmRlcnNfZ2V0X2ludm9pY2VzJywge29yZGVySWR9KSlcbiAgICAgIC50aGVuKChyZXNwb25zZSkgPT4ge1xuICAgICAgICBpZiAoIXJlc3BvbnNlIHx8ICFyZXNwb25zZS5pbnZvaWNlcyB8fCBPYmplY3Qua2V5cyhyZXNwb25zZS5pbnZvaWNlcykubGVuZ3RoIDw9IDApIHtcbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBjb25zdCAkcGF5bWVudEludm9pY2VTZWxlY3QgPSAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJQYXltZW50SW52b2ljZVNlbGVjdCk7XG4gICAgICAgIGNvbnN0ICRhZGRQcm9kdWN0SW52b2ljZVNlbGVjdCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkSW52b2ljZVNlbGVjdCk7XG4gICAgICAgIGNvbnN0ICRleGlzdGluZ0ludm9pY2VzR3JvdXAgPSAkYWRkUHJvZHVjdEludm9pY2VTZWxlY3QuZmluZCgnb3B0Z3JvdXA6Zmlyc3QnKTtcbiAgICAgICAgY29uc3QgJHByb2R1Y3RFZGl0SW52b2ljZVNlbGVjdCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdEludm9pY2VTZWxlY3QpO1xuICAgICAgICBjb25zdCAkYWRkRGlzY291bnRJbnZvaWNlU2VsZWN0ID0gJChPcmRlclZpZXdQYWdlTWFwLmFkZENhcnRSdWxlSW52b2ljZUlkU2VsZWN0KTtcbiAgICAgICAgJGV4aXN0aW5nSW52b2ljZXNHcm91cC5lbXB0eSgpO1xuICAgICAgICAkcGF5bWVudEludm9pY2VTZWxlY3QuZW1wdHkoKTtcbiAgICAgICAgJHByb2R1Y3RFZGl0SW52b2ljZVNlbGVjdC5lbXB0eSgpO1xuICAgICAgICAkYWRkRGlzY291bnRJbnZvaWNlU2VsZWN0LmVtcHR5KCk7XG5cbiAgICAgICAgT2JqZWN0LmtleXMocmVzcG9uc2UuaW52b2ljZXMpLmZvckVhY2goKGludm9pY2VOYW1lKSA9PiB7XG4gICAgICAgICAgY29uc3QgaW52b2ljZUlkID0gcmVzcG9uc2UuaW52b2ljZXNbaW52b2ljZU5hbWVdO1xuICAgICAgICAgIGNvbnN0IGludm9pY2VOYW1lV2l0aG91dFByaWNlID0gaW52b2ljZU5hbWUuc3BsaXQoJyAtICcpWzBdO1xuXG4gICAgICAgICAgJGV4aXN0aW5nSW52b2ljZXNHcm91cC5hcHBlbmQoYDxvcHRpb24gdmFsdWU9XCIke2ludm9pY2VJZH1cIj4ke2ludm9pY2VOYW1lV2l0aG91dFByaWNlfTwvb3B0aW9uPmApO1xuICAgICAgICAgICRwYXltZW50SW52b2ljZVNlbGVjdC5hcHBlbmQoYDxvcHRpb24gdmFsdWU9XCIke2ludm9pY2VJZH1cIj4ke2ludm9pY2VOYW1lV2l0aG91dFByaWNlfTwvb3B0aW9uPmApO1xuICAgICAgICAgICRwcm9kdWN0RWRpdEludm9pY2VTZWxlY3QuYXBwZW5kKGA8b3B0aW9uIHZhbHVlPVwiJHtpbnZvaWNlSWR9XCI+JHtpbnZvaWNlTmFtZVdpdGhvdXRQcmljZX08L29wdGlvbj5gKTtcbiAgICAgICAgICAkYWRkRGlzY291bnRJbnZvaWNlU2VsZWN0LmFwcGVuZChgPG9wdGlvbiB2YWx1ZT1cIiR7aW52b2ljZUlkfVwiPiR7aW52b2ljZU5hbWV9PC9vcHRpb24+YCk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0QWRkSW52b2ljZVNlbGVjdCkuc2VsZWN0ZWRJbmRleCA9IDA7XG4gICAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvdmlldy9vcmRlci1pbnZvaWNlcy1yZWZyZXNoZXIuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCBSb3V0ZXIgZnJvbSAnQGNvbXBvbmVudHMvcm91dGVyJztcbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gJ0BwYWdlcy9vcmRlci9PcmRlclZpZXdQYWdlTWFwJztcblxuY29uc3QgeyR9ID0gd2luZG93O1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPcmRlclBheW1lbnRzUmVmcmVzaGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gIH1cblxuICByZWZyZXNoKG9yZGVySWQpIHtcbiAgICAkLmFqYXgodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19nZXRfcGF5bWVudHMnLCB7b3JkZXJJZH0pKVxuICAgICAgICAudGhlbihcbiAgICAgICAgcmVzcG9uc2UgPT4ge1xuICAgICAgICAgICAgJChPcmRlclZpZXdQYWdlTWFwLnZpZXdPcmRlclBheW1lbnRzQWxlcnQpLnJlbW92ZSgpO1xuICAgICAgICAgICAgJChgJHtPcmRlclZpZXdQYWdlTWFwLnZpZXdPcmRlclBheW1lbnRzQmxvY2t9IC5jYXJkLWJvZHlgKS5wcmVwZW5kKHJlc3BvbnNlKTtcbiAgICAgICAgICB9LFxuICAgICAgICAgIHJlc3BvbnNlID0+IHtcbiAgICAgICAgICAgIGlmIChyZXNwb25zZS5yZXNwb25zZUpTT04gJiYgcmVzcG9uc2UucmVzcG9uc2VKU09OLm1lc3NhZ2UpIHtcbiAgICAgICAgICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogcmVzcG9uc2UucmVzcG9uc2VKU09OLm1lc3NhZ2V9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9XG4gICAgICAgICk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItcGF5bWVudHMtcmVmcmVzaGVyLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICdAcGFnZXMvb3JkZXIvT3JkZXJWaWV3UGFnZU1hcCc7XG5pbXBvcnQgeyBOdW1iZXJGb3JtYXR0ZXIgfSBmcm9tICdAYXBwL2NsZHInO1xuXG5jb25zdCB7JH0gPSB3aW5kb3c7XG5cbi8qKlxuICogbWFuYWdlcyBhbGwgcHJvZHVjdCBjYW5jZWwgYWN0aW9ucywgdGhhdCBpbmNsdWRlcyBhbGwgcmVmdW5kIG9wZXJhdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3JkZXJQcm9kdWN0Q2FuY2VsIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gICAgdGhpcy5jYW5jZWxQcm9kdWN0Rm9ybSA9ICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LmZvcm0pO1xuICAgIHRoaXMub3JkZXJJZCA9IHRoaXMuY2FuY2VsUHJvZHVjdEZvcm0uZGF0YSgnb3JkZXJJZCcpO1xuICAgIHRoaXMub3JkZXJEZWxpdmVyZWQgPSBwYXJzZUludCh0aGlzLmNhbmNlbFByb2R1Y3RGb3JtLmRhdGEoJ2lzRGVsaXZlcmVkJyksIDEwKSA9PT0gMTtcbiAgICB0aGlzLmlzVGF4SW5jbHVkZWQgPSBwYXJzZUludCh0aGlzLmNhbmNlbFByb2R1Y3RGb3JtLmRhdGEoJ2lzVGF4SW5jbHVkZWQnKSwgMTApID09PSAxO1xuICAgIHRoaXMuZGlzY291bnRzQW1vdW50ID0gcGFyc2VGbG9hdCh0aGlzLmNhbmNlbFByb2R1Y3RGb3JtLmRhdGEoJ2Rpc2NvdW50c0Ftb3VudCcpKTtcbiAgICB0aGlzLmN1cnJlbmN5Rm9ybWF0dGVyID0gTnVtYmVyRm9ybWF0dGVyLmJ1aWxkKHRoaXMuY2FuY2VsUHJvZHVjdEZvcm0uZGF0YSgncHJpY2VTcGVjaWZpY2F0aW9uJykpO1xuICAgIHRoaXMudXNlQW1vdW50SW5wdXRzID0gdHJ1ZTtcbiAgICB0aGlzLmxpc3RlbkZvcklucHV0cygpO1xuICB9XG5cbiAgc2hvd1BhcnRpYWxSZWZ1bmQoKSB7XG4gICAgLy8gQWx3YXlzIHN0YXJ0IGJ5IGhpZGluZyBlbGVtZW50cyB0aGVuIHNob3cgdGhlIG90aGVycywgc2luY2Ugc29tZSBlbGVtZW50cyBhcmUgY29tbW9uXG4gICAgdGhpcy5oaWRlQ2FuY2VsRWxlbWVudHMoKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC50b2dnbGUucGFydGlhbFJlZnVuZCkuc2hvdygpO1xuICAgIHRoaXMudXNlQW1vdW50SW5wdXRzID0gdHJ1ZTtcbiAgICB0aGlzLmluaXRGb3JtKFxuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuYnV0dG9ucy5zYXZlKS5kYXRhKCdwYXJ0aWFsUmVmdW5kTGFiZWwnKSxcbiAgICAgIHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9vcmRlcnNfcGFydGlhbF9yZWZ1bmQnLCB7b3JkZXJJZDogdGhpcy5vcmRlcklkfSksXG4gICAgICAncGFydGlhbC1yZWZ1bmQnXG4gICAgKTtcbiAgfVxuXG4gIHNob3dTdGFuZGFyZFJlZnVuZCgpIHtcbiAgICAvLyBBbHdheXMgc3RhcnQgYnkgaGlkaW5nIGVsZW1lbnRzIHRoZW4gc2hvdyB0aGUgb3RoZXJzLCBzaW5jZSBzb21lIGVsZW1lbnRzIGFyZSBjb21tb25cbiAgICB0aGlzLmhpZGVDYW5jZWxFbGVtZW50cygpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnRvZ2dsZS5zdGFuZGFyZFJlZnVuZCkuc2hvdygpO1xuICAgIHRoaXMudXNlQW1vdW50SW5wdXRzID0gZmFsc2U7XG4gICAgdGhpcy5pbml0Rm9ybShcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LmJ1dHRvbnMuc2F2ZSkuZGF0YSgnc3RhbmRhcmRSZWZ1bmRMYWJlbCcpLFxuICAgICAgdGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19zdGFuZGFyZF9yZWZ1bmQnLCB7b3JkZXJJZDogdGhpcy5vcmRlcklkfSksXG4gICAgICAnc3RhbmRhcmQtcmVmdW5kJ1xuICAgICk7XG4gIH1cblxuICBzaG93UmV0dXJuUHJvZHVjdCgpIHtcbiAgICAvLyBBbHdheXMgc3RhcnQgYnkgaGlkaW5nIGVsZW1lbnRzIHRoZW4gc2hvdyB0aGUgb3RoZXJzLCBzaW5jZSBzb21lIGVsZW1lbnRzIGFyZSBjb21tb25cbiAgICB0aGlzLmhpZGVDYW5jZWxFbGVtZW50cygpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnRvZ2dsZS5yZXR1cm5Qcm9kdWN0KS5zaG93KCk7XG4gICAgdGhpcy51c2VBbW91bnRJbnB1dHMgPSBmYWxzZTtcbiAgICB0aGlzLmluaXRGb3JtKFxuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuYnV0dG9ucy5zYXZlKS5kYXRhKCdyZXR1cm5Qcm9kdWN0TGFiZWwnKSxcbiAgICAgIHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9vcmRlcnNfcmV0dXJuX3Byb2R1Y3QnLCB7b3JkZXJJZDogdGhpcy5vcmRlcklkfSksXG4gICAgICAncmV0dXJuLXByb2R1Y3QnXG4gICAgKTtcbiAgfVxuXG4gIGhpZGVSZWZ1bmQoKSB7XG4gICAgdGhpcy5oaWRlQ2FuY2VsRWxlbWVudHMoKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC50YWJsZS5hY3Rpb25zKS5zaG93KCk7XG4gIH1cblxuICBoaWRlQ2FuY2VsRWxlbWVudHMoKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QudG9nZ2xlLnN0YW5kYXJkUmVmdW5kKS5oaWRlKCk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QudG9nZ2xlLnBhcnRpYWxSZWZ1bmQpLmhpZGUoKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC50b2dnbGUucmV0dXJuUHJvZHVjdCkuaGlkZSgpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnRhYmxlLmFjdGlvbnMpLmhpZGUoKTtcbiAgfVxuXG4gIGluaXRGb3JtKGFjdGlvbk5hbWUsIGZvcm1BY3Rpb24sIGZvcm1DbGFzcykge1xuICAgIHRoaXMudXBkYXRlVm91Y2hlclJlZnVuZCgpO1xuXG4gICAgdGhpcy5jYW5jZWxQcm9kdWN0Rm9ybS5wcm9wKCdhY3Rpb24nLCBmb3JtQWN0aW9uKTtcbiAgICB0aGlzLmNhbmNlbFByb2R1Y3RGb3JtLnJlbW92ZUNsYXNzKCdzdGFuZGFyZC1yZWZ1bmQgcGFydGlhbC1yZWZ1bmQgcmV0dXJuLXByb2R1Y3QgY2FuY2VsLXByb2R1Y3QnKS5hZGRDbGFzcyhmb3JtQ2xhc3MpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LmJ1dHRvbnMuc2F2ZSkuaHRtbChhY3Rpb25OYW1lKTtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC50YWJsZS5oZWFkZXIpLmh0bWwoYWN0aW9uTmFtZSk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuY2hlY2tib3hlcy5yZXN0b2NrKS5wcm9wKCdjaGVja2VkJywgdGhpcy5vcmRlckRlbGl2ZXJlZCk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuY2hlY2tib3hlcy5jcmVkaXRTbGlwKS5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuY2hlY2tib3hlcy52b3VjaGVyKS5wcm9wKCdjaGVja2VkJywgZmFsc2UpO1xuICB9XG5cbiAgbGlzdGVuRm9ySW5wdXRzKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjaGFuZ2UnLCBPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuaW5wdXRzLnF1YW50aXR5LCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRwcm9kdWN0UXVhbnRpdHlJbnB1dCA9ICQoZXZlbnQudGFyZ2V0KTtcbiAgICAgIGlmICh0aGlzLnVzZUFtb3VudElucHV0cykge1xuICAgICAgICB0aGlzLnVwZGF0ZUFtb3VudElucHV0KCRwcm9kdWN0UXVhbnRpdHlJbnB1dCk7XG4gICAgICB9XG4gICAgICB0aGlzLnVwZGF0ZVZvdWNoZXJSZWZ1bmQoKTtcbiAgICB9KTtcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjaGFuZ2UnLCBPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuaW5wdXRzLmFtb3VudCwgKCkgPT4ge1xuICAgICAgdGhpcy51cGRhdGVWb3VjaGVyUmVmdW5kKCk7XG4gICAgfSk7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2hhbmdlJywgT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LmlucHV0cy5zZWxlY3RvciwgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkcHJvZHVjdENoZWNrYm94ID0gJChldmVudC50YXJnZXQpO1xuICAgICAgY29uc3QgJHBhcmVudENlbGwgPSAkcHJvZHVjdENoZWNrYm94LnBhcmVudHMoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnRhYmxlLmNlbGwpO1xuICAgICAgY29uc3QgcHJvZHVjdFF1YW50aXR5SW5wdXQgPSAkcGFyZW50Q2VsbC5maW5kKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5pbnB1dHMucXVhbnRpdHkpO1xuICAgICAgY29uc3QgcmVmdW5kYWJsZVF1YW50aXR5ID0gcGFyc2VJbnQocHJvZHVjdFF1YW50aXR5SW5wdXQuZGF0YSgncXVhbnRpdHlSZWZ1bmRhYmxlJyksIDEwKTtcbiAgICAgIGNvbnN0IHByb2R1Y3RRdWFudGl0eSA9IHBhcnNlSW50KHByb2R1Y3RRdWFudGl0eUlucHV0LnZhbCgpLCAxMCk7XG4gICAgICBpZiAoISRwcm9kdWN0Q2hlY2tib3guaXMoJzpjaGVja2VkJykpIHtcbiAgICAgICAgcHJvZHVjdFF1YW50aXR5SW5wdXQudmFsKDApO1xuICAgICAgfSBlbHNlIGlmIChOdW1iZXIuaXNOYU4ocHJvZHVjdFF1YW50aXR5KSB8fCBwcm9kdWN0UXVhbnRpdHkgPT09IDApIHtcbiAgICAgICAgcHJvZHVjdFF1YW50aXR5SW5wdXQudmFsKHJlZnVuZGFibGVRdWFudGl0eSk7XG4gICAgICB9XG4gICAgICB0aGlzLnVwZGF0ZVZvdWNoZXJSZWZ1bmQoKTtcbiAgICB9KTtcbiAgfVxuXG4gIHVwZGF0ZUFtb3VudElucHV0KCRwcm9kdWN0UXVhbnRpdHlJbnB1dCkge1xuICAgIGNvbnN0ICRwYXJlbnRDZWxsID0gJHByb2R1Y3RRdWFudGl0eUlucHV0LnBhcmVudHMoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnRhYmxlLmNlbGwpO1xuICAgIGNvbnN0ICRwcm9kdWN0QW1vdW50ID0gJHBhcmVudENlbGwuZmluZChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuaW5wdXRzLmFtb3VudCk7XG4gICAgY29uc3QgcHJvZHVjdFF1YW50aXR5ID0gcGFyc2VJbnQoJHByb2R1Y3RRdWFudGl0eUlucHV0LnZhbCgpLCAxMCk7XG4gICAgaWYgKHByb2R1Y3RRdWFudGl0eSA8PSAwKSB7XG4gICAgICAkcHJvZHVjdEFtb3VudC52YWwoMCk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCBwcmljZUZpZWxkTmFtZSA9IHRoaXMuaXNUYXhJbmNsdWRlZCA/ICdwcm9kdWN0UHJpY2VUYXhJbmNsJyA6ICdwcm9kdWN0UHJpY2VUYXhFeGNsJztcbiAgICBjb25zdCBwcm9kdWN0VW5pdFByaWNlID0gcGFyc2VGbG9hdCgkcHJvZHVjdFF1YW50aXR5SW5wdXQuZGF0YShwcmljZUZpZWxkTmFtZSkpO1xuICAgIGNvbnN0IGFtb3VudFJlZnVuZGFibGUgPSBwYXJzZUZsb2F0KCRwcm9kdWN0UXVhbnRpdHlJbnB1dC5kYXRhKCdhbW91bnRSZWZ1bmRhYmxlJykpO1xuICAgIGNvbnN0IGd1ZXNzZWRBbW91bnQgPSAocHJvZHVjdFVuaXRQcmljZSAqIHByb2R1Y3RRdWFudGl0eSkgPCBhbW91bnRSZWZ1bmRhYmxlID9cbiAgICAgIChwcm9kdWN0VW5pdFByaWNlICogcHJvZHVjdFF1YW50aXR5KSA6IGFtb3VudFJlZnVuZGFibGU7XG4gICAgY29uc3QgYW1vdW50VmFsdWUgPSBwYXJzZUZsb2F0KCRwcm9kdWN0QW1vdW50LnZhbCgpKTtcbiAgICBpZiAoJHByb2R1Y3RBbW91bnQudmFsKCkgPT09ICcnIHx8IGFtb3VudFZhbHVlID09PSAwIHx8IGFtb3VudFZhbHVlID4gZ3Vlc3NlZEFtb3VudCkge1xuICAgICAgJHByb2R1Y3RBbW91bnQudmFsKGd1ZXNzZWRBbW91bnQpO1xuICAgIH1cbiAgfVxuXG4gIGdldFJlZnVuZEFtb3VudCgpIHtcbiAgICBsZXQgdG90YWxBbW91bnQgPSAwO1xuXG4gICAgaWYgKHRoaXMudXNlQW1vdW50SW5wdXRzKSB7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5pbnB1dHMuYW1vdW50KS5lYWNoKChpbmRleCwgYW1vdW50KSA9PiB7XG4gICAgICAgIGNvbnN0IGZsb2F0VmFsdWUgPSBwYXJzZUZsb2F0KGFtb3VudC52YWx1ZSk7XG4gICAgICAgIHRvdGFsQW1vdW50ICs9ICFOdW1iZXIuaXNOYU4oZmxvYXRWYWx1ZSkgPyBmbG9hdFZhbHVlIDogMDtcbiAgICAgIH0pO1xuICAgIH0gZWxzZSB7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5pbnB1dHMucXVhbnRpdHkpLmVhY2goKGluZGV4LCBxdWFudGl0eSkgPT4ge1xuICAgICAgICBjb25zdCAkcXVhbnRpdHlJbnB1dCA9ICQocXVhbnRpdHkpO1xuICAgICAgICBjb25zdCBwcmljZUZpZWxkTmFtZSA9IHRoaXMuaXNUYXhJbmNsdWRlZCA/ICdwcm9kdWN0UHJpY2VUYXhJbmNsJyA6ICdwcm9kdWN0UHJpY2VUYXhFeGNsJztcbiAgICAgICAgY29uc3QgcHJvZHVjdFVuaXRQcmljZSA9IHBhcnNlRmxvYXQoJHF1YW50aXR5SW5wdXQuZGF0YShwcmljZUZpZWxkTmFtZSkpO1xuICAgICAgICBjb25zdCBwcm9kdWN0UXVhbnRpdHkgPSBwYXJzZUludCgkcXVhbnRpdHlJbnB1dC52YWwoKSwgMTApO1xuICAgICAgICB0b3RhbEFtb3VudCArPSBwcm9kdWN0UXVhbnRpdHkgKiBwcm9kdWN0VW5pdFByaWNlO1xuICAgICAgfSk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHRvdGFsQW1vdW50O1xuICB9XG5cbiAgdXBkYXRlVm91Y2hlclJlZnVuZCgpIHtcbiAgICBjb25zdCByZWZ1bmRBbW91bnQgPSB0aGlzLmdldFJlZnVuZEFtb3VudCgpO1xuXG4gICAgdGhpcy51cGRhdGVWb3VjaGVyUmVmdW5kVHlwZUxhYmVsKFxuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QucmFkaW9zLnZvdWNoZXJSZWZ1bmRUeXBlLnByb2R1Y3RQcmljZXMpLFxuICAgICAgcmVmdW5kQW1vdW50XG4gICAgKTtcbiAgICBjb25zdCByZWZ1bmRWb3VjaGVyRXhjbHVkZWQgPSByZWZ1bmRBbW91bnQgLSB0aGlzLmRpc2NvdW50c0Ftb3VudDtcbiAgICB0aGlzLnVwZGF0ZVZvdWNoZXJSZWZ1bmRUeXBlTGFiZWwoXG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5yYWRpb3Mudm91Y2hlclJlZnVuZFR5cGUucHJvZHVjdFByaWNlc1ZvdWNoZXJFeGNsdWRlZCksXG4gICAgICByZWZ1bmRWb3VjaGVyRXhjbHVkZWRcbiAgICApO1xuXG4gICAgLy8gRGlzYWJsZSB2b3VjaGVyIGV4Y2x1ZGVkIG9wdGlvbiB3aGVuIHRoZSB2b3VjaGVyIGFtb3VudCBpcyB0b28gaGlnaFxuICAgIGlmIChyZWZ1bmRWb3VjaGVyRXhjbHVkZWQgPCAwKSB7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5yYWRpb3Mudm91Y2hlclJlZnVuZFR5cGUucHJvZHVjdFByaWNlc1ZvdWNoZXJFeGNsdWRlZClcbiAgICAgICAgLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSlcbiAgICAgICAgLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAuY2FuY2VsUHJvZHVjdC5yYWRpb3Mudm91Y2hlclJlZnVuZFR5cGUucHJvZHVjdFByaWNlcykucHJvcCgnY2hlY2tlZCcsIHRydWUpO1xuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QucmFkaW9zLnZvdWNoZXJSZWZ1bmRUeXBlLm5lZ2F0aXZlRXJyb3JNZXNzYWdlKS5zaG93KCk7XG4gICAgfSBlbHNlIHtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnJhZGlvcy52b3VjaGVyUmVmdW5kVHlwZS5wcm9kdWN0UHJpY2VzVm91Y2hlckV4Y2x1ZGVkKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnJhZGlvcy52b3VjaGVyUmVmdW5kVHlwZS5uZWdhdGl2ZUVycm9yTWVzc2FnZSkuaGlkZSgpO1xuICAgIH1cbiAgfVxuXG4gIHVwZGF0ZVZvdWNoZXJSZWZ1bmRUeXBlTGFiZWwoJGlucHV0LCByZWZ1bmRBbW91bnQpIHtcbiAgICBjb25zdCBkZWZhdWx0TGFiZWwgPSAkaW5wdXQuZGF0YSgnZGVmYXVsdExhYmVsJyk7XG4gICAgY29uc3QgJGxhYmVsID0gJGlucHV0LnBhcmVudHMoJ2xhYmVsJyk7XG4gICAgY29uc3QgZm9ybWF0dGVkQW1vdW50ID0gdGhpcy5jdXJyZW5jeUZvcm1hdHRlci5mb3JtYXQocmVmdW5kQW1vdW50KTtcblxuICAgIC8vIENoYW5nZSB0aGUgZW5kaW5nIHRleHQgcGFydCBvbmx5IHRvIGF2b2lkIHJlbW92aW5nIHRoZSBpbnB1dCAodGhlIEVPTCBpcyBvbiBwdXJwb3NlIGZvciBiZXR0ZXIgZGlzcGxheSlcbiAgICAkbGFiZWwuZ2V0KDApLmxhc3RDaGlsZC5ub2RlVmFsdWUgPSBgXG4gICAgJHtkZWZhdWx0TGFiZWx9ICR7Zm9ybWF0dGVkQW1vdW50fWA7XG4gIH1cblxuICBzaG93Q2FuY2VsUHJvZHVjdEZvcm0oKSB7XG4gICAgY29uc3QgY2FuY2VsUHJvZHVjdFJvdXRlID0gdGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19jYW5jZWxsYXRpb24nLCB7b3JkZXJJZDogdGhpcy5vcmRlcklkfSk7XG4gICAgdGhpcy5pbml0Rm9ybShcbiAgICAgICAgJChPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFByb2R1Y3QuYnV0dG9ucy5zYXZlKS5kYXRhKCdjYW5jZWxMYWJlbCcpLFxuICAgICAgICBjYW5jZWxQcm9kdWN0Um91dGUsXG4gICAgICAgICdjYW5jZWwtcHJvZHVjdCcsXG4gICAgKTtcbiAgICB0aGlzLmhpZGVDYW5jZWxFbGVtZW50cygpO1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5jYW5jZWxQcm9kdWN0LnRvZ2dsZS5jYW5jZWxQcm9kdWN0cykuc2hvdygpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtY2FuY2VsLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICdAcGFnZXMvb3JkZXIvT3JkZXJWaWV3UGFnZU1hcCc7XG5pbXBvcnQge0V2ZW50RW1pdHRlcn0gZnJvbSAnQGNvbXBvbmVudHMvZXZlbnQtZW1pdHRlcic7XG5pbXBvcnQgT3JkZXJWaWV3RXZlbnRNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL3ZpZXcvb3JkZXItdmlldy1ldmVudC1tYXAnO1xuaW1wb3J0IE9yZGVyUHJpY2VzIGZyb20gJ0BwYWdlcy9vcmRlci92aWV3L29yZGVyLXByaWNlcyc7XG5pbXBvcnQgQ29uZmlybU1vZGFsIGZyb20gJ0Bjb21wb25lbnRzL21vZGFsJztcbmltcG9ydCBPcmRlclByaWNlc1JlZnJlc2hlciBmcm9tICdAcGFnZXMvb3JkZXIvdmlldy9vcmRlci1wcmljZXMtcmVmcmVzaGVyJztcblxuY29uc3QgeyR9ID0gd2luZG93O1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPcmRlclByb2R1Y3RFZGl0IHtcbiAgY29uc3RydWN0b3Iob3JkZXJEZXRhaWxJZCkge1xuICAgIHRoaXMucm91dGVyID0gbmV3IFJvdXRlcigpO1xuICAgIHRoaXMub3JkZXJEZXRhaWxJZCA9IG9yZGVyRGV0YWlsSWQ7XG4gICAgdGhpcy5wcm9kdWN0Um93ID0gJChgI29yZGVyUHJvZHVjdF8ke3RoaXMub3JkZXJEZXRhaWxJZH1gKTtcbiAgICB0aGlzLnByb2R1Y3QgPSB7fTtcbiAgICB0aGlzLmN1cnJlbmN5UHJlY2lzaW9uID0gJChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RzVGFibGUpLmRhdGEoJ2N1cnJlbmN5UHJlY2lzaW9uJyk7XG4gICAgdGhpcy5wcmljZVRheENhbGN1bGF0b3IgPSBuZXcgT3JkZXJQcmljZXMoKTtcbiAgICB0aGlzLnByb2R1Y3RFZGl0U2F2ZUJ0biA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFNhdmVCdG4pO1xuICAgIHRoaXMucXVhbnRpdHlJbnB1dCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFF1YW50aXR5SW5wdXQpO1xuICAgIHRoaXMub3JkZXJQcmljZXNSZWZyZXNoZXIgPSBuZXcgT3JkZXJQcmljZXNSZWZyZXNoZXIoKTtcbiAgfVxuXG4gIHNldHVwTGlzdGVuZXIoKSB7XG4gICAgdGhpcy5xdWFudGl0eUlucHV0Lm9uKCdjaGFuZ2Uga2V5dXAnLCBldmVudCA9PiB7XG4gICAgICBjb25zdCBuZXdRdWFudGl0eSA9IE51bWJlcihldmVudC50YXJnZXQudmFsdWUpO1xuICAgICAgY29uc3QgYXZhaWxhYmxlUXVhbnRpdHkgPSBwYXJzZUludCgkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ2F2YWlsYWJsZVF1YW50aXR5JyksIDEwKTtcbiAgICAgIGNvbnN0IHByZXZpb3VzUXVhbnRpdHkgPSBwYXJzZUludCh0aGlzLnF1YW50aXR5SW5wdXQuZGF0YSgncHJldmlvdXNRdWFudGl0eScpLCAxMCk7XG4gICAgICBjb25zdCByZW1haW5pbmdBdmFpbGFibGUgPSBhdmFpbGFibGVRdWFudGl0eSAtIChuZXdRdWFudGl0eSAtIHByZXZpb3VzUXVhbnRpdHkpO1xuICAgICAgY29uc3QgYXZhaWxhYmxlT3V0T2ZTdG9jayA9IHRoaXMuYXZhaWxhYmxlVGV4dC5kYXRhKCdhdmFpbGFibGVPdXRPZlN0b2NrJyk7XG4gICAgICB0aGlzLnF1YW50aXR5ID0gbmV3UXVhbnRpdHk7XG4gICAgICB0aGlzLmF2YWlsYWJsZVRleHQudGV4dChyZW1haW5pbmdBdmFpbGFibGUpO1xuICAgICAgdGhpcy5hdmFpbGFibGVUZXh0LnRvZ2dsZUNsYXNzKCd0ZXh0LWRhbmdlciBmb250LXdlaWdodC1ib2xkJywgcmVtYWluaW5nQXZhaWxhYmxlIDwgMCk7XG4gICAgICB0aGlzLnVwZGF0ZVRvdGFsKCk7XG4gICAgICBjb25zdCBkaXNhYmxlRWRpdEFjdGlvbkJ0biA9IG5ld1F1YW50aXR5IDw9IDAgfHwgKHJlbWFpbmluZ0F2YWlsYWJsZSA8IDAgJiYgIWF2YWlsYWJsZU91dE9mU3RvY2spO1xuICAgICAgdGhpcy5wcm9kdWN0RWRpdFNhdmVCdG4ucHJvcCgnZGlzYWJsZWQnLCBkaXNhYmxlRWRpdEFjdGlvbkJ0bik7XG4gICAgfSk7XG5cbiAgICB0aGlzLnByb2R1Y3RFZGl0SW52b2ljZVNlbGVjdC5vbignY2hhbmdlJywgKCkgPT4ge1xuICAgICAgdGhpcy5wcm9kdWN0RWRpdFNhdmVCdG4ucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gICAgfSk7XG5cbiAgICB0aGlzLnByaWNlVGF4SW5jbHVkZWRJbnB1dC5vbignY2hhbmdlIGtleXVwJywgZXZlbnQgPT4ge1xuICAgICAgdGhpcy50YXhJbmNsdWRlZCA9IHBhcnNlRmxvYXQoZXZlbnQudGFyZ2V0LnZhbHVlKTtcbiAgICAgIHRoaXMudGF4RXhjbHVkZWQgPSB0aGlzLnByaWNlVGF4Q2FsY3VsYXRvci5jYWxjdWxhdGVUYXhFeGNsdWRlZChcbiAgICAgICAgdGhpcy50YXhJbmNsdWRlZCxcbiAgICAgICAgdGhpcy50YXhSYXRlLFxuICAgICAgICB0aGlzLmN1cnJlbmN5UHJlY2lzaW9uXG4gICAgICApO1xuICAgICAgdGhpcy5wcmljZVRheEV4Y2x1ZGVkSW5wdXQudmFsKHRoaXMudGF4RXhjbHVkZWQpO1xuICAgICAgdGhpcy51cGRhdGVUb3RhbCgpO1xuICAgIH0pO1xuXG4gICAgdGhpcy5wcmljZVRheEV4Y2x1ZGVkSW5wdXQub24oJ2NoYW5nZSBrZXl1cCcsIGV2ZW50ID0+IHtcbiAgICAgIHRoaXMudGF4RXhjbHVkZWQgPSBwYXJzZUZsb2F0KGV2ZW50LnRhcmdldC52YWx1ZSk7XG4gICAgICB0aGlzLnRheEluY2x1ZGVkID0gdGhpcy5wcmljZVRheENhbGN1bGF0b3IuY2FsY3VsYXRlVGF4SW5jbHVkZWQoXG4gICAgICAgIHRoaXMudGF4RXhjbHVkZWQsXG4gICAgICAgIHRoaXMudGF4UmF0ZSxcbiAgICAgICAgdGhpcy5jdXJyZW5jeVByZWNpc2lvblxuICAgICAgKTtcbiAgICAgIHRoaXMucHJpY2VUYXhJbmNsdWRlZElucHV0LnZhbCh0aGlzLnRheEluY2x1ZGVkKTtcbiAgICAgIHRoaXMudXBkYXRlVG90YWwoKTtcbiAgICB9KTtcblxuICAgIHRoaXMucHJvZHVjdEVkaXRTYXZlQnRuLm9uKCdjbGljaycsIGV2ZW50ID0+IHtcbiAgICAgIGNvbnN0ICRidG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgY29uZmlybWVkID0gd2luZG93LmNvbmZpcm0oJGJ0bi5kYXRhKCd1cGRhdGVNZXNzYWdlJykpO1xuXG4gICAgICBpZiAoIWNvbmZpcm1lZCkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgICRidG4ucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcbiAgICAgIHRoaXMuaGFuZGxlRWRpdFByb2R1Y3RXaXRoQ29uZmlybWF0aW9uTW9kYWwoZXZlbnQpO1xuICAgIH0pO1xuXG4gICAgdGhpcy5wcm9kdWN0RWRpdENhbmNlbEJ0bi5vbignY2xpY2snLCAoKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChPcmRlclZpZXdFdmVudE1hcC5wcm9kdWN0RWRpdGlvbkNhbmNlbGVkLCB7XG4gICAgICAgIG9yZGVyRGV0YWlsSWQ6IHRoaXMub3JkZXJEZXRhaWxJZFxuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cblxuICB1cGRhdGVUb3RhbCgpIHtcbiAgICBjb25zdCB1cGRhdGVkVG90YWwgPSB0aGlzLnByaWNlVGF4Q2FsY3VsYXRvci5jYWxjdWxhdGVUb3RhbFByaWNlKFxuICAgICAgdGhpcy5xdWFudGl0eSxcbiAgICAgIHRoaXMuaXNPcmRlclRheEluY2x1ZGVkID8gdGhpcy50YXhJbmNsdWRlZCA6IHRoaXMudGF4RXhjbHVkZWQsXG4gICAgICB0aGlzLmN1cnJlbmN5UHJlY2lzaW9uXG4gICAgKTtcbiAgICB0aGlzLnByaWNlVG90YWxUZXh0Lmh0bWwodXBkYXRlZFRvdGFsKTtcbiAgICB0aGlzLnByb2R1Y3RFZGl0U2F2ZUJ0bi5wcm9wKCdkaXNhYmxlZCcsIHVwZGF0ZWRUb3RhbCA9PT0gdGhpcy5pbml0aWFsVG90YWwpO1xuICB9XG5cbiAgZGlzcGxheVByb2R1Y3QocHJvZHVjdCkge1xuICAgIHRoaXMucHJvZHVjdFJvd0VkaXQgPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRSb3dUZW1wbGF0ZSkuY2xvbmUodHJ1ZSk7XG4gICAgdGhpcy5wcm9kdWN0Um93RWRpdC5hdHRyKCdpZCcsIGBlZGl0T3JkZXJQcm9kdWN0XyR7dGhpcy5vcmRlckRldGFpbElkfWApO1xuICAgIHRoaXMucHJvZHVjdFJvd0VkaXQuZmluZCgnKltpZF0nKS5lYWNoKGZ1bmN0aW9uIHJlbW92ZUFsbElkcygpIHtcbiAgICAgICQodGhpcykucmVtb3ZlQXR0cignaWQnKTtcbiAgICB9KTtcblxuICAgIC8vIEZpbmQgY29udHJvbHNcbiAgICB0aGlzLnByb2R1Y3RFZGl0U2F2ZUJ0biA9IHRoaXMucHJvZHVjdFJvd0VkaXQuZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0U2F2ZUJ0bik7XG4gICAgdGhpcy5wcm9kdWN0RWRpdENhbmNlbEJ0biA9IHRoaXMucHJvZHVjdFJvd0VkaXQuZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0Q2FuY2VsQnRuKTtcbiAgICB0aGlzLnByb2R1Y3RFZGl0SW52b2ljZVNlbGVjdCA9IHRoaXMucHJvZHVjdFJvd0VkaXQuZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0SW52b2ljZVNlbGVjdCk7XG4gICAgdGhpcy5wcm9kdWN0RWRpdEltYWdlID0gdGhpcy5wcm9kdWN0Um93RWRpdC5maW5kKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRJbWFnZSk7XG4gICAgdGhpcy5wcm9kdWN0RWRpdE5hbWUgPSB0aGlzLnByb2R1Y3RSb3dFZGl0LmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdE5hbWUpO1xuICAgIHRoaXMucHJpY2VUYXhJbmNsdWRlZElucHV0ID0gdGhpcy5wcm9kdWN0Um93RWRpdC5maW5kKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRQcmljZVRheEluY2xJbnB1dCk7XG4gICAgdGhpcy5wcmljZVRheEV4Y2x1ZGVkSW5wdXQgPSB0aGlzLnByb2R1Y3RSb3dFZGl0LmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFByaWNlVGF4RXhjbElucHV0KTtcbiAgICB0aGlzLnF1YW50aXR5SW5wdXQgPSB0aGlzLnByb2R1Y3RSb3dFZGl0LmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdFF1YW50aXR5SW5wdXQpO1xuICAgIHRoaXMubG9jYXRpb25UZXh0ID0gdGhpcy5wcm9kdWN0Um93RWRpdC5maW5kKE9yZGVyVmlld1BhZ2VNYXAucHJvZHVjdEVkaXRMb2NhdGlvblRleHQpO1xuICAgIHRoaXMuYXZhaWxhYmxlVGV4dCA9IHRoaXMucHJvZHVjdFJvd0VkaXQuZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0QXZhaWxhYmxlVGV4dCk7XG4gICAgdGhpcy5wcmljZVRvdGFsVGV4dCA9IHRoaXMucHJvZHVjdFJvd0VkaXQuZmluZChPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0VG90YWxQcmljZVRleHQpO1xuXG4gICAgLy8gSW5pdCBpbnB1dCB2YWx1ZXNcbiAgICB0aGlzLnByaWNlVGF4RXhjbHVkZWRJbnB1dC52YWwod2luZG93LnBzX3JvdW5kKHByb2R1Y3QucHJpY2VfdGF4X2V4Y2wsIHRoaXMuY3VycmVuY3lQcmVjaXNpb24pKTtcblxuICAgIHRoaXMucHJpY2VUYXhJbmNsdWRlZElucHV0LnZhbCh3aW5kb3cucHNfcm91bmQocHJvZHVjdC5wcmljZV90YXhfaW5jbCwgdGhpcy5jdXJyZW5jeVByZWNpc2lvbikpO1xuXG4gICAgdGhpcy5xdWFudGl0eUlucHV0XG4gICAgICAudmFsKHByb2R1Y3QucXVhbnRpdHkpXG4gICAgICAuZGF0YSgnYXZhaWxhYmxlUXVhbnRpdHknLCBwcm9kdWN0LmF2YWlsYWJsZVF1YW50aXR5KVxuICAgICAgLmRhdGEoJ3ByZXZpb3VzUXVhbnRpdHknLCBwcm9kdWN0LnF1YW50aXR5KTtcbiAgICB0aGlzLmF2YWlsYWJsZVRleHQuZGF0YSgnYXZhaWxhYmxlT3V0T2ZTdG9jaycsIHByb2R1Y3QuYXZhaWxhYmxlT3V0T2ZTdG9jayk7XG5cbiAgICAvLyBzZXQgdGhpcyBwcm9kdWN0J3Mgb3JkZXJJbnZvaWNlSWQgYXMgc2VsZWN0ZWRcbiAgICBpZiAocHJvZHVjdC5vcmRlckludm9pY2VJZCkge1xuICAgICAgdGhpcy5wcm9kdWN0RWRpdEludm9pY2VTZWxlY3QudmFsKHByb2R1Y3Qub3JkZXJJbnZvaWNlSWQpO1xuICAgIH1cblxuICAgIC8vIEluaXQgZWRpdG9yIGRhdGFcbiAgICB0aGlzLnRheFJhdGUgPSBwcm9kdWN0LnRheF9yYXRlO1xuICAgIHRoaXMuaW5pdGlhbFRvdGFsID0gdGhpcy5wcmljZVRheENhbGN1bGF0b3IuY2FsY3VsYXRlVG90YWxQcmljZShcbiAgICAgIHByb2R1Y3QucXVhbnRpdHksXG4gICAgICBwcm9kdWN0LmlzT3JkZXJUYXhJbmNsdWRlZCA/IHByb2R1Y3QucHJpY2VfdGF4X2luY2wgOiBwcm9kdWN0LnByaWNlX3RheF9leGNsLFxuICAgICAgdGhpcy5jdXJyZW5jeVByZWNpc2lvblxuICAgICk7XG4gICAgdGhpcy5pc09yZGVyVGF4SW5jbHVkZWQgPSBwcm9kdWN0LmlzT3JkZXJUYXhJbmNsdWRlZDtcbiAgICB0aGlzLnF1YW50aXR5ID0gcHJvZHVjdC5xdWFudGl0eTtcbiAgICB0aGlzLnRheEluY2x1ZGVkID0gcHJvZHVjdC5wcmljZV90YXhfaW5jbDtcbiAgICB0aGlzLnRheEV4Y2x1ZGVkID0gcHJvZHVjdC5wcmljZV90YXhfZXhjbDtcblxuICAgIC8vIENvcHkgcHJvZHVjdCBjb250ZW50IGluIGNlbGxzXG4gICAgdGhpcy5wcm9kdWN0RWRpdEltYWdlLmh0bWwodGhpcy5wcm9kdWN0Um93LmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdEltYWdlKS5odG1sKCkpO1xuICAgIHRoaXMucHJvZHVjdEVkaXROYW1lLmh0bWwodGhpcy5wcm9kdWN0Um93LmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5wcm9kdWN0RWRpdE5hbWUpLmh0bWwoKSk7XG4gICAgdGhpcy5sb2NhdGlvblRleHQuaHRtbChwcm9kdWN0LmxvY2F0aW9uKTtcbiAgICB0aGlzLmF2YWlsYWJsZVRleHQuaHRtbChwcm9kdWN0LmF2YWlsYWJsZVF1YW50aXR5KTtcbiAgICB0aGlzLnByaWNlVG90YWxUZXh0Lmh0bWwodGhpcy5pbml0aWFsVG90YWwpO1xuICAgIHRoaXMucHJvZHVjdFJvdy5hZGRDbGFzcygnZC1ub25lJykuYWZ0ZXIodGhpcy5wcm9kdWN0Um93RWRpdC5yZW1vdmVDbGFzcygnZC1ub25lJykpO1xuXG4gICAgdGhpcy5zZXR1cExpc3RlbmVyKCk7XG4gIH1cblxuICBoYW5kbGVFZGl0UHJvZHVjdFdpdGhDb25maXJtYXRpb25Nb2RhbChldmVudCkge1xuICAgIGNvbnN0IHByb2R1Y3RFZGl0QnRuID0gJChgI29yZGVyUHJvZHVjdF8ke3RoaXMub3JkZXJEZXRhaWxJZH0gJHtPcmRlclZpZXdQYWdlTWFwLnByb2R1Y3RFZGl0QnV0dG9uc31gKTtcbiAgICBjb25zdCBwcm9kdWN0SWQgPSBwcm9kdWN0RWRpdEJ0bi5kYXRhKCdwcm9kdWN0LWlkJyk7XG4gICAgY29uc3QgY29tYmluYXRpb25JZCA9IHByb2R1Y3RFZGl0QnRuLmRhdGEoJ2NvbWJpbmF0aW9uLWlkJyk7XG4gICAgY29uc3Qgb3JkZXJJbnZvaWNlSWQgPSBwcm9kdWN0RWRpdEJ0bi5kYXRhKCdvcmRlci1pbnZvaWNlLWlkJyk7XG4gICAgY29uc3QgcHJvZHVjdFByaWNlTWF0Y2ggPSB0aGlzLm9yZGVyUHJpY2VzUmVmcmVzaGVyLmNoZWNrT3RoZXJQcm9kdWN0UHJpY2VzTWF0Y2goXG4gICAgICB0aGlzLnByaWNlVGF4SW5jbHVkZWRJbnB1dC52YWwoKSxcbiAgICAgIHByb2R1Y3RJZCxcbiAgICAgIGNvbWJpbmF0aW9uSWQsXG4gICAgICBvcmRlckludm9pY2VJZCxcbiAgICAgIHRoaXMub3JkZXJEZXRhaWxJZFxuICAgICk7XG5cbiAgICBpZiAocHJvZHVjdFByaWNlTWF0Y2gpIHtcbiAgICAgIHRoaXMuZWRpdFByb2R1Y3QoJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdvcmRlcklkJyksIHRoaXMub3JkZXJEZXRhaWxJZCk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCBkYXRhU2VsZWN0b3IgPSBOdW1iZXIob3JkZXJJbnZvaWNlSWQpID09PSAwID8gdGhpcy5wcmljZVRheEV4Y2x1ZGVkSW5wdXQgOiB0aGlzLnByb2R1Y3RFZGl0SW52b2ljZVNlbGVjdDtcblxuICAgIGNvbnN0IG1vZGFsRWRpdFByaWNlID0gbmV3IENvbmZpcm1Nb2RhbChcbiAgICAgIHtcbiAgICAgICAgaWQ6ICdtb2RhbC1jb25maXJtLW5ldy1wcmljZScsXG4gICAgICAgIGNvbmZpcm1UaXRsZTogZGF0YVNlbGVjdG9yLmRhdGEoJ21vZGFsLWVkaXQtcHJpY2UtdGl0bGUnKSxcbiAgICAgICAgY29uZmlybU1lc3NhZ2U6IGRhdGFTZWxlY3Rvci5kYXRhKCdtb2RhbC1lZGl0LXByaWNlLWJvZHknKSxcbiAgICAgICAgY29uZmlybUJ1dHRvbkxhYmVsOiBkYXRhU2VsZWN0b3IuZGF0YSgnbW9kYWwtZWRpdC1wcmljZS1hcHBseScpLFxuICAgICAgICBjbG9zZUJ1dHRvbkxhYmVsOiBkYXRhU2VsZWN0b3IuZGF0YSgnbW9kYWwtZWRpdC1wcmljZS1jYW5jZWwnKVxuICAgICAgfSxcbiAgICAgICgpID0+IHtcbiAgICAgICAgdGhpcy5lZGl0UHJvZHVjdCgkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ29yZGVySWQnKSwgdGhpcy5vcmRlckRldGFpbElkKTtcbiAgICAgIH1cbiAgICApO1xuXG4gICAgbW9kYWxFZGl0UHJpY2Uuc2hvdygpO1xuICB9XG5cbiAgZWRpdFByb2R1Y3Qob3JkZXJJZCwgb3JkZXJEZXRhaWxJZCkge1xuICAgIGNvbnN0IHBhcmFtcyA9IHtcbiAgICAgIHByaWNlX3RheF9pbmNsOiB0aGlzLnByaWNlVGF4SW5jbHVkZWRJbnB1dC52YWwoKSxcbiAgICAgIHByaWNlX3RheF9leGNsOiB0aGlzLnByaWNlVGF4RXhjbHVkZWRJbnB1dC52YWwoKSxcbiAgICAgIHF1YW50aXR5OiB0aGlzLnF1YW50aXR5SW5wdXQudmFsKCksXG4gICAgICBpbnZvaWNlOiB0aGlzLnByb2R1Y3RFZGl0SW52b2ljZVNlbGVjdC52YWwoKVxuICAgIH07XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdXJsOiB0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fb3JkZXJzX3VwZGF0ZV9wcm9kdWN0Jywge1xuICAgICAgICBvcmRlcklkLFxuICAgICAgICBvcmRlckRldGFpbElkXG4gICAgICB9KSxcbiAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgICAgZGF0YTogcGFyYW1zXG4gICAgfSkudGhlbihcbiAgICAgICgpID0+IHtcbiAgICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoT3JkZXJWaWV3RXZlbnRNYXAucHJvZHVjdFVwZGF0ZWQsIHtcbiAgICAgICAgICBvcmRlcklkLFxuICAgICAgICAgIG9yZGVyRGV0YWlsSWQsXG4gICAgICAgIH0pO1xuICAgICAgfSxcbiAgICAgIHJlc3BvbnNlID0+IHtcbiAgICAgICAgaWYgKHJlc3BvbnNlLnJlc3BvbnNlSlNPTiAmJiByZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSkge1xuICAgICAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IHJlc3BvbnNlLnJlc3BvbnNlSlNPTi5tZXNzYWdlfSk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICApO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtZWRpdC5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IFJvdXRlciBmcm9tICdAY29tcG9uZW50cy9yb3V0ZXInO1xuaW1wb3J0IHtFdmVudEVtaXR0ZXJ9IGZyb20gJ0Bjb21wb25lbnRzL2V2ZW50LWVtaXR0ZXInO1xuaW1wb3J0IE9yZGVyVmlld0V2ZW50TWFwIGZyb20gJ0BwYWdlcy9vcmRlci92aWV3L29yZGVyLXZpZXctZXZlbnQtbWFwJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPcmRlclByb2R1Y3RNYW5hZ2VyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gIH1cblxuICBoYW5kbGVEZWxldGVQcm9kdWN0RXZlbnQoZXZlbnQpIHtcbiAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgY29uc3QgY29uZmlybWVkID0gd2luZG93LmNvbmZpcm0oJGJ0bi5kYXRhKCdkZWxldGVNZXNzYWdlJykpO1xuICAgIGlmICghY29uZmlybWVkKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgJGJ0bi5wc3Rvb2x0aXAoJ2Rpc3Bvc2UnKTtcbiAgICAkYnRuLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gICAgdGhpcy5kZWxldGVQcm9kdWN0KCRidG4uZGF0YSgnb3JkZXJJZCcpLCAkYnRuLmRhdGEoJ29yZGVyRGV0YWlsSWQnKSk7XG4gIH1cblxuICBkZWxldGVQcm9kdWN0KG9yZGVySWQsIG9yZGVyRGV0YWlsSWQpIHtcbiAgICAkLmFqYXgodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19kZWxldGVfcHJvZHVjdCcsIHtvcmRlcklkLCBvcmRlckRldGFpbElkfSksIHtcbiAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgIH0pLnRoZW4oKCkgPT4ge1xuICAgICAgRXZlbnRFbWl0dGVyLmVtaXQoT3JkZXJWaWV3RXZlbnRNYXAucHJvZHVjdERlbGV0ZWRGcm9tT3JkZXIsIHtcbiAgICAgICAgb2xkT3JkZXJEZXRhaWxJZDogb3JkZXJEZXRhaWxJZCxcbiAgICAgICAgb3JkZXJJZCxcbiAgICAgIH0pO1xuICAgIH0sIChyZXNwb25zZSkgPT4ge1xuICAgICAgaWYgKHJlc3BvbnNlLm1lc3NhZ2UpIHtcbiAgICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogcmVzcG9uc2UubWVzc2FnZX0pO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci92aWV3L29yZGVyLXByb2R1Y3QtbWFuYWdlci5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IFJvdXRlciBmcm9tICdAY29tcG9uZW50cy9yb3V0ZXInO1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL09yZGVyVmlld1BhZ2VNYXAnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIE9yZGVyU2hpcHBpbmdSZWZyZXNoZXIge1xuICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgICB9XG5cbiAgICByZWZyZXNoKG9yZGVySWQpIHtcbiAgICAgICAgJC5nZXRKU09OKHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9vcmRlcnNfZ2V0X3NoaXBwaW5nJywge29yZGVySWR9KSlcbiAgICAgICAgICAgIC50aGVuKChyZXNwb25zZSkgPT4ge1xuICAgICAgICAgICAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlclNoaXBwaW5nVGFiQ291bnQpLnRleHQocmVzcG9uc2UudG90YWwpO1xuICAgICAgICAgICAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlclNoaXBwaW5nVGFiQm9keSkuaHRtbChyZXNwb25zZS5odG1sKTtcbiAgICAgICAgICAgIH0pO1xuICAgIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL3ZpZXcvb3JkZXItc2hpcHBpbmctcmVmcmVzaGVyLmpzIiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczYubnVtYmVyLmlzLW5hbicpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuTnVtYmVyLmlzTmFOO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vbnVtYmVyL2lzLW5hbi5qc1xuLy8gbW9kdWxlIGlkID0gNTk5XG4vLyBtb2R1bGUgY2h1bmtzID0gMyIsIi8vIDIwLjEuMi40IE51bWJlci5pc05hTihudW1iZXIpXG52YXIgJGV4cG9ydCA9IHJlcXVpcmUoJy4vX2V4cG9ydCcpO1xuXG4kZXhwb3J0KCRleHBvcnQuUywgJ051bWJlcicsIHtcbiAgaXNOYU46IGZ1bmN0aW9uIGlzTmFOKG51bWJlcil7XG4gICAgcmV0dXJuIG51bWJlciAhPSBudW1iZXI7XG4gIH1cbn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYubnVtYmVyLmlzLW5hbi5qc1xuLy8gbW9kdWxlIGlkID0gNjEyXG4vLyBtb2R1bGUgY2h1bmtzID0gMyJdLCJzb3VyY2VSb290IjoiIn0=