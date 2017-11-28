module.exports = Object.assign({
    languageFO: {
      language_selector: '//*[@id="_desktop_language_selector"]/div/div/button',
      language_EN: '//*[@id="_desktop_language_selector"]/div/div/ul/li[1]/a',
      language_FR: '//*[@id="_desktop_language_selector"]/div/div/ul/li[2]/a'
    }
  },
  require('./access_page'),
  require('./add_account_page'),
  require('./buy_order_page'),
  require('./layer_cart_page'),
  require('./product_page'),
  require('./search_product_page')
);
