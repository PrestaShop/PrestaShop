/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Displays, fills or hides State selection block depending on selected country.
 *
 * Usage:
 *
 * <!-- Country select must have unique identifier & url for states API -->
 * <select name="id_country" id="id_country" states-url="path/to/states/api">
 *   ...
 * </select>
 *
 * <!-- If selected country does not have states, then this block will be hidden -->
 * <div class="js-state-selection-block">
 *   <select name="id_state">
 *     ...
 *   </select>
 * </div>
 *
 * In JS:
 *
 * new CountryStateSelectionToggler('#id_country', '#id_state', '.js-state-selection-block');
 */
export default class CountryStateSelectionToggler {
  $stateSelectionBlock: JQuery;

  $countryStateSelector: JQuery;

  $countryInput: JQuery;

  constructor(
    countryInputSelector: string,
    countryStateSelector: string,
    stateSelectionBlockSelector: string,
  ) {
    this.$stateSelectionBlock = $(stateSelectionBlockSelector);
    this.$countryStateSelector = $(countryStateSelector);
    this.$countryInput = $(countryInputSelector);

    this.$countryInput.on('change', () => this.onChange());
    this.onChange();
  }

  /**
   * Change State selection
   *
   * @private
   */
  private onChange(): void {
    const countryId = this.$countryInput.val();

    if (countryId === '') {
      return;
    }
    $.get({
      url: this.$countryInput.data('states-url'),
      dataType: 'json',
      data: {
        id_country: countryId,
      },
    })
      .then((response) => {
        this.$countryStateSelector.empty();

        Object.keys(response.states).forEach((value) => {
          this.$countryStateSelector.append(
            $('<option></option>')
              .attr('value', response.states[value])
              .text(value),
          );
        });

        this.toggle();
      })
      .catch((response: AjaxError) => {
        if (typeof response.responseJSON !== 'undefined') {
          window.showErrorMessage(response.responseJSON.message);
        }
      });
  }

  toggle(): void {
    // Display the field State if:
    // - there is options in the select
    // - (OR)
    // - there is error for the field
    this.$stateSelectionBlock.toggleClass(
      'd-none',
      this.$countryStateSelector.find('option').length === 0 && !this.$stateSelectionBlock.hasClass('has-error'),
    );
  }
}
