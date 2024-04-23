// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import searchResultsPage from '@pages/FO/hummingbird/searchResults';
import quickViewModal from '@pages/FO/hummingbird/modal/quickView';

// Import demo data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_search_consultProductQuickView';

/*
Pre-condition:
- Install hummingbird themeProducts
Scenario:
- Go to FO
- Search Mug value and see result
- Quick view the first product
- Check product information
- Close quick view modal
Post-condition:
- Uninstall hummingbird theme
*/

describe('FO - Search Page : Consult product quick view', async () => {
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

  describe('Consult product quick view', async () => {
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

    it('should quick view the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct1', baseContext);

      await searchResultsPage.quickViewProduct(page, 1);

      const isModalVisible = await quickViewModal.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should check product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation1', baseContext);

      const result = await quickViewModal.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_14.name),
        expect(result.price).to.equal(Products.demo_14.finalPrice),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(Products.demo_14.summary),
        expect(result.coverImage).to.contains(Products.demo_14.coverImage),
        expect(result.thumbImage).to.contains(Products.demo_14.thumbImage),
      ]);
    });

    it('should close the quick view modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeQuickOptionModal', baseContext);

      const isQuickViewModalClosed = await quickViewModal.closeQuickViewModal(page);
      expect(isQuickViewModalClosed).to.equal(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
