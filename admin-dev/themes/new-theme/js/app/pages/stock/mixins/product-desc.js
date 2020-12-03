/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
export default {
  computed: {
    thumbnail() {
      if (this.product.combination_thumbnail !== 'N/A') {
        return `${this.product.combination_thumbnail}`;
      } else if (this.product.product_thumbnail !== 'N/A') {
        return `${this.product.product_thumbnail}`;
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
