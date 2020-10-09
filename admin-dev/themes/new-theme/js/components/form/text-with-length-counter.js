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
 * TextWithLengthCounter handles input with length counter UI.
 *
 * Usage:
 *
 * There must be an element that wraps both input & counter display with ".js-text-with-length-counter" class.
 * Counter display must have ".js-countable-text-display" class and input must have ".js-countable-text-input" class.
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
  constructor() {
    this.wrapperSelector = '.js-text-with-length-counter';
    this.textSelector = '.js-countable-text';
    this.inputSelector = '.js-countable-input';

    $(document).on('input', `${this.wrapperSelector} ${this.inputSelector}`, (e) => {
      const $input = $(e.currentTarget);
      const remainingLength = $input.data('max-length') - $input.val().length;

      $input.closest(this.wrapperSelector).find(this.textSelector).text(remainingLength);
    });
  }
}
