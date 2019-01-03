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
  AttributeSubMenu: {
    submenu: '//*[@id="subtab-AdminParentAttributesGroups"]/a',
    add_new_attribute: '//*[@id="page-header-desc-attribute_group-new_attribute_group"]',
    name_input: '//*[@id="name_1"]',
    public_name_input: '//*[@id="public_name_1"]',
    type_select: '//*[@id="group_type"]',
    save_button: '//*[@id="attribute_group_form_submit_btn"]',
    search_input: '//*[@id="table-attribute_group"]/thead/tr[2]/th[3]/input',
    search_button: '//*[@id="submitFilterButtonattribute_group"]',
    attribute_name: '//*[@id="table-attribute_group"]/tbody/tr/td[3]',
    add_value_button: '//*[@id="page-header-desc-attribute-new_value"]',
    save_and_add_button: '//*[@id="fieldset_0"]/div[3]/button[2]',
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
    bulk_actions_button: '//*[@id="bulk_action_menu_attribute_group"]',
    delete_bulk_action_button: '//*[@id="form-attribute_group"]//div[contains(@class,"bulk-actions")]//li[4]/a',
    attribute_id: '//*[@id="table-attribute_group"]/tbody/tr/td[2]',
    color_input: '//*[@id="color_0"]',
    texture_input_file: '//*[@id="texture"]'
  }

};
