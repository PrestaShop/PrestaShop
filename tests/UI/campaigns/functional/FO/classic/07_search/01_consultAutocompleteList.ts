// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import {homePage} from '@pages/FO/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_search_consultAutocompleteList';

/*
  Go to FO
  Check autocomplete list
  Click outside the autocomplete list
  Check the autocomplete list with values
  Check the autocomplete list with a string with less than 3 characters
*/

describe('FO - Search Page : Search product and consult autocomplete list', async () => {
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

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should check the autocomplete list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAutocompleteList', baseContext);

    const searchValue: string = 'test';
    const numSearchResults: number = 7;

    const numResults = await homePage.countAutocompleteSearchResult(page, searchValue);
    expect(numResults).equal(numSearchResults);

    const inputValue = await homePage.getSearchValue(page);
    expect(inputValue).equal(searchValue);
  });

  it('should click outside the autocomplete list and check that the list is not displayed', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOutsideAutocompleteList', baseContext);

    await homePage.closeAutocompleteSearch(page);

    const hasAutocompleteList = await homePage.isAutocompleteSearchResultVisible(page);
    expect(hasAutocompleteList).to.eq(false);
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
  ].forEach((search, index: number) => {
    it(`should check the autocomplete list with the value ${search.searchValue}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkAutocompleteList_${index}`, baseContext);

      const numResults = await homePage.countAutocompleteSearchResult(page, search.searchValue);
      expect(numResults).equal(search.numResults);

      const inputValue = await homePage.getSearchValue(page);
      expect(inputValue).equal(search.searchValue);

      await homePage.closeAutocompleteSearch(page);
    });
  });

  it('should check the autocomplete list with a string with less than 3 characters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAutocompleteListSmallString', baseContext);

    const hasSearchResult = await homePage.hasAutocompleteSearchResult(page, 'te');
    expect(hasSearchResult, 'There are results in autocomplete search').to.eq(false);
  });
});
