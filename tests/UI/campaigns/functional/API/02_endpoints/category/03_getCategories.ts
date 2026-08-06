// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boCategoriesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_category_getCategories';

describe('API : GET /categories', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let numCategories: number = 0;
  let numCategoriesDisplayed: number = 0;
  const clientScope: string = 'category_read';

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('API : Fetch the access token', async () => {
    it('should request the endpoint /access_token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('API : Fetch Data', async () => {
    it('should request the endpoint /categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('categories', {
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
        'sortOrder',
        'limit',
        'orderBy',
        'filters',
        'items',
      );

      expect(jsonResponse.totalItems).to.be.gt(0);

      for (let i:number = 0; i < jsonResponse.totalItems; i++) {
        expect(jsonResponse.items[i]).to.have.all.keys(
          'categoryId',
          'enabled',
          'name',
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

    it('should go to \'Catalog > Categories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.categoriesLink,
      );

      const pageTitle = await boCategoriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
    });

    it('should filter by "Displayed": "Yes"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDisplayedYes', baseContext);

      // Filter by "Displayed": "Yes"
      await boCategoriesPage.resetFilter(page);
      numCategories = await boCategoriesPage.getNumberOfElementInGrid(page);

      await boCategoriesPage.filterCategories(page, 'select', 'active', '1');
      numCategoriesDisplayed = await boCategoriesPage.getNumberOfElementInGrid(page);
      // "Root" is removed because only displayed on multistore
      expect(numCategoriesDisplayed).to.be.equal(jsonResponse.totalItems - 1);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      expect(jsonResponse.totalItems).to.be.gt(0);
      for (let idxItem: number = 0; idxItem < jsonResponse.totalItems; idxItem++) {
        // eslint-disable-next-line no-loop-func
        if (jsonResponse.items[idxItem].name !== 'Root') {
          await boCategoriesPage.resetFilter(page);
          await boCategoriesPage.filterCategories(page, 'select', 'active', '1');
          await boCategoriesPage.filterCategories(page, 'input', 'id_category', jsonResponse.items[idxItem].categoryId);

          const numFilteredCategories = await boCategoriesPage.getNumberOfElementInGrid(page);
          expect(numFilteredCategories).to.be.equal(1);

          const categoryId = parseInt(
            (await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category')).toString(),
            10,
          );
          expect(categoryId).to.equal(jsonResponse.items[idxItem].categoryId);

          const name = await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
          expect(name).to.equal(jsonResponse.items[idxItem].name);

          const enabled = await boCategoriesPage.getStatus(page, 1);
          expect(enabled).to.equal(jsonResponse.items[idxItem].enabled);
        }
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numCategoriesAfterReset = await boCategoriesPage.resetAndGetNumberOfLines(page);
      expect(numCategoriesAfterReset).to.be.equal(numCategories);
    });
  });
});
