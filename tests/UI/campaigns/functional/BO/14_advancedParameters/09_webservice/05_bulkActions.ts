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

  const firstWebServiceData: FakerWebservice = new FakerWebservice({keyDescription: 'todelete10'});
  const secondWebServiceData: FakerWebservice = new FakerWebservice({keyDescription: 'todelete20'});

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
    expect(numberOfWebserviceKeys).to.be.greaterThanOrEqual(0);
  });

  [
    firstWebServiceData,
    secondWebServiceData,
  ].forEach((data: FakerWebservice, index: number) => {
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
        data,
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

    [
      {action: 'disable', enabledValue: false},
      {action: 'enable', enabledValue: true},
    ].forEach((arg: { action: string, enabledValue: boolean}) => {
      it(`should ${arg.action} with bulk actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${arg.action}WebserviceKey`, baseContext);

        const textResult = await boWebservicesPage.bulkSetStatus(page, arg.enabledValue);
        expect(textResult).to.be.equal(boWebservicesPage.successfulUpdateStatusMessage);

        const numberOfWebserviceKeysAfterBulk = await boWebservicesPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfWebserviceKeysAfterBulk; i++) {
          const webserviceStatus = await boWebservicesPage.getStatus(page, i);
          expect(webserviceStatus).to.equal(arg.enabledValue);
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

    it('should filter list by key description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterBeforeBulkDelete', baseContext);

      await boWebservicesPage.filterWebserviceTable(page, 'input', 'description', 'todelete');

      const key = await boWebservicesPage.getTextColumnFromTable(page, 1, 'description');
      expect(key).to.contains('todelete');
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
