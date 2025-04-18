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
  foClassicProductPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_emailalerts_installation_uninstallAndInstallModule';

describe('Mail alerts module - Uninstall and install module', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idProduct: number;
  let nthProduct: number|null;

  const productOutOfStockNotAllowed: FakerProduct = new FakerProduct({
    name: 'Product Out of stock not allowed',
    type: 'standard',
    taxRule: 'No tax',
    tax: 0,
    quantity: 0,
    behaviourOutOfStock: 'Deny orders',
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile('module.zip');
  });

  // Pre-condition : Create product out of stock not allowed
  createProductTest(productOutOfStockNotAllowed, `${baseContext}_preTest`);

  describe('BackOffice - Fetch the ID of the product', async () => {
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

      await boProductsPage.filterProducts(page, 'product_name', productOutOfStockNotAllowed.name, 'input');

      const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.eq(1);

      idProduct = await boProductsPage.getTextColumn(page, 'id_product', 1) as number;
    });
  });

  describe('BackOffice - Uninstall Module', async () => {
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

    it(`should search the module ${dataModules.psEmailAlerts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psEmailAlerts);
      expect(isModuleVisible).to.eq(true);
    });

    it('should display the uninstall modal and cancel it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModuleAndCancel', baseContext);

      const textResult = await boModuleManagerPage.setActionInModule(page, dataModules.psEmailAlerts, 'uninstall', true);
      expect(textResult).to.eq('');

      const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.psEmailAlerts);
      expect(isModuleVisible).to.eq(true);

      const isModalVisible = await boModuleManagerPage.isModalActionVisible(page, dataModules.psEmailAlerts, 'uninstall');
      expect(isModalVisible).to.eq(false);

      const dirExists = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/modules/${dataModules.psEmailAlerts.tag}/`);
      expect(dirExists).to.eq(true);
    });

    it('should uninstall the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psEmailAlerts, 'uninstall', false);
      expect(successMessage).to.eq(boModuleManagerPage.uninstallModuleSuccessMessage(dataModules.psEmailAlerts.tag));

      // Check the directory `modules/dataModules.psEmailAlerts.tag`
      const dirExists = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/modules/${dataModules.psEmailAlerts.tag}/`);
      expect(dirExists).to.eq(true);
    });
  });

  describe('FrontOffice - Check that the module is not present', async () => {
    it('should go to Front Office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoAfterUninstall', baseContext);

      page = await boModuleManagerPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the All Products Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPageAfterUninstall', baseContext);

      await foClassicHomePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should go the the second page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondPage', baseContext);

      await foClassicCategoryPage.goToNextPage(page);

      nthProduct = await foClassicCategoryPage.getNThChildFromIDProduct(page, idProduct);
      expect(nthProduct).to.not.eq(null);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foClassicCategoryPage.goToProductPage(page, nthProduct!);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(productOutOfStockNotAllowed.name.toUpperCase());

      const hasFlagOutOfStock = await foClassicProductPage.hasProductFlag(page, 'out_of_stock');
      expect(hasFlagOutOfStock).to.be.equal(true);

      const hasBlockMailAlert = await foClassicProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(false);
    });
  });

  describe('BackOffice - Install the module', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);
      await boModuleManagerPage.reloadPage(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it('should install the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'installModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psEmailAlerts, 'install', false);
      expect(successMessage).to.eq(boModuleManagerPage.installModuleSuccessMessage(dataModules.psEmailAlerts.tag));

      // Check the directory `modules/dataModules.psEmailAlerts.tag`
      const dirExists = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/modules/${dataModules.psEmailAlerts.tag}/`);
      expect(dirExists).to.eq(true);
    });
  });

  describe('FrontOffice - Check that the module is present', async () => {
    it('should go to Front Office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      page = await boModuleManagerPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the All Products Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProductsPage', baseContext);

      await foClassicHomePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageWithMailAlert', baseContext);

      await foClassicCategoryPage.goToNextPage(page);
      await foClassicCategoryPage.goToProductPage(page, nthProduct!);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(productOutOfStockNotAllowed.name.toUpperCase());

      const hasFlagOutOfStock = await foClassicProductPage.hasProductFlag(page, 'out_of_stock');
      expect(hasFlagOutOfStock).to.be.equal(true);

      const hasBlockMailAlert = await foClassicProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);
    });
  });

  deleteProductTest(productOutOfStockNotAllowed, `${baseContext}_postTest_0`);
});
