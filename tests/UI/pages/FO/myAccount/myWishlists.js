require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Vouchers page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class MyWishlists extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on vouchers page
   */
  constructor() {
    super();

    this.pageTitle = 'My wishlists';

    // Selectors
    this.headerTitle = '#content-wrapper h1';
  }

  /*
  Methods
   */
  /**
   * @override
   * Get the page title from the main section
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPageTitle(page) {
    return this.getTextContent(page, this.headerTitle);
  }
}

module.exports = new MyWishlists();
