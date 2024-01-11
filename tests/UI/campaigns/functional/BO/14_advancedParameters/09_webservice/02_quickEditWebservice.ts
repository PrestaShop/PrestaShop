// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import webservicePage from '@pages/BO/advancedParameters/webservice';
import addWebservicePage from '@pages/BO/advancedParameters/webservice/add';

// Import data
import WebserviceData from '@data/faker/webservice';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_webservice_quickEditWebservice';

describe('BO - Advanced Parameters - Webservice : Quick edit webservice', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfWebserviceKeys: number = 0;

  const webServiceData: WebserviceData = new WebserviceData();

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

  it('should go to \'Advanced parameters > Webservice\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToWebservicePage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.webserviceLink,
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

  it('should go to add new webservice key page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewWebserviceKeyPage', baseContext);

    await webservicePage.goToAddNewWebserviceKeyPage(page);

    const pageTitle = await addWebservicePage.getPageTitle(page);
    expect(pageTitle).to.contains(addWebservicePage.pageTitleCreate);
  });

  it('should create webservice key', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createWebserviceKey', baseContext);

    const textResult = await addWebservicePage.createEditWebservice(page, webServiceData, false);
    expect(textResult).to.equal(addWebservicePage.successfulCreationMessage);

    const numberOfWebserviceKeysAfterCreation = await webservicePage.getNumberOfElementInGrid(page);
    expect(numberOfWebserviceKeysAfterCreation).to.be.equal(numberOfWebserviceKeys + 1);
  });

  describe('Quick edit webservice', async () => {
    const statuses = [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ];

    statuses.forEach((webservice: { args: { status: string, enable: boolean } }) => {
      it(`should ${webservice.args.status} the webservice`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${webservice.args.status}Webservice`, baseContext);

        const isActionPerformed = await webservicePage.setStatus(
          page,
          1,
          webservice.args.enable,
        );

        if (isActionPerformed) {
          const resultMessage = await webservicePage.getValidationMessage(page);
          expect(resultMessage).to.contains(webservicePage.successfulUpdateStatusMessage);
        }

        const webserviceStatus = await webservicePage.getStatus(page, 1);
        expect(webserviceStatus).to.be.equal(webservice.args.enable);
      });
    });
  });

  describe('Delete the created webservice key', async () => {
    it('should delete the created webservice key', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteWebserviceKey', baseContext);

      const textResult = await webservicePage.deleteWebserviceKey(page, 1);
      expect(textResult).to.equal(webservicePage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of webservice keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfElement = await webservicePage.resetAndGetNumberOfLines(page);
      expect(numberOfElement).to.be.equal(numberOfWebserviceKeys);
    });
  });
});
