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
    this.moduleActionMenuLinkSelector = 'a.module_action_menu_';
    this.moduleActionMenuInstallLinkSelector = 'a.module_action_menu_install';
    this.moduleActionMenuEnableLinkSelector = 'a.module_action_menu_enable';
    this.moduleActionMenuUninstallLinkSelector = 'a.module_action_menu_uninstall';
    this.moduleActionMenuDisableLinkSelector = 'a.module_action_menu_disable';
    this.moduleActionMenuResetLinkSelector = 'a.module_action_menu_reset';
    this.moduleActionMenuUpdateLinkSelector = 'a.module_action_menu_update';

    /* Selectors only for modal buttons */
    this.moduleActionModalDisableLinkSelector = 'a.module_action_modal_disable';
    this.moduleActionModalResetLinkSelector = 'a.module_action_modal_reset';
    this.moduleActionModalUninstallLinkSelector = 'a.module_action_modal_uninstall';

    /**
     * Initialize all listeners and bind everything
     * @method init
     * @memberof AdminModuleCard
     */
    this.init = function () {
        this.initActionButtons();
    };

    this.initActionButtons = function () {
        // action buttons on a module card
        var confirmAction = function (action, element) {
            var modal = $('#' + $(element).data('confirm_modal'));
            if (modal.length != 1) {
                return true;
            }
            modal.first().modal('show');
            ;
            return false; // do not allow a.href to reload the page. The confirm modal dialog will do it async if needed.
        };
        var dispatchPreEvent = function (action, element) {
            var event = jQuery.Event('module_card_action_event');
            $(element).trigger(event, [action]);
            if (event.isPropagationStopped() !== false || event.isImmediatePropagationStopped() !== false) {
                return false; // if all handlers have not been called, then stop propagation of the click event.
            }
            return (event.result !== false); // explicit false must be set from handlers to stop propagation of the click event.
        };

        $(document).on('click', this.moduleActionMenuInstallLinkSelector, function () {
            return dispatchPreEvent('install', this) && confirmAction('install', this) && module_card_controller.requestToController('install', $(this));
        });
        $(document).on('click', this.moduleActionMenuEnableLinkSelector, function () {
            return dispatchPreEvent('enable', this) && confirmAction('enable', this) && module_card_controller.requestToController('enable', $(this));
        });
        $(document).on('click', this.moduleActionMenuUninstallLinkSelector, function () {
            return dispatchPreEvent('uninstall', this) && confirmAction('uninstall', this) && module_card_controller.requestToController('uninstall', $(this));
        });
        $(document).on('click', this.moduleActionMenuDisableLinkSelector, function () {
            return dispatchPreEvent('disable', this) && confirmAction('disable', this) && module_card_controller.requestToController('disable', $(this));
        });
        $(document).on('click', this.moduleActionMenuResetLinkSelector, function () {
            return dispatchPreEvent('reset', this) && confirmAction('reset', this) && module_card_controller.requestToController('reset', $(this));
        });
        $(document).on('click', this.moduleActionMenuUpdateLinkSelector, function () {
            return dispatchPreEvent('update', this) && confirmAction('update', this) && module_card_controller.requestToController('update', $(this));
        });

        $(document).on('click', this.moduleActionModalDisableLinkSelector, function () {
            return module_card_controller.requestToController('disable', $(module_card_controller.moduleActionMenuDisableLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']")));
        });
        $(document).on('click', this.moduleActionModalResetLinkSelector, function () {
            return module_card_controller.requestToController('reset', $(module_card_controller.moduleActionMenuResetLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']")));
        });
        $(document).on('click', this.moduleActionModalUninstallLinkSelector, function () {
            return module_card_controller.requestToController('uninstall', $(module_card_controller.moduleActionMenuUninstallLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']")));
        });
    };

    this.requestToController = function (action, element) {
        var jqElementObj = element.closest(".btn-group");
        var spinnerObj = $("<button class=\"btn btn-primary-reverse btn-lg onclic unbind pull-right\"></button>");
        $.ajax({
            url: "//" + window.location.hostname + element.attr("href"),
            dataType: 'json',
            beforeSend: function () {
                jqElementObj.hide();
                jqElementObj.after(spinnerObj);
            }
        }).done(function (result) {
            if (typeof result === undefined) {
                $.growl.error({message: "No answer received from server"});
            } else {
                var moduleTechName = Object.keys(result)[0];
                if (result[moduleTechName].status == false) {
                    $.growl.error({message: result[moduleTechName].msg});
                } else {
                    $.growl.notice({message: result[moduleTechName].msg});
                    if (action != "uninstall") {
                        jqElementObj.html(result[moduleTechName].action_menu_html);
                    } else {
                        jqElementObj.html("");
                    }
                }
            }
        }).always(function () {
            jqElementObj.fadeIn();
            spinnerObj.remove();
        });
        return false;
    };

};
