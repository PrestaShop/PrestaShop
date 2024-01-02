// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import searchPage from '@pages/BO/shopParameters/search';
import addSearchPage from '@pages/BO/shopParameters/search/add';

// Import data
import SearchAliasData from '@data/faker/search';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_search_search_CRUDSearch';

/*
Create new search
Update the created search
Delete search
 */
describe('BO - Shop Parameters - Search : Create, update and delete search in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSearch: number = 0;

  const createAliasData: SearchAliasData = new SearchAliasData();
  const editSearchData: SearchAliasData = new SearchAliasData({alias: createAliasData.alias});

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

  it('should reset all filters and get number of alias in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearch = await searchPage.resetAndGetNumberOfLines(page);
    expect(numberOfSearch).to.be.above(0);
  });

  // 1 - Create alias
  describe('Create alias in BO', async () => {
    it('should go to add new search page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddAliasPage', baseContext);

      await searchPage.goToAddNewAliasPage(page);

      const pageTitle = await addSearchPage.getPageTitle(page);
      expect(pageTitle).to.contains(addSearchPage.pageTitleCreate);
    });

    it('should create alias and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAlias', baseContext);

      const textResult = await addSearchPage.setAlias(page, createAliasData);
      expect(textResult).to.contains(searchPage.successfulCreationMessage);

      const numberOfElementAfterCreation = await searchPage.getNumberOfElementInGrid(page);
      expect(numberOfElementAfterCreation).to.be.equal(numberOfSearch + 1);
    });
  });

  // 2 - Update alias
  describe('Update alias created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await searchPage.resetFilter(page);
      await searchPage.filterTable(page, 'input', 'alias', createAliasData.alias);

      const textEmail = await searchPage.getTextColumn(page, 1, 'alias');
      expect(textEmail).to.contains(createAliasData.alias);
    });

    it('should go to edit alias page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAliasPage', baseContext);

      await searchPage.gotoEditAliasPage(page, 1);

      const pageTitle = await addSearchPage.getPageTitle(page);
      expect(pageTitle).to.contains(addSearchPage.pageTitleEdit);
    });

    it('should update alias', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAlias', baseContext);

      const textResult = await addSearchPage.setAlias(page, editSearchData);
      expect(textResult).to.contains(searchPage.successfulUpdateMessage);

      const numberOfSearchAfterUpdate = await searchPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchAfterUpdate).to.be.equal(numberOfSearch + 1);
    });
  });

  // 3 - Delete alias
  describe('Delete alias', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await searchPage.resetFilter(page);
      await searchPage.filterTable(page, 'input', 'alias', createAliasData.alias);

      const textEmail = await searchPage.getTextColumn(page, 1, 'alias');
      expect(textEmail).to.contains(createAliasData.alias);
    });

    it('should delete alias', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAlias', baseContext);

      const textResult = await searchPage.deleteAlias(page, 1);
      expect(textResult).to.contains(searchPage.successfulDeleteMessage);

      const numberOfSearchAfterDelete = await searchPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchAfterDelete).to.be.equal(numberOfSearch);
    });
  });
});
