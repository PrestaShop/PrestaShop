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
  Pages: {
    Common: {
      name_input: '//*[@id="name_1"]',
      meta_description_input: '//*[@id="meta_description_1"]',
      enable_display_option: '//label[@for="active_on"]',
      success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
      alert_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "alert")]'
    },
    Category: {
      add_category_button: '//*[@id="page-header-desc-cms-new_cms_category"]',
      parent_category_select_option: '//select[@name="id_parent"]/option[contains(text(), "%CATEGORY_NAME")]',
      parent_category_select: '//select[@name="id_parent"]',
      description_textarea: '//*[@id="description_1"]',
      meta_title_input: '//*[@id="meta_title_1"]',
      meta_keywords_input: '//*[@id="meta_keywords_1"]',
      parent_category: '//*[@id="cms_category_form"]//select[@name="id_parent"]',
      save_button: '//*[@id="cms_category_form_submit_btn"]',
      name_filter: '//*[@id="table-cms_category"]//input[@name="cms_categoryFilter_name"]',
      view_button: '//*[@id="table-cms_category"]//a[@title="View"]',
      dropdown_toggle: '//*[@id="table-cms_category"]//button[@data-toggle="dropdown"]',
      edit_button: '//*[@id="table-cms_category"]//a[@title="Edit"]',
      search_name_result: '//*[@id="table-cms_category"]//td[%ID]',
      delete_button: '//*[@id="table-cms_category"]//a[@title="Delete"]',
      bulk_actions_button: '//*[@id="bulk_action_menu_cms_category"]',
      bulk_actions_select_all_button: '//*[@id="form-cms_category"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "true")]',
      bulk_actions_delete_button: '//*[@id="form-cms_category"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "submitBulkdeletecms_category")]',
      bulk_actions_disable_button: '//*[@id="form-cms_category"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "submitBulkdisableSelectioncms_category")]',
      bulk_actions_enable_button: '//*[@id="form-cms_category"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "submitBulkenableSelectioncms_category")]',
      home_icon: '//*[@id="content"]//i[@class="icon-home"]'
    },
    Page: {
      page_category: '//select[@name="id_cms_category"]',
      add_new_page_button: '//*[@id="page-header-desc-cms-new_cms_page"]',
      meta_keywords_input: '//div[@class="tagify-container"]//input',
      delete_tag_button: '//div[@class="tagify-container"]//span[%POS]/a',
      enable_indexation_option: '//label[@for="indexation_on"]',
      save_button: '//*[@id="cms_form_submit_btn"]',
      save_and_preview_button: '//button[contains(@name, "viewcms")]',
      title_filter_input: '//input[@name="cmsFilter_b!meta_title"]',
      search_title_result: '//*[@id="table-cms"]//td[%ID]',
      edit_button: '//*[@id="table-cms"]//a[@title="Edit"]',
      dropdown_toggle: '//*[@id="table-cms"]//button[@data-toggle="dropdown"]',
      delete_button: '//*[@id="table-cms"]//a[@title="Delete"]',
      page_content: '//*[@id="cms_form"]//div[@class="mce-tinymce mce-container mce-panel"]',
      bulk_actions_button: '//*[@id="bulk_action_menu_cms"]',
      bulk_actions_select_all_button: '//*[@id="form-cms"]//div[contains(@class,\'bulk-actions\')]//ul[@class="dropdown-menu"]//a[contains(@onclick, "true")]',
      bulk_actions_delete_button: '//*[@id="form-cms"]//div[contains(@class,\'bulk-actions\')]//ul[@class="dropdown-menu"]//a[contains(@onclick, "submitBulkdeletecms")]',
      bulk_actions_disable_button: '//*[@id="form-cms"]//div[contains(@class,\'bulk-actions\')]//ul[@class="dropdown-menu"]//a[contains(@onclick, "submitBulkdisableSelectioncms")]',
      bulk_actions_enable_button: '//*[@id="form-cms"]//div[contains(@class,\'bulk-actions\')]//ul[@class="dropdown-menu"]//a[contains(@onclick, "submitBulkenableSelectioncms")]',
      reset_button: '//button[@name="submitResetcms"]',
      category_option: '//select/option[contains(text(), "%CATEGORY_NAME")]',
      cancel_button: '//*[@id="cms_form_cancel_btn"]',
    }
  }
};