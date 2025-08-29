import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabStocksPage,
  boProductSettingsPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsStock_enableStockManagement';

describe('BO - Shop Parameters - Product Settings : Enable/Disable stock management', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Enable/Disable stock management', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    const tests = [
      {args: {action: 'disable', enable: false, isQuantityVisible: false}},
      {args: {action: 'enable', enable: true, isQuantityVisible: true}},
    ];

    tests.forEach((test, index: number) => {
      it('should go to \'Shop parameters > Product Settings\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage_${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.shopParametersParentLink,
          boDashboardPage.productSettingsLink,
        );

        const pageTitle = await boProductSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
      });

      it(`should ${test.args.action} stock management`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}StockManagement`, baseContext);

        const result = await boProductSettingsPage.setEnableStockManagementStatus(page, test.args.enable);
        expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

        await boProductSettingsPage.goToSubMenu(
          page,
          boProductSettingsPage.catalogParentLink,
          boProductSettingsPage.productsLink,
        );
        await boProductsPage.closeSfToolBar(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should click on new product button and go to new product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductPage${index}`, baseContext);

        const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.be.equal(true);

        await boProductsPage.selectProductType(page, 'standard');

        await boProductsPage.clickOnAddNewProduct(page);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should check the existence of quantity input', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkIsQuantityInput${test.args.action}`, baseContext);

        const isVisible = await boProductsCreateTabStocksPage.isQuantityInputVisible(page);
        expect(isVisible).to.equal(test.args.isQuantityVisible);
      });
    });
  });
});
