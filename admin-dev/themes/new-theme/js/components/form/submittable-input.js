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
 * Activates, deactivates, shows, hides submit button inside an input
 * (depending if input was changed comparing to initial value)
 * After button is clicked, component fires the callback function which was provided to constructor.
 */
export default class SubmittableInput {
  /**
   * @param {String} wrapperSelector
   * @param {Function} callback
   *
   * @returns {{}}
   */
  constructor(wrapperSelector, callback) {
    this.inputSelector = '.submittable-input';
    this.callback = callback;
    this.wrapperSelector = wrapperSelector;
    this.buttonSelector = '.check-button';

    this.init();

    return {};
  }

  /**
   * @private
   */
  init() {
    const inputs = `${this.wrapperSelector} ${this.inputSelector}`;

    $(document).on('focus', inputs, (e) => {
      this.refreshButtonState(e.currentTarget, true);
    });
    $(document).on('input mouseleave', inputs, (e) => {
      this.refreshButtonState(e.currentTarget);
    });
    this.onButtonClick();
    this.onEnterKey();
  }

  /**
   * @private
   */
  onButtonClick() {
    $(document).on('click', `${this.wrapperSelector} ${this.buttonSelector}`, (e) => {
      const input = this.findInput(e.currentTarget);
      this.callback(input);
      $(input).data('initial-value', input.value);
      this.toggleButtonVisibility(e.currentTarget, false);
    });
  }

  /**
   * @private
   */
  onEnterKey() {
    $(document).on('keyup', this.inputSelector, (e) => {
      if (e.keyCode === 13) {
        e.preventDefault();
        $(this.findButton(e.currentTarget)).click();
      }
    });
  }

  /**
   * @param {HTMLElement} input
   * @param {Boolean|null} visible
   */
  refreshButtonState(input, visible = null) {
    const button = this.findButton(input);
    const valueWasChanged = this.inputValueChanged(input);
    this.toggleButtonActivity(button, valueWasChanged);

    if (visible !== null) {
      this.toggleButtonVisibility(button, visible);
    } else {
      this.toggleButtonVisibility(button, valueWasChanged);
    }
  }

  /**
   * @param {HTMLElement} button
   * @param {Boolean} active
   *
   * @private
   */
  toggleButtonActivity(button, active) {
    if (active) {
      $(button).removeClass('d-none');
      $(button).addClass('active');
    } else {
      $(button).removeClass('active');
    }
  }

  /**
   * @param {HTMLElement} button
   * @param {Boolean} visible
   *
   * @private
   */
  toggleButtonVisibility(button, visible) {
    const $button = $(button);

    if (visible) {
      $(button).removeClass('d-none');
    } else {
      $button.addClass('d-none');
    }
  }

  /**
   * @param {HTMLElement} input
   *
   * @returns {HTMLElement}
   *
   * @private
   */
  findButton(input) {
    return $(input).closest(this.wrapperSelector).find(this.buttonSelector)[0];
  }

  /**
   * @param {HTMLElement} button
   *
   * @returns {HTMLElement}
   *
   * @private
   */
  findInput(button) {
    return $(button).closest(this.wrapperSelector).find(this.inputSelector)[0];
  }

  /**
   * @param {HTMLElement} input
   *
   * @returns {Boolean}
   *
   * @private
   */
  inputValueChanged(input) {
    const initialValue = $(input).data('initial-value');
    let newValue = $(input).val();

    if (typeof initialValue === 'number') {
      newValue = Number(newValue);
    }

    return initialValue !== newValue;
  }
}
