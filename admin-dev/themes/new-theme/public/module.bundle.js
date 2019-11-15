window["module"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 365);
/******/ })
/************************************************************************/
/******/ ({

/***/ 12:
/***/ (function(module, exports) {

(function() { module.exports = window["jQuery"]; }());

/***/ }),

/***/ 274:
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
 * Module Admin Page Controller.
 * @constructor
 */

var AdminModuleController = function () {
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

    this.moduleShortList = '.module-short-list';
    // See more & See less selector
    this.seeMoreSelector = '.see-more';
    this.seeLessSelector = '.see-less';

    // Selectors into vars to make it easier to change them while keeping same code logic
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
    this.addonItemListSelector = '.module-addons-item-list';

    // Upgrade All selectors
    this.upgradeAllSource = '.module_action_menu_upgrade_all';
    this.upgradeAllTargets = '#modules-list-container-update .module_action_menu_upgrade:visible';

    // Bulk action selectors
    this.bulkActionDropDownSelector = '.module-bulk-actions';
    this.bulkItemSelector = '.module-bulk-menu';
    this.bulkActionCheckboxListSelector = '.module-checkbox-bulk-list input';
    this.bulkActionCheckboxGridSelector = '.module-checkbox-bulk-grid input';
    this.checkedBulkActionListSelector = this.bulkActionCheckboxListSelector + ':checked';
    this.checkedBulkActionGridSelector = this.bulkActionCheckboxGridSelector + ':checked';
    this.bulkActionCheckboxSelector = '#module-modal-bulk-checkbox';
    this.bulkConfirmModalSelector = '#module-modal-bulk-confirm';
    this.bulkConfirmModalActionNameSelector = '#module-modal-bulk-confirm-action-name';
    this.bulkConfirmModalListSelector = '#module-modal-bulk-confirm-list';
    this.bulkConfirmModalAckBtnSelector = '#module-modal-confirm-bulk-ack';

    // Placeholders
    this.placeholderGlobalSelector = '.module-placeholders-wrapper';
    this.placeholderFailureGlobalSelector = '.module-placeholders-failure';
    this.placeholderFailureMsgSelector = '.module-placeholders-failure-msg';
    this.placeholderFailureRetryBtnSelector = '#module-placeholders-failure-retry';

    // Module's statuses selectors
    this.statusSelectorLabelSelector = '.module-status-selector-label';
    this.statusItemSelector = '.module-status-menu';
    this.statusResetBtnSelector = '.module-status-reset';

    // Selectors for Module Import and Addons connect
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
    key: 'initFilterStatusDropdown',
    value: function initFilterStatusDropdown() {
      var self = this;
      var body = $('body');
      body.on('click', self.statusItemSelector, function () {
        // Get data from li DOM input
        self.currentRefStatus = parseInt($(this).data('status-ref'), 10);
        // Change dropdown label to set it to the current status' displayname
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
    key: 'initBulkDropdown',
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
          $.growl.warning({ message: window.translate_javascripts['Bulk Action - One module minimum'] });
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
    key: 'initBOEventRegistering',
    value: function initBOEventRegistering() {
      window.BOEvent.on('Module Disabled', this.onModuleDisabled, this);
      window.BOEvent.on('Module Uninstalled', this.updateTotalResults, this);
    }
  }, {
    key: 'onModuleDisabled',
    value: function onModuleDisabled() {
      var self = this;
      var moduleItemSelector = self.getModuleItemSelector();

      $('.modules-list').each(function scanModulesList() {
        self.updateTotalResults();
      });
    }
  }, {
    key: 'initPlaceholderMechanism',
    value: function initPlaceholderMechanism() {
      var self = this;
      if ($(self.placeholderGlobalSelector).length) {
        self.ajaxLoadPage();
      }

      // Retry loading mechanism
      $('body').on('click', self.placeholderFailureRetryBtnSelector, function () {
        $(self.placeholderFailureGlobalSelector).fadeOut();
        $(self.placeholderGlobalSelector).fadeIn();
        self.ajaxLoadPage();
      });
    }
  }, {
    key: 'ajaxLoadPage',
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
          var requiredSelectorCombination = moduleGlobalSelector + ',' + moduleSortingSelector;

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
    key: 'fetchModulesList',
    value: function fetchModulesList() {
      var self = this;
      var container = void 0;
      var $this = void 0;

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
    key: 'updateModuleSorting',
    value: function updateModuleSorting() {
      var self = this;

      if (!self.currentSorting) {
        return;
      }

      // Modules sorting
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
    key: 'updateModuleContainerDisplay',
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
          container.find(self.seeMoreSelector + ', ' + self.seeLessSelector).show();
        } else {
          container.find(self.seeMoreSelector + ', ' + self.seeLessSelector).hide();
        }
      });
    }
  }, {
    key: 'updateModuleVisibility',
    value: function updateModuleVisibility() {
      var self = this;

      self.updateModuleSorting();

      $(self.recentlyUsedSelector).find('.module-item').remove();
      $('.modules-list').find('.module-item').remove();

      // Modules visibility management
      var isVisible = void 0;
      var currentModule = void 0;
      var moduleCategory = void 0;
      var tagExists = void 0;
      var newValue = void 0;

      var modulesListLength = self.modulesList.length;
      var counter = {};

      for (var i = 0; i < modulesListLength; i += 1) {
        currentModule = self.modulesList[i];
        if (currentModule.display === self.currentDisplay) {
          isVisible = true;

          moduleCategory = self.currentRefCategory === self.CATEGORY_RECENTLY_USED ? self.CATEGORY_RECENTLY_USED : currentModule.categories;

          // Check for same category
          if (self.currentRefCategory !== null) {
            isVisible &= moduleCategory === self.currentRefCategory;
          }

          // Check for same status
          if (self.currentRefStatus !== null) {
            isVisible &= currentModule.active === self.currentRefStatus;
          }

          // Check for tag list
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
          }

          // If visible, display (Thx captain obvious)
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
    key: 'initPageChangeProtection',
    value: function initPageChangeProtection() {
      var self = this;

      $(window).on('beforeunload', function () {
        if (self.isUploadStarted === true) {
          return 'It seems some critical operation are running, are you sure you want to change page ? It might cause some unexepcted behaviors.';
        }
      });
    }
  }, {
    key: 'buildBulkActionModuleList',
    value: function buildBulkActionModuleList() {
      var checkBoxesSelector = this.getBulkCheckboxesCheckedSelector();
      var moduleItemSelector = this.getModuleItemSelector();
      var alreadyDoneFlag = 0;
      var htmlGenerated = '';
      var currentElement = void 0;

      $(checkBoxesSelector).each(function prepareCheckboxes() {
        if (alreadyDoneFlag === 10) {
          // Break each
          htmlGenerated += '- ...';
          return false;
        }

        currentElement = $(this).closest(moduleItemSelector);
        htmlGenerated += '- ' + currentElement.data('name') + '<br/>';
        alreadyDoneFlag += 1;

        return true;
      });

      return htmlGenerated;
    }
  }, {
    key: 'initAddonsConnect',
    value: function initAddonsConnect() {
      var self = this;

      // Make addons connect modal ready to be clicked
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
            $.growl.error({ message: response.message });
            $(self.addonsLoginButtonSelector).hide();
            $('button.btn[type="submit"]', self.addonsConnectForm).fadeIn();
          }
        });
      });
    }
  }, {
    key: 'initAddModuleAction',
    value: function initAddModuleAction() {
      var self = this;
      var addModuleButton = $(self.addonsImportModalBtnSelector);
      addModuleButton.attr('data-toggle', 'modal');
      addModuleButton.attr('data-target', self.dropZoneModalSelector);
    }
  }, {
    key: 'initDropzone',
    value: function initDropzone() {
      var self = this;
      var body = $('body');
      var dropzone = $('.dropzone');

      // Reset modal when click on Retry in case of failure
      body.on('click', this.moduleImportFailureRetrySelector, function () {
        $(self.moduleImportSuccessSelector + ',' + self.moduleImportFailureSelector + ',' + self.moduleImportProcessingSelector).fadeOut(function () {
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
      });

      // Reinit modal on exit, but check if not already processing something
      body.on('hidden.bs.modal', this.dropZoneModalSelector, function () {
        $(self.moduleImportSuccessSelector + ', ' + self.moduleImportFailureSelector).hide();
        $(self.moduleImportStartSelector).show();

        dropzone.removeAttr('style');
        $(self.moduleImportFailureMsgDetailsSelector).hide();
        $(self.moduleImportSuccessConfigureBtnSelector).hide();
        $(self.dropZoneModalFooterSelector).html('');
        $(self.moduleImportConfirmSelector).hide();
      });

      // Change the way Dropzone.js lib handle file input trigger
      body.on('click', '.dropzone:not(' + this.moduleImportSelectFileManualSelector + ', ' + this.moduleImportSuccessConfigureBtnSelector + ')', function (event, manualSelect) {
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
      });

      // Handle modal closure
      body.on('click', this.moduleImportModalCloseBtn, function () {
        if (self.isUploadStarted !== true) {
          $(self.dropZoneModalSelector).modal('hide');
        }
      });

      // Fix issue on click configure button
      body.on('click', this.moduleImportSuccessConfigureBtnSelector, function initializeBodyClickOnModuleImport(event) {
        event.stopPropagation();
        event.preventDefault();
        window.location = $(this).attr('href');
      });

      // Open failure message details box
      body.on('click', this.moduleImportFailureDetailsBtnSelector, function () {
        $(self.moduleImportFailureMsgDetailsSelector).slideDown();
      });

      // @see: dropzone.js
      var dropzoneOptions = {
        url: window.moduleURLs.moduleImport,
        acceptedFiles: '.zip, .tar',
        // The name that will be used to transfer the file
        paramName: 'file_uploaded',
        maxFilesize: 50, // can't be greater than 50Mb because it's an addons limitation
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
        processing: function processing() {
          // Leave it empty since we don't require anything while processing upload
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
          }
          // State that we have finish the process to unlock some actions
          self.isUploadStarted = false;
        }
      };

      dropzone.dropzone($.extend(dropzoneOptions));
    }
  }, {
    key: 'animateStartUpload',
    value: function animateStartUpload() {
      var self = this;
      var dropzone = $('.dropzone');
      // State that we start module upload
      self.isUploadStarted = true;
      $(self.moduleImportStartSelector).hide(0);
      dropzone.css('border', 'none');
      $(self.moduleImportProcessingSelector).fadeIn();
    }
  }, {
    key: 'animateEndUpload',
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
    key: 'displayOnUploadDone',
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
    key: 'displayOnUploadError',
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
    key: 'displayPrestaTrustStep',
    value: function displayPrestaTrustStep(result) {
      var self = this;
      var modal = self.moduleCardController._replacePrestaTrustPlaceholders(result);
      var moduleName = result.module.attributes.name;

      $(this.moduleImportConfirmSelector).html(modal.find('.modal-body').html()).fadeIn();
      $(this.dropZoneModalFooterSelector).html(modal.find('.modal-footer').html()).fadeIn();

      $(this.dropZoneModalFooterSelector).find('.pstrust-install').off('click').on('click', function () {
        $(self.moduleImportConfirmSelector).hide();
        $(self.dropZoneModalFooterSelector).html('');
        self.animateStartUpload();

        // Install ajax call
        $.post(result.module.attributes.urls.install, { 'actionParams[confirmPrestaTrust]': '1' }).done(function (data) {
          self.displayOnUploadDone(data[moduleName]);
        }).fail(function (data) {
          self.displayOnUploadError(data[moduleName]);
        }).always(function () {
          self.isUploadStarted = false;
        });
      });
    }
  }, {
    key: 'getBulkCheckboxesSelector',
    value: function getBulkCheckboxesSelector() {
      return this.currentDisplay === this.DISPLAY_GRID ? this.bulkActionCheckboxGridSelector : this.bulkActionCheckboxListSelector;
    }
  }, {
    key: 'getBulkCheckboxesCheckedSelector',
    value: function getBulkCheckboxesCheckedSelector() {
      return this.currentDisplay === this.DISPLAY_GRID ? this.checkedBulkActionGridSelector : this.checkedBulkActionListSelector;
    }
  }, {
    key: 'getModuleItemSelector',
    value: function getModuleItemSelector() {
      return this.currentDisplay === this.DISPLAY_GRID ? this.moduleItemGridSelector : this.moduleItemListSelector;
    }

    /**
     * Get the module notifications count and displays it as a badge on the notification tab
     * @return void
     */

  }, {
    key: 'getNotificationsCount',
    value: function getNotificationsCount() {
      var self = this;
      $.getJSON(window.moduleURLs.notificationsCount, self.updateNotificationsCount).fail(function () {
        console.error('Could not retrieve module notifications count.');
      });
    }
  }, {
    key: 'updateNotificationsCount',
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
    key: 'initAddonsSearch',
    value: function initAddonsSearch() {
      var self = this;
      $('body').on('click', self.addonItemGridSelector + ', ' + self.addonItemListSelector, function () {
        var searchQuery = '';
        if (self.currentTagsList.length) {
          searchQuery = encodeURIComponent(self.currentTagsList.join(' '));
        }

        window.open(self.baseAddonsUrl + 'search.php?search_query=' + searchQuery, '_blank');
      });
    }
  }, {
    key: 'initCategoriesGrid',
    value: function initCategoriesGrid() {
      var self = this;

      $('body').on('click', this.categoryGridItemSelector, function initilaizeGridBodyClick(event) {
        event.stopPropagation();
        event.preventDefault();
        var refCategory = $(this).data('category-ref');

        // In case we have some tags we need to reset it !
        if (self.currentTagsList.length) {
          self.pstaggerInput.resetTags(false);
          self.currentTagsList = [];
        }
        var menuCategoryToTrigger = $(self.categoryItemSelector + '[data-category-ref="' + refCategory + '"]');

        if (!menuCategoryToTrigger.length) {
          console.warn('No category with ref (' + refCategory + ') seems to exist!');
          return false;
        }

        // Hide current category grid
        if (self.isCategoryGridDisplayed === true) {
          $(self.categoryGridSelector).fadeOut();
          self.isCategoryGridDisplayed = false;
        }

        // Trigger click on right category
        $(self.categoryItemSelector + '[data-category-ref="' + refCategory + '"]').click();
        return true;
      });
    }
  }, {
    key: 'initCurrentDisplay',
    value: function initCurrentDisplay() {
      this.currentDisplay = this.currentDisplay === '' ? this.DISPLAY_LIST : this.DISPLAY_GRID;
    }
  }, {
    key: 'initSortingDropdown',
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
    key: 'doBulkAction',
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
      };

      // Note no grid selector used yet since we do not needed it at dev time
      // Maybe useful to implement this kind of things later if intended to
      // use this functionality elsewhere but "manage my module" section
      if (typeof bulkActionToUrl[requestedBulkAction] === 'undefined') {
        $.growl.error({ message: window.translate_javascripts['Bulk Action - Request not found'].replace('[1]', requestedBulkAction) });
        return false;
      }

      // Loop over all checked bulk checkboxes
      var bulkActionSelectedSelector = this.getBulkCheckboxesCheckedSelector();
      var bulkModuleAction = bulkActionToUrl[requestedBulkAction];

      if ($(bulkActionSelectedSelector).length <= 0) {
        console.warn(window.translate_javascripts['Bulk Action - One module minimum']);
        return false;
      }

      var modulesActions = [];
      var moduleTechName = void 0;
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
    key: 'performModulesAction',
    value: function performModulesAction(modulesActions, bulkModuleAction, forceDeletion) {
      var self = this;
      if (typeof self.moduleCardController === 'undefined') {
        return;
      }

      //First let's filter modules that can't perform this action
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
        });
        //Display a spinner for the last module
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
        modulesRequestedCountdown--;
        //Now that all other modules have performed their action WITHOUT cache clear, we
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
        var actionMenuLink = void 0;
        $.each(modulesActions, function filterAllowedModules(index, moduleData) {
          actionMenuLink = $(self.moduleCardController.moduleActionMenuLinkSelector + bulkModuleAction, moduleData.actionMenuObj);
          if (actionMenuLink.length > 0) {
            actionMenuLinks.push(actionMenuLink);
          } else {
            $.growl.error({ message: window.translate_javascripts['Bulk Action - Request not available for module'].replace('[1]', bulkModuleAction).replace('[2]', moduleData.techName) });
          }
        });

        return actionMenuLinks;
      }
    }
  }, {
    key: 'initActionButtons',
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
      });

      // "Upgrade All" button handler
      $('body').on('click', self.upgradeAllSource, function (event) {
        event.preventDefault();

        if ($(self.upgradeAllTargets).length <= 0) {
          console.warn(window.translate_javascripts['Upgrade All Action - One module minimum']);
          return false;
        }

        var modulesActions = [];
        var moduleTechName = void 0;
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
    key: 'initCategorySelect',
    value: function initCategorySelect() {
      var self = this;
      var body = $('body');
      body.on('click', self.categoryItemSelector, function initializeCategorySelectClick() {
        // Get data from li DOM input
        self.currentRefCategory = $(this).data('category-ref');
        self.currentRefCategory = self.currentRefCategory ? String(self.currentRefCategory).toLowerCase() : null;
        // Change dropdown label to set it to the current category's displayname
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
    key: 'initSearchBlock',
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
    key: 'initSortingDisplaySwitch',
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
    key: 'switchSortingDisplayTo',
    value: function switchSortingDisplayTo(switchTo) {
      if (switchTo !== this.DISPLAY_GRID && switchTo !== this.DISPLAY_LIST) {
        console.error('Can\'t switch to undefined display property "' + switchTo + '"');
        return;
      }

      $('.module-sort-switch').removeClass('module-sort-active');
      $('#module-sort-' + switchTo).addClass('module-sort-active');
      this.currentDisplay = switchTo;
      this.updateModuleVisibility();
    }
  }, {
    key: 'initializeSeeMore',
    value: function initializeSeeMore() {
      var self = this;

      $(self.moduleShortList + ' ' + self.seeMoreSelector).on('click', function seeMore() {
        self.currentCategoryDisplay[$(this).data('category')] = true;
        $(this).addClass('d-none');
        $(this).closest(self.moduleShortList).find(self.seeLessSelector).removeClass('d-none');
        self.updateModuleVisibility();
      });

      $(self.moduleShortList + ' ' + self.seeLessSelector).on('click', function seeMore() {
        self.currentCategoryDisplay[$(this).data('category')] = false;
        $(this).addClass('d-none');
        $(this).closest(self.moduleShortList).find(self.seeMoreSelector).removeClass('d-none');
        self.updateModuleVisibility();
      });
    }
  }, {
    key: 'updateTotalResults',
    value: function updateTotalResults() {
      var replaceFirstWordBy = function replaceFirstWordBy(element, value) {
        var explodedText = element.text().split(' ');
        explodedText[0] = value;
        element.text(explodedText.join(' '));
      };

      // If there are some shortlist: each shortlist count the modules on the next container.
      var $shortLists = $('.module-short-list');
      if ($shortLists.length > 0) {
        $shortLists.each(function shortLists() {
          var $this = $(this);
          replaceFirstWordBy($this.find('.module-search-result-wording'), $this.next('.modules-list').find('.module-item').length);
        });

        // If there is no shortlist: the wording directly update from the only module container.
      } else {
        var modulesCount = $('.modules-list').find('.module-item').length;
        replaceFirstWordBy($('.module-search-result-wording'), modulesCount);

        var selectorToToggle = self.currentDisplay === self.DISPLAY_LIST ? this.addonItemListSelector : this.addonItemGridSelector;
        $(selectorToToggle).toggle(modulesCount !== this.modulesList.length / 2);

        if (modulesCount === 0) {
          $('.module-addons-search-link').attr('href', this.baseAddonsUrl + 'search.php?search_query=' + encodeURIComponent(this.currentTagsList.join(' ')));
        }
      }
    }
  }]);

  return AdminModuleController;
}();

exports.default = AdminModuleController;

/***/ }),

/***/ 275:
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
 * Module Admin Page Loader.
 * @constructor
 */

var ModuleLoader = function () {
  function ModuleLoader() {
    _classCallCheck(this, ModuleLoader);

    ModuleLoader.handleImport();
    ModuleLoader.handleEvents();
  }

  _createClass(ModuleLoader, null, [{
    key: 'handleImport',
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
    key: 'handleEvents',
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

exports.default = ModuleLoader;

/***/ }),

/***/ 365:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _moduleCard = __webpack_require__(65);

var _moduleCard2 = _interopRequireDefault(_moduleCard);

var _controller = __webpack_require__(274);

var _controller2 = _interopRequireDefault(_controller);

var _loader = __webpack_require__(275);

var _loader2 = _interopRequireDefault(_loader);

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
  var moduleCardController = new _moduleCard2.default();
  new _loader2.default();
  new _controller2.default(moduleCardController);
});

/***/ }),

