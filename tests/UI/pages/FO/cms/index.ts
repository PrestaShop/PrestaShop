// Import pages
import FOBasePage from '@pages/FO/FObasePage';

/**
 * CMS page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class CMS extends FOBasePage {
  public readonly pageNotFound: string;

  public readonly pageTitle: string;

  public readonly pageContent: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on cms page
   */
  constructor() {
    super();
    this.pageNotFound = 'The page you are looking for was not found.';

    // Selectors
    this.pageTitle = '#main header h1';
    this.pageContent = '#content';
  }
}

export default new CMS();
