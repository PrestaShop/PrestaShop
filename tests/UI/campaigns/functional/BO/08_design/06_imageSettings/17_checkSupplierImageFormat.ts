import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boBrandsPage,
  boDashboardPage,
  boImageSettingsPage,
  boLoginPage,
  boSuppliersCreatePage,
  boSuppliersPage,
  type BrowserContext,
  FakerSupplier,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_imageSettings_checkSupplierImageFormat';

describe('BO - Design - Image Settings : Check suppliers image format', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idSupplier: number = 0;

  const supplierDataJPG: FakerSupplier = new FakerSupplier({
    logo: 'logoJPG.jpg',
  });
  const supplierDataPNG: FakerSupplier = new FakerSupplier({
    logo: 'logoPNG.png',
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await Promise.all([
      utilsFile.generateImage(supplierDataJPG.logo),
      utilsFile.generateImage(supplierDataPNG.logo),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await Promise.all([
      utilsFile.deleteFile(supplierDataJPG.logo),
      utilsFile.deleteFile(supplierDataPNG.logo),
    ]);
  });

  describe('Enable WebP for image generation', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Design > Image Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToImageSettingsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.imageSettingsLink,
      );
      await boImageSettingsPage.closeSfToolBar(page);

      const pageTitle = await boImageSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boImageSettingsPage.pageTitle);
    });

    it('should enable WebP image format', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableWebP', baseContext);

      const result = await boImageSettingsPage.setImageFormatToGenerateChecked(page, 'webp', true);
      expect(result).to.be.eq(boImageSettingsPage.messageSettingsUpdated);
    });

    it('should check image generation options', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkImageGenerationOptions', baseContext);

      // JPEG/PNG should be checked
      const jpegChecked = await boImageSettingsPage.isImageFormatToGenerateChecked(page, 'jpg');
      expect(jpegChecked).to.eq(true);

      // JPEG/PNG should be checked
      const jpegDisabled = await boImageSettingsPage.isImageFormatToGenerateDisabled(page, 'jpg');
      expect(jpegDisabled).to.eq(true);

      // WebP should be checked
      const webpChecked = await boImageSettingsPage.isImageFormatToGenerateChecked(page, 'webp');
      expect(webpChecked).to.eq(true);
    });
  });

  [
    {
      supplier: supplierDataPNG,
      extOriginal: 'png',
      extImageType: 'png',
    },
    {
      supplier: supplierDataJPG,
      extOriginal: 'jpg',
      extImageType: 'jpg',
    },
  ].forEach((arg: {supplier: FakerSupplier, extOriginal: string, extImageType: string}) => {
    describe(`Image Generation - Supplier - Image Format : ${arg.extOriginal.toUpperCase()}`, async () => {
      it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToBrandsPage${arg.extOriginal}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.brandsAndSuppliersLink,
        );
        await boBrandsPage.closeSfToolBar(page);

        const pageTitle = await boBrandsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boBrandsPage.pageTitle);
      });

      it('should go to \'Suppliers\' tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToSuppliersTab${arg.extOriginal}`, baseContext);

        await boBrandsPage.goToSubTabSuppliers(page);

        const pageTitle = await boSuppliersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boSuppliersPage.pageTitle);
      });

      it('should click on \'Add new supplier\' button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnNewSupplierButton${arg.extOriginal}`, baseContext);

        await boSuppliersPage.goToAddNewSupplierPage(page);

        const pageTitle = await boSuppliersCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boSuppliersCreatePage.pageTitle);
      });

      it('should create a supplier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createSupplier${arg.extOriginal}`, baseContext);

        const result = await boSuppliersCreatePage.createEditSupplier(page, arg.supplier);
        expect(result).to.equal(boSuppliersPage.successfulCreationMessage);
      });

      it('should search for the new supplier and fetch the ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchCreatedSupplier${arg.extOriginal}`, baseContext);

        await boSuppliersPage.resetFilter(page);
        await boSuppliersPage.filterTable(page, 'input', 'name', arg.supplier.name);

        const textColumn = await boSuppliersPage.getTextColumnFromTableSupplier(page, 1, 'name');
        expect(textColumn).to.contains(arg.supplier.name);

        idSupplier = parseInt(await boSuppliersPage.getTextColumnFromTableSupplier(page, 1, 'id_supplier'), 10);
      });

      it('should check that images are generated', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkSupplierImages${arg.extOriginal}`, baseContext);

        // Check the original file
        const pathImageOrigJPG: string = `${utilsFile.getRootPath()}/img/su/${idSupplier}.jpg`;

        const fileExistsOrigJPG = await utilsFile.doesFileExist(pathImageOrigJPG);
        expect(fileExistsOrigJPG, `The file ${pathImageOrigJPG} doesn't exist!`).to.eq(true);

        const imageTypeOrigJPG = await utilsFile.getFileType(pathImageOrigJPG);
        expect(imageTypeOrigJPG).to.be.eq(arg.extImageType);

        // Check the imageFormat file
        const pathImageJPG: string = `${utilsFile.getRootPath()}/img/su/${idSupplier}-small_default.jpg`;

        const fileExistsJPG = await utilsFile.doesFileExist(pathImageJPG);
        expect(fileExistsJPG, `The file ${pathImageJPG} doesn't exist!`).to.eq(true);

        const imageTypeJPG = await utilsFile.getFileType(pathImageJPG);
        expect(imageTypeJPG).to.be.eq(arg.extOriginal);

        // Check the WebP file
        const pathImageWEBP: string = `${utilsFile.getRootPath()}/img/su/${idSupplier}-small_default.webp`;

        const fileExistsWEBP = await utilsFile.doesFileExist(pathImageWEBP);
        expect(fileExistsWEBP, `The file ${pathImageWEBP} doesn't exist!`).to.eq(true);

        const imageTypeWEBP = await utilsFile.getFileType(pathImageWEBP);
        expect(imageTypeWEBP).to.be.eq('webp');
      });

      // Post-condition: Remove supplier
      it('should delete the supplier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteSupplier${arg.extOriginal}`, baseContext);

        const textResult = await boSuppliersPage.deleteSupplier(page, 1);
        expect(textResult).to.equal(boSuppliersPage.successfulDeleteMessage);
      });
    });
  });
});
