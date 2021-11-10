require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');

// Import FO pages
const foLoginPage = require('@pages/FO/login');
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const foCartPage = require('@pages/FO/cart');
const foCheckoutPage = require('@pages/FO/checkout');
const foOrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Products} = require('@data/demo/products');
const {Carriers} = require('@data/demo/carriers');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_paymentBlock';

let browserContext;
let page;

// Get today date format 'mm/dd/yyyy'
const today = new Date();
const mm = (`0${today.getMonth() + 1}`).slice(-2); // Current month
const dd = (`0${today.getDate()}`).slice(-2); // Current day
const yyyy = today.getFullYear(); // Current year
const todayDate = `${yyyy}/${mm}/${dd}`;
const todayDateToCheck = `${mm}/${dd}/${yyyy}`;

const totalOrder = 22.94;

const paymentDataAmountInfTotal = {
  date: todayDate,
  paymentMethod: 'Payment by check',
  transactionID: 12156,
  amount: 12.25,
};

const paymentDataAmountEqualTotal = {
  date: todayDate,
  paymentMethod: 'Bank transfer',
  transactionID: 12190,
  amount: (totalOrder - paymentDataAmountInfTotal.amount).toFixed(2),
};

const paymentDataAmountSupTotal = {
  date: todayDate,
  paymentMethod: 'Bank transfer',
  transactionID: 12639,
  amount: 30.56,
};

/*
Pre-condition :
- Create order by default customer
Scenario :
- View order page
- Add payment inferior to the total
- Add payment equal to the total
 */

describe('BO - Orders - View and edit order : Check payment Block', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition - Create order by default customer
  describe('Create order by default customer in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await foHomePage.goToFo(page);

      // Change FO language
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

      await foHomePage.goToHomePage(page);

      await foHomePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should add product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHomePage.goToHomePage(page);

      // Go to the first product page
      await foHomePage.goToProductPage(page, 1);

      // Add the product to the cart
      await foProductPage.addProductToTheCart(page);

      const notificationsNumber = await foCartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await foCartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foCheckoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foCheckoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      // Payment step - Choose payment step
      await foCheckoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await foOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      await expect(cardTitle).to.contains(foOrderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters1', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle, 'Error when view order page!').to.contains(viewOrderPage.pageTitle);
    });
  });

  // 2 - Add payment inferior to the total
  describe('Add payment inferior to the total', async () => {
    it('should check that payments number is equal to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentsNumber1', baseContext);

      const paymentsNumber = await viewOrderPage.getPaymentsNumber(page);
      await expect(paymentsNumber, 'Payments number is not correct! ').to.equal(0);
    });

    it('should add payment when amount is inferior to the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'testAmountInferiorTotal', baseContext);

      const validationMessage = await viewOrderPage.addPayment(page, paymentDataAmountInfTotal);
      expect(validationMessage, 'Successful message is not correct!').to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it('should check the warning message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWarning', baseContext);

      const warningMessage = await viewOrderPage.getPaymentWarning(page);
      expect(warningMessage, 'Warning message is not correct!')
        .to.equal(`Warning €${paymentDataAmountInfTotal.amount} paid instead of €${totalOrder}`);
    });

    it('should check that the payment number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentsNumber2', baseContext);

      const paymentsNumber = await viewOrderPage.getPaymentsNumber(page);
      await expect(paymentsNumber, 'Payments number is not correct! ').to.equal(1);
    });

    it('should check the payment details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPayment', baseContext);

      const result = await viewOrderPage.getPaymentsDetails(page);
      await Promise.all([
        expect(result.date).to.contain(todayDateToCheck),
        expect(result.paymentMethod).to.equal(paymentDataAmountInfTotal.paymentMethod),
        expect(result.transactionID).to.equal(paymentDataAmountInfTotal.transactionID),
        expect(result.amount).to.equal(`€${paymentDataAmountInfTotal.amount}`),
        expect(result.invoice).to.equal(''),
      ]);
    });
  });

  // 3 - Add payment equal to the total
  describe('Add payment equal to the total', async () => {
    it('should add payment when amount is equal to the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'testAmountEqualTotal', baseContext);

      const validationMessage = await viewOrderPage.addPayment(page, paymentDataAmountEqualTotal);
      expect(validationMessage, 'Successful message is not correct!').to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it('should check that the payment number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentsNumber3', baseContext);

      const paymentsNumber = await viewOrderPage.getPaymentsNumber(page);
      await expect(paymentsNumber, 'Payments number is not correct! ').to.equal(2);
    });

    it('should check the payment details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPayment2', baseContext);

      const result = await viewOrderPage.getPaymentsDetails(page, 3);
      await Promise.all([
        expect(result.date).to.contain(todayDateToCheck),
        expect(result.paymentMethod).to.equal(paymentDataAmountEqualTotal.paymentMethod),
        expect(result.transactionID).to.equal(paymentDataAmountEqualTotal.transactionID),
        expect(result.amount).to.equal(`€${paymentDataAmountEqualTotal.amount}`),
        expect(result.invoice).to.equal(''),
      ]);
    });
  });

  // 4 - Add payment superior to the total
  describe('Add payment superior to the total', async () => {
    it('should add payment when amount is superior to the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'testAmountSupTotal', baseContext);

      const validationMessage = await viewOrderPage.addPayment(page, paymentDataAmountSupTotal);
      expect(validationMessage, 'Successful message is not correct!').to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it('should check the warning message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWarning2', baseContext);

      const warningMessage = await viewOrderPage.getPaymentWarning(page);
      expect(warningMessage, 'Warning message is not correct!')
        .to.equal(`Warning €${(paymentDataAmountSupTotal.amount + totalOrder).toFixed(2)}`
        + ` paid instead of €${totalOrder}`);
    });

    it('should check that the payment number is equal to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentsNumber4', baseContext);

      const paymentsNumber = await viewOrderPage.getPaymentsNumber(page);
      await expect(paymentsNumber, 'Payments number is not correct! ').to.equal(3);
    });

    it('should check the payment details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPayment2', baseContext);

      const result = await viewOrderPage.getPaymentsDetails(page, 5);
      await Promise.all([
        expect(result.date).to.contain(todayDateToCheck),
        expect(result.paymentMethod).to.equal(paymentDataAmountSupTotal.paymentMethod),
        expect(result.transactionID).to.equal(paymentDataAmountSupTotal.transactionID),
        expect(result.amount).to.equal(`€${paymentDataAmountSupTotal.amount}`),
        expect(result.invoice).to.equal(''),
      ]);
    });
  });
});
