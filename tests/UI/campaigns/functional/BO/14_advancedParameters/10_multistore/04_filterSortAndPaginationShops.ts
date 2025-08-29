import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

import {
  boDashboardPage,
  boLoginPage,
  boMultistorePage,
  boMultistoreShopPage,
  boMultistoreShopCreatePage,
  type BrowserContext,
  FakerShop,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  const shopCreate: FakerShop = new FakerShop({name: 'todelete0', shopGroup: 'Default', categoryRoot: 'Home'});

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
  describe('Go to \'Multistore\' page and create the first shop', async () => {
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

    it('should go to add new shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopsPage', baseContext);

      await boMultistorePage.goToNewShopPage(page);

      const pageTitle = await boMultistoreShopCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopCreatePage.pageTitleCreate);
    });

    it('should create shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createFirstShop', baseContext);

      const textResult = await boMultistoreShopCreatePage.setShop(page, shopCreate);
      expect(textResult).to.contains(boMultistorePage.successfulCreationMessage);
    });
  });

  // 3 : Create 19 shops
  describe('Create 19 shops', async () => {
    Array(19).fill(0, 0, 19).forEach((test: number, index: number) => {
      const shopCreate: FakerShop = new FakerShop({
        name: `Todelete${index + 1}`,
        shopGroup: 'Default',
        categoryRoot: 'Home',
      });
      it('should go to add new shop page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewShopsPage${index}`, baseContext);

        await boMultistoreShopPage.goToNewShopPage(page);

        const pageTitle = await boMultistoreShopCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boMultistoreShopCreatePage.pageTitleCreate);
      });

      it(`should create shop n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createShop${index}`, baseContext);

        const textResult = await boMultistoreShopCreatePage.setShop(page, shopCreate);
        expect(textResult).to.contains(boMultistorePage.successfulCreationMessage);
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
    ].forEach((test: { args: { filterBy: string, filterValue: string } }, index: number) => {
      it(`should filter list by ${test.args.filterBy}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterBy_${test.args.filterBy}`, baseContext);

        await boMultistoreShopPage.filterTable(page, test.args.filterBy, test.args.filterValue);

        const numberOfElementAfterFilter = await boMultistoreShopPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementAfterFilter; i++) {
          const textColumn = await boMultistoreShopPage.getTextColumn(page, i, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset filter and check the number of shops', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter_${index}`, baseContext);

        const numberOfElement = await boMultistoreShopPage.resetAndGetNumberOfLines(page);
        expect(numberOfElement).to.be.above(20);
      });
    });
  });

  // 5 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await boMultistoreShopPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boMultistoreShopPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boMultistoreShopPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await boMultistoreShopPage.selectPaginationLimit(page, 50);
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
    ].forEach((test: { args: { testIdentifier: string, sortBy: string, sortDirection: string, isFloat?: boolean } }) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await boMultistoreShopPage.getAllRowsColumnContent(page, test.args.sortBy);
        await boMultistoreShopPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boMultistoreShopPage.getAllRowsColumnContent(page, test.args.sortBy);

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

  // 7 : Delete all shops created
  describe('delete all shops created', async () => {
    new Array(20).fill(0, 0, 20).forEach((test: number, index: number) => {
      it(`should delete the shop 'Todelete${index}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteShop${index}`, baseContext);

        await boMultistoreShopPage.filterTable(page, 'a!name', `Todelete${index}`);

        const textResult = await boMultistoreShopPage.deleteShop(page, 1);
        expect(textResult).to.contains(boMultistoreShopPage.successfulDeleteMessage);
      });
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
