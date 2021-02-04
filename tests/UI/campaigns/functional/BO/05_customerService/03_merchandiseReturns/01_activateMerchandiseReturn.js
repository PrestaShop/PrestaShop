require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const merchandiseReturnsPage = require('@pages/BO/customerService/merchandiseReturns');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');
const homePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const checkoutPage = require('@pages/FO/checkout');
const myAccountPage = require('@pages/FO/myAccount');
const orderHistoryPage = require('@pages/FO/myAccount/orderHistory');
const orderDetailsPage = require('@pages/FO/myAccount/orderDetails');

// Import data
const {DefaultAccount} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');
const {PaymentMethods} = require('@data/demo/paymentMethods');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customerService_orderMessages_activateMerchandiseReturns';

let browserContext;
let page;

/*
Create order in FO
Activate/Deactivate merchandise return
Change the first order status in the list to shipped
Check the existence of the button return products
Go to FO>My account>Order history> first order detail in the list
Check the existence of product return form
 */
describe('Activate/Deactivate merchandise return', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

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
    await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

    await foLoginPage.customerLogin(page, DefaultAccount);
    const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
  });

  it('should create an order', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createOrder', baseContext);

    // Go to home page
    await foLoginPage.goToHomePage(page);

    // Go to the first product page
    await homePage.goToProductPage(page, 1);

    // Add the created product to the cart
    await productPage.addProductToTheCart(page);

    // Edit the product quantity
    await cartPage.editProductQuantity(page, 1, 5);

    // Proceed to checkout the shopping cart
    await cartPage.clickOnProceedToCheckout(page);

    // Address step - Go to delivery step
    const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
    await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;

    // Delivery step - Go to payment step
    const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
    await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

    // Payment step - Choose payment step
    await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

    // Check the confirmation message
    const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
    await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
  });

  it('should sign out from FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sighOutFO', baseContext);

    await orderConfirmationPage.logout(page);
    const isCustomerConnected = await orderConfirmationPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is connected').to.be.false;
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  const tests = [
    {args: {action: 'activate', enable: true}},
    {args: {action: 'deactivate', enable: false}},
  ];

  tests.forEach((test, index) => {
    it('should go to merchandise returns page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToMerchandiseReturnsPage${index}`, baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.merchandiseReturnsLink,
      );

      await merchandiseReturnsPage.closeSfToolBar(page);

      const pageTitle = await merchandiseReturnsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(merchandiseReturnsPage.pageTitle);
    });

    it(`should ${test.args.action} merchandise returns`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Returns`, baseContext);

      const result = await merchandiseReturnsPage.setOrderReturnStatus(page, test.args.enable);
      await expect(result).to.contains(merchandiseReturnsPage.successfulUpdateMessage);
    });

    it('should go to orders page', async function () {
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

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultAccount.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultAccount.lastName);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToOrderPage${index}`, baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus${index}`, baseContext);

      const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it('should check if the button \'Return products\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkReturnProductsButton${index}`, baseContext);

      const result = await viewOrderPage.isReturnProductsButtonVisible(page);
      await expect(result).to.equal(test.args.enable);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFO${index}`, baseContext);

      // Click on view my shop
      page = await viewOrderPage.viewMyShop(page);

      // Change FO language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });

    // Go to My account page by login the first time and click on account link the second time
    if (index === 0) {
      it('should login', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAccountPage${index}`, baseContext);

        await homePage.goToLoginPage(page);
        await foLoginPage.customerLogin(page, DefaultAccount);

        const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
        await expect(isCustomerConnected).to.be.true;

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.contains(myAccountPage.pageTitle);
      });
    } else {
      it('should go to account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAccountPage${index}`, baseContext);

        await homePage.goToYourAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.contains(myAccountPage.pageTitle);
      });
    }

    it('should go to \'Order history and details\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToOrderHistoryPage${index}`, baseContext);

      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageTitle = await orderHistoryPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderHistoryPage.pageTitle);
    });

    it('should go to the first order in the list and check the existence of order return form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `isOrderReturnFormVisible${index}`, baseContext);

      await orderHistoryPage.goToDetailsPage(page, 1);

      const result = await orderDetailsPage.isOrderReturnFormVisible(page);
      await expect(result).to.equal(test.args.enable);
    });

    it('should close the FO page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closeFoAndGoBackToBO${index}`, baseContext);

      page = await orderDetailsPage.closePage(browserContext, page, 0);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });
  });
});
