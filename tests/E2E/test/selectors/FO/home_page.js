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
  HomePage: {
    home_page: '//*[@id="index"]',
    contact_us_link: '//*[@id="header"]/nav/div/div/div[1]/div[1]',
    logo_home_page: '//*[@id="_desktop_logo"]',
    top_menu_page: '//*[@id="_desktop_top_menu"]/..',
    search_widget: '//*[@id="search_widget"]',
    all_product_link: '//*[@id="content"]/section/a',
    newsletter_block: '//*[@id="footer"]/div[1]/div/div[1]',
    products_block: '//*[@id="footer"]/div[2]/div/div[1]/div[1]/div/div[1]',
    our_campany_block: '//*[@id="footer"]/div[2]/div/div[1]/div[1]/div/div[2]',
    your_account_block: '//*[@id="block_myaccount_infos"]',
    store_information_block: '//*[@id="footer"]/div[2]/div/div[1]/div[3]'
  }
};