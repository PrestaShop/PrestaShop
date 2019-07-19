window["module_card"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 298);
/******/ })
/************************************************************************/
/******/ ({

/***/ 1:
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || Function("return this")() || (1,eval)("this");
} catch(e) {
	// This works if the window reference is available
	if(typeof window === "object")
		g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ 298:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

var _moduleCard = __webpack_require__(58);

var _moduleCard2 = _interopRequireDefault(_moduleCard);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = global.$; /**
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
  new _moduleCard2.default().init();
});
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }),

/***/ 58:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(jQuery) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

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
    var _event = document.createEvent(eventType);
    // true values stand for: can bubble, and is cancellable
    _event.initEvent(eventName, true, true);
    document.dispatchEvent(_event);
  }
};

/**
 * Class is responsible for handling Module Card behavior
 *
 * This is a port of admin-dev/themes/default/js/bundle/module/module_card.js
 */

var ModuleCard = function () {
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
    key: 'initActionButtons',
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
    key: '_getModuleItemSelector',
    value: function _getModuleItemSelector() {
      if ($(this.moduleItemListSelector).length) {
        return this.moduleItemListSelector;
      } else {
        return this.moduleItemGridSelector;
      }
    }
  }, {
    key: '_confirmAction',
    value: function _confirmAction(action, element) {
      var modal = $('#' + $(element).data('confirm_modal'));
      if (modal.length != 1) {
        return true;
      }
      modal.first().modal('show');

      return false; // do not allow a.href to reload the page. The confirm modal dialog will do it async if needed.
    }
  }, {
    key: '_confirmPrestaTrust',


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
    key: '_replacePrestaTrustPlaceholders',
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

      modal.find("#pstrust-img").attr({ src: module.img, alt: module.name });
      modal.find("#pstrust-name").text(module.displayName);
      modal.find("#pstrust-author").text(module.author);
      modal.find("#pstrust-label").attr("class", "text-" + alertClass).text(module.prestatrust.status ? 'OK' : 'KO');
      modal.find("#pstrust-message").attr("class", "alert alert-" + alertClass);
      modal.find("#pstrust-message > p").text(module.prestatrust.message);

      return modal;
    }
  }, {
    key: '_dispatchPreEvent',
    value: function _dispatchPreEvent(action, element) {
      var event = jQuery.Event('module_card_action_event');

      $(element).trigger(event, [action]);
      if (event.isPropagationStopped() !== false || event.isImmediatePropagationStopped() !== false) {
        return false; // if all handlers have not been called, then stop propagation of the click event.
      }

      return event.result !== false; // explicit false must be set from handlers to stop propagation of the click event.
    }
  }, {
    key: '_requestToController',
    value: function _requestToController(action, element, forceDeletion, disableCacheClear, callback) {
      var self = this;
      var jqElementObj = element.closest(this.moduleItemActionsSelector);
      var form = element.closest("form");
      var spinnerObj = $("<button class=\"btn-primary-reverse onclick unbind spinner \"></button>");
      var url = "//" + window.location.host + form.attr("action");
      var actionParams = form.serializeArray();

      if (forceDeletion === "true" || forceDeletion === true) {
        actionParams.push({ name: "actionParams[deletion]", value: true });
      }
      if (disableCacheClear === "true" || disableCacheClear === true) {
        actionParams.push({ name: "actionParams[cacheClearEnabled]", value: 0 });
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
        if ((typeof result === 'undefined' ? 'undefined' : _typeof(result)) === undefined) {
          $.growl.error({ message: "No answer received from server" });
        } else {
          var moduleTechName = Object.keys(result)[0];

          if (result[moduleTechName].status === false) {
            if (typeof result[moduleTechName].confirmation_subject !== 'undefined') {
              self._confirmPrestaTrust(result[moduleTechName]);
            }

            $.growl.error({ message: result[moduleTechName].msg });
          } else {
            $.growl.notice({ message: result[moduleTechName].msg });

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
        $.growl.error({ message: "Could not perform action " + action + " for module " + techName });
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

exports.default = ModuleCard;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(7)))

/***/ }),

/***/ 7:
/***/ (function(module, exports) {

(function() { module.exports = window["jQuery"]; }());

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODI/M2YxNioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8od2VicGFjaykvYnVpbGRpbi9nbG9iYWwuanM/MzY5OCoqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL3BhZ2VzL21vZHVsZS1jYXJkL2luZGV4LmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvbW9kdWxlLWNhcmQuanM/Y2YzZSIsIndlYnBhY2s6Ly8vZXh0ZXJuYWwgXCJqUXVlcnlcIj8wY2I4KioqKioqKiJdLCJuYW1lcyI6WyIkIiwiZ2xvYmFsIiwiTW9kdWxlQ2FyZCIsImluaXQiLCJ3aW5kb3ciLCJCT0V2ZW50Iiwib24iLCJldmVudE5hbWUiLCJjYWxsYmFjayIsImNvbnRleHQiLCJkb2N1bWVudCIsImFkZEV2ZW50TGlzdGVuZXIiLCJldmVudCIsImNhbGwiLCJlbWl0RXZlbnQiLCJldmVudFR5cGUiLCJfZXZlbnQiLCJjcmVhdGVFdmVudCIsImluaXRFdmVudCIsImRpc3BhdGNoRXZlbnQiLCJtb2R1bGVBY3Rpb25NZW51TGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudUluc3RhbGxMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25NZW51RW5hYmxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudVVuaW5zdGFsbExpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudUVuYWJsZU1vYmlsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTW9iaWxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudVJlc2V0TGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudVVwZGF0ZUxpbmtTZWxlY3RvciIsIm1vZHVsZUl0ZW1MaXN0U2VsZWN0b3IiLCJtb2R1bGVJdGVtR3JpZFNlbGVjdG9yIiwibW9kdWxlSXRlbUFjdGlvbnNTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1vZGFsRGlzYWJsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1vZGFsUmVzZXRMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25Nb2RhbFVuaW5zdGFsbExpbmtTZWxlY3RvciIsImZvcmNlRGVsZXRpb25PcHRpb24iLCJpbml0QWN0aW9uQnV0dG9ucyIsInNlbGYiLCJidG4iLCJhdHRyIiwicHJvcCIsInJlbW92ZUF0dHIiLCJsZW5ndGgiLCJtb2RhbCIsIl9kaXNwYXRjaFByZUV2ZW50IiwiX2NvbmZpcm1BY3Rpb24iLCJfcmVxdWVzdFRvQ29udHJvbGxlciIsImUiLCJ0YXJnZXQiLCJwYXJlbnRzIiwiYmluZCIsImFjdGlvbiIsImVsZW1lbnQiLCJkYXRhIiwiZmlyc3QiLCJyZXN1bHQiLCJ0aGF0IiwiX3JlcGxhY2VQcmVzdGFUcnVzdFBsYWNlaG9sZGVycyIsImZpbmQiLCJvZmYiLCJpbnN0YWxsX2J1dHRvbiIsIm1vZHVsZSIsImF0dHJpYnV0ZXMiLCJuYW1lIiwiZm9ybSIsInBhcmVudCIsInR5cGUiLCJ2YWx1ZSIsImFwcGVuZFRvIiwiY2xpY2siLCJjb25maXJtYXRpb25fc3ViamVjdCIsImFsZXJ0Q2xhc3MiLCJwcmVzdGF0cnVzdCIsInN0YXR1cyIsImNoZWNrX2xpc3QiLCJwcm9wZXJ0eSIsInNob3ciLCJoaWRlIiwidXJsIiwidG9nZ2xlIiwic3JjIiwiaW1nIiwiYWx0IiwidGV4dCIsImRpc3BsYXlOYW1lIiwiYXV0aG9yIiwibWVzc2FnZSIsImpRdWVyeSIsIkV2ZW50IiwidHJpZ2dlciIsImlzUHJvcGFnYXRpb25TdG9wcGVkIiwiaXNJbW1lZGlhdGVQcm9wYWdhdGlvblN0b3BwZWQiLCJmb3JjZURlbGV0aW9uIiwiZGlzYWJsZUNhY2hlQ2xlYXIiLCJqcUVsZW1lbnRPYmoiLCJjbG9zZXN0Iiwic3Bpbm5lck9iaiIsImxvY2F0aW9uIiwiaG9zdCIsImFjdGlvblBhcmFtcyIsInNlcmlhbGl6ZUFycmF5IiwicHVzaCIsImFqYXgiLCJkYXRhVHlwZSIsIm1ldGhvZCIsImJlZm9yZVNlbmQiLCJhZnRlciIsImRvbmUiLCJ1bmRlZmluZWQiLCJncm93bCIsImVycm9yIiwibW9kdWxlVGVjaE5hbWUiLCJPYmplY3QiLCJrZXlzIiwiX2NvbmZpcm1QcmVzdGFUcnVzdCIsIm1zZyIsIm5vdGljZSIsImFsdGVyZWRTZWxlY3RvciIsIl9nZXRNb2R1bGVJdGVtU2VsZWN0b3IiLCJyZXBsYWNlIiwibWFpbkVsZW1lbnQiLCJyZW1vdmUiLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIiwicmVwbGFjZVdpdGgiLCJhY3Rpb25fbWVudV9odG1sIiwiZmFpbCIsIm1vZHVsZUl0ZW0iLCJ0ZWNoTmFtZSIsImFsd2F5cyIsImZhZGVJbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7Ozs7QUNoRUE7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsQ0FBQzs7QUFFRDtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLDRDQUE0Qzs7QUFFNUM7Ozs7Ozs7Ozs7O0FDS0E7Ozs7OztBQUVBLElBQU1BLElBQUlDLE9BQU9ELENBQWpCLEMsQ0Ez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LFlBQU1pQyxXQUFXRCxXQUFXMUUsSUFBWCxDQUFnQixVQUFoQixDQUFqQjtBQUNBckQsVUFBRTZHLEtBQUYsQ0FBUUMsS0FBUixDQUFjLEVBQUN4QixTQUFTLDhCQUE0Qm5DLE1BQTVCLEdBQW1DLGNBQW5DLEdBQWtENkUsUUFBNUQsRUFBZDtBQUNELE9BckRELEVBcURHQyxNQXJESCxDQXFEVSxZQUFZO0FBQ3BCbkMscUJBQWFvQyxNQUFiO0FBQ0FsQyxtQkFBV3lCLE1BQVg7QUFDQSxZQUFJakgsUUFBSixFQUFjO0FBQ1pBO0FBQ0Q7QUFDRixPQTNERDs7QUE2REEsYUFBTyxLQUFQO0FBQ0Q7Ozs7OztrQkF4UGtCTixVOzs7Ozs7OztBQ3JEckIsYUFBYSxtQ0FBbUMsRUFBRSxJIiwiZmlsZSI6Im1vZHVsZV9jYXJkLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMjk4KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCA1ZDk5OTA5NGQxMWFlZmYwYjU4MiIsInZhciBnO1xyXG5cclxuLy8gVGhpcyB3b3JrcyBpbiBub24tc3RyaWN0IG1vZGVcclxuZyA9IChmdW5jdGlvbigpIHtcclxuXHRyZXR1cm4gdGhpcztcclxufSkoKTtcclxuXHJcbnRyeSB7XHJcblx0Ly8gVGhpcyB3b3JrcyBpZiBldmFsIGlzIGFsbG93ZWQgKHNlZSBDU1ApXHJcblx0ZyA9IGcgfHwgRnVuY3Rpb24oXCJyZXR1cm4gdGhpc1wiKSgpIHx8ICgxLGV2YWwpKFwidGhpc1wiKTtcclxufSBjYXRjaChlKSB7XHJcblx0Ly8gVGhpcyB3b3JrcyBpZiB0aGUgd2luZG93IHJlZmVyZW5jZSBpcyBhdmFpbGFibGVcclxuXHRpZih0eXBlb2Ygd2luZG93ID09PSBcIm9iamVjdFwiKVxyXG5cdFx0ZyA9IHdpbmRvdztcclxufVxyXG5cclxuLy8gZyBjYW4gc3RpbGwgYmUgdW5kZWZpbmVkLCBidXQgbm90aGluZyB0byBkbyBhYm91dCBpdC4uLlxyXG4vLyBXZSByZXR1cm4gdW5kZWZpbmVkLCBpbnN0ZWFkIG9mIG5vdGhpbmcgaGVyZSwgc28gaXQnc1xyXG4vLyBlYXNpZXIgdG8gaGFuZGxlIHRoaXMgY2FzZS4gaWYoIWdsb2JhbCkgeyAuLi59XHJcblxyXG5tb2R1bGUuZXhwb3J0cyA9IGc7XHJcblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vICh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qc1xuLy8gbW9kdWxlIGlkID0gMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjUgMzAgMzUiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgTW9kdWxlQ2FyZCBmcm9tICcuLi8uLi8uLi9jb21wb25lbnRzL21vZHVsZS1jYXJkJztcblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG4kKCgpID0+IHtcbiAgbmV3IE1vZHVsZUNhcmQoKS5pbml0KCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC9wYWdlcy9tb2R1bGUtY2FyZC9pbmRleC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxudmFyIEJPRXZlbnQgPSB7XG4gIG9uOiBmdW5jdGlvbihldmVudE5hbWUsIGNhbGxiYWNrLCBjb250ZXh0KSB7XG5cbiAgICBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKGV2ZW50TmFtZSwgZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgIGlmICh0eXBlb2YgY29udGV4dCAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgY2FsbGJhY2suY2FsbChjb250ZXh0LCBldmVudCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBjYWxsYmFjayhldmVudCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH0sXG5cbiAgZW1pdEV2ZW50OiBmdW5jdGlvbihldmVudE5hbWUsIGV2ZW50VHlwZSkge1xuICAgIHZhciBfZXZlbnQgPSBkb2N1bWVudC5jcmVhdGVFdmVudChldmVudFR5cGUpO1xuICAgIC8vIHRydWUgdmFsdWVzIHN0YW5kIGZvcjogY2FuIGJ1YmJsZSwgYW5kIGlzIGNhbmNlbGxhYmxlXG4gICAgX2V2ZW50LmluaXRFdmVudChldmVudE5hbWUsIHRydWUsIHRydWUpO1xuICAgIGRvY3VtZW50LmRpc3BhdGNoRXZlbnQoX2V2ZW50KTtcbiAgfVxufTtcblxuXG4vKipcbiAqIENsYXNzIGlzIHJlc3BvbnNpYmxlIGZvciBoYW5kbGluZyBNb2R1bGUgQ2FyZCBiZWhhdmlvclxuICpcbiAqIFRoaXMgaXMgYSBwb3J0IG9mIGFkbWluLWRldi90aGVtZXMvZGVmYXVsdC9qcy9idW5kbGUvbW9kdWxlL21vZHVsZV9jYXJkLmpzXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIE1vZHVsZUNhcmQge1xuXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIC8qIFNlbGVjdG9ycyBmb3IgbW9kdWxlIGFjdGlvbiBsaW5rcyAodW5pbnN0YWxsLCByZXNldCwgZXRjLi4uKSB0byBhZGQgYSBjb25maXJtIHBvcGluICovXG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51TGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVJbnN0YWxsTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfaW5zdGFsbCc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51RW5hYmxlTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfZW5hYmxlJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVVbmluc3RhbGxMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV91bmluc3RhbGwnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudURpc2FibGVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9kaXNhYmxlJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVFbmFibGVNb2JpbGVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9lbmFibGVfbW9iaWxlJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTW9iaWxlTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfZGlzYWJsZV9tb2JpbGUnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudVJlc2V0TGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfcmVzZXQnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudVVwZGF0ZUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X3VwZ3JhZGUnO1xuICAgIHRoaXMubW9kdWxlSXRlbUxpc3RTZWxlY3RvciA9ICcubW9kdWxlLWl0ZW0tbGlzdCc7XG4gICAgdGhpcy5tb2R1bGVJdGVtR3JpZFNlbGVjdG9yID0gJy5tb2R1bGUtaXRlbS1ncmlkJztcbiAgICB0aGlzLm1vZHVsZUl0ZW1BY3Rpb25zU2VsZWN0b3IgPSAnLm1vZHVsZS1hY3Rpb25zJztcblxuICAgIC8qIFNlbGVjdG9ycyBvbmx5IGZvciBtb2RhbCBidXR0b25zICovXG4gICAgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbERpc2FibGVMaW5rU2VsZWN0b3IgPSAnYS5tb2R1bGVfYWN0aW9uX21vZGFsX2Rpc2FibGUnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxSZXNldExpbmtTZWxlY3RvciA9ICdhLm1vZHVsZV9hY3Rpb25fbW9kYWxfcmVzZXQnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxVbmluc3RhbGxMaW5rU2VsZWN0b3IgPSAnYS5tb2R1bGVfYWN0aW9uX21vZGFsX3VuaW5zdGFsbCc7XG4gICAgdGhpcy5mb3JjZURlbGV0aW9uT3B0aW9uID0gJyNmb3JjZV9kZWxldGlvbic7XG5cbiAgICB0aGlzLmluaXRBY3Rpb25CdXR0b25zKCk7XG4gIH1cblxuICBpbml0QWN0aW9uQnV0dG9ucygpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMuZm9yY2VEZWxldGlvbk9wdGlvbiwgZnVuY3Rpb24gKCkge1xuICAgICAgY29uc3QgYnRuID0gJChzZWxmLm1vZHVsZUFjdGlvbk1vZGFsVW5pbnN0YWxsTGlua1NlbGVjdG9yLCAkKFwiZGl2Lm1vZHVsZS1pdGVtLWxpc3RbZGF0YS10ZWNoLW5hbWU9J1wiICsgJCh0aGlzKS5hdHRyKFwiZGF0YS10ZWNoLW5hbWVcIikgKyBcIiddXCIpKTtcbiAgICAgIGlmICgkKHRoaXMpLnByb3AoJ2NoZWNrZWQnKSA9PT0gdHJ1ZSkge1xuICAgICAgICBidG4uYXR0cignZGF0YS1kZWxldGlvbicsICd0cnVlJyk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBidG4ucmVtb3ZlQXR0cignZGF0YS1kZWxldGlvbicpO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51SW5zdGFsbExpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgaWYgKCQoXCIjbW9kYWwtcHJlc3RhdHJ1c3RcIikubGVuZ3RoKSB7XG4gICAgICAgICQoXCIjbW9kYWwtcHJlc3RhdHJ1c3RcIikubW9kYWwoJ2hpZGUnKTtcbiAgICAgIH1cbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdpbnN0YWxsJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbignaW5zdGFsbCcsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2luc3RhbGwnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVFbmFibGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdlbmFibGUnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdlbmFibGUnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdlbmFibGUnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVVbmluc3RhbGxMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCd1bmluc3RhbGwnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCd1bmluc3RhbGwnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCd1bmluc3RhbGwnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgnZGlzYWJsZScsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ2Rpc2FibGUnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdkaXNhYmxlJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51RW5hYmxlTW9iaWxlTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgnZW5hYmxlX21vYmlsZScsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ2VuYWJsZV9tb2JpbGUnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdlbmFibGVfbW9iaWxlJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51RGlzYWJsZU1vYmlsZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ2Rpc2FibGVfbW9iaWxlJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbignZGlzYWJsZV9tb2JpbGUnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdkaXNhYmxlX21vYmlsZScsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudVJlc2V0TGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgncmVzZXQnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdyZXNldCcsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ3Jlc2V0JywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51VXBkYXRlTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgndXBkYXRlJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbigndXBkYXRlJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcigndXBkYXRlJywgJCh0aGlzKSk7XG4gICAgfSk7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1vZGFsRGlzYWJsZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2Rpc2FibGUnLCAkKHNlbGYubW9kdWxlQWN0aW9uTWVudURpc2FibGVMaW5rU2VsZWN0b3IsICQoXCJkaXYubW9kdWxlLWl0ZW0tbGlzdFtkYXRhLXRlY2gtbmFtZT0nXCIgKyAkKHRoaXMpLmF0dHIoXCJkYXRhLXRlY2gtbmFtZVwiKSArIFwiJ11cIikpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1vZGFsUmVzZXRMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdyZXNldCcsICQoc2VsZi5tb2R1bGVBY3Rpb25NZW51UmVzZXRMaW5rU2VsZWN0b3IsICQoXCJkaXYubW9kdWxlLWl0ZW0tbGlzdFtkYXRhLXRlY2gtbmFtZT0nXCIgKyAkKHRoaXMpLmF0dHIoXCJkYXRhLXRlY2gtbmFtZVwiKSArIFwiJ11cIikpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1vZGFsVW5pbnN0YWxsTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoZSkge1xuICAgICAgJChlLnRhcmdldCkucGFyZW50cygnLm1vZGFsJykub24oJ2hpZGRlbi5icy5tb2RhbCcsIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgIHJldHVybiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKFxuICAgICAgICAgICd1bmluc3RhbGwnLFxuICAgICAgICAgICQoXG4gICAgICAgICAgICBzZWxmLm1vZHVsZUFjdGlvbk1lbnVVbmluc3RhbGxMaW5rU2VsZWN0b3IsXG4gICAgICAgICAgICAkKFwiZGl2Lm1vZHVsZS1pdGVtLWxpc3RbZGF0YS10ZWNoLW5hbWU9J1wiICsgJChlLnRhcmdldCkuYXR0cihcImRhdGEtdGVjaC1uYW1lXCIpICsgXCInXVwiKVxuICAgICAgICAgICksXG4gICAgICAgICAgJChlLnRhcmdldCkuYXR0cihcImRhdGEtZGVsZXRpb25cIilcbiAgICAgICAgKTtcbiAgICAgIH0uYmluZChlKSk7XG4gICAgfSk7XG4gIH07XG5cbiAgX2dldE1vZHVsZUl0ZW1TZWxlY3RvcigpIHtcbiAgICBpZiAoJCh0aGlzLm1vZHVsZUl0ZW1MaXN0U2VsZWN0b3IpLmxlbmd0aCkge1xuICAgICAgcmV0dXJuIHRoaXMubW9kdWxlSXRlbUxpc3RTZWxlY3RvcjtcbiAgICB9IGVsc2Uge1xuICAgICAgcmV0dXJuIHRoaXMubW9kdWxlSXRlbUdyaWRTZWxlY3RvcjtcbiAgICB9XG4gIH07XG5cbiAgX2NvbmZpcm1BY3Rpb24oYWN0aW9uLCBlbGVtZW50KSB7XG4gICAgdmFyIG1vZGFsID0gJCgnIycgKyAkKGVsZW1lbnQpLmRhdGEoJ2NvbmZpcm1fbW9kYWwnKSk7XG4gICAgaWYgKG1vZGFsLmxlbmd0aCAhPSAxKSB7XG4gICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9XG4gICAgbW9kYWwuZmlyc3QoKS5tb2RhbCgnc2hvdycpO1xuXG4gICAgcmV0dXJuIGZhbHNlOyAvLyBkbyBub3QgYWxsb3cgYS5ocmVmIHRvIHJlbG9hZCB0aGUgcGFnZS4gVGhlIGNvbmZpcm0gbW9kYWwgZGlhbG9nIHdpbGwgZG8gaXQgYXN5bmMgaWYgbmVlZGVkLlxuICB9O1xuXG4gIC8qKlxuICAgKiBVcGRhdGUgdGhlIGNvbnRlbnQgb2YgYSBtb2RhbCBhc2tpbmcgYSBjb25maXJtYXRpb24gZm9yIFByZXN0YVRydXN0IGFuZCBvcGVuIGl0XG4gICAqXG4gICAqIEBwYXJhbSB7YXJyYXl9IHJlc3VsdCBjb250YWluaW5nIG1vZHVsZSBkYXRhXG4gICAqIEByZXR1cm4ge3ZvaWR9XG4gICAqL1xuICBfY29uZmlybVByZXN0YVRydXN0KHJlc3VsdCkge1xuICAgIHZhciB0aGF0ID0gdGhpcztcbiAgICB2YXIgbW9kYWwgPSB0aGlzLl9yZXBsYWNlUHJlc3RhVHJ1c3RQbGFjZWhvbGRlcnMocmVzdWx0KTtcblxuICAgIG1vZGFsLmZpbmQoXCIucHN0cnVzdC1pbnN0YWxsXCIpLm9mZignY2xpY2snKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgIC8vIEZpbmQgcmVsYXRlZCBmb3JtLCB1cGRhdGUgaXQgYW5kIHN1Ym1pdCBpdFxuICAgICAgdmFyIGluc3RhbGxfYnV0dG9uID0gJCh0aGF0Lm1vZHVsZUFjdGlvbk1lbnVJbnN0YWxsTGlua1NlbGVjdG9yLCAnLm1vZHVsZS1pdGVtW2RhdGEtdGVjaC1uYW1lPVwiJyArIHJlc3VsdC5tb2R1bGUuYXR0cmlidXRlcy5uYW1lICsgJ1wiXScpO1xuICAgICAgdmFyIGZvcm0gPSBpbnN0YWxsX2J1dHRvbi5wYXJlbnQoXCJmb3JtXCIpO1xuICAgICAgJCgnPGlucHV0PicpLmF0dHIoe1xuICAgICAgICB0eXBlOiAnaGlkZGVuJyxcbiAgICAgICAgdmFsdWU6ICcxJyxcbiAgICAgICAgbmFtZTogJ2FjdGlvblBhcmFtc1tjb25maXJtUHJlc3RhVHJ1c3RdJ1xuICAgICAgfSkuYXBwZW5kVG8oZm9ybSk7XG5cbiAgICAgIGluc3RhbGxfYnV0dG9uLmNsaWNrKCk7XG4gICAgICBtb2RhbC5tb2RhbCgnaGlkZScpO1xuICAgIH0pO1xuXG4gICAgbW9kYWwubW9kYWwoKTtcbiAgfTtcblxuICBfcmVwbGFjZVByZXN0YVRydXN0UGxhY2Vob2xkZXJzKHJlc3VsdCkge1xuICAgIHZhciBtb2RhbCA9ICQoXCIjbW9kYWwtcHJlc3RhdHJ1c3RcIik7XG4gICAgdmFyIG1vZHVsZSA9IHJlc3VsdC5tb2R1bGUuYXR0cmlidXRlcztcblxuICAgIGlmIChyZXN1bHQuY29uZmlybWF0aW9uX3N1YmplY3QgIT09ICdQcmVzdGFUcnVzdCcgfHwgIW1vZGFsLmxlbmd0aCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHZhciBhbGVydENsYXNzID0gbW9kdWxlLnByZXN0YXRydXN0LnN0YXR1cyA/ICdzdWNjZXNzJyA6ICd3YXJuaW5nJztcblxuICAgIGlmIChtb2R1bGUucHJlc3RhdHJ1c3QuY2hlY2tfbGlzdC5wcm9wZXJ0eSkge1xuICAgICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWJ0bi1wcm9wZXJ0eS1va1wiKS5zaG93KCk7XG4gICAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYnRuLXByb3BlcnR5LW5va1wiKS5oaWRlKCk7XG4gICAgfSBlbHNlIHtcbiAgICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1idG4tcHJvcGVydHktb2tcIikuaGlkZSgpO1xuICAgICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWJ0bi1wcm9wZXJ0eS1ub2tcIikuc2hvdygpO1xuICAgICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWJ1eVwiKS5hdHRyKFwiaHJlZlwiLCBtb2R1bGUudXJsKS50b2dnbGUobW9kdWxlLnVybCAhPT0gbnVsbCk7XG4gICAgfVxuXG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWltZ1wiKS5hdHRyKHtzcmM6IG1vZHVsZS5pbWcsIGFsdDogbW9kdWxlLm5hbWV9KTtcbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtbmFtZVwiKS50ZXh0KG1vZHVsZS5kaXNwbGF5TmFtZSk7XG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWF1dGhvclwiKS50ZXh0KG1vZHVsZS5hdXRob3IpO1xuICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1sYWJlbFwiKS5hdHRyKFwiY2xhc3NcIiwgXCJ0ZXh0LVwiICsgYWxlcnRDbGFzcykudGV4dChtb2R1bGUucHJlc3RhdHJ1c3Quc3RhdHVzID8gJ09LJyA6ICdLTycpO1xuICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1tZXNzYWdlXCIpLmF0dHIoXCJjbGFzc1wiLCBcImFsZXJ0IGFsZXJ0LVwiK2FsZXJ0Q2xhc3MpO1xuICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1tZXNzYWdlID4gcFwiKS50ZXh0KG1vZHVsZS5wcmVzdGF0cnVzdC5tZXNzYWdlKTtcblxuICAgIHJldHVybiBtb2RhbDtcbiAgfVxuXG4gIF9kaXNwYXRjaFByZUV2ZW50KGFjdGlvbiwgZWxlbWVudCkge1xuICAgIHZhciBldmVudCA9IGpRdWVyeS5FdmVudCgnbW9kdWxlX2NhcmRfYWN0aW9uX2V2ZW50Jyk7XG5cbiAgICAkKGVsZW1lbnQpLnRyaWdnZXIoZXZlbnQsIFthY3Rpb25dKTtcbiAgICBpZiAoZXZlbnQuaXNQcm9wYWdhdGlvblN0b3BwZWQoKSAhPT0gZmFsc2UgfHwgZXZlbnQuaXNJbW1lZGlhdGVQcm9wYWdhdGlvblN0b3BwZWQoKSAhPT0gZmFsc2UpIHtcbiAgICAgIHJldHVybiBmYWxzZTsgLy8gaWYgYWxsIGhhbmRsZXJzIGhhdmUgbm90IGJlZW4gY2FsbGVkLCB0aGVuIHN0b3AgcHJvcGFnYXRpb24gb2YgdGhlIGNsaWNrIGV2ZW50LlxuICAgIH1cblxuICAgIHJldHVybiAoZXZlbnQucmVzdWx0ICE9PSBmYWxzZSk7IC8vIGV4cGxpY2l0IGZhbHNlIG11c3QgYmUgc2V0IGZyb20gaGFuZGxlcnMgdG8gc3RvcCBwcm9wYWdhdGlvbiBvZiB0aGUgY2xpY2sgZXZlbnQuXG4gIH07XG5cbiAgX3JlcXVlc3RUb0NvbnRyb2xsZXIoYWN0aW9uLCBlbGVtZW50LCBmb3JjZURlbGV0aW9uLCBkaXNhYmxlQ2FjaGVDbGVhciwgY2FsbGJhY2spIHtcbiAgICB2YXIgc2VsZiA9IHRoaXM7XG4gICAgdmFyIGpxRWxlbWVudE9iaiA9IGVsZW1lbnQuY2xvc2VzdCh0aGlzLm1vZHVsZUl0ZW1BY3Rpb25zU2VsZWN0b3IpO1xuICAgIHZhciBmb3JtID0gZWxlbWVudC5jbG9zZXN0KFwiZm9ybVwiKTtcbiAgICB2YXIgc3Bpbm5lck9iaiA9ICQoXCI8YnV0dG9uIGNsYXNzPVxcXCJidG4tcHJpbWFyeS1yZXZlcnNlIG9uY2xpY2sgdW5iaW5kIHNwaW5uZXIgXFxcIj48L2J1dHRvbj5cIik7XG4gICAgdmFyIHVybCA9IFwiLy9cIiArIHdpbmRvdy5sb2NhdGlvbi5ob3N0ICsgZm9ybS5hdHRyKFwiYWN0aW9uXCIpO1xuICAgIHZhciBhY3Rpb25QYXJhbXMgPSBmb3JtLnNlcmlhbGl6ZUFycmF5KCk7XG5cbiAgICBpZiAoZm9yY2VEZWxldGlvbiA9PT0gXCJ0cnVlXCIgfHwgZm9yY2VEZWxldGlvbiA9PT0gdHJ1ZSkge1xuICAgICAgYWN0aW9uUGFyYW1zLnB1c2goe25hbWU6IFwiYWN0aW9uUGFyYW1zW2RlbGV0aW9uXVwiLCB2YWx1ZTogdHJ1ZX0pO1xuICAgIH1cbiAgICBpZiAoZGlzYWJsZUNhY2hlQ2xlYXIgPT09IFwidHJ1ZVwiIHx8IGRpc2FibGVDYWNoZUNsZWFyID09PSB0cnVlKSB7XG4gICAgICBhY3Rpb25QYXJhbXMucHVzaCh7bmFtZTogXCJhY3Rpb25QYXJhbXNbY2FjaGVDbGVhckVuYWJsZWRdXCIsIHZhbHVlOiAwfSk7XG4gICAgfVxuXG4gICAgJC5hamF4KHtcbiAgICAgIHVybDogdXJsLFxuICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgICAgZGF0YTogYWN0aW9uUGFyYW1zLFxuICAgICAgYmVmb3JlU2VuZDogZnVuY3Rpb24gKCkge1xuICAgICAgICBqcUVsZW1lbnRPYmouaGlkZSgpO1xuICAgICAgICBqcUVsZW1lbnRPYmouYWZ0ZXIoc3Bpbm5lck9iaik7XG4gICAgICB9XG4gICAgfSkuZG9uZShmdW5jdGlvbiAocmVzdWx0KSB7XG4gICAgICBpZiAodHlwZW9mIHJlc3VsdCA9PT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IFwiTm8gYW5zd2VyIHJlY2VpdmVkIGZyb20gc2VydmVyXCJ9KTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHZhciBtb2R1bGVUZWNoTmFtZSA9IE9iamVjdC5rZXlzKHJlc3VsdClbMF07XG5cbiAgICAgICAgaWYgKHJlc3VsdFttb2R1bGVUZWNoTmFtZV0uc3RhdHVzID09PSBmYWxzZSkge1xuICAgICAgICAgIGlmICh0eXBlb2YgcmVzdWx0W21vZHVsZVRlY2hOYW1lXS5jb25maXJtYXRpb25fc3ViamVjdCAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgICAgIHNlbGYuX2NvbmZpcm1QcmVzdGFUcnVzdChyZXN1bHRbbW9kdWxlVGVjaE5hbWVdKTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICAkLmdyb3dsLmVycm9yKHttZXNzYWdlOiByZXN1bHRbbW9kdWxlVGVjaE5hbWVdLm1zZ30pO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICQuZ3Jvd2wubm90aWNlKHttZXNzYWdlOiByZXN1bHRbbW9kdWxlVGVjaE5hbWVdLm1zZ30pO1xuXG4gICAgICAgICAgdmFyIGFsdGVyZWRTZWxlY3RvciA9IHNlbGYuX2dldE1vZHVsZUl0ZW1TZWxlY3RvcigpLnJlcGxhY2UoJy4nLCAnJyk7XG4gICAgICAgICAgdmFyIG1haW5FbGVtZW50ID0gbnVsbDtcblxuICAgICAgICAgIGlmIChhY3Rpb24gPT0gXCJ1bmluc3RhbGxcIikge1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQgPSBqcUVsZW1lbnRPYmouY2xvc2VzdCgnLicgKyBhbHRlcmVkU2VsZWN0b3IpO1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgIEJPRXZlbnQuZW1pdEV2ZW50KFwiTW9kdWxlIFVuaW5zdGFsbGVkXCIsIFwiQ3VzdG9tRXZlbnRcIik7XG4gICAgICAgICAgfSBlbHNlIGlmIChhY3Rpb24gPT0gXCJkaXNhYmxlXCIpIHtcbiAgICAgICAgICAgIG1haW5FbGVtZW50ID0ganFFbGVtZW50T2JqLmNsb3Nlc3QoJy4nICsgYWx0ZXJlZFNlbGVjdG9yKTtcbiAgICAgICAgICAgIG1haW5FbGVtZW50LmFkZENsYXNzKGFsdGVyZWRTZWxlY3RvciArICctaXNOb3RBY3RpdmUnKTtcbiAgICAgICAgICAgIG1haW5FbGVtZW50LmF0dHIoJ2RhdGEtYWN0aXZlJywgJzAnKTtcblxuICAgICAgICAgICAgQk9FdmVudC5lbWl0RXZlbnQoXCJNb2R1bGUgRGlzYWJsZWRcIiwgXCJDdXN0b21FdmVudFwiKTtcbiAgICAgICAgICB9IGVsc2UgaWYgKGFjdGlvbiA9PSBcImVuYWJsZVwiKSB7XG4gICAgICAgICAgICBtYWluRWxlbWVudCA9IGpxRWxlbWVudE9iai5jbG9zZXN0KCcuJyArIGFsdGVyZWRTZWxlY3Rvcik7XG4gICAgICAgICAgICBtYWluRWxlbWVudC5yZW1vdmVDbGFzcyhhbHRlcmVkU2VsZWN0b3IgKyAnLWlzTm90QWN0aXZlJyk7XG4gICAgICAgICAgICBtYWluRWxlbWVudC5hdHRyKCdkYXRhLWFjdGl2ZScsICcxJyk7XG5cbiAgICAgICAgICAgIEJPRXZlbnQuZW1pdEV2ZW50KFwiTW9kdWxlIEVuYWJsZWRcIiwgXCJDdXN0b21FdmVudFwiKTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBqcUVsZW1lbnRPYmoucmVwbGFjZVdpdGgocmVzdWx0W21vZHVsZVRlY2hOYW1lXS5hY3Rpb25fbWVudV9odG1sKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH0pLmZhaWwoZnVuY3Rpb24oKSB7XG4gICAgICBjb25zdCBtb2R1bGVJdGVtID0ganFFbGVtZW50T2JqLmNsb3Nlc3QoJ21vZHVsZS1pdGVtLWxpc3QnKTtcbiAgICAgIGNvbnN0IHRlY2hOYW1lID0gbW9kdWxlSXRlbS5kYXRhKCd0ZWNoTmFtZScpO1xuICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogXCJDb3VsZCBub3QgcGVyZm9ybSBhY3Rpb24gXCIrYWN0aW9uK1wiIGZvciBtb2R1bGUgXCIrdGVjaE5hbWV9KTtcbiAgICB9KS5hbHdheXMoZnVuY3Rpb24gKCkge1xuICAgICAganFFbGVtZW50T2JqLmZhZGVJbigpO1xuICAgICAgc3Bpbm5lck9iai5yZW1vdmUoKTtcbiAgICAgIGlmIChjYWxsYmFjaykge1xuICAgICAgICBjYWxsYmFjaygpO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgcmV0dXJuIGZhbHNlO1xuICB9O1xufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9tb2R1bGUtY2FyZC5qcyIsIihmdW5jdGlvbigpIHsgbW9kdWxlLmV4cG9ydHMgPSB3aW5kb3dbXCJqUXVlcnlcIl07IH0oKSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gZXh0ZXJuYWwgXCJqUXVlcnlcIlxuLy8gbW9kdWxlIGlkID0gN1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA2IDIyIDI4IDMwIl0sInNvdXJjZVJvb3QiOiIifQ==