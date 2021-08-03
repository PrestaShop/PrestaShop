window["order"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 523);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

exports.default = function (instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
};

/***/ }),

/***/ 1:
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

/***/ 10:
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

/***/ 11:
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4);
module.exports = function(it){
  if(!isObject(it))throw TypeError(it + ' is not an object!');
  return it;
};

/***/ }),

/***/ 12:
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

/***/ 13:
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

/***/ 14:
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

/***/ 16:
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4)
  , document = __webpack_require__(5).document
  // in old IE typeof document.createElement is 'object'
  , is = isObject(document) && isObject(document.createElement);
module.exports = function(it){
  return is ? document.createElement(it) : {};
};

/***/ }),

/***/ 162:
/***/ (function(module, exports) {

module.exports = {"base_url":"","routes":{"admin_product_form":{"tokens":[["variable","/","\\d+","id"],["text","/sell/catalog/products"]],"defaults":[],"requirements":{"id":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_cart_rules_search":{"tokens":[["text","/sell/catalog/cart-rules/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_view":{"tokens":[["text","/view"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_customers_search":{"tokens":[["text","/sell/customers/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_carts":{"tokens":[["text","/carts"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_orders":{"tokens":[["text","/orders"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_addresses_create":{"tokens":[["text","/sell/addresses/new"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_addresses_edit":{"tokens":[["text","/edit"],["variable","/","\\d+","addressId"],["text","/sell/addresses"]],"defaults":[],"requirements":{"addressId":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_order_addresses_edit":{"tokens":[["text","/edit"],["variable","/","delivery|invoice","addressType"],["variable","/","\\d+","orderId"],["text","/sell/addresses/order"]],"defaults":[],"requirements":{"orderId":"\\d+","addressType":"delivery|invoice"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_cart_addresses_edit":{"tokens":[["text","/edit"],["variable","/","delivery|invoice","addressType"],["variable","/","\\d+","cartId"],["text","/sell/addresses/cart"]],"defaults":[],"requirements":{"cartId":"\\d+","addressType":"delivery|invoice"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_carts_view":{"tokens":[["text","/view"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_carts_info":{"tokens":[["text","/info"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_carts_create":{"tokens":[["text","/sell/orders/carts/new"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_addresses":{"tokens":[["text","/addresses"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_carrier":{"tokens":[["text","/carrier"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_currency":{"tokens":[["text","/currency"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_language":{"tokens":[["text","/language"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_set_delivery_settings":{"tokens":[["text","/rules/delivery-settings"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_add_cart_rule":{"tokens":[["text","/cart-rules"],["variable","/","[^/]++","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_delete_cart_rule":{"tokens":[["text","/delete"],["variable","/","[^/]++","cartRuleId"],["text","/cart-rules"],["variable","/","[^/]++","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_add_product":{"tokens":[["text","/products"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_product_price":{"tokens":[["text","/price"],["variable","/","\\d+","productId"],["text","/products"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+","productId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_product_quantity":{"tokens":[["text","/quantity"],["variable","/","\\d+","productId"],["text","/products"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+","productId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_delete_product":{"tokens":[["text","/delete-product"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_place":{"tokens":[["text","/sell/orders/place"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_view":{"tokens":[["text","/view"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_orders_duplicate_cart":{"tokens":[["text","/duplicate-cart"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_update_product":{"tokens":[["variable","/","\\d+","orderDetailId"],["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+","orderDetailId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_partial_refund":{"tokens":[["text","/partial-refund"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_standard_refund":{"tokens":[["text","/standard-refund"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_return_product":{"tokens":[["text","/return-product"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_send_process_order_email":{"tokens":[["text","/sell/orders/process-order-email"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_add_product":{"tokens":[["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_delete_product":{"tokens":[["text","/delete"],["variable","/","\\d+","orderDetailId"],["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+","orderDetailId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_get_discounts":{"tokens":[["text","/discounts"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_prices":{"tokens":[["text","/prices"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_payments":{"tokens":[["text","/payments"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_products":{"tokens":[["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_invoices":{"tokens":[["text","/invoices"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_documents":{"tokens":[["text","/documents"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_shipping":{"tokens":[["text","/shipping"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_cancellation":{"tokens":[["text","/cancellation"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_configure_product_pagination":{"tokens":[["text","/sell/orders/configure-product-pagination"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_product_prices":{"tokens":[["text","/products/prices"],["variable","/","\\d+","orderId"],["text","/sell/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_products_search":{"tokens":[["text","/sell/orders/products/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]}},"prefix":"","host":"localhost","port":"","scheme":"http","locale":[]}

/***/ }),

/***/ 17:
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__(2) && !__webpack_require__(7)(function(){
  return Object.defineProperty(__webpack_require__(16)('div'), 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),

/***/ 18:
/***/ (function(module, exports) {

module.exports = function(it){
  if(typeof it != 'function')throw TypeError(it + ' is not a function!');
  return it;
};

/***/ }),

/***/ 180:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
var _extends=Object.assign||function(a){for(var b,c=1;c<arguments.length;c++)for(var d in b=arguments[c],b)Object.prototype.hasOwnProperty.call(b,d)&&(a[d]=b[d]);return a},_typeof='function'==typeof Symbol&&'symbol'==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&'function'==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?'symbol':typeof a};function _classCallCheck(a,b){if(!(a instanceof b))throw new TypeError('Cannot call a class as a function')}var Routing=function a(){var b=this;_classCallCheck(this,a),this.setRoutes=function(a){b.routesRouting=a||[]},this.getRoutes=function(){return b.routesRouting},this.setBaseUrl=function(a){b.contextRouting.base_url=a},this.getBaseUrl=function(){return b.contextRouting.base_url},this.setPrefix=function(a){b.contextRouting.prefix=a},this.setScheme=function(a){b.contextRouting.scheme=a},this.getScheme=function(){return b.contextRouting.scheme},this.setHost=function(a){b.contextRouting.host=a},this.getHost=function(){return b.contextRouting.host},this.buildQueryParams=function(a,c,d){var e=new RegExp(/\[]$/);c instanceof Array?c.forEach(function(c,f){e.test(a)?d(a,c):b.buildQueryParams(a+'['+('object'===('undefined'==typeof c?'undefined':_typeof(c))?f:'')+']',c,d)}):'object'===('undefined'==typeof c?'undefined':_typeof(c))?Object.keys(c).forEach(function(e){return b.buildQueryParams(a+'['+e+']',c[e],d)}):d(a,c)},this.getRoute=function(a){var c=b.contextRouting.prefix+a;if(!!b.routesRouting[c])return b.routesRouting[c];else if(!b.routesRouting[a])throw new Error('The route "'+a+'" does not exist.');return b.routesRouting[a]},this.generate=function(a,c,d){var e=b.getRoute(a),f=c||{},g=_extends({},f),h='_scheme',i='',j=!0,k='';if((e.tokens||[]).forEach(function(b){if('text'===b[0])return i=b[1]+i,void(j=!1);if('variable'===b[0]){var c=(e.defaults||{})[b[3]];if(!1==j||!c||(f||{})[b[3]]&&f[b[3]]!==e.defaults[b[3]]){var d;if((f||{})[b[3]])d=f[b[3]],delete g[b[3]];else if(c)d=e.defaults[b[3]];else{if(j)return;throw new Error('The route "'+a+'" requires the parameter "'+b[3]+'".')}var h=!0===d||!1===d||''===d;if(!h||!j){var k=encodeURIComponent(d).replace(/%2F/g,'/');'null'===k&&null===d&&(k=''),i=b[1]+k+i}j=!1}else c&&delete g[b[3]];return}throw new Error('The token type "'+b[0]+'" is not supported.')}),''==i&&(i='/'),(e.hosttokens||[]).forEach(function(a){var b;return'text'===a[0]?void(k=a[1]+k):void('variable'===a[0]&&((f||{})[a[3]]?(b=f[a[3]],delete g[a[3]]):e.defaults[a[3]]&&(b=e.defaults[a[3]]),k=a[1]+b+k))}),i=b.contextRouting.base_url+i,e.requirements[h]&&b.getScheme()!==e.requirements[h]?i=e.requirements[h]+'://'+(k||b.getHost())+i:k&&b.getHost()!==k?i=b.getScheme()+'://'+k+i:!0===d&&(i=b.getScheme()+'://'+b.getHost()+i),0<Object.keys(g).length){var l=[],m=function(a,b){var c=b;c='function'==typeof c?c():c,c=null===c?'':c,l.push(encodeURIComponent(a)+'='+encodeURIComponent(c))};Object.keys(g).forEach(function(a){return b.buildQueryParams(a,g[a],m)}),i=i+'?'+l.join('&').replace(/%20/g,'+')}return i},this.setData=function(a){b.setBaseUrl(a.base_url),b.setRoutes(a.routes),'prefix'in a&&b.setPrefix(a.prefix),b.setHost(a.host),b.setScheme(a.scheme)},this.contextRouting={base_url:'',prefix:'',host:'',scheme:''}};module.exports=new Routing;

/***/ }),

/***/ 19:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(20), __esModule: true };

/***/ }),

/***/ 2:
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__(7)(function(){
  return Object.defineProperty({}, 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),

/***/ 20:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(21);
var $Object = __webpack_require__(3).Object;
module.exports = function defineProperty(it, key, desc){
  return $Object.defineProperty(it, key, desc);
};

/***/ }),

/***/ 21:
/***/ (function(module, exports, __webpack_require__) {

var $export = __webpack_require__(8);
// 19.1.2.4 / 15.2.3.6 Object.defineProperty(O, P, Attributes)
$export($export.S + $export.F * !__webpack_require__(2), 'Object', {defineProperty: __webpack_require__(6).f});

/***/ }),

/***/ 22:
/***/ (function(module, exports, __webpack_require__) {

// to indexed object, toObject with fallback for non-array-like ES3 strings
var IObject = __webpack_require__(52)
  , defined = __webpack_require__(35);
module.exports = function(it){
  return IObject(defined(it));
};

/***/ }),

/***/ 24:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

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

var $ = window.$;

/**
 * Class is responsible for handling Grid events
 */

var Grid = function () {
  /**
   * Grid id
   *
   * @param {string} id
   */
  function Grid(id) {
    (0, _classCallCheck3.default)(this, Grid);

    this.id = id;
    this.$container = $('#' + this.id + '_grid');
  }

  /**
   * Get grid id
   *
   * @returns {string}
   */


  (0, _createClass3.default)(Grid, [{
    key: 'getId',
    value: function getId() {
      return this.id;
    }

    /**
     * Get grid container
     *
     * @returns {jQuery}
     */

  }, {
    key: 'getContainer',
    value: function getContainer() {
      return this.$container;
    }

    /**
     * Get grid header container
     *
     * @returns {jQuery}
     */

  }, {
    key: 'getHeaderContainer',
    value: function getHeaderContainer() {
      return this.$container.closest('.js-grid-panel').find('.js-grid-header');
    }

    /**
     * Extend grid with external extensions
     *
     * @param {object} extension
     */

  }, {
    key: 'addExtension',
    value: function addExtension(extension) {
      extension.extend(this);
    }
  }]);
  return Grid;
}();

exports.default = Grid;

/***/ }),

/***/ 25:
/***/ (function(module, exports) {

var hasOwnProperty = {}.hasOwnProperty;
module.exports = function(it, key){
  return hasOwnProperty.call(it, key);
};

/***/ }),

/***/ 26:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _reset_search = __webpack_require__(43);

var _reset_search2 = _interopRequireDefault(_reset_search);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * Class FiltersResetExtension extends grid with filters resetting
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

var FiltersResetExtension = function () {
  function FiltersResetExtension() {
    (0, _classCallCheck3.default)(this, FiltersResetExtension);
  }

  (0, _createClass3.default)(FiltersResetExtension, [{
    key: 'extend',


    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-reset-search', function (event) {
        (0, _reset_search2.default)($(event.currentTarget).data('url'), $(event.currentTarget).data('redirect'));
      });
    }
  }]);
  return FiltersResetExtension;
}();

exports.default = FiltersResetExtension;

/***/ }),

/***/ 27:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _tableSorting = __webpack_require__(41);

var _tableSorting2 = _interopRequireDefault(_tableSorting);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */
var SortingExtension = function () {
  function SortingExtension() {
    (0, _classCallCheck3.default)(this, SortingExtension);
  }

  (0, _createClass3.default)(SortingExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var $sortableTable = grid.getContainer().find('table.table');

      new _tableSorting2.default($sortableTable).attach();
    }
  }]);
  return SortingExtension;
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

exports.default = SortingExtension;

/***/ }),

/***/ 28:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

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

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */
var ReloadListExtension = function () {
  function ReloadListExtension() {
    (0, _classCallCheck3.default)(this, ReloadListExtension);
  }

  (0, _createClass3.default)(ReloadListExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getHeaderContainer().on('click', '.js-common_refresh_list-grid-action', function () {
        location.reload();
      });
    }
  }]);
  return ReloadListExtension;
}();

exports.default = ReloadListExtension;

/***/ }),

/***/ 29:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

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

var $ = window.$;

/**
 * Class ExportToSqlManagerExtension extends grid with exporting query to SQL Manager
 */

var ExportToSqlManagerExtension = function () {
  function ExportToSqlManagerExtension() {
    (0, _classCallCheck3.default)(this, ExportToSqlManagerExtension);
  }

  (0, _createClass3.default)(ExportToSqlManagerExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var _this = this;

      grid.getHeaderContainer().on('click', '.js-common_show_query-grid-action', function () {
        return _this._onShowSqlQueryClick(grid);
      });
      grid.getHeaderContainer().on('click', '.js-common_export_sql_manager-grid-action', function () {
        return _this._onExportSqlManagerClick(grid);
      });
    }

    /**
     * Invoked when clicking on the "show sql query" toolbar button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_onShowSqlQueryClick',
    value: function _onShowSqlQueryClick(grid) {
      var $sqlManagerForm = $('#' + grid.getId() + '_common_show_query_modal_form');
      this._fillExportForm($sqlManagerForm, grid);

      var $modal = $('#' + grid.getId() + '_grid_common_show_query_modal');
      $modal.modal('show');

      $modal.on('click', '.btn-sql-submit', function () {
        return $sqlManagerForm.submit();
      });
    }

    /**
     * Invoked when clicking on the "export to the sql query" toolbar button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_onExportSqlManagerClick',
    value: function _onExportSqlManagerClick(grid) {
      var $sqlManagerForm = $('#' + grid.getId() + '_common_show_query_modal_form');

      this._fillExportForm($sqlManagerForm, grid);

      $sqlManagerForm.submit();
    }

    /**
     * Fill export form with SQL and it's name
     *
     * @param {jQuery} $sqlManagerForm
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_fillExportForm',
    value: function _fillExportForm($sqlManagerForm, grid) {
      var query = grid.getContainer().find('.js-grid-table').data('query');

      $sqlManagerForm.find('textarea[name="sql"]').val(query);
      $sqlManagerForm.find('input[name="name"]').val(this._getNameFromBreadcrumb());
    }

    /**
     * Get export name from page's breadcrumb
     *
     * @return {String}
     *
     * @private
     */

  }, {
    key: '_getNameFromBreadcrumb',
    value: function _getNameFromBreadcrumb() {
      var $breadcrumbs = $('.header-toolbar').find('.breadcrumb-item');
      var name = '';

      $breadcrumbs.each(function (i, item) {
        var $breadcrumb = $(item);

        var breadcrumbTitle = 0 < $breadcrumb.find('a').length ? $breadcrumb.find('a').text() : $breadcrumb.text();

        if (0 < name.length) {
          name = name.concat(' > ');
        }

        name = name.concat(breadcrumbTitle);
      });

      return name;
    }
  }]);
  return ExportToSqlManagerExtension;
}();

exports.default = ExportToSqlManagerExtension;

/***/ }),

/***/ 3:
/***/ (function(module, exports) {

var core = module.exports = {version: '2.4.0'};
if(typeof __e == 'number')__e = core; // eslint-disable-line no-undef

/***/ }),

/***/ 30:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

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

/**
 * Responsible for grid filters search and reset button availability when filter inputs changes.
 */
var FiltersSubmitButtonEnablerExtension = function () {
  function FiltersSubmitButtonEnablerExtension() {
    (0, _classCallCheck3.default)(this, FiltersSubmitButtonEnablerExtension);
  }

  (0, _createClass3.default)(FiltersSubmitButtonEnablerExtension, [{
    key: 'extend',


    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var $filtersRow = grid.getContainer().find('.column-filters');
      $filtersRow.find('.grid-search-button').prop('disabled', true);

      $filtersRow.find('input:not(.js-bulk-action-select-all), select').on('input dp.change', function () {
        $filtersRow.find('.grid-search-button').prop('disabled', false);
        $filtersRow.find('.js-grid-reset-button').prop('hidden', false);
      });
    }
  }]);
  return FiltersSubmitButtonEnablerExtension;
}();

exports.default = FiltersSubmitButtonEnablerExtension;

/***/ }),

/***/ 31:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

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

var $ = window.$;

/**
 * Class BulkActionSelectCheckboxExtension
 */

var BulkActionCheckboxExtension = function () {
  function BulkActionCheckboxExtension() {
    (0, _classCallCheck3.default)(this, BulkActionCheckboxExtension);
  }

  (0, _createClass3.default)(BulkActionCheckboxExtension, [{
    key: 'extend',

    /**
     * Extend grid with bulk action checkboxes handling functionality
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      this._handleBulkActionCheckboxSelect(grid);
      this._handleBulkActionSelectAllCheckbox(grid);
    }

    /**
     * Handles "Select all" button in the grid
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_handleBulkActionSelectAllCheckbox',
    value: function _handleBulkActionSelectAllCheckbox(grid) {
      var _this = this;

      grid.getContainer().on('change', '.js-bulk-action-select-all', function (e) {
        var $checkbox = $(e.currentTarget);

        var isChecked = $checkbox.is(':checked');
        if (isChecked) {
          _this._enableBulkActionsBtn(grid);
        } else {
          _this._disableBulkActionsBtn(grid);
        }

        grid.getContainer().find('.js-bulk-action-checkbox').prop('checked', isChecked);
      });
    }

    /**
     * Handles each bulk action checkbox select in the grid
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_handleBulkActionCheckboxSelect',
    value: function _handleBulkActionCheckboxSelect(grid) {
      var _this2 = this;

      grid.getContainer().on('change', '.js-bulk-action-checkbox', function () {
        var checkedRowsCount = grid.getContainer().find('.js-bulk-action-checkbox:checked').length;

        if (checkedRowsCount > 0) {
          _this2._enableBulkActionsBtn(grid);
        } else {
          _this2._disableBulkActionsBtn(grid);
        }
      });
    }

    /**
     * Enable bulk actions button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_enableBulkActionsBtn',
    value: function _enableBulkActionsBtn(grid) {
      grid.getContainer().find('.js-bulk-actions-btn').prop('disabled', false);
    }

    /**
     * Disable bulk actions button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_disableBulkActionsBtn',
    value: function _disableBulkActionsBtn(grid) {
      grid.getContainer().find('.js-bulk-actions-btn').prop('disabled', true);
    }
  }]);
  return BulkActionCheckboxExtension;
}();

exports.default = BulkActionCheckboxExtension;

/***/ }),

/***/ 32:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _modal = __webpack_require__(47);

var _modal2 = _interopRequireDefault(_modal);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * Handles submit of grid actions
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

var SubmitBulkActionExtension = function () {
  function SubmitBulkActionExtension() {
    var _this = this;

    (0, _classCallCheck3.default)(this, SubmitBulkActionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }

  /**
   * Extend grid with bulk action submitting
   *
   * @param {Grid} grid
   */


  (0, _createClass3.default)(SubmitBulkActionExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this2 = this;

      grid.getContainer().on('click', '.js-bulk-action-submit-btn', function (event) {
        _this2.submit(event, grid);
      });
    }

    /**
     * Handle bulk action submitting
     *
     * @param {Event} event
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: 'submit',
    value: function submit(event, grid) {
      var $submitBtn = $(event.currentTarget);
      var confirmMessage = $submitBtn.data('confirm-message');
      var confirmTitle = $submitBtn.data('confirmTitle');

      if (confirmMessage !== undefined && 0 < confirmMessage.length) {
        if (confirmTitle !== undefined) {
          this.showConfirmModal($submitBtn, grid, confirmMessage, confirmTitle);
        } else if (confirm(confirmMessage)) {
          this.postForm($submitBtn, grid);
        }
      } else {
        this.postForm($submitBtn, grid);
      }
    }

    /**
     * @param {jQuery} $submitBtn
     * @param {Grid} grid
     * @param {string} confirmMessage
     * @param {string} confirmTitle
     */

  }, {
    key: 'showConfirmModal',
    value: function showConfirmModal($submitBtn, grid, confirmMessage, confirmTitle) {
      var _this3 = this;

      var confirmButtonLabel = $submitBtn.data('confirmButtonLabel');
      var closeButtonLabel = $submitBtn.data('closeButtonLabel');
      var confirmButtonClass = $submitBtn.data('confirmButtonClass');

      var modal = new _modal2.default({
        id: grid.getId() + '_grid_confirm_modal',
        confirmTitle: confirmTitle,
        confirmMessage: confirmMessage,
        confirmButtonLabel: confirmButtonLabel,
        closeButtonLabel: closeButtonLabel,
        confirmButtonClass: confirmButtonClass
      }, function () {
        return _this3.postForm($submitBtn, grid);
      });

      modal.show();
    }

    /**
     * @param {jQuery} $submitBtn
     * @param {Grid} grid
     */

  }, {
    key: 'postForm',
    value: function postForm($submitBtn, grid) {
      var $form = $('#' + grid.getId() + '_filter_form');

      $form.attr('action', $submitBtn.data('form-url'));
      $form.attr('method', $submitBtn.data('form-method'));
      $form.submit();
    }
  }]);
  return SubmitBulkActionExtension;
}();

exports.default = SubmitBulkActionExtension;

/***/ }),

/***/ 33:
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 / 15.2.3.14 Object.keys(O)
var $keys       = __webpack_require__(55)
  , enumBugKeys = __webpack_require__(49);

module.exports = Object.keys || function keys(O){
  return $keys(O, enumBugKeys);
};

/***/ }),

/***/ 34:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

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

var $ = window.$;

/**
 * Class LinkRowActionExtension handles link row actions
 */

var LinkRowActionExtension = function () {
  function LinkRowActionExtension() {
    (0, _classCallCheck3.default)(this, LinkRowActionExtension);
  }

  (0, _createClass3.default)(LinkRowActionExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      this.initRowLinks(grid);
      this.initConfirmableActions(grid);
    }

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */

  }, {
    key: 'initConfirmableActions',
    value: function initConfirmableActions(grid) {
      grid.getContainer().on('click', '.js-link-row-action', function (event) {
        var confirmMessage = $(event.currentTarget).data('confirm-message');

        if (confirmMessage.length && !confirm(confirmMessage)) {
          event.preventDefault();
        }
      });
    }

    /**
     * Add a click event on rows that matches the first link action (if present)
     *
     * @param {Grid} grid
     */

  }, {
    key: 'initRowLinks',
    value: function initRowLinks(grid) {
      $('tr', grid.getContainer()).each(function initEachRow() {
        var $parentRow = $(this);

        $('.js-link-row-action[data-clickable-row=1]:first', $parentRow).each(function propagateFirstLinkAction() {
          var $rowAction = $(this);
          var $parentCell = $rowAction.closest('td');

          var clickableCells = $('td.clickable', $parentRow).not($parentCell);

          clickableCells.addClass('cursor-pointer').click(function () {
            var confirmMessage = $rowAction.data('confirm-message');

            if (!confirmMessage.length || confirm(confirmMessage)) {
              document.location = $rowAction.attr('href');
            }
          });
        });
      });
    }
  }]);
  return LinkRowActionExtension;
}();

exports.default = LinkRowActionExtension;

/***/ }),

/***/ 35:
/***/ (function(module, exports) {

// 7.2.1 RequireObjectCoercible(argument)
module.exports = function(it){
  if(it == undefined)throw TypeError("Can't call method on  " + it);
  return it;
};

/***/ }),

/***/ 36:
/***/ (function(module, exports) {

// 7.1.4 ToInteger
var ceil  = Math.ceil
  , floor = Math.floor;
module.exports = function(it){
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};

/***/ }),

/***/ 389:
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

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * Class BulkOpenTabsExtension
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

var BulkOpenTabsExtension = function () {
  function BulkOpenTabsExtension() {
    var _this = this;

    (0, _classCallCheck3.default)(this, BulkOpenTabsExtension);

    this.router = new _router2.default();
    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }

  /**
   * Extend grid with bulk action open tabs
   *
   * @param {Grid} grid
   */


  (0, _createClass3.default)(BulkOpenTabsExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this2 = this;

      grid.getContainer().on('click', '.js-bulk-action-btn.open_tabs', function (event) {
        _this2.openTabs(event, grid);
      });
    }

    /**
     * Handle bulk action opening tabs
     *
     * @param {Event} event
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: 'openTabs',
    value: function openTabs(event, grid) {
      var _this3 = this;

      var $submitBtn = $(event.currentTarget);
      var route = $submitBtn.data('route');
      var routeParamName = $submitBtn.data('routeParamName');
      var tabsBlockedMessage = $submitBtn.data('tabsBlockedMessage');

      var $checkboxes = grid.getContainer().find('.js-bulk-action-checkbox:checked');
      var allTabsOpened = true;
      $checkboxes.each(function (i, element) {
        var $checkbox = $(element);
        var routeParams = {};
        routeParams[routeParamName] = $checkbox.val();

        var handle = window.open(_this3.router.generate(route, routeParams));
        if (handle) {
          handle.blur();
          window.focus();
        } else {
          allTabsOpened = false;
        }

        if (!allTabsOpened) {
          alert(tabsBlockedMessage);
        }
      });
    }
  }]);
  return BulkOpenTabsExtension;
}();

exports.default = BulkOpenTabsExtension;

/***/ }),

/***/ 390:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

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

var $ = global.$;

/**
 * This extension enables submit functionality of the choice fields in grid.
 *
 * Usage of the extension:
 *
 * const myGrid = new Grid('myGrid');
 * myGrid.addExtension(new ChoiceExtension());
 *
 */

var ChoiceExtension = function () {
  function ChoiceExtension() {
    (0, _classCallCheck3.default)(this, ChoiceExtension);

    this.lock = [];
  }

  (0, _createClass3.default)(ChoiceExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this = this;

      var $choiceOptionsContainer = grid.getContainer().find('table.table .js-choice-options');

      $choiceOptionsContainer.find('.js-dropdown-item').on('click', function (e) {
        e.preventDefault();
        var $button = $(e.currentTarget);
        var $parent = $button.closest('.js-choice-options');
        var url = $parent.data('url');

        _this._submitForm(url, $button);
      });
    }

    /**
     * Submits the form.
     * @param {string} url
     * @param {jQuery} $button
     * @private
     */

  }, {
    key: '_submitForm',
    value: function _submitForm(url, $button) {
      var selectedStatusId = $button.data('value');

      if (this._isLocked(url)) {
        return;
      }

      var $form = $('<form>', {
        action: url,
        method: 'POST'
      }).append($('<input>', {
        name: 'value',
        value: selectedStatusId,
        type: 'hidden'
      }));

      $form.appendTo('body');
      $form.submit();

      this._lock(url);
    }

    /**
     * Checks if current url is being used at the moment.
     *
     * @param url
     * @return {boolean}
     *
     * @private
     */

  }, {
    key: '_isLocked',
    value: function _isLocked(url) {
      return this.lock.includes(url);
    }

    /**
     * Locks the current url so it cant be used twice to execute same request
     * @param url
     * @private
     */

  }, {
    key: '_lock',
    value: function _lock(url) {
      this.lock.push(url);
    }
  }]);
  return ChoiceExtension;
}();

exports.default = ChoiceExtension;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(9)))

/***/ }),

/***/ 392:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

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

var $ = window.$;

/**
 * Allows submitting form inside modals.
 * Form must be inside modal, see example structure below:
 *
 * <div class="modal" id="uniqueModalId">
 *  <form data-bulk-inputs-id="bulkInputs">
 *    <div class="d-none">
 *      <div id="bulkInputs" data-prototype="<input type="hidden" name="__name__"/>"></div>
 *    </div>
 *  </form>
 * </div>
 *
 * Note that "data-prototype" is required to add checked items to the form. "__name__"
 * will be replaced with value of bulk checkbox.
 */

var ModalFormSubmitExtension = function () {
  function ModalFormSubmitExtension() {
    (0, _classCallCheck3.default)(this, ModalFormSubmitExtension);
  }

  (0, _createClass3.default)(ModalFormSubmitExtension, [{
    key: 'extend',
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-bulk-modal-form-submit-btn', function (event) {
        var modalId = $(event.target).data('modal-id');

        var $modal = $('#' + modalId);
        $modal.modal('show');

        $modal.find('.js-submit-modal-form-btn').on('click', function () {
          var $form = $modal.find('form');
          var $bulkInputsBlock = $form.find('#' + $form.data('bulk-inputs-id'));
          var $checkboxes = grid.getContainer().find('.js-bulk-action-checkbox:checked');

          $checkboxes.each(function (i, element) {
            var $checkbox = $(element);

            var input = $bulkInputsBlock.data('prototype').replace(/__name__/g, $checkbox.val());

            var $input = $($.parseHTML(input)[0]);
            $input.val($checkbox.val());

            $form.append($input);
          });

          $form.submit();
        });
      });
    }
  }]);
  return ModalFormSubmitExtension;
}();

exports.default = ModalFormSubmitExtension;

/***/ }),

/***/ 393:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

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

var $ = window.$;

/**
 * Extends grid with preview functionality.
 */

var PreviewExtension = function () {
  function PreviewExtension(previewCustomization) {
    (0, _classCallCheck3.default)(this, PreviewExtension);

    this.lock = [];
    this.expandSelector = '.js-expand';
    this.collapseSelector = '.js-collapse';
    this.previewOpenClass = 'preview-open';
    this.previewToggleSelector = '.preview-toggle';
    this.previewCustomization = previewCustomization;
  }

  /**
   * Extends provided grid with preview functionality
   *
   * @param grid
   */


  (0, _createClass3.default)(PreviewExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this = this;

      this.$gridContainer = $(grid.getContainer);

      this.$gridContainer.find('tbody tr').on('mouseover mouseleave', function (event) {
        return _this._handleIconHovering(event);
      });
      this.$gridContainer.find(this.previewToggleSelector).on('click', function (event) {
        return _this._togglePreview(event);
      });
    }

    /**
     * Shows/hides preview toggling icons
     *
     * @param event
     * @private
     */

  }, {
    key: '_handleIconHovering',
    value: function _handleIconHovering(event) {
      var $previewToggle = $(event.currentTarget).find(this.previewToggleSelector);

      if (event.type === 'mouseover' && !$(event.currentTarget).hasClass(this.previewOpenClass)) {
        this._showExpandIcon($previewToggle);
      } else {
        this._hideExpandIcon($previewToggle);
      }
    }

    /**
     * Shows/hides preview
     *
     * @param event
     * @private
     */

  }, {
    key: '_togglePreview',
    value: function _togglePreview(event) {
      var _this2 = this;

      var $previewToggle = $(event.currentTarget);
      var $columnRow = $previewToggle.closest('tr');

      if ($columnRow.hasClass(this.previewOpenClass)) {
        $columnRow.next('.preview-row').remove();
        $columnRow.removeClass(this.previewOpenClass);
        this._showExpandIcon($columnRow);
        this._hideCollapseIcon($columnRow);

        return;
      }

      this._closeOpenedPreviews();

      var dataUrl = $(event.currentTarget).data('preview-data-url');

      if (this._isLocked(dataUrl)) {
        return;
      }

      // Prevents loading preview multiple times.
      // Uses "dataUrl" as lock key.
      this._lock(dataUrl);

      $.ajax({
        url: dataUrl,
        method: 'GET',
        dataType: 'json',
        complete: function complete() {
          _this2._unlock(dataUrl);
        }
      }).then(function (response) {
        _this2._renderPreviewContent($columnRow, response.preview);
      }).catch(function (e) {
        showErrorMessage(e.responseJSON.message);
      });
    }

    /**
     * Renders preview content
     *
     * @param $columnRow
     * @param content
     *
     * @private
     */

  }, {
    key: '_renderPreviewContent',
    value: function _renderPreviewContent($columnRow, content) {
      var rowColumnCount = $columnRow.find('td').length;

      var $previewTemplate = $('\n        <tr class="preview-row">\n          <td colspan="' + rowColumnCount + '">' + content + '</td>\n        </tr>\n      ');

      $columnRow.addClass(this.previewOpenClass);
      this._showCollapseIcon($columnRow);
      this._hideExpandIcon($columnRow);

      if (typeof this.previewCustomization === 'function') {
        this.previewCustomization($previewTemplate);
      }

      $columnRow.after($previewTemplate);
    }

    /**
     * Shows preview expanding icon
     *
     * @param parent
     * @private
     */

  }, {
    key: '_showExpandIcon',
    value: function _showExpandIcon(parent) {
      parent.find(this.expandSelector).removeClass('d-none');
    }

    /**
     * Hides preview expanding icon
     *
     * @param parent
     * @private
     */

  }, {
    key: '_hideExpandIcon',
    value: function _hideExpandIcon(parent) {
      parent.find(this.expandSelector).addClass('d-none');
    }

    /**
     * Shows preview collapsing icon
     *
     * @param parent
     * @private
     */

  }, {
    key: '_showCollapseIcon',
    value: function _showCollapseIcon(parent) {
      parent.find(this.collapseSelector).removeClass('d-none');
    }

    /**
     * Hides preview collapsing icon
     *
     * @param parent
     * @private
     */

  }, {
    key: '_hideCollapseIcon',
    value: function _hideCollapseIcon(parent) {
      parent.find(this.collapseSelector).addClass('d-none');
    }
  }, {
    key: '_isLocked',
    value: function _isLocked(key) {
      return this.lock.indexOf(key) !== -1;
    }
  }, {
    key: '_lock',
    value: function _lock(key) {
      if (this._isLocked(key)) {
        return;
      }

      this.lock.push(key);
    }
  }, {
    key: '_unlock',
    value: function _unlock(key) {
      var index = this.lock.indexOf(key);

      if (index === -1) {
        return;
      }

      this.lock.splice(index, 1);
    }

    /**
     * Close all previews that are open.
     *
     * @private
     */

  }, {
    key: '_closeOpenedPreviews',
    value: function _closeOpenedPreviews() {
      var _this3 = this;

      var $rows = this.$gridContainer.find('.grid-table tbody').find('tr:not(.preview-row)');

      $.each($rows, function (i, row) {
        var $row = $(row);

        if (!$row.hasClass(_this3.previewOpenClass)) {
          return;
        }

        var $previewRow = $row.next();

        if (!$previewRow.hasClass('preview-row')) {
          return;
        }

        $previewRow.remove();
        $row.removeClass(_this3.previewOpenClass);
        _this3._hideCollapseIcon($row);
      });
    }
  }]);
  return PreviewExtension;
}();

exports.default = PreviewExtension;

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

module.exports = function(it){
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};

/***/ }),

/***/ 40:
/***/ (function(module, exports) {

var id = 0
  , px = Math.random();
module.exports = function(key){
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};

/***/ }),

/***/ 41:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

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

var $ = global.$;

/**
 * Makes a table sortable by columns.
 * This forces a page reload with more query parameters.
 */

var TableSorting = function () {

  /**
   * @param {jQuery} table
   */
  function TableSorting(table) {
    (0, _classCallCheck3.default)(this, TableSorting);

    this.selector = '.ps-sortable-column';
    this.columns = $(table).find(this.selector);
  }

  /**
   * Attaches the listeners
   */


  (0, _createClass3.default)(TableSorting, [{
    key: 'attach',
    value: function attach() {
      var _this = this;

      this.columns.on('click', function (e) {
        var $column = $(e.delegateTarget);
        _this._sortByColumn($column, _this._getToggledSortDirection($column));
      });
    }

    /**
     * Sort using a column name
     * @param {string} columnName
     * @param {string} direction "asc" or "desc"
     */

  }, {
    key: 'sortBy',
    value: function sortBy(columnName, direction) {
      var $column = this.columns.is('[data-sort-col-name="' + columnName + '"]');
      if (!$column) {
        throw new Error('Cannot sort by "' + columnName + '": invalid column');
      }

      this._sortByColumn($column, direction);
    }

    /**
     * Sort using a column element
     * @param {jQuery} column
     * @param {string} direction "asc" or "desc"
     * @private
     */

  }, {
    key: '_sortByColumn',
    value: function _sortByColumn(column, direction) {
      window.location = this._getUrl(column.data('sortColName'), direction === 'desc' ? 'desc' : 'asc', column.data('sortPrefix'));
    }

    /**
     * Returns the inverted direction to sort according to the column's current one
     * @param {jQuery} column
     * @return {string}
     * @private
     */

  }, {
    key: '_getToggledSortDirection',
    value: function _getToggledSortDirection(column) {
      return column.data('sortDirection') === 'asc' ? 'desc' : 'asc';
    }

    /**
     * Returns the url for the sorted table
     * @param {string} colName
     * @param {string} direction
     * @param {string} prefix
     * @return {string}
     * @private
     */

  }, {
    key: '_getUrl',
    value: function _getUrl(colName, direction, prefix) {
      var url = new URL(window.location.href);
      var params = url.searchParams;

      if (prefix) {
        params.set(prefix + '[orderBy]', colName);
        params.set(prefix + '[sortOrder]', direction);
      } else {
        params.set('orderBy', colName);
        params.set('sortOrder', direction);
      }

      return url.toString();
    }
  }]);
  return TableSorting;
}();

exports.default = TableSorting;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(9)))

/***/ }),

/***/ 421:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = previewProductsToggler;
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
 * Toggles hidden products in order preview block.
 *
 * @param {jQuery} $gridContainer
 */
function previewProductsToggler($row) {
  toggleStockLocationColumn($row);
  $row.on('click', '.js-preview-more-products-btn', function (event) {
    event.preventDefault();

    var $btn = $(event.currentTarget);
    var $hiddenProducts = $btn.closest('tbody').find('.js-product-preview-more');

    $hiddenProducts.removeClass('d-none');
    $btn.closest('tr').remove();
    toggleStockLocationColumn($row);
  });
}

function toggleStockLocationColumn($container) {
  var showColumn = false;
  $('.js-cell-product-stock-location', $container.find('tr:not(.d-none)')).filter('td').each(function (index, element) {
    if ($(element).html().trim() !== '') {
      showColumn = true;
      return false;
    }
    return true;
  });
  $('.js-cell-product-stock-location', $container).toggle(showColumn);
}

/***/ }),

/***/ 43:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

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

/**
 * Send a Post Request to reset search Action.
 */

var $ = global.$;

var init = function resetSearch(url, redirectUrl) {
  $.post(url).then(function () {
    return window.location.assign(redirectUrl);
  });
};

exports.default = init;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(9)))

