require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

// Needed to create customer in orders page
const addAddressPage = require('@pages/BO/customers/addresses/add');

/**
 * Add order page, contains functions that can be used on view/edit order page
 * @class
 * @extends BOBasePage
 */
class Order extends BOBasePage {
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
    this.noAvailableDocumentsMessage = 'There is no available document';
    this.updateSuccessfullMessage = 'Update successful';
    this.validationSendMessage = 'The message was successfully sent to the customer.';
    this.errorAssignSameStatus = 'The order has already been assigned this status.';
    this.discountMustBeNumberErrorMessage = 'Discount value must be a number.';
    this.invalidPercentValueErrorMessage = 'Percent value cannot exceed 100.';
    this.percentValueNotPositiveErrorMessage = 'Percent value must be greater than 0.';
    this.discountCannotExceedTotalErrorMessage = 'Discount value cannot exceed the total price of this order.';
    // Selectors
    this.alertBlock = 'div.alert[role=\'alert\'] div.alert-text';

    // Order actions selectors
    this.orderStatusesSelect = '#update_order_status_action_input';
    this.updateStatusButton = '#update_order_status_action_btn';
    this.viewInvoiceButton = 'form.order-actions-invoice a[data-role=\'view-invoice\']';
    this.viewDeliverySlipButton = 'form.order-actions-delivery a[data-role=\'view-delivery-slip\']';
    this.partialRefundButton = 'button.partial-refund-display';

    // Customer block
    this.customerInfoBlock = '#customerInfo';
    this.ViewAllDetailsLink = '#viewFullDetails a';
    this.customerEmailLink = '#customerEmail a';
    this.validatedOrders = '#validatedOrders span.badge';
    this.shippingAddressBlock = '#addressShipping';
    this.shippingAddressToolTipLink = `${this.shippingAddressBlock} .tooltip-link`;
    this.editShippingAddressButton = '#js-delivery-address-edit-btn';
    this.selectAnotherShippingAddressButton = `${this.shippingAddressBlock} .js-update-customer-address-modal-btn`;
    this.changeOrderAddressSelect = '#change_order_address_new_address_id';
    this.submitAnotherAddressButton = '#change-address-submit-button';
    this.editAddressIframe = 'iframe.fancybox-iframe';
    this.invoiceAddressBlock = '#addressInvoice';
    this.invoiceAddressToolTipLink = `${this.invoiceAddressBlock} .tooltip-link`;
    this.editInvoiceAddressButton = '#js-invoice-address-edit-btn';
    this.selectAnotherInvoiceAddressButton = `${this.invoiceAddressBlock} .js-update-customer-address-modal-btn`;
    this.privateNoteDiv = '#privateNote';
    this.privateNoteTextarea = '#private_note_note';
    this.addNewPrivateNoteLink = '#privateNote a.js-private-note-toggle-btn';
    this.privateNoteSaveButton = `${this.privateNoteDiv} .js-private-note-btn`;

    // Products block
    this.productsCountSpan = '#orderProductsPanelCount';
    this.orderProductsTableProductName = row => `${this.orderProductsTableNameColumn(row)} p.productName`;
    this.orderProductsTableProductReference = row => `${this.orderProductsTableNameColumn(row)} p.productReference`;
    this.orderProductsTableProductBasePrice = row => `${this.orderProductsRowTable(row)} td.cellProductUnitPrice`;
    this.orderProductsTableProductQuantity = row => `${this.orderProductsRowTable(row)} td.cellProductQuantity`;
    this.orderProductsTableProductAvailable = row => `${this.orderProductsRowTable(row)}
     td.cellProductAvailableQuantity`;
    this.orderProductsTableProductPrice = row => `${this.orderProductsRowTable(row)} td.cellProductTotalPrice`;
    this.deleteProductButton = row => `${this.orderProductsRowTable(row)} button.js-order-product-delete-btn`;

    // Pagination selectors
    this.paginationLimitSelect = '#orderProductsTablePaginationNumberSelector';
    this.paginationLabel = '#orderProductsNavPagination .page-item.active';
    this.paginationNextLink = '#orderProductsTablePaginationNext';
    this.paginationPreviousLink = '#orderProductsTablePaginationPrev';

