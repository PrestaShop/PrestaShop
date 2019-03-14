/**
 * modal confirmation management
 */
var modalConfirmation = (function() {
  var modal = $('#confirmation_modal');

  if(!modal) {
    throw new Error('Modal confirmation is not available');
  }

  var actionsCallbacks = {
    onCancel: function() {
      console.log('modal canceled');
      return;
    },
    onContinue: function() {
      console.log('modal continued');
      return;
    }
  };

  modal.find('button.cancel').click(function() {
    if (typeof actionsCallbacks.onCancel === 'function') {
      actionsCallbacks.onCancel();
    }
    modalConfirmation.hide();
  });

  modal.find('button.continue').click(function() {
    if (typeof actionsCallbacks.onContinue === 'function') {
      actionsCallbacks.onContinue();
    }
    modalConfirmation.hide();
  });
  return {
    'init': function init() {},
    'create': function create(content, title, callbacks) {
      if(title != null){
        modal.find('.modal-title').html(title);
      }
      if(content != null){
        modal.find('.modal-body').html(content);
      }

      actionsCallbacks = callbacks;
      return this;
    },
    'show': function show() {
      modal.modal('show');
    },
    'hide': function hide() {
      modal.modal('hide');
    }
  };
})();

BOEvent.on("Modal confirmation started", function initModalConfirmationSystem() {
  modalConfirmation.init();
}, "Back office");
