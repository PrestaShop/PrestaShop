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

const baseContext: string = 'functional_BO_advancedParameters_adminAPI_CRUD';

describe('BO - Advanced Parameter - API Client : CRUD', async () => {
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
      expect(textResult).to.contain(boApiClientsCreatePage.successfulCreationMessage);

      // Go back to list to get number of elements because creation form redirects to edition form
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );
      const numElements = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
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
