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
    return this.getTextContent(page, '#content-wrapper div:nth-child(2) div.card-body');
  }

  /**
   * Get order details
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getOrderDetails(page: Page): Promise<string> {
    return this.getTextContent(page, 'div.order-confirmation__details ul.order-details');
  }

  async getNumberOfProducts(page: Page): Promise<number> {
    return page.locator('div.order-confirmation__items div.item').count();
  }

  async getProductDetailsInRow(page: Page, row: number): Promise<ProductOrderConfirmation> {
    return {
      image: await this.getAttributeContent(page, `div.order-confirmation__items div.item:nth-child(${row}) div.item__image img`, 'srcset'),
      details: await this.getTextContent(page, `div.order-confirmation__items div.item:nth-child(${row}) div.item__details`),
      prices: await this.getTextContent(page, `div.order-confirmation__items div.item:nth-child(${row}) div.item__prices`),
    };
  }
}

const orderConfirmationPage = new OrderConfirmationPage();
export {orderConfirmationPage, OrderConfirmationPage};
