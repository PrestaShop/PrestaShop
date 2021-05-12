require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Order extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Order';
    this.partialRefundValidationMessage = 'A partial refund was successfully created.';
    this.successfulAddProductMessage = 'The product was successfully added.';
    this.successfulDeleteProductMessage = 'The product was successfully removed.';
    this.errorMinimumQuantityMessage = 'Minimum quantity of "3" must be added';
    this.errorAddSameProduct = 'This product is already in your order, please edit the quantity instead.';

    // Customer block
    this.customerInfoBlock = '#customerInfo';
    this.ViewAllDetailsLink = '#viewFullDetails a';
    this.customerEmailLink = '#customerEmail';
    this.validatedOrders = '#validatedOrders span.badge';
    this.shippingAddressBlock = '#addressShipping';
    this.invoiceAddressBlock = '#addressInvoice';
    this.privateNoteDiv = '#privateNote';
    this.privateNoteTextarea = '#private_note_note';
    this.addNewPrivateNoteLink = '#privateNote a.js-private-note-toggle-btn';
    this.privateNoteSaveButton = `${this.privateNoteDiv} .js-private-note-btn`;

    // Products block
    this.productsCountSpan = '#orderProductsPanelCount';
    this.orderProductsTable = '#orderProductsTable';
    this.orderProductsRowTable = row => `${this.orderProductsTable} tbody tr:nth-child(${row})`;
    this.orderProductsTableNameColumn = row => `${this.orderProductsRowTable(row)} td.cellProductName`;
    this.orderProductsTableProductName = row => `${this.orderProductsTableNameColumn(row)} p.productName`;
    this.orderProductsTableProductBasePrice = row => `${this.orderProductsRowTable(row)} td.cellProductUnitPrice`;
    this.orderProductsTableProductQuantity = row => `${this.orderProductsRowTable(row)} td.cellProductQuantity`;
    this.orderProductsTableProductAvailable = row => `${this.orderProductsRowTable(row)}
     td.cellProductAvailableQuantity`;
    this.orderProductsTableProductPrice = row => `${this.orderProductsRowTable(row)} td.cellProductTotalPrice`;
    this.editProductButton = row => `${this.orderProductsRowTable(row)} button[data-original-title='Edit']`;
    this.deleteProductButton = row => `${this.orderProductsRowTable(row)} button[data-original-title='Delete']`;
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
    this.addProductAddButton = '#add_product_row_add';
    this.addProductCancelButton = '#add_product_row_cancel';

    // Pagination selectors
    this.paginationLimitSelect = '#orderProductsTablePaginationNumberSelector';
    this.paginationLabel = '#orderProductsNavPagination .page-item.active';
    this.paginationNextLink = '#orderProductsTablePaginationNext';
    this.paginationPreviousLink = '#orderProductsTablePaginationPrev';

    // Status tab
    this.orderStatusesSelect = '#update_order_status_action_input';
    this.updateStatusButton = '#update_order_status_action_btn';

    // Document tab
    this.documentTab = 'a#orderDocumentsTab';
    this.documentsTableDiv = '#orderDocumentsTabContent';
    this.documentsTableRow = row => `${this.documentsTableDiv} table tbody tr:nth-child(${row})`;
    this.documentNumberLink = row => `${this.documentsTableRow(row)} td:nth-child(3) a`;
    this.documentName = row => `${this.documentsTableRow(row)} td:nth-child(2)`;

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
   * @param page
   * @return {Promise<string>}
   */
  getShippingAddress(page) {
    return this.getTextContent(page, this.shippingAddressBlock);
  }

  /**
   * Get invoice address from customer card
   * @param page
   * @return {Promise<string>}
   */
  getInvoiceAddress(page) {
    return this.getTextContent(page, this.invoiceAddressBlock);
  }

  /**
   * Get product name from products table
   * @param page
   * @param row
   * @return {Promise<string>}
   */
  getProductNameFromTable(page, row) {
    return this.getTextContent(page, this.orderProductsTableProductName(row));
  }

  /**
   * Modify the product quantity
   * @param page
   * @param row
   * @param quantity
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
   * @param page
   * @param row
   * @param price
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
   * @param page
   * @param row
   * @returns {Promise<string>}
   */
  async deleteProduct(page, row) {
    await this.dialogListener(page);
    await this.waitForSelectorAndClick(page, this.deleteProductButton(row));
    return this.getGrowlMessageContent(page);
  }

  /**
   * Modify the order status
   * @param page
   * @param status
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
   * @param page
   * @return {Promise<string>}
   */
  async getOrderStatus(page) {
    return this.getTextContent(page, `${this.orderStatusesSelect} option[selected='selected']`, false);
  }

  /**
   * Does status exist
   * @param page
   * @param statusName
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

    options = await options.filter(option => statusName === option.textContent);
    return options.length !== 0;
  }

  /**
   * Get total price from products tab
   * @param page
   * @return {Promise<number>}
   */
  getOrderTotalPrice(page) {
    return this.getPriceFromText(page, this.orderTotalPriceSpan);
  }

  /**
   * Get document name
   * @param page
   * @param rowChild
   * @returns {Promise<string>}
   */
  async getDocumentName(page, rowChild = 1) {
    await this.goToDocumentsTab(page);

    return this.getTextContent(page, this.documentName(rowChild));
  }

  /**
   * Go to documents tab
   * @param page
   * @return {Promise<void>}
   */
  async goToDocumentsTab(page) {
    await Promise.all([
      page.click(this.documentTab),
      this.waitForVisibleSelector(page, `${this.documentTab}.active`),
    ]);
  }

  /**
   * Get file name
   * @param page
   * @param rowChild
   * @returns fileName
   */
  async getFileName(page, rowChild = 1) {
    await this.goToDocumentsTab(page);

    const fileName = await this.getTextContent(page, this.documentNumberLink(rowChild));
    return fileName.replace('#', '').trim();
  }

  /**
   * Download a document in document tab
   * @param page
   * @param row
   * @return {Promise<*>}
   */
  async downloadDocument(page, row) {
    /* eslint-disable no-return-assign, no-param-reassign */
    // Delete the target because a new tab is opened when downloading the file
    await page.$eval(this.documentNumberLink(row), el => el.target = '');

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.click(this.documentNumberLink(row)),
    ]);

    return download.path();
    /* eslint-enable no-return-assign, no-param-reassign */
  }

  /**
   * Download invoice
   * @param page
   * @returns {Promise<void>}
   */
  async downloadInvoice(page) {
    await this.goToDocumentsTab(page);

    return this.downloadDocument(page, 1);
  }

  /**
   * Click on partial refund button
   * @param page
   * @returns {Promise<void>}
   */
  async clickOnPartialRefund(page) {
    await page.click(this.partialRefundButton);
    await this.waitForVisibleSelector(page, this.refundProductQuantity(1));
  }

  /**
   * Add partial refund product
   * @param page
   * @param productRow
   * @param quantity
   * @param amount
   * @param shipping
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
   * @param page
   * @returns {Promise<*>}
   */
  async downloadDeliverySlip(page) {
    /* eslint-disable no-return-assign, no-param-reassign */
    await this.goToDocumentsTab(page);

    // Delete the target because a new tab is opened when downloading the file
    return this.downloadDocument(page, 3);
    /* eslint-enable no-return-assign, no-param-reassign */
  }

  /**
   * Is return products button visible
   * @param page
   * @returns {Promise<boolean>}
   */
  isReturnProductsButtonVisible(page) {
    return this.elementVisible(page, this.returnProductsButton, 2000);
  }

  /**
   * Go to view full details page
   * @param page
   * @returns {Promise<void>}
   */
  async goToViewFullDetails(page) {
    await this.clickAndWaitForNavigation(page, this.ViewAllDetailsLink);
  }

  /**
   * Get customer information
   * @param page
   * @returns {Promise<string>}
   */
  getCustomerInfoBlock(page) {
    return this.getTextContent(page, this.customerInfoBlock);
  }

  /**
   * Get customer email
   * @param page
   * @returns {Promise<string>}
   */
  getCustomerEmail(page) {
    return this.getTextContent(page, this.customerEmailLink);
  }

  /**
   * Get validated orders number
   * @param page
   * @returns {Promise<number>}
   */
  getValidatedOrdersNumber(page) {
    return this.getNumberFromText(page, `${this.validatedOrders}.badge-dark`);
  }

  /**
   * Is private note textarea visible
   * @param page
   * @returns {Promise<boolean>}
   */
  isPrivateNoteTextareaVisible(page) {
    return this.elementVisible(page, this.privateNoteTextarea, 2000);
  }

  /**
   * Click on add new private note link
   * @param page
   * @returns {Promise<void>}
   */
  async clickAddNewPrivateNote(page) {
    await page.click(this.addNewPrivateNoteLink);
    await this.waitForVisibleSelector(page, this.privateNoteTextarea);
  }

  /**
   * Set private note
   * @param page
   * @param note
   * @returns {Promise<string>}
   */
  async setPrivateNote(page, note) {
    await this.setValue(page, this.privateNoteTextarea, note);
    await page.click(this.privateNoteSaveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get private note content
   * @param page
   * @returns {Promise<string>}
   */
  getPrivateNoteContent(page) {
    return this.getTextContent(page, this.privateNoteTextarea);
  }

  /**
   * Get products number
   * @param page
   * @returns {Promise<number>}
   */
  getProductsNumber(page) {
    return this.getNumberFromText(page, this.productsCountSpan);
  }

  /**
   * Search product
   * @param page
   * @param name
   * @returns {Promise<void>}
   */
  async searchProduct(page, name) {
    await this.waitForSelectorAndClick(page, this.addProductButton);
    await this.setValue(page, this.addProductRowSearch, name);
    await this.waitForSelectorAndClick(page, `${this.addProductTableRow} a`);
  }

  /**
   * Get searched product details
   * @param page
   * @returns {Promise<{available: *, basePriceTInc: *, basePriceTExc: *}>}
   */
  async getSearchedProductDetails(page) {
    return {
      stockLocation: await this.getTextContent(page, this.addProductRowStockLocation),
      available: parseInt(await this.getTextContent(page, this.addProductAvailable), 10),
    };
  }

  /**
   * Add product to cart
   * @param page
   * @param quantity
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
   * @param page
   * @param quantity
   * @returns {Promise<void>}
   */
  async addQuantity(page, quantity) {
    await this.setValue(page, this.addProductRowQuantity, quantity);
  }

  /**
   * Cancel add product
   * @param page
   * @returns {Promise<void>}
   */
  async cancelAddProductToCart(page) {
    await this.waitForSelectorAndClick(page, this.addProductCancelButton);
  }

  /**
   * Is button disabled
   * @param page
   * @returns {Promise<boolean>}
   */
  isAddButtonDisabled(page) {
    return this.elementVisible(page, `${this.addProductAddButton},disabled`, 1000);
  }

  /**
   * Get product details
   * @param page
   * @param row
   * @returns {Promise<{total: number, quantity: number, name: *, available: number, basePrice: number}>}
   */
  async getProductDetails(page, row) {
    return {
      name: await this.getTextContent(page, this.orderProductsTableProductName(row)),
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
   * @param page
   * @returns {Promise<string>}
   */
  async getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Click on next
   * @param page
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.waitForSelectorAndClick(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.scrollTo(page, this.productsCountSpan);
    await this.waitForSelectorAndClick(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Select pagination limit
   * @param page
   * @param number
   * @returns {Promise<boolean>}
   */
  async selectPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);
    await this.waitForVisibleSelector(page, this.orderProductsTableProductName(1));

    return this.elementVisible(page, this.paginationNextLink, 1000);
  }
}

module.exports = new Order();
