import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';

import {
  boDashboardPage,
  boLoginPage,
  boMultistorePage,
  boMultistoreShopUrlPage,
  boMultistoreShopUrlCreatePage,
  type BrowserContext,
  FakerShop,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_multistore_filterSortAndPaginationShopUrls';

/*
Enable multistore
Create 20 shop urls
Filter by: Id, shop name, URL, is the main URL, Enabled
Pagination between pages
Sort table by: Id, shop name, URL
Delete the created shop urls
Disable multistore
 */
describe('BO - Advanced Parameters - Multistore : Filter, sort and pagination shop Urls', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition: Enable multistore
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
  describe('Go to \'Multistore\' page', async () => {
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

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should go to \'Shop Urls\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopUrlsPage', baseContext);

      await boMultistorePage.goToShopURLPage(page, 1);

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });
  });

  // 3 : Create 20 shop urls
  describe('Create 20 shop Urls', async () => {
    Array(20).fill(0, 0, 20).forEach((test: number, index: number) => {
      const shopUrlData: FakerShop = new FakerShop({
        name: `ToDelete${index + 1}Shop`,
        shopGroup: '',
        categoryRoot: '',
      });
      it('should go to add shop URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddURL${index}`, baseContext);

        await boMultistoreShopUrlPage.goToAddNewUrl(page);

        const pageTitle = await boMultistoreShopUrlCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boMultistoreShopUrlCreatePage.pageTitleCreate);
      });

      it(`should create shop URl n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addURL${index}`, baseContext);

        const textResult = await boMultistoreShopUrlCreatePage.setVirtualUrl(page, shopUrlData.name);
        expect(textResult).to.contains(boMultistoreShopUrlCreatePage.successfulCreationMessage);
      });
    });
  });

  // 4 : Filter shop urls
  describe('Filter shop table', async () => {
    [
      {args: {filterBy: 'id_shop_url', filterValue: '10', filterType: 'input'}},
      {args: {filterBy: 's!name', filterValue: global.INSTALL.SHOP_NAME, filterType: 'input'}},
      {args: {filterBy: 'url', filterValue: 'ToDelete10', filterType: 'input'}},
      {args: {filterBy: 'main', filterValue: 'Yes', filterType: 'select'}, expected: 'Enabled'},
      {args: {filterBy: 'active', filterValue: 'Yes', filterType: 'select'}, expected: 'Enabled'},
    ].forEach((
      test: { args: { filterBy: string, filterValue: string, filterType: string }, expected?: string },
      index: number,
    ) => {
      it(`should filter list by ${test.args.filterBy}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterBy_${test.args.filterBy}`, baseContext);

        await boMultistoreShopUrlPage.filterTable(page, test.args.filterType, test.args.filterBy, test.args.filterValue);

        const numberOfElementAfterFilter = await boMultistoreShopUrlPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementAfterFilter; i++) {
          const textColumn = await boMultistoreShopUrlPage.getTextColumn(page, i, test.args.filterBy);

          if (test.expected !== undefined) {
            expect(textColumn).to.contains(test.expected);
          } else {
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset filter and check the number of shops', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter_${index}`, baseContext);

        const numberOfElement = await boMultistoreShopUrlPage.resetAndGetNumberOfLines(page);
        expect(numberOfElement).to.be.above(20);
      });
    });
  });

  // 5 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await boMultistoreShopUrlPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boMultistoreShopUrlPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boMultistoreShopUrlPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await boMultistoreShopUrlPage.selectPaginationLimit(page, 50);
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
    ].forEach((test: { args: { testIdentifier: string, sortBy: string, sortDirection: string, isFloat?: boolean } }) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await boMultistoreShopUrlPage.getAllRowsColumnContent(page, test.args.sortBy);
        await boMultistoreShopUrlPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boMultistoreShopUrlPage.getAllRowsColumnContent(page, test.args.sortBy);

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
      it(`should delete the shop url contains 'ToDelete${index + 1}Shop'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteShopUrl${index}_`, baseContext);

        await boMultistoreShopUrlPage.filterTable(page, 'input', 'url', `ToDelete${index + 1}Shop`);

        const textResult = await boMultistoreShopUrlPage.deleteShopURL(page, 1);
        expect(textResult).to.contains(boMultistoreShopUrlPage.successfulDeleteMessage);
      });
    });
  });

  // Post-condition : Disable multi store
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
