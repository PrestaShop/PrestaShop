module.exports = {
  SearchProductPage: {
    search_input: '.ui-autocomplete-input',
    search_button: '.material-icons.search',
    product_result_name: '.h3.product-title > a',
    product_result_discount: '#js-product-list span.discount-percentage',
    attribute_name: '#add-to-cart-or-refresh div .product-variants-item span.control-label',
    feature_name: '#product-details section dl dt.name ',
    feature_value: '#product-details section dl dd',
    attribute_select_values: '#group_%ID option',
    attribute_radio_values: '#add-to-cart-or-refresh div.product-variants li span.radio-label',
    attribute_color_and_texture_values: '#add-to-cart-or-refresh div.product-variants li span.sr-only',
    quick_view_first_product: '#js-product-list a.quick-view',
    empty_result_section: ' section.page-not-found',
    first_product_name_link: 'h2.product-title a:nth-child(1)',
    search_product_name: '#js-product-list h2.product-title > a[href*="%PRODUCTNAME"] '
  }
};
