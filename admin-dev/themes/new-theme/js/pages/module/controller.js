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

import ConfirmModal from '@components/modal';

const {$} = window;

/**
 * Module Admin Page Controller.
 * @constructor
 */
class AdminModuleController {
  /**
   * Initialize all listeners and bind everything
   * @method init
   * @memberof AdminModule
   */
  constructor(moduleCardController) {
    this.eventEmitter = window.prestashop.component.EventEmitter;
    this.moduleCardController = moduleCardController;

    this.DEFAULT_MAX_RECENTLY_USED = 10;
    this.DISPLAY_LIST = 'list';
    this.CATEGORY_RECENTLY_USED = 'recently-used';

    this.currentDisplay = this.DISPLAY_LIST;
    this.isCategoryGridDisplayed = false;
    this.currentTagsList = [];
    this.currentCategoryFilter = null;
    this.currentModuleStatusFilter = null;
    this.pstaggerInput = null;
    this.lastBulkAction = null;
    this.isUploadStarted = false;
    this.findModuleUsed = false;

    this.recentlyUsedSelector = '#module-recently-used-list .modules-list';

    /**
     * Loaded modules list.
     * Containing the card and list display.
     * @type {Array}
     */
    this.modulesList = [];

    this.moduleShortList = '.module-short-list';

    // Selectors into vars to make it easier to change them while keeping same code logic
    this.moduleItemListSelector = '.module-item-list';
    this.categorySelectorLabelSelector = '.module-category-selector-label';
    this.categorySelector = '.module-category-selector';
    this.categoryItemSelector = '.module-category-menu';
    this.categoryResetBtnSelector = '.module-category-reset';
    this.moduleInstallBtnSelector = 'input.module-install-btn';

    // Upgrade All selectors
    this.upgradeAllSource = '.module_action_menu_upgrade_all';
    this.upgradeContainer = '#modules-list-container-update';
    this.upgradeAllTargets = `${this.upgradeContainer} .module_action_menu_upgrade:visible`;

    // Notification selectors
    this.notificationContainer = '#modules-list-container-notification';

    // Bulk action selectors
    this.bulkActionDropDownSelector = '.module-bulk-actions';
    this.bulkItemSelector = '.module-bulk-menu';
    this.bulkActionCheckboxListSelector = '.module-checkbox-bulk-list input';
    this.checkedBulkActionListSelector = `${this.bulkActionCheckboxListSelector}:checked`;
    this.bulkActionCheckboxSelector = '#module-modal-bulk-checkbox';
    this.bulkConfirmModalSelector = '#module-modal-bulk-confirm';
    this.bulkConfirmModalActionNameSelector = '#module-modal-bulk-confirm-action-name';
    this.bulkConfirmModalListSelector = '#module-modal-bulk-confirm-list';
    this.bulkConfirmModalAckBtnSelector = '#module-modal-confirm-bulk-ack';

    // Module's statuses selectors
    this.statusSelectorLabelSelector = '.module-status-selector-label';
    this.statusItemSelector = '.module-status-menu';
    this.statusResetBtnSelector = '.module-status-reset';

    // Selectors for Module Import
    this.importModalBtnSelector = '#page-header-desc-configuration-add_module';
    this.dropZoneModalSelector = '#module-modal-import';
    this.dropZoneModalFooterSelector = '#module-modal-import .modal-footer';
    this.dropZoneImportZoneSelector = '#importDropzone';
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

    this.initBOEventRegistering();
    this.initBulkDropdown();
    this.initSearchBlock();
    this.initCategorySelect();
    this.initActionButtons();
    this.initAddModuleAction();
    this.initDropzone();
    this.initPageChangeProtection();
    this.initFilterStatusDropdown();
    this.fetchModulesList();
    this.getNotificationsCount();
  }

  initFilterStatusDropdown() {
    const self = this;
    const body = $('body');
    body.on('click', self.statusItemSelector, function () {
      // Get data from li DOM input
      self.currentModuleStatusFilter = parseInt($(this).data('status-ref'), 10);
      // Change dropdown label to set it to the current status' displayname
      $(self.statusSelectorLabelSelector).text($(this).text());
      $(self.statusResetBtnSelector).show();
      self.updateModuleVisibility();
    });

    body.on('click', self.statusResetBtnSelector, function () {
      $(self.statusSelectorLabelSelector).text($(this).text());
      $(this).hide();
      self.currentModuleStatusFilter = null;
      self.updateModuleVisibility();
    });
  }

