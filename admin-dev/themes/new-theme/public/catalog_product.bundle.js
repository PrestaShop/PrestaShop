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
/******/ 	return __webpack_require__(__webpack_require__.s = 338);
/******/ })
/************************************************************************/
/******/ ({

/***/ 262:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

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

/***/ 338:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _specificPriceFormHandler = __webpack_require__(262);

var _specificPriceFormHandler2 = _interopRequireDefault(_specificPriceFormHandler);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
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

$(function () {
  new _specificPriceFormHandler2.default();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjA/ODU5MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9jYXRhbG9nL3Byb2R1Y3Qvc3BlY2lmaWMtcHJpY2UtZm9ybS1oYW5kbGVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2NhdGFsb2cvcHJvZHVjdC9pbmRleC5qcyJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwiU3BlY2lmaWNQcmljZUZvcm1IYW5kbGVyIiwicHJlZml4Q3JlYXRlRm9ybSIsInByZWZpeEVkaXRGb3JtIiwiZWRpdE1vZGFsSXNPcGVuIiwiJGNyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMiLCJPYmplY3QiLCJzdG9yZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMiLCJsb2FkQW5kRGlzcGxheUV4aXN0aW5nU3BlY2lmaWNQcmljZXNMaXN0IiwiY29uZmlndXJlQWRkUHJpY2VGb3JtQmVoYXZpb3IiLCJjb25maWd1cmVFZGl0UHJpY2VNb2RhbEJlaGF2aW9yIiwiY29uZmlndXJlRGVsZXRlUHJpY2VCdXR0b25zQmVoYXZpb3IiLCJjb25maWd1cmVNdWx0aXBsZU1vZGFsc0JlaGF2aW9yIiwibGlzdENvbnRhaW5lciIsInVybCIsImRhdGEiLCJyZXBsYWNlIiwiZ2V0UHJvZHVjdElkIiwiYWpheCIsInR5cGUiLCJkb25lIiwidGJvZHkiLCJmaW5kIiwicmVtb3ZlIiwic3BlY2lmaWNQcmljZXMiLCJsZW5ndGgiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwic3BlY2lmaWNQcmljZXNMaXN0IiwicmVuZGVyU3BlY2lmaWNQcmljZXNMaXN0aW5nQXNIdG1sIiwiYXBwZW5kIiwic2VsZiIsImVhY2giLCJpbmRleCIsInNwZWNpZmljUHJpY2UiLCJkZWxldGVVcmwiLCJhdHRyIiwiaWRfc3BlY2lmaWNfcHJpY2UiLCJyb3ciLCJyZW5kZXJTcGVjaWZpY1ByaWNlUm93Iiwic3BlY2lmaWNQcmljZUlkIiwicnVsZV9uYW1lIiwiYXR0cmlidXRlc19uYW1lIiwiY3VycmVuY3kiLCJjb3VudHJ5IiwiZ3JvdXAiLCJjdXN0b21lciIsImZpeGVkX3ByaWNlIiwiaW1wYWN0IiwicGVyaW9kIiwiZnJvbV9xdWFudGl0eSIsImNhbl9kZWxldGUiLCJjYW5fZWRpdCIsInVzZVByZWZpeEZvckNyZWF0ZSIsInNlbGVjdG9yUHJlZml4IiwiZ2V0UHJlZml4U2VsZWN0b3IiLCJjbGljayIsInJlc2V0Q3JlYXRlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcyIsImNvbGxhcHNlIiwib24iLCJzdWJtaXRDcmVhdGVQcmljZUZvcm0iLCJsb2FkQW5kRmlsbE9wdGlvbnNGb3JTZWxlY3RDb21iaW5hdGlvbklucHV0IiwiZW5hYmxlU3BlY2lmaWNQcmljZUZpZWxkSWZFbGlnaWJsZSIsImVuYWJsZVNwZWNpZmljUHJpY2VUYXhGaWVsZElmRWxpZ2libGUiLCJjbG9zZUVkaXRQcmljZU1vZGFsQW5kUmVtb3ZlRm9ybSIsInN1Ym1pdEVkaXRQcmljZUZvcm0iLCJyZWluaXRpYWxpemVEYXRlUGlja2VycyIsImluaXRpYWxpemVMZWF2ZUJQcmljZUZpZWxkIiwiZGF0ZXRpbWVwaWNrZXIiLCJmb3JtYXQiLCJ2YWwiLCJwcm9wIiwiZG9jdW1lbnQiLCJldmVudCIsInByZXZlbnREZWZhdWx0IiwiY3VycmVudFRhcmdldCIsIm9wZW5FZGl0UHJpY2VNb2RhbEFuZExvYWRGb3JtIiwiZGVsZXRlU3BlY2lmaWNQcmljZSIsInNlcmlhbGl6ZSIsInNob3dTdWNjZXNzTWVzc2FnZSIsInRyYW5zbGF0ZV9qYXZhc2NyaXB0cyIsInJlbW92ZUF0dHIiLCJmYWlsIiwic2hvd0Vycm9yTWVzc2FnZSIsImVycm9ycyIsInJlc3BvbnNlSlNPTiIsImJhc2VVcmwiLCJjbGlja2VkTGluayIsIm1vZGFsQ29uZmlybWF0aW9uIiwiY3JlYXRlIiwib25Db250aW51ZSIsInJlc3BvbnNlIiwic2hvdyIsInN0b3JhZ2UiLCJ2YWx1ZSIsImlucHV0RmllbGQiLCJjb21iaW5hdGlvbnMiLCJjb21iaW5hdGlvbiIsImlkIiwibmFtZSIsInRyaWdnZXIiLCJoaWRlIiwicHJldmlvdXNseVN0b3JlZFZhbHVlcyIsImNoYW5nZSIsImlzIiwibW9kYWwiLCJpbnNlcnRFZGl0U3BlY2lmaWNQcmljZUZvcm1JbnRvTW9kYWwiLCJjb25maWd1cmVFZGl0UHJpY2VGb3JtSW5zaWRlTW9kYWxCZWhhdmlvciIsImZvcm1Mb2NhdGlvbkhvbGRlciIsImVtcHR5IiwiZm9ybSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQ2hFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQSxJQUFNQSxJQUFJQyxPQUFPRCxDQUFqQjs7SUFFTUUsd0I7QUFFSixzQ0FBYztBQUFBOztBQUNaLFNBQUtDLGdCQUFMLEdBQXdCLDRCQUF4QjtBQUNBLFNBQUtDLGNBQUwsR0FBc0IsYUFBdEI7QUFDQSxTQUFLQyxlQUFMLEdBQXVCLEtBQXZCOztBQUVBLFNBQUtDLDZCQUFMLEdBQXFDLElBQUlDLE1BQUosRUFBckM7QUFDQSxTQUFLQywyQkFBTDs7QUFFQSxTQUFLQyx3Q0FBTDs7QUFFQSxTQUFL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RDtBQUNGOzs7Ozs7a0JBR1lGLHdCOzs7Ozs7Ozs7O0FDdmRmOzs7Ozs7QUFFQSxJQUFNRixJQUFJQyxPQUFPRCxDQUFqQixDLENBM0JBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBNkJBQSxFQUFFLFlBQU07QUFDTixNQUFJRSxrQ0FBSjtBQUNELENBRkQsRSIsImZpbGUiOiJjYXRhbG9nX3Byb2R1Y3QuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAzMzgpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDFlNjYyNjM5MDBlOTY2ZGZiYmYwIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG5jbGFzcyBTcGVjaWZpY1ByaWNlRm9ybUhhbmRsZXIge1xuXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMucHJlZml4Q3JlYXRlRm9ybSA9ICdmb3JtX3N0ZXAyX3NwZWNpZmljX3ByaWNlXyc7XG4gICAgdGhpcy5wcmVmaXhFZGl0Rm9ybSA9ICdmb3JtX21vZGFsXyc7XG4gICAgdGhpcy5lZGl0TW9kYWxJc09wZW4gPSBmYWxzZTtcblxuICAgIHRoaXMuJGNyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMgPSBuZXcgT2JqZWN0KCk7XG4gICAgdGhpcy5zdG9yZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMoKTtcblxuICAgIHRoaXMubG9hZEFuZERpc3BsYXlFeGlzdGluZ1NwZWNpZmljUHJpY2VzTGlzdCgpO1xuXG4gICAgdGhpcy5jb25maWd1cmVBZGRQcmljZUZvcm1CZWhhdmlvcigpO1xuXG4gICAgdGhpcy5jb25maWd1cmVFZGl0UHJpY2VNb2RhbEJlaGF2aW9yKCk7XG5cbiAgICB0aGlzLmNvbmZpZ3VyZURlbGV0ZVByaWNlQnV0dG9uc0JlaGF2aW9yKCk7XG5cbiAgICB0aGlzLmNvbmZpZ3VyZU11bHRpcGxlTW9kYWxzQmVoYXZpb3IoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgbG9hZEFuZERpc3BsYXlFeGlzdGluZ1NwZWNpZmljUHJpY2VzTGlzdCgpIHtcbiAgICB2YXIgbGlzdENvbnRhaW5lciA9ICQoJyNqcy1zcGVjaWZpYy1wcmljZS1saXN0Jyk7XG4gICAgdmFyIHVybCA9IGxpc3RDb250YWluZXIuZGF0YSgnbGlzdGluZ1VybCcpLnJlcGxhY2UoL2xpc3RcXC9cXGQrLywgJ2xpc3QvJyArIHRoaXMuZ2V0UHJvZHVjdElkKCkpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdHRVQnLFxuICAgICAgdXJsOiB1cmwsXG4gICAgfSlcbiAgICAgICAgLmRvbmUoc3BlY2lmaWNQcmljZXMgPT4ge1xuICAgICAgICAgIHZhciB0Ym9keSA9IGxpc3RDb250YWluZXIuZmluZCgndGJvZHknKTtcbiAgICAgICAgICB0Ym9keS5maW5kKCd0cicpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgaWYgKHNwZWNpZmljUHJpY2VzLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgIGxpc3RDb250YWluZXIucmVtb3ZlQ2xhc3MoJ2hpZGUnKTtcbiAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgbGlzdENvbnRhaW5lci5hZGRDbGFzcygnaGlkZScpO1xuICAgICAgICAgIH1cblxuICAgICAgICAgIHZhciBzcGVjaWZpY1ByaWNlc0xpc3QgPSB0aGlzLnJlbmRlclNwZWNpZmljUHJpY2VzTGlzdGluZ0FzSHRtbChzcGVjaWZpY1ByaWNlcyk7XG5cbiAgICAgICAgICB0Ym9keS5hcHBlbmQoc3BlY2lmaWNQcmljZXNMaXN0KTtcbiAgICAgICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIGFycmF5IHNwZWNpZmljUHJpY2VzXG4gICAqXG4gICAqIEByZXR1cm5zIHN0cmluZ1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgcmVuZGVyU3BlY2lmaWNQcmljZXNMaXN0aW5nQXNIdG1sKHNwZWNpZmljUHJpY2VzKSB7XG4gICAgdmFyIHNwZWNpZmljUHJpY2VzTGlzdCA9ICcnO1xuXG4gICAgdmFyIHNlbGYgPSB0aGlzO1xuXG4gICAgJC5lYWNoKHNwZWNpZmljUHJpY2VzLCAoaW5kZXgsIHNwZWNpZmljUHJpY2UpID0+IHtcbiAgICAgIHZhciBkZWxldGVVcmwgPSAkKCcjanMtc3BlY2lmaWMtcHJpY2UtbGlzdCcpLmF0dHIoJ2RhdGEtYWN0aW9uLWRlbGV0ZScpLnJlcGxhY2UoL2RlbGV0ZVxcL1xcZCsvLCAnZGVsZXRlLycgKyBzcGVjaWZpY1ByaWNlLmlkX3NwZWNpZmljX3ByaWNlKTtcbiAgICAgIHZhciByb3cgPSBzZWxmLnJlbmRlclNwZWNpZmljUHJpY2VSb3coc3BlY2lmaWNQcmljZSwgZGVsZXRlVXJsKTtcblxuICAgICAgc3BlY2lmaWNQcmljZXNMaXN0ID0gc3BlY2lmaWNQcmljZXNMaXN0ICsgcm93O1xuICAgIH0pO1xuXG4gICAgcmV0dXJuIHNwZWNpZmljUHJpY2VzTGlzdDtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0gT2JqZWN0IHNwZWNpZmljUHJpY2VcbiAgICogQHBhcmFtIHN0cmluZyBkZWxldGVVcmxcbiAgICpcbiAgICogQHJldHVybnMgc3RyaW5nXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICByZW5kZXJTcGVjaWZpY1ByaWNlUm93KHNwZWNpZmljUHJpY2UsIGRlbGV0ZVVybCkge1xuXG4gICAgdmFyIHNwZWNpZmljUHJpY2VJZCA9IHNwZWNpZmljUHJpY2UuaWRfc3BlY2lmaWNfcHJpY2U7XG5cbiAgICB2YXIgcm93ID0gJzx0cj4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5ydWxlX25hbWUgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5hdHRyaWJ1dGVzX25hbWUgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5jdXJyZW5jeSArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyBzcGVjaWZpY1ByaWNlLmNvdW50cnkgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5ncm91cCArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyBzcGVjaWZpY1ByaWNlLmN1c3RvbWVyICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuZml4ZWRfcHJpY2UgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5pbXBhY3QgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5wZXJpb2QgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5mcm9tX3F1YW50aXR5ICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIChzcGVjaWZpY1ByaWNlLmNhbl9kZWxldGUgPyAnPGEgaHJlZj1cIicgKyBkZWxldGVVcmwgKyAnXCIgY2xhc3M9XCJqcy1kZWxldGUgZGVsZXRlIGJ0biB0b29sdGlwLWxpbmsgZGVsZXRlIHBsLTAgcHItMFwiPjxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5kZWxldGU8L2k+PC9hPicgOiAnJykgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgKHNwZWNpZmljUHJpY2UuY2FuX2VkaXQgPyAnPGEgaHJlZj1cIiNcIiBkYXRhLXNwZWNpZmljLXByaWNlLWlkPVwiJyArIHNwZWNpZmljUHJpY2VJZCArICdcIiBjbGFzcz1cImpzLWVkaXQgZWRpdCBidG4gdG9vbHRpcC1saW5rIGRlbGV0ZSBwbC0wIHByLTBcIj48aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+ZWRpdDwvaT48L2E+JyA6ICcnKSArICc8L3RkPicgK1xuICAgICAgICAnPC90cj4nO1xuXG4gICAgcmV0dXJuIHJvdztcbiAgfVxuXG4gIC8qKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgY29uZmlndXJlQWRkUHJpY2VGb3JtQmVoYXZpb3IoKSB7XG4gICAgY29uc3QgdXNlUHJlZml4Rm9yQ3JlYXRlID0gdHJ1ZTtcbiAgICB2YXIgc2VsZWN0b3JQcmVmaXggPSB0aGlzLmdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSk7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybSAuanMtY2FuY2VsJykuY2xpY2soKCkgPT4ge1xuICAgICAgdGhpcy5yZXNldENyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMoKTtcbiAgICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtJykuY29sbGFwc2UoJ2hpZGUnKTtcbiAgICB9KTtcblxuICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtIC5qcy1zYXZlJykub24oJ2NsaWNrJywgKCkgPT4gdGhpcy5zdWJtaXRDcmVhdGVQcmljZUZvcm0oKSk7XG5cbiAgICAkKCcjanMtb3Blbi1jcmVhdGUtc3BlY2lmaWMtcHJpY2UtZm9ybScpLm9uKCdjbGljaycsICgpID0+IHRoaXMubG9hZEFuZEZpbGxPcHRpb25zRm9yU2VsZWN0Q29tYmluYXRpb25JbnB1dCh1c2VQcmVmaXhGb3JDcmVhdGUpKTtcblxuICAgICQoc2VsZWN0b3JQcmVmaXggKyAnbGVhdmVfYnByaWNlJykub24oJ2NsaWNrJywgKCkgPT4gdGhpcy5lbmFibGVTcGVjaWZpY1ByaWNlRmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkpO1xuXG4gICAgJChzZWxlY3RvclByZWZpeCArICdzcF9yZWR1Y3Rpb25fdHlwZScpLm9uKCdjaGFuZ2UnLCAoKSA9PiB0aGlzLmVuYWJsZVNwZWNpZmljUHJpY2VUYXhGaWVsZElmRWxpZ2libGUodXNlUHJlZml4Rm9yQ3JlYXRlKSk7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGNvbmZpZ3VyZUVkaXRQcmljZUZvcm1JbnNpZGVNb2RhbEJlaGF2aW9yKCkge1xuICAgIGNvbnN0IHVzZVByZWZpeEZvckNyZWF0ZSA9IGZhbHNlO1xuICAgIHZhciBzZWxlY3RvclByZWZpeCA9IHRoaXMuZ2V0UHJlZml4U2VsZWN0b3IodXNlUHJlZml4Rm9yQ3JlYXRlKTtcblxuICAgICQoJyNmb3JtX21vZGFsX2NhbmNlbCcpLmNsaWNrKCgpID0+IHRoaXMuY2xvc2VFZGl0UHJpY2VNb2RhbEFuZFJlbW92ZUZvcm0oKSk7XG4gICAgJCgnI2Zvcm1fbW9kYWxfY2xvc2UnKS5jbGljaygoKSA9PiB0aGlzLmNsb3NlRWRpdFByaWNlTW9kYWxBbmRSZW1vdmVGb3JtKCkpO1xuXG4gICAgJCgnI2Zvcm1fbW9kYWxfc2F2ZScpLmNsaWNrKCgpID0+IHRoaXMuc3VibWl0RWRpdFByaWNlRm9ybSgpKTtcblxuICAgIHRoaXMubG9hZEFuZEZpbGxPcHRpb25zRm9yU2VsZWN0Q29tYmluYXRpb25JbnB1dCh1c2VQcmVmaXhGb3JDcmVhdGUpO1xuXG4gICAgJChzZWxlY3RvclByZWZpeCArICdsZWF2ZV9icHJpY2UnKS5vbignY2xpY2snLCAoKSA9PiB0aGlzLmVuYWJsZVNwZWNpZmljUHJpY2VGaWVsZElmRWxpZ2libGUodXNlUHJlZml4Rm9yQ3JlYXRlKSk7XG5cbiAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ3NwX3JlZHVjdGlvbl90eXBlJykub24oJ2NoYW5nZScsICgpID0+IHRoaXMuZW5hYmxlU3BlY2lmaWNQcmljZVRheEZpZWxkSWZFbGlnaWJsZSh1c2VQcmVmaXhGb3JDcmVhdGUpKTtcblxuICAgIHRoaXMucmVpbml0aWFsaXplRGF0ZVBpY2tlcnMoKTtcblxuICAgIHRoaXMuaW5pdGlhbGl6ZUxlYXZlQlByaWNlRmllbGQodXNlUHJlZml4Rm9yQ3JlYXRlKTtcbiAgICB0aGlzLmVuYWJsZVNwZWNpZmljUHJpY2VUYXhGaWVsZElmRWxpZ2libGUodXNlUHJlZml4Rm9yQ3JlYXRlKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgcmVpbml0aWFsaXplRGF0ZVBpY2tlcnMoKSB7XG4gICAgJCgnLmRhdGVwaWNrZXIgaW5wdXQnKS5kYXRldGltZXBpY2tlcih7Zm9ybWF0OiAnWVlZWS1NTS1ERCd9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0gYm9vbGVhbiB1c2VQcmVmaXhGb3JDcmVhdGVcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGluaXRpYWxpemVMZWF2ZUJQcmljZUZpZWxkKHVzZVByZWZpeEZvckNyZWF0ZSkge1xuICAgIHZhciBzZWxlY3RvclByZWZpeCA9IHRoaXMuZ2V0UHJlZml4U2VsZWN0b3IodXNlUHJlZml4Rm9yQ3JlYXRlKTtcblxuICAgIGlmICgkKHNlbGVjdG9yUHJlZml4ICsgJ3NwX3ByaWNlJykudmFsKCkgIT0gJycpIHtcbiAgICAgICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcHJpY2UnKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAgICQoc2VsZWN0b3JQcmVmaXggKyAnbGVhdmVfYnByaWNlJykucHJvcCgnY2hlY2tlZCcsIGZhbHNlKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGNvbmZpZ3VyZUVkaXRQcmljZU1vZGFsQmVoYXZpb3IoKSB7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJyNqcy1zcGVjaWZpYy1wcmljZS1saXN0IC5qcy1lZGl0JywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICB2YXIgc3BlY2lmaWNQcmljZUlkID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdzcGVjaWZpY1ByaWNlSWQnKTtcblxuICAgICAgdGhpcy5vcGVuRWRpdFByaWNlTW9kYWxBbmRMb2FkRm9ybShzcGVjaWZpY1ByaWNlSWQpO1xuICAgIH0pO1xuXG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGNvbmZpZ3VyZURlbGV0ZVByaWNlQnV0dG9uc0JlaGF2aW9yKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcjanMtc3BlY2lmaWMtcHJpY2UtbGlzdCAuanMtZGVsZXRlJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgdGhpcy5kZWxldGVTcGVjaWZpY1ByaWNlKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBzZWUgaHR0cHM6Ly92aWpheWFzYW5rYXJuLndvcmRwcmVzcy5jb20vMjAxNy8wMi8yNC9xdWljay1maXgtc2Nyb2xsaW5nLWFuZC1mb2N1cy13aGVuLW11bHRpcGxlLWJvb3RzdHJhcC1tb2RhbHMtYXJlLW9wZW4vXG4gICAqL1xuICBjb25maWd1cmVNdWx0aXBsZU1vZGFsc0JlaGF2aW9yKCkge1xuICAgICQoJy5tb2RhbCcpLm9uKCdoaWRkZW4uYnMubW9kYWwnLCAoKSA9PiB7XG4gICAgICBpZiAodGhpcy5lZGl0TW9kYWxJc09wZW4pIHtcbiAgICAgICAgJCgnYm9keScpLmFkZENsYXNzKCdtb2RhbC1vcGVuJyk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHN1Ym1pdENyZWF0ZVByaWNlRm9ybSgpIHtcblxuICAgIGNvbnN0IHVybCA9ICQoJyNzcGVjaWZpY19wcmljZV9mb3JtJykuYXR0cignZGF0YS1hY3Rpb24nKTtcbiAgICBjb25zdCBkYXRhID0gJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0gaW5wdXQsICNzcGVjaWZpY19wcmljZV9mb3JtIHNlbGVjdCwgI2Zvcm1faWRfcHJvZHVjdCcpLnNlcmlhbGl6ZSgpO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0gLmpzLXNhdmUnKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgIHVybDogdXJsLFxuICAgICAgZGF0YTogZGF0YSxcbiAgICB9KVxuICAgICAgICAuZG9uZShyZXNwb25zZSA9PiB7XG4gICAgICAgICAgc2hvd1N1Y2Nlc3NNZXNzYWdlKHRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snRm9ybSB1cGRhdGUgc3VjY2VzcyddKTtcbiAgICAgICAgICB0aGlzLnJlc2V0Q3JlYXRlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcygpO1xuICAgICAgICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtJykuY29sbGFwc2UoJ2hpZGUnKTtcbiAgICAgICAgICB0aGlzLmxvYWRBbmREaXNwbGF5RXhpc3RpbmdTcGVjaWZpY1ByaWNlc0xpc3QoKTtcblxuICAgICAgICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtIC5qcy1zYXZlJykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcblxuICAgICAgICB9KVxuICAgICAgICAuZmFpbChlcnJvcnMgPT4ge1xuICAgICAgICAgIHNob3dFcnJvck1lc3NhZ2UoZXJyb3JzLnJlc3BvbnNlSlNPTik7XG5cbiAgICAgICAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybSAuanMtc2F2ZScpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBzdWJtaXRFZGl0UHJpY2VGb3JtKCkge1xuICAgIGNvbnN0IGJhc2VVcmwgPSAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtJykuYXR0cignZGF0YS1hY3Rpb24nKTtcbiAgICBjb25zdCBzcGVjaWZpY1ByaWNlSWQgPSAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtJykuZGF0YSgnc3BlY2lmaWNQcmljZUlkJyk7XG4gICAgY29uc3QgdXJsID0gYmFzZVVybC5yZXBsYWNlKC91cGRhdGVcXC9cXGQrLywgJ3VwZGF0ZS8nICsgc3BlY2lmaWNQcmljZUlkKTtcblxuICAgIGNvbnN0IGRhdGEgPSAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtIGlucHV0LCAjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtIHNlbGVjdCwgI2Zvcm1faWRfcHJvZHVjdCcpLnNlcmlhbGl6ZSgpO1xuXG4gICAgJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybSAuanMtc2F2ZScpLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdHlwZTogJ1BPU1QnLFxuICAgICAgdXJsOiB1cmwsXG4gICAgICBkYXRhOiBkYXRhLFxuICAgIH0pXG4gICAgICAgIC5kb25lKHJlc3BvbnNlID0+IHtcbiAgICAgICAgICBzaG93U3VjY2Vzc01lc3NhZ2UodHJhbnNsYXRlX2phdmFzY3JpcHRzWydGb3JtIHVwZGF0ZSBzdWNjZXNzJ10pO1xuICAgICAgICAgIHRoaXMuY2xvc2VFZGl0UHJpY2VNb2RhbEFuZFJlbW92ZUZvcm0oKTtcbiAgICAgICAgICB0aGlzLmxvYWRBbmREaXNwbGF5RXhpc3RpbmdTcGVjaWZpY1ByaWNlc0xpc3QoKTtcbiAgICAgICAgICAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtIC5qcy1zYXZlJykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcbiAgICAgICAgfSlcbiAgICAgICAgLmZhaWwoZXJyb3JzID0+IHtcbiAgICAgICAgICBzaG93RXJyb3JNZXNzYWdlKGVycm9ycy5yZXNwb25zZUpTT04pO1xuXG4gICAgICAgICAgJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybSAuanMtc2F2ZScpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSBzdHJpbmcgY2xpY2tlZExpbmsgc2VsZWN0b3JcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGRlbGV0ZVNwZWNpZmljUHJpY2UoY2xpY2tlZExpbmspIHtcbiAgICBtb2RhbENvbmZpcm1hdGlvbi5jcmVhdGUodHJhbnNsYXRlX2phdmFzY3JpcHRzWydUaGlzIHdpbGwgZGVsZXRlIHRoZSBzcGVjaWZpYyBwcmljZS4gRG8geW91IHdpc2ggdG8gcHJvY2VlZD8nXSwgbnVsbCwge1xuICAgICAgb25Db250aW51ZTogKCkgPT4ge1xuXG4gICAgICAgIHZhciB1cmwgPSAkKGNsaWNrZWRMaW5rKS5hdHRyKCdocmVmJyk7XG4gICAgICAgICQoY2xpY2tlZExpbmspLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICB0eXBlOiAnR0VUJyxcbiAgICAgICAgICB1cmw6IHVybCxcbiAgICAgICAgfSlcbiAgICAgICAgICAgIC5kb25lKHJlc3BvbnNlID0+IHtcbiAgICAgICAgICAgICAgdGhpcy5sb2FkQW5kRGlzcGxheUV4aXN0aW5nU3BlY2lmaWNQcmljZXNMaXN0KCk7XG4gICAgICAgICAgICAgIHNob3dTdWNjZXNzTWVzc2FnZShyZXNwb25zZSk7XG4gICAgICAgICAgICAgICQoY2xpY2tlZExpbmspLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICB9KVxuICAgICAgICAgICAgLmZhaWwoZXJyb3JzID0+IHtcbiAgICAgICAgICAgICAgc2hvd0Vycm9yTWVzc2FnZShlcnJvcnMucmVzcG9uc2VKU09OKTtcbiAgICAgICAgICAgICAgJChjbGlja2VkTGluaykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgfSk7XG4gICAgICB9XG4gICAgfSkuc2hvdygpO1xuICB9XG5cbiAgLyoqXG4gICAqIFN0b3JlICdhZGQgc3BlY2lmaWMgcHJpY2UnIGZvcm0gdmFsdWVzXG4gICAqIGZvciBmdXR1cmUgdXNhZ2VcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHN0b3JlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcygpIHtcbiAgICB2YXIgc3RvcmFnZSA9IHRoaXMuJGNyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXM7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmZpbmQoJ3NlbGVjdCxpbnB1dCcpLmVhY2goKGluZGV4LCB2YWx1ZSkgPT4ge1xuICAgICAgc3RvcmFnZVskKHZhbHVlKS5hdHRyKCdpZCcpXSA9ICQodmFsdWUpLnZhbCgpO1xuICAgIH0pO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0nKS5maW5kKCdpbnB1dDpjaGVja2JveCcpLmVhY2goKGluZGV4LCB2YWx1ZSkgPT4ge1xuICAgICAgc3RvcmFnZVskKHZhbHVlKS5hdHRyKCdpZCcpXSA9ICQodmFsdWUpLnByb3AoJ2NoZWNrZWQnKTtcbiAgICB9KTtcblxuICAgIHRoaXMuJGNyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMgPSBzdG9yYWdlO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSBib29sZWFuIHVzZVByZWZpeEZvckNyZWF0ZVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgbG9hZEFuZEZpbGxPcHRpb25zRm9yU2VsZWN0Q29tYmluYXRpb25JbnB1dCh1c2VQcmVmaXhGb3JDcmVhdGUpIHtcblxuICAgIHZhciBzZWxlY3RvclByZWZpeCA9IHRoaXMuZ2V0UHJlZml4U2VsZWN0b3IodXNlUHJlZml4Rm9yQ3JlYXRlKTtcblxuICAgIHZhciBpbnB1dEZpZWxkID0gJChzZWxlY3RvclByZWZpeCArICdzcF9pZF9wcm9kdWN0X2F0dHJpYnV0ZScpO1xuICAgIHZhciB1cmwgPSBpbnB1dEZpZWxkLmF0dHIoJ2RhdGEtYWN0aW9uJykucmVwbGFjZSgvcHJvZHVjdC1jb21iaW5hdGlvbnNcXC9cXGQrLywgJ3Byb2R1Y3QtY29tYmluYXRpb25zLycgKyB0aGlzLmdldFByb2R1Y3RJZCgpKTtcblxuICAgICQuYWpheCh7XG4gICAgICB0eXBlOiAnR0VUJyxcbiAgICAgIHVybDogdXJsLFxuICAgIH0pXG4gICAgICAgIC5kb25lKGNvbWJpbmF0aW9ucyA9PiB7XG4gICAgICAgICAgLyoqIHJlbW92ZSBhbGwgb3B0aW9ucyBleGNlcHQgZmlyc3Qgb25lICovXG4gICAgICAgICAgaW5wdXRGaWVsZC5maW5kKCdvcHRpb246Z3QoMCknKS5yZW1vdmUoKTtcblxuICAgICAgICAgICQuZWFjaChjb21iaW5hdGlvbnMsIChpbmRleCwgY29tYmluYXRpb24pID0+IHtcbiAgICAgICAgICAgIGlucHV0RmllbGQuYXBwZW5kKCc8b3B0aW9uIHZhbHVlPVwiJyArIGNvbWJpbmF0aW9uLmlkICsgJ1wiPicgKyBjb21iaW5hdGlvbi5uYW1lICsgJzwvb3B0aW9uPicpO1xuICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgaWYgKGlucHV0RmllbGQuZGF0YSgnc2VsZWN0ZWRBdHRyaWJ1dGUnKSAhPSAnMCcpIHtcbiAgICAgICAgICAgIGlucHV0RmllbGQudmFsKGlucHV0RmllbGQuZGF0YSgnc2VsZWN0ZWRBdHRyaWJ1dGUnKSkudHJpZ2dlcignY2hhbmdlJyk7XG4gICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0gYm9vbGVhbiB1c2VQcmVmaXhGb3JDcmVhdGVcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGVuYWJsZVNwZWNpZmljUHJpY2VUYXhGaWVsZElmRWxpZ2libGUodXNlUHJlZml4Rm9yQ3JlYXRlKSB7XG5cbiAgICB2YXIgc2VsZWN0b3JQcmVmaXggPSB0aGlzLmdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSk7XG5cbiAgICBpZiAoJChzZWxlY3RvclByZWZpeCArICdzcF9yZWR1Y3Rpb25fdHlwZScpLnZhbCgpID09PSAncGVyY2VudGFnZScpIHtcbiAgICAgICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcmVkdWN0aW9uX3RheCcpLmhpZGUoKTtcbiAgICB9IGVsc2Uge1xuICAgICAgJChzZWxlY3RvclByZWZpeCArICdzcF9yZWR1Y3Rpb25fdGF4Jykuc2hvdygpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBSZXNldCAnYWRkIHNwZWNpZmljIHByaWNlJyBmb3JtIHZhbHVlc1xuICAgKiB1c2luZyBwcmV2aW91c2x5IHN0b3JlZCBkZWZhdWx0IHZhbHVlc1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgcmVzZXRDcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzKCkge1xuICAgIHZhciBwcmV2aW91c2x5U3RvcmVkVmFsdWVzID0gdGhpcy4kY3JlYXRlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcztcblxuICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtJykuZmluZCgnaW5wdXQnKS5lYWNoKChpbmRleCwgdmFsdWUpID0+IHtcbiAgICAgICQodmFsdWUpLnZhbChwcmV2aW91c2x5U3RvcmVkVmFsdWVzWyQodmFsdWUpLmF0dHIoJ2lkJyldKTtcbiAgICB9KTtcblxuICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtJykuZmluZCgnc2VsZWN0JykuZWFjaCgoaW5kZXgsIHZhbHVlKSA9PiB7XG4gICAgICAkKHZhbHVlKS52YWwocHJldmlvdXNseVN0b3JlZFZhbHVlc1skKHZhbHVlKS5hdHRyKCdpZCcpXSkuY2hhbmdlKCk7XG4gICAgfSk7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmZpbmQoJ2lucHV0OmNoZWNrYm94JykuZWFjaCgoaW5kZXgsIHZhbHVlKSA9PiB7XG4gICAgICAkKHZhbHVlKS5wcm9wKFwiY2hlY2tlZFwiLCB0cnVlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0gYm9vbGVhbiB1c2VQcmVmaXhGb3JDcmVhdGVcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGVuYWJsZVNwZWNpZmljUHJpY2VGaWVsZElmRWxpZ2libGUodXNlUHJlZml4Rm9yQ3JlYXRlKSB7XG4gICAgdmFyIHNlbGVjdG9yUHJlZml4ID0gdGhpcy5nZXRQcmVmaXhTZWxlY3Rvcih1c2VQcmVmaXhGb3JDcmVhdGUpO1xuXG4gICAgJChzZWxlY3RvclByZWZpeCArICdzcF9wcmljZScpLnByb3AoJ2Rpc2FibGVkJywgJChzZWxlY3RvclByZWZpeCArICdsZWF2ZV9icHJpY2UnKS5pcygnOmNoZWNrZWQnKSkudmFsKCcnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBPcGVuICdlZGl0IHNwZWNpZmljIHByaWNlJyBmb3JtIGludG8gYSBtb2RhbFxuICAgKlxuICAgKiBAcGFyYW0gaW50ZWdlciBzcGVjaWZpY1ByaWNlSWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIG9wZW5FZGl0UHJpY2VNb2RhbEFuZExvYWRGb3JtKHNwZWNpZmljUHJpY2VJZCkge1xuICAgIGNvbnN0IHVybCA9ICQoJyNqcy1zcGVjaWZpYy1wcmljZS1saXN0JykuZGF0YSgnYWN0aW9uRWRpdCcpLnJlcGxhY2UoL2Zvcm1cXC9cXGQrLywgJ2Zvcm0vJyArIHNwZWNpZmljUHJpY2VJZCk7XG5cbiAgICAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbCcpLm1vZGFsKFwic2hvd1wiKTtcbiAgICB0aGlzLmVkaXRNb2RhbElzT3BlbiA9IHRydWU7XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICB1cmw6IHVybCxcbiAgICB9KVxuICAgICAgICAuZG9uZShyZXNwb25zZSA9PiB7XG4gICAgICAgICAgdGhpcy5pbnNlcnRFZGl0U3BlY2lmaWNQcmljZUZvcm1JbnRvTW9kYWwocmVzcG9uc2UpO1xuICAgICAgICAgICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0nKS5kYXRhKCdzcGVjaWZpY1ByaWNlSWQnLCBzcGVjaWZpY1ByaWNlSWQpO1xuICAgICAgICAgIHRoaXMuY29uZmlndXJlRWRpdFByaWNlRm9ybUluc2lkZU1vZGFsQmVoYXZpb3IoKTtcbiAgICAgICAgfSlcbiAgICAgICAgLmZhaWwoZXJyb3JzID0+IHtcbiAgICAgICAgICBzaG93RXJyb3JNZXNzYWdlKGVycm9ycy5yZXNwb25zZUpTT04pO1xuICAgICAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgY2xvc2VFZGl0UHJpY2VNb2RhbEFuZFJlbW92ZUZvcm0oKSB7XG4gICAgJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwnKS5tb2RhbChcImhpZGVcIik7XG4gICAgdGhpcy5lZGl0TW9kYWxJc09wZW4gPSBmYWxzZTtcblxuICAgIHZhciBmb3JtTG9jYXRpb25Ib2xkZXIgPSAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtJyk7XG5cbiAgICBmb3JtTG9jYXRpb25Ib2xkZXIuZW1wdHkoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0gc3RyaW5nIGZvcm06IEhUTUwgJ2VkaXQgc3BlY2lmaWMgcHJpY2UnIGZvcm1cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGluc2VydEVkaXRTcGVjaWZpY1ByaWNlRm9ybUludG9Nb2RhbChmb3JtKSB7XG4gICAgdmFyIGZvcm1Mb2NhdGlvbkhvbGRlciA9ICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0nKTtcblxuICAgIGZvcm1Mb2NhdGlvbkhvbGRlci5lbXB0eSgpO1xuICAgIGZvcm1Mb2NhdGlvbkhvbGRlci5hcHBlbmQoZm9ybSk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IHByb2R1Y3QgSUQgZm9yIGN1cnJlbnQgQ2F0YWxvZyBQcm9kdWN0IHBhZ2VcbiAgICpcbiAgICogQHJldHVybnMgaW50ZWdlclxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgZ2V0UHJvZHVjdElkKCkge1xuICAgIHJldHVybiAkKCcjZm9ybV9pZF9wcm9kdWN0JykudmFsKCk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIGJvb2xlYW4gdXNlUHJlZml4Rm9yQ3JlYXRlXG4gICAqXG4gICAqIEByZXR1cm5zIHN0cmluZ1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgZ2V0UHJlZml4U2VsZWN0b3IodXNlUHJlZml4Rm9yQ3JlYXRlKSB7XG4gICAgaWYgKHVzZVByZWZpeEZvckNyZWF0ZSA9PSB0cnVlKSB7XG4gICAgICByZXR1cm4gJyMnICsgdGhpcy5wcmVmaXhDcmVhdGVGb3JtO1xuICAgIH0gZWxzZSB7XG4gICAgICByZXR1cm4gJyMnICsgdGhpcy5wcmVmaXhFZGl0Rm9ybTtcbiAgICB9XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgU3BlY2lmaWNQcmljZUZvcm1IYW5kbGVyO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvY2F0YWxvZy9wcm9kdWN0L3NwZWNpZmljLXByaWNlLWZvcm0taGFuZGxlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBTcGVjaWZpY1ByaWNlRm9ybUhhbmRsZXIgZnJvbSAnLi9zcGVjaWZpYy1wcmljZS1mb3JtLWhhbmRsZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBuZXcgU3BlY2lmaWNQcmljZUZvcm1IYW5kbGVyKCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2NhdGFsb2cvcHJvZHVjdC9pbmRleC5qcyJdLCJzb3VyY2VSb290IjoiIn0=