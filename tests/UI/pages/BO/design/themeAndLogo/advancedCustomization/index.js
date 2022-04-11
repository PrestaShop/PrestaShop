require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Advanced customization page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AdvancedCustomization extends BOBasePage{
  /**
   * @constructs
   * Setting up texts and selectors to use on advanced customization page
   */
  constructor() {
    super();

    this.pageTitle = 'Advanced Customization •';
  }
}

module.exports = new AdvancedCustomization();
