import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * View order base page, contains functions that can be used on view/edit order page
 * @class
 * @extends BOBasePage
 */
class ViewOrderBasePage extends BOBasePage {
  public readonly pageTitle: string;

  public readonly partialRefundValidationMessage: string;

  public readonly successfulAddProductMessage: string;

  public readonly successfulDeleteProductMessage: string;

  public readonly errorMinimumQuantityMessage: string;

  public readonly errorAddSameProduct: string;

  public readonly errorAddSameProductInInvoice: (invoice: string) => string;

  public readonly noAvailableDocumentsMessage: string;

  public readonly updateSuccessfullMessage: string;

  public readonly commentSuccessfullMessage: string;

  public readonly validationSendMessage: string;

  public readonly errorAssignSameStatus: string;

  public readonly discountMustBeNumberErrorMessage: string;

  public readonly invalidPercentValueErrorMessage: string;

  public readonly percentValueNotPositiveErrorMessage: string;

  public readonly discountCannotExceedTotalErrorMessage: string;

  private readonly orderID: string;

  private readonly orderReference: string;

  private readonly orderStatusesSelect: string;

  private readonly updateStatusButton: string;

  private readonly viewInvoiceButton: string;

  private readonly viewDeliverySlipButton: string;

  private readonly partialRefundButton: string;

  private readonly returnProductsButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on view/edit order page
   */
  constructor() {
    super();

    this.pageTitle = 'Order';
    this.partialRefundValidationMessage = 'A partial refund was successfully created.';
    this.successfulAddProductMessage = 'The product was successfully added.';
    this.successfulDeleteProductMessage = 'The product was successfully removed.';
    this.errorMinimumQuantityMessage = 'Minimum quantity of "3" must be added';
    this.errorAddSameProduct = 'This product is already in your order, please edit the quantity instead.';
    this.errorAddSameProductInInvoice = (invoice: string) => `This product is already in the invoice #${invoice}, `
      + 'please edit the quantity instead.';
    this.noAvailableDocumentsMessage = 'There is no available document';
    this.updateSuccessfullMessage = 'Update successful';
    this.commentSuccessfullMessage = 'Comment successfully added.';
    this.validationSendMessage = 'The message was successfully sent to the customer.';
    this.errorAssignSameStatus = 'The order has already been assigned this status.';
    this.discountMustBeNumberErrorMessage = 'Discount value must be a number.';
    this.invalidPercentValueErrorMessage = 'Percent value cannot exceed 100.';
    this.percentValueNotPositiveErrorMessage = 'Percent value must be greater than 0.';
    this.discountCannotExceedTotalErrorMessage = 'Discount value cannot exceed the total price of this order.';

    // Header selectors
    this.alertBlock = 'div.alert[role=\'alert\'] div.alert-text';
    this.orderID = '.title-content strong[data-role=order-id]';
    this.orderReference = '.title-content strong[data-role=order-reference]';
    this.orderStatusesSelect = '#update_order_status_action_input';
    this.updateStatusButton = '#update_order_status_action_btn';
    this.viewInvoiceButton = 'form.order-actions-invoice a[data-role=view-invoice]';
    this.viewDeliverySlipButton = 'form.order-actions-delivery a[data-role=view-delivery-slip]';
    this.partialRefundButton = 'button.partial-refund-display';
    this.returnProductsButton = '#order-view-page button.return-product-display';
  }

  /*
  Methods
   */
  /**
   * Get order ID
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getOrderID(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.orderID);
  }

  /**
   * Get order reference
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getOrderReference(page: Page): Promise<string> {
    return this.getTextContent(page, this.orderReference);
  }

  // Methods for order actions
  /**
   * Does status exist
   * @param page {Page} Browser tab
   * @param statusName {string} Status to check
   * @returns {Promise<boolean>}
   */
  async doesStatusExist(page: Page, statusName: string): Promise<boolean> {
    const options = await page.$$eval(
      `${this.orderStatusesSelect} option`,
      (all: HTMLElement[]) => all.map((option: HTMLElement) => option.textContent),
    );

    return options.indexOf(statusName) !== -1;
  }

  /**
   * Is update status button disabled
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isUpdateStatusButtonDisabled(page: Page): Promise<boolean> {
    return this.elementVisible(page, `${this.updateStatusButton}[disabled]`, 1000);
  }

  /**
   * Select order status
   * @param page {Page} Browser tab
   * @param status {string} Status to edit
   * @returns {Promise<void>}
   */
  async selectOrderStatus(page: Page, status: string): Promise<void> {
    await this.selectByVisibleText(page, this.orderStatusesSelect, status);
  }

  /**
   * Get order status
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getOrderStatus(page: Page): Promise<string> {
    return this.getTextContent(page, `${this.orderStatusesSelect} option[selected='selected']`, false);
  }

  /**
   * Modify the order status
   * @param page {Page} Browser tab
   * @param status {string} Status to edit
   * @returns {Promise<string>}
   */
  async modifyOrderStatus(page: Page, status: string): Promise<string> {
    const actualStatus = await this.getOrderStatus(page);

    if (status !== actualStatus) {
      await this.selectByVisibleText(page, this.orderStatusesSelect, status);
      await this.clickAndWaitForURL(page, this.updateStatusButton);
      return this.getOrderStatus(page);
    }

    return actualStatus;
  }

  /**
   * Is view invoice button visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isViewInvoiceButtonVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.viewInvoiceButton, 1000);
  }

  /**
   * Click on view invoice button to download the invoice
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async viewInvoice(page: Page): Promise<string | null> {
    return this.clickAndWaitForDownload(page, this.viewInvoiceButton);
  }

  /**
   * Is partial refund button visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isPartialRefundButtonVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.partialRefundButton, 1000);
  }

  /**
   * Click on partial refund button
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnPartialRefund(page: Page): Promise<void> {
    await page.click(this.partialRefundButton);
  }

  /**
   * Is delivery slip button visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isDeliverySlipButtonVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.viewDeliverySlipButton, 1000);
  }

  /**
   * Click on view delivery slip button to download the invoice
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async viewDeliverySlip(page: Page): Promise<string | null> {
    return this.clickAndWaitForDownload(page, this.viewDeliverySlipButton);
  }

  /**
   * Is return products button visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isReturnProductsButtonVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.returnProductsButton, 2000);
  }

  /**
   * Is return product button disabled
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isReturnProductsButtonDisabled(page: Page): Promise<boolean> {
    return this.elementVisible(page, `${this.returnProductsButton}[disabled]`, 2000);
  }

  /**
   * Click on return product button
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnReturnProductsButton(page: Page): Promise<void> {
    await page.locator(this.returnProductsButton).click();
  }
}

const viewOrderBasePage = new ViewOrderBasePage();
export {viewOrderBasePage, ViewOrderBasePage};
