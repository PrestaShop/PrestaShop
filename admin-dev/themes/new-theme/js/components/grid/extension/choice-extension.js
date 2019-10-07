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

const $ = global.$;

export default class ChoiceExtension {
  extend(grid) {
    const $choiceOptionsContainer = grid.getContainer().find('table.table .js-choice-options');

    $choiceOptionsContainer.find('.js-dropdown-item').on('click', (e) => {
      e.preventDefault();
      const $button = $(e.currentTarget);
      const $parent = $button.closest('.js-choice-options');
      const url = $parent.data('url');
      const selectedStatusId = $button.data('value');

      this._submitForm(url, selectedStatusId);
    });
  }

  /**
   * Submits the form.
   * @param {string} url
   * @param {number} selectedStatusId
   * @private
   */
  _submitForm(url, selectedStatusId) {

    const $form = $(`<form method="POST" action="${url}">
        <input type="text" name="value" value="${selectedStatusId}">
    </form>`);

    $form.appendTo('body');

    $form.submit();
  }
}
