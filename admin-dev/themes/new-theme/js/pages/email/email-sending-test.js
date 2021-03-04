/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

const {$} = window;

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
      this.handle(event);
    });
  }

  /**
   * Handle test email sending
   *
   * @param {Event} event
   *
   * @private
   */
  handle(event) {
    // fill test email sending form with configured values
    $('#test_email_sending_mail_method').val($('input[name="form[mail_method]"]:checked').val());
    $('#test_email_sending_smtp_server').val($('#form_smtp_config_server').val());
    $('#test_email_sending_smtp_username').val($('#form_smtp_config_username').val());
    $('#test_email_sending_smtp_password').val($('#form_smtp_config_password').val());
    $('#test_email_sending_smtp_port').val($('#form_smtp_config_port').val());
    $('#test_email_sending_smtp_encryption').val($('#form_smtp_config_encryption').val());

    const $testEmailSendingForm = $(event.currentTarget).closest('form');

    this.resetMessages();

    this.hideSendEmailButton();
    this.showLoader();

    $.post({
      url: $testEmailSendingForm.attr('action'),
      data: $testEmailSendingForm.serialize(),
    }).then((response) => {
      this.hideLoader();
      this.showSendEmailButton();

      if (response.errors.length !== 0) {
        this.showErrors(response.errors);
        return;
      }

      this.showSuccess();
    });
  }

  /**
   * Make sure that additional content (alerts, loader) is not visible
   *
   * @private
   */
  resetMessages() {
    this.hideSuccess();
    this.hideErrors();
  }

  /**
   * Show success message
   *
   * @private
   */
  showSuccess() {
    this.$successAlert.removeClass('d-none');
  }

  /**
   * Hide success message
   *
   * @private
   */
  hideSuccess() {
    this.$successAlert.addClass('d-none');
  }

  /**
   * Show loader during AJAX call
   *
   * @private
   */
  showLoader() {
    this.$loader.removeClass('d-none');
  }

  /**
   * Hide loader
   *
   * @private
   */
  hideLoader() {
    this.$loader.addClass('d-none');
  }

  /**
   * Show errors
   *
   * @param {Array} errors
   *
   * @private
   */
  showErrors(errors) {
    errors.forEach((error) => {
      this.$errorAlert.append(`<p>${error}</p>`);
    });

    this.$errorAlert.removeClass('d-none');
  }

  /**
   * Hide errors
   *
   * @private
   */
  hideErrors() {
    this.$errorAlert.addClass('d-none').empty();
  }

  /**
   * Show send email button
   *
   * @private
   */
  showSendEmailButton() {
    this.$sendEmailBtn.removeClass('d-none');
  }

  /**
   * Hide send email button
   *
   * @private
   */
  hideSendEmailButton() {
    this.$sendEmailBtn.addClass('d-none');
  }
}

export default EmailSendingTest;
