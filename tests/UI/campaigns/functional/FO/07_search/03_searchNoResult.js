require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Import pages
const homePage = require('@pages/FO/home');
const searchResultsPage = require('@pages/FO/searchResults');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_search_searchNoResult';

let browserContext;
let page;

/*
  Go to FO
  Search a string lower than 3 characters
  Search an empty string
*/
describe('FO - Search Page : Search product', async () => {
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

  it('should search a string with less than 3 characters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchSmallString', baseContext);

    const hasSearchResult = await homePage.hasAutocompleteSearchResult(page, 'te');
    await expect(hasSearchResult, 'There are results in autocomplete search').to.be.false;

    await homePage.searchProduct(page, 'te');

    const pageTitle = await searchResultsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(searchResultsPage.pageTitle);

    const hasResults = await searchResultsPage.hasResults(page);
    await expect(hasResults, 'There are results!').to.be.false;

    const searchInputValue = await searchResultsPage.getInputValue(page, searchResultsPage.searchInput);
    await expect(searchInputValue, 'A search value exists').to.be.equal('te');
  });

  it('should search an empty string', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchEmptyString', baseContext);

    await homePage.searchProduct(page, '');

    const pageTitle = await searchResultsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(searchResultsPage.pageTitle);

    const hasResults = await searchResultsPage.hasResults(page);
    await expect(hasResults, 'There are results!').to.be.false;

    const searchInputValue = await searchResultsPage.getInputValue(page, searchResultsPage.searchInput);
    await expect(searchInputValue, 'A search value exists').to.be.equal('');
  });
});
