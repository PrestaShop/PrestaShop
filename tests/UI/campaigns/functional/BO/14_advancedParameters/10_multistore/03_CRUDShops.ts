import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

import {
  boDashboardPage,
  boLoginPage,
  boMultistorePage,
  boMultistoreShopPage,
  boMultistoreShopCreatePage,
  boMultistoreShopUrlCreatePage,
  type BrowserContext,
  FakerShop,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_multistore_CRUDShops';

// Create, Read, Update and Delete shop in BO
describe('BO - Advanced Parameters - Multistore : Create, Read, Update and Delete shop in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let shopID: number = 0;
  const createShopData: FakerShop = new FakerShop({shopGroup: 'Default', categoryRoot: 'Home'});
  const updateShopData: FakerShop = new FakerShop({shopGroup: 'Default', categoryRoot: 'Home'});

  //Pre-condition: Enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 2 : Create shop
  describe('Create shop', async () => {
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

    it('should go to add new shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopPage', baseContext);

      await boMultistorePage.goToNewShopPage(page);

      const pageTitle = await boMultistoreShopCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopCreatePage.pageTitleCreate);
    });

    it('should create shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShop', baseContext);

      const textResult = await boMultistoreShopCreatePage.setShop(page, createShopData);
      expect(textResult).to.contains(boMultistorePage.successfulCreationMessage);
    });

    it('should get the id of the new shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getShopID', baseContext);

      const numberOfShops = await boMultistoreShopPage.getNumberOfElementInGrid(page);
      expect(numberOfShops).to.be.above(0);

      shopID = parseInt(await boMultistoreShopPage.getTextColumn(page, 1, 'id_shop'), 10);
    });
  });

  // 3 : Update shop
  describe('Update shop', async () => {
    it('should go to edit the created shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditShopPage', baseContext);

      await boMultistoreShopPage.filterTable(page, 'a!name', createShopData.name);
      await boMultistoreShopPage.gotoEditShopPage(page, 1);

      const pageTitle = await boMultistoreShopCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopCreatePage.pageTitleEdit);
    });

    it('should edit shop and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateShop', baseContext);

      const textResult = await boMultistoreShopCreatePage.setShop(page, updateShopData);
      expect(textResult).to.contains(boMultistoreShopCreatePage.successfulUpdateMessage);
    });

    it('should go to add URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

      await boMultistoreShopPage.filterTable(page, 'a!name', updateShopData.name);
      await boMultistoreShopPage.goToSetURL(page, 1);

      const pageTitle = await boMultistoreShopUrlCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopUrlCreatePage.pageTitleCreate);
    });

    it('should set URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

      const textResult = await boMultistoreShopUrlCreatePage.setVirtualUrl(page, updateShopData.name);
      expect(textResult).to.contains(boMultistoreShopUrlCreatePage.successfulCreationMessage);
    });
  });

  // 4 : Delete the shop
  describe('Delete shop', async () => {
    it('should go to the created shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedShopPage', baseContext);

      await boMultistorePage.goToShopPage(page, shopID);

      const pageTitle = await boMultistoreShopPage.getPageTitle(page);
      expect(pageTitle).to.contains(updateShopData.name);
    });

    it('should delete the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShop', baseContext);

      await boMultistoreShopPage.filterTable(page, 'a!name', updateShopData.name);

      const textResult = await boMultistoreShopPage.deleteShop(page, 1);
      expect(textResult).to.contains(boMultistoreShopPage.successfulDeleteMessage);
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
