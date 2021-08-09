require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Dashboard page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Dashboard extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on dashboard page
   */
  constructor() {
    super();

    this.pageTitle = 'Dashboard • ';
  }
}

module.exports = new Dashboard();
