import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boSearchPage,
  boSearchAliasPage,
  boSearchAliasCreatePage,
  type BrowserContext,
  FakerSearchAlias,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const createAliasData: FakerSearchAlias = new FakerSearchAlias();
  const editSearchData: FakerSearchAlias = new FakerSearchAlias({search: createAliasData.search});

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Shop Parameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.searchLink,
    );

    const pageTitle = await boSearchPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSearchPage.pageTitle);
  });

  it('should go to \'Aliases\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAliasesTab', baseContext);

    await boSearchPage.goToAliasesPage(page);

    const pageTitle = await boSearchAliasPage.getPageTitle(page);
    expect(pageTitle).to.equals(boSearchAliasPage.pageTitle);
  });

  it('should reset all filters and get number of alias in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearch = await boSearchAliasPage.resetAndGetNumberOfLines(page);
    expect(numberOfSearch).to.be.above(0);
  });

  // 1 - Create alias
  describe('Create alias in BO', async () => {
    it('should go to add new search page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddAliasPage', baseContext);

      await boSearchAliasPage.goToAddNewAliasPage(page);

      const pageTitle = await boSearchAliasCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boSearchAliasCreatePage.pageTitleCreate);
    });

    it('should create alias and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAlias', baseContext);

      const textResult = await boSearchAliasCreatePage.setAlias(page, createAliasData);
      expect(textResult).to.contains(boSearchAliasPage.successfulCreationMessage);

      const numberOfElementAfterCreation = await boSearchAliasPage.getNumberOfElementInGrid(page);
      expect(numberOfElementAfterCreation).to.be.equal(numberOfSearch + 1);
    });
  });

  // 2 - Update alias
  describe('Update alias created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await boSearchAliasPage.resetFilter(page);
      await boSearchAliasPage.filterTable(page, 'input', 'search', createAliasData.search);

      const textEmail = await boSearchAliasPage.getTextColumn(page, 1, 'search');
      expect(textEmail).to.contains(createAliasData.search);
    });

    it('should go to edit alias page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAliasPage', baseContext);

      await boSearchAliasPage.gotoEditAliasPage(page, 1);

      const pageTitle = await boSearchAliasCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boSearchAliasCreatePage.pageTitleEdit);
    });

    it('should update alias', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAlias', baseContext);

      const textResult = await boSearchAliasCreatePage.setAlias(page, editSearchData);
      expect(textResult).to.contains(boSearchAliasPage.updateSuccessMessage);

      const numberOfSearchAfterUpdate = await boSearchAliasPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchAfterUpdate).to.be.equal(numberOfSearch + 1);
    });
  });

  // 3 - Delete alias
  describe('Delete alias', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await boSearchAliasPage.resetFilter(page);
      await boSearchAliasPage.filterTable(page, 'input', 'search', createAliasData.search);

      const textEmail = await boSearchAliasPage.getTextColumn(page, 1, 'search');
      expect(textEmail).to.contains(createAliasData.search);
    });

    it('should delete alias', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAlias', baseContext);

      const textResult = await boSearchAliasPage.deleteAlias(page, 1);
      expect(textResult).to.contains(boSearchAliasPage.successfulDeleteMessage);

      const numberOfSearchAfterDelete = await boSearchAliasPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchAfterDelete).to.be.equal(numberOfSearch);
    });
  });
});
