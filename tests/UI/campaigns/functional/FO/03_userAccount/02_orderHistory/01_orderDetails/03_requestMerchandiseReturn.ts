// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import {Statuses} from '@data/demo/orderStatuses';
import date from '@utils/date';

// Import common tests
import {createOrderByCustomerTest} from '@commonTests/FO/createOrder';
import {
  enableMerchandiseReturns,
  disableMerchandiseReturns,
} from '@commonTests/BO/customerService/enableDisableMerchandiseReturns';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import foHomePage from '@pages/FO/home';
import foLoginPage from '@pages/FO/login';
import foMyAccountPage from '@pages/FO/myAccount';
import foOrderHistoryPage from '@pages/FO/myAccount/orderHistory';
import invoicesPage from '@pages/BO/orders/invoices';
import ordersPage from '@pages/BO/orders';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';
import orderDetailsPage from '@pages/FO/myAccount/orderDetails';
import foMerchandiseReturnsPage from '@pages/FO/myAccount/merchandiseReturns';

// Import data
import {DefaultCustomer} from '@data/demo/customer';
import {PaymentMethods} from '@data/demo/paymentMethods';
import {Order, MerchandiseReturns} from '@data/types/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// context
const baseContext: string = 'functional_FO_userAccount_orderHistory_orderDetails_requestMerchandiseReturn';

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

  const orderData: Order = {
    customer: DefaultCustomer,
    productId: 1,
    productQuantity: 2,
    paymentMethod: PaymentMethods.wirePayment.moduleName,
  };
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

      const pageTitle: string = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForUpdatedPrefix', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle: string = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${Statuses.delivered.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateStatus', baseContext);

      const result: string = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.delivered.status);
      await expect(result).to.equal(Statuses.delivered.status);
    });

    it('should get the order reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstOrderUpdatedPrefix', baseContext);

      orderReference = await orderPageTabListBlock.getOrderReference(page) as string;
      await expect(orderReference).not.null;
    });
  });

  describe('Request merchandise return', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await foHomePage.goToFo(page);

      const isHomePage: boolean = await foHomePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageHeaderTitle: string = await foLoginPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foLoginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);

      const isCustomerConnected: boolean = await foMyAccountPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await foHomePage.goToMyAccountPage(page);

      const pageTitle: string = await foMyAccountPage.getPageTitle(page);
      await expect(pageTitle).to.equal(foMyAccountPage.pageTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle: string = await foOrderHistoryPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foOrderHistoryPage.pageTitle);
    });

    it('should go to order details page of the first order in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await foOrderHistoryPage.goToDetailsPage(page);

      const pageTitle: string = await orderDetailsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(orderDetailsPage.pageTitle);
    });

    it('should check the existence of order return form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isOrderReturnFormVisible', baseContext);

      const result: boolean = await orderDetailsPage.isOrderReturnFormVisible(page);
      await expect(result).to.be.true;
    });

    it('should create a merchandise return and check if merchandise return page is displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createMerchandiseReturn', baseContext);

      await orderDetailsPage.requestMerchandiseReturn(page, 'Test merchandise returns');

      const pageTitle: string = await foMerchandiseReturnsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(foMerchandiseReturnsPage.pageTitle);
    });

    it('should check the merchandise returns table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMerchandiseReturnsTable', baseContext);

      const result: MerchandiseReturns = await foMerchandiseReturnsPage.getMerchandiseReturnsDetails(page);
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
