require('module-alias/register');
const ViewOrderBasePage = require('@pages/BO/orders/view/viewOrderBasePage');

/**
 * Products block, contains functions that can be used on view/edit products block on view order page
 * @class
 * @extends ViewOrderBasePage
 */
class ProductsBlock extends ViewOrderBasePage.constructor {
  /**
   * @constructs
   * Setting up texts and selectors to use on products block
   */
  constructor() {
    super();

    // Products block header
    this.productsCountSpan = '#orderProductsPanelCount';
    this.orderProductsLoading = '#orderProductsLoading';

    // Products table
    this.orderProductsTable = '#orderProductsTable';
    this.orderProductsRowTable = row => `${this.orderProductsTable} tbody tr:nth-child(${row})`;
    this.orderProductsTableNameColumn = row => `${this.orderProductsRowTable(row)} td.cellProductName`;
    this.orderProductsTableProductName = row => `${this.orderProductsTableNameColumn(row)} p.productName`;
    this.orderProductsTableProductReference = row => `${this.orderProductsTableNameColumn(row)} p.productReference`;
    this.orderProductsTableProductBasePrice = row => `${this.orderProductsRowTable(row)} td.cellProductUnitPrice`;
    this.orderProductsTableProductQuantity = row => `${this.orderProductsRowTable(row)} td.cellProductQuantity`;
    this.orderProductsTableProductAvailable = row => `${this.orderProductsRowTable(row)}
     td.cellProductAvailableQuantity`;
    this.orderProductsTableProductPrice = row => `${this.orderProductsRowTable(row)} td.cellProductTotalPrice`;
    this.deleteProductButton = row => `${this.orderProductsRowTable(row)} button.js-order-product-delete-btn`;
    this.editProductButton = row => `${this.orderProductsRowTable(row)} button.js-order-product-edit-btn`;
    this.productQuantitySpan = row => `${this.orderProductsRowTable(row)} td.cellProductQuantity span`;

    // Edit row table
    this.orderProductsEditRowTable = `${this.orderProductsTable} tbody tr.editProductRow`;
    this.editProductQuantityInput = `${this.orderProductsEditRowTable} input.editProductQuantity`;
    this.editProductPriceInput = `${this.orderProductsEditRowTable} input.editProductPriceTaxIncl`;
    this.UpdateProductButton = `${this.orderProductsEditRowTable} button.productEditSaveBtn`;
    this.modalConfirmNewPrice = '#modal-confirm-new-price';
    this.modalConfirmNewPriceSubmitButton = `${this.modalConfirmNewPrice} button.btn-confirm-submit`;

    // Total order
    this.orderTotalPriceSpan = '#orderTotal';

    // Add discount
    this.orderTotalDiscountsSpan = '#orderDiscountsTotal';

    // Add product
    this.addProductButton = '#addProductBtn';
    this.addProductTableRow = '#addProductTableRow';
    this.addProductRowSearch = '#add_product_row_search';
    this.addProductRowQuantity = '#add_product_row_quantity';
    this.addProductRowPrice = '#add_product_row_price_tax_included';
    this.addProductRowStockLocation = '#addProductLocation';
    this.addProductAvailable = '#addProductAvailable';
    this.addProductTotalPrice = '#addProductTotalPrice';
    this.addProductInvoiceSelect = '#add_product_row_invoice';
    this.addProductNewInvoiceCarrierName = '#addProductNewInvoiceInfo div p[data-role=\'carrier-name\']';
    this.addProductNewInvoiceFreeShippingCheckbox = '#add_product_row_free_shipping';
    this.addProductNewInvoiceFreeShippingDiv = '#addProductNewInvoiceInfo td div.md-checkbox';

    this.addProductAddButton = '#add_product_row_add';
    this.addProductCancelButton = '#add_product_row_cancel';
    this.addProductModalConfirmNewInvoice = '#modal-confirm-new-invoice';
    this.addProductCreateNewInvoiceButton = `${this.addProductModalConfirmNewInvoice} .btn-confirm-submit`;

    // Add discount
    this.addDiscountButton = 'button[data-target=\'#addOrderDiscountModal\']';
    this.orderDiscountModal = '#addOrderDiscountModal';
    this.addOrderCartRuleNameInput = '#add_order_cart_rule_name';
    this.addOrderCartRuleTypeSelect = '#add_order_cart_rule_type';
    this.addOrderCartRuleValueInput = '#add_order_cart_rule_value';
    this.addOrderCartRuleAddButton = '#add_order_cart_rule_submit';

    // Discount table
    this.discountListTable = 'table.table.discountList';
    this.discountListRowTable = row => `${this.discountListTable} tbody tr:nth-child(${row})`;
    this.discountListNameColumn = row => `${this.discountListRowTable(row)} td.discountList-name`;
    this.discountListDiscountColumn = row => `${this.discountListRowTable(row)} td[data-role='discountList-value']`;
    this.discountDeleteIcon = row => `${this.discountListRowTable(row)} a.delete-cart-rule`;

    // Refund form
    this.refundProductQuantity = row => `${this.orderProductsRowTable(row)} input[id*='cancel_product_quantity']`;
    this.refundProductAmount = row => `${this.orderProductsRowTable(row)} input[id*='cancel_product_amount']`;
    this.refundShippingCost = row => `${this.orderProductsRowTable(row)} input[id*='cancel_product_shipping_amount']`;
    this.partialRefundSubmitButton = 'button#cancel_product_save';

    // Pagination selectors
    this.paginationLimitSelect = '#orderProductsTablePaginationNumberSelector';
    this.paginationLabel = '#orderProductsNavPagination .page-item.active';
    this.paginationNextLink = '#orderProductsTablePaginationNext';
    this.paginationPreviousLink = '#orderProductsTablePaginationPrev';
  }

