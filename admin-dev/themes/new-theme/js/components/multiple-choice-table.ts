/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import ComponentsMap from '@components/components-map';

const {$} = window;

/**
 * MultipleChoiceTable is responsible for managing common actions in multiple choice table form type
 */
export default class MultipleChoiceTable {
  /**
   * Init constructor
   */
  constructor() {
    $(document).on(
      'click',
      ComponentsMap.multipleChoiceTable.selectColumn,
      (e: JQueryEventObject) => this.handleSelectColumn(e),
    );
  }

  /**
   * Check/uncheck all boxes in column
   *
   * @param {Event} event
   */
  handleSelectColumn(event: JQueryEventObject): void {
    event.preventDefault();

    const $selectColumnBtn = $(event.target);
    const checked = $selectColumnBtn.data('column-checked');
    $selectColumnBtn.data('column-checked', !checked);

    const $table = $selectColumnBtn.closest('table');

    $table
      .find(
        ComponentsMap.multipleChoiceTable.selectColumnCheckbox(
          $selectColumnBtn.data('column-num'),
        ),
      )
      .prop('checked', !checked);
  }
}
