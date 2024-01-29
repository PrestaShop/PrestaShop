// Import FO pages
import {CartPage} from '@pages/FO/classic/cart/index';

/**
 * Cart page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Cart extends CartPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');
  }
}

export default new Cart();
