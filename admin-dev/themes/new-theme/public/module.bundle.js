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
/******/ 	return __webpack_require__(__webpack_require__.s = 338);
/******/ })
/************************************************************************/
/******/ ({

/***/ 11:
/***/ (function(module, exports) {

(function() { module.exports = window["jQuery"]; }());

/***/ }),

/***/ 260:
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

/***/ 261:
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

/***/ 338:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _moduleCard = __webpack_require__(59);

var _moduleCard2 = _interopRequireDefault(_moduleCard);

var _controller = __webpack_require__(260);

var _controller2 = _interopRequireDefault(_controller);

var _loader = __webpack_require__(261);

var _loader2 = _interopRequireDefault(_loader);

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
  var moduleCardController = new _moduleCard2.default();
  new _loader2.default();
  new _controller2.default(moduleCardController);
});

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQ/MjBkNCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy9leHRlcm5hbCBcImpRdWVyeVwiPzBjYjgqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvbW9kdWxlL2NvbnRyb2xsZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvbW9kdWxlL2xvYWRlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9tb2R1bGUvaW5kZXguanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9tb2R1bGUtY2FyZC5qcyJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwiQWRtaW5Nb2R1bGVDb250cm9sbGVyIiwibW9kdWxlQ2FyZENvbnRyb2xsZXIiLCJERUZBVUxUX01BWF9SRUNFTlRMWV9VU0VEIiwiREVGQVVMVF9NQVhfUEVSX0NBVEVHT1JJRVMiLCJESVNQTEFZX0dSSUQiLCJESVNQTEFZX0xJU1QiLCJDQVRFR09SWV9SRUNFTlRMWV9VU0VEIiwiY3VycmVudENhdGVnb3J5RGlzcGxheSIsImN1cnJlbnREaXNwbGF5IiwiaXNDYXRlZ29yeUdyaWREaXNwbGF5ZWQiLCJjdXJyZW50VGFnc0xpc3QiLCJjdXJyZW50UmVmQ2F0ZWdvcnkiLCJjdXJyZW50UmVmU3RhdHVzIiwiY3VycmVudFNvcnRpbmciLCJiYXNlQWRkb25zVXJsIiwicHN0YWdnZXJJbnB1dCIsImxhc3RCdWxrQWN0aW9uIiwiaXNVcGxvYWRTdGFydGVkIiwicmVjZW50bHlVc2VkU2VsZWN0b3IiLCJtb2R1bGVzTGlzdCIsImFkZG9uc0NhcmRHcmlkIiwiYWRkb25zQ2FyZExpc3QiLCJtb2R1bGVTaG9ydExpc3QiLCJzZWVNb3JlU2VsZWN0b3IiLCJzZWVMZXNzU2VsZWN0b3IiLCJtb2R1bGVJdGVtR3JpZFNlbGVjdG9yIiwibW9kdWxlSXRlbUxpc3RTZWxlY3RvciIsImNhdGVnb3J5U2VsZWN0b3JMYWJlbFNlbGVjdG9yIiwiY2F0ZWdvcnlTZWxlY3RvciIsImNhdGVnb3J5SXRlbVNlbGVjdG9yIiwiYWRkb25zTG9naW5CdXR0b25TZWxlY3RvciIsImNhdGVnb3J5UmVzZXRCdG5TZWxlY3RvciIsIm1vZHVsZUluc3RhbGxCdG5TZWxlY3RvciIsIm1vZHVsZVNvcnRpbmdEcm9wZG93blNlbGVjdG9yIiwiY2F0ZWdvcnlHcmlkU2VsZWN0b3IiLCJjYXRlZ29yeUdyaWRJdGVtU2VsZWN0b3IiLCJhZGRvbkl0ZW1HcmlkU2VsZWN0b3IiLCJhZGRvbkl0ZW1MaXN0U2VsZWN0b3IiLCJ1cGdyYWRlQWxsU291cmNlIiwidXBncmFkZUFsbFRhcmdldHMiLCJidWxrQWN0aW9uRHJvcERvd25TZWxlY3RvciIsImJ1bGtJdGVtU2VsZWN0b3IiLCJidWxrQWN0aW9uQ2hlY2tib3hMaXN0U2VsZWN0b3IiLCJidWxrQWN0aW9uQ2hlY2tib3hHcmlkU2VsZWN0b3IiLCJjaGVja2VkQnVsa0FjdGlvbkxpc3RTZWxlY3RvciIsImNoZWNrZWRCdWxrQWN0aW9uR3JpZFNlbGVjdG9yIiwiYnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0b3IiLCJidWxrQ29uZmlybU1vZGFsU2VsZWN0b3IiLCJidWxrQ29uZmlybU1vZGFsQWN0aW9uTmFtZVNlbGVjdG9yIiwiYnVsa0NvbmZpcm1Nb2RhbExpc3RTZWxlY3RvciIsImJ1bGtDb25maXJtTW9kYWxBY2tCdG5TZWxlY3RvciIsInBsYWNlaG9sZGVyR2xvYmFsU2VsZWN0b3IiLCJwbGFjZWhvbGRlckZhaWx1cmVHbG9iYWxTZWxlY3RvciIsInBsYWNlaG9sZGVyRmFpbHVyZU1zZ1NlbGVjdG9yIiwicGxhY2Vob2xkZXJGYWlsdXJlUmV0cnlCdG5TZWxlY3RvciIsInN0YXR1c1NlbGVjdG9yTGFiZWxTZWxlY3RvciIsInN0YXR1c0l0ZW1TZWxlY3RvciIsInN0YXR1c1Jlc2V0QnRuU2VsZWN0b3IiLCJhZGRvbnNDb25uZWN0TW9kYWxCdG5TZWxlY3RvciIsImFkZG9uc0xvZ291dE1vZGFsQnRuU2VsZWN0b3IiLCJhZGRvbnNJbXBvcnRNb2RhbEJ0blNlbGVjdG9yIiwiZHJvcFpvbmVNb2RhbFNlbGVjdG9yIiwiZHJvcFpvbmVNb2RhbEZvb3RlclNlbGVjdG9yIiwiZHJvcFpvbmVJbXBvcnRab25lU2VsZWN0b3IiLCJhZGRvbnNDb25uZWN0TW9kYWxTZWxlY3RvciIsImFkZG9uc0xvZ291dE1vZGFsU2VsZWN0b3IiLCJhZGRvbnNDb25uZWN0Rm9ybSIsIm1vZHVsZUltcG9ydE1vZGFsQ2xvc2VCdG4iLCJtb2R1bGVJbXBvcnRTdGFydFNlbGVjdG9yIiwibW9kdWxlSW1wb3J0UHJvY2Vzc2luZ1NlbGVjdG9yIiwibW9kdWxlSW1wb3J0U3VjY2Vzc1NlbGVjdG9yIiwibW9kdWxlSW1wb3J0U3VjY2Vzc0NvbmZpZ3VyZUJ0blNlbGVjdG9yIiwibW9kdWxlSW1wb3J0RmFpbHVyZVNlbGVjdG9yIiwibW9kdWxlSW1wb3J0RmFpbHVyZVJldHJ5U2VsZWN0b3IiLCJtb2R1bGVJbXBvcnRGYWlsdXJlRGV0YWlsc0J0blNlbGVjdG9yIiwibW9kdWxlSW1wb3J0U2VsZWN0RmlsZU1hbnVhbFNlbGVjdG9yIiwibW9kdWxlSW1wb3J0RmFpbHVyZU1zZ0RldGFpbHNTZWxlY3RvciIsIm1vZHVsZUltcG9ydENvbmZpcm1TZWxlY3RvciIsImluaXRTb3J0aW5nRHJvcGRvd24iLCJpbml0Qk9FdmVudFJlZ2lzdGVyaW5nIiwiaW5pdEN1cnJlbnREaXNwbGF5IiwiaW5pdFNvcnRpbmdEaXNwbGF5U3dpdGNoIiwiaW5pdEJ1bGtEcm9wZG93biIsImluaXRTZWFyY2hCbG9jayIsImluaXRDYXRlZ29yeVNlbGVjdCIsImluaXRDYXRlZ29yaWVzR3JpZCIsImluaXRBY3Rpb25CdXR0b25zIiwiaW5pdEFkZG9uc1NlYXJjaCIsImluaXRBZGRvbnNDb25uZWN0IiwiaW5pdEFkZE1vZHVsZUFjdGlvbiIsImluaXREcm9wem9uZSIsImluaXRQYWdlQ2hhbmdlUHJvdGVjdGlvbiIsImluaXRQbGFjZWhvbGRlck1lY2hhbmlzbSIsImluaXRGaWx0ZXJTdGF0dXNEcm9wZG93biIsImZldGNoTW9kdWxlc0xpc3QiLCJnZXROb3RpZmljYXRpb25zQ291bnQiLCJpbml0aWFsaXplU2VlTW9yZSIsInNlbGYiLCJib2R5Iiwib24iLCJwYXJzZUludCIsImRhdGEiLCJ0ZXh0IiwiZmluZCIsInNob3ciLCJ1cGRhdGVNb2R1bGVWaXNpYmlsaXR5IiwiaGlkZSIsImdldEJ1bGtDaGVja2JveGVzU2VsZWN0b3IiLCJzZWxlY3RvciIsImdldEJ1bGtDaGVja2JveGVzQ2hlY2tlZFNlbGVjdG9yIiwibGVuZ3RoIiwiY2xvc2VzdCIsInJlbW92ZUNsYXNzIiwiYWRkQ2xhc3MiLCJpbml0aWFsaXplQm9keUNoYW5nZSIsImdyb3dsIiwid2FybmluZyIsIm1lc3NhZ2UiLCJ0cmFuc2xhdGVfamF2YXNjcmlwdHMiLCJtb2R1bGVzTGlzdFN0cmluZyIsImJ1aWxkQnVsa0FjdGlvbk1vZHVsZUxpc3QiLCJhY3Rpb25TdHJpbmciLCJ0b0xvd2VyQ2FzZSIsImh0bWwiLCJtb2RhbCIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJkb0J1bGtBY3Rpb24iLCJCT0V2ZW50Iiwib25Nb2R1bGVEaXNhYmxlZCIsInVwZGF0ZVRvdGFsUmVzdWx0cyIsIm1vZHVsZUl0ZW1TZWxlY3RvciIsImdldE1vZHVsZUl0ZW1TZWxlY3RvciIsImVhY2giLCJzY2FuTW9kdWxlc0xpc3QiLCJhamF4TG9hZFBhZ2UiLCJmYWRlT3V0IiwiZmFkZUluIiwiYWpheCIsIm1ldGhvZCIsInVybCIsIm1vZHVsZVVSTHMiLCJjYXRhbG9nUmVmcmVzaCIsImRvbmUiLCJyZXNwb25zZSIsInN0YXR1cyIsImRvbUVsZW1lbnRzIiwibXNnIiwic3R5bGVzaGVldCIsImRvY3VtZW50Iiwic3R5bGVTaGVldHMiLCJzdHlsZXNoZWV0UnVsZSIsIm1vZHVsZUdsb2JhbFNlbGVjdG9yIiwibW9kdWxlU29ydGluZ1NlbGVjdG9yIiwicmVxdWlyZWRTZWxlY3RvckNvbWJpbmF0aW9uIiwiaW5zZXJ0UnVsZSIsImNzc1J1bGVzIiwiYWRkUnVsZSIsImluZGV4IiwiZWxlbWVudCIsImFwcGVuZCIsImNvbnRlbnQiLCJjc3MiLCJwb3BvdmVyIiwiZmFpbCIsInN0YXR1c1RleHQiLCJjb250YWluZXIiLCIkdGhpcyIsInByZXBhcmVDb250YWluZXIiLCJwcmVwYXJlTW9kdWxlcyIsInB1c2giLCJkb21PYmplY3QiLCJpZCIsIm5hbWUiLCJzY29yaW5nIiwicGFyc2VGbG9hdCIsImxvZ28iLCJhdXRob3IiLCJ2ZXJzaW9uIiwiZGVzY3JpcHRpb24iLCJ0ZWNoTmFtZSIsImNoaWxkQ2F0ZWdvcmllcyIsImNhdGVnb3JpZXMiLCJTdHJpbmciLCJ0eXBlIiwicHJpY2UiLCJhY3RpdmUiLCJhY2Nlc3MiLCJkaXNwbGF5IiwiaGFzQ2xhc3MiLCJyZW1vdmUiLCJ0cmlnZ2VyIiwib3JkZXIiLCJrZXkiLCJzcGxpdHRlZEtleSIsInNwbGl0IiwiY3VycmVudENvbXBhcmUiLCJhIiwiYiIsImFEYXRhIiwiYkRhdGEiLCJEYXRlIiwiZ2V0VGltZSIsImlzTmFOIiwibG9jYWxlQ29tcGFyZSIsInNvcnQiLCJyZXZlcnNlIiwic2V0U2hvcnRMaXN0VmlzaWJpbGl0eSIsIm5iTW9kdWxlc0luQ29udGFpbmVyIiwidXBkYXRlTW9kdWxlU29ydGluZyIsImlzVmlzaWJsZSIsImN1cnJlbnRNb2R1bGUiLCJtb2R1bGVDYXRlZ29yeSIsInRhZ0V4aXN0cyIsIm5ld1ZhbHVlIiwibW9kdWxlc0xpc3RMZW5ndGgiLCJjb3VudGVyIiwiaSIsInZhbHVlIiwiaW5kZXhPZiIsInVuZGVmaW5lZCIsInVwZGF0ZU1vZHVsZUNvbnRhaW5lckRpc3BsYXkiLCJjaGVja0JveGVzU2VsZWN0b3IiLCJhbHJlYWR5RG9uZUZsYWciLCJodG1sR2VuZXJhdGVkIiwiY3VycmVudEVsZW1lbnQiLCJwcmVwYXJlQ2hlY2tib3hlcyIsImF0dHIiLCJpbml0aWFsaXplQm9keVN1Ym1pdCIsImRhdGFUeXBlIiwic2VyaWFsaXplIiwiYmVmb3JlU2VuZCIsInN1Y2Nlc3MiLCJsb2NhdGlvbiIsInJlbG9hZCIsImVycm9yIiwiYWRkTW9kdWxlQnV0dG9uIiwiZHJvcHpvbmUiLCJzZXRUaW1lb3V0IiwicmVtb3ZlQXR0ciIsIm1hbnVhbFNlbGVjdCIsImluaXRpYWxpemVCb2R5Q2xpY2tPbk1vZHVsZUltcG9ydCIsInNsaWRlRG93biIsImRyb3B6b25lT3B0aW9ucyIsIm1vZHVsZUltcG9ydCIsImFjY2VwdGVkRmlsZXMiLCJwYXJhbU5hbWUiLCJtYXhGaWxlc2l6ZSIsInVwbG9hZE11bHRpcGxlIiwiYWRkUmVtb3ZlTGlua3MiLCJkaWN0RGVmYXVsdE1lc3NhZ2UiLCJoaWRkZW5JbnB1dENvbnRhaW5lciIsInRpbWVvdXQiLCJhZGRlZGZpbGUiLCJhbmltYXRlU3RhcnRVcGxvYWQiLCJwcm9jZXNzaW5nIiwiZmlsZSIsImRpc3BsYXlPblVwbG9hZEVycm9yIiwiY29tcGxldGUiLCJyZXNwb25zZU9iamVjdCIsInBhcnNlSlNPTiIsInhociIsImlzX2NvbmZpZ3VyYWJsZSIsIm1vZHVsZV9uYW1lIiwiZGlzcGxheU9uVXBsb2FkRG9uZSIsImV4dGVuZCIsImNhbGxiYWNrIiwiZmluaXNoIiwicmVzdWx0IiwiYW5pbWF0ZUVuZFVwbG9hZCIsImNvbmZpZ3VyZUxpbmsiLCJjb25maWd1cmF0aW9uUGFnZSIsInJlcGxhY2UiLCJjb25maXJtYXRpb25fc3ViamVjdCIsImRpc3BsYXlQcmVzdGFUcnVzdFN0ZXAiLCJfcmVwbGFjZVByZXN0YVRydXN0UGxhY2Vob2xkZXJzIiwibW9kdWxlTmFtZSIsIm1vZHVsZSIsImF0dHJpYnV0ZXMiLCJvZmYiLCJwb3N0IiwidXJscyIsImluc3RhbGwiLCJhbHdheXMiLCJnZXRKU09OIiwibm90aWZpY2F0aW9uc0NvdW50IiwidXBkYXRlTm90aWZpY2F0aW9uc0NvdW50IiwiY29uc29sZSIsImJhZGdlIiwiZGVzdGluYXRpb25UYWJzIiwidG9fY29uZmlndXJlIiwidG9fdXBkYXRlIiwic2VhcmNoUXVlcnkiLCJlbmNvZGVVUklDb21wb25lbnQiLCJqb2luIiwib3BlbiIsImluaXRpbGFpemVHcmlkQm9keUNsaWNrIiwicmVmQ2F0ZWdvcnkiLCJyZXNldFRhZ3MiLCJtZW51Q2F0ZWdvcnlUb1RyaWdnZXIiLCJ3YXJuIiwiY2xpY2siLCJpbml0aWFsaXplQm9keVNvcnRpbmdDaGFuZ2UiLCJyZXF1ZXN0ZWRCdWxrQWN0aW9uIiwiZm9yY2VEZWxldGlvbiIsInByb3AiLCJidWxrQWN0aW9uVG9VcmwiLCJidWxrQWN0aW9uU2VsZWN0ZWRTZWxlY3RvciIsImJ1bGtNb2R1bGVBY3Rpb24iLCJtb2R1bGVzQWN0aW9ucyIsIm1vZHVsZVRlY2hOYW1lIiwiYnVsa0FjdGlvblNlbGVjdG9yIiwiYWN0aW9uTWVudU9iaiIsIm5leHQiLCJwZXJmb3JtTW9kdWxlc0FjdGlvbiIsImFjdGlvbk1lbnVMaW5rcyIsImZpbHRlckFsbG93ZWRBY3Rpb25zIiwibW9kdWxlc1JlcXVlc3RlZENvdW50ZG93biIsInNwaW5uZXJPYmoiLCJidWxrTW9kdWxlc0xvb3AiLCJhY3Rpb25NZW51TGluayIsInJlcXVlc3RNb2R1bGVBY3Rpb24iLCJjb3VudGRvd25Nb2R1bGVzUmVxdWVzdCIsImxhc3RNZW51TGluayIsIm1vZHVsZUl0ZW1BY3Rpb25zU2VsZWN0b3IiLCJhZnRlciIsImRpc2FibGVDYWNoZUNsZWFyIiwicmVxdWVzdEVuZENhbGxiYWNrIiwiX3JlcXVlc3RUb0NvbnRyb2xsZXIiLCJmaWx0ZXJBbGxvd2VkTW9kdWxlcyIsIm1vZHVsZURhdGEiLCJtb2R1bGVBY3Rpb25NZW51TGlua1NlbGVjdG9yIiwiaW5pdGlhbGl6ZUFjdGlvbkJ1dHRvbnNDbGljayIsIiRuZXh0IiwibW9kdWxlSXRlbUxpc3QiLCJpbml0aWFsaXplQ2F0ZWdvcnlTZWxlY3RDbGljayIsImluaXRpYWxpemVDYXRlZ29yeVJlc2V0QnV0dG9uQ2xpY2siLCJyYXdUZXh0IiwidXBwZXJGaXJzdExldHRlciIsImNoYXJBdCIsInRvVXBwZXJDYXNlIiwicmVtb3ZlZEZpcnN0TGV0dGVyIiwic2xpY2UiLCJvcmlnaW5hbFRleHQiLCJwc3RhZ2dlciIsIm9uVGFnc0NoYW5nZWQiLCJ0YWdMaXN0Iiwib25SZXNldFRhZ3MiLCJpbnB1dFBsYWNlaG9sZGVyIiwiY2xvc2luZ0Nyb3NzIiwiY29udGV4dCIsInN3aXRjaFNvcnQiLCJzd2l0Y2hUbyIsImlzQWxyZWFkeURpc3BsYXllZCIsInN3aXRjaFNvcnRpbmdEaXNwbGF5VG8iLCJzZWVNb3JlIiwicmVwbGFjZUZpcnN0V29yZEJ5IiwiZXhwbG9kZWRUZXh0IiwiJHNob3J0TGlzdHMiLCJzaG9ydExpc3RzIiwibW9kdWxlc0NvdW50Iiwic2VsZWN0b3JUb1RvZ2dsZSIsInRvZ2dsZSIsIk1vZHVsZUxvYWRlciIsImhhbmRsZUltcG9ydCIsImhhbmRsZUV2ZW50cyIsInZhbGlkYXRlIiwibW9kdWxlUG9wcGluIiwidGFyZ2V0IiwiZ2V0IiwiaHJlZiIsIk1vZHVsZUNhcmQiLCJldmVudE5hbWUiLCJhZGRFdmVudExpc3RlbmVyIiwiY2FsbCIsImVtaXRFdmVudCIsImV2ZW50VHlwZSIsIl9ldmVudCIsImNyZWF0ZUV2ZW50IiwiaW5pdEV2ZW50IiwiZGlzcGF0Y2hFdmVudCIsIm1vZHVsZUFjdGlvbk1lbnVJbnN0YWxsTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudUVuYWJsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVVbmluc3RhbGxMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25NZW51RGlzYWJsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVFbmFibGVNb2JpbGVMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25NZW51RGlzYWJsZU1vYmlsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVSZXNldExpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVVcGRhdGVMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25Nb2RhbERpc2FibGVMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25Nb2RhbFJlc2V0TGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTW9kYWxVbmluc3RhbGxMaW5rU2VsZWN0b3IiLCJmb3JjZURlbGV0aW9uT3B0aW9uIiwiYnRuIiwiX2Rpc3BhdGNoUHJlRXZlbnQiLCJfY29uZmlybUFjdGlvbiIsImUiLCJwYXJlbnRzIiwiYmluZCIsImFjdGlvbiIsImZpcnN0IiwidGhhdCIsImluc3RhbGxfYnV0dG9uIiwiZm9ybSIsInBhcmVudCIsImFwcGVuZFRvIiwiYWxlcnRDbGFzcyIsInByZXN0YXRydXN0IiwiY2hlY2tfbGlzdCIsInByb3BlcnR5Iiwic3JjIiwiaW1nIiwiYWx0IiwiZGlzcGxheU5hbWUiLCJqUXVlcnkiLCJFdmVudCIsImlzUHJvcGFnYXRpb25TdG9wcGVkIiwiaXNJbW1lZGlhdGVQcm9wYWdhdGlvblN0b3BwZWQiLCJqcUVsZW1lbnRPYmoiLCJob3N0IiwiYWN0aW9uUGFyYW1zIiwic2VyaWFsaXplQXJyYXkiLCJPYmplY3QiLCJrZXlzIiwiX2NvbmZpcm1QcmVzdGFUcnVzdCIsIm5vdGljZSIsImFsdGVyZWRTZWxlY3RvciIsIl9nZXRNb2R1bGVJdGVtU2VsZWN0b3IiLCJtYWluRWxlbWVudCIsInJlcGxhY2VXaXRoIiwiYWN0aW9uX21lbnVfaHRtbCIsIm1vZHVsZUl0ZW0iXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FB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QUFrQixjQUExQztBQUNBRSwwQkFBWXpNLElBQVosQ0FBaUIsYUFBakIsRUFBZ0MsR0FBaEM7O0FBRUFuRyxzQkFBUXVQLFNBQVIsQ0FBa0IsZ0JBQWxCLEVBQW9DLGFBQXBDO0FBQ0Q7O0FBRUQyQyx5QkFBYVcsV0FBYixDQUF5QmpLLE9BQU8wQyxjQUFQLEVBQXVCd0gsZ0JBQWhEO0FBQ0Q7QUFDRjtBQUNGLE9BakRELEVBaURHMVEsSUFqREgsQ0FpRFEsWUFBVztBQUNqQixZQUFNMlEsYUFBYWIsYUFBYXBULE9BQWIsQ0FBcUIsa0JBQXJCLENBQW5CO0FBQ0EsWUFBTXNFLFdBQVcyUCxXQUFXM1UsSUFBWCxDQUFnQixVQUFoQixDQUFqQjtBQUNBdkcsVUFBRXFILEtBQUYsQ0FBUXlILEtBQVIsQ0FBYyxFQUFDdkgsU0FBUyw4QkFBNEIyUixNQUE1QixHQUFtQyxjQUFuQyxHQUFrRDNOLFFBQTVELEVBQWQ7QUFDRCxPQXJERCxFQXFER3VHLE1BckRILENBcURVLFlBQVk7QUFDcEJ1SSxxQkFBYXpSLE1BQWI7QUFDQXFMLG1CQUFXaEksTUFBWDtBQUNBLFlBQUk0RSxRQUFKLEVBQWM7QUFDWkE7QUFDRDtBQUNGLE9BM0REOztBQTZEQSxhQUFPLEtBQVA7QUFDRDs7Ozs7O2tCQXhQa0J5RyxVIiwiZmlsZSI6Im1vZHVsZS5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDMzOCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQiLCIoZnVuY3Rpb24oKSB7IG1vZHVsZS5leHBvcnRzID0gd2luZG93W1wialF1ZXJ5XCJdOyB9KCkpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIGV4dGVybmFsIFwialF1ZXJ5XCJcbi8vIG1vZHVsZSBpZCA9IDExXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDYgMjMgMzAgMzIiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogTW9kdWxlIEFkbWluIFBhZ2UgQ29udHJvbGxlci5cbiAqIEBjb25zdHJ1Y3RvclxuICovXG5jbGFzcyBBZG1pbk1vZHVsZUNvbnRyb2xsZXIge1xuICAvKipcbiAgICogSW5pdGlhbGl6ZSBhbGwgbGlzdGVuZXJzIGFuZCBiaW5kIGV2ZXJ5dGhpbmdcbiAgICogQG1ldGhvZCBpbml0XG4gICAqIEBtZW1iZXJvZiBBZG1pbk1vZHVsZVxuICAgKi9cbiAgY29uc3RydWN0b3IobW9kdWxlQ2FyZENvbnRyb2xsZXIpIHtcbiAgICB0aGlzLm1vZHVsZUNhcmRDb250cm9sbGVyID0gbW9kdWxlQ2FyZENvbnRyb2xsZXI7XG5cbiAgICB0aGlzLkRFRkFVTFRfTUFYX1JFQ0VOVExZX1VTRUQgPSAxMDtcbiAgICB0aGlzLkRFRkFVTFRfTUFYX1BFUl9DQVRFR09SSUVTID0gNjtcbiAgICB0aGlzLkRJU1BMQVlfR1JJRCA9ICdncmlkJztcbiAgICB0aGlzLkRJU1BMQVlfTElTVCA9ICdsaXN0JztcbiAgICB0aGlzLkNBVEVHT1JZX1JFQ0VOVExZX1VTRUQgPSAncmVjZW50bHktdXNlZCc7XG5cbiAgICB0aGlzLmN1cnJlbnRDYXRlZ29yeURpc3BsYXkgPSB7fTtcbiAgICB0aGlzLmN1cnJlbnREaXNwbGF5ID0gJyc7XG4gICAgdGhpcy5pc0NhdGVnb3J5R3JpZERpc3BsYXllZCA9IGZhbHNlO1xuICAgIHRoaXMuY3VycmVudFRhZ3NMaXN0ID0gW107XG4gICAgdGhpcy5jdXJyZW50UmVmQ2F0ZWdvcnkgPSBudWxsO1xuICAgIHRoaXMuY3VycmVudFJlZlN0YXR1cyA9IG51bGw7XG4gICAgdGhpcy5jdXJyZW50U29ydGluZyA9IG51bGw7XG4gICAgdGhpcy5iYXNlQWRkb25zVXJsID0gJ2h0dHBzOi8vYWRkb25zLnByZXN0YXNob3AuY29tLyc7XG4gICAgdGhpcy5wc3RhZ2dlcklucHV0ID0gbnVsbDtcbiAgICB0aGlzLmxhc3RCdWxrQWN0aW9uID0gbnVsbDtcbiAgICB0aGlzLmlzVXBsb2FkU3RhcnRlZCA9IGZhbHNlO1xuXG4gICAgdGhpcy5yZWNlbnRseVVzZWRTZWxlY3RvciA9ICcjbW9kdWxlLXJlY2VudGx5LXVzZWQtbGlzdCAubW9kdWxlcy1saXN0JztcblxuICAgIC8qKlxuICAgICAqIExvYWRlZCBtb2R1bGVzIGxpc3QuXG4gICAgICogQ29udGFpbmluZyB0aGUgY2FyZCBhbmQgbGlzdCBkaXNwbGF5LlxuICAgICAqIEB0eXBlIHtBcnJheX1cbiAgICAgKi9cbiAgICB0aGlzLm1vZHVsZXNMaXN0ID0gW107XG4gICAgdGhpcy5hZGRvbnNDYXJkR3JpZCA9IG51bGw7XG4gICAgdGhpcy5hZGRvbnNDYXJkTGlzdCA9IG51bGw7XG5cbiAgICB0aGlzLm1vZHVsZVNob3J0TGlzdCA9ICcubW9kdWxlLXNob3J0LWxpc3QnO1xuICAgIC8vIFNlZSBtb3JlICYgU2VlIGxlc3Mgc2VsZWN0b3JcbiAgICB0aGlzLnNlZU1vcmVTZWxlY3RvciA9ICcuc2VlLW1vcmUnO1xuICAgIHRoaXMuc2VlTGVzc1NlbGVjdG9yID0gJy5zZWUtbGVzcyc7XG5cbiAgICAvLyBTZWxlY3RvcnMgaW50byB2YXJzIHRvIG1ha2UgaXQgZWFzaWVyIHRvIGNoYW5nZSB0aGVtIHdoaWxlIGtlZXBpbmcgc2FtZSBjb2RlIGxvZ2ljXG4gICAgdGhpcy5tb2R1bGVJdGVtR3JpZFNlbGVjdG9yID0gJy5tb2R1bGUtaXRlbS1ncmlkJztcbiAgICB0aGlzLm1vZHVsZUl0ZW1MaXN0U2VsZWN0b3IgPSAnLm1vZHVsZS1pdGVtLWxpc3QnO1xuICAgIHRoaXMuY2F0ZWdvcnlTZWxlY3RvckxhYmVsU2VsZWN0b3IgPSAnLm1vZHVsZS1jYXRlZ29yeS1zZWxlY3Rvci1sYWJlbCc7XG4gICAgdGhpcy5jYXRlZ29yeVNlbGVjdG9yID0gJy5tb2R1bGUtY2F0ZWdvcnktc2VsZWN0b3InO1xuICAgIHRoaXMuY2F0ZWdvcnlJdGVtU2VsZWN0b3IgPSAnLm1vZHVsZS1jYXRlZ29yeS1tZW51JztcbiAgICB0aGlzLmFkZG9uc0xvZ2luQnV0dG9uU2VsZWN0b3IgPSAnI2FkZG9uc19sb2dpbl9idG4nO1xuICAgIHRoaXMuY2F0ZWdvcnlSZXNldEJ0blNlbGVjdG9yID0gJy5tb2R1bGUtY2F0ZWdvcnktcmVzZXQnO1xuICAgIHRoaXMubW9kdWxlSW5zdGFsbEJ0blNlbGVjdG9yID0gJ2lucHV0Lm1vZHVsZS1pbnN0YWxsLWJ0bic7XG4gICAgdGhpcy5tb2R1bGVTb3J0aW5nRHJvcGRvd25TZWxlY3RvciA9ICcubW9kdWxlLXNvcnRpbmctYXV0aG9yIHNlbGVjdCc7XG4gICAgdGhpcy5jYXRlZ29yeUdyaWRTZWxlY3RvciA9ICcjbW9kdWxlcy1jYXRlZ29yaWVzLWdyaWQnO1xuICAgIHRoaXMuY2F0ZWdvcnlHcmlkSXRlbVNlbGVjdG9yID0gJy5tb2R1bGUtY2F0ZWdvcnktaXRlbSc7XG4gICAgdGhpcy5hZGRvbkl0ZW1HcmlkU2VsZWN0b3IgPSAnLm1vZHVsZS1hZGRvbnMtaXRlbS1ncmlkJztcbiAgICB0aGlzLmFkZG9uSXRlbUxpc3RTZWxlY3RvciA9ICcubW9kdWxlLWFkZG9ucy1pdGVtLWxpc3QnO1xuXG4gICAgLy8gVXBncmFkZSBBbGwgc2VsZWN0b3JzXG4gICAgdGhpcy51cGdyYWRlQWxsU291cmNlID0gJy5tb2R1bGVfYWN0aW9uX21lbnVfdXBncmFkZV9hbGwnO1xuICAgIHRoaXMudXBncmFkZUFsbFRhcmdldHMgPSAnI21vZHVsZXMtbGlzdC1jb250YWluZXItdXBkYXRlIC5tb2R1bGVfYWN0aW9uX21lbnVfdXBncmFkZTp2aXNpYmxlJztcblxuICAgIC8vIEJ1bGsgYWN0aW9uIHNlbGVjdG9yc1xuICAgIHRoaXMuYnVsa0FjdGlvbkRyb3BEb3duU2VsZWN0b3IgPSAnLm1vZHVsZS1idWxrLWFjdGlvbnMnO1xuICAgIHRoaXMuYnVsa0l0ZW1TZWxlY3RvciA9ICcubW9kdWxlLWJ1bGstbWVudSc7XG4gICAgdGhpcy5idWxrQWN0aW9uQ2hlY2tib3hMaXN0U2VsZWN0b3IgPSAnLm1vZHVsZS1jaGVja2JveC1idWxrLWxpc3QgaW5wdXQnO1xuICAgIHRoaXMuYnVsa0FjdGlvbkNoZWNrYm94R3JpZFNlbGVjdG9yID0gJy5tb2R1bGUtY2hlY2tib3gtYnVsay1ncmlkIGlucHV0JztcbiAgICB0aGlzLmNoZWNrZWRCdWxrQWN0aW9uTGlzdFNlbGVjdG9yID0gYCR7dGhpcy5idWxrQWN0aW9uQ2hlY2tib3hMaXN0U2VsZWN0b3J9OmNoZWNrZWRgO1xuICAgIHRoaXMuY2hlY2tlZEJ1bGtBY3Rpb25HcmlkU2VsZWN0b3IgPSBgJHt0aGlzLmJ1bGtBY3Rpb25DaGVja2JveEdyaWRTZWxlY3Rvcn06Y2hlY2tlZGA7XG4gICAgdGhpcy5idWxrQWN0aW9uQ2hlY2tib3hTZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWJ1bGstY2hlY2tib3gnO1xuICAgIHRoaXMuYnVsa0NvbmZpcm1Nb2RhbFNlbGVjdG9yID0gJyNtb2R1bGUtbW9kYWwtYnVsay1jb25maXJtJztcbiAgICB0aGlzLmJ1bGtDb25maXJtTW9kYWxBY3Rpb25OYW1lU2VsZWN0b3IgPSAnI21vZHVsZS1tb2RhbC1idWxrLWNvbmZpcm0tYWN0aW9uLW5hbWUnO1xuICAgIHRoaXMuYnVsa0NvbmZpcm1Nb2RhbExpc3RTZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWJ1bGstY29uZmlybS1saXN0JztcbiAgICB0aGlzLmJ1bGtDb25maXJtTW9kYWxBY2tCdG5TZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWNvbmZpcm0tYnVsay1hY2snO1xuXG4gICAgLy8gUGxhY2Vob2xkZXJzXG4gICAgdGhpcy5wbGFjZWhvbGRlckdsb2JhbFNlbGVjdG9yID0gJy5tb2R1bGUtcGxhY2Vob2xkZXJzLXdyYXBwZXInO1xuICAgIHRoaXMucGxhY2Vob2xkZXJGYWlsdXJlR2xvYmFsU2VsZWN0b3IgPSAnLm1vZHVsZS1wbGFjZWhvbGRlcnMtZmFpbHVyZSc7XG4gICAgdGhpcy5wbGFjZWhvbGRlckZhaWx1cmVNc2dTZWxlY3RvciA9ICcubW9kdWxlLXBsYWNlaG9sZGVycy1mYWlsdXJlLW1zZyc7XG4gICAgdGhpcy5wbGFjZWhvbGRlckZhaWx1cmVSZXRyeUJ0blNlbGVjdG9yID0gJyNtb2R1bGUtcGxhY2Vob2xkZXJzLWZhaWx1cmUtcmV0cnknO1xuXG4gICAgLy8gTW9kdWxlJ3Mgc3RhdHVzZXMgc2VsZWN0b3JzXG4gICAgdGhpcy5zdGF0dXNTZWxlY3RvckxhYmVsU2VsZWN0b3IgPSAnLm1vZHVsZS1zdGF0dXMtc2VsZWN0b3ItbGFiZWwnO1xuICAgIHRoaXMuc3RhdHVzSXRlbVNlbGVjdG9yID0gJy5tb2R1bGUtc3RhdHVzLW1lbnUnO1xuICAgIHRoaXMuc3RhdHVzUmVzZXRCdG5TZWxlY3RvciA9ICcubW9kdWxlLXN0YXR1cy1yZXNldCc7XG5cbiAgICAvLyBTZWxlY3RvcnMgZm9yIE1vZHVsZSBJbXBvcnQgYW5kIEFkZG9ucyBjb25uZWN0XG4gICAgdGhpcy5hZGRvbnNDb25uZWN0TW9kYWxCdG5TZWxlY3RvciA9ICcjcGFnZS1oZWFkZXItZGVzYy1jb25maWd1cmF0aW9uLWFkZG9uc19jb25uZWN0JztcbiAgICB0aGlzLmFkZG9uc0xvZ291dE1vZGFsQnRuU2VsZWN0b3IgPSAnI3BhZ2UtaGVhZGVyLWRlc2MtY29uZmlndXJhdGlvbi1hZGRvbnNfbG9nb3V0JztcbiAgICB0aGlzLmFkZG9uc0ltcG9ydE1vZGFsQnRuU2VsZWN0b3IgPSAnI3BhZ2UtaGVhZGVyLWRlc2MtY29uZmlndXJhdGlvbi1hZGRfbW9kdWxlJztcbiAgICB0aGlzLmRyb3Bab25lTW9kYWxTZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWltcG9ydCc7XG4gICAgdGhpcy5kcm9wWm9uZU1vZGFsRm9vdGVyU2VsZWN0b3IgPSAnI21vZHVsZS1tb2RhbC1pbXBvcnQgLm1vZGFsLWZvb3Rlcic7XG4gICAgdGhpcy5kcm9wWm9uZUltcG9ydFpvbmVTZWxlY3RvciA9ICcjaW1wb3J0RHJvcHpvbmUnO1xuICAgIHRoaXMuYWRkb25zQ29ubmVjdE1vZGFsU2VsZWN0b3IgPSAnI21vZHVsZS1tb2RhbC1hZGRvbnMtY29ubmVjdCc7XG4gICAgdGhpcy5hZGRvbnNMb2dvdXRNb2RhbFNlbGVjdG9yID0gJyNtb2R1bGUtbW9kYWwtYWRkb25zLWxvZ291dCc7XG4gICAgdGhpcy5hZGRvbnNDb25uZWN0Rm9ybSA9ICcjYWRkb25zLWNvbm5lY3QtZm9ybSc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRNb2RhbENsb3NlQnRuID0gJyNtb2R1bGUtbW9kYWwtaW1wb3J0LWNsb3NpbmctY3Jvc3MnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0U3RhcnRTZWxlY3RvciA9ICcubW9kdWxlLWltcG9ydC1zdGFydCc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRQcm9jZXNzaW5nU2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtcHJvY2Vzc2luZyc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRTdWNjZXNzU2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtc3VjY2Vzcyc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRTdWNjZXNzQ29uZmlndXJlQnRuU2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtc3VjY2Vzcy1jb25maWd1cmUnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZVNlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LWZhaWx1cmUnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZVJldHJ5U2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtZmFpbHVyZS1yZXRyeSc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRGYWlsdXJlRGV0YWlsc0J0blNlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LWZhaWx1cmUtZGV0YWlscy1hY3Rpb24nO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0U2VsZWN0RmlsZU1hbnVhbFNlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LXN0YXJ0LXNlbGVjdC1tYW51YWwnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZU1zZ0RldGFpbHNTZWxlY3RvciA9ICcubW9kdWxlLWltcG9ydC1mYWlsdXJlLWRldGFpbHMnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0Q29uZmlybVNlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LWNvbmZpcm0nO1xuXG4gICAgdGhpcy5pbml0U29ydGluZ0Ryb3Bkb3duKCk7XG4gICAgdGhpcy5pbml0Qk9FdmVudFJlZ2lzdGVyaW5nKCk7XG4gICAgdGhpcy5pbml0Q3VycmVudERpc3BsYXkoKTtcbiAgICB0aGlzLmluaXRTb3J0aW5nRGlzcGxheVN3aXRjaCgpO1xuICAgIHRoaXMuaW5pdEJ1bGtEcm9wZG93bigpO1xuICAgIHRoaXMuaW5pdFNlYXJjaEJsb2NrKCk7XG4gICAgdGhpcy5pbml0Q2F0ZWdvcnlTZWxlY3QoKTtcbiAgICB0aGlzLmluaXRDYXRlZ29yaWVzR3JpZCgpO1xuICAgIHRoaXMuaW5pdEFjdGlvbkJ1dHRvbnMoKTtcbiAgICB0aGlzLmluaXRBZGRvbnNTZWFyY2goKTtcbiAgICB0aGlzLmluaXRBZGRvbnNDb25uZWN0KCk7XG4gICAgdGhpcy5pbml0QWRkTW9kdWxlQWN0aW9uKCk7XG4gICAgdGhpcy5pbml0RHJvcHpvbmUoKTtcbiAgICB0aGlzLmluaXRQYWdlQ2hhbmdlUHJvdGVjdGlvbigpO1xuICAgIHRoaXMuaW5pdFBsYWNlaG9sZGVyTWVjaGFuaXNtKCk7XG4gICAgdGhpcy5pbml0RmlsdGVyU3RhdHVzRHJvcGRvd24oKTtcbiAgICB0aGlzLmZldGNoTW9kdWxlc0xpc3QoKTtcbiAgICB0aGlzLmdldE5vdGlmaWNhdGlvbnNDb3VudCgpO1xuICAgIHRoaXMuaW5pdGlhbGl6ZVNlZU1vcmUoKTtcbiAgfVxuXG4gIGluaXRGaWx0ZXJTdGF0dXNEcm9wZG93bigpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBib2R5ID0gJCgnYm9keScpO1xuICAgIGJvZHkub24oJ2NsaWNrJywgc2VsZi5zdGF0dXNJdGVtU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIC8vIEdldCBkYXRhIGZyb20gbGkgRE9NIGlucHV0XG4gICAgICBzZWxmLmN1cnJlbnRSZWZTdGF0dXMgPSBwYXJzZUludCgkKHRoaXMpLmRhdGEoJ3N0YXR1cy1yZWYnKSwgMTApO1xuICAgICAgLy8gQ2hhbmdlIGRyb3Bkb3duIGxhYmVsIHRvIHNldCBpdCB0byB0aGUgY3VycmVudCBzdGF0dXMnIGRpc3BsYXluYW1lXG4gICAgICAkKHNlbGYuc3RhdHVzU2VsZWN0b3JMYWJlbFNlbGVjdG9yKS50ZXh0KCQodGhpcykuZmluZCgnYTpmaXJzdCcpLnRleHQoKSk7XG4gICAgICAkKHNlbGYuc3RhdHVzUmVzZXRCdG5TZWxlY3Rvcikuc2hvdygpO1xuICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgfSk7XG5cbiAgICBib2R5Lm9uKCdjbGljaycsIHNlbGYuc3RhdHVzUmVzZXRCdG5TZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgJChzZWxmLnN0YXR1c1NlbGVjdG9yTGFiZWxTZWxlY3RvcikudGV4dCgkKHRoaXMpLmZpbmQoJ2EnKS50ZXh0KCkpO1xuICAgICAgJCh0aGlzKS5oaWRlKCk7XG4gICAgICBzZWxmLmN1cnJlbnRSZWZTdGF0dXMgPSBudWxsO1xuICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgfSk7XG4gIH1cblxuICBpbml0QnVsa0Ryb3Bkb3duKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0IGJvZHkgPSAkKCdib2R5Jyk7XG5cblxuICAgIGJvZHkub24oJ2NsaWNrJywgc2VsZi5nZXRCdWxrQ2hlY2tib3hlc1NlbGVjdG9yKCksICgpID0+IHtcbiAgICAgIGNvbnN0IHNlbGVjdG9yID0gJChzZWxmLmJ1bGtBY3Rpb25Ecm9wRG93blNlbGVjdG9yKTtcbiAgICAgIGlmICgkKHNlbGYuZ2V0QnVsa0NoZWNrYm94ZXNDaGVja2VkU2VsZWN0b3IoKSkubGVuZ3RoID4gMCkge1xuICAgICAgICBzZWxlY3Rvci5jbG9zZXN0KCcubW9kdWxlLXRvcC1tZW51LWl0ZW0nKVxuICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHNlbGVjdG9yLmNsb3Nlc3QoJy5tb2R1bGUtdG9wLW1lbnUtaXRlbScpXG4gICAgICAgICAgICAgICAgLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgYm9keS5vbignY2xpY2snLCBzZWxmLmJ1bGtJdGVtU2VsZWN0b3IsIGZ1bmN0aW9uIGluaXRpYWxpemVCb2R5Q2hhbmdlKCkge1xuICAgICAgaWYgKCQoc2VsZi5nZXRCdWxrQ2hlY2tib3hlc0NoZWNrZWRTZWxlY3RvcigpKS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgJC5ncm93bC53YXJuaW5nKHttZXNzYWdlOiB3aW5kb3cudHJhbnNsYXRlX2phdmFzY3JpcHRzWydCdWxrIEFjdGlvbiAtIE9uZSBtb2R1bGUgbWluaW11bSddfSk7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgc2VsZi5sYXN0QnVsa0FjdGlvbiA9ICQodGhpcykuZGF0YSgncmVmJyk7XG4gICAgICBjb25zdCBtb2R1bGVzTGlzdFN0cmluZyA9IHNlbGYuYnVpbGRCdWxrQWN0aW9uTW9kdWxlTGlzdCgpO1xuICAgICAgY29uc3QgYWN0aW9uU3RyaW5nID0gJCh0aGlzKS5maW5kKCc6Y2hlY2tlZCcpLnRleHQoKS50b0xvd2VyQ2FzZSgpO1xuICAgICAgJChzZWxmLmJ1bGtDb25maXJtTW9kYWxMaXN0U2VsZWN0b3IpLmh0bWwobW9kdWxlc0xpc3RTdHJpbmcpO1xuICAgICAgJChzZWxmLmJ1bGtDb25maXJtTW9kYWxBY3Rpb25OYW1lU2VsZWN0b3IpLnRleHQoYWN0aW9uU3RyaW5nKTtcblxuICAgICAgaWYgKHNlbGYubGFzdEJ1bGtBY3Rpb24gPT09ICdidWxrLXVuaW5zdGFsbCcpIHtcbiAgICAgICAgJChzZWxmLmJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdG9yKS5zaG93KCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkKHNlbGYuYnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgIH1cblxuICAgICAgJChzZWxmLmJ1bGtDb25maXJtTW9kYWxTZWxlY3RvcikubW9kYWwoJ3Nob3cnKTtcbiAgICB9KTtcblxuICAgIGJvZHkub24oJ2NsaWNrJywgdGhpcy5idWxrQ29uZmlybU1vZGFsQWNrQnRuU2VsZWN0b3IsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgJChzZWxmLmJ1bGtDb25maXJtTW9kYWxTZWxlY3RvcikubW9kYWwoJ2hpZGUnKTtcbiAgICAgIHNlbGYuZG9CdWxrQWN0aW9uKHNlbGYubGFzdEJ1bGtBY3Rpb24pO1xuICAgIH0pO1xuICB9XG5cbiAgaW5pdEJPRXZlbnRSZWdpc3RlcmluZygpIHtcbiAgICB3aW5kb3cuQk9FdmVudC5vbignTW9kdWxlIERpc2FibGVkJywgdGhpcy5vbk1vZHVsZURpc2FibGVkLCB0aGlzKTtcbiAgICB3aW5kb3cuQk9FdmVudC5vbignTW9kdWxlIFVuaW5zdGFsbGVkJywgdGhpcy51cGRhdGVUb3RhbFJlc3VsdHMsIHRoaXMpO1xuICB9XG5cbiAgb25Nb2R1bGVEaXNhYmxlZCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBtb2R1bGVJdGVtU2VsZWN0b3IgPSBzZWxmLmdldE1vZHVsZUl0ZW1TZWxlY3RvcigpO1xuXG4gICAgJCgnLm1vZHVsZXMtbGlzdCcpLmVhY2goZnVuY3Rpb24gc2Nhbk1vZHVsZXNMaXN0KCkge1xuICAgICAgc2VsZi51cGRhdGVUb3RhbFJlc3VsdHMoKTtcbiAgICB9KTtcbiAgfVxuXG4gIGluaXRQbGFjZWhvbGRlck1lY2hhbmlzbSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBpZiAoJChzZWxmLnBsYWNlaG9sZGVyR2xvYmFsU2VsZWN0b3IpLmxlbmd0aCkge1xuICAgICAgc2VsZi5hamF4TG9hZFBhZ2UoKTtcbiAgICB9XG5cbiAgICAvLyBSZXRyeSBsb2FkaW5nIG1lY2hhbmlzbVxuICAgICQoJ2JvZHknKS5vbignY2xpY2snLCBzZWxmLnBsYWNlaG9sZGVyRmFpbHVyZVJldHJ5QnRuU2VsZWN0b3IsICgpID0+IHtcbiAgICAgICQoc2VsZi5wbGFjZWhvbGRlckZhaWx1cmVHbG9iYWxTZWxlY3RvcikuZmFkZU91dCgpO1xuICAgICAgJChzZWxmLnBsYWNlaG9sZGVyR2xvYmFsU2VsZWN0b3IpLmZhZGVJbigpO1xuICAgICAgc2VsZi5hamF4TG9hZFBhZ2UoKTtcbiAgICB9KTtcbiAgfVxuXG4gIGFqYXhMb2FkUGFnZSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQuYWpheCh7XG4gICAgICBtZXRob2Q6ICdHRVQnLFxuICAgICAgdXJsOiB3aW5kb3cubW9kdWxlVVJMcy5jYXRhbG9nUmVmcmVzaCxcbiAgICB9KS5kb25lKChyZXNwb25zZSkgPT4ge1xuICAgICAgaWYgKHJlc3BvbnNlLnN0YXR1cyA9PT0gdHJ1ZSkge1xuICAgICAgICBpZiAodHlwZW9mIHJlc3BvbnNlLmRvbUVsZW1lbnRzID09PSAndW5kZWZpbmVkJykgcmVzcG9uc2UuZG9tRWxlbWVudHMgPSBudWxsO1xuICAgICAgICBpZiAodHlwZW9mIHJlc3BvbnNlLm1zZyA9PT0gJ3VuZGVmaW5lZCcpIHJlc3BvbnNlLm1zZyA9IG51bGw7XG5cbiAgICAgICAgY29uc3Qgc3R5bGVzaGVldCA9IGRvY3VtZW50LnN0eWxlU2hlZXRzWzBdO1xuICAgICAgICBjb25zdCBzdHlsZXNoZWV0UnVsZSA9ICd7ZGlzcGxheTogbm9uZX0nO1xuICAgICAgICBjb25zdCBtb2R1bGVHbG9iYWxTZWxlY3RvciA9ICcubW9kdWxlcy1saXN0JztcbiAgICAgICAgY29uc3QgbW9kdWxlU29ydGluZ1NlbGVjdG9yID0gJy5tb2R1bGUtc29ydGluZy1tZW51JztcbiAgICAgICAgY29uc3QgcmVxdWlyZWRTZWxlY3RvckNvbWJpbmF0aW9uID0gYCR7bW9kdWxlR2xvYmFsU2VsZWN0b3J9LCR7bW9kdWxlU29ydGluZ1NlbGVjdG9yfWA7XG5cbiAgICAgICAgaWYgKHN0eWxlc2hlZXQuaW5zZXJ0UnVsZSkge1xuICAgICAgICAgIHN0eWxlc2hlZXQuaW5zZXJ0UnVsZShcbiAgICAgICAgICAgIHJlcXVpcmVkU2VsZWN0b3JDb21iaW5hdGlvbiArXG4gICAgICAgICAgICBzdHlsZXNoZWV0UnVsZSwgc3R5bGVzaGVldC5jc3NSdWxlcy5sZW5ndGhcbiAgICAgICAgICApO1xuICAgICAgICB9IGVsc2UgaWYgKHN0eWxlc2hlZXQuYWRkUnVsZSkge1xuICAgICAgICAgIHN0eWxlc2hlZXQuYWRkUnVsZShcbiAgICAgICAgICAgIHJlcXVpcmVkU2VsZWN0b3JDb21iaW5hdGlvbixcbiAgICAgICAgICAgIHN0eWxlc2hlZXRSdWxlLFxuICAgICAgICAgICAgLTFcbiAgICAgICAgICApO1xuICAgICAgICB9XG5cbiAgICAgICAgJChzZWxmLnBsYWNlaG9sZGVyR2xvYmFsU2VsZWN0b3IpLmZhZGVPdXQoODAwLCAoKSA9PiB7XG4gICAgICAgICAgJC5lYWNoKHJlc3BvbnNlLmRvbUVsZW1lbnRzLCAoaW5kZXgsIGVsZW1lbnQpID0+IHtcbiAgICAgICAgICAgICQoZWxlbWVudC5zZWxlY3RvcikuYXBwZW5kKGVsZW1lbnQuY29udGVudCk7XG4gICAgICAgICAgfSk7XG4gICAgICAgICAgJChtb2R1bGVHbG9iYWxTZWxlY3RvcikuZmFkZUluKDgwMCkuY3NzKCdkaXNwbGF5JywgJ2ZsZXgnKTtcbiAgICAgICAgICAkKG1vZHVsZVNvcnRpbmdTZWxlY3RvcikuZmFkZUluKDgwMCk7XG4gICAgICAgICAgJCgnW2RhdGEtdG9nZ2xlPVwicG9wb3ZlclwiXScpLnBvcG92ZXIoKTtcbiAgICAgICAgICBzZWxmLmluaXRDdXJyZW50RGlzcGxheSgpO1xuICAgICAgICAgIHNlbGYuZmV0Y2hNb2R1bGVzTGlzdCgpO1xuICAgICAgICB9KTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICQoc2VsZi5wbGFjZWhvbGRlckdsb2JhbFNlbGVjdG9yKS5mYWRlT3V0KDgwMCwgKCkgPT4ge1xuICAgICAgICAgICQoc2VsZi5wbGFjZWhvbGRlckZhaWx1cmVNc2dTZWxlY3RvcikudGV4dChyZXNwb25zZS5tc2cpO1xuICAgICAgICAgICQoc2VsZi5wbGFjZWhvbGRlckZhaWx1cmVHbG9iYWxTZWxlY3RvcikuZmFkZUluKDgwMCk7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH0pLmZhaWwoKHJlc3BvbnNlKSA9PiB7XG4gICAgICAkKHNlbGYucGxhY2Vob2xkZXJHbG9iYWxTZWxlY3RvcikuZmFkZU91dCg4MDAsICgpID0+IHtcbiAgICAgICAgJChzZWxmLnBsYWNlaG9sZGVyRmFpbHVyZU1zZ1NlbGVjdG9yKS50ZXh0KHJlc3BvbnNlLnN0YXR1c1RleHQpO1xuICAgICAgICAkKHNlbGYucGxhY2Vob2xkZXJGYWlsdXJlR2xvYmFsU2VsZWN0b3IpLmZhZGVJbig4MDApO1xuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cblxuICBmZXRjaE1vZHVsZXNMaXN0KCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGxldCBjb250YWluZXI7XG4gICAgbGV0ICR0aGlzO1xuXG4gICAgc2VsZi5tb2R1bGVzTGlzdCA9IFtdO1xuICAgICQoJy5tb2R1bGVzLWxpc3QnKS5lYWNoKGZ1bmN0aW9uIHByZXBhcmVDb250YWluZXIoKSB7XG4gICAgICBjb250YWluZXIgPSAkKHRoaXMpO1xuICAgICAgY29udGFpbmVyLmZpbmQoJy5tb2R1bGUtaXRlbScpLmVhY2goZnVuY3Rpb24gcHJlcGFyZU1vZHVsZXMoKSB7XG4gICAgICAgICR0aGlzID0gJCh0aGlzKTtcbiAgICAgICAgc2VsZi5tb2R1bGVzTGlzdC5wdXNoKHtcbiAgICAgICAgICBkb21PYmplY3Q6ICR0aGlzLFxuICAgICAgICAgIGlkOiAkdGhpcy5kYXRhKCdpZCcpLFxuICAgICAgICAgIG5hbWU6ICR0aGlzLmRhdGEoJ25hbWUnKS50b0xvd2VyQ2FzZSgpLFxuICAgICAgICAgIHNjb3Jpbmc6IHBhcnNlRmxvYXQoJHRoaXMuZGF0YSgnc2NvcmluZycpKSxcbiAgICAgICAgICBsb2dvOiAkdGhpcy5kYXRhKCdsb2dvJyksXG4gICAgICAgICAgYXV0aG9yOiAkdGhpcy5kYXRhKCdhdXRob3InKS50b0xvd2VyQ2FzZSgpLFxuICAgICAgICAgIHZlcnNpb246ICR0aGlzLmRhdGEoJ3ZlcnNpb24nKSxcbiAgICAgICAgICBkZXNjcmlwdGlvbjogJHRoaXMuZGF0YSgnZGVzY3JpcHRpb24nKS50b0xvd2VyQ2FzZSgpLFxuICAgICAgICAgIHRlY2hOYW1lOiAkdGhpcy5kYXRhKCd0ZWNoLW5hbWUnKS50b0xvd2VyQ2FzZSgpLFxuICAgICAgICAgIGNoaWxkQ2F0ZWdvcmllczogJHRoaXMuZGF0YSgnY2hpbGQtY2F0ZWdvcmllcycpLFxuICAgICAgICAgIGNhdGVnb3JpZXM6IFN0cmluZygkdGhpcy5kYXRhKCdjYXRlZ29yaWVzJykpLnRvTG93ZXJDYXNlKCksXG4gICAgICAgICAgdHlwZTogJHRoaXMuZGF0YSgndHlwZScpLFxuICAgICAgICAgIHByaWNlOiBwYXJzZUZsb2F0KCR0aGlzLmRhdGEoJ3ByaWNlJykpLFxuICAgICAgICAgIGFjdGl2ZTogcGFyc2VJbnQoJHRoaXMuZGF0YSgnYWN0aXZlJyksIDEwKSxcbiAgICAgICAgICBhY2Nlc3M6ICR0aGlzLmRhdGEoJ2xhc3QtYWNjZXNzJyksXG4gICAgICAgICAgZGlzcGxheTogJHRoaXMuaGFzQ2xhc3MoJ21vZHVsZS1pdGVtLWxpc3QnKSA/IHNlbGYuRElTUExBWV9MSVNUIDogc2VsZi5ESVNQTEFZX0dSSUQsXG4gICAgICAgICAgY29udGFpbmVyLFxuICAgICAgICB9KTtcblxuICAgICAgICAkdGhpcy5yZW1vdmUoKTtcbiAgICAgIH0pO1xuICAgIH0pO1xuXG4gICAgc2VsZi5hZGRvbnNDYXJkR3JpZCA9ICQodGhpcy5hZGRvbkl0ZW1HcmlkU2VsZWN0b3IpO1xuICAgIHNlbGYuYWRkb25zQ2FyZExpc3QgPSAkKHRoaXMuYWRkb25JdGVtTGlzdFNlbGVjdG9yKTtcbiAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICAkKCdib2R5JykudHJpZ2dlcignbW9kdWxlQ2F0YWxvZ0xvYWRlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIFByZXBhcmUgc29ydGluZ1xuICAgKlxuICAgKi9cbiAgdXBkYXRlTW9kdWxlU29ydGluZygpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgIGlmICghc2VsZi5jdXJyZW50U29ydGluZykge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIC8vIE1vZHVsZXMgc29ydGluZ1xuICAgIGxldCBvcmRlciA9ICdhc2MnO1xuICAgIGxldCBrZXkgPSBzZWxmLmN1cnJlbnRTb3J0aW5nO1xuICAgIGNvbnN0IHNwbGl0dGVkS2V5ID0ga2V5LnNwbGl0KCctJyk7XG4gICAgaWYgKHNwbGl0dGVkS2V5Lmxlbmd0aCA+IDEpIHtcbiAgICAgIGtleSA9IHNwbGl0dGVkS2V5WzBdO1xuICAgICAgaWYgKHNwbGl0dGVkS2V5WzFdID09PSAnZGVzYycpIHtcbiAgICAgICAgb3JkZXIgPSAnZGVzYyc7XG4gICAgICB9XG4gICAgfVxuXG4gICAgY29uc3QgY3VycmVudENvbXBhcmUgPSAoYSwgYikgPT4ge1xuICAgICAgbGV0IGFEYXRhID0gYVtrZXldO1xuICAgICAgbGV0IGJEYXRhID0gYltrZXldO1xuICAgICAgaWYgKGtleSA9PT0gJ2FjY2VzcycpIHtcbiAgICAgICAgYURhdGEgPSAobmV3IERhdGUoYURhdGEpKS5nZXRUaW1lKCk7XG4gICAgICAgIGJEYXRhID0gKG5ldyBEYXRlKGJEYXRhKSkuZ2V0VGltZSgpO1xuICAgICAgICBhRGF0YSA9IGlzTmFOKGFEYXRhKSA/IDAgOiBhRGF0YTtcbiAgICAgICAgYkRhdGEgPSBpc05hTihiRGF0YSkgPyAwIDogYkRhdGE7XG4gICAgICAgIGlmIChhRGF0YSA9PT0gYkRhdGEpIHtcbiAgICAgICAgICByZXR1cm4gYi5uYW1lLmxvY2FsZUNvbXBhcmUoYS5uYW1lKTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICBpZiAoYURhdGEgPCBiRGF0YSkgcmV0dXJuIC0xO1xuICAgICAgaWYgKGFEYXRhID4gYkRhdGEpIHJldHVybiAxO1xuXG4gICAgICByZXR1cm4gMDtcbiAgICB9O1xuXG4gICAgc2VsZi5tb2R1bGVzTGlzdC5zb3J0KGN1cnJlbnRDb21wYXJlKTtcbiAgICBpZiAob3JkZXIgPT09ICdkZXNjJykge1xuICAgICAgc2VsZi5tb2R1bGVzTGlzdC5yZXZlcnNlKCk7XG4gICAgfVxuICB9XG5cbiAgdXBkYXRlTW9kdWxlQ29udGFpbmVyRGlzcGxheSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoJy5tb2R1bGUtc2hvcnQtbGlzdCcpLmVhY2goZnVuY3Rpb24gc2V0U2hvcnRMaXN0VmlzaWJpbGl0eSgpIHtcbiAgICAgIGNvbnN0IGNvbnRhaW5lciA9ICQodGhpcyk7XG4gICAgICBjb25zdCBuYk1vZHVsZXNJbkNvbnRhaW5lciA9IGNvbnRhaW5lci5maW5kKCcubW9kdWxlLWl0ZW0nKS5sZW5ndGg7XG4gICAgICBpZiAoXG4gICAgICAgIChcbiAgICAgICAgICBzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeVxuICAgICAgICAgICYmIHNlbGYuY3VycmVudFJlZkNhdGVnb3J5ICE9PSBTdHJpbmcoY29udGFpbmVyLmZpbmQoJy5tb2R1bGVzLWxpc3QnKS5kYXRhKCduYW1lJykpXG4gICAgICAgICkgfHwgKFxuICAgICAgICAgIHNlbGYuY3VycmVudFJlZlN0YXR1cyAhPT0gbnVsbFxuICAgICAgICAgICYmIG5iTW9kdWxlc0luQ29udGFpbmVyID09PSAwXG4gICAgICAgICkgfHwgKFxuICAgICAgICAgIG5iTW9kdWxlc0luQ29udGFpbmVyID09PSAwXG4gICAgICAgICAgJiYgU3RyaW5nKGNvbnRhaW5lci5maW5kKCcubW9kdWxlcy1saXN0JykuZGF0YSgnbmFtZScpKSA9PT0gc2VsZi5DQVRFR09SWV9SRUNFTlRMWV9VU0VEXG4gICAgICAgICkgfHwgKFxuICAgICAgICAgIHNlbGYuY3VycmVudFRhZ3NMaXN0Lmxlbmd0aCA+IDBcbiAgICAgICAgICAmJiBuYk1vZHVsZXNJbkNvbnRhaW5lciA9PT0gMFxuICAgICAgICApXG4gICAgICApIHtcbiAgICAgICAgY29udGFpbmVyLmhpZGUoKTtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBjb250YWluZXIuc2hvdygpO1xuICAgICAgaWYgKG5iTW9kdWxlc0luQ29udGFpbmVyID49IHNlbGYuREVGQVVMVF9NQVhfUEVSX0NBVEVHT1JJRVMpIHtcbiAgICAgICAgY29udGFpbmVyLmZpbmQoYCR7c2VsZi5zZWVNb3JlU2VsZWN0b3J9LCAke3NlbGYuc2VlTGVzc1NlbGVjdG9yfWApLnNob3coKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGNvbnRhaW5lci5maW5kKGAke3NlbGYuc2VlTW9yZVNlbGVjdG9yfSwgJHtzZWxmLnNlZUxlc3NTZWxlY3Rvcn1gKS5oaWRlKCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICB1cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgc2VsZi51cGRhdGVNb2R1bGVTb3J0aW5nKCk7XG5cbiAgICAkKHNlbGYucmVjZW50bHlVc2VkU2VsZWN0b3IpLmZpbmQoJy5tb2R1bGUtaXRlbScpLnJlbW92ZSgpO1xuICAgICQoJy5tb2R1bGVzLWxpc3QnKS5maW5kKCcubW9kdWxlLWl0ZW0nKS5yZW1vdmUoKTtcblxuICAgIC8vIE1vZHVsZXMgdmlzaWJpbGl0eSBtYW5hZ2VtZW50XG4gICAgbGV0IGlzVmlzaWJsZTtcbiAgICBsZXQgY3VycmVudE1vZHVsZTtcbiAgICBsZXQgbW9kdWxlQ2F0ZWdvcnk7XG4gICAgbGV0IHRhZ0V4aXN0cztcbiAgICBsZXQgbmV3VmFsdWU7XG5cbiAgICBjb25zdCBtb2R1bGVzTGlzdExlbmd0aCA9IHNlbGYubW9kdWxlc0xpc3QubGVuZ3RoO1xuICAgIGNvbnN0IGNvdW50ZXIgPSB7fTtcblxuICAgIGZvciAobGV0IGkgPSAwOyBpIDwgbW9kdWxlc0xpc3RMZW5ndGg7IGkgKz0gMSkge1xuICAgICAgY3VycmVudE1vZHVsZSA9IHNlbGYubW9kdWxlc0xpc3RbaV07XG4gICAgICBpZiAoY3VycmVudE1vZHVsZS5kaXNwbGF5ID09PSBzZWxmLmN1cnJlbnREaXNwbGF5KSB7XG4gICAgICAgIGlzVmlzaWJsZSA9IHRydWU7XG5cbiAgICAgICAgbW9kdWxlQ2F0ZWdvcnkgPSBzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeSA9PT0gc2VsZi5DQVRFR09SWV9SRUNFTlRMWV9VU0VEID9cbiAgICAgICAgICAgICAgICAgICAgICAgICBzZWxmLkNBVEVHT1JZX1JFQ0VOVExZX1VTRUQgOlxuICAgICAgICAgICAgICAgICAgICAgICAgIGN1cnJlbnRNb2R1bGUuY2F0ZWdvcmllcztcblxuICAgICAgICAvLyBDaGVjayBmb3Igc2FtZSBjYXRlZ29yeVxuICAgICAgICBpZiAoc2VsZi5jdXJyZW50UmVmQ2F0ZWdvcnkgIT09IG51bGwpIHtcbiAgICAgICAgICBpc1Zpc2libGUgJj0gbW9kdWxlQ2F0ZWdvcnkgPT09IHNlbGYuY3VycmVudFJlZkNhdGVnb3J5O1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gQ2hlY2sgZm9yIHNhbWUgc3RhdHVzXG4gICAgICAgIGlmIChzZWxmLmN1cnJlbnRSZWZTdGF0dXMgIT09IG51bGwpIHtcbiAgICAgICAgICBpc1Zpc2libGUgJj0gY3VycmVudE1vZHVsZS5hY3RpdmUgPT09IHNlbGYuY3VycmVudFJlZlN0YXR1cztcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIENoZWNrIGZvciB0YWcgbGlzdFxuICAgICAgICBpZiAoc2VsZi5jdXJyZW50VGFnc0xpc3QubGVuZ3RoKSB7XG4gICAgICAgICAgdGFnRXhpc3RzID0gZmFsc2U7XG4gICAgICAgICAgJC5lYWNoKHNlbGYuY3VycmVudFRhZ3NMaXN0LCAoaW5kZXgsIHZhbHVlKSA9PiB7XG4gICAgICAgICAgICBuZXdWYWx1ZSA9IHZhbHVlLnRvTG93ZXJDYXNlKCk7XG4gICAgICAgICAgICB0YWdFeGlzdHMgfD0gKFxuICAgICAgICAgICAgICBjdXJyZW50TW9kdWxlLm5hbWUuaW5kZXhPZihuZXdWYWx1ZSkgIT09IC0xXG4gICAgICAgICAgICAgIHx8IGN1cnJlbnRNb2R1bGUuZGVzY3JpcHRpb24uaW5kZXhPZihuZXdWYWx1ZSkgIT09IC0xXG4gICAgICAgICAgICAgIHx8IGN1cnJlbnRNb2R1bGUuYXV0aG9yLmluZGV4T2YobmV3VmFsdWUpICE9PSAtMVxuICAgICAgICAgICAgICB8fCBjdXJyZW50TW9kdWxlLnRlY2hOYW1lLmluZGV4T2YobmV3VmFsdWUpICE9PSAtMVxuICAgICAgICAgICAgKTtcbiAgICAgICAgICB9KTtcbiAgICAgICAgICBpc1Zpc2libGUgJj0gdGFnRXhpc3RzO1xuICAgICAgICB9XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIElmIGxpc3QgZGlzcGxheSB3aXRob3V0IHNlYXJjaCB3ZSBtdXN0IGRpc3BsYXkgb25seSB0aGUgZmlyc3QgNSBtb2R1bGVzXG4gICAgICAgICAqL1xuICAgICAgICBpZiAoc2VsZi5jdXJyZW50RGlzcGxheSA9PT0gc2VsZi5ESVNQTEFZX0xJU1QgJiYgIXNlbGYuY3VycmVudFRhZ3NMaXN0Lmxlbmd0aCkge1xuICAgICAgICAgIGlmIChzZWxmLmN1cnJlbnRDYXRlZ29yeURpc3BsYXlbbW9kdWxlQ2F0ZWdvcnldID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgIHNlbGYuY3VycmVudENhdGVnb3J5RGlzcGxheVttb2R1bGVDYXRlZ29yeV0gPSBmYWxzZTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBpZiAoIWNvdW50ZXJbbW9kdWxlQ2F0ZWdvcnldKSB7XG4gICAgICAgICAgICBjb3VudGVyW21vZHVsZUNhdGVnb3J5XSA9IDA7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgaWYgKG1vZHVsZUNhdGVnb3J5ID09PSBzZWxmLkNBVEVHT1JZX1JFQ0VOVExZX1VTRUQpIHtcbiAgICAgICAgICAgIGlmIChjb3VudGVyW21vZHVsZUNhdGVnb3J5XSA+PSBzZWxmLkRFRkFVTFRfTUFYX1JFQ0VOVExZX1VTRUQpIHtcbiAgICAgICAgICAgICAgaXNWaXNpYmxlICY9IHNlbGYuY3VycmVudENhdGVnb3J5RGlzcGxheVttb2R1bGVDYXRlZ29yeV07XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfSBlbHNlIGlmIChjb3VudGVyW21vZHVsZUNhdGVnb3J5XSA+PSBzZWxmLkRFRkFVTFRfTUFYX1BFUl9DQVRFR09SSUVTKSB7XG4gICAgICAgICAgICBpc1Zpc2libGUgJj0gc2VsZi5jdXJyZW50Q2F0ZWdvcnlEaXNwbGF5W21vZHVsZUNhdGVnb3J5XTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBjb3VudGVyW21vZHVsZUNhdGVnb3J5XSArPSAxO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gSWYgdmlzaWJsZSwgZGlzcGxheSAoVGh4IGNhcHRhaW4gb2J2aW91cylcbiAgICAgICAgaWYgKGlzVmlzaWJsZSkge1xuICAgICAgICAgIGlmIChzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeSA9PT0gc2VsZi5DQVRFR09SWV9SRUNFTlRMWV9VU0VEKSB7XG4gICAgICAgICAgICAkKHNlbGYucmVjZW50bHlVc2VkU2VsZWN0b3IpLmFwcGVuZChjdXJyZW50TW9kdWxlLmRvbU9iamVjdCk7XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGN1cnJlbnRNb2R1bGUuY29udGFpbmVyLmFwcGVuZChjdXJyZW50TW9kdWxlLmRvbU9iamVjdCk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG4gICAgfVxuXG4gICAgc2VsZi51cGRhdGVNb2R1bGVDb250YWluZXJEaXNwbGF5KCk7XG5cbiAgICBpZiAoc2VsZi5jdXJyZW50VGFnc0xpc3QubGVuZ3RoKSB7XG4gICAgICAkKCcubW9kdWxlcy1saXN0JykuYXBwZW5kKHRoaXMuY3VycmVudERpc3BsYXkgPT09IHNlbGYuRElTUExBWV9HUklEID8gdGhpcy5hZGRvbnNDYXJkR3JpZCA6IHRoaXMuYWRkb25zQ2FyZExpc3QpO1xuICAgIH1cblxuICAgIHNlbGYudXBkYXRlVG90YWxSZXN1bHRzKCk7XG4gIH1cblxuICBpbml0UGFnZUNoYW5nZVByb3RlY3Rpb24oKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAkKHdpbmRvdykub24oJ2JlZm9yZXVubG9hZCcsICgpID0+IHtcbiAgICAgIGlmIChzZWxmLmlzVXBsb2FkU3RhcnRlZCA9PT0gdHJ1ZSkge1xuICAgICAgICByZXR1cm4gJ0l0IHNlZW1zIHNvbWUgY3JpdGljYWwgb3BlcmF0aW9uIGFyZSBydW5uaW5nLCBhcmUgeW91IHN1cmUgeW91IHdhbnQgdG8gY2hhbmdlIHBhZ2UgPyBJdCBtaWdodCBjYXVzZSBzb21lIHVuZXhlcGN0ZWQgYmVoYXZpb3JzLic7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuXG4gIGJ1aWxkQnVsa0FjdGlvbk1vZHVsZUxpc3QoKSB7XG4gICAgY29uc3QgY2hlY2tCb3hlc1NlbGVjdG9yID0gdGhpcy5nZXRCdWxrQ2hlY2tib3hlc0NoZWNrZWRTZWxlY3RvcigpO1xuICAgIGNvbnN0IG1vZHVsZUl0ZW1TZWxlY3RvciA9IHRoaXMuZ2V0TW9kdWxlSXRlbVNlbGVjdG9yKCk7XG4gICAgbGV0IGFscmVhZHlEb25lRmxhZyA9IDA7XG4gICAgbGV0IGh0bWxHZW5lcmF0ZWQgPSAnJztcbiAgICBsZXQgY3VycmVudEVsZW1lbnQ7XG5cbiAgICAkKGNoZWNrQm94ZXNTZWxlY3RvcikuZWFjaChmdW5jdGlvbiBwcmVwYXJlQ2hlY2tib3hlcygpIHtcbiAgICAgIGlmIChhbHJlYWR5RG9uZUZsYWcgPT09IDEwKSB7XG4gICAgICAgIC8vIEJyZWFrIGVhY2hcbiAgICAgICAgaHRtbEdlbmVyYXRlZCArPSAnLSAuLi4nO1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICB9XG5cbiAgICAgIGN1cnJlbnRFbGVtZW50ID0gJCh0aGlzKS5jbG9zZXN0KG1vZHVsZUl0ZW1TZWxlY3Rvcik7XG4gICAgICBodG1sR2VuZXJhdGVkICs9IGAtICR7Y3VycmVudEVsZW1lbnQuZGF0YSgnbmFtZScpfTxici8+YDtcbiAgICAgIGFscmVhZHlEb25lRmxhZyArPSAxO1xuXG4gICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9KTtcblxuICAgIHJldHVybiBodG1sR2VuZXJhdGVkO1xuICB9XG5cbiAgaW5pdEFkZG9uc0Nvbm5lY3QoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAvLyBNYWtlIGFkZG9ucyBjb25uZWN0IG1vZGFsIHJlYWR5IHRvIGJlIGNsaWNrZWRcbiAgICBpZiAoJChzZWxmLmFkZG9uc0Nvbm5lY3RNb2RhbEJ0blNlbGVjdG9yKS5hdHRyKCdocmVmJykgPT09ICcjJykge1xuICAgICAgJChzZWxmLmFkZG9uc0Nvbm5lY3RNb2RhbEJ0blNlbGVjdG9yKS5hdHRyKCdkYXRhLXRvZ2dsZScsICdtb2RhbCcpO1xuICAgICAgJChzZWxmLmFkZG9uc0Nvbm5lY3RNb2RhbEJ0blNlbGVjdG9yKS5hdHRyKCdkYXRhLXRhcmdldCcsIHNlbGYuYWRkb25zQ29ubmVjdE1vZGFsU2VsZWN0b3IpO1xuICAgIH1cblxuICAgIGlmICgkKHNlbGYuYWRkb25zTG9nb3V0TW9kYWxCdG5TZWxlY3RvcikuYXR0cignaHJlZicpID09PSAnIycpIHtcbiAgICAgICQoc2VsZi5hZGRvbnNMb2dvdXRNb2RhbEJ0blNlbGVjdG9yKS5hdHRyKCdkYXRhLXRvZ2dsZScsICdtb2RhbCcpO1xuICAgICAgJChzZWxmLmFkZG9uc0xvZ291dE1vZGFsQnRuU2VsZWN0b3IpLmF0dHIoJ2RhdGEtdGFyZ2V0Jywgc2VsZi5hZGRvbnNMb2dvdXRNb2RhbFNlbGVjdG9yKTtcbiAgICB9XG5cbiAgICAkKCdib2R5Jykub24oJ3N1Ym1pdCcsIHNlbGYuYWRkb25zQ29ubmVjdEZvcm0sIGZ1bmN0aW9uIGluaXRpYWxpemVCb2R5U3VibWl0KGV2ZW50KSB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICQuYWpheCh7XG4gICAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgICAgICB1cmw6ICQodGhpcykuYXR0cignYWN0aW9uJyksXG4gICAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICAgIGRhdGE6ICQodGhpcykuc2VyaWFsaXplKCksXG4gICAgICAgIGJlZm9yZVNlbmQ6ICgpID0+IHtcbiAgICAgICAgICAkKHNlbGYuYWRkb25zTG9naW5CdXR0b25TZWxlY3Rvcikuc2hvdygpO1xuICAgICAgICAgICQoJ2J1dHRvbi5idG5bdHlwZT1cInN1Ym1pdFwiXScsIHNlbGYuYWRkb25zQ29ubmVjdEZvcm0pLmhpZGUoKTtcbiAgICAgICAgfVxuICAgICAgfSkuZG9uZSgocmVzcG9uc2UpID0+IHtcbiAgICAgICAgaWYgKHJlc3BvbnNlLnN1Y2Nlc3MgPT09IDEpIHtcbiAgICAgICAgICBsb2NhdGlvbi5yZWxvYWQoKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAkLmdyb3dsLmVycm9yKHttZXNzYWdlOiByZXNwb25zZS5tZXNzYWdlfSk7XG4gICAgICAgICAgJChzZWxmLmFkZG9uc0xvZ2luQnV0dG9uU2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgICAgICAkKCdidXR0b24uYnRuW3R5cGU9XCJzdWJtaXRcIl0nLCBzZWxmLmFkZG9uc0Nvbm5lY3RGb3JtKS5mYWRlSW4oKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cblxuICBpbml0QWRkTW9kdWxlQWN0aW9uKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0IGFkZE1vZHVsZUJ1dHRvbiA9ICQoc2VsZi5hZGRvbnNJbXBvcnRNb2RhbEJ0blNlbGVjdG9yKTtcbiAgICBhZGRNb2R1bGVCdXR0b24uYXR0cignZGF0YS10b2dnbGUnLCAnbW9kYWwnKTtcbiAgICBhZGRNb2R1bGVCdXR0b24uYXR0cignZGF0YS10YXJnZXQnLCBzZWxmLmRyb3Bab25lTW9kYWxTZWxlY3Rvcik7XG4gIH1cblxuICBpbml0RHJvcHpvbmUoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgY29uc3QgYm9keSA9ICQoJ2JvZHknKTtcbiAgICBjb25zdCBkcm9wem9uZSA9ICQoJy5kcm9wem9uZScpO1xuXG4gICAgLy8gUmVzZXQgbW9kYWwgd2hlbiBjbGljayBvbiBSZXRyeSBpbiBjYXNlIG9mIGZhaWx1cmVcbiAgICBib2R5Lm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZVJldHJ5U2VsZWN0b3IsXG4gICAgICAoKSA9PiB7XG4gICAgICAgICQoYCR7c2VsZi5tb2R1bGVJbXBvcnRTdWNjZXNzU2VsZWN0b3J9LCR7c2VsZi5tb2R1bGVJbXBvcnRGYWlsdXJlU2VsZWN0b3J9LCR7c2VsZi5tb2R1bGVJbXBvcnRQcm9jZXNzaW5nU2VsZWN0b3J9YCkuZmFkZU91dCgoKSA9PiB7XG4gICAgICAgICAgLyoqXG4gICAgICAgICAgICogQWRkZWQgdGltZW91dCBmb3IgYSBiZXR0ZXIgcmVuZGVyIG9mIGFuaW1hdGlvblxuICAgICAgICAgICAqIGFuZCBhdm9pZCB0byBoYXZlIGRpc3BsYXllZCBhdCB0aGUgc2FtZSB0aW1lXG4gICAgICAgICAgICovXG4gICAgICAgICAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3RhcnRTZWxlY3RvcikuZmFkZUluKCgpID0+IHtcbiAgICAgICAgICAgICAgJChzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVNc2dEZXRhaWxzU2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgICAgICAgICAgJChzZWxmLm1vZHVsZUltcG9ydFN1Y2Nlc3NDb25maWd1cmVCdG5TZWxlY3RvcikuaGlkZSgpO1xuICAgICAgICAgICAgICBkcm9wem9uZS5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgfSwgNTUwKTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgKTtcblxuICAgIC8vIFJlaW5pdCBtb2RhbCBvbiBleGl0LCBidXQgY2hlY2sgaWYgbm90IGFscmVhZHkgcHJvY2Vzc2luZyBzb21ldGhpbmdcbiAgICBib2R5Lm9uKCdoaWRkZW4uYnMubW9kYWwnLCB0aGlzLmRyb3Bab25lTW9kYWxTZWxlY3RvciwgKCkgPT4ge1xuICAgICAgJChgJHtzZWxmLm1vZHVsZUltcG9ydFN1Y2Nlc3NTZWxlY3Rvcn0sICR7c2VsZi5tb2R1bGVJbXBvcnRGYWlsdXJlU2VsZWN0b3J9YCkuaGlkZSgpO1xuICAgICAgJChzZWxmLm1vZHVsZUltcG9ydFN0YXJ0U2VsZWN0b3IpLnNob3coKTtcblxuICAgICAgZHJvcHpvbmUucmVtb3ZlQXR0cignc3R5bGUnKTtcbiAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRGYWlsdXJlTXNnRGV0YWlsc1NlbGVjdG9yKS5oaWRlKCk7XG4gICAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3VjY2Vzc0NvbmZpZ3VyZUJ0blNlbGVjdG9yKS5oaWRlKCk7XG4gICAgICAkKHNlbGYuZHJvcFpvbmVNb2RhbEZvb3RlclNlbGVjdG9yKS5odG1sKCcnKTtcbiAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRDb25maXJtU2VsZWN0b3IpLmhpZGUoKTtcbiAgICB9KTtcblxuICAgIC8vIENoYW5nZSB0aGUgd2F5IERyb3B6b25lLmpzIGxpYiBoYW5kbGUgZmlsZSBpbnB1dCB0cmlnZ2VyXG4gICAgYm9keS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICBgLmRyb3B6b25lOm5vdCgke3RoaXMubW9kdWxlSW1wb3J0U2VsZWN0RmlsZU1hbnVhbFNlbGVjdG9yfSwgJHt0aGlzLm1vZHVsZUltcG9ydFN1Y2Nlc3NDb25maWd1cmVCdG5TZWxlY3Rvcn0pYCxcbiAgICAgIChldmVudCwgbWFudWFsU2VsZWN0KSA9PiB7XG4gICAgICAgIC8vIGlmIGNsaWNrIGNvbWVzIGZyb20gLm1vZHVsZS1pbXBvcnQtc3RhcnQtc2VsZWN0LW1hbnVhbCwgc3RvcCBldmVyeXRoaW5nXG4gICAgICAgIGlmICh0eXBlb2YgbWFudWFsU2VsZWN0ID09PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICApO1xuXG4gICAgYm9keS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUltcG9ydFNlbGVjdEZpbGVNYW51YWxTZWxlY3RvciwgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAvKipcbiAgICAgICAqIFRyaWdnZXIgY2xpY2sgb24gaGlkZGVuIGZpbGUgaW5wdXQsIGFuZCBwYXNzIGV4dHJhIGRhdGFcbiAgICAgICAqIHRvIC5kcm9wem9uZSBjbGljayBoYW5kbGVyIGZybyBpdCB0byBub3RpY2UgaXQgY29tZXMgZnJvbSBoZXJlXG4gICAgICAgKi9cbiAgICAgICQoJy5kei1oaWRkZW4taW5wdXQnKS50cmlnZ2VyKCdjbGljaycsIFsnbWFudWFsX3NlbGVjdCddKTtcbiAgICB9KTtcblxuICAgIC8vIEhhbmRsZSBtb2RhbCBjbG9zdXJlXG4gICAgYm9keS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUltcG9ydE1vZGFsQ2xvc2VCdG4sICgpID0+IHtcbiAgICAgIGlmIChzZWxmLmlzVXBsb2FkU3RhcnRlZCAhPT0gdHJ1ZSkge1xuICAgICAgICAkKHNlbGYuZHJvcFpvbmVNb2RhbFNlbGVjdG9yKS5tb2RhbCgnaGlkZScpO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgLy8gRml4IGlzc3VlIG9uIGNsaWNrIGNvbmZpZ3VyZSBidXR0b25cbiAgICBib2R5Lm9uKCdjbGljaycsIHRoaXMubW9kdWxlSW1wb3J0U3VjY2Vzc0NvbmZpZ3VyZUJ0blNlbGVjdG9yLCBmdW5jdGlvbiBpbml0aWFsaXplQm9keUNsaWNrT25Nb2R1bGVJbXBvcnQoZXZlbnQpIHtcbiAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIHdpbmRvdy5sb2NhdGlvbiA9ICQodGhpcykuYXR0cignaHJlZicpO1xuICAgIH0pO1xuXG4gICAgLy8gT3BlbiBmYWlsdXJlIG1lc3NhZ2UgZGV0YWlscyBib3hcbiAgICBib2R5Lm9uKCdjbGljaycsIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZURldGFpbHNCdG5TZWxlY3RvciwgKCkgPT4ge1xuICAgICAgJChzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVNc2dEZXRhaWxzU2VsZWN0b3IpLnNsaWRlRG93bigpO1xuICAgIH0pO1xuXG4gICAgLy8gQHNlZTogZHJvcHpvbmUuanNcbiAgICBjb25zdCBkcm9wem9uZU9wdGlvbnMgPSB7XG4gICAgICB1cmw6IHdpbmRvdy5tb2R1bGVVUkxzLm1vZHVsZUltcG9ydCxcbiAgICAgIGFjY2VwdGVkRmlsZXM6ICcuemlwLCAudGFyJyxcbiAgICAgIC8vIFRoZSBuYW1lIHRoYXQgd2lsbCBiZSB1c2VkIHRvIHRyYW5zZmVyIHRoZSBmaWxlXG4gICAgICBwYXJhbU5hbWU6ICdmaWxlX3VwbG9hZGVkJyxcbiAgICAgIG1heEZpbGVzaXplOiA1MCwgLy8gY2FuJ3QgYmUgZ3JlYXRlciB0aGFuIDUwTWIgYmVjYXVzZSBpdCdzIGFuIGFkZG9ucyBsaW1pdGF0aW9uXG4gICAgICB1cGxvYWRNdWx0aXBsZTogZmFsc2UsXG4gICAgICBhZGRSZW1vdmVMaW5rczogdHJ1ZSxcbiAgICAgIGRpY3REZWZhdWx0TWVzc2FnZTogJycsXG4gICAgICBoaWRkZW5JbnB1dENvbnRhaW5lcjogc2VsZi5kcm9wWm9uZUltcG9ydFpvbmVTZWxlY3RvcixcbiAgICAgIC8qKlxuICAgICAgICogQWRkIHVubGltaXRlZCB0aW1lb3V0LiBPdGhlcndpc2UgZHJvcHpvbmUgdGltZW91dCBpcyAzMCBzZWNvbmRzXG4gICAgICAgKiAgYW5kIGlmIGEgbW9kdWxlIGlzIGxvbmcgdG8gaW5zdGFsbCwgaXQgaXMgbm90IHBvc3NpYmxlIHRvIGluc3RhbGwgdGhlIG1vZHVsZS5cbiAgICAgICAqL1xuICAgICAgdGltZW91dDogMCxcbiAgICAgIGFkZGVkZmlsZTogKCkgPT4ge1xuICAgICAgICBzZWxmLmFuaW1hdGVTdGFydFVwbG9hZCgpO1xuICAgICAgfSxcbiAgICAgIHByb2Nlc3Npbmc6ICgpID0+IHtcbiAgICAgICAgLy8gTGVhdmUgaXQgZW1wdHkgc2luY2Ugd2UgZG9uJ3QgcmVxdWlyZSBhbnl0aGluZyB3aGlsZSBwcm9jZXNzaW5nIHVwbG9hZFxuICAgICAgfSxcbiAgICAgIGVycm9yOiAoZmlsZSwgbWVzc2FnZSkgPT4ge1xuICAgICAgICBzZWxmLmRpc3BsYXlPblVwbG9hZEVycm9yKG1lc3NhZ2UpO1xuICAgICAgfSxcbiAgICAgIGNvbXBsZXRlOiAoZmlsZSkgPT4ge1xuICAgICAgICBpZiAoZmlsZS5zdGF0dXMgIT09ICdlcnJvcicpIHtcbiAgICAgICAgICBjb25zdCByZXNwb25zZU9iamVjdCA9ICQucGFyc2VKU09OKGZpbGUueGhyLnJlc3BvbnNlKTtcbiAgICAgICAgICBpZiAodHlwZW9mIHJlc3BvbnNlT2JqZWN0LmlzX2NvbmZpZ3VyYWJsZSA9PT0gJ3VuZGVmaW5lZCcpIHJlc3BvbnNlT2JqZWN0LmlzX2NvbmZpZ3VyYWJsZSA9IG51bGw7XG4gICAgICAgICAgaWYgKHR5cGVvZiByZXNwb25zZU9iamVjdC5tb2R1bGVfbmFtZSA9PT0gJ3VuZGVmaW5lZCcpIHJlc3BvbnNlT2JqZWN0Lm1vZHVsZV9uYW1lID0gbnVsbDtcblxuICAgICAgICAgIHNlbGYuZGlzcGxheU9uVXBsb2FkRG9uZShyZXNwb25zZU9iamVjdCk7XG4gICAgICAgIH1cbiAgICAgICAgLy8gU3RhdGUgdGhhdCB3ZSBoYXZlIGZpbmlzaCB0aGUgcHJvY2VzcyB0byB1bmxvY2sgc29tZSBhY3Rpb25zXG4gICAgICAgIHNlbGYuaXNVcGxvYWRTdGFydGVkID0gZmFsc2U7XG4gICAgICB9LFxuICAgIH07XG5cbiAgICBkcm9wem9uZS5kcm9wem9uZSgkLmV4dGVuZChkcm9wem9uZU9wdGlvbnMpKTtcbiAgfVxuXG4gIGFuaW1hdGVTdGFydFVwbG9hZCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBkcm9wem9uZSA9ICQoJy5kcm9wem9uZScpO1xuICAgIC8vIFN0YXRlIHRoYXQgd2Ugc3RhcnQgbW9kdWxlIHVwbG9hZFxuICAgIHNlbGYuaXNVcGxvYWRTdGFydGVkID0gdHJ1ZTtcbiAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3RhcnRTZWxlY3RvcikuaGlkZSgwKTtcbiAgICBkcm9wem9uZS5jc3MoJ2JvcmRlcicsICdub25lJyk7XG4gICAgJChzZWxmLm1vZHVsZUltcG9ydFByb2Nlc3NpbmdTZWxlY3RvcikuZmFkZUluKCk7XG4gIH1cblxuICBhbmltYXRlRW5kVXBsb2FkKGNhbGxiYWNrKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgJChzZWxmLm1vZHVsZUltcG9ydFByb2Nlc3NpbmdTZWxlY3RvcikuZmluaXNoKCkuZmFkZU91dChjYWxsYmFjayk7XG4gIH1cblxuICAvKipcbiAgICogTWV0aG9kIHRvIGNhbGwgZm9yIHVwbG9hZCBtb2RhbCwgd2hlbiB0aGUgYWpheCBjYWxsIHdlbnQgd2VsbC5cbiAgICpcbiAgICogQHBhcmFtIG9iamVjdCByZXN1bHQgY29udGFpbmluZyB0aGUgc2VydmVyIHJlc3BvbnNlXG4gICAqL1xuICBkaXNwbGF5T25VcGxvYWREb25lKHJlc3VsdCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIHNlbGYuYW5pbWF0ZUVuZFVwbG9hZCgoKSA9PiB7XG4gICAgICBpZiAocmVzdWx0LnN0YXR1cyA9PT0gdHJ1ZSkge1xuICAgICAgICBpZiAocmVzdWx0LmlzX2NvbmZpZ3VyYWJsZSA9PT0gdHJ1ZSkge1xuICAgICAgICAgIGNvbnN0IGNvbmZpZ3VyZUxpbmsgPSB3aW5kb3cubW9kdWxlVVJMcy5jb25maWd1cmF0aW9uUGFnZS5yZXBsYWNlKC86bnVtYmVyOi8sIHJlc3VsdC5tb2R1bGVfbmFtZSk7XG4gICAgICAgICAgJChzZWxmLm1vZHVsZUltcG9ydFN1Y2Nlc3NDb25maWd1cmVCdG5TZWxlY3RvcikuYXR0cignaHJlZicsIGNvbmZpZ3VyZUxpbmspO1xuICAgICAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRTdWNjZXNzQ29uZmlndXJlQnRuU2VsZWN0b3IpLnNob3coKTtcbiAgICAgICAgfVxuICAgICAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3VjY2Vzc1NlbGVjdG9yKS5mYWRlSW4oKTtcbiAgICAgIH0gZWxzZSBpZiAodHlwZW9mIHJlc3VsdC5jb25maXJtYXRpb25fc3ViamVjdCAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgc2VsZi5kaXNwbGF5UHJlc3RhVHJ1c3RTdGVwKHJlc3VsdCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkKHNlbGYubW9kdWxlSW1wb3J0RmFpbHVyZU1zZ0RldGFpbHNTZWxlY3RvcikuaHRtbChyZXN1bHQubXNnKTtcbiAgICAgICAgJChzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVTZWxlY3RvcikuZmFkZUluKCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTWV0aG9kIHRvIGNhbGwgZm9yIHVwbG9hZCBtb2RhbCwgd2hlbiB0aGUgYWpheCBjYWxsIHdlbnQgd3Jvbmcgb3Igd2hlbiB0aGUgYWN0aW9uIHJlcXVlc3RlZCBjb3VsZCBub3RcbiAgICogc3VjY2VlZCBmb3Igc29tZSByZWFzb24uXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgbWVzc2FnZSBleHBsYWluaW5nIHRoZSBlcnJvci5cbiAgICovXG4gIGRpc3BsYXlPblVwbG9hZEVycm9yKG1lc3NhZ2UpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBzZWxmLmFuaW1hdGVFbmRVcGxvYWQoKCkgPT4ge1xuICAgICAgJChzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVNc2dEZXRhaWxzU2VsZWN0b3IpLmh0bWwobWVzc2FnZSk7XG4gICAgICAkKHNlbGYubW9kdWxlSW1wb3J0RmFpbHVyZVNlbGVjdG9yKS5mYWRlSW4oKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJZiBQcmVzdGFUcnVzdCBuZWVkcyB0byBiZSBjb25maXJtZWQsIHdlIGFzayBmb3IgdGhlIGNvbmZpcm1hdGlvblxuICAgKiBtb2RhbCBjb250ZW50IGFuZCB3ZSBkaXNwbGF5IGl0IGluIHRoZSBjdXJyZW50bHkgZGlzcGxheWVkIG9uZS5cbiAgICogV2UgYWxzbyBnZW5lcmF0ZSB0aGUgYWpheCBjYWxsIHRvIHRyaWdnZXIgb25jZSB3ZSBjb25maXJtIHdlIHdhbnQgdG8gaW5zdGFsbFxuICAgKiB0aGUgbW9kdWxlLlxuICAgKlxuICAgKiBAcGFyYW0gUHJldmlvdXMgc2VydmVyIHJlc3BvbnNlIHJlc3VsdFxuICAgKi9cbiAgZGlzcGxheVByZXN0YVRydXN0U3RlcChyZXN1bHQpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBtb2RhbCA9IHNlbGYubW9kdWxlQ2FyZENvbnRyb2xsZXIuX3JlcGxhY2VQcmVzdGFUcnVzdFBsYWNlaG9sZGVycyhyZXN1bHQpO1xuICAgIGNvbnN0IG1vZHVsZU5hbWUgPSByZXN1bHQubW9kdWxlLmF0dHJpYnV0ZXMubmFtZTtcblxuICAgICQodGhpcy5tb2R1bGVJbXBvcnRDb25maXJtU2VsZWN0b3IpLmh0bWwobW9kYWwuZmluZCgnLm1vZGFsLWJvZHknKS5odG1sKCkpLmZhZGVJbigpO1xuICAgICQodGhpcy5kcm9wWm9uZU1vZGFsRm9vdGVyU2VsZWN0b3IpLmh0bWwobW9kYWwuZmluZCgnLm1vZGFsLWZvb3RlcicpLmh0bWwoKSkuZmFkZUluKCk7XG5cbiAgICAkKHRoaXMuZHJvcFpvbmVNb2RhbEZvb3RlclNlbGVjdG9yKS5maW5kKCcucHN0cnVzdC1pbnN0YWxsJykub2ZmKCdjbGljaycpLm9uKCdjbGljaycsICgpID0+IHtcbiAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRDb25maXJtU2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgICQoc2VsZi5kcm9wWm9uZU1vZGFsRm9vdGVyU2VsZWN0b3IpLmh0bWwoJycpO1xuICAgICAgc2VsZi5hbmltYXRlU3RhcnRVcGxvYWQoKTtcblxuICAgICAgLy8gSW5zdGFsbCBhamF4IGNhbGxcbiAgICAgICQucG9zdChyZXN1bHQubW9kdWxlLmF0dHJpYnV0ZXMudXJscy5pbnN0YWxsLCB7J2FjdGlvblBhcmFtc1tjb25maXJtUHJlc3RhVHJ1c3RdJzogJzEnfSlcbiAgICAgICAuZG9uZSgoZGF0YSkgPT4ge1xuICAgICAgICAgc2VsZi5kaXNwbGF5T25VcGxvYWREb25lKGRhdGFbbW9kdWxlTmFtZV0pO1xuICAgICAgIH0pXG4gICAgICAgLmZhaWwoKGRhdGEpID0+IHtcbiAgICAgICAgIHNlbGYuZGlzcGxheU9uVXBsb2FkRXJyb3IoZGF0YVttb2R1bGVOYW1lXSk7XG4gICAgICAgfSlcbiAgICAgICAuYWx3YXlzKCgpID0+IHtcbiAgICAgICAgIHNlbGYuaXNVcGxvYWRTdGFydGVkID0gZmFsc2U7XG4gICAgICAgfSk7XG4gICAgfSk7XG4gIH1cblxuICBnZXRCdWxrQ2hlY2tib3hlc1NlbGVjdG9yKCkge1xuICAgIHJldHVybiB0aGlzLmN1cnJlbnREaXNwbGF5ID09PSB0aGlzLkRJU1BMQVlfR1JJRFxuICAgICAgICAgPyB0aGlzLmJ1bGtBY3Rpb25DaGVja2JveEdyaWRTZWxlY3RvclxuICAgICAgICAgOiB0aGlzLmJ1bGtBY3Rpb25DaGVja2JveExpc3RTZWxlY3RvcjtcbiAgfVxuXG5cbiAgZ2V0QnVsa0NoZWNrYm94ZXNDaGVja2VkU2VsZWN0b3IoKSB7XG4gICAgcmV0dXJuIHRoaXMuY3VycmVudERpc3BsYXkgPT09IHRoaXMuRElTUExBWV9HUklEXG4gICAgICAgICA/IHRoaXMuY2hlY2tlZEJ1bGtBY3Rpb25HcmlkU2VsZWN0b3JcbiAgICAgICAgIDogdGhpcy5jaGVja2VkQnVsa0FjdGlvbkxpc3RTZWxlY3RvcjtcbiAgfVxuXG4gIGdldE1vZHVsZUl0ZW1TZWxlY3RvcigpIHtcbiAgICByZXR1cm4gdGhpcy5jdXJyZW50RGlzcGxheSA9PT0gdGhpcy5ESVNQTEFZX0dSSURcbiAgICAgICAgID8gdGhpcy5tb2R1bGVJdGVtR3JpZFNlbGVjdG9yXG4gICAgICAgICA6IHRoaXMubW9kdWxlSXRlbUxpc3RTZWxlY3RvcjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIG1vZHVsZSBub3RpZmljYXRpb25zIGNvdW50IGFuZCBkaXNwbGF5cyBpdCBhcyBhIGJhZGdlIG9uIHRoZSBub3RpZmljYXRpb24gdGFiXG4gICAqIEByZXR1cm4gdm9pZFxuICAgKi9cbiAgZ2V0Tm90aWZpY2F0aW9uc0NvdW50KCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgICQuZ2V0SlNPTihcbiAgICAgIHdpbmRvdy5tb2R1bGVVUkxzLm5vdGlmaWNhdGlvbnNDb3VudCxcbiAgICAgIHNlbGYudXBkYXRlTm90aWZpY2F0aW9uc0NvdW50XG4gICAgKS5mYWlsKCgpID0+IHtcbiAgICAgIGNvbnNvbGUuZXJyb3IoJ0NvdWxkIG5vdCByZXRyaWV2ZSBtb2R1bGUgbm90aWZpY2F0aW9ucyBjb3VudC4nKTtcbiAgICB9KTtcbiAgfVxuXG4gIHVwZGF0ZU5vdGlmaWNhdGlvbnNDb3VudChiYWRnZSkge1xuICAgIGNvbnN0IGRlc3RpbmF0aW9uVGFicyA9IHtcbiAgICAgIHRvX2NvbmZpZ3VyZTogJCgnI3N1YnRhYi1BZG1pbk1vZHVsZXNOb3RpZmljYXRpb25zJyksXG4gICAgICB0b191cGRhdGU6ICQoJyNzdWJ0YWItQWRtaW5Nb2R1bGVzVXBkYXRlcycpLFxuICAgIH07XG5cbiAgICBmb3IgKGxldCBrZXkgaW4gZGVzdGluYXRpb25UYWJzKSB7XG4gICAgICBpZiAoZGVzdGluYXRpb25UYWJzW2tleV0ubGVuZ3RoID09PSAwKSB7XG4gICAgICAgIGNvbnRpbnVlO1xuICAgICAgfVxuXG4gICAgICBkZXN0aW5hdGlvblRhYnNba2V5XS5maW5kKCcubm90aWZpY2F0aW9uLWNvdW50ZXInKS50ZXh0KGJhZGdlW2tleV0pO1xuICAgIH1cbiAgfVxuXG4gIGluaXRBZGRvbnNTZWFyY2goKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgJCgnYm9keScpLm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgIGAke3NlbGYuYWRkb25JdGVtR3JpZFNlbGVjdG9yfSwgJHtzZWxmLmFkZG9uSXRlbUxpc3RTZWxlY3Rvcn1gLFxuICAgICAgKCkgPT4ge1xuICAgICAgICBsZXQgc2VhcmNoUXVlcnkgPSAnJztcbiAgICAgICAgaWYgKHNlbGYuY3VycmVudFRhZ3NMaXN0Lmxlbmd0aCkge1xuICAgICAgICAgIHNlYXJjaFF1ZXJ5ID0gZW5jb2RlVVJJQ29tcG9uZW50KHNlbGYuY3VycmVudFRhZ3NMaXN0LmpvaW4oJyAnKSk7XG4gICAgICAgIH1cblxuICAgICAgICB3aW5kb3cub3BlbihgJHtzZWxmLmJhc2VBZGRvbnNVcmx9c2VhcmNoLnBocD9zZWFyY2hfcXVlcnk9JHtzZWFyY2hRdWVyeX1gLCAnX2JsYW5rJyk7XG4gICAgICB9XG4gICAgKTtcbiAgfVxuXG4gIGluaXRDYXRlZ29yaWVzR3JpZCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoJ2JvZHknKS5vbignY2xpY2snLCB0aGlzLmNhdGVnb3J5R3JpZEl0ZW1TZWxlY3RvciwgZnVuY3Rpb24gaW5pdGlsYWl6ZUdyaWRCb2R5Q2xpY2soZXZlbnQpIHtcbiAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIGNvbnN0IHJlZkNhdGVnb3J5ID0gJCh0aGlzKS5kYXRhKCdjYXRlZ29yeS1yZWYnKTtcblxuICAgICAgLy8gSW4gY2FzZSB3ZSBoYXZlIHNvbWUgdGFncyB3ZSBuZWVkIHRvIHJlc2V0IGl0ICFcbiAgICAgIGlmIChzZWxmLmN1cnJlbnRUYWdzTGlzdC5sZW5ndGgpIHtcbiAgICAgICAgc2VsZi5wc3RhZ2dlcklucHV0LnJlc2V0VGFncyhmYWxzZSk7XG4gICAgICAgIHNlbGYuY3VycmVudFRhZ3NMaXN0ID0gW107XG4gICAgICB9XG4gICAgICBjb25zdCBtZW51Q2F0ZWdvcnlUb1RyaWdnZXIgPSAkKGAke3NlbGYuY2F0ZWdvcnlJdGVtU2VsZWN0b3J9W2RhdGEtY2F0ZWdvcnktcmVmPVwiJHtyZWZDYXRlZ29yeX1cIl1gKTtcblxuICAgICAgaWYgKCFtZW51Q2F0ZWdvcnlUb1RyaWdnZXIubGVuZ3RoKSB7XG4gICAgICAgIGNvbnNvbGUud2FybihgTm8gY2F0ZWdvcnkgd2l0aCByZWYgKCR7cmVmQ2F0ZWdvcnl9KSBzZWVtcyB0byBleGlzdCFgKTtcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgfVxuXG4gICAgICAvLyBIaWRlIGN1cnJlbnQgY2F0ZWdvcnkgZ3JpZFxuICAgICAgaWYgKHNlbGYuaXNDYXRlZ29yeUdyaWREaXNwbGF5ZWQgPT09IHRydWUpIHtcbiAgICAgICAgJChzZWxmLmNhdGVnb3J5R3JpZFNlbGVjdG9yKS5mYWRlT3V0KCk7XG4gICAgICAgIHNlbGYuaXNDYXRlZ29yeUdyaWREaXNwbGF5ZWQgPSBmYWxzZTtcbiAgICAgIH1cblxuICAgICAgLy8gVHJpZ2dlciBjbGljayBvbiByaWdodCBjYXRlZ29yeVxuICAgICAgJChgJHtzZWxmLmNhdGVnb3J5SXRlbVNlbGVjdG9yfVtkYXRhLWNhdGVnb3J5LXJlZj1cIiR7cmVmQ2F0ZWdvcnl9XCJdYCkuY2xpY2soKTtcbiAgICAgIHJldHVybiB0cnVlO1xuICAgIH0pO1xuICB9XG5cbiAgaW5pdEN1cnJlbnREaXNwbGF5KCkge1xuICAgIHRoaXMuY3VycmVudERpc3BsYXkgPSB0aGlzLmN1cnJlbnREaXNwbGF5ID09PSAnJyA/IHRoaXMuRElTUExBWV9MSVNUIDogdGhpcy5ESVNQTEFZX0dSSUQ7XG4gIH1cblxuICBpbml0U29ydGluZ0Ryb3Bkb3duKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgc2VsZi5jdXJyZW50U29ydGluZyA9ICQodGhpcy5tb2R1bGVTb3J0aW5nRHJvcGRvd25TZWxlY3RvcikuZmluZCgnOmNoZWNrZWQnKS5hdHRyKCd2YWx1ZScpO1xuICAgIGlmICghc2VsZi5jdXJyZW50U29ydGluZykge1xuICAgICAgc2VsZi5jdXJyZW50U29ydGluZyA9ICdhY2Nlc3MtZGVzYyc7XG4gICAgfVxuXG4gICAgJCgnYm9keScpLm9uKFxuICAgICAgJ2NoYW5nZScsXG4gICAgICBzZWxmLm1vZHVsZVNvcnRpbmdEcm9wZG93blNlbGVjdG9yLFxuICAgICAgZnVuY3Rpb24gaW5pdGlhbGl6ZUJvZHlTb3J0aW5nQ2hhbmdlKCkge1xuICAgICAgICBzZWxmLmN1cnJlbnRTb3J0aW5nID0gJCh0aGlzKS5maW5kKCc6Y2hlY2tlZCcpLmF0dHIoJ3ZhbHVlJyk7XG4gICAgICAgIHNlbGYudXBkYXRlTW9kdWxlVmlzaWJpbGl0eSgpO1xuICAgICAgfVxuICAgICk7XG4gIH1cblxuICBkb0J1bGtBY3Rpb24ocmVxdWVzdGVkQnVsa0FjdGlvbikge1xuICAgIC8vIFRoaXMgb2JqZWN0IGlzIHVzZWQgdG8gY2hlY2sgaWYgcmVxdWVzdGVkIGJ1bGtBY3Rpb24gaXMgYXZhaWxhYmxlIGFuZCBnaXZlIHByb3BlclxuICAgIC8vIHVybCBzZWdtZW50IHRvIGJlIGNhbGxlZCBmb3IgaXRcbiAgICBjb25zdCBmb3JjZURlbGV0aW9uID0gJCgnI2ZvcmNlX2J1bGtfZGVsZXRpb24nKS5wcm9wKCdjaGVja2VkJyk7XG5cbiAgICBjb25zdCBidWxrQWN0aW9uVG9VcmwgPSB7XG4gICAgICAnYnVsay11bmluc3RhbGwnOiAndW5pbnN0YWxsJyxcbiAgICAgICdidWxrLWRpc2FibGUnOiAnZGlzYWJsZScsXG4gICAgICAnYnVsay1lbmFibGUnOiAnZW5hYmxlJyxcbiAgICAgICdidWxrLWRpc2FibGUtbW9iaWxlJzogJ2Rpc2FibGVfbW9iaWxlJyxcbiAgICAgICdidWxrLWVuYWJsZS1tb2JpbGUnOiAnZW5hYmxlX21vYmlsZScsXG4gICAgICAnYnVsay1yZXNldCc6ICdyZXNldCcsXG4gICAgfTtcblxuICAgIC8vIE5vdGUgbm8gZ3JpZCBzZWxlY3RvciB1c2VkIHlldCBzaW5jZSB3ZSBkbyBub3QgbmVlZGVkIGl0IGF0IGRldiB0aW1lXG4gICAgLy8gTWF5YmUgdXNlZnVsIHRvIGltcGxlbWVudCB0aGlzIGtpbmQgb2YgdGhpbmdzIGxhdGVyIGlmIGludGVuZGVkIHRvXG4gICAgLy8gdXNlIHRoaXMgZnVuY3Rpb25hbGl0eSBlbHNld2hlcmUgYnV0IFwibWFuYWdlIG15IG1vZHVsZVwiIHNlY3Rpb25cbiAgICBpZiAodHlwZW9mIGJ1bGtBY3Rpb25Ub1VybFtyZXF1ZXN0ZWRCdWxrQWN0aW9uXSA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IHdpbmRvdy50cmFuc2xhdGVfamF2YXNjcmlwdHNbJ0J1bGsgQWN0aW9uIC0gUmVxdWVzdCBub3QgZm91bmQnXS5yZXBsYWNlKCdbMV0nLCByZXF1ZXN0ZWRCdWxrQWN0aW9uKX0pO1xuICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cblxuICAgIC8vIExvb3Agb3ZlciBhbGwgY2hlY2tlZCBidWxrIGNoZWNrYm94ZXNcbiAgICBjb25zdCBidWxrQWN0aW9uU2VsZWN0ZWRTZWxlY3RvciA9IHRoaXMuZ2V0QnVsa0NoZWNrYm94ZXNDaGVja2VkU2VsZWN0b3IoKTtcbiAgICBjb25zdCBidWxrTW9kdWxlQWN0aW9uID0gYnVsa0FjdGlvblRvVXJsW3JlcXVlc3RlZEJ1bGtBY3Rpb25dO1xuXG4gICAgaWYgKCQoYnVsa0FjdGlvblNlbGVjdGVkU2VsZWN0b3IpLmxlbmd0aCA8PSAwKSB7XG4gICAgICBjb25zb2xlLndhcm4od2luZG93LnRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snQnVsayBBY3Rpb24gLSBPbmUgbW9kdWxlIG1pbmltdW0nXSk7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuXG4gICAgY29uc3QgbW9kdWxlc0FjdGlvbnMgPSBbXTtcbiAgICBsZXQgbW9kdWxlVGVjaE5hbWU7XG4gICAgJChidWxrQWN0aW9uU2VsZWN0ZWRTZWxlY3RvcikuZWFjaChmdW5jdGlvbiBidWxrQWN0aW9uU2VsZWN0b3IoKSB7XG4gICAgICBtb2R1bGVUZWNoTmFtZSA9ICQodGhpcykuZGF0YSgndGVjaC1uYW1lJyk7XG4gICAgICBtb2R1bGVzQWN0aW9ucy5wdXNoKHtcbiAgICAgICAgdGVjaE5hbWU6IG1vZHVsZVRlY2hOYW1lLFxuICAgICAgICBhY3Rpb25NZW51T2JqOiAkKHRoaXMpLmNsb3Nlc3QoJy5tb2R1bGUtY2hlY2tib3gtYnVsay1saXN0JykubmV4dCgpLFxuICAgICAgfSk7XG4gICAgfSk7XG5cbiAgICB0aGlzLnBlcmZvcm1Nb2R1bGVzQWN0aW9uKG1vZHVsZXNBY3Rpb25zLCBidWxrTW9kdWxlQWN0aW9uLCBmb3JjZURlbGV0aW9uKTtcblxuICAgIHJldHVybiB0cnVlO1xuICB9XG5cbiAgcGVyZm9ybU1vZHVsZXNBY3Rpb24obW9kdWxlc0FjdGlvbnMsIGJ1bGtNb2R1bGVBY3Rpb24sIGZvcmNlRGVsZXRpb24pIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBpZiAodHlwZW9mIHNlbGYubW9kdWxlQ2FyZENvbnRyb2xsZXIgPT09ICd1bmRlZmluZWQnKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgLy9GaXJzdCBsZXQncyBmaWx0ZXIgbW9kdWxlcyB0aGF0IGNhbid0IHBlcmZvcm0gdGhpcyBhY3Rpb25cbiAgICBsZXQgYWN0aW9uTWVudUxpbmtzID0gZmlsdGVyQWxsb3dlZEFjdGlvbnMobW9kdWxlc0FjdGlvbnMpO1xuICAgIGlmICghYWN0aW9uTWVudUxpbmtzLmxlbmd0aCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGxldCBtb2R1bGVzUmVxdWVzdGVkQ291bnRkb3duID0gYWN0aW9uTWVudUxpbmtzLmxlbmd0aCAtIDE7XG4gICAgbGV0IHNwaW5uZXJPYmogPSAkKFwiPGJ1dHRvbiBjbGFzcz1cXFwiYnRuLXByaW1hcnktcmV2ZXJzZSBvbmNsaWNrIHVuYmluZCBzcGlubmVyIFxcXCI+PC9idXR0b24+XCIpO1xuICAgIGlmIChhY3Rpb25NZW51TGlua3MubGVuZ3RoID4gMSkge1xuICAgICAgLy9Mb29wIHRocm91Z2ggYWxsIHRoZSBtb2R1bGVzIGV4Y2VwdCB0aGUgbGFzdCBvbmUgd2hpY2ggd2FpdHMgZm9yIG90aGVyXG4gICAgICAvL3JlcXVlc3RzIGFuZCB0aGVuIGNhbGwgaXRzIHJlcXVlc3Qgd2l0aCBjYWNoZSBjbGVhciBlbmFibGVkXG4gICAgICAkLmVhY2goYWN0aW9uTWVudUxpbmtzLCBmdW5jdGlvbiBidWxrTW9kdWxlc0xvb3AoaW5kZXgsIGFjdGlvbk1lbnVMaW5rKSB7XG4gICAgICAgIGlmIChpbmRleCA+PSBhY3Rpb25NZW51TGlua3MubGVuZ3RoIC0gMSkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICByZXF1ZXN0TW9kdWxlQWN0aW9uKGFjdGlvbk1lbnVMaW5rLCB0cnVlLCBjb3VudGRvd25Nb2R1bGVzUmVxdWVzdCk7XG4gICAgICB9KTtcbiAgICAgIC8vRGlzcGxheSBhIHNwaW5uZXIgZm9yIHRoZSBsYXN0IG1vZHVsZVxuICAgICAgY29uc3QgbGFzdE1lbnVMaW5rID0gYWN0aW9uTWVudUxpbmtzW2FjdGlvbk1lbnVMaW5rcy5sZW5ndGggLSAxXTtcbiAgICAgIGNvbnN0IGFjdGlvbk1lbnVPYmogPSBsYXN0TWVudUxpbmsuY2xvc2VzdChzZWxmLm1vZHVsZUNhcmRDb250cm9sbGVyLm1vZHVsZUl0ZW1BY3Rpb25zU2VsZWN0b3IpO1xuICAgICAgYWN0aW9uTWVudU9iai5oaWRlKCk7XG4gICAgICBhY3Rpb25NZW51T2JqLmFmdGVyKHNwaW5uZXJPYmopO1xuICAgIH0gZWxzZSB7XG4gICAgICByZXF1ZXN0TW9kdWxlQWN0aW9uKGFjdGlvbk1lbnVMaW5rc1swXSk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gcmVxdWVzdE1vZHVsZUFjdGlvbihhY3Rpb25NZW51TGluaywgZGlzYWJsZUNhY2hlQ2xlYXIsIHJlcXVlc3RFbmRDYWxsYmFjaykge1xuICAgICAgc2VsZi5tb2R1bGVDYXJkQ29udHJvbGxlci5fcmVxdWVzdFRvQ29udHJvbGxlcihcbiAgICAgICAgYnVsa01vZHVsZUFjdGlvbixcbiAgICAgICAgYWN0aW9uTWVudUxpbmssXG4gICAgICAgIGZvcmNlRGVsZXRpb24sXG4gICAgICAgIGRpc2FibGVDYWNoZUNsZWFyLFxuICAgICAgICByZXF1ZXN0RW5kQ2FsbGJhY2tcbiAgICAgICk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gY291bnRkb3duTW9kdWxlc1JlcXVlc3QoKSB7XG4gICAgICBtb2R1bGVzUmVxdWVzdGVkQ291bnRkb3duLS07XG4gICAgICAvL05vdyB0aGF0IGFsbCBvdGhlciBtb2R1bGVzIGhhdmUgcGVyZm9ybWVkIHRoZWlyIGFjdGlvbiBXSVRIT1VUIGNhY2hlIGNsZWFyLCB3ZVxuICAgICAgLy9jYW4gcmVxdWVzdCB0aGUgbGFzdCBtb2R1bGUgcmVxdWVzdCBXSVRIIGNhY2hlIGNsZWFyXG4gICAgICBpZiAobW9kdWxlc1JlcXVlc3RlZENvdW50ZG93biA8PSAwKSB7XG4gICAgICAgIGlmIChzcGlubmVyT2JqKSB7XG4gICAgICAgICAgc3Bpbm5lck9iai5yZW1vdmUoKTtcbiAgICAgICAgICBzcGlubmVyT2JqID0gbnVsbDtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IGxhc3RNZW51TGluayA9IGFjdGlvbk1lbnVMaW5rc1thY3Rpb25NZW51TGlua3MubGVuZ3RoIC0gMV07XG4gICAgICAgIGNvbnN0IGFjdGlvbk1lbnVPYmogPSBsYXN0TWVudUxpbmsuY2xvc2VzdChzZWxmLm1vZHVsZUNhcmRDb250cm9sbGVyLm1vZHVsZUl0ZW1BY3Rpb25zU2VsZWN0b3IpO1xuICAgICAgICBhY3Rpb25NZW51T2JqLmZhZGVJbigpO1xuICAgICAgICByZXF1ZXN0TW9kdWxlQWN0aW9uKGxhc3RNZW51TGluayk7XG4gICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gZmlsdGVyQWxsb3dlZEFjdGlvbnMobW9kdWxlc0FjdGlvbnMpIHtcbiAgICAgIGxldCBhY3Rpb25NZW51TGlua3MgPSBbXTtcbiAgICAgIGxldCBhY3Rpb25NZW51TGluaztcbiAgICAgICQuZWFjaChtb2R1bGVzQWN0aW9ucywgZnVuY3Rpb24gZmlsdGVyQWxsb3dlZE1vZHVsZXMoaW5kZXgsIG1vZHVsZURhdGEpIHtcbiAgICAgICAgYWN0aW9uTWVudUxpbmsgPSAkKFxuICAgICAgICAgIHNlbGYubW9kdWxlQ2FyZENvbnRyb2xsZXIubW9kdWxlQWN0aW9uTWVudUxpbmtTZWxlY3RvciArIGJ1bGtNb2R1bGVBY3Rpb24sXG4gICAgICAgICAgbW9kdWxlRGF0YS5hY3Rpb25NZW51T2JqXG4gICAgICAgICk7XG4gICAgICAgIGlmIChhY3Rpb25NZW51TGluay5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgYWN0aW9uTWVudUxpbmtzLnB1c2goYWN0aW9uTWVudUxpbmspO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IHdpbmRvdy50cmFuc2xhdGVfamF2YXNjcmlwdHNbJ0J1bGsgQWN0aW9uIC0gUmVxdWVzdCBub3QgYXZhaWxhYmxlIGZvciBtb2R1bGUnXVxuICAgICAgICAgICAgICAucmVwbGFjZSgnWzFdJywgYnVsa01vZHVsZUFjdGlvbilcbiAgICAgICAgICAgICAgLnJlcGxhY2UoJ1syXScsIG1vZHVsZURhdGEudGVjaE5hbWUpfSk7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuXG4gICAgICByZXR1cm4gYWN0aW9uTWVudUxpbmtzO1xuICAgIH1cbiAgfVxuXG4gIGluaXRBY3Rpb25CdXR0b25zKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgICQoJ2JvZHknKS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICBzZWxmLm1vZHVsZUluc3RhbGxCdG5TZWxlY3RvcixcbiAgICAgIGZ1bmN0aW9uIGluaXRpYWxpemVBY3Rpb25CdXR0b25zQ2xpY2soZXZlbnQpIHtcbiAgICAgICAgY29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICBjb25zdCAkbmV4dCA9ICQoJHRoaXMubmV4dCgpKTtcbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICAkdGhpcy5oaWRlKCk7XG4gICAgICAgICRuZXh0LnNob3coKTtcblxuICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgIHVybDogJHRoaXMuZGF0YSgndXJsJyksXG4gICAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgICAgfSkuZG9uZSgoKSA9PiB7XG4gICAgICAgICAgJG5leHQuZmFkZU91dCgpO1xuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICApO1xuXG4gICAgLy8gXCJVcGdyYWRlIEFsbFwiIGJ1dHRvbiBoYW5kbGVyXG4gICAgJCgnYm9keScpLm9uKCdjbGljaycsIHNlbGYudXBncmFkZUFsbFNvdXJjZSwgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICBpZiAoJChzZWxmLnVwZ3JhZGVBbGxUYXJnZXRzKS5sZW5ndGggPD0gMCkge1xuICAgICAgICBjb25zb2xlLndhcm4od2luZG93LnRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snVXBncmFkZSBBbGwgQWN0aW9uIC0gT25lIG1vZHVsZSBtaW5pbXVtJ10pO1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICB9XG5cbiAgICAgIGNvbnN0IG1vZHVsZXNBY3Rpb25zID0gW107XG4gICAgICBsZXQgbW9kdWxlVGVjaE5hbWU7XG4gICAgICAkKHNlbGYudXBncmFkZUFsbFRhcmdldHMpLmVhY2goZnVuY3Rpb24gYnVsa0FjdGlvblNlbGVjdG9yKCkge1xuICAgICAgICBjb25zdCBtb2R1bGVJdGVtTGlzdCA9ICQodGhpcykuY2xvc2VzdCgnLm1vZHVsZS1pdGVtLWxpc3QnKTtcbiAgICAgICAgbW9kdWxlVGVjaE5hbWUgPSBtb2R1bGVJdGVtTGlzdC5kYXRhKCd0ZWNoLW5hbWUnKTtcbiAgICAgICAgbW9kdWxlc0FjdGlvbnMucHVzaCh7XG4gICAgICAgICAgdGVjaE5hbWU6IG1vZHVsZVRlY2hOYW1lLFxuICAgICAgICAgIGFjdGlvbk1lbnVPYmo6ICQoJy5tb2R1bGUtYWN0aW9ucycsIG1vZHVsZUl0ZW1MaXN0KSxcbiAgICAgICAgfSk7XG4gICAgICB9KTtcblxuICAgICAgdGhpcy5wZXJmb3JtTW9kdWxlc0FjdGlvbihtb2R1bGVzQWN0aW9ucywgJ3VwZ3JhZGUnKTtcblxuICAgICAgcmV0dXJuIHRydWU7XG4gICAgfSk7XG4gIH1cblxuICBpbml0Q2F0ZWdvcnlTZWxlY3QoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgY29uc3QgYm9keSA9ICQoJ2JvZHknKTtcbiAgICBib2R5Lm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgIHNlbGYuY2F0ZWdvcnlJdGVtU2VsZWN0b3IsXG4gICAgICBmdW5jdGlvbiBpbml0aWFsaXplQ2F0ZWdvcnlTZWxlY3RDbGljaygpIHtcbiAgICAgICAgLy8gR2V0IGRhdGEgZnJvbSBsaSBET00gaW5wdXRcbiAgICAgICAgc2VsZi5jdXJyZW50UmVmQ2F0ZWdvcnkgPSAkKHRoaXMpLmRhdGEoJ2NhdGVnb3J5LXJlZicpO1xuICAgICAgICBzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeSA9IHNlbGYuY3VycmVudFJlZkNhdGVnb3J5ID8gU3RyaW5nKHNlbGYuY3VycmVudFJlZkNhdGVnb3J5KS50b0xvd2VyQ2FzZSgpIDogbnVsbDtcbiAgICAgICAgLy8gQ2hhbmdlIGRyb3Bkb3duIGxhYmVsIHRvIHNldCBpdCB0byB0aGUgY3VycmVudCBjYXRlZ29yeSdzIGRpc3BsYXluYW1lXG4gICAgICAgICQoc2VsZi5jYXRlZ29yeVNlbGVjdG9yTGFiZWxTZWxlY3RvcikudGV4dCgkKHRoaXMpLmRhdGEoJ2NhdGVnb3J5LWRpc3BsYXktbmFtZScpKTtcbiAgICAgICAgJChzZWxmLmNhdGVnb3J5UmVzZXRCdG5TZWxlY3Rvcikuc2hvdygpO1xuICAgICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICAgIH1cbiAgICApO1xuXG4gICAgYm9keS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICBzZWxmLmNhdGVnb3J5UmVzZXRCdG5TZWxlY3RvcixcbiAgICAgIGZ1bmN0aW9uIGluaXRpYWxpemVDYXRlZ29yeVJlc2V0QnV0dG9uQ2xpY2soKSB7XG4gICAgICAgIGNvbnN0IHJhd1RleHQgPSAkKHNlbGYuY2F0ZWdvcnlTZWxlY3RvcikuYXR0cignYXJpYS1sYWJlbGxlZGJ5Jyk7XG4gICAgICAgIGNvbnN0IHVwcGVyRmlyc3RMZXR0ZXIgPSByYXdUZXh0LmNoYXJBdCgwKS50b1VwcGVyQ2FzZSgpO1xuICAgICAgICBjb25zdCByZW1vdmVkRmlyc3RMZXR0ZXIgPSByYXdUZXh0LnNsaWNlKDEpO1xuICAgICAgICBjb25zdCBvcmlnaW5hbFRleHQgPSB1cHBlckZpcnN0TGV0dGVyICsgcmVtb3ZlZEZpcnN0TGV0dGVyO1xuXG4gICAgICAgICQoc2VsZi5jYXRlZ29yeVNlbGVjdG9yTGFiZWxTZWxlY3RvcikudGV4dChvcmlnaW5hbFRleHQpO1xuICAgICAgICAkKHRoaXMpLmhpZGUoKTtcbiAgICAgICAgc2VsZi5jdXJyZW50UmVmQ2F0ZWdvcnkgPSBudWxsO1xuICAgICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICAgIH1cbiAgICApO1xuICB9XG5cbiAgaW5pdFNlYXJjaEJsb2NrKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIHNlbGYucHN0YWdnZXJJbnB1dCA9ICQoJyNtb2R1bGUtc2VhcmNoLWJhcicpLnBzdGFnZ2VyKHtcbiAgICAgIG9uVGFnc0NoYW5nZWQ6ICh0YWdMaXN0KSA9PiB7XG4gICAgICAgIHNlbGYuY3VycmVudFRhZ3NMaXN0ID0gdGFnTGlzdDtcbiAgICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgICB9LFxuICAgICAgb25SZXNldFRhZ3M6ICgpID0+IHtcbiAgICAgICAgc2VsZi5jdXJyZW50VGFnc0xpc3QgPSBbXTtcbiAgICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgICB9LFxuICAgICAgaW5wdXRQbGFjZWhvbGRlcjogd2luZG93LnRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snU2VhcmNoIC0gcGxhY2Vob2xkZXInXSxcbiAgICAgIGNsb3NpbmdDcm9zczogdHJ1ZSxcbiAgICAgIGNvbnRleHQ6IHNlbGYsXG4gICAgfSk7XG5cbiAgICAkKCdib2R5Jykub24oJ2NsaWNrJywgJy5tb2R1bGUtYWRkb25zLXNlYXJjaC1saW5rJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICB3aW5kb3cub3BlbigkKHRoaXMpLmF0dHIoJ2hyZWYnKSwgJ19ibGFuaycpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRpYWxpemUgZGlzcGxheSBzd2l0Y2hpbmcgYmV0d2VlbiBMaXN0IG9yIEdyaWRcbiAgICovXG4gIGluaXRTb3J0aW5nRGlzcGxheVN3aXRjaCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoJ2JvZHknKS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICAnLm1vZHVsZS1zb3J0LXN3aXRjaCcsXG4gICAgICBmdW5jdGlvbiBzd2l0Y2hTb3J0KCkge1xuICAgICAgICBjb25zdCBzd2l0Y2hUbyA9ICQodGhpcykuZGF0YSgnc3dpdGNoJyk7XG4gICAgICAgIGNvbnN0IGlzQWxyZWFkeURpc3BsYXllZCA9ICQodGhpcykuaGFzQ2xhc3MoJ2FjdGl2ZS1kaXNwbGF5Jyk7XG4gICAgICAgIGlmICh0eXBlb2Ygc3dpdGNoVG8gIT09ICd1bmRlZmluZWQnICYmIGlzQWxyZWFkeURpc3BsYXllZCA9PT0gZmFsc2UpIHtcbiAgICAgICAgICBzZWxmLnN3aXRjaFNvcnRpbmdEaXNwbGF5VG8oc3dpdGNoVG8pO1xuICAgICAgICAgIHNlbGYuY3VycmVudERpc3BsYXkgPSBzd2l0Y2hUbztcbiAgICAgICAgfVxuICAgICAgfVxuICAgICk7XG4gIH1cblxuICBzd2l0Y2hTb3J0aW5nRGlzcGxheVRvKHN3aXRjaFRvKSB7XG4gICAgaWYgKHN3aXRjaFRvICE9PSB0aGlzLkRJU1BMQVlfR1JJRCAmJiBzd2l0Y2hUbyAhPT0gdGhpcy5ESVNQTEFZX0xJU1QpIHtcbiAgICAgIGNvbnNvbGUuZXJyb3IoYENhbid0IHN3aXRjaCB0byB1bmRlZmluZWQgZGlzcGxheSBwcm9wZXJ0eSBcIiR7c3dpdGNoVG99XCJgKTtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAkKCcubW9kdWxlLXNvcnQtc3dpdGNoJykucmVtb3ZlQ2xhc3MoJ21vZHVsZS1zb3J0LWFjdGl2ZScpO1xuICAgICQoYCNtb2R1bGUtc29ydC0ke3N3aXRjaFRvfWApLmFkZENsYXNzKCdtb2R1bGUtc29ydC1hY3RpdmUnKTtcbiAgICB0aGlzLmN1cnJlbnREaXNwbGF5ID0gc3dpdGNoVG87XG4gICAgdGhpcy51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gIH1cblxuICBpbml0aWFsaXplU2VlTW9yZSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoYCR7c2VsZi5tb2R1bGVTaG9ydExpc3R9ICR7c2VsZi5zZWVNb3JlU2VsZWN0b3J9YCkub24oJ2NsaWNrJywgZnVuY3Rpb24gc2VlTW9yZSgpIHtcbiAgICAgIHNlbGYuY3VycmVudENhdGVnb3J5RGlzcGxheVskKHRoaXMpLmRhdGEoJ2NhdGVnb3J5JyldID0gdHJ1ZTtcbiAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgJCh0aGlzKS5jbG9zZXN0KHNlbGYubW9kdWxlU2hvcnRMaXN0KS5maW5kKHNlbGYuc2VlTGVzc1NlbGVjdG9yKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICB9KTtcblxuICAgICQoYCR7c2VsZi5tb2R1bGVTaG9ydExpc3R9ICR7c2VsZi5zZWVMZXNzU2VsZWN0b3J9YCkub24oJ2NsaWNrJywgZnVuY3Rpb24gc2VlTW9yZSgpIHtcbiAgICAgIHNlbGYuY3VycmVudENhdGVnb3J5RGlzcGxheVskKHRoaXMpLmRhdGEoJ2NhdGVnb3J5JyldID0gZmFsc2U7XG4gICAgICAkKHRoaXMpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgICQodGhpcykuY2xvc2VzdChzZWxmLm1vZHVsZVNob3J0TGlzdCkuZmluZChzZWxmLnNlZU1vcmVTZWxlY3RvcikucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgfSk7XG4gIH1cblxuICB1cGRhdGVUb3RhbFJlc3VsdHMoKSB7XG4gICAgY29uc3QgcmVwbGFjZUZpcnN0V29yZEJ5ID0gKGVsZW1lbnQsIHZhbHVlKSA9PiB7XG4gICAgICBjb25zdCBleHBsb2RlZFRleHQgPSBlbGVtZW50LnRleHQoKS5zcGxpdCgnICcpO1xuICAgICAgZXhwbG9kZWRUZXh0WzBdID0gdmFsdWU7XG4gICAgICBlbGVtZW50LnRleHQoZXhwbG9kZWRUZXh0LmpvaW4oJyAnKSk7XG4gICAgfTtcblxuICAgIC8vIElmIHRoZXJlIGFyZSBzb21lIHNob3J0bGlzdDogZWFjaCBzaG9ydGxpc3QgY291bnQgdGhlIG1vZHVsZXMgb24gdGhlIG5leHQgY29udGFpbmVyLlxuICAgIGNvbnN0ICRzaG9ydExpc3RzID0gJCgnLm1vZHVsZS1zaG9ydC1saXN0Jyk7XG4gICAgaWYgKCRzaG9ydExpc3RzLmxlbmd0aCA+IDApIHtcbiAgICAgICRzaG9ydExpc3RzLmVhY2goZnVuY3Rpb24gc2hvcnRMaXN0cygpIHtcbiAgICAgICAgY29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICByZXBsYWNlRmlyc3RXb3JkQnkoXG4gICAgICAgICAgJHRoaXMuZmluZCgnLm1vZHVsZS1zZWFyY2gtcmVzdWx0LXdvcmRpbmcnKSxcbiAgICAgICAgICAkdGhpcy5uZXh0KCcubW9kdWxlcy1saXN0JykuZmluZCgnLm1vZHVsZS1pdGVtJykubGVuZ3RoXG4gICAgICAgICk7XG4gICAgICB9KTtcblxuICAgICAgLy8gSWYgdGhlcmUgaXMgbm8gc2hvcnRsaXN0OiB0aGUgd29yZGluZyBkaXJlY3RseSB1cGRhdGUgZnJvbSB0aGUgb25seSBtb2R1bGUgY29udGFpbmVyLlxuICAgIH0gZWxzZSB7XG4gICAgICBjb25zdCBtb2R1bGVzQ291bnQgPSAkKCcubW9kdWxlcy1saXN0JykuZmluZCgnLm1vZHVsZS1pdGVtJykubGVuZ3RoO1xuICAgICAgcmVwbGFjZUZpcnN0V29yZEJ5KCQoJy5tb2R1bGUtc2VhcmNoLXJlc3VsdC13b3JkaW5nJyksIG1vZHVsZXNDb3VudCk7XG5cbiAgICAgIGNvbnN0IHNlbGVjdG9yVG9Ub2dnbGUgPSAoc2VsZi5jdXJyZW50RGlzcGxheSA9PT0gc2VsZi5ESVNQTEFZX0xJU1QpID9cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmFkZG9uSXRlbUxpc3RTZWxlY3RvciA6XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5hZGRvbkl0ZW1HcmlkU2VsZWN0b3I7XG4gICAgICAkKHNlbGVjdG9yVG9Ub2dnbGUpLnRvZ2dsZShtb2R1bGVzQ291bnQgIT09ICh0aGlzLm1vZHVsZXNMaXN0Lmxlbmd0aCAvIDIpKTtcblxuICAgICAgaWYgKG1vZHVsZXNDb3VudCA9PT0gMCkge1xuICAgICAgICAkKCcubW9kdWxlLWFkZG9ucy1zZWFyY2gtbGluaycpLmF0dHIoXG4gICAgICAgICAgJ2hyZWYnLFxuICAgICAgICAgIGAke3RoaXMuYmFzZUFkZG9uc1VybH1zZWFyY2gucGhwP3NlYXJjaF9xdWVyeT0ke2VuY29kZVVSSUNvbXBvbmVudCh0aGlzLmN1cnJlbnRUYWdzTGlzdC5qb2luKCcgJykpfWBcbiAgICAgICAgKTtcbiAgICAgIH1cbiAgICB9XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgQWRtaW5Nb2R1bGVDb250cm9sbGVyO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvbW9kdWxlL2NvbnRyb2xsZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogTW9kdWxlIEFkbWluIFBhZ2UgTG9hZGVyLlxuICogQGNvbnN0cnVjdG9yXG4gKi9cbmNsYXNzIE1vZHVsZUxvYWRlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIE1vZHVsZUxvYWRlci5oYW5kbGVJbXBvcnQoKTtcbiAgICBNb2R1bGVMb2FkZXIuaGFuZGxlRXZlbnRzKCk7XG4gIH1cblxuICBzdGF0aWMgaGFuZGxlSW1wb3J0KCkge1xuICAgIGNvbnN0IG1vZHVsZUltcG9ydCA9ICQoJyNtb2R1bGUtaW1wb3J0Jyk7XG4gICAgbW9kdWxlSW1wb3J0LmNsaWNrKCgpID0+IHtcbiAgICAgIG1vZHVsZUltcG9ydC5hZGRDbGFzcygnb25jbGljaycsIDI1MCwgdmFsaWRhdGUpO1xuICAgIH0pO1xuXG4gICAgZnVuY3Rpb24gdmFsaWRhdGUoKSB7XG4gICAgICBzZXRUaW1lb3V0KFxuICAgICAgICAoKSA9PiB7XG4gICAgICAgICAgbW9kdWxlSW1wb3J0LnJlbW92ZUNsYXNzKCdvbmNsaWNrJyk7XG4gICAgICAgICAgbW9kdWxlSW1wb3J0LmFkZENsYXNzKCd2YWxpZGF0ZScsIDQ1MCwgY2FsbGJhY2spO1xuICAgICAgICB9LFxuICAgICAgICAyMjUwXG4gICAgICApO1xuICAgIH1cbiAgICBmdW5jdGlvbiBjYWxsYmFjaygpIHtcbiAgICAgIHNldFRpbWVvdXQoXG4gICAgICAgICgpID0+IHtcbiAgICAgICAgICBtb2R1bGVJbXBvcnQucmVtb3ZlQ2xhc3MoJ3ZhbGlkYXRlJyk7XG4gICAgICAgIH0sXG4gICAgICAgIDEyNTBcbiAgICAgICk7XG4gICAgfVxuICB9XG5cbiAgc3RhdGljIGhhbmRsZUV2ZW50cygpIHtcbiAgICAkKCdib2R5Jykub24oXG4gICAgICAnY2xpY2snLFxuICAgICAgJ2EubW9kdWxlLXJlYWQtbW9yZS1ncmlkLWJ0biwgYS5tb2R1bGUtcmVhZC1tb3JlLWxpc3QtYnRuJyxcbiAgICAgIChldmVudCkgPT4ge1xuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICBjb25zdCBtb2R1bGVQb3BwaW4gPSAkKGV2ZW50LnRhcmdldCkuZGF0YSgndGFyZ2V0Jyk7XG5cbiAgICAgICAgJC5nZXQoZXZlbnQudGFyZ2V0LmhyZWYsIChkYXRhKSA9PiB7XG4gICAgICAgICAgJChtb2R1bGVQb3BwaW4pLmh0bWwoZGF0YSk7XG4gICAgICAgICAgJChtb2R1bGVQb3BwaW4pLm1vZGFsKCk7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgICk7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgTW9kdWxlTG9hZGVyO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvbW9kdWxlL2xvYWRlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBNb2R1bGVDYXJkIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvbW9kdWxlLWNhcmQnO1xuaW1wb3J0IEFkbWluTW9kdWxlQ29udHJvbGxlciBmcm9tICcuL2NvbnRyb2xsZXInO1xuaW1wb3J0IE1vZHVsZUxvYWRlciBmcm9tICcuL2xvYWRlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gIGNvbnN0IG1vZHVsZUNhcmRDb250cm9sbGVyID0gbmV3IE1vZHVsZUNhcmQoKTtcbiAgbmV3IE1vZHVsZUxvYWRlcigpO1xuICBuZXcgQWRtaW5Nb2R1bGVDb250cm9sbGVyKG1vZHVsZUNhcmRDb250cm9sbGVyKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvbW9kdWxlL2luZGV4LmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG52YXIgQk9FdmVudCA9IHtcbiAgb246IGZ1bmN0aW9uKGV2ZW50TmFtZSwgY2FsbGJhY2ssIGNvbnRleHQpIHtcblxuICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoZXZlbnROYW1lLCBmdW5jdGlvbihldmVudCkge1xuICAgICAgaWYgKHR5cGVvZiBjb250ZXh0ICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICBjYWxsYmFjay5jYWxsKGNvbnRleHQsIGV2ZW50KTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGNhbGxiYWNrKGV2ZW50KTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfSxcblxuICBlbWl0RXZlbnQ6IGZ1bmN0aW9uKGV2ZW50TmFtZSwgZXZlbnRUeXBlKSB7XG4gICAgdmFyIF9ldmVudCA9IGRvY3VtZW50LmNyZWF0ZUV2ZW50KGV2ZW50VHlwZSk7XG4gICAgLy8gdHJ1ZSB2YWx1ZXMgc3RhbmQgZm9yOiBjYW4gYnViYmxlLCBhbmQgaXMgY2FuY2VsbGFibGVcbiAgICBfZXZlbnQuaW5pdEV2ZW50KGV2ZW50TmFtZSwgdHJ1ZSwgdHJ1ZSk7XG4gICAgZG9jdW1lbnQuZGlzcGF0Y2hFdmVudChfZXZlbnQpO1xuICB9XG59O1xuXG5cbi8qKlxuICogQ2xhc3MgaXMgcmVzcG9uc2libGUgZm9yIGhhbmRsaW5nIE1vZHVsZSBDYXJkIGJlaGF2aW9yXG4gKlxuICogVGhpcyBpcyBhIHBvcnQgb2YgYWRtaW4tZGV2L3RoZW1lcy9kZWZhdWx0L2pzL2J1bmRsZS9tb2R1bGUvbW9kdWxlX2NhcmQuanNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTW9kdWxlQ2FyZCB7XG5cbiAgY29uc3RydWN0b3IoKSB7XG4gICAgLyogU2VsZWN0b3JzIGZvciBtb2R1bGUgYWN0aW9uIGxpbmtzICh1bmluc3RhbGwsIHJlc2V0LCBldGMuLi4pIHRvIGFkZCBhIGNvbmZpcm0gcG9waW4gKi9cbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV8nO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudUluc3RhbGxMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9pbnN0YWxsJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVFbmFibGVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9lbmFibGUnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudVVuaW5zdGFsbExpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X3VuaW5zdGFsbCc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51RGlzYWJsZUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2Rpc2FibGUnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudUVuYWJsZU1vYmlsZUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2VuYWJsZV9tb2JpbGUnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudURpc2FibGVNb2JpbGVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9kaXNhYmxlX21vYmlsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51UmVzZXRMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9yZXNldCc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51VXBkYXRlTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfdXBncmFkZSc7XG4gICAgdGhpcy5tb2R1bGVJdGVtTGlzdFNlbGVjdG9yID0gJy5tb2R1bGUtaXRlbS1saXN0JztcbiAgICB0aGlzLm1vZHVsZUl0ZW1HcmlkU2VsZWN0b3IgPSAnLm1vZHVsZS1pdGVtLWdyaWQnO1xuICAgIHRoaXMubW9kdWxlSXRlbUFjdGlvbnNTZWxlY3RvciA9ICcubW9kdWxlLWFjdGlvbnMnO1xuXG4gICAgLyogU2VsZWN0b3JzIG9ubHkgZm9yIG1vZGFsIGJ1dHRvbnMgKi9cbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1vZGFsRGlzYWJsZUxpbmtTZWxlY3RvciA9ICdhLm1vZHVsZV9hY3Rpb25fbW9kYWxfZGlzYWJsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbFJlc2V0TGlua1NlbGVjdG9yID0gJ2EubW9kdWxlX2FjdGlvbl9tb2RhbF9yZXNldCc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbFVuaW5zdGFsbExpbmtTZWxlY3RvciA9ICdhLm1vZHVsZV9hY3Rpb25fbW9kYWxfdW5pbnN0YWxsJztcbiAgICB0aGlzLmZvcmNlRGVsZXRpb25PcHRpb24gPSAnI2ZvcmNlX2RlbGV0aW9uJztcblxuICAgIHRoaXMuaW5pdEFjdGlvbkJ1dHRvbnMoKTtcbiAgfVxuXG4gIGluaXRBY3Rpb25CdXR0b25zKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5mb3JjZURlbGV0aW9uT3B0aW9uLCBmdW5jdGlvbiAoKSB7XG4gICAgICBjb25zdCBidG4gPSAkKHNlbGYubW9kdWxlQWN0aW9uTW9kYWxVbmluc3RhbGxMaW5rU2VsZWN0b3IsICQoXCJkaXYubW9kdWxlLWl0ZW0tbGlzdFtkYXRhLXRlY2gtbmFtZT0nXCIgKyAkKHRoaXMpLmF0dHIoXCJkYXRhLXRlY2gtbmFtZVwiKSArIFwiJ11cIikpO1xuICAgICAgaWYgKCQodGhpcykucHJvcCgnY2hlY2tlZCcpID09PSB0cnVlKSB7XG4gICAgICAgIGJ0bi5hdHRyKCdkYXRhLWRlbGV0aW9uJywgJ3RydWUnKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGJ0bi5yZW1vdmVBdHRyKCdkYXRhLWRlbGV0aW9uJyk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVJbnN0YWxsTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICBpZiAoJChcIiNtb2RhbC1wcmVzdGF0cnVzdFwiKS5sZW5ndGgpIHtcbiAgICAgICAgJChcIiNtb2RhbC1wcmVzdGF0cnVzdFwiKS5tb2RhbCgnaGlkZScpO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ2luc3RhbGwnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdpbnN0YWxsJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignaW5zdGFsbCcsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudUVuYWJsZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ2VuYWJsZScsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ2VuYWJsZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2VuYWJsZScsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudVVuaW5zdGFsbExpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ3VuaW5zdGFsbCcsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ3VuaW5zdGFsbCcsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ3VuaW5zdGFsbCcsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudURpc2FibGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdkaXNhYmxlJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbignZGlzYWJsZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2Rpc2FibGUnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVFbmFibGVNb2JpbGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdlbmFibGVfbW9iaWxlJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbignZW5hYmxlX21vYmlsZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2VuYWJsZV9tb2JpbGUnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTW9iaWxlTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgnZGlzYWJsZV9tb2JpbGUnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdkaXNhYmxlX21vYmlsZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2Rpc2FibGVfbW9iaWxlJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51UmVzZXRMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdyZXNldCcsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ3Jlc2V0JywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcigncmVzZXQnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVVcGRhdGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCd1cGRhdGUnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCd1cGRhdGUnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCd1cGRhdGUnLCAkKHRoaXMpKTtcbiAgICB9KTtcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxEaXNhYmxlTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZGlzYWJsZScsICQoc2VsZi5tb2R1bGVBY3Rpb25NZW51RGlzYWJsZUxpbmtTZWxlY3RvciwgJChcImRpdi5tb2R1bGUtaXRlbS1saXN0W2RhdGEtdGVjaC1uYW1lPSdcIiArICQodGhpcykuYXR0cihcImRhdGEtdGVjaC1uYW1lXCIpICsgXCInXVwiKSkpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxSZXNldExpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ3Jlc2V0JywgJChzZWxmLm1vZHVsZUFjdGlvbk1lbnVSZXNldExpbmtTZWxlY3RvciwgJChcImRpdi5tb2R1bGUtaXRlbS1saXN0W2RhdGEtdGVjaC1uYW1lPSdcIiArICQodGhpcykuYXR0cihcImRhdGEtdGVjaC1uYW1lXCIpICsgXCInXVwiKSkpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxVbmluc3RhbGxMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAkKGUudGFyZ2V0KS5wYXJlbnRzKCcubW9kYWwnKS5vbignaGlkZGVuLmJzLm1vZGFsJywgZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgcmV0dXJuIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoXG4gICAgICAgICAgJ3VuaW5zdGFsbCcsXG4gICAgICAgICAgJChcbiAgICAgICAgICAgIHNlbGYubW9kdWxlQWN0aW9uTWVudVVuaW5zdGFsbExpbmtTZWxlY3RvcixcbiAgICAgICAgICAgICQoXCJkaXYubW9kdWxlLWl0ZW0tbGlzdFtkYXRhLXRlY2gtbmFtZT0nXCIgKyAkKGUudGFyZ2V0KS5hdHRyKFwiZGF0YS10ZWNoLW5hbWVcIikgKyBcIiddXCIpXG4gICAgICAgICAgKSxcbiAgICAgICAgICAkKGUudGFyZ2V0KS5hdHRyKFwiZGF0YS1kZWxldGlvblwiKVxuICAgICAgICApO1xuICAgICAgfS5iaW5kKGUpKTtcbiAgICB9KTtcbiAgfTtcblxuICBfZ2V0TW9kdWxlSXRlbVNlbGVjdG9yKCkge1xuICAgIGlmICgkKHRoaXMubW9kdWxlSXRlbUxpc3RTZWxlY3RvcikubGVuZ3RoKSB7XG4gICAgICByZXR1cm4gdGhpcy5tb2R1bGVJdGVtTGlzdFNlbGVjdG9yO1xuICAgIH0gZWxzZSB7XG4gICAgICByZXR1cm4gdGhpcy5tb2R1bGVJdGVtR3JpZFNlbGVjdG9yO1xuICAgIH1cbiAgfTtcblxuICBfY29uZmlybUFjdGlvbihhY3Rpb24sIGVsZW1lbnQpIHtcbiAgICB2YXIgbW9kYWwgPSAkKCcjJyArICQoZWxlbWVudCkuZGF0YSgnY29uZmlybV9tb2RhbCcpKTtcbiAgICBpZiAobW9kYWwubGVuZ3RoICE9IDEpIHtcbiAgICAgIHJldHVybiB0cnVlO1xuICAgIH1cbiAgICBtb2RhbC5maXJzdCgpLm1vZGFsKCdzaG93Jyk7XG5cbiAgICByZXR1cm4gZmFsc2U7IC8vIGRvIG5vdCBhbGxvdyBhLmhyZWYgdG8gcmVsb2FkIHRoZSBwYWdlLiBUaGUgY29uZmlybSBtb2RhbCBkaWFsb2cgd2lsbCBkbyBpdCBhc3luYyBpZiBuZWVkZWQuXG4gIH07XG5cbiAgLyoqXG4gICAqIFVwZGF0ZSB0aGUgY29udGVudCBvZiBhIG1vZGFsIGFza2luZyBhIGNvbmZpcm1hdGlvbiBmb3IgUHJlc3RhVHJ1c3QgYW5kIG9wZW4gaXRcbiAgICpcbiAgICogQHBhcmFtIHthcnJheX0gcmVzdWx0IGNvbnRhaW5pbmcgbW9kdWxlIGRhdGFcbiAgICogQHJldHVybiB7dm9pZH1cbiAgICovXG4gIF9jb25maXJtUHJlc3RhVHJ1c3QocmVzdWx0KSB7XG4gICAgdmFyIHRoYXQgPSB0aGlzO1xuICAgIHZhciBtb2RhbCA9IHRoaXMuX3JlcGxhY2VQcmVzdGFUcnVzdFBsYWNlaG9sZGVycyhyZXN1bHQpO1xuXG4gICAgbW9kYWwuZmluZChcIi5wc3RydXN0LWluc3RhbGxcIikub2ZmKCdjbGljaycpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgLy8gRmluZCByZWxhdGVkIGZvcm0sIHVwZGF0ZSBpdCBhbmQgc3VibWl0IGl0XG4gICAgICB2YXIgaW5zdGFsbF9idXR0b24gPSAkKHRoYXQubW9kdWxlQWN0aW9uTWVudUluc3RhbGxMaW5rU2VsZWN0b3IsICcubW9kdWxlLWl0ZW1bZGF0YS10ZWNoLW5hbWU9XCInICsgcmVzdWx0Lm1vZHVsZS5hdHRyaWJ1dGVzLm5hbWUgKyAnXCJdJyk7XG4gICAgICB2YXIgZm9ybSA9IGluc3RhbGxfYnV0dG9uLnBhcmVudChcImZvcm1cIik7XG4gICAgICAkKCc8aW5wdXQ+JykuYXR0cih7XG4gICAgICAgIHR5cGU6ICdoaWRkZW4nLFxuICAgICAgICB2YWx1ZTogJzEnLFxuICAgICAgICBuYW1lOiAnYWN0aW9uUGFyYW1zW2NvbmZpcm1QcmVzdGFUcnVzdF0nXG4gICAgICB9KS5hcHBlbmRUbyhmb3JtKTtcblxuICAgICAgaW5zdGFsbF9idXR0b24uY2xpY2soKTtcbiAgICAgIG1vZGFsLm1vZGFsKCdoaWRlJyk7XG4gICAgfSk7XG5cbiAgICBtb2RhbC5tb2RhbCgpO1xuICB9O1xuXG4gIF9yZXBsYWNlUHJlc3RhVHJ1c3RQbGFjZWhvbGRlcnMocmVzdWx0KSB7XG4gICAgdmFyIG1vZGFsID0gJChcIiNtb2RhbC1wcmVzdGF0cnVzdFwiKTtcbiAgICB2YXIgbW9kdWxlID0gcmVzdWx0Lm1vZHVsZS5hdHRyaWJ1dGVzO1xuXG4gICAgaWYgKHJlc3VsdC5jb25maXJtYXRpb25fc3ViamVjdCAhPT0gJ1ByZXN0YVRydXN0JyB8fCAhbW9kYWwubGVuZ3RoKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdmFyIGFsZXJ0Q2xhc3MgPSBtb2R1bGUucHJlc3RhdHJ1c3Quc3RhdHVzID8gJ3N1Y2Nlc3MnIDogJ3dhcm5pbmcnO1xuXG4gICAgaWYgKG1vZHVsZS5wcmVzdGF0cnVzdC5jaGVja19saXN0LnByb3BlcnR5KSB7XG4gICAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYnRuLXByb3BlcnR5LW9rXCIpLnNob3coKTtcbiAgICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1idG4tcHJvcGVydHktbm9rXCIpLmhpZGUoKTtcbiAgICB9IGVsc2Uge1xuICAgICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWJ0bi1wcm9wZXJ0eS1va1wiKS5oaWRlKCk7XG4gICAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYnRuLXByb3BlcnR5LW5va1wiKS5zaG93KCk7XG4gICAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYnV5XCIpLmF0dHIoXCJocmVmXCIsIG1vZHVsZS51cmwpLnRvZ2dsZShtb2R1bGUudXJsICE9PSBudWxsKTtcbiAgICB9XG5cbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtaW1nXCIpLmF0dHIoe3NyYzogbW9kdWxlLmltZywgYWx0OiBtb2R1bGUubmFtZX0pO1xuICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1uYW1lXCIpLnRleHQobW9kdWxlLmRpc3BsYXlOYW1lKTtcbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYXV0aG9yXCIpLnRleHQobW9kdWxlLmF1dGhvcik7XG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWxhYmVsXCIpLmF0dHIoXCJjbGFzc1wiLCBcInRleHQtXCIgKyBhbGVydENsYXNzKS50ZXh0KG1vZHVsZS5wcmVzdGF0cnVzdC5zdGF0dXMgPyAnT0snIDogJ0tPJyk7XG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LW1lc3NhZ2VcIikuYXR0cihcImNsYXNzXCIsIFwiYWxlcnQgYWxlcnQtXCIrYWxlcnRDbGFzcyk7XG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LW1lc3NhZ2UgPiBwXCIpLnRleHQobW9kdWxlLnByZXN0YXRydXN0Lm1lc3NhZ2UpO1xuXG4gICAgcmV0dXJuIG1vZGFsO1xuICB9XG5cbiAgX2Rpc3BhdGNoUHJlRXZlbnQoYWN0aW9uLCBlbGVtZW50KSB7XG4gICAgdmFyIGV2ZW50ID0galF1ZXJ5LkV2ZW50KCdtb2R1bGVfY2FyZF9hY3Rpb25fZXZlbnQnKTtcblxuICAgICQoZWxlbWVudCkudHJpZ2dlcihldmVudCwgW2FjdGlvbl0pO1xuICAgIGlmIChldmVudC5pc1Byb3BhZ2F0aW9uU3RvcHBlZCgpICE9PSBmYWxzZSB8fCBldmVudC5pc0ltbWVkaWF0ZVByb3BhZ2F0aW9uU3RvcHBlZCgpICE9PSBmYWxzZSkge1xuICAgICAgcmV0dXJuIGZhbHNlOyAvLyBpZiBhbGwgaGFuZGxlcnMgaGF2ZSBub3QgYmVlbiBjYWxsZWQsIHRoZW4gc3RvcCBwcm9wYWdhdGlvbiBvZiB0aGUgY2xpY2sgZXZlbnQuXG4gICAgfVxuXG4gICAgcmV0dXJuIChldmVudC5yZXN1bHQgIT09IGZhbHNlKTsgLy8gZXhwbGljaXQgZmFsc2UgbXVzdCBiZSBzZXQgZnJvbSBoYW5kbGVycyB0byBzdG9wIHByb3BhZ2F0aW9uIG9mIHRoZSBjbGljayBldmVudC5cbiAgfTtcblxuICBfcmVxdWVzdFRvQ29udHJvbGxlcihhY3Rpb24sIGVsZW1lbnQsIGZvcmNlRGVsZXRpb24sIGRpc2FibGVDYWNoZUNsZWFyLCBjYWxsYmFjaykge1xuICAgIHZhciBzZWxmID0gdGhpcztcbiAgICB2YXIganFFbGVtZW50T2JqID0gZWxlbWVudC5jbG9zZXN0KHRoaXMubW9kdWxlSXRlbUFjdGlvbnNTZWxlY3Rvcik7XG4gICAgdmFyIGZvcm0gPSBlbGVtZW50LmNsb3Nlc3QoXCJmb3JtXCIpO1xuICAgIHZhciBzcGlubmVyT2JqID0gJChcIjxidXR0b24gY2xhc3M9XFxcImJ0bi1wcmltYXJ5LXJldmVyc2Ugb25jbGljayB1bmJpbmQgc3Bpbm5lciBcXFwiPjwvYnV0dG9uPlwiKTtcbiAgICB2YXIgdXJsID0gXCIvL1wiICsgd2luZG93LmxvY2F0aW9uLmhvc3QgKyBmb3JtLmF0dHIoXCJhY3Rpb25cIik7XG4gICAgdmFyIGFjdGlvblBhcmFtcyA9IGZvcm0uc2VyaWFsaXplQXJyYXkoKTtcblxuICAgIGlmIChmb3JjZURlbGV0aW9uID09PSBcInRydWVcIiB8fCBmb3JjZURlbGV0aW9uID09PSB0cnVlKSB7XG4gICAgICBhY3Rpb25QYXJhbXMucHVzaCh7bmFtZTogXCJhY3Rpb25QYXJhbXNbZGVsZXRpb25dXCIsIHZhbHVlOiB0cnVlfSk7XG4gICAgfVxuICAgIGlmIChkaXNhYmxlQ2FjaGVDbGVhciA9PT0gXCJ0cnVlXCIgfHwgZGlzYWJsZUNhY2hlQ2xlYXIgPT09IHRydWUpIHtcbiAgICAgIGFjdGlvblBhcmFtcy5wdXNoKHtuYW1lOiBcImFjdGlvblBhcmFtc1tjYWNoZUNsZWFyRW5hYmxlZF1cIiwgdmFsdWU6IDB9KTtcbiAgICB9XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdXJsOiB1cmwsXG4gICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICBkYXRhOiBhY3Rpb25QYXJhbXMsXG4gICAgICBiZWZvcmVTZW5kOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgIGpxRWxlbWVudE9iai5oaWRlKCk7XG4gICAgICAgIGpxRWxlbWVudE9iai5hZnRlcihzcGlubmVyT2JqKTtcbiAgICAgIH1cbiAgICB9KS5kb25lKGZ1bmN0aW9uIChyZXN1bHQpIHtcbiAgICAgIGlmICh0eXBlb2YgcmVzdWx0ID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogXCJObyBhbnN3ZXIgcmVjZWl2ZWQgZnJvbSBzZXJ2ZXJcIn0pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdmFyIG1vZHVsZVRlY2hOYW1lID0gT2JqZWN0LmtleXMocmVzdWx0KVswXTtcblxuICAgICAgICBpZiAocmVzdWx0W21vZHVsZVRlY2hOYW1lXS5zdGF0dXMgPT09IGZhbHNlKSB7XG4gICAgICAgICAgaWYgKHR5cGVvZiByZXN1bHRbbW9kdWxlVGVjaE5hbWVdLmNvbmZpcm1hdGlvbl9zdWJqZWN0ICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgICAgc2VsZi5fY29uZmlybVByZXN0YVRydXN0KHJlc3VsdFttb2R1bGVUZWNoTmFtZV0pO1xuICAgICAgICAgIH1cblxuICAgICAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IHJlc3VsdFttb2R1bGVUZWNoTmFtZV0ubXNnfSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgJC5ncm93bC5ub3RpY2Uoe21lc3NhZ2U6IHJlc3VsdFttb2R1bGVUZWNoTmFtZV0ubXNnfSk7XG5cbiAgICAgICAgICB2YXIgYWx0ZXJlZFNlbGVjdG9yID0gc2VsZi5fZ2V0TW9kdWxlSXRlbVNlbGVjdG9yKCkucmVwbGFjZSgnLicsICcnKTtcbiAgICAgICAgICB2YXIgbWFpbkVsZW1lbnQgPSBudWxsO1xuXG4gICAgICAgICAgaWYgKGFjdGlvbiA9PSBcInVuaW5zdGFsbFwiKSB7XG4gICAgICAgICAgICBtYWluRWxlbWVudCA9IGpxRWxlbWVudE9iai5jbG9zZXN0KCcuJyArIGFsdGVyZWRTZWxlY3Rvcik7XG4gICAgICAgICAgICBtYWluRWxlbWVudC5yZW1vdmUoKTtcblxuICAgICAgICAgICAgQk9FdmVudC5lbWl0RXZlbnQoXCJNb2R1bGUgVW5pbnN0YWxsZWRcIiwgXCJDdXN0b21FdmVudFwiKTtcbiAgICAgICAgICB9IGVsc2UgaWYgKGFjdGlvbiA9PSBcImRpc2FibGVcIikge1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQgPSBqcUVsZW1lbnRPYmouY2xvc2VzdCgnLicgKyBhbHRlcmVkU2VsZWN0b3IpO1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQuYWRkQ2xhc3MoYWx0ZXJlZFNlbGVjdG9yICsgJy1pc05vdEFjdGl2ZScpO1xuICAgICAgICAgICAgbWFpbkVsZW1lbnQuYXR0cignZGF0YS1hY3RpdmUnLCAnMCcpO1xuXG4gICAgICAgICAgICBCT0V2ZW50LmVtaXRFdmVudChcIk1vZHVsZSBEaXNhYmxlZFwiLCBcIkN1c3RvbUV2ZW50XCIpO1xuICAgICAgICAgIH0gZWxzZSBpZiAoYWN0aW9uID09IFwiZW5hYmxlXCIpIHtcbiAgICAgICAgICAgIG1haW5FbGVtZW50ID0ganFFbGVtZW50T2JqLmNsb3Nlc3QoJy4nICsgYWx0ZXJlZFNlbGVjdG9yKTtcbiAgICAgICAgICAgIG1haW5FbGVtZW50LnJlbW92ZUNsYXNzKGFsdGVyZWRTZWxlY3RvciArICctaXNOb3RBY3RpdmUnKTtcbiAgICAgICAgICAgIG1haW5FbGVtZW50LmF0dHIoJ2RhdGEtYWN0aXZlJywgJzEnKTtcblxuICAgICAgICAgICAgQk9FdmVudC5lbWl0RXZlbnQoXCJNb2R1bGUgRW5hYmxlZFwiLCBcIkN1c3RvbUV2ZW50XCIpO1xuICAgICAgICAgIH1cblxuICAgICAgICAgIGpxRWxlbWVudE9iai5yZXBsYWNlV2l0aChyZXN1bHRbbW9kdWxlVGVjaE5hbWVdLmFjdGlvbl9tZW51X2h0bWwpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfSkuZmFpbChmdW5jdGlvbigpIHtcbiAgICAgIGNvbnN0IG1vZHVsZUl0ZW0gPSBqcUVsZW1lbnRPYmouY2xvc2VzdCgnbW9kdWxlLWl0ZW0tbGlzdCcpO1xuICAgICAgY29uc3QgdGVjaE5hbWUgPSBtb2R1bGVJdGVtLmRhdGEoJ3RlY2hOYW1lJyk7XG4gICAgICAkLmdyb3dsLmVycm9yKHttZXNzYWdlOiBcIkNvdWxkIG5vdCBwZXJmb3JtIGFjdGlvbiBcIithY3Rpb24rXCIgZm9yIG1vZHVsZSBcIit0ZWNoTmFtZX0pO1xuICAgIH0pLmFsd2F5cyhmdW5jdGlvbiAoKSB7XG4gICAgICBqcUVsZW1lbnRPYmouZmFkZUluKCk7XG4gICAgICBzcGlubmVyT2JqLnJlbW92ZSgpO1xuICAgICAgaWYgKGNhbGxiYWNrKSB7XG4gICAgICAgIGNhbGxiYWNrKCk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICByZXR1cm4gZmFsc2U7XG4gIH07XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL21vZHVsZS1jYXJkLmpzIl0sInNvdXJjZVJvb3QiOiIifQ==