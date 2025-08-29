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
 * Class DeleteCustomerRowActionExtension handles submitting of row action
 */
export default class DeleteCustomerRowActionExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid
      .getContainer()
      .on('click', GridMap.rows.customerDeleteAction, (event) => {
        event.preventDefault();

        const $deleteCustomersModal = $(
          GridMap.bulks.deleteCustomerModal(grid.getId()),
        );
        $deleteCustomersModal.modal('show');

        $deleteCustomersModal.on(
          'click',
          GridMap.bulks.submitDeleteCustomers,
          () => {
            const $button = $(event.currentTarget);
            const customerId = $button.data('customer-id');

            this.addCustomerInput(customerId);

            const $form = $deleteCustomersModal.find('form');

            $form.attr('action', $button.data('customer-delete-url'));
            $form.submit();
          },
        );
      });
  }

  /**
   * Adds input for selected customer to delete form
   *
   * @param {integer} customerId
   *
   * @private
   */
  private addCustomerInput(customerId: number): void {
    const $customersToDeleteInputBlock = $(GridMap.bulks.customersToDelete);

    const customerInput = $customersToDeleteInputBlock
      .data('prototype')
      .replace(/__name__/g, $customersToDeleteInputBlock.children().length);

    const $item = $($.parseHTML(customerInput)[0]);
    $item.val(customerId);

    $customersToDeleteInputBlock.append($item);
  }
}
