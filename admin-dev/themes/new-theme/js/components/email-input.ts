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
