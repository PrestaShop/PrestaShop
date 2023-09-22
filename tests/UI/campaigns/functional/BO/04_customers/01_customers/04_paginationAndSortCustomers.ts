// Import utils
import basicHelper from '@utils/basicHelper';
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import importFileTest from '@commonTests/BO/advancedParameters/importFile';
import {bulkDeleteCustomersTest} from '@commonTests/BO/customers/customer';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import customersPage from '@pages/BO/customers';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import ImportCustomers from '@data/import/customers';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'functional_BO_customers_customers_paginationAndSortCustomers';

/*
Pre-condition:
- Import list of customers
Scenario:
- Paginate between pages
- Sort customers table
Post-condition:
- Delete imported customers with bulk actions
 */
describe('BO - Customers - Customers : Pagination and sort customers table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCustomers: number = 0;

  // Variable used to create customers csv file
  const fileName: string = 'customers.csv';

  // Pre-condition: Import list of categories
  importFileTest(fileName, ImportCustomers.entity, `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    // Create csv file with all customers data
    await files.createCSVFile('.', fileName, ImportCustomers);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    // Delete created csv file
    await files.deleteFile(fileName);
  });

  // 1 : Go to customers page
  describe('Go to \'Customers > Customers\' page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );
      await dashboardPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should reset all filters and get number of customers in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

      numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomers).to.be.above(0);
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await customersPage.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await customersPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await customersPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await customersPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 3 : Sort customers
  describe('Sort customers table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_customer', sortDirection: 'desc', isNumber: true,
        },
      },
      {args: {testIdentifier: 'sortBySocialTitleAsc', sortBy: 'social_title', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortBySocialTitleDesc', sortBy: 'social_title', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByFirstNameAsc', sortBy: 'firstname', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByFirstNameDesc', sortBy: 'firstname', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByLastNameCodeAsc', sortBy: 'lastname', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortLastNameDesc', sortBy: 'lastname', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByEmailAsc', sortBy: 'email', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByEmailDesc', sortBy: 'email', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortBySalesAsc', sortBy: 'total_spent', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortBySalesDesc', sortBy: 'total_spent', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByEnabledAsc', sortBy: 'active', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByEnabledDesc', sortBy: 'active', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByNewslettersAsc', sortBy: 'newsletter', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByNewslettersDesc', sortBy: 'newsletter', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByPartnerOffersAsc', sortBy: 'optin', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByPartnerOffersDesc', sortBy: 'optin', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByRegistrationAsc', sortBy: 'date_add', sortDirection: 'asc', isDate: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByRegistrationDesc', sortBy: 'date_add', sortDirection: 'desc', isDate: true,
        },
      },
      {args: {testIdentifier: 'sortByLastVisitAsc', sortBy: 'connect', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByLastVisitDesc', sortBy: 'connect', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_customer', sortDirection: 'asc', isNumber: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await customersPage.getAllRowsColumnContent(page, test.args.sortBy);

        await customersPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await customersPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isNumber) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseInt(text, 10));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseInt(text, 10));

          const expectedResult: number[] = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else if (test.args.isDate) {
          const expectedResult: string[] = await basicHelper.sortArrayDate(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
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

  // Post-condition: Delete imported customers by bulk actions
  bulkDeleteCustomersTest('email', 'test', `${baseContext}_postTest_1`);
});
