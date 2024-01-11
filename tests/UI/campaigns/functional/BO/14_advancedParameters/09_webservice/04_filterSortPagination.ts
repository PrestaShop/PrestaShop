// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import webservicePage from '@pages/BO/advancedParameters/webservice';
import addWebservicePage from '@pages/BO/advancedParameters/webservice/add';

// Import data
import WebserviceData from '@data/faker/webservice';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_webservice_filterSortPagination';

/*
Create 11 webservice keys
Filter
Pagination next and previous
Sort SQL queries by : key, enabled
Delete by bulk actions
 */
describe('BO - Advanced Parameters - Webservice : Filter, Sort and pagination web service keys', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfWebserviceKeys: number = 0;

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

  it('should go to \'Advanced parameters > Webservice\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToWebservicePage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.webserviceLink,
    );
    await webservicePage.closeSfToolBar(page);

    const pageTitle = await webservicePage.getPageTitle(page);
    expect(pageTitle).to.contains(webservicePage.pageTitle);
  });

  it('should reset all filters and get number of webservices', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstReset', baseContext);

    numberOfWebserviceKeys = await webservicePage.resetAndGetNumberOfLines(page);
    if (numberOfWebserviceKeys !== 0) {
      expect(numberOfWebserviceKeys).to.be.above(0);
    }
  });

  // 1 - Create 11 webservice keys
  describe('Create 11 webservice keys in BO', async () => {
    const creationTests: number[] = new Array(11).fill(0, 0, 11);
    creationTests.forEach((test: number, index: number) => {
      const webserviceData: WebserviceData = new WebserviceData(
        {
          key: `JYGPBFGYHXAP3J6BV42C27ABLW7XJC1${index}`,
          keyDescription: `todelete${index}`,
        });

      it('should go to add new webservice key page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewWebserviceKeyPage_${index}`, baseContext);

        await webservicePage.goToAddNewWebserviceKeyPage(page);

        const pageTitle = await addWebservicePage.getPageTitle(page);
        expect(pageTitle).to.contains(addWebservicePage.pageTitleCreate);
      });

      it(`should create webservice key n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createWebserviceKey_${index}`, baseContext);

        const textResult = await addWebservicePage.createEditWebservice(page, webserviceData, true);
        expect(textResult).to.equal(addWebservicePage.successfulCreationMessage);

        const numberOfWebserviceKeysAfterCreation = await webservicePage.getNumberOfElementInGrid(page);
        expect(numberOfWebserviceKeysAfterCreation).to.be.equal(numberOfWebserviceKeys + 1 + index);
      });
    });
  });

  // 2 - Filter
  describe('Filter webservice table', async () => {
    const testsFilter = [
      {
        args: {
          identifier: 'filterByKey',
          filterType: 'input',
          filterBy: 'key',
          filterValue: 'JYGPBFGYHXAP3J6BV42C27ABLW7XJC16',
        },
      },
      {
        args: {
          identifier: 'filterByDescription',
          filterType: 'input',
          filterBy: 'description',
          filterValue: 'todelete2',
        },
      },
      {
        args: {
          identifier: 'filterByStatus',
          filterType: 'select',
          filterBy: 'active',
          filterValue: '1',
        },
      },
    ];

    testsFilter.forEach((
      test: {args: {identifier: string, filterType: string, filterBy: string, filterValue: string, }},
      index: number,
    ) => {
      it(`should filter list by ${test.args.filterBy}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.identifier, baseContext);

        await webservicePage.filterWebserviceTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfElementAfterFilter = await webservicePage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const wenServiceStatus = await webservicePage.getStatus(page, i);
            expect(wenServiceStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await webservicePage.getTextColumnFromTable(page, i, test.args.filterBy);
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset filter and check the number of webservice keys', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter_${index}`, baseContext);

        const numberOfElement = await webservicePage.resetAndGetNumberOfLines(page);
        expect(numberOfElement).to.be.equal(numberOfWebserviceKeys + 11);
      });
    });
  });

  // 3 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await webservicePage.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await webservicePage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await webservicePage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await webservicePage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 4 - Sort webservice keys table
  describe('Sort webservice keys table', async () => {
    const sortTests = [
      {args: {testIdentifier: 'sortByKeyDesc', sortBy: 'key', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByEnabledAsc', sortBy: 'active', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByEnabledDesc', sortBy: 'active', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByKeyAsc', sortBy: 'key', sortDirection: 'asc'}},
    ];

    sortTests.forEach((test: {args: {testIdentifier: string, sortBy: string, sortDirection: string}}) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await webservicePage.getAllRowsColumnContent(page, test.args.sortBy);
        await webservicePage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await webservicePage.getAllRowsColumnContent(page, test.args.sortBy);
        const expectedResult = await basicHelper.sortArray(nonSortedTable);

        if (test.args.sortDirection === 'asc') {
          expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 5 - Delete webservice keys by bulk actions
  describe('Delete the created webservice keys by bulk actions', async () => {
    it('should filter list by key description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterEnableDisable', baseContext);

      await webservicePage.filterWebserviceTable(page, 'input', 'description', 'todelete');

      const key = await webservicePage.getTextColumnFromTable(page, 1, 'description');
      expect(key).to.contains('todelete');
    });

    it('should delete webservice keys created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteWebserviceKey', baseContext);

      const textResult = await webservicePage.deleteWithBulkActions(page);
      expect(textResult).to.equal(webservicePage.successfulMultiDeleteMessage);
    });

    it('should reset filter and check the number of webservice keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfElement = await webservicePage.resetAndGetNumberOfLines(page);
      expect(numberOfElement).to.be.equal(numberOfWebserviceKeys);
    });
  });
});
