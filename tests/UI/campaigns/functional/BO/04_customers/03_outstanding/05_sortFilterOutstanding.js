require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const basicHelper = require('@utils/basicHelper');
const testContext = require('@utils/testContext');
const {getDateFormat} = require('@utils/date');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import Common tests
const {enableB2BTest, disableB2BTest} = require('@commonTests/BO/shopParameters/enableDisableB2B');
const {createCustomerB2BTest, bulkDeleteCustomersTest} = require('@commonTests/BO/customers/createDeleteCustomer');
const {createAddressTest} = require('@commonTests/BO/customers/createDeleteAddress');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const outstandingPage = require('@pages/BO/customers/outstanding');
const ordersPage = require('@pages/BO/orders');

// Import faker data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');

// Import demo data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Statuses} = require('@data/demo/orderStatuses');

const baseContext = 'functional_BO_customers_outstanding_filterSortOutstanding';

let browserContext;
let page;

// Variable used to get the number of outstanding
let numberOutstanding;

// New B2B customers
const createCustomerData1 = new CustomerFaker();
const createCustomerData2 = new CustomerFaker();
const createCustomerData3 = new CustomerFaker();

const customersData = [createCustomerData1, createCustomerData2, createCustomerData3];

// Const used to get today date format
const today = getDateFormat('yyyy-mm-dd');
const dateToCheck = getDateFormat('mm/dd/yyyy');

