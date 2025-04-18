import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boContactsPage,
  boDashboardPage,
  boImageSettingsPage,
  boLoginPage,
  boStoresPage,
  boStoresCreatePage,
  type BrowserContext,
  FakerStore,
  foClassicHomePage,
  foClassicStoresPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_imageSettings_checkStoreImageFormat';

describe('BO - Design - Image Settings - Check store image format', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idStore: number = 0;

  const storeDataJPG: FakerStore = new FakerStore({
    picture: 'pictureJPG.jpg',
  });
  const storeDataPNG: FakerStore = new FakerStore({
    picture: 'picturePNG.png',
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await Promise.all([
      storeDataJPG.picture,
      storeDataPNG.picture,
    ].map(async (image: string|null) => {
      if (image) {
        await utilsFile.generateImage(image);
      }
    }));
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await Promise.all([
      storeDataJPG.picture,
      storeDataPNG.picture,
    ].map(async (image: string|null) => {
      if (image) {
        await utilsFile.deleteFile(image);
      }
    }));
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
      store: storeDataPNG,
      extOriginal: 'png',
      extGenerated: 'jpg',
      extImageType: 'png',
    },
    {
      store: storeDataJPG,
      extOriginal: 'jpg',
      extGenerated: 'jpg',
      extImageType: 'jpg',
    },
  ].forEach((arg: {store: FakerStore, extOriginal: string, extGenerated: string, extImageType: string}, index: number) => {
    describe(`Image Generation - Store - Image Format : ${arg.extOriginal.toUpperCase()}`, async () => {
      if (index) {
        it('should go to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToBoStores${arg.extOriginal}`, baseContext);

          await foClassicStoresPage.goToBO(page);

          const pageTitle = await boDashboardPage.getPageTitle(page);
          expect(pageTitle).to.contains(boDashboardPage.pageTitle);
        });
      }

      it('should go to \'Shop Parameters > Contact\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToContactPage${arg.extOriginal}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.shopParametersParentLink,
          boDashboardPage.contactLink,
        );
        await boContactsPage.closeSfToolBar(page);

        const pageTitle = await boContactsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boContactsPage.pageTitle);
      });

      it('should go to \'Stores\' tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToStoresTab${arg.extOriginal}`, baseContext);

        await boContactsPage.goToStoresPage(page);

        const pageTitle = await boStoresPage.getPageTitle(page);
        expect(pageTitle).to.contains(boStoresPage.pageTitle);
      });

      it('should click on \'Add new store\' button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnNewStoreButton${arg.extOriginal}`, baseContext);

        await boStoresPage.goToNewStorePage(page);

        const pageTitle = await boStoresCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boStoresCreatePage.pageTitleCreate);
      });

      it('should create a store', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createStore${arg.extOriginal}`, baseContext);

        const createMessage = await boStoresCreatePage.createEditStore(page, arg.store);
        expect(createMessage).to.contains(boStoresPage.successfulCreationMessage);
      });

      it('should search for the new store and fetch the ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchCreatedCategory${arg.extOriginal}`, baseContext);

        await boStoresPage.resetFilter(page);
        await boStoresPage.filterTable(
          page,
          'input',
          'sl!name',
          arg.store.name,
        );

        const textColumn = await boStoresPage.getTextColumn(page, 1, 'sl!name');
        expect(textColumn).to.contains(arg.store.name);

        idStore = parseInt(await boStoresPage.getTextColumn(page, 1, 'id_store'), 10);
      });

      it('should check that images are generated', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductImages${arg.extOriginal}`, baseContext);

        // Check the original file
        const pathImageOrigJPG: string = `${utilsFile.getRootPath()}/img/st/${idStore}.jpg`;

        const fileExistsOrigJPG = await utilsFile.doesFileExist(pathImageOrigJPG);
        expect(fileExistsOrigJPG, `The file ${pathImageOrigJPG} doesn't exist!`).to.eq(true);

        const imageTypeOrigJPG = await utilsFile.getFileType(pathImageOrigJPG);
        expect(imageTypeOrigJPG).to.be.eq(arg.extImageType);

        // Check the imageFormat file
        const pathImageJPG: string = `${utilsFile.getRootPath()}/img/st/${idStore}-stores_default.jpg`;

        const fileExistsJPG = await utilsFile.doesFileExist(pathImageJPG);
        expect(fileExistsJPG, `The file ${pathImageJPG} doesn't exist!`).to.eq(true);

        const imageTypeJPG = await utilsFile.getFileType(pathImageJPG);
        expect(imageTypeJPG).to.be.eq(arg.extOriginal);

        // Check the WebP file
        const pathImageWEBP: string = `${utilsFile.getRootPath()}/img/st/${idStore}-stores_default.webp`;

        const fileExistsWEBP = await utilsFile.doesFileExist(pathImageWEBP);
        expect(fileExistsWEBP, `The file ${pathImageWEBP} doesn't exist!`).to.eq(true);

        const imageTypeWEBP = await utilsFile.getFileType(pathImageWEBP);
        expect(imageTypeWEBP).to.be.eq('webp');
      });

      it('should go to FO page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFo${arg.extOriginal}`, baseContext);

        page = await boStoresPage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to Stores page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAllProducts${arg.extOriginal}`, baseContext);

        await foClassicHomePage.goToFooterLink(page, 'Stores');

        const pageTitle = await foClassicStoresPage.getPageTitle(page);
        expect(pageTitle).to.be.eq(foClassicStoresPage.pageTitle);
      });

      it('should check that the main image of the store is a WebP', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkStoreImageMain${arg.extOriginal}`, baseContext);

        // Check the WebP file from the Stores page
        const quickViewImageMain = await foClassicStoresPage.getStoreImageMain(page, idStore);
        expect(quickViewImageMain).to.not.eq(null);

        await utilsFile.downloadFile(quickViewImageMain as string, 'image.img');

        const quickViewImageMainType = await utilsFile.getFileType('image.img');
        expect(quickViewImageMainType).to.be.eq('webp');

        await utilsFile.deleteFile('image.img');

        // Check the WebP file from the file system
        const pathImageWEBP: string = `${utilsFile.getRootPath()}/img/st/${idStore}-stores_default.webp`;

        const fileExistsWEBP = await utilsFile.doesFileExist(pathImageWEBP);
        expect(fileExistsWEBP, `The file ${pathImageWEBP} doesn't exist!`).to.eq(true);

        const imageTypeWEBP = await utilsFile.getFileType(pathImageWEBP);
        expect(imageTypeWEBP).to.be.eq('webp');
      });
    });
  });

  // Post-condition: Remove stores
  [
    {
      store: storeDataPNG,
      extension: 'png',
    },
    {
      store: storeDataJPG,
      extension: 'jpg',
    },
  ].forEach((arg: {store: FakerStore, extension: string}, index: number) => {
    describe(`POST-CONDITION : Remove store : ${arg.extension.toUpperCase()}`, async () => {
      if (!index) {
        it('should go to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToBoStores${arg.extension}`, baseContext);

          await foClassicStoresPage.goToBO(page);

          const pageTitle = await boDashboardPage.getPageTitle(page);
          expect(pageTitle).to.contains(boDashboardPage.pageTitle);
        });

        it('should go to \'Shop Parameters > Contact\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToContactPage${arg.extension}ForRemoval`, baseContext);

          await boDashboardPage.goToSubMenu(
            page,
            boDashboardPage.shopParametersParentLink,
            boDashboardPage.contactLink,
          );
          await boContactsPage.closeSfToolBar(page);

          const pageTitle = await boContactsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boContactsPage.pageTitle);
        });

        it('should go to \'Stores\' tab', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToStoresTab${arg.extension}ForRemoval`, baseContext);

          await boContactsPage.goToStoresPage(page);

          const pageTitle = await boStoresPage.getPageTitle(page);
          expect(pageTitle).to.contains(boStoresPage.pageTitle);
        });
      }

      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterForDelete${arg.extension}`, baseContext);

        await boStoresPage.resetFilter(page);
        await boStoresPage.filterTable(page, 'input', 'sl!name', arg.store.name);

        const storeName = await boStoresPage.getTextColumn(page, 1, 'sl!name');
        expect(storeName).to.contains(arg.store.name);
      });

      it('should delete store', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteStore${arg.extension}`, baseContext);

        const textResult = await boStoresPage.deleteStore(page, 1);
        expect(textResult).to.contains(boStoresPage.successfulDeleteMessage);
      });
    });
  });
});
