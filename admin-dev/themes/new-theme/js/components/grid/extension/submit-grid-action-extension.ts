/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';

const {$} = window;

/**
 * Class SubmitGridActionExtension handles grid action submits
 */
export default class SubmitGridActionExtension {
  extend(grid: Grid): void {
    grid
      .getHeaderContainer()
      .on(
        'click',
        GridMap.bulks.gridSubmitAction,
        (event: JQueryEventObject) => {
          this.handleSubmit(event, grid);
        },
      );
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
  private handleSubmit(event: JQueryEventObject, grid: Grid): void {
    const $submitBtn = $(event.currentTarget);
    const confirmMessage = $submitBtn.data('confirm-message');

    if (
      typeof confirmMessage !== 'undefined'
      && confirmMessage.length > 0
      && !window.confirm(confirmMessage)
    ) {
      return;
    }

    const $form = $(GridMap.filterForm(grid.getId()));

    $form.attr('action', $submitBtn.data('url'));
    $form.attr('method', $submitBtn.data('method'));
    $form
      .find(GridMap.actions.tokenInput(grid.getId()))
      .val($submitBtn.data('csrf'));
    $form.submit();
  }
}
