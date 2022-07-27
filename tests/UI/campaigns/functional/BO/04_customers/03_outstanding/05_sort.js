require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const basicHelper = require('@utils/basicHelper');

// Import common tests
const {enableB2BTest, disableB2BTest} = require('@commonTests/BO/shopParameters/enableDisableB2B');
const {createCustomerB2BTest} = require('@commonTests/BO/customers/createDeleteCustomer');
const {createAddressTest} = require('@commonTests/BO/customers/createDeleteAddress');

// const {createAccountTest, createAddressTest} = require('@commonTests/FO/createAccount');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');


// Import data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Statuses} = require('@data/demo/orderStatuses');

// Import test context
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const outstandingPage = require('@pages/BO/customers/outstanding');
const ordersPage = require('@pages/BO/orders');

const baseContext = 'functional_BO_customers_outstanding_sort';

let browserContext;
let page;

// Variable used to get the number of outstanding
let numberOutstanding;

// Const used to get the least number of outstanding to display pagination
const firstPagination = 1;

describe('BO - Customers - Outstanding : Sort of the outstanding page', async () => {
  // Pre-Condition : Enable B2B
  enableB2BTest(baseContext);

  describe('PRE-TEST: Create outstanding', async () => {
    it('should login to BO', async function () {
      await loginCommon.loginBO(this, page);
    });
    const creationTests = new Array(firstPagination).fill(0, 0, firstPagination);
    creationTests.forEach((value, index) => {
      const customerData = new CustomerFaker();
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
      // Pre-condition: Create new customer
      createCustomerB2BTest(customerData, `${baseContext}_preTest_createB2BAccount_${index}`);

      // Pre-condition: Create new address
      createAddressTest(addressData, `${baseContext}_preTest_createAddress_${index}`);

      // Pre-condition: Create order
      createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_createOrder_${index}`);

      // Pre-condition: Update order status to payment accepted
      describe(`PRE-TEST_${index}: Update order status to payment accepted`, async () => {
        it(`should go to 'Orders > Orders' page ${index}`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.ordersParentLink,
            dashboardPage.ordersLink,
          );

          const pageTitle = await ordersPage.getPageTitle(page);
          await expect(pageTitle).to.contains(ordersPage.pageTitle);
        });

        it('should update order status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

          const textResult = await ordersPage.setOrderStatus(page, 1, Statuses.paymentAccepted);
          await expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
        });

        it('should check that the status is updated successfully', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStatusBO', baseContext);

          const orderStatus = await ordersPage.getTextColumn(page, 'osname', 1);
          await expect(orderStatus, 'Order status was not updated').to.equal(Statuses.paymentAccepted.status);
        });
      });
    });
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Sort outstanding table', async () => {
    it('should go to BO > Customers > Outstanding page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOutstandingPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.outstandingLink,
      );
    });

    it('should reset all filters and get the number of outstanding', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAndGetNumberOfOutstanding');

      await outstandingPage.resetFilter(page);

      numberOutstanding = await outstandingPage.getNumberOutstanding(page);
      await expect(numberOutstanding).to.be.above(0);
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
          testIdentifier: 'sortByOutstandingAllowanceDesc',
          sortBy: 'outstanding_allow_amount',
          sortDirection: 'desc',
          isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateAsc', sortBy: 'date_add', sortDirection: 'asc', isDate: true,
        },
      },
      {args: {testIdentifier: 'sortByCustomerAsc', sortBy: 'customer', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByCompanyAsc', sortBy: 'company', sortDirection: 'asc'}},
      {
        args: {
          testIdentifier: 'sortByOutstandingAllowanceAsc',
          sortBy: 'outstanding_allow_amount',
          sortDirection: 'asc',
          isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_invoice', sortDirection: 'asc', isNumber: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_invoice', sortDirection: 'desc', isNumber: true,
        },
      },
    ];
    sortTests.forEach((test) => {
      it(`should sort by ${test.args.sortBy} ${test.args.sortDirection}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await outstandingPage.getAllRowsColumnContent(page, test.args.sortBy);
        console.log(outstandingPage.getAllRowsColumnContent(page, test.args.sortBy));

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
});
