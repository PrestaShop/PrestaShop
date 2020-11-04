require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');

// Import common tests
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const searchPage = require('@pages/BO/shopParameters/search');
const tagsPage = require('@pages/BO/shopParameters/search/tags');
const addTagPage = require('@pages/BO/shopParameters/search/tags/add');

// Import data
const TagFaker = require('@data/faker/tag');
const {Languages} = require('@data/demo/languages');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_search_tags_filterSortAndPagination';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;
let numberOfTags = 0;

/*
Create 21 tags
Filter tags by : Id, Language, Name, Products
Sort tags by : Id, Language, Name, Products
Pagination next and previous
Delete by bulk actions
 */
describe('Filter, sort and pagination tag in BO', async () => {
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

  it('should go to \'ShopParameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.searchLink,
    );

    const pageTitle = await searchPage.getPageTitle(page);
    await expect(pageTitle).to.contains(searchPage.pageTitle);
  });

  it('should go to \'Tags\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTagsPage', baseContext);

    await searchPage.goToTagsPage(page);

    numberOfTags = await tagsPage.getNumberOfElementInGrid(page);

    const pageTitle = await tagsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(tagsPage.pageTitle);
  });

  // 1 - Create tag
  const creationTests = new Array(21).fill(0, 0, 21);

  creationTests.forEach((test, index) => {
    describe(`Create tag n°${index + 1} in BO`, async () => {
      const tagData = new TagFaker({name: `todelete${index}`, language: Languages.english.name});

      it('should go to add new tag page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddTagPage${index}`, baseContext);

        await tagsPage.goToAddNewTagPage(page);

        const pageTitle = await addTagPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addTagPage.pageTitleCreate);
      });

      it('should create tag and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createTag${index}`, baseContext);

        const textResult = await addTagPage.setTag(page, tagData);
        await expect(textResult).to.contains(tagsPage.successfulCreationMessage);

        const numberOfElementAfterCreation = await tagsPage.getNumberOfElementInGrid(page);
        await expect(numberOfElementAfterCreation).to.be.equal(numberOfTags + 1 + index);
      });
    });
  });
});
