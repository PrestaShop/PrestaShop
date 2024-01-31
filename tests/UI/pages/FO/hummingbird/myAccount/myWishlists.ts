// Import FO pages
import {MyWishlistsPage} from '@pages/FO/classic/myAccount/myWishlists';

/**
 * @class
 * @extends FOBasePage
 */
class MyWishlists extends MyWishlistsPage {
  /**
   * @constructs
   * Setting up texts and selectors to use
   */
  constructor() {
    super('hummingbird');
  }
}

export default new MyWishlists();
