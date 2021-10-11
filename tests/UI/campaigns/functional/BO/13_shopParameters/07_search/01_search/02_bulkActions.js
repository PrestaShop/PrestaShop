require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import common tests
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const searchPage = require('@pages/BO/shopParameters/search');
const addSearchPage = require('@pages/BO/shopParameters/search/add');

// Import data
const SearchFaker = require('@data/faker/search');

const baseContext = 'functional_BO_shopParameters_search_search_bulkActions';

// Browser and tab
let browserContext;
let page;
let numberOfSearch = 0;

/*
Create 2 aliases
Enable status by bulk actions
Disable status by bulk actions
Delete th created aliases by bulk actions
 */
describe('BO - Shop Parameters - Search : Enable/Disable and delete by bulk actions search', async () => {
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
    await expect(pageTitle).to.contains(searchPage.pageTitle);
  });

  it('should reset all filters and get number of aliases in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearch = await searchPage.resetAndGetNumberOfLines(page);
    await expect(numberOfSearch).to.be.above(0);
  });

  // 1 - Create 2 aliases
  const creationTests = new Array(2).fill(0, 0, 2);
  describe('Create 2 aliases in BO', async () => {
    creationTests.forEach((test, index) => {
      const aliasData = new SearchFaker({alias: `todelete${index}`});

      it('should go to add new search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddAliasPage${index}`, baseContext);

        await searchPage.goToAddNewAliasPage(page);

        const pageTitle = await addSearchPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addSearchPage.pageTitleCreate);
      });

      it(`should create alias n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAlias${index}`, baseContext);

        const textResult = await addSearchPage.setAlias(page, aliasData);
        await expect(textResult).to.contains(searchPage.successfulCreationMessage);

        const numberOfElementAfterCreation = await searchPage.getNumberOfElementInGrid(page);
        await expect(numberOfElementAfterCreation).to.be.equal(numberOfSearch + 1 + index);
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
      await expect(textAlias).to.contains('todelete');
    });

    const tests = [
      {args: {action: 'disable', value: false}},
      {args: {action: 'enable', value: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Status`, baseContext);

        const textResult = await searchPage.bulkSetStatus(page, test.args.value);
        await expect(textResult).to.contains(searchPage.successfulUpdateStatusMessage);

        const numberOfElementInGrid = await searchPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementInGrid; i++) {
          const textColumn = await searchPage.getStatus(page, i);
          await expect(textColumn).to.equal(test.args.value);
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
      await expect(textAlias).to.contains('todelete');
    });

    it('should delete aliases', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAliases', baseContext);

      const textResult = await searchPage.bulkDeleteAliases(page);
      await expect(textResult).to.contains(searchPage.successfulMultiDeleteMessage);

      const numberOfSearchAfterDelete = await searchPage.resetAndGetNumberOfLines(page);
      await expect(numberOfSearchAfterDelete).to.be.equal(numberOfSearch);
    });
  });
});
