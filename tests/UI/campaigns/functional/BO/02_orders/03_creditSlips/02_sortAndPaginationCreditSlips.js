require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const basicHelper = require('@utils/basicHelper');


// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders/index');
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');
const orderPageProductsBlock = require('@pages/BO/orders/view/productsBlock');
const creditSlipsPage = require('@pages/BO/orders/creditSlips/index');

// Import data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_creditSlips_sortAndPaginationCreditSlips';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfCreditSlips;

const creditSlipDocumentName = 'Credit slip';
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

const numberOfOrderToCreate = 11;

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
    createOrderByCustomerTest(orderByCustomerData, `baseContext_${i}`);

    // eslint-disable-next-line no-loop-func
    describe(`Create Credit slip n°${i + 1}`, async () => {
      it('should go to \'Orders > Orders\' page\'', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToOrdersPage${i}`,
          baseContext,
        );

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToCreatedOrderPage${i}`,
          baseContext,
        );

        await ordersPage.goToOrder(page, 1);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });

      it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `updateCreatedOrderStatus${i}`,
          baseContext,
        );

        const result = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.shipped.status);
        await expect(result).to.equal(Statuses.shipped.status);
      });

      it('should add a partial refund', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `addPartialRefund${i}`,
          baseContext,
        );

        await orderPageTabListBlock.clickOnPartialRefund(page);

        const textMessage = await orderPageProductsBlock.addPartialRefundProduct(page, 1, 1);
        await expect(textMessage).to.contains(orderPageProductsBlock.partialRefundValidationMessage);
      });

      it('should check the existence of the Credit slip document', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkCreditSlipDocumentName${i}`,
          baseContext,
        );

        const documentType = await orderPageTabListBlock.getDocumentType(page, 4);
        await expect(documentType).to.be.equal(creditSlipDocumentName);
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
      await expect(pageTitle).to.contains(creditSlipsPage.pageTitle);
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

      const paginationNumber = await creditSlipsPage.selectPaginationLimit(page, '100');
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

        let nonSortedTable = await creditSlipsPage.getAllRowsColumnContent(page, test.args.sortBy);

        await creditSlipsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await creditSlipsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await basicHelper.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          expect(sortedTable).to.deep.equal(expectedResult.reverse());
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
      const paginationNumber = await creditSlipsPage.selectPaginationLimit(page, '10');
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

      const paginationNumber = await creditSlipsPage.selectPaginationLimit(page, '50');
      expect(paginationNumber, 'Number of pages is not correct').to.contains('(page 1 / 1)');
    });
  });
});
