// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {productPage as foProductPage} from '@pages/FO/classic/product';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  dataProducts,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_displayPackContentAndLinkProduct';

describe('FO - Product Page : Display pack content and link to product', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should search the product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchPack', baseContext);

    await homePage.searchProduct(page, 'pack');

    const pageTitle = await searchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(searchResultsPage.pageTitle);

    const hasResults = await searchResultsPage.hasResults(page);
    expect(hasResults).to.eq(true);

    const numResults = await searchResultsPage.getSearchResultsNumber(page);
    expect(numResults).to.eq(1);
  });

  it('should click on the first result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickFirstProduct', baseContext);

    await searchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_21.name);
  });

  it('should check product information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

    const product1 = await foProductPage.getProductInPackList(page, 1);
    await Promise.all([
      expect(product1.name).to.equals(
        `${dataProducts.demo_7.name} `
        + `${basicHelper.capitalize(dataProducts.demo_7.attributes[0].name)}-${dataProducts.demo_7.attributes[0].values[0]}`,
      ),
      expect(product1.price).to.equals(`€${dataProducts.demo_7.price.toFixed(2)}`),
      expect(product1.quantity).to.equals(5),
    ]);

    const product2 = await foProductPage.getProductInPackList(page, 2);
    await Promise.all([
      expect(product2.name).to.equals(dataProducts.demo_12.name),
      expect(product2.price).to.equals(`€${dataProducts.demo_12.price.toFixed(2)}`),
      expect(product2.quantity).to.equals(5),
    ]);
  });

  it('should click on the first product of the pack', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickPackProduct', baseContext);

    await foProductPage.clickProductInPackList(page, 1);

    const pageTitle = await foProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_7.name);
  });
});
