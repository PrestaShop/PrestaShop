/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

/**
 * Class is responsible for managing test email sending
 */
class EmailSendingTest {
  constructor() {
    this.$successAlert = $('.js-test-email-success');
    this.$errorAlert = $('.js-test-email-errors');
    this.$loader = $('.js-test-email-loader');
    this.$sendEmailBtn = $('.js-send-test-email-btn');

    this.$sendEmailBtn.on('click', (event) => {
      this._handle(event);
    });
  }

  /**
   * Handle test email sending
   *
   * @param {Event} event
   *
   * @private
   */
  _handle(event) {
    // fill test email sending form with configured values
    $('#test_email_sending_mail_method').val($('input[name="form[email_config][mail_method]"]:checked').val());
    $('#test_email_sending_smtp_server').val($('#form_smtp_config_server').val());
    $('#test_email_sending_smtp_username').val($('#form_smtp_config_username').val());
    $('#test_email_sending_smtp_password').val($('#form_smtp_config_password').val());
    $('#test_email_sending_smtp_port').val($('#form_smtp_config_port').val());
    $('#test_email_sending_smtp_encryption').val($('#form_smtp_config_encryption').val());

    const $testEmailSendingForm = $(event.currentTarget).closest('form');

    this._resetMessages();

    this._hideSendEmailButton();
    this._showLoader();

    $.post({
      url: $testEmailSendingForm.attr('action'),
      data: $testEmailSendingForm.serialize(),
    }).then((response) => {
      this._hideLoader();
      this._showSendEmailButton();

      if (0 === response.errors.length) {
        this._showSuccess();

        return;
      }

      this._showErrors(response.errors);
    });
  }

  /**
   * Make sure that additional content (alerts, loader) is not visible
   *
   * @private
   */
  _resetMessages() {
    this._hideSuccess();
    this._hideErrors();
  }

  /**
   * Show success message
   *
   * @private
   */
  _showSuccess() {
    this.$successAlert.removeClass('d-none');
  }

  /**
   * Hide success message
   *
   * @private
   */
  _hideSuccess() {
    this.$successAlert.addClass('d-none');
  }

  /**
   * Show loader during AJAX call
   *
   * @private
   */
  _showLoader() {
    this.$loader.removeClass('d-none');
  }

  /**
   * Hide loader
   *
   * @private
   */
  _hideLoader() {
    this.$loader.addClass('d-none');
  }

  /**
   * Show errors
   *
   * @param {Array} errors
   *
   * @private
   */
  _showErrors(errors) {
    errors.forEach((error) => {
      this.$errorAlert.append('<p>' + error + '</p>');
    });

    this.$errorAlert.removeClass('d-none');
  }

  /**
   * Hide errors
   *
   * @private
   */
  _hideErrors() {
    this.$errorAlert.addClass('d-none').empty();
  }

  /**
   * Show send email button
   *
   * @private
   */
  _showSendEmailButton() {
    this.$sendEmailBtn.removeClass('d-none');
  }

  /**
   * Hide send email button
   *
   * @private
   */
  _hideSendEmailButton() {
    this.$sendEmailBtn.addClass('d-none');
  }
}

export default EmailSendingTest;
