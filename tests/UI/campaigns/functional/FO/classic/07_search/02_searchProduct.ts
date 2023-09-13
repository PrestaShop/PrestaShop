// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import {homePage} from '@pages/FO/home';
import productPage from '@pages/FO/product';
import {searchResultsPage} from '@pages/FO/searchResults';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should check autocomplete', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAutocomplete', baseContext);

    const numResults = await homePage.countAutocompleteSearchResult(page, Products.demo_8.name);
    await expect(numResults).equal(3);

    const results = await homePage.getAutocompleteSearchResult(page, Products.demo_8.name);
    await expect(results).contains('notebook');
  });

  it('should choose product on the autocomplete list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseProductOnList', baseContext);

    await homePage.clickAutocompleteSearchResult(page, Products.demo_8.name, 1);

    const pageTitle = await productPage.getPageTitle(page);
    await expect(pageTitle).to.contains(Products.demo_8.name);

    const inputValue = await homePage.getSearchValue(page);
    await expect(inputValue).is.empty;
  });

  it('should click on logo link and go to home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickLogoLinkAndGoHomePage', baseContext);

    await homePage.clickOnHeaderLink(page, 'Logo');

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should click on Enter in autocomplete list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

    await homePage.searchProduct(page, Products.demo_8.name);

    const pageTitle = await searchResultsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(searchResultsPage.pageTitle);

    const inputValue = await searchResultsPage.getSearchValue(page);
    await expect(inputValue).is.equal(Products.demo_8.name);
  });
});
