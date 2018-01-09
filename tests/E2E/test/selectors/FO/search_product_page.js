module.exports = {
  SearchProductPage: {
    category_list: '//*[@id="left-column"]/div[1]/ul/li[2]/ul/li',
    second_category_name: '//*[@id="left-column"]/div[1]/ul/li[2]/ul/li[6]/a',
    search_input: '.ui-autocomplete-input',
    search_button: '.material-icons.search',
    product_result_name: '.h3.product-title > a',
    second_product_result_name: '//*[@id="js-product-list"]/div[1]/article[2]/div/div[1]/h1/a',
    product_result_price: '[itemprop="price"]',
    attribute_name: '//*[@id="add-to-cart-or-refresh"]/div[1]/div/span',
    feature_name: '//*[@id="product-details"]/section/dl/dt',
    feature_value: '//*[@id="product-details"]/section/dl/dd',
    attribute_value_1: '//*[@id="add-to-cart-or-refresh"]/div[1]/div/ul/li[1]/label/span',
    attribute_value_2: '//*[@id="add-to-cart-or-refresh"]/div[1]/div/ul/li[2]/label/span',
    attribute_value_3: '//*[@id="add-to-cart-or-refresh"]/div[1]/div/ul/li[3]/label/span',
  }
};
