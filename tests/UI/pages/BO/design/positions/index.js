require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Positions extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Positions';

    // Selectors
    this.searchInput = '#hook-search';
    this.modulePositionForm = '#module-positions-form';
    this.searchResultHookNameSpan = `${this.modulePositionForm} section[style] header span span`;
  }

  /*
 Methods
  */

  /**
   * Search for a hook
   * @param page
   * @param hookValue
   * @returns {Promise<string>}
   */
  async searchHook(page, hookValue) {
    await this.setValue(page, this.searchInput, hookValue);
    return this.getTextContent(page, this.searchResultHookNameSpan);
  }
}

module.exports = new Positions();
