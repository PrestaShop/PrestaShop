// Import BO pages
import BOBasePage from '@pages/BO/BObasePage';
import addCustomerPage from '@pages/BO/customers/add';

// Import data
import type CustomerData from '@data/faker/customer';
import type ProductData from '@data/faker/product';
import type OrderStatusData from '@data/faker/orderStatus';

import type {Frame, Page} from 'playwright';
import OrderData from '@data/faker/order';

/**
 * Add order page, contains functions that can be used on create order page
 * @class
 * @extends BOBasePage
 */
class AddOrder extends BOBasePage {
  public readonly pageTitle: string;

  public readonly noCustomerFoundText: string;

  public readonly noProductFoundText: string;

  public readonly cartRuleAlreadyExistErrorText: string;

  public readonly noVoucherFoudErrorMessage: string;

  public readonly voucherDisabledErrorMessage: string;

  public readonly emailSendSuccessMessage: string;

  private readonly iframe: string;

  private readonly closeFancyBoxIframe: string;

  private readonly addCustomerLink: string;

  private readonly customerSearchInput: string;

  private readonly customerSearchLoadingNoticeBlock: string;

  private readonly customerSearchEmptyResultBlock: string;

  private readonly customerSearchEmptyResultParagraphe: string;

  private readonly customerSearchFullResultsBlock: string;

  private readonly customerResultsBlock: string;

  private readonly customerCardBlock: (pos: number) => string;

  private readonly customerCardNameTitle: (pos: number) => string;

  private readonly customerCardBody: (pos: number) => string;

  private readonly customerCardChooseButton: (pos: number) => string;

  private readonly customerCardDetailButton: string;

  private readonly checkoutHistoryBlock: string;

  private readonly customerCartsTable: string;

  private readonly customerCartsTableBody: string;

  private readonly customerCartsTableRow: (row: number) => string;

  private readonly customerCartsTableColumn: (column: string, row: number) => string;

  private readonly emptyCartBlock: string;

  private readonly customerCartsTableDetailsButton: (row: number) => string;

  private readonly customerCartsTableUseButton: (row: number) => string;

  private readonly cartBlock: string;

  private readonly ordersTab: string;

  private readonly cartErrorBlock: string;

  private readonly customerOrdersTable: string;

  private readonly customerOrdersTableBody: string;

  private readonly customerOrdersTableRows: string;

  private readonly customerOrdersTableRow: (row: number) => string;

  private readonly customerOrdersTableColumn: (column: string, row: number) => string;

  private readonly orderDetailsButton: (row: number) => string;

  private readonly orderUseButton: (row: number) => string;

  private readonly productSearchInput: string;

  private readonly noProductFoundAlert: string;

  private readonly addProductToCartForm: string;

  private readonly productResultsSelect: string;

  private readonly productQuantityInput: string;

  private readonly productCustomInput: string;

  private readonly currencySelect: string;

  private readonly languageSelect: string;

  private readonly addtoCartButton: string;

  private readonly productsTable: string;

  private readonly productsTableBody: string;

  private readonly productsTableRows: string;

  private readonly productsTableRow: (row: number) => string;

  private readonly productsTableColumn: (column: string, row: number) => string;

  private readonly productTableQuantityColumn: (row: number) => string;

  private readonly productTableImageColumn: (row: number) => string;

  private readonly productTableQuantityStockColumn: (row: number) => string;

  private readonly productTableColumnRemoveButton: (row: number) => string;

  private readonly searchVoucherInput: string;

  private readonly searchCartRuleResultBox: string;

  private readonly searchCartRuleResultFound: string;

  private readonly cartRuleErrorText: string;

  private readonly addVoucherButton: string;

  private readonly vouchersTable: string;

  private readonly vouchersTableBody: string;

  private readonly vouchersTableRows: string;

  private readonly vouchersTableRow: (row: number) => string;

  private readonly vouchersTableColumn: (column: string, row: number) => string;

  private readonly vouchersTableRowRemoveButton: (row: number) => string;

  private readonly deliveryAddressSelect: string;

  private readonly deliveryAddressDetails: string;

  private readonly deliveryAddressEditButton: string;

  private readonly invoiceAddressSelect: string;

  private readonly invoiceAddressDetails: string;

  private readonly invoiceAddressEditButton: string;

  private readonly shippingBlock: string;

  private readonly deliveryOptionSelect: string;

  private readonly totalShippingTaxIncl: string;

  private readonly freeShippingToggleInput: (toggle: number) => string;

  private readonly giftToggleInput: (toggle: number) => string;

  private readonly recycledPackagingToggleInput: (toggle: number) => string;

  private readonly shippingCost: string;

  private readonly giftMessageTextarea: string;

  private readonly summaryBlock: string;

  private readonly totalProducts: string;

  private readonly totalDiscountProduct: string;

  private readonly totalShippingProduct: string;

  private readonly totalTaxesProduct: string;

  private readonly totalTaxExcProduct: string;

  private readonly totalTaxIncProduct: string;

  private readonly orderMessageTextArea: string;

  private readonly paymentMethodSelect: string;

  private readonly paymentMethodSelectResult: string;

  private readonly paymentMethodOption: (paymentMethod: string) => string;

  private readonly orderStatusSelect: string;

  private readonly createOrderButton: string;

