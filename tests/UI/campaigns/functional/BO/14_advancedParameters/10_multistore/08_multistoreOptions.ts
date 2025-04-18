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

const baseContext: string = 'functional_BO_advancedParameters_multistore_multistoreOptions';

describe('BO - Advanced Parameters - Multistore : Multistore options', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let shopID: number = 0;
  const createShopData: FakerShop = new FakerShop({name: 'newShop', shopGroup: 'Default', categoryRoot: 'Home'});
  const secondCreateShopData: FakerShop = new FakerShop({name: 'secondShop', shopGroup: 'Default', categoryRoot: 'Home'});

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

  // Pre-condition : Create new shop
  describe('PRE-TEST : Create new store and set URL', async () => {
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

      await boMultistoreShopPage.filterTable(page, 'a!name', createShopData.name);
      shopID = parseInt(await boMultistoreShopPage.getTextColumn(page, 1, 'id_shop'), 10);
    });

    it('should go to add URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

      await boMultistoreShopPage.filterTable(page, 'a!name', createShopData.name);
      await boMultistoreShopPage.goToSetURL(page, 1);

      const pageTitle = await boMultistoreShopUrlCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopUrlCreatePage.pageTitleCreate);
    });

    it('should set URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

      const textResult = await boMultistoreShopUrlCreatePage.setVirtualUrl(page, createShopData.name);
      expect(textResult).to.contains(boMultistoreShopUrlCreatePage.successfulCreationMessage);
    });
  });

  describe('Multistore options', async () => {
    it('should click on Default shop link and select the new store on Default store select', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectCreatedStore', baseContext);

      await boMultistorePage.goToShopGroupPage(page, 1);

      const successMessage = await boMultistorePage.selectDefaultStore(page, createShopData.name);
      expect(successMessage).to.eq(boMultistorePage.successfulUpdateMessage);
    });

    it('should go to add new shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopPage2', baseContext);

      await boMultistorePage.goToNewShopPage(page);

      const pageTitle = await boMultistoreShopCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopCreatePage.pageTitleCreate);
    });

    it('should check that the source store is the created shop in the pre-condition', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSourceStore', baseContext);

      const sourceStore = await boMultistoreShopCreatePage.getSourceStore(page);
      expect(sourceStore).to.equal(createShopData.name);
    });

    it('should create shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShop2', baseContext);

      const textResult = await boMultistoreShopCreatePage.setShop(page, secondCreateShopData);
      expect(textResult).to.contains(boMultistorePage.successfulCreationMessage);
    });

    it('should go to add URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL2', baseContext);

      await boMultistoreShopPage.filterTable(page, 'a!name', secondCreateShopData.name);
      await boMultistoreShopPage.goToSetURL(page, 1);

      const pageTitle = await boMultistoreShopUrlCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopUrlCreatePage.pageTitleCreate);
    });

    it('should set URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL2', baseContext);

      const textResult = await boMultistoreShopUrlCreatePage.setVirtualUrl(page, secondCreateShopData.name);
      expect(textResult).to.contains(boMultistoreShopUrlCreatePage.successfulCreationMessage);
    });

    it('should click on Default shop link and select the default store on Default store select', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectDefaultStore', baseContext);

      await boMultistorePage.goToShopGroupPage(page, 1);

      const successMessage = await boMultistorePage.selectDefaultStore(page, global.INSTALL.SHOP_NAME);
      expect(successMessage).to.eq(boMultistorePage.successfulUpdateMessage);
    });
  });

  // Post-condition : Delete created shop
  describe('POST-TEST: Delete shop', async () => {
    it('should go to the created shop page and delete the first created shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstSHop', baseContext);

      await boMultistorePage.goToShopPage(page, shopID);
      await boMultistoreShopPage.filterTable(page, 'a!name', createShopData.name);

      const textResult = await boMultistoreShopPage.deleteShop(page, 1);
      expect(textResult).to.contains(boMultistoreShopPage.successfulDeleteMessage);
    });

    it('should delete the second created shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShop2', baseContext);

      await boMultistoreShopPage.filterTable(page, 'a!name', secondCreateShopData.name);

      const textResult = await boMultistoreShopPage.deleteShop(page, 1);
      expect(textResult).to.contains(boMultistoreShopPage.successfulDeleteMessage);
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
