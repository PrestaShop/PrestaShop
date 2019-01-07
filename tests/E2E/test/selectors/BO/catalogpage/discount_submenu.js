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
    },
    cartRules: {
      new_cart_rule_button: '//*[@id="page-header-desc-cart_rule-new_cart_rule"]',
      name_input: '//*[@id="name_1"]',
      generate_button: '//*[@id="cart_rule_informations"]//a[text()=" Generate"]',
      code_input: '//*[@id="code"]',
      conditions_tab: '//*[@id="cart_rule_link_conditions"]',
      single_customer_input: '//*[@id="customerFilter"]',
      first_result_option: '//li[contains(@class, "ac_even ac_over")]',// selector to click on the first result option(customer or product)
      minimum_amount_input: '//*[@id="cart_rule_conditions"]//input[@name="minimum_amount"]',
      actions_tab: '//*[@id="cart_rule_link_actions"]',
      free_shipping: '//*[@id="cart_rule_actions"]//label[@for="free_shipping_on"]', //selector for setting the free shipping to yes
      apply_discount_radio: '//*[@id="apply_discount_%T"]', //selector to apply a discount by percent or amount
      reduction_input: '//*[@id="reduction_%T"]',
      apply_discount_to_product_radio: '//*[@id="apply_discount_to_product"]',
      reduction_product_filter: '//*[@id="reductionProductFilter"]',
      save_button: '//*[@id="desc-cart_rule-save"]',
      search_name_input:'//*[@id="form-cart_rule"]//input[contains(@name,"cart_ruleFilter_name")]',
      search_button:'//*[@id="submitFilterButtoncart_rule"]',
      dropdown_button:'(//*[@id="form-cart_rule"]//button[contains(@data-toggle,"dropdown")])[1]',
      delete_button:'//*[@id="form-cart_rule"]//a[contains(@class,"delete")]',
      success_delete_message:'//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
      filter_name_input: '//*[@id="table-cart_rule"]//input[@name="cart_ruleFilter_name"]',
      filter_search_button: '//*[@id="submitFilterButtoncart_rule"]',
      edit_button: '//*[@id="table-cart_rule"]//a[@title="Edit"]'
    }
  }
};