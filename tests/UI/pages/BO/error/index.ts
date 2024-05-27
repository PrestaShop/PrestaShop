import BOBasePage from '@pages/BO/BObasePage';

/**
 * Error page, contains selectors and functions for the error pages
 * @class
 * @extends BOBasePage
 */
class Error extends BOBasePage {
  public readonly notFoundTitle: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on error pages
   */
  constructor() {
    super();

    this.notFoundTitle = 'Page not found';
  }
}

export default new Error();