  initBulkDropdown() {
    const self = this;
    const body = $('body');

    body.on('click', this.bulkActionCheckboxListSelector, () => {
      const selector = $(self.bulkActionDropDownSelector);

      if ($(self.checkedBulkActionListSelector).length > 0) {
        selector.closest('.module-top-menu-item').removeClass('disabled');
      } else {
        selector.closest('.module-top-menu-item').addClass('disabled');
      }
    });

    body.on('click', self.bulkItemSelector, function initializeBodyChange() {
      if ($(self.checkedBulkActionListSelector).length === 0) {
        $.growl.warning({
          message: window.translate_javascripts['Bulk Action - One module minimum'],
        });
        return;
      }

      self.lastBulkAction = $(this).data('ref');
      const modulesListString = self.buildBulkActionModuleList();
      const actionString = $(this).data('display-name').toLowerCase();
      $(self.bulkConfirmModalListSelector).html(modulesListString);
      $(self.bulkConfirmModalActionNameSelector).text(actionString);

      if (self.lastBulkAction === 'bulk-uninstall') {
        $(self.bulkActionCheckboxSelector).show();
      } else {
        $(self.bulkActionCheckboxSelector).hide();
      }

      $(self.bulkConfirmModalSelector).modal('show');
    });

    body.on('click', this.bulkConfirmModalAckBtnSelector, (event) => {
      event.preventDefault();
      event.stopPropagation();
      $(self.bulkConfirmModalSelector).modal('hide');
      self.doBulkAction(self.lastBulkAction);
    });
  }

  initBOEventRegistering() {
    this.eventEmitter.on('Module Enabled', (context) => this.onModuleDisabled(context));
    this.eventEmitter.on('Module Disabled', (context) => this.onModuleDisabled(context));
    this.eventEmitter.on('Module Uninstalled', (context) => this.installHandler(context));
    this.eventEmitter.on('Module Delete', (context) => this.onModuleDelete(context));
    this.eventEmitter.on('Module Installed', (context) => this.installHandler(context));
  }

  installHandler(event) {
    this.updateModuleStatus(event);
    this.updateModuleVisibility();
  }

  /**
   * Updates the modulesList object
   *
   * @param event a DOM element that contains module data such as id, name, version...
   */
  updateModuleStatus(event) {
    this.modulesList = this.modulesList.map((module) => {
      const moduleElement = $(event);

      if ((moduleElement.data('tech-name') === module.techName)
      && (moduleElement.data('version') !== undefined)) {
        const newModule = {
          domObject: moduleElement,
          id: moduleElement.data('id'),
          name: moduleElement.data('name').toLowerCase(),
          scoring: parseFloat(moduleElement.data('scoring')),
          logo: moduleElement.data('logo'),
          author: moduleElement.data('author').toLowerCase(),
          version: moduleElement.data('version'),
          description: moduleElement.data('description').toLowerCase(),
          techName: moduleElement.data('tech-name').toLowerCase(),
          childCategories: moduleElement.data('child-categories'),
          categories: String(moduleElement.data('categories')).toLowerCase(),
          type: moduleElement.data('type'),
          price: parseFloat(moduleElement.data('price')),
          active: parseInt(moduleElement.data('active'), 10),
          installed: moduleElement.data('installed') === 1,
          access: moduleElement.data('last-access'),
          display: this.DISPLAY_LIST,
          container: module.container,
        };

        return newModule;
      }

      return module;
    });
  }

  onModuleDisabled(event) {
    const self = this;
    self.updateModuleStatus(event);
    $('.modules-list').each(() => {
      self.updateModuleVisibility();
    });
  }

  onModuleDelete(event) {
    this.modulesList = this.modulesList.filter((value) => value.techName !== $(event).data('tech-name'));
    this.installHandler(event);
  }

  initPlaceholderMechanism() {
    const self = this;

    if ($(self.placeholderGlobalSelector).length) {
      self.ajaxLoadPage();
    }

    // Retry loading mechanism
    $('body').on('click', self.placeholderFailureRetryBtnSelector, () => {
      $(self.placeholderFailureGlobalSelector).fadeOut();
      $(self.placeholderGlobalSelector).fadeIn();
      self.ajaxLoadPage();
    });
  }

