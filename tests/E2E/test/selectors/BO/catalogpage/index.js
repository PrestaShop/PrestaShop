module.exports = Object.assign(
  {
    CatalogPage: {
      success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
      danger_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "danger")]',
      select_all_product_button: '//*[@id="bulk_action_select_all"]/..',
      action_group_button: '//*[@id="product_bulk_menu"]',
      action_button: '(//*[@id="main-div"]//div[contains(@class, "bulk-catalog")]//a)[%ID]',
      green_validation: '//*[@id="main-div"]//div[contains(@class, "alert-success") and not(@style)]',
      product_status_icon: '(//*[@id="product_catalog_list"]//tbody/tr[%S]//i[contains(@class, "material-icons")])[1]',
      name_search_input: '(//*[@id="product_catalog_list"]//input[contains(@name, "filter_column_name")])[1]',
      search_button: '//*[@id="product_catalog_list"]//button[contains(@name, "products_filter_submit")]',
      dropdown_toggle: '//*[@id="product_catalog_list"]//button[contains(@class, "dropdown-toggle")]',
      delete_button: '//*[@id="product_catalog_list"]//a[contains(@onclick, "delete")]',
      delete_confirmation: '//*[@id="catalog_deletion_modal"]//button[2]',
      close_delete_modal: '(//*[@id="catalog_deletion_modal"]//button[1])[2]',
      reset_button: '//*[@id="product_catalog_list"]//button[contains(@name, "products_filter_reset")]',
      search_result_message: '//*[@id="product_catalog_list"]//td[contains(text(), "There is no result for this search")]',
      deactivate_modal: '//*[@id="catalog_deactivate_all_modal"]',
      activate_modal: '//*[@id="catalog_activate_all_progression"]//div[contains(text(), "Activating")]',
      duplicate_modal: '//*[@id="catalog_duplicate_all_modal"]',
      delete_modal: '//*[@id="catalog_deletion_modal"]'
    }
  },
  require('./feature_submenu'),
  require('./category_submenu'),
  require('./attribute_submenu'),
  require('./Manufacturers'),
  require('./stocksubmenu'),
  require('./discount_submenu'),
  require('./files')
);
