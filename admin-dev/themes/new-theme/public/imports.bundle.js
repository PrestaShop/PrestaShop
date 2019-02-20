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
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/pages/import/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/pages/import/FormFieldToggle.js":
/*!********************************************!*\
  !*** ./js/pages/import/FormFieldToggle.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return FormFieldToggle; });
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
var entityCategories = 0;
var entityProducts = 1;
var entityCombinations = 2;
var entityCustomers = 3;
var entityAddresses = 4;
var entityBrands = 5;
var entitySuppliers = 6;
var entityAlias = 7;
var entityStoreContacts = 8;

var FormFieldToggle =
/*#__PURE__*/
function () {
  function FormFieldToggle() {
    var _this = this;

    _classCallCheck(this, FormFieldToggle);

    $('.js-entity-select').on('change', function () {
      return _this.toggleForm();
    });
    this.toggleForm();
  }

  _createClass(FormFieldToggle, [{
    key: "toggleForm",
    value: function toggleForm() {
      var selectedOption = $('#entity').find('option:selected');
      var selectedEntity = parseInt(selectedOption.val());
      var entityName = selectedOption.text().toLowerCase();
      this.toggleEntityAlert(selectedEntity);
      this.toggleFields(selectedEntity, entityName);
      this.loadAvailableFields(selectedEntity);
    }
    /**
     * Toggle alert warning for selected import entity
     *
     * @param {int} selectedEntity
     */

  }, {
    key: "toggleEntityAlert",
    value: function toggleEntityAlert(selectedEntity) {
      var $alert = $('.js-entity-alert');

      if ([entityCategories, entityProducts].includes(selectedEntity)) {
        $alert.show();
      } else {
        $alert.hide();
      }
    }
    /**
     * Toggle available options for selected entity
     *
     * @param {int} selectedEntity
     * @param {string} entityName
     */

  }, {
    key: "toggleFields",
    value: function toggleFields(selectedEntity, entityName) {
      var $truncateFormGroup = $('.js-truncate-form-group');
      var $matchRefFormGroup = $('.js-match-ref-form-group');
      var $regenerateFormGroup = $('.js-regenerate-form-group');
      var $forceIdsFormGroup = $('.js-force-ids-form-group');
      var $entityNamePlaceholder = $('.js-entity-name');

      if (entityStoreContacts === selectedEntity) {
        $truncateFormGroup.hide();
      } else {
        $truncateFormGroup.show();
      }

      if ([entityProducts, entityCombinations].includes(selectedEntity)) {
        $matchRefFormGroup.show();
      } else {
        $matchRefFormGroup.hide();
      }

      if ([entityCategories, entityProducts, entityBrands, entitySuppliers, entityStoreContacts].includes(selectedEntity)) {
        $regenerateFormGroup.show();
      } else {
        $regenerateFormGroup.hide();
      }

      if ([entityCategories, entityProducts, entityCustomers, entityAddresses, entityBrands, entitySuppliers, entityStoreContacts, entityAlias].includes(selectedEntity)) {
        $forceIdsFormGroup.show();
      } else {
        $forceIdsFormGroup.hide();
      }

      $entityNamePlaceholder.html(entityName);
    }
    /**
     * Load available fields for given entity
     *
     * @param {int} entity
     */

  }, {
    key: "loadAvailableFields",
    value: function loadAvailableFields(entity) {
      var _this2 = this;

      var $availableFields = $('.js-available-fields');
      $.ajax({
        url: $availableFields.data('url'),
        data: {
          entity: entity
        },
        dataType: 'json'
      }).then(function (response) {
        _this2._removeAvailableFields($availableFields);

        for (var i = 0; i < response.length; i++) {
          _this2._appendAvailableField($availableFields, response[i].label + (response[i].required ? '*' : ''), response[i].description);
        }

        $availableFields.find('[data-toggle="popover"]').popover();
      });
    }
    /**
     * Remove available fields content from given container.
     *
     * @param {jQuery} $container
     * @private
     */

  }, {
    key: "_removeAvailableFields",
    value: function _removeAvailableFields($container) {
      $container.find('[data-toggle="popover"]').popover('hide');
      $container.empty();
    }
    /**
     * Append a help box to given field.
     *
     * @param {jQuery} $field
     * @param {String} helpBoxContent
     * @private
     */

  }, {
    key: "_appendHelpBox",
    value: function _appendHelpBox($field, helpBoxContent) {
      var $helpBox = $('.js-available-field-popover-template').clone();
      $helpBox.attr('data-content', helpBoxContent);
      $helpBox.removeClass('js-available-field-popover-template d-none');
      $field.append($helpBox);
    }
    /**
     * Append available field to given container.
     *
     * @param {jQuery} $appendTo field will be appended to this container.
     * @param {String} fieldText
     * @param {String} helpBoxContent
     * @private
     */

  }, {
    key: "_appendAvailableField",
    value: function _appendAvailableField($appendTo, fieldText, helpBoxContent) {
      var $field = $('.js-available-field-template').clone();
      $field.text(fieldText);

      if (helpBoxContent) {
        // Append help box next to the field
        this._appendHelpBox($field, helpBoxContent);
      }

      $field.removeClass('js-available-field-template d-none');
      $field.appendTo($appendTo);
    }
  }]);

  return FormFieldToggle;
}();



/***/ }),

/***/ "./js/pages/import/ImportPage.js":
/*!***************************************!*\
  !*** ./js/pages/import/ImportPage.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ImportPage; });
/* harmony import */ var _FormFieldToggle__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./FormFieldToggle */ "./js/pages/import/FormFieldToggle.js");
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

