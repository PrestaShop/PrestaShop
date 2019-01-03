/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
module.exports = {
  Taxes: {
    taxRules: {
      add_new_tax_rules_group_button: '//*[@id="page-header-desc-tax_rules_group-new_tax_rules_group"]',
      name_input: '//*[@id="name"]',
      enable_button: '//*[@id="fieldset_0"]//span/label[@for="active_on"]',
      save_and_stay_button: '//*[@id="tax_rules_group_form_submit_btn"]',
      tax_select: '//*[@id="id_tax"]',
      save_button: '//*[@id="tax_rule_form_submit_btn_1"]',
      filter_name_input: '//*[@id="table-tax_rules_group"]//input[@name="tax_rules_groupFilter_name"]',
      filter_search_button: '//*[@id="submitFilterButtontax_rules_group"]',
      edit_button: '//*[@id="table-tax_rules_group"]//tr[1]//a[@title="Edit"]',
      dropdown_button: '//*[@id="table-tax_rules_group"]/tbody//button[@data-toggle="dropdown"]',
      delete_button: '//*[@id="table-tax_rules_group"]/tbody//a[@title="Delete"]',
      bulk_action_button: '//*[@id="bulk_action_menu_tax_rules_group"]',
      action_group_button: '(//*[@id="form-tax_rules_group"]//div[contains(@class, "bulk-actions")]//a)[%ID]',
      tax_field_column: '//*[@id="table-tax_rules_group"]//tr[%L]//td[%C]'
    },
    taxes: {
      filter_name_input: '//*[@id="table-tax"]//input[@name="taxFilter_name"]',
      filter_search_button: '//*[@id="submitFilterButtontax"]',
      tax_field_column: '//*[@id="table-tax"]//tr[%L]//td[%C]'
    }
  }
};
