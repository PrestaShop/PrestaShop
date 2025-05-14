import testContext from '@utils/testContext';
import {expect} from 'chai';

import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

import {
  boDashboardPage,
  boLoginPage,
  boMultistoreGroupCreatePage,
  boMultistorePage,
  type BrowserContext,
  FakerShopGroup,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 2 : Go to multistore page
  describe('Go to \'Advanced Parameters > Multistore\' page', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );
      await boMultistorePage.closeSfToolBar(page);

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should get number of shop groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfShopGroups', baseContext);

      numberOfShopGroups = await boMultistorePage.getNumberOfElementInGrid(page);
      expect(numberOfShopGroups).to.be.above(0);
    });
  });

  // 3 : Create 20 shop groups
  describe('Create 20 shop groups', async () => {
    new Array(20).fill(0, 0, 20).forEach((test: number, index: number) => {
      const shopGroupData: FakerShopGroup = new FakerShopGroup({name: `todelete${index}`});
      it('should go to add new shop group page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewShopGroupPage${index}`, baseContext);

        await boMultistorePage.goToNewShopGroupPage(page);

        const pageTitle = await boMultistoreGroupCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boMultistoreGroupCreatePage.pageTitleCreate);
      });

      it(`should create shop group n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createShopGroup${index}`, baseContext);

        const textResult = await boMultistoreGroupCreatePage.setShopGroup(page, shopGroupData);
        expect(textResult).to.contains(boMultistoreGroupCreatePage.successfulCreationMessage);

        const numberOfShopGroupsAfterCreation = await boMultistorePage.getNumberOfElementInGrid(page);
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

        await boMultistorePage.filterTable(page, test.args.filterBy, test.args.filterValue);

        const numberOfElementAfterFilter = await boMultistorePage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementAfterFilter; i++) {
          const textColumn = await boMultistorePage.getTextColumn(page, i, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset filter and check the number of shop groups', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter_${index}`, baseContext);

        const numberOfElement = await boMultistorePage.resetAndGetNumberOfLines(page);
        expect(numberOfElement).to.be.equal(numberOfShopGroups + 20);
      });
    });
  });

  // 5 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await boMultistorePage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boMultistorePage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boMultistorePage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await boMultistorePage.selectPaginationLimit(page, 50);
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

        const nonSortedTable = await boMultistorePage.getAllRowsColumnContent(page, test.args.sortBy);
        await boMultistorePage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boMultistorePage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'up') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await utilsCore.sortArray(nonSortedTable);

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

        await boMultistorePage.filterTable(page, 'a!name', `todelete${index}`);

        const textResult = await boMultistorePage.deleteShopGroup(page, 1);
        expect(textResult).to.contains(boMultistorePage.successfulDeleteMessage);

        const numberOfShopGroupsAfterDelete = await boMultistorePage.resetAndGetNumberOfLines(page);
        expect(numberOfShopGroupsAfterDelete).to.be.equal(numberOfShopGroups + 20 - index - 1);
      });
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
