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
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_displayPackContentAndLinkProduct';

describe('FO - Product Page : Display pack content and link to product', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await foClassicHomePage.goToFo(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should search the product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchPack', baseContext);

    await foClassicHomePage.searchProduct(page, 'pack');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const hasResults = await foClassicSearchResultsPage.hasResults(page);
    expect(hasResults).to.eq(true);

    const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
    expect(numResults).to.eq(1);
  });

  it('should click on the first result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickFirstProduct', baseContext);

    await foClassicSearchResultsPage.goToProductPage(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_21.name);
  });

  it('should check product information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

    const product1 = await foClassicProductPage.getProductInPackList(page, 1);
    await Promise.all([
      expect(product1.name).to.equals(
        `${dataProducts.demo_7.name} `
        + `${utilsCore.capitalize(dataProducts.demo_7.attributes[0].name)}-${dataProducts.demo_7.attributes[0].values[0]}`,
      ),
      expect(product1.price.toFixed(2)).to.equals(dataProducts.demo_7.price.toFixed(2)),
      expect(product1.quantity).to.equals(5),
    ]);

    const product2 = await foClassicProductPage.getProductInPackList(page, 2);
    await Promise.all([
      expect(product2.name).to.equals(dataProducts.demo_12.name),
      expect(product2.price.toFixed(2)).to.equals(dataProducts.demo_12.price.toFixed(2)),
      expect(product2.quantity).to.equals(5),
    ]);
  });

  it('should click on the first product of the pack', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickPackProduct', baseContext);

    await foClassicProductPage.clickProductInPackList(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_7.name);
  });
});