    // Order block
    this.orderProductsTable = '#orderProductsTable';
    this.orderProductsRowTable = row => `${this.orderProductsTable} tbody tr:nth-child(${row})`;
    this.orderProductsTableNameColumn = row => `${this.orderProductsRowTable(row)} td.cellProductName`;
    this.orderProductsTableNameNameParagraph = row => `${this.orderProductsTableNameColumn(row)} p.productName`;
    this.editProductButton = row => `${this.orderProductsRowTable(row)} button.js-order-product-edit-btn`;
    this.productQuantitySpan = row => `${this.orderProductsRowTable(row)} td.cellProductQuantity span`;
    this.orderProductsEditRowTable = `${this.orderProductsTable} tbody tr.editProductRow`;
    this.editProductQuantityInput = `${this.orderProductsEditRowTable} input.editProductQuantity`;
    this.editProductPriceInput = `${this.orderProductsEditRowTable} input.editProductPriceTaxIncl`;
    this.UpdateProductButton = `${this.orderProductsEditRowTable} button.productEditSaveBtn`;
    this.orderTotalPriceSpan = '#orderTotal';
    this.orderTotalDiscountsSpan = '#orderDiscountsTotal';
    this.returnProductsButton = '#order-view-page button.return-product-display';
    this.addProductTableRow = '#addProductTableRow';
    this.addProductButton = '#addProductBtn';
    this.addProductRowSearch = '#add_product_row_search';
    this.addProductRowQuantity = '#add_product_row_quantity';
    this.addProductRowStockLocation = '#addProductLocation';
    this.addProductAvailable = '#addProductAvailable';
    this.addProductTotalPrice = '#addProductTotalPrice';
    this.addProductInvoiceSelect = '#add_product_row_invoice';
    this.addProductAddButton = '#add_product_row_add';
    this.addProductCancelButton = '#add_product_row_cancel';
    this.addProductModalConfirmNewInvoice = '#modal-confirm-new-invoice';
    this.addProductCreateNewInvoiceButton = `${this.addProductModalConfirmNewInvoice} .btn-confirm-submit`;
    this.addDiscountButton = 'button[data-target=\'#addOrderDiscountModal\']';
    this.orderDiscountModal = '#addOrderDiscountModal';
    this.addOrderCartRuleNameInput = '#add_order_cart_rule_name';
    this.addOrderCartRuleTypeSelect = '#add_order_cart_rule_type';
    this.addOrderCartRuleValueInput = '#add_order_cart_rule_value';
    this.addOrderCartRuleAddButton = '#add_order_cart_rule_submit';
    this.discountListTable = 'table.table.discountList';
    this.discountListRowTable = row => `${this.discountListTable} tbody tr:nth-child(${row})`;
    this.discountListNameColumn = row => `${this.discountListRowTable(row)} td.discountList-name`;
    this.discountListDiscountColumn = row => `${this.discountListRowTable(row)} td[data-role='discountList-value']`;
    this.discountDeleteIcon = row => `${this.discountListRowTable(row)} a.delete-cart-rule`;

    // Status tab
    this.historyTabContent = '#historyTabContent';
    this.secondOrderStatusesSelect = '#update_order_status_new_order_status_id';
    this.secondUpdateStatusButton = `${this.historyTabContent} .card-details-form button.btn-primary`;
    this.statusGridTable = 'table[data-role=\'history-grid-table\']';
    this.statusTableBody = `${this.statusGridTable} tbody`;
    this.statusTableRow = row => `${this.statusTableBody} tr:nth-child(${row})`;
    this.statusTableColumn = (row, column) => `${this.statusTableRow(row)} td[data-role='${column}-column']`;
    this.resendEmailButton = row => `${this.statusTableRow(row)} td form[action*='resend-email'] button`;
    this.orderNoteOpenButton = `${this.historyTabContent} a.js-order-notes-toggle-btn`;
    this.orderNoteCloseButton = `${this.orderNoteOpenButton}.is-opened`;
    this.orderNoteTextarea = '#internal_note_note';
    this.orderNoteSaveButton = 'button.js-order-notes-btn';

    // Documents tab
    this.documentTab = 'a#orderDocumentsTab';
    this.orderDocumentTabContent = '#orderDocumentsTabContent';
    this.generateInvoiceButton = `${this.orderDocumentTabContent} .btn.btn-primary`;
    this.documentsTablegrid = '#documents-grid-table';
    this.documentsTableBody = `${this.documentsTablegrid} tbody`;
    this.documentsTableRow = row => `${this.documentsTableBody} tr:nth-child(${row})`;
    this.documentsTableColumn = (row, column) => `${this.documentsTableRow(row)} td.${column}`;
    this.documentNumberLink = row => `${this.documentsTableRow(row)} td.documents-table-column-download-link a`;
    this.documentType = row => `${this.documentsTableRow(row)} td.documents-table-column-type`;
    this.addDocumentNoteButton = row => `${this.documentsTableRow(row)} td button.js-open-invoice-note-btn`;
    this.documentNoteInput = row => `${this.documentsTableRow(row)} td input.invoice-note`;
    this.documentNoteSaveButton = row => `${this.documentsTableRow(row)} td button.js-save-invoice-note-btn`;
    this.editDocumentNoteButton = row => `${this.documentsTableRow(row)} td button.btn-edit`;
    this.enterPaymentButton = row => `${this.documentsTableRow(row)} td button.js-enter-payment-btn`;

    // Payment block
    this.orderPaymentsBlock = '#view_order_payments_block';
    this.orderPaymentsTitle = `${this.orderPaymentsBlock} .card-header-title`;
    this.paymentDateInput = '#order_payment_date';
    this.paymentMethodInput = '#order_payment_payment_method';
    this.transactionIDInput = '#order_payment_transaction_id';
    this.paymentAmountInput = '#order_payment_amount';
    this.paymentCurrencySelect = '#order_payment_id_currency';
    this.paymentInvoiceSelect = '#order_payment_id_invoice';
    this.paymentAddButton = `${this.orderPaymentsBlock} .btn.btn-primary.btn-sm`;
    this.paymentWarning = `${this.orderPaymentsBlock} .alert-danger`;
    this.paymentsGridTable = 'table[data-role=\'payments-grid-table\']';
    this.paymentsTableBody = `${this.paymentsGridTable} tbody`;
    this.paymentsTableRow = row => `${this.paymentsTableBody} tr:nth-child(${row})`;
    this.paymentsTableColumn = (row, column) => `${this.paymentsTableRow(row)} td[data-role='${column}-column']`;
    this.paymentsTableDetailsButton = row => `${this.paymentsTableRow(row)} button.js-payment-details-btn`;
    this.paymentTableRowDetails = row => `${this.paymentsTableRow(row)}[data-role='payment-details']`;

