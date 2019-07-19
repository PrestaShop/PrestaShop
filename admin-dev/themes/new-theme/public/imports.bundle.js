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
/******/ 	return __webpack_require__(__webpack_require__.s = 329);
/******/ })
/************************************************************************/
/******/ ({

/***/ 257:
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

var _FormFieldToggle = __webpack_require__(328);

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

/***/ 328:
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

/***/ 329:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _ImportPage = __webpack_require__(257);

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODI/M2YxNioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvaW1wb3J0L0ltcG9ydFBhZ2UuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvaW1wb3J0L0Zvcm1GaWVsZFRvZ2dsZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9pbXBvcnQvaW5kZXguanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIkltcG9ydFBhZ2UiLCJGb3JtRmllbGRUb2dnbGUiLCJvbiIsInNob3dGaWxlc0hpc3RvcnlIYW5kbGVyIiwiY2xvc2VGaWxlc0hpc3RvcnlIYW5kbGVyIiwiZXZlbnQiLCJ1c2VGaWxlRnJvbUZpbGVzSGlzdG9yeSIsImNoYW5nZUltcG9ydEZpbGVIYW5kbGVyIiwidXBsb2FkRmlsZSIsInRvZ2dsZVNlbGVjdGVkRmlsZSIsImhhbmRsZVN1Ym1pdCIsIiR0aGlzIiwiZmluZCIsInZhbCIsImNvbmZpcm0iLCJkYXRhIiwidHJpbSIsInRleHQiLCJ0b0xvd2VyQ2FzZSIsInNlbGVjdEZpbGVuYW1lIiwibGVuZ3RoIiwic2hvd0ltcG9ydEZpbGVBbGVydCIsImhpZGVGaWxlVXBsb2FkQmxvY2siLCJoaWRlSW1wb3J0RmlsZUFsZXJ0Iiwic2hvd0ZpbGVVcGxvYWRCbG9jayIsInNob3dGaWxlc0hpc3RvcnkiLCJjbG9zZUZpbGVzSGlzdG9yeSIsInJlbW92ZUNsYXNzIiwiYWRkQ2xhc3MiLCJmaWxlbmFtZSIsInRhcmdldCIsImNsb3Nlc3QiLCJyZW1vdmVBdHRyIiwiZmlsZU5hbWUiLCJmaWxlU2l6ZSIsIm1lc3NhZ2UiLCIkYWxlcnQiLCJmaWxlRGF0YSIsImh1bWFuaXplU2l6ZSIsImh0bWwiLCJieXRlcyIsInRvRml4ZWQiLCJoaWRlSW1wb3J0RmlsZUVycm9yIiwiJGlucHV0IiwidXBsb2FkZWRGaWxlIiwicHJvcCIsIm1heFVwbG9hZFNpemUiLCJzaXplIiwic2hvd0ltcG9ydEZpbGVFcnJvciIsIm5hbWUiLCJGb3JtRGF0YSIsImFwcGVuZCIsImFqYXgiLCJ0eXBlIiwidXJsIiwiY2FjaGUiLCJjb250ZW50VHlwZSIsInByb2Nlc3NEYXRhIiwidGhlbiIsInJlc3BvbnNlIiwiZXJyb3IiLCJmaWxlIiwiYWRkRmlsZVRvSGlzdG9yeVRhYmxlIiwiZW5hYmxlRmlsZXNIaXN0b3J5QnRuIiwiJHRhYmxlIiwiYmFzZURlbGV0ZVVybCIsImRlbGV0ZVVybCIsImVuY29kZVVSSUNvbXBvbmVudCIsImJhc2VEb3dubG9hZFVybCIsImRvd25sb2FkVXJsIiwiJHRlbXBsYXRlIiwiY2xvbmUiLCJhdHRyIiwiZmlsZXNOdW1iZXIiLCJlbnRpdHlDYXRlZ29yaWVzIiwiZW50aXR5UHJvZHVjdHMiLCJlbnRpdHlDb21iaW5hdGlvbnMiLCJlbnRpdHlDdXN0b21lcnMiLCJlbnRpdHlBZGRyZXNzZXMiLCJlbnRpdHlCcmFuZHMiLCJlbnRpdHlTdXBwbGllcnMiLCJlbnRpdHlBbGlhcyIsImVudGl0eVN0b3JlQ29udGFjdHMiLCJ0b2dnbGVGb3JtIiwic2VsZWN0ZWRPcHRpb24iLCJzZWxlY3RlZEVudGl0eSIsInBhcnNlSW50IiwiZW50aXR5TmFtZSIsInRvZ2dsZUVudGl0eUFsZXJ0IiwidG9nZ2xlRmllbGRzIiwibG9hZEF2YWlsYWJsZUZpZWxkcyIsImluY2x1ZGVzIiwic2hvdyIsImhpZGUiLCIkdHJ1bmNhdGVGb3JtR3JvdXAiLCIkbWF0Y2hSZWZGb3JtR3JvdXAiLCIkcmVnZW5lcmF0ZUZvcm1Hcm91cCIsIiRmb3JjZUlkc0Zvcm1Hcm91cCIsIiRlbnRpdHlOYW1lUGxhY2Vob2xkZXIiLCJlbnRpdHkiLCIkYXZhaWxhYmxlRmllbGRzIiwiZGF0YVR5cGUiLCJfcmVtb3ZlQXZhaWxhYmxlRmllbGRzIiwiaSIsIl9hcHBlbmRBdmFpbGFibGVGaWVsZCIsImxhYmVsIiwicmVxdWlyZWQiLCJkZXNjcmlwdGlvbiIsInBvcG92ZXIiLCIkY29udGFpbmVyIiwiZW1wdHkiLCIkZmllbGQiLCJoZWxwQm94Q29udGVudCIsIiRoZWxwQm94IiwiJGFwcGVuZFRvIiwiZmllbGRUZXh0IiwiX2FwcGVuZEhlbHBCb3giLCJhcHBlbmRUbyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7Ozs7Ozs7Ozs7O3FqQkNoRUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkE7Ozs7Ozs7O0FBRUEsSUFBTUEsSUFBSUMsT0FBT0QsQ0FBakI7O0lBRXFCRSxVO0FBQ25CLHdCQUFjO0FBQUE7O0FBQUE7O0FBQ1osUUFBSUMseUJBQUo7O0FBRUFILE1BQUUsNEJBQUYsRUFBZ0NJLEVBQWhDLENBQW1DLE9BQW5DLEVBQTRDO0FBQUEsYUFBTSxNQUFLQyx1QkFBTCxFQUFOO0FBQUEsS0FBNUM7QUFDQUwsTUFBRSxtQ0FBRixFQUF1Q0ksRUFBdkMsQ0FBMEMsT0FBMUMsRUFBbUQ7QUFBQSxhQUFNLE1BQUtFLHdCQUFMLEVBQU47QUFBQSxLQUFuRDtBQUNBTixNQUFFLG1CQUFGLEVBQXVCSSxFQUF2QixDQUEwQixPQUExQixFQUFtQyxrQkFBbkMsRUFBdUQsVUFBQ0csS0FBRDtBQUFBLGFBQVcsTUFBS0MsdUJBQUwsQ0FBNkJELEtBQTdCLENBQVg7QUFBQSxLQUF2RDtBQUNB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V0FIZDtBQUtEOztBQUVEUCx5QkFBaUJ4RixJQUFqQixDQUFzQix5QkFBdEIsRUFBaURnRyxPQUFqRDtBQUNELE9BbEJEO0FBbUJEOztBQUVEOzs7Ozs7Ozs7MkNBTXVCQyxVLEVBQVk7QUFDakNBLGlCQUFXakcsSUFBWCxDQUFnQix5QkFBaEIsRUFBMkNnRyxPQUEzQyxDQUFtRCxNQUFuRDtBQUNBQyxpQkFBV0MsS0FBWDtBQUNEOztBQUVEOzs7Ozs7Ozs7O21DQU9lQyxNLEVBQVFDLGMsRUFBZ0I7QUFDckMsVUFBSUMsV0FBV25ILEVBQUUsc0NBQUYsRUFBMEN5RSxLQUExQyxFQUFmOztBQUVBMEMsZUFBU3pDLElBQVQsQ0FBYyxjQUFkLEVBQThCd0MsY0FBOUI7QUFDQUMsZUFBU3RGLFdBQVQsQ0FBcUIsNENBQXJCO0FBQ0FvRixhQUFPNUQsTUFBUCxDQUFjOEQsUUFBZDtBQUNEOztBQUVEOzs7Ozs7Ozs7OzswQ0FRc0JDLFMsRUFBV0MsUyxFQUFXSCxjLEVBQWdCO0FBQzFELFVBQUlELFNBQVNqSCxFQUFFLDhCQUFGLEVBQWtDeUUsS0FBbEMsRUFBYjs7QUFFQXdDLGFBQU85RixJQUFQLENBQVlrRyxTQUFaOztBQUVBLFVBQUlILGNBQUosRUFBb0I7QUFDbEI7QUFDQSxhQUFLSSxjQUFMLENBQW9CTCxNQUFwQixFQUE0QkMsY0FBNUI7QUFDRDs7QUFFREQsYUFBT3BGLFdBQVAsQ0FBbUIsb0NBQW5CO0FBQ0FvRixhQUFPTSxRQUFQLENBQWdCSCxTQUFoQjtBQUNEOzs7Ozs7a0JBcEtrQmpILGU7Ozs7Ozs7Ozs7QUNackI7Ozs7OztBQUVBLElBQU1ILElBQUlDLE9BQU9ELENBQWpCLEMsQ0EzQkE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUE2QkFBLEVBQUUsWUFBTTtBQUNOLE1BQUlFLG9CQUFKO0FBQ0QsQ0FGRCxFIiwiZmlsZSI6ImltcG9ydHMuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAzMjkpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDVkOTk5MDk0ZDExYWVmZjBiNTgyIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IEZvcm1GaWVsZFRvZ2dsZSBmcm9tIFwiLi9Gb3JtRmllbGRUb2dnbGVcIjtcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBJbXBvcnRQYWdlIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgbmV3IEZvcm1GaWVsZFRvZ2dsZSgpO1xuXG4gICAgJCgnLmpzLWZyb20tZmlsZXMtaGlzdG9yeS1idG4nKS5vbignY2xpY2snLCAoKSA9PiB0aGlzLnNob3dGaWxlc0hpc3RvcnlIYW5kbGVyKCkpO1xuICAgICQoJy5qcy1jbG9zZS1maWxlcy1oaXN0b3J5LWJsb2NrLWJ0bicpLm9uKCdjbGljaycsICgpID0+IHRoaXMuY2xvc2VGaWxlc0hpc3RvcnlIYW5kbGVyKCkpO1xuICAgICQoJyNmaWxlSGlzdG9yeVRhYmxlJykub24oJ2NsaWNrJywgJy5qcy11c2UtZmlsZS1idG4nLCAoZXZlbnQpID0+IHRoaXMudXNlRmlsZUZyb21GaWxlc0hpc3RvcnkoZXZlbnQpKTtcbiAgICAkKCcuanMtY2hhbmdlLWltcG9ydC1maWxlLWJ0bicpLm9uKCdjbGljaycsICgpID0+IHRoaXMuY2hhbmdlSW1wb3J0RmlsZUhhbmRsZXIoKSk7XG4gICAgJCgnLmpzLWltcG9ydC1maWxlJykub24oJ2NoYW5nZScsICgpID0+IHRoaXMudXBsb2FkRmlsZSgpKTtcblxuICAgIHRoaXMudG9nZ2xlU2VsZWN0ZWRGaWxlKCk7XG4gICAgdGhpcy5oYW5kbGVTdWJtaXQoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgc3VibWl0IGFuZCBhZGQgY29uZmlybSBib3ggaW4gY2FzZSB0aGUgdG9nZ2xlIGJ1dHRvbiBhYm91dFxuICAgKiBkZWxldGluZyBhbGwgZW50aXRpZXMgYmVmb3JlIGltcG9ydCBpcyBjaGVja2VkXG4gICAqL1xuICBoYW5kbGVTdWJtaXQoKSB7XG4gICAgJCgnLmpzLWltcG9ydC1mb3JtJykub24oJ3N1Ym1pdCcsIGZ1bmN0aW9uKCkge1xuICAgICAgY29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgaWYgKCR0aGlzLmZpbmQoJ2lucHV0W25hbWU9XCJ0cnVuY2F0ZVwiXTpjaGVja2VkJykudmFsKCkgPT09ICcxJykge1xuICAgICAgICByZXR1cm4gY29uZmlybShgJHskdGhpcy5kYXRhKCdkZWxldGUtY29uZmlybS1tZXNzYWdlJyl9ICR7JC50cmltKCQoJyNlbnRpdHkgPiBvcHRpb246c2VsZWN0ZWQnKS50ZXh0KCkudG9Mb3dlckNhc2UoKSl9P2ApO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIHNlbGVjdGVkIGZpbGUgbmFtZXMgZXhpc3RzIGFuZCBpZiBzbywgdGhlbiBkaXNwbGF5IGl0XG4gICAqL1xuICB0b2dnbGVTZWxlY3RlZEZpbGUoKSB7XG4gICAgbGV0IHNlbGVjdEZpbGVuYW1lID0gJCgnI2NzdicpLnZhbCgpO1xuICAgIGlmIChzZWxlY3RGaWxlbmFtZS5sZW5ndGggPiAwKSB7XG4gICAgICB0aGlzLnNob3dJbXBvcnRGaWxlQWxlcnQoc2VsZWN0RmlsZW5hbWUpO1xuICAgICAgdGhpcy5oaWRlRmlsZVVwbG9hZEJsb2NrKCk7XG4gICAgfVxuICB9XG5cbiAgY2hhbmdlSW1wb3J0RmlsZUhhbmRsZXIoKSB7XG4gICAgdGhpcy5oaWRlSW1wb3J0RmlsZUFsZXJ0KCk7XG4gICAgdGhpcy5zaG93RmlsZVVwbG9hZEJsb2NrKCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvdyBmaWxlcyBoaXN0b3J5IGV2ZW50IGhhbmRsZXJcbiAgICovXG4gIHNob3dGaWxlc0hpc3RvcnlIYW5kbGVyKCkge1xuICAgIHRoaXMuc2hvd0ZpbGVzSGlzdG9yeSgpO1xuICAgIHRoaXMuaGlkZUZpbGVVcGxvYWRCbG9jaygpO1xuICB9XG5cbiAgLyoqXG4gICAqIENsb3NlIGZpbGVzIGhpc3RvcnkgZXZlbnQgaGFuZGxlclxuICAgKi9cbiAgY2xvc2VGaWxlc0hpc3RvcnlIYW5kbGVyKCkge1xuICAgIHRoaXMuY2xvc2VGaWxlc0hpc3RvcnkoKTtcbiAgICB0aGlzLnNob3dGaWxlVXBsb2FkQmxvY2soKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IGZpbGVzIGhpc3RvcnkgYmxvY2tcbiAgICovXG4gIHNob3dGaWxlc0hpc3RvcnkoKSB7XG4gICAgJCgnLmpzLWZpbGVzLWhpc3RvcnktYmxvY2snKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZSBmaWxlcyBoaXN0b3J5IGJsb2NrXG4gICAqL1xuICBjbG9zZUZpbGVzSGlzdG9yeSgpIHtcbiAgICAkKCcuanMtZmlsZXMtaGlzdG9yeS1ibG9jaycpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiAgUHJlZmlsbCBoaWRkZW4gZmlsZSBpbnB1dCB3aXRoIHNlbGVjdGVkIGZpbGUgbmFtZSBmcm9tIGhpc3RvcnlcbiAgICovXG4gIHVzZUZpbGVGcm9tRmlsZXNIaXN0b3J5KGV2ZW50KSB7XG4gICAgbGV0IGZpbGVuYW1lID0gJChldmVudC50YXJnZXQpLmNsb3Nlc3QoJy5idG4tZ3JvdXAnKS5kYXRhKCdmaWxlJyk7XG5cbiAgICAkKCcuanMtaW1wb3J0LWZpbGUtaW5wdXQnKS52YWwoZmlsZW5hbWUpO1xuXG4gICAgdGhpcy5zaG93SW1wb3J0RmlsZUFsZXJ0KGZpbGVuYW1lKTtcbiAgICB0aGlzLmNsb3NlRmlsZXNIaXN0b3J5KCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvdyBhbGVydCB3aXRoIGltcG9ydGVkIGZpbGUgbmFtZVxuICAgKi9cbiAgc2hvd0ltcG9ydEZpbGVBbGVydChmaWxlbmFtZSkge1xuICAgICQoJy5qcy1pbXBvcnQtZmlsZS1hbGVydCcpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAkKCcuanMtaW1wb3J0LWZpbGUnKS50ZXh0KGZpbGVuYW1lKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyBzZWxlY3RlZCBpbXBvcnQgZmlsZSBhbGVydFxuICAgKi9cbiAgaGlkZUltcG9ydEZpbGVBbGVydCgpIHtcbiAgICAkKCcuanMtaW1wb3J0LWZpbGUtYWxlcnQnKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgaW1wb3J0IGZpbGUgdXBsb2FkIGJsb2NrXG4gICAqL1xuICBoaWRlRmlsZVVwbG9hZEJsb2NrKCkge1xuICAgICQoJy5qcy1maWxlLXVwbG9hZC1mb3JtLWdyb3VwJykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIGltcG9ydCBmaWxlIHVwbG9hZCBibG9ja1xuICAgKi9cbiAgc2hvd0ZpbGVVcGxvYWRCbG9jaygpIHtcbiAgICAkKCcuanMtZmlsZS11cGxvYWQtZm9ybS1ncm91cCcpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBNYWtlIGZpbGUgaGlzdG9yeSBidXR0b24gY2xpY2thYmxlXG4gICAqL1xuICBlbmFibGVGaWxlc0hpc3RvcnlCdG4oKSB7XG4gICAgJCgnLmpzLWZyb20tZmlsZXMtaGlzdG9yeS1idG4nKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3cgZXJyb3IgbWVzc2FnZSBpZiBmaWxlIHVwbG9hZGluZyBmYWlsZWRcbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IGZpbGVOYW1lXG4gICAqIEBwYXJhbSB7aW50ZWdlcn0gZmlsZVNpemVcbiAgICogQHBhcmFtIHtzdHJpbmd9IG1lc3NhZ2VcbiAgICovXG4gIHNob3dJbXBvcnRGaWxlRXJyb3IoZmlsZU5hbWUsIGZpbGVTaXplLCBtZXNzYWdlKSB7XG4gICAgY29uc3QgJGFsZXJ0ID0gJCgnLmpzLWltcG9ydC1maWxlLWVycm9yJyk7XG5cbiAgICBjb25zdCBmaWxlRGF0YSA9IGZpbGVOYW1lICsgJyAoJyArIHRoaXMuaHVtYW5pemVTaXplKGZpbGVTaXplKSArICcpJztcblxuICAgICRhbGVydC5maW5kKCcuanMtZmlsZS1kYXRhJykuaHRtbChmaWxlRGF0YSk7XG4gICAgJGFsZXJ0LmZpbmQoJy5qcy1lcnJvci1tZXNzYWdlJykuaHRtbChtZXNzYWdlKTtcbiAgICAkYWxlcnQucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGUgZmlsZSB1cGxvYWRpbmcgZXJyb3JcbiAgICovXG4gIGhpZGVJbXBvcnRGaWxlRXJyb3IoKSB7XG4gICAgY29uc3QgJGFsZXJ0ID0gJCgnLmpzLWltcG9ydC1maWxlLWVycm9yJyk7XG4gICAgJGFsZXJ0LmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IGZpbGUgc2l6ZSBpbiBodW1hbiByZWFkYWJsZSBmb3JtYXRcbiAgICpcbiAgICogQHBhcmFtIHtpbnR9IGJ5dGVzXG4gICAqXG4gICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAqL1xuICBodW1hbml6ZVNpemUoYnl0ZXMpIHtcbiAgICBpZiAodHlwZW9mIGJ5dGVzICE9PSAnbnVtYmVyJykge1xuICAgICAgcmV0dXJuICcnO1xuICAgIH1cblxuICAgIGlmIChieXRlcyA+PSAxMDAwMDAwMDAwKSB7XG4gICAgICByZXR1cm4gKGJ5dGVzIC8gMTAwMDAwMDAwMCkudG9GaXhlZCgyKSArICcgR0InO1xuICAgIH1cblxuICAgIGlmIChieXRlcyA+PSAxMDAwMDAwKSB7XG4gICAgICByZXR1cm4gKGJ5dGVzIC8gMTAwMDAwMCkudG9GaXhlZCgyKSArICcgTUInO1xuICAgIH1cblxuICAgIHJldHVybiAoYnl0ZXMgLyAxMDAwKS50b0ZpeGVkKDIpICsgJyBLQic7XG4gIH1cblxuICAvKipcbiAgICogVXBsb2FkIHNlbGVjdGVkIGltcG9ydCBmaWxlXG4gICAqL1xuICB1cGxvYWRGaWxlKCkge1xuICAgIHRoaXMuaGlkZUltcG9ydEZpbGVFcnJvcigpO1xuXG4gICAgY29uc3QgJGlucHV0ID0gJCgnI2ZpbGUnKTtcbiAgICBjb25zdCB1cGxvYWRlZEZpbGUgPSAkaW5wdXQucHJvcCgnZmlsZXMnKVswXTtcblxuICAgIGNvbnN0IG1heFVwbG9hZFNpemUgPSAkaW5wdXQuZGF0YSgnbWF4LWZpbGUtdXBsb2FkLXNpemUnKTtcbiAgICBpZiAobWF4VXBsb2FkU2l6ZSA8IHVwbG9hZGVkRmlsZS5zaXplKSB7XG4gICAgICB0aGlzLnNob3dJbXBvcnRGaWxlRXJyb3IodXBsb2FkZWRGaWxlLm5hbWUsIHVwbG9hZGVkRmlsZS5zaXplLCAnRmlsZSBpcyB0b28gbGFyZ2UnKTtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCBkYXRhID0gbmV3IEZvcm1EYXRhKCk7XG4gICAgZGF0YS5hcHBlbmQoJ2ZpbGUnLCB1cGxvYWRlZEZpbGUpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgIHVybDogJCgnLmpzLWltcG9ydC1mb3JtJykuZGF0YSgnZmlsZS11cGxvYWQtdXJsJyksXG4gICAgICBkYXRhOiBkYXRhLFxuICAgICAgY2FjaGU6IGZhbHNlLFxuICAgICAgY29udGVudFR5cGU6IGZhbHNlLFxuICAgICAgcHJvY2Vzc0RhdGE6IGZhbHNlLFxuICAgIH0pLnRoZW4ocmVzcG9uc2UgPT4ge1xuICAgICAgaWYgKHJlc3BvbnNlLmVycm9yKSB7XG4gICAgICAgIHRoaXMuc2hvd0ltcG9ydEZpbGVFcnJvcih1cGxvYWRlZEZpbGUubmFtZSwgdXBsb2FkZWRGaWxlLnNpemUsIHJlc3BvbnNlLmVycm9yKTtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBsZXQgZmlsZW5hbWUgPSByZXNwb25zZS5maWxlLm5hbWU7XG5cbiAgICAgICQoJy5qcy1pbXBvcnQtZmlsZS1pbnB1dCcpLnZhbChmaWxlbmFtZSk7XG5cbiAgICAgIHRoaXMuc2hvd0ltcG9ydEZpbGVBbGVydChmaWxlbmFtZSk7XG4gICAgICB0aGlzLmhpZGVGaWxlVXBsb2FkQmxvY2soKTtcbiAgICAgIHRoaXMuYWRkRmlsZVRvSGlzdG9yeVRhYmxlKGZpbGVuYW1lKTtcbiAgICAgIHRoaXMuZW5hYmxlRmlsZXNIaXN0b3J5QnRuKCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBuZXcgcm93IGluIGZpbGVzIGhpc3RvcnkgdGFibGVcbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IGZpbGVuYW1lXG4gICAqL1xuICBhZGRGaWxlVG9IaXN0b3J5VGFibGUoZmlsZW5hbWUpIHtcbiAgICBjb25zdCAkdGFibGUgPSAkKCcjZmlsZUhpc3RvcnlUYWJsZScpO1xuXG4gICAgbGV0IGJhc2VEZWxldGVVcmwgPSAkdGFibGUuZGF0YSgnZGVsZXRlLWZpbGUtdXJsJyk7XG4gICAgbGV0IGRlbGV0ZVVybCA9IGJhc2VEZWxldGVVcmwgKyAnJmZpbGVuYW1lPScgKyBlbmNvZGVVUklDb21wb25lbnQoZmlsZW5hbWUpO1xuXG4gICAgbGV0IGJhc2VEb3dubG9hZFVybCA9ICR0YWJsZS5kYXRhKCdkb3dubG9hZC1maWxlLXVybCcpO1xuICAgIGxldCBkb3dubG9hZFVybCA9IGJhc2VEb3dubG9hZFVybCArICcmZmlsZW5hbWU9JyArIGVuY29kZVVSSUNvbXBvbmVudChmaWxlbmFtZSk7XG5cbiAgICBsZXQgJHRlbXBsYXRlID0gJHRhYmxlLmZpbmQoJ3RyOmZpcnN0JykuY2xvbmUoKTtcblxuICAgICR0ZW1wbGF0ZS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgJHRlbXBsYXRlLmZpbmQoJ3RkOmZpcnN0JykudGV4dChmaWxlbmFtZSk7XG4gICAgJHRlbXBsYXRlLmZpbmQoJy5idG4tZ3JvdXAnKS5hdHRyKCdkYXRhLWZpbGUnLCBmaWxlbmFtZSk7XG4gICAgJHRlbXBsYXRlLmZpbmQoJy5qcy1kZWxldGUtZmlsZS1idG4nKS5hdHRyKCdocmVmJywgZGVsZXRlVXJsKTtcbiAgICAkdGVtcGxhdGUuZmluZCgnLmpzLWRvd25sb2FkLWZpbGUtYnRuJykuYXR0cignaHJlZicsIGRvd25sb2FkVXJsKTtcblxuICAgICR0YWJsZS5maW5kKCd0Ym9keScpLmFwcGVuZCgkdGVtcGxhdGUpO1xuXG4gICAgbGV0IGZpbGVzTnVtYmVyID0gJHRhYmxlLmZpbmQoJ3RyJykubGVuZ3RoIC0gMTtcbiAgICAkKCcuanMtZmlsZXMtaGlzdG9yeS1udW1iZXInKS50ZXh0KGZpbGVzTnVtYmVyKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvaW1wb3J0L0ltcG9ydFBhZ2UuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmNvbnN0IGVudGl0eUNhdGVnb3JpZXMgPSAwO1xuY29uc3QgZW50aXR5UHJvZHVjdHMgPSAxO1xuY29uc3QgZW50aXR5Q29tYmluYXRpb25zID0gMjtcbmNvbnN0IGVudGl0eUN1c3RvbWVycyA9IDM7XG5jb25zdCBlbnRpdHlBZGRyZXNzZXMgPSA0O1xuY29uc3QgZW50aXR5QnJhbmRzID0gNTtcbmNvbnN0IGVudGl0eVN1cHBsaWVycyA9IDY7XG5jb25zdCBlbnRpdHlBbGlhcyA9IDc7XG5jb25zdCBlbnRpdHlTdG9yZUNvbnRhY3RzID0gODtcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRm9ybUZpZWxkVG9nZ2xlIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgJCgnLmpzLWVudGl0eS1zZWxlY3QnKS5vbignY2hhbmdlJywgKCkgPT4gdGhpcy50b2dnbGVGb3JtKCkpO1xuXG4gICAgdGhpcy50b2dnbGVGb3JtKCk7XG4gIH1cblxuICB0b2dnbGVGb3JtKCkge1xuICAgIGxldCBzZWxlY3RlZE9wdGlvbiA9ICQoJyNlbnRpdHknKS5maW5kKCdvcHRpb246c2VsZWN0ZWQnKTtcbiAgICBsZXQgc2VsZWN0ZWRFbnRpdHkgPSBwYXJzZUludChzZWxlY3RlZE9wdGlvbi52YWwoKSk7XG4gICAgbGV0IGVudGl0eU5hbWUgPSBzZWxlY3RlZE9wdGlvbi50ZXh0KCkudG9Mb3dlckNhc2UoKTtcblxuICAgIHRoaXMudG9nZ2xlRW50aXR5QWxlcnQoc2VsZWN0ZWRFbnRpdHkpO1xuICAgIHRoaXMudG9nZ2xlRmllbGRzKHNlbGVjdGVkRW50aXR5LCBlbnRpdHlOYW1lKTtcbiAgICB0aGlzLmxvYWRBdmFpbGFibGVGaWVsZHMoc2VsZWN0ZWRFbnRpdHkpO1xuICB9XG5cbiAgLyoqXG4gICAqIFRvZ2dsZSBhbGVydCB3YXJuaW5nIGZvciBzZWxlY3RlZCBpbXBvcnQgZW50aXR5XG4gICAqXG4gICAqIEBwYXJhbSB7aW50fSBzZWxlY3RlZEVudGl0eVxuICAgKi9cbiAgdG9nZ2xlRW50aXR5QWxlcnQoc2VsZWN0ZWRFbnRpdHkpIHtcbiAgICBsZXQgJGFsZXJ0ID0gJCgnLmpzLWVudGl0eS1hbGVydCcpO1xuXG4gICAgaWYgKFtlbnRpdHlDYXRlZ29yaWVzLCBlbnRpdHlQcm9kdWN0c10uaW5jbHVkZXMoc2VsZWN0ZWRFbnRpdHkpKSB7XG4gICAgICAkYWxlcnQuc2hvdygpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkYWxlcnQuaGlkZSgpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBUb2dnbGUgYXZhaWxhYmxlIG9wdGlvbnMgZm9yIHNlbGVjdGVkIGVudGl0eVxuICAgKlxuICAgKiBAcGFyYW0ge2ludH0gc2VsZWN0ZWRFbnRpdHlcbiAgICogQHBhcmFtIHtzdHJpbmd9IGVudGl0eU5hbWVcbiAgICovXG4gIHRvZ2dsZUZpZWxkcyhzZWxlY3RlZEVudGl0eSwgZW50aXR5TmFtZSkge1xuICAgIGNvbnN0ICR0cnVuY2F0ZUZvcm1Hcm91cCA9ICQoJy5qcy10cnVuY2F0ZS1mb3JtLWdyb3VwJyk7XG4gICAgY29uc3QgJG1hdGNoUmVmRm9ybUdyb3VwID0gJCgnLmpzLW1hdGNoLXJlZi1mb3JtLWdyb3VwJyk7XG4gICAgY29uc3QgJHJlZ2VuZXJhdGVGb3JtR3JvdXAgPSAkKCcuanMtcmVnZW5lcmF0ZS1mb3JtLWdyb3VwJyk7XG4gICAgY29uc3QgJGZvcmNlSWRzRm9ybUdyb3VwID0gJCgnLmpzLWZvcmNlLWlkcy1mb3JtLWdyb3VwJyk7XG4gICAgY29uc3QgJGVudGl0eU5hbWVQbGFjZWhvbGRlciA9ICQoJy5qcy1lbnRpdHktbmFtZScpO1xuXG4gICAgaWYgKGVudGl0eVN0b3JlQ29udGFjdHMgPT09IHNlbGVjdGVkRW50aXR5KSB7XG4gICAgICAkdHJ1bmNhdGVGb3JtR3JvdXAuaGlkZSgpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkdHJ1bmNhdGVGb3JtR3JvdXAuc2hvdygpO1xuICAgIH1cblxuICAgIGlmIChbZW50aXR5UHJvZHVjdHMsIGVudGl0eUNvbWJpbmF0aW9uc10uaW5jbHVkZXMoc2VsZWN0ZWRFbnRpdHkpKSB7XG4gICAgICAkbWF0Y2hSZWZGb3JtR3JvdXAuc2hvdygpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkbWF0Y2hSZWZGb3JtR3JvdXAuaGlkZSgpO1xuICAgIH1cblxuICAgIGlmIChbXG4gICAgICBlbnRpdHlDYXRlZ29yaWVzLFxuICAgICAgZW50aXR5UHJvZHVjdHMsXG4gICAgICBlbnRpdHlCcmFuZHMsXG4gICAgICBlbnRpdHlTdXBwbGllcnMsXG4gICAgICBlbnRpdHlTdG9yZUNvbnRhY3RzXG4gICAgXS5pbmNsdWRlcyhzZWxlY3RlZEVudGl0eSlcbiAgICApIHtcbiAgICAgICRyZWdlbmVyYXRlRm9ybUdyb3VwLnNob3coKTtcbiAgICB9IGVsc2Uge1xuICAgICAgJHJlZ2VuZXJhdGVGb3JtR3JvdXAuaGlkZSgpO1xuICAgIH1cblxuICAgIGlmIChbXG4gICAgICBlbnRpdHlDYXRlZ29yaWVzLFxuICAgICAgZW50aXR5UHJvZHVjdHMsXG4gICAgICBlbnRpdHlDdXN0b21lcnMsXG4gICAgICBlbnRpdHlBZGRyZXNzZXMsXG4gICAgICBlbnRpdHlCcmFuZHMsXG4gICAgICBlbnRpdHlTdXBwbGllcnMsXG4gICAgICBlbnRpdHlTdG9yZUNvbnRhY3RzLFxuICAgICAgZW50aXR5QWxpYXNcbiAgICBdLmluY2x1ZGVzKHNlbGVjdGVkRW50aXR5KVxuICAgICkge1xuICAgICAgJGZvcmNlSWRzRm9ybUdyb3VwLnNob3coKTtcbiAgICB9IGVsc2Uge1xuICAgICAgJGZvcmNlSWRzRm9ybUdyb3VwLmhpZGUoKTtcbiAgICB9XG5cbiAgICAkZW50aXR5TmFtZVBsYWNlaG9sZGVyLmh0bWwoZW50aXR5TmFtZSk7XG4gIH1cblxuICAvKipcbiAgICogTG9hZCBhdmFpbGFibGUgZmllbGRzIGZvciBnaXZlbiBlbnRpdHlcbiAgICpcbiAgICogQHBhcmFtIHtpbnR9IGVudGl0eVxuICAgKi9cbiAgbG9hZEF2YWlsYWJsZUZpZWxkcyhlbnRpdHkpIHtcbiAgICBjb25zdCAkYXZhaWxhYmxlRmllbGRzID0gJCgnLmpzLWF2YWlsYWJsZS1maWVsZHMnKTtcblxuICAgICQuYWpheCh7XG4gICAgICB1cmw6ICRhdmFpbGFibGVGaWVsZHMuZGF0YSgndXJsJyksXG4gICAgICBkYXRhOiB7XG4gICAgICAgIGVudGl0eTogZW50aXR5XG4gICAgICB9LFxuICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICB9KS50aGVuKHJlc3BvbnNlID0+IHtcbiAgICAgIHRoaXMuX3JlbW92ZUF2YWlsYWJsZUZpZWxkcygkYXZhaWxhYmxlRmllbGRzKTtcblxuICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCByZXNwb25zZS5sZW5ndGg7IGkrKykge1xuICAgICAgICB0aGlzLl9hcHBlbmRBdmFpbGFibGVGaWVsZChcbiAgICAgICAgICAkYXZhaWxhYmxlRmllbGRzLFxuICAgICAgICAgIHJlc3BvbnNlW2ldLmxhYmVsICsgKHJlc3BvbnNlW2ldLnJlcXVpcmVkID8gJyonIDogJycpLFxuICAgICAgICAgIHJlc3BvbnNlW2ldLmRlc2NyaXB0aW9uXG4gICAgICAgICk7XG4gICAgICB9XG5cbiAgICAgICRhdmFpbGFibGVGaWVsZHMuZmluZCgnW2RhdGEtdG9nZ2xlPVwicG9wb3ZlclwiXScpLnBvcG92ZXIoKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW1vdmUgYXZhaWxhYmxlIGZpZWxkcyBjb250ZW50IGZyb20gZ2l2ZW4gY29udGFpbmVyLlxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGNvbnRhaW5lclxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbW92ZUF2YWlsYWJsZUZpZWxkcygkY29udGFpbmVyKSB7XG4gICAgJGNvbnRhaW5lci5maW5kKCdbZGF0YS10b2dnbGU9XCJwb3BvdmVyXCJdJykucG9wb3ZlcignaGlkZScpO1xuICAgICRjb250YWluZXIuZW1wdHkoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBcHBlbmQgYSBoZWxwIGJveCB0byBnaXZlbiBmaWVsZC5cbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRmaWVsZFxuICAgKiBAcGFyYW0ge1N0cmluZ30gaGVscEJveENvbnRlbnRcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9hcHBlbmRIZWxwQm94KCRmaWVsZCwgaGVscEJveENvbnRlbnQpIHtcbiAgICBsZXQgJGhlbHBCb3ggPSAkKCcuanMtYXZhaWxhYmxlLWZpZWxkLXBvcG92ZXItdGVtcGxhdGUnKS5jbG9uZSgpO1xuXG4gICAgJGhlbHBCb3guYXR0cignZGF0YS1jb250ZW50JywgaGVscEJveENvbnRlbnQpO1xuICAgICRoZWxwQm94LnJlbW92ZUNsYXNzKCdqcy1hdmFpbGFibGUtZmllbGQtcG9wb3Zlci10ZW1wbGF0ZSBkLW5vbmUnKTtcbiAgICAkZmllbGQuYXBwZW5kKCRoZWxwQm94KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBcHBlbmQgYXZhaWxhYmxlIGZpZWxkIHRvIGdpdmVuIGNvbnRhaW5lci5cbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRhcHBlbmRUbyBmaWVsZCB3aWxsIGJlIGFwcGVuZGVkIHRvIHRoaXMgY29udGFpbmVyLlxuICAgKiBAcGFyYW0ge1N0cmluZ30gZmllbGRUZXh0XG4gICAqIEBwYXJhbSB7U3RyaW5nfSBoZWxwQm94Q29udGVudFxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2FwcGVuZEF2YWlsYWJsZUZpZWxkKCRhcHBlbmRUbywgZmllbGRUZXh0LCBoZWxwQm94Q29udGVudCkge1xuICAgIGxldCAkZmllbGQgPSAkKCcuanMtYXZhaWxhYmxlLWZpZWxkLXRlbXBsYXRlJykuY2xvbmUoKTtcblxuICAgICRmaWVsZC50ZXh0KGZpZWxkVGV4dCk7XG5cbiAgICBpZiAoaGVscEJveENvbnRlbnQpIHtcbiAgICAgIC8vIEFwcGVuZCBoZWxwIGJveCBuZXh0IHRvIHRoZSBmaWVsZFxuICAgICAgdGhpcy5fYXBwZW5kSGVscEJveCgkZmllbGQsIGhlbHBCb3hDb250ZW50KTtcbiAgICB9XG5cbiAgICAkZmllbGQucmVtb3ZlQ2xhc3MoJ2pzLWF2YWlsYWJsZS1maWVsZC10ZW1wbGF0ZSBkLW5vbmUnKTtcbiAgICAkZmllbGQuYXBwZW5kVG8oJGFwcGVuZFRvKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvaW1wb3J0L0Zvcm1GaWVsZFRvZ2dsZS5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBJbXBvcnRQYWdlIGZyb20gJy4vSW1wb3J0UGFnZSc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gIG5ldyBJbXBvcnRQYWdlKCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2ltcG9ydC9pbmRleC5qcyJdLCJzb3VyY2VSb290IjoiIn0=