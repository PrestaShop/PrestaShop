// Import FO pages
import {CreditSlipPage} from '@pages/FO/classic/myAccount/creditSlips';

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

    this.homeLink = 'nav.breadcrumb__wrapper li.breadcrumb-item:nth-child(1) a';
  }
}

export default new CreditSlip();
