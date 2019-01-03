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
