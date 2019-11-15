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
/******/ 	return __webpack_require__(__webpack_require__.s = 360);
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

/***/ 360:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _tinymceEditor = __webpack_require__(32);

var _tinymceEditor2 = _interopRequireDefault(_tinymceEditor);

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
  new _tinymceEditor2.default();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjA/ODU5MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy90aW55bWNlLWVkaXRvci5qcz81MjZiKioiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvbWFpbnRlbmFuY2UvaW5kZXguanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIlRpbnlNQ0VFZGl0b3IiLCJvcHRpb25zIiwidGlueU1DRUxvYWRlZCIsImJhc2VBZG1pblVybCIsImJhc2VBZG1pbkRpciIsInBhdGhQYXJ0cyIsImxvY2F0aW9uIiwicGF0aG5hbWUiLCJzcGxpdCIsImV2ZXJ5IiwicGF0aFBhcnQiLCJsYW5nSXNSdGwiLCJsYW5nX2lzX3J0bCIsInNldHVwVGlueU1DRSIsImNvbmZpZyIsInRpbnlNQ0UiLCJsb2FkQW5kSW5pdFRpbnlNQ0UiLCJpbml0VGlueU1DRSIsIk9iamVjdCIsImFzc2lnbiIsInNlbGVjdG9yIiwicGx1Z2lucyIsImJyb3dzZXJfc3BlbGxjaGVjayIsInRvb2xiYXIxIiwidG9vbGJhcjIiLCJleHRlcm5hbF9maWxlbWFuYWdlcl9wYXRoIiwiZmlsZW1hbmFnZXJfdGl0bGUiLCJleHRlcm5hbF9wbHVnaW5zIiwibGFuZ3VhZ2UiLCJpc29fdXNlciIsImNvbnRlbnRfc3R5bGUiLCJza2luIiwibWVudWJhciIsInN0YXR1c2JhciIsInJlbGF0aXZlX3VybHMiLCJjb252ZXJ0X3VybHMiLCJlbnRpdHlfZW5jb2RpbmciLCJleHRlbmRlZF92YWxpZF9lbGVtZW50cyIsInZhbGlkX2NoaWxkcmVuIiwidmFsaWRfZWxlbWVudHMiLCJyZWxfbGlzdCIsInRpdGxlIiwidmFsdWUiLCJlZGl0b3Jfc2VsZWN0b3IiLCJpbml0X2luc3RhbmNlX2NhbGxiYWNrIiwiY2hhbmdlVG9NYXRlcmlhbCIsInNldHVwIiwiZWRpdG9yIiwic2V0dXBFZGl0b3IiLCJvbiIsImluaXQiLCJ3YXRjaFRhYkNoYW5nZXMiLCJldmVudCIsImhhbmRsZUNvdW50ZXJUaW55IiwidGFyZ2V0IiwiaWQiLCJ0cmlnZ2VyU2F2ZSIsImVhY2giLCJpbmRleCIsInRleHRhcmVhIiwidHJhbnNsYXRlZEZpZWxkIiwiY2xvc2VzdCIsInRhYkNvbnRhaW5lciIsImxlbmd0aCIsInRleHRhcmVhTG9jYWxlIiwiZGF0YSIsInRleHRhcmVhTGlua1NlbGVjdG9yIiwiZ2V0Iiwic2V0Q29udGVudCIsImdldENvbnRlbnQiLCJwYXRoQXJyYXkiLCJzcGxpY2UiLCJmaW5hbFBhdGgiLCJqb2luIiwidGlueU1DRVByZUluaXQiLCJiYXNlIiwic3VmZml4IiwiZ2V0U2NyaXB0IiwibWF0ZXJpYWxJY29uQXNzb2MiLCJyZXBsYWNlV2l0aCIsImNvdW50ZXIiLCJhdHRyIiwiY291bnRlclR5cGUiLCJtYXgiLCJhY3RpdmVFZGl0b3IiLCJnZXRCb2R5IiwidGV4dENvbnRlbnQiLCJwYXJlbnQiLCJmaW5kIiwidGV4dCIsImFkZENsYXNzIiwicmVtb3ZlQ2xhc3MiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNoRUE7Ozs7Ozs7Ozs7Ozs7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ImZpbGUiOiJtYWludGVuYW5jZS5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDM2MCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjAiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogVGhpcyBjbGFzcyBpbml0IFRpbnlNQ0UgaW5zdGFuY2VzIGluIHRoZSBiYWNrLW9mZmljZS4gSXQgaXMgd2lsZGx5IGluc3BpcmVkIGJ5XG4gKiB0aGUgc2NyaXB0cyBmcm9tIGpzL2FkbWluIEFuZCBpdCBhY3R1YWxseSBsb2FkcyBUaW55TUNFIGZyb20gdGhlIGpzL3RpbnlfbWNlXG4gKiBmb2xkZXIgYWxvbmcgd2l0aCBpdHMgbW9kdWxlcy4gT25lIGltcHJvdmVtZW50IGNvdWxkIGJlIHRvIGluc3RhbGwgVGlueU1DRSB2aWFcbiAqIG5wbSBhbmQgZnVsbHkgaW50ZWdyYXRlIGluIHRoZSBiYWNrLW9mZmljZSB0aGVtZS5cbiAqL1xuY2xhc3MgVGlueU1DRUVkaXRvciB7XG4gIGNvbnN0cnVjdG9yKG9wdGlvbnMpIHtcbiAgICBvcHRpb25zID0gb3B0aW9ucyB8fCB7fTtcbiAgICB0aGlzLnRpbnlNQ0VMb2FkZWQgPSBmYWxzZTtcbiAgICBpZiAodHlwZW9mIG9wdGlvbnMuYmFzZUFkbWluVXJsID09ICd1bmRlZmluZWQnKSB7XG4gICAgICBpZiAodHlwZW9mIHdpbmRvdy5iYXNlQWRtaW5EaXIgIT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgb3B0aW9ucy5iYXNlQWRtaW5VcmwgPSB3aW5kb3cuYmFzZUFkbWluRGlyO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgY29uc3QgcGF0aFBhcnRzID0gd2luZG93LmxvY2F0aW9uLnBhdGhuYW1lLnNwbGl0KCcvJyk7XG4gICAgICAgIHBhdGhQYXJ0cy5ldmVyeShmdW5jdGlvbihwYXRoUGFydCkge1xuICAgICAgICAgIGlmIChwYXRoUGFydCAhPT0gJycpIHtcbiAgICAgICAgICAgIG9wdGlvbnMuYmFzZUFkbWluVXJsID0gYC8ke3BhdGhQYXJ0fS9gO1xuXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH1cbiAgICBpZiAodHlwZW9mIG9wdGlvbnMubGFuZ0lzUnRsID09ICd1bmRlZmluZWQnKSB7XG4gICAgICBvcHRpb25zLmxhbmdJc1J0bCA9IHR5cGVvZiB3aW5kb3cubGFuZ19pc19ydGwgIT0gJ3VuZGVmaW5lZCcgPyB3aW5kb3cubGFuZ19pc19ydGwgPT09ICcxJyA6IGZhbHNlO1xuICAgIH1cbiAgICB0aGlzLnNldHVwVGlueU1DRShvcHRpb25zKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0aWFsIHNldHVwIHdoaWNoIGNoZWNrcyBpZiB0aGUgdGlueU1DRSBsaWJyYXJ5IGlzIGFscmVhZHkgbG9hZGVkLlxuICAgKlxuICAgKiBAcGFyYW0gY29uZmlnXG4gICAqL1xuICBzZXR1cFRpbnlNQ0UoY29uZmlnKSB7XG4gICAgaWYgKHR5cGVvZiB0aW55TUNFID09PSAndW5kZWZpbmVkJykge1xuICAgICAgdGhpcy5sb2FkQW5kSW5pdFRpbnlNQ0UoY29uZmlnKTtcbiAgICB9IGVsc2Uge1xuICAgICAgdGhpcy5pbml0VGlueU1DRShjb25maWcpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBQcmVwYXJlIHRoZSBjb25maWcgYW5kIGluaXQgYWxsIFRpbnlNQ0UgZWRpdG9yc1xuICAgKlxuICAgKiBAcGFyYW0gY29uZmlnXG4gICAqL1xuICBpbml0VGlueU1DRShjb25maWcpIHtcbiAgICBjb25maWcgPSBPYmplY3QuYXNzaWduKHtcbiAgICAgIHNlbGVjdG9yOiAnLnJ0ZScsXG4gICAgICBwbHVnaW5zOiAnYWxpZ24gY29sb3JwaWNrZXIgbGluayBpbWFnZSBmaWxlbWFuYWdlciB0YWJsZSBtZWRpYSBwbGFjZWhvbGRlciBhZHZsaXN0IGNvZGUgdGFibGUgYXV0b3Jlc2l6ZScsXG4gICAgICBicm93c2VyX3NwZWxsY2hlY2s6IHRydWUsXG4gICAgICB0b29sYmFyMTogJ2NvZGUsY29sb3JwaWNrZXIsYm9sZCxpdGFsaWMsdW5kZXJsaW5lLHN0cmlrZXRocm91Z2gsYmxvY2txdW90ZSxsaW5rLGFsaWduLGJ1bGxpc3QsbnVtbGlzdCx0YWJsZSxpbWFnZSxtZWRpYSxmb3JtYXRzZWxlY3QnLFxuICAgICAgdG9vbGJhcjI6ICcnLFxuICAgICAgZXh0ZXJuYWxfZmlsZW1hbmFnZXJfcGF0aDogY29uZmlnLmJhc2VBZG1pblVybCArICdmaWxlbWFuYWdlci8nLFxuICAgICAgZmlsZW1hbmFnZXJfdGl0bGU6ICdGaWxlIG1hbmFnZXInLFxuICAgICAgZXh0ZXJuYWxfcGx1Z2luczoge1xuICAgICAgICAnZmlsZW1hbmFnZXInOiBjb25maWcuYmFzZUFkbWluVXJsICsgJ2ZpbGVtYW5hZ2VyL3BsdWdpbi5taW4uanMnXG4gICAgICB9LFxuICAgICAgbGFuZ3VhZ2U6IGlzb191c2VyLFxuICAgICAgY29udGVudF9zdHlsZSA6IChjb25maWcubGFuZ0lzUnRsID8gJ2JvZHkge2RpcmVjdGlvbjpydGw7fScgOiAnJyksXG4gICAgICBza2luOiAncHJlc3Rhc2hvcCcsXG4gICAgICBtZW51YmFyOiBmYWxzZSxcbiAgICAgIHN0YXR1c2JhcjogZmFsc2UsXG4gICAgICByZWxhdGl2ZV91cmxzOiBmYWxzZSxcbiAgICAgIGNvbnZlcnRfdXJsczogZmFsc2UsXG4gICAgICBlbnRpdHlfZW5jb2Rpbmc6ICdyYXcnLFxuICAgICAgZXh0ZW5kZWRfdmFsaWRfZWxlbWVudHM6ICdlbVtjbGFzc3xuYW1lfGlkXSxAW3JvbGV8ZGF0YS0qfGFyaWEtKl0nLFxuICAgICAgdmFsaWRfY2hpbGRyZW46ICcrKlsqXScsXG4gICAgICB2YWxpZF9lbGVtZW50czogJypbKl0nLFxuICAgICAgcmVsX2xpc3Q6W1xuICAgICAgICB7IHRpdGxlOiAnbm9mb2xsb3cnLCB2YWx1ZTogJ25vZm9sbG93JyB9XG4gICAgICBdLFxuICAgICAgZWRpdG9yX3NlbGVjdG9yIDonYXV0b2xvYWRfcnRlJyxcbiAgICAgIGluaXRfaW5zdGFuY2VfY2FsbGJhY2s6ICgpID0+IHsgdGhpcy5jaGFuZ2VUb01hdGVyaWFsKCk7IH0sXG4gICAgICBzZXR1cCA6IChlZGl0b3IpID0+IHsgdGhpcy5zZXR1cEVkaXRvcihlZGl0b3IpOyB9LFxuICAgIH0sIGNvbmZpZyk7XG5cbiAgICBpZiAodHlwZW9mIGNvbmZpZy5lZGl0b3Jfc2VsZWN0b3IgIT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgIGNvbmZpZy5zZWxlY3RvciA9ICcuJyArIGNvbmZpZy5lZGl0b3Jfc2VsZWN0b3I7XG4gICAgfVxuXG4gICAgLy8gQ2hhbmdlIGljb25zIGluIHBvcHVwc1xuICAgICQoJ2JvZHknKS5vbignY2xpY2snLCAnLm1jZS1idG4sIC5tY2Utb3BlbiwgLm1jZS1tZW51LWl0ZW0nLCAoKSA9PiB7IHRoaXMuY2hhbmdlVG9NYXRlcmlhbCgpOyB9KTtcblxuICAgIHRpbnlNQ0UuaW5pdChjb25maWcpO1xuICAgIHRoaXMud2F0Y2hUYWJDaGFuZ2VzKGNvbmZpZyk7XG4gIH1cblxuICAvKipcbiAgICogU2V0dXAgVGlueU1DRSBlZGl0b3Igb25jZSBpdCBoYXMgYmVlbiBpbml0aWFsaXplZFxuICAgKlxuICAgKiBAcGFyYW0gZWRpdG9yXG4gICAqL1xuICBzZXR1cEVkaXRvcihlZGl0b3IpIHtcbiAgICBlZGl0b3Iub24oJ2xvYWRDb250ZW50JywgKGV2ZW50KSA9PiB7XG4gICAgICB0aGlzLmhhbmRsZUNvdW50ZXJUaW55KGV2ZW50LnRhcmdldC5pZCk7XG4gICAgfSk7XG4gICAgZWRpdG9yLm9uKCdjaGFuZ2UnLCAoZXZlbnQpID0+IHtcbiAgICAgIHRpbnlNQ0UudHJpZ2dlclNhdmUoKTtcbiAgICAgIHRoaXMuaGFuZGxlQ291bnRlclRpbnkoZXZlbnQudGFyZ2V0LmlkKTtcbiAgICB9KTtcbiAgICBlZGl0b3Iub24oJ2JsdXInLCAoKSA9PiB7XG4gICAgICB0aW55TUNFLnRyaWdnZXJTYXZlKCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogV2hlbiB0aGUgZWRpdG9yIGlzIGluc2lkZSBhIHRhYiBpdCBjYW4gY2F1c2UgYSBidWcgb24gdGFiIHN3aXRjaGluZy5cbiAgICogU28gd2UgY2hlY2sgaWYgdGhlIGVkaXRvciBpcyBjb250YWluZWQgaW4gYSBuYXZpZ2F0aW9uIGFuZCByZWZyZXNoIHRoZSBlZGl0b3Igd2hlbiBpdHNcbiAgICogcGFyZW50IHRhYiBpcyBzaG93bi5cbiAgICpcbiAgICogQHBhcmFtIGNvbmZpZ1xuICAgKi9cbiAgd2F0Y2hUYWJDaGFuZ2VzKGNvbmZpZykge1xuICAgICQoY29uZmlnLnNlbGVjdG9yKS5lYWNoKChpbmRleCwgdGV4dGFyZWEpID0+IHtcbiAgICAgIGNvbnN0IHRyYW5zbGF0ZWRGaWVsZCA9ICQodGV4dGFyZWEpLmNsb3Nlc3QoJy50cmFuc2xhdGlvbi1maWVsZCcpO1xuICAgICAgY29uc3QgdGFiQ29udGFpbmVyID0gJCh0ZXh0YXJlYSkuY2xvc2VzdCgnLnRyYW5zbGF0aW9ucy50YWJiYWJsZScpO1xuXG4gICAgICBpZiAodHJhbnNsYXRlZEZpZWxkLmxlbmd0aCAmJiB0YWJDb250YWluZXIubGVuZ3RoKSB7XG4gICAgICAgIGNvbnN0IHRleHRhcmVhTG9jYWxlID0gdHJhbnNsYXRlZEZpZWxkLmRhdGEoJ2xvY2FsZScpO1xuICAgICAgICBjb25zdCB0ZXh0YXJlYUxpbmtTZWxlY3RvciA9ICcubmF2LWl0ZW0gYVtkYXRhLWxvY2FsZT1cIicrdGV4dGFyZWFMb2NhbGUrJ1wiXSc7XG5cbiAgICAgICAgJCh0ZXh0YXJlYUxpbmtTZWxlY3RvciwgdGFiQ29udGFpbmVyKS5vbignc2hvd24uYnMudGFiJywgKCkgPT4ge1xuICAgICAgICAgIGNvbnN0IGVkaXRvciA9IHRpbnlNQ0UuZ2V0KHRleHRhcmVhLmlkKTtcbiAgICAgICAgICBpZiAoZWRpdG9yKSB7XG4gICAgICAgICAgICAvL1Jlc2V0IGNvbnRlbnQgdG8gZm9yY2UgcmVmcmVzaCBvZiBlZGl0b3JcbiAgICAgICAgICAgIGVkaXRvci5zZXRDb250ZW50KGVkaXRvci5nZXRDb250ZW50KCkpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTG9hZHMgdGhlIFRpbnlNQ0UgamF2YXNjcmlwdCBsaWJyYXJ5IGFuZCB0aGVuIGluaXQgdGhlIGVkaXRvcnNcbiAgICpcbiAgICogQHBhcmFtIGNvbmZpZ1xuICAgKi9cbiAgbG9hZEFuZEluaXRUaW55TUNFKGNvbmZpZykge1xuICAgIGlmICh0aGlzLnRpbnlNQ0VMb2FkZWQpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB0aGlzLnRpbnlNQ0VMb2FkZWQgPSB0cnVlO1xuICAgIGNvbnN0IHBhdGhBcnJheSA9IGNvbmZpZy5iYXNlQWRtaW5Vcmwuc3BsaXQoJy8nKTtcbiAgICBwYXRoQXJyYXkuc3BsaWNlKChwYXRoQXJyYXkubGVuZ3RoIC0gMiksIDIpO1xuICAgIGNvbnN0IGZpbmFsUGF0aCA9IHBhdGhBcnJheS5qb2luKCcvJyk7XG4gICAgd2luZG93LnRpbnlNQ0VQcmVJbml0ID0ge307XG4gICAgd2luZG93LnRpbnlNQ0VQcmVJbml0LmJhc2UgPSBmaW5hbFBhdGgrJy9qcy90aW55X21jZSc7XG4gICAgd2luZG93LnRpbnlNQ0VQcmVJbml0LnN1ZmZpeCA9ICcubWluJztcbiAgICAkLmdldFNjcmlwdChgJHtmaW5hbFBhdGh9L2pzL3RpbnlfbWNlL3RpbnltY2UubWluLmpzYCwgKCkgPT4ge3RoaXMuc2V0dXBUaW55TUNFKGNvbmZpZyl9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXBsYWNlIGluaXRpYWwgVGlueU1DRSBpY29ucyB3aXRoIG1hdGVyaWFsIGljb25zXG4gICAqL1xuICBjaGFuZ2VUb01hdGVyaWFsKCkge1xuICAgIGxldCBtYXRlcmlhbEljb25Bc3NvYyA9IHtcbiAgICAgICdtY2UtaS1jb2RlJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5jb2RlPC9pPicsXG4gICAgICAnbWNlLWktbm9uZSc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X2NvbG9yX3RleHQ8L2k+JyxcbiAgICAgICdtY2UtaS1ib2xkJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfYm9sZDwvaT4nLFxuICAgICAgJ21jZS1pLWl0YWxpYyc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X2l0YWxpYzwvaT4nLFxuICAgICAgJ21jZS1pLXVuZGVybGluZSc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X3VuZGVybGluZWQ8L2k+JyxcbiAgICAgICdtY2UtaS1zdHJpa2V0aHJvdWdoJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfc3RyaWtldGhyb3VnaDwvaT4nLFxuICAgICAgJ21jZS1pLWJsb2NrcXVvdGUnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9xdW90ZTwvaT4nLFxuICAgICAgJ21jZS1pLWxpbmsnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmxpbms8L2k+JyxcbiAgICAgICdtY2UtaS1hbGlnbmxlZnQnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9hbGlnbl9sZWZ0PC9pPicsXG4gICAgICAnbWNlLWktYWxpZ25jZW50ZXInOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9hbGlnbl9jZW50ZXI8L2k+JyxcbiAgICAgICdtY2UtaS1hbGlnbnJpZ2h0JzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfYWxpZ25fcmlnaHQ8L2k+JyxcbiAgICAgICdtY2UtaS1hbGlnbmp1c3RpZnknOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9hbGlnbl9qdXN0aWZ5PC9pPicsXG4gICAgICAnbWNlLWktYnVsbGlzdCc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X2xpc3RfYnVsbGV0ZWQ8L2k+JyxcbiAgICAgICdtY2UtaS1udW1saXN0JzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfbGlzdF9udW1iZXJlZDwvaT4nLFxuICAgICAgJ21jZS1pLWltYWdlJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5pbWFnZTwvaT4nLFxuICAgICAgJ21jZS1pLXRhYmxlJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5ncmlkX29uPC9pPicsXG4gICAgICAnbWNlLWktbWVkaWEnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPnZpZGVvX2xpYnJhcnk8L2k+JyxcbiAgICAgICdtY2UtaS1icm93c2UnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmF0dGFjaG1lbnQ8L2k+JyxcbiAgICAgICdtY2UtaS1jaGVja2JveCc6ICc8aSBjbGFzcz1cIm1jZS1pY28gbWNlLWktY2hlY2tib3hcIj48L2k+JyxcbiAgICB9O1xuXG4gICAgJC5lYWNoKG1hdGVyaWFsSWNvbkFzc29jLCBmdW5jdGlvbiAoaW5kZXgsIHZhbHVlKSB7XG4gICAgICAkKGAuJHtpbmRleH1gKS5yZXBsYWNlV2l0aCh2YWx1ZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogVXBkYXRlcyB0aGUgY2hhcmFjdGVycyBjb3VudGVyXG4gICAqXG4gICAqIEBwYXJhbSBpZFxuICAgKi9cbiAgaGFuZGxlQ291bnRlclRpbnkoaWQpIHtcbiAgICBjb25zdCB0ZXh0YXJlYSA9ICQoYCMke2lkfWApO1xuICAgIGNvbnN0IGNvdW50ZXIgPSB0ZXh0YXJlYS5hdHRyKCdjb3VudGVyJyk7XG4gICAgY29uc3QgY291bnRlclR5cGUgPSB0ZXh0YXJlYS5hdHRyKCdjb3VudGVyX3R5cGUnKTtcbiAgICBjb25zdCBtYXggPSB0aW55TUNFLmFjdGl2ZUVkaXRvci5nZXRCb2R5KCkudGV4dENvbnRlbnQubGVuZ3RoO1xuXG4gICAgdGV4dGFyZWEucGFyZW50KCkuZmluZCgnc3Bhbi5jdXJyZW50TGVuZ3RoJykudGV4dChtYXgpO1xuICAgIGlmICgncmVjb21tZW5kZWQnICE9PSBjb3VudGVyVHlwZSAmJiBtYXggPiBjb3VudGVyKSB7XG4gICAgICB0ZXh0YXJlYS5wYXJlbnQoKS5maW5kKCdzcGFuLm1heExlbmd0aCcpLmFkZENsYXNzKCd0ZXh0LWRhbmdlcicpO1xuICAgIH0gZWxzZSB7XG4gICAgICB0ZXh0YXJlYS5wYXJlbnQoKS5maW5kKCdzcGFuLm1heExlbmd0aCcpLnJlbW92ZUNsYXNzKCd0ZXh0LWRhbmdlcicpO1xuICAgIH1cbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBUaW55TUNFRWRpdG9yO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy90aW55bWNlLWVkaXRvci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBUaW55TUNFRWRpdG9yIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvdGlueW1jZS1lZGl0b3InO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBuZXcgVGlueU1DRUVkaXRvcigpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9tYWludGVuYW5jZS9pbmRleC5qcyJdLCJzb3VyY2VSb290IjoiIn0=