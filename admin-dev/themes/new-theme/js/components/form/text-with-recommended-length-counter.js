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
    $(document).on('input', '.js-recommended-length-input', (event) => {
      const $input = $(event.currentTarget);

      $($input.data('recommended-length-counter')).find('.js-current-length').text($input.val().length);
    });
  }
}
