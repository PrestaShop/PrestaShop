// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import stocksTab from '@pages/BO/catalog/products/add/stocksTab';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_enableStockManagement';

describe('BO - Shop Parameters - Product Settings : Enable/Disable stock management', async () => {
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

  describe('Enable/Disable stock management', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    const tests = [
      {args: {action: 'disable', enable: false, isQuantityVisible: false}},
      {args: {action: 'enable', enable: true, isQuantityVisible: true}},
    ];

    tests.forEach((test, index: number) => {
      it('should go to \'Shop parameters > Product Settings\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage_${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.shopParametersParentLink,
          dashboardPage.productSettingsLink,
        );

        const pageTitle = await productSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productSettingsPage.pageTitle);
      });

      it(`should ${test.args.action} stock management`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}StockManagement`, baseContext);

        const result = await productSettingsPage.setEnableStockManagementStatus(page, test.args.enable);
        expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

        await productSettingsPage.goToSubMenu(
          page,
          productSettingsPage.catalogParentLink,
          productSettingsPage.productsLink,
        );
        await productsPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should click on new product button and go to new product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductPage${index}`, baseContext);

        const isModalVisible = await productsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.be.equal(true);

        await productsPage.selectProductType(page, 'standard');

        await productsPage.clickOnAddNewProduct(page);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should check the existence of quantity input', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkIsQuantityInput${test.args.action}`, baseContext);

        const isVisible = await stocksTab.isQuantityInputVisible(page);
        expect(isVisible).to.equal(test.args.isQuantityVisible);
      });
    });
  });
});
