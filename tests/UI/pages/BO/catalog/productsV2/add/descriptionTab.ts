import type {Page} from 'playwright';

import type ProductData from '@data/faker/product';

// Import pages
import BOBasePage from '@pages/BO/BObasePage';

/**
 * Description tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class DescriptionTab extends BOBasePage {
  public readonly settingUpdatedMessage: string;

  private readonly descriptionTabLink: string;

  private readonly productImageDropZoneDiv: string;

  private readonly imagePreviewBlock: string;

  private readonly imagePreviewCover: string;

  private readonly productImage: string;

  private readonly productImageDropZoneWindow: string;

  private readonly productImageDropZoneCover: string;

  private readonly productImageDropZoneBtnLang: string;

  private readonly productImageDropZoneDropdown: string;

  private readonly productImageDropZoneDropdownItem: (locale: string) => string;

  private readonly productImageDropZoneCaption: string;

  private readonly productImageDropZoneBtnSubmit: string;

  private readonly productSummary: string;

  private readonly productSummaryTabLocale: (locale: string) => string;

  private readonly productDescription: string;

  private readonly productDescriptionTabLocale: (locale: string) => string;

  private readonly productDefaultCategory: string;

  private readonly productManufacturer: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on description tab
   */
  constructor() {
    super();

    // Message
    this.settingUpdatedMessage = 'Settings updated';

    // Selectors in description tab
    this.descriptionTabLink = '#product_description-tab-nav';
    this.productImageDropZoneDiv = '#product-images-dropzone';
    this.imagePreviewBlock = `${this.productImageDropZoneDiv} div.dz-preview.openfilemanager`;
    this.imagePreviewCover = `${this.productImageDropZoneDiv} div.dz-preview.is-cover`;
    this.productImage = `${this.productImageDropZoneDiv} div.dz-preview.dz-image-preview.dz-complete`;
    this.productImageDropZoneWindow = '#product-images-container .dropzone-window';
    this.productImageDropZoneCover = `${this.productImageDropZoneWindow} #is-cover-checkbox`;
    this.productImageDropZoneBtnLang = `${this.productImageDropZoneWindow} #product_dropzone_lang`;
    this.productImageDropZoneDropdown = `${this.productImageDropZoneWindow} .locale-dropdown-menu.show`;
    this.productImageDropZoneDropdownItem = (locale: string) => `${this.productImageDropZoneDropdown} span`
      + `[data-locale="${locale}"]`;
    this.productImageDropZoneCaption = `${this.productImageDropZoneWindow} #caption-textarea`;
    this.productImageDropZoneBtnSubmit = `${this.productImageDropZoneWindow} button.save-image-settings`;
    this.productSummary = '#product_description_description_short';
    this.productSummaryTabLocale = (locale: string) => `${this.productSummary} a[data-locale="${locale}"]`;
    this.productDescription = '#product_description_description';
    this.productDescriptionTabLocale = (locale: string) => `${this.productDescription} a[data-locale="${locale}"]`;
    this.productDefaultCategory = '#product_description_categories_default_category_id';
    this.productManufacturer = '#product_description_manufacturer';
  }

  /*
  Methods
   */

  /**
   * Get Number of images to set on the product
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfImages(page: Page): Promise<number> {
    return page.locator(this.productImage).count();
  }

  /**
   * Add product images
   * @param page {Page} Browser tab
   * @param imagesPaths {Array<?string>} Paths of the images to add to the product
   * @returns {Promise<void>}
   */
  async addProductImages(page: Page, imagesPaths: any[] = []): Promise<void> {
    const filteredImagePaths = imagesPaths.filter((el) => el !== null);

    if (filteredImagePaths !== null && filteredImagePaths.length !== 0) {
      const numberOfImages = await this.getNumberOfImages(page);
      await this.uploadOnFileChooser(
        page,
        numberOfImages === 0 ? this.productImageDropZoneDiv : this.imagePreviewBlock,
        filteredImagePaths,
      );

      await this.waitForVisibleSelector(page, this.imagePreviewBlock);
      await this.waitForVisibleLocator(page.locator(this.productImage).nth(numberOfImages + filteredImagePaths.length - 1));
    }
  }

  /**
   * Set Product Image Information
   * @param page {Page} Browser tab
   * @param numImage {number} Number of the image
   * @param useAsCoverImage {boolean|undefined} Use as cover image
   * @param captionEn {string|undefined} Caption in English
   * @param captionFr {string|undefined} Caption in French
   * @returns {Promise<string|null>}
   */
  async setProductImageInformation(
    page: Page,
    numImage: number,
    useAsCoverImage: boolean|undefined,
    captionEn: string|undefined,
    captionFr: string|undefined,
  ): Promise<string|null> {
    // Select the image
    await page.locator(this.productImage).nth(numImage - 1).click();

    if (useAsCoverImage) {
      await this.setCheckedWithIcon(page, this.productImageDropZoneCover, useAsCoverImage);
    }
    if (captionEn) {
      await page.locator(this.productImageDropZoneBtnLang).click();
      await this.elementVisible(page, this.productImageDropZoneDropdown);

      await page.locator(this.productImageDropZoneDropdownItem('en')).click();
      await this.setValue(page, this.productImageDropZoneCaption, captionEn);
    }
    if (captionFr) {
      await page.locator(this.productImageDropZoneBtnLang).click();
      await this.elementVisible(page, this.productImageDropZoneDropdown);

      await page.locator(this.productImageDropZoneDropdownItem('fr')).click();
      await this.setValue(page, this.productImageDropZoneCaption, captionFr);
    }

    await page.locator(this.productImageDropZoneBtnSubmit).click();

    return this.getGrowlMessageContent(page);
  }

  /**
   * Set value on tinyMce textarea
   * @param page {Page} Browser tab
   * @param selector {string} Value of selector to use
   * @param value {string} Text to set on tinymce input
   * @returns {Promise<void>}
   */
  async setValueOnTinymceInput(page: Page, selector: string, value: string): Promise<void> {
    // Select all
    await page.click(`${selector} .mce-edit-area`, {clickCount: 3});

    // Delete all text
    await page.keyboard.press('Backspace');

    // Fill the text
    await page.keyboard.type(value);
  }

  /**
   * Set product description
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in description form
   * @returns {Promise<void>}
   */
  async setProductDescription(page: Page, productData: ProductData): Promise<void> {
    await this.waitForSelectorAndClick(page, this.descriptionTabLink);

    await this.addProductImages(page, [productData.coverImage, productData.thumbImage]);

    await page.locator(this.productSummaryTabLocale('en')).click();
    await this.elementVisible(page, `${this.productSummaryTabLocale('en')}.active`);
    await this.setValueOnTinymceInput(page, this.productSummary, productData.summary);

    await page.locator(this.productDescriptionTabLocale('en')).click();
    await this.elementVisible(page, `${this.productDescriptionTabLocale('en')}.active`);
    await this.setValueOnTinymceInput(page, this.productDescription, productData.description);
  }

  /**
   * Get Product ID Image Cover
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getProductIDImageCover(page: Page): Promise<number> {
    return parseInt(await page.getAttribute(this.imagePreviewCover, 'data-id') ?? '', 10);
  }

  /**
   * Returns the value of a form element
   * @param page {Page}
   * @param inputName {string}
   * @param languageId {string | undefined}
   */
  async getValue(page: Page, inputName: string, languageId?: string): Promise<string> {
    switch (inputName) {
      case 'description':
        return this.getTextContent(page, `${this.productDescription}_${languageId}`, false);
      case 'id_category_default':
        return page.locator(this.productDefaultCategory).evaluate((node: HTMLSelectElement) => node.value);
      case 'manufacturer':
        return page.locator(this.productManufacturer).evaluate((node: HTMLSelectElement) => node.value);
      case 'summary':
        return this.getTextContent(page, `${this.productSummary}_${languageId}`, false);
      default:
        throw new Error(`Input ${inputName} was not found`);
    }
  }
}

export default new DescriptionTab();
