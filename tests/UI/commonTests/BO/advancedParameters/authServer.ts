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
  utilsPlaywright, utilsAPI, type APIRequestContext,
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
 * @param clientId {string} Client ID of the APi client we want to remove, if empty the first row is used
 */
function deleteAPIClientTest(baseContext: string = 'commonTests-deleteAPIClientTest', clientId: string = 'client-id'): void {
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
      expect(numberOfAPIClient).to.greaterThanOrEqual(0);
    });

    it(`should delete API Client by clientId ${clientId}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAPIClient', baseContext);

      const row = await boApiClientsPage.getNthRowByClientId(page, clientId);
      expect(row).to.be.a('number', `Could not find API client with client ID "${clientId}"`);
      expect(row).to.be.greaterThan(0, `Could not find API client with client ID "${clientId}"`);

      // @ts-ignore
      const textResult = await boApiClientsPage.deleteAPIClient(page, row);
      expect(textResult).to.equal(boApiClientsCreatePage.successfulDeleteMessage);

      const numElements = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(numberOfAPIClient - 1);
    });
  });
}

/**
 * Function to request an access token with specified scopes (separated by comma)
 * @param clientScopes {string} List of scopes separated by comma
 * @return Promise<string> The Bearer access token value
 */
async function requestAccessToken(clientScopes: string): Promise<string> {
  const apiContext: APIRequestContext = await utilsPlaywright.createAPIContext(global.API.URL);
  const apiResponse = await apiContext.post('access_token', {
    form: {
      client_id: global.API.CLIENT_ID,
      client_secret: global.API.CLIENT_SECRET,
      grant_type: 'client_credentials',
      scope: clientScopes,
    },
  });
  expect(apiResponse.status()).to.eq(200, (await apiResponse.json()).message);
  expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
  expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

  const jsonResponse = await apiResponse.json();
  expect(jsonResponse).to.have.property('access_token');
  expect(jsonResponse.token_type).to.be.a('string');

  return jsonResponse.access_token;
}

export {
  createAPIClientTest,
  deleteAPIClientTest,
  requestAccessToken,
};
