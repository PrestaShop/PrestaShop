/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {showGrowl} from '@app/utils/growl';
import ComponentsMap from '@components/components-map';

import ClickEvent = JQuery.ClickEvent;

const {$} = window;

export type SubmittableInputConfig = {
  wrapperSelector: string;
  submitCallback: (input: HTMLInputElement) => any;
  afterSuccess?: ((input: HTMLInputElement, response: AjaxResponse) => any);
  afterFailure?: ((input: HTMLInputElement, error: AjaxError) => any);
}

/**
 * Activates, deactivates, shows, hides submit button inside an input
 * (depending if input was changed comparing to initial value)
 * After button is clicked, component fires the callback function which was provided to constructor.
 */
export default class SubmittableInput {
  config: SubmittableInputConfig;

  inputSelector: string;

  inputsInContainerSelector: string;

  buttonSelector: string;

  loading: boolean;

  constructor(config: SubmittableInputConfig) {
    this.config = config;
    this.inputSelector = ComponentsMap.submittableInput.inputSelector;
    this.buttonSelector = ComponentsMap.submittableInput.buttonSelector;
    this.inputsInContainerSelector = `${this.config.wrapperSelector} ${this.inputSelector}`;
    this.loading = false;

    this.init();
  }

  public reset(input: HTMLInputElement, value: string|number): void {
    $(input).val(value);
    $(input).data('initialValue', value);
  }

  private init(): void {
    $(document).on('focus mouseenter', this.inputsInContainerSelector, (e) => {
      this.refreshButtonState(e.currentTarget, true);
    });
    $(document).on('input blur mouseleave', this.inputsInContainerSelector, (e) => {
      this.refreshButtonState(e.currentTarget);
    });
    $(document).on('click', `${this.config.wrapperSelector} ${this.buttonSelector}`, (e: ClickEvent) => {
      e.stopImmediatePropagation();
      this.submitInput(this.findInput(e.currentTarget));
    });
    $(document).on('keyup', this.inputsInContainerSelector, (e: JQueryEventObject) => {
      // only on ENTER
      if (e.keyCode !== 13) {
        return;
      }

      e.preventDefault();
      e.stopImmediatePropagation();
      this.submitInput(e.target as HTMLInputElement);
    });
  }

  private submitInput(input: HTMLInputElement): void {
    if (this.loading || !this.inputValueChanged(input)) {
      return;
    }

    this.toggleLoading(input, true);
    const button = this.findButton(input);

    this.config.submitCallback(input)
      .then((response: AjaxResponse) => {
        $(input).data('initialValue', input.value);
        this.toggleButtonVisibility(button, false);
        this.toggleLoading(input, false);

        if (response.message) {
          showGrowl('success', response.message);
        }

        if (this.config.afterSuccess) {
          this.config.afterSuccess(input, response);
        }
      })
      .catch((error: AjaxError) => {
        this.toggleError(button, true);
        this.toggleButtonVisibility(button, false);
        this.toggleLoading(input, false);

        if (typeof error.responseJSON.errors === 'undefined') {
          return;
        }

        const messages = error.responseJSON.errors;
        Object.keys(messages).forEach((key) => {
          showGrowl('error', messages[key]);
        });
        if (this.config.afterFailure) {
          this.config.afterFailure(input, error);
        }
      });
  }

  private refreshButtonState(
    input: HTMLElement,
    visible: boolean | null = null,
  ): void {
    const button = this.findButton(input);
    const valueWasChanged = this.inputValueChanged(input);
    this.toggleButtonActivity(button, valueWasChanged);

    if (visible !== null) {
      this.toggleButtonVisibility(button, visible);
    } else {
      this.toggleButtonVisibility(button, valueWasChanged);
    }
  }

  private toggleButtonActivity(button: HTMLElement, active: boolean): void {
    $(button).toggleClass('active', active);
  }

  private toggleButtonVisibility(
    button: Element,
    visible: boolean,
  ): void {
    const $button = $(button);
    $button.toggleClass('d-none', !visible);
  }

  private toggleLoading(input: HTMLInputElement, loading: boolean): void {
    this.loading = loading;
    const button = this.findButton(input);
    // eslint-disable-next-line no-param-reassign
    input.disabled = this.loading;
    button.disabled = this.loading;

    if (this.loading) {
      $(button).html('<span class="spinner-border spinner-border-sm"></span>');
    } else {
      $(button).html('<i class="material-icons">check</i>');
    }
  }

  private toggleError(button: HTMLButtonElement, error: boolean): void {
    const input = this.findInput(button);

    $(input).toggleClass('is-invalid', error);
  }

  private findButton(input: Element): HTMLButtonElement {
    return <HTMLButtonElement>$(input)
      .closest(this.config.wrapperSelector)
      .find(this.buttonSelector)[0];
  }

  private findInput(button: HTMLButtonElement): HTMLInputElement {
    return <HTMLInputElement>$(button)
      .closest(this.config.wrapperSelector)
      .find(this.inputSelector)[0];
  }

  private inputValueChanged(input: HTMLElement): boolean {
    const initialValue = $(input).data('initial-value');
    let newValue = $(input).val();

    if ($(input).hasClass('is-invalid')) {
      $(input).removeClass('is-invalid');
    }

    if (typeof initialValue === 'number') {
      newValue = Number(newValue);
    }

    return initialValue !== newValue;
  }
}
