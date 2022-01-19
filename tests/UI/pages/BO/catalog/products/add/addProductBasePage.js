require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add product page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddProductBasePage extends BOBasePage {
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

    // Header selectors
    this.productNameInput = '#form_step1_name_1';
    this.productTypeSelect = '#form_step1_type_product';

    // Selectors of form nav list
    this.formNavList = '#form-nav';
    this.forNavListItemLink = id => `${this.formNavList} #tab_step${id} a`;

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
   * Get product name from input
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getProductName(page) {
    return this.getAttributeContent(page, this.productNameInput, 'value');
  }

  /**
   * Navigate between forms in add product
   * @param page {Page} Browser tab
   * @param id {number} Value of form id to go
   * @return {Promise<void>}
   */
  async goToFormStep(page, id = 1) {
    const selector = this.forNavListItemLink(id);
    if (await this.elementNotVisible(page, `${selector}[aria-selected='true']`, 1000)) {
      await page.click(selector);
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
   * Get online product status
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  getOnlineButtonStatus(page) {
    return this.elementVisible(page, this.productOnlineTitle, 1000);
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
}

module.exports = AddProductBasePage;
