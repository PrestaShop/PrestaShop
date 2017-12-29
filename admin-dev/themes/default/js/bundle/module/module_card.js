var module_card_controller = {};

$(document).ready(function () {

    module_card_controller = new AdminModuleCard();
    module_card_controller.init();

});

/**
 * AdminModule card Controller.
 * @constructor
 */
var AdminModuleCard = function () {
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

    /* Selectors only for modal buttons */
    this.moduleActionModalDisableLinkSelector = 'a.module_action_modal_disable';
    this.moduleActionModalResetLinkSelector = 'a.module_action_modal_reset';
    this.moduleActionModalUninstallLinkSelector = 'a.module_action_modal_uninstall';
    this.forceDeletionOption = '#force_deletion';

    /**
     * Initialize all listeners and bind everything
     * @method init
     * @memberof AdminModuleCard
     */
    this.init = function () {
        this.initActionButtons();
    };

    this.getModuleItemSelector = function () {
        if ($(this.moduleItemListSelector).length) {
            return this.moduleItemListSelector;
        } else {
            return this.moduleItemGridSelector;
        }
    };

    this.confirmAction = function(action, element) {
        var modal = $('#' + $(element).data('confirm_modal'));
        if (modal.length != 1) {
            return true;
        }
        modal.first().modal('show');
        return false; // do not allow a.href to reload the page. The confirm modal dialog will do it async if needed.
    };

    /**
     * Update the content of a modal asking a confirmation for PrestaTrust and open it
     * 
     * @param {array} result containing module data
     * @return {void}
     */
    this.confirmPrestaTrust = function confirmPrestaTrust(result) {
        var that = this;
        var modal = this.replacePrestaTrustPlaceholders(result);
        modal.find(".pstrust-install").off('click').on('click', function() {
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
    };
    
    this.replacePrestaTrustPlaceholders = function replacePrestaTrustPlaceholders(result) {
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
        
        modal.find("#pstrust-img").attr({src: module.img, alt: module.name});
        modal.find("#pstrust-name").text(module.displayName);
        modal.find("#pstrust-author").text(module.author);
        modal.find("#pstrust-label").attr("class", "text-" + alertClass).text(module.prestatrust.status ? 'OK' : 'KO');
        modal.find("#pstrust-message").attr("class", "alert alert-"+alertClass);
        modal.find("#pstrust-message > p").text(module.prestatrust.message);
        
        return modal;
    }

    this.dispatchPreEvent = function (action, element) {
        var event = jQuery.Event('module_card_action_event');
        $(element).trigger(event, [action]);
        if (event.isPropagationStopped() !== false || event.isImmediatePropagationStopped() !== false) {
            return false; // if all handlers have not been called, then stop propagation of the click event.
        }
        return (event.result !== false); // explicit false must be set from handlers to stop propagation of the click event.
    };

    this.initActionButtons = function () {
        var _this = this;

        $(document).on('click', this.forceDeletionOption, function () {
            var btn = $(_this.moduleActionModalUninstallLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']"));
            if($(this).prop('checked') === true) {
              btn.attr('data-deletion', 'true');
            }else {
              btn.removeAttr('data-deletion');
            }
        });

        $(document).on('click', this.moduleActionMenuInstallLinkSelector, function () {
            if ($("#modal-prestatrust").length) {
                $("#modal-prestatrust").modal('hide');
            }
            return _this.dispatchPreEvent('install', this) && _this.confirmAction('install', this) && _this.requestToController('install', $(this));
        });
        $(document).on('click', this.moduleActionMenuEnableLinkSelector, function () {
            return _this.dispatchPreEvent('enable', this) && _this.confirmAction('enable', this) && _this.requestToController('enable', $(this));
        });
        $(document).on('click', this.moduleActionMenuUninstallLinkSelector, function () {
            return _this.dispatchPreEvent('uninstall', this) && _this.confirmAction('uninstall', this) && _this.requestToController('uninstall', $(this));
        });
        $(document).on('click', this.moduleActionMenuDisableLinkSelector, function () {
            return _this.dispatchPreEvent('disable', this) && _this.confirmAction('disable', this) && _this.requestToController('disable', $(this));
        });
        $(document).on('click', this.moduleActionMenuEnableMobileLinkSelector, function () {
            return _this.dispatchPreEvent('enable_mobile', this) && _this.confirmAction('enable_mobile', this) && _this.requestToController('enable_mobile', $(this));
        });
        $(document).on('click', this.moduleActionMenuDisableMobileLinkSelector, function () {
            return _this.dispatchPreEvent('disable_mobile', this) && _this.confirmAction('disable_mobile', this) && _this.requestToController('disable_mobile', $(this));
        });
        $(document).on('click', this.moduleActionMenuResetLinkSelector, function () {
            return _this.dispatchPreEvent('reset', this) && _this.confirmAction('reset', this) && _this.requestToController('reset', $(this));
        });
        $(document).on('click', this.moduleActionMenuUpdateLinkSelector, function () {
            return _this.dispatchPreEvent('update', this) && _this.confirmAction('update', this) && _this.requestToController('update', $(this));
        });

        $(document).on('click', this.moduleActionModalDisableLinkSelector, function () {
            return _this.requestToController('disable', $(_this.moduleActionMenuDisableLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']")));
        });
        $(document).on('click', this.moduleActionModalResetLinkSelector, function () {
            return _this.requestToController('reset', $(_this.moduleActionMenuResetLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']")));
        });
        $(document).on('click', this.moduleActionModalUninstallLinkSelector, function (e) {
            jQuery(e.target).parents('.modal').on('hidden.bs.modal', function(event) {
                return _this.requestToController(
                    'uninstall',
                    jQuery(
                        _this.moduleActionMenuUninstallLinkSelector,
                        jQuery("div.module-item-list[data-tech-name='" + jQuery(e.target).attr("data-tech-name") + "']")
                    ),
                    jQuery(e.target).attr("data-deletion")
                );
            }.bind(e));
        });
    };

    this.requestToController = function (action, element, forceDeletion) {
        var _this = this;
        var jqElementObj = element.closest("div.btn-group");
        var form = element.closest("form");
        var spinnerObj = $("<button class=\"btn-primary-reverse onclick unbind spinner \"></button>");
        var url = "//" + window.location.host + form.attr("action");
        var actionParams = form.serializeArray();

        if (forceDeletion === "true" || forceDeletion === true) {
          actionParams.push({name: "actionParams[deletion]", value: true});
        }

        $.ajax({
            url: url,
            dataType: 'json',
            method: 'POST',
            data: actionParams,
            beforeSend: function () {
                jqElementObj.hide();
                jqElementObj.after(spinnerObj);
            }
        }).done(function (result) {
            if (typeof result === undefined) {
                $.growl.error({message: "No answer received from server"});
            } else {
                var moduleTechName = Object.keys(result)[0];
                if (result[moduleTechName].status === false) {
                    if (typeof result[moduleTechName].confirmation_subject !== 'undefined') {
                        _this.confirmPrestaTrust(result[moduleTechName]);
                    }
                    $.growl.error({message: result[moduleTechName].msg});
                } else {
                    $.growl.notice({message: result[moduleTechName].msg});
                    var alteredSelector = null;
                    var mainElement = null;
                    if (action == "uninstall") {
                        jqElementObj.fadeOut(function() {
                            alteredSelector = _this.getModuleItemSelector().replace('.', '');
                            mainElement = jqElementObj.parents('.' + alteredSelector).first();
                            mainElement.remove();
                        });
                        BOEvent.emitEvent("Module Uninstalled", "CustomEvent");
                    } else if (action == "disable") {
                        alteredSelector = _this.getModuleItemSelector().replace('.', '');
                        mainElement = jqElementObj.parents('.' + alteredSelector).first();
                        mainElement.addClass(alteredSelector + '-isNotActive');
                        mainElement.attr('data-active', '0');
                        BOEvent.emitEvent("Module Disabled", "CustomEvent");
                    } else if (action == "enable") {
                        alteredSelector = _this.getModuleItemSelector().replace('.', '');
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
    };

};
