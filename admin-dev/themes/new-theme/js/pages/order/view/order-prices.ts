/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default class OrderPrices {
  calculateTaxExcluded(taxIncluded: number, taxRatePerCent: number, currencyPrecision: number): number {
    let priceTaxIncl = taxIncluded;

    if (priceTaxIncl < 0 || Number.isNaN(priceTaxIncl)) {
      priceTaxIncl = 0;
    }
    const taxRate = taxRatePerCent / 100 + 1;

    return window.ps_round(priceTaxIncl / taxRate, currencyPrecision);
  }

  calculateTaxIncluded(taxExcluded: number, taxRatePerCent: number, currencyPrecision: number): number {
    let priceTaxExcl = taxExcluded;

    if (priceTaxExcl < 0 || Number.isNaN(priceTaxExcl)) {
      priceTaxExcl = 0;
    }
    const taxRate = taxRatePerCent / 100 + 1;

    return window.ps_round(priceTaxExcl * taxRate, currencyPrecision);
  }

  calculateTotalPrice(quantity: number, unitPrice: number, currencyPrecision: number): number {
    return window.ps_round(unitPrice * quantity, currencyPrecision);
  }
}
