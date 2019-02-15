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
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/pages/module/index.js");
/******/ })
/************************************************************************/
/******/ ({

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

/***/ "./js/pages/module/controller.js":
/*!***************************************!*\
  !*** ./js/pages/module/controller.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
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
/**
 * Module Admin Page Controller.
 * @constructor
 */

var AdminModuleController =
/*#__PURE__*/
function () {
  /**
   * Initialize all listeners and bind everything
   * @method init
   * @memberof AdminModule
   */
  function AdminModuleController(moduleCardController) {
    _classCallCheck(this, AdminModuleController);

    this.moduleCardController = moduleCardController;
    this.DEFAULT_MAX_RECENTLY_USED = 10;
    this.DEFAULT_MAX_PER_CATEGORIES = 6;
    this.DISPLAY_GRID = 'grid';
    this.DISPLAY_LIST = 'list';
    this.CATEGORY_RECENTLY_USED = 'recently-used';
    this.currentCategoryDisplay = {};
    this.currentDisplay = '';
    this.isCategoryGridDisplayed = false;
    this.currentTagsList = [];
    this.currentRefCategory = null;
    this.currentRefStatus = null;
    this.currentSorting = null;
    this.baseAddonsUrl = 'https://addons.prestashop.com/';
    this.pstaggerInput = null;
    this.lastBulkAction = null;
    this.isUploadStarted = false;
    this.recentlyUsedSelector = '#module-recently-used-list .modules-list';
    /**
     * Loaded modules list.
     * Containing the card and list display.
     * @type {Array}
     */

    this.modulesList = [];
    this.addonsCardGrid = null;
    this.addonsCardList = null;
    this.moduleShortList = '.module-short-list'; // See more & See less selector

    this.seeMoreSelector = '.see-more';
    this.seeLessSelector = '.see-less'; // Selectors into vars to make it easier to change them while keeping same code logic

    this.moduleItemGridSelector = '.module-item-grid';
    this.moduleItemListSelector = '.module-item-list';
    this.categorySelectorLabelSelector = '.module-category-selector-label';
    this.categorySelector = '.module-category-selector';
    this.categoryItemSelector = '.module-category-menu';
    this.addonsLoginButtonSelector = '#addons_login_btn';
    this.categoryResetBtnSelector = '.module-category-reset';
    this.moduleInstallBtnSelector = 'input.module-install-btn';
    this.moduleSortingDropdownSelector = '.module-sorting-author select';
    this.categoryGridSelector = '#modules-categories-grid';
    this.categoryGridItemSelector = '.module-category-item';
    this.addonItemGridSelector = '.module-addons-item-grid';
    this.addonItemListSelector = '.module-addons-item-list'; // Upgrade All selectors

    this.upgradeAllSource = '.module_action_menu_upgrade_all';
    this.upgradeAllTargets = '#modules-list-container-update .module_action_menu_upgrade:visible'; // Bulk action selectors

    this.bulkActionDropDownSelector = '.module-bulk-actions';
    this.bulkItemSelector = '.module-bulk-menu';
    this.bulkActionCheckboxListSelector = '.module-checkbox-bulk-list input';
    this.bulkActionCheckboxGridSelector = '.module-checkbox-bulk-grid input';
    this.checkedBulkActionListSelector = "".concat(this.bulkActionCheckboxListSelector, ":checked");
    this.checkedBulkActionGridSelector = "".concat(this.bulkActionCheckboxGridSelector, ":checked");
    this.bulkActionCheckboxSelector = '#module-modal-bulk-checkbox';
    this.bulkConfirmModalSelector = '#module-modal-bulk-confirm';
    this.bulkConfirmModalActionNameSelector = '#module-modal-bulk-confirm-action-name';
    this.bulkConfirmModalListSelector = '#module-modal-bulk-confirm-list';
    this.bulkConfirmModalAckBtnSelector = '#module-modal-confirm-bulk-ack'; // Placeholders

    this.placeholderGlobalSelector = '.module-placeholders-wrapper';
    this.placeholderFailureGlobalSelector = '.module-placeholders-failure';
    this.placeholderFailureMsgSelector = '.module-placeholders-failure-msg';
    this.placeholderFailureRetryBtnSelector = '#module-placeholders-failure-retry'; // Module's statuses selectors

    this.statusSelectorLabelSelector = '.module-status-selector-label';
    this.statusItemSelector = '.module-status-menu';
    this.statusResetBtnSelector = '.module-status-reset'; // Selectors for Module Import and Addons connect

    this.addonsConnectModalBtnSelector = '#page-header-desc-configuration-addons_connect';
    this.addonsLogoutModalBtnSelector = '#page-header-desc-configuration-addons_logout';
    this.addonsImportModalBtnSelector = '#page-header-desc-configuration-add_module';
    this.dropZoneModalSelector = '#module-modal-import';
    this.dropZoneModalFooterSelector = '#module-modal-import .modal-footer';
    this.dropZoneImportZoneSelector = '#importDropzone';
    this.addonsConnectModalSelector = '#module-modal-addons-connect';
    this.addonsLogoutModalSelector = '#module-modal-addons-logout';
    this.addonsConnectForm = '#addons-connect-form';
    this.moduleImportModalCloseBtn = '#module-modal-import-closing-cross';
    this.moduleImportStartSelector = '.module-import-start';
    this.moduleImportProcessingSelector = '.module-import-processing';
    this.moduleImportSuccessSelector = '.module-import-success';
    this.moduleImportSuccessConfigureBtnSelector = '.module-import-success-configure';
    this.moduleImportFailureSelector = '.module-import-failure';
    this.moduleImportFailureRetrySelector = '.module-import-failure-retry';
    this.moduleImportFailureDetailsBtnSelector = '.module-import-failure-details-action';
    this.moduleImportSelectFileManualSelector = '.module-import-start-select-manual';
    this.moduleImportFailureMsgDetailsSelector = '.module-import-failure-details';
    this.moduleImportConfirmSelector = '.module-import-confirm';
    this.initSortingDropdown();
    this.initBOEventRegistering();
    this.initCurrentDisplay();
    this.initSortingDisplaySwitch();
    this.initBulkDropdown();
    this.initSearchBlock();
    this.initCategorySelect();
    this.initCategoriesGrid();
    this.initActionButtons();
    this.initAddonsSearch();
    this.initAddonsConnect();
    this.initAddModuleAction();
    this.initDropzone();
    this.initPageChangeProtection();
    this.initPlaceholderMechanism();
    this.initFilterStatusDropdown();
    this.fetchModulesList();
    this.getNotificationsCount();
    this.initializeSeeMore();
  }

  _createClass(AdminModuleController, [{
    key: "initFilterStatusDropdown",
    value: function initFilterStatusDropdown() {
      var self = this;
      var body = $('body');
      body.on('click', self.statusItemSelector, function () {
        // Get data from li DOM input
        self.currentRefStatus = parseInt($(this).data('status-ref'), 10); // Change dropdown label to set it to the current status' displayname

        $(self.statusSelectorLabelSelector).text($(this).find('a:first').text());
        $(self.statusResetBtnSelector).show();
        self.updateModuleVisibility();
      });
      body.on('click', self.statusResetBtnSelector, function () {
        $(self.statusSelectorLabelSelector).text($(this).find('a').text());
        $(this).hide();
        self.currentRefStatus = null;
        self.updateModuleVisibility();
      });
    }
  }, {
    key: "initBulkDropdown",
    value: function initBulkDropdown() {
      var self = this;
      var body = $('body');
      body.on('click', self.getBulkCheckboxesSelector(), function () {
        var selector = $(self.bulkActionDropDownSelector);

        if ($(self.getBulkCheckboxesCheckedSelector()).length > 0) {
          selector.closest('.module-top-menu-item').removeClass('disabled');
        } else {
          selector.closest('.module-top-menu-item').addClass('disabled');
        }
      });
      body.on('click', self.bulkItemSelector, function initializeBodyChange() {
        if ($(self.getBulkCheckboxesCheckedSelector()).length === 0) {
          $.growl.warning({
            message: window.translate_javascripts['Bulk Action - One module minimum']
          });
          return;
        }

        self.lastBulkAction = $(this).data('ref');
        var modulesListString = self.buildBulkActionModuleList();
        var actionString = $(this).find(':checked').text().toLowerCase();
        $(self.bulkConfirmModalListSelector).html(modulesListString);
        $(self.bulkConfirmModalActionNameSelector).text(actionString);

        if (self.lastBulkAction === 'bulk-uninstall') {
          $(self.bulkActionCheckboxSelector).show();
        } else {
          $(self.bulkActionCheckboxSelector).hide();
        }

        $(self.bulkConfirmModalSelector).modal('show');
      });
      body.on('click', this.bulkConfirmModalAckBtnSelector, function (event) {
        event.preventDefault();
        event.stopPropagation();
        $(self.bulkConfirmModalSelector).modal('hide');
        self.doBulkAction(self.lastBulkAction);
      });
    }
  }, {
    key: "initBOEventRegistering",
    value: function initBOEventRegistering() {
      window.BOEvent.on('Module Disabled', this.onModuleDisabled, this);
      window.BOEvent.on('Module Uninstalled', this.updateTotalResults, this);
    }
  }, {
    key: "onModuleDisabled",
    value: function onModuleDisabled() {
      var self = this;
      var moduleItemSelector = self.getModuleItemSelector();
      $('.modules-list').each(function scanModulesList() {
        self.updateTotalResults();
      });
    }
  }, {
    key: "initPlaceholderMechanism",
    value: function initPlaceholderMechanism() {
      var self = this;

      if ($(self.placeholderGlobalSelector).length) {
        self.ajaxLoadPage();
      } // Retry loading mechanism


      $('body').on('click', self.placeholderFailureRetryBtnSelector, function () {
        $(self.placeholderFailureGlobalSelector).fadeOut();
        $(self.placeholderGlobalSelector).fadeIn();
        self.ajaxLoadPage();
      });
    }
  }, {
    key: "ajaxLoadPage",
    value: function ajaxLoadPage() {
      var self = this;
      $.ajax({
        method: 'GET',
        url: window.moduleURLs.catalogRefresh
      }).done(function (response) {
        if (response.status === true) {
          if (typeof response.domElements === 'undefined') response.domElements = null;
          if (typeof response.msg === 'undefined') response.msg = null;
          var stylesheet = document.styleSheets[0];
          var stylesheetRule = '{display: none}';
          var moduleGlobalSelector = '.modules-list';
          var moduleSortingSelector = '.module-sorting-menu';
          var requiredSelectorCombination = "".concat(moduleGlobalSelector, ",").concat(moduleSortingSelector);

          if (stylesheet.insertRule) {
            stylesheet.insertRule(requiredSelectorCombination + stylesheetRule, stylesheet.cssRules.length);
          } else if (stylesheet.addRule) {
            stylesheet.addRule(requiredSelectorCombination, stylesheetRule, -1);
          }

          $(self.placeholderGlobalSelector).fadeOut(800, function () {
            $.each(response.domElements, function (index, element) {
              $(element.selector).append(element.content);
            });
            $(moduleGlobalSelector).fadeIn(800).css('display', 'flex');
            $(moduleSortingSelector).fadeIn(800);
            $('[data-toggle="popover"]').popover();
            self.initCurrentDisplay();
            self.fetchModulesList();
          });
        } else {
          $(self.placeholderGlobalSelector).fadeOut(800, function () {
            $(self.placeholderFailureMsgSelector).text(response.msg);
            $(self.placeholderFailureGlobalSelector).fadeIn(800);
          });
        }
      }).fail(function (response) {
        $(self.placeholderGlobalSelector).fadeOut(800, function () {
          $(self.placeholderFailureMsgSelector).text(response.statusText);
          $(self.placeholderFailureGlobalSelector).fadeIn(800);
        });
      });
    }
  }, {
    key: "fetchModulesList",
    value: function fetchModulesList() {
      var self = this;
      var container;
      var $this;
      self.modulesList = [];
      $('.modules-list').each(function prepareContainer() {
        container = $(this);
        container.find('.module-item').each(function prepareModules() {
          $this = $(this);
          self.modulesList.push({
            domObject: $this,
            id: $this.data('id'),
            name: $this.data('name').toLowerCase(),
            scoring: parseFloat($this.data('scoring')),
            logo: $this.data('logo'),
            author: $this.data('author').toLowerCase(),
            version: $this.data('version'),
            description: $this.data('description').toLowerCase(),
            techName: $this.data('tech-name').toLowerCase(),
            childCategories: $this.data('child-categories'),
            categories: String($this.data('categories')).toLowerCase(),
            type: $this.data('type'),
            price: parseFloat($this.data('price')),
            active: parseInt($this.data('active'), 10),
            access: $this.data('last-access'),
            display: $this.hasClass('module-item-list') ? self.DISPLAY_LIST : self.DISPLAY_GRID,
            container: container
          });
          $this.remove();
        });
      });
      self.addonsCardGrid = $(this.addonItemGridSelector);
      self.addonsCardList = $(this.addonItemListSelector);
      self.updateModuleVisibility();
      $('body').trigger('moduleCatalogLoaded');
    }
    /**
     * Prepare sorting
     *
     */

  }, {
    key: "updateModuleSorting",
    value: function updateModuleSorting() {
      var self = this;

      if (!self.currentSorting) {
        return;
      } // Modules sorting


      var order = 'asc';
      var key = self.currentSorting;
      var splittedKey = key.split('-');

      if (splittedKey.length > 1) {
        key = splittedKey[0];

        if (splittedKey[1] === 'desc') {
          order = 'desc';
        }
      }

      var currentCompare = function currentCompare(a, b) {
        var aData = a[key];
        var bData = b[key];

        if (key === 'access') {
          aData = new Date(aData).getTime();
          bData = new Date(bData).getTime();
          aData = isNaN(aData) ? 0 : aData;
          bData = isNaN(bData) ? 0 : bData;

          if (aData === bData) {
            return b.name.localeCompare(a.name);
          }
        }

        if (aData < bData) return -1;
        if (aData > bData) return 1;
        return 0;
      };

      self.modulesList.sort(currentCompare);

      if (order === 'desc') {
        self.modulesList.reverse();
      }
    }
  }, {
    key: "updateModuleContainerDisplay",
    value: function updateModuleContainerDisplay() {
      var self = this;
      $('.module-short-list').each(function setShortListVisibility() {
        var container = $(this);
        var nbModulesInContainer = container.find('.module-item').length;

        if (self.currentRefCategory && self.currentRefCategory !== String(container.find('.modules-list').data('name')) || self.currentRefStatus !== null && nbModulesInContainer === 0 || nbModulesInContainer === 0 && String(container.find('.modules-list').data('name')) === self.CATEGORY_RECENTLY_USED || self.currentTagsList.length > 0 && nbModulesInContainer === 0) {
          container.hide();
          return;
        }

        container.show();

        if (nbModulesInContainer >= self.DEFAULT_MAX_PER_CATEGORIES) {
          container.find("".concat(self.seeMoreSelector, ", ").concat(self.seeLessSelector)).show();
        } else {
          container.find("".concat(self.seeMoreSelector, ", ").concat(self.seeLessSelector)).hide();
        }
      });
    }
  }, {
    key: "updateModuleVisibility",
    value: function updateModuleVisibility() {
      var self = this;
      self.updateModuleSorting();
      $(self.recentlyUsedSelector).find('.module-item').remove();
      $('.modules-list').find('.module-item').remove(); // Modules visibility management

      var isVisible;
      var currentModule;
      var moduleCategory;
      var tagExists;
      var newValue;
      var modulesListLength = self.modulesList.length;
      var counter = {};

      for (var i = 0; i < modulesListLength; i += 1) {
        currentModule = self.modulesList[i];

        if (currentModule.display === self.currentDisplay) {
          isVisible = true;
          moduleCategory = self.currentRefCategory === self.CATEGORY_RECENTLY_USED ? self.CATEGORY_RECENTLY_USED : currentModule.categories; // Check for same category

          if (self.currentRefCategory !== null) {
            isVisible &= moduleCategory === self.currentRefCategory;
          } // Check for same status


          if (self.currentRefStatus !== null) {
            isVisible &= currentModule.active === self.currentRefStatus;
          } // Check for tag list


          if (self.currentTagsList.length) {
            tagExists = false;
            $.each(self.currentTagsList, function (index, value) {
              newValue = value.toLowerCase();
              tagExists |= currentModule.name.indexOf(newValue) !== -1 || currentModule.description.indexOf(newValue) !== -1 || currentModule.author.indexOf(newValue) !== -1 || currentModule.techName.indexOf(newValue) !== -1;
            });
            isVisible &= tagExists;
          }
          /**
           * If list display without search we must display only the first 5 modules
           */


          if (self.currentDisplay === self.DISPLAY_LIST && !self.currentTagsList.length) {
            if (self.currentCategoryDisplay[moduleCategory] === undefined) {
              self.currentCategoryDisplay[moduleCategory] = false;
            }

            if (!counter[moduleCategory]) {
              counter[moduleCategory] = 0;
            }

            if (moduleCategory === self.CATEGORY_RECENTLY_USED) {
              if (counter[moduleCategory] >= self.DEFAULT_MAX_RECENTLY_USED) {
                isVisible &= self.currentCategoryDisplay[moduleCategory];
              }
            } else if (counter[moduleCategory] >= self.DEFAULT_MAX_PER_CATEGORIES) {
              isVisible &= self.currentCategoryDisplay[moduleCategory];
            }

            counter[moduleCategory] += 1;
          } // If visible, display (Thx captain obvious)


          if (isVisible) {
            if (self.currentRefCategory === self.CATEGORY_RECENTLY_USED) {
              $(self.recentlyUsedSelector).append(currentModule.domObject);
            } else {
              currentModule.container.append(currentModule.domObject);
            }
          }
        }
      }

      self.updateModuleContainerDisplay();

      if (self.currentTagsList.length) {
        $('.modules-list').append(this.currentDisplay === self.DISPLAY_GRID ? this.addonsCardGrid : this.addonsCardList);
      }

      self.updateTotalResults();
    }
  }, {
    key: "initPageChangeProtection",
    value: function initPageChangeProtection() {
      var self = this;
      $(window).on('beforeunload', function () {
        if (self.isUploadStarted === true) {
          return 'It seems some critical operation are running, are you sure you want to change page ? It might cause some unexepcted behaviors.';
        }
      });
    }
  }, {
    key: "buildBulkActionModuleList",
    value: function buildBulkActionModuleList() {
      var checkBoxesSelector = this.getBulkCheckboxesCheckedSelector();
      var moduleItemSelector = this.getModuleItemSelector();
      var alreadyDoneFlag = 0;
      var htmlGenerated = '';
      var currentElement;
      $(checkBoxesSelector).each(function prepareCheckboxes() {
        if (alreadyDoneFlag === 10) {
          // Break each
          htmlGenerated += '- ...';
          return false;
        }

        currentElement = $(this).closest(moduleItemSelector);
        htmlGenerated += "- ".concat(currentElement.data('name'), "<br/>");
        alreadyDoneFlag += 1;
        return true;
      });
      return htmlGenerated;
    }
  }, {
    key: "initAddonsConnect",
    value: function initAddonsConnect() {
      var self = this; // Make addons connect modal ready to be clicked

      if ($(self.addonsConnectModalBtnSelector).attr('href') === '#') {
        $(self.addonsConnectModalBtnSelector).attr('data-toggle', 'modal');
        $(self.addonsConnectModalBtnSelector).attr('data-target', self.addonsConnectModalSelector);
      }

      if ($(self.addonsLogoutModalBtnSelector).attr('href') === '#') {
        $(self.addonsLogoutModalBtnSelector).attr('data-toggle', 'modal');
        $(self.addonsLogoutModalBtnSelector).attr('data-target', self.addonsLogoutModalSelector);
      }

      $('body').on('submit', self.addonsConnectForm, function initializeBodySubmit(event) {
        event.preventDefault();
        event.stopPropagation();
        $.ajax({
          method: 'POST',
          url: $(this).attr('action'),
          dataType: 'json',
          data: $(this).serialize(),
          beforeSend: function beforeSend() {
            $(self.addonsLoginButtonSelector).show();
            $('button.btn[type="submit"]', self.addonsConnectForm).hide();
          }
        }).done(function (response) {
          if (response.success === 1) {
            location.reload();
          } else {
            $.growl.error({
              message: response.message
            });
            $(self.addonsLoginButtonSelector).hide();
            $('button.btn[type="submit"]', self.addonsConnectForm).fadeIn();
          }
        });
      });
    }
  }, {
    key: "initAddModuleAction",
    value: function initAddModuleAction() {
      var self = this;
      var addModuleButton = $(self.addonsImportModalBtnSelector);
      addModuleButton.attr('data-toggle', 'modal');
      addModuleButton.attr('data-target', self.dropZoneModalSelector);
    }
  }, {
    key: "initDropzone",
    value: function initDropzone() {
      var self = this;
      var body = $('body');
      var dropzone = $('.dropzone'); // Reset modal when click on Retry in case of failure

      body.on('click', this.moduleImportFailureRetrySelector, function () {
        $("".concat(self.moduleImportSuccessSelector, ",").concat(self.moduleImportFailureSelector, ",").concat(self.moduleImportProcessingSelector)).fadeOut(function () {
          /**
           * Added timeout for a better render of animation
           * and avoid to have displayed at the same time
           */
          setTimeout(function () {
            $(self.moduleImportStartSelector).fadeIn(function () {
              $(self.moduleImportFailureMsgDetailsSelector).hide();
              $(self.moduleImportSuccessConfigureBtnSelector).hide();
              dropzone.removeAttr('style');
            });
          }, 550);
        });
      }); // Reinit modal on exit, but check if not already processing something

      body.on('hidden.bs.modal', this.dropZoneModalSelector, function () {
        $("".concat(self.moduleImportSuccessSelector, ", ").concat(self.moduleImportFailureSelector)).hide();
        $(self.moduleImportStartSelector).show();
        dropzone.removeAttr('style');
        $(self.moduleImportFailureMsgDetailsSelector).hide();
        $(self.moduleImportSuccessConfigureBtnSelector).hide();
        $(self.dropZoneModalFooterSelector).html('');
        $(self.moduleImportConfirmSelector).hide();
      }); // Change the way Dropzone.js lib handle file input trigger

      body.on('click', ".dropzone:not(".concat(this.moduleImportSelectFileManualSelector, ", ").concat(this.moduleImportSuccessConfigureBtnSelector, ")"), function (event, manualSelect) {
        // if click comes from .module-import-start-select-manual, stop everything
        if (typeof manualSelect === 'undefined') {
          event.stopPropagation();
          event.preventDefault();
        }
      });
      body.on('click', this.moduleImportSelectFileManualSelector, function (event) {
        event.stopPropagation();
        event.preventDefault();
        /**
         * Trigger click on hidden file input, and pass extra data
         * to .dropzone click handler fro it to notice it comes from here
         */

        $('.dz-hidden-input').trigger('click', ['manual_select']);
      }); // Handle modal closure

      body.on('click', this.moduleImportModalCloseBtn, function () {
        if (self.isUploadStarted !== true) {
          $(self.dropZoneModalSelector).modal('hide');
        }
      }); // Fix issue on click configure button

      body.on('click', this.moduleImportSuccessConfigureBtnSelector, function initializeBodyClickOnModuleImport(event) {
        event.stopPropagation();
        event.preventDefault();
        window.location = $(this).attr('href');
      }); // Open failure message details box

      body.on('click', this.moduleImportFailureDetailsBtnSelector, function () {
        $(self.moduleImportFailureMsgDetailsSelector).slideDown();
      }); // @see: dropzone.js

      var dropzoneOptions = {
        url: window.moduleURLs.moduleImport,
        acceptedFiles: '.zip, .tar',
        // The name that will be used to transfer the file
        paramName: 'file_uploaded',
        maxFilesize: 50,
        // can't be greater than 50Mb because it's an addons limitation
        uploadMultiple: false,
        addRemoveLinks: true,
        dictDefaultMessage: '',
        hiddenInputContainer: self.dropZoneImportZoneSelector,

        /**
         * Add unlimited timeout. Otherwise dropzone timeout is 30 seconds
         *  and if a module is long to install, it is not possible to install the module.
         */
        timeout: 0,
        addedfile: function addedfile() {
          self.animateStartUpload();
        },
        processing: function processing() {// Leave it empty since we don't require anything while processing upload
        },
        error: function error(file, message) {
          self.displayOnUploadError(message);
        },
        complete: function complete(file) {
          if (file.status !== 'error') {
            var responseObject = $.parseJSON(file.xhr.response);
            if (typeof responseObject.is_configurable === 'undefined') responseObject.is_configurable = null;
            if (typeof responseObject.module_name === 'undefined') responseObject.module_name = null;
            self.displayOnUploadDone(responseObject);
          } // State that we have finish the process to unlock some actions


          self.isUploadStarted = false;
        }
      };
      dropzone.dropzone($.extend(dropzoneOptions));
    }
  }, {
    key: "animateStartUpload",
    value: function animateStartUpload() {
      var self = this;
      var dropzone = $('.dropzone'); // State that we start module upload

      self.isUploadStarted = true;
      $(self.moduleImportStartSelector).hide(0);
      dropzone.css('border', 'none');
      $(self.moduleImportProcessingSelector).fadeIn();
    }
  }, {
    key: "animateEndUpload",
    value: function animateEndUpload(callback) {
      var self = this;
      $(self.moduleImportProcessingSelector).finish().fadeOut(callback);
    }
    /**
     * Method to call for upload modal, when the ajax call went well.
     *
     * @param object result containing the server response
     */

  }, {
    key: "displayOnUploadDone",
    value: function displayOnUploadDone(result) {
      var self = this;
      self.animateEndUpload(function () {
        if (result.status === true) {
          if (result.is_configurable === true) {
            var configureLink = window.moduleURLs.configurationPage.replace(/:number:/, result.module_name);
            $(self.moduleImportSuccessConfigureBtnSelector).attr('href', configureLink);
            $(self.moduleImportSuccessConfigureBtnSelector).show();
          }

          $(self.moduleImportSuccessSelector).fadeIn();
        } else if (typeof result.confirmation_subject !== 'undefined') {
          self.displayPrestaTrustStep(result);
        } else {
          $(self.moduleImportFailureMsgDetailsSelector).html(result.msg);
          $(self.moduleImportFailureSelector).fadeIn();
        }
      });
    }
    /**
     * Method to call for upload modal, when the ajax call went wrong or when the action requested could not
     * succeed for some reason.
     *
     * @param string message explaining the error.
     */

  }, {
    key: "displayOnUploadError",
    value: function displayOnUploadError(message) {
      var self = this;
      self.animateEndUpload(function () {
        $(self.moduleImportFailureMsgDetailsSelector).html(message);
        $(self.moduleImportFailureSelector).fadeIn();
      });
    }
    /**
     * If PrestaTrust needs to be confirmed, we ask for the confirmation
     * modal content and we display it in the currently displayed one.
     * We also generate the ajax call to trigger once we confirm we want to install
     * the module.
     *
     * @param Previous server response result
     */

  }, {
    key: "displayPrestaTrustStep",
    value: function displayPrestaTrustStep(result) {
      var self = this;

      var modal = self.moduleCardController._replacePrestaTrustPlaceholders(result);

      var moduleName = result.module.attributes.name;
      $(this.moduleImportConfirmSelector).html(modal.find('.modal-body').html()).fadeIn();
      $(this.dropZoneModalFooterSelector).html(modal.find('.modal-footer').html()).fadeIn();
      $(this.dropZoneModalFooterSelector).find('.pstrust-install').off('click').on('click', function () {
        $(self.moduleImportConfirmSelector).hide();
        $(self.dropZoneModalFooterSelector).html('');
        self.animateStartUpload(); // Install ajax call

        $.post(result.module.attributes.urls.install, {
          'actionParams[confirmPrestaTrust]': '1'
        }).done(function (data) {
          self.displayOnUploadDone(data[moduleName]);
        }).fail(function (data) {
          self.displayOnUploadError(data[moduleName]);
        }).always(function () {
          self.isUploadStarted = false;
        });
      });
    }
  }, {
    key: "getBulkCheckboxesSelector",
    value: function getBulkCheckboxesSelector() {
      return this.currentDisplay === this.DISPLAY_GRID ? this.bulkActionCheckboxGridSelector : this.bulkActionCheckboxListSelector;
    }
  }, {
    key: "getBulkCheckboxesCheckedSelector",
    value: function getBulkCheckboxesCheckedSelector() {
      return this.currentDisplay === this.DISPLAY_GRID ? this.checkedBulkActionGridSelector : this.checkedBulkActionListSelector;
    }
  }, {
    key: "getModuleItemSelector",
    value: function getModuleItemSelector() {
      return this.currentDisplay === this.DISPLAY_GRID ? this.moduleItemGridSelector : this.moduleItemListSelector;
    }
    /**
     * Get the module notifications count and displays it as a badge on the notification tab
     * @return void
     */

  }, {
    key: "getNotificationsCount",
    value: function getNotificationsCount() {
      var self = this;
      $.getJSON(window.moduleURLs.notificationsCount, self.updateNotificationsCount).fail(function () {
        console.error('Could not retrieve module notifications count.');
      });
    }
  }, {
    key: "updateNotificationsCount",
    value: function updateNotificationsCount(badge) {
      var destinationTabs = {
        to_configure: $('#subtab-AdminModulesNotifications'),
        to_update: $('#subtab-AdminModulesUpdates')
      };

      for (var key in destinationTabs) {
        if (destinationTabs[key].length === 0) {
          continue;
        }

        destinationTabs[key].find('.notification-counter').text(badge[key]);
      }
    }
  }, {
    key: "initAddonsSearch",
    value: function initAddonsSearch() {
      var self = this;
      $('body').on('click', "".concat(self.addonItemGridSelector, ", ").concat(self.addonItemListSelector), function () {
        var searchQuery = '';

        if (self.currentTagsList.length) {
          searchQuery = encodeURIComponent(self.currentTagsList.join(' '));
        }

        window.open("".concat(self.baseAddonsUrl, "search.php?search_query=").concat(searchQuery), '_blank');
      });
    }
  }, {
    key: "initCategoriesGrid",
    value: function initCategoriesGrid() {
      var self = this;
      $('body').on('click', this.categoryGridItemSelector, function initilaizeGridBodyClick(event) {
        event.stopPropagation();
        event.preventDefault();
        var refCategory = $(this).data('category-ref'); // In case we have some tags we need to reset it !

        if (self.currentTagsList.length) {
          self.pstaggerInput.resetTags(false);
          self.currentTagsList = [];
        }

        var menuCategoryToTrigger = $("".concat(self.categoryItemSelector, "[data-category-ref=\"").concat(refCategory, "\"]"));

        if (!menuCategoryToTrigger.length) {
          console.warn("No category with ref (".concat(refCategory, ") seems to exist!"));
          return false;
        } // Hide current category grid


        if (self.isCategoryGridDisplayed === true) {
          $(self.categoryGridSelector).fadeOut();
          self.isCategoryGridDisplayed = false;
        } // Trigger click on right category


        $("".concat(self.categoryItemSelector, "[data-category-ref=\"").concat(refCategory, "\"]")).click();
        return true;
      });
    }
  }, {
    key: "initCurrentDisplay",
    value: function initCurrentDisplay() {
      this.currentDisplay = this.currentDisplay === '' ? this.DISPLAY_LIST : this.DISPLAY_GRID;
    }
  }, {
    key: "initSortingDropdown",
    value: function initSortingDropdown() {
      var self = this;
      self.currentSorting = $(this.moduleSortingDropdownSelector).find(':checked').attr('value');

      if (!self.currentSorting) {
        self.currentSorting = 'access-desc';
      }

      $('body').on('change', self.moduleSortingDropdownSelector, function initializeBodySortingChange() {
        self.currentSorting = $(this).find(':checked').attr('value');
        self.updateModuleVisibility();
      });
    }
  }, {
    key: "doBulkAction",
    value: function doBulkAction(requestedBulkAction) {
      // This object is used to check if requested bulkAction is available and give proper
      // url segment to be called for it
      var forceDeletion = $('#force_bulk_deletion').prop('checked');
      var bulkActionToUrl = {
        'bulk-uninstall': 'uninstall',
        'bulk-disable': 'disable',
        'bulk-enable': 'enable',
        'bulk-disable-mobile': 'disable_mobile',
        'bulk-enable-mobile': 'enable_mobile',
        'bulk-reset': 'reset'
      }; // Note no grid selector used yet since we do not needed it at dev time
      // Maybe useful to implement this kind of things later if intended to
      // use this functionality elsewhere but "manage my module" section

      if (typeof bulkActionToUrl[requestedBulkAction] === 'undefined') {
        $.growl.error({
          message: window.translate_javascripts['Bulk Action - Request not found'].replace('[1]', requestedBulkAction)
        });
        return false;
      } // Loop over all checked bulk checkboxes


      var bulkActionSelectedSelector = this.getBulkCheckboxesCheckedSelector();
      var bulkModuleAction = bulkActionToUrl[requestedBulkAction];

      if ($(bulkActionSelectedSelector).length <= 0) {
        console.warn(window.translate_javascripts['Bulk Action - One module minimum']);
        return false;
      }

      var modulesActions = [];
      var moduleTechName;
      $(bulkActionSelectedSelector).each(function bulkActionSelector() {
        moduleTechName = $(this).data('tech-name');
        modulesActions.push({
          techName: moduleTechName,
          actionMenuObj: $(this).closest('.module-checkbox-bulk-list').next()
        });
      });
      this.performModulesAction(modulesActions, bulkModuleAction, forceDeletion);
      return true;
    }
  }, {
    key: "performModulesAction",
    value: function performModulesAction(modulesActions, bulkModuleAction, forceDeletion) {
      var self = this;

      if (typeof self.moduleCardController === 'undefined') {
        return;
      } //First let's filter modules that can't perform this action


      var actionMenuLinks = filterAllowedActions(modulesActions);

      if (!actionMenuLinks.length) {
        return;
      }

      var modulesRequestedCountdown = actionMenuLinks.length - 1;
      var spinnerObj = $("<button class=\"btn-primary-reverse onclick unbind spinner \"></button>");

      if (actionMenuLinks.length > 1) {
        //Loop through all the modules except the last one which waits for other
        //requests and then call its request with cache clear enabled
        $.each(actionMenuLinks, function bulkModulesLoop(index, actionMenuLink) {
          if (index >= actionMenuLinks.length - 1) {
            return;
          }

          requestModuleAction(actionMenuLink, true, countdownModulesRequest);
        }); //Display a spinner for the last module

        var lastMenuLink = actionMenuLinks[actionMenuLinks.length - 1];
        var actionMenuObj = lastMenuLink.closest(self.moduleCardController.moduleItemActionsSelector);
        actionMenuObj.hide();
        actionMenuObj.after(spinnerObj);
      } else {
        requestModuleAction(actionMenuLinks[0]);
      }

      function requestModuleAction(actionMenuLink, disableCacheClear, requestEndCallback) {
        self.moduleCardController._requestToController(bulkModuleAction, actionMenuLink, forceDeletion, disableCacheClear, requestEndCallback);
      }

      function countdownModulesRequest() {
        modulesRequestedCountdown--; //Now that all other modules have performed their action WITHOUT cache clear, we
        //can request the last module request WITH cache clear

        if (modulesRequestedCountdown <= 0) {
          if (spinnerObj) {
            spinnerObj.remove();
            spinnerObj = null;
          }

          var _lastMenuLink = actionMenuLinks[actionMenuLinks.length - 1];

          var _actionMenuObj = _lastMenuLink.closest(self.moduleCardController.moduleItemActionsSelector);

          _actionMenuObj.fadeIn();

          requestModuleAction(_lastMenuLink);
        }
      }

      function filterAllowedActions(modulesActions) {
        var actionMenuLinks = [];
        var actionMenuLink;
        $.each(modulesActions, function filterAllowedModules(index, moduleData) {
          actionMenuLink = $(self.moduleCardController.moduleActionMenuLinkSelector + bulkModuleAction, moduleData.actionMenuObj);

          if (actionMenuLink.length > 0) {
            actionMenuLinks.push(actionMenuLink);
          } else {
            $.growl.error({
              message: window.translate_javascripts['Bulk Action - Request not available for module'].replace('[1]', bulkModuleAction).replace('[2]', moduleData.techName)
            });
          }
        });
        return actionMenuLinks;
      }
    }
  }, {
    key: "initActionButtons",
    value: function initActionButtons() {
      var _this = this;

      var self = this;
      $('body').on('click', self.moduleInstallBtnSelector, function initializeActionButtonsClick(event) {
        var $this = $(this);
        var $next = $($this.next());
        event.preventDefault();
        $this.hide();
        $next.show();
        $.ajax({
          url: $this.data('url'),
          dataType: 'json'
        }).done(function () {
          $next.fadeOut();
        });
      }); // "Upgrade All" button handler

      $('body').on('click', self.upgradeAllSource, function (event) {
        event.preventDefault();

        if ($(self.upgradeAllTargets).length <= 0) {
          console.warn(window.translate_javascripts['Upgrade All Action - One module minimum']);
          return false;
        }

        var modulesActions = [];
        var moduleTechName;
        $(self.upgradeAllTargets).each(function bulkActionSelector() {
          var moduleItemList = $(this).closest('.module-item-list');
          moduleTechName = moduleItemList.data('tech-name');
          modulesActions.push({
            techName: moduleTechName,
            actionMenuObj: $('.module-actions', moduleItemList)
          });
        });

        _this.performModulesAction(modulesActions, 'upgrade');

        return true;
      });
    }
  }, {
    key: "initCategorySelect",
    value: function initCategorySelect() {
      var self = this;
      var body = $('body');
      body.on('click', self.categoryItemSelector, function initializeCategorySelectClick() {
        // Get data from li DOM input
        self.currentRefCategory = $(this).data('category-ref');
        self.currentRefCategory = self.currentRefCategory ? String(self.currentRefCategory).toLowerCase() : null; // Change dropdown label to set it to the current category's displayname

        $(self.categorySelectorLabelSelector).text($(this).data('category-display-name'));
        $(self.categoryResetBtnSelector).show();
        self.updateModuleVisibility();
      });
      body.on('click', self.categoryResetBtnSelector, function initializeCategoryResetButtonClick() {
        var rawText = $(self.categorySelector).attr('aria-labelledby');
        var upperFirstLetter = rawText.charAt(0).toUpperCase();
        var removedFirstLetter = rawText.slice(1);
        var originalText = upperFirstLetter + removedFirstLetter;
        $(self.categorySelectorLabelSelector).text(originalText);
        $(this).hide();
        self.currentRefCategory = null;
        self.updateModuleVisibility();
      });
    }
  }, {
    key: "initSearchBlock",
    value: function initSearchBlock() {
      var _this2 = this;

      var self = this;
      self.pstaggerInput = $('#module-search-bar').pstagger({
        onTagsChanged: function onTagsChanged(tagList) {
          self.currentTagsList = tagList;
          self.updateModuleVisibility();
        },
        onResetTags: function onResetTags() {
          self.currentTagsList = [];
          self.updateModuleVisibility();
        },
        inputPlaceholder: window.translate_javascripts['Search - placeholder'],
        closingCross: true,
        context: self
      });
      $('body').on('click', '.module-addons-search-link', function (event) {
        event.preventDefault();
        event.stopPropagation();
        window.open($(_this2).attr('href'), '_blank');
      });
    }
    /**
     * Initialize display switching between List or Grid
     */

  }, {
    key: "initSortingDisplaySwitch",
    value: function initSortingDisplaySwitch() {
      var self = this;
      $('body').on('click', '.module-sort-switch', function switchSort() {
        var switchTo = $(this).data('switch');
        var isAlreadyDisplayed = $(this).hasClass('active-display');

        if (typeof switchTo !== 'undefined' && isAlreadyDisplayed === false) {
          self.switchSortingDisplayTo(switchTo);
          self.currentDisplay = switchTo;
        }
      });
    }
  }, {
    key: "switchSortingDisplayTo",
    value: function switchSortingDisplayTo(switchTo) {
      if (switchTo !== this.DISPLAY_GRID && switchTo !== this.DISPLAY_LIST) {
        console.error("Can't switch to undefined display property \"".concat(switchTo, "\""));
        return;
      }

      $('.module-sort-switch').removeClass('module-sort-active');
      $("#module-sort-".concat(switchTo)).addClass('module-sort-active');
      this.currentDisplay = switchTo;
      this.updateModuleVisibility();
    }
  }, {
    key: "initializeSeeMore",
    value: function initializeSeeMore() {
      var self = this;
      $("".concat(self.moduleShortList, " ").concat(self.seeMoreSelector)).on('click', function seeMore() {
        self.currentCategoryDisplay[$(this).data('category')] = true;
        $(this).addClass('d-none');
        $(this).closest(self.moduleShortList).find(self.seeLessSelector).removeClass('d-none');
        self.updateModuleVisibility();
      });
      $("".concat(self.moduleShortList, " ").concat(self.seeLessSelector)).on('click', function seeMore() {
        self.currentCategoryDisplay[$(this).data('category')] = false;
        $(this).addClass('d-none');
        $(this).closest(self.moduleShortList).find(self.seeMoreSelector).removeClass('d-none');
        self.updateModuleVisibility();
      });
    }
  }, {
    key: "updateTotalResults",
    value: function updateTotalResults() {
      var replaceFirstWordBy = function replaceFirstWordBy(element, value) {
        var explodedText = element.text().split(' ');
        explodedText[0] = value;
        element.text(explodedText.join(' '));
      }; // If there are some shortlist: each shortlist count the modules on the next container.


      var $shortLists = $('.module-short-list');

      if ($shortLists.length > 0) {
        $shortLists.each(function shortLists() {
          var $this = $(this);
          replaceFirstWordBy($this.find('.module-search-result-wording'), $this.next('.modules-list').find('.module-item').length);
        }); // If there is no shortlist: the wording directly update from the only module container.
      } else {
        var modulesCount = $('.modules-list').find('.module-item').length;
        replaceFirstWordBy($('.module-search-result-wording'), modulesCount);
        var selectorToToggle = self.currentDisplay === self.DISPLAY_LIST ? this.addonItemListSelector : this.addonItemGridSelector;
        $(selectorToToggle).toggle(modulesCount !== this.modulesList.length / 2);

        if (modulesCount === 0) {
          $('.module-addons-search-link').attr('href', "".concat(this.baseAddonsUrl, "search.php?search_query=").concat(encodeURIComponent(this.currentTagsList.join(' '))));
        }
      }
    }
  }]);

  return AdminModuleController;
}();

/* harmony default export */ __webpack_exports__["default"] = (AdminModuleController);

/***/ }),

