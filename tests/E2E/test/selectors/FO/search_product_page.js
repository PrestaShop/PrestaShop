module.exports = {
  SearchProductPage: {
    search_input: '.ui-autocomplete-input',
    search_button: '.material-icons.search',
    product_result_name: '.h3.product-title > a',
    product_result_discount: '//*[@id="js-product-list"]//span[contains(@class, "discount-percentage")]',
    attribute_name: '//*[@id="add-to-cart-or-refresh"]//div[contains (@class, "product-variants-item")]/span',
<<<<<<< HEAD
    feature_name: '//*[@id="product-details"]/section/dl/dt[@class="name"]',
=======
    feature_name: '//*[@id="product-details"]/section/dl/dt',
>>>>>>> upstream/1.7.3.x
    feature_value: '//*[@id="product-details"]/section/dl/dd',
    attribute_values: '//*[@id="add-to-cart-or-refresh"]/div[@class="product-variants"]//li//span',
    quick_view_first_product:'//*[@id="js-product-list"]//a[contains(@class,"quick-view")]'
  }
};
