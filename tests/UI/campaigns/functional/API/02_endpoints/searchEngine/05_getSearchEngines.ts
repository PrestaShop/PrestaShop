import {expect} from 'chai';
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {
  type APIRequestContext,
  boDashboardPage,
  boLoginPage,
  boSearchEnginesPage,
  boSeoUrlsPage,
  type BrowserContext,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';
import testContext from '@utils/testContext';

const baseContext: string = 'functional_API_endpoints_searchEngine_getSearchEngines';

describe('API : GET /admin-api/search-engines', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;

  const clientScope: string = 'search_engine_read';

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
    it('should request the endpoint /search-engines', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('search-engines', {
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
        'filters',
        'items',
      );

      expect(jsonResponse.totalItems).to.be.gt(0);

      for (let i:number = 0; i < jsonResponse.totalItems; i++) {
        expect(jsonResponse.items[i]).to.have.all.keys(
          'queryKey',
          'searchEngineId',
          'server',
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

    it('should go to \'Shop Parameters > Traffic & SEO\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSeoAndUrlsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.trafficAndSeoLink,
      );

      const pageTitle = await boSeoUrlsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSeoUrlsPage.pageTitle);
    });

    it('should go to \'Search Engines\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSearchEnginesPage', baseContext);

      await boSeoUrlsPage.goToSearchEnginesPage(page);

      const pageTitle = await boSearchEnginesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSearchEnginesPage.pageTitle);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponse.totalItems; idxItem++) {
        await boSearchEnginesPage.resetAndGetNumberOfLines(page);

        await boSearchEnginesPage.filterTable(page, 'id_search_engine', jsonResponse.items[idxItem].searchEngineId.toString());

        const numSearchEngines = await boSearchEnginesPage.getNumberOfElementInGrid(page);
        expect(numSearchEngines).to.be.equal(1);

        const searchEngineId = parseInt(await boSearchEnginesPage.getTextColumn(page, 1, 'id_search_engine'), 10);
        expect(searchEngineId).to.equal(jsonResponse.items[idxItem].searchEngineId);

        const queryKey = await boSearchEnginesPage.getTextColumn(page, 1, 'query_key');
        expect(queryKey).to.equal(jsonResponse.items[idxItem].queryKey);

        const server = await boSearchEnginesPage.getTextColumn(page, 1, 'server');
        expect(server).to.equal(jsonResponse.items[idxItem].server);
      }
    });
  });
});
