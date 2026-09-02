/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

class TermsAndConditionsOptionHandler {
  constructor() {
    this.handle();

    $('input[name="general[enable_tos]"]').on('change', () => this.handle());
  }

  handle(): void {
    const tosEnabledVal = $('input[name="general[enable_tos]"]:checked').val();
    const isTosEnabled = parseInt(<string>tosEnabledVal, 10);

    this.handleTermsAndConditionsCmsSelect(isTosEnabled);
  }

  /**
   * If terms and conditions option is disabled, then terms and conditions
   * cms select must be disabled.
   *
   * @param {int} isTosEnabled
   */
  handleTermsAndConditionsCmsSelect(isTosEnabled: number): void {
    $('#form_general_tos_cms_id').prop('disabled', !isTosEnabled);
  }
}

export default TermsAndConditionsOptionHandler;
