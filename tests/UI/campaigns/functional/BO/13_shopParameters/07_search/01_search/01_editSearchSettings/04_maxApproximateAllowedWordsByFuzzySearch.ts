// Import utils
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

const baseContext: string = 'functional_BO_shopParameters_search_search_editSearchSettings'
  + '_maxApproximateAllowedWordsByFuzzySearch';

describe('BO - Shop Parameters - Search : Maximum approximate words allowed by fuzzy search', async () => {
  const maximumApproximateWords: number = 4;

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

  it('should verify the maximum approximate words value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMaxApproximateWordsValue', baseContext);

    const value = await boSearchPage.getMaximumApproximateWords(page);
    expect(value).to.equal(maximumApproximateWords);
  });

  it('should update the maximum approximate words value: "3"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setMaxApproximateWordsValue', baseContext);

    const textResult = await boSearchPage.setMaximumApproximateWords(page, 3);
    expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
  });

  it('should go to the FrontOffice', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    page = await boSearchPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should search the word "notenook"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchWordnotenook', baseContext);

    await foClassicHomePage.searchProduct(page, 'notenook');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(true);

    const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
    expect(numResults).to.eq(3);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal('notenook');

    const titleTable = await foClassicSearchResultsPage.getAllProductsAttribute(page, 'title');
    expect(titleTable).to.deep.equal([
      dataProducts.demo_8.name,
      dataProducts.demo_9.name,
      dataProducts.demo_10.name,
    ]);
  });

  it('should search the word "briow beer"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchWordbriowbeer', baseContext);

    await foClassicSearchResultsPage.searchProduct(page, 'briow beer');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(true);

    const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
    expect(numResults).to.eq(3);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal('briow beer');

    const titleTable = await foClassicSearchResultsPage.getAllProductsAttribute(page, 'title');
    expect(titleTable).to.deep.equal([
      dataProducts.demo_16.name,
      dataProducts.demo_19.name,
      dataProducts.demo_9.name,
    ]);
  });

  it('should set the minimum word length to 5', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setMinWordLengthTo5', baseContext);

    page = await foClassicSearchResultsPage.changePage(browserContext, 0);
    const textResult = await boSearchPage.setMaximumApproximateWords(page, maximumApproximateWords);
    expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
  });
});
