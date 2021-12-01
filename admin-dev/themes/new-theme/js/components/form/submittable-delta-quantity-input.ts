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
import SubmittableInput, {SubmittableInputConfig} from '@components/form/submittable-input';
import {EventEmitter} from 'events';
import DeltaQuantityInput, {DeltaQuantityConfig} from '@components/form/delta-quantity-input';

export type SubmittableDeltaConfig = Omit<SubmittableInputConfig, 'afterSuccess'>

export class SubmittableDeltaQuantityInput {
  private deltaQuantityComponent: DeltaQuantityInput;

  private submittableInputComponent: SubmittableInput;

  private eventEmitter: EventEmitter;

  constructor(deltaConfig: Partial<DeltaQuantityConfig> = {}, submittableConfig: SubmittableDeltaConfig) {
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.deltaQuantityComponent = new DeltaQuantityInput(deltaConfig);

    this.submittableInputComponent = new SubmittableInput({
      wrapperSelector: submittableConfig.wrapperSelector,
      submitCallback: submittableConfig.submitCallback,
      afterSuccess: (input: HTMLInputElement) => this.reset(input),
    });
  }

  private reset(input: HTMLInputElement): void {
    this.deltaQuantityComponent.reset(input);
    this.submittableInputComponent.reset(input);
  }
}
