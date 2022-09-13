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
  'price.priceTaxExcluded': 'product[pricing][retail_price][price_tax_excluded]',
  'price.overrideAllPriceTaxExcluded': [
    'product[pricing][retail_price][modify_all_shops_price_tax_excluded]',
    'product[pricing][retail_price][modify_all_shops_price_tax_included]',
  ],
  'price.priceTaxIncluded': 'product[pricing][retail_price][price_tax_included]',
  'price.taxRulesGroupId': 'product[pricing][retail_price][tax_rules_group_id]',
  'price.wholesalePrice': 'product[pricing][wholesale_price]',
  'price.unitPriceTaxExcluded': 'product[pricing][unit_price][price_tax_excluded]',
  'price.unitPriceTaxIncluded': 'product[pricing][unit_price][price_tax_included]',
  'price.unity': 'product[pricing][unit_price][unity]',
  'price.ecotaxTaxExcluded': 'product[pricing][retail_price][ecotax_tax_excluded]',
  'price.ecotaxTaxIncluded': 'product[pricing][retail_price][ecotax_tax_included]',
  'price.overrideAllUnitPriceTaxExcluded': [
    'product[pricing][unit_price][modify_all_shops_price_tax_excluded]',
    'product[pricing][unit_price][modify_all_shops_price_tax_included]',
  ],
  'stock.hasVirtualProductFile': 'product[stock][virtual_product_file][has_file]',
  'seo.overrideAllRedirectOption': [
    'product[seo][redirect_option][modify_all_shops_type]',
    'product[seo][redirect_option][modify_all_shops_target]',
  ],
  'suppliers.defaultSupplierId': 'product[options][suppliers][default_supplier_id]',
};
