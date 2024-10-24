// Import utils
import testContext from '@utils/testContext';

// Import pages
import webservicePage from '@pages/BO/advancedParameters/webservice';
import addWebservicePage from '@pages/BO/advancedParameters/webservice/add';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerWebservice,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_webservice_bulkActions';

describe('BO - Advanced Parameters - Webservice : Bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfWebserviceKeys: number = 0;

  const firstWebServiceData: FakerWebservice = new FakerWebservice({keyDescription: 'todelete'});
  const secondWebServiceData: FakerWebservice = new FakerWebservice();

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

  it('should go to \'Advanced parameters > Webservice\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToWebservicePage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.webserviceLink,
    );
    await webservicePage.closeSfToolBar(page);

    const pageTitle = await webservicePage.getPageTitle(page);
    expect(pageTitle).to.contains(webservicePage.pageTitle);
  });

  it('should reset all filters and get number of webservices', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstReset', baseContext);

    numberOfWebserviceKeys = await webservicePage.resetAndGetNumberOfLines(page);
    if (numberOfWebserviceKeys !== 0) expect(numberOfWebserviceKeys).to.be.above(0);
  });

  const tests = [
    {args: {webserviceToCreate: firstWebServiceData}},
    {args: {webserviceToCreate: secondWebServiceData}},
  ];

  tests.forEach((test: { args: { webserviceToCreate: FakerWebservice } }, index: number) => {
    it('should go to add new webservice key page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAddNewWebserviceKeyPage_${index}`, baseContext);

      await webservicePage.goToAddNewWebserviceKeyPage(page);

      const pageTitle = await addWebservicePage.getPageTitle(page);
      expect(pageTitle).to.contains(addWebservicePage.pageTitleCreate);
    });

    it('should create webservice key', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createWebserviceKey_${index}`, baseContext);

      const textResult = await addWebservicePage.createEditWebservice(
        page,
        test.args.webserviceToCreate,
        false,
      );
      expect(textResult).to.equal(addWebservicePage.successfulCreationMessage);

      const numberOfWebserviceKeysAfterCreation = await webservicePage.getNumberOfElementInGrid(page);
      expect(numberOfWebserviceKeysAfterCreation).to.be.equal(numberOfWebserviceKeys + 1 + index);
    });
  });

  describe('Enable/Disable the created webservice keys by bulk actions', async () => {
    it('should filter list by key description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterSort', baseContext);

      await webservicePage.filterWebserviceTable(page, 'input', 'description', 'todelete');

      const key = await webservicePage.getTextColumnFromTable(page, 1, 'description');
      expect(key).to.contains('todelete');
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test: { args: { action: string, enabledValue: boolean } }) => {
      it(`should ${test.args.action} with bulk actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}WebserviceKey`, baseContext);

        const textResult = await webservicePage.bulkSetStatus(page, test.args.enabledValue);
        expect(textResult).to.be.equal(webservicePage.successfulUpdateStatusMessage);

        const numberOfWebserviceKeys = await webservicePage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfWebserviceKeys; i++) {
          const webserviceStatus = await webservicePage.getStatus(page, i);
          expect(webserviceStatus).to.equal(test.args.enabledValue);
        }
      });
    });
  });

  describe('Delete the created webservice keys by bulk actions', async () => {
    it('should reset filter and check the number of webservice keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterBeforeDelete', baseContext);

      const numberOfElement = await webservicePage.resetAndGetNumberOfLines(page);
      expect(numberOfElement).to.be.equal(numberOfWebserviceKeys + 2);
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
