require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
// BO
const dashboardPage = require('@pages/BO/dashboard');
const searchPage = require('@pages/BO/shopParameters/search');

// FO
const homePage = require('@pages/FO/home');
const searchResultsPage = require('@pages/FO/searchResults');

// Import data
const {Products} = require('@data/demo/products');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_search_consultAutocompleteList';

let browserContext;
let page;
const searchValue = 'Hummingbird printed';

// Search result should contain element who includes the search value
const searchResults = Object.values(Products)
  .map(product => product.name)
  .filter(name => name.includes(searchValue));

/*
Disable Fuzzy search in BO
Go to FO
Search Product and check result
Check the products number
 */

describe('FO - Search Page : Search product and consult autocomplete list', async () => {
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

  it('should go to \'Shop parameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPageToDisable', baseContext);

    await dashboardPage.goToSubMenu(page, dashboardPage.shopParametersParentLink, dashboardPage.searchLink);

    const pageTitle = await searchPage.getPageTitle(page);
    await expect(pageTitle).to.contains(searchPage.pageTitle);
  });

  it('should disable fuzzy search', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'DisableFuzzySearch', baseContext);

    const result = await searchPage.setFuzzySearch(page, false);
    await expect(result).to.contains(searchPage.settingsUpdateMessage);
  });

  it('should go to FO and search product to check the autocomplete list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAutocompleteList', baseContext);

    page = await searchPage.viewMyShop(page);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;

    const result = await homePage.getAutocompleteSearchResult(page, searchValue);

    for (let i = 0; i < searchResults.length; i++) {
      await expect(result, `Search result should contain ${searchResults[i]}`).to.contain(searchResults[i]);
    }
  });

  it('should search product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

    await homePage.searchProduct(page, searchValue);

    const pageTitle = await searchResultsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
  });

  it('should check search result number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductNumber', baseContext);

    const resultNumber = await searchResultsPage.getSearchResultsNumber(page);
    await expect(resultNumber, 'Product searched number is incorrect!').to.be.equal(searchResults.length);

    page = await searchResultsPage.closePage(browserContext, page, 0);
  });

  it('should enable fuzzy search', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'EnableFuzzySearch', baseContext);

    const result = await searchPage.setFuzzySearch(page, true);
    await expect(result).to.contains(searchPage.settingsUpdateMessage);
  });
});
