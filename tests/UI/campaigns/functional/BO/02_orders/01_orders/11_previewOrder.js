require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByGuestTest} = require('@commonTests/FO/createOrder');
const {deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const viewCustomerPage = require('@pages/BO/customers/view');

// Import FO pages
const homePage = require('@pages/FO/home');
const searchResultsPage = require('@pages/FO/searchResults');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const checkoutPage = require('@pages/FO/checkout');

// Import demo data
const {Products} = require('@data/demo/products');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Carriers} = require('@data/demo/carriers');

// Import faker data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');

const baseContext = 'functional_BO_orders_orders_previewOrder';

// Browser and tab
let browserContext;
let page;

const customerData = new CustomerFaker({password: ''});
const addressData = new AddressFaker({country: 'France'});

/*
Pre-condition:
- Create order by guest
Scenario:
- Go to orders page
- Filter by guest email

Post-condition
- Delete guest account
 */
describe('BO - Orders : Preview order', async () => {
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition: Create order contains 11 products by guest in FO
  describe('PRE-TEST: Create order contains 11 products by guest in FO', async () => {
    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      // Go to FO and change language
      await homePage.goToFo(page);

      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    const tests = [
      {args: {productName: Products.demo_1.name}},
      {args: {productName: Products.demo_3.name}},
      {args: {productName: Products.demo_5.name}},
      {args: {productName: Products.demo_6.name}},
      {args: {productName: Products.demo_7.name}},
      {args: {productName: Products.demo_8.name}},
      {args: {productName: Products.demo_12.name}},
      {args: {productName: Products.demo_11.name}},
      {args: {productName: Products.demo_13.name}},
      {args: {productName: Products.demo_14.name}},
      {args: {productName: Products.demo_18.name}},
    ];

    tests.forEach((test, index) => {
      it(`should search for the product '${test.args.productName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchForProduct_${index}`, baseContext);

        await homePage.searchProduct(page, test.args.productName);

        const pageTitle = await searchResultsPage.getPageTitle(page);
        await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
      });

      it('should add the product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        await searchResultsPage.goToProductPage(page, 1);

        // Add the product to the cart
        await productPage.addProductToTheCart(page, 1, {size: null, color: null}, false);

        const notificationsNumber = await productPage.getCartNotificationsNumber(page);
        await expect(notificationsNumber).to.be.equal(index + 1);
      });
    });

    it('should go to shopping cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCart', baseContext);

      await productPage.goToCartPage(page);

      const pageTitle = await cartPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartPage.pageTitle);
    });

    it('should proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Go to checkout page
      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      await expect(isCheckoutPage).to.be.true;
    });

    it('should fill guest personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

      const isStepPersonalInfoCompleted = await checkoutPage.setGuestPersonalInformation(page, customerData);
      await expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.be.true;
    });

    it('should fill address form and go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

      const isStepAddressComplete = await checkoutPage.setAddress(page, addressData);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should validate the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateOrder', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);

      // Check the confirmation message
      await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Preview the created order on BO', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter order by customer last name ${customerData.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const numberOfOrders = await ordersPage.getNumberOfElementInGrid(page);
      await expect(numberOfOrders).to.be.at.least(1);
    });

    it('should click on the icon loop to preview the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewOrder', baseContext);

      const isPreviewBlockVisible = await ordersPage.previewOrder(page);
      await expect(isPreviewBlockVisible, 'Preview block is not visible').to.be.true;
    });

    it('should check the shipping details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingDetails', baseContext);

      const shippingDetails = await ordersPage.getShippingDetails(page);
      await expect(shippingDetails, 'Shipping details are not correct!')
        .to.equal(`Carrier: ${Carriers.default.name} Tracking number: - Shipping details: `
          + `${customerData.firstName} ${customerData.lastName} ${addressData.company} ${addressData.address} `
          + `${addressData.postalCode} ${addressData.city} ${addressData.country} ${addressData.phone}`);
    });

    it('should check the email address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmailAddress', baseContext);

      const emailAddress = await ordersPage.getCustomerEmail(page);
      await expect(emailAddress, 'Email address is not correct!').to.equal(`Email: ${customerData.email}`);
    });

    it('should check the invoice address details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceDetails', baseContext);

      const invoiceAddress = await ordersPage.getCustomerInvoiceAddressDetails(page);
      await expect(invoiceAddress, 'Invoice details are not correct!')
        .to.equal(`Invoice details: ${customerData.firstName} ${customerData.lastName} `
          + `${addressData.company} ${addressData.address} ${addressData.postalCode} ${addressData.city} `
          + `${addressData.country} ${addressData.phone}`);
    });

    it('should check the products number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsNumber', baseContext);

      const productsNumber = await ordersPage.getProductsNumberFromTable(page);
      await expect(productsNumber, 'Products number is not correct!').to.equal(11);
    });

    it('should check the products list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsList', baseContext);

      const tests = [
        {args: {productName: Products.demo_1.name}},
        {args: {productName: Products.demo_3.name}},
        {args: {productName: Products.demo_5.name}},
        {args: {productName: Products.demo_6.name}},
        {args: {productName: Products.demo_7.name}},
        {args: {productName: Products.demo_8.name}},
        {args: {productName: Products.demo_12.name}},
        {args: {productName: Products.demo_11.name}},
        {args: {productName: Products.demo_13.name}},
        {args: {productName: Products.demo_14.name}},
        {args: {productName: Products.demo_18.name}},
      ];
      tests.forEach((test, index) => {
        const productInformation = await ordersPage.getProductDetailsFromTable(page, i);
        await expect(productInformation).to.equal(11);
      });
    });
  });

  // Post-condition: Delete guest account
  //deleteCustomerTest(customerData, baseContext);
});
