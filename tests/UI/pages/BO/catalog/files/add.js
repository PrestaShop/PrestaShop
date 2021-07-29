require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddFile extends BOBasePage {
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
   * @param page
   * @param fileData
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
   * @param page
   * @param lang
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
