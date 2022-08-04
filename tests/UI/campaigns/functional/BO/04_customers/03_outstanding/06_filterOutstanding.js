require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
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

const baseContext = 'functional_BO_customers_outstanding_sort';

let browserContext;
let page;

const leastNumberOutstanding = 1;

// Variable for the last outstanding ID created
let outstandingId;

// Variable used to get the number of outstanding
let numberOutstanding;

let customerData;

const today = getDateFormat('yyyy-mm-dd');
const dateToCheck = getDateFormat('mm/dd/yyyy');

describe('BO - Customers - Outstanding : Filter the Outstanding table', async () => {
  // Pre-Condition : Enable B2B
  enableB2BTest(baseContext);

  describe('PRE-TEST: Create outstanding', async () => {
    it('should login to BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    const creationTests = new Array(leastNumberOutstanding).fill(0, 0, leastNumberOutstanding);
    creationTests.forEach((value, index) => {
      customerData = new CustomerFaker();
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
      describe(`Pre-Test : Update order status to payment accepted ${index}`, async () => {
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

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Filter outstanding table', async () => {
    it('should got to BO > Customers > Outstanding page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOutstandingPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.outstandingLink,
      );
    });
    it('should reset filter and get the last outstanding ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOutstanding', baseContext);

      await outstandingPage.resetFilter(page);

      outstandingId = await outstandingPage.getTextColumn(page, 'id_invoice', 1);
      await expect(outstandingId).to.be.at.least(1);
      numberOutstanding = await outstandingPage.getNumberOutstanding(page);
      await expect(numberOutstanding).to.be.above(0);
    });

    const filterTests = [
      {
        args: {
          filterType: 'input',
          testIdentifier: 'filterByID',
          filterBy: 'id_invoice',
          filterValue: outstandingId,
        },
      },
      {
        args: {
          filterType: 'input',
          testIdentifier: 'filterByCustomer',
          filterBy: 'customer',
          filterValue: customerData.lastName,
        },
      },
      {
        args: {
          filterType: 'input',
          testIdentifier: 'filterByCompany',
          filterBy: 'company',
          filterValue: customerData.company,
        },
      },
      {
        args: {
          filterType: 'select',
          testIdentifier: 'filterByRisk',
          filterBy: 'risk',
          filterValue: customerData.riskRating,
        },
      },
      {
        args: {
          filterType: 'input',
          testIdentifier: 'filterOutstandingAllowance',
          filterBy: 'outstanding_allow_amount',
          filterValue: customerData.allowedOutstandingAmount,
        },
      },
    ];
    filterTests.forEach((test) => {
      it(`should filter by ${test.args.filterBy}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier);

        await outstandingPage.filterTable(page, test.args.filterType, test.args.filterBy, test.args.filterValue);

        const numberOutstandingAfterFilter = await outstandingPage.getNumberOutstanding(page);
        await expect(numberOutstandingAfterFilter).to.be.at.most(numberOutstanding);
      });

      it('should reset all filters and get the number of outstanding', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAndGetNumberOfOutstanding');

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
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAndGetNumberOfOutstanding');

      await outstandingPage.resetFilter(page);

      const numberOutstandingAfterReset = await outstandingPage.getNumberOutstanding(page);
      await expect(numberOutstandingAfterReset).to.be.equal(numberOutstanding);
    });
  });
  // Post-Condition : Disable B2B
  disableB2BTest(browserContext);

  // Post-condition: Delete created customers by bulk action
  bulkDeleteCustomersTest('email', customerData.email, `${baseContext}_postTest_1`);
});
