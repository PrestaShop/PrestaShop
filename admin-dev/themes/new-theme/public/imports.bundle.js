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
/******/ 	return __webpack_require__(__webpack_require__.s = 500);
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

/***/ 412:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _FormFieldToggle = __webpack_require__(499);

var _FormFieldToggle2 = _interopRequireDefault(_FormFieldToggle);

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

var ImportPage = function () {
  function ImportPage() {
    var _this = this;

    (0, _classCallCheck3.default)(this, ImportPage);

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


  (0, _createClass3.default)(ImportPage, [{
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

      $alert.find('.js-file-data').text(fileData);
      $alert.find('.js-error-message').text(message);
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

/***/ 499:
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

    (0, _classCallCheck3.default)(this, FormFieldToggle);

    $('.js-entity-select').on('change', function () {
      return _this.toggleForm();
    });

    this.toggleForm();
  }

  (0, _createClass3.default)(FormFieldToggle, [{
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

/***/ 5:
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self : Function('return this')();
if(typeof __g == 'number')__g = global; // eslint-disable-line no-undef

/***/ }),

/***/ 500:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _ImportPage = __webpack_require__(412);

var _ImportPage2 = _interopRequireDefault(_ImportPage);

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
  new _ImportPage2.default();
});

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgM2E2MTdjZWQyOWViY2NiNmExZDA/MTI1MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY2xhc3NDYWxsQ2hlY2suanM/MjFhZioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NyZWF0ZUNsYXNzLmpzPzFkZmUqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oaWRlLmpzP2E2ZGEqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2FuLW9iamVjdC5qcz8wZGEzKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19wcm9wZXJ0eS1kZXNjLmpzPzFlODYqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2N0eC5qcz9jZTAwKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1wcmltaXRpdmUuanM/NDlhNCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZG9tLWNyZWF0ZS5qcz9hYjQ0KioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pZTgtZG9tLWRlZmluZS5qcz9iZDFmKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hLWZ1bmN0aW9uLmpzP2Q1M2UqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanM/NWY3MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZGVzY3JpcHRvcnMuanM/NzA1MSoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qcz9iN2Q4KioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5LmpzP2M4MmMqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvcmUuanM/MWI2MioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXMtb2JqZWN0LmpzPzI0YzgqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2ltcG9ydC9JbXBvcnRQYWdlLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2ltcG9ydC9Gb3JtRmllbGRUb2dnbGUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZ2xvYmFsLmpzPzc3YWEqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2ltcG9ydC9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZHAuanM/NDExNioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZmFpbHMuanM/OTM1ZCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZXhwb3J0LmpzP2VjZTIqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJJbXBvcnRQYWdlIiwiRm9ybUZpZWxkVG9nZ2xlIiwib24iLCJzaG93RmlsZXNIaXN0b3J5SGFuZGxlciIsImNsb3NlRmlsZXNIaXN0b3J5SGFuZGxlciIsImV2ZW50IiwidXNlRmlsZUZyb21GaWxlc0hpc3RvcnkiLCJjaGFuZ2VJbXBvcnRGaWxlSGFuZGxlciIsInVwbG9hZEZpbGUiLCJ0b2dnbGVTZWxlY3RlZEZpbGUiLCJoYW5kbGVTdWJtaXQiLCIkdGhpcyIsImZpbmQiLCJ2YWwiLCJjb25maXJtIiwiZGF0YSIsInRyaW0iLCJ0ZXh0IiwidG9Mb3dlckNhc2UiLCJzZWxlY3RGaWxlbmFtZSIsImxlbmd0aCIsInNob3dJbXBvcnRGaWxlQWxlcnQiLCJoaWRlRmlsZVVwbG9hZEJsb2NrIiwiaGlkZUltcG9ydEZpbGVBbGVydCIsInNob3dGaWxlVXBsb2FkQmxvY2siLCJzaG93RmlsZXNIaXN0b3J5IiwiY2xvc2VGaWxlc0hpc3RvcnkiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiZmlsZW5hbWUiLCJ0YXJnZXQiLCJjbG9zZXN0IiwicmVtb3ZlQXR0ciIsImZpbGVOYW1lIiwiZmlsZVNpemUiLCJtZXNzYWdlIiwiJGFsZXJ0IiwiZmlsZURhdGEiLCJodW1hbml6ZVNpemUiLCJieXRlcyIsInRvRml4ZWQiLCJoaWRlSW1wb3J0RmlsZUVycm9yIiwiJGlucHV0IiwidXBsb2FkZWRGaWxlIiwicHJvcCIsIm1heFVwbG9hZFNpemUiLCJzaXplIiwic2hvd0ltcG9ydEZpbGVFcnJvciIsIm5hbWUiLCJGb3JtRGF0YSIsImFwcGVuZCIsImFqYXgiLCJ0eXBlIiwidXJsIiwiY2FjaGUiLCJjb250ZW50VHlwZSIsInByb2Nlc3NEYXRhIiwidGhlbiIsInJlc3BvbnNlIiwiZXJyb3IiLCJmaWxlIiwiYWRkRmlsZVRvSGlzdG9yeVRhYmxlIiwiZW5hYmxlRmlsZXNIaXN0b3J5QnRuIiwiJHRhYmxlIiwiYmFzZURlbGV0ZVVybCIsImRlbGV0ZVVybCIsImVuY29kZVVSSUNvbXBvbmVudCIsImJhc2VEb3dubG9hZFVybCIsImRvd25sb2FkVXJsIiwiJHRlbXBsYXRlIiwiY2xvbmUiLCJhdHRyIiwiZmlsZXNOdW1iZXIiLCJlbnRpdHlDYXRlZ29yaWVzIiwiZW50aXR5UHJvZHVjdHMiLCJlbnRpdHlDb21iaW5hdGlvbnMiLCJlbnRpdHlDdXN0b21lcnMiLCJlbnRpdHlBZGRyZXNzZXMiLCJlbnRpdHlCcmFuZHMiLCJlbnRpdHlTdXBwbGllcnMiLCJlbnRpdHlBbGlhcyIsImVudGl0eVN0b3JlQ29udGFjdHMiLCJ0b2dnbGVGb3JtIiwic2VsZWN0ZWRPcHRpb24iLCJzZWxlY3RlZEVudGl0eSIsInBhcnNlSW50IiwiZW50aXR5TmFtZSIsInRvZ2dsZUVudGl0eUFsZXJ0IiwidG9nZ2xlRmllbGRzIiwibG9hZEF2YWlsYWJsZUZpZWxkcyIsImluY2x1ZGVzIiwic2hvdyIsImhpZGUiLCIkdHJ1bmNhdGVGb3JtR3JvdXAiLCIkbWF0Y2hSZWZGb3JtR3JvdXAiLCIkcmVnZW5lcmF0ZUZvcm1Hcm91cCIsIiRmb3JjZUlkc0Zvcm1Hcm91cCIsIiRlbnRpdHlOYW1lUGxhY2Vob2xkZXIiLCJodG1sIiwiZW50aXR5IiwiJGF2YWlsYWJsZUZpZWxkcyIsImRhdGFUeXBlIiwiX3JlbW92ZUF2YWlsYWJsZUZpZWxkcyIsImkiLCJfYXBwZW5kQXZhaWxhYmxlRmllbGQiLCJsYWJlbCIsInJlcXVpcmVkIiwiZGVzY3JpcHRpb24iLCJwb3BvdmVyIiwiJGNvbnRhaW5lciIsImVtcHR5IiwiJGZpZWxkIiwiaGVscEJveENvbnRlbnQiLCIkaGVscEJveCIsIiRhcHBlbmRUbyIsImZpZWxkVGV4dCIsIl9hcHBlbmRIZWxwQm94IiwiYXBwZW5kVG8iXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7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QWU7QUFDZixnQkFBZ0I7QUFDaEIseUIiLCJmaWxlIjoiaW1wb3J0cy5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDUwMCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgM2E2MTdjZWQyOWViY2NiNmExZDAiLCJcInVzZSBzdHJpY3RcIjtcblxuZXhwb3J0cy5fX2VzTW9kdWxlID0gdHJ1ZTtcblxuZXhwb3J0cy5kZWZhdWx0ID0gZnVuY3Rpb24gKGluc3RhbmNlLCBDb25zdHJ1Y3Rvcikge1xuICBpZiAoIShpbnN0YW5jZSBpbnN0YW5jZW9mIENvbnN0cnVjdG9yKSkge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoXCJDYW5ub3QgY2FsbCBhIGNsYXNzIGFzIGEgZnVuY3Rpb25cIik7XG4gIH1cbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9jbGFzc0NhbGxDaGVjay5qc1xuLy8gbW9kdWxlIGlkID0gMFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIlwidXNlIHN0cmljdFwiO1xuXG5leHBvcnRzLl9fZXNNb2R1bGUgPSB0cnVlO1xuXG52YXIgX2RlZmluZVByb3BlcnR5ID0gcmVxdWlyZShcIi4uL2NvcmUtanMvb2JqZWN0L2RlZmluZS1wcm9wZXJ0eVwiKTtcblxudmFyIF9kZWZpbmVQcm9wZXJ0eTIgPSBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KF9kZWZpbmVQcm9wZXJ0eSk7XG5cbmZ1bmN0aW9uIF9pbnRlcm9wUmVxdWlyZURlZmF1bHQob2JqKSB7IHJldHVybiBvYmogJiYgb2JqLl9fZXNNb2R1bGUgPyBvYmogOiB7IGRlZmF1bHQ6IG9iaiB9OyB9XG5cbmV4cG9ydHMuZGVmYXVsdCA9IGZ1bmN0aW9uICgpIHtcbiAgZnVuY3Rpb24gZGVmaW5lUHJvcGVydGllcyh0YXJnZXQsIHByb3BzKSB7XG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBwcm9wcy5sZW5ndGg7IGkrKykge1xuICAgICAgdmFyIGRlc2NyaXB0b3IgPSBwcm9wc1tpXTtcbiAgICAgIGRlc2NyaXB0b3IuZW51bWVyYWJsZSA9IGRlc2NyaXB0b3IuZW51bWVyYWJsZSB8fCBmYWxzZTtcbiAgICAgIGRlc2NyaXB0b3IuY29uZmlndXJhYmxlID0gdHJ1ZTtcbiAgICAgIGlmIChcInZhbHVlXCIgaW4gZGVzY3JpcHRvcikgZGVzY3JpcHRvci53cml0YWJsZSA9IHRydWU7XG4gICAgICAoMCwgX2RlZmluZVByb3BlcnR5Mi5kZWZhdWx0KSh0YXJnZXQsIGRlc2NyaXB0b3Iua2V5LCBkZXNjcmlwdG9yKTtcbiAgICB9XG4gIH1cblxuICByZXR1cm4gZnVuY3Rpb24gKENvbnN0cnVjdG9yLCBwcm90b1Byb3BzLCBzdGF0aWNQcm9wcykge1xuICAgIGlmIChwcm90b1Byb3BzKSBkZWZpbmVQcm9wZXJ0aWVzKENvbnN0cnVjdG9yLnByb3RvdHlwZSwgcHJvdG9Qcm9wcyk7XG4gICAgaWYgKHN0YXRpY1Byb3BzKSBkZWZpbmVQcm9wZXJ0aWVzKENvbnN0cnVjdG9yLCBzdGF0aWNQcm9wcyk7XG4gICAgcmV0dXJuIENvbnN0cnVjdG9yO1xuICB9O1xufSgpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY3JlYXRlQ2xhc3MuanNcbi8vIG1vZHVsZSBpZCA9IDFcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgZFAgICAgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1kcCcpXG4gICwgY3JlYXRlRGVzYyA9IHJlcXVpcmUoJy4vX3Byb3BlcnR5LWRlc2MnKTtcbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSA/IGZ1bmN0aW9uKG9iamVjdCwga2V5LCB2YWx1ZSl7XG4gIHJldHVybiBkUC5mKG9iamVjdCwga2V5LCBjcmVhdGVEZXNjKDEsIHZhbHVlKSk7XG59IDogZnVuY3Rpb24ob2JqZWN0LCBrZXksIHZhbHVlKXtcbiAgb2JqZWN0W2tleV0gPSB2YWx1ZTtcbiAgcmV0dXJuIG9iamVjdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oaWRlLmpzXG4vLyBtb2R1bGUgaWQgPSAxMFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgaXNPYmplY3QgPSByZXF1aXJlKCcuL19pcy1vYmplY3QnKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICBpZighaXNPYmplY3QoaXQpKXRocm93IFR5cGVFcnJvcihpdCArICcgaXMgbm90IGFuIG9iamVjdCEnKTtcbiAgcmV0dXJuIGl0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2FuLW9iamVjdC5qc1xuLy8gbW9kdWxlIGlkID0gMTFcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihiaXRtYXAsIHZhbHVlKXtcbiAgcmV0dXJuIHtcbiAgICBlbnVtZXJhYmxlICA6ICEoYml0bWFwICYgMSksXG4gICAgY29uZmlndXJhYmxlOiAhKGJpdG1hcCAmIDIpLFxuICAgIHdyaXRhYmxlICAgIDogIShiaXRtYXAgJiA0KSxcbiAgICB2YWx1ZSAgICAgICA6IHZhbHVlXG4gIH07XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fcHJvcGVydHktZGVzYy5qc1xuLy8gbW9kdWxlIGlkID0gMTJcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLy8gb3B0aW9uYWwgLyBzaW1wbGUgY29udGV4dCBiaW5kaW5nXG52YXIgYUZ1bmN0aW9uID0gcmVxdWlyZSgnLi9fYS1mdW5jdGlvbicpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihmbiwgdGhhdCwgbGVuZ3RoKXtcbiAgYUZ1bmN0aW9uKGZuKTtcbiAgaWYodGhhdCA9PT0gdW5kZWZpbmVkKXJldHVybiBmbjtcbiAgc3dpdGNoKGxlbmd0aCl7XG4gICAgY2FzZSAxOiByZXR1cm4gZnVuY3Rpb24oYSl7XG4gICAgICByZXR1cm4gZm4uY2FsbCh0aGF0LCBhKTtcbiAgICB9O1xuICAgIGNhc2UgMjogcmV0dXJuIGZ1bmN0aW9uKGEsIGIpe1xuICAgICAgcmV0dXJuIGZuLmNhbGwodGhhdCwgYSwgYik7XG4gICAgfTtcbiAgICBjYXNlIDM6IHJldHVybiBmdW5jdGlvbihhLCBiLCBjKXtcbiAgICAgIHJldHVybiBmbi5jYWxsKHRoYXQsIGEsIGIsIGMpO1xuICAgIH07XG4gIH1cbiAgcmV0dXJuIGZ1bmN0aW9uKC8qIC4uLmFyZ3MgKi8pe1xuICAgIHJldHVybiBmbi5hcHBseSh0aGF0LCBhcmd1bWVudHMpO1xuICB9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2N0eC5qc1xuLy8gbW9kdWxlIGlkID0gMTNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLy8gNy4xLjEgVG9QcmltaXRpdmUoaW5wdXQgWywgUHJlZmVycmVkVHlwZV0pXG52YXIgaXNPYmplY3QgPSByZXF1aXJlKCcuL19pcy1vYmplY3QnKTtcbi8vIGluc3RlYWQgb2YgdGhlIEVTNiBzcGVjIHZlcnNpb24sIHdlIGRpZG4ndCBpbXBsZW1lbnQgQEB0b1ByaW1pdGl2ZSBjYXNlXG4vLyBhbmQgdGhlIHNlY29uZCBhcmd1bWVudCAtIGZsYWcgLSBwcmVmZXJyZWQgdHlwZSBpcyBhIHN0cmluZ1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCwgUyl7XG4gIGlmKCFpc09iamVjdChpdCkpcmV0dXJuIGl0O1xuICB2YXIgZm4sIHZhbDtcbiAgaWYoUyAmJiB0eXBlb2YgKGZuID0gaXQudG9TdHJpbmcpID09ICdmdW5jdGlvbicgJiYgIWlzT2JqZWN0KHZhbCA9IGZuLmNhbGwoaXQpKSlyZXR1cm4gdmFsO1xuICBpZih0eXBlb2YgKGZuID0gaXQudmFsdWVPZikgPT0gJ2Z1bmN0aW9uJyAmJiAhaXNPYmplY3QodmFsID0gZm4uY2FsbChpdCkpKXJldHVybiB2YWw7XG4gIGlmKCFTICYmIHR5cGVvZiAoZm4gPSBpdC50b1N0cmluZykgPT0gJ2Z1bmN0aW9uJyAmJiAhaXNPYmplY3QodmFsID0gZm4uY2FsbChpdCkpKXJldHVybiB2YWw7XG4gIHRocm93IFR5cGVFcnJvcihcIkNhbid0IGNvbnZlcnQgb2JqZWN0IHRvIHByaW1pdGl2ZSB2YWx1ZVwiKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1wcmltaXRpdmUuanNcbi8vIG1vZHVsZSBpZCA9IDE0XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpXG4gICwgZG9jdW1lbnQgPSByZXF1aXJlKCcuL19nbG9iYWwnKS5kb2N1bWVudFxuICAvLyBpbiBvbGQgSUUgdHlwZW9mIGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQgaXMgJ29iamVjdCdcbiAgLCBpcyA9IGlzT2JqZWN0KGRvY3VtZW50KSAmJiBpc09iamVjdChkb2N1bWVudC5jcmVhdGVFbGVtZW50KTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gaXMgPyBkb2N1bWVudC5jcmVhdGVFbGVtZW50KGl0KSA6IHt9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2RvbS1jcmVhdGUuanNcbi8vIG1vZHVsZSBpZCA9IDE2XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gIXJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJykgJiYgIXJlcXVpcmUoJy4vX2ZhaWxzJykoZnVuY3Rpb24oKXtcbiAgcmV0dXJuIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShyZXF1aXJlKCcuL19kb20tY3JlYXRlJykoJ2RpdicpLCAnYScsIHtnZXQ6IGZ1bmN0aW9uKCl7IHJldHVybiA3OyB9fSkuYSAhPSA3O1xufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pZTgtZG9tLWRlZmluZS5qc1xuLy8gbW9kdWxlIGlkID0gMTdcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIGlmKHR5cGVvZiBpdCAhPSAnZnVuY3Rpb24nKXRocm93IFR5cGVFcnJvcihpdCArICcgaXMgbm90IGEgZnVuY3Rpb24hJyk7XG4gIHJldHVybiBpdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hLWZ1bmN0aW9uLmpzXG4vLyBtb2R1bGUgaWQgPSAxOFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eVwiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qc1xuLy8gbW9kdWxlIGlkID0gMTlcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLy8gVGhhbmsncyBJRTggZm9yIGhpcyBmdW5ueSBkZWZpbmVQcm9wZXJ0eVxubW9kdWxlLmV4cG9ydHMgPSAhcmVxdWlyZSgnLi9fZmFpbHMnKShmdW5jdGlvbigpe1xuICByZXR1cm4gT2JqZWN0LmRlZmluZVByb3BlcnR5KHt9LCAnYScsIHtnZXQ6IGZ1bmN0aW9uKCl7IHJldHVybiA3OyB9fSkuYSAhPSA3O1xufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kZXNjcmlwdG9ycy5qc1xuLy8gbW9kdWxlIGlkID0gMlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJyZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5Jyk7XG52YXIgJE9iamVjdCA9IHJlcXVpcmUoJy4uLy4uL21vZHVsZXMvX2NvcmUnKS5PYmplY3Q7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uIGRlZmluZVByb3BlcnR5KGl0LCBrZXksIGRlc2Mpe1xuICByZXR1cm4gJE9iamVjdC5kZWZpbmVQcm9wZXJ0eShpdCwga2V5LCBkZXNjKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvZGVmaW5lLXByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAyMFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgJGV4cG9ydCA9IHJlcXVpcmUoJy4vX2V4cG9ydCcpO1xuLy8gMTkuMS4yLjQgLyAxNS4yLjMuNiBPYmplY3QuZGVmaW5lUHJvcGVydHkoTywgUCwgQXR0cmlidXRlcylcbiRleHBvcnQoJGV4cG9ydC5TICsgJGV4cG9ydC5GICogIXJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJyksICdPYmplY3QnLCB7ZGVmaW5lUHJvcGVydHk6IHJlcXVpcmUoJy4vX29iamVjdC1kcCcpLmZ9KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5kZWZpbmUtcHJvcGVydHkuanNcbi8vIG1vZHVsZSBpZCA9IDIxXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBjb3JlID0gbW9kdWxlLmV4cG9ydHMgPSB7dmVyc2lvbjogJzIuNC4wJ307XG5pZih0eXBlb2YgX19lID09ICdudW1iZXInKV9fZSA9IGNvcmU7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tdW5kZWZcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvcmUuanNcbi8vIG1vZHVsZSBpZCA9IDNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIHJldHVybiB0eXBlb2YgaXQgPT09ICdvYmplY3QnID8gaXQgIT09IG51bGwgOiB0eXBlb2YgaXQgPT09ICdmdW5jdGlvbic7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXMtb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSA0XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IEZvcm1GaWVsZFRvZ2dsZSBmcm9tIFwiLi9Gb3JtRmllbGRUb2dnbGVcIjtcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBJbXBvcnRQYWdlIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgbmV3IEZvcm1GaWVsZFRvZ2dsZSgpO1xuXG4gICAgJCgnLmpzLWZyb20tZmlsZXMtaGlzdG9yeS1idG4nKS5vbignY2xpY2snLCAoKSA9PiB0aGlzLnNob3dGaWxlc0hpc3RvcnlIYW5kbGVyKCkpO1xuICAgICQoJy5qcy1jbG9zZS1maWxlcy1oaXN0b3J5LWJsb2NrLWJ0bicpLm9uKCdjbGljaycsICgpID0+IHRoaXMuY2xvc2VGaWxlc0hpc3RvcnlIYW5kbGVyKCkpO1xuICAgICQoJyNmaWxlSGlzdG9yeVRhYmxlJykub24oJ2NsaWNrJywgJy5qcy11c2UtZmlsZS1idG4nLCAoZXZlbnQpID0+IHRoaXMudXNlRmlsZUZyb21GaWxlc0hpc3RvcnkoZXZlbnQpKTtcbiAgICAkKCcuanMtY2hhbmdlLWltcG9ydC1maWxlLWJ0bicpLm9uKCdjbGljaycsICgpID0+IHRoaXMuY2hhbmdlSW1wb3J0RmlsZUhhbmRsZXIoKSk7XG4gICAgJCgnLmpzLWltcG9ydC1maWxlJykub24oJ2NoYW5nZScsICgpID0+IHRoaXMudXBsb2FkRmlsZSgpKTtcblxuICAgIHRoaXMudG9nZ2xlU2VsZWN0ZWRGaWxlKCk7XG4gICAgdGhpcy5oYW5kbGVTdWJtaXQoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgc3VibWl0IGFuZCBhZGQgY29uZmlybSBib3ggaW4gY2FzZSB0aGUgdG9nZ2xlIGJ1dHRvbiBhYm91dFxuICAgKiBkZWxldGluZyBhbGwgZW50aXRpZXMgYmVmb3JlIGltcG9ydCBpcyBjaGVja2VkXG4gICAqL1xuICBoYW5kbGVTdWJtaXQoKSB7XG4gICAgJCgnLmpzLWltcG9ydC1mb3JtJykub24oJ3N1Ym1pdCcsIGZ1bmN0aW9uKCkge1xuICAgICAgY29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgaWYgKCR0aGlzLmZpbmQoJ2lucHV0W25hbWU9XCJ0cnVuY2F0ZVwiXTpjaGVja2VkJykudmFsKCkgPT09ICcxJykge1xuICAgICAgICByZXR1cm4gY29uZmlybShgJHskdGhpcy5kYXRhKCdkZWxldGUtY29uZmlybS1tZXNzYWdlJyl9ICR7JC50cmltKCQoJyNlbnRpdHkgPiBvcHRpb246c2VsZWN0ZWQnKS50ZXh0KCkudG9Mb3dlckNhc2UoKSl9P2ApO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrIGlmIHNlbGVjdGVkIGZpbGUgbmFtZXMgZXhpc3RzIGFuZCBpZiBzbywgdGhlbiBkaXNwbGF5IGl0XG4gICAqL1xuICB0b2dnbGVTZWxlY3RlZEZpbGUoKSB7XG4gICAgbGV0IHNlbGVjdEZpbGVuYW1lID0gJCgnI2NzdicpLnZhbCgpO1xuICAgIGlmIChzZWxlY3RGaWxlbmFtZS5sZW5ndGggPiAwKSB7XG4gICAgICB0aGlzLnNob3dJbXBvcnRGaWxlQWxlcnQoc2VsZWN0RmlsZW5hbWUpO1xuICAgICAgdGhpcy5oaWRlRmlsZVVwbG9hZEJsb2NrKCk7XG4gICAgfVxuICB9XG5cbiAgY2hhbmdlSW1wb3J0RmlsZUhhbmRsZXIoKSB7XG4gICAgdGhpcy5oaWRlSW1wb3J0RmlsZUFsZXJ0KCk7XG4gICAgdGhpcy5zaG93RmlsZVVwbG9hZEJsb2NrKCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvdyBmaWxlcyBoaXN0b3J5IGV2ZW50IGhhbmRsZXJcbiAgICovXG4gIHNob3dGaWxlc0hpc3RvcnlIYW5kbGVyKCkge1xuICAgIHRoaXMuc2hvd0ZpbGVzSGlzdG9yeSgpO1xuICAgIHRoaXMuaGlkZUZpbGVVcGxvYWRCbG9jaygpO1xuICB9XG5cbiAgLyoqXG4gICAqIENsb3NlIGZpbGVzIGhpc3RvcnkgZXZlbnQgaGFuZGxlclxuICAgKi9cbiAgY2xvc2VGaWxlc0hpc3RvcnlIYW5kbGVyKCkge1xuICAgIHRoaXMuY2xvc2VGaWxlc0hpc3RvcnkoKTtcbiAgICB0aGlzLnNob3dGaWxlVXBsb2FkQmxvY2soKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IGZpbGVzIGhpc3RvcnkgYmxvY2tcbiAgICovXG4gIHNob3dGaWxlc0hpc3RvcnkoKSB7XG4gICAgJCgnLmpzLWZpbGVzLWhpc3RvcnktYmxvY2snKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZSBmaWxlcyBoaXN0b3J5IGJsb2NrXG4gICAqL1xuICBjbG9zZUZpbGVzSGlzdG9yeSgpIHtcbiAgICAkKCcuanMtZmlsZXMtaGlzdG9yeS1ibG9jaycpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiAgUHJlZmlsbCBoaWRkZW4gZmlsZSBpbnB1dCB3aXRoIHNlbGVjdGVkIGZpbGUgbmFtZSBmcm9tIGhpc3RvcnlcbiAgICovXG4gIHVzZUZpbGVGcm9tRmlsZXNIaXN0b3J5KGV2ZW50KSB7XG4gICAgbGV0IGZpbGVuYW1lID0gJChldmVudC50YXJnZXQpLmNsb3Nlc3QoJy5idG4tZ3JvdXAnKS5kYXRhKCdmaWxlJyk7XG5cbiAgICAkKCcuanMtaW1wb3J0LWZpbGUtaW5wdXQnKS52YWwoZmlsZW5hbWUpO1xuXG4gICAgdGhpcy5zaG93SW1wb3J0RmlsZUFsZXJ0KGZpbGVuYW1lKTtcbiAgICB0aGlzLmNsb3NlRmlsZXNIaXN0b3J5KCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvdyBhbGVydCB3aXRoIGltcG9ydGVkIGZpbGUgbmFtZVxuICAgKi9cbiAgc2hvd0ltcG9ydEZpbGVBbGVydChmaWxlbmFtZSkge1xuICAgICQoJy5qcy1pbXBvcnQtZmlsZS1hbGVydCcpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAkKCcuanMtaW1wb3J0LWZpbGUnKS50ZXh0KGZpbGVuYW1lKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyBzZWxlY3RlZCBpbXBvcnQgZmlsZSBhbGVydFxuICAgKi9cbiAgaGlkZUltcG9ydEZpbGVBbGVydCgpIHtcbiAgICAkKCcuanMtaW1wb3J0LWZpbGUtYWxlcnQnKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgaW1wb3J0IGZpbGUgdXBsb2FkIGJsb2NrXG4gICAqL1xuICBoaWRlRmlsZVVwbG9hZEJsb2NrKCkge1xuICAgICQoJy5qcy1maWxlLXVwbG9hZC1mb3JtLWdyb3VwJykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIGltcG9ydCBmaWxlIHVwbG9hZCBibG9ja1xuICAgKi9cbiAgc2hvd0ZpbGVVcGxvYWRCbG9jaygpIHtcbiAgICAkKCcuanMtZmlsZS11cGxvYWQtZm9ybS1ncm91cCcpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBNYWtlIGZpbGUgaGlzdG9yeSBidXR0b24gY2xpY2thYmxlXG4gICAqL1xuICBlbmFibGVGaWxlc0hpc3RvcnlCdG4oKSB7XG4gICAgJCgnLmpzLWZyb20tZmlsZXMtaGlzdG9yeS1idG4nKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3cgZXJyb3IgbWVzc2FnZSBpZiBmaWxlIHVwbG9hZGluZyBmYWlsZWRcbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IGZpbGVOYW1lXG4gICAqIEBwYXJhbSB7aW50ZWdlcn0gZmlsZVNpemVcbiAgICogQHBhcmFtIHtzdHJpbmd9IG1lc3NhZ2VcbiAgICovXG4gIHNob3dJbXBvcnRGaWxlRXJyb3IoZmlsZU5hbWUsIGZpbGVTaXplLCBtZXNzYWdlKSB7XG4gICAgY29uc3QgJGFsZXJ0ID0gJCgnLmpzLWltcG9ydC1maWxlLWVycm9yJyk7XG5cbiAgICBjb25zdCBmaWxlRGF0YSA9IGZpbGVOYW1lICsgJyAoJyArIHRoaXMuaHVtYW5pemVTaXplKGZpbGVTaXplKSArICcpJztcblxuICAgICRhbGVydC5maW5kKCcuanMtZmlsZS1kYXRhJykudGV4dChmaWxlRGF0YSk7XG4gICAgJGFsZXJ0LmZpbmQoJy5qcy1lcnJvci1tZXNzYWdlJykudGV4dChtZXNzYWdlKTtcbiAgICAkYWxlcnQucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGUgZmlsZSB1cGxvYWRpbmcgZXJyb3JcbiAgICovXG4gIGhpZGVJbXBvcnRGaWxlRXJyb3IoKSB7XG4gICAgY29uc3QgJGFsZXJ0ID0gJCgnLmpzLWltcG9ydC1maWxlLWVycm9yJyk7XG4gICAgJGFsZXJ0LmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93IGZpbGUgc2l6ZSBpbiBodW1hbiByZWFkYWJsZSBmb3JtYXRcbiAgICpcbiAgICogQHBhcmFtIHtpbnR9IGJ5dGVzXG4gICAqXG4gICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAqL1xuICBodW1hbml6ZVNpemUoYnl0ZXMpIHtcbiAgICBpZiAodHlwZW9mIGJ5dGVzICE9PSAnbnVtYmVyJykge1xuICAgICAgcmV0dXJuICcnO1xuICAgIH1cblxuICAgIGlmIChieXRlcyA+PSAxMDAwMDAwMDAwKSB7XG4gICAgICByZXR1cm4gKGJ5dGVzIC8gMTAwMDAwMDAwMCkudG9GaXhlZCgyKSArICcgR0InO1xuICAgIH1cblxuICAgIGlmIChieXRlcyA+PSAxMDAwMDAwKSB7XG4gICAgICByZXR1cm4gKGJ5dGVzIC8gMTAwMDAwMCkudG9GaXhlZCgyKSArICcgTUInO1xuICAgIH1cblxuICAgIHJldHVybiAoYnl0ZXMgLyAxMDAwKS50b0ZpeGVkKDIpICsgJyBLQic7XG4gIH1cblxuICAvKipcbiAgICogVXBsb2FkIHNlbGVjdGVkIGltcG9ydCBmaWxlXG4gICAqL1xuICB1cGxvYWRGaWxlKCkge1xuICAgIHRoaXMuaGlkZUltcG9ydEZpbGVFcnJvcigpO1xuXG4gICAgY29uc3QgJGlucHV0ID0gJCgnI2ZpbGUnKTtcbiAgICBjb25zdCB1cGxvYWRlZEZpbGUgPSAkaW5wdXQucHJvcCgnZmlsZXMnKVswXTtcblxuICAgIGNvbnN0IG1heFVwbG9hZFNpemUgPSAkaW5wdXQuZGF0YSgnbWF4LWZpbGUtdXBsb2FkLXNpemUnKTtcbiAgICBpZiAobWF4VXBsb2FkU2l6ZSA8IHVwbG9hZGVkRmlsZS5zaXplKSB7XG4gICAgICB0aGlzLnNob3dJbXBvcnRGaWxlRXJyb3IodXBsb2FkZWRGaWxlLm5hbWUsIHVwbG9hZGVkRmlsZS5zaXplLCAnRmlsZSBpcyB0b28gbGFyZ2UnKTtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCBkYXRhID0gbmV3IEZvcm1EYXRhKCk7XG4gICAgZGF0YS5hcHBlbmQoJ2ZpbGUnLCB1cGxvYWRlZEZpbGUpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgIHVybDogJCgnLmpzLWltcG9ydC1mb3JtJykuZGF0YSgnZmlsZS11cGxvYWQtdXJsJyksXG4gICAgICBkYXRhOiBkYXRhLFxuICAgICAgY2FjaGU6IGZhbHNlLFxuICAgICAgY29udGVudFR5cGU6IGZhbHNlLFxuICAgICAgcHJvY2Vzc0RhdGE6IGZhbHNlLFxuICAgIH0pLnRoZW4ocmVzcG9uc2UgPT4ge1xuICAgICAgaWYgKHJlc3BvbnNlLmVycm9yKSB7XG4gICAgICAgIHRoaXMuc2hvd0ltcG9ydEZpbGVFcnJvcih1cGxvYWRlZEZpbGUubmFtZSwgdXBsb2FkZWRGaWxlLnNpemUsIHJlc3BvbnNlLmVycm9yKTtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBsZXQgZmlsZW5hbWUgPSByZXNwb25zZS5maWxlLm5hbWU7XG5cbiAgICAgICQoJy5qcy1pbXBvcnQtZmlsZS1pbnB1dCcpLnZhbChmaWxlbmFtZSk7XG5cbiAgICAgIHRoaXMuc2hvd0ltcG9ydEZpbGVBbGVydChmaWxlbmFtZSk7XG4gICAgICB0aGlzLmhpZGVGaWxlVXBsb2FkQmxvY2soKTtcbiAgICAgIHRoaXMuYWRkRmlsZVRvSGlzdG9yeVRhYmxlKGZpbGVuYW1lKTtcbiAgICAgIHRoaXMuZW5hYmxlRmlsZXNIaXN0b3J5QnRuKCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBuZXcgcm93IGluIGZpbGVzIGhpc3RvcnkgdGFibGVcbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IGZpbGVuYW1lXG4gICAqL1xuICBhZGRGaWxlVG9IaXN0b3J5VGFibGUoZmlsZW5hbWUpIHtcbiAgICBjb25zdCAkdGFibGUgPSAkKCcjZmlsZUhpc3RvcnlUYWJsZScpO1xuXG4gICAgbGV0IGJhc2VEZWxldGVVcmwgPSAkdGFibGUuZGF0YSgnZGVsZXRlLWZpbGUtdXJsJyk7XG4gICAgbGV0IGRlbGV0ZVVybCA9IGJhc2VEZWxldGVVcmwgKyAnJmZpbGVuYW1lPScgKyBlbmNvZGVVUklDb21wb25lbnQoZmlsZW5hbWUpO1xuXG4gICAgbGV0IGJhc2VEb3dubG9hZFVybCA9ICR0YWJsZS5kYXRhKCdkb3dubG9hZC1maWxlLXVybCcpO1xuICAgIGxldCBkb3dubG9hZFVybCA9IGJhc2VEb3dubG9hZFVybCArICcmZmlsZW5hbWU9JyArIGVuY29kZVVSSUNvbXBvbmVudChmaWxlbmFtZSk7XG5cbiAgICBsZXQgJHRlbXBsYXRlID0gJHRhYmxlLmZpbmQoJ3RyOmZpcnN0JykuY2xvbmUoKTtcblxuICAgICR0ZW1wbGF0ZS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgJHRlbXBsYXRlLmZpbmQoJ3RkOmZpcnN0JykudGV4dChmaWxlbmFtZSk7XG4gICAgJHRlbXBsYXRlLmZpbmQoJy5idG4tZ3JvdXAnKS5hdHRyKCdkYXRhLWZpbGUnLCBmaWxlbmFtZSk7XG4gICAgJHRlbXBsYXRlLmZpbmQoJy5qcy1kZWxldGUtZmlsZS1idG4nKS5hdHRyKCdocmVmJywgZGVsZXRlVXJsKTtcbiAgICAkdGVtcGxhdGUuZmluZCgnLmpzLWRvd25sb2FkLWZpbGUtYnRuJykuYXR0cignaHJlZicsIGRvd25sb2FkVXJsKTtcblxuICAgICR0YWJsZS5maW5kKCd0Ym9keScpLmFwcGVuZCgkdGVtcGxhdGUpO1xuXG4gICAgbGV0IGZpbGVzTnVtYmVyID0gJHRhYmxlLmZpbmQoJ3RyJykubGVuZ3RoIC0gMTtcbiAgICAkKCcuanMtZmlsZXMtaGlzdG9yeS1udW1iZXInKS50ZXh0KGZpbGVzTnVtYmVyKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvaW1wb3J0L0ltcG9ydFBhZ2UuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuY29uc3QgZW50aXR5Q2F0ZWdvcmllcyA9IDA7XG5jb25zdCBlbnRpdHlQcm9kdWN0cyA9IDE7XG5jb25zdCBlbnRpdHlDb21iaW5hdGlvbnMgPSAyO1xuY29uc3QgZW50aXR5Q3VzdG9tZXJzID0gMztcbmNvbnN0IGVudGl0eUFkZHJlc3NlcyA9IDQ7XG5jb25zdCBlbnRpdHlCcmFuZHMgPSA1O1xuY29uc3QgZW50aXR5U3VwcGxpZXJzID0gNjtcbmNvbnN0IGVudGl0eUFsaWFzID0gNztcbmNvbnN0IGVudGl0eVN0b3JlQ29udGFjdHMgPSA4O1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBGb3JtRmllbGRUb2dnbGUge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICAkKCcuanMtZW50aXR5LXNlbGVjdCcpLm9uKCdjaGFuZ2UnLCAoKSA9PiB0aGlzLnRvZ2dsZUZvcm0oKSk7XG5cbiAgICB0aGlzLnRvZ2dsZUZvcm0oKTtcbiAgfVxuXG4gIHRvZ2dsZUZvcm0oKSB7XG4gICAgbGV0IHNlbGVjdGVkT3B0aW9uID0gJCgnI2VudGl0eScpLmZpbmQoJ29wdGlvbjpzZWxlY3RlZCcpO1xuICAgIGxldCBzZWxlY3RlZEVudGl0eSA9IHBhcnNlSW50KHNlbGVjdGVkT3B0aW9uLnZhbCgpKTtcbiAgICBsZXQgZW50aXR5TmFtZSA9IHNlbGVjdGVkT3B0aW9uLnRleHQoKS50b0xvd2VyQ2FzZSgpO1xuXG4gICAgdGhpcy50b2dnbGVFbnRpdHlBbGVydChzZWxlY3RlZEVudGl0eSk7XG4gICAgdGhpcy50b2dnbGVGaWVsZHMoc2VsZWN0ZWRFbnRpdHksIGVudGl0eU5hbWUpO1xuICAgIHRoaXMubG9hZEF2YWlsYWJsZUZpZWxkcyhzZWxlY3RlZEVudGl0eSk7XG4gIH1cblxuICAvKipcbiAgICogVG9nZ2xlIGFsZXJ0IHdhcm5pbmcgZm9yIHNlbGVjdGVkIGltcG9ydCBlbnRpdHlcbiAgICpcbiAgICogQHBhcmFtIHtpbnR9IHNlbGVjdGVkRW50aXR5XG4gICAqL1xuICB0b2dnbGVFbnRpdHlBbGVydChzZWxlY3RlZEVudGl0eSkge1xuICAgIGxldCAkYWxlcnQgPSAkKCcuanMtZW50aXR5LWFsZXJ0Jyk7XG5cbiAgICBpZiAoW2VudGl0eUNhdGVnb3JpZXMsIGVudGl0eVByb2R1Y3RzXS5pbmNsdWRlcyhzZWxlY3RlZEVudGl0eSkpIHtcbiAgICAgICRhbGVydC5zaG93KCk7XG4gICAgfSBlbHNlIHtcbiAgICAgICRhbGVydC5oaWRlKCk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIFRvZ2dsZSBhdmFpbGFibGUgb3B0aW9ucyBmb3Igc2VsZWN0ZWQgZW50aXR5XG4gICAqXG4gICAqIEBwYXJhbSB7aW50fSBzZWxlY3RlZEVudGl0eVxuICAgKiBAcGFyYW0ge3N0cmluZ30gZW50aXR5TmFtZVxuICAgKi9cbiAgdG9nZ2xlRmllbGRzKHNlbGVjdGVkRW50aXR5LCBlbnRpdHlOYW1lKSB7XG4gICAgY29uc3QgJHRydW5jYXRlRm9ybUdyb3VwID0gJCgnLmpzLXRydW5jYXRlLWZvcm0tZ3JvdXAnKTtcbiAgICBjb25zdCAkbWF0Y2hSZWZGb3JtR3JvdXAgPSAkKCcuanMtbWF0Y2gtcmVmLWZvcm0tZ3JvdXAnKTtcbiAgICBjb25zdCAkcmVnZW5lcmF0ZUZvcm1Hcm91cCA9ICQoJy5qcy1yZWdlbmVyYXRlLWZvcm0tZ3JvdXAnKTtcbiAgICBjb25zdCAkZm9yY2VJZHNGb3JtR3JvdXAgPSAkKCcuanMtZm9yY2UtaWRzLWZvcm0tZ3JvdXAnKTtcbiAgICBjb25zdCAkZW50aXR5TmFtZVBsYWNlaG9sZGVyID0gJCgnLmpzLWVudGl0eS1uYW1lJyk7XG5cbiAgICBpZiAoZW50aXR5U3RvcmVDb250YWN0cyA9PT0gc2VsZWN0ZWRFbnRpdHkpIHtcbiAgICAgICR0cnVuY2F0ZUZvcm1Hcm91cC5oaWRlKCk7XG4gICAgfSBlbHNlIHtcbiAgICAgICR0cnVuY2F0ZUZvcm1Hcm91cC5zaG93KCk7XG4gICAgfVxuXG4gICAgaWYgKFtlbnRpdHlQcm9kdWN0cywgZW50aXR5Q29tYmluYXRpb25zXS5pbmNsdWRlcyhzZWxlY3RlZEVudGl0eSkpIHtcbiAgICAgICRtYXRjaFJlZkZvcm1Hcm91cC5zaG93KCk7XG4gICAgfSBlbHNlIHtcbiAgICAgICRtYXRjaFJlZkZvcm1Hcm91cC5oaWRlKCk7XG4gICAgfVxuXG4gICAgaWYgKFtcbiAgICAgIGVudGl0eUNhdGVnb3JpZXMsXG4gICAgICBlbnRpdHlQcm9kdWN0cyxcbiAgICAgIGVudGl0eUJyYW5kcyxcbiAgICAgIGVudGl0eVN1cHBsaWVycyxcbiAgICAgIGVudGl0eVN0b3JlQ29udGFjdHNcbiAgICBdLmluY2x1ZGVzKHNlbGVjdGVkRW50aXR5KVxuICAgICkge1xuICAgICAgJHJlZ2VuZXJhdGVGb3JtR3JvdXAuc2hvdygpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkcmVnZW5lcmF0ZUZvcm1Hcm91cC5oaWRlKCk7XG4gICAgfVxuXG4gICAgaWYgKFtcbiAgICAgIGVudGl0eUNhdGVnb3JpZXMsXG4gICAgICBlbnRpdHlQcm9kdWN0cyxcbiAgICAgIGVudGl0eUN1c3RvbWVycyxcbiAgICAgIGVudGl0eUFkZHJlc3NlcyxcbiAgICAgIGVudGl0eUJyYW5kcyxcbiAgICAgIGVudGl0eVN1cHBsaWVycyxcbiAgICAgIGVudGl0eVN0b3JlQ29udGFjdHMsXG4gICAgICBlbnRpdHlBbGlhc1xuICAgIF0uaW5jbHVkZXMoc2VsZWN0ZWRFbnRpdHkpXG4gICAgKSB7XG4gICAgICAkZm9yY2VJZHNGb3JtR3JvdXAuc2hvdygpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkZm9yY2VJZHNGb3JtR3JvdXAuaGlkZSgpO1xuICAgIH1cblxuICAgICRlbnRpdHlOYW1lUGxhY2Vob2xkZXIuaHRtbChlbnRpdHlOYW1lKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMb2FkIGF2YWlsYWJsZSBmaWVsZHMgZm9yIGdpdmVuIGVudGl0eVxuICAgKlxuICAgKiBAcGFyYW0ge2ludH0gZW50aXR5XG4gICAqL1xuICBsb2FkQXZhaWxhYmxlRmllbGRzKGVudGl0eSkge1xuICAgIGNvbnN0ICRhdmFpbGFibGVGaWVsZHMgPSAkKCcuanMtYXZhaWxhYmxlLWZpZWxkcycpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHVybDogJGF2YWlsYWJsZUZpZWxkcy5kYXRhKCd1cmwnKSxcbiAgICAgIGRhdGE6IHtcbiAgICAgICAgZW50aXR5OiBlbnRpdHlcbiAgICAgIH0sXG4gICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgIH0pLnRoZW4ocmVzcG9uc2UgPT4ge1xuICAgICAgdGhpcy5fcmVtb3ZlQXZhaWxhYmxlRmllbGRzKCRhdmFpbGFibGVGaWVsZHMpO1xuXG4gICAgICBmb3IgKGxldCBpID0gMDsgaSA8IHJlc3BvbnNlLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgIHRoaXMuX2FwcGVuZEF2YWlsYWJsZUZpZWxkKFxuICAgICAgICAgICRhdmFpbGFibGVGaWVsZHMsXG4gICAgICAgICAgcmVzcG9uc2VbaV0ubGFiZWwgKyAocmVzcG9uc2VbaV0ucmVxdWlyZWQgPyAnKicgOiAnJyksXG4gICAgICAgICAgcmVzcG9uc2VbaV0uZGVzY3JpcHRpb25cbiAgICAgICAgKTtcbiAgICAgIH1cblxuICAgICAgJGF2YWlsYWJsZUZpZWxkcy5maW5kKCdbZGF0YS10b2dnbGU9XCJwb3BvdmVyXCJdJykucG9wb3ZlcigpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbW92ZSBhdmFpbGFibGUgZmllbGRzIGNvbnRlbnQgZnJvbSBnaXZlbiBjb250YWluZXIuXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkY29udGFpbmVyXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVtb3ZlQXZhaWxhYmxlRmllbGRzKCRjb250YWluZXIpIHtcbiAgICAkY29udGFpbmVyLmZpbmQoJ1tkYXRhLXRvZ2dsZT1cInBvcG92ZXJcIl0nKS5wb3BvdmVyKCdoaWRlJyk7XG4gICAgJGNvbnRhaW5lci5lbXB0eSgpO1xuICB9XG5cbiAgLyoqXG4gICAqIEFwcGVuZCBhIGhlbHAgYm94IHRvIGdpdmVuIGZpZWxkLlxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGZpZWxkXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBoZWxwQm94Q29udGVudFxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2FwcGVuZEhlbHBCb3goJGZpZWxkLCBoZWxwQm94Q29udGVudCkge1xuICAgIGxldCAkaGVscEJveCA9ICQoJy5qcy1hdmFpbGFibGUtZmllbGQtcG9wb3Zlci10ZW1wbGF0ZScpLmNsb25lKCk7XG5cbiAgICAkaGVscEJveC5hdHRyKCdkYXRhLWNvbnRlbnQnLCBoZWxwQm94Q29udGVudCk7XG4gICAgJGhlbHBCb3gucmVtb3ZlQ2xhc3MoJ2pzLWF2YWlsYWJsZS1maWVsZC1wb3BvdmVyLXRlbXBsYXRlIGQtbm9uZScpO1xuICAgICRmaWVsZC5hcHBlbmQoJGhlbHBCb3gpO1xuICB9XG5cbiAgLyoqXG4gICAqIEFwcGVuZCBhdmFpbGFibGUgZmllbGQgdG8gZ2l2ZW4gY29udGFpbmVyLlxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGFwcGVuZFRvIGZpZWxkIHdpbGwgYmUgYXBwZW5kZWQgdG8gdGhpcyBjb250YWluZXIuXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBmaWVsZFRleHRcbiAgICogQHBhcmFtIHtTdHJpbmd9IGhlbHBCb3hDb250ZW50XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfYXBwZW5kQXZhaWxhYmxlRmllbGQoJGFwcGVuZFRvLCBmaWVsZFRleHQsIGhlbHBCb3hDb250ZW50KSB7XG4gICAgbGV0ICRmaWVsZCA9ICQoJy5qcy1hdmFpbGFibGUtZmllbGQtdGVtcGxhdGUnKS5jbG9uZSgpO1xuXG4gICAgJGZpZWxkLnRleHQoZmllbGRUZXh0KTtcblxuICAgIGlmIChoZWxwQm94Q29udGVudCkge1xuICAgICAgLy8gQXBwZW5kIGhlbHAgYm94IG5leHQgdG8gdGhlIGZpZWxkXG4gICAgICB0aGlzLl9hcHBlbmRIZWxwQm94KCRmaWVsZCwgaGVscEJveENvbnRlbnQpO1xuICAgIH1cblxuICAgICRmaWVsZC5yZW1vdmVDbGFzcygnanMtYXZhaWxhYmxlLWZpZWxkLXRlbXBsYXRlIGQtbm9uZScpO1xuICAgICRmaWVsZC5hcHBlbmRUbygkYXBwZW5kVG8pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9pbXBvcnQvRm9ybUZpZWxkVG9nZ2xlLmpzIiwiLy8gaHR0cHM6Ly9naXRodWIuY29tL3psb2lyb2NrL2NvcmUtanMvaXNzdWVzLzg2I2lzc3VlY29tbWVudC0xMTU3NTkwMjhcbnZhciBnbG9iYWwgPSBtb2R1bGUuZXhwb3J0cyA9IHR5cGVvZiB3aW5kb3cgIT0gJ3VuZGVmaW5lZCcgJiYgd2luZG93Lk1hdGggPT0gTWF0aFxuICA/IHdpbmRvdyA6IHR5cGVvZiBzZWxmICE9ICd1bmRlZmluZWQnICYmIHNlbGYuTWF0aCA9PSBNYXRoID8gc2VsZiA6IEZ1bmN0aW9uKCdyZXR1cm4gdGhpcycpKCk7XG5pZih0eXBlb2YgX19nID09ICdudW1iZXInKV9fZyA9IGdsb2JhbDsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBuby11bmRlZlxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSA1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IEltcG9ydFBhZ2UgZnJvbSAnLi9JbXBvcnRQYWdlJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgbmV3IEltcG9ydFBhZ2UoKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvaW1wb3J0L2luZGV4LmpzIiwidmFyIGFuT2JqZWN0ICAgICAgID0gcmVxdWlyZSgnLi9fYW4tb2JqZWN0JylcbiAgLCBJRThfRE9NX0RFRklORSA9IHJlcXVpcmUoJy4vX2llOC1kb20tZGVmaW5lJylcbiAgLCB0b1ByaW1pdGl2ZSAgICA9IHJlcXVpcmUoJy4vX3RvLXByaW1pdGl2ZScpXG4gICwgZFAgICAgICAgICAgICAgPSBPYmplY3QuZGVmaW5lUHJvcGVydHk7XG5cbmV4cG9ydHMuZiA9IHJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJykgPyBPYmplY3QuZGVmaW5lUHJvcGVydHkgOiBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0eShPLCBQLCBBdHRyaWJ1dGVzKXtcbiAgYW5PYmplY3QoTyk7XG4gIFAgPSB0b1ByaW1pdGl2ZShQLCB0cnVlKTtcbiAgYW5PYmplY3QoQXR0cmlidXRlcyk7XG4gIGlmKElFOF9ET01fREVGSU5FKXRyeSB7XG4gICAgcmV0dXJuIGRQKE8sIFAsIEF0dHJpYnV0ZXMpO1xuICB9IGNhdGNoKGUpeyAvKiBlbXB0eSAqLyB9XG4gIGlmKCdnZXQnIGluIEF0dHJpYnV0ZXMgfHwgJ3NldCcgaW4gQXR0cmlidXRlcyl0aHJvdyBUeXBlRXJyb3IoJ0FjY2Vzc29ycyBub3Qgc3VwcG9ydGVkIScpO1xuICBpZigndmFsdWUnIGluIEF0dHJpYnV0ZXMpT1tQXSA9IEF0dHJpYnV0ZXMudmFsdWU7XG4gIHJldHVybiBPO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1kcC5qc1xuLy8gbW9kdWxlIGlkID0gNlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGV4ZWMpe1xuICB0cnkge1xuICAgIHJldHVybiAhIWV4ZWMoKTtcbiAgfSBjYXRjaChlKXtcbiAgICByZXR1cm4gdHJ1ZTtcbiAgfVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2ZhaWxzLmpzXG4vLyBtb2R1bGUgaWQgPSA3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBnbG9iYWwgICAgPSByZXF1aXJlKCcuL19nbG9iYWwnKVxuICAsIGNvcmUgICAgICA9IHJlcXVpcmUoJy4vX2NvcmUnKVxuICAsIGN0eCAgICAgICA9IHJlcXVpcmUoJy4vX2N0eCcpXG4gICwgaGlkZSAgICAgID0gcmVxdWlyZSgnLi9faGlkZScpXG4gICwgUFJPVE9UWVBFID0gJ3Byb3RvdHlwZSc7XG5cbnZhciAkZXhwb3J0ID0gZnVuY3Rpb24odHlwZSwgbmFtZSwgc291cmNlKXtcbiAgdmFyIElTX0ZPUkNFRCA9IHR5cGUgJiAkZXhwb3J0LkZcbiAgICAsIElTX0dMT0JBTCA9IHR5cGUgJiAkZXhwb3J0LkdcbiAgICAsIElTX1NUQVRJQyA9IHR5cGUgJiAkZXhwb3J0LlNcbiAgICAsIElTX1BST1RPICA9IHR5cGUgJiAkZXhwb3J0LlBcbiAgICAsIElTX0JJTkQgICA9IHR5cGUgJiAkZXhwb3J0LkJcbiAgICAsIElTX1dSQVAgICA9IHR5cGUgJiAkZXhwb3J0LldcbiAgICAsIGV4cG9ydHMgICA9IElTX0dMT0JBTCA/IGNvcmUgOiBjb3JlW25hbWVdIHx8IChjb3JlW25hbWVdID0ge30pXG4gICAgLCBleHBQcm90byAgPSBleHBvcnRzW1BST1RPVFlQRV1cbiAgICAsIHRhcmdldCAgICA9IElTX0dMT0JBTCA/IGdsb2JhbCA6IElTX1NUQVRJQyA/IGdsb2JhbFtuYW1lXSA6IChnbG9iYWxbbmFtZV0gfHwge30pW1BST1RPVFlQRV1cbiAgICAsIGtleSwgb3duLCBvdXQ7XG4gIGlmKElTX0dMT0JBTClzb3VyY2UgPSBuYW1lO1xuICBmb3Ioa2V5IGluIHNvdXJjZSl7XG4gICAgLy8gY29udGFpbnMgaW4gbmF0aXZlXG4gICAgb3duID0gIUlTX0ZPUkNFRCAmJiB0YXJnZXQgJiYgdGFyZ2V0W2tleV0gIT09IHVuZGVmaW5lZDtcbiAgICBpZihvd24gJiYga2V5IGluIGV4cG9ydHMpY29udGludWU7XG4gICAgLy8gZXhwb3J0IG5hdGl2ZSBvciBwYXNzZWRcbiAgICBvdXQgPSBvd24gPyB0YXJnZXRba2V5XSA6IHNvdXJjZVtrZXldO1xuICAgIC8vIHByZXZlbnQgZ2xvYmFsIHBvbGx1dGlvbiBmb3IgbmFtZXNwYWNlc1xuICAgIGV4cG9ydHNba2V5XSA9IElTX0dMT0JBTCAmJiB0eXBlb2YgdGFyZ2V0W2tleV0gIT0gJ2Z1bmN0aW9uJyA/IHNvdXJjZVtrZXldXG4gICAgLy8gYmluZCB0aW1lcnMgdG8gZ2xvYmFsIGZvciBjYWxsIGZyb20gZXhwb3J0IGNvbnRleHRcbiAgICA6IElTX0JJTkQgJiYgb3duID8gY3R4KG91dCwgZ2xvYmFsKVxuICAgIC8vIHdyYXAgZ2xvYmFsIGNvbnN0cnVjdG9ycyBmb3IgcHJldmVudCBjaGFuZ2UgdGhlbSBpbiBsaWJyYXJ5XG4gICAgOiBJU19XUkFQICYmIHRhcmdldFtrZXldID09IG91dCA/IChmdW5jdGlvbihDKXtcbiAgICAgIHZhciBGID0gZnVuY3Rpb24oYSwgYiwgYyl7XG4gICAgICAgIGlmKHRoaXMgaW5zdGFuY2VvZiBDKXtcbiAgICAgICAgICBzd2l0Y2goYXJndW1lbnRzLmxlbmd0aCl7XG4gICAgICAgICAgICBjYXNlIDA6IHJldHVybiBuZXcgQztcbiAgICAgICAgICAgIGNhc2UgMTogcmV0dXJuIG5ldyBDKGEpO1xuICAgICAgICAgICAgY2FzZSAyOiByZXR1cm4gbmV3IEMoYSwgYik7XG4gICAgICAgICAgfSByZXR1cm4gbmV3IEMoYSwgYiwgYyk7XG4gICAgICAgIH0gcmV0dXJuIEMuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICAgIH07XG4gICAgICBGW1BST1RPVFlQRV0gPSBDW1BST1RPVFlQRV07XG4gICAgICByZXR1cm4gRjtcbiAgICAvLyBtYWtlIHN0YXRpYyB2ZXJzaW9ucyBmb3IgcHJvdG90eXBlIG1ldGhvZHNcbiAgICB9KShvdXQpIDogSVNfUFJPVE8gJiYgdHlwZW9mIG91dCA9PSAnZnVuY3Rpb24nID8gY3R4KEZ1bmN0aW9uLmNhbGwsIG91dCkgOiBvdXQ7XG4gICAgLy8gZXhwb3J0IHByb3RvIG1ldGhvZHMgdG8gY29yZS4lQ09OU1RSVUNUT1IlLm1ldGhvZHMuJU5BTUUlXG4gICAgaWYoSVNfUFJPVE8pe1xuICAgICAgKGV4cG9ydHMudmlydHVhbCB8fCAoZXhwb3J0cy52aXJ0dWFsID0ge30pKVtrZXldID0gb3V0O1xuICAgICAgLy8gZXhwb3J0IHByb3RvIG1ldGhvZHMgdG8gY29yZS4lQ09OU1RSVUNUT1IlLnByb3RvdHlwZS4lTkFNRSVcbiAgICAgIGlmKHR5cGUgJiAkZXhwb3J0LlIgJiYgZXhwUHJvdG8gJiYgIWV4cFByb3RvW2tleV0paGlkZShleHBQcm90bywga2V5LCBvdXQpO1xuICAgIH1cbiAgfVxufTtcbi8vIHR5cGUgYml0bWFwXG4kZXhwb3J0LkYgPSAxOyAgIC8vIGZvcmNlZFxuJGV4cG9ydC5HID0gMjsgICAvLyBnbG9iYWxcbiRleHBvcnQuUyA9IDQ7ICAgLy8gc3RhdGljXG4kZXhwb3J0LlAgPSA4OyAgIC8vIHByb3RvXG4kZXhwb3J0LkIgPSAxNjsgIC8vIGJpbmRcbiRleHBvcnQuVyA9IDMyOyAgLy8gd3JhcFxuJGV4cG9ydC5VID0gNjQ7ICAvLyBzYWZlXG4kZXhwb3J0LlIgPSAxMjg7IC8vIHJlYWwgcHJvdG8gbWV0aG9kIGZvciBgbGlicmFyeWAgXG5tb2R1bGUuZXhwb3J0cyA9ICRleHBvcnQ7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19leHBvcnQuanNcbi8vIG1vZHVsZSBpZCA9IDhcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4Il0sInNvdXJjZVJvb3QiOiIifQ==