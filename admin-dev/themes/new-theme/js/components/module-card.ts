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
import ComponentsMap from './components-map';

const ModuleCardMap = ComponentsMap.moduleCard;

const {$} = window;

const BOEvent = {
  on(eventName: string, callback: (event: Event) => void, context: any) {
    document.addEventListener(eventName, (event) => {
      if (typeof context !== 'undefined') {
        callback.call(context, event);
      } else {
        callback(event);
      }
    });
  },

  emitEvent(eventName: string, eventType: string, datas: JQuery) {
    const event = new CustomEvent(eventType, <any>datas);
    // true values stand for: can bubble, and is cancellable
    event.initCustomEvent(eventName, true, true, datas);
    document.dispatchEvent(event);
  },
};

/**
 * Class is responsible for handling Module Card behavior
 *
 * This is a port of admin-dev/themes/default/js/bundle/module/module_card.js
 */
export default class ModuleCard {
  moduleActionMenuLinkSelector: string;

  moduleActionMenuInstallLinkSelector: string;

  moduleActionMenuEnableLinkSelector: string;

  moduleActionMenuUninstallLinkSelector: string;

  moduleActionMenuDisableLinkSelector: string;

  moduleActionMenuEnableMobileLinkSelector: string;

  moduleActionMenuDisableMobileLinkSelector: string;

  moduleActionMenuResetLinkSelector: string;

  moduleActionMenuUpdateLinkSelector: string;

  moduleItemListSelector: string;

  moduleItemGridSelector: string;

  moduleItemActionsSelector: string;

  moduleActionModalDisableLinkSelector: string;

  moduleActionModalResetLinkSelector: string;

  moduleActionModalUninstallLinkSelector: string;

  forceDeletionOption: string;

