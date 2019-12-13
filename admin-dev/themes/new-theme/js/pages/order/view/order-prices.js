/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


export default class OrderPrices {
  calculateTaxExcluded(taxIncluded, taxRatePerCent) {
    let priceTaxIncl = parseFloat(taxIncluded);
    if (priceTaxIncl < 0 || isNaN(priceTaxIncl)) {
      priceTaxIncl = 0;
    }
    const taxRate = taxRatePerCent / 100 + 1;
    return ps_round(priceTaxIncl / taxRate, 2);
  }

  calculateTaxIncluded(taxExcluded, taxRatePerCent) {
    let priceTaxExcl = parseFloat(taxExcluded);
    if (priceTaxExcl < 0 || isNaN(priceTaxExcl)) {
      priceTaxExcl = 0;
    }
    const taxRate = taxRatePerCent / 100 + 1;
    return ps_round(priceTaxExcl * taxRate, 2);
  }

  calculateTotalPrice(quantity, unitPrice) {
    return ps_round(unitPrice * quantity, 2);
  }
}
