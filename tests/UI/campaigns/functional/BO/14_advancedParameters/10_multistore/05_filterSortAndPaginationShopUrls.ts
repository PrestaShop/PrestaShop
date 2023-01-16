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
import addShopUrlPage from '@pages/BO/advancedParameters/multistore/url/addURL';
import shopUrlPage from '@pages/BO/advancedParameters/multistore/url';

// Import data
import ShopFaker from '@data/faker/shop';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
  describe('Go to \'Multistore\' page', async () => {
    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.multistoreLink,
      );

      const pageTitle = await multiStorePage.getPageTitle(page);
      await expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });

    it('should go to \'Shop Urls\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopUrlsPage', baseContext);

      await multiStorePage.goToShopURLPage(page, 1);

      const pageTitle = await multiStorePage.getPageTitle(page);
      await expect(pageTitle).to.contains(multiStorePage.pageTitle);
    });
  });

  // 3 : Create 20 shop urls
  describe('Create 20 shop Urls', async () => {
    Array(20).fill(0, 0, 20).forEach((test, index) => {
      const ShopUrlData = new ShopFaker({name: `ToDelete${index + 1}Shop`});
      it('should go to add shop URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddURL${index}`, baseContext);

        await shopUrlPage.goToAddNewUrl(page);

        const pageTitle = await addShopUrlPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addShopUrlPage.pageTitleCreate);
      });

      it(`should create shop URl n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addURL${index}`, baseContext);

        const textResult = await addShopUrlPage.setVirtualUrl(page, ShopUrlData);
        await expect(textResult).to.contains(addShopUrlPage.successfulCreationMessage);
      });
    });
  });

  // 4 : Filter shop urls
  describe('Filter shop table', async () => {
    [
      {args: {filterBy: 'id_shop_url', filterValue: '10', filterType: 'input'}},
      {args: {filterBy: 's!name', filterValue: 'PrestaShop', filterType: 'input'}},
      {args: {filterBy: 'url', filterValue: 'ToDelete10', filterType: 'input'}},
      {args: {filterBy: 'main', filterValue: 'Yes', filterType: 'select'}, expected: 'Enabled'},
      {args: {filterBy: 'active', filterValue: 'Yes', filterType: 'select'}, expected: 'Enabled'},
    ].forEach((
      test: {args:{filterBy: string, filterValue: string, filterType: string}, expected?: string},
      index: number,
    ) => {
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
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await shopUrlPage.selectPaginationLimit(page, 20);
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

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await shopUrlPage.selectPaginationLimit(page, 50);
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
    ].forEach((test: {args: {testIdentifier: string, sortBy: string, sortDirection: string, isFloat?: boolean}}) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await shopUrlPage.getAllRowsColumnContent(page, test.args.sortBy);
        await shopUrlPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await shopUrlPage.getAllRowsColumnContent(page, test.args.sortBy);

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
      it(`should delete the shop url contains 'ToDelete${index + 1}Shop'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteShopUrl${index}_`, baseContext);

        await shopUrlPage.filterTable(page, 'input', 'url', `ToDelete${index + 1}Shop`);

        const textResult = await shopUrlPage.deleteShopURL(page, 1);
        await expect(textResult).to.contains(shopUrlPage.successfulDeleteMessage);
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
