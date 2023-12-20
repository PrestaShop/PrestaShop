// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {createAPIAccessTest} from '@commonTests/BO/advancedParameters/authServer';
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

const baseContext: string = 'functional_BO_advancedParameters_authorizationServer_editAPIAccess';

describe('BO - Advanced Parameter - Authorization Server : Edit API Access', async () => {
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
    tokenLifetime: 5,
    scopes: [
      'hook_write',
      'api_access_read',
    ],
  });

  // Pre-condition: Enable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, true, `${baseContext}_enableAuthorizationServer`);

  // Pre-condition: Create an API Access
  createAPIAccessTest(createAPIAccess, `${baseContext}_preTest_0`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('BO - Advanced Parameter - API Access : Edit API Access', async () => {
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
    });

    it('should check information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInformations', baseContext);

      const tokenLifetime = await addNewApiAccessPage.getValue(page, 'tokenLifetime');
      expect(tokenLifetime).to.be.equal(editAPIAccess.tokenLifetime.toString());

      const hasScopeHookRead = await addNewApiAccessPage.isAPIScopeChecked(page, 'hook_read');
      expect(hasScopeHookRead).to.be.equal(false);

      const hasScopeHookWrite = await addNewApiAccessPage.isAPIScopeChecked(page, 'hook_write');
      expect(hasScopeHookWrite).to.be.equal(true);
    });

    it('should disable the application', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableApplication', baseContext);

      await addNewApiAccessPage.setEnabled(page, false);

      const textResult = await addNewApiAccessPage.saveForm(page);
      expect(textResult).to.equal(addNewApiAccessPage.successfulUpdateMessage);
    });

    it('should check information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInformationsAfterDisable', baseContext);

      const status = await addNewApiAccessPage.isEnabled(page);
      expect(status).to.equal(false);
    });

    it('should return to the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToListAfterDisable', baseContext);

      // Go back to list to get number of elements because edition form redirects to itself
      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );
      const numElements = await apiAccessPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });

    it('should check list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkListAfterDisable', baseContext);

      const status = await apiAccessPage.getStatus(page, 1);
      expect(status).to.equal(false);
    });

    it('should go to edit API Access page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAPIAccessPageAfterDisable', baseContext);

      await apiAccessPage.goToEditAPIAccessPage(page, 1);

      const pageTitle = await addNewApiAccessPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiAccessPage.pageTitleEdit(editAPIAccess.clientName));
    });

    it('should enable the application', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableApplication', baseContext);

      await addNewApiAccessPage.setEnabled(page, true);

      const textResult = await addNewApiAccessPage.saveForm(page);
      expect(textResult).to.equal(addNewApiAccessPage.successfulUpdateMessage);
    });

    it('should check information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInformationsAfterEnable', baseContext);

      const status = await addNewApiAccessPage.isEnabled(page);
      expect(status).to.equal(true);
    });

    it('should return to the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToListAfterEnable', baseContext);

      // Go back to list to get number of elements because edition form redirects to itself
      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );
      const numElements = await apiAccessPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });

    it('should check list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkListAfterEnable', baseContext);

      const status = await apiAccessPage.getStatus(page, 1);
      expect(status).to.equal(true);
    });

    it('should go to edit API Access page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAPIAccessPageAfterEnable', baseContext);

      await apiAccessPage.goToEditAPIAccessPage(page, 1);

      const pageTitle = await addNewApiAccessPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiAccessPage.pageTitleEdit(editAPIAccess.clientName));
    });

    it('should regenerate the client secret', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'regenerateClientSecret', baseContext);

      const textResult = await addNewApiAccessPage.regenerateClientSecret(page);
      expect(textResult).to.contains(addNewApiAccessPage.successfulCreationMessage);

      const textMessage = await addNewApiAccessPage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(addNewApiAccessPage.apiAccessRegeneratedMessage);
    });

    it('should copy client secret', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'copyClientSecret', baseContext);

      await addNewApiAccessPage.copyClientSecret(page);

      const clipboardContent = await addNewApiAccessPage.getClipboardText(page);
      expect(clipboardContent.length).to.be.gt(0);

      const clientSecret = await addNewApiAccessPage.getClientSecret(page);
      expect(clientSecret.length).to.be.gt(0);

      expect(clipboardContent).to.be.equal(clientSecret);
    });

    it('should reload page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAPIAccess', baseContext);

      const hasAlertBlock = await addNewApiAccessPage.hasAlertBlock(page);
      expect(hasAlertBlock).to.equal(false);
    });

    it('should return to the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToList', baseContext);

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
