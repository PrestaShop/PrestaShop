// Import utils
import basicHelper from '@utils/basicHelper';
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import importFileTest from '@commonTests/BO/advancedParameters/importFile';
import {bulkDeleteAddressesTest} from '@commonTests/BO/customers/address';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import addressesPage from '@pages/BO/customers/addresses';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import ImportAddresses from '@data/import/addresses';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customers_addresses_paginationAndSortAddresses';

/*
Pre-condition:
- Import list of addresses
Scenario:
- Paginate between pages
- Sort addresses by id, firstname, lastname, address, post code, city and country
Post-condition:
- Delete addresses with bulk actions
 */
describe('BO - Customers - Addresses : Pagination and sort addresses table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAddresses: number = 0;

  // Variable used to create customers csv file
  const fileName: string = 'addresses.csv';

  // Pre-condition: Import list of categories
  importFileTest(fileName, ImportAddresses.entity, `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    // Create csv file with all customers data
    await files.createCSVFile('.', fileName, ImportAddresses);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    // Delete created csv file
    await files.deleteFile(fileName);
  });

  // 1 : Go to addresses page
  describe('Go to \'Customers > Addresses\' page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Customers > Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.addressesLink,
      );
      await dashboardPage.closeSfToolBar(page);

      const pageTitle = await addressesPage.getPageTitle(page);
      expect(pageTitle).to.contains(addressesPage.pageTitle);
    });

    it('should reset all filters and get number of addresses in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

      numberOfAddresses = await addressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddresses).to.be.above(0);
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10', baseContext);

      const paginationNumber = await addressesPage.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await addressesPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await addressesPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await addressesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 3 : Sort addresses
  describe('Sort addresses table', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_address', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortByFirstNameAsc', sortBy: 'firstname', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByFirstNameDesc', sortBy: 'firstname', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByLastNameAsc', sortBy: 'lastname', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByLastNameDesc', sortBy: 'lastname', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByAddress1Asc', sortBy: 'address1', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByAddress1Desc', sortBy: 'address1', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByPostCodeAsc', sortBy: 'postcode', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByPostCodeDesc', sortBy: 'postcode', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByCityAsc', sortBy: 'city', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByCityDesc', sortBy: 'city', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByCountryAsc', sortBy: 'country_name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByCountryDesc', sortBy: 'country_name', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_address', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    tests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await addressesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await addressesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await addressesPage.getAllRowsColumnContent(page, test.args.sortBy);

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

  // Post-condition: Delete imported addresses with bulk actions
  bulkDeleteAddressesTest('lastname', 'test', `${baseContext}_postTest_1`);
});
