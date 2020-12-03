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

const $ = window.$;

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
    this.checkedBulkActionListSelector = `${this.bulkActionCheckboxListSelector}:checked`;
    this.checkedBulkActionGridSelector = `${this.bulkActionCheckboxGridSelector}:checked`;
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

  initFilterStatusDropdown() {
    const self = this;
    const body = $('body');
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

  initBulkDropdown() {
    const self = this;
    const body = $('body');


    body.on('click', self.getBulkCheckboxesSelector(), () => {
      const selector = $(self.bulkActionDropDownSelector);
      if ($(self.getBulkCheckboxesCheckedSelector()).length > 0) {
        selector.closest('.module-top-menu-item')
                .removeClass('disabled');
      } else {
        selector.closest('.module-top-menu-item')
                .addClass('disabled');
      }
    });

    body.on('click', self.bulkItemSelector, function initializeBodyChange() {
      if ($(self.getBulkCheckboxesCheckedSelector()).length === 0) {
        $.growl.warning({message: window.translate_javascripts['Bulk Action - One module minimum']});
        return;
      }

      self.lastBulkAction = $(this).data('ref');
      const modulesListString = self.buildBulkActionModuleList();
      const actionString = $(this).find(':checked').text().toLowerCase();
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
    window.BOEvent.on('Module Disabled', this.onModuleDisabled, this);
    window.BOEvent.on('Module Uninstalled', this.updateTotalResults, this);
  }

  onModuleDisabled() {
    const self = this;
    const moduleItemSelector = self.getModuleItemSelector();

    $('.modules-list').each(function scanModulesList() {
      self.updateTotalResults();
    });
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
    }).done((response) => {
      if (response.status === true) {
        if (typeof response.domElements === 'undefined') response.domElements = null;
        if (typeof response.msg === 'undefined') response.msg = null;

        const stylesheet = document.styleSheets[0];
        const stylesheetRule = '{display: none}';
        const moduleGlobalSelector = '.modules-list';
        const moduleSortingSelector = '.module-sorting-menu';
        const requiredSelectorCombination = `${moduleGlobalSelector},${moduleSortingSelector}`;

        if (stylesheet.insertRule) {
          stylesheet.insertRule(
            requiredSelectorCombination +
            stylesheetRule, stylesheet.cssRules.length
          );
        } else if (stylesheet.addRule) {
          stylesheet.addRule(
            requiredSelectorCombination,
            stylesheetRule,
            -1
          );
        }

        $(self.placeholderGlobalSelector).fadeOut(800, () => {
          $.each(response.domElements, (index, element) => {
            $(element.selector).append(element.content);
          });
          $(moduleGlobalSelector).fadeIn(800).css('display', 'flex');
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
    }).fail((response) => {
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
          access: $this.data('last-access'),
          display: $this.hasClass('module-item-list') ? self.DISPLAY_LIST : self.DISPLAY_GRID,
          container,
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
  updateModuleSorting() {
    const self = this;

    if (!self.currentSorting) {
      return;
    }

    // Modules sorting
    let order = 'asc';
    let key = self.currentSorting;
    const splittedKey = key.split('-');
    if (splittedKey.length > 1) {
      key = splittedKey[0];
      if (splittedKey[1] === 'desc') {
        order = 'desc';
      }
    }

    const currentCompare = (a, b) => {
      let aData = a[key];
      let bData = b[key];
      if (key === 'access') {
        aData = (new Date(aData)).getTime();
        bData = (new Date(bData)).getTime();
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

  updateModuleContainerDisplay() {
    const self = this;

    $('.module-short-list').each(function setShortListVisibility() {
      const container = $(this);
      const nbModulesInContainer = container.find('.module-item').length;
      if (
        (
          self.currentRefCategory
          && self.currentRefCategory !== String(container.find('.modules-list').data('name'))
        ) || (
          self.currentRefStatus !== null
          && nbModulesInContainer === 0
        ) || (
          nbModulesInContainer === 0
          && String(container.find('.modules-list').data('name')) === self.CATEGORY_RECENTLY_USED
        ) || (
          self.currentTagsList.length > 0
          && nbModulesInContainer === 0
        )
      ) {
        container.hide();
        return;
      }

      container.show();
      if (nbModulesInContainer >= self.DEFAULT_MAX_PER_CATEGORIES) {
        container.find(`${self.seeMoreSelector}, ${self.seeLessSelector}`).show();
      } else {
        container.find(`${self.seeMoreSelector}, ${self.seeLessSelector}`).hide();
      }
    });
  }

  updateModuleVisibility() {
    const self = this;

    self.updateModuleSorting();

    $(self.recentlyUsedSelector).find('.module-item').remove();
    $('.modules-list').find('.module-item').remove();

    // Modules visibility management
    let isVisible;
    let currentModule;
    let moduleCategory;
    let tagExists;
    let newValue;

    const modulesListLength = self.modulesList.length;
    const counter = {};

    for (let i = 0; i < modulesListLength; i += 1) {
      currentModule = self.modulesList[i];
      if (currentModule.display === self.currentDisplay) {
        isVisible = true;

        moduleCategory = self.currentRefCategory === self.CATEGORY_RECENTLY_USED ?
                         self.CATEGORY_RECENTLY_USED :
                         currentModule.categories;

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
          $.each(self.currentTagsList, (index, value) => {
            newValue = value.toLowerCase();
            tagExists |= (
              currentModule.name.indexOf(newValue) !== -1
              || currentModule.description.indexOf(newValue) !== -1
              || currentModule.author.indexOf(newValue) !== -1
              || currentModule.techName.indexOf(newValue) !== -1
            );
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

  initPageChangeProtection() {
    const self = this;

    $(window).on('beforeunload', () => {
      if (self.isUploadStarted === true) {
        return 'It seems some critical operation are running, are you sure you want to change page ? It might cause some unexepcted behaviors.';
      }
    });
  }


  buildBulkActionModuleList() {
    const checkBoxesSelector = this.getBulkCheckboxesCheckedSelector();
    const moduleItemSelector = this.getModuleItemSelector();
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

  initAddonsConnect() {
    const self = this;

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
        beforeSend: () => {
          $(self.addonsLoginButtonSelector).show();
          $('button.btn[type="submit"]', self.addonsConnectForm).hide();
        }
      }).done((response) => {
        if (response.success === 1) {
          location.reload();
        } else {
          $.growl.error({message: response.message});
          $(self.addonsLoginButtonSelector).hide();
          $('button.btn[type="submit"]', self.addonsConnectForm).fadeIn();
        }
      });
    });
  }

  initAddModuleAction() {
    const self = this;
    const addModuleButton = $(self.addonsImportModalBtnSelector);
    addModuleButton.attr('data-toggle', 'modal');
    addModuleButton.attr('data-target', self.dropZoneModalSelector);
  }

  initDropzone() {
    const self = this;
    const body = $('body');
    const dropzone = $('.dropzone');

    // Reset modal when click on Retry in case of failure
    body.on(
      'click',
      this.moduleImportFailureRetrySelector,
      () => {
        $(`${self.moduleImportSuccessSelector},${self.moduleImportFailureSelector},${self.moduleImportProcessingSelector}`).fadeOut(() => {
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
      }
    );

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
      }
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
      addedfile: () => {
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
    $(self.moduleImportProcessingSelector).finish().fadeOut(callback);
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
  displayOnUploadError(message) {
    const self = this;
    self.animateEndUpload(() => {
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
  displayPrestaTrustStep(result) {
    const self = this;
    const modal = self.moduleCardController._replacePrestaTrustPlaceholders(result);
    const moduleName = result.module.attributes.name;

    $(this.moduleImportConfirmSelector).html(modal.find('.modal-body').html()).fadeIn();
    $(this.dropZoneModalFooterSelector).html(modal.find('.modal-footer').html()).fadeIn();

    $(this.dropZoneModalFooterSelector).find('.pstrust-install').off('click').on('click', () => {
      $(self.moduleImportConfirmSelector).hide();
      $(self.dropZoneModalFooterSelector).html('');
      self.animateStartUpload();

      // Install ajax call
      $.post(result.module.attributes.urls.install, {'actionParams[confirmPrestaTrust]': '1'})
       .done((data) => {
         self.displayOnUploadDone(data[moduleName]);
       })
       .fail((data) => {
         self.displayOnUploadError(data[moduleName]);
       })
       .always(() => {
         self.isUploadStarted = false;
       });
    });
  }

  getBulkCheckboxesSelector() {
    return this.currentDisplay === this.DISPLAY_GRID
         ? this.bulkActionCheckboxGridSelector
         : this.bulkActionCheckboxListSelector;
  }


  getBulkCheckboxesCheckedSelector() {
    return this.currentDisplay === this.DISPLAY_GRID
         ? this.checkedBulkActionGridSelector
         : this.checkedBulkActionListSelector;
  }

  getModuleItemSelector() {
    return this.currentDisplay === this.DISPLAY_GRID
         ? this.moduleItemGridSelector
         : this.moduleItemListSelector;
  }

  /**
   * Get the module notifications count and displays it as a badge on the notification tab
   * @return void
   */
  getNotificationsCount() {
    const self = this;
    $.getJSON(
      window.moduleURLs.notificationsCount,
      self.updateNotificationsCount
    ).fail(() => {
      console.error('Could not retrieve module notifications count.');
    });
  }

  updateNotificationsCount(badge) {
    const destinationTabs = {
      to_configure: $('#subtab-AdminModulesNotifications'),
      to_update: $('#subtab-AdminModulesUpdates'),
    };

    for (let key in destinationTabs) {
      if (destinationTabs[key].length === 0) {
        continue;
      }

      destinationTabs[key].find('.notification-counter').text(badge[key]);
    }
  }

  initAddonsSearch() {
    const self = this;
    $('body').on(
      'click',
      `${self.addonItemGridSelector}, ${self.addonItemListSelector}`,
      () => {
        let searchQuery = '';
        if (self.currentTagsList.length) {
          searchQuery = encodeURIComponent(self.currentTagsList.join(' '));
        }

        window.open(`${self.baseAddonsUrl}search.php?search_query=${searchQuery}`, '_blank');
      }
    );
  }

  initCategoriesGrid() {
    const self = this;

    $('body').on('click', this.categoryGridItemSelector, function initilaizeGridBodyClick(event) {
      event.stopPropagation();
      event.preventDefault();
      const refCategory = $(this).data('category-ref');

      // In case we have some tags we need to reset it !
      if (self.currentTagsList.length) {
        self.pstaggerInput.resetTags(false);
        self.currentTagsList = [];
      }
      const menuCategoryToTrigger = $(`${self.categoryItemSelector}[data-category-ref="${refCategory}"]`);

      if (!menuCategoryToTrigger.length) {
        console.warn(`No category with ref (${refCategory}) seems to exist!`);
        return false;
      }

      // Hide current category grid
      if (self.isCategoryGridDisplayed === true) {
        $(self.categoryGridSelector).fadeOut();
        self.isCategoryGridDisplayed = false;
      }

      // Trigger click on right category
      $(`${self.categoryItemSelector}[data-category-ref="${refCategory}"]`).click();
      return true;
    });
  }

  initCurrentDisplay() {
    this.currentDisplay = this.currentDisplay === '' ? this.DISPLAY_LIST : this.DISPLAY_GRID;
  }

  initSortingDropdown() {
    const self = this;

    self.currentSorting = $(this.moduleSortingDropdownSelector).find(':checked').attr('value');
    if (!self.currentSorting) {
      self.currentSorting = 'access-desc';
    }

    $('body').on(
      'change',
      self.moduleSortingDropdownSelector,
      function initializeBodySortingChange() {
        self.currentSorting = $(this).find(':checked').attr('value');
        self.updateModuleVisibility();
      }
    );
  }

  doBulkAction(requestedBulkAction) {
    // This object is used to check if requested bulkAction is available and give proper
    // url segment to be called for it
    const forceDeletion = $('#force_bulk_deletion').prop('checked');

    const bulkActionToUrl = {
      'bulk-uninstall': 'uninstall',
      'bulk-disable': 'disable',
      'bulk-enable': 'enable',
      'bulk-disable-mobile': 'disable_mobile',
      'bulk-enable-mobile': 'enable_mobile',
      'bulk-reset': 'reset',
    };

    // Note no grid selector used yet since we do not needed it at dev time
    // Maybe useful to implement this kind of things later if intended to
    // use this functionality elsewhere but "manage my module" section
    if (typeof bulkActionToUrl[requestedBulkAction] === 'undefined') {
      $.growl.error({message: window.translate_javascripts['Bulk Action - Request not found'].replace('[1]', requestedBulkAction)});
      return false;
    }

    // Loop over all checked bulk checkboxes
    const bulkActionSelectedSelector = this.getBulkCheckboxesCheckedSelector();
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
        actionMenuObj: $(this).closest('.module-checkbox-bulk-list').next(),
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

    //First let's filter modules that can't perform this action
    let actionMenuLinks = filterAllowedActions(modulesActions);
    if (!actionMenuLinks.length) {
      return;
    }

    let modulesRequestedCountdown = actionMenuLinks.length - 1;
    let spinnerObj = $("<button class=\"btn-primary-reverse onclick unbind spinner \"></button>");
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
      const lastMenuLink = actionMenuLinks[actionMenuLinks.length - 1];
      const actionMenuObj = lastMenuLink.closest(self.moduleCardController.moduleItemActionsSelector);
      actionMenuObj.hide();
      actionMenuObj.after(spinnerObj);
    } else {
      requestModuleAction(actionMenuLinks[0]);
    }

    function requestModuleAction(actionMenuLink, disableCacheClear, requestEndCallback) {
      self.moduleCardController._requestToController(
        bulkModuleAction,
        actionMenuLink,
        forceDeletion,
        disableCacheClear,
        requestEndCallback
      );
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

        const lastMenuLink = actionMenuLinks[actionMenuLinks.length - 1];
        const actionMenuObj = lastMenuLink.closest(self.moduleCardController.moduleItemActionsSelector);
        actionMenuObj.fadeIn();
        requestModuleAction(lastMenuLink);
      }
    }

    function filterAllowedActions(modulesActions) {
      let actionMenuLinks = [];
      let actionMenuLink;
      $.each(modulesActions, function filterAllowedModules(index, moduleData) {
        actionMenuLink = $(
          self.moduleCardController.moduleActionMenuLinkSelector + bulkModuleAction,
          moduleData.actionMenuObj
        );
        if (actionMenuLink.length > 0) {
          actionMenuLinks.push(actionMenuLink);
        } else {
          $.growl.error({message: window.translate_javascripts['Bulk Action - Request not available for module']
              .replace('[1]', bulkModuleAction)
              .replace('[2]', moduleData.techName)});
        }
      });

      return actionMenuLinks;
    }
  }

  initActionButtons() {
    const self = this;
    $('body').on(
      'click',
      self.moduleInstallBtnSelector,
      function initializeActionButtonsClick(event) {
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
      }
    );

    // "Upgrade All" button handler
    $('body').on('click', self.upgradeAllSource, (event) => {
      event.preventDefault();

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
    });
  }

  initCategorySelect() {
    const self = this;
    const body = $('body');
    body.on(
      'click',
      self.categoryItemSelector,
      function initializeCategorySelectClick() {
        // Get data from li DOM input
        self.currentRefCategory = $(this).data('category-ref');
        self.currentRefCategory = self.currentRefCategory ? String(self.currentRefCategory).toLowerCase() : null;
        // Change dropdown label to set it to the current category's displayname
        $(self.categorySelectorLabelSelector).text($(this).data('category-display-name'));
        $(self.categoryResetBtnSelector).show();
        self.updateModuleVisibility();
      }
    );

    body.on(
      'click',
      self.categoryResetBtnSelector,
      function initializeCategoryResetButtonClick() {
        const rawText = $(self.categorySelector).attr('aria-labelledby');
        const upperFirstLetter = rawText.charAt(0).toUpperCase();
        const removedFirstLetter = rawText.slice(1);
        const originalText = upperFirstLetter + removedFirstLetter;

        $(self.categorySelectorLabelSelector).text(originalText);
        $(this).hide();
        self.currentRefCategory = null;
        self.updateModuleVisibility();
      }
    );
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

    $('body').on('click', '.module-addons-search-link', (event) => {
      event.preventDefault();
      event.stopPropagation();
      window.open($(this).attr('href'), '_blank');
    });
  }

  /**
   * Initialize display switching between List or Grid
   */
  initSortingDisplaySwitch() {
    const self = this;

    $('body').on(
      'click',
      '.module-sort-switch',
      function switchSort() {
        const switchTo = $(this).data('switch');
        const isAlreadyDisplayed = $(this).hasClass('active-display');
        if (typeof switchTo !== 'undefined' && isAlreadyDisplayed === false) {
          self.switchSortingDisplayTo(switchTo);
          self.currentDisplay = switchTo;
        }
      }
    );
  }

  switchSortingDisplayTo(switchTo) {
    if (switchTo !== this.DISPLAY_GRID && switchTo !== this.DISPLAY_LIST) {
      console.error(`Can't switch to undefined display property "${switchTo}"`);
      return;
    }

    $('.module-sort-switch').removeClass('module-sort-active');
    $(`#module-sort-${switchTo}`).addClass('module-sort-active');
    this.currentDisplay = switchTo;
    this.updateModuleVisibility();
  }

  initializeSeeMore() {
    const self = this;

    $(`${self.moduleShortList} ${self.seeMoreSelector}`).on('click', function seeMore() {
      self.currentCategoryDisplay[$(this).data('category')] = true;
      $(this).addClass('d-none');
      $(this).closest(self.moduleShortList).find(self.seeLessSelector).removeClass('d-none');
      self.updateModuleVisibility();
    });

    $(`${self.moduleShortList} ${self.seeLessSelector}`).on('click', function seeMore() {
      self.currentCategoryDisplay[$(this).data('category')] = false;
      $(this).addClass('d-none');
      $(this).closest(self.moduleShortList).find(self.seeMoreSelector).removeClass('d-none');
      self.updateModuleVisibility();
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
          $this.next('.modules-list').find('.module-item').length
        );
      });

      // If there is no shortlist: the wording directly update from the only module container.
    } else {
      const modulesCount = $('.modules-list').find('.module-item').length;
      replaceFirstWordBy($('.module-search-result-wording'), modulesCount);

      const selectorToToggle = (self.currentDisplay === self.DISPLAY_LIST) ?
                               this.addonItemListSelector :
                               this.addonItemGridSelector;
      $(selectorToToggle).toggle(modulesCount !== (this.modulesList.length / 2));

      if (modulesCount === 0) {
        $('.module-addons-search-link').attr(
          'href',
          `${this.baseAddonsUrl}search.php?search_query=${encodeURIComponent(this.currentTagsList.join(' '))}`
        );
      }
    }
  }
}

export default AdminModuleController;
