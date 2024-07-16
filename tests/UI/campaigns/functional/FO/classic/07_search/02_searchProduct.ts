// Import utils
import testContext from '@utils/testContext';

// Import FO pages
import {productPage} from '@pages/FO/classic/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  dataProducts,
  foClassicHomePage,
  foClassicSearchResultsPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_search_searchProduct';

/*
  Go to FO
  Check autocomplete
  Choose product on the autocomplete list
  Click on logo link and go to home page
  Click on Enter in autocomplete list
*/
describe('FO - Search Page : Search a product and validate', async () => {
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

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await foClassicHomePage.goToFo(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should check autocomplete', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAutocomplete', baseContext);

    const numResults = await foClassicHomePage.countAutocompleteSearchResult(page, dataProducts.demo_8.name);
    expect(numResults).equal(3);

    const results = await foClassicHomePage.getAutocompleteSearchResult(page, dataProducts.demo_8.name);
    expect(results).contains('notebook');
  });

  it('should choose product on the autocomplete list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseProductOnList', baseContext);

    await foClassicHomePage.setProductNameInSearchInput(page, dataProducts.demo_8.name);
    await foClassicHomePage.clickAutocompleteSearchResult(page, 1);

    const pageTitle = await productPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_8.name);

    const inputValue = await foClassicHomePage.getSearchValue(page);
    expect(inputValue).to.have.lengthOf(0);
  });

  it('should click on logo link and go to home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickLogoLinkAndGoHomePage', baseContext);

    await foClassicHomePage.clickOnHeaderLink(page, 'Logo');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should click on Enter in autocomplete list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

    await foClassicHomePage.searchProduct(page, dataProducts.demo_8.name);

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const inputValue = await foClassicSearchResultsPage.getSearchValue(page);
    expect(inputValue).is.equal(dataProducts.demo_8.name);
  });
});
