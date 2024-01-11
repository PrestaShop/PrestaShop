// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import imageSettingsPage from '@pages/BO/design/imageSettings';
import contactPage from '@pages/BO/shopParameters/contact';
import storesPage from '@pages/BO/shopParameters/stores';
import createStoresPage from '@pages/BO/shopParameters/stores/add';
// Import FO pages
import {homePage} from '@pages/FO/home';
import {storesPage as storePage} from '@pages/FO/stores';

// Import data
import StoreData from '@data/faker/store';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_imageSettings_checkStoreImageFormat';

describe('BO - Design - Image Settings - Check store image format', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idStore: number = 0;

  const storeDataJPG: StoreData = new StoreData({
    picture: 'pictureJPG.jpg',
  });
  const storeDataPNG: StoreData = new StoreData({
    picture: 'picturePNG.png',
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await Promise.all([
      storeDataJPG.picture,
      storeDataPNG.picture,
    ].map(async (image: string|null) => {
      if (image) {
        await files.generateImage(image);
      }
    }));
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await Promise.all([
      storeDataJPG.picture,
      storeDataPNG.picture,
    ].map(async (image: string|null) => {
      if (image) {
        await files.deleteFile(image);
      }
    }));
  });

  describe('Enable WebP for image generation', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Design > Image Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToImageSettingsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.designParentLink,
        dashboardPage.imageSettingsLink,
      );
      await imageSettingsPage.closeSfToolBar(page);

      const pageTitle = await imageSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(imageSettingsPage.pageTitle);
    });

    it('should enable WebP image format', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableWebP', baseContext);

      const result = await imageSettingsPage.setImageFormatToGenerateChecked(page, 'webp', true);
      expect(result).to.be.eq(imageSettingsPage.messageSettingsUpdated);
    });

    it('should check image generation options', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkImageGenerationOptions', baseContext);

      // JPEG/PNG should be checked
      const jpegChecked = await imageSettingsPage.isImageFormatToGenerateChecked(page, 'jpg');
      expect(jpegChecked).to.eq(true);

      // JPEG/PNG should be checked
      const jpegDisabled = await imageSettingsPage.isImageFormatToGenerateDisabled(page, 'jpg');
      expect(jpegDisabled).to.eq(true);

      // WebP should be checked
      const webpChecked = await imageSettingsPage.isImageFormatToGenerateChecked(page, 'webp');
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
  ].forEach((arg: {store: StoreData, extOriginal: string, extGenerated: string, extImageType: string}, index: number) => {
    describe(`Image Generation - Store - Image Format : ${arg.extOriginal.toUpperCase()}`, async () => {
      if (index) {
        it('should go to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToBoStores${arg.extOriginal}`, baseContext);

          await storePage.goToBO(page);

          const pageTitle = await dashboardPage.getPageTitle(page);
          expect(pageTitle).to.contains(dashboardPage.pageTitle);
        });
      }

      it('should go to \'Shop Parameters > Contact\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToContactPage${arg.extOriginal}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.shopParametersParentLink,
          dashboardPage.contactLink,
        );
        await contactPage.closeSfToolBar(page);

        const pageTitle = await contactPage.getPageTitle(page);
        expect(pageTitle).to.contains(contactPage.pageTitle);
      });

      it('should go to \'Stores\' tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToStoresTab${arg.extOriginal}`, baseContext);

        await contactPage.goToStoresPage(page);

        const pageTitle = await storesPage.getPageTitle(page);
        expect(pageTitle).to.contains(storesPage.pageTitle);
      });

      it('should click on \'Add new store\' button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnNewStoreButton${arg.extOriginal}`, baseContext);

        await storesPage.goToNewStorePage(page);

        const pageTitle = await createStoresPage.getPageTitle(page);
        expect(pageTitle).to.contains(createStoresPage.pageTitleCreate);
      });

      it('should create a store', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createStore${arg.extOriginal}`, baseContext);

        const createMessage = await createStoresPage.createEditStore(page, arg.store);
        expect(createMessage).to.contains(storesPage.successfulCreationMessage);
      });

      it('should search for the new store and fetch the ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchCreatedCategory${arg.extOriginal}`, baseContext);

        await storesPage.resetFilter(page);
        await storesPage.filterTable(
          page,
          'input',
          'sl!name',
          arg.store.name,
        );

        const textColumn = await storesPage.getTextColumn(page, 1, 'sl!name');
        expect(textColumn).to.contains(arg.store.name);

        idStore = parseInt(await storesPage.getTextColumn(page, 1, 'id_store'), 10);
      });

      it('should check that images are generated', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductImages${arg.extOriginal}`, baseContext);

        // Check the original file
        const pathImageOrigJPG: string = `${files.getRootPath()}/img/st/${idStore}.jpg`;

        const fileExistsOrigJPG = await files.doesFileExist(pathImageOrigJPG);
        expect(fileExistsOrigJPG, `The file ${pathImageOrigJPG} doesn't exist!`).to.eq(true);

        const imageTypeOrigJPG = await files.getFileType(pathImageOrigJPG);
        expect(imageTypeOrigJPG).to.be.eq(arg.extImageType);

        // Check the imageFormat file
        const pathImageJPG: string = `${files.getRootPath()}/img/st/${idStore}-stores_default.jpg`;

        const fileExistsJPG = await files.doesFileExist(pathImageJPG);
        expect(fileExistsJPG, `The file ${pathImageJPG} doesn't exist!`).to.eq(true);

        const imageTypeJPG = await files.getFileType(pathImageJPG);
        expect(imageTypeJPG).to.be.eq(arg.extOriginal);

        // Check the WebP file
        const pathImageWEBP: string = `${files.getRootPath()}/img/st/${idStore}-stores_default.webp`;

        const fileExistsWEBP = await files.doesFileExist(pathImageWEBP);
        expect(fileExistsWEBP, `The file ${pathImageWEBP} doesn't exist!`).to.eq(true);

        const imageTypeWEBP = await files.getFileType(pathImageWEBP);
        expect(imageTypeWEBP).to.be.eq('webp');
      });

      it('should go to FO page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFo${arg.extOriginal}`, baseContext);

        page = await storesPage.viewMyShop(page);
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to Stores page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAllProducts${arg.extOriginal}`, baseContext);

        await homePage.goToFooterLink(page, 'Stores');

        const pageTitle = await storePage.getPageTitle(page);
        expect(pageTitle).to.be.eq(storePage.pageTitle);
      });

      it('should check that the main image of the store is a WebP', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkStoreImageMain${arg.extOriginal}`, baseContext);

        // Check the WebP file from the Stores page
        const quickViewImageMain = await storePage.getStoreImageMain(page, idStore);
        expect(quickViewImageMain).to.not.eq(null);

        await files.downloadFile(quickViewImageMain as string, 'image.img');

        const quickViewImageMainType = await files.getFileType('image.img');
        expect(quickViewImageMainType).to.be.eq('webp');

        await files.deleteFile('image.img');

        // Check the WebP file from the file system
        const pathImageWEBP: string = `${files.getRootPath()}/img/st/${idStore}-stores_default.webp`;

        const fileExistsWEBP = await files.doesFileExist(pathImageWEBP);
        expect(fileExistsWEBP, `The file ${pathImageWEBP} doesn't exist!`).to.eq(true);

        const imageTypeWEBP = await files.getFileType(pathImageWEBP);
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
  ].forEach((arg: {store: StoreData, extension: string}, index: number) => {
    describe(`POST-CONDITION : Remove store : ${arg.extension.toUpperCase()}`, async () => {
      if (!index) {
        it('should go to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToBoStores${arg.extension}`, baseContext);

          await storePage.goToBO(page);

          const pageTitle = await dashboardPage.getPageTitle(page);
          expect(pageTitle).to.contains(dashboardPage.pageTitle);
        });

        it('should go to \'Shop Parameters > Contact\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToContactPage${arg.extension}ForRemoval`, baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.shopParametersParentLink,
            dashboardPage.contactLink,
          );
          await contactPage.closeSfToolBar(page);

          const pageTitle = await contactPage.getPageTitle(page);
          expect(pageTitle).to.contains(contactPage.pageTitle);
        });

        it('should go to \'Stores\' tab', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToStoresTab${arg.extension}ForRemoval`, baseContext);

          await contactPage.goToStoresPage(page);

          const pageTitle = await storesPage.getPageTitle(page);
          expect(pageTitle).to.contains(storesPage.pageTitle);
        });
      }

      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterForDelete${arg.extension}`, baseContext);

        await storesPage.resetFilter(page);
        await storesPage.filterTable(page, 'input', 'sl!name', arg.store.name);

        const storeName = await storesPage.getTextColumn(page, 1, 'sl!name');
        expect(storeName).to.contains(arg.store.name);
      });

      it('should delete store', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteStore${arg.extension}`, baseContext);

        const textResult = await storesPage.deleteStore(page, 1);
        expect(textResult).to.contains(storesPage.successfulDeleteMessage);
      });
    });
  });
});