    // Carriers tab
    this.carriersTab = '#orderShippingTab';
    this.orderShippingTabContent = '#orderShippingTabContent';
    this.carriersGridTable = '#shipping-grid-table';
    this.carriersTableBody = `${this.carriersGridTable} tbody`;
    this.carriersTableRow = row => `${this.carriersTableBody} tr:nth-child(${row})`;
    this.carriersTableColumn = (row, column) => `${this.carriersTableRow(row)} td.${column}`;
    this.editLink = `${this.orderShippingTabContent} a.js-update-shipping-btn`;
    this.updateOrderShippingModal = '#updateOrderShippingModal';
    this.updateOrderShippingModalDialog = `${this.updateOrderShippingModal} div.modal-dialog`;
    this.trackingNumberInput = `${this.updateOrderShippingModalDialog} #update_order_shipping_tracking_number`;
    this.carrierSelect = `${this.updateOrderShippingModalDialog} #update_order_shipping_new_carrier_id`;
    this.updateCarrierButton = `${this.updateOrderShippingModalDialog} button.btn-primary`;

    // Merchandise returns tab
    this.merchandiseReturnsTab = '#orderReturnsTab';
    this.merchandisereturnCount = `${this.merchandiseReturnsTab} span[data-role='count']`;
    this.merchandiseReturnsGridTable = 'table[data-role=\'merchandise-returns-grid-table\']';
    this.merchandiseReturnsTableBody = `${this.merchandiseReturnsGridTable} tbody`;
    this.merchandiseReturnsTableRow = row => `${this.merchandiseReturnsTableBody} tr:nth-child(${row})`;
    this.merchandiseReturnsTableColumn = (row, column) => `${this.merchandiseReturnsTableRow(row)}`
      + ` td[data-role='merchandise-${column}']`;

