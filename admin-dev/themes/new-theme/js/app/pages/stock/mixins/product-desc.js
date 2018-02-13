/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
export default {
  computed: {
    thumbnail() {
      if (this.product.combination_thumbnail !== 'N/A') {
        return `${window.data.baseUrl}/${this.product.combination_thumbnail}`;
      } else if (this.product.product_thumbnail !== 'N/A') {
        return `${window.data.baseUrl}/${this.product.product_thumbnail}`;
      }
      return null;
    },
    combinationName() {
      const arr = this.product.combination_name.split(',');
      let attr = '';
      arr.forEach((attribute) => {
        const value = attribute.split('-');
        attr += attr.length ? ` - ${value[1]}` : value[1];
      });
      return attr;
    },
    hasCombination() {
      return !!this.product.combination_id;
    },
  },
};
