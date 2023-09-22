// Import FO pages
import {PricesDropPage} from '@pages/FO/pricesDrop/index';

/**
 * @class
 * @extends FOBasePage
 */
class PricesDrop extends PricesDropPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');
  }
}

export default new PricesDrop();
