module.exports = Object.assign(
  {
    BO: {
      success_panel: '#content > div.bootstrap > div.success',
      alert_success_text: 'div.alert-success .alert-text p',
      alert_success: 'div.alert-success',
      alert_panel: '#content div.bootstrap div.alert-danger',
      modal_dialog_accept: '.modal-dialog .modal-footer button[class*="submit-delete-customers"]',
      modal_dialog_close: '.modal-dialog .modal-footer button[data-dismiss]'
    }
  },
  require('./addresses'),
  require('./customer')
);
