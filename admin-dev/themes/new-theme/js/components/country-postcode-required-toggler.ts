/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Toggle Postcode input requirement on country selection
 *
 * Usage:
 *
 * <!-- Country select options must have need_postcode attribute when needed -->
 * <select name="id_country" id="id_country" states-url="path/to/states/api">
 *   ...
 *   <option value="6" need_postcode="1">Spain</value>
 *   ...
 * </select>
 *
 * In JS:
 *
 * new CountryPostcodeRequiredToggler('#id_country', '#id_country_postcode', 'label[for="id_country_postcode"]');
 */
export default class CountryPostcodeRequiredToggler {
  $countryPostcodeInput: JQuery;

  $countryPostcodeInputLabel: JQuery;

  $countryInput: JQuery;

  countryInputSelectedSelector: string;

  countryPostcodeInputLabelDangerSelector: string;

  constructor(
    countryInputSelector: string,
    countryPostcodeInput: string,
    countryPostcodeInputLabel: string,
  ) {
    this.$countryPostcodeInput = $(countryPostcodeInput);
    this.$countryPostcodeInputLabel = $(countryPostcodeInputLabel);
    this.$countryInput = $(countryInputSelector);
    this.countryInputSelectedSelector = `${countryInputSelector}>option:selected`;
    this.countryPostcodeInputLabelDangerSelector = `${countryPostcodeInputLabel}>span.text-danger`;

    // If field is required regardless of the country
    // keep it required
    if (this.$countryPostcodeInput.attr('required')) {
      return;
    }

    this.$countryInput.on('change', () => this.toggle());

    // toggle on page load
    this.toggle();
  }

  /**
   * Toggles Postcode input required
   *
   * @private
   */
  private toggle(): void {
    $(this.countryPostcodeInputLabelDangerSelector).remove();
    this.$countryPostcodeInput.prop('required', false);
    if (
      parseInt(
        <string>$(this.countryInputSelectedSelector).attr('need_postcode'),
        10,
      ) === 1
    ) {
      this.$countryPostcodeInput.prop('required', true);
      this.$countryPostcodeInputLabel.prepend(
        $('<span class="text-danger">*</span>'),
      );
    }
  }
}
