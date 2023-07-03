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

  public readonly pageTitleEdit: string;

  private readonly nameInput: string;

  private readonly iconInput: string;

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

    this.pageTitleCreate = 'Statuses > Add new •';
    this.pageTitleEdit = 'Statuses > Edit:';

    // Form selectors
    this.nameInput = '#name_1';
    this.iconInput = '#icon';
    this.colorInput = '#color_0';
    this.logableOnCheckbox = '#logable_on';
    this.invoiceOnCheckbox = '#invoice_on';
    this.hiddenOnCheckbox = '#hidden_on';
    this.sendEmailOnCheckbox = '#send_email_on';
    this.pdfInvoiceOnCheckbox = '#pdf_invoice_on';
    this.pdfDeliveryOnCheckbox = '#pdf_delivery_on';
    this.shippedOnCheckbox = '#shipped_on';
    this.paidOnCheckbox = '#paid_on';
    this.deliveryOnCheckbox = '#delivery_on';
    this.saveButton = '#order_state_form_submit_btn';
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

    // Set icon for order status
    await this.uploadFile(page, this.iconInput, `${orderStatusData.name}.jpg`);

    // Set color
    await this.setValue(page, this.colorInput, orderStatusData.color);

    await this.setChecked(page, this.logableOnCheckbox, orderStatusData.logableOn);
    await this.setChecked(page, this.invoiceOnCheckbox, orderStatusData.invoiceOn);
    await this.setChecked(page, this.hiddenOnCheckbox, orderStatusData.hiddenOn);
    await this.setChecked(page, this.sendEmailOnCheckbox, orderStatusData.sendEmailOn);
    await this.setChecked(page, this.pdfInvoiceOnCheckbox, orderStatusData.pdfInvoiceOn);
    await this.setChecked(page, this.pdfDeliveryOnCheckbox, orderStatusData.pdfDeliveryOn);
    await this.setChecked(page, this.shippedOnCheckbox, orderStatusData.shippedOn);
    await this.setChecked(page, this.paidOnCheckbox, orderStatusData.paidOn);
    await this.setChecked(page, this.deliveryOnCheckbox, orderStatusData.deliveryOn);

    // Save order status
    await this.clickAndWaitForURL(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }
}

export default new AddOrderStatus();
