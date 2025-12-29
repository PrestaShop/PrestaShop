import testContext from '@utils/testContext';
import {createOrderByCustomerTest} from '@commonTests/FO/hummingbird/order';
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';
import {expect} from 'chai';

import {
  boDashboardPage,
  boInvoicesPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyOrderDetailsPage,
  foHummingbirdMyOrderHistoryPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_userAccount_orderHistory_orderDetails_downloadInvoice';

/*
Pre-condition:
- Install the theme hummingbird
- Create 2 orders by default customer
Scenario:
- Change the first order status to Shipped
- Go to FO> Order details page and check the invoice for the first order
- Check that no invoice is visible for the second order
Post-condition:
- Uninstall the theme hummingbird
 */
describe('FO - User account - Order history - Order details : Download invoice from order details', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let fileName: string;

  const orderData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_0`);

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_1`);

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Change the first order status to \'Delivered\'', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageForUpdatedPrefix', baseContext);

      await boInvoicesPage.goToSubMenu(
        page,
        boInvoicesPage.ordersParentLink,
        boInvoicesPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForUpdatedPrefix', baseContext);

      // View order
      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateStatus', baseContext);

      const result = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should get the invoice file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstOrderUpdatedPrefix', baseContext);

      // Get invoice file name
      fileName = await boOrdersViewBlockTabListPage.getFileName(page);
      expect(fileName).to.not.eq(null);
    });
  });

  describe('Download invoice', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdLoginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.equal(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foHummingbirdMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await foHummingbirdMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyOrderHistoryPage.pageTitle);
    });

    it('should go to order details page of the first order in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await foHummingbirdMyOrderHistoryPage.goToDetailsPage(page);

      const pageTitle = await foHummingbirdMyOrderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyOrderDetailsPage.pageTitle);
    });

    it('should download the invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDownloadFile', baseContext);

      const downloadFilePath = await foHummingbirdMyOrderDetailsPage.downloadInvoice(page);

      const doesFileExist = await utilsFile.doesFileExist(downloadFilePath, 5000);
      expect(doesFileExist, 'File is not downloaded!').to.equal(true);

      const exist = await utilsFile.isTextInPDF(downloadFilePath, fileName);
      expect(exist).to.equal(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage2', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage2', baseContext);

      await foHummingbirdMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await foHummingbirdMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyOrderHistoryPage.pageTitle);
    });

    it('should go to order details page of the second order in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails2', baseContext);

      await foHummingbirdMyOrderHistoryPage.goToDetailsPage(page, 2);

      const pageTitle = await foHummingbirdMyOrderDetailsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyOrderDetailsPage.pageTitle);
    });

    it('should check that no invoice is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoInvoiceIsVisible', baseContext);

      const isInvoiceVisible = await foHummingbirdMyOrderDetailsPage.isInvoiceVisible(page);
      expect(isInvoiceVisible).to.equal(false);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
