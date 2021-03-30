require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const searchPage = require('@pages/BO/shopParameters/search');
const homePage = require('@pages/FO/home');
const searchResultsPage = require('@pages/FO/searchResults');

// Import data
const {Products} = require('@data/demo/products');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_search_searchProduct';

let browserContext;
let page;
const productName = 'Hummingbird';
const searchResult = 'Men > Hummingbird printed t-shirtHome Accessories > Hummingbird cushionWomen > '
  + 'Hummingbird printed sweaterArt > Hummingbird - Vector graphicsStationery > Hummingbird notebook';

/*
Go to FO
Search Product
 */

describe('Search product', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

    await dashboardPage.goToSubMenu(page, dashboardPage.shopParametersParentLink, dashboardPage.searchLink);

    const pageTitle = await searchPage.getPageTitle(page);
    await expect(pageTitle).to.contains(searchPage.pageTitle);
  });

  it('disable fuzzy search', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'DisableFuzzySearch', baseContext);

    const result = await searchPage.setFuzzySearch(page, false);
    await expect(result).to.contains(searchPage.successfulUpdateMessage);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should search product and check the autocomplete list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAutocompleteList', baseContext);

    const result = await homePage.getAutocompleteSearchResult(page, productName);
    await expect(result, 'Search result is incorrect!').to.be.equal(searchResult);
  });

  it('should search product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

    await homePage.searchProduct(page, productName);

    const pageTitle = await searchResultsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
  });

  it('should check search result number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductNumber', baseContext);

    const resultNumber = await searchResultsPage.getSearchResultsNumber(page);
    await expect(resultNumber, 'Product number is incorrect!').to.be.equal(5);
  });
});
