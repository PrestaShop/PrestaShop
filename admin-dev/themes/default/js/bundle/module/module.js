$(document).ready(function () {
  var controller = new AdminModuleController();
  controller.init();
});

/**
 * Module Admin Page Controller.
 * @constructor
 */
var AdminModuleController = function () {

  this.currentDisplay = '';
  this.isCategoryGridDisplayed = false;
  this.currentTagsList = [];
  this.currentRefCategory = null;
  this.currentRefStatus = null;
  this.currentSorting = null;
  this.areAllModuleDisplayed = true;
  this.baseAddonsUrl = 'https://addons.prestashop.com/';
  this.pstaggerInput = null;
  this.lastBulkAction = null;
  this.isUploadStarted = false;
  this.baseAdminDir = '';

  // Selectors into vars to make it easier to change them while keeping same code logic
  this.searchBarSelector = '#module-search-bar';
  this.sortDisplaySelector = '.module-sort-switch';
  this.moduleListSelector = '.modules-list';
  this.moduleGridSelector = '.modules-grid';
  this.moduleSortListSelector = '#module-sort-list';
  this.moduleSortGridSelector = '#module-sort-grid';
  this.moduleItemListSelector = '.module-item-list';
  this.moduleItemGridSelector = '.module-item-grid';
  this.categorySelectorLabelSelector = '.module-category-selector-label';
  this.categorySelector = '.module-category-selector';
  this.categoryItemSelector = '.module-category-menu';
  this.totalResultSelector = '.module-search-result-wording';
  this.addonsSearchSelector = '.module-addons-search';
  this.addonsSearchLinkSelector = '.module-addons-search-link';
  this.addonsLoginButtonSelector = '#addons_login_btn';
  this.categoryResetBtnSelector = '.module-category-reset';
  this.moduleInstallBtnSelector = 'input.module-install-btn';
  this.moduleSortingDropdownSelector = '.module-sorting-author select';
  this.categoryGridSelector = '#modules-categories-grid';
  this.categoryGridItemSelector = '.module-category-item';
  this.addonItemGridSelector = '.module-addons-item-grid';
  this.addonItemListSelector = '.module-addons-item-list';
  this.bulkActionDropDownSelector = '.module-bulk-actions select';
  this.checkedBulkActionListSelector = '.module-checkbox-bulk-list:checked';
  this.checkedBulkActionGridSelector = '.module-checkbox-bulk-grid:checked';
  this.bulkActionCheckboxGridSelector = '.module-checkbox-bulk-grid';
  this.bulkActionCheckboxListSelector = '.module-checkbox-bulk-list';
  this.bulkActionCheckboxSelector = '#module-modal-bulk-checkbox';
  this.selectAllBulkActionSelector = '.module-checkbox-bulk-select-all';
  this.bulkConfirmModalSelector = '#module-modal-bulk-confirm';
  this.bulkConfirmModalActionNameSelector = '#module-modal-bulk-confirm-action-name';
  this.bulkConfirmModalListSelector = '#module-modal-bulk-confirm-list';
  this.bulkConfirmModalAckBtnSelector = '#module-modal-confirm-bulk-ack';
  this.placeholderGlobalSelector = '.module-placeholders-wrapper';
  this.placeholderFailureGlobalSelector = '.module-placeholders-failure';
  this.placeholderFailureMsgSelector = '.module-placeholders-failure-msg';
  this.placeholderFailureRetryBtnSelector = '#module-placeholders-failure-retry';
  /* Module's statuses selectors */
  this.statusSelectorLabelSelector = '.module-status-selector-label';
  this.statusItemSelector = '.module-status-menu';
  this.statusResetBtnSelector = '.module-status-reset';

  /* Selectors for Module Import and Addons connect */
  this.dropModuleBtnSelector = '#page-header-desc-configuration-add_module';
  this.addonsConnectModalBtnSelector = '#page-header-desc-configuration-addons_connect';
  this.addonsLogoutModalBtnSelector = '#page-header-desc-configuration-addons_logout';
  this.dropZoneModalSelector = '#module-modal-import';
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

  /**
   * Initialize all listners and bind everything
   * @method init
   * @memberof AdminModule
   */
  this.init = function () {
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
  };

  this.initFilterStatusDropdown = function() {
    var self = this;
    var body = $('body');
    body.on('click', this.statusItemSelector, function () {
      // Get data from li DOM input
      self.currentRefStatus = $(this).attr('data-status-ref');
      var statusSelectedDisplayName = $(this).find('a:first').text();
      // Change dropdown label to set it to the current status' displayname
      $(self.statusSelectorLabelSelector).text(statusSelectedDisplayName);
      $(self.statusResetBtnSelector).show();
      // Do Search on categoryRef
      self.doSearch();
    });

    body.on('click', this.statusResetBtnSelector, function () {
      var text = $(this).find('a').text();
      $(self.statusSelectorLabelSelector).text(text);
      $(this).hide();
      self.currentRefStatus = null;
      self.doSearch();
    });
  };

  this.isModuleItemCategoryCompliant = function(moduleItem) {
    return moduleItem.attr('data-categories').toLowerCase() === this.currentRefCategory.toLowerCase();
  };

  this.isModuleItemStatusCompliant = function(moduleItem) {
    return parseInt(moduleItem.attr('data-active')) === parseInt(this.currentRefStatus);
  };

  this.isModuleItemTagsCompliant = function(moduleItem) {
    var dataName = moduleItem.attr('data-name').toLowerCase();
    var dataTechName = moduleItem.attr('data-tech-name').toLowerCase();
    var dataDescription = moduleItem.attr('data-description').toLowerCase();
    var dataAuthor = moduleItem.attr('data-author').toLowerCase();
    var matchedTagsCount = 0;

    $.each(this.currentTagsList, function(index, value) {
      // If match any on these attrbute  its a match
      value = value.toLowerCase();
      if (
          dataName.indexOf(value) != -1
          || dataDescription.indexOf(value) != -1
          || dataAuthor.indexOf(value) != -1
          || dataTechName.indexOf(value) != -1
      ) {
        matchedTagsCount += 1;
      }
    });

    return matchedTagsCount > 0;
  };

  this.doSearch = function() {
    // Pick the right selector to process search
    var moduleItemSelector = this.getModuleItemSelector();
    var moduleGlobalSelector = this.getModuleGlobalSelector();
    var self = this;

    $(moduleGlobalSelector).each(function () {
      var totalFoundModules = 0;
      // Go through each module items to check if its contains filters tags keywords...
      $(this).find(moduleItemSelector).each(function () {
        var isModuleToBeFound = true;
        if (self.currentRefCategory !== null) {
          isModuleToBeFound &= self.isModuleItemCategoryCompliant($(this));
        }
        if (self.currentRefStatus !== null) {
          isModuleToBeFound &= self.isModuleItemStatusCompliant($(this));
        }
        if (self.currentTagsList.length) {
          isModuleToBeFound &= self.isModuleItemTagsCompliant($(this));
        }
        if (isModuleToBeFound) {
          // If moduleItem is compliant with all filters, display it
          $(this).show();
          totalFoundModules += 1;
        } else {
          $(this).hide();
        }
      });

      // TODO: Redo current sorting if necessary
      if (self.currentSorting !== null) {
        self.doDropdownSort(self.currentSorting);
      }
      if(totalFoundModules != $(this).find(moduleItemSelector).length) {
        self.areAllModuleDisplayed = false;
      }

      self.updateTotalResults(totalFoundModules, $(this));
    });
  };

  this.doDropdownSort = function(typeSort) {
    var availableSorts = [
      'sort-by-price-asc',
      'sort-by-price-desc',
      'sort-by-name',
      'sort-by-scoring'
    ];

    if ($.inArray(typeSort, availableSorts) === -1) {
      console.error('typeSort "' + typeSort + '" is not a valid sort option');
      return false;
    }

    var dataAttr = null;
    var sortOrder = 'asc';
    var sortKind = 'alpha';
    var moduleGlobalSelector = this.getModuleGlobalSelector();
    var moduleItemSelector = this.getModuleItemSelector();
    var addonsItemSelector = this.getAddonItemSelector();
    var addonItemHtmlBackup = null;

    if ($(addonsItemSelector).length) {
      addonItemHtmlBackup = $(addonsItemSelector).get(0).outerHTML;
    }

    switch (typeSort) {
      case availableSorts[0]:
        dataAttr = ['data-price', 'data-tech-name'];
        sortKind = 'num';
        break;
      case availableSorts[1]:
        dataAttr = ['data-price', 'data-tech-name'];
        sortOrder = 'desc';
        sortKind = 'num';
        break;
      case availableSorts[2]:
        dataAttr = ['data-name', 'data-tech-name'];
        break;
      case availableSorts[3]:
        dataAttr = ['data-scoring', 'data-tech-name'];
        sortOrder = 'desc';
        sortKind = 'num';
        break;
    }

    $(moduleGlobalSelector).each(function() {

      var arrayToSort = {};
      var keysToSort = [];

      $(this).find(moduleItemSelector).each(function() {
        var selectorObject = $(this);
        var uniqueID = '';
        $.each(dataAttr, function (index, value) {
          if (uniqueID !== '') {
            uniqueID += ' #'; // Explode separator
          }
          uniqueID += selectorObject.attr(value);
        });
        arrayToSort[uniqueID] = $(this);
        keysToSort.push(uniqueID);
      });

      if (sortKind == 'alpha') {
        keysToSort.sort();
      } else {
        keysToSort.sort(function(elem1, elem2) {
          var elem1Formatted = parseFloat(elem1.substring(0, elem1.indexOf('#')));
          var elem2Formatted = parseFloat(elem2.substring(0, elem2.indexOf('#')));
          if (sortOrder == 'asc') {
            return elem1Formatted - elem2Formatted;
          } else {
            return elem2Formatted - elem1Formatted;
          }
        });
      }

      var currentSelector = $(this);

      currentSelector.empty();
      currentSelector.append('<div class="row">');

      $.each(keysToSort, function(index, value){
        currentSelector.find('.row').first().append(arrayToSort[value].get(0).outerHTML);
        delete arrayToSort[value];
      });

      currentSelector.find('.row').first().append(addonItemHtmlBackup);
      // Take care of Addons Search Card
      if ($(moduleItemSelector + ':visible').length != $(moduleItemSelector).length && addonItemHtmlBackup !== null) {
        $(addonsItemSelector).css('display', 'table');
      }

      currentSelector.append('</div>');
    });
  };

  this.updateTagList = function(tagList) {
    this.currentTagsList = tagList;
    // When this happen we need to update the interface accordingly
    this.doSearch();
  };

  this.resetSearch = function () {
    // Pick the right selector to process search
    var moduleItemSelector = this.getModuleItemSelector();
    var moduleGlobalSelector = this.getModuleGlobalSelector();
    var self = this;

    // Reset currentTagsList
    this.currentTagsList = [];
    self.doSearch();

    // Avoid trying to redisplay everything if it's already fully displayed
    if (this.areAllModuleDisplayed === false) {

      $(moduleGlobalSelector).each(function () {
        var totalModules = 0;
        var _that = self;
        $(this).find(moduleItemSelector).each(function () {
          if (_that.currentRefCategory !== null) {
            var isFromFilterCategory = ($(this).attr('data-categories') == _that.currentRefCategory);
            if (isFromFilterCategory === true) {
              totalModules += 1;
            }
            if ($(this).is(':hidden') && isFromFilterCategory === true) {
              $(this).show();
            }
          } else {
            totalModules += 1;
            if ($(this).is(':hidden')) {
              $(this).show();
            }
          }
        });

        // Dont forget this vital var once this done
        self.areAllModuleDisplayed = true;
        self.updateTotalResults(totalModules, $(this));
      });
    }
  };

  this.initBOEventRegistering = function() {
    BOEvent.on('Module Disabled', this.onModuleDisabled, this);
  };

  this.onModuleDisabled = function() {
    var globalModuleSelector = this.getModuleGlobalSelector();
    var moduleItemSelector = this.getModuleItemSelector();
    var self = this;

    $(globalModuleSelector).each(function() {
      var totalForCurrentSelector = $(this).find(moduleItemSelector+':visible').length;
      self.updateTotalResults(totalForCurrentSelector, $(this));
    });

  };

  this.initPlaceholderMechanism = function() {
    var self = this;

    if ($(this.placeholderGlobalSelector).length) {
      this.ajaxLoadPage();
    }

    // Retry loading mechanism
    $('body').on('click', this.placeholderFailureRetryBtnSelector, function() {
      $(self.placeholderFailureGlobalSelector).fadeOut();
      $(self.placeholderGlobalSelector).fadeIn();
      self.ajaxLoadPage();
    });
  };

  this.ajaxLoadPage = function() {
    var urlToCall = this.baseAdminDir + 'module/catalog/refresh';
    var self = this;

    $.ajax({
      method: 'GET',
      url: urlToCall
    }).done(function (response) {
      if (response.status === true) {
        if (typeof response.domElements === 'undefined') response.domElements = null;
        if (typeof response.msg === 'undefined') response.msg = null;

        var stylesheet = document.styleSheets[0];
        var stylesheetRule = '{display: none}';
        var moduleGlobalSelector = self.getModuleGlobalSelector();
        var requiredSelectorCombination = moduleGlobalSelector + ', .module-sorting-menu ';

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

        $(self.placeholderGlobalSelector).fadeOut(800, function() {
          $.each(response.domElements, function(index, element){
            $(element.selector).append(element.content);
          });
          $(requiredSelectorCombination).fadeIn(800);
          $('[data-toggle="popover"]').popover();
        });
      } else {
        $(self.placeholderGlobalSelector).fadeOut(800, function() {
          $(self.placeholderFailureMsgSelector).text(response.msg);
          $(self.placeholderFailureGlobalSelector).fadeIn(800);
        });
      }
    }).fail(function (response){
      var _that = self;

      $(self.placeholderGlobalSelector).fadeOut(800, function() {
        $(_that.placeholderFailureMsgSelector).text(response.statusText);
        $(_that.placeholderFailureGlobalSelector).fadeIn(800);
      });
    });
  };

  this.initPageChangeProtection = function() {
    var _this = this;

    $(window).on('beforeunload', function() {
      if (_this.isUploadStarted === true) {
        return "It seems some critical operation are running, are you sure you want to change page ? It might cause some unexepcted behaviors.";
      }
    });
  };

  this.initBulkActions = function() {
    var self = this;
    var body = $('body');

    body.on('change', this.bulkActionDropDownSelector, function() {
      self.lastBulkAction = $(this).find(':checked').attr('value');
      var modulesListString = self.buildBulkActionModuleList();
      var actionString = $(this).find(':checked').text().toLowerCase();
      $(self.bulkConfirmModalListSelector).html(modulesListString);
      $(self.bulkConfirmModalActionNameSelector).text(actionString);

      if (self.lastBulkAction !== 'bulk-uninstall') {
        $(self.bulkActionCheckboxSelector).hide();
      }
      $(self.bulkConfirmModalSelector).modal('show');
    });

    body.on('change', this.selectAllBulkActionSelector, function() {
      self.changeBulkCheckboxesState($(this).is(':checked'));
    });

    body.on('click', this.bulkConfirmModalAckBtnSelector, function(event) {
      event.preventDefault();
      event.stopPropagation();
      $(self.bulkConfirmModalSelector).modal('hide');
      self.doBulkAction(self.lastBulkAction);
    });
  };

  this.buildBulkActionModuleList = function() {
    var checkBoxesSelector = this.getBulkCheckboxesSelector();
    var moduleItemSelector = this.getModuleItemSelector();
    var alreadyDoneFlag = 0;
    var htmlGenerated = '';

    $(checkBoxesSelector + ':checked').each(function() {
      if (alreadyDoneFlag != 10) {
        var currentElement = $(this).parents(moduleItemSelector);
        htmlGenerated += '- ' + currentElement.attr('data-name') + '<br/>';
        alreadyDoneFlag += 1;
      } else {
        // Break each
        htmlGenerated += '- ...';
        return false;
      }
    });

    return htmlGenerated;
  };

  this.changeBulkCheckboxesState = function (hasToCheck) {
    var checkBoxesSelector = this.getBulkCheckboxesSelector();

    $(checkBoxesSelector).each(function () {
      $(this).prop('checked', hasToCheck);
    });
  };

  this.initAddonsConnect = function () {
    var _this = this;

    // Make addons connect modal ready to be clicked
    if ($(this.addonsConnectModalBtnSelector).attr('href') == '#') {
      $(this.addonsConnectModalBtnSelector).attr('data-toggle', 'modal');
      $(this.addonsConnectModalBtnSelector).attr('data-target', this.addonsConnectModalSelector);
    }
    if ($(this.addonsLogoutModalBtnSelector).attr('href') == '#') {
      $(this.addonsLogoutModalBtnSelector).attr('data-toggle', 'modal');
      $(this.addonsLogoutModalBtnSelector).attr('data-target', this.addonsLogoutModalSelector);
    }
    $('body').on('submit', this.addonsConnectForm, function (event) {
      event.preventDefault();
      event.stopPropagation();

      var _that = _this;

      $.ajax({
        method: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: $(this).serialize(),
        beforeSend: function() {
          $(_that.addonsLoginButtonSelector).show();
          $("button.btn[type='submit']", _that.addonsConnectForm).hide();
        }
      }).done(function (response) {
        var responseCode = response.success;
        var responseMsg = response.message;

        if (responseCode === 1) {
          location.reload();
        } else {
          $.growl.error({message: responseMsg});
          $(_that.addonsLoginButtonSelector).hide();
          $("button.btn[type='submit']", _that.addonsConnectForm).fadeIn();
        }
      });
    });
  };

  this.initAddModuleAction = function () {
    $(this.dropModuleBtnSelector).attr('data-toggle', 'modal');
    $(this.dropModuleBtnSelector).attr('data-target', this.dropZoneModalSelector);
  };

  this.initDropzone = function () {
    var self = this;
    var body = $('body');
    var dropzone = $('.dropzone');

    // Reset modal when click on Retry in case of failure
    body.on('click', this.moduleImportFailureRetrySelector, function() {
      $(self.moduleImportSuccessSelector + ', ' + self.moduleImportFailureSelector + ', ' + self.moduleImportProcessingSelector).fadeOut(function() {
        // Added timeout for a better render of animation and avoid to have displayed at the same time
        setTimeout(function() {
          $(self.moduleImportStartSelector).fadeIn(function() {
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
    });

    // Change the way Dropzone.js lib handle file input trigger
    body.on(
        'click', '.dropzone:not('+this.moduleImportSelectFileManualSelector+', '+this.moduleImportSuccessConfigureBtnSelector+')',
        function(event, manual_select) {
        // if click comes from .module-import-start-select-manual, stop everything
        if (typeof manual_select == "undefined") {
          event.stopPropagation();
          event.preventDefault();
        }
      }
    );

    body.on('click', this.moduleImportSelectFileManualSelector, function(event) {
      event.stopPropagation();
      event.preventDefault();
      // Trigger click on hidden file input, and pass extra data to .dropzone click handler fro it to notice it comes from here
      $('.dz-hidden-input').trigger('click', ["manual_select"]);
    });

    // Handle modal closure
    body.on('click', this.moduleImportModalCloseBtn, function() {
      if (self.isUploadStarted === true) {
        // TODO: Display tooltip saying you can't escape at this stage
      } else {
        $(self.dropZoneModalSelector).modal('hide');
      }
    });

    // Fix issue on click configure button
    body.on('click', this.moduleImportSuccessConfigureBtnSelector, function(event) {
      event.stopPropagation();
      event.preventDefault();
      window.location = $(this).attr('href');
    });

    // Open failure message details box
    body.on('click', this.moduleImportFailureDetailsBtnSelector, function() {
      $(self.moduleImportFailureMsgDetailsSelector).slideDown();
    });

    // @see: dropzone.js
    Dropzone.options.importDropzone = {
      url: 'import',
      acceptedFiles: '.zip, .tar',
      // The name that will be used to transfer the file
      paramName: 'file_uploaded',
      maxFilesize: 50, // can't be greater than 50Mb because it's an addons limitation
      uploadMultiple: false,
      addRemoveLinks: true,
      dictDefaultMessage: '',
      hiddenInputContainer: self.dropZoneImportZoneSelector,
      addedfile: function() {
        // State that we start module upload
        self.isUploadStarted = true;
        $(self.moduleImportStartSelector).hide(0);
        dropzone.css('border', 'none');
        $(self.moduleImportProcessingSelector).fadeIn();
      },
      processing: function () {
        // Leave it empty since we don't require anything while processing upload
      },
      error: function (file, message) {
        $(self.moduleImportProcessingSelector).finish().fadeOut(function() {
          $(self.moduleImportFailureMsgDetailsSelector).html(message);
          $(self.moduleImportFailureSelector).fadeIn();
        });
      },
      complete: function (file) {
        if (file.status !== 'error') {
          var responseObject = jQuery.parseJSON(file.xhr.response);
          if (typeof responseObject.is_configurable === 'undefined') responseObject.is_configurable = null;
          if (typeof responseObject.module_name === 'undefined') responseObject.module_name = null;

          $(self.moduleImportProcessingSelector).finish().fadeOut(function() {
            if (responseObject.status === true) {
              if (responseObject.is_configurable === true) {
                var configureLink = self.baseAdminDir + 'module/manage/action/configure/' + responseObject.module_name;
                $(self.moduleImportSuccessConfigureBtnSelector).attr('href', configureLink);
                $(self.moduleImportSuccessConfigureBtnSelector).show();
              }
              $(self.moduleImportSuccessSelector).fadeIn();
            } else {
              $(self.moduleImportFailureMsgDetailsSelector).html(responseObject.msg);
              $(self.moduleImportFailureSelector).fadeIn();
            }
          });
        }
        // State that we have finish the process to unlock some actions
        self.isUploadStarted = false;
      }
    };
  };

  this.getBulkCheckboxesSelector = function () {
    return this.currentDisplay == 'grid'
      ? this.bulkActionCheckboxGridSelector
      : this.bulkActionCheckboxListSelector;
  };

  this.loadVariables = function () {
    if ($(this.moduleListSelector).length) {
      this.currentDisplay = 'list';
    } else {
      this.currentDisplay = 'grid';
    }

    // If index.php found in the current URL, we need it also in the baseAdminDir
    //noinspection JSUnresolvedVariable
    this.baseAdminDir = baseAdminDir;
    if (window.location.href.indexOf('index.php') != -1) {
      this.baseAdminDir += 'index.php/';
    }
  };

  this.getModuleItemSelector = function () {
    return this.currentDisplay == 'grid'
      ? this.moduleItemGridSelector
      : this.moduleItemListSelector;
  };

  this.getModuleGlobalSelector = function () {
    return this.currentDisplay == 'grid'
      ? this.moduleGridSelector
      : this.moduleListSelector;
  };

  this.getAddonItemSelector = function () {
    return this.currentDisplay == 'grid'
      ? this.addonItemGridSelector
      : this.addonItemListSelector;
  };

  this.getBulkActionSelectedSelector = function () {
    return this.currentDisplay == 'grid'
      ? this.checkedBulkActionGridSelector
      : this.checkedBulkActionListSelector;
  };

  this.initAddonsSearch = function () {
    var self = this;
    $('body').on('click', this.addonItemGridSelector+', '+this.addonItemListSelector, function () {
      var searchQuery = '';
      if (self.currentTagsList.length) {
        searchQuery = encodeURIComponent(self.currentTagsList.join(' '));
      }
      var hrefUrl = self.baseAddonsUrl+'search.php?search_query='+searchQuery;
      window.open(hrefUrl, '_blank');
    });
  };

  this.initCategoriesGrid = function () {
    if (typeof refMenu === 'undefined') var refMenu = null;
    var self = this;

    $('body').on('click', this.categoryGridItemSelector, function (event) {
      event.stopPropagation();
      event.preventDefault();
      var refCategory = $(this).attr('data-category-ref');

      // In case we have some tags we need to reset it !
      if (self.currentTagsList.length) {
        self.pstaggerInput.resetTags(false);
        self.currentTagsList = [];
      }
      var menuCategoryToTrigger = $(self.categoryItemSelector+'[data-category-ref="' + refCategory + '"]');

      if (!menuCategoryToTrigger.length) {
        alert('No category with ref ('+refMenu+') seems to exists!');
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
  };

  this.initSortingDropdown = function () {
    var self = this;

    $('body').on('change', this.moduleSortingDropdownSelector, function() {
      var selectedSorting = $(this).find(':checked').attr('value');
      self.currentSorting = selectedSorting;
      self.doDropdownSort(selectedSorting);
    });
  };

  this.doBulkAction = function (requestedBulkAction) {
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

    // char is used only to be easy to replace by the end of this function
    var baseActionUrl = this.baseAdminDir + 'module/manage/action/@/';

    // Note no grid selector used yet since we do not needed it at dev time
    // Maybe useful to implement this kind of things later if intended to
    // use this functionality elsewhere but "manage my module" section
    if (typeof bulkActionToUrl[requestedBulkAction] === "undefined") {
      console.error('Request bulk action "'+requestedBulkAction+'" does not exist');
      return false;
    }

    // Loop over all checked bulk checkboxes
    var bulkActionSelectedSelector = this.getBulkActionSelectedSelector();

    if ($(bulkActionSelectedSelector).length > 0) {
      var bulkModulesTechNames = [];
      $(bulkActionSelectedSelector).each(function () {
        var moduleTechName = $(this).attr('data-tech-name');
        bulkModulesTechNames.push({
          techName: moduleTechName,
          actionMenuObj: $(this).parent().next()
        });
      });

      $.each(bulkModulesTechNames, function (index, data) {
        var actionMenuObj = data.actionMenuObj;
        var moduleTechName = data.techName;

        var urlActionSegment = bulkActionToUrl[requestedBulkAction];
        baseActionUrl.replace('@', urlActionSegment);

        if (typeof module_card_controller !== 'undefined') {
          // We use jQuery to get the specific link for this action. If found, we send it.
          var urlElement = $(module_card_controller.moduleActionMenuLinkSelector + urlActionSegment, actionMenuObj);

          if (urlElement.length > 0) {
            module_card_controller.requestToController(urlActionSegment, urlElement, forceDeletion);
          } else {
            $.growl.error({message: "Action " + urlActionSegment + " not available for module " + moduleTechName + ". Skipped."});
          }
        }
      });

    } else {
      console.warning('Request bulk action "' + requestedBulkAction + '" can\'t be performed if you don\'t select at least 1 module');
      return false;
    }
  };

  this.doDropdownSort = function(typeSort) {
    var availableSorts = [
      'sort-by-price-asc',
      'sort-by-price-desc',
      'sort-by-name',
      'sort-by-scoring',
      'sort-by-access-date'
    ];

    if ($.inArray(typeSort, availableSorts) === -1) {
      console.error('typeSort "' + typeSort + '" is not a valid sort option');
      return false;
    }

    var dataAttr = null;
    var sortOrder = 'asc';
    var sortKind = 'alpha';
    var moduleGlobalSelector = this.getModuleGlobalSelector();
    var moduleItemSelector = this.getModuleItemSelector();
    var addonsItemSelector = this.getAddonItemSelector();
    var addonItemHtmlBackup = null;

    if ($(addonsItemSelector).length) {
      addonItemHtmlBackup = $(addonsItemSelector).get(0).outerHTML;
    }

    switch (typeSort) {
      case availableSorts[0]:
        dataAttr = ['data-price', 'data-tech-name'];
        sortKind = 'num';
        break;
      case availableSorts[1]:
        dataAttr = ['data-price', 'data-tech-name'];
        sortOrder = 'desc';
        sortKind = 'num';
        break;
      case availableSorts[2]:
        dataAttr = ['data-name', 'data-tech-name'];
        break;
      case availableSorts[3]:
        dataAttr = ['data-scoring', 'data-tech-name'];
        sortOrder = 'desc';
        sortKind = 'num';
        break;
      case availableSorts[4]:
        dataAttr = ['data-access-date', 'data-tech-name'];
        sortKind = 'date';
        sortOrder = 'desc';
        break;
    }

    $(moduleGlobalSelector).each(function() {

      var arrayToSort = {};
      var keysToSort = [];

      $(this).find(moduleItemSelector).each(function() {
        var selectorObject = $(this);
        var uniqueID = '';
        $.each(dataAttr, function (index, value) {
          if (uniqueID !== '') {
            uniqueID += ' #'; // Explode separator
          }
          uniqueID += selectorObject.attr(value);
        });
        arrayToSort[uniqueID] = $(this);
        keysToSort.push(uniqueID);
      });

      if (sortKind == 'alpha') {
        keysToSort.sort();
      } else if (sortKind == 'num') {
        keysToSort.sort(function(elem1, elem2) {
          var elem1Formatted = parseFloat(elem1.substring(0, elem1.indexOf('#')));
          var elem2Formatted = parseFloat(elem2.substring(0, elem2.indexOf('#')));
          if (sortOrder == 'asc') {
            return elem1Formatted - elem2Formatted;
          } else {
            return elem2Formatted - elem1Formatted;
          }
        });
      } else if (sortKind == 'date') {
        keysToSort.sort(function(elem1, elem2) {
          var elem1Formatted = elem1.substring(0, elem1.indexOf('#'));
          var elem2Formatted = elem2.substring(0, elem2.indexOf('#'));
          if (sortOrder == 'asc') {
            if (elem1Formatted > elem2Formatted) {
              return 1;
            }
            if (elem1Formatted < elem2Formatted) {
              return -1;
            }
          } else {
            if (elem1Formatted > elem2Formatted) {
              return -1;
            }
            if (elem1Formatted < elem2Formatted) {
              return 1;
            }
          }
        });
      }

      var currentSelector = $(this);

      currentSelector.empty();
      currentSelector.append('<div class="row">');

      $.each(keysToSort, function(index, value){
        currentSelector.find('.row').first().append(arrayToSort[value].get(0).outerHTML);
        delete arrayToSort[value];
      });

      currentSelector.find('.row').first().append(addonItemHtmlBackup);
      // Take care of Addons Search Card
      if ($(moduleItemSelector + ':visible').length != $(moduleItemSelector).length && addonItemHtmlBackup !== null) {
        $(addonsItemSelector).css('display', 'table');
      }

      currentSelector.append('</div>');
    });
  };

  this.initActionButtons = function () {
    $('body').on('click', this.moduleInstallBtnSelector, function (event) {
      event.preventDefault();
      var next = $(this).next();
      $(this).hide();
      $(next).show();
      $.ajax({
        url: $(this).attr("data-url"),
        dataType: 'json'
      }).done(function () {
        $(next).fadeOut();
      });
    });
  };

  this.initCategorySelect = function () {
    var self = this;
    var body = $('body');
    body.on('click', this.categoryItemSelector, function () {
      // Get data from li DOM input
      self.currentRefCategory = $(this).attr('data-category-ref');
      var categorySelectedDisplayName = $(this).attr('data-category-display-name');
      // Change dropdown label to set it to the current category's displayname
      $(self.categorySelectorLabelSelector).text(categorySelectedDisplayName);
      $(self.categoryResetBtnSelector).show();
      // Do Search on categoryRef
      self.doSearch();
    });

    body.on('click', this.categoryResetBtnSelector, function () {
      var rawText = $(self.categorySelector).attr('aria-labelledby');
      var upperFirstLetter = rawText.charAt(0).toUpperCase();
      var removedFirstLetter = rawText.slice(1);
      var originalText = upperFirstLetter + removedFirstLetter;
      $(self.categorySelectorLabelSelector).text(originalText);
      $(this).hide();
      self.currentRefCategory = null;
      self.doSearch();
    });
  };

  this.updateTotalResults = function (totalResultFound, domObject) {
    // Pick the right selector to process search
    var addonsItemSelector = this.getAddonItemSelector();
    var resultWordingObject = domObject.prev().find(this.totalResultSelector);

    $(addonsItemSelector).hide();
    var str = resultWordingObject.text();
    var explodedStr = str.split(' ');
    explodedStr[0] = totalResultFound;
    var gluedStr = explodedStr.join(' ');
    resultWordingObject.text(gluedStr);

    if (totalResultFound === 0) {
      // Construct search query
      var searchQuery = encodeURIComponent(this.currentTagsList.join(' '));
      var hrefUrl = this.baseAddonsUrl + 'search.php?search_query=' + searchQuery;
      $(this.addonsSearchLinkSelector).attr('href', hrefUrl);
      $(this.addonsSearchSelector).show();
      // Display category grid
      if (this.isCategoryGridDisplayed === false) {
        $(this.categoryGridSelector).fadeIn();
        this.isCategoryGridDisplayed = true;
      }
      $(addonsItemSelector).hide();

    } else {
      if (this.isCategoryGridDisplayed === true) {
        $(this.categoryGridSelector).fadeOut();
        this.isCategoryGridDisplayed = false;
      }
      var moduleItemSelector = this.getModuleItemSelector();

      if (totalResultFound != $(moduleItemSelector).length) {
        $(addonsItemSelector).css('display', 'table');
      } else {
        $(addonsItemSelector).hide();
      }
    }
  };

  this.initSearchBlock = function() {
    var self = this;
    this.pstaggerInput = $(this.searchBarSelector).pstagger({
      onTagsChanged: self.updateTagList,
      onResetTags: self.resetSearch,
      inputPlaceholder: 'Search modules: keyword, name, author...',
      closingCross: true,
      context: self,
      clearAllBtn: true,
      clearAllIconClassAdditional: 'material-icons',
      clearAllSpanClassAdditional: 'module-tags-clear-btn ',
      tagInputClassAdditional: 'module-tags-input',
      tagClassAdditional: 'module-tag ',
      tagsWrapperClassAdditional: 'module-tags-labels'
    });

    $('body').on('click', this.addonsSearchLinkSelector, function(event) {
      event.preventDefault();
      event.stopPropagation();
      var href = $(this).attr('href');
      window.open(href, '_blank');
    });
  };

  /**
   * Initialize display switching between List or Grid
   * @method initSortingDisplaySwitch
   * @memberof AdminModule
   */
  this.initSortingDisplaySwitch = function() {
    var self = this;

    $('body').on('click', this.sortDisplaySelector, function() {
      var switchTo = $(this).attr('data-switch');
      var isAlreadyDisplayed = $(this).hasClass('active-display');
      if (typeof switchTo !== 'undefined' && isAlreadyDisplayed === false) {
        self.switchSortingDisplayTo(switchTo);
        self.currentDisplay = switchTo;
      }
    });
  };

  /**
   * Initialize display switching between List or Grid
   * @method switchSortingDisplayTo
   * @memberof AdminModule
   * @param {string} switchTo name of the display to switch to
   * @return {boolean}
   */
  this.switchSortingDisplayTo = function (switchTo) {
    var self = this;
    var addonsItemSelector = this.getAddonItemSelector();
    var gridListSelector = this.getModuleGlobalSelector();
    var addonItem = $(addonsItemSelector);

    if (switchTo == 'grid') {
      // Change main wrapper class to grid
      $(gridListSelector).addClass('modules-grid').removeClass('modules-list');
      $(this.moduleItemListSelector).each(function () {
        $(self.moduleSortListSelector).removeClass('module-sort-active');
        $(self.moduleSortGridSelector).addClass('module-sort-active');
        $(this).removeClass().addClass('module-item-grid col-12 col-xl-4 col-lg-6 col-md-12 col-sm-12');
        self.setNewDisplay($(this), '-list', '-grid');
      });
      // Change module addons item
      addonItem.removeClass().addClass('module-addons-item-grid col-12 col-xl-4 col-lg-6 col-md-12 col-sm-12');
      self.setNewDisplay(addonItem, '-list', '-grid');

    } else if (switchTo == 'list') {
      // Change main wrapper class to list
      $(gridListSelector).addClass('modules-list').removeClass('modules-grid');
      $(this.moduleItemGridSelector).each(function() {
        $(self.moduleSortGridSelector).removeClass('module-sort-active');
        $(self.moduleSortListSelector).addClass('module-sort-active');
        $(this).removeClass().addClass('module-item-list col-lg-12');
        self.setNewDisplay($(this), '-grid', '-list');
      });
      // Change module addons item
      addonItem.removeClass().addClass('module-addons-item-list col-lg-12');
      self.setNewDisplay(addonItem, '-grid', '-list');
    } else {
      console.error('Can\'t switch to undefined display property "' + switchTo + '"');
      return false;
    }

    return true;
  };

  /**
   * Initialize display switching between List or Grid
   * @method switchSortingDisplayTo
   * @memberof AdminModule
   * @param {object} domObj jQuery Dom Element
   * @param {string} toBeReplaced the string that has to be replaced
   * @param {string} replaceWith the string to replace toBeReplaced with
   */
  this.setNewDisplay = function (domObj, toBeReplaced, replaceWith) {
    var replaceRegex = new RegExp(toBeReplaced, 'g');
    var originalHTML = domObj.html();
    var alteredHTML = originalHTML.replace(replaceRegex, replaceWith);
    domObj.empty().html(alteredHTML);
  };
};
