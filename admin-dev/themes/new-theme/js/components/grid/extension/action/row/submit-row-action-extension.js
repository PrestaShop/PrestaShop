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
 * Class SubmitRowActionExtension handles submitting of row action
 */
export default class SubmitRowActionExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid) {
    grid.getContainer().on('click', '.js-submit-row-action', (event) => {
      event.preventDefault();

      const $button = $(event.currentTarget);
      const confirmMessage = $button.data('confirm-message');

      if (confirmMessage.length && !confirm(confirmMessage)) {
        return;
      }

      const method = $button.data('method');
      const isGetOrPostMethod = ['GET', 'POST'].includes(method);

      const $form = $('<form>', {
        'action': $button.data('url'),
        'method': isGetOrPostMethod ? method : 'POST',
      }).appendTo('body');

      if (!isGetOrPostMethod) {
        $form.append($('<input>', {
          'type': '_hidden',
          'name': '_method',
          'value': method
        }));
      }

      $form.submit();
    });
  }
}
