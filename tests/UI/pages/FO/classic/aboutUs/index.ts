import FOBasePage from '@pages/FO/classic/FObasePage';

/**
 * About us page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class AboutUsPage extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on about us page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'About us';
  }
}

const aboutUsPage = new AboutUsPage();
export {aboutUsPage, AboutUsPage};
