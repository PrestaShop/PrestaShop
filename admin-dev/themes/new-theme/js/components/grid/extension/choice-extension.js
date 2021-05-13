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
 * This extension enables submit functionality of the choice fields in grid.
 *
 * Usage of the extension:
 *
 * const myGrid = new Grid('myGrid');
 * myGrid.addExtension(new ChoiceExtension());
 *
 */
export default class ChoiceExtension {
  constructor() {
    this.locks = [];
  }

  extend(grid) {
    const $choiceOptionsContainer = grid.getContainer().find('table.table .js-choice-options');

    $choiceOptionsContainer.find('.js-dropdown-item').on('click', (e) => {
      e.preventDefault();
      const $button = $(e.currentTarget);
      const $parent = $button.closest('.js-choice-options');
      const url = $parent.data('url');

      this.submitForm(url, $button);
    });
  }

  /**
   * Submits the form.
   * @param {string} url
   * @param {jQuery} $button
   * @private
   */
  submitForm(url, $button) {
    const selectedStatusId = $button.data('value');

    if (this.isLocked(url)) {
      return;
    }

    const $form = $('<form>', {
      action: url,
      method: 'POST',
    }).append(
      $('<input>', {
        name: 'value',
        value: selectedStatusId,
        type: 'hidden',
      }));

    $form.appendTo('body');
    $form.submit();

    this.lock(url);
  }

  /**
   * Checks if current url is being used at the moment.
   *
   * @param url
   * @return {boolean}
   *
   * @private
   */
  isLocked(url) {
    return this.locks.includes(url);
  }

  /**
   * Locks the current url so it cant be used twice to execute same request
   * @param url
   * @private
   */
  lock(url) {
    this.locks.push(url);
  }
}
