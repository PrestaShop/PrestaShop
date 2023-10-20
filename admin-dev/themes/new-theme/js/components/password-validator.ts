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

/**
 * Class responsible for checking password's validity.
 * Can validate entered password's length against min/max values.
 * If password confirmation input is provided, can validate if entered password is matching confirmation.
 */
export default class PasswordValidator {
  newPasswordInput: HTMLInputElement | null;

  confirmPasswordInput: HTMLInputElement | null;

  minPasswordLength: number;

  maxPasswordLength: number;

  /**
   * @param {String} passwordInputSelector selector of the password input.
   * @param {String|null} confirmPasswordInputSelector (optional) selector for the password confirmation input.
   * @param {Object} options allows overriding default options.
   */
  constructor(
    passwordInputSelector: string,
    confirmPasswordInputSelector: string,
    options: any = {},
  ) {
    this.newPasswordInput = document.querySelector(passwordInputSelector);

    this.confirmPasswordInput = document.querySelector(
      confirmPasswordInputSelector,
    );

    // Minimum allowed length for entered password
    this.minPasswordLength = options.minPasswordLength || 8;

    // Maximum allowed length for entered password
    this.maxPasswordLength = options.maxPasswordLength || 255;
  }

  /**
   * Check if the password is valid.
   *
   * @returns {boolean}
   */
  isPasswordValid(): boolean {
    if (this.confirmPasswordInput && !this.isPasswordMatchingConfirmation()) {
      return false;
    }

    return this.isPasswordLengthValid();
  }

  /**
   * Check if password's length is valid.
   *
   * @returns {boolean}
   */
  isPasswordLengthValid(): boolean {
    return !this.isPasswordTooShort() && !this.isPasswordTooLong();
  }

  /**
   * Check if password is matching it's confirmation.
   *
   * @returns {boolean}
   */
  isPasswordMatchingConfirmation(): boolean {
    if (!this.confirmPasswordInput) {
      throw new Error(
        'Confirm password input is not provided for the password validator.',
      );
    }

    if (this.confirmPasswordInput.value === '' || !this.newPasswordInput) {
      return true;
    }

    return this.newPasswordInput.value === this.confirmPasswordInput.value;
  }

  /**
   * Check if password is too short.
   *
   * @returns {boolean}
   */
  isPasswordTooShort(): boolean {
    if (this.newPasswordInput?.value) {
      return this.newPasswordInput.value.length < this.minPasswordLength;
    }

    return false;
  }

  /**
   * Check if password is too long.
   *
   * @returns {boolean}
   */
  isPasswordTooLong(): boolean {
    if (this.newPasswordInput?.value) {
      return this.newPasswordInput.value.length > this.maxPasswordLength;
    }

    return false;
  }
}
