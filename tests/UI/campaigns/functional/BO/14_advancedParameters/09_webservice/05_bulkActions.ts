import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boWebservicesPage,
  boWebservicesCreatePage,
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
    await boWebservicesPage.closeSfToolBar(page);

    const pageTitle = await boWebservicesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boWebservicesPage.pageTitle);
  });

  it('should reset all filters and get number of webservices', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstReset', baseContext);

    numberOfWebserviceKeys = await boWebservicesPage.resetAndGetNumberOfLines(page);
    if (numberOfWebserviceKeys !== 0) expect(numberOfWebserviceKeys).to.be.above(0);
  });

  const tests = [
    {args: {webserviceToCreate: firstWebServiceData}},
    {args: {webserviceToCreate: secondWebServiceData}},
  ];

  tests.forEach((test: { args: { webserviceToCreate: FakerWebservice } }, index: number) => {
    it('should go to add new webservice key page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAddNewWebserviceKeyPage_${index}`, baseContext);

      await boWebservicesPage.goToAddNewWebserviceKeyPage(page);

      const pageTitle = await boWebservicesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boWebservicesCreatePage.pageTitleCreate);
    });

    it('should create webservice key', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createWebserviceKey_${index}`, baseContext);

      const textResult = await boWebservicesCreatePage.createEditWebservice(
        page,
        test.args.webserviceToCreate,
        false,
      );
      expect(textResult).to.equal(boWebservicesCreatePage.successfulCreationMessage);

      const numberOfWebserviceKeysAfterCreation = await boWebservicesPage.getNumberOfElementInGrid(page);
      expect(numberOfWebserviceKeysAfterCreation).to.be.equal(numberOfWebserviceKeys + 1 + index);
    });
  });

  describe('Enable/Disable the created webservice keys by bulk actions', async () => {
    it('should filter list by key description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterSort', baseContext);

      await boWebservicesPage.filterWebserviceTable(page, 'input', 'description', 'todelete');

      const key = await boWebservicesPage.getTextColumnFromTable(page, 1, 'description');
      expect(key).to.contains('todelete');
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test: { args: { action: string, enabledValue: boolean } }) => {
      it(`should ${test.args.action} with bulk actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}WebserviceKey`, baseContext);

        const textResult = await boWebservicesPage.bulkSetStatus(page, test.args.enabledValue);
        expect(textResult).to.be.equal(boWebservicesPage.successfulUpdateStatusMessage);

        const numberOfWebserviceKeys = await boWebservicesPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfWebserviceKeys; i++) {
          const webserviceStatus = await boWebservicesPage.getStatus(page, i);
          expect(webserviceStatus).to.equal(test.args.enabledValue);
        }
      });
    });
  });

  describe('Delete the created webservice keys by bulk actions', async () => {
    it('should reset filter and check the number of webservice keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterBeforeDelete', baseContext);

      const numberOfElement = await boWebservicesPage.resetAndGetNumberOfLines(page);
      expect(numberOfElement).to.be.equal(numberOfWebserviceKeys + 2);
    });

    it('should delete webservice keys created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteWebserviceKey', baseContext);

      const textResult = await boWebservicesPage.deleteWithBulkActions(page);
      expect(textResult).to.equal(boWebservicesPage.successfulMultiDeleteMessage);
    });

    it('should reset filter and check the number of webservice keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfElement = await boWebservicesPage.resetAndGetNumberOfLines(page);
      expect(numberOfElement).to.be.equal(numberOfWebserviceKeys);
    });
  });
});
