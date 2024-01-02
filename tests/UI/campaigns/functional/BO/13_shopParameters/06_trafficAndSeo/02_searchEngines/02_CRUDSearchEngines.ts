// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import seoAndUrlsPage from '@pages/BO/shopParameters/trafficAndSeo/seoAndUrls';
import searchEnginesPage from '@pages/BO/shopParameters/trafficAndSeo/searchEngines';
import addSearchEnginePage from '@pages/BO/shopParameters/trafficAndSeo/searchEngines/add';

// Import data
import SearchEngineDate from '@data/faker/searchEngine';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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

  const createSearchEngineData: SearchEngineDate = new SearchEngineDate();
  const editSearchEngineData: SearchEngineDate = new SearchEngineDate();

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

  it('should go to \'Shop Parameters > Traffic & SEO\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoAndUrlsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.trafficAndSeoLink,
    );

    await seoAndUrlsPage.closeSfToolBar(page);

    const pageTitle = await seoAndUrlsPage.getPageTitle(page);
    expect(pageTitle).to.contain(seoAndUrlsPage.pageTitle);
  });

  it('should go to \'Search Engines\' pge', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchEnginesPage', baseContext);

    await seoAndUrlsPage.goToSearchEnginesPage(page);

    const pageTitle = await searchEnginesPage.getPageTitle(page);
    expect(pageTitle).to.contain(searchEnginesPage.pageTitle);
  });

  it('should reset all filters and get number of search engines in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearchEngines = await searchEnginesPage.resetAndGetNumberOfLines(page);
    expect(numberOfSearchEngines).to.be.above(0);
  });

  describe('Create search engine', async () => {
    it('should go to new search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewSearchEnginePage', baseContext);

      await searchEnginesPage.goToNewSearchEnginePage(page);

      const pageTitle = await addSearchEnginePage.getPageTitle(page);
      expect(pageTitle).to.contain(addSearchEnginePage.pageTitleCreate);
    });

    it('should create search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSearchEngine', baseContext);

      const result = await addSearchEnginePage.createEditSearchEngine(page, createSearchEngineData);
      expect(result).to.contain(searchEnginesPage.successfulCreationMessage);

      const numberOfSearchEnginesAfterCreation = await searchEnginesPage.getNumberOfElementInGrid(page);
      expect(numberOfSearchEnginesAfterCreation).to.equal(numberOfSearchEngines + 1);
    });
  });

  describe('Update search engine', async () => {
    it('should filter by server', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      await searchEnginesPage.filterTable(page, 'server', createSearchEngineData.server);

      const numberOfSearchEnginesAfterFilter = await searchEnginesPage.getNumberOfElementInGrid(page);
      expect(numberOfSearchEnginesAfterFilter).to.be.at.least(1);

      const textColumn = await searchEnginesPage.getTextColumn(page, 1, 'server');
      expect(textColumn).to.contain(createSearchEngineData.server);
    });

    it('should go to edit first search engine page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditSearchEngine', baseContext);

      await searchEnginesPage.goToEditSearchEnginePage(page, 1);

      const pageTitle = await addSearchEnginePage.getPageTitle(page);
      expect(pageTitle).to.contain(addSearchEnginePage.pageTitleEdit);
    });

    it('should edit search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editSearchEngine', baseContext);

      const result = await addSearchEnginePage.createEditSearchEngine(page, editSearchEngineData);
      expect(result).to.contain(searchEnginesPage.successfulUpdateMessage);
    });

    it('should reset filter and check number of search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterUpdate', baseContext);

      const numberOfSearchEnginesAfterUpdate = await searchEnginesPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchEnginesAfterUpdate).to.equal(numberOfSearchEngines + 1);
    });
  });

  describe('Delete search engine', async () => {
    it('should filter by server', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await searchEnginesPage.filterTable(page, 'server', editSearchEngineData.server);

      const numberOfSearchEnginesAfterFilter = await searchEnginesPage.getNumberOfElementInGrid(page);
      expect(numberOfSearchEnginesAfterFilter).to.be.at.least(1);

      const textColumn = await searchEnginesPage.getTextColumn(page, 1, 'server');
      expect(textColumn).to.contain(editSearchEngineData.server);
    });

    it('should delete search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSearchEngine', baseContext);

      // delete search engine in first row
      const result = await searchEnginesPage.deleteSearchEngine(page, 1);
      expect(result).to.be.contain(searchEnginesPage.successfulDeleteMessage);
    });

    it('should reset filter and check number of searchEngine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfSearchEnginesAfterDelete = await searchEnginesPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchEnginesAfterDelete).to.equal(numberOfSearchEngines);
    });
  });
});
