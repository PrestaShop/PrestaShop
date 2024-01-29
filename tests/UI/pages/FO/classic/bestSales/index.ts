import FOBasePage from '@pages/FO/classic/FObasePage';

/**
 * Best sales page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class BestSalesPage extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on best sales page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Best sellers';
  }
}

const bestSalesPage = new BestSalesPage();
export {bestSalesPage, BestSalesPage};
