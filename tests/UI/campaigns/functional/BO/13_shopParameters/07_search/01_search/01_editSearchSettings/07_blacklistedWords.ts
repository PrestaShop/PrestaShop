import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boSearchPage,
  type BrowserContext,
  dataLanguages,
  foClassicHomePage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_search_search_editSearchSettings_blacklistedWords';

describe('BO - Shop Parameters - Search : Blacklisted words', async () => {
  const searchWord: string = 'does';

  let browserContext: BrowserContext;
  let page: Page;
  let blacklistedWordsListEN: string;

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

  it('should verify that the Blacklisted words list is not empty', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBlacklistedWordsList', baseContext);

    blacklistedWordsListEN = await boSearchPage.getBlacklistedWords(page, dataLanguages.english.id);
    expect(blacklistedWordsListEN.length).to.be.gt(0);
    expect(blacklistedWordsListEN).to.contains(searchWord);

    const blacklistedWordsListFR = await boSearchPage.getBlacklistedWords(page, dataLanguages.french.id);
    expect(blacklistedWordsListFR.length).to.be.gt(0);
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogProducts', baseContext);

    await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
    await boProductsPage.closeSfToolBar(page);

    const pageTitle = await boProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsPage.pageTitle);
  });

  it('should create a standard product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickAddNewProduct', baseContext);

    const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
    expect(isModalVisible).to.eq(true);

    await boProductsPage.selectProductType(page, 'standard');
    await boProductsPage.clickOnAddNewProduct(page);

    const pageTitle = await boProductsCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
  });

  it('should set the title product and publish', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setTitleProduct', baseContext);

    await boProductsCreatePage.setProductName(page, `Test ${searchWord}`);
    await boProductsCreatePage.setProductStatus(page, true);

    const updateProductMessage = await boProductsCreatePage.saveProduct(page);
    expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
  });

  it('should go to the FrontOffice', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    page = await boProductsCreatePage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it(`should search the word "${searchWord}"`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchWordWithError', baseContext);

    await foClassicHomePage.searchProduct(page, searchWord);

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(false);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal(searchWord);
  });

  it('should return to \'Shop Parameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToSearchPage', baseContext);

    page = await foClassicSearchResultsPage.changePage(browserContext, 0);
    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.searchLink,
    );

    const pageTitle = await boSearchPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSearchPage.pageTitle);
  });

  it(`should remove the word ${searchWord} from the list`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'removeWordFromList', baseContext);

    const textResult = await boSearchPage.setBlacklistedWords(
      page,
      dataLanguages.english.id,
      blacklistedWordsListEN.replace(`${searchWord}|`, ''),
    );
    expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
  });

  it(`should search the word "${searchWord}"`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchWordWithSuccess', baseContext);

    page = await foClassicSearchResultsPage.changePage(browserContext, 1);
    await page.reload();

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(true);

    const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(searchInputValue).to.be.equal(searchWord);
  });

  it('should reset the list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetList', baseContext);

    page = await foClassicSearchResultsPage.changePage(browserContext, 0);

    const textResult = await boSearchPage.setBlacklistedWords(page, dataLanguages.english.id, blacklistedWordsListEN);
    expect(textResult).to.be.eq(boSearchPage.settingsUpdateMessage);
  });
});
