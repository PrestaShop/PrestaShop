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
const {Products} = require('@data/demo/products');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_tags_CRUDTag';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;
let numberOfTags = 0;

const createTagData = new TagFaker({language: Languages.english.name});
const editTagData = new TagFaker({language: Languages.french.name, products: Products.demo_1.nameFr});

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

    numberOfTags = await tagsPage.getNumberOfElementInGrid(page);

    const pageTitle = await tagsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(tagsPage.pageTitle);
  });

  // 1 - Create tag
  describe('Create tag in BO', async () => {
    it('should go to add new tag page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddTagPage', baseContext);

      await tagsPage.goToAddNewTagPage(page);

      const pageTitle = await addTagPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addTagPage.pageTitleCreate);
    });

    it('should create tag and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createTag', baseContext);

      const textResult = await addTagPage.setTag(page, createTagData);
      await expect(textResult).to.contains(tagsPage.successfulCreationMessage);

      const numberOfElementAfterCreation = await tagsPage.getNumberOfElementInGrid(page);
      await expect(numberOfElementAfterCreation).to.be.equal(numberOfTags + 1);
    });
  });

  // 2 - Update tag
  describe('Update tag created', async () => {
    it('should go to edit tag page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditTagPage', baseContext);

      await tagsPage.gotoEditTagPage(page, 1);

      const pageTitle = await addTagPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addTagPage.pageTitleEdit);
    });

    it('should update tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateTag', baseContext);

      const textResult = await addTagPage.setTag(page, editTagData);
      await expect(textResult).to.contains(tagsPage.successfulUpdateMessage);

      const numberOfTagsAfterUpdate = await tagsPage.getNumberOfElementInGrid(page);
      await expect(numberOfTagsAfterUpdate).to.be.equal(numberOfTags + 1);
    });
  });

  /*// 3 - Delete tag
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
