require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/BO/loginBO');

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

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('check the autocomplete list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAutocompleteList', baseContext);

    const searchValue = 'test';
    const numSearchResults = 7;

    const numResults = await homePage.countAutocompleteSearchResult(page, searchValue);
    await expect(numResults).equal(numSearchResults);

    const inputValue = await homePage.getInputValue(page, homePage.searchInput);
    await expect(inputValue).equal(searchValue);
  });

  it('should click outside the autocomplete list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOutsideAutocompleteList', baseContext);

    await page.keyboard.press('Escape');

    const hasAutocompleteList = await homePage.elementVisible(page, homePage.autocompleteSearchResult);
    await expect(hasAutocompleteList).to.be.false;
  });

  [
    {
      searchValue: 'Mug',
      numResults: 5,
    },
    {
      searchValue: 'T-sh',
      numResults: 1,
    },
    {
      searchValue: 'Notebook',
      numResults: 3,
    },
  ].forEach((search, index) => {
    it(`should check the autocomplete list with the value ${search.searchValue}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkAutocompleteList_${index}`, baseContext);

      const numResults = await homePage.countAutocompleteSearchResult(page, search.searchValue);
      await expect(numResults).equal(search.numResults);

      const inputValue = await homePage.getInputValue(page, homePage.searchInput);
      await expect(inputValue).equal(search.searchValue);
    });
  });

  it('should check the autocomplete list with a string with less than 3 characters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAutocompleteListSmallString', baseContext);

    const hasSearchResult = await homePage.hasAutocompleteSearchResult(page, 'te');
    await expect(hasSearchResult, 'There are results in autocomplete search').to.be.false;
  });
});
