const BOBasePage = require('../BO/BObasePage');

module.exports = class AddProduct extends BOBasePage {
  constructor(page) {
    super(page);

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
    this.productDeleteLink = '.product-footer a.delete';

    // Growls : override value from BObasePage
    this.growlMessageBloc = '#growls-default .growl-message';
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
    await this.page.type(this.productTypeSelect, productData.type);
    await this.page.click(this.productReferenceInput, {clickCount: 3});
    await this.page.type(this.productReferenceInput, productData.reference);
    await this.page.click(this.productQuantityInput, {clickCount: 3});
    await this.page.type(this.productQuantityInput, productData.quantity);
    await this.page.click(this.productPriceTtcInput, {clickCount: 3});
    await this.page.type(this.productPriceTtcInput, productData.price);
    // Set description value
    await this.page.click(this.productDescriotionTab);
    await this.setValueOnTinymceInput(this.productDescriptionIframe, productData.description);
    // Add combinations if exists
    if (productData.withCombination === true) await this.page.click(this.productWithCombinationsInput);
    // Switch product online before save
    if (switchProductOnline) {
      await Promise.all([
        this.page.waitForSelector(this.growlMessageBloc, {visible: true}),
        this.page.click(this.productOnlineSwitch),
      ]);
    }
    // Save created product
    await Promise.all([
      this.page.waitForSelector(this.growlMessageBloc, {visible: true}),
      this.page.click(this.saveProductButton),
    ]);
    return this.getTextContent(this.growlMessageBloc);
  }

  /**
   * Preview product in new tab
   * @return page opened
   */
  async previewProduct() {
    return this.openLinkWithTargetBlank(this.page, this.previewProductLink);
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
      this.page.waitForSelector(this.alertSuccessBlocParagraph, {visible: true}),
      this.page.click(this.modalDialogYesButton),
    ]);
    return this.getTextContent(this.alertSuccessBlocParagraph);
  }
};
