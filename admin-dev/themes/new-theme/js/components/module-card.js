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

const BOEvent = {
  on(eventName, callback, context) {
    document.addEventListener(eventName, (event) => {
      if (typeof context !== 'undefined') {
        callback.call(context, event);
      } else {
        callback(event);
      }
    });
  },

  emitEvent(eventName, eventType) {
    const event = document.createEvent(eventType);
    // true values stand for: can bubble, and is cancellable
    event.initEvent(eventName, true, true);
    document.dispatchEvent(event);
  },
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
      const btn = $(
        self.moduleActionModalUninstallLinkSelector,
        $(`div.module-item-list[data-tech-name='${$(this).attr('data-tech-name')}']`),
      );

      if ($(this).prop('checked') === true) {
        btn.attr('data-deletion', 'true');
      } else {
        btn.removeAttr('data-deletion');
      }
    });

    $(document).on('click', this.moduleActionMenuInstallLinkSelector, function () {
      if ($('#modal-prestatrust').length) {
        $('#modal-prestatrust').modal('hide');
      }

      return (
        self.dispatchPreEvent('install', this)
        && self.confirmAction('install', this)
        && self.requestToController('install', $(this))
      );
    });

    $(document).on('click', this.moduleActionMenuEnableLinkSelector, function () {
      return (
        self.dispatchPreEvent('enable', this)
        && self.confirmAction('enable', this)
        && self.requestToController('enable', $(this))
      );
    });

    $(document).on('click', this.moduleActionMenuUninstallLinkSelector, function () {
      return (
        self.dispatchPreEvent('uninstall', this)
        && self.confirmAction('uninstall', this)
        && self.requestToController('uninstall', $(this))
      );
    });

    $(document).on('click', this.moduleActionMenuDisableLinkSelector, function () {
      return (
        self.dispatchPreEvent('disable', this)
        && self.confirmAction('disable', this)
        && self.requestToController('disable', $(this))
      );
    });

    $(document).on('click', this.moduleActionMenuEnableMobileLinkSelector, function () {
      return (
        self.dispatchPreEvent('enable_mobile', this)
        && self.confirmAction('enable_mobile', this)
        && self.requestToController('enable_mobile', $(this))
      );
    });

    $(document).on('click', this.moduleActionMenuDisableMobileLinkSelector, function () {
      return (
        self.dispatchPreEvent('disable_mobile', this)
        && self.confirmAction('disable_mobile', this)
        && self.requestToController('disable_mobile', $(this))
      );
    });

    $(document).on('click', this.moduleActionMenuResetLinkSelector, function () {
      return (
        self.dispatchPreEvent('reset', this)
        && self.confirmAction('reset', this)
        && self.requestToController('reset', $(this))
      );
    });

    $(document).on('click', this.moduleActionMenuUpdateLinkSelector, function (event) {
      event.preventDefault();
      const modal = $(`#${$(this).data('confirm_modal')}`);
      const isMaintenanceMode = window.isShopMaintenance;

      if (modal.length !== 1) {
        // Modal body element
        const maintenanceLink = document.createElement('a');
        maintenanceLink.classList.add('btn', 'btn-primary', 'btn-lg');
        maintenanceLink.setAttribute('href', window.moduleURLs.maintenancePage);
        maintenanceLink.innerHTML = window.moduleTranslations.moduleModalUpdateMaintenance;

        const updateConfirmModal = new ConfirmModal(
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

          () => self.dispatchPreEvent('update', this)
            && self.confirmAction('update', this)
            && self.requestToController('update', $(this)),
        );

        updateConfirmModal.show();
      } else {
        return (
          self.dispatchPreEvent('update', this)
          && self.confirmAction('update', this)
          && self.requestToController('update', $(this))
        );
      }

      return false;
    });

    $(document).on('click', this.moduleActionModalDisableLinkSelector, function () {
      return self.requestToController(
        'disable',
        $(
          self.moduleActionMenuDisableLinkSelector,
          $(`div.module-item-list[data-tech-name='${$(this).attr('data-tech-name')}']`),
        ),
      );
    });

    $(document).on('click', this.moduleActionModalResetLinkSelector, function () {
      return self.requestToController(
        'reset',
        $(
          self.moduleActionMenuResetLinkSelector,
          $(`div.module-item-list[data-tech-name='${$(this).attr('data-tech-name')}']`),
        ),
      );
    });

    $(document).on('click', this.moduleActionModalUninstallLinkSelector, (e) => {
      $(e.target)
        .parents('.modal')
        .on('hidden.bs.modal', () => self.requestToController(
          'uninstall',
          $(
            self.moduleActionMenuUninstallLinkSelector,
            $(`div.module-item-list[data-tech-name='${$(e.target).attr('data-tech-name')}']`),
          ),
          $(e.target).attr('data-deletion'),
        ),
        );
    });
  }

  getModuleItemSelector() {
    if ($(this.moduleItemListSelector).length) {
      return this.moduleItemListSelector;
    }

    return this.moduleItemGridSelector;
  }

  confirmAction(action, element) {
    const modal = $(`#${$(element).data('confirm_modal')}`);

    if (modal.length !== 1) {
      return true;
    }

    modal.first().modal('show');

    return false; // do not allow a.href to reload the page. The confirm modal dialog will do it async if needed.
  }

  /**
   * Update the content of a modal asking a confirmation for PrestaTrust and open it
   *
   * @param {array} result containing module data
   * @return {void}
   */
  confirmPrestaTrust(result) {
    const that = this;
    const modal = this.replacePrestaTrustPlaceholders(result);

    modal
      .find('.pstrust-install')
      .off('click')
      .on('click', () => {
        // Find related form, update it and submit it
        const installButton = $(
          that.moduleActionMenuInstallLinkSelector,
          `.module-item[data-tech-name="${result.module.attributes.name}"]`,
        );

        const form = installButton.parent('form');
        $('<input>')
          .attr({
            type: 'hidden',
            value: '1',
            name: 'actionParams[confirmPrestaTrust]',
          })
          .appendTo(form);

        installButton.click();
        modal.modal('hide');
      });

    modal.modal();
  }

  replacePrestaTrustPlaceholders(result) {
    const modal = $('#modal-prestatrust');
    const module = result.module.attributes;

    if (result.confirmation_subject !== 'PrestaTrust' || !modal.length) {
      return false;
    }

    const alertClass = module.prestatrust.status ? 'success' : 'warning';

    if (module.prestatrust.check_list.property) {
      modal.find('#pstrust-btn-property-ok').show();
      modal.find('#pstrust-btn-property-nok').hide();
    } else {
      modal.find('#pstrust-btn-property-ok').hide();
      modal.find('#pstrust-btn-property-nok').show();
      modal
        .find('#pstrust-buy')
        .attr('href', module.url)
        .toggle(module.url !== null);
    }

    modal.find('#pstrust-img').attr({src: module.img, alt: module.name});
    modal.find('#pstrust-name').text(module.displayName);
    modal.find('#pstrust-author').text(module.author);
    modal
      .find('#pstrust-label')
      .attr('class', `text-${alertClass}`)
      .text(module.prestatrust.status ? 'OK' : 'KO');
    modal.find('#pstrust-message').attr('class', `alert alert-${alertClass}`);
    modal.find('#pstrust-message > p').text(module.prestatrust.message);

    return modal;
  }

  dispatchPreEvent(action, element) {
    const event = jQuery.Event('module_card_action_event');

    $(element).trigger(event, [action]);
    if (event.isPropagationStopped() !== false || event.isImmediatePropagationStopped() !== false) {
      return false; // if all handlers have not been called, then stop propagation of the click event.
    }

    return event.result !== false; // explicit false must be set from handlers to stop propagation of the click event.
  }

  requestToController(action, element, forceDeletion, disableCacheClear, callback) {
    const self = this;
    const jqElementObj = element.closest(this.moduleItemActionsSelector);
    const form = element.closest('form');
    const spinnerObj = $('<button class="btn-primary-reverse onclick unbind spinner "></button>');
    const url = `//${window.location.host}${form.attr('action')}`;
    const actionParams = form.serializeArray();

    if (forceDeletion === 'true' || forceDeletion === true) {
      actionParams.push({name: 'actionParams[deletion]', value: true});
    }
    if (disableCacheClear === 'true' || disableCacheClear === true) {
      actionParams.push({name: 'actionParams[cacheClearEnabled]', value: 0});
    }

    $.ajax({
      url,
      dataType: 'json',
      method: 'POST',
      data: actionParams,
      beforeSend() {
        jqElementObj.hide();
        jqElementObj.after(spinnerObj);
      },
    })
      .done((result) => {
        if (result === undefined) {
          $.growl.error({
            message: 'No answer received from server',
            fixed: true,
          });
          return;
        }

        if (typeof result.status !== 'undefined' && result.status === false) {
          $.growl.error({message: result.msg, fixed: true});
          return;
        }

        const moduleTechName = Object.keys(result)[0];

        if (result[moduleTechName].status === false) {
          if (typeof result[moduleTechName].confirmation_subject !== 'undefined') {
            self.confirmPrestaTrust(result[moduleTechName]);
          }

          $.growl.error({message: result[moduleTechName].msg, fixed: true});
          return;
        }

        $.growl({
          message: result[moduleTechName].msg,
          duration: 6000,
        });

        const alteredSelector = self.getModuleItemSelector().replace('.', '');
        let mainElement = null;

        if (action === 'uninstall') {
          mainElement = jqElementObj.closest(`.${alteredSelector}`);
          mainElement.remove();

          BOEvent.emitEvent('Module Uninstalled', 'CustomEvent');
        } else if (action === 'disable') {
          mainElement = jqElementObj.closest(`.${alteredSelector}`);
          mainElement.addClass(`${alteredSelector}-isNotActive`);
          mainElement.attr('data-active', '0');

          BOEvent.emitEvent('Module Disabled', 'CustomEvent');
        } else if (action === 'enable') {
          mainElement = jqElementObj.closest(`.${alteredSelector}`);
          mainElement.removeClass(`${alteredSelector}-isNotActive`);
          mainElement.attr('data-active', '1');

          BOEvent.emitEvent('Module Enabled', 'CustomEvent');
        }

        jqElementObj.replaceWith(result[moduleTechName].action_menu_html);
      })
      .fail(() => {
        const moduleItem = jqElementObj.closest('module-item-list');
        const techName = moduleItem.data('techName');
        $.growl.error({
          message: `Could not perform action ${action} for module ${techName}`,
          fixed: true,
        });
      })
      .always(() => {
        jqElementObj.fadeIn();
        spinnerObj.remove();
        if (callback) {
          callback();
        }
      });

    return false;
  }
}
