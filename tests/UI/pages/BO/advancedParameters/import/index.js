require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Import extends BOBasePage {
  constructor() {
    super();


    this.pageTitle = 'Import • ';
    this.importModalTitle = 'Importing your data...';
    this.importPanelTitle = 'Match your data';

    // Selectors
    this.alertSuccessBlockParagraph = `${this.alertSuccessBlock} p.alert-text.js-import-file`;
    this.downloadSampleFileLink = type => `#download-sample-${type}-file-link`;
    this.fileInputField = '#file';
    this.nextStepButton = 'button[name=submitImportFile]';
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
   * @param page {Page} Browser tab
   * @param type {string} Type of the data to import
   * @return {Promise<string>}
   */
  downloadSampleFile(page, type) {
    return this.clickAndWaitForDownload(page, this.downloadSampleFileLink(type));
  }

  /**
   * Select the type of the file and upload the sample file
   * @param page
   * @param dropdownValue
   * @param filePath
   * @return {Promise<string>}
   */
  async uploadSampleFile(page, dropdownValue, filePath) {
    await this.selectByVisibleText(page, this.fileTypeSelector, dropdownValue);
    await page.setInputFiles(this.fileInputField, filePath);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Go to 'Next step' of import
   * @param page
   * @return {Promise<string>}
   */
  async goToImportNextStep(page) {
    await page.click(this.nextStepButton);

    return this.getTextContent(page, this.importFileSecondStepPanelTitle);
  }

  /**
   * Confirm the upload by clicking on the 'import' button
   * @param page
   * @return {Promise<string>}
   */
  async startFileImport(page) {
    await page.click(this.importButton);

    return this.getTextContent(page, this.importProgressModal);
  }

  /**
   * Close modal at the end of the import
   * @param page
   * @return {Promise<boolean>}
   */
  async closeImportModal(page) {
    await this.waitForVisibleSelector(page, this.importProgressModalCloseButton);
    await this.clickAndWaitForNavigation(page, this.importProgressModalCloseButton);

    return this.elementVisible(page, this.fileTypeSelector, 1000);
  }
}

module.exports = new Import();
