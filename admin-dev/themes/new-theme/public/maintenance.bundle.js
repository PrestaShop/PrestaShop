window["maintenance"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 334);
/******/ })
/************************************************************************/
/******/ ({

/***/ 32:
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

/**
 * This class init TinyMCE instances in the back-office. It is wildly inspired by
 * the scripts from js/admin And it actually loads TinyMCE from the js/tiny_mce
 * folder along with its modules. One improvement could be to install TinyMCE via
 * npm and fully integrate in the back-office theme.
 */

var TinyMCEEditor = function () {
  function TinyMCEEditor(options) {
    _classCallCheck(this, TinyMCEEditor);

    options = options || {};
    this.tinyMCELoaded = false;
    if (typeof options.baseAdminUrl == 'undefined') {
      if (typeof window.baseAdminDir != 'undefined') {
        options.baseAdminUrl = window.baseAdminDir;
      } else {
        var pathParts = window.location.pathname.split('/');
        pathParts.every(function (pathPart) {
          if (pathPart !== '') {
            options.baseAdminUrl = '/' + pathPart + '/';

            return false;
          }

          return true;
        });
      }
    }
    if (typeof options.langIsRtl == 'undefined') {
      options.langIsRtl = typeof window.lang_is_rtl != 'undefined' ? window.lang_is_rtl === '1' : false;
    }
    this.setupTinyMCE(options);
  }

  /**
   * Initial setup which checks if the tinyMCE library is already loaded.
   *
   * @param config
   */


  _createClass(TinyMCEEditor, [{
    key: 'setupTinyMCE',
    value: function setupTinyMCE(config) {
      if (typeof tinyMCE === 'undefined') {
        this.loadAndInitTinyMCE(config);
      } else {
        this.initTinyMCE(config);
      }
    }

    /**
     * Prepare the config and init all TinyMCE editors
     *
     * @param config
     */

  }, {
    key: 'initTinyMCE',
    value: function initTinyMCE(config) {
      var _this = this;

      config = Object.assign({
        selector: '.rte',
        plugins: 'align colorpicker link image filemanager table media placeholder advlist code table autoresize',
        browser_spellcheck: true,
        toolbar1: 'code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,align,bullist,numlist,table,image,media,formatselect',
        toolbar2: '',
        external_filemanager_path: config.baseAdminUrl + 'filemanager/',
        filemanager_title: 'File manager',
        external_plugins: {
          'filemanager': config.baseAdminUrl + 'filemanager/plugin.min.js'
        },
        language: iso_user,
        content_style: config.langIsRtl ? 'body {direction:rtl;}' : '',
        skin: 'prestashop',
        menubar: false,
        statusbar: false,
        relative_urls: false,
        convert_urls: false,
        entity_encoding: 'raw',
        extended_valid_elements: 'em[class|name|id],@[role|data-*|aria-*]',
        valid_children: '+*[*]',
        valid_elements: '*[*]',
        rel_list: [{ title: 'nofollow', value: 'nofollow' }],
        editor_selector: 'autoload_rte',
        init_instance_callback: function init_instance_callback() {
          _this.changeToMaterial();
        },
        setup: function setup(editor) {
          _this.setupEditor(editor);
        }
      }, config);

      if (typeof config.editor_selector != 'undefined') {
        config.selector = '.' + config.editor_selector;
      }

      // Change icons in popups
      $('body').on('click', '.mce-btn, .mce-open, .mce-menu-item', function () {
        _this.changeToMaterial();
      });

      tinyMCE.init(config);
      this.watchTabChanges(config);
    }

    /**
     * Setup TinyMCE editor once it has been initialized
     *
     * @param editor
     */

  }, {
    key: 'setupEditor',
    value: function setupEditor(editor) {
      var _this2 = this;

      editor.on('loadContent', function (event) {
        _this2.handleCounterTiny(event.target.id);
      });
      editor.on('change', function (event) {
        tinyMCE.triggerSave();
        _this2.handleCounterTiny(event.target.id);
      });
      editor.on('blur', function () {
        tinyMCE.triggerSave();
      });
    }

    /**
     * When the editor is inside a tab it can cause a bug on tab switching.
     * So we check if the editor is contained in a navigation and refresh the editor when its
     * parent tab is shown.
     *
     * @param config
     */

  }, {
    key: 'watchTabChanges',
    value: function watchTabChanges(config) {
      $(config.selector).each(function (index, textarea) {
        var translatedField = $(textarea).closest('.translation-field');
        var tabContainer = $(textarea).closest('.translations.tabbable');

        if (translatedField.length && tabContainer.length) {
          var textareaLocale = translatedField.data('locale');
          var textareaLinkSelector = '.nav-item a[data-locale="' + textareaLocale + '"]';

          $(textareaLinkSelector, tabContainer).on('shown.bs.tab', function () {
            var editor = tinyMCE.get(textarea.id);
            if (editor) {
              //Reset content to force refresh of editor
              editor.setContent(editor.getContent());
            }
          });
        }
      });
    }

    /**
     * Loads the TinyMCE javascript library and then init the editors
     *
     * @param config
     */

  }, {
    key: 'loadAndInitTinyMCE',
    value: function loadAndInitTinyMCE(config) {
      var _this3 = this;

      if (this.tinyMCELoaded) {
        return;
      }

      this.tinyMCELoaded = true;
      var pathArray = config.baseAdminUrl.split('/');
      pathArray.splice(pathArray.length - 2, 2);
      var finalPath = pathArray.join('/');
      window.tinyMCEPreInit = {};
      window.tinyMCEPreInit.base = finalPath + '/js/tiny_mce';
      window.tinyMCEPreInit.suffix = '.min';
      $.getScript(finalPath + '/js/tiny_mce/tinymce.min.js', function () {
        _this3.setupTinyMCE(config);
      });
    }

    /**
     * Replace initial TinyMCE icons with material icons
     */

  }, {
    key: 'changeToMaterial',
    value: function changeToMaterial() {
      var materialIconAssoc = {
        'mce-i-code': '<i class="material-icons">code</i>',
        'mce-i-none': '<i class="material-icons">format_color_text</i>',
        'mce-i-bold': '<i class="material-icons">format_bold</i>',
        'mce-i-italic': '<i class="material-icons">format_italic</i>',
        'mce-i-underline': '<i class="material-icons">format_underlined</i>',
        'mce-i-strikethrough': '<i class="material-icons">format_strikethrough</i>',
        'mce-i-blockquote': '<i class="material-icons">format_quote</i>',
        'mce-i-link': '<i class="material-icons">link</i>',
        'mce-i-alignleft': '<i class="material-icons">format_align_left</i>',
        'mce-i-aligncenter': '<i class="material-icons">format_align_center</i>',
        'mce-i-alignright': '<i class="material-icons">format_align_right</i>',
        'mce-i-alignjustify': '<i class="material-icons">format_align_justify</i>',
        'mce-i-bullist': '<i class="material-icons">format_list_bulleted</i>',
        'mce-i-numlist': '<i class="material-icons">format_list_numbered</i>',
        'mce-i-image': '<i class="material-icons">image</i>',
        'mce-i-table': '<i class="material-icons">grid_on</i>',
        'mce-i-media': '<i class="material-icons">video_library</i>',
        'mce-i-browse': '<i class="material-icons">attachment</i>',
        'mce-i-checkbox': '<i class="mce-ico mce-i-checkbox"></i>'
      };

      $.each(materialIconAssoc, function (index, value) {
        $('.' + index).replaceWith(value);
      });
    }

    /**
     * Updates the characters counter
     *
     * @param id
     */

  }, {
    key: 'handleCounterTiny',
    value: function handleCounterTiny(id) {
      var textarea = $('#' + id);
      var counter = textarea.attr('counter');
      var counterType = textarea.attr('counter_type');
      var max = tinyMCE.activeEditor.getBody().textContent.length;

      textarea.parent().find('span.currentLength').text(max);
      if ('recommended' !== counterType && max > counter) {
        textarea.parent().find('span.maxLength').addClass('text-danger');
      } else {
        textarea.parent().find('span.maxLength').removeClass('text-danger');
      }
    }
  }]);

  return TinyMCEEditor;
}();

exports.default = TinyMCEEditor;

/***/ }),

