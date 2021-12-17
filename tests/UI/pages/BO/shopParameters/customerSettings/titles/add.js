require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add title page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddTitle extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add title page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Titles > Add new •';
    this.pageTitleEdit = 'Titles > Edit:';

    // Form selectors
    this.genderForm = '#gender_form';
    this.nameInput = idLang => `#name_${idLang}`;
    this.genderInput = type => `#type_${type}`;
    this.imageInput = '#image-name';
    this.imageWidthInput = '#img_width';
    this.imageHeightInput = '#img_height';
    this.saveButton = '#gender_form_submit_btn';
    this.alertSuccessBlockParagraph = '.alert-success';

    // Language selectors
    this.dropdownButton = `${this.genderForm} button.dropdown-toggle`;
    this.dropdownMenu = `${this.genderForm} ul.dropdown-menu`;
    this.dropdownMenuItemLink = idLang => `${this.dropdownMenu} li:nth-child(${idLang}) a`;
  }

  /*
  Methods
   */

  /**
   * Change language in form
   * @param page {Page} Browser tab
   * @param idLang {number} Id language to select
   * @return {Promise<void>}
   */
  async changeLanguage(page, idLang) {
    await Promise.all([
      page.click(this.dropdownButton),
      this.waitForVisibleSelector(page, this.dropdownMenuItemLink(idLang)),
    ]);

    await Promise.all([
      page.click(this.dropdownMenuItemLink(idLang)),
      this.waitForHiddenSelector(page, this.dropdownMenuItemLink(idLang)),
    ]);
  }

  /**
   * Fill title form and get successful message
   * @param page {Page} Browser tab
   * @param titleData {TitleData} Data to set on create/edit title form
   * @return {Promise<string>}
   */
  async createEditTitle(page, titleData) {
    await this.changeLanguage(page, 1);
    await this.setValue(page, this.nameInput(1), titleData.name);

    await this.changeLanguage(page, 2);
    await this.setValue(page, this.nameInput(2), titleData.frName);

    await page.click(this.genderInput(titleData.gender.toLowerCase()));

    // Upload image
    await this.uploadFile(page, this.imageInput, titleData.imageName);

    await this.setValue(page, this.imageWidthInput, titleData.imageWidth);
    await this.setValue(page, this.imageHeightInput, titleData.imageHeight);

    // Save title
    await this.clickAndWaitForNavigation(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddTitle();
