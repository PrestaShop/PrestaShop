// Import FO pages
import {CreditSlipPage} from '@pages/FO/myAccount/creditSlips';

/**
 * @class
 * @extends FOBasePage
 */
class CreditSlip extends CreditSlipPage {
  /**
   * @constructs
   * Setting up texts and selectors to use on create account page
   */
  constructor() {
    super('hummingbird');
  }
}

export default new CreditSlip();
