require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Stats page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Stats extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on stats page
   */
  constructor() {
    super();

    this.pageTitle = 'Stats •';

    // Selectors
  }

  /*
  Methods
   */
}

module.exports = new Stats();
