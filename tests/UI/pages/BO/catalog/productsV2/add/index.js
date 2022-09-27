require('module-alias/register');
// Importing page
const BOBasePage = require('@pages/BO/BObasePage');
const productsPage = require('@pages/BO/catalog/productsV2');
const descriptionTab = require('@pages/BO/catalog/productsV2/add/descriptionTab');
const detailsTab = require('@pages/BO/catalog/productsV2/add/detailsTab');
const stocksTab = require('@pages/BO/catalog/productsV2/add/stocksTab');
const virtualProductTab = require('@pages/BO/catalog/productsV2/add/virtualProductTab');
const pricingTab = require('@pages/BO/catalog/productsV2/add/pricingTab');

/**
 * Products V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Products extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on products V2 page
   */
  constructor() {
    super();

    this.pageTitle = 'Products';

    // Header selectors
    this.productNameInput = '#product_header_name_1';
    this.productActiveSwitchButton = '#product_header_active_1';

    // Footer selectors
    this.previewProductButton = '#product_footer_preview';
    this.saveProductButton = '#product_footer_save';
    this.deleteProductButton = '#product_footer_delete';

    // Footer modal
    this.deleteProductFooterModal = '#delete-product-footer-modal';
    this.deleteProductSubmitButton = `${this.deleteProductFooterModal} button.btn-confirm-submit`;
    this.newProductButton = '#product_footer_new_product';
    this.goToCatalogButton = '#product_footer_catalog';
  }

  /*
  Methods
   */

  /**
   * Save product
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async saveProduct(page) {
    await this.clickAndWaitForNavigation(page, this.saveProductButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get save button name
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getSaveButtonName(page) {
    return this.getTextContent(page, this.saveProductButton);
  }

  /**
   * Set product
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in new product page
   * @returns {Promise<string>}
   */
  async setProduct(page, productData) {
    await this.setValue(page, this.productNameInput, productData.name);

    await descriptionTab.setProductDescription(page, productData);

    await detailsTab.setProductDetails(page, productData);

    if (productData.type === 'virtual') {
      await virtualProductTab.setVirtualProduct(page, productData);
    } else if (productData.type !== 'combinations') {
      await stocksTab.setProductStock(page, productData);
    }

    await pricingTab.setProductPricing(page, productData);

    await this.setChecked(page, this.productActiveSwitchButton, productData.status);

    return this.saveProduct(page);
  }

  /**
   * Preview product in new tab
   * @param page {Page} Browser tab
   * @return page opened
   */
  async previewProduct(page) {
    const newPage = await this.openLinkWithTargetBlank(page, this.previewProductButton, 'body a');
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
    await this.waitForSelectorAndClick(page, this.deleteProductButton);
    await this.waitForVisibleSelector(page, this.deleteProductFooterModal);
    await this.clickAndWaitForNavigation(page, this.deleteProductSubmitButton);

    return productsPage.getAlertSuccessBlockParagraphContent(page);
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
   * Click on new product button
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnNewProductButton(page) {
    await this.waitForSelectorAndClick(page, this.newProductButton);

    return this.elementVisible(page, productsPage.modalCreateProduct, 1000);
  }

  /**
   * Choose product type
   * @param page {Page} Browser tab
   * @param productType
   * @returns {Promise<void>}
   */
  async chooseProductType(page, productType) {
    await productsPage.chooseProductType(page, productType);
    await page.waitForNavigation({waitUntil: 'networkidle'});
  }

  /**
   * Is choose product iframe visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isChooseProductIframeVisible(page) {
    return !(await this.elementNotVisible(page, `${productsPage.modalCreateProduct} iframe`, 1000));
  }
}

module.exports = new Products();
