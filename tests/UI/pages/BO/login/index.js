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
   * Fill email input
   * @param page {Page} Browser tab
   * @param email {string} String of employee email
   * @return {Promise<void>}
   */
  async fillEmailInput(page, email) {
    await this.setValue(page, this.emailInput, email);
  }

  /**
   * Fill password input
   * @param page {Page} Browser tab
   * @param password {string} String of employee password
   * @return {Promise<void>}
   */
  async fillPasswordInput(page, password) {
    await this.setValue(page, this.passwordInput, password);
  }

  /**
   * Enter credentials for login form
   * @param page {Page} Browser tab
   * @param email {string} String of employee email
   * @param password {string} String of employee password
   * @return {Promise<void>}
   */
  async fillForm(page, email, password) {
    await this.fillEmailInput(page, email);
    await this.fillPasswordInput(page, password);
  }

  /**
   * Click on login button
   * @param page {Page} Browser tab
   * @param waitForNavigation {boolean} true to wait for navigation after the click on button
   * @return {Promise<void>}
   */
  async clickOnLoginButton(page, waitForNavigation) {
    // Wait for navigation if the login is successful
    if (waitForNavigation) {
      await this.clickAndWaitForNavigation(page, this.submitLoginButton);
    } else {
      await page.click(this.submitLoginButton);
    }
  }

  /**
   * Fill login form and success login
   * @param page {Page} Browser tab
   * @param email {string} String of employee email
   * @param password {string} String of employee password
   * @returns {Promise<void>}
   */
  async successLogin(page, email, password) {
    await this.fillForm(page, email, password);
    await this.clickOnLoginButton(page, true);
  }

  /**
   * Fill login form and submit without waiting for success
   * @param page {Page} Browser tab
   * @param email {string} String of employee email
   * @param password {string} String of employee password
   * @return {Promise<void>}
   */
  async failedLogin(page, email, password) {
    await this.fillForm(page, email, password);
    await this.clickOnLoginButton(page, false);
  }

  /**
   * Get login error
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getLoginError(page) {
    return this.getTextContent(page, this.alertDangerTextBlock);
  }

  // Reset password functions
  /**
   * Go to forgot password view
   * @param page
   * @return {Promise<void>}
   */
  async goToForgotPasswordView(page) {
    await page.click(this.forgotPasswordLink);
    await this.waitForVisibleSelector(page, this.resetPasswordButton);
  }

  /**
   * Fill reset password email field
   * @param page {Page} Browser tab
   * @param email {string} String of employee email
   * @return {Promise<void>}
   */
  async fillResetPasswordEmailInput(page, email) {
    await this.setValue(page, this.resetPasswordEmailFormField, email);
  }

  /**
   * Go to password reset page and send reset password link
   * @param page {Page} Browser tab
   * @param email {string} String of employee email
   * @returns {Promise<void>}
   */
  async sendResetPasswordLink(page, email) {
    await this.goToForgotPasswordView(page);
    await this.fillResetPasswordEmailInput(page, email);
    await page.click(this.resetPasswordButton);
  }

  /**
   * Get and return reset password success message text
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getResetPasswordSuccessMessage(page) {
    return this.getTextContent(page, this.resetPasswordSuccessConfirmationText);
  }
}

module.exports = new Login();