/*
Pre-condition:
- Enable B2B
- Create 3 B2B customers
- Create 3 addresses
- Create 3 orders in FO
- Update orders status to payment accepted
Scenario:
- Filter outstanding by: ID, Date, Customer, Company, Risk, Outstanding allowance
- Sort outstanding by: ID, Date, Customer, Company, Outstanding allowance DESC and ASC
Post-condition:
- Delete created customers by bulk actions
- Disable B2B
*/
describe('BO - Customers - Outstanding : Filter and sort the Outstanding table', async () => {
  // Pre-Condition : Enable B2B
  enableB2BTest(baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('PRE-TEST: Create outstanding', async () => {
    it('should login to BO', async function () {
      await loginCommon.loginBO(this, page);
    });
    customersData.forEach((customerData, index = 1) => {
      const addressData = new AddressFaker({
        email: customerData.email,
        country: 'France',
      });
      const orderByCustomerData = {
        customer: customerData,
        product: 1,
        productQuantity: 1,
        address: addressData,
        paymentMethod: PaymentMethods.wirePayment.moduleName,
      };

      // Pre-Condition : Create new B2B customer
      createCustomerB2BTest(customerData, `${baseContext}_preTest_createB2BAccount_${index}`);

      // Pre-Condition : Create new address
      createAddressTest(addressData, `${baseContext}_preTest_createAddress_${index}`);

      // Pre-condition : Create order from the FO
      createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_createOrder_${index}`);

      // Pre-Condition : Update order status to payment accepted
      describe('PRE-TEST : Update order status to payment accepted', async () => {
        it('should go to Orders > Orders page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage_${index}`, baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.ordersParentLink,
            dashboardPage.ordersLink,
          );

          const pageTitle = await ordersPage.getPageTitle(page);
          await expect(pageTitle).to.contains(ordersPage.pageTitle);
        });

        it('should update order status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus_${index}`, baseContext);

          const textResult = await ordersPage.setOrderStatus(page, 1, Statuses.paymentAccepted);
          await expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
        });

        it('should check that the status is updated successfully', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkStatusBO_${index}`, baseContext);

          const orderStatus = await ordersPage.getTextColumn(page, 'osname', 1);
          await expect(orderStatus, 'Order status was not updated').to.equal(Statuses.paymentAccepted.status);
        });
      });
    });
  });

  // Go to the outstanding page
  describe('Go to the outstanding page', async () => {
    it('should go to \'Customers > Outstanding\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOutstandingPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.outstandingLink,
      );

      const pageTitle = await outstandingPage.getPageTitle(page);
      await expect(pageTitle).to.contains(outstandingPage.pageTitle);
    });
    it('should reset filter and get the outstanding number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOutstanding', baseContext);

      await outstandingPage.resetFilter(page);

      numberOutstanding = await outstandingPage.getNumberOutstanding(page);
      await expect(numberOutstanding).to.be.above(0);
    });
  });

  /*
  Filter outstanding by:
  ID, Date, Customer, Company, Risk, Outstanding allowance
*/
  describe('Filter outstanding table', async () => {
    const filterTests = [
      {
        args: {
          filterType: 'input',
          testIdentifier: 'filterByID',
          filterBy: 'id_invoice',
          filterValue: '2',
        },
      },
      {
        args: {
          filterType: 'input',
          testIdentifier: 'filterByCustomer',
          filterBy: 'customer',
          filterValue: createCustomerData1.lastName,
        },
      },
      {
        args: {
          filterType: 'input',
          testIdentifier: 'filterByCompany',
          filterBy: 'company',
          filterValue: createCustomerData2.company,
        },
      },
      {
        args: {
          filterType: 'select',
          testIdentifier: 'filterByRisk',
          filterBy: 'risk',
          filterValue: createCustomerData3.riskRating,
        },
      },
      {
        args: {
          filterType: 'input',
          testIdentifier: 'filterOutstandingAllowance1',
          filterBy: 'outstanding_allow_amount',
          filterValue: createCustomerData1.allowedOutstandingAmount,
        },
      },
    ];
    filterTests.forEach((test, index) => {
      it(`should filter by ${test.args.filterBy}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier);

        await outstandingPage.filterTable(page, test.args.filterType, test.args.filterBy, test.args.filterValue);

        const numberOutstandingAfterFilter = await outstandingPage.getNumberOutstanding(page);
        await expect(numberOutstandingAfterFilter).to.be.at.most(numberOutstanding);
      });

      it('should reset all filters and get the number of outstanding', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAndGetNumberOfOutstanding1_${index}`);

        await outstandingPage.resetFilter(page);

        const numberOutstandingAfterReset = await outstandingPage.getNumberOutstanding(page);
        await expect(numberOutstandingAfterReset).to.be.equal(numberOutstanding);
      });
    });

    it('should filter the outstanding table by \'Date from\' and \'Date to\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDate', baseContext);

      // Filter outstanding
      await outstandingPage.filterOutstandingByDate(page, today, today);

      // Check number of element
      const numberOfOutstandingAfterFilter = await outstandingPage.getNumberOutstanding(page);
      await expect(numberOfOutstandingAfterFilter).to.be.at.most(numberOutstanding);

      for (let i = 1; i <= numberOfOutstandingAfterFilter; i++) {
        const textColumn = await outstandingPage.getTextColumn(page, 'date_add', i);
        await expect(textColumn).to.contains(dateToCheck);
      }
    });

    it('should reset all filters and get the number of outstanding', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAndGetNumberOfOutstanding2');

      await outstandingPage.resetFilter(page);

      const numberOutstandingAfterReset = await outstandingPage.getNumberOutstanding(page);
      await expect(numberOutstandingAfterReset).to.be.equal(numberOutstanding);
    });
  });

  /*
    Sort outstanding by:
    ID, Date, Customer, Company, Outstanding allowance DESC and ASC
 */
  describe('Sort outstanding table', async () => {
    it('should filter outstanding table by outstanding allowance', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByOutstandingAllowance2');

      await outstandingPage.filterTable(page, 'input', 'outstanding_allow_amount', '€');

      const numberOutstandingAfterFilter = await outstandingPage.getNumberOutstanding(page);
      await expect(numberOutstandingAfterFilter).to.be.at.most(numberOutstanding);
    });
    const sortByOutstandingAllowance = [
      {
        args: {
          testIdentifier: 'sortByOutstandingAllowanceDesc',
          sortBy: 'outstanding_allow_amount',
          sortDirection: 'desc',
          isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByOutstandingAllowanceAsc',
          sortBy: 'outstanding_allow_amount',
          sortDirection: 'asc',
          isFloat: true,
        },
      },
    ];
    sortByOutstandingAllowance.forEach((test) => {
      it(`should sort by outstanding allowance ${test.args.sortDirection}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await outstandingPage.getAllRowsColumnContent(page, 'outstanding_allow_amount');

        await outstandingPage.sortTable(page, 'outstanding_allow_amount', test.args.sortDirection);

        let sortedTable = await outstandingPage.getAllRowsColumnContent(page, 'outstanding_allow_amount');

        nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
        sortedTable = await sortedTable.map(text => parseFloat(text));

        const expectedResult = await basicHelper.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
    it('should reset all filters and get the number of outstanding', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAndGetNumberOfOutstanding3');

      await outstandingPage.resetFilter(page);

      const numberOutstandingAfterReset = await outstandingPage.getNumberOutstanding(page);
      await expect(numberOutstandingAfterReset).to.be.equal(numberOutstanding);
    });

    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByDateDesc', sortBy: 'date_add', sortDirection: 'desc', isDate: true,
        },
      },
      {args: {testIdentifier: 'sortByCustomerDesc', sortBy: 'customer', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByCompanyDesc', sortBy: 'company', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByDateAsc', sortBy: 'date_add', sortDirection: 'asc', isDate: true,
        },
      },
      {args: {testIdentifier: 'sortByCustomerAsc', sortBy: 'customer', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByCompanyAsc', sortBy: 'company', sortDirection: 'asc'}},
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_invoice', sortDirection: 'asc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_invoice', sortDirection: 'desc', isFloat: true,
        },
      },
    ];
    sortTests.forEach((test) => {
      it(`should sort by ${test.args.sortBy} ${test.args.sortDirection}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await outstandingPage.getAllRowsColumnContent(page, test.args.sortBy);

        await outstandingPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await outstandingPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await basicHelper.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // Post-Condition: Delete created customers by bulk action
  customersData.forEach((customerData, index) => {
    bulkDeleteCustomersTest('email', customerData.email, `${baseContext}_postTest_${index + 1}`);
  });

  // Post-Condition : Disable B2B
  disableB2BTest(`${baseContext}_postTest_4`);
});
