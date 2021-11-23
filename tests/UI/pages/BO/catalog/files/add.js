require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add file page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddFile extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add file page
   */
  constructor() {
    super();

    this.pageTitle = 'Add new file •';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.nameLangButton = '#attachment_name';
    this.nameLangSpan = lang => `div.dropdown-menu[aria-labelledby='attachment_name'] span[data-locale='${lang}']`;
    this.nameInput = id => `#attachment_name_${id}`;
    this.descriptionInput = id => `#attachment_file_description_${id}`;
    this.fileInput = '#attachment_file';
    this.saveButton = '.card-footer button';
  }

  /* Methods */
  /**
   * Create or edit file
   * @param page {Page} Browser tab
   * @param fileData {FileData} Data to set on add/edit file form
   * @returns {Promise<string>}
   */
  async createEditFile(page, fileData) {
    // Fill name and description in english
    await this.changeLanguageForSelectors(page, 'en');
    await this.setValue(page, this.nameInput(1), fileData.name);
    await this.setValue(page, this.descriptionInput(1), fileData.description);

    // Fill name and description in french
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.nameInput(2), fileData.frName);
    await this.setValue(page, this.descriptionInput(2), fileData.frDescription);

    // Upload file
    const fileInputElement = await page.$(this.fileInput);
    await fileInputElement.setInputFiles(fileData.filename);

    // Save Supplier
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Change language for selectors
   * @param page {Page} Browser tab
   * @param lang {string} Value oof language to change
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(page, lang = 'en') {
    await Promise.all([
      page.click(this.nameLangButton),
      this.waitForVisibleSelector(page, `${this.nameLangButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.click(this.nameLangSpan(lang)),
      this.waitForVisibleSelector(page, `${this.nameLangButton}[aria-expanded='false']`),
    ]);
  }
}

module.exports = new AddFile();
