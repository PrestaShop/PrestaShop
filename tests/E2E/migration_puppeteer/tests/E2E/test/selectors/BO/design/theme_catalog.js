module.exports = {
  ThemeCatalog: {
    discover_all_of_the_theme_button: '#content" | #main-div  a [contains(text(), "Discover all of the themes")]',
    category_name_text: '#category_name',
    discover_button: '#content" | #main-div"] a:nth-child(1) p[contains(text(), "Discover")]):nth-child(%POS)',
    theme_name: '#content" | #main-div a:nth-child(1) p[class="bold"]:nth-child(%POS)',
    search_addons_input: '#addons-search-box',

//     Selectors in addons.prestashop.com site
    search_name: '#search_name b',
    theme_header_name: '#product_content h1'
  }
};
