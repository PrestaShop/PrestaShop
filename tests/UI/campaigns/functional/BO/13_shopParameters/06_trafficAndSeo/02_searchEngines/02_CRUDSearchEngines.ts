import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boSearchEnginesPage,
  boSearchEnginesCreatePage,
  boSeoUrlsPage,
  type BrowserContext,
  FakerSearchEngine,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_searchEngines_CRUDSearchEngines';

/*
Create new search engine
Update it
And delete it
 */
describe('BO - Shop Parameters - Traffic & SEO : Create, update and delete search engines', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSearchEngines: number = 0;

  const createSearchEngineData: FakerSearchEngine = new FakerSearchEngine();
  const editSearchEngineData: FakerSearchEngine = new FakerSearchEngine();

  // before and after functions
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

  it('should go to \'Shop Parameters > Traffic & SEO\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoAndUrlsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.trafficAndSeoLink,
    );

    await boSeoUrlsPage.closeSfToolBar(page);

    const pageTitle = await boSeoUrlsPage.getPageTitle(page);
    expect(pageTitle).to.contain(boSeoUrlsPage.pageTitle);
  });

  it('should go to \'Search Engines\' pge', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchEnginesPage', baseContext);

    await boSeoUrlsPage.goToSearchEnginesPage(page);

    const pageTitle = await boSearchEnginesPage.getPageTitle(page);
    expect(pageTitle).to.contain(boSearchEnginesPage.pageTitle);
  });

  it('should reset all filters and get number of search engines in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearchEngines = await boSearchEnginesPage.resetAndGetNumberOfLines(page);
    expect(numberOfSearchEngines).to.be.above(0);
  });

  describe('Create search engine', async () => {
    it('should go to new search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewSearchEnginePage', baseContext);

      await boSearchEnginesPage.goToNewSearchEnginePage(page);

      const pageTitle = await boSearchEnginesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contain(boSearchEnginesCreatePage.pageTitleCreate);
    });

    it('should create search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSearchEngine', baseContext);

      const result = await boSearchEnginesCreatePage.createEditSearchEngine(page, createSearchEngineData);
      expect(result).to.contain(boSearchEnginesPage.successfulCreationMessage);

      const numberOfSearchEnginesAfterCreation = await boSearchEnginesPage.getNumberOfElementInGrid(page);
      expect(numberOfSearchEnginesAfterCreation).to.equal(numberOfSearchEngines + 1);
    });
  });

  describe('Update search engine', async () => {
    it('should filter by server', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      await boSearchEnginesPage.filterTable(page, 'server', createSearchEngineData.server);

      const numberOfSearchEnginesAfterFilter = await boSearchEnginesPage.getNumberOfElementInGrid(page);
      expect(numberOfSearchEnginesAfterFilter).to.be.at.least(1);

      const textColumn = await boSearchEnginesPage.getTextColumn(page, 1, 'server');
      expect(textColumn).to.contain(createSearchEngineData.server);
    });

    it('should go to edit first search engine page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditSearchEngine', baseContext);

      await boSearchEnginesPage.goToEditSearchEnginePage(page, 1);

      const pageTitle = await boSearchEnginesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contain(boSearchEnginesCreatePage.pageTitleEdit);
    });

    it('should edit search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editSearchEngine', baseContext);

      const result = await boSearchEnginesCreatePage.createEditSearchEngine(page, editSearchEngineData);
      expect(result).to.contain(boSearchEnginesPage.successfulUpdateMessage);
    });

    it('should reset filter and check number of search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterUpdate', baseContext);

      const numberOfSearchEnginesAfterUpdate = await boSearchEnginesPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchEnginesAfterUpdate).to.equal(numberOfSearchEngines + 1);
    });
  });

  describe('Delete search engine', async () => {
    it('should filter by server', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boSearchEnginesPage.filterTable(page, 'server', editSearchEngineData.server);

      const numberOfSearchEnginesAfterFilter = await boSearchEnginesPage.getNumberOfElementInGrid(page);
      expect(numberOfSearchEnginesAfterFilter).to.be.at.least(1);

      const textColumn = await boSearchEnginesPage.getTextColumn(page, 1, 'server');
      expect(textColumn).to.contain(editSearchEngineData.server);
    });

    it('should delete search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSearchEngine', baseContext);

      // delete search engine in first row
      const result = await boSearchEnginesPage.deleteSearchEngine(page, 1);
      expect(result).to.be.contain(boSearchEnginesPage.successfulDeleteMessage);
    });

    it('should reset filter and check number of searchEngine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfSearchEnginesAfterDelete = await boSearchEnginesPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchEnginesAfterDelete).to.equal(numberOfSearchEngines);
    });
  });
});