/***/ "./js/pages/module/index.js":
/*!**********************************!*\
  !*** ./js/pages/module/index.js ***!
  \**********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_module_card__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/module-card */ "./js/components/module-card.js");
/* harmony import */ var _controller__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./controller */ "./js/pages/module/controller.js");
/* harmony import */ var _loader__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./loader */ "./js/pages/module/loader.js");
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
  var moduleCardController = new _components_module_card__WEBPACK_IMPORTED_MODULE_0__["default"]();
  new _loader__WEBPACK_IMPORTED_MODULE_2__["default"]();
  new _controller__WEBPACK_IMPORTED_MODULE_1__["default"](moduleCardController);
});

/***/ }),

/***/ "./js/pages/module/loader.js":
/*!***********************************!*\
  !*** ./js/pages/module/loader.js ***!
  \***********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
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
/**
 * Module Admin Page Loader.
 * @constructor
 */

var ModuleLoader =
/*#__PURE__*/
function () {
  function ModuleLoader() {
    _classCallCheck(this, ModuleLoader);

    ModuleLoader.handleImport();
    ModuleLoader.handleEvents();
  }

  _createClass(ModuleLoader, null, [{
    key: "handleImport",
    value: function handleImport() {
      var moduleImport = $('#module-import');
      moduleImport.click(function () {
        moduleImport.addClass('onclick', 250, validate);
      });

      function validate() {
        setTimeout(function () {
          moduleImport.removeClass('onclick');
          moduleImport.addClass('validate', 450, callback);
        }, 2250);
      }

      function callback() {
        setTimeout(function () {
          moduleImport.removeClass('validate');
        }, 1250);
      }
    }
  }, {
    key: "handleEvents",
    value: function handleEvents() {
      $('body').on('click', 'a.module-read-more-grid-btn, a.module-read-more-list-btn', function (event) {
        event.preventDefault();
        var modulePoppin = $(event.target).data('target');
        $.get(event.target.href, function (data) {
          $(modulePoppin).html(data);
          $(modulePoppin).modal();
        });
      });
    }
  }]);

  return ModuleLoader;
}();

