// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_products_duplicateProductFromList';

/*
Go to products page
Filter products by name
Duplicate product
Filter by name of the duplicated product
Delete the duplicated product
*/

describe('BO - Catalog - Products : Duplicate product from list', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

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
    await testContext.addContextItem(this, 'testIdentifier', 'filterToDuplicate', baseContext);

    await productsPage.filterProducts(page, 'name', Products.demo_5.name);

    const textColumn = await productsPage.getProductNameFromList(page, 1);
    await expect(textColumn).to.contains(Products.demo_5.name);
  });

  it('should duplicate product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'duplicateProduct', baseContext);

    // Duplicate product from list
    const textResult = await productsPage.duplicateProduct(page, 1);
    await expect(textResult).to.contain(addProductPage.duplicateSuccessfulMessage);
  });

  it('should check duplicated product name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductName', baseContext);

    const productName = await addProductPage.getProductName(page);
    await expect(productName).to.contain(`copy of ${Products.demo_5.name}`);
  });

  it('should delete duplicated product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

    const textResult = await addProductPage.deleteProduct(page);
    await expect(textResult).to.contain(productsPage.productDeletedSuccessfulMessage);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

    const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProductsAfterReset).to.be.equal(numberOfProducts);
  });
});
