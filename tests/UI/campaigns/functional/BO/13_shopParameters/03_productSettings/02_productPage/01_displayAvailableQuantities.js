require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productSettingsPage = require('@pages/BO/shopParameters/productSettings');
const homePage = require('@pages/FO/home');
const productPage = require('@pages/FO/product');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_productSettings_displayAvailableQuantities';

let browserContext;
let page;

/*
Disable display available quantities on product page
Check that quantity is not displayed
Enable display available quantities on product page
Check that quantity is displayed
 */

describe('Display available quantities on the product page', async () => {
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

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.productSettingsLink,
    );

    await productSettingsPage.closeSfToolBar(page);

    const pageTitle = await productSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'disable', enable: false}},
    {args: {action: 'enable', enable: true}},
  ];

  tests.forEach((test, index) => {
    it(`should ${test.args.action} Display available quantities on the product page`, async function () {
      await testContext.addContextItem(this,
        'testIdentifier',
        `${test.args.action}DisplayAvailableQuantities`,
        baseContext,
      );

      const result = await productSettingsPage.setDisplayAvailableQuantitiesStatus(page, test.args.enable);
      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should check the product quantity on the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkQuantity${index}`, baseContext);

      page = await productSettingsPage.viewMyShop(page);

      await homePage.goToProductPage(page, 1);

      const quantityIsVisible = await productPage.isQuantityDisplayed(page);
      await expect(quantityIsVisible).to.be.equal(test.args.enable);
      page = await homePage.closePage(browserContext, page, 0);
    });
  });
});
