require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Import extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Import • ';

    // Selectors
    this.downloadSampleFileLink = 'a[href*=\'import/sample/download/%TYPE\']';
  }

  /*
  Methods
   */

  /**
   * Click on simple file link to download it
   * @param type
   * @return {Promise<void>}
   */
  async downloadSampleFile(type) {
    await this.page.click(this.downloadSampleFileLink.replace('%TYPE', type));
  }
};
