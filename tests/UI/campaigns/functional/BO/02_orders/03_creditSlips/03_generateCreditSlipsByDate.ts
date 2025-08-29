// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

import {
  boCreditSlipsPage,
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  type Page,
  utilsDate,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_orders_creditSlips_generateCreditSlipsByDate';

/*
Pre-condition:
- Create order in FO
Scenario:
- Create credit slip on the created order
- Generate credit slip file by date
 */
describe('BO - Orders - Credit slips : Generate Credit slip file by date', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const futureDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'future');
  const creditSlipDocumentName: string = 'Credit slip';
  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 5,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition: Create order in FO
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create Credit slip ', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedOrderPage', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCreatedOrderStatus', baseContext);

      const result = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should add a partial refund', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPartialRefund', baseContext);

      await boOrdersViewBlockTabListPage.clickOnPartialRefund(page);

      const textMessage = await boOrdersViewBlockProductsPage.addPartialRefundProduct(page, 1, 1);
      expect(textMessage).to.contains(boOrdersViewBlockProductsPage.partialRefundValidationMessage);
    });

    it('should check the existence of the Credit slip document', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreditSlipDocumentName', baseContext);

      const documentType = await boOrdersViewBlockTabListPage.getDocumentType(page, 4);
      expect(documentType).to.be.equal(creditSlipDocumentName);
    });
  });

  describe('Generate Credit slip file by date', async () => {
    it('should go to Credit slips page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);

      await boOrdersViewBlockTabListPage.goToSubMenu(
        page,
        boOrdersViewBlockTabListPage.ordersParentLink,
        boOrdersViewBlockTabListPage.creditSlipsLink,
      );
      await boCreditSlipsPage.closeSfToolBar(page);

      const pageTitle = await boCreditSlipsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCreditSlipsPage.pageTitle);
    });

    it('should generate PDF file by date and check the file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generatePdfFileExistence', baseContext);

      // Generate credit slip
      const filePath = await boCreditSlipsPage.generatePDFByDateAndDownload(page);

      const exist = await utilsFile.doesFileExist(filePath);
      expect(exist).to.eq(true);
    });

    it('should check the error message when there is no credit slip in the entered date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessageNonexistentCreditSlip', baseContext);

      // Generate credit slip and get error message
      const textMessage = await boCreditSlipsPage.generatePDFByDateAndFail(page, futureDate, futureDate);
      expect(textMessage).to.equal(boCreditSlipsPage.errorMessageWhenGenerateFileByDate);
    });
  });
});
