
require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * CMS page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class CMS extends FOBasePage {
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

module.exports = new CMS();
