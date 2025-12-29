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
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_search_consultResultsList';

/*
Pre-condition:
- Install hummingbird themeProducts
Scenario:
- Go to FO
- Search Mug value and see result
- Go to second product in the list
- Click on precedent
- Search Fox value and see result
- Go to first product in the list
- Click on precedent
- Delete the searched value and click on enter
- Check no result
Post-condition:
- Uninstall hummingbird theme
*/

describe('FO - Search Page : Consult results list', async () => {
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

  describe('Consult results list', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should put \'Mug\' in the search input and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct1', baseContext);

      await foHummingbirdHomePage.searchProduct(page, 'mug');

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should check the search result page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'countResult', baseContext);

      const countResults = await foHummingbirdSearchResultsPage.getSearchResultsNumber(page);
      expect(countResults).to.equal(5);
    });

    it('should go to the second product in the list and check the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondProductInList', baseContext);

      await foHummingbirdSearchResultsPage.goToProductPage(page, 2);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_11.name);
    });

    it('should go back to the precedent page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPrecedentPage', baseContext);

      await page.goBack();

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should put \'Fox\' in the search input and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct2', baseContext);

      await foHummingbirdSearchResultsPage.searchProduct(page, 'fox');

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should check the search result page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'countResult2', baseContext);

      const countResults = await foHummingbirdSearchResultsPage.getSearchResultsNumber(page);
      expect(countResults).to.equal(7);
    });

    it('should go to the first product in the list and check the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductInList', baseContext);

      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_15.name);
    });

    it('should go back to the precedent page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPrecedentPage2', baseContext);

      await page.goBack();

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should remove the searched value and press enter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeSearch', baseContext);

      await foHummingbirdHomePage.searchProduct(page, '');

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);

      const hasResults = await foHummingbirdSearchResultsPage.hasResults(page);
      expect(hasResults, 'There are results!').to.equal(false);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
