require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Login extends BOBasePage {
  constructor() {
    super();

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
   * @param page
   * @param email
   * @param password
   * @param waitForNavigation, false if login should fail
   * @returns {Promise<void>}
   */
  async login(page, email, password, waitForNavigation = true) {
    await this.setValue(page, this.emailInput, email);
    await this.setValue(page, this.passwordInput, password);

    // Wait for navigation if the login is successful
    if (waitForNavigation) {
      await this.clickAndWaitForNavigation(page, this.submitLoginButton);
    } else {
      await page.click(this.submitLoginButton);
    }
  }

  /**
   * Get login error
   * @param page
   * @return {Promise<string>}
   */
  async getLoginError(page) {
    return this.getTextContent(page, this.alertDangerTextBlock);
  }
}

module.exports = new Login();
