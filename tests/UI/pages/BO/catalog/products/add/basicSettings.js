require('module-alias/register');
const AddProductBasePage = require('@pages/BO/catalog/products/add/addProductBasePage');

/**
 * Basic settings form, contains functions that can be used on the form
 * @class
 * @extends AddProductBasePage
 */
class BasicSettings extends AddProductBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add product page
   */
  constructor() {
    super();

    // Text Message
    this.errorMessageWhenSummaryTooLong = number => 'This value is too long.'
      + ` It should have ${number} characters or less.`;

    // Selectors of tab: Basic settings
    this.productImageDropZoneDiv = '#product-images-dropzone';
    this.openFileManagerDiv = `${this.productImageDropZoneDiv} .disabled.openfilemanager.dz-clickable`;
    this.imagePreviewBlock = `${this.productImageDropZoneDiv} > div.dz-complete.dz-image-preview`;

    this.productShortDescriptionIframe = '#form_step1_description_short';
    this.dangerMessageShortDescription = '#form_step1_description_short .has-danger li';
    this.productDescriptionIframe = '#form_step1_description';

    this.productWithCombinationRadioButton = '#show_variations_selector div:nth-of-type(2) input';
    this.productReferenceInput = '#form_step6_reference';
    this.productQuantityInput = '#form_step1_qty_0_shortcut';
    this.productPriceTTCInput = '#form_step1_price_ttc_shortcut';
    this.productTaxRuleSelect = '#step2_id_tax_rules_group_rendered';

    // Selectors of pack of products form
    this.packItemsInput = '#form_step1_inputPackItems';
    this.packsearchResult = '#js_form_step1_inputPackItems .tt-selectable tr:nth-child(1) td:nth-child(1)';
    this.packQuantityInput = '#form_step1_inputPackItems-curPackItemQty';
    this.addProductToPackButton = '#form_step1_inputPackItems-curPackItemAdd';
  }

  /*
  Methods
   */
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
   * Add product to pack
   * @param page {Page} Browser tab
   * @param product {string} Value of product name to set on input
   * @param quantity {number} Value of quantity to set on input
   * @returns {Promise<void>}
   */
  async addProductToPack(page, product, quantity) {
    await page.type(this.packItemsInput, product);
    await this.waitForSelectorAndClick(page, this.packsearchResult);
    await this.setValue(page, this.packQuantityInput, quantity);
    await page.click(this.addProductToPackButton);
  }

  /**
   * Add pack of products
   * @param page {Page} Browser tab
   * @param pack {Object} Data to set on pack form
   * @returns {Promise<void>}
   */
  async addPackOfProducts(page, pack) {
    const keys = Object.keys(pack);

    for (let i = 0; i < keys.length; i += 1) {
      await this.addProductToPack(page, keys[i], pack[keys[i]]);
    }
  }

  /**
   * Get the error message when short description is too long
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getErrorMessageWhenSummaryIsTooLong(page) {
    await this.goToFormStep(page, 1);
    return this.getTextContent(page, this.dangerMessageShortDescription);
  }

  /**
   * Add product images
   * @param page {Page} Browser tab
   * @param imagesPaths {Array<?string>} Paths of the images to add to the product
   * @returns {Promise<void>}
   */
  async addProductImages(page, imagesPaths = []) {
    const filteredImagePaths = imagesPaths.filter(el => el !== null);

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
   * Set Name, type of product, Reference, price ATI, description and short description
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on basic settings form
   * @return {Promise<void>}
   */
  async setBasicSettings(page, productData) {
    // Go to Basic settings tab
    await this.goToFormStep(page, 1);
    // Set product images
    await this.addProductImages(page, [productData.coverImage, productData.thumbImage]);
    // Set description and summary
    await this.setValueOnTinymceInput(page, this.productDescriptionIframe, productData.description);
    await this.setValueOnTinymceInput(page, this.productShortDescriptionIframe, productData.summary);
    // Select 'Product with combination' radio button
    if (productData.productHasCombinations) {
      await page.click(this.productWithCombinationRadioButton);
    }
    // Set reference and quantity
    await this.setValue(page, this.productReferenceInput, productData.reference);
    if (await this.elementVisible(page, this.productQuantityInput, 500)) {
      await this.setValue(page, this.productQuantityInput, productData.quantity);
    }
    // Select tax rule
    await this.selectByVisibleText(page, this.productTaxRuleSelect, productData.taxRule);
    // Set price
    await this.setValue(page, this.productPriceTTCInput, productData.price);
  }

  /**
   * Get Number of images to set on the product
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfImages(page) {
    return (await page.$$(this.imagePreviewBlock)).length;
  }

  /**
   * Is quantity input visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isQuantityInputVisible(page) {
    return this.elementVisible(page, this.productQuantityInput, 1000);
  }
}

module.exports = new BasicSettings();
