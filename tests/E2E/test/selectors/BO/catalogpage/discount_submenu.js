module.exports = {
  DiscountSubMenu: {
    catalogPriceRules: {
      catalog_price_rules_subtab: '//*[@id="content"]//a[text()="Catalog Price Rules"]',
      new_catalog_price_rules_button: '//*[@id="page-header-desc-specific_price_rule-new_specific_price_rule"]',
      name_input: '//*[@id="name"]',
      reduction_type_select: '//*[@id="reduction_type"]',
      reduction_input: '//*[@id="reduction"]',
      save_button: '//*[@id="specific_price_rule_form_submit_btn"]',
      form_quantity: '//*[@id="from_quantity"]',
      search_name_input:'//*[@id="form-specific_price_rule"]//input[contains(@name,"specific_price_ruleFilter_a!name")]',
      search_button:'//*[@id="submitFilterButtonspecific_price_rule"]',
      dropdown_button:'(//*[@id="form-specific_price_rule"]//button[contains(@data-toggle,"dropdown")])[1]',
      delete_button:'//*[@id="form-specific_price_rule"]//a[contains(@class,"delete")]',
      success_delete_message:'(//div[contains(@class,"alert-success")])[1]'
    }
  }
};