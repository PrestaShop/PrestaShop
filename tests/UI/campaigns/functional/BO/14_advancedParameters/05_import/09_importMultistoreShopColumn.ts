// Import utils
import testContext from '@utils/testContext';
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

import {expect} from 'chai';
import {
  boCategoriesPage,
  boDashboardPage,
  boImportPage,
  boLoginPage,
  boMultistorePage,
  boMultistoreShopPage,
  boMultistoreShopCreatePage,
  boMultistoreShopUrlCreatePage,
  type BrowserContext,
  FakerShop,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_import_importMultistoreShopColumn';

/*
Multistore: import categories whose "shop" column explicitly targets different
shops. Each category must end up associated only to the shop named in its row,
proving the shop column drives the per-shop split.
 */
describe('BO - Advanced Parameters - Import : Multistore - import with an explicit shop column', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let shopID: number = 0;
  let csvContent: string = '';

  const createShopData: FakerShop = new FakerShop({name: 'columnShop', shopGroup: 'Default', categoryRoot: 'Home'});
  const fileName: string = 'ui_import_ms_column.csv';
  const defaultShopCategory: string = 'UI Split Category Default';
  const secondShopCategory: string = 'UI Split Category Second';

  // Pre-condition: enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Columns follow the import field order; the "shop" column is the last category field.
    csvContent = 'Category ID;Active (0/1);Name *;Parent category;Root category (0/1);'
      + 'Description;Meta title;Meta description;URL rewritten;Image URL;ID / Name of the store\n'
      + `;1;${defaultShopCategory};Home;0;;;;;;${global.INSTALL.SHOP_NAME}\n`
      + `;1;${secondShopCategory};Home;0;;;;;;${createShopData.name}\n`;
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

  describe('Import categories with an explicit shop column', async () => {
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

    it('should import the categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importCategories', baseContext);

      const uploadSuccessText = await boImportPage.uploadImportFile(page, 'Categories', fileName);
      expect(uploadSuccessText).to.contains(fileName);

      await boImportPage.goToImportNextStep(page);
      await boImportPage.startFileImport(page);

      const isCompleted = await boImportPage.getImportValidationMessage(page);
      expect(isCompleted, 'The import is not completed!').to.contains('Data imported');

      await boImportPage.closeImportModal(page);
    });
  });

  describe('Check the per-shop split', async () => {
    it('should go to \'Catalog > Categories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.categoriesLink,
      );

      const pageTitle = await boCategoriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
    });

    it('should see only the default-shop category in the default shop context', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultShop', baseContext);

      await boCategoriesPage.clickOnMultiStoreHeader(page);
      await boCategoriesPage.chooseShop(page, 1);

      await boCategoriesPage.resetFilter(page);
      await boCategoriesPage.filterCategories(page, 'input', 'name', defaultShopCategory);
      expect(await boCategoriesPage.getNumberOfElementInGrid(page)).to.be.eq(1);

      await boCategoriesPage.resetFilter(page);
      await boCategoriesPage.filterCategories(page, 'input', 'name', secondShopCategory);
      expect(await boCategoriesPage.getNumberOfElementInGrid(page)).to.be.eq(0);
    });

    it('should see only the second-shop category in the second shop context', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondShop', baseContext);

      await boCategoriesPage.clickOnMultiStoreHeader(page);
      await boCategoriesPage.chooseShop(page, 2);

      await boCategoriesPage.resetFilter(page);
      await boCategoriesPage.filterCategories(page, 'input', 'name', secondShopCategory);
      expect(await boCategoriesPage.getNumberOfElementInGrid(page)).to.be.eq(1);

      await boCategoriesPage.resetFilter(page);
      await boCategoriesPage.filterCategories(page, 'input', 'name', defaultShopCategory);
      expect(await boCategoriesPage.getNumberOfElementInGrid(page)).to.be.eq(0);

      await boCategoriesPage.resetFilter(page);
    });
  });

  describe('Delete the second shop', async () => {
    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToMultiStore', baseContext);

      await boCategoriesPage.clickOnMultiStoreHeader(page);
      await boCategoriesPage.chooseShop(page, 1);

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
