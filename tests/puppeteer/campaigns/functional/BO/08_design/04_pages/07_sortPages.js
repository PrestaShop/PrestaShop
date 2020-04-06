require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const PagesPage = require('@pages/BO/design/pages/index');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_pages_helperCard';

let browser;
let page;
let numberOfPages = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    pagesPage: new PagesPage(page),
  };
};
/*
Sort pages by :
id, url, title, position
 */
describe('Sort pages', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to Pages page
  loginCommon.loginBO();

  it('should go to \'Design > Pages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCmsPagesPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.designParentLink,
      this.pageObjects.boBasePage.pagesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
  });

  it('should reset filter and get number of pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);
    numberOfPages = await this.pageObjects.pagesPage.resetAndGetNumberOfLines('cms_page');
    await expect(numberOfPages).to.be.above(0);
  });

  const tests = [
    {
      args:
        {
          testIdentifier: 'sortByPositionDesc', sortBy: 'position', sortDirection: 'desc', isFloat: true,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_cms', sortDirection: 'asc', isFloat: true,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_cms', sortDirection: 'desc', isFloat: true,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByUrlAsc', sortBy: 'link_rewrite', sortDirection: 'asc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByUrlDesc', sortBy: 'link_rewrite', sortDirection: 'desc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByTitleAsc', sortBy: 'meta_title', sortDirection: 'asc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByTitleDesc', sortBy: 'meta_title', sortDirection: 'desc', isFloat: false,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByPositionAsc', sortBy: 'position', sortDirection: 'asc', isFloat: true,
        },
    },
  ];

  tests.forEach((test) => {
    it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);
      let nonSortedTable = await this.pageObjects.pagesPage.getAllRowsColumnContentTableCmsPage(test.args.sortBy);
      await this.pageObjects.pagesPage.sortTableCmsPage(test.args.sortBy, test.args.sortDirection);
      let sortedTable = await this.pageObjects.pagesPage.getAllRowsColumnContentTableCmsPage(test.args.sortBy);
      if (test.args.isFloat) {
        nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
        sortedTable = await sortedTable.map(text => parseFloat(text));
      }
      const expectedResult = await this.pageObjects.pagesPage.sortArray(nonSortedTable, test.args.isFloat);
      if (test.args.sortDirection === 'asc') {
        await expect(sortedTable).to.deep.equal(expectedResult);
      } else {
        await expect(sortedTable).to.deep.equal(expectedResult.reverse());
      }
    });
  });
});
