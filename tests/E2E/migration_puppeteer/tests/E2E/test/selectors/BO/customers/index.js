module.exports = Object.assign(
  {
    BO: {
      success_panel: '#content > div.bootstrap > div.success',
      alert_success_text: 'div.alert-success .alert-text p',
      alert_success: 'div.alert-success',
      alert_panel: '#content div[class*=bootstrap] div[class*=alert-danger]'
    }
  },
  require('./addresses'),
  require('./customer')
);
