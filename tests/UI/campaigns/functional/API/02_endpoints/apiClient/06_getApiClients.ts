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
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_apiClient_getApiClients';

describe('API : GET /api-clients', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;

  const clientScope: string = 'api_client_read';

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

  describe('API : Fetch Data', async () => {
    it('should request the endpoint /api-clients', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('api-clients', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);
      expect(jsonResponse).to.have.all.keys(
        'totalItems',
        'orderBy',
        'sortOrder',
        'limit',
        'offset',
        'filters',
        'items',
      );

      expect(jsonResponse.totalItems).to.be.gt(0);

      for (let i:number = 0; i < jsonResponse.totalItems; i++) {
        expect(jsonResponse.items[i]).to.have.all.keys(
          'apiClientId',
          'clientId',
          'clientName',
          'description',
          'externalIssuer',
          'enabled',
          'lifetime',
        );
      }
    });
  });

  describe('BackOffice : Expected data', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponse.totalItems; idxItem++) {
        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.advancedParametersLink,
          boDashboardPage.adminAPILink,
        );

        const pageTitle = await boApiClientsPage.getPageTitle(page);
        expect(pageTitle).to.eq(boApiClientsPage.pageTitle);

        const numAPIClients = await boApiClientsPage.getNumberOfElementInGrid(page);
        expect(numAPIClients).to.be.equal(1);

        const apiClientId = parseInt((await boApiClientsPage.getTextColumn(page, 'id_api_client', idxItem + 1)).toString(), 10);
        expect(apiClientId).to.equal(jsonResponse.items[idxItem].apiClientId);

        const clientId = await boApiClientsPage.getTextColumn(page, 'client_id', idxItem + 1);
        expect(clientId).to.equal(jsonResponse.items[idxItem].clientId);

        const clientName = await boApiClientsPage.getTextColumn(page, 'client_name', idxItem + 1);
        expect(clientName).to.equal(jsonResponse.items[idxItem].clientName);

        const externalIssuer = await boApiClientsPage.getTextColumn(page, 'external_issuer', idxItem + 1);
        expect(externalIssuer).to.equal(
          jsonResponse.items[idxItem].externalIssuer === null
            ? ''
            : jsonResponse.items[idxItem].externalIssuer,
        );

        const enabled = await boApiClientsPage.getStatus(page, idxItem + 1);
        expect(enabled).to.equal(jsonResponse.items[idxItem].enabled);

        await boApiClientsPage.goToEditAPIClientPage(page, idxItem + 1);

        const pageTitleEdit = await boApiClientsCreatePage.getPageTitle(page);
        expect(pageTitleEdit).to.eq(boApiClientsCreatePage.pageTitleEdit(jsonResponse.items[idxItem].clientName));

        const description = await boApiClientsCreatePage.getValue(page, 'description');
        expect(description).to.equal(jsonResponse.items[idxItem].description);

        const lifetime = parseInt(await boApiClientsCreatePage.getValue(page, 'tokenLifetime'), 10);
        expect(lifetime).to.equal(jsonResponse.items[idxItem].lifetime);
      }
    });
  });
});
