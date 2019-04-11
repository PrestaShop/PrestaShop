module.exports = {
  DiscountSubMenu: {
    catalogPriceRules: {
      catalog_price_rules_subtab: '#content a [text()="Catalog Price Rules"]',
      new_catalog_price_rules_button: '#page-header-desc-specific_price_rule-new_specific_price_rule ',
      name_input: '#name ',
      reduction_type_select: '#reduction_type ',
      reduction_input: '#reduction ',
      save_button: '#specific_price_rule_form_submit_btn ',
      form_quantity: '#from_quantity ',
      search_name_input: '#form-specific_price_rule input[contains(name,"specific_price_ruleFilter_a!name")]',
      search_button: '#submitFilterButtonspecific_price_rule ',
      dropdown_button: '#form-specific_price_rule button[contains(data-toggle,"dropdown")]):nth-child(1)',
      delete_button: '#form-specific_price_rule a .delete',
      success_delete_message: 'div.alert-success:nth-child(1)'
    },
    cartRules: {
      new_cart_rule_button: '#page-header-desc-cart_rule-new_cart_rule',
      name_input: '#name_1',
      generate_button: '#cart_rule_informations  div:nth-child(3) a',
      code_input: '#code',
      conditions_tab: '#cart_rule_link_conditions',
      single_customer_input: '#customerFilter',
      first_result_option: 'li.ac_even.ac_over',
      /*selector to click on the first result option(customer or product)*/
      minimum_amount_input: '#cart_rule_conditions input[name=minimum_amount]',
      actions_tab: '#cart_rule_link_actions',
      free_shipping: '#cart_rule_actions label[for=free_shipping_on]',
      /*selector for setting the free shipping to yes*/
      apply_discount_radio: '#apply_discount_%T',
      /*selector to apply a discount by percent or amount*/
      reduction_input: '#reduction_%T',
      apply_discount_to_product_radio: '#apply_discount_to_product ',
      reduction_product_filter: '#reductionProductFilter ',
      save_button: '#desc-cart_rule-save',
      search_name_input: '#form-cart_rule input[contains(name,"cart_ruleFilter_name")]',
      search_button: '#submitFilterButtoncart_rule ',
      dropdown_button: '#form-cart_rule button[contains(data-toggle,"dropdown")]:nth-child(1)',
      delete_button: '#form-cart_rule a .delete',
      success_delete_message: '#content div.bootstrap div.success',
      filter_name_input: '#table-cart_rule input[name="cart_ruleFilter_name"]',
      filter_search_button: '#submitFilterButtoncart_rule ',
      edit_button: '#table-cart_rule a [title="Edit"]',
      highlight_button: '#cart_rule_informations > div:nth-of-type(4) label[for=highlight_%S]',
      partial_use_button: '#cart_rule_informations > div:nth-of-type(5) label[for=partial_use_%S]',
      free_shipping_button: '#cart_rule_actions > div:nth-of-type(1) label[for=free_shipping_%S]'
    }
  }
};
