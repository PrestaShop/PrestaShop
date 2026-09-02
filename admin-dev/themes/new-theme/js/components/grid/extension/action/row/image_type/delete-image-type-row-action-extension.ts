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
export default class DeleteImageTypeRowActionExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid
      .getContainer()
      .on('click', GridMap.rows.imageTypeDeleteAction, (event) => {
        event.preventDefault();

        const $button = $(event.currentTarget);
        const $deleteImageTypeModal = $(GridMap.rows.deleteImageTypeModal(grid.getId()));
        $deleteImageTypeModal.modal('show');

        $deleteImageTypeModal.on('click', GridMap.rows.submitDeleteImageType, () => {
          const $form = $deleteImageTypeModal.find('form');
          $form.attr('action', $button.data('delete-url'));
          $form.submit();
        });
      });
  }
}
