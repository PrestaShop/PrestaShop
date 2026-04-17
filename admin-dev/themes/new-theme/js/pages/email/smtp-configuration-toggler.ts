/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Class SmtpConfigurationToggler is responsible for showing/hiding SMTP configuration form
 */
class SmtpConfigurationToggler {
  constructor() {
    $('.js-email-method').on('change', 'input[type="radio"]', (event) => {
      const mailMethod = Number($(event.currentTarget).val());

      $('.js-smtp-configuration').toggleClass(
        'd-none',
        this.getSmtpMailMethodOption() !== mailMethod,
      );
    });
  }

  /**
   * Get SMTP mail option value
   *
   * @private
   *
   * @returns {Number}
   */
  private getSmtpMailMethodOption(): number {
    return $('.js-email-method').data('smtp-mail-method');
  }
}

export default SmtpConfigurationToggler;