/***/ }),

/***/ 44:
/***/ (function(module, exports, __webpack_require__) {

// 7.1.13 ToObject(argument)
var defined = __webpack_require__(35);
module.exports = function(it){
  return Object(defined(it));
};

/***/ }),

/***/ 45:
/***/ (function(module, exports, __webpack_require__) {

var shared = __webpack_require__(50)('keys')
  , uid    = __webpack_require__(40);
module.exports = function(key){
  return shared[key] || (shared[key] = uid(key));
};

/***/ }),

/***/ 47:
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

/***/ 48:
/***/ (function(module, exports) {

var toString = {}.toString;

module.exports = function(it){
  return toString.call(it).slice(8, -1);
};

/***/ }),

/***/ 49:
/***/ (function(module, exports) {

// IE 8- don't enum bug keys
module.exports = (
  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
).split(',');

/***/ }),

/***/ 5:
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self : Function('return this')();
if(typeof __g == 'number')__g = global; // eslint-disable-line no-undef

/***/ }),

/***/ 50:
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__(5)
  , SHARED = '__core-js_shared__'
  , store  = global[SHARED] || (global[SHARED] = {});
module.exports = function(key){
  return store[key] || (store[key] = {});
};

/***/ }),

/***/ 52:
/***/ (function(module, exports, __webpack_require__) {

// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = __webpack_require__(48);
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function(it){
  return cof(it) == 'String' ? it.split('') : Object(it);
};

/***/ }),

/***/ 523:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _grid = __webpack_require__(24);

var _grid2 = _interopRequireDefault(_grid);

var _filtersResetExtension = __webpack_require__(26);

var _filtersResetExtension2 = _interopRequireDefault(_filtersResetExtension);

var _reloadListExtension = __webpack_require__(28);

var _reloadListExtension2 = _interopRequireDefault(_reloadListExtension);

var _exportToSqlManagerExtension = __webpack_require__(29);

var _exportToSqlManagerExtension2 = _interopRequireDefault(_exportToSqlManagerExtension);

var _sortingExtension = __webpack_require__(27);

var _sortingExtension2 = _interopRequireDefault(_sortingExtension);

var _linkRowActionExtension = __webpack_require__(34);

var _linkRowActionExtension2 = _interopRequireDefault(_linkRowActionExtension);

var _submitGridActionExtension = __webpack_require__(69);

var _submitGridActionExtension2 = _interopRequireDefault(_submitGridActionExtension);

var _submitBulkActionExtension = __webpack_require__(32);

var _submitBulkActionExtension2 = _interopRequireDefault(_submitBulkActionExtension);

var _bulkActionCheckboxExtension = __webpack_require__(31);

var _bulkActionCheckboxExtension2 = _interopRequireDefault(_bulkActionCheckboxExtension);

var _filtersSubmitButtonEnablerExtension = __webpack_require__(30);

var _filtersSubmitButtonEnablerExtension2 = _interopRequireDefault(_filtersSubmitButtonEnablerExtension);

var _choiceExtension = __webpack_require__(390);

var _choiceExtension2 = _interopRequireDefault(_choiceExtension);

var _modalFormSubmitExtension = __webpack_require__(392);

var _modalFormSubmitExtension2 = _interopRequireDefault(_modalFormSubmitExtension);

var _previewExtension = __webpack_require__(393);

var _previewExtension2 = _interopRequireDefault(_previewExtension);

var _previewProductsToggler = __webpack_require__(421);

var _previewProductsToggler2 = _interopRequireDefault(_previewProductsToggler);

var _bulkOpenTabsExtension = __webpack_require__(389);

var _bulkOpenTabsExtension2 = _interopRequireDefault(_bulkOpenTabsExtension);

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
  var orderGrid = new _grid2.default('order');
  orderGrid.addExtension(new _reloadListExtension2.default());
  orderGrid.addExtension(new _exportToSqlManagerExtension2.default());
  orderGrid.addExtension(new _filtersResetExtension2.default());
  orderGrid.addExtension(new _sortingExtension2.default());
  orderGrid.addExtension(new _linkRowActionExtension2.default());
  orderGrid.addExtension(new _submitGridActionExtension2.default());
  orderGrid.addExtension(new _submitBulkActionExtension2.default());
  orderGrid.addExtension(new _bulkActionCheckboxExtension2.default());
  orderGrid.addExtension(new _filtersSubmitButtonEnablerExtension2.default());
  orderGrid.addExtension(new _modalFormSubmitExtension2.default());
  orderGrid.addExtension(new _choiceExtension2.default());
  orderGrid.addExtension(new _previewExtension2.default(_previewProductsToggler2.default));
  orderGrid.addExtension(new _bulkOpenTabsExtension2.default());
});

/***/ }),

/***/ 53:
/***/ (function(module, exports, __webpack_require__) {

// 7.1.15 ToLength
var toInteger = __webpack_require__(36)
  , min       = Math.min;
module.exports = function(it){
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};

/***/ }),

/***/ 54:
/***/ (function(module, exports) {

exports.f = {}.propertyIsEnumerable;

/***/ }),

/***/ 55:
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

/***/ 57:
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

/***/ 58:
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__(36)
  , max       = Math.max
  , min       = Math.min;
module.exports = function(index, length){
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};

/***/ }),

/***/ 59:
/***/ (function(module, exports) {

exports.f = Object.getOwnPropertySymbols;

/***/ }),

/***/ 6:
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

/***/ 65:
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

/***/ 69:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

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

var $ = window.$;

/**
 * Class SubmitGridActionExtension handles grid action submits
 */

var SubmitGridActionExtension = function () {
  function SubmitGridActionExtension() {
    var _this = this;

    (0, _classCallCheck3.default)(this, SubmitGridActionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }

  (0, _createClass3.default)(SubmitGridActionExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this2 = this;

      grid.getHeaderContainer().on('click', '.js-grid-action-submit-btn', function (event) {
        _this2.handleSubmit(event, grid);
      });
    }

    /**
     * Handle grid action submit.
     * It uses grid form to submit actions.
     *
     * @param {Event} event
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: 'handleSubmit',
    value: function handleSubmit(event, grid) {
      var $submitBtn = $(event.currentTarget);
      var confirmMessage = $submitBtn.data('confirm-message');

      if (typeof confirmMessage !== "undefined" && 0 < confirmMessage.length && !confirm(confirmMessage)) {
        return;
      }

      var $form = $('#' + grid.getId() + '_filter_form');

      $form.attr('action', $submitBtn.data('url'));
      $form.attr('method', $submitBtn.data('method'));
      $form.find('input[name="' + grid.getId() + '[_token]"]').val($submitBtn.data('csrf'));
      $form.submit();
    }
  }]);
  return SubmitGridActionExtension;
}();

exports.default = SubmitGridActionExtension;

/***/ }),

/***/ 7:
/***/ (function(module, exports) {

module.exports = function(exec){
  try {
    return !!exec();
  } catch(e){
    return true;
  }
};

/***/ }),

/***/ 8:
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

/***/ 83:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(84), __esModule: true };

/***/ }),

/***/ 84:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(89);
module.exports = __webpack_require__(3).Object.assign;

/***/ }),

/***/ 87:
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

/***/ 89:
/***/ (function(module, exports, __webpack_require__) {

// 19.1.3.1 Object.assign(target, source)
var $export = __webpack_require__(8);

$export($export.S + $export.F, 'Object', {assign: __webpack_require__(87)});

/***/ }),

/***/ 9:
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


