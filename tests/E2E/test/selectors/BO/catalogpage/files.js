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
  Files: {
    add_new_file_button: '//*[@id="page-header-desc-attachment-new_attachment"]',
    filename_input: '//*[@id="name_1"]',
    description_textarea: '//*[@id="description_1"]',
    file: '//*[@id="file"]',
    save_button: '//*[@id="attachment_form_submit_btn"]',
    filter_name_input: '//*[@id="table-attachment"]/thead//input[@name="attachmentFilter_name"]',
    filter_size_input: '//*[@id="table-attachment"]/thead//input[@name="attachmentFilter_file_size"]',
    filter_associated_input: '//*[@id="table-attachment"]/thead//input[@name="attachmentFilter_virtual_product_attachment!products"]',
    filter_search_button: '//*[@id="submitFilterButtonattachment"]',
    filter_reset_button: '//*[@id="table-attachment"]/thead//button[@name="submitResetattachment"]',
    edit_button: '//*[@id="table-attachment"]/tbody//a[@title="Edit"]',
    dropdown_button: '//*[@id="table-attachment"]/tbody//button[@data-toggle="dropdown"]',
    action_button: '//*[@id="table-attachment"]/tbody//a[@title="%B"]',
    bulk_action_button: '//*[@id="bulk_action_menu_attachment"]',
    bulk_actions_select_all_button: '//*[@id="form-attachment"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "true")]',
    bulk_actions_delete_button: '//*[@id="form-attachment"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "submitBulkdeleteattachment")]',
    files_number: '//*[@id="form-attachment"]//span[@class="badge"]',
    files_table: '//*[@id="table-attachment"]/tbody/tr[%R]/td[%D]',
    sort_by_icon: '//*[@id="table-attachment"]/thead/tr[1]/th[%H]//i[contains(@class, "%BY")]',
    empty_list: '//*[@id="table-attachment"]/tbody//td[@class="list-empty"]'
  }
};