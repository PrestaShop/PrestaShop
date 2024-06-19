// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

// Import pages
import multiStorePage from '@pages/BO/advancedParameters/multistore';
import shopPage from '@pages/BO/advancedParameters/multistore/shop';
import addShopPage from '@pages/BO/advancedParameters/multistore/shop/add';
import addShopUrlPage from '@pages/BO/advancedParameters/multistore/url/addURL';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  FakerShop,
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
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );

      const pageTitle = await multiStorePage.getPageTitle(page);
      expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });

    it('should go to add new shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopPage', baseContext);

      await multiStorePage.goToNewShopPage(page);

      const pageTitle = await addShopPage.getPageTitle(page);
      expect(pageTitle).to.contains(addShopPage.pageTitleCreate);
    });

    it('should create shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShop', baseContext);

      const textResult = await addShopPage.setShop(page, createShopData);
      expect(textResult).to.contains(multiStorePage.successfulCreationMessage);
    });

    it('should get the id of the new shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getShopID', baseContext);

      const numberOfShops = await shopPage.getNumberOfElementInGrid(page);
      expect(numberOfShops).to.be.above(0);

      await shopPage.filterTable(page, 'a!name', createShopData.name);
      shopID = parseInt(await shopPage.getTextColumn(page, 1, 'id_shop'), 10);
    });

    it('should go to add URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

      await shopPage.filterTable(page, 'a!name', createShopData.name);
      await shopPage.goToSetURL(page, 1);

      const pageTitle = await addShopUrlPage.getPageTitle(page);
      expect(pageTitle).to.contains(addShopUrlPage.pageTitleCreate);
    });

    it('should set URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

      const textResult = await addShopUrlPage.setVirtualUrl(page, createShopData.name);
      expect(textResult).to.contains(addShopUrlPage.successfulCreationMessage);
    });
  });

  describe('Multistore options', async () => {
    it('should click on Default shop link and select the new store on Default store select', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectCreatedStore', baseContext);

      await multiStorePage.goToShopGroupPage(page, 1);

      const successMessage = await multiStorePage.selectDefaultStore(page, createShopData.name);
      expect(successMessage).to.eq(multiStorePage.successfulUpdateMessage);
    });

    it('should go to add new shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopPage2', baseContext);

      await multiStorePage.goToNewShopPage(page);

      const pageTitle = await addShopPage.getPageTitle(page);
      expect(pageTitle).to.contains(addShopPage.pageTitleCreate);
    });

    it('should check that the source store is the created shop in the pre-condition', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSourceStore', baseContext);

      const sourceStore = await addShopPage.getSourceStore(page);
      expect(sourceStore).to.equal(createShopData.name);
    });

    it('should create shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShop2', baseContext);

      const textResult = await addShopPage.setShop(page, secondCreateShopData);
      expect(textResult).to.contains(multiStorePage.successfulCreationMessage);
    });

    it('should go to add URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL2', baseContext);

      await shopPage.filterTable(page, 'a!name', secondCreateShopData.name);
      await shopPage.goToSetURL(page, 1);

      const pageTitle = await addShopUrlPage.getPageTitle(page);
      expect(pageTitle).to.contains(addShopUrlPage.pageTitleCreate);
    });

    it('should set URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL2', baseContext);

      const textResult = await addShopUrlPage.setVirtualUrl(page, secondCreateShopData.name);
      expect(textResult).to.contains(addShopUrlPage.successfulCreationMessage);
    });

    it('should click on Default shop link and select the default store on Default store select', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectDefaultStore', baseContext);

      await multiStorePage.goToShopGroupPage(page, 1);

      const successMessage = await multiStorePage.selectDefaultStore(page, global.INSTALL.SHOP_NAME);
      expect(successMessage).to.eq(multiStorePage.successfulUpdateMessage);
    });
  });

  // Post-condition : Delete created shop
  describe('POST-TEST: Delete shop', async () => {
    it('should go to the created shop page and delete the first created shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstSHop', baseContext);

      await multiStorePage.goToShopPage(page, shopID);
      await shopPage.filterTable(page, 'a!name', createShopData.name);

      const textResult = await shopPage.deleteShop(page, 1);
      expect(textResult).to.contains(shopPage.successfulDeleteMessage);
    });

    it('should delete the second created shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShop2', baseContext);

      await shopPage.filterTable(page, 'a!name', secondCreateShopData.name);

      const textResult = await shopPage.deleteShop(page, 1);
      expect(textResult).to.contains(shopPage.successfulDeleteMessage);
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
