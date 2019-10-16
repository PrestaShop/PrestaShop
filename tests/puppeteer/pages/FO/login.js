require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class Login extends FOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Login';

    // Selectors
    this.loginForm = '#login-form';
    this.emailInput = `${this.loginForm} input[name='email']`;
    this.passwordInput = `${this.loginForm} input[name='password']`;
    this.signInButton = `${this.loginForm} button#submit-login`;
  }

  /*
  Methods
   */

  /**
   * Login in FO
   * @param customer
   * @return {Promise<void>}
   */
  async customerLogin(customer) {
    await this.setValue(this.emailInput, customer.email);
    await this.setValue(this.passwordInput, customer.password);
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.signInButton),
    ]);
  }
};
