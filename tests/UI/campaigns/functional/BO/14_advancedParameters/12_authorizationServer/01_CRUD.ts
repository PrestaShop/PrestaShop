// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
import apiAccessPage from 'pages/BO/advancedParameters/APIAccess';
import addNewApiAccessPage from '@pages/BO/advancedParameters/APIAccess/add';
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import APIAccessData from '@data/faker/APIAccess';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_authorizationServer_CRUD';

describe('BO - Advanced Parameter - API Access : CRUD', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const createAPIAccess: APIAccessData = new APIAccessData({
    clientName: 'API Access XYZ',
    clientId: 'api-access-xyz',
    description: 'Description ABC',
  });
  const editAPIAccess: APIAccessData = new APIAccessData({
    clientName: 'API Access UVW',
    clientId: 'api-access-uvw',
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

  describe('BO - Advanced Parameter - API Access : CRUD', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > API Access\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAuthorizationServerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );

      const pageTitle = await apiAccessPage.getPageTitle(page);
      expect(pageTitle).to.eq(apiAccessPage.pageTitle);
    });

    it('should check that no records found', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

      const noRecordsFoundText = await apiAccessPage.getTextForEmptyTable(page);
      expect(noRecordsFoundText).to.contains('warning No records found');
    });

    it('should go to add New API Access page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIAccessPage', baseContext);

      await apiAccessPage.goToNewAPIAccessPage(page);

      const pageTitle = await addNewApiAccessPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiAccessPage.pageTitleCreate);
    });

    it('should create API Access', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAPIAccess', baseContext);

      const textResult = await addNewApiAccessPage.addAPIAccess(page, createAPIAccess);
      expect(textResult).to.contain(addNewApiAccessPage.successfulCreationMessage);

      // Go back to list to get number of elements because creation form redirects to edition form
      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );
      const numElements = await apiAccessPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });

    it('should go to edit API Access page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAPIAccessPage', baseContext);

      await apiAccessPage.goToEditAPIAccessPage(page, 1);

      const pageTitle = await addNewApiAccessPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiAccessPage.pageTitleEdit(createAPIAccess.clientName));
    });

    it('should edit API Access', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editAPIAccess', baseContext);

      const textResult = await addNewApiAccessPage.addAPIAccess(page, editAPIAccess);
      expect(textResult).to.equal(addNewApiAccessPage.successfulUpdateMessage);

      // Go back to list to get number of elements because edition form redirects to itself
      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );
      const numElements = await apiAccessPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });

    it('should delete API Access', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAPIAccess', baseContext);

      const textResult = await apiAccessPage.deleteAPIAccess(page, 1);
      expect(textResult).to.equal(addNewApiAccessPage.successfulDeleteMessage);

      const numElements = await apiAccessPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(0);
    });
  });

  // Post-condition: Disable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, false, `${baseContext}_disableAuthorizationServer`);
});
