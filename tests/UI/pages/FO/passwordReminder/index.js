require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Password reminder page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class PasswordReminder extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on password reminder page
   */
  constructor() {
    super();

    this.pageTitle = 'Forgot your password';
    this.errorMessage = 'You can regenerate your password only every 360 minute(s)';

    // Selectors
    this.emailFormField = '#email';
    this.backToLoginLink = '#back-to-login';
    this.sendResetLinkButton = '#send-reset-link';
    this.emailAddressText = 'section.renew-password .email';
    this.newPasswordInput = 'section.renew-password input[name=passwd]';
    this.confirmationPasswoedInput = 'section.renew-password input[name=confirmation]';
    this.submitButton = 'section.renew-password button[name=submit]';

    // Success message
    this.sendResetLinkSuccessAlert = '.ps-alert-success';
    // Error message
    this.errorMessageAlert = '.ps-alert-error';
  }

  /*
  Methods
   */

  /**
   * Fill the reset password email form field and click "send reset link" button
   * @param page {Page} Browser tab
   * @param email {string} Account's email to fill on input
   * @returns {Promise<void>}
   */
  async sendResetPasswordLink(page, email) {
    await this.setValue(page, this.emailFormField, email);
    await this.clickAndWaitForNavigation(page, this.sendResetLinkButton);
  }

  /**
   * Check that the success alert message is visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async checkResetLinkSuccess(page) {
    return this.getTextContent(page, this.sendResetLinkSuccessAlert);
  }

  /**
   * Open forgot password link
   * @param page {Page} Browser tab
   * @param emailBody {string} Text body in the mail
   * @returns {Promise<void>}
   */
  async openForgotPasswordPage(page, emailBody) {
    // To get reset email password from received email
    const resetPasswordURL = emailBody.split(new RegExp('(.*)(http.*password-recovery\\?.*)(\\s.*)'))[2];
    await this.goTo(page, resetPasswordURL);
  }

  /**
   * Get email address to reset
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getEmailAddressToReset(page) {
    return this.getTextContent(page, this.emailAddressText);
  }

  /**
   * Set new password
   * @param page {Page} Browser tab
   * @param password {string} New password to set
   * @returns {Promise<void>}
   */
  async setNewPassword(page, password) {
    await this.setValue(page, this.newPasswordInput, password);
    await this.setValue(page, this.confirmationPasswoedInput, password);
    await page.click(this.submitButton);
  }

  /**
   * Get error message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getErrorMessage(page) {
    return this.getTextContent(page, this.errorMessageAlert);
  }
}

module.exports = new PasswordReminder();
