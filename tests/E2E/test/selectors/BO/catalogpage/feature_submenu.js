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
  FeatureSubMenu: {
    add_new_feature: '//*[@id="page-header-desc-feature-new_feature"]',
    name_input: '//*[@id="name_1"]',
    save_button: '//*[@id="feature_form_submit_btn"]',
    search_input: '//*[@id="table-feature"]//th//input[@name="featureFilter_%SEARCHBY"]',
    search_button: '//*[@id="submitFilterButtonfeature"]',
    selected_feature: '//*[@id="table-feature"]//a[@title="View"]',
    feature_select: '//*[@id="id_feature"]',
    add_value_button: '//*[@id="page-header-desc-feature_value-new_feature_value"] | //*[@id="page-header-desc-feature-new_feature_value"]',
    value_input: '//*[@id="value_1"]',
    save_value_button: '//*[@id="feature_value_form_submit_btn"]',
    select_option: '//*[@id="table-feature"]//button[@data-toggle="dropdown"]',
    update_feature_button: '//*[@id="table-feature"]//a[@class="edit"]',
    update_feature_value_button: '(//*[@id="table-feature_value"]//a[contains(@class, "edit")])[%ID]',
    delete_feature: '//*[@id="table-feature"]//a[@class="delete"]',
    reset_button: '//*[@id="table-feature"]//button[@name="submitResetfeature"]',
    feature_checkbox: '//*[@id="table-feature"]//input[@type="checkbox"]',
    feature_bulk_actions: '//*[@id="bulk_action_menu_feature"]',
    feature_delete_bulk_action: '//*[@id="form-feature"]//div[contains(@class,"bulk-actions")]//li[4]/a',
    save_then_add_another_value_button:'//*[@name="submitAddfeature_valueAndStay"]',
    dropdown_option: '(//*[@id="table-feature_value"]//td[4]//button)[%ID]',
    delete_button:'(//*[@id="table-feature_value"]//td[4]//a[contains(@onclick,"Delete")])[1]',
    view_button:'//*[@id="table-feature"]//td[6]//a[@title="View"]',
    features_number_span: '//*[@id="form-feature"]//span[@class="badge"]',
    value_search_input: '//*[@id="table-feature_value"]//input[@name="feature_valueFilter_%SEARCHBY"]',
    value_search_button: '//*[@id="submitFilterButtonfeature_value"]',
    value_column: '//*[@id="table-feature_value"]/tbody/tr[%ID]/td[%B]',
    value_reset_button: '//*[@id="table-feature_value"]//button[@name="submitResetfeature_value"]',
    back_to_list_button: '//*[@id="desc-feature_value-back"]',
    feature_table: '//*[@id="table-feature"]',
    feature_column: '//*[@id="table-feature"]/tbody/tr[%ID]/td[%B]',
    feature_sort_icon: '//*[@id="table-feature"]//span[contains(text(),"%B")]/a[%W]'
  }
};
