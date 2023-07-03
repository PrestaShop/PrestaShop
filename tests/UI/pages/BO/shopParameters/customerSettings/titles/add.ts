// Import pages
import BOBasePage from '@pages/BO/BObasePage';

// Import data
import TitleData from '@data/faker/title';

import type {Page} from 'playwright';

/**
 * Add title page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddTitle extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly genderForm: string;

  private readonly nameInput: (idLang: number) => string;

  private readonly genderInput: (type: string) => string;

  private readonly imageInput: string;

  private readonly imageWidthInput: string;

  private readonly imageHeightInput: string;

  private readonly saveButton: string;

  private readonly dropdownButton: string;

  private readonly dropdownMenu: string;

  private readonly dropdownMenuItemLink: (idLang: number) => string;

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
    this.nameInput = (idLang: number) => `#name_${idLang}`;
    this.genderInput = (type: string) => `#type_${type}`;
    this.imageInput = '#image-name';
    this.imageWidthInput = '#img_width';
    this.imageHeightInput = '#img_height';
    this.saveButton = '#gender_form_submit_btn';
    this.alertSuccessBlockParagraph = '.alert-success';

    // Language selectors
    this.dropdownButton = `${this.genderForm} button.dropdown-toggle`;
    this.dropdownMenu = `${this.genderForm} ul.dropdown-menu`;
    this.dropdownMenuItemLink = (idLang: number) => `${this.dropdownMenu} li:nth-child(${idLang}) a`;
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
  async changeLanguage(page: Page, idLang: number): Promise<void> {
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
  async createEditTitle(page: Page, titleData: TitleData): Promise<string> {
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
    await this.clickAndWaitForURL(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddTitle();
