import FOBasePage from '@pages/FO/FObasePage';

require('module-alias/register');

/**
 * Guest order tracking page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class GuestOrderTracking extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on Guest order tracking page
   */
  constructor() {
    super();
    this.pageTitle = 'Guest tracking';

    // Selectors for the page
  }
}

module.exports = new GuestOrderTracking();
