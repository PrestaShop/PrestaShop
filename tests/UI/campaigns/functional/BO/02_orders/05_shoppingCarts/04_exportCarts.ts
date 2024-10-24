// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boShoppingCartsPage,
  type BrowserContext,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  it('should go to \'Orders > Shopping carts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.ordersParentLink,
      boDashboardPage.shoppingCartsLink,
    );

    const pageTitle = await boShoppingCartsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boShoppingCartsPage.pageTitle);
  });

  it('should export carts to a csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportCarts', baseContext);

    filePath = await boShoppingCartsPage.exportDataToCsv(page);

    const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
    expect(doesFileExist, 'Export of data has failed').to.eq(true);
  });

  it('should check existence of carts data in csv file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAllCartsInCsvFile', baseContext);

    // Get number of orders
    const numberOfOrders = await boShoppingCartsPage.getNumberOfElementInGrid(page);

    // Check each order in file
    for (let row = 1; row <= numberOfOrders; row++) {
      const cartInCsvFormat = await boShoppingCartsPage.getCartInCsvFormat(page, row);
      const textExist = await utilsFile.isTextInFile(filePath, cartInCsvFormat, true, true);
      expect(textExist, `${cartInCsvFormat} was not found in the file`).to.eq(true);
    }
  });
});
