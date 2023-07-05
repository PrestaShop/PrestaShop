// Import utils
import basicHelper from '@utils/basicHelper';
import date from '@utils/date';
import helper from '@utils/helpers';
import {expect} from 'chai';
import testContext from '@utils/testContext';

// Import common
import loginCommon from '@commonTests/BO/loginBO';

// Import data
import Employees from '@data/demo/employees';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import logsPage from '@pages/BO/advancedParameters/logs';

import type {BrowserContext, Page} from 'playwright';

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
  const today = date.getDateFormat('mm/dd/yyyy');

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

  it('should go to \'Advanced Parameters > Logs\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLogsPageToEraseLogs', baseContext);

    await dashboardPage.goToSubMenu(page, dashboardPage.advancedParametersLink, dashboardPage.logsLink);
    await logsPage.closeSfToolBar(page);

    const pageTitle = await logsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(logsPage.pageTitle);
  });

  it('should erase all logs', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'eraseLogs', baseContext);

    const textResult = await logsPage.eraseAllLogs(page);
    await expect(textResult).to.equal(logsPage.successfulUpdateMessage);

    numberOfLogs = await logsPage.getNumberOfElementInGrid(page);
    await expect(numberOfLogs).to.be.equal(0);
  });

  // Login and logout 11 times to have 11 logs
  describe('Logout then login 11 times to have 11 logs', async () => {
    const tests: number[] = new Array(11).fill(0, 0, 11);

    tests.forEach((test: number, index: number) => {
      it(`should logout from BO n°${index + 1}`, async function () {
        await loginCommon.logoutBO(this, page);
      });

      it(`should login in BO n°${index + 1}`, async function () {
        await loginCommon.loginBO(this, page);
      });
    });

    it('should go to \'Advanced parameters > Logs\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLogsPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.advancedParametersLink, dashboardPage.logsLink);

      const pageTitle = await logsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(logsPage.pageTitle);
    });

    it('should check the number of logs', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLogsNumber', baseContext);

      const numberOfElements = await logsPage.getNumberOfElementInGrid(page);
      await expect(numberOfElements).to.be.equal(11);
    });
  });

  // 1 - Pagination
  describe('Pagination next and previous', async () => {
    it('should go to \'Advanced parameters > Logs\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLogsPageToFilter', baseContext);

      await logsPage.reloadPage(page);

      const pageTitle = await logsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(logsPage.pageTitle);

      const numberOfElements = await logsPage.getNumberOfElementInGrid(page);
      await expect(numberOfElements).to.be.equal(numberOfLogs + 11);
    });

    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await logsPage.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await logsPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await logsPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await logsPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.contains('(page 1 / 1)');
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
            filterValue: '50',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByEmployee',
            filterType: 'input',
            filterBy: 'employee',
            filterValue: Employees.DefaultEmployee.lastName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterBySeverity',
            filterType: 'input',
            filterBy: 'severity',
            filterValue: 'Error',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByMessage',
            filterType: 'input',
            filterBy: 'message',
            filterValue: 'Back office',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByObjectType',
            filterType: 'input',
            filterBy: 'object_type',
            filterValue: 'Cart',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByObjectID',
            filterType: 'input',
            filterBy: 'object_id',
            filterValue: '2',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByErrorCode',
            filterType: 'input',
            filterBy: 'error_code',
            filterValue: '1',
          },
      },
    ].forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await logsPage.filterLogs(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfLogsAfterFilter = await logsPage.getNumberOfElementInGrid(page);

        await expect(numberOfLogsAfterFilter).to.be.at.most(numberOfLogs + 11);

        for (let i = 1; i <= numberOfLogsAfterFilter; i++) {
          const textColumn = await logsPage.getTextColumn(page, i, test.args.filterBy);

          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfLogsAfterReset = await logsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfLogsAfterReset).to.equal(numberOfLogs + 11);
      });
    });

    it('should filter logs by date sent \'From\' and \'To\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDateSent', baseContext);

      await logsPage.filterLogsByDate(page, today, today);

      const numberOfEmailsAfterFilter = await logsPage.getNumberOfElementInGrid(page);
      await expect(numberOfEmailsAfterFilter).to.be.at.most(numberOfLogs + 11);

      for (let row: number = 1; row <= numberOfEmailsAfterFilter; row++) {
        const textColumn = await logsPage.getTextColumn(page, row, 'date_add');
        await expect(textColumn).to.contains(today);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterFilterByDate', baseContext);

      const numberOfLogsAfterReset = await logsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLogsAfterReset).to.equal(numberOfLogs + 11);
    });
  });

  // 3 : Sort logs
  describe('Sort logs table', async () => {
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

        const nonSortedTable = await logsPage.getAllRowsColumnContent(page, test.args.sortBy);
        await logsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await logsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });
});
