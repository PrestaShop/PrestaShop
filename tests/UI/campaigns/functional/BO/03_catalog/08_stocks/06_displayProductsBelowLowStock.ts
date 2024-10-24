// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boStockPage,
  type BrowserContext,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_stocks_displayProductsBelowLowStock';

// Simple filter stocks
describe('BO - Catalog - Stocks : Display products below low stock level first', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  let productQuantity: number;

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

  it('should go to \'Catalog > Stocks\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.stocksLink,
    );
    await boStockPage.closeSfToolBar(page);

    const pageTitle = await boStockPage.getPageTitle(page);
    expect(pageTitle).to.contains(boStockPage.pageTitle);
  });

  it('should get the number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProductsInList', baseContext);

    numberOfProducts = await boStockPage.getTotalNumberOfProducts(page);
    expect(numberOfProducts).to.be.above(0);
  });

  it('should get the quantity of the second product in the list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getQuantityOfSecondProduct', baseContext);

    productQuantity = parseInt(await boStockPage.getTextColumnFromTableStocks(page, 2, 'available'), 10);
  });

  it('should update the second product quantity to -300', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateProductQuantity', baseContext);

    // Update Quantity and check successful message
    const updateMessage = await boStockPage.updateRowQuantityWithInput(page, 2, -productQuantity);
    expect(updateMessage).to.contains(boStockPage.successfulUpdateMessage);

    // Check physical and available quantities of product after update
    const quantityToCheck = await boStockPage.getStockQuantityForProduct(page, 2);
    expect(quantityToCheck.physical).to.be.equal(0);
    expect(quantityToCheck.available).to.be.equal(0);
  });

  it('should check \'Display products below low stock level first\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayProductBelowLowStock', baseContext);

    await boStockPage.setDisplayProductsBelowLowOfStock(page, true);
  });

  it('should check that the second product is displayed first', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSecondProductIsDisplayedFirst', baseContext);

    const productName = await boStockPage.getTextColumnFromTableStocks(page, 1, 'product_name');
    expect(productName).to.contain('Hummingbird notebook');
  });

  it('should check the available quantity for the first product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAvailableQuantity', baseContext);

    const availableQuantity = await boStockPage.getTextColumnFromTableStocks(page, 1, 'available');
    expect(availableQuantity).to.equal('0 !');
  });

  it('should check the physical quantity for the first product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkPhysicalQuantity', baseContext);

    const productQuantity = await boStockPage.getTextColumnFromTableStocks(page, 1, 'physical');
    expect(productQuantity).to.equal('0');
  });

  it('should check that the whole product line is red', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkLineIsRed', baseContext);

    const isLowStock = await boStockPage.isProductLowStock(page, 1);
    expect(isLowStock).to.equal(true);
  });

  it('should uncheck \'Display products below low stock level first\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uncheckDisplayProductBelowLowStock', baseContext);

    await boStockPage.setDisplayProductsBelowLowOfStock(page, false);
  });

  it('should check that the edited product is displayed second', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProduct', baseContext);

    const productName = await boStockPage.getTextColumnFromTableStocks(page, 1, 'product_name');
    expect(productName).to.not.contain('Hummingbird notebook');
  });

  it('should check that the whole product line is not red', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkLineIsNotRed', baseContext);

    const isLowStock = await boStockPage.isProductLowStock(page, 1);
    expect(isLowStock).to.equal(false);
  });

  // @todo : https://github.com/PrestaShop/PrestaShop/issues/33681
  it.skip('should check that the products are sorted by ID desc', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductSortedByIDDesc', baseContext);

    const table = await boStockPage.getAllRowsColumnContent(page, 'product_id');

    const tableFloat: number[] = table.map((text: string): number => parseInt(text, 10));
    const expectedResult: number[] = await utilsCore.sortArrayNumber(tableFloat);
    expect(tableFloat).to.deep.equal(expectedResult.reverse());
  });
});
