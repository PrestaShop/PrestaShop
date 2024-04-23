// Import FO pages
import {MerchandiseReturns} from '@pages/FO/classic/myAccount/merchandiseReturns';

/**
 * @class
 * @extends FOBasePage
 */
class MerchandiseReturnsPage extends MerchandiseReturns {
  /**
   * @constructs
   * Setting up texts and selectors to use
   */
  constructor() {
    super('hummingbird');
  }
}

export default new MerchandiseReturnsPage();
