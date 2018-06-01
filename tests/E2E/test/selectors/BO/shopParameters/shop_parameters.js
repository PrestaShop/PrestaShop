module.exports = {
  ShopParameters: {
    maintenance_tab: '//a[text() = "Maintenance"]',
    success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
    enable_shop: '//label[@for="form_general_enable_shop_%ID"]',
    source_code_button: '(//*[@id="PS_MAINTENANCE_TEXT_%ID"]//button)[1]',
    textarea_value: '//textarea[contains(@class, "mce-textbox")]',
    ok_button: '//div[contains(@class, "mce-container")]//button[contains(text(), "Ok")]',
    language_button: '//*[@id="conf_id_PS_MAINTENANCE_TEXT"]//button[@type= "button" and contains(@class, "dropdown-toggle")]',
    language_option: '(//a[contains(text(), "%LANG")])[%ID]',
    save_button: '//button[contains(text(), "Save")]',
    enable_multistore: '//label[contains(@for, "form_general_multishop_feature_active_1")]',
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
      save_button: '//*[@id="meta_fieldset_general"]//button[text()=" Save"]'
    }
  }
};
