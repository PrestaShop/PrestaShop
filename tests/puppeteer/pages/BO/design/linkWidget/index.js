require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class LinkWidget extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Link Widget •';

    // Header Selectors
    this.newBlockLink = '#page-header-desc-configuration-add';
  }

  /* Header methods */
  /**
   * Go to new Block page
   * @return {Promise<void>}
   */
  async goToNewBlockPage() {
    await this.clickAndWaitForNavigation(this.newBlockLink);
  }
};
