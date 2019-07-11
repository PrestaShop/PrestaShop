const Page = require('../page');

module.exports = class BO_login extends Page {

  constructor() {
    super();

    this.title = 'Dashboard • PrestaShop';

    this.BO_login_email_input = "#email";
    this.BO_login_password_input = "#passwd";
    this.BO_login_submitLogin_button = "#submit_login";
    this.BO_login_stayLoggedIn_checkbox = "#stay_logged_in";

    this.BO_login_forgotPassword_link = "#a.show-forgot-password";
    this.BO_login_emailForgot_input = "#email_forgot";
    this.BO_login_sendResetLink_button = "#button[name=submitLogin].btn-default";
  }

  /*
  Methods
   */

  async login(email, passwd) {
    await global.page.type(this.BO_login_email_input, email);
    await global.page.type(this.BO_login_password_input, passwd);
    await global.page.click(this.BO_login_submitLogin_button);
    await global.page.waitForNavigation();
  }

  async checkPageTitle() {
    let currentTitle = await global.page.title();
    await expect(currentTitle).to.equal(this.title);
  }

};
