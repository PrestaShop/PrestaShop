module.exports = Object.assign(
  {
    success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]'
  },
  require('./pages'),
  require('./theme_catalog')
);