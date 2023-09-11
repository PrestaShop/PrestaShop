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

  private readonly productImageUrl: string;

  private readonly productName: string;

  private readonly productNameInput: (locale: string) => string;

  private readonly productNameLanguageButton: string;

  private readonly productNameLanguageDropdown: string;

  private readonly productNameLanguageDropdownItem: (locale: string) => string;

  private readonly productTypeLabel: string;

  private readonly productTypePreview: string;

  private readonly productTypePreviewLabel: string;

  private readonly productActiveSwitchButton: (status: number) => string;

  private readonly productActiveSwitchButtonToggleInput: string;

  private readonly productHeaderSummary: string;

  private readonly productHeaderTaxExcluded: string;

  private readonly productHeaderTaxIncluded: string;

  private readonly productHeaderQuantity: string;

  private readonly productHeaderReferences: string;

  private readonly productHeaderReference: (type: string) => string;

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

  private readonly tabLink: (tabName: string) => string;

  private readonly modalSwitchType: string;

  private readonly modalSwitchTypeBtnChoice: (productType: string) => string;

  private readonly modalSwitchTypeBtnSubmit: string;

  private readonly modalConfirmType: string;

  private readonly modalConfirmTypeBtnSubmit: string;

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
    this.productActiveSwitchButton = '#product_header_active.ps-switch';
    this.productActiveSwitchButtonToggleInput = `${this.productActiveSwitchButton} input`;
    this.productImageUrl = '#product_header_cover_thumbnail';
    this.productName = '#product_header_name';
    this.productNameInput = (locale: string) => `${this.productName} div.js-locale-${locale} input`;
    this.productNameLanguageButton = `${this.productName}_dropdown`;
    this.productNameLanguageDropdown = `${this.productName} .dropdown .dropdown-menu`;
    this.productNameLanguageDropdownItem = (locale: string) => `${this.productNameLanguageDropdown} span`
      + `[data-locale="${locale}"]`;
    this.productTypePreview = '.product-type-preview';
    this.productTypePreviewLabel = `${this.productTypePreview}-label`;
    this.productTypeLabel = '.product-type-preview-label';
    this.productActiveSwitchButton = (status: number) => `#product_header_active_${status}`;
    this.productHeaderSummary = '.product-header-summary';
    this.productHeaderTaxExcluded = `${this.productHeaderSummary} div[data-role=price-tax-excluded]`;
    this.productHeaderTaxIncluded = `${this.productHeaderSummary} div[data-role=price-tax-included]`;
    this.productHeaderQuantity = `${this.productHeaderSummary} div[data-role=quantity]`;
    this.productHeaderReferences = '.product-header-references';
    this.productHeaderReference = (type: string) => `${this.productHeaderReferences} .product-reference`
      + `[data-reference-type="${type}"] span`;

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

    // Tab
    this.tabLink = (tabName: string) => `#product_${tabName}-tab-nav`;

    // Modal : Switch Product Type
    this.modalSwitchType = '#switch-product-type-modal';
    this.modalSwitchTypeBtnChoice = (productType: string) => `${this.modalSwitchType} button.product-type-choice`
        + `[data-value="${productType}"]`;
    this.modalSwitchTypeBtnSubmit = `${this.modalSwitchType} .modal-footer button.btn-confirm-submit`;
    this.modalConfirmType = '#modal-confirm-product-type';
    this.modalConfirmTypeBtnSubmit = `${this.modalConfirmType} .modal-footer button.btn-confirm-submit`;
  }

  /*
  Methods
   */
  /**
   * Go to a tab
   * @param page {Page} Browser tab
   * @param tabName {'description'|'details'|'options'|'pricing'|'seo'|'shipping'|'stock'} Name of the tab
   * @returns {Promise<void>}
   */
  async goToTab(
    page: Page,
    tabName: 'description' | 'details' | 'options' | 'pricing' | 'seo' | 'shipping' | 'stock',
  ): Promise<void> {
    await this.waitForSelectorAndClick(page, this.tabLink(tabName));
    await this.waitForVisibleSelector(page, `${this.tabLink(tabName)} a.active`, 2000);
  }

  /**
   * Is Tab active
   * @param page {Page} Browser tab
   * @param tabName {'description'|'details'|'options'|'pricing'|'seo'|'shipping'|'stock'} Name of the tab
   * @returns {Promise<boolean>}
   */
  async isTabActive(
    page: Page,
    tabName: 'description' | 'details' | 'options' | 'pricing' | 'seo' | 'shipping' | 'stock',
  ): Promise<boolean> {
    return this.elementVisible(page, `${this.tabLink(tabName)} a.active`, 2000);
  }

  /**
   * Is Tab visible
   * @param page {Page} Browser tab
   * @param tabName {'description'|'details'|'options'|'pricing'|'seo'|'shipping'|'stock'} Name of the tab
   * @returns {Promise<boolean>}
   */
  async isTabVisible(
    page: Page,
    tabName: 'description' | 'details' | 'options' | 'pricing' | 'seo' | 'shipping' | 'stock',
  ): Promise<boolean> {
    return this.elementVisible(page, `${this.tabLink(tabName)} a`, 2000);
  }

  /**
   * Get product header summary
   * @param page {Page} Browser tab
   * @returns {Promise<ProductHeaderSummary>}
   */
  async getProductHeaderSummary(page: Page): Promise<ProductHeaderSummary> {
    return {
      imageUrl: await this.getAttributeContent(page, this.productImageUrl, 'value'),
      priceTaxExc: await this.getTextContent(page, this.productHeaderTaxExcluded),
      priceTaxIncl: await this.getTextContent(page, this.productHeaderTaxIncluded),
      quantity: await this.getTextContent(page, this.productHeaderQuantity, false),
      reference: await this.getTextContent(page, this.productHeaderReference('reference'), false),
      mpn: (await page.locator(this.productHeaderReference('mpn')).count())
        ? await this.getTextContent(page, this.productHeaderReference('mpn'), false)
        : '',
      upc: (await page.locator(this.productHeaderReference('upc')).count())
        ? await this.getTextContent(page, this.productHeaderReference('upc'), false)
        : '',
      ean_13: (await page.locator(this.productHeaderReference('ean_13')).count())
        ? await this.getTextContent(page, this.productHeaderReference('ean_13'), false)
        : '',
      isbn: (await page.locator(this.productHeaderReference('isbn')).count())
        ? await this.getTextContent(page, this.productHeaderReference('isbn'), false)
        : '',
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
   * Return the product type
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductType(page: Page): Promise<string> {
    const typeLabel = await this.getTextContent(page, this.productTypePreviewLabel);

    switch (typeLabel) {
      case 'Standard product':
        return 'standard';
      case 'Product with combinations':
        return 'combinations';
      case 'Pack of products':
        return 'pack';
      case 'Virtual product':
        return 'virtual';
      default:
        throw new Error(`Type ${typeLabel} is not defined`);
    }
  }

  /**
   * Set product status
   * @param page {Page} Browser tab
   * @param status {boolean} The product status
   * @returns {Promise<void>}
   */
  async setProductStatus(page: Page, status: boolean): Promise<boolean> {
    if (await this.getProductStatus(page) !== status) {
      await this.clickAndWaitForLoadState(page, this.productActiveSwitchButton);
      return true;
    }

    return false;
  }

  /**
   * Get product status
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async getProductStatus(page: Page): Promise<boolean> {
    // Get value of the check input
    const inputValue = await this.getAttributeContent(
      page,
      `${this.productActiveSwitchButtonToggleInput}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Set product
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in new product page
   * @returns {Promise<string>}
   */
  async setProduct(page: Page, productData: ProductData): Promise<string> {
    // Set status
    await this.setProductStatus(page, productData.status);
    // Set description
    await descriptionTab.setProductDescription(page, productData);
    // Set name
    await this.setProductName(page, productData.name, 'en');
    await this.setProductName(page, productData.nameFR, 'fr');

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

    return this.saveProduct(page);
  }

  /**
   * Set product name
   * @param page {Page} Browser tab
   * @param name {string} Name of the product
   * @param locale {string} Locale
   * @returns {Promise<void>}
   */
  async setProductName(page: Page, name: string, locale: string = 'en'): Promise<void> {
    await this.waitForSelectorAndClick(page, this.productNameLanguageButton);
    await this.waitForSelectorAndClick(page, this.productNameLanguageDropdownItem(locale));
    await page.evaluate(
      (selector: string) => (document.querySelector(selector) as HTMLElement).click(),
      this.productNameLanguageDropdownItem(locale),
    );
    await this.setValue(page, this.productNameInput(locale), name);
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
   * Change product type
   * @param page {Page} Browser tab
   * @param productType {string} Data to choose in product type
   * @returns {Promise<string>}
   */
  async changeProductType(page: Page, productType: string): Promise<string> {
    // Click on the type label
    await page.locator(this.productTypePreview).click();
    // Modal "Change the product type"
    await this.elementVisible(page, this.modalSwitchType, 2000);
    await this.waitForSelectorAndClick(page, this.modalSwitchTypeBtnChoice(productType));
    await this.waitForSelectorAndClick(page, this.modalSwitchTypeBtnSubmit);
    // Modal "Are you sure you want to change the product type?"
    await this.elementVisible(page, this.modalConfirmType, 2000);
    await this.elementVisible(page, this.modalConfirmTypeBtnSubmit, 2000);
    await this.waitForSelectorAndClick(page!, this.modalConfirmTypeBtnSubmit);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Is choose product iframe visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isChooseProductIframeVisible(page: Page): Promise<boolean> {
    return !(await this.elementNotVisible(page, `${productsPage.modalCreateProduct} iframe`, 1000));
  }

  /**
   * Return the product name
   * @param page {Page} Browser tab
   * @param locale {string} Locale
   * @returns {Promise<string>}
   */
  async getProductName(page: Page, locale: string = 'en'): Promise<string> {
    return this.getAttributeContent(page, this.productNameInput(locale), 'value');
  }

  /**
   * Get product status
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async getProductStatus(page: Page): Promise<boolean> {
    return this.isChecked(page, this.productActiveSwitchButton(1));
  }
}

export default new CreateProduct();
