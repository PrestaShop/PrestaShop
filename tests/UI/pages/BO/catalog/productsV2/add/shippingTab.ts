// Import pages
import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Shipping tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class ShippingTab extends BOBasePage {
  private readonly productDimensionsWidthInput: string;

  private readonly productDimensionsHeightInput: string;

  private readonly productDimensionsDepthInput: string;

  private readonly productDimensionsWeightInput: string;

  private readonly productDeliveryInStockInput: string;

  private readonly productDeliveryOutStockInput: string;

  private readonly productAdditionalShippingCostInput: string;

  private readonly productDeliveryTimeInput: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Shipping tab
   */
  constructor() {
    super();

    // Selectors in stocks tab
    this.productDimensionsWidthInput = '#product_shipping_dimensions_width';
    this.productDimensionsHeightInput = '#product_shipping_dimensions_height';
    this.productDimensionsDepthInput = '#product_shipping_dimensions_depth';
    this.productDimensionsWeightInput = '#product_shipping_dimensions_weight';
    this.productDeliveryInStockInput = '#product_shipping_delivery_time_notes_in_stock';
    this.productDeliveryOutStockInput = '#product_shipping_delivery_time_notes_out_of_stock';
    this.productAdditionalShippingCostInput = '#product_shipping_additional_shipping_cost';
    this.productDeliveryTimeInput = 'input[name="product[shipping][delivery_time_note_type]"]';
  }

  /*
  Methods
   */

  /**
   * @param page {Page}
   * @param inputName {string}
   * @param languageId {string | undefined}
   */
  async getValue(page: Page, inputName: string, languageId?: string): Promise<string> {
    switch (inputName) {
      case 'additional_delivery_times':
        return this.getAttributeContent(page, `${this.productDeliveryTimeInput}[checked="checked"]`, 'value');
      case 'additional_shipping_cost':
        return this.getAttributeContent(page, this.productAdditionalShippingCostInput, 'value');
      case 'delivery_in_stock':
        return this.getAttributeContent(page, `${this.productDeliveryInStockInput}_${languageId}`, 'value');
      case 'delivery_out_stock':
        return this.getAttributeContent(page, `${this.productDeliveryOutStockInput}_${languageId}`, 'value');
      case 'depth':
        return this.getAttributeContent(page, this.productDimensionsDepthInput, 'value');
      case 'height':
        return this.getAttributeContent(page, this.productDimensionsHeightInput, 'value');
      case 'weight':
        return this.getAttributeContent(page, this.productDimensionsWeightInput, 'value');
      case 'width':
        return this.getAttributeContent(page, this.productDimensionsWidthInput, 'value');
      default:
        throw new Error(`Input ${inputName} was not found`);
    }
  }
}

export default new ShippingTab();