  private readonly moreActionsDropDownButton: string;

  private readonly sendOrderMailButton: string;

  private readonly proceedOrderLink: string;

  private readonly summarySuccessMessageBlock: string;

  private readonly totalTaxIncluded: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on create order page
   */
  constructor() {
    super();

    this.pageTitle = `New order • ${global.INSTALL.SHOP_NAME}`;
    this.noCustomerFoundText = 'No customers found';
    this.noProductFoundText = 'No products found';
    this.cartRuleAlreadyExistErrorText = 'This voucher is already in your cart';
    this.noVoucherFoudErrorMessage = 'No voucher was found';
    this.voucherDisabledErrorMessage = 'This voucher is disabled';
    this.emailSendSuccessMessage = 'The email was sent to your customer.';

    // Iframe
    this.iframe = 'iframe.fancybox-iframe';
    this.closeFancyBoxIframe = 'a.fancybox-close';

    // Customer selectors
    this.addCustomerLink = '#customer-add-btn';
    this.customerSearchInput = '#customer-search-input';
    this.customerSearchLoadingNoticeBlock = '#customer-search-loading-notice';

    // Empty results
    this.customerSearchEmptyResultBlock = '#customer-search-empty-result-warn';
    this.customerSearchEmptyResultParagraphe = `${this.customerSearchEmptyResultBlock} .alert-text`;

    // Full results
    this.customerSearchFullResultsBlock = 'div.js-customer-search-results';
    this.customerResultsBlock = `${this.customerSearchFullResultsBlock} div.js-customer-search-result-col`;
    this.customerCardBlock = (pos: number) => `${this.customerSearchFullResultsBlock} `
      + `.js-customer-search-result-col:nth-child(${pos})`;
    this.customerCardNameTitle = (pos: number) => `${this.customerCardBlock(pos)} .js-customer-name`;
    this.customerCardBody = (pos: number) => `${this.customerCardBlock(pos)} .card-body`;
    this.customerCardChooseButton = (pos: number) => `${this.customerCardBlock(pos)} .js-choose-customer-btn`;
    this.customerCardDetailButton = `${this.customerSearchFullResultsBlock} a.js-details-customer-btn`;

    // Checkout history selectors
    this.checkoutHistoryBlock = '#customer-checkout-history';

    // Carts table selectors
    this.customerCartsTable = '#customer-carts-table';
    this.customerCartsTableBody = `${this.customerCartsTable} tbody`;
    this.customerCartsTableRow = (row: number) => `${this.customerCartsTableBody} tr:nth-child(${row})`;
    this.customerCartsTableColumn = (column: string, row: number) => `${this.customerCartsTableRow(row)} td.js-cart-${column}`;
    this.emptyCartBlock = `${this.customerCartsTableBody} div.grid-table-empty`;
    this.customerCartsTableDetailsButton = (row: number) => `${this.customerCartsTableRow(row)} td a.js-cart-details-btn`;
    this.customerCartsTableUseButton = (row: number) => `${this.customerCartsTableRow(row)} td button.js-use-cart-btn`;

    // Cart selectors
    this.cartBlock = '#cart-block';
    this.ordersTab = '#customer-orders-tab';
    this.cartErrorBlock = '#js-cart-error-block';

    // Orders table selectors
    this.customerOrdersTable = '#customer-orders-table';
    this.customerOrdersTableBody = `${this.customerOrdersTable} tbody`;
    this.customerOrdersTableRows = `${this.customerOrdersTableBody} tr`;
    this.customerOrdersTableRow = (row: number) => `${this.customerOrdersTableRows}:nth-child(${row})`;
    this.customerOrdersTableColumn = (column: string, row: number) => `${this.customerOrdersTableRow(row)} td.js-order-${column}`;
    this.orderDetailsButton = (row: number) => `${this.customerOrdersTableRow(row)} td a.js-order-details-btn`;
    this.orderUseButton = (row: number) => `${this.customerOrdersTableRow(row)} td button.js-use-order-btn`;

    // Cart selectors
    this.productSearchInput = '#product-search';
    this.noProductFoundAlert = `${this.cartBlock} .js-no-products-found`;
    this.addProductToCartForm = '#js-add-product-form';
    this.productResultsSelect = '#product-select';
    this.productQuantityInput = '#quantity-input';
    this.productCustomInput = '.js-product-custom-input';
    this.currencySelect = '#js-cart-currency-select';
    this.languageSelect = '#js-cart-language-select';
    this.addtoCartButton = '#add-product-to-cart-btn';
    /* Products table selectors */
    this.productsTable = '#products-table';
    this.productsTableBody = `${this.productsTable} tbody`;
    this.productsTableRows = `${this.productsTableBody} tr`;
    this.productsTableRow = (row: number) => `${this.productsTableRows}:nth-child(${row})`;
    this.productsTableColumn = (column: string, row: number) => `${this.productsTableRow(row)} td.js-product-${column}`;
    this.productTableQuantityColumn = (row: number) => `${this.productsTableRow(row)} td input.js-product-qty-input`;
    this.productTableImageColumn = (row: number) => `${this.productsTableRow(row)} td img.js-product-image`;
    this.productTableQuantityStockColumn = (row: number) => `${this.productsTableRow(row)} td span.js-product-qty-stock`;
    this.productTableColumnRemoveButton = (row: number) => `${this.productsTableRow(row)} td button.js-product-remove-btn`;

    // Vouchers block selectors
    this.searchVoucherInput = '#search-cart-rules-input';
    this.searchCartRuleResultBox = '#search-cart-rules-result-box';
    this.searchCartRuleResultFound = '#cart-rules-search-block li.js-found-cart-rule.found-cart-rule';
    this.cartRuleErrorText = '#js-cart-rule-error-text';
    this.addVoucherButton = '#js-add-cart-rule-btn';
    this.vouchersTable = '#cart-rules-table';
    this.vouchersTableBody = `${this.vouchersTable} tbody`;
    this.vouchersTableRows = `${this.vouchersTableBody} tr`;
    this.vouchersTableRow = (row: number) => `${this.vouchersTableRows}:nth-child(${row})`;
    this.vouchersTableColumn = (column: string, row: number) => `${this.vouchersTableRow(row)} td.js-cart-rule-${column}`;
    this.vouchersTableRowRemoveButton = (row: number) => `${this.vouchersTableRows}:nth-child(${row})`
      + ' td button.js-cart-rule-delete-btn';

    // Addresses form selectors
    this.deliveryAddressSelect = '#delivery-address-select';
    this.deliveryAddressDetails = '#delivery-address-details';
    this.deliveryAddressEditButton = '#js-delivery-address-edit-btn';
    this.invoiceAddressSelect = '#invoice-address-select';
    this.invoiceAddressDetails = '#invoice-address-details';
    this.invoiceAddressEditButton = '#js-invoice-address-edit-btn';

    // Shipping form selectors
    this.shippingBlock = '#shipping-block';
    this.deliveryOptionSelect = '#delivery-option-select';
    this.totalShippingTaxIncl = '.js-total-shipping-tax-inc';
    this.freeShippingToggleInput = (toggle: number) => `#free-shipping_${toggle}`;
    this.giftToggleInput = (toggle: number) => `#is-gift_${toggle}`;
    this.recycledPackagingToggleInput = (toggle: number) => `#recycled-packaging_${toggle}`;
    this.shippingCost = `${this.shippingBlock} span.js-total-shipping-tax-inc`;
    this.giftMessageTextarea = '#cart_gift_message';

    // Summary selectors
    this.summaryBlock = '#summary-block';
    this.totalProducts = `${this.summaryBlock} .js-total-products`;
    this.totalDiscountProduct = `${this.summaryBlock} .js-total-discounts`;
    this.totalShippingProduct = `${this.summaryBlock} .js-total-shipping`;
    this.totalTaxesProduct = `${this.summaryBlock} .js-total-taxes`;
    this.totalTaxExcProduct = `${this.summaryBlock} .js-total-without-tax`;
    this.totalTaxIncProduct = `${this.summaryBlock} div:nth-child(6)`;
    this.orderMessageTextArea = '#cart_summary_order_message';
    this.paymentMethodSelect = `${this.summaryBlock} #select2-cart_summary_payment_module-container`;
    this.paymentMethodSelectResult = 'body span.select2-results';
    this.paymentMethodOption = (paymentMethod: string) => '#select2-cart_summary_payment_module-results '
      + `li[data-select2-id*='${paymentMethod}']`;
    this.orderStatusSelect = '#cart_summary_order_state';
    this.createOrderButton = '#create-order-button';
    this.moreActionsDropDownButton = '#dropdown-menu-actions';
    this.sendOrderMailButton = '#js-send-process-order-email-btn';
    this.proceedOrderLink = '#js-process-order-link';
    this.summarySuccessMessageBlock = '#js-summary-success-block';
    this.totalTaxIncluded = '#summary-block span.js-total-with-tax';
  }

