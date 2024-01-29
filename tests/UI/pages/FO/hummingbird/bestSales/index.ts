// Import FO pages
import {BestSalesPage} from '@pages/FO/classic/bestSales/index';

/**
 * Contact Us page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class BestSales extends BestSalesPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');
  }
}

export default new BestSales();
