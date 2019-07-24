module.exports = {
  SearchProductPage: {
    search_input: '.ui-autocomplete-input',
    search_button: '.material-icons.search',
    product_result_name: '.h3.product-title > a',
    product_result_discount: '.products article .discount',
    attribute_name: '//*[@id="add-to-cart-or-refresh"]//div[contains (@class, "product-variants-item")]/span',
    feature_name: '//*[@id="product-details"]/section/dl/dt[@class="name"]',
    feature_value: '//*[@id="product-details"]/section/dl/dd',
    attribute_select_values: '//*[@id="group_%ID"]/option',
    attribute_radio_values: '//*[@id="add-to-cart-or-refresh"]/div[@class="product-variants"]//li//span[contains(@class, "radio-label")]',
    attribute_color_and_texture_values: '//*[@id="add-to-cart-or-refresh"]/div[@class="product-variants"]//li//span[contains(@class, "sr-only")]',
    quick_view_first_product: '//*[@id="js-product-list"]//a[contains(@class,"quick-view")]',
    empty_result_section: '//section[contains(@class,"page-not-found")]',
    first_product_name_link: '(//h2[contains(@class,"product-title")]/a)[1]',
    search_product_name:'//*[@id="js-product-list"]//a[text()="%PRODUCT"]'
  }
};