  /* Customer functions */

  /**
   * Fill customer search input and wait for results to load
   * @param page {Page} Browser tab
   * @param customer {string} Customer name/email to search
   * @returns {Promise<void>}
   */
  async searchCustomer(page: Page, customer: string): Promise<void> {
    await this.setValue(page, this.customerSearchInput, customer);

    await this.waitForHiddenSelector(page, this.customerSearchLoadingNoticeBlock);
  }

  /**
   * Get Error message when no customer was found after searching
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getNoCustomerFoundError(page: Page): Promise<string> {
    return this.getTextContent(page, this.customerSearchEmptyResultParagraphe);
  }

  /**
   * Get customer search number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getCustomersSearchNumber(page: Page): Promise<number> {
    await this.waitForVisibleSelector(page, this.customerCardNameTitle(1));

    return page.$$eval(this.customerResultsBlock, (divs: HTMLElement[]) => divs.length);
  }

  /**
   * Get customer name from card result
   * @param page {Page} Browser tab
   * @param cardPosition {number} Position of the card in results
   * @returns {Promise<string>}
   */
  getCustomerNameFromResult(page: Page, cardPosition: number = 1): Promise<string> {
    return this.getTextContent(page, this.customerCardNameTitle(cardPosition));
  }

  /**
   * Get customer card body
   * @param page {Page} Browser tab
   * @param cardPosition {number} Position of the card in results
   * @returns {Promise<string>}
   */
  getCustomerCardBody(page: Page, cardPosition: number = 1): Promise<string> {
    return this.getTextContent(page, this.customerCardBody(cardPosition));
  }