  constructor() {
    /* Selectors for module action links (uninstall, reset, etc...) to add a confirm popin */
    this.moduleActionMenuLinkSelector = 'button.module_action_menu_';
    this.moduleActionMenuInstallLinkSelector = 'button.module_action_menu_install';
    this.moduleActionMenuEnableLinkSelector = 'button.module_action_menu_enable';
    this.moduleActionMenuUninstallLinkSelector = 'button.module_action_menu_uninstall';
    this.moduleActionMenuDisableLinkSelector = 'button.module_action_menu_disable';
    this.moduleActionMenuEnableMobileLinkSelector = 'button.module_action_menu_enableMobile';
    this.moduleActionMenuDisableMobileLinkSelector = 'button.module_action_menu_disableMobile';
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

  initActionButtons(): void {
    const self = this;

    $(document).on('click', this.forceDeletionOption, function () {
      const btn = $(
        self.moduleActionModalUninstallLinkSelector,
        $(ModuleCardMap.moduleItemList(<string>$(this).attr('data-tech-name'))),
      );

      if ($(this).prop('checked') === true) {
        btn.attr('data-deletion', 'true');
      } else {
        btn.removeAttr('data-deletion');
      }
    });

    $(document).on(
      'click',
      this.moduleActionMenuInstallLinkSelector,
      function () {
        return (
          self.dispatchPreEvent('install', this)
          && self.confirmAction('install', this)
          && self.requestToController('install', $(this))
        );
      },
    );

    $(document).on(
      'click',
      this.moduleActionMenuEnableLinkSelector,
      function () {
        return (
          self.dispatchPreEvent('enable', this)
          && self.confirmAction('enable', this)
          && self.requestToController('enable', $(this))
        );
      },
    );

    $(document).on(
      'click',
      this.moduleActionMenuUninstallLinkSelector,
      function () {
        return (
          self.dispatchPreEvent('uninstall', this)
          && self.confirmAction('uninstall', this)
          && self.requestToController('uninstall', $(this))
        );
      },
    );

    $(document).on(
      'click',
      this.moduleActionMenuDisableLinkSelector,
      function () {
        return (
          self.dispatchPreEvent('disable', this)
          && self.confirmAction('disable', this)
          && self.requestToController('disable', $(this))
        );
      },
    );

    $(document).on(
      'click',
      this.moduleActionMenuEnableMobileLinkSelector,
      function () {
        return (
          self.dispatchPreEvent('enableMobile', this)
          && self.confirmAction('enableMobile', this)
          && self.requestToController('enableMobile', $(this))
        );
      },
    );

    $(document).on(
      'click',
      this.moduleActionMenuDisableMobileLinkSelector,
      function () {
        return (
          self.dispatchPreEvent('disableMobile', this)
          && self.confirmAction('disableMobile', this)
          && self.requestToController('disableMobile', $(this))
        );
      },
    );

    $(document).on('click', this.moduleActionMenuResetLinkSelector, function () {
      return (
        self.dispatchPreEvent('reset', this)
        && self.confirmAction('reset', this)
        && self.requestToController('reset', $(this))
      );
    });

    $(document).on('click', this.moduleActionMenuUpdateLinkSelector, function (
      event,
    ) {
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
            confirmTitle:
              window.moduleTranslations.singleModuleModalUpdateTitle,
            closeButtonLabel: window.moduleTranslations.moduleModalUpdateCancel,
            confirmButtonLabel: isMaintenanceMode
              ? window.moduleTranslations.moduleModalUpdateUpgrade
              : window.moduleTranslations.upgradeAnywayButtonText,
            confirmButtonClass: isMaintenanceMode
              ? 'btn-primary'
              : 'btn-secondary',
            confirmMessage: isMaintenanceMode
              ? ''
              : window.moduleTranslations.moduleModalUpdateConfirmMessage,
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

    $(document).on(
      'click',
      this.moduleActionModalDisableLinkSelector,
      function () {
        return self.requestToController(
          'disable',
          $(
            self.moduleActionMenuDisableLinkSelector,
            $(
              ModuleCardMap.moduleItemList(
                <string>$(this).attr('data-tech-name'),
              ),
            ),
          ),
        );
      },
    );

    $(document).on(
      'click',
      this.moduleActionModalResetLinkSelector,
      function () {
        return self.requestToController(
          'reset',
          $(
            self.moduleActionMenuResetLinkSelector,
            $(
              ModuleCardMap.moduleItemList(
                <string>$(this).attr('data-tech-name'),
              ),
            ),
          ),
        );
      },
    );

    $(document).on(
      'click',
      this.moduleActionModalUninstallLinkSelector,
      (e) => {
        $(e.target)
          .parents('.modal')
          .on('hidden.bs.modal', () => self.requestToController(
            'uninstall',
            $(
              self.moduleActionMenuUninstallLinkSelector,
              $(
                ModuleCardMap.moduleItemList(
                    <string>$(e.target).attr('data-tech-name'),
                ),
              ),
            ),
            $(e.target).attr('data-deletion'),
          ),
          );
      },
    );
  }

  getModuleItemSelector(): string {
    if ($(this.moduleItemListSelector).length) {
      return this.moduleItemListSelector;
    }

    return this.moduleItemGridSelector;
  }

  confirmAction(action: string, element: string): boolean {
    const modal = $(
      ComponentsMap.confirmModal($(element).data('confirm_modal')),
    );

    if (modal.length !== 1) {
      return true;
    }

    modal.first().modal('show');

    return false; // do not allow a.href to reload the page. The confirm modal dialog will do it async if needed.
  }

  dispatchPreEvent(action: string, element: string): boolean {
    const event = jQuery.Event('module_card_action_event');

    $(element).trigger(event, [action]);
    if (
      event.isPropagationStopped() !== false
      || event.isImmediatePropagationStopped() !== false
    ) {
      return false; // if all handlers have not been called, then stop propagation of the click event.
    }

    // @ts-ignore-next-line
    return event.result !== false; // explicit false must be set from handlers to stop propagation of the click event.
  }

  requestToController(
    action: string,
    element: JQuery,
    forceDeletion: string | boolean = false,
    disableCacheClear: string | boolean = false,
    callback = () => true,
  ): boolean {
    const self = this;
    let jqElementObj = element.closest(this.moduleItemActionsSelector);
    const form = element.closest('form');
    const spinnerObj = $(
      '<button class="btn-primary-reverse onclick unbind spinner "></button>',
    );
    const url = `//${window.location.host}${form.attr('action')}`;
    const actionParams = form.serializeArray();
    let refreshNeeded = false;

    if (forceDeletion === 'true' || forceDeletion === true) {
      actionParams.push({name: 'actionParams[deletion]', value: 'true'});
    }
    if (disableCacheClear === 'true' || disableCacheClear === true) {
      actionParams.push({
        name: 'actionParams[cacheClearEnabled]',
        value: 'false',
      });
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
          $.growl.error({message: result[moduleTechName].msg, fixed: true});
          return;
        }

        $.growl({
          message: result[moduleTechName].msg,
          duration: 6000,
        });

        if (result[moduleTechName].refresh_needed === true) {
          refreshNeeded = true;
          return;
        }

        const alteredSelector = self.getModuleItemSelector().replace('.', '');
        let mainElement = null;

        if (action === 'uninstall') {
          mainElement = jqElementObj.closest(`.${alteredSelector}`);
          mainElement.attr('data-installed', '0');
          mainElement.attr('data-active', '0');

          BOEvent.emitEvent('Module Uninstalled', 'CustomEvent', mainElement);
        } else if (action === 'disable') {
          mainElement = jqElementObj.closest(`.${alteredSelector}`);
          mainElement.addClass(`${alteredSelector}-isNotActive`);
          mainElement.attr('data-active', '0');

          BOEvent.emitEvent('Module Disabled', 'CustomEvent', mainElement);
        } else if (action === 'enable') {
          mainElement = jqElementObj.closest(`.${alteredSelector}`);
          mainElement.removeClass(`${alteredSelector}-isNotActive`);
          mainElement.attr('data-active', '1');

          BOEvent.emitEvent('Module Enabled', 'CustomEvent', mainElement);
        } else if (action === 'install') {
          mainElement = jqElementObj.closest(`.${alteredSelector}`);
          mainElement.attr('data-installed', '1');
          mainElement.attr('data-active', '1');
          mainElement.removeClass(`${alteredSelector}-isNotActive`);

          BOEvent.emitEvent('Module Installed', 'CustomEvent', mainElement);
        };

        // Since we replace the DOM content
        // we need to update the jquery object reference to target the new content,
        // and we need to hide the new content which is not hidden by default
        jqElementObj = $(result[moduleTechName].action_menu_html).replaceAll(jqElementObj);
        jqElementObj.hide();
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
        if (refreshNeeded) {
          document.location.reload();
          return;
        }
        jqElementObj.fadeIn();
        spinnerObj.remove();
        if (callback) {
          callback();
        }
      });

    return false;
  }
}