/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgM2E2MTdjZWQyOWViY2NiNmExZDA/MTI1MCoqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9jbGFzc0NhbGxDaGVjay5qcz8yMWFmKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9jcmVhdGVDbGFzcy5qcz8xZGZlKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oaWRlLmpzP2E2ZGEqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYW4tb2JqZWN0LmpzPzBkYTMqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fcHJvcGVydHktZGVzYy5qcz8xZTg2KioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2N0eC5qcz9jZTAwKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLXByaW1pdGl2ZS5qcz80OWE0KioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2RvbS1jcmVhdGUuanM/YWI0NCoqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9mb3NfanNfcm91dGVzLmpzb24/YTE5OSoiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faWU4LWRvbS1kZWZpbmUuanM/YmQxZioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hLWZ1bmN0aW9uLmpzP2Q1M2UqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9mb3Mtcm91dGluZy9kaXN0L3JvdXRpbmcuanM/YTM2MyoiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qcz81ZjcwKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2Rlc2NyaXB0b3JzLmpzPzcwNTEqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qcz9iN2Q4KioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5kZWZpbmUtcHJvcGVydHkuanM/YzgyYyoqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1pb2JqZWN0LmpzPzY5NDYqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9ncmlkLmpzPzgxM2EqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2hhcy5qcz9kODUwKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uLmpzPzE2ZjEqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc29ydGluZy1leHRlbnNpb24uanM/MTEzZSoiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9yZWxvYWQtbGlzdC1leHRlbnNpb24uanM/ZDNlMCoiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9leHBvcnQtdG8tc3FsLW1hbmFnZXItZXh0ZW5zaW9uLmpzP2VkMmEqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvcmUuanM/MWI2MioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtc3VibWl0LWJ1dHRvbi1lbmFibGVyLWV4dGVuc2lvbi5qcz8wMzc5KiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2J1bGstYWN0aW9uLWNoZWNrYm94LWV4dGVuc2lvbi5qcz9iMDk3KiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb24uanM/MWIxZioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWtleXMuanM/ZjViYyoqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9saW5rLXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzPzM5ZGMqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2RlZmluZWQuanM/NDVkMyoqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8taW50ZWdlci5qcz9mNjVmKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2J1bGstb3Blbi10YWJzLWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2Nob2ljZS1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9tb2RhbC1mb3JtLXN1Ym1pdC1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9wcmV2aWV3LWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pcy1vYmplY3QuanM/MjRjOCoqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL191aWQuanM/ZThjZCoqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL3V0aWxzL3RhYmxlLXNvcnRpbmcuanM/MTVkNCoiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvcHJldmlldy1wcm9kdWN0cy10b2dnbGVyLmpzIiwid2VicGFjazovLy8uL2pzL2FwcC91dGlscy9yZXNldF9zZWFyY2guanM/MWE3ZioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8tb2JqZWN0LmpzP2I1YzAqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NoYXJlZC1rZXkuanM/MmE2YyoqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9tb2RhbC5qcz83NTU4KioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvZi5qcz80OGVhKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19lbnVtLWJ1Zy1rZXlzLmpzPzc1OTgqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2dsb2JhbC5qcz83N2FhKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NoYXJlZC5qcz83YjZjKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pb2JqZWN0LmpzPzVjZjkqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2luZGV4LmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLWxlbmd0aC5qcz82MmE3KioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtcGllLmpzP2QwZDIqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWtleXMtaW50ZXJuYWwuanM/ZmNlYSoqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYXJyYXktaW5jbHVkZXMuanM/NjE5OSoqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8taW5kZXguanM/OWZkNCoqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdvcHMuanM/YTVmYioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZHAuanM/NDExNioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL3JvdXRlci5qcz80MGFjKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1ncmlkLWFjdGlvbi1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZmFpbHMuanM/OTM1ZCoqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19leHBvcnQuanM/ZWNlMioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3QvYXNzaWduLmpzP2U2Y2EqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9hc3NpZ24uanM/ODBlNCoqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWFzc2lnbi5qcz81YzBjKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuYXNzaWduLmpzPzkwMDcqKioqIiwid2VicGFjazovLy8od2VicGFjaykvYnVpbGRpbi9nbG9iYWwuanM/MzY5OCoqKioqKioqKioqIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJHcmlkIiwiaWQiLCIkY29udGFpbmVyIiwiY2xvc2VzdCIsImZpbmQiLCJleHRlbnNpb24iLCJleHRlbmQiLCJGaWx0ZXJzUmVzZXRFeHRlbnNpb24iLCJncmlkIiwiZ2V0Q29udGFpbmVyIiwib24iLCJldmVudCIsImN1cnJlbnRUYXJnZXQiLCJkYXRhIiwiU29ydGluZ0V4dGVuc2lvbiIsIiRzb3J0YWJsZVRhYmxlIiwiVGFibGVTb3J0aW5nIiwiYXR0YWNoIiwiUmVsb2FkTGlzdEV4dGVuc2lvbiIsImdldEhlYWRlckNvbnRhaW5lciIsImxvY2F0aW9uIiwicmVsb2FkIiwiRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIiwiX29uU2hvd1NxbFF1ZXJ5Q2xpY2siLCJfb25FeHBvcnRTcWxNYW5hZ2VyQ2xpY2siLCIkc3FsTWFuYWdlckZvcm0iLCJnZXRJZCIsIl9maWxsRXhwb3J0Rm9ybSIsIiRtb2RhbCIsIm1vZGFsIiwic3VibWl0IiwicXVlcnkiLCJ2YWwiLCJfZ2V0TmFtZUZyb21CcmVhZGNydW1iIiwiJGJyZWFkY3J1bWJzIiwibmFtZSIsImVhY2giLCJpIiwiaXRlbSIsIiRicmVhZGNydW1iIiwiYnJlYWRjcnVtYlRpdGxlIiwibGVuZ3RoIiwidGV4dCIsImNvbmNhdCIsIkZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uIiwiJGZpbHRlcnNSb3ciLCJwcm9wIiwiQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uIiwiX2hhbmRsZUJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdCIsIl9oYW5kbGVCdWxrQWN0aW9uU2VsZWN0QWxsQ2hlY2tib3giLCJlIiwiJGNoZWNrYm94IiwiaXNDaGVja2VkIiwiaXMiLCJfZW5hYmxlQnVsa0FjdGlvbnNCdG4iLCJfZGlzYWJsZUJ1bGtBY3Rpb25zQnRuIiwiY2hlY2tlZFJvd3NDb3VudCIsIlN1Ym1pdEJ1bGtBY3Rpb25FeHRlbnNpb24iLCIkc3VibWl0QnRuIiwiY29uZmlybU1lc3NhZ2UiLCJjb25maXJtVGl0bGUiLCJ1bmRlZmluZWQiLCJzaG93Q29uZmlybU1vZGFsIiwiY29uZmlybSIsInBvc3RGb3JtIiwiY29uZmlybUJ1dHRvbkxhYmVsIiwiY2xvc2VCdXR0b25MYWJlbCIsImNvbmZpcm1CdXR0b25DbGFzcyIsIkNvbmZpcm1Nb2RhbCIsInNob3ciLCIkZm9ybSIsImF0dHIiLCJMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIiwiaW5pdFJvd0xpbmtzIiwiaW5pdENvbmZpcm1hYmxlQWN0aW9ucyIsInByZXZlbnREZWZhdWx0IiwiaW5pdEVhY2hSb3ciLCIkcGFyZW50Um93IiwicHJvcGFnYXRlRmlyc3RMaW5rQWN0aW9uIiwiJHJvd0FjdGlvbiIsIiRwYXJlbnRDZWxsIiwiY2xpY2thYmxlQ2VsbHMiLCJub3QiLCJhZGRDbGFzcyIsImNsaWNrIiwiZG9jdW1lbnQiLCJCdWxrT3BlblRhYnNFeHRlbnNpb24iLCJyb3V0ZXIiLCJSb3V0ZXIiLCJvcGVuVGFicyIsInJvdXRlIiwicm91dGVQYXJhbU5hbWUiLCJ0YWJzQmxvY2tlZE1lc3NhZ2UiLCIkY2hlY2tib3hlcyIsImFsbFRhYnNPcGVuZWQiLCJlbGVtZW50Iiwicm91dGVQYXJhbXMiLCJoYW5kbGUiLCJvcGVuIiwiZ2VuZXJhdGUiLCJibHVyIiwiZm9jdXMiLCJhbGVydCIsImdsb2JhbCIsIkNob2ljZUV4dGVuc2lvbiIsImxvY2siLCIkY2hvaWNlT3B0aW9uc0NvbnRhaW5lciIsIiRidXR0b24iLCIkcGFyZW50IiwidXJsIiwiX3N1Ym1pdEZvcm0iLCJzZWxlY3RlZFN0YXR1c0lkIiwiX2lzTG9ja2VkIiwiYWN0aW9uIiwibWV0aG9kIiwiYXBwZW5kIiwidmFsdWUiLCJ0eXBlIiwiYXBwZW5kVG8iLCJfbG9jayIsImluY2x1ZGVzIiwicHVzaCIsIk1vZGFsRm9ybVN1Ym1pdEV4dGVuc2lvbiIsIm1vZGFsSWQiLCJ0YXJnZXQiLCIkYnVsa0lucHV0c0Jsb2NrIiwiaW5wdXQiLCJyZXBsYWNlIiwiJGlucHV0IiwicGFyc2VIVE1MIiwiUHJldmlld0V4dGVuc2lvbiIsInByZXZpZXdDdXN0b21pemF0aW9uIiwiZXhwYW5kU2VsZWN0b3IiLCJjb2xsYXBzZVNlbGVjdG9yIiwicHJldmlld09wZW5DbGFzcyIsInByZXZpZXdUb2dnbGVTZWxlY3RvciIsIiRncmlkQ29udGFpbmVyIiwiX2hhbmRsZUljb25Ib3ZlcmluZyIsIl90b2dnbGVQcmV2aWV3IiwiJHByZXZpZXdUb2dnbGUiLCJoYXNDbGFzcyIsIl9zaG93RXhwYW5kSWNvbiIsIl9oaWRlRXhwYW5kSWNvbiIsIiRjb2x1bW5Sb3ciLCJuZXh0IiwicmVtb3ZlIiwicmVtb3ZlQ2xhc3MiLCJfaGlkZUNvbGxhcHNlSWNvbiIsIl9jbG9zZU9wZW5lZFByZXZpZXdzIiwiZGF0YVVybCIsImFqYXgiLCJkYXRhVHlwZSIsImNvbXBsZXRlIiwiX3VubG9jayIsInRoZW4iLCJyZXNwb25zZSIsIl9yZW5kZXJQcmV2aWV3Q29udGVudCIsInByZXZpZXciLCJjYXRjaCIsInNob3dFcnJvck1lc3NhZ2UiLCJyZXNwb25zZUpTT04iLCJtZXNzYWdlIiwiY29udGVudCIsInJvd0NvbHVtbkNvdW50IiwiJHByZXZpZXdUZW1wbGF0ZSIsIl9zaG93Q29sbGFwc2VJY29uIiwiYWZ0ZXIiLCJwYXJlbnQiLCJrZXkiLCJpbmRleE9mIiwiaW5kZXgiLCJzcGxpY2UiLCIkcm93cyIsInJvdyIsIiRyb3ciLCIkcHJldmlld1JvdyIsInRhYmxlIiwic2VsZWN0b3IiLCJjb2x1bW5zIiwiJGNvbHVtbiIsImRlbGVnYXRlVGFyZ2V0IiwiX3NvcnRCeUNvbHVtbiIsIl9nZXRUb2dnbGVkU29ydERpcmVjdGlvbiIsImNvbHVtbk5hbWUiLCJkaXJlY3Rpb24iLCJFcnJvciIsImNvbHVtbiIsIl9nZXRVcmwiLCJjb2xOYW1lIiwicHJlZml4IiwiVVJMIiwiaHJlZiIsInBhcmFtcyIsInNlYXJjaFBhcmFtcyIsInNldCIsInRvU3RyaW5nIiwicHJldmlld1Byb2R1Y3RzVG9nZ2xlciIsInRvZ2dsZVN0b2NrTG9jYXRpb25Db2x1bW4iLCIkYnRuIiwiJGhpZGRlblByb2R1Y3RzIiwic2hvd0NvbHVtbiIsImZpbHRlciIsImh0bWwiLCJ0cmltIiwidG9nZ2xlIiwiaW5pdCIsInJlc2V0U2VhcmNoIiwicmVkaXJlY3RVcmwiLCJwb3N0IiwiYXNzaWduIiwiY29uZmlybUNhbGxiYWNrIiwiY2xvc2FibGUiLCJNb2RhbCIsImNvbnRhaW5lciIsImNvbmZpcm1CdXR0b24iLCJhZGRFdmVudExpc3RlbmVyIiwiYmFja2Ryb3AiLCJrZXlib2FyZCIsInF1ZXJ5U2VsZWN0b3IiLCJib2R5IiwiYXBwZW5kQ2hpbGQiLCJjcmVhdGVFbGVtZW50IiwiY2xhc3NMaXN0IiwiYWRkIiwiZGlhbG9nIiwiaGVhZGVyIiwidGl0bGUiLCJpbm5lckhUTUwiLCJjbG9zZUljb24iLCJzZXRBdHRyaWJ1dGUiLCJkYXRhc2V0IiwiZGlzbWlzcyIsImZvb3RlciIsImNsb3NlQnV0dG9uIiwib3JkZXJHcmlkIiwiYWRkRXh0ZW5zaW9uIiwiUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbiIsIlN1Ym1pdEdyaWRFeHRlbnNpb24iLCJTdWJtaXRCdWxrRXh0ZW5zaW9uIiwiUm91dGluZyIsInNldERhdGEiLCJyb3V0ZXMiLCJzZXRCYXNlVXJsIiwidG9rZW5pemVkUGFyYW1zIiwiX3Rva2VuIiwiU3VibWl0R3JpZEFjdGlvbkV4dGVuc2lvbiIsImhhbmRsZVN1Ym1pdCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFB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QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGtDQUFrQyxVQUFVLEVBQUU7QUFDOUMsbUJBQW1CLHNDQUFzQztBQUN6RCxDQUFDLG9DQUFvQztBQUNyQztBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0gsQ0FBQyxXOzs7Ozs7O0FDaENEO0FBQ0E7O0FBRUEsMENBQTBDLGdDQUFvQyxFOzs7Ozs7O0FDSDlFOztBQUVBO0FBQ0E7QUFDQTtBQUNBLENBQUM7O0FBRUQ7QUFDQTtBQUNBO0FBQ0EsQ0FBQztBQUNEO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSw0Q0FBNEM7O0FBRTVDIiwiZmlsZSI6Im9yZGVyLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gNTIzKTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCAzYTYxN2NlZDI5ZWJjY2I2YTFkMCIsIlwidXNlIHN0cmljdFwiO1xuXG5leHBvcnRzLl9fZXNNb2R1bGUgPSB0cnVlO1xuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoaW5zdGFuY2UsIENvbnN0cnVjdG9yKSB7XG4gIGlmICghKGluc3RhbmNlIGluc3RhbmNlb2YgQ29uc3RydWN0b3IpKSB7XG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcihcIkNhbm5vdCBjYWxsIGEgY2xhc3MgYXMgYSBmdW5jdGlvblwiKTtcbiAgfVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NsYXNzQ2FsbENoZWNrLmpzXG4vLyBtb2R1bGUgaWQgPSAwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiXCJ1c2Ugc3RyaWN0XCI7XG5cbmV4cG9ydHMuX19lc01vZHVsZSA9IHRydWU7XG5cbnZhciBfZGVmaW5lUHJvcGVydHkgPSByZXF1aXJlKFwiLi4vY29yZS1qcy9vYmplY3QvZGVmaW5lLXByb3BlcnR5XCIpO1xuXG52YXIgX2RlZmluZVByb3BlcnR5MiA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX2RlZmluZVByb3BlcnR5KTtcblxuZnVuY3Rpb24gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChvYmopIHsgcmV0dXJuIG9iaiAmJiBvYmouX19lc01vZHVsZSA/IG9iaiA6IHsgZGVmYXVsdDogb2JqIH07IH1cblxuZXhwb3J0cy5kZWZhdWx0ID0gZnVuY3Rpb24gKCkge1xuICBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0aWVzKHRhcmdldCwgcHJvcHMpIHtcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IHByb3BzLmxlbmd0aDsgaSsrKSB7XG4gICAgICB2YXIgZGVzY3JpcHRvciA9IHByb3BzW2ldO1xuICAgICAgZGVzY3JpcHRvci5lbnVtZXJhYmxlID0gZGVzY3JpcHRvci5lbnVtZXJhYmxlIHx8IGZhbHNlO1xuICAgICAgZGVzY3JpcHRvci5jb25maWd1cmFibGUgPSB0cnVlO1xuICAgICAgaWYgKFwidmFsdWVcIiBpbiBkZXNjcmlwdG9yKSBkZXNjcmlwdG9yLndyaXRhYmxlID0gdHJ1ZTtcbiAgICAgICgwLCBfZGVmaW5lUHJvcGVydHkyLmRlZmF1bHQpKHRhcmdldCwgZGVzY3JpcHRvci5rZXksIGRlc2NyaXB0b3IpO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiBmdW5jdGlvbiAoQ29uc3RydWN0b3IsIHByb3RvUHJvcHMsIHN0YXRpY1Byb3BzKSB7XG4gICAgaWYgKHByb3RvUHJvcHMpIGRlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IucHJvdG90eXBlLCBwcm90b1Byb3BzKTtcbiAgICBpZiAoc3RhdGljUHJvcHMpIGRlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IsIHN0YXRpY1Byb3BzKTtcbiAgICByZXR1cm4gQ29uc3RydWN0b3I7XG4gIH07XG59KCk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9jcmVhdGVDbGFzcy5qc1xuLy8gbW9kdWxlIGlkID0gMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBkUCAgICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWRwJylcbiAgLCBjcmVhdGVEZXNjID0gcmVxdWlyZSgnLi9fcHJvcGVydHktZGVzYycpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpID8gZnVuY3Rpb24ob2JqZWN0LCBrZXksIHZhbHVlKXtcbiAgcmV0dXJuIGRQLmYob2JqZWN0LCBrZXksIGNyZWF0ZURlc2MoMSwgdmFsdWUpKTtcbn0gOiBmdW5jdGlvbihvYmplY3QsIGtleSwgdmFsdWUpe1xuICBvYmplY3Rba2V5XSA9IHZhbHVlO1xuICByZXR1cm4gb2JqZWN0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2hpZGUuanNcbi8vIG1vZHVsZSBpZCA9IDEwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIGlmKCFpc09iamVjdChpdCkpdGhyb3cgVHlwZUVycm9yKGl0ICsgJyBpcyBub3QgYW4gb2JqZWN0IScpO1xuICByZXR1cm4gaXQ7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYW4tb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSAxMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGJpdG1hcCwgdmFsdWUpe1xuICByZXR1cm4ge1xuICAgIGVudW1lcmFibGUgIDogIShiaXRtYXAgJiAxKSxcbiAgICBjb25maWd1cmFibGU6ICEoYml0bWFwICYgMiksXG4gICAgd3JpdGFibGUgICAgOiAhKGJpdG1hcCAmIDQpLFxuICAgIHZhbHVlICAgICAgIDogdmFsdWVcbiAgfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19wcm9wZXJ0eS1kZXNjLmpzXG4vLyBtb2R1bGUgaWQgPSAxMlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyBvcHRpb25hbCAvIHNpbXBsZSBjb250ZXh0IGJpbmRpbmdcbnZhciBhRnVuY3Rpb24gPSByZXF1aXJlKCcuL19hLWZ1bmN0aW9uJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGZuLCB0aGF0LCBsZW5ndGgpe1xuICBhRnVuY3Rpb24oZm4pO1xuICBpZih0aGF0ID09PSB1bmRlZmluZWQpcmV0dXJuIGZuO1xuICBzd2l0Y2gobGVuZ3RoKXtcbiAgICBjYXNlIDE6IHJldHVybiBmdW5jdGlvbihhKXtcbiAgICAgIHJldHVybiBmbi5jYWxsKHRoYXQsIGEpO1xuICAgIH07XG4gICAgY2FzZSAyOiByZXR1cm4gZnVuY3Rpb24oYSwgYil7XG4gICAgICByZXR1cm4gZm4uY2FsbCh0aGF0LCBhLCBiKTtcbiAgICB9O1xuICAgIGNhc2UgMzogcmV0dXJuIGZ1bmN0aW9uKGEsIGIsIGMpe1xuICAgICAgcmV0dXJuIGZuLmNhbGwodGhhdCwgYSwgYiwgYyk7XG4gICAgfTtcbiAgfVxuICByZXR1cm4gZnVuY3Rpb24oLyogLi4uYXJncyAqLyl7XG4gICAgcmV0dXJuIGZuLmFwcGx5KHRoYXQsIGFyZ3VtZW50cyk7XG4gIH07XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY3R4LmpzXG4vLyBtb2R1bGUgaWQgPSAxM1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyA3LjEuMSBUb1ByaW1pdGl2ZShpbnB1dCBbLCBQcmVmZXJyZWRUeXBlXSlcbnZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpO1xuLy8gaW5zdGVhZCBvZiB0aGUgRVM2IHNwZWMgdmVyc2lvbiwgd2UgZGlkbid0IGltcGxlbWVudCBAQHRvUHJpbWl0aXZlIGNhc2Vcbi8vIGFuZCB0aGUgc2Vjb25kIGFyZ3VtZW50IC0gZmxhZyAtIHByZWZlcnJlZCB0eXBlIGlzIGEgc3RyaW5nXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0LCBTKXtcbiAgaWYoIWlzT2JqZWN0KGl0KSlyZXR1cm4gaXQ7XG4gIHZhciBmbiwgdmFsO1xuICBpZihTICYmIHR5cGVvZiAoZm4gPSBpdC50b1N0cmluZykgPT0gJ2Z1bmN0aW9uJyAmJiAhaXNPYmplY3QodmFsID0gZm4uY2FsbChpdCkpKXJldHVybiB2YWw7XG4gIGlmKHR5cGVvZiAoZm4gPSBpdC52YWx1ZU9mKSA9PSAnZnVuY3Rpb24nICYmICFpc09iamVjdCh2YWwgPSBmbi5jYWxsKGl0KSkpcmV0dXJuIHZhbDtcbiAgaWYoIVMgJiYgdHlwZW9mIChmbiA9IGl0LnRvU3RyaW5nKSA9PSAnZnVuY3Rpb24nICYmICFpc09iamVjdCh2YWwgPSBmbi5jYWxsKGl0KSkpcmV0dXJuIHZhbDtcbiAgdGhyb3cgVHlwZUVycm9yKFwiQ2FuJ3QgY29udmVydCBvYmplY3QgdG8gcHJpbWl0aXZlIHZhbHVlXCIpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLXByaW1pdGl2ZS5qc1xuLy8gbW9kdWxlIGlkID0gMTRcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi9faXMtb2JqZWN0JylcbiAgLCBkb2N1bWVudCA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpLmRvY3VtZW50XG4gIC8vIGluIG9sZCBJRSB0eXBlb2YgZG9jdW1lbnQuY3JlYXRlRWxlbWVudCBpcyAnb2JqZWN0J1xuICAsIGlzID0gaXNPYmplY3QoZG9jdW1lbnQpICYmIGlzT2JqZWN0KGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIHJldHVybiBpcyA/IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoaXQpIDoge307XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZG9tLWNyZWF0ZS5qc1xuLy8gbW9kdWxlIGlkID0gMTZcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSB7XCJiYXNlX3VybFwiOlwiXCIsXCJyb3V0ZXNcIjp7XCJhZG1pbl9wcm9kdWN0X2Zvcm1cIjp7XCJ0b2tlbnNcIjpbW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiaWRcIl0sW1widGV4dFwiLFwiL3NlbGwvY2F0YWxvZy9wcm9kdWN0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiaWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIixcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydF9ydWxlc19zZWFyY2hcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NlbGwvY2F0YWxvZy9jYXJ0LXJ1bGVzL3NlYXJjaFwiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2N1c3RvbWVyc192aWV3XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi92aWV3XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImN1c3RvbWVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvY3VzdG9tZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjdXN0b21lcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCIsXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2N1c3RvbWVyc19zZWFyY2hcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NlbGwvY3VzdG9tZXJzL3NlYXJjaFwiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2N1c3RvbWVyc19jYXJ0c1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvY2FydHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY3VzdG9tZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9jdXN0b21lcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImN1c3RvbWVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY3VzdG9tZXJzX29yZGVyc1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvb3JkZXJzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImN1c3RvbWVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvY3VzdG9tZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjdXN0b21lcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2FkZHJlc3Nlc19jcmVhdGVcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NlbGwvYWRkcmVzc2VzL25ld1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCIsXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2FkZHJlc3Nlc19lZGl0XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9lZGl0XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImFkZHJlc3NJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9hZGRyZXNzZXNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImFkZHJlc3NJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiLFwiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcl9hZGRyZXNzZXNfZWRpdFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvZWRpdFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiZGVsaXZlcnl8aW52b2ljZVwiLFwiYWRkcmVzc1R5cGVcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9hZGRyZXNzZXMvb3JkZXJcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wiLFwiYWRkcmVzc1R5cGVcIjpcImRlbGl2ZXJ5fGludm9pY2VcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCIsXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRfYWRkcmVzc2VzX2VkaXRcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2VkaXRcIl0sW1widmFyaWFibGVcIixcIi9cIixcImRlbGl2ZXJ5fGludm9pY2VcIixcImFkZHJlc3NUeXBlXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9hZGRyZXNzZXMvY2FydFwiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIixcImFkZHJlc3NUeXBlXCI6XCJkZWxpdmVyeXxpbnZvaWNlXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiLFwiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c192aWV3XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi92aWV3XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19pbmZvXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9pbmZvXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19jcmVhdGVcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzL25ld1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19lZGl0X2FkZHJlc3Nlc1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvYWRkcmVzc2VzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfZWRpdF9jYXJyaWVyXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9jYXJyaWVyXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfZWRpdF9jdXJyZW5jeVwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvY3VycmVuY3lcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19lZGl0X2xhbmd1YWdlXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9sYW5ndWFnZVwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX3NldF9kZWxpdmVyeV9zZXR0aW5nc1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvcnVsZXMvZGVsaXZlcnktc2V0dGluZ3NcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19hZGRfY2FydF9ydWxlXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9jYXJ0LXJ1bGVzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJbXi9dKytcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOltdLFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfZGVsZXRlX2NhcnRfcnVsZVwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvZGVsZXRlXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJbXi9dKytcIixcImNhcnRSdWxlSWRcIl0sW1widGV4dFwiLFwiL2NhcnQtcnVsZXNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlteL10rK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19hZGRfcHJvZHVjdFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvcHJvZHVjdHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19lZGl0X3Byb2R1Y3RfcHJpY2VcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3ByaWNlXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcInByb2R1Y3RJZFwiXSxbXCJ0ZXh0XCIsXCIvcHJvZHVjdHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIixcInByb2R1Y3RJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfZWRpdF9wcm9kdWN0X3F1YW50aXR5XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9xdWFudGl0eVwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJwcm9kdWN0SWRcIl0sW1widGV4dFwiLFwiL3Byb2R1Y3RzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCIsXCJwcm9kdWN0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2RlbGV0ZV9wcm9kdWN0XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9kZWxldGUtcHJvZHVjdFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19wbGFjZVwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvcGxhY2VcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOltdLFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3ZpZXdcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3ZpZXdcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIixcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX2R1cGxpY2F0ZV9jYXJ0XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9kdXBsaWNhdGUtY2FydFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3VwZGF0ZV9wcm9kdWN0XCI6e1widG9rZW5zXCI6W1tcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVyRGV0YWlsSWRcIl0sW1widGV4dFwiLFwiL3Byb2R1Y3RzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIixcIm9yZGVyRGV0YWlsSWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19wYXJ0aWFsX3JlZnVuZFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvcGFydGlhbC1yZWZ1bmRcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19zdGFuZGFyZF9yZWZ1bmRcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3N0YW5kYXJkLXJlZnVuZFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3JldHVybl9wcm9kdWN0XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9yZXR1cm4tcHJvZHVjdFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3NlbmRfcHJvY2Vzc19vcmRlcl9lbWFpbFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvcHJvY2Vzcy1vcmRlci1lbWFpbFwiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfYWRkX3Byb2R1Y3RcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3Byb2R1Y3RzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfZGVsZXRlX3Byb2R1Y3RcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2RlbGV0ZVwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlckRldGFpbElkXCJdLFtcInRleHRcIixcIi9wcm9kdWN0c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCIsXCJvcmRlckRldGFpbElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfZ2V0X2Rpc2NvdW50c1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvZGlzY291bnRzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19nZXRfcHJpY2VzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wcmljZXNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX2dldF9wYXltZW50c1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvcGF5bWVudHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX2dldF9wcm9kdWN0c1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvcHJvZHVjdHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX2dldF9pbnZvaWNlc1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvaW52b2ljZXNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX2dldF9kb2N1bWVudHNcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2RvY3VtZW50c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfZ2V0X3NoaXBwaW5nXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9zaGlwcGluZ1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfY2FuY2VsbGF0aW9uXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9jYW5jZWxsYXRpb25cIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19jb25maWd1cmVfcHJvZHVjdF9wYWdpbmF0aW9uXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9zZWxsL29yZGVycy9jb25maWd1cmUtcHJvZHVjdC1wYWdpbmF0aW9uXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19wcm9kdWN0X3ByaWNlc1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvcHJvZHVjdHMvcHJpY2VzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19wcm9kdWN0c19zZWFyY2hcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL3Byb2R1Y3RzL3NlYXJjaFwiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfX0sXCJwcmVmaXhcIjpcIlwiLFwiaG9zdFwiOlwibG9jYWxob3N0XCIsXCJwb3J0XCI6XCJcIixcInNjaGVtZVwiOlwiaHR0cFwiLFwibG9jYWxlXCI6W119XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9qcy9mb3NfanNfcm91dGVzLmpzb25cbi8vIG1vZHVsZSBpZCA9IDE2MlxuLy8gbW9kdWxlIGNodW5rcyA9IDMgMTAgMTMiLCJtb2R1bGUuZXhwb3J0cyA9ICFyZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpICYmICFyZXF1aXJlKCcuL19mYWlscycpKGZ1bmN0aW9uKCl7XG4gIHJldHVybiBPYmplY3QuZGVmaW5lUHJvcGVydHkocmVxdWlyZSgnLi9fZG9tLWNyZWF0ZScpKCdkaXYnKSwgJ2EnLCB7Z2V0OiBmdW5jdGlvbigpeyByZXR1cm4gNzsgfX0pLmEgIT0gNztcbn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faWU4LWRvbS1kZWZpbmUuanNcbi8vIG1vZHVsZSBpZCA9IDE3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICBpZih0eXBlb2YgaXQgIT0gJ2Z1bmN0aW9uJyl0aHJvdyBUeXBlRXJyb3IoaXQgKyAnIGlzIG5vdCBhIGZ1bmN0aW9uIScpO1xuICByZXR1cm4gaXQ7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYS1mdW5jdGlvbi5qc1xuLy8gbW9kdWxlIGlkID0gMThcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiJ3VzZSBzdHJpY3QnO3ZhciBfZXh0ZW5kcz1PYmplY3QuYXNzaWdufHxmdW5jdGlvbihhKXtmb3IodmFyIGIsYz0xO2M8YXJndW1lbnRzLmxlbmd0aDtjKyspZm9yKHZhciBkIGluIGI9YXJndW1lbnRzW2NdLGIpT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKGIsZCkmJihhW2RdPWJbZF0pO3JldHVybiBhfSxfdHlwZW9mPSdmdW5jdGlvbic9PXR5cGVvZiBTeW1ib2wmJidzeW1ib2wnPT10eXBlb2YgU3ltYm9sLml0ZXJhdG9yP2Z1bmN0aW9uKGEpe3JldHVybiB0eXBlb2YgYX06ZnVuY3Rpb24oYSl7cmV0dXJuIGEmJidmdW5jdGlvbic9PXR5cGVvZiBTeW1ib2wmJmEuY29uc3RydWN0b3I9PT1TeW1ib2wmJmEhPT1TeW1ib2wucHJvdG90eXBlPydzeW1ib2wnOnR5cGVvZiBhfTtmdW5jdGlvbiBfY2xhc3NDYWxsQ2hlY2soYSxiKXtpZighKGEgaW5zdGFuY2VvZiBiKSl0aHJvdyBuZXcgVHlwZUVycm9yKCdDYW5ub3QgY2FsbCBhIGNsYXNzIGFzIGEgZnVuY3Rpb24nKX12YXIgUm91dGluZz1mdW5jdGlvbiBhKCl7dmFyIGI9dGhpcztfY2xhc3NDYWxsQ2hlY2sodGhpcyxhKSx0aGlzLnNldFJvdXRlcz1mdW5jdGlvbihhKXtiLnJvdXRlc1JvdXRpbmc9YXx8W119LHRoaXMuZ2V0Um91dGVzPWZ1bmN0aW9uKCl7cmV0dXJuIGIucm91dGVzUm91dGluZ30sdGhpcy5zZXRCYXNlVXJsPWZ1bmN0aW9uKGEpe2IuY29udGV4dFJvdXRpbmcuYmFzZV91cmw9YX0sdGhpcy5nZXRCYXNlVXJsPWZ1bmN0aW9uKCl7cmV0dXJuIGIuY29udGV4dFJvdXRpbmcuYmFzZV91cmx9LHRoaXMuc2V0UHJlZml4PWZ1bmN0aW9uKGEpe2IuY29udGV4dFJvdXRpbmcucHJlZml4PWF9LHRoaXMuc2V0U2NoZW1lPWZ1bmN0aW9uKGEpe2IuY29udGV4dFJvdXRpbmcuc2NoZW1lPWF9LHRoaXMuZ2V0U2NoZW1lPWZ1bmN0aW9uKCl7cmV0dXJuIGIuY29udGV4dFJvdXRpbmcuc2NoZW1lfSx0aGlzLnNldEhvc3Q9ZnVuY3Rpb24oYSl7Yi5jb250ZXh0Um91dGluZy5ob3N0PWF9LHRoaXMuZ2V0SG9zdD1mdW5jdGlvbigpe3JldHVybiBiLmNvbnRleHRSb3V0aW5nLmhvc3R9LHRoaXMuYnVpbGRRdWVyeVBhcmFtcz1mdW5jdGlvbihhLGMsZCl7dmFyIGU9bmV3IFJlZ0V4cCgvXFxbXSQvKTtjIGluc3RhbmNlb2YgQXJyYXk/Yy5mb3JFYWNoKGZ1bmN0aW9uKGMsZil7ZS50ZXN0KGEpP2QoYSxjKTpiLmJ1aWxkUXVlcnlQYXJhbXMoYSsnWycrKCdvYmplY3QnPT09KCd1bmRlZmluZWQnPT10eXBlb2YgYz8ndW5kZWZpbmVkJzpfdHlwZW9mKGMpKT9mOicnKSsnXScsYyxkKX0pOidvYmplY3QnPT09KCd1bmRlZmluZWQnPT10eXBlb2YgYz8ndW5kZWZpbmVkJzpfdHlwZW9mKGMpKT9PYmplY3Qua2V5cyhjKS5mb3JFYWNoKGZ1bmN0aW9uKGUpe3JldHVybiBiLmJ1aWxkUXVlcnlQYXJhbXMoYSsnWycrZSsnXScsY1tlXSxkKX0pOmQoYSxjKX0sdGhpcy5nZXRSb3V0ZT1mdW5jdGlvbihhKXt2YXIgYz1iLmNvbnRleHRSb3V0aW5nLnByZWZpeCthO2lmKCEhYi5yb3V0ZXNSb3V0aW5nW2NdKXJldHVybiBiLnJvdXRlc1JvdXRpbmdbY107ZWxzZSBpZighYi5yb3V0ZXNSb3V0aW5nW2FdKXRocm93IG5ldyBFcnJvcignVGhlIHJvdXRlIFwiJythKydcIiBkb2VzIG5vdCBleGlzdC4nKTtyZXR1cm4gYi5yb3V0ZXNSb3V0aW5nW2FdfSx0aGlzLmdlbmVyYXRlPWZ1bmN0aW9uKGEsYyxkKXt2YXIgZT1iLmdldFJvdXRlKGEpLGY9Y3x8e30sZz1fZXh0ZW5kcyh7fSxmKSxoPSdfc2NoZW1lJyxpPScnLGo9ITAsaz0nJztpZigoZS50b2tlbnN8fFtdKS5mb3JFYWNoKGZ1bmN0aW9uKGIpe2lmKCd0ZXh0Jz09PWJbMF0pcmV0dXJuIGk9YlsxXStpLHZvaWQoaj0hMSk7aWYoJ3ZhcmlhYmxlJz09PWJbMF0pe3ZhciBjPShlLmRlZmF1bHRzfHx7fSlbYlszXV07aWYoITE9PWp8fCFjfHwoZnx8e30pW2JbM11dJiZmW2JbM11dIT09ZS5kZWZhdWx0c1tiWzNdXSl7dmFyIGQ7aWYoKGZ8fHt9KVtiWzNdXSlkPWZbYlszXV0sZGVsZXRlIGdbYlszXV07ZWxzZSBpZihjKWQ9ZS5kZWZhdWx0c1tiWzNdXTtlbHNle2lmKGopcmV0dXJuO3Rocm93IG5ldyBFcnJvcignVGhlIHJvdXRlIFwiJythKydcIiByZXF1aXJlcyB0aGUgcGFyYW1ldGVyIFwiJytiWzNdKydcIi4nKX12YXIgaD0hMD09PWR8fCExPT09ZHx8Jyc9PT1kO2lmKCFofHwhail7dmFyIGs9ZW5jb2RlVVJJQ29tcG9uZW50KGQpLnJlcGxhY2UoLyUyRi9nLCcvJyk7J251bGwnPT09ayYmbnVsbD09PWQmJihrPScnKSxpPWJbMV0raytpfWo9ITF9ZWxzZSBjJiZkZWxldGUgZ1tiWzNdXTtyZXR1cm59dGhyb3cgbmV3IEVycm9yKCdUaGUgdG9rZW4gdHlwZSBcIicrYlswXSsnXCIgaXMgbm90IHN1cHBvcnRlZC4nKX0pLCcnPT1pJiYoaT0nLycpLChlLmhvc3R0b2tlbnN8fFtdKS5mb3JFYWNoKGZ1bmN0aW9uKGEpe3ZhciBiO3JldHVybid0ZXh0Jz09PWFbMF0/dm9pZChrPWFbMV0rayk6dm9pZCgndmFyaWFibGUnPT09YVswXSYmKChmfHx7fSlbYVszXV0/KGI9ZlthWzNdXSxkZWxldGUgZ1thWzNdXSk6ZS5kZWZhdWx0c1thWzNdXSYmKGI9ZS5kZWZhdWx0c1thWzNdXSksaz1hWzFdK2IraykpfSksaT1iLmNvbnRleHRSb3V0aW5nLmJhc2VfdXJsK2ksZS5yZXF1aXJlbWVudHNbaF0mJmIuZ2V0U2NoZW1lKCkhPT1lLnJlcXVpcmVtZW50c1toXT9pPWUucmVxdWlyZW1lbnRzW2hdKyc6Ly8nKyhrfHxiLmdldEhvc3QoKSkraTprJiZiLmdldEhvc3QoKSE9PWs/aT1iLmdldFNjaGVtZSgpKyc6Ly8nK2sraTohMD09PWQmJihpPWIuZ2V0U2NoZW1lKCkrJzovLycrYi5nZXRIb3N0KCkraSksMDxPYmplY3Qua2V5cyhnKS5sZW5ndGgpe3ZhciBsPVtdLG09ZnVuY3Rpb24oYSxiKXt2YXIgYz1iO2M9J2Z1bmN0aW9uJz09dHlwZW9mIGM/YygpOmMsYz1udWxsPT09Yz8nJzpjLGwucHVzaChlbmNvZGVVUklDb21wb25lbnQoYSkrJz0nK2VuY29kZVVSSUNvbXBvbmVudChjKSl9O09iamVjdC5rZXlzKGcpLmZvckVhY2goZnVuY3Rpb24oYSl7cmV0dXJuIGIuYnVpbGRRdWVyeVBhcmFtcyhhLGdbYV0sbSl9KSxpPWkrJz8nK2wuam9pbignJicpLnJlcGxhY2UoLyUyMC9nLCcrJyl9cmV0dXJuIGl9LHRoaXMuc2V0RGF0YT1mdW5jdGlvbihhKXtiLnNldEJhc2VVcmwoYS5iYXNlX3VybCksYi5zZXRSb3V0ZXMoYS5yb3V0ZXMpLCdwcmVmaXgnaW4gYSYmYi5zZXRQcmVmaXgoYS5wcmVmaXgpLGIuc2V0SG9zdChhLmhvc3QpLGIuc2V0U2NoZW1lKGEuc2NoZW1lKX0sdGhpcy5jb250ZXh0Um91dGluZz17YmFzZV91cmw6JycscHJlZml4OicnLGhvc3Q6Jycsc2NoZW1lOicnfX07bW9kdWxlLmV4cG9ydHM9bmV3IFJvdXRpbmc7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2Zvcy1yb3V0aW5nL2Rpc3Qvcm91dGluZy5qc1xuLy8gbW9kdWxlIGlkID0gMTgwXG4vLyBtb2R1bGUgY2h1bmtzID0gMyAxMCAxMyIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvZGVmaW5lLXByb3BlcnR5XCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3QvZGVmaW5lLXByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAxOVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyBUaGFuaydzIElFOCBmb3IgaGlzIGZ1bm55IGRlZmluZVByb3BlcnR5XG5tb2R1bGUuZXhwb3J0cyA9ICFyZXF1aXJlKCcuL19mYWlscycpKGZ1bmN0aW9uKCl7XG4gIHJldHVybiBPYmplY3QuZGVmaW5lUHJvcGVydHkoe30sICdhJywge2dldDogZnVuY3Rpb24oKXsgcmV0dXJuIDc7IH19KS5hICE9IDc7XG59KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2Rlc2NyaXB0b3JzLmpzXG4vLyBtb2R1bGUgaWQgPSAyXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM2Lm9iamVjdC5kZWZpbmUtcHJvcGVydHknKTtcbnZhciAkT2JqZWN0ID0gcmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9fY29yZScpLk9iamVjdDtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24gZGVmaW5lUHJvcGVydHkoaXQsIGtleSwgZGVzYyl7XG4gIHJldHVybiAkT2JqZWN0LmRlZmluZVByb3BlcnR5KGl0LCBrZXksIGRlc2MpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanNcbi8vIG1vZHVsZSBpZCA9IDIwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciAkZXhwb3J0ID0gcmVxdWlyZSgnLi9fZXhwb3J0Jyk7XG4vLyAxOS4xLjIuNCAvIDE1LjIuMy42IE9iamVjdC5kZWZpbmVQcm9wZXJ0eShPLCBQLCBBdHRyaWJ1dGVzKVxuJGV4cG9ydCgkZXhwb3J0LlMgKyAkZXhwb3J0LkYgKiAhcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSwgJ09iamVjdCcsIHtkZWZpbmVQcm9wZXJ0eTogcmVxdWlyZSgnLi9fb2JqZWN0LWRwJykuZn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LmRlZmluZS1wcm9wZXJ0eS5qc1xuLy8gbW9kdWxlIGlkID0gMjFcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLy8gdG8gaW5kZXhlZCBvYmplY3QsIHRvT2JqZWN0IHdpdGggZmFsbGJhY2sgZm9yIG5vbi1hcnJheS1saWtlIEVTMyBzdHJpbmdzXG52YXIgSU9iamVjdCA9IHJlcXVpcmUoJy4vX2lvYmplY3QnKVxuICAsIGRlZmluZWQgPSByZXF1aXJlKCcuL19kZWZpbmVkJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIElPYmplY3QoZGVmaW5lZChpdCkpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLWlvYmplY3QuanNcbi8vIG1vZHVsZSBpZCA9IDIyXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgaXMgcmVzcG9uc2libGUgZm9yIGhhbmRsaW5nIEdyaWQgZXZlbnRzXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEdyaWQge1xuICAvKipcbiAgICogR3JpZCBpZFxuICAgKlxuICAgKiBAcGFyYW0ge3N0cmluZ30gaWRcbiAgICovXG4gIGNvbnN0cnVjdG9yKGlkKSB7XG4gICAgdGhpcy5pZCA9IGlkO1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQoJyMnICsgdGhpcy5pZCArICdfZ3JpZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGlkXG4gICAqXG4gICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAqL1xuICBnZXRJZCgpIHtcbiAgICByZXR1cm4gdGhpcy5pZDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZ3JpZCBjb250YWluZXJcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGdldENvbnRhaW5lcigpIHtcbiAgICByZXR1cm4gdGhpcy4kY29udGFpbmVyO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGhlYWRlciBjb250YWluZXJcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGdldEhlYWRlckNvbnRhaW5lcigpIHtcbiAgICByZXR1cm4gdGhpcy4kY29udGFpbmVyLmNsb3Nlc3QoJy5qcy1ncmlkLXBhbmVsJykuZmluZCgnLmpzLWdyaWQtaGVhZGVyJyk7XG4gIH1cblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBleHRlcm5hbCBleHRlbnNpb25zXG4gICAqXG4gICAqIEBwYXJhbSB7b2JqZWN0fSBleHRlbnNpb25cbiAgICovXG4gIGFkZEV4dGVuc2lvbihleHRlbnNpb24pIHtcbiAgICBleHRlbnNpb24uZXh0ZW5kKHRoaXMpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZ3JpZC5qcyIsInZhciBoYXNPd25Qcm9wZXJ0eSA9IHt9Lmhhc093blByb3BlcnR5O1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCwga2V5KXtcbiAgcmV0dXJuIGhhc093blByb3BlcnR5LmNhbGwoaXQsIGtleSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faGFzLmpzXG4vLyBtb2R1bGUgaWQgPSAyNVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IHJlc2V0U2VhcmNoIGZyb20gJ0BhcHAvdXRpbHMvcmVzZXRfc2VhcmNoJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEZpbHRlcnNSZXNldEV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBmaWx0ZXJzIHJlc2V0dGluZ1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24ge1xuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLXJlc2V0LXNlYXJjaCcsIChldmVudCkgPT4ge1xuICAgICAgcmVzZXRTZWFyY2goJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCd1cmwnKSwgJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdyZWRpcmVjdCcpKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbi5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IFRhYmxlU29ydGluZyBmcm9tICdAYXBwL3V0aWxzL3RhYmxlLXNvcnRpbmcnO1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJMaXN0IHJlbG9hZFwiIGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTb3J0aW5nRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkc29ydGFibGVUYWJsZSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgndGFibGUudGFibGUnKTtcblxuICAgIG5ldyBUYWJsZVNvcnRpbmcoJHNvcnRhYmxlVGFibGUpLmF0dGFjaCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJMaXN0IHJlbG9hZFwiIGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX3JlZnJlc2hfbGlzdC1ncmlkLWFjdGlvbicsICgpID0+IHtcbiAgICAgIGxvY2F0aW9uLnJlbG9hZCgpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbi5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBleHBvcnRpbmcgcXVlcnkgdG8gU1FMIE1hbmFnZXJcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX3Nob3dfcXVlcnktZ3JpZC1hY3Rpb24nLCAoKSA9PiB0aGlzLl9vblNob3dTcWxRdWVyeUNsaWNrKGdyaWQpKTtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX2V4cG9ydF9zcWxfbWFuYWdlci1ncmlkLWFjdGlvbicsICgpID0+IHRoaXMuX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrKGdyaWQpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbnZva2VkIHdoZW4gY2xpY2tpbmcgb24gdGhlIFwic2hvdyBzcWwgcXVlcnlcIiB0b29sYmFyIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vblNob3dTcWxRdWVyeUNsaWNrKGdyaWQpIHtcbiAgICBjb25zdCAkc3FsTWFuYWdlckZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWxfZm9ybScpO1xuICAgIHRoaXMuX2ZpbGxFeHBvcnRGb3JtKCRzcWxNYW5hZ2VyRm9ybSwgZ3JpZCk7XG5cbiAgICBjb25zdCAkbW9kYWwgPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfZ3JpZF9jb21tb25fc2hvd19xdWVyeV9tb2RhbCcpO1xuICAgICRtb2RhbC5tb2RhbCgnc2hvdycpO1xuXG4gICAgJG1vZGFsLm9uKCdjbGljaycsICcuYnRuLXNxbC1zdWJtaXQnLCAoKSA9PiAkc3FsTWFuYWdlckZvcm0uc3VibWl0KCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEludm9rZWQgd2hlbiBjbGlja2luZyBvbiB0aGUgXCJleHBvcnQgdG8gdGhlIHNxbCBxdWVyeVwiIHRvb2xiYXIgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrKGdyaWQpIHtcbiAgICBjb25zdCAkc3FsTWFuYWdlckZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWxfZm9ybScpO1xuXG4gICAgdGhpcy5fZmlsbEV4cG9ydEZvcm0oJHNxbE1hbmFnZXJGb3JtLCBncmlkKTtcblxuICAgICRzcWxNYW5hZ2VyRm9ybS5zdWJtaXQoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBGaWxsIGV4cG9ydCBmb3JtIHdpdGggU1FMIGFuZCBpdCdzIG5hbWVcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRzcWxNYW5hZ2VyRm9ybVxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9maWxsRXhwb3J0Rm9ybSgkc3FsTWFuYWdlckZvcm0sIGdyaWQpIHtcbiAgICBjb25zdCBxdWVyeSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWdyaWQtdGFibGUnKS5kYXRhKCdxdWVyeScpO1xuXG4gICAgJHNxbE1hbmFnZXJGb3JtLmZpbmQoJ3RleHRhcmVhW25hbWU9XCJzcWxcIl0nKS52YWwocXVlcnkpO1xuICAgICRzcWxNYW5hZ2VyRm9ybS5maW5kKCdpbnB1dFtuYW1lPVwibmFtZVwiXScpLnZhbCh0aGlzLl9nZXROYW1lRnJvbUJyZWFkY3J1bWIoKSk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGV4cG9ydCBuYW1lIGZyb20gcGFnZSdzIGJyZWFkY3J1bWJcbiAgICpcbiAgICogQHJldHVybiB7U3RyaW5nfVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldE5hbWVGcm9tQnJlYWRjcnVtYigpIHtcbiAgICBjb25zdCAkYnJlYWRjcnVtYnMgPSAkKCcuaGVhZGVyLXRvb2xiYXInKS5maW5kKCcuYnJlYWRjcnVtYi1pdGVtJyk7XG4gICAgbGV0IG5hbWUgPSAnJztcblxuICAgICRicmVhZGNydW1icy5lYWNoKChpLCBpdGVtKSA9PiB7XG4gICAgICBjb25zdCAkYnJlYWRjcnVtYiA9ICQoaXRlbSk7XG5cbiAgICAgIGNvbnN0IGJyZWFkY3J1bWJUaXRsZSA9IDAgPCAkYnJlYWRjcnVtYi5maW5kKCdhJykubGVuZ3RoID9cbiAgICAgICAgJGJyZWFkY3J1bWIuZmluZCgnYScpLnRleHQoKSA6XG4gICAgICAgICRicmVhZGNydW1iLnRleHQoKTtcblxuICAgICAgaWYgKDAgPCBuYW1lLmxlbmd0aCkge1xuICAgICAgICBuYW1lID0gbmFtZS5jb25jYXQoJyA+ICcpO1xuICAgICAgfVxuXG4gICAgICBuYW1lID0gbmFtZS5jb25jYXQoYnJlYWRjcnVtYlRpdGxlKTtcbiAgICB9KTtcblxuICAgIHJldHVybiBuYW1lO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2V4cG9ydC10by1zcWwtbWFuYWdlci1leHRlbnNpb24uanMiLCJ2YXIgY29yZSA9IG1vZHVsZS5leHBvcnRzID0ge3ZlcnNpb246ICcyLjQuMCd9O1xuaWYodHlwZW9mIF9fZSA9PSAnbnVtYmVyJylfX2UgPSBjb3JlOyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lIG5vLXVuZGVmXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jb3JlLmpzXG4vLyBtb2R1bGUgaWQgPSAzXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuLyoqXG4gKiBSZXNwb25zaWJsZSBmb3IgZ3JpZCBmaWx0ZXJzIHNlYXJjaCBhbmQgcmVzZXQgYnV0dG9uIGF2YWlsYWJpbGl0eSB3aGVuIGZpbHRlciBpbnB1dHMgY2hhbmdlcy5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb24ge1xuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgY29uc3QgJGZpbHRlcnNSb3cgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5jb2x1bW4tZmlsdGVycycpO1xuICAgICRmaWx0ZXJzUm93LmZpbmQoJy5ncmlkLXNlYXJjaC1idXR0b24nKS5wcm9wKCdkaXNhYmxlZCcsIHRydWUpO1xuXG4gICAgJGZpbHRlcnNSb3cuZmluZCgnaW5wdXQ6bm90KC5qcy1idWxrLWFjdGlvbi1zZWxlY3QtYWxsKSwgc2VsZWN0Jykub24oJ2lucHV0IGRwLmNoYW5nZScsICgpID0+IHtcbiAgICAgICRmaWx0ZXJzUm93LmZpbmQoJy5ncmlkLXNlYXJjaC1idXR0b24nKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAgICRmaWx0ZXJzUm93LmZpbmQoJy5qcy1ncmlkLXJlc2V0LWJ1dHRvbicpLnByb3AoJ2hpZGRlbicsIGZhbHNlKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXN1Ym1pdC1idXR0b24tZW5hYmxlci1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBCdWxrQWN0aW9uU2VsZWN0Q2hlY2tib3hFeHRlbnNpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkIHdpdGggYnVsayBhY3Rpb24gY2hlY2tib3hlcyBoYW5kbGluZyBmdW5jdGlvbmFsaXR5XG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICB0aGlzLl9oYW5kbGVCdWxrQWN0aW9uQ2hlY2tib3hTZWxlY3QoZ3JpZCk7XG4gICAgdGhpcy5faGFuZGxlQnVsa0FjdGlvblNlbGVjdEFsbENoZWNrYm94KGdyaWQpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZXMgXCJTZWxlY3QgYWxsXCIgYnV0dG9uIGluIHRoZSBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2hhbmdlJywgJy5qcy1idWxrLWFjdGlvbi1zZWxlY3QtYWxsJywgKGUpID0+IHtcbiAgICAgIGNvbnN0ICRjaGVja2JveCA9ICQoZS5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgY29uc3QgaXNDaGVja2VkID0gJGNoZWNrYm94LmlzKCc6Y2hlY2tlZCcpO1xuICAgICAgaWYgKGlzQ2hlY2tlZCkge1xuICAgICAgICB0aGlzLl9lbmFibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHRoaXMuX2Rpc2FibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH1cblxuICAgICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb24tY2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJywgaXNDaGVja2VkKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIGVhY2ggYnVsayBhY3Rpb24gY2hlY2tib3ggc2VsZWN0IGluIHRoZSBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZUJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2hhbmdlJywgJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveCcsICgpID0+IHtcbiAgICAgIGNvbnN0IGNoZWNrZWRSb3dzQ291bnQgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveDpjaGVja2VkJykubGVuZ3RoO1xuXG4gICAgICBpZiAoY2hlY2tlZFJvd3NDb3VudCA+IDApIHtcbiAgICAgICAgdGhpcy5fZW5hYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB0aGlzLl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogRW5hYmxlIGJ1bGsgYWN0aW9ucyBidXR0b25cbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZW5hYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9ucy1idG4nKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNhYmxlIGJ1bGsgYWN0aW9ucyBidXR0b25cbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZGlzYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbnMtYnRuJykucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLWFjdGlvbi1jaGVja2JveC1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCBDb25maXJtTW9kYWwgZnJvbSAnQGNvbXBvbmVudHMvbW9kYWwnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogSGFuZGxlcyBzdWJtaXQgb2YgZ3JpZCBhY3Rpb25zXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFN1Ym1pdEJ1bGtBY3Rpb25FeHRlbnNpb24ge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICByZXR1cm4ge1xuICAgICAgZXh0ZW5kOiAoZ3JpZCkgPT4gdGhpcy5leHRlbmQoZ3JpZCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGJ1bGsgYWN0aW9uIHN1Ym1pdHRpbmdcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1idWxrLWFjdGlvbi1zdWJtaXQtYnRuJywgKGV2ZW50KSA9PiB7XG4gICAgICB0aGlzLnN1Ym1pdChldmVudCwgZ3JpZCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlIGJ1bGsgYWN0aW9uIHN1Ym1pdHRpbmdcbiAgICpcbiAgICogQHBhcmFtIHtFdmVudH0gZXZlbnRcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBzdWJtaXQoZXZlbnQsIGdyaWQpIHtcbiAgICBjb25zdCAkc3VibWl0QnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICRzdWJtaXRCdG4uZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG4gICAgY29uc3QgY29uZmlybVRpdGxlID0gJHN1Ym1pdEJ0bi5kYXRhKCdjb25maXJtVGl0bGUnKTtcblxuICAgIGlmIChjb25maXJtTWVzc2FnZSAhPT0gdW5kZWZpbmVkICYmIDAgPCBjb25maXJtTWVzc2FnZS5sZW5ndGgpIHtcbiAgICAgIGlmIChjb25maXJtVGl0bGUgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICB0aGlzLnNob3dDb25maXJtTW9kYWwoJHN1Ym1pdEJ0biwgZ3JpZCwgY29uZmlybU1lc3NhZ2UsIGNvbmZpcm1UaXRsZSk7XG4gICAgICB9IGVsc2UgaWYgKGNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgIHRoaXMucG9zdEZvcm0oJHN1Ym1pdEJ0biwgZ3JpZCk7XG4gICAgICB9XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMucG9zdEZvcm0oJHN1Ym1pdEJ0biwgZ3JpZCk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkc3VibWl0QnRuXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKiBAcGFyYW0ge3N0cmluZ30gY29uZmlybU1lc3NhZ2VcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbmZpcm1UaXRsZVxuICAgKi9cbiAgc2hvd0NvbmZpcm1Nb2RhbCgkc3VibWl0QnRuLCBncmlkLCBjb25maXJtTWVzc2FnZSwgY29uZmlybVRpdGxlKSB7XG4gICAgY29uc3QgY29uZmlybUJ1dHRvbkxhYmVsID0gJHN1Ym1pdEJ0bi5kYXRhKCdjb25maXJtQnV0dG9uTGFiZWwnKTtcbiAgICBjb25zdCBjbG9zZUJ1dHRvbkxhYmVsID0gJHN1Ym1pdEJ0bi5kYXRhKCdjbG9zZUJ1dHRvbkxhYmVsJyk7XG4gICAgY29uc3QgY29uZmlybUJ1dHRvbkNsYXNzID0gJHN1Ym1pdEJ0bi5kYXRhKCdjb25maXJtQnV0dG9uQ2xhc3MnKTtcblxuICAgIGNvbnN0IG1vZGFsID0gbmV3IENvbmZpcm1Nb2RhbCh7XG4gICAgICBpZDogYCR7Z3JpZC5nZXRJZCgpfV9ncmlkX2NvbmZpcm1fbW9kYWxgLFxuICAgICAgY29uZmlybVRpdGxlLFxuICAgICAgY29uZmlybU1lc3NhZ2UsXG4gICAgICBjb25maXJtQnV0dG9uTGFiZWwsXG4gICAgICBjbG9zZUJ1dHRvbkxhYmVsLFxuICAgICAgY29uZmlybUJ1dHRvbkNsYXNzLFxuICAgIH0sICgpID0+IHRoaXMucG9zdEZvcm0oJHN1Ym1pdEJ0biwgZ3JpZCkpO1xuXG4gICAgbW9kYWwuc2hvdygpO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkc3VibWl0QnRuXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgcG9zdEZvcm0oJHN1Ym1pdEJ0biwgZ3JpZCkge1xuICAgIGNvbnN0ICRmb3JtID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2ZpbHRlcl9mb3JtJyk7XG5cbiAgICAkZm9ybS5hdHRyKCdhY3Rpb24nLCAkc3VibWl0QnRuLmRhdGEoJ2Zvcm0tdXJsJykpO1xuICAgICRmb3JtLmF0dHIoJ21ldGhvZCcsICRzdWJtaXRCdG4uZGF0YSgnZm9ybS1tZXRob2QnKSk7XG4gICAgJGZvcm0uc3VibWl0KCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWJ1bGstYWN0aW9uLWV4dGVuc2lvbi5qcyIsIi8vIDE5LjEuMi4xNCAvIDE1LjIuMy4xNCBPYmplY3Qua2V5cyhPKVxudmFyICRrZXlzICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWtleXMtaW50ZXJuYWwnKVxuICAsIGVudW1CdWdLZXlzID0gcmVxdWlyZSgnLi9fZW51bS1idWcta2V5cycpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IE9iamVjdC5rZXlzIHx8IGZ1bmN0aW9uIGtleXMoTyl7XG4gIHJldHVybiAka2V5cyhPLCBlbnVtQnVnS2V5cyk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWtleXMuanNcbi8vIG1vZHVsZSBpZCA9IDMzXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiBoYW5kbGVzIGxpbmsgcm93IGFjdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgdGhpcy5pbml0Um93TGlua3MoZ3JpZCk7XG4gICAgdGhpcy5pbml0Q29uZmlybWFibGVBY3Rpb25zKGdyaWQpO1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgaW5pdENvbmZpcm1hYmxlQWN0aW9ucyhncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWxpbmstcm93LWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgY29uZmlybU1lc3NhZ2UgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ2NvbmZpcm0tbWVzc2FnZScpO1xuXG4gICAgICBpZiAoY29uZmlybU1lc3NhZ2UubGVuZ3RoICYmICFjb25maXJtKGNvbmZpcm1NZXNzYWdlKSkge1xuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEFkZCBhIGNsaWNrIGV2ZW50IG9uIHJvd3MgdGhhdCBtYXRjaGVzIHRoZSBmaXJzdCBsaW5rIGFjdGlvbiAoaWYgcHJlc2VudClcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBpbml0Um93TGlua3MoZ3JpZCkge1xuICAgICQoJ3RyJywgZ3JpZC5nZXRDb250YWluZXIoKSkuZWFjaChmdW5jdGlvbiBpbml0RWFjaFJvdygpIHtcbiAgICAgIGNvbnN0ICRwYXJlbnRSb3cgPSAkKHRoaXMpO1xuXG4gICAgICAkKCcuanMtbGluay1yb3ctYWN0aW9uW2RhdGEtY2xpY2thYmxlLXJvdz0xXTpmaXJzdCcsICRwYXJlbnRSb3cpLmVhY2goZnVuY3Rpb24gcHJvcGFnYXRlRmlyc3RMaW5rQWN0aW9uKCkge1xuICAgICAgICBjb25zdCAkcm93QWN0aW9uID0gJCh0aGlzKTtcbiAgICAgICAgY29uc3QgJHBhcmVudENlbGwgPSAkcm93QWN0aW9uLmNsb3Nlc3QoJ3RkJyk7XG4gICAgICAgIFxuICAgICAgICBjb25zdCBjbGlja2FibGVDZWxscyA9ICQoJ3RkLmNsaWNrYWJsZScsICRwYXJlbnRSb3cpXG4gICAgICAgICAgLm5vdCgkcGFyZW50Q2VsbClcbiAgICAgICAgO1xuXG4gICAgICAgIGNsaWNrYWJsZUNlbGxzLmFkZENsYXNzKCdjdXJzb3ItcG9pbnRlcicpLmNsaWNrKCgpID0+IHtcbiAgICAgICAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICRyb3dBY3Rpb24uZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICAgICAgICBpZiAoIWNvbmZpcm1NZXNzYWdlLmxlbmd0aCB8fCBjb25maXJtKGNvbmZpcm1NZXNzYWdlKSkge1xuICAgICAgICAgICAgZG9jdW1lbnQubG9jYXRpb24gPSAkcm93QWN0aW9uLmF0dHIoJ2hyZWYnKTtcbiAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcyIsIi8vIDcuMi4xIFJlcXVpcmVPYmplY3RDb2VyY2libGUoYXJndW1lbnQpXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgaWYoaXQgPT0gdW5kZWZpbmVkKXRocm93IFR5cGVFcnJvcihcIkNhbid0IGNhbGwgbWV0aG9kIG9uICBcIiArIGl0KTtcbiAgcmV0dXJuIGl0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2RlZmluZWQuanNcbi8vIG1vZHVsZSBpZCA9IDM1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLy8gNy4xLjQgVG9JbnRlZ2VyXG52YXIgY2VpbCAgPSBNYXRoLmNlaWxcbiAgLCBmbG9vciA9IE1hdGguZmxvb3I7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGlzTmFOKGl0ID0gK2l0KSA/IDAgOiAoaXQgPiAwID8gZmxvb3IgOiBjZWlsKShpdCk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8taW50ZWdlci5qc1xuLy8gbW9kdWxlIGlkID0gMzZcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5pbXBvcnQgUm91dGVyIGZyb20gJy4uLy4uL3JvdXRlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBCdWxrT3BlblRhYnNFeHRlbnNpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQnVsa09wZW5UYWJzRXh0ZW5zaW9uIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gICAgcmV0dXJuIHtcbiAgICAgIGV4dGVuZDogKGdyaWQpID0+IHRoaXMuZXh0ZW5kKGdyaWQpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBidWxrIGFjdGlvbiBvcGVuIHRhYnNcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1idWxrLWFjdGlvbi1idG4ub3Blbl90YWJzJywgKGV2ZW50KSA9PiB7XG4gICAgICB0aGlzLm9wZW5UYWJzKGV2ZW50LCBncmlkKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgYnVsayBhY3Rpb24gb3BlbmluZyB0YWJzXG4gICAqXG4gICAqIEBwYXJhbSB7RXZlbnR9IGV2ZW50XG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgb3BlblRhYnMoZXZlbnQsIGdyaWQpIHtcbiAgICBjb25zdCAkc3VibWl0QnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICBjb25zdCByb3V0ZSA9ICRzdWJtaXRCdG4uZGF0YSgncm91dGUnKTtcbiAgICBjb25zdCByb3V0ZVBhcmFtTmFtZSA9ICRzdWJtaXRCdG4uZGF0YSgncm91dGVQYXJhbU5hbWUnKTtcbiAgICBjb25zdCB0YWJzQmxvY2tlZE1lc3NhZ2UgPSAkc3VibWl0QnRuLmRhdGEoJ3RhYnNCbG9ja2VkTWVzc2FnZScpO1xuXG4gICAgY29uc3QgJGNoZWNrYm94ZXMgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveDpjaGVja2VkJyk7XG4gICAgbGV0IGFsbFRhYnNPcGVuZWQgPSB0cnVlO1xuICAgICRjaGVja2JveGVzLmVhY2goKGksIGVsZW1lbnQpID0+IHtcbiAgICAgIGNvbnN0ICRjaGVja2JveCA9ICQoZWxlbWVudCk7XG4gICAgICBjb25zdCByb3V0ZVBhcmFtcyA9IHt9O1xuICAgICAgcm91dGVQYXJhbXNbcm91dGVQYXJhbU5hbWVdID0gJGNoZWNrYm94LnZhbCgpO1xuXG4gICAgICBsZXQgaGFuZGxlID0gd2luZG93Lm9wZW4odGhpcy5yb3V0ZXIuZ2VuZXJhdGUocm91dGUsIHJvdXRlUGFyYW1zKSk7XG4gICAgICBpZiAoaGFuZGxlKSB7XG4gICAgICAgIGhhbmRsZS5ibHVyKCk7XG4gICAgICAgIHdpbmRvdy5mb2N1cygpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgYWxsVGFic09wZW5lZCA9IGZhbHNlO1xuICAgICAgfVxuXG4gICAgICBpZiAoIWFsbFRhYnNPcGVuZWQpIHtcbiAgICAgICAgYWxlcnQodGFic0Jsb2NrZWRNZXNzYWdlKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLW9wZW4tdGFicy1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuLyoqXG4gKiBUaGlzIGV4dGVuc2lvbiBlbmFibGVzIHN1Ym1pdCBmdW5jdGlvbmFsaXR5IG9mIHRoZSBjaG9pY2UgZmllbGRzIGluIGdyaWQuXG4gKlxuICogVXNhZ2Ugb2YgdGhlIGV4dGVuc2lvbjpcbiAqXG4gKiBjb25zdCBteUdyaWQgPSBuZXcgR3JpZCgnbXlHcmlkJyk7XG4gKiBteUdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBDaG9pY2VFeHRlbnNpb24oKSk7XG4gKlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDaG9pY2VFeHRlbnNpb24ge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLmxvY2sgPSBbXTtcbiAgfVxuXG4gIGV4dGVuZChncmlkKSB7XG4gICAgY29uc3QgJGNob2ljZU9wdGlvbnNDb250YWluZXIgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJ3RhYmxlLnRhYmxlIC5qcy1jaG9pY2Utb3B0aW9ucycpO1xuXG4gICAgJGNob2ljZU9wdGlvbnNDb250YWluZXIuZmluZCgnLmpzLWRyb3Bkb3duLWl0ZW0nKS5vbignY2xpY2snLCAoZSkgPT4ge1xuICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgY29uc3QgJGJ1dHRvbiA9ICQoZS5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0ICRwYXJlbnQgPSAkYnV0dG9uLmNsb3Nlc3QoJy5qcy1jaG9pY2Utb3B0aW9ucycpO1xuICAgICAgY29uc3QgdXJsID0gJHBhcmVudC5kYXRhKCd1cmwnKTtcblxuICAgICAgdGhpcy5fc3VibWl0Rm9ybSh1cmwsICRidXR0b24pO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFN1Ym1pdHMgdGhlIGZvcm0uXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB1cmxcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRidXR0b25cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zdWJtaXRGb3JtKHVybCwgJGJ1dHRvbikge1xuICAgIGNvbnN0IHNlbGVjdGVkU3RhdHVzSWQgPSAkYnV0dG9uLmRhdGEoJ3ZhbHVlJyk7XG5cbiAgICBpZiAodGhpcy5faXNMb2NrZWQodXJsKSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGNvbnN0ICRmb3JtID0gJCgnPGZvcm0+Jywge1xuICAgICAgYWN0aW9uOiB1cmwsXG4gICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICB9KS5hcHBlbmQoXG4gICAgICAkKCc8aW5wdXQ+Jywge1xuICAgICAgICBuYW1lOiAndmFsdWUnLFxuICAgICAgICB2YWx1ZTogc2VsZWN0ZWRTdGF0dXNJZCxcbiAgICAgICAgdHlwZTogJ2hpZGRlbicsXG4gICAgICB9KSk7XG5cbiAgICAkZm9ybS5hcHBlbmRUbygnYm9keScpO1xuICAgICRmb3JtLnN1Ym1pdCgpO1xuXG4gICAgdGhpcy5fbG9jayh1cmwpO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrcyBpZiBjdXJyZW50IHVybCBpcyBiZWluZyB1c2VkIGF0IHRoZSBtb21lbnQuXG4gICAqXG4gICAqIEBwYXJhbSB1cmxcbiAgICogQHJldHVybiB7Ym9vbGVhbn1cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pc0xvY2tlZCh1cmwpIHtcbiAgICByZXR1cm4gdGhpcy5sb2NrLmluY2x1ZGVzKHVybCk7XG4gIH1cblxuICAvKipcbiAgICogTG9ja3MgdGhlIGN1cnJlbnQgdXJsIHNvIGl0IGNhbnQgYmUgdXNlZCB0d2ljZSB0byBleGVjdXRlIHNhbWUgcmVxdWVzdFxuICAgKiBAcGFyYW0gdXJsXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfbG9jayh1cmwpIHtcbiAgICB0aGlzLmxvY2sucHVzaCh1cmwpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2Nob2ljZS1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBBbGxvd3Mgc3VibWl0dGluZyBmb3JtIGluc2lkZSBtb2RhbHMuXG4gKiBGb3JtIG11c3QgYmUgaW5zaWRlIG1vZGFsLCBzZWUgZXhhbXBsZSBzdHJ1Y3R1cmUgYmVsb3c6XG4gKlxuICogPGRpdiBjbGFzcz1cIm1vZGFsXCIgaWQ9XCJ1bmlxdWVNb2RhbElkXCI+XG4gKiAgPGZvcm0gZGF0YS1idWxrLWlucHV0cy1pZD1cImJ1bGtJbnB1dHNcIj5cbiAqICAgIDxkaXYgY2xhc3M9XCJkLW5vbmVcIj5cbiAqICAgICAgPGRpdiBpZD1cImJ1bGtJbnB1dHNcIiBkYXRhLXByb3RvdHlwZT1cIjxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cIl9fbmFtZV9fXCIvPlwiPjwvZGl2PlxuICogICAgPC9kaXY+XG4gKiAgPC9mb3JtPlxuICogPC9kaXY+XG4gKlxuICogTm90ZSB0aGF0IFwiZGF0YS1wcm90b3R5cGVcIiBpcyByZXF1aXJlZCB0byBhZGQgY2hlY2tlZCBpdGVtcyB0byB0aGUgZm9ybS4gXCJfX25hbWVfX1wiXG4gKiB3aWxsIGJlIHJlcGxhY2VkIHdpdGggdmFsdWUgb2YgYnVsayBjaGVja2JveC5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTW9kYWxGb3JtU3VibWl0RXh0ZW5zaW9uIHtcbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtYnVsay1tb2RhbC1mb3JtLXN1Ym1pdC1idG4nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0IG1vZGFsSWQgPSAkKGV2ZW50LnRhcmdldCkuZGF0YSgnbW9kYWwtaWQnKTtcblxuICAgICAgY29uc3QgJG1vZGFsID0gJChgIyR7bW9kYWxJZH1gKTtcbiAgICAgICRtb2RhbC5tb2RhbCgnc2hvdycpO1xuXG4gICAgICAkbW9kYWwuZmluZCgnLmpzLXN1Ym1pdC1tb2RhbC1mb3JtLWJ0bicpLm9uKCdjbGljaycsICgpID0+IHtcbiAgICAgICAgY29uc3QgJGZvcm0gPSAkbW9kYWwuZmluZCgnZm9ybScpO1xuICAgICAgICBjb25zdCAkYnVsa0lucHV0c0Jsb2NrID0gJGZvcm0uZmluZChgIyR7JGZvcm0uZGF0YSgnYnVsay1pbnB1dHMtaWQnKX1gKTtcbiAgICAgICAgY29uc3QgJGNoZWNrYm94ZXMgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveDpjaGVja2VkJyk7XG5cbiAgICAgICAgJGNoZWNrYm94ZXMuZWFjaCgoaSwgZWxlbWVudCkgPT4ge1xuICAgICAgICAgIGNvbnN0ICRjaGVja2JveCA9ICQoZWxlbWVudCk7XG5cbiAgICAgICAgICBjb25zdCBpbnB1dCA9ICRidWxrSW5wdXRzQmxvY2tcbiAgICAgICAgICAgIC5kYXRhKCdwcm90b3R5cGUnKVxuICAgICAgICAgICAgLnJlcGxhY2UoL19fbmFtZV9fL2csICRjaGVja2JveC52YWwoKSk7XG5cbiAgICAgICAgICBjb25zdCAkaW5wdXQgPSAkKCQucGFyc2VIVE1MKGlucHV0KVswXSk7XG4gICAgICAgICAgJGlucHV0LnZhbCgkY2hlY2tib3gudmFsKCkpO1xuXG4gICAgICAgICAgJGZvcm0uYXBwZW5kKCRpbnB1dCk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgICRmb3JtLnN1Ym1pdCgpO1xuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbW9kYWwtZm9ybS1zdWJtaXQtZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogRXh0ZW5kcyBncmlkIHdpdGggcHJldmlldyBmdW5jdGlvbmFsaXR5LlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBQcmV2aWV3RXh0ZW5zaW9uIHtcbiAgY29uc3RydWN0b3IocHJldmlld0N1c3RvbWl6YXRpb24pIHtcbiAgICB0aGlzLmxvY2sgPSBbXTtcbiAgICB0aGlzLmV4cGFuZFNlbGVjdG9yID0gJy5qcy1leHBhbmQnO1xuICAgIHRoaXMuY29sbGFwc2VTZWxlY3RvciA9ICcuanMtY29sbGFwc2UnO1xuICAgIHRoaXMucHJldmlld09wZW5DbGFzcyA9ICdwcmV2aWV3LW9wZW4nO1xuICAgIHRoaXMucHJldmlld1RvZ2dsZVNlbGVjdG9yID0gJy5wcmV2aWV3LXRvZ2dsZSc7XG4gICAgdGhpcy5wcmV2aWV3Q3VzdG9taXphdGlvbiA9IHByZXZpZXdDdXN0b21pemF0aW9uO1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZHMgcHJvdmlkZWQgZ3JpZCB3aXRoIHByZXZpZXcgZnVuY3Rpb25hbGl0eVxuICAgKlxuICAgKiBAcGFyYW0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICB0aGlzLiRncmlkQ29udGFpbmVyID0gJChncmlkLmdldENvbnRhaW5lcik7XG5cbiAgICB0aGlzLiRncmlkQ29udGFpbmVyLmZpbmQoJ3Rib2R5IHRyJykub24oJ21vdXNlb3ZlciBtb3VzZWxlYXZlJywgZXZlbnQgPT4gdGhpcy5faGFuZGxlSWNvbkhvdmVyaW5nKGV2ZW50KSk7XG4gICAgdGhpcy4kZ3JpZENvbnRhaW5lci5maW5kKHRoaXMucHJldmlld1RvZ2dsZVNlbGVjdG9yKS5vbignY2xpY2snLCBldmVudCA9PiB0aGlzLl90b2dnbGVQcmV2aWV3KGV2ZW50KSk7XG4gIH1cblxuICAvKipcbiAgICogU2hvd3MvaGlkZXMgcHJldmlldyB0b2dnbGluZyBpY29uc1xuICAgKlxuICAgKiBAcGFyYW0gZXZlbnRcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVJY29uSG92ZXJpbmcoZXZlbnQpIHtcbiAgICBjb25zdCAkcHJldmlld1RvZ2dsZSA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZmluZCh0aGlzLnByZXZpZXdUb2dnbGVTZWxlY3Rvcik7XG5cbiAgICBpZiAoZXZlbnQudHlwZSA9PT0gJ21vdXNlb3ZlcicgJiYgISQoZXZlbnQuY3VycmVudFRhcmdldCkuaGFzQ2xhc3ModGhpcy5wcmV2aWV3T3BlbkNsYXNzKSkge1xuICAgICAgdGhpcy5fc2hvd0V4cGFuZEljb24oJHByZXZpZXdUb2dnbGUpO1xuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLl9oaWRlRXhwYW5kSWNvbigkcHJldmlld1RvZ2dsZSk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzL2hpZGVzIHByZXZpZXdcbiAgICpcbiAgICogQHBhcmFtIGV2ZW50XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlUHJldmlldyhldmVudCkge1xuICAgIGNvbnN0ICRwcmV2aWV3VG9nZ2xlID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICBjb25zdCAkY29sdW1uUm93ID0gJHByZXZpZXdUb2dnbGUuY2xvc2VzdCgndHInKTtcblxuICAgIGlmICgkY29sdW1uUm93Lmhhc0NsYXNzKHRoaXMucHJldmlld09wZW5DbGFzcykpIHtcbiAgICAgICRjb2x1bW5Sb3cubmV4dCgnLnByZXZpZXctcm93JykucmVtb3ZlKCk7XG4gICAgICAkY29sdW1uUm93LnJlbW92ZUNsYXNzKHRoaXMucHJldmlld09wZW5DbGFzcyk7XG4gICAgICB0aGlzLl9zaG93RXhwYW5kSWNvbigkY29sdW1uUm93KTtcbiAgICAgIHRoaXMuX2hpZGVDb2xsYXBzZUljb24oJGNvbHVtblJvdyk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB0aGlzLl9jbG9zZU9wZW5lZFByZXZpZXdzKCk7XG5cbiAgICBjb25zdCBkYXRhVXJsID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdwcmV2aWV3LWRhdGEtdXJsJyk7XG5cbiAgICBpZiAodGhpcy5faXNMb2NrZWQoZGF0YVVybCkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAvLyBQcmV2ZW50cyBsb2FkaW5nIHByZXZpZXcgbXVsdGlwbGUgdGltZXMuXG4gICAgLy8gVXNlcyBcImRhdGFVcmxcIiBhcyBsb2NrIGtleS5cbiAgICB0aGlzLl9sb2NrKGRhdGFVcmwpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHVybDogZGF0YVVybCxcbiAgICAgIG1ldGhvZDogJ0dFVCcsXG4gICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgY29tcGxldGU6ICgpID0+IHtcbiAgICAgICAgdGhpcy5fdW5sb2NrKGRhdGFVcmwpO1xuICAgICAgfSxcbiAgICB9KS50aGVuKChyZXNwb25zZSkgPT4ge1xuICAgICAgdGhpcy5fcmVuZGVyUHJldmlld0NvbnRlbnQoJGNvbHVtblJvdywgcmVzcG9uc2UucHJldmlldyk7XG4gICAgfSkuY2F0Y2goKGUpID0+IHtcbiAgICAgIHNob3dFcnJvck1lc3NhZ2UoZS5yZXNwb25zZUpTT04ubWVzc2FnZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBwcmV2aWV3IGNvbnRlbnRcbiAgICpcbiAgICogQHBhcmFtICRjb2x1bW5Sb3dcbiAgICogQHBhcmFtIGNvbnRlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJQcmV2aWV3Q29udGVudCgkY29sdW1uUm93LCBjb250ZW50KSB7XG4gICAgY29uc3Qgcm93Q29sdW1uQ291bnQgPSAkY29sdW1uUm93LmZpbmQoJ3RkJykubGVuZ3RoO1xuXG4gICAgY29uc3QgJHByZXZpZXdUZW1wbGF0ZSA9ICQoYFxuICAgICAgICA8dHIgY2xhc3M9XCJwcmV2aWV3LXJvd1wiPlxuICAgICAgICAgIDx0ZCBjb2xzcGFuPVwiJHtyb3dDb2x1bW5Db3VudH1cIj4ke2NvbnRlbnR9PC90ZD5cbiAgICAgICAgPC90cj5cbiAgICAgIGApO1xuXG4gICAgJGNvbHVtblJvdy5hZGRDbGFzcyh0aGlzLnByZXZpZXdPcGVuQ2xhc3MpO1xuICAgIHRoaXMuX3Nob3dDb2xsYXBzZUljb24oJGNvbHVtblJvdyk7XG4gICAgdGhpcy5faGlkZUV4cGFuZEljb24oJGNvbHVtblJvdyk7XG5cbiAgICBpZiAodHlwZW9mIHRoaXMucHJldmlld0N1c3RvbWl6YXRpb24gPT09ICdmdW5jdGlvbicpIHtcbiAgICAgIHRoaXMucHJldmlld0N1c3RvbWl6YXRpb24oJHByZXZpZXdUZW1wbGF0ZSk7XG4gICAgfVxuXG4gICAgJGNvbHVtblJvdy5hZnRlcigkcHJldmlld1RlbXBsYXRlKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyBwcmV2aWV3IGV4cGFuZGluZyBpY29uXG4gICAqXG4gICAqIEBwYXJhbSBwYXJlbnRcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93RXhwYW5kSWNvbihwYXJlbnQpIHtcbiAgICBwYXJlbnQuZmluZCh0aGlzLmV4cGFuZFNlbGVjdG9yKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgcHJldmlldyBleHBhbmRpbmcgaWNvblxuICAgKlxuICAgKiBAcGFyYW0gcGFyZW50XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZUV4cGFuZEljb24ocGFyZW50KSB7XG4gICAgcGFyZW50LmZpbmQodGhpcy5leHBhbmRTZWxlY3RvcikuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIHByZXZpZXcgY29sbGFwc2luZyBpY29uXG4gICAqXG4gICAqIEBwYXJhbSBwYXJlbnRcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Q29sbGFwc2VJY29uKHBhcmVudCkge1xuICAgIHBhcmVudC5maW5kKHRoaXMuY29sbGFwc2VTZWxlY3RvcikucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIHByZXZpZXcgY29sbGFwc2luZyBpY29uXG4gICAqXG4gICAqIEBwYXJhbSBwYXJlbnRcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlQ29sbGFwc2VJY29uKHBhcmVudCkge1xuICAgIHBhcmVudC5maW5kKHRoaXMuY29sbGFwc2VTZWxlY3RvcikuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgX2lzTG9ja2VkKGtleSkge1xuICAgIHJldHVybiB0aGlzLmxvY2suaW5kZXhPZihrZXkpICE9PSAtMTtcbiAgfVxuXG4gIF9sb2NrKGtleSkge1xuICAgIGlmICh0aGlzLl9pc0xvY2tlZChrZXkpKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdGhpcy5sb2NrLnB1c2goa2V5KTtcbiAgfVxuXG4gIF91bmxvY2soa2V5KSB7XG4gICAgY29uc3QgaW5kZXggPSB0aGlzLmxvY2suaW5kZXhPZihrZXkpO1xuXG4gICAgaWYgKGluZGV4ID09PSAtMSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHRoaXMubG9jay5zcGxpY2UoaW5kZXgsIDEpO1xuICB9XG5cbiAgLyoqXG4gICAqIENsb3NlIGFsbCBwcmV2aWV3cyB0aGF0IGFyZSBvcGVuLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2Nsb3NlT3BlbmVkUHJldmlld3MoKSB7XG4gICAgY29uc3QgJHJvd3MgPSB0aGlzLiRncmlkQ29udGFpbmVyLmZpbmQoJy5ncmlkLXRhYmxlIHRib2R5JykuZmluZCgndHI6bm90KC5wcmV2aWV3LXJvdyknKTtcblxuICAgICQuZWFjaCgkcm93cywgKGksIHJvdykgPT4ge1xuICAgICAgY29uc3QgJHJvdyA9ICQocm93KTtcblxuICAgICAgaWYgKCEkcm93Lmhhc0NsYXNzKHRoaXMucHJldmlld09wZW5DbGFzcykpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBjb25zdCAkcHJldmlld1JvdyA9ICRyb3cubmV4dCgpO1xuXG4gICAgICBpZiAoISRwcmV2aWV3Um93Lmhhc0NsYXNzKCdwcmV2aWV3LXJvdycpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgJHByZXZpZXdSb3cucmVtb3ZlKCk7XG4gICAgICAkcm93LnJlbW92ZUNsYXNzKHRoaXMucHJldmlld09wZW5DbGFzcyk7XG4gICAgICB0aGlzLl9oaWRlQ29sbGFwc2VJY29uKCRyb3cpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3ByZXZpZXctZXh0ZW5zaW9uLmpzIiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIHJldHVybiB0eXBlb2YgaXQgPT09ICdvYmplY3QnID8gaXQgIT09IG51bGwgOiB0eXBlb2YgaXQgPT09ICdmdW5jdGlvbic7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXMtb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSA0XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBpZCA9IDBcbiAgLCBweCA9IE1hdGgucmFuZG9tKCk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGtleSl7XG4gIHJldHVybiAnU3ltYm9sKCcuY29uY2F0KGtleSA9PT0gdW5kZWZpbmVkID8gJycgOiBrZXksICcpXycsICgrK2lkICsgcHgpLnRvU3RyaW5nKDM2KSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdWlkLmpzXG4vLyBtb2R1bGUgaWQgPSA0MFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG4vKipcbiAqIE1ha2VzIGEgdGFibGUgc29ydGFibGUgYnkgY29sdW1ucy5cbiAqIFRoaXMgZm9yY2VzIGEgcGFnZSByZWxvYWQgd2l0aCBtb3JlIHF1ZXJ5IHBhcmFtZXRlcnMuXG4gKi9cbmNsYXNzIFRhYmxlU29ydGluZyB7XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSB0YWJsZVxuICAgKi9cbiAgY29uc3RydWN0b3IodGFibGUpIHtcbiAgICB0aGlzLnNlbGVjdG9yID0gJy5wcy1zb3J0YWJsZS1jb2x1bW4nO1xuICAgIHRoaXMuY29sdW1ucyA9ICQodGFibGUpLmZpbmQodGhpcy5zZWxlY3Rvcik7XG4gIH1cblxuICAvKipcbiAgICogQXR0YWNoZXMgdGhlIGxpc3RlbmVyc1xuICAgKi9cbiAgYXR0YWNoKCkge1xuICAgIHRoaXMuY29sdW1ucy5vbignY2xpY2snLCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGNvbHVtbiA9ICQoZS5kZWxlZ2F0ZVRhcmdldCk7XG4gICAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgdGhpcy5fZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24oJGNvbHVtbikpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFNvcnQgdXNpbmcgYSBjb2x1bW4gbmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gY29sdW1uTmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICovXG4gIHNvcnRCeShjb2x1bW5OYW1lLCBkaXJlY3Rpb24pIHtcbiAgICBjb25zdCAkY29sdW1uID0gdGhpcy5jb2x1bW5zLmlzKGBbZGF0YS1zb3J0LWNvbC1uYW1lPVwiJHtjb2x1bW5OYW1lfVwiXWApO1xuICAgIGlmICghJGNvbHVtbikge1xuICAgICAgdGhyb3cgbmV3IEVycm9yKGBDYW5ub3Qgc29ydCBieSBcIiR7Y29sdW1uTmFtZX1cIjogaW52YWxpZCBjb2x1bW5gKTtcbiAgICB9XG5cbiAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgZGlyZWN0aW9uKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTb3J0IHVzaW5nIGEgY29sdW1uIGVsZW1lbnRcbiAgICogQHBhcmFtIHtqUXVlcnl9IGNvbHVtblxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zb3J0QnlDb2x1bW4oY29sdW1uLCBkaXJlY3Rpb24pIHtcbiAgICB3aW5kb3cubG9jYXRpb24gPSB0aGlzLl9nZXRVcmwoY29sdW1uLmRhdGEoJ3NvcnRDb2xOYW1lJyksIChkaXJlY3Rpb24gPT09ICdkZXNjJykgPyAnZGVzYycgOiAnYXNjJywgY29sdW1uLmRhdGEoJ3NvcnRQcmVmaXgnKSk7XG4gIH1cblxuICAvKipcbiAgICogUmV0dXJucyB0aGUgaW52ZXJ0ZWQgZGlyZWN0aW9uIHRvIHNvcnQgYWNjb3JkaW5nIHRvIHRoZSBjb2x1bW4ncyBjdXJyZW50IG9uZVxuICAgKiBAcGFyYW0ge2pRdWVyeX0gY29sdW1uXG4gICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRUb2dnbGVkU29ydERpcmVjdGlvbihjb2x1bW4pIHtcbiAgICByZXR1cm4gY29sdW1uLmRhdGEoJ3NvcnREaXJlY3Rpb24nKSA9PT0gJ2FzYycgPyAnZGVzYycgOiAnYXNjJztcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXR1cm5zIHRoZSB1cmwgZm9yIHRoZSBzb3J0ZWQgdGFibGVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbE5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvblxuICAgKiBAcGFyYW0ge3N0cmluZ30gcHJlZml4XG4gICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRVcmwoY29sTmFtZSwgZGlyZWN0aW9uLCBwcmVmaXgpIHtcbiAgICBjb25zdCB1cmwgPSBuZXcgVVJMKHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcbiAgICBjb25zdCBwYXJhbXMgPSB1cmwuc2VhcmNoUGFyYW1zO1xuXG4gICAgaWYgKHByZWZpeCkge1xuICAgICAgcGFyYW1zLnNldChwcmVmaXgrJ1tvcmRlckJ5XScsIGNvbE5hbWUpO1xuICAgICAgcGFyYW1zLnNldChwcmVmaXgrJ1tzb3J0T3JkZXJdJywgZGlyZWN0aW9uKTtcbiAgICB9IGVsc2Uge1xuICAgICAgcGFyYW1zLnNldCgnb3JkZXJCeScsIGNvbE5hbWUpO1xuICAgICAgcGFyYW1zLnNldCgnc29ydE9yZGVyJywgZGlyZWN0aW9uKTtcbiAgICB9XG5cbiAgICByZXR1cm4gdXJsLnRvU3RyaW5nKCk7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgVGFibGVTb3J0aW5nO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL3V0aWxzL3RhYmxlLXNvcnRpbmcuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBUb2dnbGVzIGhpZGRlbiBwcm9kdWN0cyBpbiBvcmRlciBwcmV2aWV3IGJsb2NrLlxuICpcbiAqIEBwYXJhbSB7alF1ZXJ5fSAkZ3JpZENvbnRhaW5lclxuICovXG5leHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBwcmV2aWV3UHJvZHVjdHNUb2dnbGVyKCRyb3cpIHtcbiAgdG9nZ2xlU3RvY2tMb2NhdGlvbkNvbHVtbigkcm93KTtcbiAgJHJvdy5vbignY2xpY2snLCAnLmpzLXByZXZpZXctbW9yZS1wcm9kdWN0cy1idG4nLCAoZXZlbnQpID0+IHtcbiAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgY29uc3QgJGhpZGRlblByb2R1Y3RzID0gJGJ0bi5jbG9zZXN0KCd0Ym9keScpLmZpbmQoJy5qcy1wcm9kdWN0LXByZXZpZXctbW9yZScpO1xuXG4gICAgJGhpZGRlblByb2R1Y3RzLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAkYnRuLmNsb3Nlc3QoJ3RyJykucmVtb3ZlKCk7XG4gICAgdG9nZ2xlU3RvY2tMb2NhdGlvbkNvbHVtbigkcm93KTtcbiAgfSk7XG59XG5cbmZ1bmN0aW9uIHRvZ2dsZVN0b2NrTG9jYXRpb25Db2x1bW4oJGNvbnRhaW5lcikge1xuICBsZXQgc2hvd0NvbHVtbiA9IGZhbHNlO1xuICAkKCcuanMtY2VsbC1wcm9kdWN0LXN0b2NrLWxvY2F0aW9uJywgJGNvbnRhaW5lci5maW5kKCd0cjpub3QoLmQtbm9uZSknKSkuZmlsdGVyKCd0ZCcpLmVhY2goKGluZGV4LCBlbGVtZW50KSA9PiB7XG4gICAgaWYgKCQoZWxlbWVudCkuaHRtbCgpLnRyaW0oKSAhPT0gJycpIHtcbiAgICAgIHNob3dDb2x1bW4gPSB0cnVlO1xuICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cbiAgICByZXR1cm4gdHJ1ZTtcbiAgfSk7XG4gICQoJy5qcy1jZWxsLXByb2R1Y3Qtc3RvY2stbG9jYXRpb24nLCAkY29udGFpbmVyKS50b2dnbGUoc2hvd0NvbHVtbik7XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9wcmV2aWV3LXByb2R1Y3RzLXRvZ2dsZXIuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbi8qKlxuICogU2VuZCBhIFBvc3QgUmVxdWVzdCB0byByZXNldCBzZWFyY2ggQWN0aW9uLlxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuY29uc3QgaW5pdCA9IGZ1bmN0aW9uIHJlc2V0U2VhcmNoKHVybCwgcmVkaXJlY3RVcmwpIHtcbiAgICAkLnBvc3QodXJsKS50aGVuKCgpID0+IHdpbmRvdy5sb2NhdGlvbi5hc3NpZ24ocmVkaXJlY3RVcmwpKTtcbn07XG5cbmV4cG9ydCBkZWZhdWx0IGluaXQ7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvdXRpbHMvcmVzZXRfc2VhcmNoLmpzIiwiLy8gNy4xLjEzIFRvT2JqZWN0KGFyZ3VtZW50KVxudmFyIGRlZmluZWQgPSByZXF1aXJlKCcuL19kZWZpbmVkJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIE9iamVjdChkZWZpbmVkKGl0KSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8tb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSA0NFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsInZhciBzaGFyZWQgPSByZXF1aXJlKCcuL19zaGFyZWQnKSgna2V5cycpXG4gICwgdWlkICAgID0gcmVxdWlyZSgnLi9fdWlkJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGtleSl7XG4gIHJldHVybiBzaGFyZWRba2V5XSB8fCAoc2hhcmVkW2tleV0gPSB1aWQoa2V5KSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fc2hhcmVkLWtleS5qc1xuLy8gbW9kdWxlIGlkID0gNDVcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDb25maXJtTW9kYWwgY29tcG9uZW50XG4gKlxuICogQHBhcmFtIHtTdHJpbmd9IGlkXG4gKiBAcGFyYW0ge1N0cmluZ30gY29uZmlybVRpdGxlXG4gKiBAcGFyYW0ge1N0cmluZ30gY29uZmlybU1lc3NhZ2VcbiAqIEBwYXJhbSB7U3RyaW5nfSBjbG9zZUJ1dHRvbkxhYmVsXG4gKiBAcGFyYW0ge1N0cmluZ30gY29uZmlybUJ1dHRvbkxhYmVsXG4gKiBAcGFyYW0ge1N0cmluZ30gY29uZmlybUJ1dHRvbkNsYXNzXG4gKiBAcGFyYW0ge0Jvb2xlYW59IGNsb3NhYmxlXG4gKiBAcGFyYW0ge0Z1bmN0aW9ufSBjb25maXJtQ2FsbGJhY2tcbiAqXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIENvbmZpcm1Nb2RhbChwYXJhbXMsIGNvbmZpcm1DYWxsYmFjaykge1xuICAvLyBDb25zdHJ1Y3QgdGhlIG1vZGFsXG4gIGNvbnN0IHtpZCwgY2xvc2FibGV9ID0gcGFyYW1zO1xuICB0aGlzLm1vZGFsID0gTW9kYWwocGFyYW1zKTtcblxuICAvLyBqUXVlcnkgbW9kYWwgb2JqZWN0XG4gIHRoaXMuJG1vZGFsID0gJCh0aGlzLm1vZGFsLmNvbnRhaW5lcik7XG5cbiAgdGhpcy5zaG93ID0gKCkgPT4ge1xuICAgIHRoaXMuJG1vZGFsLm1vZGFsKCk7XG4gIH07XG5cbiAgdGhpcy5tb2RhbC5jb25maXJtQnV0dG9uLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgY29uZmlybUNhbGxiYWNrKTtcblxuICB0aGlzLiRtb2RhbC5tb2RhbCh7XG4gICAgYmFja2Ryb3A6IChjbG9zYWJsZSA/IHRydWUgOiAnc3RhdGljJyksXG4gICAga2V5Ym9hcmQ6IGNsb3NhYmxlICE9PSB1bmRlZmluZWQgPyBjbG9zYWJsZSA6IHRydWUsXG4gICAgY2xvc2FibGU6IGNsb3NhYmxlICE9PSB1bmRlZmluZWQgPyBjbG9zYWJsZSA6IHRydWUsXG4gICAgc2hvdzogZmFsc2UsXG4gIH0pO1xuXG4gIHRoaXMuJG1vZGFsLm9uKCdoaWRkZW4uYnMubW9kYWwnLCAoKSA9PiB7XG4gICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvcihgIyR7aWR9YCkucmVtb3ZlKCk7XG4gIH0pO1xuXG4gIGRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQodGhpcy5tb2RhbC5jb250YWluZXIpO1xufVxuXG4vKipcbiAqIE1vZGFsIGNvbXBvbmVudCB0byBpbXByb3ZlIGxpc2liaWxpdHkgYnkgY29uc3RydWN0aW5nIHRoZSBtb2RhbCBvdXRzaWRlIHRoZSBtYWluIGZ1bmN0aW9uXG4gKlxuICogQHBhcmFtIHtPYmplY3R9IHBhcmFtc1xuICpcbiAqL1xuZnVuY3Rpb24gTW9kYWwoXG4gIHtcbiAgICBpZCA9ICdjb25maXJtX21vZGFsJyxcbiAgICBjb25maXJtVGl0bGUsXG4gICAgY29uZmlybU1lc3NhZ2UgPSAnJyxcbiAgICBjbG9zZUJ1dHRvbkxhYmVsID0gJ0Nsb3NlJyxcbiAgICBjb25maXJtQnV0dG9uTGFiZWwgPSAnQWNjZXB0JyxcbiAgICBjb25maXJtQnV0dG9uQ2xhc3MgPSAnYnRuLXByaW1hcnknLFxuICB9KSB7XG4gIGNvbnN0IG1vZGFsID0ge307XG5cbiAgLy8gTWFpbiBtb2RhbCBlbGVtZW50XG4gIG1vZGFsLmNvbnRhaW5lciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICBtb2RhbC5jb250YWluZXIuY2xhc3NMaXN0LmFkZCgnbW9kYWwnLCAnZmFkZScpO1xuICBtb2RhbC5jb250YWluZXIuaWQgPSBpZDtcblxuICAvLyBNb2RhbCBkaWFsb2cgZWxlbWVudFxuICBtb2RhbC5kaWFsb2cgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuZGlhbG9nLmNsYXNzTGlzdC5hZGQoJ21vZGFsLWRpYWxvZycpO1xuXG4gIC8vIE1vZGFsIGNvbnRlbnQgZWxlbWVudFxuICBtb2RhbC5jb250ZW50ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gIG1vZGFsLmNvbnRlbnQuY2xhc3NMaXN0LmFkZCgnbW9kYWwtY29udGVudCcpO1xuXG4gIC8vIE1vZGFsIGhlYWRlciBlbGVtZW50XG4gIG1vZGFsLmhlYWRlciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICBtb2RhbC5oZWFkZXIuY2xhc3NMaXN0LmFkZCgnbW9kYWwtaGVhZGVyJyk7XG5cbiAgLy8gTW9kYWwgdGl0bGUgZWxlbWVudFxuICBpZiAoY29uZmlybVRpdGxlKSB7XG4gICAgbW9kYWwudGl0bGUgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdoNCcpO1xuICAgIG1vZGFsLnRpdGxlLmNsYXNzTGlzdC5hZGQoJ21vZGFsLXRpdGxlJyk7XG4gICAgbW9kYWwudGl0bGUuaW5uZXJIVE1MID0gY29uZmlybVRpdGxlO1xuICB9XG5cbiAgLy8gTW9kYWwgY2xvc2UgYnV0dG9uIGljb25cbiAgbW9kYWwuY2xvc2VJY29uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYnV0dG9uJyk7XG4gIG1vZGFsLmNsb3NlSWNvbi5jbGFzc0xpc3QuYWRkKCdjbG9zZScpO1xuICBtb2RhbC5jbG9zZUljb24uc2V0QXR0cmlidXRlKCd0eXBlJywgJ2J1dHRvbicpO1xuICBtb2RhbC5jbG9zZUljb24uZGF0YXNldC5kaXNtaXNzID0gJ21vZGFsJztcbiAgbW9kYWwuY2xvc2VJY29uLmlubmVySFRNTCA9ICfDlyc7XG5cbiAgLy8gTW9kYWwgYm9keSBlbGVtZW50XG4gIG1vZGFsLmJvZHkgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuYm9keS5jbGFzc0xpc3QuYWRkKCdtb2RhbC1ib2R5JywgJ3RleHQtbGVmdCcsICdmb250LXdlaWdodC1ub3JtYWwnKTtcblxuICAvLyBNb2RhbCBtZXNzYWdlIGVsZW1lbnRcbiAgbW9kYWwubWVzc2FnZSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ3AnKTtcbiAgbW9kYWwubWVzc2FnZS5jbGFzc0xpc3QuYWRkKCdjb25maXJtLW1lc3NhZ2UnKTtcbiAgbW9kYWwubWVzc2FnZS5pbm5lckhUTUwgPSBjb25maXJtTWVzc2FnZTtcblxuICAvLyBNb2RhbCBmb290ZXIgZWxlbWVudFxuICBtb2RhbC5mb290ZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuZm9vdGVyLmNsYXNzTGlzdC5hZGQoJ21vZGFsLWZvb3RlcicpO1xuXG4gIC8vIE1vZGFsIGNsb3NlIGJ1dHRvbiBlbGVtZW50XG4gIG1vZGFsLmNsb3NlQnV0dG9uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYnV0dG9uJyk7XG4gIG1vZGFsLmNsb3NlQnV0dG9uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgbW9kYWwuY2xvc2VCdXR0b24uY2xhc3NMaXN0LmFkZCgnYnRuJywgJ2J0bi1vdXRsaW5lLXNlY29uZGFyeScsICdidG4tbGcnKTtcbiAgbW9kYWwuY2xvc2VCdXR0b24uZGF0YXNldC5kaXNtaXNzID0gJ21vZGFsJztcbiAgbW9kYWwuY2xvc2VCdXR0b24uaW5uZXJIVE1MID0gY2xvc2VCdXR0b25MYWJlbDtcblxuICAvLyBNb2RhbCBjbG9zZSBidXR0b24gZWxlbWVudFxuICBtb2RhbC5jb25maXJtQnV0dG9uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYnV0dG9uJyk7XG4gIG1vZGFsLmNvbmZpcm1CdXR0b24uc2V0QXR0cmlidXRlKCd0eXBlJywgJ2J1dHRvbicpO1xuICBtb2RhbC5jb25maXJtQnV0dG9uLmNsYXNzTGlzdC5hZGQoJ2J0bicsIGNvbmZpcm1CdXR0b25DbGFzcywgJ2J0bi1sZycsICdidG4tY29uZmlybS1zdWJtaXQnKTtcbiAgbW9kYWwuY29uZmlybUJ1dHRvbi5kYXRhc2V0LmRpc21pc3MgPSAnbW9kYWwnO1xuICBtb2RhbC5jb25maXJtQnV0dG9uLmlubmVySFRNTCA9IGNvbmZpcm1CdXR0b25MYWJlbDtcblxuICAvLyBDb25zdHJ1Y3RpbmcgdGhlIG1vZGFsXG4gIGlmIChjb25maXJtVGl0bGUpIHtcbiAgICBtb2RhbC5oZWFkZXIuYXBwZW5kKG1vZGFsLnRpdGxlLCBtb2RhbC5jbG9zZUljb24pO1xuICB9IGVsc2Uge1xuICAgIG1vZGFsLmhlYWRlci5hcHBlbmRDaGlsZChtb2RhbC5jbG9zZUljb24pO1xuICB9XG5cbiAgbW9kYWwuYm9keS5hcHBlbmRDaGlsZChtb2RhbC5tZXNzYWdlKTtcbiAgbW9kYWwuZm9vdGVyLmFwcGVuZChtb2RhbC5jbG9zZUJ1dHRvbiwgbW9kYWwuY29uZmlybUJ1dHRvbik7XG4gIG1vZGFsLmNvbnRlbnQuYXBwZW5kKG1vZGFsLmhlYWRlciwgbW9kYWwuYm9keSwgbW9kYWwuZm9vdGVyKTtcbiAgbW9kYWwuZGlhbG9nLmFwcGVuZENoaWxkKG1vZGFsLmNvbnRlbnQpO1xuICBtb2RhbC5jb250YWluZXIuYXBwZW5kQ2hpbGQobW9kYWwuZGlhbG9nKTtcblxuICByZXR1cm4gbW9kYWw7XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL21vZGFsLmpzIiwidmFyIHRvU3RyaW5nID0ge30udG9TdHJpbmc7XG5cbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gdG9TdHJpbmcuY2FsbChpdCkuc2xpY2UoOCwgLTEpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvZi5qc1xuLy8gbW9kdWxlIGlkID0gNDhcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCIvLyBJRSA4LSBkb24ndCBlbnVtIGJ1ZyBrZXlzXG5tb2R1bGUuZXhwb3J0cyA9IChcbiAgJ2NvbnN0cnVjdG9yLGhhc093blByb3BlcnR5LGlzUHJvdG90eXBlT2YscHJvcGVydHlJc0VudW1lcmFibGUsdG9Mb2NhbGVTdHJpbmcsdG9TdHJpbmcsdmFsdWVPZidcbikuc3BsaXQoJywnKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2VudW0tYnVnLWtleXMuanNcbi8vIG1vZHVsZSBpZCA9IDQ5XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLy8gaHR0cHM6Ly9naXRodWIuY29tL3psb2lyb2NrL2NvcmUtanMvaXNzdWVzLzg2I2lzc3VlY29tbWVudC0xMTU3NTkwMjhcbnZhciBnbG9iYWwgPSBtb2R1bGUuZXhwb3J0cyA9IHR5cGVvZiB3aW5kb3cgIT0gJ3VuZGVmaW5lZCcgJiYgd2luZG93Lk1hdGggPT0gTWF0aFxuICA/IHdpbmRvdyA6IHR5cGVvZiBzZWxmICE9ICd1bmRlZmluZWQnICYmIHNlbGYuTWF0aCA9PSBNYXRoID8gc2VsZiA6IEZ1bmN0aW9uKCdyZXR1cm4gdGhpcycpKCk7XG5pZih0eXBlb2YgX19nID09ICdudW1iZXInKV9fZyA9IGdsb2JhbDsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBuby11bmRlZlxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSA1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBnbG9iYWwgPSByZXF1aXJlKCcuL19nbG9iYWwnKVxuICAsIFNIQVJFRCA9ICdfX2NvcmUtanNfc2hhcmVkX18nXG4gICwgc3RvcmUgID0gZ2xvYmFsW1NIQVJFRF0gfHwgKGdsb2JhbFtTSEFSRURdID0ge30pO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihrZXkpe1xuICByZXR1cm4gc3RvcmVba2V5XSB8fCAoc3RvcmVba2V5XSA9IHt9KTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19zaGFyZWQuanNcbi8vIG1vZHVsZSBpZCA9IDUwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLy8gZmFsbGJhY2sgZm9yIG5vbi1hcnJheS1saWtlIEVTMyBhbmQgbm9uLWVudW1lcmFibGUgb2xkIFY4IHN0cmluZ3NcbnZhciBjb2YgPSByZXF1aXJlKCcuL19jb2YnKTtcbm1vZHVsZS5leHBvcnRzID0gT2JqZWN0KCd6JykucHJvcGVydHlJc0VudW1lcmFibGUoMCkgPyBPYmplY3QgOiBmdW5jdGlvbihpdCl7XG4gIHJldHVybiBjb2YoaXQpID09ICdTdHJpbmcnID8gaXQuc3BsaXQoJycpIDogT2JqZWN0KGl0KTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSA1MlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IEdyaWQgZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9ncmlkJztcbmltcG9ydCBGaWx0ZXJzUmVzZXRFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1yZXNldC1leHRlbnNpb24nO1xuaW1wb3J0IFJlbG9hZExpc3RBY3Rpb25FeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uJztcbmltcG9ydCBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvbic7XG5pbXBvcnQgU29ydGluZ0V4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zb3J0aW5nLWV4dGVuc2lvbic7XG5pbXBvcnQgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9saW5rLXJvdy1hY3Rpb24tZXh0ZW5zaW9uJztcbmltcG9ydCBTdWJtaXRHcmlkRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1ncmlkLWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IFN1Ym1pdEJ1bGtFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWJ1bGstYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2J1bGstYWN0aW9uLWNoZWNrYm94LWV4dGVuc2lvbic7XG5pbXBvcnQgRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb25cbiAgZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1zdWJtaXQtYnV0dG9uLWVuYWJsZXItZXh0ZW5zaW9uJztcbmltcG9ydCBDaG9pY2VFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vY2hvaWNlLWV4dGVuc2lvbic7XG5pbXBvcnQgTW9kYWxGb3JtU3VibWl0RXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL21vZGFsLWZvcm0tc3VibWl0LWV4dGVuc2lvbic7XG5pbXBvcnQgUHJldmlld0V4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9wcmV2aWV3LWV4dGVuc2lvbic7XG5pbXBvcnQgcHJldmlld1Byb2R1Y3RzVG9nZ2xlciBmcm9tICdAcGFnZXMvb3JkZXIvcHJldmlldy1wcm9kdWN0cy10b2dnbGVyJztcbmltcG9ydCBCdWxrT3BlblRhYnNFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYnVsay1vcGVuLXRhYnMtZXh0ZW5zaW9uJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgY29uc3Qgb3JkZXJHcmlkID0gbmV3IEdyaWQoJ29yZGVyJyk7XG4gIG9yZGVyR3JpZC5hZGRFeHRlbnNpb24obmV3IFJlbG9hZExpc3RBY3Rpb25FeHRlbnNpb24oKSk7XG4gIG9yZGVyR3JpZC5hZGRFeHRlbnNpb24obmV3IEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbigpKTtcbiAgb3JkZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uKCkpO1xuICBvcmRlckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTb3J0aW5nRXh0ZW5zaW9uKCkpO1xuICBvcmRlckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uKCkpO1xuICBvcmRlckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTdWJtaXRHcmlkRXh0ZW5zaW9uKCkpO1xuICBvcmRlckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTdWJtaXRCdWxrRXh0ZW5zaW9uKCkpO1xuICBvcmRlckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24oKSk7XG4gIG9yZGVyR3JpZC5hZGRFeHRlbnNpb24obmV3IEZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uKCkpO1xuICBvcmRlckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBNb2RhbEZvcm1TdWJtaXRFeHRlbnNpb24oKSk7XG4gIG9yZGVyR3JpZC5hZGRFeHRlbnNpb24obmV3IENob2ljZUV4dGVuc2lvbigpKTtcbiAgb3JkZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgUHJldmlld0V4dGVuc2lvbihwcmV2aWV3UHJvZHVjdHNUb2dnbGVyKSk7XG4gIG9yZGVyR3JpZC5hZGRFeHRlbnNpb24obmV3IEJ1bGtPcGVuVGFic0V4dGVuc2lvbigpKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvaW5kZXguanMiLCIvLyA3LjEuMTUgVG9MZW5ndGhcbnZhciB0b0ludGVnZXIgPSByZXF1aXJlKCcuL190by1pbnRlZ2VyJylcbiAgLCBtaW4gICAgICAgPSBNYXRoLm1pbjtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gaXQgPiAwID8gbWluKHRvSW50ZWdlcihpdCksIDB4MWZmZmZmZmZmZmZmZmYpIDogMDsgLy8gcG93KDIsIDUzKSAtIDEgPT0gOTAwNzE5OTI1NDc0MDk5MVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLWxlbmd0aC5qc1xuLy8gbW9kdWxlIGlkID0gNTNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJleHBvcnRzLmYgPSB7fS5wcm9wZXJ0eUlzRW51bWVyYWJsZTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1waWUuanNcbi8vIG1vZHVsZSBpZCA9IDU0XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNSAxNiAxOCIsInZhciBoYXMgICAgICAgICAgPSByZXF1aXJlKCcuL19oYXMnKVxuICAsIHRvSU9iamVjdCAgICA9IHJlcXVpcmUoJy4vX3RvLWlvYmplY3QnKVxuICAsIGFycmF5SW5kZXhPZiA9IHJlcXVpcmUoJy4vX2FycmF5LWluY2x1ZGVzJykoZmFsc2UpXG4gICwgSUVfUFJPVE8gICAgID0gcmVxdWlyZSgnLi9fc2hhcmVkLWtleScpKCdJRV9QUk9UTycpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKG9iamVjdCwgbmFtZXMpe1xuICB2YXIgTyAgICAgID0gdG9JT2JqZWN0KG9iamVjdClcbiAgICAsIGkgICAgICA9IDBcbiAgICAsIHJlc3VsdCA9IFtdXG4gICAgLCBrZXk7XG4gIGZvcihrZXkgaW4gTylpZihrZXkgIT0gSUVfUFJPVE8paGFzKE8sIGtleSkgJiYgcmVzdWx0LnB1c2goa2V5KTtcbiAgLy8gRG9uJ3QgZW51bSBidWcgJiBoaWRkZW4ga2V5c1xuICB3aGlsZShuYW1lcy5sZW5ndGggPiBpKWlmKGhhcyhPLCBrZXkgPSBuYW1lc1tpKytdKSl7XG4gICAgfmFycmF5SW5kZXhPZihyZXN1bHQsIGtleSkgfHwgcmVzdWx0LnB1c2goa2V5KTtcbiAgfVxuICByZXR1cm4gcmVzdWx0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1rZXlzLWludGVybmFsLmpzXG4vLyBtb2R1bGUgaWQgPSA1NVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIi8vIGZhbHNlIC0+IEFycmF5I2luZGV4T2Zcbi8vIHRydWUgIC0+IEFycmF5I2luY2x1ZGVzXG52YXIgdG9JT2JqZWN0ID0gcmVxdWlyZSgnLi9fdG8taW9iamVjdCcpXG4gICwgdG9MZW5ndGggID0gcmVxdWlyZSgnLi9fdG8tbGVuZ3RoJylcbiAgLCB0b0luZGV4ICAgPSByZXF1aXJlKCcuL190by1pbmRleCcpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihJU19JTkNMVURFUyl7XG4gIHJldHVybiBmdW5jdGlvbigkdGhpcywgZWwsIGZyb21JbmRleCl7XG4gICAgdmFyIE8gICAgICA9IHRvSU9iamVjdCgkdGhpcylcbiAgICAgICwgbGVuZ3RoID0gdG9MZW5ndGgoTy5sZW5ndGgpXG4gICAgICAsIGluZGV4ICA9IHRvSW5kZXgoZnJvbUluZGV4LCBsZW5ndGgpXG4gICAgICAsIHZhbHVlO1xuICAgIC8vIEFycmF5I2luY2x1ZGVzIHVzZXMgU2FtZVZhbHVlWmVybyBlcXVhbGl0eSBhbGdvcml0aG1cbiAgICBpZihJU19JTkNMVURFUyAmJiBlbCAhPSBlbCl3aGlsZShsZW5ndGggPiBpbmRleCl7XG4gICAgICB2YWx1ZSA9IE9baW5kZXgrK107XG4gICAgICBpZih2YWx1ZSAhPSB2YWx1ZSlyZXR1cm4gdHJ1ZTtcbiAgICAvLyBBcnJheSN0b0luZGV4IGlnbm9yZXMgaG9sZXMsIEFycmF5I2luY2x1ZGVzIC0gbm90XG4gICAgfSBlbHNlIGZvcig7bGVuZ3RoID4gaW5kZXg7IGluZGV4KyspaWYoSVNfSU5DTFVERVMgfHwgaW5kZXggaW4gTyl7XG4gICAgICBpZihPW2luZGV4XSA9PT0gZWwpcmV0dXJuIElTX0lOQ0xVREVTIHx8IGluZGV4IHx8IDA7XG4gICAgfSByZXR1cm4gIUlTX0lOQ0xVREVTICYmIC0xO1xuICB9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2FycmF5LWluY2x1ZGVzLmpzXG4vLyBtb2R1bGUgaWQgPSA1N1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsInZhciB0b0ludGVnZXIgPSByZXF1aXJlKCcuL190by1pbnRlZ2VyJylcbiAgLCBtYXggICAgICAgPSBNYXRoLm1heFxuICAsIG1pbiAgICAgICA9IE1hdGgubWluO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpbmRleCwgbGVuZ3RoKXtcbiAgaW5kZXggPSB0b0ludGVnZXIoaW5kZXgpO1xuICByZXR1cm4gaW5kZXggPCAwID8gbWF4KGluZGV4ICsgbGVuZ3RoLCAwKSA6IG1pbihpbmRleCwgbGVuZ3RoKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1pbmRleC5qc1xuLy8gbW9kdWxlIGlkID0gNThcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJleHBvcnRzLmYgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdvcHMuanNcbi8vIG1vZHVsZSBpZCA9IDU5XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNSAxNiAxOCIsInZhciBhbk9iamVjdCAgICAgICA9IHJlcXVpcmUoJy4vX2FuLW9iamVjdCcpXG4gICwgSUU4X0RPTV9ERUZJTkUgPSByZXF1aXJlKCcuL19pZTgtZG9tLWRlZmluZScpXG4gICwgdG9QcmltaXRpdmUgICAgPSByZXF1aXJlKCcuL190by1wcmltaXRpdmUnKVxuICAsIGRQICAgICAgICAgICAgID0gT2JqZWN0LmRlZmluZVByb3BlcnR5O1xuXG5leHBvcnRzLmYgPSByZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpID8gT2JqZWN0LmRlZmluZVByb3BlcnR5IDogZnVuY3Rpb24gZGVmaW5lUHJvcGVydHkoTywgUCwgQXR0cmlidXRlcyl7XG4gIGFuT2JqZWN0KE8pO1xuICBQID0gdG9QcmltaXRpdmUoUCwgdHJ1ZSk7XG4gIGFuT2JqZWN0KEF0dHJpYnV0ZXMpO1xuICBpZihJRThfRE9NX0RFRklORSl0cnkge1xuICAgIHJldHVybiBkUChPLCBQLCBBdHRyaWJ1dGVzKTtcbiAgfSBjYXRjaChlKXsgLyogZW1wdHkgKi8gfVxuICBpZignZ2V0JyBpbiBBdHRyaWJ1dGVzIHx8ICdzZXQnIGluIEF0dHJpYnV0ZXMpdGhyb3cgVHlwZUVycm9yKCdBY2Nlc3NvcnMgbm90IHN1cHBvcnRlZCEnKTtcbiAgaWYoJ3ZhbHVlJyBpbiBBdHRyaWJ1dGVzKU9bUF0gPSBBdHRyaWJ1dGVzLnZhbHVlO1xuICByZXR1cm4gTztcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZHAuanNcbi8vIG1vZHVsZSBpZCA9IDZcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgUm91dGluZyBmcm9tICdmb3Mtcm91dGluZyc7XG5pbXBvcnQgcm91dGVzIGZyb20gJ0Bqcy9mb3NfanNfcm91dGVzLmpzb24nO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogV3JhcHMgRk9TSnNSb3V0aW5nYnVuZGxlIHdpdGggZXhwb3NlZCByb3V0ZXMuXG4gKiBUbyBleHBvc2Ugcm91dGUgYWRkIG9wdGlvbiBgZXhwb3NlOiB0cnVlYCBpbiAueW1sIHJvdXRpbmcgY29uZmlnXG4gKlxuICogZS5nLlxuICpcbiAqIGBteV9yb3V0ZVxuICogICAgcGF0aDogL215LXBhdGhcbiAqICAgIG9wdGlvbnM6XG4gKiAgICAgIGV4cG9zZTogdHJ1ZVxuICogYFxuICogQW5kIHJ1biBgYmluL2NvbnNvbGUgZm9zOmpzLXJvdXRpbmc6ZHVtcCAtLWZvcm1hdD1qc29uIC0tdGFyZ2V0PWFkbWluLWRldi90aGVtZXMvbmV3LXRoZW1lL2pzL2Zvc19qc19yb3V0ZXMuanNvbmBcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUm91dGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgUm91dGluZy5zZXREYXRhKHJvdXRlcyk7XG4gICAgUm91dGluZy5zZXRCYXNlVXJsKCQoZG9jdW1lbnQpLmZpbmQoJ2JvZHknKS5kYXRhKCdiYXNlLXVybCcpKTtcblxuICAgIHJldHVybiB0aGlzO1xuICB9XG5cbiAgLyoqXG4gICAqIERlY29yYXRlZCBcImdlbmVyYXRlXCIgbWV0aG9kLCB3aXRoIHByZWRlZmluZWQgc2VjdXJpdHkgdG9rZW4gaW4gcGFyYW1zXG4gICAqXG4gICAqIEBwYXJhbSByb3V0ZVxuICAgKiBAcGFyYW0gcGFyYW1zXG4gICAqXG4gICAqIEByZXR1cm5zIHtTdHJpbmd9XG4gICAqL1xuICBnZW5lcmF0ZShyb3V0ZSwgcGFyYW1zID0ge30pIHtcbiAgICBjb25zdCB0b2tlbml6ZWRQYXJhbXMgPSBPYmplY3QuYXNzaWduKHBhcmFtcywge190b2tlbjogJChkb2N1bWVudCkuZmluZCgnYm9keScpLmRhdGEoJ3Rva2VuJyl9KTtcblxuICAgIHJldHVybiBSb3V0aW5nLmdlbmVyYXRlKHJvdXRlLCB0b2tlbml6ZWRQYXJhbXMpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL3JvdXRlci5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIFN1Ym1pdEdyaWRBY3Rpb25FeHRlbnNpb24gaGFuZGxlcyBncmlkIGFjdGlvbiBzdWJtaXRzXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFN1Ym1pdEdyaWRBY3Rpb25FeHRlbnNpb24ge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICByZXR1cm4ge1xuICAgICAgZXh0ZW5kOiAoZ3JpZCkgPT4gdGhpcy5leHRlbmQoZ3JpZClcbiAgICB9O1xuICB9XG5cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtZ3JpZC1hY3Rpb24tc3VibWl0LWJ0bicsIChldmVudCkgPT4ge1xuICAgICAgdGhpcy5oYW5kbGVTdWJtaXQoZXZlbnQsIGdyaWQpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZSBncmlkIGFjdGlvbiBzdWJtaXQuXG4gICAqIEl0IHVzZXMgZ3JpZCBmb3JtIHRvIHN1Ym1pdCBhY3Rpb25zLlxuICAgKlxuICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudFxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGhhbmRsZVN1Ym1pdChldmVudCwgZ3JpZCkge1xuICAgIGNvbnN0ICRzdWJtaXRCdG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJHN1Ym1pdEJ0bi5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgIGlmICh0eXBlb2YgY29uZmlybU1lc3NhZ2UgIT09IFwidW5kZWZpbmVkXCIgJiYgMCA8IGNvbmZpcm1NZXNzYWdlLmxlbmd0aCAmJiAhY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGNvbnN0ICRmb3JtID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2ZpbHRlcl9mb3JtJyk7XG5cbiAgICAkZm9ybS5hdHRyKCdhY3Rpb24nLCAkc3VibWl0QnRuLmRhdGEoJ3VybCcpKTtcbiAgICAkZm9ybS5hdHRyKCdtZXRob2QnLCAkc3VibWl0QnRuLmRhdGEoJ21ldGhvZCcpKTtcbiAgICAkZm9ybS5maW5kKCdpbnB1dFtuYW1lPVwiJyArIGdyaWQuZ2V0SWQoKSArICdbX3Rva2VuXVwiXScpLnZhbCgkc3VibWl0QnRuLmRhdGEoJ2NzcmYnKSk7XG4gICAgJGZvcm0uc3VibWl0KCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWdyaWQtYWN0aW9uLWV4dGVuc2lvbi5qcyIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oZXhlYyl7XG4gIHRyeSB7XG4gICAgcmV0dXJuICEhZXhlYygpO1xuICB9IGNhdGNoKGUpe1xuICAgIHJldHVybiB0cnVlO1xuICB9XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZmFpbHMuanNcbi8vIG1vZHVsZSBpZCA9IDdcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGdsb2JhbCAgICA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpXG4gICwgY29yZSAgICAgID0gcmVxdWlyZSgnLi9fY29yZScpXG4gICwgY3R4ICAgICAgID0gcmVxdWlyZSgnLi9fY3R4JylcbiAgLCBoaWRlICAgICAgPSByZXF1aXJlKCcuL19oaWRlJylcbiAgLCBQUk9UT1RZUEUgPSAncHJvdG90eXBlJztcblxudmFyICRleHBvcnQgPSBmdW5jdGlvbih0eXBlLCBuYW1lLCBzb3VyY2Upe1xuICB2YXIgSVNfRk9SQ0VEID0gdHlwZSAmICRleHBvcnQuRlxuICAgICwgSVNfR0xPQkFMID0gdHlwZSAmICRleHBvcnQuR1xuICAgICwgSVNfU1RBVElDID0gdHlwZSAmICRleHBvcnQuU1xuICAgICwgSVNfUFJPVE8gID0gdHlwZSAmICRleHBvcnQuUFxuICAgICwgSVNfQklORCAgID0gdHlwZSAmICRleHBvcnQuQlxuICAgICwgSVNfV1JBUCAgID0gdHlwZSAmICRleHBvcnQuV1xuICAgICwgZXhwb3J0cyAgID0gSVNfR0xPQkFMID8gY29yZSA6IGNvcmVbbmFtZV0gfHwgKGNvcmVbbmFtZV0gPSB7fSlcbiAgICAsIGV4cFByb3RvICA9IGV4cG9ydHNbUFJPVE9UWVBFXVxuICAgICwgdGFyZ2V0ICAgID0gSVNfR0xPQkFMID8gZ2xvYmFsIDogSVNfU1RBVElDID8gZ2xvYmFsW25hbWVdIDogKGdsb2JhbFtuYW1lXSB8fCB7fSlbUFJPVE9UWVBFXVxuICAgICwga2V5LCBvd24sIG91dDtcbiAgaWYoSVNfR0xPQkFMKXNvdXJjZSA9IG5hbWU7XG4gIGZvcihrZXkgaW4gc291cmNlKXtcbiAgICAvLyBjb250YWlucyBpbiBuYXRpdmVcbiAgICBvd24gPSAhSVNfRk9SQ0VEICYmIHRhcmdldCAmJiB0YXJnZXRba2V5XSAhPT0gdW5kZWZpbmVkO1xuICAgIGlmKG93biAmJiBrZXkgaW4gZXhwb3J0cyljb250aW51ZTtcbiAgICAvLyBleHBvcnQgbmF0aXZlIG9yIHBhc3NlZFxuICAgIG91dCA9IG93biA/IHRhcmdldFtrZXldIDogc291cmNlW2tleV07XG4gICAgLy8gcHJldmVudCBnbG9iYWwgcG9sbHV0aW9uIGZvciBuYW1lc3BhY2VzXG4gICAgZXhwb3J0c1trZXldID0gSVNfR0xPQkFMICYmIHR5cGVvZiB0YXJnZXRba2V5XSAhPSAnZnVuY3Rpb24nID8gc291cmNlW2tleV1cbiAgICAvLyBiaW5kIHRpbWVycyB0byBnbG9iYWwgZm9yIGNhbGwgZnJvbSBleHBvcnQgY29udGV4dFxuICAgIDogSVNfQklORCAmJiBvd24gPyBjdHgob3V0LCBnbG9iYWwpXG4gICAgLy8gd3JhcCBnbG9iYWwgY29uc3RydWN0b3JzIGZvciBwcmV2ZW50IGNoYW5nZSB0aGVtIGluIGxpYnJhcnlcbiAgICA6IElTX1dSQVAgJiYgdGFyZ2V0W2tleV0gPT0gb3V0ID8gKGZ1bmN0aW9uKEMpe1xuICAgICAgdmFyIEYgPSBmdW5jdGlvbihhLCBiLCBjKXtcbiAgICAgICAgaWYodGhpcyBpbnN0YW5jZW9mIEMpe1xuICAgICAgICAgIHN3aXRjaChhcmd1bWVudHMubGVuZ3RoKXtcbiAgICAgICAgICAgIGNhc2UgMDogcmV0dXJuIG5ldyBDO1xuICAgICAgICAgICAgY2FzZSAxOiByZXR1cm4gbmV3IEMoYSk7XG4gICAgICAgICAgICBjYXNlIDI6IHJldHVybiBuZXcgQyhhLCBiKTtcbiAgICAgICAgICB9IHJldHVybiBuZXcgQyhhLCBiLCBjKTtcbiAgICAgICAgfSByZXR1cm4gQy5hcHBseSh0aGlzLCBhcmd1bWVudHMpO1xuICAgICAgfTtcbiAgICAgIEZbUFJPVE9UWVBFXSA9IENbUFJPVE9UWVBFXTtcbiAgICAgIHJldHVybiBGO1xuICAgIC8vIG1ha2Ugc3RhdGljIHZlcnNpb25zIGZvciBwcm90b3R5cGUgbWV0aG9kc1xuICAgIH0pKG91dCkgOiBJU19QUk9UTyAmJiB0eXBlb2Ygb3V0ID09ICdmdW5jdGlvbicgPyBjdHgoRnVuY3Rpb24uY2FsbCwgb3V0KSA6IG91dDtcbiAgICAvLyBleHBvcnQgcHJvdG8gbWV0aG9kcyB0byBjb3JlLiVDT05TVFJVQ1RPUiUubWV0aG9kcy4lTkFNRSVcbiAgICBpZihJU19QUk9UTyl7XG4gICAgICAoZXhwb3J0cy52aXJ0dWFsIHx8IChleHBvcnRzLnZpcnR1YWwgPSB7fSkpW2tleV0gPSBvdXQ7XG4gICAgICAvLyBleHBvcnQgcHJvdG8gbWV0aG9kcyB0byBjb3JlLiVDT05TVFJVQ1RPUiUucHJvdG90eXBlLiVOQU1FJVxuICAgICAgaWYodHlwZSAmICRleHBvcnQuUiAmJiBleHBQcm90byAmJiAhZXhwUHJvdG9ba2V5XSloaWRlKGV4cFByb3RvLCBrZXksIG91dCk7XG4gICAgfVxuICB9XG59O1xuLy8gdHlwZSBiaXRtYXBcbiRleHBvcnQuRiA9IDE7ICAgLy8gZm9yY2VkXG4kZXhwb3J0LkcgPSAyOyAgIC8vIGdsb2JhbFxuJGV4cG9ydC5TID0gNDsgICAvLyBzdGF0aWNcbiRleHBvcnQuUCA9IDg7ICAgLy8gcHJvdG9cbiRleHBvcnQuQiA9IDE2OyAgLy8gYmluZFxuJGV4cG9ydC5XID0gMzI7ICAvLyB3cmFwXG4kZXhwb3J0LlUgPSA2NDsgIC8vIHNhZmVcbiRleHBvcnQuUiA9IDEyODsgLy8gcmVhbCBwcm90byBtZXRob2QgZm9yIGBsaWJyYXJ5YCBcbm1vZHVsZS5leHBvcnRzID0gJGV4cG9ydDtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2V4cG9ydC5qc1xuLy8gbW9kdWxlIGlkID0gOFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2Fzc2lnblwiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2Fzc2lnbi5qc1xuLy8gbW9kdWxlIGlkID0gODNcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDcgMTAgMTEgMTIgMTMgMTUgMTYgMTgiLCJyZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5vYmplY3QuYXNzaWduJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4uLy4uL21vZHVsZXMvX2NvcmUnKS5PYmplY3QuYXNzaWduO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2Fzc2lnbi5qc1xuLy8gbW9kdWxlIGlkID0gODRcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDcgMTAgMTEgMTIgMTMgMTUgMTYgMTgiLCIndXNlIHN0cmljdCc7XG4vLyAxOS4xLjIuMSBPYmplY3QuYXNzaWduKHRhcmdldCwgc291cmNlLCAuLi4pXG52YXIgZ2V0S2V5cyAgPSByZXF1aXJlKCcuL19vYmplY3Qta2V5cycpXG4gICwgZ09QUyAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZ29wcycpXG4gICwgcElFICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtcGllJylcbiAgLCB0b09iamVjdCA9IHJlcXVpcmUoJy4vX3RvLW9iamVjdCcpXG4gICwgSU9iamVjdCAgPSByZXF1aXJlKCcuL19pb2JqZWN0JylcbiAgLCAkYXNzaWduICA9IE9iamVjdC5hc3NpZ247XG5cbi8vIHNob3VsZCB3b3JrIHdpdGggc3ltYm9scyBhbmQgc2hvdWxkIGhhdmUgZGV0ZXJtaW5pc3RpYyBwcm9wZXJ0eSBvcmRlciAoVjggYnVnKVxubW9kdWxlLmV4cG9ydHMgPSAhJGFzc2lnbiB8fCByZXF1aXJlKCcuL19mYWlscycpKGZ1bmN0aW9uKCl7XG4gIHZhciBBID0ge31cbiAgICAsIEIgPSB7fVxuICAgICwgUyA9IFN5bWJvbCgpXG4gICAgLCBLID0gJ2FiY2RlZmdoaWprbG1ub3BxcnN0JztcbiAgQVtTXSA9IDc7XG4gIEsuc3BsaXQoJycpLmZvckVhY2goZnVuY3Rpb24oayl7IEJba10gPSBrOyB9KTtcbiAgcmV0dXJuICRhc3NpZ24oe30sIEEpW1NdICE9IDcgfHwgT2JqZWN0LmtleXMoJGFzc2lnbih7fSwgQikpLmpvaW4oJycpICE9IEs7XG59KSA/IGZ1bmN0aW9uIGFzc2lnbih0YXJnZXQsIHNvdXJjZSl7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tdW51c2VkLXZhcnNcbiAgdmFyIFQgICAgID0gdG9PYmplY3QodGFyZ2V0KVxuICAgICwgYUxlbiAgPSBhcmd1bWVudHMubGVuZ3RoXG4gICAgLCBpbmRleCA9IDFcbiAgICAsIGdldFN5bWJvbHMgPSBnT1BTLmZcbiAgICAsIGlzRW51bSAgICAgPSBwSUUuZjtcbiAgd2hpbGUoYUxlbiA+IGluZGV4KXtcbiAgICB2YXIgUyAgICAgID0gSU9iamVjdChhcmd1bWVudHNbaW5kZXgrK10pXG4gICAgICAsIGtleXMgICA9IGdldFN5bWJvbHMgPyBnZXRLZXlzKFMpLmNvbmNhdChnZXRTeW1ib2xzKFMpKSA6IGdldEtleXMoUylcbiAgICAgICwgbGVuZ3RoID0ga2V5cy5sZW5ndGhcbiAgICAgICwgaiAgICAgID0gMFxuICAgICAgLCBrZXk7XG4gICAgd2hpbGUobGVuZ3RoID4gailpZihpc0VudW0uY2FsbChTLCBrZXkgPSBrZXlzW2orK10pKVRba2V5XSA9IFNba2V5XTtcbiAgfSByZXR1cm4gVDtcbn0gOiAkYXNzaWduO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWFzc2lnbi5qc1xuLy8gbW9kdWxlIGlkID0gODdcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDcgMTAgMTEgMTIgMTMgMTUgMTYgMTgiLCIvLyAxOS4xLjMuMSBPYmplY3QuYXNzaWduKHRhcmdldCwgc291cmNlKVxudmFyICRleHBvcnQgPSByZXF1aXJlKCcuL19leHBvcnQnKTtcblxuJGV4cG9ydCgkZXhwb3J0LlMgKyAkZXhwb3J0LkYsICdPYmplY3QnLCB7YXNzaWduOiByZXF1aXJlKCcuL19vYmplY3QtYXNzaWduJyl9KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5hc3NpZ24uanNcbi8vIG1vZHVsZSBpZCA9IDg5XG4vLyBtb2R1bGUgY2h1bmtzID0gMyA3IDEwIDExIDEyIDEzIDE1IDE2IDE4IiwidmFyIGc7XHJcblxyXG4vLyBUaGlzIHdvcmtzIGluIG5vbi1zdHJpY3QgbW9kZVxyXG5nID0gKGZ1bmN0aW9uKCkge1xyXG5cdHJldHVybiB0aGlzO1xyXG59KSgpO1xyXG5cclxudHJ5IHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIGV2YWwgaXMgYWxsb3dlZCAoc2VlIENTUClcclxuXHRnID0gZyB8fCBGdW5jdGlvbihcInJldHVybiB0aGlzXCIpKCkgfHwgKDEsZXZhbCkoXCJ0aGlzXCIpO1xyXG59IGNhdGNoKGUpIHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIHRoZSB3aW5kb3cgcmVmZXJlbmNlIGlzIGF2YWlsYWJsZVxyXG5cdGlmKHR5cGVvZiB3aW5kb3cgPT09IFwib2JqZWN0XCIpXHJcblx0XHRnID0gd2luZG93O1xyXG59XHJcblxyXG4vLyBnIGNhbiBzdGlsbCBiZSB1bmRlZmluZWQsIGJ1dCBub3RoaW5nIHRvIGRvIGFib3V0IGl0Li4uXHJcbi8vIFdlIHJldHVybiB1bmRlZmluZWQsIGluc3RlYWQgb2Ygbm90aGluZyBoZXJlLCBzbyBpdCdzXHJcbi8vIGVhc2llciB0byBoYW5kbGUgdGhpcyBjYXNlLiBpZighZ2xvYmFsKSB7IC4uLn1cclxuXHJcbm1vZHVsZS5leHBvcnRzID0gZztcclxuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSA5XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDkgMTAgMTEgMTIgMTMgMTcgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDYgNTQiXSwic291cmNlUm9vdCI6IiJ9