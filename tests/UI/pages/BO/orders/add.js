require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

// Needed to create customer in orders page
const addCustomerPage = require('@pages/BO/customers/add');

// Needed to check cart Iframe
const viewCartPage = require('@pages/BO/orders/shoppingCarts/view');

/**
 * Add order page, contains functions that can be used on create order page
 * @class
 * @extends BOBasePage
 */
class AddOrder extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on create order page
   */
  constructor() {
    super();

    this.pageTitle = 'Create order •';
    this.noCustomerFoundText = 'No customers found';

    // Iframe
    this.iframe = 'iframe.fancybox-iframe';
    this.closeIframe = 'a.fancybox-close';

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
    this.customerCardBlock = pos => `${this.customerSearchFullResultsBlock} `
      + `.js-customer-search-result-col:nth-child(${pos})`;
    this.customerCardNameTitle = pos => `${this.customerCardBlock(pos)} .js-customer-name`;
    this.customerCardBody = pos => `${this.customerCardBlock(pos)} .card-body`;
    this.customerCardChooseButton = pos => `${this.customerCardBlock(pos)} .js-choose-customer-btn`;
    this.customerCardDetailButton = `${this.customerSearchFullResultsBlock} a.js-details-customer-btn`;

    // Checkout history selectors
    this.checkoutHistoryBlock = '#customer-checkout-history';

    // Carts table selectors
    this.customerCartsTable = '#customer-carts-table';
    this.customerCartsTableBody = `${this.customerCartsTable} tbody`;
    this.customerCartsTableRow = row => `${this.customerCartsTableBody} tr:nth-child(${row})`;
    this.customerCartsTableColumn = (column, row) => `${this.customerCartsTableRow(row)} td.js-cart-${column}`;
    this.emptyCartBlock = `${this.customerCartsTableBody} div.grid-table-empty`;
    this.customerCartsTableDetailsButton = row => `${this.customerCartsTableRow(row)} td a.js-cart-details-btn`;

    // View Carts iframe
    this.cartsIframe = 'iframe.fancybox-iframe';


    // Cart selectors
    this.cartBlock = '#cart-block';
    this.productSearchInput = '#product-search';
    this.addProductToCartForm = '#js-add-product-form';
    this.productResultsSelect = '#product-select';
    this.productQuantityInput = '#quantity-input';
    this.addtoCartButton = '#add-product-to-cart-btn';
    this.productsTable = '#products-table';
    this.ordersTab = '#customer-orders-tab';
    this.customerOrdersTable = '#customer-orders-table';
    this.customerOrdersTableBody = `${this.customerOrdersTable} tbody`;
    this.customerOrdersTableRows = `${this.customerOrdersTableBody} tr`;
    this.customerOrdersTableRow = row => `${this.customerOrdersTableRows}:nth-child(${row})`;
    this.customerOrdersTableColumn = (column, row) => `${this.customerOrdersTableRow(row)} td.js-order-${column}`;
    this.orderDetailsButton = row => `${this.customerOrdersTableRow(row)} td a.js-order-details-btn`;
    this.orderUseButton = row => `${this.customerOrdersTableRow(row)} td button.js-use-order-btn`;

    // Addresses form selectors
    this.deliveryAddressSelect = '#delivery-address-select';
    this.invoiceAddressSelect = '#invoice-address-select';

    // Shipping form selectors
    this.deliveryOptionSelect = '#delivery-option-select';
    this.freeShippingToggleInput = toggle => `#free-shipping_${toggle}`;

    // Summary selectors
    this.paymentMethodSelect = '#cart_summary_payment_module';
    this.orderStatusSelect = '#cart_summary_order_state';
    this.createOrderButton = '#create-order-button';
  }

  /* Customer functions */

  /**
   * Fill customer search input and wait for results to load
   * @param page {Page} Browser tab
   * @param customer {string} Customer name/email to search
   * @returns {Promise<void>}
   */
  async searchCustomer(page, customer) {
    await this.setValue(page, this.customerSearchInput, customer);

    await this.waitForHiddenSelector(page, this.customerSearchLoadingNoticeBlock);
  }

  /**
   * Get Error message when when no customer was found after searching
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getNoCustomerFoundError(page) {
    return this.getTextContent(page, this.customerSearchEmptyResultParagraphe);
  }

  /**
   * Get customer search number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getCustomersSearchNumber(page) {
    await this.waitForVisibleSelector(page, this.customerCardNameTitle(1));

    return page.$$eval(this.customerResultsBlock, divs => divs.length);
  }

  /**
   * Get customer name from card result
   * @param page {Page} Browser tab
   * @param cardPosition {number} Position of the card in results
   * @returns {Promise<string>}
   */
  getCustomerNameFromResult(page, cardPosition = 1) {
    return this.getTextContent(page, this.customerCardNameTitle(cardPosition));
  }

  /**
   * Get customer card body
   * @param page {Page} Browser tab
   * @param cardPosition {number} Position of the card in results
   * @returns {Promise<string>}
   */
  getCustomerCardBody(page, cardPosition = 1) {
    return this.getTextContent(page, this.customerCardBody(cardPosition));
  }

  /**
   * Click on add new customer and new customer iFrame
   * @param page {Page} Browser tab
   * @param customerData {CustomerData} Customer data fake object
   * @returns {Promise<string>}
   */
  async addNewCustomer(page, customerData) {
    await page.click(this.addCustomerLink);
    await this.waitForVisibleSelector(page, this.iframe);

    const customerFrame = await page.frame({url: new RegExp('sell/customers/new', 'gmi')});

    await addCustomerPage.fillCustomerForm(customerFrame, customerData);

    await Promise.all([
      customerFrame.click(addCustomerPage.saveCustomerButton),
      this.waitForHiddenSelector(page, this.iframe),
    ]);

    return this.getCustomerNameFromResult(page);
  }

  /**
   * Click on choose customer in list
   * @param page {Page} Browser tab
   * @param cardPosition {number} Position of customer to choose on the list
   * @returns {Promise<boolean>}
   */
  async chooseCustomer(page, cardPosition = 1) {
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
  async clickOnDetailsButton(page) {
    await this.waitForSelectorAndClick(page, this.customerCardDetailButton);

    return this.elementVisible(page, this.iframe, 2000);
  }

  /**
   * Get customer Iframe
   * @param page {Page} Browser tab
   * @param customerID {number} Id of customer to check
   * @returns {*}
   */
  getCustomerIframe(page, customerID) {
    return page.frame({url: new RegExp(`sell/customers/${customerID}/view`, 'gmi')});
  }

  /**
   * Get text column from carts table
   * @param page {Page} Browser tab
   * @param column {String} Column name from table
   * @param row {Number} Row on table
   * @returns {Promise<string>}
   */
  async getTextColumnFromCartsTable(page, column, row = 1) {
    return this.getTextContent(page, this.customerCartsTableColumn(column, row));
  }

  /**
   *
   * @param page {Page} Browser tab
   * @param row {Number} Row on table
   * @returns {Promise<Boolean>}
   */
  async clickOnCartDetailsButton(page, row = 1) {
    await this.waitForSelectorAndClick(page, this.customerCartsTableDetailsButton(row));

    return this.elementVisible(page, this.cartsIframe, 1000);
  }

  /**
   *
   * @param page {Page} Browser tab
   * @param cartId {Number} Cart Id
   * @returns {Promise<*|string>}
   */
  async getCartId(page, cartId) {
    const cartIframe = await page.frame({url: new RegExp(`sell/orders/carts/${cartId}/view`, 'gmi')});

    return viewCartPage.getCartId(cartIframe);
  }

  /**
   *
   * @param page {Page} Browser tab
   * @param cartId {Number} Cart Id
   * @returns {Promise<*|number>}
   */
  async getCartTotal(page, cartId) {
    const cartIframe = await page.frame({url: new RegExp(`sell/orders/carts/${cartId}/view`, 'gmi')});

    return viewCartPage.getCartTotal(cartIframe);
  }

  /**
   *
   * @param page {Page} Browser tab
   * @param cartId {Number} Cart Id
   * @returns {Promise<*|string>}
   */
  async getCustomerInformation(page, cartId) {
    const cartIframe = await page.frame({url: new RegExp(`sell/orders/carts/${cartId}/view`, 'gmi')});

    return viewCartPage.getCustomerInformation(cartIframe);
  }

  /**
   *
   * @param page {Page} Browser tab
   * @param cartId {Number} Card Id
   * @returns {Promise<string>}
   */
  async getOrderInformation(page, cartId) {
    const cartIframe = await page.frame({url: new RegExp(`sell/orders/carts/${cartId}/view`, 'gmi')});

    return viewCartPage.getOrderInformation(cartIframe);
  }

  /**
   *
   * @param page {Page} Browser tab
   * @param cartId {Number} Card Id
   * @param column {String} Row on table
   * @param row {Number} column on table
   * @returns {Promise<*|string>}
   */
  async getCartSummary(page, cartId, column, row) {
    const cartIframe = await page.frame({url: new RegExp(`sell/orders/carts/${cartId}/view`, 'gmi')});

    return viewCartPage.getTextColumn(cartIframe, column, row);
  }

  /**
   *
   * @param page {Page} Browser tab
   * @param cartId {Number} Card Id
   * @param column {String} Row on table
   * @returns {Promise<*|number>}
   */
  async getPriceColumnTotalFromCartSummary(page, cartId, column) {
    const cartIframe = await page.frame({url: new RegExp(`sell/orders/carts/${cartId}/view`, 'gmi')});

    return viewCartPage.getPriceColumnTotal(cartIframe, column);
  }

  /* Cart methods */

  /**
   * Add product to cart
   * @param page {Page} Browser tab
   * @param product {ProductData} Product data to search with
   * @param quantity {number} Product quantity to add to the cart
   * @returns {Promise<void>}
   */
  async addProductToCart(page, product, quantity) {
    // Search product
    await this.setValue(page, this.productSearchInput, product.name);
    await this.waitForVisibleSelector(page, this.addProductToCartForm);

    // Fill add product form
    await this.selectByVisibleText(page, this.productResultsSelect, product.name);
    await this.setValue(page, this.productQuantityInput, quantity.toString());

    // Add to cart
    await page.click(this.addtoCartButton);

    await this.waitForVisibleSelector(page, this.productsTable);
  }

  /**
   * Click on orders tab
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnOrdersTab(page) {
    await this.waitForSelectorAndClick(page, this.ordersTab);

    return this.elementVisible(page, this.customerOrdersTable, 1000);
  }

  /**
   * Get orders number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getOrdersNumber(page) {
    await this.waitForVisibleSelector(page, this.customerOrdersTable);

    return page.$$eval(this.customerOrdersTableRows, trs => trs.length);
  }

  /**
   * Get text column from orders table
   * @param page {Page} Browser tab
   * @param column {string} Column name in orders table
   * @param row {number} Column row in orders table
   * @returns {Promise<string>}
   */
  async getTextFromOrdersTable(page, column, row = 1) {
    return this.getTextContent(page, this.customerOrdersTableColumn(column, row));
  }

  /**
   * Click on order details button
   * @param page {Page} Browser tab
   * @row {number} Column row in orders table
   * @returns {Promise<boolean>}
   */
  async clickOnOrderDetailsButton(page, row = 1) {
    await this.waitForSelectorAndClick(page, this.orderDetailsButton(row));

    return this.elementVisible(page, this.iframe, 2000);
  }

  /**
   * Get order Iframe
   * @param page {Page} Browser tab
   * @param orderID {number} Id of order to check
   * @returns {*}
   */
  getOrderIframe(page, orderID) {
    return page.frame({url: new RegExp(`sell/orders/${orderID}/view`, 'gmi')});
  }

  /**
   * Close order iframe
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeOrderIframe(page) {
    await this.waitForSelectorAndClick(page, this.closeIframe);

    return this.elementNotVisible(page, this.iframe, 3000);
  }

  /**
   * Click on order use button
   * @param page {Page} Browser tab
   * @param row {number} Row in orders table
   * @returns {Promise<boolean>}
   */
  async clickOnOrderUseButton(page, row = 1) {
    await this.waitForSelectorAndClick(page, this.orderUseButton(row));

    return this.elementVisible(page, this.productsTable, 1000);
  }

  /* Addresses methods */

  /**
   * Choose addresses in form
   * @param page {Page} Browser tab
   * @param deliveryAddress {string} Delivery address to choose
   * @param invoiceAddress {string} Invoice address to choose
   * @returns {Promise<void>}
   */
  async chooseAddresses(page, deliveryAddress, invoiceAddress) {
    await this.selectByVisibleText(page, this.deliveryAddressSelect, deliveryAddress);
    await this.selectByVisibleText(page, this.invoiceAddressSelect, invoiceAddress);
  }

  /* Shipping methods */

  /**
   * Fill delivery option form
   * @param page {Page} Browser tab
   * @param deliveryOptionName {string} Delivery option name to choose
   * @param isFreeShipping {boolean} True if we want a free shipping
   * @returns {Promise<void>}
   */
  async setDeliveryOption(page, deliveryOptionName, isFreeShipping = false) {
    await this.selectByVisibleText(page, this.deliveryOptionSelect, deliveryOptionName);
    await this.setChecked(page, this.freeShippingToggleInput(isFreeShipping ? 1 : 0));
  }

  /* Summary methods */
  /**
   * Set payment method
   * @param page {Page} Browser tab
   * @param paymentMethodName {string} Payment method to choose
   * @returns {Promise<void>}
   */
  async setPaymentMethod(page, paymentMethodName) {
    await this.selectByVisibleText(page, this.paymentMethodSelect, paymentMethodName);
  }

  /**
   * Set order status
   * @param page {Page} Browser tab
   * @param orderStatus {{id: number, status: string}} Order status to choose
   * @returns {Promise<void>}
   */
  async setOrderStatus(page, orderStatus) {
    await this.selectByVisibleText(page, this.orderStatusSelect, orderStatus.status);
  }

  /**
   * Click on create order button
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnCreateOrderButton(page) {
    await this.clickAndWaitForNavigation(page, this.createOrderButton);
  }

  /**
   * Set summary block
   * @param page {Page} Browser tab
   * @param paymentMethodName {string} Payment method to choose
   * @param orderStatus {{id: number, status: string}} Order status to choose
   * @returns {Promise<void>}
   */
  async setSummaryAndCreateOrder(page, paymentMethodName, orderStatus) {
    await this.setPaymentMethod(page, paymentMethodName);
    await this.setOrderStatus(page, orderStatus);
    await this.clickOnCreateOrderButton(page);
  }

  /* All form methods */

  /**
   * Create order with existing customer
   * @param page {Page} Browser tab
   * @param orderToMake {object} Order data to create
   * @param isNewCustomer {boolean} True if the customer is new
   * @returns {Promise<void>}
   */
  async createOrder(page, orderToMake, isNewCustomer = false) {
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
      await this.addProductToCart(page, orderToMake.products[i].value, orderToMake.products[i].quantity);
    }

    // Choose address
    await this.chooseAddresses(page, orderToMake.deliveryAddress, orderToMake.invoiceAddress);

    // Choose delivery options
    await this.setDeliveryOption(page, orderToMake.deliveryOption.name, orderToMake.deliveryOption.freeShipping);

    // Choose payment method
    await this.setPaymentMethod(page, orderToMake.paymentMethod);

    // Set order status
    await this.setOrderStatus(page, orderToMake.orderStatus);

    // Create the order
    await this.clickAndWaitForNavigation(page, this.createOrderButton);
  }
}

module.exports = new AddOrder();
