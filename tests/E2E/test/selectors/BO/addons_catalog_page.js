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
  ModulesCatalogPage: {
    view_all_traffic_modules_link: '//*[@id="main-div"]//div[contains(@class, "addons-module-traffic-block")]//a[contains(text(), "View all the Traffic modules")]',
    category_name_text: '//*[@id="category_name"]',
    discover_button: '//*[@id="main-div"]//div[contains(@class, "addons-module-traffic-block")]//a[@title="SEO Expert"]',
    module_name: '//*[@id="product_content"]//h1',
    discover_payment_modules_link: '//*[@id="main-div"]//div[contains(@class, "addons-module-selection-ps")]//a[contains(text(), "Discover the payment modules")]',
    view_all_modules_button: '//*[@id="main-div"]//div[contains(@class, "addons-all-modules")]//a[contains(text(), "View all modules")]',
    prestashop_addons_logo: '//*[@id="logo"]',
    search_addons_input: '//*[@id="addons-search-box"]',
    search_name: '//*[@id="search_name"]/b'
  }
};