// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import basicHelper from '@utils/basicHelper';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import stocksPage from '@pages/BO/catalog/stocks';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_stocks_displayProductsBelowLowStock';

// Simple filter stocks
describe('BO - Catalog - Stocks : Display products below low stock level first', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  let productQuantity: number;

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

  it('should go to \'Catalog > Stocks\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.stocksLink,
    );
    await stocksPage.closeSfToolBar(page);

    const pageTitle = await stocksPage.getPageTitle(page);
    expect(pageTitle).to.contains(stocksPage.pageTitle);
  });

  it('should get the number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProductsInList', baseContext);

    numberOfProducts = await stocksPage.getTotalNumberOfProducts(page);
    expect(numberOfProducts).to.be.above(0);
  });

  it('should get the quantity of the second product in the list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getQuantityOfSecondProduct', baseContext);

    productQuantity = parseInt(await stocksPage.getTextColumnFromTableStocks(page, 2, 'available'), 10);
  });

  it('should update the second product quantity to -300', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateProductQuantity', baseContext);

    // Update Quantity and check successful message
    const updateMessage = await stocksPage.updateRowQuantityWithInput(page, 2, -productQuantity);
    expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);

    // Check physical and available quantities of product after update
    const quantityToCheck = await stocksPage.getStockQuantityForProduct(page, 2);
    expect(quantityToCheck.physical).to.be.equal(0);
    expect(quantityToCheck.available).to.be.equal(0);
  });

  it('should check \'Display products below low stock level first\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDisplayProductBelowLowStock', baseContext);

    await stocksPage.setDisplayProductsBelowLowOfStock(page, true);
  });

  it('should check that the second product is displayed first', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSecondProductIsDisplayedFirst', baseContext);

    const productName = await stocksPage.getTextColumnFromTableStocks(page, 1, 'product_name');
    expect(productName).to.contain('Hummingbird notebook');
  });

  it('should check the available quantity for the first product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAvailableQuantity', baseContext);

    const availableQuantity = await stocksPage.getTextColumnFromTableStocks(page, 1, 'available');
    expect(availableQuantity).to.equal('0 !');
  });

  it('should check the physical quantity for the first product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkPhysicalQuantity', baseContext);

    const productQuantity = await stocksPage.getTextColumnFromTableStocks(page, 1, 'physical');
    expect(productQuantity).to.equal('0');
  });

  it('should check that the whole product line is red', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkLineIsRed', baseContext);

    const isLowStock = await stocksPage.isProductLowStock(page, 1);
    expect(isLowStock).to.equal(true);
  });

  it('should uncheck \'Display products below low stock level first\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uncheckDisplayProductBelowLowStock', baseContext);

    await stocksPage.setDisplayProductsBelowLowOfStock(page, false);
  });

  it('should check that the edited product is displayed second', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProduct', baseContext);

    const productName = await stocksPage.getTextColumnFromTableStocks(page, 1, 'product_name');
    expect(productName).to.not.contain('Hummingbird notebook');
  });

  it('should check that the whole product line is not red', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkLineIsNotRed', baseContext);

    const isLowStock = await stocksPage.isProductLowStock(page, 1);
    expect(isLowStock).to.equal(false);
  });

  // @todo : https://github.com/PrestaShop/PrestaShop/issues/33681
  it.skip('should check that the products are sorted by ID desc', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductSortedByIDDesc', baseContext);

    const table = await stocksPage.getAllRowsColumnContent(page, 'product_id');

    const tableFloat: number[] = table.map((text: string): number => parseInt(text, 10));
    const expectedResult: number[] = await basicHelper.sortArrayNumber(tableFloat);
    expect(tableFloat).to.deep.equal(expectedResult.reverse());
  });
});
