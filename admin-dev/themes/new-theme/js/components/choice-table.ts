/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import ComponentsMap from './components-map';

const ChoiceTableMap = ComponentsMap.choiceTable;

const {$} = window;

/**
 * ChoiceTable is responsible for managing common actions in choice table form type
 */
export default class ChoiceTable {
  /**
   * Init constructor
   */
  constructor() {
    $(document).on(
      'change',
      ChoiceTableMap.selectAll,
      (e: JQueryEventObject) => {
        this.handleSelectAll(e);
      },
    );
  }

  /**
   * Check/uncheck all boxes in table
   *
   * @param {Event} event
   */
  handleSelectAll(event: JQueryEventObject): void {
    const $selectAllCheckboxes = $(event.target);
    const isSelectAllChecked = $selectAllCheckboxes.is(':checked');

    $selectAllCheckboxes
      .closest('table')
      .find('tbody input:checkbox')
      .prop('checked', isSelectAllChecked);
  }
}
