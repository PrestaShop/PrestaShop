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
const tagFaker = require('@data/faker/tag');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_tags_CRUDTag';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfTags = 0;

const createTagData = new tagFaker();
const editTagData = new tagFaker();

/*
Create new tag
Update the created tag
Delete tag
 */
describe('Create, update and delete tag in BO', async () => {
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

    const pageTitle = await tagsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(tagsPage.pageTitle);
  });

  it('should reset all filters and get number of tags in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTags = await tagsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfTags).to.be.above(0);
  });

  // 1 - Create tag
  /*describe('Create tag in BO', async () => {
    it('should go to add new tag page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddtagPage', baseContext);

      await tagsPage.goToAddNewtagPage(page);
      const pageTitle = await addtagPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addtagPage.pageTitleCreate);
    });

    it('should create tag and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createtag', baseContext);

      const textResult = await addtagPage.createEdittag(page, createTagData);
      await expect(textResult).to.contains(tagsPage.successfulCreationMessage);

      const numbertagsAfterCreation = await tagsPage.getNumberOfElementInGrid(page);
      await expect(numbertagsAfterCreation).to.be.equal(numberOfTags + 1);
    });

    it('should filter list by name and get the new tag ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckNewtag', baseContext);

      await tagsPage.resetFilter(page);

      await tagsPage.filterTable(
        page,
        'input',
        'name',
        createTagData.name,
      );

      tagID = await tagsPage.getTextColumn(page, 1, 'id_tag');

      const name = await tagsPage.getTextColumn(page, 1, 'name');
      await expect(name).to.contains(createTagData.name);
    });
  });*/

  /*// 2 - Update tag
  describe('Update tag created', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'firstGoBackToBO', baseContext);

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await tagsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(tagsPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await tagsPage.resetFilter(page);

      await tagsPage.filterTable(
        page,
        'input',
        'name',
        createTagData.name,
      );

      const textEmail = await tagsPage.getTextColumn(page, 1, 'name');
      await expect(textEmail).to.contains(createTagData.name);
    });

    it('should go to edit tag page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEdittagPage', baseContext);

      await tagsPage.gotoEdittagPage(page, 1);
      const pageTitle = await addtagPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addtagPage.pageTitleEdit);
    });

    it('should update tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updatetag', baseContext);

      const textResult = await addtagPage.createEdittag(page, editTagData);
      await expect(textResult).to.contains(tagsPage.successfulUpdateMessage);

      const numberOfTagsAfterUpdate = await tagsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfTagsAfterUpdate).to.be.equal(numberOfTags + 1);
    });

    it('should filter list by name and get the edited tag ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckEditedtag', baseContext);

      await tagsPage.resetFilter(page);

      await tagsPage.filterTable(
        page,
        'input',
        'name',
        editTagData.name,
      );

      tagID = await tagsPage.getTextColumn(page, 1, 'id_tag');

      const name = await tagsPage.getTextColumn(page, 1, 'name');
      await expect(name).to.contains(editTagData.name);
    });
  });

  // 3 - Delete tag
  describe('Delete tag', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondGoBackToBO', baseContext);

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await tagsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(tagsPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await tagsPage.resetFilter(page);

      await tagsPage.filterTable(
        page,
        'input',
        'name',
        editTagData.name,
      );

      const textEmail = await tagsPage.getTextColumn(page, 1, 'name');
      await expect(textEmail).to.contains(editTagData.name);
    });

    it('should delete tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deletetag', baseContext);

      const textResult = await tagsPage.deletetag(page, 1);
      await expect(textResult).to.contains(tagsPage.successfulDeleteMessage);

      const numberOfTagsAfterDelete = await tagsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfTagsAfterDelete).to.be.equal(numberOfTags);
    });
  });*/
});
