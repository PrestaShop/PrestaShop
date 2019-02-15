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
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/app/pages/module-card/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/app/pages/module-card/index.js":
/*!*******************************************!*\
  !*** ./js/app/pages/module-card/index.js ***!
  \*******************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var _components_module_card__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../components/module-card */ "./js/components/module-card.js");
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

var $ = global.$;
$(function () {
  new _components_module_card__WEBPACK_IMPORTED_MODULE_0__["default"]().init();
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./js/components/module-card.js":
/*!**************************************!*\
  !*** ./js/components/module-card.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(jQuery) {/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ModuleCard; });
function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

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
var BOEvent = {
  on: function on(eventName, callback, context) {
    document.addEventListener(eventName, function (event) {
      if (typeof context !== 'undefined') {
        callback.call(context, event);
      } else {
        callback(event);
      }
    });
  },
  emitEvent: function emitEvent(eventName, eventType) {
    var _event = document.createEvent(eventType); // true values stand for: can bubble, and is cancellable


    _event.initEvent(eventName, true, true);

    document.dispatchEvent(_event);
  }
};
/**
 * Class is responsible for handling Module Card behavior
 *
 * This is a port of admin-dev/themes/default/js/bundle/module/module_card.js
 */

var ModuleCard =
/*#__PURE__*/
function () {
  function ModuleCard() {
    _classCallCheck(this, ModuleCard);

    /* Selectors for module action links (uninstall, reset, etc...) to add a confirm popin */
    this.moduleActionMenuLinkSelector = 'button.module_action_menu_';
    this.moduleActionMenuInstallLinkSelector = 'button.module_action_menu_install';
    this.moduleActionMenuEnableLinkSelector = 'button.module_action_menu_enable';
    this.moduleActionMenuUninstallLinkSelector = 'button.module_action_menu_uninstall';
    this.moduleActionMenuDisableLinkSelector = 'button.module_action_menu_disable';
    this.moduleActionMenuEnableMobileLinkSelector = 'button.module_action_menu_enable_mobile';
    this.moduleActionMenuDisableMobileLinkSelector = 'button.module_action_menu_disable_mobile';
    this.moduleActionMenuResetLinkSelector = 'button.module_action_menu_reset';
    this.moduleActionMenuUpdateLinkSelector = 'button.module_action_menu_upgrade';
    this.moduleItemListSelector = '.module-item-list';
    this.moduleItemGridSelector = '.module-item-grid';
    this.moduleItemActionsSelector = '.module-actions';
    /* Selectors only for modal buttons */

    this.moduleActionModalDisableLinkSelector = 'a.module_action_modal_disable';
    this.moduleActionModalResetLinkSelector = 'a.module_action_modal_reset';
    this.moduleActionModalUninstallLinkSelector = 'a.module_action_modal_uninstall';
    this.forceDeletionOption = '#force_deletion';
    this.initActionButtons();
  }

  _createClass(ModuleCard, [{
    key: "initActionButtons",
    value: function initActionButtons() {
      var self = this;
      $(document).on('click', this.forceDeletionOption, function () {
        var btn = $(self.moduleActionModalUninstallLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']"));

        if ($(this).prop('checked') === true) {
          btn.attr('data-deletion', 'true');
        } else {
          btn.removeAttr('data-deletion');
        }
      });
      $(document).on('click', this.moduleActionMenuInstallLinkSelector, function () {
        if ($("#modal-prestatrust").length) {
          $("#modal-prestatrust").modal('hide');
        }

        return self._dispatchPreEvent('install', this) && self._confirmAction('install', this) && self._requestToController('install', $(this));
      });
      $(document).on('click', this.moduleActionMenuEnableLinkSelector, function () {
        return self._dispatchPreEvent('enable', this) && self._confirmAction('enable', this) && self._requestToController('enable', $(this));
      });
      $(document).on('click', this.moduleActionMenuUninstallLinkSelector, function () {
        return self._dispatchPreEvent('uninstall', this) && self._confirmAction('uninstall', this) && self._requestToController('uninstall', $(this));
      });
      $(document).on('click', this.moduleActionMenuDisableLinkSelector, function () {
        return self._dispatchPreEvent('disable', this) && self._confirmAction('disable', this) && self._requestToController('disable', $(this));
      });
      $(document).on('click', this.moduleActionMenuEnableMobileLinkSelector, function () {
        return self._dispatchPreEvent('enable_mobile', this) && self._confirmAction('enable_mobile', this) && self._requestToController('enable_mobile', $(this));
      });
      $(document).on('click', this.moduleActionMenuDisableMobileLinkSelector, function () {
        return self._dispatchPreEvent('disable_mobile', this) && self._confirmAction('disable_mobile', this) && self._requestToController('disable_mobile', $(this));
      });
      $(document).on('click', this.moduleActionMenuResetLinkSelector, function () {
        return self._dispatchPreEvent('reset', this) && self._confirmAction('reset', this) && self._requestToController('reset', $(this));
      });
      $(document).on('click', this.moduleActionMenuUpdateLinkSelector, function () {
        return self._dispatchPreEvent('update', this) && self._confirmAction('update', this) && self._requestToController('update', $(this));
      });
      $(document).on('click', this.moduleActionModalDisableLinkSelector, function () {
        return self._requestToController('disable', $(self.moduleActionMenuDisableLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']")));
      });
      $(document).on('click', this.moduleActionModalResetLinkSelector, function () {
        return self._requestToController('reset', $(self.moduleActionMenuResetLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']")));
      });
      $(document).on('click', this.moduleActionModalUninstallLinkSelector, function (e) {
        $(e.target).parents('.modal').on('hidden.bs.modal', function (event) {
          return self._requestToController('uninstall', $(self.moduleActionMenuUninstallLinkSelector, $("div.module-item-list[data-tech-name='" + $(e.target).attr("data-tech-name") + "']")), $(e.target).attr("data-deletion"));
        }.bind(e));
      });
    }
  }, {
    key: "_getModuleItemSelector",
    value: function _getModuleItemSelector() {
      if ($(this.moduleItemListSelector).length) {
        return this.moduleItemListSelector;
      } else {
        return this.moduleItemGridSelector;
      }
    }
  }, {
    key: "_confirmAction",
    value: function _confirmAction(action, element) {
      var modal = $('#' + $(element).data('confirm_modal'));

      if (modal.length != 1) {
        return true;
      }

      modal.first().modal('show');
      return false; // do not allow a.href to reload the page. The confirm modal dialog will do it async if needed.
    }
  }, {
    key: "_confirmPrestaTrust",

    /**
     * Update the content of a modal asking a confirmation for PrestaTrust and open it
     *
     * @param {array} result containing module data
     * @return {void}
     */
    value: function _confirmPrestaTrust(result) {
      var that = this;

      var modal = this._replacePrestaTrustPlaceholders(result);

      modal.find(".pstrust-install").off('click').on('click', function () {
        // Find related form, update it and submit it
        var install_button = $(that.moduleActionMenuInstallLinkSelector, '.module-item[data-tech-name="' + result.module.attributes.name + '"]');
        var form = install_button.parent("form");
        $('<input>').attr({
          type: 'hidden',
          value: '1',
          name: 'actionParams[confirmPrestaTrust]'
        }).appendTo(form);
        install_button.click();
        modal.modal('hide');
      });
      modal.modal();
    }
  }, {
    key: "_replacePrestaTrustPlaceholders",
    value: function _replacePrestaTrustPlaceholders(result) {
      var modal = $("#modal-prestatrust");
      var module = result.module.attributes;

      if (result.confirmation_subject !== 'PrestaTrust' || !modal.length) {
        return;
      }

      var alertClass = module.prestatrust.status ? 'success' : 'warning';

      if (module.prestatrust.check_list.property) {
        modal.find("#pstrust-btn-property-ok").show();
        modal.find("#pstrust-btn-property-nok").hide();
      } else {
        modal.find("#pstrust-btn-property-ok").hide();
        modal.find("#pstrust-btn-property-nok").show();
        modal.find("#pstrust-buy").attr("href", module.url).toggle(module.url !== null);
      }

      modal.find("#pstrust-img").attr({
        src: module.img,
        alt: module.name
      });
      modal.find("#pstrust-name").text(module.displayName);
      modal.find("#pstrust-author").text(module.author);
      modal.find("#pstrust-label").attr("class", "text-" + alertClass).text(module.prestatrust.status ? 'OK' : 'KO');
      modal.find("#pstrust-message").attr("class", "alert alert-" + alertClass);
      modal.find("#pstrust-message > p").text(module.prestatrust.message);
      return modal;
    }
  }, {
    key: "_dispatchPreEvent",
    value: function _dispatchPreEvent(action, element) {
      var event = jQuery.Event('module_card_action_event');
      $(element).trigger(event, [action]);

      if (event.isPropagationStopped() !== false || event.isImmediatePropagationStopped() !== false) {
        return false; // if all handlers have not been called, then stop propagation of the click event.
      }

      return event.result !== false; // explicit false must be set from handlers to stop propagation of the click event.
    }
  }, {
    key: "_requestToController",
    value: function _requestToController(action, element, forceDeletion, disableCacheClear, callback) {
      var self = this;
      var jqElementObj = element.closest(this.moduleItemActionsSelector);
      var form = element.closest("form");
      var spinnerObj = $("<button class=\"btn-primary-reverse onclick unbind spinner \"></button>");
      var url = "//" + window.location.host + form.attr("action");
      var actionParams = form.serializeArray();

      if (forceDeletion === "true" || forceDeletion === true) {
        actionParams.push({
          name: "actionParams[deletion]",
          value: true
        });
      }

      if (disableCacheClear === "true" || disableCacheClear === true) {
        actionParams.push({
          name: "actionParams[cacheClearEnabled]",
          value: 0
        });
      }

      $.ajax({
        url: url,
        dataType: 'json',
        method: 'POST',
        data: actionParams,
        beforeSend: function beforeSend() {
          jqElementObj.hide();
          jqElementObj.after(spinnerObj);
        }
      }).done(function (result) {
        if (_typeof(result) === undefined) {
          $.growl.error({
            message: "No answer received from server"
          });
        } else {
          var moduleTechName = Object.keys(result)[0];

          if (result[moduleTechName].status === false) {
            if (typeof result[moduleTechName].confirmation_subject !== 'undefined') {
              self._confirmPrestaTrust(result[moduleTechName]);
            }

            $.growl.error({
              message: result[moduleTechName].msg
            });
          } else {
            $.growl.notice({
              message: result[moduleTechName].msg
            });

            var alteredSelector = self._getModuleItemSelector().replace('.', '');

            var mainElement = null;

            if (action == "uninstall") {
              mainElement = jqElementObj.closest('.' + alteredSelector);
              mainElement.remove();
              BOEvent.emitEvent("Module Uninstalled", "CustomEvent");
            } else if (action == "disable") {
              mainElement = jqElementObj.closest('.' + alteredSelector);
              mainElement.addClass(alteredSelector + '-isNotActive');
              mainElement.attr('data-active', '0');
              BOEvent.emitEvent("Module Disabled", "CustomEvent");
            } else if (action == "enable") {
              mainElement = jqElementObj.closest('.' + alteredSelector);
              mainElement.removeClass(alteredSelector + '-isNotActive');
              mainElement.attr('data-active', '1');
              BOEvent.emitEvent("Module Enabled", "CustomEvent");
            }

            jqElementObj.replaceWith(result[moduleTechName].action_menu_html);
          }
        }
      }).fail(function () {
        var moduleItem = jqElementObj.closest('module-item-list');
        var techName = moduleItem.data('techName');
        $.growl.error({
          message: "Could not perform action " + action + " for module " + techName
        });
      }).always(function () {
        jqElementObj.fadeIn();
        spinnerObj.remove();

        if (callback) {
          callback();
        }
      });
      return false;
    }
  }]);

  return ModuleCard;
}();


/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "jquery")))

/***/ }),

/***/ "./node_modules/webpack/buildin/global.js":
/*!***********************************!*\
  !*** (webpack)/buildin/global.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || new Function("return this")();
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL3BhZ2VzL21vZHVsZS1jYXJkL2luZGV4LmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvbW9kdWxlLWNhcmQuanMiLCJ3ZWJwYWNrOi8vLyh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qcyIsIndlYnBhY2s6Ly8vZXh0ZXJuYWwgXCJqUXVlcnlcIiJdLCJuYW1lcyI6WyIkIiwiZ2xvYmFsIiwiTW9kdWxlQ2FyZCIsImluaXQiLCJ3aW5kb3ciLCJCT0V2ZW50Iiwib24iLCJldmVudE5hbWUiLCJjYWxsYmFjayIsImNvbnRleHQiLCJkb2N1bWVudCIsImFkZEV2ZW50TGlzdGVuZXIiLCJldmVudCIsImNhbGwiLCJlbWl0RXZlbnQiLCJldmVudFR5cGUiLCJfZXZlbnQiLCJjcmVhdGVFdmVudCIsImluaXRFdmVudCIsImRpc3BhdGNoRXZlbnQiLCJtb2R1bGVBY3Rpb25NZW51TGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudUluc3RhbGxMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25NZW51RW5hYmxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudVVuaW5zdGFsbExpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudUVuYWJsZU1vYmlsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTW9iaWxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudVJlc2V0TGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudVVwZGF0ZUxpbmtTZWxlY3RvciIsIm1vZHVsZUl0ZW1MaXN0U2VsZWN0b3IiLCJtb2R1bGVJdGVtR3JpZFNlbGVjdG9yIiwibW9kdWxlSXRlbUFjdGlvbnNTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1vZGFsRGlzYWJsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1vZGFsUmVzZXRMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25Nb2RhbFVuaW5zdGFsbExpbmtTZWxlY3RvciIsImZvcmNlRGVsZXRpb25PcHRpb24iLCJpbml0QWN0aW9uQnV0dG9ucyIsInNlbGYiLCJidG4iLCJhdHRyIiwicHJvcCIsInJlbW92ZUF0dHIiLCJsZW5ndGgiLCJtb2RhbCIsIl9kaXNwYXRjaFByZUV2ZW50IiwiX2NvbmZpcm1BY3Rpb24iLCJfcmVxdWVzdFRvQ29udHJvbGxlciIsImUiLCJ0YXJnZXQiLCJwYXJlbnRzIiwiYmluZCIsImFjdGlvbiIsImVsZW1lbnQiLCJkYXRhIiwiZmlyc3QiLCJyZXN1bHQiLCJ0aGF0IiwiX3JlcGxhY2VQcmVzdGFUcnVzdFBsYWNlaG9sZGVycyIsImZpbmQiLCJvZmYiLCJpbnN0YWxsX2J1dHRvbiIsIm1vZHVsZSIsImF0dHJpYnV0ZXMiLCJuYW1lIiwiZm9ybSIsInBhcmVudCIsInR5cGUiLCJ2YWx1ZSIsImFwcGVuZFRvIiwiY2xpY2siLCJjb25maXJtYXRpb25fc3ViamVjdCIsImFsZXJ0Q2xhc3MiLCJwcmVzdGF0cnVzdCIsInN0YXR1cyIsImNoZWNrX2xpc3QiLCJwcm9wZXJ0eSIsInNob3ciLCJoaWRlIiwidXJsIiwidG9nZ2xlIiwic3JjIiwiaW1nIiwiYWx0IiwidGV4dCIsImRpc3BsYXlOYW1lIiwiYXV0aG9yIiwibWVzc2FnZSIsImpRdWVyeSIsIkV2ZW50IiwidHJpZ2dlciIsImlzUHJvcGFnYXRpb25TdG9wcGVkIiwiaXNJbW1lZGlhdGVQcm9wYWdhdGlvblN0b3BwZWQiLCJmb3JjZURlbGV0aW9uIiwiZGlzYWJsZUNhY2hlQ2xlYXIiLCJqcUVsZW1lbnRPYmoiLCJjbG9zZXN0Iiwic3Bpbm5lck9iaiIsImxvY2F0aW9uIiwiaG9zdCIsImFjdGlvblBhcmFtcyIsInNlcmlhbGl6ZUFycmF5IiwicHVzaCIsImFqYXgiLCJkYXRhVHlwZSIsIm1ldGhvZCIsImJlZm9yZVNlbmQiLCJhZnRlciIsImRvbmUiLCJ1bmRlZmluZWQiLCJncm93bCIsImVycm9yIiwibW9kdWxlVGVjaE5hbWUiLCJPYmplY3QiLCJrZXlzIiwiX2NvbmZpcm1QcmVzdGFUcnVzdCIsIm1zZyIsIm5vdGljZSIsImFsdGVyZWRTZWxlY3RvciIsIl9nZXRNb2R1bGVJdGVtU2VsZWN0b3IiLCJyZXBsYWNlIiwibWFpbkVsZW1lbnQiLCJyZW1vdmUiLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIiwicmVwbGFjZVdpdGgiLCJhY3Rpb25fbWVudV9odG1sIiwiZmFpbCIsIm1vZHVsZUl0ZW0iLCJ0ZWNoTmFtZSIsImFsd2F5cyIsImZhZGVJbiJdLCJtYXBwaW5ncyI6IjtBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0Esa0RBQTBDLGdDQUFnQztBQUMxRTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLGdFQUF3RCxrQkFBa0I7QUFDMUU7QUFDQSx5REFBaUQsY0FBYztBQUMvRDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsaURBQXlDLGlDQUFpQztBQUMxRSx3SEFBZ0gsbUJBQW1CLEVBQUU7QUFDckk7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7O0FBR0E7QUFDQTs7Ozs7Ozs7Ozs7OztBQ2xGQTtBQUFBO0FBQUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQTtBQUVBLElBQU1BLENBQUMsR0FBR0MsTUFBTSxDQUFDRCxDQUFqQjtBQUVBQSxDQUFDLENBQUMsWUFBTTtBQUNOLE1BQUlF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LENBQWlCLGFBQWpCLEVBQWdDLEdBQWhDO0FBRUFsQyxxQkFBTyxDQUFDUyxTQUFSLENBQWtCLGdCQUFsQixFQUFvQyxhQUFwQztBQUNEOztBQUVEZ0Ysd0JBQVksQ0FBQzhCLFdBQWIsQ0FBeUJyRSxNQUFNLENBQUN3RCxjQUFELENBQU4sQ0FBdUJjLGdCQUFoRDtBQUNEO0FBQ0Y7QUFDRixPQWpERCxFQWlER0MsSUFqREgsQ0FpRFEsWUFBVztBQUNqQixZQUFNQyxVQUFVLEdBQUdqQyxZQUFZLENBQUNDLE9BQWIsQ0FBcUIsa0JBQXJCLENBQW5CO0FBQ0EsWUFBTWlDLFFBQVEsR0FBR0QsVUFBVSxDQUFDMUUsSUFBWCxDQUFnQixVQUFoQixDQUFqQjtBQUNBckQsU0FBQyxDQUFDNkcsS0FBRixDQUFRQyxLQUFSLENBQWM7QUFBQ3hCLGlCQUFPLEVBQUUsOEJBQTRCbkMsTUFBNUIsR0FBbUMsY0FBbkMsR0FBa0Q2RTtBQUE1RCxTQUFkO0FBQ0QsT0FyREQsRUFxREdDLE1BckRILENBcURVLFlBQVk7QUFDcEJuQyxvQkFBWSxDQUFDb0MsTUFBYjtBQUNBbEMsa0JBQVUsQ0FBQ3lCLE1BQVg7O0FBQ0EsWUFBSWpILFFBQUosRUFBYztBQUNaQSxrQkFBUTtBQUNUO0FBQ0YsT0EzREQ7QUE2REEsYUFBTyxLQUFQO0FBQ0Q7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQzdTSDs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxDQUFDOztBQUVEO0FBQ0E7QUFDQTtBQUNBLENBQUM7QUFDRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLDRDQUE0Qzs7QUFFNUM7Ozs7Ozs7Ozs7OztBQ25CQSx3QiIsImZpbGUiOiJtb2R1bGVfY2FyZC5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBnZXR0ZXIgfSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGRlZmluZSBfX2VzTW9kdWxlIG9uIGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uciA9IGZ1bmN0aW9uKGV4cG9ydHMpIHtcbiBcdFx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFN5bWJvbC50b1N0cmluZ1RhZywgeyB2YWx1ZTogJ01vZHVsZScgfSk7XG4gXHRcdH1cbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbiBcdH07XG5cbiBcdC8vIGNyZWF0ZSBhIGZha2UgbmFtZXNwYWNlIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDE6IHZhbHVlIGlzIGEgbW9kdWxlIGlkLCByZXF1aXJlIGl0XG4gXHQvLyBtb2RlICYgMjogbWVyZ2UgYWxsIHByb3BlcnRpZXMgb2YgdmFsdWUgaW50byB0aGUgbnNcbiBcdC8vIG1vZGUgJiA0OiByZXR1cm4gdmFsdWUgd2hlbiBhbHJlYWR5IG5zIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDh8MTogYmVoYXZlIGxpa2UgcmVxdWlyZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy50ID0gZnVuY3Rpb24odmFsdWUsIG1vZGUpIHtcbiBcdFx0aWYobW9kZSAmIDEpIHZhbHVlID0gX193ZWJwYWNrX3JlcXVpcmVfXyh2YWx1ZSk7XG4gXHRcdGlmKG1vZGUgJiA4KSByZXR1cm4gdmFsdWU7XG4gXHRcdGlmKChtb2RlICYgNCkgJiYgdHlwZW9mIHZhbHVlID09PSAnb2JqZWN0JyAmJiB2YWx1ZSAmJiB2YWx1ZS5fX2VzTW9kdWxlKSByZXR1cm4gdmFsdWU7XG4gXHRcdHZhciBucyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18ucihucyk7XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShucywgJ2RlZmF1bHQnLCB7IGVudW1lcmFibGU6IHRydWUsIHZhbHVlOiB2YWx1ZSB9KTtcbiBcdFx0aWYobW9kZSAmIDIgJiYgdHlwZW9mIHZhbHVlICE9ICdzdHJpbmcnKSBmb3IodmFyIGtleSBpbiB2YWx1ZSkgX193ZWJwYWNrX3JlcXVpcmVfXy5kKG5zLCBrZXksIGZ1bmN0aW9uKGtleSkgeyByZXR1cm4gdmFsdWVba2V5XTsgfS5iaW5kKG51bGwsIGtleSkpO1xuIFx0XHRyZXR1cm4gbnM7XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIi9hZG1pbi1kZXYvdGhlbWVzL25ldy10aGVtZS9wdWJsaWMvXCI7XG5cblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSBcIi4vanMvYXBwL3BhZ2VzL21vZHVsZS1jYXJkL2luZGV4LmpzXCIpO1xuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IE1vZHVsZUNhcmQgZnJvbSAnLi4vLi4vLi4vY29tcG9uZW50cy9tb2R1bGUtY2FyZCc7XG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuJCgoKSA9PiB7XG4gIG5ldyBNb2R1bGVDYXJkKCkuaW5pdCgpO1xufSk7XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbnZhciBCT0V2ZW50ID0ge1xuICBvbjogZnVuY3Rpb24oZXZlbnROYW1lLCBjYWxsYmFjaywgY29udGV4dCkge1xuXG4gICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcihldmVudE5hbWUsIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICBpZiAodHlwZW9mIGNvbnRleHQgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgIGNhbGxiYWNrLmNhbGwoY29udGV4dCwgZXZlbnQpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgY2FsbGJhY2soZXZlbnQpO1xuICAgICAgfVxuICAgIH0pO1xuICB9LFxuXG4gIGVtaXRFdmVudDogZnVuY3Rpb24oZXZlbnROYW1lLCBldmVudFR5cGUpIHtcbiAgICB2YXIgX2V2ZW50ID0gZG9jdW1lbnQuY3JlYXRlRXZlbnQoZXZlbnRUeXBlKTtcbiAgICAvLyB0cnVlIHZhbHVlcyBzdGFuZCBmb3I6IGNhbiBidWJibGUsIGFuZCBpcyBjYW5jZWxsYWJsZVxuICAgIF9ldmVudC5pbml0RXZlbnQoZXZlbnROYW1lLCB0cnVlLCB0cnVlKTtcbiAgICBkb2N1bWVudC5kaXNwYXRjaEV2ZW50KF9ldmVudCk7XG4gIH1cbn07XG5cblxuLyoqXG4gKiBDbGFzcyBpcyByZXNwb25zaWJsZSBmb3IgaGFuZGxpbmcgTW9kdWxlIENhcmQgYmVoYXZpb3JcbiAqXG4gKiBUaGlzIGlzIGEgcG9ydCBvZiBhZG1pbi1kZXYvdGhlbWVzL2RlZmF1bHQvanMvYnVuZGxlL21vZHVsZS9tb2R1bGVfY2FyZC5qc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBNb2R1bGVDYXJkIHtcblxuICBjb25zdHJ1Y3RvcigpIHtcbiAgICAvKiBTZWxlY3RvcnMgZm9yIG1vZHVsZSBhY3Rpb24gbGlua3MgKHVuaW5zdGFsbCwgcmVzZXQsIGV0Yy4uLikgdG8gYWRkIGEgY29uZmlybSBwb3BpbiAqL1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51Xyc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51SW5zdGFsbExpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2luc3RhbGwnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudUVuYWJsZUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2VuYWJsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51VW5pbnN0YWxsTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfdW5pbnN0YWxsJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfZGlzYWJsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51RW5hYmxlTW9iaWxlTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfZW5hYmxlX21vYmlsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51RGlzYWJsZU1vYmlsZUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2Rpc2FibGVfbW9iaWxlJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVSZXNldExpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X3Jlc2V0JztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVVcGRhdGVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV91cGdyYWRlJztcbiAgICB0aGlzLm1vZHVsZUl0ZW1MaXN0U2VsZWN0b3IgPSAnLm1vZHVsZS1pdGVtLWxpc3QnO1xuICAgIHRoaXMubW9kdWxlSXRlbUdyaWRTZWxlY3RvciA9ICcubW9kdWxlLWl0ZW0tZ3JpZCc7XG4gICAgdGhpcy5tb2R1bGVJdGVtQWN0aW9uc1NlbGVjdG9yID0gJy5tb2R1bGUtYWN0aW9ucyc7XG5cbiAgICAvKiBTZWxlY3RvcnMgb25seSBmb3IgbW9kYWwgYnV0dG9ucyAqL1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxEaXNhYmxlTGlua1NlbGVjdG9yID0gJ2EubW9kdWxlX2FjdGlvbl9tb2RhbF9kaXNhYmxlJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1vZGFsUmVzZXRMaW5rU2VsZWN0b3IgPSAnYS5tb2R1bGVfYWN0aW9uX21vZGFsX3Jlc2V0JztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1vZGFsVW5pbnN0YWxsTGlua1NlbGVjdG9yID0gJ2EubW9kdWxlX2FjdGlvbl9tb2RhbF91bmluc3RhbGwnO1xuICAgIHRoaXMuZm9yY2VEZWxldGlvbk9wdGlvbiA9ICcjZm9yY2VfZGVsZXRpb24nO1xuXG4gICAgdGhpcy5pbml0QWN0aW9uQnV0dG9ucygpO1xuICB9XG5cbiAgaW5pdEFjdGlvbkJ1dHRvbnMoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLmZvcmNlRGVsZXRpb25PcHRpb24sIGZ1bmN0aW9uICgpIHtcbiAgICAgIGNvbnN0IGJ0biA9ICQoc2VsZi5tb2R1bGVBY3Rpb25Nb2RhbFVuaW5zdGFsbExpbmtTZWxlY3RvciwgJChcImRpdi5tb2R1bGUtaXRlbS1saXN0W2RhdGEtdGVjaC1uYW1lPSdcIiArICQodGhpcykuYXR0cihcImRhdGEtdGVjaC1uYW1lXCIpICsgXCInXVwiKSk7XG4gICAgICBpZiAoJCh0aGlzKS5wcm9wKCdjaGVja2VkJykgPT09IHRydWUpIHtcbiAgICAgICAgYnRuLmF0dHIoJ2RhdGEtZGVsZXRpb24nLCAndHJ1ZScpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgYnRuLnJlbW92ZUF0dHIoJ2RhdGEtZGVsZXRpb24nKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudUluc3RhbGxMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIGlmICgkKFwiI21vZGFsLXByZXN0YXRydXN0XCIpLmxlbmd0aCkge1xuICAgICAgICAkKFwiI21vZGFsLXByZXN0YXRydXN0XCIpLm1vZGFsKCdoaWRlJyk7XG4gICAgICB9XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgnaW5zdGFsbCcsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ2luc3RhbGwnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdpbnN0YWxsJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51RW5hYmxlTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgnZW5hYmxlJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbignZW5hYmxlJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZW5hYmxlJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51VW5pbnN0YWxsTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgndW5pbnN0YWxsJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbigndW5pbnN0YWxsJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcigndW5pbnN0YWxsJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51RGlzYWJsZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ2Rpc2FibGUnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdkaXNhYmxlJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZGlzYWJsZScsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudUVuYWJsZU1vYmlsZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ2VuYWJsZV9tb2JpbGUnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdlbmFibGVfbW9iaWxlJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZW5hYmxlX21vYmlsZScsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudURpc2FibGVNb2JpbGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdkaXNhYmxlX21vYmlsZScsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ2Rpc2FibGVfbW9iaWxlJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZGlzYWJsZV9tb2JpbGUnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVSZXNldExpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ3Jlc2V0JywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbigncmVzZXQnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdyZXNldCcsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudVVwZGF0ZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ3VwZGF0ZScsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ3VwZGF0ZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ3VwZGF0ZScsICQodGhpcykpO1xuICAgIH0pO1xuXG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbERpc2FibGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdkaXNhYmxlJywgJChzZWxmLm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTGlua1NlbGVjdG9yLCAkKFwiZGl2Lm1vZHVsZS1pdGVtLWxpc3RbZGF0YS10ZWNoLW5hbWU9J1wiICsgJCh0aGlzKS5hdHRyKFwiZGF0YS10ZWNoLW5hbWVcIikgKyBcIiddXCIpKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbFJlc2V0TGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcigncmVzZXQnLCAkKHNlbGYubW9kdWxlQWN0aW9uTWVudVJlc2V0TGlua1NlbGVjdG9yLCAkKFwiZGl2Lm1vZHVsZS1pdGVtLWxpc3RbZGF0YS10ZWNoLW5hbWU9J1wiICsgJCh0aGlzKS5hdHRyKFwiZGF0YS10ZWNoLW5hbWVcIikgKyBcIiddXCIpKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbFVuaW5zdGFsbExpbmtTZWxlY3RvciwgZnVuY3Rpb24gKGUpIHtcbiAgICAgICQoZS50YXJnZXQpLnBhcmVudHMoJy5tb2RhbCcpLm9uKCdoaWRkZW4uYnMubW9kYWwnLCBmdW5jdGlvbihldmVudCkge1xuICAgICAgICByZXR1cm4gc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcihcbiAgICAgICAgICAndW5pbnN0YWxsJyxcbiAgICAgICAgICAkKFxuICAgICAgICAgICAgc2VsZi5tb2R1bGVBY3Rpb25NZW51VW5pbnN0YWxsTGlua1NlbGVjdG9yLFxuICAgICAgICAgICAgJChcImRpdi5tb2R1bGUtaXRlbS1saXN0W2RhdGEtdGVjaC1uYW1lPSdcIiArICQoZS50YXJnZXQpLmF0dHIoXCJkYXRhLXRlY2gtbmFtZVwiKSArIFwiJ11cIilcbiAgICAgICAgICApLFxuICAgICAgICAgICQoZS50YXJnZXQpLmF0dHIoXCJkYXRhLWRlbGV0aW9uXCIpXG4gICAgICAgICk7XG4gICAgICB9LmJpbmQoZSkpO1xuICAgIH0pO1xuICB9O1xuXG4gIF9nZXRNb2R1bGVJdGVtU2VsZWN0b3IoKSB7XG4gICAgaWYgKCQodGhpcy5tb2R1bGVJdGVtTGlzdFNlbGVjdG9yKS5sZW5ndGgpIHtcbiAgICAgIHJldHVybiB0aGlzLm1vZHVsZUl0ZW1MaXN0U2VsZWN0b3I7XG4gICAgfSBlbHNlIHtcbiAgICAgIHJldHVybiB0aGlzLm1vZHVsZUl0ZW1HcmlkU2VsZWN0b3I7XG4gICAgfVxuICB9O1xuXG4gIF9jb25maXJtQWN0aW9uKGFjdGlvbiwgZWxlbWVudCkge1xuICAgIHZhciBtb2RhbCA9ICQoJyMnICsgJChlbGVtZW50KS5kYXRhKCdjb25maXJtX21vZGFsJykpO1xuICAgIGlmIChtb2RhbC5sZW5ndGggIT0gMSkge1xuICAgICAgcmV0dXJuIHRydWU7XG4gICAgfVxuICAgIG1vZGFsLmZpcnN0KCkubW9kYWwoJ3Nob3cnKTtcblxuICAgIHJldHVybiBmYWxzZTsgLy8gZG8gbm90IGFsbG93IGEuaHJlZiB0byByZWxvYWQgdGhlIHBhZ2UuIFRoZSBjb25maXJtIG1vZGFsIGRpYWxvZyB3aWxsIGRvIGl0IGFzeW5jIGlmIG5lZWRlZC5cbiAgfTtcblxuICAvKipcbiAgICogVXBkYXRlIHRoZSBjb250ZW50IG9mIGEgbW9kYWwgYXNraW5nIGEgY29uZmlybWF0aW9uIGZvciBQcmVzdGFUcnVzdCBhbmQgb3BlbiBpdFxuICAgKlxuICAgKiBAcGFyYW0ge2FycmF5fSByZXN1bHQgY29udGFpbmluZyBtb2R1bGUgZGF0YVxuICAgKiBAcmV0dXJuIHt2b2lkfVxuICAgKi9cbiAgX2NvbmZpcm1QcmVzdGFUcnVzdChyZXN1bHQpIHtcbiAgICB2YXIgdGhhdCA9IHRoaXM7XG4gICAgdmFyIG1vZGFsID0gdGhpcy5fcmVwbGFjZVByZXN0YVRydXN0UGxhY2Vob2xkZXJzKHJlc3VsdCk7XG5cbiAgICBtb2RhbC5maW5kKFwiLnBzdHJ1c3QtaW5zdGFsbFwiKS5vZmYoJ2NsaWNrJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAvLyBGaW5kIHJlbGF0ZWQgZm9ybSwgdXBkYXRlIGl0IGFuZCBzdWJtaXQgaXRcbiAgICAgIHZhciBpbnN0YWxsX2J1dHRvbiA9ICQodGhhdC5tb2R1bGVBY3Rpb25NZW51SW5zdGFsbExpbmtTZWxlY3RvciwgJy5tb2R1bGUtaXRlbVtkYXRhLXRlY2gtbmFtZT1cIicgKyByZXN1bHQubW9kdWxlLmF0dHJpYnV0ZXMubmFtZSArICdcIl0nKTtcbiAgICAgIHZhciBmb3JtID0gaW5zdGFsbF9idXR0b24ucGFyZW50KFwiZm9ybVwiKTtcbiAgICAgICQoJzxpbnB1dD4nKS5hdHRyKHtcbiAgICAgICAgdHlwZTogJ2hpZGRlbicsXG4gICAgICAgIHZhbHVlOiAnMScsXG4gICAgICAgIG5hbWU6ICdhY3Rpb25QYXJhbXNbY29uZmlybVByZXN0YVRydXN0XSdcbiAgICAgIH0pLmFwcGVuZFRvKGZvcm0pO1xuXG4gICAgICBpbnN0YWxsX2J1dHRvbi5jbGljaygpO1xuICAgICAgbW9kYWwubW9kYWwoJ2hpZGUnKTtcbiAgICB9KTtcblxuICAgIG1vZGFsLm1vZGFsKCk7XG4gIH07XG5cbiAgX3JlcGxhY2VQcmVzdGFUcnVzdFBsYWNlaG9sZGVycyhyZXN1bHQpIHtcbiAgICB2YXIgbW9kYWwgPSAkKFwiI21vZGFsLXByZXN0YXRydXN0XCIpO1xuICAgIHZhciBtb2R1bGUgPSByZXN1bHQubW9kdWxlLmF0dHJpYnV0ZXM7XG5cbiAgICBpZiAocmVzdWx0LmNvbmZpcm1hdGlvbl9zdWJqZWN0ICE9PSAnUHJlc3RhVHJ1c3QnIHx8ICFtb2RhbC5sZW5ndGgpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB2YXIgYWxlcnRDbGFzcyA9IG1vZHVsZS5wcmVzdGF0cnVzdC5zdGF0dXMgPyAnc3VjY2VzcycgOiAnd2FybmluZyc7XG5cbiAgICBpZiAobW9kdWxlLnByZXN0YXRydXN0LmNoZWNrX2xpc3QucHJvcGVydHkpIHtcbiAgICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1idG4tcHJvcGVydHktb2tcIikuc2hvdygpO1xuICAgICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWJ0bi1wcm9wZXJ0eS1ub2tcIikuaGlkZSgpO1xuICAgIH0gZWxzZSB7XG4gICAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYnRuLXByb3BlcnR5LW9rXCIpLmhpZGUoKTtcbiAgICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1idG4tcHJvcGVydHktbm9rXCIpLnNob3coKTtcbiAgICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1idXlcIikuYXR0cihcImhyZWZcIiwgbW9kdWxlLnVybCkudG9nZ2xlKG1vZHVsZS51cmwgIT09IG51bGwpO1xuICAgIH1cblxuICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1pbWdcIikuYXR0cih7c3JjOiBtb2R1bGUuaW1nLCBhbHQ6IG1vZHVsZS5uYW1lfSk7XG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LW5hbWVcIikudGV4dChtb2R1bGUuZGlzcGxheU5hbWUpO1xuICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1hdXRob3JcIikudGV4dChtb2R1bGUuYXV0aG9yKTtcbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtbGFiZWxcIikuYXR0cihcImNsYXNzXCIsIFwidGV4dC1cIiArIGFsZXJ0Q2xhc3MpLnRleHQobW9kdWxlLnByZXN0YXRydXN0LnN0YXR1cyA/ICdPSycgOiAnS08nKTtcbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtbWVzc2FnZVwiKS5hdHRyKFwiY2xhc3NcIiwgXCJhbGVydCBhbGVydC1cIithbGVydENsYXNzKTtcbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtbWVzc2FnZSA+IHBcIikudGV4dChtb2R1bGUucHJlc3RhdHJ1c3QubWVzc2FnZSk7XG5cbiAgICByZXR1cm4gbW9kYWw7XG4gIH1cblxuICBfZGlzcGF0Y2hQcmVFdmVudChhY3Rpb24sIGVsZW1lbnQpIHtcbiAgICB2YXIgZXZlbnQgPSBqUXVlcnkuRXZlbnQoJ21vZHVsZV9jYXJkX2FjdGlvbl9ldmVudCcpO1xuXG4gICAgJChlbGVtZW50KS50cmlnZ2VyKGV2ZW50LCBbYWN0aW9uXSk7XG4gICAgaWYgKGV2ZW50LmlzUHJvcGFnYXRpb25TdG9wcGVkKCkgIT09IGZhbHNlIHx8IGV2ZW50LmlzSW1tZWRpYXRlUHJvcGFnYXRpb25TdG9wcGVkKCkgIT09IGZhbHNlKSB7XG4gICAgICByZXR1cm4gZmFsc2U7IC8vIGlmIGFsbCBoYW5kbGVycyBoYXZlIG5vdCBiZWVuIGNhbGxlZCwgdGhlbiBzdG9wIHByb3BhZ2F0aW9uIG9mIHRoZSBjbGljayBldmVudC5cbiAgICB9XG5cbiAgICByZXR1cm4gKGV2ZW50LnJlc3VsdCAhPT0gZmFsc2UpOyAvLyBleHBsaWNpdCBmYWxzZSBtdXN0IGJlIHNldCBmcm9tIGhhbmRsZXJzIHRvIHN0b3AgcHJvcGFnYXRpb24gb2YgdGhlIGNsaWNrIGV2ZW50LlxuICB9O1xuXG4gIF9yZXF1ZXN0VG9Db250cm9sbGVyKGFjdGlvbiwgZWxlbWVudCwgZm9yY2VEZWxldGlvbiwgZGlzYWJsZUNhY2hlQ2xlYXIsIGNhbGxiYWNrKSB7XG4gICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgIHZhciBqcUVsZW1lbnRPYmogPSBlbGVtZW50LmNsb3Nlc3QodGhpcy5tb2R1bGVJdGVtQWN0aW9uc1NlbGVjdG9yKTtcbiAgICB2YXIgZm9ybSA9IGVsZW1lbnQuY2xvc2VzdChcImZvcm1cIik7XG4gICAgdmFyIHNwaW5uZXJPYmogPSAkKFwiPGJ1dHRvbiBjbGFzcz1cXFwiYnRuLXByaW1hcnktcmV2ZXJzZSBvbmNsaWNrIHVuYmluZCBzcGlubmVyIFxcXCI+PC9idXR0b24+XCIpO1xuICAgIHZhciB1cmwgPSBcIi8vXCIgKyB3aW5kb3cubG9jYXRpb24uaG9zdCArIGZvcm0uYXR0cihcImFjdGlvblwiKTtcbiAgICB2YXIgYWN0aW9uUGFyYW1zID0gZm9ybS5zZXJpYWxpemVBcnJheSgpO1xuXG4gICAgaWYgKGZvcmNlRGVsZXRpb24gPT09IFwidHJ1ZVwiIHx8IGZvcmNlRGVsZXRpb24gPT09IHRydWUpIHtcbiAgICAgIGFjdGlvblBhcmFtcy5wdXNoKHtuYW1lOiBcImFjdGlvblBhcmFtc1tkZWxldGlvbl1cIiwgdmFsdWU6IHRydWV9KTtcbiAgICB9XG4gICAgaWYgKGRpc2FibGVDYWNoZUNsZWFyID09PSBcInRydWVcIiB8fCBkaXNhYmxlQ2FjaGVDbGVhciA9PT0gdHJ1ZSkge1xuICAgICAgYWN0aW9uUGFyYW1zLnB1c2goe25hbWU6IFwiYWN0aW9uUGFyYW1zW2NhY2hlQ2xlYXJFbmFibGVkXVwiLCB2YWx1ZTogMH0pO1xuICAgIH1cblxuICAgICQuYWpheCh7XG4gICAgICB1cmw6IHVybCxcbiAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICAgIGRhdGE6IGFjdGlvblBhcmFtcyxcbiAgICAgIGJlZm9yZVNlbmQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAganFFbGVtZW50T2JqLmhpZGUoKTtcbiAgICAgICAganFFbGVtZW50T2JqLmFmdGVyKHNwaW5uZXJPYmopO1xuICAgICAgfVxuICAgIH0pLmRvbmUoZnVuY3Rpb24gKHJlc3VsdCkge1xuICAgICAgaWYgKHR5cGVvZiByZXN1bHQgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAkLmdyb3dsLmVycm9yKHttZXNzYWdlOiBcIk5vIGFuc3dlciByZWNlaXZlZCBmcm9tIHNlcnZlclwifSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB2YXIgbW9kdWxlVGVjaE5hbWUgPSBPYmplY3Qua2V5cyhyZXN1bHQpWzBdO1xuXG4gICAgICAgIGlmIChyZXN1bHRbbW9kdWxlVGVjaE5hbWVdLnN0YXR1cyA9PT0gZmFsc2UpIHtcbiAgICAgICAgICBpZiAodHlwZW9mIHJlc3VsdFttb2R1bGVUZWNoTmFtZV0uY29uZmlybWF0aW9uX3N1YmplY3QgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICBzZWxmLl9jb25maXJtUHJlc3RhVHJ1c3QocmVzdWx0W21vZHVsZVRlY2hOYW1lXSk7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogcmVzdWx0W21vZHVsZVRlY2hOYW1lXS5tc2d9KTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAkLmdyb3dsLm5vdGljZSh7bWVzc2FnZTogcmVzdWx0W21vZHVsZVRlY2hOYW1lXS5tc2d9KTtcblxuICAgICAgICAgIHZhciBhbHRlcmVkU2VsZWN0b3IgPSBzZWxmLl9nZXRNb2R1bGVJdGVtU2VsZWN0b3IoKS5yZXBsYWNlKCcuJywgJycpO1xuICAgICAgICAgIHZhciBtYWluRWxlbWVudCA9IG51bGw7XG5cbiAgICAgICAgICBpZiAoYWN0aW9uID09IFwidW5pbnN0YWxsXCIpIHtcbiAgICAgICAgICAgIG1haW5FbGVtZW50ID0ganFFbGVtZW50T2JqLmNsb3Nlc3QoJy4nICsgYWx0ZXJlZFNlbGVjdG9yKTtcbiAgICAgICAgICAgIG1haW5FbGVtZW50LnJlbW92ZSgpO1xuXG4gICAgICAgICAgICBCT0V2ZW50LmVtaXRFdmVudChcIk1vZHVsZSBVbmluc3RhbGxlZFwiLCBcIkN1c3RvbUV2ZW50XCIpO1xuICAgICAgICAgIH0gZWxzZSBpZiAoYWN0aW9uID09IFwiZGlzYWJsZVwiKSB7XG4gICAgICAgICAgICBtYWluRWxlbWVudCA9IGpxRWxlbWVudE9iai5jbG9zZXN0KCcuJyArIGFsdGVyZWRTZWxlY3Rvcik7XG4gICAgICAgICAgICBtYWluRWxlbWVudC5hZGRDbGFzcyhhbHRlcmVkU2VsZWN0b3IgKyAnLWlzTm90QWN0aXZlJyk7XG4gICAgICAgICAgICBtYWluRWxlbWVudC5hdHRyKCdkYXRhLWFjdGl2ZScsICcwJyk7XG5cbiAgICAgICAgICAgIEJPRXZlbnQuZW1pdEV2ZW50KFwiTW9kdWxlIERpc2FibGVkXCIsIFwiQ3VzdG9tRXZlbnRcIik7XG4gICAgICAgICAgfSBlbHNlIGlmIChhY3Rpb24gPT0gXCJlbmFibGVcIikge1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQgPSBqcUVsZW1lbnRPYmouY2xvc2VzdCgnLicgKyBhbHRlcmVkU2VsZWN0b3IpO1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQucmVtb3ZlQ2xhc3MoYWx0ZXJlZFNlbGVjdG9yICsgJy1pc05vdEFjdGl2ZScpO1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQuYXR0cignZGF0YS1hY3RpdmUnLCAnMScpO1xuXG4gICAgICAgICAgICBCT0V2ZW50LmVtaXRFdmVudChcIk1vZHVsZSBFbmFibGVkXCIsIFwiQ3VzdG9tRXZlbnRcIik7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAganFFbGVtZW50T2JqLnJlcGxhY2VXaXRoKHJlc3VsdFttb2R1bGVUZWNoTmFtZV0uYWN0aW9uX21lbnVfaHRtbCk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9KS5mYWlsKGZ1bmN0aW9uKCkge1xuICAgICAgY29uc3QgbW9kdWxlSXRlbSA9IGpxRWxlbWVudE9iai5jbG9zZXN0KCdtb2R1bGUtaXRlbS1saXN0Jyk7XG4gICAgICBjb25zdCB0ZWNoTmFtZSA9IG1vZHVsZUl0ZW0uZGF0YSgndGVjaE5hbWUnKTtcbiAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IFwiQ291bGQgbm90IHBlcmZvcm0gYWN0aW9uIFwiK2FjdGlvbitcIiBmb3IgbW9kdWxlIFwiK3RlY2hOYW1lfSk7XG4gICAgfSkuYWx3YXlzKGZ1bmN0aW9uICgpIHtcbiAgICAgIGpxRWxlbWVudE9iai5mYWRlSW4oKTtcbiAgICAgIHNwaW5uZXJPYmoucmVtb3ZlKCk7XG4gICAgICBpZiAoY2FsbGJhY2spIHtcbiAgICAgICAgY2FsbGJhY2soKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgIHJldHVybiBmYWxzZTtcbiAgfTtcbn1cbiIsInZhciBnO1xuXG4vLyBUaGlzIHdvcmtzIGluIG5vbi1zdHJpY3QgbW9kZVxuZyA9IChmdW5jdGlvbigpIHtcblx0cmV0dXJuIHRoaXM7XG59KSgpO1xuXG50cnkge1xuXHQvLyBUaGlzIHdvcmtzIGlmIGV2YWwgaXMgYWxsb3dlZCAoc2VlIENTUClcblx0ZyA9IGcgfHwgbmV3IEZ1bmN0aW9uKFwicmV0dXJuIHRoaXNcIikoKTtcbn0gY2F0Y2ggKGUpIHtcblx0Ly8gVGhpcyB3b3JrcyBpZiB0aGUgd2luZG93IHJlZmVyZW5jZSBpcyBhdmFpbGFibGVcblx0aWYgKHR5cGVvZiB3aW5kb3cgPT09IFwib2JqZWN0XCIpIGcgPSB3aW5kb3c7XG59XG5cbi8vIGcgY2FuIHN0aWxsIGJlIHVuZGVmaW5lZCwgYnV0IG5vdGhpbmcgdG8gZG8gYWJvdXQgaXQuLi5cbi8vIFdlIHJldHVybiB1bmRlZmluZWQsIGluc3RlYWQgb2Ygbm90aGluZyBoZXJlLCBzbyBpdCdzXG4vLyBlYXNpZXIgdG8gaGFuZGxlIHRoaXMgY2FzZS4gaWYoIWdsb2JhbCkgeyAuLi59XG5cbm1vZHVsZS5leHBvcnRzID0gZztcbiIsIm1vZHVsZS5leHBvcnRzID0galF1ZXJ5OyJdLCJzb3VyY2VSb290IjoiIn0=