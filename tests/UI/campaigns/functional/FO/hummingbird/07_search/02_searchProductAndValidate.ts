// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {expect} from 'chai';
import {
  type BrowserContext,
  dataProducts,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_search_searchProductAndValidate';

/*
Pre-condition:
- Install hummingbird themeProducts
Scenario:
- Go to FO
- Search product and see result
- Click on first product and check product page
- Click on enter and check search result page
Post-condition:
- Uninstall hummingbird theme
*/

describe('FO - Search Page : Search product and validate', async () => {
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

  describe('Search product and validate', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it(`should search for the product ${dataProducts.demo_8.name} and check result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct1', baseContext);

      const searchValue: string = dataProducts.demo_8.name;
      const numSearchResults: number = 3;

      const numResults = await foHummingbirdHomePage.countAutocompleteSearchResult(page, searchValue);
      expect(numResults).equal(numSearchResults);

      const results = await foHummingbirdHomePage.getAutocompleteSearchResult(page, searchValue);

      const occurrence = await utilsCore.searchOccurrence(results, 'notebook');
      expect(occurrence).to.equal(numSearchResults);
    });

    it('should go to the first product in the list and check the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductInList', baseContext);

      await foHummingbirdHomePage.clickAutocompleteSearchResult(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_8.name);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foHummingbirdProductPage.goToHomePage(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should search for the product and click on enter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct2', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_8.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should check that the searched value in the search input is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSearchedValue', baseContext);

      const inputContent = await foHummingbirdSearchResultsPage.getSearchInput(page);
      expect(inputContent).to.equal(dataProducts.demo_8.name);
    });

    it('should check the search result page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSearchResultPage', baseContext);

      const countResults = await foHummingbirdSearchResultsPage.getSearchResultsNumber(page);
      expect(countResults).to.equal(3);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
