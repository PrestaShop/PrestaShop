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

const baseContext = 'functional_BO_modules_advancedParameters_webservice_sortAndPagination';

let browserContext;
let page;

let numberOfWebserviceKeys = 0;

/*
Create 11 webservice keys
Pagination next and previous
Sort SQL queries by : key, enabled
Delete by bulk actions
 */
describe('Sort and pagination web service keys', async () => {
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
      const webserviceData = new WebserviceFaker({name: `todelete${index}`});

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
});
