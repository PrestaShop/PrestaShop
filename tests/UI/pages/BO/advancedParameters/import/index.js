require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Import extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Import • ';

    // Selectors
    this.downloadSampleFileLink = type => `a[href*='import/sample/download/${type}']`;
    this.fileInputField = '#file';
    this.nextStepButton = 'css=button >> text=Next step';
    this.importButton = '#import';
    this.confirmationModalAlert = '#import_details_finished';
    this.importModalCloseButton = '#import_close_button';
    this.fileTypeSelector = '#entity';
    this.importFileSecondStepPanelTitle = '#container-customer > h3';
    this.importProgressModal = '#importProgress';
    this.importProgressModalCloseButton = '#import_close_button';
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

  /**
   *
   * @param page
   * @param dropdownValue
   * @param filePath
   * @return {Promise<string>}
   */
  async uploadSampleFile(page, dropdownValue, filePath) {
    await page.selectOption(this.fileTypeSelector, {value: dropdownValue});
    await page.setInputFiles(this.fileInputField, filePath);

    return this.getTextContent(page, this.alertSuccessBlock);
  }

  async goToImportNextStep(page) {
    await page.click(this.nextStepButton);
    return this.getTextContent(page, this.importFileSecondStepPanelTitle);
  }

  async startFileImport(page) {
    await page.click(this.importButton);
    return this.getTextContent(page, this.importProgressModal);
  }

  async closeImportModal(page) {
    await this.waitForVisibleSelector(page, this.importProgressModalCloseButton);
    await this.clickAndWaitForNavigation(page, this.importProgressModalCloseButton);
    return this.elementVisible(page, this.fileTypeSelector, 1000);
  }
}

module.exports = new Import();
