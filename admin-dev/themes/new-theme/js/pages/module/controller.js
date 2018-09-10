/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

/**
 * Module Admin Page Controller.
 * @constructor
 */
class AdminModuleController {
  /**
   * Initialize all listners and bind everything
   * @method init
   * @memberof AdminModule
   */
  constructor() {
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

    /**
     * Loaded modules list.
     * Containing the card and list display.
     * @type {Array}
     */
    this.modulesList = [];
    this.addonsCardGrid = null;
    this.addonsCardList = null;

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
    this.bulkActionDropDownSelector = '.module-bulk-actions select';
    this.checkedBulkActionListSelector = '.module-checkbox-bulk-list input:checked';
    this.checkedBulkActionGridSelector = '.module-checkbox-bulk-grid input:checked';
    this.bulkActionCheckboxGridSelector = '.module-checkbox-bulk-grid';
    this.bulkActionCheckboxListSelector = '.module-checkbox-bulk-list';
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

    this.initBOEventRegistering();
    this.loadVariables();
    this.initSortingDisplaySwitch();
    this.initSortingDropdown();
    this.initSearchBlock();
    this.initCategorySelect();
    this.initCategoriesGrid();
    this.initActionButtons();
    this.initAddonsSearch();
    this.initAddonsConnect();
    this.initAddModuleAction();
    this.initDropzone();
    this.initPageChangeProtection();
    this.initBulkActions();
    this.initPlaceholderMechanism();
    this.initFilterStatusDropdown();
    this.fetchModulesList();
    this.getNotificationsCount();
  }

  initFilterStatusDropdown() {
    const self = this;
    const body = $('body');
    body.on('click', this.statusItemSelector, function () {
      // Get data from li DOM input
      self.currentRefStatus = parseInt($(this).attr('data-status-ref'), 10);
      // Change dropdown label to set it to the current status' displayname
      $(self.statusSelectorLabelSelector).text($(this).find('a:first').text());
      $(self.statusResetBtnSelector).show();
      // Do Search on categoryRef
      self.updateModuleVisibility();
    });

    body.on('click', this.statusResetBtnSelector, function () {
      $(self.statusSelectorLabelSelector).text($(this).find('a').text());
      $(this).hide();
      self.currentRefStatus = null;
      self.updateModuleVisibility();
    });
  }

  initBOEventRegistering() {
    BOEvent.on('Module Disabled', this.onModuleDisabled, this);
  }