var ImportPage =
/*#__PURE__*/
function () {
  function ImportPage() {
    var _this = this;

    _classCallCheck(this, ImportPage);

    new _FormFieldToggle__WEBPACK_IMPORTED_MODULE_0__["default"]();
    $('.js-from-files-history-btn').on('click', function () {
      return _this.showFilesHistoryHandler();
    });
    $('.js-close-files-history-block-btn').on('click', function () {
      return _this.closeFilesHistoryHandler();
    });
    $('#fileHistoryTable').on('click', '.js-use-file-btn', function (event) {
      return _this.useFileFromFilesHistory(event);
    });
    $('.js-change-import-file-btn').on('click', function () {
      return _this.changeImportFileHandler();
    });
    $('.js-import-file').on('change', function () {
      return _this.uploadFile();
    });
    this.toggleSelectedFile();
    this.handleSubmit();
  }
  /**
   * Handle submit and add confirm box in case the toggle button about
   * deleting all entities before import is checked
   */


  _createClass(ImportPage, [{
    key: "handleSubmit",
    value: function handleSubmit() {
      $('.js-import-form').on('submit', function () {
        var $this = $(this);

        if ($this.find('input[name="truncate"]:checked').val() === '1') {
          return confirm("".concat($this.data('delete-confirm-message'), " ").concat($.trim($('#entity > option:selected').text().toLowerCase()), "?"));
        }
      });
    }
    /**
     * Check if selected file names exists and if so, then display it
     */

  }, {
    key: "toggleSelectedFile",
    value: function toggleSelectedFile() {
      var selectFilename = $('#csv').val();

      if (selectFilename.length > 0) {
        this.showImportFileAlert(selectFilename);
        this.hideFileUploadBlock();
      }
    }
  }, {
    key: "changeImportFileHandler",
    value: function changeImportFileHandler() {
      this.hideImportFileAlert();
      this.showFileUploadBlock();
    }
    /**
     * Show files history event handler
     */

  }, {
    key: "showFilesHistoryHandler",
    value: function showFilesHistoryHandler() {
      this.showFilesHistory();
      this.hideFileUploadBlock();
    }
    /**
     * Close files history event handler
     */

  }, {
    key: "closeFilesHistoryHandler",
    value: function closeFilesHistoryHandler() {
      this.closeFilesHistory();
      this.showFileUploadBlock();
    }
    /**
     * Show files history block
     */

  }, {
    key: "showFilesHistory",
    value: function showFilesHistory() {
      $('.js-files-history-block').removeClass('d-none');
    }
    /**
     * Hide files history block
     */

  }, {
    key: "closeFilesHistory",
    value: function closeFilesHistory() {
      $('.js-files-history-block').addClass('d-none');
    }
    /**
     *  Prefill hidden file input with selected file name from history
     */

  }, {
    key: "useFileFromFilesHistory",
    value: function useFileFromFilesHistory(event) {
      var filename = $(event.target).closest('.btn-group').data('file');
      $('.js-import-file-input').val(filename);
      this.showImportFileAlert(filename);
      this.closeFilesHistory();
    }
    /**
     * Show alert with imported file name
     */

  }, {
    key: "showImportFileAlert",
    value: function showImportFileAlert(filename) {
      $('.js-import-file-alert').removeClass('d-none');
      $('.js-import-file').text(filename);
    }
    /**
     * Hides selected import file alert
     */

  }, {
    key: "hideImportFileAlert",
    value: function hideImportFileAlert() {
      $('.js-import-file-alert').addClass('d-none');
    }
    /**
     * Hides import file upload block
     */

  }, {
    key: "hideFileUploadBlock",
    value: function hideFileUploadBlock() {
      $('.js-file-upload-form-group').addClass('d-none');
    }
    /**
     * Hides import file upload block
     */

  }, {
    key: "showFileUploadBlock",
    value: function showFileUploadBlock() {
      $('.js-file-upload-form-group').removeClass('d-none');
    }
    /**
     * Make file history button clickable
     */

  }, {
    key: "enableFilesHistoryBtn",
    value: function enableFilesHistoryBtn() {
      $('.js-from-files-history-btn').removeAttr('disabled');
    }
    /**
     * Show error message if file uploading failed
     *
     * @param {string} fileName
     * @param {integer} fileSize
     * @param {string} message
     */

  }, {
    key: "showImportFileError",
    value: function showImportFileError(fileName, fileSize, message) {
      var $alert = $('.js-import-file-error');
      var fileData = fileName + ' (' + this.humanizeSize(fileSize) + ')';
      $alert.find('.js-file-data').html(fileData);
      $alert.find('.js-error-message').html(message);
      $alert.removeClass('d-none');
    }
    /**
     * Hide file uploading error
     */

  }, {
    key: "hideImportFileError",
    value: function hideImportFileError() {
      var $alert = $('.js-import-file-error');
      $alert.addClass('d-none');
    }
    /**
     * Show file size in human readable format
     *
     * @param {int} bytes
     *
     * @returns {string}
     */

  }, {
    key: "humanizeSize",
    value: function humanizeSize(bytes) {
      if (typeof bytes !== 'number') {
        return '';
      }

      if (bytes >= 1000000000) {
        return (bytes / 1000000000).toFixed(2) + ' GB';
      }

      if (bytes >= 1000000) {
        return (bytes / 1000000).toFixed(2) + ' MB';
      }

      return (bytes / 1000).toFixed(2) + ' KB';
    }
    /**
     * Upload selected import file
     */

  }, {
    key: "uploadFile",
    value: function uploadFile() {
      var _this2 = this;

      this.hideImportFileError();
      var $input = $('#file');
      var uploadedFile = $input.prop('files')[0];
      var maxUploadSize = $input.data('max-file-upload-size');

      if (maxUploadSize < uploadedFile.size) {
        this.showImportFileError(uploadedFile.name, uploadedFile.size, 'File is too large');
        return;
      }

      var data = new FormData();
      data.append('file', uploadedFile);
      $.ajax({
        type: 'POST',
        url: $('.js-import-form').data('file-upload-url'),
        data: data,
        cache: false,
        contentType: false,
        processData: false
      }).then(function (response) {
        if (response.error) {
          _this2.showImportFileError(uploadedFile.name, uploadedFile.size, response.error);

          return;
        }

        var filename = response.file.name;
        $('.js-import-file-input').val(filename);

        _this2.showImportFileAlert(filename);

        _this2.hideFileUploadBlock();

        _this2.addFileToHistoryTable(filename);

        _this2.enableFilesHistoryBtn();
      });
    }
    /**
     * Renders new row in files history table
     *
     * @param {string} filename
     */

  }, {
    key: "addFileToHistoryTable",
    value: function addFileToHistoryTable(filename) {
      var $table = $('#fileHistoryTable');
      var baseDeleteUrl = $table.data('delete-file-url');
      var deleteUrl = baseDeleteUrl + '&filename=' + encodeURIComponent(filename);
      var baseDownloadUrl = $table.data('download-file-url');
      var downloadUrl = baseDownloadUrl + '&filename=' + encodeURIComponent(filename);
      var $template = $table.find('tr:first').clone();
      $template.removeClass('d-none');
      $template.find('td:first').text(filename);
      $template.find('.btn-group').attr('data-file', filename);
      $template.find('.js-delete-file-btn').attr('href', deleteUrl);
      $template.find('.js-download-file-btn').attr('href', downloadUrl);
      $table.find('tbody').append($template);
      var filesNumber = $table.find('tr').length - 1;
      $('.js-files-history-number').text(filesNumber);
    }
  }]);

  return ImportPage;
}();



