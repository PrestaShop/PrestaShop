import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Import page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Import extends BOBasePage {
  public readonly pageTitle: string;

  public readonly importModalTitle: string;

  public readonly importPanelTitle: string;

  private readonly downloadSampleFileLink: (type: string) => string;

  private readonly fileInputField: string;

  private readonly nextStepButton: string;

  private readonly importButton: string;

  private readonly confirmationModalAlert: string;

  private readonly importModalCloseButton: string;

  private readonly fileTypeSelector: string;

  private readonly importFileSecondStepPanelTitle: string;

  private readonly importProgressModal: string;

  private readonly progressValidateBarInfo: string;

  private readonly progressImportBarInfo: string;

  private readonly importDetailsFinished: string;

  private readonly importProgressModalCloseButton: string;

  private readonly forceAllIDNumber: (toggle: number) => string;

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
    this.downloadSampleFileLink = (type: string) => `#download-sample-${type}-file-link`;
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
    this.forceAllIDNumber = (toggle: number) => `#forceIDs_${toggle}`;
  }

  /*
  Methods
   */
  /**
   * Click on simple file link to download it
   * @param page {Page} Browser tab
   * @param type {string} Type of the data to import
   * @return {Promise<string|null>}
   */
  downloadSampleFile(page: Page, type: string): Promise<string | null> {
    return this.clickAndWaitForDownload(page, this.downloadSampleFileLink(type));
  }

  async selectFileType(page: Page, fileType: string): Promise<void> {
    await this.selectByVisibleText(page, this.fileTypeSelector, fileType);
  }

  /**
   * Select the type of the file and upload the sample file
   * @param page {Page} Browser tab
   * @param fileType {string} Value of file type to select
   * @param filePath {string} Value of file path to set on file input
   * @return {Promise<string>}
   */
  async uploadImportFile(page: Page, fileType: string, filePath: string): Promise<string> {
    await this.selectByVisibleText(page, this.fileTypeSelector, fileType);
    await page.setInputFiles(this.fileInputField, filePath);

    await page.waitForTimeout(2000);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  async clickOnDownloadedFile(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, '#main-div div.alert.alert-success.js-import-file-alert button.js-change-import-file-btn');
  }

  async isChooseFromHistoryButtonVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, '#main-div button.js-from-files-history-btn', 2000);
  }

  async chooseFromHistoryFTP(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, '#main-div button.js-from-files-history-btn');

    return this.elementVisible(page, '#fileHistoryTable');
  }

  async getImportedFilesList(page: Page): Promise<string> {
    return this.getTextContent(page, '#fileHistoryTable tbody');
  }

  async deleteFile(page: Page, row: number = 1): Promise<boolean> {
    await this.waitForSelectorAndClick(page, `#fileHistoryTable tbody tr:nth-child(${row + 1}) td:nth-child(2) button.dropdown-toggle.dropdown-toggle-split`);
    await this.waitForSelectorAndClick(page, `#fileHistoryTable tbody tr:nth-child(${row + 1}) td:nth-child(2) a:nth-child(3)`);

    return this.isChooseFromHistoryButtonVisible(page);
  }

  async useFile(page: Page, row: number = 1): Promise<string> {
    await this.waitForSelectorAndClick(page, `#fileHistoryTable tbody tr:nth-child(${row + 1}) td:nth-child(2) button.js-use-file-btn`);
    await page.waitForTimeout(2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Is force all id numbers visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isForceAllIDNumbersVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.forceAllIDNumber(1), 2000);
  }

  /**
   * Enable/Disable force all ID numbers
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable all id numbers
   * @returns {Promise<void>}
   */
  async setForceAllIDNumbers(page: Page, toEnable: boolean = true): Promise<void> {
    await this.setChecked(page, this.forceAllIDNumber(toEnable ? 1 : 0));
  }

  /**
   * Go to 'Next step' of import
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async goToImportNextStep(page: Page): Promise<string> {
    await page.click(this.nextStepButton);

    return this.getTextContent(page, this.importFileSecondStepPanelTitle);
  }

  /**
   * Confirm the upload by clicking on the 'import' button
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async startFileImport(page: Page): Promise<string> {
    await page.click(this.importButton);

    return this.getTextContent(page, this.importProgressModal);
  }

  /**
   * Get import validation message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getImportValidationMessage(page: Page): Promise<string> {
    await this.waitForVisibleSelector(page, `${this.progressValidateBarInfo}[style="width: 100%;"]`);
    await this.waitForVisibleSelector(page, `${this.progressImportBarInfo}[style="width: 100%;"]`);

    return this.getTextContent(page, this.importDetailsFinished);
  }

  /**
   * Close modal at the end of the import
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async closeImportModal(page: Page): Promise<boolean> {
    await this.waitForVisibleSelector(page, this.importProgressModalCloseButton);
    await this.clickAndWaitForURL(page, this.importProgressModalCloseButton);

    return this.elementNotVisible(page, this.importProgressModalCloseButton, 1000);
  }
}

export default new Import();
