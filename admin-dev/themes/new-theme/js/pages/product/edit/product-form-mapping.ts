/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
