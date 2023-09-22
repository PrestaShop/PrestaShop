// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
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

const baseContext: string = 'functional_BO_orders_creditSlips_sortAndPaginationCreditSlips';

/*
Pre-condition:
- Create 11 orders in FO
- Create credit slip for each order
Scenario:
- Go to Orders > credit slips page
- Sort credit slips table by (ID, order ID, date issued)
- Pagination next and previous
 */

describe('BO - Orders - Credit slips : Sort (by ID, Date and OrderID) and Pagination of Credit Slips', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCreditSlips: number;

  const creditSlipDocumentName: string = 'Credit slip';
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
  const numberOfOrderToCreate: number = 11;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  for (let i = 0; i < numberOfOrderToCreate; i++) {
    // Pre-condition: Create order in FO
    createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_${i}`);

    // eslint-disable-next-line no-loop-func
    describe(`Create Credit slip n°${i + 1}`, async () => {
      it('should go to \'Orders > Orders\' page\'', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToOrdersPage${i}`,
          `${baseContext}_preTest_${i}`,
        );

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it(`should change the order status to '${OrderStatuses.shipped.name}' and check it`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `updateCreatedOrderStatus${i}`,
          `${baseContext}_preTest_${i}`,
        );

        const textResult = await ordersPage.setOrderStatus(page, 1, OrderStatuses.shipped);
        expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToCreatedOrderPage${i}`,
          `${baseContext}_preTest_${i}`,
        );

        await ordersPage.goToOrder(page, 1);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });

      it('should add a partial refund', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `addPartialRefund${i}`,
          `${baseContext}_preTest_${i}`,
        );

        await orderPageTabListBlock.clickOnPartialRefund(page);

        const textMessage = await orderPageProductsBlock.addPartialRefundProduct(page, 1, 1);
        expect(textMessage).to.contains(orderPageProductsBlock.partialRefundValidationMessage);
      });

      it('should check the existence of the Credit slip document', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkCreditSlipDocumentName${i}`,
          `${baseContext}_preTest_${i}`,
        );

        const documentType = await orderPageTabListBlock.getDocumentType(page, 4);
        expect(documentType).to.be.equal(creditSlipDocumentName);
      });
    });
  }

  describe('Sort Credit Slip Table', async () => {
    it('should go to Credit slips page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'goToCreditSlipsPage',
        baseContext,
      );

      await orderPageTabListBlock.goToSubMenu(
        page,
        orderPageTabListBlock.ordersParentLink,
        orderPageTabListBlock.creditSlipsLink,
      );
      await creditSlipsPage.closeSfToolBar(page);

      const pageTitle = await creditSlipsPage.getPageTitle(page);
      expect(pageTitle).to.contains(creditSlipsPage.pageTitle);
    });

    it('should reset all filters and get number of credit slips', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'resetFilterFirst',
        baseContext,
      );

      numberOfCreditSlips = await creditSlipsPage.resetAndGetNumberOfLines(page);
      expect(numberOfCreditSlips).to.be.above(0);
    });

    it('should change the items number to 100 per page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'changeItemssNumberTo100',
        baseContext,
      );

      const paginationNumber = await creditSlipsPage.selectPaginationLimit(page, 100);
      expect(paginationNumber, 'Number of pages is not correct').to.contains('(page 1 / 1)');
    });

    const tests = [
      {
        args: {
          testIdentifier: 'sortByOrderIDAsc', sortBy: 'id_order', sortDirection: 'asc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByOrderIDDesc', sortBy: 'id_order', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateDesc', sortBy: 'date_add', sortDirection: 'desc', isDate: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateAsc', sortBy: 'date_add', sortDirection: 'asc', isDate: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_order_slip', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_order_slip', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    tests.forEach((test) => {
      it(`should sort by ${test.args.sortBy} ${test.args.sortDirection}`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          test.args.testIdentifier,
          baseContext,
        );

        const nonSortedTable = await creditSlipsPage.getAllRowsColumnContent(page, test.args.sortBy);

        await creditSlipsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await creditSlipsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult: number[] = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult: string[] = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  describe('Pagination credit slip table', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'changeItemssNumberTo10',
        baseContext,
      );

      const numberOfCreditSlips = await creditSlipsPage.resetAndGetNumberOfLines(page);
      const paginationNumber = await creditSlipsPage.selectPaginationLimit(page, 10);
      expect(paginationNumber, `Number of pages is not correct (page 1 / ${Math.ceil(numberOfCreditSlips / 10)})`)
        .to.contains(`(page 1 / ${Math.ceil(numberOfCreditSlips / 10)})`);
    });

    it('should click on next', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'clickOnNext',
        baseContext,
      );

      const numberOfCreditSlips = await creditSlipsPage.resetAndGetNumberOfLines(page);
      const paginationNumber = await creditSlipsPage.paginationNext(page);
      expect(paginationNumber, `Number of pages is not (page 2 / ${Math.ceil(numberOfCreditSlips / 10)})`)
        .to.contains(`(page 2 / ${Math.ceil(numberOfCreditSlips / 10)})`);
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'clickOnPrevious',
        baseContext,
      );

      const paginationNumber = await creditSlipsPage.paginationPrevious(page);
      expect(paginationNumber, `Number of pages is not (page 1 / ${Math.ceil(numberOfCreditSlips / 10)})`)
        .to.contains(`(page 1 / ${Math.ceil(numberOfCreditSlips / 10)})`);
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'changeItemsNumberTo50',
        baseContext,
      );

      const paginationNumber = await creditSlipsPage.selectPaginationLimit(page, 50);
      expect(paginationNumber, 'Number of pages is not correct').to.contains('(page 1 / 1)');
    });
  });
});
