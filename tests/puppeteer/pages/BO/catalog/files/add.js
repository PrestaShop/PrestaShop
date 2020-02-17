require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddFile extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Add new file •';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.nameLangButton = '#attachment_name';
    this.nameLangSpan = 'div.dropdown-menu[aria-labelledby=\'attachment_name\'] span[data-locale=\'%LANG\']';
    this.nameInput = '#attachment_name_%ID';
    this.descriptionInput = '#attachment_file_description_%ID';
    this.fileInput = '#attachment_file';
    this.saveButton = '.card-footer button';
  }

  /* Methods */
  /**
   *  Create or edit file
   * @param fileData
   * @return {Promise<void>}
   */
  async createEditFile(fileData) {
    // Fill name and description in english
    await this.changeLanguageForSelectors('en');
    await this.setValue(this.nameInput.replace('%ID', 1), fileData.name);
    await this.setValue(this.descriptionInput.replace('%ID', 1), fileData.description);
    // Fill name and description in french
    await this.changeLanguageForSelectors('fr');
    await this.setValue(this.nameInput.replace('%ID', 2), fileData.frName);
    await this.setValue(this.descriptionInput.replace('%ID', 2), fileData.frDescription);
    // Upload file
    const fileInputElement = await this.page.$(this.fileInput);
    await fileInputElement.uploadFile(fileData.filename);
    // Save Supplier
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Change language for selectors
   * @param lang
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(lang = 'en') {
    await Promise.all([
      this.page.click(this.nameLangButton),
      this.page.waitForSelector(`${this.nameLangButton}[aria-expanded='true']`, {visible: true}),
    ]);
    await Promise.all([
      this.page.click(this.nameLangSpan.replace('%LANG', lang)),
      this.page.waitForSelector(`${this.nameLangButton}[aria-expanded='false']`, {visible: true}),
    ]);
  }
};
