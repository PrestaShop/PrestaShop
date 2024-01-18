import {ViewOrderBasePage} from '@pages/BO/orders/view/viewOrderBasePage';

import type {ProductDiscount} from '@data/types/product';

import type {Frame, Page} from 'playwright';

/**
 * Products block, contains functions that can be used on view/edit products block on view order page
 * @class
 * @extends ViewOrderBasePage
 */
class ProductsBlock extends ViewOrderBasePage {
  private readonly productsCountSpan: string;

  private readonly orderProductsLoading: string;

  private readonly orderProductsTable: string;

  private readonly generateVoucherCheckbox: string;

  private readonly returnProductButton: string;

  private readonly returnQuantityInput: (row: number) => string;

  private readonly returnQuantityCheckbox: (row: number) => string;

  private readonly orderProductsRowTable: (row: number) => string;

  private readonly orderProductsTableNameColumn: (row: number) => string;

  private readonly orderProductsTableProductName: (row: number) => string;

  private readonly orderProductsTableProductReference: (row: number) => string;

  private readonly orderProductsTableProductBasePrice: (row: number) => string;

  private readonly orderProductsTableProductQuantity: (row: number) => string;

  private readonly orderProductsTableProductAvailable: (row: number) => string;

  private readonly orderProductsTableProductPrice: (row: number) => string;

  private readonly deleteProductButton: (row: number) => string;

  private readonly editProductButton: (row: number) => string;

  private readonly productQuantitySpan: (row: number) => string;

  private readonly orderProductsEditRowTable: string;

  private readonly editProductQuantityInput: string;

  private readonly editProductPriceInput: string;

  private readonly updateProductButton: string;

  private readonly modalConfirmNewPrice: string;

  private readonly modalConfirmNewPriceSubmitButton: string;

  private readonly orderTotalPriceSpan: string;

  private readonly orderWrappingTotal: string;

  private readonly orderTotalProductsSpan: string;

  private readonly orderTotalDiscountsSpan: string;

  private readonly orderTotalShippingSpan: string;

  private readonly addProductButton: string;

  private readonly addProductTableRow: string;

  private readonly addProductRowSearch: string;

  private readonly addProductRowQuantity: string;

  private readonly addProductRowPrice: string;

  private readonly addProductRowStockLocation: string;

  private readonly addProductAvailable: string;

  private readonly addProductTotalPrice: string;

  private readonly addProductInvoiceSelect: string;

  private readonly addProductNewInvoiceCarrierName: string;

  private readonly addProductNewInvoiceFreeShippingCheckbox: string;

  private readonly addProductNewInvoiceFreeShippingDiv: string;

  private readonly addProductAddButton: string;

  private readonly addProductCancelButton: string;

  private readonly addProductModalConfirmNewInvoice: string;

  private readonly addProductCreateNewInvoiceButton: string;

  private readonly addDiscountButton: string;

  private readonly orderDiscountModal: string;

  private readonly addOrderCartRuleNameInput: string;

  private readonly addOrderCartRuleTypeSelect: string;

  private readonly addOrderCartRuleValueInput: string;

  private readonly addOrderCartRuleAddButton: string;

  private readonly discountListTable: string;

  private readonly discountListRowTable: (row: number) => string;

  private readonly discountListNameColumn: (row: number) => string;

  private readonly discountListDiscountColumn: (row: number) => string;

  private readonly discountDeleteIcon: (row: number) => string;

  private readonly refundProductQuantity: (row: number) => string;

  private readonly refundProductAmount: (row: number) => string;

  private readonly refundShippingCost: (row: number) => string;

