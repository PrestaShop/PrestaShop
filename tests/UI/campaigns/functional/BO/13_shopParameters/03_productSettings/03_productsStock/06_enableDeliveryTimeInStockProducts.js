require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const productSettingsPage = require('@pages/BO/shopParameters/productSettings');

// Import FO pages
const foProductPage = require('@pages/FO/product');
const foHomePage = require('@pages/FO/home');

const baseContext = 'functional_BO_shopParameters_productSettings_productsStock_enableDeliveryTimeInStockProducts';

let browserContext;
let page;

describe('BO - Shop Parameters - Product Settings : Enable delivery time in stocks products', async () => {
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

    const pageTitle = await productSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', enable: true, deliveryTimeText: '3-4 days'}},
    {args: {action: 'disable', enable: false, deliveryTimeText: ''}},
  ];

  tests.forEach((test, index) => {
    it(`should ${test.args.action} delivery time of in-stock products`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}StockManagement`, baseContext);

      const result = await productSettingsPage.setDeliveryTimeInStock(page, test.args.deliveryTimeText);
      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      page = await productSettingsPage.viewMyShop(page);

      await foHomePage.changeLanguage(page, 'en');
      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should check delivery time block visibility', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `deliveryTimeBlockVisible${index}`, baseContext);

      await foHomePage.goToProductPage(page, 4);
      const isDeliveryTimeBlockVisible = await foProductPage.isDeliveryInformationVisible(page);
      await expect(isDeliveryTimeBlockVisible).to.equal(test.args.enable);
    });

    if (test.args.enable) {
      it('should check delivery time text', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deliveryTimeBlockText${index}`, baseContext);

        const deliveryTimeText = await foProductPage.getDeliveryInformationText(page);
        await expect(deliveryTimeText).to.equal(test.args.deliveryTimeText);
      });
    }

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });
  });
});
