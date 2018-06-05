module.exports = {
  AttributeSubMenu: {
    submenu: '//*[@id="subtab-AdminParentAttributesGroups"]/a',
    add_new_attribute: '//*[@id="page-header-desc-attribute_group-new_attribute_group"]',
    name_input: '//*[@id="name_1"]',
    public_name_input: '//*[@id="public_name_1"]',
    type_select: '//*[@id="group_type"]',
    save_button: '//*[@id="attribute_group_form_submit_btn"]',
    search_input: '//*[@id="table-attribute_group"]/thead/tr[2]/th[3]/input',
    search_button: '//*[@id="submitFilterButtonattribute_group"]',
    selected_attribute: '//*[@id="table-attribute_group"]/tbody/tr/td[3]',
    add_value_button: '//*[@id="page-header-desc-attribute-new_value"]',
    save_and_add: '//*[@id="fieldset_0"]/div[3]/button[2]',
    save_value_button: '//*[@id="attribute_form_submit_btn"]',
    value_input: '//*[@id="name_1"]',
    value_action_group_button: '(//*[@id="table-attribute"]//div[contains(@class, "btn-group")]/button)[1]',
    delete_value_button: '(//*[@id="table-attribute"]//a[@class="delete"])[1]',
    group_action_button: '//*[@id="table-attribute_group"]//button[@data-toggle="dropdown"]',
    delete_attribute_button: '//*[@id="table-attribute_group"]//a[@class="delete"]',
    update_button: '//*[@id="table-attribute_group"]//a[@class="edit"]',
    update_value_button: '(//*[@id="table-attribute"]//a[@title="Edit"])[%POS]',
    reset_button: '//*[@id="table-attribute_group"]//button[@name="submitResetattribute_group"]',
    attribute_checkbox: '//*[@id="table-attribute_group"]//input[@type="checkbox"]',
    bulk_actions: '//*[@id="bulk_action_menu_attribute_group"]',
    delete_bulk_action: '//*[@id="form-attribute_group"]//div[contains(@class,"bulk-actions")]//li[4]/a'
  }

};
