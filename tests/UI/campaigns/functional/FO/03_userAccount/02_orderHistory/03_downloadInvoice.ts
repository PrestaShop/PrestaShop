// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import {createOrderByCustomerTest} from '@commonTests/FO/createOrder';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import foHomePage from '@pages/FO/home';
import foLoginPage from '@pages/FO/login';
import foMyAccountPage from '@pages/FO/myAccount';
import foOrderHistoryPage from '@pages/FO/myAccount/orderHistory';
import invoicesPage from '@pages/BO/orders/invoices';
import ordersPage from '@pages/BO/orders';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

// Import data
import {DefaultCustomer} from '@data/demo/customer';
import {PaymentMethods} from '@data/demo/paymentMethods';
import Order from '@data/types/order';
import {Statuses} from '@data/demo/orderStatuses';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// context
const baseContext: string = 'functional_FO_userAccount_orderHistory_downloadInvoice';

/*
Pre-condition:
- Create 2 orders by default customer
Scenario:
- Change the first order status to Shipped
- Go to FO and check the invoice for the first order
- Check that no invoice is visible for the second order
 */
describe('FO - Account - Order history : download invoice', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let fileName: string;

  const orderData: Order = {
    customer: DefaultCustomer,
    productId: 1,
    productQuantity: 1,
    paymentMethod: PaymentMethods.wirePayment.moduleName,
  };

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
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForUpdatedPrefix', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle: string = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateStatus', baseContext);

      const result: string = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it('should get the invoice file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstOrderUpdatedPrefix', baseContext);

      // Get invoice file name
      fileName = await orderPageTabListBlock.getFileName(page) as string;
      await expect(fileName).not.null;
    });
  });

  describe('Download invoice', async () => {
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

    it('should check that the invoice of the first order in list is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

      const isVisible: boolean = await foOrderHistoryPage.isInvoiceVisible(page, 1);
      await expect(isVisible, 'The invoice file is not existing!').to.be.true;
    });

    it('should download the invoice and check the invoice ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoice', baseContext);

      const downloadFilePath: string = await foOrderHistoryPage.downloadInvoice(page);

      const exist: boolean = await files.isTextInPDF(downloadFilePath, fileName);
      await expect(exist).to.be.true;
    });

    it('should check that no invoice is visible for the second order in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoInvoice', baseContext);

      const isVisible: boolean = await foOrderHistoryPage.isInvoiceVisible(page, 2);
      await expect(isVisible).to.be.false;
    });
  });
});
