// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import webservicePage from '@pages/BO/advancedParameters/webservice';
import dashboardPage from '@pages/BO/dashboard';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import addWebservicePage from '@pages/BO/advancedParameters/webservice/add';
import WebserviceData from '@data/faker/webservice';
import {WebservicePermission} from '@data/types/webservice';

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
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > Webservice\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdvParametersWebservice', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.webserviceLink,
      );
      await webservicePage.closeSfToolBar(page);

      const pageTitle = await webservicePage.getPageTitle(page);
      await expect(pageTitle).to.contains(webservicePage.pageTitle);
    });

    it(`should ${status ? 'enable' : 'disable'} the webservice`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setWebserviceStatus', baseContext);

      const textResult = await webservicePage.setWebserviceStatus(page, status);
      await expect(textResult).to.contains(webservicePage.successfulUpdateMessage);
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

    const webserviceData: WebserviceData = new WebserviceData({
      keyDescription,
      permissions: keyPermissions,
    });

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

    it('should go to \'Advanced Parameters > Webservice\' page', async function () {
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
      await expect(numberOfWebserviceKeys).to.be.eq(0);
    });

    it('should go to add new webservice key page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewWebserviceKeyPage', baseContext);

      await webservicePage.goToAddNewWebserviceKeyPage(page);

      const pageTitle = await addWebservicePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addWebservicePage.pageTitleCreate);
    });

    it('should create webservice key and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createWebserviceKey', baseContext);

      const textResult = await addWebservicePage.createEditWebservice(page, webserviceData);
      await expect(textResult).to.equal(addWebservicePage.successfulCreationMessage);

      const numberOfWebserviceKeysAfterCreation = await webservicePage.getNumberOfElementInGrid(page);
      await expect(numberOfWebserviceKeysAfterCreation).to.be.eq(1);
    });
  });
}
function removeWebserviceKey(keyDescription: string, baseContext: string = 'commonTests-removeWebserviceKey'): void {
  describe(`Remove a new webservice key named "${keyDescription}"`, async () => {
    let numberOfWebserviceKeys: number = 0;

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

    it('should go to \'Advanced Parameters > Webservice\' page', async function () {
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
      await expect(numberOfWebserviceKeys).to.be.eq(1);
    });

    it('should filter list by key description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterBeforeDelete', baseContext);

      await webservicePage.filterWebserviceTable(
        page,
        'input',
        'description',
        keyDescription,
      );

      const key = await webservicePage.getTextColumnFromTable(page, 1, 'description');
      await expect(key).to.contains(keyDescription);
    });

    it('should delete webservice key', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteWebserviceKey', baseContext);

      const textResult = await webservicePage.deleteWebserviceKey(page, 1);
      await expect(textResult).to.equal(webservicePage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of webservice keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfWebserviceKeyAfterDelete = await webservicePage.resetAndGetNumberOfLines(page);
      await expect(numberOfWebserviceKeyAfterDelete).to.be.equal(0);
    });
  });
}

export {setWebserviceStatus, addWebserviceKey, removeWebserviceKey};
