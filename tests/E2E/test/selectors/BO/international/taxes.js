module.exports = {
  Taxes:{
    taxRules: {
      add_new_tax_rules_group_button: '//*[@id="page-header-desc-tax_rules_group-new_tax_rules_group"]',
      name_input: '//*[@id="name"]',
      enable_button: '//*[@id="fieldset_0"]//span/label[@for="active_on"]',
      save_and_stay_button: '//*[@id="tax_rules_group_form_submit_btn"]',
      tax_select: '//*[@id="id_tax"]',
      save_button: '//*[@id="tax_rule_form_submit_btn_1"]',
      success_alert: '(//div[contains(@class,"alert-success")])[1]',
      filter_name_input: '//*[@id="table-tax_rules_group"]//input[@name="tax_rules_groupFilter_name"]',
      filter_search_button: '//*[@id="submitFilterButtontax_rules_group"]',
      edit_button: '//*[@id="table-tax_rules_group"]//tr[1]//a[@title="Edit"]',
      dropdown_button: '//*[@id="table-tax_rules_group"]/tbody//button[@data-toggle="dropdown"]',
      delete_button: '//*[@id="table-tax_rules_group"]/tbody//a[@title="Delete"]'
    }
  }
};