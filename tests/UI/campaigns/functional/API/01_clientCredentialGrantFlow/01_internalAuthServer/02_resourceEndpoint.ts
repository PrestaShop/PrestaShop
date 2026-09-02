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

const baseContext: string = 'functional_API_clientCredentialGrantFlow_internalAuthServer_resourceEndpoint';

describe('API : Internal Auth Server - Resource Endpoint', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let accessTokenExpired: string;
  let clientSecret: string;

  const apiClient: FakerAPIClient = new FakerAPIClient({
    enabled: true,
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

  describe('Resource Endpoint', async () => {
    it('should request the authorization endpoint and fetch valid token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthorizationEndpoint', baseContext);

      const apiResponse = await apiContext.post('access_token', {
        form: {
          client_id: apiClient.clientId,
          client_secret: clientSecret,
          grant_type: 'client_credentials',
          scope: 'hook_read',
        },
      });
      expect(apiResponse.status()).to.eq(200);

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('access_token');
      expect(jsonResponse.access_token).to.be.a('string');

      accessToken = jsonResponse.access_token;
      accessTokenExpired = utilsAPI.setAccessTokenAsExpired(accessToken);
    });

    it('should request the endpoint /hooks/1 without access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithoutAccessToken', baseContext);

      const apiResponse = await apiContext.get('hooks/1');
      expect(apiResponse.status()).to.eq(401);
    });

    it('should request the endpoint /hooks/1 with invalid access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithInvalidAccessToken', baseContext);

      const apiResponse = await apiContext.get('hooks/1', {
        headers: {
          Authorization: 'Bearer INVALIDTOKEN',
        },
      });
      expect(apiResponse.status()).to.eq(401);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /hooks/1 with expired access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithExpiredAccessToken', baseContext);

      const apiResponse = await apiContext.get('hooks/1', {
        headers: {
          Authorization: `Bearer ${accessTokenExpired}`,
        },
      });
      expect(apiResponse.status()).to.eq(401);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /hooks/1 with valid access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithValidAccessToken', baseContext);

      const apiResponse = await apiContext.get('hooks/1', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });

      expect(apiResponse.status()).to.eq(200);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('hookId');
      expect(jsonResponse.hookId).to.be.a('number');
      expect(jsonResponse).to.have.property('enabled');
      expect(jsonResponse.enabled).to.be.a('boolean');
    });
  });

  deleteAPIClientTest(`${baseContext}_postTest_0`, apiClient.clientId);
});
