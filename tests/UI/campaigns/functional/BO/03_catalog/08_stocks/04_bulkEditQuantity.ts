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

const baseContext: string = 'functional_BO_catalog_stocks_bulkEditQuantity';

/*
Filter by Products demo_8
Add and subtract quantity for all products in list and check result
 */
describe('BO - Catalog - Stocks : Bulk edit quantity', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

  const stocks: any = {};

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

  describe('Bulk edit quantity by setting input value', async () => {
    it(`should filter by name '${dataProducts.demo_8.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateQuantities', baseContext);

      await boStockPage.simpleFilter(page, dataProducts.demo_8.name);

      const numberOfProductsAfterFilter = await boStockPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);

      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const textColumn = await boStockPage.getTextColumnFromTableStocks(page, i, 'product_name');
        expect(textColumn).to.contains(dataProducts.demo_8.name);

        // Get physical and available quantities of product
        stocks[`product${i}`] = {
          physical: parseInt(await boStockPage.getTextColumnFromTableStocks(page, i, 'physical'), 10),
          available: parseInt(await boStockPage.getTextColumnFromTableStocks(page, i, 'available'), 10),
        };

        expect(stocks[`product${i}`].physical).to.be.above(0);
        expect(stocks[`product${i}`].available).to.be.above(0);
      }
    });

    [
      {args: {action: 'add', updateValue: 100}},
      {args: {action: 'subtract', updateValue: -100}},
    ].forEach((test) => {
      it(`should ${test.args.action} to quantities by setting input value`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ToQuantities`, baseContext);

        // Update quantity and check successful message
        const updateMessage = await boStockPage.bulkEditQuantityWithInput(page, test.args.updateValue);
        expect(updateMessage).to.contains(boStockPage.successfulUpdateMessage);

        const numberOfProductsInList = await boStockPage.getNumberOfProductsFromList(page);

        // Check physical and available quantities of product after update
        for (let i = 1; i <= numberOfProductsInList; i++) {
          const quantityToCheck = await boStockPage.getStockQuantityForProduct(page, i);

          expect(quantityToCheck.physical).to.be.equal(stocks[`product${i}`].physical + test.args.updateValue);
          stocks[`product${i}`].physical = quantityToCheck.physical;

          expect(quantityToCheck.available).to.be.equal(stocks[`product${i}`].available + test.args.updateValue);
          stocks[`product${i}`].available = quantityToCheck.available;
        }
      });
    });
  });

  describe('Bulk edit quantity by using the arrow up/down', async () => {
    [
      {args: {action: 'add', updateValue: 5, direction: 'up'}},
      {args: {action: 'subtract', updateValue: -5, direction: 'down'}},
    ].forEach((test) => {
      it(`should ${test.args.action} quantity by using the arrow up/down`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ToQuantities2`, baseContext);

        // Update quantity and check successful message
        const updateMessage = await boStockPage.bulkEditQuantityWithArrowUpDownButtons(page,
          test.args.updateValue,
          test.args.direction,
        );
        expect(updateMessage).to.contains(boStockPage.successfulUpdateMessage);

        const numberOfProductsInList = await boStockPage.getNumberOfProductsFromList(page);

        // Check physical and available quantities of product after update
        for (let i = 1; i <= numberOfProductsInList; i++) {
          const quantityToCheck = await boStockPage.getStockQuantityForProduct(page, i);

          expect(quantityToCheck.physical).to.be.equal(stocks[`product${i}`].physical + test.args.updateValue);
          stocks[`product${i}`].physical = quantityToCheck.physical;

          expect(quantityToCheck.available).to.be.equal(stocks[`product${i}`].available + test.args.updateValue);
          stocks[`product${i}`].available = quantityToCheck.available;
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterEditQuantities', baseContext);

      const numberOfProductsAfterReset = await boStockPage.resetFilter(page);
      expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });
});
