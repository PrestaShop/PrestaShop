module.exports = {
  ShopParameters: {
    maintenance_tab: '//*[@id="content"]//a[text() = "Maintenance"]',
    success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
    general_success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
    maintenance_success_panel: '//div[contains(@class, "success")]//p',
    enable_shop: '//*[@id="form_general_enable_shop"]',
    textarea_input: '(//*[@id="form_general_maintenance_text"]//div[@class="mce-tinymce mce-container mce-panel"])[%ID]',
    language_option: '(//a[contains(text(), "%LANG")])[%ID]',
    save_button: '//button[contains(text(), "Save")]',
    enable_multistore: '//*[@id="conf_id_PS_MULTISHOP_FEATURE_ACTIVE"]//label[contains(text(), "Yes")]',
    general_save_button: '//*[@id="configuration_fieldset_general"]//button[@name="submitOptionsconfiguration"]',
    maintenance_message: '//*[@id="content"]'
  },
  TrafficAndSeo: {
    SeoAndUrls: {
      friendly_url_button: '//*[@id="conf_id_PS_REWRITING_SETTINGS"]//label[contains(@for, "%s")]',
      save_button: '//*[@id="meta_fieldset_general"]//button[text()=" Save"]'
    }
  }

};
