import BOBasePage from '@pages/BO/BObasePage';

import type BrandData from '@data/faker/brand';

import type {Page} from 'playwright';

/**
 * Add brand page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddBrand extends BOBasePage {
  public readonly pageTitle: string;

  public readonly pageTitleEdit: string;

  private readonly nameInput: string;

  private readonly shortDescriptionDiv: string;

  private readonly shortDescriptionLangLink: (lang: string) => string;

  private readonly shortDescriptionIFrame: (id: number) => string;

  private readonly descriptionDiv: string;

  private readonly descriptionIFrame: (id: number) => string;

  private readonly logoFileInput: string;

  private readonly metaTitleInput: (id: number) => string;

  private readonly metaDescriptionInput: (id: number) => string;

  private readonly statusToggleInput: (toggle: number) => string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on add brand page
   */
  constructor() {
    super();

    this.pageTitle = `New brand • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = 'Editing brand';

    // Selectors
    this.nameInput = '#manufacturer_name';
    this.shortDescriptionDiv = '#manufacturer_short_description';
    this.shortDescriptionLangLink = (lang: string) => `${this.shortDescriptionDiv} li.nav-item a[data-locale='${lang}']`;
    this.shortDescriptionIFrame = (id: number) => `${this.shortDescriptionDiv} #manufacturer_short_description_${id}_ifr`;
    this.descriptionDiv = '#manufacturer_description';
    this.descriptionIFrame = (id: number) => `${this.descriptionDiv} #manufacturer_description_${id}_ifr`;
    this.logoFileInput = '#manufacturer_logo';
    this.metaTitleInput = (id: number) => `#manufacturer_meta_title_${id}`;
    this.metaDescriptionInput = (id: number) => `#manufacturer_meta_description_${id}`;
    this.statusToggleInput = (toggle: number) => `#manufacturer_is_enabled_${toggle}`;

    this.saveButton = '.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Create or edit Brand
   * @param page {Page} Browser tab
   * @param brandData {BrandData} Data to set in brand form
   * @returns {Promise<string>}
   */
  async createEditBrand(page: Page, brandData: BrandData): Promise<string> {
    // Fill Name
    await this.setValue(page, this.nameInput, brandData.name);
    // Fill information in english
    await this.changeLanguage(page, 'en');
    await this.setValueOnTinymceInput(page, this.shortDescriptionIFrame(1), brandData.shortDescription);
    await this.setValueOnTinymceInput(page, this.descriptionIFrame(1), brandData.description);
    await this.setValue(page, this.metaTitleInput(1), brandData.metaTitle);
    await this.setValue(page, this.metaDescriptionInput(1), brandData.metaDescription);

    // Fill Information in french
    await this.changeLanguage(page, 'fr');
    await this.setValueOnTinymceInput(page, this.shortDescriptionIFrame(2), brandData.shortDescriptionFr);
    await this.setValueOnTinymceInput(page, this.descriptionIFrame(2), brandData.descriptionFr);
    await this.setValue(page, this.metaTitleInput(2), brandData.metaTitleFr);
    await this.setValue(page, this.metaDescriptionInput(2), brandData.metaDescriptionFr);

    // Add logo
    await this.uploadFile(page, this.logoFileInput, brandData.logo);

    // Set Enabled value
    await this.setChecked(page, this.statusToggleInput(brandData.enabled ? 1 : 0));

    // Save Created brand
    await this.clickAndWaitForURL(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Change language for selector
   * @param page {Page} Browser tab
   * @param lang {string} Language to choose
   * @return {Promise<void>}
   */
  async changeLanguage(page: Page, lang: string): Promise<void> {
    await Promise.all([
      page.$eval(this.shortDescriptionLangLink(lang), (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.shortDescriptionLangLink(lang)}.active`),
    ]);
  }
}

export default new AddBrand();
