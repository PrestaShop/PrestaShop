// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import apiClientPage from 'pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  FakerAPIClient,
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
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdminAPIPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
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
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
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
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
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
