import helper from '@utils/helpers';
import testContext from '@utils/testContext';

import loginCommon from '@commonTests/BO/loginBO';

import dashboardPage from '@pages/BO/dashboard';
import apiClientPage from 'pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';

import APIClientData from '@data/faker/APIClient';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

/**
 * Function to create API Client
 * @param apiClient {APIClientData} Data to set in API Client form
 * @param baseContext {string} String to identify the test
 */
function createAPIClientTest(apiClient: APIClientData, baseContext: string = 'commonTests-createAPIClientTest'): void {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAPIClient: number = 0;

  describe('PRE-TEST: Create an API Client', async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

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

      numberOfAPIClient = await apiClientPage.getNumberOfElementInGrid(page);
      expect(numberOfAPIClient).to.gte(0);
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

      const textResult = await addNewApiClientPage.addAPIClient(page, apiClient);
      expect(textResult).to.contains(addNewApiClientPage.successfulCreationMessage);

      // Go back to list to get number of elements because creation form redirects to edition form
      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );
      const numElements = await apiClientPage.getNumberOfElementInGrid(page);
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

  describe('PRE-TEST: Delete an API Client', async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

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

      numberOfAPIClient = await apiClientPage.getNumberOfElementInGrid(page);
      expect(numberOfAPIClient).to.gte(0);
    });

    it('should delete API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAPIClient', baseContext);

      const textResult = await apiClientPage.deleteAPIClient(page, 1);
      expect(textResult).to.equal(addNewApiClientPage.successfulDeleteMessage);

      const numElements = await apiClientPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(0);
    });
  });
}

export {
  createAPIClientTest,
  deleteAPIClientTest,
};
