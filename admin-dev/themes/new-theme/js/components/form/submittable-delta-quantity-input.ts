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
import DeltaQuantityInput, {DeltaQuantityConfig} from '@components/form/delta-quantity-input';

type SubmittableConfig = Omit<SubmittableInputConfig, 'wrapperSelector'> & {
  submittableWrapperSelector: string;
}

export type SubmittableDeltaConfig = Partial<DeltaQuantityConfig> & SubmittableConfig;

export default class SubmittableDeltaQuantityInput {
  private deltaQuantityComponent: DeltaQuantityInput;

  private submittableInputComponent: SubmittableInput;

  constructor(deltaConfig: SubmittableDeltaConfig) {
    const deltaQuantityConfig: Partial<DeltaQuantityConfig> = {};

    if (deltaConfig.containerSelector) {
      deltaQuantityConfig.containerSelector = deltaConfig.containerSelector;
    }
    if (deltaConfig.deltaInputSelector) {
      deltaQuantityConfig.deltaInputSelector = deltaConfig.deltaInputSelector;
    }
    if (deltaConfig.updateQuantitySelector) {
      deltaQuantityConfig.updateQuantitySelector = deltaConfig.updateQuantitySelector;
    }
    if (deltaConfig.modifiedQuantityClass) {
      deltaQuantityConfig.modifiedQuantityClass = deltaConfig.modifiedQuantityClass;
    }
    if (deltaConfig.newQuantitySelector) {
      deltaQuantityConfig.newQuantitySelector = deltaConfig.newQuantitySelector;
    }
    if (deltaConfig.initialQuantityPreviewSelector) {
      deltaQuantityConfig.initialQuantityPreviewSelector = deltaConfig.initialQuantityPreviewSelector;
    }

    this.deltaQuantityComponent = new DeltaQuantityInput(deltaQuantityConfig);

    this.submittableInputComponent = new SubmittableInput({
      wrapperSelector: deltaConfig.submittableWrapperSelector,
      submitCallback: deltaConfig.submitCallback,
      afterSuccess: (
        input: HTMLInputElement,
        response: AjaxResponse,
      ) => this.reset(input, response, deltaConfig.afterSuccess),
    });
  }

  private reset(
    input: HTMLInputElement,
    response: AjaxResponse,
    afterSuccess?: (deltaInput: HTMLInputElement, ajaxResponse: AjaxResponse) => any,
  ): void {
    this.deltaQuantityComponent.applyNewQuantity(input);
    this.submittableInputComponent.reset(input, 0);

    if (afterSuccess) {
      afterSuccess(input, response);
    }
  }
}
