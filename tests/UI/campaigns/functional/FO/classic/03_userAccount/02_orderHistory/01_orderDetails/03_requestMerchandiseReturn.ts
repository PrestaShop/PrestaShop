// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import date from '@utils/date';

// Import common tests
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';
import {
  enableMerchandiseReturns,
  disableMerchandiseReturns,
} from '@commonTests/BO/customerService/merchandiseReturns';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {orderHistoryPage} from '@pages/FO/classic/myAccount/orderHistory';
import invoicesPage from '@pages/BO/orders/invoices';
import ordersPage from '@pages/BO/orders';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';
import {orderDetailsPage} from '@pages/FO/classic/myAccount/orderDetails';
import {merchandiseReturnsPage as foMerchandiseReturnsPage} from '@pages/FO/classic/myAccount/merchandiseReturns';

// Import data
import Products from '@data/demo/products';
import PaymentMethods from '@data/demo/paymentMethods';
import OrderStatuses from '@data/demo/orderStatuses';
import OrderData from '@data/faker/order';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// context
const baseContext: string = 'functional_FO_classic_userAccount_orderHistory_orderDetails_requestMerchandiseReturn';

/*
Pre-condition:
- Create order by default customer
- Enable merchandise return
Scenario:
- Change order status to Delivered
- Go to order details page
- Create merchandise return
- Check the created return
Post-condition:
- Disable merchandise return
 */
describe('FO - Account - Order details : Request merchandise return', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let orderReference: string;

  const orderData: OrderData = new OrderData({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });
  const today: string = date.getDateFormat('mm/dd/yyyy');

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_1`);

  // Pre-condition: Enable merchandise returns
  enableMerchandiseReturns(`${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Change the created order status to \'Delivered\'', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageForUpdatedPrefix', baseContext);

      await invoicesPage.goToSubMenu(
        page,
        invoicesPage.ordersParentLink,
        invoicesPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForUpdatedPrefix', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${OrderStatuses.delivered.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, OrderStatuses.delivered.name);
      expect(result).to.equal(OrderStatuses.delivered.name);
    });

    it('should get the order reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstOrderUpdatedPrefix', baseContext);

      orderReference = await orderPageTabListBlock.getOrderReference(page);
      expect(orderReference).to.not.eq(null);
    });
  });

  describe('Request merchandise return', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await foHomePage.goToFo(page);

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foLoginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected: boolean = await myAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await foHomePage.goToMyAccountPage(page);

      const pageTitle = await myAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await orderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
    });

    it('should go to order details page of the first order in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await orderHistoryPage.goToDetailsPage(page);

      const pageTitle = await orderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(orderDetailsPage.pageTitle);
    });

    it('should check the existence of order return form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isOrderReturnFormVisible', baseContext);

      const result = await orderDetailsPage.isOrderReturnFormVisible(page);
      expect(result).to.eq(true);
    });

    it('should create a merchandise return and check if merchandise return page is displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createMerchandiseReturn', baseContext);

      await orderDetailsPage.requestMerchandiseReturn(page, 'Test merchandise returns');

      const pageTitle = await foMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
    });

    it('should check the merchandise returns table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMerchandiseReturnsTable', baseContext);

      const result = await foMerchandiseReturnsPage.getMerchandiseReturnsDetails(page);
      await Promise.all([
        expect(result.orderReference).to.equal(orderReference),
        expect(result.fileName).to.contains('#RE'),
        expect(result.status).to.equal('Waiting for confirmation'),
        expect(result.dateIssued).to.equal(today),
      ]);
    });
  });

  // Post-condition: Disable merchandise returns
  disableMerchandiseReturns(`${baseContext}_postTest_1`);
});
