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
  ThemeCatalog: {
    discover_all_of_the_theme_button: '//*[@id="content" | @id="main-div"]//a[contains(text(), "Discover all of the themes")]',
    category_name_text: '//*[@id="category_name"]',
    discover_button: '(//*[@id="content" | @id="main-div"]//a[1]//p[contains(text(), "Discover")])[%POS]',
    theme_name: '(//*[@id="content" | @id="main-div"]//a[1]//p[@class="bold"])[%POS]',
    search_addons_input: '//*[@id="addons-search-box"]',

    //Selectors in addons.prestashop.com site
    search_name: '//*[@id="search_name"]/b',
    theme_header_name: '//*[@id="product_content"]//h1'
  }
};