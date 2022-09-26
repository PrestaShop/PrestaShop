require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const myAccountPage = require('@pages/FO/myAccount');
const orderHistoryPage = require('@pages/FO/myAccount/orderHistory');
const orderDetailsPage = require('@pages/FO/myAccount/orderDetails');
const foMerchandiseReturnsPage = require('@pages/FO/myAccount/merchandiseReturns');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const boMerchandiseReturnsPage = require('@pages/BO/customerService/merchandiseReturns');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view/viewOrderBasePage');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Statuses} = require('@data/demo/orderStatuses');

const baseContext = 'functional_FO_userAccount_merchandiseReturns_consultMerchandiseReturnsList';

let browserContext;
let page;
let orderReference;
let orderDate;

// New order by customer data
const orderData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*

 */
describe('FO - Account : Consult merchandise returns list', async () => {
  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('PRE-TEST : Enable merchandise returns', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'enableMerchandiseReturns', baseContext);

      const result = await boMerchandiseReturnsPage.setOrderReturnStatus(page, true);
      await expect(result).to.contains(boMerchandiseReturnsPage.successfulUpdateMessage);
    });
  });

  describe('Case 1 : Check that no merchandise returns has been requested', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount1', baseContext);

      await homePage.goToFo(page);
      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await homePage.goToLoginPage(page);
      await loginPage.customerLogin(page, DefaultCustomer);

      const isCustomerConnected = await loginPage.isCustomerConnected(page);
      await expect(isCustomerConnected).to.be.true;
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await homePage.goToMyAccountPage(page);

      const pageTitle = await myAccountPage.getPageTitle(page);
      await expect(pageTitle).to.contains(myAccountPage.pageTitle);
    });

    it('should click on \'Merchandise returns\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnMerchandiseReturnsLink', baseContext);

      await myAccountPage.goToMerchandiseReturnsPage(page);

      const pageTitle = await foMerchandiseReturnsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
    });

    it('should check that no merchandise returns has been requested', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoMerchandiseReturns', baseContext);

      const alert = await foMerchandiseReturnsPage.getAlertText(page);
      await expect(alert).to.equal(foMerchandiseReturnsPage.alertNoMerchandiseReturns);
    });
  });

  describe('Case 2 : Check merchandise returns in list', async () => {
    describe(`Change the created orders status to '${Statuses.shipped.status}'`, async () => {
      it('should go to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToBO', baseContext);

        await foMerchandiseReturnsPage.goTo(page, global.BO.URL);

        const pageTitle = await dashboardPage.getPageTitle(page);
        await expect(pageTitle).to.contains(dashboardPage.pageTitle);
      });

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

      it('should get the created Order information', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterOrder', baseContext);

        orderReference = await ordersPage.getTextColumn(page, 'reference', 1);
        await expect(orderReference).to.not.be.null;
      });

      it('should get the created Order date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterOrder', baseContext);

        orderDate = await ordersPage.getTextColumn(page, 'date_add', 1);
        await expect(orderDate).to.not.be.null;
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

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
    });

    describe('Check merchandise returns list', async () => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

        // Click on view my shop
        page = await viewOrderPage.viewMyShop(page);

        // Change FO language
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage, 'Home page is not displayed').to.be.true;
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage2', baseContext);

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

        await orderDetailsPage.requestMerchandiseReturn(page, 'test');

        const pageTitle = await foMerchandiseReturnsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
      });

      it('should verify the Order reference', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnReference', baseContext);

        const packageStatus = await foMerchandiseReturnsPage.getTextColumn(page, 'orderReference');
        await expect(packageStatus).to.equal(orderReference);
      });

      it('should verify the Order return file name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnFileName', baseContext);

        const packageStatus = await foMerchandiseReturnsPage.getTextColumn(page, 'fileName');
        await expect(packageStatus).to.contains('#RE00');
      });

      it('should verify the order return status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnStatus', baseContext);

        const packageStatus = await foMerchandiseReturnsPage.getTextColumn(page, 'status');
        await expect(packageStatus).to.equal('Waiting for confirmation');
      });

      it('should verify the order return date issued', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReturnDateIssued', baseContext);

        const packageStatus = await foMerchandiseReturnsPage.getTextColumn(page, 'dateIssued');
        await expect(packageStatus).to.equal(orderDate.substr(0, 10));
      });
    });
  });
});
