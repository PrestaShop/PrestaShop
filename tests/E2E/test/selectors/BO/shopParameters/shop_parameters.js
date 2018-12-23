module.exports = {
  ShopParameters: {
    maintenance_tab: '//*[@id="subtab-AdminMaintenance"]',
    success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
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
      friendly_url_button: '//*[@id="conf_id_PS_REWRITING_SETTINGS"]//label[contains(@for, "%s")]',
      save_button: '//*[@id="meta_form"]//button[text()=" Save"]'
    }
  },
  Contact: {
    Contacts: {
      add_new_contact_button: '//*[@id="page-header-desc-contact-new_contact"]',
      title_input: '//*[@id="name_1"]',
      email_address_input: '//*[@id="email"]',
      save_messages_button: '//*[@id="fieldset_0"]//label[@for="customer_service_on"]',
      description_textarea: '//*[@id="description_1"]',
      save_button: '//*[@id="contact_form_submit_btn"]',
      filter_title_input: '//*[@id="table-contact"]//input[@name="contactFilter_name"]',
      filter_search_button: '//*[@id="submitFilterButtoncontact"]',
      edit_button: '//*[@id="table-contact"]/tbody//a[@title="Edit"]',
      dropdown_button: '//*[@id="table-contact"]/tbody//button[@data-toggle="dropdown"]',
      delete_button: '//*[@id="table-contact"]/tbody//a[@title="Delete"]',
      bulk_action_button: '//*[@id="bulk_action_menu_contact"]',
      bulk_actions_select_all_button: '//*[@id="form-contact"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "true")]',
      bulk_actions_unselect_all_button: '//*[@id="form-contact"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, ", false")]',
      bulk_actions_delete_button: '//*[@id="form-contact"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "submitBulkdeletecontact")]',
      empty_list: '//*[@id="table-contact"]/tbody//td[@class="list-empty"]',
      checkbox_element: '//*[@id="table-contact"]/tbody//input[@type="checkbox"]'
    }
  }
};
