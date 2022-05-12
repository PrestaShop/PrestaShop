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

import ObjectFormMapper, {FormUpdateEvent} from '@components/form/form-object-mapper';
import CombinationFormMapping from '@pages/product/combination/form/combination-form-mapping';
import CombinationEventMap from '@pages/product/combination/form/combination-event-map';
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
      eventEmitter,
      {
        modelUpdated: CombinationEventMap.combinationModelUpdated,
        modelFieldUpdated: CombinationEventMap.combinationFieldUpdated,
        updateModel: CombinationEventMap.updateCombinationModel,
      },
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
      'impact.ecotaxTaxExcluded',
      'impact.ecotaxTaxIncluded',
      'impact.unitPriceTaxExcluded',
      'impact.unitPriceTaxIncluded',
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
    const taxRatio = this.getTaxRatio();

    if (taxRatio.isNaN()) {
      return;
    }

    // eslint-disable-next-line default-case
    switch (event.modelKey) {
      // Regular retail price
      case 'impact.priceTaxIncluded': {
        const priceTaxIncluded = this.mapper.getBigNumber('impact.priceTaxIncluded') ?? new BigNumber(0);
        const ecotaxTaxIncluded = this.mapper.getBigNumber('impact.ecotaxTaxIncluded') ?? new BigNumber(0);
        this.mapper.set('impact.priceTaxExcluded', this.removeTax(priceTaxIncluded.minus(ecotaxTaxIncluded)));
        break;
      }
      case 'impact.priceTaxExcluded': {
        const priceTaxExcluded = this.mapper.getBigNumber('impact.priceTaxExcluded') ?? new BigNumber(0);
        const ecotaxTaxIncluded = this.mapper.getBigNumber('impact.ecotaxTaxIncluded') ?? new BigNumber(0);
        this.mapper.set(
          'impact.priceTaxIncluded',
          priceTaxExcluded.times(taxRatio).plus(ecotaxTaxIncluded).toFixed(this.precision),
        );
        break;
      }

      // Ecotax values
      case 'impact.ecotaxTaxIncluded': {
        // Only this update is needed here, the rest will be updated via the trigger for impact.ecotaxTaxExcluded
        const ecoTaxRatio = this.getEcoTaxRatio();
        const ecotaxTaxIncluded = this.mapper.getBigNumber('impact.ecotaxTaxIncluded') ?? new BigNumber(0);
        this.mapper.set(
          'impact.ecotaxTaxExcluded',
          ecotaxTaxIncluded.dividedBy(ecoTaxRatio).toFixed(this.precision),
        );
        break;
      }
      case 'impact.ecotaxTaxExcluded': {
        const ecoTaxRatio = this.getEcoTaxRatio();
        const priceTaxIncluded = this.mapper.getBigNumber('impact.priceTaxIncluded') ?? new BigNumber(0);
        const ecotaxTaxExcluded = this.mapper.getBigNumber('impact.ecotaxTaxExcluded') ?? new BigNumber(0);
        const newEcotaxTaxIncluded = ecotaxTaxExcluded.times(ecoTaxRatio);
        this.mapper.set('impact.ecotaxTaxIncluded', newEcotaxTaxIncluded.toFixed(this.precision));
        this.mapper.set('impact.priceTaxExcluded', this.removeTax(priceTaxIncluded.minus(newEcotaxTaxIncluded)));
        break;
      }

      // Unit price
      case 'impact.unitPriceTaxIncluded': {
        const unitPriceTaxIncluded = this.mapper.getBigNumber('impact.unitPriceTaxIncluded') ?? new BigNumber(0);
        this.mapper.set('impact.unitPriceTaxExcluded', this.removeTax(unitPriceTaxIncluded));
        break;
      }
      case 'impact.unitPriceTaxExcluded': {
        const unitPriceTaxExcluded = this.mapper.getBigNumber('impact.unitPriceTaxExcluded') ?? new BigNumber(0);
        this.mapper.set('impact.unitPriceTaxIncluded', this.addTax(unitPriceTaxExcluded));
        break;
      }
    }

    this.updateFinalPrices();
  }

  private updateFinalPrices(): void {
    const taxRatio: BigNumber = this.getTaxRatio();
    const productPriceTaxExcluded: BigNumber = this.mapper.getBigNumber('product.priceTaxExcluded') ?? new BigNumber(0);
    const combinationImpactTaxExcluded: BigNumber = this.mapper.getBigNumber('impact.priceTaxExcluded') ?? new BigNumber(0);
    const finalPriceTaxExcluded: BigNumber = productPriceTaxExcluded.plus(combinationImpactTaxExcluded);
    const finalPriceTaxIncluded: BigNumber = finalPriceTaxExcluded.times(taxRatio);

    const $finalPriceTaxExcluded = this.mapper.getInputsFor('price.finalPriceTaxExcluded')?.siblings('.final-price-preview');
    const $finalPriceTaxIncluded = this.mapper.getInputsFor('price.finalPriceTaxIncluded')?.siblings('.final-price-preview');

    if ($finalPriceTaxExcluded) {
      $finalPriceTaxExcluded.text(this.displayPrice(finalPriceTaxExcluded));
    }

    if ($finalPriceTaxIncluded) {
      $finalPriceTaxIncluded.text(this.displayPrice(finalPriceTaxIncluded));
    }
  }

  private getTaxRatio(): BigNumber {
    const taxRate: BigNumber = this.mapper.getBigNumber('product.taxRate') ?? new BigNumber(0);

    return taxRate.plus(1);
  }

  private getEcoTaxRatio(): BigNumber {
    const $ecotaxTaxExcluded = this.mapper.getInputsFor('impact.ecotaxTaxExcluded');

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

    return taxRate.plus(1);
  }

  private removeTax(price: BigNumber): string {
    const taxRatio = this.getTaxRatio();

    if (taxRatio.isNaN()) {
      return price.toFixed(this.precision);
    }

    return price.dividedBy(taxRatio).toFixed(this.precision);
  }

  private addTax(price: BigNumber): string {
    const taxRatio = this.getTaxRatio();

    if (taxRatio.isNaN()) {
      return price.toFixed(this.precision);
    }

    return price.times(taxRatio).toFixed(this.precision);
  }
}
