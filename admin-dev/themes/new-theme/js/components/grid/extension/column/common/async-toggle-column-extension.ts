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
import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';

const {$} = window;

/**
 * Class AsyncToggleColumnExtension submits toggle action using AJAX
 */
export default class AsyncToggleColumnExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid
      .getContainer()
      .find(GridMap.gridTable)
      .on('click', GridMap.togglableRow, (event) => {
        const $button = $(event.currentTarget);

        if (!$button.hasClass('ps-switch')) {
          event.preventDefault();
        }

        $.post({
          url: $button.data('toggle-url'),
        })
          .then((response) => {
            if (response.status) {
              window.showSuccessMessage(response.message);

              this.toggleButtonDisplay($button);

              return;
            }

            window.showErrorMessage(response.message);
          })
          .catch((error: AjaxError) => {
            const response = error.responseJSON;

            window.showErrorMessage(response.message);
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
  private toggleButtonDisplay($button: JQuery): void {
    const isActive = $button.hasClass('grid-toggler-icon-valid');

    const classToAdd = isActive
      ? 'grid-toggler-icon-not-valid'
      : 'grid-toggler-icon-valid';
    const classToRemove = isActive
      ? 'grid-toggler-icon-valid'
      : 'grid-toggler-icon-not-valid';
    const icon = isActive ? 'clear' : 'check';

    $button.removeClass(classToRemove);
    $button.addClass(classToAdd);

    if ($button.hasClass('material-icons')) {
      $button.text(icon);
    }
  }
}
