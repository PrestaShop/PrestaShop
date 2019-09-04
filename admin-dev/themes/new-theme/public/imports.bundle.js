window["imports"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 328);
/******/ })
/************************************************************************/
/******/ ({

/***/ 256:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
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

var _FormFieldToggle = __webpack_require__(327);

var _FormFieldToggle2 = _interopRequireDefault(_FormFieldToggle);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

var ImportPage = function () {
  function ImportPage() {
    var _this = this;

    _classCallCheck(this, ImportPage);

    new _FormFieldToggle2.default();

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
    key: 'handleSubmit',
    value: function handleSubmit() {
      $('.js-import-form').on('submit', function () {
        var $this = $(this);
        if ($this.find('input[name="truncate"]:checked').val() === '1') {
          return confirm($this.data('delete-confirm-message') + ' ' + $.trim($('#entity > option:selected').text().toLowerCase()) + '?');
        }
      });
    }

    /**
     * Check if selected file names exists and if so, then display it
     */

  }, {
    key: 'toggleSelectedFile',
    value: function toggleSelectedFile() {
      var selectFilename = $('#csv').val();
      if (selectFilename.length > 0) {
        this.showImportFileAlert(selectFilename);
        this.hideFileUploadBlock();
      }
    }
  }, {
    key: 'changeImportFileHandler',
    value: function changeImportFileHandler() {
      this.hideImportFileAlert();
      this.showFileUploadBlock();
    }

    /**
     * Show files history event handler
     */

  }, {
    key: 'showFilesHistoryHandler',
    value: function showFilesHistoryHandler() {
      this.showFilesHistory();
      this.hideFileUploadBlock();
    }

    /**
     * Close files history event handler
     */

  }, {
    key: 'closeFilesHistoryHandler',
    value: function closeFilesHistoryHandler() {
      this.closeFilesHistory();
      this.showFileUploadBlock();
    }

    /**
     * Show files history block
     */

  }, {
    key: 'showFilesHistory',
    value: function showFilesHistory() {
      $('.js-files-history-block').removeClass('d-none');
    }

    /**
     * Hide files history block
     */

  }, {
    key: 'closeFilesHistory',
    value: function closeFilesHistory() {
      $('.js-files-history-block').addClass('d-none');
    }

    /**
     *  Prefill hidden file input with selected file name from history
     */

  }, {
    key: 'useFileFromFilesHistory',
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
    key: 'showImportFileAlert',
    value: function showImportFileAlert(filename) {
      $('.js-import-file-alert').removeClass('d-none');
      $('.js-import-file').text(filename);
    }

    /**
     * Hides selected import file alert
     */

  }, {
    key: 'hideImportFileAlert',
    value: function hideImportFileAlert() {
      $('.js-import-file-alert').addClass('d-none');
    }

    /**
     * Hides import file upload block
     */

  }, {
    key: 'hideFileUploadBlock',
    value: function hideFileUploadBlock() {
      $('.js-file-upload-form-group').addClass('d-none');
    }

    /**
     * Hides import file upload block
     */

  }, {
    key: 'showFileUploadBlock',
    value: function showFileUploadBlock() {
      $('.js-file-upload-form-group').removeClass('d-none');
    }

    /**
     * Make file history button clickable
     */

  }, {
    key: 'enableFilesHistoryBtn',
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
    key: 'showImportFileError',
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
    key: 'hideImportFileError',
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
    key: 'humanizeSize',
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
    key: 'uploadFile',
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
    key: 'addFileToHistoryTable',
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

exports.default = ImportPage;

/***/ }),

/***/ 327:
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

var entityCategories = 0;
var entityProducts = 1;
var entityCombinations = 2;
var entityCustomers = 3;
var entityAddresses = 4;
var entityBrands = 5;
var entitySuppliers = 6;
var entityAlias = 7;
var entityStoreContacts = 8;

var FormFieldToggle = function () {
  function FormFieldToggle() {
    var _this = this;

    _classCallCheck(this, FormFieldToggle);

    $('.js-entity-select').on('change', function () {
      return _this.toggleForm();
    });

    this.toggleForm();
  }

  _createClass(FormFieldToggle, [{
    key: 'toggleForm',
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
    key: 'toggleEntityAlert',
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
    key: 'toggleFields',
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
    key: 'loadAvailableFields',
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
    key: '_removeAvailableFields',
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
    key: '_appendHelpBox',
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
    key: '_appendAvailableField',
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

exports.default = FormFieldToggle;

/***/ }),

/***/ 328:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _ImportPage = __webpack_require__(256);

var _ImportPage2 = _interopRequireDefault(_ImportPage);

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
  new _ImportPage2.default();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQ/MjBkNCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9pbXBvcnQvSW1wb3J0UGFnZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9pbXBvcnQvRm9ybUZpZWxkVG9nZ2xlLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2ltcG9ydC9pbmRleC5qcyJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwiSW1wb3J0UGFnZSIsIkZvcm1GaWVsZFRvZ2dsZSIsIm9uIiwic2hvd0ZpbGVzSGlzdG9yeUhhbmRsZXIiLCJjbG9zZUZpbGVzSGlzdG9yeUhhbmRsZXIiLCJldmVudCIsInVzZUZpbGVGcm9tRmlsZXNIaXN0b3J5IiwiY2hhbmdlSW1wb3J0RmlsZUhhbmRsZXIiLCJ1cGxvYWRGaWxlIiwidG9nZ2xlU2VsZWN0ZWRGaWxlIiwiaGFuZGxlU3VibWl0IiwiJHRoaXMiLCJmaW5kIiwidmFsIiwiY29uZmlybSIsImRhdGEiLCJ0cmltIiwidGV4dCIsInRvTG93ZXJDYXNlIiwic2VsZWN0RmlsZW5hbWUiLCJsZW5ndGgiLCJzaG93SW1wb3J0RmlsZUFsZXJ0IiwiaGlkZUZpbGVVcGxvYWRCbG9jayIsImhpZGVJbXBvcnRGaWxlQWxlcnQiLCJzaG93RmlsZVVwbG9hZEJsb2NrIiwic2hvd0ZpbGVzSGlzdG9yeSIsImNsb3NlRmlsZXNIaXN0b3J5IiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsImZpbGVuYW1lIiwidGFyZ2V0IiwiY2xvc2VzdCIsInJlbW92ZUF0dHIiLCJmaWxlTmFtZSIsImZpbGVTaXplIiwibWVzc2FnZSIsIiRhbGVydCIsImZpbGVEYXRhIiwiaHVtYW5pemVTaXplIiwiaHRtbCIsImJ5dGVzIiwidG9GaXhlZCIsImhpZGVJbXBvcnRGaWxlRXJyb3IiLCIkaW5wdXQiLCJ1cGxvYWRlZEZpbGUiLCJwcm9wIiwibWF4VXBsb2FkU2l6ZSIsInNpemUiLCJzaG93SW1wb3J0RmlsZUVycm9yIiwibmFtZSIsIkZvcm1EYXRhIiwiYXBwZW5kIiwiYWpheCIsInR5cGUiLCJ1cmwiLCJjYWNoZSIsImNvbnRlbnRUeXBlIiwicHJvY2Vzc0RhdGEiLCJ0aGVuIiwicmVzcG9uc2UiLCJlcnJvciIsImZpbGUiLCJhZGRGaWxlVG9IaXN0b3J5VGFibGUiLCJlbmFibGVGaWxlc0hpc3RvcnlCdG4iLCIkdGFibGUiLCJiYXNlRGVsZXRlVXJsIiwiZGVsZXRlVXJsIiwiZW5jb2RlVVJJQ29tcG9uZW50IiwiYmFzZURvd25sb2FkVXJsIiwiZG93bmxvYWRVcmwiLCIkdGVtcGxhdGUiLCJjbG9uZSIsImF0dHIiLCJmaWxlc051bWJlciIsImVudGl0eUNhdGVnb3JpZXMiLCJlbnRpdHlQcm9kdWN0cyIsImVudGl0eUNvbWJpbmF0aW9ucyIsImVudGl0eUN1c3RvbWVycyIsImVudGl0eUFkZHJlc3NlcyIsImVudGl0eUJyYW5kcyIsImVudGl0eVN1cHBsaWVycyIsImVudGl0eUFsaWFzIiwiZW50aXR5U3RvcmVDb250YWN0cyIsInRvZ2dsZUZvcm0iLCJzZWxlY3RlZE9wdGlvbiIsInNlbGVjdGVkRW50aXR5IiwicGFyc2VJbnQiLCJlbnRpdHlOYW1lIiwidG9nZ2xlRW50aXR5QWxlcnQiLCJ0b2dnbGVGaWVsZHMiLCJsb2FkQXZhaWxhYmxlRmllbGRzIiwiaW5jbHVkZXMiLCJzaG93IiwiaGlkZSIsIiR0cnVuY2F0ZUZvcm1Hcm91cCIsIiRtYXRjaFJlZkZvcm1Hcm91cCIsIiRyZWdlbmVyYXRlRm9ybUdyb3VwIiwiJGZvcmNlSWRzRm9ybUdyb3VwIiwiJGVudGl0eU5hbWVQbGFjZWhvbGRlciIsImVudGl0eSIsIiRhdmFpbGFibGVGaWVsZHMiLCJkYXRhVHlwZSIsIl9yZW1vdmVBdmFpbGFibGVGaWVsZHMiLCJpIiwiX2FwcGVuZEF2YWlsYWJsZUZpZWxkIiwibGFiZWwiLCJyZXF1aXJlZCIsImRlc2NyaXB0aW9uIiwicG9wb3ZlciIsIiRjb250YWluZXIiLCJlbXB0eSIsIiRmaWVsZCIsImhlbHBCb3hDb250ZW50IiwiJGhlbHBCb3giLCIkYXBwZW5kVG8iLCJmaWVsZFRleHQiLCJfYXBwZW5kSGVscEJveCIsImFwcGVuZFRvIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7Ozs7Ozs7Ozs7cWpCQ2hFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQTs7Ozs7Ozs7QUFFQSxJQUFNQSxJQUFJQyxPQUFPRCxDQUFqQjs7SUFFcUJFLFU7QUFDbkIsd0JBQWM7QUFBQTs7QUFBQTs7QUFDWixRQUFJQyx5QkFBSjs7QUFFQUgsTUFBRSw0QkFBRixFQUFnQ0ksRUFBaEMsQ0FBbUMsT0FBbkMsRUFBNEM7QUFBQSxhQUFNLE1BQUtDLHVCQUFMLEVBQU47QUFBQSxLQUE1QztBQUNBTCxNQUFFLG1DQUFGLEVBQXVDSSxFQUF2QyxDQUEwQyxPQUExQyxFQUFtRDtBQUFBLGFBQU0sTUFBS0Usd0JBQUwsRUFBTjtBQUFBLEtBQW5EO0FBQ0FOLE1BQUUsbUJBQUYsRUFBdUJJLEVBQXZCLENBQTBCLE9BQTFCLEVBQW1DLGtCQUFuQyxFQUF1RCxVQUFDRyxLQUFEO0FBQUEsYUFBVyxNQUFLQyx1QkFBTCxDQUE2QkQsS0FBN0IsQ0FBWDtBQUFBLEtBQXZEO0FB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SSxXQUhkO0FBS0Q7O0FBRURQLHlCQUFpQnhGLElBQWpCLENBQXNCLHlCQUF0QixFQUFpRGdHLE9BQWpEO0FBQ0QsT0FsQkQ7QUFtQkQ7O0FBRUQ7Ozs7Ozs7OzsyQ0FNdUJDLFUsRUFBWTtBQUNqQ0EsaUJBQVdqRyxJQUFYLENBQWdCLHlCQUFoQixFQUEyQ2dHLE9BQTNDLENBQW1ELE1BQW5EO0FBQ0FDLGlCQUFXQyxLQUFYO0FBQ0Q7O0FBRUQ7Ozs7Ozs7Ozs7bUNBT2VDLE0sRUFBUUMsYyxFQUFnQjtBQUNyQyxVQUFJQyxXQUFXbkgsRUFBRSxzQ0FBRixFQUEwQ3lFLEtBQTFDLEVBQWY7O0FBRUEwQyxlQUFTekMsSUFBVCxDQUFjLGNBQWQsRUFBOEJ3QyxjQUE5QjtBQUNBQyxlQUFTdEYsV0FBVCxDQUFxQiw0Q0FBckI7QUFDQW9GLGFBQU81RCxNQUFQLENBQWM4RCxRQUFkO0FBQ0Q7O0FBRUQ7Ozs7Ozs7Ozs7OzBDQVFzQkMsUyxFQUFXQyxTLEVBQVdILGMsRUFBZ0I7QUFDMUQsVUFBSUQsU0FBU2pILEVBQUUsOEJBQUYsRUFBa0N5RSxLQUFsQyxFQUFiOztBQUVBd0MsYUFBTzlGLElBQVAsQ0FBWWtHLFNBQVo7O0FBRUEsVUFBSUgsY0FBSixFQUFvQjtBQUNsQjtBQUNBLGFBQUtJLGNBQUwsQ0FBb0JMLE1BQXBCLEVBQTRCQyxjQUE1QjtBQUNEOztBQUVERCxhQUFPcEYsV0FBUCxDQUFtQixvQ0FBbkI7QUFDQW9GLGFBQU9NLFFBQVAsQ0FBZ0JILFNBQWhCO0FBQ0Q7Ozs7OztrQkFwS2tCakgsZTs7Ozs7Ozs7OztBQ1pyQjs7Ozs7O0FBRUEsSUFBTUgsSUFBSUMsT0FBT0QsQ0FBakIsQyxDQTNCQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQTZCQUEsRUFBRSxZQUFNO0FBQ04sTUFBSUUsb0JBQUo7QUFDRCxDQUZELEUiLCJmaWxlIjoiaW1wb3J0cy5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDMyOCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgRm9ybUZpZWxkVG9nZ2xlIGZyb20gXCIuL0Zvcm1GaWVsZFRvZ2dsZVwiO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEltcG9ydFBhZ2Uge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICBuZXcgRm9ybUZpZWxkVG9nZ2xlKCk7XG5cbiAgICAkKCcuanMtZnJvbS1maWxlcy1oaXN0b3J5LWJ0bicpLm9uKCdjbGljaycsICgpID0+IHRoaXMuc2hvd0ZpbGVzSGlzdG9yeUhhbmRsZXIoKSk7XG4gICAgJCgnLmpzLWNsb3NlLWZpbGVzLWhpc3RvcnktYmxvY2stYnRuJykub24oJ2NsaWNrJywgKCkgPT4gdGhpcy5jbG9zZUZpbGVzSGlzdG9yeUhhbmRsZXIoKSk7XG4gICAgJCgnI2ZpbGVIaXN0b3J5VGFibGUnKS5vbignY2xpY2snLCAnLmpzLXVzZS1maWxlLWJ0bicsIChldmVudCkgPT4gdGhpcy51c2VGaWxlRnJvbUZpbGVzSGlzdG9yeShldmVudCkpO1xuICAgICQoJy5qcy1jaGFuZ2UtaW1wb3J0LWZpbGUtYnRuJykub24oJ2NsaWNrJywgKCkgPT4gdGhpcy5jaGFuZ2VJbXBvcnRGaWxlSGFuZGxlcigpKTtcbiAgICAkKCcuanMtaW1wb3J0LWZpbGUnKS5vbignY2hhbmdlJywgKCkgPT4gdGhpcy51cGxvYWRGaWxlKCkpO1xuXG4gICAgdGhpcy50b2dnbGVTZWxlY3RlZEZpbGUoKTtcbiAgICB0aGlzLmhhbmRsZVN1Ym1pdCgpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZSBzdWJtaXQgYW5kIGFkZCBjb25maXJtIGJveCBpbiBjYXNlIHRoZSB0b2dnbGUgYnV0dG9uIGFib3V0XG4gICAqIGRlbGV0aW5nIGFsbCBlbnRpdGllcyBiZWZvcmUgaW1wb3J0IGlzIGNoZWNrZWRcbiAgICovXG4gIGhhbmRsZVN1Ym1pdCgpIHtcbiAgICAkKCcuanMtaW1wb3J0LWZvcm0nKS5vbignc3VibWl0JywgZnVuY3Rpb24oKSB7XG4gICAgICBjb25zdCAkdGhpcyA9ICQodGhpcyk7XG4gICAgICBpZiAoJHRoaXMuZmluZCgnaW5wdXRbbmFtZT1cInRydW5jYXRlXCJdOmNoZWNrZWQnKS52YWwoKSA9PT0gJzEnKSB7XG4gICAgICAgIHJldHVybiBjb25maXJtKGAkeyR0aGlzLmRhdGEoJ2RlbGV0ZS1jb25maXJtLW1lc3NhZ2UnKX0gJHskLnRyaW0oJCgnI2VudGl0eSA+IG9wdGlvbjpzZWxlY3RlZCcpLnRleHQoKS50b0xvd2VyQ2FzZSgpKX0/YCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQ2hlY2sgaWYgc2VsZWN0ZWQgZmlsZSBuYW1lcyBleGlzdHMgYW5kIGlmIHNvLCB0aGVuIGRpc3BsYXkgaXRcbiAgICovXG4gIHRvZ2dsZVNlbGVjdGVkRmlsZSgpIHtcbiAgICBsZXQgc2VsZWN0RmlsZW5hbWUgPSAkKCcjY3N2JykudmFsKCk7XG4gICAgaWYgKHNlbGVjdEZpbGVuYW1lLmxlbmd0aCA+IDApIHtcbiAgICAgIHRoaXMuc2hvd0ltcG9ydEZpbGVBbGVydChzZWxlY3RGaWxlbmFtZSk7XG4gICAgICB0aGlzLmhpZGVGaWxlVXBsb2FkQmxvY2soKTtcbiAgICB9XG4gIH1cblxuICBjaGFuZ2VJbXBvcnRGaWxlSGFuZGxlcigpIHtcbiAgICB0aGlzLmhpZGVJbXBvcnRGaWxlQWxlcnQoKTtcbiAgICB0aGlzLnNob3dGaWxlVXBsb2FkQmxvY2soKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IGZpbGVzIGhpc3RvcnkgZXZlbnQgaGFuZGxlclxuICAgKi9cbiAgc2hvd0ZpbGVzSGlzdG9yeUhhbmRsZXIoKSB7XG4gICAgdGhpcy5zaG93RmlsZXNIaXN0b3J5KCk7XG4gICAgdGhpcy5oaWRlRmlsZVVwbG9hZEJsb2NrKCk7XG4gIH1cblxuICAvKipcbiAgICogQ2xvc2UgZmlsZXMgaGlzdG9yeSBldmVudCBoYW5kbGVyXG4gICAqL1xuICBjbG9zZUZpbGVzSGlzdG9yeUhhbmRsZXIoKSB7XG4gICAgdGhpcy5jbG9zZUZpbGVzSGlzdG9yeSgpO1xuICAgIHRoaXMuc2hvd0ZpbGVVcGxvYWRCbG9jaygpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3cgZmlsZXMgaGlzdG9yeSBibG9ja1xuICAgKi9cbiAgc2hvd0ZpbGVzSGlzdG9yeSgpIHtcbiAgICAkKCcuanMtZmlsZXMtaGlzdG9yeS1ibG9jaycpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlIGZpbGVzIGhpc3RvcnkgYmxvY2tcbiAgICovXG4gIGNsb3NlRmlsZXNIaXN0b3J5KCkge1xuICAgICQoJy5qcy1maWxlcy1oaXN0b3J5LWJsb2NrJykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqICBQcmVmaWxsIGhpZGRlbiBmaWxlIGlucHV0IHdpdGggc2VsZWN0ZWQgZmlsZSBuYW1lIGZyb20gaGlzdG9yeVxuICAgKi9cbiAgdXNlRmlsZUZyb21GaWxlc0hpc3RvcnkoZXZlbnQpIHtcbiAgICBsZXQgZmlsZW5hbWUgPSAkKGV2ZW50LnRhcmdldCkuY2xvc2VzdCgnLmJ0bi1ncm91cCcpLmRhdGEoJ2ZpbGUnKTtcblxuICAgICQoJy5qcy1pbXBvcnQtZmlsZS1pbnB1dCcpLnZhbChmaWxlbmFtZSk7XG5cbiAgICB0aGlzLnNob3dJbXBvcnRGaWxlQWxlcnQoZmlsZW5hbWUpO1xuICAgIHRoaXMuY2xvc2VGaWxlc0hpc3RvcnkoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IGFsZXJ0IHdpdGggaW1wb3J0ZWQgZmlsZSBuYW1lXG4gICAqL1xuICBzaG93SW1wb3J0RmlsZUFsZXJ0KGZpbGVuYW1lKSB7XG4gICAgJCgnLmpzLWltcG9ydC1maWxlLWFsZXJ0JykucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICQoJy5qcy1pbXBvcnQtZmlsZScpLnRleHQoZmlsZW5hbWUpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIHNlbGVjdGVkIGltcG9ydCBmaWxlIGFsZXJ0XG4gICAqL1xuICBoaWRlSW1wb3J0RmlsZUFsZXJ0KCkge1xuICAgICQoJy5qcy1pbXBvcnQtZmlsZS1hbGVydCcpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyBpbXBvcnQgZmlsZSB1cGxvYWQgYmxvY2tcbiAgICovXG4gIGhpZGVGaWxlVXBsb2FkQmxvY2soKSB7XG4gICAgJCgnLmpzLWZpbGUtdXBsb2FkLWZvcm0tZ3JvdXAnKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgaW1wb3J0IGZpbGUgdXBsb2FkIGJsb2NrXG4gICAqL1xuICBzaG93RmlsZVVwbG9hZEJsb2NrKCkge1xuICAgICQoJy5qcy1maWxlLXVwbG9hZC1mb3JtLWdyb3VwJykucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIE1ha2UgZmlsZSBoaXN0b3J5IGJ1dHRvbiBjbGlja2FibGVcbiAgICovXG4gIGVuYWJsZUZpbGVzSGlzdG9yeUJ0bigpIHtcbiAgICAkKCcuanMtZnJvbS1maWxlcy1oaXN0b3J5LWJ0bicpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gIH1cblxuICAvKipcbiAgICogU2hvdyBlcnJvciBtZXNzYWdlIGlmIGZpbGUgdXBsb2FkaW5nIGZhaWxlZFxuICAgKlxuICAgKiBAcGFyYW0ge3N0cmluZ30gZmlsZU5hbWVcbiAgICogQHBhcmFtIHtpbnRlZ2VyfSBmaWxlU2l6ZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gbWVzc2FnZVxuICAgKi9cbiAgc2hvd0ltcG9ydEZpbGVFcnJvcihmaWxlTmFtZSwgZmlsZVNpemUsIG1lc3NhZ2UpIHtcbiAgICBjb25zdCAkYWxlcnQgPSAkKCcuanMtaW1wb3J0LWZpbGUtZXJyb3InKTtcblxuICAgIGNvbnN0IGZpbGVEYXRhID0gZmlsZU5hbWUgKyAnICgnICsgdGhpcy5odW1hbml6ZVNpemUoZmlsZVNpemUpICsgJyknO1xuXG4gICAgJGFsZXJ0LmZpbmQoJy5qcy1maWxlLWRhdGEnKS5odG1sKGZpbGVEYXRhKTtcbiAgICAkYWxlcnQuZmluZCgnLmpzLWVycm9yLW1lc3NhZ2UnKS5odG1sKG1lc3NhZ2UpO1xuICAgICRhbGVydC5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZSBmaWxlIHVwbG9hZGluZyBlcnJvclxuICAgKi9cbiAgaGlkZUltcG9ydEZpbGVFcnJvcigpIHtcbiAgICBjb25zdCAkYWxlcnQgPSAkKCcuanMtaW1wb3J0LWZpbGUtZXJyb3InKTtcbiAgICAkYWxlcnQuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3cgZmlsZSBzaXplIGluIGh1bWFuIHJlYWRhYmxlIGZvcm1hdFxuICAgKlxuICAgKiBAcGFyYW0ge2ludH0gYnl0ZXNcbiAgICpcbiAgICogQHJldHVybnMge3N0cmluZ31cbiAgICovXG4gIGh1bWFuaXplU2l6ZShieXRlcykge1xuICAgIGlmICh0eXBlb2YgYnl0ZXMgIT09ICdudW1iZXInKSB7XG4gICAgICByZXR1cm4gJyc7XG4gICAgfVxuXG4gICAgaWYgKGJ5dGVzID49IDEwMDAwMDAwMDApIHtcbiAgICAgIHJldHVybiAoYnl0ZXMgLyAxMDAwMDAwMDAwKS50b0ZpeGVkKDIpICsgJyBHQic7XG4gICAgfVxuXG4gICAgaWYgKGJ5dGVzID49IDEwMDAwMDApIHtcbiAgICAgIHJldHVybiAoYnl0ZXMgLyAxMDAwMDAwKS50b0ZpeGVkKDIpICsgJyBNQic7XG4gICAgfVxuXG4gICAgcmV0dXJuIChieXRlcyAvIDEwMDApLnRvRml4ZWQoMikgKyAnIEtCJztcbiAgfVxuXG4gIC8qKlxuICAgKiBVcGxvYWQgc2VsZWN0ZWQgaW1wb3J0IGZpbGVcbiAgICovXG4gIHVwbG9hZEZpbGUoKSB7XG4gICAgdGhpcy5oaWRlSW1wb3J0RmlsZUVycm9yKCk7XG5cbiAgICBjb25zdCAkaW5wdXQgPSAkKCcjZmlsZScpO1xuICAgIGNvbnN0IHVwbG9hZGVkRmlsZSA9ICRpbnB1dC5wcm9wKCdmaWxlcycpWzBdO1xuXG4gICAgY29uc3QgbWF4VXBsb2FkU2l6ZSA9ICRpbnB1dC5kYXRhKCdtYXgtZmlsZS11cGxvYWQtc2l6ZScpO1xuICAgIGlmIChtYXhVcGxvYWRTaXplIDwgdXBsb2FkZWRGaWxlLnNpemUpIHtcbiAgICAgIHRoaXMuc2hvd0ltcG9ydEZpbGVFcnJvcih1cGxvYWRlZEZpbGUubmFtZSwgdXBsb2FkZWRGaWxlLnNpemUsICdGaWxlIGlzIHRvbyBsYXJnZScpO1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGNvbnN0IGRhdGEgPSBuZXcgRm9ybURhdGEoKTtcbiAgICBkYXRhLmFwcGVuZCgnZmlsZScsIHVwbG9hZGVkRmlsZSk7XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdHlwZTogJ1BPU1QnLFxuICAgICAgdXJsOiAkKCcuanMtaW1wb3J0LWZvcm0nKS5kYXRhKCdmaWxlLXVwbG9hZC11cmwnKSxcbiAgICAgIGRhdGE6IGRhdGEsXG4gICAgICBjYWNoZTogZmFsc2UsXG4gICAgICBjb250ZW50VHlwZTogZmFsc2UsXG4gICAgICBwcm9jZXNzRGF0YTogZmFsc2UsXG4gICAgfSkudGhlbihyZXNwb25zZSA9PiB7XG4gICAgICBpZiAocmVzcG9uc2UuZXJyb3IpIHtcbiAgICAgICAgdGhpcy5zaG93SW1wb3J0RmlsZUVycm9yKHVwbG9hZGVkRmlsZS5uYW1lLCB1cGxvYWRlZEZpbGUuc2l6ZSwgcmVzcG9uc2UuZXJyb3IpO1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIGxldCBmaWxlbmFtZSA9IHJlc3BvbnNlLmZpbGUubmFtZTtcblxuICAgICAgJCgnLmpzLWltcG9ydC1maWxlLWlucHV0JykudmFsKGZpbGVuYW1lKTtcblxuICAgICAgdGhpcy5zaG93SW1wb3J0RmlsZUFsZXJ0KGZpbGVuYW1lKTtcbiAgICAgIHRoaXMuaGlkZUZpbGVVcGxvYWRCbG9jaygpO1xuICAgICAgdGhpcy5hZGRGaWxlVG9IaXN0b3J5VGFibGUoZmlsZW5hbWUpO1xuICAgICAgdGhpcy5lbmFibGVGaWxlc0hpc3RvcnlCdG4oKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIG5ldyByb3cgaW4gZmlsZXMgaGlzdG9yeSB0YWJsZVxuICAgKlxuICAgKiBAcGFyYW0ge3N0cmluZ30gZmlsZW5hbWVcbiAgICovXG4gIGFkZEZpbGVUb0hpc3RvcnlUYWJsZShmaWxlbmFtZSkge1xuICAgIGNvbnN0ICR0YWJsZSA9ICQoJyNmaWxlSGlzdG9yeVRhYmxlJyk7XG5cbiAgICBsZXQgYmFzZURlbGV0ZVVybCA9ICR0YWJsZS5kYXRhKCdkZWxldGUtZmlsZS11cmwnKTtcbiAgICBsZXQgZGVsZXRlVXJsID0gYmFzZURlbGV0ZVVybCArICcmZmlsZW5hbWU9JyArIGVuY29kZVVSSUNvbXBvbmVudChmaWxlbmFtZSk7XG5cbiAgICBsZXQgYmFzZURvd25sb2FkVXJsID0gJHRhYmxlLmRhdGEoJ2Rvd25sb2FkLWZpbGUtdXJsJyk7XG4gICAgbGV0IGRvd25sb2FkVXJsID0gYmFzZURvd25sb2FkVXJsICsgJyZmaWxlbmFtZT0nICsgZW5jb2RlVVJJQ29tcG9uZW50KGZpbGVuYW1lKTtcblxuICAgIGxldCAkdGVtcGxhdGUgPSAkdGFibGUuZmluZCgndHI6Zmlyc3QnKS5jbG9uZSgpO1xuXG4gICAgJHRlbXBsYXRlLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAkdGVtcGxhdGUuZmluZCgndGQ6Zmlyc3QnKS50ZXh0KGZpbGVuYW1lKTtcbiAgICAkdGVtcGxhdGUuZmluZCgnLmJ0bi1ncm91cCcpLmF0dHIoJ2RhdGEtZmlsZScsIGZpbGVuYW1lKTtcbiAgICAkdGVtcGxhdGUuZmluZCgnLmpzLWRlbGV0ZS1maWxlLWJ0bicpLmF0dHIoJ2hyZWYnLCBkZWxldGVVcmwpO1xuICAgICR0ZW1wbGF0ZS5maW5kKCcuanMtZG93bmxvYWQtZmlsZS1idG4nKS5hdHRyKCdocmVmJywgZG93bmxvYWRVcmwpO1xuXG4gICAgJHRhYmxlLmZpbmQoJ3Rib2R5JykuYXBwZW5kKCR0ZW1wbGF0ZSk7XG5cbiAgICBsZXQgZmlsZXNOdW1iZXIgPSAkdGFibGUuZmluZCgndHInKS5sZW5ndGggLSAxO1xuICAgICQoJy5qcy1maWxlcy1oaXN0b3J5LW51bWJlcicpLnRleHQoZmlsZXNOdW1iZXIpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9pbXBvcnQvSW1wb3J0UGFnZS5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuY29uc3QgZW50aXR5Q2F0ZWdvcmllcyA9IDA7XG5jb25zdCBlbnRpdHlQcm9kdWN0cyA9IDE7XG5jb25zdCBlbnRpdHlDb21iaW5hdGlvbnMgPSAyO1xuY29uc3QgZW50aXR5Q3VzdG9tZXJzID0gMztcbmNvbnN0IGVudGl0eUFkZHJlc3NlcyA9IDQ7XG5jb25zdCBlbnRpdHlCcmFuZHMgPSA1O1xuY29uc3QgZW50aXR5U3VwcGxpZXJzID0gNjtcbmNvbnN0IGVudGl0eUFsaWFzID0gNztcbmNvbnN0IGVudGl0eVN0b3JlQ29udGFjdHMgPSA4O1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBGb3JtRmllbGRUb2dnbGUge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICAkKCcuanMtZW50aXR5LXNlbGVjdCcpLm9uKCdjaGFuZ2UnLCAoKSA9PiB0aGlzLnRvZ2dsZUZvcm0oKSk7XG5cbiAgICB0aGlzLnRvZ2dsZUZvcm0oKTtcbiAgfVxuXG4gIHRvZ2dsZUZvcm0oKSB7XG4gICAgbGV0IHNlbGVjdGVkT3B0aW9uID0gJCgnI2VudGl0eScpLmZpbmQoJ29wdGlvbjpzZWxlY3RlZCcpO1xuICAgIGxldCBzZWxlY3RlZEVudGl0eSA9IHBhcnNlSW50KHNlbGVjdGVkT3B0aW9uLnZhbCgpKTtcbiAgICBsZXQgZW50aXR5TmFtZSA9IHNlbGVjdGVkT3B0aW9uLnRleHQoKS50b0xvd2VyQ2FzZSgpO1xuXG4gICAgdGhpcy50b2dnbGVFbnRpdHlBbGVydChzZWxlY3RlZEVudGl0eSk7XG4gICAgdGhpcy50b2dnbGVGaWVsZHMoc2VsZWN0ZWRFbnRpdHksIGVudGl0eU5hbWUpO1xuICAgIHRoaXMubG9hZEF2YWlsYWJsZUZpZWxkcyhzZWxlY3RlZEVudGl0eSk7XG4gIH1cblxuICAvKipcbiAgICogVG9nZ2xlIGFsZXJ0IHdhcm5pbmcgZm9yIHNlbGVjdGVkIGltcG9ydCBlbnRpdHlcbiAgICpcbiAgICogQHBhcmFtIHtpbnR9IHNlbGVjdGVkRW50aXR5XG4gICAqL1xuICB0b2dnbGVFbnRpdHlBbGVydChzZWxlY3RlZEVudGl0eSkge1xuICAgIGxldCAkYWxlcnQgPSAkKCcuanMtZW50aXR5LWFsZXJ0Jyk7XG5cbiAgICBpZiAoW2VudGl0eUNhdGVnb3JpZXMsIGVudGl0eVByb2R1Y3RzXS5pbmNsdWRlcyhzZWxlY3RlZEVudGl0eSkpIHtcbiAgICAgICRhbGVydC5zaG93KCk7XG4gICAgfSBlbHNlIHtcbiAgICAgICRhbGVydC5oaWRlKCk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIFRvZ2dsZSBhdmFpbGFibGUgb3B0aW9ucyBmb3Igc2VsZWN0ZWQgZW50aXR5XG4gICAqXG4gICAqIEBwYXJhbSB7aW50fSBzZWxlY3RlZEVudGl0eVxuICAgKiBAcGFyYW0ge3N0cmluZ30gZW50aXR5TmFtZVxuICAgKi9cbiAgdG9nZ2xlRmllbGRzKHNlbGVjdGVkRW50aXR5LCBlbnRpdHlOYW1lKSB7XG4gICAgY29uc3QgJHRydW5jYXRlRm9ybUdyb3VwID0gJCgnLmpzLXRydW5jYXRlLWZvcm0tZ3JvdXAnKTtcbiAgICBjb25zdCAkbWF0Y2hSZWZGb3JtR3JvdXAgPSAkKCcuanMtbWF0Y2gtcmVmLWZvcm0tZ3JvdXAnKTtcbiAgICBjb25zdCAkcmVnZW5lcmF0ZUZvcm1Hcm91cCA9ICQoJy5qcy1yZWdlbmVyYXRlLWZvcm0tZ3JvdXAnKTtcbiAgICBjb25zdCAkZm9yY2VJZHNGb3JtR3JvdXAgPSAkKCcuanMtZm9yY2UtaWRzLWZvcm0tZ3JvdXAnKTtcbiAgICBjb25zdCAkZW50aXR5TmFtZVBsYWNlaG9sZGVyID0gJCgnLmpzLWVudGl0eS1uYW1lJyk7XG5cbiAgICBpZiAoZW50aXR5U3RvcmVDb250YWN0cyA9PT0gc2VsZWN0ZWRFbnRpdHkpIHtcbiAgICAgICR0cnVuY2F0ZUZvcm1Hcm91cC5oaWRlKCk7XG4gICAgfSBlbHNlIHtcbiAgICAgICR0cnVuY2F0ZUZvcm1Hcm91cC5zaG93KCk7XG4gICAgfVxuXG4gICAgaWYgKFtlbnRpdHlQcm9kdWN0cywgZW50aXR5Q29tYmluYXRpb25zXS5pbmNsdWRlcyhzZWxlY3RlZEVudGl0eSkpIHtcbiAgICAgICRtYXRjaFJlZkZvcm1Hcm91cC5zaG93KCk7XG4gICAgfSBlbHNlIHtcbiAgICAgICRtYXRjaFJlZkZvcm1Hcm91cC5oaWRlKCk7XG4gICAgfVxuXG4gICAgaWYgKFtcbiAgICAgIGVudGl0eUNhdGVnb3JpZXMsXG4gICAgICBlbnRpdHlQcm9kdWN0cyxcbiAgICAgIGVudGl0eUJyYW5kcyxcbiAgICAgIGVudGl0eVN1cHBsaWVycyxcbiAgICAgIGVudGl0eVN0b3JlQ29udGFjdHNcbiAgICBdLmluY2x1ZGVzKHNlbGVjdGVkRW50aXR5KVxuICAgICkge1xuICAgICAgJHJlZ2VuZXJhdGVGb3JtR3JvdXAuc2hvdygpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkcmVnZW5lcmF0ZUZvcm1Hcm91cC5oaWRlKCk7XG4gICAgfVxuXG4gICAgaWYgKFtcbiAgICAgIGVudGl0eUNhdGVnb3JpZXMsXG4gICAgICBlbnRpdHlQcm9kdWN0cyxcbiAgICAgIGVudGl0eUN1c3RvbWVycyxcbiAgICAgIGVudGl0eUFkZHJlc3NlcyxcbiAgICAgIGVudGl0eUJyYW5kcyxcbiAgICAgIGVudGl0eVN1cHBsaWVycyxcbiAgICAgIGVudGl0eVN0b3JlQ29udGFjdHMsXG4gICAgICBlbnRpdHlBbGlhc1xuICAgIF0uaW5jbHVkZXMoc2VsZWN0ZWRFbnRpdHkpXG4gICAgKSB7XG4gICAgICAkZm9yY2VJZHNGb3JtR3JvdXAuc2hvdygpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkZm9yY2VJZHNGb3JtR3JvdXAuaGlkZSgpO1xuICAgIH1cblxuICAgICRlbnRpdHlOYW1lUGxhY2Vob2xkZXIuaHRtbChlbnRpdHlOYW1lKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMb2FkIGF2YWlsYWJsZSBmaWVsZHMgZm9yIGdpdmVuIGVudGl0eVxuICAgKlxuICAgKiBAcGFyYW0ge2ludH0gZW50aXR5XG4gICAqL1xuICBsb2FkQXZhaWxhYmxlRmllbGRzKGVudGl0eSkge1xuICAgIGNvbnN0ICRhdmFpbGFibGVGaWVsZHMgPSAkKCcuanMtYXZhaWxhYmxlLWZpZWxkcycpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHVybDogJGF2YWlsYWJsZUZpZWxkcy5kYXRhKCd1cmwnKSxcbiAgICAgIGRhdGE6IHtcbiAgICAgICAgZW50aXR5OiBlbnRpdHlcbiAgICAgIH0sXG4gICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgIH0pLnRoZW4ocmVzcG9uc2UgPT4ge1xuICAgICAgdGhpcy5fcmVtb3ZlQXZhaWxhYmxlRmllbGRzKCRhdmFpbGFibGVGaWVsZHMpO1xuXG4gICAgICBmb3IgKGxldCBpID0gMDsgaSA8IHJlc3BvbnNlLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgIHRoaXMuX2FwcGVuZEF2YWlsYWJsZUZpZWxkKFxuICAgICAgICAgICRhdmFpbGFibGVGaWVsZHMsXG4gICAgICAgICAgcmVzcG9uc2VbaV0ubGFiZWwgKyAocmVzcG9uc2VbaV0ucmVxdWlyZWQgPyAnKicgOiAnJyksXG4gICAgICAgICAgcmVzcG9uc2VbaV0uZGVzY3JpcHRpb25cbiAgICAgICAgKTtcbiAgICAgIH1cblxuICAgICAgJGF2YWlsYWJsZUZpZWxkcy5maW5kKCdbZGF0YS10b2dnbGU9XCJwb3BvdmVyXCJdJykucG9wb3ZlcigpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbW92ZSBhdmFpbGFibGUgZmllbGRzIGNvbnRlbnQgZnJvbSBnaXZlbiBjb250YWluZXIuXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkY29udGFpbmVyXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVtb3ZlQXZhaWxhYmxlRmllbGRzKCRjb250YWluZXIpIHtcbiAgICAkY29udGFpbmVyLmZpbmQoJ1tkYXRhLXRvZ2dsZT1cInBvcG92ZXJcIl0nKS5wb3BvdmVyKCdoaWRlJyk7XG4gICAgJGNvbnRhaW5lci5lbXB0eSgpO1xuICB9XG5cbiAgLyoqXG4gICAqIEFwcGVuZCBhIGhlbHAgYm94IHRvIGdpdmVuIGZpZWxkLlxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGZpZWxkXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBoZWxwQm94Q29udGVudFxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2FwcGVuZEhlbHBCb3goJGZpZWxkLCBoZWxwQm94Q29udGVudCkge1xuICAgIGxldCAkaGVscEJveCA9ICQoJy5qcy1hdmFpbGFibGUtZmllbGQtcG9wb3Zlci10ZW1wbGF0ZScpLmNsb25lKCk7XG5cbiAgICAkaGVscEJveC5hdHRyKCdkYXRhLWNvbnRlbnQnLCBoZWxwQm94Q29udGVudCk7XG4gICAgJGhlbHBCb3gucmVtb3ZlQ2xhc3MoJ2pzLWF2YWlsYWJsZS1maWVsZC1wb3BvdmVyLXRlbXBsYXRlIGQtbm9uZScpO1xuICAgICRmaWVsZC5hcHBlbmQoJGhlbHBCb3gpO1xuICB9XG5cbiAgLyoqXG4gICAqIEFwcGVuZCBhdmFpbGFibGUgZmllbGQgdG8gZ2l2ZW4gY29udGFpbmVyLlxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGFwcGVuZFRvIGZpZWxkIHdpbGwgYmUgYXBwZW5kZWQgdG8gdGhpcyBjb250YWluZXIuXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBmaWVsZFRleHRcbiAgICogQHBhcmFtIHtTdHJpbmd9IGhlbHBCb3hDb250ZW50XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfYXBwZW5kQXZhaWxhYmxlRmllbGQoJGFwcGVuZFRvLCBmaWVsZFRleHQsIGhlbHBCb3hDb250ZW50KSB7XG4gICAgbGV0ICRmaWVsZCA9ICQoJy5qcy1hdmFpbGFibGUtZmllbGQtdGVtcGxhdGUnKS5jbG9uZSgpO1xuXG4gICAgJGZpZWxkLnRleHQoZmllbGRUZXh0KTtcblxuICAgIGlmIChoZWxwQm94Q29udGVudCkge1xuICAgICAgLy8gQXBwZW5kIGhlbHAgYm94IG5leHQgdG8gdGhlIGZpZWxkXG4gICAgICB0aGlzLl9hcHBlbmRIZWxwQm94KCRmaWVsZCwgaGVscEJveENvbnRlbnQpO1xuICAgIH1cblxuICAgICRmaWVsZC5yZW1vdmVDbGFzcygnanMtYXZhaWxhYmxlLWZpZWxkLXRlbXBsYXRlIGQtbm9uZScpO1xuICAgICRmaWVsZC5hcHBlbmRUbygkYXBwZW5kVG8pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9pbXBvcnQvRm9ybUZpZWxkVG9nZ2xlLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IEltcG9ydFBhZ2UgZnJvbSAnLi9JbXBvcnRQYWdlJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgbmV3IEltcG9ydFBhZ2UoKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvaW1wb3J0L2luZGV4LmpzIl0sInNvdXJjZVJvb3QiOiIifQ==