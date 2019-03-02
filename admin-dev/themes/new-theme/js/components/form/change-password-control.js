/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import ChangePasswordHandler from "../change-password-handler";
import PasswordValidator from "../password-validator";

const $ = window.$;

/**
 * Class responsible for actions related to "change password" form type.
 */
export default class ChangePasswordControl {
  constructor() {
    this.$inputsBlock = $('.js-change-password-block');

    // Action buttons selectors
    this.showButtonSelector = '.js-change-password';
    this.hideButtonSelector = '.js-change-password-cancel';
    this.generatePasswordButtonSelector = '#employee_change_password_generate_password_button';

    // Password inputs selectors
    this.oldPasswordInputSelector = '#employee_change_password_old_password';
    this.newPasswordInputSelector = '#employee_change_password_new_password_first';
    this.confirmNewPasswordInputSelector = '#employee_change_password_new_password_second';
    this.generatedPasswordDisplaySelector = '#employee_change_password_generated_password';

    // Main input for password generation
    this.$newPasswordInputs = this.$inputsBlock
      .find(this.newPasswordInputSelector);

    // Generated password will be copied to these inputs
    this.$copyPasswordInputs = this.$inputsBlock
      .find(this.confirmNewPasswordInputSelector)
      .add(this.generatedPasswordDisplaySelector);

    // All inputs in the change password block, that are submittable with the form.
    this.$submittableInputs = this.$inputsBlock
      .find(this.oldPasswordInputSelector)
      .add(this.newPasswordInputSelector)
      .add(this.confirmNewPasswordInputSelector);

    this.passwordHandler = new ChangePasswordHandler();
    this.passwordValidator = new PasswordValidator(
      this.newPasswordInputSelector,
      this.confirmNewPasswordInputSelector
    );

    this._hideInputsBlock();
    this.initEvents();
  }

  /**
   * Initialize events.
   */
  initEvents() {
    $(document).on('click', this.showButtonSelector, (e) => {
      this._hide($(e.currentTarget));
      this._showInputsBlock();
    });

    $(document).on('click', this.hideButtonSelector, () => {
      this._hideInputsBlock();
      this._show($(this.showButtonSelector));
    });

    this.passwordHandler.watchPasswordStrength(this.$newPasswordInputs);

    $(document).on('click', this.generatePasswordButtonSelector, () => {
      // Generate the password into main input.
      this.passwordHandler.generatePassword(this.$newPasswordInputs);

      // Copy the generated password from main input to additional inputs
      this.$copyPasswordInputs.val(this.$newPasswordInputs.val());
      this._checkPasswordValidity();
    });

    $(document).on('keyup', `${this.newPasswordInputSelector},${this.confirmNewPasswordInputSelector}`, () => {
      this._checkPasswordValidity();
    });

    // Prevent submitting the form if new password is not valid
    $(document).on('submit', $(this.oldPasswordInputSelector).closest('form'), (event) => {
      // If password input is disabled - we don't need to validate it.
      if ($(this.oldPasswordInputSelector).is(':disabled')) {
        return;
      }

      if (!this.passwordValidator.isPasswordValid()) {
        event.preventDefault();
      }
    });
  }

  /**
   * Check if password is valid, show error messages if it's not.
   *
   * @private
   */
  _checkPasswordValidity() {
    const $firstPasswordErrorContainer = $(this.newPasswordInputSelector).parent().find('.form-text');
    const $secondPasswordErrorContainer = $(this.confirmNewPasswordInputSelector).parent().find('.form-text');

    $firstPasswordErrorContainer
      .text(this._getPasswordLengthValidationMessage())
      .toggleClass('text-danger', !this.passwordValidator.isPasswordLengthValid())
    ;

    $secondPasswordErrorContainer
      .text(this._getPasswordConfirmationValidationMessage())
      .toggleClass('text-danger', !this.passwordValidator.isPasswordMatchingConfirmation())
    ;
  }

  /**
   * Get password confirmation validation message.
   *
   * @returns {String}
   * @private
   */
  _getPasswordConfirmationValidationMessage() {
    if (!this.passwordValidator.isPasswordMatchingConfirmation()) {
      return $(this.confirmNewPasswordInputSelector).data('invalid-password');
    }

    return '';
  }

  /**
   * Get password length validation message.
   *
   * @returns {String}
   * @private
   */
  _getPasswordLengthValidationMessage() {
    if (this.passwordValidator.isPasswordTooShort()) {
      return $(this.newPasswordInputSelector).data('password-too-short')
    }

    if (this.passwordValidator.isPasswordTooLong()) {
      return $(this.newPasswordInputSelector).data('password-too-long');
    }

    return '';
  }

  /**
   * Show the password inputs block.
   */
  _showInputsBlock() {
    this._show(this.$inputsBlock);
    this.$submittableInputs.removeAttr('disabled');
    this.$submittableInputs.attr('required', 'required');
  }

  /**
   * Hide the password inputs block.
   */
  _hideInputsBlock() {
    this._hide(this.$inputsBlock);
    this.$submittableInputs.attr('disabled', 'disabled');
    this.$submittableInputs.removeAttr('required');
    this.$inputsBlock.find('input').val('');
    this.$inputsBlock.find('.form-text').text('');
  }

  /**
   * Hide an element.
   */
  _hide($el) {
    $el.addClass('d-none');
  }

  /**
   * Show hidden element.
   */
  _show($el) {
    $el.removeClass('d-none');
  }
}
