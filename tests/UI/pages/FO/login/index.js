require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Login extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Login';

    // Selectors
    this.loginForm = '#login-form';
    this.emailInput = `${this.loginForm} input[name='email']`;
    this.passwordInput = `${this.loginForm} input[name='password']`;
    this.signInButton = `${this.loginForm} button#submit-login`;
    this.displayRegisterFormLink = 'div.no-account a[data-link-action=\'display-register-form\']';
    this.passwordReminderLink = '.forgot-password a';
  }

  /*
  Methods
   */

  /**
   * Login in FO
   * @param page {Page} Browser tab
   * @param customer {object} Object of the customer infos (email, password, ....)
   * @return {Promise<void>}
   */
  async customerLogin(page, customer) {
    await this.setValue(page, this.emailInput, customer.email);
    await this.setValue(page, this.passwordInput, customer.password);
    await this.clickAndWaitForNavigation(page, this.signInButton);
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
