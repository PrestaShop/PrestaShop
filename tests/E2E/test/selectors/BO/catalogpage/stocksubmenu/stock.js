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
  Stock: {
    product_quantity_input: '(//*[@id="app"]//div[contains(@class,"edit-qty")])[%O]/input',
    product_quantity: '//*[@id="app"]//tr[%O]/td[7]',
    product_quantity_modified: '(//*[@id="app"]//tr[%O]//span[contains(@class,"qty-update")])[1]',
    save_product_quantity_button: '(//*[@id="app"]//button[contains(@class,"check-button")])[1]',
    group_apply_button: '//*[@id="app"]//button[contains(@class,"update-qty")]',
    add_quantity_button: '(//*[@id="app"]//span[contains(@class,"ps-number-up")])[%ITEM]',
    remove_quantity_button: '(//*[@id="app"]//span[contains(@class,"ps-number-down")])[%ITEM]',
    success_panel: '//*[@id="growls"]',
    search_input: '(//*[@id="search"]//input[contains(@class,"input")])[1]',
    search_button: '//*[@id="search"]//button[contains(@class,"search-button")]',
    sort_product_icon: '//*[@id="app"]//table//div[contains(@data-sort-direction,"asc")]',
    check_sign: '//*[@id="app"]//button[@class="check-button"]',
    physical_column: '//*[@id="app"]//div//table[@class="table"]//tr[%ID]//td[5]',
    green_validation: '//*[@id="search"]/div[2]/div/button',
    product_column: '//*[@id="app"]//div/table[@class="table"]//tr[%O]/td[1]',
    available_column: '//*[@id="app"]//div//table[@class="table"]//tr[%ID]/td[7]',
    reference_product_column: '//*[@id="app"]//div/table[@class="table"]//tr[%O]/td[2]',
    employee_column: '//*[@id="app"]//div/table[@class="table"]//tr[%O]/td[6]',
    product_selector: '//*[@id="app"]//table//tr//p[contains(text(),"%ProductName")]',
    success_hidden_panel: '//*[@id="search"]//div[contains(@class,"alert-box")]//div[contains(@class,"alert-success")]/p/span'
  }
};
