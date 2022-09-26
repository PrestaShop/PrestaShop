require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * GDPR personal data page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class GDPRPersonalData extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on gdpr personal data page
   */
  constructor() {
    super();

    this.pageTitle = 'GDPR - Personal data';

    // Selectors
    this.headerTitle = '#content-wrapper h1';
    this.exportDataToPDFButton = '#exportDataToPdf';
    this.contactUsLink = '#content section.page_content a[href*=\'contact-us\']';
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

  /**
   * Export data to PDF
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async exportDataToPDF(page) {
    return this.clickAndWaitForDownload(page, this.exportDataToPDFButton);
  }

  /**
   * Go to contact us page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToContactUsPage(page) {
    await this.clickAndWaitForNavigation(page, this.contactUsLink);
  }
}

module.exports = new GDPRPersonalData();
