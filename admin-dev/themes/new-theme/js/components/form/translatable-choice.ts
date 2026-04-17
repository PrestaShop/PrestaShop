/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ComponentsMap from '@components/components-map';

const {$} = window;

/**
 * Component responsible for filtering select values by language selected.
 */
export default class TranslatableChoice {
  constructor() {
    // registers the event which displays the popover
    $(document).on(
      'change',
      ComponentsMap.form.selectLanguage,
      (event: JQueryEventObject) => {
        this.filterSelect(event);
      },
    );

    $('select.translatable_choice_language').trigger('change');
    $('select.translatable_choice').trigger('change');
  }

  filterSelect(event: JQueryEventObject): void {
    const $element = $(event.currentTarget);
    const $formGroup = $element.closest('.form-group');
    const language = $element.find('option:selected').val();

    // show all the languages selects
    $formGroup
      .find(ComponentsMap.form.selectChoice(<string>language))
      .parent()
      .show();

    const $selects = $formGroup.find('select.translatable_choice');

    // Hide all the selects not corresponding to the language selected
    $selects
      .not(ComponentsMap.form.selectChoice(<string>language))
      .each((index, item) => {
        $(item)
          .parent()
          .hide();
      });

    // Bind choice selection to fill the hidden input
    this.bindValueSelection($selects);
  }

  bindValueSelection($selects: JQuery): void {
    $selects.each((index, element) => {
      $(element).on('change', (event) => {
        const $select = $(event.currentTarget);
        const selectId = $select.attr('id');
        const selectedValue = $select.find('option:selected').val();
        $(`#${selectId}_value`).val(<string>selectedValue);
      });
    });
  }
}
