/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';

const {$} = window;

/**
 * Handles bulk delete for "Customers" grid.
 */
export default class DeleteCustomersBulkActionExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid.getContainer().on('click', GridMap.bulks.deleteCustomers, (event) => {
      event.preventDefault();

      const submitUrl = $(event.currentTarget).data('customers-delete-url');

      const $modal = $(GridMap.bulks.deleteCustomerModal(grid.getId()));
      $modal.modal('show');

      $modal.on('click', GridMap.bulks.submitDeleteCustomers, () => {
        const $selectedCustomerCheckboxes = grid
          .getContainer()
          .find(GridMap.bulks.checkedCheckbox);

        $selectedCustomerCheckboxes.each((i, checkbox) => {
          const $input = $(checkbox);

          this.addCustomerToDeleteCollectionInput(<number>$input.val());
        });

        const $form = $modal.find('form');

        $form.attr('action', submitUrl);
        $form.submit();
      });
    });
  }

  /**
   * Create input with customer id and add it to delete collection input
   *
   * @private
   */
  private addCustomerToDeleteCollectionInput(customerId: number): void {
    const $customersInput = $(GridMap.bulks.customersToDelete);

    const customerInput = $customersInput
      .data('prototype')
      .replace(/__name__/g, customerId);
    const $item = $($.parseHTML(customerInput)[0]);
    $item.val(customerId);

    $customersInput.append($item);
  }
}
