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

const {$} = window;

/**
 * Generates a password and informs about it's strength.
 * You can pass a password input to watch the password strength and display feedback messages.
 * You can also generate a random password into an input.
 */
export default class ChangePasswordHandler {
  constructor(passwordStrengthFeedbackContainerSelector, options = {}) {
    // Minimum length of the generated password.
    this.minLength = options.minLength || 8;

    // Feedback container holds messages representing password strength.
    this.$feedbackContainer = $(passwordStrengthFeedbackContainerSelector);

    return {
      watchPasswordStrength: ($input) => this.watchPasswordStrength($input),
      generatePassword: ($input) => this.generatePassword($input),
    };
  }

  /**
   * Watch password, which is entered in the input, strength and inform about it.
   *
   * @param {jQuery} $input the input to watch.
   */
  watchPasswordStrength($input) {
    $.passy.requirements.length.min = this.minLength;
    $.passy.requirements.characters = 'DIGIT';

    $input.each((index, element) => {
      const $outputContainer = $('<span>');

      $outputContainer.insertAfter($(element));

      $(element).passy((strength, valid) => {
        this.displayFeedback($outputContainer, strength, valid);
      });
    });
  }

  /**
   * Generates a password and fills it to given input.
   *
   * @param {jQuery} $input the input to fill the password into.
   */
  generatePassword($input) {
    $input.passy('generate', this.minLength);
  }

  /**
   * Display feedback about password's strength.
   *
   * @param {jQuery} $outputContainer a container to put feedback output into.
   * @param {number} passwordStrength
   * @param {boolean} isPasswordValid
   *
   * @private
   */
  displayFeedback($outputContainer, passwordStrength, isPasswordValid) {
    const feedback = this.getPasswordStrengthFeedback(passwordStrength);
    $outputContainer.text(feedback.message);
    $outputContainer.removeClass('text-danger text-warning text-success');
    $outputContainer.addClass(feedback.elementClass);
    $outputContainer.toggleClass('d-none', !isPasswordValid);
  }

  /**
   * Get feedback that describes given password strength.
   * Response contains text message and element class.
   *
   * @param {number} strength
   *
   * @private
   */
  getPasswordStrengthFeedback(strength) {
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

      default:
        throw new Error('Invalid password strength indicator.');
    }
  }
}
