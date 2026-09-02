/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * TextWithLengthCounter handles input with length counter UI.
 *
 * Usage:
 *
 * There must be an element that wraps both
 * input & counter display with ".js-text-with-length-counter" class.
 * Counter display must have ".js-countable-text-display" class
 * and input must have ".js-countable-text-input" class.
 * Text input must have "data-max-length" attribute.
 *
 * <div class="js-text-with-length-counter">
 *  <span class="js-countable-text"></span>
 *  <input class="js-countable-input" data-max-length="255">
 * </div>
 *
 * In Javascript you must enable this component:
 *
 * new TextWithLengthCounter();
 */
export default class TextWithLengthCounter {
  wrapperSelector: string;

  textSelector: string;

  inputSelector: string;

  constructor() {
    this.wrapperSelector = '.js-text-with-length-counter';
    this.textSelector = '.js-countable-text';
    this.inputSelector = '.js-countable-input';

    $(document).on(
      'input',
      `${this.wrapperSelector} ${this.inputSelector}`,
      (e) => {
        const $input = $(e.currentTarget);
        const inputVal = <string>$input.val();
        const remainingLength = $input.data('max-length') - inputVal.length;

        $input
          .closest(this.wrapperSelector)
          .find(this.textSelector)
          .text(remainingLength);
      },
    );
  }
}
