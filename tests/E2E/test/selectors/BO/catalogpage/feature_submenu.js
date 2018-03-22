module.exports = {
  FeatureSubMenu: {
    tabmenu: '//*[@id="content"]/div[1]/div/div[2]/a[2]',
    add_new_feature: '//*[@id="page-header-desc-feature-new_feature"]',
    name_input: '//*[@id="name_1"]',
    save_button: '//*[@id="feature_form_submit_btn"]',
    search_input: '//*[@id="table-feature"]//th//input[@name="featureFilter_b!%SEARCHBY"]',
    search_button: '//*[@id="submitFilterButtonfeature"]',
    selected_feature: '//*[@id="table-feature"]//a[@title="View"]',
    add_value_button: '//*[@id="page-header-desc-feature_value-new_feature_value"]',
    value_input: '//*[@id="value_1"]',
    save_value_button: '//*[@id="feature_value_form_submit_btn"]',
    select_option: '//*[@id="table-feature"]//button[@data-toggle="dropdown"]',
    update_feature_button: '//*[@id="table-feature"]//a[@class="edit"]',
    update_feature_value_button: '//*[@id="table-feature_value"]//a[contains(@class, "edit")]',
    delete_feature: '//*[@id="table-feature"]//a[@class="delete"]',
    reset_button: '//*[@id="table-feature"]//button[@name="submitResetfeature"]'
  }
};
