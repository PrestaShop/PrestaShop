require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Legal notice page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class LegalNotice extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on legal notice page
   */
  constructor() {
    super();

    this.pageTitle = 'Legal Notice';
  }
}

module.exports = new LegalNotice();
