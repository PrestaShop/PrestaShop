/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const PageFaker = require('@data/faker/CMSpage');

// Import pages
const LoginPage = require('@pages/BO/login/index');
const DashboardPage = require('@pages/BO/dashboard/index');
const PagesPage = require('@pages/BO/design/pages/index');
const AddPageCategoryPage = require('@pages/BO/design/pages/pageCategory/add');
const AddPagePage = require('@pages/BO/design/pages/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_pages_pagesBulkAction';


let browserContext;
let page;
let numberOfPages = 0;

const firstPageData = new PageFaker({title: 'todelete'});
const secondPageData = new PageFaker({title: 'todelete'});

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    pagesPage: new PagesPage(page),
    addPageCategoryPage: new AddPageCategoryPage(page),
    addPagePage: new AddPagePage(page),
  };
};

// Create Pages, Then disable / Enable and Delete with Bulk actions
describe('Create Pages, Then disable / Enable and Delete with Bulk actions', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO
  loginCommon.loginBO();

  // Go to Design>Pages page
  it('should go to "Design>Pages" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCmsPagesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.designParentLink,
      this.pageObjects.dashboardPage.pagesLink,
    );

    await this.pageObjects.pagesPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
  });

  it('should reset filter and get number of pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

    numberOfPages = await this.pageObjects.pagesPage.resetAndGetNumberOfLines('cms_page');
    await expect(numberOfPages).to.be.above(0);
  });

  // 1 : Create 2 pages In BO
  describe('Create 2 pages', async () => {
    const pagesToCreate = [firstPageData, secondPageData];

    pagesToCreate.forEach((pageToCreate, index) => {
      it('should go to add new page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddPage${index + 1}`, baseContext);

        await this.pageObjects.pagesPage.goToAddNewPage();
        const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
      });

      it('should create page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createPage${index + 1}`, baseContext);

        const textResult = await this.pageObjects.addPagePage.createEditPage(pageToCreate);
        await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
      });
    });
  });

  // 2 : Enable/Disable Pages created with bulk actions
  describe('Enable and Disable pages with Bulk Actions', async () => {
    it('should filter list by Title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkEditStatus', baseContext);

      await this.pageObjects.pagesPage.filterTable(
        'cms_page',
        'input',
        'meta_title',
        'todelete',
      );

      const textResult = await this.pageObjects.pagesPage.getTextColumnFromTableCmsPage(
        1,
        'meta_title',
      );

      await expect(textResult).to.contains('todelete');
    });

    const statuses = [
      {args: {status: 'disable', enable: false}, expected: 'clear'},
      {args: {status: 'enable', enable: true}, expected: 'check'},
    ];

    statuses.forEach((pageStatus) => {
      it(`should ${pageStatus.args.status} pages with Bulk Actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${pageStatus.args.status}Page`, baseContext);

        const textResult = await this.pageObjects.pagesPage.changeEnabledColumnBulkActions(
          'cms_page',
          pageStatus.args.enable);

        await expect(textResult).to.be.equal(this.pageObjects.pagesPage.successfulUpdateStatusMessage);

        const numberOfPagesInGrid = await this.pageObjects.pagesPage.getNumberOfElementInGrid('cms_page');
        await expect(numberOfPagesInGrid).to.be.at.most(numberOfPages);

        for (let i = 1; i <= numberOfPagesInGrid; i++) {
          const textColumn = await this.pageObjects.pagesPage.getTextColumnFromTableCmsPage(i, 'active');
          await expect(textColumn).to.contains(pageStatus.expected);
        }
      });
    });
  });

  // 3 : Delete Pages created with bulk actions
  describe('Delete pages with Bulk Actions', async () => {
    it('should filter list by Title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await this.pageObjects.pagesPage.filterTable(
        'cms_page',
        'input',
        'meta_title',
        'todelete',
      );

      const textResult = await this.pageObjects.pagesPage.getTextColumnFromTableCmsPage(
        1,
        'meta_title',
      );

      await expect(textResult).to.contains('todelete');
    });

    it('should delete pages with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await this.pageObjects.pagesPage.deleteWithBulkActions('cms_page');
      await expect(deleteTextResult).to.be.equal(this.pageObjects.pagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.resetAndGetNumberOfLines('cms_page');
      await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages);
    });
  });
});
