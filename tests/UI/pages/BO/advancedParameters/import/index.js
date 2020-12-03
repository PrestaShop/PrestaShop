require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Import extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Import • ';

    // Selectors
    this.downloadSampleFileLink = type => `a[href*='import/sample/download/${type}']`;
  }

  /*
  Methods
   */

  /**
   * Click on simple file link to download it
   * @param page
   * @param type
   * @return {Promise<void>}
   */
  async downloadSampleFile(page, type) {
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      await page.click(this.downloadSampleFileLink(type)),
    ]);
    return download.path();
  }
}
module.exports = new Import();