/* harmony default export */ __webpack_exports__["default"] = (ModuleLoader);

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9tb2R1bGUtY2FyZC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9tb2R1bGUvY29udHJvbGxlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9tb2R1bGUvaW5kZXguanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvbW9kdWxlL2xvYWRlci5qcyIsIndlYnBhY2s6Ly8vZXh0ZXJuYWwgXCJqUXVlcnlcIiJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwiQk9FdmVudCIsIm9uIiwiZXZlbnROYW1lIiwiY2FsbGJhY2siLCJjb250ZXh0IiwiZG9jdW1lbnQiLCJhZGRFdmVudExpc3RlbmVyIiwiZXZlbnQiLCJjYWxsIiwiZW1pdEV2ZW50IiwiZXZlbnRUeXBlIiwiX2V2ZW50IiwiY3JlYXRlRXZlbnQiLCJpbml0RXZlbnQiLCJkaXNwYXRjaEV2ZW50IiwiTW9kdWxlQ2FyZCIsIm1vZHVsZUFjdGlvbk1lbnVMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25NZW51SW5zdGFsbExpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVFbmFibGVMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25NZW51VW5pbnN0YWxsTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudURpc2FibGVMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25NZW51RW5hYmxlTW9iaWxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudURpc2FibGVNb2JpbGVMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25NZW51UmVzZXRMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25NZW51VXBkYXRlTGlua1NlbGVjdG9yIiwibW9kdWxlSXRlbUxpc3RTZWxlY3RvciIsIm1vZHVsZUl0ZW1HcmlkU2VsZWN0b3IiLCJtb2R1bGVJdGVtQWN0aW9uc1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTW9kYWxEaXNhYmxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTW9kYWxSZXNldExpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1vZGFsVW5pbnN0YWxsTGlua1NlbGVjdG9yIiwiZm9yY2VEZWxldGlvbk9wdGlvbiIsImluaXRBY3Rpb25CdXR0b25zIiwic2VsZiIsImJ0biIsImF0dHIiLCJwcm9wIiwicmVtb3ZlQXR0ciIsImxlbmd0aCIsIm1vZGFsIiwiX2Rpc3BhdGNoUHJlRXZlbnQiLCJfY29uZmlybUFjdGlvbiIsIl9yZXF1ZXN0VG9Db250cm9sbGVyIiwiZSIsInRhcmdldCIsInBhcmVudHMiLCJiaW5kIiwiYWN0aW9uIiwiZWxlbWVudCIsImRhdGEiLCJmaXJzdCIsInJlc3VsdCIsInRoYXQiLCJfcmVwbGFjZVByZXN0YVRydXN0UGxhY2Vob2xkZXJzIiwiZmluZCIsIm9mZiIsImluc3RhbGxfYnV0dG9uIiwibW9kdWxlIiwiYXR0cmlidXRlcyIsIm5hbWUiLCJmb3JtIiwicGFyZW50IiwidHlwZSIsInZhbHVlIiwiYXBwZW5kVG8iLCJjbGljayIsImNvbmZpcm1hdGlvbl9zdWJqZWN0IiwiYWxlcnRDbGFzcyIsInByZXN0YXRydXN0Iiwic3RhdHVzIiwiY2hlY2tfbGlzdCIsInByb3BlcnR5Iiwic2hvdyIsImhpZGUiLCJ1cmwiLCJ0b2dnbGUiLCJzcmMiLCJpbWciLCJhbHQiLCJ0ZXh0IiwiZGlzcGxheU5hbWUiLCJhdXRob3IiLCJtZXNzYWdlIiwialF1ZXJ5IiwiRXZlbnQiLCJ0cmlnZ2VyIiwiaXNQcm9wYWdhdGlvblN0b3BwZWQiLCJpc0ltbWVkaWF0ZVByb3BhZ2F0aW9uU3RvcHBlZCIsImZvcmNlRGVsZXRpb24iLCJkaXNhYmxlQ2FjaGVDbGVhciIsImpxRWxlbWVudE9iaiIsImNsb3Nlc3QiLCJzcGlubmVyT2JqIiwibG9jYXRpb24iLCJob3N0IiwiYWN0aW9uUGFyYW1zIiwic2VyaWFsaXplQXJyYXkiLCJwdXNoIiwiYWpheCIsImRhdGFUeXBlIiwibWV0aG9kIiwiYmVmb3JlU2VuZCIsImFmdGVyIiwiZG9uZSIsInVuZGVmaW5lZCIsImdyb3dsIiwiZXJyb3IiLCJtb2R1bGVUZWNoTmFtZSIsIk9iamVjdCIsImtleXMiLCJfY29uZmlybVByZXN0YVRydXN0IiwibXNnIiwibm90aWNlIiwiYWx0ZXJlZFNlbGVjdG9yIiwiX2dldE1vZHVsZUl0ZW1TZWxlY3RvciIsInJlcGxhY2UiLCJtYWluRWxlbWVudCIsInJlbW92ZSIsImFkZENsYXNzIiwicmVtb3ZlQ2xhc3MiLCJyZXBsYWNlV2l0aCIsImFjdGlvbl9tZW51X2h0bWwiLCJmYWlsIiwibW9kdWxlSXRlbSIsInRlY2hOYW1lIiwiYWx3YXlzIiwiZmFkZUluIiwiQWRtaW5Nb2R1bGVDb250cm9sbGVyIiwibW9kdWxlQ2FyZENvbnRyb2xsZXIiLCJERUZBVUxUX01BWF9SRUNFTlRMWV9VU0VEIiwiREVGQVVMVF9NQVhfUEVSX0NBVEVHT1JJRVMiLCJESVNQTEFZX0dSSUQiLCJESVNQTEFZX0xJU1QiLCJDQVRFR09SWV9SRUNFTlRMWV9VU0VEIiwiY3VycmVudENhdGVnb3J5RGlzcGxheSIsImN1cnJlbnREaXNwbGF5IiwiaXNDYXRlZ29yeUdyaWREaXNwbGF5ZWQiLCJjdXJyZW50VGFnc0xpc3QiLCJjdXJyZW50UmVmQ2F0ZWdvcnkiLCJjdXJyZW50UmVmU3RhdHVzIiwiY3VycmVudFNvcnRpbmciLCJiYXNlQWRkb25zVXJsIiwicHN0YWdnZXJJbnB1dCIsImxhc3RCdWxrQWN0aW9uIiwiaXNVcGxvYWRTdGFydGVkIiwicmVjZW50bHlVc2VkU2VsZWN0b3IiLCJtb2R1bGVzTGlzdCIsImFkZG9uc0NhcmRHcmlkIiwiYWRkb25zQ2FyZExpc3QiLCJtb2R1bGVTaG9ydExpc3QiLCJzZWVNb3JlU2VsZWN0b3IiLCJzZWVMZXNzU2VsZWN0b3IiLCJjYXRlZ29yeVNlbGVjdG9yTGFiZWxTZWxlY3RvciIsImNhdGVnb3J5U2VsZWN0b3IiLCJjYXRlZ29yeUl0ZW1TZWxlY3RvciIsImFkZG9uc0xvZ2luQnV0dG9uU2VsZWN0b3IiLCJjYXRlZ29yeVJlc2V0QnRuU2VsZWN0b3IiLCJtb2R1bGVJbnN0YWxsQnRuU2VsZWN0b3IiLCJtb2R1bGVTb3J0aW5nRHJvcGRvd25TZWxlY3RvciIsImNhdGVnb3J5R3JpZFNlbGVjdG9yIiwiY2F0ZWdvcnlHcmlkSXRlbVNlbGVjdG9yIiwiYWRkb25JdGVtR3JpZFNlbGVjdG9yIiwiYWRkb25JdGVtTGlzdFNlbGVjdG9yIiwidXBncmFkZUFsbFNvdXJjZSIsInVwZ3JhZGVBbGxUYXJnZXRzIiwiYnVsa0FjdGlvbkRyb3BEb3duU2VsZWN0b3IiLCJidWxrSXRlbVNlbGVjdG9yIiwiYnVsa0FjdGlvbkNoZWNrYm94TGlzdFNlbGVjdG9yIiwiYnVsa0FjdGlvbkNoZWNrYm94R3JpZFNlbGVjdG9yIiwiY2hlY2tlZEJ1bGtBY3Rpb25MaXN0U2VsZWN0b3IiLCJjaGVja2VkQnVsa0FjdGlvbkdyaWRTZWxlY3RvciIsImJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdG9yIiwiYnVsa0NvbmZpcm1Nb2RhbFNlbGVjdG9yIiwiYnVsa0NvbmZpcm1Nb2RhbEFjdGlvbk5hbWVTZWxlY3RvciIsImJ1bGtDb25maXJtTW9kYWxMaXN0U2VsZWN0b3IiLCJidWxrQ29uZmlybU1vZGFsQWNrQnRuU2VsZWN0b3IiLCJwbGFjZWhvbGRlckdsb2JhbFNlbGVjdG9yIiwicGxhY2Vob2xkZXJGYWlsdXJlR2xvYmFsU2VsZWN0b3IiLCJwbGFjZWhvbGRlckZhaWx1cmVNc2dTZWxlY3RvciIsInBsYWNlaG9sZGVyRmFpbHVyZVJldHJ5QnRuU2VsZWN0b3IiLCJzdGF0dXNTZWxlY3RvckxhYmVsU2VsZWN0b3IiLCJzdGF0dXNJdGVtU2VsZWN0b3IiLCJzdGF0dXNSZXNldEJ0blNlbGVjdG9yIiwiYWRkb25zQ29ubmVjdE1vZGFsQnRuU2VsZWN0b3IiLCJhZGRvbnNMb2dvdXRNb2RhbEJ0blNlbGVjdG9yIiwiYWRkb25zSW1wb3J0TW9kYWxCdG5TZWxlY3RvciIsImRyb3Bab25lTW9kYWxTZWxlY3RvciIsImRyb3Bab25lTW9kYWxGb290ZXJTZWxlY3RvciIsImRyb3Bab25lSW1wb3J0Wm9uZVNlbGVjdG9yIiwiYWRkb25zQ29ubmVjdE1vZGFsU2VsZWN0b3IiLCJhZGRvbnNMb2dvdXRNb2RhbFNlbGVjdG9yIiwiYWRkb25zQ29ubmVjdEZvcm0iLCJtb2R1bGVJbXBvcnRNb2RhbENsb3NlQnRuIiwibW9kdWxlSW1wb3J0U3RhcnRTZWxlY3RvciIsIm1vZHVsZUltcG9ydFByb2Nlc3NpbmdTZWxlY3RvciIsIm1vZHVsZUltcG9ydFN1Y2Nlc3NTZWxlY3RvciIsIm1vZHVsZUltcG9ydFN1Y2Nlc3NDb25maWd1cmVCdG5TZWxlY3RvciIsIm1vZHVsZUltcG9ydEZhaWx1cmVTZWxlY3RvciIsIm1vZHVsZUltcG9ydEZhaWx1cmVSZXRyeVNlbGVjdG9yIiwibW9kdWxlSW1wb3J0RmFpbHVyZURldGFpbHNCdG5TZWxlY3RvciIsIm1vZHVsZUltcG9ydFNlbGVjdEZpbGVNYW51YWxTZWxlY3RvciIsIm1vZHVsZUltcG9ydEZhaWx1cmVNc2dEZXRhaWxzU2VsZWN0b3IiLCJtb2R1bGVJbXBvcnRDb25maXJtU2VsZWN0b3IiLCJpbml0U29ydGluZ0Ryb3Bkb3duIiwiaW5pdEJPRXZlbnRSZWdpc3RlcmluZyIsImluaXRDdXJyZW50RGlzcGxheSIsImluaXRTb3J0aW5nRGlzcGxheVN3aXRjaCIsImluaXRCdWxrRHJvcGRvd24iLCJpbml0U2VhcmNoQmxvY2siLCJpbml0Q2F0ZWdvcnlTZWxlY3QiLCJpbml0Q2F0ZWdvcmllc0dyaWQiLCJpbml0QWRkb25zU2VhcmNoIiwiaW5pdEFkZG9uc0Nvbm5lY3QiLCJpbml0QWRkTW9kdWxlQWN0aW9uIiwiaW5pdERyb3B6b25lIiwiaW5pdFBhZ2VDaGFuZ2VQcm90ZWN0aW9uIiwiaW5pdFBsYWNlaG9sZGVyTWVjaGFuaXNtIiwiaW5pdEZpbHRlclN0YXR1c0Ryb3Bkb3duIiwiZmV0Y2hNb2R1bGVzTGlzdCIsImdldE5vdGlmaWNhdGlvbnNDb3VudCIsImluaXRpYWxpemVTZWVNb3JlIiwiYm9keSIsInBhcnNlSW50IiwidXBkYXRlTW9kdWxlVmlzaWJpbGl0eSIsImdldEJ1bGtDaGVja2JveGVzU2VsZWN0b3IiLCJzZWxlY3RvciIsImdldEJ1bGtDaGVja2JveGVzQ2hlY2tlZFNlbGVjdG9yIiwiaW5pdGlhbGl6ZUJvZHlDaGFuZ2UiLCJ3YXJuaW5nIiwidHJhbnNsYXRlX2phdmFzY3JpcHRzIiwibW9kdWxlc0xpc3RTdHJpbmciLCJidWlsZEJ1bGtBY3Rpb25Nb2R1bGVMaXN0IiwiYWN0aW9uU3RyaW5nIiwidG9Mb3dlckNhc2UiLCJodG1sIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJkb0J1bGtBY3Rpb24iLCJvbk1vZHVsZURpc2FibGVkIiwidXBkYXRlVG90YWxSZXN1bHRzIiwibW9kdWxlSXRlbVNlbGVjdG9yIiwiZ2V0TW9kdWxlSXRlbVNlbGVjdG9yIiwiZWFjaCIsInNjYW5Nb2R1bGVzTGlzdCIsImFqYXhMb2FkUGFnZSIsImZhZGVPdXQiLCJtb2R1bGVVUkxzIiwiY2F0YWxvZ1JlZnJlc2giLCJyZXNwb25zZSIsImRvbUVsZW1lbnRzIiwic3R5bGVzaGVldCIsInN0eWxlU2hlZXRzIiwic3R5bGVzaGVldFJ1bGUiLCJtb2R1bGVHbG9iYWxTZWxlY3RvciIsIm1vZHVsZVNvcnRpbmdTZWxlY3RvciIsInJlcXVpcmVkU2VsZWN0b3JDb21iaW5hdGlvbiIsImluc2VydFJ1bGUiLCJjc3NSdWxlcyIsImFkZFJ1bGUiLCJpbmRleCIsImFwcGVuZCIsImNvbnRlbnQiLCJjc3MiLCJwb3BvdmVyIiwic3RhdHVzVGV4dCIsImNvbnRhaW5lciIsIiR0aGlzIiwicHJlcGFyZUNvbnRhaW5lciIsInByZXBhcmVNb2R1bGVzIiwiZG9tT2JqZWN0IiwiaWQiLCJzY29yaW5nIiwicGFyc2VGbG9hdCIsImxvZ28iLCJ2ZXJzaW9uIiwiZGVzY3JpcHRpb24iLCJjaGlsZENhdGVnb3JpZXMiLCJjYXRlZ29yaWVzIiwiU3RyaW5nIiwicHJpY2UiLCJhY3RpdmUiLCJhY2Nlc3MiLCJkaXNwbGF5IiwiaGFzQ2xhc3MiLCJvcmRlciIsImtleSIsInNwbGl0dGVkS2V5Iiwic3BsaXQiLCJjdXJyZW50Q29tcGFyZSIsImEiLCJiIiwiYURhdGEiLCJiRGF0YSIsIkRhdGUiLCJnZXRUaW1lIiwiaXNOYU4iLCJsb2NhbGVDb21wYXJlIiwic29ydCIsInJldmVyc2UiLCJzZXRTaG9ydExpc3RWaXNpYmlsaXR5IiwibmJNb2R1bGVzSW5Db250YWluZXIiLCJ1cGRhdGVNb2R1bGVTb3J0aW5nIiwiaXNWaXNpYmxlIiwiY3VycmVudE1vZHVsZSIsIm1vZHVsZUNhdGVnb3J5IiwidGFnRXhpc3RzIiwibmV3VmFsdWUiLCJtb2R1bGVzTGlzdExlbmd0aCIsImNvdW50ZXIiLCJpIiwiaW5kZXhPZiIsInVwZGF0ZU1vZHVsZUNvbnRhaW5lckRpc3BsYXkiLCJjaGVja0JveGVzU2VsZWN0b3IiLCJhbHJlYWR5RG9uZUZsYWciLCJodG1sR2VuZXJhdGVkIiwiY3VycmVudEVsZW1lbnQiLCJwcmVwYXJlQ2hlY2tib3hlcyIsImluaXRpYWxpemVCb2R5U3VibWl0Iiwic2VyaWFsaXplIiwic3VjY2VzcyIsInJlbG9hZCIsImFkZE1vZHVsZUJ1dHRvbiIsImRyb3B6b25lIiwic2V0VGltZW91dCIsIm1hbnVhbFNlbGVjdCIsImluaXRpYWxpemVCb2R5Q2xpY2tPbk1vZHVsZUltcG9ydCIsInNsaWRlRG93biIsImRyb3B6b25lT3B0aW9ucyIsIm1vZHVsZUltcG9ydCIsImFjY2VwdGVkRmlsZXMiLCJwYXJhbU5hbWUiLCJtYXhGaWxlc2l6ZSIsInVwbG9hZE11bHRpcGxlIiwiYWRkUmVtb3ZlTGlua3MiLCJkaWN0RGVmYXVsdE1lc3NhZ2UiLCJoaWRkZW5JbnB1dENvbnRhaW5lciIsInRpbWVvdXQiLCJhZGRlZGZpbGUiLCJhbmltYXRlU3RhcnRVcGxvYWQiLCJwcm9jZXNzaW5nIiwiZmlsZSIsImRpc3BsYXlPblVwbG9hZEVycm9yIiwiY29tcGxldGUiLCJyZXNwb25zZU9iamVjdCIsInBhcnNlSlNPTiIsInhociIsImlzX2NvbmZpZ3VyYWJsZSIsIm1vZHVsZV9uYW1lIiwiZGlzcGxheU9uVXBsb2FkRG9uZSIsImV4dGVuZCIsImZpbmlzaCIsImFuaW1hdGVFbmRVcGxvYWQiLCJjb25maWd1cmVMaW5rIiwiY29uZmlndXJhdGlvblBhZ2UiLCJkaXNwbGF5UHJlc3RhVHJ1c3RTdGVwIiwibW9kdWxlTmFtZSIsInBvc3QiLCJ1cmxzIiwiaW5zdGFsbCIsImdldEpTT04iLCJub3RpZmljYXRpb25zQ291bnQiLCJ1cGRhdGVOb3RpZmljYXRpb25zQ291bnQiLCJjb25zb2xlIiwiYmFkZ2UiLCJkZXN0aW5hdGlvblRhYnMiLCJ0b19jb25maWd1cmUiLCJ0b191cGRhdGUiLCJzZWFyY2hRdWVyeSIsImVuY29kZVVSSUNvbXBvbmVudCIsImpvaW4iLCJvcGVuIiwiaW5pdGlsYWl6ZUdyaWRCb2R5Q2xpY2siLCJyZWZDYXRlZ29yeSIsInJlc2V0VGFncyIsIm1lbnVDYXRlZ29yeVRvVHJpZ2dlciIsIndhcm4iLCJpbml0aWFsaXplQm9keVNvcnRpbmdDaGFuZ2UiLCJyZXF1ZXN0ZWRCdWxrQWN0aW9uIiwiYnVsa0FjdGlvblRvVXJsIiwiYnVsa0FjdGlvblNlbGVjdGVkU2VsZWN0b3IiLCJidWxrTW9kdWxlQWN0aW9uIiwibW9kdWxlc0FjdGlvbnMiLCJidWxrQWN0aW9uU2VsZWN0b3IiLCJhY3Rpb25NZW51T2JqIiwibmV4dCIsInBlcmZvcm1Nb2R1bGVzQWN0aW9uIiwiYWN0aW9uTWVudUxpbmtzIiwiZmlsdGVyQWxsb3dlZEFjdGlvbnMiLCJtb2R1bGVzUmVxdWVzdGVkQ291bnRkb3duIiwiYnVsa01vZHVsZXNMb29wIiwiYWN0aW9uTWVudUxpbmsiLCJyZXF1ZXN0TW9kdWxlQWN0aW9uIiwiY291bnRkb3duTW9kdWxlc1JlcXVlc3QiLCJsYXN0TWVudUxpbmsiLCJyZXF1ZXN0RW5kQ2FsbGJhY2siLCJmaWx0ZXJBbGxvd2VkTW9kdWxlcyIsIm1vZHVsZURhdGEiLCJpbml0aWFsaXplQWN0aW9uQnV0dG9uc0NsaWNrIiwiJG5leHQiLCJtb2R1bGVJdGVtTGlzdCIsImluaXRpYWxpemVDYXRlZ29yeVNlbGVjdENsaWNrIiwiaW5pdGlhbGl6ZUNhdGVnb3J5UmVzZXRCdXR0b25DbGljayIsInJhd1RleHQiLCJ1cHBlckZpcnN0TGV0dGVyIiwiY2hhckF0IiwidG9VcHBlckNhc2UiLCJyZW1vdmVkRmlyc3RMZXR0ZXIiLCJzbGljZSIsIm9yaWdpbmFsVGV4dCIsInBzdGFnZ2VyIiwib25UYWdzQ2hhbmdlZCIsInRhZ0xpc3QiLCJvblJlc2V0VGFncyIsImlucHV0UGxhY2Vob2xkZXIiLCJjbG9zaW5nQ3Jvc3MiLCJzd2l0Y2hTb3J0Iiwic3dpdGNoVG8iLCJpc0FscmVhZHlEaXNwbGF5ZWQiLCJzd2l0Y2hTb3J0aW5nRGlzcGxheVRvIiwic2VlTW9yZSIsInJlcGxhY2VGaXJzdFdvcmRCeSIsImV4cGxvZGVkVGV4dCIsIiRzaG9ydExpc3RzIiwic2hvcnRMaXN0cyIsIm1vZHVsZXNDb3VudCIsInNlbGVjdG9yVG9Ub2dnbGUiLCJNb2R1bGVMb2FkZXIiLCJoYW5kbGVJbXBvcnQiLCJoYW5kbGVFdmVudHMiLCJ2YWxpZGF0ZSIsIm1vZHVsZVBvcHBpbiIsImdldCIsImhyZWYiXSwibWFwcGluZ3MiOiI7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLGtEQUEwQyxnQ0FBZ0M7QUFDMUU7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxnRUFBd0Qsa0JBQWtCO0FBQzFFO0FBQ0EseURBQWlELGNBQWM7QUFDL0Q7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGlEQUF5QyxpQ0FBaUM7QUFDMUUsd0hBQWdILG1CQUFtQixFQUFFO0FBQ3JJO0FBQ0E7O0FB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QUpPLEVBS1IsSUFMUSxDQUFWO0FBT0Q7O0FBQ0QsZUFBU0EsUUFBVCxHQUFvQjtBQUNsQmdVLGtCQUFVLENBQ1IsWUFBTTtBQUNKSyxzQkFBWSxDQUFDak4sV0FBYixDQUF5QixVQUF6QjtBQUNELFNBSE8sRUFJUixJQUpRLENBQVY7QUFNRDtBQUNGOzs7bUNBRXFCO0FBQ3BCekgsT0FBQyxDQUFDLE1BQUQsQ0FBRCxDQUFVRyxFQUFWLENBQ0UsT0FERixFQUVFLDBEQUZGLEVBR0UsVUFBQ00sS0FBRCxFQUFXO0FBQ1RBLGFBQUssQ0FBQ29PLGNBQU47QUFDQSxZQUFNbU0sWUFBWSxHQUFHaGIsQ0FBQyxDQUFDUyxLQUFLLENBQUNxQyxNQUFQLENBQUQsQ0FBZ0JLLElBQWhCLENBQXFCLFFBQXJCLENBQXJCO0FBRUFuRCxTQUFDLENBQUNpYixHQUFGLENBQU14YSxLQUFLLENBQUNxQyxNQUFOLENBQWFvWSxJQUFuQixFQUF5QixVQUFDL1gsSUFBRCxFQUFVO0FBQ2pDbkQsV0FBQyxDQUFDZ2IsWUFBRCxDQUFELENBQWdCcE0sSUFBaEIsQ0FBcUJ6TCxJQUFyQjtBQUNBbkQsV0FBQyxDQUFDZ2IsWUFBRCxDQUFELENBQWdCdlksS0FBaEI7QUFDRCxTQUhEO0FBSUQsT0FYSDtBQWFEOzs7Ozs7QUFHWW1ZLDJFQUFmLEU7Ozs7Ozs7Ozs7O0FDL0VBLHdCIiwiZmlsZSI6Im1vZHVsZS5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBnZXR0ZXIgfSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGRlZmluZSBfX2VzTW9kdWxlIG9uIGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uciA9IGZ1bmN0aW9uKGV4cG9ydHMpIHtcbiBcdFx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFN5bWJvbC50b1N0cmluZ1RhZywgeyB2YWx1ZTogJ01vZHVsZScgfSk7XG4gXHRcdH1cbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbiBcdH07XG5cbiBcdC8vIGNyZWF0ZSBhIGZha2UgbmFtZXNwYWNlIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDE6IHZhbHVlIGlzIGEgbW9kdWxlIGlkLCByZXF1aXJlIGl0XG4gXHQvLyBtb2RlICYgMjogbWVyZ2UgYWxsIHByb3BlcnRpZXMgb2YgdmFsdWUgaW50byB0aGUgbnNcbiBcdC8vIG1vZGUgJiA0OiByZXR1cm4gdmFsdWUgd2hlbiBhbHJlYWR5IG5zIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDh8MTogYmVoYXZlIGxpa2UgcmVxdWlyZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy50ID0gZnVuY3Rpb24odmFsdWUsIG1vZGUpIHtcbiBcdFx0aWYobW9kZSAmIDEpIHZhbHVlID0gX193ZWJwYWNrX3JlcXVpcmVfXyh2YWx1ZSk7XG4gXHRcdGlmKG1vZGUgJiA4KSByZXR1cm4gdmFsdWU7XG4gXHRcdGlmKChtb2RlICYgNCkgJiYgdHlwZW9mIHZhbHVlID09PSAnb2JqZWN0JyAmJiB2YWx1ZSAmJiB2YWx1ZS5fX2VzTW9kdWxlKSByZXR1cm4gdmFsdWU7XG4gXHRcdHZhciBucyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18ucihucyk7XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShucywgJ2RlZmF1bHQnLCB7IGVudW1lcmFibGU6IHRydWUsIHZhbHVlOiB2YWx1ZSB9KTtcbiBcdFx0aWYobW9kZSAmIDIgJiYgdHlwZW9mIHZhbHVlICE9ICdzdHJpbmcnKSBmb3IodmFyIGtleSBpbiB2YWx1ZSkgX193ZWJwYWNrX3JlcXVpcmVfXy5kKG5zLCBrZXksIGZ1bmN0aW9uKGtleSkgeyByZXR1cm4gdmFsdWVba2V5XTsgfS5iaW5kKG51bGwsIGtleSkpO1xuIFx0XHRyZXR1cm4gbnM7XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIi9hZG1pbi1kZXYvdGhlbWVzL25ldy10aGVtZS9wdWJsaWMvXCI7XG5cblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSBcIi4vanMvcGFnZXMvbW9kdWxlL2luZGV4LmpzXCIpO1xuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG52YXIgQk9FdmVudCA9IHtcbiAgb246IGZ1bmN0aW9uKGV2ZW50TmFtZSwgY2FsbGJhY2ssIGNvbnRleHQpIHtcblxuICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoZXZlbnROYW1lLCBmdW5jdGlvbihldmVudCkge1xuICAgICAgaWYgKHR5cGVvZiBjb250ZXh0ICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICBjYWxsYmFjay5jYWxsKGNvbnRleHQsIGV2ZW50KTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGNhbGxiYWNrKGV2ZW50KTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfSxcblxuICBlbWl0RXZlbnQ6IGZ1bmN0aW9uKGV2ZW50TmFtZSwgZXZlbnRUeXBlKSB7XG4gICAgdmFyIF9ldmVudCA9IGRvY3VtZW50LmNyZWF0ZUV2ZW50KGV2ZW50VHlwZSk7XG4gICAgLy8gdHJ1ZSB2YWx1ZXMgc3RhbmQgZm9yOiBjYW4gYnViYmxlLCBhbmQgaXMgY2FuY2VsbGFibGVcbiAgICBfZXZlbnQuaW5pdEV2ZW50KGV2ZW50TmFtZSwgdHJ1ZSwgdHJ1ZSk7XG4gICAgZG9jdW1lbnQuZGlzcGF0Y2hFdmVudChfZXZlbnQpO1xuICB9XG59O1xuXG5cbi8qKlxuICogQ2xhc3MgaXMgcmVzcG9uc2libGUgZm9yIGhhbmRsaW5nIE1vZHVsZSBDYXJkIGJlaGF2aW9yXG4gKlxuICogVGhpcyBpcyBhIHBvcnQgb2YgYWRtaW4tZGV2L3RoZW1lcy9kZWZhdWx0L2pzL2J1bmRsZS9tb2R1bGUvbW9kdWxlX2NhcmQuanNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTW9kdWxlQ2FyZCB7XG5cbiAgY29uc3RydWN0b3IoKSB7XG4gICAgLyogU2VsZWN0b3JzIGZvciBtb2R1bGUgYWN0aW9uIGxpbmtzICh1bmluc3RhbGwsIHJlc2V0LCBldGMuLi4pIHRvIGFkZCBhIGNvbmZpcm0gcG9waW4gKi9cbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV8nO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudUluc3RhbGxMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9pbnN0YWxsJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVFbmFibGVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9lbmFibGUnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudVVuaW5zdGFsbExpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X3VuaW5zdGFsbCc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51RGlzYWJsZUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2Rpc2FibGUnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudUVuYWJsZU1vYmlsZUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2VuYWJsZV9tb2JpbGUnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudURpc2FibGVNb2JpbGVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9kaXNhYmxlX21vYmlsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51UmVzZXRMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9yZXNldCc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51VXBkYXRlTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfdXBncmFkZSc7XG4gICAgdGhpcy5tb2R1bGVJdGVtTGlzdFNlbGVjdG9yID0gJy5tb2R1bGUtaXRlbS1saXN0JztcbiAgICB0aGlzLm1vZHVsZUl0ZW1HcmlkU2VsZWN0b3IgPSAnLm1vZHVsZS1pdGVtLWdyaWQnO1xuICAgIHRoaXMubW9kdWxlSXRlbUFjdGlvbnNTZWxlY3RvciA9ICcubW9kdWxlLWFjdGlvbnMnO1xuXG4gICAgLyogU2VsZWN0b3JzIG9ubHkgZm9yIG1vZGFsIGJ1dHRvbnMgKi9cbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1vZGFsRGlzYWJsZUxpbmtTZWxlY3RvciA9ICdhLm1vZHVsZV9hY3Rpb25fbW9kYWxfZGlzYWJsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbFJlc2V0TGlua1NlbGVjdG9yID0gJ2EubW9kdWxlX2FjdGlvbl9tb2RhbF9yZXNldCc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbFVuaW5zdGFsbExpbmtTZWxlY3RvciA9ICdhLm1vZHVsZV9hY3Rpb25fbW9kYWxfdW5pbnN0YWxsJztcbiAgICB0aGlzLmZvcmNlRGVsZXRpb25PcHRpb24gPSAnI2ZvcmNlX2RlbGV0aW9uJztcblxuICAgIHRoaXMuaW5pdEFjdGlvbkJ1dHRvbnMoKTtcbiAgfVxuXG4gIGluaXRBY3Rpb25CdXR0b25zKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5mb3JjZURlbGV0aW9uT3B0aW9uLCBmdW5jdGlvbiAoKSB7XG4gICAgICBjb25zdCBidG4gPSAkKHNlbGYubW9kdWxlQWN0aW9uTW9kYWxVbmluc3RhbGxMaW5rU2VsZWN0b3IsICQoXCJkaXYubW9kdWxlLWl0ZW0tbGlzdFtkYXRhLXRlY2gtbmFtZT0nXCIgKyAkKHRoaXMpLmF0dHIoXCJkYXRhLXRlY2gtbmFtZVwiKSArIFwiJ11cIikpO1xuICAgICAgaWYgKCQodGhpcykucHJvcCgnY2hlY2tlZCcpID09PSB0cnVlKSB7XG4gICAgICAgIGJ0bi5hdHRyKCdkYXRhLWRlbGV0aW9uJywgJ3RydWUnKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGJ0bi5yZW1vdmVBdHRyKCdkYXRhLWRlbGV0aW9uJyk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVJbnN0YWxsTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICBpZiAoJChcIiNtb2RhbC1wcmVzdGF0cnVzdFwiKS5sZW5ndGgpIHtcbiAgICAgICAgJChcIiNtb2RhbC1wcmVzdGF0cnVzdFwiKS5tb2RhbCgnaGlkZScpO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ2luc3RhbGwnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdpbnN0YWxsJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignaW5zdGFsbCcsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudUVuYWJsZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ2VuYWJsZScsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ2VuYWJsZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2VuYWJsZScsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudVVuaW5zdGFsbExpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ3VuaW5zdGFsbCcsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ3VuaW5zdGFsbCcsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ3VuaW5zdGFsbCcsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudURpc2FibGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdkaXNhYmxlJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbignZGlzYWJsZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2Rpc2FibGUnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVFbmFibGVNb2JpbGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdlbmFibGVfbW9iaWxlJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbignZW5hYmxlX21vYmlsZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2VuYWJsZV9tb2JpbGUnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTW9iaWxlTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgnZGlzYWJsZV9tb2JpbGUnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdkaXNhYmxlX21vYmlsZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2Rpc2FibGVfbW9iaWxlJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51UmVzZXRMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdyZXNldCcsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ3Jlc2V0JywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcigncmVzZXQnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVVcGRhdGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCd1cGRhdGUnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCd1cGRhdGUnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCd1cGRhdGUnLCAkKHRoaXMpKTtcbiAgICB9KTtcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxEaXNhYmxlTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZGlzYWJsZScsICQoc2VsZi5tb2R1bGVBY3Rpb25NZW51RGlzYWJsZUxpbmtTZWxlY3RvciwgJChcImRpdi5tb2R1bGUtaXRlbS1saXN0W2RhdGEtdGVjaC1uYW1lPSdcIiArICQodGhpcykuYXR0cihcImRhdGEtdGVjaC1uYW1lXCIpICsgXCInXVwiKSkpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxSZXNldExpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ3Jlc2V0JywgJChzZWxmLm1vZHVsZUFjdGlvbk1lbnVSZXNldExpbmtTZWxlY3RvciwgJChcImRpdi5tb2R1bGUtaXRlbS1saXN0W2RhdGEtdGVjaC1uYW1lPSdcIiArICQodGhpcykuYXR0cihcImRhdGEtdGVjaC1uYW1lXCIpICsgXCInXVwiKSkpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxVbmluc3RhbGxMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAkKGUudGFyZ2V0KS5wYXJlbnRzKCcubW9kYWwnKS5vbignaGlkZGVuLmJzLm1vZGFsJywgZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgcmV0dXJuIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoXG4gICAgICAgICAgJ3VuaW5zdGFsbCcsXG4gICAgICAgICAgJChcbiAgICAgICAgICAgIHNlbGYubW9kdWxlQWN0aW9uTWVudVVuaW5zdGFsbExpbmtTZWxlY3RvcixcbiAgICAgICAgICAgICQoXCJkaXYubW9kdWxlLWl0ZW0tbGlzdFtkYXRhLXRlY2gtbmFtZT0nXCIgKyAkKGUudGFyZ2V0KS5hdHRyKFwiZGF0YS10ZWNoLW5hbWVcIikgKyBcIiddXCIpXG4gICAgICAgICAgKSxcbiAgICAgICAgICAkKGUudGFyZ2V0KS5hdHRyKFwiZGF0YS1kZWxldGlvblwiKVxuICAgICAgICApO1xuICAgICAgfS5iaW5kKGUpKTtcbiAgICB9KTtcbiAgfTtcblxuICBfZ2V0TW9kdWxlSXRlbVNlbGVjdG9yKCkge1xuICAgIGlmICgkKHRoaXMubW9kdWxlSXRlbUxpc3RTZWxlY3RvcikubGVuZ3RoKSB7XG4gICAgICByZXR1cm4gdGhpcy5tb2R1bGVJdGVtTGlzdFNlbGVjdG9yO1xuICAgIH0gZWxzZSB7XG4gICAgICByZXR1cm4gdGhpcy5tb2R1bGVJdGVtR3JpZFNlbGVjdG9yO1xuICAgIH1cbiAgfTtcblxuICBfY29uZmlybUFjdGlvbihhY3Rpb24sIGVsZW1lbnQpIHtcbiAgICB2YXIgbW9kYWwgPSAkKCcjJyArICQoZWxlbWVudCkuZGF0YSgnY29uZmlybV9tb2RhbCcpKTtcbiAgICBpZiAobW9kYWwubGVuZ3RoICE9IDEpIHtcbiAgICAgIHJldHVybiB0cnVlO1xuICAgIH1cbiAgICBtb2RhbC5maXJzdCgpLm1vZGFsKCdzaG93Jyk7XG5cbiAgICByZXR1cm4gZmFsc2U7IC8vIGRvIG5vdCBhbGxvdyBhLmhyZWYgdG8gcmVsb2FkIHRoZSBwYWdlLiBUaGUgY29uZmlybSBtb2RhbCBkaWFsb2cgd2lsbCBkbyBpdCBhc3luYyBpZiBuZWVkZWQuXG4gIH07XG5cbiAgLyoqXG4gICAqIFVwZGF0ZSB0aGUgY29udGVudCBvZiBhIG1vZGFsIGFza2luZyBhIGNvbmZpcm1hdGlvbiBmb3IgUHJlc3RhVHJ1c3QgYW5kIG9wZW4gaXRcbiAgICpcbiAgICogQHBhcmFtIHthcnJheX0gcmVzdWx0IGNvbnRhaW5pbmcgbW9kdWxlIGRhdGFcbiAgICogQHJldHVybiB7dm9pZH1cbiAgICovXG4gIF9jb25maXJtUHJlc3RhVHJ1c3QocmVzdWx0KSB7XG4gICAgdmFyIHRoYXQgPSB0aGlzO1xuICAgIHZhciBtb2RhbCA9IHRoaXMuX3JlcGxhY2VQcmVzdGFUcnVzdFBsYWNlaG9sZGVycyhyZXN1bHQpO1xuXG4gICAgbW9kYWwuZmluZChcIi5wc3RydXN0LWluc3RhbGxcIikub2ZmKCdjbGljaycpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgLy8gRmluZCByZWxhdGVkIGZvcm0sIHVwZGF0ZSBpdCBhbmQgc3VibWl0IGl0XG4gICAgICB2YXIgaW5zdGFsbF9idXR0b24gPSAkKHRoYXQubW9kdWxlQWN0aW9uTWVudUluc3RhbGxMaW5rU2VsZWN0b3IsICcubW9kdWxlLWl0ZW1bZGF0YS10ZWNoLW5hbWU9XCInICsgcmVzdWx0Lm1vZHVsZS5hdHRyaWJ1dGVzLm5hbWUgKyAnXCJdJyk7XG4gICAgICB2YXIgZm9ybSA9IGluc3RhbGxfYnV0dG9uLnBhcmVudChcImZvcm1cIik7XG4gICAgICAkKCc8aW5wdXQ+JykuYXR0cih7XG4gICAgICAgIHR5cGU6ICdoaWRkZW4nLFxuICAgICAgICB2YWx1ZTogJzEnLFxuICAgICAgICBuYW1lOiAnYWN0aW9uUGFyYW1zW2NvbmZpcm1QcmVzdGFUcnVzdF0nXG4gICAgICB9KS5hcHBlbmRUbyhmb3JtKTtcblxuICAgICAgaW5zdGFsbF9idXR0b24uY2xpY2soKTtcbiAgICAgIG1vZGFsLm1vZGFsKCdoaWRlJyk7XG4gICAgfSk7XG5cbiAgICBtb2RhbC5tb2RhbCgpO1xuICB9O1xuXG4gIF9yZXBsYWNlUHJlc3RhVHJ1c3RQbGFjZWhvbGRlcnMocmVzdWx0KSB7XG4gICAgdmFyIG1vZGFsID0gJChcIiNtb2RhbC1wcmVzdGF0cnVzdFwiKTtcbiAgICB2YXIgbW9kdWxlID0gcmVzdWx0Lm1vZHVsZS5hdHRyaWJ1dGVzO1xuXG4gICAgaWYgKHJlc3VsdC5jb25maXJtYXRpb25fc3ViamVjdCAhPT0gJ1ByZXN0YVRydXN0JyB8fCAhbW9kYWwubGVuZ3RoKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdmFyIGFsZXJ0Q2xhc3MgPSBtb2R1bGUucHJlc3RhdHJ1c3Quc3RhdHVzID8gJ3N1Y2Nlc3MnIDogJ3dhcm5pbmcnO1xuXG4gICAgaWYgKG1vZHVsZS5wcmVzdGF0cnVzdC5jaGVja19saXN0LnByb3BlcnR5KSB7XG4gICAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYnRuLXByb3BlcnR5LW9rXCIpLnNob3coKTtcbiAgICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1idG4tcHJvcGVydHktbm9rXCIpLmhpZGUoKTtcbiAgICB9IGVsc2Uge1xuICAgICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWJ0bi1wcm9wZXJ0eS1va1wiKS5oaWRlKCk7XG4gICAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYnRuLXByb3BlcnR5LW5va1wiKS5zaG93KCk7XG4gICAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYnV5XCIpLmF0dHIoXCJocmVmXCIsIG1vZHVsZS51cmwpLnRvZ2dsZShtb2R1bGUudXJsICE9PSBudWxsKTtcbiAgICB9XG5cbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtaW1nXCIpLmF0dHIoe3NyYzogbW9kdWxlLmltZywgYWx0OiBtb2R1bGUubmFtZX0pO1xuICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1uYW1lXCIpLnRleHQobW9kdWxlLmRpc3BsYXlOYW1lKTtcbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYXV0aG9yXCIpLnRleHQobW9kdWxlLmF1dGhvcik7XG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWxhYmVsXCIpLmF0dHIoXCJjbGFzc1wiLCBcInRleHQtXCIgKyBhbGVydENsYXNzKS50ZXh0KG1vZHVsZS5wcmVzdGF0cnVzdC5zdGF0dXMgPyAnT0snIDogJ0tPJyk7XG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LW1lc3NhZ2VcIikuYXR0cihcImNsYXNzXCIsIFwiYWxlcnQgYWxlcnQtXCIrYWxlcnRDbGFzcyk7XG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LW1lc3NhZ2UgPiBwXCIpLnRleHQobW9kdWxlLnByZXN0YXRydXN0Lm1lc3NhZ2UpO1xuXG4gICAgcmV0dXJuIG1vZGFsO1xuICB9XG5cbiAgX2Rpc3BhdGNoUHJlRXZlbnQoYWN0aW9uLCBlbGVtZW50KSB7XG4gICAgdmFyIGV2ZW50ID0galF1ZXJ5LkV2ZW50KCdtb2R1bGVfY2FyZF9hY3Rpb25fZXZlbnQnKTtcblxuICAgICQoZWxlbWVudCkudHJpZ2dlcihldmVudCwgW2FjdGlvbl0pO1xuICAgIGlmIChldmVudC5pc1Byb3BhZ2F0aW9uU3RvcHBlZCgpICE9PSBmYWxzZSB8fCBldmVudC5pc0ltbWVkaWF0ZVByb3BhZ2F0aW9uU3RvcHBlZCgpICE9PSBmYWxzZSkge1xuICAgICAgcmV0dXJuIGZhbHNlOyAvLyBpZiBhbGwgaGFuZGxlcnMgaGF2ZSBub3QgYmVlbiBjYWxsZWQsIHRoZW4gc3RvcCBwcm9wYWdhdGlvbiBvZiB0aGUgY2xpY2sgZXZlbnQuXG4gICAgfVxuXG4gICAgcmV0dXJuIChldmVudC5yZXN1bHQgIT09IGZhbHNlKTsgLy8gZXhwbGljaXQgZmFsc2UgbXVzdCBiZSBzZXQgZnJvbSBoYW5kbGVycyB0byBzdG9wIHByb3BhZ2F0aW9uIG9mIHRoZSBjbGljayBldmVudC5cbiAgfTtcblxuICBfcmVxdWVzdFRvQ29udHJvbGxlcihhY3Rpb24sIGVsZW1lbnQsIGZvcmNlRGVsZXRpb24sIGRpc2FibGVDYWNoZUNsZWFyLCBjYWxsYmFjaykge1xuICAgIHZhciBzZWxmID0gdGhpcztcbiAgICB2YXIganFFbGVtZW50T2JqID0gZWxlbWVudC5jbG9zZXN0KHRoaXMubW9kdWxlSXRlbUFjdGlvbnNTZWxlY3Rvcik7XG4gICAgdmFyIGZvcm0gPSBlbGVtZW50LmNsb3Nlc3QoXCJmb3JtXCIpO1xuICAgIHZhciBzcGlubmVyT2JqID0gJChcIjxidXR0b24gY2xhc3M9XFxcImJ0bi1wcmltYXJ5LXJldmVyc2Ugb25jbGljayB1bmJpbmQgc3Bpbm5lciBcXFwiPjwvYnV0dG9uPlwiKTtcbiAgICB2YXIgdXJsID0gXCIvL1wiICsgd2luZG93LmxvY2F0aW9uLmhvc3QgKyBmb3JtLmF0dHIoXCJhY3Rpb25cIik7XG4gICAgdmFyIGFjdGlvblBhcmFtcyA9IGZvcm0uc2VyaWFsaXplQXJyYXkoKTtcblxuICAgIGlmIChmb3JjZURlbGV0aW9uID09PSBcInRydWVcIiB8fCBmb3JjZURlbGV0aW9uID09PSB0cnVlKSB7XG4gICAgICBhY3Rpb25QYXJhbXMucHVzaCh7bmFtZTogXCJhY3Rpb25QYXJhbXNbZGVsZXRpb25dXCIsIHZhbHVlOiB0cnVlfSk7XG4gICAgfVxuICAgIGlmIChkaXNhYmxlQ2FjaGVDbGVhciA9PT0gXCJ0cnVlXCIgfHwgZGlzYWJsZUNhY2hlQ2xlYXIgPT09IHRydWUpIHtcbiAgICAgIGFjdGlvblBhcmFtcy5wdXNoKHtuYW1lOiBcImFjdGlvblBhcmFtc1tjYWNoZUNsZWFyRW5hYmxlZF1cIiwgdmFsdWU6IDB9KTtcbiAgICB9XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdXJsOiB1cmwsXG4gICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICBkYXRhOiBhY3Rpb25QYXJhbXMsXG4gICAgICBiZWZvcmVTZW5kOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgIGpxRWxlbWVudE9iai5oaWRlKCk7XG4gICAgICAgIGpxRWxlbWVudE9iai5hZnRlcihzcGlubmVyT2JqKTtcbiAgICAgIH1cbiAgICB9KS5kb25lKGZ1bmN0aW9uIChyZXN1bHQpIHtcbiAgICAgIGlmICh0eXBlb2YgcmVzdWx0ID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogXCJObyBhbnN3ZXIgcmVjZWl2ZWQgZnJvbSBzZXJ2ZXJcIn0pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdmFyIG1vZHVsZVRlY2hOYW1lID0gT2JqZWN0LmtleXMocmVzdWx0KVswXTtcblxuICAgICAgICBpZiAocmVzdWx0W21vZHVsZVRlY2hOYW1lXS5zdGF0dXMgPT09IGZhbHNlKSB7XG4gICAgICAgICAgaWYgKHR5cGVvZiByZXN1bHRbbW9kdWxlVGVjaE5hbWVdLmNvbmZpcm1hdGlvbl9zdWJqZWN0ICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgICAgc2VsZi5fY29uZmlybVByZXN0YVRydXN0KHJlc3VsdFttb2R1bGVUZWNoTmFtZV0pO1xuICAgICAgICAgIH1cblxuICAgICAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IHJlc3VsdFttb2R1bGVUZWNoTmFtZV0ubXNnfSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgJC5ncm93bC5ub3RpY2Uoe21lc3NhZ2U6IHJlc3VsdFttb2R1bGVUZWNoTmFtZV0ubXNnfSk7XG5cbiAgICAgICAgICB2YXIgYWx0ZXJlZFNlbGVjdG9yID0gc2VsZi5fZ2V0TW9kdWxlSXRlbVNlbGVjdG9yKCkucmVwbGFjZSgnLicsICcnKTtcbiAgICAgICAgICB2YXIgbWFpbkVsZW1lbnQgPSBudWxsO1xuXG4gICAgICAgICAgaWYgKGFjdGlvbiA9PSBcInVuaW5zdGFsbFwiKSB7XG4gICAgICAgICAgICBtYWluRWxlbWVudCA9IGpxRWxlbWVudE9iai5jbG9zZXN0KCcuJyArIGFsdGVyZWRTZWxlY3Rvcik7XG4gICAgICAgICAgICBtYWluRWxlbWVudC5yZW1vdmUoKTtcblxuICAgICAgICAgICAgQk9FdmVudC5lbWl0RXZlbnQoXCJNb2R1bGUgVW5pbnN0YWxsZWRcIiwgXCJDdXN0b21FdmVudFwiKTtcbiAgICAgICAgICB9IGVsc2UgaWYgKGFjdGlvbiA9PSBcImRpc2FibGVcIikge1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQgPSBqcUVsZW1lbnRPYmouY2xvc2VzdCgnLicgKyBhbHRlcmVkU2VsZWN0b3IpO1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQuYWRkQ2xhc3MoYWx0ZXJlZFNlbGVjdG9yICsgJy1pc05vdEFjdGl2ZScpO1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQuYXR0cignZGF0YS1hY3RpdmUnLCAnMCcpO1xuXG4gICAgICAgICAgICBCT0V2ZW50LmVtaXRFdmVudChcIk1vZHVsZSBEaXNhYmxlZFwiLCBcIkN1c3RvbUV2ZW50XCIpO1xuICAgICAgICAgIH0gZWxzZSBpZiAoYWN0aW9uID09IFwiZW5hYmxlXCIpIHtcbiAgICAgICAgICAgIG1haW5FbGVtZW50ID0ganFFbGVtZW50T2JqLmNsb3Nlc3QoJy4nICsgYWx0ZXJlZFNlbGVjdG9yKTtcbiAgICAgICAgICAgIG1haW5FbGVtZW50LnJlbW92ZUNsYXNzKGFsdGVyZWRTZWxlY3RvciArICctaXNOb3RBY3RpdmUnKTtcbiAgICAgICAgICAgIG1haW5FbGVtZW50LmF0dHIoJ2RhdGEtYWN0aXZlJywgJzEnKTtcblxuICAgICAgICAgICAgQk9FdmVudC5lbWl0RXZlbnQoXCJNb2R1bGUgRW5hYmxlZFwiLCBcIkN1c3RvbUV2ZW50XCIpO1xuICAgICAgICAgIH1cblxuICAgICAgICAgIGpxRWxlbWVudE9iai5yZXBsYWNlV2l0aChyZXN1bHRbbW9kdWxlVGVjaE5hbWVdLmFjdGlvbl9tZW51X2h0bWwpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfSkuZmFpbChmdW5jdGlvbigpIHtcbiAgICAgIGNvbnN0IG1vZHVsZUl0ZW0gPSBqcUVsZW1lbnRPYmouY2xvc2VzdCgnbW9kdWxlLWl0ZW0tbGlzdCcpO1xuICAgICAgY29uc3QgdGVjaE5hbWUgPSBtb2R1bGVJdGVtLmRhdGEoJ3RlY2hOYW1lJyk7XG4gICAgICAkLmdyb3dsLmVycm9yKHttZXNzYWdlOiBcIkNvdWxkIG5vdCBwZXJmb3JtIGFjdGlvbiBcIithY3Rpb24rXCIgZm9yIG1vZHVsZSBcIit0ZWNoTmFtZX0pO1xuICAgIH0pLmFsd2F5cyhmdW5jdGlvbiAoKSB7XG4gICAgICBqcUVsZW1lbnRPYmouZmFkZUluKCk7XG4gICAgICBzcGlubmVyT2JqLnJlbW92ZSgpO1xuICAgICAgaWYgKGNhbGxiYWNrKSB7XG4gICAgICAgIGNhbGxiYWNrKCk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICByZXR1cm4gZmFsc2U7XG4gIH07XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogTW9kdWxlIEFkbWluIFBhZ2UgQ29udHJvbGxlci5cbiAqIEBjb25zdHJ1Y3RvclxuICovXG5jbGFzcyBBZG1pbk1vZHVsZUNvbnRyb2xsZXIge1xuICAvKipcbiAgICogSW5pdGlhbGl6ZSBhbGwgbGlzdGVuZXJzIGFuZCBiaW5kIGV2ZXJ5dGhpbmdcbiAgICogQG1ldGhvZCBpbml0XG4gICAqIEBtZW1iZXJvZiBBZG1pbk1vZHVsZVxuICAgKi9cbiAgY29uc3RydWN0b3IobW9kdWxlQ2FyZENvbnRyb2xsZXIpIHtcbiAgICB0aGlzLm1vZHVsZUNhcmRDb250cm9sbGVyID0gbW9kdWxlQ2FyZENvbnRyb2xsZXI7XG5cbiAgICB0aGlzLkRFRkFVTFRfTUFYX1JFQ0VOVExZX1VTRUQgPSAxMDtcbiAgICB0aGlzLkRFRkFVTFRfTUFYX1BFUl9DQVRFR09SSUVTID0gNjtcbiAgICB0aGlzLkRJU1BMQVlfR1JJRCA9ICdncmlkJztcbiAgICB0aGlzLkRJU1BMQVlfTElTVCA9ICdsaXN0JztcbiAgICB0aGlzLkNBVEVHT1JZX1JFQ0VOVExZX1VTRUQgPSAncmVjZW50bHktdXNlZCc7XG5cbiAgICB0aGlzLmN1cnJlbnRDYXRlZ29yeURpc3BsYXkgPSB7fTtcbiAgICB0aGlzLmN1cnJlbnREaXNwbGF5ID0gJyc7XG4gICAgdGhpcy5pc0NhdGVnb3J5R3JpZERpc3BsYXllZCA9IGZhbHNlO1xuICAgIHRoaXMuY3VycmVudFRhZ3NMaXN0ID0gW107XG4gICAgdGhpcy5jdXJyZW50UmVmQ2F0ZWdvcnkgPSBudWxsO1xuICAgIHRoaXMuY3VycmVudFJlZlN0YXR1cyA9IG51bGw7XG4gICAgdGhpcy5jdXJyZW50U29ydGluZyA9IG51bGw7XG4gICAgdGhpcy5iYXNlQWRkb25zVXJsID0gJ2h0dHBzOi8vYWRkb25zLnByZXN0YXNob3AuY29tLyc7XG4gICAgdGhpcy5wc3RhZ2dlcklucHV0ID0gbnVsbDtcbiAgICB0aGlzLmxhc3RCdWxrQWN0aW9uID0gbnVsbDtcbiAgICB0aGlzLmlzVXBsb2FkU3RhcnRlZCA9IGZhbHNlO1xuXG4gICAgdGhpcy5yZWNlbnRseVVzZWRTZWxlY3RvciA9ICcjbW9kdWxlLXJlY2VudGx5LXVzZWQtbGlzdCAubW9kdWxlcy1saXN0JztcblxuICAgIC8qKlxuICAgICAqIExvYWRlZCBtb2R1bGVzIGxpc3QuXG4gICAgICogQ29udGFpbmluZyB0aGUgY2FyZCBhbmQgbGlzdCBkaXNwbGF5LlxuICAgICAqIEB0eXBlIHtBcnJheX1cbiAgICAgKi9cbiAgICB0aGlzLm1vZHVsZXNMaXN0ID0gW107XG4gICAgdGhpcy5hZGRvbnNDYXJkR3JpZCA9IG51bGw7XG4gICAgdGhpcy5hZGRvbnNDYXJkTGlzdCA9IG51bGw7XG5cbiAgICB0aGlzLm1vZHVsZVNob3J0TGlzdCA9ICcubW9kdWxlLXNob3J0LWxpc3QnO1xuICAgIC8vIFNlZSBtb3JlICYgU2VlIGxlc3Mgc2VsZWN0b3JcbiAgICB0aGlzLnNlZU1vcmVTZWxlY3RvciA9ICcuc2VlLW1vcmUnO1xuICAgIHRoaXMuc2VlTGVzc1NlbGVjdG9yID0gJy5zZWUtbGVzcyc7XG5cbiAgICAvLyBTZWxlY3RvcnMgaW50byB2YXJzIHRvIG1ha2UgaXQgZWFzaWVyIHRvIGNoYW5nZSB0aGVtIHdoaWxlIGtlZXBpbmcgc2FtZSBjb2RlIGxvZ2ljXG4gICAgdGhpcy5tb2R1bGVJdGVtR3JpZFNlbGVjdG9yID0gJy5tb2R1bGUtaXRlbS1ncmlkJztcbiAgICB0aGlzLm1vZHVsZUl0ZW1MaXN0U2VsZWN0b3IgPSAnLm1vZHVsZS1pdGVtLWxpc3QnO1xuICAgIHRoaXMuY2F0ZWdvcnlTZWxlY3RvckxhYmVsU2VsZWN0b3IgPSAnLm1vZHVsZS1jYXRlZ29yeS1zZWxlY3Rvci1sYWJlbCc7XG4gICAgdGhpcy5jYXRlZ29yeVNlbGVjdG9yID0gJy5tb2R1bGUtY2F0ZWdvcnktc2VsZWN0b3InO1xuICAgIHRoaXMuY2F0ZWdvcnlJdGVtU2VsZWN0b3IgPSAnLm1vZHVsZS1jYXRlZ29yeS1tZW51JztcbiAgICB0aGlzLmFkZG9uc0xvZ2luQnV0dG9uU2VsZWN0b3IgPSAnI2FkZG9uc19sb2dpbl9idG4nO1xuICAgIHRoaXMuY2F0ZWdvcnlSZXNldEJ0blNlbGVjdG9yID0gJy5tb2R1bGUtY2F0ZWdvcnktcmVzZXQnO1xuICAgIHRoaXMubW9kdWxlSW5zdGFsbEJ0blNlbGVjdG9yID0gJ2lucHV0Lm1vZHVsZS1pbnN0YWxsLWJ0bic7XG4gICAgdGhpcy5tb2R1bGVTb3J0aW5nRHJvcGRvd25TZWxlY3RvciA9ICcubW9kdWxlLXNvcnRpbmctYXV0aG9yIHNlbGVjdCc7XG4gICAgdGhpcy5jYXRlZ29yeUdyaWRTZWxlY3RvciA9ICcjbW9kdWxlcy1jYXRlZ29yaWVzLWdyaWQnO1xuICAgIHRoaXMuY2F0ZWdvcnlHcmlkSXRlbVNlbGVjdG9yID0gJy5tb2R1bGUtY2F0ZWdvcnktaXRlbSc7XG4gICAgdGhpcy5hZGRvbkl0ZW1HcmlkU2VsZWN0b3IgPSAnLm1vZHVsZS1hZGRvbnMtaXRlbS1ncmlkJztcbiAgICB0aGlzLmFkZG9uSXRlbUxpc3RTZWxlY3RvciA9ICcubW9kdWxlLWFkZG9ucy1pdGVtLWxpc3QnO1xuXG4gICAgLy8gVXBncmFkZSBBbGwgc2VsZWN0b3JzXG4gICAgdGhpcy51cGdyYWRlQWxsU291cmNlID0gJy5tb2R1bGVfYWN0aW9uX21lbnVfdXBncmFkZV9hbGwnO1xuICAgIHRoaXMudXBncmFkZUFsbFRhcmdldHMgPSAnI21vZHVsZXMtbGlzdC1jb250YWluZXItdXBkYXRlIC5tb2R1bGVfYWN0aW9uX21lbnVfdXBncmFkZTp2aXNpYmxlJztcblxuICAgIC8vIEJ1bGsgYWN0aW9uIHNlbGVjdG9yc1xuICAgIHRoaXMuYnVsa0FjdGlvbkRyb3BEb3duU2VsZWN0b3IgPSAnLm1vZHVsZS1idWxrLWFjdGlvbnMnO1xuICAgIHRoaXMuYnVsa0l0ZW1TZWxlY3RvciA9ICcubW9kdWxlLWJ1bGstbWVudSc7XG4gICAgdGhpcy5idWxrQWN0aW9uQ2hlY2tib3hMaXN0U2VsZWN0b3IgPSAnLm1vZHVsZS1jaGVja2JveC1idWxrLWxpc3QgaW5wdXQnO1xuICAgIHRoaXMuYnVsa0FjdGlvbkNoZWNrYm94R3JpZFNlbGVjdG9yID0gJy5tb2R1bGUtY2hlY2tib3gtYnVsay1ncmlkIGlucHV0JztcbiAgICB0aGlzLmNoZWNrZWRCdWxrQWN0aW9uTGlzdFNlbGVjdG9yID0gYCR7dGhpcy5idWxrQWN0aW9uQ2hlY2tib3hMaXN0U2VsZWN0b3J9OmNoZWNrZWRgO1xuICAgIHRoaXMuY2hlY2tlZEJ1bGtBY3Rpb25HcmlkU2VsZWN0b3IgPSBgJHt0aGlzLmJ1bGtBY3Rpb25DaGVja2JveEdyaWRTZWxlY3Rvcn06Y2hlY2tlZGA7XG4gICAgdGhpcy5idWxrQWN0aW9uQ2hlY2tib3hTZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWJ1bGstY2hlY2tib3gnO1xuICAgIHRoaXMuYnVsa0NvbmZpcm1Nb2RhbFNlbGVjdG9yID0gJyNtb2R1bGUtbW9kYWwtYnVsay1jb25maXJtJztcbiAgICB0aGlzLmJ1bGtDb25maXJtTW9kYWxBY3Rpb25OYW1lU2VsZWN0b3IgPSAnI21vZHVsZS1tb2RhbC1idWxrLWNvbmZpcm0tYWN0aW9uLW5hbWUnO1xuICAgIHRoaXMuYnVsa0NvbmZpcm1Nb2RhbExpc3RTZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWJ1bGstY29uZmlybS1saXN0JztcbiAgICB0aGlzLmJ1bGtDb25maXJtTW9kYWxBY2tCdG5TZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWNvbmZpcm0tYnVsay1hY2snO1xuXG4gICAgLy8gUGxhY2Vob2xkZXJzXG4gICAgdGhpcy5wbGFjZWhvbGRlckdsb2JhbFNlbGVjdG9yID0gJy5tb2R1bGUtcGxhY2Vob2xkZXJzLXdyYXBwZXInO1xuICAgIHRoaXMucGxhY2Vob2xkZXJGYWlsdXJlR2xvYmFsU2VsZWN0b3IgPSAnLm1vZHVsZS1wbGFjZWhvbGRlcnMtZmFpbHVyZSc7XG4gICAgdGhpcy5wbGFjZWhvbGRlckZhaWx1cmVNc2dTZWxlY3RvciA9ICcubW9kdWxlLXBsYWNlaG9sZGVycy1mYWlsdXJlLW1zZyc7XG4gICAgdGhpcy5wbGFjZWhvbGRlckZhaWx1cmVSZXRyeUJ0blNlbGVjdG9yID0gJyNtb2R1bGUtcGxhY2Vob2xkZXJzLWZhaWx1cmUtcmV0cnknO1xuXG4gICAgLy8gTW9kdWxlJ3Mgc3RhdHVzZXMgc2VsZWN0b3JzXG4gICAgdGhpcy5zdGF0dXNTZWxlY3RvckxhYmVsU2VsZWN0b3IgPSAnLm1vZHVsZS1zdGF0dXMtc2VsZWN0b3ItbGFiZWwnO1xuICAgIHRoaXMuc3RhdHVzSXRlbVNlbGVjdG9yID0gJy5tb2R1bGUtc3RhdHVzLW1lbnUnO1xuICAgIHRoaXMuc3RhdHVzUmVzZXRCdG5TZWxlY3RvciA9ICcubW9kdWxlLXN0YXR1cy1yZXNldCc7XG5cbiAgICAvLyBTZWxlY3RvcnMgZm9yIE1vZHVsZSBJbXBvcnQgYW5kIEFkZG9ucyBjb25uZWN0XG4gICAgdGhpcy5hZGRvbnNDb25uZWN0TW9kYWxCdG5TZWxlY3RvciA9ICcjcGFnZS1oZWFkZXItZGVzYy1jb25maWd1cmF0aW9uLWFkZG9uc19jb25uZWN0JztcbiAgICB0aGlzLmFkZG9uc0xvZ291dE1vZGFsQnRuU2VsZWN0b3IgPSAnI3BhZ2UtaGVhZGVyLWRlc2MtY29uZmlndXJhdGlvbi1hZGRvbnNfbG9nb3V0JztcbiAgICB0aGlzLmFkZG9uc0ltcG9ydE1vZGFsQnRuU2VsZWN0b3IgPSAnI3BhZ2UtaGVhZGVyLWRlc2MtY29uZmlndXJhdGlvbi1hZGRfbW9kdWxlJztcbiAgICB0aGlzLmRyb3Bab25lTW9kYWxTZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWltcG9ydCc7XG4gICAgdGhpcy5kcm9wWm9uZU1vZGFsRm9vdGVyU2VsZWN0b3IgPSAnI21vZHVsZS1tb2RhbC1pbXBvcnQgLm1vZGFsLWZvb3Rlcic7XG4gICAgdGhpcy5kcm9wWm9uZUltcG9ydFpvbmVTZWxlY3RvciA9ICcjaW1wb3J0RHJvcHpvbmUnO1xuICAgIHRoaXMuYWRkb25zQ29ubmVjdE1vZGFsU2VsZWN0b3IgPSAnI21vZHVsZS1tb2RhbC1hZGRvbnMtY29ubmVjdCc7XG4gICAgdGhpcy5hZGRvbnNMb2dvdXRNb2RhbFNlbGVjdG9yID0gJyNtb2R1bGUtbW9kYWwtYWRkb25zLWxvZ291dCc7XG4gICAgdGhpcy5hZGRvbnNDb25uZWN0Rm9ybSA9ICcjYWRkb25zLWNvbm5lY3QtZm9ybSc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRNb2RhbENsb3NlQnRuID0gJyNtb2R1bGUtbW9kYWwtaW1wb3J0LWNsb3NpbmctY3Jvc3MnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0U3RhcnRTZWxlY3RvciA9ICcubW9kdWxlLWltcG9ydC1zdGFydCc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRQcm9jZXNzaW5nU2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtcHJvY2Vzc2luZyc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRTdWNjZXNzU2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtc3VjY2Vzcyc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRTdWNjZXNzQ29uZmlndXJlQnRuU2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtc3VjY2Vzcy1jb25maWd1cmUnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZVNlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LWZhaWx1cmUnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZVJldHJ5U2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtZmFpbHVyZS1yZXRyeSc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRGYWlsdXJlRGV0YWlsc0J0blNlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LWZhaWx1cmUtZGV0YWlscy1hY3Rpb24nO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0U2VsZWN0RmlsZU1hbnVhbFNlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LXN0YXJ0LXNlbGVjdC1tYW51YWwnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZU1zZ0RldGFpbHNTZWxlY3RvciA9ICcubW9kdWxlLWltcG9ydC1mYWlsdXJlLWRldGFpbHMnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0Q29uZmlybVNlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LWNvbmZpcm0nO1xuXG4gICAgdGhpcy5pbml0U29ydGluZ0Ryb3Bkb3duKCk7XG4gICAgdGhpcy5pbml0Qk9FdmVudFJlZ2lzdGVyaW5nKCk7XG4gICAgdGhpcy5pbml0Q3VycmVudERpc3BsYXkoKTtcbiAgICB0aGlzLmluaXRTb3J0aW5nRGlzcGxheVN3aXRjaCgpO1xuICAgIHRoaXMuaW5pdEJ1bGtEcm9wZG93bigpO1xuICAgIHRoaXMuaW5pdFNlYXJjaEJsb2NrKCk7XG4gICAgdGhpcy5pbml0Q2F0ZWdvcnlTZWxlY3QoKTtcbiAgICB0aGlzLmluaXRDYXRlZ29yaWVzR3JpZCgpO1xuICAgIHRoaXMuaW5pdEFjdGlvbkJ1dHRvbnMoKTtcbiAgICB0aGlzLmluaXRBZGRvbnNTZWFyY2goKTtcbiAgICB0aGlzLmluaXRBZGRvbnNDb25uZWN0KCk7XG4gICAgdGhpcy5pbml0QWRkTW9kdWxlQWN0aW9uKCk7XG4gICAgdGhpcy5pbml0RHJvcHpvbmUoKTtcbiAgICB0aGlzLmluaXRQYWdlQ2hhbmdlUHJvdGVjdGlvbigpO1xuICAgIHRoaXMuaW5pdFBsYWNlaG9sZGVyTWVjaGFuaXNtKCk7XG4gICAgdGhpcy5pbml0RmlsdGVyU3RhdHVzRHJvcGRvd24oKTtcbiAgICB0aGlzLmZldGNoTW9kdWxlc0xpc3QoKTtcbiAgICB0aGlzLmdldE5vdGlmaWNhdGlvbnNDb3VudCgpO1xuICAgIHRoaXMuaW5pdGlhbGl6ZVNlZU1vcmUoKTtcbiAgfVxuXG4gIGluaXRGaWx0ZXJTdGF0dXNEcm9wZG93bigpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBib2R5ID0gJCgnYm9keScpO1xuICAgIGJvZHkub24oJ2NsaWNrJywgc2VsZi5zdGF0dXNJdGVtU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIC8vIEdldCBkYXRhIGZyb20gbGkgRE9NIGlucHV0XG4gICAgICBzZWxmLmN1cnJlbnRSZWZTdGF0dXMgPSBwYXJzZUludCgkKHRoaXMpLmRhdGEoJ3N0YXR1cy1yZWYnKSwgMTApO1xuICAgICAgLy8gQ2hhbmdlIGRyb3Bkb3duIGxhYmVsIHRvIHNldCBpdCB0byB0aGUgY3VycmVudCBzdGF0dXMnIGRpc3BsYXluYW1lXG4gICAgICAkKHNlbGYuc3RhdHVzU2VsZWN0b3JMYWJlbFNlbGVjdG9yKS50ZXh0KCQodGhpcykuZmluZCgnYTpmaXJzdCcpLnRleHQoKSk7XG4gICAgICAkKHNlbGYuc3RhdHVzUmVzZXRCdG5TZWxlY3Rvcikuc2hvdygpO1xuICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgfSk7XG5cbiAgICBib2R5Lm9uKCdjbGljaycsIHNlbGYuc3RhdHVzUmVzZXRCdG5TZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgJChzZWxmLnN0YXR1c1NlbGVjdG9yTGFiZWxTZWxlY3RvcikudGV4dCgkKHRoaXMpLmZpbmQoJ2EnKS50ZXh0KCkpO1xuICAgICAgJCh0aGlzKS5oaWRlKCk7XG4gICAgICBzZWxmLmN1cnJlbnRSZWZTdGF0dXMgPSBudWxsO1xuICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgfSk7XG4gIH1cblxuICBpbml0QnVsa0Ryb3Bkb3duKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0IGJvZHkgPSAkKCdib2R5Jyk7XG5cblxuICAgIGJvZHkub24oJ2NsaWNrJywgc2VsZi5nZXRCdWxrQ2hlY2tib3hlc1NlbGVjdG9yKCksICgpID0+IHtcbiAgICAgIGNvbnN0IHNlbGVjdG9yID0gJChzZWxmLmJ1bGtBY3Rpb25Ecm9wRG93blNlbGVjdG9yKTtcbiAgICAgIGlmICgkKHNlbGYuZ2V0QnVsa0NoZWNrYm94ZXNDaGVja2VkU2VsZWN0b3IoKSkubGVuZ3RoID4gMCkge1xuICAgICAgICBzZWxlY3Rvci5jbG9zZXN0KCcubW9kdWxlLXRvcC1tZW51LWl0ZW0nKVxuICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHNlbGVjdG9yLmNsb3Nlc3QoJy5tb2R1bGUtdG9wLW1lbnUtaXRlbScpXG4gICAgICAgICAgICAgICAgLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgYm9keS5vbignY2xpY2snLCBzZWxmLmJ1bGtJdGVtU2VsZWN0b3IsIGZ1bmN0aW9uIGluaXRpYWxpemVCb2R5Q2hhbmdlKCkge1xuICAgICAgaWYgKCQoc2VsZi5nZXRCdWxrQ2hlY2tib3hlc0NoZWNrZWRTZWxlY3RvcigpKS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgJC5ncm93bC53YXJuaW5nKHttZXNzYWdlOiB3aW5kb3cudHJhbnNsYXRlX2phdmFzY3JpcHRzWydCdWxrIEFjdGlvbiAtIE9uZSBtb2R1bGUgbWluaW11bSddfSk7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgc2VsZi5sYXN0QnVsa0FjdGlvbiA9ICQodGhpcykuZGF0YSgncmVmJyk7XG4gICAgICBjb25zdCBtb2R1bGVzTGlzdFN0cmluZyA9IHNlbGYuYnVpbGRCdWxrQWN0aW9uTW9kdWxlTGlzdCgpO1xuICAgICAgY29uc3QgYWN0aW9uU3RyaW5nID0gJCh0aGlzKS5maW5kKCc6Y2hlY2tlZCcpLnRleHQoKS50b0xvd2VyQ2FzZSgpO1xuICAgICAgJChzZWxmLmJ1bGtDb25maXJtTW9kYWxMaXN0U2VsZWN0b3IpLmh0bWwobW9kdWxlc0xpc3RTdHJpbmcpO1xuICAgICAgJChzZWxmLmJ1bGtDb25maXJtTW9kYWxBY3Rpb25OYW1lU2VsZWN0b3IpLnRleHQoYWN0aW9uU3RyaW5nKTtcblxuICAgICAgaWYgKHNlbGYubGFzdEJ1bGtBY3Rpb24gPT09ICdidWxrLXVuaW5zdGFsbCcpIHtcbiAgICAgICAgJChzZWxmLmJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdG9yKS5zaG93KCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkKHNlbGYuYnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgIH1cblxuICAgICAgJChzZWxmLmJ1bGtDb25maXJtTW9kYWxTZWxlY3RvcikubW9kYWwoJ3Nob3cnKTtcbiAgICB9KTtcblxuICAgIGJvZHkub24oJ2NsaWNrJywgdGhpcy5idWxrQ29uZmlybU1vZGFsQWNrQnRuU2VsZWN0b3IsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgJChzZWxmLmJ1bGtDb25maXJtTW9kYWxTZWxlY3RvcikubW9kYWwoJ2hpZGUnKTtcbiAgICAgIHNlbGYuZG9CdWxrQWN0aW9uKHNlbGYubGFzdEJ1bGtBY3Rpb24pO1xuICAgIH0pO1xuICB9XG5cbiAgaW5pdEJPRXZlbnRSZWdpc3RlcmluZygpIHtcbiAgICB3aW5kb3cuQk9FdmVudC5vbignTW9kdWxlIERpc2FibGVkJywgdGhpcy5vbk1vZHVsZURpc2FibGVkLCB0aGlzKTtcbiAgICB3aW5kb3cuQk9FdmVudC5vbignTW9kdWxlIFVuaW5zdGFsbGVkJywgdGhpcy51cGRhdGVUb3RhbFJlc3VsdHMsIHRoaXMpO1xuICB9XG5cbiAgb25Nb2R1bGVEaXNhYmxlZCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBtb2R1bGVJdGVtU2VsZWN0b3IgPSBzZWxmLmdldE1vZHVsZUl0ZW1TZWxlY3RvcigpO1xuXG4gICAgJCgnLm1vZHVsZXMtbGlzdCcpLmVhY2goZnVuY3Rpb24gc2Nhbk1vZHVsZXNMaXN0KCkge1xuICAgICAgc2VsZi51cGRhdGVUb3RhbFJlc3VsdHMoKTtcbiAgICB9KTtcbiAgfVxuXG4gIGluaXRQbGFjZWhvbGRlck1lY2hhbmlzbSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBpZiAoJChzZWxmLnBsYWNlaG9sZGVyR2xvYmFsU2VsZWN0b3IpLmxlbmd0aCkge1xuICAgICAgc2VsZi5hamF4TG9hZFBhZ2UoKTtcbiAgICB9XG5cbiAgICAvLyBSZXRyeSBsb2FkaW5nIG1lY2hhbmlzbVxuICAgICQoJ2JvZHknKS5vbignY2xpY2snLCBzZWxmLnBsYWNlaG9sZGVyRmFpbHVyZVJldHJ5QnRuU2VsZWN0b3IsICgpID0+IHtcbiAgICAgICQoc2VsZi5wbGFjZWhvbGRlckZhaWx1cmVHbG9iYWxTZWxlY3RvcikuZmFkZU91dCgpO1xuICAgICAgJChzZWxmLnBsYWNlaG9sZGVyR2xvYmFsU2VsZWN0b3IpLmZhZGVJbigpO1xuICAgICAgc2VsZi5hamF4TG9hZFBhZ2UoKTtcbiAgICB9KTtcbiAgfVxuXG4gIGFqYXhMb2FkUGFnZSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQuYWpheCh7XG4gICAgICBtZXRob2Q6ICdHRVQnLFxuICAgICAgdXJsOiB3aW5kb3cubW9kdWxlVVJMcy5jYXRhbG9nUmVmcmVzaCxcbiAgICB9KS5kb25lKChyZXNwb25zZSkgPT4ge1xuICAgICAgaWYgKHJlc3BvbnNlLnN0YXR1cyA9PT0gdHJ1ZSkge1xuICAgICAgICBpZiAodHlwZW9mIHJlc3BvbnNlLmRvbUVsZW1lbnRzID09PSAndW5kZWZpbmVkJykgcmVzcG9uc2UuZG9tRWxlbWVudHMgPSBudWxsO1xuICAgICAgICBpZiAodHlwZW9mIHJlc3BvbnNlLm1zZyA9PT0gJ3VuZGVmaW5lZCcpIHJlc3BvbnNlLm1zZyA9IG51bGw7XG5cbiAgICAgICAgY29uc3Qgc3R5bGVzaGVldCA9IGRvY3VtZW50LnN0eWxlU2hlZXRzWzBdO1xuICAgICAgICBjb25zdCBzdHlsZXNoZWV0UnVsZSA9ICd7ZGlzcGxheTogbm9uZX0nO1xuICAgICAgICBjb25zdCBtb2R1bGVHbG9iYWxTZWxlY3RvciA9ICcubW9kdWxlcy1saXN0JztcbiAgICAgICAgY29uc3QgbW9kdWxlU29ydGluZ1NlbGVjdG9yID0gJy5tb2R1bGUtc29ydGluZy1tZW51JztcbiAgICAgICAgY29uc3QgcmVxdWlyZWRTZWxlY3RvckNvbWJpbmF0aW9uID0gYCR7bW9kdWxlR2xvYmFsU2VsZWN0b3J9LCR7bW9kdWxlU29ydGluZ1NlbGVjdG9yfWA7XG5cbiAgICAgICAgaWYgKHN0eWxlc2hlZXQuaW5zZXJ0UnVsZSkge1xuICAgICAgICAgIHN0eWxlc2hlZXQuaW5zZXJ0UnVsZShcbiAgICAgICAgICAgIHJlcXVpcmVkU2VsZWN0b3JDb21iaW5hdGlvbiArXG4gICAgICAgICAgICBzdHlsZXNoZWV0UnVsZSwgc3R5bGVzaGVldC5jc3NSdWxlcy5sZW5ndGhcbiAgICAgICAgICApO1xuICAgICAgICB9IGVsc2UgaWYgKHN0eWxlc2hlZXQuYWRkUnVsZSkge1xuICAgICAgICAgIHN0eWxlc2hlZXQuYWRkUnVsZShcbiAgICAgICAgICAgIHJlcXVpcmVkU2VsZWN0b3JDb21iaW5hdGlvbixcbiAgICAgICAgICAgIHN0eWxlc2hlZXRSdWxlLFxuICAgICAgICAgICAgLTFcbiAgICAgICAgICApO1xuICAgICAgICB9XG5cbiAgICAgICAgJChzZWxmLnBsYWNlaG9sZGVyR2xvYmFsU2VsZWN0b3IpLmZhZGVPdXQoODAwLCAoKSA9PiB7XG4gICAgICAgICAgJC5lYWNoKHJlc3BvbnNlLmRvbUVsZW1lbnRzLCAoaW5kZXgsIGVsZW1lbnQpID0+IHtcbiAgICAgICAgICAgICQoZWxlbWVudC5zZWxlY3RvcikuYXBwZW5kKGVsZW1lbnQuY29udGVudCk7XG4gICAgICAgICAgfSk7XG4gICAgICAgICAgJChtb2R1bGVHbG9iYWxTZWxlY3RvcikuZmFkZUluKDgwMCkuY3NzKCdkaXNwbGF5JywgJ2ZsZXgnKTtcbiAgICAgICAgICAkKG1vZHVsZVNvcnRpbmdTZWxlY3RvcikuZmFkZUluKDgwMCk7XG4gICAgICAgICAgJCgnW2RhdGEtdG9nZ2xlPVwicG9wb3ZlclwiXScpLnBvcG92ZXIoKTtcbiAgICAgICAgICBzZWxmLmluaXRDdXJyZW50RGlzcGxheSgpO1xuICAgICAgICAgIHNlbGYuZmV0Y2hNb2R1bGVzTGlzdCgpO1xuICAgICAgICB9KTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICQoc2VsZi5wbGFjZWhvbGRlckdsb2JhbFNlbGVjdG9yKS5mYWRlT3V0KDgwMCwgKCkgPT4ge1xuICAgICAgICAgICQoc2VsZi5wbGFjZWhvbGRlckZhaWx1cmVNc2dTZWxlY3RvcikudGV4dChyZXNwb25zZS5tc2cpO1xuICAgICAgICAgICQoc2VsZi5wbGFjZWhvbGRlckZhaWx1cmVHbG9iYWxTZWxlY3RvcikuZmFkZUluKDgwMCk7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH0pLmZhaWwoKHJlc3BvbnNlKSA9PiB7XG4gICAgICAkKHNlbGYucGxhY2Vob2xkZXJHbG9iYWxTZWxlY3RvcikuZmFkZU91dCg4MDAsICgpID0+IHtcbiAgICAgICAgJChzZWxmLnBsYWNlaG9sZGVyRmFpbHVyZU1zZ1NlbGVjdG9yKS50ZXh0KHJlc3BvbnNlLnN0YXR1c1RleHQpO1xuICAgICAgICAkKHNlbGYucGxhY2Vob2xkZXJGYWlsdXJlR2xvYmFsU2VsZWN0b3IpLmZhZGVJbig4MDApO1xuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cblxuICBmZXRjaE1vZHVsZXNMaXN0KCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGxldCBjb250YWluZXI7XG4gICAgbGV0ICR0aGlzO1xuXG4gICAgc2VsZi5tb2R1bGVzTGlzdCA9IFtdO1xuICAgICQoJy5tb2R1bGVzLWxpc3QnKS5lYWNoKGZ1bmN0aW9uIHByZXBhcmVDb250YWluZXIoKSB7XG4gICAgICBjb250YWluZXIgPSAkKHRoaXMpO1xuICAgICAgY29udGFpbmVyLmZpbmQoJy5tb2R1bGUtaXRlbScpLmVhY2goZnVuY3Rpb24gcHJlcGFyZU1vZHVsZXMoKSB7XG4gICAgICAgICR0aGlzID0gJCh0aGlzKTtcbiAgICAgICAgc2VsZi5tb2R1bGVzTGlzdC5wdXNoKHtcbiAgICAgICAgICBkb21PYmplY3Q6ICR0aGlzLFxuICAgICAgICAgIGlkOiAkdGhpcy5kYXRhKCdpZCcpLFxuICAgICAgICAgIG5hbWU6ICR0aGlzLmRhdGEoJ25hbWUnKS50b0xvd2VyQ2FzZSgpLFxuICAgICAgICAgIHNjb3Jpbmc6IHBhcnNlRmxvYXQoJHRoaXMuZGF0YSgnc2NvcmluZycpKSxcbiAgICAgICAgICBsb2dvOiAkdGhpcy5kYXRhKCdsb2dvJyksXG4gICAgICAgICAgYXV0aG9yOiAkdGhpcy5kYXRhKCdhdXRob3InKS50b0xvd2VyQ2FzZSgpLFxuICAgICAgICAgIHZlcnNpb246ICR0aGlzLmRhdGEoJ3ZlcnNpb24nKSxcbiAgICAgICAgICBkZXNjcmlwdGlvbjogJHRoaXMuZGF0YSgnZGVzY3JpcHRpb24nKS50b0xvd2VyQ2FzZSgpLFxuICAgICAgICAgIHRlY2hOYW1lOiAkdGhpcy5kYXRhKCd0ZWNoLW5hbWUnKS50b0xvd2VyQ2FzZSgpLFxuICAgICAgICAgIGNoaWxkQ2F0ZWdvcmllczogJHRoaXMuZGF0YSgnY2hpbGQtY2F0ZWdvcmllcycpLFxuICAgICAgICAgIGNhdGVnb3JpZXM6IFN0cmluZygkdGhpcy5kYXRhKCdjYXRlZ29yaWVzJykpLnRvTG93ZXJDYXNlKCksXG4gICAgICAgICAgdHlwZTogJHRoaXMuZGF0YSgndHlwZScpLFxuICAgICAgICAgIHByaWNlOiBwYXJzZUZsb2F0KCR0aGlzLmRhdGEoJ3ByaWNlJykpLFxuICAgICAgICAgIGFjdGl2ZTogcGFyc2VJbnQoJHRoaXMuZGF0YSgnYWN0aXZlJyksIDEwKSxcbiAgICAgICAgICBhY2Nlc3M6ICR0aGlzLmRhdGEoJ2xhc3QtYWNjZXNzJyksXG4gICAgICAgICAgZGlzcGxheTogJHRoaXMuaGFzQ2xhc3MoJ21vZHVsZS1pdGVtLWxpc3QnKSA/IHNlbGYuRElTUExBWV9MSVNUIDogc2VsZi5ESVNQTEFZX0dSSUQsXG4gICAgICAgICAgY29udGFpbmVyLFxuICAgICAgICB9KTtcblxuICAgICAgICAkdGhpcy5yZW1vdmUoKTtcbiAgICAgIH0pO1xuICAgIH0pO1xuXG4gICAgc2VsZi5hZGRvbnNDYXJkR3JpZCA9ICQodGhpcy5hZGRvbkl0ZW1HcmlkU2VsZWN0b3IpO1xuICAgIHNlbGYuYWRkb25zQ2FyZExpc3QgPSAkKHRoaXMuYWRkb25JdGVtTGlzdFNlbGVjdG9yKTtcbiAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICAkKCdib2R5JykudHJpZ2dlcignbW9kdWxlQ2F0YWxvZ0xvYWRlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIFByZXBhcmUgc29ydGluZ1xuICAgKlxuICAgKi9cbiAgdXBkYXRlTW9kdWxlU29ydGluZygpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgIGlmICghc2VsZi5jdXJyZW50U29ydGluZykge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIC8vIE1vZHVsZXMgc29ydGluZ1xuICAgIGxldCBvcmRlciA9ICdhc2MnO1xuICAgIGxldCBrZXkgPSBzZWxmLmN1cnJlbnRTb3J0aW5nO1xuICAgIGNvbnN0IHNwbGl0dGVkS2V5ID0ga2V5LnNwbGl0KCctJyk7XG4gICAgaWYgKHNwbGl0dGVkS2V5Lmxlbmd0aCA+IDEpIHtcbiAgICAgIGtleSA9IHNwbGl0dGVkS2V5WzBdO1xuICAgICAgaWYgKHNwbGl0dGVkS2V5WzFdID09PSAnZGVzYycpIHtcbiAgICAgICAgb3JkZXIgPSAnZGVzYyc7XG4gICAgICB9XG4gICAgfVxuXG4gICAgY29uc3QgY3VycmVudENvbXBhcmUgPSAoYSwgYikgPT4ge1xuICAgICAgbGV0IGFEYXRhID0gYVtrZXldO1xuICAgICAgbGV0IGJEYXRhID0gYltrZXldO1xuICAgICAgaWYgKGtleSA9PT0gJ2FjY2VzcycpIHtcbiAgICAgICAgYURhdGEgPSAobmV3IERhdGUoYURhdGEpKS5nZXRUaW1lKCk7XG4gICAgICAgIGJEYXRhID0gKG5ldyBEYXRlKGJEYXRhKSkuZ2V0VGltZSgpO1xuICAgICAgICBhRGF0YSA9IGlzTmFOKGFEYXRhKSA/IDAgOiBhRGF0YTtcbiAgICAgICAgYkRhdGEgPSBpc05hTihiRGF0YSkgPyAwIDogYkRhdGE7XG4gICAgICAgIGlmIChhRGF0YSA9PT0gYkRhdGEpIHtcbiAgICAgICAgICByZXR1cm4gYi5uYW1lLmxvY2FsZUNvbXBhcmUoYS5uYW1lKTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICBpZiAoYURhdGEgPCBiRGF0YSkgcmV0dXJuIC0xO1xuICAgICAgaWYgKGFEYXRhID4gYkRhdGEpIHJldHVybiAxO1xuXG4gICAgICByZXR1cm4gMDtcbiAgICB9O1xuXG4gICAgc2VsZi5tb2R1bGVzTGlzdC5zb3J0KGN1cnJlbnRDb21wYXJlKTtcbiAgICBpZiAob3JkZXIgPT09ICdkZXNjJykge1xuICAgICAgc2VsZi5tb2R1bGVzTGlzdC5yZXZlcnNlKCk7XG4gICAgfVxuICB9XG5cbiAgdXBkYXRlTW9kdWxlQ29udGFpbmVyRGlzcGxheSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoJy5tb2R1bGUtc2hvcnQtbGlzdCcpLmVhY2goZnVuY3Rpb24gc2V0U2hvcnRMaXN0VmlzaWJpbGl0eSgpIHtcbiAgICAgIGNvbnN0IGNvbnRhaW5lciA9ICQodGhpcyk7XG4gICAgICBjb25zdCBuYk1vZHVsZXNJbkNvbnRhaW5lciA9IGNvbnRhaW5lci5maW5kKCcubW9kdWxlLWl0ZW0nKS5sZW5ndGg7XG4gICAgICBpZiAoXG4gICAgICAgIChcbiAgICAgICAgICBzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeVxuICAgICAgICAgICYmIHNlbGYuY3VycmVudFJlZkNhdGVnb3J5ICE9PSBTdHJpbmcoY29udGFpbmVyLmZpbmQoJy5tb2R1bGVzLWxpc3QnKS5kYXRhKCduYW1lJykpXG4gICAgICAgICkgfHwgKFxuICAgICAgICAgIHNlbGYuY3VycmVudFJlZlN0YXR1cyAhPT0gbnVsbFxuICAgICAgICAgICYmIG5iTW9kdWxlc0luQ29udGFpbmVyID09PSAwXG4gICAgICAgICkgfHwgKFxuICAgICAgICAgIG5iTW9kdWxlc0luQ29udGFpbmVyID09PSAwXG4gICAgICAgICAgJiYgU3RyaW5nKGNvbnRhaW5lci5maW5kKCcubW9kdWxlcy1saXN0JykuZGF0YSgnbmFtZScpKSA9PT0gc2VsZi5DQVRFR09SWV9SRUNFTlRMWV9VU0VEXG4gICAgICAgICkgfHwgKFxuICAgICAgICAgIHNlbGYuY3VycmVudFRhZ3NMaXN0Lmxlbmd0aCA+IDBcbiAgICAgICAgICAmJiBuYk1vZHVsZXNJbkNvbnRhaW5lciA9PT0gMFxuICAgICAgICApXG4gICAgICApIHtcbiAgICAgICAgY29udGFpbmVyLmhpZGUoKTtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBjb250YWluZXIuc2hvdygpO1xuICAgICAgaWYgKG5iTW9kdWxlc0luQ29udGFpbmVyID49IHNlbGYuREVGQVVMVF9NQVhfUEVSX0NBVEVHT1JJRVMpIHtcbiAgICAgICAgY29udGFpbmVyLmZpbmQoYCR7c2VsZi5zZWVNb3JlU2VsZWN0b3J9LCAke3NlbGYuc2VlTGVzc1NlbGVjdG9yfWApLnNob3coKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGNvbnRhaW5lci5maW5kKGAke3NlbGYuc2VlTW9yZVNlbGVjdG9yfSwgJHtzZWxmLnNlZUxlc3NTZWxlY3Rvcn1gKS5oaWRlKCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICB1cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgc2VsZi51cGRhdGVNb2R1bGVTb3J0aW5nKCk7XG5cbiAgICAkKHNlbGYucmVjZW50bHlVc2VkU2VsZWN0b3IpLmZpbmQoJy5tb2R1bGUtaXRlbScpLnJlbW92ZSgpO1xuICAgICQoJy5tb2R1bGVzLWxpc3QnKS5maW5kKCcubW9kdWxlLWl0ZW0nKS5yZW1vdmUoKTtcblxuICAgIC8vIE1vZHVsZXMgdmlzaWJpbGl0eSBtYW5hZ2VtZW50XG4gICAgbGV0IGlzVmlzaWJsZTtcbiAgICBsZXQgY3VycmVudE1vZHVsZTtcbiAgICBsZXQgbW9kdWxlQ2F0ZWdvcnk7XG4gICAgbGV0IHRhZ0V4aXN0cztcbiAgICBsZXQgbmV3VmFsdWU7XG5cbiAgICBjb25zdCBtb2R1bGVzTGlzdExlbmd0aCA9IHNlbGYubW9kdWxlc0xpc3QubGVuZ3RoO1xuICAgIGNvbnN0IGNvdW50ZXIgPSB7fTtcblxuICAgIGZvciAobGV0IGkgPSAwOyBpIDwgbW9kdWxlc0xpc3RMZW5ndGg7IGkgKz0gMSkge1xuICAgICAgY3VycmVudE1vZHVsZSA9IHNlbGYubW9kdWxlc0xpc3RbaV07XG4gICAgICBpZiAoY3VycmVudE1vZHVsZS5kaXNwbGF5ID09PSBzZWxmLmN1cnJlbnREaXNwbGF5KSB7XG4gICAgICAgIGlzVmlzaWJsZSA9IHRydWU7XG5cbiAgICAgICAgbW9kdWxlQ2F0ZWdvcnkgPSBzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeSA9PT0gc2VsZi5DQVRFR09SWV9SRUNFTlRMWV9VU0VEID9cbiAgICAgICAgICAgICAgICAgICAgICAgICBzZWxmLkNBVEVHT1JZX1JFQ0VOVExZX1VTRUQgOlxuICAgICAgICAgICAgICAgICAgICAgICAgIGN1cnJlbnRNb2R1bGUuY2F0ZWdvcmllcztcblxuICAgICAgICAvLyBDaGVjayBmb3Igc2FtZSBjYXRlZ29yeVxuICAgICAgICBpZiAoc2VsZi5jdXJyZW50UmVmQ2F0ZWdvcnkgIT09IG51bGwpIHtcbiAgICAgICAgICBpc1Zpc2libGUgJj0gbW9kdWxlQ2F0ZWdvcnkgPT09IHNlbGYuY3VycmVudFJlZkNhdGVnb3J5O1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gQ2hlY2sgZm9yIHNhbWUgc3RhdHVzXG4gICAgICAgIGlmIChzZWxmLmN1cnJlbnRSZWZTdGF0dXMgIT09IG51bGwpIHtcbiAgICAgICAgICBpc1Zpc2libGUgJj0gY3VycmVudE1vZHVsZS5hY3RpdmUgPT09IHNlbGYuY3VycmVudFJlZlN0YXR1cztcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIENoZWNrIGZvciB0YWcgbGlzdFxuICAgICAgICBpZiAoc2VsZi5jdXJyZW50VGFnc0xpc3QubGVuZ3RoKSB7XG4gICAgICAgICAgdGFnRXhpc3RzID0gZmFsc2U7XG4gICAgICAgICAgJC5lYWNoKHNlbGYuY3VycmVudFRhZ3NMaXN0LCAoaW5kZXgsIHZhbHVlKSA9PiB7XG4gICAgICAgICAgICBuZXdWYWx1ZSA9IHZhbHVlLnRvTG93ZXJDYXNlKCk7XG4gICAgICAgICAgICB0YWdFeGlzdHMgfD0gKFxuICAgICAgICAgICAgICBjdXJyZW50TW9kdWxlLm5hbWUuaW5kZXhPZihuZXdWYWx1ZSkgIT09IC0xXG4gICAgICAgICAgICAgIHx8IGN1cnJlbnRNb2R1bGUuZGVzY3JpcHRpb24uaW5kZXhPZihuZXdWYWx1ZSkgIT09IC0xXG4gICAgICAgICAgICAgIHx8IGN1cnJlbnRNb2R1bGUuYXV0aG9yLmluZGV4T2YobmV3VmFsdWUpICE9PSAtMVxuICAgICAgICAgICAgICB8fCBjdXJyZW50TW9kdWxlLnRlY2hOYW1lLmluZGV4T2YobmV3VmFsdWUpICE9PSAtMVxuICAgICAgICAgICAgKTtcbiAgICAgICAgICB9KTtcbiAgICAgICAgICBpc1Zpc2libGUgJj0gdGFnRXhpc3RzO1xuICAgICAgICB9XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIElmIGxpc3QgZGlzcGxheSB3aXRob3V0IHNlYXJjaCB3ZSBtdXN0IGRpc3BsYXkgb25seSB0aGUgZmlyc3QgNSBtb2R1bGVzXG4gICAgICAgICAqL1xuICAgICAgICBpZiAoc2VsZi5jdXJyZW50RGlzcGxheSA9PT0gc2VsZi5ESVNQTEFZX0xJU1QgJiYgIXNlbGYuY3VycmVudFRhZ3NMaXN0Lmxlbmd0aCkge1xuICAgICAgICAgIGlmIChzZWxmLmN1cnJlbnRDYXRlZ29yeURpc3BsYXlbbW9kdWxlQ2F0ZWdvcnldID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgIHNlbGYuY3VycmVudENhdGVnb3J5RGlzcGxheVttb2R1bGVDYXRlZ29yeV0gPSBmYWxzZTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBpZiAoIWNvdW50ZXJbbW9kdWxlQ2F0ZWdvcnldKSB7XG4gICAgICAgICAgICBjb3VudGVyW21vZHVsZUNhdGVnb3J5XSA9IDA7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgaWYgKG1vZHVsZUNhdGVnb3J5ID09PSBzZWxmLkNBVEVHT1JZX1JFQ0VOVExZX1VTRUQpIHtcbiAgICAgICAgICAgIGlmIChjb3VudGVyW21vZHVsZUNhdGVnb3J5XSA+PSBzZWxmLkRFRkFVTFRfTUFYX1JFQ0VOVExZX1VTRUQpIHtcbiAgICAgICAgICAgICAgaXNWaXNpYmxlICY9IHNlbGYuY3VycmVudENhdGVnb3J5RGlzcGxheVttb2R1bGVDYXRlZ29yeV07XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfSBlbHNlIGlmIChjb3VudGVyW21vZHVsZUNhdGVnb3J5XSA+PSBzZWxmLkRFRkFVTFRfTUFYX1BFUl9DQVRFR09SSUVTKSB7XG4gICAgICAgICAgICBpc1Zpc2libGUgJj0gc2VsZi5jdXJyZW50Q2F0ZWdvcnlEaXNwbGF5W21vZHVsZUNhdGVnb3J5XTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBjb3VudGVyW21vZHVsZUNhdGVnb3J5XSArPSAxO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gSWYgdmlzaWJsZSwgZGlzcGxheSAoVGh4IGNhcHRhaW4gb2J2aW91cylcbiAgICAgICAgaWYgKGlzVmlzaWJsZSkge1xuICAgICAgICAgIGlmIChzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeSA9PT0gc2VsZi5DQVRFR09SWV9SRUNFTlRMWV9VU0VEKSB7XG4gICAgICAgICAgICAkKHNlbGYucmVjZW50bHlVc2VkU2VsZWN0b3IpLmFwcGVuZChjdXJyZW50TW9kdWxlLmRvbU9iamVjdCk7XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGN1cnJlbnRNb2R1bGUuY29udGFpbmVyLmFwcGVuZChjdXJyZW50TW9kdWxlLmRvbU9iamVjdCk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG4gICAgfVxuXG4gICAgc2VsZi51cGRhdGVNb2R1bGVDb250YWluZXJEaXNwbGF5KCk7XG5cbiAgICBpZiAoc2VsZi5jdXJyZW50VGFnc0xpc3QubGVuZ3RoKSB7XG4gICAgICAkKCcubW9kdWxlcy1saXN0JykuYXBwZW5kKHRoaXMuY3VycmVudERpc3BsYXkgPT09IHNlbGYuRElTUExBWV9HUklEID8gdGhpcy5hZGRvbnNDYXJkR3JpZCA6IHRoaXMuYWRkb25zQ2FyZExpc3QpO1xuICAgIH1cblxuICAgIHNlbGYudXBkYXRlVG90YWxSZXN1bHRzKCk7XG4gIH1cblxuICBpbml0UGFnZUNoYW5nZVByb3RlY3Rpb24oKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAkKHdpbmRvdykub24oJ2JlZm9yZXVubG9hZCcsICgpID0+IHtcbiAgICAgIGlmIChzZWxmLmlzVXBsb2FkU3RhcnRlZCA9PT0gdHJ1ZSkge1xuICAgICAgICByZXR1cm4gJ0l0IHNlZW1zIHNvbWUgY3JpdGljYWwgb3BlcmF0aW9uIGFyZSBydW5uaW5nLCBhcmUgeW91IHN1cmUgeW91IHdhbnQgdG8gY2hhbmdlIHBhZ2UgPyBJdCBtaWdodCBjYXVzZSBzb21lIHVuZXhlcGN0ZWQgYmVoYXZpb3JzLic7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuXG4gIGJ1aWxkQnVsa0FjdGlvbk1vZHVsZUxpc3QoKSB7XG4gICAgY29uc3QgY2hlY2tCb3hlc1NlbGVjdG9yID0gdGhpcy5nZXRCdWxrQ2hlY2tib3hlc0NoZWNrZWRTZWxlY3RvcigpO1xuICAgIGNvbnN0IG1vZHVsZUl0ZW1TZWxlY3RvciA9IHRoaXMuZ2V0TW9kdWxlSXRlbVNlbGVjdG9yKCk7XG4gICAgbGV0IGFscmVhZHlEb25lRmxhZyA9IDA7XG4gICAgbGV0IGh0bWxHZW5lcmF0ZWQgPSAnJztcbiAgICBsZXQgY3VycmVudEVsZW1lbnQ7XG5cbiAgICAkKGNoZWNrQm94ZXNTZWxlY3RvcikuZWFjaChmdW5jdGlvbiBwcmVwYXJlQ2hlY2tib3hlcygpIHtcbiAgICAgIGlmIChhbHJlYWR5RG9uZUZsYWcgPT09IDEwKSB7XG4gICAgICAgIC8vIEJyZWFrIGVhY2hcbiAgICAgICAgaHRtbEdlbmVyYXRlZCArPSAnLSAuLi4nO1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICB9XG5cbiAgICAgIGN1cnJlbnRFbGVtZW50ID0gJCh0aGlzKS5jbG9zZXN0KG1vZHVsZUl0ZW1TZWxlY3Rvcik7XG4gICAgICBodG1sR2VuZXJhdGVkICs9IGAtICR7Y3VycmVudEVsZW1lbnQuZGF0YSgnbmFtZScpfTxici8+YDtcbiAgICAgIGFscmVhZHlEb25lRmxhZyArPSAxO1xuXG4gICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9KTtcblxuICAgIHJldHVybiBodG1sR2VuZXJhdGVkO1xuICB9XG5cbiAgaW5pdEFkZG9uc0Nvbm5lY3QoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAvLyBNYWtlIGFkZG9ucyBjb25uZWN0IG1vZGFsIHJlYWR5IHRvIGJlIGNsaWNrZWRcbiAgICBpZiAoJChzZWxmLmFkZG9uc0Nvbm5lY3RNb2RhbEJ0blNlbGVjdG9yKS5hdHRyKCdocmVmJykgPT09ICcjJykge1xuICAgICAgJChzZWxmLmFkZG9uc0Nvbm5lY3RNb2RhbEJ0blNlbGVjdG9yKS5hdHRyKCdkYXRhLXRvZ2dsZScsICdtb2RhbCcpO1xuICAgICAgJChzZWxmLmFkZG9uc0Nvbm5lY3RNb2RhbEJ0blNlbGVjdG9yKS5hdHRyKCdkYXRhLXRhcmdldCcsIHNlbGYuYWRkb25zQ29ubmVjdE1vZGFsU2VsZWN0b3IpO1xuICAgIH1cblxuICAgIGlmICgkKHNlbGYuYWRkb25zTG9nb3V0TW9kYWxCdG5TZWxlY3RvcikuYXR0cignaHJlZicpID09PSAnIycpIHtcbiAgICAgICQoc2VsZi5hZGRvbnNMb2dvdXRNb2RhbEJ0blNlbGVjdG9yKS5hdHRyKCdkYXRhLXRvZ2dsZScsICdtb2RhbCcpO1xuICAgICAgJChzZWxmLmFkZG9uc0xvZ291dE1vZGFsQnRuU2VsZWN0b3IpLmF0dHIoJ2RhdGEtdGFyZ2V0Jywgc2VsZi5hZGRvbnNMb2dvdXRNb2RhbFNlbGVjdG9yKTtcbiAgICB9XG5cbiAgICAkKCdib2R5Jykub24oJ3N1Ym1pdCcsIHNlbGYuYWRkb25zQ29ubmVjdEZvcm0sIGZ1bmN0aW9uIGluaXRpYWxpemVCb2R5U3VibWl0KGV2ZW50KSB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICQuYWpheCh7XG4gICAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgICAgICB1cmw6ICQodGhpcykuYXR0cignYWN0aW9uJyksXG4gICAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICAgIGRhdGE6ICQodGhpcykuc2VyaWFsaXplKCksXG4gICAgICAgIGJlZm9yZVNlbmQ6ICgpID0+IHtcbiAgICAgICAgICAkKHNlbGYuYWRkb25zTG9naW5CdXR0b25TZWxlY3Rvcikuc2hvdygpO1xuICAgICAgICAgICQoJ2J1dHRvbi5idG5bdHlwZT1cInN1Ym1pdFwiXScsIHNlbGYuYWRkb25zQ29ubmVjdEZvcm0pLmhpZGUoKTtcbiAgICAgICAgfVxuICAgICAgfSkuZG9uZSgocmVzcG9uc2UpID0+IHtcbiAgICAgICAgaWYgKHJlc3BvbnNlLnN1Y2Nlc3MgPT09IDEpIHtcbiAgICAgICAgICBsb2NhdGlvbi5yZWxvYWQoKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAkLmdyb3dsLmVycm9yKHttZXNzYWdlOiByZXNwb25zZS5tZXNzYWdlfSk7XG4gICAgICAgICAgJChzZWxmLmFkZG9uc0xvZ2luQnV0dG9uU2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgICAgICAkKCdidXR0b24uYnRuW3R5cGU9XCJzdWJtaXRcIl0nLCBzZWxmLmFkZG9uc0Nvbm5lY3RGb3JtKS5mYWRlSW4oKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cblxuICBpbml0QWRkTW9kdWxlQWN0aW9uKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0IGFkZE1vZHVsZUJ1dHRvbiA9ICQoc2VsZi5hZGRvbnNJbXBvcnRNb2RhbEJ0blNlbGVjdG9yKTtcbiAgICBhZGRNb2R1bGVCdXR0b24uYXR0cignZGF0YS10b2dnbGUnLCAnbW9kYWwnKTtcbiAgICBhZGRNb2R1bGVCdXR0b24uYXR0cignZGF0YS10YXJnZXQnLCBzZWxmLmRyb3Bab25lTW9kYWxTZWxlY3Rvcik7XG4gIH1cblxuICBpbml0RHJvcHpvbmUoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgY29uc3QgYm9keSA9ICQoJ2JvZHknKTtcbiAgICBjb25zdCBkcm9wem9uZSA9ICQoJy5kcm9wem9uZScpO1xuXG4gICAgLy8gUmVzZXQgbW9kYWwgd2hlbiBjbGljayBvbiBSZXRyeSBpbiBjYXNlIG9mIGZhaWx1cmVcbiAgICBib2R5Lm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZVJldHJ5U2VsZWN0b3IsXG4gICAgICAoKSA9PiB7XG4gICAgICAgICQoYCR7c2VsZi5tb2R1bGVJbXBvcnRTdWNjZXNzU2VsZWN0b3J9LCR7c2VsZi5tb2R1bGVJbXBvcnRGYWlsdXJlU2VsZWN0b3J9LCR7c2VsZi5tb2R1bGVJbXBvcnRQcm9jZXNzaW5nU2VsZWN0b3J9YCkuZmFkZU91dCgoKSA9PiB7XG4gICAgICAgICAgLyoqXG4gICAgICAgICAgICogQWRkZWQgdGltZW91dCBmb3IgYSBiZXR0ZXIgcmVuZGVyIG9mIGFuaW1hdGlvblxuICAgICAgICAgICAqIGFuZCBhdm9pZCB0byBoYXZlIGRpc3BsYXllZCBhdCB0aGUgc2FtZSB0aW1lXG4gICAgICAgICAgICovXG4gICAgICAgICAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3RhcnRTZWxlY3RvcikuZmFkZUluKCgpID0+IHtcbiAgICAgICAgICAgICAgJChzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVNc2dEZXRhaWxzU2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgICAgICAgICAgJChzZWxmLm1vZHVsZUltcG9ydFN1Y2Nlc3NDb25maWd1cmVCdG5TZWxlY3RvcikuaGlkZSgpO1xuICAgICAgICAgICAgICBkcm9wem9uZS5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgfSwgNTUwKTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgKTtcblxuICAgIC8vIFJlaW5pdCBtb2RhbCBvbiBleGl0LCBidXQgY2hlY2sgaWYgbm90IGFscmVhZHkgcHJvY2Vzc2luZyBzb21ldGhpbmdcbiAgICBib2R5Lm9uKCdoaWRkZW4uYnMubW9kYWwnLCB0aGlzLmRyb3Bab25lTW9kYWxTZWxlY3RvciwgKCkgPT4ge1xuICAgICAgJChgJHtzZWxmLm1vZHVsZUltcG9ydFN1Y2Nlc3NTZWxlY3Rvcn0sICR7c2VsZi5tb2R1bGVJbXBvcnRGYWlsdXJlU2VsZWN0b3J9YCkuaGlkZSgpO1xuICAgICAgJChzZWxmLm1vZHVsZUltcG9ydFN0YXJ0U2VsZWN0b3IpLnNob3coKTtcblxuICAgICAgZHJvcHpvbmUucmVtb3ZlQXR0cignc3R5bGUnKTtcbiAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRGYWlsdXJlTXNnRGV0YWlsc1NlbGVjdG9yKS5oaWRlKCk7XG4gICAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3VjY2Vzc0NvbmZpZ3VyZUJ0blNlbGVjdG9yKS5oaWRlKCk7XG4gICAgICAkKHNlbGYuZHJvcFpvbmVNb2RhbEZvb3RlclNlbGVjdG9yKS5odG1sKCcnKTtcbiAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRDb25maXJtU2VsZWN0b3IpLmhpZGUoKTtcbiAgICB9KTtcblxuICAgIC8vIENoYW5nZSB0aGUgd2F5IERyb3B6b25lLmpzIGxpYiBoYW5kbGUgZmlsZSBpbnB1dCB0cmlnZ2VyXG4gICAgYm9keS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICBgLmRyb3B6b25lOm5vdCgke3RoaXMubW9kdWxlSW1wb3J0U2VsZWN0RmlsZU1hbnVhbFNlbGVjdG9yfSwgJHt0aGlzLm1vZHVsZUltcG9ydFN1Y2Nlc3NDb25maWd1cmVCdG5TZWxlY3Rvcn0pYCxcbiAgICAgIChldmVudCwgbWFudWFsU2VsZWN0KSA9PiB7XG4gICAgICAgIC8vIGlmIGNsaWNrIGNvbWVzIGZyb20gLm1vZHVsZS1pbXBvcnQtc3RhcnQtc2VsZWN0LW1hbnVhbCwgc3RvcCBldmVyeXRoaW5nXG4gICAgICAgIGlmICh0eXBlb2YgbWFudWFsU2VsZWN0ID09PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICApO1xuXG4gICAgYm9keS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUltcG9ydFNlbGVjdEZpbGVNYW51YWxTZWxlY3RvciwgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAvKipcbiAgICAgICAqIFRyaWdnZXIgY2xpY2sgb24gaGlkZGVuIGZpbGUgaW5wdXQsIGFuZCBwYXNzIGV4dHJhIGRhdGFcbiAgICAgICAqIHRvIC5kcm9wem9uZSBjbGljayBoYW5kbGVyIGZybyBpdCB0byBub3RpY2UgaXQgY29tZXMgZnJvbSBoZXJlXG4gICAgICAgKi9cbiAgICAgICQoJy5kei1oaWRkZW4taW5wdXQnKS50cmlnZ2VyKCdjbGljaycsIFsnbWFudWFsX3NlbGVjdCddKTtcbiAgICB9KTtcblxuICAgIC8vIEhhbmRsZSBtb2RhbCBjbG9zdXJlXG4gICAgYm9keS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUltcG9ydE1vZGFsQ2xvc2VCdG4sICgpID0+IHtcbiAgICAgIGlmIChzZWxmLmlzVXBsb2FkU3RhcnRlZCAhPT0gdHJ1ZSkge1xuICAgICAgICAkKHNlbGYuZHJvcFpvbmVNb2RhbFNlbGVjdG9yKS5tb2RhbCgnaGlkZScpO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgLy8gRml4IGlzc3VlIG9uIGNsaWNrIGNvbmZpZ3VyZSBidXR0b25cbiAgICBib2R5Lm9uKCdjbGljaycsIHRoaXMubW9kdWxlSW1wb3J0U3VjY2Vzc0NvbmZpZ3VyZUJ0blNlbGVjdG9yLCBmdW5jdGlvbiBpbml0aWFsaXplQm9keUNsaWNrT25Nb2R1bGVJbXBvcnQoZXZlbnQpIHtcbiAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIHdpbmRvdy5sb2NhdGlvbiA9ICQodGhpcykuYXR0cignaHJlZicpO1xuICAgIH0pO1xuXG4gICAgLy8gT3BlbiBmYWlsdXJlIG1lc3NhZ2UgZGV0YWlscyBib3hcbiAgICBib2R5Lm9uKCdjbGljaycsIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZURldGFpbHNCdG5TZWxlY3RvciwgKCkgPT4ge1xuICAgICAgJChzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVNc2dEZXRhaWxzU2VsZWN0b3IpLnNsaWRlRG93bigpO1xuICAgIH0pO1xuXG4gICAgLy8gQHNlZTogZHJvcHpvbmUuanNcbiAgICBjb25zdCBkcm9wem9uZU9wdGlvbnMgPSB7XG4gICAgICB1cmw6IHdpbmRvdy5tb2R1bGVVUkxzLm1vZHVsZUltcG9ydCxcbiAgICAgIGFjY2VwdGVkRmlsZXM6ICcuemlwLCAudGFyJyxcbiAgICAgIC8vIFRoZSBuYW1lIHRoYXQgd2lsbCBiZSB1c2VkIHRvIHRyYW5zZmVyIHRoZSBmaWxlXG4gICAgICBwYXJhbU5hbWU6ICdmaWxlX3VwbG9hZGVkJyxcbiAgICAgIG1heEZpbGVzaXplOiA1MCwgLy8gY2FuJ3QgYmUgZ3JlYXRlciB0aGFuIDUwTWIgYmVjYXVzZSBpdCdzIGFuIGFkZG9ucyBsaW1pdGF0aW9uXG4gICAgICB1cGxvYWRNdWx0aXBsZTogZmFsc2UsXG4gICAgICBhZGRSZW1vdmVMaW5rczogdHJ1ZSxcbiAgICAgIGRpY3REZWZhdWx0TWVzc2FnZTogJycsXG4gICAgICBoaWRkZW5JbnB1dENvbnRhaW5lcjogc2VsZi5kcm9wWm9uZUltcG9ydFpvbmVTZWxlY3RvcixcbiAgICAgIC8qKlxuICAgICAgICogQWRkIHVubGltaXRlZCB0aW1lb3V0LiBPdGhlcndpc2UgZHJvcHpvbmUgdGltZW91dCBpcyAzMCBzZWNvbmRzXG4gICAgICAgKiAgYW5kIGlmIGEgbW9kdWxlIGlzIGxvbmcgdG8gaW5zdGFsbCwgaXQgaXMgbm90IHBvc3NpYmxlIHRvIGluc3RhbGwgdGhlIG1vZHVsZS5cbiAgICAgICAqL1xuICAgICAgdGltZW91dDogMCxcbiAgICAgIGFkZGVkZmlsZTogKCkgPT4ge1xuICAgICAgICBzZWxmLmFuaW1hdGVTdGFydFVwbG9hZCgpO1xuICAgICAgfSxcbiAgICAgIHByb2Nlc3Npbmc6ICgpID0+IHtcbiAgICAgICAgLy8gTGVhdmUgaXQgZW1wdHkgc2luY2Ugd2UgZG9uJ3QgcmVxdWlyZSBhbnl0aGluZyB3aGlsZSBwcm9jZXNzaW5nIHVwbG9hZFxuICAgICAgfSxcbiAgICAgIGVycm9yOiAoZmlsZSwgbWVzc2FnZSkgPT4ge1xuICAgICAgICBzZWxmLmRpc3BsYXlPblVwbG9hZEVycm9yKG1lc3NhZ2UpO1xuICAgICAgfSxcbiAgICAgIGNvbXBsZXRlOiAoZmlsZSkgPT4ge1xuICAgICAgICBpZiAoZmlsZS5zdGF0dXMgIT09ICdlcnJvcicpIHtcbiAgICAgICAgICBjb25zdCByZXNwb25zZU9iamVjdCA9ICQucGFyc2VKU09OKGZpbGUueGhyLnJlc3BvbnNlKTtcbiAgICAgICAgICBpZiAodHlwZW9mIHJlc3BvbnNlT2JqZWN0LmlzX2NvbmZpZ3VyYWJsZSA9PT0gJ3VuZGVmaW5lZCcpIHJlc3BvbnNlT2JqZWN0LmlzX2NvbmZpZ3VyYWJsZSA9IG51bGw7XG4gICAgICAgICAgaWYgKHR5cGVvZiByZXNwb25zZU9iamVjdC5tb2R1bGVfbmFtZSA9PT0gJ3VuZGVmaW5lZCcpIHJlc3BvbnNlT2JqZWN0Lm1vZHVsZV9uYW1lID0gbnVsbDtcblxuICAgICAgICAgIHNlbGYuZGlzcGxheU9uVXBsb2FkRG9uZShyZXNwb25zZU9iamVjdCk7XG4gICAgICAgIH1cbiAgICAgICAgLy8gU3RhdGUgdGhhdCB3ZSBoYXZlIGZpbmlzaCB0aGUgcHJvY2VzcyB0byB1bmxvY2sgc29tZSBhY3Rpb25zXG4gICAgICAgIHNlbGYuaXNVcGxvYWRTdGFydGVkID0gZmFsc2U7XG4gICAgICB9LFxuICAgIH07XG5cbiAgICBkcm9wem9uZS5kcm9wem9uZSgkLmV4dGVuZChkcm9wem9uZU9wdGlvbnMpKTtcbiAgfVxuXG4gIGFuaW1hdGVTdGFydFVwbG9hZCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBkcm9wem9uZSA9ICQoJy5kcm9wem9uZScpO1xuICAgIC8vIFN0YXRlIHRoYXQgd2Ugc3RhcnQgbW9kdWxlIHVwbG9hZFxuICAgIHNlbGYuaXNVcGxvYWRTdGFydGVkID0gdHJ1ZTtcbiAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3RhcnRTZWxlY3RvcikuaGlkZSgwKTtcbiAgICBkcm9wem9uZS5jc3MoJ2JvcmRlcicsICdub25lJyk7XG4gICAgJChzZWxmLm1vZHVsZUltcG9ydFByb2Nlc3NpbmdTZWxlY3RvcikuZmFkZUluKCk7XG4gIH1cblxuICBhbmltYXRlRW5kVXBsb2FkKGNhbGxiYWNrKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgJChzZWxmLm1vZHVsZUltcG9ydFByb2Nlc3NpbmdTZWxlY3RvcikuZmluaXNoKCkuZmFkZU91dChjYWxsYmFjayk7XG4gIH1cblxuICAvKipcbiAgICogTWV0aG9kIHRvIGNhbGwgZm9yIHVwbG9hZCBtb2RhbCwgd2hlbiB0aGUgYWpheCBjYWxsIHdlbnQgd2VsbC5cbiAgICpcbiAgICogQHBhcmFtIG9iamVjdCByZXN1bHQgY29udGFpbmluZyB0aGUgc2VydmVyIHJlc3BvbnNlXG4gICAqL1xuICBkaXNwbGF5T25VcGxvYWREb25lKHJlc3VsdCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIHNlbGYuYW5pbWF0ZUVuZFVwbG9hZCgoKSA9PiB7XG4gICAgICBpZiAocmVzdWx0LnN0YXR1cyA9PT0gdHJ1ZSkge1xuICAgICAgICBpZiAocmVzdWx0LmlzX2NvbmZpZ3VyYWJsZSA9PT0gdHJ1ZSkge1xuICAgICAgICAgIGNvbnN0IGNvbmZpZ3VyZUxpbmsgPSB3aW5kb3cubW9kdWxlVVJMcy5jb25maWd1cmF0aW9uUGFnZS5yZXBsYWNlKC86bnVtYmVyOi8sIHJlc3VsdC5tb2R1bGVfbmFtZSk7XG4gICAgICAgICAgJChzZWxmLm1vZHVsZUltcG9ydFN1Y2Nlc3NDb25maWd1cmVCdG5TZWxlY3RvcikuYXR0cignaHJlZicsIGNvbmZpZ3VyZUxpbmspO1xuICAgICAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRTdWNjZXNzQ29uZmlndXJlQnRuU2VsZWN0b3IpLnNob3coKTtcbiAgICAgICAgfVxuICAgICAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3VjY2Vzc1NlbGVjdG9yKS5mYWRlSW4oKTtcbiAgICAgIH0gZWxzZSBpZiAodHlwZW9mIHJlc3VsdC5jb25maXJtYXRpb25fc3ViamVjdCAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgc2VsZi5kaXNwbGF5UHJlc3RhVHJ1c3RTdGVwKHJlc3VsdCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkKHNlbGYubW9kdWxlSW1wb3J0RmFpbHVyZU1zZ0RldGFpbHNTZWxlY3RvcikuaHRtbChyZXN1bHQubXNnKTtcbiAgICAgICAgJChzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVTZWxlY3RvcikuZmFkZUluKCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTWV0aG9kIHRvIGNhbGwgZm9yIHVwbG9hZCBtb2RhbCwgd2hlbiB0aGUgYWpheCBjYWxsIHdlbnQgd3Jvbmcgb3Igd2hlbiB0aGUgYWN0aW9uIHJlcXVlc3RlZCBjb3VsZCBub3RcbiAgICogc3VjY2VlZCBmb3Igc29tZSByZWFzb24uXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgbWVzc2FnZSBleHBsYWluaW5nIHRoZSBlcnJvci5cbiAgICovXG4gIGRpc3BsYXlPblVwbG9hZEVycm9yKG1lc3NhZ2UpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBzZWxmLmFuaW1hdGVFbmRVcGxvYWQoKCkgPT4ge1xuICAgICAgJChzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVNc2dEZXRhaWxzU2VsZWN0b3IpLmh0bWwobWVzc2FnZSk7XG4gICAgICAkKHNlbGYubW9kdWxlSW1wb3J0RmFpbHVyZVNlbGVjdG9yKS5mYWRlSW4oKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJZiBQcmVzdGFUcnVzdCBuZWVkcyB0byBiZSBjb25maXJtZWQsIHdlIGFzayBmb3IgdGhlIGNvbmZpcm1hdGlvblxuICAgKiBtb2RhbCBjb250ZW50IGFuZCB3ZSBkaXNwbGF5IGl0IGluIHRoZSBjdXJyZW50bHkgZGlzcGxheWVkIG9uZS5cbiAgICogV2UgYWxzbyBnZW5lcmF0ZSB0aGUgYWpheCBjYWxsIHRvIHRyaWdnZXIgb25jZSB3ZSBjb25maXJtIHdlIHdhbnQgdG8gaW5zdGFsbFxuICAgKiB0aGUgbW9kdWxlLlxuICAgKlxuICAgKiBAcGFyYW0gUHJldmlvdXMgc2VydmVyIHJlc3BvbnNlIHJlc3VsdFxuICAgKi9cbiAgZGlzcGxheVByZXN0YVRydXN0U3RlcChyZXN1bHQpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBtb2RhbCA9IHNlbGYubW9kdWxlQ2FyZENvbnRyb2xsZXIuX3JlcGxhY2VQcmVzdGFUcnVzdFBsYWNlaG9sZGVycyhyZXN1bHQpO1xuICAgIGNvbnN0IG1vZHVsZU5hbWUgPSByZXN1bHQubW9kdWxlLmF0dHJpYnV0ZXMubmFtZTtcblxuICAgICQodGhpcy5tb2R1bGVJbXBvcnRDb25maXJtU2VsZWN0b3IpLmh0bWwobW9kYWwuZmluZCgnLm1vZGFsLWJvZHknKS5odG1sKCkpLmZhZGVJbigpO1xuICAgICQodGhpcy5kcm9wWm9uZU1vZGFsRm9vdGVyU2VsZWN0b3IpLmh0bWwobW9kYWwuZmluZCgnLm1vZGFsLWZvb3RlcicpLmh0bWwoKSkuZmFkZUluKCk7XG5cbiAgICAkKHRoaXMuZHJvcFpvbmVNb2RhbEZvb3RlclNlbGVjdG9yKS5maW5kKCcucHN0cnVzdC1pbnN0YWxsJykub2ZmKCdjbGljaycpLm9uKCdjbGljaycsICgpID0+IHtcbiAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRDb25maXJtU2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgICQoc2VsZi5kcm9wWm9uZU1vZGFsRm9vdGVyU2VsZWN0b3IpLmh0bWwoJycpO1xuICAgICAgc2VsZi5hbmltYXRlU3RhcnRVcGxvYWQoKTtcblxuICAgICAgLy8gSW5zdGFsbCBhamF4IGNhbGxcbiAgICAgICQucG9zdChyZXN1bHQubW9kdWxlLmF0dHJpYnV0ZXMudXJscy5pbnN0YWxsLCB7J2FjdGlvblBhcmFtc1tjb25maXJtUHJlc3RhVHJ1c3RdJzogJzEnfSlcbiAgICAgICAuZG9uZSgoZGF0YSkgPT4ge1xuICAgICAgICAgc2VsZi5kaXNwbGF5T25VcGxvYWREb25lKGRhdGFbbW9kdWxlTmFtZV0pO1xuICAgICAgIH0pXG4gICAgICAgLmZhaWwoKGRhdGEpID0+IHtcbiAgICAgICAgIHNlbGYuZGlzcGxheU9uVXBsb2FkRXJyb3IoZGF0YVttb2R1bGVOYW1lXSk7XG4gICAgICAgfSlcbiAgICAgICAuYWx3YXlzKCgpID0+IHtcbiAgICAgICAgIHNlbGYuaXNVcGxvYWRTdGFydGVkID0gZmFsc2U7XG4gICAgICAgfSk7XG4gICAgfSk7XG4gIH1cblxuICBnZXRCdWxrQ2hlY2tib3hlc1NlbGVjdG9yKCkge1xuICAgIHJldHVybiB0aGlzLmN1cnJlbnREaXNwbGF5ID09PSB0aGlzLkRJU1BMQVlfR1JJRFxuICAgICAgICAgPyB0aGlzLmJ1bGtBY3Rpb25DaGVja2JveEdyaWRTZWxlY3RvclxuICAgICAgICAgOiB0aGlzLmJ1bGtBY3Rpb25DaGVja2JveExpc3RTZWxlY3RvcjtcbiAgfVxuXG5cbiAgZ2V0QnVsa0NoZWNrYm94ZXNDaGVja2VkU2VsZWN0b3IoKSB7XG4gICAgcmV0dXJuIHRoaXMuY3VycmVudERpc3BsYXkgPT09IHRoaXMuRElTUExBWV9HUklEXG4gICAgICAgICA/IHRoaXMuY2hlY2tlZEJ1bGtBY3Rpb25HcmlkU2VsZWN0b3JcbiAgICAgICAgIDogdGhpcy5jaGVja2VkQnVsa0FjdGlvbkxpc3RTZWxlY3RvcjtcbiAgfVxuXG4gIGdldE1vZHVsZUl0ZW1TZWxlY3RvcigpIHtcbiAgICByZXR1cm4gdGhpcy5jdXJyZW50RGlzcGxheSA9PT0gdGhpcy5ESVNQTEFZX0dSSURcbiAgICAgICAgID8gdGhpcy5tb2R1bGVJdGVtR3JpZFNlbGVjdG9yXG4gICAgICAgICA6IHRoaXMubW9kdWxlSXRlbUxpc3RTZWxlY3RvcjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIG1vZHVsZSBub3RpZmljYXRpb25zIGNvdW50IGFuZCBkaXNwbGF5cyBpdCBhcyBhIGJhZGdlIG9uIHRoZSBub3RpZmljYXRpb24gdGFiXG4gICAqIEByZXR1cm4gdm9pZFxuICAgKi9cbiAgZ2V0Tm90aWZpY2F0aW9uc0NvdW50KCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgICQuZ2V0SlNPTihcbiAgICAgIHdpbmRvdy5tb2R1bGVVUkxzLm5vdGlmaWNhdGlvbnNDb3VudCxcbiAgICAgIHNlbGYudXBkYXRlTm90aWZpY2F0aW9uc0NvdW50XG4gICAgKS5mYWlsKCgpID0+IHtcbiAgICAgIGNvbnNvbGUuZXJyb3IoJ0NvdWxkIG5vdCByZXRyaWV2ZSBtb2R1bGUgbm90aWZpY2F0aW9ucyBjb3VudC4nKTtcbiAgICB9KTtcbiAgfVxuXG4gIHVwZGF0ZU5vdGlmaWNhdGlvbnNDb3VudChiYWRnZSkge1xuICAgIGNvbnN0IGRlc3RpbmF0aW9uVGFicyA9IHtcbiAgICAgIHRvX2NvbmZpZ3VyZTogJCgnI3N1YnRhYi1BZG1pbk1vZHVsZXNOb3RpZmljYXRpb25zJyksXG4gICAgICB0b191cGRhdGU6ICQoJyNzdWJ0YWItQWRtaW5Nb2R1bGVzVXBkYXRlcycpLFxuICAgIH07XG5cbiAgICBmb3IgKGxldCBrZXkgaW4gZGVzdGluYXRpb25UYWJzKSB7XG4gICAgICBpZiAoZGVzdGluYXRpb25UYWJzW2tleV0ubGVuZ3RoID09PSAwKSB7XG4gICAgICAgIGNvbnRpbnVlO1xuICAgICAgfVxuXG4gICAgICBkZXN0aW5hdGlvblRhYnNba2V5XS5maW5kKCcubm90aWZpY2F0aW9uLWNvdW50ZXInKS50ZXh0KGJhZGdlW2tleV0pO1xuICAgIH1cbiAgfVxuXG4gIGluaXRBZGRvbnNTZWFyY2goKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgJCgnYm9keScpLm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgIGAke3NlbGYuYWRkb25JdGVtR3JpZFNlbGVjdG9yfSwgJHtzZWxmLmFkZG9uSXRlbUxpc3RTZWxlY3Rvcn1gLFxuICAgICAgKCkgPT4ge1xuICAgICAgICBsZXQgc2VhcmNoUXVlcnkgPSAnJztcbiAgICAgICAgaWYgKHNlbGYuY3VycmVudFRhZ3NMaXN0Lmxlbmd0aCkge1xuICAgICAgICAgIHNlYXJjaFF1ZXJ5ID0gZW5jb2RlVVJJQ29tcG9uZW50KHNlbGYuY3VycmVudFRhZ3NMaXN0LmpvaW4oJyAnKSk7XG4gICAgICAgIH1cblxuICAgICAgICB3aW5kb3cub3BlbihgJHtzZWxmLmJhc2VBZGRvbnNVcmx9c2VhcmNoLnBocD9zZWFyY2hfcXVlcnk9JHtzZWFyY2hRdWVyeX1gLCAnX2JsYW5rJyk7XG4gICAgICB9XG4gICAgKTtcbiAgfVxuXG4gIGluaXRDYXRlZ29yaWVzR3JpZCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoJ2JvZHknKS5vbignY2xpY2snLCB0aGlzLmNhdGVnb3J5R3JpZEl0ZW1TZWxlY3RvciwgZnVuY3Rpb24gaW5pdGlsYWl6ZUdyaWRCb2R5Q2xpY2soZXZlbnQpIHtcbiAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIGNvbnN0IHJlZkNhdGVnb3J5ID0gJCh0aGlzKS5kYXRhKCdjYXRlZ29yeS1yZWYnKTtcblxuICAgICAgLy8gSW4gY2FzZSB3ZSBoYXZlIHNvbWUgdGFncyB3ZSBuZWVkIHRvIHJlc2V0IGl0ICFcbiAgICAgIGlmIChzZWxmLmN1cnJlbnRUYWdzTGlzdC5sZW5ndGgpIHtcbiAgICAgICAgc2VsZi5wc3RhZ2dlcklucHV0LnJlc2V0VGFncyhmYWxzZSk7XG4gICAgICAgIHNlbGYuY3VycmVudFRhZ3NMaXN0ID0gW107XG4gICAgICB9XG4gICAgICBjb25zdCBtZW51Q2F0ZWdvcnlUb1RyaWdnZXIgPSAkKGAke3NlbGYuY2F0ZWdvcnlJdGVtU2VsZWN0b3J9W2RhdGEtY2F0ZWdvcnktcmVmPVwiJHtyZWZDYXRlZ29yeX1cIl1gKTtcblxuICAgICAgaWYgKCFtZW51Q2F0ZWdvcnlUb1RyaWdnZXIubGVuZ3RoKSB7XG4gICAgICAgIGNvbnNvbGUud2FybihgTm8gY2F0ZWdvcnkgd2l0aCByZWYgKCR7cmVmQ2F0ZWdvcnl9KSBzZWVtcyB0byBleGlzdCFgKTtcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgfVxuXG4gICAgICAvLyBIaWRlIGN1cnJlbnQgY2F0ZWdvcnkgZ3JpZFxuICAgICAgaWYgKHNlbGYuaXNDYXRlZ29yeUdyaWREaXNwbGF5ZWQgPT09IHRydWUpIHtcbiAgICAgICAgJChzZWxmLmNhdGVnb3J5R3JpZFNlbGVjdG9yKS5mYWRlT3V0KCk7XG4gICAgICAgIHNlbGYuaXNDYXRlZ29yeUdyaWREaXNwbGF5ZWQgPSBmYWxzZTtcbiAgICAgIH1cblxuICAgICAgLy8gVHJpZ2dlciBjbGljayBvbiByaWdodCBjYXRlZ29yeVxuICAgICAgJChgJHtzZWxmLmNhdGVnb3J5SXRlbVNlbGVjdG9yfVtkYXRhLWNhdGVnb3J5LXJlZj1cIiR7cmVmQ2F0ZWdvcnl9XCJdYCkuY2xpY2soKTtcbiAgICAgIHJldHVybiB0cnVlO1xuICAgIH0pO1xuICB9XG5cbiAgaW5pdEN1cnJlbnREaXNwbGF5KCkge1xuICAgIHRoaXMuY3VycmVudERpc3BsYXkgPSB0aGlzLmN1cnJlbnREaXNwbGF5ID09PSAnJyA/IHRoaXMuRElTUExBWV9MSVNUIDogdGhpcy5ESVNQTEFZX0dSSUQ7XG4gIH1cblxuICBpbml0U29ydGluZ0Ryb3Bkb3duKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgc2VsZi5jdXJyZW50U29ydGluZyA9ICQodGhpcy5tb2R1bGVTb3J0aW5nRHJvcGRvd25TZWxlY3RvcikuZmluZCgnOmNoZWNrZWQnKS5hdHRyKCd2YWx1ZScpO1xuICAgIGlmICghc2VsZi5jdXJyZW50U29ydGluZykge1xuICAgICAgc2VsZi5jdXJyZW50U29ydGluZyA9ICdhY2Nlc3MtZGVzYyc7XG4gICAgfVxuXG4gICAgJCgnYm9keScpLm9uKFxuICAgICAgJ2NoYW5nZScsXG4gICAgICBzZWxmLm1vZHVsZVNvcnRpbmdEcm9wZG93blNlbGVjdG9yLFxuICAgICAgZnVuY3Rpb24gaW5pdGlhbGl6ZUJvZHlTb3J0aW5nQ2hhbmdlKCkge1xuICAgICAgICBzZWxmLmN1cnJlbnRTb3J0aW5nID0gJCh0aGlzKS5maW5kKCc6Y2hlY2tlZCcpLmF0dHIoJ3ZhbHVlJyk7XG4gICAgICAgIHNlbGYudXBkYXRlTW9kdWxlVmlzaWJpbGl0eSgpO1xuICAgICAgfVxuICAgICk7XG4gIH1cblxuICBkb0J1bGtBY3Rpb24ocmVxdWVzdGVkQnVsa0FjdGlvbikge1xuICAgIC8vIFRoaXMgb2JqZWN0IGlzIHVzZWQgdG8gY2hlY2sgaWYgcmVxdWVzdGVkIGJ1bGtBY3Rpb24gaXMgYXZhaWxhYmxlIGFuZCBnaXZlIHByb3BlclxuICAgIC8vIHVybCBzZWdtZW50IHRvIGJlIGNhbGxlZCBmb3IgaXRcbiAgICBjb25zdCBmb3JjZURlbGV0aW9uID0gJCgnI2ZvcmNlX2J1bGtfZGVsZXRpb24nKS5wcm9wKCdjaGVja2VkJyk7XG5cbiAgICBjb25zdCBidWxrQWN0aW9uVG9VcmwgPSB7XG4gICAgICAnYnVsay11bmluc3RhbGwnOiAndW5pbnN0YWxsJyxcbiAgICAgICdidWxrLWRpc2FibGUnOiAnZGlzYWJsZScsXG4gICAgICAnYnVsay1lbmFibGUnOiAnZW5hYmxlJyxcbiAgICAgICdidWxrLWRpc2FibGUtbW9iaWxlJzogJ2Rpc2FibGVfbW9iaWxlJyxcbiAgICAgICdidWxrLWVuYWJsZS1tb2JpbGUnOiAnZW5hYmxlX21vYmlsZScsXG4gICAgICAnYnVsay1yZXNldCc6ICdyZXNldCcsXG4gICAgfTtcblxuICAgIC8vIE5vdGUgbm8gZ3JpZCBzZWxlY3RvciB1c2VkIHlldCBzaW5jZSB3ZSBkbyBub3QgbmVlZGVkIGl0IGF0IGRldiB0aW1lXG4gICAgLy8gTWF5YmUgdXNlZnVsIHRvIGltcGxlbWVudCB0aGlzIGtpbmQgb2YgdGhpbmdzIGxhdGVyIGlmIGludGVuZGVkIHRvXG4gICAgLy8gdXNlIHRoaXMgZnVuY3Rpb25hbGl0eSBlbHNld2hlcmUgYnV0IFwibWFuYWdlIG15IG1vZHVsZVwiIHNlY3Rpb25cbiAgICBpZiAodHlwZW9mIGJ1bGtBY3Rpb25Ub1VybFtyZXF1ZXN0ZWRCdWxrQWN0aW9uXSA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IHdpbmRvdy50cmFuc2xhdGVfamF2YXNjcmlwdHNbJ0J1bGsgQWN0aW9uIC0gUmVxdWVzdCBub3QgZm91bmQnXS5yZXBsYWNlKCdbMV0nLCByZXF1ZXN0ZWRCdWxrQWN0aW9uKX0pO1xuICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cblxuICAgIC8vIExvb3Agb3ZlciBhbGwgY2hlY2tlZCBidWxrIGNoZWNrYm94ZXNcbiAgICBjb25zdCBidWxrQWN0aW9uU2VsZWN0ZWRTZWxlY3RvciA9IHRoaXMuZ2V0QnVsa0NoZWNrYm94ZXNDaGVja2VkU2VsZWN0b3IoKTtcbiAgICBjb25zdCBidWxrTW9kdWxlQWN0aW9uID0gYnVsa0FjdGlvblRvVXJsW3JlcXVlc3RlZEJ1bGtBY3Rpb25dO1xuXG4gICAgaWYgKCQoYnVsa0FjdGlvblNlbGVjdGVkU2VsZWN0b3IpLmxlbmd0aCA8PSAwKSB7XG4gICAgICBjb25zb2xlLndhcm4od2luZG93LnRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snQnVsayBBY3Rpb24gLSBPbmUgbW9kdWxlIG1pbmltdW0nXSk7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuXG4gICAgY29uc3QgbW9kdWxlc0FjdGlvbnMgPSBbXTtcbiAgICBsZXQgbW9kdWxlVGVjaE5hbWU7XG4gICAgJChidWxrQWN0aW9uU2VsZWN0ZWRTZWxlY3RvcikuZWFjaChmdW5jdGlvbiBidWxrQWN0aW9uU2VsZWN0b3IoKSB7XG4gICAgICBtb2R1bGVUZWNoTmFtZSA9ICQodGhpcykuZGF0YSgndGVjaC1uYW1lJyk7XG4gICAgICBtb2R1bGVzQWN0aW9ucy5wdXNoKHtcbiAgICAgICAgdGVjaE5hbWU6IG1vZHVsZVRlY2hOYW1lLFxuICAgICAgICBhY3Rpb25NZW51T2JqOiAkKHRoaXMpLmNsb3Nlc3QoJy5tb2R1bGUtY2hlY2tib3gtYnVsay1saXN0JykubmV4dCgpLFxuICAgICAgfSk7XG4gICAgfSk7XG5cbiAgICB0aGlzLnBlcmZvcm1Nb2R1bGVzQWN0aW9uKG1vZHVsZXNBY3Rpb25zLCBidWxrTW9kdWxlQWN0aW9uLCBmb3JjZURlbGV0aW9uKTtcblxuICAgIHJldHVybiB0cnVlO1xuICB9XG5cbiAgcGVyZm9ybU1vZHVsZXNBY3Rpb24obW9kdWxlc0FjdGlvbnMsIGJ1bGtNb2R1bGVBY3Rpb24sIGZvcmNlRGVsZXRpb24pIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBpZiAodHlwZW9mIHNlbGYubW9kdWxlQ2FyZENvbnRyb2xsZXIgPT09ICd1bmRlZmluZWQnKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgLy9GaXJzdCBsZXQncyBmaWx0ZXIgbW9kdWxlcyB0aGF0IGNhbid0IHBlcmZvcm0gdGhpcyBhY3Rpb25cbiAgICBsZXQgYWN0aW9uTWVudUxpbmtzID0gZmlsdGVyQWxsb3dlZEFjdGlvbnMobW9kdWxlc0FjdGlvbnMpO1xuICAgIGlmICghYWN0aW9uTWVudUxpbmtzLmxlbmd0aCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGxldCBtb2R1bGVzUmVxdWVzdGVkQ291bnRkb3duID0gYWN0aW9uTWVudUxpbmtzLmxlbmd0aCAtIDE7XG4gICAgbGV0IHNwaW5uZXJPYmogPSAkKFwiPGJ1dHRvbiBjbGFzcz1cXFwiYnRuLXByaW1hcnktcmV2ZXJzZSBvbmNsaWNrIHVuYmluZCBzcGlubmVyIFxcXCI+PC9idXR0b24+XCIpO1xuICAgIGlmIChhY3Rpb25NZW51TGlua3MubGVuZ3RoID4gMSkge1xuICAgICAgLy9Mb29wIHRocm91Z2ggYWxsIHRoZSBtb2R1bGVzIGV4Y2VwdCB0aGUgbGFzdCBvbmUgd2hpY2ggd2FpdHMgZm9yIG90aGVyXG4gICAgICAvL3JlcXVlc3RzIGFuZCB0aGVuIGNhbGwgaXRzIHJlcXVlc3Qgd2l0aCBjYWNoZSBjbGVhciBlbmFibGVkXG4gICAgICAkLmVhY2goYWN0aW9uTWVudUxpbmtzLCBmdW5jdGlvbiBidWxrTW9kdWxlc0xvb3AoaW5kZXgsIGFjdGlvbk1lbnVMaW5rKSB7XG4gICAgICAgIGlmIChpbmRleCA+PSBhY3Rpb25NZW51TGlua3MubGVuZ3RoIC0gMSkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICByZXF1ZXN0TW9kdWxlQWN0aW9uKGFjdGlvbk1lbnVMaW5rLCB0cnVlLCBjb3VudGRvd25Nb2R1bGVzUmVxdWVzdCk7XG4gICAgICB9KTtcbiAgICAgIC8vRGlzcGxheSBhIHNwaW5uZXIgZm9yIHRoZSBsYXN0IG1vZHVsZVxuICAgICAgY29uc3QgbGFzdE1lbnVMaW5rID0gYWN0aW9uTWVudUxpbmtzW2FjdGlvbk1lbnVMaW5rcy5sZW5ndGggLSAxXTtcbiAgICAgIGNvbnN0IGFjdGlvbk1lbnVPYmogPSBsYXN0TWVudUxpbmsuY2xvc2VzdChzZWxmLm1vZHVsZUNhcmRDb250cm9sbGVyLm1vZHVsZUl0ZW1BY3Rpb25zU2VsZWN0b3IpO1xuICAgICAgYWN0aW9uTWVudU9iai5oaWRlKCk7XG4gICAgICBhY3Rpb25NZW51T2JqLmFmdGVyKHNwaW5uZXJPYmopO1xuICAgIH0gZWxzZSB7XG4gICAgICByZXF1ZXN0TW9kdWxlQWN0aW9uKGFjdGlvbk1lbnVMaW5rc1swXSk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gcmVxdWVzdE1vZHVsZUFjdGlvbihhY3Rpb25NZW51TGluaywgZGlzYWJsZUNhY2hlQ2xlYXIsIHJlcXVlc3RFbmRDYWxsYmFjaykge1xuICAgICAgc2VsZi5tb2R1bGVDYXJkQ29udHJvbGxlci5fcmVxdWVzdFRvQ29udHJvbGxlcihcbiAgICAgICAgYnVsa01vZHVsZUFjdGlvbixcbiAgICAgICAgYWN0aW9uTWVudUxpbmssXG4gICAgICAgIGZvcmNlRGVsZXRpb24sXG4gICAgICAgIGRpc2FibGVDYWNoZUNsZWFyLFxuICAgICAgICByZXF1ZXN0RW5kQ2FsbGJhY2tcbiAgICAgICk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gY291bnRkb3duTW9kdWxlc1JlcXVlc3QoKSB7XG4gICAgICBtb2R1bGVzUmVxdWVzdGVkQ291bnRkb3duLS07XG4gICAgICAvL05vdyB0aGF0IGFsbCBvdGhlciBtb2R1bGVzIGhhdmUgcGVyZm9ybWVkIHRoZWlyIGFjdGlvbiBXSVRIT1VUIGNhY2hlIGNsZWFyLCB3ZVxuICAgICAgLy9jYW4gcmVxdWVzdCB0aGUgbGFzdCBtb2R1bGUgcmVxdWVzdCBXSVRIIGNhY2hlIGNsZWFyXG4gICAgICBpZiAobW9kdWxlc1JlcXVlc3RlZENvdW50ZG93biA8PSAwKSB7XG4gICAgICAgIGlmIChzcGlubmVyT2JqKSB7XG4gICAgICAgICAgc3Bpbm5lck9iai5yZW1vdmUoKTtcbiAgICAgICAgICBzcGlubmVyT2JqID0gbnVsbDtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IGxhc3RNZW51TGluayA9IGFjdGlvbk1lbnVMaW5rc1thY3Rpb25NZW51TGlua3MubGVuZ3RoIC0gMV07XG4gICAgICAgIGNvbnN0IGFjdGlvbk1lbnVPYmogPSBsYXN0TWVudUxpbmsuY2xvc2VzdChzZWxmLm1vZHVsZUNhcmRDb250cm9sbGVyLm1vZHVsZUl0ZW1BY3Rpb25zU2VsZWN0b3IpO1xuICAgICAgICBhY3Rpb25NZW51T2JqLmZhZGVJbigpO1xuICAgICAgICByZXF1ZXN0TW9kdWxlQWN0aW9uKGxhc3RNZW51TGluayk7XG4gICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gZmlsdGVyQWxsb3dlZEFjdGlvbnMobW9kdWxlc0FjdGlvbnMpIHtcbiAgICAgIGxldCBhY3Rpb25NZW51TGlua3MgPSBbXTtcbiAgICAgIGxldCBhY3Rpb25NZW51TGluaztcbiAgICAgICQuZWFjaChtb2R1bGVzQWN0aW9ucywgZnVuY3Rpb24gZmlsdGVyQWxsb3dlZE1vZHVsZXMoaW5kZXgsIG1vZHVsZURhdGEpIHtcbiAgICAgICAgYWN0aW9uTWVudUxpbmsgPSAkKFxuICAgICAgICAgIHNlbGYubW9kdWxlQ2FyZENvbnRyb2xsZXIubW9kdWxlQWN0aW9uTWVudUxpbmtTZWxlY3RvciArIGJ1bGtNb2R1bGVBY3Rpb24sXG4gICAgICAgICAgbW9kdWxlRGF0YS5hY3Rpb25NZW51T2JqXG4gICAgICAgICk7XG4gICAgICAgIGlmIChhY3Rpb25NZW51TGluay5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgYWN0aW9uTWVudUxpbmtzLnB1c2goYWN0aW9uTWVudUxpbmspO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IHdpbmRvdy50cmFuc2xhdGVfamF2YXNjcmlwdHNbJ0J1bGsgQWN0aW9uIC0gUmVxdWVzdCBub3QgYXZhaWxhYmxlIGZvciBtb2R1bGUnXVxuICAgICAgICAgICAgICAucmVwbGFjZSgnWzFdJywgYnVsa01vZHVsZUFjdGlvbilcbiAgICAgICAgICAgICAgLnJlcGxhY2UoJ1syXScsIG1vZHVsZURhdGEudGVjaE5hbWUpfSk7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuXG4gICAgICByZXR1cm4gYWN0aW9uTWVudUxpbmtzO1xuICAgIH1cbiAgfVxuXG4gIGluaXRBY3Rpb25CdXR0b25zKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgICQoJ2JvZHknKS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICBzZWxmLm1vZHVsZUluc3RhbGxCdG5TZWxlY3RvcixcbiAgICAgIGZ1bmN0aW9uIGluaXRpYWxpemVBY3Rpb25CdXR0b25zQ2xpY2soZXZlbnQpIHtcbiAgICAgICAgY29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICBjb25zdCAkbmV4dCA9ICQoJHRoaXMubmV4dCgpKTtcbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICAkdGhpcy5oaWRlKCk7XG4gICAgICAgICRuZXh0LnNob3coKTtcblxuICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgIHVybDogJHRoaXMuZGF0YSgndXJsJyksXG4gICAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgICAgfSkuZG9uZSgoKSA9PiB7XG4gICAgICAgICAgJG5leHQuZmFkZU91dCgpO1xuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICApO1xuXG4gICAgLy8gXCJVcGdyYWRlIEFsbFwiIGJ1dHRvbiBoYW5kbGVyXG4gICAgJCgnYm9keScpLm9uKCdjbGljaycsIHNlbGYudXBncmFkZUFsbFNvdXJjZSwgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICBpZiAoJChzZWxmLnVwZ3JhZGVBbGxUYXJnZXRzKS5sZW5ndGggPD0gMCkge1xuICAgICAgICBjb25zb2xlLndhcm4od2luZG93LnRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snVXBncmFkZSBBbGwgQWN0aW9uIC0gT25lIG1vZHVsZSBtaW5pbXVtJ10pO1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICB9XG5cbiAgICAgIGNvbnN0IG1vZHVsZXNBY3Rpb25zID0gW107XG4gICAgICBsZXQgbW9kdWxlVGVjaE5hbWU7XG4gICAgICAkKHNlbGYudXBncmFkZUFsbFRhcmdldHMpLmVhY2goZnVuY3Rpb24gYnVsa0FjdGlvblNlbGVjdG9yKCkge1xuICAgICAgICBjb25zdCBtb2R1bGVJdGVtTGlzdCA9ICQodGhpcykuY2xvc2VzdCgnLm1vZHVsZS1pdGVtLWxpc3QnKTtcbiAgICAgICAgbW9kdWxlVGVjaE5hbWUgPSBtb2R1bGVJdGVtTGlzdC5kYXRhKCd0ZWNoLW5hbWUnKTtcbiAgICAgICAgbW9kdWxlc0FjdGlvbnMucHVzaCh7XG4gICAgICAgICAgdGVjaE5hbWU6IG1vZHVsZVRlY2hOYW1lLFxuICAgICAgICAgIGFjdGlvbk1lbnVPYmo6ICQoJy5tb2R1bGUtYWN0aW9ucycsIG1vZHVsZUl0ZW1MaXN0KSxcbiAgICAgICAgfSk7XG4gICAgICB9KTtcblxuICAgICAgdGhpcy5wZXJmb3JtTW9kdWxlc0FjdGlvbihtb2R1bGVzQWN0aW9ucywgJ3VwZ3JhZGUnKTtcblxuICAgICAgcmV0dXJuIHRydWU7XG4gICAgfSk7XG4gIH1cblxuICBpbml0Q2F0ZWdvcnlTZWxlY3QoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgY29uc3QgYm9keSA9ICQoJ2JvZHknKTtcbiAgICBib2R5Lm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgIHNlbGYuY2F0ZWdvcnlJdGVtU2VsZWN0b3IsXG4gICAgICBmdW5jdGlvbiBpbml0aWFsaXplQ2F0ZWdvcnlTZWxlY3RDbGljaygpIHtcbiAgICAgICAgLy8gR2V0IGRhdGEgZnJvbSBsaSBET00gaW5wdXRcbiAgICAgICAgc2VsZi5jdXJyZW50UmVmQ2F0ZWdvcnkgPSAkKHRoaXMpLmRhdGEoJ2NhdGVnb3J5LXJlZicpO1xuICAgICAgICBzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeSA9IHNlbGYuY3VycmVudFJlZkNhdGVnb3J5ID8gU3RyaW5nKHNlbGYuY3VycmVudFJlZkNhdGVnb3J5KS50b0xvd2VyQ2FzZSgpIDogbnVsbDtcbiAgICAgICAgLy8gQ2hhbmdlIGRyb3Bkb3duIGxhYmVsIHRvIHNldCBpdCB0byB0aGUgY3VycmVudCBjYXRlZ29yeSdzIGRpc3BsYXluYW1lXG4gICAgICAgICQoc2VsZi5jYXRlZ29yeVNlbGVjdG9yTGFiZWxTZWxlY3RvcikudGV4dCgkKHRoaXMpLmRhdGEoJ2NhdGVnb3J5LWRpc3BsYXktbmFtZScpKTtcbiAgICAgICAgJChzZWxmLmNhdGVnb3J5UmVzZXRCdG5TZWxlY3Rvcikuc2hvdygpO1xuICAgICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICAgIH1cbiAgICApO1xuXG4gICAgYm9keS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICBzZWxmLmNhdGVnb3J5UmVzZXRCdG5TZWxlY3RvcixcbiAgICAgIGZ1bmN0aW9uIGluaXRpYWxpemVDYXRlZ29yeVJlc2V0QnV0dG9uQ2xpY2soKSB7XG4gICAgICAgIGNvbnN0IHJhd1RleHQgPSAkKHNlbGYuY2F0ZWdvcnlTZWxlY3RvcikuYXR0cignYXJpYS1sYWJlbGxlZGJ5Jyk7XG4gICAgICAgIGNvbnN0IHVwcGVyRmlyc3RMZXR0ZXIgPSByYXdUZXh0LmNoYXJBdCgwKS50b1VwcGVyQ2FzZSgpO1xuICAgICAgICBjb25zdCByZW1vdmVkRmlyc3RMZXR0ZXIgPSByYXdUZXh0LnNsaWNlKDEpO1xuICAgICAgICBjb25zdCBvcmlnaW5hbFRleHQgPSB1cHBlckZpcnN0TGV0dGVyICsgcmVtb3ZlZEZpcnN0TGV0dGVyO1xuXG4gICAgICAgICQoc2VsZi5jYXRlZ29yeVNlbGVjdG9yTGFiZWxTZWxlY3RvcikudGV4dChvcmlnaW5hbFRleHQpO1xuICAgICAgICAkKHRoaXMpLmhpZGUoKTtcbiAgICAgICAgc2VsZi5jdXJyZW50UmVmQ2F0ZWdvcnkgPSBudWxsO1xuICAgICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICAgIH1cbiAgICApO1xuICB9XG5cbiAgaW5pdFNlYXJjaEJsb2NrKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIHNlbGYucHN0YWdnZXJJbnB1dCA9ICQoJyNtb2R1bGUtc2VhcmNoLWJhcicpLnBzdGFnZ2VyKHtcbiAgICAgIG9uVGFnc0NoYW5nZWQ6ICh0YWdMaXN0KSA9PiB7XG4gICAgICAgIHNlbGYuY3VycmVudFRhZ3NMaXN0ID0gdGFnTGlzdDtcbiAgICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgICB9LFxuICAgICAgb25SZXNldFRhZ3M6ICgpID0+IHtcbiAgICAgICAgc2VsZi5jdXJyZW50VGFnc0xpc3QgPSBbXTtcbiAgICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgICB9LFxuICAgICAgaW5wdXRQbGFjZWhvbGRlcjogd2luZG93LnRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snU2VhcmNoIC0gcGxhY2Vob2xkZXInXSxcbiAgICAgIGNsb3NpbmdDcm9zczogdHJ1ZSxcbiAgICAgIGNvbnRleHQ6IHNlbGYsXG4gICAgfSk7XG5cbiAgICAkKCdib2R5Jykub24oJ2NsaWNrJywgJy5tb2R1bGUtYWRkb25zLXNlYXJjaC1saW5rJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICB3aW5kb3cub3BlbigkKHRoaXMpLmF0dHIoJ2hyZWYnKSwgJ19ibGFuaycpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRpYWxpemUgZGlzcGxheSBzd2l0Y2hpbmcgYmV0d2VlbiBMaXN0IG9yIEdyaWRcbiAgICovXG4gIGluaXRTb3J0aW5nRGlzcGxheVN3aXRjaCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoJ2JvZHknKS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICAnLm1vZHVsZS1zb3J0LXN3aXRjaCcsXG4gICAgICBmdW5jdGlvbiBzd2l0Y2hTb3J0KCkge1xuICAgICAgICBjb25zdCBzd2l0Y2hUbyA9ICQodGhpcykuZGF0YSgnc3dpdGNoJyk7XG4gICAgICAgIGNvbnN0IGlzQWxyZWFkeURpc3BsYXllZCA9ICQodGhpcykuaGFzQ2xhc3MoJ2FjdGl2ZS1kaXNwbGF5Jyk7XG4gICAgICAgIGlmICh0eXBlb2Ygc3dpdGNoVG8gIT09ICd1bmRlZmluZWQnICYmIGlzQWxyZWFkeURpc3BsYXllZCA9PT0gZmFsc2UpIHtcbiAgICAgICAgICBzZWxmLnN3aXRjaFNvcnRpbmdEaXNwbGF5VG8oc3dpdGNoVG8pO1xuICAgICAgICAgIHNlbGYuY3VycmVudERpc3BsYXkgPSBzd2l0Y2hUbztcbiAgICAgICAgfVxuICAgICAgfVxuICAgICk7XG4gIH1cblxuICBzd2l0Y2hTb3J0aW5nRGlzcGxheVRvKHN3aXRjaFRvKSB7XG4gICAgaWYgKHN3aXRjaFRvICE9PSB0aGlzLkRJU1BMQVlfR1JJRCAmJiBzd2l0Y2hUbyAhPT0gdGhpcy5ESVNQTEFZX0xJU1QpIHtcbiAgICAgIGNvbnNvbGUuZXJyb3IoYENhbid0IHN3aXRjaCB0byB1bmRlZmluZWQgZGlzcGxheSBwcm9wZXJ0eSBcIiR7c3dpdGNoVG99XCJgKTtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAkKCcubW9kdWxlLXNvcnQtc3dpdGNoJykucmVtb3ZlQ2xhc3MoJ21vZHVsZS1zb3J0LWFjdGl2ZScpO1xuICAgICQoYCNtb2R1bGUtc29ydC0ke3N3aXRjaFRvfWApLmFkZENsYXNzKCdtb2R1bGUtc29ydC1hY3RpdmUnKTtcbiAgICB0aGlzLmN1cnJlbnREaXNwbGF5ID0gc3dpdGNoVG87XG4gICAgdGhpcy51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gIH1cblxuICBpbml0aWFsaXplU2VlTW9yZSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoYCR7c2VsZi5tb2R1bGVTaG9ydExpc3R9ICR7c2VsZi5zZWVNb3JlU2VsZWN0b3J9YCkub24oJ2NsaWNrJywgZnVuY3Rpb24gc2VlTW9yZSgpIHtcbiAgICAgIHNlbGYuY3VycmVudENhdGVnb3J5RGlzcGxheVskKHRoaXMpLmRhdGEoJ2NhdGVnb3J5JyldID0gdHJ1ZTtcbiAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgJCh0aGlzKS5jbG9zZXN0KHNlbGYubW9kdWxlU2hvcnRMaXN0KS5maW5kKHNlbGYuc2VlTGVzc1NlbGVjdG9yKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICB9KTtcblxuICAgICQoYCR7c2VsZi5tb2R1bGVTaG9ydExpc3R9ICR7c2VsZi5zZWVMZXNzU2VsZWN0b3J9YCkub24oJ2NsaWNrJywgZnVuY3Rpb24gc2VlTW9yZSgpIHtcbiAgICAgIHNlbGYuY3VycmVudENhdGVnb3J5RGlzcGxheVskKHRoaXMpLmRhdGEoJ2NhdGVnb3J5JyldID0gZmFsc2U7XG4gICAgICAkKHRoaXMpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgICQodGhpcykuY2xvc2VzdChzZWxmLm1vZHVsZVNob3J0TGlzdCkuZmluZChzZWxmLnNlZU1vcmVTZWxlY3RvcikucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgfSk7XG4gIH1cblxuICB1cGRhdGVUb3RhbFJlc3VsdHMoKSB7XG4gICAgY29uc3QgcmVwbGFjZUZpcnN0V29yZEJ5ID0gKGVsZW1lbnQsIHZhbHVlKSA9PiB7XG4gICAgICBjb25zdCBleHBsb2RlZFRleHQgPSBlbGVtZW50LnRleHQoKS5zcGxpdCgnICcpO1xuICAgICAgZXhwbG9kZWRUZXh0WzBdID0gdmFsdWU7XG4gICAgICBlbGVtZW50LnRleHQoZXhwbG9kZWRUZXh0LmpvaW4oJyAnKSk7XG4gICAgfTtcblxuICAgIC8vIElmIHRoZXJlIGFyZSBzb21lIHNob3J0bGlzdDogZWFjaCBzaG9ydGxpc3QgY291bnQgdGhlIG1vZHVsZXMgb24gdGhlIG5leHQgY29udGFpbmVyLlxuICAgIGNvbnN0ICRzaG9ydExpc3RzID0gJCgnLm1vZHVsZS1zaG9ydC1saXN0Jyk7XG4gICAgaWYgKCRzaG9ydExpc3RzLmxlbmd0aCA+IDApIHtcbiAgICAgICRzaG9ydExpc3RzLmVhY2goZnVuY3Rpb24gc2hvcnRMaXN0cygpIHtcbiAgICAgICAgY29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICByZXBsYWNlRmlyc3RXb3JkQnkoXG4gICAgICAgICAgJHRoaXMuZmluZCgnLm1vZHVsZS1zZWFyY2gtcmVzdWx0LXdvcmRpbmcnKSxcbiAgICAgICAgICAkdGhpcy5uZXh0KCcubW9kdWxlcy1saXN0JykuZmluZCgnLm1vZHVsZS1pdGVtJykubGVuZ3RoXG4gICAgICAgICk7XG4gICAgICB9KTtcblxuICAgICAgLy8gSWYgdGhlcmUgaXMgbm8gc2hvcnRsaXN0OiB0aGUgd29yZGluZyBkaXJlY3RseSB1cGRhdGUgZnJvbSB0aGUgb25seSBtb2R1bGUgY29udGFpbmVyLlxuICAgIH0gZWxzZSB7XG4gICAgICBjb25zdCBtb2R1bGVzQ291bnQgPSAkKCcubW9kdWxlcy1saXN0JykuZmluZCgnLm1vZHVsZS1pdGVtJykubGVuZ3RoO1xuICAgICAgcmVwbGFjZUZpcnN0V29yZEJ5KCQoJy5tb2R1bGUtc2VhcmNoLXJlc3VsdC13b3JkaW5nJyksIG1vZHVsZXNDb3VudCk7XG5cbiAgICAgIGNvbnN0IHNlbGVjdG9yVG9Ub2dnbGUgPSAoc2VsZi5jdXJyZW50RGlzcGxheSA9PT0gc2VsZi5ESVNQTEFZX0xJU1QpID9cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmFkZG9uSXRlbUxpc3RTZWxlY3RvciA6XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5hZGRvbkl0ZW1HcmlkU2VsZWN0b3I7XG4gICAgICAkKHNlbGVjdG9yVG9Ub2dnbGUpLnRvZ2dsZShtb2R1bGVzQ291bnQgIT09ICh0aGlzLm1vZHVsZXNMaXN0Lmxlbmd0aCAvIDIpKTtcblxuICAgICAgaWYgKG1vZHVsZXNDb3VudCA9PT0gMCkge1xuICAgICAgICAkKCcubW9kdWxlLWFkZG9ucy1zZWFyY2gtbGluaycpLmF0dHIoXG4gICAgICAgICAgJ2hyZWYnLFxuICAgICAgICAgIGAke3RoaXMuYmFzZUFkZG9uc1VybH1zZWFyY2gucGhwP3NlYXJjaF9xdWVyeT0ke2VuY29kZVVSSUNvbXBvbmVudCh0aGlzLmN1cnJlbnRUYWdzTGlzdC5qb2luKCcgJykpfWBcbiAgICAgICAgKTtcbiAgICAgIH1cbiAgICB9XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgQWRtaW5Nb2R1bGVDb250cm9sbGVyO1xuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IE1vZHVsZUNhcmQgZnJvbSAnLi4vLi4vY29tcG9uZW50cy9tb2R1bGUtY2FyZCc7XG5pbXBvcnQgQWRtaW5Nb2R1bGVDb250cm9sbGVyIGZyb20gJy4vY29udHJvbGxlcic7XG5pbXBvcnQgTW9kdWxlTG9hZGVyIGZyb20gJy4vbG9hZGVyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgY29uc3QgbW9kdWxlQ2FyZENvbnRyb2xsZXIgPSBuZXcgTW9kdWxlQ2FyZCgpO1xuICBuZXcgTW9kdWxlTG9hZGVyKCk7XG4gIG5ldyBBZG1pbk1vZHVsZUNvbnRyb2xsZXIobW9kdWxlQ2FyZENvbnRyb2xsZXIpO1xufSk7XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogTW9kdWxlIEFkbWluIFBhZ2UgTG9hZGVyLlxuICogQGNvbnN0cnVjdG9yXG4gKi9cbmNsYXNzIE1vZHVsZUxvYWRlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIE1vZHVsZUxvYWRlci5oYW5kbGVJbXBvcnQoKTtcbiAgICBNb2R1bGVMb2FkZXIuaGFuZGxlRXZlbnRzKCk7XG4gIH1cblxuICBzdGF0aWMgaGFuZGxlSW1wb3J0KCkge1xuICAgIGNvbnN0IG1vZHVsZUltcG9ydCA9ICQoJyNtb2R1bGUtaW1wb3J0Jyk7XG4gICAgbW9kdWxlSW1wb3J0LmNsaWNrKCgpID0+IHtcbiAgICAgIG1vZHVsZUltcG9ydC5hZGRDbGFzcygnb25jbGljaycsIDI1MCwgdmFsaWRhdGUpO1xuICAgIH0pO1xuXG4gICAgZnVuY3Rpb24gdmFsaWRhdGUoKSB7XG4gICAgICBzZXRUaW1lb3V0KFxuICAgICAgICAoKSA9PiB7XG4gICAgICAgICAgbW9kdWxlSW1wb3J0LnJlbW92ZUNsYXNzKCdvbmNsaWNrJyk7XG4gICAgICAgICAgbW9kdWxlSW1wb3J0LmFkZENsYXNzKCd2YWxpZGF0ZScsIDQ1MCwgY2FsbGJhY2spO1xuICAgICAgICB9LFxuICAgICAgICAyMjUwXG4gICAgICApO1xuICAgIH1cbiAgICBmdW5jdGlvbiBjYWxsYmFjaygpIHtcbiAgICAgIHNldFRpbWVvdXQoXG4gICAgICAgICgpID0+IHtcbiAgICAgICAgICBtb2R1bGVJbXBvcnQucmVtb3ZlQ2xhc3MoJ3ZhbGlkYXRlJyk7XG4gICAgICAgIH0sXG4gICAgICAgIDEyNTBcbiAgICAgICk7XG4gICAgfVxuICB9XG5cbiAgc3RhdGljIGhhbmRsZUV2ZW50cygpIHtcbiAgICAkKCdib2R5Jykub24oXG4gICAgICAnY2xpY2snLFxuICAgICAgJ2EubW9kdWxlLXJlYWQtbW9yZS1ncmlkLWJ0biwgYS5tb2R1bGUtcmVhZC1tb3JlLWxpc3QtYnRuJyxcbiAgICAgIChldmVudCkgPT4ge1xuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICBjb25zdCBtb2R1bGVQb3BwaW4gPSAkKGV2ZW50LnRhcmdldCkuZGF0YSgndGFyZ2V0Jyk7XG5cbiAgICAgICAgJC5nZXQoZXZlbnQudGFyZ2V0LmhyZWYsIChkYXRhKSA9PiB7XG4gICAgICAgICAgJChtb2R1bGVQb3BwaW4pLmh0bWwoZGF0YSk7XG4gICAgICAgICAgJChtb2R1bGVQb3BwaW4pLm1vZGFsKCk7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgICk7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTW9kdWxlTG9hZGVyO1xuIiwibW9kdWxlLmV4cG9ydHMgPSBqUXVlcnk7Il0sInNvdXJjZVJvb3QiOiIifQ==