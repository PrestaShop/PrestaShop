// Import utils
import api from '@utils/api';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';

// Import pages
import apiClientPage from 'pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import APIClientData from '@data/faker/APIClient';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_endpoints_apiClient_patchAPIApiClientId';

describe('API : PATCH /api-client/{apiClientId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let clientSecret: string;
  let idApiClient: number;

  const clientScope: string = 'api_client_write';
  const clientData: APIClientData = new APIClientData({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });
  const createClient: APIClientData = new APIClientData({
    enabled: true,
    scopes: [
      'api_client_read',
      'hook_write',
    ],
  });
  const patchClient: APIClientData = new APIClientData({
    clientId: 'Client ID Patch',
    clientName: 'Client Name Patch',
    description: 'Description Patch',
    enabled: false,
    tokenLifetime: 1234,
    scopes: [
      'api_client_write',
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
    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToAuthorizationServerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );

      const pageTitle = await apiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(apiClientPage.pageTitle);

      const numRecords = await apiClientPage.getNumberOfElementInGrid(page);
      expect(numRecords).to.be.equal(1);
    });

    it('should go to add New API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIClientPageForPatch', baseContext);

      await apiClientPage.goToNewAPIClientPage(page);

      const pageTitle = await addNewApiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiClientPage.pageTitleCreate);
    });

    it('should create API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAPIClientForPatch', baseContext);

      const textResult = await addNewApiClientPage.addAPIClient(page, createClient);
      expect(textResult).to.contains(addNewApiClientPage.successfulCreationMessage);

      const textMessage = await addNewApiClientPage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(addNewApiClientPage.apiClientGeneratedMessage);
    });

    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToAuthorizationServerPageAfterCreate', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );

      const pageTitle = await apiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(apiClientPage.pageTitle);

      const numRecords = await apiClientPage.getNumberOfElementInGrid(page);
      expect(numRecords).to.be.equal(2);
    });

    it('should fetch the identifier of the API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fetchIDApiClient', baseContext);

      idApiClient = parseInt(await apiClientPage.getTextColumn(page, 'id_api_client', 2), 10);
      expect(idApiClient).to.be.gt(0);
    });

    it('should go to edit page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      await apiClientPage.goToEditAPIClientPage(page, 2);

      const pageTitle = await addNewApiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiClientPage.pageTitleEdit(createClient.clientName));
    });
  });

  [
    {
      propertyName: 'clientId',
      propertyValue: patchClient.clientId,
    },
    {
      propertyName: 'clientName',
      propertyValue: patchClient.clientName,
    },
    {
      propertyName: 'description',
      propertyValue: patchClient.description,
    },
    {
      propertyName: 'enabled',
      propertyValue: patchClient.enabled,
    },
    {
      propertyName: 'lifetime',
      propertyValue: patchClient.tokenLifetime,
    },
    {
      propertyName: 'scopes',
      propertyValue: patchClient.scopes,
    },
  ].forEach((data: { propertyName: string, propertyValue: boolean|number|string|string[] }) => {
    describe(`Update the property \`${data.propertyName}\` with API and check in BO`, async () => {
      it('should request the endpoint /api-client/{apiClientId}', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `requestEndpoint${data.propertyName}`, baseContext);

        const dataPatch: any = {};
        dataPatch[data.propertyName] = data.propertyValue;

        const apiResponse = await apiContext.patch(`api-client/${idApiClient}`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
          data: dataPatch,
        });
        expect(apiResponse.status()).to.eq(200);
        expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        const jsonResponse = await apiResponse.json();
        expect(jsonResponse).to.have.property(data.propertyName);
        expect(jsonResponse[data.propertyName]).to.deep.equal(data.propertyValue);
      });

      it(`should check that the property "${data.propertyName}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProperty${data.propertyName}`, baseContext);

        await addNewApiClientPage.reloadPage(page);

        if (['clientId', 'clientName', 'description'].includes(data.propertyName)) {
          const valueProperty = await addNewApiClientPage.getValue(page, data.propertyName);
          expect(valueProperty).to.equal(data.propertyValue);
        } else if (data.propertyName === 'lifetime') {
          const valueProperty = await addNewApiClientPage.getValue(page, 'tokenLifetime');
          expect(valueProperty).to.equal(data.propertyValue.toString());
        } else if (data.propertyName === 'enabled') {
          const valueProperty = await addNewApiClientPage.isEnabled(page);
          expect(valueProperty).to.equal(data.propertyValue);
        } else if (data.propertyName === 'scopes') {
          const valueProperty = await addNewApiClientPage.getApiScopes(page, 'ps_apiresources', true);
          expect(valueProperty).to.deep.equal(data.propertyValue);
        }
      });
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
