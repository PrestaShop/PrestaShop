/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import * as EmailValidator from 'email-validator';

import ComponentsMap from '@components/components-map';

export interface EmailInputOptions extends OptionsObject {
  emailInputSelector: string;
}

/**
 * Component to validate email input, it displays an error message when the email is invalid,
 * and it prevents the form from being submitted until the email value is valid.
 */
export default class EmailInput {
  private readonly options: EmailInputOptions;

  constructor(options: Partial<EmailInputOptions> | undefined = undefined) {
    this.options = {
      ...{
        emailInputSelector: ComponentsMap.emailInput.inputSelector,
      },
      ...options,
    };
    this.init();
  }

  private init(): void {
    document.querySelectorAll<HTMLInputElement>(this.options.emailInputSelector).forEach((input) => {
      input.addEventListener('change', () => {
        this.toggleError(input, EmailValidator.validate(input.value));
      });
      const inputForm = input.closest('form');

      if (inputForm) {
        inputForm.addEventListener('submit', (event) => {
          // Prevent submit when input value is still invalid
          if (!EmailValidator.validate(input.value)) {
            event.stopImmediatePropagation();
            event.preventDefault();
            this.toggleError(input, false);
            input.focus();
          }
        });
      }
    });
  }

  private toggleError(input: HTMLInputElement, isValid: boolean): void {
    const formGroup: HTMLElement | null = input.closest(ComponentsMap.formGroup);
    input.classList.toggle(ComponentsMap.formControlInvalidClass, !isValid);

    if (formGroup) {
      let feedbackDiv = formGroup.querySelector(`div.${ComponentsMap.formControlInvalidFeedbackClass}`);

      if (!isValid) {
        // Create feedback div if it doesn't exist
        if (!feedbackDiv) {
          feedbackDiv = document.createElement('div');
          feedbackDiv.classList.add(ComponentsMap.formControlInvalidFeedbackClass);
          formGroup.append(feedbackDiv);
        }

        feedbackDiv.textContent = input.dataset.invalidMessage ?? 'Invalid email address.';
      }
    }
  }
};