  ajaxLoadPage() {
    const self = this;

    $.ajax({
      method: 'GET',
      url: window.moduleURLs.catalogRefresh,
    })
      .done((response) => {
        if (response.status === true) {
          if (typeof response.domElements === 'undefined') response.domElements = null;
          if (typeof response.msg === 'undefined') response.msg = null;

          const stylesheet = document.styleSheets[0];
          const stylesheetRule = '{display: none}';
          const moduleGlobalSelector = '.modules-list';
          const moduleSortingSelector = '.module-sorting-menu';
          const requiredSelectorCombination = `${moduleGlobalSelector},${moduleSortingSelector}`;

          if (stylesheet.insertRule) {
            stylesheet.insertRule(requiredSelectorCombination + stylesheetRule, stylesheet.cssRules.length);
          } else if (stylesheet.addRule) {
            stylesheet.addRule(requiredSelectorCombination, stylesheetRule, -1);
          }

          $(self.placeholderGlobalSelector).fadeOut(800, () => {
            $.each(response.domElements, (index, element) => {
              $(element.selector).append(element.content);
            });
            $(moduleGlobalSelector)
              .fadeIn(800)
              .css('display', 'flex');
            $(moduleSortingSelector).fadeIn(800);
            $('[data-toggle="popover"]').popover();
            self.initCurrentDisplay();
            self.fetchModulesList();
          });
        } else {
          $(self.placeholderGlobalSelector).fadeOut(800, () => {
            $(self.placeholderFailureMsgSelector).text(response.msg);
            $(self.placeholderFailureGlobalSelector).fadeIn(800);
          });
        }
      })
      .fail((response) => {
        $(self.placeholderGlobalSelector).fadeOut(800, () => {
          $(self.placeholderFailureMsgSelector).text(response.statusText);
          $(self.placeholderFailureGlobalSelector).fadeIn(800);
        });
      });
  }