  onModuleDisabled() {
    this.getModuleItemSelector();
    // Don't care nothing to do?
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
      url: moduleURLs.catalogRefresh,
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

        $(self.placeholderGlobalSelector).fadeOut(800, function () {
          $.each(response.domElements, function (index, element){
            $(element.selector).append(element.content);
          });
          $(moduleGlobalSelector).fadeIn(800).css('display','flex');
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

  fetchModulesList() {
    const self = this;
    let container;
    let $this;

    self.modulesList = [];
    $('.modules-list').each(function () {
      container = $(this);
      container.find('.module-item').each(function () {
        $this = $(this);
        self.modulesList.push({
          domObject: $this,
          id: $this.attr('data-id'),
          name: $this.attr('data-name').toLowerCase(),
          scoring: parseFloat($this.attr('data-scoring')),
          logo: $this.attr('data-logo'),
          author: $this.attr('data-author').toLowerCase(),
          version: $this.attr('data-version'),
          description: $this.attr('data-description').toLowerCase(),
          techName: $this.attr('data-tech-name').toLowerCase(),
          childCategories: $this.attr('data-child-categories'),
          categories: $this.attr('data-categories').toLowerCase(),
          type: $this.attr('data-type'),
          price: parseFloat($this.attr('data-price')),
          active: parseInt($this.attr('data-active')),
          access: $this.attr('data-last-access'),
          display: $this.hasClass('module-item-list') ? 'list' : 'grid',
          container: container,
        });
        $this.remove();
      });
    });

    self.addonsCardGrid = $(this.addonItemGridSelector);
    self.addonsCardList = $(this.addonItemListSelector);
    self.updateModuleVisibility();
    $('body').trigger('moduleCatalogLoaded');
  }

  updateModuleVisibility() {
    const self = this;

    if (self.currentSorting) {
      // Modules sorting
      let order = 'asc';
      let key = self.currentSorting;
      if (key.split('-').length > 1) {
        key = key.split('-')[0];
      }

      if (self.currentSorting.indexOf('-desc') !== -1) {
        order = 'desc';
      }

      const currentCompare = (a, b) => {
        if (a[key] < b[key]) return -1;
        if (a[key] > b[key]) return 1;
        return 0;
      };

      self.modulesList.sort(currentCompare);
      if (order === 'desc') {
        self.modulesList.reverse();
      }
    }

    $('.modules-list').html('');

    // Modules visibility management
    let isVisible;
    let currentModule;
    let tagExists;
    let newValue;

    for (let i = 0; i < self.modulesList.length; i++) {
      currentModule = self.modulesList[i];
      if (currentModule.display === self.currentDisplay) {
        isVisible = true;
        if (self.currentRefCategory !== null) {
          isVisible &= currentModule.categories === self.currentRefCategory;
        }

        if (self.currentRefStatus !== null) {
          isVisible &= currentModule.active === self.currentRefStatus;
        }

        if (self.currentTagsList.length) {
          tagExists = false;
          $.each(self.currentTagsList, function (index, value) {
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

        if (isVisible) {
          currentModule.container.append(currentModule.domObject);
        }
      }
    }

    $('.module-short-list').each(function () {
      if ((self.currentRefCategory || self.currentRefStatus)
          && $(this).find('.module-item').length === 0
      ) {
        $(this).hide();
        return;
      }

      $(this).show();
    });

    if (self.currentTagsList.length) {
      $('.modules-list').append(this.currentDisplay === 'grid' ? this.addonsCardGrid : this.addonsCardList);
    }
  }

  initPageChangeProtection() {
    const self = this;

    $(window).on('beforeunload', () => {
      if (self.isUploadStarted === true) {
        return 'It seems some critical operation are running, are you sure you want to change page ? It might cause some unexepcted behaviors.';
      }

      return false;
    });
  }

  initBulkActions() {
    const self = this;
    const body = $('body');

    body.on('change', self.bulkActionDropDownSelector, function () {
      if (0 === $(self.getBulkCheckboxesCheckedSelector()).length) {
        $.growl.warning({message: translate_javascripts['Bulk Action - One module minimum']});
        return;
      }

      self.lastBulkAction = $(this).find(':checked').attr('value');
      const modulesListString = self.buildBulkActionModuleList();
      const actionString = $(this).find(':checked').text().toLowerCase();
      $(self.bulkConfirmModalListSelector).html(modulesListString);
      $(self.bulkConfirmModalActionNameSelector).text(actionString);

      if (self.lastBulkAction !== 'bulk-uninstall') {
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

  buildBulkActionModuleList() {
    const checkBoxesSelector = this.getBulkCheckboxesCheckedSelector();
    const moduleItemSelector = this.getModuleItemSelector();
    let alreadyDoneFlag = 0;
    let htmlGenerated = '';
    let currentElement;

    $(checkBoxesSelector).each(function () {
      if (alreadyDoneFlag !== 10) {
        currentElement = $(this).parents(moduleItemSelector);
        htmlGenerated += `- ${currentElement.attr('data-name')}<br/>`;
        alreadyDoneFlag += 1;
      } else {
        // Break each
        htmlGenerated += '- ...';
        return false;
      }
    });

    return htmlGenerated;
  }

  initAddonsConnect() {
    const self = this;

    // Make addons connect modal ready to be clicked
    if ($(this.addonsConnectModalBtnSelector).attr('href') === '#') {
      $(this.addonsConnectModalBtnSelector).attr('data-toggle', 'modal');
      $(this.addonsConnectModalBtnSelector).attr('data-target', this.addonsConnectModalSelector);
    }

    if ($(this.addonsLogoutModalBtnSelector).attr('href') === '#') {
      $(this.addonsLogoutModalBtnSelector).attr('data-toggle', 'modal');
      $(this.addonsLogoutModalBtnSelector).attr('data-target', this.addonsLogoutModalSelector);
    }

    $('body').on('submit', this.addonsConnectForm, function (event) {
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
        $(`${self.moduleImportSuccessSelector},${self.moduleImportFailureSelector},${self.moduleImportProcessingSelector}`).fadeOut(function () {
          // Added timeout for a better render of animation and avoid to have displayed at the same time
          setTimeout(function () {
            $(self.moduleImportStartSelector).fadeIn(function () {
              $(self.moduleImportFailureMsgDetailsSelector).hide();
              $(self.moduleImportSuccessConfigureBtnSelector).hide();
              dropzone.removeAttr('style');
            });
          }, 550);
        });
      }
    );

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
    body.on(
      'click', '.dropzone:not('+this.moduleImportSelectFileManualSelector+', '+this.moduleImportSuccessConfigureBtnSelector+')',
      function (event, manual_select) {
        // if click comes from .module-import-start-select-manual, stop everything
        if (typeof manual_select === 'undefined') {
          event.stopPropagation();
          event.preventDefault();
        }
      }
    );

    body.on('click', this.moduleImportSelectFileManualSelector, function (event) {
      event.stopPropagation();
      event.preventDefault();
      // Trigger click on hidden file input, and pass extra data to .dropzone click handler fro it to notice it comes from here
      $('.dz-hidden-input').trigger('click', ['manual_select']);
    });

    // Handle modal closure
    body.on('click', this.moduleImportModalCloseBtn, function () {
      if (self.isUploadStarted !== true) {
        $(self.dropZoneModalSelector).modal('hide');
      }
    });

    // Fix issue on click configure button
    body.on('click', this.moduleImportSuccessConfigureBtnSelector, function (event) {
      event.stopPropagation();
      event.preventDefault();
      window.location = $(this).attr('href');
    });

    // Open failure message details box
    body.on('click', this.moduleImportFailureDetailsBtnSelector, function () {
      $(self.moduleImportFailureMsgDetailsSelector).slideDown();
    });

    // @see: dropzone.js
    let dropzoneOptions = {
      url: moduleURLs.moduleImport,
      acceptedFiles: '.zip, .tar',
      // The name that will be used to transfer the file
      paramName: 'file_uploaded',
      maxFilesize: 50, // can't be greater than 50Mb because it's an addons limitation
      uploadMultiple: false,
      addRemoveLinks: true,
      dictDefaultMessage: '',
      hiddenInputContainer: self.dropZoneImportZoneSelector,
      timeout:0, // add unlimited timeout. Otherwise dropzone timeout is 30 seconds and if a module is long to install, it is not possible to install the module.
      addedfile: function () {
        self.animateStartUpload();
      },
      processing: function () {
        // Leave it empty since we don't require anything while processing upload
      },
      error: function (file, message) {
        self.displayOnUploadError(message);
      },
      complete: function (file) {
        if (file.status !== 'error') {
          let responseObject = jQuery.parseJSON(file.xhr.response);
          if (typeof responseObject.is_configurable === 'undefined') responseObject.is_configurable = null;
          if (typeof responseObject.module_name === 'undefined') responseObject.module_name = null;

          self.displayOnUploadDone(responseObject);
        }
        // State that we have finish the process to unlock some actions
        self.isUploadStarted = false;
      }
    }
    dropzone.dropzone($.extend(dropzoneOptions));

    this.animateStartUpload = function () {
      // State that we start module upload
      self.isUploadStarted = true;
      $(self.moduleImportStartSelector).hide(0);
      dropzone.css('border', 'none');
      $(self.moduleImportProcessingSelector).fadeIn();
    }

    this.animateEndUpload = function (callback) {
      $(self.moduleImportProcessingSelector).finish().fadeOut(callback);
    }

    /**
     * Method to call for upload modal, when the ajax call went well.
     *
     * @param object result containing the server response
     */
    this.displayOnUploadDone = function (result) {
      const self = this;
      self.animateEndUpload(function () {
        if (result.status === true) {
          if (result.is_configurable === true) {
            let configureLink = moduleURLs.configurationPage.replace('1', result.module_name);
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
    this.displayOnUploadError = function (message) {
      self.animateEndUpload(function () {
        $(self.moduleImportFailureMsgDetailsSelector).html(message);
        $(self.moduleImportFailureSelector).fadeIn();
      });
    }

    /**
     * If PrestaTrust needs to be confirmed, we ask for the confirmation modal content and we display it in the
     * currently displayed one. We also generate the ajax call to trigger once we confirm we want to install
     * the module.
     *
     * @param Previous server response result
     */
    this.displayPrestaTrustStep = function (result) {
      const self = this;
      let modal = module_card_controller.replacePrestaTrustPlaceholders(result);
      let moduleName = result.module.attributes.name;
      $(this.moduleImportConfirmSelector).html(modal.find('.modal-body').html()).fadeIn();
      $(this.dropZoneModalFooterSelector).html(modal.find('.modal-footer').html()).fadeIn();
      $(this.dropZoneModalFooterSelector).find('.pstrust-install').off('click').on('click', function () {

        $(self.moduleImportConfirmSelector).hide();
        $(self.dropZoneModalFooterSelector).html('');
        self.animateStartUpload();

        // Install ajax call
        $.post(result.module.attributes.urls.install, { 'actionParams[confirmPrestaTrust]': '1'})
         .done(function (data) {
           self.displayOnUploadDone(data[moduleName]);
         })
         .fail(function (data) {
           self.displayOnUploadError(data[moduleName]);
         })
         .always(function () {
           self.isUploadStarted = false;
         });
      });
    }
  }

  getBulkCheckboxesSelector() {
    return this.currentDisplay === 'grid'
         ? this.bulkActionCheckboxGridSelector
         : this.bulkActionCheckboxListSelector;
  }


  getBulkCheckboxesCheckedSelector() {
    return this.currentDisplay === 'grid'
         ? this.checkedBulkActionGridSelector
         : this.checkedBulkActionListSelector;
  }

  loadVariables() {
    this.initCurrentDisplay();
  }

  getModuleItemSelector() {
    return this.currentDisplay === 'grid'
         ? this.moduleItemGridSelector
         : this.moduleItemListSelector;
  }

  /**
   * Get the module notifications count and displays it as a badge on the notification tab
   * @return void
   */
  getNotificationsCount() {
    let urlToCall = moduleURLs.notificationsCount;

    $.getJSON(
      urlToCall,
      this.updateNotificationsCount
    ).fail(function () {
      console.error('Could not retrieve module notifications count.');
    });
  }

  updateNotificationsCount(badge) {
    let destinationTabs = {
      'to_configure': $('#subtab-AdminModulesNotifications'),
      'to_update': $('#subtab-AdminModulesUpdates'),
    }

    for (let key in destinationTabs) {
      if (destinationTabs[key].length === 0) {
        continue;
      }
      destinationTabs[key].find('.notification-counter').text(badge[key]);
    }
  }

  initAddonsSearch() {
    const self = this;
    $('body').on('click', this.addonItemGridSelector+', '+this.addonItemListSelector, function () {
      let searchQuery = '';
      if (self.currentTagsList.length) {
        searchQuery = encodeURIComponent(self.currentTagsList.join(' '));
      }
      let hrefUrl = self.baseAddonsUrl+'search.php?search_query='+searchQuery;
      window.open(hrefUrl, '_blank');
    });
  }

  initCategoriesGrid() {
    if (typeof refMenu === 'undefined') {
      let refMenu = null;
    }

    const self = this;

    $('body').on('click', this.categoryGridItemSelector, function (event) {
      event.stopPropagation();
      event.preventDefault();
      let refCategory = $(this).attr('data-category-ref');

      // In case we have some tags we need to reset it !
      if (self.currentTagsList.length) {
        self.pstaggerInput.resetTags(false);
        self.currentTagsList = [];
      }
      let menuCategoryToTrigger = $(`${self.categoryItemSelector}[data-category-ref="${refCategory}"]`);

      if (!menuCategoryToTrigger.length) {
        console.warn(`No category with ref (${refMenu}) seems to exist!`);
        return false;
      }

      // Hide current category grid
      if (self.isCategoryGridDisplayed === true) {
        $(self.categoryGridSelector).fadeOut();
        self.isCategoryGridDisplayed = false;
      }

      // Trigger click on right category
      $(self.categoryItemSelector+'[data-category-ref="'+refCategory+'"]').click();
    });
  }

  initCurrentDisplay() {
    if (this.currentDisplay === '') {
      this.currentDisplay = 'list';
    } else {
      this.currentDisplay = 'grid';
    }
  }

  initSortingDropdown() {
    const self = this;

    self.currentSorting = $(this.moduleSortingDropdownSelector).find(':checked').attr('value');

    $('body').on('change', this.moduleSortingDropdownSelector, function () {
      self.currentSorting = $(this).find(':checked').attr('value');
      self.updateModuleVisibility();
    });
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
    if (typeof bulkActionToUrl[requestedBulkAction] === "undefined") {
      $.growl.error({message: translate_javascripts['Bulk Action - Request not found'].replace('[1]', requestedBulkAction)});
      return false;
    }

    // Loop over all checked bulk checkboxes
    const bulkActionSelectedSelector = this.getBulkCheckboxesCheckedSelector();

    if ($(bulkActionSelectedSelector).length <= 0) {
      console.warn(translate_javascripts['Bulk Action - One module minimum']);
      return false;
    }

    const bulkModulesTechNames = [];
    let moduleTechName;
    $(bulkActionSelectedSelector).each(function () {
      moduleTechName = $(this).attr('data-tech-name');
      bulkModulesTechNames.push({
        techName: moduleTechName,
        actionMenuObj: $(this).parent().next()
      });
    });

    let actionMenuObj;
    let urlActionSegment;
    let urlElement;
    $.each(bulkModulesTechNames, function (index, data) {
      actionMenuObj = data.actionMenuObj;
      moduleTechName = data.techName;

      urlActionSegment = bulkActionToUrl[requestedBulkAction];

      if (typeof module_card_controller !== 'undefined') {
        // We use jQuery to get the specific link for this action. If found, we send it.
        urlElement = $(module_card_controller.moduleActionMenuLinkSelector + urlActionSegment, actionMenuObj);

        if (urlElement.length > 0) {
          module_card_controller.requestToController(urlActionSegment, urlElement, forceDeletion);
        } else {
          $.growl.error({message: translate_javascripts["Bulk Action - Request not available for module"]
            .replace('[1]', urlActionSegment)
            .replace('[2]', moduleTechName)});
        }
      }
    });

    return true;
  }

  initActionButtons() {
    const self = this;
    $('body').on('click', self.moduleInstallBtnSelector, function (event) {
      const $this = $(this);
      const $next = $($this.next());
      event.preventDefault();

      $this.hide();
      $next.show();

      $.ajax({
        url: $this.attr('data-url'),
        dataType: 'json',
      }).done(() => {
        $next.fadeOut();
      });
    });

    // "Upgrade All" button handler
    $('body').on('click', self.upgradeAllSource, function (event) {
      event.preventDefault();
      $(self.upgradeAllTargets).click();
    });
  }

  initCategorySelect() {
    const self = this;
    const body = $('body');
    body.on('click', this.categoryItemSelector, function () {
      // Get data from li DOM input
      self.currentRefCategory = $(this).attr('data-category-ref');
      self.currentRefCategory = self.currentRefCategory ? self.currentRefCategory.toLowerCase() : null;
      let categorySelectedDisplayName = $(this).attr('data-category-display-name');
      // Change dropdown label to set it to the current category's displayname
      $(self.categorySelectorLabelSelector).text(categorySelectedDisplayName);
      $(self.categoryResetBtnSelector).show();
      // Do Search on categoryRef
      self.updateModuleVisibility();
    });

    body.on('click', this.categoryResetBtnSelector, function () {
      let rawText = $(self.categorySelector).attr('aria-labelledby');
      let upperFirstLetter = rawText.charAt(0).toUpperCase();
      let removedFirstLetter = rawText.slice(1);
      let originalText = upperFirstLetter + removedFirstLetter;
      $(self.categorySelectorLabelSelector).text(originalText);
      $(this).hide();
      self.currentRefCategory = null;
      self.updateModuleVisibility();
    });
  }

  initSearchBlock() {
    const self = this;
    this.pstaggerInput = $('#module-search-bar').pstagger({
      onTagsChanged: function (tagList) {
        self.currentTagsList = tagList;
        self.updateModuleVisibility();
      },
      onResetTags: function () {
        self.currentTagsList = [];
        self.updateModuleVisibility();
      },
      inputPlaceholder: translate_javascripts['Search - placeholder'],
      closingCross: true,
      context: self,
    });

    $('body').on('click', '.module-addons-search-link', function (event) {
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

    $('body').on('click', '.module-sort-switch', function () {
      const switchTo = $(this).attr('data-switch');
      const isAlreadyDisplayed = $(this).hasClass('active-display');
      if (typeof switchTo !== 'undefined' && isAlreadyDisplayed === false) {
        self.switchSortingDisplayTo(switchTo);
        self.currentDisplay = switchTo;
      }
    });
  }

  switchSortingDisplayTo(switchTo) {
    if (switchTo === 'grid' || switchTo === 'list') {
      $('.module-sort-switch').removeClass('module-sort-active');
      $(`#module-sort-${switchTo}`).addClass('module-sort-active');
      this.currentDisplay = switchTo;
      this.updateModuleVisibility();
    } else {
      console.error(`Can't switch to undefined display property "${switchTo}"`);
    }
  }
}

export default AdminModuleController;
