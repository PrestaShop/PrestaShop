// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boModuleManagerPage,
  dataModules,
  dataProducts,
  foClassicHomePage,
  foClassicProductPage,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_blockwishlist_installation_uninstallAndDeleteModule';

describe('Wishlist module - Uninstall and delete module', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile('module.zip');
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
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

  it(`should search the module ${dataModules.blockwishlist.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.blockwishlist);
    expect(isModuleVisible).to.eq(true);
  });

  it('should display the uninstall modal and cancel it', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModuleAndCancel', baseContext);

    const textResult = await boModuleManagerPage.setActionInModule(page, dataModules.blockwishlist, 'uninstall', true);
    expect(textResult).to.eq('');

    const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.blockwishlist);
    expect(isModuleVisible).to.eq(true);

    const isModalVisible = await boModuleManagerPage.isModalActionVisible(page, dataModules.blockwishlist, 'uninstall');
    expect(isModalVisible).to.eq(false);

    const dirExists = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/modules/${dataModules.blockwishlist.tag}/`);
    expect(dirExists).to.eq(true);
  });

  it('should uninstall the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

    const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.blockwishlist, 'uninstall', false, true);
    expect(successMessage).to.eq(boModuleManagerPage.uninstallModuleSuccessMessage(dataModules.blockwishlist.tag));

    // Check the directory `modules/dataModules.blockwishlist.tag`
    const dirExists = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/modules/${dataModules.blockwishlist.tag}/`);
    expect(dirExists).to.eq(false);
  });

  it('should view my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    page = await boModuleManagerPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should go the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

    await foClassicHomePage.goToProductPage(page, 1);

    const productInformations = await foClassicProductPage.getProductInformation(page);
    expect(productInformations.name).to.eq(dataProducts.demo_1.name);
  });

  it('should check if the button "Add to wishlist" is present', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkButtonAddToWoshlist', baseContext);

    const hasAddToWishlistButton = await foClassicProductPage.hasAddToWishlistButton(page);
    expect(hasAddToWishlistButton).to.equal(false);
  });

  describe(`POST-CONDITION : Install the module ${dataModules.blockwishlist.name}`, async () => {
    it('should go back to Back Office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToModulesManager', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should download the zip of the module '${dataModules.blockwishlist.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadModule', baseContext);

      await utilsFile.downloadFile(dataModules.blockwishlist.releaseZip, 'module.zip');

      const found = await utilsFile.doesFileExist('module.zip');
      expect(found).to.eq(true);
    });

    it(`should upload the module '${dataModules.blockwishlist.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uploadModule', baseContext);

      const successMessage = await boModuleManagerPage.uploadModule(page, 'module.zip');
      expect(successMessage).to.eq(boModuleManagerPage.uploadModuleSuccessMessage);
    });

    it('should close upload module modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

      const isModalNotVisible = await boModuleManagerPage.closeUploadModuleModal(page);
      expect(isModalNotVisible).to.eq(true);
    });

    it(`should search the module '${dataModules.blockwishlist.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkModulePresent', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.blockwishlist);
      expect(isModuleVisible, 'Module is not visible!').to.eq(true);
    });
  });
});