/***/ 334:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _tinymceEditor = __webpack_require__(32);

var _tinymceEditor2 = _interopRequireDefault(_tinymceEditor);

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
  new _tinymceEditor2.default();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQ/MjBkNCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL3RpbnltY2UtZWRpdG9yLmpzPzUyNmIqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9tYWludGVuYW5jZS9pbmRleC5qcyJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwiVGlueU1DRUVkaXRvciIsIm9wdGlvbnMiLCJ0aW55TUNFTG9hZGVkIiwiYmFzZUFkbWluVXJsIiwiYmFzZUFkbWluRGlyIiwicGF0aFBhcnRzIiwibG9jYXRpb24iLCJwYXRobmFtZSIsInNwbGl0IiwiZXZlcnkiLCJwYXRoUGFydCIsImxhbmdJc1J0bCIsImxhbmdfaXNfcnRsIiwic2V0dXBUaW55TUNFIiwiY29uZmlnIiwidGlueU1DRSIsImxvYWRBbmRJbml0VGlueU1DRSIsImluaXRUaW55TUNFIiwiT2JqZWN0IiwiYXNzaWduIiwic2VsZWN0b3IiLCJwbHVnaW5zIiwiYnJvd3Nlcl9zcGVsbGNoZWNrIiwidG9vbGJhcjEiLCJ0b29sYmFyMiIsImV4dGVybmFsX2ZpbGVtYW5hZ2VyX3BhdGgiLCJmaWxlbWFuYWdlcl90aXRsZSIsImV4dGVybmFsX3BsdWdpbnMiLCJsYW5ndWFnZSIsImlzb191c2VyIiwiY29udGVudF9zdHlsZSIsInNraW4iLCJtZW51YmFyIiwic3RhdHVzYmFyIiwicmVsYXRpdmVfdXJscyIsImNvbnZlcnRfdXJscyIsImVudGl0eV9lbmNvZGluZyIsImV4dGVuZGVkX3ZhbGlkX2VsZW1lbnRzIiwidmFsaWRfY2hpbGRyZW4iLCJ2YWxpZF9lbGVtZW50cyIsInJlbF9saXN0IiwidGl0bGUiLCJ2YWx1ZSIsImVkaXRvcl9zZWxlY3RvciIsImluaXRfaW5zdGFuY2VfY2FsbGJhY2siLCJjaGFuZ2VUb01hdGVyaWFsIiwic2V0dXAiLCJlZGl0b3IiLCJzZXR1cEVkaXRvciIsIm9uIiwiaW5pdCIsIndhdGNoVGFiQ2hhbmdlcyIsImV2ZW50IiwiaGFuZGxlQ291bnRlclRpbnkiLCJ0YXJnZXQiLCJpZCIsInRyaWdnZXJTYXZlIiwiZWFjaCIsImluZGV4IiwidGV4dGFyZWEiLCJ0cmFuc2xhdGVkRmllbGQiLCJjbG9zZXN0IiwidGFiQ29udGFpbmVyIiwibGVuZ3RoIiwidGV4dGFyZWFMb2NhbGUiLCJkYXRhIiwidGV4dGFyZWFMaW5rU2VsZWN0b3IiLCJnZXQiLCJzZXRDb250ZW50IiwiZ2V0Q29udGVudCIsInBhdGhBcnJheSIsInNwbGljZSIsImZpbmFsUGF0aCIsImpvaW4iLCJ0aW55TUNFUHJlSW5pdCIsImJhc2UiLCJzdWZmaXgiLCJnZXRTY3JpcHQiLCJtYXRlcmlhbEljb25Bc3NvYyIsInJlcGxhY2VXaXRoIiwiY291bnRlciIsImF0dHIiLCJjb3VudGVyVHlwZSIsIm1heCIsImFjdGl2ZUVkaXRvciIsImdldEJvZHkiLCJ0ZXh0Q29udGVudCIsInBhcmVudCIsImZpbmQiLCJ0ZXh0IiwiYWRkQ2xhc3MiLCJyZW1vdmVDbGFzcyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQ2hFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztB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ZW5hbmNlLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMzM0KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCA2OGU4MjkxZjEzNjA3MGYyNzZiZCIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBUaGlzIGNsYXNzIGluaXQgVGlueU1DRSBpbnN0YW5jZXMgaW4gdGhlIGJhY2stb2ZmaWNlLiBJdCBpcyB3aWxkbHkgaW5zcGlyZWQgYnlcbiAqIHRoZSBzY3JpcHRzIGZyb20ganMvYWRtaW4gQW5kIGl0IGFjdHVhbGx5IGxvYWRzIFRpbnlNQ0UgZnJvbSB0aGUganMvdGlueV9tY2VcbiAqIGZvbGRlciBhbG9uZyB3aXRoIGl0cyBtb2R1bGVzLiBPbmUgaW1wcm92ZW1lbnQgY291bGQgYmUgdG8gaW5zdGFsbCBUaW55TUNFIHZpYVxuICogbnBtIGFuZCBmdWxseSBpbnRlZ3JhdGUgaW4gdGhlIGJhY2stb2ZmaWNlIHRoZW1lLlxuICovXG5jbGFzcyBUaW55TUNFRWRpdG9yIHtcbiAgY29uc3RydWN0b3Iob3B0aW9ucykge1xuICAgIG9wdGlvbnMgPSBvcHRpb25zIHx8IHt9O1xuICAgIHRoaXMudGlueU1DRUxvYWRlZCA9IGZhbHNlO1xuICAgIGlmICh0eXBlb2Ygb3B0aW9ucy5iYXNlQWRtaW5VcmwgPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgIGlmICh0eXBlb2Ygd2luZG93LmJhc2VBZG1pbkRpciAhPSAndW5kZWZpbmVkJykge1xuICAgICAgICBvcHRpb25zLmJhc2VBZG1pblVybCA9IHdpbmRvdy5iYXNlQWRtaW5EaXI7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBjb25zdCBwYXRoUGFydHMgPSB3aW5kb3cubG9jYXRpb24ucGF0aG5hbWUuc3BsaXQoJy8nKTtcbiAgICAgICAgcGF0aFBhcnRzLmV2ZXJ5KGZ1bmN0aW9uKHBhdGhQYXJ0KSB7XG4gICAgICAgICAgaWYgKHBhdGhQYXJ0ICE9PSAnJykge1xuICAgICAgICAgICAgb3B0aW9ucy5iYXNlQWRtaW5VcmwgPSBgLyR7cGF0aFBhcnR9L2A7XG5cbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgfVxuICAgIGlmICh0eXBlb2Ygb3B0aW9ucy5sYW5nSXNSdGwgPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgIG9wdGlvbnMubGFuZ0lzUnRsID0gdHlwZW9mIHdpbmRvdy5sYW5nX2lzX3J0bCAhPSAndW5kZWZpbmVkJyA/IHdpbmRvdy5sYW5nX2lzX3J0bCA9PT0gJzEnIDogZmFsc2U7XG4gICAgfVxuICAgIHRoaXMuc2V0dXBUaW55TUNFKG9wdGlvbnMpO1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRpYWwgc2V0dXAgd2hpY2ggY2hlY2tzIGlmIHRoZSB0aW55TUNFIGxpYnJhcnkgaXMgYWxyZWFkeSBsb2FkZWQuXG4gICAqXG4gICAqIEBwYXJhbSBjb25maWdcbiAgICovXG4gIHNldHVwVGlueU1DRShjb25maWcpIHtcbiAgICBpZiAodHlwZW9mIHRpbnlNQ0UgPT09ICd1bmRlZmluZWQnKSB7XG4gICAgICB0aGlzLmxvYWRBbmRJbml0VGlueU1DRShjb25maWcpO1xuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLmluaXRUaW55TUNFKGNvbmZpZyk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIFByZXBhcmUgdGhlIGNvbmZpZyBhbmQgaW5pdCBhbGwgVGlueU1DRSBlZGl0b3JzXG4gICAqXG4gICAqIEBwYXJhbSBjb25maWdcbiAgICovXG4gIGluaXRUaW55TUNFKGNvbmZpZykge1xuICAgIGNvbmZpZyA9IE9iamVjdC5hc3NpZ24oe1xuICAgICAgc2VsZWN0b3I6ICcucnRlJyxcbiAgICAgIHBsdWdpbnM6ICdhbGlnbiBjb2xvcnBpY2tlciBsaW5rIGltYWdlIGZpbGVtYW5hZ2VyIHRhYmxlIG1lZGlhIHBsYWNlaG9sZGVyIGFkdmxpc3QgY29kZSB0YWJsZSBhdXRvcmVzaXplJyxcbiAgICAgIGJyb3dzZXJfc3BlbGxjaGVjazogdHJ1ZSxcbiAgICAgIHRvb2xiYXIxOiAnY29kZSxjb2xvcnBpY2tlcixib2xkLGl0YWxpYyx1bmRlcmxpbmUsc3RyaWtldGhyb3VnaCxibG9ja3F1b3RlLGxpbmssYWxpZ24sYnVsbGlzdCxudW1saXN0LHRhYmxlLGltYWdlLG1lZGlhLGZvcm1hdHNlbGVjdCcsXG4gICAgICB0b29sYmFyMjogJycsXG4gICAgICBleHRlcm5hbF9maWxlbWFuYWdlcl9wYXRoOiBjb25maWcuYmFzZUFkbWluVXJsICsgJ2ZpbGVtYW5hZ2VyLycsXG4gICAgICBmaWxlbWFuYWdlcl90aXRsZTogJ0ZpbGUgbWFuYWdlcicsXG4gICAgICBleHRlcm5hbF9wbHVnaW5zOiB7XG4gICAgICAgICdmaWxlbWFuYWdlcic6IGNvbmZpZy5iYXNlQWRtaW5VcmwgKyAnZmlsZW1hbmFnZXIvcGx1Z2luLm1pbi5qcydcbiAgICAgIH0sXG4gICAgICBsYW5ndWFnZTogaXNvX3VzZXIsXG4gICAgICBjb250ZW50X3N0eWxlIDogKGNvbmZpZy5sYW5nSXNSdGwgPyAnYm9keSB7ZGlyZWN0aW9uOnJ0bDt9JyA6ICcnKSxcbiAgICAgIHNraW46ICdwcmVzdGFzaG9wJyxcbiAgICAgIG1lbnViYXI6IGZhbHNlLFxuICAgICAgc3RhdHVzYmFyOiBmYWxzZSxcbiAgICAgIHJlbGF0aXZlX3VybHM6IGZhbHNlLFxuICAgICAgY29udmVydF91cmxzOiBmYWxzZSxcbiAgICAgIGVudGl0eV9lbmNvZGluZzogJ3JhdycsXG4gICAgICBleHRlbmRlZF92YWxpZF9lbGVtZW50czogJ2VtW2NsYXNzfG5hbWV8aWRdLEBbcm9sZXxkYXRhLSp8YXJpYS0qXScsXG4gICAgICB2YWxpZF9jaGlsZHJlbjogJysqWypdJyxcbiAgICAgIHZhbGlkX2VsZW1lbnRzOiAnKlsqXScsXG4gICAgICByZWxfbGlzdDpbXG4gICAgICAgIHsgdGl0bGU6ICdub2ZvbGxvdycsIHZhbHVlOiAnbm9mb2xsb3cnIH1cbiAgICAgIF0sXG4gICAgICBlZGl0b3Jfc2VsZWN0b3IgOidhdXRvbG9hZF9ydGUnLFxuICAgICAgaW5pdF9pbnN0YW5jZV9jYWxsYmFjazogKCkgPT4geyB0aGlzLmNoYW5nZVRvTWF0ZXJpYWwoKTsgfSxcbiAgICAgIHNldHVwIDogKGVkaXRvcikgPT4geyB0aGlzLnNldHVwRWRpdG9yKGVkaXRvcik7IH0sXG4gICAgfSwgY29uZmlnKTtcblxuICAgIGlmICh0eXBlb2YgY29uZmlnLmVkaXRvcl9zZWxlY3RvciAhPSAndW5kZWZpbmVkJykge1xuICAgICAgY29uZmlnLnNlbGVjdG9yID0gJy4nICsgY29uZmlnLmVkaXRvcl9zZWxlY3RvcjtcbiAgICB9XG5cbiAgICAvLyBDaGFuZ2UgaWNvbnMgaW4gcG9wdXBzXG4gICAgJCgnYm9keScpLm9uKCdjbGljaycsICcubWNlLWJ0biwgLm1jZS1vcGVuLCAubWNlLW1lbnUtaXRlbScsICgpID0+IHsgdGhpcy5jaGFuZ2VUb01hdGVyaWFsKCk7IH0pO1xuXG4gICAgdGlueU1DRS5pbml0KGNvbmZpZyk7XG4gICAgdGhpcy53YXRjaFRhYkNoYW5nZXMoY29uZmlnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTZXR1cCBUaW55TUNFIGVkaXRvciBvbmNlIGl0IGhhcyBiZWVuIGluaXRpYWxpemVkXG4gICAqXG4gICAqIEBwYXJhbSBlZGl0b3JcbiAgICovXG4gIHNldHVwRWRpdG9yKGVkaXRvcikge1xuICAgIGVkaXRvci5vbignbG9hZENvbnRlbnQnLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMuaGFuZGxlQ291bnRlclRpbnkoZXZlbnQudGFyZ2V0LmlkKTtcbiAgICB9KTtcbiAgICBlZGl0b3Iub24oJ2NoYW5nZScsIChldmVudCkgPT4ge1xuICAgICAgdGlueU1DRS50cmlnZ2VyU2F2ZSgpO1xuICAgICAgdGhpcy5oYW5kbGVDb3VudGVyVGlueShldmVudC50YXJnZXQuaWQpO1xuICAgIH0pO1xuICAgIGVkaXRvci5vbignYmx1cicsICgpID0+IHtcbiAgICAgIHRpbnlNQ0UudHJpZ2dlclNhdmUoKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBXaGVuIHRoZSBlZGl0b3IgaXMgaW5zaWRlIGEgdGFiIGl0IGNhbiBjYXVzZSBhIGJ1ZyBvbiB0YWIgc3dpdGNoaW5nLlxuICAgKiBTbyB3ZSBjaGVjayBpZiB0aGUgZWRpdG9yIGlzIGNvbnRhaW5lZCBpbiBhIG5hdmlnYXRpb24gYW5kIHJlZnJlc2ggdGhlIGVkaXRvciB3aGVuIGl0c1xuICAgKiBwYXJlbnQgdGFiIGlzIHNob3duLlxuICAgKlxuICAgKiBAcGFyYW0gY29uZmlnXG4gICAqL1xuICB3YXRjaFRhYkNoYW5nZXMoY29uZmlnKSB7XG4gICAgJChjb25maWcuc2VsZWN0b3IpLmVhY2goKGluZGV4LCB0ZXh0YXJlYSkgPT4ge1xuICAgICAgY29uc3QgdHJhbnNsYXRlZEZpZWxkID0gJCh0ZXh0YXJlYSkuY2xvc2VzdCgnLnRyYW5zbGF0aW9uLWZpZWxkJyk7XG4gICAgICBjb25zdCB0YWJDb250YWluZXIgPSAkKHRleHRhcmVhKS5jbG9zZXN0KCcudHJhbnNsYXRpb25zLnRhYmJhYmxlJyk7XG5cbiAgICAgIGlmICh0cmFuc2xhdGVkRmllbGQubGVuZ3RoICYmIHRhYkNvbnRhaW5lci5sZW5ndGgpIHtcbiAgICAgICAgY29uc3QgdGV4dGFyZWFMb2NhbGUgPSB0cmFuc2xhdGVkRmllbGQuZGF0YSgnbG9jYWxlJyk7XG4gICAgICAgIGNvbnN0IHRleHRhcmVhTGlua1NlbGVjdG9yID0gJy5uYXYtaXRlbSBhW2RhdGEtbG9jYWxlPVwiJyt0ZXh0YXJlYUxvY2FsZSsnXCJdJztcblxuICAgICAgICAkKHRleHRhcmVhTGlua1NlbGVjdG9yLCB0YWJDb250YWluZXIpLm9uKCdzaG93bi5icy50YWInLCAoKSA9PiB7XG4gICAgICAgICAgY29uc3QgZWRpdG9yID0gdGlueU1DRS5nZXQodGV4dGFyZWEuaWQpO1xuICAgICAgICAgIGlmIChlZGl0b3IpIHtcbiAgICAgICAgICAgIC8vUmVzZXQgY29udGVudCB0byBmb3JjZSByZWZyZXNoIG9mIGVkaXRvclxuICAgICAgICAgICAgZWRpdG9yLnNldENvbnRlbnQoZWRpdG9yLmdldENvbnRlbnQoKSk7XG4gICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMb2FkcyB0aGUgVGlueU1DRSBqYXZhc2NyaXB0IGxpYnJhcnkgYW5kIHRoZW4gaW5pdCB0aGUgZWRpdG9yc1xuICAgKlxuICAgKiBAcGFyYW0gY29uZmlnXG4gICAqL1xuICBsb2FkQW5kSW5pdFRpbnlNQ0UoY29uZmlnKSB7XG4gICAgaWYgKHRoaXMudGlueU1DRUxvYWRlZCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHRoaXMudGlueU1DRUxvYWRlZCA9IHRydWU7XG4gICAgY29uc3QgcGF0aEFycmF5ID0gY29uZmlnLmJhc2VBZG1pblVybC5zcGxpdCgnLycpO1xuICAgIHBhdGhBcnJheS5zcGxpY2UoKHBhdGhBcnJheS5sZW5ndGggLSAyKSwgMik7XG4gICAgY29uc3QgZmluYWxQYXRoID0gcGF0aEFycmF5LmpvaW4oJy8nKTtcbiAgICB3aW5kb3cudGlueU1DRVByZUluaXQgPSB7fTtcbiAgICB3aW5kb3cudGlueU1DRVByZUluaXQuYmFzZSA9IGZpbmFsUGF0aCsnL2pzL3RpbnlfbWNlJztcbiAgICB3aW5kb3cudGlueU1DRVByZUluaXQuc3VmZml4ID0gJy5taW4nO1xuICAgICQuZ2V0U2NyaXB0KGAke2ZpbmFsUGF0aH0vanMvdGlueV9tY2UvdGlueW1jZS5taW4uanNgLCAoKSA9PiB7dGhpcy5zZXR1cFRpbnlNQ0UoY29uZmlnKX0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlcGxhY2UgaW5pdGlhbCBUaW55TUNFIGljb25zIHdpdGggbWF0ZXJpYWwgaWNvbnNcbiAgICovXG4gIGNoYW5nZVRvTWF0ZXJpYWwoKSB7XG4gICAgbGV0IG1hdGVyaWFsSWNvbkFzc29jID0ge1xuICAgICAgJ21jZS1pLWNvZGUnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmNvZGU8L2k+JyxcbiAgICAgICdtY2UtaS1ub25lJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfY29sb3JfdGV4dDwvaT4nLFxuICAgICAgJ21jZS1pLWJvbGQnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9ib2xkPC9pPicsXG4gICAgICAnbWNlLWktaXRhbGljJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfaXRhbGljPC9pPicsXG4gICAgICAnbWNlLWktdW5kZXJsaW5lJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfdW5kZXJsaW5lZDwvaT4nLFxuICAgICAgJ21jZS1pLXN0cmlrZXRocm91Z2gnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9zdHJpa2V0aHJvdWdoPC9pPicsXG4gICAgICAnbWNlLWktYmxvY2txdW90ZSc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X3F1b3RlPC9pPicsXG4gICAgICAnbWNlLWktbGluayc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+bGluazwvaT4nLFxuICAgICAgJ21jZS1pLWFsaWdubGVmdCc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X2FsaWduX2xlZnQ8L2k+JyxcbiAgICAgICdtY2UtaS1hbGlnbmNlbnRlcic6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X2FsaWduX2NlbnRlcjwvaT4nLFxuICAgICAgJ21jZS1pLWFsaWducmlnaHQnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9hbGlnbl9yaWdodDwvaT4nLFxuICAgICAgJ21jZS1pLWFsaWduanVzdGlmeSc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X2FsaWduX2p1c3RpZnk8L2k+JyxcbiAgICAgICdtY2UtaS1idWxsaXN0JzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfbGlzdF9idWxsZXRlZDwvaT4nLFxuICAgICAgJ21jZS1pLW51bWxpc3QnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9saXN0X251bWJlcmVkPC9pPicsXG4gICAgICAnbWNlLWktaW1hZ2UnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmltYWdlPC9pPicsXG4gICAgICAnbWNlLWktdGFibGUnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmdyaWRfb248L2k+JyxcbiAgICAgICdtY2UtaS1tZWRpYSc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+dmlkZW9fbGlicmFyeTwvaT4nLFxuICAgICAgJ21jZS1pLWJyb3dzZSc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+YXR0YWNobWVudDwvaT4nLFxuICAgICAgJ21jZS1pLWNoZWNrYm94JzogJzxpIGNsYXNzPVwibWNlLWljbyBtY2UtaS1jaGVja2JveFwiPjwvaT4nLFxuICAgIH07XG5cbiAgICAkLmVhY2gobWF0ZXJpYWxJY29uQXNzb2MsIGZ1bmN0aW9uIChpbmRleCwgdmFsdWUpIHtcbiAgICAgICQoYC4ke2luZGV4fWApLnJlcGxhY2VXaXRoKHZhbHVlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBVcGRhdGVzIHRoZSBjaGFyYWN0ZXJzIGNvdW50ZXJcbiAgICpcbiAgICogQHBhcmFtIGlkXG4gICAqL1xuICBoYW5kbGVDb3VudGVyVGlueShpZCkge1xuICAgIGNvbnN0IHRleHRhcmVhID0gJChgIyR7aWR9YCk7XG4gICAgY29uc3QgY291bnRlciA9IHRleHRhcmVhLmF0dHIoJ2NvdW50ZXInKTtcbiAgICBjb25zdCBjb3VudGVyVHlwZSA9IHRleHRhcmVhLmF0dHIoJ2NvdW50ZXJfdHlwZScpO1xuICAgIGNvbnN0IG1heCA9IHRpbnlNQ0UuYWN0aXZlRWRpdG9yLmdldEJvZHkoKS50ZXh0Q29udGVudC5sZW5ndGg7XG5cbiAgICB0ZXh0YXJlYS5wYXJlbnQoKS5maW5kKCdzcGFuLmN1cnJlbnRMZW5ndGgnKS50ZXh0KG1heCk7XG4gICAgaWYgKCdyZWNvbW1lbmRlZCcgIT09IGNvdW50ZXJUeXBlICYmIG1heCA+IGNvdW50ZXIpIHtcbiAgICAgIHRleHRhcmVhLnBhcmVudCgpLmZpbmQoJ3NwYW4ubWF4TGVuZ3RoJykuYWRkQ2xhc3MoJ3RleHQtZGFuZ2VyJyk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRleHRhcmVhLnBhcmVudCgpLmZpbmQoJ3NwYW4ubWF4TGVuZ3RoJykucmVtb3ZlQ2xhc3MoJ3RleHQtZGFuZ2VyJyk7XG4gICAgfVxuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IFRpbnlNQ0VFZGl0b3I7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL3RpbnltY2UtZWRpdG9yLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IFRpbnlNQ0VFZGl0b3IgZnJvbSAnLi4vLi4vY29tcG9uZW50cy90aW55bWNlLWVkaXRvcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gIG5ldyBUaW55TUNFRWRpdG9yKCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL21haW50ZW5hbmNlL2luZGV4LmpzIl0sInNvdXJjZVJvb3QiOiIifQ==