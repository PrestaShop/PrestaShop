// Import FO pages
import {AddAddressPage} from '@pages/FO/myAccount/addAddress';

/**
 * @class
 * @extends FOBasePage
 */
class AddAddress extends AddAddressPage {
  /**
   * @constructs
   * Setting up texts and selectors to use on create account page
   */
  constructor() {
    super('hummingbird');
  }
}

export default new AddAddress();
