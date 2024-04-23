// Import FO pages
import {VouchersPage} from '@pages/FO/classic/myAccount/vouchers';

/**
 * @class
 * @extends FOBasePage
 */
class Vouchers extends VouchersPage {
  /**
   * @constructs
   * Setting up texts and selectors to use
   */
  constructor() {
    super('hummingbird');
  }
}

export default new Vouchers();
