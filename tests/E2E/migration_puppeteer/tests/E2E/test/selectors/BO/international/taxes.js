module.exports = {
  Taxes: {
    taxRules: {
      add_new_tax_rules_group_button: '#page-header-desc-tax_rules_group-new_tax_rules_group',
      name_input: '#name',
      enable_button: '#fieldset_0 span label[for="active_on"]',
      save_and_stay_button: '#tax_rules_group_form_submit_btn',
      tax_select: '#id_tax',
      save_button: '#tax_rule_form_submit_btn_1',
      filter_name_input: '#table-tax_rules_group input[name="tax_rules_groupFilter_name"]',
      filter_search_button: '#submitFilterButtontax_rules_group',
      edit_button: '#table-tax_rules_group tr:nth-child(1) a [title="Edit"]',
      dropdown_button: '#table-tax_rules_group tbody button[data-toggle="dropdown"]',
      delete_button: '#table-tax_rules_group tbody a [title="Delete"]',
      bulk_action_button: '#bulk_action_menu_tax_rules_group',
      action_group_button: '#form-tax_rules_group div.bulk-actions a:nth-child(%ID)',
      tax_field_column: '#table-tax_rules_group tr:nth-child(%L) td:nth-child(%C)'
    },
    taxes: {
      filter_name_input: '#table-tax input[name="taxFilter_name"]',
      filter_search_button: '#submitFilterButtontax',
      tax_field_column: '#table-tax tr:nth-child(%L) td:nth-child(%C)',
      display_tax: '#conf_id_PS_TAX_DISPLAY label[for="PS_TAX_DISPLAY_%D"]',
      save_button: '#tax_fieldset_general div:nth-child(3) button',

    }
  }
};
