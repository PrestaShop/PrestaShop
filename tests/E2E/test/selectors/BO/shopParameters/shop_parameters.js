module.exports = {
  ShopParameters: {
    maintenance_tab: '//*[@id="subtab-AdminMaintenance"]',
    success_panel: '//*[@id="main-div"]//div[@class="alert alert-success" and @role="alert"]',
    enable_shop: '//label[@for="form_general_enable_shop_%ID"]',
    source_code_button: '(//*[@id="PS_MAINTENANCE_TEXT_%ID"]//button)[1]',
    textarea_value: '//textarea[contains(@class, "mce-textbox")]',
    ok_button: '//div[contains(@class, "mce-container")]//button[contains(text(), "Ok")]',
    language_button: '//*[@id="conf_id_PS_MAINTENANCE_TEXT"]//button[@type= "button" and contains(@class, "dropdown-toggle")]',
    language_option: '(//a[contains(text(), "%LANG")])[%ID]',
    save_button: '//button[contains(text(), "Save")]',
    enable_disable_multistore_toggle_button: '//label[@for="form_general_multishop_feature_active_%ID"]',
    general_save_button: '//*[@id="configuration_form"]//button',
    textarea_input: '(//*[@id="form_general_maintenance_text"]//div[@class="mce-tinymce mce-container mce-panel"])[%ID]',
    maintenance_message: '//*[@id="content"]',
    success_box: '//*[@id="main-div"]//div[contains(@class, "success")]//div[contains(@class, "alert-text")]',
    menu_button: '//*[@id="subtab-ShopParameters"]',
    maintenance_mode_link: '//*[@id="maintenance-mode"]',
  },
  TrafficAndSeo: {
    SeoAndUrls: {
      friendly_url_button: '//*[@id="main-div"]//label[contains(@for, "meta_settings_form_set_up_urls_friendly_url_%s")]',
      save_button: '(//*[@id="main-div"]//form[@name="meta_settings_form"]//button)[1]'
    }
  },
  Contact: {
    Contacts: {
      add_new_contact_button: '//*[@id="page-header-desc-configuration-add"]',
      title_input: '//*[@id="contact_title_1"]',
      email_address_input: '//*[@id="contact_email"]',
      save_messages_button: '//*[@id="main-div"]//label[@for="contact_is_messages_saving_enabled_1"]',
      description_textarea: '//*[@id="contact_description_1"]',
      save_button: '//*[@id="main-div"]//button[contains(text(),"Save")]',
      filter_title_input: '//*[@id="contact_name"]',
      filter_search_button: '//*[@id="contact_grid_table"]//button[@title="Search"]',
      edit_button: '//*[@id="contact_grid_table"]/tbody//a/i[text()="edit"]',
      dropdown_button: '//*[@id="contact_grid_table"]/tbody//a[@data-toggle="dropdown"]',
      delete_button: '//*[@id="contact_grid_table"]/tbody//a[@data-method="POST"]',
      bulk_action_button: '//*[@id="contact_grid"]//button[@data-toggle="dropdown"]',
      bulk_actions_delete_button: '//*[@id="contact_grid_bulk_action_delete_all"]',
      empty_list: '//*[@id="contact_grid_table"]//div[@class="text-center grid-table-empty"]',
      checkbox_element: '//*[@id="contact_grid_table"]//div[@class="md-checkbox"]'
    }
  }
};