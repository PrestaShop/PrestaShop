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
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
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
/******/ 	__webpack_require__.p = "/admin-dev/themes/new-theme/public/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/pages/catalog/product/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/pages/catalog/product/index.js":
/*!*******************************************!*\
  !*** ./js/pages/catalog/product/index.js ***!
  \*******************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _specific_price_form_handler__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./specific-price-form-handler */ "./js/pages/catalog/product/specific-price-form-handler.js");
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
$(function () {
  new _specific_price_form_handler__WEBPACK_IMPORTED_MODULE_0__["default"]();
});

/***/ }),

/***/ "./js/pages/catalog/product/specific-price-form-handler.js":
/*!*****************************************************************!*\
  !*** ./js/pages/catalog/product/specific-price-form-handler.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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

var SpecificPriceFormHandler =
/*#__PURE__*/
function () {
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
    key: "loadAndDisplayExistingSpecificPricesList",
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
    key: "renderSpecificPricesListingAsHtml",
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
    key: "renderSpecificPriceRow",
    value: function renderSpecificPriceRow(specificPrice, deleteUrl) {
      var specificPriceId = specificPrice.id_specific_price;
      var row = '<tr>' + '<td>' + specificPrice.rule_name + '</td>' + '<td>' + specificPrice.attributes_name + '</td>' + '<td>' + specificPrice.currency + '</td>' + '<td>' + specificPrice.country + '</td>' + '<td>' + specificPrice.group + '</td>' + '<td>' + specificPrice.customer + '</td>' + '<td>' + specificPrice.fixed_price + '</td>' + '<td>' + specificPrice.impact + '</td>' + '<td>' + specificPrice.period + '</td>' + '<td>' + specificPrice.from_quantity + '</td>' + '<td>' + (specificPrice.can_delete ? '<a href="' + deleteUrl + '" class="js-delete delete btn tooltip-link delete pl-0 pr-0"><i class="material-icons">delete</i></a>' : '') + '</td>' + '<td>' + (specificPrice.can_edit ? '<a href="#" data-specific-price-id="' + specificPriceId + '" class="js-edit edit btn tooltip-link delete pl-0 pr-0"><i class="material-icons">edit</i></a>' : '') + '</td>' + '</tr>';
      return row;
    }
    /**
     * @private
     */

  }, {
    key: "configureAddPriceFormBehavior",
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
    key: "configureEditPriceFormInsideModalBehavior",
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
    key: "reinitializeDatePickers",
    value: function reinitializeDatePickers() {
      $('.datepicker input').datetimepicker({
        format: 'YYYY-MM-DD'
      });
    }
    /**
     * @param boolean usePrefixForCreate
     *
     * @private
     */

  }, {
    key: "initializeLeaveBPriceField",
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
    key: "configureEditPriceModalBehavior",
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
    key: "configureDeletePriceButtonsBehavior",
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
    key: "configureMultipleModalsBehavior",
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
    key: "submitCreatePriceForm",
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
    key: "submitEditPriceForm",
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
    key: "deleteSpecificPrice",
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
    key: "storePriceFormDefaultValues",
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
    key: "loadAndFillOptionsForSelectCombinationInput",
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
    key: "enableSpecificPriceTaxFieldIfEligible",
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
    key: "resetCreatePriceFormDefaultValues",
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
    key: "enableSpecificPriceFieldIfEligible",
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
    key: "openEditPriceModalAndLoadForm",
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
    key: "closeEditPriceModalAndRemoveForm",
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
    key: "insertEditSpecificPriceFormIntoModal",
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
    key: "getProductId",
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
    key: "getPrefixSelector",
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

/* harmony default export */ __webpack_exports__["default"] = (SpecificPriceFormHandler);

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvY2F0YWxvZy9wcm9kdWN0L2luZGV4LmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2NhdGFsb2cvcHJvZHVjdC9zcGVjaWZpYy1wcmljZS1mb3JtLWhhbmRsZXIuanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIlNwZWNpZmljUHJpY2VGb3JtSGFuZGxlciIsInByZWZpeENyZWF0ZUZvcm0iLCJwcmVmaXhFZGl0Rm9ybSIsImVkaXRNb2RhbElzT3BlbiIsIiRjcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzIiwiT2JqZWN0Iiwic3RvcmVQcmljZUZvcm1EZWZhdWx0VmFsdWVzIiwibG9hZEFuZERpc3BsYXlFeGlzdGluZ1NwZWNpZmljUHJpY2VzTGlzdCIsImNvbmZpZ3VyZUFkZFByaWNlRm9ybUJlaGF2aW9yIiwiY29uZmlndXJlRWRpdFByaWNlTW9kYWxCZWhhdmlvciIsImNvbmZpZ3VyZURlbGV0ZVByaWNlQnV0dG9uc0JlaGF2aW9yIiwiY29uZmlndXJlTXVsdGlwbGVNb2RhbHNCZWhhdmlvciIsImxpc3RDb250YWluZXIiLCJ1cmwiLCJkYXRhIiwicmVwbGFjZSIsImdldFByb2R1Y3RJZCIsImFqYXgiLCJ0eXBlIiwiZG9uZSIsInNwZWNpZmljUHJpY2VzIiwidGJvZHkiLCJmaW5kIiwicmVtb3ZlIiwibGVuZ3RoIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsInNwZWNpZmljUHJpY2VzTGlzdCIsInJlbmRlclNwZWNpZmljUHJpY2VzTGlzdGluZ0FzSHRtbCIsImFwcGVuZCIsInNlbGYiLCJlYWNoIiwiaW5kZXgiLCJzcGVjaWZpY1ByaWNlIiwiZGVsZXRlVXJsIiwiYXR0ciIsImlkX3NwZWNpZmljX3ByaWNlIiwicm93IiwicmVuZGVyU3BlY2lmaWNQcmljZVJvdyIsInNwZWNpZmljUHJpY2VJZCIsInJ1bGVfbmFtZSIsImF0dHJpYnV0ZXNfbmFtZSIsImN1cnJlbmN5IiwiY291bnRyeSIsImdyb3VwIiwiY3VzdG9tZXIiLCJmaXhlZF9wcmljZSIsImltcGFjdCIsInBlcmlvZCIsImZyb21fcXVhbnRpdHkiLCJjYW5fZGVsZXRlIiwiY2FuX2VkaXQiLCJ1c2VQcmVmaXhGb3JDcmVhdGUiLCJzZWxlY3RvclByZWZpeCIsImdldFByZWZpeFNlbGVjdG9yIiwiY2xpY2siLCJyZXNldENyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMiLCJjb2xsYXBzZSIsIm9uIiwic3VibWl0Q3JlYXRlUHJpY2VGb3JtIiwibG9hZEFuZEZpbGxPcHRpb25zRm9yU2VsZWN0Q29tYmluYXRpb25JbnB1dCIsImVuYWJsZVNwZWNpZmljUHJpY2VGaWVsZElmRWxpZ2libGUiLCJlbmFibGVTcGVjaWZpY1ByaWNlVGF4RmllbGRJZkVsaWdpYmxlIiwiY2xvc2VFZGl0UHJpY2VNb2RhbEFuZFJlbW92ZUZvcm0iLCJzdWJtaXRFZGl0UHJpY2VGb3JtIiwicmVpbml0aWFsaXplRGF0ZVBpY2tlcnMiLCJpbml0aWFsaXplTGVhdmVCUHJpY2VGaWVsZCIsImRhdGV0aW1lcGlja2VyIiwiZm9ybWF0IiwidmFsIiwicHJvcCIsImRvY3VtZW50IiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsImN1cnJlbnRUYXJnZXQiLCJvcGVuRWRpdFByaWNlTW9kYWxBbmRMb2FkRm9ybSIsImRlbGV0ZVNwZWNpZmljUHJpY2UiLCJzZXJpYWxpemUiLCJyZXNwb25zZSIsInNob3dTdWNjZXNzTWVzc2FnZSIsInRyYW5zbGF0ZV9qYXZhc2NyaXB0cyIsInJlbW92ZUF0dHIiLCJmYWlsIiwiZXJyb3JzIiwic2hvd0Vycm9yTWVzc2FnZSIsInJlc3BvbnNlSlNPTiIsImJhc2VVcmwiLCJjbGlja2VkTGluayIsIm1vZGFsQ29uZmlybWF0aW9uIiwiY3JlYXRlIiwib25Db250aW51ZSIsInNob3ciLCJzdG9yYWdlIiwidmFsdWUiLCJpbnB1dEZpZWxkIiwiY29tYmluYXRpb25zIiwiY29tYmluYXRpb24iLCJpZCIsIm5hbWUiLCJ0cmlnZ2VyIiwiaGlkZSIsInByZXZpb3VzbHlTdG9yZWRWYWx1ZXMiLCJjaGFuZ2UiLCJpcyIsIm1vZGFsIiwiaW5zZXJ0RWRpdFNwZWNpZmljUHJpY2VGb3JtSW50b01vZGFsIiwiY29uZmlndXJlRWRpdFByaWNlRm9ybUluc2lkZU1vZGFsQmVoYXZpb3IiLCJmb3JtTG9jYXRpb25Ib2xkZXIiLCJlbXB0eSIsImZvcm0iXSwibWFwcGluZ3MiOiI7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLGtEQUEwQyxnQ0FBZ0M7QUFDMUU7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxnRUFBd0Qsa0JBQWtCO0FBQzFFO0FBQ0EseURBQWlELGNBQWM7QUFDL0Q7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGlEQUF5QyxpQ0FBaUM7QUFDMUUsd0hBQWdILG1CQUFtQixFQUFFO0FBQ3JJO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7OztBQUdBO0FBQ0E7Ozs7Ozs7Ozs7Ozs7QUNsRkE7QUFBQTtBQUFBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkE7QUFFQSxJQUFNQSxDQUFDLEdBQUdDLE1BQU0sQ0FBQ0QsQ0FBakI7QUFFQUEsQ0FBQyxDQUFDLFlBQU07QUFDTixNQUFJRSxvRUFBSjtBQUNELENBRkEsQ0FBRCxDOzs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDN0JBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTUYsQ0FB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Qzs7QUFDQWhGLFNBQUMsQ0FBQyxpQ0FBRCxDQUFELENBQXFDZ0IsSUFBckMsQ0FBMEMsaUJBQTFDLEVBQTZEeUIsZUFBN0Q7O0FBQ0EsZUFBSSxDQUFDbUUseUNBQUw7QUFDRCxPQVJMLEVBU0t4QixJQVRMLENBU1UsVUFBQUMsTUFBTSxFQUFJO0FBQ2RDLHdCQUFnQixDQUFDRCxNQUFNLENBQUNFLFlBQVIsQ0FBaEI7QUFDRCxPQVhMO0FBWUQ7QUFFRDs7Ozs7O3VEQUdtQztBQUNqQ3ZGLE9BQUMsQ0FBQyw0QkFBRCxDQUFELENBQWdDMEcsS0FBaEMsQ0FBc0MsTUFBdEM7QUFDQSxXQUFLckcsZUFBTCxHQUF1QixLQUF2QjtBQUVBLFVBQUl3RyxrQkFBa0IsR0FBRzdHLENBQUMsQ0FBQyxpQ0FBRCxDQUExQjtBQUVBNkcsd0JBQWtCLENBQUNDLEtBQW5CO0FBQ0Q7QUFFRDs7Ozs7Ozs7eURBS3FDQyxJLEVBQU07QUFDekMsVUFBSUYsa0JBQWtCLEdBQUc3RyxDQUFDLENBQUMsaUNBQUQsQ0FBMUI7QUFFQTZHLHdCQUFrQixDQUFDQyxLQUFuQjtBQUNBRCx3QkFBa0IsQ0FBQzlFLE1BQW5CLENBQTBCZ0YsSUFBMUI7QUFDRDtBQUVEOzs7Ozs7Ozs7O21DQU9lO0FBQ2IsYUFBTy9HLENBQUMsQ0FBQyxrQkFBRCxDQUFELENBQXNCdUUsR0FBdEIsRUFBUDtBQUNEO0FBRUQ7Ozs7Ozs7Ozs7c0NBT2tCakIsa0IsRUFBb0I7QUFDcEMsVUFBSUEsa0JBQWtCLElBQUksSUFBMUIsRUFBZ0M7QUFDOUIsZUFBTyxNQUFNLEtBQUtuRCxnQkFBbEI7QUFDRCxPQUZELE1BRU87QUFDTCxlQUFPLE1BQU0sS0FBS0MsY0FBbEI7QUFDRDtBQUNGOzs7Ozs7QUFHWUYsdUZBQWYsRSIsImZpbGUiOiJjYXRhbG9nX3Byb2R1Y3QuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZ2V0dGVyIH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSBmdW5jdGlvbihleHBvcnRzKSB7XG4gXHRcdGlmKHR5cGVvZiBTeW1ib2wgIT09ICd1bmRlZmluZWQnICYmIFN5bWJvbC50b1N0cmluZ1RhZykge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBTeW1ib2wudG9TdHJpbmdUYWcsIHsgdmFsdWU6ICdNb2R1bGUnIH0pO1xuIFx0XHR9XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCAnX19lc01vZHVsZScsIHsgdmFsdWU6IHRydWUgfSk7XG4gXHR9O1xuXG4gXHQvLyBjcmVhdGUgYSBmYWtlIG5hbWVzcGFjZSBvYmplY3RcbiBcdC8vIG1vZGUgJiAxOiB2YWx1ZSBpcyBhIG1vZHVsZSBpZCwgcmVxdWlyZSBpdFxuIFx0Ly8gbW9kZSAmIDI6IG1lcmdlIGFsbCBwcm9wZXJ0aWVzIG9mIHZhbHVlIGludG8gdGhlIG5zXG4gXHQvLyBtb2RlICYgNDogcmV0dXJuIHZhbHVlIHdoZW4gYWxyZWFkeSBucyBvYmplY3RcbiBcdC8vIG1vZGUgJiA4fDE6IGJlaGF2ZSBsaWtlIHJlcXVpcmVcbiBcdF9fd2VicGFja19yZXF1aXJlX18udCA9IGZ1bmN0aW9uKHZhbHVlLCBtb2RlKSB7XG4gXHRcdGlmKG1vZGUgJiAxKSB2YWx1ZSA9IF9fd2VicGFja19yZXF1aXJlX18odmFsdWUpO1xuIFx0XHRpZihtb2RlICYgOCkgcmV0dXJuIHZhbHVlO1xuIFx0XHRpZigobW9kZSAmIDQpICYmIHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcgJiYgdmFsdWUgJiYgdmFsdWUuX19lc01vZHVsZSkgcmV0dXJuIHZhbHVlO1xuIFx0XHR2YXIgbnMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIobnMpO1xuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkobnMsICdkZWZhdWx0JywgeyBlbnVtZXJhYmxlOiB0cnVlLCB2YWx1ZTogdmFsdWUgfSk7XG4gXHRcdGlmKG1vZGUgJiAyICYmIHR5cGVvZiB2YWx1ZSAhPSAnc3RyaW5nJykgZm9yKHZhciBrZXkgaW4gdmFsdWUpIF9fd2VicGFja19yZXF1aXJlX18uZChucywga2V5LCBmdW5jdGlvbihrZXkpIHsgcmV0dXJuIHZhbHVlW2tleV07IH0uYmluZChudWxsLCBrZXkpKTtcbiBcdFx0cmV0dXJuIG5zO1xuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCIvYWRtaW4tZGV2L3RoZW1lcy9uZXctdGhlbWUvcHVibGljL1wiO1xuXG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gXCIuL2pzL3BhZ2VzL2NhdGFsb2cvcHJvZHVjdC9pbmRleC5qc1wiKTtcbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBTcGVjaWZpY1ByaWNlRm9ybUhhbmRsZXIgZnJvbSAnLi9zcGVjaWZpYy1wcmljZS1mb3JtLWhhbmRsZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBuZXcgU3BlY2lmaWNQcmljZUZvcm1IYW5kbGVyKCk7XG59KTtcbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuY2xhc3MgU3BlY2lmaWNQcmljZUZvcm1IYW5kbGVyIHtcblxuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnByZWZpeENyZWF0ZUZvcm0gPSAnZm9ybV9zdGVwMl9zcGVjaWZpY19wcmljZV8nO1xuICAgIHRoaXMucHJlZml4RWRpdEZvcm0gPSAnZm9ybV9tb2RhbF8nO1xuICAgIHRoaXMuZWRpdE1vZGFsSXNPcGVuID0gZmFsc2U7XG5cbiAgICB0aGlzLiRjcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzID0gbmV3IE9iamVjdCgpO1xuICAgIHRoaXMuc3RvcmVQcmljZUZvcm1EZWZhdWx0VmFsdWVzKCk7XG5cbiAgICB0aGlzLmxvYWRBbmREaXNwbGF5RXhpc3RpbmdTcGVjaWZpY1ByaWNlc0xpc3QoKTtcblxuICAgIHRoaXMuY29uZmlndXJlQWRkUHJpY2VGb3JtQmVoYXZpb3IoKTtcblxuICAgIHRoaXMuY29uZmlndXJlRWRpdFByaWNlTW9kYWxCZWhhdmlvcigpO1xuXG4gICAgdGhpcy5jb25maWd1cmVEZWxldGVQcmljZUJ1dHRvbnNCZWhhdmlvcigpO1xuXG4gICAgdGhpcy5jb25maWd1cmVNdWx0aXBsZU1vZGFsc0JlaGF2aW9yKCk7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGxvYWRBbmREaXNwbGF5RXhpc3RpbmdTcGVjaWZpY1ByaWNlc0xpc3QoKSB7XG4gICAgdmFyIGxpc3RDb250YWluZXIgPSAkKCcjanMtc3BlY2lmaWMtcHJpY2UtbGlzdCcpO1xuICAgIHZhciB1cmwgPSBsaXN0Q29udGFpbmVyLmRhdGEoJ2xpc3RpbmdVcmwnKS5yZXBsYWNlKC9saXN0XFwvXFxkKy8sICdsaXN0LycgKyB0aGlzLmdldFByb2R1Y3RJZCgpKTtcblxuICAgICQuYWpheCh7XG4gICAgICB0eXBlOiAnR0VUJyxcbiAgICAgIHVybDogdXJsLFxuICAgIH0pXG4gICAgICAgIC5kb25lKHNwZWNpZmljUHJpY2VzID0+IHtcbiAgICAgICAgICB2YXIgdGJvZHkgPSBsaXN0Q29udGFpbmVyLmZpbmQoJ3Rib2R5Jyk7XG4gICAgICAgICAgdGJvZHkuZmluZCgndHInKS5yZW1vdmUoKTtcblxuICAgICAgICAgIGlmIChzcGVjaWZpY1ByaWNlcy5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICBsaXN0Q29udGFpbmVyLnJlbW92ZUNsYXNzKCdoaWRlJyk7XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGxpc3RDb250YWluZXIuYWRkQ2xhc3MoJ2hpZGUnKTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICB2YXIgc3BlY2lmaWNQcmljZXNMaXN0ID0gdGhpcy5yZW5kZXJTcGVjaWZpY1ByaWNlc0xpc3RpbmdBc0h0bWwoc3BlY2lmaWNQcmljZXMpO1xuXG4gICAgICAgICAgdGJvZHkuYXBwZW5kKHNwZWNpZmljUHJpY2VzTGlzdCk7XG4gICAgICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSBhcnJheSBzcGVjaWZpY1ByaWNlc1xuICAgKlxuICAgKiBAcmV0dXJucyBzdHJpbmdcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHJlbmRlclNwZWNpZmljUHJpY2VzTGlzdGluZ0FzSHRtbChzcGVjaWZpY1ByaWNlcykge1xuICAgIHZhciBzcGVjaWZpY1ByaWNlc0xpc3QgPSAnJztcblxuICAgIHZhciBzZWxmID0gdGhpcztcblxuICAgICQuZWFjaChzcGVjaWZpY1ByaWNlcywgKGluZGV4LCBzcGVjaWZpY1ByaWNlKSA9PiB7XG4gICAgICB2YXIgZGVsZXRlVXJsID0gJCgnI2pzLXNwZWNpZmljLXByaWNlLWxpc3QnKS5hdHRyKCdkYXRhLWFjdGlvbi1kZWxldGUnKS5yZXBsYWNlKC9kZWxldGVcXC9cXGQrLywgJ2RlbGV0ZS8nICsgc3BlY2lmaWNQcmljZS5pZF9zcGVjaWZpY19wcmljZSk7XG4gICAgICB2YXIgcm93ID0gc2VsZi5yZW5kZXJTcGVjaWZpY1ByaWNlUm93KHNwZWNpZmljUHJpY2UsIGRlbGV0ZVVybCk7XG5cbiAgICAgIHNwZWNpZmljUHJpY2VzTGlzdCA9IHNwZWNpZmljUHJpY2VzTGlzdCArIHJvdztcbiAgICB9KTtcblxuICAgIHJldHVybiBzcGVjaWZpY1ByaWNlc0xpc3Q7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIE9iamVjdCBzcGVjaWZpY1ByaWNlXG4gICAqIEBwYXJhbSBzdHJpbmcgZGVsZXRlVXJsXG4gICAqXG4gICAqIEByZXR1cm5zIHN0cmluZ1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgcmVuZGVyU3BlY2lmaWNQcmljZVJvdyhzcGVjaWZpY1ByaWNlLCBkZWxldGVVcmwpIHtcblxuICAgIHZhciBzcGVjaWZpY1ByaWNlSWQgPSBzcGVjaWZpY1ByaWNlLmlkX3NwZWNpZmljX3ByaWNlO1xuXG4gICAgdmFyIHJvdyA9ICc8dHI+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UucnVsZV9uYW1lICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuYXR0cmlidXRlc19uYW1lICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuY3VycmVuY3kgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5jb3VudHJ5ICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuZ3JvdXAgKyAnPC90ZD4nICtcbiAgICAgICAgJzx0ZD4nICsgc3BlY2lmaWNQcmljZS5jdXN0b21lciArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyBzcGVjaWZpY1ByaWNlLmZpeGVkX3ByaWNlICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuaW1wYWN0ICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UucGVyaW9kICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIHNwZWNpZmljUHJpY2UuZnJvbV9xdWFudGl0eSArICc8L3RkPicgK1xuICAgICAgICAnPHRkPicgKyAoc3BlY2lmaWNQcmljZS5jYW5fZGVsZXRlID8gJzxhIGhyZWY9XCInICsgZGVsZXRlVXJsICsgJ1wiIGNsYXNzPVwianMtZGVsZXRlIGRlbGV0ZSBidG4gdG9vbHRpcC1saW5rIGRlbGV0ZSBwbC0wIHByLTBcIj48aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+ZGVsZXRlPC9pPjwvYT4nIDogJycpICsgJzwvdGQ+JyArXG4gICAgICAgICc8dGQ+JyArIChzcGVjaWZpY1ByaWNlLmNhbl9lZGl0ID8gJzxhIGhyZWY9XCIjXCIgZGF0YS1zcGVjaWZpYy1wcmljZS1pZD1cIicgKyBzcGVjaWZpY1ByaWNlSWQgKyAnXCIgY2xhc3M9XCJqcy1lZGl0IGVkaXQgYnRuIHRvb2x0aXAtbGluayBkZWxldGUgcGwtMCBwci0wXCI+PGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmVkaXQ8L2k+PC9hPicgOiAnJykgKyAnPC90ZD4nICtcbiAgICAgICAgJzwvdHI+JztcblxuICAgIHJldHVybiByb3c7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGNvbmZpZ3VyZUFkZFByaWNlRm9ybUJlaGF2aW9yKCkge1xuICAgIGNvbnN0IHVzZVByZWZpeEZvckNyZWF0ZSA9IHRydWU7XG4gICAgdmFyIHNlbGVjdG9yUHJlZml4ID0gdGhpcy5nZXRQcmVmaXhTZWxlY3Rvcih1c2VQcmVmaXhGb3JDcmVhdGUpO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0gLmpzLWNhbmNlbCcpLmNsaWNrKCgpID0+IHtcbiAgICAgIHRoaXMucmVzZXRDcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzKCk7XG4gICAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmNvbGxhcHNlKCdoaWRlJyk7XG4gICAgfSk7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybSAuanMtc2F2ZScpLm9uKCdjbGljaycsICgpID0+IHRoaXMuc3VibWl0Q3JlYXRlUHJpY2VGb3JtKCkpO1xuXG4gICAgJCgnI2pzLW9wZW4tY3JlYXRlLXNwZWNpZmljLXByaWNlLWZvcm0nKS5vbignY2xpY2snLCAoKSA9PiB0aGlzLmxvYWRBbmRGaWxsT3B0aW9uc0ZvclNlbGVjdENvbWJpbmF0aW9uSW5wdXQodXNlUHJlZml4Rm9yQ3JlYXRlKSk7XG5cbiAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ2xlYXZlX2JwcmljZScpLm9uKCdjbGljaycsICgpID0+IHRoaXMuZW5hYmxlU3BlY2lmaWNQcmljZUZpZWxkSWZFbGlnaWJsZSh1c2VQcmVmaXhGb3JDcmVhdGUpKTtcblxuICAgICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcmVkdWN0aW9uX3R5cGUnKS5vbignY2hhbmdlJywgKCkgPT4gdGhpcy5lbmFibGVTcGVjaWZpY1ByaWNlVGF4RmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBjb25maWd1cmVFZGl0UHJpY2VGb3JtSW5zaWRlTW9kYWxCZWhhdmlvcigpIHtcbiAgICBjb25zdCB1c2VQcmVmaXhGb3JDcmVhdGUgPSBmYWxzZTtcbiAgICB2YXIgc2VsZWN0b3JQcmVmaXggPSB0aGlzLmdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSk7XG5cbiAgICAkKCcjZm9ybV9tb2RhbF9jYW5jZWwnKS5jbGljaygoKSA9PiB0aGlzLmNsb3NlRWRpdFByaWNlTW9kYWxBbmRSZW1vdmVGb3JtKCkpO1xuICAgICQoJyNmb3JtX21vZGFsX2Nsb3NlJykuY2xpY2soKCkgPT4gdGhpcy5jbG9zZUVkaXRQcmljZU1vZGFsQW5kUmVtb3ZlRm9ybSgpKTtcblxuICAgICQoJyNmb3JtX21vZGFsX3NhdmUnKS5jbGljaygoKSA9PiB0aGlzLnN1Ym1pdEVkaXRQcmljZUZvcm0oKSk7XG5cbiAgICB0aGlzLmxvYWRBbmRGaWxsT3B0aW9uc0ZvclNlbGVjdENvbWJpbmF0aW9uSW5wdXQodXNlUHJlZml4Rm9yQ3JlYXRlKTtcblxuICAgICQoc2VsZWN0b3JQcmVmaXggKyAnbGVhdmVfYnByaWNlJykub24oJ2NsaWNrJywgKCkgPT4gdGhpcy5lbmFibGVTcGVjaWZpY1ByaWNlRmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkpO1xuXG4gICAgJChzZWxlY3RvclByZWZpeCArICdzcF9yZWR1Y3Rpb25fdHlwZScpLm9uKCdjaGFuZ2UnLCAoKSA9PiB0aGlzLmVuYWJsZVNwZWNpZmljUHJpY2VUYXhGaWVsZElmRWxpZ2libGUodXNlUHJlZml4Rm9yQ3JlYXRlKSk7XG5cbiAgICB0aGlzLnJlaW5pdGlhbGl6ZURhdGVQaWNrZXJzKCk7XG5cbiAgICB0aGlzLmluaXRpYWxpemVMZWF2ZUJQcmljZUZpZWxkKHVzZVByZWZpeEZvckNyZWF0ZSk7XG4gICAgdGhpcy5lbmFibGVTcGVjaWZpY1ByaWNlVGF4RmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSk7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHJlaW5pdGlhbGl6ZURhdGVQaWNrZXJzKCkge1xuICAgICQoJy5kYXRlcGlja2VyIGlucHV0JykuZGF0ZXRpbWVwaWNrZXIoe2Zvcm1hdDogJ1lZWVktTU0tREQnfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIGJvb2xlYW4gdXNlUHJlZml4Rm9yQ3JlYXRlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBpbml0aWFsaXplTGVhdmVCUHJpY2VGaWVsZCh1c2VQcmVmaXhGb3JDcmVhdGUpIHtcbiAgICB2YXIgc2VsZWN0b3JQcmVmaXggPSB0aGlzLmdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSk7XG5cbiAgICBpZiAoJChzZWxlY3RvclByZWZpeCArICdzcF9wcmljZScpLnZhbCgpICE9ICcnKSB7XG4gICAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ3NwX3ByaWNlJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gICAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ2xlYXZlX2JwcmljZScpLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBjb25maWd1cmVFZGl0UHJpY2VNb2RhbEJlaGF2aW9yKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcjanMtc3BlY2lmaWMtcHJpY2UtbGlzdCAuanMtZWRpdCcsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgdmFyIHNwZWNpZmljUHJpY2VJZCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnc3BlY2lmaWNQcmljZUlkJyk7XG5cbiAgICAgIHRoaXMub3BlbkVkaXRQcmljZU1vZGFsQW5kTG9hZEZvcm0oc3BlY2lmaWNQcmljZUlkKTtcbiAgICB9KTtcblxuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBjb25maWd1cmVEZWxldGVQcmljZUJ1dHRvbnNCZWhhdmlvcigpIHtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnI2pzLXNwZWNpZmljLXByaWNlLWxpc3QgLmpzLWRlbGV0ZScsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIHRoaXMuZGVsZXRlU3BlY2lmaWNQcmljZShldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAc2VlIGh0dHBzOi8vdmlqYXlhc2Fua2Fybi53b3JkcHJlc3MuY29tLzIwMTcvMDIvMjQvcXVpY2stZml4LXNjcm9sbGluZy1hbmQtZm9jdXMtd2hlbi1tdWx0aXBsZS1ib290c3RyYXAtbW9kYWxzLWFyZS1vcGVuL1xuICAgKi9cbiAgY29uZmlndXJlTXVsdGlwbGVNb2RhbHNCZWhhdmlvcigpIHtcbiAgICAkKCcubW9kYWwnKS5vbignaGlkZGVuLmJzLm1vZGFsJywgKCkgPT4ge1xuICAgICAgaWYgKHRoaXMuZWRpdE1vZGFsSXNPcGVuKSB7XG4gICAgICAgICQoJ2JvZHknKS5hZGRDbGFzcygnbW9kYWwtb3BlbicpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBzdWJtaXRDcmVhdGVQcmljZUZvcm0oKSB7XG5cbiAgICBjb25zdCB1cmwgPSAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmF0dHIoJ2RhdGEtYWN0aW9uJyk7XG4gICAgY29uc3QgZGF0YSA9ICQoJyNzcGVjaWZpY19wcmljZV9mb3JtIGlucHV0LCAjc3BlY2lmaWNfcHJpY2VfZm9ybSBzZWxlY3QsICNmb3JtX2lkX3Byb2R1Y3QnKS5zZXJpYWxpemUoKTtcblxuICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtIC5qcy1zYXZlJykuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcblxuICAgICQuYWpheCh7XG4gICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICB1cmw6IHVybCxcbiAgICAgIGRhdGE6IGRhdGEsXG4gICAgfSlcbiAgICAgICAgLmRvbmUocmVzcG9uc2UgPT4ge1xuICAgICAgICAgIHNob3dTdWNjZXNzTWVzc2FnZSh0cmFuc2xhdGVfamF2YXNjcmlwdHNbJ0Zvcm0gdXBkYXRlIHN1Y2Nlc3MnXSk7XG4gICAgICAgICAgdGhpcy5yZXNldENyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMoKTtcbiAgICAgICAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmNvbGxhcHNlKCdoaWRlJyk7XG4gICAgICAgICAgdGhpcy5sb2FkQW5kRGlzcGxheUV4aXN0aW5nU3BlY2lmaWNQcmljZXNMaXN0KCk7XG5cbiAgICAgICAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybSAuanMtc2F2ZScpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgfSlcbiAgICAgICAgLmZhaWwoZXJyb3JzID0+IHtcbiAgICAgICAgICBzaG93RXJyb3JNZXNzYWdlKGVycm9ycy5yZXNwb25zZUpTT04pO1xuXG4gICAgICAgICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0gLmpzLXNhdmUnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICAgICAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgc3VibWl0RWRpdFByaWNlRm9ybSgpIHtcbiAgICBjb25zdCBiYXNlVXJsID0gJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybScpLmF0dHIoJ2RhdGEtYWN0aW9uJyk7XG4gICAgY29uc3Qgc3BlY2lmaWNQcmljZUlkID0gJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybScpLmRhdGEoJ3NwZWNpZmljUHJpY2VJZCcpO1xuICAgIGNvbnN0IHVybCA9IGJhc2VVcmwucmVwbGFjZSgvdXBkYXRlXFwvXFxkKy8sICd1cGRhdGUvJyArIHNwZWNpZmljUHJpY2VJZCk7XG5cbiAgICBjb25zdCBkYXRhID0gJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybSBpbnB1dCwgI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybSBzZWxlY3QsICNmb3JtX2lkX3Byb2R1Y3QnKS5zZXJpYWxpemUoKTtcblxuICAgICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0gLmpzLXNhdmUnKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgIHVybDogdXJsLFxuICAgICAgZGF0YTogZGF0YSxcbiAgICB9KVxuICAgICAgICAuZG9uZShyZXNwb25zZSA9PiB7XG4gICAgICAgICAgc2hvd1N1Y2Nlc3NNZXNzYWdlKHRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snRm9ybSB1cGRhdGUgc3VjY2VzcyddKTtcbiAgICAgICAgICB0aGlzLmNsb3NlRWRpdFByaWNlTW9kYWxBbmRSZW1vdmVGb3JtKCk7XG4gICAgICAgICAgdGhpcy5sb2FkQW5kRGlzcGxheUV4aXN0aW5nU3BlY2lmaWNQcmljZXNMaXN0KCk7XG4gICAgICAgICAgJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybSAuanMtc2F2ZScpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgICAgIH0pXG4gICAgICAgIC5mYWlsKGVycm9ycyA9PiB7XG4gICAgICAgICAgc2hvd0Vycm9yTWVzc2FnZShlcnJvcnMucmVzcG9uc2VKU09OKTtcblxuICAgICAgICAgICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsLWZvcm0gLmpzLXNhdmUnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICAgICAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0gc3RyaW5nIGNsaWNrZWRMaW5rIHNlbGVjdG9yXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBkZWxldGVTcGVjaWZpY1ByaWNlKGNsaWNrZWRMaW5rKSB7XG4gICAgbW9kYWxDb25maXJtYXRpb24uY3JlYXRlKHRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snVGhpcyB3aWxsIGRlbGV0ZSB0aGUgc3BlY2lmaWMgcHJpY2UuIERvIHlvdSB3aXNoIHRvIHByb2NlZWQ/J10sIG51bGwsIHtcbiAgICAgIG9uQ29udGludWU6ICgpID0+IHtcblxuICAgICAgICB2YXIgdXJsID0gJChjbGlja2VkTGluaykuYXR0cignaHJlZicpO1xuICAgICAgICAkKGNsaWNrZWRMaW5rKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICAgICAgdXJsOiB1cmwsXG4gICAgICAgIH0pXG4gICAgICAgICAgICAuZG9uZShyZXNwb25zZSA9PiB7XG4gICAgICAgICAgICAgIHRoaXMubG9hZEFuZERpc3BsYXlFeGlzdGluZ1NwZWNpZmljUHJpY2VzTGlzdCgpO1xuICAgICAgICAgICAgICBzaG93U3VjY2Vzc01lc3NhZ2UocmVzcG9uc2UpO1xuICAgICAgICAgICAgICAkKGNsaWNrZWRMaW5rKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICAgICAgICAgICAgfSlcbiAgICAgICAgICAgIC5mYWlsKGVycm9ycyA9PiB7XG4gICAgICAgICAgICAgIHNob3dFcnJvck1lc3NhZ2UoZXJyb3JzLnJlc3BvbnNlSlNPTik7XG4gICAgICAgICAgICAgICQoY2xpY2tlZExpbmspLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgIH0pO1xuICAgICAgfVxuICAgIH0pLnNob3coKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTdG9yZSAnYWRkIHNwZWNpZmljIHByaWNlJyBmb3JtIHZhbHVlc1xuICAgKiBmb3IgZnV0dXJlIHVzYWdlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBzdG9yZVByaWNlRm9ybURlZmF1bHRWYWx1ZXMoKSB7XG4gICAgdmFyIHN0b3JhZ2UgPSB0aGlzLiRjcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0nKS5maW5kKCdzZWxlY3QsaW5wdXQnKS5lYWNoKChpbmRleCwgdmFsdWUpID0+IHtcbiAgICAgIHN0b3JhZ2VbJCh2YWx1ZSkuYXR0cignaWQnKV0gPSAkKHZhbHVlKS52YWwoKTtcbiAgICB9KTtcblxuICAgICQoJyNzcGVjaWZpY19wcmljZV9mb3JtJykuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKS5lYWNoKChpbmRleCwgdmFsdWUpID0+IHtcbiAgICAgIHN0b3JhZ2VbJCh2YWx1ZSkuYXR0cignaWQnKV0gPSAkKHZhbHVlKS5wcm9wKCdjaGVja2VkJyk7XG4gICAgfSk7XG5cbiAgICB0aGlzLiRjcmVhdGVQcmljZUZvcm1EZWZhdWx0VmFsdWVzID0gc3RvcmFnZTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0gYm9vbGVhbiB1c2VQcmVmaXhGb3JDcmVhdGVcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGxvYWRBbmRGaWxsT3B0aW9uc0ZvclNlbGVjdENvbWJpbmF0aW9uSW5wdXQodXNlUHJlZml4Rm9yQ3JlYXRlKSB7XG5cbiAgICB2YXIgc2VsZWN0b3JQcmVmaXggPSB0aGlzLmdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSk7XG5cbiAgICB2YXIgaW5wdXRGaWVsZCA9ICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfaWRfcHJvZHVjdF9hdHRyaWJ1dGUnKTtcbiAgICB2YXIgdXJsID0gaW5wdXRGaWVsZC5hdHRyKCdkYXRhLWFjdGlvbicpLnJlcGxhY2UoL3Byb2R1Y3QtY29tYmluYXRpb25zXFwvXFxkKy8sICdwcm9kdWN0LWNvbWJpbmF0aW9ucy8nICsgdGhpcy5nZXRQcm9kdWN0SWQoKSk7XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICB1cmw6IHVybCxcbiAgICB9KVxuICAgICAgICAuZG9uZShjb21iaW5hdGlvbnMgPT4ge1xuICAgICAgICAgIC8qKiByZW1vdmUgYWxsIG9wdGlvbnMgZXhjZXB0IGZpcnN0IG9uZSAqL1xuICAgICAgICAgIGlucHV0RmllbGQuZmluZCgnb3B0aW9uOmd0KDApJykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAkLmVhY2goY29tYmluYXRpb25zLCAoaW5kZXgsIGNvbWJpbmF0aW9uKSA9PiB7XG4gICAgICAgICAgICBpbnB1dEZpZWxkLmFwcGVuZCgnPG9wdGlvbiB2YWx1ZT1cIicgKyBjb21iaW5hdGlvbi5pZCArICdcIj4nICsgY29tYmluYXRpb24ubmFtZSArICc8L29wdGlvbj4nKTtcbiAgICAgICAgICB9KTtcblxuICAgICAgICAgIGlmIChpbnB1dEZpZWxkLmRhdGEoJ3NlbGVjdGVkQXR0cmlidXRlJykgIT0gJzAnKSB7XG4gICAgICAgICAgICBpbnB1dEZpZWxkLnZhbChpbnB1dEZpZWxkLmRhdGEoJ3NlbGVjdGVkQXR0cmlidXRlJykpLnRyaWdnZXIoJ2NoYW5nZScpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIGJvb2xlYW4gdXNlUHJlZml4Rm9yQ3JlYXRlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBlbmFibGVTcGVjaWZpY1ByaWNlVGF4RmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkge1xuXG4gICAgdmFyIHNlbGVjdG9yUHJlZml4ID0gdGhpcy5nZXRQcmVmaXhTZWxlY3Rvcih1c2VQcmVmaXhGb3JDcmVhdGUpO1xuXG4gICAgaWYgKCQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcmVkdWN0aW9uX3R5cGUnKS52YWwoKSA9PT0gJ3BlcmNlbnRhZ2UnKSB7XG4gICAgICAkKHNlbGVjdG9yUHJlZml4ICsgJ3NwX3JlZHVjdGlvbl90YXgnKS5oaWRlKCk7XG4gICAgfSBlbHNlIHtcbiAgICAgICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcmVkdWN0aW9uX3RheCcpLnNob3coKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogUmVzZXQgJ2FkZCBzcGVjaWZpYyBwcmljZScgZm9ybSB2YWx1ZXNcbiAgICogdXNpbmcgcHJldmlvdXNseSBzdG9yZWQgZGVmYXVsdCB2YWx1ZXNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHJlc2V0Q3JlYXRlUHJpY2VGb3JtRGVmYXVsdFZhbHVlcygpIHtcbiAgICB2YXIgcHJldmlvdXNseVN0b3JlZFZhbHVlcyA9IHRoaXMuJGNyZWF0ZVByaWNlRm9ybURlZmF1bHRWYWx1ZXM7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmZpbmQoJ2lucHV0JykuZWFjaCgoaW5kZXgsIHZhbHVlKSA9PiB7XG4gICAgICAkKHZhbHVlKS52YWwocHJldmlvdXNseVN0b3JlZFZhbHVlc1skKHZhbHVlKS5hdHRyKCdpZCcpXSk7XG4gICAgfSk7XG5cbiAgICAkKCcjc3BlY2lmaWNfcHJpY2VfZm9ybScpLmZpbmQoJ3NlbGVjdCcpLmVhY2goKGluZGV4LCB2YWx1ZSkgPT4ge1xuICAgICAgJCh2YWx1ZSkudmFsKHByZXZpb3VzbHlTdG9yZWRWYWx1ZXNbJCh2YWx1ZSkuYXR0cignaWQnKV0pLmNoYW5nZSgpO1xuICAgIH0pO1xuXG4gICAgJCgnI3NwZWNpZmljX3ByaWNlX2Zvcm0nKS5maW5kKCdpbnB1dDpjaGVja2JveCcpLmVhY2goKGluZGV4LCB2YWx1ZSkgPT4ge1xuICAgICAgJCh2YWx1ZSkucHJvcChcImNoZWNrZWRcIiwgdHJ1ZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIGJvb2xlYW4gdXNlUHJlZml4Rm9yQ3JlYXRlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBlbmFibGVTcGVjaWZpY1ByaWNlRmllbGRJZkVsaWdpYmxlKHVzZVByZWZpeEZvckNyZWF0ZSkge1xuICAgIHZhciBzZWxlY3RvclByZWZpeCA9IHRoaXMuZ2V0UHJlZml4U2VsZWN0b3IodXNlUHJlZml4Rm9yQ3JlYXRlKTtcblxuICAgICQoc2VsZWN0b3JQcmVmaXggKyAnc3BfcHJpY2UnKS5wcm9wKCdkaXNhYmxlZCcsICQoc2VsZWN0b3JQcmVmaXggKyAnbGVhdmVfYnByaWNlJykuaXMoJzpjaGVja2VkJykpLnZhbCgnJyk7XG4gIH1cblxuICAvKipcbiAgICogT3BlbiAnZWRpdCBzcGVjaWZpYyBwcmljZScgZm9ybSBpbnRvIGEgbW9kYWxcbiAgICpcbiAgICogQHBhcmFtIGludGVnZXIgc3BlY2lmaWNQcmljZUlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBvcGVuRWRpdFByaWNlTW9kYWxBbmRMb2FkRm9ybShzcGVjaWZpY1ByaWNlSWQpIHtcbiAgICBjb25zdCB1cmwgPSAkKCcjanMtc3BlY2lmaWMtcHJpY2UtbGlzdCcpLmRhdGEoJ2FjdGlvbkVkaXQnKS5yZXBsYWNlKC9mb3JtXFwvXFxkKy8sICdmb3JtLycgKyBzcGVjaWZpY1ByaWNlSWQpO1xuXG4gICAgJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwnKS5tb2RhbChcInNob3dcIik7XG4gICAgdGhpcy5lZGl0TW9kYWxJc09wZW4gPSB0cnVlO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdHRVQnLFxuICAgICAgdXJsOiB1cmwsXG4gICAgfSlcbiAgICAgICAgLmRvbmUocmVzcG9uc2UgPT4ge1xuICAgICAgICAgIHRoaXMuaW5zZXJ0RWRpdFNwZWNpZmljUHJpY2VGb3JtSW50b01vZGFsKHJlc3BvbnNlKTtcbiAgICAgICAgICAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtJykuZGF0YSgnc3BlY2lmaWNQcmljZUlkJywgc3BlY2lmaWNQcmljZUlkKTtcbiAgICAgICAgICB0aGlzLmNvbmZpZ3VyZUVkaXRQcmljZUZvcm1JbnNpZGVNb2RhbEJlaGF2aW9yKCk7XG4gICAgICAgIH0pXG4gICAgICAgIC5mYWlsKGVycm9ycyA9PiB7XG4gICAgICAgICAgc2hvd0Vycm9yTWVzc2FnZShlcnJvcnMucmVzcG9uc2VKU09OKTtcbiAgICAgICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGNsb3NlRWRpdFByaWNlTW9kYWxBbmRSZW1vdmVGb3JtKCkge1xuICAgICQoJyNlZGl0LXNwZWNpZmljLXByaWNlLW1vZGFsJykubW9kYWwoXCJoaWRlXCIpO1xuICAgIHRoaXMuZWRpdE1vZGFsSXNPcGVuID0gZmFsc2U7XG5cbiAgICB2YXIgZm9ybUxvY2F0aW9uSG9sZGVyID0gJCgnI2VkaXQtc3BlY2lmaWMtcHJpY2UtbW9kYWwtZm9ybScpO1xuXG4gICAgZm9ybUxvY2F0aW9uSG9sZGVyLmVtcHR5KCk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIHN0cmluZyBmb3JtOiBIVE1MICdlZGl0IHNwZWNpZmljIHByaWNlJyBmb3JtXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBpbnNlcnRFZGl0U3BlY2lmaWNQcmljZUZvcm1JbnRvTW9kYWwoZm9ybSkge1xuICAgIHZhciBmb3JtTG9jYXRpb25Ib2xkZXIgPSAkKCcjZWRpdC1zcGVjaWZpYy1wcmljZS1tb2RhbC1mb3JtJyk7XG5cbiAgICBmb3JtTG9jYXRpb25Ib2xkZXIuZW1wdHkoKTtcbiAgICBmb3JtTG9jYXRpb25Ib2xkZXIuYXBwZW5kKGZvcm0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBwcm9kdWN0IElEIGZvciBjdXJyZW50IENhdGFsb2cgUHJvZHVjdCBwYWdlXG4gICAqXG4gICAqIEByZXR1cm5zIGludGVnZXJcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGdldFByb2R1Y3RJZCgpIHtcbiAgICByZXR1cm4gJCgnI2Zvcm1faWRfcHJvZHVjdCcpLnZhbCgpO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSBib29sZWFuIHVzZVByZWZpeEZvckNyZWF0ZVxuICAgKlxuICAgKiBAcmV0dXJucyBzdHJpbmdcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIGdldFByZWZpeFNlbGVjdG9yKHVzZVByZWZpeEZvckNyZWF0ZSkge1xuICAgIGlmICh1c2VQcmVmaXhGb3JDcmVhdGUgPT0gdHJ1ZSkge1xuICAgICAgcmV0dXJuICcjJyArIHRoaXMucHJlZml4Q3JlYXRlRm9ybTtcbiAgICB9IGVsc2Uge1xuICAgICAgcmV0dXJuICcjJyArIHRoaXMucHJlZml4RWRpdEZvcm07XG4gICAgfVxuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IFNwZWNpZmljUHJpY2VGb3JtSGFuZGxlcjtcbiJdLCJzb3VyY2VSb290IjoiIn0=