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

const $ = window.$;

/**
 * Generates a password and informs about it's strength.
 */
export default class ChangePasswordHandler {
  constructor() {
    // Minimum length of the generated password.
    this.minLength = 8;

    // Feedback container holds messages representing password strength.
    this.$feedbackContainer = $('.js-password-strength-feedback');
  }

  /**
   *
   * @param $input
   * @param $output this element will be used
   */
  watchPasswordStrength($input) {
    $.passy.requirements.length.min = this.minLength;
    $.passy.requirements.characters = 'DIGIT';

    $input.each((index, element) => {
      const $output = $(element).parent().find('.form-text');

      $(element).passy((strength, valid) => {
        const feedback = this._getPasswordStrengthFeedback(strength);
        $output.text(feedback.message);
        $output.removeClass('text-danger text-warning text-success');
        $output.addClass(feedback.elementClass);
        $output.toggleClass('d-none', !valid);
      });
    });
  }

  /**
   * Generates a password and fills it to given input.
   *
   * @param $input the input to fill the password into.
   */
  generatePassword($input) {
    $input.passy('generate', this.minLength);
  }

  /**
   * Get feedback that describes given password strength.
   * Response contains text message and element class.
   *
   * @param {number} strength
   */
  _getPasswordStrengthFeedback(strength) {
    switch (strength) {
      case $.passy.strength.LOW:
        return {
          message: this.$feedbackContainer.find('.strength-low').text(),
          elementClass: 'text-danger',
        };

      case $.passy.strength.MEDIUM:
        return {
          message: this.$feedbackContainer.find('.strength-medium').text(),
          elementClass: 'text-warning',
        };

      case $.passy.strength.HIGH:
        return {
          message: this.$feedbackContainer.find('.strength-high').text(),
          elementClass: 'text-success',
        };

      case $.passy.strength.EXTREME:
        return {
          message: this.$feedbackContainer.find('.strength-extreme').text(),
          elementClass: 'text-success',
        };
    }

    throw 'Invalid password strength indicator.';
  }
}
