/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
