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

    // Selectors
    this.emailFormField = '#email';
    this.backToLoginLink = '#back-to-login';
    this.sendResetLinkButton = '#send-reset-link';

    // Success message
    this.sendResetLinkSuccessAlert = '.ps-alert-success';
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
}

module.exports = new PasswordReminder();
