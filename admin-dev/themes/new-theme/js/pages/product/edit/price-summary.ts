/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ProductFormModel from '@pages/product/edit/product-form-model';
import ProductMap from '@pages/product/product-map';
import BigNumber from '@node_modules/bignumber.js';
import {isUndefined} from '@components/typeguard';

/**
 * This component watches for product form changes in inputs related to price and display them in a summary block.
 */
export default class PriceSummary {
  private readonly productFormModel: ProductFormModel;

  private summaryContainer: HTMLElement | null;

  private priceTaxExcluded?: HTMLElement;

  private priceTaxIncluded?: HTMLElement;

  private unitPrice?: HTMLElement;

  private margin?: HTMLElement;

  private marginRate?: HTMLElement;

  private wholesalePrice?: HTMLElement;

  private priceTaxExcludedLabel: string;

  private priceTaxIncludedLabel: string;

  private unitPriceLabel: string;

  private marginLabel: string;

  private marginRateLabel: string;

  private wholesalePriceLabel: string;

  constructor(productFormModel: ProductFormModel) {
    this.productFormModel = productFormModel;

    this.summaryContainer = document.querySelector<HTMLElement>(ProductMap.priceSummary.container);

    this.priceTaxExcluded = this.getSummaryField(ProductMap.priceSummary.priceTaxExcluded);
    this.priceTaxIncluded = this.getSummaryField(ProductMap.priceSummary.priceTaxIncluded);
    this.unitPrice = this.getSummaryField(ProductMap.priceSummary.unitPrice);
    this.margin = this.getSummaryField(ProductMap.priceSummary.margin);
    this.marginRate = this.getSummaryField(ProductMap.priceSummary.marginRate);
    this.wholesalePrice = this.getSummaryField(ProductMap.priceSummary.wholesalePrice);

    this.priceTaxExcludedLabel = this.getSummaryLabel('priceTaxExcluded', '%price% tax excl.');
    this.priceTaxIncludedLabel = this.getSummaryLabel('priceTaxIncluded', '%price% tax incl.');
    this.unitPriceLabel = this.getSummaryLabel('unitPrice', '%price% %unity%');
    this.marginLabel = this.getSummaryLabel('margin', '%price% margin');
    this.marginRateLabel = this.getSummaryLabel('marginRate', '%margin_rate% margin rate');
    this.wholesalePriceLabel = this.getSummaryLabel('wholesalePrice', '%price% cost price');

    this.init();
  }

  private init(): void {
    const watchedFields: string[] = [
      'price.priceTaxExcluded',
      'price.priceTaxIncluded',
      'price.wholesalePrice',
      'price.unitPriceTaxExcluded',
      'price.unitPriceTaxIncluded',
      'price.unity',
    ];

    this.productFormModel.watch(watchedFields, () => this.updateSummary());
    this.updateSummary();
  }

  private updateSummary(): void {
    this.updateField(this.priceTaxIncluded, this.fillLabelWithPrice(this.priceTaxIncludedLabel, 'price.priceTaxIncluded'));
    this.updateField(this.wholesalePrice, this.fillLabelWithPrice(this.wholesalePriceLabel, 'price.wholesalePrice'));

    // Final price tax excluded is composed with price tax excluded and ecotax part
    const priceTaxExcluded: BigNumber = this.getBigNumber('price.priceTaxExcluded');
    const ecotaxTaxExcluded: BigNumber = this.getBigNumber('price.ecotaxTaxExcluded');
    const finalPriceTaxExcluded: BigNumber = priceTaxExcluded.plus(ecotaxTaxExcluded);
    this.updateField(
      this.priceTaxExcluded,
      this.priceTaxExcludedLabel.replace('%price%', this.productFormModel.displayPrice(finalPriceTaxExcluded)),
    );

    // Compute margin based on wholesale price
    const wholesalePrice = this.getBigNumber('price.wholesalePrice');
    const price:BigNumber = this.getBigNumber('price.priceTaxExcluded');
    const margin:BigNumber = price.minus(wholesalePrice);
    this.updateField(this.margin, this.marginLabel.replace('%price%', this.productFormModel.displayPrice(margin)));

    const marginRate:BigNumber = price.isZero() ? new BigNumber('-100') : margin.dividedBy(price).times(new BigNumber('100'));
    this.updateField(this.marginRate, this.marginRateLabel.replace('%margin_rate%', marginRate.toFixed(2)));

    // Unit price is composed of two fields and it is shown only when values are not empty
    const unitPrice = this.getBigNumber('price.unitPriceTaxExcluded');
    const {unity} = this.productFormModel.getProduct().price;

    if (unity !== '' && !unitPrice.isZero()) {
      const unitPriceLabel = this.fillLabelWithPrice(this.unitPriceLabel, 'price.unitPriceTaxExcluded');
      this.updateField(this.unitPrice, unitPriceLabel.replace('%unity%', unity));
      this.unitPrice?.classList.remove('d-none');
    } else {
      this.unitPrice?.classList.add('d-none');
    }
  }

  private updateField(summaryField: HTMLElement | undefined, content: string): void {
    if (isUndefined(summaryField)) {
      return;
    }

    // eslint-disable-next-line no-param-reassign
    summaryField.innerHTML = content;
  }

  private fillLabelWithPrice(label: string, priceModelKey: string): string {
    const price: BigNumber = this.getBigNumber(priceModelKey);

    return label.replace('%price%', this.productFormModel.displayPrice(price));
  }

  private getSummaryField(selector: string): HTMLElement | undefined {
    if (!this.summaryContainer) {
      return undefined;
    }

    return this.summaryContainer.querySelector<HTMLElement>(selector) ?? undefined;
  }

  private getSummaryLabel(labelName: string, defaultLabel: string): string {
    if (!this.summaryContainer) {
      return defaultLabel;
    }

    return this.summaryContainer.dataset[labelName] ?? defaultLabel;
  }

  private getBigNumber(modelKey: string): BigNumber {
    const bigNumber = this.productFormModel.getBigNumber(modelKey) ?? new BigNumber(0);

    // Value can be NaN if input value is invalid (like empty string or .)
    if (bigNumber.isNaN()) {
      return new BigNumber(0);
    }

    return bigNumber;
  }
};
