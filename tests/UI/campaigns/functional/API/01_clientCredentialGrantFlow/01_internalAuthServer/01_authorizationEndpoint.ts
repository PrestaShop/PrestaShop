import testContext from '@utils/testContext';

// Import commonTest
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';

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

const baseContext: string = 'functional_API_clientCredentialGrantFlow_internalAuthServer_authorizationEndpoint';

describe('API : Internal Auth Server - Authorization Endpoint', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let apiContext: APIRequestContext;
  let clientSecret: string;

  const apiClient: FakerAPIClient = new FakerAPIClient({
    scopes: [
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

  describe('API Client : Fetch the client secret', async () => {
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

    it('should check that at least one API client is present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatOneAPIClientExists', baseContext);

      const apiClientsNumber = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(apiClientsNumber).to.be.greaterThanOrEqual(1);
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

      const textMessage = await boApiClientsCreatePage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(boApiClientsCreatePage.apiClientGeneratedMessage);
    });

    it('should copy client secret', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'copyClientSecret', baseContext);

      await boApiClientsCreatePage.copyClientSecret(page);

      clientSecret = await boApiClientsCreatePage.getClipboardText(page);
      expect(clientSecret.length).to.be.gt(0);
    });
  });

  describe('Authorization Endpoint', async () => {
    it('should request the endpoint /access_token with method GET', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodGET', baseContext);

      const apiResponse = await apiContext.get('access_token');
      expect(apiResponse.status()).to.eq(405);
    });

    it('should request the endpoint /access_token with method POST', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOST', baseContext);

      const apiResponse = await apiContext.post('access_token');
      expect(apiResponse.status()).to.eq(400);
    });

    it('should request the endpoint /access_token with method POST with unuseful data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTUnusefulData', baseContext);

      const apiResponse = await apiContext.post('access_token', {
        form: {
          notUsed: 'notUsed',
        },
      });
      expect(apiResponse.status()).to.eq(400);
    });

    it('should request the endpoint /access_token with method POST with invalid data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTInvalidData', baseContext);

      const apiResponse = await apiContext.post('access_token', {
        form: {
          client_id: 'bad_client_id',
          client_secret: 'bad_client_secret',
          grant_type: 'client_credentials',
        },
      });
      expect(apiResponse.status()).to.eq(401);
    });

    it('should request the endpoint /access_token with method POST with valid + unuseful data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTValidAndUnusefulData', baseContext);

      const apiResponse = await apiContext.post('access_token', {
        form: {
          client_id: apiClient.clientId,
          client_secret: clientSecret,
          grant_type: 'client_credentials',
          notUsed: 'notUsed',
        },
      });
      expect(apiResponse.status()).to.eq(200);
    });

    it('should request the endpoint /access_token with method POST with valid data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTValidData', baseContext);

      const apiResponse = await apiContext.post('access_token', {
        form: {
          client_id: apiClient.clientId,
          client_secret: clientSecret,
          grant_type: 'client_credentials',
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('token_type');
      expect(jsonResponse.token_type).to.be.eq('Bearer');
      expect(jsonResponse).to.have.property('expires_in');
      expect(jsonResponse.expires_in).to.be.eq(apiClient.tokenLifetime);
      expect(jsonResponse).to.have.property('access_token');
      expect(jsonResponse.token_type).to.be.a('string');
    });
  });

  deleteAPIClientTest(`${baseContext}_postTest_0`, apiClient.clientId);
});
