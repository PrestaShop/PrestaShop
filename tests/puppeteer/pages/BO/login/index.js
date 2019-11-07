require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Login extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'PrestaShop';

    this.emailInput = '#email';
    this.passwordInput = '#passwd';
    this.submitLoginButton = '#submit_login';
  }

  /*
  Methods
   */

  /**
   * Enter credentials and submit login form
   * @param email
   * @param passwd
   * @returns {Promise<void>}
   */
  async login(email, passwd) {
    await this.page.type(this.emailInput, email);
    await this.page.type(this.passwordInput, passwd);
    await this.clickAndWaitForNavigation(this.submitLoginButton);
  }
};
