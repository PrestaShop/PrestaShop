module.exports = Object.assign(
  {
    BO: {
      success_panel: '#content > div.bootstrap > div[class*=success]',
      alert_panel: '#content div[class*=bootstrap] div[class*=alert-danger]'
    }
  },
  require('./addresses'),
  require('./customer')
);
