/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

        const $newStateInput = $button.find('input:checked');
        const newState = Boolean($newStateInput.val());

        $.post({
          url: $button.data('toggle-url'),
        })
          .then((response) => {
            if (response.status) {
              window.showSuccessMessage(response.message);

              this.toggleButtonDisplay($button);

              return;
            }

            this.showErrorMessage(response.message, $newStateInput.prop('name'), !newState);
          })
          .catch((error: AjaxError) => {
            const response = error.responseJSON;
            this.showErrorMessage(response.message, $newStateInput.prop('name'), !newState);
          });
      });
  }

  private showErrorMessage(message: string, switchName: string, initialState: boolean): void {
    // We need to toggle back the switch state
    this.toggleSwitch(switchName, initialState);

    window.showErrorMessage(message);
  }

  private toggleSwitch(switchName: string, checked: boolean): void {
    const $switchOn = $(`[name="${switchName}"][value="1"]`);
    const $switchOff = $(`[name="${switchName}"][value="0"]`);

    if ($switchOn.is(':checked') !== checked) {
      $switchOn.prop('checked', checked);
    }
    if ($switchOff.is(':checked') === checked) {
      $switchOff.prop('checked', !checked);
    }
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
