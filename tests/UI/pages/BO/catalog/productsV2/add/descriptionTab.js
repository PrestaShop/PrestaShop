require('module-alias/register');
// Importing page
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Description tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class DescriptionTab extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on description tab
   */
  constructor() {
    super();

    // Selectors in description tab
    this.descriptionTabLink = '#product_description-tab-nav';
    this.productImageDropZoneDiv = '#product-images-dropzone';
    this.openFileManagerDiv = `${this.productImageDropZoneDiv} div.dz-default.openfilemanager.dz-clickable`;
    this.imagePreviewBlock = `${this.productImageDropZoneDiv} div.dz-preview.openfilemanager`;
    this.productSummary = '#product_description_description_short';
    this.productdescription = '#product_description_description';
  }

  /*
  Methods
   */

  /**
   * Get Number of images to set on the product
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfImages(page) {
    return (await page.$$(this.imagePreviewBlock)).length;
  }

  /**
   * Add product images
   * @param page {Page} Browser tab
   * @param imagesPaths {Array<?string>} Paths of the images to add to the product
   * @returns {Promise<void>}
   */
  async addProductImages(page, imagesPaths = []) {
    const filteredImagePaths = imagesPaths.filter((el) => el !== null);

    if (filteredImagePaths !== null && filteredImagePaths.length !== 0) {
      const numberOfImages = await this.getNumberOfImages(page);
      await this.uploadOnFileChooser(
        page,
        numberOfImages === 0 ? this.productImageDropZoneDiv : this.openFileManagerDiv,
        filteredImagePaths,
      );

      await this.waitForVisibleSelector(page, this.imagePreviewBlock);
    }
  }

  /**
   * Set value on tinyMce textarea
   * @param page {Page} Browser tab
   * @param selector {string} Value of selector to use
   * @param value {string} Text to set on tinymce input
   * @returns {Promise<void>}
   */
  async setValueOnTinymceInput(page, selector, value) {
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
  async setProductDescription(page, productData) {
    await this.waitForSelectorAndClick(page, this.descriptionTabLink);

    await this.addProductImages(page, [productData.coverImage, productData.thumbImage]);

    await this.setValueOnTinymceInput(page, this.productSummary, productData.summary);
    await this.setValueOnTinymceInput(page, this.productdescription, productData.description);
  }
}

module.exports = new DescriptionTab();
