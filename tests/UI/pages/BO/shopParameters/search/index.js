require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Search extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Search •';

    // Selectors
    // Tabs
    this.tagsTabLink = '#subtab-AdminTags';
  }

  /*
  Methods
   */

  /**
   * Go to tags page
   * @param page
   * @returns {Promise<void>}
   */
  async goToTagsPage(page) {
    await this.clickAndWaitForNavigation(page, this.tagsTabLink);
  }
}

module.exports = new Search();
