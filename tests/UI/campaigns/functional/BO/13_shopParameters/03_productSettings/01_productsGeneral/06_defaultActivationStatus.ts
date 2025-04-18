import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductSettingsPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsGeneral_defaultActivationStatus';

/*
Enable default activation status
Check that a new product is online by default
Disable default activation status
Check that a new product is offline by default
 */
describe('BO - Shop Parameters - Product Settings : Enable/Disable default activation status', async () => {
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

  describe('Enable/Disable default activation status', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    const tests = [
      {args: {action: 'enable', enable: true}},
      {args: {action: 'disable', enable: false}},
    ];

    tests.forEach((test, index: number) => {
      it('should go to \'Shop parameters > Product Settings\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.shopParametersParentLink,
          boDashboardPage.productSettingsLink,
        );
        await boProductSettingsPage.closeSfToolBar(page);

        const pageTitle = await boProductSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
      });

      it(`should ${test.args.action} default activation status`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.action}DefaultActivationStatus`,
          baseContext,
        );

        const result = await boProductSettingsPage.setDefaultActivationStatus(page, test.args.enable);
        expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

        await boProductSettingsPage.goToSubMenu(
          page,
          boProductSettingsPage.catalogParentLink,
          boProductSettingsPage.productsLink,
        );

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should click on \'New product\' button and check new product modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductButton${index}`, baseContext);

        const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.be.equal(true);
      });

      it('should select product type and create new product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

        await boProductsPage.selectProductType(page, 'standard');
        await boProductsPage.clickOnAddNewProduct(page);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should check the new product online status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddProductPage${index}`, baseContext);

        const online = await boProductsCreatePage.getProductStatus(page);
        expect(online).to.be.equal(test.args.enable);
      });
    });
  });
});
