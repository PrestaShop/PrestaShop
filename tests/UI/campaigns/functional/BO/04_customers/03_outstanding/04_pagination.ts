// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {disableB2BTest, enableB2BTest} from '@commonTests/BO/shopParameters/b2b';
import {createOrderByCustomerTest} from '@commonTests/FO/order';

// Import pages
import outstandingPage from '@pages/BO/customers/outstanding';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';

// Import data
import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import OrderData from '@data/faker/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import bulkUpdateOrderStatusTest from "@commonTests/BO/orders/orders";

const baseContext: string = 'functional_BO_customers_outstanding_pagination';

/*
Pre-condition:
- Enable B2B
- Create 11 orders in FO
- Update orders status to payment accepted
Scenario:
- Change the items number to 10 per page
- Click on next button
- Click on previous
- Change the items number to 50 per page
Post-condition:
- Disable B2B
*/
describe('BO - Customers - Outstanding : Pagination of the outstanding page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  // Variable used to get the number of outstanding
  let numberOutstanding: number;

  // Const used to get the least number of outstanding to display pagination
  const numberOfOrdersToCreate: number = 11;
  const orderByCustomerData: OrderData = new OrderData({
    customer: Customers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });

  const orderStatusData: object = ({
    orderStatus: OrderStatuses.paymentAccepted.name,
    isAllOrders: false,
    rows: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
  });

  // Pre-Condition : Enable B2B
  enableB2BTest(baseContext);

  // Pre-Condition : Create 11 orders on FO
  describe('PRE-TEST: Create 11 orders on FO', async () => {
    const creationTests: number[] = new Array(numberOfOrdersToCreate).fill(0, 0, numberOfOrdersToCreate);
    creationTests.forEach((value: number, index: number) => {
      createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_${index}`);
    });
  });

  // Pre-Condition : Bulk update order status
  bulkUpdateOrderStatusTest(orderStatusData, baseContext);

  describe('Pagination next and previous', async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    it('should login to BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to BO > Customers > Outstanding page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOutstandingPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.outstandingLink,
      );

      const pageTitle = await outstandingPage.getPageTitle(page);
      expect(pageTitle).to.contains(outstandingPage.pageTitle);
    });

    it('should reset all filters and get the number of outstanding', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAndGetNumberOfOutstanding');

      await outstandingPage.resetFilter(page);

      numberOutstanding = await outstandingPage.getNumberOutstanding(page);
      expect(numberOutstanding).to.be.above(10);
    });

    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10', baseContext);

      const paginationNumber = await outstandingPage.selectPaginationLimit(page, 10);
      expect(paginationNumber, `Number of pages is not correct (page 1 / ${Math.ceil(numberOutstanding / 10)})`)
        .to.contains(`(page 1 / ${Math.ceil(numberOutstanding / 10)})`);
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await outstandingPage.paginationNext(page);
      expect(paginationNumber, `Number of pages is not (page 2 / ${Math.ceil(numberOutstanding / 10)})`)
        .to.contains(`(page 2 / ${Math.ceil(numberOutstanding / 10)})`);
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await outstandingPage.paginationPrevious(page);
      expect(paginationNumber, `Number of pages is not (page 1 / ${Math.ceil(numberOutstanding / 10)})`)
        .to.contains(`(page 1 / ${Math.ceil(numberOutstanding / 10)})`);
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await outstandingPage.selectPaginationLimit(page, 50);
      expect(paginationNumber, 'Number of pages is not correct').to.contains('(page 1 / 1)');
    });
  });

  // POST-Condition : Disable B2B
  disableB2BTest(baseContext);
});
