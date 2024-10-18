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

const baseContext: string = 'functional_BO_shopParameters_search_search_editSearchSettings_minimumWordLength';

describe('BO - Shop Parameters - Search : Minimum word length (in characters)', async () => {
  const minimumWordLength: number = 3;

  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.searchLink,
    );

    const pageTitle = await boSearchPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSearchPage.pageTitle);
  });

  it('should verify the minimum word length value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMinWordLengthValue', baseContext);

    const value = await boSearchPage.getMinimumWordLength(page);
    expect(value).to.equal(minimumWordLength);
  });

  it('should go to the FrontOffice', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    page = await boSearchPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should search the word "Pack"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchWordPack', baseContext);

    await foClassicHomePage.searchProduct(page, 'Pack');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(true);

    const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
    expect(numResults).to.eq(1);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal('Pack');
  });

  it('should search the word "Pac"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchWordPac', baseContext);

    await foClassicSearchResultsPage.searchProduct(page, 'Pac');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(true);

    const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
    expect(numResults).to.eq(1);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal('Pac');
  });

  it('should search the word "Pa"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchWordPa', baseContext);

    await foClassicSearchResultsPage.searchProduct(page, 'Pa');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(false);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal('Pa');
  });

  it('should set the minimum word length to 5', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setMinWordLengthTo5', baseContext);

    page = await foClassicSearchResultsPage.changePage(browserContext, 0);
    const textResult = await boSearchPage.setMinimumWordLength(page, 5);
    expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
  });

  it('should search the word "noteb"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchWordNoteb', baseContext);

    page = await boSearchPage.changePage(browserContext, 1);
    await foClassicHomePage.searchProduct(page, 'noteb');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(true);

    const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
    expect(numResults).to.eq(3);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal('noteb');
  });

  it('should search the word "note"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchWordNote', baseContext);

    page = await boSearchPage.changePage(browserContext, 1);
    await foClassicHomePage.searchProduct(page, 'note');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(false);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal('note');
  });

  it('should reset the minimum word length', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetMinimumWordLength', baseContext);

    page = await foClassicSearchResultsPage.changePage(browserContext, 0);

    const textResult = await boSearchPage.setMinimumWordLength(page, minimumWordLength);
    expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
  });
});
