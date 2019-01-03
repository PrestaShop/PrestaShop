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
  Employee:{
    new_employee_button: '#page-header-desc-employee-new_employee',
    first_name_input: '#firstname',
    last_name_input: '#lastname',
    email_input: '#email',
    password_input: '#passwd',
    profile_select: '#id_profile',
    save_button: '#employee_form_submit_btn',
    orders_page: '//*[@id="subtab-AdminParentOrders"]/a',
    email_search_input: '[name="employeeFilter_email"]',
    search_button_team: '#submitFilterButtonemployee',
    search_result: '.badge',
    team_employee_name: '//*[@id="form-employee"]//tbody//td[3]',
    team_employee_last_name: '//*[@id="form-employee"]//tbody//td[4]',
    team_employee_email: '//*[@id="form-employee"]//tbody//td[5]',
    team_employee_profile: '//*[@id="form-employee"]//tbody//td[6]',
    reset_search_button: '[name="submitResetemployee"]',
    dropdown_toggle: '//*[@id="form-employee"]//tbody//button',
    delete_link: '//*[@id="form-employee"]//tbody//li/a',
    edit_button: '//*[@id="table-employee"]//a[contains(@class, "edit")]',
    language_select: '//*[@id="id_lang"]'
  }
};