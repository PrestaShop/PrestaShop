require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_modules_advancedParameters_webservice_filterAndQuickEditWebservice';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login/index');
const DashboardPage = require('@pages/BO/dashboard/index');
const WebservicePage = require('@pages/BO/advancedParameters/webservice');
const AddWebservicePage = require('@pages/BO/advancedParameters/webservice/add');
// Importing data
const WebserviceFaker = require('@data/faker/webservice');

let browser;
let page;
let numberOfWebserviceKeys = 0;
const firstWebServiceData = new WebserviceFaker({});
const secondWebServiceData = new WebserviceFaker({});

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    webservicePage: new WebservicePage(page),
    addWebservicePage: new AddWebservicePage(page),
  };
};

describe('Filter and quick edit webservice', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login from BO and go to "Advanced parameters > Webservice" page
  loginCommon.loginBO();

  it('should go to "Advanced parameters > Webservice" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToWebservicePage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.advancedParametersLink,
      this.pageObjects.boBasePage.webserviceLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.webservicePage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.webservicePage.pageTitle);
  });

  it('should reset all filters and get number of webservices', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstReset', baseContext);
    numberOfWebserviceKeys = await this.pageObjects.webservicePage.resetAndGetNumberOfLines();
    if (numberOfWebserviceKeys !== 0) await expect(numberOfWebserviceKeys).to.be.above(0);
  });

  let tests = [
    {args: {webserviceToCreate: firstWebServiceData}},
    {args: {webserviceToCreate: secondWebServiceData}},
  ];
  tests.forEach((test, index) => {
    it('should go to add new webservice key page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAddNewWebserviceKeyPage_${index}`, baseContext);
      await this.pageObjects.webservicePage.goToAddNewWebserviceKeyPage();
      const pageTitle = await this.pageObjects.addWebservicePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addWebservicePage.pageTitleCreate);
    });

    it('should create webservice key', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createWebserviceKey_${index}`, baseContext);
      const textResult = await this.pageObjects.addWebservicePage.createEditWebservice(
        test.args.webserviceToCreate,
        false,
      );
      await expect(textResult).to.equal(this.pageObjects.addWebservicePage.successfulCreationMessage);
      const numberOfWebserviceKeysAfterCreation = await this.pageObjects.webservicePage.getNumberOfElementInGrid();
      await expect(numberOfWebserviceKeysAfterCreation).to.be.equal(numberOfWebserviceKeys + 1 + index);
    });
  });
  describe('Filter webservice', async () => {
    tests = [
      {args: {filterType: 'input', filterBy: 'key', filterValue: firstWebServiceData.key}},
      {args: {filterType: 'input', filterBy: 'description', filterValue: firstWebServiceData.keyDescription}},
      {args: {filterType: 'select', filterBy: 'active', filterValue: firstWebServiceData.status}, expected: 'check'},
    ];
    tests.forEach((test, index) => {
      it(`should filter list by ${test.args.filterBy}`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `filterBy${this.pageObjects.boBasePage.uppercaseFirstCharacter(test.args.filterBy)}`,
          baseContext,
        );
        await this.pageObjects.webservicePage.filterWebserviceTable(
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );
        const numberOfElementAfterFilter = await this.pageObjects.webservicePage.getNumberOfElementInGrid();
        for (let i = 1; i <= numberOfElementAfterFilter; i++) {
          const key = await this.pageObjects.webservicePage.getTextColumnFromTable(i, test.args.filterBy);
          if (test.expected !== undefined) {
            await expect(key).to.contains(test.expected);
          } else {
            await expect(key).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset filter and check the number of webservice keys', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter_${index}`, baseContext);
        const numberOfElement = await this.pageObjects.webservicePage.resetAndGetNumberOfLines();
        await expect(numberOfElement).to.be.equal(numberOfWebserviceKeys + 2);
      });
    });
  });

  describe('Quick Edit webservice', async () => {
    it('should filter list by key description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);
      await this.pageObjects.webservicePage.filterWebserviceTable(
        'input',
        'description',
        firstWebServiceData.keyDescription,
      );
      const key = await this.pageObjects.webservicePage.getTextColumnFromTable(1, 'description');
      await expect(key).to.contains(firstWebServiceData.keyDescription);
    });
    const statuses = [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ];
    statuses.forEach((webservice) => {
      it(`should ${webservice.args.status} the webservice`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${webservice.args.status}Webservice`, baseContext);
        const isActionPerformed = await this.pageObjects.webservicePage.updateToggleColumnValue(
          1,
          webservice.args.enable,
        );
        if (isActionPerformed) {
          const resultMessage = await this.pageObjects.webservicePage.getValidationMessage();
          await expect(resultMessage).to.contains(this.pageObjects.webservicePage.successfulUpdateStatusMessage);
        }
        const isStatusChanged = await this.pageObjects.webservicePage.getToggleColumnValue(1);
        await expect(isStatusChanged).to.be.equal(webservice.args.enable);
      });
    });
  });

  describe('Delete the created webservice keys', async () => {
    tests = [
      {args: {name: 'first'}},
      {args: {name: 'second'}},
    ];
    tests.forEach((test, index) => {
      it(`should delete the ${test.args.name} webservice key created`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteWebserviceKey_${index}`, baseContext);
        const textResult = await this.pageObjects.webservicePage.deleteWebserviceKey(1);
        await expect(textResult).to.equal(this.pageObjects.webservicePage.successfulDeleteMessage);
      });

      it('should reset filter and check the number of webservice keys', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterDelete_${index}`, baseContext);
        const numberOfElement = await this.pageObjects.webservicePage.resetAndGetNumberOfLines();
        await expect(numberOfElement).to.be.equal(numberOfWebserviceKeys - index + 1);
      });
    });
  });
});
