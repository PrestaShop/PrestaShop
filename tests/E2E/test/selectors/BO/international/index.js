module.exports =Object.assign(
  {
    InternationalPage: {
      success_panel: '//*[@id="content"]//div[@class="alert alert-success"]'
    }
  },
  require('./taxes'),
  require('./translations'),
  require('./localization')
);
