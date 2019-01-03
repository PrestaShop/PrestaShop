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
  CustomerServicePage: {
    email_filter_input: '//*[@id="table-customer_thread"]//input[@name="customer_threadFilter_a!email"]',
    search_button: '//*[@id="submitFilterButtoncustomer_thread"]',
    reset_button: '//*[@id="table-customer_thread"]//button[@name="submitResetcustomer_thread"]',
    dropdown_button: '//*[@id="table-customer_thread"]//button[@data-toggle="dropdown"]',
    view_button: '//*[@id="table-customer_thread"]//a[@title="View"]',
    delete_button: '//*[@id="table-customer_thread"]//a[@title="Delete"]',
    success_panel: '//*[@id="content"]//div[contains(@class,"alert-success") and not(contains(@class,"hide"))]',
    email_sender_text: '//*[@id="content"]//div[contains(@class, "media-body")]//h2',
    email_receive_text: '//*[@id="content"]//div[contains(@class, "media-body")]//span[@class="badge"]',
    message_text: '//*[@id="content"]//div[@class="message-body"]//p'
  }
};