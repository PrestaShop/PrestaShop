require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class PasswordReminder extends FOBasePage {
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
   * @param email {string} String of the email
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
