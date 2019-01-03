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
module.exports = Object.assign(
  {
    CatalogPage: {
      success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
      danger_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "danger")]',
      select_all_product_button: '//*[@id="bulk_action_select_all"]/..',
      action_group_button: '//*[@id="product_bulk_menu"]',
      action_button: '(//*[@id="main-div"]//div[contains(@class, "bulk-catalog")]//a)[%ID]',
      green_validation: '//*[@id="main-div"]//div[contains(@class, "alert-success") and not(@style)]',
      product_status_icon: '(//*[@id="product_catalog_list"]//tbody/tr[%S]//i[contains(@class, "material-icons")])[1]',
      name_search_input: '(//*[@id="product_catalog_list"]//input[contains(@name, "filter_column_name")])[1]',
      search_button: '//*[@id="product_catalog_list"]//button[contains(@name, "products_filter_submit")]',
      dropdown_toggle: '//*[@id="product_catalog_list"]//button[contains(@class, "dropdown-toggle")]',
      delete_button: '//*[@id="product_catalog_list"]//a[contains(@onclick, "delete")]',
      delete_confirmation: '//*[@id="catalog_deletion_modal"]//button[2]',
      close_delete_modal: '(//*[@id="catalog_deletion_modal"]//button[1])[2]',
      reset_button: '//*[@id="product_catalog_list"]//button[contains(@name, "products_filter_reset")]',
      search_result_message: '//*[@id="product_catalog_list"]//td[contains(text(), "There is no result for this search")]',
      deactivate_modal: '//*[@id="catalog_deactivate_all_modal"]',
      activate_modal: '//*[@id="catalog_activate_all_progression"]//div[contains(text(), "Activating")]',
      duplicate_modal: '//*[@id="catalog_duplicate_all_modal"]',
      delete_modal: '//*[@id="catalog_deletion_modal"]'
    }
  },
  require('./feature_submenu'),
  require('./category_submenu'),
  require('./attribute_submenu'),
  require('./Manufacturers'),
  require('./stocksubmenu'),
  require('./discount_submenu'),
  require('./files')
);
