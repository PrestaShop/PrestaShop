// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import generalPage from '@pages/BO/shopParameters/general';
import multiStorePage from '@pages/BO/advancedParameters/multistore';
import addShopGroupPage from '@pages/BO/advancedParameters/multistore/add';
import addShopPage from '@pages/BO/advancedParameters/multistore/shop/add';
import shopPage from '@pages/BO/advancedParameters/multistore/shop';

// Import data
import ShopGroupData from '@data/faker/shopGroup';
import ShopData from '@data/faker/shop';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_multistore_CRUDShopGroups';

// Create, Read, Update and Delete shop groups in BO
describe('BO - Advanced Parameters - Multistore : Create, Read, Update and Delete shop groups in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfShopGroups: number = 0;
  let shopID: number = 0;

  const createShopGroupData: ShopGroupData = new ShopGroupData();
  const updateShopGroupData: ShopGroupData = new ShopGroupData();
  const shopData: ShopData = new ShopData({shopGroup: updateShopGroupData.name, categoryRoot: 'Home'});

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  // 1 : Enable multi store
  describe('Enable \'Multistore\'', async () => {
    it('should go to \'Shop Parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGeneralPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.shopParametersGeneralLink,
      );
      await generalPage.closeSfToolBar(page);

      const pageTitle = await generalPage.getPageTitle(page);
      await expect(pageTitle).to.contains(generalPage.pageTitle);
    });

    it('should enable \'Multistore\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableMultiStore', baseContext);

      const result = await generalPage.setMultiStoreStatus(page, true);
      await expect(result).to.contains(generalPage.successfulUpdateMessage);
    });
  });

  // 2 : Create shop group
  describe('Create shop group', async () => {
    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.multistoreLink,
      );
      await multiStorePage.closeSfToolBar(page);

      const pageTitle = await multiStorePage.getPageTitle(page);
      await expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });

    it('should get number of shop group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfShopGroups', baseContext);

      numberOfShopGroups = await multiStorePage.getNumberOfElementInGrid(page);
      await expect(numberOfShopGroups).to.be.above(0);
    });

    it('should go to add new shop group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopGroupPage', baseContext);

      await multiStorePage.goToNewShopGroupPage(page);

      const pageTitle = await addShopGroupPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addShopGroupPage.pageTitleCreate);
    });

    it('should create shop group and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShopGroup', baseContext);

      const textResult = await addShopGroupPage.setShopGroup(page, createShopGroupData);
      await expect(textResult).to.contains(addShopGroupPage.successfulCreationMessage);

      const numberOfShopGroupsAfterCreation = await multiStorePage.getNumberOfElementInGrid(page);
      await expect(numberOfShopGroupsAfterCreation).to.be.equal(numberOfShopGroups + 1);
    });
  });

  // 3 : Update shop group
  describe('Update shop group', async () => {
    it('should go to edit the created shop group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditShopGroupPage', baseContext);

      await multiStorePage.filterTable(page, 'a!name', createShopGroupData.name);
      await multiStorePage.gotoEditShopGroupPage(page, 1);

      const pageTitle = await addShopGroupPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addShopGroupPage.pageTitleEdit);
    });

    it('should edit shop group and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateShopGroup', baseContext);

      const textResult = await addShopGroupPage.setShopGroup(page, updateShopGroupData);
      await expect(textResult).to.contains(addShopGroupPage.successfulUpdateMessage);

      const numberOfShopGroupsAfterUpdate = await multiStorePage.resetAndGetNumberOfLines(page);
      await expect(numberOfShopGroupsAfterUpdate).to.be.equal(numberOfShopGroups + 1);
    });
  });

  // 4 - Create shop related to the updated shop group
  describe('Create shop for the updated shop group', async () => {
    it('should go to add new shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopPage', baseContext);

      await multiStorePage.goToNewShopPage(page);

      const pageTitle = await addShopPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addShopPage.pageTitleCreate);
    });

    it('should create shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShop', baseContext);

      const textResult = await addShopPage.setShop(page, shopData);
      await expect(textResult).to.contains(multiStorePage.successfulCreationMessage);
    });

    it('should get the id of the new shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getShopID', baseContext);

      const numberOfShops = await shopPage.getNumberOfElementInGrid(page);
      await expect(numberOfShops).to.be.above(0);

      shopID = parseInt(await shopPage.getTextColumn(page, 1, 'id_shop'), 10);
    });
  });

  // 5 : Check that we cannot delete a shop group that has a shop
  describe('Check that there is no delete button in the edited shop group', async () => {
    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePageToDeleteShopGroup', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.multistoreLink,
      );
      await multiStorePage.closeSfToolBar(page);

      const pageTitle = await multiStorePage.getPageTitle(page);
      await expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });

    it('should check that there is no delete button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoDeleteButton', baseContext);

      await multiStorePage.filterTable(page, 'a!name', updateShopGroupData.name);

      const isVisible = await multiStorePage.isActionToggleButtonVisible(page, 1);
      await expect(isVisible).to.be.false;
    });
  });

  // 6 : Delete the shop and the edited shop group
  describe('Delete shop then shop group', async () => {
    it('should go to the created shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedShopPage', baseContext);

      await multiStorePage.goToShopPage(page, shopID);

      const pageTitle = await shopPage.getPageTitle(page);
      await expect(pageTitle).to.contains(updateShopGroupData.name);
    });

    it('should delete the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShop', baseContext);

      const numberOfShops = await shopPage.getNumberOfElementInGrid(page);
      await expect(numberOfShops).to.be.above(1);

      await shopPage.filterTable(page, 'a!name', shopData.name);

      const textResult = await shopPage.deleteShop(page, 1);
      await expect(textResult).to.contains(shopPage.successfulDeleteMessage);

      const numberOfShopsAfterDelete = await shopPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShopsAfterDelete).to.be.equal(1);
    });

    it('should go to \'Advanced parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePageToDeleteShopGroup2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.multistoreLink,
      );

      const pageTitle = await multiStorePage.getPageTitle(page);
      await expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });

    it('should delete the shop group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteEditedSHopGroup', baseContext);

      await multiStorePage.filterTable(page, 'a!name', updateShopGroupData.name);

      const textResult = await multiStorePage.deleteShopGroup(page, 1);
      await expect(textResult).to.contains(multiStorePage.successfulDeleteMessage);

      const numberOfShopGroupsAfterDelete = await multiStorePage.resetAndGetNumberOfLines(page);
      await expect(numberOfShopGroupsAfterDelete).to.be.equal(numberOfShopGroups);
    });
  });

  // 7 : Disable multi store
  describe('Disable \'Multistore\'', async () => {
    it('should go to \'Shop Parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGeneralPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.shopParametersGeneralLink,
      );
      await generalPage.closeSfToolBar(page);

      const pageTitle = await generalPage.getPageTitle(page);
      await expect(pageTitle).to.contains(generalPage.pageTitle);
    });

    it('should disable \'Multistore\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableMultiStore', baseContext);

      const result = await generalPage.setMultiStoreStatus(page, false);
      await expect(result).to.contains(generalPage.successfulUpdateMessage);
    });
  });
});
