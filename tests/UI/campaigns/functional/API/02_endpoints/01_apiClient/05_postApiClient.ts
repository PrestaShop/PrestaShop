// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boApiClientsPage,
  boApiClientsCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerAPIClient,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_apiClient_postApiClient';

describe('API : POST /api-clients', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;

  const clientScope: string = 'api_client_write';
  const createClient: FakerAPIClient = new FakerAPIClient({
    scopes: [
      'api_client_read',
      'hook_read',
    ],
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('API : Create the API Access', async () => {
    it('should request the endpoint /api-clients', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.post('api-clients', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          clientId: createClient.clientId,
          clientName: createClient.clientName,
          description: createClient.description,
          enabled: createClient.enabled,
          lifetime: createClient.tokenLifetime,
          scopes: createClient.scopes,
        },
      });
      expect(apiResponse.status()).to.eq(201);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);

      expect(jsonResponse).to.have.all.keys(
        'apiClientId',
        'secret',
      );
    });

    it('should check the JSON Response', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseJSON', baseContext);

      expect(jsonResponse.apiClientId).to.be.gt(0);
      expect(jsonResponse.secret.length).to.be.gt(0);
    });
  });

  describe('BackOffice : Check the API Access is created', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToAdminAPIPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );

      const pageTitle = await boApiClientsPage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsPage.pageTitle);
    });

    it('should check there are 2 records', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkRecords', baseContext);

      const numRecords = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numRecords).to.equal(2);
    });

    it('should check the JSON Response : `apiClientId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseApiClientId', baseContext);

      // Get ID from last created API Client
      const apiClientsNumber = await boApiClientsPage.getNumberOfElementInGrid(page);
      const value = parseInt(await boApiClientsPage.getTextColumn(page, 'id_api_client', apiClientsNumber), 10);
      expect(value).to.equal(jsonResponse.apiClientId);
    });

    it('should check the JSON Response : `clientId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseClientId', baseContext);

      // Get ID from last created API Client
      const apiClientsNumber = await boApiClientsPage.getNumberOfElementInGrid(page);
      const value = await boApiClientsPage.getTextColumn(page, 'client_id', apiClientsNumber);
      expect(value).to.equal(createClient.clientId);
    });

    it('should check the JSON Response : `clientName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseClientName', baseContext);

      // Get ID from last created API Client
      const apiClientsNumber = await boApiClientsPage.getNumberOfElementInGrid(page);
      const value = await boApiClientsPage.getTextColumn(page, 'client_name', apiClientsNumber);
      expect(value).to.equal(createClient.clientName);
    });

    it('should go to the edit page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAPIClientPage', baseContext);

      await boApiClientsPage.goToEditAPIClientPage(page, 2);

      const pageTitle = await boApiClientsCreatePage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsCreatePage.pageTitleEdit(createClient.clientName));
    });

    it('should check the JSON Response : `description`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescription', baseContext);

      const value = await boApiClientsCreatePage.getValue(page, 'description');
      expect(value).to.equal(createClient.description);
    });

    it('should check the JSON Response : `enabled`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseEnabled', baseContext);

      const value = await boApiClientsCreatePage.isEnabled(page);
      expect(value).to.equal(createClient.enabled);
    });

    it('should check the JSON Response : `lifetime`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLifetime', baseContext);

      const value = parseInt(await boApiClientsCreatePage.getValue(page, 'tokenLifetime'), 10);
      expect(value).to.equal(createClient.tokenLifetime);
    });

    it('should check the JSON Response : `scopes`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseScopes', baseContext);

      const value = await boApiClientsCreatePage.getApiScopes(page, 'ps_apiresources', true);
      expect(value).to.deep.equal(createClient.scopes);
    });
  });
  describe('BackOffice : Delete the API Access', async () => {
    it('should return to the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToList', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );

      const pageTitle = await boApiClientsPage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsPage.pageTitle);

      const numElements = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(2);
    });

    it('should delete API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAPIClient', baseContext);

      const textResult = await boApiClientsPage.deleteAPIClient(page, 2);
      expect(textResult).to.equal(boApiClientsCreatePage.successfulDeleteMessage);

      const numElements = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });
  });
});
