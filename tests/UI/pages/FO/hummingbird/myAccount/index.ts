// Import FO pages
import {MyAccountPage} from '@pages/FO/myAccount/index';

/**
 * My account page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class MyAccount extends MyAccountPage {
  /**
   * @constructs
   * Setting up texts and selectors to use on my account page
   */
  constructor() {
    super('hummingbird');

    this.logoutFooterLink = '#my-account .account-menu .account-menu--signout';
  }
}

export default new MyAccount();
