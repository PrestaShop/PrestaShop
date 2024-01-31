// Import FO pages
import {AccountIdentityPage} from '@pages/FO/classic/myAccount/identity';

/**
 * @class
 * @extends FOBasePage
 */
class AccountIdentity extends AccountIdentityPage {
  /**
   * @constructs
   * Setting up texts and selectors to use
   */
  constructor() {
    super('hummingbird');
  }
}

export default new AccountIdentity();
