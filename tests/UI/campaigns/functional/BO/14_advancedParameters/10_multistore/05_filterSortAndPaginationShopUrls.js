require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const generalPage = require('@pages/BO/shopParameters/general');
const multiStorePage = require('@pages/BO/advancedParameters/multistore');
const addShopUrlPage = require('@pages/BO/advancedParameters/multistore/url/addURL');
const shopUrlPage = require('@pages/BO/advancedParameters/multistore/url');

// Import data
const ShopFaker = require('@data/faker/shop');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_multistore_filterSortAndPaginationShopUrls';

let browserContext;
let page;

/*
Enable multistore
Create 20 shop urls
Filter by: Id, shop name, shop group, root category and URL
Pagination between pages
Sort table by: Id, shop name, shop group, root category and URL
Delete the created shop
Disable multistore
 */
describe('Filter, sort and pagination shop Urls', async () => {
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

  // 2 : Go to multistore page
  describe('Go to multistore page', async () => {
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

    it('should go to shop Urls page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopUrlsPage', baseContext);

      await multiStorePage.goToShopURLPage(page, 1);

      const pageTitle = await multiStorePage.getPageTitle(page);
      await expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });
  });

  // 3 : Create 20 shop urls
  Array(20).fill(0, 0, 20).forEach((test, index) => {
    describe(`Create shop Url n°${index + 1}`, async () => {
      const ShopUrlData = new ShopFaker({name: `ToDelete${index + 1}`});
      it('should go to add shop URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

        await shopUrlPage.goToAddNewUrl(page);

        const pageTitle = await addShopUrlPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addShopUrlPage.pageTitleCreate);
      });

      it('should set shop URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

        const textResult = await addShopUrlPage.setVirtualUrl(page, ShopUrlData);
        await expect(textResult).to.contains(addShopUrlPage.successfulCreationMessage);
      });
    });
  });

  // 8 : Disable multi store
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
