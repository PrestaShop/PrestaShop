// Import pages
import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';

/**
 * Order confirmation page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class OrderConfirmation extends FOBasePage {
  public readonly pageTitle: string;

  public readonly orderConfirmationCardTitle: string;

  private readonly orderConfirmationCardSection: string;

  private readonly orderConfirmationCardTitleH3: string;

  private readonly orderSummaryContent: string;

  private readonly orderReferenceValue: string;

  private readonly customerSupportLink: string;

  private readonly orderConfirmationTable: string;

  private readonly giftWrappingRow: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on order confirmation page
   */
  constructor() {
    super();

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
  }

  /*
    Methods
     */
  /**
   * Check if final summary is visible
   * @param page {Page} Browser tab
   * @returns {boolean}
   */
  isFinalSummaryVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.orderSummaryContent, 2000);
  }

  /**
   * Get order confirmation card title
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getOrderConfirmationCardTitle(page: Page): Promise<string> {
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
   * Get gift wrapping value
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getGiftWrappingValue(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.giftWrappingRow);
  }
}

export default new OrderConfirmation();
