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
const addShopUrlPage = require('@pages/BO/advancedParameters/multistore/url/addURL');
const shopPage = require('@pages/BO/advancedParameters/multistore/shop/index');
const shopURLPage = require('@pages/BO/advancedParameters/multistore/url/index');

// Import data
const ShopFaker = require('@data/faker/shop');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_modules_advancedParameters_multistore_CRUDShops';

let browserContext;
let page;

const createShopData = new ShopFaker({shopGroup: 'Default', categoryRoot: 'Home'});
const updateShopData = new ShopFaker({shopGroup: 'Default', categoryRoot: 'Home'});
let shopID = 0;

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

    it('should get the id of the new shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getShopID', baseContext);

      const numberOfShops = await shopPage.getNumberOfElementInGrid(page);
      await expect(numberOfShops).to.be.above(0);

      shopID = await shopPage.getTextColumn(page, 1, 'id_shop');
    });
  });

  // 3 : Update shop
  describe('Update shop', async () => {
    it('should go to edit the created shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditShopPage', baseContext);

      await shopPage.filterTable(page, 'a!name', createShopData.name);

      await shopPage.gotoEditShopPage(page, 1);

      const pageTitle = await addShopPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addShopPage.pageTitleEdit);
    });

    it('should edit shop and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateShop', baseContext);

      const textResult = await addShopPage.setShop(page, updateShopData);
      await expect(textResult).to.contains(addShopPage.successfulUpdateMessage);
    });

    it('should go to add URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

      await shopPage.filterTable(page, 'a!name', updateShopData.name);

      await shopPage.goToSetURL(page, 1);

      const pageTitle = await addShopUrlPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addShopUrlPage.pageTitleCreate);
    });

    it('should set URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

      const textResult = await addShopUrlPage.setVirtualUrl(page, updateShopData);
      await expect(textResult).to.contains(addShopUrlPage.successfulCreationMessage);
    });
  });

  // 4 : Delete shop URL
  describe('delete shop URL', async () => {
    it('should delete the shop URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShopURL', baseContext);

      const textResult = await shopURLPage.deleteShopURL(page, 1);
      await expect(textResult).to.contains(shopPage.successfulDeleteMessage);
    });
  });

  // 4 : Delete the shop
  describe('delete shop', async () => {
    it('should go to the created shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedShopPage', baseContext);

      await multiStorePage.goToShopPage(page, shopID);

      const pageTitle = await shopPage.getPageTitle(page);
      await expect(pageTitle).to.contains(updateShopData.name);
    });

    it('should delete the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShop', baseContext);

      await shopPage.filterTable(page, 'a!name', updateShopData.name);

      const textResult = await shopPage.deleteShop(page, 1);
      await expect(textResult).to.contains(shopPage.successfulDeleteMessage);
    });
  });

  // 5 : Disable multi store
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
