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
/******/ 	return __webpack_require__(__webpack_require__.s = 335);
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

/***/ 335:
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODI/M2YxNioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvdGlueW1jZS1lZGl0b3IuanM/NTI2YioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL21haW50ZW5hbmNlL2luZGV4LmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJUaW55TUNFRWRpdG9yIiwib3B0aW9ucyIsInRpbnlNQ0VMb2FkZWQiLCJiYXNlQWRtaW5VcmwiLCJiYXNlQWRtaW5EaXIiLCJwYXRoUGFydHMiLCJsb2NhdGlvbiIsInBhdGhuYW1lIiwic3BsaXQiLCJldmVyeSIsInBhdGhQYXJ0IiwibGFuZ0lzUnRsIiwibGFuZ19pc19ydGwiLCJzZXR1cFRpbnlNQ0UiLCJjb25maWciLCJ0aW55TUNFIiwibG9hZEFuZEluaXRUaW55TUNFIiwiaW5pdFRpbnlNQ0UiLCJPYmplY3QiLCJhc3NpZ24iLCJzZWxlY3RvciIsInBsdWdpbnMiLCJicm93c2VyX3NwZWxsY2hlY2siLCJ0b29sYmFyMSIsInRvb2xiYXIyIiwiZXh0ZXJuYWxfZmlsZW1hbmFnZXJfcGF0aCIsImZpbGVtYW5hZ2VyX3RpdGxlIiwiZXh0ZXJuYWxfcGx1Z2lucyIsImxhbmd1YWdlIiwiaXNvX3VzZXIiLCJjb250ZW50X3N0eWxlIiwic2tpbiIsIm1lbnViYXIiLCJzdGF0dXNiYXIiLCJyZWxhdGl2ZV91cmxzIiwiY29udmVydF91cmxzIiwiZW50aXR5X2VuY29kaW5nIiwiZXh0ZW5kZWRfdmFsaWRfZWxlbWVudHMiLCJ2YWxpZF9jaGlsZHJlbiIsInZhbGlkX2VsZW1lbnRzIiwicmVsX2xpc3QiLCJ0aXRsZSIsInZhbHVlIiwiZWRpdG9yX3NlbGVjdG9yIiwiaW5pdF9pbnN0YW5jZV9jYWxsYmFjayIsImNoYW5nZVRvTWF0ZXJpYWwiLCJzZXR1cCIsImVkaXRvciIsInNldHVwRWRpdG9yIiwib24iLCJpbml0Iiwid2F0Y2hUYWJDaGFuZ2VzIiwiZXZlbnQiLCJoYW5kbGVDb3VudGVyVGlueSIsInRhcmdldCIsImlkIiwidHJpZ2dlclNhdmUiLCJlYWNoIiwiaW5kZXgiLCJ0ZXh0YXJlYSIsInRyYW5zbGF0ZWRGaWVsZCIsImNsb3Nlc3QiLCJ0YWJDb250YWluZXIiLCJsZW5ndGgiLCJ0ZXh0YXJlYUxvY2FsZSIsImRhdGEiLCJ0ZXh0YXJlYUxpbmtTZWxlY3RvciIsImdldCIsInNldENvbnRlbnQiLCJnZXRDb250ZW50IiwicGF0aEFycmF5Iiwic3BsaWNlIiwiZmluYWxQYXRoIiwiam9pbiIsInRpbnlNQ0VQcmVJbml0IiwiYmFzZSIsInN1ZmZpeCIsImdldFNjcmlwdCIsIm1hdGVyaWFsSWNvbkFzc29jIiwicmVwbGFjZVdpdGgiLCJjb3VudGVyIiwiYXR0ciIsImNvdW50ZXJUeXBlIiwibWF4IiwiYWN0aXZlRWRpdG9yIiwiZ2V0Qm9keSIsInRleHRDb250ZW50IiwicGFyZW50IiwiZmluZCIsInRleHQiLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDaEVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FB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bmFuY2UuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAzMzUpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDVkOTk5MDk0ZDExYWVmZjBiNTgyIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFRoaXMgY2xhc3MgaW5pdCBUaW55TUNFIGluc3RhbmNlcyBpbiB0aGUgYmFjay1vZmZpY2UuIEl0IGlzIHdpbGRseSBpbnNwaXJlZCBieVxuICogdGhlIHNjcmlwdHMgZnJvbSBqcy9hZG1pbiBBbmQgaXQgYWN0dWFsbHkgbG9hZHMgVGlueU1DRSBmcm9tIHRoZSBqcy90aW55X21jZVxuICogZm9sZGVyIGFsb25nIHdpdGggaXRzIG1vZHVsZXMuIE9uZSBpbXByb3ZlbWVudCBjb3VsZCBiZSB0byBpbnN0YWxsIFRpbnlNQ0UgdmlhXG4gKiBucG0gYW5kIGZ1bGx5IGludGVncmF0ZSBpbiB0aGUgYmFjay1vZmZpY2UgdGhlbWUuXG4gKi9cbmNsYXNzIFRpbnlNQ0VFZGl0b3Ige1xuICBjb25zdHJ1Y3RvcihvcHRpb25zKSB7XG4gICAgb3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG4gICAgdGhpcy50aW55TUNFTG9hZGVkID0gZmFsc2U7XG4gICAgaWYgKHR5cGVvZiBvcHRpb25zLmJhc2VBZG1pblVybCA9PSAndW5kZWZpbmVkJykge1xuICAgICAgaWYgKHR5cGVvZiB3aW5kb3cuYmFzZUFkbWluRGlyICE9ICd1bmRlZmluZWQnKSB7XG4gICAgICAgIG9wdGlvbnMuYmFzZUFkbWluVXJsID0gd2luZG93LmJhc2VBZG1pbkRpcjtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGNvbnN0IHBhdGhQYXJ0cyA9IHdpbmRvdy5sb2NhdGlvbi5wYXRobmFtZS5zcGxpdCgnLycpO1xuICAgICAgICBwYXRoUGFydHMuZXZlcnkoZnVuY3Rpb24ocGF0aFBhcnQpIHtcbiAgICAgICAgICBpZiAocGF0aFBhcnQgIT09ICcnKSB7XG4gICAgICAgICAgICBvcHRpb25zLmJhc2VBZG1pblVybCA9IGAvJHtwYXRoUGFydH0vYDtcblxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgIH1cblxuICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICB9XG4gICAgaWYgKHR5cGVvZiBvcHRpb25zLmxhbmdJc1J0bCA9PSAndW5kZWZpbmVkJykge1xuICAgICAgb3B0aW9ucy5sYW5nSXNSdGwgPSB0eXBlb2Ygd2luZG93LmxhbmdfaXNfcnRsICE9ICd1bmRlZmluZWQnID8gd2luZG93LmxhbmdfaXNfcnRsID09PSAnMScgOiBmYWxzZTtcbiAgICB9XG4gICAgdGhpcy5zZXR1cFRpbnlNQ0Uob3B0aW9ucyk7XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbCBzZXR1cCB3aGljaCBjaGVja3MgaWYgdGhlIHRpbnlNQ0UgbGlicmFyeSBpcyBhbHJlYWR5IGxvYWRlZC5cbiAgICpcbiAgICogQHBhcmFtIGNvbmZpZ1xuICAgKi9cbiAgc2V0dXBUaW55TUNFKGNvbmZpZykge1xuICAgIGlmICh0eXBlb2YgdGlueU1DRSA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgIHRoaXMubG9hZEFuZEluaXRUaW55TUNFKGNvbmZpZyk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuaW5pdFRpbnlNQ0UoY29uZmlnKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogUHJlcGFyZSB0aGUgY29uZmlnIGFuZCBpbml0IGFsbCBUaW55TUNFIGVkaXRvcnNcbiAgICpcbiAgICogQHBhcmFtIGNvbmZpZ1xuICAgKi9cbiAgaW5pdFRpbnlNQ0UoY29uZmlnKSB7XG4gICAgY29uZmlnID0gT2JqZWN0LmFzc2lnbih7XG4gICAgICBzZWxlY3RvcjogJy5ydGUnLFxuICAgICAgcGx1Z2luczogJ2FsaWduIGNvbG9ycGlja2VyIGxpbmsgaW1hZ2UgZmlsZW1hbmFnZXIgdGFibGUgbWVkaWEgcGxhY2Vob2xkZXIgYWR2bGlzdCBjb2RlIHRhYmxlIGF1dG9yZXNpemUnLFxuICAgICAgYnJvd3Nlcl9zcGVsbGNoZWNrOiB0cnVlLFxuICAgICAgdG9vbGJhcjE6ICdjb2RlLGNvbG9ycGlja2VyLGJvbGQsaXRhbGljLHVuZGVybGluZSxzdHJpa2V0aHJvdWdoLGJsb2NrcXVvdGUsbGluayxhbGlnbixidWxsaXN0LG51bWxpc3QsdGFibGUsaW1hZ2UsbWVkaWEsZm9ybWF0c2VsZWN0JyxcbiAgICAgIHRvb2xiYXIyOiAnJyxcbiAgICAgIGV4dGVybmFsX2ZpbGVtYW5hZ2VyX3BhdGg6IGNvbmZpZy5iYXNlQWRtaW5VcmwgKyAnZmlsZW1hbmFnZXIvJyxcbiAgICAgIGZpbGVtYW5hZ2VyX3RpdGxlOiAnRmlsZSBtYW5hZ2VyJyxcbiAgICAgIGV4dGVybmFsX3BsdWdpbnM6IHtcbiAgICAgICAgJ2ZpbGVtYW5hZ2VyJzogY29uZmlnLmJhc2VBZG1pblVybCArICdmaWxlbWFuYWdlci9wbHVnaW4ubWluLmpzJ1xuICAgICAgfSxcbiAgICAgIGxhbmd1YWdlOiBpc29fdXNlcixcbiAgICAgIGNvbnRlbnRfc3R5bGUgOiAoY29uZmlnLmxhbmdJc1J0bCA/ICdib2R5IHtkaXJlY3Rpb246cnRsO30nIDogJycpLFxuICAgICAgc2tpbjogJ3ByZXN0YXNob3AnLFxuICAgICAgbWVudWJhcjogZmFsc2UsXG4gICAgICBzdGF0dXNiYXI6IGZhbHNlLFxuICAgICAgcmVsYXRpdmVfdXJsczogZmFsc2UsXG4gICAgICBjb252ZXJ0X3VybHM6IGZhbHNlLFxuICAgICAgZW50aXR5X2VuY29kaW5nOiAncmF3JyxcbiAgICAgIGV4dGVuZGVkX3ZhbGlkX2VsZW1lbnRzOiAnZW1bY2xhc3N8bmFtZXxpZF0sQFtyb2xlfGRhdGEtKnxhcmlhLSpdJyxcbiAgICAgIHZhbGlkX2NoaWxkcmVuOiAnKypbKl0nLFxuICAgICAgdmFsaWRfZWxlbWVudHM6ICcqWypdJyxcbiAgICAgIHJlbF9saXN0OltcbiAgICAgICAgeyB0aXRsZTogJ25vZm9sbG93JywgdmFsdWU6ICdub2ZvbGxvdycgfVxuICAgICAgXSxcbiAgICAgIGVkaXRvcl9zZWxlY3RvciA6J2F1dG9sb2FkX3J0ZScsXG4gICAgICBpbml0X2luc3RhbmNlX2NhbGxiYWNrOiAoKSA9PiB7IHRoaXMuY2hhbmdlVG9NYXRlcmlhbCgpOyB9LFxuICAgICAgc2V0dXAgOiAoZWRpdG9yKSA9PiB7IHRoaXMuc2V0dXBFZGl0b3IoZWRpdG9yKTsgfSxcbiAgICB9LCBjb25maWcpO1xuXG4gICAgaWYgKHR5cGVvZiBjb25maWcuZWRpdG9yX3NlbGVjdG9yICE9ICd1bmRlZmluZWQnKSB7XG4gICAgICBjb25maWcuc2VsZWN0b3IgPSAnLicgKyBjb25maWcuZWRpdG9yX3NlbGVjdG9yO1xuICAgIH1cblxuICAgIC8vIENoYW5nZSBpY29ucyBpbiBwb3B1cHNcbiAgICAkKCdib2R5Jykub24oJ2NsaWNrJywgJy5tY2UtYnRuLCAubWNlLW9wZW4sIC5tY2UtbWVudS1pdGVtJywgKCkgPT4geyB0aGlzLmNoYW5nZVRvTWF0ZXJpYWwoKTsgfSk7XG5cbiAgICB0aW55TUNFLmluaXQoY29uZmlnKTtcbiAgICB0aGlzLndhdGNoVGFiQ2hhbmdlcyhjb25maWcpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNldHVwIFRpbnlNQ0UgZWRpdG9yIG9uY2UgaXQgaGFzIGJlZW4gaW5pdGlhbGl6ZWRcbiAgICpcbiAgICogQHBhcmFtIGVkaXRvclxuICAgKi9cbiAgc2V0dXBFZGl0b3IoZWRpdG9yKSB7XG4gICAgZWRpdG9yLm9uKCdsb2FkQ29udGVudCcsIChldmVudCkgPT4ge1xuICAgICAgdGhpcy5oYW5kbGVDb3VudGVyVGlueShldmVudC50YXJnZXQuaWQpO1xuICAgIH0pO1xuICAgIGVkaXRvci5vbignY2hhbmdlJywgKGV2ZW50KSA9PiB7XG4gICAgICB0aW55TUNFLnRyaWdnZXJTYXZlKCk7XG4gICAgICB0aGlzLmhhbmRsZUNvdW50ZXJUaW55KGV2ZW50LnRhcmdldC5pZCk7XG4gICAgfSk7XG4gICAgZWRpdG9yLm9uKCdibHVyJywgKCkgPT4ge1xuICAgICAgdGlueU1DRS50cmlnZ2VyU2F2ZSgpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFdoZW4gdGhlIGVkaXRvciBpcyBpbnNpZGUgYSB0YWIgaXQgY2FuIGNhdXNlIGEgYnVnIG9uIHRhYiBzd2l0Y2hpbmcuXG4gICAqIFNvIHdlIGNoZWNrIGlmIHRoZSBlZGl0b3IgaXMgY29udGFpbmVkIGluIGEgbmF2aWdhdGlvbiBhbmQgcmVmcmVzaCB0aGUgZWRpdG9yIHdoZW4gaXRzXG4gICAqIHBhcmVudCB0YWIgaXMgc2hvd24uXG4gICAqXG4gICAqIEBwYXJhbSBjb25maWdcbiAgICovXG4gIHdhdGNoVGFiQ2hhbmdlcyhjb25maWcpIHtcbiAgICAkKGNvbmZpZy5zZWxlY3RvcikuZWFjaCgoaW5kZXgsIHRleHRhcmVhKSA9PiB7XG4gICAgICBjb25zdCB0cmFuc2xhdGVkRmllbGQgPSAkKHRleHRhcmVhKS5jbG9zZXN0KCcudHJhbnNsYXRpb24tZmllbGQnKTtcbiAgICAgIGNvbnN0IHRhYkNvbnRhaW5lciA9ICQodGV4dGFyZWEpLmNsb3Nlc3QoJy50cmFuc2xhdGlvbnMudGFiYmFibGUnKTtcblxuICAgICAgaWYgKHRyYW5zbGF0ZWRGaWVsZC5sZW5ndGggJiYgdGFiQ29udGFpbmVyLmxlbmd0aCkge1xuICAgICAgICBjb25zdCB0ZXh0YXJlYUxvY2FsZSA9IHRyYW5zbGF0ZWRGaWVsZC5kYXRhKCdsb2NhbGUnKTtcbiAgICAgICAgY29uc3QgdGV4dGFyZWFMaW5rU2VsZWN0b3IgPSAnLm5hdi1pdGVtIGFbZGF0YS1sb2NhbGU9XCInK3RleHRhcmVhTG9jYWxlKydcIl0nO1xuXG4gICAgICAgICQodGV4dGFyZWFMaW5rU2VsZWN0b3IsIHRhYkNvbnRhaW5lcikub24oJ3Nob3duLmJzLnRhYicsICgpID0+IHtcbiAgICAgICAgICBjb25zdCBlZGl0b3IgPSB0aW55TUNFLmdldCh0ZXh0YXJlYS5pZCk7XG4gICAgICAgICAgaWYgKGVkaXRvcikge1xuICAgICAgICAgICAgLy9SZXNldCBjb250ZW50IHRvIGZvcmNlIHJlZnJlc2ggb2YgZWRpdG9yXG4gICAgICAgICAgICBlZGl0b3Iuc2V0Q29udGVudChlZGl0b3IuZ2V0Q29udGVudCgpKTtcbiAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExvYWRzIHRoZSBUaW55TUNFIGphdmFzY3JpcHQgbGlicmFyeSBhbmQgdGhlbiBpbml0IHRoZSBlZGl0b3JzXG4gICAqXG4gICAqIEBwYXJhbSBjb25maWdcbiAgICovXG4gIGxvYWRBbmRJbml0VGlueU1DRShjb25maWcpIHtcbiAgICBpZiAodGhpcy50aW55TUNFTG9hZGVkKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdGhpcy50aW55TUNFTG9hZGVkID0gdHJ1ZTtcbiAgICBjb25zdCBwYXRoQXJyYXkgPSBjb25maWcuYmFzZUFkbWluVXJsLnNwbGl0KCcvJyk7XG4gICAgcGF0aEFycmF5LnNwbGljZSgocGF0aEFycmF5Lmxlbmd0aCAtIDIpLCAyKTtcbiAgICBjb25zdCBmaW5hbFBhdGggPSBwYXRoQXJyYXkuam9pbignLycpO1xuICAgIHdpbmRvdy50aW55TUNFUHJlSW5pdCA9IHt9O1xuICAgIHdpbmRvdy50aW55TUNFUHJlSW5pdC5iYXNlID0gZmluYWxQYXRoKycvanMvdGlueV9tY2UnO1xuICAgIHdpbmRvdy50aW55TUNFUHJlSW5pdC5zdWZmaXggPSAnLm1pbic7XG4gICAgJC5nZXRTY3JpcHQoYCR7ZmluYWxQYXRofS9qcy90aW55X21jZS90aW55bWNlLm1pbi5qc2AsICgpID0+IHt0aGlzLnNldHVwVGlueU1DRShjb25maWcpfSk7XG4gIH1cblxuICAvKipcbiAgICogUmVwbGFjZSBpbml0aWFsIFRpbnlNQ0UgaWNvbnMgd2l0aCBtYXRlcmlhbCBpY29uc1xuICAgKi9cbiAgY2hhbmdlVG9NYXRlcmlhbCgpIHtcbiAgICBsZXQgbWF0ZXJpYWxJY29uQXNzb2MgPSB7XG4gICAgICAnbWNlLWktY29kZSc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Y29kZTwvaT4nLFxuICAgICAgJ21jZS1pLW5vbmUnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9jb2xvcl90ZXh0PC9pPicsXG4gICAgICAnbWNlLWktYm9sZCc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X2JvbGQ8L2k+JyxcbiAgICAgICdtY2UtaS1pdGFsaWMnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9pdGFsaWM8L2k+JyxcbiAgICAgICdtY2UtaS11bmRlcmxpbmUnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF91bmRlcmxpbmVkPC9pPicsXG4gICAgICAnbWNlLWktc3RyaWtldGhyb3VnaCc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X3N0cmlrZXRocm91Z2g8L2k+JyxcbiAgICAgICdtY2UtaS1ibG9ja3F1b3RlJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfcXVvdGU8L2k+JyxcbiAgICAgICdtY2UtaS1saW5rJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5saW5rPC9pPicsXG4gICAgICAnbWNlLWktYWxpZ25sZWZ0JzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfYWxpZ25fbGVmdDwvaT4nLFxuICAgICAgJ21jZS1pLWFsaWduY2VudGVyJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfYWxpZ25fY2VudGVyPC9pPicsXG4gICAgICAnbWNlLWktYWxpZ25yaWdodCc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X2FsaWduX3JpZ2h0PC9pPicsXG4gICAgICAnbWNlLWktYWxpZ25qdXN0aWZ5JzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfYWxpZ25fanVzdGlmeTwvaT4nLFxuICAgICAgJ21jZS1pLWJ1bGxpc3QnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9saXN0X2J1bGxldGVkPC9pPicsXG4gICAgICAnbWNlLWktbnVtbGlzdCc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X2xpc3RfbnVtYmVyZWQ8L2k+JyxcbiAgICAgICdtY2UtaS1pbWFnZSc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+aW1hZ2U8L2k+JyxcbiAgICAgICdtY2UtaS10YWJsZSc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Z3JpZF9vbjwvaT4nLFxuICAgICAgJ21jZS1pLW1lZGlhJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj52aWRlb19saWJyYXJ5PC9pPicsXG4gICAgICAnbWNlLWktYnJvd3NlJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5hdHRhY2htZW50PC9pPicsXG4gICAgICAnbWNlLWktY2hlY2tib3gnOiAnPGkgY2xhc3M9XCJtY2UtaWNvIG1jZS1pLWNoZWNrYm94XCI+PC9pPicsXG4gICAgfTtcblxuICAgICQuZWFjaChtYXRlcmlhbEljb25Bc3NvYywgZnVuY3Rpb24gKGluZGV4LCB2YWx1ZSkge1xuICAgICAgJChgLiR7aW5kZXh9YCkucmVwbGFjZVdpdGgodmFsdWUpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFVwZGF0ZXMgdGhlIGNoYXJhY3RlcnMgY291bnRlclxuICAgKlxuICAgKiBAcGFyYW0gaWRcbiAgICovXG4gIGhhbmRsZUNvdW50ZXJUaW55KGlkKSB7XG4gICAgY29uc3QgdGV4dGFyZWEgPSAkKGAjJHtpZH1gKTtcbiAgICBjb25zdCBjb3VudGVyID0gdGV4dGFyZWEuYXR0cignY291bnRlcicpO1xuICAgIGNvbnN0IGNvdW50ZXJUeXBlID0gdGV4dGFyZWEuYXR0cignY291bnRlcl90eXBlJyk7XG4gICAgY29uc3QgbWF4ID0gdGlueU1DRS5hY3RpdmVFZGl0b3IuZ2V0Qm9keSgpLnRleHRDb250ZW50Lmxlbmd0aDtcblxuICAgIHRleHRhcmVhLnBhcmVudCgpLmZpbmQoJ3NwYW4uY3VycmVudExlbmd0aCcpLnRleHQobWF4KTtcbiAgICBpZiAoJ3JlY29tbWVuZGVkJyAhPT0gY291bnRlclR5cGUgJiYgbWF4ID4gY291bnRlcikge1xuICAgICAgdGV4dGFyZWEucGFyZW50KCkuZmluZCgnc3Bhbi5tYXhMZW5ndGgnKS5hZGRDbGFzcygndGV4dC1kYW5nZXInKTtcbiAgICB9IGVsc2Uge1xuICAgICAgdGV4dGFyZWEucGFyZW50KCkuZmluZCgnc3Bhbi5tYXhMZW5ndGgnKS5yZW1vdmVDbGFzcygndGV4dC1kYW5nZXInKTtcbiAgICB9XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgVGlueU1DRUVkaXRvcjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvdGlueW1jZS1lZGl0b3IuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgVGlueU1DRUVkaXRvciBmcm9tICcuLi8uLi9jb21wb25lbnRzL3RpbnltY2UtZWRpdG9yJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgbmV3IFRpbnlNQ0VFZGl0b3IoKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvbWFpbnRlbmFuY2UvaW5kZXguanMiXSwic291cmNlUm9vdCI6IiJ9