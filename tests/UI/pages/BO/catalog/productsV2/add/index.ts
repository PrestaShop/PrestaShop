// Import pages
import virtualProductTab from '@pages/BO/catalog/productsV2/add/virtualProductTab';
import BOBasePage from '@pages/BO/BObasePage';
import productsPage from '@pages/BO/catalog/productsV2';
import descriptionTab from '@pages/BO/catalog/productsV2/add/descriptionTab';
import detailsTab from '@pages/BO/catalog/productsV2/add/detailsTab';
import stocksTab from '@pages/BO/catalog/productsV2/add/stocksTab';
import pricingTab from '@pages/BO/catalog/productsV2/add/pricingTab';
import packTab from '@pages/BO/catalog/productsV2/add/packTab';

import type ProductData from '@data/faker/product';
import type {ProductHeaderSummary} from '@data/types/product';

import type {Page} from 'playwright';

/**
 * Create Product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class CreateProduct extends BOBasePage {
  public readonly pageTitle: string;

  public readonly saveAndPublishButtonName: string;

  public readonly successfulDuplicateMessage: string;

  private readonly productNameInput: string;

  private readonly productActiveSwitchButton: string;

  private readonly productHeaderSummary: string;

  private readonly productHeaderTaxExcluded: string;

  private readonly productHeaderTaxIncluded: string;

  private readonly productHeaderQuantity: string;

  private readonly productHeaderReference: string;

  private readonly footerProductDropDown: string;

  private readonly previewProductButton: string;

  private readonly saveProductButton: string;

  private readonly deleteProductButton: string;

  private readonly deleteProductFooterModal: string;

  private readonly deleteProductSubmitButton: string;

  private readonly newProductButton: string;

  private readonly goToCatalogButton: string;

  private readonly duplicateProductButton: string;

  private readonly duplicateProductFooterModal: string;

  private readonly duplicateProductFooterModalConfirmSubmit: string;

  private readonly formProductPage: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on products V2 page
   */
  constructor() {
    super();

    this.pageTitle = 'Products';
    this.saveAndPublishButtonName = 'Save and publish';
    this.successfulDuplicateMessage = 'Successful duplication';

    // Header selectors
    this.productNameInput = '#product_header_name_1';
    this.productActiveSwitchButton = '#product_header_active_1';
    this.productHeaderSummary = '.product-header-summary';
    this.productHeaderTaxExcluded = `${this.productHeaderSummary} div[data-role=price-tax-excluded]`;
    this.productHeaderTaxIncluded = `${this.productHeaderSummary} div[data-role=price-tax-included]`;
    this.productHeaderQuantity = `${this.productHeaderSummary} div[data-role=quantity]`;
    this.productHeaderReference = '.product-header-references';

    // Footer selectors
    this.footerProductDropDown = '#product_footer_actions_dropdown';
    this.previewProductButton = '#product_footer_actions_preview';
    this.saveProductButton = '#product_footer_save';
    this.deleteProductButton = '#product_footer_actions_delete';

    // Footer modal
    this.deleteProductFooterModal = '#delete-product-footer-modal';
    this.deleteProductSubmitButton = `${this.deleteProductFooterModal} button.btn-confirm-submit`;
    this.newProductButton = '#product_footer_actions_new_product';
    this.goToCatalogButton = '#product_footer_actions_catalog';
    this.duplicateProductButton = '#product_footer_actions_duplicate_product';
    this.duplicateProductFooterModal = '#duplicate-product-footer-modal';
    this.duplicateProductFooterModalConfirmSubmit = `${this.duplicateProductFooterModal} button.btn-confirm-submit`;

    // Form
    this.formProductPage = 'form.product-page';
  }

  /*
  Methods
   */
  /**
   * Get product header summary
   * @param page {Page} Browser tab
   * @returns {Promise<ProductHeaderSummary>}
   */
  async getProductHeaderSummary(page: Page): Promise<ProductHeaderSummary> {
    return {
      priceTaxExc: await this.getTextContent(page, this.productHeaderTaxExcluded),
      priceTaxIncl: await this.getTextContent(page, this.productHeaderTaxIncluded),
      quantity: await this.getTextContent(page, this.productHeaderQuantity, false),
      reference: await this.getTextContent(page, this.productHeaderReference),
    };
  }

  /**
   * Get product header summary
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getProductID(page: Page): Promise<number> {
    return parseInt(await page.getAttribute(this.formProductPage, 'data-product-id') ?? '', 10);
  }

  /**
   * Set product status
   * @param page {Page} Browser tab
   * @param status {boolean} The product status
   * @returns {Promise<void>}
   */
  async setProductStatus(page: Page, status: boolean): Promise<void> {
    await this.setChecked(page, this.productActiveSwitchButton, status);
  }

  /**
   * Set product
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in new product page
   * @returns {Promise<string>}
   */
  async setProduct(page: Page, productData: ProductData): Promise<string> {
    await this.setValue(page, this.productNameInput, productData.name);

    await descriptionTab.setProductDescription(page, productData);

    await detailsTab.setProductDetails(page, productData);

    if (productData.type === 'virtual') {
      await virtualProductTab.setVirtualProduct(page, productData);
    } else if (productData.type !== 'combinations') {
      await stocksTab.setProductStock(page, productData);
    }

    if (productData.type === 'pack') {
      await packTab.setPackOfProducts(page, productData.pack);
    }

    await pricingTab.setProductPricing(page, productData);

    await this.setProductStatus(page, productData.status);

    return this.saveProduct(page);
  }

  /**
   * Save product
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async saveProduct(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.saveProductButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get save button name
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getSaveButtonName(page: Page): Promise<string> {
    return this.getTextContent(page, this.saveProductButton);
  }

  /**
   * Preview product in new tab
   * @param page {Page} Browser tab
   * @return {Promise<Page>}
   */
  async previewProduct(page: Page): Promise<Page> {
    await this.waitForSelectorAndClick(page, this.footerProductDropDown);
    const newPage = await this.openLinkWithTargetBlank(page, this.previewProductButton, 'body a');
    const textBody = await this.getTextContent(newPage, 'body');

    if (textBody.includes('[Debug] This page has moved')) {
      await this.clickAndWaitForURL(newPage, 'a');
    }
    return newPage;
  }

  /**
   * Delete product
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteProduct(page: Page): Promise<string> {
    await this.waitForSelectorAndClick(page, this.footerProductDropDown);
    await this.waitForSelectorAndClick(page, this.deleteProductButton);
    await this.waitForVisibleSelector(page, this.deleteProductFooterModal);
    await this.clickAndWaitForURL(page, this.deleteProductSubmitButton);

    return productsPage.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Go to catalog page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToCatalogPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.goToCatalogButton);
  }

  /**
   * Click on new product button
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnNewProductButton(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.footerProductDropDown);
    await this.waitForSelectorAndClick(page, this.newProductButton);

    return this.elementVisible(page, productsPage.modalCreateProduct, 1000);
  }

  /**
   * Click on duplicate product button
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async duplicateProduct(page: Page): Promise<string> {
    await this.waitForSelectorAndClick(page, this.footerProductDropDown);
    await this.waitForSelectorAndClick(page, this.duplicateProductButton);

    await this.waitForSelectorAndClick(page, this.duplicateProductFooterModalConfirmSubmit);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Choose product type
   * @param page {Page} Browser tab
   * @param productType {string} Data to choose in product type
   * @returns {Promise<void>}
   */
  async chooseProductType(page: Page, productType: string): Promise<void> {
    const currentUrl: string = page.url();

    await productsPage.selectProductType(page, productType);
    await productsPage.clickOnAddNewProduct(page);
    await page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'});
  }

  /**
   * Is choose product iframe visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isChooseProductIframeVisible(page: Page): Promise<boolean> {
    return !(await this.elementNotVisible(page, `${productsPage.modalCreateProduct} iframe`, 1000));
  }
}

export default new CreateProduct();
