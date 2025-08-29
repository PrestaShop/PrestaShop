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
  type WebservicePermission,
} from '@prestashop-core/ui-testing';

let browserContext: BrowserContext;
let page: Page;

/**
 * Set Webservice Status
 * @param status {boolean} Status of the webservice
 * @param baseContext {string} String to identify the test
 */
function setWebserviceStatus(status: boolean, baseContext: string = 'commonTests-setWebserviceStatus'): void {
  describe(`${status ? 'Enable' : 'Disable'} the Webservice`, async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdvParametersWebservice', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.webserviceLink,
      );
      await boWebservicesPage.closeSfToolBar(page);

      const pageTitle = await boWebservicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boWebservicesPage.pageTitle);
    });

    it(`should ${status ? 'enable' : 'disable'} the webservice`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setWebserviceStatus', baseContext);

      const textResult = await boWebservicesPage.setWebserviceStatus(page, status);
      expect(textResult).to.contains(boWebservicesPage.successfulUpdateMessage);
    });
  });
}

function addWebserviceKey(
  keyDescription: string,
  keyPermissions: WebservicePermission[],
  baseContext: string = 'commonTests-addWebserviceKey',
): void {
  describe(`Add a new webservice key named "${keyDescription}"`, async () => {
    let numberOfWebserviceKeys: number = 0;

    const webserviceData: FakerWebservice = new FakerWebservice({
      keyDescription,
      permissions: keyPermissions,
    });

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
      expect(numberOfWebserviceKeys).to.be.eq(0);
    });

    it('should go to add new webservice key page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewWebserviceKeyPage', baseContext);

      await boWebservicesPage.goToAddNewWebserviceKeyPage(page);

      const pageTitle = await boWebservicesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boWebservicesCreatePage.pageTitleCreate);
    });

    it('should create webservice key and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createWebserviceKey', baseContext);

      const textResult = await boWebservicesCreatePage.createEditWebservice(page, webserviceData);
      expect(textResult).to.equal(boWebservicesCreatePage.successfulCreationMessage);

      const numberOfWebserviceKeysAfterCreation = await boWebservicesPage.getNumberOfElementInGrid(page);
      expect(numberOfWebserviceKeysAfterCreation).to.be.eq(1);
    });
  });
}
function removeWebserviceKey(keyDescription: string, baseContext: string = 'commonTests-removeWebserviceKey'): void {
  describe(`Remove a new webservice key named "${keyDescription}"`, async () => {
    let numberOfWebserviceKeys: number = 0;

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
      expect(numberOfWebserviceKeys).to.be.eq(1);
    });

    it('should filter list by key description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterBeforeDelete', baseContext);

      await boWebservicesPage.filterWebserviceTable(
        page,
        'input',
        'description',
        keyDescription,
      );

      const key = await boWebservicesPage.getTextColumnFromTable(page, 1, 'description');
      expect(key).to.contains(keyDescription);
    });

    it('should delete webservice key', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteWebserviceKey', baseContext);

      const textResult = await boWebservicesPage.deleteWebserviceKey(page, 1);
      expect(textResult).to.equal(boWebservicesPage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of webservice keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfWebserviceKeyAfterDelete = await boWebservicesPage.resetAndGetNumberOfLines(page);
      expect(numberOfWebserviceKeyAfterDelete).to.be.equal(0);
    });
  });
}

export {setWebserviceStatus, addWebserviceKey, removeWebserviceKey};
