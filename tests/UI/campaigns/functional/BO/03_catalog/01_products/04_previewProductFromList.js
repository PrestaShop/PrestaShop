require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import data
const {Products} = require('@data/demo/products');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/products');
const foProductPage = require('@pages/FO/product');

const baseContext = 'functional_BO_catalog_products_previewProductFromList';

let browserContext;
let page;

let numberOfProducts = 0;

/*
Go to products page
Filter products by name
Preview product
Check product information in FO
*/

describe('BO - Catalog - Products : Preview product from list', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });


  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.productsLink,
    );

    await productsPage.closeSfToolBar(page);

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should reset all filters and get number of products', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProducts).to.be.above(0);
  });

  it(`should filter product by name '${Products.demo_5.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToPreview', baseContext);

    await productsPage.filterProducts(page, 'name', Products.demo_5.name);

    const textColumn = await productsPage.getProductNameFromList(page, 1);
    await expect(textColumn).to.contains(Products.demo_5.name);
  });

  it('should preview product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

    // Preview product
    page = await productsPage.previewProduct(page, 1);

    // Check product information in FO
    const productInformation = await foProductPage.getProductInformation(page);
    await expect(productInformation.name).to.equal(Products.demo_5.name);
  });
});
