require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const seoAndUrlsPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls');
const searchEnginesPage = require('@pages/BO/shopParameters/trafficAndSeo/searchEngines');
const addSearchEnginePage = require('@pages/BO/shopParameters/trafficAndSeo/searchEngines/add');

const baseContext = 'functional_BO_shopParameters_TrafficAndSeo_searchEngines_CRUDSearchEngines';

let browserContext;
let page;

// Create date for test
const SearchEngineFaker = require('@data/faker/searchEngine');

const createSearchEngineData = new SearchEngineFaker();
const editSearchEngineData = new SearchEngineFaker();

let numberOfSearchEngines = 0;

/*
Create new search engine
Update it
And delete it
 */
describe('BO - Shop Parameters - Traffic & SEO : Create, update and delete search engines', async () => {
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
    await expect(pageTitle).to.contain(seoAndUrlsPage.pageTitle);
  });

  it('should go to \'Search Engines\' pge', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchEnginesPage', baseContext);

    await seoAndUrlsPage.goToSearchEnginesPage(page);

    const pageTitle = await searchEnginesPage.getPageTitle(page);
    await expect(pageTitle).to.contain(searchEnginesPage.pageTitle);
  });

  it('should reset all filters and get number of search engines in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearchEngines = await searchEnginesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfSearchEngines).to.be.above(0);
  });

  describe('Create search engine', async () => {
    it('should go to new search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewSearchEnginePage', baseContext);

      await searchEnginesPage.goToNewSearchEnginePage(page);
      const pageTitle = await addSearchEnginePage.getPageTitle(page);
      await expect(pageTitle).to.contain(addSearchEnginePage.pageTitleCreate);
    });

    it('should create search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSearchEngine', baseContext);

      const result = await addSearchEnginePage.createEditSearchEngine(page, createSearchEngineData);
      await expect(result).to.contain(searchEnginesPage.successfulCreationMessage);

      const numberOfSearchEnginesAfterCreation = await searchEnginesPage.getNumberOfElementInGrid(page);
      await expect(numberOfSearchEnginesAfterCreation).to.equal(numberOfSearchEngines + 1);
    });
  });

  describe('Update search engine', async () => {
    it('should filter by server', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      await searchEnginesPage.filterTable(page, 'server', createSearchEngineData.server);

      const numberOfSearchEnginesAfterFilter = await searchEnginesPage.getNumberOfElementInGrid(page);
      await expect(numberOfSearchEnginesAfterFilter).to.be.at.least(1);

      const textColumn = await searchEnginesPage.getTextColumn(page, 1, 'server');
      await expect(textColumn).to.contain(createSearchEngineData.server);
    });

    it('should go to edit first search engine page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditSearchEngine', baseContext);

      await searchEnginesPage.goToEditSearchEnginePage(page, 1);
      const pageTitle = await addSearchEnginePage.getPageTitle(page);
      await expect(pageTitle).to.contain(addSearchEnginePage.pageTitleEdit);
    });

    it('should edit search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editSearchEngine', baseContext);

      const result = await addSearchEnginePage.createEditSearchEngine(page, editSearchEngineData);
      await expect(result).to.contain(searchEnginesPage.successfulUpdateMessage);
    });

    it('should reset filter and check number of search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterUpdate', baseContext);

      const numberOfSearchEnginesAfterUpdate = await searchEnginesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfSearchEnginesAfterUpdate).to.equal(numberOfSearchEngines + 1);
    });
  });

  describe('Delete search engine', async () => {
    it('should filter by server', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await searchEnginesPage.filterTable(page, 'server', editSearchEngineData.server);

      const numberOfSearchEnginesAfterFilter = await searchEnginesPage.getNumberOfElementInGrid(page);
      await expect(numberOfSearchEnginesAfterFilter).to.be.at.least(1);

      const textColumn = await searchEnginesPage.getTextColumn(page, 1, 'server');
      await expect(textColumn).to.contain(editSearchEngineData.server);
    });

    it('should delete search engine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSearchEngine', baseContext);

      // delete search engine in first row
      const result = await searchEnginesPage.deleteSearchEngine(page, 1);
      await expect(result).to.be.contain(searchEnginesPage.successfulDeleteMessage);
    });

    it('should reset filter and check number of searchEngine', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfSearchEnginesAfterDelete = await searchEnginesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfSearchEnginesAfterDelete).to.equal(numberOfSearchEngines);
    });
  });
});
