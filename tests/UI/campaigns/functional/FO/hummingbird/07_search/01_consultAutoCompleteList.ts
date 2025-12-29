// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {expect} from 'chai';
import {
  type BrowserContext,
  foHummingbirdHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_search_consultAutoCompleteList';

/*
Pre-condition:
- Install hummingbird theme
Scenario:
- Go to FO
- Check autocomplete list
- Click outside the autocomplete list
- Check the autocomplete list with values
- Check the autocomplete list with a string with less than 3 characters
Post-condition:
- Uninstall hummingbird theme
*/

describe('FO - Search Page : Search product and consult autocomplete list', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Consult autocomplete list', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should check the autocomplete list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAutocompleteList', baseContext);

      const searchValue: string = 'test';
      const numSearchResults: number = 7;

      const numResults = await foHummingbirdHomePage.countAutocompleteSearchResult(page, searchValue);
      expect(numResults).equal(numSearchResults);

      const inputValue = await foHummingbirdHomePage.getSearchValue(page);
      expect(inputValue).equal(searchValue);
    });

    it('should click outside the autocomplete list and check that the list is not displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOutsideAutocompleteList', baseContext);

      await foHummingbirdHomePage.closeAutocompleteSearch(page);

      const hasAutocompleteList = await foHummingbirdHomePage.isAutocompleteSearchResultVisible(page);
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

        const numResults = await foHummingbirdHomePage.countAutocompleteSearchResult(page, search.searchValue);
        expect(numResults).equal(search.numResults);

        const inputValue = await foHummingbirdHomePage.getSearchValue(page);
        expect(inputValue).equal(search.searchValue);
      });

      it('should close the autocomplete list', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `closeAutocompleteList_${index}`, baseContext);

        await foHummingbirdHomePage.closeAutocompleteSearch(page);

        const hasAutocompleteList = await foHummingbirdHomePage.isAutocompleteSearchResultVisible(page);
        expect(hasAutocompleteList).to.eq(false);
      });
    });

    it('should check the autocomplete list with a string with less than 3 characters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAutocompleteListSmallString', baseContext);

      const hasSearchResult = await foHummingbirdHomePage.hasAutocompleteSearchResult(page, 'te');
      expect(hasSearchResult, 'There are results in autocomplete search').to.eq(false);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
