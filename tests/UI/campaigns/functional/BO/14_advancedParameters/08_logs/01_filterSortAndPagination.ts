// Import utils
import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boLoginPage,
  boLogsPage,
  type BrowserContext,
  dataEmployees,
  type Page,
  utilsCore,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_logs_filterSortAndPagination';

/*
Erase all logs
Login and logout 6 times
Create 6 orders
Pagination next and previous
Filter logs table by : Id, Employee, Severity, Message, Object type, Object ID, Error code, Date
Sort logs table by : Id, Employee, Severity, Message, Object type, Object ID, Error code, Date
 */

describe('BO - Advanced Parameters - Logs : Filter, sort and pagination logs table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfLogs: number = 0;
  const today = utilsDate.getDateFormat('mm/dd/yyyy');

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Advanced Parameters > Logs\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLogsPageToEraseLogs', baseContext);

    await boDashboardPage.goToSubMenu(page, boDashboardPage.advancedParametersLink, boDashboardPage.logsLink);
    await boLogsPage.closeSfToolBar(page);

    const pageTitle = await boLogsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLogsPage.pageTitle);
  });

  it('should erase all logs', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'eraseLogs', baseContext);

    const textResult = await boLogsPage.eraseAllLogs(page);
    expect(textResult).to.equal(boLogsPage.successfulUpdateMessage);

    const numberOfElements = await boLogsPage.getNumberOfElementInGrid(page);
    expect(numberOfElements).to.be.equal(0);
  });

  it('should set the log minimum severity to Debug or the login logs will not be created', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setDebugSeverityForDatabase', baseContext);

    const errorMessage = await boLogsPage.setMinimumSeverityLevelForDatabase(page, 'Debug');
    expect(errorMessage).to.eq(boLogsPage.successfulUpdateMessage);
  });

  // Login and logout 11 times to have 11 logs
  describe('Logout then login 11 times to have 11 logs', async () => {
    const tests: number[] = new Array(11).fill(0, 0, 11);

    tests.forEach((test: number, index: number) => {
      it(`should logout from BO n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

        await boDashboardPage.logoutBO(page);

        const pageTitle = await boLoginPage.getPageTitle(page);
        expect(pageTitle).to.contains(boLoginPage.pageTitle);
      });

      it(`should login in BO n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);
        await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });
    });

    it('should go to \'Advanced parameters > Logs\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLogsPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.advancedParametersLink, boDashboardPage.logsLink);

      const pageTitle = await boLogsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLogsPage.pageTitle);
    });

    it('should check the number of logs', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLogsNumber', baseContext);

      const numberOfElements = await boLogsPage.getNumberOfElementInGrid(page);
      expect(numberOfElements).to.be.greaterThanOrEqual(11);
    });
  });

  // 1 - Pagination
  describe('Pagination next and previous', async () => {
    it('should go to \'Advanced parameters > Logs\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLogsPageToFilter', baseContext);

      await boLogsPage.reloadPage(page);

      const pageTitle = await boLogsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLogsPage.pageTitle);

      numberOfLogs = await boLogsPage.getNumberOfElementInGrid(page);
      expect(numberOfLogs).to.be.greaterThanOrEqual(11);
    });

    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const pagesNb = Math.ceil(numberOfLogs / 10);
      const paginationNumber = await boLogsPage.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contains(`(page 1 / ${pagesNb})`);
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const pagesNb = Math.ceil(numberOfLogs / 10);
      const paginationNumber = await boLogsPage.paginationNext(page);
      expect(paginationNumber).to.contains(`(page 2 / ${pagesNb})`);
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const pagesNb = Math.ceil(numberOfLogs / 10);
      const paginationNumber = await boLogsPage.paginationPrevious(page);
      expect(paginationNumber).to.contains(`(page 1 / ${pagesNb})`);
    });

    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const pagesNb = Math.ceil(numberOfLogs / 20);
      const paginationNumber = await boLogsPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.contains(`(page 1 / ${pagesNb})`);
    });
  });

  // 2 - Filter logs
  describe('Filter Logs table', async () => {
    [
      {
        args:
          {
            testIdentifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_log',
            filterValue: 'numberOfLogs',
            expectedCount: 1,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByEmployee',
            filterType: 'input',
            filterBy: 'employee',
            filterValue: dataEmployees.defaultEmployee.lastName,
            expectedCount: null,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterBySeverityError',
            filterType: 'input',
            filterBy: 'severity',
            filterValue: 'Error',
            expectedCount: 0,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterBySeverityInformativeOnly',
            filterType: 'input',
            filterBy: 'severity',
            filterValue: 'Informative Only',
            expectedCount: 0,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterBySeverityDebug',
            filterType: 'input',
            filterBy: 'severity',
            filterValue: 'Debug',
            expectedCount: 'numberOfLogs',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByMessage',
            filterType: 'input',
            filterBy: 'message',
            filterValue: 'Remember me',
            expectedCount: 11,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByObjectType',
            filterType: 'input',
            filterBy: 'object_type',
            filterValue: 'Cart',
            expectedCount: 0,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByObjectIDTwo',
            filterType: 'input',
            filterBy: 'object_id',
            filterValue: '2',
            expectedCount: 0,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByObjectIDZero',
            filterType: 'input',
            filterBy: 'object_id',
            filterValue: '0',
            expectedCount: 'numberOfLogs',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByErrorCodeOne',
            filterType: 'input',
            filterBy: 'error_code',
            filterValue: '1',
            expectedCount: 0,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByErrorCodeZero',
            filterType: 'input',
            filterBy: 'error_code',
            filterValue: '0',
            expectedCount: 'numberOfLogs',
          },
      },
    ].forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        const testValue = test.args.filterValue === 'numberOfLogs' ? String(numberOfLogs) : test.args.filterValue;
        await boLogsPage.filterLogs(
          page,
          test.args.filterType,
          test.args.filterBy,
          testValue,
        );

        const expectedCount = test.args.expectedCount === 'numberOfLogs' ? numberOfLogs : test.args.expectedCount;
        const numberOfLogsAfterFilter = await boLogsPage.getNumberOfElementInGrid(page);

        // If expected count is null, we don't expect any particular value
        if (expectedCount !== null) {
          expect(numberOfLogsAfterFilter).to.be.eq(expectedCount);
        }

        for (let i = 1; i <= await boLogsPage.getNumberOfRowsInGrid(page); i++) {
          const textColumn = await boLogsPage.getTextColumn(page, i, test.args.filterBy);

          // Lower case mostly needed because of Informative Only ("Only" in filter values, but "only" in column values)
          expect(textColumn.toLowerCase()).to.contains(testValue.toLowerCase());
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfLogsAfterReset = await boLogsPage.resetAndGetNumberOfLines(page);
        expect(numberOfLogsAfterReset).to.greaterThanOrEqual(11);
      });
    });

    it('should filter logs by date sent \'From\' and \'To\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDateSent', baseContext);

      await boLogsPage.filterLogsByDate(page, today, today);

      const numberOfLogsAfterFilter = await boLogsPage.getNumberOfElementInGrid(page);
      expect(numberOfLogsAfterFilter).to.be.at.greaterThanOrEqual(11);

      for (let row: number = 1; row <= await boLogsPage.getNumberOfRowsInGrid(page); row++) {
        const textColumn = await boLogsPage.getTextColumn(page, row, 'date_add');
        expect(textColumn).to.contains(today);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterFilterByDate', baseContext);

      const numberOfLogsAfterReset = await boLogsPage.resetAndGetNumberOfLines(page);
      expect(numberOfLogsAfterReset).to.greaterThanOrEqual(11);
    });
  });

  // 3 : Sort logs
  describe('Sort logs table', async () => {
    // Force a lot of elements by page, so we only have one page and can compare all the elements sorted or not
    it('should change the items number to 100 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo100', baseContext);

      await boLogsPage.resetFilter(page);
      const paginationNumber = await boLogsPage.selectPaginationLimit(page, 100);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });

    [
      {
        args:
          {
            testIdentifier: 'sortByIdDesc', sortBy: 'id_log', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByEmployeeAsc', sortBy: 'employee', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByEmployeeDesc', sortBy: 'employee', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortBySeverityDesc', sortBy: 'severity', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortBySeverityAsc', sortBy: 'severity', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByMessageDesc', sortBy: 'message', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByMessageAsc', sortBy: 'message', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByObjectTypeDesc', sortBy: 'object_type', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByObjectTypeAsc', sortBy: 'object_type', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByObjectIDDesc', sortBy: 'object_id', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByObjectIDAsc', sortBy: 'object_id', sortDirection: 'asc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByErrorCodeDesc', sortBy: 'error_code', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByErrorCodeDAsc', sortBy: 'error_code', sortDirection: 'asc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByDateAddDesc', sortBy: 'date_add', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByDateAddAsc', sortBy: 'date_add', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByIdAsc', sortBy: 'id_log', sortDirection: 'asc', isFloat: true,
          },
      },
    ].forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await boLogsPage.getAllRowsColumnContent(page, test.args.sortBy);
        await boLogsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boLogsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await utilsCore.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });

    it('should reset the log minimum severity to initial value Informative only', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setInfoSeverityForDatabase', baseContext);

      const errorMessage = await boLogsPage.setMinimumSeverityLevelForDatabase(page, 'Informative only');
      expect(errorMessage).to.eq(boLogsPage.successfulUpdateMessage);
    });
  });
});
