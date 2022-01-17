require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add product page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddProduct extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add product page
   */
  constructor() {
    super();

    this.pageTitle = 'Product •';

    // Text Message
    this.settingUpdatedMessage = 'Settings updated.';
    this.duplicateSuccessfulMessage = 'Product successfully duplicated.';
    this.errorMessage = 'Unable to update settings.';
    this.errorMessageWhenSummaryTooLong = number => 'This value is too long.'
      + ` It should have ${number} characters or less.`;

    // Header selectors
    this.productNameInput = '#form_step1_name_1';
    this.productTypeSelect = '#form_step1_type_product';

    // Selectors of form nav list
    this.formNavList = '#form-nav';
    this.forNavListItemLink = id => `${this.formNavList} #tab_step${id} a`;

    // 1 - Selectors of tab: Basic settings
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

    // Selectors for pack of products form
    this.packItemsInput = '#form_step1_inputPackItems';
    this.packsearchResult = '#js_form_step1_inputPackItems .tt-selectable tr:nth-child(1) td:nth-child(1)';
    this.packQuantityInput = '#form_step1_inputPackItems-curPackItemQty';
    this.addProductToPackButton = '#form_step1_inputPackItems-curPackItemAdd';

    // 2 - Selectors of tab: Pricing
    // Retail price
    this.ecoTaxInput = '#form_step2_ecotax';
    // Specific prices
    this.addSpecificPriceButton = '#js-open-create-specific-price-form';
    this.specificPriceForm = '#specific_price_form';
    this.specificPriceCombinationSelect = '#form_step2_specific_price_sp_id_product_attribute';
    this.specificPriceStartingAtInput = '#form_step2_specific_price_sp_from_quantity';
    this.specificPriceApplyDiscountOfInput = '#form_step2_specific_price_sp_reduction';
    this.specificPriceReductionType = '#form_step2_specific_price_sp_reduction_type';
    this.specificPriceApplyButton = '#form_step2_specific_price_save';

    // 3 - Selectors of tab: Combinations
    this.combinationsInput = '#form_step3_attributes-tokenfield';
    this.generateCombinationsButton = '#create-combinations';
    this.productCombinationSelectAllCheckbox = 'input#toggle-all-combinations';
    this.productCombinationTableRow = row => `#accordion_combinations tr:nth-of-type(${row})`;
    // Bulk actions form
    this.productCombinationsBulkForm = '#combinations-bulk-form';
    this.productCombinationsBulkFormTitle = `${this.productCombinationsBulkForm} p[aria-controls]`;
    this.productCombinationBulkQuantityInput = '#product_combination_bulk_quantity';
    this.applyOnCombinationsButton = '#apply-on-combinations';
    this.deleteCombinationsButton = '#delete-combinations';

    // 4 - Selectors of tab: Quantities
    // Quantities
    this.quantityInput = '#form_step3_qty_0';
    this.minimumQuantityInput = '#form_step3_minimal_quantity';
    // Stocks
    this.stockLocationInput = '#form_step3_location';
    this.lowStockLevelInput = '#form_step3_low_stock_threshold';
    // Availability preferences
    this.behaviourOutOfStockInput = id => `#form_step3_out_of_stock_${id}`;
    this.labelWhenInStockInput = '#form_step3_available_now_1';
    this.labelWhenOutOfStock = '#form_step3_available_later_1';

    // 5 - Selectors of tab: SEO
    this.friendlyUrlInput = '#form_step5_link_rewrite_1';
    this.resetUrlButton = '#seo-url-regenerate';

    // Footer selectors
    this.productDeleteLink = '.product-footer a.delete';
    this.productOnlineTitle = 'h2.for-switch.online-title';
    this.productOnlineSwitch = '.product-footer div.switch-input';
    this.previewProductLink = 'a#product_form_preview_btn';
    this.saveProductButton = 'input#submit[value=\'Save\']';
    this.goToCatalogButton = '#product_form_save_go_to_catalog_btn';
    this.addNewProductButton = '#product_form_save_new_btn';
  }

  /*
  Methods
   */
  /**
   * Navigate between forms in add product
   * @param page {Page} Browser tab
   * @param id {number} Value of form id to go
   * @return {Promise<void>}
   */
  async goToFormStep(page, id = 1) {
    const selector = this.forNavListItemLink(id);
    await Promise.all([
      this.waitForVisibleSelector(page, `${selector}[aria-selected='true']`),
      this.waitForSelectorAndClick(page, selector),
    ]);
  }

  /**
   * Set Name, type of product, Reference, price ATI, description and short description
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on basic settings form
   * @return {Promise<void>}
   */
  async setBasicSettings(page, productData) {
    await this.setValue(page, this.productNameInput, productData.name);
    // Select type
    await this.selectByVisibleText(page, this.productTypeSelect, productData.type, true);
    // Set product images
    await this.addProductImages(page, [productData.coverImage, productData.thumbImage]);
    // Set description and summary
    await this.setValueOnTinymceInput(page, this.productDescriptionIframe, productData.description);
    await this.setValueOnTinymceInput(page, this.productShortDescriptionIframe, productData.summary);
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
   * Create basic product
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on new/edit product form
   * @returns {Promise<string>}
   */
  async createEditBasicProduct(page, productData) {
    await this.setBasicSettings(page, productData);

    if (productData.type === 'Pack of products') {
      await this.addPackOfProducts(page, productData.pack);
    }

    await this.setProductStatus(page, productData.status);
    return this.saveProduct(page);
  }

  /**
   * Set product
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on on add/edit product form
   * @returns {Promise<string>}
   */
  async setProduct(page, productData) {
    await this.setBasicSettings(page, productData);
    if (productData.type === 'Pack of products') {
      await this.addPackOfProducts(page, productData.pack);
    }
    if (productData.productHasCombinations) {
      await this.setCombinationsInProduct(page, productData);
    }
    if (!productData.productHasCombinations) {
      await this.setQuantitiesSettings(page, productData);
    }
    if (productData.productWithSpecificPrice) {
      await this.addSpecificPrice(page, productData.specificPrice);
    }
    if (productData.productWithEcoTax) {
      await this.addEcoTax(page, productData.ecoTax);
    }
    await this.setProductStatus(page, productData.status);

    return this.saveProduct(page);
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
   * Set product online or offline
   * @param page {Page} Browser tab
   * @param wantedStatus {boolean} True if we need to enable status, false if not
   * @return {Promise<void>}
   */
  async setProductStatus(page, wantedStatus) {
    const isProductOnline = await this.getOnlineButtonStatus(page);

    if (isProductOnline !== wantedStatus) {
      await page.click(this.productOnlineSwitch);
      await this.closeGrowlMessage(page);
    }
  }

  /**
   * Save product and close the growl message linked to
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async saveProduct(page) {
    await page.click(this.saveProductButton);
    const growlTextMessage = await this.getGrowlMessageContent(page, 30000);
    await this.closeGrowlMessage(page);

    return growlTextMessage;
  }

  /**
   * Return true if combinations table is displayed
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  isCombinationsTableVisible(page) {
    return this.elementVisible(page, this.productCombinationTableRow(1), 2000);
  }


  /**
   * Add one combination
   * @param page {Page} Browser tab
   * @param combination {string} Data to set on combination form
   * @return {Promise<void>}
   */
  async addCombination(page, combination) {
    await page.type(this.combinationsInput, combination);
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('Enter');
  }

  /**
   * Generate combinations in input
   * @param page {Page} Browser tab
   * @param combinations {Object|{color: Array<string>, size: Array<string>}} Data to set on combination form
   * @return {Promise<void>}
   */
  async addCombinations(page, combinations) {
    const keys = Object.keys(combinations);
    /*eslint-disable*/
    for (const key of keys) {
      for (const value of combinations[key]) {
        await this.addCombination(page, `${key} : ${value}`);
      }
    }
    /* eslint-enable */
    await page.$eval(this.generateCombinationsButton, el => el.click());
    await this.closeGrowlMessage(page);
  }

  /**
   * Set quantity for all combinations
   * @param page {Page} Browser tab
   * @param quantity {number} Value of quantity to set on quantity input
   * @return {Promise<void>}
   */
  async setCombinationsQuantity(page, quantity) {
    // Select all combinations
    await this.setChecked(page, this.productCombinationSelectAllCheckbox);

    // Open combinations bulk form
    if (await this.elementNotVisible(page, this.productCombinationBulkQuantityInput, 1000)) {
      await page.click(this.productCombinationsBulkFormTitle);
      await this.waitForVisibleSelector(page, this.productCombinationBulkQuantityInput, 5000);
    }

    // Edit quantity
    await page.type(this.productCombinationBulkQuantityInput, quantity.toString());
    await this.scrollTo(page, this.applyOnCombinationsButton);
    await page.click(this.applyOnCombinationsButton);

    // Close growl message
    await this.closeGrowlMessage(page);
  }

  /**
   * Set Combinations for product
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on combination form
   * @returns {Promise<string>}
   */
  async setCombinationsInProduct(page, productData) {
    await page.click(this.productWithCombinationRadioButton);
    // Go to Combination tab : id = 3
    await this.goToFormStep(page, 3);
    // Delete All combinations if exists
    await this.deleteAllCombinations(page);
    // Add combinations
    await this.addCombinations(page, productData.combinations);
    // Set quantity
    await this.setCombinationsQuantity(page, productData.quantity);
    // Go to Basic settings Tab : id = 1
    await this.goToFormStep(page, 1);

    return this.saveProduct(page);
  }

  /**
   * Preview product in new tab
   * @param page {Page} Browser tab
   * @return page opened
   */
  async previewProduct(page) {
    await this.waitForVisibleSelector(page, this.previewProductLink);
    const newPage = await this.openLinkWithTargetBlank(page, this.previewProductLink, 'body a');
    const textBody = await this.getTextContent(newPage, 'body');

    if (textBody.includes('[Debug] This page has moved')) {
      await this.clickAndWaitForNavigation(newPage, 'a');
    }
    return newPage;
  }

  /**
   * Delete product
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteProduct(page) {
    await Promise.all([
      this.waitForVisibleSelector(page, this.modalDialog),
      page.click(this.productDeleteLink),
    ]);
    await this.clickAndWaitForNavigation(page, this.modalDialogYesButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }


  /**
   * Delete all combinations
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async deleteAllCombinations(page) {
    if (await this.isCombinationsTableVisible(page)) {
      // Select all combinations
      await this.setChecked(page, this.productCombinationSelectAllCheckbox);

      // Open combinations bulk form
      if (await this.elementNotVisible(page, this.productCombinationBulkQuantityInput, 1000)) {
        await page.click(this.productCombinationsBulkFormTitle);
        await this.waitForVisibleSelector(page, this.productCombinationBulkQuantityInput, 5000);
      }

      // Scroll and click on delete combinations button
      await this.scrollTo(page, this.deleteCombinationsButton);

      await Promise.all([
        page.click(this.deleteCombinationsButton),
        this.waitForVisibleSelector(page, this.modalDialog),
      ]);
      await page.waitForTimeout(250);
      await page.click(this.modalDialogYesButton);
      await this.closeGrowlMessage(page);
    }
  }

  /**
   * Get friendly URL
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getFriendlyURL(page) {
    await this.reloadPage(page);
    await this.goToFormStep(page, 5);

    return this.getAttributeContent(page, this.friendlyUrlInput, 'value');
  }

  /**
   * Reset friendly URL
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFriendlyURL(page) {
    await this.goToFormStep(page, 5);
    await this.waitForVisibleSelector(page, this.resetUrlButton);
    await this.scrollTo(page, this.resetUrlButton);
    await page.click(this.resetUrlButton);
    await this.goToFormStep(page, 1);
  }

  /**
   * Get the error message when short description is too long
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getErrorMessageWhenSummaryIsTooLong(page) {
    return this.getTextContent(page, this.dangerMessageShortDescription);
  }

  /**
   * Add specific price
   * @param page {Page} Browser tab
   * @param specificPriceData {Object|{combinations: ?string, discount: ?number, startingAt: ?number,
   * specificPriceReductionType: ?string}} Data to set on specific price form
   * @return {Promise<string>}
   */
  async addSpecificPrice(page, specificPriceData) {
    await this.reloadPage(page);

    // Go to pricing tab : id = 2
    await this.goToFormStep(page, 2);
    await Promise.all([
      page.click(this.addSpecificPriceButton),
      this.waitForVisibleSelector(page, `${this.specificPriceForm}.show`),
    ]);

    // Choose combinations if exist
    if (specificPriceData.combinations) {
      await this.waitForVisibleSelector(page, this.specificPriceCombinationSelect);
      await this.scrollTo(page, this.specificPriceCombinationSelect);
      await this.selectByVisibleText(page, this.specificPriceCombinationSelect, specificPriceData.combinations);
    }
    await this.setValue(page, this.specificPriceStartingAtInput, specificPriceData.startingAt);
    await this.setValue(page, this.specificPriceApplyDiscountOfInput, specificPriceData.discount);
    await this.selectByVisibleText(page, this.specificPriceReductionType, specificPriceData.specificPriceReductionType);

    // Apply specific price
    await this.scrollTo(page, this.specificPriceApplyButton);
    await page.click(this.specificPriceApplyButton);

    // Get growl message
    const growlMessageText = await this.getGrowlMessageContent(page, 30000);

    await this.closeGrowlMessage(page);
    await this.goToFormStep(page, 1);

    return growlMessageText;
  }

  /**
   * Get online product status
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  getOnlineButtonStatus(page) {
    return this.elementVisible(page, this.productOnlineTitle, 1000);
  }

  /**
   * Is quantity input visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isQuantityInputVisible(page) {
    return this.elementVisible(page, this.productQuantityInput, 1000);
  }

  /**
   * Go to catalog page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToCatalogPage(page) {
    await this.clickAndWaitForNavigation(page, this.goToCatalogButton);
  }

  /**
   * Go to add product page
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddProductPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewProductButton);
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
   * Get product name from input
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getProductName(page) {
    return this.getAttributeContent(page, this.productNameInput, 'value');
  }

  /**
   * Set quantities settings
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on quantities setting form
   * @returns {Promise<void>}
   */
  async setQuantitiesSettings(page, productData) {
    let columnSelector;
    // Go to Quantities tab
    await this.goToFormStep(page, 3);
    // Set Quantities form
    await this.setValue(page, this.quantityInput, productData.quantity);
    await this.setValue(page, this.minimumQuantityInput, productData.minimumQuantity);
    // Set Stock form
    await this.setValue(page, this.stockLocationInput, productData.stockLocation);
    await this.setValue(page, this.lowStockLevelInput, productData.lowStockLevel);

    // Set Availability preferences form
    switch (productData.behaviourOutOfStock) {
      case 'Deny orders':
        columnSelector = this.behaviourOutOfStockInput(0);
        break;

      case 'Allow orders':
        columnSelector = this.behaviourOutOfStockInput(1);
        break;

      case 'Default behavior':
        columnSelector = this.behaviourOutOfStockInput(2);
        break;

      default:
        throw new Error(`Column ${productData.behaviourOutOfStock} was not found`);
    }

    await page.$eval(columnSelector, el => el.click());

    // Set value on label In and out of stock inputs
    await this.scrollTo(page, this.labelWhenInStockInput);
    await this.setValue(page, this.labelWhenInStockInput, productData.labelWhenInStock);
    await this.setValue(page, this.labelWhenOutOfStock, productData.LabelWhenOutOfStock);
  }

  /**
   * Set ecoTax value and save
   * @param page
   * @param ecoTax
   * @returns {Promise<string>}
   */
  async addEcoTax(page, ecoTax) {
    // Go to pricing tab : id = 2
    await this.goToFormStep(page, 2);
    await Promise.all([
      page.click(this.addSpecificPriceButton),
      this.waitForVisibleSelector(page, `${this.specificPriceForm}.show`),
    ]);

    await this.setValue(page, this.ecoTaxInput, ecoTax);
    return this.saveProduct(page);
  }
}

module.exports = new AddProduct();
