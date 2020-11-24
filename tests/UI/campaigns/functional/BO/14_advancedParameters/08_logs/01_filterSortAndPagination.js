require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard/index');
const logsPage = require('@pages/BO/advancedParameters/logs');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_modules_advancedParameters_logs_filterSortAndPagination';

let browserContext;
let page;

let numberOfLogs = 0;

describe('Filter, sort and pagination logs', async () => {
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

  it('should go to "Advanced parameters > Logs" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLogsPage', baseContext);

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

  // 1 - Filter logs
  describe('Filter Logs', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_log',
            filterValue: 300,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByEmployee',
            filterType: 'input',
            filterBy: 'employee',
            filterValue: 'Nesrine Abdmouleh',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterBySeverity',
            filterType: 'select',
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
            filterValue: 'ShopGroup',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByObjectID',
            filterType: 'input',
            filterBy: 'object_id',
            filterValue: 2,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByErrorCode',
            filterType: 'input',
            filterBy: 'error_code',
            filterValue: 1,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await logsPage.filterLogs(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfLogsAfterFilter = await logsPage.getNumberOfElementInGrid(page);

        await expect(numberOfLogsAfterFilter).to.be.at.most(numberOfLogs);

        for (let i = 1; i <= numberOfLogsAfterFilter; i++) {
          const textColumn = await logsPage.getTextColumn(page, i, test.args.filterBy);

          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfLogsAfterReset = await logsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfLogsAfterReset).to.equal(numberOfLogs);
      });
    });
  });
});
