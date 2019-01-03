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
  SearchProductPage: {
    search_input: '.ui-autocomplete-input',
    search_button: '.material-icons.search',
    product_result_name: '.h3.product-title > a',
    product_result_discount: '//*[@id="js-product-list"]//span[contains(@class, "discount-percentage")]',
    attribute_name: '//*[@id="add-to-cart-or-refresh"]//div[contains (@class, "product-variants-item")]/span',
    feature_name: '//*[@id="product-details"]/section/dl/dt[@class="name"]',
    feature_value: '//*[@id="product-details"]/section/dl/dd',
    attribute_select_values: '//*[@id="group_%ID"]/option',
    attribute_radio_values: '//*[@id="add-to-cart-or-refresh"]/div[@class="product-variants"]//li//span[contains(@class, "radio-label")]',
    attribute_color_and_texture_values: '//*[@id="add-to-cart-or-refresh"]/div[@class="product-variants"]//li//span[contains(@class, "sr-only")]',
    quick_view_first_product: '//*[@id="js-product-list"]//a[contains(@class,"quick-view")]'
  }
};
