module.exports = Object.assign(
  {
    BO: {
      success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
      alert_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "alert-danger")]'
    }
  },
  require('./addresses'),
  require('./customer')
);