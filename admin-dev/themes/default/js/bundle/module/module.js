$(document).ready(function () {

    var controller = new AdminModule();
    controller.init();

});

/**
 * AdminModule Page Controller.
 * @constructor
 */
var AdminModule = function () {

    /* Global configuration */
    this.keywordsSplitCharacter = ' ';
    this.currentDisplay = '';
    this.isCategoryGridDisplayed = false;
    this.currentTagsList = [];
    this.currentRefMenu = null;
    this.tagSearchBlock = null;
    this.areAllModuleDisplayed = true;
    this.baseAddonsUrl = 'https://addons.prestashop.com/';
    this.pstaggerInput = null;
    this.lastBulkAction = null;

    /* Selectors into vars to make it easier to change them while keeping same code logic */
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
    this.moduleInstallLoaderSelector = '.module-install-loader';
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
    this.selectAllBulkActionSelector = '.module-checkbox-bulk-select-all';
    this.bulkConfirmModalSelector = '#module-modal-bulk-confirm';
    this.bulkConfirmModalActionNameSelector = '#module-modal-bulk-confirm-action-name';
    this.bulkConfirmModalListSelector = '#module-modal-bulk-confirm-list';
    this.bulkConfirmModalAckBtnSelector = '#module-modal-confirm-bulk-ack';

    /* Selectors for Module Import and Addons connect */
    this.dropModuleBtnSelector = '#page-header-desc-configuration-add_module';
    this.addonsConnectModalBtnSelector = '#page-header-desc-configuration-addons_connect';
    this.dropZoneModalSelector = '#module-modal-import';
    this.addonsConnectModalSelector = '#module-modal-addons-connect';
    this.addonsConnectForm = '#addons-connect-form';

    /**
     * Initialize all listners and bind everything
     * @method init
     * @memberof AdminModule
     */
    this.init = function () {
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
        this.initBulkActions();
    };

  //@TODO: JS Doc
  this.initBulkActions = function() {
      var _this = this;

      $(this.bulkActionDropDownSelector).on('change', function(event){
          _this.lastBulkAction = $(this).find(':checked').attr('value');
          var modulesListString = _this.buildBulkActionModuleList();
          var actionString = $(this).find(':checked').text().toLowerCase();
          $(_this.bulkConfirmModalListSelector).html(modulesListString);
          $(_this.bulkConfirmModalActionNameSelector).text(actionString);
          $(_this.bulkConfirmModalSelector).modal('show');
      });

      $(this.selectAllBulkActionSelector).on('change', function(event){
          _this.changeBulkCheckboxesState($(this).is(':checked'));
      });

      $(this.bulkConfirmModalAckBtnSelector).on('click', function(event) {
          event.preventDefault();
          event.stopPropagation();
          $(_this.bulkConfirmModalSelector).modal('hide');
          _this.doBulkAction(_this.lastBulkAction);
      });
  };

  this.buildBulkActionModuleList = function() {
      var checkBoxesSelector = this.getBulkCheckboxesSelector();
      var moduleItemSelector = this.getModuleItemSelector();
      var _this = this;
      var alreadyDoneFlag = 0;
      var htmlGenerated = '';


      $(checkBoxesSelector + ':checked').each(function(index, value){
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

    // @TODO: JS Doc
    this.changeBulkCheckboxesState = function (hasToCheck) {
        var checkBoxesSelector = this.getBulkCheckboxesSelector();

        $(checkBoxesSelector).each(function () {
            $(this).prop('checked', hasToCheck);
        });
    };

    //@TODO: JS Doc
    this.initAddonsConnect = function () {
        $(this.dropModuleBtnSelector).attr('data-toggle', 'modal');
        $(this.dropModuleBtnSelector).attr('data-target', this.dropZoneModalSelector);

        var _this = this;

        $(this.addonsConnectForm).on('submit', function (event) {
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

    //@TODO: JS Doc
    this.initAddModuleAction = function () {
        if ($(this.addonsConnectModalBtnSelector).attr('href') == '#') {
            $(this.addonsConnectModalBtnSelector).attr('data-toggle', 'modal');
            $(this.addonsConnectModalBtnSelector).attr('data-target', this.addonsConnectModalSelector);
        }
    };

  //@TODO: JS Doc
  this.initDropzone = function () {

      $('.module-import-failure-retry').on('click', function(event){
           $('.module-import-success, .module-import-failure, .module-import-processing').fadeOut(function(){
               $('.dz-preview, .dz-message').fadeIn();
           });
      });

      $('#module-modal-import').on('hidden.bs.modal', function (event) {
          $('.module-import-success').css('display', 'none');
          $('.module-import-failure').css('display', 'none');
          $('.module-import-zipname').parent().css('display', 'none');
      });

      Dropzone.options.importDropzone = {
          url: 'import',
          acceptedFiles: '.zip, .tar',
           // The name that will be used to transfer the file
          paramName: 'file_uploaded',
          maxFilesize: 5, // MB
          uploadMultiple: false,
          addRemoveLinks: true,
          dictDefaultMessage: 'Drop a module\'s zip archive here in order to install it',
          addedfile: function(file) {
              $('.dz-preview, .dz-message').fadeOut(function() {
                  // Display our text
                  $('.module-import-zipname').text('Processing: ' + file.name).parent().fadeIn();
                  // Display Loader
                  $('.module-import-loader, .install-message').fadeIn();
              });
          },
          processing: function (file, response) {
              // Leave it empty ATM since we don't require anything while processing upload
          },
          complete: function (file, response) {
              var responseObject = jQuery.parseJSON(file.xhr.response);

              $('.module-import-zipname').parent().fadeOut(function() {
                  if (responseObject.status === true) {
                      $('.module-import-name').first().text(responseObject.msg);
                      var configureLink = baseAdminDir + '/module/manage/action/configure/' + responseObject.module_name;
                      $('.module-import-success-configure').attr('href', configureLink);
                      $('.module-import-success').css('display', 'block');
                  } else {
                      $('.module-import-error-msg').html(responseObject.msg);
                      $('.module-import-failure').css('display', 'block');
                  }
              });
          }
      };
  };

    //@TODO: JS Doc
    this.getBulkCheckboxesSelector = function () {
        return (
                this.currentDisplay == 'grid' ?
                this.bulkActionCheckboxGridSelector :
                this.bulkActionCheckboxListSelector
                );
    };

    //@TODO: JS Doc
    this.loadVariables = function () {
        if ($(this.moduleListSelector).length) {
            this.currentDisplay = 'list';
        } else {
            this.currentDisplay = 'grid';
        }
    };

    //@TODO: JS Doc
    this.getModuleItemSelector = function () {
        return (
                this.currentDisplay == 'grid' ?
                this.moduleItemGridSelector :
                this.moduleItemListSelector
                );
    };

    //@TODO: JS Doc
    this.getModuleGlobalSelector = function () {
        return (
                this.currentDisplay == 'grid' ?
                this.moduleGridSelector :
                this.moduleListSelector
                );
    };

    //@TODO: JS Doc
    this.getAddonItemSelector = function () {
        return (
                this.currentDisplay == 'grid' ?
                this.addonItemGridSelector :
                this.addonItemListSelector
                );
    };

    //@TODO: JS Doc
    this.getBulkActionSelectedSelector = function () {
        return (
                this.currentDisplay == 'grid' ?
                this.checkedBulkActionGridSelector :
                this.checkedBulkActionListSelector
                );
    };

    //@TODO: JS Doc
    this.initAddonsSearch = function () {
        var _this = this;
        $(this.addonItemGridSelector + ', ' + this.addonItemListSelector).on('click', function (event) {
            var searchQuery = '';
            if (_this.currentTagsList.length) {
                searchQuery = encodeURIComponent(_this.currentTagsList.join(' '));
            }
            var hrefUrl = _this.baseAddonsUrl + 'search.php?search_query=' + searchQuery;
            window.open(hrefUrl, '_blank');
        });
    };

    this.initCategoriesGrid = function () {
        var _this = this;

        $(this.categoryGridItemSelector).on('click', function (event) {
            event.stopPropagation();
            event.preventDefault();
            var refCategory = $(this).attr('data-category-ref');

            // In case we have some tags we need to reset it !
            if (_this.currentTagsList.length) {
                _this.pstaggerInput.resetTags(false);
                _this.currentTagsList = [];
            }
            var menuCategoryToTrigger = $(_this.categoryItemSelector + '[data-category-ref="' + refCategory + '"]');

            if (!menuCategoryToTrigger.length) {
                alert('No category with ref (' + refMenu + ') seems to exists!');
                return false;
            }

            // Hide current category grid
            if (_this.isCategoryGridDisplayed === true) {
                $(_this.categoryGridSelector).fadeOut();
                _this.isCategoryGridDisplayed = false;
            }
            // Trigger click on right category
            $(_this.categoryItemSelector + '[data-category-ref="' + refCategory + '"]').click();

        });
    };

    this.initSortingDropdown = function () {
        var _this = this;

        $(this.moduleSortingDropdownSelector).on('change', function(event){
            var selectedSorting = $(this).find(':checked').attr('value');
            _this.doDropdownSort(selectedSorting);
        });
    };

    //@TODO: JS Doc
    this.doBulkAction = function (requestedBulkAction) {
        // @NOTE:
        // This object is used to check if reequested bulkAction is available and give proper
        // url segement to be called for it
        var bulkActionToUrl = {
            'bulk-uninstall': 'uninstall',
            'bulk-disable': 'disable',
            'bulk-enable': 'enable',
            'bulk-disable-mobile': 'disable-mobile',
            'bulk-enable-mobile': 'enable-mobile',
            'bulk-reset': 'reset'
        };

        //@NOTE:
        // "@" char is used only to be easy to replace by the end of this function ;)
        var baseActionUrl = baseAdminDir + 'module/manage/action/@/';

        //@NOTE:
        // Note no grid selector used yet since we do not needed it at dev time
        // Maybe usefull to implement this kind of things later if intended to
        // use this functionnality elsewhere but "manage my module" section

        if (typeof bulkActionToUrl[requestedBulkAction] == "undefined") {
            console.error('Request bulk action "' + requestedBulkAction + '" does not exist');
            return false;
        }

        // Loop over all checked bulk checkboxes
        var bulkActionSelectedSelector = this.getBulkActionSelectedSelector();

        if ($(bulkActionSelectedSelector).length > 0) {
            var bulkModulesTechNames = [];
            $(bulkActionSelectedSelector).each(function (index, value) {
                var moduleTechName = $(this).attr('data-tech-name');
                bulkModulesTechNames.push({
                    techName: moduleTechName,
                    actionMenuObj: $(this).parent().next()
                });
            });

            $.each(bulkModulesTechNames, function (index, data) {
                var actionMenuObj = data.actionMenuObj;
                var moduleTechName = data.techName;

                //actionMenuObj.fadeOut();

                var urlActionSegment = bulkActionToUrl[requestedBulkAction];
                var actionUrlBase = baseActionUrl.replace('@', urlActionSegment);

                if (typeof module_card_controller !== undefined) {
                    // We use jQuery to get the specific linbk for this action. If found, we send it.
                    var urlElement = $(module_card_controller.moduleActionMenuLinkSelector + urlActionSegment, actionMenuObj);

                    if (urlElement.length > 0) {
                        module_card_controller.requestToController(urlActionSegment, urlElement);
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
                                'sort-by-scoring'
                            ];

        if ($.inArray(typeSort, availableSorts) === -1) {
            console.error('typeSort "' + typeSort + '" is not a valid sort option');
            return false;
        }

        var dataAttr = null;
        var sortOrder = 'asc';
        var sortKind = 'alpha';
        var _this = this;
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
                sortKind = 'num';
                break;
        }

        $(moduleGlobalSelector).each(function(index, value) {

            var arrayToSort = {};
            var keysToSort = [];

            $(this).find(moduleItemSelector).each(function(index, value) {
                var selectorObject = $(this);
                var uniqueID = '';
                $.each(dataAttr, function (index, value) {
                    if (uniqueID !== '') {
                        uniqueID += '#'; // Explode separator
                    }
                    uniqueID += selectorObject.attr(value);
                });
                arrayToSort[uniqueID] = $(this);
                keysToSort.push(uniqueID);
            });

            var keysArrayLength = keysToSort.length;

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

            currentSelector.fadeOut(function() {
                var _that = _this;
                var _arrayToSort = arrayToSort;
                var _currentSelector = currentSelector;

                currentSelector.empty();
                currentSelector.append('<div class="row">');

                $.each(keysToSort, function(index, value){
                    _currentSelector.find('.row').first().append(_arrayToSort[value].get(0).outerHTML);
                    delete _arrayToSort[value];
                });

                // Take care of Addons Search Card
                if ($(moduleItemSelector + ':visible').length != $(moduleItemSelector).length && addonItemHtmlBackup !== null) {
                    currentSelector.find('.row').first().append(addonItemHtmlBackup);
                    $(addonsItemSelector).css('display', 'table');
                }

                currentSelector.append('</div>');
                currentSelector.fadeIn();
            });
        });
    };

    this.initActionButtons = function () {
        var _this = this;

        $(this.moduleInstallBtnSelector).on('click', function (event) {
            event.preventDefault();
            var _that = _this;
            var next = $(this).next();
            $(this).hide();
            $(next).show();
            $.ajax({
                url: $(this).attr("data-url"),
                dataType: 'json',
            }).done(function () {
                $(next).fadeOut();
            });
        });
    };

    this.initCategorySelect = function () {
        var _this = this;
        $(this.categoryItemSelector).on('click', function () {
            // Get data from li DOM input
            _this.currentRefMenu = $(this).attr('data-category-ref');
            var categorySelectedDisplayName = $(this).attr('data-category-display-name');
            // Change dropdown label to set it to the current category's displayname
            $(_this.categorySelectorLabelSelector).text(categorySelectedDisplayName);
            $(_this.categoryResetBtnSelector).css('display', 'block');
            // Do Search on categoryRef
            _this.doCategorySearch(_this.currentRefMenu);
        });

        $(this.categoryResetBtnSelector).on('click', function () {
            var rawText = $(_this.categorySelector).attr('aria-labelledby');
            var upperFirstLetter = rawText.charAt(0).toUpperCase();
            var removedFirstLetter = rawText.slice(1);
            var originalText = upperFirstLetter + removedFirstLetter;
            $(_this.categorySelectorLabelSelector).text(originalText);
            $(this).css('display', 'none');
            _this.currentRefMenu = null;
            _this.doTagSearch(_this.currentTagsList);
        });
    };


    this.doCategorySearch = function (categoryRef) {
        // Pick the right selector to process search
        var moduleItemSelector = this.getModuleItemSelector();
        var moduleGlobalSelector = this.getModuleGlobalSelector();
        var _this = this;

        $(moduleGlobalSelector).each(function (index, value) {
            var _that = _this;
            var totalModules = 0;
            // Go through each module items to check if its contains filters tags keywords...
            $(this).find(moduleItemSelector).each(function (index, value) {
                // get Module's categories references to match them against categoryRef
                var dataCategories = $(this).attr('data-categories').toLowerCase();
                var moduleItem = $(this);

                if (dataCategories === categoryRef.toLowerCase()) {
                    moduleItem.css('display', 'block');
                    totalModules += 1;
                    // Match found, return true to continue to iterate
                    return true;
                } else {
                    // Nothing found so we have to return true to apply 'display: none' on item
                    moduleItem.css('display', 'none');
                }
            });
            // If any tags already here redo search, with new categeory
            if (_this.currentTagsList.length) {
                _this.doTagSearch(_this.currentTagsList);
            } else {
                _this.updateTotalResults(totalModules, $(this));
            }
        });


    };

    this.resetSearch = function () {
        // Pick the right selector to process search
        var moduleItemSelector = this.getModuleItemSelector();
        var moduleGlobalSelector = this.getModuleGlobalSelector();
        var _this = this;

        // Reset currentTagsList
        this.currentTagsList = [];

        // Avoid trying to redisplay everything if it's already fully displayed
        if (this.areAllModuleDisplayed === false) {

            $(moduleGlobalSelector).each(function (index, value) {
                var totalModules = 0;
                var _that = _this;
                $(this).find(moduleItemSelector).each(function (index, value) {
                    if (_that.currentRefMenu !== null) {
                        var isFromFilterCategory = ($(this).attr('data-categories') == _that.currentRefMenu);
                        if (isFromFilterCategory === true) {
                            totalModules += 1;
                        }
                        if ($(this).is(':hidden') && isFromFilterCategory === true) {
                            $(this).css('display', 'block');
                        }
                    } else {
                        totalModules += 1;
                        if ($(this).is(':hidden')) {
                            $(this).css('display', 'block');
                        }
                    }
                });

                // Dont forget this vital var once this done
                _this.areAllModuleDisplayed = true;
                _this.updateTotalResults(totalModules, $(this));
            });
        }
    };

    this.doTagSearch = function (tagsList) {
        var _this = this;
        this.currentTagsList = tagsList;
        // Pick the right selector to process search
        var moduleItemSelector = this.getModuleItemSelector();
        var moduleGlobalSelector = this.getModuleGlobalSelector();
        var totalResultFound = 0;
        // First reset no result screen if needed
        if (!$('.module-search-no-result').is(':hidden')) {
            $('.module-search-no-result').css('display', 'none');
        }
        // Avoid redisplaying modules if there are already all here
        if (this.areAllModuleDisplayed === false && this.currentTagsList.length === 0) {
            this.resetSearch();
        } else {
            $(moduleGlobalSelector).each(function (index, value) {
                var _that = _this;
                var matchCounter = 0;
                // Go through each module items to check if its contains filters tags keywords...
                $(this).find(moduleItemSelector).each(function (index, value) {
                    // #1: Check if any current category filter
                    if (_that.currentRefMenu !== null) {
                        if ($(this).attr('data-categories') !== _that.currentRefMenu) {
                            if (!$(this).is(':hidden')) {
                                $(this).css('display', 'none');
                                _that.areAllModuleDisplayed = false;
                            }
                            // Iterate to next item
                            return true;
                        }
                    }
                    // If no match on data-name, data-description or data-author hide module item
                    var dataName = $(this).attr('data-name').toLowerCase();
                    var dataDescription = $(this).attr('data-description').toLowerCase();
                    var dataAuthor = $(this).attr('data-author').toLowerCase();
                    var moduleItem = $(this);
                    var hasMatched = false;
                    var matchedTagsCount = 0;

                    $.each(_that.currentTagsList, function (index, value) {
                        // If match any on these attrbute  its a match
                        value = value.toLowerCase();
                        if (dataName.indexOf(value) != -1 || dataDescription.indexOf(value) != -1 ||
                                dataAuthor.indexOf(value) != -1) {
                            matchedTagsCount += 1;
                        }
                    });

                    // If module has matched all the tags display it, else hide it
                    if (matchedTagsCount == _that.currentTagsList.length) {
                        moduleItem.css('display', 'block');
                        matchCounter += 1;
                    } else {
                        moduleItem.css('display', 'none');
                        _that.areAllModuleDisplayed = false;
                    }
                });
                _this.updateTotalResults(matchCounter, $(this));
            });
        }
    };

    this.updateTotalResults = function (totalResultFound, domObject) {
        // Pick the right selector to process search
        var addonsItemSelector = this.getAddonItemSelector();
        var resultWordingObject = domObject.prev().find(this.totalResultSelector);

        $(addonsItemSelector).css('display', 'none');
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
            $(this.addonsSearchSelector).css('display', 'block');
            // Display category grid
            if (this.isCategoryGridDisplayed === false) {
                $(this.categoryGridSelector).fadeIn();
                this.isCategoryGridDisplayed = true;
            }
            $(addonsItemSelector).css('display', 'none');

        } else {
            if (this.isCategoryGridDisplayed === true) {
                $(this.categoryGridSelector).fadeOut();
                this.isCategoryGridDisplayed = false;
            }
            var moduleItemSelector = this.getModuleItemSelector();

            if (totalResultFound != $(moduleItemSelector).length) {
                $(addonsItemSelector).css('display', 'table');
            } else {
                $(addonsItemSelector).css('display', 'none');
            }
        }
    };


    this.initSearchBlock = function() {
        var _this = this;
       this.pstaggerInput = $(this.searchBarSelector).pstagger({
                                                                       onTagsChanged: _this.doTagSearch,
                                                                       onResetTags: _this.resetSearch,
                                                                       inputPlaceholder: 'Add tag ...',
                                                                       closingCross: true,
                                                                       context: _this,
                                                                       clearAllBtn: true,
                                                                       clearAllIconClassAdditional: 'material-icons',
                                                                       clearAllSpanClassAdditional: 'module-tags-clear-btn ',
                                                                       tagInputClassAdditional: 'module-tags-input',
                                                                       tagClassAdditional: 'module-tag ',
                                                                       tagsWrapperClassAdditional: 'module-tags-labels',
                                                                   });

       $(this.addonsSearchLinkSelector).on('click', function(event){
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
       var _this = this;

       $(this.sortDisplaySelector).on('click', function() {
         var switchTo = $(this).attr('data-switch');
         var isAlreadyDisplayed = $(this).hasClass('active-display');
         if (typeof switchTo != 'undefined' && isAlreadyDisplayed === false) {
           _this.switchSortingDisplayTo(switchTo);
           _this.currentDisplay = switchTo;
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
        var _this = this;
        var addonsItemSelector = this.getAddonItemSelector();
        var gridListSelector = this.getModuleGlobalSelector();
        var addonItem = $(addonsItemSelector);

        if (switchTo == 'grid') {
            // Change main wrapper classe to grid
            $(gridListSelector).addClass('modules-grid').removeClass('modules-list');
            $(this.moduleItemListSelector).each(function () {
                $(_this.moduleSortListSelector).removeClass('module-sort-active');
                $(_this.moduleSortGridSelector).addClass('module-sort-active');
                $(this).removeClass();
                $(this).addClass('module-item-grid col-lg-3 col-md-4 col-sm-6');
                _this.setNewDisplay($(this), '-list', '-grid');
            });
            // Change module addons item
            addonItem.removeClass();
            addonItem.addClass('module-addons-item-grid col-lg-3 col-md-4 col-sm-6');
            this.setNewDisplay(addonItem, '-list', '-grid');

        } else if (switchTo == 'list') {
            // Change main wrapper classe to list
            $(gridListSelector).addClass('modules-list').removeClass('modules-grid');
            $(this.moduleItemGridSelector).each(function (index) {
                $(_this.moduleSortGridSelector).removeClass('module-sort-active');
                $(_this.moduleSortListSelector).addClass('module-sort-active');
                $(this).removeClass();
                $(this).addClass('module-item-list col-md-12');
                _this.setNewDisplay($(this), '-grid', '-list');
            });
            // Change module addons item
            addonItem.removeClass();
            addonItem.addClass('module-addons-item-list col-md-12');
            this.setNewDisplay(addonItem, '-grid', '-list');
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
     * @param {string} domObj jQuery Dom Element
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
