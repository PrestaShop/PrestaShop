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
        $(document).on('click', this.moduleActionModalUninstallLinkSelector, function () {
            return _this.requestToController('uninstall', $(_this.moduleActionMenuUninstallLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']")), $(this).attr("data-deletion"));
        });
    };

    this.requestToController = function (action, element, forceDeletion) {
        var _this = this;
        var jqElementObj = element.closest("div.btn-group");
        var form = element.closest("form");
        var spinnerObj = $("<button class=\"btn-primary-reverse onclick unbind pull-right\"></button>");
        var url = "//" + window.location.host + form.attr("action");

        if (forceDeletion === "true" || forceDeletion === true) {
          url +="&deletion=true";
        }

        $.ajax({
            url: url,
            dataType: 'json',
            method: 'POST',
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
