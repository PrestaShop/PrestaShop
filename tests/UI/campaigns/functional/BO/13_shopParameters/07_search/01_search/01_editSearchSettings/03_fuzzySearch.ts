// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import searchPage from '@pages/BO/shopParameters/search';
// Import FO pages
import {homePage} from '@pages/FO/home';
import {searchResultsPage} from '@pages/FO/searchResults';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_search_search_editSearchSettings_fuzzySearch';

describe('BO - Shop Parameters - Search : Fuzzy search', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shop Parameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPageWoFuzzy', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.searchLink,
    );

    const pageTitle = await searchPage.getPageTitle(page);
    await expect(pageTitle).to.contains(searchPage.pageTitle);
  });

  it('should disable the Fuzzy Search', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableFuzzySearch', baseContext);

    const textResult = await searchPage.setFuzzySearch(page, false);
    await expect(textResult).to.be.eq(searchPage.settingsUpdateMessage);
  });

  it('should go to the Front Office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoWoFuzzy', baseContext);

    await searchPage.goToFo(page);

    const pageTitle = await homePage.getPageTitle(page);
    expect(pageTitle).to.be.eq(homePage.pageTitle);
  });

  it('should check the autocomplete', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAutocompleteWoFuzzy', baseContext);

    const hasSearchResult = await homePage.hasAutocompleteSearchResult(page, 'test');
    await expect(hasSearchResult).to.be.false;

    const inputValue = await homePage.getSearchValue(page);
    await expect(inputValue).equal('test');
  });

  it('should check the search page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSearchPageWoFuzzy', baseContext);

    await homePage.searchProduct(page, 'test');

    const pageTitle = await searchResultsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(searchResultsPage.pageTitle);

    const hasResults = await searchResultsPage.hasResults(page);
    await expect(hasResults).to.be.false;

    const searchInputValue = await searchResultsPage.getSearchValue(page);
    await expect(searchInputValue).to.be.equal('test');
  });

  it('should go to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBo', baseContext);

    await searchResultsPage.goToBO(page);

    const pageTitle = await dashboardPage.getPageTitle(page);
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
  });

  it('should go to \'Shop Parameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPageWFuzzy', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.searchLink,
    );

    const pageTitle = await searchPage.getPageTitle(page);
    await expect(pageTitle).to.contains(searchPage.pageTitle);
  });

  it('should disable the Fuzzy Search', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableFuzzySearch', baseContext);

    const textResult = await searchPage.setFuzzySearch(page, true);
    await expect(textResult).to.be.eq(searchPage.settingsUpdateMessage);
  });

  it('should go to the Front Office', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoWFuzzy', baseContext);

    await searchPage.goToFo(page);

    const pageTitle = await homePage.getPageTitle(page);
    expect(pageTitle).to.be.eq(homePage.pageTitle);
  });

  it('should check the autocomplete', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAutocompleteWFuzzy', baseContext);

    const hasSearchResult = await homePage.hasAutocompleteSearchResult(page, 'test');
    await expect(hasSearchResult).to.be.true;

    const countSearchResult = await homePage.countAutocompleteSearchResult(page, 'test');
    await expect(countSearchResult).to.be.eq(7);

    const inputValue = await homePage.getSearchValue(page);
    await expect(inputValue).equal('test');
  });

  it('should check the search page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSearchPageWFuzzy', baseContext);

    await homePage.searchProduct(page, 'test');

    const pageTitle = await searchResultsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(searchResultsPage.pageTitle);

    const hasResults = await searchResultsPage.hasResults(page);
    await expect(hasResults).to.be.true;

    const countResults = await searchResultsPage.getSearchResultsNumber(page);
    await expect(countResults).to.be.eq(7);

    const searchInputValue = await searchResultsPage.getSearchValue(page);
    await expect(searchInputValue).to.be.equal('test');
  });
});
