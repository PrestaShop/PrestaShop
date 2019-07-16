const CommonPage = require('../commonPage');

module.exports = class BO_login extends CommonPage {

  constructor(page) {
    super(page);

    this.pageTitle = 'Dashboard • PrestaShop';

    this.emailInput = "#email";
    this.passwordInput = "#passwd";
    this.submitLoginButton = "#submit_login";
    this.stayLoggedInCheckbox = "#stay_logged_in";

    this.forgotPasswordLink = "#a.show-forgot-password";
    this.emailForgotInput = "#email_forgot";
    this.sendResetLinkButton = "#button[name=submitLogin].btn-default";
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
