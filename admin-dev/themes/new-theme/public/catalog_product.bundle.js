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
/******/ 	return __webpack_require__(__webpack_require__.s = 314);
/******/ })
/************************************************************************/
/******/ ({

/***/ 250:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2019 PrestaShop and Contributors
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

var $ = window.$;

var SpecificPriceFormHandler = function () {
  function SpecificPriceFormHandler() {
    _classCallCheck(this, SpecificPriceFormHandler);

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


  _createClass(SpecificPriceFormHandler, [{
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

/***/ 314:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _specificPriceFormHandler = __webpack_require__(250);

var _specificPriceFormHandler2 = _interopRequireDefault(_specificPriceFormHandler);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
                   * 2007-2019 PrestaShop and Contributors
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

$(function () {
  new _specificPriceFormHandler2.default();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQ/MjBkNCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2NhdGFsb2cvcHJvZHVjdC9zcGVjaWZpYy1wcmljZS1mb3JtLWhhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvY2F0YWxvZy9wcm9kdWN0L2luZGV4LmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJTcGVjaWZpY1ByaWNlRm9ybUhhbmRsZXIiLCJwcmVmaXhDcmVhdGVGb3JtIiwicHJlZml4RWRpdEZvcm0iLCJlZGl0TW9kYWxJc09wZW4iLCIkY3JlYXRlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcyIsIk9iamVjdCIsInN0b3JlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcyIsImxvYWRBbmREaXNwbGF5RXhpc3RpbmdTcGVjaWZpY1ByaWNlc0xpc3QiLCJjb25maWd1cmVBZGRQcmljZUZvcm1CZWhhdmlvciIsImNvbmZpZ3VyZUVkaXRQcmljZU1vZGFsQmVoYXZpb3IiLCJjb25maWd1cmVEZWxldGVQcmljZUJ1dHRvbnNCZWhhdmlvciIsImNvbmZpZ3VyZU11bHRpcGxlTW9kYWxzQmVoYXZpb3IiLCJsaXN0Q29udGFpbmVyIiwidXJsIiwiZGF0YSIsInJlcGxhY2UiLCJnZXRQcm9kdWN0SWQiLCJhamF4IiwidHlwZSIsImRvbmUiLCJ0Ym9keSIsImZpbmQiLCJyZW1vdmUiLCJzcGVjaWZpY1ByaWNlcyIsImxlbmd0aCIsInJlbW92ZUNsYXNzIiwiYWRkQ2xhc3MiLCJzcGVjaWZpY1ByaWNlc0xpc3QiLCJyZW5kZXJTcGVjaWZpY1ByaWNlc0xpc3RpbmdBc0h0bWwiLCJhcHBlbmQiLCJzZWxmIiwiZWFjaCIsImluZGV4Iiwic3BlY2lmaWNQcmljZSIsImRlbGV0ZVVybCIsImF0dHIiLCJpZF9zcGVjaWZpY19wcmljZSIsInJvdyIsInJlbmRlclNwZWNpZmljUHJpY2VSb3ciLCJzcGVjaWZpY1ByaWNlSWQiLCJydWxlX25hbWUiLCJhdHRyaWJ1dGVzX25hbWUiLCJjdXJyZW5jeSIsImNvdW50cnkiLCJncm91cCIsImN1c3RvbWVyIiwiZml4ZWRfcHJpY2UiLCJpbXBhY3QiLCJwZXJpb2QiLCJmcm9tX3F1YW50aXR5IiwiY2FuX2RlbGV0ZSIsImNhbl9lZGl0IiwidXNlUHJlZml4Rm9yQ3JlYXRlIiwic2VsZWN0b3JQcmVmaXgiLCJnZXRQcmVmaXhTZWxlY3RvciIsImNsaWNrIiwicmVzZXRDcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzIiwiY29sbGFwc2UiLCJvbiIsInN1Ym1pdENyZWF0ZVByaWNlRm9ybSIsImxvYWRBbmRGaWxsT3B0aW9uc0ZvclNlbGVjdENvbWJpbmF0aW9uSW5wdXQiLCJlbmFibGVTcGVjaWZpY1ByaWNlRmllbGRJZkVsaWdpYmxlIiwiZW5hYmxlU3BlY2lmaWNQcmljZVRheEZpZWxkSWZFbGlnaWJsZSIsImNsb3NlRWRpdFByaWNlTW9kYWxBbmRSZW1vdmVGb3JtIiwic3VibWl0RWRpdFByaWNlRm9ybSIsInJlaW5pdGlhbGl6ZURhdGVQaWNrZXJzIiwiaW5pdGlhbGl6ZUxlYXZlQlByaWNlRmllbGQiLCJkYXRldGltZXBpY2tlciIsImZvcm1hdCIsInZhbCIsInByb3AiLCJkb2N1bWVudCIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCJjdXJyZW50VGFyZ2V0Iiwib3BlbkVkaXRQcmljZU1vZGFsQW5kTG9hZEZvcm0iLCJkZWxldGVTcGVjaWZpY1ByaWNlIiwic2VyaWFsaXplIiwic2hvd1N1Y2Nlc3NNZXNzYWdlIiwidHJhbnNsYXRlX2phdmFzY3JpcHRzIiwicmVtb3ZlQXR0ciIsImZhaWwiLCJzaG93RXJyb3JNZXNzYWdlIiwiZXJyb3JzIiwicmVzcG9uc2VKU09OIiwiYmFzZVVybCIsImNsaWNrZWRMaW5rIiwibW9kYWxDb25maXJtYXRpb24iLCJjcmVhdGUiLCJvbkNvbnRpbnVlIiwicmVzcG9uc2UiLCJzaG93Iiwic3RvcmFnZSIsInZhbHVlIiwiaW5wdXRGaWVsZCIsImNvbWJpbmF0aW9ucyIsImNvbWJpbmF0aW9uIiwiaWQiLCJuYW1lIiwidHJpZ2dlciIsImhpZGUiLCJwcmV2aW91c2x5U3RvcmVkVmFsdWVzIiwiY2hhbmdlIiwiaXMiLCJtb2RhbCIsImluc2VydEVkaXRTcGVjaWZpY1ByaWNlRm9ybUludG9Nb2RhbCIsImNvbmZpZ3VyZUVkaXRQcmljZUZvcm1JbnNpZGVNb2RhbEJlaGF2aW9yIiwiZm9ybUxvY2F0aW9uSG9sZGVyIiwiZW1wdHkiLCJmb3JtIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDaEVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLElBQU1BLElBQUlDLE9BQU9ELENBQWpCOztJQUVNRSx3QjtBQUVKLHNDQUFjO0FBQUE7O0FBQ1osU0FBS0MsZ0JBQUwsR0FBd0IsNEJBQXhCO0FBQ0EsU0FBS0MsY0FBTCxHQUFzQixhQUF0QjtBQUNBLFNBQUtDLGVBQUwsR0FBdUIsS0FBdkI7O0FBRUEsU0FBS0MsNkJBQUwsR0FBcUMsSUFBSUMsTUFBSixFQUFyQztBQUNBLFNBQUtDLDJCQUFMOztBQUVBLFNBQUtDLHdDQUFMOztBQUVBLFNBQUtDLDZCQUFMOztBQUVB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QkFHWUYsd0I7Ozs7Ozs7Ozs7QUN2ZGY7Ozs7OztBQUVBLElBQU1GLElBQUlDLE9BQU9ELENBQWpCLEMsQ0EzQkE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUE2QkFBLEVBQUUsWUFBTTtBQUNOLE1BQUlFLGtDQUFKO0FBQ0QsQ0FGRCxFIiwiZmlsZSI6ImNhdGFsb2dfcHJvZHVjdC5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDMxNCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmNsYXNzIFNwZWNpZmljUHJpY2VGb3JtSGFuZGxlciB7XG5cbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5wcmVmaXhDcmVhdGVGb3JtID0gJ2Zvcm1fc3RlcDJfc3BlY2lmaWNfcHJpY2VfJztcbiAgICB0aGlzLnByZWZpeEVkaXRGb3JtID0gJ2Zvcm1fbW9kYWxfJztcbiAgICB0aGlzLmVkaXRNb2RhbElzT3BlbiA9IGZhbHNlO1xuXG4gICAgdGhpcy4kY3JlYXRlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcyA9IG5ldyBPYmplY3QoKTtcbiAgICB0aGlzLnN0b3JlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcygpO1xuXG4gICAgdGhpcy5sb2FkQW5kRGlzcGxheUV4aXN0aW5nU3BlY2lmaWNQcmljZXNMaXN0KCk7XG5cbiAgICB0aGlzLmNvbmZpZ3VyZUFkZFByaWNlRm9ybUJlaGF2aW9yKCk7XG5cbiAgICB0aGlzLmNvbmZpZ3VyZUVkaXRQcmljZU1vZGFsQmVoYXZpb3IoKTtcblxuICAgIHRoaXMuY29uZmlndXJlRGVsZXRlUHJpY2VCdXR0b25zQmVoYXZpb3IoKTtcblxuICAgIHRoaXMuY29uZmlndXJlTXVsdGlwbGVNb2RhbHNCZWhhdmlvcigpO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBsb2FkQW5kRGlzcGxheUV4aXN0aW5nU3BlY2lmaWNQcmljZXNMaXN0KCkge1xuICAgIHZhciBsaXN0Q29udGFpbmVyID0gJCgnI2pzLXNwZWNpZmljLXByaWNlLWxpc3QnKTtcbiAgICB2YXIgdXJsID0gbGlzdENvbnRhaW5lci5kYXRhKCdsaXN0aW5nVXJsJykucmVwbGFjZSgvbGlzdFxcL1xcZCsvLCAnbGlzdC8nICsgdGhpcy5nZXRQcm9kdWN0SWQoKSk7XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICB1cmw6IHVybCxcbiAgICB9KVxuICAgICAgICAuZG9uZShzcGVjaWZpY1ByaWNlcyA9PiB7XG4gICAgICAgICAgdmFyIHRib2R5ID0gbGlzdENvbnRhaW5lci5maW5kKCd0Ym9keScpO1xuICAgICAgICAgIHRib2R5LmZpbmQoJ3RyJykucmVtb3ZlKCk7XG5cbiAgICAgICAgICBpZiAoc3BlY2lmaWNQcmljZXMubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgbGlzdENvbnRhaW5lci5yZW1vdmVDbGFzcygnaGlkZScpO1xuICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBsaXN0Q29udGFpbmVyLmFkZENsYXNzKCdoaWRlJyk7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgdmFyIHNwZWNpZmljUHJpY2VzTGlzdCA9IHRoaXMucmVuZGVyU3BlY2lmaWNQcmljZXNMaXN0aW5nQXNIdG1sKHNwZWNpZmljUHJpY2VzKTtcblxuICAgICAgICAgIHRib2R5LmFwcGVuZChzcGVjaWZpY1ByaWNlc0xpc3QpO1xuICAgICAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0gYXJyYXkgc3BlY2lmaWNQcmljZXNcbiAgICpcbiAgICogQHJldHVybnMgc3RyaW5nXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICByZW5kZXJTcGVjaWZpY1ByaWNlc0xpc3RpbmdBc0h0bWwoc3BlY2lmaWNQcmljZXMpIHtcbiAgICB2YXIgc3BlY2lmaWNQcmljZXNMaXN0ID0gJyc7XG5cbiAgICB2YXIgc2VsZiA9IHRoaXM7XG5cbiAgICAkLmVhY2goc3BlY2lmaWNQcmljZXMsIChpbmRleCwgc3BlY2lmaWNQcmljZSkgPT4ge1xuICAgICAgdmFyIGRlbGV0ZVVybCA9ICQoJyNqcy1zcGVjaWZpYy1wcmljZS1saXN0JykuYXR0cignZGF0YS1hY3Rpb24tZGVsZXRlJykucmVwbGFjZSgvZGVsZXRlXFwvXFxkKy8sICdkZWxldGUvJyArIHNwZWNpZmljUHJpY2UuaWRfc3BlY2lmaWNfcHJpY2UpO1xuICAgICAgdmFyIHJvdyA9IHNlbGYucmVuZGVyU3BlY2lmaWNQcmljZVJvdyhzcGVjaWZpY1ByaWNlLCBkZWxldGVVcmwpO1xuXG4gICAgICBzcGVjaWZpY1ByaWNlc0xpc3QgPSBzcGVjaWZpY1ByaWNlc0xpc3QgKyByb3c7XG4gICAgfSk7XG5cbiAgICByZXR1cm4gc3BlY2lmaWNQcmljZXNMaXN0O1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSBPYmplY3Qgc3BlY2lmaWNQcmljZVxuICAgKiBAcGFyYW0gc3RyaW5nIGRlbGV0ZVVybFxuICAgKlxuICAgKiBAcmV0dXJucyBzdHJpbmdcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHJlbmRlclNwZWNpZmljUHJpY2VSb3coc3BlY2lmaWNQcmljZSwgZGVsZXRlVXJsKSB7XG5cbiAgICB2YXIgc3BlY2lmaWNQcmljZUlkID0gc3BlY2lmaWNQcmljZS5pZF9zcGVjaWZpY19wcmljZTtcblxuICAgIHZhciByb3cgPSAnPHRyPicgK1xuICAgICAgICAnPHRkPicgKyBzcGVjaWZpY1ByaWNlLnJ1bGVfbmFtZSArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyBzcGVjaWZpY1ByaWNlLmF0dHJpYnV0ZXNfbmFtZSArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyBzcGVjaWZpY1ByaWNlLmN1cnJlbmN5ICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuY291bnRyeSArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyBzcGVjaWZpY1ByaWNlLmdyb3VwICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuY3VzdG9tZXIgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5maXhlZF9wcmljZSArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyBzcGVjaWZpY1ByaWNlLmltcGFjdCArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyBzcGVjaWZpY1ByaWNlLnBlcmlvZCArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyBzcGVjaWZpY1ByaWNlLmZyb21fcXVhbnRpdHkgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgKHNwZWNpZmljUHJpY2UuY2FuX2RlbGV0ZSA/ICc8YSBocmVmPVwiJyArIGRlbGV0ZVVybCArICdcIiBjbGFzcz1cImpzLWRlbGV0ZSBkZWxldGUgYnRuIHRvb2x0aXAtbGluayBkZWxldGUgcGwtMCBwci0wXCI+PGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmRlbGV0ZTwvaT48L2E+JyA6ICcnKSArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyAoc3BlY2lmaWNQcmljZS5jYW5fZWRpdCA/ICc8YSBocmVmPVwiI1wiIGRhdGEtc3BlY2lmaWMtcHJpY2UtaWQ9XCInICsgc3BlY2lmaWNQcmljZUlkICsgJ1wiIGNsYXNzPVwianMtZWRpdCBlZGl0IGJ0biB0b29sdGlwLWxpbmsgZGVsZXRlIHBsLTAgcHItMFwiPjxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5lZGl0PC9pPjwvYT4nIDogJycpICsgJzwvdGQ+JyArXG4gICAgICAgICc8L3RyPic7XG5cbiAgICByZXR1cm4gcm93O1xuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBjb25maWd1cmVBZGRQcmljZUZvcm1CZWhhdmlvcigpIHtcbiAgICBjb25zdCB1c2VQcmVmaXhGb3JDcmVhdGUgPSB0cnVlO1xuICAgIHZhciBzZWxlY3RvclByZWZpeCA9IHRoaXMuZ2V0UHJlZml4U2VsZWN0b3IodXNlUHJlZml4Rm9yQ3JlYXRlKTtcblxuICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtIC5qcy1jYW5jZWwnKS5jbGljaygoKSA9PiB7XG4gICAgICB0aGlzLnJlc2V0Q3JlYXRlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcygpO1xuICAgICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0nKS5jb2xsYXBzZSgnaGlkZScpO1xuICAgIH0pO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0gLmpzLXNhdmUnKS5vbignY2xpY2snLCAoKSA9PiB0aGlzLnN1Ym1pdENyZWF0ZVByaWNlRm9ybSgpKTtcblxuICAgICQoJyNqcy1vcGVuLWNyZWF0ZS1zcGVjaWZpYy1wcmljZS1mb3JtJykub24oJ2NsaWNrJywgKCkgPT4gdGhpcy5sb2FkQW5kRmlsbE9wdGlvbnNGb3JTZWxlY3RDb21iaW5hdGlvbklucHV0KHVzZVByZWZpeEZvckNyZWF0ZSkpO1xuXG4gICAgJChzZWxlY3RvclByZWZpeCArICdsZWF2ZV9icHJpY2UnKS5vbignY2xpY2snLCAoKSA9PiB0aGlzLmVuYWJsZVNwZWNpZmljUHJpY2VGaWVsZElmRWxpZ2libGUodXNlUHJlZml4Rm9yQ3JlYXRlKSk7XG5cbiAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ3NwX3JlZHVjdGlvbl90eXBlJykub24oJ2NoYW5nZScsICgpID0+IHRoaXMuZW5hYmxlU3BlY2lmaWNQcmljZVRheEZpZWxkSWZFbGlnaWJsZSh1c2VQcmVmaXhGb3JDcmVhdGUpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgY29uZmlndXJlRWRpdFByaWNlRm9ybUluc2lkZU1vZGFsQmVoYXZpb3IoKSB7XG4gICAgY29uc3QgdXNlUHJlZml4Rm9yQ3JlYXRlID0gZmFsc2U7XG4gICAgdmFyIHNlbGVjdG9yUHJlZml4ID0gdGhpcy5nZXRQcmVmaXhTZWxlY3Rvcih1c2VQcmVmaXhGb3JDcmVhdGUpO1xuXG4gICAgJCgnI2Zvcm1fbW9kYWxfY2FuY2VsJykuY2xpY2soKCkgPT4gdGhpcy5jbG9zZUVkaXRQcmljZU1vZGFsQW5kUmVtb3ZlRm9ybSgpKTtcbiAgICAkKCcjZm9ybV9tb2RhbF9jbG9zZScpLmNsaWNrKCgpID0+IHRoaXMuY2xvc2VFZGl0UHJpY2VNb2RhbEFuZFJlbW92ZUZvcm0oKSk7XG5cbiAgICAkKCcjZm9ybV9tb2RhbF9zYXZlJykuY2xpY2soKCkgPT4gdGhpcy5zdWJtaXRFZGl0UHJpY2VGb3JtKCkpO1xuXG4gICAgdGhpcy5sb2FkQW5kRmlsbE9wdGlvbnNGb3JTZWxlY3RDb21iaW5hdGlvbklucHV0KHVzZVByZWZpeEZvckNyZWF0ZSk7XG5cbiAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ2xlYXZlX2JwcmljZScpLm9uKCdjbGljaycsICgpID0+IHRoaXMuZW5hYmxlU3BlY2lmaWNQcmljZUZpZWxkSWZFbGlnaWJsZSh1c2VQcmVmaXhGb3JDcmVhdGUpKTtcblxuICAgICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcmVkdWN0aW9uX3R5cGUnKS5vbignY2hhbmdlJywgKCkgPT4gdGhpcy5lbmFibGVTcGVjaWZpY1ByaWNlVGF4RmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkpO1xuXG4gICAgdGhpcy5yZWluaXRpYWxpemVEYXRlUGlja2VycygpO1xuXG4gICAgdGhpcy5pbml0aWFsaXplTGVhdmVCUHJpY2VGaWVsZCh1c2VQcmVmaXhGb3JDcmVhdGUpO1xuICAgIHRoaXMuZW5hYmxlU3BlY2lmaWNQcmljZVRheEZpZWxkSWZFbGlnaWJsZSh1c2VQcmVmaXhGb3JDcmVhdGUpO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICByZWluaXRpYWxpemVEYXRlUGlja2VycygpIHtcbiAgICAkKCcuZGF0ZXBpY2tlciBpbnB1dCcpLmRhdGV0aW1lcGlja2VyKHtmb3JtYXQ6ICdZWVlZLU1NLUREJ30pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSBib29sZWFuIHVzZVByZWZpeEZvckNyZWF0ZVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgaW5pdGlhbGl6ZUxlYXZlQlByaWNlRmllbGQodXNlUHJlZml4Rm9yQ3JlYXRlKSB7XG4gICAgdmFyIHNlbGVjdG9yUHJlZml4ID0gdGhpcy5nZXRQcmVmaXhTZWxlY3Rvcih1c2VQcmVmaXhGb3JDcmVhdGUpO1xuXG4gICAgaWYgKCQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcHJpY2UnKS52YWwoKSAhPSAnJykge1xuICAgICAgJChzZWxlY3RvclByZWZpeCArICdzcF9wcmljZScpLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICAgICAgJChzZWxlY3RvclByZWZpeCArICdsZWF2ZV9icHJpY2UnKS5wcm9wKCdjaGVja2VkJywgZmFsc2UpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgY29uZmlndXJlRWRpdFByaWNlTW9kYWxCZWhhdmlvcigpIHtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnI2pzLXNwZWNpZmljLXByaWNlLWxpc3QgLmpzLWVkaXQnLCAoZXZlbnQpID0+IHtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgIHZhciBzcGVjaWZpY1ByaWNlSWQgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ3NwZWNpZmljUHJpY2VJZCcpO1xuXG4gICAgICB0aGlzLm9wZW5FZGl0UHJpY2VNb2RhbEFuZExvYWRGb3JtKHNwZWNpZmljUHJpY2VJZCk7XG4gICAgfSk7XG5cbiAgfVxuXG4gIC8qKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgY29uZmlndXJlRGVsZXRlUHJpY2VCdXR0b25zQmVoYXZpb3IoKSB7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJyNqcy1zcGVjaWZpYy1wcmljZS1saXN0IC5qcy1kZWxldGUnLCAoZXZlbnQpID0+IHtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICB0aGlzLmRlbGV0ZVNwZWNpZmljUHJpY2UoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHNlZSBodHRwczovL3ZpamF5YXNhbmthcm4ud29yZHByZXNzLmNvbS8yMDE3LzAyLzI0L3F1aWNrLWZpeC1zY3JvbGxpbmctYW5kLWZvY3VzLXdoZW4tbXVsdGlwbGUtYm9vdHN0cmFwLW1vZGFscy1hcmUtb3Blbi9cbiAgICovXG4gIGNvbmZpZ3VyZU11bHRpcGxlTW9kYWxzQmVoYXZpb3IoKSB7XG4gICAgJCgnLm1vZGFsJykub24oJ2hpZGRlbi5icy5tb2RhbCcsICgpID0+IHtcbiAgICAgIGlmICh0aGlzLmVkaXRNb2RhbElzT3Blbikge1xuICAgICAgICAkKCdib2R5JykuYWRkQ2xhc3MoJ21vZGFsLW9wZW4nKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgc3VibWl0Q3JlYXRlUHJpY2VGb3JtKCkge1xuXG4gICAgY29uc3QgdXJsID0gJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0nKS5hdHRyKCdkYXRhLWFjdGlvbicpO1xuICAgIGNvbnN0IGRhdGEgPSAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybSBpbnB1dCwgI3NwZWNpZmljX3ByaWNlX2Zvcm0gc2VsZWN0LCAjZm9ybV9pZF9wcm9kdWN0Jykuc2VyaWFsaXplKCk7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybSAuanMtc2F2ZScpLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdHlwZTogJ1BPU1QnLFxuICAgICAgdXJsOiB1cmwsXG4gICAgICBkYXRhOiBkYXRhLFxuICAgIH0pXG4gICAgICAgIC5kb25lKHJlc3BvbnNlID0+IHtcbiAgICAgICAgICBzaG93U3VjY2Vzc01lc3NhZ2UodHJhbnNsYXRlX2phdmFzY3JpcHRzWydGb3JtIHVwZGF0ZSBzdWNjZXNzJ10pO1xuICAgICAgICAgIHRoaXMucmVzZXRDcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzKCk7XG4gICAgICAgICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0nKS5jb2xsYXBzZSgnaGlkZScpO1xuICAgICAgICAgIHRoaXMubG9hZEFuZERpc3BsYXlFeGlzdGluZ1NwZWNpZmljUHJpY2VzTGlzdCgpO1xuXG4gICAgICAgICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0gLmpzLXNhdmUnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgIH0pXG4gICAgICAgIC5mYWlsKGVycm9ycyA9PiB7XG4gICAgICAgICAgc2hvd0Vycm9yTWVzc2FnZShlcnJvcnMucmVzcG9uc2VKU09OKTtcblxuICAgICAgICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtIC5qcy1zYXZlJykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcbiAgICAgICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHN1Ym1pdEVkaXRQcmljZUZvcm0oKSB7XG4gICAgY29uc3QgYmFzZVVybCA9ICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0nKS5hdHRyKCdkYXRhLWFjdGlvbicpO1xuICAgIGNvbnN0IHNwZWNpZmljUHJpY2VJZCA9ICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0nKS5kYXRhKCdzcGVjaWZpY1ByaWNlSWQnKTtcbiAgICBjb25zdCB1cmwgPSBiYXNlVXJsLnJlcGxhY2UoL3VwZGF0ZVxcL1xcZCsvLCAndXBkYXRlLycgKyBzcGVjaWZpY1ByaWNlSWQpO1xuXG4gICAgY29uc3QgZGF0YSA9ICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0gaW5wdXQsICNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0gc2VsZWN0LCAjZm9ybV9pZF9wcm9kdWN0Jykuc2VyaWFsaXplKCk7XG5cbiAgICAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtIC5qcy1zYXZlJykuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcblxuICAgICQuYWpheCh7XG4gICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICB1cmw6IHVybCxcbiAgICAgIGRhdGE6IGRhdGEsXG4gICAgfSlcbiAgICAgICAgLmRvbmUocmVzcG9uc2UgPT4ge1xuICAgICAgICAgIHNob3dTdWNjZXNzTWVzc2FnZSh0cmFuc2xhdGVfamF2YXNjcmlwdHNbJ0Zvcm0gdXBkYXRlIHN1Y2Nlc3MnXSk7XG4gICAgICAgICAgdGhpcy5jbG9zZUVkaXRQcmljZU1vZGFsQW5kUmVtb3ZlRm9ybSgpO1xuICAgICAgICAgIHRoaXMubG9hZEFuZERpc3BsYXlFeGlzdGluZ1NwZWNpZmljUHJpY2VzTGlzdCgpO1xuICAgICAgICAgICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0gLmpzLXNhdmUnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICAgICAgICB9KVxuICAgICAgICAuZmFpbChlcnJvcnMgPT4ge1xuICAgICAgICAgIHNob3dFcnJvck1lc3NhZ2UoZXJyb3JzLnJlc3BvbnNlSlNPTik7XG5cbiAgICAgICAgICAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtIC5qcy1zYXZlJykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcbiAgICAgICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIHN0cmluZyBjbGlja2VkTGluayBzZWxlY3RvclxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgZGVsZXRlU3BlY2lmaWNQcmljZShjbGlja2VkTGluaykge1xuICAgIG1vZGFsQ29uZmlybWF0aW9uLmNyZWF0ZSh0cmFuc2xhdGVfamF2YXNjcmlwdHNbJ1RoaXMgd2lsbCBkZWxldGUgdGhlIHNwZWNpZmljIHByaWNlLiBEbyB5b3Ugd2lzaCB0byBwcm9jZWVkPyddLCBudWxsLCB7XG4gICAgICBvbkNvbnRpbnVlOiAoKSA9PiB7XG5cbiAgICAgICAgdmFyIHVybCA9ICQoY2xpY2tlZExpbmspLmF0dHIoJ2hyZWYnKTtcbiAgICAgICAgJChjbGlja2VkTGluaykuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcblxuICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgIHR5cGU6ICdHRVQnLFxuICAgICAgICAgIHVybDogdXJsLFxuICAgICAgICB9KVxuICAgICAgICAgICAgLmRvbmUocmVzcG9uc2UgPT4ge1xuICAgICAgICAgICAgICB0aGlzLmxvYWRBbmREaXNwbGF5RXhpc3RpbmdTcGVjaWZpY1ByaWNlc0xpc3QoKTtcbiAgICAgICAgICAgICAgc2hvd1N1Y2Nlc3NNZXNzYWdlKHJlc3BvbnNlKTtcbiAgICAgICAgICAgICAgJChjbGlja2VkTGluaykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcbiAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAuZmFpbChlcnJvcnMgPT4ge1xuICAgICAgICAgICAgICBzaG93RXJyb3JNZXNzYWdlKGVycm9ycy5yZXNwb25zZUpTT04pO1xuICAgICAgICAgICAgICAkKGNsaWNrZWRMaW5rKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICB9KTtcbiAgICAgIH1cbiAgICB9KS5zaG93KCk7XG4gIH1cblxuICAvKipcbiAgICogU3RvcmUgJ2FkZCBzcGVjaWZpYyBwcmljZScgZm9ybSB2YWx1ZXNcbiAgICogZm9yIGZ1dHVyZSB1c2FnZVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgc3RvcmVQcmljZUZvcm1EZWZhdWx0VmFsdWVzKCkge1xuICAgIHZhciBzdG9yYWdlID0gdGhpcy4kY3JlYXRlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcztcblxuICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtJykuZmluZCgnc2VsZWN0LGlucHV0JykuZWFjaCgoaW5kZXgsIHZhbHVlKSA9PiB7XG4gICAgICBzdG9yYWdlWyQodmFsdWUpLmF0dHIoJ2lkJyldID0gJCh2YWx1ZSkudmFsKCk7XG4gICAgfSk7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmZpbmQoJ2lucHV0OmNoZWNrYm94JykuZWFjaCgoaW5kZXgsIHZhbHVlKSA9PiB7XG4gICAgICBzdG9yYWdlWyQodmFsdWUpLmF0dHIoJ2lkJyldID0gJCh2YWx1ZSkucHJvcCgnY2hlY2tlZCcpO1xuICAgIH0pO1xuXG4gICAgdGhpcy4kY3JlYXRlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcyA9IHN0b3JhZ2U7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIGJvb2xlYW4gdXNlUHJlZml4Rm9yQ3JlYXRlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBsb2FkQW5kRmlsbE9wdGlvbnNGb3JTZWxlY3RDb21iaW5hdGlvbklucHV0KHVzZVByZWZpeEZvckNyZWF0ZSkge1xuXG4gICAgdmFyIHNlbGVjdG9yUHJlZml4ID0gdGhpcy5nZXRQcmVmaXhTZWxlY3Rvcih1c2VQcmVmaXhGb3JDcmVhdGUpO1xuXG4gICAgdmFyIGlucHV0RmllbGQgPSAkKHNlbGVjdG9yUHJlZml4ICsgJ3NwX2lkX3Byb2R1Y3RfYXR0cmlidXRlJyk7XG4gICAgdmFyIHVybCA9IGlucHV0RmllbGQuYXR0cignZGF0YS1hY3Rpb24nKS5yZXBsYWNlKC9wcm9kdWN0LWNvbWJpbmF0aW9uc1xcL1xcZCsvLCAncHJvZHVjdC1jb21iaW5hdGlvbnMvJyArIHRoaXMuZ2V0UHJvZHVjdElkKCkpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdHRVQnLFxuICAgICAgdXJsOiB1cmwsXG4gICAgfSlcbiAgICAgICAgLmRvbmUoY29tYmluYXRpb25zID0+IHtcbiAgICAgICAgICAvKiogcmVtb3ZlIGFsbCBvcHRpb25zIGV4Y2VwdCBmaXJzdCBvbmUgKi9cbiAgICAgICAgICBpbnB1dEZpZWxkLmZpbmQoJ29wdGlvbjpndCgwKScpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgJC5lYWNoKGNvbWJpbmF0aW9ucywgKGluZGV4LCBjb21iaW5hdGlvbikgPT4ge1xuICAgICAgICAgICAgaW5wdXRGaWVsZC5hcHBlbmQoJzxvcHRpb24gdmFsdWU9XCInICsgY29tYmluYXRpb24uaWQgKyAnXCI+JyArIGNvbWJpbmF0aW9uLm5hbWUgKyAnPC9vcHRpb24+Jyk7XG4gICAgICAgICAgfSk7XG5cbiAgICAgICAgICBpZiAoaW5wdXRGaWVsZC5kYXRhKCdzZWxlY3RlZEF0dHJpYnV0ZScpICE9ICcwJykge1xuICAgICAgICAgICAgaW5wdXRGaWVsZC52YWwoaW5wdXRGaWVsZC5kYXRhKCdzZWxlY3RlZEF0dHJpYnV0ZScpKS50cmlnZ2VyKCdjaGFuZ2UnKTtcbiAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSBib29sZWFuIHVzZVByZWZpeEZvckNyZWF0ZVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgZW5hYmxlU3BlY2lmaWNQcmljZVRheEZpZWxkSWZFbGlnaWJsZSh1c2VQcmVmaXhGb3JDcmVhdGUpIHtcblxuICAgIHZhciBzZWxlY3RvclByZWZpeCA9IHRoaXMuZ2V0UHJlZml4U2VsZWN0b3IodXNlUHJlZml4Rm9yQ3JlYXRlKTtcblxuICAgIGlmICgkKHNlbGVjdG9yUHJlZml4ICsgJ3NwX3JlZHVjdGlvbl90eXBlJykudmFsKCkgPT09ICdwZXJjZW50YWdlJykge1xuICAgICAgJChzZWxlY3RvclByZWZpeCArICdzcF9yZWR1Y3Rpb25fdGF4JykuaGlkZSgpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ3NwX3JlZHVjdGlvbl90YXgnKS5zaG93KCk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIFJlc2V0ICdhZGQgc3BlY2lmaWMgcHJpY2UnIGZvcm0gdmFsdWVzXG4gICAqIHVzaW5nIHByZXZpb3VzbHkgc3RvcmVkIGRlZmF1bHQgdmFsdWVzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICByZXNldENyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMoKSB7XG4gICAgdmFyIHByZXZpb3VzbHlTdG9yZWRWYWx1ZXMgPSB0aGlzLiRjcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0nKS5maW5kKCdpbnB1dCcpLmVhY2goKGluZGV4LCB2YWx1ZSkgPT4ge1xuICAgICAgJCh2YWx1ZSkudmFsKHByZXZpb3VzbHlTdG9yZWRWYWx1ZXNbJCh2YWx1ZSkuYXR0cignaWQnKV0pO1xuICAgIH0pO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0nKS5maW5kKCdzZWxlY3QnKS5lYWNoKChpbmRleCwgdmFsdWUpID0+IHtcbiAgICAgICQodmFsdWUpLnZhbChwcmV2aW91c2x5U3RvcmVkVmFsdWVzWyQodmFsdWUpLmF0dHIoJ2lkJyldKS5jaGFuZ2UoKTtcbiAgICB9KTtcblxuICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtJykuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKS5lYWNoKChpbmRleCwgdmFsdWUpID0+IHtcbiAgICAgICQodmFsdWUpLnByb3AoXCJjaGVja2VkXCIsIHRydWUpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSBib29sZWFuIHVzZVByZWZpeEZvckNyZWF0ZVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgZW5hYmxlU3BlY2lmaWNQcmljZUZpZWxkSWZFbGlnaWJsZSh1c2VQcmVmaXhGb3JDcmVhdGUpIHtcbiAgICB2YXIgc2VsZWN0b3JQcmVmaXggPSB0aGlzLmdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSk7XG5cbiAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ3NwX3ByaWNlJykucHJvcCgnZGlzYWJsZWQnLCAkKHNlbGVjdG9yUHJlZml4ICsgJ2xlYXZlX2JwcmljZScpLmlzKCc6Y2hlY2tlZCcpKS52YWwoJycpO1xuICB9XG5cbiAgLyoqXG4gICAqIE9wZW4gJ2VkaXQgc3BlY2lmaWMgcHJpY2UnIGZvcm0gaW50byBhIG1vZGFsXG4gICAqXG4gICAqIEBwYXJhbSBpbnRlZ2VyIHNwZWNpZmljUHJpY2VJZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgb3BlbkVkaXRQcmljZU1vZGFsQW5kTG9hZEZvcm0oc3BlY2lmaWNQcmljZUlkKSB7XG4gICAgY29uc3QgdXJsID0gJCgnI2pzLXNwZWNpZmljLXByaWNlLWxpc3QnKS5kYXRhKCdhY3Rpb25FZGl0JykucmVwbGFjZSgvZm9ybVxcL1xcZCsvLCAnZm9ybS8nICsgc3BlY2lmaWNQcmljZUlkKTtcblxuICAgICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsJykubW9kYWwoXCJzaG93XCIpO1xuICAgIHRoaXMuZWRpdE1vZGFsSXNPcGVuID0gdHJ1ZTtcblxuICAgICQuYWpheCh7XG4gICAgICB0eXBlOiAnR0VUJyxcbiAgICAgIHVybDogdXJsLFxuICAgIH0pXG4gICAgICAgIC5kb25lKHJlc3BvbnNlID0+IHtcbiAgICAgICAgICB0aGlzLmluc2VydEVkaXRTcGVjaWZpY1ByaWNlRm9ybUludG9Nb2RhbChyZXNwb25zZSk7XG4gICAgICAgICAgJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybScpLmRhdGEoJ3NwZWNpZmljUHJpY2VJZCcsIHNwZWNpZmljUHJpY2VJZCk7XG4gICAgICAgICAgdGhpcy5jb25maWd1cmVFZGl0UHJpY2VGb3JtSW5zaWRlTW9kYWxCZWhhdmlvcigpO1xuICAgICAgICB9KVxuICAgICAgICAuZmFpbChlcnJvcnMgPT4ge1xuICAgICAgICAgIHNob3dFcnJvck1lc3NhZ2UoZXJyb3JzLnJlc3BvbnNlSlNPTik7XG4gICAgICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBjbG9zZUVkaXRQcmljZU1vZGFsQW5kUmVtb3ZlRm9ybSgpIHtcbiAgICAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbCcpLm1vZGFsKFwiaGlkZVwiKTtcbiAgICB0aGlzLmVkaXRNb2RhbElzT3BlbiA9IGZhbHNlO1xuXG4gICAgdmFyIGZvcm1Mb2NhdGlvbkhvbGRlciA9ICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0nKTtcblxuICAgIGZvcm1Mb2NhdGlvbkhvbGRlci5lbXB0eSgpO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSBzdHJpbmcgZm9ybTogSFRNTCAnZWRpdCBzcGVjaWZpYyBwcmljZScgZm9ybVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgaW5zZXJ0RWRpdFNwZWNpZmljUHJpY2VGb3JtSW50b01vZGFsKGZvcm0pIHtcbiAgICB2YXIgZm9ybUxvY2F0aW9uSG9sZGVyID0gJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybScpO1xuXG4gICAgZm9ybUxvY2F0aW9uSG9sZGVyLmVtcHR5KCk7XG4gICAgZm9ybUxvY2F0aW9uSG9sZGVyLmFwcGVuZChmb3JtKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgcHJvZHVjdCBJRCBmb3IgY3VycmVudCBDYXRhbG9nIFByb2R1Y3QgcGFnZVxuICAgKlxuICAgKiBAcmV0dXJucyBpbnRlZ2VyXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBnZXRQcm9kdWN0SWQoKSB7XG4gICAgcmV0dXJuICQoJyNmb3JtX2lkX3Byb2R1Y3QnKS52YWwoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0gYm9vbGVhbiB1c2VQcmVmaXhGb3JDcmVhdGVcbiAgICpcbiAgICogQHJldHVybnMgc3RyaW5nXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBnZXRQcmVmaXhTZWxlY3Rvcih1c2VQcmVmaXhGb3JDcmVhdGUpIHtcbiAgICBpZiAodXNlUHJlZml4Rm9yQ3JlYXRlID09IHRydWUpIHtcbiAgICAgIHJldHVybiAnIycgKyB0aGlzLnByZWZpeENyZWF0ZUZvcm07XG4gICAgfSBlbHNlIHtcbiAgICAgIHJldHVybiAnIycgKyB0aGlzLnByZWZpeEVkaXRGb3JtO1xuICAgIH1cbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBTcGVjaWZpY1ByaWNlRm9ybUhhbmRsZXI7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9jYXRhbG9nL3Byb2R1Y3Qvc3BlY2lmaWMtcHJpY2UtZm9ybS1oYW5kbGVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IFNwZWNpZmljUHJpY2VGb3JtSGFuZGxlciBmcm9tICcuL3NwZWNpZmljLXByaWNlLWZvcm0taGFuZGxlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gIG5ldyBTcGVjaWZpY1ByaWNlRm9ybUhhbmRsZXIoKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvY2F0YWxvZy9wcm9kdWN0L2luZGV4LmpzIl0sInNvdXJjZVJvb3QiOiIifQ==