require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add order status page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddOrderStatus extends BOBasePage {
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
  async setOrderStatus(page, orderStatusData) {
    await this.setValue(page, this.nameInput, orderStatusData.name);

    // Set icon for order status
    await this.uploadFile(page, this.iconInput, `${orderStatusData.name}.jpg`);

    // Set color
    await this.setValue(page, this.colorInput, orderStatusData.color);

    await this.changeCheckboxValue(page, this.logableOnCheckbox, orderStatusData.logableOn);
    await this.changeCheckboxValue(page, this.invoiceOnCheckbox, orderStatusData.invoiceOn);
    await this.changeCheckboxValue(page, this.hiddenOnCheckbox, orderStatusData.hiddenOn);
    await this.changeCheckboxValue(page, this.sendEmailOnCheckbox, orderStatusData.sendEmailOn);
    await this.changeCheckboxValue(page, this.pdfInvoiceOnCheckbox, orderStatusData.pdfInvoiceOn);
    await this.changeCheckboxValue(page, this.pdfDeliveryOnCheckbox, orderStatusData.pdfDeliveryOn);
    await this.changeCheckboxValue(page, this.shippedOnCheckbox, orderStatusData.shippedOn);
    await this.changeCheckboxValue(page, this.paidOnCheckbox, orderStatusData.paidOn);
    await this.changeCheckboxValue(page, this.deliveryOnCheckbox, orderStatusData.deliveryOn);

    // Save order status
    await this.clickAndWaitForNavigation(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new AddOrderStatus();
