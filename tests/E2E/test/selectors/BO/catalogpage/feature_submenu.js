module.exports = {
  FeatureSubMenu:{
    tabmenu: '//*[@id="content"]/div[1]/div/div[2]/a[2]',
    add_new_feature: '//*[@id="page-header-desc-feature-new_feature"]/div',
    name_input: '//*[@id="name_1"]',
    save_button: '//*[@id="feature_form_submit_btn"]',
    search_input: '//*[@id="table-feature"]/thead/tr[2]/th[3]/input',
    search_button: '//*[@id="submitFilterButtonfeature"]',
    selected_feature: '//*[@id="table-feature"]/tbody/tr/td[6]/div/div/a',
    add_value_button: '//*[@id="page-header-desc-feature_value-new_feature_value"]',
    value_input: '//*[@id="value_1"]',
    save_value_button: '//*[@id="feature_value_form_submit_btn"]',
    select_option: '//*[@id="table-feature"]/tbody/tr/td[6]/div/div/button',
    update_feature_button: '//*[@id="table-feature"]/tbody/tr/td[6]/div/div/ul/li[1]/a',
    update_feature_value_button: '//*[@id="table-feature_value"]//tr/td[3]/div/div/a',
    delete_feature: '//*[@id="table-feature"]/tbody/tr/td[6]/div/div/ul/li[3]/a',
    reset_button: '//*[@id="table-feature"]//button[@name="submitResetfeature"]',
    feature_checkbox: '//*[@id="table-feature"]//input[@type="checkbox"]',
    feature_bulk_actions: '//*[@id="bulk_action_menu_feature"]',
    feature_delete_bulk_action: '//*[@id="form-feature"]//div[contains(@class,"bulk-actions")]//li[4]/a'
  }
};
