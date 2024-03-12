// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import searchResultsPage from '@pages/FO/hummingbird/searchResults';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_search_searchNoResult';

/*
Pre-condition:
- Install hummingbird themeProducts
Scenario:
- Go to FO
- Search a string with less than 3 characters
- Search an empty string
Post-condition:
- Uninstall hummingbird theme
*/

describe('FO - Search Page : Search no result', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Search no result', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should search a string with less than 3 characters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchSmallString', baseContext);

      const hasSearchResult = await homePage.hasAutocompleteSearchResult(page, 'te');
      expect(hasSearchResult, 'There are results in autocomplete search').to.eq(false);

      await homePage.searchProduct(page, 'te');

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);

      const hasResults = await searchResultsPage.hasResults(page);
      expect(hasResults, 'There are results!').to.equal(false);

      const searchInputValue = await searchResultsPage.getSearchValue(page);
      expect(searchInputValue, 'A search value exists').to.equal('te');
    });

    it('should search an empty string', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchEmptyString', baseContext);

      await homePage.searchProduct(page, '');

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);

      const hasResults = await searchResultsPage.hasResults(page);
      expect(hasResults, 'There are results!').to.equal(false);

      const searchInputValue = await searchResultsPage.getSearchValue(page);
      expect(searchInputValue, 'A search value exists').to.equal('');
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
