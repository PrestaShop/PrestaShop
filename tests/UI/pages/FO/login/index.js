require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Login page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Login extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on login page
   */
  constructor() {
    super();

    this.pageTitle = 'Login';
    this.loginErrorText = 'Authentication failed.';
    this.disabledAccountErrorText = 'Your account isn\'t available at this time, please contact us';

    // Selectors
    this.loginForm = '#login-form';
    this.emailInput = `${this.loginForm} input[name='email']`;
    this.passwordInput = `${this.loginForm} input[name='password']`;
    this.signInButton = `${this.loginForm} button#submit-login`;
    this.displayRegisterFormLink = 'div.no-account a[data-link-action=\'display-register-form\']';
    this.passwordReminderLink = '.forgot-password a';
    this.showPasswordButton = '#login-form button[data-action=show-password]';
    this.alertDangerTextBlock = '#content section.login-form div.help-block li.alert-danger';
  }

  /*
  Methods
   */

  /**
   * Login in FO
   * @param page {Page} Browser tab
   * @param customer {object} Customer's information (email and password)
   * @param waitForNavigation {boolean} true to wait for navigation after the click on button
   * @return {Promise<void>}
   */
  async customerLogin(page, customer, waitForNavigation = true) {
    await this.setValue(page, this.emailInput, customer.email);
    await this.setValue(page, this.passwordInput, customer.password);
    if (waitForNavigation) {
      await this.clickAndWaitForNavigation(page, this.signInButton);
    } else {
      await page.click(this.signInButton);
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
   * Get password type
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPasswordType(page) {
    return this.getAttributeContent(page, this.passwordInput, 'type');
  }

  /**
   * Show password
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async showPassword(page) {
    await this.waitForSelectorAndClick(page, this.showPasswordButton);

    return this.getAttributeContent(page, this.passwordInput, 'type');
  }

  /**
   * Go to create account page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToCreateAccountPage(page) {
    await this.clickAndWaitForNavigation(page, this.displayRegisterFormLink);
  }

  /**
   * Go to the password reminder page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToPasswordReminderPage(page) {
    await this.clickAndWaitForNavigation(page, this.passwordReminderLink);
  }
}

module.exports = new Login();
