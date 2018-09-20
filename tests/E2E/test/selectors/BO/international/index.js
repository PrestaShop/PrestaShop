module.exports =Object.assign(
  {
    InternationalPage: {
      success_panel: '//*[@class="alert alert-success"]/div[@class="alert-text"]',
    }
  },
  require('./taxes'),
  require('./translations'),
  require('./localization')
);
