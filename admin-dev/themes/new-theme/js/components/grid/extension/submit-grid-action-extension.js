/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

/**
 * Class SubmitGridActionExtension handles grid action submits
 */
export default class SubmitGridActionExtension {
  constructor() {
    return {
      'extend': (grid) => this.extend(grid)
    };
  }

  extend(grid) {
    grid.getContainer().on('click', '.js-grid-action-submit-btn', (event) => {
      this.handleSubmit(event, grid);
    });
  }

  /**
   * Handle grid action submit.
   * It uses grid form to submit actions.
   *
   * @param {Event} event
   * @param {Grid} grid
   *
   * @private
   */
  handleSubmit(event, grid) {
    const $submitBtn = $(event.currentTarget);
    const confirmMessage = $submitBtn.data('confirm-message');

    if (typeof confirmMessage !== "undefined" && 0 < confirmMessage.length) {
      if (!confirm(confirmMessage)) {
        return;
      }
    }

    const $form = $('#' + grid.getId() + '_filter_form');

    $form.attr('action', $submitBtn.data('url'));
    $form.attr('method', $submitBtn.data('method'));
    $form.find('input[name="' + grid.getId() + '[_token]"]').val($submitBtn.data('csrf'));
    $form.submit();
  }
}
