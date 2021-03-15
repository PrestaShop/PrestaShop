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

export default class SubmittableInput {
  constructor() {
    this.wrapperSelector = '.ps-submittable-input-wrapper';
    this.buttonSelector = '.check-button';
    this.inputsSelector = `${this.wrapperSelector} .submittable-input`;
    this.eventEmitter = window.prestashop.instance.eventEmitter;

    this.init();
  }

  init() {
    $(document).on('mouseenter', this.inputsSelector, (e) => {
      this.showButton($(e.currentTarget));
    });
    $(document).on('mouseleave', this.inputsSelector, (e) => {
      this.hideButton($(e.currentTarget));
    });
    $(document).on('input', this.inputsSelector, (e) => {
      this.activateButton($(e.currentTarget));
    });
  }

  activateButton($input) {
    const $btn = $input.closest(this.wrapperSelector).find(this.buttonSelector);

    if (!this.valuesAreEqual($input.data('initial-value'), $input.val())) {
      $btn.addClass('active');
    }
  }

  showButton($input) {
    const $btn = $input.closest(this.wrapperSelector).find(this.buttonSelector);
    $btn.removeClass('d-none');
  }

  hideButton($input) {
    const $btn = $input.closest(this.wrapperSelector).find(this.buttonSelector);

    if (this.valuesAreEqual($input.data('initial-value'), $input.val())) {
      $btn.addClass('d-none');
      $btn.removeClass('active');
    }
  }

  /**
   * @param initialValue
   * @param currentValue
   *
   * @returns {Boolean}
   *
   * @private
   */
  valuesAreEqual(initialValue, currentValue) {
    let typedValue = currentValue;

    if (typeof initialValue === 'number') {
      typedValue = Number(currentValue);
    }

    return initialValue === typedValue;
  }
}
