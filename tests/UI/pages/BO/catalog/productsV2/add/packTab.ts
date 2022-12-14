// Import pages
import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Pack tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends CommonPage
 */
export default class PackTab extends BOBasePage {
  private readonly packTabLink: string;

  private readonly searchProductInput: string;

  private readonly searchResult: string;

  private readonly packSearchResult: string;

  private readonly listOfProducts: string;

  private readonly quantityInput: (productInList: number) => string;

  private readonly saveProductButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on pack tab
   */
  constructor() {
    super();

    // Selectors in pack tab
    this.packTabLink = '#product_stock-tab-nav';
    this.searchProductInput = '#product_stock_packed_products_search_input';
    this.searchResult = '.tt-menu.tt-open';
    this.packSearchResult = `${this.searchResult} div.tt-dataset.tt-dataset-2`;
    this.listOfProducts = '#product_stock_packed_products_list';
    this.quantityInput = (productInList) => `#product_stock_packed_products_${productInList}_quantity`;
    this.saveProductButton = '#product_footer_save';
  }

  /*
 Methods
  */

  async searchProductToPack(page: Page, product: string): Promise<string> {
    await this.waitForSelectorAndClick(page, this.packTabLink);
    await page.type(this.searchProductInput, product);
    await this.waitForVisibleSelector(page, this.searchResult);
    await page.waitForTimeout(1000);

    return this.getTextContent(page, this.packSearchResult);
  }

  async getNumberOfSearchedProduct(page: Page): Promise<number> {
    return (await page.$$(`${this.packSearchResult} div`)).length;
  }

  async selectProductFromList(page: Page, productInList: number): Promise<string> {
    const numberOfProducts = await this.getNumberOfSearchedProduct(page);

    if (numberOfProducts > 1) {
      await this.waitForSelectorAndClick(page, `${this.packSearchResult} div.search-suggestion:nth-child(${productInList})`);
    } else {
      await this.waitForSelectorAndClick(page, `${this.packSearchResult} div.search-suggestion`);
    }

    return this.elementVisible(page, this.listOfProducts, 1000);
  }

  async getNumberOfProductsInPack(page: Page): Promise<number> {
    return (await page.$$(`${this.listOfProducts} li`)).length;
  }

  async getProductInPackInformation(page: Page, productInList: number): Promise<object> {
    return {
      image: await this.getAttributeContent(page, `#product_stock_packed_products_list li:nth-child(${productInList}) div.packed-product-image img`, 'src'),
      iconDelete: await this.elementVisible(page, `#product_stock_packed_products_list li:nth-child(${productInList}) div.packed-product-legend span i.entity-item-delete`),
      name: await this.getAttributeContent(page, `#product_stock_packed_products_list li:nth-child(${productInList}) #product_stock_packed_products_${productInList - 1}_name `, 'value'),
      reference: await this.getTextContent(page, `#product_stock_packed_products_list li:nth-child(${productInList}) div.packed-product-legend span.reference-preview`),
      quantity: await this.getAttributeContent(page, `#product_stock_packed_products_list li:nth-child(${productInList}) #product_stock_packed_products_${productInList - 1}_quantity`, 'value'),
    };
  }

  async setProductQuantity(page: Page, productInList: number, quantity: number): Promise<void> {
    await this.setValue(page, this.quantityInput(productInList), quantity);
  }

  async saveAndGetProductInPackErrorMessage(page: Page, productInList: number): Promise<string> {
    await this.clickAndWaitForNavigation(page, this.saveProductButton);

    return this.getTextContent(page, `#product_stock_packed_products_list li:nth-child(${productInList}) div.alert-danger p`);
  }

  /**
   * Add product to pack
   * @param page {Page} Browser tab
   * @param product {string} Value of product name to set on input
   * @param quantity {number} Value of quantity to set on input
   * @returns {Promise<void>}
   */
  async addProductToPack(page: Page, product: string, quantity: number): Promise<void> {
    await this.searchProductToPack(page, product);
    await this.waitForSelectorAndClick(page, this.packSearchResult);
    await this.waitForVisibleSelector(page, this.listOfProducts);
    if (quantity) {
      await this.setValue(page, '#product_stock_packed_products_0_quantity', quantity);
    }
  }

  /**
   * Add pack of products
   * @param page {Page} Browser tab
   * @param pack {Object} Data to set on pack form
   * @returns {Promise<void>}
   */
  async addPackOfProducts(page: Page, pack: object): Promise<void> {
    const keys = Object.keys(pack);

    for (let i = 0; i < keys.length; i += 1) {
      await this.addProductToPack(page, keys[i], pack[keys[i]]);
    }
  }

  /**
   * Add combination
   * @param page {Page} Browser tab
   * @param packData {object} Data of the pack
   * @returns {Promise<void>}
   */
  async setPackOfProducts(page: Page, packData: object): Promise<void> {
    await this.waitForSelectorAndClick(page, this.packTabLink);
    await this.addPackOfProducts(page, packData);
  }

  async isDeleteModalVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, '#modal-confirm-remove-entity', 1000);
  }

  async cancelDeleteProductFromPack(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, '#modal-confirm-remove-entity button.btn-outline-secondary');
  }

  async confirmDeleteProductFromPack(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, '#modal-confirm-remove-entity button.btn-confirm-submit');
  }

  async deleteProduct(page: Page, productInList: number, toDelete: boolean): Promise<boolean> {
    await this.waitForSelectorAndClick(page, `#product_stock_packed_products_list li:nth-child(${productInList}) div.packed-product-legend span i.entity-item-delete`);

    await this.waitForVisibleSelector(page, '#modal-confirm-remove-entity');

    if (toDelete) {
      await this.confirmDeleteProductFromPack(page);
      return this.getAlertSuccessBlockParagraphContent(page);
    }
    await this.cancelDeleteProductFromPack(page);
    return this.isDeleteModalVisible(page);
  }

  async editPackOfProducts(page: Page, packData: object): Promise<void> {
    await this.setValue(page, '#product_stock_quantities_delta_quantity_delta', packData.quantity);
    await this.setValue(page, '#product_stock_quantities_minimal_quantity', packData.minimumQuantity);

    switch (packData.packQuantities) {
      case 'Decrement pack only':
        await page.click('#product_stock_pack_stock_type_0 +i');
        break;

      case 'Decrement products in pack only':
        await page.click('#product_stock_pack_stock_type_1 +i');
        break;

      case 'Decrement both':
        await page.click('#product_stock_pack_stock_type_2 +i');
        break;

      case 'Default':
        await page.click('#product_stock_pack_stock_type_3 +i');
        break;

      default:
        throw new Error(`Radio button for ${packData.packQuantities} was not found`);
    }
  }

  async getStockMovement(page: Page, movementLine: number): Promise<object> {
    return {
      dateTime: await this.getTextContent(page, `#product_stock_quantities_stock_movements_${movementLine - 1}_date`),
      employee: await this.getTextContent(page, `#product_stock_quantities_stock_movements_${movementLine - 1}_employee_name`),
      quantity: await this.getNumberFromText(page, `#product_stock_quantities_stock_movements_${movementLine - 1}_delta_quantity`),
    };
  }
}

module.exports = new PackTab();
