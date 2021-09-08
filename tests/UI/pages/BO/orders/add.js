require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

// Needed to create customer in orders page
const addCustomerPage = require('@pages/BO/customers/add');

class AddOrder extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Create order •';
    this.noCustomerFoundText = 'No customers found';

    // Customer selectors
    this.addCustomerLink = '#customer-add-btn';
    this.addCustomerIframe = 'iframe.fancybox-iframe';
    this.customerSearchInput = '#customer-search-input';
    this.customerSearchLoadingNoticeBlock = '#customer-search-loading-notice';
    // Empty results
    this.customerSearchEmptyResultBlock = '#customer-search-empty-result-warn';
    this.customerSearchEmptyResultParagraphe = `${this.customerSearchEmptyResultBlock} .alert-text`;
    // Full results
    this.customerSearchFullResultsBlock = '.js-customer-search-results';
    this.customerCardBlock = pos => `${this.customerSearchFullResultsBlock} `
      + `.js-customer-search-result:nth-child(${pos})`;
    this.customerCardNameTitle = pos => `${this.customerCardBlock(pos)} .js-customer-name`;
    this.customerCardChooseButton = pos => `${this.customerCardBlock(pos)} .js-choose-customer-btn`;

    // Checkout history selectors
    this.checkoutHistoryBlock = '#customer-checkout-history';

    // Cart selectors
    this.cartBlock = '#cart-block';
    this.productSearchInput = '#product-search';
    this.addProductToCartForm = '#js-add-product-form';
    this.productResultsSelect = '#product-select';
    this.productQuantityInput = '#quantity-input';
    this.addtoCartButton = '#add-product-to-cart-btn';
    this.productsTable = '#products-table';

    // Addresses form selectors
    this.deliveryAddressSelect = '#delivery-address-select';
    this.invoiceAddressSelect = '#invoice-address-select';

    // Shipping form selectors
    this.shippingBlock = '#shipping-block';
    this.deliveryOptionSelect = '#delivery-option-select';
    this.freeShippingToggle = toggle => `${this.shippingBlock} label[for='free-shipping_${toggle}']`;

    // Summary selectors
    this.paymentMethodSelect = '#cart_summary_payment_module';
    this.orderStatusSelect = '#cart_summary_order_state';
    this.createOrderButton = '#create-order-button';
  }

  /* Customer functions */

  /**
   * Fill customer search input and wait for results to load
   * @param page
   * @param customerToSearch
   * @return {Promise<void>}
   */
  async searchCustomer(page, customerToSearch) {
    await this.setValue(page, this.customerSearchInput, customerToSearch);

    await this.waitForHiddenSelector(page, this.customerSearchLoadingNoticeBlock);
  }

  /**
   * Get Error message when when no customer was found after searching
   * @param page
   * @return {Promise<string>}
   */
  getNoCustomerFoundError(page) {
    return this.getTextContent(page, this.customerSearchEmptyResultParagraphe);
  }

  /**
   * Get customer name from card result
   * @param page
   * @param cardPosition, position of the card in results
   * @return {Promise<string>}
   */
  getCustomerNameFromResult(page, cardPosition = 1) {
    return this.getTextContent(page, this.customerCardNameTitle(cardPosition));
  }

  /**
   * Click on add new customer and new customer iFrame
   * @param page
   * @param customerData
   * @return {Promise<string>}
   */
  async addNewCustomer(page, customerData) {
    await page.click(this.addCustomerLink);
    await this.waitForVisibleSelector(page, this.addCustomerIframe);

    const customerFrame = await page.frame({url: new RegExp('sell/customers/new', 'gmi')});

    await addCustomerPage.fillCustomerForm(customerFrame, customerData);

    await Promise.all([
      customerFrame.click(addCustomerPage.saveCustomerButton),
      this.waitForHiddenSelector(page, this.addCustomerIframe),
    ]);

    return this.getCustomerNameFromResult(page);
  }

  /**
   * Click on choose customer in list
   * @param page
   * @param cardPosition
   * @return {Promise<void>}
   */
  async chooseCustomer(page, cardPosition = 1) {
    await page.click(this.customerCardChooseButton(cardPosition));

    await Promise.all([
      this.waitForHiddenSelector(page, this.customerCardChooseButton(cardPosition)),
      this.waitForVisibleSelector(page, this.checkoutHistoryBlock),
    ]);
  }

  /* Cart methods */

  /**
   * Add product to cart
   * @param page
   * @param product
   * @param quantity
   * @return {Promise<void>}
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


  /* Addresses methods */

  /**
   * Choose addresses in form
   * @param page
   * @param deliveryAddress
   * @param invoiceAddress
   * @return {Promise<void>}
   */
  async chooseAddresses(page, deliveryAddress, invoiceAddress) {
    await this.selectByVisibleText(page, this.deliveryAddressSelect, deliveryAddress);
    await this.selectByVisibleText(page, this.invoiceAddressSelect, invoiceAddress);
  }

  /* Shipping methods */

  /**
   * Fill delivery option form
   * @param page
   * @param deliveryOptionName
   * @param freeShipping
   * @return {Promise<void>}
   */
  async setDeliveryOption(page, deliveryOptionName, freeShipping = false) {
    await this.selectByVisibleText(page, this.deliveryOptionSelect, deliveryOptionName);
    await page.click(this.freeShippingToggle(freeShipping ? 1 : 0));
  }

  /* Summary methods */
  /**
   * Set payment method
   * @param page
   * @param paymentMethodName
   * @return {Promise<void>}
   */
  async setPaymentMethod(page, paymentMethodName) {
    await this.selectByVisibleText(page, this.paymentMethodSelect, paymentMethodName);
  }

  /**
   * Set order status
   * @param page
   * @param orderStatus
   * @return {Promise<void>}
   */
  async setOrderStatus(page, orderStatus) {
    await this.selectByVisibleText(page, this.orderStatusSelect, orderStatus.status);
  }

  /* All form methods */

  /**
   * Create order with existing customer
   * @param page
   * @param orderToMake
   * @param newCustomer
   * @return {Promise<void>}
   */
  async createOrder(page, orderToMake, newCustomer = false) {
    // Choose customer
    // If it's a new customer, the creation of customer should be done in test
    // with add customer page
    if (!newCustomer) {
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
