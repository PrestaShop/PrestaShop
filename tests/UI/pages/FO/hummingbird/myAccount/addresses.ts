// Import FO pages
import {AddressesPage} from '@pages/FO/classic/myAccount/addresses';

/**
 * Create account page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Addresses extends AddressesPage {
  /**
   * @constructs
   * Setting up texts and selectors to use on create account page
   */
  constructor() {
    super('hummingbird');

    this.createNewAddressLink = '#content a.addresses__new-address';
    this.addressBodyTitle = 'article.address .card-body p';
  }
}

export default new Addresses();
