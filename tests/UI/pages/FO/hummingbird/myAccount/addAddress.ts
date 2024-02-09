// Import FO pages
import {AddAddressPage} from '@pages/FO/classic/myAccount/addAddress';

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

    this.pageHeaderTitle = '#content-wrapper div h1';
  }
}

export default new AddAddress();
