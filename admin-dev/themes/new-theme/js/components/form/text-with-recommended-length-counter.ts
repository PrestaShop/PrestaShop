/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ComponentsMap from '@components/components-map';

const {$} = window;

/**
 * This component is implemented to work with TextWithRecommendedLengthType,
 * but can be used as standalone component as well.
 *
 * Usage:
 *
 * Define your HTML with input and counter. Example:
 *
 * <input id="myInput"
 *        class="js-recommended-length-input"
 *        data-recommended-length-counter="#myInput_recommended_length_counter"
 * >
 *
 * <div id"myInput_recommended_length_counter">
 *  <span class="js-current-length">0</span> of 70 characters used (recommended)
 * </div>
 *
 * NOTE: You must use exactly the same Classes, but IDs can be different!
 *
 * Then enable component in JavaScript:
 *
 * new TextWithRecommendedLengthCounter();
 */
export default class TextWithRecommendedLengthCounter {
  constructor() {
    $(document).on('input', ComponentsMap.recommendedLengthInput, (event) => {
      const $input = $(event.currentTarget);
      const inputVal = <string>$input.val();

      $($input.data('recommended-length-counter'))
        .find(ComponentsMap.currentLength)
        .text(inputVal.length);
    });
  }
}
