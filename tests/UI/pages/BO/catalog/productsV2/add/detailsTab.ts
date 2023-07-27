import type {Page} from 'playwright';

import type ProductData from '@data/faker/product';

// Import pages
import BOBasePage from '@pages/BO/BObasePage';

/**
 * Details tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class DetailsTab extends BOBasePage {
  private readonly detailsTabLink: string;

  private readonly productEAN13Input: string;

  private readonly productISBNInput: string;

  private readonly productMPNInput: string;

  private readonly productReferenceInput: string;

  private readonly productUPCInput: string;

  private readonly productShowCondition: string;

  private readonly productConditionInput: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on details tab
   */
  constructor() {
    super();

    // Selectors in details tab
    this.detailsTabLink = '#product_details-tab-nav';
    this.productEAN13Input = '#product_details_references_ean_13';
    this.productISBNInput = '#product_details_references_isbn';
    this.productMPNInput = '#product_details_references_mpn';
    this.productReferenceInput = '#product_details_references_reference';
    this.productUPCInput = '#product_details_references_upc';
    this.productShowCondition = '#product_details_show_condition_1';
    this.productConditionInput = '#product_details_condition';
  }

  /*
  Methods
   */

  /**
   * Set product details
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in details form
   * @returns {Promise<void>}
   */
  async setProductDetails(page: Page, productData: ProductData): Promise<void> {
    await this.waitForSelectorAndClick(page, this.detailsTabLink);
    await this.setValue(page, this.productReferenceInput, productData.reference);
  }

  /**
   * @param page {Page}
   * @param inputName {string}
   */
  async getValue(page: Page, inputName: string): Promise<string> {
    switch (inputName) {
      case 'condition':
        return page.$eval(this.productConditionInput, (node: HTMLSelectElement) => node.value);
      case 'mpn':
        return this.getAttributeContent(page, this.productMPNInput, 'value');
      case 'reference':
        return this.getAttributeContent(page, this.productReferenceInput, 'value');
      case 'upc':
        return this.getAttributeContent(page, this.productUPCInput, 'value');
      case 'ean13':
        return this.getAttributeContent(page, this.productEAN13Input, 'value');
      case 'isbn':
        return this.getAttributeContent(page, this.productISBNInput, 'value');
      case 'show_condition':
        return (await this.isChecked(page, this.productShowCondition)) ? '1' : '0';
      default:
        throw new Error(`Input ${inputName} was not found`);
    }
  }
}

export default new DetailsTab();