  fetchModulesList() {
    const self = this;
    let container;
    let $this;

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
          installed: $this.data('installed') === 1,
          access: $this.data('last-access'),
          display: self.DISPLAY_LIST,
          container,
        });

        if (self.isModulesPage()) {
          $this.remove();
        }
      });
    });

    self.updateModuleVisibility();
  }

  updateModuleContainerDisplay() {
    const self = this;

    $('.module-short-list').each(function setShortListVisibility() {
      const container = $(this);
      const nbModulesInContainer = container.find('.module-item').length;

      if (
        (self.currentCategoryFilter
          && self.currentCategoryFilter !== String(container.find('.modules-list').data('name')))
        || (self.currentModuleStatusFilter !== null && nbModulesInContainer === 0)
        || (nbModulesInContainer === 0
          && String(container.find('.modules-list').data('name')) === self.CATEGORY_RECENTLY_USED)
        || (self.currentTagsList.length > 0 && nbModulesInContainer === 0)
      ) {
        container.hide();
        return;
      }

      container.show();
    });
  }

  updateModuleVisibility() {
    const self = this;

    // Remove recently used and modules list if we are on the modules page and no read more modal is opened
    if (self.isModulesPage() && !self.isReadMoreModalOpened()) {
      $(self.recentlyUsedSelector)
        .find('.module-item')
        .remove();
      $('.modules-list')
        .find('.module-item')
        .remove();
    }

    // Modules visibility management
    let isVisible;
    let currentModule;
    let moduleCategory;
    let tagExists;
    let newValue;

    const paramsUrl = (new URL(document.location)).searchParams;
    const findModule = paramsUrl.get('find');

    if (findModule && self.findModuleUsed !== true) {
      self.currentTagsList.push(findModule);
      self.findModuleUsed = true;
    } else if (findModule) {
      self.currentTagsList.pop(findModule);
    }

    const modulesListLength = self.modulesList.length;
    let counter = 0;
    const checkTag = (index, value) => {
      newValue = value.toLowerCase();
      tagExists
        |= currentModule.name.indexOf(newValue) !== -1
        || currentModule.description.indexOf(newValue) !== -1
        || currentModule.author.indexOf(newValue) !== -1
        || currentModule.techName.indexOf(newValue) !== -1;
    };

    for (let i = 0; i < modulesListLength; i += 1) {
      currentModule = self.modulesList[i];
      isVisible = true;

      // Check if we are displaying normal categories or the recently used list
      moduleCategory = self.currentCategoryFilter === self.CATEGORY_RECENTLY_USED
        ? self.CATEGORY_RECENTLY_USED
        : currentModule.categories;

      // If category filter is set, we display only modules matching the current category
      if (self.currentCategoryFilter !== null) {
        isVisible &= moduleCategory === self.currentCategoryFilter;
      }

      // Check if the module status filter is enabled and hide modules that
      // don't match it.
      if (self.currentModuleStatusFilter !== null) {
        isVisible &= (
          (
            currentModule.active === self.currentModuleStatusFilter
              && currentModule.installed === true
          )
            || (
              currentModule.installed === false
                && self.currentModuleStatusFilter === 2
            ) || (
            currentModule.installed === true
                && self.currentModuleStatusFilter === 3
          )
        );
      }

      // Check for tag list
      if (self.currentTagsList.length) {
        tagExists = false;
        $.each(self.currentTagsList, checkTag);
        isVisible &= tagExists;
      }

      // If we are not searching for a module, we need to manage module
      // visibility within categories. If it's the recently used category,
      // we will hide the module if we already reached the max limit.
      if (!self.currentTagsList.length && moduleCategory === self.CATEGORY_RECENTLY_USED
        && counter >= self.DEFAULT_MAX_RECENTLY_USED) {
        isVisible = false;
      }

      // If visible, display (Thx captain obvious)
      if (isVisible) {
        counter += 1;
        if (self.currentCategoryFilter === self.CATEGORY_RECENTLY_USED) {
          $(self.recentlyUsedSelector).append(currentModule.domObject);
        } else {
          currentModule.container.append(currentModule.domObject);
        }
      }
    }

    self.updateModuleContainerDisplay();
    self.updateTotalResults();
  }

  initPageChangeProtection() {
    const self = this;

    $(window).on('beforeunload', () => {
      if (self.isUploadStarted === true) {
        return (
          'It seems some critical operation are running, are you sure you want to change page? '
          + 'It might cause some unexepcted behaviors.'
        );
      }

      return undefined;
    });
  }

  buildBulkActionModuleList() {
    const checkBoxesSelector = this.checkedBulkActionListSelector;
    const moduleItemSelector = this.moduleItemListSelector;
    let alreadyDoneFlag = 0;
    let htmlGenerated = '';
    let currentElement;

    $(checkBoxesSelector).each(function prepareCheckboxes() {
      if (alreadyDoneFlag === 10) {
        // Break each
        htmlGenerated += '- ...';
        return false;
      }

      currentElement = $(this).closest(moduleItemSelector);
      htmlGenerated += `- ${currentElement.data('name')}<br/>`;
      alreadyDoneFlag += 1;

      return true;
    });

    return htmlGenerated;
  }

  initAddModuleAction() {
    const self = this;
    const addModuleButton = $(self.importModalBtnSelector);
    addModuleButton.attr('data-toggle', 'modal');
    addModuleButton.attr('data-target', self.dropZoneModalSelector);
  }

  initDropzone() {
    const self = this;
    const body = $('body');
    const dropzone = $('.dropzone');

    // Reset modal when click on Retry in case of failure
    body.on('click', this.moduleImportFailureRetrySelector, () => {
      /* eslint-disable max-len */
      $(
        `${self.moduleImportSuccessSelector},${self.moduleImportFailureSelector},${self.moduleImportProcessingSelector}`,
      ).fadeOut(() => {
        /**
         * Added timeout for a better render of animation
         * and avoid to have displayed at the same time
         */
        setTimeout(() => {
          $(self.moduleImportStartSelector).fadeIn(() => {
            $(self.moduleImportFailureMsgDetailsSelector).hide();
            $(self.moduleImportSuccessConfigureBtnSelector).hide();
            dropzone.removeAttr('style');
          });
        }, 550);
      });
      /* eslint-enable max-len */
    });

    // Reinit modal on exit, but check if not already processing something
    body.on('hidden.bs.modal', this.dropZoneModalSelector, () => {
      $(`${self.moduleImportSuccessSelector}, ${self.moduleImportFailureSelector}`).hide();
      $(self.moduleImportStartSelector).show();

      dropzone.removeAttr('style');
      $(self.moduleImportFailureMsgDetailsSelector).hide();
      $(self.moduleImportSuccessConfigureBtnSelector).hide();
      $(self.dropZoneModalFooterSelector).html('');
      $(self.moduleImportConfirmSelector).hide();
    });

    // Change the way Dropzone.js lib handle file input trigger
    body.on(
      'click',
      `.dropzone:not(${this.moduleImportSelectFileManualSelector}, ${this.moduleImportSuccessConfigureBtnSelector})`,
      (event, manualSelect) => {
        // if click comes from .module-import-start-select-manual, stop everything
        if (typeof manualSelect === 'undefined') {
          event.stopPropagation();
          event.preventDefault();
        }
      },
    );

    body.on('click', this.moduleImportSelectFileManualSelector, (event) => {
      event.stopPropagation();
      event.preventDefault();
      /**
       * Trigger click on hidden file input, and pass extra data
       * to .dropzone click handler fro it to notice it comes from here
       */
      $('.dz-hidden-input').trigger('click', ['manual_select']);
    });

    // Handle modal closure
    body.on('click', this.moduleImportModalCloseBtn, () => {
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
    body.on('click', this.moduleImportFailureDetailsBtnSelector, () => {
      $(self.moduleImportFailureMsgDetailsSelector).slideDown();
    });

    // @see: dropzone.js
    const dropzoneOptions = {
      url: window.moduleURLs.moduleImport,
      acceptedFiles: '.zip, .tar',
      // The name that will be used to transfer the file
      paramName: 'file_uploaded',
      uploadMultiple: false,
      addRemoveLinks: true,
      dictDefaultMessage: '',
      hiddenInputContainer: self.dropZoneImportZoneSelector,
      /**
       * Add unlimited timeout. Otherwise dropzone timeout is 30 seconds
       *  and if a module is long to install, it is not possible to install the module.
       */
      timeout: 0,
      addedfile: () => {
        $(`${self.moduleImportSuccessSelector}, ${self.moduleImportFailureSelector}`).hide();
        self.animateStartUpload();
      },
      processing: () => {
        // Leave it empty since we don't require anything while processing upload
      },
      error: (file, message) => {
        self.displayOnUploadError(message);
      },
      complete: (file) => {
        if (file.status !== 'error') {
          const responseObject = $.parseJSON(file.xhr.response);

          if (typeof responseObject.is_configurable === 'undefined') responseObject.is_configurable = null;
          if (typeof responseObject.module_name === 'undefined') responseObject.module_name = null;

          self.displayOnUploadDone(responseObject);

          const elem = $(`<div data-tech-name="${responseObject.module_name}"></div>`);
          this.eventEmitter.emit((responseObject.upgraded ? 'Module Upgraded' : 'Module Installed'), elem);
        }
        // State that we have finish the process to unlock some actions
        self.isUploadStarted = false;
      },
    };

    dropzone.dropzone($.extend(dropzoneOptions));
  }

  animateStartUpload() {
    const self = this;
    const dropzone = $('.dropzone');
    // State that we start module upload
    self.isUploadStarted = true;
    $(self.moduleImportStartSelector).hide(0);
    dropzone.css('border', 'none');
    $(self.moduleImportProcessingSelector).fadeIn();
  }

  animateEndUpload(callback) {
    const self = this;
    $(self.moduleImportProcessingSelector)
      .finish()
      .fadeOut(callback);
  }

  /**
   * Method to call for upload modal, when the ajax call went well.
   *
   * @param object result containing the server response
   */
  displayOnUploadDone(result) {
    const self = this;
    self.animateEndUpload(() => {
      if (result.status === true) {
        if (result.is_configurable === true) {
          const configureLink = window.moduleURLs.configurationPage.replace(/:number:/, result.module_name);
          $(self.moduleImportSuccessConfigureBtnSelector).attr('href', configureLink);
          $(self.moduleImportSuccessConfigureBtnSelector).show();
        }
        $(self.moduleImportSuccessSelector).fadeIn();
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
  displayOnUploadError(message) {
    const self = this;
    self.animateEndUpload(() => {
      $(self.moduleImportFailureMsgDetailsSelector).html(message);
      $(self.moduleImportFailureSelector).fadeIn();
    });
  }

  /**
   * Get the module notifications count and displays it as a badge on the notification tab
   * @return void
   */
  getNotificationsCount() {
    const self = this;
    $.getJSON(window.moduleURLs.notificationsCount, self.updateNotificationsCount).fail(() => {
      console.error('Could not retrieve module notifications count.');
    });
  }

  updateNotificationsCount(badge) {
    const destinationTabs = {
      to_configure: $('#subtab-AdminModulesNotifications'),
      to_update: $('#subtab-AdminModulesUpdates'),
    };

    Object.keys(destinationTabs).forEach((destinationKey) => {
      if (destinationTabs[destinationKey].length !== 0) {
        destinationTabs[destinationKey].find('.notification-counter').text(badge[destinationKey]);
      }
    });
  }

  doBulkAction(requestedBulkAction) {
    // This object is used to check if requested bulkAction is available and give proper
    // url segment to be called for it
    const forceDeletion = $('#force_bulk_deletion').prop('checked');

    const bulkActionToUrl = {
      'bulk-install': 'install',
      'bulk-uninstall': 'uninstall',
      'bulk-disable': 'disable',
      'bulk-enable': 'enable',
      'bulk-reset': 'reset',
      'bulk-delete': 'delete',
    };

    // Note no grid selector used yet since we do not needed it at dev time
    // Maybe useful to implement this kind of things later if intended to
    // use this functionality elsewhere but "manage my module" section
    if (typeof bulkActionToUrl[requestedBulkAction] === 'undefined') {
      $.growl.error({
        message: window.translate_javascripts['Bulk Action - Request not found'].replace('[1]', requestedBulkAction),
      });
      return false;
    }

    // Loop over all checked bulk checkboxes
    const bulkActionSelectedSelector = this.checkedBulkActionListSelector;
    const bulkModuleAction = bulkActionToUrl[requestedBulkAction];

    if ($(bulkActionSelectedSelector).length <= 0) {
      console.warn(window.translate_javascripts['Bulk Action - One module minimum']);
      return false;
    }

    const modulesActions = [];
    let moduleTechName;
    $(bulkActionSelectedSelector).each(function bulkActionSelector() {
      moduleTechName = $(this).data('tech-name');
      modulesActions.push({
        techName: moduleTechName,
        actionMenuObj: $(this)
          .closest('.module-checkbox-bulk-list')
          .next(),
      });
    });

    this.performModulesAction(modulesActions, bulkModuleAction, forceDeletion);

    return true;
  }

  performModulesAction(modulesActions, bulkModuleAction, forceDeletion) {
    const self = this;

    if (typeof self.moduleCardController === 'undefined') {
      return;
    }

    // First let's filter modules that can't perform this action
    const actionMenuLinks = filterAllowedActions(modulesActions);

    if (!actionMenuLinks.length) {
      return;
    }

    // Begin actions one after another
    unstackModulesActions();

    function requestModuleAction(actionMenuLink) {
      if (self.moduleCardController.hasPendingRequest()) {
        actionMenuLinks.push(actionMenuLink);
        return;
      }

      self.moduleCardController.requestToController(
        bulkModuleAction,
        actionMenuLink,
        forceDeletion,
        unstackModulesActions,
      );
    }

    function unstackModulesActions() {
      if (actionMenuLinks.length <= 0) {
        return;
      }

      const actionMenuLink = actionMenuLinks.shift();
      requestModuleAction(actionMenuLink);
    }

    function filterAllowedActions(actions) {
      const menuLinks = [];
      let actionMenuLink;
      $.each(actions, (index, moduleData) => {
        actionMenuLink = $(
          self.moduleCardController.moduleActionMenuLinkSelector + bulkModuleAction,
          moduleData.actionMenuObj,
        );
        if (actionMenuLink.length > 0) {
          menuLinks.push(actionMenuLink);
        } else {
          $.growl.error({
            message: window.translate_javascripts['Bulk Action - Request not available for module']
              .replace('[1]', bulkModuleAction)
              .replace('[2]', moduleData.techName),
          });
        }
      });

      return menuLinks;
    }
  }

  initActionButtons() {
    const self = this;
    $('body').on('click', self.moduleInstallBtnSelector, function initializeActionButtonsClick(event) {
      const $this = $(this);
      const $next = $($this.next());
      event.preventDefault();

      $this.hide();
      $next.show();

      $.ajax({
        url: $this.data('url'),
        dataType: 'json',
      }).done(() => {
        $next.fadeOut();
      });
    });

    // "Upgrade All" button handler
    $('body').on('click', self.upgradeAllSource, (event) => {
      event.preventDefault();
      const isMaintenanceMode = window.isShopMaintenance;

      // Modal body element
      const maintenanceLink = document.createElement('a');
      maintenanceLink.classList.add('btn', 'btn-primary', 'btn-lg');
      maintenanceLink.setAttribute('href', window.moduleURLs.maintenancePage);
      maintenanceLink.innerHTML = window.moduleTranslations.moduleModalUpdateMaintenance;

      const updateAllConfirmModal = new ConfirmModal(
        {
          id: 'confirm-module-update-modal',
          confirmTitle: window.moduleTranslations.singleModuleModalUpdateTitle,
          closeButtonLabel: window.moduleTranslations.moduleModalUpdateCancel,
          confirmButtonLabel: isMaintenanceMode
            ? window.moduleTranslations.moduleModalUpdateUpgrade
            : window.moduleTranslations.upgradeAnywayButtonText,
          confirmButtonClass: isMaintenanceMode ? 'btn-primary' : 'btn-secondary',
          confirmMessage: isMaintenanceMode ? '' : window.moduleTranslations.moduleModalUpdateConfirmMessage,
          closable: true,
          customButtons: isMaintenanceMode ? [] : [maintenanceLink],
        },
        () => {
          if ($(self.upgradeAllTargets).length <= 0) {
            console.warn(window.translate_javascripts['Upgrade All Action - One module minimum']);
            return false;
          }

          const modulesActions = [];
          let moduleTechName;
          $(self.upgradeAllTargets).each(function bulkActionSelector() {
            const moduleItemList = $(this).closest('.module-item-list');
            moduleTechName = moduleItemList.data('tech-name');
            modulesActions.push({
              techName: moduleTechName,
              actionMenuObj: $('.module-actions', moduleItemList),
            });
          });

          this.performModulesAction(modulesActions, 'upgrade');

          return true;
        },
      );

      updateAllConfirmModal.show();

      return true;
    });
  }

  initCategorySelect() {
    const self = this;
    const body = $('body');
    body.on('click', self.categoryItemSelector, function initializeCategorySelectClick() {
      // Get data from li DOM input
      self.currentCategoryFilter = $(this).data('category-ref');
      self.currentCategoryFilter = self.currentCategoryFilter ? String(self.currentCategoryFilter).toLowerCase() : null;
      // Change dropdown label to set it to the current category's displayname
      $(self.categorySelectorLabelSelector).text($(this).data('category-display-name'));
      $(self.categoryResetBtnSelector).show();
      self.updateModuleVisibility();
    });

    body.on('click', self.categoryResetBtnSelector, function initializeCategoryResetButtonClick() {
      const rawText = $(self.categorySelector).attr('aria-labelledby');
      const upperFirstLetter = rawText.charAt(0).toUpperCase();
      const removedFirstLetter = rawText.slice(1);
      const originalText = upperFirstLetter + removedFirstLetter;

      $(self.categorySelectorLabelSelector).text(originalText);
      $(this).hide();
      self.currentCategoryFilter = null;
      self.updateModuleVisibility();
    });
  }

  initSearchBlock() {
    const self = this;
    self.pstaggerInput = $('#module-search-bar').pstagger({
      onTagsChanged: (tagList) => {
        self.currentTagsList = tagList;
        self.updateModuleVisibility();
      },
      onResetTags: () => {
        self.currentTagsList = [];
        self.updateModuleVisibility();
      },
      inputPlaceholder: window.translate_javascripts['Search - placeholder'],
      closingCross: true,
      context: self,
    });
  }

  updateTotalResults() {
    const replaceFirstWordBy = (element, value) => {
      const explodedText = element.text().split(' ');
      explodedText[0] = value;
      element.text(explodedText.join(' '));
    };

    // If there are some shortlist: each shortlist count the modules on the next container.
    const $shortLists = $('.module-short-list');

    if ($shortLists.length > 0) {
      $shortLists.each(function shortLists() {
        const $this = $(this);
        replaceFirstWordBy(
          $this.find('.module-search-result-wording'),
          $this.next('.modules-list').find('.module-item').length,
        );
      });

      // If there is no shortlist: the wording directly update from the only module container.
    } else {
      const modulesCount = $('.modules-list').find('.module-item').length;
      replaceFirstWordBy($('.module-search-result-wording'), modulesCount);

      // eslint-disable-next-line
      $(this.addonItemListSelector).toggle(modulesCount !== this.modulesList.length / 2);
    }
  }

  isModulesPage() {
    return $(this.upgradeContainer).length === 0 && $(this.notificationContainer).length === 0;
  }

  isReadMoreModalOpened() {
    return $('.modal-read-more').is(':visible');
  }
}

export default AdminModuleController;
