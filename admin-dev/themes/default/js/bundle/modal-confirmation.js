/**
 * modal confirmation management
 */
window.modalConfirmation = (function () {
  const modal = $('#confirmation_modal');

  if (!modal) {
    throw new Error('Modal confirmation is not available');
  }

  let actionsCallbacks = {
    onCancel() {
      console.log('modal canceled');
    },
    onContinue() {
      console.log('modal continued');
    },
  };

  modal.find('button.cancel').click(() => {
    if (typeof actionsCallbacks.onCancel === 'function') {
      actionsCallbacks.onCancel();
    }
    modalConfirmation.hide();
  });

  modal.find('button.continue').click(() => {
    if (typeof actionsCallbacks.onContinue === 'function') {
      actionsCallbacks.onContinue();
    }
    modalConfirmation.hide();
  });
  return {
    init: function init() {},
    create: function create(content, title, callbacks) {
      if (title != null) {
        modal.find('.modal-title').html(title);
      }
      if (content != null) {
        modal.find('.modal-body').html(content);
      }

      actionsCallbacks = callbacks;
      return this;
    },
    show: function show() {
      modal.modal('show');
    },
    hide: function hide() {
      modal.modal('hide');
    },
  };
}());

BOEvent.on('Modal confirmation started', () => {
  modalConfirmation.init();
}, 'Back office');
