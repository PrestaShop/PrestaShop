module.exports =Object.assign(
  {
    InternationalPage: {
      success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
    }
  },
  require('./taxes'),
  require('./translations'),
  require('./localization')
);
