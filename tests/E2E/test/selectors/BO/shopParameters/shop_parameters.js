module.exports = {
  ShopParameters: {
    maintenance_tab: '//*[@id="content"]//a[text() = "Maintenance"]',
    success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
    enable_shop: '//*[@id="conf_id_PS_SHOP_ENABLE"]//label[contains(@for, "%s")]',
    source_code_button: '(//*[@id="PS_MAINTENANCE_TEXT_%ID"]//button)[1]',
    textarea_value: '//textarea[contains(@class, "mce-textbox")]',
    ok_button: '//div[contains(@class, "mce-container")]//button[contains(text(), "Ok")]',
    language_button: '//*[@id="conf_id_PS_MAINTENANCE_TEXT"]//button[@type= "button" and contains(@class, "dropdown-toggle")]',
    language_option: '(//a[contains(text(), "%LANG")])[%ID]',
    save_button: '//*[@id="configuration_fieldset_general"]//button[@type="submit"]',
    enable_multistore: '//*[@id="conf_id_PS_MULTISHOP_FEATURE_ACTIVE"]//label[contains(text(), "Yes")]',
    general_save_button: '//*[@id="configuration_fieldset_general"]//button[@name="submitOptionsconfiguration"]',
    maintenance_message: '//*[@id="content"]/p'
  },
  TrafficAndSeo: {
    SeoAndUrls: {
      friendly_url_button: '//*[@id="conf_id_PS_REWRITING_SETTINGS"]//label[contains(@for, "%s")]',
      save_button: '//*[@id="meta_fieldset_general"]//button[text()=" Save"]'
    }
  }
};
