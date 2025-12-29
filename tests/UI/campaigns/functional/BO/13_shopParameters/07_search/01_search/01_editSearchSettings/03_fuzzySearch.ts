// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boSearchPage,
  type BrowserContext,
  foClassicHomePage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_search_search_editSearchSettings_fuzzySearch';

describe('BO - Shop Parameters - Search : Fuzzy search', async () => {
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
  });

  it('should disable the Fuzzy Search', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableFuzzySearch', baseContext);

    const textResult = await boSearchPage.setFuzzySearch(page, false);
    expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
  });

  it('should go to the Front Office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoWoFuzzy', baseContext);

    await boSearchPage.goToFo(page);

    const pageTitle = await foClassicHomePage.getPageTitle(page);
    expect(pageTitle).to.be.eq(foClassicHomePage.pageTitle);
  });

  it('should check the autocomplete', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAutocompleteWoFuzzy', baseContext);

    const hasSearchResult = await foClassicHomePage.hasAutocompleteSearchResult(page, 'test');
    expect(hasSearchResult).to.eq(false);

    const inputValue = await foClassicHomePage.getSearchValue(page);
    expect(inputValue).equal('test');
  });

  it('should check the search page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSearchPageWoFuzzy', baseContext);

    await foClassicHomePage.searchProduct(page, 'test');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(false);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal('test');
  });

  it('should go to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBo', baseContext);

    await foClassicSearchResultsPage.goToBO(page);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Shop Parameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPageWFuzzy', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.searchLink,
    );

    const pageTitle = await boSearchPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSearchPage.pageTitle);
  });

  it('should disable the Fuzzy Search', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableFuzzySearch', baseContext);

    const textResult = await boSearchPage.setFuzzySearch(page, true);
    expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
  });

  it('should go to the Front Office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoWFuzzy', baseContext);

    await boSearchPage.goToFo(page);

    const pageTitle = await foClassicHomePage.getPageTitle(page);
    expect(pageTitle).to.be.eq(foClassicHomePage.pageTitle);
  });

  it('should check the autocomplete', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAutocompleteWFuzzy', baseContext);

    const hasSearchResult = await foClassicHomePage.hasAutocompleteSearchResult(page, 'test');
    expect(hasSearchResult).to.eq(true);

    const countSearchResult = await foClassicHomePage.countAutocompleteSearchResult(page, 'test');
    expect(countSearchResult).to.be.eq(7);

    const inputValue = await foClassicHomePage.getSearchValue(page);
    expect(inputValue).equal('test');
  });

  it('should check the search page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSearchPageWFuzzy', baseContext);

    await foClassicHomePage.searchProduct(page, 'test');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(true);

    const countResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
    expect(countResults).to.be.eq(7);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal('test');
  });
});
