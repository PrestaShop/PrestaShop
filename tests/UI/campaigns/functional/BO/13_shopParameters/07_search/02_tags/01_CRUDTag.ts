// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import searchPage from '@pages/BO/shopParameters/search';
import tagsPage from '@pages/BO/shopParameters/search/tags';
import addTagPage from '@pages/BO/shopParameters/search/tags/add';

// Import data
import Languages from '@data/demo/languages';
import TagData from '@data/faker/tag';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_search_tags_CRUDTag';

/*
Create new tag
Update the created tag
Delete tag
 */
describe('BO - Shop Parameters - Search : Create, update and delete tag in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTags: number = 0;

  const createTagData: TagData = new TagData({language: Languages.english.name});
  const editTagData: TagData = new TagData({language: Languages.french.name});

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

  it('should go to \'Tags\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTagsPage', baseContext);

    await searchPage.goToTagsPage(page);
    numberOfTags = await tagsPage.getNumberOfElementInGrid(page);

    const pageTitle = await tagsPage.getPageTitle(page);
    expect(pageTitle).to.contains(tagsPage.pageTitle);
  });

  // 1 - Create tag
  describe('Create tag in BO', async () => {
    it('should go to add new tag page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddTagPage', baseContext);

      await tagsPage.goToAddNewTagPage(page);

      const pageTitle = await addTagPage.getPageTitle(page);
      expect(pageTitle).to.contains(addTagPage.pageTitleCreate);
    });

    it('should create tag and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createTag', baseContext);

      const textResult = await addTagPage.setTag(page, createTagData);
      expect(textResult).to.contains(tagsPage.successfulCreationMessage);

      const numberOfElementAfterCreation = await tagsPage.getNumberOfElementInGrid(page);
      expect(numberOfElementAfterCreation).to.be.equal(numberOfTags + 1);
    });
  });

  // 2 - Update tag
  describe('Update tag created', async () => {
    it('should go to edit tag page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditTagPage', baseContext);

      await tagsPage.gotoEditTagPage(page, 1);

      const pageTitle = await addTagPage.getPageTitle(page);
      expect(pageTitle).to.contains(addTagPage.pageTitleEdit);
    });

    it('should update tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateTag', baseContext);

      const textResult = await addTagPage.setTag(page, editTagData);
      expect(textResult).to.contains(tagsPage.successfulUpdateMessage);

      const numberOfTagsAfterUpdate = await tagsPage.getNumberOfElementInGrid(page);
      expect(numberOfTagsAfterUpdate).to.be.equal(numberOfTags + 1);
    });
  });

  // 3 - Delete tag
  describe('Delete tag', async () => {
    it('should delete tag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteTag', baseContext);

      const textResult = await tagsPage.deleteTag(page, 1);
      expect(textResult).to.contains(tagsPage.successfulDeleteMessage);

      const numberOfTagsAfterDelete = await tagsPage.getNumberOfElementInGrid(page);
      expect(numberOfTagsAfterDelete).to.be.equal(numberOfTags);
    });
  });
});
