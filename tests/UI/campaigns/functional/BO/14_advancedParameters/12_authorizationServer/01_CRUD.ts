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

const baseContext: string = 'functional_BO_advancedParameters_authorizationServer_CRUD';

describe('BO - Advanced Parameter - API Client : CRUD', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const createAPIClient: APIClientData = new APIClientData({
    clientName: 'API Client XYZ',
    clientId: 'api-client-xyz',
    description: 'Description ABC',
  });
  const editAPIClient: APIClientData = new APIClientData({
    clientName: 'API Client UVW',
    clientId: 'api-client-uvw',
    description: 'Description DEF',
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
      expect(textResult).to.contain(addNewApiClientPage.successfulCreationMessage);

      // Go back to list to get number of elements because creation form redirects to edition form
      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );
      const numElements = await apiClientPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });

    it('should go to edit API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAPIClientPage', baseContext);

      await apiClientPage.goToEditAPIClientPage(page, 1);

      const pageTitle = await addNewApiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiClientPage.pageTitleEdit(createAPIClient.clientName));
    });

    it('should edit API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editAPIClient', baseContext);

      const textResult = await addNewApiClientPage.addAPIClient(page, editAPIClient);
      expect(textResult).to.equal(addNewApiClientPage.successfulUpdateMessage);

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
