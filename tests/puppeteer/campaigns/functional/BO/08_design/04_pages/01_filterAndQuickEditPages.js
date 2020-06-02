require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const {Pages} = require('@data/demo/CMSpage');

// Import pages
const LoginPage = require('@pages/BO/login/index');
const DashboardPage = require('@pages/BO/dashboard/index');
const PagesPage = require('@pages/BO/design/pages/index');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_pages_filterAndQuickEditPages';

let browser;
let page;
let numberOfPages = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    pagesPage: new PagesPage(page),
  };
};

// Filter And Quick Edit Pages
describe('Filter And Quick Edit Pages', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
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

  it('should reset all filters and get number of pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

    numberOfPages = await this.pageObjects.pagesPage.resetAndGetNumberOfLines('cms_page');
    await expect(numberOfPages).to.be.above(0);
  });

  // 1 : Filter Pages with all inputs and selects in grid table
  describe('Filter Pages', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_cms',
            filterValue: Pages.delivery.id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByLink',
            filterType: 'input',
            filterBy: 'link_rewrite',
            filterValue: Pages.aboutUs.url,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByMetaTitle',
            filterType: 'input',
            filterBy: 'meta_title',
            filterValue: Pages.termsAndCondition.title,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByPosition',
            filterType: 'input',
            filterBy: 'position',
            filterValue: Pages.securePayment.position,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: Pages.securePayment.displayed,
          },
        expected: 'check',
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await this.pageObjects.pagesPage.filterTable(
          'cms_page',
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberOfElementInGrid('cms_page');
        await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);

        for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
          const textColumn = await this.pageObjects.pagesPage.getTextColumnFromTableCmsPage(i, test.args.filterBy);

          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `reset_${test.args.testIdentifier}`, baseContext);

        const numberOfPagesAfterReset = await this.pageObjects.pagesPage.resetAndGetNumberOfLines('cms_page');
        await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
      });
    });
  });

  // 2 : Editing Pages from grid table
  describe('Quick Edit Pages', async () => {
    it('should filter by Title \'Terms and conditions of use\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickEditFilter', baseContext);

      await this.pageObjects.pagesPage.filterTable(
        'cms_page',
        'input',
        'meta_title',
        Pages.termsAndCondition.title,
      );

      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberOfElementInGrid('cms_page');

      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else {
        await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      }

      const textColumn = await this.pageObjects.pagesPage.getTextColumnFromTableCmsPage(1, 'meta_title');
      await expect(textColumn).to.contains(Pages.termsAndCondition.title);
    });

    const statuses = [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ];

    statuses.forEach((pageStatus) => {
      it(`should ${pageStatus.args.status} the page`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${pageStatus.args.status}Page`, baseContext);

        const isActionPerformed = await this.pageObjects.pagesPage.updateToggleColumnValue(
          'cms_page',
          1,
          pageStatus.args.enable,
        );

        if (isActionPerformed) {
          const resultMessage = await this.pageObjects.pagesPage.getTextContent(
            this.pageObjects.pagesPage.alertSuccessBlockParagraph,
          );

          await expect(resultMessage).to.contains(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
        }

        const currentStatus = await this.pageObjects.pagesPage.getToggleColumnValue('cms_page', 1);
        await expect(currentStatus).to.be.equal(pageStatus.args.enable);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickEditReset', baseContext);

      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.resetAndGetNumberOfLines('cms_page');
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
  });
});
