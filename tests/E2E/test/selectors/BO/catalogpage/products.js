module.exports = {
  Products_list:{
    name_search_input:'(//*[@id="product_catalog_list"]//input[contains(@name, "filter_column_name")])[1]',
    search_button:'//*[@id="product_catalog_list"]//button[contains(@name, "products_filter_submit")]',
    dropdown_toggle:'//*[@id="product_catalog_list"]//a[contains(@class, "dropdown-toggle")]',
    delete_button:'//*[@id="product_catalog_list"]//a[contains(@onclick, "delete")]',
    delete_confirmation:'//*[@id="catalog_deletion_modal"]//button[2]',
    success_panel:'//*[@id="main-div"]//div[contains(@class, "alert-text")]/p'
  }
};
