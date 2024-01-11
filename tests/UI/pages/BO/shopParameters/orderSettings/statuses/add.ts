import BOBasePage from '@pages/BO/BObasePage';

import OrderStatusData from '@data/faker/orderStatus';

import type {Page} from 'playwright';

/**
 * Add order status page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddOrderStatus extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: (name: string) => string;

  private readonly nameInput: string;

  private readonly colorInput: string;

  private readonly logableOnCheckbox: string;

  private readonly invoiceOnCheckbox: string;

  private readonly hiddenOnCheckbox: string;

  private readonly sendEmailOnCheckbox: string;

  private readonly pdfInvoiceOnCheckbox: string;

  private readonly pdfDeliveryOnCheckbox: string;

  private readonly shippedOnCheckbox: string;

  private readonly paidOnCheckbox: string;

  private readonly deliveryOnCheckbox: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on add order status page
   */
  constructor() {
    super();

    this.pageTitleCreate = `New order status • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = (name: string) => `Editing order status ${name} • ${global.INSTALL.SHOP_NAME}`;

    // Form selectors
    this.nameInput = '#order_state_name_1';
    this.colorInput = '#order_state_color';
    this.logableOnCheckbox = '#order_state_loggable';
    this.invoiceOnCheckbox = '#order_state_invoice';
    this.hiddenOnCheckbox = '#order_state_hidden';
    this.sendEmailOnCheckbox = '#order_state_send_email';
    this.pdfInvoiceOnCheckbox = '#order_state_pdf_invoice';
    this.pdfDeliveryOnCheckbox = '#order_state_pdf_delivery';
    this.shippedOnCheckbox = '#order_state_shipped';
    this.paidOnCheckbox = '#order_state_paid';
    this.deliveryOnCheckbox = '#order_state_delivery';
    this.saveButton = '#save-button';
  }

  /* Methods */

  /**
   * Fill order status form in create or edit page and save
   * @param page {Page} Browser tab
   * @param orderStatusData {OrderStatusData} Data to set on order status form
   * @return {Promise<string>}
   */
  async setOrderStatus(page: Page, orderStatusData: OrderStatusData): Promise<string> {
    await this.setValue(page, this.nameInput, orderStatusData.name);

    // Set color
    await this.setInputValue(page, this.colorInput, orderStatusData.color);

    await this.setHiddenCheckboxValue(page, this.logableOnCheckbox, orderStatusData.logableOn);
    await this.setHiddenCheckboxValue(page, this.invoiceOnCheckbox, orderStatusData.invoiceOn);
    await this.setHiddenCheckboxValue(page, this.hiddenOnCheckbox, orderStatusData.hiddenOn);
    await this.setHiddenCheckboxValue(page, this.sendEmailOnCheckbox, orderStatusData.sendEmailOn);
    await this.setHiddenCheckboxValue(page, this.pdfInvoiceOnCheckbox, orderStatusData.pdfInvoiceOn);
    await this.setHiddenCheckboxValue(page, this.pdfDeliveryOnCheckbox, orderStatusData.pdfDeliveryOn);
    await this.setHiddenCheckboxValue(page, this.shippedOnCheckbox, orderStatusData.shippedOn);
    await this.setHiddenCheckboxValue(page, this.paidOnCheckbox, orderStatusData.paidOn);
    await this.setHiddenCheckboxValue(page, this.deliveryOnCheckbox, orderStatusData.deliveryOn);

    // Save order status
    await this.clickAndWaitForURL(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddOrderStatus();
