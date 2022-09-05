require('module-alias/register');
const ViewOrderBasePage = require('@pages/BO/orders/view/viewOrderBasePage');

/**
 * Tab list block, contains functions that can be used on status, documents, carriers and merchandise returns tabs
 * @class
 * @extends ViewOrderBasePage
 */
class TabListBlock extends ViewOrderBasePage.constructor {
  /**
   * @constructs
   * Setting up texts and selectors to use on status, documents, carriers and merchandise returns tabs
   */
  constructor() {
    super();

    this.successBadge = id => `.tab-content span.badge-success:nth-child(${id + 3})`;

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
    this.carrierSelect = '#select2-update_order_shipping_new_carrier_id-container';
    this.carriersSelectResult = '#select2-update_order_shipping_new_carrier_id-results';
    this.carrierToSelect = id => `${this.carriersSelectResult} li:nth-child(${id})`;
    this.updateCarrierButton = `${this.updateOrderShippingModalDialog} button.btn-primary`;
    this.giftMessage = '#gift-message';

    // Merchandise returns tab
    this.merchandiseReturnsTab = '#orderReturnsTab';
    this.merchandisereturnCount = `${this.merchandiseReturnsTab} span[data-role='count']`;
    this.merchandiseReturnsGridTable = 'table[data-role=\'merchandise-returns-grid-table\']';
    this.merchandiseReturnsTableBody = `${this.merchandiseReturnsGridTable} tbody`;
    this.merchandiseReturnsTableRow = row => `${this.merchandiseReturnsTableBody} tr:nth-child(${row})`;
    this.merchandiseReturnsTableColumn = (row, column) => `${this.merchandiseReturnsTableRow(row)}`
      + ` td[data-role='merchandise-${column}']`;
  }

  /*
  Methods
   */

  /**
   * Get success badges
   * @param page {Page} Browser tab
   * @param numberOfBadges {number} Number of badges to get text content
   * @returns {Promise<string>}
   */
  async getSuccessBadge(page, numberOfBadges) {
    let badge = '';
    for (let i = 1; i <= numberOfBadges; i++) {
      badge += await this.getTextContent(page, this.successBadge(i));
    }
    return badge;
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
   * Click on enter payment button
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async clickOnEnterPaymentButton(page, row = 1) {
    await this.waitForSelectorAndClick(page, this.enterPaymentButton(row));
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
   * Get gift message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getGiftMessage(page) {
    await this.waitForSelectorAndClick(page, this.carriersTab);

    return this.getTextContent(page, this.giftMessage);
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
   * @param shippingData {{trackingNumber: string, carrier: string, carrierID: number}} Data to set on shipping form
   * @returns {Promise<string>}
   */
  async setShippingDetails(page, shippingData) {
    await this.setValue(page, this.trackingNumberInput, shippingData.trackingNumber);
    await page.click(this.carrierSelect);
    await this.waitForSelectorAndClick(page, this.carrierToSelect(shippingData.carrierID));
    await this.clickAndWaitForNavigation(page, this.updateCarrierButton);

    return this.getAlertSuccessBlockParagraphContent(page);
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
   * @returns {Promise<{date: string, type: string, status: string, number: string}>}
   */
  async getMerchandiseReturnsDetails(page, row = 1) {
    return {
      date: await this.getTextContent(page, this.merchandiseReturnsTableColumn(row, 'return-date')),
      type: await this.getTextContent(page, this.merchandiseReturnsTableColumn(row, 'return-type')),
      status: await this.getTextContent(page, this.merchandiseReturnsTableColumn(row, 'return-state')),
      number: await this.getTextContent(page, this.merchandiseReturnsTableColumn(row, 'return-id')),
    };
  }
}

module.exports = new TabListBlock();
