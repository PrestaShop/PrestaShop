/* eslint-disable max-len */
let moduleCardController = {};

$(document).ready(() => {
  moduleCardController = new AdminModuleCard();
  moduleCardController.init();
});

/**
 * AdminModule card Controller.
 * @constructor
 */
const AdminModuleCard = function () {
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
    }
    return this.moduleItemGridSelector;
  };

  this.confirmAction = function (action, element) {
    const modal = $(`#${$(element).data('confirm_modal')}`);

    if (modal.length !== 1) {
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
    const that = this;
    const modal = this.replacePrestaTrustPlaceholders(result);
    modal.find('.pstrust-install').off('click').on('click', () => {
      // Find related form, update it and submit it
      const installButton = $(
        that.moduleActionMenuInstallLinkSelector,
        `.module-item[data-tech-name="${result.module.attributes.name}"]`,
      );
      const form = installButton.parent('form');
      $('<input>').attr({
        type: 'hidden',
        value: '1',
        name: 'actionParams[confirmPrestaTrust]',
      }).appendTo(form);
      installButton.click();
      modal.modal('hide');
    });
    modal.modal();
  };

  this.replacePrestaTrustPlaceholders = function replacePrestaTrustPlaceholders(result) {
    const modal = $('#modal-prestatrust');
    const module = result.module.attributes;

    if (result.confirmation_subject !== 'PrestaTrust' || !modal.length) {
      return;
    }

    const alertClass = module.prestatrust.status ? 'success' : 'warning';

    if (module.prestatrust.check_list.property) {
      modal.find('#pstrust-btn-property-ok').show();
      modal.find('#pstrust-btn-property-nok').hide();
    } else {
      modal.find('#pstrust-btn-property-ok').hide();
      modal.find('#pstrust-btn-property-nok').show();
      modal.find('#pstrust-buy').attr('href', module.url).toggle(module.url !== null);
    }

    modal.find('#pstrust-img').attr({src: module.img, alt: module.name});
    modal.find('#pstrust-name').text(module.displayName);
    modal.find('#pstrust-author').text(module.author);
    modal.find('#pstrust-label').attr('class', `text-${alertClass}`).text(module.prestatrust.status ? 'OK' : 'KO');
    modal.find('#pstrust-message').attr('class', `alert alert-${alertClass}`);
    modal.find('#pstrust-message > p').text(module.prestatrust.message);

    // eslint-disable-next-line
    return modal;
  };

  this.dispatchPreEvent = function (action, element) {
    const event = jQuery.Event('module_card_action_event');
    $(element).trigger(event, [action]);
    if (event.isPropagationStopped() !== false || event.isImmediatePropagationStopped() !== false) {
      return false; // if all handlers have not been called, then stop propagation of the click event.
    }
    return (event.result !== false); // explicit false must be set from handlers to stop propagation of the click event.
  };

  this.initActionButtons = function () {
    const that = this;

    $(document).on('click', this.forceDeletionOption, function () {
      const btn = $(
        that.moduleActionModalUninstallLinkSelector,
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
      return that.dispatchPreEvent('install', this) && that.confirmAction('install', this) && that.requestToController('install', $(this));
    });
    $(document).on('click', this.moduleActionMenuEnableLinkSelector, function () {
      return that.dispatchPreEvent('enable', this) && that.confirmAction('enable', this) && that.requestToController('enable', $(this));
    });
    $(document).on('click', this.moduleActionMenuUninstallLinkSelector, function () {
      return that.dispatchPreEvent('uninstall', this) && that.confirmAction('uninstall', this) && that.requestToController('uninstall', $(this));
    });
    $(document).on('click', this.moduleActionMenuDisableLinkSelector, function () {
      return that.dispatchPreEvent('disable', this) && that.confirmAction('disable', this) && that.requestToController('disable', $(this));
    });
    $(document).on('click', this.moduleActionMenuEnableMobileLinkSelector, function () {
      return that.dispatchPreEvent('enable_mobile', this) && that.confirmAction('enable_mobile', this) && that.requestToController('enable_mobile', $(this));
    });
    $(document).on('click', this.moduleActionMenuDisableMobileLinkSelector, function () {
      return that.dispatchPreEvent('disable_mobile', this) && that.confirmAction('disable_mobile', this) && that.requestToController('disable_mobile', $(this));
    });
    $(document).on('click', this.moduleActionMenuResetLinkSelector, function () {
      return that.dispatchPreEvent('reset', this) && that.confirmAction('reset', this) && that.requestToController('reset', $(this));
    });
    $(document).on('click', this.moduleActionMenuUpdateLinkSelector, function () {
      return that.dispatchPreEvent('update', this) && that.confirmAction('update', this) && that.requestToController('update', $(this));
    });

    $(document).on('click', this.moduleActionModalDisableLinkSelector, function () {
      return that.requestToController('disable', $(that.moduleActionMenuDisableLinkSelector, $(`div.module-item-list[data-tech-name='${$(this).attr('data-tech-name')}']`)));
    });
    $(document).on('click', this.moduleActionModalResetLinkSelector, function () {
      return that.requestToController('reset', $(that.moduleActionMenuResetLinkSelector, $(`div.module-item-list[data-tech-name='${$(this).attr('data-tech-name')}']`)));
    });
    $(document).on('click', this.moduleActionModalUninstallLinkSelector, (e) => {
      $(e.target).parents('.modal').on('hidden.bs.modal', (() => that.requestToController(
        'uninstall',
        $(
          that.moduleActionMenuUninstallLinkSelector,
          $(`div.module-item-list[data-tech-name='${$(e.target).attr('data-tech-name')}']`),
        ),
        $(e.target).attr('data-deletion'),
      )));
    });
  };

  this.requestToController = function (action, element, forceDeletion) {
    const that = this;
    const jqElementObj = element.closest(this.moduleItemActionsSelector);
    const form = element.closest('form');
    const spinnerObj = $('<button class="btn-primary-reverse onclick unbind spinner "></button>');
    const url = `//${window.location.host}${form.attr('action')}`;
    const actionParams = form.serializeArray();

    if (forceDeletion === 'true' || forceDeletion === true) {
      actionParams.push({name: 'actionParams[deletion]', value: true});
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
    }).done((result) => {
      if (typeof result === 'undefined') {
        $.growl.error({message: 'No answer received from server'});
      } else {
        const moduleTechName = Object.keys(result)[0];

        if (result[moduleTechName].status === false) {
          if (typeof result[moduleTechName].confirmation_subject !== 'undefined') {
            that.confirmPrestaTrust(result[moduleTechName]);
          }
          $.growl.error({message: result[moduleTechName].msg});
        } else {
          $.growl.notice({message: result[moduleTechName].msg});
          let alteredSelector = null;
          let mainElement = null;

          if (action === 'uninstall') {
            jqElementObj.fadeOut(() => {
              alteredSelector = that.getModuleItemSelector().replace('.', '');
              mainElement = jqElementObj.parents(`.${alteredSelector}`).first();
              mainElement.remove();
            });
            BOEvent.emitEvent('Module Uninstalled', 'CustomEvent');
          } else if (action === 'disable') {
            alteredSelector = that.getModuleItemSelector().replace('.', '');
            mainElement = jqElementObj.parents(`.${alteredSelector}`).first();
            mainElement.addClass(`${alteredSelector}-isNotActive`);
            mainElement.attr('data-active', '0');
            BOEvent.emitEvent('Module Disabled', 'CustomEvent');
          } else if (action === 'enable') {
            alteredSelector = that.getModuleItemSelector().replace('.', '');
            mainElement = jqElementObj.parents(`.${alteredSelector}`).first();
            mainElement.removeClass(`${alteredSelector}-isNotActive`);
            mainElement.attr('data-active', '1');
            BOEvent.emitEvent('Module Enabled', 'CustomEvent');
          }

          jqElementObj.replaceWith(result[moduleTechName].action_menu_html);
        }
      }
    }).always(() => {
      jqElementObj.fadeIn();
      spinnerObj.remove();
    });
    return false;
  };
};
