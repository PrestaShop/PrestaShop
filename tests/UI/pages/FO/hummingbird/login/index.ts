// Import pages
import {LoginPage} from '@pages/FO/login';

/**
 * Login page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Login extends LoginPage {
  /**
   * @constructs
   * Setting up texts and selectors to use on login page
   */
  constructor() {
    super();

    this.theme = 'hummingbird';

    this.displayRegisterFormLink = 'div a[data-link-action=\'display-register-form\']';
    this.passwordReminderLink = '.login__forgot-password a';
    this.alertDangerTextBlock = '.login .help-block .alert.alert-danger';
  }
}

export default new Login();
