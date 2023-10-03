// Import utils
import basicHelper from '@utils/basicHelper';
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {createAddressTest} from '@commonTests/BO/customers/address';
import {createCustomerB2BTest, bulkDeleteCustomersTest, deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {disableB2BTest, enableB2BTest} from '@commonTests/BO/shopParameters/b2b';
import {createOrderByCustomerTest} from '@commonTests/FO/order';

// Import pages
import outstandingPage from '@pages/BO/customers/outstanding';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';

// Import data
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';
import OrderData from '@data/faker/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customers_outstanding_sortFilterOutstanding';

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
  let browserContext: BrowserContext;
  let page: Page;
  // Variable used to get the number of outstanding
  let numberOutstanding: number;

  // New B2B customers
  const createCustomerData1: CustomerData = new CustomerData();
  const createCustomerData2: CustomerData = new CustomerData();
  const createCustomerData3: CustomerData = new CustomerData();

  const customersData: CustomerData[] = [createCustomerData1, createCustomerData2, createCustomerData3];

  // Const used to get today date format
  const today: string = date.getDateFormat('yyyy-mm-dd');
  const dateToCheck: string = date.getDateFormat('mm/dd/yyyy');

  // Pre-Condition : Enable B2B
  enableB2BTest(baseContext);

  describe('PRE-TEST: Create outstanding', async () => {
    customersData.forEach((customerData: CustomerData, index: number) => {
      const addressData: AddressData = new AddressData({
        email: customerData.email,
        country: 'France',
      });
      const orderByCustomerData: OrderData = new OrderData({
        customer: customerData,
        products: [
          {
            product: Products.demo_1,
            quantity: 1,
          },
        ],
        deliveryAddress: addressData,
        paymentMethod: PaymentMethods.wirePayment,
      });

      // Pre-Condition : Create new B2B customer
      createCustomerB2BTest(customerData, `${baseContext}_preTest_createB2BAccount_${index}`);

      // Pre-Condition : Create new address
      createAddressTest(addressData, `${baseContext}_preTest_createAddress_${index}`);

      // Pre-condition : Create order from the FO
      createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_createOrder_${index}`);
    });
  });
  describe('Create outstanding, Filter & sort outstanding table', async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    // Pre-Condition : Update order status to payment accepted
    describe('PRE-TEST : Update order status to payment accepted', async () => {
      it('should login to BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to Orders > Orders page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should update created orders status with bulk action', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'bulkUpdateOrdersStatus', baseContext);

        const textResult = await ordersPage.bulkUpdateOrdersStatus(page, OrderStatuses.paymentAccepted.name, false, [1, 2, 3]);
        expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
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
        expect(pageTitle).to.contains(outstandingPage.pageTitle);
      });
      it('should reset filter and get the outstanding number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOutstanding', baseContext);

        await outstandingPage.resetFilter(page);

        numberOutstanding = await outstandingPage.getNumberOutstanding(page);
        expect(numberOutstanding).to.be.above(0);
      });
    });

    // Filter outstanding by: ID, Date, Customer, Company, Risk, Outstanding allowance
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
            filterValue: createCustomerData1.allowedOutstandingAmount.toString(),
          },
        },
      ];
      filterTests.forEach((test, index: number) => {
        it(`should filter by ${test.args.filterBy}`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier);

          await outstandingPage.filterTable(page, test.args.filterType, test.args.filterBy, test.args.filterValue);

          const numberOutstandingAfterFilter = await outstandingPage.getNumberOutstanding(page);
          expect(numberOutstandingAfterFilter).to.be.at.most(numberOutstanding);
        });

        it('should reset all filters and get the number of outstanding', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `resetFilterAndGetNumberOfOutstanding1_${index}`);

          await outstandingPage.resetFilter(page);

          const numberOutstandingAfterReset = await outstandingPage.getNumberOutstanding(page);
          expect(numberOutstandingAfterReset).to.be.equal(numberOutstanding);
        });
      });

      it('should filter the outstanding table by \'Date from\' and \'Date to\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByDate', baseContext);

        // Filter outstanding
        await outstandingPage.filterOutstandingByDate(page, today, today);

        // Check number of element
        const numberOfOutstandingAfterFilter = await outstandingPage.getNumberOutstanding(page);
        expect(numberOfOutstandingAfterFilter).to.be.at.most(numberOutstanding);

        for (let i = 1; i <= numberOfOutstandingAfterFilter; i++) {
          const textColumn = await outstandingPage.getTextColumn(page, 'date_add', i);
          expect(textColumn).to.contains(dateToCheck);
        }
      });

      it('should reset all filters and get the number of outstanding', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAndGetNumberOfOutstanding2');

        await outstandingPage.resetFilter(page);

        const numberOutstandingAfterReset = await outstandingPage.getNumberOutstanding(page);
        expect(numberOutstandingAfterReset).to.be.equal(numberOutstanding);
      });
    });

    // Sort outstanding by: ID, Date, Customer, Company, Outstanding allowance DESC and ASC
    describe('Sort outstanding table', async () => {
      it('should filter outstanding table by outstanding allowance', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByOutstandingAllowance2');

        await outstandingPage.filterTable(page, 'input', 'outstanding_allow_amount', '€');

        const numberOutstandingAfterFilter = await outstandingPage.getNumberOutstanding(page);
        expect(numberOutstandingAfterFilter).to.be.at.most(numberOutstanding);
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

          const nonSortedTable = await outstandingPage.getAllRowsColumnContent(page, 'outstanding_allow_amount');

          await outstandingPage.sortTable(page, 'outstanding_allow_amount', test.args.sortDirection);

          const sortedTable = await outstandingPage.getAllRowsColumnContent(page, 'outstanding_allow_amount');

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
            const expectedResult = await basicHelper.sortArray(nonSortedTable);

            if (test.args.sortDirection === 'asc') {
              expect(sortedTable).to.deep.equal(expectedResult);
            } else {
              expect(sortedTable).to.deep.equal(expectedResult.reverse());
            }
          }
        });
      });
      it('should reset all filters and get the number of outstanding', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAndGetNumberOfOutstanding3');

        await outstandingPage.resetFilter(page);

        const numberOutstandingAfterReset = await outstandingPage.getNumberOutstanding(page);
        expect(numberOutstandingAfterReset).to.be.equal(numberOutstanding);
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

          const nonSortedTable = await outstandingPage.getAllRowsColumnContent(page, test.args.sortBy);

          await outstandingPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

          const sortedTable = await outstandingPage.getAllRowsColumnContent(page, test.args.sortBy);

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
            const expectedResult = await basicHelper.sortArray(nonSortedTable);

            if (test.args.sortDirection === 'asc') {
              expect(sortedTable).to.deep.equal(expectedResult);
            } else {
              expect(sortedTable).to.deep.equal(expectedResult.reverse());
            }
          }
        });
      });
    });
  });

  // Post-Condition: Delete created customers
  customersData.forEach((customerData: CustomerData, index: number) => {
    deleteCustomerTest(customerData, `${baseContext}_postTest_${index + 1}`);
  });

  // Post-Condition : Disable B2B
  disableB2BTest(`${baseContext}_postTest_4`);
});
