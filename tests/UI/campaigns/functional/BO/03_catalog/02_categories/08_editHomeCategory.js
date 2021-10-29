require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const categoriesPage = require('@pages/BO/catalog/categories');
const editCategoryPage = require('@pages/BO/catalog/categories/add');

// Import data
const CategoryFaker = require('@data/faker/category');

const baseContext = 'functional_BO_catalog_categories_editHomeCategory';

let browserContext;
let page;
const editCategoryData = new CategoryFaker({name: 'Home'});

// Edit home category
describe('BO - Catalog - Categories : Edit home category', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create category image
    await files.generateImage(`${editCategoryData.name}.jpg`);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile(`${editCategoryData.name}.jpg`);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Catalog > Categories\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.categoriesLink,
    );

    await categoriesPage.closeSfToolBar(page);

    const pageTitle = await categoriesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(categoriesPage.pageTitle);
  });

  it('should go to Edit Home category page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditHomePage', baseContext);

    await categoriesPage.goToEditHomeCategoryPage(page);
    const pageTitle = await editCategoryPage.getPageTitle(page);
    await expect(pageTitle).to.contains(editCategoryPage.pageTitleEdit);
  });

  it('should update the category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCategory', baseContext);

    const textResult = await editCategoryPage.editHomeCategory(page, editCategoryData);
    await expect(textResult).to.equal(categoriesPage.successfulUpdateMessage);
  });
});
