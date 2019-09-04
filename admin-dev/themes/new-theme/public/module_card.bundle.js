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

/***/ 11:
/***/ (function(module, exports) {

(function() { module.exports = window["jQuery"]; }());

/***/ }),

/***/ 298:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

var _moduleCard = __webpack_require__(59);

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

/***/ 59:
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
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(11)))

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQ/MjBkNCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLyh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qcz8zNjk4KioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy9leHRlcm5hbCBcImpRdWVyeVwiPzBjYjgqKioqKioqIiwid2VicGFjazovLy8uL2pzL2FwcC9wYWdlcy9tb2R1bGUtY2FyZC9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL21vZHVsZS1jYXJkLmpzP2NmM2UiXSwibmFtZXMiOlsiJCIsImdsb2JhbCIsIk1vZHVsZUNhcmQiLCJpbml0Iiwid2luZG93IiwiQk9FdmVudCIsIm9uIiwiZXZlbnROYW1lIiwiY2FsbGJhY2siLCJjb250ZXh0IiwiZG9jdW1lbnQiLCJhZGRFdmVudExpc3RlbmVyIiwiZXZlbnQiLCJjYWxsIiwiZW1pdEV2ZW50IiwiZXZlbnRUeXBlIiwiX2V2ZW50IiwiY3JlYXRlRXZlbnQiLCJpbml0RXZlbnQiLCJkaXNwYXRjaEV2ZW50IiwibW9kdWxlQWN0aW9uTWVudUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVJbnN0YWxsTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudUVuYWJsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVVbmluc3RhbGxMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25NZW51RGlzYWJsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVFbmFibGVNb2JpbGVMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25NZW51RGlzYWJsZU1vYmlsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVSZXNldExpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVVcGRhdGVMaW5rU2VsZWN0b3IiLCJtb2R1bGVJdGVtTGlzdFNlbGVjdG9yIiwibW9kdWxlSXRlbUdyaWRTZWxlY3RvciIsIm1vZHVsZUl0ZW1BY3Rpb25zU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25Nb2RhbERpc2FibGVMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25Nb2RhbFJlc2V0TGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTW9kYWxVbmluc3RhbGxMaW5rU2VsZWN0b3IiLCJmb3JjZURlbGV0aW9uT3B0aW9uIiwiaW5pdEFjdGlvbkJ1dHRvbnMiLCJzZWxmIiwiYnRuIiwiYXR0ciIsInByb3AiLCJyZW1vdmVBdHRyIiwibGVuZ3RoIiwibW9kYWwiLCJfZGlzcGF0Y2hQcmVFdmVudCIsIl9jb25maXJtQWN0aW9uIiwiX3JlcXVlc3RUb0NvbnRyb2xsZXIiLCJlIiwidGFyZ2V0IiwicGFyZW50cyIsImJpbmQiLCJhY3Rpb24iLCJlbGVtZW50IiwiZGF0YSIsImZpcnN0IiwicmVzdWx0IiwidGhhdCIsIl9yZXBsYWNlUHJlc3RhVHJ1c3RQbGFjZWhvbGRlcnMiLCJmaW5kIiwib2ZmIiwiaW5zdGFsbF9idXR0b24iLCJtb2R1bGUiLCJhdHRyaWJ1dGVzIiwibmFtZSIsImZvcm0iLCJwYXJlbnQiLCJ0eXBlIiwidmFsdWUiLCJhcHBlbmRUbyIsImNsaWNrIiwiY29uZmlybWF0aW9uX3N1YmplY3QiLCJhbGVydENsYXNzIiwicHJlc3RhdHJ1c3QiLCJzdGF0dXMiLCJjaGVja19saXN0IiwicHJvcGVydHkiLCJzaG93IiwiaGlkZSIsInVybCIsInRvZ2dsZSIsInNyYyIsImltZyIsImFsdCIsInRleHQiLCJkaXNwbGF5TmFtZSIsImF1dGhvciIsIm1lc3NhZ2UiLCJqUXVlcnkiLCJFdmVudCIsInRyaWdnZXIiLCJpc1Byb3BhZ2F0aW9uU3RvcHBlZCIsImlzSW1tZWRpYXRlUHJvcGFnYXRpb25TdG9wcGVkIiwiZm9yY2VEZWxldGlvbiIsImRpc2FibGVDYWNoZUNsZWFyIiwianFFbGVtZW50T2JqIiwiY2xvc2VzdCIsInNwaW5uZXJPYmoiLCJsb2NhdGlvbiIsImhvc3QiLCJhY3Rpb25QYXJhbXMiLCJzZXJpYWxpemVBcnJheSIsInB1c2giLCJhamF4IiwiZGF0YVR5cGUiLCJtZXRob2QiLCJiZWZvcmVTZW5kIiwiYWZ0ZXIiLCJkb25lIiwidW5kZWZpbmVkIiwiZ3Jvd2wiLCJlcnJvciIsIm1vZHVsZVRlY2hOYW1lIiwiT2JqZWN0Iiwia2V5cyIsIl9jb25maXJtUHJlc3RhVHJ1c3QiLCJtc2ciLCJub3RpY2UiLCJhbHRlcmVkU2VsZWN0b3IiLCJfZ2V0TW9kdWxlSXRlbVNlbGVjdG9yIiwicmVwbGFjZSIsIm1haW5FbGVtZW50IiwicmVtb3ZlIiwiYWRkQ2xhc3MiLCJyZW1vdmVDbGFzcyIsInJlcGxhY2VXaXRoIiwiYWN0aW9uX21lbnVfaHRtbCIsImZhaWwiLCJtb2R1bGVJdGVtIiwidGVjaE5hbWUiLCJhbHdheXMiLCJmYWRlSW4iXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7O0FDaEVBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLENBQUM7O0FBRUQ7QUFDQTtBQUNBO0FBQ0EsQ0FBQztBQUNEO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSw0Q0FBNEM7O0FBRTVDOzs7Ozs7OztBQ3BCQSxhQUFhLG1DQUFtQyxFQUFFLEk7Ozs7Ozs7Ozs7QUN5QmxEOzs7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akMsYUFBYUMsT0FBYixDQUFxQixrQkFBckIsQ0FBbkI7QUFDQSxZQUFNaUMsV0FBV0QsV0FBVzFFLElBQVgsQ0FBZ0IsVUFBaEIsQ0FBakI7QUFDQXJELFVBQUU2RyxLQUFGLENBQVFDLEtBQVIsQ0FBYyxFQUFDeEIsU0FBUyw4QkFBNEJuQyxNQUE1QixHQUFtQyxjQUFuQyxHQUFrRDZFLFFBQTVELEVBQWQ7QUFDRCxPQXJERCxFQXFER0MsTUFyREgsQ0FxRFUsWUFBWTtBQUNwQm5DLHFCQUFhb0MsTUFBYjtBQUNBbEMsbUJBQVd5QixNQUFYO0FBQ0EsWUFBSWpILFFBQUosRUFBYztBQUNaQTtBQUNEO0FBQ0YsT0EzREQ7O0FBNkRBLGFBQU8sS0FBUDtBQUNEOzs7Ozs7a0JBeFBrQk4sVSIsImZpbGUiOiJtb2R1bGVfY2FyZC5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDI5OCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQiLCJ2YXIgZztcclxuXHJcbi8vIFRoaXMgd29ya3MgaW4gbm9uLXN0cmljdCBtb2RlXHJcbmcgPSAoZnVuY3Rpb24oKSB7XHJcblx0cmV0dXJuIHRoaXM7XHJcbn0pKCk7XHJcblxyXG50cnkge1xyXG5cdC8vIFRoaXMgd29ya3MgaWYgZXZhbCBpcyBhbGxvd2VkIChzZWUgQ1NQKVxyXG5cdGcgPSBnIHx8IEZ1bmN0aW9uKFwicmV0dXJuIHRoaXNcIikoKSB8fCAoMSxldmFsKShcInRoaXNcIik7XHJcbn0gY2F0Y2goZSkge1xyXG5cdC8vIFRoaXMgd29ya3MgaWYgdGhlIHdpbmRvdyByZWZlcmVuY2UgaXMgYXZhaWxhYmxlXHJcblx0aWYodHlwZW9mIHdpbmRvdyA9PT0gXCJvYmplY3RcIilcclxuXHRcdGcgPSB3aW5kb3c7XHJcbn1cclxuXHJcbi8vIGcgY2FuIHN0aWxsIGJlIHVuZGVmaW5lZCwgYnV0IG5vdGhpbmcgdG8gZG8gYWJvdXQgaXQuLi5cclxuLy8gV2UgcmV0dXJuIHVuZGVmaW5lZCwgaW5zdGVhZCBvZiBub3RoaW5nIGhlcmUsIHNvIGl0J3NcclxuLy8gZWFzaWVyIHRvIGhhbmRsZSB0aGlzIGNhc2UuIGlmKCFnbG9iYWwpIHsgLi4ufVxyXG5cclxubW9kdWxlLmV4cG9ydHMgPSBnO1xyXG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAod2VicGFjaykvYnVpbGRpbi9nbG9iYWwuanNcbi8vIG1vZHVsZSBpZCA9IDFcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDI1IDI4IDMyIDM2IiwiKGZ1bmN0aW9uKCkgeyBtb2R1bGUuZXhwb3J0cyA9IHdpbmRvd1tcImpRdWVyeVwiXTsgfSgpKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyBleHRlcm5hbCBcImpRdWVyeVwiXG4vLyBtb2R1bGUgaWQgPSAxMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA2IDIzIDMwIDMyIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IE1vZHVsZUNhcmQgZnJvbSAnLi4vLi4vLi4vY29tcG9uZW50cy9tb2R1bGUtY2FyZCc7XG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuJCgoKSA9PiB7XG4gIG5ldyBNb2R1bGVDYXJkKCkuaW5pdCgpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvcGFnZXMvbW9kdWxlLWNhcmQvaW5kZXguanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbnZhciBCT0V2ZW50ID0ge1xuICBvbjogZnVuY3Rpb24oZXZlbnROYW1lLCBjYWxsYmFjaywgY29udGV4dCkge1xuXG4gICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcihldmVudE5hbWUsIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICBpZiAodHlwZW9mIGNvbnRleHQgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgIGNhbGxiYWNrLmNhbGwoY29udGV4dCwgZXZlbnQpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgY2FsbGJhY2soZXZlbnQpO1xuICAgICAgfVxuICAgIH0pO1xuICB9LFxuXG4gIGVtaXRFdmVudDogZnVuY3Rpb24oZXZlbnROYW1lLCBldmVudFR5cGUpIHtcbiAgICB2YXIgX2V2ZW50ID0gZG9jdW1lbnQuY3JlYXRlRXZlbnQoZXZlbnRUeXBlKTtcbiAgICAvLyB0cnVlIHZhbHVlcyBzdGFuZCBmb3I6IGNhbiBidWJibGUsIGFuZCBpcyBjYW5jZWxsYWJsZVxuICAgIF9ldmVudC5pbml0RXZlbnQoZXZlbnROYW1lLCB0cnVlLCB0cnVlKTtcbiAgICBkb2N1bWVudC5kaXNwYXRjaEV2ZW50KF9ldmVudCk7XG4gIH1cbn07XG5cblxuLyoqXG4gKiBDbGFzcyBpcyByZXNwb25zaWJsZSBmb3IgaGFuZGxpbmcgTW9kdWxlIENhcmQgYmVoYXZpb3JcbiAqXG4gKiBUaGlzIGlzIGEgcG9ydCBvZiBhZG1pbi1kZXYvdGhlbWVzL2RlZmF1bHQvanMvYnVuZGxlL21vZHVsZS9tb2R1bGVfY2FyZC5qc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBNb2R1bGVDYXJkIHtcblxuICBjb25zdHJ1Y3RvcigpIHtcbiAgICAvKiBTZWxlY3RvcnMgZm9yIG1vZHVsZSBhY3Rpb24gbGlua3MgKHVuaW5zdGFsbCwgcmVzZXQsIGV0Yy4uLikgdG8gYWRkIGEgY29uZmlybSBwb3BpbiAqL1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51Xyc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51SW5zdGFsbExpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2luc3RhbGwnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudUVuYWJsZUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2VuYWJsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51VW5pbnN0YWxsTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfdW5pbnN0YWxsJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfZGlzYWJsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51RW5hYmxlTW9iaWxlTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfZW5hYmxlX21vYmlsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51RGlzYWJsZU1vYmlsZUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2Rpc2FibGVfbW9iaWxlJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVSZXNldExpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X3Jlc2V0JztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVVcGRhdGVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV91cGdyYWRlJztcbiAgICB0aGlzLm1vZHVsZUl0ZW1MaXN0U2VsZWN0b3IgPSAnLm1vZHVsZS1pdGVtLWxpc3QnO1xuICAgIHRoaXMubW9kdWxlSXRlbUdyaWRTZWxlY3RvciA9ICcubW9kdWxlLWl0ZW0tZ3JpZCc7XG4gICAgdGhpcy5tb2R1bGVJdGVtQWN0aW9uc1NlbGVjdG9yID0gJy5tb2R1bGUtYWN0aW9ucyc7XG5cbiAgICAvKiBTZWxlY3RvcnMgb25seSBmb3IgbW9kYWwgYnV0dG9ucyAqL1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxEaXNhYmxlTGlua1NlbGVjdG9yID0gJ2EubW9kdWxlX2FjdGlvbl9tb2RhbF9kaXNhYmxlJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1vZGFsUmVzZXRMaW5rU2VsZWN0b3IgPSAnYS5tb2R1bGVfYWN0aW9uX21vZGFsX3Jlc2V0JztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1vZGFsVW5pbnN0YWxsTGlua1NlbGVjdG9yID0gJ2EubW9kdWxlX2FjdGlvbl9tb2RhbF91bmluc3RhbGwnO1xuICAgIHRoaXMuZm9yY2VEZWxldGlvbk9wdGlvbiA9ICcjZm9yY2VfZGVsZXRpb24nO1xuXG4gICAgdGhpcy5pbml0QWN0aW9uQnV0dG9ucygpO1xuICB9XG5cbiAgaW5pdEFjdGlvbkJ1dHRvbnMoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLmZvcmNlRGVsZXRpb25PcHRpb24sIGZ1bmN0aW9uICgpIHtcbiAgICAgIGNvbnN0IGJ0biA9ICQoc2VsZi5tb2R1bGVBY3Rpb25Nb2RhbFVuaW5zdGFsbExpbmtTZWxlY3RvciwgJChcImRpdi5tb2R1bGUtaXRlbS1saXN0W2RhdGEtdGVjaC1uYW1lPSdcIiArICQodGhpcykuYXR0cihcImRhdGEtdGVjaC1uYW1lXCIpICsgXCInXVwiKSk7XG4gICAgICBpZiAoJCh0aGlzKS5wcm9wKCdjaGVja2VkJykgPT09IHRydWUpIHtcbiAgICAgICAgYnRuLmF0dHIoJ2RhdGEtZGVsZXRpb24nLCAndHJ1ZScpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgYnRuLnJlbW92ZUF0dHIoJ2RhdGEtZGVsZXRpb24nKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudUluc3RhbGxMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIGlmICgkKFwiI21vZGFsLXByZXN0YXRydXN0XCIpLmxlbmd0aCkge1xuICAgICAgICAkKFwiI21vZGFsLXByZXN0YXRydXN0XCIpLm1vZGFsKCdoaWRlJyk7XG4gICAgICB9XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgnaW5zdGFsbCcsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ2luc3RhbGwnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdpbnN0YWxsJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51RW5hYmxlTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgnZW5hYmxlJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbignZW5hYmxlJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZW5hYmxlJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51VW5pbnN0YWxsTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgndW5pbnN0YWxsJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbigndW5pbnN0YWxsJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcigndW5pbnN0YWxsJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51RGlzYWJsZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ2Rpc2FibGUnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdkaXNhYmxlJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZGlzYWJsZScsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudUVuYWJsZU1vYmlsZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ2VuYWJsZV9tb2JpbGUnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdlbmFibGVfbW9iaWxlJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZW5hYmxlX21vYmlsZScsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudURpc2FibGVNb2JpbGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdkaXNhYmxlX21vYmlsZScsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ2Rpc2FibGVfbW9iaWxlJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZGlzYWJsZV9tb2JpbGUnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVSZXNldExpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ3Jlc2V0JywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbigncmVzZXQnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdyZXNldCcsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudVVwZGF0ZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ3VwZGF0ZScsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ3VwZGF0ZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ3VwZGF0ZScsICQodGhpcykpO1xuICAgIH0pO1xuXG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbERpc2FibGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdkaXNhYmxlJywgJChzZWxmLm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTGlua1NlbGVjdG9yLCAkKFwiZGl2Lm1vZHVsZS1pdGVtLWxpc3RbZGF0YS10ZWNoLW5hbWU9J1wiICsgJCh0aGlzKS5hdHRyKFwiZGF0YS10ZWNoLW5hbWVcIikgKyBcIiddXCIpKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbFJlc2V0TGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcigncmVzZXQnLCAkKHNlbGYubW9kdWxlQWN0aW9uTWVudVJlc2V0TGlua1NlbGVjdG9yLCAkKFwiZGl2Lm1vZHVsZS1pdGVtLWxpc3RbZGF0YS10ZWNoLW5hbWU9J1wiICsgJCh0aGlzKS5hdHRyKFwiZGF0YS10ZWNoLW5hbWVcIikgKyBcIiddXCIpKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbFVuaW5zdGFsbExpbmtTZWxlY3RvciwgZnVuY3Rpb24gKGUpIHtcbiAgICAgICQoZS50YXJnZXQpLnBhcmVudHMoJy5tb2RhbCcpLm9uKCdoaWRkZW4uYnMubW9kYWwnLCBmdW5jdGlvbihldmVudCkge1xuICAgICAgICByZXR1cm4gc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcihcbiAgICAgICAgICAndW5pbnN0YWxsJyxcbiAgICAgICAgICAkKFxuICAgICAgICAgICAgc2VsZi5tb2R1bGVBY3Rpb25NZW51VW5pbnN0YWxsTGlua1NlbGVjdG9yLFxuICAgICAgICAgICAgJChcImRpdi5tb2R1bGUtaXRlbS1saXN0W2RhdGEtdGVjaC1uYW1lPSdcIiArICQoZS50YXJnZXQpLmF0dHIoXCJkYXRhLXRlY2gtbmFtZVwiKSArIFwiJ11cIilcbiAgICAgICAgICApLFxuICAgICAgICAgICQoZS50YXJnZXQpLmF0dHIoXCJkYXRhLWRlbGV0aW9uXCIpXG4gICAgICAgICk7XG4gICAgICB9LmJpbmQoZSkpO1xuICAgIH0pO1xuICB9O1xuXG4gIF9nZXRNb2R1bGVJdGVtU2VsZWN0b3IoKSB7XG4gICAgaWYgKCQodGhpcy5tb2R1bGVJdGVtTGlzdFNlbGVjdG9yKS5sZW5ndGgpIHtcbiAgICAgIHJldHVybiB0aGlzLm1vZHVsZUl0ZW1MaXN0U2VsZWN0b3I7XG4gICAgfSBlbHNlIHtcbiAgICAgIHJldHVybiB0aGlzLm1vZHVsZUl0ZW1HcmlkU2VsZWN0b3I7XG4gICAgfVxuICB9O1xuXG4gIF9jb25maXJtQWN0aW9uKGFjdGlvbiwgZWxlbWVudCkge1xuICAgIHZhciBtb2RhbCA9ICQoJyMnICsgJChlbGVtZW50KS5kYXRhKCdjb25maXJtX21vZGFsJykpO1xuICAgIGlmIChtb2RhbC5sZW5ndGggIT0gMSkge1xuICAgICAgcmV0dXJuIHRydWU7XG4gICAgfVxuICAgIG1vZGFsLmZpcnN0KCkubW9kYWwoJ3Nob3cnKTtcblxuICAgIHJldHVybiBmYWxzZTsgLy8gZG8gbm90IGFsbG93IGEuaHJlZiB0byByZWxvYWQgdGhlIHBhZ2UuIFRoZSBjb25maXJtIG1vZGFsIGRpYWxvZyB3aWxsIGRvIGl0IGFzeW5jIGlmIG5lZWRlZC5cbiAgfTtcblxuICAvKipcbiAgICogVXBkYXRlIHRoZSBjb250ZW50IG9mIGEgbW9kYWwgYXNraW5nIGEgY29uZmlybWF0aW9uIGZvciBQcmVzdGFUcnVzdCBhbmQgb3BlbiBpdFxuICAgKlxuICAgKiBAcGFyYW0ge2FycmF5fSByZXN1bHQgY29udGFpbmluZyBtb2R1bGUgZGF0YVxuICAgKiBAcmV0dXJuIHt2b2lkfVxuICAgKi9cbiAgX2NvbmZpcm1QcmVzdGFUcnVzdChyZXN1bHQpIHtcbiAgICB2YXIgdGhhdCA9IHRoaXM7XG4gICAgdmFyIG1vZGFsID0gdGhpcy5fcmVwbGFjZVByZXN0YVRydXN0UGxhY2Vob2xkZXJzKHJlc3VsdCk7XG5cbiAgICBtb2RhbC5maW5kKFwiLnBzdHJ1c3QtaW5zdGFsbFwiKS5vZmYoJ2NsaWNrJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAvLyBGaW5kIHJlbGF0ZWQgZm9ybSwgdXBkYXRlIGl0IGFuZCBzdWJtaXQgaXRcbiAgICAgIHZhciBpbnN0YWxsX2J1dHRvbiA9ICQodGhhdC5tb2R1bGVBY3Rpb25NZW51SW5zdGFsbExpbmtTZWxlY3RvciwgJy5tb2R1bGUtaXRlbVtkYXRhLXRlY2gtbmFtZT1cIicgKyByZXN1bHQubW9kdWxlLmF0dHJpYnV0ZXMubmFtZSArICdcIl0nKTtcbiAgICAgIHZhciBmb3JtID0gaW5zdGFsbF9idXR0b24ucGFyZW50KFwiZm9ybVwiKTtcbiAgICAgICQoJzxpbnB1dD4nKS5hdHRyKHtcbiAgICAgICAgdHlwZTogJ2hpZGRlbicsXG4gICAgICAgIHZhbHVlOiAnMScsXG4gICAgICAgIG5hbWU6ICdhY3Rpb25QYXJhbXNbY29uZmlybVByZXN0YVRydXN0XSdcbiAgICAgIH0pLmFwcGVuZFRvKGZvcm0pO1xuXG4gICAgICBpbnN0YWxsX2J1dHRvbi5jbGljaygpO1xuICAgICAgbW9kYWwubW9kYWwoJ2hpZGUnKTtcbiAgICB9KTtcblxuICAgIG1vZGFsLm1vZGFsKCk7XG4gIH07XG5cbiAgX3JlcGxhY2VQcmVzdGFUcnVzdFBsYWNlaG9sZGVycyhyZXN1bHQpIHtcbiAgICB2YXIgbW9kYWwgPSAkKFwiI21vZGFsLXByZXN0YXRydXN0XCIpO1xuICAgIHZhciBtb2R1bGUgPSByZXN1bHQubW9kdWxlLmF0dHJpYnV0ZXM7XG5cbiAgICBpZiAocmVzdWx0LmNvbmZpcm1hdGlvbl9zdWJqZWN0ICE9PSAnUHJlc3RhVHJ1c3QnIHx8ICFtb2RhbC5sZW5ndGgpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB2YXIgYWxlcnRDbGFzcyA9IG1vZHVsZS5wcmVzdGF0cnVzdC5zdGF0dXMgPyAnc3VjY2VzcycgOiAnd2FybmluZyc7XG5cbiAgICBpZiAobW9kdWxlLnByZXN0YXRydXN0LmNoZWNrX2xpc3QucHJvcGVydHkpIHtcbiAgICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1idG4tcHJvcGVydHktb2tcIikuc2hvdygpO1xuICAgICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWJ0bi1wcm9wZXJ0eS1ub2tcIikuaGlkZSgpO1xuICAgIH0gZWxzZSB7XG4gICAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYnRuLXByb3BlcnR5LW9rXCIpLmhpZGUoKTtcbiAgICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1idG4tcHJvcGVydHktbm9rXCIpLnNob3coKTtcbiAgICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1idXlcIikuYXR0cihcImhyZWZcIiwgbW9kdWxlLnVybCkudG9nZ2xlKG1vZHVsZS51cmwgIT09IG51bGwpO1xuICAgIH1cblxuICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1pbWdcIikuYXR0cih7c3JjOiBtb2R1bGUuaW1nLCBhbHQ6IG1vZHVsZS5uYW1lfSk7XG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LW5hbWVcIikudGV4dChtb2R1bGUuZGlzcGxheU5hbWUpO1xuICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1hdXRob3JcIikudGV4dChtb2R1bGUuYXV0aG9yKTtcbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtbGFiZWxcIikuYXR0cihcImNsYXNzXCIsIFwidGV4dC1cIiArIGFsZXJ0Q2xhc3MpLnRleHQobW9kdWxlLnByZXN0YXRydXN0LnN0YXR1cyA/ICdPSycgOiAnS08nKTtcbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtbWVzc2FnZVwiKS5hdHRyKFwiY2xhc3NcIiwgXCJhbGVydCBhbGVydC1cIithbGVydENsYXNzKTtcbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtbWVzc2FnZSA+IHBcIikudGV4dChtb2R1bGUucHJlc3RhdHJ1c3QubWVzc2FnZSk7XG5cbiAgICByZXR1cm4gbW9kYWw7XG4gIH1cblxuICBfZGlzcGF0Y2hQcmVFdmVudChhY3Rpb24sIGVsZW1lbnQpIHtcbiAgICB2YXIgZXZlbnQgPSBqUXVlcnkuRXZlbnQoJ21vZHVsZV9jYXJkX2FjdGlvbl9ldmVudCcpO1xuXG4gICAgJChlbGVtZW50KS50cmlnZ2VyKGV2ZW50LCBbYWN0aW9uXSk7XG4gICAgaWYgKGV2ZW50LmlzUHJvcGFnYXRpb25TdG9wcGVkKCkgIT09IGZhbHNlIHx8IGV2ZW50LmlzSW1tZWRpYXRlUHJvcGFnYXRpb25TdG9wcGVkKCkgIT09IGZhbHNlKSB7XG4gICAgICByZXR1cm4gZmFsc2U7IC8vIGlmIGFsbCBoYW5kbGVycyBoYXZlIG5vdCBiZWVuIGNhbGxlZCwgdGhlbiBzdG9wIHByb3BhZ2F0aW9uIG9mIHRoZSBjbGljayBldmVudC5cbiAgICB9XG5cbiAgICByZXR1cm4gKGV2ZW50LnJlc3VsdCAhPT0gZmFsc2UpOyAvLyBleHBsaWNpdCBmYWxzZSBtdXN0IGJlIHNldCBmcm9tIGhhbmRsZXJzIHRvIHN0b3AgcHJvcGFnYXRpb24gb2YgdGhlIGNsaWNrIGV2ZW50LlxuICB9O1xuXG4gIF9yZXF1ZXN0VG9Db250cm9sbGVyKGFjdGlvbiwgZWxlbWVudCwgZm9yY2VEZWxldGlvbiwgZGlzYWJsZUNhY2hlQ2xlYXIsIGNhbGxiYWNrKSB7XG4gICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgIHZhciBqcUVsZW1lbnRPYmogPSBlbGVtZW50LmNsb3Nlc3QodGhpcy5tb2R1bGVJdGVtQWN0aW9uc1NlbGVjdG9yKTtcbiAgICB2YXIgZm9ybSA9IGVsZW1lbnQuY2xvc2VzdChcImZvcm1cIik7XG4gICAgdmFyIHNwaW5uZXJPYmogPSAkKFwiPGJ1dHRvbiBjbGFzcz1cXFwiYnRuLXByaW1hcnktcmV2ZXJzZSBvbmNsaWNrIHVuYmluZCBzcGlubmVyIFxcXCI+PC9idXR0b24+XCIpO1xuICAgIHZhciB1cmwgPSBcIi8vXCIgKyB3aW5kb3cubG9jYXRpb24uaG9zdCArIGZvcm0uYXR0cihcImFjdGlvblwiKTtcbiAgICB2YXIgYWN0aW9uUGFyYW1zID0gZm9ybS5zZXJpYWxpemVBcnJheSgpO1xuXG4gICAgaWYgKGZvcmNlRGVsZXRpb24gPT09IFwidHJ1ZVwiIHx8IGZvcmNlRGVsZXRpb24gPT09IHRydWUpIHtcbiAgICAgIGFjdGlvblBhcmFtcy5wdXNoKHtuYW1lOiBcImFjdGlvblBhcmFtc1tkZWxldGlvbl1cIiwgdmFsdWU6IHRydWV9KTtcbiAgICB9XG4gICAgaWYgKGRpc2FibGVDYWNoZUNsZWFyID09PSBcInRydWVcIiB8fCBkaXNhYmxlQ2FjaGVDbGVhciA9PT0gdHJ1ZSkge1xuICAgICAgYWN0aW9uUGFyYW1zLnB1c2goe25hbWU6IFwiYWN0aW9uUGFyYW1zW2NhY2hlQ2xlYXJFbmFibGVkXVwiLCB2YWx1ZTogMH0pO1xuICAgIH1cblxuICAgICQuYWpheCh7XG4gICAgICB1cmw6IHVybCxcbiAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICAgIGRhdGE6IGFjdGlvblBhcmFtcyxcbiAgICAgIGJlZm9yZVNlbmQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAganFFbGVtZW50T2JqLmhpZGUoKTtcbiAgICAgICAganFFbGVtZW50T2JqLmFmdGVyKHNwaW5uZXJPYmopO1xuICAgICAgfVxuICAgIH0pLmRvbmUoZnVuY3Rpb24gKHJlc3VsdCkge1xuICAgICAgaWYgKHR5cGVvZiByZXN1bHQgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAkLmdyb3dsLmVycm9yKHttZXNzYWdlOiBcIk5vIGFuc3dlciByZWNlaXZlZCBmcm9tIHNlcnZlclwifSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB2YXIgbW9kdWxlVGVjaE5hbWUgPSBPYmplY3Qua2V5cyhyZXN1bHQpWzBdO1xuXG4gICAgICAgIGlmIChyZXN1bHRbbW9kdWxlVGVjaE5hbWVdLnN0YXR1cyA9PT0gZmFsc2UpIHtcbiAgICAgICAgICBpZiAodHlwZW9mIHJlc3VsdFttb2R1bGVUZWNoTmFtZV0uY29uZmlybWF0aW9uX3N1YmplY3QgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICBzZWxmLl9jb25maXJtUHJlc3RhVHJ1c3QocmVzdWx0W21vZHVsZVRlY2hOYW1lXSk7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogcmVzdWx0W21vZHVsZVRlY2hOYW1lXS5tc2d9KTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAkLmdyb3dsLm5vdGljZSh7bWVzc2FnZTogcmVzdWx0W21vZHVsZVRlY2hOYW1lXS5tc2d9KTtcblxuICAgICAgICAgIHZhciBhbHRlcmVkU2VsZWN0b3IgPSBzZWxmLl9nZXRNb2R1bGVJdGVtU2VsZWN0b3IoKS5yZXBsYWNlKCcuJywgJycpO1xuICAgICAgICAgIHZhciBtYWluRWxlbWVudCA9IG51bGw7XG5cbiAgICAgICAgICBpZiAoYWN0aW9uID09IFwidW5pbnN0YWxsXCIpIHtcbiAgICAgICAgICAgIG1haW5FbGVtZW50ID0ganFFbGVtZW50T2JqLmNsb3Nlc3QoJy4nICsgYWx0ZXJlZFNlbGVjdG9yKTtcbiAgICAgICAgICAgIG1haW5FbGVtZW50LnJlbW92ZSgpO1xuXG4gICAgICAgICAgICBCT0V2ZW50LmVtaXRFdmVudChcIk1vZHVsZSBVbmluc3RhbGxlZFwiLCBcIkN1c3RvbUV2ZW50XCIpO1xuICAgICAgICAgIH0gZWxzZSBpZiAoYWN0aW9uID09IFwiZGlzYWJsZVwiKSB7XG4gICAgICAgICAgICBtYWluRWxlbWVudCA9IGpxRWxlbWVudE9iai5jbG9zZXN0KCcuJyArIGFsdGVyZWRTZWxlY3Rvcik7XG4gICAgICAgICAgICBtYWluRWxlbWVudC5hZGRDbGFzcyhhbHRlcmVkU2VsZWN0b3IgKyAnLWlzTm90QWN0aXZlJyk7XG4gICAgICAgICAgICBtYWluRWxlbWVudC5hdHRyKCdkYXRhLWFjdGl2ZScsICcwJyk7XG5cbiAgICAgICAgICAgIEJPRXZlbnQuZW1pdEV2ZW50KFwiTW9kdWxlIERpc2FibGVkXCIsIFwiQ3VzdG9tRXZlbnRcIik7XG4gICAgICAgICAgfSBlbHNlIGlmIChhY3Rpb24gPT0gXCJlbmFibGVcIikge1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQgPSBqcUVsZW1lbnRPYmouY2xvc2VzdCgnLicgKyBhbHRlcmVkU2VsZWN0b3IpO1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQucmVtb3ZlQ2xhc3MoYWx0ZXJlZFNlbGVjdG9yICsgJy1pc05vdEFjdGl2ZScpO1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQuYXR0cignZGF0YS1hY3RpdmUnLCAnMScpO1xuXG4gICAgICAgICAgICBCT0V2ZW50LmVtaXRFdmVudChcIk1vZHVsZSBFbmFibGVkXCIsIFwiQ3VzdG9tRXZlbnRcIik7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAganFFbGVtZW50T2JqLnJlcGxhY2VXaXRoKHJlc3VsdFttb2R1bGVUZWNoTmFtZV0uYWN0aW9uX21lbnVfaHRtbCk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9KS5mYWlsKGZ1bmN0aW9uKCkge1xuICAgICAgY29uc3QgbW9kdWxlSXRlbSA9IGpxRWxlbWVudE9iai5jbG9zZXN0KCdtb2R1bGUtaXRlbS1saXN0Jyk7XG4gICAgICBjb25zdCB0ZWNoTmFtZSA9IG1vZHVsZUl0ZW0uZGF0YSgndGVjaE5hbWUnKTtcbiAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IFwiQ291bGQgbm90IHBlcmZvcm0gYWN0aW9uIFwiK2FjdGlvbitcIiBmb3IgbW9kdWxlIFwiK3RlY2hOYW1lfSk7XG4gICAgfSkuYWx3YXlzKGZ1bmN0aW9uICgpIHtcbiAgICAgIGpxRWxlbWVudE9iai5mYWRlSW4oKTtcbiAgICAgIHNwaW5uZXJPYmoucmVtb3ZlKCk7XG4gICAgICBpZiAoY2FsbGJhY2spIHtcbiAgICAgICAgY2FsbGJhY2soKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgIHJldHVybiBmYWxzZTtcbiAgfTtcbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvbW9kdWxlLWNhcmQuanMiXSwic291cmNlUm9vdCI6IiJ9