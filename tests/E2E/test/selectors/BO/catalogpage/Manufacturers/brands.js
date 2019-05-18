module.exports = {
  Brands:{
    new_brand_button: '//*[@id="page-header-desc-configuration-add_manufacturer"]',
    name_input: '//*[@id="manufacturer_name"]',
    image_input: '//*[@id="manufacturer_logo"]',
    meta_title_input: '//*[@id="manufacturer_meta_title_1"]',
    meta_description_input: '//*[@id="manufacturer_meta_description_1"]',
    meta_keywords_input:'//*[@id="manufacturer_meta_keyword_1-tokenfield"]',
    active_button: '//label[@for="manufacturer_is_enabled_1"]',
    save_button: '//div[@class="card-footer"]/button[contains(text(),"Save")]',
    short_description_input: '#manufacturer_short_description_1_ifr',
    description_input: '#manufacturer_description_1_ifr',
    filter_name_input: '//input[@id="manufacturer_name"]',
    brand_column: '//*[@id="manufacturer_grid_table"]//tr[%TR]/td[%COL]',
    brand_search_button: '//button[@name="manufacturer[actions][search]"]',
    brand_reset_button: '//*[@id="manufacturer_grid_table"]//button[@type="reset"]',
  }
};
