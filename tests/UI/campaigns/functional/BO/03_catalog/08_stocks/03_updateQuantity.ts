// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import stocksPage from '@pages/BO/catalog/stocks';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_stocks_updateQuantity';

// Update Quantity
describe('BO - Catalog - Stocks : Update Quantity', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  let firstProductQuantity: number = 0;
  let secondProductQuantity: number = 0;

  const productStock: any = Products.demo_18;

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

  it('should get number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts', baseContext);

    numberOfProducts = await stocksPage.getTotalNumberOfProducts(page);
    expect(numberOfProducts).to.be.above(0);
  });

  [
    {args: {action: 'add', updateValue: 5}},
    {args: {action: 'subtract', updateValue: -5}},
  ].forEach((test, index: number) => {
    describe(`Update (${test.args.action}) quantity by setting input value`, async () => {
      it(`should filter by name '${productStock.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterStocks${test.args.action}`, baseContext);

        await stocksPage.simpleFilter(page, productStock.name);

        const numberOfProductsAfterFilter = await stocksPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);

        const textColumn = await stocksPage.getTextColumnFromTableStocks(page, 1, 'product_name');
        expect(productStock.name).to.contains(textColumn);

        // Get physical and available quantities of product
        productStock.stocks = {
          physical: parseInt(await stocksPage.getTextColumnFromTableStocks(page, 1, 'physical'), 10),
          available: parseInt(await stocksPage.getTextColumnFromTableStocks(page, 1, 'available'), 10),
        };

        expect(productStock.stocks.physical).to.be.above(0);
        expect(productStock.stocks.available).to.be.above(0);
      });

      it(`should ${test.args.action} quantity by setting input value`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ToQuantity`, baseContext);

        // Update Quantity and check successful message
        const updateMessage = await stocksPage.updateRowQuantityWithInput(page, 1, test.args.updateValue);
        expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);
      });

      it('should check physical and available quantity', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}CheckQuantity`, baseContext);

        // Check physical and available quantities of product after update
        const quantityToCheck = await stocksPage.getStockQuantityForProduct(page, 1);

        expect(quantityToCheck.physical).to.be.equal(productStock.stocks.physical + test.args.updateValue);
        productStock.stocks.physical = quantityToCheck.physical;

        expect(quantityToCheck.available).to.be.equal(productStock.stocks.available + test.args.updateValue);
        productStock.stocks.available = quantityToCheck.available;
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterStocks${index}`, baseContext);

        const numberOfProductsAfterReset = await stocksPage.resetFilter(page);
        expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });
  });

  [
    {args: {action: 'add', updateValue: 5, direction: 'up'}},
    {args: {action: 'subtract', updateValue: -5, direction: 'down'}},
  ].forEach((test, index: number) => {
    describe(`Update (${test.args.action}) quantity by using the number up/down`, async () => {
      it(`should filter by name '${productStock.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterStocks2${test.args.action}`, baseContext);

        await stocksPage.simpleFilter(page, productStock.name);

        const numberOfProductsAfterFilter = await stocksPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);

        const textColumn = await stocksPage.getTextColumnFromTableStocks(page, 1, 'product_name');
        expect(productStock.name).to.contains(textColumn);

        // Get physical and available quantities of product
        productStock.stocks = {
          physical: parseInt(await stocksPage.getTextColumnFromTableStocks(page, 1, 'physical'), 10),
          available: parseInt(await stocksPage.getTextColumnFromTableStocks(page, 1, 'available'), 10),
        };

        expect(productStock.stocks.physical).to.be.above(0);
        expect(productStock.stocks.available).to.be.above(0);
      });

      it(`should ${test.args.action} quantity by using the arrow up/down buttons`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ToQuantity2`, baseContext);

        // Update Quantity and check successful message
        const updateMessage = await stocksPage.updateRowQuantityWithArrowUpDownButtons(page,
          1,
          test.args.updateValue,
          test.args.direction,
        );
        expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);
      });

      it('should check physical and available quantity', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}CheckQuantity2`, baseContext);

        // Check physical and available quantities of product after update
        const quantityToCheck = await stocksPage.getStockQuantityForProduct(page, 1);

        expect(quantityToCheck.physical).to.be.equal(productStock.stocks.physical + test.args.updateValue);
        productStock.stocks.physical = quantityToCheck.physical;

        expect(quantityToCheck.available).to.be.equal(productStock.stocks.available + test.args.updateValue);
        productStock.stocks.available = quantityToCheck.available;
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterStocks${index}2`, baseContext);

        const numberOfProductsAfterReset = await stocksPage.resetFilter(page);
        expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });
  });

  describe('Update quantity by using the number up/down and by writing in input', async () => {
    it('should add quantity of the first product by using the arrow up/down buttons', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityUp', baseContext);

      firstProductQuantity = parseInt(await stocksPage.getTextColumnFromTableStocks(page, 1, 'available'), 10);
      await stocksPage.setQuantityByArrowUpDown(page, 1, 6, 'up');
    });

    it('should add quantity of the second product by setting input value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityInput', baseContext);

      secondProductQuantity = parseInt(await stocksPage.getTextColumnFromTableStocks(page, 2, 'available'), 10);
      await stocksPage.setQuantityWithInput(page, 2, 5);
    });

    it('should click on \'Apply new quantity\' and check new quantities', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'applyNewQuantity', baseContext);

      const updateMessage = await stocksPage.clickOnApplyNewQuantity(page);
      expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);
    });

    it('should check the new quantity of the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstProductQuantity', baseContext);

      // Check physical and available quantities of product after update
      const quantityToCheck = await stocksPage.getStockQuantityForProduct(page, 1);

      expect(quantityToCheck.physical).to.be.equal(firstProductQuantity + 6);
      productStock.stocks.physical = quantityToCheck.physical;

      expect(quantityToCheck.available).to.be.equal(firstProductQuantity + 6);
      productStock.stocks.available = quantityToCheck.available;
    });

    it('should check the new quantity of the second product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondProductQuantity', baseContext);

      // Check physical and available quantities of product after update
      const quantityToCheck = await stocksPage.getStockQuantityForProduct(page, 2);

      expect(quantityToCheck.physical).to.be.equal(secondProductQuantity + 5);
      productStock.stocks.physical = quantityToCheck.physical;

      expect(quantityToCheck.available).to.be.equal(secondProductQuantity + 5);
      productStock.stocks.available = quantityToCheck.available;
    });
  });
});
