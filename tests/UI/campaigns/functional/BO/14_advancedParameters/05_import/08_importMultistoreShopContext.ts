// Import utils
import testContext from '@utils/testContext';
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

import {expect} from 'chai';
import {
  boDashboardPage,
  boImportPage,
  boLoginPage,
  boMultistorePage,
  boMultistoreShopPage,
  boMultistoreShopCreatePage,
  boMultistoreShopUrlCreatePage,
  boProductsPage,
  type BrowserContext,
  FakerShop,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_import_importMultistoreShopContext';

/*
Multistore: with the BO context set to a specific shop, import products whose
"shop" column is empty. The products must be associated only to the shop that
was active during the import (the BO context), not to the other shops.
 */
describe('BO - Advanced Parameters - Import : Multistore - import in a specific shop context', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let shopID: number = 0;

  const createShopData: FakerShop = new FakerShop({name: 'importShop', shopGroup: 'Default', categoryRoot: 'Home'});
  const fileName: string = 'ui_import_ms_context.csv';
  const firstProduct: string = 'UI MS Context Product A';
  const secondProduct: string = 'UI MS Context Product B';
  const thirdProduct: string = 'UI MS Context Product C';
  // Columns follow the import field order; the "shop" column is intentionally left out (empty).
  const csvContent: string = 'Product ID;Active (0/1);Name *;Categories (x,y,z...);Price tax excluded\n'
    + `;1;${firstProduct};Home;10.00\n`
    + `;1;${secondProduct};Home;20.00\n`
    + `;1;${thirdProduct};Home;30.00\n`;

  // Pre-condition: enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await utilsFile.createFile('.', fileName, csvContent);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile(fileName);
  });

  describe('Create a second shop', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );
      await boMultistorePage.closeSfToolBar(page);

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should create the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShop', baseContext);

      await boMultistorePage.goToNewShopPage(page);

      const textResult = await boMultistoreShopCreatePage.setShop(page, createShopData);
      expect(textResult).to.contains(boMultistorePage.successfulCreationMessage);

      shopID = parseInt(await boMultistoreShopPage.getTextColumn(page, 1, 'id_shop'), 10);
    });

    it('should set the shop URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setShopUrl', baseContext);

      await boMultistoreShopPage.filterTable(page, 'a!name', createShopData.name);
      await boMultistoreShopPage.goToSetURL(page, 1);

      const textResult = await boMultistoreShopUrlCreatePage.setVirtualUrl(page, createShopData.name);
      expect(textResult).to.contains(boMultistoreShopUrlCreatePage.successfulCreationMessage);
    });
  });

  describe('Import products in the new shop context', async () => {
    it('should go to \'Advanced Parameters > Import\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToImportPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.importLink,
      );
      await boImportPage.closeSfToolBar(page);

      const pageTitle = await boImportPage.getPageTitle(page);
      expect(pageTitle).to.contains(boImportPage.pageTitle);
    });

    it('should switch the BO context to the new shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'switchToNewShop', baseContext);

      await boImportPage.clickOnMultiStoreHeader(page);
      await boImportPage.chooseShop(page, 2);

      const shopName = await boImportPage.getShopName(page);
      expect(shopName).to.eq(createShopData.name);
    });

    it('should import the products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importProducts', baseContext);

      const uploadSuccessText = await boImportPage.uploadImportFile(page, 'Products', fileName);
      expect(uploadSuccessText).to.contains(fileName);

      await boImportPage.goToImportNextStep(page);
      await boImportPage.startFileImport(page);

      const isCompleted = await boImportPage.getImportValidationMessage(page);
      expect(isCompleted, 'The import is not completed!').to.contains('Data imported');

      await boImportPage.closeImportModal(page);
    });
  });

  describe('Check shop-specific visibility', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should find the imported products in the new shop context', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'findInNewShop', baseContext);

      await boProductsPage.clickOnMultiStoreHeader(page);
      await boProductsPage.chooseShop(page, 2);

      await boProductsPage.resetFilter(page);
      await boProductsPage.filterProducts(page, 'product_name', firstProduct, 'input');
      expect(await boProductsPage.getNumberOfProductsFromList(page)).to.be.eq(1);
    });

    it('should not find the imported products in the default shop context', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'notInDefaultShop', baseContext);

      await boProductsPage.clickOnMultiStoreHeader(page);
      await boProductsPage.chooseShop(page, 1);

      await boProductsPage.resetFilter(page);
      await boProductsPage.filterProducts(page, 'product_name', firstProduct, 'input');
      expect(await boProductsPage.getNumberOfProductsFromList(page)).to.be.eq(0);

      await boProductsPage.resetFilter(page);
    });
  });

  describe('Delete the second shop', async () => {
    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToMultiStore', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );
      await boMultistorePage.closeSfToolBar(page);

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should delete the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShop', baseContext);

      await boMultistorePage.goToShopPage(page, shopID);
      await boMultistoreShopPage.filterTable(page, 'a!name', createShopData.name);

      const textResult = await boMultistoreShopPage.deleteShop(page, 1);
      expect(textResult).to.contains(boMultistoreShopPage.successfulDeleteMessage);
    });
  });

  // Post-condition: disable multistore
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
