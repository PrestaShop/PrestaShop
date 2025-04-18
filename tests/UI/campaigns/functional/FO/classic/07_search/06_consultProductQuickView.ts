// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type BrowserContext,
  dataProducts,
  foClassicHomePage,
  foClassicModalQuickViewPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_search_consultProductQuickView';

/*
Scenario:
- Go to FO
- Search Mug value and see result
- Quick view the first product
- Check product information
- Close quick view modal
*/

describe('FO - Search Page : Consult product quick view', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await foClassicHomePage.goToFo(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should put \'Mug\' in the search input and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchProduct1', baseContext);

    await foClassicHomePage.searchProduct(page, 'mug');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
  });

  it('should check the search result page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'countResult', baseContext);

    const countResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
    expect(countResults).to.equal(5);
  });

  it('should quick view the first product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct1', baseContext);

    await foClassicSearchResultsPage.quickViewProduct(page, 1);

    const isModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
    expect(isModalVisible).to.eq(true);
  });

  it('should check product information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation1', baseContext);

    const result = await foClassicModalQuickViewPage.getProductDetailsFromQuickViewModal(page);
    await Promise.all([
      expect(result.name).to.equal(dataProducts.demo_14.name),
      expect(result.price).to.equal(dataProducts.demo_14.finalPrice),
      expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
      expect(result.shortDescription).to.equal(dataProducts.demo_14.summary),
      expect(result.coverImage).to.contains(dataProducts.demo_14.coverImage),
      expect(result.thumbImage).to.contains(dataProducts.demo_14.thumbImage),
    ]);
  });

  it('should close the quick view modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeQuickOptionModal', baseContext);

    const isQuickViewModalClosed = await foClassicModalQuickViewPage.closeQuickViewModal(page);
    expect(isQuickViewModalClosed).to.equal(true);
  });
});
