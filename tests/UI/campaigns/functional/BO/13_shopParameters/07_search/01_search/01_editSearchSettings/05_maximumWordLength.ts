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

const baseContext: string = 'functional_BO_shopParameters_search_search_editSearchSettings_maximumWordLength';

describe('BO - Shop Parameters - Search : Maximum word length (in characters)', async () => {
  const maximumWordLength: number = 15;

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
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.searchLink,
    );

    const pageTitle = await boSearchPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSearchPage.pageTitle);
  });

  it('should verify the maximum word length value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMaxWordLengthValue', baseContext);

    const value = await boSearchPage.getMaximumWordLength(page);
    expect(value).to.equal(maximumWordLength);
  });

  it('should go to the FrontOffice', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    page = await boSearchPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should search the word "hummingbird shirt"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchWordHummingbirdShirt15', baseContext);

    await foClassicHomePage.searchProduct(page, 'hummingbird shirt');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(true);

    const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
    expect(numResults).to.eq(1);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal('hummingbird shirt');
  });

  it('should set the maximum word length to 2', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setMaxWordLengthTo2', baseContext);

    page = await foClassicSearchResultsPage.changePage(browserContext, 0);
    const textResult = await boSearchPage.setMaximumWordLength(page, 2);
    expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
  });

  it('should search the word "hummingbird shirt"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchWordHummingbirdShirt2', baseContext);

    page = await boSearchPage.changePage(browserContext, 1);
    await foClassicHomePage.reloadPage(page);

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(true);

    const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
    expect(numResults).to.eq(2);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal('hummingbird shirt');
  });

  it('should set the maximum word length to 1', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setMaxWordLengthTo1', baseContext);

    page = await foClassicSearchResultsPage.changePage(browserContext, 0);
    const textResult = await boSearchPage.setMaximumWordLength(page, 1);
    expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
  });

  it('should search the word "hummingbird shirt"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchWordHummingbirdShirt1', baseContext);

    page = await boSearchPage.changePage(browserContext, 1);
    await foClassicHomePage.reloadPage(page);

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(true);

    const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
    expect(numResults).to.eq(13);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal('hummingbird shirt');
  });

  it('should set the maximum word length to 0', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setMaxWordLengthTo0', baseContext);

    page = await foClassicSearchResultsPage.changePage(browserContext, 0);
    const textResult = await boSearchPage.setMaximumWordLength(page, 0);
    expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
  });

  it('should search the word "hummingbird shirt"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchWordHummingbirdShirt0', baseContext);

    page = await boSearchPage.changePage(browserContext, 1);
    await foClassicHomePage.reloadPage(page);

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(true);

    const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
    expect(numResults).to.eq(19);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal('hummingbird shirt');
  });

  it('should set the maximum word length to ""', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setMaxWordLengthToEmpty', baseContext);

    page = await foClassicSearchResultsPage.changePage(browserContext, 0);
    const textResult = await boSearchPage.setMaximumWordLength(page, '');
    expect(textResult).to.contains(boSearchPage.errorFillFieldMessage);
  });

  it('should set the maximum word length to "vhgfud"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setMaxWordLengthToError', baseContext);

    page = await foClassicSearchResultsPage.changePage(browserContext, 0);
    const textResult = await boSearchPage.setMaximumWordLength(page, 'vhgfud');
    expect(textResult).to.contains(boSearchPage.errorMaxWordLengthInvalidMessage);
  });

  it('should reset the maximum word length', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetMaximumWordLength', baseContext);

    const textResult = await boSearchPage.setMaximumWordLength(page, maximumWordLength);
    expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
  });
});
