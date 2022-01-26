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

export default class CombinationFormModel {
  private eventEmitter: EventEmitter;

  private mapper: ObjectFormMapper;

  private precision: number;

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
    const $priceTaxExcludedInput = this.mapper.getInputsFor('price.priceTaxExcluded');
    // @ts-ignore
    this.precision = $priceTaxExcludedInput.data('displayPricePrecision');
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
}
