import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boApiClientsPage,
  boApiClientsCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type FakerAPIClient,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

/**
 * Function to create API Client
 * @param apiClient {FakerAPIClient} Data to set in API Client form
 * @param baseContext {string} String to identify the test
 */
function createAPIClientTest(apiClient: FakerAPIClient, baseContext: string = 'commonTests-createAPIClientTest'): void {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAPIClient: number = 0;

  describe('Create an API Client', async () => {
    // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

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

      numberOfAPIClient = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numberOfAPIClient).to.gte(0);
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

      const textResult = await boApiClientsCreatePage.addAPIClient(page, apiClient);
      expect(textResult).to.contains(boApiClientsCreatePage.successfulCreationMessage);

      // Go back to list to get number of elements because creation form redirects to edition form
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );
      const numElements = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(numberOfAPIClient + 1);
    });
  });
}

/**
 * Function to delete API Client
 * @param baseContext {string} String to identify the test
 */
function deleteAPIClientTest(baseContext: string = 'commonTests-deleteAPIClientTest'): void {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAPIClient: number = 0;

  describe('Delete an API Client', async () => {
    // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

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

      numberOfAPIClient = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numberOfAPIClient).to.gte(0);
    });

    it('should delete API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAPIClient', baseContext);

      const textResult = await boApiClientsPage.deleteAPIClient(page, 1);
      expect(textResult).to.equal(boApiClientsCreatePage.successfulDeleteMessage);

      const numElements = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(0);
    });
  });
}

export {
  createAPIClientTest,
  deleteAPIClientTest,
};
