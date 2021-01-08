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

// Import data
const ShopGroupFaker = require('@data/faker/shopGroup');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_modules_advancedParameters_multistore_filterSortAndPaginationShopGroup';

let browserContext;
let page;

let numberOfShopGroups = 0;

/*

 */
describe('Filter, sort and pagination shop group', async () => {
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
    it('should go to \'Shop parameters > General\' page', async function () {
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

    it('should enable \'Multi store\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableMultiStore', baseContext);

      const result = await generalPage.setMultiStoreStatus(page, true);
      await expect(result).to.contains(generalPage.successfulUpdateMessage);
    });
  });

  describe('Go to multistore page and get number of store groups', async () => {
    it('should go to \'Advanced parameters > Multi store\' page', async function () {
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

    it('should get number of shop groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfShopGroups', baseContext);

      numberOfShopGroups = await multiStorePage.getNumberOfElementInGrid(page);
      await expect(numberOfShopGroups).to.be.above(0);
    });
  });

  // 2 : Create shop group
  new Array(11).fill(0, 0, 11).forEach((test, index) => {
    describe(`Create shop group n°${index + 1}`, async () => {
      const shopGroupData = new ShopGroupFaker({name: `todelete${index}`});
      it('should go to add new shop group page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewShopGroupPage${index}`, baseContext);

        await multiStorePage.goToNewShopGroupPage(page);

        const pageTitle = await addShopGroupPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addShopGroupPage.pageTitleCreate);
      });

      it('should create shop group and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createShopGroup${index}`, baseContext);

        const textResult = await addShopGroupPage.setShopGroup(page, shopGroupData);
        await expect(textResult).to.contains(addShopGroupPage.successfulCreationMessage);

        const numberOfShopGroupsAfterCreation = await multiStorePage.getNumberOfElementInGrid(page);
        await expect(numberOfShopGroupsAfterCreation).to.be.equal(numberOfShopGroups + 1 + index);
      });
    });
  });

  // 6 : Delete shop groups created
  new Array(11).fill(0, 0, 11).forEach((test, index) => {
    describe('delete shop group', async () => {
      it(`should delete the shop group 'todelete${index}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteEditedSHopGroup', baseContext);

        await multiStorePage.filterTable(page, 'a!name', `todelete${index}`);

        const textResult = await multiStorePage.deleteShopGroup(page, 1);
        await expect(textResult).to.contains(multiStorePage.successfulDeleteMessage);

        const numberOfShopGroupsAfterDelete = await multiStorePage.resetAndGetNumberOfLines(page);
        await expect(numberOfShopGroupsAfterDelete).to.be.equal(numberOfShopGroups + 11 - index - 1);
      });
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
