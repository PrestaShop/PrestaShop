require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Import page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Import extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on import page
   */
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
    this.progressValidateBarInfo = '#validate_progressbar_done';
    this.progressImportBarInfo = '#import_progressbar_done';
    this.importDetailsFinished = '#import_details_finished';
    this.importProgressModalCloseButton = '#import_close_button';
    this.forceAllIDNumber = toggle => `#forceIDs_${toggle}`;
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
   * @param page {Page} Browser tab
   * @param fileType {string} Value of file type to select
   * @param filePath {string} Value of file path to set on file input
   * @return {Promise<string>}
   */
  async uploadFile(page, fileType, filePath) {
    await this.selectByVisibleText(page, this.fileTypeSelector, fileType);
    await page.setInputFiles(this.fileInputField, filePath);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Is force all id numbers visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isForceAllIDNumbersVisible(page) {
    return this.elementVisible(page, this.forceAllIDNumber(1), 2000);
  }

  /**
   * Enable/Disable force all ID numbers
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable all id numbers
   * @returns {Promise<void>}
   */
  async setForceAllIDNumbers(page, toEnable = true) {
    await this.setChecked(page, this.forceAllIDNumber(toEnable ? 1 : 0));
  }

  /**
   * Go to 'Next step' of import
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async goToImportNextStep(page) {
    await page.click(this.nextStepButton);

    return this.getTextContent(page, this.importFileSecondStepPanelTitle);
  }

  /**
   * Confirm the upload by clicking on the 'import' button
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async startFileImport(page) {
    await page.click(this.importButton);

    return this.getTextContent(page, this.importProgressModal);
  }

  /**
   * Get import validation message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getImportValidationMessage(page) {
    await this.waitForVisibleSelector(page, `${this.progressValidateBarInfo}[style="width: 100%;"]`);
    await this.waitForVisibleSelector(page, `${this.progressImportBarInfo}[style="width: 100%;"]`);

    return this.getTextContent(page, this.importDetailsFinished);
  }

  /**
   * Close modal at the end of the import
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async closeImportModal(page) {
    await this.waitForVisibleSelector(page, this.importProgressModalCloseButton);
    await this.clickAndWaitForNavigation(page, this.importProgressModalCloseButton);

    return this.elementNotVisible(page, this.importProgressModalCloseButton, 1000);
  }
}

module.exports = new Import();
