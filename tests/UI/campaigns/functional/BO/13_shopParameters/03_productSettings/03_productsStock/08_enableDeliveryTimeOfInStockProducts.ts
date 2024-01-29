// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
// Import FO pages
import {homePage as foHomePage} from '@pages/FO/classic/home';
import foProductPage from '@pages/FO/classic/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_enableDeliveryTimeOfInStockProducts';

describe('BO - Shop Parameters - Product Settings : Enable delivery time in stocks products', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
    expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', enable: true, deliveryTimeText: '3-4 days'}},
    {args: {action: 'disable', enable: false, deliveryTimeText: ''}},
  ];

  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} delivery time of in-stock products`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}StockManagement`, baseContext);

      const result = await productSettingsPage.setDeliveryTimeInStock(page, test.args.deliveryTimeText);
      expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      page = await productSettingsPage.viewMyShop(page);
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should check delivery time block visibility', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `deliveryTimeBlockVisible${index}`, baseContext);

      await foHomePage.goToProductPage(page, 4);

      const isDeliveryTimeBlockVisible = await foProductPage.isDeliveryInformationVisible(page);
      expect(isDeliveryTimeBlockVisible).to.equal(test.args.enable);
    });

    if (test.args.enable) {
      it('should check delivery time text', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deliveryTimeBlockText${index}`, baseContext);

        const deliveryTimeText = await foProductPage.getDeliveryInformationText(page);
        expect(deliveryTimeText).to.equal(test.args.deliveryTimeText);
      });
    }

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });
  });
});
