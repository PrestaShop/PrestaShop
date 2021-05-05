require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddProduct extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Product •';
    // Text Message
    this.settingUpdatedMessage = 'Settings updated.';
    this.duplicateSuccessfulMessage = 'Product successfully duplicated.';
    this.errorMessage = 'Unable to update settings.';
    this.errorMessageWhenSummaryTooLong = number => 'This value is too long.'
      + ` It should have ${number} characters or less.`;


    // Selectors
    this.productNameInput = '#form_step1_name_1';
    this.productImageDropZoneDiv = '#product-images-dropzone';
    this.productTypeSelect = '#form_step1_type_product';
    this.openFileManagerDiv = '.disabled.openfilemanager.dz-clickable';
    this.productWithCombinationsInput = '#show_variations_selector div:nth-of-type(2) input';
    this.productReferenceInput = '#form_step6_reference';
    this.productQuantityInput = '#form_step1_qty_0_shortcut';
    this.productPriceAtiInput = '#form_step1_price_ttc_shortcut';
    this.saveProductButton = 'input#submit[value=\'Save\']';
    this.goToCatalogButton = '#product_form_save_go_to_catalog_btn';
    this.previewProductLink = 'a#product_form_preview_btn';
    this.productOnlineSwitch = '.product-footer div.switch-input';
    this.productOnlineTitle = 'h2.for-switch.online-title';
    this.productShortDescriptionTab = '#tab_description_short a';
    this.productShortDescriptionIframe = '#form_step1_description_short';
    this.productDescriptionTab = '#tab_description a';
    this.productDescriptionIframe = '#form_step1_description';
    this.productTaxRuleSelect = '#step2_id_tax_rules_group_rendered';
    this.productDeleteLink = '.product-footer a.delete';
    this.dangerMessageShortDescription = '#form_step1_description_short .has-danger li';

    this.packItemsInput = '#form_step1_inputPackItems';
    this.packsearchResult = '#js_form_step1_inputPackItems .tt-selectable tr:nth-child(1) td:nth-child(1)';
    this.packQuantityInput = '#form_step1_inputPackItems-curPackItemQty';
    this.addProductToPackButton = '#form_step1_inputPackItems-curPackItemAdd';
    // Form nav
    this.formNavList = '#form-nav';
    this.forNavlistItemLink = id => `${this.formNavList} #tab_step${id} a`;
    // Selectors of Step 2 : Pricing
    this.addSpecificPriceButton = '#js-open-create-specific-price-form';
    this.specificPriceForm = '#specific_price_form';
    this.combinationSelect = '#form_step2_specific_price_sp_id_product_attribute';
    this.startingAtInput = '#form_step2_specific_price_sp_from_quantity';
    this.applyDiscountOfInput = '#form_step2_specific_price_sp_reduction';
    this.reductionType = '#form_step2_specific_price_sp_reduction_type';
    this.applyButton = '#form_step2_specific_price_save';
    // Selector of Step 3 : Combinations
    this.addCombinationsInput = '#form_step3_attributes-tokenfield';
    this.generateCombinationsButton = '#create-combinations';
    this.productCombinationBulkQuantityInput = '#product_combination_bulk_quantity';
    this.productCombinationSelectAllCheckbox = 'input#toggle-all-combinations';
    this.applyOnCombinationsButton = '#apply-on-combinations';
    this.productCombinationTableRow = id => `#accordion_combinations tr:nth-of-type(${id})`;
    this.deleteCombinationsButton = '#delete-combinations';
    this.productCombinationsBulkForm = '#combinations-bulk-form';
    this.productCombinationsBulkFormTitle = `${this.productCombinationsBulkForm} p[aria-controls]`;
    this.bulkCombinationsContainer = '#bulk-combinations-container';
    // Selector of step 3 : Quantities
    this.quantityInput = '#form_step3_qty_0';
    this.minimumQuantityInput = '#form_step3_minimal_quantity';
    this.stockLocationInput = '#form_step3_location';
    this.lowStockLevelInput = '#form_step3_low_stock_threshold';
    this.behaviourOutOfStockInput = id => `#form_step3_out_of_stock_${id}`;
    this.labelWhenInStockInput = '#form_step3_available_now_1';
    this.labelWhenOutOfStock = '#form_step3_available_later_1';
    // Selector of Step 5 : SEO
    this.resetUrlButton = '#seo-url-regenerate';
    this.friendlyUrlInput = '#form_step5_link_rewrite_1';
  }

  /*
  Methods
   */

  /**
   * Set value on tinyMce textarea
   * @param page
   * @param selector
   * @param value
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
   * Set Name, type of product, Reference, price ATI, description and short description
   * @param page
   * @param productData
   * @return {Promise<void>}
   */
  async setBasicSetting(page, productData) {
    await this.setValue(page, this.productNameInput, productData.name);
    if (productData.coverImage !== null) {
      await this.uploadFilePath(page, this.productImageDropZoneDiv, productData.coverImage);
    }
    if (productData.thumbImage !== null) {
      await this.uploadFilePath(page, this.openFileManagerDiv, productData.thumbImage);
    }
    await page.click(this.productDescriptionTab);
    await this.setValueOnTinymceInput(page, this.productDescriptionIframe, productData.description);
    await page.click(this.productShortDescriptionTab);
    await this.setValueOnTinymceInput(page, this.productShortDescriptionIframe, productData.summary);
    await this.selectByVisibleText(page, this.productTypeSelect, productData.type);
    await this.setValue(page, this.productReferenceInput, productData.reference);
    if (await this.elementVisible(page, this.productQuantityInput, 500)) {
      await this.setValue(page, this.productQuantityInput, productData.quantity.toString());
    }
    await this.selectByVisibleText(page, this.productTaxRuleSelect, productData.taxRule);
    await this.setValue(page, this.productPriceAtiInput, productData.price.toString());
  }

  /**
   * Set product online or offline
   * @param page
   * @param wantedStatus
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
   * @param page
   * @returns {Promise<string>}
   */
  async saveProduct(page) {
    await page.click(this.saveProductButton);
    const growlTextMessage = await this.getGrowlMessageContent(page, 30000);
    await this.closeGrowlMessage(page);

    return growlTextMessage;
  }

  /**
   * Create basic product
   * @param page
   * @param productData
   * @returns {Promise<string>}
   */
  async createEditBasicProduct(page, productData) {
    await this.setBasicSetting(page, productData);

    if (productData.type === 'Pack of products') {
      await this.addPackOfProducts(page, productData.pack);
    }

    await this.setProductStatus(page, productData.status);
    return this.saveProduct(page);
  }

  /**
   * Set Combinations for product
   * @param page
   * @param productData
   * @returns {Promise<string>}
   */
  async setCombinationsInProduct(page, productData) {
    await page.click(this.productWithCombinationsInput);
    // GOTO Combination tab : id = 3
    await this.goToFormStep(page, 3);
    // Delete All combinations if exists
    await this.deleteAllCombinations(page);
    // Add combinations
    await this.addCombinations(page, productData.combinations);
    // Set quantity
    await this.setCombinationsQuantity(page, productData.quantity);
    // GOTO Basic settings Tab : id = 1
    await this.goToFormStep(page, 1);
    return this.saveProduct(page);
  }

  /**
   * Generate combinations in input
   * @param page
   * @param combinations
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
   * add one combination
   * @param page
   * @param combination
   * @return {Promise<void>}
   */
  async addCombination(page, combination) {
    await page.type(this.addCombinationsInput, combination);
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('Enter');
  }

  /**
   * Set quantity for all combinations
   * @param page
   * @param quantity
   * @return {Promise<void>}
   */
  async setCombinationsQuantity(page, quantity) {
    // Select all combinations
    await page.check(this.productCombinationSelectAllCheckbox);

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
   * Preview product in new tab
   * @param page
   * @return page opened
   */
  async previewProduct(page) {
    await this.waitForVisibleSelector(page, this.previewProductLink);
    const newPage = await this.openLinkWithTargetBlank(page, this.previewProductLink, 'body a');
    const textBody = await this.getTextContent(newPage, 'body');

    if (await textBody.includes('[Debug] This page has moved')) {
      await this.clickAndWaitForNavigation(newPage, 'a');
    }
    return newPage;
  }

  /**
   * Delete product
   * @param page
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
   * Navigate between forms in add product
   * @param page
   * @param id
   * @return {Promise<void>}
   */
  async goToFormStep(page, id = 1) {
    const selector = this.forNavlistItemLink(id);
    await Promise.all([
      this.waitForVisibleSelector(page, `${selector}[aria-selected='true']`),
      this.waitForSelectorAndClick(page, selector),
    ]);
  }

  /**
   * Return true if combinations table is displayed
   * @param page
   * @return {boolean}
   */
  hasCombinations(page) {
    return this.elementVisible(page, this.productCombinationTableRow(1), 2000);
  }

  /**
   * Delete all combinations
   * @param page
   * @return {Promise<void>}
   */
  async deleteAllCombinations(page) {
    if (await this.hasCombinations(page)) {
      // Select all combinations
      await page.check(this.productCombinationSelectAllCheckbox);

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
   * Reset friendly URL
   * @param page
   * @returns {Promise<void>}
   */
  async resetURL(page) {
    await this.goToFormStep(page, 5);
    await this.waitForVisibleSelector(page, this.resetUrlButton);
    await this.scrollTo(page, this.resetUrlButton);
    await page.click(this.resetUrlButton);
    await this.goToFormStep(page, 1);
  }

  /**
   * Get the error message when short description is too long
   * @param page
   * @returns {Promise<string>}
   */
  async getErrorMessageWhenSummaryIsTooLong(page) {
    return this.getTextContent(page, this.dangerMessageShortDescription);
  }

  /**
   * Get friendly URL
   * @param page
   * @returns {Promise<string>}
   */
  async getFriendlyURL(page) {
    await this.reloadPage(page);
    await this.goToFormStep(page, 5);
    return this.getAttributeContent(page, this.friendlyUrlInput, 'value');
  }

  /**
   * Add specific prices
   * @param page
   * @param specificPriceData
   * @return {Promise<string>}
   */
  async addSpecificPrices(page, specificPriceData) {
    await this.reloadPage(page);
    // Go to pricing tab : id = 2
    await this.goToFormStep(page, 2);
    await Promise.all([
      page.click(this.addSpecificPriceButton),
      this.waitForVisibleSelector(page, `${this.specificPriceForm}.show`),
    ]);
    // Choose combinations if exist
    if (specificPriceData.combinations) {
      await this.waitForVisibleSelector(page, this.combinationSelect);
      await this.scrollTo(page, this.combinationSelect);
      await this.selectByVisibleText(page, this.combinationSelect, specificPriceData.combinations);
    }
    await this.setValue(page, this.startingAtInput, specificPriceData.startingAt.toString());
    await this.setValue(page, this.applyDiscountOfInput, specificPriceData.discount.toString());
    await this.selectByVisibleText(page, this.reductionType, specificPriceData.reductionType);

    // Apply specific price
    await this.scrollTo(page, this.applyButton);
    await page.click(this.applyButton);

    // Get growl message
    const growlMessageText = await this.getGrowlMessageContent(page, 30000);

    await this.closeGrowlMessage(page);
    await this.goToFormStep(page, 1);
    return growlMessageText;
  }

  /**
   * Get online product status
   * @param page
   * @returns {Promise<boolean>}
   */
  getOnlineButtonStatus(page) {
    return this.elementVisible(page, this.productOnlineTitle, 1000);
  }

  /**
   * Is quantity input visible
   * @param page
   * @returns {boolean}
   */
  isQuantityInputVisible(page) {
    return this.elementVisible(page, this.productQuantityInput, 1000);
  }

  /**
   * Go to catalog page
   * @param page
   * @returns {Promise<void>}
   */
  async goToCatalogPage(page) {
    await this.clickAndWaitForNavigation(page, this.goToCatalogButton);
  }

  /**
   * Add product to pack
   * @param page
   * @param product
   * @param quantity
   * @returns {Promise<void>}
   */
  async addProductToPack(page, product, quantity) {
    await page.type(this.packItemsInput, product);
    await this.waitForSelectorAndClick(page, this.packsearchResult);
    await this.setValue(page, this.packQuantityInput, quantity.toString());
    await page.click(this.addProductToPackButton);
  }

  /**
   * Add pack of products
   * @param page
   * @param pack
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
   * @param page
   * @return {Promise<string>}
   */
  getProductName(page) {
    return this.getAttributeContent(page, this.productNameInput, 'value');
  }

  /**
   * Set quantities settings
   * @param page
   * @param product
   * @returns {Promise<void>}
   */
  async setQuantitiesSettings(page, product) {
    let columnSelector;
    // Go to Quantities tab
    await this.goToFormStep(page, 3);
    // Set Quantities form
    await this.setValue(page, this.quantityInput, product.quantity);
    await this.setValue(page, this.minimumQuantityInput, product.minimumQuantity);
    // Set Stock form
    await this.setValue(page, this.stockLocationInput, product.stockLocation);
    await this.setValue(page, this.lowStockLevelInput, product.lowStockLevel);

    // Set Availability preferences form
    switch (product.behaviourOutOfStock) {
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
        throw new Error(`Column ${product.behaviourOutOfStock} was not found`);
    }

    await page.$eval(columnSelector, el => el.click());

    // Set value on label In and out of stock inputs
    await this.scrollTo(page, this.labelWhenInStockInput);
    await this.setValue(page, this.labelWhenInStockInput, product.labelWhenInStock);
    await this.setValue(page, this.labelWhenOutOfStock, product.LabelWhenOutOfStock);
  }

  /**
   * Set product
   * @param page
   * @param productData
   * @returns {Promise<string>}
   */
  async setProduct(page, productData) {
    await this.setBasicSetting(page, productData);
    if (productData.type === 'Pack of products') {
      await this.addPackOfProducts(page, productData.pack);
    }
    if (productData.productHasCombinations) {
      await this.setCombinationsInProduct(page, productData);
    }
    await this.setProductStatus(page, productData.status);
    await this.setQuantitiesSettings(page, productData);
    return this.saveProduct(page);
  }
}

module.exports = new AddProduct();
