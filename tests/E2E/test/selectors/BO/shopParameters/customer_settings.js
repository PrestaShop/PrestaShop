module.exports = {
  CustomerSettings: {
    groups: {
      filter_name_input: '//*[@id="table-group"]//input[@name="groupFilter_b!name"]',
      filter_search_button: '//*[@id="submitFilterButtongroup"]',
      edit_button: '//*[@id="table-group"]//a[@title="Edit"]',
      save_button: '//*[@id="group_form_submit_btn"]',
      price_display_method_select: '//*[@id="price_display_method"]',
      group_button: '//*[@id="subtab-AdminGroups"]',
      customer_edit_button: '(//*[@id="table-group"]//a[@title="Edit"])[3]',
      price_display_method: '//*[@id="price_display_method"]'
    }
  }
};
