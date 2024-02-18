// Import utils
import api from '@utils/api';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import apiClientPage from 'pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import APIClientData from '@data/faker/APIClient';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_endpoints_apiClient_getAPIApiClientId';

describe('API : GET /api/api-client/{apiClientId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let clientSecret: string;
  let idApiClient: number;

  const clientClient: APIClientData = new APIClientData({
    enabled: true,
    scopes: [
      'hook_write',
      'api_client_read',
    ],
  });

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    apiContext = await helper.createAPIContext(global.BO.URL);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition: Enable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, true, `${baseContext}_enableAuthorizationServer`);

  describe('BackOffice : Fetch the access token', async () => {
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

      const textResult = await addNewApiClientPage.addAPIClient(page, clientClient);
      expect(textResult).to.contains(addNewApiClientPage.successfulCreationMessage);

      const textMessage = await addNewApiClientPage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(addNewApiClientPage.apiClientGeneratedMessage);
    });

    it('should copy client secret', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'copyClientSecret', baseContext);

      await addNewApiClientPage.copyClientSecret(page);

      clientSecret = await addNewApiClientPage.getClipboardText(page);
      expect(clientSecret.length).to.be.gt(0);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      const apiResponse = await apiContext.post('api/oauth2/token', {
        form: {
          client_id: clientClient.clientId,
          client_secret: clientSecret,
          grant_type: 'client_credentials',
          scope: 'api_client_read',
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('access_token');
      expect(jsonResponse.token_type).to.be.a('string');

      accessToken = jsonResponse.access_token;
    });
  });

  describe('BackOffice : Expected data', async () => {
    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToAuthorizationServerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );

      const pageTitle = await apiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(apiClientPage.pageTitle);
    });

    it('should get informations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getInformations', baseContext);

      idApiClient = parseInt(await apiClientPage.getTextColumn(page, 'id_api_client', 1), 10);
      expect(idApiClient).to.be.gt(0);
    });
  });

  describe('API : Check Data', async () => {
    it('should request the endpoint /admin-dev/api/api-client/{apiClientId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`api/api-client/${idApiClient}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response : `apiClientId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseApiClientId', baseContext);

      expect(jsonResponse).to.have.property('apiClientId');
      expect(jsonResponse.apiClientId).to.be.a('number');
      expect(jsonResponse.apiClientId).to.be.equal(idApiClient);
    });

    it('should check the JSON Response : `clientName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseClientName', baseContext);

      expect(jsonResponse).to.have.property('clientName');
      expect(jsonResponse.clientName).to.be.a('string');
      expect(jsonResponse.clientName).to.be.equal(clientClient.clientName);
    });

    it('should check the JSON Response : `description`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescription', baseContext);

      expect(jsonResponse).to.have.property('description');
      expect(jsonResponse.description).to.be.a('string');
      expect(jsonResponse.description).to.be.equal(clientClient.description);
    });

    it('should check the JSON Response : `enabled`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseEnabled', baseContext);

      expect(jsonResponse).to.have.property('enabled');
      expect(jsonResponse.enabled).to.be.a('boolean');
      expect(jsonResponse.enabled).to.be.equal(clientClient.enabled);
    });
  });

  // Pre-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);

  // Post-condition: Disable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, false, `${baseContext}_disableAuthorizationServer`);
});
