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

  private readonly productImageContainer: string;

  private readonly productImageDropZoneWindow: string;

  private readonly productImageDropZoneCover: string;

  private readonly productImageDropZoneBtnLang: string;

  private readonly productImageDropZoneDropdown: string;

  private readonly productImageDropZoneDropdownItem: (locale: string) => string;

  private readonly productImageDropZoneCaption: string;

  private readonly productImageDropZoneBtnSubmit: string;

  private readonly productImageDropZoneSelectAllLink: string;

  private readonly productImageDropZoneCloseButton: string;

  private readonly productImageDropZoneZoomIcon: string;

  private readonly productImageDropZoneZoomImage: string;

  private readonly productImageDropZoneCloseZoom: string;

  private readonly productImageDropZoneReplaceImageSelection: string;

  private readonly productImageDropZoneDeleteImageSelection: string;

  private readonly applyDeleteImageButton: string;

  private readonly productSummary: string;

  private readonly productSummaryTabLocale: (locale: string) => string;

  private readonly productSummaryTabContent: (locale: string) => string;

  private readonly productDescription: string;

  private readonly productDescriptionTabLocale: (locale: string) => string;

  private readonly productDescriptionTabContent: (locale: string) => string;

  private readonly productDefaultCategory: string;

  private readonly addCategoryButton: string;

  private readonly addCategoryInput: string;

  private readonly applyCategoryButton: string;

  private readonly categoriesList: string;

  private readonly defaultCategorySelectButton: string;

  private readonly defaultCategoryList: (categoryRow: number) => string;

  private readonly deleteCategoryIcon: (categoryRow: number) => string;

  private readonly productManufacturer: string;

  private readonly productManufacturerSelectButton: string;

  private readonly relatedProductSelectButton: string;

  private readonly productManufacturerList: (brandRow: number) => string;

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
    // Image selectors
    this.productImageDropZoneDiv = '#product-images-dropzone';
    this.imagePreviewBlock = `${this.productImageDropZoneDiv} div.dz-preview.openfilemanager`;
    this.imagePreviewCover = `${this.productImageDropZoneDiv} div.dz-preview.is-cover`;
    this.productImage = `${this.productImageDropZoneDiv} div.dz-preview.dz-image-preview.dz-complete`;
    this.productImageContainer = '#product-images-container';
    this.productImageDropZoneWindow = `${this.productImageContainer} .dropzone-window`;
    this.productImageDropZoneCover = `${this.productImageDropZoneWindow} #is-cover-checkbox`;
    this.productImageDropZoneBtnLang = `${this.productImageDropZoneWindow} #product_dropzone_lang`;
    this.productImageDropZoneDropdown = `${this.productImageDropZoneWindow} .locale-dropdown-menu.show`;
    this.productImageDropZoneDropdownItem = (locale: string) => `${this.productImageDropZoneDropdown} span`
      + `[data-locale="${locale}"]`;
    this.productImageDropZoneCaption = `${this.productImageDropZoneWindow} #caption-textarea`;
    this.productImageDropZoneBtnSubmit = `${this.productImageDropZoneWindow} button.save-image-settings`;
    this.productImageDropZoneSelectAllLink = `${this.productImageDropZoneWindow} p.dropzone-window-select`;
    this.productImageDropZoneCloseButton = `${this.productImageDropZoneWindow} div.dropzone-window-header-right`
      + ' i[data-original-title="Close window"]';
    this.productImageDropZoneZoomIcon = `${this.productImageDropZoneWindow} div.dropzone-window-header-right`
      + ' i[data-original-title="Zoom on selection"]';
    this.productImageDropZoneZoomImage = `${this.productImageContainer} div.pswp--open.pswp--visible`;
    this.productImageDropZoneCloseZoom = `${this.productImageContainer} button.pswp__button--close`;
    this.productImageDropZoneReplaceImageSelection = `${this.productImageContainer} div.dropzone-window-header-right`
      + ' i[data-original-title="Replace selection"]';
    this.productImageDropZoneDeleteImageSelection = `${this.productImageContainer} div.dropzone-window-header-right`
      + ' i[data-original-title="Delete selection"]';
    this.applyDeleteImageButton = `${this.productImageContainer} footer button.btn-primary`;
    // Description & summary selectors
    this.productSummary = '#product_description_description_short';
    this.productSummaryTabLocale = (locale: string) => `${this.productSummary} a[data-locale="${locale}"]`;
    this.productSummaryTabContent = (locale: string) => `${this.productSummary} div.panel[data-locale="${locale}"]`;
    this.productDescription = '#product_description_description';
    this.productDescriptionTabLocale = (locale: string) => `${this.productDescription} a[data-locale="${locale}"]`;
    this.productDescriptionTabContent = (locale: string) => `${this.productDescription} div.panel[data-locale="${locale}"]`;
    // Categories selectors
    this.productDefaultCategory = '#product_description_categories_default_category_id';
    this.addCategoryButton = '#product_description_categories_add_categories_btn';
    this.addCategoryInput = '#ps-select-product-category';
    this.applyCategoryButton = '#category_tree_selector_apply_btn';
    this.categoriesList = '#product_description_categories_product_categories';
    this.defaultCategorySelectButton = '#select2-product_description_categories_default_category_id-container';
    this.defaultCategoryList = (categoryRow: number) => '#select2-product_description_categories_default_category_id-results'
      + ` li:nth-child(${categoryRow})`;
    this.deleteCategoryIcon = (categoryRow: number) => `#product_description_categories_product_categories_${categoryRow}_name`
      + ' + a.pstaggerClosingCross:not(.d-none)';
    // Brand selectors
    this.productManufacturer = '#product_description_manufacturer';
    this.productManufacturerSelectButton = '#select2-product_description_manufacturer-container';
    this.productManufacturerList = (BrandRow: number) => '#select2-product_description_manufacturer-results'
      + ` li:nth-child(${BrandRow})`;
    // Related product selectors
    this.relatedProductSelectButton = '#product_description_related_products_search_input';
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
   * @param selectAll {boolean|undefined} Select all
   * @param toSave {boolean} True if we need to save
   * @returns {Promise<string|null>}
   */
  async setProductImageInformation(
    page: Page,
    numImage: number,
    useAsCoverImage: boolean | undefined,
    captionEn: string | undefined,
    captionFr: string | undefined,
    selectAll: boolean | undefined = undefined,
    toSave: boolean = true,
  ): Promise<string | null> {
    // Select the image
    await page.locator(this.productImage).nth(numImage - 1).click();

    if (selectAll) {
      await page.locator(this.productImageDropZoneSelectAllLink).click();
    }

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

    if (toSave) {
      await page.locator(this.productImageDropZoneBtnSubmit).click();

      return this.getGrowlMessageContent(page);
    }
    await page.locator(this.productImageDropZoneCloseButton).click();
    return null;
  }

  /**
   * Click on magnifying glass
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnMagnifyingGlass(page: Page): Promise<boolean> {
    await page.locator(this.productImageDropZoneZoomIcon).click();

    return this.elementVisible(page, this.productImageDropZoneZoomImage, 1000);
  }

  /**
   * Close image zoom
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeImageZoom(page: Page): Promise<boolean> {
    await page.locator(this.productImageDropZoneCloseZoom).click();

    return this.elementNotVisible(page, this.productImageDropZoneZoomImage, 1000);
  }

  /**
   * Replace image selection
   * @param page {Page} Browser tab
   * @param image {string} Browser tab
   * @returns {Promise<string>}
   */
  async replaceImageSelection(page: Page, image: string): Promise<string | null> {
    await this.uploadOnFileChooser(page, this.productImageDropZoneReplaceImageSelection, [image]);
    await page.locator(this.productImageDropZoneBtnSubmit).click();

    return this.getGrowlMessageContent(page);
  }

  /**
   * delete image
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteImage(page: Page): Promise<string | null> {
    await this.closeGrowlMessage(page);
    await page.locator(this.productImageDropZoneDeleteImageSelection).click();
    await page.locator(this.applyDeleteImageButton).click();

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
    await this.setValueOnTinymceInput(page, this.productSummaryTabContent('en'), productData.summary);

    await page.locator(this.productDescriptionTabLocale('en')).click();
    await this.elementVisible(page, `${this.productDescriptionTabLocale('en')}.active`);
    await this.setValueOnTinymceInput(page, this.productDescriptionTabContent('en'), productData.description);
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

  /**
   * Add new category
   * @param page {Page} Browser tab
   * @param categories {string[]} Browser tab
   * @returns {Promise<void>}
   */
  async addNewCategory(page: Page, categories: string[]): Promise<void> {
    await page.locator(this.addCategoryButton).click();
    await this.waitForVisibleSelector(page, this.addCategoryInput);
    for (let i: number = 0; i < categories.length; i++) {
      await page.locator(this.addCategoryInput).pressSequentially(categories[i]);
      await page.keyboard.press('ArrowDown');
      await page.keyboard.press('Enter');
      await page.waitForTimeout(1000);
    }

    await this.waitForSelectorAndClick(page, this.applyCategoryButton);
  }

  /**
   * Get selected categories
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getSelectedCategories(page: Page): Promise<string> {
    return this.getTextContent(page, this.categoriesList);
  }

  /**
   * Get selected categories
   * @param page {Page} Browser tab
   * @param categoryRow {number} Category row
   * @returns {Promise<void>}
   */
  async chooseDefaultCategory(page: Page, categoryRow: number): Promise<void> {
    await page.locator(this.defaultCategorySelectButton).click();
    await page.locator(this.defaultCategoryList(categoryRow)).click();
  }

  /**
   * Is delete category icon visible
   * @param page {Page} Browser tab
   * @param categoryRow {number} Category row
   * @returns {Promise<number>}
   */
  async isDeleteCategoryIconVisible(page: Page, categoryRow: number): Promise<boolean> {
    const isDeleteIconVisible = await page.evaluate(
      (selector: string) => document.querySelector(selector),
      this.deleteCategoryIcon(categoryRow),
    );

    return isDeleteIconVisible !== null;
  }

  /**
   * Is delete category icon visible
   * @param page {Page} Browser tab
   * @param brandRow {number} Brand row
   * @returns {Promise<void>}
   */
  async chooseBrand(page: Page, brandRow: number): Promise<void> {
    await page.locator(this.productManufacturerSelectButton).click();
    await page.locator(this.productManufacturerList(brandRow)).click();
  }

  /**
   * Add related product
   * @param page {Page} Browser tab
   * @param productName {string} Product name
   * @returns {Promise<void>}
   */
  async addRelatedProduct(page: Page, productName: string): Promise<void> {
    await page.locator(this.relatedProductSelectButton).fill(productName);
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('Enter');
  }
}

export default new DescriptionTab();
