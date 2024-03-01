// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

// Import pages
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {orderHistoryPage} from '@pages/FO/classic/myAccount/orderHistory';
import invoicesPage from '@pages/BO/orders/invoices';
import ordersPage from '@pages/BO/orders';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';
import {orderDetailsPage} from '@pages/FO/classic/myAccount/orderDetails';

// Import data
import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import OrderData from '@data/faker/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// context
const baseContext: string = 'functional_FO_classic_userAccount_orderHistory_orderDetails_downloadInvoice';

/*
Pre-condition:
- Create 2 orders by default customer
Scenario:
- Change the first order status to Shipped
- Go to FO and check the invoice for the first order
- Check that no invoice is visible for the second order
 */
describe('FO - Account - Order details : Download invoice', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let fileName: string;

  const orderData: OrderData = new OrderData({
    customer: Customers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_1`);

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Change the first order status to \'Delivered\'', async () => {
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
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForUpdatedPrefix', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle: string = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${OrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateStatus', baseContext);

      const result: string = await orderPageTabListBlock.modifyOrderStatus(page, OrderStatuses.shipped.name);
      expect(result).to.equal(OrderStatuses.shipped.name);
    });

    it('should get the invoice file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstOrderUpdatedPrefix', baseContext);

      // Get invoice file name
      fileName = await orderPageTabListBlock.getFileName(page);
      expect(fileName).to.not.eq(null);
    });
  });

  describe('Download invoice', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await foHomePage.goToFo(page);

      const isHomePage: boolean = await foHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageHeaderTitle: string = await foLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foLoginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected: boolean = await myAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await foHomePage.goToMyAccountPage(page);

      const pageTitle: string = await myAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle: string = await orderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
    });

    it('should go to order details page of the first order in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await orderHistoryPage.goToDetailsPage(page);

      const pageTitle: string = await orderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(orderDetailsPage.pageTitle);
    });

    it('should download the invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDownloadFile', baseContext);

      const downloadFilePath: string | null = await orderDetailsPage.downloadInvoice(page);

      const doesFileExist: boolean = await files.doesFileExist(downloadFilePath, 5000);
      expect(doesFileExist, 'File is not downloaded!').to.eq(true);

      const exist: boolean = await files.isTextInPDF(downloadFilePath, fileName);
      expect(exist).to.eq(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage2', baseContext);

      await foHomePage.goToMyAccountPage(page);

      const pageTitle: string = await myAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage2', baseContext);

      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle: string = await orderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
    });

    it('should go to order details page of the second order in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails2', baseContext);

      await orderHistoryPage.goToDetailsPage(page, 2);

      const pageTitle: string = await orderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(orderDetailsPage.pageTitle);
    });

    it('should check that no invoice is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoInvoiceIsVisible', baseContext);

      const isInvoiceVisible: boolean = await orderDetailsPage.isInvoiceVisible(page);
      expect(isInvoiceVisible).to.eq(false);
    });
  });
});
