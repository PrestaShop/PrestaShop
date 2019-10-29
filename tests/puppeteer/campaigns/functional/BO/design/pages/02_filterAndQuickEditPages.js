require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {Pages} = require('@data/demo/CMSpage');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const PagesPage = require('@pages/BO/design/pages/pages');

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
  // Login into BO and go to Pages page
  loginCommon.loginBO();

  // Go to Design>Pages page
  it('should go to "Design>Pages" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.designParentLink,
      this.pageObjects.boBasePage.pagesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
  });


  // 1 : Filter Pages with all inputs and selects in grid table
  describe('Filter Pages', async () => {
    it('should reset all filters and get number of pages in BO', async function () {
      numberOfPages = await this.pageObjects.pagesPage.resetFilter('cms_page');
      await expect(numberOfPages).to.be.above(0);
    });

    it('should filter by Id \'1\'', async function () {
      await this.pageObjects.pagesPage.filterTable(
        'cms_page',
        'input',
        'id_cms',
        Pages.delivery.id);
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page'),
      );
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      const textColumn = await this.pageObjects.pagesPage.getTextContent(
        this.pageObjects.pagesPage.listTableColumn
          .replace('%TABLE', 'cms_page')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'id_cms'),
      );
      await expect(textColumn).to.contains(Pages.delivery.id);
    });

    it('should reset all filters', async function () {
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.resetFilter('cms_page');
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });

    it('should filter by URL \'about-us\'', async function () {
      await this.pageObjects.pagesPage.filterTable(
        'cms_page',
        'input',
        'link_rewrite',
        Pages.aboutUs.url,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page'),
      );
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      const textColumn = await this.pageObjects.pagesPage.getTextContent(
        this.pageObjects.pagesPage.listTableColumn
          .replace('%TABLE', 'cms_page')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'link_rewrite'),
      );
      await expect(textColumn).to.contains(Pages.aboutUs.url);
    });

    it('should reset all filters', async function () {
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.resetFilter('cms_page');
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });

    it('should filter by Title \'Terms and conditions of use\'', async function () {
      await this.pageObjects.pagesPage.filterTable(
        'cms_page',
        'input',
        'meta_title',
        Pages.termsAndCondition.title,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page'),
      );
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      const textColumn = await this.pageObjects.pagesPage.getTextContent(
        this.pageObjects.pagesPage.listTableColumn
          .replace('%TABLE', 'cms_page')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'meta_title'),
      );
      await expect(textColumn).to.contains(Pages.termsAndCondition.title);
    });

    it('should reset all filters', async function () {
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.resetFilter('cms_page');
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });

    it('should filter by Position \'5\'', async function () {
      await this.pageObjects.pagesPage.filterTable(
        'cms_page',
        'input',
        'position',
        Pages.securePayment.position,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page'),
      );
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      const textColumn = await this.pageObjects.pagesPage.getTextContent(
        this.pageObjects.pagesPage.listTableColumn
          .replace('%TABLE', 'cms_page')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'position'),
      );
      await expect(textColumn).to.contains(Pages.securePayment.position);
    });

    it('should reset all filters', async function () {
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.resetFilter('cms_page');
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });

    it('should filter by Displayed \'Yes\'', async function () {
      await this.pageObjects.pagesPage.filterTable(
        'cms_page',
        'select',
        'active',
        Pages.securePayment.displayed,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page'),
      );
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.listTableColumn
            .replace('%TABLE', 'cms_page')
            .replace('%ROW', i)
            .replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset all filters', async function () {
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.resetFilter('cms_page');
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
  });
  // 2 : Editing Pages from grid table
  describe('Quick Edit Pages', async () => {
    it('should filter by Title \'Terms and conditions of use\'', async function () {
      await this.pageObjects.pagesPage.filterTable(
        'cms_page',
        'input',
        'meta_title',
        Pages.termsAndCondition.title,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page'),
      );
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      const textColumn = await this.pageObjects.pagesPage.getTextContent(
        this.pageObjects.pagesPage.listTableColumn
          .replace('%TABLE', 'cms_page')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'meta_title'),
      );
      await expect(textColumn).to.contains(Pages.termsAndCondition.title);
    });

    it('should disable the Page', async function () {
      const isActionPerformed = await this.pageObjects.pagesPage.updateToggleColumnValue(
        'cms_page',
        '1',
        false,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.pagesPage.elementVisible(
        this.pageObjects.pagesPage.columnNotValidIcon
          .replace('%TABLE', 'cms_page')
          .replace('%ROW', 1),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });

    it('should enable the Page', async function () {
      const isActionPerformed = await this.pageObjects.pagesPage.updateToggleColumnValue(
        'cms_page',
        '1',
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.pagesPage.elementVisible(
        this.pageObjects.pagesPage.columnValidIcon
          .replace('%TABLE', 'cms_page')
          .replace('%ROW', 1),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });

    it('should reset all filters', async function () {
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.resetFilter('cms_page');
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
  });
});
