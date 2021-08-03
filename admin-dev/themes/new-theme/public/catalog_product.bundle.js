window["catalog_product"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 482);
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

/***/ 3:
/***/ (function(module, exports) {

var core = module.exports = {version: '2.4.0'};
if(typeof __e == 'number')__e = core; // eslint-disable-line no-undef

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

module.exports = function(it){
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};

/***/ }),

/***/ 403:
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

var SpecificPriceFormHandler = function () {
  function SpecificPriceFormHandler() {
    (0, _classCallCheck3.default)(this, SpecificPriceFormHandler);

    this.prefixCreateForm = 'form_step2_specific_price_';
    this.prefixEditForm = 'form_modal_';
    this.editModalIsOpen = false;

    this.$createPriceFormDefaultValues = new Object();
    this.storePriceFormDefaultValues();

    this.loadAndDisplayExistingSpecificPricesList();

    this.configureAddPriceFormBehavior();

    this.configureEditPriceModalBehavior();

    this.configureDeletePriceButtonsBehavior();

    this.configureMultipleModalsBehavior();
  }

  /**
   * @private
   */


  (0, _createClass3.default)(SpecificPriceFormHandler, [{
    key: 'loadAndDisplayExistingSpecificPricesList',
    value: function loadAndDisplayExistingSpecificPricesList() {
      var _this = this;

      var listContainer = $('#js-specific-price-list');
      var url = listContainer.data('listingUrl').replace(/list\/\d+/, 'list/' + this.getProductId());

      $.ajax({
        type: 'GET',
        url: url
      }).done(function (specificPrices) {
        var tbody = listContainer.find('tbody');
        tbody.find('tr').remove();

        if (specificPrices.length > 0) {
          listContainer.removeClass('hide');
        } else {
          listContainer.addClass('hide');
        }

        var specificPricesList = _this.renderSpecificPricesListingAsHtml(specificPrices);

        tbody.append(specificPricesList);
      });
    }

    /**
     * @param array specificPrices
     *
     * @returns string
     *
     * @private
     */

  }, {
    key: 'renderSpecificPricesListingAsHtml',
    value: function renderSpecificPricesListingAsHtml(specificPrices) {
      var specificPricesList = '';

      var self = this;

      $.each(specificPrices, function (index, specificPrice) {
        var deleteUrl = $('#js-specific-price-list').attr('data-action-delete').replace(/delete\/\d+/, 'delete/' + specificPrice.id_specific_price);
        var row = self.renderSpecificPriceRow(specificPrice, deleteUrl);

        specificPricesList = specificPricesList + row;
      });

      return specificPricesList;
    }

    /**
     * @param Object specificPrice
     * @param string deleteUrl
     *
     * @returns string
     *
     * @private
     */

  }, {
    key: 'renderSpecificPriceRow',
    value: function renderSpecificPriceRow(specificPrice, deleteUrl) {

      var specificPriceId = specificPrice.id_specific_price;

      var row = '<tr>' + '<td>' + specificPrice.rule_name + '</td>' + '<td>' + specificPrice.attributes_name + '</td>' + '<td>' + specificPrice.currency + '</td>' + '<td>' + specificPrice.country + '</td>' + '<td>' + specificPrice.group + '</td>' + '<td>' + specificPrice.customer + '</td>' + '<td>' + specificPrice.fixed_price + '</td>' + '<td>' + specificPrice.impact + '</td>' + '<td>' + specificPrice.period + '</td>' + '<td>' + specificPrice.from_quantity + '</td>' + '<td>' + (specificPrice.can_delete ? '<a href="' + deleteUrl + '" class="js-delete delete btn tooltip-link delete pl-0 pr-0"><i class="material-icons">delete</i></a>' : '') + '</td>' + '<td>' + (specificPrice.can_edit ? '<a href="#" data-specific-price-id="' + specificPriceId + '" class="js-edit edit btn tooltip-link delete pl-0 pr-0"><i class="material-icons">edit</i></a>' : '') + '</td>' + '</tr>';

      return row;
    }

    /**
     * @private
     */

  }, {
    key: 'configureAddPriceFormBehavior',
    value: function configureAddPriceFormBehavior() {
      var _this2 = this;

      var usePrefixForCreate = true;
      var selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

      $('#specific_price_form .js-cancel').click(function () {
        _this2.resetCreatePriceFormDefaultValues();
        $('#specific_price_form').collapse('hide');
      });

      $('#specific_price_form .js-save').on('click', function () {
        return _this2.submitCreatePriceForm();
      });

      $('#js-open-create-specific-price-form').on('click', function () {
        return _this2.loadAndFillOptionsForSelectCombinationInput(usePrefixForCreate);
      });

      $(selectorPrefix + 'leave_bprice').on('click', function () {
        return _this2.enableSpecificPriceFieldIfEligible(usePrefixForCreate);
      });

      $(selectorPrefix + 'sp_reduction_type').on('change', function () {
        return _this2.enableSpecificPriceTaxFieldIfEligible(usePrefixForCreate);
      });
    }

    /**
     * @private
     */

  }, {
    key: 'configureEditPriceFormInsideModalBehavior',
    value: function configureEditPriceFormInsideModalBehavior() {
      var _this3 = this;

      var usePrefixForCreate = false;
      var selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

      $('#form_modal_cancel').click(function () {
        return _this3.closeEditPriceModalAndRemoveForm();
      });
      $('#form_modal_close').click(function () {
        return _this3.closeEditPriceModalAndRemoveForm();
      });

      $('#form_modal_save').click(function () {
        return _this3.submitEditPriceForm();
      });

      this.loadAndFillOptionsForSelectCombinationInput(usePrefixForCreate);

      $(selectorPrefix + 'leave_bprice').on('click', function () {
        return _this3.enableSpecificPriceFieldIfEligible(usePrefixForCreate);
      });

      $(selectorPrefix + 'sp_reduction_type').on('change', function () {
        return _this3.enableSpecificPriceTaxFieldIfEligible(usePrefixForCreate);
      });

      this.reinitializeDatePickers();

      this.initializeLeaveBPriceField(usePrefixForCreate);
      this.enableSpecificPriceTaxFieldIfEligible(usePrefixForCreate);
    }

    /**
     * @private
     */

  }, {
    key: 'reinitializeDatePickers',
    value: function reinitializeDatePickers() {
      $('.datepicker input').datetimepicker({ format: 'YYYY-MM-DD' });
    }

    /**
     * @param boolean usePrefixForCreate
     *
     * @private
     */

  }, {
    key: 'initializeLeaveBPriceField',
    value: function initializeLeaveBPriceField(usePrefixForCreate) {
      var selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

      if ($(selectorPrefix + 'sp_price').val() != '') {
        $(selectorPrefix + 'sp_price').prop('disabled', false);
        $(selectorPrefix + 'leave_bprice').prop('checked', false);
      }
    }

    /**
     * @private
     */

  }, {
    key: 'configureEditPriceModalBehavior',
    value: function configureEditPriceModalBehavior() {
      var _this4 = this;

      $(document).on('click', '#js-specific-price-list .js-edit', function (event) {
        event.preventDefault();

        var specificPriceId = $(event.currentTarget).data('specificPriceId');

        _this4.openEditPriceModalAndLoadForm(specificPriceId);
      });
    }

    /**
     * @private
     */

  }, {
    key: 'configureDeletePriceButtonsBehavior',
    value: function configureDeletePriceButtonsBehavior() {
      var _this5 = this;

      $(document).on('click', '#js-specific-price-list .js-delete', function (event) {
        event.preventDefault();
        _this5.deleteSpecificPrice(event.currentTarget);
      });
    }

    /**
     * @see https://vijayasankarn.wordpress.com/2017/02/24/quick-fix-scrolling-and-focus-when-multiple-bootstrap-modals-are-open/
     */

  }, {
    key: 'configureMultipleModalsBehavior',
    value: function configureMultipleModalsBehavior() {
      var _this6 = this;

      $('.modal').on('hidden.bs.modal', function () {
        if (_this6.editModalIsOpen) {
          $('body').addClass('modal-open');
        }
      });
    }

    /**
     * @private
     */

  }, {
    key: 'submitCreatePriceForm',
    value: function submitCreatePriceForm() {
      var _this7 = this;

      var url = $('#specific_price_form').attr('data-action');
      var data = $('#specific_price_form input, #specific_price_form select, #form_id_product').serialize();

      $('#specific_price_form .js-save').attr('disabled', 'disabled');

      $.ajax({
        type: 'POST',
        url: url,
        data: data
      }).done(function (response) {
        showSuccessMessage(translate_javascripts['Form update success']);
        _this7.resetCreatePriceFormDefaultValues();
        $('#specific_price_form').collapse('hide');
        _this7.loadAndDisplayExistingSpecificPricesList();

        $('#specific_price_form .js-save').removeAttr('disabled');
      }).fail(function (errors) {
        showErrorMessage(errors.responseJSON);

        $('#specific_price_form .js-save').removeAttr('disabled');
      });
    }

    /**
     * @private
     */

  }, {
    key: 'submitEditPriceForm',
    value: function submitEditPriceForm() {
      var _this8 = this;

      var baseUrl = $('#edit-specific-price-modal-form').attr('data-action');
      var specificPriceId = $('#edit-specific-price-modal-form').data('specificPriceId');
      var url = baseUrl.replace(/update\/\d+/, 'update/' + specificPriceId);

      var data = $('#edit-specific-price-modal-form input, #edit-specific-price-modal-form select, #form_id_product').serialize();

      $('#edit-specific-price-modal-form .js-save').attr('disabled', 'disabled');

      $.ajax({
        type: 'POST',
        url: url,
        data: data
      }).done(function (response) {
        showSuccessMessage(translate_javascripts['Form update success']);
        _this8.closeEditPriceModalAndRemoveForm();
        _this8.loadAndDisplayExistingSpecificPricesList();
        $('#edit-specific-price-modal-form .js-save').removeAttr('disabled');
      }).fail(function (errors) {
        showErrorMessage(errors.responseJSON);

        $('#edit-specific-price-modal-form .js-save').removeAttr('disabled');
      });
    }

    /**
     * @param string clickedLink selector
     *
     * @private
     */

  }, {
    key: 'deleteSpecificPrice',
    value: function deleteSpecificPrice(clickedLink) {
      var _this9 = this;

      modalConfirmation.create(translate_javascripts['This will delete the specific price. Do you wish to proceed?'], null, {
        onContinue: function onContinue() {

          var url = $(clickedLink).attr('href');
          $(clickedLink).attr('disabled', 'disabled');

          $.ajax({
            type: 'GET',
            url: url
          }).done(function (response) {
            _this9.loadAndDisplayExistingSpecificPricesList();
            showSuccessMessage(response);
            $(clickedLink).removeAttr('disabled');
          }).fail(function (errors) {
            showErrorMessage(errors.responseJSON);
            $(clickedLink).removeAttr('disabled');
          });
        }
      }).show();
    }

    /**
     * Store 'add specific price' form values
     * for future usage
     *
     * @private
     */

  }, {
    key: 'storePriceFormDefaultValues',
    value: function storePriceFormDefaultValues() {
      var storage = this.$createPriceFormDefaultValues;

      $('#specific_price_form').find('select,input').each(function (index, value) {
        storage[$(value).attr('id')] = $(value).val();
      });

      $('#specific_price_form').find('input:checkbox').each(function (index, value) {
        storage[$(value).attr('id')] = $(value).prop('checked');
      });

      this.$createPriceFormDefaultValues = storage;
    }

    /**
     * @param boolean usePrefixForCreate
     *
     * @private
     */

  }, {
    key: 'loadAndFillOptionsForSelectCombinationInput',
    value: function loadAndFillOptionsForSelectCombinationInput(usePrefixForCreate) {

      var selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

      var inputField = $(selectorPrefix + 'sp_id_product_attribute');
      var url = inputField.attr('data-action').replace(/product-combinations\/\d+/, 'product-combinations/' + this.getProductId());

      $.ajax({
        type: 'GET',
        url: url
      }).done(function (combinations) {
        /** remove all options except first one */
        inputField.find('option:gt(0)').remove();

        $.each(combinations, function (index, combination) {
          inputField.append('<option value="' + combination.id + '">' + combination.name + '</option>');
        });

        if (inputField.data('selectedAttribute') != '0') {
          inputField.val(inputField.data('selectedAttribute')).trigger('change');
        }
      });
    }

    /**
     * @param boolean usePrefixForCreate
     *
     * @private
     */

  }, {
    key: 'enableSpecificPriceTaxFieldIfEligible',
    value: function enableSpecificPriceTaxFieldIfEligible(usePrefixForCreate) {

      var selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

      if ($(selectorPrefix + 'sp_reduction_type').val() === 'percentage') {
        $(selectorPrefix + 'sp_reduction_tax').hide();
      } else {
        $(selectorPrefix + 'sp_reduction_tax').show();
      }
    }

    /**
     * Reset 'add specific price' form values
     * using previously stored default values
     *
     * @private
     */

  }, {
    key: 'resetCreatePriceFormDefaultValues',
    value: function resetCreatePriceFormDefaultValues() {
      var previouslyStoredValues = this.$createPriceFormDefaultValues;

      $('#specific_price_form').find('input').each(function (index, value) {
        $(value).val(previouslyStoredValues[$(value).attr('id')]);
      });

      $('#specific_price_form').find('select').each(function (index, value) {
        $(value).val(previouslyStoredValues[$(value).attr('id')]).change();
      });

      $('#specific_price_form').find('input:checkbox').each(function (index, value) {
        $(value).prop("checked", true);
      });
    }

    /**
     * @param boolean usePrefixForCreate
     *
     * @private
     */

  }, {
    key: 'enableSpecificPriceFieldIfEligible',
    value: function enableSpecificPriceFieldIfEligible(usePrefixForCreate) {
      var selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

      $(selectorPrefix + 'sp_price').prop('disabled', $(selectorPrefix + 'leave_bprice').is(':checked')).val('');
    }

    /**
     * Open 'edit specific price' form into a modal
     *
     * @param integer specificPriceId
     *
     * @private
     */

  }, {
    key: 'openEditPriceModalAndLoadForm',
    value: function openEditPriceModalAndLoadForm(specificPriceId) {
      var _this10 = this;

      var url = $('#js-specific-price-list').data('actionEdit').replace(/form\/\d+/, 'form/' + specificPriceId);

      $('#edit-specific-price-modal').modal("show");
      this.editModalIsOpen = true;

      $.ajax({
        type: 'GET',
        url: url
      }).done(function (response) {
        _this10.insertEditSpecificPriceFormIntoModal(response);
        $('#edit-specific-price-modal-form').data('specificPriceId', specificPriceId);
        _this10.configureEditPriceFormInsideModalBehavior();
      }).fail(function (errors) {
        showErrorMessage(errors.responseJSON);
      });
    }

    /**
     * @private
     */

  }, {
    key: 'closeEditPriceModalAndRemoveForm',
    value: function closeEditPriceModalAndRemoveForm() {
      $('#edit-specific-price-modal').modal("hide");
      this.editModalIsOpen = false;

      var formLocationHolder = $('#edit-specific-price-modal-form');

      formLocationHolder.empty();
    }

    /**
     * @param string form: HTML 'edit specific price' form
     *
     * @private
     */

  }, {
    key: 'insertEditSpecificPriceFormIntoModal',
    value: function insertEditSpecificPriceFormIntoModal(form) {
      var formLocationHolder = $('#edit-specific-price-modal-form');

      formLocationHolder.empty();
      formLocationHolder.append(form);
    }

    /**
     * Get product ID for current Catalog Product page
     *
     * @returns integer
     *
     * @private
     */

  }, {
    key: 'getProductId',
    value: function getProductId() {
      return $('#form_id_product').val();
    }

    /**
     * @param boolean usePrefixForCreate
     *
     * @returns string
     *
     * @private
     */

  }, {
    key: 'getPrefixSelector',
    value: function getPrefixSelector(usePrefixForCreate) {
      if (usePrefixForCreate == true) {
        return '#' + this.prefixCreateForm;
      } else {
        return '#' + this.prefixEditForm;
      }
    }
  }]);
  return SpecificPriceFormHandler;
}();

