require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * BO login page, contains texts, selectors and functions to use on the page.
 * @class
 * @extends BOBasePage
 */
class Login extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use
   */
  constructor() {
    super();

    this.pageTitle = 'PrestaShop';
    this.loginErrorText = 'The employee does not exist, or the password provided is incorrect.';
    this.resetPasswordSuccessText = 'Please, check your mailbox.';

    this.emailInput = '#email';
    this.passwordInput = '#passwd';
    this.submitLoginButton = '#submit_login';
    this.alertDangerDiv = '#error';
    this.alertDangerTextBlock = `${this.alertDangerDiv} li`;
    // reset password selectors
    this.forgotPasswordLink = '#forgot-password-link';
    this.resetPasswordEmailFormField = '#email_forgot';
    this.resetPasswordButton = '#reset-password-button';
    this.resetPasswordSuccessConfirmationText = '#forgot_confirm_name';
  }

  /*
  Methods
   */

  /**
   * Enter credentials and submit login form
   * @param page {Page} Browser tab
   * @param email {string} String of employee email
   * @param password {string} String of employee password
   * @param waitForNavigation {boolean} true to wait for navigation after the click on button
   * @returns {Promise<void>}
   */
  async login(page, email, password, waitForNavigation = true) {
    await this.setValue(page, this.emailInput, email);
    await this.setValue(page, this.passwordInput, password);

    // Wait for navigation if the login is successful
    if (waitForNavigation) {
      await this.clickAndWaitForNavigation(page, this.submitLoginButton, 'load');
    } else {
      await page.click(this.submitLoginButton);
    }
  }

  /**
   * Get login error
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getLoginError(page) {
    return this.getTextContent(page, this.alertDangerTextBlock);
  }

  /**
   * Go to password reset page and send reset password link
   * @param page {Page} Browser tab
   * @param email {string} String of employee email
   * @returns {Promise<void>}
   */
  async sendResetPasswordLink(page, email) {
    await page.click(this.forgotPasswordLink);
    await this.waitForVisibleSelector(page, this.resetPasswordButton);
    await this.setValue(page, this.resetPasswordEmailFormField, email);
    await page.click(this.resetPasswordButton);
  }

  /**
   * Get and return reset password success message text
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getResetPasswordSuccessMessage(page) {
    return this.getTextContent(page, this.resetPasswordSuccessConfirmationText);
  }
}

module.exports = new Login();
