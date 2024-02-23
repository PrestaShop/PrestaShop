// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import {installModule} from '@commonTests/BO/modules/moduleManager';

// Import pages
// Import BO pages
import productsPage from '@pages/BO/catalog/products';
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
// Import FO pages
import {categoryPage} from '@pages/FO/classic/category';
import {homePage} from '@pages/FO/classic/home';
import {productPage as foProductPage} from '@pages/FO/classic/product';

// Import data
import Modules from '@data/demo/modules';
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_emailalerts_installation_uninstallAndDeleteModule';

describe('Mail alerts module - Uninstall and delete module', async () => {
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

  createProductTest(productOutOfStockNotAllowed, `${baseContext}_preTest_0`);

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

  describe('BackOffice - Uninstall and delete module', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'resetModuleAndCancel', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psEmailAlerts, 'uninstall', false, true);
      expect(successMessage).to.eq(moduleManagerPage.uninstallModuleSuccessMessage(Modules.psEmailAlerts.tag));

      // Check the directory `modules/Modules.psEmailAlerts.tag`
      const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${Modules.psEmailAlerts.tag}/`);
      expect(dirExists).to.eq(false);
    });

    it('should go to Front Office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      page = await moduleManagerPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the category Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoryPage', baseContext);

      await homePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should go to the next page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoryPage2', baseContext);

      await categoryPage.goToNextPage(page);

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

  installModule(Modules.psEmailAlerts, `${baseContext}_postTest_0`);

  deleteProductTest(productOutOfStockNotAllowed, `${baseContext}_postTest_1`);
});
