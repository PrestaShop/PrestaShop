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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import ObjectFormMapper, {FormUpdateEvent} from '@components/form/form-object-mapper';
import CombinationFormMapping from '@pages/product/combination/form/combination-form-mapping';
import {EventEmitter} from 'events';
import BigNumber from '@node_modules/bignumber.js';
import {NumberFormatter} from '@app/cldr';

export default class CombinationFormModel {
  private eventEmitter: EventEmitter;

  private mapper: ObjectFormMapper;

  private precision: number;

  private numberFormatter: NumberFormatter;

  constructor($form: JQuery, eventEmitter: EventEmitter) {
    this.eventEmitter = eventEmitter;
    // Init form mapper
    this.mapper = new ObjectFormMapper(
      $form,
      CombinationFormMapping,
    );

    // For now we get precision only in the component, but maybe it would deserve a more global configuration
    // BigNumber.set({DECIMAL_PLACES: someConfig}) But where can we define/inject this global config?
    const $priceTaxExcludedInput = this.mapper.getInputsFor('impact.priceTaxExcluded');
    // @ts-ignore
    this.precision = $priceTaxExcludedInput.data('displayPricePrecision');

    this.numberFormatter = NumberFormatter.build($priceTaxExcludedInput?.data('priceSpecification'));

    const pricesFields = [
      'impact.priceTaxExcluded',
      'impact.priceTaxIncluded',
      'impact.unitPriceTaxExcluded',
      'impact.unitPriceTaxIncluded',
      'price.ecotaxTaxExcluded',
      'price.ecotaxTaxIncluded',
      // This one has no impact but at least its value is guaranteed to be a number
      'price.wholesalePrice',
    ];
    this.mapper.watch(pricesFields, (event: FormUpdateEvent) => this.updateCombinationPrices(event));
    this.updateFinalPrices();
  }

  getCombination(): any {
    return this.mapper.getModel();
  }

  watch(modelKey: string, callback: (event: FormUpdateEvent) => void): void {
    this.mapper.watch(modelKey, callback);
  }

  set(modelKey: string, value: string | number | string[] | undefined): void {
    this.mapper.set(modelKey, value);
  }

  displayPrice(price: BigNumber): string {
    return this.numberFormatter.format(price.toNumber());
  }

  private updateCombinationPrices(event: FormUpdateEvent): void {
    // We don't allow invalid value which turn out to NaN values so we automatically replace them by 0
    if (new BigNumber(event.value).isNaN()) {
      event.stopPropagation();
      this.mapper.set(event.modelKey, new BigNumber(0).toFixed(this.precision));
      return;
    }

    const taxRatio = this.getTaxRatio();

    if (taxRatio.isNaN()) {
      return;
    }

    // eslint-disable-next-line default-case
    switch (event.modelKey) {
      // Regular retail price
      case 'impact.priceTaxIncluded': {
        const priceTaxIncluded = this.mapper.getBigNumber('impact.priceTaxIncluded') ?? new BigNumber(0);
        this.mapper.set('impact.priceTaxExcluded', priceTaxIncluded.dividedBy(taxRatio).toFixed(this.precision));
        break;
      }
      case 'impact.priceTaxExcluded': {
        const priceTaxExcluded = this.mapper.getBigNumber('impact.priceTaxExcluded') ?? new BigNumber(0);
        this.mapper.set('impact.priceTaxIncluded', priceTaxExcluded.times(taxRatio).toFixed(this.precision));
        break;
      }

      // Ecotax values
      case 'price.ecotaxTaxIncluded': {
        // Only this update is needed here, the rest will be updated via the trigger for price.ecotaxTaxExcluded
        const ecoTaxRatio = this.getEcoTaxRatio();
        const combinationEcotaxTaxIncluded = this.mapper.getBigNumber('price.ecotaxTaxIncluded') ?? new BigNumber(0);
        this.mapper.set('price.ecotaxTaxExcluded', combinationEcotaxTaxIncluded.dividedBy(ecoTaxRatio).toFixed(this.precision));

        break;
      }
      case 'price.ecotaxTaxExcluded': {
        // We first update the impact price before the final price is updated because it is important in the computing
        // since updateFinalPrices is called after each input update it would be triggered too soon if we start by updating
        // price.ecotaxTaxIncluded
        const ecoTaxRatio = this.getEcoTaxRatio();
        const combinationEcotaxTaxExcluded = this.mapper.getBigNumber('price.ecotaxTaxExcluded') ?? new BigNumber(0);
        const combinationEcotaxTaxIncluded = combinationEcotaxTaxExcluded.times(ecoTaxRatio);

        // We use this method which returns the product ecotax in case the combination one is not defined
        const ecotaxTaxIncluded = this.getEcotaxTaxIncluded(combinationEcotaxTaxIncluded);
        this.updateImpactForEcotax(ecotaxTaxIncluded);

        // Finally, we can update the price.ecotaxTaxIncluded
        this.mapper.set('price.ecotaxTaxIncluded', combinationEcotaxTaxIncluded.toFixed(this.precision));

        break;
      }

      // Unit price
      case 'impact.unitPriceTaxIncluded': {
        const unitPriceTaxIncluded = this.mapper.getBigNumber('impact.unitPriceTaxIncluded') ?? new BigNumber(0);
        this.mapper.set('impact.unitPriceTaxExcluded', unitPriceTaxIncluded.dividedBy(taxRatio).toFixed(this.precision));
        break;
      }
      case 'impact.unitPriceTaxExcluded': {
        const unitPriceTaxExcluded = this.mapper.getBigNumber('impact.unitPriceTaxExcluded') ?? new BigNumber(0);
        this.mapper.set('impact.unitPriceTaxIncluded', unitPriceTaxExcluded.times(taxRatio).toFixed(this.precision));
        break;
      }
    }

    this.updateFinalPrices();
  }

