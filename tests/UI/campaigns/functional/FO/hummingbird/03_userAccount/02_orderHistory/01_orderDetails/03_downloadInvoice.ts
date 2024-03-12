// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/hummingbird/order';
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import BO pages
import invoicesPage from '@pages/BO/orders/invoices';
import ordersPage from '@pages/BO/orders';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import loginPage from '@pages/FO/hummingbird/login';
import myAccountPage from '@pages/FO/hummingbird/myAccount';
import orderHistoryPage from '@pages/FO/hummingbird/myAccount/orderHistory';

// Import data
import Customers from '@data/demo/customers';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import OrderStatuses from '@data/demo/orderStatuses';
import OrderData from '@data/faker/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// context
const baseContext: string = 'functional_FO_hummingbird_userAccount_orderHistory_downloadInvoice';

/*
Pre-condition:
- Create 2 orders by default customer
Scenario:
- Change the first order status to Shipped
- Go to FO and check the invoice for the first order
- Check that no invoice is visible for the second order
 */
describe('FO - Account - Order history : Download invoice', async () => {
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

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_0`);

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

      await homePage.goToFo(page);

      const isHomePage: boolean = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await homePage.goToLoginPage(page);

      const pageHeaderTitle: string = await loginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(loginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await loginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected: boolean = await myAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await homePage.goToMyAccountPage(page);

      const pageTitle: string = await myAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle: string = await orderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
    });

    it('should check that the invoice of the first order in list is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

      const isVisible: boolean = await orderHistoryPage.isInvoiceVisible(page, 1);
      expect(isVisible, 'The invoice file is not existing!').to.eq(true);
    });

    it('should download the invoice and check the invoice ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoice', baseContext);

      const downloadFilePath: string|null = await orderHistoryPage.downloadInvoice(page);

      const exist: boolean = await files.isTextInPDF(downloadFilePath, fileName);
      expect(exist).to.eq(true);
    });

    it('should check that no invoice is visible for the second order in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoInvoice', baseContext);

      const isVisible: boolean = await orderHistoryPage.isInvoiceVisible(page, 2);
      expect(isVisible).to.eq(false);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
