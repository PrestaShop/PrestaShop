require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddProduct extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Product •';
    // Text Message
    this.settingUpdatedMessage = 'Settings updated.';
    this.errorMessage = 'Unable to update settings.';
    this.errorMessageWhenSummaryTooLong = 'This value is too long. It should have %NUMBER characters or less.';
    // Selectors
    this.productNameInput = '#form_step1_name_1';
    this.productTypeSelect = '#form_step1_type_product';
    this.productWithCombinationsInput = '#show_variations_selector div:nth-of-type(2) input';
    this.productReferenceInput = '#form_step6_reference';
    this.productQuantityInput = '#form_step1_qty_0_shortcut';
    this.productPriceTtcInput = '#form_step1_price_ttc_shortcut';
    this.productPriceHtInput = '#form_step1_price_shortcut';
    this.saveProductButton = 'input#submit[value=\'Save\']';
    this.previewProductLink = 'a#product_form_preview_btn';
    this.productOnlineSwitch = '.product-footer div.switch-input';
    this.productOnlineTitle = 'h2.for-switch.online-title';
    this.productShortDescriptionTab = '#tab_description_short a';
    this.productShortDescriptionIframe = '#form_step1_description_short_1_ifr';
    this.productDescriptionTab = '#tab_description a';
    this.productDescriptionIframe = '#form_step1_description_1_ifr';
    this.productTaxRuleSelect = '#step2_id_tax_rules_group_rendered';
    this.productDeleteLink = '.product-footer a.delete';
    this.dangerMessageShortDescription = '#form_step1_description_short .has-danger li';

    // Form nav
    this.formNavList = '#form-nav';
    this.forNavlistItemLink = `${this.formNavList} #tab_step%ID a`;
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
    this.productCombinationTableRow = '#accordion_combinations tr:nth-of-type(%ID)';
    this.deleteCombinationsButton = '#delete-combinations';
    this.productCombinationsBulkForm = '#combinations-bulk-form';
    this.productCombinationsBulkFormTitle = `${this.productCombinationsBulkForm} p[aria-controls]`;
    this.bulkCombinationsContainer = '#bulk-combinations-container';
    // Selector of Step 5 : SEO
    this.resetUrlButton = '#seo-url-regenerate';
    this.friendlyUrlInput = '#form_step5_link_rewrite_1';
  }

  /*
  Methods
   */
  /**
   * Set Name, type of product, Reference, price ttc, description and short description
   * @param productData
   * @return {Promise<void>}
   */
  async setBasicSetting(productData) {
    await this.setValue(this.productNameInput, productData.name);
    await this.page.click(this.productDescriptionTab);
    await this.setValueOnTinymceInput(this.productDescriptionIframe, productData.description);
    await this.page.click(this.productShortDescriptionTab);
    await this.setValueOnTinymceInput(this.productShortDescriptionIframe, productData.summary);
    await this.selectByVisibleText(this.productTypeSelect, productData.type);
    await this.setValue(this.productReferenceInput, productData.reference);
    if (await this.elementVisible(this.productQuantityInput, 500)) {
      await this.setValue(this.productQuantityInput, productData.quantity.toString());
    }
    await this.selectByVisibleText(this.productTaxRuleSelect, productData.taxRule);
    await this.setValue(this.productPriceTtcInput, productData.price.toString());
  }

  /**
   * Set product online or offline
   * @param wantedStatus
   * @return {Promise<void>}
   */
  async setProductStatus(wantedStatus) {
    const isProductOnline = await this.getOnlineButtonStatus();
    if (isProductOnline !== wantedStatus) {
      await this.page.click(this.productOnlineSwitch);
      await this.closeGrowlMessage();
    }
  }

  /**
   * Save product and close the growl message linked to
   * @return {Promise<string>}
   */
  async saveProduct() {
    await this.page.click(this.saveProductButton);
    return this.closeGrowlMessage();
  }

  /**
   * Create basic product
   * @param productData
   * @return {Promise<string>}
   */
  async createEditBasicProduct(productData) {
    await this.setBasicSetting(productData);
    await this.setProductStatus(productData.status);
    return this.saveProduct();
  }

  /**
   * Set Combinations for product
   * @param productData
   * @return {Promise<string>}
   */
  async setCombinationsInProduct(productData) {
    await this.page.click(this.productWithCombinationsInput);
    // GOTO Combination tab : id = 3
    await this.goToFormStep(3);
    // Delete All combinations if exists
    await this.deleteAllCombinations();
    // Add combinations
    await this.addCombinations(productData.combinations);
    // Set quantity
    await this.setCombinationsQuantity(productData.quantity);
    // GOTO Basic settings Tab : id = 1
    await this.goToFormStep(1);
    return this.saveProduct();
  }

  /**
   * Generate combinations in input
   * @param combinations
   * @return {Promise<void>}
   */
  async addCombinations(combinations) {
    const keys = Object.keys(combinations);
    /*eslint-disable*/
    for (const key of keys) {
      for (const value of combinations[key]) {
        await this.addCombination(`${key} : ${value}`);
      }
    }
    /* eslint-enable */
    await this.scrollTo(this.generateCombinationsButton);
    await Promise.all([
      this.waitForVisibleSelector(`${this.productCombinationsBulkForm}:not(.inactive)`),
      this.waitForVisibleSelector(
        `${this.productCombinationTableRow.replace('%ID', 1)}[style='display: table-row;']`,
      ),
      this.page.click(this.generateCombinationsButton),
    ]);
    await this.closeGrowlMessage();
    await this.closeCombinationsForm();
  }

  /**
   * add one combination
   * @param combination
   * @return {Promise<void>}
   */
  async addCombination(combination) {
    await this.page.type(this.addCombinationsInput, combination);
    await this.page.keyboard.press('ArrowDown');
    await this.page.keyboard.press('Enter');
  }

  /**
   * Set quantity for all combinations
   * @param quantity
   * @return {Promise<void>}
   */
  async setCombinationsQuantity(quantity) {
    // Unselect all
    await this.changeCheckboxValue(this.productCombinationSelectAllCheckbox, false);
    await Promise.all([
      this.waitForVisibleSelector(`${this.productCombinationsBulkFormTitle}[aria-expanded='true']`),
      await this.changeCheckboxValue(this.productCombinationSelectAllCheckbox, true),
    ]);
    // Edit quantity
    await this.waitForVisibleSelector(this.applyOnCombinationsButton);
    await this.scrollTo(this.productCombinationBulkQuantityInput);
    await this.page.type(this.productCombinationBulkQuantityInput, quantity.toString());
    await this.scrollTo(this.applyOnCombinationsButton);
    await this.page.click(this.applyOnCombinationsButton);
  }

  /**
   * Preview product in new tab
   * @return page opened
   */
  async previewProduct() {
    await this.waitForVisibleSelector(this.previewProductLink);
    this.page = await this.openLinkWithTargetBlank(this.page, this.previewProductLink);
    const textBody = await this.getTextContent('body');
    if (await textBody.includes('[Debug] This page has moved')) {
      await this.clickAndWaitForNavigation('a');
    }
    return this.page;
  }

  /**
   * Delete product
   * @return {Promise<textContent>}
   */
  async deleteProduct() {
    await Promise.all([
      this.waitForVisibleSelector(this.modalDialog),
      this.page.click(this.productDeleteLink),
    ]);
    await this.clickAndWaitForNavigation(this.modalDialogYesButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Navigate between forms in add product
   * @param id
   * @return {Promise<void>}
   */
  async goToFormStep(id = 1) {
    const selector = this.forNavlistItemLink.replace('%ID', id);
    await Promise.all([
      this.waitForVisibleSelector(`${selector}[aria-selected='true']`),
      this.waitForSelectorAndClick(selector),
    ]);
  }

  /**
   * Return true if combinations table is displayed
   * @return {boolean}
   */
  hasCombinations() {
    return this.elementVisible(this.productCombinationTableRow.replace('%ID', 1), 2000);
  }

  /**
   * Delete all combinations
   * @return {Promise<void>}
   */
  async deleteAllCombinations() {
    if (await this.hasCombinations()) {
      // Unselect all
      await this.changeCheckboxValue(this.productCombinationSelectAllCheckbox, false);
      // Select all and delete combinations
      await Promise.all([
        this.changeCheckboxValue(this.productCombinationSelectAllCheckbox, true),
        this.waitForVisibleSelector(`${this.bulkCombinationsContainer}.show`),
      ]);
      await this.scrollTo(this.deleteCombinationsButton);
      await Promise.all([
        this.page.click(this.deleteCombinationsButton),
        this.waitForVisibleSelector(this.modalDialog),
      ]);
      await this.page.waitFor(250);
      await Promise.all([
        this.page.click(this.modalDialogYesButton),
        this.waitForSelectorAndClick(this.growlCloseButton),
      ]);
      // Unselect all
      await this.changeCheckboxValue(this.productCombinationSelectAllCheckbox, false);
      await this.closeCombinationsForm();
    }
  }

  /**
   * Close combinations form if open
   * @return {Promise<void>}
   */
  async closeCombinationsForm() {
    if (!(await this.elementVisible(`${this.productCombinationsBulkFormTitle}[aria-expanded='false']`, 1000))) {
      await Promise.all([
        this.page.click(this.productCombinationsBulkFormTitle),
        this.waitForVisibleSelector(`${this.productCombinationsBulkFormTitle}[aria-expanded='false']`),
      ]);
    }
  }

  /**
   * Reset friendly URL
   * @returns {Promise<void>}
   */
  async resetURL() {
    await this.goToFormStep(5);
    await this.waitForVisibleSelector(this.resetUrlButton);
    await this.scrollTo(this.resetUrlButton);
    await this.page.click(this.resetUrlButton);
    await this.goToFormStep(1);
  }

  /**
   * Get the error message when short description is too long
   * @returns {Promise<string>}
   */
  async getErrorMessageWhenSummaryIsTooLong() {
    return this.getTextContent(this.dangerMessageShortDescription);
  }

  /**
   * Get friendly URL
   * @returns {Promise<string|*>}
   */
  async getFriendlyURL() {
    await this.reloadPage();
    await this.goToFormStep(5);
    return this.getAttributeContent(this.friendlyUrlInput, 'value');
  }

  /**
   * Add specific prices
   * @param specificPriceData
   * @return {Promise<string>}
   */
  async addSpecificPrices(specificPriceData) {
    await this.reloadPage();
    // Go to pricing tab : id = 2
    await this.goToFormStep(2);
    await Promise.all([
      this.page.click(this.addSpecificPriceButton),
      this.waitForVisibleSelector(`${this.specificPriceForm}.show`),
    ]);
    // Choose combinations if exist
    if (specificPriceData.combinations) {
      await this.waitForVisibleSelector(this.combinationSelect);
      await this.scrollTo(this.combinationSelect);
      await this.selectByVisibleText(this.combinationSelect, specificPriceData.combinations);
    }
    await this.setValue(this.startingAtInput, specificPriceData.startingAt.toString());
    await this.setValue(this.applyDiscountOfInput, specificPriceData.discount.toString());
    await this.selectByVisibleText(this.reductionType, specificPriceData.reductionType);
    // Apply specific price
    await Promise.all([
      this.scrollTo(this.applyButton),
      this.page.click(this.applyButton),
    ]);
    const growlMessageText = await this.closeGrowlMessage();
    await this.goToFormStep(1);
    return growlMessageText;
  }

  /**
   * Get online product status
   * @returns {Promise<boolean>}
   */
  getOnlineButtonStatus() {
    return this.elementVisible(this.productOnlineTitle, 1000);
  }

  /**
   * Is quantity input visible
   * @returns {boolean}
   */
  isQuantityInputVisible() {
    return this.elementVisible(this.productQuantityInput, 1000);
  }
};
