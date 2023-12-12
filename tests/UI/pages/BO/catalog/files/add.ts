import {Page} from 'playwright';

import type FileData from '@data/faker/file';

import BOBasePage from '@pages/BO/BObasePage';

/**
 * Add file page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddFile extends BOBasePage {
  public readonly pageTitle: string;

  public readonly pageTitleEdit: string;

  private readonly nameLangButton: string;

  private readonly nameLangSpan: (lang: string) => string;

  private readonly nameInput: (id: number) => string;

  private readonly descriptionInput: (id: number) => string;

  private readonly fileInput: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add file page
   */
  constructor() {
    super();

    this.pageTitle = `New file • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = 'Editing file';

    // Selectors
    this.nameLangButton = '#attachment_name_dropdown';
    this.nameLangSpan = (lang: string) => 'div.dropdown-menu[aria-labelledby=\'attachment_name_dropdown\']'
      + ` span[data-locale='${lang}']`;
    this.nameInput = (id: number) => `#attachment_name_${id}`;
    this.descriptionInput = (id: number) => `#attachment_file_description_${id}`;
    this.fileInput = '#attachment_file';
    this.saveButton = '.card-footer button';
  }

  /* Methods */
  /**
   * Create or edit file
   * @param page {Page} Browser tab
   * @param fileData {FileData} Data to set on add/edit file form
   * @param save {boolean} True if we need to save the form
   * @returns {Promise<string>}
   */
  async createEditFile(page: Page, fileData: FileData, save: boolean = true): Promise<string | null> {
    // Fill name and description in english
    await this.changeLanguageForSelectors(page, 'en');
    await this.setValue(page, this.nameInput(1), fileData.name);
    await this.setValue(page, this.descriptionInput(1), fileData.description);

    // Fill name and description in French
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.nameInput(2), fileData.frName);
    await this.setValue(page, this.descriptionInput(2), fileData.frDescription);

    // Upload file
    await this.uploadFile(page, this.fileInput, fileData.filename);

    if (save) {
      // Save
      await this.clickAndWaitForURL(page, this.saveButton);

      return this.getAlertSuccessBlockParagraphContent(page);
    }
    return null;
  }

  /**
   * Get text danger
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getTextDanger(page: Page): Promise<string> {
    await page.locator(this.saveButton).click();

    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * Change language for selectors
   * @param page {Page} Browser tab
   * @param lang {string} Value oof language to change
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(page: Page, lang: string = 'en'): Promise<void> {
    await Promise.all([
      page.locator(this.nameLangButton).click(),
      this.waitForVisibleSelector(page, `${this.nameLangButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.locator(this.nameLangSpan(lang)).click(),
      this.waitForVisibleSelector(page, `${this.nameLangButton}[aria-expanded='false']`),
    ]);
  }
}

export default new AddFile();
