// Import FO pages
import {CartPage} from '@pages/FO/cart/index';

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
    super();

    this.theme = 'hummingbird';
  }
}

export default new Cart();
