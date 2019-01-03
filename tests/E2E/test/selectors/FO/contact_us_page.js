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
  ContactUsPageFO: {
    contact_us_button: '//*[@id="contact-link"]/a',
    subject_select: '//*[@id="content"]//select[@name="id_contact"]',
    subject_select_option: '//*[@id="content"]//select[@name="id_contact"]/option[@value="%V"]',
    email_address_input: '//*[@id="content"]//input[@name="from"]',
    attachment_file: '//*[@id="filestyle-0"]',
    message_textarea: '//*[@id="content"]//textarea[@name="message"]',
    send_button: '//*[@id="content"]//input[@type="submit"]',
    success_panel: '//*[@id="content"]//div[contains(@class,"alert-success")]'
  }
};