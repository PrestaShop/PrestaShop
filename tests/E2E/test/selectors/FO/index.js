module.exports = Object.assign({
    languageFO: {
      language_selector: '//*[@id="_desktop_language_selector"]/div/div/button',
      language_EN: '//*[@id="_desktop_language_selector"]/div/div/ul/li[1]/a',
      language_FR: '//*[@id="_desktop_language_selector"]/div/div/ul/li[2]/a',
      language_option: '//*[@id="_desktop_language_selector"]//a[contains(@href, "/%LANG/")]',
      selected_language_button: '//*[@id="_desktop_language_selector"]//span[@class="expand-more"]'
    }
  },
  require('./access_page'),
  require('./add_account_page'),
  require('./order_page'),
  require('./product_page'),
  require('./search_product_page')
);
