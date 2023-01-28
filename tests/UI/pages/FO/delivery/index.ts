// Import pages
import FOBasePage from '@pages/FO/FObasePage';

/**
 * Delivery page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Delivery extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on delivery page
   */
  constructor() {
    super();

    this.pageTitle = 'Delivery';
  }
}

export default new Delivery();
