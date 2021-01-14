require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class PersonalGdprData extends FOBasePage {
  constructor() {
    super();

    this.formTitle = 'GDPR - Personal data';

    // Selectors
    this.pageHeaderTitle = '#main .page-header h1';
    this.getPdfDataLink = '#exportDataToPdf';
  }

  /*
  Methods
   */

  /**
   * Get form header title
   * @param page
   * @return {Promise<string>}
   */
  getHeaderTitle(page) {
    return this.getTextContent(page, this.pageHeaderTitle);
  }

  /**
   * Download personal data on a pdf
   * @param page
   * @returns {Promise<string>}
   */
  async downloadPersonalDataOnPdf(page) {
    // Download file
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      await page.click(this.getPdfDataLink),
    ]);

    // Return file path
    return download.path();
  }
}

module.exports = new PersonalGdprData();