/***/ }),

/***/ "./js/pages/import/index.js":
/*!**********************************!*\
  !*** ./js/pages/import/index.js ***!
  \**********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ImportPage__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ImportPage */ "./js/pages/import/ImportPage.js");
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
  new _ImportPage__WEBPACK_IMPORTED_MODULE_0__["default"]();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvaW1wb3J0L0Zvcm1GaWVsZFRvZ2dsZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9pbXBvcnQvSW1wb3J0UGFnZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9pbXBvcnQvaW5kZXguanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsImVudGl0eUNhdGVnb3JpZXMiLCJlbnRpdHlQcm9kdWN0cyIsImVudGl0eUNvbWJpbmF0aW9ucyIsImVudGl0eUN1c3RvbWVycyIsImVudGl0eUFkZHJlc3NlcyIsImVudGl0eUJyYW5kcyIsImVudGl0eVN1cHBsaWVycyIsImVudGl0eUFsaWFzIiwiZW50aXR5U3RvcmVDb250YWN0cyIsIkZvcm1GaWVsZFRvZ2dsZSIsIm9uIiwidG9nZ2xlRm9ybSIsInNlbGVjdGVkT3B0aW9uIiwiZmluZCIsInNlbGVjdGVkRW50aXR5IiwicGFyc2VJbnQiLCJ2YWwiLCJlbnRpdHlOYW1lIiwidGV4dCIsInRvTG93ZXJDYXNlIiwidG9nZ2xlRW50aXR5QWxlcnQiLCJ0b2dnbGVGaWVsZHMiLCJsb2FkQXZhaWxhYmxlRmllbGRzIiwiJGFsZXJ0IiwiaW5jbHVkZXMiLCJzaG93IiwiaGlkZSIsIiR0cnVuY2F0ZUZvcm1Hcm91cCIsIiRtYXRjaFJlZkZvcm1Hcm91cCIsIiRyZWdlbmVyYXRlRm9ybUdyb3VwIiwiJGZvcmNlSWRzRm9ybUdyb3VwIiwiJGVudGl0eU5hbWVQbGFjZWhvbGRlciIsImh0bWwiLCJlbnRpdHkiLCIkYXZhaWxhYmxlRmllbGRzIiwiYWpheCIsInVybCIsImRhdGEiLCJkYXRhVHlwZSIsInRoZW4iLCJyZXNwb25zZSIsIl9yZW1vdmVBdmFpbGFibGVGaWVsZHMiLCJpIiwibGVuZ3RoIiwiX2FwcGVuZEF2YWlsYWJsZUZpZWxkIiwibGFiZWwiLCJyZXF1aXJlZCIsImRlc2NyaXB0aW9uIiwicG9wb3ZlciIsIiRjb250YWluZXIiLCJlbXB0eSIsIiRmaWVsZCIsImhlbHBCb3hDb250ZW50IiwiJGhlbHBCb3giLCJjbG9uZSIsImF0dHIiLCJyZW1vdmVDbGFzcyIsImFwcGVuZCIsIiRhcHBlbmRUbyIsImZpZWxkVGV4dCIsIl9hcHBlbmRIZWxwQm94IiwiYXBwZW5kVG8iLCJJbXBvcnRQYWdlIiwic2hvd0ZpbGVzSGlzdG9yeUhhbmRsZXIiLCJjbG9zZUZpbGVzSGlzdG9yeUhhbmRsZXIiLCJldmVudCIsInVzZUZpbGVGcm9tRmlsZXNIaXN0b3J5IiwiY2hhbmdlSW1wb3J0RmlsZUhhbmRsZXIiLCJ1cGxvYWRGaWxlIiwidG9nZ2xlU2VsZWN0ZWRGaWxlIiwiaGFuZGxlU3VibWl0IiwiJHRoaXMiLCJjb25maXJtIiwidHJpbSIsInNlbGVjdEZpbGVuYW1lIiwic2hvd0ltcG9ydEZpbGVBbGVydCIsImhpZGVGaWxlVXBsb2FkQmxvY2siLCJoaWRlSW1wb3J0RmlsZUFsZXJ0Iiwic2hvd0ZpbGVVcGxvYWRCbG9jayIsInNob3dGaWxlc0hpc3RvcnkiLCJjbG9zZUZpbGVzSGlzdG9yeSIsImFkZENsYXNzIiwiZmlsZW5hbWUiLCJ0YXJnZXQiLCJjbG9zZXN0IiwicmVtb3ZlQXR0ciIsImZpbGVOYW1lIiwiZmlsZVNpemUiLCJtZXNzYWdlIiwiZmlsZURhdGEiLCJodW1hbml6ZVNpemUiLCJieXRlcyIsInRvRml4ZWQiLCJoaWRlSW1wb3J0RmlsZUVycm9yIiwiJGlucHV0IiwidXBsb2FkZWRGaWxlIiwicHJvcCIsIm1heFVwbG9hZFNpemUiLCJzaXplIiwic2hvd0ltcG9ydEZpbGVFcnJvciIsIm5hbWUiLCJGb3JtRGF0YSIsInR5cGUiLCJjYWNoZSIsImNvbnRlbnRUeXBlIiwicHJvY2Vzc0RhdGEiLCJlcnJvciIsImZpbGUiLCJhZGRGaWxlVG9IaXN0b3J5VGFibGUiLCJlbmFibGVGaWxlc0hpc3RvcnlCdG4iLCIkdGFibGUiLCJiYXNlRGVsZXRlVXJsIiwiZGVsZXRlVXJsIiwiZW5jb2RlVVJJQ29tcG9uZW50IiwiYmFz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QU12SCxDQUFDLEdBQUdDLE1BQU0sQ0FBQ0QsQ0FBakI7QUFFQUEsQ0FBQyxDQUFDLFlBQU07QUFDTixNQUFJZ0UsbURBQUo7QUFDRCxDQUZBLENBQUQsQyIsImZpbGUiOiJpbXBvcnRzLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGdldHRlciB9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuIFx0XHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcbiBcdFx0fVxuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xuIFx0fTtcblxuIFx0Ly8gY3JlYXRlIGEgZmFrZSBuYW1lc3BhY2Ugb2JqZWN0XG4gXHQvLyBtb2RlICYgMTogdmFsdWUgaXMgYSBtb2R1bGUgaWQsIHJlcXVpcmUgaXRcbiBcdC8vIG1vZGUgJiAyOiBtZXJnZSBhbGwgcHJvcGVydGllcyBvZiB2YWx1ZSBpbnRvIHRoZSBuc1xuIFx0Ly8gbW9kZSAmIDQ6IHJldHVybiB2YWx1ZSB3aGVuIGFscmVhZHkgbnMgb2JqZWN0XG4gXHQvLyBtb2RlICYgOHwxOiBiZWhhdmUgbGlrZSByZXF1aXJlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnQgPSBmdW5jdGlvbih2YWx1ZSwgbW9kZSkge1xuIFx0XHRpZihtb2RlICYgMSkgdmFsdWUgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKHZhbHVlKTtcbiBcdFx0aWYobW9kZSAmIDgpIHJldHVybiB2YWx1ZTtcbiBcdFx0aWYoKG1vZGUgJiA0KSAmJiB0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnICYmIHZhbHVlICYmIHZhbHVlLl9fZXNNb2R1bGUpIHJldHVybiB2YWx1ZTtcbiBcdFx0dmFyIG5zID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yKG5zKTtcbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KG5zLCAnZGVmYXVsdCcsIHsgZW51bWVyYWJsZTogdHJ1ZSwgdmFsdWU6IHZhbHVlIH0pO1xuIFx0XHRpZihtb2RlICYgMiAmJiB0eXBlb2YgdmFsdWUgIT0gJ3N0cmluZycpIGZvcih2YXIga2V5IGluIHZhbHVlKSBfX3dlYnBhY2tfcmVxdWlyZV9fLmQobnMsIGtleSwgZnVuY3Rpb24oa2V5KSB7IHJldHVybiB2YWx1ZVtrZXldOyB9LmJpbmQobnVsbCwga2V5KSk7XG4gXHRcdHJldHVybiBucztcbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiL2FkbWluLWRldi90aGVtZXMvbmV3LXRoZW1lL3B1YmxpYy9cIjtcblxuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IFwiLi9qcy9wYWdlcy9pbXBvcnQvaW5kZXguanNcIik7XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmNvbnN0IGVudGl0eUNhdGVnb3JpZXMgPSAwO1xuY29uc3QgZW50aXR5UHJvZHVjdHMgPSAxO1xuY29uc3QgZW50aXR5Q29tYmluYXRpb25zID0gMjtcbmNvbnN0IGVudGl0eUN1c3RvbWVycyA9IDM7XG5jb25zdCBlbnRpdHlBZGRyZXNzZXMgPSA0O1xuY29uc3QgZW50aXR5QnJhbmRzID0gNTtcbmNvbnN0IGVudGl0eVN1cHBsaWVycyA9IDY7XG5jb25zdCBlbnRpdHlBbGlhcyA9IDc7XG5jb25zdCBlbnRpdHlTdG9yZUNvbnRhY3RzID0gODtcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRm9ybUZpZWxkVG9nZ2xlIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgJCgnLmpzLWVudGl0eS1zZWxlY3QnKS5vbignY2hhbmdlJywgKCkgPT4gdGhpcy50b2dnbGVGb3JtKCkpO1xuXG4gICAgdGhpcy50b2dnbGVGb3JtKCk7XG4gIH1cblxuICB0b2dnbGVGb3JtKCkge1xuICAgIGxldCBzZWxlY3RlZE9wdGlvbiA9ICQoJyNlbnRpdHknKS5maW5kKCdvcHRpb246c2VsZWN0ZWQnKTtcbiAgICBsZXQgc2VsZWN0ZWRFbnRpdHkgPSBwYXJzZUludChzZWxlY3RlZE9wdGlvbi52YWwoKSk7XG4gICAgbGV0IGVudGl0eU5hbWUgPSBzZWxlY3RlZE9wdGlvbi50ZXh0KCkudG9Mb3dlckNhc2UoKTtcblxuICAgIHRoaXMudG9nZ2xlRW50aXR5QWxlcnQoc2VsZWN0ZWRFbnRpdHkpO1xuICAgIHRoaXMudG9nZ2xlRmllbGRzKHNlbGVjdGVkRW50aXR5LCBlbnRpdHlOYW1lKTtcbiAgICB0aGlzLmxvYWRBdmFpbGFibGVGaWVsZHMoc2VsZWN0ZWRFbnRpdHkpO1xuICB9XG5cbiAgLyoqXG4gICAqIFRvZ2dsZSBhbGVydCB3YXJuaW5nIGZvciBzZWxlY3RlZCBpbXBvcnQgZW50aXR5XG4gICAqXG4gICAqIEBwYXJhbSB7aW50fSBzZWxlY3RlZEVudGl0eVxuICAgKi9cbiAgdG9nZ2xlRW50aXR5QWxlcnQoc2VsZWN0ZWRFbnRpdHkpIHtcbiAgICBsZXQgJGFsZXJ0ID0gJCgnLmpzLWVudGl0eS1hbGVydCcpO1xuXG4gICAgaWYgKFtlbnRpdHlDYXRlZ29yaWVzLCBlbnRpdHlQcm9kdWN0c10uaW5jbHVkZXMoc2VsZWN0ZWRFbnRpdHkpKSB7XG4gICAgICAkYWxlcnQuc2hvdygpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkYWxlcnQuaGlkZSgpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBUb2dnbGUgYXZhaWxhYmxlIG9wdGlvbnMgZm9yIHNlbGVjdGVkIGVudGl0eVxuICAgKlxuICAgKiBAcGFyYW0ge2ludH0gc2VsZWN0ZWRFbnRpdHlcbiAgICogQHBhcmFtIHtzdHJpbmd9IGVudGl0eU5hbWVcbiAgICovXG4gIHRvZ2dsZUZpZWxkcyhzZWxlY3RlZEVudGl0eSwgZW50aXR5TmFtZSkge1xuICAgIGNvbnN0ICR0cnVuY2F0ZUZvcm1Hcm91cCA9ICQoJy5qcy10cnVuY2F0ZS1mb3JtLWdyb3VwJyk7XG4gICAgY29uc3QgJG1hdGNoUmVmRm9ybUdyb3VwID0gJCgnLmpzLW1hdGNoLXJlZi1mb3JtLWdyb3VwJyk7XG4gICAgY29uc3QgJHJlZ2VuZXJhdGVGb3JtR3JvdXAgPSAkKCcuanMtcmVnZW5lcmF0ZS1mb3JtLWdyb3VwJyk7XG4gICAgY29uc3QgJGZvcmNlSWRzRm9ybUdyb3VwID0gJCgnLmpzLWZvcmNlLWlkcy1mb3JtLWdyb3VwJyk7XG4gICAgY29uc3QgJGVudGl0eU5hbWVQbGFjZWhvbGRlciA9ICQoJy5qcy1lbnRpdHktbmFtZScpO1xuXG4gICAgaWYgKGVudGl0eVN0b3JlQ29udGFjdHMgPT09IHNlbGVjdGVkRW50aXR5KSB7XG4gICAgICAkdHJ1bmNhdGVGb3JtR3JvdXAuaGlkZSgpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkdHJ1bmNhdGVGb3JtR3JvdXAuc2hvdygpO1xuICAgIH1cblxuICAgIGlmIChbZW50aXR5UHJvZHVjdHMsIGVudGl0eUNvbWJpbmF0aW9uc10uaW5jbHVkZXMoc2VsZWN0ZWRFbnRpdHkpKSB7XG4gICAgICAkbWF0Y2hSZWZGb3JtR3JvdXAuc2hvdygpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkbWF0Y2hSZWZGb3JtR3JvdXAuaGlkZSgpO1xuICAgIH1cblxuICAgIGlmIChbXG4gICAgICBlbnRpdHlDYXRlZ29yaWVzLFxuICAgICAgZW50aXR5UHJvZHVjdHMsXG4gICAgICBlbnRpdHlCcmFuZHMsXG4gICAgICBlbnRpdHlTdXBwbGllcnMsXG4gICAgICBlbnRpdHlTdG9yZUNvbnRhY3RzXG4gICAgXS5pbmNsdWRlcyhzZWxlY3RlZEVudGl0eSlcbiAgICApIHtcbiAgICAgICRyZWdlbmVyYXRlRm9ybUdyb3VwLnNob3coKTtcbiAgICB9IGVsc2Uge1xuICAgICAgJHJlZ2VuZXJhdGVGb3JtR3JvdXAuaGlkZSgpO1xuICAgIH1cblxuICAgIGlmIChbXG4gICAgICBlbnRpdHlDYXRlZ29yaWVzLFxuICAgICAgZW50aXR5UHJvZHVjdHMsXG4gICAgICBlbnRpdHlDdXN0b21lcnMsXG4gICAgICBlbnRpdHlBZGRyZXNzZXMsXG4gICAgICBlbnRpdHlCcmFuZHMsXG4gICAgICBlbnRpdHlTdXBwbGllcnMsXG4gICAgICBlbnRpdHlTdG9yZUNvbnRhY3RzLFxuICAgICAgZW50aXR5QWxpYXNcbiAgICBdLmluY2x1ZGVzKHNlbGVjdGVkRW50aXR5KVxuICAgICkge1xuICAgICAgJGZvcmNlSWRzRm9ybUdyb3VwLnNob3coKTtcbiAgICB9IGVsc2Uge1xuICAgICAgJGZvcmNlSWRzRm9ybUdyb3VwLmhpZGUoKTtcbiAgICB9XG5cbiAgICAkZW50aXR5TmFtZVBsYWNlaG9sZGVyLmh0bWwoZW50aXR5TmFtZSk7XG4gIH1cblxuICAvKipcbiAgICogTG9hZCBhdmFpbGFibGUgZmllbGRzIGZvciBnaXZlbiBlbnRpdHlcbiAgICpcbiAgICogQHBhcmFtIHtpbnR9IGVudGl0eVxuICAgKi9cbiAgbG9hZEF2YWlsYWJsZUZpZWxkcyhlbnRpdHkpIHtcbiAgICBjb25zdCAkYXZhaWxhYmxlRmllbGRzID0gJCgnLmpzLWF2YWlsYWJsZS1maWVsZHMnKTtcblxuICAgICQuYWpheCh7XG4gICAgICB1cmw6ICRhdmFpbGFibGVGaWVsZHMuZGF0YSgndXJsJyksXG4gICAgICBkYXRhOiB7XG4gICAgICAgIGVudGl0eTogZW50aXR5XG4gICAgICB9LFxuICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICB9KS50aGVuKHJlc3BvbnNlID0+IHtcbiAgICAgIHRoaXMuX3JlbW92ZUF2YWlsYWJsZUZpZWxkcygkYXZhaWxhYmxlRmllbGRzKTtcblxuICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCByZXNwb25zZS5sZW5ndGg7IGkrKykge1xuICAgICAgICB0aGlzLl9hcHBlbmRBdmFpbGFibGVGaWVsZChcbiAgICAgICAgICAkYXZhaWxhYmxlRmllbGRzLFxuICAgICAgICAgIHJlc3BvbnNlW2ldLmxhYmVsICsgKHJlc3BvbnNlW2ldLnJlcXVpcmVkID8gJyonIDogJycpLFxuICAgICAgICAgIHJlc3BvbnNlW2ldLmRlc2NyaXB0aW9uXG4gICAgICAgICk7XG4gICAgICB9XG5cbiAgICAgICRhdmFpbGFibGVGaWVsZHMuZmluZCgnW2RhdGEtdG9nZ2xlPVwicG9wb3ZlclwiXScpLnBvcG92ZXIoKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW1vdmUgYXZhaWxhYmxlIGZpZWxkcyBjb250ZW50IGZyb20gZ2l2ZW4gY29udGFpbmVyLlxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGNvbnRhaW5lclxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbW92ZUF2YWlsYWJsZUZpZWxkcygkY29udGFpbmVyKSB7XG4gICAgJGNvbnRhaW5lci5maW5kKCdbZGF0YS10b2dnbGU9XCJwb3BvdmVyXCJdJykucG9wb3ZlcignaGlkZScpO1xuICAgICRjb250YWluZXIuZW1wdHkoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBcHBlbmQgYSBoZWxwIGJveCB0byBnaXZlbiBmaWVsZC5cbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRmaWVsZFxuICAgKiBAcGFyYW0ge1N0cmluZ30gaGVscEJveENvbnRlbnRcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9hcHBlbmRIZWxwQm94KCRmaWVsZCwgaGVscEJveENvbnRlbnQpIHtcbiAgICBsZXQgJGhlbHBCb3ggPSAkKCcuanMtYXZhaWxhYmxlLWZpZWxkLXBvcG92ZXItdGVtcGxhdGUnKS5jbG9uZSgpO1xuXG4gICAgJGhlbHBCb3guYXR0cignZGF0YS1jb250ZW50JywgaGVscEJveENvbnRlbnQpO1xuICAgICRoZWxwQm94LnJlbW92ZUNsYXNzKCdqcy1hdmFpbGFibGUtZmllbGQtcG9wb3Zlci10ZW1wbGF0ZSBkLW5vbmUnKTtcbiAgICAkZmllbGQuYXBwZW5kKCRoZWxwQm94KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBcHBlbmQgYXZhaWxhYmxlIGZpZWxkIHRvIGdpdmVuIGNvbnRhaW5lci5cbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRhcHBlbmRUbyBmaWVsZCB3aWxsIGJlIGFwcGVuZGVkIHRvIHRoaXMgY29udGFpbmVyLlxuICAgKiBAcGFyYW0ge1N0cmluZ30gZmllbGRUZXh0XG4gICAqIEBwYXJhbSB7U3RyaW5nfSBoZWxwQm94Q29udGVudFxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2FwcGVuZEF2YWlsYWJsZUZpZWxkKCRhcHBlbmRUbywgZmllbGRUZXh0LCBoZWxwQm94Q29udGVudCkge1xuICAgIGxldCAkZmllbGQgPSAkKCcuanMtYXZhaWxhYmxlLWZpZWxkLXRlbXBsYXRlJykuY2xvbmUoKTtcblxuICAgICRmaWVsZC50ZXh0KGZpZWxkVGV4dCk7XG5cbiAgICBpZiAoaGVscEJveENvbnRlbnQpIHtcbiAgICAgIC8vIEFwcGVuZCBoZWxwIGJveCBuZXh0IHRvIHRoZSBmaWVsZFxuICAgICAgdGhpcy5fYXBwZW5kSGVscEJveCgkZmllbGQsIGhlbHBCb3hDb250ZW50KTtcbiAgICB9XG5cbiAgICAkZmllbGQucmVtb3ZlQ2xhc3MoJ2pzLWF2YWlsYWJsZS1maWVsZC10ZW1wbGF0ZSBkLW5vbmUnKTtcbiAgICAkZmllbGQuYXBwZW5kVG8oJGFwcGVuZFRvKTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IEZvcm1GaWVsZFRvZ2dsZSBmcm9tIFwiLi9Gb3JtRmllbGRUb2dnbGVcIjtcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBJbXBvcnRQYWdlIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgbmV3IEZvcm1GaWVsZFRvZ2dsZSgpO1xuXG4gICAgJCgnLmpzLWZyb20tZmlsZXMtaGlzdG9yeS1idG4nKS5vbignY2xpY2snLCAoKSA9PiB0aGlzLnNob3dGaWxlc0hpc3RvcnlIYW5kbGVyKCkpO1xuICAgICQoJy5qcy1jbG9zZS1maWxlcy1oaXN0b3J5LWJsb2NrLWJ0bicpLm9uKCdjbGljaycsICgpID0+IHRoaXMuY2xvc2VGaWxlc0hpc3RvcnlIYW5kbGVyKCkpO1xuICAgICQoJyNmaWxlSGlzdG9yeVRhYmxlJykub24oJ2NsaWNrJywgJy5qcy11c2UtZmlsZS1idG4nLCAoZXZlbnQpID0+IHRoaXMudXNlRmlsZUZyb21GaWxlc0hpc3RvcnkoZXZlbnQpKTtcbiAgICAkKCcuanMtY2hhbmdlLWltcG9ydC1maWxlLWJ0bicpLm9uKCdjbGljaycsICgpID0+IHRoaXMuY2hhbmdlSW1wb3J0RmlsZUhhbmRsZXIoKSk7XG4gICAgJCgnLmpzLWltcG9ydC1maWxlJykub24oJ2NoYW5nZScsICgpID0+IHRoaXMudXBsb2FkRmlsZSgpKTtcblxuICAgIHRoaXMudG9nZ2xlU2VsZWN0ZWRGaWxlKCk7XG4gICAgdGhpcy5oYW5kbGVTdWJtaXQoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgc3VibWl0IGFuZCBhZGQgY29uZmlybSBib3ggaW4gY2FzZSB0aGUgdG9nZ2xlIGJ1dHRvbiBhYm91dFxuICAgKiBkZWxldGluZyBhbGwgZW50aXRpZXMgYmVmb3JlIGltcG9ydCBpcyBjaGVja2VkXG4gICAqL1xuICBoYW5kbGVTdWJtaXQoKSB7XG4gICAgJCgnLmpzLWltcG9ydC1mb3JtJykub24oJ3N1Ym1pdCcsIGZ1bmN0aW9uKCkge1xuICAgICAgY29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgaWYgKCR0aGlzLmZpbmQoJ2lucHV0W25hbWU9XCJ0cnVuY2F0ZVwiXTpjaGVja2VkJykudmFsKCkgPT09ICcxJykge1xuICAgICAgICByZXR1cm4gY29uZmlybShgJHskdGhpcy5kYXRhKCdkZWxldGUtY29uZmlybS1tZXNzYWdlJyl9ICR7JC50cmltKCQoJyNlbnRpdHkgPiBvcHRpb246c2VsZWN0ZWQnKS50ZXh0KCkudG9Mb3dlckNhc2UoKSl9P2ApO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIHNlbGVjdGVkIGZpbGUgbmFtZXMgZXhpc3RzIGFuZCBpZiBzbywgdGhlbiBkaXNwbGF5IGl0XG4gICAqL1xuICB0b2dnbGVTZWxlY3RlZEZpbGUoKSB7XG4gICAgbGV0IHNlbGVjdEZpbGVuYW1lID0gJCgnI2NzdicpLnZhbCgpO1xuICAgIGlmIChzZWxlY3RGaWxlbmFtZS5sZW5ndGggPiAwKSB7XG4gICAgICB0aGlzLnNob3dJbXBvcnRGaWxlQWxlcnQoc2VsZWN0RmlsZW5hbWUpO1xuICAgICAgdGhpcy5oaWRlRmlsZVVwbG9hZEJsb2NrKCk7XG4gICAgfVxuICB9XG5cbiAgY2hhbmdlSW1wb3J0RmlsZUhhbmRsZXIoKSB7XG4gICAgdGhpcy5oaWRlSW1wb3J0RmlsZUFsZXJ0KCk7XG4gICAgdGhpcy5zaG93RmlsZVVwbG9hZEJsb2NrKCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvdyBmaWxlcyBoaXN0b3J5IGV2ZW50IGhhbmRsZXJcbiAgICovXG4gIHNob3dGaWxlc0hpc3RvcnlIYW5kbGVyKCkge1xuICAgIHRoaXMuc2hvd0ZpbGVzSGlzdG9yeSgpO1xuICAgIHRoaXMuaGlkZUZpbGVVcGxvYWRCbG9jaygpO1xuICB9XG5cbiAgLyoqXG4gICAqIENsb3NlIGZpbGVzIGhpc3RvcnkgZXZlbnQgaGFuZGxlclxuICAgKi9cbiAgY2xvc2VGaWxlc0hpc3RvcnlIYW5kbGVyKCkge1xuICAgIHRoaXMuY2xvc2VGaWxlc0hpc3RvcnkoKTtcbiAgICB0aGlzLnNob3dGaWxlVXBsb2FkQmxvY2soKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IGZpbGVzIGhpc3RvcnkgYmxvY2tcbiAgICovXG4gIHNob3dGaWxlc0hpc3RvcnkoKSB7XG4gICAgJCgnLmpzLWZpbGVzLWhpc3RvcnktYmxvY2snKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZSBmaWxlcyBoaXN0b3J5IGJsb2NrXG4gICAqL1xuICBjbG9zZUZpbGVzSGlzdG9yeSgpIHtcbiAgICAkKCcuanMtZmlsZXMtaGlzdG9yeS1ibG9jaycpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiAgUHJlZmlsbCBoaWRkZW4gZmlsZSBpbnB1dCB3aXRoIHNlbGVjdGVkIGZpbGUgbmFtZSBmcm9tIGhpc3RvcnlcbiAgICovXG4gIHVzZUZpbGVGcm9tRmlsZXNIaXN0b3J5KGV2ZW50KSB7XG4gICAgbGV0IGZpbGVuYW1lID0gJChldmVudC50YXJnZXQpLmNsb3Nlc3QoJy5idG4tZ3JvdXAnKS5kYXRhKCdmaWxlJyk7XG5cbiAgICAkKCcuanMtaW1wb3J0LWZpbGUtaW5wdXQnKS52YWwoZmlsZW5hbWUpO1xuXG4gICAgdGhpcy5zaG93SW1wb3J0RmlsZUFsZXJ0KGZpbGVuYW1lKTtcbiAgICB0aGlzLmNsb3NlRmlsZXNIaXN0b3J5KCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvdyBhbGVydCB3aXRoIGltcG9ydGVkIGZpbGUgbmFtZVxuICAgKi9cbiAgc2hvd0ltcG9ydEZpbGVBbGVydChmaWxlbmFtZSkge1xuICAgICQoJy5qcy1pbXBvcnQtZmlsZS1hbGVydCcpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAkKCcuanMtaW1wb3J0LWZpbGUnKS50ZXh0KGZpbGVuYW1lKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyBzZWxlY3RlZCBpbXBvcnQgZmlsZSBhbGVydFxuICAgKi9cbiAgaGlkZUltcG9ydEZpbGVBbGVydCgpIHtcbiAgICAkKCcuanMtaW1wb3J0LWZpbGUtYWxlcnQnKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgaW1wb3J0IGZpbGUgdXBsb2FkIGJsb2NrXG4gICAqL1xuICBoaWRlRmlsZVVwbG9hZEJsb2NrKCkge1xuICAgICQoJy5qcy1maWxlLXVwbG9hZC1mb3JtLWdyb3VwJykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIGltcG9ydCBmaWxlIHVwbG9hZCBibG9ja1xuICAgKi9cbiAgc2hvd0ZpbGVVcGxvYWRCbG9jaygpIHtcbiAgICAkKCcuanMtZmlsZS11cGxvYWQtZm9ybS1ncm91cCcpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBNYWtlIGZpbGUgaGlzdG9yeSBidXR0b24gY2xpY2thYmxlXG4gICAqL1xuICBlbmFibGVGaWxlc0hpc3RvcnlCdG4oKSB7XG4gICAgJCgnLmpzLWZyb20tZmlsZXMtaGlzdG9yeS1idG4nKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3cgZXJyb3IgbWVzc2FnZSBpZiBmaWxlIHVwbG9hZGluZyBmYWlsZWRcbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IGZpbGVOYW1lXG4gICAqIEBwYXJhbSB7aW50ZWdlcn0gZmlsZVNpemVcbiAgICogQHBhcmFtIHtzdHJpbmd9IG1lc3NhZ2VcbiAgICovXG4gIHNob3dJbXBvcnRGaWxlRXJyb3IoZmlsZU5hbWUsIGZpbGVTaXplLCBtZXNzYWdlKSB7XG4gICAgY29uc3QgJGFsZXJ0ID0gJCgnLmpzLWltcG9ydC1maWxlLWVycm9yJyk7XG5cbiAgICBjb25zdCBmaWxlRGF0YSA9IGZpbGVOYW1lICsgJyAoJyArIHRoaXMuaHVtYW5pemVTaXplKGZpbGVTaXplKSArICcpJztcblxuICAgICRhbGVydC5maW5kKCcuanMtZmlsZS1kYXRhJykuaHRtbChmaWxlRGF0YSk7XG4gICAgJGFsZXJ0LmZpbmQoJy5qcy1lcnJvci1tZXNzYWdlJykuaHRtbChtZXNzYWdlKTtcbiAgICAkYWxlcnQucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGUgZmlsZSB1cGxvYWRpbmcgZXJyb3JcbiAgICovXG4gIGhpZGVJbXBvcnRGaWxlRXJyb3IoKSB7XG4gICAgY29uc3QgJGFsZXJ0ID0gJCgnLmpzLWltcG9ydC1maWxlLWVycm9yJyk7XG4gICAgJGFsZXJ0LmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IGZpbGUgc2l6ZSBpbiBodW1hbiByZWFkYWJsZSBmb3JtYXRcbiAgICpcbiAgICogQHBhcmFtIHtpbnR9IGJ5dGVzXG4gICAqXG4gICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAqL1xuICBodW1hbml6ZVNpemUoYnl0ZXMpIHtcbiAgICBpZiAodHlwZW9mIGJ5dGVzICE9PSAnbnVtYmVyJykge1xuICAgICAgcmV0dXJuICcnO1xuICAgIH1cblxuICAgIGlmIChieXRlcyA+PSAxMDAwMDAwMDAwKSB7XG4gICAgICByZXR1cm4gKGJ5dGVzIC8gMTAwMDAwMDAwMCkudG9GaXhlZCgyKSArICcgR0InO1xuICAgIH1cblxuICAgIGlmIChieXRlcyA+PSAxMDAwMDAwKSB7XG4gICAgICByZXR1cm4gKGJ5dGVzIC8gMTAwMDAwMCkudG9GaXhlZCgyKSArICcgTUInO1xuICAgIH1cblxuICAgIHJldHVybiAoYnl0ZXMgLyAxMDAwKS50b0ZpeGVkKDIpICsgJyBLQic7XG4gIH1cblxuICAvKipcbiAgICogVXBsb2FkIHNlbGVjdGVkIGltcG9ydCBmaWxlXG4gICAqL1xuICB1cGxvYWRGaWxlKCkge1xuICAgIHRoaXMuaGlkZUltcG9ydEZpbGVFcnJvcigpO1xuXG4gICAgY29uc3QgJGlucHV0ID0gJCgnI2ZpbGUnKTtcbiAgICBjb25zdCB1cGxvYWRlZEZpbGUgPSAkaW5wdXQucHJvcCgnZmlsZXMnKVswXTtcblxuICAgIGNvbnN0IG1heFVwbG9hZFNpemUgPSAkaW5wdXQuZGF0YSgnbWF4LWZpbGUtdXBsb2FkLXNpemUnKTtcbiAgICBpZiAobWF4VXBsb2FkU2l6ZSA8IHVwbG9hZGVkRmlsZS5zaXplKSB7XG4gICAgICB0aGlzLnNob3dJbXBvcnRGaWxlRXJyb3IodXBsb2FkZWRGaWxlLm5hbWUsIHVwbG9hZGVkRmlsZS5zaXplLCAnRmlsZSBpcyB0b28gbGFyZ2UnKTtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCBkYXRhID0gbmV3IEZvcm1EYXRhKCk7XG4gICAgZGF0YS5hcHBlbmQoJ2ZpbGUnLCB1cGxvYWRlZEZpbGUpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgIHVybDogJCgnLmpzLWltcG9ydC1mb3JtJykuZGF0YSgnZmlsZS11cGxvYWQtdXJsJyksXG4gICAgICBkYXRhOiBkYXRhLFxuICAgICAgY2FjaGU6IGZhbHNlLFxuICAgICAgY29udGVudFR5cGU6IGZhbHNlLFxuICAgICAgcHJvY2Vzc0RhdGE6IGZhbHNlLFxuICAgIH0pLnRoZW4ocmVzcG9uc2UgPT4ge1xuICAgICAgaWYgKHJlc3BvbnNlLmVycm9yKSB7XG4gICAgICAgIHRoaXMuc2hvd0ltcG9ydEZpbGVFcnJvcih1cGxvYWRlZEZpbGUubmFtZSwgdXBsb2FkZWRGaWxlLnNpemUsIHJlc3BvbnNlLmVycm9yKTtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBsZXQgZmlsZW5hbWUgPSByZXNwb25zZS5maWxlLm5hbWU7XG5cbiAgICAgICQoJy5qcy1pbXBvcnQtZmlsZS1pbnB1dCcpLnZhbChmaWxlbmFtZSk7XG5cbiAgICAgIHRoaXMuc2hvd0ltcG9ydEZpbGVBbGVydChmaWxlbmFtZSk7XG4gICAgICB0aGlzLmhpZGVGaWxlVXBsb2FkQmxvY2soKTtcbiAgICAgIHRoaXMuYWRkRmlsZVRvSGlzdG9yeVRhYmxlKGZpbGVuYW1lKTtcbiAgICAgIHRoaXMuZW5hYmxlRmlsZXNIaXN0b3J5QnRuKCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBuZXcgcm93IGluIGZpbGVzIGhpc3RvcnkgdGFibGVcbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IGZpbGVuYW1lXG4gICAqL1xuICBhZGRGaWxlVG9IaXN0b3J5VGFibGUoZmlsZW5hbWUpIHtcbiAgICBjb25zdCAkdGFibGUgPSAkKCcjZmlsZUhpc3RvcnlUYWJsZScpO1xuXG4gICAgbGV0IGJhc2VEZWxldGVVcmwgPSAkdGFibGUuZGF0YSgnZGVsZXRlLWZpbGUtdXJsJyk7XG4gICAgbGV0IGRlbGV0ZVVybCA9IGJhc2VEZWxldGVVcmwgKyAnJmZpbGVuYW1lPScgKyBlbmNvZGVVUklDb21wb25lbnQoZmlsZW5hbWUpO1xuXG4gICAgbGV0IGJhc2VEb3dubG9hZFVybCA9ICR0YWJsZS5kYXRhKCdkb3dubG9hZC1maWxlLXVybCcpO1xuICAgIGxldCBkb3dubG9hZFVybCA9IGJhc2VEb3dubG9hZFVybCArICcmZmlsZW5hbWU9JyArIGVuY29kZVVSSUNvbXBvbmVudChmaWxlbmFtZSk7XG5cbiAgICBsZXQgJHRlbXBsYXRlID0gJHRhYmxlLmZpbmQoJ3RyOmZpcnN0JykuY2xvbmUoKTtcblxuICAgICR0ZW1wbGF0ZS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgJHRlbXBsYXRlLmZpbmQoJ3RkOmZpcnN0JykudGV4dChmaWxlbmFtZSk7XG4gICAgJHRlbXBsYXRlLmZpbmQoJy5idG4tZ3JvdXAnKS5hdHRyKCdkYXRhLWZpbGUnLCBmaWxlbmFtZSk7XG4gICAgJHRlbXBsYXRlLmZpbmQoJy5qcy1kZWxldGUtZmlsZS1idG4nKS5hdHRyKCdocmVmJywgZGVsZXRlVXJsKTtcbiAgICAkdGVtcGxhdGUuZmluZCgnLmpzLWRvd25sb2FkLWZpbGUtYnRuJykuYXR0cignaHJlZicsIGRvd25sb2FkVXJsKTtcblxuICAgICR0YWJsZS5maW5kKCd0Ym9keScpLmFwcGVuZCgkdGVtcGxhdGUpO1xuXG4gICAgbGV0IGZpbGVzTnVtYmVyID0gJHRhYmxlLmZpbmQoJ3RyJykubGVuZ3RoIC0gMTtcbiAgICAkKCcuanMtZmlsZXMtaGlzdG9yeS1udW1iZXInKS50ZXh0KGZpbGVzTnVtYmVyKTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IEltcG9ydFBhZ2UgZnJvbSAnLi9JbXBvcnRQYWdlJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgbmV3IEltcG9ydFBhZ2UoKTtcbn0pO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==