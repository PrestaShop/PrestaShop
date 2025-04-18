// Import utils
import testContext from '@utils/testContext';

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

const baseContext: string = 'functional_BO_advancedParameters_adminAPI_addAPIClient';

describe('BO - Advanced Parameter - API Client : Add API Client', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const createAPIClient: FakerAPIClient = new FakerAPIClient({
    clientName: 'API Client XYZ',
    clientId: 'api-client-xyz',
    description: 'Description ABC',
    scopes: [
      'hook_write',
      'product_read',
    ],
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BO - Advanced Parameter - API Client : CRUD', async () => {
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

    it('should check that no records found', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

      const noRecordsFoundText = await boApiClientsPage.getTextForEmptyTable(page);
      expect(noRecordsFoundText).to.contains('warning No records found');
    });

    it('should go to add New API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIClientPage', baseContext);

      await boApiClientsPage.goToNewAPIClientPage(page);

      const pageTitle = await boApiClientsCreatePage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsCreatePage.pageTitleCreate);
    });

    it('should create API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAPIClient', baseContext);

      const textResult = await boApiClientsCreatePage.addAPIClient(page, createAPIClient);
      expect(textResult).to.contains(boApiClientsCreatePage.successfulCreationMessage);

      const textMessage = await boApiClientsCreatePage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(boApiClientsCreatePage.apiClientGeneratedMessage);
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
      await testContext.addContextItem(this, 'testIdentifier', 'reloadPage', baseContext);

      const hasAlertBlock = await boApiClientsCreatePage.hasAlertBlock(page);
      expect(hasAlertBlock).to.equal(false);
    });

    it('should return to the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnList', baseContext);

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
