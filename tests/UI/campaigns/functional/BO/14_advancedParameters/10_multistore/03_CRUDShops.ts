// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import multiStorePage from '@pages/BO/advancedParameters/multistore';
import addShopPage from '@pages/BO/advancedParameters/multistore/shop/add';
import addShopUrlPage from '@pages/BO/advancedParameters/multistore/url/addURL';
import shopPage from '@pages/BO/advancedParameters/multistore/shop';

// Import data
import ShopData from '@data/faker/shop';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_multistore_CRUDShops';

// Create, Read, Update and Delete shop in BO
describe('BO - Advanced Parameters - Multistore : Create, Read, Update and Delete shop in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let shopID: number = 0;
  const createShopData: ShopData = new ShopData({shopGroup: 'Default', categoryRoot: 'Home'});
  const updateShopData: ShopData = new ShopData({shopGroup: 'Default', categoryRoot: 'Home'});

  //Pre-condition: Enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 2 : Create shop
  describe('Create shop', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.multistoreLink,
      );
      await multiStorePage.closeSfToolBar(page);

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

      shopID = parseInt(await shopPage.getTextColumn(page, 1, 'id_shop'), 10);
    });
  });

  // 3 : Update shop
  describe('Update shop', async () => {
    it('should go to edit the created shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditShopPage', baseContext);

      await shopPage.filterTable(page, 'a!name', createShopData.name);
      await shopPage.gotoEditShopPage(page, 1);

      const pageTitle = await addShopPage.getPageTitle(page);
      expect(pageTitle).to.contains(addShopPage.pageTitleEdit);
    });

    it('should edit shop and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateShop', baseContext);

      const textResult = await addShopPage.setShop(page, updateShopData);
      expect(textResult).to.contains(addShopPage.successfulUpdateMessage);
    });

    it('should go to add URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

      await shopPage.filterTable(page, 'a!name', updateShopData.name);
      await shopPage.goToSetURL(page, 1);

      const pageTitle = await addShopUrlPage.getPageTitle(page);
      expect(pageTitle).to.contains(addShopUrlPage.pageTitleCreate);
    });

    it('should set URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

      const textResult = await addShopUrlPage.setVirtualUrl(page, updateShopData.name);
      expect(textResult).to.contains(addShopUrlPage.successfulCreationMessage);
    });
  });

  // 4 : Delete the shop
  describe('Delete shop', async () => {
    it('should go to the created shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedShopPage', baseContext);

      await multiStorePage.goToShopPage(page, shopID);

      const pageTitle = await shopPage.getPageTitle(page);
      expect(pageTitle).to.contains(updateShopData.name);
    });

    it('should delete the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShop', baseContext);

      await shopPage.filterTable(page, 'a!name', updateShopData.name);

      const textResult = await shopPage.deleteShop(page, 1);
      expect(textResult).to.contains(shopPage.successfulDeleteMessage);
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
