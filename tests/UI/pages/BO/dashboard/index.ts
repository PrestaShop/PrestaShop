import BOBasePage from '@pages/BO/BObasePage';

/**
 * Dashboard page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Dashboard extends BOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on dashboard page
   */
  constructor() {
    super();

    this.pageTitle = 'Dashboard • ';
  }
}

export default new Dashboard();
