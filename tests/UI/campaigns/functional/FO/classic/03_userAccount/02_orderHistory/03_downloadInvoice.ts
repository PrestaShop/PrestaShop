import testContext from '@utils/testContext';
import {expect} from 'chai';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

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
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyOrderHistoryPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_userAccount_orderHistory_downloadInvoice';

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

      const pageTitle: string = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForUpdatedPrefix', baseContext);

      // View order
      await boOrdersPage.goToOrder(page, 1);

      const pageTitle: string = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateStatus', baseContext);

      const result: string = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
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

      await foClassicHomePage.goToFo(page);

      const isHomePage: boolean = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageHeaderTitle: string = await foClassicLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected: boolean = await foClassicMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);

      const pageTitle: string = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicMyAccountPage.pageTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle: string = await foClassicMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyOrderHistoryPage.pageTitle);
    });

    it('should check that the invoice of the first order in list is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

      const isVisible: boolean = await foClassicMyOrderHistoryPage.isInvoiceVisible(page, 1);
      expect(isVisible, 'The invoice file is not existing!').to.eq(true);
    });

    it('should download the invoice and check the invoice ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoice', baseContext);

      const downloadFilePath: string|null = await foClassicMyOrderHistoryPage.downloadInvoice(page);

      const exist: boolean = await utilsFile.isTextInPDF(downloadFilePath, fileName);
      expect(exist).to.eq(true);
    });

    it('should check that no invoice is visible for the second order in list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoInvoice', baseContext);

      const isVisible: boolean = await foClassicMyOrderHistoryPage.isInvoiceVisible(page, 2);
      expect(isVisible).to.eq(false);
    });
  });
});
