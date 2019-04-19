module.exports = Object.assign(
  {
    CatalogPage: {
      success_panel: '#content > div.bootstrap > div.success',
      danger_panel: '#content div.bootstrap div.danger',
      select_all_product_button: '#bulk_action_select_all',
      action_group_button: '#product_bulk_menu ',
      action_button: '#main-div div.bulk-catalog a:nth-child(%ID)',
      green_validation: '#main-div .alert-success .alert-text p',
      alert_success: 'div.alert-success',
      product_status_icon: '#product_catalog_list tbody tr:nth-child(%S) i.material-icons:nth-child(1)',
      name_search_input: '#product_catalog_list input[name="filter_column_name"]',
      search_button: '#product_catalog_list button[name="products_filter_submit"]',
      dropdown_toggle: '#product_catalog_list button.dropdown-toggle',
      delete_button: '#product_catalog_list a[onclick*="delete"]',
      delete_confirmation: '#catalog_deletion_modal.show [type="button"][value="confirm"]',
      close_delete_modal: '#catalog_deletion_modal.show button.close',
      reset_button: '#product_catalog_list button[name*="products_filter_reset"]',
      search_result_message: '#product_catalog_list tbody tr td',
      // search_result_message: '#product_catalog_list td[contains(text(), "There is no result for this search")]',
      deactivate_modal: '#catalog_deactivate_all_modal',
      activate_modal: '#catalog_activate_all_modal',
      duplicate_modal: '#catalog_duplicate_all_modal',
      delete_modal: '#catalog_deletion_modal'
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
