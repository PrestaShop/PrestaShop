import FOBasePage from '@pages/FO/FObasePage';

/**
 * About us page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class AboutUs extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on about us page
   */
  constructor() {
    super();

    this.pageTitle = 'About us';
  }
}

export default new AboutUs();
