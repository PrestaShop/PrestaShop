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

const baseContext: string = 'functional_API_endpoints_apiClient_getApiClientId';

describe('API : GET /api-client/{apiClientId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let clientSecret: string;
  let idApiClient: number;

  const clientScope: string = 'api_client_read';
  const clientData: APIClientData = new APIClientData({
    enabled: true,
    scopes: [
      clientScope,
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdminAPIPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.adminAPILink,
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

  describe('BackOffice : Expected data', async () => {
    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToAdminAPIPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.adminAPILink,
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
    it('should request the endpoint /api-client/{apiClientId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`api-client/${idApiClient}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);

      expect(jsonResponse).to.have.all.keys(
        'apiClientId',
        'clientId',
        'clientName',
        'description',
        'externalIssuer',
        'enabled',
        'lifetime',
        'scopes',
      );
    });

    it('should check the JSON Response : `apiClientId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseApiClientId', baseContext);

      expect(jsonResponse).to.have.property('apiClientId');
      expect(jsonResponse.apiClientId).to.be.a('number');
      expect(jsonResponse.apiClientId).to.be.equal(idApiClient);
    });

    it('should check the JSON Response : `clientId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseClientId', baseContext);

      expect(jsonResponse).to.have.property('clientId');
      expect(jsonResponse.clientId).to.be.a('string');
      expect(jsonResponse.clientId).to.be.equal(clientData.clientId);
    });

    it('should check the JSON Response : `clientName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseClientName', baseContext);

      expect(jsonResponse).to.have.property('clientName');
      expect(jsonResponse.clientName).to.be.a('string');
      expect(jsonResponse.clientName).to.be.equal(clientData.clientName);
    });

    it('should check the JSON Response : `description`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescription', baseContext);

      expect(jsonResponse).to.have.property('description');
      expect(jsonResponse.description).to.be.a('string');
      expect(jsonResponse.description).to.be.equal(clientData.description);
    });

    it('should check the JSON Response : `externalIssuer`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseExternalIssuer', baseContext);

      expect(jsonResponse).to.have.property('externalIssuer');
      expect(jsonResponse.externalIssuer).to.equal(null);
    });

    it('should check the JSON Response : `enabled`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseEnabled', baseContext);

      expect(jsonResponse).to.have.property('enabled');
      expect(jsonResponse.enabled).to.be.a('boolean');
      expect(jsonResponse.enabled).to.be.equal(clientData.enabled);
    });

    it('should check the JSON Response : `lifetime`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLifetime', baseContext);

      expect(jsonResponse).to.have.property('lifetime');
      expect(jsonResponse.lifetime).to.be.a('number');
      expect(jsonResponse.lifetime).to.be.equal(clientData.tokenLifetime);
    });

    it('should check the JSON Response : `scopes`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseScopes', baseContext);

      expect(jsonResponse).to.have.property('scopes');
      expect(jsonResponse.scopes).to.be.a('array');
      expect(jsonResponse.scopes).to.deep.equal(clientData.scopes);
    });
  });

  // Post-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
