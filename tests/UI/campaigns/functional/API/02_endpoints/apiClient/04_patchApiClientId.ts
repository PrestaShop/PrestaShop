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

const baseContext: string = 'functional_API_endpoints_apiClient_patchApiClientId';

describe('API : PATCH /api-clients/{apiClientId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let idApiClient: number;

  const clientScope: string = 'api_client_write';
  const patchClient: FakerAPIClient = new FakerAPIClient({
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

      const numRecords = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numRecords).to.equal(1);
    });

    it('should go to add New API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIClientPageForPatch', baseContext);

      await boApiClientsPage.goToNewAPIClientPage(page);

      const pageTitle = await boApiClientsCreatePage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsCreatePage.pageTitleCreate);
    });

    it('should create API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAPIClientForPatch', baseContext);

      const textResult = await boApiClientsCreatePage.addAPIClient(page, patchClient);
      expect(textResult).to.contains(boApiClientsCreatePage.successfulCreationMessage);

      const textMessage = await boApiClientsCreatePage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(boApiClientsCreatePage.apiClientGeneratedMessage);
    });

    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToAdminAPIPageAfterCreate', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );

      const pageTitle = await boApiClientsPage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsPage.pageTitle);

      const numRecords = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numRecords).to.be.equal(2);
    });

    it('should fetch the identifier of the API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fetchIDApiClient', baseContext);

      // Get ID from last created API Client
      const apiClientsNumber = await boApiClientsPage.getNumberOfElementInGrid(page);
      idApiClient = parseInt(await boApiClientsPage.getTextColumn(page, 'id_api_client', apiClientsNumber), 10);
      expect(idApiClient).to.be.gt(0);
    });

    it('should go to edit page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      // Go to edit page for the last created API client (the one used for patch tests)
      const apiClientsNumber = await boApiClientsPage.getNumberOfElementInGrid(page);
      await boApiClientsPage.goToEditAPIClientPage(page, apiClientsNumber);

      const pageTitle = await boApiClientsCreatePage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsCreatePage.pageTitleEdit(patchClient.clientName));
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
      it('should request the endpoint /api-clients/{apiClientId}', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `requestEndpoint${data.propertyName}`, baseContext);

        const dataPatch: any = {};
        dataPatch[data.propertyName] = data.propertyValue;

        const apiResponse = await apiContext.patch(`api-clients/${idApiClient}`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
          data: dataPatch,
        });
        expect(apiResponse.status()).to.eq(200);
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        const jsonResponse = await apiResponse.json();
        expect(jsonResponse).to.have.property(data.propertyName);
        expect(jsonResponse[data.propertyName]).to.deep.equal(data.propertyValue);
      });

      it(`should check that the property "${data.propertyName}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProperty${data.propertyName}`, baseContext);

        await boApiClientsCreatePage.reloadPage(page);

        if (['clientId', 'clientName', 'description'].includes(data.propertyName)) {
          const valueProperty = await boApiClientsCreatePage.getValue(page, data.propertyName);
          expect(valueProperty).to.equal(data.propertyValue);
        } else if (data.propertyName === 'lifetime') {
          const valueProperty = await boApiClientsCreatePage.getValue(page, 'tokenLifetime');
          expect(valueProperty).to.equal(data.propertyValue.toString());
        } else if (data.propertyName === 'enabled') {
          const valueProperty = await boApiClientsCreatePage.isEnabled(page);
          expect(valueProperty).to.equal(data.propertyValue);
        } else if (data.propertyName === 'scopes') {
          const valueProperty = await boApiClientsCreatePage.getApiScopes(page, 'ps_apiresources', true);
          expect(valueProperty).to.deep.equal(data.propertyValue);
        }
      });
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

      let numElements = await boApiClientsPage.getNumberOfElementInGrid(page);
      // Delete the last created API Client (the one used for patch)
      const textResult = await boApiClientsPage.deleteAPIClient(page, numElements);
      expect(textResult).to.equal(boApiClientsCreatePage.successfulDeleteMessage);

      numElements = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(1);
    });
  });
});
