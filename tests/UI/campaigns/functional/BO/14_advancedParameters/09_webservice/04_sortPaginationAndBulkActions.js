require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard/index');
const webservicePage = require('@pages/BO/advancedParameters/webservice');
const addWebservicePage = require('@pages/BO/advancedParameters/webservice/add');

// Importing data
const WebserviceFaker = require('@data/faker/webservice');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_modules_advancedParameters_webservice_sortPaginationAndBulkActions';

let browserContext;
let page;

let numberOfWebserviceKeys = 0;

/*
Create 11 webservice keys
Pagination next and previous
Sort SQL queries by : key, enabled
Enable/Disable by bulk actions
Delete by bulk actions
 */
describe('Sort, pagination and bulk actionsweb service keys', async () => {
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

  it('should go to "Advanced parameters > Webservice" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToWebservicePage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.webserviceLink,
    );

    await webservicePage.closeSfToolBar(page);

    const pageTitle = await webservicePage.getPageTitle(page);
    await expect(pageTitle).to.contains(webservicePage.pageTitle);
  });

  it('should reset all filters and get number of webservices', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstReset', baseContext);

    numberOfWebserviceKeys = await webservicePage.resetAndGetNumberOfLines(page);
    if (numberOfWebserviceKeys !== 0) {
      await expect(numberOfWebserviceKeys).to.be.above(0);
    }
  });

  // 1 - Create 11 webservice keys
  const creationTests = new Array(11).fill(0, 0, 11);

  creationTests.forEach((test, index) => {
    describe(`Create webservice key n°${index + 1} in BO`, async () => {
      const webserviceData = new WebserviceFaker({keyDescription: `todelete${index}`});

      it('should go to add new webservice key page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewWebserviceKeyPage_${index}`, baseContext);

        await webservicePage.goToAddNewWebserviceKeyPage(page);

        const pageTitle = await addWebservicePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addWebservicePage.pageTitleCreate);
      });

      it('should create webservice key', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createWebserviceKey_${index}`, baseContext);

        const textResult = await addWebservicePage.createEditWebservice(page, webserviceData, true);
        await expect(textResult).to.equal(addWebservicePage.successfulCreationMessage);

        const numberOfWebserviceKeysAfterCreation = await webservicePage.getNumberOfElementInGrid(page);
        await expect(numberOfWebserviceKeysAfterCreation).to.be.equal(numberOfWebserviceKeys + 1 + index);
      });
    });
  });

  // 2 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await webservicePage.selectPaginationLimit(page, '10');
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

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await webservicePage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 3 - Sort webservice keys table
  describe('Sort webservice keys table', async () => {
    const sortTests = [
      {args: {testIdentifier: 'sortByKeyDesc', sortBy: 'key', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByEnabledAsc', sortBy: 'active', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByEnabledDesc', sortBy: 'active', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByKeyAsc', sortBy: 'key', sortDirection: 'asc'}},
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await webservicePage.getAllRowsColumnContent(page, test.args.sortBy);
        await webservicePage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await webservicePage.getAllRowsColumnContent(page, test.args.sortBy);
        const expectedResult = await webservicePage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 4 - Enable/Disable webservice keys by bulk actions
  describe('Enable/Disable the created webservice keys by bulk actions', async () => {
    it('should filter list by key description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterSort', baseContext);

      await webservicePage.filterWebserviceTable(page, 'input', 'description', 'todelete');

      const key = await webservicePage.getTextColumnFromTable(page, 1, 'description');
      await expect(key).to.contains('todelete');
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}WebserviceKey`, baseContext);

        await webservicePage.bulkSetStatus(page, test.args.enabledValue);

        // Validation message not displayed, skipping it https://github.com/PrestaShop/PrestaShop/issues/21745
        // await expect(textResult).to.be.equal(webservicePage.successfulUpdateStatusMessage);

        const numberOfWebserviceKeys = await webservicePage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfWebserviceKeys; i++) {
          const webserviceStatus = await webservicePage.getStatus(page, i);
          await expect(webserviceStatus).to.equal(test.args.enabledValue);
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
      await expect(key).to.contains('todelete');
    });

    it('should delete webservice keys created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteWebserviceKey', baseContext);

      const textResult = await webservicePage.deleteWithBulkActions(page);
      await expect(textResult).to.equal(webservicePage.successfulMultiDeleteMessage);
    });

    it('should reset filter and check the number of webservice keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfElement = await webservicePage.resetAndGetNumberOfLines(page);
      await expect(numberOfElement).to.be.equal(numberOfWebserviceKeys);
    });
  });
});
