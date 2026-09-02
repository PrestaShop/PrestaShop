/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Toggle DNI input requirement on country selection
 *
 * Usage:
 *
 * <!-- Country select options must have need_dni attribute when needed -->
 * <select name="id_country" id="id_country" states-url="path/to/states/api">
 *   ...
 *   <option value="6" need_dni="1">Spain</value>
 *   ...
 * </select>
 *
 * In JS:
 *
 * new CountryDniRequiredToggler('#id_country', '#id_country_dni', 'label[for="id_country_dni"]');
 */
export default class CountryDniRequiredToggler {
  $countryDniInput: JQuery;

  $countryDniInputLabel: JQuery;

  $countryInput: JQuery;

  countryInputSelectedSelector: string;

  countryDniInputLabelDangerSelector: string;

  constructor(
    countryInputSelector: string,
    countryDniInput: string,
    countryDniInputLabel: string,
  ) {
    this.$countryDniInput = $(countryDniInput);
    this.$countryDniInputLabel = $(countryDniInputLabel);
    this.$countryInput = $(countryInputSelector);
    this.countryInputSelectedSelector = `${countryInputSelector}>option:selected`;
    this.countryDniInputLabelDangerSelector = `${countryDniInputLabel}>span.text-danger`;

    // If field is required regardless of the country
    // keep it required
    if (this.$countryDniInput.attr('required')) {
      return;
    }

    this.$countryInput.on('change', () => this.toggle());

    // toggle on page load
    this.toggle();
  }

  /**
   * Toggles DNI input required
   *
   * @private
   */
  private toggle(): void {
    $(this.countryDniInputLabelDangerSelector).remove();
    this.$countryDniInput.prop('required', false);
    if (
      parseInt(
        <string>$(this.countryInputSelectedSelector).attr('need_dni'),
        10,
      ) === 1
    ) {
      this.$countryDniInput.prop('required', true);
      this.$countryDniInputLabel.prepend(
        $('<span class="text-danger">*</span>'),
      );
    }
  }
}
