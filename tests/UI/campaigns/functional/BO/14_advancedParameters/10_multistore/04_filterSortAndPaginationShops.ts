// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import generalPage from '@pages/BO/shopParameters/general';
import multiStorePage from '@pages/BO/advancedParameters/multistore';
import addShopPage from '@pages/BO/advancedParameters/multistore/shop/add';
import shopsPage from '@pages/BO/advancedParameters/multistore/shop';

// Import data
import ShopFaker from '@data/faker/shop';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_multistore_filterSortAndPaginationShops';

/*
Enable multistore
Create 20 shops
Filter by: Id, shop name, shop group, root category and URL
Pagination between pages
Sort table by: Id, shop name, shop group, root category and URL
Delete the created shop
Disable multistore
 */
describe('BO - Advanced Parameters - Multistore : Filter, sort and pagination shops', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const ShopData: ShopFaker = new ShopFaker({name: 'todelete0', shopGroup: 'Default', categoryRoot: 'Home'});

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

  // 2 : Go to multistore page
  describe('Go to \'Multistore\' page and create the first shop', async () => {
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

    it('should go to add new shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopsPage', baseContext);

      await multiStorePage.goToNewShopPage(page);

      const pageTitle = await addShopPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addShopPage.pageTitleCreate);
    });

    it('should create shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createFirstShop', baseContext);

      const textResult = await addShopPage.setShop(page, ShopData);
      await expect(textResult).to.contains(multiStorePage.successfulCreationMessage);
    });
  });

  // 3 : Create 19 shops
  describe('Create 19 shops', async () => {
    Array(19).fill(0, 0, 19).forEach((test, index) => {
      const ShopData = new ShopFaker({name: `Todelete${index + 1}`, shopGroup: 'Default', categoryRoot: 'Home'});
      it('should go to add new shop page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewShopsPage${index}`, baseContext);

        await shopsPage.goToNewShopPage(page);

        const pageTitle = await addShopPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addShopPage.pageTitleCreate);
      });

      it(`should create shop n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createShop${index}`, baseContext);

        const textResult = await addShopPage.setShop(page, ShopData);
        await expect(textResult).to.contains(multiStorePage.successfulCreationMessage);
      });
    });
  });

  // 4 : Filter shops
  describe('Filter shops table', async () => {
    [
      {args: {filterBy: 'id_shop', filterValue: '10'}},
      {args: {filterBy: 'a!name', filterValue: 'Todelete10'}},
      {args: {filterBy: 'gs!name', filterValue: 'Default'}},
      {args: {filterBy: 'cl!name', filterValue: 'Home'}},
      {args: {filterBy: 'url', filterValue: 'Click here'}},
    ].forEach((test: {args: {filterBy: string, filterValue: string}}, index: number) => {
      it(`should filter list by ${test.args.filterBy}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterBy_${test.args.filterBy}`, baseContext);

        await shopsPage.filterTable(page, test.args.filterBy, test.args.filterValue);

        const numberOfElementAfterFilter = await shopsPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementAfterFilter; i++) {
          const textColumn = await shopsPage.getTextColumn(page, i, test.args.filterBy);
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset filter and check the number of shops', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter_${index}`, baseContext);

        const numberOfElement = await shopsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfElement).to.be.above(20);
      });
    });
  });

  // 5 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await shopsPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await shopsPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await shopsPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await shopsPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.equal('1');
    });
  });

  // 6 : Sort
  describe('Sort shops table', async () => {
    [
      {
        args:
          {
            testIdentifier: 'sortByIdDesc', sortBy: 'id_shop', sortDirection: 'down', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByShopNameAsc', sortBy: 'a!name', sortDirection: 'up',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByShopNameDesc', sortBy: 'a!name', sortDirection: 'down',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByShopGroupAsc', sortBy: 'gs!name', sortDirection: 'up',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByShopGroupDesc', sortBy: 'gs!name', sortDirection: 'down',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByMessageAsc', sortBy: 'cl!name', sortDirection: 'up',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByRootCategoryDesc', sortBy: 'cl!name', sortDirection: 'down',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByURLAsc', sortBy: 'url', sortDirection: 'up',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByURLDesc', sortBy: 'url', sortDirection: 'down',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByIdAsc', sortBy: 'id_shop', sortDirection: 'up', isFloat: true,
          },
      },
    ].forEach((test: {args: {testIdentifier: string, sortBy: string, sortDirection: string, isFloat?: boolean}}) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await shopsPage.getAllRowsColumnContent(page, test.args.sortBy);
        await shopsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await shopsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'up') {
            await expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'up') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 7 : Delete all shops created
  describe('delete all shops created', async () => {
    new Array(20).fill(0, 0, 20).forEach((test: number, index: number) => {
      it(`should delete the shop 'Todelete${index}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteShop${index}`, baseContext);

        await shopsPage.filterTable(page, 'a!name', `Todelete${index}`);

        const textResult = await shopsPage.deleteShop(page, 1);
        await expect(textResult).to.contains(shopsPage.successfulDeleteMessage);
      });
    });
  });

  // 8 : Disable multi store
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
