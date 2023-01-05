import FOBasePage from '@pages/FO/FObasePage';

/**
 * New products page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class NewProducts extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on new products page
   */
  constructor() {
    super();

    this.pageTitle = 'New products';
  }
}

export default new NewProducts();
