// Import utils
import {expect} from 'chai';
import testContext from '@utils/testContext';

// Import pages
import webservicePage from '@pages/BO/advancedParameters/webservice';
import addWebservicePage from '@pages/BO/advancedParameters/webservice/add';

import {
  boDashboardPage,
  boLoginPage,
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
    await webservicePage.closeSfToolBar(page);

    const pageTitle = await webservicePage.getPageTitle(page);
    expect(pageTitle).to.contains(webservicePage.pageTitle);
  });

  it('should reset all filters and get number of webservices', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstReset', baseContext);

    numberOfWebserviceKeys = await webservicePage.resetAndGetNumberOfLines(page);
    if (numberOfWebserviceKeys !== 0) expect(numberOfWebserviceKeys).to.be.above(0);
  });

  // 1 : Create webservice key
  describe('Create webservice key', async () => {
    it('should go to add new webservice key page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewWebserviceKeyPage', baseContext);

      await webservicePage.goToAddNewWebserviceKeyPage(page);

      const pageTitle = await addWebservicePage.getPageTitle(page);
      expect(pageTitle).to.contains(addWebservicePage.pageTitleCreate);
    });

    it('should create webservice key and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createWebserviceKey', baseContext);

      const textResult = await addWebservicePage.createEditWebservice(page, createWebserviceData);
      expect(textResult).to.equal(addWebservicePage.successfulCreationMessage);

      const numberOfWebserviceKeysAfterCreation = await webservicePage.getNumberOfElementInGrid(page);
      expect(numberOfWebserviceKeysAfterCreation).to.be.equal(numberOfWebserviceKeys + 1);
    });
  });

  // 2 : Update webservice key
  describe('Update webservice key', async () => {
    it('should filter list by key description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterUpdate', baseContext);

      await webservicePage.filterWebserviceTable(
        page,
        'input',
        'description',
        createWebserviceData.keyDescription,
      );

      const key = await webservicePage.getTextColumnFromTable(page, 1, 'description');
      expect(key).to.contains(createWebserviceData.keyDescription);
    });

    it('should go to edit webservice page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditWebservicePage', baseContext);

      await webservicePage.goToEditWebservicePage(page, 1);

      const pageTitle = await addWebservicePage.getPageTitle(page);
      expect(pageTitle).to.contains(addWebservicePage.pageTitleEdit);
    });

    it('should update the webservice key and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateWebserviceKey', baseContext);

      const textResult = await addWebservicePage.createEditWebservice(page, editWebserviceData);
      expect(textResult).to.equal(addWebservicePage.successfulUpdateMessage);
    });

    it('should reset filter and check the number of webservice keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterUpdate', baseContext);

      const numberOfWebserviceKeyAfterDelete = await webservicePage.resetAndGetNumberOfLines(page);
      expect(numberOfWebserviceKeyAfterDelete).to.be.equal(numberOfWebserviceKeys + 1);
    });
  });

  // 3 : Delete webservice key
  describe('Delete webservice key', async () => {
    it('should filter list by key description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterBeforeDelete', baseContext);

      await webservicePage.filterWebserviceTable(
        page,
        'input',
        'description',
        editWebserviceData.keyDescription,
      );

      const key = await webservicePage.getTextColumnFromTable(page, 1, 'description');
      expect(key).to.contains(editWebserviceData.keyDescription);
    });

    it('should delete webservice key', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteWebserviceKey', baseContext);

      const textResult = await webservicePage.deleteWebserviceKey(page, 1);
      expect(textResult).to.equal(webservicePage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of webservice keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfWebserviceKeyAfterDelete = await webservicePage.resetAndGetNumberOfLines(page);
      expect(numberOfWebserviceKeyAfterDelete).to.be.equal(numberOfWebserviceKeys);
    });
  });
});
