module.exports = {
  ThemeCatalog: {
    discover_all_of_the_theme_button: '//*[@id="content" | @id="main-div"]//a[contains(text(), "Discover all of the themes")]',
    category_name_text: '//*[@id="category_name"]',
    discover_button: '(//*[@id="content" | @id="main-div"]//a[1]//p[contains(text(), "Discover")])[%POS]',
    theme_name: '(//*[@id="content" | @id="main-div"]//a[1]//p[@class="bold"])[%POS]',
    search_addons_input: '//*[@id="addons-search-box"]',

    //Selectors in addons.prestashop.com site
    search_name: '//*[@id="search_name"]/b',
    theme_header_name: '//*[@id="product_content"]//h1'
  }
};