// Import FO pages
import {Product} from '@pages/FO/classic/product';

/**
 * @class
 * @extends FOBasePage
 */
class ProductPage extends Product {
  /**
   * @constructs
   * Setting up texts and selectors to use on checkout page
   */
  constructor() {
    super('hummingbird');

    this.proceedToCheckoutButton = '#blockcart-modal div.cart-footer-actions a';
  }
}

export default new ProductPage();
