// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import productsPage from '@pages/BO/catalog/products';
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
// Import FO pages
import categoryPage from '@pages/FO/category';
import {homePage} from '@pages/FO/home';
import foProductPage from '@pages/FO/product';

// Import data
import Modules from '@data/demo/modules';
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_emailalerts_installation_uninstallAndInstallModule';

describe('Mail alerts module - Uninstall and install module', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idProduct: number;
  let nthProduct: number|null;

  const productOutOfStockNotAllowed: ProductData = new ProductData({
    name: 'Product Out of stock not allowed',
    type: 'standard',
    taxRule: 'No tax',
    tax: 0,
    quantity: 0,
    behaviourOutOfStock: 'Deny orders',
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('module.zip');
  });

  // Pre-condition : Create product out of stock not allowed
  createProductTest(productOutOfStockNotAllowed, `${baseContext}_preTest`);

  describe('BackOffice - Fetch the ID of the product', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should filter list by \'product_name\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterProductName', baseContext);

      await productsPage.filterProducts(page, 'product_name', productOutOfStockNotAllowed.name, 'input');

      const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.eq(1);

      idProduct = await productsPage.getTextColumn(page, 'id_product', 1) as number;
    });
  });

  describe('BackOffice - Uninstall Module', async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${Modules.psEmailAlerts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psEmailAlerts);
      expect(isModuleVisible).to.eq(true);
    });

    it('should display the uninstall modal and cancel it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModuleAndCancel', baseContext);

      const textResult = await moduleManagerPage.setActionInModule(page, Modules.psEmailAlerts, 'uninstall', true);
      expect(textResult).to.eq('');

      const isModuleVisible = await moduleManagerPage.isModuleVisible(page, Modules.psEmailAlerts);
      expect(isModuleVisible).to.eq(true);

      const isModalVisible = await moduleManagerPage.isModalActionVisible(page, Modules.psEmailAlerts, 'uninstall');
      expect(isModalVisible).to.eq(false);

      const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${Modules.psEmailAlerts.tag}/`);
      expect(dirExists).to.eq(true);
    });

    it('should uninstall the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psEmailAlerts, 'uninstall', false);
      expect(successMessage).to.eq(moduleManagerPage.uninstallModuleSuccessMessage(Modules.psEmailAlerts.tag));

      // Check the directory `modules/Modules.psEmailAlerts.tag`
      const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${Modules.psEmailAlerts.tag}/`);
      expect(dirExists).to.eq(true);
    });
  });

  describe('FrontOffice - Check that the module is not present', async () => {
    it('should go to Front Office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      page = await moduleManagerPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the All Products Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPage', baseContext);

      await homePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should go the the second page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondPage', baseContext);

      await categoryPage.goToNextPage(page);

      console.log(idProduct);

      nthProduct = await categoryPage.getNThChildFromIDProduct(page, idProduct);
      expect(nthProduct).to.not.eq(null);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await categoryPage.goToProductPage(page, nthProduct!);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(productOutOfStockNotAllowed.name.toUpperCase());

      const hasFlagOutOfStock = await foProductPage.hasProductFlag(page, 'out_of_stock');
      expect(hasFlagOutOfStock).to.be.equal(true);

      const hasBlockMailAlert = await foProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(false);
    });
  });

  describe('BackOffice - Install the module', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      page = await foProductPage.closePage(browserContext, page, 0);
      await moduleManagerPage.reloadPage(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it('should install the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psEmailAlerts, 'install', false);
      expect(successMessage).to.eq(moduleManagerPage.installModuleSuccessMessage(Modules.psEmailAlerts.tag));

      // Check the directory `modules/Modules.psEmailAlerts.tag`
      const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${Modules.psEmailAlerts.tag}/`);
      expect(dirExists).to.eq(true);
    });
  });

  describe('FrontOffice - Check that the module is present', async () => {
    it('should go to Front Office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      page = await moduleManagerPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the All Products Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPage', baseContext);

      await homePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageWithMailAlert', baseContext);

      await categoryPage.goToNextPage(page);
      await categoryPage.goToProductPage(page, nthProduct!);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(productOutOfStockNotAllowed.name.toUpperCase());

      const hasFlagOutOfStock = await foProductPage.hasProductFlag(page, 'out_of_stock');
      expect(hasFlagOutOfStock).to.be.equal(true);

      const hasBlockMailAlert = await foProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);
    });
  });

  deleteProductTest(productOutOfStockNotAllowed, `${baseContext}_postTest_0`);
});
