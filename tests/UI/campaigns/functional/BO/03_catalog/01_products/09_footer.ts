// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import createProductsPage from '@pages/BO/catalog/products/add';

// Import data
import Products from '@data/demo/products';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'functional_BO_catalog_products_footer';

describe('BO - Catalog - Products : Footer', async () => {
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
    expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it(`should filter a product named "${Products.demo_12.name}"`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterProduct', baseContext);

    await productsPage.resetFilter(page);
    await productsPage.filterProducts(page, 'product_name', Products.demo_12.name, 'input');

    const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
    expect(numberOfProductsAfterFilter).to.equal(1);

    const textColumn = await productsPage.getTextColumn(page, 'product_name', 1);
    expect(textColumn).to.equal(Products.demo_12.name);
  });

  it('should edit the product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

    await productsPage.goToProductPage(page, 1);

    const pageTitle: string = await createProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(createProductsPage.pageTitle);
  });

  it('should duplicate the product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'duplicateProduct', baseContext);

    const textMessage = await createProductsPage.duplicateProduct(page);
    expect(textMessage).to.equal(createProductsPage.successfulDuplicateMessage);
  });

  it('should check the duplicated product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDuplicatedProduct', baseContext);

    const nameEN = await createProductsPage.getProductName(page, 'en');
    expect(nameEN).to.equal(`copy of ${Products.demo_12.name}`);

    const nameFR = await createProductsPage.getProductName(page, 'fr');
    expect(nameFR).to.equal(`copie de ${Products.demo_12.name}`);
  });

  it('should delete the duplicated product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteDuplicatedProduct', baseContext);

    const textMessage = await createProductsPage.deleteProduct(page);
    expect(textMessage).to.equal(createProductsPage.successfulDeleteMessage);
  });
});
