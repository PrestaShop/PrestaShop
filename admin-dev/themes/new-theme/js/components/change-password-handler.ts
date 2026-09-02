/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {zxcvbn, zxcvbnOptions, ZxcvbnResult} from '@zxcvbn-ts/core';
import * as zxcvbnCommonPackage from '@zxcvbn-ts/language-common';
import * as zxcvbnEnPackage from '@zxcvbn-ts/language-en';

// eslint-disable-next-line @typescript-eslint/no-var-requires
const {sprintf} = require('sprintf-js');

// Initialize zxcvbn-ts with language packages
const zxcvbnConfig = {
  translations: zxcvbnEnPackage.translations,
  graphs: zxcvbnCommonPackage.adjacencyGraphs,
  dictionary: {
    ...zxcvbnCommonPackage.dictionary,
    ...zxcvbnEnPackage.dictionary,
  },
};

zxcvbnOptions.setOptions(zxcvbnConfig);

const {$} = window;

export interface ChangePasswordHandlerOptions {
  minLength?: number;
}

/**
 * Generates a password and informs about it's strength.
 * You can pass a password input to watch the password strength and display feedback messages.
 * You can also generate a random password into an input.
 */
export default class ChangePasswordHandler {
  minLength: number;

  feedbackSelector: string;

  isValid: boolean;

  constructor(
    passwordStrengthFeedbackContainerSelector: string,
    options: ChangePasswordHandlerOptions = {},
  ) {
    // Minimum length of the generated password.
    this.minLength = <number>options.minLength || 8;

    // Feedback container holds messages representing password strength.
    this.feedbackSelector = passwordStrengthFeedbackContainerSelector;

    this.isValid = false;
  }

  /**
   * Watch password, which is entered in the input, strength and inform about it.
   *
   * @param {jQuery} $input the input to watch.
   */
  watchPasswordStrength($input: JQuery): void {
    const self = this;

    $input.each((index, element) => {
      $(element).on('keyup', function checkPasswordStrength() {
        const passwordValue = <string>$(this).val();
        let $feedbackContainer = $(this).parent().find(self.feedbackSelector);

        if ($feedbackContainer.length === 0) {
          $(this).parent().append($('#password-feedback').html());
          $feedbackContainer = $(this).parent().find(self.feedbackSelector);
        }

        const passwordRequirementsLength = $feedbackContainer.find('.password-requirements-length');
        passwordRequirementsLength.find('span').text(
          sprintf(
            passwordRequirementsLength.data('translation'),
            $(this).data('minlength'),
            $(this).data('maxlength'),
          ),
        );

        const passwordRequirementsScore = $feedbackContainer.find('.password-requirements-score');
        passwordRequirementsScore.find('span').text(
          sprintf(
            passwordRequirementsScore.data('translation'),
            $feedbackContainer.data('translations')[$(this).data('minscore')],
          ),
        );

        if (passwordValue === '') {
          $feedbackContainer.toggleClass('d-none', true);
        } else {
          const result = zxcvbn(passwordValue);
          self.displayFeedback($(this), $feedbackContainer, result);
          $feedbackContainer.removeClass('d-none');
        }
      });
    });
  }

  isPasswordValid(): boolean {
    return this.isValid;
  }

  /**
   * Display feedback about password's strength.
   *
   * @param {jQuery} $passwordInput The currenct password field
   * @param {jQuery} $outputContainer a container to put feedback output into.
   * @param {ZXCVBNResult} result
   *
   * @private
   */
  private displayFeedback(
    $passwordInput: JQuery,
    $outputContainer: JQuery,
    result: ZxcvbnResult,
  ): void {
    const feedback = this.getPasswordStrengthFeedback(result.score);
    const translations = $outputContainer.data('translations');
    const popoverContent:string[] = [];

    $outputContainer.find('.password-strength-text').text(translations[result.score]);
    $passwordInput.popover('dispose');

    if (result.feedback.warning && result.feedback.warning !== '') {
      if (result.feedback.warning in translations) {
        popoverContent.push(translations[result.feedback.warning]);
      }
    }

    result.feedback.suggestions.forEach((suggestion) => {
      if (suggestion in translations) {
        popoverContent.push(translations[suggestion]);
      }
    });

    $passwordInput.popover({
      html: true,
      placement: 'top',
      content: popoverContent.join('<br/>'),
    }).popover('show');

    const passwordLength = (<string>$passwordInput.val()).length;

    const passwordLengthValid = passwordLength >= $passwordInput.data('minlength')
      && passwordLength <= $passwordInput.data('maxlength');
    $outputContainer.find('.password-requirements-length .material-icons').toggleClass(
      'text-success',
      passwordLengthValid,
    );

    const passwordScoreValid = $passwordInput.data('minscore') <= result.score;
    $outputContainer.find('.password-requirements-score .material-icons').toggleClass(
      'text-success',
      passwordScoreValid,
    );

    $passwordInput
      .removeClass()
      .addClass(passwordScoreValid && passwordLengthValid ? 'border-success' : 'border-danger')
      .addClass('form-control border');
    this.isValid = passwordScoreValid && passwordLengthValid;

    const percentage = (result.score * 20) + 20;
    // increase and decrease progress bar
    $outputContainer
      .find('.progress-bar')
      .width(`${percentage}%`)
      .css('visibility', 'visible')
      .css('background-color', feedback.color);
  }

  /**
   * Get feedback that describes given password strength.
   * Response contains text message and element class.
   *
   * @param {zxcvbn.ZXCVBNScore} strength
   *
   * @private
   */
  private getPasswordStrengthFeedback(
    strength: number,
  ): Record<string, string> {
    switch (strength) {
      case 0:
        return {
          color: '#D5343C',
        };

      case 1:
        return {
          color: '#D5343C',
        };

      case 2:
        return {
          color: '#FFA000',
        };

      case 3:
        return {
          color: '#21834D',
        };

      case 4:
        return {
          color: '#21834D',
        };

      default:
        throw new Error('Invalid password strength indicator.');
    }
  }
}
