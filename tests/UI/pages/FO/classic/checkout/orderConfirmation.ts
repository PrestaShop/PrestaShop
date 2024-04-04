// Import pages
import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';
import type {ProductOrderConfirmation} from '@data/types/product';

/**
 * Order confirmation page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class OrderConfirmationPage extends FOBasePage {
  public readonly pageTitle: string;

  public readonly orderConfirmationCardTitle: string;

  protected orderConfirmationCardSection: string;

  protected orderConfirmationCardTitleH3: string;

  private readonly orderSummaryContent: string;

  protected orderReferenceValue: string;

  protected customerSupportLink: string;

  private readonly orderConfirmationTable: string;

  protected paymentInformationBody: string;

  protected orderDetails: string;

  protected productRow: string;

  protected customizationButton: string;

  protected customizationModal: string;

  protected customizationModalBody: string;

  protected customizationModalCloseButton: string;

  protected productRowNth: (row: number) => string;

  protected productRowImage: (row: number) => string;

  protected productRowDetails: (row: number) => string;

  protected productRowPrices: (row: number) => string;

  private readonly giftWrappingRow: string;

  protected orderDetailsTable: string;

  protected paymentMethodRow: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on order confirmation page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Order confirmation';
    this.orderConfirmationCardTitle = 'Your order is confirmed';

    // Selectors
    this.orderConfirmationCardSection = '#content-hook_order_confirmation';
    this.orderConfirmationCardTitleH3 = `${this.orderConfirmationCardSection} h3.card-title`;
    this.orderSummaryContent = '#order-summary-content';
    this.orderReferenceValue = '#order-reference-value';
    this.customerSupportLink = '#content-hook_payment_return a';
    this.orderConfirmationTable = 'div.order-confirmation-table';
    this.giftWrappingRow = `${this.orderConfirmationTable} tr:nth-child(3)`;
    this.orderDetailsTable = 'div#order-details';
    this.paymentMethodRow = `${this.orderDetailsTable} li:nth-child(2)`;
    this.paymentInformationBody = '#content-hook_payment_return';
    this.orderDetails = 'div#order-details ul';
    this.productRow = `${this.orderConfirmationTable} div.order-line`;
    this.customizationButton = `${this.productRow} div.customizations a`;
    this.customizationModal = 'div[id*="product-customizations-modal"]';
    this.customizationModalBody = `${this.customizationModal} div.modal-body`;
    this.customizationModalCloseButton = `${this.customizationModal} div.modal-header button`;
    this.productRowNth = (row: number) => `${this.productRow}:nth-child(${row})`;
    this.productRowImage = (row: number) => `${this.productRowNth(row)} span.image img`;
    this.productRowDetails = (row: number) => `${this.productRowNth(row)} div.details`;
    this.productRowPrices = (row: number) => `${this.productRowNth(row)} div.qty div`;
  }

  /*
    Methods
     */
  /**
   * Check if final summary is visible
   * @param page {Page} Browser tab
   * @returns {boolean}
   */
  async isFinalSummaryVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.orderSummaryContent, 2000);
  }

  /**
   * Get order confirmation card title
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getOrderConfirmationCardTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.orderConfirmationCardTitleH3);
  }

  /**
   * Get and return the order reference value
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getOrderReferenceValue(page: Page): Promise<string> {
    const orderRefText = await this.getTextContent(page, this.orderReferenceValue);

    return (orderRefText.split(':'))[1].trim();
  }

  /**
   * Click on the 'customer support' link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToContactUsPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.customerSupportLink);
  }

  /**
   * Return the payment method
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPaymentMethod(page: Page): Promise<string> {
    const text = await this.getTextContent(page, this.paymentMethodRow);

    return (text.split(':'))[1].trim();
  }

  /**
   * Get gift wrapping value
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getGiftWrappingValue(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.giftWrappingRow);
  }

  /**
   * Get payment information
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPaymentInformation(page: Page): Promise<string> {
    return this.getTextContent(page, this.paymentInformationBody);
  }

  /**
   * Get order details
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getOrderDetails(page: Page): Promise<string> {
    return this.getTextContent(page, this.orderDetails);
  }

  /**
   * Get number of products
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getNumberOfProducts(page: Page): Promise<number> {
    return page.locator(this.productRow).count();
  }

  /**
   * Get product details in row
   * @param page {Page} Browser tab
   * @param row {number} Row of product
   * @returns {Promise<ProductOrderConfirmation>}
   */
  async getProductDetailsInRow(page: Page, row: number): Promise<ProductOrderConfirmation> {
    return {
      image: await this.getAttributeContent(page, this.productRowImage(row), 'src'),
      details: await this.getTextContent(page, this.productRowDetails(row)),
      prices: await this.getTextContent(page, this.productRowPrices(row)),
    };
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
}

const orderConfirmationPage = new OrderConfirmationPage();
export {orderConfirmationPage, OrderConfirmationPage};
