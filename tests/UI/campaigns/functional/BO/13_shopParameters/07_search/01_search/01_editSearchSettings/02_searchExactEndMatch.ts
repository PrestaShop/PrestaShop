// Import utils
import testContext from '@utils/testContext';

// Import login steps
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boSearchPage,
  type BrowserContext,
  dataProducts,
  FakerProduct,
  foClassicHomePage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_search_search_editSearchSettings_searchExactEndMatch';

describe('BO - Shop Parameters - Search : Search exact end match', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: FakerProduct = new FakerProduct({
    name: 'My note',
    type: 'standard',
    status: true,
  });

  // Pre-condition : Create product
  createProductTest(productData, `${baseContext}_preTest_0`);

  describe('Search exact end match', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Shop Parameters > Search\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPageWoFuzzy', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.searchLink,
      );

      const pageTitle = await boSearchPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSearchPage.pageTitle);

      const statusSearchExactEndMatch = await boSearchPage.getSearchExactEndMatchStatus(page);
      expect(statusSearchExactEndMatch).to.be.eq(false);
    });

    [
      {
        verb: 'enable',
        numResults: 1,
        results: [
          productData.name,
        ],
      },
      {
        verb: 'disable',
        numResults: 4,
        results: [
          dataProducts.demo_8.name,
          dataProducts.demo_9.name,
          dataProducts.demo_10.name,
          productData.name,
        ],
      },
    ].forEach((arg: {verb: string, numResults: number, results: string[]}, index: number) => {
      it(`should ${arg.verb} the Search exact end match`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${arg.verb}SearchExactEndMatch`, baseContext);

        const textResult = await boSearchPage.setSearchExactEndMatch(page, arg.verb === 'enable');
        expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
      });

      it('should go to the Front Office', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFo${index}`, baseContext);

        page = await boSearchPage.viewMyShop(page);

        const pageTitle = await foClassicHomePage.getPageTitle(page);
        expect(pageTitle).to.be.eq(foClassicHomePage.pageTitle);
      });

      it('should check the search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkSearchPage${index}`, baseContext);

        await foClassicHomePage.searchProduct(page, 'note');

        const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

        const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
        expect(searchInputValue).to.be.equal('note');

        const hasResults = await foClassicSearchResultsPage.hasResults(page);
        expect(hasResults).to.eq(true);

        const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
        expect(numResults).to.eq(arg.numResults);

        const titleTable = await foClassicSearchResultsPage.getAllProductsAttribute(page, 'title');
        expect(titleTable).to.deep.equal(arg.results);
      });

      it('should close the FO page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `closeFoAndGoBackToBO${index}`, baseContext);

        page = await foClassicSearchResultsPage.closePage(browserContext, page, 0);

        const pageTitle = await boSearchPage.getPageTitle(page);
        expect(pageTitle).to.contains(boSearchPage.pageTitle);
      });
    });
  });

  // Post-condition : Delete the created product
  deleteProductTest(productData, `${baseContext}_postTest_0`);
});
