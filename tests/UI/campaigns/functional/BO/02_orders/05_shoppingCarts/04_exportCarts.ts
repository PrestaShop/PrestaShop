// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import shoppingCartsPage from '@pages/BO/orders/shoppingCarts';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_shoppingCarts_exportCarts';

/*
Scenario:
- Go to orders > shopping carts page
- Change pagination number to 300
- Export shopping carts
- Check that all data in shopping carts table exit in the csv file
- Go back to default pagination number
 */
describe('BO - Orders - Shopping carts: Export carts', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;

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

  it('should go to \'Orders > Shopping carts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.ordersParentLink,
      dashboardPage.shoppingCartsLink,
    );

    const pageTitle = await shoppingCartsPage.getPageTitle(page);
    expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
  });

  it('should change the items number to 300 per page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo300', baseContext);

    const numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
    expect(numberOfShoppingCarts).to.be.above(0);

    if (numberOfShoppingCarts >= 21) {
      await shoppingCartsPage.selectPaginationLimit(page, 300);
    }
  });

  it('should export carts to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportCarts', baseContext);

    filePath = await shoppingCartsPage.exportDataToCsv(page);

    const doesFileExist = await files.doesFileExist(filePath, 5000);
    expect(doesFileExist, 'Export of data has failed').to.eq(true);
  });

  it('should check existence of carts data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllCartsInCsvFile', baseContext);

    // Get number of orders
    const numberOfOrders = await shoppingCartsPage.getNumberOfElementInGrid(page);

    // Check each order in file
    for (let row = 1; row <= numberOfOrders; row++) {
      const cartInCsvFormat = await shoppingCartsPage.getCartInCsvFormat(page, row);
      const textExist = await files.isTextInFile(filePath, cartInCsvFormat, true, true);
      expect(textExist, `${cartInCsvFormat} was not found in the file`).to.eq(true);
    }
  });
});
