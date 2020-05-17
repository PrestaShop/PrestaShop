require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Login extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'PrestaShop';
    this.loginErrorText = 'The employee does not exist, or the password provided is incorrect.';

    this.emailInput = '#email';
    this.passwordInput = '#passwd';
    this.submitLoginButton = '#submit_login';
    this.alertDangerDiv = '#error';
    this.alertDangerTextBlock = `${this.alertDangerDiv} li`;
  }

  /*
  Methods
   */

  /**
   * Enter credentials and submit login form
   * @param email
   * @param passwd
   * @param waitForNavigation, false if login should fail
   * @returns {Promise<void>}
   */
  async login(email, passwd, waitForNavigation = true) {
    await this.page.type(this.emailInput, email);
    await this.page.type(this.passwordInput, passwd);
    if (waitForNavigation) {
      await this.clickAndWaitForNavigation(this.submitLoginButton);
    } else {
      await this.page.click(this.submitLoginButton);
    }
  }

  /**
   * Get login error
   * @return {Promise<string>}
   */
  async getLoginError() {
    return this.getTextContent(this.alertDangerTextBlock);
  }
};
