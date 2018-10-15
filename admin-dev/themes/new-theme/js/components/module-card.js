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

const $ = window.$;

var BOEvent = {
  on: function(eventName, callback, context) {

    document.addEventListener(eventName, function(event) {
      if (typeof context !== 'undefined') {
        callback.call(context, event);
      } else {
        callback(event);
      }
    });
  },

  emitEvent: function(eventName, eventType) {
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
export default class ModuleCard {

  constructor() {
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

  initActionButtons() {
    const self = this;

    $(document).on('click', this.forceDeletionOption, function () {
      const btn = $(self.moduleActionModalUninstallLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']"));
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
      $(e.target).parents('.modal').on('hidden.bs.modal', function(event) {
        return self._requestToController(
          'uninstall',
          $(
            self.moduleActionMenuUninstallLinkSelector,
            $("div.module-item-list[data-tech-name='" + $(e.target).attr("data-tech-name") + "']")
          ),
          $(e.target).attr("data-deletion")
        );
      }.bind(e));
    });
  };

  _getModuleItemSelector() {
    if ($(this.moduleItemListSelector).length) {
      return this.moduleItemListSelector;
    } else {
      return this.moduleItemGridSelector;
    }
  };

  _confirmAction(action, element) {
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
  _confirmPrestaTrust(result) {
    var that = this;
    var modal = this._replacePrestaTrustPlaceholders(result);

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

  _replacePrestaTrustPlaceholders(result) {
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

  _dispatchPreEvent(action, element) {
    var event = jQuery.Event('module_card_action_event');

    $(element).trigger(event, [action]);
    if (event.isPropagationStopped() !== false || event.isImmediatePropagationStopped() !== false) {
      return false; // if all handlers have not been called, then stop propagation of the click event.
    }

    return (event.result !== false); // explicit false must be set from handlers to stop propagation of the click event.
  };

  _requestToController(action, element, forceDeletion) {
    var self = this;
    var jqElementObj = element.closest(this.moduleItemActionsSelector);
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
            self._confirmPrestaTrust(result[moduleTechName]);
          }

          $.growl.error({message: result[moduleTechName].msg});
        } else {
          $.growl.notice({message: result[moduleTechName].msg});

          var alteredSelector = null;
          var mainElement = null;

          if (action == "uninstall") {
            jqElementObj.fadeOut(function() {
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
  };
}
