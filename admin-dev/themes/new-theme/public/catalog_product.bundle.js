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
/******/ 	return __webpack_require__(__webpack_require__.s = 315);
/******/ })
/************************************************************************/
/******/ ({

/***/ 251:
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

/***/ 315:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _specificPriceFormHandler = __webpack_require__(251);

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODI/M2YxNioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvY2F0YWxvZy9wcm9kdWN0L3NwZWNpZmljLXByaWNlLWZvcm0taGFuZGxlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9jYXRhbG9nL3Byb2R1Y3QvaW5kZXguanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIlNwZWNpZmljUHJpY2VGb3JtSGFuZGxlciIsInByZWZpeENyZWF0ZUZvcm0iLCJwcmVmaXhFZGl0Rm9ybSIsImVkaXRNb2RhbElzT3BlbiIsIiRjcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzIiwiT2JqZWN0Iiwic3RvcmVQcmljZUZvcm1EZWZhdWx0VmFsdWVzIiwibG9hZEFuZERpc3BsYXlFeGlzdGluZ1NwZWNpZmljUHJpY2VzTGlzdCIsImNvbmZpZ3VyZUFkZFByaWNlRm9ybUJlaGF2aW9yIiwiY29uZmlndXJlRWRpdFByaWNlTW9kYWxCZWhhdmlvciIsImNvbmZpZ3VyZURlbGV0ZVByaWNlQnV0dG9uc0JlaGF2aW9yIiwiY29uZmlndXJlTXVsdGlwbGVNb2RhbHNCZWhhdmlvciIsImxpc3RDb250YWluZXIiLCJ1cmwiLCJkYXRhIiwicmVwbGFjZSIsImdldFByb2R1Y3RJZCIsImFqYXgiLCJ0eXBlIiwiZG9uZSIsInRib2R5IiwiZmluZCIsInJlbW92ZSIsInNwZWNpZmljUHJpY2VzIiwibGVuZ3RoIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsInNwZWNpZmljUHJpY2VzTGlzdCIsInJlbmRlclNwZWNpZmljUHJpY2VzTGlzdGluZ0FzSHRtbCIsImFwcGVuZCIsInNlbGYiLCJlYWNoIiwiaW5kZXgiLCJzcGVjaWZpY1ByaWNlIiwiZGVsZXRlVXJsIiwiYXR0ciIsImlkX3NwZWNpZmljX3ByaWNlIiwicm93IiwicmVuZGVyU3BlY2lmaWNQcmljZVJvdyIsInNwZWNpZmljUHJpY2VJZCIsInJ1bGVfbmFtZSIsImF0dHJpYnV0ZXNfbmFtZSIsImN1cnJlbmN5IiwiY291bnRyeSIsImdyb3VwIiwiY3VzdG9tZXIiLCJmaXhlZF9wcmljZSIsImltcGFjdCIsInBlcmlvZCIsImZyb21fcXVhbnRpdHkiLCJjYW5fZGVsZXRlIiwiY2FuX2VkaXQiLCJ1c2VQcmVmaXhGb3JDcmVhdGUiLCJzZWxlY3RvclByZWZpeCIsImdldFByZWZpeFNlbGVjdG9yIiwiY2xpY2siLCJyZXNldENyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMiLCJjb2xsYXBzZSIsIm9uIiwic3VibWl0Q3JlYXRlUHJpY2VGb3JtIiwibG9hZEFuZEZpbGxPcHRpb25zRm9yU2VsZWN0Q29tYmluYXRpb25JbnB1dCIsImVuYWJsZVNwZWNpZmljUHJpY2VGaWVsZElmRWxpZ2libGUiLCJlbmFibGVTcGVjaWZpY1ByaWNlVGF4RmllbGRJZkVsaWdpYmxlIiwiY2xvc2VFZGl0UHJpY2VNb2RhbEFuZFJlbW92ZUZvcm0iLCJzdWJtaXRFZGl0UHJpY2VGb3JtIiwicmVpbml0aWFsaXplRGF0ZVBpY2tlcnMiLCJpbml0aWFsaXplTGVhdmVCUHJpY2VGaWVsZCIsImRhdGV0aW1lcGlja2VyIiwiZm9ybWF0IiwidmFsIiwicHJvcCIsImRvY3VtZW50IiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsImN1cnJlbnRUYXJnZXQiLCJvcGVuRWRpdFByaWNlTW9kYWxBbmRMb2FkRm9ybSIsImRlbGV0ZVNwZWNpZmljUHJpY2UiLCJzZXJpYWxpemUiLCJzaG93U3VjY2Vzc01lc3NhZ2UiLCJ0cmFuc2xhdGVfamF2YXNjcmlwdHMiLCJyZW1vdmVBdHRyIiwiZmFpbCIsInNob3dFcnJvck1lc3NhZ2UiLCJlcnJvcnMiLCJyZXNwb25zZUpTT04iLCJiYXNlVXJsIiwiY2xpY2tlZExpbmsiLCJtb2RhbENvbmZpcm1hdGlvbiIsImNyZWF0ZSIsIm9uQ29udGludWUiLCJyZXNwb25zZSIsInNob3ciLCJzdG9yYWdlIiwidmFsdWUiLCJpbnB1dEZpZWxkIiwiY29tYmluYXRpb25zIiwiY29tYmluYXRpb24iLCJpZCIsIm5hbWUiLCJ0cmlnZ2VyIiwiaGlkZSIsInByZXZpb3VzbHlTdG9yZWRWYWx1ZXMiLCJjaGFuZ2UiLCJpcyIsIm1vZGFsIiwiaW5zZXJ0RWRpdFNwZWNpZmljUHJpY2VGb3JtSW50b01vZGFsIiwiY29uZmlndXJlRWRpdFByaWNlRm9ybUluc2lkZU1vZGFsQmVoYXZpb3IiLCJmb3JtTG9jYXRpb25Ib2xkZXIiLCJlbXB0eSIsImZvcm0iXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNoRUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTUEsSUFBSUMsT0FBT0QsQ0FBakI7O0lBRU1FLHdCO0FBRUosc0NBQWM7QUFBQTs7QUFDWixTQUFLQyxnQkFBTCxHQUF3Qiw0QkFBeEI7QUFDQSxTQUFLQyxjQUFMLEdBQXNCLGFBQXRCO0FBQ0EsU0FBS0MsZUFBTCxHQUF1QixLQUF2Qjs7QUFFQSxTQUFLQyw2QkFBTCxHQUFxQyxJQUFJQyxNQUFKLEVBQXJDO0FBQ0EsU0FBS0MsMkJBQUw7O0FBRUEsU0FBS0Msd0NBQUw7O0FBRUEsU0FBS0MsNkJBQUw7O0FBRUEs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QUdZRix3Qjs7Ozs7Ozs7OztBQ3ZkZjs7Ozs7O0FBRUEsSUFBTUYsSUFBSUMsT0FBT0QsQ0FBakIsQyxDQTNCQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQTZCQUEsRUFBRSxZQUFNO0FBQ04sTUFBSUUsa0NBQUo7QUFDRCxDQUZELEUiLCJmaWxlIjoiY2F0YWxvZ19wcm9kdWN0LmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMzE1KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCA1ZDk5OTA5NGQxMWFlZmYwYjU4MiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuY2xhc3MgU3BlY2lmaWNQcmljZUZvcm1IYW5kbGVyIHtcblxuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnByZWZpeENyZWF0ZUZvcm0gPSAnZm9ybV9zdGVwMl9zcGVjaWZpY19wcmljZV8nO1xuICAgIHRoaXMucHJlZml4RWRpdEZvcm0gPSAnZm9ybV9tb2RhbF8nO1xuICAgIHRoaXMuZWRpdE1vZGFsSXNPcGVuID0gZmFsc2U7XG5cbiAgICB0aGlzLiRjcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzID0gbmV3IE9iamVjdCgpO1xuICAgIHRoaXMuc3RvcmVQcmljZUZvcm1EZWZhdWx0VmFsdWVzKCk7XG5cbiAgICB0aGlzLmxvYWRBbmREaXNwbGF5RXhpc3RpbmdTcGVjaWZpY1ByaWNlc0xpc3QoKTtcblxuICAgIHRoaXMuY29uZmlndXJlQWRkUHJpY2VGb3JtQmVoYXZpb3IoKTtcblxuICAgIHRoaXMuY29uZmlndXJlRWRpdFByaWNlTW9kYWxCZWhhdmlvcigpO1xuXG4gICAgdGhpcy5jb25maWd1cmVEZWxldGVQcmljZUJ1dHRvbnNCZWhhdmlvcigpO1xuXG4gICAgdGhpcy5jb25maWd1cmVNdWx0aXBsZU1vZGFsc0JlaGF2aW9yKCk7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGxvYWRBbmREaXNwbGF5RXhpc3RpbmdTcGVjaWZpY1ByaWNlc0xpc3QoKSB7XG4gICAgdmFyIGxpc3RDb250YWluZXIgPSAkKCcjanMtc3BlY2lmaWMtcHJpY2UtbGlzdCcpO1xuICAgIHZhciB1cmwgPSBsaXN0Q29udGFpbmVyLmRhdGEoJ2xpc3RpbmdVcmwnKS5yZXBsYWNlKC9saXN0XFwvXFxkKy8sICdsaXN0LycgKyB0aGlzLmdldFByb2R1Y3RJZCgpKTtcblxuICAgICQuYWpheCh7XG4gICAgICB0eXBlOiAnR0VUJyxcbiAgICAgIHVybDogdXJsLFxuICAgIH0pXG4gICAgICAgIC5kb25lKHNwZWNpZmljUHJpY2VzID0+IHtcbiAgICAgICAgICB2YXIgdGJvZHkgPSBsaXN0Q29udGFpbmVyLmZpbmQoJ3Rib2R5Jyk7XG4gICAgICAgICAgdGJvZHkuZmluZCgndHInKS5yZW1vdmUoKTtcblxuICAgICAgICAgIGlmIChzcGVjaWZpY1ByaWNlcy5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICBsaXN0Q29udGFpbmVyLnJlbW92ZUNsYXNzKCdoaWRlJyk7XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGxpc3RDb250YWluZXIuYWRkQ2xhc3MoJ2hpZGUnKTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICB2YXIgc3BlY2lmaWNQcmljZXNMaXN0ID0gdGhpcy5yZW5kZXJTcGVjaWZpY1ByaWNlc0xpc3RpbmdBc0h0bWwoc3BlY2lmaWNQcmljZXMpO1xuXG4gICAgICAgICAgdGJvZHkuYXBwZW5kKHNwZWNpZmljUHJpY2VzTGlzdCk7XG4gICAgICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSBhcnJheSBzcGVjaWZpY1ByaWNlc1xuICAgKlxuICAgKiBAcmV0dXJucyBzdHJpbmdcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHJlbmRlclNwZWNpZmljUHJpY2VzTGlzdGluZ0FzSHRtbChzcGVjaWZpY1ByaWNlcykge1xuICAgIHZhciBzcGVjaWZpY1ByaWNlc0xpc3QgPSAnJztcblxuICAgIHZhciBzZWxmID0gdGhpcztcblxuICAgICQuZWFjaChzcGVjaWZpY1ByaWNlcywgKGluZGV4LCBzcGVjaWZpY1ByaWNlKSA9PiB7XG4gICAgICB2YXIgZGVsZXRlVXJsID0gJCgnI2pzLXNwZWNpZmljLXByaWNlLWxpc3QnKS5hdHRyKCdkYXRhLWFjdGlvbi1kZWxldGUnKS5yZXBsYWNlKC9kZWxldGVcXC9cXGQrLywgJ2RlbGV0ZS8nICsgc3BlY2lmaWNQcmljZS5pZF9zcGVjaWZpY19wcmljZSk7XG4gICAgICB2YXIgcm93ID0gc2VsZi5yZW5kZXJTcGVjaWZpY1ByaWNlUm93KHNwZWNpZmljUHJpY2UsIGRlbGV0ZVVybCk7XG5cbiAgICAgIHNwZWNpZmljUHJpY2VzTGlzdCA9IHNwZWNpZmljUHJpY2VzTGlzdCArIHJvdztcbiAgICB9KTtcblxuICAgIHJldHVybiBzcGVjaWZpY1ByaWNlc0xpc3Q7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIE9iamVjdCBzcGVjaWZpY1ByaWNlXG4gICAqIEBwYXJhbSBzdHJpbmcgZGVsZXRlVXJsXG4gICAqXG4gICAqIEByZXR1cm5zIHN0cmluZ1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgcmVuZGVyU3BlY2lmaWNQcmljZVJvdyhzcGVjaWZpY1ByaWNlLCBkZWxldGVVcmwpIHtcblxuICAgIHZhciBzcGVjaWZpY1ByaWNlSWQgPSBzcGVjaWZpY1ByaWNlLmlkX3NwZWNpZmljX3ByaWNlO1xuXG4gICAgdmFyIHJvdyA9ICc8dHI+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UucnVsZV9uYW1lICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuYXR0cmlidXRlc19uYW1lICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuY3VycmVuY3kgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5jb3VudHJ5ICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuZ3JvdXAgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5jdXN0b21lciArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyBzcGVjaWZpY1ByaWNlLmZpeGVkX3ByaWNlICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuaW1wYWN0ICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UucGVyaW9kICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuZnJvbV9xdWFudGl0eSArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyAoc3BlY2lmaWNQcmljZS5jYW5fZGVsZXRlID8gJzxhIGhyZWY9XCInICsgZGVsZXRlVXJsICsgJ1wiIGNsYXNzPVwianMtZGVsZXRlIGRlbGV0ZSBidG4gdG9vbHRpcC1saW5rIGRlbGV0ZSBwbC0wIHByLTBcIj48aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+ZGVsZXRlPC9pPjwvYT4nIDogJycpICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIChzcGVjaWZpY1ByaWNlLmNhbl9lZGl0ID8gJzxhIGhyZWY9XCIjXCIgZGF0YS1zcGVjaWZpYy1wcmljZS1pZD1cIicgKyBzcGVjaWZpY1ByaWNlSWQgKyAnXCIgY2xhc3M9XCJqcy1lZGl0IGVkaXQgYnRuIHRvb2x0aXAtbGluayBkZWxldGUgcGwtMCBwci0wXCI+PGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmVkaXQ8L2k+PC9hPicgOiAnJykgKyAnPC90ZD4nICtcbiAgICAgICAgJzwvdHI+JztcblxuICAgIHJldHVybiByb3c7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGNvbmZpZ3VyZUFkZFByaWNlRm9ybUJlaGF2aW9yKCkge1xuICAgIGNvbnN0IHVzZVByZWZpeEZvckNyZWF0ZSA9IHRydWU7XG4gICAgdmFyIHNlbGVjdG9yUHJlZml4ID0gdGhpcy5nZXRQcmVmaXhTZWxlY3Rvcih1c2VQcmVmaXhGb3JDcmVhdGUpO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0gLmpzLWNhbmNlbCcpLmNsaWNrKCgpID0+IHtcbiAgICAgIHRoaXMucmVzZXRDcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzKCk7XG4gICAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmNvbGxhcHNlKCdoaWRlJyk7XG4gICAgfSk7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybSAuanMtc2F2ZScpLm9uKCdjbGljaycsICgpID0+IHRoaXMuc3VibWl0Q3JlYXRlUHJpY2VGb3JtKCkpO1xuXG4gICAgJCgnI2pzLW9wZW4tY3JlYXRlLXNwZWNpZmljLXByaWNlLWZvcm0nKS5vbignY2xpY2snLCAoKSA9PiB0aGlzLmxvYWRBbmRGaWxsT3B0aW9uc0ZvclNlbGVjdENvbWJpbmF0aW9uSW5wdXQodXNlUHJlZml4Rm9yQ3JlYXRlKSk7XG5cbiAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ2xlYXZlX2JwcmljZScpLm9uKCdjbGljaycsICgpID0+IHRoaXMuZW5hYmxlU3BlY2lmaWNQcmljZUZpZWxkSWZFbGlnaWJsZSh1c2VQcmVmaXhGb3JDcmVhdGUpKTtcblxuICAgICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcmVkdWN0aW9uX3R5cGUnKS5vbignY2hhbmdlJywgKCkgPT4gdGhpcy5lbmFibGVTcGVjaWZpY1ByaWNlVGF4RmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBjb25maWd1cmVFZGl0UHJpY2VGb3JtSW5zaWRlTW9kYWxCZWhhdmlvcigpIHtcbiAgICBjb25zdCB1c2VQcmVmaXhGb3JDcmVhdGUgPSBmYWxzZTtcbiAgICB2YXIgc2VsZWN0b3JQcmVmaXggPSB0aGlzLmdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSk7XG5cbiAgICAkKCcjZm9ybV9tb2RhbF9jYW5jZWwnKS5jbGljaygoKSA9PiB0aGlzLmNsb3NlRWRpdFByaWNlTW9kYWxBbmRSZW1vdmVGb3JtKCkpO1xuICAgICQoJyNmb3JtX21vZGFsX2Nsb3NlJykuY2xpY2soKCkgPT4gdGhpcy5jbG9zZUVkaXRQcmljZU1vZGFsQW5kUmVtb3ZlRm9ybSgpKTtcblxuICAgICQoJyNmb3JtX21vZGFsX3NhdmUnKS5jbGljaygoKSA9PiB0aGlzLnN1Ym1pdEVkaXRQcmljZUZvcm0oKSk7XG5cbiAgICB0aGlzLmxvYWRBbmRGaWxsT3B0aW9uc0ZvclNlbGVjdENvbWJpbmF0aW9uSW5wdXQodXNlUHJlZml4Rm9yQ3JlYXRlKTtcblxuICAgICQoc2VsZWN0b3JQcmVmaXggKyAnbGVhdmVfYnByaWNlJykub24oJ2NsaWNrJywgKCkgPT4gdGhpcy5lbmFibGVTcGVjaWZpY1ByaWNlRmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkpO1xuXG4gICAgJChzZWxlY3RvclByZWZpeCArICdzcF9yZWR1Y3Rpb25fdHlwZScpLm9uKCdjaGFuZ2UnLCAoKSA9PiB0aGlzLmVuYWJsZVNwZWNpZmljUHJpY2VUYXhGaWVsZElmRWxpZ2libGUodXNlUHJlZml4Rm9yQ3JlYXRlKSk7XG5cbiAgICB0aGlzLnJlaW5pdGlhbGl6ZURhdGVQaWNrZXJzKCk7XG5cbiAgICB0aGlzLmluaXRpYWxpemVMZWF2ZUJQcmljZUZpZWxkKHVzZVByZWZpeEZvckNyZWF0ZSk7XG4gICAgdGhpcy5lbmFibGVTcGVjaWZpY1ByaWNlVGF4RmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSk7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHJlaW5pdGlhbGl6ZURhdGVQaWNrZXJzKCkge1xuICAgICQoJy5kYXRlcGlja2VyIGlucHV0JykuZGF0ZXRpbWVwaWNrZXIoe2Zvcm1hdDogJ1lZWVktTU0tREQnfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIGJvb2xlYW4gdXNlUHJlZml4Rm9yQ3JlYXRlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBpbml0aWFsaXplTGVhdmVCUHJpY2VGaWVsZCh1c2VQcmVmaXhGb3JDcmVhdGUpIHtcbiAgICB2YXIgc2VsZWN0b3JQcmVmaXggPSB0aGlzLmdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSk7XG5cbiAgICBpZiAoJChzZWxlY3RvclByZWZpeCArICdzcF9wcmljZScpLnZhbCgpICE9ICcnKSB7XG4gICAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ3NwX3ByaWNlJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gICAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ2xlYXZlX2JwcmljZScpLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBjb25maWd1cmVFZGl0UHJpY2VNb2RhbEJlaGF2aW9yKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcjanMtc3BlY2lmaWMtcHJpY2UtbGlzdCAuanMtZWRpdCcsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgdmFyIHNwZWNpZmljUHJpY2VJZCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnc3BlY2lmaWNQcmljZUlkJyk7XG5cbiAgICAgIHRoaXMub3BlbkVkaXRQcmljZU1vZGFsQW5kTG9hZEZvcm0oc3BlY2lmaWNQcmljZUlkKTtcbiAgICB9KTtcblxuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBjb25maWd1cmVEZWxldGVQcmljZUJ1dHRvbnNCZWhhdmlvcigpIHtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnI2pzLXNwZWNpZmljLXByaWNlLWxpc3QgLmpzLWRlbGV0ZScsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIHRoaXMuZGVsZXRlU3BlY2lmaWNQcmljZShldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAc2VlIGh0dHBzOi8vdmlqYXlhc2Fua2Fybi53b3JkcHJlc3MuY29tLzIwMTcvMDIvMjQvcXVpY2stZml4LXNjcm9sbGluZy1hbmQtZm9jdXMtd2hlbi1tdWx0aXBsZS1ib290c3RyYXAtbW9kYWxzLWFyZS1vcGVuL1xuICAgKi9cbiAgY29uZmlndXJlTXVsdGlwbGVNb2RhbHNCZWhhdmlvcigpIHtcbiAgICAkKCcubW9kYWwnKS5vbignaGlkZGVuLmJzLm1vZGFsJywgKCkgPT4ge1xuICAgICAgaWYgKHRoaXMuZWRpdE1vZGFsSXNPcGVuKSB7XG4gICAgICAgICQoJ2JvZHknKS5hZGRDbGFzcygnbW9kYWwtb3BlbicpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBzdWJtaXRDcmVhdGVQcmljZUZvcm0oKSB7XG5cbiAgICBjb25zdCB1cmwgPSAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmF0dHIoJ2RhdGEtYWN0aW9uJyk7XG4gICAgY29uc3QgZGF0YSA9ICQoJyNzcGVjaWZpY19wcmljZV9mb3JtIGlucHV0LCAjc3BlY2lmaWNfcHJpY2VfZm9ybSBzZWxlY3QsICNmb3JtX2lkX3Byb2R1Y3QnKS5zZXJpYWxpemUoKTtcblxuICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtIC5qcy1zYXZlJykuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcblxuICAgICQuYWpheCh7XG4gICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICB1cmw6IHVybCxcbiAgICAgIGRhdGE6IGRhdGEsXG4gICAgfSlcbiAgICAgICAgLmRvbmUocmVzcG9uc2UgPT4ge1xuICAgICAgICAgIHNob3dTdWNjZXNzTWVzc2FnZSh0cmFuc2xhdGVfamF2YXNjcmlwdHNbJ0Zvcm0gdXBkYXRlIHN1Y2Nlc3MnXSk7XG4gICAgICAgICAgdGhpcy5yZXNldENyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMoKTtcbiAgICAgICAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmNvbGxhcHNlKCdoaWRlJyk7XG4gICAgICAgICAgdGhpcy5sb2FkQW5kRGlzcGxheUV4aXN0aW5nU3BlY2lmaWNQcmljZXNMaXN0KCk7XG5cbiAgICAgICAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybSAuanMtc2F2ZScpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgfSlcbiAgICAgICAgLmZhaWwoZXJyb3JzID0+IHtcbiAgICAgICAgICBzaG93RXJyb3JNZXNzYWdlKGVycm9ycy5yZXNwb25zZUpTT04pO1xuXG4gICAgICAgICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0gLmpzLXNhdmUnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICAgICAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgc3VibWl0RWRpdFByaWNlRm9ybSgpIHtcbiAgICBjb25zdCBiYXNlVXJsID0gJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybScpLmF0dHIoJ2RhdGEtYWN0aW9uJyk7XG4gICAgY29uc3Qgc3BlY2lmaWNQcmljZUlkID0gJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybScpLmRhdGEoJ3NwZWNpZmljUHJpY2VJZCcpO1xuICAgIGNvbnN0IHVybCA9IGJhc2VVcmwucmVwbGFjZSgvdXBkYXRlXFwvXFxkKy8sICd1cGRhdGUvJyArIHNwZWNpZmljUHJpY2VJZCk7XG5cbiAgICBjb25zdCBkYXRhID0gJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybSBpbnB1dCwgI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybSBzZWxlY3QsICNmb3JtX2lkX3Byb2R1Y3QnKS5zZXJpYWxpemUoKTtcblxuICAgICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0gLmpzLXNhdmUnKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgIHVybDogdXJsLFxuICAgICAgZGF0YTogZGF0YSxcbiAgICB9KVxuICAgICAgICAuZG9uZShyZXNwb25zZSA9PiB7XG4gICAgICAgICAgc2hvd1N1Y2Nlc3NNZXNzYWdlKHRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snRm9ybSB1cGRhdGUgc3VjY2VzcyddKTtcbiAgICAgICAgICB0aGlzLmNsb3NlRWRpdFByaWNlTW9kYWxBbmRSZW1vdmVGb3JtKCk7XG4gICAgICAgICAgdGhpcy5sb2FkQW5kRGlzcGxheUV4aXN0aW5nU3BlY2lmaWNQcmljZXNMaXN0KCk7XG4gICAgICAgICAgJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybSAuanMtc2F2ZScpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgICAgIH0pXG4gICAgICAgIC5mYWlsKGVycm9ycyA9PiB7XG4gICAgICAgICAgc2hvd0Vycm9yTWVzc2FnZShlcnJvcnMucmVzcG9uc2VKU09OKTtcblxuICAgICAgICAgICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0gLmpzLXNhdmUnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICAgICAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0gc3RyaW5nIGNsaWNrZWRMaW5rIHNlbGVjdG9yXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBkZWxldGVTcGVjaWZpY1ByaWNlKGNsaWNrZWRMaW5rKSB7XG4gICAgbW9kYWxDb25maXJtYXRpb24uY3JlYXRlKHRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snVGhpcyB3aWxsIGRlbGV0ZSB0aGUgc3BlY2lmaWMgcHJpY2UuIERvIHlvdSB3aXNoIHRvIHByb2NlZWQ/J10sIG51bGwsIHtcbiAgICAgIG9uQ29udGludWU6ICgpID0+IHtcblxuICAgICAgICB2YXIgdXJsID0gJChjbGlja2VkTGluaykuYXR0cignaHJlZicpO1xuICAgICAgICAkKGNsaWNrZWRMaW5rKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICAgICAgdXJsOiB1cmwsXG4gICAgICAgIH0pXG4gICAgICAgICAgICAuZG9uZShyZXNwb25zZSA9PiB7XG4gICAgICAgICAgICAgIHRoaXMubG9hZEFuZERpc3BsYXlFeGlzdGluZ1NwZWNpZmljUHJpY2VzTGlzdCgpO1xuICAgICAgICAgICAgICBzaG93U3VjY2Vzc01lc3NhZ2UocmVzcG9uc2UpO1xuICAgICAgICAgICAgICAkKGNsaWNrZWRMaW5rKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICAgICAgICAgICAgfSlcbiAgICAgICAgICAgIC5mYWlsKGVycm9ycyA9PiB7XG4gICAgICAgICAgICAgIHNob3dFcnJvck1lc3NhZ2UoZXJyb3JzLnJlc3BvbnNlSlNPTik7XG4gICAgICAgICAgICAgICQoY2xpY2tlZExpbmspLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgIH0pO1xuICAgICAgfVxuICAgIH0pLnNob3coKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTdG9yZSAnYWRkIHNwZWNpZmljIHByaWNlJyBmb3JtIHZhbHVlc1xuICAgKiBmb3IgZnV0dXJlIHVzYWdlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBzdG9yZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMoKSB7XG4gICAgdmFyIHN0b3JhZ2UgPSB0aGlzLiRjcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0nKS5maW5kKCdzZWxlY3QsaW5wdXQnKS5lYWNoKChpbmRleCwgdmFsdWUpID0+IHtcbiAgICAgIHN0b3JhZ2VbJCh2YWx1ZSkuYXR0cignaWQnKV0gPSAkKHZhbHVlKS52YWwoKTtcbiAgICB9KTtcblxuICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtJykuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKS5lYWNoKChpbmRleCwgdmFsdWUpID0+IHtcbiAgICAgIHN0b3JhZ2VbJCh2YWx1ZSkuYXR0cignaWQnKV0gPSAkKHZhbHVlKS5wcm9wKCdjaGVja2VkJyk7XG4gICAgfSk7XG5cbiAgICB0aGlzLiRjcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzID0gc3RvcmFnZTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0gYm9vbGVhbiB1c2VQcmVmaXhGb3JDcmVhdGVcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGxvYWRBbmRGaWxsT3B0aW9uc0ZvclNlbGVjdENvbWJpbmF0aW9uSW5wdXQodXNlUHJlZml4Rm9yQ3JlYXRlKSB7XG5cbiAgICB2YXIgc2VsZWN0b3JQcmVmaXggPSB0aGlzLmdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSk7XG5cbiAgICB2YXIgaW5wdXRGaWVsZCA9ICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfaWRfcHJvZHVjdF9hdHRyaWJ1dGUnKTtcbiAgICB2YXIgdXJsID0gaW5wdXRGaWVsZC5hdHRyKCdkYXRhLWFjdGlvbicpLnJlcGxhY2UoL3Byb2R1Y3QtY29tYmluYXRpb25zXFwvXFxkKy8sICdwcm9kdWN0LWNvbWJpbmF0aW9ucy8nICsgdGhpcy5nZXRQcm9kdWN0SWQoKSk7XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICB1cmw6IHVybCxcbiAgICB9KVxuICAgICAgICAuZG9uZShjb21iaW5hdGlvbnMgPT4ge1xuICAgICAgICAgIC8qKiByZW1vdmUgYWxsIG9wdGlvbnMgZXhjZXB0IGZpcnN0IG9uZSAqL1xuICAgICAgICAgIGlucHV0RmllbGQuZmluZCgnb3B0aW9uOmd0KDApJykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAkLmVhY2goY29tYmluYXRpb25zLCAoaW5kZXgsIGNvbWJpbmF0aW9uKSA9PiB7XG4gICAgICAgICAgICBpbnB1dEZpZWxkLmFwcGVuZCgnPG9wdGlvbiB2YWx1ZT1cIicgKyBjb21iaW5hdGlvbi5pZCArICdcIj4nICsgY29tYmluYXRpb24ubmFtZSArICc8L29wdGlvbj4nKTtcbiAgICAgICAgICB9KTtcblxuICAgICAgICAgIGlmIChpbnB1dEZpZWxkLmRhdGEoJ3NlbGVjdGVkQXR0cmlidXRlJykgIT0gJzAnKSB7XG4gICAgICAgICAgICBpbnB1dEZpZWxkLnZhbChpbnB1dEZpZWxkLmRhdGEoJ3NlbGVjdGVkQXR0cmlidXRlJykpLnRyaWdnZXIoJ2NoYW5nZScpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIGJvb2xlYW4gdXNlUHJlZml4Rm9yQ3JlYXRlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBlbmFibGVTcGVjaWZpY1ByaWNlVGF4RmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkge1xuXG4gICAgdmFyIHNlbGVjdG9yUHJlZml4ID0gdGhpcy5nZXRQcmVmaXhTZWxlY3Rvcih1c2VQcmVmaXhGb3JDcmVhdGUpO1xuXG4gICAgaWYgKCQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcmVkdWN0aW9uX3R5cGUnKS52YWwoKSA9PT0gJ3BlcmNlbnRhZ2UnKSB7XG4gICAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ3NwX3JlZHVjdGlvbl90YXgnKS5oaWRlKCk7XG4gICAgfSBlbHNlIHtcbiAgICAgICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcmVkdWN0aW9uX3RheCcpLnNob3coKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogUmVzZXQgJ2FkZCBzcGVjaWZpYyBwcmljZScgZm9ybSB2YWx1ZXNcbiAgICogdXNpbmcgcHJldmlvdXNseSBzdG9yZWQgZGVmYXVsdCB2YWx1ZXNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHJlc2V0Q3JlYXRlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcygpIHtcbiAgICB2YXIgcHJldmlvdXNseVN0b3JlZFZhbHVlcyA9IHRoaXMuJGNyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXM7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmZpbmQoJ2lucHV0JykuZWFjaCgoaW5kZXgsIHZhbHVlKSA9PiB7XG4gICAgICAkKHZhbHVlKS52YWwocHJldmlvdXNseVN0b3JlZFZhbHVlc1skKHZhbHVlKS5hdHRyKCdpZCcpXSk7XG4gICAgfSk7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmZpbmQoJ3NlbGVjdCcpLmVhY2goKGluZGV4LCB2YWx1ZSkgPT4ge1xuICAgICAgJCh2YWx1ZSkudmFsKHByZXZpb3VzbHlTdG9yZWRWYWx1ZXNbJCh2YWx1ZSkuYXR0cignaWQnKV0pLmNoYW5nZSgpO1xuICAgIH0pO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0nKS5maW5kKCdpbnB1dDpjaGVja2JveCcpLmVhY2goKGluZGV4LCB2YWx1ZSkgPT4ge1xuICAgICAgJCh2YWx1ZSkucHJvcChcImNoZWNrZWRcIiwgdHJ1ZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIGJvb2xlYW4gdXNlUHJlZml4Rm9yQ3JlYXRlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBlbmFibGVTcGVjaWZpY1ByaWNlRmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkge1xuICAgIHZhciBzZWxlY3RvclByZWZpeCA9IHRoaXMuZ2V0UHJlZml4U2VsZWN0b3IodXNlUHJlZml4Rm9yQ3JlYXRlKTtcblxuICAgICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcHJpY2UnKS5wcm9wKCdkaXNhYmxlZCcsICQoc2VsZWN0b3JQcmVmaXggKyAnbGVhdmVfYnByaWNlJykuaXMoJzpjaGVja2VkJykpLnZhbCgnJyk7XG4gIH1cblxuICAvKipcbiAgICogT3BlbiAnZWRpdCBzcGVjaWZpYyBwcmljZScgZm9ybSBpbnRvIGEgbW9kYWxcbiAgICpcbiAgICogQHBhcmFtIGludGVnZXIgc3BlY2lmaWNQcmljZUlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBvcGVuRWRpdFByaWNlTW9kYWxBbmRMb2FkRm9ybShzcGVjaWZpY1ByaWNlSWQpIHtcbiAgICBjb25zdCB1cmwgPSAkKCcjanMtc3BlY2lmaWMtcHJpY2UtbGlzdCcpLmRhdGEoJ2FjdGlvbkVkaXQnKS5yZXBsYWNlKC9mb3JtXFwvXFxkKy8sICdmb3JtLycgKyBzcGVjaWZpY1ByaWNlSWQpO1xuXG4gICAgJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwnKS5tb2RhbChcInNob3dcIik7XG4gICAgdGhpcy5lZGl0TW9kYWxJc09wZW4gPSB0cnVlO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdHRVQnLFxuICAgICAgdXJsOiB1cmwsXG4gICAgfSlcbiAgICAgICAgLmRvbmUocmVzcG9uc2UgPT4ge1xuICAgICAgICAgIHRoaXMuaW5zZXJ0RWRpdFNwZWNpZmljUHJpY2VGb3JtSW50b01vZGFsKHJlc3BvbnNlKTtcbiAgICAgICAgICAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtJykuZGF0YSgnc3BlY2lmaWNQcmljZUlkJywgc3BlY2lmaWNQcmljZUlkKTtcbiAgICAgICAgICB0aGlzLmNvbmZpZ3VyZUVkaXRQcmljZUZvcm1JbnNpZGVNb2RhbEJlaGF2aW9yKCk7XG4gICAgICAgIH0pXG4gICAgICAgIC5mYWlsKGVycm9ycyA9PiB7XG4gICAgICAgICAgc2hvd0Vycm9yTWVzc2FnZShlcnJvcnMucmVzcG9uc2VKU09OKTtcbiAgICAgICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGNsb3NlRWRpdFByaWNlTW9kYWxBbmRSZW1vdmVGb3JtKCkge1xuICAgICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsJykubW9kYWwoXCJoaWRlXCIpO1xuICAgIHRoaXMuZWRpdE1vZGFsSXNPcGVuID0gZmFsc2U7XG5cbiAgICB2YXIgZm9ybUxvY2F0aW9uSG9sZGVyID0gJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybScpO1xuXG4gICAgZm9ybUxvY2F0aW9uSG9sZGVyLmVtcHR5KCk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIHN0cmluZyBmb3JtOiBIVE1MICdlZGl0IHNwZWNpZmljIHByaWNlJyBmb3JtXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBpbnNlcnRFZGl0U3BlY2lmaWNQcmljZUZvcm1JbnRvTW9kYWwoZm9ybSkge1xuICAgIHZhciBmb3JtTG9jYXRpb25Ib2xkZXIgPSAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtJyk7XG5cbiAgICBmb3JtTG9jYXRpb25Ib2xkZXIuZW1wdHkoKTtcbiAgICBmb3JtTG9jYXRpb25Ib2xkZXIuYXBwZW5kKGZvcm0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBwcm9kdWN0IElEIGZvciBjdXJyZW50IENhdGFsb2cgUHJvZHVjdCBwYWdlXG4gICAqXG4gICAqIEByZXR1cm5zIGludGVnZXJcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGdldFByb2R1Y3RJZCgpIHtcbiAgICByZXR1cm4gJCgnI2Zvcm1faWRfcHJvZHVjdCcpLnZhbCgpO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSBib29sZWFuIHVzZVByZWZpeEZvckNyZWF0ZVxuICAgKlxuICAgKiBAcmV0dXJucyBzdHJpbmdcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSkge1xuICAgIGlmICh1c2VQcmVmaXhGb3JDcmVhdGUgPT0gdHJ1ZSkge1xuICAgICAgcmV0dXJuICcjJyArIHRoaXMucHJlZml4Q3JlYXRlRm9ybTtcbiAgICB9IGVsc2Uge1xuICAgICAgcmV0dXJuICcjJyArIHRoaXMucHJlZml4RWRpdEZvcm07XG4gICAgfVxuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IFNwZWNpZmljUHJpY2VGb3JtSGFuZGxlcjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2NhdGFsb2cvcHJvZHVjdC9zcGVjaWZpYy1wcmljZS1mb3JtLWhhbmRsZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgU3BlY2lmaWNQcmljZUZvcm1IYW5kbGVyIGZyb20gJy4vc3BlY2lmaWMtcHJpY2UtZm9ybS1oYW5kbGVyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgbmV3IFNwZWNpZmljUHJpY2VGb3JtSGFuZGxlcigpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9jYXRhbG9nL3Byb2R1Y3QvaW5kZXguanMiXSwic291cmNlUm9vdCI6IiJ9