/***/ 65:
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
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(12)))

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjA/ODU5MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vZXh0ZXJuYWwgXCJqUXVlcnlcIj8wY2I4KioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9tb2R1bGUvY29udHJvbGxlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9tb2R1bGUvbG9hZGVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL21vZHVsZS9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL21vZHVsZS1jYXJkLmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJBZG1pbk1vZHVsZUNvbnRyb2xsZXIiLCJtb2R1bGVDYXJkQ29udHJvbGxlciIsIkRFRkFVTFRfTUFYX1JFQ0VOVExZX1VTRUQiLCJERUZBVUxUX01BWF9QRVJfQ0FURUdPUklFUyIsIkRJU1BMQVlfR1JJRCIsIkRJU1BMQVlfTElTVCIsIkNBVEVHT1JZX1JFQ0VOVExZX1VTRUQiLCJjdXJyZW50Q2F0ZWdvcnlEaXNwbGF5IiwiY3VycmVudERpc3BsYXkiLCJpc0NhdGVnb3J5R3JpZERpc3BsYXllZCIsImN1cnJlbnRUYWdzTGlzdCIsImN1cnJlbnRSZWZDYXRlZ29yeSIsImN1cnJlbnRSZWZTdGF0dXMiLCJjdXJyZW50U29ydGluZyIsImJhc2VBZGRvbnNVcmwiLCJwc3RhZ2dlcklucHV0IiwibGFzdEJ1bGtBY3Rpb24iLCJpc1VwbG9hZFN0YXJ0ZWQiLCJyZWNlbnRseVVzZWRTZWxlY3RvciIsIm1vZHVsZXNMaXN0IiwiYWRkb25zQ2FyZEdyaWQiLCJhZGRvbnNDYXJkTGlzdCIsIm1vZHVsZVNob3J0TGlzdCIsInNlZU1vcmVTZWxlY3RvciIsInNlZUxlc3NTZWxlY3RvciIsIm1vZHVsZUl0ZW1HcmlkU2VsZWN0b3IiLCJtb2R1bGVJdGVtTGlzdFNlbGVjdG9yIiwiY2F0ZWdvcnlTZWxlY3RvckxhYmVsU2VsZWN0b3IiLCJjYXRlZ29yeVNlbGVjdG9yIiwiY2F0ZWdvcnlJdGVtU2VsZWN0b3IiLCJhZGRvbnNMb2dpbkJ1dHRvblNlbGVjdG9yIiwiY2F0ZWdvcnlSZXNldEJ0blNlbGVjdG9yIiwibW9kdWxlSW5zdGFsbEJ0blNlbGVjdG9yIiwibW9kdWxlU29ydGluZ0Ryb3Bkb3duU2VsZWN0b3IiLCJjYXRlZ29yeUdyaWRTZWxlY3RvciIsImNhdGVnb3J5R3JpZEl0ZW1TZWxlY3RvciIsImFkZG9uSXRlbUdyaWRTZWxlY3RvciIsImFkZG9uSXRlbUxpc3RTZWxlY3RvciIsInVwZ3JhZGVBbGxTb3VyY2UiLCJ1cGdyYWRlQWxsVGFyZ2V0cyIsImJ1bGtBY3Rpb25Ecm9wRG93blNlbGVjdG9yIiwiYnVsa0l0ZW1TZWxlY3RvciIsImJ1bGtBY3Rpb25DaGVja2JveExpc3RTZWxlY3RvciIsImJ1bGtBY3Rpb25DaGVja2JveEdyaWRTZWxlY3RvciIsImNoZWNrZWRCdWxrQWN0aW9uTGlzdFNlbGVjdG9yIiwiY2hlY2tlZEJ1bGtBY3Rpb25HcmlkU2VsZWN0b3IiLCJidWxrQWN0aW9uQ2hlY2tib3hTZWxlY3RvciIsImJ1bGtDb25maXJtTW9kYWxTZWxlY3RvciIsImJ1bGtDb25maXJtTW9kYWxBY3Rpb25OYW1lU2VsZWN0b3IiLCJidWxrQ29uZmlybU1vZGFsTGlzdFNlbGVjdG9yIiwiYnVsa0NvbmZpcm1Nb2RhbEFja0J0blNlbGVjdG9yIiwicGxhY2Vob2xkZXJHbG9iYWxTZWxlY3RvciIsInBsYWNlaG9sZGVyRmFpbHVyZUdsb2JhbFNlbGVjdG9yIiwicGxhY2Vob2xkZXJGYWlsdXJlTXNnU2VsZWN0b3IiLCJwbGFjZWhvbGRlckZhaWx1cmVSZXRyeUJ0blNlbGVjdG9yIiwic3RhdHVzU2VsZWN0b3JMYWJlbFNlbGVjdG9yIiwic3RhdHVzSXRlbVNlbGVjdG9yIiwic3RhdHVzUmVzZXRCdG5TZWxlY3RvciIsImFkZG9uc0Nvbm5lY3RNb2RhbEJ0blNlbGVjdG9yIiwiYWRkb25zTG9nb3V0TW9kYWxCdG5TZWxlY3RvciIsImFkZG9uc0ltcG9ydE1vZGFsQnRuU2VsZWN0b3IiLCJkcm9wWm9uZU1vZGFsU2VsZWN0b3IiLCJkcm9wWm9uZU1vZGFsRm9vdGVyU2VsZWN0b3IiLCJkcm9wWm9uZUltcG9ydFpvbmVTZWxlY3RvciIsImFkZG9uc0Nvbm5lY3RNb2RhbFNlbGVjdG9yIiwiYWRkb25zTG9nb3V0TW9kYWxTZWxlY3RvciIsImFkZG9uc0Nvbm5lY3RGb3JtIiwibW9kdWxlSW1wb3J0TW9kYWxDbG9zZUJ0biIsIm1vZHVsZUltcG9ydFN0YXJ0U2VsZWN0b3IiLCJtb2R1bGVJbXBvcnRQcm9jZXNzaW5nU2VsZWN0b3IiLCJtb2R1bGVJbXBvcnRTdWNjZXNzU2VsZWN0b3IiLCJtb2R1bGVJbXBvcnRTdWNjZXNzQ29uZmlndXJlQnRuU2VsZWN0b3IiLCJtb2R1bGVJbXBvcnRGYWlsdXJlU2VsZWN0b3IiLCJtb2R1bGVJbXBvcnRGYWlsdXJlUmV0cnlTZWxlY3RvciIsIm1vZHVsZUltcG9ydEZhaWx1cmVEZXRhaWxzQnRuU2VsZWN0b3IiLCJtb2R1bGVJbXBvcnRTZWxlY3RGaWxlTWFudWFsU2VsZWN0b3IiLCJtb2R1bGVJbXBvcnRGYWlsdXJlTXNnRGV0YWlsc1NlbGVjdG9yIiwibW9kdWxlSW1wb3J0Q29uZmlybVNlbGVjdG9yIiwiaW5pdFNvcnRpbmdEcm9wZG93biIsImluaXRCT0V2ZW50UmVnaXN0ZXJpbmciLCJpbml0Q3VycmVudERpc3BsYXkiLCJpbml0U29ydGluZ0Rpc3BsYXlTd2l0Y2giLCJpbml0QnVsa0Ryb3Bkb3duIiwiaW5pdFNlYXJjaEJsb2NrIiwiaW5pdENhdGVnb3J5U2VsZWN0IiwiaW5pdENhdGVnb3JpZXNHcmlkIiwiaW5pdEFjdGlvbkJ1dHRvbnMiLCJpbml0QWRkb25zU2VhcmNoIiwiaW5pdEFkZG9uc0Nvbm5lY3QiLCJpbml0QWRkTW9kdWxlQWN0aW9uIiwiaW5pdERyb3B6b25lIiwiaW5pdFBhZ2VDaGFuZ2VQcm90ZWN0aW9uIiwiaW5pdFBsYWNlaG9sZGVyTWVjaGFuaXNtIiwiaW5pdEZpbHRlclN0YXR1c0Ryb3Bkb3duIiwiZmV0Y2hNb2R1bGVzTGlzdCIsImdldE5vdGlmaWNhdGlvbnNDb3VudCIsImluaXRpYWxpemVTZWVNb3JlIiwic2VsZiIsImJvZHkiLCJvbiIsInBhcnNlSW50IiwiZGF0YSIsInRleHQiLCJmaW5kIiwic2hvdyIsInVwZGF0ZU1vZHVsZVZpc2liaWxpdHkiLCJoaWRlIiwiZ2V0QnVsa0NoZWNrYm94ZXNTZWxlY3RvciIsInNlbGVjdG9yIiwiZ2V0QnVsa0NoZWNrYm94ZXNDaGVja2VkU2VsZWN0b3IiLCJsZW5ndGgiLCJjbG9zZXN0IiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsImluaXRpYWxpemVCb2R5Q2hhbmdlIiwiZ3Jvd2wiLCJ3YXJuaW5nIiwibWVzc2FnZSIsInRyYW5zbGF0ZV9qYXZhc2NyaXB0cyIsIm1vZHVsZXNMaXN0U3RyaW5nIiwiYnVpbGRCdWxrQWN0aW9uTW9kdWxlTGlzdCIsImFjdGlvblN0cmluZyIsInRvTG93ZXJDYXNlIiwiaHRtbCIsIm1vZGFsIiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsInN0b3BQcm9wYWdhdGlvbiIsImRvQnVsa0FjdGlvbiIsIkJPRXZlbnQiLCJvbk1vZHVsZURpc2FibGVkIiwidXBkYXRlVG90YWxSZXN1bHRzIiwibW9kdWxlSXRlbVNlbGVjdG9yIiwiZ2V0TW9kdWxlSXRlbVNlbGVjdG9yIiwiZWFjaCIsInNjYW5Nb2R1bGVzTGlzdCIsImFqYXhMb2FkUGFnZSIsImZhZGVPdXQiLCJmYWRlSW4iLCJhamF4IiwibWV0aG9kIiwidXJsIiwibW9kdWxlVVJMcyIsImNhdGFsb2dSZWZyZXNoIiwiZG9uZSIsInJlc3BvbnNlIiwic3RhdHVzIiwiZG9tRWxlbWVudHMiLCJtc2ciLCJzdHlsZXNoZWV0IiwiZG9jdW1lbnQiLCJzdHlsZVNoZWV0cyIsInN0eWxlc2hlZXRSdWxlIiwibW9kdWxlR2xvYmFsU2VsZWN0b3IiLCJtb2R1bGVTb3J0aW5nU2VsZWN0b3IiLCJyZXF1aXJlZFNlbGVjdG9yQ29tYmluYXRpb24iLCJpbnNlcnRSdWxlIiwiY3NzUnVsZXMiLCJhZGRSdWxlIiwiaW5kZXgiLCJlbGVtZW50IiwiYXBwZW5kIiwiY29udGVudCIsImNzcyIsInBvcG92ZXIiLCJmYWlsIiwic3RhdHVzVGV4dCIsImNvbnRhaW5lciIsIiR0aGlzIiwicHJlcGFyZUNvbnRhaW5lciIsInByZXBhcmVNb2R1bGVzIiwicHVzaCIsImRvbU9iamVjdCIsImlkIiwibmFtZSIsInNjb3JpbmciLCJwYXJzZUZsb2F0IiwibG9nbyIsImF1dGhvciIsInZlcnNpb24iLCJkZXNjcmlwdGlvbiIsInRlY2hOYW1lIiwiY2hpbGRDYXRlZ29yaWVzIiwiY2F0ZWdvcmllcyIsIlN0cmluZyIsInR5cGUiLCJwcmljZSIsImFjdGl2ZSIsImFjY2VzcyIsImRpc3BsYXkiLCJoYXNDbGFzcyIsInJlbW92ZSIsInRyaWdnZXIiLCJvcmRlciIsImtleSIsInNwbGl0dGVkS2V5Iiwic3BsaXQiLCJjdXJyZW50Q29tcGFyZSIsImEiLCJiIiwiYURhdGEiLCJiRGF0YSIsIkRhdGUiLCJnZXRUaW1lIiwiaXNOYU4iLCJsb2NhbGVDb21wYXJlIiwic29ydCIsInJldmVyc2UiLCJzZXRTaG9ydExpc3RWaXNpYmlsaXR5IiwibmJNb2R1bGVzSW5Db250YWluZXIiLCJ1cGRhdGVNb2R1bGVTb3J0aW5nIiwiaXNWaXNpYmxlIiwiY3VycmVudE1vZHVsZSIsIm1vZHVsZUNhdGVnb3J5IiwidGFnRXhpc3RzIiwibmV3VmFsdWUiLCJtb2R1bGVzTGlzdExlbmd0aCIsImNvdW50ZXIiLCJpIiwidmFsdWUiLCJpbmRleE9mIiwidW5kZWZpbmVkIiwidXBkYXRlTW9kdWxlQ29udGFpbmVyRGlzcGxheSIsImNoZWNrQm94ZXNTZWxlY3RvciIsImFscmVhZHlEb25lRmxhZyIsImh0bWxHZW5lcmF0ZWQiLCJjdXJyZW50RWxlbWVudCIsInByZXBhcmVDaGVja2JveGVzIiwiYXR0ciIsImluaXRpYWxpemVCb2R5U3VibWl0IiwiZGF0YVR5cGUiLCJzZXJpYWxpemUiLCJiZWZvcmVTZW5kIiwic3VjY2VzcyIsImxvY2F0aW9uIiwicmVsb2FkIiwiZXJyb3IiLCJhZGRNb2R1bGVCdXR0b24iLCJkcm9wem9uZSIsInNldFRpbWVvdXQiLCJyZW1vdmVBdHRyIiwibWFudWFsU2VsZWN0IiwiaW5pdGlhbGl6ZUJvZHlDbGlja09uTW9kdWxlSW1wb3J0Iiwic2xpZGVEb3duIiwiZHJvcHpvbmVPcHRpb25zIiwibW9kdWxlSW1wb3J0IiwiYWNjZXB0ZWRGaWxlcyIsInBhcmFtTmFtZSIsIm1heEZpbGVzaXplIiwidXBsb2FkTXVsdGlwbGUiLCJhZGRSZW1vdmVMaW5rcyIsImRpY3REZWZhdWx0TWVzc2FnZSIsImhpZGRlbklucHV0Q29udGFpbmVyIiwidGltZW91dCIsImFkZGVkZmlsZSIsImFuaW1hdGVTdGFydFVwbG9hZCIsInByb2Nlc3NpbmciLCJmaWxlIiwiZGlzcGxheU9uVXBsb2FkRXJyb3IiLCJjb21wbGV0ZSIsInJlc3BvbnNlT2JqZWN0IiwicGFyc2VKU09OIiwieGhyIiwiaXNfY29uZmlndXJhYmxlIiwibW9kdWxlX25hbWUiLCJkaXNwbGF5T25VcGxvYWREb25lIiwiZXh0ZW5kIiwiY2FsbGJhY2siLCJmaW5pc2giLCJyZXN1bHQiLCJhbmltYXRlRW5kVXBsb2FkIiwiY29uZmlndXJlTGluayIsImNvbmZpZ3VyYXRpb25QYWdlIiwicmVwbGFjZSIsImNvbmZpcm1hdGlvbl9zdWJqZWN0IiwiZGlzcGxheVByZXN0YVRydXN0U3RlcCIsIl9yZXBsYWNlUHJlc3RhVHJ1c3RQbGFjZWhvbGRlcnMiLCJtb2R1bGVOYW1lIiwibW9kdWxlIiwiYXR0cmlidXRlcyIsIm9mZiIsInBvc3QiLCJ1cmxzIiwiaW5zdGFsbCIsImFsd2F5cyIsImdldEpTT04iLCJub3RpZmljYXRpb25zQ291bnQiLCJ1cGRhdGVOb3RpZmljYXRpb25zQ291bnQiLCJjb25zb2xlIiwiYmFkZ2UiLCJkZXN0aW5hdGlvblRhYnMiLCJ0b19jb25maWd1cmUiLCJ0b191cGRhdGUiLCJzZWFyY2hRdWVyeSIsImVuY29kZVVSSUNvbXBvbmVudCIsImpvaW4iLCJvcGVuIiwiaW5pdGlsYWl6ZUdyaWRCb2R5Q2xpY2siLCJyZWZDYXRlZ29yeSIsInJlc2V0VGFncyIsIm1lbnVDYXRlZ29yeVRvVHJpZ2dlciIsIndhcm4iLCJjbGljayIsImluaXRpYWxpemVCb2R5U29ydGluZ0NoYW5nZSIsInJlcXVlc3RlZEJ1bGtBY3Rpb24iLCJmb3JjZURlbGV0aW9uIiwicHJvcCIsImJ1bGtBY3Rpb25Ub1VybCIsImJ1bGtBY3Rpb25TZWxlY3RlZFNlbGVjdG9yIiwiYnVsa01vZHVsZUFjdGlvbiIsIm1vZHVsZXNBY3Rpb25zIiwibW9kdWxlVGVjaE5hbWUiLCJidWxrQWN0aW9uU2VsZWN0b3IiLCJhY3Rpb25NZW51T2JqIiwibmV4dCIsInBlcmZvcm1Nb2R1bGVzQWN0aW9uIiwiYWN0aW9uTWVudUxpbmtzIiwiZmlsdGVyQWxsb3dlZEFjdGlvbnMiLCJtb2R1bGVzUmVxdWVzdGVkQ291bnRkb3duIiwic3Bpbm5lck9iaiIsImJ1bGtNb2R1bGVzTG9vcCIsImFjdGlvbk1lbnVMaW5rIiwicmVxdWVzdE1vZHVsZUFjdGlvbiIsImNvdW50ZG93bk1vZHVsZXNSZXF1ZXN0IiwibGFzdE1lbnVMaW5rIiwibW9kdWxlSXRlbUFjdGlvbnNTZWxlY3RvciIsImFmdGVyIiwiZGlzYWJsZUNhY2hlQ2xlYXIiLCJyZXF1ZXN0RW5kQ2FsbGJhY2siLCJfcmVxdWVzdFRvQ29udHJvbGxlciIsImZpbHRlckFsbG93ZWRNb2R1bGVzIiwibW9kdWxlRGF0YSIsIm1vZHVsZUFjdGlvbk1lbnVMaW5rU2VsZWN0b3IiLCJpbml0aWFsaXplQWN0aW9uQnV0dG9uc0NsaWNrIiwiJG5leHQiLCJtb2R1bGVJdGVtTGlzdCIsImluaXRpYWxpemVDYXRlZ29yeVNlbGVjdENsaWNrIiwiaW5pdGlhbGl6ZUNhdGVnb3J5UmVzZXRCdXR0b25DbGljayIsInJhd1RleHQiLCJ1cHBlckZpcnN0TGV0dGVyIiwiY2hhckF0IiwidG9VcHBlckNhc2UiLCJyZW1vdmVkRmlyc3RMZXR0ZXIiLCJzbGljZSIsIm9yaWdpbmFsVGV4dCIsInBzdGFnZ2VyIiwib25UYWdzQ2hhbmdlZCIsInRhZ0xpc3QiLCJvblJlc2V0VGFncyIsImlucHV0UGxhY2Vob2xkZXIiLCJjbG9zaW5nQ3Jvc3MiLCJjb250ZXh0Iiwic3dpdGNoU29ydCIsInN3aXRjaFRvIiwiaXNBbHJlYWR5RGlzcGxheWVkIiwic3dpdGNoU29ydGluZ0Rpc3BsYXlUbyIsInNlZU1vcmUiLCJyZXBsYWNlRmlyc3RXb3JkQnkiLCJleHBsb2RlZFRleHQiLCIkc2hvcnRMaXN0cyIsInNob3J0TGlzdHMiLCJtb2R1bGVzQ291bnQiLCJzZWxlY3RvclRvVG9nZ2xlIiwidG9nZ2xlIiwiTW9kdWxlTG9hZGVyIiwiaGFuZGxlSW1wb3J0IiwiaGFuZGxlRXZlbnRzIiwidmFsaWRhdGUiLCJtb2R1bGVQb3BwaW4iLCJ0YXJnZXQiLCJnZXQiLCJocmVmIiwiTW9kdWxlQ2FyZCIsImV2ZW50TmFtZSIsImFkZEV2ZW50TGlzdGVuZXIiLCJjYWxsIiwiZW1pdEV2ZW50IiwiZXZlbnRUeXBlIiwiX2V2ZW50IiwiY3JlYXRlRXZlbnQiLCJpbml0RXZlbnQiLCJkaXNwYXRjaEV2ZW50IiwibW9kdWxlQWN0aW9uTWVudUluc3RhbGxMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25NZW51RW5hYmxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudVVuaW5zdGFsbExpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudUVuYWJsZU1vYmlsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTW9iaWxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudVJlc2V0TGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudVVwZGF0ZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1vZGFsRGlzYWJsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1vZGFsUmVzZXRMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25Nb2RhbFVuaW5zdGFsbExpbmtTZWxlY3RvciIsImZvcmNlRGVsZXRpb25PcHRpb24iLCJidG4iLCJfZGlzcGF0Y2hQcmVFdmVudCIsIl9jb25maXJtQWN0aW9uIiwiZSIsInBhcmVudHMiLCJiaW5kIiwiYWN0aW9uIiwiZmlyc3QiLCJ0aGF0IiwiaW5zdGFsbF9idXR0b24iLCJmb3JtIiwicGFyZW50IiwiYXBwZW5kVG8iLCJhbGVydENsYXNzIiwicHJlc3RhdHJ1c3QiLCJjaGVja19saXN0IiwicHJvcGVydHkiLCJzcmMiLCJpbWciLCJhbHQiLCJkaXNwbGF5TmFtZSIsImpRdWVyeSIsIkV2ZW50IiwiaXNQcm9wYWdhdGlvblN0b3BwZWQiLCJpc0ltbWVkaWF0ZVByb3BhZ2F0aW9uU3RvcHBlZCIsImpxRWxlbWVudE9iaiIsImhvc3QiLCJhY3Rpb25QYXJhbXMiLCJzZXJpYWxpemVBcnJheSIsIk9iamVjdCIsImtleXMiLCJfY29uZmlybVByZXN0YVRydXN0Iiwibm90aWNlIiwiYWx0ZXJlZFNlbGVjdG9yIiwiX2dldE1vZHVsZUl0ZW1TZWxlY3RvciIsIm1haW5FbGVtZW50IiwicmVwbGFjZVdpdGgiLCJhY3Rpb25fbWVudV9odG1sIiwibW9kdWxlSXRlbSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hE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QUFaLENBQXdCMlQsa0JBQWtCLGNBQTFDO0FBQ0FFLDBCQUFZek0sSUFBWixDQUFpQixhQUFqQixFQUFnQyxHQUFoQzs7QUFFQW5HLHNCQUFRdVAsU0FBUixDQUFrQixnQkFBbEIsRUFBb0MsYUFBcEM7QUFDRDs7QUFFRDJDLHlCQUFhVyxXQUFiLENBQXlCakssT0FBTzBDLGNBQVAsRUFBdUJ3SCxnQkFBaEQ7QUFDRDtBQUNGO0FBQ0YsT0FqREQsRUFpREcxUSxJQWpESCxDQWlEUSxZQUFXO0FBQ2pCLFlBQU0yUSxhQUFhYixhQUFhcFQsT0FBYixDQUFxQixrQkFBckIsQ0FBbkI7QUFDQSxZQUFNc0UsV0FBVzJQLFdBQVczVSxJQUFYLENBQWdCLFVBQWhCLENBQWpCO0FBQ0F2RyxVQUFFcUgsS0FBRixDQUFReUgsS0FBUixDQUFjLEVBQUN2SCxTQUFTLDhCQUE0QjJSLE1BQTVCLEdBQW1DLGNBQW5DLEdBQWtEM04sUUFBNUQsRUFBZDtBQUNELE9BckRELEVBcURHdUcsTUFyREgsQ0FxRFUsWUFBWTtBQUNwQnVJLHFCQUFhelIsTUFBYjtBQUNBcUwsbUJBQVdoSSxNQUFYO0FBQ0EsWUFBSTRFLFFBQUosRUFBYztBQUNaQTtBQUNEO0FBQ0YsT0EzREQ7O0FBNkRBLGFBQU8sS0FBUDtBQUNEOzs7Ozs7a0JBeFBrQnlHLFUiLCJmaWxlIjoibW9kdWxlLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMzY1KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCAxZTY2MjYzOTAwZTk2NmRmYmJmMCIsIihmdW5jdGlvbigpIHsgbW9kdWxlLmV4cG9ydHMgPSB3aW5kb3dbXCJqUXVlcnlcIl07IH0oKSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gZXh0ZXJuYWwgXCJqUXVlcnlcIlxuLy8gbW9kdWxlIGlkID0gMTJcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNiAxNSAyMCAzMSAzMiAzOSA0MCA0NSIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBNb2R1bGUgQWRtaW4gUGFnZSBDb250cm9sbGVyLlxuICogQGNvbnN0cnVjdG9yXG4gKi9cbmNsYXNzIEFkbWluTW9kdWxlQ29udHJvbGxlciB7XG4gIC8qKlxuICAgKiBJbml0aWFsaXplIGFsbCBsaXN0ZW5lcnMgYW5kIGJpbmQgZXZlcnl0aGluZ1xuICAgKiBAbWV0aG9kIGluaXRcbiAgICogQG1lbWJlcm9mIEFkbWluTW9kdWxlXG4gICAqL1xuICBjb25zdHJ1Y3Rvcihtb2R1bGVDYXJkQ29udHJvbGxlcikge1xuICAgIHRoaXMubW9kdWxlQ2FyZENvbnRyb2xsZXIgPSBtb2R1bGVDYXJkQ29udHJvbGxlcjtcblxuICAgIHRoaXMuREVGQVVMVF9NQVhfUkVDRU5UTFlfVVNFRCA9IDEwO1xuICAgIHRoaXMuREVGQVVMVF9NQVhfUEVSX0NBVEVHT1JJRVMgPSA2O1xuICAgIHRoaXMuRElTUExBWV9HUklEID0gJ2dyaWQnO1xuICAgIHRoaXMuRElTUExBWV9MSVNUID0gJ2xpc3QnO1xuICAgIHRoaXMuQ0FURUdPUllfUkVDRU5UTFlfVVNFRCA9ICdyZWNlbnRseS11c2VkJztcblxuICAgIHRoaXMuY3VycmVudENhdGVnb3J5RGlzcGxheSA9IHt9O1xuICAgIHRoaXMuY3VycmVudERpc3BsYXkgPSAnJztcbiAgICB0aGlzLmlzQ2F0ZWdvcnlHcmlkRGlzcGxheWVkID0gZmFsc2U7XG4gICAgdGhpcy5jdXJyZW50VGFnc0xpc3QgPSBbXTtcbiAgICB0aGlzLmN1cnJlbnRSZWZDYXRlZ29yeSA9IG51bGw7XG4gICAgdGhpcy5jdXJyZW50UmVmU3RhdHVzID0gbnVsbDtcbiAgICB0aGlzLmN1cnJlbnRTb3J0aW5nID0gbnVsbDtcbiAgICB0aGlzLmJhc2VBZGRvbnNVcmwgPSAnaHR0cHM6Ly9hZGRvbnMucHJlc3Rhc2hvcC5jb20vJztcbiAgICB0aGlzLnBzdGFnZ2VySW5wdXQgPSBudWxsO1xuICAgIHRoaXMubGFzdEJ1bGtBY3Rpb24gPSBudWxsO1xuICAgIHRoaXMuaXNVcGxvYWRTdGFydGVkID0gZmFsc2U7XG5cbiAgICB0aGlzLnJlY2VudGx5VXNlZFNlbGVjdG9yID0gJyNtb2R1bGUtcmVjZW50bHktdXNlZC1saXN0IC5tb2R1bGVzLWxpc3QnO1xuXG4gICAgLyoqXG4gICAgICogTG9hZGVkIG1vZHVsZXMgbGlzdC5cbiAgICAgKiBDb250YWluaW5nIHRoZSBjYXJkIGFuZCBsaXN0IGRpc3BsYXkuXG4gICAgICogQHR5cGUge0FycmF5fVxuICAgICAqL1xuICAgIHRoaXMubW9kdWxlc0xpc3QgPSBbXTtcbiAgICB0aGlzLmFkZG9uc0NhcmRHcmlkID0gbnVsbDtcbiAgICB0aGlzLmFkZG9uc0NhcmRMaXN0ID0gbnVsbDtcblxuICAgIHRoaXMubW9kdWxlU2hvcnRMaXN0ID0gJy5tb2R1bGUtc2hvcnQtbGlzdCc7XG4gICAgLy8gU2VlIG1vcmUgJiBTZWUgbGVzcyBzZWxlY3RvclxuICAgIHRoaXMuc2VlTW9yZVNlbGVjdG9yID0gJy5zZWUtbW9yZSc7XG4gICAgdGhpcy5zZWVMZXNzU2VsZWN0b3IgPSAnLnNlZS1sZXNzJztcblxuICAgIC8vIFNlbGVjdG9ycyBpbnRvIHZhcnMgdG8gbWFrZSBpdCBlYXNpZXIgdG8gY2hhbmdlIHRoZW0gd2hpbGUga2VlcGluZyBzYW1lIGNvZGUgbG9naWNcbiAgICB0aGlzLm1vZHVsZUl0ZW1HcmlkU2VsZWN0b3IgPSAnLm1vZHVsZS1pdGVtLWdyaWQnO1xuICAgIHRoaXMubW9kdWxlSXRlbUxpc3RTZWxlY3RvciA9ICcubW9kdWxlLWl0ZW0tbGlzdCc7XG4gICAgdGhpcy5jYXRlZ29yeVNlbGVjdG9yTGFiZWxTZWxlY3RvciA9ICcubW9kdWxlLWNhdGVnb3J5LXNlbGVjdG9yLWxhYmVsJztcbiAgICB0aGlzLmNhdGVnb3J5U2VsZWN0b3IgPSAnLm1vZHVsZS1jYXRlZ29yeS1zZWxlY3Rvcic7XG4gICAgdGhpcy5jYXRlZ29yeUl0ZW1TZWxlY3RvciA9ICcubW9kdWxlLWNhdGVnb3J5LW1lbnUnO1xuICAgIHRoaXMuYWRkb25zTG9naW5CdXR0b25TZWxlY3RvciA9ICcjYWRkb25zX2xvZ2luX2J0bic7XG4gICAgdGhpcy5jYXRlZ29yeVJlc2V0QnRuU2VsZWN0b3IgPSAnLm1vZHVsZS1jYXRlZ29yeS1yZXNldCc7XG4gICAgdGhpcy5tb2R1bGVJbnN0YWxsQnRuU2VsZWN0b3IgPSAnaW5wdXQubW9kdWxlLWluc3RhbGwtYnRuJztcbiAgICB0aGlzLm1vZHVsZVNvcnRpbmdEcm9wZG93blNlbGVjdG9yID0gJy5tb2R1bGUtc29ydGluZy1hdXRob3Igc2VsZWN0JztcbiAgICB0aGlzLmNhdGVnb3J5R3JpZFNlbGVjdG9yID0gJyNtb2R1bGVzLWNhdGVnb3JpZXMtZ3JpZCc7XG4gICAgdGhpcy5jYXRlZ29yeUdyaWRJdGVtU2VsZWN0b3IgPSAnLm1vZHVsZS1jYXRlZ29yeS1pdGVtJztcbiAgICB0aGlzLmFkZG9uSXRlbUdyaWRTZWxlY3RvciA9ICcubW9kdWxlLWFkZG9ucy1pdGVtLWdyaWQnO1xuICAgIHRoaXMuYWRkb25JdGVtTGlzdFNlbGVjdG9yID0gJy5tb2R1bGUtYWRkb25zLWl0ZW0tbGlzdCc7XG5cbiAgICAvLyBVcGdyYWRlIEFsbCBzZWxlY3RvcnNcbiAgICB0aGlzLnVwZ3JhZGVBbGxTb3VyY2UgPSAnLm1vZHVsZV9hY3Rpb25fbWVudV91cGdyYWRlX2FsbCc7XG4gICAgdGhpcy51cGdyYWRlQWxsVGFyZ2V0cyA9ICcjbW9kdWxlcy1saXN0LWNvbnRhaW5lci11cGRhdGUgLm1vZHVsZV9hY3Rpb25fbWVudV91cGdyYWRlOnZpc2libGUnO1xuXG4gICAgLy8gQnVsayBhY3Rpb24gc2VsZWN0b3JzXG4gICAgdGhpcy5idWxrQWN0aW9uRHJvcERvd25TZWxlY3RvciA9ICcubW9kdWxlLWJ1bGstYWN0aW9ucyc7XG4gICAgdGhpcy5idWxrSXRlbVNlbGVjdG9yID0gJy5tb2R1bGUtYnVsay1tZW51JztcbiAgICB0aGlzLmJ1bGtBY3Rpb25DaGVja2JveExpc3RTZWxlY3RvciA9ICcubW9kdWxlLWNoZWNrYm94LWJ1bGstbGlzdCBpbnB1dCc7XG4gICAgdGhpcy5idWxrQWN0aW9uQ2hlY2tib3hHcmlkU2VsZWN0b3IgPSAnLm1vZHVsZS1jaGVja2JveC1idWxrLWdyaWQgaW5wdXQnO1xuICAgIHRoaXMuY2hlY2tlZEJ1bGtBY3Rpb25MaXN0U2VsZWN0b3IgPSBgJHt0aGlzLmJ1bGtBY3Rpb25DaGVja2JveExpc3RTZWxlY3Rvcn06Y2hlY2tlZGA7XG4gICAgdGhpcy5jaGVja2VkQnVsa0FjdGlvbkdyaWRTZWxlY3RvciA9IGAke3RoaXMuYnVsa0FjdGlvbkNoZWNrYm94R3JpZFNlbGVjdG9yfTpjaGVja2VkYDtcbiAgICB0aGlzLmJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdG9yID0gJyNtb2R1bGUtbW9kYWwtYnVsay1jaGVja2JveCc7XG4gICAgdGhpcy5idWxrQ29uZmlybU1vZGFsU2VsZWN0b3IgPSAnI21vZHVsZS1tb2RhbC1idWxrLWNvbmZpcm0nO1xuICAgIHRoaXMuYnVsa0NvbmZpcm1Nb2RhbEFjdGlvbk5hbWVTZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWJ1bGstY29uZmlybS1hY3Rpb24tbmFtZSc7XG4gICAgdGhpcy5idWxrQ29uZmlybU1vZGFsTGlzdFNlbGVjdG9yID0gJyNtb2R1bGUtbW9kYWwtYnVsay1jb25maXJtLWxpc3QnO1xuICAgIHRoaXMuYnVsa0NvbmZpcm1Nb2RhbEFja0J0blNlbGVjdG9yID0gJyNtb2R1bGUtbW9kYWwtY29uZmlybS1idWxrLWFjayc7XG5cbiAgICAvLyBQbGFjZWhvbGRlcnNcbiAgICB0aGlzLnBsYWNlaG9sZGVyR2xvYmFsU2VsZWN0b3IgPSAnLm1vZHVsZS1wbGFjZWhvbGRlcnMtd3JhcHBlcic7XG4gICAgdGhpcy5wbGFjZWhvbGRlckZhaWx1cmVHbG9iYWxTZWxlY3RvciA9ICcubW9kdWxlLXBsYWNlaG9sZGVycy1mYWlsdXJlJztcbiAgICB0aGlzLnBsYWNlaG9sZGVyRmFpbHVyZU1zZ1NlbGVjdG9yID0gJy5tb2R1bGUtcGxhY2Vob2xkZXJzLWZhaWx1cmUtbXNnJztcbiAgICB0aGlzLnBsYWNlaG9sZGVyRmFpbHVyZVJldHJ5QnRuU2VsZWN0b3IgPSAnI21vZHVsZS1wbGFjZWhvbGRlcnMtZmFpbHVyZS1yZXRyeSc7XG5cbiAgICAvLyBNb2R1bGUncyBzdGF0dXNlcyBzZWxlY3RvcnNcbiAgICB0aGlzLnN0YXR1c1NlbGVjdG9yTGFiZWxTZWxlY3RvciA9ICcubW9kdWxlLXN0YXR1cy1zZWxlY3Rvci1sYWJlbCc7XG4gICAgdGhpcy5zdGF0dXNJdGVtU2VsZWN0b3IgPSAnLm1vZHVsZS1zdGF0dXMtbWVudSc7XG4gICAgdGhpcy5zdGF0dXNSZXNldEJ0blNlbGVjdG9yID0gJy5tb2R1bGUtc3RhdHVzLXJlc2V0JztcblxuICAgIC8vIFNlbGVjdG9ycyBmb3IgTW9kdWxlIEltcG9ydCBhbmQgQWRkb25zIGNvbm5lY3RcbiAgICB0aGlzLmFkZG9uc0Nvbm5lY3RNb2RhbEJ0blNlbGVjdG9yID0gJyNwYWdlLWhlYWRlci1kZXNjLWNvbmZpZ3VyYXRpb24tYWRkb25zX2Nvbm5lY3QnO1xuICAgIHRoaXMuYWRkb25zTG9nb3V0TW9kYWxCdG5TZWxlY3RvciA9ICcjcGFnZS1oZWFkZXItZGVzYy1jb25maWd1cmF0aW9uLWFkZG9uc19sb2dvdXQnO1xuICAgIHRoaXMuYWRkb25zSW1wb3J0TW9kYWxCdG5TZWxlY3RvciA9ICcjcGFnZS1oZWFkZXItZGVzYy1jb25maWd1cmF0aW9uLWFkZF9tb2R1bGUnO1xuICAgIHRoaXMuZHJvcFpvbmVNb2RhbFNlbGVjdG9yID0gJyNtb2R1bGUtbW9kYWwtaW1wb3J0JztcbiAgICB0aGlzLmRyb3Bab25lTW9kYWxGb290ZXJTZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWltcG9ydCAubW9kYWwtZm9vdGVyJztcbiAgICB0aGlzLmRyb3Bab25lSW1wb3J0Wm9uZVNlbGVjdG9yID0gJyNpbXBvcnREcm9wem9uZSc7XG4gICAgdGhpcy5hZGRvbnNDb25uZWN0TW9kYWxTZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWFkZG9ucy1jb25uZWN0JztcbiAgICB0aGlzLmFkZG9uc0xvZ291dE1vZGFsU2VsZWN0b3IgPSAnI21vZHVsZS1tb2RhbC1hZGRvbnMtbG9nb3V0JztcbiAgICB0aGlzLmFkZG9uc0Nvbm5lY3RGb3JtID0gJyNhZGRvbnMtY29ubmVjdC1mb3JtJztcbiAgICB0aGlzLm1vZHVsZUltcG9ydE1vZGFsQ2xvc2VCdG4gPSAnI21vZHVsZS1tb2RhbC1pbXBvcnQtY2xvc2luZy1jcm9zcyc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRTdGFydFNlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LXN0YXJ0JztcbiAgICB0aGlzLm1vZHVsZUltcG9ydFByb2Nlc3NpbmdTZWxlY3RvciA9ICcubW9kdWxlLWltcG9ydC1wcm9jZXNzaW5nJztcbiAgICB0aGlzLm1vZHVsZUltcG9ydFN1Y2Nlc3NTZWxlY3RvciA9ICcubW9kdWxlLWltcG9ydC1zdWNjZXNzJztcbiAgICB0aGlzLm1vZHVsZUltcG9ydFN1Y2Nlc3NDb25maWd1cmVCdG5TZWxlY3RvciA9ICcubW9kdWxlLWltcG9ydC1zdWNjZXNzLWNvbmZpZ3VyZSc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRGYWlsdXJlU2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtZmFpbHVyZSc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRGYWlsdXJlUmV0cnlTZWxlY3RvciA9ICcubW9kdWxlLWltcG9ydC1mYWlsdXJlLXJldHJ5JztcbiAgICB0aGlzLm1vZHVsZUltcG9ydEZhaWx1cmVEZXRhaWxzQnRuU2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtZmFpbHVyZS1kZXRhaWxzLWFjdGlvbic7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRTZWxlY3RGaWxlTWFudWFsU2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtc3RhcnQtc2VsZWN0LW1hbnVhbCc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRGYWlsdXJlTXNnRGV0YWlsc1NlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LWZhaWx1cmUtZGV0YWlscyc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRDb25maXJtU2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtY29uZmlybSc7XG5cbiAgICB0aGlzLmluaXRTb3J0aW5nRHJvcGRvd24oKTtcbiAgICB0aGlzLmluaXRCT0V2ZW50UmVnaXN0ZXJpbmcoKTtcbiAgICB0aGlzLmluaXRDdXJyZW50RGlzcGxheSgpO1xuICAgIHRoaXMuaW5pdFNvcnRpbmdEaXNwbGF5U3dpdGNoKCk7XG4gICAgdGhpcy5pbml0QnVsa0Ryb3Bkb3duKCk7XG4gICAgdGhpcy5pbml0U2VhcmNoQmxvY2soKTtcbiAgICB0aGlzLmluaXRDYXRlZ29yeVNlbGVjdCgpO1xuICAgIHRoaXMuaW5pdENhdGVnb3JpZXNHcmlkKCk7XG4gICAgdGhpcy5pbml0QWN0aW9uQnV0dG9ucygpO1xuICAgIHRoaXMuaW5pdEFkZG9uc1NlYXJjaCgpO1xuICAgIHRoaXMuaW5pdEFkZG9uc0Nvbm5lY3QoKTtcbiAgICB0aGlzLmluaXRBZGRNb2R1bGVBY3Rpb24oKTtcbiAgICB0aGlzLmluaXREcm9wem9uZSgpO1xuICAgIHRoaXMuaW5pdFBhZ2VDaGFuZ2VQcm90ZWN0aW9uKCk7XG4gICAgdGhpcy5pbml0UGxhY2Vob2xkZXJNZWNoYW5pc20oKTtcbiAgICB0aGlzLmluaXRGaWx0ZXJTdGF0dXNEcm9wZG93bigpO1xuICAgIHRoaXMuZmV0Y2hNb2R1bGVzTGlzdCgpO1xuICAgIHRoaXMuZ2V0Tm90aWZpY2F0aW9uc0NvdW50KCk7XG4gICAgdGhpcy5pbml0aWFsaXplU2VlTW9yZSgpO1xuICB9XG5cbiAgaW5pdEZpbHRlclN0YXR1c0Ryb3Bkb3duKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0IGJvZHkgPSAkKCdib2R5Jyk7XG4gICAgYm9keS5vbignY2xpY2snLCBzZWxmLnN0YXR1c0l0ZW1TZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgLy8gR2V0IGRhdGEgZnJvbSBsaSBET00gaW5wdXRcbiAgICAgIHNlbGYuY3VycmVudFJlZlN0YXR1cyA9IHBhcnNlSW50KCQodGhpcykuZGF0YSgnc3RhdHVzLXJlZicpLCAxMCk7XG4gICAgICAvLyBDaGFuZ2UgZHJvcGRvd24gbGFiZWwgdG8gc2V0IGl0IHRvIHRoZSBjdXJyZW50IHN0YXR1cycgZGlzcGxheW5hbWVcbiAgICAgICQoc2VsZi5zdGF0dXNTZWxlY3RvckxhYmVsU2VsZWN0b3IpLnRleHQoJCh0aGlzKS5maW5kKCdhOmZpcnN0JykudGV4dCgpKTtcbiAgICAgICQoc2VsZi5zdGF0dXNSZXNldEJ0blNlbGVjdG9yKS5zaG93KCk7XG4gICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICB9KTtcblxuICAgIGJvZHkub24oJ2NsaWNrJywgc2VsZi5zdGF0dXNSZXNldEJ0blNlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICAkKHNlbGYuc3RhdHVzU2VsZWN0b3JMYWJlbFNlbGVjdG9yKS50ZXh0KCQodGhpcykuZmluZCgnYScpLnRleHQoKSk7XG4gICAgICAkKHRoaXMpLmhpZGUoKTtcbiAgICAgIHNlbGYuY3VycmVudFJlZlN0YXR1cyA9IG51bGw7XG4gICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICB9KTtcbiAgfVxuXG4gIGluaXRCdWxrRHJvcGRvd24oKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgY29uc3QgYm9keSA9ICQoJ2JvZHknKTtcblxuXG4gICAgYm9keS5vbignY2xpY2snLCBzZWxmLmdldEJ1bGtDaGVja2JveGVzU2VsZWN0b3IoKSwgKCkgPT4ge1xuICAgICAgY29uc3Qgc2VsZWN0b3IgPSAkKHNlbGYuYnVsa0FjdGlvbkRyb3BEb3duU2VsZWN0b3IpO1xuICAgICAgaWYgKCQoc2VsZi5nZXRCdWxrQ2hlY2tib3hlc0NoZWNrZWRTZWxlY3RvcigpKS5sZW5ndGggPiAwKSB7XG4gICAgICAgIHNlbGVjdG9yLmNsb3Nlc3QoJy5tb2R1bGUtdG9wLW1lbnUtaXRlbScpXG4gICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgc2VsZWN0b3IuY2xvc2VzdCgnLm1vZHVsZS10b3AtbWVudS1pdGVtJylcbiAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICBib2R5Lm9uKCdjbGljaycsIHNlbGYuYnVsa0l0ZW1TZWxlY3RvciwgZnVuY3Rpb24gaW5pdGlhbGl6ZUJvZHlDaGFuZ2UoKSB7XG4gICAgICBpZiAoJChzZWxmLmdldEJ1bGtDaGVja2JveGVzQ2hlY2tlZFNlbGVjdG9yKCkpLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICAkLmdyb3dsLndhcm5pbmcoe21lc3NhZ2U6IHdpbmRvdy50cmFuc2xhdGVfamF2YXNjcmlwdHNbJ0J1bGsgQWN0aW9uIC0gT25lIG1vZHVsZSBtaW5pbXVtJ119KTtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBzZWxmLmxhc3RCdWxrQWN0aW9uID0gJCh0aGlzKS5kYXRhKCdyZWYnKTtcbiAgICAgIGNvbnN0IG1vZHVsZXNMaXN0U3RyaW5nID0gc2VsZi5idWlsZEJ1bGtBY3Rpb25Nb2R1bGVMaXN0KCk7XG4gICAgICBjb25zdCBhY3Rpb25TdHJpbmcgPSAkKHRoaXMpLmZpbmQoJzpjaGVja2VkJykudGV4dCgpLnRvTG93ZXJDYXNlKCk7XG4gICAgICAkKHNlbGYuYnVsa0NvbmZpcm1Nb2RhbExpc3RTZWxlY3RvcikuaHRtbChtb2R1bGVzTGlzdFN0cmluZyk7XG4gICAgICAkKHNlbGYuYnVsa0NvbmZpcm1Nb2RhbEFjdGlvbk5hbWVTZWxlY3RvcikudGV4dChhY3Rpb25TdHJpbmcpO1xuXG4gICAgICBpZiAoc2VsZi5sYXN0QnVsa0FjdGlvbiA9PT0gJ2J1bGstdW5pbnN0YWxsJykge1xuICAgICAgICAkKHNlbGYuYnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0b3IpLnNob3coKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICQoc2VsZi5idWxrQWN0aW9uQ2hlY2tib3hTZWxlY3RvcikuaGlkZSgpO1xuICAgICAgfVxuXG4gICAgICAkKHNlbGYuYnVsa0NvbmZpcm1Nb2RhbFNlbGVjdG9yKS5tb2RhbCgnc2hvdycpO1xuICAgIH0pO1xuXG4gICAgYm9keS5vbignY2xpY2snLCB0aGlzLmJ1bGtDb25maXJtTW9kYWxBY2tCdG5TZWxlY3RvciwgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAkKHNlbGYuYnVsa0NvbmZpcm1Nb2RhbFNlbGVjdG9yKS5tb2RhbCgnaGlkZScpO1xuICAgICAgc2VsZi5kb0J1bGtBY3Rpb24oc2VsZi5sYXN0QnVsa0FjdGlvbik7XG4gICAgfSk7XG4gIH1cblxuICBpbml0Qk9FdmVudFJlZ2lzdGVyaW5nKCkge1xuICAgIHdpbmRvdy5CT0V2ZW50Lm9uKCdNb2R1bGUgRGlzYWJsZWQnLCB0aGlzLm9uTW9kdWxlRGlzYWJsZWQsIHRoaXMpO1xuICAgIHdpbmRvdy5CT0V2ZW50Lm9uKCdNb2R1bGUgVW5pbnN0YWxsZWQnLCB0aGlzLnVwZGF0ZVRvdGFsUmVzdWx0cywgdGhpcyk7XG4gIH1cblxuICBvbk1vZHVsZURpc2FibGVkKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0IG1vZHVsZUl0ZW1TZWxlY3RvciA9IHNlbGYuZ2V0TW9kdWxlSXRlbVNlbGVjdG9yKCk7XG5cbiAgICAkKCcubW9kdWxlcy1saXN0JykuZWFjaChmdW5jdGlvbiBzY2FuTW9kdWxlc0xpc3QoKSB7XG4gICAgICBzZWxmLnVwZGF0ZVRvdGFsUmVzdWx0cygpO1xuICAgIH0pO1xuICB9XG5cbiAgaW5pdFBsYWNlaG9sZGVyTWVjaGFuaXNtKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGlmICgkKHNlbGYucGxhY2Vob2xkZXJHbG9iYWxTZWxlY3RvcikubGVuZ3RoKSB7XG4gICAgICBzZWxmLmFqYXhMb2FkUGFnZSgpO1xuICAgIH1cblxuICAgIC8vIFJldHJ5IGxvYWRpbmcgbWVjaGFuaXNtXG4gICAgJCgnYm9keScpLm9uKCdjbGljaycsIHNlbGYucGxhY2Vob2xkZXJGYWlsdXJlUmV0cnlCdG5TZWxlY3RvciwgKCkgPT4ge1xuICAgICAgJChzZWxmLnBsYWNlaG9sZGVyRmFpbHVyZUdsb2JhbFNlbGVjdG9yKS5mYWRlT3V0KCk7XG4gICAgICAkKHNlbGYucGxhY2Vob2xkZXJHbG9iYWxTZWxlY3RvcikuZmFkZUluKCk7XG4gICAgICBzZWxmLmFqYXhMb2FkUGFnZSgpO1xuICAgIH0pO1xuICB9XG5cbiAgYWpheExvYWRQYWdlKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIG1ldGhvZDogJ0dFVCcsXG4gICAgICB1cmw6IHdpbmRvdy5tb2R1bGVVUkxzLmNhdGFsb2dSZWZyZXNoLFxuICAgIH0pLmRvbmUoKHJlc3BvbnNlKSA9PiB7XG4gICAgICBpZiAocmVzcG9uc2Uuc3RhdHVzID09PSB0cnVlKSB7XG4gICAgICAgIGlmICh0eXBlb2YgcmVzcG9uc2UuZG9tRWxlbWVudHMgPT09ICd1bmRlZmluZWQnKSByZXNwb25zZS5kb21FbGVtZW50cyA9IG51bGw7XG4gICAgICAgIGlmICh0eXBlb2YgcmVzcG9uc2UubXNnID09PSAndW5kZWZpbmVkJykgcmVzcG9uc2UubXNnID0gbnVsbDtcblxuICAgICAgICBjb25zdCBzdHlsZXNoZWV0ID0gZG9jdW1lbnQuc3R5bGVTaGVldHNbMF07XG4gICAgICAgIGNvbnN0IHN0eWxlc2hlZXRSdWxlID0gJ3tkaXNwbGF5OiBub25lfSc7XG4gICAgICAgIGNvbnN0IG1vZHVsZUdsb2JhbFNlbGVjdG9yID0gJy5tb2R1bGVzLWxpc3QnO1xuICAgICAgICBjb25zdCBtb2R1bGVTb3J0aW5nU2VsZWN0b3IgPSAnLm1vZHVsZS1zb3J0aW5nLW1lbnUnO1xuICAgICAgICBjb25zdCByZXF1aXJlZFNlbGVjdG9yQ29tYmluYXRpb24gPSBgJHttb2R1bGVHbG9iYWxTZWxlY3Rvcn0sJHttb2R1bGVTb3J0aW5nU2VsZWN0b3J9YDtcblxuICAgICAgICBpZiAoc3R5bGVzaGVldC5pbnNlcnRSdWxlKSB7XG4gICAgICAgICAgc3R5bGVzaGVldC5pbnNlcnRSdWxlKFxuICAgICAgICAgICAgcmVxdWlyZWRTZWxlY3RvckNvbWJpbmF0aW9uICtcbiAgICAgICAgICAgIHN0eWxlc2hlZXRSdWxlLCBzdHlsZXNoZWV0LmNzc1J1bGVzLmxlbmd0aFxuICAgICAgICAgICk7XG4gICAgICAgIH0gZWxzZSBpZiAoc3R5bGVzaGVldC5hZGRSdWxlKSB7XG4gICAgICAgICAgc3R5bGVzaGVldC5hZGRSdWxlKFxuICAgICAgICAgICAgcmVxdWlyZWRTZWxlY3RvckNvbWJpbmF0aW9uLFxuICAgICAgICAgICAgc3R5bGVzaGVldFJ1bGUsXG4gICAgICAgICAgICAtMVxuICAgICAgICAgICk7XG4gICAgICAgIH1cblxuICAgICAgICAkKHNlbGYucGxhY2Vob2xkZXJHbG9iYWxTZWxlY3RvcikuZmFkZU91dCg4MDAsICgpID0+IHtcbiAgICAgICAgICAkLmVhY2gocmVzcG9uc2UuZG9tRWxlbWVudHMsIChpbmRleCwgZWxlbWVudCkgPT4ge1xuICAgICAgICAgICAgJChlbGVtZW50LnNlbGVjdG9yKS5hcHBlbmQoZWxlbWVudC5jb250ZW50KTtcbiAgICAgICAgICB9KTtcbiAgICAgICAgICAkKG1vZHVsZUdsb2JhbFNlbGVjdG9yKS5mYWRlSW4oODAwKS5jc3MoJ2Rpc3BsYXknLCAnZmxleCcpO1xuICAgICAgICAgICQobW9kdWxlU29ydGluZ1NlbGVjdG9yKS5mYWRlSW4oODAwKTtcbiAgICAgICAgICAkKCdbZGF0YS10b2dnbGU9XCJwb3BvdmVyXCJdJykucG9wb3ZlcigpO1xuICAgICAgICAgIHNlbGYuaW5pdEN1cnJlbnREaXNwbGF5KCk7XG4gICAgICAgICAgc2VsZi5mZXRjaE1vZHVsZXNMaXN0KCk7XG4gICAgICAgIH0pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgJChzZWxmLnBsYWNlaG9sZGVyR2xvYmFsU2VsZWN0b3IpLmZhZGVPdXQoODAwLCAoKSA9PiB7XG4gICAgICAgICAgJChzZWxmLnBsYWNlaG9sZGVyRmFpbHVyZU1zZ1NlbGVjdG9yKS50ZXh0KHJlc3BvbnNlLm1zZyk7XG4gICAgICAgICAgJChzZWxmLnBsYWNlaG9sZGVyRmFpbHVyZUdsb2JhbFNlbGVjdG9yKS5mYWRlSW4oODAwKTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgfSkuZmFpbCgocmVzcG9uc2UpID0+IHtcbiAgICAgICQoc2VsZi5wbGFjZWhvbGRlckdsb2JhbFNlbGVjdG9yKS5mYWRlT3V0KDgwMCwgKCkgPT4ge1xuICAgICAgICAkKHNlbGYucGxhY2Vob2xkZXJGYWlsdXJlTXNnU2VsZWN0b3IpLnRleHQocmVzcG9uc2Uuc3RhdHVzVGV4dCk7XG4gICAgICAgICQoc2VsZi5wbGFjZWhvbGRlckZhaWx1cmVHbG9iYWxTZWxlY3RvcikuZmFkZUluKDgwMCk7XG4gICAgICB9KTtcbiAgICB9KTtcbiAgfVxuXG4gIGZldGNoTW9kdWxlc0xpc3QoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgbGV0IGNvbnRhaW5lcjtcbiAgICBsZXQgJHRoaXM7XG5cbiAgICBzZWxmLm1vZHVsZXNMaXN0ID0gW107XG4gICAgJCgnLm1vZHVsZXMtbGlzdCcpLmVhY2goZnVuY3Rpb24gcHJlcGFyZUNvbnRhaW5lcigpIHtcbiAgICAgIGNvbnRhaW5lciA9ICQodGhpcyk7XG4gICAgICBjb250YWluZXIuZmluZCgnLm1vZHVsZS1pdGVtJykuZWFjaChmdW5jdGlvbiBwcmVwYXJlTW9kdWxlcygpIHtcbiAgICAgICAgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICBzZWxmLm1vZHVsZXNMaXN0LnB1c2goe1xuICAgICAgICAgIGRvbU9iamVjdDogJHRoaXMsXG4gICAgICAgICAgaWQ6ICR0aGlzLmRhdGEoJ2lkJyksXG4gICAgICAgICAgbmFtZTogJHRoaXMuZGF0YSgnbmFtZScpLnRvTG93ZXJDYXNlKCksXG4gICAgICAgICAgc2NvcmluZzogcGFyc2VGbG9hdCgkdGhpcy5kYXRhKCdzY29yaW5nJykpLFxuICAgICAgICAgIGxvZ286ICR0aGlzLmRhdGEoJ2xvZ28nKSxcbiAgICAgICAgICBhdXRob3I6ICR0aGlzLmRhdGEoJ2F1dGhvcicpLnRvTG93ZXJDYXNlKCksXG4gICAgICAgICAgdmVyc2lvbjogJHRoaXMuZGF0YSgndmVyc2lvbicpLFxuICAgICAgICAgIGRlc2NyaXB0aW9uOiAkdGhpcy5kYXRhKCdkZXNjcmlwdGlvbicpLnRvTG93ZXJDYXNlKCksXG4gICAgICAgICAgdGVjaE5hbWU6ICR0aGlzLmRhdGEoJ3RlY2gtbmFtZScpLnRvTG93ZXJDYXNlKCksXG4gICAgICAgICAgY2hpbGRDYXRlZ29yaWVzOiAkdGhpcy5kYXRhKCdjaGlsZC1jYXRlZ29yaWVzJyksXG4gICAgICAgICAgY2F0ZWdvcmllczogU3RyaW5nKCR0aGlzLmRhdGEoJ2NhdGVnb3JpZXMnKSkudG9Mb3dlckNhc2UoKSxcbiAgICAgICAgICB0eXBlOiAkdGhpcy5kYXRhKCd0eXBlJyksXG4gICAgICAgICAgcHJpY2U6IHBhcnNlRmxvYXQoJHRoaXMuZGF0YSgncHJpY2UnKSksXG4gICAgICAgICAgYWN0aXZlOiBwYXJzZUludCgkdGhpcy5kYXRhKCdhY3RpdmUnKSwgMTApLFxuICAgICAgICAgIGFjY2VzczogJHRoaXMuZGF0YSgnbGFzdC1hY2Nlc3MnKSxcbiAgICAgICAgICBkaXNwbGF5OiAkdGhpcy5oYXNDbGFzcygnbW9kdWxlLWl0ZW0tbGlzdCcpID8gc2VsZi5ESVNQTEFZX0xJU1QgOiBzZWxmLkRJU1BMQVlfR1JJRCxcbiAgICAgICAgICBjb250YWluZXIsXG4gICAgICAgIH0pO1xuXG4gICAgICAgICR0aGlzLnJlbW92ZSgpO1xuICAgICAgfSk7XG4gICAgfSk7XG5cbiAgICBzZWxmLmFkZG9uc0NhcmRHcmlkID0gJCh0aGlzLmFkZG9uSXRlbUdyaWRTZWxlY3Rvcik7XG4gICAgc2VsZi5hZGRvbnNDYXJkTGlzdCA9ICQodGhpcy5hZGRvbkl0ZW1MaXN0U2VsZWN0b3IpO1xuICAgIHNlbGYudXBkYXRlTW9kdWxlVmlzaWJpbGl0eSgpO1xuICAgICQoJ2JvZHknKS50cmlnZ2VyKCdtb2R1bGVDYXRhbG9nTG9hZGVkJyk7XG4gIH1cblxuICAvKipcbiAgICogUHJlcGFyZSBzb3J0aW5nXG4gICAqXG4gICAqL1xuICB1cGRhdGVNb2R1bGVTb3J0aW5nKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgaWYgKCFzZWxmLmN1cnJlbnRTb3J0aW5nKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgLy8gTW9kdWxlcyBzb3J0aW5nXG4gICAgbGV0IG9yZGVyID0gJ2FzYyc7XG4gICAgbGV0IGtleSA9IHNlbGYuY3VycmVudFNvcnRpbmc7XG4gICAgY29uc3Qgc3BsaXR0ZWRLZXkgPSBrZXkuc3BsaXQoJy0nKTtcbiAgICBpZiAoc3BsaXR0ZWRLZXkubGVuZ3RoID4gMSkge1xuICAgICAga2V5ID0gc3BsaXR0ZWRLZXlbMF07XG4gICAgICBpZiAoc3BsaXR0ZWRLZXlbMV0gPT09ICdkZXNjJykge1xuICAgICAgICBvcmRlciA9ICdkZXNjJztcbiAgICAgIH1cbiAgICB9XG5cbiAgICBjb25zdCBjdXJyZW50Q29tcGFyZSA9IChhLCBiKSA9PiB7XG4gICAgICBsZXQgYURhdGEgPSBhW2tleV07XG4gICAgICBsZXQgYkRhdGEgPSBiW2tleV07XG4gICAgICBpZiAoa2V5ID09PSAnYWNjZXNzJykge1xuICAgICAgICBhRGF0YSA9IChuZXcgRGF0ZShhRGF0YSkpLmdldFRpbWUoKTtcbiAgICAgICAgYkRhdGEgPSAobmV3IERhdGUoYkRhdGEpKS5nZXRUaW1lKCk7XG4gICAgICAgIGFEYXRhID0gaXNOYU4oYURhdGEpID8gMCA6IGFEYXRhO1xuICAgICAgICBiRGF0YSA9IGlzTmFOKGJEYXRhKSA/IDAgOiBiRGF0YTtcbiAgICAgICAgaWYgKGFEYXRhID09PSBiRGF0YSkge1xuICAgICAgICAgIHJldHVybiBiLm5hbWUubG9jYWxlQ29tcGFyZShhLm5hbWUpO1xuICAgICAgICB9XG4gICAgICB9XG5cbiAgICAgIGlmIChhRGF0YSA8IGJEYXRhKSByZXR1cm4gLTE7XG4gICAgICBpZiAoYURhdGEgPiBiRGF0YSkgcmV0dXJuIDE7XG5cbiAgICAgIHJldHVybiAwO1xuICAgIH07XG5cbiAgICBzZWxmLm1vZHVsZXNMaXN0LnNvcnQoY3VycmVudENvbXBhcmUpO1xuICAgIGlmIChvcmRlciA9PT0gJ2Rlc2MnKSB7XG4gICAgICBzZWxmLm1vZHVsZXNMaXN0LnJldmVyc2UoKTtcbiAgICB9XG4gIH1cblxuICB1cGRhdGVNb2R1bGVDb250YWluZXJEaXNwbGF5KCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgJCgnLm1vZHVsZS1zaG9ydC1saXN0JykuZWFjaChmdW5jdGlvbiBzZXRTaG9ydExpc3RWaXNpYmlsaXR5KCkge1xuICAgICAgY29uc3QgY29udGFpbmVyID0gJCh0aGlzKTtcbiAgICAgIGNvbnN0IG5iTW9kdWxlc0luQ29udGFpbmVyID0gY29udGFpbmVyLmZpbmQoJy5tb2R1bGUtaXRlbScpLmxlbmd0aDtcbiAgICAgIGlmIChcbiAgICAgICAgKFxuICAgICAgICAgIHNlbGYuY3VycmVudFJlZkNhdGVnb3J5XG4gICAgICAgICAgJiYgc2VsZi5jdXJyZW50UmVmQ2F0ZWdvcnkgIT09IFN0cmluZyhjb250YWluZXIuZmluZCgnLm1vZHVsZXMtbGlzdCcpLmRhdGEoJ25hbWUnKSlcbiAgICAgICAgKSB8fCAoXG4gICAgICAgICAgc2VsZi5jdXJyZW50UmVmU3RhdHVzICE9PSBudWxsXG4gICAgICAgICAgJiYgbmJNb2R1bGVzSW5Db250YWluZXIgPT09IDBcbiAgICAgICAgKSB8fCAoXG4gICAgICAgICAgbmJNb2R1bGVzSW5Db250YWluZXIgPT09IDBcbiAgICAgICAgICAmJiBTdHJpbmcoY29udGFpbmVyLmZpbmQoJy5tb2R1bGVzLWxpc3QnKS5kYXRhKCduYW1lJykpID09PSBzZWxmLkNBVEVHT1JZX1JFQ0VOVExZX1VTRURcbiAgICAgICAgKSB8fCAoXG4gICAgICAgICAgc2VsZi5jdXJyZW50VGFnc0xpc3QubGVuZ3RoID4gMFxuICAgICAgICAgICYmIG5iTW9kdWxlc0luQ29udGFpbmVyID09PSAwXG4gICAgICAgIClcbiAgICAgICkge1xuICAgICAgICBjb250YWluZXIuaGlkZSgpO1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIGNvbnRhaW5lci5zaG93KCk7XG4gICAgICBpZiAobmJNb2R1bGVzSW5Db250YWluZXIgPj0gc2VsZi5ERUZBVUxUX01BWF9QRVJfQ0FURUdPUklFUykge1xuICAgICAgICBjb250YWluZXIuZmluZChgJHtzZWxmLnNlZU1vcmVTZWxlY3Rvcn0sICR7c2VsZi5zZWVMZXNzU2VsZWN0b3J9YCkuc2hvdygpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgY29udGFpbmVyLmZpbmQoYCR7c2VsZi5zZWVNb3JlU2VsZWN0b3J9LCAke3NlbGYuc2VlTGVzc1NlbGVjdG9yfWApLmhpZGUoKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIHVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICBzZWxmLnVwZGF0ZU1vZHVsZVNvcnRpbmcoKTtcblxuICAgICQoc2VsZi5yZWNlbnRseVVzZWRTZWxlY3RvcikuZmluZCgnLm1vZHVsZS1pdGVtJykucmVtb3ZlKCk7XG4gICAgJCgnLm1vZHVsZXMtbGlzdCcpLmZpbmQoJy5tb2R1bGUtaXRlbScpLnJlbW92ZSgpO1xuXG4gICAgLy8gTW9kdWxlcyB2aXNpYmlsaXR5IG1hbmFnZW1lbnRcbiAgICBsZXQgaXNWaXNpYmxlO1xuICAgIGxldCBjdXJyZW50TW9kdWxlO1xuICAgIGxldCBtb2R1bGVDYXRlZ29yeTtcbiAgICBsZXQgdGFnRXhpc3RzO1xuICAgIGxldCBuZXdWYWx1ZTtcblxuICAgIGNvbnN0IG1vZHVsZXNMaXN0TGVuZ3RoID0gc2VsZi5tb2R1bGVzTGlzdC5sZW5ndGg7XG4gICAgY29uc3QgY291bnRlciA9IHt9O1xuXG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBtb2R1bGVzTGlzdExlbmd0aDsgaSArPSAxKSB7XG4gICAgICBjdXJyZW50TW9kdWxlID0gc2VsZi5tb2R1bGVzTGlzdFtpXTtcbiAgICAgIGlmIChjdXJyZW50TW9kdWxlLmRpc3BsYXkgPT09IHNlbGYuY3VycmVudERpc3BsYXkpIHtcbiAgICAgICAgaXNWaXNpYmxlID0gdHJ1ZTtcblxuICAgICAgICBtb2R1bGVDYXRlZ29yeSA9IHNlbGYuY3VycmVudFJlZkNhdGVnb3J5ID09PSBzZWxmLkNBVEVHT1JZX1JFQ0VOVExZX1VTRUQgP1xuICAgICAgICAgICAgICAgICAgICAgICAgIHNlbGYuQ0FURUdPUllfUkVDRU5UTFlfVVNFRCA6XG4gICAgICAgICAgICAgICAgICAgICAgICAgY3VycmVudE1vZHVsZS5jYXRlZ29yaWVzO1xuXG4gICAgICAgIC8vIENoZWNrIGZvciBzYW1lIGNhdGVnb3J5XG4gICAgICAgIGlmIChzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeSAhPT0gbnVsbCkge1xuICAgICAgICAgIGlzVmlzaWJsZSAmPSBtb2R1bGVDYXRlZ29yeSA9PT0gc2VsZi5jdXJyZW50UmVmQ2F0ZWdvcnk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBDaGVjayBmb3Igc2FtZSBzdGF0dXNcbiAgICAgICAgaWYgKHNlbGYuY3VycmVudFJlZlN0YXR1cyAhPT0gbnVsbCkge1xuICAgICAgICAgIGlzVmlzaWJsZSAmPSBjdXJyZW50TW9kdWxlLmFjdGl2ZSA9PT0gc2VsZi5jdXJyZW50UmVmU3RhdHVzO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gQ2hlY2sgZm9yIHRhZyBsaXN0XG4gICAgICAgIGlmIChzZWxmLmN1cnJlbnRUYWdzTGlzdC5sZW5ndGgpIHtcbiAgICAgICAgICB0YWdFeGlzdHMgPSBmYWxzZTtcbiAgICAgICAgICAkLmVhY2goc2VsZi5jdXJyZW50VGFnc0xpc3QsIChpbmRleCwgdmFsdWUpID0+IHtcbiAgICAgICAgICAgIG5ld1ZhbHVlID0gdmFsdWUudG9Mb3dlckNhc2UoKTtcbiAgICAgICAgICAgIHRhZ0V4aXN0cyB8PSAoXG4gICAgICAgICAgICAgIGN1cnJlbnRNb2R1bGUubmFtZS5pbmRleE9mKG5ld1ZhbHVlKSAhPT0gLTFcbiAgICAgICAgICAgICAgfHwgY3VycmVudE1vZHVsZS5kZXNjcmlwdGlvbi5pbmRleE9mKG5ld1ZhbHVlKSAhPT0gLTFcbiAgICAgICAgICAgICAgfHwgY3VycmVudE1vZHVsZS5hdXRob3IuaW5kZXhPZihuZXdWYWx1ZSkgIT09IC0xXG4gICAgICAgICAgICAgIHx8IGN1cnJlbnRNb2R1bGUudGVjaE5hbWUuaW5kZXhPZihuZXdWYWx1ZSkgIT09IC0xXG4gICAgICAgICAgICApO1xuICAgICAgICAgIH0pO1xuICAgICAgICAgIGlzVmlzaWJsZSAmPSB0YWdFeGlzdHM7XG4gICAgICAgIH1cblxuICAgICAgICAvKipcbiAgICAgICAgICogSWYgbGlzdCBkaXNwbGF5IHdpdGhvdXQgc2VhcmNoIHdlIG11c3QgZGlzcGxheSBvbmx5IHRoZSBmaXJzdCA1IG1vZHVsZXNcbiAgICAgICAgICovXG4gICAgICAgIGlmIChzZWxmLmN1cnJlbnREaXNwbGF5ID09PSBzZWxmLkRJU1BMQVlfTElTVCAmJiAhc2VsZi5jdXJyZW50VGFnc0xpc3QubGVuZ3RoKSB7XG4gICAgICAgICAgaWYgKHNlbGYuY3VycmVudENhdGVnb3J5RGlzcGxheVttb2R1bGVDYXRlZ29yeV0gPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgc2VsZi5jdXJyZW50Q2F0ZWdvcnlEaXNwbGF5W21vZHVsZUNhdGVnb3J5XSA9IGZhbHNlO1xuICAgICAgICAgIH1cblxuICAgICAgICAgIGlmICghY291bnRlclttb2R1bGVDYXRlZ29yeV0pIHtcbiAgICAgICAgICAgIGNvdW50ZXJbbW9kdWxlQ2F0ZWdvcnldID0gMDtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBpZiAobW9kdWxlQ2F0ZWdvcnkgPT09IHNlbGYuQ0FURUdPUllfUkVDRU5UTFlfVVNFRCkge1xuICAgICAgICAgICAgaWYgKGNvdW50ZXJbbW9kdWxlQ2F0ZWdvcnldID49IHNlbGYuREVGQVVMVF9NQVhfUkVDRU5UTFlfVVNFRCkge1xuICAgICAgICAgICAgICBpc1Zpc2libGUgJj0gc2VsZi5jdXJyZW50Q2F0ZWdvcnlEaXNwbGF5W21vZHVsZUNhdGVnb3J5XTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICB9IGVsc2UgaWYgKGNvdW50ZXJbbW9kdWxlQ2F0ZWdvcnldID49IHNlbGYuREVGQVVMVF9NQVhfUEVSX0NBVEVHT1JJRVMpIHtcbiAgICAgICAgICAgIGlzVmlzaWJsZSAmPSBzZWxmLmN1cnJlbnRDYXRlZ29yeURpc3BsYXlbbW9kdWxlQ2F0ZWdvcnldO1xuICAgICAgICAgIH1cblxuICAgICAgICAgIGNvdW50ZXJbbW9kdWxlQ2F0ZWdvcnldICs9IDE7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBJZiB2aXNpYmxlLCBkaXNwbGF5IChUaHggY2FwdGFpbiBvYnZpb3VzKVxuICAgICAgICBpZiAoaXNWaXNpYmxlKSB7XG4gICAgICAgICAgaWYgKHNlbGYuY3VycmVudFJlZkNhdGVnb3J5ID09PSBzZWxmLkNBVEVHT1JZX1JFQ0VOVExZX1VTRUQpIHtcbiAgICAgICAgICAgICQoc2VsZi5yZWNlbnRseVVzZWRTZWxlY3RvcikuYXBwZW5kKGN1cnJlbnRNb2R1bGUuZG9tT2JqZWN0KTtcbiAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgY3VycmVudE1vZHVsZS5jb250YWluZXIuYXBwZW5kKGN1cnJlbnRNb2R1bGUuZG9tT2JqZWN0KTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9XG5cbiAgICBzZWxmLnVwZGF0ZU1vZHVsZUNvbnRhaW5lckRpc3BsYXkoKTtcblxuICAgIGlmIChzZWxmLmN1cnJlbnRUYWdzTGlzdC5sZW5ndGgpIHtcbiAgICAgICQoJy5tb2R1bGVzLWxpc3QnKS5hcHBlbmQodGhpcy5jdXJyZW50RGlzcGxheSA9PT0gc2VsZi5ESVNQTEFZX0dSSUQgPyB0aGlzLmFkZG9uc0NhcmRHcmlkIDogdGhpcy5hZGRvbnNDYXJkTGlzdCk7XG4gICAgfVxuXG4gICAgc2VsZi51cGRhdGVUb3RhbFJlc3VsdHMoKTtcbiAgfVxuXG4gIGluaXRQYWdlQ2hhbmdlUHJvdGVjdGlvbigpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQod2luZG93KS5vbignYmVmb3JldW5sb2FkJywgKCkgPT4ge1xuICAgICAgaWYgKHNlbGYuaXNVcGxvYWRTdGFydGVkID09PSB0cnVlKSB7XG4gICAgICAgIHJldHVybiAnSXQgc2VlbXMgc29tZSBjcml0aWNhbCBvcGVyYXRpb24gYXJlIHJ1bm5pbmcsIGFyZSB5b3Ugc3VyZSB5b3Ugd2FudCB0byBjaGFuZ2UgcGFnZSA/IEl0IG1pZ2h0IGNhdXNlIHNvbWUgdW5leGVwY3RlZCBiZWhhdmlvcnMuJztcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG5cbiAgYnVpbGRCdWxrQWN0aW9uTW9kdWxlTGlzdCgpIHtcbiAgICBjb25zdCBjaGVja0JveGVzU2VsZWN0b3IgPSB0aGlzLmdldEJ1bGtDaGVja2JveGVzQ2hlY2tlZFNlbGVjdG9yKCk7XG4gICAgY29uc3QgbW9kdWxlSXRlbVNlbGVjdG9yID0gdGhpcy5nZXRNb2R1bGVJdGVtU2VsZWN0b3IoKTtcbiAgICBsZXQgYWxyZWFkeURvbmVGbGFnID0gMDtcbiAgICBsZXQgaHRtbEdlbmVyYXRlZCA9ICcnO1xuICAgIGxldCBjdXJyZW50RWxlbWVudDtcblxuICAgICQoY2hlY2tCb3hlc1NlbGVjdG9yKS5lYWNoKGZ1bmN0aW9uIHByZXBhcmVDaGVja2JveGVzKCkge1xuICAgICAgaWYgKGFscmVhZHlEb25lRmxhZyA9PT0gMTApIHtcbiAgICAgICAgLy8gQnJlYWsgZWFjaFxuICAgICAgICBodG1sR2VuZXJhdGVkICs9ICctIC4uLic7XG4gICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgIH1cblxuICAgICAgY3VycmVudEVsZW1lbnQgPSAkKHRoaXMpLmNsb3Nlc3QobW9kdWxlSXRlbVNlbGVjdG9yKTtcbiAgICAgIGh0bWxHZW5lcmF0ZWQgKz0gYC0gJHtjdXJyZW50RWxlbWVudC5kYXRhKCduYW1lJyl9PGJyLz5gO1xuICAgICAgYWxyZWFkeURvbmVGbGFnICs9IDE7XG5cbiAgICAgIHJldHVybiB0cnVlO1xuICAgIH0pO1xuXG4gICAgcmV0dXJuIGh0bWxHZW5lcmF0ZWQ7XG4gIH1cblxuICBpbml0QWRkb25zQ29ubmVjdCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgIC8vIE1ha2UgYWRkb25zIGNvbm5lY3QgbW9kYWwgcmVhZHkgdG8gYmUgY2xpY2tlZFxuICAgIGlmICgkKHNlbGYuYWRkb25zQ29ubmVjdE1vZGFsQnRuU2VsZWN0b3IpLmF0dHIoJ2hyZWYnKSA9PT0gJyMnKSB7XG4gICAgICAkKHNlbGYuYWRkb25zQ29ubmVjdE1vZGFsQnRuU2VsZWN0b3IpLmF0dHIoJ2RhdGEtdG9nZ2xlJywgJ21vZGFsJyk7XG4gICAgICAkKHNlbGYuYWRkb25zQ29ubmVjdE1vZGFsQnRuU2VsZWN0b3IpLmF0dHIoJ2RhdGEtdGFyZ2V0Jywgc2VsZi5hZGRvbnNDb25uZWN0TW9kYWxTZWxlY3Rvcik7XG4gICAgfVxuXG4gICAgaWYgKCQoc2VsZi5hZGRvbnNMb2dvdXRNb2RhbEJ0blNlbGVjdG9yKS5hdHRyKCdocmVmJykgPT09ICcjJykge1xuICAgICAgJChzZWxmLmFkZG9uc0xvZ291dE1vZGFsQnRuU2VsZWN0b3IpLmF0dHIoJ2RhdGEtdG9nZ2xlJywgJ21vZGFsJyk7XG4gICAgICAkKHNlbGYuYWRkb25zTG9nb3V0TW9kYWxCdG5TZWxlY3RvcikuYXR0cignZGF0YS10YXJnZXQnLCBzZWxmLmFkZG9uc0xvZ291dE1vZGFsU2VsZWN0b3IpO1xuICAgIH1cblxuICAgICQoJ2JvZHknKS5vbignc3VibWl0Jywgc2VsZi5hZGRvbnNDb25uZWN0Rm9ybSwgZnVuY3Rpb24gaW5pdGlhbGl6ZUJvZHlTdWJtaXQoZXZlbnQpIHtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgJC5hamF4KHtcbiAgICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICAgIHVybDogJCh0aGlzKS5hdHRyKCdhY3Rpb24nKSxcbiAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgICAgZGF0YTogJCh0aGlzKS5zZXJpYWxpemUoKSxcbiAgICAgICAgYmVmb3JlU2VuZDogKCkgPT4ge1xuICAgICAgICAgICQoc2VsZi5hZGRvbnNMb2dpbkJ1dHRvblNlbGVjdG9yKS5zaG93KCk7XG4gICAgICAgICAgJCgnYnV0dG9uLmJ0blt0eXBlPVwic3VibWl0XCJdJywgc2VsZi5hZGRvbnNDb25uZWN0Rm9ybSkuaGlkZSgpO1xuICAgICAgICB9XG4gICAgICB9KS5kb25lKChyZXNwb25zZSkgPT4ge1xuICAgICAgICBpZiAocmVzcG9uc2Uuc3VjY2VzcyA9PT0gMSkge1xuICAgICAgICAgIGxvY2F0aW9uLnJlbG9hZCgpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IHJlc3BvbnNlLm1lc3NhZ2V9KTtcbiAgICAgICAgICAkKHNlbGYuYWRkb25zTG9naW5CdXR0b25TZWxlY3RvcikuaGlkZSgpO1xuICAgICAgICAgICQoJ2J1dHRvbi5idG5bdHlwZT1cInN1Ym1pdFwiXScsIHNlbGYuYWRkb25zQ29ubmVjdEZvcm0pLmZhZGVJbigpO1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgICB9KTtcbiAgfVxuXG4gIGluaXRBZGRNb2R1bGVBY3Rpb24oKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgY29uc3QgYWRkTW9kdWxlQnV0dG9uID0gJChzZWxmLmFkZG9uc0ltcG9ydE1vZGFsQnRuU2VsZWN0b3IpO1xuICAgIGFkZE1vZHVsZUJ1dHRvbi5hdHRyKCdkYXRhLXRvZ2dsZScsICdtb2RhbCcpO1xuICAgIGFkZE1vZHVsZUJ1dHRvbi5hdHRyKCdkYXRhLXRhcmdldCcsIHNlbGYuZHJvcFpvbmVNb2RhbFNlbGVjdG9yKTtcbiAgfVxuXG4gIGluaXREcm9wem9uZSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBib2R5ID0gJCgnYm9keScpO1xuICAgIGNvbnN0IGRyb3B6b25lID0gJCgnLmRyb3B6b25lJyk7XG5cbiAgICAvLyBSZXNldCBtb2RhbCB3aGVuIGNsaWNrIG9uIFJldHJ5IGluIGNhc2Ugb2YgZmFpbHVyZVxuICAgIGJvZHkub24oXG4gICAgICAnY2xpY2snLFxuICAgICAgdGhpcy5tb2R1bGVJbXBvcnRGYWlsdXJlUmV0cnlTZWxlY3RvcixcbiAgICAgICgpID0+IHtcbiAgICAgICAgJChgJHtzZWxmLm1vZHVsZUltcG9ydFN1Y2Nlc3NTZWxlY3Rvcn0sJHtzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVTZWxlY3Rvcn0sJHtzZWxmLm1vZHVsZUltcG9ydFByb2Nlc3NpbmdTZWxlY3Rvcn1gKS5mYWRlT3V0KCgpID0+IHtcbiAgICAgICAgICAvKipcbiAgICAgICAgICAgKiBBZGRlZCB0aW1lb3V0IGZvciBhIGJldHRlciByZW5kZXIgb2YgYW5pbWF0aW9uXG4gICAgICAgICAgICogYW5kIGF2b2lkIHRvIGhhdmUgZGlzcGxheWVkIGF0IHRoZSBzYW1lIHRpbWVcbiAgICAgICAgICAgKi9cbiAgICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgICAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRTdGFydFNlbGVjdG9yKS5mYWRlSW4oKCkgPT4ge1xuICAgICAgICAgICAgICAkKHNlbGYubW9kdWxlSW1wb3J0RmFpbHVyZU1zZ0RldGFpbHNTZWxlY3RvcikuaGlkZSgpO1xuICAgICAgICAgICAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3VjY2Vzc0NvbmZpZ3VyZUJ0blNlbGVjdG9yKS5oaWRlKCk7XG4gICAgICAgICAgICAgIGRyb3B6b25lLnJlbW92ZUF0dHIoJ3N0eWxlJyk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICB9LCA1NTApO1xuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICApO1xuXG4gICAgLy8gUmVpbml0IG1vZGFsIG9uIGV4aXQsIGJ1dCBjaGVjayBpZiBub3QgYWxyZWFkeSBwcm9jZXNzaW5nIHNvbWV0aGluZ1xuICAgIGJvZHkub24oJ2hpZGRlbi5icy5tb2RhbCcsIHRoaXMuZHJvcFpvbmVNb2RhbFNlbGVjdG9yLCAoKSA9PiB7XG4gICAgICAkKGAke3NlbGYubW9kdWxlSW1wb3J0U3VjY2Vzc1NlbGVjdG9yfSwgJHtzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVTZWxlY3Rvcn1gKS5oaWRlKCk7XG4gICAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3RhcnRTZWxlY3Rvcikuc2hvdygpO1xuXG4gICAgICBkcm9wem9uZS5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgICAgJChzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVNc2dEZXRhaWxzU2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRTdWNjZXNzQ29uZmlndXJlQnRuU2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgICQoc2VsZi5kcm9wWm9uZU1vZGFsRm9vdGVyU2VsZWN0b3IpLmh0bWwoJycpO1xuICAgICAgJChzZWxmLm1vZHVsZUltcG9ydENvbmZpcm1TZWxlY3RvcikuaGlkZSgpO1xuICAgIH0pO1xuXG4gICAgLy8gQ2hhbmdlIHRoZSB3YXkgRHJvcHpvbmUuanMgbGliIGhhbmRsZSBmaWxlIGlucHV0IHRyaWdnZXJcbiAgICBib2R5Lm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgIGAuZHJvcHpvbmU6bm90KCR7dGhpcy5tb2R1bGVJbXBvcnRTZWxlY3RGaWxlTWFudWFsU2VsZWN0b3J9LCAke3RoaXMubW9kdWxlSW1wb3J0U3VjY2Vzc0NvbmZpZ3VyZUJ0blNlbGVjdG9yfSlgLFxuICAgICAgKGV2ZW50LCBtYW51YWxTZWxlY3QpID0+IHtcbiAgICAgICAgLy8gaWYgY2xpY2sgY29tZXMgZnJvbSAubW9kdWxlLWltcG9ydC1zdGFydC1zZWxlY3QtbWFudWFsLCBzdG9wIGV2ZXJ5dGhpbmdcbiAgICAgICAgaWYgKHR5cGVvZiBtYW51YWxTZWxlY3QgPT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgICk7XG5cbiAgICBib2R5Lm9uKCdjbGljaycsIHRoaXMubW9kdWxlSW1wb3J0U2VsZWN0RmlsZU1hbnVhbFNlbGVjdG9yLCAoZXZlbnQpID0+IHtcbiAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIC8qKlxuICAgICAgICogVHJpZ2dlciBjbGljayBvbiBoaWRkZW4gZmlsZSBpbnB1dCwgYW5kIHBhc3MgZXh0cmEgZGF0YVxuICAgICAgICogdG8gLmRyb3B6b25lIGNsaWNrIGhhbmRsZXIgZnJvIGl0IHRvIG5vdGljZSBpdCBjb21lcyBmcm9tIGhlcmVcbiAgICAgICAqL1xuICAgICAgJCgnLmR6LWhpZGRlbi1pbnB1dCcpLnRyaWdnZXIoJ2NsaWNrJywgWydtYW51YWxfc2VsZWN0J10pO1xuICAgIH0pO1xuXG4gICAgLy8gSGFuZGxlIG1vZGFsIGNsb3N1cmVcbiAgICBib2R5Lm9uKCdjbGljaycsIHRoaXMubW9kdWxlSW1wb3J0TW9kYWxDbG9zZUJ0biwgKCkgPT4ge1xuICAgICAgaWYgKHNlbGYuaXNVcGxvYWRTdGFydGVkICE9PSB0cnVlKSB7XG4gICAgICAgICQoc2VsZi5kcm9wWm9uZU1vZGFsU2VsZWN0b3IpLm1vZGFsKCdoaWRlJyk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICAvLyBGaXggaXNzdWUgb24gY2xpY2sgY29uZmlndXJlIGJ1dHRvblxuICAgIGJvZHkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVJbXBvcnRTdWNjZXNzQ29uZmlndXJlQnRuU2VsZWN0b3IsIGZ1bmN0aW9uIGluaXRpYWxpemVCb2R5Q2xpY2tPbk1vZHVsZUltcG9ydChldmVudCkge1xuICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgd2luZG93LmxvY2F0aW9uID0gJCh0aGlzKS5hdHRyKCdocmVmJyk7XG4gICAgfSk7XG5cbiAgICAvLyBPcGVuIGZhaWx1cmUgbWVzc2FnZSBkZXRhaWxzIGJveFxuICAgIGJvZHkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVJbXBvcnRGYWlsdXJlRGV0YWlsc0J0blNlbGVjdG9yLCAoKSA9PiB7XG4gICAgICAkKHNlbGYubW9kdWxlSW1wb3J0RmFpbHVyZU1zZ0RldGFpbHNTZWxlY3Rvcikuc2xpZGVEb3duKCk7XG4gICAgfSk7XG5cbiAgICAvLyBAc2VlOiBkcm9wem9uZS5qc1xuICAgIGNvbnN0IGRyb3B6b25lT3B0aW9ucyA9IHtcbiAgICAgIHVybDogd2luZG93Lm1vZHVsZVVSTHMubW9kdWxlSW1wb3J0LFxuICAgICAgYWNjZXB0ZWRGaWxlczogJy56aXAsIC50YXInLFxuICAgICAgLy8gVGhlIG5hbWUgdGhhdCB3aWxsIGJlIHVzZWQgdG8gdHJhbnNmZXIgdGhlIGZpbGVcbiAgICAgIHBhcmFtTmFtZTogJ2ZpbGVfdXBsb2FkZWQnLFxuICAgICAgbWF4RmlsZXNpemU6IDUwLCAvLyBjYW4ndCBiZSBncmVhdGVyIHRoYW4gNTBNYiBiZWNhdXNlIGl0J3MgYW4gYWRkb25zIGxpbWl0YXRpb25cbiAgICAgIHVwbG9hZE11bHRpcGxlOiBmYWxzZSxcbiAgICAgIGFkZFJlbW92ZUxpbmtzOiB0cnVlLFxuICAgICAgZGljdERlZmF1bHRNZXNzYWdlOiAnJyxcbiAgICAgIGhpZGRlbklucHV0Q29udGFpbmVyOiBzZWxmLmRyb3Bab25lSW1wb3J0Wm9uZVNlbGVjdG9yLFxuICAgICAgLyoqXG4gICAgICAgKiBBZGQgdW5saW1pdGVkIHRpbWVvdXQuIE90aGVyd2lzZSBkcm9wem9uZSB0aW1lb3V0IGlzIDMwIHNlY29uZHNcbiAgICAgICAqICBhbmQgaWYgYSBtb2R1bGUgaXMgbG9uZyB0byBpbnN0YWxsLCBpdCBpcyBub3QgcG9zc2libGUgdG8gaW5zdGFsbCB0aGUgbW9kdWxlLlxuICAgICAgICovXG4gICAgICB0aW1lb3V0OiAwLFxuICAgICAgYWRkZWRmaWxlOiAoKSA9PiB7XG4gICAgICAgIHNlbGYuYW5pbWF0ZVN0YXJ0VXBsb2FkKCk7XG4gICAgICB9LFxuICAgICAgcHJvY2Vzc2luZzogKCkgPT4ge1xuICAgICAgICAvLyBMZWF2ZSBpdCBlbXB0eSBzaW5jZSB3ZSBkb24ndCByZXF1aXJlIGFueXRoaW5nIHdoaWxlIHByb2Nlc3NpbmcgdXBsb2FkXG4gICAgICB9LFxuICAgICAgZXJyb3I6IChmaWxlLCBtZXNzYWdlKSA9PiB7XG4gICAgICAgIHNlbGYuZGlzcGxheU9uVXBsb2FkRXJyb3IobWVzc2FnZSk7XG4gICAgICB9LFxuICAgICAgY29tcGxldGU6IChmaWxlKSA9PiB7XG4gICAgICAgIGlmIChmaWxlLnN0YXR1cyAhPT0gJ2Vycm9yJykge1xuICAgICAgICAgIGNvbnN0IHJlc3BvbnNlT2JqZWN0ID0gJC5wYXJzZUpTT04oZmlsZS54aHIucmVzcG9uc2UpO1xuICAgICAgICAgIGlmICh0eXBlb2YgcmVzcG9uc2VPYmplY3QuaXNfY29uZmlndXJhYmxlID09PSAndW5kZWZpbmVkJykgcmVzcG9uc2VPYmplY3QuaXNfY29uZmlndXJhYmxlID0gbnVsbDtcbiAgICAgICAgICBpZiAodHlwZW9mIHJlc3BvbnNlT2JqZWN0Lm1vZHVsZV9uYW1lID09PSAndW5kZWZpbmVkJykgcmVzcG9uc2VPYmplY3QubW9kdWxlX25hbWUgPSBudWxsO1xuXG4gICAgICAgICAgc2VsZi5kaXNwbGF5T25VcGxvYWREb25lKHJlc3BvbnNlT2JqZWN0KTtcbiAgICAgICAgfVxuICAgICAgICAvLyBTdGF0ZSB0aGF0IHdlIGhhdmUgZmluaXNoIHRoZSBwcm9jZXNzIHRvIHVubG9jayBzb21lIGFjdGlvbnNcbiAgICAgICAgc2VsZi5pc1VwbG9hZFN0YXJ0ZWQgPSBmYWxzZTtcbiAgICAgIH0sXG4gICAgfTtcblxuICAgIGRyb3B6b25lLmRyb3B6b25lKCQuZXh0ZW5kKGRyb3B6b25lT3B0aW9ucykpO1xuICB9XG5cbiAgYW5pbWF0ZVN0YXJ0VXBsb2FkKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0IGRyb3B6b25lID0gJCgnLmRyb3B6b25lJyk7XG4gICAgLy8gU3RhdGUgdGhhdCB3ZSBzdGFydCBtb2R1bGUgdXBsb2FkXG4gICAgc2VsZi5pc1VwbG9hZFN0YXJ0ZWQgPSB0cnVlO1xuICAgICQoc2VsZi5tb2R1bGVJbXBvcnRTdGFydFNlbGVjdG9yKS5oaWRlKDApO1xuICAgIGRyb3B6b25lLmNzcygnYm9yZGVyJywgJ25vbmUnKTtcbiAgICAkKHNlbGYubW9kdWxlSW1wb3J0UHJvY2Vzc2luZ1NlbGVjdG9yKS5mYWRlSW4oKTtcbiAgfVxuXG4gIGFuaW1hdGVFbmRVcGxvYWQoY2FsbGJhY2spIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICAkKHNlbGYubW9kdWxlSW1wb3J0UHJvY2Vzc2luZ1NlbGVjdG9yKS5maW5pc2goKS5mYWRlT3V0KGNhbGxiYWNrKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBNZXRob2QgdG8gY2FsbCBmb3IgdXBsb2FkIG1vZGFsLCB3aGVuIHRoZSBhamF4IGNhbGwgd2VudCB3ZWxsLlxuICAgKlxuICAgKiBAcGFyYW0gb2JqZWN0IHJlc3VsdCBjb250YWluaW5nIHRoZSBzZXJ2ZXIgcmVzcG9uc2VcbiAgICovXG4gIGRpc3BsYXlPblVwbG9hZERvbmUocmVzdWx0KSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgc2VsZi5hbmltYXRlRW5kVXBsb2FkKCgpID0+IHtcbiAgICAgIGlmIChyZXN1bHQuc3RhdHVzID09PSB0cnVlKSB7XG4gICAgICAgIGlmIChyZXN1bHQuaXNfY29uZmlndXJhYmxlID09PSB0cnVlKSB7XG4gICAgICAgICAgY29uc3QgY29uZmlndXJlTGluayA9IHdpbmRvdy5tb2R1bGVVUkxzLmNvbmZpZ3VyYXRpb25QYWdlLnJlcGxhY2UoLzpudW1iZXI6LywgcmVzdWx0Lm1vZHVsZV9uYW1lKTtcbiAgICAgICAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3VjY2Vzc0NvbmZpZ3VyZUJ0blNlbGVjdG9yKS5hdHRyKCdocmVmJywgY29uZmlndXJlTGluayk7XG4gICAgICAgICAgJChzZWxmLm1vZHVsZUltcG9ydFN1Y2Nlc3NDb25maWd1cmVCdG5TZWxlY3Rvcikuc2hvdygpO1xuICAgICAgICB9XG4gICAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRTdWNjZXNzU2VsZWN0b3IpLmZhZGVJbigpO1xuICAgICAgfSBlbHNlIGlmICh0eXBlb2YgcmVzdWx0LmNvbmZpcm1hdGlvbl9zdWJqZWN0ICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICBzZWxmLmRpc3BsYXlQcmVzdGFUcnVzdFN0ZXAocmVzdWx0KTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRGYWlsdXJlTXNnRGV0YWlsc1NlbGVjdG9yKS5odG1sKHJlc3VsdC5tc2cpO1xuICAgICAgICAkKHNlbGYubW9kdWxlSW1wb3J0RmFpbHVyZVNlbGVjdG9yKS5mYWRlSW4oKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBNZXRob2QgdG8gY2FsbCBmb3IgdXBsb2FkIG1vZGFsLCB3aGVuIHRoZSBhamF4IGNhbGwgd2VudCB3cm9uZyBvciB3aGVuIHRoZSBhY3Rpb24gcmVxdWVzdGVkIGNvdWxkIG5vdFxuICAgKiBzdWNjZWVkIGZvciBzb21lIHJlYXNvbi5cbiAgICpcbiAgICogQHBhcmFtIHN0cmluZyBtZXNzYWdlIGV4cGxhaW5pbmcgdGhlIGVycm9yLlxuICAgKi9cbiAgZGlzcGxheU9uVXBsb2FkRXJyb3IobWVzc2FnZSkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIHNlbGYuYW5pbWF0ZUVuZFVwbG9hZCgoKSA9PiB7XG4gICAgICAkKHNlbGYubW9kdWxlSW1wb3J0RmFpbHVyZU1zZ0RldGFpbHNTZWxlY3RvcikuaHRtbChtZXNzYWdlKTtcbiAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRGYWlsdXJlU2VsZWN0b3IpLmZhZGVJbigpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIElmIFByZXN0YVRydXN0IG5lZWRzIHRvIGJlIGNvbmZpcm1lZCwgd2UgYXNrIGZvciB0aGUgY29uZmlybWF0aW9uXG4gICAqIG1vZGFsIGNvbnRlbnQgYW5kIHdlIGRpc3BsYXkgaXQgaW4gdGhlIGN1cnJlbnRseSBkaXNwbGF5ZWQgb25lLlxuICAgKiBXZSBhbHNvIGdlbmVyYXRlIHRoZSBhamF4IGNhbGwgdG8gdHJpZ2dlciBvbmNlIHdlIGNvbmZpcm0gd2Ugd2FudCB0byBpbnN0YWxsXG4gICAqIHRoZSBtb2R1bGUuXG4gICAqXG4gICAqIEBwYXJhbSBQcmV2aW91cyBzZXJ2ZXIgcmVzcG9uc2UgcmVzdWx0XG4gICAqL1xuICBkaXNwbGF5UHJlc3RhVHJ1c3RTdGVwKHJlc3VsdCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0IG1vZGFsID0gc2VsZi5tb2R1bGVDYXJkQ29udHJvbGxlci5fcmVwbGFjZVByZXN0YVRydXN0UGxhY2Vob2xkZXJzKHJlc3VsdCk7XG4gICAgY29uc3QgbW9kdWxlTmFtZSA9IHJlc3VsdC5tb2R1bGUuYXR0cmlidXRlcy5uYW1lO1xuXG4gICAgJCh0aGlzLm1vZHVsZUltcG9ydENvbmZpcm1TZWxlY3RvcikuaHRtbChtb2RhbC5maW5kKCcubW9kYWwtYm9keScpLmh0bWwoKSkuZmFkZUluKCk7XG4gICAgJCh0aGlzLmRyb3Bab25lTW9kYWxGb290ZXJTZWxlY3RvcikuaHRtbChtb2RhbC5maW5kKCcubW9kYWwtZm9vdGVyJykuaHRtbCgpKS5mYWRlSW4oKTtcblxuICAgICQodGhpcy5kcm9wWm9uZU1vZGFsRm9vdGVyU2VsZWN0b3IpLmZpbmQoJy5wc3RydXN0LWluc3RhbGwnKS5vZmYoJ2NsaWNrJykub24oJ2NsaWNrJywgKCkgPT4ge1xuICAgICAgJChzZWxmLm1vZHVsZUltcG9ydENvbmZpcm1TZWxlY3RvcikuaGlkZSgpO1xuICAgICAgJChzZWxmLmRyb3Bab25lTW9kYWxGb290ZXJTZWxlY3RvcikuaHRtbCgnJyk7XG4gICAgICBzZWxmLmFuaW1hdGVTdGFydFVwbG9hZCgpO1xuXG4gICAgICAvLyBJbnN0YWxsIGFqYXggY2FsbFxuICAgICAgJC5wb3N0KHJlc3VsdC5tb2R1bGUuYXR0cmlidXRlcy51cmxzLmluc3RhbGwsIHsnYWN0aW9uUGFyYW1zW2NvbmZpcm1QcmVzdGFUcnVzdF0nOiAnMSd9KVxuICAgICAgIC5kb25lKChkYXRhKSA9PiB7XG4gICAgICAgICBzZWxmLmRpc3BsYXlPblVwbG9hZERvbmUoZGF0YVttb2R1bGVOYW1lXSk7XG4gICAgICAgfSlcbiAgICAgICAuZmFpbCgoZGF0YSkgPT4ge1xuICAgICAgICAgc2VsZi5kaXNwbGF5T25VcGxvYWRFcnJvcihkYXRhW21vZHVsZU5hbWVdKTtcbiAgICAgICB9KVxuICAgICAgIC5hbHdheXMoKCkgPT4ge1xuICAgICAgICAgc2VsZi5pc1VwbG9hZFN0YXJ0ZWQgPSBmYWxzZTtcbiAgICAgICB9KTtcbiAgICB9KTtcbiAgfVxuXG4gIGdldEJ1bGtDaGVja2JveGVzU2VsZWN0b3IoKSB7XG4gICAgcmV0dXJuIHRoaXMuY3VycmVudERpc3BsYXkgPT09IHRoaXMuRElTUExBWV9HUklEXG4gICAgICAgICA/IHRoaXMuYnVsa0FjdGlvbkNoZWNrYm94R3JpZFNlbGVjdG9yXG4gICAgICAgICA6IHRoaXMuYnVsa0FjdGlvbkNoZWNrYm94TGlzdFNlbGVjdG9yO1xuICB9XG5cblxuICBnZXRCdWxrQ2hlY2tib3hlc0NoZWNrZWRTZWxlY3RvcigpIHtcbiAgICByZXR1cm4gdGhpcy5jdXJyZW50RGlzcGxheSA9PT0gdGhpcy5ESVNQTEFZX0dSSURcbiAgICAgICAgID8gdGhpcy5jaGVja2VkQnVsa0FjdGlvbkdyaWRTZWxlY3RvclxuICAgICAgICAgOiB0aGlzLmNoZWNrZWRCdWxrQWN0aW9uTGlzdFNlbGVjdG9yO1xuICB9XG5cbiAgZ2V0TW9kdWxlSXRlbVNlbGVjdG9yKCkge1xuICAgIHJldHVybiB0aGlzLmN1cnJlbnREaXNwbGF5ID09PSB0aGlzLkRJU1BMQVlfR1JJRFxuICAgICAgICAgPyB0aGlzLm1vZHVsZUl0ZW1HcmlkU2VsZWN0b3JcbiAgICAgICAgIDogdGhpcy5tb2R1bGVJdGVtTGlzdFNlbGVjdG9yO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCB0aGUgbW9kdWxlIG5vdGlmaWNhdGlvbnMgY291bnQgYW5kIGRpc3BsYXlzIGl0IGFzIGEgYmFkZ2Ugb24gdGhlIG5vdGlmaWNhdGlvbiB0YWJcbiAgICogQHJldHVybiB2b2lkXG4gICAqL1xuICBnZXROb3RpZmljYXRpb25zQ291bnQoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgJC5nZXRKU09OKFxuICAgICAgd2luZG93Lm1vZHVsZVVSTHMubm90aWZpY2F0aW9uc0NvdW50LFxuICAgICAgc2VsZi51cGRhdGVOb3RpZmljYXRpb25zQ291bnRcbiAgICApLmZhaWwoKCkgPT4ge1xuICAgICAgY29uc29sZS5lcnJvcignQ291bGQgbm90IHJldHJpZXZlIG1vZHVsZSBub3RpZmljYXRpb25zIGNvdW50LicpO1xuICAgIH0pO1xuICB9XG5cbiAgdXBkYXRlTm90aWZpY2F0aW9uc0NvdW50KGJhZGdlKSB7XG4gICAgY29uc3QgZGVzdGluYXRpb25UYWJzID0ge1xuICAgICAgdG9fY29uZmlndXJlOiAkKCcjc3VidGFiLUFkbWluTW9kdWxlc05vdGlmaWNhdGlvbnMnKSxcbiAgICAgIHRvX3VwZGF0ZTogJCgnI3N1YnRhYi1BZG1pbk1vZHVsZXNVcGRhdGVzJyksXG4gICAgfTtcblxuICAgIGZvciAobGV0IGtleSBpbiBkZXN0aW5hdGlvblRhYnMpIHtcbiAgICAgIGlmIChkZXN0aW5hdGlvblRhYnNba2V5XS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgY29udGludWU7XG4gICAgICB9XG5cbiAgICAgIGRlc3RpbmF0aW9uVGFic1trZXldLmZpbmQoJy5ub3RpZmljYXRpb24tY291bnRlcicpLnRleHQoYmFkZ2Vba2V5XSk7XG4gICAgfVxuICB9XG5cbiAgaW5pdEFkZG9uc1NlYXJjaCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICAkKCdib2R5Jykub24oXG4gICAgICAnY2xpY2snLFxuICAgICAgYCR7c2VsZi5hZGRvbkl0ZW1HcmlkU2VsZWN0b3J9LCAke3NlbGYuYWRkb25JdGVtTGlzdFNlbGVjdG9yfWAsXG4gICAgICAoKSA9PiB7XG4gICAgICAgIGxldCBzZWFyY2hRdWVyeSA9ICcnO1xuICAgICAgICBpZiAoc2VsZi5jdXJyZW50VGFnc0xpc3QubGVuZ3RoKSB7XG4gICAgICAgICAgc2VhcmNoUXVlcnkgPSBlbmNvZGVVUklDb21wb25lbnQoc2VsZi5jdXJyZW50VGFnc0xpc3Quam9pbignICcpKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHdpbmRvdy5vcGVuKGAke3NlbGYuYmFzZUFkZG9uc1VybH1zZWFyY2gucGhwP3NlYXJjaF9xdWVyeT0ke3NlYXJjaFF1ZXJ5fWAsICdfYmxhbmsnKTtcbiAgICAgIH1cbiAgICApO1xuICB9XG5cbiAgaW5pdENhdGVnb3JpZXNHcmlkKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgJCgnYm9keScpLm9uKCdjbGljaycsIHRoaXMuY2F0ZWdvcnlHcmlkSXRlbVNlbGVjdG9yLCBmdW5jdGlvbiBpbml0aWxhaXplR3JpZEJvZHlDbGljayhldmVudCkge1xuICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgY29uc3QgcmVmQ2F0ZWdvcnkgPSAkKHRoaXMpLmRhdGEoJ2NhdGVnb3J5LXJlZicpO1xuXG4gICAgICAvLyBJbiBjYXNlIHdlIGhhdmUgc29tZSB0YWdzIHdlIG5lZWQgdG8gcmVzZXQgaXQgIVxuICAgICAgaWYgKHNlbGYuY3VycmVudFRhZ3NMaXN0Lmxlbmd0aCkge1xuICAgICAgICBzZWxmLnBzdGFnZ2VySW5wdXQucmVzZXRUYWdzKGZhbHNlKTtcbiAgICAgICAgc2VsZi5jdXJyZW50VGFnc0xpc3QgPSBbXTtcbiAgICAgIH1cbiAgICAgIGNvbnN0IG1lbnVDYXRlZ29yeVRvVHJpZ2dlciA9ICQoYCR7c2VsZi5jYXRlZ29yeUl0ZW1TZWxlY3Rvcn1bZGF0YS1jYXRlZ29yeS1yZWY9XCIke3JlZkNhdGVnb3J5fVwiXWApO1xuXG4gICAgICBpZiAoIW1lbnVDYXRlZ29yeVRvVHJpZ2dlci5sZW5ndGgpIHtcbiAgICAgICAgY29uc29sZS53YXJuKGBObyBjYXRlZ29yeSB3aXRoIHJlZiAoJHtyZWZDYXRlZ29yeX0pIHNlZW1zIHRvIGV4aXN0IWApO1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICB9XG5cbiAgICAgIC8vIEhpZGUgY3VycmVudCBjYXRlZ29yeSBncmlkXG4gICAgICBpZiAoc2VsZi5pc0NhdGVnb3J5R3JpZERpc3BsYXllZCA9PT0gdHJ1ZSkge1xuICAgICAgICAkKHNlbGYuY2F0ZWdvcnlHcmlkU2VsZWN0b3IpLmZhZGVPdXQoKTtcbiAgICAgICAgc2VsZi5pc0NhdGVnb3J5R3JpZERpc3BsYXllZCA9IGZhbHNlO1xuICAgICAgfVxuXG4gICAgICAvLyBUcmlnZ2VyIGNsaWNrIG9uIHJpZ2h0IGNhdGVnb3J5XG4gICAgICAkKGAke3NlbGYuY2F0ZWdvcnlJdGVtU2VsZWN0b3J9W2RhdGEtY2F0ZWdvcnktcmVmPVwiJHtyZWZDYXRlZ29yeX1cIl1gKS5jbGljaygpO1xuICAgICAgcmV0dXJuIHRydWU7XG4gICAgfSk7XG4gIH1cblxuICBpbml0Q3VycmVudERpc3BsYXkoKSB7XG4gICAgdGhpcy5jdXJyZW50RGlzcGxheSA9IHRoaXMuY3VycmVudERpc3BsYXkgPT09ICcnID8gdGhpcy5ESVNQTEFZX0xJU1QgOiB0aGlzLkRJU1BMQVlfR1JJRDtcbiAgfVxuXG4gIGluaXRTb3J0aW5nRHJvcGRvd24oKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICBzZWxmLmN1cnJlbnRTb3J0aW5nID0gJCh0aGlzLm1vZHVsZVNvcnRpbmdEcm9wZG93blNlbGVjdG9yKS5maW5kKCc6Y2hlY2tlZCcpLmF0dHIoJ3ZhbHVlJyk7XG4gICAgaWYgKCFzZWxmLmN1cnJlbnRTb3J0aW5nKSB7XG4gICAgICBzZWxmLmN1cnJlbnRTb3J0aW5nID0gJ2FjY2Vzcy1kZXNjJztcbiAgICB9XG5cbiAgICAkKCdib2R5Jykub24oXG4gICAgICAnY2hhbmdlJyxcbiAgICAgIHNlbGYubW9kdWxlU29ydGluZ0Ryb3Bkb3duU2VsZWN0b3IsXG4gICAgICBmdW5jdGlvbiBpbml0aWFsaXplQm9keVNvcnRpbmdDaGFuZ2UoKSB7XG4gICAgICAgIHNlbGYuY3VycmVudFNvcnRpbmcgPSAkKHRoaXMpLmZpbmQoJzpjaGVja2VkJykuYXR0cigndmFsdWUnKTtcbiAgICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgICB9XG4gICAgKTtcbiAgfVxuXG4gIGRvQnVsa0FjdGlvbihyZXF1ZXN0ZWRCdWxrQWN0aW9uKSB7XG4gICAgLy8gVGhpcyBvYmplY3QgaXMgdXNlZCB0byBjaGVjayBpZiByZXF1ZXN0ZWQgYnVsa0FjdGlvbiBpcyBhdmFpbGFibGUgYW5kIGdpdmUgcHJvcGVyXG4gICAgLy8gdXJsIHNlZ21lbnQgdG8gYmUgY2FsbGVkIGZvciBpdFxuICAgIGNvbnN0IGZvcmNlRGVsZXRpb24gPSAkKCcjZm9yY2VfYnVsa19kZWxldGlvbicpLnByb3AoJ2NoZWNrZWQnKTtcblxuICAgIGNvbnN0IGJ1bGtBY3Rpb25Ub1VybCA9IHtcbiAgICAgICdidWxrLXVuaW5zdGFsbCc6ICd1bmluc3RhbGwnLFxuICAgICAgJ2J1bGstZGlzYWJsZSc6ICdkaXNhYmxlJyxcbiAgICAgICdidWxrLWVuYWJsZSc6ICdlbmFibGUnLFxuICAgICAgJ2J1bGstZGlzYWJsZS1tb2JpbGUnOiAnZGlzYWJsZV9tb2JpbGUnLFxuICAgICAgJ2J1bGstZW5hYmxlLW1vYmlsZSc6ICdlbmFibGVfbW9iaWxlJyxcbiAgICAgICdidWxrLXJlc2V0JzogJ3Jlc2V0JyxcbiAgICB9O1xuXG4gICAgLy8gTm90ZSBubyBncmlkIHNlbGVjdG9yIHVzZWQgeWV0IHNpbmNlIHdlIGRvIG5vdCBuZWVkZWQgaXQgYXQgZGV2IHRpbWVcbiAgICAvLyBNYXliZSB1c2VmdWwgdG8gaW1wbGVtZW50IHRoaXMga2luZCBvZiB0aGluZ3MgbGF0ZXIgaWYgaW50ZW5kZWQgdG9cbiAgICAvLyB1c2UgdGhpcyBmdW5jdGlvbmFsaXR5IGVsc2V3aGVyZSBidXQgXCJtYW5hZ2UgbXkgbW9kdWxlXCIgc2VjdGlvblxuICAgIGlmICh0eXBlb2YgYnVsa0FjdGlvblRvVXJsW3JlcXVlc3RlZEJ1bGtBY3Rpb25dID09PSAndW5kZWZpbmVkJykge1xuICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogd2luZG93LnRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snQnVsayBBY3Rpb24gLSBSZXF1ZXN0IG5vdCBmb3VuZCddLnJlcGxhY2UoJ1sxXScsIHJlcXVlc3RlZEJ1bGtBY3Rpb24pfSk7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuXG4gICAgLy8gTG9vcCBvdmVyIGFsbCBjaGVja2VkIGJ1bGsgY2hlY2tib3hlc1xuICAgIGNvbnN0IGJ1bGtBY3Rpb25TZWxlY3RlZFNlbGVjdG9yID0gdGhpcy5nZXRCdWxrQ2hlY2tib3hlc0NoZWNrZWRTZWxlY3RvcigpO1xuICAgIGNvbnN0IGJ1bGtNb2R1bGVBY3Rpb24gPSBidWxrQWN0aW9uVG9VcmxbcmVxdWVzdGVkQnVsa0FjdGlvbl07XG5cbiAgICBpZiAoJChidWxrQWN0aW9uU2VsZWN0ZWRTZWxlY3RvcikubGVuZ3RoIDw9IDApIHtcbiAgICAgIGNvbnNvbGUud2Fybih3aW5kb3cudHJhbnNsYXRlX2phdmFzY3JpcHRzWydCdWxrIEFjdGlvbiAtIE9uZSBtb2R1bGUgbWluaW11bSddKTtcbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9XG5cbiAgICBjb25zdCBtb2R1bGVzQWN0aW9ucyA9IFtdO1xuICAgIGxldCBtb2R1bGVUZWNoTmFtZTtcbiAgICAkKGJ1bGtBY3Rpb25TZWxlY3RlZFNlbGVjdG9yKS5lYWNoKGZ1bmN0aW9uIGJ1bGtBY3Rpb25TZWxlY3RvcigpIHtcbiAgICAgIG1vZHVsZVRlY2hOYW1lID0gJCh0aGlzKS5kYXRhKCd0ZWNoLW5hbWUnKTtcbiAgICAgIG1vZHVsZXNBY3Rpb25zLnB1c2goe1xuICAgICAgICB0ZWNoTmFtZTogbW9kdWxlVGVjaE5hbWUsXG4gICAgICAgIGFjdGlvbk1lbnVPYmo6ICQodGhpcykuY2xvc2VzdCgnLm1vZHVsZS1jaGVja2JveC1idWxrLWxpc3QnKS5uZXh0KCksXG4gICAgICB9KTtcbiAgICB9KTtcblxuICAgIHRoaXMucGVyZm9ybU1vZHVsZXNBY3Rpb24obW9kdWxlc0FjdGlvbnMsIGJ1bGtNb2R1bGVBY3Rpb24sIGZvcmNlRGVsZXRpb24pO1xuXG4gICAgcmV0dXJuIHRydWU7XG4gIH1cblxuICBwZXJmb3JtTW9kdWxlc0FjdGlvbihtb2R1bGVzQWN0aW9ucywgYnVsa01vZHVsZUFjdGlvbiwgZm9yY2VEZWxldGlvbikge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGlmICh0eXBlb2Ygc2VsZi5tb2R1bGVDYXJkQ29udHJvbGxlciA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAvL0ZpcnN0IGxldCdzIGZpbHRlciBtb2R1bGVzIHRoYXQgY2FuJ3QgcGVyZm9ybSB0aGlzIGFjdGlvblxuICAgIGxldCBhY3Rpb25NZW51TGlua3MgPSBmaWx0ZXJBbGxvd2VkQWN0aW9ucyhtb2R1bGVzQWN0aW9ucyk7XG4gICAgaWYgKCFhY3Rpb25NZW51TGlua3MubGVuZ3RoKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgbGV0IG1vZHVsZXNSZXF1ZXN0ZWRDb3VudGRvd24gPSBhY3Rpb25NZW51TGlua3MubGVuZ3RoIC0gMTtcbiAgICBsZXQgc3Bpbm5lck9iaiA9ICQoXCI8YnV0dG9uIGNsYXNzPVxcXCJidG4tcHJpbWFyeS1yZXZlcnNlIG9uY2xpY2sgdW5iaW5kIHNwaW5uZXIgXFxcIj48L2J1dHRvbj5cIik7XG4gICAgaWYgKGFjdGlvbk1lbnVMaW5rcy5sZW5ndGggPiAxKSB7XG4gICAgICAvL0xvb3AgdGhyb3VnaCBhbGwgdGhlIG1vZHVsZXMgZXhjZXB0IHRoZSBsYXN0IG9uZSB3aGljaCB3YWl0cyBmb3Igb3RoZXJcbiAgICAgIC8vcmVxdWVzdHMgYW5kIHRoZW4gY2FsbCBpdHMgcmVxdWVzdCB3aXRoIGNhY2hlIGNsZWFyIGVuYWJsZWRcbiAgICAgICQuZWFjaChhY3Rpb25NZW51TGlua3MsIGZ1bmN0aW9uIGJ1bGtNb2R1bGVzTG9vcChpbmRleCwgYWN0aW9uTWVudUxpbmspIHtcbiAgICAgICAgaWYgKGluZGV4ID49IGFjdGlvbk1lbnVMaW5rcy5sZW5ndGggLSAxKSB7XG4gICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIHJlcXVlc3RNb2R1bGVBY3Rpb24oYWN0aW9uTWVudUxpbmssIHRydWUsIGNvdW50ZG93bk1vZHVsZXNSZXF1ZXN0KTtcbiAgICAgIH0pO1xuICAgICAgLy9EaXNwbGF5IGEgc3Bpbm5lciBmb3IgdGhlIGxhc3QgbW9kdWxlXG4gICAgICBjb25zdCBsYXN0TWVudUxpbmsgPSBhY3Rpb25NZW51TGlua3NbYWN0aW9uTWVudUxpbmtzLmxlbmd0aCAtIDFdO1xuICAgICAgY29uc3QgYWN0aW9uTWVudU9iaiA9IGxhc3RNZW51TGluay5jbG9zZXN0KHNlbGYubW9kdWxlQ2FyZENvbnRyb2xsZXIubW9kdWxlSXRlbUFjdGlvbnNTZWxlY3Rvcik7XG4gICAgICBhY3Rpb25NZW51T2JqLmhpZGUoKTtcbiAgICAgIGFjdGlvbk1lbnVPYmouYWZ0ZXIoc3Bpbm5lck9iaik7XG4gICAgfSBlbHNlIHtcbiAgICAgIHJlcXVlc3RNb2R1bGVBY3Rpb24oYWN0aW9uTWVudUxpbmtzWzBdKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiByZXF1ZXN0TW9kdWxlQWN0aW9uKGFjdGlvbk1lbnVMaW5rLCBkaXNhYmxlQ2FjaGVDbGVhciwgcmVxdWVzdEVuZENhbGxiYWNrKSB7XG4gICAgICBzZWxmLm1vZHVsZUNhcmRDb250cm9sbGVyLl9yZXF1ZXN0VG9Db250cm9sbGVyKFxuICAgICAgICBidWxrTW9kdWxlQWN0aW9uLFxuICAgICAgICBhY3Rpb25NZW51TGluayxcbiAgICAgICAgZm9yY2VEZWxldGlvbixcbiAgICAgICAgZGlzYWJsZUNhY2hlQ2xlYXIsXG4gICAgICAgIHJlcXVlc3RFbmRDYWxsYmFja1xuICAgICAgKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBjb3VudGRvd25Nb2R1bGVzUmVxdWVzdCgpIHtcbiAgICAgIG1vZHVsZXNSZXF1ZXN0ZWRDb3VudGRvd24tLTtcbiAgICAgIC8vTm93IHRoYXQgYWxsIG90aGVyIG1vZHVsZXMgaGF2ZSBwZXJmb3JtZWQgdGhlaXIgYWN0aW9uIFdJVEhPVVQgY2FjaGUgY2xlYXIsIHdlXG4gICAgICAvL2NhbiByZXF1ZXN0IHRoZSBsYXN0IG1vZHVsZSByZXF1ZXN0IFdJVEggY2FjaGUgY2xlYXJcbiAgICAgIGlmIChtb2R1bGVzUmVxdWVzdGVkQ291bnRkb3duIDw9IDApIHtcbiAgICAgICAgaWYgKHNwaW5uZXJPYmopIHtcbiAgICAgICAgICBzcGlubmVyT2JqLnJlbW92ZSgpO1xuICAgICAgICAgIHNwaW5uZXJPYmogPSBudWxsO1xuICAgICAgICB9XG5cbiAgICAgICAgY29uc3QgbGFzdE1lbnVMaW5rID0gYWN0aW9uTWVudUxpbmtzW2FjdGlvbk1lbnVMaW5rcy5sZW5ndGggLSAxXTtcbiAgICAgICAgY29uc3QgYWN0aW9uTWVudU9iaiA9IGxhc3RNZW51TGluay5jbG9zZXN0KHNlbGYubW9kdWxlQ2FyZENvbnRyb2xsZXIubW9kdWxlSXRlbUFjdGlvbnNTZWxlY3Rvcik7XG4gICAgICAgIGFjdGlvbk1lbnVPYmouZmFkZUluKCk7XG4gICAgICAgIHJlcXVlc3RNb2R1bGVBY3Rpb24obGFzdE1lbnVMaW5rKTtcbiAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBmaWx0ZXJBbGxvd2VkQWN0aW9ucyhtb2R1bGVzQWN0aW9ucykge1xuICAgICAgbGV0IGFjdGlvbk1lbnVMaW5rcyA9IFtdO1xuICAgICAgbGV0IGFjdGlvbk1lbnVMaW5rO1xuICAgICAgJC5lYWNoKG1vZHVsZXNBY3Rpb25zLCBmdW5jdGlvbiBmaWx0ZXJBbGxvd2VkTW9kdWxlcyhpbmRleCwgbW9kdWxlRGF0YSkge1xuICAgICAgICBhY3Rpb25NZW51TGluayA9ICQoXG4gICAgICAgICAgc2VsZi5tb2R1bGVDYXJkQ29udHJvbGxlci5tb2R1bGVBY3Rpb25NZW51TGlua1NlbGVjdG9yICsgYnVsa01vZHVsZUFjdGlvbixcbiAgICAgICAgICBtb2R1bGVEYXRhLmFjdGlvbk1lbnVPYmpcbiAgICAgICAgKTtcbiAgICAgICAgaWYgKGFjdGlvbk1lbnVMaW5rLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICBhY3Rpb25NZW51TGlua3MucHVzaChhY3Rpb25NZW51TGluayk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogd2luZG93LnRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snQnVsayBBY3Rpb24gLSBSZXF1ZXN0IG5vdCBhdmFpbGFibGUgZm9yIG1vZHVsZSddXG4gICAgICAgICAgICAgIC5yZXBsYWNlKCdbMV0nLCBidWxrTW9kdWxlQWN0aW9uKVxuICAgICAgICAgICAgICAucmVwbGFjZSgnWzJdJywgbW9kdWxlRGF0YS50ZWNoTmFtZSl9KTtcbiAgICAgICAgfVxuICAgICAgfSk7XG5cbiAgICAgIHJldHVybiBhY3Rpb25NZW51TGlua3M7XG4gICAgfVxuICB9XG5cbiAgaW5pdEFjdGlvbkJ1dHRvbnMoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgJCgnYm9keScpLm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgIHNlbGYubW9kdWxlSW5zdGFsbEJ0blNlbGVjdG9yLFxuICAgICAgZnVuY3Rpb24gaW5pdGlhbGl6ZUFjdGlvbkJ1dHRvbnNDbGljayhldmVudCkge1xuICAgICAgICBjb25zdCAkdGhpcyA9ICQodGhpcyk7XG4gICAgICAgIGNvbnN0ICRuZXh0ID0gJCgkdGhpcy5uZXh0KCkpO1xuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICR0aGlzLmhpZGUoKTtcbiAgICAgICAgJG5leHQuc2hvdygpO1xuXG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgdXJsOiAkdGhpcy5kYXRhKCd1cmwnKSxcbiAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgICB9KS5kb25lKCgpID0+IHtcbiAgICAgICAgICAkbmV4dC5mYWRlT3V0KCk7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgICk7XG5cbiAgICAvLyBcIlVwZ3JhZGUgQWxsXCIgYnV0dG9uIGhhbmRsZXJcbiAgICAkKCdib2R5Jykub24oJ2NsaWNrJywgc2VsZi51cGdyYWRlQWxsU291cmNlLCAoZXZlbnQpID0+IHtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgIGlmICgkKHNlbGYudXBncmFkZUFsbFRhcmdldHMpLmxlbmd0aCA8PSAwKSB7XG4gICAgICAgIGNvbnNvbGUud2Fybih3aW5kb3cudHJhbnNsYXRlX2phdmFzY3JpcHRzWydVcGdyYWRlIEFsbCBBY3Rpb24gLSBPbmUgbW9kdWxlIG1pbmltdW0nXSk7XG4gICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgIH1cblxuICAgICAgY29uc3QgbW9kdWxlc0FjdGlvbnMgPSBbXTtcbiAgICAgIGxldCBtb2R1bGVUZWNoTmFtZTtcbiAgICAgICQoc2VsZi51cGdyYWRlQWxsVGFyZ2V0cykuZWFjaChmdW5jdGlvbiBidWxrQWN0aW9uU2VsZWN0b3IoKSB7XG4gICAgICAgIGNvbnN0IG1vZHVsZUl0ZW1MaXN0ID0gJCh0aGlzKS5jbG9zZXN0KCcubW9kdWxlLWl0ZW0tbGlzdCcpO1xuICAgICAgICBtb2R1bGVUZWNoTmFtZSA9IG1vZHVsZUl0ZW1MaXN0LmRhdGEoJ3RlY2gtbmFtZScpO1xuICAgICAgICBtb2R1bGVzQWN0aW9ucy5wdXNoKHtcbiAgICAgICAgICB0ZWNoTmFtZTogbW9kdWxlVGVjaE5hbWUsXG4gICAgICAgICAgYWN0aW9uTWVudU9iajogJCgnLm1vZHVsZS1hY3Rpb25zJywgbW9kdWxlSXRlbUxpc3QpLFxuICAgICAgICB9KTtcbiAgICAgIH0pO1xuXG4gICAgICB0aGlzLnBlcmZvcm1Nb2R1bGVzQWN0aW9uKG1vZHVsZXNBY3Rpb25zLCAndXBncmFkZScpO1xuXG4gICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9KTtcbiAgfVxuXG4gIGluaXRDYXRlZ29yeVNlbGVjdCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBib2R5ID0gJCgnYm9keScpO1xuICAgIGJvZHkub24oXG4gICAgICAnY2xpY2snLFxuICAgICAgc2VsZi5jYXRlZ29yeUl0ZW1TZWxlY3RvcixcbiAgICAgIGZ1bmN0aW9uIGluaXRpYWxpemVDYXRlZ29yeVNlbGVjdENsaWNrKCkge1xuICAgICAgICAvLyBHZXQgZGF0YSBmcm9tIGxpIERPTSBpbnB1dFxuICAgICAgICBzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeSA9ICQodGhpcykuZGF0YSgnY2F0ZWdvcnktcmVmJyk7XG4gICAgICAgIHNlbGYuY3VycmVudFJlZkNhdGVnb3J5ID0gc2VsZi5jdXJyZW50UmVmQ2F0ZWdvcnkgPyBTdHJpbmcoc2VsZi5jdXJyZW50UmVmQ2F0ZWdvcnkpLnRvTG93ZXJDYXNlKCkgOiBudWxsO1xuICAgICAgICAvLyBDaGFuZ2UgZHJvcGRvd24gbGFiZWwgdG8gc2V0IGl0IHRvIHRoZSBjdXJyZW50IGNhdGVnb3J5J3MgZGlzcGxheW5hbWVcbiAgICAgICAgJChzZWxmLmNhdGVnb3J5U2VsZWN0b3JMYWJlbFNlbGVjdG9yKS50ZXh0KCQodGhpcykuZGF0YSgnY2F0ZWdvcnktZGlzcGxheS1uYW1lJykpO1xuICAgICAgICAkKHNlbGYuY2F0ZWdvcnlSZXNldEJ0blNlbGVjdG9yKS5zaG93KCk7XG4gICAgICAgIHNlbGYudXBkYXRlTW9kdWxlVmlzaWJpbGl0eSgpO1xuICAgICAgfVxuICAgICk7XG5cbiAgICBib2R5Lm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgIHNlbGYuY2F0ZWdvcnlSZXNldEJ0blNlbGVjdG9yLFxuICAgICAgZnVuY3Rpb24gaW5pdGlhbGl6ZUNhdGVnb3J5UmVzZXRCdXR0b25DbGljaygpIHtcbiAgICAgICAgY29uc3QgcmF3VGV4dCA9ICQoc2VsZi5jYXRlZ29yeVNlbGVjdG9yKS5hdHRyKCdhcmlhLWxhYmVsbGVkYnknKTtcbiAgICAgICAgY29uc3QgdXBwZXJGaXJzdExldHRlciA9IHJhd1RleHQuY2hhckF0KDApLnRvVXBwZXJDYXNlKCk7XG4gICAgICAgIGNvbnN0IHJlbW92ZWRGaXJzdExldHRlciA9IHJhd1RleHQuc2xpY2UoMSk7XG4gICAgICAgIGNvbnN0IG9yaWdpbmFsVGV4dCA9IHVwcGVyRmlyc3RMZXR0ZXIgKyByZW1vdmVkRmlyc3RMZXR0ZXI7XG5cbiAgICAgICAgJChzZWxmLmNhdGVnb3J5U2VsZWN0b3JMYWJlbFNlbGVjdG9yKS50ZXh0KG9yaWdpbmFsVGV4dCk7XG4gICAgICAgICQodGhpcykuaGlkZSgpO1xuICAgICAgICBzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeSA9IG51bGw7XG4gICAgICAgIHNlbGYudXBkYXRlTW9kdWxlVmlzaWJpbGl0eSgpO1xuICAgICAgfVxuICAgICk7XG4gIH1cblxuICBpbml0U2VhcmNoQmxvY2soKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgc2VsZi5wc3RhZ2dlcklucHV0ID0gJCgnI21vZHVsZS1zZWFyY2gtYmFyJykucHN0YWdnZXIoe1xuICAgICAgb25UYWdzQ2hhbmdlZDogKHRhZ0xpc3QpID0+IHtcbiAgICAgICAgc2VsZi5jdXJyZW50VGFnc0xpc3QgPSB0YWdMaXN0O1xuICAgICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICAgIH0sXG4gICAgICBvblJlc2V0VGFnczogKCkgPT4ge1xuICAgICAgICBzZWxmLmN1cnJlbnRUYWdzTGlzdCA9IFtdO1xuICAgICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICAgIH0sXG4gICAgICBpbnB1dFBsYWNlaG9sZGVyOiB3aW5kb3cudHJhbnNsYXRlX2phdmFzY3JpcHRzWydTZWFyY2ggLSBwbGFjZWhvbGRlciddLFxuICAgICAgY2xvc2luZ0Nyb3NzOiB0cnVlLFxuICAgICAgY29udGV4dDogc2VsZixcbiAgICB9KTtcblxuICAgICQoJ2JvZHknKS5vbignY2xpY2snLCAnLm1vZHVsZS1hZGRvbnMtc2VhcmNoLWxpbmsnLCAoZXZlbnQpID0+IHtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgIHdpbmRvdy5vcGVuKCQodGhpcykuYXR0cignaHJlZicpLCAnX2JsYW5rJyk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbGl6ZSBkaXNwbGF5IHN3aXRjaGluZyBiZXR3ZWVuIExpc3Qgb3IgR3JpZFxuICAgKi9cbiAgaW5pdFNvcnRpbmdEaXNwbGF5U3dpdGNoKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgJCgnYm9keScpLm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgICcubW9kdWxlLXNvcnQtc3dpdGNoJyxcbiAgICAgIGZ1bmN0aW9uIHN3aXRjaFNvcnQoKSB7XG4gICAgICAgIGNvbnN0IHN3aXRjaFRvID0gJCh0aGlzKS5kYXRhKCdzd2l0Y2gnKTtcbiAgICAgICAgY29uc3QgaXNBbHJlYWR5RGlzcGxheWVkID0gJCh0aGlzKS5oYXNDbGFzcygnYWN0aXZlLWRpc3BsYXknKTtcbiAgICAgICAgaWYgKHR5cGVvZiBzd2l0Y2hUbyAhPT0gJ3VuZGVmaW5lZCcgJiYgaXNBbHJlYWR5RGlzcGxheWVkID09PSBmYWxzZSkge1xuICAgICAgICAgIHNlbGYuc3dpdGNoU29ydGluZ0Rpc3BsYXlUbyhzd2l0Y2hUbyk7XG4gICAgICAgICAgc2VsZi5jdXJyZW50RGlzcGxheSA9IHN3aXRjaFRvO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgKTtcbiAgfVxuXG4gIHN3aXRjaFNvcnRpbmdEaXNwbGF5VG8oc3dpdGNoVG8pIHtcbiAgICBpZiAoc3dpdGNoVG8gIT09IHRoaXMuRElTUExBWV9HUklEICYmIHN3aXRjaFRvICE9PSB0aGlzLkRJU1BMQVlfTElTVCkge1xuICAgICAgY29uc29sZS5lcnJvcihgQ2FuJ3Qgc3dpdGNoIHRvIHVuZGVmaW5lZCBkaXNwbGF5IHByb3BlcnR5IFwiJHtzd2l0Y2hUb31cImApO1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgICQoJy5tb2R1bGUtc29ydC1zd2l0Y2gnKS5yZW1vdmVDbGFzcygnbW9kdWxlLXNvcnQtYWN0aXZlJyk7XG4gICAgJChgI21vZHVsZS1zb3J0LSR7c3dpdGNoVG99YCkuYWRkQ2xhc3MoJ21vZHVsZS1zb3J0LWFjdGl2ZScpO1xuICAgIHRoaXMuY3VycmVudERpc3BsYXkgPSBzd2l0Y2hUbztcbiAgICB0aGlzLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgfVxuXG4gIGluaXRpYWxpemVTZWVNb3JlKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgJChgJHtzZWxmLm1vZHVsZVNob3J0TGlzdH0gJHtzZWxmLnNlZU1vcmVTZWxlY3Rvcn1gKS5vbignY2xpY2snLCBmdW5jdGlvbiBzZWVNb3JlKCkge1xuICAgICAgc2VsZi5jdXJyZW50Q2F0ZWdvcnlEaXNwbGF5WyQodGhpcykuZGF0YSgnY2F0ZWdvcnknKV0gPSB0cnVlO1xuICAgICAgJCh0aGlzKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgICAkKHRoaXMpLmNsb3Nlc3Qoc2VsZi5tb2R1bGVTaG9ydExpc3QpLmZpbmQoc2VsZi5zZWVMZXNzU2VsZWN0b3IpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAgIHNlbGYudXBkYXRlTW9kdWxlVmlzaWJpbGl0eSgpO1xuICAgIH0pO1xuXG4gICAgJChgJHtzZWxmLm1vZHVsZVNob3J0TGlzdH0gJHtzZWxmLnNlZUxlc3NTZWxlY3Rvcn1gKS5vbignY2xpY2snLCBmdW5jdGlvbiBzZWVNb3JlKCkge1xuICAgICAgc2VsZi5jdXJyZW50Q2F0ZWdvcnlEaXNwbGF5WyQodGhpcykuZGF0YSgnY2F0ZWdvcnknKV0gPSBmYWxzZTtcbiAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgJCh0aGlzKS5jbG9zZXN0KHNlbGYubW9kdWxlU2hvcnRMaXN0KS5maW5kKHNlbGYuc2VlTW9yZVNlbGVjdG9yKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICB9KTtcbiAgfVxuXG4gIHVwZGF0ZVRvdGFsUmVzdWx0cygpIHtcbiAgICBjb25zdCByZXBsYWNlRmlyc3RXb3JkQnkgPSAoZWxlbWVudCwgdmFsdWUpID0+IHtcbiAgICAgIGNvbnN0IGV4cGxvZGVkVGV4dCA9IGVsZW1lbnQudGV4dCgpLnNwbGl0KCcgJyk7XG4gICAgICBleHBsb2RlZFRleHRbMF0gPSB2YWx1ZTtcbiAgICAgIGVsZW1lbnQudGV4dChleHBsb2RlZFRleHQuam9pbignICcpKTtcbiAgICB9O1xuXG4gICAgLy8gSWYgdGhlcmUgYXJlIHNvbWUgc2hvcnRsaXN0OiBlYWNoIHNob3J0bGlzdCBjb3VudCB0aGUgbW9kdWxlcyBvbiB0aGUgbmV4dCBjb250YWluZXIuXG4gICAgY29uc3QgJHNob3J0TGlzdHMgPSAkKCcubW9kdWxlLXNob3J0LWxpc3QnKTtcbiAgICBpZiAoJHNob3J0TGlzdHMubGVuZ3RoID4gMCkge1xuICAgICAgJHNob3J0TGlzdHMuZWFjaChmdW5jdGlvbiBzaG9ydExpc3RzKCkge1xuICAgICAgICBjb25zdCAkdGhpcyA9ICQodGhpcyk7XG4gICAgICAgIHJlcGxhY2VGaXJzdFdvcmRCeShcbiAgICAgICAgICAkdGhpcy5maW5kKCcubW9kdWxlLXNlYXJjaC1yZXN1bHQtd29yZGluZycpLFxuICAgICAgICAgICR0aGlzLm5leHQoJy5tb2R1bGVzLWxpc3QnKS5maW5kKCcubW9kdWxlLWl0ZW0nKS5sZW5ndGhcbiAgICAgICAgKTtcbiAgICAgIH0pO1xuXG4gICAgICAvLyBJZiB0aGVyZSBpcyBubyBzaG9ydGxpc3Q6IHRoZSB3b3JkaW5nIGRpcmVjdGx5IHVwZGF0ZSBmcm9tIHRoZSBvbmx5IG1vZHVsZSBjb250YWluZXIuXG4gICAgfSBlbHNlIHtcbiAgICAgIGNvbnN0IG1vZHVsZXNDb3VudCA9ICQoJy5tb2R1bGVzLWxpc3QnKS5maW5kKCcubW9kdWxlLWl0ZW0nKS5sZW5ndGg7XG4gICAgICByZXBsYWNlRmlyc3RXb3JkQnkoJCgnLm1vZHVsZS1zZWFyY2gtcmVzdWx0LXdvcmRpbmcnKSwgbW9kdWxlc0NvdW50KTtcblxuICAgICAgY29uc3Qgc2VsZWN0b3JUb1RvZ2dsZSA9IChzZWxmLmN1cnJlbnREaXNwbGF5ID09PSBzZWxmLkRJU1BMQVlfTElTVCkgP1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMuYWRkb25JdGVtTGlzdFNlbGVjdG9yIDpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmFkZG9uSXRlbUdyaWRTZWxlY3RvcjtcbiAgICAgICQoc2VsZWN0b3JUb1RvZ2dsZSkudG9nZ2xlKG1vZHVsZXNDb3VudCAhPT0gKHRoaXMubW9kdWxlc0xpc3QubGVuZ3RoIC8gMikpO1xuXG4gICAgICBpZiAobW9kdWxlc0NvdW50ID09PSAwKSB7XG4gICAgICAgICQoJy5tb2R1bGUtYWRkb25zLXNlYXJjaC1saW5rJykuYXR0cihcbiAgICAgICAgICAnaHJlZicsXG4gICAgICAgICAgYCR7dGhpcy5iYXNlQWRkb25zVXJsfXNlYXJjaC5waHA/c2VhcmNoX3F1ZXJ5PSR7ZW5jb2RlVVJJQ29tcG9uZW50KHRoaXMuY3VycmVudFRhZ3NMaXN0LmpvaW4oJyAnKSl9YFxuICAgICAgICApO1xuICAgICAgfVxuICAgIH1cbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBBZG1pbk1vZHVsZUNvbnRyb2xsZXI7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9tb2R1bGUvY29udHJvbGxlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBNb2R1bGUgQWRtaW4gUGFnZSBMb2FkZXIuXG4gKiBAY29uc3RydWN0b3JcbiAqL1xuY2xhc3MgTW9kdWxlTG9hZGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgTW9kdWxlTG9hZGVyLmhhbmRsZUltcG9ydCgpO1xuICAgIE1vZHVsZUxvYWRlci5oYW5kbGVFdmVudHMoKTtcbiAgfVxuXG4gIHN0YXRpYyBoYW5kbGVJbXBvcnQoKSB7XG4gICAgY29uc3QgbW9kdWxlSW1wb3J0ID0gJCgnI21vZHVsZS1pbXBvcnQnKTtcbiAgICBtb2R1bGVJbXBvcnQuY2xpY2soKCkgPT4ge1xuICAgICAgbW9kdWxlSW1wb3J0LmFkZENsYXNzKCdvbmNsaWNrJywgMjUwLCB2YWxpZGF0ZSk7XG4gICAgfSk7XG5cbiAgICBmdW5jdGlvbiB2YWxpZGF0ZSgpIHtcbiAgICAgIHNldFRpbWVvdXQoXG4gICAgICAgICgpID0+IHtcbiAgICAgICAgICBtb2R1bGVJbXBvcnQucmVtb3ZlQ2xhc3MoJ29uY2xpY2snKTtcbiAgICAgICAgICBtb2R1bGVJbXBvcnQuYWRkQ2xhc3MoJ3ZhbGlkYXRlJywgNDUwLCBjYWxsYmFjayk7XG4gICAgICAgIH0sXG4gICAgICAgIDIyNTBcbiAgICAgICk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGNhbGxiYWNrKCkge1xuICAgICAgc2V0VGltZW91dChcbiAgICAgICAgKCkgPT4ge1xuICAgICAgICAgIG1vZHVsZUltcG9ydC5yZW1vdmVDbGFzcygndmFsaWRhdGUnKTtcbiAgICAgICAgfSxcbiAgICAgICAgMTI1MFxuICAgICAgKTtcbiAgICB9XG4gIH1cblxuICBzdGF0aWMgaGFuZGxlRXZlbnRzKCkge1xuICAgICQoJ2JvZHknKS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICAnYS5tb2R1bGUtcmVhZC1tb3JlLWdyaWQtYnRuLCBhLm1vZHVsZS1yZWFkLW1vcmUtbGlzdC1idG4nLFxuICAgICAgKGV2ZW50KSA9PiB7XG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIGNvbnN0IG1vZHVsZVBvcHBpbiA9ICQoZXZlbnQudGFyZ2V0KS5kYXRhKCd0YXJnZXQnKTtcblxuICAgICAgICAkLmdldChldmVudC50YXJnZXQuaHJlZiwgKGRhdGEpID0+IHtcbiAgICAgICAgICAkKG1vZHVsZVBvcHBpbikuaHRtbChkYXRhKTtcbiAgICAgICAgICAkKG1vZHVsZVBvcHBpbikubW9kYWwoKTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgKTtcbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBNb2R1bGVMb2FkZXI7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9tb2R1bGUvbG9hZGVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IE1vZHVsZUNhcmQgZnJvbSAnLi4vLi4vY29tcG9uZW50cy9tb2R1bGUtY2FyZCc7XG5pbXBvcnQgQWRtaW5Nb2R1bGVDb250cm9sbGVyIGZyb20gJy4vY29udHJvbGxlcic7XG5pbXBvcnQgTW9kdWxlTG9hZGVyIGZyb20gJy4vbG9hZGVyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgY29uc3QgbW9kdWxlQ2FyZENvbnRyb2xsZXIgPSBuZXcgTW9kdWxlQ2FyZCgpO1xuICBuZXcgTW9kdWxlTG9hZGVyKCk7XG4gIG5ldyBBZG1pbk1vZHVsZUNvbnRyb2xsZXIobW9kdWxlQ2FyZENvbnRyb2xsZXIpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9tb2R1bGUvaW5kZXguanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbnZhciBCT0V2ZW50ID0ge1xuICBvbjogZnVuY3Rpb24oZXZlbnROYW1lLCBjYWxsYmFjaywgY29udGV4dCkge1xuXG4gICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcihldmVudE5hbWUsIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICBpZiAodHlwZW9mIGNvbnRleHQgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgIGNhbGxiYWNrLmNhbGwoY29udGV4dCwgZXZlbnQpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgY2FsbGJhY2soZXZlbnQpO1xuICAgICAgfVxuICAgIH0pO1xuICB9LFxuXG4gIGVtaXRFdmVudDogZnVuY3Rpb24oZXZlbnROYW1lLCBldmVudFR5cGUpIHtcbiAgICB2YXIgX2V2ZW50ID0gZG9jdW1lbnQuY3JlYXRlRXZlbnQoZXZlbnRUeXBlKTtcbiAgICAvLyB0cnVlIHZhbHVlcyBzdGFuZCBmb3I6IGNhbiBidWJibGUsIGFuZCBpcyBjYW5jZWxsYWJsZVxuICAgIF9ldmVudC5pbml0RXZlbnQoZXZlbnROYW1lLCB0cnVlLCB0cnVlKTtcbiAgICBkb2N1bWVudC5kaXNwYXRjaEV2ZW50KF9ldmVudCk7XG4gIH1cbn07XG5cblxuLyoqXG4gKiBDbGFzcyBpcyByZXNwb25zaWJsZSBmb3IgaGFuZGxpbmcgTW9kdWxlIENhcmQgYmVoYXZpb3JcbiAqXG4gKiBUaGlzIGlzIGEgcG9ydCBvZiBhZG1pbi1kZXYvdGhlbWVzL2RlZmF1bHQvanMvYnVuZGxlL21vZHVsZS9tb2R1bGVfY2FyZC5qc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBNb2R1bGVDYXJkIHtcblxuICBjb25zdHJ1Y3RvcigpIHtcbiAgICAvKiBTZWxlY3RvcnMgZm9yIG1vZHVsZSBhY3Rpb24gbGlua3MgKHVuaW5zdGFsbCwgcmVzZXQsIGV0Yy4uLikgdG8gYWRkIGEgY29uZmlybSBwb3BpbiAqL1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51Xyc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51SW5zdGFsbExpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2luc3RhbGwnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudUVuYWJsZUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2VuYWJsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51VW5pbnN0YWxsTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfdW5pbnN0YWxsJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfZGlzYWJsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51RW5hYmxlTW9iaWxlTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfZW5hYmxlX21vYmlsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51RGlzYWJsZU1vYmlsZUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2Rpc2FibGVfbW9iaWxlJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVSZXNldExpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X3Jlc2V0JztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVVcGRhdGVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV91cGdyYWRlJztcbiAgICB0aGlzLm1vZHVsZUl0ZW1MaXN0U2VsZWN0b3IgPSAnLm1vZHVsZS1pdGVtLWxpc3QnO1xuICAgIHRoaXMubW9kdWxlSXRlbUdyaWRTZWxlY3RvciA9ICcubW9kdWxlLWl0ZW0tZ3JpZCc7XG4gICAgdGhpcy5tb2R1bGVJdGVtQWN0aW9uc1NlbGVjdG9yID0gJy5tb2R1bGUtYWN0aW9ucyc7XG5cbiAgICAvKiBTZWxlY3RvcnMgb25seSBmb3IgbW9kYWwgYnV0dG9ucyAqL1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxEaXNhYmxlTGlua1NlbGVjdG9yID0gJ2EubW9kdWxlX2FjdGlvbl9tb2RhbF9kaXNhYmxlJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1vZGFsUmVzZXRMaW5rU2VsZWN0b3IgPSAnYS5tb2R1bGVfYWN0aW9uX21vZGFsX3Jlc2V0JztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1vZGFsVW5pbnN0YWxsTGlua1NlbGVjdG9yID0gJ2EubW9kdWxlX2FjdGlvbl9tb2RhbF91bmluc3RhbGwnO1xuICAgIHRoaXMuZm9yY2VEZWxldGlvbk9wdGlvbiA9ICcjZm9yY2VfZGVsZXRpb24nO1xuXG4gICAgdGhpcy5pbml0QWN0aW9uQnV0dG9ucygpO1xuICB9XG5cbiAgaW5pdEFjdGlvbkJ1dHRvbnMoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLmZvcmNlRGVsZXRpb25PcHRpb24sIGZ1bmN0aW9uICgpIHtcbiAgICAgIGNvbnN0IGJ0biA9ICQoc2VsZi5tb2R1bGVBY3Rpb25Nb2RhbFVuaW5zdGFsbExpbmtTZWxlY3RvciwgJChcImRpdi5tb2R1bGUtaXRlbS1saXN0W2RhdGEtdGVjaC1uYW1lPSdcIiArICQodGhpcykuYXR0cihcImRhdGEtdGVjaC1uYW1lXCIpICsgXCInXVwiKSk7XG4gICAgICBpZiAoJCh0aGlzKS5wcm9wKCdjaGVja2VkJykgPT09IHRydWUpIHtcbiAgICAgICAgYnRuLmF0dHIoJ2RhdGEtZGVsZXRpb24nLCAndHJ1ZScpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgYnRuLnJlbW92ZUF0dHIoJ2RhdGEtZGVsZXRpb24nKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudUluc3RhbGxMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIGlmICgkKFwiI21vZGFsLXByZXN0YXRydXN0XCIpLmxlbmd0aCkge1xuICAgICAgICAkKFwiI21vZGFsLXByZXN0YXRydXN0XCIpLm1vZGFsKCdoaWRlJyk7XG4gICAgICB9XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgnaW5zdGFsbCcsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ2luc3RhbGwnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdpbnN0YWxsJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51RW5hYmxlTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgnZW5hYmxlJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbignZW5hYmxlJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZW5hYmxlJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51VW5pbnN0YWxsTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgndW5pbnN0YWxsJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbigndW5pbnN0YWxsJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcigndW5pbnN0YWxsJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51RGlzYWJsZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ2Rpc2FibGUnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdkaXNhYmxlJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZGlzYWJsZScsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudUVuYWJsZU1vYmlsZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ2VuYWJsZV9tb2JpbGUnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdlbmFibGVfbW9iaWxlJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZW5hYmxlX21vYmlsZScsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudURpc2FibGVNb2JpbGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdkaXNhYmxlX21vYmlsZScsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ2Rpc2FibGVfbW9iaWxlJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZGlzYWJsZV9tb2JpbGUnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVSZXNldExpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ3Jlc2V0JywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbigncmVzZXQnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdyZXNldCcsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudVVwZGF0ZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ3VwZGF0ZScsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ3VwZGF0ZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ3VwZGF0ZScsICQodGhpcykpO1xuICAgIH0pO1xuXG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbERpc2FibGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCdkaXNhYmxlJywgJChzZWxmLm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTGlua1NlbGVjdG9yLCAkKFwiZGl2Lm1vZHVsZS1pdGVtLWxpc3RbZGF0YS10ZWNoLW5hbWU9J1wiICsgJCh0aGlzKS5hdHRyKFwiZGF0YS10ZWNoLW5hbWVcIikgKyBcIiddXCIpKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbFJlc2V0TGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcigncmVzZXQnLCAkKHNlbGYubW9kdWxlQWN0aW9uTWVudVJlc2V0TGlua1NlbGVjdG9yLCAkKFwiZGl2Lm1vZHVsZS1pdGVtLWxpc3RbZGF0YS10ZWNoLW5hbWU9J1wiICsgJCh0aGlzKS5hdHRyKFwiZGF0YS10ZWNoLW5hbWVcIikgKyBcIiddXCIpKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbFVuaW5zdGFsbExpbmtTZWxlY3RvciwgZnVuY3Rpb24gKGUpIHtcbiAgICAgICQoZS50YXJnZXQpLnBhcmVudHMoJy5tb2RhbCcpLm9uKCdoaWRkZW4uYnMubW9kYWwnLCBmdW5jdGlvbihldmVudCkge1xuICAgICAgICByZXR1cm4gc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcihcbiAgICAgICAgICAndW5pbnN0YWxsJyxcbiAgICAgICAgICAkKFxuICAgICAgICAgICAgc2VsZi5tb2R1bGVBY3Rpb25NZW51VW5pbnN0YWxsTGlua1NlbGVjdG9yLFxuICAgICAgICAgICAgJChcImRpdi5tb2R1bGUtaXRlbS1saXN0W2RhdGEtdGVjaC1uYW1lPSdcIiArICQoZS50YXJnZXQpLmF0dHIoXCJkYXRhLXRlY2gtbmFtZVwiKSArIFwiJ11cIilcbiAgICAgICAgICApLFxuICAgICAgICAgICQoZS50YXJnZXQpLmF0dHIoXCJkYXRhLWRlbGV0aW9uXCIpXG4gICAgICAgICk7XG4gICAgICB9LmJpbmQoZSkpO1xuICAgIH0pO1xuICB9O1xuXG4gIF9nZXRNb2R1bGVJdGVtU2VsZWN0b3IoKSB7XG4gICAgaWYgKCQodGhpcy5tb2R1bGVJdGVtTGlzdFNlbGVjdG9yKS5sZW5ndGgpIHtcbiAgICAgIHJldHVybiB0aGlzLm1vZHVsZUl0ZW1MaXN0U2VsZWN0b3I7XG4gICAgfSBlbHNlIHtcbiAgICAgIHJldHVybiB0aGlzLm1vZHVsZUl0ZW1HcmlkU2VsZWN0b3I7XG4gICAgfVxuICB9O1xuXG4gIF9jb25maXJtQWN0aW9uKGFjdGlvbiwgZWxlbWVudCkge1xuICAgIHZhciBtb2RhbCA9ICQoJyMnICsgJChlbGVtZW50KS5kYXRhKCdjb25maXJtX21vZGFsJykpO1xuICAgIGlmIChtb2RhbC5sZW5ndGggIT0gMSkge1xuICAgICAgcmV0dXJuIHRydWU7XG4gICAgfVxuICAgIG1vZGFsLmZpcnN0KCkubW9kYWwoJ3Nob3cnKTtcblxuICAgIHJldHVybiBmYWxzZTsgLy8gZG8gbm90IGFsbG93IGEuaHJlZiB0byByZWxvYWQgdGhlIHBhZ2UuIFRoZSBjb25maXJtIG1vZGFsIGRpYWxvZyB3aWxsIGRvIGl0IGFzeW5jIGlmIG5lZWRlZC5cbiAgfTtcblxuICAvKipcbiAgICogVXBkYXRlIHRoZSBjb250ZW50IG9mIGEgbW9kYWwgYXNraW5nIGEgY29uZmlybWF0aW9uIGZvciBQcmVzdGFUcnVzdCBhbmQgb3BlbiBpdFxuICAgKlxuICAgKiBAcGFyYW0ge2FycmF5fSByZXN1bHQgY29udGFpbmluZyBtb2R1bGUgZGF0YVxuICAgKiBAcmV0dXJuIHt2b2lkfVxuICAgKi9cbiAgX2NvbmZpcm1QcmVzdGFUcnVzdChyZXN1bHQpIHtcbiAgICB2YXIgdGhhdCA9IHRoaXM7XG4gICAgdmFyIG1vZGFsID0gdGhpcy5fcmVwbGFjZVByZXN0YVRydXN0UGxhY2Vob2xkZXJzKHJlc3VsdCk7XG5cbiAgICBtb2RhbC5maW5kKFwiLnBzdHJ1c3QtaW5zdGFsbFwiKS5vZmYoJ2NsaWNrJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAvLyBGaW5kIHJlbGF0ZWQgZm9ybSwgdXBkYXRlIGl0IGFuZCBzdWJtaXQgaXRcbiAgICAgIHZhciBpbnN0YWxsX2J1dHRvbiA9ICQodGhhdC5tb2R1bGVBY3Rpb25NZW51SW5zdGFsbExpbmtTZWxlY3RvciwgJy5tb2R1bGUtaXRlbVtkYXRhLXRlY2gtbmFtZT1cIicgKyByZXN1bHQubW9kdWxlLmF0dHJpYnV0ZXMubmFtZSArICdcIl0nKTtcbiAgICAgIHZhciBmb3JtID0gaW5zdGFsbF9idXR0b24ucGFyZW50KFwiZm9ybVwiKTtcbiAgICAgICQoJzxpbnB1dD4nKS5hdHRyKHtcbiAgICAgICAgdHlwZTogJ2hpZGRlbicsXG4gICAgICAgIHZhbHVlOiAnMScsXG4gICAgICAgIG5hbWU6ICdhY3Rpb25QYXJhbXNbY29uZmlybVByZXN0YVRydXN0XSdcbiAgICAgIH0pLmFwcGVuZFRvKGZvcm0pO1xuXG4gICAgICBpbnN0YWxsX2J1dHRvbi5jbGljaygpO1xuICAgICAgbW9kYWwubW9kYWwoJ2hpZGUnKTtcbiAgICB9KTtcblxuICAgIG1vZGFsLm1vZGFsKCk7XG4gIH07XG5cbiAgX3JlcGxhY2VQcmVzdGFUcnVzdFBsYWNlaG9sZGVycyhyZXN1bHQpIHtcbiAgICB2YXIgbW9kYWwgPSAkKFwiI21vZGFsLXByZXN0YXRydXN0XCIpO1xuICAgIHZhciBtb2R1bGUgPSByZXN1bHQubW9kdWxlLmF0dHJpYnV0ZXM7XG5cbiAgICBpZiAocmVzdWx0LmNvbmZpcm1hdGlvbl9zdWJqZWN0ICE9PSAnUHJlc3RhVHJ1c3QnIHx8ICFtb2RhbC5sZW5ndGgpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB2YXIgYWxlcnRDbGFzcyA9IG1vZHVsZS5wcmVzdGF0cnVzdC5zdGF0dXMgPyAnc3VjY2VzcycgOiAnd2FybmluZyc7XG5cbiAgICBpZiAobW9kdWxlLnByZXN0YXRydXN0LmNoZWNrX2xpc3QucHJvcGVydHkpIHtcbiAgICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1idG4tcHJvcGVydHktb2tcIikuc2hvdygpO1xuICAgICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWJ0bi1wcm9wZXJ0eS1ub2tcIikuaGlkZSgpO1xuICAgIH0gZWxzZSB7XG4gICAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYnRuLXByb3BlcnR5LW9rXCIpLmhpZGUoKTtcbiAgICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1idG4tcHJvcGVydHktbm9rXCIpLnNob3coKTtcbiAgICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1idXlcIikuYXR0cihcImhyZWZcIiwgbW9kdWxlLnVybCkudG9nZ2xlKG1vZHVsZS51cmwgIT09IG51bGwpO1xuICAgIH1cblxuICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1pbWdcIikuYXR0cih7c3JjOiBtb2R1bGUuaW1nLCBhbHQ6IG1vZHVsZS5uYW1lfSk7XG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LW5hbWVcIikudGV4dChtb2R1bGUuZGlzcGxheU5hbWUpO1xuICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1hdXRob3JcIikudGV4dChtb2R1bGUuYXV0aG9yKTtcbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtbGFiZWxcIikuYXR0cihcImNsYXNzXCIsIFwidGV4dC1cIiArIGFsZXJ0Q2xhc3MpLnRleHQobW9kdWxlLnByZXN0YXRydXN0LnN0YXR1cyA/ICdPSycgOiAnS08nKTtcbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtbWVzc2FnZVwiKS5hdHRyKFwiY2xhc3NcIiwgXCJhbGVydCBhbGVydC1cIithbGVydENsYXNzKTtcbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtbWVzc2FnZSA+IHBcIikudGV4dChtb2R1bGUucHJlc3RhdHJ1c3QubWVzc2FnZSk7XG5cbiAgICByZXR1cm4gbW9kYWw7XG4gIH1cblxuICBfZGlzcGF0Y2hQcmVFdmVudChhY3Rpb24sIGVsZW1lbnQpIHtcbiAgICB2YXIgZXZlbnQgPSBqUXVlcnkuRXZlbnQoJ21vZHVsZV9jYXJkX2FjdGlvbl9ldmVudCcpO1xuXG4gICAgJChlbGVtZW50KS50cmlnZ2VyKGV2ZW50LCBbYWN0aW9uXSk7XG4gICAgaWYgKGV2ZW50LmlzUHJvcGFnYXRpb25TdG9wcGVkKCkgIT09IGZhbHNlIHx8IGV2ZW50LmlzSW1tZWRpYXRlUHJvcGFnYXRpb25TdG9wcGVkKCkgIT09IGZhbHNlKSB7XG4gICAgICByZXR1cm4gZmFsc2U7IC8vIGlmIGFsbCBoYW5kbGVycyBoYXZlIG5vdCBiZWVuIGNhbGxlZCwgdGhlbiBzdG9wIHByb3BhZ2F0aW9uIG9mIHRoZSBjbGljayBldmVudC5cbiAgICB9XG5cbiAgICByZXR1cm4gKGV2ZW50LnJlc3VsdCAhPT0gZmFsc2UpOyAvLyBleHBsaWNpdCBmYWxzZSBtdXN0IGJlIHNldCBmcm9tIGhhbmRsZXJzIHRvIHN0b3AgcHJvcGFnYXRpb24gb2YgdGhlIGNsaWNrIGV2ZW50LlxuICB9O1xuXG4gIF9yZXF1ZXN0VG9Db250cm9sbGVyKGFjdGlvbiwgZWxlbWVudCwgZm9yY2VEZWxldGlvbiwgZGlzYWJsZUNhY2hlQ2xlYXIsIGNhbGxiYWNrKSB7XG4gICAgdmFyIHNlbGYgPSB0aGlzO1xuICAgIHZhciBqcUVsZW1lbnRPYmogPSBlbGVtZW50LmNsb3Nlc3QodGhpcy5tb2R1bGVJdGVtQWN0aW9uc1NlbGVjdG9yKTtcbiAgICB2YXIgZm9ybSA9IGVsZW1lbnQuY2xvc2VzdChcImZvcm1cIik7XG4gICAgdmFyIHNwaW5uZXJPYmogPSAkKFwiPGJ1dHRvbiBjbGFzcz1cXFwiYnRuLXByaW1hcnktcmV2ZXJzZSBvbmNsaWNrIHVuYmluZCBzcGlubmVyIFxcXCI+PC9idXR0b24+XCIpO1xuICAgIHZhciB1cmwgPSBcIi8vXCIgKyB3aW5kb3cubG9jYXRpb24uaG9zdCArIGZvcm0uYXR0cihcImFjdGlvblwiKTtcbiAgICB2YXIgYWN0aW9uUGFyYW1zID0gZm9ybS5zZXJpYWxpemVBcnJheSgpO1xuXG4gICAgaWYgKGZvcmNlRGVsZXRpb24gPT09IFwidHJ1ZVwiIHx8IGZvcmNlRGVsZXRpb24gPT09IHRydWUpIHtcbiAgICAgIGFjdGlvblBhcmFtcy5wdXNoKHtuYW1lOiBcImFjdGlvblBhcmFtc1tkZWxldGlvbl1cIiwgdmFsdWU6IHRydWV9KTtcbiAgICB9XG4gICAgaWYgKGRpc2FibGVDYWNoZUNsZWFyID09PSBcInRydWVcIiB8fCBkaXNhYmxlQ2FjaGVDbGVhciA9PT0gdHJ1ZSkge1xuICAgICAgYWN0aW9uUGFyYW1zLnB1c2goe25hbWU6IFwiYWN0aW9uUGFyYW1zW2NhY2hlQ2xlYXJFbmFibGVkXVwiLCB2YWx1ZTogMH0pO1xuICAgIH1cblxuICAgICQuYWpheCh7XG4gICAgICB1cmw6IHVybCxcbiAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICAgIGRhdGE6IGFjdGlvblBhcmFtcyxcbiAgICAgIGJlZm9yZVNlbmQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAganFFbGVtZW50T2JqLmhpZGUoKTtcbiAgICAgICAganFFbGVtZW50T2JqLmFmdGVyKHNwaW5uZXJPYmopO1xuICAgICAgfVxuICAgIH0pLmRvbmUoZnVuY3Rpb24gKHJlc3VsdCkge1xuICAgICAgaWYgKHR5cGVvZiByZXN1bHQgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAkLmdyb3dsLmVycm9yKHttZXNzYWdlOiBcIk5vIGFuc3dlciByZWNlaXZlZCBmcm9tIHNlcnZlclwifSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB2YXIgbW9kdWxlVGVjaE5hbWUgPSBPYmplY3Qua2V5cyhyZXN1bHQpWzBdO1xuXG4gICAgICAgIGlmIChyZXN1bHRbbW9kdWxlVGVjaE5hbWVdLnN0YXR1cyA9PT0gZmFsc2UpIHtcbiAgICAgICAgICBpZiAodHlwZW9mIHJlc3VsdFttb2R1bGVUZWNoTmFtZV0uY29uZmlybWF0aW9uX3N1YmplY3QgIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICBzZWxmLl9jb25maXJtUHJlc3RhVHJ1c3QocmVzdWx0W21vZHVsZVRlY2hOYW1lXSk7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogcmVzdWx0W21vZHVsZVRlY2hOYW1lXS5tc2d9KTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAkLmdyb3dsLm5vdGljZSh7bWVzc2FnZTogcmVzdWx0W21vZHVsZVRlY2hOYW1lXS5tc2d9KTtcblxuICAgICAgICAgIHZhciBhbHRlcmVkU2VsZWN0b3IgPSBzZWxmLl9nZXRNb2R1bGVJdGVtU2VsZWN0b3IoKS5yZXBsYWNlKCcuJywgJycpO1xuICAgICAgICAgIHZhciBtYWluRWxlbWVudCA9IG51bGw7XG5cbiAgICAgICAgICBpZiAoYWN0aW9uID09IFwidW5pbnN0YWxsXCIpIHtcbiAgICAgICAgICAgIG1haW5FbGVtZW50ID0ganFFbGVtZW50T2JqLmNsb3Nlc3QoJy4nICsgYWx0ZXJlZFNlbGVjdG9yKTtcbiAgICAgICAgICAgIG1haW5FbGVtZW50LnJlbW92ZSgpO1xuXG4gICAgICAgICAgICBCT0V2ZW50LmVtaXRFdmVudChcIk1vZHVsZSBVbmluc3RhbGxlZFwiLCBcIkN1c3RvbUV2ZW50XCIpO1xuICAgICAgICAgIH0gZWxzZSBpZiAoYWN0aW9uID09IFwiZGlzYWJsZVwiKSB7XG4gICAgICAgICAgICBtYWluRWxlbWVudCA9IGpxRWxlbWVudE9iai5jbG9zZXN0KCcuJyArIGFsdGVyZWRTZWxlY3Rvcik7XG4gICAgICAgICAgICBtYWluRWxlbWVudC5hZGRDbGFzcyhhbHRlcmVkU2VsZWN0b3IgKyAnLWlzTm90QWN0aXZlJyk7XG4gICAgICAgICAgICBtYWluRWxlbWVudC5hdHRyKCdkYXRhLWFjdGl2ZScsICcwJyk7XG5cbiAgICAgICAgICAgIEJPRXZlbnQuZW1pdEV2ZW50KFwiTW9kdWxlIERpc2FibGVkXCIsIFwiQ3VzdG9tRXZlbnRcIik7XG4gICAgICAgICAgfSBlbHNlIGlmIChhY3Rpb24gPT0gXCJlbmFibGVcIikge1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQgPSBqcUVsZW1lbnRPYmouY2xvc2VzdCgnLicgKyBhbHRlcmVkU2VsZWN0b3IpO1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQucmVtb3ZlQ2xhc3MoYWx0ZXJlZFNlbGVjdG9yICsgJy1pc05vdEFjdGl2ZScpO1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQuYXR0cignZGF0YS1hY3RpdmUnLCAnMScpO1xuXG4gICAgICAgICAgICBCT0V2ZW50LmVtaXRFdmVudChcIk1vZHVsZSBFbmFibGVkXCIsIFwiQ3VzdG9tRXZlbnRcIik7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAganFFbGVtZW50T2JqLnJlcGxhY2VXaXRoKHJlc3VsdFttb2R1bGVUZWNoTmFtZV0uYWN0aW9uX21lbnVfaHRtbCk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9KS5mYWlsKGZ1bmN0aW9uKCkge1xuICAgICAgY29uc3QgbW9kdWxlSXRlbSA9IGpxRWxlbWVudE9iai5jbG9zZXN0KCdtb2R1bGUtaXRlbS1saXN0Jyk7XG4gICAgICBjb25zdCB0ZWNoTmFtZSA9IG1vZHVsZUl0ZW0uZGF0YSgndGVjaE5hbWUnKTtcbiAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IFwiQ291bGQgbm90IHBlcmZvcm0gYWN0aW9uIFwiK2FjdGlvbitcIiBmb3IgbW9kdWxlIFwiK3RlY2hOYW1lfSk7XG4gICAgfSkuYWx3YXlzKGZ1bmN0aW9uICgpIHtcbiAgICAgIGpxRWxlbWVudE9iai5mYWRlSW4oKTtcbiAgICAgIHNwaW5uZXJPYmoucmVtb3ZlKCk7XG4gICAgICBpZiAoY2FsbGJhY2spIHtcbiAgICAgICAgY2FsbGJhY2soKTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgIHJldHVybiBmYWxzZTtcbiAgfTtcbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvbW9kdWxlLWNhcmQuanMiXSwic291cmNlUm9vdCI6IiJ9