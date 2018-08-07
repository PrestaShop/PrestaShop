module.exports = {
  AccessPageFO: {
    sign_in_button: '//*[@id="_desktop_user_info"]/div/a',
    login_input: '//*[@id="login-form"]/section/div[1]/div[1]/input',
    password_inputFO: '//*[@id="login-form"]/section/div[2]/div[1]/div/input',
    login_button: '//*[@id="login-form"]/footer/button',
    sign_out_button: '//*[@id="_desktop_user_info"]/div/a[1]',
    logo_home_page: '//*[@id="_desktop_logo"]//a',
    product_list_button: '//*[@id="content"]/section/a',
    categories_list: '//*[@id="left-column"]/div[1]/ul/li[2]/ul',
    category_name: '//*[@id="left-column"]/div[contains(@class, "categories")]//a[text()="%NAME"]',
    shopping_cart_button: '//*[@id="_desktop_cart"]/div',
    top_sellers_block: '//h1[contains(text(), "Best Sellers")]',
    new_products_block: '//h1[contains(text(), "New products")]',
    sitemap: '//*[@id="link-static-page-sitemap-2"]',
    page_link: '//*[@id="main"]//a[@title="%pageName"]',
    page_content: '//*[@id="content"]/p',
    address_information_link: '//*[@id="addresses-link"]',
    address_information:'//*[@id="address-%ID"]//address',
    addresses_warning: '//*[@id="notifications"]//li',
    page_category: '//*[@id="wrapper"]//a/span[contains(text(),"%CATEGORY")]',
    review_page_link: '//*[@id="content"]//a[contains(text(),"%PAGENAME")]',
    not_found_erreur_message: '//*[@id="main"]//h1'
  }
};