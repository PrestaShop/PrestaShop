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

  private readonly productReferenceInput: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on details tab
   */
  constructor() {
    super();

    // Selectors in details tab
    this.detailsTabLink = '#product_details-tab-nav';
    this.productReferenceInput = '#product_details_references_reference';
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
}

export default new DetailsTab();
