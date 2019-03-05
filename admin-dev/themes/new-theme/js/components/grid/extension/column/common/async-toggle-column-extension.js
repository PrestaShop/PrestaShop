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

const $ = window.$;

/**
 * Class AsyncToggleColumnExtension submits toggle action using AJAX
 */
export default class AsyncToggleColumnExtension {

  constructor() {
    return {
      extend: (grid) => this.extend(grid),
    }
  }

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid) {
    grid.getContainer().find('.js-grid-table').on('click', '.ps-togglable-row', (event) => {
      event.preventDefault();

      const $button = $(event.currentTarget);

      $.post({
        url: $button.data('toggle-url'),
      }).then((response) => {
        if (response.status) {
          showSuccessMessage(response.message);

          this._toggleButtonDisplay($button);

          return;
        }

        showErrorMessage(response.message);
      });
    });
  }

  /**
   * Toggle button display from enabled to disabled and other way around
   *
   * @param {jQuery} $button
   *
   * @private
   */
  _toggleButtonDisplay($button) {
    const isActive = $button.hasClass('grid-toggler-icon-valid');

    const classToAdd = isActive ? 'grid-toggler-icon-not-valid' : 'grid-toggler-icon-valid';
    const classToRemove = isActive ? 'grid-toggler-icon-valid' : 'grid-toggler-icon-not-valid';
    const icon = isActive ? 'clear' : 'check';

    $button.removeClass(classToRemove);
    $button.addClass(classToAdd);
    $button.text(icon);
  }
}
