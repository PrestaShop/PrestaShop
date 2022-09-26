require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Import pages
const homePage = require('@pages/FO/home');
const productPage = require('@pages/FO/product');
const searchResultsPage = require('@pages/FO/searchResults');

// Import data
const {Products} = require('@data/demo/products');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_search_searchProduct';

let browserContext;
let page;

/*
  Go to FO
  Check autocomplete
  Choose product on the autocomplete list
  Click on logo link and go to home page
  Click on Enter in autocomplete list
*/
describe('FO - Search Page : Search a product and validate', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should check autocomplete', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAutocomplete', baseContext);

    const numResults = await homePage.countAutocompleteSearchResult(page, Products.demo_8.name);
    await expect(numResults).equal(3);

    const results = await homePage.getAutocompleteSearchResult(page, Products.demo_8.name);
    await expect(results).contains('notebook');
  });

  it('should choose product on the autocomplete list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseProductOnList', baseContext);

    await homePage.clickAutocompleteSearchResult(page, Products.demo_8.name, 1);

    const pageTitle = await productPage.getPageTitle(page);
    await expect(pageTitle).to.contains(Products.demo_8.name);

    const inputValue = await homePage.getInputValue(page, productPage.searchInput);
    await expect(inputValue).is.empty;
  });

  it('should click on logo link and go to home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickLogoLinkAndGoHomePage', baseContext);

    await homePage.clickOnHeaderLink(page, 'Logo');

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should click on Enter in autocomplete list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

    await homePage.searchProduct(page, Products.demo_8.name);

    const pageTitle = await searchResultsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(searchResultsPage.pageTitle);

    const inputValue = await searchResultsPage.getInputValue(page, searchResultsPage.searchInput);
    await expect(inputValue).is.equal(Products.demo_8.name);
  });
});
