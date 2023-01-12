// Import pages
import FOBasePage from '@pages/FO/FObasePage';

/**
 * Best sellers products page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class BestSellers extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on best sellers products page
   */
  constructor() {
    super();

    this.pageTitle = 'Best sellers';
  }
}

export default new BestSellers();
