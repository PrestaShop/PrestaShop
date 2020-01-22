require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddProduct extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Product •';
    // Text Message
    this.settingUpdatedMessage = 'Settings updated.';
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
    this.productDescriotionTab = '#tab_description a';
    this.productDescriptionIframe = '#form_step1_description_1_ifr';
    this.productTaxRuleSelect = '#step2_id_tax_rules_group_rendered';
    this.productDeleteLink = '.product-footer a.delete';

    // Form nav
    this.formNavList = '#form-nav';
    this.forNavlistItemLink = `${this.formNavList} #tab_step%ID a`;

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
    // Selector of Step 5 : SEO
    this.resetUrlButton = '#seo-url-regenerate';
    // Growls : override value from BObasePage
    this.growlDefaultDiv = '#growls-default';
    this.growlMessageBlock = `${this.growlDefaultDiv} .growl-message:last-of-type`;
    this.growlCloseButton = `${this.growlDefaultDiv} .growl-close`;
  }

  /*
  Methods
   */
  /**
   * Create or edit product in BO
   * @param productData
   * @param switchProductOnline
   * @return {Promise<textContent>}
   */
  async createEditProduct(productData, switchProductOnline = true) {
    // Set Name, type of product, Reference, price ttc and quantity, and with combinations
    await this.page.click(this.productNameInput, {clickCount: 3});
    await this.page.type(this.productNameInput, productData.name);
    await this.selectByVisibleText(this.productTypeSelect, productData.type);
    await this.page.click(this.productReferenceInput, {clickCount: 3});
    await this.page.type(this.productReferenceInput, productData.reference);
    await this.page.click(this.productPriceTtcInput, {clickCount: 3});
    await this.page.type(this.productPriceTtcInput, productData.price);
    // Set description value
    await this.page.click(this.productDescriotionTab);
    await this.setValueOnTinymceInput(this.productDescriptionIframe, productData.description);
    // Add combinations if exists
    if (productData.withCombination) {
      await this.page.click(this.productWithCombinationsInput);
      await this.setCombinationsInProduct(productData);
    } else {
      await this.page.click(this.productQuantityInput, {clickCount: 3});
      await this.page.type(this.productQuantityInput, productData.quantity);
    }
    await this.selectByVisibleText(this.productTaxRuleSelect, productData.taxRule);
    // Switch product online before save
    if (switchProductOnline) {
      await Promise.all([
        this.page.waitForSelector(this.growlMessageBlock, {visible: true}),
        this.page.click(this.productOnlineSwitch),
      ]);
    }
    // Save created product
    await Promise.all([
      this.page.waitForSelector(this.growlMessageBlock, {visible: true}),
      this.page.click(this.saveProductButton),
    ]);
    return this.getTextContent(this.growlMessageBlock);
  }

  /**
   * Set Combinations for product
   * @param productData
   * @return {Promise<void>}
   */
  async setCombinationsInProduct(productData) {
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
      this.page.waitForSelector(`${this.productCombinationsBulkForm}:not(.inactive)`, {visible: true}),
      this.page.waitForSelector(
        `${this.productCombinationTableRow.replace('%ID', 1)}[style='display: table-row;']`,
        {visible: true},
      ),
      this.page.click(this.generateCombinationsButton),
      this.waitForSelectorAndClick(this.growlMessageBlock),
    ]);
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
      this.page.waitForSelector(`${this.productCombinationsBulkFormTitle}[aria-expanded='true']`, {visible: true}),
      await this.changeCheckboxValue(this.productCombinationSelectAllCheckbox, true),
    ]);
    // Edit quantity
    await this.page.waitForSelector(this.applyOnCombinationsButton, {visible: true});
    await this.scrollTo(this.productCombinationBulkQuantityInput);
    await this.page.type(this.productCombinationBulkQuantityInput, quantity);
    await this.scrollTo(this.applyOnCombinationsButton);
    await this.page.click(this.applyOnCombinationsButton);
    await this.closeCombinationsForm();
  }

  /**
   * Preview product in new tab
   * @return page opened
   */
  async previewProduct() {
    await this.page.waitForSelector(this.previewProductLink);
    this.page = await this.openLinkWithTargetBlank(this.page, this.previewProductLink);
    const textBody = await this.getTextContent('body');
    if (await textBody.includes('[Debug] This page has moved')) {
      await Promise.all([
        this.page.waitForNavigation({waitUntil: 'networkidle0'}),
        this.page.click('a'),
      ]);
    }
    return this.page;
  }

  /**
   * Delete product
   * @return {Promise<textContent>}
   */
  async deleteProduct() {
    await Promise.all([
      this.page.waitForSelector(this.modalDialog, {visible: true}),
      this.page.click(this.productDeleteLink),
    ]);
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true}),
      this.page.click(this.modalDialogYesButton),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Navigate between forms in add product
   * @param id
   * @return {Promise<void>}
   */
  async goToFormStep(id = '1') {
    const selector = this.forNavlistItemLink.replace('%ID', id);
    await Promise.all([
      this.page.waitForSelector(`${selector}[aria-selected='true']`, {visible: true}),
      this.page.click(selector),
    ]);
  }

  /**
   * Delete all combinations
   * @return {Promise<void>}
   */
  async deleteAllCombinations() {
    if (await this.elementVisible(this.productCombinationTableRow.replace('%ID', 1), 2000)) {
      // Unselect all
      await this.changeCheckboxValue(this.productCombinationSelectAllCheckbox, false);
      // Select all and delete combinations
      await Promise.all([
        this.changeCheckboxValue(this.productCombinationSelectAllCheckbox, true),
        this.page.waitForSelector(`${this.productCombinationsBulkFormTitle}[aria-expanded='true']`, {visible: true}),
        this.page.waitForSelector(this.deleteCombinationsButton, {visible: true}),
      ]);
      await this.page.waitFor(250);
      await this.scrollTo(this.deleteCombinationsButton);
      await Promise.all([
        this.page.click(this.deleteCombinationsButton),
        this.page.waitForSelector(this.modalDialog, {visible: true}),
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
        this.page.waitForSelector(`${this.productCombinationsBulkFormTitle}[aria-expanded='false']`, {visible: true}),
      ]);
    }
  }

  /**
   * Go to SEO step and reset friendly URL
   * @returns {Promise<void>}
   */
  async resetURL() {
    await this.waitForSelectorAndClick(this.forNavlistItemLink.replace('%ID', 5));
    await Promise.all([
      this.page.waitForSelector(this.resetUrlButton, {visible: true}),
      this.scrollTo(this.resetUrlButton),
      this.page.click(this.resetUrlButton),
    ]);
  }
};
