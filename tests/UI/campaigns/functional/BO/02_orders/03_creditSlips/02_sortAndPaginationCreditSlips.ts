// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

// Import BO pages
import creditSlipsPage from '@pages/BO/orders/creditSlips';

import {
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
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

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
  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  const numberOfOrderToCreate: number = 11;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
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

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `updateCreatedOrderStatus${i}`,
          `${baseContext}_preTest_${i}`,
        );

        const textResult = await boOrdersPage.setOrderStatus(page, 1, dataOrderStatuses.shipped);
        expect(textResult).to.equal(boOrdersPage.successfulUpdateMessage);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToCreatedOrderPage${i}`,
          `${baseContext}_preTest_${i}`,
        );

        await boOrdersPage.goToOrder(page, 1);

        const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
      });

      it('should add a partial refund', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `addPartialRefund${i}`,
          `${baseContext}_preTest_${i}`,
        );

        await boOrdersViewBlockTabListPage.clickOnPartialRefund(page);

        const textMessage = await boOrdersViewBlockProductsPage.addPartialRefundProduct(page, 1, 1);
        expect(textMessage).to.contains(boOrdersViewBlockProductsPage.partialRefundValidationMessage);
      });

      it('should check the existence of the Credit slip document', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkCreditSlipDocumentName${i}`,
          `${baseContext}_preTest_${i}`,
        );

        const documentType = await boOrdersViewBlockTabListPage.getDocumentType(page, 4);
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

      await boOrdersViewBlockTabListPage.goToSubMenu(
        page,
        boOrdersViewBlockTabListPage.ordersParentLink,
        boOrdersViewBlockTabListPage.creditSlipsLink,
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

          const expectedResult: number[] = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult: string[] = await utilsCore.sortArray(nonSortedTable);

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
