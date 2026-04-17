/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * In Add/Edit page of Webservice key there is permissions table input (permissons as columns / resources as rows).
 * There is "All" column and once resource is checked under this column
 * every other permission column should be auto-selected for that resource.
 */
export default class PermissionsRowSelector {
  constructor() {
    // when checkbox in "All" column is checked
    $('input[id^="webservice_key_permissions_all"]').on(
      'change',
      (event: JQueryEventObject) => {
        const $checkedBox = $(event.currentTarget);

        const isChecked = $checkedBox.is(':checked');

        // for each input in same row we need to toggle its value
        $checkedBox
          .closest('tr')
          .find(`input:not(input[id="${$checkedBox.attr('id')}"])`)
          .each((i, input) => {
            $(input).prop('checked', isChecked);
          });
      },
    );
  }
}
