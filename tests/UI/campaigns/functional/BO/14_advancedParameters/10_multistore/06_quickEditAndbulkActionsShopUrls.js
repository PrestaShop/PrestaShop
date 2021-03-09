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

const baseContext = 'functional_BO_advancedParameters_multistore_quickEditAndBulkActionsShopUrls';

let browserContext;
let page;
let numberOfShopUrls = 0;

/*
Enable multistore
Create 2 shop urls
Quick edit (enable, disable)
Bulk actions (enable, disable)
Deleted created shop urls
Disable multistore
 */
describe('Quick edit and bulk actions shop Urls', async () => {
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

      const pageTitle = await multiStorePage.getPageTitle(page);
      await expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });

    it('should go to shop Urls page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopUrlsPage', baseContext);

      await multiStorePage.goToShopURLPage(page, 1);

      const pageTitle = await multiStorePage.getPageTitle(page);
      await expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });

    it('should reset filter and get the number of shop urls', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numberOfShopUrls = await shopUrlPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShopUrls).to.be.above(0);
    });
  });

  // 3 : Create 2 shop urls
  Array(2).fill(0, 0, 2).forEach((test, index) => {
    describe(`Create shop Url n°${index + 1}`, async () => {
      const ShopUrlData = new ShopFaker({name: `ToDelete${index + 1}`});
      it('should go to add shop URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddURL${index}`, baseContext);

        await shopUrlPage.goToAddNewUrl(page);

        const pageTitle = await addShopUrlPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addShopUrlPage.pageTitleCreate);
      });

      it('should set shop URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addURL${index}`, baseContext);

        const textResult = await addShopUrlPage.setVirtualUrl(page, ShopUrlData);
        await expect(textResult).to.contains(addShopUrlPage.successfulCreationMessage);
      });
    });
  });

  // 4 : Quick edit first created shop url
  describe('Quick edit first shop url', async () => {
    it('should filter list by URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForEnableDisable', baseContext);

      await shopUrlPage.filterTable(page, 'input', 'url', 'ToDelete1');

      const numberOfShopUrlsAfterFilter = await shopUrlPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfShopUrlsAfterFilter; i++) {
        const textColumn = await shopUrlPage.getTextColumn(page, i, 'url');
        await expect(textColumn).to.contains('ToDelete1');
      }
    });

    const tests = [
      {args: {column: 5, columnName: 'Is it the mail URL', action: 'enable', enabledValue: true}},
      {args: {column: 5, columnName: 'Is it the mail URL', action: 'disable', enabledValue: false}},
      {args: {column: 6, columnName: 'Enabled', action: 'enable', enabledValue: true}},
      {args: {column: 6, columnName: 'Enabled', action: 'disable', enabledValue: false}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} the column '${test.args.columnName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ShopUrl`, baseContext);

        const isActionPerformed = await shopUrlPage.setStatus(page, 1, test.args.column, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await shopUrlPage.getAlertSuccessBlockContent(page);
          await expect(resultMessage).to.contains(shopUrlPage.successfulUpdateMessage);
        }

        const carrierStatus = await shopUrlPage.getStatus(page, 1, test.args.column);
        await expect(carrierStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterEnableDisable', baseContext);

      const numberOfShopUrlsAfterReset = await shopUrlPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShopUrlsAfterReset).to.be.equal(numberOfShopUrls + 2);
    });
  });

  // 7 : Delete all shops created
  /*describe('delete all shops created', async () => {
    new Array(2).fill(0, 0, 2).forEach((test, index) => {
      it(`should delete the shop url contains 'ToDelete${index + 1}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteShopUrl${index}_`, baseContext);

        await shopUrlPage.filterTable(page, 'input', 'url', `ToDelete${index + 1}`);

        const textResult = await shopUrlPage.deleteShopURL(page, 1);
        await expect(textResult).to.contains(shopUrlPage.successfulDeleteMessage);
      });
    });
  });*/

  /*// 8 : Disable multi store
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
