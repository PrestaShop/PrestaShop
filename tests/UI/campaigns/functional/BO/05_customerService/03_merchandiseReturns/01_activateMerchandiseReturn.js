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
const myAccountPage = require('@pages/FO/myAccount');
const orderHistoryPage = require('@pages/FO/myAccount/orderHistory');
const orderDetailsPage = require('@pages/FO/myAccount/orderDetails');

// Import data
const {DefaultAccount} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customerService_orderMessages_activateMerchandiseReturns';

let browserContext;
let page;

/*
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

    it('should check that the button \'Return products\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkReturnProductsButton${index}`, baseContext);

      const result = await viewOrderPage.isReturnProductsButtonVisible(page);
      await expect(result).to.equal(test.args.enable);
    });

    it('should go to FO and login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `openTheShop${index}`, baseContext);

      // Click on view my shop
      page = await viewOrderPage.viewMyShop(page);

      // Change FO language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;

      if (index === 0) {
        // Login FO
        await homePage.goToLoginPage(page);
        await foLoginPage.customerLogin(page, DefaultAccount);

        const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
        await expect(isCustomerConnected).to.be.true;

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.contains(myAccountPage.pageTitle);
      } else {
        await homePage.goToYourAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.contains(myAccountPage.pageTitle);
      }
    });

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

      page = await orderDetailsPage.closePage(browserContext, page, 0);
    });
  });
});
