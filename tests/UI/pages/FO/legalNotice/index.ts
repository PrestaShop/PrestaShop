// Import pages
import FOBasePage from '@pages/FO/FObasePage';

/**
 * Legal notice page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class LegalNoticePage extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on legal notice page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Legal Notice';
  }
}

const legalNoticePage = new LegalNoticePage();
export {legalNoticePage, LegalNoticePage};
