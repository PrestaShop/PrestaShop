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

import ChangePasswordHandler from '../change-password-handler';
import PasswordValidator from '../password-validator';

const {$} = window;

/**
 * Class responsible for actions related to "change password" form type.
 * Generates random passwords, validates new password and it's confirmation,
 * displays error messages related to validation.
 */
export default class ChangePasswordControl {
  $inputsBlock: JQuery;

  showButtonSelector: string;

  hideButtonSelector: string;

  oldPasswordInputSelector: string;

  newPasswordInputSelector: string;

  confirmNewPasswordInputSelector: string;

  generatedPasswordDisplaySelector: string;

  passwordStrengthFeedbackContainerSelector: string;

  $newPasswordInputs: JQuery<HTMLElement>;

  $copyPasswordInputs: JQuery<HTMLElement>;

  $submittableInputs: JQuery<HTMLElement>;

  passwordHandler: ChangePasswordHandler;

  passwordValidator: PasswordValidator;

  constructor(
    inputsBlockSelector: string,
    showButtonSelector: string,
    hideButtonSelector: string,
    oldPasswordInputSelector: string,
    newPasswordInputSelector: string,
    confirmNewPasswordInputSelector: string,
    generatedPasswordDisplaySelector: string,
    passwordStrengthFeedbackContainerSelector: string,
  ) {
    // Block that contains password inputs
    this.$inputsBlock = $(inputsBlockSelector);

    // Button that shows the password inputs block
    this.showButtonSelector = showButtonSelector;

    // Button that hides the password inputs block
    this.hideButtonSelector = hideButtonSelector;

    // Input to enter old password
    this.oldPasswordInputSelector = oldPasswordInputSelector;

    // Input to enter new password
    this.newPasswordInputSelector = newPasswordInputSelector;

    // Input to confirm the new password
    this.confirmNewPasswordInputSelector = confirmNewPasswordInputSelector;

    // Input that displays generated random password
    this.generatedPasswordDisplaySelector = generatedPasswordDisplaySelector;

    // Block that displays password strength feedback
    this.passwordStrengthFeedbackContainerSelector = passwordStrengthFeedbackContainerSelector;

    // Main input for password generation
    this.$newPasswordInputs = this.$inputsBlock.find(
      this.newPasswordInputSelector,
    );

    // Generated password will be copied to these inputs
    this.$copyPasswordInputs = this.$inputsBlock
      .find(this.confirmNewPasswordInputSelector)
      .add(this.generatedPasswordDisplaySelector);

    // All inputs in the change password block, that are submittable with the form.
    this.$submittableInputs = this.$inputsBlock
      .find(this.oldPasswordInputSelector)
      .add(this.newPasswordInputSelector)
      .add(this.confirmNewPasswordInputSelector);

    this.passwordHandler = new ChangePasswordHandler(
      passwordStrengthFeedbackContainerSelector,
    );

    this.passwordValidator = new PasswordValidator(
      this.newPasswordInputSelector,
      this.confirmNewPasswordInputSelector,
    );

    this.hideInputsBlock();
    this.initEvents();
  }

  /**
   * Initialize events.
   *
   * @private
   */
  private initEvents(): void {
    // Show the inputs block when show button is clicked
    $(document).on('click', this.showButtonSelector, (e) => {
      this.hide($(e.currentTarget));
      this.showInputsBlock();
    });

    $(document).on('click', this.hideButtonSelector, () => {
      this.hideInputsBlock();
      this.show($(this.showButtonSelector));
    });

    // Watch and display feedback about password's strength
    this.passwordHandler.watchPasswordStrength(this.$newPasswordInputs);

    // Validate new password and it's confirmation when any of the inputs is changed
    $(document).on(
      'keyup',
      `${this.newPasswordInputSelector},${this.confirmNewPasswordInputSelector}`,
      () => {
        this.checkPasswordValidity();
      },
    );

    // Prevent submitting the form if new password is not valid
    $(document).on(
      'submit',
      $(this.oldPasswordInputSelector).closest('form'),
      (event) => {
        // If password input is disabled - we don't need to validate it.
        if ($(this.oldPasswordInputSelector).is(':disabled')) {
          return;
        }

        if (!this.passwordValidator.isPasswordValid()) {
          event.preventDefault();
        }
      },
    );
  }

  /**
   * Check if password is valid, show error messages if it's not.
   *
   * @private
   */
  private checkPasswordValidity(): void {
    const $firstPasswordErrorContainer = $(this.newPasswordInputSelector)
      .parent()
      .find('.form-text');
    const $secondPasswordErrorContainer = $(
      this.confirmNewPasswordInputSelector,
    )
      .parent()
      .find('.form-text');

    $firstPasswordErrorContainer
      .text(this.getPasswordLengthValidationMessage())
      .toggleClass(
        'text-danger',
        !this.passwordValidator.isPasswordLengthValid(),
      );
    $secondPasswordErrorContainer
      .text(this.getPasswordConfirmationValidationMessage())
      .toggleClass(
        'text-danger',
        !this.passwordValidator.isPasswordMatchingConfirmation(),
      );
  }

  /**
   * Get password confirmation validation message.
   *
   * @returns {String}
   *
   * @private
   */
  private getPasswordConfirmationValidationMessage(): string {
    if (!this.passwordValidator.isPasswordMatchingConfirmation()) {
      return $(this.confirmNewPasswordInputSelector).data('invalid-password');
    }

    return '';
  }

  /**
   * Get password length validation message.
   *
   * @returns {String}
   *
   * @private
   */
  private getPasswordLengthValidationMessage(): string {
    if (this.passwordValidator.isPasswordTooShort()) {
      return $(this.newPasswordInputSelector).data('password-too-short');
    }

    if (this.passwordValidator.isPasswordTooLong()) {
      return $(this.newPasswordInputSelector).data('password-too-long');
    }

    return '';
  }

  /**
   * Show the password inputs block.
   *
   * @private
   */
  private showInputsBlock(): void {
    this.show(this.$inputsBlock);
    this.$submittableInputs.removeAttr('disabled');
    this.$submittableInputs.attr('required', 'required');
  }

  /**
   * Hide the password inputs block.
   *
   * @private
   */
  private hideInputsBlock(): void {
    this.hide(this.$inputsBlock);
    this.$submittableInputs.attr('disabled', 'disabled');
    this.$submittableInputs.removeAttr('required');
    this.$inputsBlock.find('input').val('');
    this.$inputsBlock.find('.form-text').text('');
    this.$newPasswordInputs.popover('dispose');
    this.hide(this.$inputsBlock.find(this.passwordStrengthFeedbackContainerSelector));
    this.$newPasswordInputs.removeClass('border-success border-danger');
  }

  /**
   * Hide an element.
   *
   * @param {jQuery} $el
   *
   * @private
   */
  private hide($el: JQuery): void {
    $el.addClass('d-none');
  }

  /**
   * Show hidden element.
   *
   * @param {jQuery} $el
   *
   * @private
   */
  private show($el: JQuery): void {
    $el.removeClass('d-none');
  }
}
