// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {createAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  boApiClientsPage,
  boApiClientsCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerAPIClient,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_adminAPI_editAPIClient';

describe('BO - Advanced Parameter - Authorization Server : Edit API Client', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const createAPIClient: FakerAPIClient = new FakerAPIClient({
    clientName: 'API Client XYZ',
    clientId: 'api-client-xyz',
    description: 'Description ABC',
  });
  const editAPIClient: FakerAPIClient = new FakerAPIClient({
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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BO - Advanced Parameter - API Client : Edit API Client', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdminAPIPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );

      const pageTitle = await boApiClientsPage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsPage.pageTitle);
    });

    it('should go to edit API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAPIClientPage', baseContext);

      await boApiClientsPage.goToEditAPIClientPage(page, 1);

      const pageTitle = await boApiClientsCreatePage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsCreatePage.pageTitleEdit(createAPIClient.clientName));
    });

    it('should edit API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editAPIClient', baseContext);

      const textResult = await boApiClientsCreatePage.addAPIClient(page, editAPIClient);
      expect(textResult).to.equal(boApiClientsCreatePage.successfulUpdateMessage);
    });

    it('should check information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInformations', baseContext);

      const tokenLifetime = await boApiClientsCreatePage.getValue(page, 'tokenLifetime');
      expect(tokenLifetime).to.be.equal(editAPIClient.tokenLifetime.toString());

      const hasScopeHookRead = await boApiClientsCreatePage.isAPIScopeChecked(page, 'hook_read');
      expect(hasScopeHookRead).to.be.equal(false);

      const hasScopeHookWrite = await boApiClientsCreatePage.isAPIScopeChecked(page, 'hook_write');
      expect(hasScopeHookWrite).to.be.equal(true);
    });

    it('should disable the application', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableApplication', baseContext);

      await boApiClientsCreatePage.setEnabled(page, false);

      const textResult = await boApiClientsCreatePage.saveForm(page);
      expect(textResult).to.equal(boApiClientsCreatePage.successfulUpdateMessage);
    });

    it('should check information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInformationsAfterDisable', baseContext);

      const status = await boApiClientsCreatePage.isEnabled(page);
      expect(status).to.equal(false);
    });

    it('should return to the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToListAfterDisable', baseContext);

      // Go back to list to get number of elements because edition form redirects to itself
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );
      const numElements = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });

    it('should check list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkListAfterDisable', baseContext);

      const status = await boApiClientsPage.getStatus(page, 1);
      expect(status).to.equal(false);
    });

    it('should go to edit API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAPIClientPageAfterDisable', baseContext);

      await boApiClientsPage.goToEditAPIClientPage(page, 1);

      const pageTitle = await boApiClientsCreatePage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsCreatePage.pageTitleEdit(editAPIClient.clientName));
    });

    it('should enable the application', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableApplication', baseContext);

      await boApiClientsCreatePage.setEnabled(page, true);

      const textResult = await boApiClientsCreatePage.saveForm(page);
      expect(textResult).to.equal(boApiClientsCreatePage.successfulUpdateMessage);
    });

    it('should check information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInformationsAfterEnable', baseContext);

      const status = await boApiClientsCreatePage.isEnabled(page);
      expect(status).to.equal(true);
    });

    it('should return to the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToListAfterEnable', baseContext);

      // Go back to list to get number of elements because edition form redirects to itself
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );
      const numElements = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });

    it('should check list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkListAfterEnable', baseContext);

      const status = await boApiClientsPage.getStatus(page, 1);
      expect(status).to.equal(true);
    });

    it('should go to edit API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAPIClientPageAfterEnable', baseContext);

      await boApiClientsPage.goToEditAPIClientPage(page, 1);

      const pageTitle = await boApiClientsCreatePage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsCreatePage.pageTitleEdit(editAPIClient.clientName));
    });

    it('should regenerate the client secret', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'regenerateClientSecret', baseContext);

      const textResult = await boApiClientsCreatePage.regenerateClientSecret(page);
      expect(textResult).to.contains(boApiClientsCreatePage.successfulCreationMessage);

      const textMessage = await boApiClientsCreatePage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(boApiClientsCreatePage.apiClientRegeneratedMessage);
    });

    it('should copy client secret', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'copyClientSecret', baseContext);

      await boApiClientsCreatePage.copyClientSecret(page);

      const clipboardContent = await boApiClientsCreatePage.getClipboardText(page);
      expect(clipboardContent.length).to.be.gt(0);

      const clientSecret = await boApiClientsCreatePage.getClientSecret(page);
      expect(clientSecret.length).to.be.gt(0);

      expect(clipboardContent).to.be.equal(clientSecret);
    });

    it('should reload page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAPIClient', baseContext);

      const hasAlertBlock = await boApiClientsCreatePage.hasAlertBlock(page);
      expect(hasAlertBlock).to.equal(false);
    });

    it('should return to the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToList', baseContext);

      // Go back to list to get number of elements because edition form redirects to itself
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );
      const numElements = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });

    it('should delete API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAPIClient', baseContext);

      const textResult = await boApiClientsPage.deleteAPIClient(page, 1);
      expect(textResult).to.equal(boApiClientsCreatePage.successfulDeleteMessage);

      const numElements = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(0);
    });
  });
});
