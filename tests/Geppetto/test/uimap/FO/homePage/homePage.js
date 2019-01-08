module.exports = {
  HomePage: {
    page_content: 'body',
    home_search_product_input_field: '.ui-autocomplete-input', //@Todo
    home_search_product_icon: '#search_widget > form > button > i[class*="search"]', //@Todo
    home_product_name_link: '.h3.product-title > a', //@Todo
    language_selector: '#_desktop_language_selector button',
    language_EN: '#_desktop_language_selector li:nth-child(1) > a',
    language_FR: '#_desktop_language_selector li:nth-child(2) > a',
    sign_in_button: '#_desktop_user_info span',
    order_history_and_details_button: '#history-link',
    sign_out_button: '#_desktop_user_info  a.logout',
    logo_home_page: '#_desktop_logo  img',
    product_name: 'div.products > article:nth-child(%ID) .h3.product-title > a',
    all_product_link: '#content a.all-product-link',
    home_first_product_name_link: '#content h3[itemprop="name"]:nth-child(1)', //@TODO
  }
};
