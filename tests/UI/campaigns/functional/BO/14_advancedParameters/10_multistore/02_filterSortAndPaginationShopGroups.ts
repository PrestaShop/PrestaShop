// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import multiStorePage from '@pages/BO/advancedParameters/multistore';
import addShopGroupPage from '@pages/BO/advancedParameters/multistore/add';

// Import data
import ShopGroupData from '@data/faker/shopGroup';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_multistore_filterSortAndPaginationShopGroups';

/*
Enable multistore
Create 20 shop groups
Filter by : Id and shop group
Pagination between pages
Sort table
Delete the created shop groups
Disable multistore
 */
describe('BO - Advanced Parameters - MultiStore : Filter, sort and pagination shop group table', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfShopGroups: number = 0;

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

  // 2 : Go to multistore page
  describe('Go to \'Advanced Parameters > Multistore\' page', async () => {
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

    it('should get number of shop groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfShopGroups', baseContext);

      numberOfShopGroups = await multiStorePage.getNumberOfElementInGrid(page);
      expect(numberOfShopGroups).to.be.above(0);
    });
  });

  // 3 : Create 20 shop groups
  describe('Create 20 shop groups', async () => {
    new Array(20).fill(0, 0, 20).forEach((test: number, index: number) => {
      const shopGroupData: ShopGroupData = new ShopGroupData({name: `todelete${index}`});
      it('should go to add new shop group page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewShopGroupPage${index}`, baseContext);

        await multiStorePage.goToNewShopGroupPage(page);

        const pageTitle = await addShopGroupPage.getPageTitle(page);
        expect(pageTitle).to.contains(addShopGroupPage.pageTitleCreate);
      });

      it(`should create shop group n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createShopGroup${index}`, baseContext);

        const textResult = await addShopGroupPage.setShopGroup(page, shopGroupData);
        expect(textResult).to.contains(addShopGroupPage.successfulCreationMessage);

        const numberOfShopGroupsAfterCreation = await multiStorePage.getNumberOfElementInGrid(page);
        expect(numberOfShopGroupsAfterCreation).to.be.equal(numberOfShopGroups + 1 + index);
      });
    });
  });

  // 4 : Filter shop groups
  describe('Filter shop groups table', async () => {
    [
      {args: {filterBy: 'id_shop_group', filterValue: '10'}},
      {args: {filterBy: 'a!name', filterValue: 'todelete10'}},
    ].forEach((test: { args: { filterBy: string, filterValue: string } }, index: number) => {
      it(`should filter list by ${test.args.filterBy}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterBy_${test.args.filterBy}`, baseContext);

        await multiStorePage.filterTable(page, test.args.filterBy, test.args.filterValue);

        const numberOfElementAfterFilter = await multiStorePage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementAfterFilter; i++) {
          const textColumn = await multiStorePage.getTextColumn(page, i, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset filter and check the number of shop groups', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter_${index}`, baseContext);

        const numberOfElement = await multiStorePage.resetAndGetNumberOfLines(page);
        expect(numberOfElement).to.be.equal(numberOfShopGroups + 20);
      });
    });
  });

  // 5 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await multiStorePage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await multiStorePage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await multiStorePage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await multiStorePage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.equal('1');
    });
  });

  // 6 : Sort table
  describe('Sort shop groups table', async () => {
    [
      {
        args:
          {
            testIdentifier: 'sortByIdDesc', sortBy: 'id_shop_group', sortDirection: 'down', isFloat: true,
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
            testIdentifier: 'sortByIdAsc', sortBy: 'id_shop_group', sortDirection: 'up', isFloat: true,
          },
      },
    ].forEach((test: { args: { testIdentifier: string, sortBy: string, sortDirection: string, isFloat?: boolean } }) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await multiStorePage.getAllRowsColumnContent(page, test.args.sortBy);
        await multiStorePage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await multiStorePage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'up') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'up') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 7 : Delete shop groups created
  describe('Delete all shop groups created', async () => {
    new Array(20).fill(0, 0, 20).forEach((test: number, index: number) => {
      it(`should delete the shop group 'todelete${index}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteShopGroup${index}`, baseContext);

        await multiStorePage.filterTable(page, 'a!name', `todelete${index}`);

        const textResult = await multiStorePage.deleteShopGroup(page, 1);
        expect(textResult).to.contains(multiStorePage.successfulDeleteMessage);

        const numberOfShopGroupsAfterDelete = await multiStorePage.resetAndGetNumberOfLines(page);
        expect(numberOfShopGroupsAfterDelete).to.be.equal(numberOfShopGroups + 20 - index - 1);
      });
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
