/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
