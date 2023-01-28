import FOBasePage from '@pages/FO/FObasePage';

/**
 * Prices drop page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class PricesDrop extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on prices drop page
   */
  constructor() {
    super();

    this.pageTitle = 'Prices drop';
  }
}

export default new PricesDrop();