  /*
  Methods
   */
  // Methods for create partial refund
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
    await this.waitForVisibleSelector(page, this.refundProductQuantity(1));
    await this.setValue(page, this.refundProductQuantity(productRow), quantity);
    if (amount !== 0) {
      await this.setValue(page, this.refundProductAmount(productRow), amount);
    }
    if (shipping !== 0) {
      await this.setValue(page, this.refundShippingCost(productRow), shipping);
    }
    await this.clickAndWaitForNavigation(page, this.partialRefundSubmitButton);

    return this.getAlertSuccessBlockParagraphContent(page);
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
   * Modify product quantity
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
    await this.setValue(page, this.editProductQuantityInput, quantity);
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

    await this.waitForSelectorAndClick(page, this.editProductButton(row));
    await this.setValue(page, this.editProductPriceInput, price);

    await Promise.all([
      page.click(this.UpdateProductButton),
      this.waitForHiddenSelector(page, this.editProductPriceInput),
    ]);
    await Promise.all([
      this.waitForVisibleSelector(page, this.orderProductsLoading),
      this.waitForHiddenSelector(page, this.orderProductsLoading),
    ]);
    await this.waitForVisibleSelector(page, this.orderProductsTableProductBasePrice(row));
  }

  /**
   * Modify product price for multi invoice
   * @param page {Page} Browser tab
   * @param row {number} Product row on table
   * @param price {number} Price to edit
   * @returns {Promise<void>}
   */
  async modifyProductPriceForMultiInvoice(page, row, price) {
    this.dialogListener(page);

    await Promise.all([
      page.click(this.editProductButton(row)),
      this.waitForVisibleSelector(page, this.editProductPriceInput),
    ]);
    await this.setValue(page, this.editProductPriceInput, price);

    await Promise.all([
      page.click(this.UpdateProductButton),
      this.waitForVisibleSelector(page, this.modalConfirmNewPrice),
    ]);

    await page.click(this.modalConfirmNewPriceSubmitButton);

    await this.waitForVisibleSelector(page, this.orderProductsLoading);
    await this.waitForHiddenSelector(page, this.orderProductsLoading);

    await this.waitForVisibleSelector(page, this.orderProductsTableProductName(row));
  }

  /**
   * Delete product
   * @param page {Page} Browser tab
   * @param row {number} Product row on table
   * @returns {Promise<string>}
   */
  async deleteProduct(page, row) {
    await this.dialogListener(page);
    if (await this.elementVisible(page, this.growlMessageBlock)) {
      await this.closeGrowlMessage(page);
    }
    await this.waitForSelectorAndClick(page, this.deleteProductButton(row));

    return this.getGrowlMessageContent(page);
  }

  /**
   * Get total price from products tab
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getOrderTotalPrice(page) {
    return this.getPriceFromText(page, this.orderTotalPriceSpan, 1000);
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
   * Create new invoice
   * @param page {Page} Browser tab
   * @param invoice {string} The invoice to select from dropdown list
   * @returns {Promise<void>}
   */
  async selectInvoice(page, invoice = 'Create a new invoice') {
    await this.selectByVisibleText(page, this.addProductInvoiceSelect, invoice);
  }

  /**
   * Get invoices list from select options
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getInvoicesFromSelectOptions(page) {
    return this.getTextContent(page, this.addProductInvoiceSelect);
  }

  /**
   * Get carrier name when creating new invoice
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getNewInvoiceCarrierName(page) {
    return this.getTextContent(page, this.addProductNewInvoiceCarrierName);
  }

  /**
   * Is free shipping selected
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isFreeShippingSelected(page) {
    return this.isChecked(page, this.addProductNewInvoiceFreeShippingCheckbox);
  }

  /**
   * Select free shipping checkbox
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async selectFreeShippingCheckbox(page) {
    await this.waitForSelectorAndClick(page, this.addProductNewInvoiceFreeShippingDiv);
  }

  /**
   * Add product quantity from add product input
   * @param page {Page} Browser tab
   * @param quantity {number} Product quantity to add
   * @returns {Promise<void>}
   */
  async addQuantity(page, quantity) {
    await this.setValue(page, this.addProductRowQuantity, quantity);
  }

  /**
   * Set new product price from add product input
   * @param page {Page} Browser tab
   * @param price {float} Value of price to update
   * @returns {Promise<void>}
   */
  async updateProductPrice(page, price) {
    await this.setValue(page, this.addProductRowPrice, price);
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
    await this.waitForSelectorAndClick(page, this.addProductAddButton, 1000);
    if (createNewInvoice) {
      await this.waitForSelectorAndClick(page, this.addProductCreateNewInvoiceButton);
    }

    return this.getGrowlMessageContent(page);
  }

  /**
   * Cancel add product to cart
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async cancelAddProductToCart(page) {
    await this.waitForSelectorAndClick(page, this.addProductCancelButton);
  }

  /**
   * Is add button disabled
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isAddButtonDisabled(page) {
    return this.elementVisible(page, `${this.addProductAddButton}[disabled]`, 1000);
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
   * Get searched product information
   * @param page {Page} Browser tab
   * @returns {Promise<{available: number, price: float}>}
   */
  async getSearchedProductInformation(page) {
    return {
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

    await this.waitForVisibleSelector(page, `${this.addOrderCartRuleAddButton}:not([disabled])`);
    await page.$eval(this.addOrderCartRuleAddButton, el => el.click());

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
}

module.exports = new ProductsBlock();
