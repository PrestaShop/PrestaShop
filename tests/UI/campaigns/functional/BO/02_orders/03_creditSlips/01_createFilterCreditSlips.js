require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const {getDateFormat} = require('@utils/date');

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

const baseContext = 'functional_BO_orders_creditSlips_createFilterCreditSlips';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

let numberOfCreditSlips = 0;
const todayDate = getDateFormat('yyyy-mm-dd');
const todayDateToCheck = getDateFormat('mm/dd/yyyy');
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 5,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
Pre-condition:
- Create order
Scenario:
- Create 2 credit slips for the same order
- Filter Credit slips table( by ID, Order ID, Date issued From and To)
- Download the 2 credit slip files and check them
 */
describe('BO - Orders - Credit slips : Create, filter and check credit slips file', async () => {
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
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCreatedOrderStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    const tests = [
      {args: {productID: 1, quantity: 1, documentRow: 4}},
      {args: {productID: 1, quantity: 2, documentRow: 5}},
    ];

    tests.forEach((test, index) => {
      it(`should create the partial refund n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addPartialRefund${index + 1}`, baseContext);

        await orderPageTabListBlock.clickOnPartialRefund(page);

        const textMessage = await orderPageProductsBlock.addPartialRefundProduct(
          page,
          test.args.productID,
          test.args.quantity,
        );
        await expect(textMessage).to.contains(orderPageProductsBlock.partialRefundValidationMessage);
      });

      it('should check the existence of the Credit slip document', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCreditSlipDocument${index + 1}`, baseContext);

        // Get document name
        const documentType = await orderPageTabListBlock.getDocumentType(page, test.args.documentRow);
        await expect(documentType).to.be.equal('Credit slip');
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
      await expect(pageTitle).to.contains(creditSlipsPage.pageTitle);
    });

    it('should reset all filters and get number of credit slips', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfCreditSlips = await creditSlipsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCreditSlips).to.be.above(0);
    });

    const tests = [
      {
        args:
          {
            testIdentifier: 'filterIdCreditSlip',
            filterBy: 'id_credit_slip',
            filterValue: 1,
            columnName: 'id_order_slip',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterIdOrder',
            filterBy: 'id_order',
            filterValue: 4,
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
        await expect(numberOfCreditSlipsAfterFilter).to.be.at.most(numberOfCreditSlips);

        for (let i = 1; i <= numberOfCreditSlipsAfterFilter; i++) {
          const textColumn = await creditSlipsPage.getTextColumnFromTableCreditSlips(
            page,
            i,
            test.args.columnName,
          );
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCreditSlipsAfterReset = await creditSlipsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCreditSlipsAfterReset).to.be.equal(numberOfCreditSlips);
      });
    });

    it('should filter by Date issued \'From\' and \'To\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterDateIssued', baseContext);

      // Filter credit slips
      await creditSlipsPage.filterCreditSlipsByDate(page, todayDate, todayDate);

      // Check number of element
      const numberOfCreditSlipsAfterFilter = await creditSlipsPage.getNumberOfElementInGrid(page);
      await expect(numberOfCreditSlipsAfterFilter).to.be.at.most(numberOfCreditSlips);

      for (let i = 1; i <= numberOfCreditSlipsAfterFilter; i++) {
        const textColumn = await creditSlipsPage.getTextColumnFromTableCreditSlips(page, i, 'date_add');
        await expect(textColumn).to.contains(todayDateToCheck);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterDateIssuedReset', baseContext);

      const numberOfCreditSlipsAfterReset = await creditSlipsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCreditSlipsAfterReset).to.be.equal(numberOfCreditSlips);
    });
  });

  const creditSlips = [
    {args: {number: 'first', id: 1}},
    {args: {number: 'second', id: 2}},
  ];

  creditSlips.forEach((creditSlip) => {
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

        await expect(textColumn).to.contains(creditSlip.args.id);
      });

      it(`should download the ${creditSlip.args.number} credit slip and check the file existence`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `download${creditSlip.args.number}`, baseContext);

        const filePath = await creditSlipsPage.downloadCreditSlip(page);

        const exist = await files.doesFileExist(filePath);
        await expect(exist).to.be.true;
      });
    });
  });
});