  private readonly partialRefundSubmitButton: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  private readonly refundProductColumn: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on products block
   */
  constructor() {
    super();

    // Products block header
    this.productsCountSpan = '#orderProductsPanelCount';
    this.orderProductsLoading = '#orderProductsLoading';

    // Return block
    this.returnQuantityInput = (row: number) => `[id*=cancel_product_quantity]:nth-child(${row})`;
    this.returnQuantityCheckbox = (row: number) => `tr:nth-child(${row}) div.cancel-product-selector i`;
    this.generateVoucherCheckbox = '#orderProductsPanel div.refund-voucher i';
    this.returnProductButton = '#cancel_product_save';

    // Products table
    this.orderProductsTable = '#orderProductsTable';
    this.orderProductsRowTable = (row: number) => `${this.orderProductsTable} tbody tr:nth-child(${row})`;
    this.orderProductsTableNameColumn = (row: number) => `${this.orderProductsRowTable(row)} td.cellProductName`;
    this.orderProductsTableProductName = (row: number) => `${this.orderProductsTableNameColumn(row)} p.productName`;
    this.orderProductsTableProductReference = (row: number) => `${this.orderProductsTableNameColumn(row)} p.productReference`;
    this.orderProductsTableProductBasePrice = (row: number) => `${this.orderProductsRowTable(row)} td.cellProductUnitPrice`;
    this.orderProductsTableProductQuantity = (row: number) => `${this.orderProductsRowTable(row)} td.cellProductQuantity`;
    this.orderProductsTableProductAvailable = (row: number) => `${this.orderProductsRowTable(row)}
     td.cellProductAvailableQuantity`;
    this.orderProductsTableProductPrice = (row: number) => `${this.orderProductsRowTable(row)} td.cellProductTotalPrice`;
    this.deleteProductButton = (row: number) => `${this.orderProductsRowTable(row)} button.js-order-product-delete-btn`;
    this.editProductButton = (row: number) => `${this.orderProductsRowTable(row)} button.js-order-product-edit-btn`;
    this.productQuantitySpan = (row: number) => `${this.orderProductsRowTable(row)} td.cellProductQuantity span`;
    this.refundProductColumn = `${this.orderProductsTable} th.cellProductRefunded`;

    // Edit row table
    this.orderProductsEditRowTable = `${this.orderProductsTable} tbody tr.editProductRow`;
    this.editProductQuantityInput = `${this.orderProductsEditRowTable} input.editProductQuantity`;
    this.editProductPriceInput = `${this.orderProductsEditRowTable} input.editProductPriceTaxIncl`;
    this.updateProductButton = `${this.orderProductsEditRowTable} button.productEditSaveBtn`;
    this.modalConfirmNewPrice = '#modal-confirm-new-price';
    this.modalConfirmNewPriceSubmitButton = `${this.modalConfirmNewPrice} button.btn-confirm-submit`;

    // Total order
    this.orderTotalPriceSpan = '#orderTotal';
    this.orderWrappingTotal = '#orderWrappingTotal';

    // Add discount
    this.orderTotalProductsSpan = '#orderProductsTotal';
    this.orderTotalDiscountsSpan = '#orderDiscountsTotal';
    this.orderTotalShippingSpan = '#orderShippingTotal';

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
    this.discountListRowTable = (row: number) => `${this.discountListTable} tbody tr:nth-child(${row})`;
    this.discountListNameColumn = (row: number) => `${this.discountListRowTable(row)} td.discountList-name`;
    this.discountListDiscountColumn = (row: number) => `${this.discountListRowTable(row)} td[data-role='discountList-value']`;
    this.discountDeleteIcon = (row: number) => `${this.discountListRowTable(row)} a.delete-cart-rule`;

    // Refund form
    this.refundProductQuantity = (row: number) => `${this.orderProductsRowTable(row)} input[id*='cancel_product_quantity']`;
    this.refundProductAmount = (row: number) => `${this.orderProductsRowTable(row)} input[id*='cancel_product_amount']`;
    this.refundShippingCost = (row: number) => `${this.orderProductsRowTable(row)} input[id*='cancel_product_shipping_amount']`;
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
  async addPartialRefundProduct(
    page: Page,
    productRow: number,
    quantity: number = 0,
    amount: number = 0,
    shipping: number = 0,
  ): Promise<string> {
    await this.waitForVisibleSelector(page, this.refundProductQuantity(1));
    await this.setValue(page, this.refundProductQuantity(productRow), quantity);
    if (amount !== 0) {
      await this.setValue(page, this.refundProductAmount(productRow), amount);
    }
    if (shipping !== 0) {
      await this.setValue(page, this.refundShippingCost(productRow), shipping);
    }
    await this.clickAndWaitForLoadState(page, this.partialRefundSubmitButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  // Methods for product block
  /**
   * Get products number
   * @param page {Frame|Page} Browser tab
   * @returns {Promise<number>}
   */
  async getProductsNumber(page: Frame | Page): Promise<number> {
    return this.getNumberFromText(page, this.productsCountSpan);
  }

  /**
   * Get product name from products table
   * @param page {Page} Browser tab
   * @param row {number} Product row on table
   * @returns {Promise<string>}
   */
  async getProductNameFromTable(page: Page, row: number): Promise<string> {
    return this.getTextContent(page, this.orderProductsTableProductName(row));
  }

  /**
   * Modify product quantity
   * @param page {Page} Browser tab
   * @param row {number} Product row on table
   * @param quantity {number} Quantity to edit
   * @returns {Promise<number>}
   */
  async modifyProductQuantity(page: Page, row: number, quantity: number): Promise<number> {
    await this.dialogListener(page);
    await Promise.all([
      page.locator(this.editProductButton(row)).click(),
      this.waitForVisibleSelector(page, this.editProductQuantityInput),
    ]);
    await this.setValue(page, `${this.editProductQuantityInput}:visible`, quantity);
    await Promise.all([
      page.locator(`${this.updateProductButton}:visible`).first().click(),
      this.waitForVisibleSelector(page, this.editProductQuantityInput),
    ]);
    if (await this.elementVisible(page, this.orderProductsLoading, 2000)) {
      await this.waitForHiddenSelector(page, this.orderProductsLoading);
    }
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
  async modifyProductPrice(page: Page, row: number, price: number): Promise<void> {
    await this.dialogListener(page);

    await this.waitForSelectorAndClick(page, this.editProductButton(row));
    await this.setValue(page, `${this.editProductPriceInput}:visible`, price);

    await Promise.all([
      page.locator(this.updateProductButton).first().click(),
      this.waitForHiddenSelector(page, this.editProductPriceInput),
    ]);

    if (await this.elementVisible(page, this.orderProductsLoading, 1000)) {
      await this.waitForHiddenSelector(page, this.orderProductsLoading);
    }
    await this.waitForVisibleSelector(page, this.orderProductsTableProductBasePrice(row));
  }

  /**
   * Modify product price for multi invoice
   * @param page {Page} Browser tab
   * @param row {number} Product row on table
   * @param price {number} Price to edit
   * @returns {Promise<void>}
   */
  async modifyProductPriceForMultiInvoice(page: Page, row: number, price: number): Promise<void> {
    await this.dialogListener(page);

    await Promise.all([
      page.locator(this.editProductButton(row)).click(),
      this.waitForVisibleSelector(page, this.editProductPriceInput),
    ]);
    await this.setValue(page, `${this.editProductPriceInput}:visible`, price);

    await Promise.all([
      page.locator(this.updateProductButton).first().click(),
      this.waitForVisibleSelector(page, this.modalConfirmNewPrice),
    ]);

    await page.locator(this.modalConfirmNewPriceSubmitButton).click();

    if (await this.elementVisible(page, this.orderProductsLoading, 1000)) {
      await this.waitForHiddenSelector(page, this.orderProductsLoading);
    }

    await this.waitForVisibleSelector(page, this.orderProductsTableProductName(row));
  }

  /**
   * Delete product
   * @param page {Page} Browser tab
   * @param row {number} Product row on table
   * @returns {Promise<string|null>}
   */
  async deleteProduct(page: Page, row: number): Promise<string | null> {
    await this.dialogListener(page);
    await this.closeGrowlMessage(page);
    await Promise.all([
      page.waitForResponse((response) => response.url().includes('/products?_token')),
      this.waitForSelectorAndClick(page, this.deleteProductButton(row)),
    ]);

    return this.getGrowlMessageContent(page);
  }

  /**
   * Get total price from products tab
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getOrderTotalPrice(page: Page): Promise<number> {
    return this.getPriceFromText(page, this.orderTotalPriceSpan, 1000);
  }

  /**
   * Get order wrapping total
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getOrderWrappingTotal(page: Page): Promise<number> {
    return this.getPriceFromText(page, this.orderWrappingTotal);
  }

  /**
   * Get order total discounts
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getOrderTotalProducts(page: Page): Promise<number> {
    return this.getPriceFromText(page, this.orderTotalProductsSpan);
  }

  /**
   * Get order total discounts
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getOrderTotalDiscounts(page: Page): Promise<number> {
    return this.getPriceFromText(page, this.orderTotalDiscountsSpan, 0, false);
  }

  /**
   * Get order total shipping
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getOrderTotalShipping(page: Page): Promise<number> {
    return this.getPriceFromText(page, this.orderTotalShippingSpan, 0, false);
  }

  /**
   * Create new invoice
   * @param page {Page} Browser tab
   * @param invoice {string} The invoice to select from dropdown list
   * @returns {Promise<void>}
   */
  async selectInvoice(page: Page, invoice: string = 'Create a new invoice'): Promise<void> {
    await this.selectByVisibleText(page, this.addProductInvoiceSelect, invoice);
  }

  /**
   * Get invoices list from select options
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getInvoicesFromSelectOptions(page: Page): Promise<string> {
    return this.getTextContent(page, this.addProductInvoiceSelect);
  }

  /**
   * Get carrier name when creating new invoice
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getNewInvoiceCarrierName(page: Page): Promise<string> {
    return this.getTextContent(page, this.addProductNewInvoiceCarrierName);
  }

  /**
   * Is free shipping selected
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isFreeShippingSelected(page: Page): Promise<boolean> {
    return this.isChecked(page, this.addProductNewInvoiceFreeShippingCheckbox);
  }

  /**
   * Select free shipping checkbox
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async selectFreeShippingCheckbox(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.addProductNewInvoiceFreeShippingDiv);
  }

  /**
   * Add product quantity from add product input
   * @param page {Page} Browser tab
   * @param quantity {number} Product quantity to add
   * @returns {Promise<void>}
   */
  async addQuantity(page: Page, quantity: number): Promise<void> {
    await this.setValue(page, this.addProductRowQuantity, quantity);
  }

  /**
   * Set new product price from add product input
   * @param page {Page} Browser tab
   * @param price {float} Value of price to update
   * @returns {Promise<void>}
   */
  async updateProductPrice(page: Page, price: number): Promise<void> {
    await this.setValue(page, this.addProductRowPrice, price);
  }

  /**
   * Add product to cart
   * @param page {Page} Browser tab
   * @param quantity {number} Product quantity to add
   * @param createNewInvoice {boolean} True if we need to create new invoice
   * @returns {Promise<string|null>}
   */
  async addProductToCart(page: Page, quantity: number = 1, createNewInvoice: boolean = false): Promise<string | null> {
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
  async cancelAddProductToCart(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.addProductCancelButton);
  }

  /**
   * Is add button disabled
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isAddButtonDisabled(page: Page): Promise<boolean> {
    return this.elementVisible(page, `${this.addProductAddButton}[disabled]`, 1000);
  }

  /**
   * Is add product table row visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isAddProductTableRowVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.addProductTableRow, 1000);
  }

  /**
   * Get product details
   * @param page {Frame|Page} Browser tab
   * @param row {number} Product row on table
   * @returns {Promise<{total: number, quantity: number, name: string, available: number, basePrice: number}>}
   */
  async getProductDetails(page: Frame | Page, row: number) {
    return {
      orderDetailId: await this.getAttributeContent(page, this.editProductButton(row), 'data-order-detail-id'),
      productId: await this.getAttributeContent(page, this.editProductButton(row), 'data-product-id'),
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
   * Is refunded column visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isRefundedColumnVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.refundProductColumn, 2000);
  }

  /**
   * Search product
   * @param page {Page} Browser tab
   * @param name {string} Product name to search
   * @returns {Promise<void>}
   */
  async searchProduct(page: Page, name: string): Promise<void> {
    await this.waitForSelectorAndClick(page, this.addProductButton);
    await this.setValue(page, this.addProductRowSearch, name);
    await this.waitForSelectorAndClick(page, `${this.addProductTableRow} a`);
  }

  /**
   * Get searched product details
   * @param page {Page} Browser tab
   * @returns {Promise<{stockLocation: string, available: number, price:number;}>}
   */
  async getSearchedProductDetails(page: Page): Promise<{ stockLocation: string; available: number; price: number; }> {
    return {
      stockLocation: await this.getTextContent(page, this.addProductRowStockLocation),
      available: parseInt(await this.getTextContent(page, this.addProductAvailable), 10),
      price: parseFloat(await this.getTextContent(page, this.addProductTotalPrice)),
    };
  }

  /**
   * Get searched product information
   * @param page {Page} Browser tab
   * @returns {Promise<{available: number, price: number}>}
   */
  async getSearchedProductInformation(page: Page): Promise<{ available: number; price: number; }> {
    return {
      available: parseInt(await this.getTextContent(page, this.addProductAvailable), 10),
      price: parseFloat(await this.getTextContent(page, this.addProductTotalPrice)),
    };
  }

  /**
   * Add discount
   * @param page {Page} Browser tab
   * @param discountData {ProductDiscount} Data to set on discount form
   * @returns {Promise<string>}
   */
  async addDiscount(page: Page, discountData: ProductDiscount): Promise<string> {
    await this.waitForSelectorAndClick(page, this.addDiscountButton);
    await this.waitForVisibleSelector(page, this.orderDiscountModal);
    await this.waitForSelectorAndClick(page, this.addOrderCartRuleNameInput);
    await this.setValue(page, this.addOrderCartRuleNameInput, discountData.name);

    if (discountData.type !== 'Free shipping') {
      await this.setValue(page, this.addOrderCartRuleValueInput, discountData.value);
    }
    await this.selectByVisibleText(page, this.addOrderCartRuleTypeSelect, discountData.type);

    await this.waitForVisibleSelector(page, `${this.addOrderCartRuleAddButton}:not([disabled])`);
    await page.locator(this.addOrderCartRuleAddButton).click();
    await this.waitForVisibleSelector(page, this.alertBlock);

    return this.getTextContent(page, this.alertBlock);
  }

  /**
   * Is discount table visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isDiscountListTableVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.discountListTable, 2000);
  }

  /**
   * Get text column from discount table
   * @param page {Page} Browser tab
   * @param column {string} Column name on the table
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async getTextColumnFromDiscountTable(page: Page, column: string, row: number = 1): Promise<string> {
    switch (column) {
      case 'name':
        return this.getTextContent(page, this.discountListNameColumn(row));
      case 'value':
        return this.getTextContent(page, this.discountListDiscountColumn(row));
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
  async deleteDiscount(page: Page, row: number = 1): Promise<string> {
    await this.waitForSelectorAndClick(page, this.discountDeleteIcon(row));

    return this.getTextContent(page, this.alertBlock);
  }

  // Methods for product list pagination
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page): Promise<string> {
    await this.waitForSelectorAndClick(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page): Promise<string> {
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
  async selectPaginationLimit(page: Page, number: number): Promise<boolean> {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);
    await this.waitForVisibleSelector(page, this.orderProductsTableProductName(1));

    return this.elementVisible(page, this.paginationNextLink, 1000);
  }

  // Methods to return products

  /**
   * Set returned product quantity
   * @param page {Page} Browser tab
   * @param row {number} Row in return product table
   * @param quantity {number} Quantity to return
   * @returns {Promise<void>}
   */
  async setReturnedProductQuantity(page: Page, row: number = 1, quantity: number = 1): Promise<void> {
    await this.setValue(page, this.returnQuantityInput(row), quantity);
  }

  /**
   * Check returned quantity
   * @param page {Page} Browser tab
   * @param row {number} Row in return product table
   * @returns {Promise<void>}
   */
  async checkReturnedQuantity(page: Page, row: number = 1): Promise<void> {
    await this.setChecked(page, this.returnQuantityCheckbox(row), true, true);
  }

  /**
   * Check generate voucher
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable generate voucher
   * @returns {Promise<void>}
   */
  async checkGenerateVoucher(page: Page, toEnable: boolean): Promise<void> {
    await this.setChecked(page, this.generateVoucherCheckbox, toEnable, true);
  }

  /**
   * Click on return products
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async clickOnReturnProducts(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.returnProductButton);

    return this.getAlertBlockContent(page);
  }
}

export default new ProductsBlock();
