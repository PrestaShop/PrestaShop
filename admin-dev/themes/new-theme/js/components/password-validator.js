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

/**
 * Class responsible for checking password's validity.
 */
export default class PasswordValidator {
  constructor(newPasswordInputSelector, confirmPasswordInputSelector = null) {
    this.newPasswordInput = document.querySelector(newPasswordInputSelector);
    this.confirmPasswordInput = document.querySelector(confirmPasswordInputSelector);

    // Minimum allowed length for entered password
    this.minPasswordLength = 8;

    // Maximum allowed length for entered password
    this.maxPasswordLength = 255;
  }

  /**
   * Check if the password is valid.
   *
   * @returns {boolean}
   */
  isPasswordValid() {
    return this.isPasswordMatchingConfirmation() && this.isPasswordLengthValid();
  }

  /**
   * Check if password's length is valid.
   *
   * @returns {boolean}
   */
  isPasswordLengthValid() {
    return !this.isPasswordTooShort() && !this.isPasswordTooLong();
  }

  /**
   * Check if password is matching it's confirmation.
   *
   * @returns {boolean}
   */
  isPasswordMatchingConfirmation() {
    if (this.confirmPasswordInput.value === '') {
      return true;
    }

    return this.newPasswordInput.value === this.confirmPasswordInput.value;
  }

  /**
   * Check if password is too short.
   *
   * @returns {boolean}
   */
  isPasswordTooShort() {
    return this.newPasswordInput.value.length < this.minPasswordLength;
  }

  /**
   * Check if password is too long.
   *
   * @returns {boolean}
   */
  isPasswordTooLong() {
    return this.newPasswordInput.value.length > this.maxPasswordLength;
  }
}
