require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Positions page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Positions extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on positions page
   */
  constructor() {
    super();

    this.pageTitle = 'Positions';

    // Selectors
    this.searchInput = '#hook-search';
    this.modulePositionForm = '#module-positions-form';
    this.searchResultHookNameSpan = `${this.modulePositionForm} section[style] header span span`;
  }

  /* Methods */

  /**
   * Search for a hook
   * @param page {Page} Browser tab
   * @param hookValue {string} Value of hook to set on input
   * @returns {Promise<string>}
   */
  async searchHook(page, hookValue) {
    await this.setValue(page, this.searchInput, hookValue);

    return this.getTextContent(page, this.searchResultHookNameSpan);
  }
}

module.exports = new Positions();
