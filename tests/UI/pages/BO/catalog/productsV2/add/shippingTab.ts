// Import pages
import BOBasePage from '@pages/BO/BObasePage';
import type {Page} from 'playwright';

// Import data
import ProductData from '@data/faker/product';

/**
 * Shipping tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class ShippingTab extends BOBasePage {
  private readonly shippingTabLink: string;

  private readonly productDimensionsWidthInput: string;

  private readonly productDimensionsHeightInput: string;

  private readonly productDimensionsDepthInput: string;

  private readonly productDimensionsWeightInput: string;

  private readonly productDeliveryInStockInput: string;

  private readonly productDeliveryOutStockInput: string;

  private readonly productAdditionalShippingCostInput: string;

  private readonly productDeliveryTimeInput: string;

  private readonly deliveryTimeInStockProducts: string;

  private readonly deliveryTimeOutOfStockProducts: string;

  private readonly editDeliveryTimeLink: string;

  private readonly allCarriersSelect: string;

  private readonly availableCarrierCheckboxButton: (carrierID: number) => string;

  private readonly deliveryTimeType: (type: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Shipping tab
   */
  constructor() {
    super();

    // Selectors in Shipping tab
    this.shippingTabLink = '#product_shipping-tab-nav';
    // Package dimension section
    this.productDimensionsWidthInput = '#product_shipping_dimensions_width';
    this.productDimensionsHeightInput = '#product_shipping_dimensions_height';
    this.productDimensionsDepthInput = '#product_shipping_dimensions_depth';
    this.productDimensionsWeightInput = '#product_shipping_dimensions_weight';
    this.productDeliveryInStockInput = '#product_shipping_delivery_time_notes_in_stock';
    this.productDeliveryOutStockInput = '#product_shipping_delivery_time_notes_out_of_stock';
    // Delivery time section
    this.deliveryTimeType = (type: number) => `#product_shipping_delivery_time_note_type_${type}`;
    this.deliveryTimeInStockProducts = '#product_shipping_delivery_time_notes_in_stock_1';
    this.deliveryTimeOutOfStockProducts = '#product_shipping_delivery_time_notes_out_of_stock_1';
    this.editDeliveryTimeLink = '#product_shipping_delivery_time_note_type label a[href*="configure/shop/product-preferences"]';
    // Shipping fees section
    this.productAdditionalShippingCostInput = '#product_shipping_additional_shipping_cost';
    this.productDeliveryTimeInput = 'input[name="product[shipping][delivery_time_note_type]"]';
    this.allCarriersSelect = '#carrier-checkboxes-dropdown button';
    this.availableCarrierCheckboxButton = (carrierID: number) => '#carrier-checkboxes-dropdown'
      + ` div:nth-child(${carrierID}).md-checkbox label div`;
  }

  /*
  Methods
   */
  /**
   * Set package dimension
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in package dimension form
   * @returns {Promise<void>}
   */
  async setPackageDimension(page: Page, productData: ProductData): Promise<void> {
    await this.waitForSelectorAndClick(page, this.shippingTabLink);
    await this.setValue(page, this.productDimensionsWidthInput, productData.packageDimensionWidth);
    await this.setValue(page, this.productDimensionsHeightInput, productData.packageDimensionHeight);
    await this.setValue(page, this.productDimensionsDepthInput, productData.packageDimensionDepth);
    await this.setValue(page, this.productDimensionsWeightInput, productData.packageDimensionWeight);
  }

  /**
   * Set delivery time
   * @param page {Page} Browser tab
   * @param deliveryTime {string} Delivery time value to check
   * @returns {Promise<void>}
   */
  async setDeliveryTime(page: Page, deliveryTime: string): Promise<void> {
    switch (deliveryTime) {
      case 'None':
        await this.setChecked(page, this.deliveryTimeType(0));
        break;
      case 'Default delivery time':
        await this.setChecked(page, this.deliveryTimeType(1));
        break;
      case 'Specific delivery time':
        await this.setChecked(page, this.deliveryTimeType(2));
        break;
      default:
        throw new Error(`Button ${deliveryTime} was not found`);
    }
  }

  /**
   * Set delivery time in stock
   * @param page {Page} Browser tab
   * @param numberOfDays {string} Number of days of delivery
   * @returns {Promise<void>}
   */
  async setDeliveryTimeInStockProducts(page: Page, numberOfDays: string): Promise<void> {
    await this.setValue(page, this.deliveryTimeInStockProducts, numberOfDays);
  }

  /**
   * Set delivery time out of stock
   * @param page {Page} Browser tab
   * @param numberOfDays {string} Number of days of delivery
   * @returns {Promise<void>}
   */
  async setDeliveryTimeOutOfStockProducts(page: Page, numberOfDays: string): Promise<void> {
    await this.setValue(page, this.deliveryTimeOutOfStockProducts, numberOfDays);
  }

  /**
   * Click on edit delivery time link
   * @param page {Page} Browser tab
   * @returns {Promise<Page>}
   */
  async clickOnEditDeliveryTimeLink(page: Page): Promise<Page> {
    return this.openLinkWithTargetBlank(page, this.editDeliveryTimeLink);
  }

  /**
   * Set additional shipping costs
   * @param page {Page} Browser tab
   * @param shippingCosts {number} Shipping cost
   * @returns {Promise<void>}
   */
  async setAdditionalShippingCosts(page: Page, shippingCosts: number): Promise<void> {
    await this.waitForSelectorAndClick(page, this.shippingTabLink);
    await this.setValue(page, this.productAdditionalShippingCostInput, shippingCosts);
  }

  /**
   * Select available carrier
   * @param page {Page} Browser tab
   * @param carrier {string} Carrier to choose
   * @returns {Promise<void>}
   */
  async selectAvailableCarrier(page: Page, carrier: string): Promise<void> {
    await this.waitForSelectorAndClick(page, this.allCarriersSelect);
    switch (carrier) {
      case 'Click and collect':
        await this.setChecked(page, this.availableCarrierCheckboxButton(1));
        break;
      case 'My cheap carrier':
        await this.setChecked(page, this.availableCarrierCheckboxButton(2));
        break;
      case 'My carrier':
        await this.setChecked(page, this.availableCarrierCheckboxButton(3));
        break;
      case 'My light carrier':
        await this.setChecked(page, this.availableCarrierCheckboxButton(4));
        break;
      default:
        throw new Error(`${carrier} was not found`);
    }
  }

  /**
   * Returns the value of a form element
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
