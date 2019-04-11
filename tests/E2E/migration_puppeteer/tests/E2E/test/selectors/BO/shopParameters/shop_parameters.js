module.exports = {
  ShopParameters: {
    maintenance_tab: '#subtab-AdminMaintenance ',
    success_panel: '#content div.bootstrap div.success',
    enable_shop: ' label[for="form_general_enable_shop_%ID"]',
    source_code_button: '#PS_MAINTENANCE_TEXT_%ID button)[1]',
    textarea_value: ' textarea .mce-textbox',
    ok_button: ' div.mce-container button[contains(text(), "Ok")]',
    language_button: '#conf_id_PS_MAINTENANCE_TEXT button[type= "button" and .dropdown-toggle")]',
    language_option: '( a [contains(text(), "%LANG")]):nth-child(%ID)',
    save_button: ' button[contains(text(), "Save")]',
    enable_disable_multistore_toggle_button: ' label[for="form_general_multishop_feature_active_%ID"]',
    general_save_button: '#configuration_form button',
    textarea_input: '#form_general_maintenance_text div.mce-tinymce mce-container mce-panel"]):nth-child(%ID)',
    maintenance_message: '#content ',
    success_box: '#main-div div.success div.alert-text',
    menu_button: '#subtab-ShopParameters ',
    maintenance_mode_link: '#maintenance-mode ',
  },
  TrafficAndSeo: {
    SeoAndUrls: {
      friendly_url_button: '#main-div label[for="meta_settings_form_set_up_urls_friendly_url_%s"]',
      save_button: '#main-div div [name="meta_settings_form"] button:nth-child(1)'
    }
  },
  Contact: {
    Contacts: {
      add_new_contact_button: '#page-header-desc-contact-new_contact ',
      title_input: '#name_1 ',
      email_address_input: '#email ',
      save_messages_button: '#fieldset_0 label[for="customer_service_on"]',
      description_textarea: '#description_1 ',
      save_button: '#contact_form_submit_btn ',
      filter_title_input: '#table-contact input[name="contactFilter_name"]',
      filter_search_button: '#submitFilterButtoncontact ',
      edit_button: '#table-contact tbody a [title="Edit"]',
      dropdown_button: '#table-contact tbody button[data-toggle="dropdown"]',
      delete_button: '#table-contact tbody a [title="Delete"]',
      bulk_action_button: '#bulk_action_menu_contact ',
      bulk_actions_select_all_button: '#form-contact div.bulk-actions ul.dropdown-menu a [contains(onclick, "true")]',
      bulk_actions_unselect_all_button: '#form-contact div.bulk-actions ul.dropdown-menu a [contains(onclick, ", false")]',
      bulk_actions_delete_button: '#form-contact div.bulk-actions ul.dropdown-menu a [contains(onclick, "submitBulkdeletecontact")]',
      empty_list: '#table-contact tbody td.list-empty ',
      checkbox_element: '#table-contact tbody input[type="checkbox"]'
    }
  }
};
