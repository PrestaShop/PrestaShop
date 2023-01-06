// Import pages
import FOBasePage from '@pages/FO/FObasePage';

/**
 * Legal notice page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class LegalNotice extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on legal notice page
   */
  constructor() {
    super();

    this.pageTitle = 'Legal Notice';
  }
}

export default new LegalNotice();
