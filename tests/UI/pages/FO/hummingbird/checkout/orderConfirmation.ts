import type {ProductOrderConfirmation} from '@data/types/product';
import {OrderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';
import type {Page} from 'playwright';

/**
 * Cart page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class OrderConfirmation extends OrderConfirmationPage {
  private readonly customizationButton: string;

  private readonly customizationModal: string;

  private readonly customizationModalBody: string;

  private readonly customizationModalCloseButton: string;

  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');

    // Selectors
    this.orderConfirmationCardSection = '#content-wrapper .alert';
    this.orderConfirmationCardTitleH3 = `${this.orderConfirmationCardSection} h1.alert-heading`;
    this.orderDetailsTable = 'div.order-confirmation__details ul.order-details';
    this.orderReferenceValue = `${this.orderDetailsTable} li:nth-child(1)`;
    this.customerSupportLink = 'div.card .card-footer a.alert-link';
    this.paymentMethodRow = `${this.orderDetailsTable} li:nth-child(2)`;
    this.paymentInformationBody = '#content-wrapper div:nth-child(2) div.card-body';
    this.orderDetails = 'div.order-confirmation__details ul.order-details';
    this.customizationButton = '#content-wrapper div.order-confirmation__details div.order-confirmation__items button';
    this.customizationModal = 'div[id*="product-customization-modal"]';
    this.customizationModalBody = 'div[id*="product-customization-modal"] div.modal-body';
    this.customizationModalCloseButton = 'div[id*="product-customization-modal"] div.modal-header button';
    this.productRow = (row: number) => `div.order-confirmation__items div.item:nth-child(${row})`;
    this.productRowImage = (row: number) => `${this.productRow(row)} div.item__image img`;
    this.productRowDetails = (row: number) => `${this.productRow(row)} div.item__details`;
    this.productRowPrices = (row: number) => `${this.productRow(row)} div.item__prices`;
  }

  /**
   * Click on customized button
   * @param page{Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnCustomizedButton(page: Page): Promise<boolean> {
    await page.locator(this.customizationButton).first().click();

    return this.elementVisible(page, this.customizationModal, 2000);
  }

  /**
   * Get modal product customization content
   * @param page{Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async getModalProductCustomizationContent(page: Page): Promise<string> {
    return this.getTextContent(page, this.customizationModalBody);
  }

  /**
   * Close modal product customization
   * @param page{Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeModalProductCustomization(page: Page): Promise<boolean> {
    await page.locator(this.customizationModalCloseButton).click();

    return this.elementNotVisible(page, this.customizationModal, 2000);
  }

  /**
   * Get product details in row
   * @param page {Page} Browser tab
   * @param row {number} Row of product
   * @returns {Promise<ProductOrderConfirmation>}
   */
  async getProductDetailsInRow(page: Page, row: number): Promise<ProductOrderConfirmation> {
    return {
      image: await this.getAttributeContent(page, this.productRowImage(row), 'srcset'),
      details: await this.getTextContent(page, this.productRowDetails(row)),
      prices: await this.getTextContent(page, this.productRowPrices(row)),
    };
  }
}

export default new OrderConfirmation();
