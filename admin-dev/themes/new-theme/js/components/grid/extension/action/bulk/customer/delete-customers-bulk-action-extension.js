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
 * Handles bulk delete for "Customers" grid.
 */
export default class DeleteCustomersBulkActionExtension {

  constructor() {
    return {
      extend: (grid) => this.extend(grid),
    };
  }

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid) {
    grid.getContainer().on('click', '.js-delete-customers-bulk-action', (event) => {
      event.preventDefault();

      const submitUrl = $(event.currentTarget).data('customers-delete-url');

      const $modal = $(`#${grid.getId()}_grid_delete_customers_modal`);
      $modal.modal('show');

      $modal.on('click', '.js-submit-delete-customers', () => {
        const $checkboxes = grid.getContainer().find('.js-bulk-action-checkbox:checked');
        const $customersInput = $('#delete_customers_customers_to_delete');

        $checkboxes.each((i, value) => {
          const $input = $(value);
          const customerInput = $customersInput
            .data('prototype')
            .replace(/__name__/g, $input.val())
          ;

          const $item = $($.parseHTML(customerInput)[0]);
          $item.val($input.val());

          $customersInput.append($item);
        });

        const $form = $modal.find('form');

        $form.attr('action', submitUrl);
        $form.submit();
      });
    });
  }
}
