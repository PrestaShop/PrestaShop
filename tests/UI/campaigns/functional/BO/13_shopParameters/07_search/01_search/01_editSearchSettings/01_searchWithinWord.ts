import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boSearchPage,
  type BrowserContext,
  dataProducts,
  foClassicHomePage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_search_search_editSearchSettings_searchWithinWord';

describe('BO - Shop Parameters - Search : Search within word', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
      verb: 'disable',
      numResults: 3,
      results: [
        dataProducts.demo_6.name,
        dataProducts.demo_5.name,
        dataProducts.demo_7.name,
      ],
    },
    {
      verb: 'enable',
      numResults: 3,
      results: [
        dataProducts.demo_8.name,
        dataProducts.demo_9.name,
        dataProducts.demo_10.name,
      ],
    },
  ].forEach((arg: {verb: string, numResults: number, results: string[]}, index: number) => {
    it(`should ${arg.verb} the Search within word`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${arg.verb}SearchWithinWord`, baseContext);

      const textResult = await boSearchPage.setSearchWithinWord(page, arg.verb === 'enable');
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

      await foClassicHomePage.searchProduct(page, 'book');

      const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

      const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
      expect(searchInputValue).to.be.equal('book');

      const hasResults = await foClassicSearchResultsPage.hasResults(page);
      expect(hasResults).to.eq(true);

      const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
      expect(numResults).to.eq(arg.numResults);

      const titleTable = await foClassicSearchResultsPage.getAllProductsAttribute(page, 'title');
      expect(titleTable.length).to.equals(arg.numResults);
      for (let nthTable = 0; nthTable < titleTable.length; nthTable++) {
        expect(arg.results[nthTable]).to.contains(titleTable[nthTable].replace('...', ''));
      }
    });

    it('should close the FO page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closeFoAndGoBackToBO${index}`, baseContext);

      page = await foClassicSearchResultsPage.closePage(browserContext, page, 0);

      const pageTitle = await boSearchPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSearchPage.pageTitle);
    });
  });

  it('should reset the Search within word', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetSearchWithinWord', baseContext);

    const textResult = await boSearchPage.setSearchWithinWord(page, false);
    expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
  });
});
