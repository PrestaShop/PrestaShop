// Import FO pages
import {DeliveryPage} from '@pages/FO/classic/delivery/index';

/**
 * Contact Us page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Delivery extends DeliveryPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');
  }
}

export default new Delivery();