  /**
   * Click on add new customer and new customer iFrame
   * @param page {Page} Browser tab
   * @param customerData {CustomerData} Customer data fake object
   * @returns {Promise<string>}
   */
  async addNewCustomer(page: Page, customerData: CustomerData): Promise<string> {
    await page.click(this.addCustomerLink);
    await this.waitForVisibleSelector(page, this.iframe);

    const customerFrame = await page.frame({url: /sell\/customers\/new/gmi});

    if (!customerFrame) {
      throw new Error('The customerFrame doesn\'t exist!');
    }

    await addCustomerPage.closeSfToolBar(customerFrame!);
    await addCustomerPage.createEditCustomer(customerFrame!, customerData, false);

    await this.waitForHiddenSelector(page, this.iframe);

    return this.getCustomerNameFromResult(page);
  }

  /**
   * Click on choose customer in list
   * @param page {Page} Browser tab
   * @param cardPosition {number} Position of customer to choose on the list
   * @returns {Promise<boolean>}
   */
  async chooseCustomer(page: Page, cardPosition: number = 1): Promise<boolean> {
    await page.click(this.customerCardChooseButton(cardPosition));

    await Promise.all([
      this.waitForHiddenSelector(page, this.customerCardChooseButton(cardPosition)),
      this.waitForVisibleSelector(page, this.checkoutHistoryBlock),
    ]);

    return this.elementVisible(page, this.checkoutHistoryBlock, 1000);
  }

  /**
   * Click on details button
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnDetailsButton(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.customerCardDetailButton);

    return this.elementVisible(page, this.iframe, 2000);
  }

  /**
   * Get customer Iframe
   * @param page {Page} Browser tab
   * @param customerID {number} Id of customer to check
   * @returns {Frame | null}
   */
  getCustomerIframe(page: Page, customerID: number): Frame | null {
    return page.frame({url: new RegExp(`sell/customers/${customerID}/view`, 'gmi')});
  }

  /**
   * Close iframe
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeIframe(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.closeFancyBoxIframe);

    return this.elementNotVisible(page, this.iframe, 3000);
  }

  /* Carts table methods */

  /**
   * Get text when carts table is empty
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getTextWhenCartsTableIsEmpty(page: Page): Promise<string> {
    await page.waitForTimeout(2000);
    return this.getTextContent(page, this.emptyCartBlock, true);
  }

  /**
   * Get text column from carts table
   * @param page {Page} Browser tab
   * @param column {String} Column name from table
   * @param row {Number} Row on table
   * @returns {Promise<string>}
   */
  async getTextColumnFromCartsTable(page: Page, column: string, row: number = 1): Promise<string> {
    return this.getTextContent(page, this.customerCartsTableColumn(column, row));
  }

