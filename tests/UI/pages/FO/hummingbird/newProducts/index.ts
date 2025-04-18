// Import FO pages
import {NewProductsPage} from '@pages/FO/classic/newProducts/index';

/**
 * @class
 * @extends FOBasePage
 */
class NewProducts extends NewProductsPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');
  }
}

export default new NewProducts();
