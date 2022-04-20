require('module-alias/register');

/**
 * Parent Child Theme dev doc page, contains functions that can be used on the page
 * @class parentChildTheme
 */
class ParentChildTheme {
  /**
   * @constructs
   * Setting up texts and selectors to use on Parent Child theme dev doc page
   */
  constructor() {
    this.pageTitle = 'Parent/child theme :: PrestaShop Developer Documentation';
  }

  /**
   * Get page title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPageTitle(page) {
    return page.title();
  }
}
module.exports = new ParentChildTheme();
