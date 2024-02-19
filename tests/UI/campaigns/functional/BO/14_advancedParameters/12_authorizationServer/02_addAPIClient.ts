// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
import apiClientPage from 'pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import APIClientData from '@data/faker/APIClient';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_authorizationServer_addAPIClient';

describe('BO - Advanced Parameter - API Client : Add API Client', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const createAPIClient: APIClientData = new APIClientData({
    clientName: 'API Client XYZ',
    clientId: 'api-client-xyz',
    description: 'Description ABC',
    scopes: [
      'hook_write',
      'product_read',
    ],
  });

  // Pre-condition: Enable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, true, `${baseContext}_enableAuthorizationServer`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('BO - Advanced Parameter - API Client : CRUD', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAuthorizationServerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );

      const pageTitle = await apiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(apiClientPage.pageTitle);
    });

    it('should check that no records found', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

      const noRecordsFoundText = await apiClientPage.getTextForEmptyTable(page);
      expect(noRecordsFoundText).to.contains('warning No records found');
    });

    it('should go to add New API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIClientPage', baseContext);

      await apiClientPage.goToNewAPIClientPage(page);

      const pageTitle = await addNewApiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiClientPage.pageTitleCreate);
    });

    it('should create API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAPIClient', baseContext);

      const textResult = await addNewApiClientPage.addAPIClient(page, createAPIClient);
      expect(textResult).to.contains(addNewApiClientPage.successfulCreationMessage);

      const textMessage = await addNewApiClientPage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(addNewApiClientPage.apiClientGeneratedMessage);
    });

    it('should copy client secret', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'copyClientSecret', baseContext);

      await addNewApiClientPage.copyClientSecret(page);

      const clipboardContent = await addNewApiClientPage.getClipboardText(page);
      expect(clipboardContent.length).to.be.gt(0);

      const clientSecret = await addNewApiClientPage.getClientSecret(page);
      expect(clientSecret.length).to.be.gt(0);

      expect(clipboardContent).to.be.equal(clientSecret);
    });

    it('should reload page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reloadPage', baseContext);

      const hasAlertBlock = await addNewApiClientPage.hasAlertBlock(page);
      expect(hasAlertBlock).to.equal(false);
    });

    it('should return to the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnList', baseContext);

      // Go back to list to get number of elements because edition form redirects to itself
      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );
      const numElements = await apiClientPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });

    it('should delete API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAPIClient', baseContext);

      const textResult = await apiClientPage.deleteAPIClient(page, 1);
      expect(textResult).to.equal(addNewApiClientPage.successfulDeleteMessage);

      const numElements = await apiClientPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(0);
    });
  });

  // Post-condition: Disable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, false, `${baseContext}_disableAuthorizationServer`);
});