  /**
   * We compute the value impact price (with taxes) is supposed to have so that the current final price is not modified despite the change of ecotax
   */
  private updateImpactForEcotax(ecotaxTaxIncluded: BigNumber): void {
    const taxRatio: BigNumber = this.getTaxRatio();
    const currentFinalPriceTaxIncluded = this.mapper.getBigNumber('price.finalPriceTaxIncluded') ?? new BigNumber(0);
    const productPriceTaxExcluded: BigNumber = this.mapper.getBigNumber('product.priceTaxExcluded') ?? new BigNumber(0);
    const productPriceTaxIncluded: BigNumber = productPriceTaxExcluded.times(taxRatio);

    const impactPriceTaxIncluded: BigNumber = currentFinalPriceTaxIncluded
      .minus(ecotaxTaxIncluded)
      .minus(productPriceTaxIncluded);

    // Finally update the impact on price (without taxes)
    this.mapper.set('impact.priceTaxExcluded', impactPriceTaxIncluded.dividedBy(taxRatio).toFixed(this.precision));
  }

  private updateFinalPrices(): void {
    const taxRatio: BigNumber = this.getTaxRatio();
    const ecotaxRatio = this.getEcoTaxRatio();

    let productPriceTaxExcluded: BigNumber = this.mapper.getBigNumber('product.priceTaxExcluded') ?? new BigNumber(0);
    let impactTaxExcluded: BigNumber = this.mapper.getBigNumber('impact.priceTaxExcluded') ?? new BigNumber(0);
    let combinationEcotaxTaxExcluded: BigNumber = this.getEcotaxTaxExcluded();

    // Make sure no value is NaN
    if (productPriceTaxExcluded.isNaN()) {
      productPriceTaxExcluded = new BigNumber(0);
    }

    if (impactTaxExcluded.isNaN()) {
      impactTaxExcluded = new BigNumber(0);
    }

    if (combinationEcotaxTaxExcluded.isNaN()) {
      combinationEcotaxTaxExcluded = new BigNumber(0);
    }

    const combinationEcotaxTaxIncluded = combinationEcotaxTaxExcluded.times(ecotaxRatio);
    // Combination price is the initial product price plus the impact from combination
    const combinationPriceTaxExcluded = productPriceTaxExcluded.plus(impactTaxExcluded);

    // Final display price (without) taxes also includes the ecotax (without taxes)
    const finalPriceTaxExcluded: BigNumber = combinationPriceTaxExcluded.plus(combinationEcotaxTaxExcluded);
    // Final display price is the combination price (with taxes) plus the ecotax (with its own tax)
    const finalPriceTaxIncluded: BigNumber = combinationPriceTaxExcluded.times(taxRatio).plus(combinationEcotaxTaxIncluded);
    this.mapper.set('price.finalPriceTaxExcluded', finalPriceTaxExcluded.toFixed(this.precision));
    this.mapper.set('price.finalPriceTaxIncluded', finalPriceTaxIncluded.toFixed(this.precision));

    const $finalPriceTaxExcluded = this.mapper.getInputsFor('price.finalPriceTaxExcluded');
    const $finalPriceTaxIncluded = this.mapper.getInputsFor('price.finalPriceTaxIncluded');

    if ($finalPriceTaxExcluded) {
      $finalPriceTaxExcluded.siblings('.final-price-preview').text(this.displayPrice(finalPriceTaxExcluded));
    }

    if ($finalPriceTaxIncluded) {
      $finalPriceTaxIncluded.siblings('.final-price-preview').text(this.displayPrice(finalPriceTaxIncluded));
    }
  }

  private getEcotaxTaxExcluded(): BigNumber {
    const combinationEcotaxTaxExcluded: BigNumber = this.mapper.getBigNumber('price.ecotaxTaxExcluded') ?? new BigNumber(0);

    // If no ecotax is defined for combination we use the one from product for computing
    if (combinationEcotaxTaxExcluded.isNegative() || combinationEcotaxTaxExcluded.isZero()) {
      return this.mapper.getBigNumber('product.ecotaxTaxExcluded') ?? new BigNumber(0);
    }

    return combinationEcotaxTaxExcluded;
  }

  private getEcotaxTaxIncluded(combinationEcotaxTaxIncluded: BigNumber): BigNumber {
    if (!combinationEcotaxTaxIncluded.isNegative() && !combinationEcotaxTaxIncluded.isZero()) {
      return combinationEcotaxTaxIncluded;
    }

    const ecotaxTaxExcluded = this.mapper.getBigNumber('product.ecotaxTaxExcluded') ?? new BigNumber(0);
    const ecotaxRatio = this.getEcoTaxRatio();

    return ecotaxTaxExcluded.times(ecotaxRatio);
  }

  private getTaxRatio(): BigNumber {
    let taxRate: BigNumber = this.mapper.getBigNumber('product.taxRate') ?? new BigNumber(0);

    if (taxRate.isNaN()) {
      taxRate = new BigNumber(0);
    }

    return taxRate.dividedBy(100).plus(1);
  }

  private getEcoTaxRatio(): BigNumber {
    const $ecotaxTaxExcluded = this.mapper.getInputsFor('price.ecotaxTaxExcluded');

    // If no ecotax field found return 1 this way it has no impact in computing
    if (!$ecotaxTaxExcluded) {
      return new BigNumber(1);
    }

    let taxRate;
    try {
      taxRate = new BigNumber($ecotaxTaxExcluded.data('taxRate'));
    } catch (error) {
      taxRate = new BigNumber(NaN);
    }
    if (taxRate.isNaN()) {
      taxRate = new BigNumber(0);
    }

    return taxRate.dividedBy(100).plus(1);
  }
}
