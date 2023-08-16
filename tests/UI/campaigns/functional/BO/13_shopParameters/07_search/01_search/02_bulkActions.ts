// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import searchPage from '@pages/BO/shopParameters/search';
import addSearchPage from '@pages/BO/shopParameters/search/add';

// Import data
import SearchAliasData from '@data/faker/search';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_search_search_bulkActions';

/*
Create 2 aliases
Enable status by bulk actions
Disable status by bulk actions
Delete th created aliases by bulk actions
 */
describe('BO - Shop Parameters - Search : Enable/Disable and delete by bulk actions search', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSearch: number = 0;

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

  it('should go to \'Shop Parameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.searchLink,
    );

    const pageTitle = await searchPage.getPageTitle(page);
    expect(pageTitle).to.contains(searchPage.pageTitle);
  });

  it('should reset all filters and get number of aliases in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearch = await searchPage.resetAndGetNumberOfLines(page);
    expect(numberOfSearch).to.be.above(0);
  });

  // 1 - Create 2 aliases
  const creationTests: number[] = new Array(2).fill(0, 0, 2);
  describe('Create 2 aliases in BO', async () => {
    creationTests.forEach((test: number, index: number) => {
      const aliasData: SearchAliasData = new SearchAliasData({alias: `todelete${index}`});

      it('should go to add new search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddAliasPage${index}`, baseContext);

        await searchPage.goToAddNewAliasPage(page);

        const pageTitle = await addSearchPage.getPageTitle(page);
        expect(pageTitle).to.contains(addSearchPage.pageTitleCreate);
      });

      it(`should create alias n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAlias${index}`, baseContext);

        const textResult = await addSearchPage.setAlias(page, aliasData);
        expect(textResult).to.contains(searchPage.successfulCreationMessage);

        const numberOfElementAfterCreation = await searchPage.getNumberOfElementInGrid(page);
        expect(numberOfElementAfterCreation).to.be.equal(numberOfSearch + 1 + index);
      });
    });
  });

  // 2- Enable/Disable aliases by bulk actions
  describe('Enable/Disable the status by bulk actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToEnableDisable', baseContext);

      await searchPage.resetFilter(page);
      await searchPage.filterTable(page, 'input', 'alias', 'todelete');

      const textAlias = await searchPage.getTextColumn(page, 1, 'alias');
      expect(textAlias).to.contains('todelete');
    });

    [
      {args: {action: 'disable', value: false}},
      {args: {action: 'enable', value: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Status`, baseContext);

        const textResult = await searchPage.bulkSetStatus(page, test.args.value);
        expect(textResult).to.contains(searchPage.successfulUpdateStatusMessage);

        const numberOfElementInGrid = await searchPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementInGrid; i++) {
          const textColumn = await searchPage.getStatus(page, i);
          expect(textColumn).to.equal(test.args.value);
        }
      });
    });
  });

  // 3 - Delete aliases by bulk actions
  describe('Delete aliases by bulk actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await searchPage.resetFilter(page);
      await searchPage.filterTable(page, 'input', 'alias', 'todelete');

      const textAlias = await searchPage.getTextColumn(page, 1, 'alias');
      expect(textAlias).to.contains('todelete');
    });

    it('should delete aliases', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAliases', baseContext);

      const textResult = await searchPage.bulkDeleteAliases(page);
      expect(textResult).to.contains(searchPage.successfulMultiDeleteMessage);

      const numberOfSearchAfterDelete = await searchPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchAfterDelete).to.be.equal(numberOfSearch);
    });
  });
});
