// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type BrowserContext,
  foHummingbirdCategoryPage,
  foHummingbirdHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_menuAndNavigation_sortAndFilter_backNavigationAfterFilter';

/*
Scenario:
- Go to FO > All products page and record the unfiltered listing
- Filter products by composition
- Go back with the browser and check the unfiltered listing is restored
 */
describe('FO - Menu and Navigation - Sort and filter : Browser back after filtering', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let unfilteredUrl: string;
  let unfilteredProductsNumber: number;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openShopPage', baseContext);

    await foHummingbirdHomePage.goTo(page, global.FO.URL);

    const result = await foHummingbirdHomePage.isHomePage(page);
    expect(result).to.equal(true);
  });

  it('should go to all products page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts', baseContext);

    await foHummingbirdHomePage.changeLanguage(page, 'en');
    await foHummingbirdHomePage.goToAllProductsPage(page, 'ps-featuredproducts');

    const isCategoryPageVisible = await foHummingbirdCategoryPage.isCategoryPage(page);
    expect(isCategoryPageVisible, 'Home category page was not opened').to.equal(true);
  });

  it('should record the unfiltered listing', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'recordUnfilteredListing', baseContext);

    unfilteredUrl = await foHummingbirdCategoryPage.getCurrentURL(page);
    unfilteredProductsNumber = await foHummingbirdCategoryPage.getNumberOfProducts(page);
    expect(unfilteredProductsNumber).to.be.above(1);

    const isActiveFilterNotVisible = await foHummingbirdCategoryPage.isActiveFilterNotVisible(page);
    expect(isActiveFilterNotVisible).to.equal(true);
  });

  it('should filter products by composition \'Ceramic\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByComposition', baseContext);

    await foHummingbirdCategoryPage.filterByCheckbox(page, 'Composition', 'Composition-Ceramic', true);

    const activeFilters = await foHummingbirdCategoryPage.getActiveFilters(page);
    expect(activeFilters).to.contains('Composition: Ceramic');
  });

  it('should check that the filtered listing differs from the unfiltered one', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkFilteredListing', baseContext);

    const filteredUrl = await foHummingbirdCategoryPage.getCurrentURL(page);
    expect(filteredUrl).to.not.equal(unfilteredUrl);

    const filteredProductsNumber = await foHummingbirdCategoryPage.getNumberOfProducts(page);
    expect(filteredProductsNumber).to.be.below(unfilteredProductsNumber);
  });

  it('should go back with the browser and check the unfiltered listing is restored', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToUnfilteredListing', baseContext);

    await page.goBack();
    // The filtered listing was rendered in the same document, so the restored entry is reloaded
    await page.waitForTimeout(2000);
    await page.waitForLoadState('networkidle');

    const currentUrl = await foHummingbirdCategoryPage.getCurrentURL(page);
    expect(currentUrl).to.equal(unfilteredUrl);

    const isActiveFilterNotVisible = await foHummingbirdCategoryPage.isActiveFilterNotVisible(page);
    expect(isActiveFilterNotVisible).to.equal(true);

    const productsNumber = await foHummingbirdCategoryPage.getNumberOfProducts(page);
    expect(productsNumber).to.equal(unfilteredProductsNumber);
  });
});
