require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const PagesPage = require('@pages/BO/design/pages/pages');
const AddPageCategoryPage = require('@pages/BO/design/pages/addPageCategory');
const AddPagePage = require('@pages/BO/design/pages/addPage');
const FOBasePage = require('@pages/FO/FObasePage');
const SiteMapPage = require('@pages/FO/siteMap');
const CMSPage = require('@pages/FO/cms');
const CategoryPageFaker = require('@data/faker/CMSCategory');
const PageFaker = require('@data/faker/CMSPage');

let browser;
let page;
let numberOfCategories = 0;
let numberOfPages = 0;
let createCategoryData;
let editCategoryData;
let createPageData;
let editPageData;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    pagesPage: new PagesPage(page),
    addPageCategoryPage: new AddPageCategoryPage(page),
    addPagePage: new AddPagePage(page),
    foBasePage: new FOBasePage(page),
    siteMapPage: new SiteMapPage(page),
    cmsPage: new CMSPage(page),
  };
};

// Create, Read, Update and Delete Page Category and Page
describe('Create, Read, Update and Delete Page Category and Page', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    createCategoryData = await (new CategoryPageFaker());
    editCategoryData = await (new CategoryPageFaker({name: `update${createCategoryData.name}`}));
    createPageData = await (new PageFaker());
    editPageData = await (new PageFaker({
      displayed: false,
      title: `update${createPageData.title}`,
    }));
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to categories page
  loginCommon.loginBO();
  it('should go to "Design>Pages" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.designParentLink,
      this.pageObjects.boBasePage.pagesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
  });
  it('should reset all filters and get number of categories in BO', async function () {
    if (await this.pageObjects.pagesPage.elementVisible(
      this.pageObjects.pagesPage.categoryfilterResetButton, 2000)) {
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.categoryfilterResetButton);
    }
    numberOfCategories = await this.pageObjects.pagesPage.getNumberFromText(
      this.pageObjects.pagesPage.categoryGridTitle);
    if (numberOfCategories === 0) {
      const filterButtonVisibility = await this.pageObjects.pagesPage.elementVisible(
        this.pageObjects.pagesPage.categoryfilterResetButton, 2000);
      await expect(filterButtonVisibility).to.be.false;
    } else await expect(numberOfCategories).to.be.above(0);
  });
  // 1 : Create category then go to FO to check it
  describe('Create Category in BO and check it in FO', async () => {
    it('should go to add new category page', async function () {
      await this.pageObjects.pagesPage.clickAndWaitForNavigation(
        this.pageObjects.pagesPage.addNewPageCategoryLink,
      );
      const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
    });
    it('should create category ', async function () {
      const textResult = await this.pageObjects.addPageCategoryPage.createEditPageCategory(createCategoryData);
      await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
    });
    it('should go back to categories list and check the categories number', async function () {
      await this.pageObjects.pagesPage.clickAndWaitForNavigation(
        this.pageObjects.pagesPage.backToListButton,
      );
      const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
      const numberOfCategoriesAfterCreation = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
    });
    it('should search for the new category and check result', async function () {
      await this.pageObjects.pagesPage.filterPageCategories(
        'input',
        'name',
        createCategoryData.name,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.categoryGridTitle);
      if (numberOfCategories === 0) {
        await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories + 1);
      } else await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'name'),
        );
        await expect(textColumn).to.contains(createCategoryData.name);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should go to FO and check the created category', async function () {
      const pageCategoryID = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', 1).replace(
          '%COLUMN',
          'id_cms_category',
        ),
      );
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeLanguage('en');
      await this.pageObjects.foBasePage.clickAndWaitForNavigation(this.pageObjects.foBasePage.siteMapLink);
      const pageTitle = await this.pageObjects.siteMapPage.getPageTitle();
      await expect(pageTitle).to.equal(this.pageObjects.siteMapPage.pageTitle);
      const pageCategoryName = await this.pageObjects.siteMapPage.getTextContent(
        this.pageObjects.siteMapPage.categoryPageNameSelect.replace('%ID', pageCategoryID));
      await expect(pageCategoryName).to.contains(createCategoryData.name);
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });
  // 2 : Create Page then go to FO to check it
  describe('Create Page in BO and preview it in FO', async () => {
    it('should click on view category', async function () {
      await this.pageObjects.pagesPage.viewCategory('1');
      const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
      numberOfPages = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pagesGridTitle);
    });
    it('should go to add new page page', async function () {
      await this.pageObjects.pagesPage.clickAndWaitForNavigation(
        this.pageObjects.pagesPage.addNewPageLink,
      );
      const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
    });
    it('should create page and click on Save and preview button', async function () {
      const textResult = await this.pageObjects.addPagePage.createEditPage(createPageData, true);
      await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
    });
    it('should check the created page in FO', async function () {
      page = await this.pageObjects.addPagePage.switchTab(browser, 2);
      this.pageObjects = await init();
      const pageTitle = await this.pageObjects.cmsPage.getTextContent(this.pageObjects.cmsPage.pageTitle);
      await expect(pageTitle).to.contains(createPageData.title);
      const metaTitle = await this.pageObjects.cmsPage.getPageTitle();
      await expect(metaTitle).to.equal(createPageData.metaTitle);
      const pageContent = await this.pageObjects.cmsPage.getTextContent(this.pageObjects.cmsPage.pageContent);
      await expect(pageContent).to.include(createPageData.content);
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });
  // 3 : Update category then go to FO to check it
  describe('Update Category created in BO and check it in FO', async () => {
    it('should switch to BO and click on cancel button', async function () {
      page = await this.pageObjects.addPagePage.switchTab(browser, 1);
      await this.pageObjects.addPagePage.cancelPage();
      const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
    });
    it('should reset all filters and get number of categories in BO', async function () {
      if (await this.pageObjects.pagesPage.elementVisible(
        this.pageObjects.pagesPage.categoryfilterResetButton, 2000)) {
        await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.categoryfilterResetButton);
      }
      if (numberOfCategories === 0) {
        const filterButtonVisibility = await this.pageObjects.pagesPage.elementVisible(
          this.pageObjects.pagesPage.categoryfilterResetButton, 2000);
        await expect(filterButtonVisibility).to.be.false;
      } else await expect(numberOfCategories).to.be.above(0);
    });
    it('should search for the created category and check result', async function () {
      await this.pageObjects.pagesPage.filterPageCategories(
        'input',
        'name',
        createCategoryData.name,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.categoryGridTitle);
      if (numberOfCategories === 0) {
        await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories + 1);
      } else await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'name'),
        );
        await expect(textColumn).to.contains(createCategoryData.name);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should go to edit category page', async function () {
      await this.pageObjects.pagesPage.goToEditCategoryPage('1');
      const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
    });
    it('should update the created Category', async function () {
      const textResult = await this.pageObjects.addPageCategoryPage.createEditPageCategory(editCategoryData);
      await expect(textResult).to.equal(this.pageObjects.addPageCategoryPage.successfulUpdateMessage);
    });
    it('should go back to categories list', async function () {
      await this.pageObjects.pagesPage.clickAndWaitForNavigation(
        this.pageObjects.pagesPage.backToListButton,
      );
      const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
    });
    it('should search for the updated category and check result', async function () {
      await this.pageObjects.pagesPage.filterPageCategories(
        'input',
        'name',
        editCategoryData.name,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.categoryGridTitle);
      if (numberOfCategories === 0) {
        await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories + 1);
      } else await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'name'),
        );
        await expect(textColumn).to.contains(editCategoryData.name);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should go to FO and check the updated category', async function () {
      const pageCategoryID = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', 1).replace(
          '%COLUMN',
          'id_cms_category',
        ),
      );
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeLanguage('en');
      await this.pageObjects.foBasePage.clickAndWaitForNavigation(this.pageObjects.foBasePage.siteMapLink);
      const pageTitle = await this.pageObjects.siteMapPage.getPageTitle();
      await expect(pageTitle).to.equal(this.pageObjects.siteMapPage.pageTitle);
      const pageCategoryName = await this.pageObjects.siteMapPage.getTextContent(
        this.pageObjects.siteMapPage.categoryPageNameSelect.replace('%ID', pageCategoryID));
      await expect(pageCategoryName).to.contains(editCategoryData.name);
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });
  // 4 : Update page then go to FO to check it
  describe('Update page created in BO and view it in FO', async () => {
    it('should click on view category', async function () {
      await this.pageObjects.pagesPage.viewCategory('1');
      const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
    });
    it('should reset all filters and get number of pages in BO', async function () {
      if (await this.pageObjects.pagesPage.elementVisible(this.pageObjects.pagesPage.pagefilterResetButton, 2000)) {
        await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      }
      const numberOfPagesAfterCreate = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pagesGridTitle);
      await expect(numberOfPagesAfterCreate).to.be.above(0);
    });
    it('should search for the created Page and check result', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'meta_title',
        createPageData.title,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pagesGridTitle);
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'meta_title'),
        );
        await expect(textColumn).to.contains(createPageData.title);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should go to edit page', async function () {
      await this.pageObjects.pagesPage.goToEditPagePage('1');
      const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
    });
    it('should update the created page then click on save and preview', async function () {
      const textResult = await this.pageObjects.addPagePage.createEditPage(editPageData, true);
      await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulUpdateMessage);
    });
    it('should check that the page does not exist in FO', async function () {
      page = await this.pageObjects.addPagePage.switchTab(browser, 2);
      this.pageObjects = await init();
      const pageTitle = await this.pageObjects.cmsPage.getTextContent(this.pageObjects.cmsPage.pageTitle);
      await expect(pageTitle).to.include(this.pageObjects.cmsPage.pageNotFound);
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });
    it('should update the page and click on save', async function () {
      page = await this.pageObjects.addPagePage.switchTab(browser, 1);
      const textResult = await this.pageObjects.addPagePage.createEditPage(createPageData);
      await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulUpdateMessage);
    });
  });
  // 4 : Delete Page and Category from BO
  describe('Delete Page and Category', async () => {
    it('should search for the created page and check result', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'meta_title',
        createPageData.title,
      );
      const textColumn = await this.pageObjects.pagesPage.getTextContent(
        this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', 1).replace('%COLUMN', 'meta_title'),
      );
      await expect(textColumn).to.contains(createPageData.title);
    });
    it('should delete Page', async function () {
      const textResult = await this.pageObjects.pagesPage.deletePage('1');
      await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulDeleteMessage);
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      const numberOfPagesAfterDeletion = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pagesGridTitle);
      await expect(numberOfPagesAfterDeletion).to.be.equal(numberOfPages);
    });
    it('should click on back to list', async function () {
      await this.pageObjects.pagesPage.clickAndWaitForNavigation(
        this.pageObjects.pagesPage.backToListButton,
      );
      const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
    });
    it('should delete category', async function () {
      const textResult = await this.pageObjects.pagesPage.deleteCategory('1');
      await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulDeleteMessage);
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.categoryfilterResetButton);
      const numberOfCategoriesAfterDeletion = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterDeletion).to.be.equal(numberOfCategories);
    });
  });
});
