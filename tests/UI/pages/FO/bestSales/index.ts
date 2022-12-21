import FOBasePage from '@pages/FO/FObasePage';

/**
 * Best sales page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class BestSales extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on best sales page
   */
  constructor() {
    super();

    this.pageTitle = 'Best sellers';
  }
}

export default new BestSales();
