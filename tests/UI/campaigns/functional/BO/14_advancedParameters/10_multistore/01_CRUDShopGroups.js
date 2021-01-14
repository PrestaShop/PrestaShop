require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const generalPage = require('@pages/BO/shopParameters/general');
const multiStorePage = require('@pages/BO/advancedParameters/multistore');
const addShopGroupPage = require('@pages/BO/advancedParameters/multistore/add');
const addShopPage = require('@pages/BO/advancedParameters/multistore/shop/add');
const shopPage = require('@pages/BO/advancedParameters/multistore/shop/index');

// Import data
const ShopGroupFaker = require('@data/faker/shopGroup');
const ShopFaker = require('@data/faker/shop');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_modules_advancedParameters_multistore_CRUDShopGroups';

let browserContext;
let page;

let numberOfShopGroups = 0;
let shopID = 0;

const createShopGroupData = new ShopGroupFaker({});
const updateShopGroupData = new ShopGroupFaker({});
const shopData = new ShopFaker({shopGroup: updateShopGroupData.name, categoryRoot: 'Home'});

// Create, Read, Update and Delete shop groups in BO
describe('Create, Read, Update and Delete shop groups in BO', async () => {
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
  describe('Enable multistore', async () => {
    it('should go to "Shop parameters > General" page', async function () {
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

    it('should enable "Multi store"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableMultiStore', baseContext);

      const result = await generalPage.setMultiStoreStatus(page, true);
      await expect(result).to.contains(generalPage.successfulUpdateMessage);
    });
  });

  // 2 : Create shop group
  describe('Create shop group', async () => {
    it('should go to "Advanced parameters > Multi store" page', async function () {
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

      shopID = await shopPage.getTextColumn(page, 1, 'id_shop');
    });
  });

  // 5 : Check that we cannot delete a shop group that has a shop
  describe('Check that there is no delete button in the edited shop group', async () => {
    it('should go to "Advanced parameters > Multi store" page', async function () {
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
  describe('delete shop then shop group', async () => {
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

    it('should go to "Advanced parameters > Multi store" page', async function () {
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
  describe('Disable multistore', async () => {
    it('should go to "Shop parameters > General" page', async function () {
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

    it('should disable "Multi store"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableMultiStore', baseContext);

      const result = await generalPage.setMultiStoreStatus(page, false);
      await expect(result).to.contains(generalPage.successfulUpdateMessage);
    });
  });
});
