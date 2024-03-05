// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {createAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';

// Import pages
import apiClientPage from 'pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import APIClientData from '@data/faker/APIClient';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_authorizationServer_editAPIClient';

describe('BO - Advanced Parameter - Authorization Server : Edit API Client', async () => {
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
    tokenLifetime: 5,
    scopes: [
      'hook_write',
      'product_read',
    ],
  });

  // Pre-condition: Create an API Client
  createAPIClientTest(createAPIClient, `${baseContext}_preTest_0`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('BO - Advanced Parameter - API Client : Edit API Client', async () => {
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
    });

    it('should check information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInformations', baseContext);

      const tokenLifetime = await addNewApiClientPage.getValue(page, 'tokenLifetime');
      expect(tokenLifetime).to.be.equal(editAPIClient.tokenLifetime.toString());

      const hasScopeHookRead = await addNewApiClientPage.isAPIScopeChecked(page, 'hook_read');
      expect(hasScopeHookRead).to.be.equal(false);

      const hasScopeHookWrite = await addNewApiClientPage.isAPIScopeChecked(page, 'hook_write');
      expect(hasScopeHookWrite).to.be.equal(true);
    });

    it('should disable the application', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableApplication', baseContext);

      await addNewApiClientPage.setEnabled(page, false);

      const textResult = await addNewApiClientPage.saveForm(page);
      expect(textResult).to.equal(addNewApiClientPage.successfulUpdateMessage);
    });

    it('should check information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInformationsAfterDisable', baseContext);

      const status = await addNewApiClientPage.isEnabled(page);
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
      const numElements = await apiClientPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });

    it('should check list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkListAfterDisable', baseContext);

      const status = await apiClientPage.getStatus(page, 1);
      expect(status).to.equal(false);
    });

    it('should go to edit API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAPIClientPageAfterDisable', baseContext);

      await apiClientPage.goToEditAPIClientPage(page, 1);

      const pageTitle = await addNewApiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiClientPage.pageTitleEdit(editAPIClient.clientName));
    });

    it('should enable the application', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableApplication', baseContext);

      await addNewApiClientPage.setEnabled(page, true);

      const textResult = await addNewApiClientPage.saveForm(page);
      expect(textResult).to.equal(addNewApiClientPage.successfulUpdateMessage);
    });

    it('should check information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInformationsAfterEnable', baseContext);

      const status = await addNewApiClientPage.isEnabled(page);
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
      const numElements = await apiClientPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });

    it('should check list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkListAfterEnable', baseContext);

      const status = await apiClientPage.getStatus(page, 1);
      expect(status).to.equal(true);
    });

    it('should go to edit API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAPIClientPageAfterEnable', baseContext);

      await apiClientPage.goToEditAPIClientPage(page, 1);

      const pageTitle = await addNewApiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiClientPage.pageTitleEdit(editAPIClient.clientName));
    });

    it('should regenerate the client secret', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'regenerateClientSecret', baseContext);

      const textResult = await addNewApiClientPage.regenerateClientSecret(page);
      expect(textResult).to.contains(addNewApiClientPage.successfulCreationMessage);

      const textMessage = await addNewApiClientPage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(addNewApiClientPage.apiClientRegeneratedMessage);
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
      await testContext.addContextItem(this, 'testIdentifier', 'createAPIClient', baseContext);

      const hasAlertBlock = await addNewApiClientPage.hasAlertBlock(page);
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
});
