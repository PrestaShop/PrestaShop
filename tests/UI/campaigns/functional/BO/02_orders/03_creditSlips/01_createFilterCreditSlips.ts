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

  const todayDate: string = utilsDate.getDateFormat('yyyy-mm-dd');
  const todayDateToCheck: string = utilsDate.getDateFormat('mm/dd/yyyy');
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

  describe('Create 2 credit slips for the same order', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
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

    const tests = [
      {args: {productID: 1, quantity: 1, documentRow: 4}},
      {args: {productID: 1, quantity: 2, documentRow: 5}},
    ];

    tests.forEach((test, index: number) => {
      it(`should create the partial refund n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addPartialRefund${index + 1}`, baseContext);

        await boOrdersViewBlockTabListPage.clickOnPartialRefund(page);

        const textMessage = await boOrdersViewBlockProductsPage.addPartialRefundProduct(
          page,
          test.args.productID,
          test.args.quantity,
        );
        expect(textMessage).to.contains(boOrdersViewBlockProductsPage.partialRefundValidationMessage);
      });

      it('should check the existence of the Credit slip document', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCreditSlipDocument${index + 1}`, baseContext);

        // Get document name
        const documentType = await boOrdersViewBlockTabListPage.getDocumentType(page, test.args.documentRow);
        expect(documentType).to.be.equal('Credit slip');
      });
    });
  });

  describe('Filter Credit slips', async () => {
    it('should go to Credit slips page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.creditSlipsLink,
      );
      await boCreditSlipsPage.closeSfToolBar(page);

      const pageTitle = await boCreditSlipsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCreditSlipsPage.pageTitle);
    });

    it('should reset all filters and get number of credit slips', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfCreditSlips = await boCreditSlipsPage.resetAndGetNumberOfLines(page);
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

        await boCreditSlipsPage.filterCreditSlips(
          page,
          test.args.filterBy,
          test.args.filterValue,
        );

        // Get number of credit slips
        const numberOfCreditSlipsAfterFilter = await boCreditSlipsPage.getNumberOfElementInGrid(page);
        expect(numberOfCreditSlipsAfterFilter).to.be.at.most(numberOfCreditSlips);

        for (let i = 1; i <= numberOfCreditSlipsAfterFilter; i++) {
          const textColumn = await boCreditSlipsPage.getTextColumnFromTableCreditSlips(
            page,
            i,
            test.args.columnName,
          );
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCreditSlipsAfterReset = await boCreditSlipsPage.resetAndGetNumberOfLines(page);
        expect(numberOfCreditSlipsAfterReset).to.be.equal(numberOfCreditSlips);
      });
    });

    it('should filter by Date issued \'From\' and \'To\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterDateIssued', baseContext);

      // Filter credit slips
      await boCreditSlipsPage.filterCreditSlipsByDate(page, todayDate, todayDate);

      // Check number of element
      const numberOfCreditSlipsAfterFilter = await boCreditSlipsPage.getNumberOfElementInGrid(page);
      expect(numberOfCreditSlipsAfterFilter).to.be.at.most(numberOfCreditSlips);

      for (let i = 1; i <= numberOfCreditSlipsAfterFilter; i++) {
        const textColumn = await boCreditSlipsPage.getTextColumnFromTableCreditSlips(page, i, 'date_add');
        expect(textColumn).to.contains(todayDateToCheck);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterDateIssuedReset', baseContext);

      const numberOfCreditSlipsAfterReset = await boCreditSlipsPage.resetAndGetNumberOfLines(page);
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
        await boCreditSlipsPage.filterCreditSlips(
          page,
          'id_credit_slip',
          creditSlip.args.id,
        );

        // Check text column
        const textColumn = await boCreditSlipsPage.getTextColumnFromTableCreditSlips(
          page,
          1,
          'id_order_slip',
        );
        expect(textColumn).to.contains(creditSlip.args.id);
      });

      it(`should download the ${creditSlip.args.number} credit slip and check the file existence`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `download${creditSlip.args.number}`, baseContext);

        const filePath = await boCreditSlipsPage.downloadCreditSlip(page);

        const exist = await utilsFile.doesFileExist(filePath);
        expect(exist).to.eq(true);
      });
    });
  });
});
