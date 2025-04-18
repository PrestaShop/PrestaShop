import {expect} from 'chai';
import testContext from '@utils/testContext';

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

const baseContext: string = 'functional_BO_advancedParameters_webservice_CRUDWebservice';

// Create, Read, Update and Delete webservice key in BO
describe('BO - Advanced Parameters - Webservice : Create, Read, Update and Delete webservice key in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfWebserviceKeys: number = 0;

  const createWebserviceData: FakerWebservice = new FakerWebservice({});
  const editWebserviceData: FakerWebservice = new FakerWebservice({});

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

  it('should go to \'Advanced Parameters > Webservice\' page', async function () {
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

  // 1 : Create webservice key
  describe('Create webservice key', async () => {
    it('should go to add new webservice key page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewWebserviceKeyPage', baseContext);

      await boWebservicesPage.goToAddNewWebserviceKeyPage(page);

      const pageTitle = await boWebservicesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boWebservicesCreatePage.pageTitleCreate);
    });

    it('should create webservice key and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createWebserviceKey', baseContext);

      const textResult = await boWebservicesCreatePage.createEditWebservice(page, createWebserviceData);
      expect(textResult).to.equal(boWebservicesCreatePage.successfulCreationMessage);

      const numberOfWebserviceKeysAfterCreation = await boWebservicesPage.getNumberOfElementInGrid(page);
      expect(numberOfWebserviceKeysAfterCreation).to.be.equal(numberOfWebserviceKeys + 1);
    });
  });

  // 2 : Update webservice key
  describe('Update webservice key', async () => {
    it('should filter list by key description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterUpdate', baseContext);

      await boWebservicesPage.filterWebserviceTable(
        page,
        'input',
        'description',
        createWebserviceData.keyDescription,
      );

      const key = await boWebservicesPage.getTextColumnFromTable(page, 1, 'description');
      expect(key).to.contains(createWebserviceData.keyDescription);
    });

    it('should go to edit webservice page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditWebservicePage', baseContext);

      await boWebservicesPage.goToEditWebservicePage(page, 1);

      const pageTitle = await boWebservicesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boWebservicesCreatePage.pageTitleEdit);
    });

    it('should update the webservice key and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateWebserviceKey', baseContext);

      const textResult = await boWebservicesCreatePage.createEditWebservice(page, editWebserviceData);
      expect(textResult).to.equal(boWebservicesCreatePage.successfulUpdateMessage);
    });

    it('should reset filter and check the number of webservice keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterUpdate', baseContext);

      const numberOfWebserviceKeyAfterDelete = await boWebservicesPage.resetAndGetNumberOfLines(page);
      expect(numberOfWebserviceKeyAfterDelete).to.be.equal(numberOfWebserviceKeys + 1);
    });
  });

  // 3 : Delete webservice key
  describe('Delete webservice key', async () => {
    it('should filter list by key description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterBeforeDelete', baseContext);

      await boWebservicesPage.filterWebserviceTable(
        page,
        'input',
        'description',
        editWebserviceData.keyDescription,
      );

      const key = await boWebservicesPage.getTextColumnFromTable(page, 1, 'description');
      expect(key).to.contains(editWebserviceData.keyDescription);
    });

    it('should delete webservice key', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteWebserviceKey', baseContext);

      const textResult = await boWebservicesPage.deleteWebserviceKey(page, 1);
      expect(textResult).to.equal(boWebservicesPage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of webservice keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfWebserviceKeyAfterDelete = await boWebservicesPage.resetAndGetNumberOfLines(page);
      expect(numberOfWebserviceKeyAfterDelete).to.be.equal(numberOfWebserviceKeys);
    });
  });
});