  /**
   * Click on cart details button
   * @param page {Page} Browser tab
   * @param row {Number} Row on table
   * @returns {Promise<Boolean>}
   */
  async clickOnCartDetailsButton(page: Page, row: number = 1): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.customerCartsTableDetailsButton(row));

    return this.elementVisible(page, this.iframe, 2000);
  }

  /**
   * Get shopping cart Iframe
   * @param page {Page} Browser tab
   * @param cartId {number} Id of customer to check
   * @returns {*}
   */
  getShoppingCartIframe(page: Page, cartId: number): Frame | null {
    return page.frame({url: new RegExp(`sell/orders/carts/${cartId}/view`, 'gmi')});
  }

  /**
   * Click on cart use button
   * @param page {Page} Browser tab
   * @param row {Number} Row on table
   * @returns {Promise<Boolean>}
   */
  async clickOnCartUseButton(page: Page, row: number = 1): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.customerCartsTableUseButton(row));

    return this.elementVisible(page, this.productsTable, 1000);
  }

  /* Carts & Orders methods */

  /**
   * Click on orders tab
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnOrdersTab(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.ordersTab);

    return this.elementVisible(page, this.customerOrdersTable, 1000);
  }

  /**
   * Get orders number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getOrdersNumber(page: Page): Promise<number> {
    await this.waitForVisibleSelector(page, this.customerOrdersTable);

    return page.$$eval(this.customerOrdersTableRows, (trs: HTMLElement[]) => trs.length);
  }

  /**
   * Get text column from orders table
   * @param page {Page} Browser tab
   * @param column {string} Column name in orders table
   * @param row {number} Column row in orders table
   * @returns {Promise<string>}
   */
  async getTextFromOrdersTable(page: Page, column: string, row: number = 1): Promise<string> {
    return this.getTextContent(page, this.customerOrdersTableColumn(column, row));
  }

  /**
   * Click on order details button
   * @param page {Page} Browser tab
   * @param row {number} Column row in orders table
   * @returns {Promise<boolean>}
   */
  async clickOnOrderDetailsButton(page: Page, row: number = 1): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.orderDetailsButton(row));

    return this.elementVisible(page, this.iframe, 2000);
  }

  /**
   * Get order Iframe
   * @param page {Page} Browser tab
   * @param orderID {number} Id of order to check
   * @returns {*}
   */
  getOrderIframe(page: Page, orderID: number): Frame | null {
    return page.frame({url: new RegExp(`sell/orders/${orderID}/view`, 'gmi')});
  }

  /**
   * Click on order use button
   * @param page {Page} Browser tab
   * @param row {number} Row in orders table
   * @returns {Promise<boolean>}
   */
  async clickOnOrderUseButton(page: Page, row: number = 1): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.orderUseButton(row));

    return this.elementVisible(page, this.productsTable, 1000);
  }

  /* Cart methods */

  /**
   * Search a product and get error alert
   * @param page {Page} Browser tab
   * @param productName {string} Product name to search
   * @returns {Promise<string>}
   */
  async searchProductAndGetAlert(page: Page, productName: string): Promise<string> {
    await this.setValue(page, this.productSearchInput, productName);

    return this.getTextContent(page, this.noProductFoundAlert);
  }

  /**
   * Add product to cart and get alert
   * @param page {Page} Browser tab
   * @param productToSearch {string} Product name to search with
   * @param productToSelect {string} Product name to select
   * @param quantity {number} Product quantity to add to the cart
   * @returns {Promise<string>}
   */
  async AddProductToCartAndGetAlert(
    page: Page,
    productToSearch: string,
    productToSelect: string,
    quantity: number = 1,
  ): Promise<string> {
    // Search product
    await this.setValue(page, this.productSearchInput, productToSearch);
    await this.waitForVisibleSelector(page, this.addProductToCartForm);

    // Fill add product form
    await this.selectByVisibleText(page, this.productResultsSelect, productToSelect);
    await this.setValue(page, this.productQuantityInput, quantity);

    // Add to cart
    await page.click(this.addtoCartButton);

    // Return error message
    return this.getTextContent(page, this.cartErrorBlock);
  }

  /**
   * Add quantity and add product to cart
   * @param page {Page} Browser tab
   * @param quantity {number} Product quantity to add to the cart
   * @param row {number} Row on products table
   * @returns {Promise<void>}
   */
  async addProductQuantity(page: Page, quantity: number, row: number): Promise<void> {
    await this.setValue(page, this.productTableQuantityColumn(row), quantity);

    await page.click(this.productsTableColumn('total-price', row));

    await page.waitForTimeout(2000);
  }

  /**
   * Add product to cart
   * @param page {Page} Browser tab
   * @param productToSearch {ProductData} Product data to search with
   * @param productToSelect {string} Product name to select
   * @param quantity {number} Product quantity to add to the cart
   * @param customizedValue {string}
   * @returns {Promise<void>}
   */
  async addProductToCart(
    page: Page,
    productToSearch: ProductData,
    productToSelect: string,
    quantity: number = 1,
    customizedValue: string = '',
  ) {
    // Search product
    await this.setValue(page, this.productSearchInput, productToSearch.name);
    await this.waitForVisibleSelector(page, this.addProductToCartForm);

    // Fill add product form
    await this.selectByVisibleText(page, this.productResultsSelect, productToSelect);
    if (await this.elementVisible(page, this.productCustomInput, 1000)) {
      await this.setValue(page, this.productCustomInput, customizedValue);
    }
    await this.setValue(page, this.productQuantityInput, quantity);

    // Add to cart
    await page.click(this.addtoCartButton);

    await page.waitForTimeout(500);
    await this.waitForVisibleSelector(page, this.productsTable);
  }

  /**
   * Get product details from table
   * @param page {Page} Browser tab
   * @param row {number} Row on product table
   * @returns {Promise<{reference: string, image: string, quantityMax: number, price: number, description: string,
   * quantityMin: number}>}
   */
  async getProductDetailsFromTable(page: Page, row: number = 1) {
    return {
      image: await this.getAttributeContent(page, this.productTableImageColumn(row), 'src'),
      description: await this.getTextContent(page, this.productsTableColumn('definition-td', row)),
      reference: await this.getTextContent(page, this.productsTableColumn('ref', row)),
      quantityMin: parseInt(await this.getAttributeContent(page, this.productTableQuantityColumn(row), 'min') ?? '', 10),
      quantityMax: parseInt(await this.getTextContent(page, this.productTableQuantityStockColumn(row)), 10),
      price: parseFloat(await this.getTextContent(page, this.productsTableColumn('total-price', row))),
    };
  }

  /**
   * Wait for visible product language
   * @param page {Page} Browser tab
   * @param row {number} Row on product table
   * @param image {string} Image file name to check
   * @returns {Promise<void>}
   */
  async waitForVisibleProductImage(page: Page, row: number, image: string): Promise<void> {
    await this.waitForVisibleSelector(page, `${this.productTableImageColumn(row)}[src*='${image}']`);
  }

  /**
   * Get product gift details from table
   * @param page {Page} Browser tab
   * @param row {number} Row on product table
   * @returns {Promise<{reference: string, image: string, quantity: number, price: string, description: string,
   * basePrice: string}>}
   */
  async getProductGiftDetailsFromTable(page: Page, row: number = 1) {
    return {
      image: await this.getAttributeContent(page, `${this.productsTableRow(row)} td img.js-product-image`, 'src'),
      description: await this.getTextContent(page, this.productsTableColumn('definition-td', row)),
      reference: await this.getTextContent(page, this.productsTableColumn('ref', row)),
      basePrice: await this.getTextContent(page, `${this.productsTableRow(row)} td:nth-child(4)`),
      quantity: parseInt(await this.getTextContent(page, `${this.productsTableColumn('gift-qty', row)}`), 10),
      price: await this.getTextContent(page, this.productsTableColumn('total-price', row)),
    };
  }

  /**
   * Is product table row visible
   * @param page {Page} Browser tab
   * @param row {number} Row on products table
   * @returns {Promise<boolean>}
   */
  isProductTableRowNotVisible(page: Page, row: number): Promise<boolean> {
    return this.elementNotVisible(page, this.productsTableRow(row), 1000);
  }

  /**
   * Remove product
   * @param page {Page} Browser tab
   * @param row {number} Row on product table
   * @returns {Promise<boolean>}
   */
  async removeProduct(page: Page, row: number = 1): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.productTableColumnRemoveButton(row));

    return this.elementNotVisible(page, this.productsTableColumn('total-price', row), 2000);
  }

  /**
   * Is product not visible in the cart
   * @param page {Page} Browser tab
   * @param row {number} Row on cart table
   * @returns {Promise<boolean>}
   */
  async isProductNotVisibleInCart(page: Page, row: number): Promise<boolean> {
    return this.elementNotVisible(page, this.productsTableColumn('definition-td', row), 2000);
  }

  /**
   * Select another currency
   * @param page {Page} Browser tab
   * @param currency {string} Currency to select
   * @returns {Promise<void>}
   */
  async selectAnotherCurrency(page: Page, currency: string): Promise<void> {
    await this.selectByVisibleText(page, this.currencySelect, currency);

    await page.waitForTimeout(2000);
  }

  /**
   * Select another language
   * @param page {Page} Browser tab
   * @param language {string} Language to select
   * @returns {Promise<void>}
   */
  async selectAnotherLanguage(page: Page, language: string): Promise<void> {
    await this.selectByVisibleText(page, this.languageSelect, language);
  }

  /* Vouchers methods */

  /**
   * Is voucher table not visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isVouchersTableNotVisible(page: Page): Promise<boolean> {
    return this.elementNotVisible(page, this.vouchersTable, 1000);
  }

  /**
   * Search and select voucher
   * @param page {Page} Browser tab
   * @param voucherName {string} Voucher name to search
   * @returns {Promise<string>}
   */
  async searchVoucher(page: Page, voucherName: string): Promise<string> {
    await this.setValue(page, this.searchVoucherInput, voucherName);
    const cartRuleResult = await this.getTextContent(page, this.searchCartRuleResultBox);

    if (await this.elementVisible(page, this.searchCartRuleResultFound, 500)) {
      await this.waitForSelectorAndClick(page, this.searchCartRuleResultBox);
    }

    return cartRuleResult;
  }

  /**
   * Get cart rule error text
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getCartRuleErrorText(page: Page): Promise<string> {
    return this.getTextContent(page, this.cartRuleErrorText);
  }

  /**
   * Click on add voucher button
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnAddVoucherButton(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.addVoucherButton);
    await this.waitForVisibleSelector(page, this.iframe);

    return this.elementVisible(page, this.iframe, 2000);
  }

  /**
   * Get create voucher iframe
   * @param page {Page} Browser tab
   * @returns {Promise<*>}
   */
  getCreateVoucherIframe(page: Page): Frame | null {
    return page.frame({
      url: /controller=AdminCartRules&liteDisplaying=1&submitFormAjax=1&addcart_rule=1/gmi,
    });
  }

  /**
   * Get voucher details from table
   * @param page {Page} Browser tab
   * @param row {number} Row on vouchers table
   * @returns {Promise<{name: string, description: string, value: number}>}
   */
  async getVoucherDetailsFromTable(page: Page, row: number = 1) {
    return {
      name: await this.getTextContent(page, this.vouchersTableColumn('name', row)),
      description: await this.getTextContent(page, this.vouchersTableColumn('description', row)),
      value: parseFloat(await this.getTextContent(page, this.vouchersTableColumn('value', row))),
    };
  }

  /**
   * Remove voucher
   * @param page {Page} Browser tab
   * @param row {number} Row on vouchers table
   * @returns {Promise<void>}
   */
  async removeVoucher(page: Page, row: number = 1): Promise<void> {
    await this.waitForSelectorAndClick(page, this.vouchersTableRowRemoveButton(row));
  }

  /* Addresses methods */

  /**
   * Choose addresses in form
   * @param page {Page} Browser tab
   * @param deliveryAddress {string} Delivery address to choose
   * @param invoiceAddress {string} Invoice address to choose
   * @returns {Promise<void>}
   */
  async chooseAddresses(page: Page, deliveryAddress: string, invoiceAddress: string): Promise<void> {
    await this.selectByVisibleText(page, this.deliveryAddressSelect, deliveryAddress);
    await this.selectByVisibleText(page, this.invoiceAddressSelect, invoiceAddress);
  }

  /**
   * Get delivery address list
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getDeliveryAddressList(page: Page): Promise<string> {
    await this.waitForSelectorAndClick(page, this.deliveryAddressSelect);

    return this.getTextContent(page, this.deliveryAddressSelect);
  }

  /**
   * Get delivery address details
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getDeliveryAddressDetails(page: Page): Promise<string> {
    await page.waitForTimeout(3000);

    return this.getTextContent(page, this.deliveryAddressDetails);
  }

  /**
   * Choose delivery address
   * @param page {Page} Browser tab
   * @param deliveryAddress {string} Delivery address to choose
   * @returns {Promise<string>}
   */
  async chooseDeliveryAddress(page: Page, deliveryAddress: string): Promise<string> {
    await this.selectByVisibleText(page, this.deliveryAddressSelect, deliveryAddress);

    return this.getDeliveryAddressDetails(page);
  }

  /**
   * Get invoice address details
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getInvoiceAddressDetails(page: Page): Promise<string> {
    await page.waitForTimeout(2000);

    return this.getTextContent(page, this.invoiceAddressDetails);
  }

  /**
   * Choose invoice address
   * @param page {Page} Browser tab
   * @param invoiceAddress {string} Invoice address to choose
   * @returns {Promise<string>}
   */
  async chooseInvoiceAddress(page: Page, invoiceAddress: string): Promise<string> {
    await this.selectByVisibleText(page, this.invoiceAddressSelect, invoiceAddress);

    return this.getInvoiceAddressDetails(page);
  }

  /**
   * Click on edit delivery address
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnEditDeliveryAddressButton(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.deliveryAddressEditButton);

    return this.elementVisible(page, this.iframe, 2000);
  }

  /**
   * Click on edit invoice address
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnEditInvoiceAddressButton(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.invoiceAddressEditButton);

    return this.elementVisible(page, this.iframe, 2000);
  }

  /**
   * Get edit address Iframe
   * @param page {Page} Browser tab
   * @returns {Promise<*>}
   */
  getEditAddressIframe(page: Page): Frame | null {
    return page.frame({url: /sell\/addresses\/cart\//gmi});
  }

  /**
   * Click on add new address
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnAddNewAddressButton(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, '#js-add-address-btn');

    return this.elementVisible(page, this.iframe, 2000);
  }

  /**
   * Get add address Iframe
   * @param page {Page} Browser tab
   * @returns {Promise<*>}
   */
  getAddAddressIframe(page: Page): Frame | null {
    return page.frame({url: /sell\/addresses\/new\?/gmi});
  }

  /* Shipping methods */

  /**
   * Is shipping block visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isShippingBlockVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.shippingBlock, 1000);
  }

  /**
   * Fill delivery option form
   * @param page {Page} Browser tab
   * @param deliveryOptionName {string} Delivery option name to choose
   * @param isFreeShipping {boolean} True if we want a free shipping
   * @returns {Promise<string>}
   */
  async setDeliveryOption(page: Page, deliveryOptionName: string, isFreeShipping: boolean = false): Promise<string> {
    await this.selectByVisibleText(page, this.deliveryOptionSelect, deliveryOptionName);
    await page.waitForTimeout(1000);
    await this.setFreeShipping(page, isFreeShipping);
    if (isFreeShipping) {
      await this.waitForVisibleSelector(page, this.vouchersTable);
    }
    await page.waitForTimeout(1000);

    return this.getTextContent(page, this.totalShippingTaxIncl);
  }

  /**
   * Get Delivery Option Selected
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getDeliveryOption(page: Page): Promise<string> {
    return this.getTextContent(page, `${this.deliveryOptionSelect} option[selected='selected']`, false);
  }

  /**
   * Get Shipping Cost
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getShippingCost(page: Page): Promise<string> {
    await page.waitForTimeout(1000);

    return this.getTextContent(page, this.shippingCost);
  }

  /**
   * Enable/disable free shipping
   * @param page {Page} Browser tab
   * @param isEnabled {boolean} True if we need to enable free shipping
   * @returns {Promise<void>}
   */
  async setFreeShipping(page: Page, isEnabled: boolean): Promise<void> {
    await page.$eval(this.freeShippingToggleInput(isEnabled ? 1 : 0), (el: HTMLElement) => el.click());
  }

  /**
   * Enable/disable recycled packaging
   * @param page {Page} Browser tab
   * @param isEnabled {boolean} True if we need to enable recycled packaging
   * @returns {Promise<void>}
   */
  async setRecycledPackaging(page: Page, isEnabled: boolean): Promise<void> {
    await this.setChecked(page, this.recycledPackagingToggleInput(isEnabled ? 1 : 0), isEnabled);
    await page.waitForTimeout(3000);
  }

  /**
   * Enable/disable gift
   * @param page {Page} Browser tab
   * @param isEnabled {boolean} True if we need to enable gift
   * @returns {Promise<void>}
   */
  async setGift(page: Page, isEnabled: boolean): Promise<void> {
    await this.setChecked(page, this.giftToggleInput(isEnabled ? 1 : 0), isEnabled);
    await page.waitForTimeout(3000);
  }

  /**
   * Set gift message
   * @param page {Page} Browser tab
   * @param giftMessage {string} Gift message text to set on textarea
   * @returns {Promise<void>}
   */
  async setGiftMessage(page: Page, giftMessage: string): Promise<void> {
    await this.setValue(page, this.giftMessageTextarea, giftMessage);
    await page.waitForTimeout(2000);
  }

  /* Summary methods */

  /**
   * Is summary block visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isSummaryBlockVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.summaryBlock, 2000);
  }

  /**
   * Get Total
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getTotal(page: Page): Promise<string> {
    return this.getTextContent(page, this.totalTaxIncluded);
  }

  /**
   * Get summary block details
   * @param page {Page} Browser tab
   * @returns {Promise<{totalTaxIncluded: string, totalVouchers: string, totalTaxes: string, totalProducts: string,
   * totalTaxExcluded: string, totalShipping: string}>}
   */
  async getSummaryDetails(page: Page) {
    return {
      totalProducts: await this.getTextContent(page, this.totalProducts),
      totalVouchers: await this.getTextContent(page, this.totalDiscountProduct),
      totalShipping: await this.getTextContent(page, this.totalShippingProduct),
      totalTaxes: await this.getTextContent(page, this.totalTaxesProduct),
      totalTaxExcluded: await this.getTextContent(page, this.totalTaxExcProduct),
      totalTaxIncluded: await this.getTextContent(page, this.totalTaxIncProduct),
    };
  }

  /**
   * Set order message
   * @param page {Page} Browser tab
   * @param message {string} Message text to set
   * @returns {Promise<void>}
   */
  async setOrderMessage(page: Page, message: string): Promise<void> {
    await this.setValue(page, this.orderMessageTextArea, message);
  }

  /**
   * Set more actions "Pre Filled Order"
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async setMoreActionsPreFilledOrder(page: Page): Promise<string> {
    await this.waitForSelectorAndClick(page, this.moreActionsDropDownButton);

    await this.waitForSelectorAndClick(page, this.sendOrderMailButton);

    return this.getTextContent(page, this.summarySuccessMessageBlock);
  }

  /**
   * Set more actions "Proceed to Checkout"
   * @param page {Page} Browser tab
   * @returns {Promise<Page>}
   */
  async setMoreActionsProceedToCheckout(page: Page): Promise<Page> {
    await this.waitForSelectorAndClick(page, this.moreActionsDropDownButton);
    return this.openLinkWithTargetBlank(page, this.proceedOrderLink, 'body a');
  }

  /**
   * Set payment method
   * @param page {Page} Browser tab
   * @param paymentMethodModuleName {string} Payment method to choose
   * @returns {Promise<void>}
   */
  async setPaymentMethod(page: Page, paymentMethodModuleName: string): Promise<void> {
    await this.waitForSelectorAndClick(page, this.paymentMethodSelect);
    await this.waitForVisibleSelector(page, this.paymentMethodSelectResult);
    await page.click(this.paymentMethodOption(paymentMethodModuleName));
  }

  /**
   * Set order status
   * @param page {Page} Browser tab
   * @param orderStatus {OrderStatusData} Order status to choose
   * @returns {Promise<void>}
   */
  async setOrderStatus(page: Page, orderStatus: OrderStatusData): Promise<void> {
    await this.selectByVisibleText(page, this.orderStatusSelect, orderStatus.name);
  }

  /**
   * Click on create order button
   * @param page {Page} Browser tab
   * @param waitForNavigation {boolean} True if we need save and waitForNavigation, false if not
   * @returns {Promise<boolean>}
   */
  async clickOnCreateOrderButton(page: Page, waitForNavigation: boolean = true): Promise<boolean> {
    if (waitForNavigation) {
      await this.clickAndWaitForNavigation(page, this.createOrderButton);
    } else {
      await this.waitForSelectorAndClick(page, this.createOrderButton);
    }

    return this.elementNotVisible(page, this.createOrderButton, 2000);
  }

  /**
   * Set summary block
   * @param page {Page} Browser tab
   * @param paymentMethodModuleName {string} Payment method to choose
   * @param orderStatus {OrderStatusData} Order status to choose
   * @returns {Promise<void>}
   */
  async setSummaryAndCreateOrder(page: Page, paymentMethodModuleName: string, orderStatus: OrderStatusData): Promise<void> {
    await this.setPaymentMethod(page, paymentMethodModuleName);
    await this.setOrderStatus(page, orderStatus);
    await this.clickOnCreateOrderButton(page);
  }

  /* All form methods */

  /**
   * Create order with existing customer
   * @param page {Page} Browser tab
   * @param orderToMake {OrderData} Order data to create
   * @param isNewCustomer {boolean} True if the customer is new
   * @returns {Promise<void>}
   */
  async createOrder(page: Page, orderToMake: OrderData, isNewCustomer: boolean = false): Promise<void> {
    // Choose customer
    // If it's a new customer, the creation of customer should be done in test
    // with add customer page
    if (!isNewCustomer) {
      await this.searchCustomer(page, orderToMake.customer.email);
    }

    // Choose customer after search or creation
    await this.chooseCustomer(page, 1);

    // Add products to carts
    for (let i = 0; i < orderToMake.products.length; i++) {
      await this.addProductToCart(
        page, orderToMake.products[i].product, orderToMake.products[i].product.name, orderToMake.products[i].quantity);
    }

    // Choose address
    await this.chooseAddresses(page, orderToMake.deliveryAddress.name, orderToMake.invoiceAddress.name);

    // Choose delivery options
    await this.setDeliveryOption(page, orderToMake.deliveryOption.name, orderToMake.deliveryOption.freeShipping);

    // Choose payment method
    await this.setPaymentMethod(page, orderToMake.paymentMethod.moduleName);

    // Set order status
    await this.setOrderStatus(page, orderToMake.status);

    // Create the order
    await this.clickAndWaitForNavigation(page, this.createOrderButton);
  }
}

export default new AddOrder();
