import testContext from '@utils/testContext';
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {
  type APIRequestContext,
  boDashboardPage,
  boLoginPage,
  boSearchAliasCreatePage,
  boSearchAliasPage,
  boSearchPage,
  type BrowserContext,
  FakerSearchAlias,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_API_endpoints_searchAlias_deleteSearch';

describe.skip('API : DELETE /admin-api/search-aliases/{search}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSearchAliases: number;
  let accessToken: string;

  const clientScope: string = 'search_alias_write';
  const createSearchAlias: FakerSearchAlias = new FakerSearchAlias();

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

  describe('BackOffice : Create a Search Alias', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Shop Parameters > Search\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.searchLink,
      );

      const pageTitle = await boSearchPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSearchPage.pageTitle);
    });

    it('should go to \'Aliases\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAliasesTab', baseContext);

      await boSearchPage.goToAliasesPage(page);

      const pageTitle = await boSearchAliasPage.getPageTitle(page);
      expect(pageTitle).to.equals(boSearchAliasPage.pageTitle);
    });

    it('should reset all filters and get number of alias in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfSearchAliases = await boSearchAliasPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchAliases).to.be.above(0);
    });

    it('should go to add new search page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddAliasPage', baseContext);

      await boSearchAliasPage.goToAddNewAliasPage(page);

      const pageTitle = await boSearchAliasCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boSearchAliasCreatePage.pageTitleCreate);
    });

    it('should create alias and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAlias', baseContext);

      const textResult = await boSearchAliasCreatePage.setAlias(page, createSearchAlias);
      expect(textResult).to.contains(boSearchAliasPage.successfulCreationMessage);

      const numberOfElementAfterCreation = await boSearchAliasPage.getNumberOfElementInGrid(page);
      expect(numberOfElementAfterCreation).to.be.equal(numberOfSearchAliases + 1);
    });
  });

  describe('API : Delete the Search Alias', async () => {
    it('should request the endpoint /admin-api/search-aliases/{search}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.delete('search-aliases', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          searchTerm: createSearchAlias.search,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check the Search Alias is deleted', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterDeletion', baseContext);

      await boSearchAliasPage.reloadPage(page);

      const numberOfSearchAliasesAfterDelete = await boSearchAliasPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchAliasesAfterDelete).to.be.equal(numberOfSearchAliases);
    });
  });
});