exports.default = SpecificPriceFormHandler;

/***/ }),

/***/ 482:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _specificPriceFormHandler = __webpack_require__(403);

var _specificPriceFormHandler2 = _interopRequireDefault(_specificPriceFormHandler);

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
  new _specificPriceFormHandler2.default();
});

/***/ }),

/***/ 5:
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self : Function('return this')();
if(typeof __g == 'number')__g = global; // eslint-disable-line no-undef

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

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgM2E2MTdjZWQyOWViY2NiNmExZDA/MTI1MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY2xhc3NDYWxsQ2hlY2suanM/MjFhZioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NyZWF0ZUNsYXNzLmpzPzFkZmUqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oaWRlLmpzP2E2ZGEqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2FuLW9iamVjdC5qcz8wZGEzKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19wcm9wZXJ0eS1kZXNjLmpzPzFlODYqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2N0eC5qcz9jZTAwKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1wcmltaXRpdmUuanM/NDlhNCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZG9tLWNyZWF0ZS5qcz9hYjQ0KioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pZTgtZG9tLWRlZmluZS5qcz9iZDFmKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hLWZ1bmN0aW9uLmpzP2Q1M2UqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanM/NWY3MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZGVzY3JpcHRvcnMuanM/NzA1MSoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qcz9iN2Q4KioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5LmpzP2M4MmMqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvcmUuanM/MWI2MioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXMtb2JqZWN0LmpzPzI0YzgqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2NhdGFsb2cvcHJvZHVjdC9zcGVjaWZpYy1wcmljZS1mb3JtLWhhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvY2F0YWxvZy9wcm9kdWN0L2luZGV4LmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2dsb2JhbC5qcz83N2FhKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZHAuanM/NDExNioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZmFpbHMuanM/OTM1ZCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZXhwb3J0LmpzP2VjZTIqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJTcGVjaWZpY1ByaWNlRm9ybUhhbmRsZXIiLCJwcmVmaXhDcmVhdGVGb3JtIiwicHJlZml4RWRpdEZvcm0iLCJlZGl0TW9kYWxJc09wZW4iLCIkY3JlYXRlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcyIsIk9iamVjdCIsInN0b3JlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcyIsImxvYWRBbmREaXNwbGF5RXhpc3RpbmdTcGVjaWZpY1ByaWNlc0xpc3QiLCJjb25maWd1cmVBZGRQcmljZUZvcm1CZWhhdmlvciIsImNvbmZpZ3VyZUVkaXRQcmljZU1vZGFsQmVoYXZpb3IiLCJjb25maWd1cmVEZWxldGVQcmljZUJ1dHRvbnNCZWhhdmlvciIsImNvbmZpZ3VyZU11bHRpcGxlTW9kYWxzQmVoYXZpb3IiLCJsaXN0Q29udGFpbmVyIiwidXJsIiwiZGF0YSIsInJlcGxhY2UiLCJnZXRQcm9kdWN0SWQiLCJhamF4IiwidHlwZSIsImRvbmUiLCJ0Ym9keSIsImZpbmQiLCJyZW1vdmUiLCJzcGVjaWZpY1ByaWNlcyIsImxlbmd0aCIsInJlbW92ZUNsYXNzIiwiYWRkQ2xhc3MiLCJzcGVjaWZpY1ByaWNlc0xpc3QiLCJyZW5kZXJTcGVjaWZpY1ByaWNlc0xpc3RpbmdBc0h0bWwiLCJhcHBlbmQiLCJzZWxmIiwiZWFjaCIsImluZGV4Iiwic3BlY2lmaWNQcmljZSIsImRlbGV0ZVVybCIsImF0dHIiLCJpZF9zcGVjaWZpY19wcmljZSIsInJvdyIsInJlbmRlclNwZWNpZmljUHJpY2VSb3ciLCJzcGVjaWZpY1ByaWNlSWQiLCJydWxlX25hbWUiLCJhdHRyaWJ1dGVzX25hbWUiLCJjdXJyZW5jeSIsImNvdW50cnkiLCJncm91cCIsImN1c3RvbWVyIiwiZml4ZWRfcHJpY2UiLCJpbXBhY3QiLCJwZXJpb2QiLCJmcm9tX3F1YW50aXR5IiwiY2FuX2RlbGV0ZSIsImNhbl9lZGl0IiwidXNlUHJlZml4Rm9yQ3JlYXRlIiwic2VsZWN0b3JQcmVmaXgiLCJnZXRQcmVmaXhTZWxlY3RvciIsImNsaWNrIiwicmVzZXRDcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzIiwiY29sbGFwc2UiLCJvbiIsInN1Ym1pdENyZWF0ZVByaWNlRm9ybSIsImxvYWRBbmRGaWxsT3B0aW9uc0ZvclNlbGVjdENvbWJpbmF0aW9uSW5wdXQiLCJlbmFibGVTcGVjaWZpY1ByaWNlRmllbGRJZkVsaWdpYmxlIiwiZW5hYmxlU3BlY2lmaWNQcmljZVRheEZpZWxkSWZFbGlnaWJsZSIsImNsb3NlRWRpdFByaWNlTW9kYWxBbmRSZW1vdmVGb3JtIiwic3VibWl0RWRpdFByaWNlRm9ybSIsInJlaW5pdGlhbGl6ZURhdGVQaWNrZXJzIiwiaW5pdGlhbGl6ZUxlYXZlQlByaWNlRmllbGQiLCJkYXRldGltZXBpY2tlciIsImZvcm1hdCIsInZhbCIsInByb3AiLCJkb2N1bWVudCIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCJjdXJyZW50VGFyZ2V0Iiwib3BlbkVkaXRQcmljZU1vZGFsQW5kTG9hZEZvcm0iLCJkZWxldGVTcGVjaWZpY1ByaWNlIiwic2VyaWFsaXplIiwic2hvd1N1Y2Nlc3NNZXNzYWdlIiwidHJhbnNsYXRlX2phdmFzY3JpcHRzIiwicmVtb3ZlQXR0ciIsImZhaWwiLCJzaG93RXJyb3JNZXNzYWdlIiwiZXJyb3JzIiwicmVzcG9uc2VKU09OIiwiYmFzZVVybCIsImNsaWNrZWRMaW5rIiwibW9kYWxDb25maXJtYXRpb24iLCJjcmVhdGUiLCJvbkNvbnRpbnVlIiwicmVzcG9uc2UiLCJzaG93Iiwic3RvcmFnZSIsInZhbHVlIiwiaW5wdXRGaWVsZCIsImNvbWJpbmF0aW9ucyIsImNvbWJpbmF0aW9uIiwiaWQiLCJuYW1lIiwidHJpZ2dlciIsImhpZGUiLCJwcmV2aW91c2x5U3RvcmVkVmFsdWVzIiwiY2hhbmdlIiwiaXMiLCJtb2RhbCIsImluc2VydEVkaXRTcGVjaWZpY1ByaWNlRm9ybUludG9Nb2RhbCIsImNvbmZpZ3VyZUVkaXRQcmljZUZvcm1JbnNpZGVNb2RhbEJlaGF2aW9yIiwiZm9ybUxvY2F0aW9uSG9sZGVyIiwiZW1wdHkiLCJmb3JtIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVB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O0FBQ0EscUZBQXFGO0FBQ3JGO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxXQUFXO0FBQ1gsU0FBUztBQUNUO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsS0FBSztBQUNMO0FBQ0E7QUFDQSwrQ0FBK0M7QUFDL0M7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsY0FBYztBQUNkLGNBQWM7QUFDZCxjQUFjO0FBQ2QsY0FBYztBQUNkLGVBQWU7QUFDZixlQUFlO0FBQ2YsZUFBZTtBQUNmLGdCQUFnQjtBQUNoQix5QiIsImZpbGUiOiJjYXRhbG9nX3Byb2R1Y3QuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSA0ODIpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDNhNjE3Y2VkMjllYmNjYjZhMWQwIiwiXCJ1c2Ugc3RyaWN0XCI7XG5cbmV4cG9ydHMuX19lc01vZHVsZSA9IHRydWU7XG5cbmV4cG9ydHMuZGVmYXVsdCA9IGZ1bmN0aW9uIChpbnN0YW5jZSwgQ29uc3RydWN0b3IpIHtcbiAgaWYgKCEoaW5zdGFuY2UgaW5zdGFuY2VvZiBDb25zdHJ1Y3RvcikpIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKFwiQ2Fubm90IGNhbGwgYSBjbGFzcyBhcyBhIGZ1bmN0aW9uXCIpO1xuICB9XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY2xhc3NDYWxsQ2hlY2suanNcbi8vIG1vZHVsZSBpZCA9IDBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJcInVzZSBzdHJpY3RcIjtcblxuZXhwb3J0cy5fX2VzTW9kdWxlID0gdHJ1ZTtcblxudmFyIF9kZWZpbmVQcm9wZXJ0eSA9IHJlcXVpcmUoXCIuLi9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHlcIik7XG5cbnZhciBfZGVmaW5lUHJvcGVydHkyID0gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChfZGVmaW5lUHJvcGVydHkpO1xuXG5mdW5jdGlvbiBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KG9iaikgeyByZXR1cm4gb2JqICYmIG9iai5fX2VzTW9kdWxlID8gb2JqIDogeyBkZWZhdWx0OiBvYmogfTsgfVxuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoKSB7XG4gIGZ1bmN0aW9uIGRlZmluZVByb3BlcnRpZXModGFyZ2V0LCBwcm9wcykge1xuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgcHJvcHMubGVuZ3RoOyBpKyspIHtcbiAgICAgIHZhciBkZXNjcmlwdG9yID0gcHJvcHNbaV07XG4gICAgICBkZXNjcmlwdG9yLmVudW1lcmFibGUgPSBkZXNjcmlwdG9yLmVudW1lcmFibGUgfHwgZmFsc2U7XG4gICAgICBkZXNjcmlwdG9yLmNvbmZpZ3VyYWJsZSA9IHRydWU7XG4gICAgICBpZiAoXCJ2YWx1ZVwiIGluIGRlc2NyaXB0b3IpIGRlc2NyaXB0b3Iud3JpdGFibGUgPSB0cnVlO1xuICAgICAgKDAsIF9kZWZpbmVQcm9wZXJ0eTIuZGVmYXVsdCkodGFyZ2V0LCBkZXNjcmlwdG9yLmtleSwgZGVzY3JpcHRvcik7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIGZ1bmN0aW9uIChDb25zdHJ1Y3RvciwgcHJvdG9Qcm9wcywgc3RhdGljUHJvcHMpIHtcbiAgICBpZiAocHJvdG9Qcm9wcykgZGVmaW5lUHJvcGVydGllcyhDb25zdHJ1Y3Rvci5wcm90b3R5cGUsIHByb3RvUHJvcHMpO1xuICAgIGlmIChzdGF0aWNQcm9wcykgZGVmaW5lUHJvcGVydGllcyhDb25zdHJ1Y3Rvciwgc3RhdGljUHJvcHMpO1xuICAgIHJldHVybiBDb25zdHJ1Y3RvcjtcbiAgfTtcbn0oKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NyZWF0ZUNsYXNzLmpzXG4vLyBtb2R1bGUgaWQgPSAxXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGRQICAgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZHAnKVxuICAsIGNyZWF0ZURlc2MgPSByZXF1aXJlKCcuL19wcm9wZXJ0eS1kZXNjJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJykgPyBmdW5jdGlvbihvYmplY3QsIGtleSwgdmFsdWUpe1xuICByZXR1cm4gZFAuZihvYmplY3QsIGtleSwgY3JlYXRlRGVzYygxLCB2YWx1ZSkpO1xufSA6IGZ1bmN0aW9uKG9iamVjdCwga2V5LCB2YWx1ZSl7XG4gIG9iamVjdFtrZXldID0gdmFsdWU7XG4gIHJldHVybiBvYmplY3Q7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faGlkZS5qc1xuLy8gbW9kdWxlIGlkID0gMTBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi9faXMtb2JqZWN0Jyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgaWYoIWlzT2JqZWN0KGl0KSl0aHJvdyBUeXBlRXJyb3IoaXQgKyAnIGlzIG5vdCBhbiBvYmplY3QhJyk7XG4gIHJldHVybiBpdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hbi1vYmplY3QuanNcbi8vIG1vZHVsZSBpZCA9IDExXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oYml0bWFwLCB2YWx1ZSl7XG4gIHJldHVybiB7XG4gICAgZW51bWVyYWJsZSAgOiAhKGJpdG1hcCAmIDEpLFxuICAgIGNvbmZpZ3VyYWJsZTogIShiaXRtYXAgJiAyKSxcbiAgICB3cml0YWJsZSAgICA6ICEoYml0bWFwICYgNCksXG4gICAgdmFsdWUgICAgICAgOiB2YWx1ZVxuICB9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3Byb3BlcnR5LWRlc2MuanNcbi8vIG1vZHVsZSBpZCA9IDEyXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8vIG9wdGlvbmFsIC8gc2ltcGxlIGNvbnRleHQgYmluZGluZ1xudmFyIGFGdW5jdGlvbiA9IHJlcXVpcmUoJy4vX2EtZnVuY3Rpb24nKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oZm4sIHRoYXQsIGxlbmd0aCl7XG4gIGFGdW5jdGlvbihmbik7XG4gIGlmKHRoYXQgPT09IHVuZGVmaW5lZClyZXR1cm4gZm47XG4gIHN3aXRjaChsZW5ndGgpe1xuICAgIGNhc2UgMTogcmV0dXJuIGZ1bmN0aW9uKGEpe1xuICAgICAgcmV0dXJuIGZuLmNhbGwodGhhdCwgYSk7XG4gICAgfTtcbiAgICBjYXNlIDI6IHJldHVybiBmdW5jdGlvbihhLCBiKXtcbiAgICAgIHJldHVybiBmbi5jYWxsKHRoYXQsIGEsIGIpO1xuICAgIH07XG4gICAgY2FzZSAzOiByZXR1cm4gZnVuY3Rpb24oYSwgYiwgYyl7XG4gICAgICByZXR1cm4gZm4uY2FsbCh0aGF0LCBhLCBiLCBjKTtcbiAgICB9O1xuICB9XG4gIHJldHVybiBmdW5jdGlvbigvKiAuLi5hcmdzICovKXtcbiAgICByZXR1cm4gZm4uYXBwbHkodGhhdCwgYXJndW1lbnRzKTtcbiAgfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jdHguanNcbi8vIG1vZHVsZSBpZCA9IDEzXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8vIDcuMS4xIFRvUHJpbWl0aXZlKGlucHV0IFssIFByZWZlcnJlZFR5cGVdKVxudmFyIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi9faXMtb2JqZWN0Jyk7XG4vLyBpbnN0ZWFkIG9mIHRoZSBFUzYgc3BlYyB2ZXJzaW9uLCB3ZSBkaWRuJ3QgaW1wbGVtZW50IEBAdG9QcmltaXRpdmUgY2FzZVxuLy8gYW5kIHRoZSBzZWNvbmQgYXJndW1lbnQgLSBmbGFnIC0gcHJlZmVycmVkIHR5cGUgaXMgYSBzdHJpbmdcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQsIFMpe1xuICBpZighaXNPYmplY3QoaXQpKXJldHVybiBpdDtcbiAgdmFyIGZuLCB2YWw7XG4gIGlmKFMgJiYgdHlwZW9mIChmbiA9IGl0LnRvU3RyaW5nKSA9PSAnZnVuY3Rpb24nICYmICFpc09iamVjdCh2YWwgPSBmbi5jYWxsKGl0KSkpcmV0dXJuIHZhbDtcbiAgaWYodHlwZW9mIChmbiA9IGl0LnZhbHVlT2YpID09ICdmdW5jdGlvbicgJiYgIWlzT2JqZWN0KHZhbCA9IGZuLmNhbGwoaXQpKSlyZXR1cm4gdmFsO1xuICBpZighUyAmJiB0eXBlb2YgKGZuID0gaXQudG9TdHJpbmcpID09ICdmdW5jdGlvbicgJiYgIWlzT2JqZWN0KHZhbCA9IGZuLmNhbGwoaXQpKSlyZXR1cm4gdmFsO1xuICB0aHJvdyBUeXBlRXJyb3IoXCJDYW4ndCBjb252ZXJ0IG9iamVjdCB0byBwcmltaXRpdmUgdmFsdWVcIik7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8tcHJpbWl0aXZlLmpzXG4vLyBtb2R1bGUgaWQgPSAxNFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgaXNPYmplY3QgPSByZXF1aXJlKCcuL19pcy1vYmplY3QnKVxuICAsIGRvY3VtZW50ID0gcmVxdWlyZSgnLi9fZ2xvYmFsJykuZG9jdW1lbnRcbiAgLy8gaW4gb2xkIElFIHR5cGVvZiBkb2N1bWVudC5jcmVhdGVFbGVtZW50IGlzICdvYmplY3QnXG4gICwgaXMgPSBpc09iamVjdChkb2N1bWVudCkgJiYgaXNPYmplY3QoZG9jdW1lbnQuY3JlYXRlRWxlbWVudCk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGlzID8gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChpdCkgOiB7fTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kb20tY3JlYXRlLmpzXG4vLyBtb2R1bGUgaWQgPSAxNlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9ICFyZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpICYmICFyZXF1aXJlKCcuL19mYWlscycpKGZ1bmN0aW9uKCl7XG4gIHJldHVybiBPYmplY3QuZGVmaW5lUHJvcGVydHkocmVxdWlyZSgnLi9fZG9tLWNyZWF0ZScpKCdkaXYnKSwgJ2EnLCB7Z2V0OiBmdW5jdGlvbigpeyByZXR1cm4gNzsgfX0pLmEgIT0gNztcbn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faWU4LWRvbS1kZWZpbmUuanNcbi8vIG1vZHVsZSBpZCA9IDE3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICBpZih0eXBlb2YgaXQgIT0gJ2Z1bmN0aW9uJyl0aHJvdyBUeXBlRXJyb3IoaXQgKyAnIGlzIG5vdCBhIGZ1bmN0aW9uIScpO1xuICByZXR1cm4gaXQ7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYS1mdW5jdGlvbi5qc1xuLy8gbW9kdWxlIGlkID0gMThcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSB7IFwiZGVmYXVsdFwiOiByZXF1aXJlKFwiY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9kZWZpbmUtcHJvcGVydHlcIiksIF9fZXNNb2R1bGU6IHRydWUgfTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanNcbi8vIG1vZHVsZSBpZCA9IDE5XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8vIFRoYW5rJ3MgSUU4IGZvciBoaXMgZnVubnkgZGVmaW5lUHJvcGVydHlcbm1vZHVsZS5leHBvcnRzID0gIXJlcXVpcmUoJy4vX2ZhaWxzJykoZnVuY3Rpb24oKXtcbiAgcmV0dXJuIE9iamVjdC5kZWZpbmVQcm9wZXJ0eSh7fSwgJ2EnLCB7Z2V0OiBmdW5jdGlvbigpeyByZXR1cm4gNzsgfX0pLmEgIT0gNztcbn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZGVzY3JpcHRvcnMuanNcbi8vIG1vZHVsZSBpZCA9IDJcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczYub2JqZWN0LmRlZmluZS1wcm9wZXJ0eScpO1xudmFyICRPYmplY3QgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuT2JqZWN0O1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0eShpdCwga2V5LCBkZXNjKXtcbiAgcmV0dXJuICRPYmplY3QuZGVmaW5lUHJvcGVydHkoaXQsIGtleSwgZGVzYyk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qc1xuLy8gbW9kdWxlIGlkID0gMjBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyICRleHBvcnQgPSByZXF1aXJlKCcuL19leHBvcnQnKTtcbi8vIDE5LjEuMi40IC8gMTUuMi4zLjYgT2JqZWN0LmRlZmluZVByb3BlcnR5KE8sIFAsIEF0dHJpYnV0ZXMpXG4kZXhwb3J0KCRleHBvcnQuUyArICRleHBvcnQuRiAqICFyZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpLCAnT2JqZWN0Jywge2RlZmluZVByb3BlcnR5OiByZXF1aXJlKCcuL19vYmplY3QtZHAnKS5mfSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAyMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgY29yZSA9IG1vZHVsZS5leHBvcnRzID0ge3ZlcnNpb246ICcyLjQuMCd9O1xuaWYodHlwZW9mIF9fZSA9PSAnbnVtYmVyJylfX2UgPSBjb3JlOyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lIG5vLXVuZGVmXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jb3JlLmpzXG4vLyBtb2R1bGUgaWQgPSAzXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gdHlwZW9mIGl0ID09PSAnb2JqZWN0JyA/IGl0ICE9PSBudWxsIDogdHlwZW9mIGl0ID09PSAnZnVuY3Rpb24nO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2lzLW9iamVjdC5qc1xuLy8gbW9kdWxlIGlkID0gNFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuY2xhc3MgU3BlY2lmaWNQcmljZUZvcm1IYW5kbGVyIHtcblxuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnByZWZpeENyZWF0ZUZvcm0gPSAnZm9ybV9zdGVwMl9zcGVjaWZpY19wcmljZV8nO1xuICAgIHRoaXMucHJlZml4RWRpdEZvcm0gPSAnZm9ybV9tb2RhbF8nO1xuICAgIHRoaXMuZWRpdE1vZGFsSXNPcGVuID0gZmFsc2U7XG5cbiAgICB0aGlzLiRjcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzID0gbmV3IE9iamVjdCgpO1xuICAgIHRoaXMuc3RvcmVQcmljZUZvcm1EZWZhdWx0VmFsdWVzKCk7XG5cbiAgICB0aGlzLmxvYWRBbmREaXNwbGF5RXhpc3RpbmdTcGVjaWZpY1ByaWNlc0xpc3QoKTtcblxuICAgIHRoaXMuY29uZmlndXJlQWRkUHJpY2VGb3JtQmVoYXZpb3IoKTtcblxuICAgIHRoaXMuY29uZmlndXJlRWRpdFByaWNlTW9kYWxCZWhhdmlvcigpO1xuXG4gICAgdGhpcy5jb25maWd1cmVEZWxldGVQcmljZUJ1dHRvbnNCZWhhdmlvcigpO1xuXG4gICAgdGhpcy5jb25maWd1cmVNdWx0aXBsZU1vZGFsc0JlaGF2aW9yKCk7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGxvYWRBbmREaXNwbGF5RXhpc3RpbmdTcGVjaWZpY1ByaWNlc0xpc3QoKSB7XG4gICAgdmFyIGxpc3RDb250YWluZXIgPSAkKCcjanMtc3BlY2lmaWMtcHJpY2UtbGlzdCcpO1xuICAgIHZhciB1cmwgPSBsaXN0Q29udGFpbmVyLmRhdGEoJ2xpc3RpbmdVcmwnKS5yZXBsYWNlKC9saXN0XFwvXFxkKy8sICdsaXN0LycgKyB0aGlzLmdldFByb2R1Y3RJZCgpKTtcblxuICAgICQuYWpheCh7XG4gICAgICB0eXBlOiAnR0VUJyxcbiAgICAgIHVybDogdXJsLFxuICAgIH0pXG4gICAgICAgIC5kb25lKHNwZWNpZmljUHJpY2VzID0+IHtcbiAgICAgICAgICB2YXIgdGJvZHkgPSBsaXN0Q29udGFpbmVyLmZpbmQoJ3Rib2R5Jyk7XG4gICAgICAgICAgdGJvZHkuZmluZCgndHInKS5yZW1vdmUoKTtcblxuICAgICAgICAgIGlmIChzcGVjaWZpY1ByaWNlcy5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICBsaXN0Q29udGFpbmVyLnJlbW92ZUNsYXNzKCdoaWRlJyk7XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGxpc3RDb250YWluZXIuYWRkQ2xhc3MoJ2hpZGUnKTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICB2YXIgc3BlY2lmaWNQcmljZXNMaXN0ID0gdGhpcy5yZW5kZXJTcGVjaWZpY1ByaWNlc0xpc3RpbmdBc0h0bWwoc3BlY2lmaWNQcmljZXMpO1xuXG4gICAgICAgICAgdGJvZHkuYXBwZW5kKHNwZWNpZmljUHJpY2VzTGlzdCk7XG4gICAgICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSBhcnJheSBzcGVjaWZpY1ByaWNlc1xuICAgKlxuICAgKiBAcmV0dXJucyBzdHJpbmdcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHJlbmRlclNwZWNpZmljUHJpY2VzTGlzdGluZ0FzSHRtbChzcGVjaWZpY1ByaWNlcykge1xuICAgIHZhciBzcGVjaWZpY1ByaWNlc0xpc3QgPSAnJztcblxuICAgIHZhciBzZWxmID0gdGhpcztcblxuICAgICQuZWFjaChzcGVjaWZpY1ByaWNlcywgKGluZGV4LCBzcGVjaWZpY1ByaWNlKSA9PiB7XG4gICAgICB2YXIgZGVsZXRlVXJsID0gJCgnI2pzLXNwZWNpZmljLXByaWNlLWxpc3QnKS5hdHRyKCdkYXRhLWFjdGlvbi1kZWxldGUnKS5yZXBsYWNlKC9kZWxldGVcXC9cXGQrLywgJ2RlbGV0ZS8nICsgc3BlY2lmaWNQcmljZS5pZF9zcGVjaWZpY19wcmljZSk7XG4gICAgICB2YXIgcm93ID0gc2VsZi5yZW5kZXJTcGVjaWZpY1ByaWNlUm93KHNwZWNpZmljUHJpY2UsIGRlbGV0ZVVybCk7XG5cbiAgICAgIHNwZWNpZmljUHJpY2VzTGlzdCA9IHNwZWNpZmljUHJpY2VzTGlzdCArIHJvdztcbiAgICB9KTtcblxuICAgIHJldHVybiBzcGVjaWZpY1ByaWNlc0xpc3Q7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIE9iamVjdCBzcGVjaWZpY1ByaWNlXG4gICAqIEBwYXJhbSBzdHJpbmcgZGVsZXRlVXJsXG4gICAqXG4gICAqIEByZXR1cm5zIHN0cmluZ1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgcmVuZGVyU3BlY2lmaWNQcmljZVJvdyhzcGVjaWZpY1ByaWNlLCBkZWxldGVVcmwpIHtcblxuICAgIHZhciBzcGVjaWZpY1ByaWNlSWQgPSBzcGVjaWZpY1ByaWNlLmlkX3NwZWNpZmljX3ByaWNlO1xuXG4gICAgdmFyIHJvdyA9ICc8dHI+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UucnVsZV9uYW1lICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuYXR0cmlidXRlc19uYW1lICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuY3VycmVuY3kgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5jb3VudHJ5ICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuZ3JvdXAgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5jdXN0b21lciArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyBzcGVjaWZpY1ByaWNlLmZpeGVkX3ByaWNlICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuaW1wYWN0ICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UucGVyaW9kICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuZnJvbV9xdWFudGl0eSArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyAoc3BlY2lmaWNQcmljZS5jYW5fZGVsZXRlID8gJzxhIGhyZWY9XCInICsgZGVsZXRlVXJsICsgJ1wiIGNsYXNzPVwianMtZGVsZXRlIGRlbGV0ZSBidG4gdG9vbHRpcC1saW5rIGRlbGV0ZSBwbC0wIHByLTBcIj48aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+ZGVsZXRlPC9pPjwvYT4nIDogJycpICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIChzcGVjaWZpY1ByaWNlLmNhbl9lZGl0ID8gJzxhIGhyZWY9XCIjXCIgZGF0YS1zcGVjaWZpYy1wcmljZS1pZD1cIicgKyBzcGVjaWZpY1ByaWNlSWQgKyAnXCIgY2xhc3M9XCJqcy1lZGl0IGVkaXQgYnRuIHRvb2x0aXAtbGluayBkZWxldGUgcGwtMCBwci0wXCI+PGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmVkaXQ8L2k+PC9hPicgOiAnJykgKyAnPC90ZD4nICtcbiAgICAgICAgJzwvdHI+JztcblxuICAgIHJldHVybiByb3c7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGNvbmZpZ3VyZUFkZFByaWNlRm9ybUJlaGF2aW9yKCkge1xuICAgIGNvbnN0IHVzZVByZWZpeEZvckNyZWF0ZSA9IHRydWU7XG4gICAgdmFyIHNlbGVjdG9yUHJlZml4ID0gdGhpcy5nZXRQcmVmaXhTZWxlY3Rvcih1c2VQcmVmaXhGb3JDcmVhdGUpO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0gLmpzLWNhbmNlbCcpLmNsaWNrKCgpID0+IHtcbiAgICAgIHRoaXMucmVzZXRDcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzKCk7XG4gICAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmNvbGxhcHNlKCdoaWRlJyk7XG4gICAgfSk7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybSAuanMtc2F2ZScpLm9uKCdjbGljaycsICgpID0+IHRoaXMuc3VibWl0Q3JlYXRlUHJpY2VGb3JtKCkpO1xuXG4gICAgJCgnI2pzLW9wZW4tY3JlYXRlLXNwZWNpZmljLXByaWNlLWZvcm0nKS5vbignY2xpY2snLCAoKSA9PiB0aGlzLmxvYWRBbmRGaWxsT3B0aW9uc0ZvclNlbGVjdENvbWJpbmF0aW9uSW5wdXQodXNlUHJlZml4Rm9yQ3JlYXRlKSk7XG5cbiAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ2xlYXZlX2JwcmljZScpLm9uKCdjbGljaycsICgpID0+IHRoaXMuZW5hYmxlU3BlY2lmaWNQcmljZUZpZWxkSWZFbGlnaWJsZSh1c2VQcmVmaXhGb3JDcmVhdGUpKTtcblxuICAgICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcmVkdWN0aW9uX3R5cGUnKS5vbignY2hhbmdlJywgKCkgPT4gdGhpcy5lbmFibGVTcGVjaWZpY1ByaWNlVGF4RmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBjb25maWd1cmVFZGl0UHJpY2VGb3JtSW5zaWRlTW9kYWxCZWhhdmlvcigpIHtcbiAgICBjb25zdCB1c2VQcmVmaXhGb3JDcmVhdGUgPSBmYWxzZTtcbiAgICB2YXIgc2VsZWN0b3JQcmVmaXggPSB0aGlzLmdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSk7XG5cbiAgICAkKCcjZm9ybV9tb2RhbF9jYW5jZWwnKS5jbGljaygoKSA9PiB0aGlzLmNsb3NlRWRpdFByaWNlTW9kYWxBbmRSZW1vdmVGb3JtKCkpO1xuICAgICQoJyNmb3JtX21vZGFsX2Nsb3NlJykuY2xpY2soKCkgPT4gdGhpcy5jbG9zZUVkaXRQcmljZU1vZGFsQW5kUmVtb3ZlRm9ybSgpKTtcblxuICAgICQoJyNmb3JtX21vZGFsX3NhdmUnKS5jbGljaygoKSA9PiB0aGlzLnN1Ym1pdEVkaXRQcmljZUZvcm0oKSk7XG5cbiAgICB0aGlzLmxvYWRBbmRGaWxsT3B0aW9uc0ZvclNlbGVjdENvbWJpbmF0aW9uSW5wdXQodXNlUHJlZml4Rm9yQ3JlYXRlKTtcblxuICAgICQoc2VsZWN0b3JQcmVmaXggKyAnbGVhdmVfYnByaWNlJykub24oJ2NsaWNrJywgKCkgPT4gdGhpcy5lbmFibGVTcGVjaWZpY1ByaWNlRmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkpO1xuXG4gICAgJChzZWxlY3RvclByZWZpeCArICdzcF9yZWR1Y3Rpb25fdHlwZScpLm9uKCdjaGFuZ2UnLCAoKSA9PiB0aGlzLmVuYWJsZVNwZWNpZmljUHJpY2VUYXhGaWVsZElmRWxpZ2libGUodXNlUHJlZml4Rm9yQ3JlYXRlKSk7XG5cbiAgICB0aGlzLnJlaW5pdGlhbGl6ZURhdGVQaWNrZXJzKCk7XG5cbiAgICB0aGlzLmluaXRpYWxpemVMZWF2ZUJQcmljZUZpZWxkKHVzZVByZWZpeEZvckNyZWF0ZSk7XG4gICAgdGhpcy5lbmFibGVTcGVjaWZpY1ByaWNlVGF4RmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSk7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHJlaW5pdGlhbGl6ZURhdGVQaWNrZXJzKCkge1xuICAgICQoJy5kYXRlcGlja2VyIGlucHV0JykuZGF0ZXRpbWVwaWNrZXIoe2Zvcm1hdDogJ1lZWVktTU0tREQnfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIGJvb2xlYW4gdXNlUHJlZml4Rm9yQ3JlYXRlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBpbml0aWFsaXplTGVhdmVCUHJpY2VGaWVsZCh1c2VQcmVmaXhGb3JDcmVhdGUpIHtcbiAgICB2YXIgc2VsZWN0b3JQcmVmaXggPSB0aGlzLmdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSk7XG5cbiAgICBpZiAoJChzZWxlY3RvclByZWZpeCArICdzcF9wcmljZScpLnZhbCgpICE9ICcnKSB7XG4gICAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ3NwX3ByaWNlJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gICAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ2xlYXZlX2JwcmljZScpLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBjb25maWd1cmVFZGl0UHJpY2VNb2RhbEJlaGF2aW9yKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcjanMtc3BlY2lmaWMtcHJpY2UtbGlzdCAuanMtZWRpdCcsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgdmFyIHNwZWNpZmljUHJpY2VJZCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnc3BlY2lmaWNQcmljZUlkJyk7XG5cbiAgICAgIHRoaXMub3BlbkVkaXRQcmljZU1vZGFsQW5kTG9hZEZvcm0oc3BlY2lmaWNQcmljZUlkKTtcbiAgICB9KTtcblxuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBjb25maWd1cmVEZWxldGVQcmljZUJ1dHRvbnNCZWhhdmlvcigpIHtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnI2pzLXNwZWNpZmljLXByaWNlLWxpc3QgLmpzLWRlbGV0ZScsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIHRoaXMuZGVsZXRlU3BlY2lmaWNQcmljZShldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAc2VlIGh0dHBzOi8vdmlqYXlhc2Fua2Fybi53b3JkcHJlc3MuY29tLzIwMTcvMDIvMjQvcXVpY2stZml4LXNjcm9sbGluZy1hbmQtZm9jdXMtd2hlbi1tdWx0aXBsZS1ib290c3RyYXAtbW9kYWxzLWFyZS1vcGVuL1xuICAgKi9cbiAgY29uZmlndXJlTXVsdGlwbGVNb2RhbHNCZWhhdmlvcigpIHtcbiAgICAkKCcubW9kYWwnKS5vbignaGlkZGVuLmJzLm1vZGFsJywgKCkgPT4ge1xuICAgICAgaWYgKHRoaXMuZWRpdE1vZGFsSXNPcGVuKSB7XG4gICAgICAgICQoJ2JvZHknKS5hZGRDbGFzcygnbW9kYWwtb3BlbicpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBzdWJtaXRDcmVhdGVQcmljZUZvcm0oKSB7XG5cbiAgICBjb25zdCB1cmwgPSAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmF0dHIoJ2RhdGEtYWN0aW9uJyk7XG4gICAgY29uc3QgZGF0YSA9ICQoJyNzcGVjaWZpY19wcmljZV9mb3JtIGlucHV0LCAjc3BlY2lmaWNfcHJpY2VfZm9ybSBzZWxlY3QsICNmb3JtX2lkX3Byb2R1Y3QnKS5zZXJpYWxpemUoKTtcblxuICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtIC5qcy1zYXZlJykuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcblxuICAgICQuYWpheCh7XG4gICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICB1cmw6IHVybCxcbiAgICAgIGRhdGE6IGRhdGEsXG4gICAgfSlcbiAgICAgICAgLmRvbmUocmVzcG9uc2UgPT4ge1xuICAgICAgICAgIHNob3dTdWNjZXNzTWVzc2FnZSh0cmFuc2xhdGVfamF2YXNjcmlwdHNbJ0Zvcm0gdXBkYXRlIHN1Y2Nlc3MnXSk7XG4gICAgICAgICAgdGhpcy5yZXNldENyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMoKTtcbiAgICAgICAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmNvbGxhcHNlKCdoaWRlJyk7XG4gICAgICAgICAgdGhpcy5sb2FkQW5kRGlzcGxheUV4aXN0aW5nU3BlY2lmaWNQcmljZXNMaXN0KCk7XG5cbiAgICAgICAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybSAuanMtc2F2ZScpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgfSlcbiAgICAgICAgLmZhaWwoZXJyb3JzID0+IHtcbiAgICAgICAgICBzaG93RXJyb3JNZXNzYWdlKGVycm9ycy5yZXNwb25zZUpTT04pO1xuXG4gICAgICAgICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0gLmpzLXNhdmUnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICAgICAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgc3VibWl0RWRpdFByaWNlRm9ybSgpIHtcbiAgICBjb25zdCBiYXNlVXJsID0gJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybScpLmF0dHIoJ2RhdGEtYWN0aW9uJyk7XG4gICAgY29uc3Qgc3BlY2lmaWNQcmljZUlkID0gJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybScpLmRhdGEoJ3NwZWNpZmljUHJpY2VJZCcpO1xuICAgIGNvbnN0IHVybCA9IGJhc2VVcmwucmVwbGFjZSgvdXBkYXRlXFwvXFxkKy8sICd1cGRhdGUvJyArIHNwZWNpZmljUHJpY2VJZCk7XG5cbiAgICBjb25zdCBkYXRhID0gJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybSBpbnB1dCwgI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybSBzZWxlY3QsICNmb3JtX2lkX3Byb2R1Y3QnKS5zZXJpYWxpemUoKTtcblxuICAgICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0gLmpzLXNhdmUnKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgIHVybDogdXJsLFxuICAgICAgZGF0YTogZGF0YSxcbiAgICB9KVxuICAgICAgICAuZG9uZShyZXNwb25zZSA9PiB7XG4gICAgICAgICAgc2hvd1N1Y2Nlc3NNZXNzYWdlKHRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snRm9ybSB1cGRhdGUgc3VjY2VzcyddKTtcbiAgICAgICAgICB0aGlzLmNsb3NlRWRpdFByaWNlTW9kYWxBbmRSZW1vdmVGb3JtKCk7XG4gICAgICAgICAgdGhpcy5sb2FkQW5kRGlzcGxheUV4aXN0aW5nU3BlY2lmaWNQcmljZXNMaXN0KCk7XG4gICAgICAgICAgJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybSAuanMtc2F2ZScpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgICAgIH0pXG4gICAgICAgIC5mYWlsKGVycm9ycyA9PiB7XG4gICAgICAgICAgc2hvd0Vycm9yTWVzc2FnZShlcnJvcnMucmVzcG9uc2VKU09OKTtcblxuICAgICAgICAgICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0gLmpzLXNhdmUnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICAgICAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0gc3RyaW5nIGNsaWNrZWRMaW5rIHNlbGVjdG9yXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBkZWxldGVTcGVjaWZpY1ByaWNlKGNsaWNrZWRMaW5rKSB7XG4gICAgbW9kYWxDb25maXJtYXRpb24uY3JlYXRlKHRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snVGhpcyB3aWxsIGRlbGV0ZSB0aGUgc3BlY2lmaWMgcHJpY2UuIERvIHlvdSB3aXNoIHRvIHByb2NlZWQ/J10sIG51bGwsIHtcbiAgICAgIG9uQ29udGludWU6ICgpID0+IHtcblxuICAgICAgICB2YXIgdXJsID0gJChjbGlja2VkTGluaykuYXR0cignaHJlZicpO1xuICAgICAgICAkKGNsaWNrZWRMaW5rKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICAgICAgdXJsOiB1cmwsXG4gICAgICAgIH0pXG4gICAgICAgICAgICAuZG9uZShyZXNwb25zZSA9PiB7XG4gICAgICAgICAgICAgIHRoaXMubG9hZEFuZERpc3BsYXlFeGlzdGluZ1NwZWNpZmljUHJpY2VzTGlzdCgpO1xuICAgICAgICAgICAgICBzaG93U3VjY2Vzc01lc3NhZ2UocmVzcG9uc2UpO1xuICAgICAgICAgICAgICAkKGNsaWNrZWRMaW5rKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICAgICAgICAgICAgfSlcbiAgICAgICAgICAgIC5mYWlsKGVycm9ycyA9PiB7XG4gICAgICAgICAgICAgIHNob3dFcnJvck1lc3NhZ2UoZXJyb3JzLnJlc3BvbnNlSlNPTik7XG4gICAgICAgICAgICAgICQoY2xpY2tlZExpbmspLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgIH0pO1xuICAgICAgfVxuICAgIH0pLnNob3coKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTdG9yZSAnYWRkIHNwZWNpZmljIHByaWNlJyBmb3JtIHZhbHVlc1xuICAgKiBmb3IgZnV0dXJlIHVzYWdlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBzdG9yZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMoKSB7XG4gICAgdmFyIHN0b3JhZ2UgPSB0aGlzLiRjcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0nKS5maW5kKCdzZWxlY3QsaW5wdXQnKS5lYWNoKChpbmRleCwgdmFsdWUpID0+IHtcbiAgICAgIHN0b3JhZ2VbJCh2YWx1ZSkuYXR0cignaWQnKV0gPSAkKHZhbHVlKS52YWwoKTtcbiAgICB9KTtcblxuICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtJykuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKS5lYWNoKChpbmRleCwgdmFsdWUpID0+IHtcbiAgICAgIHN0b3JhZ2VbJCh2YWx1ZSkuYXR0cignaWQnKV0gPSAkKHZhbHVlKS5wcm9wKCdjaGVja2VkJyk7XG4gICAgfSk7XG5cbiAgICB0aGlzLiRjcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzID0gc3RvcmFnZTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0gYm9vbGVhbiB1c2VQcmVmaXhGb3JDcmVhdGVcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGxvYWRBbmRGaWxsT3B0aW9uc0ZvclNlbGVjdENvbWJpbmF0aW9uSW5wdXQodXNlUHJlZml4Rm9yQ3JlYXRlKSB7XG5cbiAgICB2YXIgc2VsZWN0b3JQcmVmaXggPSB0aGlzLmdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSk7XG5cbiAgICB2YXIgaW5wdXRGaWVsZCA9ICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfaWRfcHJvZHVjdF9hdHRyaWJ1dGUnKTtcbiAgICB2YXIgdXJsID0gaW5wdXRGaWVsZC5hdHRyKCdkYXRhLWFjdGlvbicpLnJlcGxhY2UoL3Byb2R1Y3QtY29tYmluYXRpb25zXFwvXFxkKy8sICdwcm9kdWN0LWNvbWJpbmF0aW9ucy8nICsgdGhpcy5nZXRQcm9kdWN0SWQoKSk7XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICB1cmw6IHVybCxcbiAgICB9KVxuICAgICAgICAuZG9uZShjb21iaW5hdGlvbnMgPT4ge1xuICAgICAgICAgIC8qKiByZW1vdmUgYWxsIG9wdGlvbnMgZXhjZXB0IGZpcnN0IG9uZSAqL1xuICAgICAgICAgIGlucHV0RmllbGQuZmluZCgnb3B0aW9uOmd0KDApJykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAkLmVhY2goY29tYmluYXRpb25zLCAoaW5kZXgsIGNvbWJpbmF0aW9uKSA9PiB7XG4gICAgICAgICAgICBpbnB1dEZpZWxkLmFwcGVuZCgnPG9wdGlvbiB2YWx1ZT1cIicgKyBjb21iaW5hdGlvbi5pZCArICdcIj4nICsgY29tYmluYXRpb24ubmFtZSArICc8L29wdGlvbj4nKTtcbiAgICAgICAgICB9KTtcblxuICAgICAgICAgIGlmIChpbnB1dEZpZWxkLmRhdGEoJ3NlbGVjdGVkQXR0cmlidXRlJykgIT0gJzAnKSB7XG4gICAgICAgICAgICBpbnB1dEZpZWxkLnZhbChpbnB1dEZpZWxkLmRhdGEoJ3NlbGVjdGVkQXR0cmlidXRlJykpLnRyaWdnZXIoJ2NoYW5nZScpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIGJvb2xlYW4gdXNlUHJlZml4Rm9yQ3JlYXRlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBlbmFibGVTcGVjaWZpY1ByaWNlVGF4RmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkge1xuXG4gICAgdmFyIHNlbGVjdG9yUHJlZml4ID0gdGhpcy5nZXRQcmVmaXhTZWxlY3Rvcih1c2VQcmVmaXhGb3JDcmVhdGUpO1xuXG4gICAgaWYgKCQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcmVkdWN0aW9uX3R5cGUnKS52YWwoKSA9PT0gJ3BlcmNlbnRhZ2UnKSB7XG4gICAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ3NwX3JlZHVjdGlvbl90YXgnKS5oaWRlKCk7XG4gICAgfSBlbHNlIHtcbiAgICAgICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcmVkdWN0aW9uX3RheCcpLnNob3coKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogUmVzZXQgJ2FkZCBzcGVjaWZpYyBwcmljZScgZm9ybSB2YWx1ZXNcbiAgICogdXNpbmcgcHJldmlvdXNseSBzdG9yZWQgZGVmYXVsdCB2YWx1ZXNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHJlc2V0Q3JlYXRlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcygpIHtcbiAgICB2YXIgcHJldmlvdXNseVN0b3JlZFZhbHVlcyA9IHRoaXMuJGNyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXM7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmZpbmQoJ2lucHV0JykuZWFjaCgoaW5kZXgsIHZhbHVlKSA9PiB7XG4gICAgICAkKHZhbHVlKS52YWwocHJldmlvdXNseVN0b3JlZFZhbHVlc1skKHZhbHVlKS5hdHRyKCdpZCcpXSk7XG4gICAgfSk7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmZpbmQoJ3NlbGVjdCcpLmVhY2goKGluZGV4LCB2YWx1ZSkgPT4ge1xuICAgICAgJCh2YWx1ZSkudmFsKHByZXZpb3VzbHlTdG9yZWRWYWx1ZXNbJCh2YWx1ZSkuYXR0cignaWQnKV0pLmNoYW5nZSgpO1xuICAgIH0pO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0nKS5maW5kKCdpbnB1dDpjaGVja2JveCcpLmVhY2goKGluZGV4LCB2YWx1ZSkgPT4ge1xuICAgICAgJCh2YWx1ZSkucHJvcChcImNoZWNrZWRcIiwgdHJ1ZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIGJvb2xlYW4gdXNlUHJlZml4Rm9yQ3JlYXRlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBlbmFibGVTcGVjaWZpY1ByaWNlRmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkge1xuICAgIHZhciBzZWxlY3RvclByZWZpeCA9IHRoaXMuZ2V0UHJlZml4U2VsZWN0b3IodXNlUHJlZml4Rm9yQ3JlYXRlKTtcblxuICAgICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcHJpY2UnKS5wcm9wKCdkaXNhYmxlZCcsICQoc2VsZWN0b3JQcmVmaXggKyAnbGVhdmVfYnByaWNlJykuaXMoJzpjaGVja2VkJykpLnZhbCgnJyk7XG4gIH1cblxuICAvKipcbiAgICogT3BlbiAnZWRpdCBzcGVjaWZpYyBwcmljZScgZm9ybSBpbnRvIGEgbW9kYWxcbiAgICpcbiAgICogQHBhcmFtIGludGVnZXIgc3BlY2lmaWNQcmljZUlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBvcGVuRWRpdFByaWNlTW9kYWxBbmRMb2FkRm9ybShzcGVjaWZpY1ByaWNlSWQpIHtcbiAgICBjb25zdCB1cmwgPSAkKCcjanMtc3BlY2lmaWMtcHJpY2UtbGlzdCcpLmRhdGEoJ2FjdGlvbkVkaXQnKS5yZXBsYWNlKC9mb3JtXFwvXFxkKy8sICdmb3JtLycgKyBzcGVjaWZpY1ByaWNlSWQpO1xuXG4gICAgJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwnKS5tb2RhbChcInNob3dcIik7XG4gICAgdGhpcy5lZGl0TW9kYWxJc09wZW4gPSB0cnVlO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdHRVQnLFxuICAgICAgdXJsOiB1cmwsXG4gICAgfSlcbiAgICAgICAgLmRvbmUocmVzcG9uc2UgPT4ge1xuICAgICAgICAgIHRoaXMuaW5zZXJ0RWRpdFNwZWNpZmljUHJpY2VGb3JtSW50b01vZGFsKHJlc3BvbnNlKTtcbiAgICAgICAgICAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtJykuZGF0YSgnc3BlY2lmaWNQcmljZUlkJywgc3BlY2lmaWNQcmljZUlkKTtcbiAgICAgICAgICB0aGlzLmNvbmZpZ3VyZUVkaXRQcmljZUZvcm1JbnNpZGVNb2RhbEJlaGF2aW9yKCk7XG4gICAgICAgIH0pXG4gICAgICAgIC5mYWlsKGVycm9ycyA9PiB7XG4gICAgICAgICAgc2hvd0Vycm9yTWVzc2FnZShlcnJvcnMucmVzcG9uc2VKU09OKTtcbiAgICAgICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGNsb3NlRWRpdFByaWNlTW9kYWxBbmRSZW1vdmVGb3JtKCkge1xuICAgICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsJykubW9kYWwoXCJoaWRlXCIpO1xuICAgIHRoaXMuZWRpdE1vZGFsSXNPcGVuID0gZmFsc2U7XG5cbiAgICB2YXIgZm9ybUxvY2F0aW9uSG9sZGVyID0gJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybScpO1xuXG4gICAgZm9ybUxvY2F0aW9uSG9sZGVyLmVtcHR5KCk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIHN0cmluZyBmb3JtOiBIVE1MICdlZGl0IHNwZWNpZmljIHByaWNlJyBmb3JtXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBpbnNlcnRFZGl0U3BlY2lmaWNQcmljZUZvcm1JbnRvTW9kYWwoZm9ybSkge1xuICAgIHZhciBmb3JtTG9jYXRpb25Ib2xkZXIgPSAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtJyk7XG5cbiAgICBmb3JtTG9jYXRpb25Ib2xkZXIuZW1wdHkoKTtcbiAgICBmb3JtTG9jYXRpb25Ib2xkZXIuYXBwZW5kKGZvcm0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBwcm9kdWN0IElEIGZvciBjdXJyZW50IENhdGFsb2cgUHJvZHVjdCBwYWdlXG4gICAqXG4gICAqIEByZXR1cm5zIGludGVnZXJcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGdldFByb2R1Y3RJZCgpIHtcbiAgICByZXR1cm4gJCgnI2Zvcm1faWRfcHJvZHVjdCcpLnZhbCgpO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSBib29sZWFuIHVzZVByZWZpeEZvckNyZWF0ZVxuICAgKlxuICAgKiBAcmV0dXJucyBzdHJpbmdcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSkge1xuICAgIGlmICh1c2VQcmVmaXhGb3JDcmVhdGUgPT0gdHJ1ZSkge1xuICAgICAgcmV0dXJuICcjJyArIHRoaXMucHJlZml4Q3JlYXRlRm9ybTtcbiAgICB9IGVsc2Uge1xuICAgICAgcmV0dXJuICcjJyArIHRoaXMucHJlZml4RWRpdEZvcm07XG4gICAgfVxuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IFNwZWNpZmljUHJpY2VGb3JtSGFuZGxlcjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2NhdGFsb2cvcHJvZHVjdC9zcGVjaWZpYy1wcmljZS1mb3JtLWhhbmRsZXIuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCBTcGVjaWZpY1ByaWNlRm9ybUhhbmRsZXIgZnJvbSAnLi9zcGVjaWZpYy1wcmljZS1mb3JtLWhhbmRsZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBuZXcgU3BlY2lmaWNQcmljZUZvcm1IYW5kbGVyKCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2NhdGFsb2cvcHJvZHVjdC9pbmRleC5qcyIsIi8vIGh0dHBzOi8vZ2l0aHViLmNvbS96bG9pcm9jay9jb3JlLWpzL2lzc3Vlcy84NiNpc3N1ZWNvbW1lbnQtMTE1NzU5MDI4XG52YXIgZ2xvYmFsID0gbW9kdWxlLmV4cG9ydHMgPSB0eXBlb2Ygd2luZG93ICE9ICd1bmRlZmluZWQnICYmIHdpbmRvdy5NYXRoID09IE1hdGhcbiAgPyB3aW5kb3cgOiB0eXBlb2Ygc2VsZiAhPSAndW5kZWZpbmVkJyAmJiBzZWxmLk1hdGggPT0gTWF0aCA/IHNlbGYgOiBGdW5jdGlvbigncmV0dXJuIHRoaXMnKSgpO1xuaWYodHlwZW9mIF9fZyA9PSAnbnVtYmVyJylfX2cgPSBnbG9iYWw7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tdW5kZWZcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2dsb2JhbC5qc1xuLy8gbW9kdWxlIGlkID0gNVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgYW5PYmplY3QgICAgICAgPSByZXF1aXJlKCcuL19hbi1vYmplY3QnKVxuICAsIElFOF9ET01fREVGSU5FID0gcmVxdWlyZSgnLi9faWU4LWRvbS1kZWZpbmUnKVxuICAsIHRvUHJpbWl0aXZlICAgID0gcmVxdWlyZSgnLi9fdG8tcHJpbWl0aXZlJylcbiAgLCBkUCAgICAgICAgICAgICA9IE9iamVjdC5kZWZpbmVQcm9wZXJ0eTtcblxuZXhwb3J0cy5mID0gcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSA/IE9iamVjdC5kZWZpbmVQcm9wZXJ0eSA6IGZ1bmN0aW9uIGRlZmluZVByb3BlcnR5KE8sIFAsIEF0dHJpYnV0ZXMpe1xuICBhbk9iamVjdChPKTtcbiAgUCA9IHRvUHJpbWl0aXZlKFAsIHRydWUpO1xuICBhbk9iamVjdChBdHRyaWJ1dGVzKTtcbiAgaWYoSUU4X0RPTV9ERUZJTkUpdHJ5IHtcbiAgICByZXR1cm4gZFAoTywgUCwgQXR0cmlidXRlcyk7XG4gIH0gY2F0Y2goZSl7IC8qIGVtcHR5ICovIH1cbiAgaWYoJ2dldCcgaW4gQXR0cmlidXRlcyB8fCAnc2V0JyBpbiBBdHRyaWJ1dGVzKXRocm93IFR5cGVFcnJvcignQWNjZXNzb3JzIG5vdCBzdXBwb3J0ZWQhJyk7XG4gIGlmKCd2YWx1ZScgaW4gQXR0cmlidXRlcylPW1BdID0gQXR0cmlidXRlcy52YWx1ZTtcbiAgcmV0dXJuIE87XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWRwLmpzXG4vLyBtb2R1bGUgaWQgPSA2XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oZXhlYyl7XG4gIHRyeSB7XG4gICAgcmV0dXJuICEhZXhlYygpO1xuICB9IGNhdGNoKGUpe1xuICAgIHJldHVybiB0cnVlO1xuICB9XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZmFpbHMuanNcbi8vIG1vZHVsZSBpZCA9IDdcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGdsb2JhbCAgICA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpXG4gICwgY29yZSAgICAgID0gcmVxdWlyZSgnLi9fY29yZScpXG4gICwgY3R4ICAgICAgID0gcmVxdWlyZSgnLi9fY3R4JylcbiAgLCBoaWRlICAgICAgPSByZXF1aXJlKCcuL19oaWRlJylcbiAgLCBQUk9UT1RZUEUgPSAncHJvdG90eXBlJztcblxudmFyICRleHBvcnQgPSBmdW5jdGlvbih0eXBlLCBuYW1lLCBzb3VyY2Upe1xuICB2YXIgSVNfRk9SQ0VEID0gdHlwZSAmICRleHBvcnQuRlxuICAgICwgSVNfR0xPQkFMID0gdHlwZSAmICRleHBvcnQuR1xuICAgICwgSVNfU1RBVElDID0gdHlwZSAmICRleHBvcnQuU1xuICAgICwgSVNfUFJPVE8gID0gdHlwZSAmICRleHBvcnQuUFxuICAgICwgSVNfQklORCAgID0gdHlwZSAmICRleHBvcnQuQlxuICAgICwgSVNfV1JBUCAgID0gdHlwZSAmICRleHBvcnQuV1xuICAgICwgZXhwb3J0cyAgID0gSVNfR0xPQkFMID8gY29yZSA6IGNvcmVbbmFtZV0gfHwgKGNvcmVbbmFtZV0gPSB7fSlcbiAgICAsIGV4cFByb3RvICA9IGV4cG9ydHNbUFJPVE9UWVBFXVxuICAgICwgdGFyZ2V0ICAgID0gSVNfR0xPQkFMID8gZ2xvYmFsIDogSVNfU1RBVElDID8gZ2xvYmFsW25hbWVdIDogKGdsb2JhbFtuYW1lXSB8fCB7fSlbUFJPVE9UWVBFXVxuICAgICwga2V5LCBvd24sIG91dDtcbiAgaWYoSVNfR0xPQkFMKXNvdXJjZSA9IG5hbWU7XG4gIGZvcihrZXkgaW4gc291cmNlKXtcbiAgICAvLyBjb250YWlucyBpbiBuYXRpdmVcbiAgICBvd24gPSAhSVNfRk9SQ0VEICYmIHRhcmdldCAmJiB0YXJnZXRba2V5XSAhPT0gdW5kZWZpbmVkO1xuICAgIGlmKG93biAmJiBrZXkgaW4gZXhwb3J0cyljb250aW51ZTtcbiAgICAvLyBleHBvcnQgbmF0aXZlIG9yIHBhc3NlZFxuICAgIG91dCA9IG93biA/IHRhcmdldFtrZXldIDogc291cmNlW2tleV07XG4gICAgLy8gcHJldmVudCBnbG9iYWwgcG9sbHV0aW9uIGZvciBuYW1lc3BhY2VzXG4gICAgZXhwb3J0c1trZXldID0gSVNfR0xPQkFMICYmIHR5cGVvZiB0YXJnZXRba2V5XSAhPSAnZnVuY3Rpb24nID8gc291cmNlW2tleV1cbiAgICAvLyBiaW5kIHRpbWVycyB0byBnbG9iYWwgZm9yIGNhbGwgZnJvbSBleHBvcnQgY29udGV4dFxuICAgIDogSVNfQklORCAmJiBvd24gPyBjdHgob3V0LCBnbG9iYWwpXG4gICAgLy8gd3JhcCBnbG9iYWwgY29uc3RydWN0b3JzIGZvciBwcmV2ZW50IGNoYW5nZSB0aGVtIGluIGxpYnJhcnlcbiAgICA6IElTX1dSQVAgJiYgdGFyZ2V0W2tleV0gPT0gb3V0ID8gKGZ1bmN0aW9uKEMpe1xuICAgICAgdmFyIEYgPSBmdW5jdGlvbihhLCBiLCBjKXtcbiAgICAgICAgaWYodGhpcyBpbnN0YW5jZW9mIEMpe1xuICAgICAgICAgIHN3aXRjaChhcmd1bWVudHMubGVuZ3RoKXtcbiAgICAgICAgICAgIGNhc2UgMDogcmV0dXJuIG5ldyBDO1xuICAgICAgICAgICAgY2FzZSAxOiByZXR1cm4gbmV3IEMoYSk7XG4gICAgICAgICAgICBjYXNlIDI6IHJldHVybiBuZXcgQyhhLCBiKTtcbiAgICAgICAgICB9IHJldHVybiBuZXcgQyhhLCBiLCBjKTtcbiAgICAgICAgfSByZXR1cm4gQy5hcHBseSh0aGlzLCBhcmd1bWVudHMpO1xuICAgICAgfTtcbiAgICAgIEZbUFJPVE9UWVBFXSA9IENbUFJPVE9UWVBFXTtcbiAgICAgIHJldHVybiBGO1xuICAgIC8vIG1ha2Ugc3RhdGljIHZlcnNpb25zIGZvciBwcm90b3R5cGUgbWV0aG9kc1xuICAgIH0pKG91dCkgOiBJU19QUk9UTyAmJiB0eXBlb2Ygb3V0ID09ICdmdW5jdGlvbicgPyBjdHgoRnVuY3Rpb24uY2FsbCwgb3V0KSA6IG91dDtcbiAgICAvLyBleHBvcnQgcHJvdG8gbWV0aG9kcyB0byBjb3JlLiVDT05TVFJVQ1RPUiUubWV0aG9kcy4lTkFNRSVcbiAgICBpZihJU19QUk9UTyl7XG4gICAgICAoZXhwb3J0cy52aXJ0dWFsIHx8IChleHBvcnRzLnZpcnR1YWwgPSB7fSkpW2tleV0gPSBvdXQ7XG4gICAgICAvLyBleHBvcnQgcHJvdG8gbWV0aG9kcyB0byBjb3JlLiVDT05TVFJVQ1RPUiUucHJvdG90eXBlLiVOQU1FJVxuICAgICAgaWYodHlwZSAmICRleHBvcnQuUiAmJiBleHBQcm90byAmJiAhZXhwUHJvdG9ba2V5XSloaWRlKGV4cFByb3RvLCBrZXksIG91dCk7XG4gICAgfVxuICB9XG59O1xuLy8gdHlwZSBiaXRtYXBcbiRleHBvcnQuRiA9IDE7ICAgLy8gZm9yY2VkXG4kZXhwb3J0LkcgPSAyOyAgIC8vIGdsb2JhbFxuJGV4cG9ydC5TID0gNDsgICAvLyBzdGF0aWNcbiRleHBvcnQuUCA9IDg7ICAgLy8gcHJvdG9cbiRleHBvcnQuQiA9IDE2OyAgLy8gYmluZFxuJGV4cG9ydC5XID0gMzI7ICAvLyB3cmFwXG4kZXhwb3J0LlUgPSA2NDsgIC8vIHNhZmVcbiRleHBvcnQuUiA9IDEyODsgLy8gcmVhbCBwcm90byBtZXRob2QgZm9yIGBsaWJyYXJ5YCBcbm1vZHVsZS5leHBvcnRzID0gJGV4cG9ydDtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2V4cG9ydC5qc1xuLy8gbW9kdWxlIGlkID0gOFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiXSwic291cmNlUm9vdCI6IiJ9