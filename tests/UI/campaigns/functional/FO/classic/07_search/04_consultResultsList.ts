// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type BrowserContext,
  dataProducts,
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_search_consultResultsList';

/*
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
*/

describe('FO - Search Page : Consult results list', async () => {
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

  it('should go to the second product in the list and check the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSecondProductInList', baseContext);

    await foClassicSearchResultsPage.goToProductPage(page, 2);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_11.name);
  });

  it('should go back to the precedent page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPrecedentPage', baseContext);

    await page.goBack();

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
  });

  it('should put \'Fox\' in the search input and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchProduct2', baseContext);

    await foClassicSearchResultsPage.searchProduct(page, 'fox');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
  });

  it('should check the search result page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'countResult2', baseContext);

    const countResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
    expect(countResults).to.equal(7);
  });

  it('should go to the first product in the list and check the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProductInList', baseContext);

    await foClassicSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_15.name);
  });

  it('should go back to the precedent page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPrecedentPage2', baseContext);

    await page.goBack();

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
  });

  it('should remove the searched value and press enter', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'removeSearch', baseContext);

    await foClassicHomePage.searchProduct(page, '');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults, 'There are results!').to.equal(false);
  });
});
