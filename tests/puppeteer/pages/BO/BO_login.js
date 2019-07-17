const CommonPage = require('../commonPage');

module.exports = class BO_LOGIN extends CommonPage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Dashboard • PrestaShop';

    this.emailInput = '#email';
    this.passwordInput = '#passwd';
    this.submitLoginButton = '#submit_login';
  }

  /*
  Methods
   */

  async login(email, passwd) {
    await this.page.type(this.emailInput, email);
    await this.page.type(this.passwordInput, passwd);
    await this.page.click(this.submitLoginButton);
    await this.page.waitForNavigation();
  }
};
