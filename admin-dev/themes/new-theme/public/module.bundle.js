<<<<<<< HEAD
window.module=function(e){function t(n){if(o[n])return o[n].exports;var i=o[n]={i:n,l:!1,exports:{}};return e[n].call(i.exports,i,i.exports,t),i.l=!0,i.exports}var o={};return t.m=e,t.c=o,t.i=function(e){return e},t.d=function(e,o,n){t.o(e,o)||Object.defineProperty(e,o,{configurable:!1,enumerable:!0,get:n})},t.n=function(e){var o=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(o,"a",o),o},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=335)}({259:function(e,t,o){"use strict";function n(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var i=function(){function e(e,t){for(var o=0;o<t.length;o++){var n=t[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,o,n){return o&&e(t.prototype,o),n&&e(t,n),t}}(),l=window.$,r=function(){function e(t){n(this,e),this.moduleCardController=t,this.DEFAULT_MAX_RECENTLY_USED=10,this.DEFAULT_MAX_PER_CATEGORIES=6,this.DISPLAY_GRID="grid",this.DISPLAY_LIST="list",this.CATEGORY_RECENTLY_USED="recently-used",this.currentCategoryDisplay={},this.currentDisplay="",this.isCategoryGridDisplayed=!1,this.currentTagsList=[],this.currentRefCategory=null,this.currentRefStatus=null,this.currentSorting=null,this.baseAddonsUrl="https://addons.prestashop.com/",this.pstaggerInput=null,this.lastBulkAction=null,this.isUploadStarted=!1,this.recentlyUsedSelector="#module-recently-used-list .modules-list",this.modulesList=[],this.addonsCardGrid=null,this.addonsCardList=null,this.moduleShortList=".module-short-list",this.seeMoreSelector=".see-more",this.seeLessSelector=".see-less",this.moduleItemGridSelector=".module-item-grid",this.moduleItemListSelector=".module-item-list",this.categorySelectorLabelSelector=".module-category-selector-label",this.categorySelector=".module-category-selector",this.categoryItemSelector=".module-category-menu",this.addonsLoginButtonSelector="#addons_login_btn",this.categoryResetBtnSelector=".module-category-reset",this.moduleInstallBtnSelector="input.module-install-btn",this.moduleSortingDropdownSelector=".module-sorting-author select",this.categoryGridSelector="#modules-categories-grid",this.categoryGridItemSelector=".module-category-item",this.addonItemGridSelector=".module-addons-item-grid",this.addonItemListSelector=".module-addons-item-list",this.upgradeAllSource=".module_action_menu_upgrade_all",this.upgradeAllTargets="#modules-list-container-update .module_action_menu_upgrade:visible",this.bulkActionDropDownSelector=".module-bulk-actions",this.bulkItemSelector=".module-bulk-menu",this.bulkActionCheckboxListSelector=".module-checkbox-bulk-list input",this.bulkActionCheckboxGridSelector=".module-checkbox-bulk-grid input",this.checkedBulkActionListSelector=this.bulkActionCheckboxListSelector+":checked",this.checkedBulkActionGridSelector=this.bulkActionCheckboxGridSelector+":checked",this.bulkActionCheckboxSelector="#module-modal-bulk-checkbox",this.bulkConfirmModalSelector="#module-modal-bulk-confirm",this.bulkConfirmModalActionNameSelector="#module-modal-bulk-confirm-action-name",this.bulkConfirmModalListSelector="#module-modal-bulk-confirm-list",this.bulkConfirmModalAckBtnSelector="#module-modal-confirm-bulk-ack",this.placeholderGlobalSelector=".module-placeholders-wrapper",this.placeholderFailureGlobalSelector=".module-placeholders-failure",this.placeholderFailureMsgSelector=".module-placeholders-failure-msg",this.placeholderFailureRetryBtnSelector="#module-placeholders-failure-retry",this.statusSelectorLabelSelector=".module-status-selector-label",this.statusItemSelector=".module-status-menu",this.statusResetBtnSelector=".module-status-reset",this.addonsConnectModalBtnSelector="#page-header-desc-configuration-addons_connect",this.addonsLogoutModalBtnSelector="#page-header-desc-configuration-addons_logout",this.addonsImportModalBtnSelector="#page-header-desc-configuration-add_module",this.dropZoneModalSelector="#module-modal-import",this.dropZoneModalFooterSelector="#module-modal-import .modal-footer",this.dropZoneImportZoneSelector="#importDropzone",this.addonsConnectModalSelector="#module-modal-addons-connect",this.addonsLogoutModalSelector="#module-modal-addons-logout",this.addonsConnectForm="#addons-connect-form",this.moduleImportModalCloseBtn="#module-modal-import-closing-cross",this.moduleImportStartSelector=".module-import-start",this.moduleImportProcessingSelector=".module-import-processing",this.moduleImportSuccessSelector=".module-import-success",this.moduleImportSuccessConfigureBtnSelector=".module-import-success-configure",this.moduleImportFailureSelector=".module-import-failure",this.moduleImportFailureRetrySelector=".module-import-failure-retry",this.moduleImportFailureDetailsBtnSelector=".module-import-failure-details-action",this.moduleImportSelectFileManualSelector=".module-import-start-select-manual",this.moduleImportFailureMsgDetailsSelector=".module-import-failure-details",this.moduleImportConfirmSelector=".module-import-confirm",this.initSortingDropdown(),this.initBOEventRegistering(),this.initCurrentDisplay(),this.initSortingDisplaySwitch(),this.initBulkDropdown(),this.initSearchBlock(),this.initCategorySelect(),this.initCategoriesGrid(),this.initActionButtons(),this.initAddonsSearch(),this.initAddonsConnect(),this.initAddModuleAction(),this.initDropzone(),this.initPageChangeProtection(),this.initPlaceholderMechanism(),this.initFilterStatusDropdown(),this.fetchModulesList(),this.getNotificationsCount(),this.initializeSeeMore()}return i(e,[{key:"initFilterStatusDropdown",value:function(){var e=this,t=l("body");t.on("click",e.statusItemSelector,function(){e.currentRefStatus=parseInt(l(this).data("status-ref"),10),l(e.statusSelectorLabelSelector).text(l(this).find("a:first").text()),l(e.statusResetBtnSelector).show(),e.updateModuleVisibility()}),t.on("click",e.statusResetBtnSelector,function(){l(e.statusSelectorLabelSelector).text(l(this).find("a").text()),l(this).hide(),e.currentRefStatus=null,e.updateModuleVisibility()})}},{key:"initBulkDropdown",value:function(){var e=this,t=l("body");t.on("click",e.getBulkCheckboxesSelector(),function(){var t=l(e.bulkActionDropDownSelector);l(e.getBulkCheckboxesCheckedSelector()).length>0?t.closest(".module-top-menu-item").removeClass("disabled"):t.closest(".module-top-menu-item").addClass("disabled")}),t.on("click",e.bulkItemSelector,function(){if(0===l(e.getBulkCheckboxesCheckedSelector()).length)return void l.growl.warning({message:window.translate_javascripts["Bulk Action - One module minimum"]});e.lastBulkAction=l(this).data("ref");var t=e.buildBulkActionModuleList(),o=l(this).find(":checked").text().toLowerCase();l(e.bulkConfirmModalListSelector).html(t),l(e.bulkConfirmModalActionNameSelector).text(o),"bulk-uninstall"===e.lastBulkAction?l(e.bulkActionCheckboxSelector).show():l(e.bulkActionCheckboxSelector).hide(),l(e.bulkConfirmModalSelector).modal("show")}),t.on("click",this.bulkConfirmModalAckBtnSelector,function(t){t.preventDefault(),t.stopPropagation(),l(e.bulkConfirmModalSelector).modal("hide"),e.doBulkAction(e.lastBulkAction)})}},{key:"initBOEventRegistering",value:function(){window.BOEvent.on("Module Disabled",this.onModuleDisabled,this),window.BOEvent.on("Module Uninstalled",this.updateTotalResults,this)}},{key:"onModuleDisabled",value:function(){var e=this;e.getModuleItemSelector();l(".modules-list").each(function(){e.updateTotalResults()})}},{key:"initPlaceholderMechanism",value:function(){var e=this;l(e.placeholderGlobalSelector).length&&e.ajaxLoadPage(),l("body").on("click",e.placeholderFailureRetryBtnSelector,function(){l(e.placeholderFailureGlobalSelector).fadeOut(),l(e.placeholderGlobalSelector).fadeIn(),e.ajaxLoadPage()})}},{key:"ajaxLoadPage",value:function(){var e=this;l.ajax({method:"GET",url:window.moduleURLs.catalogRefresh}).done(function(t){if(!0===t.status){void 0===t.domElements&&(t.domElements=null),void 0===t.msg&&(t.msg=null);var o=document.styleSheets[0];o.insertRule?o.insertRule(".modules-list,.module-sorting-menu{display: none}",o.cssRules.length):o.addRule&&o.addRule(".modules-list,.module-sorting-menu","{display: none}",-1),l(e.placeholderGlobalSelector).fadeOut(800,function(){l.each(t.domElements,function(e,t){l(t.selector).append(t.content)}),l(".modules-list").fadeIn(800).css("display","flex"),l(".module-sorting-menu").fadeIn(800),l('[data-toggle="popover"]').popover(),e.initCurrentDisplay(),e.fetchModulesList()})}else l(e.placeholderGlobalSelector).fadeOut(800,function(){l(e.placeholderFailureMsgSelector).text(t.msg),l(e.placeholderFailureGlobalSelector).fadeIn(800)})}).fail(function(t){l(e.placeholderGlobalSelector).fadeOut(800,function(){l(e.placeholderFailureMsgSelector).text(t.statusText),l(e.placeholderFailureGlobalSelector).fadeIn(800)})})}},{key:"fetchModulesList",value:function(){var e=this,t=void 0,o=void 0;e.modulesList=[],l(".modules-list").each(function(){t=l(this),t.find(".module-item").each(function(){o=l(this),e.modulesList.push({domObject:o,id:o.data("id"),name:o.data("name").toLowerCase(),scoring:parseFloat(o.data("scoring")),logo:o.data("logo"),author:o.data("author").toLowerCase(),version:o.data("version"),description:o.data("description").toLowerCase(),techName:o.data("tech-name").toLowerCase(),childCategories:o.data("child-categories"),categories:String(o.data("categories")).toLowerCase(),type:o.data("type"),price:parseFloat(o.data("price")),active:parseInt(o.data("active"),10),access:o.data("last-access"),display:o.hasClass("module-item-list")?e.DISPLAY_LIST:e.DISPLAY_GRID,container:t}),o.remove()})}),e.addonsCardGrid=l(this.addonItemGridSelector),e.addonsCardList=l(this.addonItemListSelector),e.updateModuleVisibility(),l("body").trigger("moduleCatalogLoaded")}},{key:"updateModuleSorting",value:function(){var e=this;if(e.currentSorting){var t="asc",o=e.currentSorting,n=o.split("-");n.length>1&&(o=n[0],"desc"===n[1]&&(t="desc"));var i=function(e,t){var n=e[o],i=t[o];return"access"===o&&(n=new Date(n).getTime(),i=new Date(i).getTime(),n=isNaN(n)?0:n,i=isNaN(i)?0:i,n===i)?t.name.localeCompare(e.name):n<i?-1:n>i?1:0};e.modulesList.sort(i),"desc"===t&&e.modulesList.reverse()}}},{key:"updateModuleContainerDisplay",value:function(){var e=this;l(".module-short-list").each(function(){var t=l(this),o=t.find(".module-item").length;if(e.currentRefCategory&&e.currentRefCategory!==String(t.find(".modules-list").data("name"))||null!==e.currentRefStatus&&0===o||0===o&&String(t.find(".modules-list").data("name"))===e.CATEGORY_RECENTLY_USED||e.currentTagsList.length>0&&0===o)return void t.hide();t.show(),o>=e.DEFAULT_MAX_PER_CATEGORIES?t.find(e.seeMoreSelector+", "+e.seeLessSelector).show():t.find(e.seeMoreSelector+", "+e.seeLessSelector).hide()})}},{key:"updateModuleVisibility",value:function(){var e=this;e.updateModuleSorting(),l(e.recentlyUsedSelector).find(".module-item").remove(),l(".modules-list").find(".module-item").remove();for(var t=void 0,o=void 0,n=void 0,i=void 0,r=void 0,a=e.modulesList.length,s={},u=0;u<a;u+=1)o=e.modulesList[u],o.display===e.currentDisplay&&(t=!0,n=e.currentRefCategory===e.CATEGORY_RECENTLY_USED?e.CATEGORY_RECENTLY_USED:o.categories,null!==e.currentRefCategory&&(t&=n===e.currentRefCategory),null!==e.currentRefStatus&&(t&=o.active===e.currentRefStatus),e.currentTagsList.length&&(i=!1,l.each(e.currentTagsList,function(e,t){r=t.toLowerCase(),i|=-1!==o.name.indexOf(r)||-1!==o.description.indexOf(r)||-1!==o.author.indexOf(r)||-1!==o.techName.indexOf(r)}),t&=i),e.currentDisplay!==e.DISPLAY_LIST||e.currentTagsList.length||(void 0===e.currentCategoryDisplay[n]&&(e.currentCategoryDisplay[n]=!1),s[n]||(s[n]=0),n===e.CATEGORY_RECENTLY_USED?s[n]>=e.DEFAULT_MAX_RECENTLY_USED&&(t&=e.currentCategoryDisplay[n]):s[n]>=e.DEFAULT_MAX_PER_CATEGORIES&&(t&=e.currentCategoryDisplay[n]),s[n]+=1),t&&(e.currentRefCategory===e.CATEGORY_RECENTLY_USED?l(e.recentlyUsedSelector).append(o.domObject):o.container.append(o.domObject)));e.updateModuleContainerDisplay(),e.currentTagsList.length&&l(".modules-list").append(this.currentDisplay===e.DISPLAY_GRID?this.addonsCardGrid:this.addonsCardList),e.updateTotalResults()}},{key:"initPageChangeProtection",value:function(){var e=this;l(window).on("beforeunload",function(){if(!0===e.isUploadStarted)return"It seems some critical operation are running, are you sure you want to change page ? It might cause some unexepcted behaviors."})}},{key:"buildBulkActionModuleList",value:function(){var e=this.getBulkCheckboxesCheckedSelector(),t=this.getModuleItemSelector(),o=0,n="",i=void 0;return l(e).each(function(){return 10===o?(n+="- ...",!1):(i=l(this).closest(t),n+="- "+i.data("name")+"<br/>",o+=1,!0)}),n}},{key:"initAddonsConnect",value:function(){var e=this;"#"===l(e.addonsConnectModalBtnSelector).attr("href")&&(l(e.addonsConnectModalBtnSelector).attr("data-toggle","modal"),l(e.addonsConnectModalBtnSelector).attr("data-target",e.addonsConnectModalSelector)),"#"===l(e.addonsLogoutModalBtnSelector).attr("href")&&(l(e.addonsLogoutModalBtnSelector).attr("data-toggle","modal"),l(e.addonsLogoutModalBtnSelector).attr("data-target",e.addonsLogoutModalSelector)),l("body").on("submit",e.addonsConnectForm,function(t){t.preventDefault(),t.stopPropagation(),l.ajax({method:"POST",url:l(this).attr("action"),dataType:"json",data:l(this).serialize(),beforeSend:function(){l(e.addonsLoginButtonSelector).show(),l('button.btn[type="submit"]',e.addonsConnectForm).hide()}}).done(function(t){1===t.success?location.reload():(l.growl.error({message:t.message}),l(e.addonsLoginButtonSelector).hide(),l('button.btn[type="submit"]',e.addonsConnectForm).fadeIn())})})}},{key:"initAddModuleAction",value:function(){var e=this,t=l(e.addonsImportModalBtnSelector);t.attr("data-toggle","modal"),t.attr("data-target",e.dropZoneModalSelector)}},{key:"initDropzone",value:function(){var e=this,t=l("body"),o=l(".dropzone");t.on("click",this.moduleImportFailureRetrySelector,function(){l(e.moduleImportSuccessSelector+","+e.moduleImportFailureSelector+","+e.moduleImportProcessingSelector).fadeOut(function(){setTimeout(function(){l(e.moduleImportStartSelector).fadeIn(function(){l(e.moduleImportFailureMsgDetailsSelector).hide(),l(e.moduleImportSuccessConfigureBtnSelector).hide(),o.removeAttr("style")})},550)})}),t.on("hidden.bs.modal",this.dropZoneModalSelector,function(){l(e.moduleImportSuccessSelector+", "+e.moduleImportFailureSelector).hide(),l(e.moduleImportStartSelector).show(),o.removeAttr("style"),l(e.moduleImportFailureMsgDetailsSelector).hide(),l(e.moduleImportSuccessConfigureBtnSelector).hide(),l(e.dropZoneModalFooterSelector).html(""),l(e.moduleImportConfirmSelector).hide()}),t.on("click",".dropzone:not("+this.moduleImportSelectFileManualSelector+", "+this.moduleImportSuccessConfigureBtnSelector+")",function(e,t){void 0===t&&(e.stopPropagation(),e.preventDefault())}),t.on("click",this.moduleImportSelectFileManualSelector,function(e){e.stopPropagation(),e.preventDefault(),l(".dz-hidden-input").trigger("click",["manual_select"])}),t.on("click",this.moduleImportModalCloseBtn,function(){!0!==e.isUploadStarted&&l(e.dropZoneModalSelector).modal("hide")}),t.on("click",this.moduleImportSuccessConfigureBtnSelector,function(e){e.stopPropagation(),e.preventDefault(),window.location=l(this).attr("href")}),t.on("click",this.moduleImportFailureDetailsBtnSelector,function(){l(e.moduleImportFailureMsgDetailsSelector).slideDown()});var n={url:window.moduleURLs.moduleImport,acceptedFiles:".zip, .tar",paramName:"file_uploaded",maxFilesize:50,uploadMultiple:!1,addRemoveLinks:!0,dictDefaultMessage:"",hiddenInputContainer:e.dropZoneImportZoneSelector,timeout:0,addedfile:function(){e.animateStartUpload()},processing:function(){},error:function(t,o){e.displayOnUploadError(o)},complete:function(t){if("error"!==t.status){var o=l.parseJSON(t.xhr.response);void 0===o.is_configurable&&(o.is_configurable=null),void 0===o.module_name&&(o.module_name=null),e.displayOnUploadDone(o)}e.isUploadStarted=!1}};o.dropzone(l.extend(n))}},{key:"animateStartUpload",value:function(){var e=this,t=l(".dropzone");e.isUploadStarted=!0,l(e.moduleImportStartSelector).hide(0),t.css("border","none"),l(e.moduleImportProcessingSelector).fadeIn()}},{key:"animateEndUpload",value:function(e){l(this.moduleImportProcessingSelector).finish().fadeOut(e)}},{key:"displayOnUploadDone",value:function(e){var t=this;t.animateEndUpload(function(){if(!0===e.status){if(!0===e.is_configurable){var o=window.moduleURLs.configurationPage.replace(/:number:/,e.module_name);l(t.moduleImportSuccessConfigureBtnSelector).attr("href",o),l(t.moduleImportSuccessConfigureBtnSelector).show()}l(t.moduleImportSuccessSelector).fadeIn()}else void 0!==e.confirmation_subject?t.displayPrestaTrustStep(e):(l(t.moduleImportFailureMsgDetailsSelector).html(e.msg),l(t.moduleImportFailureSelector).fadeIn())})}},{key:"displayOnUploadError",value:function(e){var t=this;t.animateEndUpload(function(){l(t.moduleImportFailureMsgDetailsSelector).html(e),l(t.moduleImportFailureSelector).fadeIn()})}},{key:"displayPrestaTrustStep",value:function(e){var t=this,o=t.moduleCardController._replacePrestaTrustPlaceholders(e),n=e.module.attributes.name;l(this.moduleImportConfirmSelector).html(o.find(".modal-body").html()).fadeIn(),l(this.dropZoneModalFooterSelector).html(o.find(".modal-footer").html()).fadeIn(),l(this.dropZoneModalFooterSelector).find(".pstrust-install").off("click").on("click",function(){l(t.moduleImportConfirmSelector).hide(),l(t.dropZoneModalFooterSelector).html(""),t.animateStartUpload(),l.post(e.module.attributes.urls.install,{"actionParams[confirmPrestaTrust]":"1"}).done(function(e){t.displayOnUploadDone(e[n])}).fail(function(e){t.displayOnUploadError(e[n])}).always(function(){t.isUploadStarted=!1})})}},{key:"getBulkCheckboxesSelector",value:function(){return this.currentDisplay===this.DISPLAY_GRID?this.bulkActionCheckboxGridSelector:this.bulkActionCheckboxListSelector}},{key:"getBulkCheckboxesCheckedSelector",value:function(){return this.currentDisplay===this.DISPLAY_GRID?this.checkedBulkActionGridSelector:this.checkedBulkActionListSelector}},{key:"getModuleItemSelector",value:function(){return this.currentDisplay===this.DISPLAY_GRID?this.moduleItemGridSelector:this.moduleItemListSelector}},{key:"getNotificationsCount",value:function(){var e=this;l.getJSON(window.moduleURLs.notificationsCount,e.updateNotificationsCount).fail(function(){console.error("Could not retrieve module notifications count.")})}},{key:"updateNotificationsCount",value:function(e){var t={to_configure:l("#subtab-AdminModulesNotifications"),to_update:l("#subtab-AdminModulesUpdates")};for(var o in t)0!==t[o].length&&t[o].find(".notification-counter").text(e[o])}},{key:"initAddonsSearch",value:function(){var e=this;l("body").on("click",e.addonItemGridSelector+", "+e.addonItemListSelector,function(){var t="";e.currentTagsList.length&&(t=encodeURIComponent(e.currentTagsList.join(" "))),window.open(e.baseAddonsUrl+"search.php?search_query="+t,"_blank")})}},{key:"initCategoriesGrid",value:function(){var e=this;l("body").on("click",this.categoryGridItemSelector,function(t){t.stopPropagation(),t.preventDefault();var o=l(this).data("category-ref");return e.currentTagsList.length&&(e.pstaggerInput.resetTags(!1),e.currentTagsList=[]),l(e.categoryItemSelector+'[data-category-ref="'+o+'"]').length?(!0===e.isCategoryGridDisplayed&&(l(e.categoryGridSelector).fadeOut(),e.isCategoryGridDisplayed=!1),l(e.categoryItemSelector+'[data-category-ref="'+o+'"]').click(),!0):(console.warn("No category with ref ("+o+") seems to exist!"),!1)})}},{key:"initCurrentDisplay",value:function(){this.currentDisplay=""===this.currentDisplay?this.DISPLAY_LIST:this.DISPLAY_GRID}},{key:"initSortingDropdown",value:function(){var e=this;e.currentSorting=l(this.moduleSortingDropdownSelector).find(":checked").attr("value"),e.currentSorting||(e.currentSorting="access-desc"),l("body").on("change",e.moduleSortingDropdownSelector,function(){e.currentSorting=l(this).find(":checked").attr("value"),e.updateModuleVisibility()})}},{key:"doBulkAction",value:function(e){var t=l("#force_bulk_deletion").prop("checked"),o={"bulk-uninstall":"uninstall","bulk-disable":"disable","bulk-enable":"enable","bulk-disable-mobile":"disable_mobile","bulk-enable-mobile":"enable_mobile","bulk-reset":"reset"};if(void 0===o[e])return l.growl.error({message:window.translate_javascripts["Bulk Action - Request not found"].replace("[1]",e)}),!1;var n=this.getBulkCheckboxesCheckedSelector(),i=o[e];if(l(n).length<=0)return console.warn(window.translate_javascripts["Bulk Action - One module minimum"]),!1;var r=[],a=void 0;return l(n).each(function(){a=l(this).data("tech-name"),r.push({techName:a,actionMenuObj:l(this).closest(".module-checkbox-bulk-list").next()})}),this.performModulesAction(r,i,t),!0}},{key:"performModulesAction",value:function(e,t,o){function n(e,n,i){r.moduleCardController._requestToController(t,e,o,n,i)}function i(){if(--s<=0){u&&(u.remove(),u=null);var e=a[a.length-1];e.closest(r.moduleCardController.moduleItemActionsSelector).fadeIn(),n(e)}}var r=this;if(void 0!==r.moduleCardController){var a=function(e){var o=[],n=void 0;return l.each(e,function(e,i){n=l(r.moduleCardController.moduleActionMenuLinkSelector+t,i.actionMenuObj),n.length>0?o.push(n):l.growl.error({message:window.translate_javascripts["Bulk Action - Request not available for module"].replace("[1]",t).replace("[2]",i.techName)})}),o}(e);if(a.length){var s=a.length-1,u=l('<button class="btn-primary-reverse onclick unbind spinner "></button>');if(a.length>1){l.each(a,function(e,t){e>=a.length-1||n(t,!0,i)});var c=a[a.length-1],d=c.closest(r.moduleCardController.moduleItemActionsSelector);d.hide(),d.after(u)}else n(a[0])}}}},{key:"initActionButtons",value:function(){var e=this,t=this;l("body").on("click",t.moduleInstallBtnSelector,function(e){var t=l(this),o=l(t.next());e.preventDefault(),t.hide(),o.show(),l.ajax({url:t.data("url"),dataType:"json"}).done(function(){o.fadeOut()})}),l("body").on("click",t.upgradeAllSource,function(o){if(o.preventDefault(),l(t.upgradeAllTargets).length<=0)return console.warn(window.translate_javascripts["Upgrade All Action - One module minimum"]),!1;var n=[],i=void 0;return l(t.upgradeAllTargets).each(function(){var e=l(this).closest(".module-item-list");i=e.data("tech-name"),n.push({techName:i,actionMenuObj:l(".module-actions",e)})}),e.performModulesAction(n,"upgrade"),!0})}},{key:"initCategorySelect",value:function(){var e=this,t=l("body");t.on("click",e.categoryItemSelector,function(){e.currentRefCategory=l(this).data("category-ref"),e.currentRefCategory=e.currentRefCategory?String(e.currentRefCategory).toLowerCase():null,l(e.categorySelectorLabelSelector).text(l(this).data("category-display-name")),l(e.categoryResetBtnSelector).show(),e.updateModuleVisibility()}),t.on("click",e.categoryResetBtnSelector,function(){var t=l(e.categorySelector).attr("aria-labelledby"),o=t.charAt(0).toUpperCase(),n=t.slice(1),i=o+n;l(e.categorySelectorLabelSelector).text(i),l(this).hide(),e.currentRefCategory=null,e.updateModuleVisibility()})}},{key:"initSearchBlock",value:function(){var e=this,t=this;t.pstaggerInput=l("#module-search-bar").pstagger({onTagsChanged:function(e){t.currentTagsList=e,t.updateModuleVisibility()},onResetTags:function(){t.currentTagsList=[],t.updateModuleVisibility()},inputPlaceholder:window.translate_javascripts["Search - placeholder"],closingCross:!0,context:t}),l("body").on("click",".module-addons-search-link",function(t){t.preventDefault(),t.stopPropagation(),window.open(l(e).attr("href"),"_blank")})}},{key:"initSortingDisplaySwitch",value:function(){var e=this;l("body").on("click",".module-sort-switch",function(){var t=l(this).data("switch"),o=l(this).hasClass("active-display");void 0!==t&&!1===o&&(e.switchSortingDisplayTo(t),e.currentDisplay=t)})}},{key:"switchSortingDisplayTo",value:function(e){if(e!==this.DISPLAY_GRID&&e!==this.DISPLAY_LIST)return void console.error("Can't switch to undefined display property \""+e+'"');l(".module-sort-switch").removeClass("module-sort-active"),l("#module-sort-"+e).addClass("module-sort-active"),this.currentDisplay=e,this.updateModuleVisibility()}},{key:"initializeSeeMore",value:function(){var e=this;l(e.moduleShortList+" "+e.seeMoreSelector).on("click",function(){e.currentCategoryDisplay[l(this).data("category")]=!0,l(this).addClass("d-none"),l(this).closest(e.moduleShortList).find(e.seeLessSelector).removeClass("d-none"),e.updateModuleVisibility()}),l(e.moduleShortList+" "+e.seeLessSelector).on("click",function(){e.currentCategoryDisplay[l(this).data("category")]=!1,l(this).addClass("d-none"),l(this).closest(e.moduleShortList).find(e.seeMoreSelector).removeClass("d-none"),e.updateModuleVisibility()})}},{key:"updateTotalResults",value:function(){var e=function(e,t){var o=e.text().split(" ");o[0]=t,e.text(o.join(" "))},t=l(".module-short-list");if(t.length>0)t.each(function(){var t=l(this);e(t.find(".module-search-result-wording"),t.next(".modules-list").find(".module-item").length)});else{var o=l(".modules-list").find(".module-item").length;e(l(".module-search-result-wording"),o);var n=self.currentDisplay===self.DISPLAY_LIST?this.addonItemListSelector:this.addonItemGridSelector;l(n).toggle(o!==this.modulesList.length/2),0===o&&l(".module-addons-search-link").attr("href",this.baseAddonsUrl+"search.php?search_query="+encodeURIComponent(this.currentTagsList.join(" ")))}}}]),e}();t.default=r},260:function(e,t,o){"use strict";function n(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var i=function(){function e(e,t){for(var o=0;o<t.length;o++){var n=t[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,o,n){return o&&e(t.prototype,o),n&&e(t,n),t}}(),l=window.$,r=function(){function e(){n(this,e),e.handleImport(),e.handleEvents()}return i(e,null,[{key:"handleImport",value:function(){function e(){setTimeout(function(){o.removeClass("onclick"),o.addClass("validate",450,t)},2250)}function t(){setTimeout(function(){o.removeClass("validate")},1250)}var o=l("#module-import");o.click(function(){o.addClass("onclick",250,e)})}},{key:"handleEvents",value:function(){l("body").on("click","a.module-read-more-grid-btn, a.module-read-more-list-btn",function(e){e.preventDefault();var t=l(e.target).data("target");l.get(e.target.href,function(e){l(t).html(e),l(t).modal()})})}}]),e}();t.default=r},335:function(e,t,o){"use strict";function n(e){return e&&e.__esModule?e:{default:e}}var i=o(56),l=n(i),r=o(259),a=n(r),s=o(260),u=n(s);/**
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
(0,window.$)(function(){var e=new l.default;new u.default,new a.default(e)})},4:function(e,t){!function(){e.exports=window.jQuery}()},56:function(e,t,o){"use strict";(function(e){function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var n="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},i=function(){function e(e,t){for(var o=0;o<t.length;o++){var n=t[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,o,n){return o&&e(t.prototype,o),n&&e(t,n),t}}(),l=window.$,r={on:function(e,t,o){document.addEventListener(e,function(e){void 0!==o?t.call(o,e):t(e)})},emitEvent:function(e,t){var o=document.createEvent(t);o.initEvent(e,!0,!0),document.dispatchEvent(o)}},a=function(){function t(){o(this,t),this.moduleActionMenuLinkSelector="button.module_action_menu_",this.moduleActionMenuInstallLinkSelector="button.module_action_menu_install",this.moduleActionMenuEnableLinkSelector="button.module_action_menu_enable",this.moduleActionMenuUninstallLinkSelector="button.module_action_menu_uninstall",this.moduleActionMenuDisableLinkSelector="button.module_action_menu_disable",this.moduleActionMenuEnableMobileLinkSelector="button.module_action_menu_enable_mobile",this.moduleActionMenuDisableMobileLinkSelector="button.module_action_menu_disable_mobile",this.moduleActionMenuResetLinkSelector="button.module_action_menu_reset",this.moduleActionMenuUpdateLinkSelector="button.module_action_menu_upgrade",this.moduleItemListSelector=".module-item-list",this.moduleItemGridSelector=".module-item-grid",this.moduleItemActionsSelector=".module-actions",this.moduleActionModalDisableLinkSelector="a.module_action_modal_disable",this.moduleActionModalResetLinkSelector="a.module_action_modal_reset",this.moduleActionModalUninstallLinkSelector="a.module_action_modal_uninstall",this.forceDeletionOption="#force_deletion",this.initActionButtons()}return i(t,[{key:"initActionButtons",value:function(){var e=this;l(document).on("click",this.forceDeletionOption,function(){var t=l(e.moduleActionModalUninstallLinkSelector,l("div.module-item-list[data-tech-name='"+l(this).attr("data-tech-name")+"']"));!0===l(this).prop("checked")?t.attr("data-deletion","true"):t.removeAttr("data-deletion")}),l(document).on("click",this.moduleActionMenuInstallLinkSelector,function(){return l("#modal-prestatrust").length&&l("#modal-prestatrust").modal("hide"),e._dispatchPreEvent("install",this)&&e._confirmAction("install",this)&&e._requestToController("install",l(this))}),l(document).on("click",this.moduleActionMenuEnableLinkSelector,function(){return e._dispatchPreEvent("enable",this)&&e._confirmAction("enable",this)&&e._requestToController("enable",l(this))}),l(document).on("click",this.moduleActionMenuUninstallLinkSelector,function(){return e._dispatchPreEvent("uninstall",this)&&e._confirmAction("uninstall",this)&&e._requestToController("uninstall",l(this))}),l(document).on("click",this.moduleActionMenuDisableLinkSelector,function(){return e._dispatchPreEvent("disable",this)&&e._confirmAction("disable",this)&&e._requestToController("disable",l(this))}),l(document).on("click",this.moduleActionMenuEnableMobileLinkSelector,function(){return e._dispatchPreEvent("enable_mobile",this)&&e._confirmAction("enable_mobile",this)&&e._requestToController("enable_mobile",l(this))}),l(document).on("click",this.moduleActionMenuDisableMobileLinkSelector,function(){return e._dispatchPreEvent("disable_mobile",this)&&e._confirmAction("disable_mobile",this)&&e._requestToController("disable_mobile",l(this))}),l(document).on("click",this.moduleActionMenuResetLinkSelector,function(){return e._dispatchPreEvent("reset",this)&&e._confirmAction("reset",this)&&e._requestToController("reset",l(this))}),l(document).on("click",this.moduleActionMenuUpdateLinkSelector,function(){return e._dispatchPreEvent("update",this)&&e._confirmAction("update",this)&&e._requestToController("update",l(this))}),l(document).on("click",this.moduleActionModalDisableLinkSelector,function(){return e._requestToController("disable",l(e.moduleActionMenuDisableLinkSelector,l("div.module-item-list[data-tech-name='"+l(this).attr("data-tech-name")+"']")))}),l(document).on("click",this.moduleActionModalResetLinkSelector,function(){return e._requestToController("reset",l(e.moduleActionMenuResetLinkSelector,l("div.module-item-list[data-tech-name='"+l(this).attr("data-tech-name")+"']")))}),l(document).on("click",this.moduleActionModalUninstallLinkSelector,function(t){l(t.target).parents(".modal").on("hidden.bs.modal",function(o){return e._requestToController("uninstall",l(e.moduleActionMenuUninstallLinkSelector,l("div.module-item-list[data-tech-name='"+l(t.target).attr("data-tech-name")+"']")),l(t.target).attr("data-deletion"))}.bind(t))})}},{key:"_getModuleItemSelector",value:function(){return l(this.moduleItemListSelector).length?this.moduleItemListSelector:this.moduleItemGridSelector}},{key:"_confirmAction",value:function(e,t){var o=l("#"+l(t).data("confirm_modal"));return 1!=o.length||(o.first().modal("show"),!1)}},{key:"_confirmPrestaTrust",value:function(e){var t=this,o=this._replacePrestaTrustPlaceholders(e);o.find(".pstrust-install").off("click").on("click",function(){var n=l(t.moduleActionMenuInstallLinkSelector,'.module-item[data-tech-name="'+e.module.attributes.name+'"]'),i=n.parent("form");l("<input>").attr({type:"hidden",value:"1",name:"actionParams[confirmPrestaTrust]"}).appendTo(i),n.click(),o.modal("hide")}),o.modal()}},{key:"_replacePrestaTrustPlaceholders",value:function(e){var t=l("#modal-prestatrust"),o=e.module.attributes;if("PrestaTrust"===e.confirmation_subject&&t.length){var n=o.prestatrust.status?"success":"warning";return o.prestatrust.check_list.property?(t.find("#pstrust-btn-property-ok").show(),t.find("#pstrust-btn-property-nok").hide()):(t.find("#pstrust-btn-property-ok").hide(),t.find("#pstrust-btn-property-nok").show(),t.find("#pstrust-buy").attr("href",o.url).toggle(null!==o.url)),t.find("#pstrust-img").attr({src:o.img,alt:o.name}),t.find("#pstrust-name").text(o.displayName),t.find("#pstrust-author").text(o.author),t.find("#pstrust-label").attr("class","text-"+n).text(o.prestatrust.status?"OK":"KO"),t.find("#pstrust-message").attr("class","alert alert-"+n),t.find("#pstrust-message > p").text(o.prestatrust.message),t}}},{key:"_dispatchPreEvent",value:function(t,o){var n=e.Event("module_card_action_event");return l(o).trigger(n,[t]),!1===n.isPropagationStopped()&&!1===n.isImmediatePropagationStopped()&&!1!==n.result}},{key:"_requestToController",value:function(e,t,o,i,a){var s=this,u=t.closest(this.moduleItemActionsSelector),c=t.closest("form"),d=l('<button class="btn-primary-reverse onclick unbind spinner "></button>'),m="//"+window.location.host+c.attr("action"),h=c.serializeArray();return"true"!==o&&!0!==o||h.push({name:"actionParams[deletion]",value:!0}),"true"!==i&&!0!==i||h.push({name:"actionParams[cacheClearEnabled]",value:0}),l.ajax({url:m,dataType:"json",method:"POST",data:h,beforeSend:function(){u.hide(),u.after(d)}}).done(function(t){if(void 0===(void 0===t?"undefined":n(t)))l.growl.error({message:"No answer received from server"});else{var o=Object.keys(t)[0];if(!1===t[o].status)void 0!==t[o].confirmation_subject&&s._confirmPrestaTrust(t[o]),l.growl.error({message:t[o].msg});else{l.growl.notice({message:t[o].msg});var i=s._getModuleItemSelector().replace(".",""),a=null;"uninstall"==e?(a=u.closest("."+i),a.remove(),r.emitEvent("Module Uninstalled","CustomEvent")):"disable"==e?(a=u.closest("."+i),a.addClass(i+"-isNotActive"),a.attr("data-active","0"),r.emitEvent("Module Disabled","CustomEvent")):"enable"==e&&(a=u.closest("."+i),a.removeClass(i+"-isNotActive"),a.attr("data-active","1"),r.emitEvent("Module Enabled","CustomEvent")),u.replaceWith(t[o].action_menu_html)}}}).fail(function(){var t=u.closest("module-item-list"),o=t.data("techName");l.growl.error({message:"Could not perform action "+e+" for module "+o})}).always(function(){u.fadeIn(),d.remove(),a&&a()}),!1}}]),t}();t.default=a}).call(t,o(4))}});
=======
/******/ (function(modules) { // webpackBootstrap
/******/ 	function hotDisposeChunk(chunkId) {
/******/ 		delete installedChunks[chunkId];
/******/ 	}
/******/ 	var parentHotUpdateCallback = this["webpackHotUpdate"];
/******/ 	this["webpackHotUpdate"] = 
/******/ 	function webpackHotUpdateCallback(chunkId, moreModules) { // eslint-disable-line no-unused-vars
/******/ 		hotAddUpdateChunk(chunkId, moreModules);
/******/ 		if(parentHotUpdateCallback) parentHotUpdateCallback(chunkId, moreModules);
/******/ 	} ;
/******/ 	
/******/ 	function hotDownloadUpdateChunk(chunkId) { // eslint-disable-line no-unused-vars
/******/ 		var head = document.getElementsByTagName("head")[0];
/******/ 		var script = document.createElement("script");
/******/ 		script.type = "text/javascript";
/******/ 		script.charset = "utf-8";
/******/ 		script.src = __webpack_require__.p + "" + chunkId + "." + hotCurrentHash + ".hot-update.js";
/******/ 		head.appendChild(script);
/******/ 	}
/******/ 	
/******/ 	function hotDownloadManifest() { // eslint-disable-line no-unused-vars
/******/ 		return new Promise(function(resolve, reject) {
/******/ 			if(typeof XMLHttpRequest === "undefined")
/******/ 				return reject(new Error("No browser support"));
/******/ 			try {
/******/ 				var request = new XMLHttpRequest();
/******/ 				var requestPath = __webpack_require__.p + "" + hotCurrentHash + ".hot-update.json";
/******/ 				request.open("GET", requestPath, true);
/******/ 				request.timeout = 10000;
/******/ 				request.send(null);
/******/ 			} catch(err) {
/******/ 				return reject(err);
/******/ 			}
/******/ 			request.onreadystatechange = function() {
/******/ 				if(request.readyState !== 4) return;
/******/ 				if(request.status === 0) {
/******/ 					// timeout
/******/ 					reject(new Error("Manifest request to " + requestPath + " timed out."));
/******/ 				} else if(request.status === 404) {
/******/ 					// no update available
/******/ 					resolve();
/******/ 				} else if(request.status !== 200 && request.status !== 304) {
/******/ 					// other failure
/******/ 					reject(new Error("Manifest request to " + requestPath + " failed."));
/******/ 				} else {
/******/ 					// success
/******/ 					try {
/******/ 						var update = JSON.parse(request.responseText);
/******/ 					} catch(e) {
/******/ 						reject(e);
/******/ 						return;
/******/ 					}
/******/ 					resolve(update);
/******/ 				}
/******/ 			};
/******/ 		});
/******/ 	}
/******/
<<<<<<< HEAD
var o={};t.m=e,t.c=o,t.i=function(e){return e},t.d=function(e,o,i){t.o(e,o)||Object.defineProperty(e,o,{configurable:!1,enumerable:!0,get:i})},t.n=function(e){var o=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(o,"a",o),o},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=452)}({211:function(e,t,o){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var i=o(39),n=o(278),l=o(279);(0,window.$)(function(){var e=new i.a;new l.a,new n.a(e)})},278:function(e,t,o){"use strict";function i(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var n=function(){function e(e,t){for(var o=0;o<t.length;o++){var i=t[o];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(e,i.key,i)}}return function(t,o,i){return o&&e(t.prototype,o),i&&e(t,i),t}}(),l=window.$,r=function(){function e(t){i(this,e),this.moduleCardController=t,this.DEFAULT_MAX_RECENTLY_USED=10,this.DEFAULT_MAX_PER_CATEGORIES=6,this.DISPLAY_GRID="grid",this.DISPLAY_LIST="list",this.CATEGORY_RECENTLY_USED="recently-used",this.currentCategoryDisplay={},this.currentDisplay="",this.isCategoryGridDisplayed=!1,this.currentTagsList=[],this.currentRefCategory=null,this.currentRefStatus=null,this.currentSorting=null,this.baseAddonsUrl="https://addons.prestashop.com/",this.pstaggerInput=null,this.lastBulkAction=null,this.isUploadStarted=!1,this.recentlyUsedSelector="#module-recently-used-list .modules-list",this.modulesList=[],this.addonsCardGrid=null,this.addonsCardList=null,this.moduleShortList=".module-short-list",this.seeMoreSelector=".see-more",this.seeLessSelector=".see-less",this.moduleItemGridSelector=".module-item-grid",this.moduleItemListSelector=".module-item-list",this.categorySelectorLabelSelector=".module-category-selector-label",this.categorySelector=".module-category-selector",this.categoryItemSelector=".module-category-menu",this.addonsLoginButtonSelector="#addons_login_btn",this.categoryResetBtnSelector=".module-category-reset",this.moduleInstallBtnSelector="input.module-install-btn",this.moduleSortingDropdownSelector=".module-sorting-author select",this.categoryGridSelector="#modules-categories-grid",this.categoryGridItemSelector=".module-category-item",this.addonItemGridSelector=".module-addons-item-grid",this.addonItemListSelector=".module-addons-item-list",this.upgradeAllSource=".module_action_menu_upgrade_all",this.upgradeAllTargets="#modules-list-container-update .module_action_menu_upgrade:visible",this.bulkActionDropDownSelector=".module-bulk-actions",this.bulkItemSelector=".module-bulk-menu",this.bulkActionCheckboxListSelector=".module-checkbox-bulk-list input",this.bulkActionCheckboxGridSelector=".module-checkbox-bulk-grid input",this.checkedBulkActionListSelector=this.bulkActionCheckboxListSelector+":checked",this.checkedBulkActionGridSelector=this.bulkActionCheckboxGridSelector+":checked",this.bulkActionCheckboxSelector="#module-modal-bulk-checkbox",this.bulkConfirmModalSelector="#module-modal-bulk-confirm",this.bulkConfirmModalActionNameSelector="#module-modal-bulk-confirm-action-name",this.bulkConfirmModalListSelector="#module-modal-bulk-confirm-list",this.bulkConfirmModalAckBtnSelector="#module-modal-confirm-bulk-ack",this.placeholderGlobalSelector=".module-placeholders-wrapper",this.placeholderFailureGlobalSelector=".module-placeholders-failure",this.placeholderFailureMsgSelector=".module-placeholders-failure-msg",this.placeholderFailureRetryBtnSelector="#module-placeholders-failure-retry",this.statusSelectorLabelSelector=".module-status-selector-label",this.statusItemSelector=".module-status-menu",this.statusResetBtnSelector=".module-status-reset",this.addonsConnectModalBtnSelector="#page-header-desc-configuration-addons_connect",this.addonsLogoutModalBtnSelector="#page-header-desc-configuration-addons_logout",this.addonsImportModalBtnSelector="#page-header-desc-configuration-add_module",this.dropZoneModalSelector="#module-modal-import",this.dropZoneModalFooterSelector="#module-modal-import .modal-footer",this.dropZoneImportZoneSelector="#importDropzone",this.addonsConnectModalSelector="#module-modal-addons-connect",this.addonsLogoutModalSelector="#module-modal-addons-logout",this.addonsConnectForm="#addons-connect-form",this.moduleImportModalCloseBtn="#module-modal-import-closing-cross",this.moduleImportStartSelector=".module-import-start",this.moduleImportProcessingSelector=".module-import-processing",this.moduleImportSuccessSelector=".module-import-success",this.moduleImportSuccessConfigureBtnSelector=".module-import-success-configure",this.moduleImportFailureSelector=".module-import-failure",this.moduleImportFailureRetrySelector=".module-import-failure-retry",this.moduleImportFailureDetailsBtnSelector=".module-import-failure-details-action",this.moduleImportSelectFileManualSelector=".module-import-start-select-manual",this.moduleImportFailureMsgDetailsSelector=".module-import-failure-details",this.moduleImportConfirmSelector=".module-import-confirm",this.initSortingDropdown(),this.initBOEventRegistering(),this.initCurrentDisplay(),this.initSortingDisplaySwitch(),this.initBulkDropdown(),this.initSearchBlock(),this.initCategorySelect(),this.initCategoriesGrid(),this.initActionButtons(),this.initAddonsSearch(),this.initAddonsConnect(),this.initAddModuleAction(),this.initDropzone(),this.initPageChangeProtection(),this.initPlaceholderMechanism(),this.initFilterStatusDropdown(),this.fetchModulesList(),this.getNotificationsCount(),this.initializeSeeMore()}return n(e,[{key:"initFilterStatusDropdown",value:function(){var e=this,t=l("body");t.on("click",e.statusItemSelector,function(){e.currentRefStatus=parseInt(l(this).data("status-ref"),10),l(e.statusSelectorLabelSelector).text(l(this).find("a:first").text()),l(e.statusResetBtnSelector).show(),e.updateModuleVisibility()}),t.on("click",e.statusResetBtnSelector,function(){l(e.statusSelectorLabelSelector).text(l(this).find("a").text()),l(this).hide(),e.currentRefStatus=null,e.updateModuleVisibility()})}},{key:"initBulkDropdown",value:function(){var e=this,t=l("body");t.on("click",e.getBulkCheckboxesSelector(),function(){var t=l(e.bulkActionDropDownSelector);l(e.getBulkCheckboxesCheckedSelector()).length>0?t.closest(".module-top-menu-item").removeClass("disabled"):t.closest(".module-top-menu-item").addClass("disabled")}),t.on("click",e.bulkItemSelector,function(){if(0===l(e.getBulkCheckboxesCheckedSelector()).length)return void l.growl.warning({message:window.translate_javascripts["Bulk Action - One module minimum"]});e.lastBulkAction=l(this).data("ref");var t=e.buildBulkActionModuleList(),o=l(this).find(":checked").text().toLowerCase();l(e.bulkConfirmModalListSelector).html(t),l(e.bulkConfirmModalActionNameSelector).text(o),"bulk-uninstall"===e.lastBulkAction?l(e.bulkActionCheckboxSelector).show():l(e.bulkActionCheckboxSelector).hide(),l(e.bulkConfirmModalSelector).modal("show")}),t.on("click",this.bulkConfirmModalAckBtnSelector,function(t){t.preventDefault(),t.stopPropagation(),l(e.bulkConfirmModalSelector).modal("hide"),e.doBulkAction(e.lastBulkAction)})}},{key:"initBOEventRegistering",value:function(){window.BOEvent.on("Module Disabled",this.onModuleDisabled,this),window.BOEvent.on("Module Uninstalled",this.updateTotalResults,this)}},{key:"onModuleDisabled",value:function(){var e=this;e.getModuleItemSelector();l(".modules-list").each(function(){e.updateTotalResults()})}},{key:"initPlaceholderMechanism",value:function(){var e=this;l(e.placeholderGlobalSelector).length&&e.ajaxLoadPage(),l("body").on("click",e.placeholderFailureRetryBtnSelector,function(){l(e.placeholderFailureGlobalSelector).fadeOut(),l(e.placeholderGlobalSelector).fadeIn(),e.ajaxLoadPage()})}},{key:"ajaxLoadPage",value:function(){var e=this;l.ajax({method:"GET",url:window.moduleURLs.catalogRefresh}).done(function(t){if(!0===t.status){void 0===t.domElements&&(t.domElements=null),void 0===t.msg&&(t.msg=null);var o=document.styleSheets[0];o.insertRule?o.insertRule(".modules-list,.module-sorting-menu{display: none}",o.cssRules.length):o.addRule&&o.addRule(".modules-list,.module-sorting-menu","{display: none}",-1),l(e.placeholderGlobalSelector).fadeOut(800,function(){l.each(t.domElements,function(e,t){l(t.selector).append(t.content)}),l(".modules-list").fadeIn(800).css("display","flex"),l(".module-sorting-menu").fadeIn(800),l('[data-toggle="popover"]').popover(),e.initCurrentDisplay(),e.fetchModulesList()})}else l(e.placeholderGlobalSelector).fadeOut(800,function(){l(e.placeholderFailureMsgSelector).text(t.msg),l(e.placeholderFailureGlobalSelector).fadeIn(800)})}).fail(function(t){l(e.placeholderGlobalSelector).fadeOut(800,function(){l(e.placeholderFailureMsgSelector).text(t.statusText),l(e.placeholderFailureGlobalSelector).fadeIn(800)})})}},{key:"fetchModulesList",value:function(){var e=this,t=void 0,o=void 0;e.modulesList=[],l(".modules-list").each(function(){t=l(this),t.find(".module-item").each(function(){o=l(this),e.modulesList.push({domObject:o,id:o.data("id"),name:o.data("name").toLowerCase(),scoring:parseFloat(o.data("scoring")),logo:o.data("logo"),author:o.data("author").toLowerCase(),version:o.data("version"),description:o.data("description").toLowerCase(),techName:o.data("tech-name").toLowerCase(),childCategories:o.data("child-categories"),categories:String(o.data("categories")).toLowerCase(),type:o.data("type"),price:parseFloat(o.data("price")),active:parseInt(o.data("active"),10),access:o.data("last-access"),display:o.hasClass("module-item-list")?e.DISPLAY_LIST:e.DISPLAY_GRID,container:t}),o.remove()})}),e.addonsCardGrid=l(this.addonItemGridSelector),e.addonsCardList=l(this.addonItemListSelector),e.updateModuleVisibility(),l("body").trigger("moduleCatalogLoaded")}},{key:"updateModuleSorting",value:function(){var e=this;if(e.currentSorting){var t="asc",o=e.currentSorting,i=o.split("-");i.length>1&&(o=i[0],"desc"===i[1]&&(t="desc"));var n=function(e,t){var i=e[o],n=t[o];return"access"===o&&(i=new Date(i).getTime(),n=new Date(n).getTime(),i=isNaN(i)?0:i,n=isNaN(n)?0:n,i===n)?t.name.localeCompare(e.name):i<n?-1:i>n?1:0};e.modulesList.sort(n),"desc"===t&&e.modulesList.reverse()}}},{key:"updateModuleContainerDisplay",value:function(){var e=this;l(".module-short-list").each(function(){var t=l(this),o=t.find(".module-item").length;if(e.currentRefCategory&&e.currentRefCategory!==String(t.find(".modules-list").data("name"))||null!==e.currentRefStatus&&0===o||0===o&&String(t.find(".modules-list").data("name"))===e.CATEGORY_RECENTLY_USED||e.currentTagsList.length>0&&0===o)return void t.hide();t.show(),o>=e.DEFAULT_MAX_PER_CATEGORIES?t.find(e.seeMoreSelector+", "+e.seeLessSelector).show():t.find(e.seeMoreSelector+", "+e.seeLessSelector).hide()})}},{key:"updateModuleVisibility",value:function(){var e=this;e.updateModuleSorting(),l(e.recentlyUsedSelector).find(".module-item").remove(),l(".modules-list").find(".module-item").remove();for(var t=void 0,o=void 0,i=void 0,n=void 0,r=void 0,a=e.modulesList.length,s={},u=0;u<a;u+=1)o=e.modulesList[u],o.display===e.currentDisplay&&(t=!0,i=e.currentRefCategory===e.CATEGORY_RECENTLY_USED?e.CATEGORY_RECENTLY_USED:o.categories,null!==e.currentRefCategory&&(t&=i===e.currentRefCategory),null!==e.currentRefStatus&&(t&=o.active===e.currentRefStatus),e.currentTagsList.length&&(n=!1,l.each(e.currentTagsList,function(e,t){r=t.toLowerCase(),n|=-1!==o.name.indexOf(r)||-1!==o.description.indexOf(r)||-1!==o.author.indexOf(r)||-1!==o.techName.indexOf(r)}),t&=n),e.currentDisplay!==e.DISPLAY_LIST||e.currentTagsList.length||(void 0===e.currentCategoryDisplay[i]&&(e.currentCategoryDisplay[i]=!1),s[i]||(s[i]=0),i===e.CATEGORY_RECENTLY_USED?s[i]>=e.DEFAULT_MAX_RECENTLY_USED&&(t&=e.currentCategoryDisplay[i]):s[i]>=e.DEFAULT_MAX_PER_CATEGORIES&&(t&=e.currentCategoryDisplay[i]),s[i]+=1),t&&(e.currentRefCategory===e.CATEGORY_RECENTLY_USED?l(e.recentlyUsedSelector).append(o.domObject):o.container.append(o.domObject)));e.updateModuleContainerDisplay(),e.currentTagsList.length&&l(".modules-list").append(this.currentDisplay===e.DISPLAY_GRID?this.addonsCardGrid:this.addonsCardList),e.updateTotalResults()}},{key:"initPageChangeProtection",value:function(){var e=this;l(window).on("beforeunload",function(){if(!0===e.isUploadStarted)return"It seems some critical operation are running, are you sure you want to change page ? It might cause some unexepcted behaviors."})}},{key:"buildBulkActionModuleList",value:function(){var e=this.getBulkCheckboxesCheckedSelector(),t=this.getModuleItemSelector(),o=0,i="",n=void 0;return l(e).each(function(){return 10===o?(i+="- ...",!1):(n=l(this).closest(t),i+="- "+n.data("name")+"<br/>",o+=1,!0)}),i}},{key:"initAddonsConnect",value:function(){var e=this;"#"===l(e.addonsConnectModalBtnSelector).attr("href")&&(l(e.addonsConnectModalBtnSelector).attr("data-toggle","modal"),l(e.addonsConnectModalBtnSelector).attr("data-target",e.addonsConnectModalSelector)),"#"===l(e.addonsLogoutModalBtnSelector).attr("href")&&(l(e.addonsLogoutModalBtnSelector).attr("data-toggle","modal"),l(e.addonsLogoutModalBtnSelector).attr("data-target",e.addonsLogoutModalSelector)),l("body").on("submit",e.addonsConnectForm,function(t){t.preventDefault(),t.stopPropagation(),l.ajax({method:"POST",url:l(this).attr("action"),dataType:"json",data:l(this).serialize(),beforeSend:function(){l(e.addonsLoginButtonSelector).show(),l('button.btn[type="submit"]',e.addonsConnectForm).hide()}}).done(function(t){1===t.success?location.reload():(l.growl.error({message:t.message}),l(e.addonsLoginButtonSelector).hide(),l('button.btn[type="submit"]',e.addonsConnectForm).fadeIn())})})}},{key:"initAddModuleAction",value:function(){var e=this,t=l(e.addonsImportModalBtnSelector);t.attr("data-toggle","modal"),t.attr("data-target",e.dropZoneModalSelector)}},{key:"initDropzone",value:function(){var e=this,t=l("body"),o=l(".dropzone");t.on("click",this.moduleImportFailureRetrySelector,function(){l(e.moduleImportSuccessSelector+","+e.moduleImportFailureSelector+","+e.moduleImportProcessingSelector).fadeOut(function(){setTimeout(function(){l(e.moduleImportStartSelector).fadeIn(function(){l(e.moduleImportFailureMsgDetailsSelector).hide(),l(e.moduleImportSuccessConfigureBtnSelector).hide(),o.removeAttr("style")})},550)})}),t.on("hidden.bs.modal",this.dropZoneModalSelector,function(){l(e.moduleImportSuccessSelector+", "+e.moduleImportFailureSelector).hide(),l(e.moduleImportStartSelector).show(),o.removeAttr("style"),l(e.moduleImportFailureMsgDetailsSelector).hide(),l(e.moduleImportSuccessConfigureBtnSelector).hide(),l(e.dropZoneModalFooterSelector).html(""),l(e.moduleImportConfirmSelector).hide()}),t.on("click",".dropzone:not("+this.moduleImportSelectFileManualSelector+", "+this.moduleImportSuccessConfigureBtnSelector+")",function(e,t){void 0===t&&(e.stopPropagation(),e.preventDefault())}),t.on("click",this.moduleImportSelectFileManualSelector,function(e){e.stopPropagation(),e.preventDefault(),l(".dz-hidden-input").trigger("click",["manual_select"])}),t.on("click",this.moduleImportModalCloseBtn,function(){!0!==e.isUploadStarted&&l(e.dropZoneModalSelector).modal("hide")}),t.on("click",this.moduleImportSuccessConfigureBtnSelector,function(e){e.stopPropagation(),e.preventDefault(),window.location=l(this).attr("href")}),t.on("click",this.moduleImportFailureDetailsBtnSelector,function(){l(e.moduleImportFailureMsgDetailsSelector).slideDown()});var i={url:window.moduleURLs.moduleImport,acceptedFiles:".zip, .tar",paramName:"file_uploaded",maxFilesize:50,uploadMultiple:!1,addRemoveLinks:!0,dictDefaultMessage:"",hiddenInputContainer:e.dropZoneImportZoneSelector,timeout:0,addedfile:function(){e.animateStartUpload()},processing:function(){},error:function(t,o){e.displayOnUploadError(o)},complete:function(t){if("error"!==t.status){var o=l.parseJSON(t.xhr.response);void 0===o.is_configurable&&(o.is_configurable=null),void 0===o.module_name&&(o.module_name=null),e.displayOnUploadDone(o)}e.isUploadStarted=!1}};o.dropzone(l.extend(i))}},{key:"animateStartUpload",value:function(){var e=this,t=l(".dropzone");e.isUploadStarted=!0,l(e.moduleImportStartSelector).hide(0),t.css("border","none"),l(e.moduleImportProcessingSelector).fadeIn()}},{key:"animateEndUpload",value:function(e){l(this.moduleImportProcessingSelector).finish().fadeOut(e)}},{key:"displayOnUploadDone",value:function(e){var t=this;t.animateEndUpload(function(){if(!0===e.status){if(!0===e.is_configurable){var o=window.moduleURLs.configurationPage.replace(/:number:/,e.module_name);l(t.moduleImportSuccessConfigureBtnSelector).attr("href",o),l(t.moduleImportSuccessConfigureBtnSelector).show()}l(t.moduleImportSuccessSelector).fadeIn()}else void 0!==e.confirmation_subject?t.displayPrestaTrustStep(e):(l(t.moduleImportFailureMsgDetailsSelector).html(e.msg),l(t.moduleImportFailureSelector).fadeIn())})}},{key:"displayOnUploadError",value:function(e){var t=this;t.animateEndUpload(function(){l(t.moduleImportFailureMsgDetailsSelector).html(e),l(t.moduleImportFailureSelector).fadeIn()})}},{key:"displayPrestaTrustStep",value:function(e){var t=this,o=t.moduleCardController._replacePrestaTrustPlaceholders(e),i=e.module.attributes.name;l(this.moduleImportConfirmSelector).html(o.find(".modal-body").html()).fadeIn(),l(this.dropZoneModalFooterSelector).html(o.find(".modal-footer").html()).fadeIn(),l(this.dropZoneModalFooterSelector).find(".pstrust-install").off("click").on("click",function(){l(t.moduleImportConfirmSelector).hide(),l(t.dropZoneModalFooterSelector).html(""),t.animateStartUpload(),l.post(e.module.attributes.urls.install,{"actionParams[confirmPrestaTrust]":"1"}).done(function(e){t.displayOnUploadDone(e[i])}).fail(function(e){t.displayOnUploadError(e[i])}).always(function(){t.isUploadStarted=!1})})}},{key:"getBulkCheckboxesSelector",value:function(){return this.currentDisplay===this.DISPLAY_GRID?this.bulkActionCheckboxGridSelector:this.bulkActionCheckboxListSelector}},{key:"getBulkCheckboxesCheckedSelector",value:function(){return this.currentDisplay===this.DISPLAY_GRID?this.checkedBulkActionGridSelector:this.checkedBulkActionListSelector}},{key:"getModuleItemSelector",value:function(){return this.currentDisplay===this.DISPLAY_GRID?this.moduleItemGridSelector:this.moduleItemListSelector}},{key:"getNotificationsCount",value:function(){var e=this;l.getJSON(window.moduleURLs.notificationsCount,e.updateNotificationsCount).fail(function(){})}},{key:"updateNotificationsCount",value:function(e){var t={to_configure:l("#subtab-AdminModulesNotifications"),to_update:l("#subtab-AdminModulesUpdates")};for(var o in t)0!==t[o].length&&t[o].find(".notification-counter").text(e[o])}},{key:"initAddonsSearch",value:function(){var e=this;l("body").on("click",e.addonItemGridSelector+", "+e.addonItemListSelector,function(){var t="";e.currentTagsList.length&&(t=encodeURIComponent(e.currentTagsList.join(" "))),window.open(e.baseAddonsUrl+"search.php?search_query="+t,"_blank")})}},{key:"initCategoriesGrid",value:function(){var e=this;l("body").on("click",this.categoryGridItemSelector,function(t){t.stopPropagation(),t.preventDefault();var o=l(this).data("category-ref");return e.currentTagsList.length&&(e.pstaggerInput.resetTags(!1),e.currentTagsList=[]),!!l(e.categoryItemSelector+'[data-category-ref="'+o+'"]').length&&(!0===e.isCategoryGridDisplayed&&(l(e.categoryGridSelector).fadeOut(),e.isCategoryGridDisplayed=!1),l(e.categoryItemSelector+'[data-category-ref="'+o+'"]').click(),!0)})}},{key:"initCurrentDisplay",value:function(){this.currentDisplay=""===this.currentDisplay?this.DISPLAY_LIST:this.DISPLAY_GRID}},{key:"initSortingDropdown",value:function(){var e=this;e.currentSorting=l(this.moduleSortingDropdownSelector).find(":checked").attr("value"),e.currentSorting||(e.currentSorting="access-desc"),l("body").on("change",e.moduleSortingDropdownSelector,function(){e.currentSorting=l(this).find(":checked").attr("value"),e.updateModuleVisibility()})}},{key:"doBulkAction",value:function(e){var t=this,o=l("#force_bulk_deletion").prop("checked"),i={"bulk-uninstall":"uninstall","bulk-disable":"disable","bulk-enable":"enable","bulk-disable-mobile":"disable_mobile","bulk-enable-mobile":"enable_mobile","bulk-reset":"reset"};if(void 0===i[e])return l.growl.error({message:window.translate_javascripts["Bulk Action - Request not found"].replace("[1]",e)}),!1;var n=this.getBulkCheckboxesCheckedSelector();if(l(n).length<=0)return!1;var r=[],a=void 0;l(n).each(function(){a=l(this).data("tech-name"),r.push({techName:a,actionMenuObj:l(this).closest(".module-checkbox-bulk-list").next()})});var s=void 0,u=void 0,c=void 0;return l.each(r,function(n,r){s=r.actionMenuObj,a=r.techName,u=i[e],void 0!==t.moduleCardController&&(c=l(t.moduleCardController.moduleActionMenuLinkSelector+u,s),c.length>0?t.moduleCardController._requestToController(u,c,o):l.growl.error({message:window.translate_javascripts["Bulk Action - Request not available for module"].replace("[1]",u).replace("[2]",a)}))}),!0}},{key:"initActionButtons",value:function(){var e=this;l("body").on("click",e.moduleInstallBtnSelector,function(e){var t=l(this),o=l(t.next());e.preventDefault(),t.hide(),o.show(),l.ajax({url:t.data("url"),dataType:"json"}).done(function(){o.fadeOut()})}),l("body").on("click",e.upgradeAllSource,function(t){t.preventDefault(),l(e.upgradeAllTargets).click()})}},{key:"initCategorySelect",value:function(){var e=this,t=l("body");t.on("click",e.categoryItemSelector,function(){e.currentRefCategory=l(this).data("category-ref"),e.currentRefCategory=e.currentRefCategory?String(e.currentRefCategory).toLowerCase():null,l(e.categorySelectorLabelSelector).text(l(this).data("category-display-name")),l(e.categoryResetBtnSelector).show(),e.updateModuleVisibility()}),t.on("click",e.categoryResetBtnSelector,function(){var t=l(e.categorySelector).attr("aria-labelledby"),o=t.charAt(0).toUpperCase(),i=t.slice(1),n=o+i;l(e.categorySelectorLabelSelector).text(n),l(this).hide(),e.currentRefCategory=null,e.updateModuleVisibility()})}},{key:"initSearchBlock",value:function(){var e=this,t=this;t.pstaggerInput=l("#module-search-bar").pstagger({onTagsChanged:function(e){t.currentTagsList=e,t.updateModuleVisibility()},onResetTags:function(){t.currentTagsList=[],t.updateModuleVisibility()},inputPlaceholder:window.translate_javascripts["Search - placeholder"],closingCross:!0,context:t}),l("body").on("click",".module-addons-search-link",function(t){t.preventDefault(),t.stopPropagation(),window.open(l(e).attr("href"),"_blank")})}},{key:"initSortingDisplaySwitch",value:function(){var e=this;l("body").on("click",".module-sort-switch",function(){var t=l(this).data("switch"),o=l(this).hasClass("active-display");void 0!==t&&!1===o&&(e.switchSortingDisplayTo(t),e.currentDisplay=t)})}},{key:"switchSortingDisplayTo",value:function(e){e!==this.DISPLAY_GRID&&e!==this.DISPLAY_LIST||(l(".module-sort-switch").removeClass("module-sort-active"),l("#module-sort-"+e).addClass("module-sort-active"),this.currentDisplay=e,this.updateModuleVisibility())}},{key:"initializeSeeMore",value:function(){var e=this;l(e.moduleShortList+" "+e.seeMoreSelector).on("click",function(){e.currentCategoryDisplay[l(this).data("category")]=!0,l(this).addClass("d-none"),l(this).closest(e.moduleShortList).find(e.seeLessSelector).removeClass("d-none"),e.updateModuleVisibility()}),l(e.moduleShortList+" "+e.seeLessSelector).on("click",function(){e.currentCategoryDisplay[l(this).data("category")]=!1,l(this).addClass("d-none"),l(this).closest(e.moduleShortList).find(e.seeMoreSelector).removeClass("d-none"),e.updateModuleVisibility()})}},{key:"updateTotalResults",value:function(){var e=function(e,t){var o=e.text().split(" ");o[0]=t,e.text(o.join(" "))},t=l(".module-short-list");if(t.length>0)t.each(function(){var t=l(this);e(t.find(".module-search-result-wording"),t.next(".modules-list").find(".module-item").length)});else{var o=l(".modules-list").find(".module-item").length;e(l(".module-search-result-wording"),o);var i=self.currentDisplay===self.DISPLAY_LIST?this.addonItemListSelector:this.addonItemGridSelector;l(i).toggle(o!==this.modulesList.length/2),0===o&&l(".module-addons-search-link").attr("href",this.baseAddonsUrl+"search.php?search_query="+encodeURIComponent(this.currentTagsList.join(" ")))}}}]),e}();t.a=r},279:function(e,t,o){"use strict";function i(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var n=function(){function e(e,t){for(var o=0;o<t.length;o++){var i=t[o];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(e,i.key,i)}}return function(t,o,i){return o&&e(t.prototype,o),i&&e(t,i),t}}(),l=window.$,r=function(){function e(){i(this,e),e.handleImport(),e.handleEvents()}return n(e,null,[{key:"handleImport",value:function(){function e(){setTimeout(function(){o.removeClass("onclick"),o.addClass("validate",450,t)},2250)}function t(){setTimeout(function(){o.removeClass("validate")},1250)}var o=l("#module-import");o.click(function(){o.addClass("onclick",250,e)})}},{key:"handleEvents",value:function(){l("body").on("click","a.module-read-more-grid-btn, a.module-read-more-list-btn",function(e){e.preventDefault();var t=l(e.target).data("target");l.get(e.target.href,function(e){l(t).html(e),l(t).modal()})})}}]),e}();t.a=r},39:function(e,t,o){"use strict";function i(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var n="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},l=function(){function e(e,t){for(var o=0;o<t.length;o++){var i=t[o];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(e,i.key,i)}}return function(t,o,i){return o&&e(t.prototype,o),i&&e(t,i),t}}(),r=window.$,a={on:function(e,t,o){document.addEventListener(e,function(e){void 0!==o?t.call(o,e):t(e)})},emitEvent:function(e,t){var o=document.createEvent(t);o.initEvent(e,!0,!0),document.dispatchEvent(o)}},s=function(){function e(){i(this,e),this.moduleActionMenuLinkSelector="button.module_action_menu_",this.moduleActionMenuInstallLinkSelector="button.module_action_menu_install",this.moduleActionMenuEnableLinkSelector="button.module_action_menu_enable",this.moduleActionMenuUninstallLinkSelector="button.module_action_menu_uninstall",this.moduleActionMenuDisableLinkSelector="button.module_action_menu_disable",this.moduleActionMenuEnableMobileLinkSelector="button.module_action_menu_enable_mobile",this.moduleActionMenuDisableMobileLinkSelector="button.module_action_menu_disable_mobile",this.moduleActionMenuResetLinkSelector="button.module_action_menu_reset",this.moduleActionMenuUpdateLinkSelector="button.module_action_menu_upgrade",this.moduleItemListSelector=".module-item-list",this.moduleItemGridSelector=".module-item-grid",this.moduleItemActionsSelector=".module-actions",this.moduleActionModalDisableLinkSelector="a.module_action_modal_disable",this.moduleActionModalResetLinkSelector="a.module_action_modal_reset",this.moduleActionModalUninstallLinkSelector="a.module_action_modal_uninstall",this.forceDeletionOption="#force_deletion",this.initActionButtons()}return l(e,[{key:"initActionButtons",value:function(){var e=this;r(document).on("click",this.forceDeletionOption,function(){var t=r(e.moduleActionModalUninstallLinkSelector,r("div.module-item-list[data-tech-name='"+r(this).attr("data-tech-name")+"']"));!0===r(this).prop("checked")?t.attr("data-deletion","true"):t.removeAttr("data-deletion")}),r(document).on("click",this.moduleActionMenuInstallLinkSelector,function(){return r("#modal-prestatrust").length&&r("#modal-prestatrust").modal("hide"),e._dispatchPreEvent("install",this)&&e._confirmAction("install",this)&&e._requestToController("install",r(this))}),r(document).on("click",this.moduleActionMenuEnableLinkSelector,function(){return e._dispatchPreEvent("enable",this)&&e._confirmAction("enable",this)&&e._requestToController("enable",r(this))}),r(document).on("click",this.moduleActionMenuUninstallLinkSelector,function(){return e._dispatchPreEvent("uninstall",this)&&e._confirmAction("uninstall",this)&&e._requestToController("uninstall",r(this))}),r(document).on("click",this.moduleActionMenuDisableLinkSelector,function(){return e._dispatchPreEvent("disable",this)&&e._confirmAction("disable",this)&&e._requestToController("disable",r(this))}),r(document).on("click",this.moduleActionMenuEnableMobileLinkSelector,function(){return e._dispatchPreEvent("enable_mobile",this)&&e._confirmAction("enable_mobile",this)&&e._requestToController("enable_mobile",r(this))}),r(document).on("click",this.moduleActionMenuDisableMobileLinkSelector,function(){return e._dispatchPreEvent("disable_mobile",this)&&e._confirmAction("disable_mobile",this)&&e._requestToController("disable_mobile",r(this))}),r(document).on("click",this.moduleActionMenuResetLinkSelector,function(){return e._dispatchPreEvent("reset",this)&&e._confirmAction("reset",this)&&e._requestToController("reset",r(this))}),r(document).on("click",this.moduleActionMenuUpdateLinkSelector,function(){return e._dispatchPreEvent("update",this)&&e._confirmAction("update",this)&&e._requestToController("update",r(this))}),r(document).on("click",this.moduleActionModalDisableLinkSelector,function(){return e._requestToController("disable",r(e.moduleActionMenuDisableLinkSelector,r("div.module-item-list[data-tech-name='"+r(this).attr("data-tech-name")+"']")))}),r(document).on("click",this.moduleActionModalResetLinkSelector,function(){return e._requestToController("reset",r(e.moduleActionMenuResetLinkSelector,r("div.module-item-list[data-tech-name='"+r(this).attr("data-tech-name")+"']")))}),r(document).on("click",this.moduleActionModalUninstallLinkSelector,function(t){r(t.target).parents(".modal").on("hidden.bs.modal",function(o){return e._requestToController("uninstall",r(e.moduleActionMenuUninstallLinkSelector,r("div.module-item-list[data-tech-name='"+r(t.target).attr("data-tech-name")+"']")),r(t.target).attr("data-deletion"))}.bind(t))})}},{key:"_getModuleItemSelector",value:function(){return r(this.moduleItemListSelector).length?this.moduleItemListSelector:this.moduleItemGridSelector}},{key:"_confirmAction",value:function(e,t){var o=r("#"+r(t).data("confirm_modal"));return 1!=o.length||(o.first().modal("show"),!1)}},{key:"_confirmPrestaTrust",value:function(e){var t=this,o=this._replacePrestaTrustPlaceholders(e);o.find(".pstrust-install").off("click").on("click",function(){var i=r(t.moduleActionMenuInstallLinkSelector,'.module-item[data-tech-name="'+e.module.attributes.name+'"]'),n=i.parent("form");r("<input>").attr({type:"hidden",value:"1",name:"actionParams[confirmPrestaTrust]"}).appendTo(n),i.click(),o.modal("hide")}),o.modal()}},{key:"_replacePrestaTrustPlaceholders",value:function(e){var t=r("#modal-prestatrust"),o=e.module.attributes;if("PrestaTrust"===e.confirmation_subject&&t.length){var i=o.prestatrust.status?"success":"warning";return o.prestatrust.check_list.property?(t.find("#pstrust-btn-property-ok").show(),t.find("#pstrust-btn-property-nok").hide()):(t.find("#pstrust-btn-property-ok").hide(),t.find("#pstrust-btn-property-nok").show(),t.find("#pstrust-buy").attr("href",o.url).toggle(null!==o.url)),t.find("#pstrust-img").attr({src:o.img,alt:o.name}),t.find("#pstrust-name").text(o.displayName),t.find("#pstrust-author").text(o.author),t.find("#pstrust-label").attr("class","text-"+i).text(o.prestatrust.status?"OK":"KO"),t.find("#pstrust-message").attr("class","alert alert-"+i),t.find("#pstrust-message > p").text(o.prestatrust.message),t}}},{key:"_dispatchPreEvent",value:function(e,t){var o=jQuery.Event("module_card_action_event");return r(t).trigger(o,[e]),!1===o.isPropagationStopped()&&!1===o.isImmediatePropagationStopped()&&!1!==o.result}},{key:"_requestToController",value:function(e,t,o){var i=this,l=t.closest(this.moduleItemActionsSelector),s=t.closest("form"),u=r('<button class="btn-primary-reverse onclick unbind spinner "></button>'),c="//"+window.location.host+s.attr("action"),d=s.serializeArray();return"true"!==o&&!0!==o||d.push({name:"actionParams[deletion]",value:!0}),r.ajax({url:c,dataType:"json",method:"POST",data:d,beforeSend:function(){l.hide(),l.after(u)}}).done(function(t){if(void 0===(void 0===t?"undefined":n(t)))r.growl.error({message:"No answer received from server"});else{var o=Object.keys(t)[0];if(!1===t[o].status)void 0!==t[o].confirmation_subject&&i._confirmPrestaTrust(t[o]),r.growl.error({message:t[o].msg});else{r.growl.notice({message:t[o].msg});var s=null,u=null;"uninstall"==e?(l.fadeOut(function(){s=i._getModuleItemSelector().replace(".",""),u=l.parents("."+s).first(),u.remove()}),a.emitEvent("Module Uninstalled","CustomEvent")):"disable"==e?(s=i._getModuleItemSelector().replace(".",""),u=l.parents("."+s).first(),u.addClass(s+"-isNotActive"),u.attr("data-active","0"),a.emitEvent("Module Disabled","CustomEvent")):"enable"==e&&(s=i._getModuleItemSelector().replace(".",""),u=l.parents("."+s).first(),u.removeClass(s+"-isNotActive"),u.attr("data-active","1"),a.emitEvent("Module Enabled","CustomEvent")),l.replaceWith(t[o].action_menu_html)}}}).always(function(){l.fadeIn(),u.remove()}),!1}}]),e}();t.a=s},452:function(e,t,o){e.exports=o(211)}});
=======
/******/ 	
/******/ 	
/******/ 	var hotApplyOnUpdate = true;
/******/ 	var hotCurrentHash = "904bd858ab2c4dd1e50b"; // eslint-disable-line no-unused-vars
/******/ 	var hotCurrentModuleData = {};
/******/ 	var hotCurrentChildModule; // eslint-disable-line no-unused-vars
/******/ 	var hotCurrentParents = []; // eslint-disable-line no-unused-vars
/******/ 	var hotCurrentParentsTemp = []; // eslint-disable-line no-unused-vars
/******/ 	
/******/ 	function hotCreateRequire(moduleId) { // eslint-disable-line no-unused-vars
/******/ 		var me = installedModules[moduleId];
/******/ 		if(!me) return __webpack_require__;
/******/ 		var fn = function(request) {
/******/ 			if(me.hot.active) {
/******/ 				if(installedModules[request]) {
/******/ 					if(installedModules[request].parents.indexOf(moduleId) < 0)
/******/ 						installedModules[request].parents.push(moduleId);
/******/ 				} else {
/******/ 					hotCurrentParents = [moduleId];
/******/ 					hotCurrentChildModule = request;
/******/ 				}
/******/ 				if(me.children.indexOf(request) < 0)
/******/ 					me.children.push(request);
/******/ 			} else {
/******/ 				console.warn("[HMR] unexpected require(" + request + ") from disposed module " + moduleId);
/******/ 				hotCurrentParents = [];
/******/ 			}
/******/ 			return __webpack_require__(request);
/******/ 		};
/******/ 		var ObjectFactory = function ObjectFactory(name) {
/******/ 			return {
/******/ 				configurable: true,
/******/ 				enumerable: true,
/******/ 				get: function() {
/******/ 					return __webpack_require__[name];
/******/ 				},
/******/ 				set: function(value) {
/******/ 					__webpack_require__[name] = value;
/******/ 				}
/******/ 			};
/******/ 		};
/******/ 		for(var name in __webpack_require__) {
/******/ 			if(Object.prototype.hasOwnProperty.call(__webpack_require__, name) && name !== "e") {
/******/ 				Object.defineProperty(fn, name, ObjectFactory(name));
/******/ 			}
/******/ 		}
/******/ 		fn.e = function(chunkId) {
/******/ 			if(hotStatus === "ready")
/******/ 				hotSetStatus("prepare");
/******/ 			hotChunksLoading++;
/******/ 			return __webpack_require__.e(chunkId).then(finishChunkLoading, function(err) {
/******/ 				finishChunkLoading();
/******/ 				throw err;
/******/ 			});
/******/ 	
/******/ 			function finishChunkLoading() {
/******/ 				hotChunksLoading--;
/******/ 				if(hotStatus === "prepare") {
/******/ 					if(!hotWaitingFilesMap[chunkId]) {
/******/ 						hotEnsureUpdateChunk(chunkId);
/******/ 					}
/******/ 					if(hotChunksLoading === 0 && hotWaitingFiles === 0) {
/******/ 						hotUpdateDownloaded();
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 		return fn;
/******/ 	}
/******/ 	
/******/ 	function hotCreateModule(moduleId) { // eslint-disable-line no-unused-vars
/******/ 		var hot = {
/******/ 			// private stuff
/******/ 			_acceptedDependencies: {},
/******/ 			_declinedDependencies: {},
/******/ 			_selfAccepted: false,
/******/ 			_selfDeclined: false,
/******/ 			_disposeHandlers: [],
/******/ 			_main: hotCurrentChildModule !== moduleId,
/******/ 	
/******/ 			// Module API
/******/ 			active: true,
/******/ 			accept: function(dep, callback) {
/******/ 				if(typeof dep === "undefined")
/******/ 					hot._selfAccepted = true;
/******/ 				else if(typeof dep === "function")
/******/ 					hot._selfAccepted = dep;
/******/ 				else if(typeof dep === "object")
/******/ 					for(var i = 0; i < dep.length; i++)
/******/ 						hot._acceptedDependencies[dep[i]] = callback || function() {};
/******/ 				else
/******/ 					hot._acceptedDependencies[dep] = callback || function() {};
/******/ 			},
/******/ 			decline: function(dep) {
/******/ 				if(typeof dep === "undefined")
/******/ 					hot._selfDeclined = true;
/******/ 				else if(typeof dep === "object")
/******/ 					for(var i = 0; i < dep.length; i++)
/******/ 						hot._declinedDependencies[dep[i]] = true;
/******/ 				else
/******/ 					hot._declinedDependencies[dep] = true;
/******/ 			},
/******/ 			dispose: function(callback) {
/******/ 				hot._disposeHandlers.push(callback);
/******/ 			},
/******/ 			addDisposeHandler: function(callback) {
/******/ 				hot._disposeHandlers.push(callback);
/******/ 			},
/******/ 			removeDisposeHandler: function(callback) {
/******/ 				var idx = hot._disposeHandlers.indexOf(callback);
/******/ 				if(idx >= 0) hot._disposeHandlers.splice(idx, 1);
/******/ 			},
/******/ 	
/******/ 			// Management API
/******/ 			check: hotCheck,
/******/ 			apply: hotApply,
/******/ 			status: function(l) {
/******/ 				if(!l) return hotStatus;
/******/ 				hotStatusHandlers.push(l);
/******/ 			},
/******/ 			addStatusHandler: function(l) {
/******/ 				hotStatusHandlers.push(l);
/******/ 			},
/******/ 			removeStatusHandler: function(l) {
/******/ 				var idx = hotStatusHandlers.indexOf(l);
/******/ 				if(idx >= 0) hotStatusHandlers.splice(idx, 1);
/******/ 			},
/******/ 	
/******/ 			//inherit from previous dispose call
/******/ 			data: hotCurrentModuleData[moduleId]
/******/ 		};
/******/ 		hotCurrentChildModule = undefined;
/******/ 		return hot;
/******/ 	}
/******/ 	
/******/ 	var hotStatusHandlers = [];
/******/ 	var hotStatus = "idle";
/******/ 	
/******/ 	function hotSetStatus(newStatus) {
/******/ 		hotStatus = newStatus;
/******/ 		for(var i = 0; i < hotStatusHandlers.length; i++)
/******/ 			hotStatusHandlers[i].call(null, newStatus);
/******/ 	}
/******/ 	
/******/ 	// while downloading
/******/ 	var hotWaitingFiles = 0;
/******/ 	var hotChunksLoading = 0;
/******/ 	var hotWaitingFilesMap = {};
/******/ 	var hotRequestedFilesMap = {};
/******/ 	var hotAvailableFilesMap = {};
/******/ 	var hotDeferred;
/******/ 	
/******/ 	// The update info
/******/ 	var hotUpdate, hotUpdateNewHash;
/******/ 	
/******/ 	function toModuleId(id) {
/******/ 		var isNumber = (+id) + "" === id;
/******/ 		return isNumber ? +id : id;
/******/ 	}
/******/ 	
/******/ 	function hotCheck(apply) {
/******/ 		if(hotStatus !== "idle") throw new Error("check() is only allowed in idle status");
/******/ 		hotApplyOnUpdate = apply;
/******/ 		hotSetStatus("check");
/******/ 		return hotDownloadManifest().then(function(update) {
/******/ 			if(!update) {
/******/ 				hotSetStatus("idle");
/******/ 				return null;
/******/ 			}
/******/ 			hotRequestedFilesMap = {};
/******/ 			hotWaitingFilesMap = {};
/******/ 			hotAvailableFilesMap = update.c;
/******/ 			hotUpdateNewHash = update.h;
/******/ 	
/******/ 			hotSetStatus("prepare");
/******/ 			var promise = new Promise(function(resolve, reject) {
/******/ 				hotDeferred = {
/******/ 					resolve: resolve,
/******/ 					reject: reject
/******/ 				};
/******/ 			});
/******/ 			hotUpdate = {};
/******/ 			var chunkId = 10;
/******/ 			{ // eslint-disable-line no-lone-blocks
/******/ 				/*globals chunkId */
/******/ 				hotEnsureUpdateChunk(chunkId);
/******/ 			}
/******/ 			if(hotStatus === "prepare" && hotChunksLoading === 0 && hotWaitingFiles === 0) {
/******/ 				hotUpdateDownloaded();
/******/ 			}
/******/ 			return promise;
/******/ 		});
/******/ 	}
/******/ 	
/******/ 	function hotAddUpdateChunk(chunkId, moreModules) { // eslint-disable-line no-unused-vars
/******/ 		if(!hotAvailableFilesMap[chunkId] || !hotRequestedFilesMap[chunkId])
/******/ 			return;
/******/ 		hotRequestedFilesMap[chunkId] = false;
/******/ 		for(var moduleId in moreModules) {
/******/ 			if(Object.prototype.hasOwnProperty.call(moreModules, moduleId)) {
/******/ 				hotUpdate[moduleId] = moreModules[moduleId];
/******/ 			}
/******/ 		}
/******/ 		if(--hotWaitingFiles === 0 && hotChunksLoading === 0) {
/******/ 			hotUpdateDownloaded();
/******/ 		}
/******/ 	}
/******/ 	
/******/ 	function hotEnsureUpdateChunk(chunkId) {
/******/ 		if(!hotAvailableFilesMap[chunkId]) {
/******/ 			hotWaitingFilesMap[chunkId] = true;
/******/ 		} else {
/******/ 			hotRequestedFilesMap[chunkId] = true;
/******/ 			hotWaitingFiles++;
/******/ 			hotDownloadUpdateChunk(chunkId);
/******/ 		}
/******/ 	}
/******/ 	
/******/ 	function hotUpdateDownloaded() {
/******/ 		hotSetStatus("ready");
/******/ 		var deferred = hotDeferred;
/******/ 		hotDeferred = null;
/******/ 		if(!deferred) return;
/******/ 		if(hotApplyOnUpdate) {
/******/ 			hotApply(hotApplyOnUpdate).then(function(result) {
/******/ 				deferred.resolve(result);
/******/ 			}, function(err) {
/******/ 				deferred.reject(err);
/******/ 			});
/******/ 		} else {
/******/ 			var outdatedModules = [];
/******/ 			for(var id in hotUpdate) {
/******/ 				if(Object.prototype.hasOwnProperty.call(hotUpdate, id)) {
/******/ 					outdatedModules.push(toModuleId(id));
/******/ 				}
/******/ 			}
/******/ 			deferred.resolve(outdatedModules);
/******/ 		}
/******/ 	}
/******/ 	
/******/ 	function hotApply(options) {
/******/ 		if(hotStatus !== "ready") throw new Error("apply() is only allowed in ready status");
/******/ 		options = options || {};
/******/ 	
/******/ 		var cb;
/******/ 		var i;
/******/ 		var j;
/******/ 		var module;
/******/ 		var moduleId;
/******/ 	
/******/ 		function getAffectedStuff(updateModuleId) {
/******/ 			var outdatedModules = [updateModuleId];
/******/ 			var outdatedDependencies = {};
/******/ 	
/******/ 			var queue = outdatedModules.slice().map(function(id) {
/******/ 				return {
/******/ 					chain: [id],
/******/ 					id: id
/******/ 				};
/******/ 			});
/******/ 			while(queue.length > 0) {
/******/ 				var queueItem = queue.pop();
/******/ 				var moduleId = queueItem.id;
/******/ 				var chain = queueItem.chain;
/******/ 				module = installedModules[moduleId];
/******/ 				if(!module || module.hot._selfAccepted)
/******/ 					continue;
/******/ 				if(module.hot._selfDeclined) {
/******/ 					return {
/******/ 						type: "self-declined",
/******/ 						chain: chain,
/******/ 						moduleId: moduleId
/******/ 					};
/******/ 				}
/******/ 				if(module.hot._main) {
/******/ 					return {
/******/ 						type: "unaccepted",
/******/ 						chain: chain,
/******/ 						moduleId: moduleId
/******/ 					};
/******/ 				}
/******/ 				for(var i = 0; i < module.parents.length; i++) {
/******/ 					var parentId = module.parents[i];
/******/ 					var parent = installedModules[parentId];
/******/ 					if(!parent) continue;
/******/ 					if(parent.hot._declinedDependencies[moduleId]) {
/******/ 						return {
/******/ 							type: "declined",
/******/ 							chain: chain.concat([parentId]),
/******/ 							moduleId: moduleId,
/******/ 							parentId: parentId
/******/ 						};
/******/ 					}
/******/ 					if(outdatedModules.indexOf(parentId) >= 0) continue;
/******/ 					if(parent.hot._acceptedDependencies[moduleId]) {
/******/ 						if(!outdatedDependencies[parentId])
/******/ 							outdatedDependencies[parentId] = [];
/******/ 						addAllToSet(outdatedDependencies[parentId], [moduleId]);
/******/ 						continue;
/******/ 					}
/******/ 					delete outdatedDependencies[parentId];
/******/ 					outdatedModules.push(parentId);
/******/ 					queue.push({
/******/ 						chain: chain.concat([parentId]),
/******/ 						id: parentId
/******/ 					});
/******/ 				}
/******/ 			}
/******/ 	
/******/ 			return {
/******/ 				type: "accepted",
/******/ 				moduleId: updateModuleId,
/******/ 				outdatedModules: outdatedModules,
/******/ 				outdatedDependencies: outdatedDependencies
/******/ 			};
/******/ 		}
/******/ 	
/******/ 		function addAllToSet(a, b) {
/******/ 			for(var i = 0; i < b.length; i++) {
/******/ 				var item = b[i];
/******/ 				if(a.indexOf(item) < 0)
/******/ 					a.push(item);
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// at begin all updates modules are outdated
/******/ 		// the "outdated" status can propagate to parents if they don't accept the children
/******/ 		var outdatedDependencies = {};
/******/ 		var outdatedModules = [];
/******/ 		var appliedUpdate = {};
/******/ 	
/******/ 		var warnUnexpectedRequire = function warnUnexpectedRequire() {
/******/ 			console.warn("[HMR] unexpected require(" + result.moduleId + ") to disposed module");
/******/ 		};
/******/ 	
/******/ 		for(var id in hotUpdate) {
/******/ 			if(Object.prototype.hasOwnProperty.call(hotUpdate, id)) {
/******/ 				moduleId = toModuleId(id);
/******/ 				var result;
/******/ 				if(hotUpdate[id]) {
/******/ 					result = getAffectedStuff(moduleId);
/******/ 				} else {
/******/ 					result = {
/******/ 						type: "disposed",
/******/ 						moduleId: id
/******/ 					};
/******/ 				}
/******/ 				var abortError = false;
/******/ 				var doApply = false;
/******/ 				var doDispose = false;
/******/ 				var chainInfo = "";
/******/ 				if(result.chain) {
/******/ 					chainInfo = "\nUpdate propagation: " + result.chain.join(" -> ");
/******/ 				}
/******/ 				switch(result.type) {
/******/ 					case "self-declined":
/******/ 						if(options.onDeclined)
/******/ 							options.onDeclined(result);
/******/ 						if(!options.ignoreDeclined)
/******/ 							abortError = new Error("Aborted because of self decline: " + result.moduleId + chainInfo);
/******/ 						break;
/******/ 					case "declined":
/******/ 						if(options.onDeclined)
/******/ 							options.onDeclined(result);
/******/ 						if(!options.ignoreDeclined)
/******/ 							abortError = new Error("Aborted because of declined dependency: " + result.moduleId + " in " + result.parentId + chainInfo);
/******/ 						break;
/******/ 					case "unaccepted":
/******/ 						if(options.onUnaccepted)
/******/ 							options.onUnaccepted(result);
/******/ 						if(!options.ignoreUnaccepted)
/******/ 							abortError = new Error("Aborted because " + moduleId + " is not accepted" + chainInfo);
/******/ 						break;
/******/ 					case "accepted":
/******/ 						if(options.onAccepted)
/******/ 							options.onAccepted(result);
/******/ 						doApply = true;
/******/ 						break;
/******/ 					case "disposed":
/******/ 						if(options.onDisposed)
/******/ 							options.onDisposed(result);
/******/ 						doDispose = true;
/******/ 						break;
/******/ 					default:
/******/ 						throw new Error("Unexception type " + result.type);
/******/ 				}
/******/ 				if(abortError) {
/******/ 					hotSetStatus("abort");
/******/ 					return Promise.reject(abortError);
/******/ 				}
/******/ 				if(doApply) {
/******/ 					appliedUpdate[moduleId] = hotUpdate[moduleId];
/******/ 					addAllToSet(outdatedModules, result.outdatedModules);
/******/ 					for(moduleId in result.outdatedDependencies) {
/******/ 						if(Object.prototype.hasOwnProperty.call(result.outdatedDependencies, moduleId)) {
/******/ 							if(!outdatedDependencies[moduleId])
/******/ 								outdatedDependencies[moduleId] = [];
/******/ 							addAllToSet(outdatedDependencies[moduleId], result.outdatedDependencies[moduleId]);
/******/ 						}
/******/ 					}
/******/ 				}
/******/ 				if(doDispose) {
/******/ 					addAllToSet(outdatedModules, [result.moduleId]);
/******/ 					appliedUpdate[moduleId] = warnUnexpectedRequire;
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// Store self accepted outdated modules to require them later by the module system
/******/ 		var outdatedSelfAcceptedModules = [];
/******/ 		for(i = 0; i < outdatedModules.length; i++) {
/******/ 			moduleId = outdatedModules[i];
/******/ 			if(installedModules[moduleId] && installedModules[moduleId].hot._selfAccepted)
/******/ 				outdatedSelfAcceptedModules.push({
/******/ 					module: moduleId,
/******/ 					errorHandler: installedModules[moduleId].hot._selfAccepted
/******/ 				});
/******/ 		}
/******/ 	
/******/ 		// Now in "dispose" phase
/******/ 		hotSetStatus("dispose");
/******/ 		Object.keys(hotAvailableFilesMap).forEach(function(chunkId) {
/******/ 			if(hotAvailableFilesMap[chunkId] === false) {
/******/ 				hotDisposeChunk(chunkId);
/******/ 			}
/******/ 		});
/******/ 	
/******/ 		var idx;
/******/ 		var queue = outdatedModules.slice();
/******/ 		while(queue.length > 0) {
/******/ 			moduleId = queue.pop();
/******/ 			module = installedModules[moduleId];
/******/ 			if(!module) continue;
/******/ 	
/******/ 			var data = {};
/******/ 	
/******/ 			// Call dispose handlers
/******/ 			var disposeHandlers = module.hot._disposeHandlers;
/******/ 			for(j = 0; j < disposeHandlers.length; j++) {
/******/ 				cb = disposeHandlers[j];
/******/ 				cb(data);
/******/ 			}
/******/ 			hotCurrentModuleData[moduleId] = data;
/******/ 	
/******/ 			// disable module (this disables requires from this module)
/******/ 			module.hot.active = false;
/******/ 	
/******/ 			// remove module from cache
/******/ 			delete installedModules[moduleId];
/******/ 	
/******/ 			// remove "parents" references from all children
/******/ 			for(j = 0; j < module.children.length; j++) {
/******/ 				var child = installedModules[module.children[j]];
/******/ 				if(!child) continue;
/******/ 				idx = child.parents.indexOf(moduleId);
/******/ 				if(idx >= 0) {
/******/ 					child.parents.splice(idx, 1);
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// remove outdated dependency from module children
/******/ 		var dependency;
/******/ 		var moduleOutdatedDependencies;
/******/ 		for(moduleId in outdatedDependencies) {
/******/ 			if(Object.prototype.hasOwnProperty.call(outdatedDependencies, moduleId)) {
/******/ 				module = installedModules[moduleId];
/******/ 				if(module) {
/******/ 					moduleOutdatedDependencies = outdatedDependencies[moduleId];
/******/ 					for(j = 0; j < moduleOutdatedDependencies.length; j++) {
/******/ 						dependency = moduleOutdatedDependencies[j];
/******/ 						idx = module.children.indexOf(dependency);
/******/ 						if(idx >= 0) module.children.splice(idx, 1);
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// Not in "apply" phase
/******/ 		hotSetStatus("apply");
/******/ 	
/******/ 		hotCurrentHash = hotUpdateNewHash;
/******/ 	
/******/ 		// insert new code
/******/ 		for(moduleId in appliedUpdate) {
/******/ 			if(Object.prototype.hasOwnProperty.call(appliedUpdate, moduleId)) {
/******/ 				modules[moduleId] = appliedUpdate[moduleId];
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// call accept handlers
/******/ 		var error = null;
/******/ 		for(moduleId in outdatedDependencies) {
/******/ 			if(Object.prototype.hasOwnProperty.call(outdatedDependencies, moduleId)) {
/******/ 				module = installedModules[moduleId];
/******/ 				moduleOutdatedDependencies = outdatedDependencies[moduleId];
/******/ 				var callbacks = [];
/******/ 				for(i = 0; i < moduleOutdatedDependencies.length; i++) {
/******/ 					dependency = moduleOutdatedDependencies[i];
/******/ 					cb = module.hot._acceptedDependencies[dependency];
/******/ 					if(callbacks.indexOf(cb) >= 0) continue;
/******/ 					callbacks.push(cb);
/******/ 				}
/******/ 				for(i = 0; i < callbacks.length; i++) {
/******/ 					cb = callbacks[i];
/******/ 					try {
/******/ 						cb(moduleOutdatedDependencies);
/******/ 					} catch(err) {
/******/ 						if(options.onErrored) {
/******/ 							options.onErrored({
/******/ 								type: "accept-errored",
/******/ 								moduleId: moduleId,
/******/ 								dependencyId: moduleOutdatedDependencies[i],
/******/ 								error: err
/******/ 							});
/******/ 						}
/******/ 						if(!options.ignoreErrored) {
/******/ 							if(!error)
/******/ 								error = err;
/******/ 						}
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// Load self accepted modules
/******/ 		for(i = 0; i < outdatedSelfAcceptedModules.length; i++) {
/******/ 			var item = outdatedSelfAcceptedModules[i];
/******/ 			moduleId = item.module;
/******/ 			hotCurrentParents = [moduleId];
/******/ 			try {
/******/ 				__webpack_require__(moduleId);
/******/ 			} catch(err) {
/******/ 				if(typeof item.errorHandler === "function") {
/******/ 					try {
/******/ 						item.errorHandler(err);
/******/ 					} catch(err2) {
/******/ 						if(options.onErrored) {
/******/ 							options.onErrored({
/******/ 								type: "self-accept-error-handler-errored",
/******/ 								moduleId: moduleId,
/******/ 								error: err2,
/******/ 								orginalError: err
/******/ 							});
/******/ 						}
/******/ 						if(!options.ignoreErrored) {
/******/ 							if(!error)
/******/ 								error = err2;
/******/ 						}
/******/ 						if(!error)
/******/ 							error = err;
/******/ 					}
/******/ 				} else {
/******/ 					if(options.onErrored) {
/******/ 						options.onErrored({
/******/ 							type: "self-accept-errored",
/******/ 							moduleId: moduleId,
/******/ 							error: err
/******/ 						});
/******/ 					}
/******/ 					if(!options.ignoreErrored) {
/******/ 						if(!error)
/******/ 							error = err;
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// handle errors in accept handlers and self accepted module load
/******/ 		if(error) {
/******/ 			hotSetStatus("fail");
/******/ 			return Promise.reject(error);
/******/ 		}
/******/ 	
/******/ 		hotSetStatus("idle");
/******/ 		return new Promise(function(resolve) {
/******/ 			resolve(outdatedModules);
/******/ 		});
/******/ 	}
/******/
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
/******/ 			exports: {},
/******/ 			hot: hotCreateModule(moduleId),
/******/ 			parents: (hotCurrentParentsTemp = hotCurrentParents, hotCurrentParents = [], hotCurrentParentsTemp),
/******/ 			children: []
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, hotCreateRequire(moduleId));
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
/******/ 	// __webpack_hash__
/******/ 	__webpack_require__.h = function() { return hotCurrentHash; };
/******/
/******/ 	// Load entry module and return exports
/******/ 	return hotCreateRequire(500)(__webpack_require__.s = 500);
/******/ })
/************************************************************************/
/******/ ({

/***/ 249:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__components_module_card__ = __webpack_require__(51);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__controller__ = __webpack_require__(299);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__loader__ = __webpack_require__(300);
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





var $ = window.$;

$(function () {
  var moduleCardController = new __WEBPACK_IMPORTED_MODULE_0__components_module_card__["a" /* default */]();
  new __WEBPACK_IMPORTED_MODULE_2__loader__["a" /* default */]();
  new __WEBPACK_IMPORTED_MODULE_1__controller__["a" /* default */](moduleCardController);
});

/***/ }),

/***/ 299:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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
      var self = this;
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

      if ($(bulkActionSelectedSelector).length <= 0) {
        console.warn(window.translate_javascripts['Bulk Action - One module minimum']);
        return false;
      }

      var bulkModulesTechNames = [];
      var moduleTechName = void 0;
      $(bulkActionSelectedSelector).each(function bulkActionSelector() {
        moduleTechName = $(this).data('tech-name');
        bulkModulesTechNames.push({
          techName: moduleTechName,
          actionMenuObj: $(this).closest('.module-checkbox-bulk-list').next()
        });
      });

      var actionMenuObj = void 0;
      var urlActionSegment = void 0;
      var urlElement = void 0;
      $.each(bulkModulesTechNames, function bulkModulesLoop(index, data) {
        actionMenuObj = data.actionMenuObj;
        moduleTechName = data.techName;

        urlActionSegment = bulkActionToUrl[requestedBulkAction];

        if (typeof self.moduleCardController !== 'undefined') {
          // We use jQuery to get the specific link for this action. If found, we send it.
          urlElement = $(self.moduleCardController.moduleActionMenuLinkSelector + urlActionSegment, actionMenuObj);

          if (urlElement.length > 0) {
            self.moduleCardController._requestToController(urlActionSegment, urlElement, forceDeletion);
          } else {
            $.growl.error({ message: window.translate_javascripts['Bulk Action - Request not available for module'].replace('[1]', urlActionSegment).replace('[2]', moduleTechName) });
          }
        }
      });

      return true;
    }
  }, {
    key: 'initActionButtons',
    value: function initActionButtons() {
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
        $(self.upgradeAllTargets).click();
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
      var _this = this;

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
        window.open($(_this).attr('href'), '_blank');
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

/* harmony default export */ __webpack_exports__["a"] = (AdminModuleController);

/***/ }),

/***/ 300:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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

/* harmony default export */ __webpack_exports__["a"] = (ModuleLoader);

/***/ }),

/***/ 500:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(249);


/***/ }),

/***/ 51:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2018 PrestaShop
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
    value: function _requestToController(action, element, forceDeletion) {
      var self = this;
      var jqElementObj = element.closest(this.moduleItemActionsSelector);
      var form = element.closest("form");
      var spinnerObj = $("<button class=\"btn-primary-reverse onclick unbind spinner \"></button>");
      var url = "//" + window.location.host + form.attr("action");
      var actionParams = form.serializeArray();

      if (forceDeletion === "true" || forceDeletion === true) {
        actionParams.push({ name: "actionParams[deletion]", value: true });
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

            var alteredSelector = null;
            var mainElement = null;

            if (action == "uninstall") {
              jqElementObj.fadeOut(function () {
                alteredSelector = self._getModuleItemSelector().replace('.', '');
                mainElement = jqElementObj.parents('.' + alteredSelector).first();
                mainElement.remove();
              });

              BOEvent.emitEvent("Module Uninstalled", "CustomEvent");
            } else if (action == "disable") {

              alteredSelector = self._getModuleItemSelector().replace('.', '');
              mainElement = jqElementObj.parents('.' + alteredSelector).first();
              mainElement.addClass(alteredSelector + '-isNotActive');
              mainElement.attr('data-active', '0');

              BOEvent.emitEvent("Module Disabled", "CustomEvent");
            } else if (action == "enable") {
              alteredSelector = self._getModuleItemSelector().replace('.', '');

              mainElement = jqElementObj.parents('.' + alteredSelector).first();
              mainElement.removeClass(alteredSelector + '-isNotActive');
              mainElement.attr('data-active', '1');

              BOEvent.emitEvent("Module Enabled", "CustomEvent");
            }

            jqElementObj.replaceWith(result[moduleTechName].action_menu_html);
          }
        }
      }).always(function () {
        jqElementObj.fadeIn();
        spinnerObj.remove();
      });

      return false;
    }
  }]);

  return ModuleCard;
}();

/* harmony default export */ __webpack_exports__["a"] = (ModuleCard);

/***/ })

/******/ });
>>>>>>> 9e2c9a6ce2... finalize UI for 500 error
>>>>>>> 603f702084... finalize UI for 500 error
