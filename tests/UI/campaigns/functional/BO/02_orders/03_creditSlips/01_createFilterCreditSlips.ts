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

const baseContext: string = 'functional_BO_orders_creditSlips_createFilterCreditSlips';

/*
Pre-condition:
- Create order
Scenario:
- Create 2 credit slips for the same order
- Filter Credit slips table( by ID, Order ID, Date issued From and To)
- Download the 2 credit slip files and check them
 */
describe('BO - Orders - Credit slips : Create, filter and check credit slips file', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCreditSlips: number = 0;

  const todayDate: string = date.getDateFormat('yyyy-mm-dd');
  const todayDateToCheck: string = date.getDateFormat('mm/dd/yyyy');
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

  describe('Create 2 credit slips for the same order', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
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

    const tests = [
      {args: {productID: 1, quantity: 1, documentRow: 4}},
      {args: {productID: 1, quantity: 2, documentRow: 5}},
    ];

    tests.forEach((test, index: number) => {
      it(`should create the partial refund n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addPartialRefund${index + 1}`, baseContext);

        await orderPageTabListBlock.clickOnPartialRefund(page);

        const textMessage = await orderPageProductsBlock.addPartialRefundProduct(
          page,
          test.args.productID,
          test.args.quantity,
        );
        expect(textMessage).to.contains(orderPageProductsBlock.partialRefundValidationMessage);
      });

      it('should check the existence of the Credit slip document', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCreditSlipDocument${index + 1}`, baseContext);

        // Get document name
        const documentType = await orderPageTabListBlock.getDocumentType(page, test.args.documentRow);
        expect(documentType).to.be.equal('Credit slip');
      });
    });
  });

  describe('Filter Credit slips', async () => {
    it('should go to Credit slips page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.creditSlipsLink,
      );
      await creditSlipsPage.closeSfToolBar(page);

      const pageTitle = await creditSlipsPage.getPageTitle(page);
      expect(pageTitle).to.contains(creditSlipsPage.pageTitle);
    });

    it('should reset all filters and get number of credit slips', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfCreditSlips = await creditSlipsPage.resetAndGetNumberOfLines(page);
      expect(numberOfCreditSlips).to.be.above(0);
    });

    const tests = [
      {
        args:
          {
            testIdentifier: 'filterIdCreditSlip',
            filterBy: 'id_credit_slip',
            filterValue: '1',
            columnName: 'id_order_slip',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterIdOrder',
            filterBy: 'id_order',
            filterValue: '4',
            columnName: 'id_order',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await creditSlipsPage.filterCreditSlips(
          page,
          test.args.filterBy,
          test.args.filterValue,
        );

        // Get number of credit slips
        const numberOfCreditSlipsAfterFilter = await creditSlipsPage.getNumberOfElementInGrid(page);
        expect(numberOfCreditSlipsAfterFilter).to.be.at.most(numberOfCreditSlips);

        for (let i = 1; i <= numberOfCreditSlipsAfterFilter; i++) {
          const textColumn = await creditSlipsPage.getTextColumnFromTableCreditSlips(
            page,
            i,
            test.args.columnName,
          );
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCreditSlipsAfterReset = await creditSlipsPage.resetAndGetNumberOfLines(page);
        expect(numberOfCreditSlipsAfterReset).to.be.equal(numberOfCreditSlips);
      });
    });

    it('should filter by Date issued \'From\' and \'To\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterDateIssued', baseContext);

      // Filter credit slips
      await creditSlipsPage.filterCreditSlipsByDate(page, todayDate, todayDate);

      // Check number of element
      const numberOfCreditSlipsAfterFilter = await creditSlipsPage.getNumberOfElementInGrid(page);
      expect(numberOfCreditSlipsAfterFilter).to.be.at.most(numberOfCreditSlips);

      for (let i = 1; i <= numberOfCreditSlipsAfterFilter; i++) {
        const textColumn = await creditSlipsPage.getTextColumnFromTableCreditSlips(page, i, 'date_add');
        expect(textColumn).to.contains(todayDateToCheck);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterDateIssuedReset', baseContext);

      const numberOfCreditSlipsAfterReset = await creditSlipsPage.resetAndGetNumberOfLines(page);
      expect(numberOfCreditSlipsAfterReset).to.be.equal(numberOfCreditSlips);
    });
  });

  [
    {args: {number: 'first', id: '1'}},
    {args: {number: 'second', id: '2'}},
  ].forEach((creditSlip) => {
    describe(`Download the ${creditSlip.args.number} Credit slips and check it`, async () => {
      it(`should filter credit slip by id '${creditSlip.args.id}'`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `filterToDownload${creditSlip.args.number}`,
          baseContext,
        );

        // Filter credit slips
        await creditSlipsPage.filterCreditSlips(
          page,
          'id_credit_slip',
          creditSlip.args.id,
        );

        // Check text column
        const textColumn = await creditSlipsPage.getTextColumnFromTableCreditSlips(
          page,
          1,
          'id_order_slip',
        );
        expect(textColumn).to.contains(creditSlip.args.id);
      });

      it(`should download the ${creditSlip.args.number} credit slip and check the file existence`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `download${creditSlip.args.number}`, baseContext);

        const filePath = await creditSlipsPage.downloadCreditSlip(page);

        const exist = await files.doesFileExist(filePath);
        expect(exist).to.eq(true);
      });
    });
  });
});
