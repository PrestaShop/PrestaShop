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

    // Customer card
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

    // Order card
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
    this.partialRefundButton = 'button.partial-refund-display';
    this.orderTotalPriceSpan = '#orderTotal';
    this.returnProductsButton = '#order-view-page button.return-product-display';
    this.addProductTableRow = '#addProductTableRow';
    this.addProductButton = '#addProductBtn';
    this.addProductRowSearch = '#add_product_row_search';
    this.addProductRowQuantity = '#add_product_row_quantity';
    this.addProductRowStockLocation = '#addProductLocation';
    this.addProductAvailable = '#addProductAvailable';
    this.addProductTotalPrice = '#addProductTotalPrice';
    this.addProductAddButton = '#add_product_row_add';
    this.addProductCancelButton = '#add_product_row_cancel';

    // Pagination selectors
    this.paginationLimitSelect = '#orderProductsTablePaginationNumberSelector';
    this.paginationLabel = '#orderProductsNavPagination .page-item.active';
    this.paginationNextLink = '#orderProductsTablePaginationNext';
    this.paginationPreviousLink = '#orderProductsTablePaginationPrev';

    // Status card
    this.orderStatusesSelect = '#update_order_status_action_input';
    this.updateStatusButton = '#update_order_status_action_btn';

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
    this.paymentAmountInput = '#order_payment_amount';

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

    // Refund form
    this.refundProductQuantity = row => `${this.orderProductsRowTable(row)} input[id*='cancel_product_quantity']`;
    this.refundProductAmount = row => `${this.orderProductsRowTable(row)} input[id*='cancel_product_amount']`;
    this.refundShippingCost = row => `${this.orderProductsRowTable(row)} input[id*='cancel_product_shipping_amount']`;
    this.partialRefundSubmitButton = 'button#cancel_product_save';
  }

  /*
  Methods
   */

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
   * Modify the order status
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
   * Get order status
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getOrderStatus(page) {
    return this.getTextContent(page, `${this.orderStatusesSelect} option[selected='selected']`, false);
  }

  /**
   * Does status exist
   * @param page {Page} Browser tab
   * @param statusName {string} Status to check
   * @returns {Promise<boolean>}
   */
  async doesStatusExist(page, statusName) {
    let options = await page.$$eval(
      `${this.orderStatusesSelect} option`,
      all => all.map(
        option => ({
          textContent: option.textContent,
          value: option.value,
        })),
    );

    options = options.filter(option => statusName === option.textContent);
    return options.length !== 0;
  }

  /**
   * Get total price from products tab
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getOrderTotalPrice(page) {
    return this.getPriceFromText(page, this.orderTotalPriceSpan);
  }

  // Documents tab methods
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
   * @returns {Promise<void>}
   */
  async downloadInvoice(page) {
    await this.goToDocumentsTab(page);

    return this.downloadDocument(page, 1);
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
   * @param shippingData {shippingData} Data to set on shipping form
   * @returns {Promise<string>}
   */
  async setShippingDetails(page, shippingData) {
    await this.setValue(page, this.trackingNumberInput, shippingData.trackingNumber);
    await this.setValue(page, this.carrierSelect, shippingData.carrier);
    await this.clickAndWaitForNavigation(page, this.updateCarrierButton);

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

  /**
   * Go to view full details page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToViewFullDetails(page) {
    await this.clickAndWaitForNavigation(page, this.ViewAllDetailsLink);
  }

  /**
   * Get customer information
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getCustomerInfoBlock(page) {
    return this.getTextContent(page, this.customerInfoBlock);
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

  /**
   * Get products number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getProductsNumber(page) {
    return this.getNumberFromText(page, this.productsCountSpan);
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
   * Add product to cart
   * @param page {Page} Browser tab
   * @param quantity {number} Product quantity to add
   * @returns {Promise<string>}
   */
  async addProductToCart(page, quantity = 0) {
    await this.closeGrowlMessage(page);
    if (quantity !== 0) {
      await this.addQuantity(page, quantity);
    }
    await this.waitForSelectorAndClick(page, this.addProductAddButton, 1000);
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
}

module.exports = new Order();