    // Refund form
    this.refundProductQuantity = row => `${this.orderProductsRowTable(row)} input[id*='cancel_product_quantity']`;
    this.refundProductAmount = row => `${this.orderProductsRowTable(row)} input[id*='cancel_product_amount']`;
    this.refundShippingCost = row => `${this.orderProductsRowTable(row)} input[id*='cancel_product_shipping_amount']`;
    this.partialRefundSubmitButton = 'button#cancel_product_save';
  }

  /*
  Methods
   */

  // Methods for order actions
  /**
   * Is update status button disabled
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isUpdateStatusButtonDisabled(page) {
    return this.elementVisible(page, `${this.updateStatusButton},disabled`, 1000);
  }

  /**
   * Is view invoice button visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isViewInvoiceButtonVisible(page) {
    return this.elementVisible(page, this.viewInvoiceButton, 1000);
  }

  /**
   * Is partial refund button visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isPartialRefundButtonVisible(page) {
    return this.elementVisible(page, this.partialRefundButton, 1000);
  }

  /**
   * Is delivery slip button visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isDeliverySlipButtonVisible(page) {
    return this.elementVisible(page, this.viewDeliverySlipButton, 1000);
  }

  /**
   * Select order status
   * @param page {Page} Browser tab
   * @param status {string} Status to edit
   * @returns {Promise<void>}
   */
  async selectOrderStatus(page, status) {
    await this.selectByVisibleText(page, this.orderStatusesSelect, status);
  }

  /**
   * Get order status
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getOrderStatus(page) {
    return this.getTextContent(page, `${this.orderStatusesSelect} option[selected='selected']`, false);
  }

  /**
   * Modify the order status from the page header
   * @param page {Page} Browser tab
   * @param status {string} Status to edit
   * @returns {Promise<string>}
   */
  async modifyOrderStatus(page, status) {
    const actualStatus = await this.getOrderStatus(page);

    if (status !== actualStatus) {
      await this.selectByVisibleText(page, this.orderStatusesSelect, status);
      await this.clickAndWaitForNavigation(page, this.updateStatusButton);
      return this.getOrderStatus(page);
    }

    return actualStatus;
  }

  /**
   * Does status exist
   * @param page {Page} Browser tab
   * @param statusName {string} Status to check
   * @returns {Promise<boolean>}
   */
  async doesStatusExist(page, statusName) {
    const options = await page.$$eval(
      `${this.orderStatusesSelect} option`,
      all => all.map(option => option.textContent),
    );

    return options.indexOf(statusName) !== -1;
  }

  /**
   * Click on view invoice button to download the invoice
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async viewInvoice(page) {
    return this.clickAndWaitForDownload(page, this.viewInvoiceButton);
  }

  /**
   * Click on view delivery slip button to download the invoice
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async viewDeliverySlip(page) {
    return this.clickAndWaitForDownload(page, this.viewDeliverySlipButton);
  }

  /**
   * Click on partial refund button
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnPartialRefund(page) {
    await page.click(this.partialRefundButton);
    await this.waitForVisibleSelector(page, this.refundProductQuantity(1));
  }

  /**
   * Add partial refund product
   * @param page {Page} Browser tab
   * @param productRow {number} Product row on table
   * @param quantity {number} Quantity value to set
   * @param amount {number} Amount value to set
   * @param shipping {number} Shipping cost to set
   * @returns {Promise<string>}
   */
  async addPartialRefundProduct(page, productRow, quantity = 0, amount = 0, shipping = 0) {
    await this.setValue(page, this.refundProductQuantity(productRow), quantity.toString());
    if (amount !== 0) {
      await this.setValue(page, this.refundProductAmount(productRow), amount.toString());
    }
    if (shipping !== 0) {
      await this.setValue(page, this.refundShippingCost(productRow), shipping.toString());
    }
    await this.clickAndWaitForNavigation(page, this.partialRefundSubmitButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Is return products button visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isReturnProductsButtonVisible(page) {
    return this.elementVisible(page, this.returnProductsButton, 2000);
  }

  // Methods for customer block
  /**
   * Get customer information
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getCustomerInfoBlock(page) {
    return this.getTextContent(page, this.customerInfoBlock);
  }

  /**
   * Go to view full details page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToViewFullDetails(page) {
    await this.clickAndWaitForNavigation(page, this.ViewAllDetailsLink);
  }

  /**
   * Get customer email
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getCustomerEmail(page) {
    return this.getAttributeContent(page, this.customerEmailLink, 'href');
  }

  /**
   * Get shipping address from customer card
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getShippingAddress(page) {
    return this.getTextContent(page, this.shippingAddressBlock);
  }

  /**
   * Get invoice address from customer card
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getInvoiceAddress(page) {
    return this.getTextContent(page, this.invoiceAddressBlock);
  }

  /**
   * Get validated orders number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getValidatedOrdersNumber(page) {
    return this.getNumberFromText(page, `${this.validatedOrders}.badge-dark`);
  }

  /**
   * Edit existing shipping address
   * @param page {Page} Browser tab
   * @param addressData {AddressData} Shipping address data to edit
   * @returns {Promise<void>}
   */
  async editExistingShippingAddress(page, addressData) {
    await this.waitForSelectorAndClick(page, this.shippingAddressToolTipLink);
    await this.waitForSelectorAndClick(page, this.editShippingAddressButton);

    await this.waitForVisibleSelector(page, this.editAddressIframe);

    const addressFrame = await page.frame({url: new RegExp('sell/addresses/order', 'gmi')});

    await addAddressPage.createEditAddress(addressFrame, addressData, false);

    await Promise.all([
      addressFrame.click(addAddressPage.saveAddressButton),
      this.waitForHiddenSelector(page, this.editAddressIframe),
    ]);

    return this.getShippingAddress(page);
  }

  /**
   * Select another shipping address
   * @param page {Page} Browser tab
   * @param address {string} Shipping address to select
   * @returns {Promise<string>}
   */
  async selectAnotherShippingAddress(page, address) {
    await this.waitForSelectorAndClick(page, this.shippingAddressToolTipLink);
    await this.waitForSelectorAndClick(page, this.selectAnotherShippingAddressButton);

    await this.selectByVisibleText(page, this.changeOrderAddressSelect, address);
    await this.waitForSelectorAndClick(page, this.submitAnotherAddressButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Edit existing shipping address
   * @param page {Page} Browser tab
   * @param addressData {AddressData} Invoice address data to edit
   * @returns {Promise<void>}
   */
  async editExistingInvoiceAddress(page, addressData) {
    await this.waitForSelectorAndClick(page, this.invoiceAddressToolTipLink);
    await this.waitForSelectorAndClick(page, this.editInvoiceAddressButton);

    await this.waitForVisibleSelector(page, this.editAddressIframe);

    const addressFrame = await page.frame({url: new RegExp('sell/addresses/order', 'gmi')});

    await addAddressPage.createEditAddress(addressFrame, addressData, false);

    await Promise.all([
      addressFrame.click(addAddressPage.saveAddressButton),
      this.waitForHiddenSelector(page, this.editAddressIframe),
    ]);

    return this.getInvoiceAddress(page);
  }

  /**
   * Select another shipping address
   * @param page {Page} Browser tab
   * @param address {string} Invoice address to select
   * @returns {Promise<string>}
   */
  async selectAnotherInvoiceAddress(page, address) {
    await this.waitForSelectorAndClick(page, this.invoiceAddressToolTipLink);
    await this.waitForSelectorAndClick(page, this.selectAnotherInvoiceAddressButton);

    await this.selectByVisibleText(page, this.changeOrderAddressSelect, address);
    await this.waitForSelectorAndClick(page, this.submitAnotherAddressButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Is private note textarea visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isPrivateNoteTextareaVisible(page) {
    return this.elementVisible(page, this.privateNoteTextarea, 2000);
  }

  /**
   * Click on add new private note link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickAddNewPrivateNote(page) {
    await page.click(this.addNewPrivateNoteLink);
    await this.waitForVisibleSelector(page, this.privateNoteTextarea);
  }

  /**
   * Set private note
   * @param page {Page} Browser tab
   * @param note {string} Private note to set
   * @returns {Promise<string>}
   */
  async setPrivateNote(page, note) {
    await this.setValue(page, this.privateNoteTextarea, note);
    await page.click(this.privateNoteSaveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get private note content
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getPrivateNoteContent(page) {
    return this.getTextContent(page, this.privateNoteTextarea);
  }

  // Methods for product block
  /**
   * Get products number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getProductsNumber(page) {
    return this.getNumberFromText(page, this.productsCountSpan);
  }

  /**
   * Get product name from products table
   * @param page {Page} Browser tab
   * @param row {number} Product row on table
   * @returns {Promise<string>}
   */
  getProductNameFromTable(page, row) {
    return this.getTextContent(page, this.orderProductsTableProductName(row));
  }

  /**
   * Modify the product quantity
   * @param page {Page} Browser tab
   * @param row {number} Product row on table
   * @param quantity {number} Quantity to edit
   * @returns {Promise<number>}
   */
  async modifyProductQuantity(page, row, quantity) {
    await this.dialogListener(page);
    await Promise.all([
      page.click(this.editProductButton(row)),
      this.waitForVisibleSelector(page, this.editProductQuantityInput),
    ]);
    await this.setValue(page, this.editProductQuantityInput, quantity.toString());
    await Promise.all([
      page.click(this.UpdateProductButton),
      this.waitForVisibleSelector(page, this.editProductQuantityInput),
    ]);
    await this.waitForVisibleSelector(page, this.productQuantitySpan(row));
    return parseFloat(await this.getTextContent(page, this.productQuantitySpan(row)));
  }

  /**
   * Modify product price
   * @param page {Page} Browser tab
   * @param row {number} Product row on table
   * @param price {number} Price to edit
   * @returns {Promise<void>}
   */
  async modifyProductPrice(page, row, price) {
    this.dialogListener(page);
    await Promise.all([
      page.click(this.editProductButton(row)),
      this.waitForVisibleSelector(page, this.editProductPriceInput),
    ]);
    await this.setValue(page, this.editProductPriceInput, price);
    await Promise.all([
      page.click(this.UpdateProductButton),
      this.waitForHiddenSelector(page, this.editProductPriceInput),
    ]);

    await page.waitForTimeout(1000);
    await Promise.all([
      this.waitForVisibleSelector(page, this.customerInfoBlock),
      this.waitForVisibleSelector(page, this.orderProductsTableProductBasePrice(row)),
    ]);
  }

  /**
   * Delete product
   * @param page {Page} Browser tab
   * @param row {number} Product row on table
   * @returns {Promise<string>}
   */
  async deleteProduct(page, row) {
    await this.dialogListener(page);
    await this.waitForSelectorAndClick(page, this.deleteProductButton(row));
    return this.getGrowlMessageContent(page);
  }

  /**
   * Get total price from products tab
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getOrderTotalPrice(page) {
    return this.getPriceFromText(page, this.orderTotalPriceSpan);
  }

  /**
   * Get order total discounts
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getOrderTotalDiscounts(page) {
    return this.getPriceFromText(page, this.orderTotalDiscountsSpan);
  }

  /**
   * Add product to cart
   * @param page {Page} Browser tab
   * @param quantity {number} Product quantity to add
   * @param createNewInvoice {boolean} True if we need to create new invoice
   * @returns {Promise<string>}
   */
  async addProductToCart(page, quantity = 1, createNewInvoice = false) {
    await this.closeGrowlMessage(page);
    if (quantity !== 1) {
      await this.addQuantity(page, quantity);
    }
    if (createNewInvoice) {
      await this.selectByVisibleText(page, this.addProductInvoiceSelect, 'Create a new invoice');
    }
    await this.waitForSelectorAndClick(page, this.addProductAddButton, 1000);
    if (createNewInvoice) {
      await this.waitForSelectorAndClick(page, this.addProductCreateNewInvoiceButton);
    }
    return this.getGrowlMessageContent(page);
  }

  /**
   * add product quantity
   * @param page {Page} Browser tab
   * @param quantity {number} Product quantity to add
   * @returns {Promise<void>}
   */
  async addQuantity(page, quantity) {
    await this.setValue(page, this.addProductRowQuantity, quantity);
  }

  /**
   * Cancel add product
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async cancelAddProductToCart(page) {
    await this.waitForSelectorAndClick(page, this.addProductCancelButton);
  }

  /**
   * Is button disabled
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isAddButtonDisabled(page) {
    return this.elementVisible(page, `${this.addProductAddButton},disabled`, 1000);
  }

  /**
   * Is add product table row visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isAddProductTableRowVisible(page) {
    return this.elementVisible(page, this.addProductTableRow, 1000);
  }

  /**
   * Get product details
   * @param page {Page} Browser tab
   * @param row {number} Product row on table
   * @returns {Promise<{total: number, quantity: number, name: string, available: number, basePrice: number}>}
   */
  async getProductDetails(page, row) {
    return {
      name: await this.getTextContent(page, this.orderProductsTableProductName(row)),
      reference: await this.getTextContent(page, this.orderProductsTableProductReference(row)),
      basePrice: parseFloat((await this.getTextContent(
        page,
        this.orderProductsTableProductBasePrice(row))).replace('€', ''),
      ),
      quantity: parseInt(await this.getTextContent(page, this.orderProductsTableProductQuantity(row)), 10),
      available: parseInt(await this.getTextContent(page, this.orderProductsTableProductAvailable(row)), 10),
      total: parseFloat((await this.getTextContent(page, this.orderProductsTableProductPrice(row))).replace('€', '')),
    };
  }

  /**
   * Search product
   * @param page {Page} Browser tab
   * @param name {string} Product name to search
   * @returns {Promise<void>}
   */
  async searchProduct(page, name) {
    await this.waitForSelectorAndClick(page, this.addProductButton);
    await this.setValue(page, this.addProductRowSearch, name);
    await this.waitForSelectorAndClick(page, `${this.addProductTableRow} a`);
  }

  /**
   * Get searched product details
   * @param page {Page} Browser tab
   * @returns {Promise<{stockLocation: string, available: number}>}
   */
  async getSearchedProductDetails(page) {
    return {
      stockLocation: await this.getTextContent(page, this.addProductRowStockLocation),
      available: parseInt(await this.getTextContent(page, this.addProductAvailable), 10),
      price: parseFloat(await this.getTextContent(page, this.addProductTotalPrice)),
    };
  }

  /**
   * Add discount
   * @param page {Page} Browser tab
   * @param discountData {{name: string, type: string, value:number}} Data to set on discount form
   * @returns {Promise<string>}
   */
  async addDiscount(page, discountData) {
    await this.waitForSelectorAndClick(page, this.addDiscountButton);
    await this.waitForVisibleSelector(page, this.orderDiscountModal);

    await this.waitForSelectorAndClick(page, this.addOrderCartRuleNameInput);
    await this.setValue(page, this.addOrderCartRuleNameInput, discountData.name);
    await this.selectByVisibleText(page, this.addOrderCartRuleTypeSelect, discountData.type);
    if (discountData.type !== 'Free shipping') {
      await this.setValue(page, this.addOrderCartRuleValueInput, discountData.value);
    }
    await Promise.all([
      this.waitForVisibleSelector(page, `${this.addOrderCartRuleAddButton}:not([disabled])`),
      page.$eval(this.addOrderCartRuleAddButton, el => el.click()),
    ]);

    return this.getTextContent(page, this.alertBlock);
  }

  /**
   * Is discount table visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isDiscountListTableVisible(page) {
    return this.elementVisible(page, this.discountListTable, 2000);
  }

  /**
   * Get text column from discount table
   * @param page {Page} Browser tab
   * @param column {string} Column name on the table
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async getTextColumnFromDiscountTable(page, column, row = 1) {
    switch (column) {
      case 'name':
        return this.getTextContent(page, this.discountListNameColumn(row, 'name'));
      case 'value':
        return this.getTextContent(page, this.discountListDiscountColumn(row, 'value'));
      default:
        throw new Error(`The column ${column} is not visible in discount table`);
    }
  }

  /**
   * Delete discount
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteDiscount(page, row = 1) {
    await this.waitForSelectorAndClick(page, this.discountDeleteIcon(row));

    return this.getTextContent(page, this.alertBlock);
  }

  // Methods for product list pagination
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.waitForSelectorAndClick(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.scrollTo(page, this.productsCountSpan);
    await this.waitForSelectorAndClick(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Pagination number to select
   * @returns {Promise<boolean>}
   */
  async selectPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);
    await this.waitForVisibleSelector(page, this.orderProductsTableProductName(1));

    return this.elementVisible(page, this.paginationNextLink, 1000);
  }

  // Methods for Merchandise returns tab
  /**
   * Go to merchandise returns tab
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async goToMerchandiseReturnsTab(page) {
    await this.waitForSelectorAndClick(page, this.merchandiseReturnsTab);

    return this.elementVisible(page, `${this.merchandiseReturnsTab}.active`, 1000);
  }

  /**
   * Get merchandise returns number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getMerchandiseReturnsNumber(page) {
    return this.getNumberFromText(page, this.merchandisereturnCount);
  }

  /**
   * Get merchandise returns details
   * @param page {Page} Browser tab
   * @param row {number} Row on table merchandise returns
   * @returns {Promise<{date: string, carrier: string, shippingCost: string, weight: string, trackingNumber: string}>}
   */
  async getMerchandiseReturnsDetails(page, row = 1) {
    return {
      date: await this.getTextContent(page, this.merchandiseReturnsTableColumn(row, 'return-date')),
      type: await this.getTextContent(page, this.merchandiseReturnsTableColumn(row, 'return-type')),
      carrier: await this.getTextContent(page, this.merchandiseReturnsTableColumn(row, 'return-state')),
      trackingNumber: await this.getTextContent(
        page,
        this.merchandiseReturnsTableColumn(row, 'return-tracking-number'),
      ),
    };
  }

  // Payments block
  /**
   * Get payments number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getPaymentsNumber(page) {
    return this.getNumberFromText(page, this.orderPaymentsTitle);
  }

  /**
   * Add payment
   * @param page {Page} Browser tab
   * @param paymentData {object} Data to set on payment line
   * @param invoice {string} Invoice number to select
   * @returns {Promise<string>}
   */
  async addPayment(page, paymentData, invoice = '') {
    await this.setValue(page, this.paymentDateInput, paymentData.date);
    await this.setValue(page, this.paymentMethodInput, paymentData.paymentMethod);
    await this.setValue(page, this.transactionIDInput, paymentData.transactionID);
    await this.setValue(page, this.paymentAmountInput, paymentData.amount);
    if (paymentData.currency !== '€') {
      await this.selectByVisibleText(page, this.paymentCurrencySelect, paymentData.currency);
    }

    if (invoice !== '') {
      await this.selectByVisibleText(page, this.paymentInvoiceSelect, invoice);
    }

    await this.clickAndWaitForNavigation(page, this.paymentAddButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get invoice ID
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<number>}
   */
  getInvoiceID(page, row = 1) {
    return this.getNumberFromText(page, this.paymentsTableColumn(row, 'invoice'));
  }

  /**
   * Get payment warning
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getPaymentWarning(page) {
    return this.getTextContent(page, this.paymentWarning);
  }

  /**
   * Get payment details
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<{date: string, amount: string, paymentMethod: string, invoice: string, transactionID: string}>}
   */
  async getPaymentsDetails(page, row = 1) {
    return {
      date: await this.getTextContent(page, this.paymentsTableColumn(row, 'date')),
      paymentMethod: await this.getTextContent(page, this.paymentsTableColumn(row, 'payment-method')),
      transactionID: await this.getTextContent(page, this.paymentsTableColumn(row, 'transaction-id')),
      amount: await this.getTextContent(page, this.paymentsTableColumn(row, 'amount')),
      invoice: await this.getTextContent(page, this.paymentsTableColumn(row, 'invoice')),
    };
  }

  /**
   * Display payment details
   * @param page {Page} Browser tab
   * @param row {number} Row on table - Start by 2
   * @returns {Promise<string>}
   */
  async displayPaymentDetail(page, row = 2) {
    await this.waitForSelectorAndClick(page, this.paymentsTableDetailsButton(row - 1));

    return this.getTextContent(page, this.paymentTableRowDetails(row));
  }

  /**
   * Get currency select options
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getCurrencySelectOptions(page) {
    return this.getTextContent(page, this.paymentCurrencySelect);
  }

  // Methods for status tab
  /**
   * Get statuses number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getStatusesNumber(page) {
    return this.getNumberFromText(page, this.historyTabContent);
  }

  /**
   * Click on update status without select new status and get error message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async clickOnUpdateStatus(page) {
    await this.clickAndWaitForNavigation(page, this.secondUpdateStatusButton);

    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * Modify the order status from status tab
   * @param page {Page} Browser tab
   * @param status {string} Status to edit
   * @returns {Promise<string>}
   */
  async updateOrderStatus(page, status) {
    await this.selectByVisibleText(page, this.secondOrderStatusesSelect, status);
    await this.clickAndWaitForNavigation(page, this.secondUpdateStatusButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get status number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getStatusNumber(page) {
    return this.getNumberFromText(page, this.historyTabContent);
  }

  /**
   * Get text from Column on history table
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @param row {number} status row in table
   * @returns {Promise<string>}
   */
  async getTextColumnFromHistoryTable(page, columnName, row) {
    return this.getTextContent(page, this.statusTableColumn(row, columnName));
  }

  /**
   * Is order note opened
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isOrderNoteOpened(page) {
    return this.elementVisible(page, this.orderNoteCloseButton, 100);
  }

  /**
   * Open order note textarea
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async openOrderNoteTextarea(page) {
    await this.waitForSelectorAndClick(page, this.orderNoteOpenButton);

    return this.isOrderNoteOpened(page);
  }

  /**
   * Set order note
   * @param page {Page} Browser tab
   * @param orderNote {String} Value of order note to set on textarea
   * @returns {Promise<string>}
   */
  async setOrderNote(page, orderNote) {
    if (!(await this.isOrderNoteOpened(page))) {
      await this.openOrderNoteTextarea(page);
    }
    await this.setValue(page, this.orderNoteTextarea, orderNote);
    await this.waitForSelectorAndClick(page, this.orderNoteSaveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get order note content
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getOrderNoteContent(page) {
    return this.getTextContent(page, this.orderNoteTextarea);
  }

  /**
   * Resend email to customer
   * @param page {Page} Browser tab
   * @param row {number} Value of row number of resend button
   * @returns {Promise<string>}
   */
  async resendEmail(page, row = 1) {
    await this.waitForSelectorAndClick(page, this.resendEmailButton(row));

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  // Methods for documents tab
  /**
   * Go to documents tab
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async goToDocumentsTab(page) {
    await page.click(this.documentTab);
    return this.elementVisible(page, `${this.documentTab}.active`, 1000);
  }

  /**
   * Is generate invoice button visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isGenerateInvoiceButtonVisible(page) {
    return this.elementVisible(page, this.generateInvoiceButton, 1000);
  }

  /**
   * Get documents number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getDocumentsNumber(page) {
    return this.getNumberFromText(page, `${this.documentTab} .count`);
  }

  /**
   * Get text from Column on documents table
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @param row {number} status row in table
   * @returns {Promise<string>}
   */
  async getTextColumnFromDocumentsTable(page, columnName, row) {
    return this.getTextContent(page, this.documentsTableColumn(row, columnName));
  }

  /**
   * Click on generate invoice button
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async generateInvoice(page) {
    await this.clickAndWaitForNavigation(page, this.generateInvoiceButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get file name
   * @param page {Page} Browser tab
   * @param rowChild {number} Document row on table
   * @returns {Promise<string>}
   */
  async getFileName(page, rowChild = 1) {
    await this.goToDocumentsTab(page);

    const fileName = await this.getTextContent(page, this.documentNumberLink(rowChild));

    return fileName.replace('#', '').trim();
  }

  /**
   * Get document name
   * @param page {Page} Browser tab
   * @param rowChild {number} Document row on table
   * @returns {Promise<string>}
   */
  async getDocumentType(page, rowChild = 1) {
    await this.goToDocumentsTab(page);

    return this.getTextContent(page, this.documentType(rowChild));
  }

  /**
   * Download a document in document tab
   * @param page {Page} Browser tab
   * @param row {number} Document row on table
   * @return {Promise<string>}
   */
  downloadDocument(page, row) {
    return this.clickAndWaitForDownload(page, this.documentNumberLink(row));
  }

  /**
   * Download invoice
   * @param page {Page} Browser tab
   * @param row {number} Row of the invoice
   * @returns {Promise<void>}
   */
  async downloadInvoice(page, row = 1) {
    await this.goToDocumentsTab(page);

    return this.downloadDocument(page, row);
  }

  /**
   * Set document note
   * @param page {Page} Browser tab
   * @param note {String} Text to set on note input
   * @param row {number} Row in documents table
   * @returns {Promise<string>}
   */
  async setDocumentNote(page, note, row = 1) {
    await this.waitForSelectorAndClick(page, this.addDocumentNoteButton(row));
    await this.setValue(page, this.documentNoteInput(row + 1), note);
    await this.waitForSelectorAndClick(page, this.documentNoteSaveButton(row + 1));

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Is edit note button visible
   * @param page {Page} Browser tab
   * @param row {number} Row on table documents
   * @returns {Promise<boolean>}
   */
  async isEditDocumentNoteButtonVisible(page, row = 1) {
    await this.goToDocumentsTab(page);

    return this.elementVisible(page, this.editDocumentNoteButton(row), 1000);
  }

  /**
   * Is add note button visible
   * @param page {Page} Browser tab
   * @param row {number} Row on table documents
   * @returns {Promise<boolean>}
   */
  async isAddDocumentNoteButtonVisible(page, row = 1) {
    await this.goToDocumentsTab(page);

    return this.elementVisible(page, this.addDocumentNoteButton(row), 1000);
  }

  /**
   * Is enter payment button visible
   * @param page {Page} Browser tab
   * @param row {number} Row on table documents
   * @returns {Promise<boolean>}
   */
  async isEnterPaymentButtonVisible(page, row = 1) {
    await this.goToDocumentsTab(page);

    return this.elementVisible(page, this.enterPaymentButton(row), 1000);
  }

  /**
   * Click on enter payment button and get amount value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<*>}
   */
  async clickOnEnterPaymentButton(page, row = 1) {
    await this.waitForSelectorAndClick(page, this.enterPaymentButton(row));

    return page.$eval(this.paymentAmountInput, el => el.value);
  }

  /**
   * Download delivery slip
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async downloadDeliverySlip(page) {
    /* eslint-disable no-return-assign, no-param-reassign */
    await this.goToDocumentsTab(page);

    // Delete the target because a new tab is opened when downloading the file
    return this.downloadDocument(page, 3);
    /* eslint-enable no-return-assign, no-param-reassign */
  }

  // Methods for carriers tab
  /**
   * Get carriers number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getCarriersNumber(page) {
    return this.getNumberFromText(page, `${this.carriersTab} .count`);
  }

  /**
   * Go to carriers tab
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async goToCarriersTab(page) {
    await this.waitForSelectorAndClick(page, this.carriersTab);

    return this.elementVisible(page, `${this.carriersTab}.active`, 1000);
  }

  /**
   * Get carrier details
   * @param page {Page} Browser tab
   * @param row {number} Row on carriers table
   * @returns {Promise<{date: string, carrier: string, shippingCost: string, weight: string, trackingNumber: string}>}
   */
  async getCarrierDetails(page, row = 1) {
    return {
      date: await this.getTextContent(page, this.carriersTableColumn(row, 'date')),
      carrier: await this.getTextContent(page, this.carriersTableColumn(row, 'carrier-name')),
      weight: await this.getTextContent(page, this.carriersTableColumn(row, 'carrier-weight')),
      shippingCost: await this.getTextContent(page, this.carriersTableColumn(row, 'carrier-price')),
      trackingNumber: await this.getTextContent(page, this.carriersTableColumn(row, 'carrier-tracking-number')),
    };
  }

  /**
   * Click on edit link and check if the modal is visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnEditLink(page) {
    await this.waitForSelectorAndClick(page, this.editLink);

    return this.elementVisible(page, this.updateOrderShippingModalDialog, 1000);
  }

  /**
   * Set shipping details
   * @param page {Page} Browser tab
   * @param shippingData {{carrier: string, shippingCost: string, trackingNumber: string}} Data to set on shipping form
   * @returns {Promise<string>}
   */
  async setShippingDetails(page, shippingData) {
    await this.setValue(page, this.trackingNumberInput, shippingData.trackingNumber);
    await this.setValue(page, this.carrierSelect, shippingData.carrier);
    await this.clickAndWaitForNavigation(page, this.updateCarrierButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new Order();
