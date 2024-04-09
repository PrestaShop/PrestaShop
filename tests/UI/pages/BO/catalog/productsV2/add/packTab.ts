// Import pages
import BOBasePage from '@pages/BO/BObasePage';

import type {ProductPackInformation, ProductPackItem} from '@data/types/product';

import type {Page} from 'playwright';
import {ProductPackOptions, ProductStockMovement} from '@data/types/product';

/**
 * Pack tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class PackTab extends BOBasePage {
  private readonly packTabLink: string;

  private readonly searchProductInput: string;

  private readonly searchResult: string;

  private readonly packSearchResult: string;

  private readonly searchResultSuggestion: string;

  private readonly searchResultSuggestionRow: (productInSearchList: number) => string;

  private readonly listOfProducts: string;

  private readonly quantityInput: (productInList: number) => string;

  private readonly productRowInList: (productInList: number) => string;

  private readonly productInListLegend: (productInList: number) => string;

  private readonly deleteProductInListIcon: (productInList: number) => string;

  private readonly productInListImage: (productInList: number) => string;

  private readonly productInListName: (productInList: number) => string;

  private readonly productInListReference: (productInList: number) => string;

  private readonly productInListQuantity: (productInList: number) => string;

  private readonly alertDangerProductInPack: (productInList: number) => string;

  private readonly editQuantityInput: string;

  private readonly minimalQuantityInput: string;

  private readonly packStockTypeRadioButton: (buttonRow: number) => string;

  private readonly dateTimeRowInTable: (movementRow: number) => string;

  private readonly employeeRowInTable: (movementRow: number) => string;

  private readonly quantityRowInTable: (movementRow: number) => string;

  private readonly modalDeleteProduct: string;

  private readonly confirmDeleteButtonInModal: string;

  private readonly cancelDeleteButtonInModal: string;

  private readonly saveProductButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on pack tab
   */
  constructor() {
    super();

    // Selectors in pack tab
    this.packTabLink = '#product_stock-tab-nav';
    // Search product selectors
    this.searchProductInput = '#product_stock_packed_products_search_input';
    this.searchResult = '.tt-menu.tt-open';
    this.packSearchResult = `${this.searchResult} div.tt-dataset.tt-dataset-2`;
    this.searchResultSuggestion = `${this.packSearchResult} div.search-suggestion`;
    this.searchResultSuggestionRow = (productInSearchList: number) => `${this.searchResultSuggestion}:nth-child(`
      + `${productInSearchList})`;

    // List of products in pack selectors
    this.listOfProducts = '#product_stock_packed_products_list';
    this.quantityInput = (productInList: number) => `#product_stock_packed_products_${productInList}_quantity`;
    this.productRowInList = (productInList: number) => `${this.listOfProducts} li:nth-child(${productInList})`;
    this.productInListLegend = (productInList: number) => `${this.productRowInList(productInList)} div.packed-product-legend`;
    this.deleteProductInListIcon = (productInList: number) => `${this.productInListLegend(productInList)} `
      + 'span i.entity-item-delete';
    this.productInListImage = (productInList: number) => `${this.productRowInList(productInList)} div.packed-product-image img`;
    this.productInListName = (productInList: number) => `#product_stock_packed_products_${productInList - 1}_name`;
    this.productInListReference = (productInList: number) => `${this.productInListLegend(productInList)} span.reference-preview`;
    this.productInListQuantity = (productInList: number) => `#product_stock_packed_products_${productInList - 1}_quantity`;
    this.alertDangerProductInPack = (productInList: number) => `${this.productRowInList(productInList)} div.alert-danger p`;

    // Modal delete product in pack selectors
    this.modalDeleteProduct = '#modal-confirm-remove-entity';
    this.confirmDeleteButtonInModal = '#modal-confirm-remove-entity button.btn-confirm-submit';
    this.cancelDeleteButtonInModal = '#modal-confirm-remove-entity button.btn-outline-secondary';

    // Stock movement table selectors
    this.dateTimeRowInTable = (movementRow: number) => `#product_stock_quantities_stock_movements_${movementRow}_date `
      + '+ span';
    this.employeeRowInTable = (movementRow: number) => `#product_stock_quantities_stock_movements_${movementRow}_`
      + 'employee_name + span';
    this.quantityRowInTable = (movementRow: number) => `#product_stock_quantities_stock_movements_${movementRow}_`
      + 'delta_quantity + span';

    // Edit quantity selectors
    this.editQuantityInput = '#product_stock_quantities_delta_quantity_delta';
    this.minimalQuantityInput = '#product_stock_quantities_minimal_quantity';
    this.packStockTypeRadioButton = (buttonRow: number) => `#product_stock_pack_stock_type_${buttonRow} +i`;

    // Save button selector
    this.saveProductButton = '#product_footer_save';
  }

  /*
 Methods
  */

  // Methods to search product
  /**
   * Search product to add to the pack
   * @param page {Page} Browser tab
   * @param productName {string} Product name to search
   */
  async searchProduct(page: Page, productName: string): Promise<string> {
    await this.waitForSelectorAndClick(page, this.packTabLink);
    await page.type(this.searchProductInput, productName);
    await this.waitForVisibleSelector(page, this.searchResult);
    await page.waitForTimeout(1000);

    return this.getTextContent(page, this.packSearchResult);
  }

  /**
   * Get number of searched product
   * @param page {Page} Browser tab
   */
  async getNumberOfSearchedProduct(page: Page): Promise<number> {
    return (await page.$$(`${this.packSearchResult} div`)).length;
  }

  /**
   * Select product from list
   * @param page {Page} Browser tab
   * @param productInSearchList {number} The row of product in the search list
   */
  async selectProductFromList(page: Page, productInSearchList: number): Promise<boolean> {
    const numberOfProducts = await this.getNumberOfSearchedProduct(page);

    if (numberOfProducts > 1) {
      await this.waitForSelectorAndClick(page, this.searchResultSuggestionRow(productInSearchList));
    } else {
      await this.waitForSelectorAndClick(page, this.searchResultSuggestion);
    }

    return this.elementVisible(page, this.listOfProducts, 1000);
  }

  // Methods to get products in pack
  /**
   * Get number of product in pack
   * @param page {Page} Browser tab
   */
  async getNumberOfProductsInPack(page: Page): Promise<number> {
    return (await page.$$(`${this.listOfProducts} li`)).length;
  }

  /**
   * Get product in pack information
   * @param page {Page} Browser tab
   * @param productInList {number} The row of product in pack
   */
  async getProductInPackInformation(page: Page, productInList: number): Promise<ProductPackInformation> {
    await this.waitForVisibleSelector(page, this.listOfProducts);
    return {
      image: await this.getAttributeContent(page, this.productInListImage(productInList), 'src'),
      name: await this.getAttributeContent(page, this.productInListName(productInList), 'value'),
      reference: await this.getTextContent(page, this.productInListReference(productInList)),
      quantity: parseInt(await this.getAttributeContent(page, this.productInListQuantity(productInList), 'value'), 10),
    };
  }

  // Methods to add/edit products in pack
  /**
   * Set product in pack quantity
   * @param page {Page} Browser tab
   * @param productInList {number} The row of product in pack
   * @param quantity {number|string} The product quantity to set
   */
  async setProductQuantity(page: Page, productInList: number, quantity: number|string): Promise<void> {
    await this.setValue(page, this.quantityInput(productInList), quantity);
  }

  /**
   * Save and get product in pack error message
   * @param page {Page} Browser tab
   * @param productInList {number} The row of product in pack
   */
  async saveAndGetProductInPackErrorMessage(page: Page, productInList: number): Promise<string> {
    await page.click(this.saveProductButton);

    return this.getTextContent(page, this.alertDangerProductInPack(productInList));
  }

  /**
   * Add product to pack
   * @param page {Page} Browser tab
   * @param product {string} Value of product name to set on input
   * @param quantity {number} Value of quantity to set on input
   */
  async addProductToPack(page: Page, product: string, quantity: number): Promise<void> {
    await this.searchProduct(page, product);
    await this.waitForSelectorAndClick(page, this.searchResultSuggestion);
    await this.waitForVisibleSelector(page, this.listOfProducts);
    const numberOfProducts: number = await this.getNumberOfProductsInPack(page);

    if (quantity) {
      await this.setValue(page, this.quantityInput(numberOfProducts - 1), quantity);
    }
  }

  /**
   * Add combination
   * @param page {Page} Browser tab
   * @param packData {ProductPackItem[]} Data of the pack
   * @returns {Promise<void>}
   */
  async setPackOfProducts(page: Page, packData: ProductPackItem[]): Promise<void> {
    await this.waitForSelectorAndClick(page, this.packTabLink);

    for (let i = 0; i < packData.length; i += 1) {
      await this.addProductToPack(page, packData[i].reference, packData[i].quantity);
    }
  }

  /**
   * Get stock movements data
   * @param page {Page} Browser tab
   * @param movementRow {number} Movement row in table stock movements
   */
  async getStockMovement(page: Page, movementRow: number): Promise<ProductStockMovement> {
    return {
      dateTime: await this.getTextContent(page, this.dateTimeRowInTable(movementRow - 1)),
      employee: await this.getTextContent(page, this.employeeRowInTable(movementRow - 1)),
      quantity: await this.getNumberFromText(page, this.quantityRowInTable(movementRow - 1)),
    };
  }

  // Methods to delete products in pack
  /**
   * Is delete modal visible
   * @param page {Page} Browser tab
   */
  async isDeleteModalVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.modalDeleteProduct, 1000);
  }

  /**
   * Cancel delete product from pack
   * @param page {Page} Browser tab
   */
  async cancelDeleteProductFromPack(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.cancelDeleteButtonInModal);
  }

  /**
   * Confirm delete product from pack
   * @param page {Page} Browser tab
   */
  async confirmDeleteProductFromPack(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.confirmDeleteButtonInModal);
  }

  /**
   * Delete product from pack
   * @param page {Page} Browser tab
   * @param productInList {number} The row of product in pack
   * @param toDelete {boolean} True if we need to delete product, false to cancel delete
   */
  async deleteProduct(page: Page, productInList: number, toDelete: boolean): Promise<boolean | string> {
    await this.waitForSelectorAndClick(page, this.deleteProductInListIcon(productInList));

    await this.waitForVisibleSelector(page, this.modalDeleteProduct);

    if (toDelete) {
      await this.confirmDeleteProductFromPack(page);
      return this.getAlertSuccessBlockParagraphContent(page);
    }
    await this.cancelDeleteProductFromPack(page);
    return this.isDeleteModalVisible(page);
  }

  // Methods to edit pack of products
  /**
   * Edit pack of products
   * @param page {Page} Browser tab
   * @param packData {ProductPackOptions} Data to edit pack of products
   */
  async editPackOfProducts(page: Page, packData: ProductPackOptions): Promise<void> {
    await this.setValue(page, this.editQuantityInput, packData.quantity);
    await this.setValue(page, this.minimalQuantityInput, packData.minimalQuantity);

    switch (packData.packQuantitiesOption) {
      case 'Use pack quantity':
        await page.click(this.packStockTypeRadioButton(0));
        break;

      case 'Use quantity of products in the pack':
        await page.click(this.packStockTypeRadioButton(1));
        break;

      case 'Use both, whatever is lower':
        await page.click(this.packStockTypeRadioButton(2));
        break;

      case 'Default':
        await page.click(this.packStockTypeRadioButton(3));
        break;

      default:
        throw new Error(`Radio button for ${packData.packQuantitiesOption} was not found`);
    }
  }
}

export default new PackTab();
