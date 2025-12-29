// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {expect} from 'chai';
import {
  type BrowserContext,
  foHummingbirdHomePage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  enableTheme('hummingbird', `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Search no result', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should search a string with less than 3 characters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchSmallString', baseContext);

      const hasSearchResult = await foHummingbirdHomePage.hasAutocompleteSearchResult(page, 'te');
      expect(hasSearchResult, 'There are results in autocomplete search').to.eq(false);

      await foHummingbirdHomePage.searchProduct(page, 'te');

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);

      const hasResults = await foHummingbirdSearchResultsPage.hasResults(page);
      expect(hasResults, 'There are results!').to.equal(false);

      const searchInputValue = await foHummingbirdSearchResultsPage.getSearchValue(page);
      expect(searchInputValue, 'A search value exists').to.equal('te');
    });

    it('should search an empty string', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchEmptyString', baseContext);

      await foHummingbirdHomePage.searchProduct(page, '');

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);

      const hasResults = await foHummingbirdSearchResultsPage.hasResults(page);
      expect(hasResults, 'There are results!').to.equal(false);

      const searchInputValue = await foHummingbirdSearchResultsPage.getSearchValue(page);
      expect(searchInputValue, 'A search value exists').to.equal('');
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
