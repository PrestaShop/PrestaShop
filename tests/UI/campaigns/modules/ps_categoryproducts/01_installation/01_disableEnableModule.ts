// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {productPage as foProductPage} from '@pages/FO/classic/product';
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

// Import data
import Modules from '@data/demo/modules';
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_categoryproducts_installation_disableEnableModule';

describe('Category products module - Disable/Enable module', async () => {
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

  describe('Disable/Enable module', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPageForEnable', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${Modules.psCategoryProducts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForDisable', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psCategoryProducts);
      expect(isModuleVisible).to.eq(true);
    });

    it('should disable and cancel the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableCancelModule', baseContext);

      await moduleManagerPage.setActionInModule(page, Modules.psCategoryProducts, 'disable', true);

      const isModuleVisible = await moduleManagerPage.isModuleVisible(page, Modules.psCategoryProducts);
      expect(isModuleVisible).to.eq(true);
    });

    it('should disable the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psCategoryProducts, 'disable');
      expect(successMessage).to.eq(moduleManagerPage.disableModuleSuccessMessage(Modules.psCategoryProducts.tag));
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOAfterDisable', baseContext);

      page = await moduleManagerPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageAfterDisable', baseContext);

      await homePage.goToProductPage(page, Products.demo_6.id);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(Products.demo_6.name.toUpperCase());
    });

    it('should check if the "Category Products" block is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotVisible', baseContext);

      const hasProductsBlock = await foProductPage.hasProductsBlock(page, 'categoryproducts');
      expect(hasProductsBlock).to.eq(false);
    });

    it('should return to the back office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnBO', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${Modules.psCategoryProducts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForEnable', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psCategoryProducts);
      expect(isModuleVisible).to.eq(true);
    });

    it('should enable the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psCategoryProducts, 'enable');
      expect(successMessage).to.eq(moduleManagerPage.enableModuleSuccessMessage(Modules.psCategoryProducts.tag));
    });

    it('should go to the front office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOAfterEnable', baseContext);

      page = await moduleManagerPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageAfterEnable', baseContext);

      await homePage.goToProductPage(page, Products.demo_6.id);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(Products.demo_6.name.toUpperCase());
    });

    it('should check if the "Category Products" block is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVisible', baseContext);

      const hasProductsBlock = await foProductPage.hasProductsBlock(page, 'categoryproducts');
      expect(hasProductsBlock).to.eq(true);
    });
  });
});
