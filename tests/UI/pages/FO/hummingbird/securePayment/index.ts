// Import FO pages
import {SecurePaymentPage} from '@pages/FO/securePayment/index';

/**
 * @class
 * @extends FOBasePage
 */
class SecurePayment extends SecurePaymentPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');
  }
}

export default new SecurePayment();
