/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';

const {$} = window;

/**
 * Allows submitting form inside modals.
 * Form must be inside modal, see example structure below:
 *
 * <div class="modal" id="uniqueModalId">
 *  <form data-bulk-inputs-id="bulkInputs">
 *    <div class="d-none">
 *      <div id="bulkInputs" data-prototype="<input type="hidden" name="__name__"/>"></div>
 *    </div>
 *  </form>
 * </div>
 *
 * Note that "data-prototype" is required to add checked items to the form. "__name__"
 * will be replaced with value of bulk checkbox.
 */
export default class ModalFormSubmitExtension {
  extend(grid: Grid): void {
    grid
      .getContainer()
      .on(
        'click',
        GridMap.bulks.modalFormSubmitBtn,
        (event: JQueryEventObject) => {
          const modalId = $(event.target).data('modal-id');

          const $modal = $(`#${modalId}`);
          $modal.modal('show');

          $modal.find(GridMap.actions.submitModalFormBtn).on('click', () => {
            const $form = $modal.find('form');
            const $bulkInputsBlock = $form.find(
              GridMap.actions.bulkInputsBlock($form.data('bulk-inputs-id')),
            );
            const $checkboxes = grid
              .getContainer()
              .find(GridMap.bulks.checkedCheckbox);

            $checkboxes.each((i, element) => {
              const $checkbox = $(element);

              const input = $bulkInputsBlock
                .data('prototype')
                .replace(/__name__/g, $checkbox.val());

              const $input = $($.parseHTML(input)[0]);
              $input.val(<string>$checkbox.val());

              $form.append($input);
            });

            $form.submit();
          });
        },
      );
  }
}
