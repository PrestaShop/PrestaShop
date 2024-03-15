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
import dashboardPage from '@pages/BO/dashboard';

// Import data
import APIClientData from '@data/faker/APIClient';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_endpoints_apiClient_postAPIApiClient';

describe('API : POST /api-client', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let clientSecret: string;
  let jsonResponse: any;

  const clientScope: string = 'api_client_write';
  const clientData: APIClientData = new APIClientData({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });
  const createClient: APIClientData = new APIClientData({
    scopes: [
      'api_client_read',
      'hook_read',
    ],
  });

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    apiContext = await helper.createAPIContext(global.API.URL);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

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

      const textResult = await addNewApiClientPage.addAPIClient(page, clientData);
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

    it('should request the endpoint /access_token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      const apiResponse = await apiContext.post('access_token', {
        form: {
          client_id: clientData.clientId,
          client_secret: clientSecret,
          grant_type: 'client_credentials',
          scope: clientScope,
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

  describe('API : Create the API Access', async () => {
    it('should request the endpoint /api-client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.post('api-client', {
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
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

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

    it('should check there are 2 records', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkRecords', baseContext);

      const numRecords = await apiClientPage.getNumberOfElementInGrid(page);
      expect(numRecords).to.equal(2);
    });

    it('should check the JSON Response : `apiClientId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseApiClientId', baseContext);

      const value = parseInt(await apiClientPage.getTextColumn(page, 'id_api_client', 2), 10);
      expect(value).to.equal(jsonResponse.apiClientId);
    });

    it('should check the JSON Response : `clientId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseClientId', baseContext);

      const value = await apiClientPage.getTextColumn(page, 'client_id', 2);
      expect(value).to.equal(createClient.clientId);
    });

    it('should check the JSON Response : `clientName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseClientName', baseContext);

      const value = await apiClientPage.getTextColumn(page, 'client_name', 2);
      expect(value).to.equal(createClient.clientName);
    });

    it('should go to the edit page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAPIClientPage', baseContext);

      await apiClientPage.goToEditAPIClientPage(page, 2);

      const pageTitle = await addNewApiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiClientPage.pageTitleEdit(createClient.clientName));
    });

    it('should check the JSON Response : `description`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescription', baseContext);

      const value = await addNewApiClientPage.getValue(page, 'description');
      expect(value).to.equal(createClient.description);
    });

    it('should check the JSON Response : `enabled`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseEnabled', baseContext);

      const value = await addNewApiClientPage.isEnabled(page);
      expect(value).to.equal(createClient.enabled);
    });

    it('should check the JSON Response : `lifetime`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLifetime', baseContext);

      const value = parseInt(await addNewApiClientPage.getValue(page, 'tokenLifetime'), 10);
      expect(value).to.equal(createClient.tokenLifetime);
    });

    it('should check the JSON Response : `scopes`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseScopes', baseContext);

      const value = await addNewApiClientPage.getApiScopes(page, 'ps_apiresources', true);
      expect(value).to.deep.equal(createClient.scopes);
    });
  });
  describe('BackOffice : Delete the API Access', async () => {
    it('should return to the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToList', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );

      const pageTitle = await apiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(apiClientPage.pageTitle);

      const numElements = await apiClientPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(2);
    });

    it('should delete API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAPIClient', baseContext);

      const textResult = await apiClientPage.deleteAPIClient(page, 2);
      expect(textResult).to.equal(addNewApiClientPage.successfulDeleteMessage);

      const numElements = await apiClientPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });
  });

  // Pre-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
