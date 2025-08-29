// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  boProductsPage,
  type BrowserContext,
  dataModules,
  FakerProduct,
  foClassicCategoryPage,
  foClassicHomePage,
  modPsFacetedsearchBoMain,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_facetedsearch_configuration_showUnavailableOutOfStockLastFieldConfiguration';

describe('Faceted search module: Show unavailable, out of stock last\'s field configuration', async () => {
  const productOutOfStock: FakerProduct = new FakerProduct({
    quantity: 0,
  });

  // PRE-TEST: Create a out of stock product
  createProductTest(productOutOfStock, `${baseContext}_preTest_0`);

  describe('Show unavailable, out of stock last\'s field configuration', async () => {
    let browserContext: BrowserContext;
    let page: Page;
    let idProduct: number;

    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
      await utilsFile.deleteFile('module.zip');
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should filter list by \'product_name\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterProductName', baseContext);

      await boProductsPage.filterProducts(page, 'product_name', productOutOfStock.name, 'input');

      const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.eq(1);

      idProduct = await boProductsPage.getTextColumn(page, 'id_product', 1) as number;
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psFacetedSearch.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psFacetedSearch);
      expect(isModuleVisible).to.be.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psFacetedSearch.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psFacetedSearch.tag);

      const pageTitle = await modPsFacetedsearchBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsFacetedsearchBoMain.pageSubTitle);
    });

    it('should check the switch "Show unavailable, out of stock last" is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDisabled', baseContext);

      const isShowProductsFromSubcategoriesChecked = await modPsFacetedsearchBoMain.isShowUnavailableOutOfStockLastChecked(page);
      expect(isShowProductsFromSubcategoriesChecked).to.equal(false);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      page = await modPsFacetedsearchBoMain.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the All products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPage', baseContext);

      await foClassicHomePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible).to.equal(true);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/36906
    it('should go the the second page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondPage', baseContext);

      await foClassicCategoryPage.goToNextPage(page);

      //const nthProduct = await foClassicCategoryPage.getNThChildFromIDProduct(page, idProduct);
      //expect(nthProduct).to.eq(null);
    });

    it('should return to the backoffice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToTheBO', baseContext);

      page = await foClassicCategoryPage.changePage(browserContext, 0);

      const pageTitle = await modPsFacetedsearchBoMain.getPageSubtitle(page);
      expect(pageTitle).to.equal(modPsFacetedsearchBoMain.pageSubTitle);
    });

    it('should enable the switch "Show unavailable, out of stock last"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableSwitch', baseContext);

      const textResult = await modPsFacetedsearchBoMain.setShowUnavailableOutOfStockLastValue(page, true);
      expect(textResult).to.equal(modPsFacetedsearchBoMain.settingsSavedMessage);

      const isShowProductsFromSubcategoriesChecked = await modPsFacetedsearchBoMain.isShowUnavailableOutOfStockLastChecked(page);
      expect(isShowProductsFromSubcategoriesChecked).to.equal(true);
    });

    it('should check the frontoffice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFrontOffice', baseContext);

      page = await foClassicHomePage.changePage(browserContext, 1);
      await foClassicHomePage.reloadPage(page);

      const productsNum = await foClassicCategoryPage.getNumberOfProductsDisplayed(page);
      expect(productsNum).to.gt(0);

      const nthProduct = await foClassicCategoryPage.getNThChildFromIDProduct(page, idProduct) as number;
      expect(nthProduct).to.eq(productsNum);
    });

    it('should reset the switch "Show unavailable, out of stock last"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetSwitch', baseContext);

      page = await foClassicHomePage.changePage(browserContext, 0);
      const textResult = await modPsFacetedsearchBoMain.setShowUnavailableOutOfStockLastValue(page, false);
      expect(textResult).to.equal(modPsFacetedsearchBoMain.settingsSavedMessage);

      const isShowProductsFromSubcategoriesChecked = await modPsFacetedsearchBoMain.isShowUnavailableOutOfStockLastChecked(page);
      expect(isShowProductsFromSubcategoriesChecked).to.equal(false);
    });
  });

  // POST-TEST: Delete a out of stock product
  deleteProductTest(productOutOfStock, `${baseContext}_postTest_0`);
});
