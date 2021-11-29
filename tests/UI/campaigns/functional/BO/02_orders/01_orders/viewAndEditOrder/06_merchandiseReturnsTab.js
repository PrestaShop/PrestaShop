/*
require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const boMerchandiseReturnsPage = require('@pages/BO/customerService/merchandiseReturns');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');
const editMerchandiseReturnsPage = require('@pages/BO/customerService/merchandiseReturns/edit');

// Import FO mages
const homePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const checkoutPage = require('@pages/FO/checkout');
const myAccountPage = require('@pages/FO/myAccount');
const orderHistoryPage = require('@pages/FO/myAccount/orderHistory');
const orderDetailsPage = require('@pages/FO/myAccount/orderDetails');
const foMerchandiseReturnsPage = require('@pages/FO/myAccount/merchandiseReturns');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {ReturnStatuses} = require('@data/demo/orderReturnStatuses');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_merchandiseReturnsTab';

let browserContext;
let page;
let orderID = 1;
let trackingNumber = 1;

// Get today date format 'mm/dd/yyyy'
const today = new Date();
const mm = (`0${today.getMonth() + 1}`).slice(-2); // Current month
const dd = (`0${today.getDate()}`).slice(-2); // Current day
const yyyy = today.getFullYear(); // Current year
const todayDate = `${mm}/${dd}/${yyyy}`;

/!*
Create order in FO
Enable merchandise returns and check 'Return products' button
Change order status to 'Shipped'
Create merchandise returns on FO then check it on BO
Update status of merchandise return
- Waiting for package
- Package received
- Return complete
Check the new status on BO and FO
Disable merchandise returns
 *!/
describe('BO - Orders - View and edit order : Check merchandise returns tab', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create order in FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      // Go to FO and change language
      await homePage.goToFo(page);

      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);
      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Go to home page
      await foLoginPage.goToHomePage(page);

      // Go to the first product page
      await homePage.goToProductPage(page, 1);

      // Add the product to the cart
      await productPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      // Payment step - Choose payment step
      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('Enable merchandise returns', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.merchandiseReturnsLink,
      );

      await boMerchandiseReturnsPage.closeSfToolBar(page);

      const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
    });

    it('should enable merchandise returns', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableReturns', baseContext);

      const result = await boMerchandiseReturnsPage.setOrderReturnStatus(page, true);
      await expect(result).to.contains(boMerchandiseReturnsPage.successfulUpdateMessage);
    });
  });

  describe(`Change the new order status to '${Statuses.shipped.status}'`, async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should filter the Orders table by the default customer and check the result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrder', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should get the order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderID', baseContext);

      orderID = await ordersPage.getOrderIDNumber(page);
      expect(orderID).to.not.equal(1);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it('should check if the button \'Return products\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkReturnProductsButton', baseContext);

      const result = await viewOrderPage.isReturnProductsButtonVisible(page);
      await expect(result).to.be.true;
    });
  });

  describe('Create merchandise returns on FO', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      // Click on view my shop
      page = await viewOrderPage.viewMyShop(page);

      // Change FO language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });

    it('should go to account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await homePage.goToMyAccountPage(page);

      const pageTitle = await myAccountPage.getPageTitle(page);
      await expect(pageTitle).to.contains(myAccountPage.pageTitle);
    });

    it('should go to \'Order history and details\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageTitle = await orderHistoryPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderHistoryPage.pageTitle);
    });

    it('should go to the first order in the list and check the existence of order return form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isOrderReturnFormVisible', baseContext);

      await orderHistoryPage.goToDetailsPage(page, 1);

      const result = await orderDetailsPage.isOrderReturnFormVisible(page);
      await expect(result).to.be.true;
    });

    it('should create a merchandise return', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createMerchandiseReturn', baseContext);

      await orderDetailsPage.requestMerchandiseReturn(page, 'Test merchandise returns');

      const pageTitle = await foMerchandiseReturnsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
    });

    it('should close the FO page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFoAndGoBackToBO', baseContext);

      page = await orderDetailsPage.closePage(browserContext, page, 0);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });
  });

  describe('Check the existence of merchandise returns on \'Merchandise returns\' page', async () => {
    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.merchandiseReturnsLink,
      );

      await boMerchandiseReturnsPage.closeSfToolBar(page);

      const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
    });

    it('should check the existence of the merchandise returns in the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExistenceOfMerchandiseReturn', baseContext);

      await boMerchandiseReturnsPage.filterMerchandiseReturnsTable(page, 'a!id_order', orderID);

      const result = await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(page, 'id_order');
      await expect(result).to.contains(orderID);
    });

    it('should get the tracking number from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getTrackingNumber', baseContext);

      trackingNumber = await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(page, 'id_order_return');
      await expect(parseInt(trackingNumber, 10)).to.not.equal(0);
    });
  });

  describe('Check the existence of the merchandise returns on \'Merchandise returns\' tab', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should filter the Orders table by the default customer and check the result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrder', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it('should click on \'Merchandise returns\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayCarriersTab', baseContext);

      const isTabOpened = await viewOrderPage.goToMerchandiseReturnsTab(page);
      await expect(isTabOpened).to.be.true;
    });

    it('should check that the merchandise returns number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersNumber', baseContext);

      const carriersNumber = await viewOrderPage.getMerchandiseReturnsNumber(page);
      await expect(carriersNumber).to.be.equal(1);
    });

    it('should check the merchandise returns details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarrierDetails', baseContext);

      const result = await viewOrderPage.getMerchandiseReturnsDetails(page);
      await Promise.all([
        expect(result.date).to.contains(todayDate),
        expect(result.type).to.equal('Return'),
        expect(result.carrier).to.equal('Waiting for confirmation'),
        expect(result.trackingNumber).to.equal(trackingNumber),
      ]);
    });
  });

  const tests = [
    {args: {status: ReturnStatuses.waitingForPackage.name}},
    {args: {status: ReturnStatuses.packageReceived.name}},
    {args: {status: ReturnStatuses.returnCompleted.name}},
  ];

  tests.forEach((test, index) => {
    describe(`Update status of merchandise return to '${test.args.status}'`, async () => {
      it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToMerchandiseReturnsPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.customerServiceParentLink,
          dashboardPage.merchandiseReturnsLink,
        );

        await boMerchandiseReturnsPage.closeSfToolBar(page);

        const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
      });

      it('should check the existence of the merchandise returns in the table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkExistenceOfReturns${index}`, baseContext);

        await boMerchandiseReturnsPage.filterMerchandiseReturnsTable(page, 'a!id_order', orderID);

        const result = await boMerchandiseReturnsPage.getTextColumnFromMerchandiseReturnsTable(page, 'id_order');
        await expect(result).to.contains(orderID);
      });

      it('should go to edit merchandise returns page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToEditReturnsPage${index}`, baseContext);

        await boMerchandiseReturnsPage.goToMerchandiseReturnPage(page);

        const pageTitle = await editMerchandiseReturnsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(editMerchandiseReturnsPage.pageTitle);
      });

      it('should edit merchandise returns status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `editReturnStatus${index}`, baseContext);

        const textResult = await editMerchandiseReturnsPage.setStatus(page, test.args.status);
        await expect(textResult).to.contains(editMerchandiseReturnsPage.successfulUpdateMessage);
      });
    });

    describe('Check the updated status of merchandise returns on view order page', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should filter the Orders table by the default customer and check the result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterOrder${index}`, baseContext);

        await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

        const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
        await expect(textColumn).to.contains(DefaultCustomer.lastName);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage${index}`, baseContext);

        // View order
        await ordersPage.goToOrder(page, 1);

        const pageTitle = await viewOrderPage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
      });

      it('should click on \'Merchandise returns\' tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnMerchandiseReturn${index}`, baseContext);

        const isTabOpened = await viewOrderPage.goToMerchandiseReturnsTab(page);
        await expect(isTabOpened).to.be.true;
      });

      it('should check that the merchandise returns number is equal to 1', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkMerchandiseReturnsNumber${index}`, baseContext);

        const carriersNumber = await viewOrderPage.getMerchandiseReturnsNumber(page);
        await expect(carriersNumber).to.be.equal(1);
      });

      it(`should check the merchandise returns status is '${test.args.status}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCarrierDetails', baseContext);

        const result = await viewOrderPage.getMerchandiseReturnsDetails(page);
        await expect(result.carrier).to.equal(test.args.status);
      });
    });

    describe('Check the updated status of merchandise returns on FO', async () => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFO${index}`, baseContext);

        // Click on view my shop
        page = await viewOrderPage.viewMyShop(page);

        // Change FO language
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage, 'Home page is not displayed').to.be.true;
      });

      it('should go to account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAccountPage${index}`, baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.contains(myAccountPage.pageTitle);
      });

      it('should go to \'Merchandise Returns\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToMerchandiseReturnPage${index}`, baseContext);

        await myAccountPage.goToMerchandiseReturnsPage(page);

        const pageTitle = await foMerchandiseReturnsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
      });

      it('should verify order return status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOrderReturnStatus${index}`, baseContext);

        const fileName = await foMerchandiseReturnsPage.getOrderReturnStatus(page);
        await expect(fileName).to.be.equal(test.args.status);
      });

      it('should close the FO page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `closeFoAndGoBackToBO${index}`, baseContext);

        page = await orderDetailsPage.closePage(browserContext, page, 0);

        const pageTitle = await viewOrderPage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
      });
    });
  });

  describe('Disable merchandise returns', async () => {
    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPageToDisable', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.merchandiseReturnsLink,
      );

      const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
    });

    it('should disable merchandise returns', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableReturns', baseContext);

      const result = await boMerchandiseReturnsPage.setOrderReturnStatus(page, false);
      await expect(result).to.contains(boMerchandiseReturnsPage.successfulUpdateMessage);
    });
  });
});
*/
