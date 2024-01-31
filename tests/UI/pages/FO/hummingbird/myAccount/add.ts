// Import FO pages
import {CreateAccountPage} from '@pages/FO/classic/myAccount/add';

/**
 * Create account page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class CreateAccount extends CreateAccountPage {
  /**
   * @constructs
   * Setting up texts and selectors to use on create account page
   */
  constructor() {
    super('hummingbird');

    this.pageHeaderTitle = '#wrapper .page-header h1';
  }
}

export default new CreateAccount();
