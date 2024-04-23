// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import productPage from '@pages/FO/hummingbird/product';
import searchResultsPage from '@pages/FO/hummingbird/searchResults';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
  installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Consult results list', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should put \'Mug\' in the search input and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct1', baseContext);

      await homePage.searchProduct(page, 'mug');

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should check the search result page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'countResult', baseContext);

      const countResults = await searchResultsPage.getSearchResultsNumber(page);
      expect(countResults).to.equal(5);
    });

    it('should go to the second product in the list and check the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondProductInList', baseContext);

      await searchResultsPage.goToProductPage(page, 2);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_11.name);
    });

    it('should go back to the precedent page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPrecedentPage', baseContext);

      await page.goBack();

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should put \'Fox\' in the search input and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct2', baseContext);

      await searchResultsPage.searchProduct(page, 'fox');

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should check the search result page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'countResult2', baseContext);

      const countResults = await searchResultsPage.getSearchResultsNumber(page);
      expect(countResults).to.equal(7);
    });

    it('should go to the first product in the list and check the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductInList', baseContext);

      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_15.name);
    });

    it('should go back to the precedent page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPrecedentPage2', baseContext);

      await page.goBack();

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should remove the searched value and press enter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeSearch', baseContext);

      await homePage.searchProduct(page, '');

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);

      const hasResults = await searchResultsPage.hasResults(page);
      expect(hasResults, 'There are results!').to.equal(false);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
