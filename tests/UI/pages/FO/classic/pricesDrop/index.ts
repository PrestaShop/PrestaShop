import FOBasePage from '@pages/FO/FObasePage';

/**
 * Prices drop page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class PricesDropPage extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on prices drop page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Prices drop';
  }
}

const pricesDropPage = new PricesDropPage();
export {pricesDropPage, PricesDropPage};
