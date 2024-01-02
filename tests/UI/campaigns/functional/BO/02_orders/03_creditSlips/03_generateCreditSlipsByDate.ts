// Import utils
import date from '@utils/date';
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/order';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import creditSlipsPage from '@pages/BO/orders/creditSlips';
import ordersPage from '@pages/BO/orders';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

// Import data
import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import OrderData from '@data/faker/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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

  const futureDate: string = date.getDateFormat('yyyy-mm-dd', 'future');
  const creditSlipDocumentName: string = 'Credit slip';
  const orderByCustomerData: OrderData = new OrderData({
    customer: Customers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 5,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });

  // Pre-condition: Create order in FO
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create Credit slip ', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${OrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCreatedOrderStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, OrderStatuses.shipped.name);
      expect(result).to.equal(OrderStatuses.shipped.name);
    });

    it('should add a partial refund', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPartialRefund', baseContext);

      await orderPageTabListBlock.clickOnPartialRefund(page);

      const textMessage = await orderPageProductsBlock.addPartialRefundProduct(page, 1, 1);
      expect(textMessage).to.contains(orderPageProductsBlock.partialRefundValidationMessage);
    });

    it('should check the existence of the Credit slip document', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreditSlipDocumentName', baseContext);

      const documentType = await orderPageTabListBlock.getDocumentType(page, 4);
      expect(documentType).to.be.equal(creditSlipDocumentName);
    });
  });

  describe('Generate Credit slip file by date', async () => {
    it('should go to Credit slips page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);

      await orderPageTabListBlock.goToSubMenu(
        page,
        orderPageTabListBlock.ordersParentLink,
        orderPageTabListBlock.creditSlipsLink,
      );
      await creditSlipsPage.closeSfToolBar(page);

      const pageTitle = await creditSlipsPage.getPageTitle(page);
      expect(pageTitle).to.contains(creditSlipsPage.pageTitle);
    });

    it('should generate PDF file by date and check the file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generatePdfFileExistence', baseContext);

      // Generate credit slip
      const filePath = await creditSlipsPage.generatePDFByDateAndDownload(page);

      const exist = await files.doesFileExist(filePath);
      expect(exist).to.eq(true);
    });

    it('should check the error message when there is no credit slip in the entered date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessageNonexistentCreditSlip', baseContext);

      // Generate credit slip and get error message
      const textMessage = await creditSlipsPage.generatePDFByDateAndFail(page, futureDate, futureDate);
      expect(textMessage).to.equal(creditSlipsPage.errorMessageWhenGenerateFileByDate);
    });
  });
});
