import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  type BrowserContext,
  dataProducts,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_footer';

describe('BO - Catalog - Products : Footer', async () => {
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

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.productsLink,
    );
    await boProductsPage.closeSfToolBar(page);

    const pageTitle = await boProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsPage.pageTitle);
  });

  it(`should filter a product named "${dataProducts.demo_12.name}"`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterProduct', baseContext);

    await boProductsPage.resetFilter(page);
    await boProductsPage.filterProducts(page, 'product_name', dataProducts.demo_12.name, 'input');

    const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
    expect(numberOfProductsAfterFilter).to.equal(1);

    const textColumn = await boProductsPage.getTextColumn(page, 'product_name', 1);
    expect(textColumn).to.equal(dataProducts.demo_12.name);
  });

  it('should edit the product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

    await boProductsPage.goToProductPage(page, 1);

    const pageTitle: string = await boProductsCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
  });

  it('should duplicate the product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'duplicateProduct', baseContext);

    const textMessage = await boProductsCreatePage.duplicateProduct(page);
    expect(textMessage).to.equal(boProductsCreatePage.successfulDuplicateMessage);
  });

  it('should check the duplicated product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDuplicatedProduct', baseContext);

    const nameEN = await boProductsCreatePage.getProductName(page, 'en');
    expect(nameEN).to.equal(`copy of ${dataProducts.demo_12.name}`);

    const nameFR = await boProductsCreatePage.getProductName(page, 'fr');
    expect(nameFR).to.equal(`copie de ${dataProducts.demo_12.name}`);
  });

  it('should delete the duplicated product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteDuplicatedProduct', baseContext);

    const textMessage = await boProductsCreatePage.deleteProduct(page);
    expect(textMessage).to.equal(boProductsCreatePage.successfulDeleteMessage);
  });
});
