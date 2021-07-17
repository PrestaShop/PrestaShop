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
Filter by: Id, shop name, URL, is the main URL, Enabled
Pagination between pages
Sort table by: Id, shop name, URL
Delete the created shop urls
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
      const ShopUrlData = new ShopFaker({name: `ToDelete${index + 1}Shop`});
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

  // 4 : Filter shop urls
  describe('Filter shop table', async () => {
    [
      {args: {filterBy: 'id_shop_url', filterValue: 10, filterType: 'input'}},
      {args: {filterBy: 's!name', filterValue: 'PrestaShop', filterType: 'input'}},
      {args: {filterBy: 'url', filterValue: 'ToDelete10', filterType: 'input'}},
      {args: {filterBy: 'main', filterValue: 'Yes', filterType: 'select'}, expected: 'Enabled'},
      {args: {filterBy: 'active', filterValue: 'Yes', filterType: 'select'}, expected: 'Enabled'},
    ].forEach((test, index) => {
      it(`should filter list by ${test.args.filterBy}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterBy_${test.args.filterBy}`, baseContext);

        await shopUrlPage.filterTable(page, test.args.filterType, test.args.filterBy, test.args.filterValue);

        const numberOfElementAfterFilter = await shopUrlPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementAfterFilter; i++) {
          const textColumn = await shopUrlPage.getTextColumn(page, i, test.args.filterBy);
          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset filter and check the number of shops', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter_${index}`, baseContext);

        const numberOfElement = await shopUrlPage.resetAndGetNumberOfLines(page);
        await expect(numberOfElement).to.be.above(20);
      });
    });
  });

  // 5 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await shopUrlPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await shopUrlPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await shopUrlPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await shopUrlPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.equal('1');
    });
  });

  // 6 : Sort
  describe('Sort shop Urls table', async () => {
    [
      {
        args:
          {
            testIdentifier: 'sortByIdDesc', sortBy: 'id_shop_url', sortDirection: 'down', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByShopNameAsc', sortBy: 's!name', sortDirection: 'up',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByShopNameDesc', sortBy: 's!name', sortDirection: 'down',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByUrlAsc', sortBy: 'url', sortDirection: 'up',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByUrlDesc', sortBy: 'url', sortDirection: 'down',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByIdAsc', sortBy: 'id_shop_url', sortDirection: 'up', isFloat: true,
          },
      },
    ].forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await shopUrlPage.getAllRowsColumnContent(page, test.args.sortBy);
        await shopUrlPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await shopUrlPage.getAllRowsColumnContent(page, test.args.sortBy);
        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await shopUrlPage.sortArray(nonSortedTable, test.args.isFloat);
        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 7 : Delete all shops created
  describe('delete all shops created', async () => {
    new Array(20).fill(0, 0, 20).forEach((test, index) => {
      it(`should delete the shop url contains 'ToDelete${index + 1}Shop'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteShopUrl${index}_`, baseContext);

        await shopUrlPage.filterTable(page, 'input', 'url', `ToDelete${index + 1}Shop`);

        const textResult = await shopUrlPage.deleteShopURL(page, 1);
        await expect(textResult).to.contains(shopUrlPage.successfulDeleteMessage);
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
