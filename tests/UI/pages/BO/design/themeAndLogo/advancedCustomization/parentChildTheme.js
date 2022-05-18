require('module-alias/register');
const CommonPage = require('@pages/commonPage');

/**
 * Parent Child Theme dev doc page, contains functions that can be used on the page
 * @class parentChildTheme
 * @extends CommonPage
 */
class ParentChildTheme extends CommonPage {
  /**
   * @constructs
   * Setting up texts and selectors to use on Parent Child theme dev doc page
   */
  constructor() {
    super();

    this.pageTitle = 'Parent/child theme :: PrestaShop Developer Documentation';
  }
}

module.exports = new ParentChildTheme();
