// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boStockPage,
  type BrowserContext,
  dataProducts,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_stocks_updateQuantity';

// Update Quantity
describe('BO - Catalog - Stocks : Update Quantity', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  let firstProductQuantity: number = 0;
  let secondProductQuantity: number = 0;

  const productStock: any = dataProducts.demo_18;

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

  it('should get number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts', baseContext);

    numberOfProducts = await boStockPage.getTotalNumberOfProducts(page);
    expect(numberOfProducts).to.be.above(0);
  });

  [
    {args: {action: 'add', updateValue: 5}},
    {args: {action: 'subtract', updateValue: -5}},
  ].forEach((test, index: number) => {
    describe(`Update (${test.args.action}) quantity by setting input value`, async () => {
      it(`should filter by name '${productStock.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterStocks${test.args.action}`, baseContext);

        await boStockPage.simpleFilter(page, productStock.name);

        const numberOfProductsAfterFilter = await boStockPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);

        const textColumn = await boStockPage.getTextColumnFromTableStocks(page, 1, 'product_name');
        expect(productStock.name).to.contains(textColumn);

        // Get physical and available quantities of product
        productStock.stocks = {
          physical: parseInt(await boStockPage.getTextColumnFromTableStocks(page, 1, 'physical'), 10),
          available: parseInt(await boStockPage.getTextColumnFromTableStocks(page, 1, 'available'), 10),
        };

        expect(productStock.stocks.physical).to.be.above(0);
        expect(productStock.stocks.available).to.be.above(0);
      });

      it(`should ${test.args.action} quantity by setting input value`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ToQuantity`, baseContext);

        // Update Quantity and check successful message
        const updateMessage = await boStockPage.updateRowQuantityWithInput(page, 1, test.args.updateValue);
        expect(updateMessage).to.contains(boStockPage.successfulUpdateMessage);
      });

      it('should check physical and available quantity', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}CheckQuantity`, baseContext);

        // Check physical and available quantities of product after update
        const quantityToCheck = await boStockPage.getStockQuantityForProduct(page, 1);

        expect(quantityToCheck.physical).to.be.equal(productStock.stocks.physical + test.args.updateValue);
        productStock.stocks.physical = quantityToCheck.physical;

        expect(quantityToCheck.available).to.be.equal(productStock.stocks.available + test.args.updateValue);
        productStock.stocks.available = quantityToCheck.available;
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterStocks${index}`, baseContext);

        const numberOfProductsAfterReset = await boStockPage.resetFilter(page);
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

        await boStockPage.simpleFilter(page, productStock.name);

        const numberOfProductsAfterFilter = await boStockPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);

        const textColumn = await boStockPage.getTextColumnFromTableStocks(page, 1, 'product_name');
        expect(productStock.name).to.contains(textColumn);

        // Get physical and available quantities of product
        productStock.stocks = {
          physical: parseInt(await boStockPage.getTextColumnFromTableStocks(page, 1, 'physical'), 10),
          available: parseInt(await boStockPage.getTextColumnFromTableStocks(page, 1, 'available'), 10),
        };

        expect(productStock.stocks.physical).to.be.above(0);
        expect(productStock.stocks.available).to.be.above(0);
      });

      it(`should ${test.args.action} quantity by using the arrow up/down buttons`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ToQuantity2`, baseContext);

        // Update Quantity and check successful message
        const updateMessage = await boStockPage.updateRowQuantityWithArrowUpDownButtons(page,
          1,
          test.args.updateValue,
          test.args.direction,
        );
        expect(updateMessage).to.contains(boStockPage.successfulUpdateMessage);
      });

      it('should check physical and available quantity', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}CheckQuantity2`, baseContext);

        // Check physical and available quantities of product after update
        const quantityToCheck = await boStockPage.getStockQuantityForProduct(page, 1);

        expect(quantityToCheck.physical).to.be.equal(productStock.stocks.physical + test.args.updateValue);
        productStock.stocks.physical = quantityToCheck.physical;

        expect(quantityToCheck.available).to.be.equal(productStock.stocks.available + test.args.updateValue);
        productStock.stocks.available = quantityToCheck.available;
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterStocks${index}2`, baseContext);

        const numberOfProductsAfterReset = await boStockPage.resetFilter(page);
        expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });
  });

  describe('Update quantity by using the number up/down and by writing in input', async () => {
    it('should add quantity of the first product by using the arrow up/down buttons', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityUp', baseContext);

      firstProductQuantity = parseInt(await boStockPage.getTextColumnFromTableStocks(page, 1, 'available'), 10);
      await boStockPage.setQuantityByArrowUpDown(page, 1, 6, 'up');
    });

    it('should add quantity of the second product by setting input value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityInput', baseContext);

      secondProductQuantity = parseInt(await boStockPage.getTextColumnFromTableStocks(page, 2, 'available'), 10);
      await boStockPage.setQuantityWithInput(page, 2, 5);
    });

    it('should click on \'Apply new quantity\' and check new quantities', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'applyNewQuantity', baseContext);

      const updateMessage = await boStockPage.clickOnApplyNewQuantity(page);
      expect(updateMessage).to.contains(boStockPage.successfulUpdateMessage);
    });

    it('should check the new quantity of the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstProductQuantity', baseContext);

      // Check physical and available quantities of product after update
      const quantityToCheck = await boStockPage.getStockQuantityForProduct(page, 1);

      expect(quantityToCheck.physical).to.be.equal(firstProductQuantity + 6);
      productStock.stocks.physical = quantityToCheck.physical;

      expect(quantityToCheck.available).to.be.equal(firstProductQuantity + 6);
      productStock.stocks.available = quantityToCheck.available;
    });

    it('should check the new quantity of the second product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondProductQuantity', baseContext);

      // Check physical and available quantities of product after update
      const quantityToCheck = await boStockPage.getStockQuantityForProduct(page, 2);

      expect(quantityToCheck.physical).to.be.equal(secondProductQuantity + 5);
      productStock.stocks.physical = quantityToCheck.physical;

      expect(quantityToCheck.available).to.be.equal(secondProductQuantity + 5);
      productStock.stocks.available = quantityToCheck.available;
    });
  });
});
