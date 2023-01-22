import FOBasePage from '@pages/FO/FObasePage';

/**
 * Stores page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Stores extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on stores page
   */
  constructor() {
    super();

    this.pageTitle = 'Stores';
  }
}

export default new Stores();
