require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const generalPage = require('@pages/BO/shopParameters/general');
const multiStorePage = require('@pages/BO/advancedParameters/multistore');
const addShopPage = require('@pages/BO/advancedParameters/multistore/shop/add');
const shopPage = require('@pages/BO/advancedParameters/multistore/shop/index');

// Import data
const ShopFaker = require('@data/faker/shop');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_modules_advancedParameters_multistore_CRUDShops';

let browserContext;
let page;

const createShopData = new ShopFaker({shopGroup: 'Default', categoryRoot: 'Home'});
const updateShopData = new ShopFaker({shopGroup: 'Default', categoryRoot: 'Home'});

// Create, Read, Update and Delete shop in BO
describe('Create, Read, Update and Delete shop in BO', async () => {
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

  // 2 : Create shop
  describe('Create shop', async () => {
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

    it('should go to add new shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopPage', baseContext);

      await multiStorePage.goToNewShopPage(page);

      const pageTitle = await addShopPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addShopPage.pageTitleCreate);
    });

    it('should create shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShop', baseContext);

      const textResult = await addShopPage.setShop(page, createShopData);
      await expect(textResult).to.contains(multiStorePage.successfulCreationMessage);
    });
  });

  // 3 : Update shop
  /*describe('Update shop', async () => {
    it('should go to edit the created shop group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditShopGroupPage', baseContext);

      await multiStorePage.filterTable(page, 'a!name', createSHopData.name);

      await multiStorePage.gotoEditShopGroupPage(page, 1);

      const pageTitle = await addShopGroupPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addShopGroupPage.pageTitleEdit);
    });

    it('should edit shop group and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateShopGroup', baseContext);

      const textResult = await addShopGroupPage.setShopGroup(page, updateShopData);
      await expect(textResult).to.contains(addShopGroupPage.successfulUpdateMessage);

      const numberOfShopsAfterUpdate = await multiStorePage.resetAndGetNumberOfLines(page);
      await expect(numberOfShopsAfterUpdate).to.be.equal(numberOfShops + 1);
    });
  });*/

 /* // 6 : Delete the shop
  describe('delete shop', async () => {
    it('should go to the created shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedShopPage', baseContext);

      await multiStorePage.goToShopPage(page, shopID);

      const pageTitle = await shopPage.getPageTitle(page);
      await expect(pageTitle).to.contains(updateShopData.name);
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

      await multiStorePage.filterTable(page, 'a!name', updateShopData.name);

      const textResult = await multiStorePage.deleteShopGroup(page, 1);
      await expect(textResult).to.contains(multiStorePage.successfulDeleteMessage);

      const numberOfShopsAfterDelete = await multiStorePage.resetAndGetNumberOfLines(page);
      await expect(numberOfShopsAfterDelete).to.be.equal(numberOfShops);
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
  });*/
});
